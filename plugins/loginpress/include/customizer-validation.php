<?php
/**
 * Customizer: Sanitization Callbacks
 *
 * This file demonstrates how to define sanitization callback functions for various data types.
 *
 * @since 1.1.16
 *
 * @version 1.6.1
 */

/**
 * Checkbox sanitization callback example.
 *
 * Sanitization callback for 'checkbox' type controls. This callback sanitizes `$checked`
 * as a boolean value, either TRUE or FALSE.
 *
 * @param bool $checked Whether the checkbox is checked.
 * @return bool Whether the checkbox is checked.
 */
function loginpress_sanitize_checkbox( $checked ) {

	// Boolean check.
	return ( ( isset( $checked ) && true == $checked ) ? true : false );
}

/**
 * Select sanitization callback example.
 *
 * - Sanitization: select
 * - Control: select, radio
 *
 * Sanitization callback for 'select' and 'radio' type controls. This callback sanitizes `$input`
 * as a slug, and then validates `$input` against the choices defined for the control.
 *
 * @see sanitize_key()               https://developer.wordpress.org/reference/functions/sanitize_key/
 * @see $wp_customize->get_control() https://developer.wordpress.org/reference/classes/wp_customize_manager/get_control/
 *
 * @param string               $input   Slug to sanitize.
 * @param WP_Customize_Setting $setting Setting instance.
 * @return string Sanitized slug if it is a valid choice; otherwise, the setting default.
 */
function loginpress_sanitize_select( $input, $setting ) {

	// Ensure input is a slug.
	$input = sanitize_key( $input );

	// Get list of choices from the control associated with the setting.
	$choices = $setting->manager->get_control( $setting->id )->choices;

	// If the input is a valid key, return it; otherwise, return the default.
	return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}

/**
 * Image sanitization callback example.
 *
 * Checks the image's file extension and mime type against a whitelist. If they're allowed,
 * send back the filename, otherwise, return the setting default.
 *
 * - Sanitization: image file extension
 * - Control: text, WP_Customize_Image_Control
 *
 * @see wp_check_filetype() https://developer.wordpress.org/reference/functions/wp_check_filetype/
 *
 * @param string               $image   Image filename.
 * @param WP_Customize_Setting $setting Setting instance.
 * @return string The image filename if the extension is allowed; otherwise, the setting default.
 *
 * @since 1.1.17
 *
 * @version 3.0.0
 */
function loginpress_sanitize_image( $image, $setting ) {

	/**
	 * Array of valid image file types.
	 *
	 * The array includes image mime types that are included in wp_get_mime_types()
	 */
	$mimes = array(
		'jpg|jpeg|jpe' => 'image/jpeg',
		'gif'          => 'image/gif',
		'png'          => 'image/png',
		'bmp'          => 'image/bmp',
		'tif|tiff'     => 'image/tiff',
		'ico'          => 'image/x-icon',
	);

	// Allowed svg mime type in version 1.2.2
	$allowed_mime = get_allowed_mime_types();

	/**
	 * Filter the list of mime types that are allowed for uploads.
	 *
	 * @since 1.6.1
	 */
	$extra_mimes = array(
		'svg'  => 'image/svg+xml', // Allowed svg mime type in version 1.2.2
		'webp' => 'image/webp',   // Allowed webp mime type in version 1.6.1
	);

	foreach ( $extra_mimes as $key => $value ) {
		$mime_check = isset( $allowed_mime[ $key ] ) ? true : false;
		if ( $mime_check ) {
			$allow_mime = array( $key => $value );
			$mimes      = array_merge( $mimes, $allow_mime );
		}
	}

	$file_type = false;

	/**
	 * Return an array with file extension and mime_type.
	 *
	 * @since 3.0.0
	 * @version 3.0.3
	 */
	if ( ! empty( $image ) ) {

		// Return an array with file extension and mime_type.
		$file = wp_check_filetype( $image, $mimes );

		// If $image has a valid mime_type, return it; otherwise, return the default.
		return ( $file['ext'] ? $image : loginpress_image_content_type( $image, $mimes, $setting ) );
	}

	return $setting->default;
}

/**
 * If CDN is being used get sanitization option.
 *
 * @param string $image The image URL.
 * @param array  $mimes The mime type allowed.
 * @param object $setting The settings object.
 *
 * @version 3.0.2
 * @return mixed The images based on content type.
 */
function loginpress_image_content_type( $image, $mimes, $setting ) {
	$headers           = get_headers( $image, 1 );
	$content_type      = false;
	$file_type         = false;
	$content_types_can = array( 'Content-Type', 'content-type' );
	foreach ( $content_types_can as $type ) {
		if ( isset( $headers[ $type ] ) && ! empty( $headers[ $type ] ) ) {
			$content_type = $headers[ $type ];
			break;
		}
	}

	if ( $content_type ) {

		$file_type = $content_type ? in_array( $content_type, $mimes ) : false;

		if ( is_array( $content_type ) ) {
			foreach ( $content_type as $type ) {
				$file_type = $type ? in_array( $type, $mimes ) : false;
				if ( $file_type ) {
					break;
				}
			}
		}
	}

	// If $image has a valid mime_type, return it; otherwise, return the default.
	return ( $file_type ? $image : $setting->default );
}
