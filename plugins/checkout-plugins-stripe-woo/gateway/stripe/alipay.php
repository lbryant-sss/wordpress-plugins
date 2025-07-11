<?php
/**
 * Alipay Gateway
 *
 * @package checkout-plugins-stripe-woo
 * @since 1.2.0
 */

namespace CPSW\Gateway\Stripe;

use CPSW\Inc\Helper;
use CPSW\Inc\Traits\Get_Instance;
use CPSW\Gateway\Local_Gateway;

/**
 * Alipay
 *
 * @since 1.2.0
 */
class Alipay extends Local_Gateway {

	use Get_Instance;

	/**
	 * Gateway id
	 *
	 * @var string
	 */
	public $id = 'cpsw_alipay';

	/**
	 * Payment method types
	 *
	 * @var string
	 */
	public $payment_method_types = 'alipay';

	/**
	 * Allowed countries based on currency codes.
	 *
	 * The keys represent currency codes, and the values are arrays of countries allowed for each currency.
	 * 
	 * Reference : https://stripe.com/docs/payments/alipay#supported-currencies
	 * 
	 * @var array
	 */
	public $allowed_countries = [
		'EUR' => [ 'AT', 'BE', 'BG', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'NO', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'CH' ],
	];

	/**
	 * Constructor
	 *
	 * @since 1.2.0
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
		$this->method_title       = __( 'Alipay', 'checkout-plugins-stripe-woo' );
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
	 * Description for alipay gateway
	 *
	 * @since 1.2.0
	 *
	 * @return string
	 */
	public function method_description() {
		$payment_description = $this->payment_description();
		/* translators: HTML Entities.*/
		$extra_description = $this->is_current_section() && 'EUR' === get_woocommerce_currency() ? sprintf( __( '%1$sEUR%2$s is supported only for billing country %1$sDenmark (DK), Belgium (BE), Austria (AT), Bulgaria (BG), Cyprus (CY), Czech Republic (CZ), Estonia (EE), Finland (FI), France (FR), Germany (DE), Greece (GR), Ireland (IE), Italy (IT), Latvia (LV), Lithuania (LT), Luxembourg (LU), Malta (MT), Netherlands (NL), Norway (NO), Portugal (PT), Romania (RO), Slovakia (SK), Slovenia (SI), Spain (ES), Sweden (SE), and Switzerland (CH)%2$s.', 'checkout-plugins-stripe-woo' ), '<strong>', '</strong>' ) : '';

		return sprintf(
			/* translators: %1$s: Break, %2$s: Gateway appear message, %3$s: Break, %4$s: Gateway appear message currency wise, %4$s:  HTML entities */
			__( 'Accept payments using Alipay. %1$s %2$s %3$s %4$s', 'checkout-plugins-stripe-woo' ),
			'<br/>',
			$payment_description,
			'<br/>',
			$extra_description
		);
	}

	/**
	 * Returns all supported currencies for this payment method.
	 *
	 * @since 1.2.0
	 *
	 * @return array
	 */
	public function get_supported_currency() {
		return apply_filters(
			'cpsw_alipay_supported_currencies',
			[
				'EUR',
				'AUD',
				'CAD',
				'CNY',
				'GBP',
				'HKD',
				'JPY',
				'NZD',
				'SGD',
				'USD',
				'MYR',
			]
		);
	}

	/**
	 * Checks whether this gateway is available.
	 *
	 * @since 1.2.0
	 *
	 * @return boolean
	 */
	public function is_available() {
		if ( ! in_array( $this->get_currency(), $this->get_supported_currency(), true ) ) {
			return false;
		}

		// Perform a conditional check based on currency and billing country.
		// This check is applicable only for the classic checkout. For checkout blocks, it's handled in JavaScript.
		if ( ! Helper::is_block_checkout() ) {
			if ( 'EUR' === $this->get_currency() && ! in_array( $this->get_billing_country(), $this->allowed_countries['EUR'], true ) ) {
				return false;
			}
		}
		return parent::is_available();
	}

	/**
	 * Creates markup for payment form for card payments
	 *
	 * @since 1.2.0
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
			id="cpsw-alipay-payment-data"
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
