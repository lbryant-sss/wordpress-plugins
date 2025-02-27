<?php
/**
 * Global variables used in plugin.
 *
 * @package    One User Avatar
 * @author     Bangbay Siboliban
 * @author     Flippercode
 * @author     ProfilePress
 * @author     One Designs
 * @copyright  2013-2014 Bangbay Siboliban
 * @copyright  2014-2020 Flippercode
 * @copyright  2020-2021 ProfilePress
 * @copyright  2021 One Designs
 * @version    2.5.0
 */

/**
 * @since 1.8
 * @uses get_intermediate_image_sizes()
 * @uses get_option()
 * @uses wp_max_upload_size()
 */

// Define global variables
global  $avatar_default,
		$show_avatars,
		$wpua_allow_upload,
		$wpua_force_file_uploader,
		$wpua_disable_um_avatars,
		$wpua_avatar_default,
		$wpua_disable_gravatar,
		$wpua_edit_avatar,
		$wpua_resize_crop,
		$wpua_resize_h,
		$wpua_resize_upload,
		$wpua_resize_w,
		$wpua_tinymce,
		$mustache_original,
		$mustache_medium,
		$mustache_thumbnail,
		$mustache_avatar,
		$mustache_avatar_2x,
		$mustache_admin,
		$mustache_admin_2x,
		$wpua_default_avatar_updated,
		$wpua_users_updated,
		$wpua_media_updated,
		$upload_size_limit,
		$upload_size_limit_with_units,
		$wpua_user_upload_size_limit,
		$wpua_upload_size_limit,
		$wpua_upload_size_limit_with_units,
		$all_sizes,
		$wpua_hash_gravatar;

// Store if hash has gravatar
$wpua_hash_gravatar = get_option( 'wpua_hash_gravatar' );

if ( false != $wpua_hash_gravatar ) {
	$wpua_hash_gravatar = maybe_unserialize( $wpua_hash_gravatar );
}

// Default avatar name
$avatar_default = get_option( 'avatar_default' );

// Attachment ID of default avatar
$wpua_avatar_default = get_option( 'avatar_default_wp_user_avatar' );

// Booleans
$show_avatars             = get_option( 'show_avatars' );
$wpua_allow_upload        = get_option( 'wp_user_avatar_allow_upload' );
$wpua_disable_um_avatars  = get_option( 'wp_user_avatar_disable_um_avatars' );
$wpua_force_file_uploader = get_option( 'wp_user_avatar_force_file_uploader' );
$wpua_disable_gravatar    = get_option( 'wp_user_avatar_disable_gravatar' );
$wpua_edit_avatar         = get_option( 'wp_user_avatar_edit_avatar' );
$wpua_resize_crop         = get_option( 'wp_user_avatar_resize_crop' );
$wpua_resize_upload       = get_option( 'wp_user_avatar_resize_upload' );
$wpua_tinymce             = get_option( 'wp_user_avatar_tinymce' );

// Resize dimensions
$wpua_resize_h = get_option( 'wp_user_avatar_resize_h' );
$wpua_resize_w = get_option( 'wp_user_avatar_resize_w' );

// Default avatar 512x512
$mustache_original  = WPUA_IMG_URL . 'wpua.png';

// Default avatar 300x300
$mustache_medium    = WPUA_IMG_URL . 'wpua-300x300.png';

// Default avatar 150x150
$mustache_thumbnail = WPUA_IMG_URL . 'wpua-150x150.png';

// Default avatar 96x96
$mustache_avatar    = WPUA_IMG_URL . 'wpua-96x96.png';

// Default avatar 192x192
$mustache_avatar_2x = WPUA_IMG_URL . 'wpua-192x192.png';

// Default avatar 64x64
$mustache_admin     = WPUA_IMG_URL . 'wpua-32x32.png';
$mustache_admin_2x  = WPUA_IMG_URL . 'wpua-64x64.png';

// Check for updates
$wpua_default_avatar_updated = get_option( 'wp_user_avatar_default_avatar_updated' );
$wpua_users_updated          = get_option( 'wp_user_avatar_users_updated' );
$wpua_media_updated          = get_option( 'wp_user_avatar_media_updated' );

// Server upload size limit
$upload_size_limit = wp_max_upload_size();

// Convert to KB
if ( 1024 < $upload_size_limit ) {
	$upload_size_limit /= 1024;
}

$upload_size_limit_with_units = (int) $upload_size_limit . 'KB';

// User upload size limit
$wpua_user_upload_size_limit = get_option( 'wp_user_avatar_upload_size_limit' );

if( 0 == $wpua_user_upload_size_limit || $wpua_user_upload_size_limit > wp_max_upload_size() ) {
	$wpua_user_upload_size_limit = wp_max_upload_size();
}

// Value in bytes
$wpua_upload_size_limit = $wpua_user_upload_size_limit;

// Convert to KB
if ( 1024 < $wpua_user_upload_size_limit ) {
	$wpua_user_upload_size_limit /= 1024;
}

$wpua_upload_size_limit_with_units = (int) $wpua_user_upload_size_limit . 'KB';

// Check for custom image sizes
$all_sizes = array_merge( get_intermediate_image_sizes(), array( 'original' ) );
