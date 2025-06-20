<?php
/**
 * Checkout Ajax.
 *
 * @package CartFlows
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Global Checkout
 *
 * @since 1.0.0
 */
class Cartflows_Checkout_Ajax {


	/**
	 * Member Variable
	 *
	 * @var object instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 *  Constructor
	 */
	public function __construct() {

		/* Ajax Endpoint */
		add_filter( 'woocommerce_ajax_get_endpoint', array( $this, 'get_ajax_endpoint' ), 10, 2 );

		add_action( 'wp_ajax_wcf_woo_apply_coupon', array( $this, 'apply_coupon' ) );
		add_action( 'wp_ajax_nopriv_wcf_woo_apply_coupon', array( $this, 'apply_coupon' ) );

		add_action( 'wp_ajax_wcf_woo_remove_coupon', array( $this, 'remove_coupon' ) );
		add_action( 'wp_ajax_nopriv_wcf_woo_remove_coupon', array( $this, 'remove_coupon' ) );

		add_action( 'wp_ajax_wcf_woo_remove_cart_product', array( $this, 'wcf_woo_remove_cart_product' ) );
		add_action( 'wp_ajax_nopriv_wcf_woo_remove_cart_product', array( $this, 'wcf_woo_remove_cart_product' ) );

		add_action( 'wp_ajax_nopriv_wcf_check_email_exists', array( $this, 'check_email_exists' ) );
		add_action( 'wp_ajax_nopriv_wcf_woocommerce_login', array( $this, 'woocommerce_user_login' ) );

	}

	/**
	 * Get ajax end points.
	 *
	 * @param string $endpoint_url end point URL.
	 * @param string $request end point request.
	 * @return string
	 */
	public function get_ajax_endpoint( $endpoint_url, $request ) {
		global $post;

		if ( ! empty( $post ) && ! empty( $_SERVER['REQUEST_URI'] ) ) {

			if ( _is_wcf_checkout_type() ) {

				$query_args = array();
				$url        = $endpoint_url;

				if ( mb_strpos( $endpoint_url, 'checkout', 0, 'utf-8' ) === false ) {

					if ( '' === $request ) {
						$query_args = array(
							'wc-ajax' => '%%endpoint%%',
						);
					} else {
						$query_args = array(
							'wc-ajax' => $request,
						);
					}

					$uri = explode( '?', esc_url_raw( $_SERVER['REQUEST_URI'] ), 2 );
					$url = esc_url( $uri[0] );
				}

				$query_args['wcf_checkout_id'] = $post->ID;

				$endpoint_url = add_query_arg( $query_args, $url );
			}
		}

		return $endpoint_url;
	}

	/**
	 * Apply coupon on submit of custom coupon form.
	 */
	public function apply_coupon() {
		$response = '';

		if ( ! check_ajax_referer( 'wcf-apply-coupon', 'security', false ) ) {
			$response_data = array(
				'status' => false,
				'error'  => __( 'Nonce validation failed', 'cartflows' ),
			);
			wp_send_json_error( $response_data );
		}

		// Update the billing email before adding a coupon required for coupon conditions.
		$this->update_billing_email();

		ob_start();

		if ( ! empty( $_POST['coupon_code'] ) ) {
			$result = WC()->cart->add_discount( sanitize_text_field( wp_unslash( $_POST['coupon_code'] ) ) );
		} else {
			wc_add_notice( WC_Coupon::get_generic_coupon_error( WC_Coupon::E_WC_COUPON_PLEASE_ENTER ), 'error' );
		}

		$response = array(
			'status' => $result,
			'msg'    => wc_print_notices( true ),
		);

		ob_clean(); // Clearing the uncessary echo HTML.
		wp_send_json( $response );

		die();
	}

	/**
	 * Remove coupon.
	 */
	public function remove_coupon() {
		check_ajax_referer( 'wcf-remove-coupon', 'security' );
		$coupon = isset( $_POST['coupon_code'] ) ? sanitize_text_field( wp_unslash( $_POST['coupon_code'] ) ) : false;

		if ( empty( $coupon ) ) {
			echo "<div class='woocommerce-error'>" . esc_html__( 'Sorry there was a problem removing this coupon.', 'cartflows' );
		} else {
			WC()->cart->remove_coupon( $coupon );
			echo "<div class='woocommerce-error'>" . esc_html__( 'Coupon has been removed.', 'cartflows' ) . '</div>';
		}
		wc_print_notices();
		wp_die();
	}

