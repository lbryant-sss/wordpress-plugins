<?php

trait Brizy_Editor_Asset_StaticFileTrait
{

    public static function get_asset_content($asset_source)
    {
        $http = new WP_Http();
        $wp_response = null;
        if (is_string($asset_source)) {
            $wp_response = $http->request($asset_source, array('timeout' => 30));
        } else {
            foreach ($asset_source as $url) {
                $wp_response = $http->request($url, array('timeout' => 30));

                if (is_wp_error($wp_response)) {
                    Brizy_Logger::instance()->error('Unable to get media content', array('exception' => $wp_response));
                    continue;
                }

                break;
            }
        }

        $code = wp_remote_retrieve_response_code($wp_response);

        if (is_wp_error($wp_response) || !($code >= 200 && $code < 300)) {
            Brizy_Logger::instance()->error('Unable to get media content', array('exception' => $wp_response));

            return false;
        }

        $content = wp_remote_retrieve_body($wp_response);

        return $content;
    }

    /**
     * @param $asset_source
     * @param $asset_path
     *
     * @return bool
     */
    protected function store_file($asset_source, $asset_path)
    {

        if (file_exists($asset_path)) {
            return true;
        }

        try {
            // check destination dir
            $dir_path = dirname($asset_path);
            $basename = basename( $asset_path );

            if (!file_exists($dir_path)) {
                if (!file_exists($dir_path) && !mkdir($dir_path, 0755, true) && !is_dir($dir_path)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir_path));
                }
            }

            $content = self::get_asset_content($asset_source);

			if( !$content ) {
				throw new Exception( "Unable to obtain asset content");
			}


	        $tempFile = Brizy_Editor_Asset_StaticFile::createSideLoadFile(
		        $basename,
		        $content
	        );

	        $filePath = Brizy_Editor_Asset_StaticFile::createSideLoadMedia( $tempFile, $asset_path );

