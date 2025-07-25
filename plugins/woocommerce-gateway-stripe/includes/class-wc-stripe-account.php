<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Stripe_Account class.
 *
 * Communicates with Stripe API.
 */
class WC_Stripe_Account {

	/**
	 * The Account Data cache key.
	 *
	 * @var string
	 */
	const ACCOUNT_CACHE_KEY = 'account_data';

	/**
	 * The Account Data cache expiration (TTL).
	 *
	 * @var int
	 */
	const ACCOUNT_CACHE_EXPIRATION = 2 * HOUR_IN_SECONDS;

	const LIVE_WEBHOOK_STATUS_OPTION = 'wcstripe_webhook_status_live';
	const TEST_WEBHOOK_STATUS_OPTION = 'wcstripe_webhook_status_test';

	const STATUS_COMPLETE        = 'complete';
	const STATUS_NO_ACCOUNT      = 'NOACCOUNT';
	const STATUS_RESTRICTED_SOON = 'restricted_soon';
	const STATUS_RESTRICTED      = 'restricted';

	/**
	 * List of webhook events that this plugin listens to.
	 * Based on WC_Stripe_Webhook_Handler::process_webhook()
	 */
	const WEBHOOK_EVENTS = [
		'account.updated',
		'source.chargeable',
		'source.canceled',
		'charge.succeeded',
		'charge.failed',
		'charge.captured',
		'charge.dispute.created',
		'charge.dispute.closed',
		'charge.refunded',
		'charge.refund.updated',
		'review.opened',
		'review.closed',
		'payment_intent.processing',
		'payment_intent.succeeded',
		'payment_intent.payment_failed',
		'payment_intent.amount_capturable_updated',
		'payment_intent.requires_action',
		'setup_intent.succeeded',
		'setup_intent.setup_failed',
	];

	/**
	 * The Stripe connect instance.
	 *
	 * @var WC_Stripe_Connect
	 */
	private $connect;

	/**
	 * The Stripe API class to access the static method.
	 *
	 * @var WC_Stripe_API
	 */
	private $stripe_api;

	/**
	 * Constructor
	 *
	 * @param WC_Stripe_Connect $connect Stripe connect
	 * @param string $stripe_api Stripe API class
	 */
	public function __construct( WC_Stripe_Connect $connect, $stripe_api ) {
		$this->connect    = $connect;
		$this->stripe_api = $stripe_api;
	}

	/**
	 * Gets and caches the data for the account connected to this site.
	 *
	 * @param string|null $mode Optional. The mode to get the account data for. 'live' or 'test'. Default will use the current mode.
	 * @return array Account data or empty if failed to retrieve account data.
	 */
	public function get_cached_account_data( $mode = null ) {
		if ( ! $this->connect->is_connected( $mode ) ) {
			return [];
		}

		$account = $this->read_account_from_cache();

		if ( ! empty( $account ) ) {
			return $account;
		}

		return $this->cache_account( $mode );
	}

	/**
	 * Read the account from the WP option we cache it in.
	 *
	 * @return array empty when no data found, otherwise returns the cached data
	 */
	private function read_account_from_cache() {
		$account_cache = WC_Stripe_Database_Cache::get( self::ACCOUNT_CACHE_KEY );

		return false === $account_cache ? [] : $account_cache;
	}

	/**
	 * Caches account data for a period of time.
	 *
	 * @param string|null $mode Optional. The mode to get the account data for. 'live' or 'test'. Default will use the current mode.
	 */
	private function cache_account( $mode = null ) {
		// If a mode is provided, we'll set the API secret key to the appropriate key to retrieve the account data.
		if ( ! is_null( $mode ) ) {
			WC_Stripe_API::set_secret_key_for_mode( $mode );
		}

		// need call_user_func() as ( $this->stripe_api )::retrieve this syntax is not supported in php < 5.2
		$account = call_user_func( [ $this->stripe_api, 'retrieve' ], 'account' );

		// Restore the secret key to the original value.
		WC_Stripe_API::set_secret_key_for_mode();

		if ( is_wp_error( $account ) || isset( $account->error->message ) ) {
			return [];
		}

		// Convert the account data to an array.
		$account_cache = json_decode( wp_json_encode( $account ), true );

		// Create or update the account data cache.
		WC_Stripe_Database_Cache::set( self::ACCOUNT_CACHE_KEY, $account_cache, self::ACCOUNT_CACHE_EXPIRATION );

		return $account_cache;
	}

