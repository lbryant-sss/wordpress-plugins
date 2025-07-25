<?php
/**
 * Copyright (c) Facebook, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * Plugin Name: Facebook for WooCommerce
 * Plugin URI: https://github.com/woocommerce/facebook-for-woocommerce/
 * Description: Grow your business on Facebook! Use this official plugin to help sell more of your products using Facebook. After completing the setup, you'll be ready to create ads that promote your products and you can also create a shop section on your Page where customers can browse your products on Facebook.
 * Author: Facebook
 * Author URI: https://www.facebook.com/
 * Version: 3.5.5
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * Text Domain: facebook-for-woocommerce
 * Requires Plugins: woocommerce
 * Tested up to: 6.8.1
 * WC requires at least: 6.4
 * WC tested up to: 9.9.4
 *
 * @package FacebookCommerce
 */

require_once __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Grow\Tools\CompatChecker\v0_0_1\Checker;
use Automattic\WooCommerce\Utilities\FeaturesUtil;

defined( 'ABSPATH' ) || exit;

// HPOS compatibility declaration.
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility( 'custom_order_tables', plugin_basename( __FILE__ ), true );
		}
	}
);

if ( is_admin() ) {
	add_action(
		'admin_init',
		function () {
			if ( ! class_exists( 'WC_Facebookcommerce_Admin_Banner' ) ) {
				require_once plugin_dir_path( __FILE__ ) .
				'facebook-commerce-admin-banner.php';
			}
			new WC_Facebookcommerce_Admin_Banner();
		}
	);
}

/**
 * The plugin loader class.
 *
 * @since 1.10.0
 */
class WC_Facebook_Loader {

	/**
	 * @var string the plugin version. This must be in the main plugin file to be automatically bumped by Woorelease.
	 */
	const PLUGIN_VERSION = '3.5.5'; // WRCS: DEFINED_VERSION.

	// Minimum PHP version required by this plugin.
	const MINIMUM_PHP_VERSION = '7.4.0';

	// Minimum WordPress version required by this plugin.
	const MINIMUM_WP_VERSION = '4.4';

	// Minimum WooCommerce version required by this plugin.
	const MINIMUM_WC_VERSION = '5.3';

	// SkyVerge plugin framework version used by this plugin.
	const FRAMEWORK_VERSION = '5.10.0';

	// The plugin name, for displaying notices.
	const PLUGIN_NAME = 'Facebook for WooCommerce';


	/**
	 * This class instance.
	 *
	 * @var \WC_Facebook_Loader single instance of this class.
	 */
	private static $instance;

	/**
	 * Admin notices to add.
	 *
	 * @var array Array of admin notices.
	 */
	private $notices = array();


	/**
	 * Constructs the class.
	 *
	 * @since 1.10.0
	 */
	protected function __construct() {

		register_activation_hook( __FILE__, array( $this, 'activation_check' ) );

		add_action( 'admin_init', array( $this, 'check_environment' ) );

		add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );

