<?php
/**
 * Bancontact Gateway
 *
 * @package checkout-plugins-stripe-woo
 * @since 1.3.0
 */

namespace CPSW\Gateway\Stripe;

use CPSW\Inc\Helper;
use CPSW\Inc\Traits\Get_Instance;
use CPSW\Gateway\Local_Gateway;

/**
 * Bancontact
 *
 * @since 1.3.0
 */
class Bancontact extends Local_Gateway {

	use Get_Instance;

	/**
	 * Gateway id
	 *
	 * @var string
	 */
	public $id = 'cpsw_bancontact';

	/**
	 * Payment method types
	 *
	 * @var string
	 */
	public $payment_method_types = 'bancontact';

	/**
	 * Constructor
	 *
	 * @since 1.3.0
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'init', [ $this, 'init_gateway' ] );
	}

	/**
	 * Initializes the gateway.
	 *
	 * Sets up the gateway's properties and settings.
	 *
	 * @since 1.11.0
	 */
	public function init_gateway() {
		$this->method_title       = __( 'Bancontact', 'checkout-plugins-stripe-woo' );
		$this->method_description = $this->method_description();
		$this->has_fields         = true;
		$this->init_supports();

		$this->init_form_fields();
		$this->init_settings();
		// get_option should be called after init_form_fields().
		$this->title             = $this->get_option( 'title' );
		$this->description       = $this->get_option( 'description' );
		$this->order_button_text = $this->get_option( 'order_button_text' );
	}

	/**
	 * Description for Bancontact gateway
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public function method_description() {
		$payment_description = $this->payment_description();

		return sprintf(
			/* translators: %1$s: Break, %2$s: Gateway appear message, %3$s: Break, %4$s: Gateway appear message currency wise, %4$s:  HTML entities */
			__( 'Accept payments using Bancontact. %1$s %2$s', 'checkout-plugins-stripe-woo' ),
			'<br/>',
			$payment_description
		);
	}

	/**
	 * Returns all supported currencies for this payment method.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	public function get_supported_currency() {
		return apply_filters(
			'cpsw_bancontact_supported_currencies',
			[
				'EUR',
			]
		);
	}

	/**
	 * Add more gateway form fields
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	public function get_default_settings() {
		$local_settings = parent::get_default_settings();

		$local_settings['allowed_countries']['default']  = 'specific';
		$local_settings['specific_countries']['default'] = [ 'BE' ];

		return $local_settings;
	}

	/**
	 * Checks whether this gateway is available.
	 *
	 * @since 1.3.0
	 *
	 * @return boolean
	 */
	public function is_available() {
		if ( ! in_array( $this->get_currency(), $this->get_supported_currency(), true ) ) {
			return false;
		}

		return parent::is_available();
	}

	/**
	 * Creates markup for payment form for card payments
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public function payment_fields() {
		global $wp;

		$user  = wp_get_current_user();
		$total = WC()->cart->total;

		// If paying from order, we need to get total from order not cart.
		if ( isset( $_GET['pay_for_order'] ) && ! empty( $_GET['key'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$order = wc_get_order( wc_clean( $wp->query_vars['order-pay'] ) );
			$total = $order->get_total();
		}

		if ( is_add_payment_method_page() ) {
			$pay_button_text = __( 'Add Payment', 'checkout-plugins-stripe-woo' );
			$total           = '';
		} else {
			$pay_button_text = '';
		}

		/**
		 * Action before payment field.
		 *
		 * @since 1.3.0
		 */
		do_action( $this->id . '_before_payment_field_checkout' );

		echo '<div
			id="cpsw-bancontact-payment-data"
			data-amount="' . esc_attr( $total ) . '"
			data-currency="' . esc_attr( strtolower( $this->get_currency() ) ) . '">';

		if ( $this->description ) {
			echo wp_kses_post( $this->description );
		}

		echo '</div>';
		if ( 'test' === Helper::get_payment_mode() ) {
			echo '<div class="cpsw_stripe_test_description">';
			echo wp_kses_post( $this->get_test_mode_description() );
			echo '</div>';
		}

		/**
		 * Action after payment field.
		 *
		 * @since 1.3.0
		 */
		do_action( $this->id . '_after_payment_field_checkout' );
	}
}