	/**
	 * Wipes the account data option.
	 */
	public function clear_cache() {
		WC_Stripe_Database_Cache::delete( self::ACCOUNT_CACHE_KEY );

		// Clear the webhook status cache.
		delete_transient( self::LIVE_WEBHOOK_STATUS_OPTION );
		delete_transient( self::TEST_WEBHOOK_STATUS_OPTION );
	}

	/**
	 * Indicates whether the account has any pending requirements that could cause the account to be restricted.
	 *
	 * @return bool True if account has pending restrictions, false otherwise.
	 */
	public function has_pending_requirements() {
		$requirements = $this->get_cached_account_data()['requirements'] ?? [];

		if ( empty( $requirements ) ) {
			return false;
		}

		$currently_due  = $requirements['currently_due'] ?? [];
		$past_due       = $requirements['past_due'] ?? [];
		$eventually_due = $requirements['eventually_due'] ?? [];

		return (
			! empty( $currently_due ) ||
			! empty( $past_due ) ||
			! empty( $eventually_due )
		);
	}

	/**
	 * Indicates whether the account has any overdue requirements that could cause the account to be restricted.
	 *
	 * @return bool True if account has overdue restrictions, false otherwise.
	 */
	public function has_overdue_requirements() {
		$requirements = $this->get_cached_account_data()['requirements'] ?? [];
		return ! empty( $requirements['past_due'] );
	}

	/**
	 * Returns the account's Stripe status (completed, restricted_soon, restricted).
	 *
	 * @return string The account's status.
	 */
	public function get_account_status() {
		$account = $this->get_cached_account_data();
		if ( empty( $account ) ) {
			return self::STATUS_NO_ACCOUNT;
		}

		$requirements = $account['requirements'] ?? [];
		if ( empty( $requirements ) ) {
			return self::STATUS_COMPLETE;
		}

		if ( isset( $requirements['disabled_reason'] ) && is_string( $requirements['disabled_reason'] ) ) {
			// If an account has been rejected, then disabled_reason will have a value like "rejected.<reason>"
			if ( strpos( $requirements['disabled_reason'], 'rejected' ) === 0 ) {
				return $requirements['disabled_reason'];
			}
			// If disabled_reason is not empty, then the account has been restricted.
			if ( ! empty( $requirements['disabled_reason'] ) ) {
				return self::STATUS_RESTRICTED;
			}
		}
		// Should be covered by the non-empty disabled_reason, but past due requirements also restrict the account.
		if ( isset( $requirements['past_due'] ) && ! empty( $requirements['past_due'] ) ) {
			return self::STATUS_RESTRICTED;
		}
		// Any other pending requirments indicate restricted soon.
		if ( $this->has_pending_requirements() ) {
			return self::STATUS_RESTRICTED_SOON;
		}

		return self::STATUS_COMPLETE;
	}

	/**
	 * Returns the Stripe's account supported currencies.
	 *
	 * @return string[] Supported store currencies.
	 */
	public function get_supported_store_currencies(): array {
		$account = $this->get_cached_account_data();
		if ( ! isset( $account['external_accounts']['data'] ) ) {
			return [ $account['default_currency'] ?? get_woocommerce_currency() ];
		}

		$currencies = array_filter( array_column( $account['external_accounts']['data'], 'currency' ) );
		return array_values( array_unique( $currencies ) );
	}

	/**
	 * Gets the account default currency.
	 *
	 * @return string Currency code in lowercase.
	 */
	public function get_account_default_currency(): string {
		$account = $this->get_cached_account_data();

		return isset( $account['default_currency'] ) ? strtolower( $account['default_currency'] ) : '';
	}

	/**
	 * Returns the Stripe account's card payment bank statement prefix.
	 *
	 * Merchants can set this in their Stripe settings at: https://dashboard.stripe.com/settings/public.
	 *
	 * @return string The Stripe Accounts card statement prefix.
	 */
	public function get_card_statement_prefix() {
		$account = $this->get_cached_account_data();
		return $account['settings']['card_payments']['statement_descriptor_prefix'] ?? '';
	}

	/**
	 * Gets the account country.
	 *
	 * @return string Country.
	 */
	public function get_account_country() {
		$account = $this->get_cached_account_data();
		return $account['country'] ?? 'US';
	}

