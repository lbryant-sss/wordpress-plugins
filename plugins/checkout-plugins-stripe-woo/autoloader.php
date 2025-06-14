<?php
/**
 * Auto Loader.
 *
 * @package checkout-plugins-stripe-woo
 * @since 0.0.1
 */

namespace CPSW;

use CPSW\Gateway\Stripe\Card_Payments;
use CPSW\Gateway\Stripe\Sepa;
use CPSW\Gateway\Stripe\Alipay;
use CPSW\Gateway\Stripe\Klarna;
use CPSW\Gateway\Stripe\Payment_Request_Api;
use CPSW\Gateway\Stripe\Link_Payment_Token;
use CPSW\Gateway\Stripe\Ideal;
use CPSW\Gateway\Stripe\Bancontact;
use CPSW\Gateway\Stripe\P24;
use CPSW\Gateway\Stripe\Wechat;
use CPSW\Gateway\Stripe\Payment_Element;
use CPSW\Compatibility\Apple_Pay;
use CPSW\Admin\Admin_Controller;
use CPSW\Admin\Backward_Compatibility;
use CPSW\Gateway\Stripe\Webhook;
use CPSW\Gateway\Stripe\Frontend_Scripts;
use CPSW\Wizard\Onboarding;
use CPSW\Gateway\BlockSupport\Credit_Card_Payments;
use CPSW\Gateway\BlockSupport\Ideal_Payments;
use CPSW\Gateway\BlockSupport\Alipay_Payments;
use CPSW\Gateway\BlockSupport\Klarna_Payments;
use CPSW\Gateway\BlockSupport\Stripe_Element;
use CPSW\Gateway\BlockSupport\Sepa_Payments;
use CPSW\Gateway\BlockSupport\Wechat_Payments;
use CPSW\Gateway\BlockSupport\P24_Payments;
use CPSW\Gateway\BlockSupport\Bancontact_Payments;
use CPSW\Inc\Notice;

/**
 * CPSW_Loader
 *
 * @since 0.0.1
 */
