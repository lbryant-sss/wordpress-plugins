<?php
/**
 * Plugin Name: WC Custom Thank You
 * Plugin URI: https://wordpress.org/plugins/wc-custom-thank-you/
 * Description: A WooCommerce extension that allows you to define e custom Thank you page.
 * Version: 1.2.1
 * Author: Nicola Mustone
 * Author URI: https://nicola.blog/
 * Requires at least: 4.1
 *
 * Tested up to: 4.9.7
 *
 * WC tested up to: 3.4.3
 *
 * Text Domain: woocommerce-custom-thankyou
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Required functions
 */
if ( ! function_exists( 'is_woocommerce_active' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Check if WooCommerce is active, and if it isn't, disable the plugin.
 */
if ( ! is_woocommerce_active() ) {
	return;
}

/**
 * Main plugin class.
 */
class WC_Custom_Thankyou {
	/**
	 * Class instance
	 *
	 * @static
	 * @access protected
	 * @var WC_Custom_Thankyou
	 */
	protected static $instance;

	/**
	 * Thank you page ID
	 *
	 * @var int
	 */
	public $page_id;

	/**
	 * Main WC_Custom_Thankyou Instance
	 *
	 * Ensures only one instance of WC_Custom_Thankyou is loaded or can be loaded.
	 *
	 * @static
	 * @see wc_custom_thankyou()
	 * @return WC_Custom_Thankyou - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'woocommerce-custom-thankyou' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'woocommerce-custom-thankyou' ), '1.0.0' );
	}

	/**
	 * __construct
	 */
	public function __construct() {
		// Set up localization.
		$this->load_plugin_textdomain();

		$this->page_id = get_option( 'woocommerce_custom_thankyou_page_id' );

		add_action( 'template_redirect', array( $this, 'custom_redirect_after_purchase' ) );
		add_filter( 'the_content', array( $this, 'custom_thankyou_page_content' ) );
		add_filter( 'woocommerce_is_order_received_page', array( $this, 'custom_thankyou_is_order_received_page' ) );

		// Admin Settings.
		add_filter( 'woocommerce_settings_pages', array( $this, 'add_settings' ) );
	}

	/**
	 * Checks if the page shown is the Custom Thank you page and returns true in case.
	 *
	 * @param  bool $is_order_received_page Original value from the filter.
	 * @return bool
	 */
	public function custom_thankyou_is_order_received_page( $is_order_received_page ) {
		if ( is_page( $this->page_id ) ) {
			return true;
		}

		return $is_order_received_page;
	}

	/**
	 * Redirects the customer to the custom Thank you page
	 */
	public function custom_redirect_after_purchase() {
		global $wp;

		if ( is_checkout() && ! empty( $wp->query_vars['order-received'] ) ) {
			$order_id  = absint( $wp->query_vars['order-received'] );
			$order_key = wc_clean( $_GET['key'] );

			$redirect  = get_permalink( $this->page_id );
			$redirect  = add_query_arg( array(
				'order' => $order_id,
				'key'   => $order_key,
			), $redirect );

			wp_safe_redirect( $redirect );
			exit;
		}
	}

	/**
	 * Prints the custom Thank you page content before the templates
	 *
	 * @param  string $content The content of the custom Thank you page.
	 * @return string
	 */
	public function custom_thankyou_page_content( $content ) {
		// Check if is the correct page.
		if ( ! is_page( $this->page_id ) ) {
			return $content;
		}

		// Check if the order ID exists.
		if ( empty( $_GET['key'] ) || empty( $_GET['order'] ) ) {
			return $content;
		}

		wc_print_notices();

		$order     = false;
		$order_id  = absint( apply_filters( 'woocommerce_thankyou_order_id', absint( $_GET['order'] ) ) );
		$order_key = wc_clean( apply_filters( 'woocommerce_thankyou_order_key', empty( $_GET['key'] ) ? '' : wc_clean( $_GET['key'] ) ) );

		if ( $order_id > 0 ) {
			$order = wc_get_order( $order_id );
			if ( $order->get_order_key() !== $order_key ) {
				$order = false;
			}
		}

		if ( false === $order || $order->get_id() !== $order_id || $order->get_order_key() !== $order_key ) {
			return '<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">' . apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce-custom-thankyou' ), null ) . '</p>';
		}

		// Empty awaiting payment session.
		unset( WC()->session->order_awaiting_payment );

		// Empty current cart.
		wc_empty_cart();

		ob_start();

		wc_get_template( 'checkout/thankyou.php', array( 'order' => $order ) );

		$content .= ob_get_contents();
		ob_end_clean();

		return $content;
	}

	/**
	 * Add the Thank you page dropdown in Settings > Checkout
	 *
	 * @param  array $settings The core settings.
	 * @return array
	 */
	public function add_settings( $settings ) {
		$settings[] = array( 'title' => esc_html__( 'Custom Thank You', 'woocommerce-custom-thankyou' ), 'type' => 'title', 'id' => 'custom_thankyou_options' );

		$settings[] = array(
			'title'    => esc_html__( 'Thank You Page', 'woocommerce-custom-thankyou' ),
			'id'       => 'woocommerce_custom_thankyou_page_id',
			'type'     => 'single_select_page',
			'default'  => '',
			'class'    => 'wc-enhanced-select-nostd',
			'css'      => 'min-width:300px;',
			'desc_tip' => true,
		);

		$settings[] = array( 'type' => 'sectionend', 'id' => 'custom_thankyou_options' );

		return $settings;
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales are found in:
	 *        WP_LANG_DIR/woocommerce-custom-thankyou/woocommerce-custom-thankyou-LOCALE.mo
	 *        woocommerce-custom-thankyou/languages/woocommerce-custom-thankyou-LOCALE.mo (which if not found falls back to:)
	 *        WP_LANG_DIR/plugins/woocommerce-custom-thankyou-LOCALE.mo
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-custom-thankyou' );

		load_textdomain( 'woocommerce-custom-thankyou', WP_LANG_DIR . '/woocommerce-custom-thankyou/woocommerce-custom-thankyou-' . $locale . '.mo' );
		load_textdomain( 'woocommerce-custom-thankyou', WP_LANG_DIR . '/plugins/woocommerce-custom-thankyou-' . $locale . '.mo' );

		load_plugin_textdomain( 'woocommerce-custom-thankyou', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
}

/**
 * Returns the main instance of WC Customer Messages to prevent the need to use globals.
 *
 * @return WooCommerce_Customer_Messages
 */
function wc_custom_thankyou() {
	return WC_Custom_Thankyou::instance();
}

// Let's start the game!
add_action( 'woocommerce_init', 'wc_custom_thankyou' );