	/**
	 * Configures webhooks for the account.
	 *
	 * @param string $mode The mode to configure webhooks for. Either 'live' or 'test'. Default is 'live'.
	 *
	 * @throws Exception If there was a problem setting up the webhooks.
	 * @return object The response from the API.
	 */
	public function configure_webhooks( $mode = 'live', $secret_key = '' ) {
		$request = [
			'enabled_events' => self::WEBHOOK_EVENTS,
			'url'            => WC_Stripe_Helper::get_webhook_url(),
			'api_version'    => WC_Stripe_API::STRIPE_API_VERSION,
		];

		// If a secret key is provided, use it to configure the webhooks.
		if ( $secret_key ) {
			$previous_secret = WC_Stripe_API::get_secret_key();
			WC_Stripe_API::set_secret_key( $secret_key );
		}

		$response = WC_Stripe_API::request( $request, 'webhook_endpoints', 'POST' );

		if ( isset( $response->error->message ) ) {
			// Translators: %s is the error message from the Stripe API.
			throw new Exception( sprintf( __( 'There was a problem setting up your webhooks. %s', 'woocommerce-gateway-stripe' ), $response->error->message ) );
		}

		if ( ! isset( $response->secret, $response->id ) ) {
			throw new Exception( __( 'There was a problem setting up your webhooks, please try again later.', 'woocommerce-gateway-stripe' ) );
		}

		// Delete any previously configured webhooks. Exclude the current webhook ID from the deletion.
		$this->delete_previously_configured_webhooks( $response->id );

		// Restore the previous secret key if we changed it.
		if ( $secret_key && isset( $previous_secret ) ) {
			WC_Stripe_API::set_secret_key( $previous_secret );
		}

		$settings = WC_Stripe_Helper::get_stripe_settings();

		$webhook_secret_setting = 'live' === $mode ? 'webhook_secret' : 'test_webhook_secret';
		$webhook_data_setting   = 'live' === $mode ? 'webhook_data' : 'test_webhook_data';

		// Save the Webhook secret and ID.
		$settings[ $webhook_secret_setting ] = wc_clean( $response->secret );
		$settings[ $webhook_data_setting ]   = [
			'id'     => wc_clean( $response->id ),
			'url'    => wc_clean( $response->url ),
			'secret' => WC_Stripe_API::get_secret_key(),
		];

		WC_Stripe_Helper::update_main_stripe_settings( $settings );

		// After reconfiguring webhooks, clear the webhook state.
		WC_Stripe_Webhook_State::clear_state();

		return $response;
	}

	/**
	 * Deletes any previously configured webhooks that are sent to the current site's webhook URL.
	 *
	 * @param string $exclude_webhook_id Webhook ID to exclude from deletion.
	 */
	public function delete_previously_configured_webhooks( $exclude_webhook_id = '' ) {
		$webhooks = $this->stripe_api::retrieve( 'webhook_endpoints' );

		if ( is_wp_error( $webhooks ) || ! isset( $webhooks->data ) || empty( $webhooks->data ) ) {
			return;
		}

		$webhook_url = WC_Stripe_Helper::get_webhook_url();

		WC_Stripe_Logger::log(
			$exclude_webhook_id ? "Deleting all webhooks sent to {$webhook_url} except for {$exclude_webhook_id}" : "Deleting all webhooks sent to {$webhook_url}"
		);

		foreach ( $webhooks->data as $webhook ) {
			if ( ! isset( $webhook->id, $webhook->url ) ) {
				continue;
			}

			// Skip the webhook we're excluding from deletion.
			if ( $exclude_webhook_id && $webhook->id === $exclude_webhook_id ) {
				continue;
			}

			// Delete the webhook if it matches the current site's webhook URL.
			if ( WC_Stripe_Helper::is_webhook_url( $webhook->url, $webhook_url ) ) {
				$this->stripe_api::request(
					[],
					"webhook_endpoints/{$webhook->id}",
					'DELETE'
				);
				WC_Stripe_Logger::log( "Deleted webhook {$webhook->id} because it was being sent to this site's webhook URL." );
			}
		}
	}

