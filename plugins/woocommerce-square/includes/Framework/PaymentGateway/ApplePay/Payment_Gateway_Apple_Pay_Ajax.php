<?php
/**
 * WooCommerce Payment Gateway Framework
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0 or later
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * @since     3.0.0
 * @author    WooCommerce / SkyVerge
 * @copyright Copyright (c) 2013-2016, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0 or later
 *
 * Modified by WooCommerce on 19 December 2021.
 */

namespace WooCommerce\Square\Framework\PaymentGateway\ApplePay;

use WooCommerce\Square\Framework\Square_Helper;

defined( 'ABSPATH' ) or exit;

/**
 * The Apple Pay AJAX handler.
 *
 * @since 3.0.0
 */
class Payment_Gateway_Apple_Pay_AJAX {


	/** @var Payment_Gateway_Apple_Pay $handler the Apple Pay handler instance */
	protected $handler;


	/**
	 * Constructs the class.
	 *
	 * @since 3.0.0
	 *
	 * @param Payment_Gateway_Apple_Pay $handler the Apple Pay handler instance
	 */
	public function __construct( Payment_Gateway_Apple_Pay $handler ) {

		$this->handler = $handler;

		if ( $this->get_handler()->is_available() ) {

			add_action( 'wp_ajax_sv_wc_apple_pay_get_payment_request',        array( $this, 'get_payment_request' ) );
			add_action( 'wp_ajax_nopriv_sv_wc_apple_pay_get_payment_request', array( $this, 'get_payment_request' ) );

			// validate the merchant
			add_action( 'wp_ajax_sv_wc_apple_pay_validate_merchant',        array( $this, 'validate_merchant' ) );
			add_action( 'wp_ajax_nopriv_sv_wc_apple_pay_validate_merchant', array( $this, 'validate_merchant' ) );

			// recalculate the payment request totals
			add_action( 'wp_ajax_sv_wc_apple_pay_recalculate_totals',        array( $this, 'recalculate_totals' ) );
			add_action( 'wp_ajax_nopriv_sv_wc_apple_pay_recalculate_totals', array( $this, 'recalculate_totals' ) );

			// process the payment
			add_action( 'wp_ajax_sv_wc_apple_pay_process_payment',        array( $this, 'process_payment' ) );
			add_action( 'wp_ajax_nopriv_sv_wc_apple_pay_process_payment', array( $this, 'process_payment' ) );
		}
	}


	/**
	 * Gets a payment request for the specified type.
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 */
	public function get_payment_request() {

		$this->get_handler()->log( 'Getting payment request' );

		try {

			$request = $this->get_handler()->get_cart_payment_request( WC()->cart );

			$this->get_handler()->log( sprintf( "Payment Request:\n %s", print_r( $request, true ) ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r

			wp_send_json_success( wp_json_encode( $request ) );

		} catch ( \Exception $e ) {

			$this->get_handler()->log( sprintf( 'Could not build payment request. %s', $e->getMessage() ) );

			wp_send_json_error( array(
				'message' => $e->getMessage(),
				'code'    => $e->getCode(),
			) );
		}
	}


	/**
	 * Validates the merchant.
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 */
	public function validate_merchant() {

		$this->get_handler()->log( 'Validating merchant' );

		check_ajax_referer( 'sv_wc_apple_pay_validate_merchant', 'nonce' );

		$merchant_id = Square_Helper::get_post( 'merchant_id' );
		$url         = Square_Helper::get_post( 'url' );

		try {

			$response = $this->get_handler()->get_api()->validate_merchant( $url, $merchant_id, home_url(), get_bloginfo( 'name' ) );

			wp_send_json_success( $response->get_merchant_session() );

		} catch ( \Exception $e ) {

			/* translators: %s: error message */
			$this->get_handler()->log( sprintf( esc_html__( 'Could not validate merchant. %s', 'woocommerce-square' ), $e->getMessage() ) );

			wp_send_json_error( array(
				'message' => $e->getMessage(),
				'code'    => $e->getCode(),
			) );
		}
	}


	/**
	 * Recalculates the totals for the current payment request.
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 */
	public function recalculate_totals() {

		$this->get_handler()->log( 'Recalculating totals' );

		check_ajax_referer( 'sv_wc_apple_pay_recalculate_totals', 'nonce' );

		try {

			// if a contact is passed, set the customer address data
			if ( isset( $_REQUEST['contact'] ) && is_array( $_REQUEST['contact'] ) ) {

				$contact = wp_parse_args(
					wc_clean( wp_unslash( $_REQUEST['contact'] ) ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					array(
						'administrativeArea' => null,
						'countryCode'        => null,
						'locality'           => null,
						'postalCode'         => null,
					)
				);

				$state    = $contact['administrativeArea'];
				$country  = strtoupper( $contact['countryCode'] );
				$city     = $contact['locality'];
				$postcode = $contact['postalCode'];

				WC()->customer->set_shipping_city( $city );
				WC()->customer->set_shipping_state( $state );
				WC()->customer->set_shipping_country( $country );
				WC()->customer->set_shipping_postcode( $postcode );

				if ( $country ) {
					WC()->customer->set_calculated_shipping( true );
				}
			}

			$chosen_shipping_methods = ( $method = Square_Helper::get_request( 'method' ) ) ? array( wc_clean( $method ) ) : array();

			WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );

			$payment_request = $this->get_handler()->recalculate_totals();

			$data = array(
				'shipping_methods' => $payment_request['shippingMethods'],
				'line_items'       => array_values( $payment_request['lineItems'] ),
				'total'            => $payment_request['total'],
			);

			$this->get_handler()->log( sprintf( "New totals:\n %s", print_r( $data, true ) ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r

			wp_send_json_success( $data );

		} catch ( \Exception $e ) {

			$this->get_handler()->log( $e->getMessage() );

			wp_send_json_error( array(
				'message' => $e->getMessage(),
				'code'    => $e->getCode(),
			) );
		}
	}


	/**
	 * Processes the payment after the Apple Pay authorization.
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 */
	public function process_payment() {

		$this->get_handler()->log( 'Processing payment' );

		$type     = Square_Helper::get_post( 'type' );
		$response = stripslashes( Square_Helper::get_post( 'payment' ) );

		$this->get_handler()->store_payment_response( $response );

		try {

			$result = $this->get_handler()->process_payment( $type, $response );

			wp_send_json_success( $result );

		} catch ( \Exception $e ) {

			$this->get_handler()->log( sprintf( 'Payment failed. %s', $e->getMessage() ) );

			wp_send_json_error( array(
				'message' => $e->getMessage(),
				'code'    => $e->getCode(),
			) );
		}
	}


	/**
	 * Gets the Apple Pay handler instance.
	 *
	 * @since 3.0.0
	 *
	 * @return Payment_Gateway_Apple_Pay
	 */
	protected function get_handler() {

		return $this->handler;
	}
}
