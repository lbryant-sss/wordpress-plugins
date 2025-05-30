<?php
/**
 * Helper class to provide some common functionalities
 *
 * @package Kliken Marketing for Google
 */

namespace Kliken\WcPlugin;

defined( 'ABSPATH' ) || exit;

/**
 * Helper class
 */
class Helper {
	/**
	 * Name of the option that WooCommerce will save the plugin settings into.
	 *
	 * @var string
	 */
	private static $_option_key = null;

	/**
	 * A wrapper around WC_Logger log method.
	 *
	 * @param string  $level Log level.
	 * @param mixed   $message Message to log.
	 * @param boolean $force Force logging without the need of WP_DEBUG mode.
	 */
	public static function wc_log( $level, $message, $force = false ) {
		if ( ( WP_DEBUG || $force )
			&& function_exists( 'wc_get_logger' )
			&& $message
			&& in_array( $level, [ 'debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency' ], true ) ) {

			if ( ! is_string( $message ) ) {
				$message = wp_json_encode( $message );
			}

			wc_get_logger()->log( $level, $message, [ 'source' => 'kliken-marketing-for-google' ] );
		}
	}

	/**
	 * Get plugin option key, because of WooCommerce Integration Settings API.
	 *
	 * @return string
	 */
	public static function get_option_key() {
		if ( null === self::$_option_key ) {
			if ( function_exists( 'wc' ) && class_exists( '\WC_Integration' ) ) {
				self::$_option_key = ( new WC_Integration( false ) )->get_option_key();
			} else {
				// In the weird case when WooCommerce is not available.
				self::$_option_key = 'woocommerce_kk_wcintegration_settings';
			}
		}

		return self::$_option_key;
	}

	/**
	 * Get plugin options, being an integration with WooCommerce Settings API.
	 *
	 * @return array
	 */
	public static function get_plugin_options() {
		return get_option( self::get_option_key(), [] );
	}

	/**
	 * Check if provided id can be considered a valid account id.
	 * Should be an integer.
	 *
	 * @param mixed $id Account Id.
	 * @return boolean
	 */
	public static function is_valid_account_id( $id ) {
		return ! empty( $id ) && ctype_digit( strval( $id ) );
	}

	/**
	 * Check if the provided token can be considered a valid application token.
	 * Should be not empty, for now.
	 *
	 * @param string $token Application token.
	 * @return boolean
	 */
	public static function is_valid_app_token( $token ) {
		$token = sanitize_text_field( $token );
		return ! empty( $token );
	}