	        if ( $filePath instanceof WP_Error ) {
		        throw new Exception( "Unable to store the thumbnail: ".$filePath->get_error_message() );
	        }

        } catch (Exception $e) {
            // clean up
            if ($asset_path) {
                @unlink($asset_path);
            }

            return false;
        }

        return true;
    }

	/**
	 * @deprcated "Do not use this method anymore"
	 *
	 * @param $content
	 * @param $asset_path
	 * @param $overwrite
	 *
	 * @return bool
	 */
    protected function store_content_in_file($content, $asset_path, $overwrite = false)
    {
        if (file_exists($asset_path) && !$overwrite) {
            return true;
        }

        try {
            // check destination dir
            $dir_path = dirname($asset_path);

            if (!file_exists($dir_path)) {
                if (!file_exists($dir_path) && !mkdir($dir_path, 0755, true) && !is_dir($dir_path)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir_path));
                }
            }

            if ($content !== false) {
                file_put_contents($asset_path, $content);
            } else {
                return false;
            }

        } catch (Exception $e) {
            // clean up
            if ($asset_path) {
                @unlink($asset_path);
            }

            return false;
        }

        return true;
    }

    protected function create_attachment(
        $madia_name,
        $absolute_asset_path,
        $relative_asset_path,
        $post_id = null,
        $uid = null
    ) {
        return self::createMediaAttachment($absolute_asset_path, $relative_asset_path, $post_id, $uid);
    }

    public static function createSideLoadMediaAttachment(
        $tempFileArray,
        $assetPath,
        $postId = null,
        $uid = null
    ) {
        $id = media_handle_sideload($tempFileArray, $postId, "Attached image file");

        if (!$id || is_wp_error($id)) {
            Brizy_Logger::instance()->critical('Unable to handle image sideload', [$id]);
            throw new Exception('Unable to handle image side load');
        }

        $filePath = get_attached_file($id, true);

        if (wp_mkdir_p(dirname($assetPath)) === false) {
            Brizy_Logger::instance()->critical('Unable to create folder', [$assetPath]);
            throw new Exception('Unable to create folder for block images');
        }

        @copy($filePath, $assetPath);

        unlink($filePath);

        update_attached_file($id, $assetPath);

        update_post_meta($id, 'brizy_attachment_uid', $uid ? $uid : md5($id.time()));
        if (!function_exists('wp_generate_attachment_metadata')) {
            include_once ABSPATH."/wp-admin/includes/image.php";
        }
        $attach_data = wp_generate_attachment_metadata($id, $assetPath);
        wp_update_attachment_metadata($id, $attach_data);
    }

	public static function createSideLoadMedia( $tempFileArray, $assetPath, $postId = null, $uid = null ) {

		$overrides = array( 'test_form' => false );
		$post      = get_post( $postId );
		$time = date( "Y/m" );
		if ( $post && substr( $post->post_date, 0, 4 ) > 0 ) {
			$time = $post->post_date;
		}
		if ( wp_mkdir_p( dirname( $assetPath ) ) === false ) {
			Brizy_Logger::instance()->critical( 'Unable to create folder', [ $assetPath ] );
			throw new Exception( 'Unable to create folder for block images' );
		}
		$uploadData = wp_handle_sideload( $tempFileArray, $overrides, $time );
		if ( isset( $uploadData['error'] ) ) {
			return new WP_Error( 'upload_error', $uploadData['error'] );
		}

		if ( $uploadData['file'] == $assetPath ) {
			return $assetPath;
		}

		if ( ! @copy( $uploadData['file'], $assetPath ) ) {
			$error = error_get_last();

			return new WP_Error( 'upload_error', htmlentities( $error['message'] ) );
		}
		unlink( $uploadData['file'] );

		return $assetPath;
	}

    public static function createSideLoadFile($basename, $content)
    {
        $filePath = tempnam(sys_get_temp_dir(), $basename);
        $result = file_put_contents($filePath, $content);

        if ($result === false) {
            Brizy_Logger::instance()->error('Filed to write content', ['filePath' => $filePath]);
            throw new Exception(__('Failed to write content', 'brizy'));
        }

        return array(
            'name' => $basename,
            'type' => Brizy_Public_AssetProxy::get_mime($filePath),
            'tmp_name' => $filePath,
            'error' => 0,
            'size' => filesize($filePath),
        );
    }


    public static function createMediaAttachment(
        $absolute_asset_path,
        $relative_asset_path,
        $post_id = null,
        $uid = null
    ) {
        $filetype = wp_check_filetype($absolute_asset_path);

        if (!$filetype['type']) {
            return false;
        }

        $upload_path = wp_upload_dir();

        $attachment = array(
            'guid' => $upload_path['baseurl']."/".$relative_asset_path,
            'post_mime_type' => $filetype['type'],
            'post_title' => basename($absolute_asset_path),
            'post_content' => '',
            'post_status' => 'inherit',
        );

        $attachment_id = self::getAttachmentByGuid($upload_path['baseurl']."/".$relative_asset_path);
        if (!$attachment_id) {
            $attachment_id = wp_insert_attachment($attachment, $relative_asset_path, $post_id);
        }

        if (is_wp_error($attachment_id) || $attachment_id === 0 || $attachment_id === null) {
            return false;
        }

        update_post_meta($attachment_id, 'brizy_attachment_uid', $uid ? $uid : md5($attachment_id.time()));

        if (!function_exists('wp_generate_attachment_metadata')) {
            include_once ABSPATH."/wp-admin/includes/image.php";
        }

        $attach_data = wp_generate_attachment_metadata($attachment_id, $absolute_asset_path);
        wp_update_attachment_metadata($attachment_id, $attach_data);

        return $attachment_id;
    }

    /**
     * @param $filename
     * @param array $headers
     */
    public function send_file($filename, $headers = array())
    {
        if (file_exists($filename)) {

            $defaultHeaders = array(
                'Content-Type' => self::get_mime($filename, 1),
                'Cache-Control' => 'max-age=600',
            );

            $content = file_get_contents($filename);

            // send headers
            $headers = array_merge($defaultHeaders, $headers);

            foreach ($headers as $key => $val) {
                if (is_array($val)) {
                    $val = implode(', ', $val);
                }

                header("{$key}: {$val}");
            }
            // send file content
            echo $content;
            exit;
        } else {
            global $wp_query;
            $wp_query->set_404();

            return;
        }

    }

    /**
     * @param $filename
     * @param int $mode
     *
     * @return mixed|string
     */
    public static function get_mime($filename, $mode = 0)
    {

        // mode 0 = full check
        // mode 1 = extension check only

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            'webp' => 'image/webp',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'docx' => 'application/msword',
            'xlsx' => 'application/vnd.ms-excel',
            'pptx' => 'application/vnd.ms-powerpoint',


            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $array = explode('.', $filename);
        $str = end($array);
        $ext = strtolower($str);

        if (function_exists('mime_content_type') && $mode == 0) {
            $mimetype = mime_content_type($filename);

            return $mimetype;

        } elseif (function_exists('finfo_open') && $mode == 0) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);

            return $mimetype;
        } elseif (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        } else {
            return 'application/octet-stream';
        }
    }


    private static function getAttachmentByGuid($media_name)
    {
        global $wpdb;

        return $wpdb->get_var(
            $wpdb->prepare(
                "SELECT 
						p.ID
					FROM {$wpdb->posts} p
					WHERE p.guid = %s AND p.post_type = 'attachment'",
                $media_name
            )
        );
    }
}



