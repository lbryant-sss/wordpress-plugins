<?php
/**
 * Plugin Name: ووکامرس فارسی
 * Plugin URI: https://woosupport.ir
 * Description: بسته فارسی ساز ووکامرس پارسی به راحتی سیستم فروشگاه ساز ووکامرس را فارسی می کند. با فعال سازی افزونه ، بسیاری از قابلیت های مخصوص ایران به افزونه افزوده می شوند. پشتیبانی در <a href="http://woosupport.ir" target="_blank">ووکامرس پارسی</a>.
 * Version: 9.3.0
 * Author: ووکامرس فارسی
 * Author URI: https://woosupport.ir
 * WC requires at least: 7.0.0
 * WC tested up to: 9.7.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'PW_VERSION' ) ) {
	define( 'PW_VERSION', '9.3.0' );
}

if ( ! defined( 'PW_DIR' ) ) {
	define( 'PW_DIR', __DIR__ );
}

if ( ! defined( 'PW_FILE' ) ) {
	define( 'PW_FILE', __FILE__ );
}

if ( ! defined( 'PW_URL' ) ) {
	define( 'PW_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * @return Persian_Woocommerce_Core
 */
function PW() {
	return Persian_Woocommerce_Core::instance();
}


add_action( 'woocommerce_loaded', function () {

	require_once( 'vendor/autoload.php' );
	require_once( 'include/class-core.php' );
	require_once( 'include/class-widget.php' );
	require_once( 'include/class-translate.php' );
	require_once( 'include/class-tools.php' );
	require_once( 'include/class-address.php' );
	require_once( 'include/class-currencies.php' );
	require_once( 'include/class-notice.php' );

} );

require_once( 'include/class-install.php' );

register_activation_hook( PW_FILE, function () {
	file_put_contents( PW_DIR . '/.activated', '' );
} );

add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );
