<?php
/**
 * Head & Footer Code plugin for WordPress
 *
 * @link        https://urosevic.net/
 * @since       1.0.0
 * @package     Head_Footer_Code
 *
 * Plugin Name: Head & Footer Code
 * Plugin URI:  https://urosevic.net/wordpress/plugins/head-footer-code/
 * Description: Easy add site-wide, category or article specific custom code before the closing <strong>&lt;/head&gt;</strong> and <strong>&lt;/body&gt;</strong> or after opening <strong>&lt;body&gt;</strong> HTML tag.
 * Version:     1.3.7
 * Author:      Aleksandar Urošević
 * Author URI:  https://urosevic.net/
 * License:     GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: head-footer-code
 * Domain Path: /languages
 * Requires at least: 4.9
 * Tested up to: 6.7
 * Requires PHP: 5.5
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'HFC_PHP_VER', '5.5' ); // Minimum version of PHP required for this plugin.
define( 'HFC_WP_VER', '4.9' ); // Minimum version of WordPress required for this plugin.
define( 'HFC_VER_DB', '8' );
define( 'HFC_VER', '1.3.7' );
define( 'HFC_PLUGIN_FILE', plugin_basename( __FILE__ ) );
define( 'HFC_PLUGIN_NAME', 'Head & Footer Code' );
define( 'HFC_PLUGIN_SLUG', 'head-footer-code' );
define( 'HFC_FILE', __FILE__ );
define( 'HFC_DIR', __DIR__ );
define( 'HFC_DIR_INC', HFC_DIR . '/inc/' );
define( 'HFC_URL', plugin_dir_url( __FILE__ ) );

// Load files.
require_once 'inc/helpers.php';
