<?php
/**
 * Plugin Name:       Simple Local Avatars
 * Plugin URI:        https://10up.com/plugins/simple-local-avatars-wordpress/
 * Description:       Adds an avatar upload field to user profiles. Generates requested sizes on demand, just like Gravatar! Simple and lightweight.
 * Version:           2.8.4
 * Requires at least: 6.6
 * Requires PHP:      7.4
 * Author:            10up
 * Author URI:        https://10up.com
 * License:           GPL-2.0-or-later
 * License URI:       https://spdx.org/licenses/GPL-2.0-or-later.html
 * Text Domain:       simple-local-avatars
 *
 * @package           SimpleLocalAvatars
 */

if ( ! is_readable( __DIR__ . '/10up-lib/wp-compat-validation-tool/src/Validator.php' ) ) {
	return;
}

require_once '10up-lib/wp-compat-validation-tool/src/Validator.php';

$compat_checker = new \SimpleLocalAvatarsValidator\Validator();
$compat_checker
	->set_plugin_name( 'Simple Local Avatars' )
	->set_php_min_required_version( '7.4' );

if ( ! $compat_checker->is_plugin_compatible() ) {
	return;
}

define( 'SLA_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once dirname( __FILE__ ) . '/includes/class-simple-local-avatars.php';

// Global constants.
define( 'SLA_VERSION', '2.8.4' );
define( 'SLA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'SLA_IS_NETWORK' ) ) {
	define( 'SLA_IS_NETWORK', Simple_Local_Avatars::is_network( plugin_basename( __FILE__ ) ) );
}

/**
 * Init the plugin.
 */
global $simple_local_avatars;
$simple_local_avatars = new Simple_Local_Avatars();

/**
 * More efficient to call simple local avatar directly in theme and avoid
 * gravatar setup.
 *
 * Since 2.2, This function is only a proxy for get_avatar due to internal changes.
 *
 * @param int|string|object $id_or_email A user ID,  email address, or comment object
 * @param int               $size        Size of the avatar image
 * @param string            $default     URL to a default image to use if no avatar is available
 * @param string            $alt         Alternate text to use in image tag. Defaults to blank
 * @param array             $args        Optional. Extra arguments to retrieve the avatar.
 *
 * @return string <img> tag for the user's avatar
 */
function get_simple_local_avatar( $id_or_email, $size = 96, $default = '', $alt = '', $args = array() ) {
	return apply_filters( 'simple_local_avatar', get_avatar( $id_or_email, $size, $default, $alt, $args ) );
}

register_uninstall_hook( __FILE__, 'simple_local_avatars_uninstall' );
/**
 * On uninstallation, remove the custom field from the users and delete the local avatars
 */
function simple_local_avatars_uninstall() {
	$simple_local_avatars = new Simple_Local_Avatars();
	$users                = get_users(
		array(
			'meta_key' => 'simple_local_avatar', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'fields'   => 'ids',
		)
	);

	foreach ( $users as $user_id ) :
		$simple_local_avatars->avatar_delete( $user_id );
	endforeach;

	delete_option( 'simple_local_avatars' );
	delete_option( 'simple_local_avatars_migrations' );
}
