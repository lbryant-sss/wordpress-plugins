<?php

class Meow_MWAI_Services_Image {
  private $core;

  public function __construct( $core ) {
    $this->core = $core;
  }

  public function is_image( $mimeType ) {
    return strpos( $mimeType, 'image/' ) === 0;
  }

  public function get_image_resolution( $imageData ) {
    try {
      $tempFile = tmpfile();
      $tempFilePath = stream_get_meta_data( $tempFile )['uri'];
      fwrite( $tempFile, $imageData );
      $imageSize = getimagesize( $tempFilePath );
      fclose( $tempFile );
      if ( $imageSize !== false ) {
        return $imageSize[0] . 'x' . $imageSize[1];
      }
    }
    catch ( Exception $e ) {
      throw new Exception( 'Failed to get image resolution.' );
    }
    return null;
  }

  public function get_mime_type( $file, $fileData = null ) {
    $mimeType = null;

    // If we have file data, let's use it
    if ( !empty( $fileData ) ) {
      $f = finfo_open();
      $mimeType = finfo_buffer( $f, $fileData, FILEINFO_MIME_TYPE );
    }

    // Try to use mime_content_type for local files
    if ( !$mimeType ) {
      $isUrl = filter_var( $file, FILTER_VALIDATE_URL );
      if ( !$isUrl && function_exists( 'mime_content_type' ) && file_exists( $file ) ) {
        $mimeType = mime_content_type( $file );
      }
    }

    // Otherwise, let's check the file extension (which can actually also be an URL)
    if ( !$mimeType ) {
      $extension = pathinfo( $file, PATHINFO_EXTENSION );
      $extension = strtolower( $extension );
      $mimeTypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'bmp' => 'image/bmp',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'pdf' => 'application/pdf',
      ];
      $mimeType = isset( $mimeTypes[$extension] ) ? $mimeTypes[$extension] : null;
    }

    return $mimeType;
  }

  public function download_image( $url ) {
    $response = wp_safe_remote_get( $url, [ 'timeout' => 60 ] );
    if ( is_wp_error( $response ) ) {
      throw new Exception( $response->get_error_message() );
    }
    return wp_remote_retrieve_body( $response );
  }

  /**
  * Add an image from a URL to the Media Library.
  * @param string $url The URL of the image to be downloaded.
  * @param string $filename The filename of the image, if not set, it will be the basename of the URL.
  * @param string $title The title of the image.
  * @param string $description The description of the image.
  * @param string $caption The caption of the image.
  * @param string $alt The alt text of the image.
  * @return int The attachment ID of the image.
  */
  public function add_image_from_url( $url, $filename = null, $title = null, $description = null, $caption = null, $alt = null, $attachedPost = null ) {
    $path_parts = pathinfo( parse_url( $url, PHP_URL_PATH ) );
    $url_filename = $path_parts['basename'];
    $file_type = wp_check_filetype( $url_filename, null );
    $allowed_types = get_allowed_mime_types();
    if ( !$file_type || !in_array( $file_type['type'], $allowed_types ) ) {
      throw new Exception( 'Invalid file type from URL.' );
    }

    // Initial extension from URL file name
    $extension = $file_type['ext'];

    if ( !empty( $filename ) ) {
      $custom_file_type = wp_check_filetype( $filename, null );
      if ( !$custom_file_type || !in_array( $custom_file_type['type'], $allowed_types ) ) {
        throw new Exception( 'Invalid custom file type.' );
      }
      // Use the extension from the custom filename if valid
      $extension = $custom_file_type['ext'];
    }

    $image_data = $this->download_image( $url );
    if ( !$image_data ) {
      throw new Exception( 'Could not download the image.' );
    }
    $upload_dir = wp_upload_dir();

    // Filename handling including 'generated_' prefix scenario
    if ( empty( $filename ) ) {
      $filename = sanitize_file_name( $url_filename );
      if ( empty( $extension ) ) { // This condition might now be redundant
        $extension = $file_type['ext'];
      }
      // Filename length check and prepend if conditions met
      if ( strlen( $filename ) > 32 || strlen( $filename ) < 4 || strpos( $filename, 'generated_' ) === 0 ) {
        $filename = uniqid( 'ai_', true ) . '.' . $extension;
      }
      if ( strpos( $filename, '.' ) === false ) {
        $filename .= '.' . $extension;
      }
    }

    // Directory and file path handling
    if ( wp_mkdir_p( $upload_dir['path'] ) ) {
      $file = $upload_dir['path'] . '/' . $filename;
    }
    else {
      $file = $upload_dir['basedir'] . '/' . $filename;
    }

    // Ensure file name uniqueness in the directory
    $i = 1;
    $parts = pathinfo( $file );
    while ( file_exists( $file ) ) {
      $file = $parts['dirname'] . '/' . $parts['filename'] . '-' . $i . '.' . $parts['extension'];
      $i++;
    }

    // Write file to filesystem
    file_put_contents( $file, $image_data );

    // Prepare and insert attachment
    $wp_filetype = wp_check_filetype( basename( $file ), null );
    $attachment = [
      'post_mime_type' => $wp_filetype['type'],
      'post_title' => !is_null( $title ) ? $title : preg_replace( '/\.[^.]+$/', '', basename( $file ) ),
      'post_content' => !is_null( $description ) ? $description : '',
      'post_status' => 'inherit',
      'post_excerpt' => !is_null( $caption ) ? $caption : '',
    ];

    $attach_id = wp_insert_attachment( $attachment, $file, $attachedPost );
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    wp_update_attachment_metadata( $attach_id, $attach_data );
    if ( !is_null( $alt ) ) {
      update_post_meta( $attach_id, '_wp_attachment_image_alt', $alt );
    }
    return $attach_id;
  }
}