		// If the environment check fails, initialize the plugin.
		if ( $this->is_environment_compatible() ) {
			add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
		}
	}


	/**
	 * Cloning instances is forbidden due to singleton pattern.
	 *
	 * @since 1.10.0
	 */
	public function __clone() {

		wc_doing_it_wrong( __FUNCTION__, sprintf( 'You cannot clone instances of %s.', get_class( $this ) ), '1.10.0' );
	}


	/**
	 * Unserializing instances is forbidden due to singleton pattern.
	 *
	 * @since 1.10.0
	 */
	public function __wakeup() {

		wc_doing_it_wrong( __FUNCTION__, sprintf( 'You cannot unserialize instances of %s.', get_class( $this ) ), '1.10.0' );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @since 1.10.0
	 */
	public function init_plugin() {

		if ( ! Checker::instance()->is_compatible( __FILE__, self::PLUGIN_VERSION ) ) {
			return;
		}

		self::set_wc_facebook_svr_flags();

		require_once plugin_dir_path( __FILE__ ) . 'class-wc-facebookcommerce.php';

		// fire it up!
		if ( function_exists( 'facebook_for_woocommerce' ) ) {
			facebook_for_woocommerce();
		}
	}


	/**
	 * Gets the framework version in namespace form.
	 *
	 * @since 1.10.0
	 *
	 * @return string
	 */
	public function get_framework_version_namespace() {
		return 'v' . str_replace( '.', '_', $this->get_framework_version() );
	}


	/**
	 * Gets the framework version used by this plugin.
	 *
	 * @since 1.10.0
	 *
	 * @return string
	 */
	public function get_framework_version() {

		return self::FRAMEWORK_VERSION;
	}


	/**
	 * Checks the server environment and other factors and deactivates plugins as necessary.
	 *
	 * Based on http://wptavern.com/how-to-prevent-wordpress-plugins-from-activating-on-sites-with-incompatible-hosting-environments
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function activation_check() {

		if ( ! $this->is_environment_compatible() ) {

			$this->deactivate_plugin();

			wp_die( esc_html( self::PLUGIN_NAME . ' could not be activated. ' . $this->get_environment_message() ) );
		}
	}


	/**
	 * Checks the environment on loading WordPress, just in case the environment changes after activation.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function check_environment() {

		if ( ! $this->is_environment_compatible() && is_plugin_active( plugin_basename( __FILE__ ) ) ) {

			$this->deactivate_plugin();

			$this->add_admin_notice( 'bad_environment', 'error', self::PLUGIN_NAME . ' has been deactivated. ' . $this->get_environment_message() );
		}
	}


	/**
	 * Deactivates the plugin.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	protected function deactivate_plugin() {

		deactivate_plugins( plugin_basename( __FILE__ ) );

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}


	/**
	 * Adds an admin notice to be displayed.
	 *
	 * @since 1.10.0
	 *
	 * @param string $slug    The slug for the notice.
	 * @param string $class   The css class for the notice.
	 * @param string $message The notice message.
	 */
	private function add_admin_notice( $slug, $class, $message ) {

		$this->notices[ $slug ] = array(
			'class'   => $class,
			'message' => $message,
		);
	}


	/**
	 * Displays any admin notices added with \WC_Facebook_Loader::add_admin_notice()
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function admin_notices() {

		foreach ( (array) $this->notices as $notice_key => $notice ) {

			?>
			<div class="<?php echo esc_attr( $notice['class'] ); ?>">
				<p>
				<?php
				echo wp_kses(
					$notice['message'],
					array(
						'a'      => array(
							'href' => array(),
						),
						'strong' => array(),
					)
				);
				?>
				</p>
			</div>
			<?php
		}
	}


	/**
	 * Determines if the server environment is compatible with this plugin.
	 *
	 * Override this method to add checks for more than just the PHP version.
	 *
	 * @since 1.10.0
	 *
	 * @return bool
	 */
	private function is_environment_compatible() {
		return version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '>=' );
	}


	/**
	 * Gets the message for display when the environment is incompatible with this plugin.
	 *
	 * @since 1.10.0
	 *
	 * @return string
	 */
	private function get_environment_message() {

		return sprintf( 'The minimum PHP version required for this plugin is %1$s. You are running %2$s.', self::MINIMUM_PHP_VERSION, PHP_VERSION );
	}


	private static function is_wp_com() {
		$api_url       = 'https://public-api.wordpress.com/rest/v1.1/sites/' . wp_parse_url( get_site_url() )['host'];
		$response      = wp_remote_get( $api_url );
		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_wp_error( $response ) && isset( $response_body['ID'] ) ) {
			return true;
		}
		return false;
	}


	private static function is_site_connected_compat() {
		if ( ! is_callable( array( 'WC_Helper_Options', 'get' ) ) ) {
			return false;
		}

		$auth = WC_Helper_Options::get( 'auth' );

		// If `access_token` is empty, there's no active connection.
		return ! empty( $auth['access_token'] );
	}


	private static function is_woo_com() {
		$site_connected = false;
		if ( ! is_callable( array( 'WC_Helper', 'is_site_connected' ) ) ) {
			$site_connected = self::is_site_connected_compat();
		} else {
			$site_connected = WC_Helper::is_site_connected();
		}
		return $site_connected;
	}


	private static function has_woo_um_active() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		return is_plugin_active( 'woo-update-manager/woo-update-manager.php' );
	}


	private static function set_wc_facebook_svr_flags() {

		if ( ! function_exists( 'update_option' ) ||
			 ! function_exists( 'get_transient' ) ||
			 ! function_exists( 'set_transient' ) ) {
			return;
		}

		if ( false !== get_transient( 'wc_facebook_svr_flags_ds' ) ) {
			return;
		}

		$wp_woo_flags = 0;

		$is_wp_com = self::is_wp_com();
		if ( $is_wp_com ) {
			$wp_woo_flags |= 1;
		}
		$is_woo_com = self::is_woo_com();
		if ( $is_woo_com ) {
			$wp_woo_flags |= 2;
		}
		$has_plugin_mgr = self::has_woo_um_active();
		if ( $has_plugin_mgr ) {
			$wp_woo_flags |= 4;
		}

		update_option( 'wc_facebook_svr_flags', $wp_woo_flags );
		set_transient( 'wc_facebook_svr_flags_ds', 1, WEEK_IN_SECONDS );
	}


	/**
	 * Gets the main \WC_Facebook_Loader instance.
	 *
	 * Ensures only one instance can be loaded.
	 *
	 * @since 1.10.0
	 *
	 * @return \WC_Facebook_Loader
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

// fire it up!
WC_Facebook_Loader::instance();