	/**
	 * Check if current page is a page of the plugin, matching the provided page slug.
	 *
	 * @param string $page_slug Page slug.
	 * @return boolean
	 */
	public static function is_plugin_page( $page_slug = null ) {
		global $pagenow;

		if ( 'admin.php' !== $pagenow ) {
			return false;
		}

		if ( null !== $page_slug ) {
			return ( isset( $_GET['page'] ) && $page_slug === $_GET['page'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- CSRF ok, input var ok.
		} else {
			return (
				isset( $_GET['page'] ) && 'wc-settings' === $_GET['page'] // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- WPCS: CSRF ok, input var ok.
				&& isset( $_GET['tab'] ) && 'integration' === $_GET['tab'] // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- WPCS: CSRF ok, input var ok.
				&& isset( $_GET['section'] ) && KK_WC_INTEGRATION_PAGE_ID === $_GET['section'] // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- WPCS: CSRF ok, input var ok.
			);
		}
	}

	/**
	 * Get plugin page URL.
	 */
	public static function get_plugin_page() {
		return 'admin.php?page=wc-settings&tab=integration&section=' . KK_WC_INTEGRATION_PAGE_ID;
	}

	/**
	 * Add Kliken tracking script to page.
	 */
	public static function add_tracking_script() {
		global $wp;

		$saved_settings = self::get_plugin_options();

		if ( ! self::is_valid_account_id( $saved_settings['account_id'] ) ) {
			return;
		}

		// Check if is product page.
		if ( is_product() ) {
			$product = self::build_product_data();

			if ( null !== $product ) {
				include_once KK_WC_PLUGIN_DIR . 'pages/productscript.php';
			}
		}

		// Check if is order received page.
		if ( is_order_received_page() ) {
			$order_id = isset( $wp->query_vars['order-received'] ) ? $wp->query_vars['order-received'] : 0;
			$trans    = self::build_transaction_data( $order_id );

			if ( null !== $trans ) {
				include_once KK_WC_PLUGIN_DIR . 'pages/transactionscript.php';
			}
		}

		$account_id = $saved_settings['account_id'];
		include_once KK_WC_PLUGIN_DIR . 'pages/trackingscript.php';
	}

	/**
	 * Add Facebook Verification Token to page meta.
	 */
	public static function add_facebook_verification_token() {
		$saved_settings = self::get_plugin_options();

		// Needed to add check because this may not exist during upgrading of plugin so an error would appear about being undefined.
		if ( array_key_exists( 'facebook_token', $saved_settings ) ) {
			// Sanitize the saved string again just in case.
			$token = sanitize_text_field( $saved_settings['facebook_token'] );

			if ( $token ) {
				printf( '<!-- Kliken Facebook Site Verification Token Tag -->' );
				printf( '<meta name="facebook-domain-verification" content="%s" />', esc_attr( $token ) );
			}
		}
	}

	/**
	 * Add Google Verification Token to page meta.
	 */
	public static function add_google_verification_token() {
		$saved_settings = self::get_plugin_options();

		// Sanitize the saved string again just in case.
		$token = sanitize_text_field( $saved_settings['google_token'] );

		if ( $token ) {
			printf( '<!-- Kliken Google Site Verification Token Tag -->' );
			printf( '<meta name="google-site-verification" content="%s" />', esc_attr( $token ) );
		}
	}

	/**
	 * Build transaction/order data preparing to be recorded by our tracking script
	 *
	 * @param int $order_id WooCommerce Order Id.
	 * @return array|null
	 */
	public static function build_transaction_data( $order_id ) {
		// Get the order detail.
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return null;
		}

		// We don't care about these statuses.
		$status = $order->get_status();
		if ( 'cancelled' === $status || 'refunded' === $status || 'failed' === $status ) {
			return null;
		}

		$transaction = [
			'order_id'  => $order_id,
			'currency'  => $order->get_currency(),
			'affiliate' => null,
			'sub_total' => $order->get_subtotal(),
			'tax'       => $order->get_total_tax(),
			'city'      => $order->get_billing_city(),
			'state'     => $order->get_billing_state(),
			'country'   => $order->get_billing_country(),
			'total'     => $order->get_total(),
			'items'     => [],
		];

		$order_items = $order->get_items();

		// Cache category info, because in the order, there might be multiple items under same category.
		$category_cache = [];

		foreach ( $order_items as $index => $item ) {
			$product = $item->get_product();

			if ( ! $product instanceof \WC_Product ) {
				continue;
			}

			$product_categories = $product->get_category_ids();
			$category_name      = '';

			foreach ( $product_categories as $index => $id ) {
				if ( array_key_exists( $id, $category_cache ) ) {
					$category_name = $category_cache[ $id ];
				} else {
					$term = get_term_by( 'id', $id, 'product_cat' );
					if ( $term ) {
						$category_name         = $term->name;
						$category_cache[ $id ] = $category_name;
					}
				}
			}

			array_push(
				$transaction['items'],
				[
					'id'       => $product->get_id(),
					'name'     => $product->get_name(),
					'category' => $category_name,
					'price'    => $product->get_price(),
					'quantity' => $item->get_quantity(),
				]
			);
		}

		return $transaction;
	}

	/**
	 * Build product data preparing to be recorded by our tracking script
	 *
	 * @return array|null
	 */
	public static function build_product_data() {
		$product = wc_get_product();

		if ( ! $product ) {
			return null;
		}

		$product_info = [
			'id'       => $product->get_id(),
			'name'     => $product->get_name(),
			'price'    => $product->get_price(),
			'category' => $product->get_category_ids(),
		];

		return $product_info;
	}

	/**
	 * Build the WooCommerce authorization URL
	 * Doc: https://woocommerce.github.io/woocommerce-rest-api-docs/#rest-api-keys
	 *
	 * @param int    $account_id Account Id.
	 * @param string $application_token Application Token.
	 * @return string|null
	 */
	public static function build_authorization_url( $account_id, $application_token ) {
		if ( empty( $account_id ) || empty( $application_token ) ) {
			return null;
		}

		$authorization_url = get_site_url() . '/wc-auth/v1/authorize'
			. '?app_name=' . rawurlencode( __( 'AI Powered Marketing' ) )
			. '&scope=read_write'
			. '&user_id=' . base64_encode( $account_id . ':' . $application_token ) // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			. '&return_url=' . rawurlencode( 'bit.ly/2OweS8h' ) // This links back to woo.kliken.com. We just need to do this to shorten the link because some WordPress hostings seem to dislike long links.
			. '&callback_url=' . rawurlencode( KK_WC_AUTH_CALLBACK_URL );

		return $authorization_url;
	}

	/**
	 * Build sign up link.
	 */
	public static function build_signup_link() {
		$current_user = wp_get_current_user();

		return sprintf(
			KK_WC_WOOKLIKEN_BASE_URL . 'auth/woo/?u=%s&n=%s&e=%s&t=%s&return=%s',
			rawurlencode( get_site_url() ),
			rawurlencode( $current_user->display_name ),
			rawurlencode( $current_user->user_email ),
			wp_create_nonce( KK_WC_ACTION_SAVE_ACCOUNT ),
			get_admin_url() . 'admin.php?action=' . KK_WC_ACTION_SAVE_ACCOUNT
		);
	}

	/**
	 * Save account information.
	 *
	 * @param int    $account_id Account Id.
	 * @param string $application_token Application Token.
	 * @return string|null WooCommerce authorization URL to redirect to after saving account info.
	 */
	public static function save_account_info( $account_id, $application_token ) {
		if ( self::is_valid_account_id( $account_id )
			&& self::is_valid_app_token( $application_token )
		) {
			$saved_settings = self::get_plugin_options();

			$google_token = '';
			if ( ! empty( $saved_settings['google_token'] ) ) {
				$google_token = $saved_settings['google_token'];
			}

			$facebook_token = '';
			if ( ! empty( $saved_settings['facebook_token'] ) ) {
				$facebook_token = $saved_settings['facebook_token'];
			}

			update_option(
				self::get_option_key(),
				[
					'account_id'     => intval( $account_id ),
					'app_token'      => sanitize_text_field( $application_token ),
					'google_token'   => $google_token,
					'facebook_token' => $facebook_token,
				]
			);

			return self::build_authorization_url( $account_id, $application_token );
		}

		return null;
	}

	/**
	 * Redirect to WooCommerce Authorization page if needed.
	 */
	public static function check_redirect_for_wc_auth() {
		// Bail if activating from network, or bulk.
		if ( is_network_admin() ) {
			return;
		}

		// If WooCommerce is not available/active, don't bother.
		if ( ! function_exists( 'wc' ) ) {
			return;
		}

		$authorization_url = get_site_transient( KK_WC_TRANSIENT_AUTH_REDIRECT );

		if ( ! empty( $authorization_url ) ) {
			delete_site_transient( KK_WC_TRANSIENT_AUTH_REDIRECT );

			if ( wp_safe_redirect( $authorization_url ) ) {
				exit;
			}
		}
	}

	/**
	 * Get the formatted, clean, safe onboarding message.
	 */
	public static function get_onboarding_message() {
		return sprintf(
			wp_kses(
				/* translators: %s: Plugin name. %s: A hyperlink. Do not translate. */
				__( '<strong>%1$s plugin is almost ready.</strong> <a href="%2$s">Click here</a> to get started.', 'kliken-marketing-for-google' ),
				[
					'strong' => [],
					'a'      => [ 'href' => [] ],
				]
			),
			__( 'AI Powered Marketing' ),
			esc_url( self::get_plugin_page() )
		);
	}

	/**
	 * Get various data of the other plugin, for interoperability checks.
	 *
	 * @param bool $active_state_only If only need to get active state, as if the plugin is active.
	 * @return array
	 */
	public static function get_other_plugin_state( $active_state_only = false ) {
		$state = [];

		if ( defined( 'KK_FB_WC_SETTINGS_OPTION_KEY' ) ) {
			$state['active']     = true;
			$state['option_key'] = KK_FB_WC_SETTINGS_OPTION_KEY;
		} else {
			$state['active']     = self::is_plugin_active( 'kliken-ads-pixel-for-meta/kliken-ads-pixel-for-meta.php' );
			$state['option_key'] = 'kk_fb_wc_settings';
		}

		if ( true === $active_state_only ) {
			return $state;
		}

		// Check if the new plugin has been configured.
		if ( method_exists( '\Kliken\FbWcPlugin\Helper', 'is_plugin_configured' ) ) {
			$state['settings']   = \Kliken\FbWcPlugin\Helper::get_plugin_settings();
			$state['configured'] = \Kliken\FbWcPlugin\Helper::is_plugin_configured( $state['settings'] );
		} else {
			$state['settings']   = get_option( $state['option_key'], [] );
			$state['configured'] = ! empty( $state['settings'] )
				&& self::is_valid_account_id( $state['settings']['account_id'] )
				&& self::is_valid_app_token( $state['settings']['app_token'] );
		}

		return $state;
	}

	/**
	 * Save settings to the other plugin of ours.
	 *
	 * @param array $settings Setings to be saved.
	 * @param bool  $force Force updating.
	 * @return void
	 */
	public static function save_other_plugin_settings( array $settings, bool $force = false ) {
		$state = self::get_other_plugin_state();

		if ( $state['active'] && false === $force ) {
			// The new plugin should handle things at this state.
			return;
		}

		if ( ! $state['active'] && ! $state['configured'] && $state['settings'] ) {
			// Plugin is probably not even installed.
			return;
		}

		update_option( $state['option_key'], array_merge( $state['settings'], $settings ) );
	}

	/**
	 * Try to check if a plugin is active. Will try to use built-in WordPress function first.
	 * Otherwise, it's using the copied code of that built-in WordPress function.
	 * We do this because sometimes, we need this check before the WordPress function is available to us.
	 *
	 * @param mixed $plugin Plugin path. Should be the folder name, and the main file name.
	 * @return bool
	 */
	private static function is_plugin_active( $plugin ) {
		// Use WordPress function if it exists at this point.
		if ( function_exists( 'is_plugin_active' ) ) {
			return is_plugin_active( $plugin );
		}

		return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) || self::is_plugin_active_for_network( $plugin );
	}

	/**
	 * A copy of the built-in WordPress method of the same name.
	 * We do this because sometimes, we need this check before the WordPress function is available to us.
	 *
	 * @param mixed $plugin Plugin path. Should be the folder name, and the main file name.
	 * @return bool
	 */
	private static function is_plugin_active_for_network( $plugin ) {
		if ( ! is_multisite() ) {
			return false;
		}

		$plugins = get_site_option( 'active_sitewide_plugins' );
		if ( isset( $plugins[ $plugin ] ) ) {
			return true;
		}

		return false;
	}
}