class CPSW_Loader {

	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class Instance.
	 * @since 0.0.1
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @since 0.0.1
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Autoload classes.
	 *
	 * @param string $class class name.
	 */
	public function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ . '\\' ) ) {
			return;
		}
	
		// Reject invalid characters to prevent injection.
		if ( preg_match( '/[^a-zA-Z0-9_\\\\]/', $class ) ) {
			return;
		}
	
		$class_to_load = $class;
	
		// Normalize class name to file path.
		$filename = strtolower(
			preg_replace(
				[ '/^' . preg_quote( __NAMESPACE__, '/' ) . '\\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\\/' ],
				[ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
				$class_to_load
			)
		);
	
		$file = CPSW_DIR . $filename . '.php';
	
		$real_file = realpath( $file );
		$real_base = realpath( CPSW_DIR );
	
		// Validate path, check existence, and load.
		if (
			$real_file &&
			$real_base &&
			strpos( $real_file, $real_base ) === 0 &&
			is_readable( $real_file ) &&
			file_exists( $real_file ) &&
			! class_exists( $class, false )
		) {
			/**
			 * Reason for adding an ignore rule:
			 * 
			 * This code is designed to safely include class files in a WordPress plugin.
			 * It uses a controlled autoloader to ensure that only classes within the plugin's namespace are loaded.
			 * The file path is constructed from a known base directory (CPSW_DIR) and the class name,
			 * ensuring that it cannot be manipulated to include arbitrary files.
			 * The use of realpath() ensures that the file exists and is within the expected directory.
			 * The class is only loaded if it does not already exist, preventing multiple inclusions.
			 */
			// phpcs:ignore Squiz.Commenting.InlineComment.InvalidEndChar
			require_once $real_file; // nosemgrep: audit.php.lang.security.file.inclusion-arg
		}
	}
	

	/**
	 * Constructor
	 *
	 * @since 0.0.1
	 */
	public function __construct() {
		// Activation hook.
		register_activation_hook( CPSW_FILE, [ $this, 'install' ] );

		spl_autoload_register( [ $this, 'autoload' ] );
		if ( ! class_exists( '\Stripe\Stripe' ) ) {
			require_once 'lib/vendor/stripe-php/init.php';
		}

		add_action( 'init', [ $this, 'setup_classes' ] ); // Moved here.
		add_action( 'plugins_loaded', [ $this, 'load_classes' ] );
		add_filter( 'plugin_action_links_' . CPSW_BASE, [ $this, 'action_links' ] );
		add_action( 'before_woocommerce_init', [ $this, 'compatibility_declaration' ] );
		add_action( 'woocommerce_init', [ $this, 'frontend_scripts' ] );
		add_action( 'init', [ $this, 'load_cpsw_textdomain' ] );
		add_action( 'woocommerce_blocks_loaded', [ $this, 'woocommerce_block_supports' ] );

		if ( is_admin() ) {
			add_action( 'admin_init', [ $this, 'check_for_onboarding' ] );
		}
	}

	/**
	 * Sets up base classes.
	 *
	 * @return void
	 */
	public function setup_classes() {
		Backward_compatibility::get_instance();
		Admin_Controller::get_instance();
		Apple_Pay::get_instance();
	}

	/**
	 * Includes frontend scripts.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function frontend_scripts() {
		if ( is_admin() ) {
			return;
		}

		Frontend_Scripts::get_instance();
	}

	/**
	 * Adds links in Plugins page
	 *
	 * @param array $links existing links.
	 * @return array
	 * @since 1.0.0
	 */
	public function action_links( $links ) {
		$plugin_links = apply_filters(
			'cpsw_plugin_action_links',
			[
				'cpsw_settings'      => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=cpsw_api_settings' ) . '">' . __( 'Settings', 'checkout-plugins-stripe-woo' ) . '</a>',
				'cpsw_documentation' => '<a href="' . esc_url( 'https://checkoutplugins.com/docs/stripe-api-settings/' ) . '" target="_blank" >' . __( 'Documentation', 'checkout-plugins-stripe-woo' ) . '</a>',
			]
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Loads classes on plugins_loaded hook.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function load_classes() {
		// Initializing Onboarding.
		Onboarding::get_instance();
		Notice::get_instance();
		if ( ! class_exists( 'woocommerce' ) ) {
			Notice::add_custom( 'inactive_wc_notice', 'notice-error', $this->wc_is_not_active(), true );
			return;
		}
		// Initializing Gateways.

		Sepa::get_instance();
		Wechat::get_instance();
		Payment_Element::get_instance();
		Bancontact::get_instance();
		P24::get_instance();
		Klarna::get_instance();
		Ideal::get_instance();
		Alipay::get_instance();
		Card_Payments::get_instance();
		Payment_Request_Api::get_instance();
		Webhook::get_instance();
		Link_Payment_Token::get_instance();
	}

	/**
	 * Loads classes on plugins_loaded hook.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function wc_is_not_active() {
		$install_url = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'install-plugin',
					'plugin' => 'woocommerce',
				),
				admin_url( 'update.php' )
			),
			'install-plugin_woocommerce'
		);
		$output      = '<p>';
		// translators: 1$-2$: opening and closing <strong> tags, 3$-4$: link tags, takes to woocommerce plugin on wp.org, 5$-6$: opening and closing link tags, leads to plugins.php in admin.
		$output .= sprintf( esc_html__( '%1$sCheckout Plugins - Stripe for WooCommerce is currently inactive.%2$s The %3$sWooCommerce plugin%4$s must be active for Checkout Plugins - Stripe for WooCommerce to work. Please %5$sinstall & activate WooCommerce &raquo;%6$s', 'checkout-plugins-stripe-woo' ), '<strong>', '</strong>', '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . esc_url( $install_url ) . '">', '</a>' );
		$output .= '</p>';
		return $output;
	}

	/**
	 * Checks for installation routine
	 * Loads plugins translation file
	 *
	 * @return void
	 * @since 1.3.0
	 */
	public function install() {
		$admin_controller = Admin_Controller::get_instance();
		if ( get_option( 'cpsw_setup_status', false ) || apply_filters( 'cpsw_prevent_onboarding_redirect', false ) || $admin_controller->is_stripe_connected() ) {
			return;
		}

		update_option( 'cpsw_start_onboarding', true );
	}

	/**
	 * Checks whether onboarding is required or not
	 *
	 * @return void
	 * @since 1.3.0
	 */
	public function check_for_onboarding() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! get_option( 'cpsw_start_onboarding', false ) ) {
			return;
		}

		$onboarding_url = admin_url( 'index.php?page=cpsw-onboarding' );

		if ( ! class_exists( 'woocommerce' ) ) {
			$onboarding_url = add_query_arg( 'cpsw_call', 'setup-woocommerce', $onboarding_url );
		}

		delete_option( 'cpsw_start_onboarding' );

		wp_safe_redirect( esc_url_raw( $onboarding_url ) );
		exit();
	}

	/**
	 * Loads plugins translation file
	 *
	 * @return void
	 * @since 1.3.0
	 */
	public function load_cpsw_textdomain() {
		// Default languages directory.
		$lang_dir = CPSW_DIR . 'languages/';

		// Traditional WordPress plugin locale filter.
		global $wp_version;

		$get_locale = get_locale();

		if ( $wp_version >= 4.7 ) {
			$get_locale = get_user_locale();
		}

		$locale = apply_filters( 'plugin_locale', $get_locale, 'checkout-plugins-stripe-woo' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'checkout-plugins-stripe-woo', $locale );

		// Setup paths to current locale file.
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/checkout-plugins-stripe-woo/ folder.
			load_textdomain( 'checkout-plugins-stripe-woo', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/checkout-plugins-stripe-woo/languages/ folder.
			load_textdomain( 'checkout-plugins-stripe-woo', $mofile_local );
		} else {
			// Load the default language files.
			load_plugin_textdomain( 'checkout-plugins-stripe-woo', false, $lang_dir );
		}
	}

	/**
	 * Declares compatibility with WooCommerce
	 *
	 * @return void
	 * @since 1.4.8
	 */
	public function compatibility_declaration() {
		// HPOS compatibility declaration.
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', CPSW_FILE, true );
		}

		// Woo Block compatibility declaration.
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', CPSW_FILE, true );
		}
	}

	/**
	 * Adding support for woocommerce blocks
	 *
	 * @since 1.6.0
	 * @return void
	 */
	public function woocommerce_block_supports() {
		$this->register_payment_methods();
	}

	/**
	 * Register payment methods for woocommerce blocks
	 *
	 * @since 1.6.0
	 * @return void
	 */
	public function register_payment_methods() {
		add_action(
			'woocommerce_blocks_payment_method_type_registration',
			function( \Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {

				$container = \Automattic\WooCommerce\Blocks\Package::container();

				$payment_gateways = [
					Credit_Card_Payments::class,
					Ideal_Payments::class,
					Alipay_Payments::class,
					Klarna_Payments::class,
					Stripe_Element::class,
					Bancontact_Payments::class,
					Sepa_Payments::class,
					Wechat_Payments::class,
					P24_Payments::class,
				];

				// registers as shared instance.
				foreach ( $payment_gateways as $gateway_class ) {
					$container->register(
						$gateway_class,
						function () use ( $gateway_class ) {
							return new $gateway_class();
						}
					);

					// Register the payment gateway with the PaymentMethodRegistry.
					$payment_method_registry->register(
						$container->get( $gateway_class )
					);
				}
			},
			5
		);
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
CPSW_Loader::get_instance();