	/**
	 * Determine if the webhook is enabled by checking with Stripe.
	 *
	 * @return bool
	 */
	public function is_webhook_enabled() {
		$stripe_settings = WC_Stripe_Helper::get_stripe_settings();
		$is_testmode     = ( ! empty( $stripe_settings['testmode'] ) && 'yes' === $stripe_settings['testmode'] ) ? true : false;
		$key             = $is_testmode ? 'test_webhook_data' : 'webhook_data';

		if ( empty( $stripe_settings[ $key ]['id'] ) || empty( $stripe_settings[ $key ]['secret'] ) ) {
			return false;
		}

		// Check if we have a cached status.
		$cache_key     = $is_testmode ? self::TEST_WEBHOOK_STATUS_OPTION : self::LIVE_WEBHOOK_STATUS_OPTION;
		$cached_status = get_transient( $cache_key );
		if ( false !== $cached_status ) {
			return 'enabled' === $cached_status;
		}

		try {
			$webhook_id     = $stripe_settings[ $key ]['id'];
			$webhook_secret = $stripe_settings[ $key ]['secret'];
			WC_Stripe_API::set_secret_key( $webhook_secret );
			$webhook = $this->stripe_api::request( [], 'webhook_endpoints/' . $webhook_id, 'GET' );

			// Cache the status for 2 hours.
			$webhook_status = ! empty( $webhook->status ) && 'enabled' === $webhook->status ?
				'enabled' :
				'disabled';
			set_transient( $cache_key, $webhook_status, 2 * HOUR_IN_SECONDS );

			return 'enabled' === $webhook_status;
		} catch ( Exception $e ) {
			WC_Stripe_Logger::log( 'Unable to determine webhook status: .;' . $e->getMessage() );
			return false;
		}
	}

	/**
	 * Checks if the enabled events in an existing webhook differ from our desired events.
	 *
	 * @param object $existing_webhook The existing webhook object from Stripe.
	 * @return bool True if events differ, false if they match.
	 */
	private function do_webhook_events_differ( $existing_webhook ) {
		$desired_events = self::WEBHOOK_EVENTS;
		sort( $desired_events );
		$existing_events = $existing_webhook->enabled_events;
		sort( $existing_events );

		return $desired_events !== $existing_events;
	}

	/**
	 * Gets the existing webhook for the site's URL.
	 *
	 * @return object|false The webhook object if found, false otherwise.
	 */
	public function get_existing_webhook() {
		$webhooks = WC_Stripe_API::retrieve( 'webhook_endpoints' );

		if ( is_wp_error( $webhooks ) || ! isset( $webhooks->data ) ) {
			return false;
		}

		$webhook_url = WC_Stripe_Helper::get_webhook_url();

		foreach ( $webhooks->data as $webhook ) {
			if ( isset( $webhook->url ) && WC_Stripe_Helper::is_webhook_url( $webhook->url, $webhook_url ) ) {
				return $webhook;
			}
		}

		return false;
	}

	/**
	 * Reconfigures webhooks during plugin update.
	 * This ensures webhooks are updated with any new events that may have been added.
	 * Only reconfigures if there's an existing webhook and its events differ from desired events.
	 *
	 * @return void
	 */
	public function maybe_reconfigure_webhooks_on_update() {
		$settings = WC_Stripe_Helper::get_stripe_settings();
		$modes    = [ 'live', 'test' ];

		foreach ( $modes as $mode ) {
			$secret_key_setting = 'live' === $mode ? 'secret_key' : 'test_secret_key';
			$secret_key         = $settings[ $secret_key_setting ] ?? '';

			if ( empty( $secret_key ) ) {
				continue;
			}

			try {
				// Set the API key for this mode
				$previous_secret = WC_Stripe_API::get_secret_key();
				WC_Stripe_API::set_secret_key( $secret_key );

				// Check for existing webhook
				$existing_webhook = $this->get_existing_webhook();

				if ( ! $existing_webhook ) {
					continue;
				}

				// Check if events differ
				if ( ! $this->do_webhook_events_differ( $existing_webhook ) ) {
					continue;
				}

				// Events differ, reconfigure webhook
				WC_Stripe_Logger::log( "Webhook events need updating for {$mode} mode - reconfiguring." );
				$this->configure_webhooks( $mode, $secret_key );
				WC_Stripe_Logger::log( "Successfully reconfigured webhooks for {$mode} mode after plugin update." );

			} catch ( Exception $e ) {
				WC_Stripe_Logger::log( "Failed to check/reconfigure webhooks for {$mode} mode: " . $e->getMessage() );
			} finally {
				// Restore the previous secret key if we changed it
				if ( isset( $previous_secret ) ) {
					WC_Stripe_API::set_secret_key( $previous_secret );
				}
			}
		}
	}
}
