<?php
/**
 * Class WC_REST_Stripe_Account_Controller
 */

defined( 'ABSPATH' ) || exit;

/**
 * REST controller for retrieving Stripe's account data.
 *
 * @since 5.6.0
 */
class WC_REST_Stripe_Account_Controller extends WC_Stripe_REST_Base_Controller {
	/**
	 * Endpoint path.
	 *
	 * @var string
	 */
	protected $rest_base = 'wc_stripe/account';

	/**
	 * The account data utility.
	 *
	 * @var WC_Stripe_Account
	 */
	private $account;

	/**
	 * Stripe payment gateway.
	 *
	 * @var WC_Gateway_Stripe
	 */
	private $gateway;

	public function __construct( WC_Gateway_Stripe $gateway, WC_Stripe_Account $account ) {
		$this->gateway = $gateway;
		$this->account = $account;
	}

	/**
	 * Configure REST API routes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_account' ],
				'permission_callback' => [ $this, 'check_permission' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/summary',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_account_summary' ],
				'permission_callback' => [ $this, 'check_permission' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/webhook-status-message',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_webhook_status_message' ],
				'permission_callback' => [ $this, 'check_permission' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/refresh',
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'refresh_account' ],
				'permission_callback' => [ $this, 'check_permission' ],
			]
		);
	}

	/**
	 * Retrieve the Stripe account information.
	 *
	 * @return WP_REST_Response
	 */
	public function get_account() {
		return new WP_REST_Response(
			[
				'account'                 => $this->account->get_cached_account_data(),
				'testmode'                => WC_Stripe_Mode::is_test(),
				'webhook_status_code'     => WC_Stripe_Webhook_State::get_webhook_status_code(),
				'webhook_status_message'  => WC_Stripe_Webhook_State::get_webhook_status_message(),
				'webhook_url'             => WC_Stripe_Helper::get_webhook_url(),
				'configured_webhook_urls' => WC_Stripe_Webhook_State::get_configured_webhook_urls(),
				'is_webhook_enabled'      => $this->account->is_webhook_enabled(),
				'oauth_connections'       => [
					'test' => $this->get_account_oauth_connection_data( 'test' ),
					'live' => $this->get_account_oauth_connection_data( 'live' ),
				],
			]
		);
	}

	/**
	 * Return a summary of Stripe account details.
	 *
	 * @return WP_REST_Response
	 */
	public function get_account_summary() {
		$account = $this->account->get_cached_account_data();

		// Use statement descriptor from settings, falling back to Stripe account statement descriptor if needed.
		$statement_descriptor = WC_Stripe_Helper::clean_statement_descriptor( $this->gateway->get_option( 'statement_descriptor' ) );
		if ( empty( $statement_descriptor ) ) {
			$statement_descriptor = $account['settings']['payments']['statement_descriptor'];
		}
		if ( empty( $statement_descriptor ) ) {
			$statement_descriptor = null;
		}

		return new WP_REST_Response(
			[
				'has_pending_requirements' => $this->account->has_pending_requirements(),
				'has_overdue_requirements' => $this->account->has_overdue_requirements(),
				'current_deadline'         => $account['requirements']['current_deadline'] ?? null,
				'status'                   => $this->account->get_account_status(),
				'statement_descriptor'     => $statement_descriptor,
				'store_currencies'         => [
					'default'   => $account['default_currency'] ?? get_woocommerce_currency(),
					'supported' => $this->account->get_supported_store_currencies(),
				],
				'country'                  => $account['country'] ?? WC()->countries->get_base_country(),
				'is_live'                  => WC_Stripe_Mode::is_live() && ( $account['charges_enabled'] ?? false ),
				'test_mode'                => WC_Stripe_Mode::is_test(),
			]
		);
	}

	/**
	 * Retrieve the webhook status information.
	 *
	 * @return WP_REST_Response
	 */
	public function get_webhook_status_message() {
		return new WP_REST_Response(
			[
				'code'    => WC_Stripe_Webhook_State::get_webhook_status_code(),
				'message' => WC_Stripe_Webhook_State::get_webhook_status_message(),
			]
		);
	}

	/**
	 * Clears the cached account data and returns the updated one.
	 *
	 * @return WP_REST_Response
	 */
	public function refresh_account() {
		$this->account->clear_cache();

		// calling the same "get" method, so that the data format is the same.
		return $this->get_account();
	}

	/**
	 * Generates the OAuth connection data for the given mode.
	 *
	 * @param string $mode The mode. Can be 'test' or 'live'.
	 * @return array The connection data.
	 */
	private function get_account_oauth_connection_data( $mode ) {
		$connection = [
			'connected' => (bool) WC_Stripe::get_instance()->connect->is_connected_via_oauth( $mode ),
			'type'      => WC_Stripe::get_instance()->connect->get_connection_type( $mode ),
		];

		// If the connection is an app connection, check if the keys have expired.
		if ( 'app' === $connection['type'] ) {
			$connection['expired'] = $this->account->get_cached_account_data( $mode ) ? false : true; // If we have the account data, it's not expired.
		}

		return $connection;
	}
}