	/**
	 * Remove cart item.
	 */
	public function wcf_woo_remove_cart_product() {
		check_ajax_referer( 'wcf-remove-cart-product', 'security' );
		$product_key   = isset( $_POST['p_key'] ) ? sanitize_text_field( wp_unslash( $_POST['p_key'] ) ) : false;
		$product_id    = isset( $_POST['p_id'] ) ? sanitize_text_field( wp_unslash( $_POST['p_id'] ) ) : '';
		$product_title = get_the_title( $product_id );

		$needs_shipping = false;
		$is_order_bump  = false;
		$order_bump_id  = '';

		// Check if the product is an order bump before removing it.
		if ( ! empty( $product_key ) ) {
			$cart_item = WC()->cart->get_cart_item( $product_key );
			if ( isset( $cart_item['cartflows_bump'] ) && $cart_item['cartflows_bump'] ) {
				$is_order_bump = true;
				$order_bump_id = isset( $cart_item['ob_id'] ) ? $cart_item['ob_id'] : '';
			}
			
			WC()->cart->remove_cart_item( $product_key );
			$msg = "<div class='woocommerce-message'>" . $product_title . __( ' has been removed.', 'cartflows' ) . '</div>';
		} else {
			$msg = "<div class='woocommerce-message'>" . __( 'Sorry there was a problem removing ', 'cartflows' ) . $product_title;
		}

		foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
			if ( $values['data']->needs_shipping() ) {
				$needs_shipping = true;
				break;
			}
		}

		$response = array(
			'need_shipping' => $needs_shipping,
			'msg'           => $msg,
			'is_order_bump' => $is_order_bump,
			'order_bump_id' => $order_bump_id,
		);

		echo wp_json_encode( $response );
		wp_die();
	}


	/**
	 * Check email exist.
	 */
	public function check_email_exists() {

		check_ajax_referer( 'check-email-exist', 'security' );

		$email_address = isset( $_POST['email_address'] ) ? sanitize_email( wp_unslash( $_POST['email_address'] ) ) : false;

		$is_exist = email_exists( $email_address );

		$response = array(
			'success'          => boolval( $is_exist ),
			'is_login_allowed' => 'yes' === get_option( 'woocommerce_enable_checkout_login_reminder' ),
			'msg'              => $is_exist ? __( 'Email Exist.', 'cartflows' ) : __( 'Email not exist', 'cartflows' ),
		);

		wp_send_json_success( $response );
	}

	/**
	 * Update billing email address before applying the coupon. This is used for coupon conditions.
	 *
	 * @return void
	 * @since 2.0.12
	 */
	public function update_billing_email() {

		if ( ! wcf()->is_woo_active ) {
			return;
		}

		if ( ! class_exists( 'Automattic\WooCommerce\Utilities\ArrayUtil' ) ) {
			return;
		}

		// Sanitize the billing email.
		$billing_email = ! empty( $_POST['billing_email'] ) ? sanitize_email( wp_unslash( $_POST['billing_email'] ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Missing

		$billing_email = \Automattic\WooCommerce\Utilities\ArrayUtil::get_value_or_default(
			array(
				'billing_email' => $billing_email,
			),
			'billing_email' 
		);

		if ( is_string( $billing_email ) && is_email( $billing_email ) ) {
			wc()->customer->set_billing_email( $billing_email );
		}
	}

	/**
	 * Check email exist.
	 */
	public function woocommerce_user_login() {

		check_ajax_referer( 'woocommerce-login', 'security' );

		$response = array(
			'success' => false,
		);

		$email_address = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : false;
		$password      = isset( $_POST['password'] ) ? wp_unslash( $_POST['password'] ) : false; // phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$creds = array(
			'user_login'    => $email_address,
			'user_password' => $password,
			'remember'      => false,
		);

		$user = wp_signon( $creds, false );

		if ( ! is_wp_error( $user ) ) {

			$response = array(
				'success' => true,
			);
		} else {
			$response['error'] = wp_kses_post( $user->get_error_message() );
		}

		wp_send_json_success( $response );
	}

}

/**
 *  Kicking this off by calling 'get_instance()' method
 */
Cartflows_Checkout_Ajax::get_instance();
