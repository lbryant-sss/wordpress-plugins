<?php
/**
 * Plugin Name: YITH WooCommerce Quick View
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-quick-view
 * Description: The <code><strong>YITH WooCommerce Quick View</strong></code> plugin allows your customers to have a quick look about products. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 2.6.0
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-quick-view
 * Domain Path: /languages/
 * Requires Plugins: woocommerce
 * WC requires at least: 9.8
 * WC tested up to: 10.0
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Quick View
 * @version 2.6.0
 */

/**  Copyright 2015-2025 Your Inspiration Solutions (email : plugins@yithemes.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/**
 * Message if WooCommerce is not installed.
 *
 * @since 1.0.0
 * @return void
 */
function yith_wcqv_install_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p><?php esc_html_e( 'YITH WooCommerce Quick View is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-quick-view' ); ?></p>
	</div>
	<?php
}

/**
 * Message if Premium plugin is installed.
 *
 * @since 1.0.0
 * @return void
 */
function yith_wcqv_install_free_admin_notice() {
	?>
	<div class="error">
		<p><?php esc_html_e( 'You can\'t activate the free version of YITH WooCommerce Quick View while you are using the premium one.', 'yith-woocommerce-quick-view' ); ?></p>
	</div>
	<?php
}

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


if ( ! defined( 'YITH_WCQV_VERSION' ) ) {
	define( 'YITH_WCQV_VERSION', '2.6.0' );
}

if ( ! defined( 'YITH_WCQV_FREE_INIT' ) ) {
	define( 'YITH_WCQV_FREE_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WCQV_INIT' ) ) {
	define( 'YITH_WCQV_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WCQV' ) ) {
	define( 'YITH_WCQV', true );
}

if ( ! defined( 'YITH_WCQV_FILE' ) ) {
	define( 'YITH_WCQV_FILE', __FILE__ );
}

if ( ! defined( 'YITH_WCQV_URL' ) ) {
	define( 'YITH_WCQV_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'YITH_WCQV_DIR' ) ) {
	define( 'YITH_WCQV_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_WCQV_TEMPLATE_PATH' ) ) {
	define( 'YITH_WCQV_TEMPLATE_PATH', YITH_WCQV_DIR . 'templates' );
}

if ( ! defined( 'YITH_WCQV_ASSETS_URL' ) ) {
	define( 'YITH_WCQV_ASSETS_URL', YITH_WCQV_URL . 'assets' );
}

if ( ! defined( 'YITH_WCQV_SLUG' ) ) {
	define( 'YITH_WCQV_SLUG', 'yith-woocommerce-quick-view' );
}

// Plugin Framework Loader.
if ( file_exists( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php';
}

/**
 * Init.
 *
 * @since 1.0.0
 * @return void
 */
function yith_wcqv_init() {

	yith_plugin_fw_load_plugin_textdomain( 'yith-woocommerce-quick-view', dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	// Load required classes and functions.
	require_once 'includes/functions.yith-wcqv.php';
	require_once 'includes/class.yith-wcqv.php';
	// Let's start the game!
	YITH_WCQV();
}

add_action( 'yith_wcqv_init', 'yith_wcqv_init' );

/**
 * Install.
 *
 * @since 1.0.0
 * @return void
 */
function yith_wcqv_install() {

	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_wcqv_install_woocommerce_admin_notice' );
	} elseif ( defined( 'YITH_WCQV_PREMIUM' ) ) {
		add_action( 'admin_notices', 'yith_wcqv_install_free_admin_notice' );
		deactivate_plugins( plugin_basename( __FILE__ ) );
	} else {
		do_action( 'yith_wcqv_init' );
	}
}

add_action( 'plugins_loaded', 'yith_wcqv_install', 11 );

add_action( 'before_woocommerce_init', 'yith_wcqv_declare_hpos_compatibility' );

/**
 * Declare HPOS compatibility
 *
 * @return void
 * @since  1.23.0
 */

if( ! function_exists( 'yith_wcqv_declare_hpos_compatibility' ) ){
	function yith_wcqv_declare_hpos_compatibility() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
}
