<?php
/**
 * Google Client admin class.
 *
 * Handles retrieving whether a particular notice has been dismissed or not,
 * as well as marking a notice as dismissed.
 *
 * @since 7.0.0
 *
 * @package ExactMetrics
 * @subpackage GA Client
 * @author  Chris Christoff
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ExactMetrics_API_Auth {

	/**
	 * Primary class constructor.
	 *
	 * @access public
	 * @since 7.0.0
	 */
	public function __construct() {

		// Authentication Actions
		add_action( 'wp_ajax_exactmetrics_maybe_authenticate', array( $this, 'maybe_authenticate' ) );
		add_action( 'wp_ajax_nopriv_onboarding_exactmetrics_maybe_authenticate', array( $this, 'onboarding_maybe_authenticate' ) );
		add_action( 'wp_ajax_exactmetrics_maybe_reauthenticate', array( $this, 'maybe_reauthenticate' ) );
		add_action( 'wp_ajax_exactmetrics_maybe_verify', array( $this, 'maybe_verify' ) );
		add_action( 'wp_ajax_exactmetrics_maybe_delete', array( $this, 'maybe_delete' ) );

		add_action( 'admin_init', array( $this, 'authenticate_listener' ) );
		add_action( 'admin_init', array( $this, 'reauthenticate_listener' ) );

		add_action( 'wp_ajax_nopriv_exactmetrics_is_installed', array( $this, 'is_installed' ) );
		add_action( 'wp_ajax_nopriv_exactmetrics_rauthenticate', array( $this, 'rauthenticate' ) );

		add_filter( 'exactmetrics_maybe_authenticate_siteurl', array( $this, 'before_redirect' ) );

		add_action( 'wp_ajax_nopriv_exactmetrics_push_mp_token', array( $this, 'handle_relay_mp_token_push' ) );
	}

	public function get_tt() {
		$tt = is_network_admin() ? get_site_option( 'exactmetrics_network_tt', '' ) : get_option( 'exactmetrics_site_tt', '' );
		if ( empty( $tt ) ) {
			// if TT is empty, generate a new one, save it and then return it
			$tt = $this->generate_tt();
			$this->is_network_admin() ? update_site_option( 'exactmetrics_network_tt', $tt ) : update_option( 'exactmetrics_site_tt', $tt );
		}

		return $tt;
	}

	public function rotate_tt() {
		$tt = $this->generate_tt();
		is_network_admin() ? update_site_option( 'exactmetrics_network_tt', $tt ) : update_option( 'exactmetrics_site_tt', $tt );
	}

	public function generate_tt() {
		return defined( 'AUTH_SALT' ) ? hash( 'sha512', wp_generate_password( 128, true, true ) . AUTH_SALT . uniqid( "", true ) ) : hash( 'sha512', wp_generate_password( 128, true, true ) . uniqid( "", true ) );
	}

	public function validate_tt( $passed_tt = '' ) {
		$tt = $this->get_tt();

		return hash_equals( $tt, $passed_tt );
	}

	public function is_installed() {
		wp_send_json_success(
			array(
				'version' => EXACTMETRICS_VERSION,
				'pro'     => exactmetrics_is_pro_version(),
			)
		);
	}

	public function maybe_authenticate() {
		// Check nonce
		check_ajax_referer( 'mi-admin-nonce', 'nonce' );
		// current user can authenticate
		if ( ! current_user_can( 'exactmetrics_save_settings' ) ) {
			// Translators: link tag starts with url, link tag ends.
			$message = sprintf(
				__( 'You don\'t have the correct WordPress user permissions to authenticate into ExactMetrics. Please check with your site administrator that your role is included in the ExactMetrics permissions settings. %1$sClick here for more information%2$s.', 'google-analytics-dashboard-for-wp' ),
				'<a target="_blank" href="' . exactmetrics_get_url( 'notice', 'cannot-save-settings', 'https://www.exactmetrics.com/docs/how-to-allow-user-roles-to-access-the-exactmetrics-reports-and-settings/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		if ( ! empty( $_REQUEST['isnetwork'] ) && $_REQUEST['isnetwork'] ) { // phpcs:ignore
			define( 'WP_NETWORK_ADMIN', true );
		}

		// Only for Pro users, require a license key to be entered first so we can link to things.
		if ( exactmetrics_is_pro_version() ) {
			$valid = is_network_admin() ? ExactMetrics()->license->is_network_licensed() : ExactMetrics()->license->is_site_licensed();
			if ( ! $valid ) {
				wp_send_json_error( array( 'message' => __( "Cannot authenticate. Please enter a valid, active license key for ExactMetrics Pro into the settings page.", 'google-analytics-dashboard-for-wp' ) ) );
			}
		}

		// we do not have a current auth
		if ( ! $this->is_network_admin() && ExactMetrics()->auth->is_authed() ) {
			// Translators: Support link tag starts with url, Support link tag ends.
			$message = sprintf(
				__( 'Oops! There has been an error authenticating. Please try again in a few minutes. If the problem persists, please %1$scontact our support%2$s team.', 'google-analytics-dashboard-for-wp' ),
				'<a target="_blank" href="' . exactmetrics_get_url( 'notice', 'error-authenticating', 'https://www.exactmetrics.com/my-account/support/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		} else if ( $this->is_network_admin() && ExactMetrics()->auth->is_network_authed() ) {
			// Translators: Support link tag starts with url, Support link tag ends.
			$message = sprintf(
				__( 'Oops! There has been an error authenticating. Please try again in a few minutes. If the problem persists, please %1$scontact our support%2$s team.', 'google-analytics-dashboard-for-wp' ),
				'<a target="_blank" href="' . exactmetrics_get_url( 'notice', 'error-authenticating', 'https://www.exactmetrics.com/my-account/support/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		$sitei = $this->get_sitei();

		$site_type = exactmetrics_get_option( 'site_type' );

		$auth_request_args = array(
			'tt'        => $this->get_tt(),
			'sitei'     => $sitei,
			'miversion' => EXACTMETRICS_VERSION,
			'ajaxurl'   => admin_url( 'admin-ajax.php' ),
			'network'   => is_network_admin() ? 'network' : 'site',
			'siteurl'   => is_network_admin() ? network_admin_url() : home_url(),
			'return'    => is_network_admin() ? network_admin_url( 'admin.php?page=exactmetrics_network' ) : admin_url( 'admin.php?page=exactmetrics_settings' ),
			'testurl'   => 'https://' . exactmetrics_get_api_url() . 'test/',
			'site_type' => $site_type ?: 'business',
		);

		$auth_request_args = apply_filters('exactmetrics_auth_request_body', $auth_request_args);

		$siteurl = add_query_arg($auth_request_args, $this->get_route( 'https://' . exactmetrics_get_api_url() . 'auth/new/{type}' ) );

		if ( exactmetrics_is_pro_version() ) {
			$key     = is_network_admin() ? ExactMetrics()->license->get_network_license_key() : ExactMetrics()->license->get_site_license_key();
			$siteurl = add_query_arg( 'license', $key, $siteurl );
		}

		$siteurl = apply_filters( 'exactmetrics_maybe_authenticate_siteurl', $siteurl );
		wp_send_json_success( array( 'redirect' => $siteurl ) );
	}

	private function send_missing_args_error( $arg ) {
		wp_send_json_error(
			array(
				'error'   => 'authenticate_missing_arg',
				'message' => 'Authenticate missing parameter: ' . $arg,
				'version' => EXACTMETRICS_VERSION,
				'pro'     => exactmetrics_is_pro_version(),
			)
		);
	}

	public function rauthenticate() {
		// Check for missing params
		$reqd_args = array( 'key', 'token', 'miview', 'a', 'w', 'p', 'tt', 'network' );

		if ( empty( $_REQUEST['v4'] ) ) {
			$this->send_missing_args_error( 'v4' );
		}

		foreach ( $reqd_args as $arg ) {
			if ( empty( $_REQUEST[ $arg ] ) ) {
				$this->send_missing_args_error( $arg );
			}
		}

		if ( ! empty( $_REQUEST['network'] ) && 'network' === $_REQUEST['network'] ) {
			define( 'WP_NETWORK_ADMIN', true );
		}

		if ( ! $this->validate_tt( $_REQUEST['tt'] ) ) { // phpcs:ignore
			wp_send_json_error(
				array(
					'error'   => 'authenticate_invalid_tt',
					'message' => 'Invalid TT sent',
					'version' => EXACTMETRICS_VERSION,
					'pro'     => exactmetrics_is_pro_version(),
				)
			);
		}

		// If the tt is validated, send a success response to trigger the regular auth process.
		wp_send_json_success();
	}

	public function authenticate_listener() {
		// Make sure it's for us
		if ( empty( $_REQUEST['mi-oauth-action'] ) || $_REQUEST['mi-oauth-action'] !== 'auth' ) {
			return;
		}

		// User can authenticate
		if ( ! current_user_can( 'exactmetrics_save_settings' ) ) {
			return;
		}

		// Invalid request
		if ( empty( $_REQUEST['tt'] ) || ! $this->validate_tt( $_REQUEST['tt'] ) ) {
			return;
		}

		// Make sure has required params
		if (
			empty( $_REQUEST['key'] ) ||
			empty( $_REQUEST['token'] ) ||
			empty( $_REQUEST['miview'] ) ||
			empty( $_REQUEST['a'] ) ||
			empty( $_REQUEST['w'] ) ||
			empty( $_REQUEST['p'] ) ||
			empty( $_REQUEST['v4'] )
		) {
			return;
		}

        $code_value = exactmetrics_is_valid_v4_id( $_REQUEST['v4'] ); // phpcs:ignore

		if ( empty( $code_value ) ) {
			return;
		}

		$profile = array(
			'key'           => sanitize_text_field( $_REQUEST['key'] ),
			'token'         => sanitize_text_field( $_REQUEST['token'] ),
			'viewname'      => sanitize_text_field( $_REQUEST['miview'] ),
			'a'             => sanitize_text_field( $_REQUEST['a'] ), // AccountID
			'w'             => sanitize_text_field( $_REQUEST['w'] ), // PropertyID
			'p'             => sanitize_text_field( $_REQUEST['p'] ), // View ID
			'site_hash'     => !empty($_REQUEST['site_hash']) ? sanitize_text_field( $_REQUEST['site_hash'] ) : '', // Site Hash
			'siteurl'       => home_url(),
			'neturl'        => network_admin_url(),
		);

		if ( ! empty( $_REQUEST['mp'] ) ) {
			$profile['measurement_protocol_secret'] = sanitize_text_field( $_REQUEST['mp'] );
		}

		$profile['v4'] = $code_value;

		$worked = $this->verify_auth( $profile );
		if ( ! $worked || is_wp_error( $worked ) ) {
			return;
		}

		// Save Profile
		$this->is_network_admin() ? ExactMetrics()->auth->set_network_analytics_profile( $profile ) : ExactMetrics()->auth->set_analytics_profile( $profile );

		// Clear cache
		$where = $this->is_network_admin() ? 'network' : 'site';
		ExactMetrics()->reporting->delete_aggregate_data( $where );
		
		if ( class_exists( 'ExactMetrics_Google_Ads' ) ) {
			// Clear any Google Ads stored data
			ExactMetrics_Google_Ads::clear_data();
		}

		// Check site and property timezone.
		$this->check_property_timezone();

		$url = $this->is_network_admin() ? network_admin_url( 'admin.php?page=exactmetrics_network' ) : admin_url( 'admin.php?page=exactmetrics_settings' );
		$url = add_query_arg( array(
			'mi_action' => 'auth',
			'success'   => 'true',
		), $url );

		$url = apply_filters( 'exactmetrics_auth_success_redirect_url', $url );
		wp_safe_redirect( $url );
		exit;
	}

	public function maybe_reauthenticate() {

		// Check nonce
		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		$url = admin_url( 'admin.php?page=exactmetrics-onboarding' );

		// current user can authenticate
		if ( ! current_user_can( 'exactmetrics_save_settings' ) ) {
			// Translators: Link tag starts with url and link tag ends.
			$message = sprintf(
				__( 'You don\'t have the correct WordPress user permissions to re-authenticate into ExactMetrics. Please check with your site administrator that your role is included in the ExactMetrics permissions settings. %1$sClick here for more information%2$s.', 'google-analytics-dashboard-for-wp' ),
				'<a target="_blank" href="' . exactmetrics_get_url( 'notice', 'cannot-save-settings', 'https://www.exactmetrics.com/docs/how-to-allow-user-roles-to-access-the-exactmetrics-reports-and-settings/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		if ( ! empty( $_REQUEST['isnetwork'] ) && filter_var($_REQUEST['isnetwork'], FILTER_VALIDATE_BOOLEAN) ) {
			define( 'WP_NETWORK_ADMIN', true );
		}

		// Only for Pro users, require a license key to be entered first so we can link to things.
		if ( exactmetrics_is_pro_version() ) {
			$valid = is_network_admin() ? ExactMetrics()->license->is_network_licensed() : ExactMetrics()->license->is_site_licensed();
			if ( exactmetrics_is_pro_version() && ! $valid ) {
				wp_send_json_error( array( 'message' => __( "Your license key for ExactMetrics is invalid. The key no longer exists or the user associated with the key has been deleted. Please use a different key.", 'google-analytics-dashboard-for-wp' ) ) );
			}
		}

		// we do have a current auth
		if ( ! $this->is_network_admin() && ! ExactMetrics()->auth->is_authed() ) {
			// Translators: Wizard Link tag starts with url, Wizard link tag ends, Support link tag starts, Support link tag ends.
			$message = sprintf(
				__( 'Oops! There was a problem while re-authenticating. Please try to complete the ExactMetrics %1$ssetup wizard%2$s again. If the problem persists, please %3$scontact our support%4$s team.', 'google-analytics-dashboard-for-wp' ),
				'<a href="' . esc_url( $url ) . '">',
				'</a>',
				'<a target="_blank" href="' . exactmetrics_get_url( 'notice', 'cannot-re-authenticate', 'https://www.exactmetrics.com/my-account/support/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		} else if ( $this->is_network_admin() && ! ExactMetrics()->auth->is_network_authed() ) {
			// Translators: Wizard Link tag starts with url, Wizard link tag ends, Support link tag starts, Support link tag ends.
			$message = sprintf(
				__( 'Oops! There was a problem while re-authenticating. Please try to complete the ExactMetrics %1$ssetup wizard%2$s again. If the problem persists, please %3$scontact our support%4$s team.', 'google-analytics-dashboard-for-wp' ),
				'<a href="' . esc_url( $url ) . '">',
				'</a>',
				'<a target="_blank" href="' . exactmetrics_get_url( 'notice', 'cannot-re-authenticate', 'https://www.exactmetrics.com/my-account/support/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		$site_type = exactmetrics_get_option( 'site_type' );

		$auth_request_args = array(
			'tt'        => $this->get_tt(),
			'sitei'     => $this->get_sitei(),
			'miversion' => EXACTMETRICS_VERSION,
			'ajaxurl'   => admin_url( 'admin-ajax.php' ),
			'network'   => is_network_admin() ? 'network' : 'site',
			'siteurl'   => is_network_admin() ? network_admin_url() : home_url(),
			'key'       => is_network_admin() ? ExactMetrics()->auth->get_network_key() : ExactMetrics()->auth->get_key(),
			'token'     => is_network_admin() ? ExactMetrics()->auth->get_network_token() : ExactMetrics()->auth->get_token(),
			'return'    => is_network_admin() ? network_admin_url( 'admin.php?page=exactmetrics_network' ) : admin_url( 'admin.php?page=exactmetrics_settings' ),
			'testurl'   => 'https://' . exactmetrics_get_api_url() . 'test/',
			'site_type' => $site_type ?: 'business',
		);

		$auth_request_args = apply_filters('exactmetrics_auth_request_body', $auth_request_args);

		$siteurl = add_query_arg( $auth_request_args, $this->get_route( 'https://' . exactmetrics_get_api_url() . 'auth/reauth/{type}' ) );

		if ( exactmetrics_is_pro_version() ) {
			$key     = is_network_admin() ? ExactMetrics()->license->get_network_license_key() : ExactMetrics()->license->get_site_license_key();
			$siteurl = add_query_arg( 'license', $key, $siteurl );
		}

		$siteurl = apply_filters( 'exactmetrics_maybe_authenticate_siteurl', $siteurl );

		wp_send_json_success( array( 'redirect' => $siteurl ) );
	}


	public function reauthenticate_listener() {
		// Make sure it's for us
		if ( empty( $_REQUEST['mi-oauth-action'] ) || $_REQUEST['mi-oauth-action'] !== 'reauth' ) {
			return;
		}

		// User can authenticate
		if ( ! current_user_can( 'exactmetrics_save_settings' ) ) {
			return;
		}

		// Invalid request
		if ( empty( $_REQUEST['tt'] ) || ! $this->validate_tt( $_REQUEST['tt'] ) ) { // phpcs:ignore
			return;
		}

		// Make sure has required params
		if (
			empty( $_REQUEST['v4'] ) ||
			empty( $_REQUEST['miview'] ) ||
			empty( $_REQUEST['a'] ) ||
			empty( $_REQUEST['w'] ) ||
			empty( $_REQUEST['p'] )
		) {
			return;
		}

        $code_value = exactmetrics_is_valid_v4_id( $_REQUEST['v4'] ); // phpcs:ignore

		if ( empty( $code_value ) ) {
			return;
		}

		// we do have a current auth
		$existing = $this->is_network_admin() ? ExactMetrics()->auth->get_network_analytics_profile() : ExactMetrics()->auth->get_analytics_profile();
		if ( empty( $existing['key'] ) || empty( $existing['token'] ) ) {
			return;
		}

		$profile = array(
			'key'           => $existing['key'],
			'token'         => $existing['token'],
			'viewname'      => sanitize_text_field( $_REQUEST['miview'] ),
			'a'             => sanitize_text_field( $_REQUEST['a'] ),
			'w'             => sanitize_text_field( $_REQUEST['w'] ),
			'p'             => sanitize_text_field( $_REQUEST['p'] ),
			'site_hash'     => !empty($_REQUEST['site_hash']) ? sanitize_text_field( $_REQUEST['site_hash'] ) : '', // Site Hash
			'v4'            => $existing['v4'],
			'siteurl'       => home_url(),
			'neturl'        => network_admin_url(),
		);

		if ( ! empty( $_REQUEST['mp'] ) ) {
			$profile['measurement_protocol_secret'] = sanitize_text_field( $_REQUEST['mp'] );
		}

		$profile['v4'] = $code_value;

		// Save Profile
		$this->is_network_admin() ? ExactMetrics()->auth->set_network_analytics_profile( $profile ) : ExactMetrics()->auth->set_analytics_profile( $profile );

		// Clear cache
		$where = $this->is_network_admin() ? 'network' : 'site';
		ExactMetrics()->reporting->delete_aggregate_data( $where );
		
		if ( class_exists( 'ExactMetrics_Google_Ads' ) ) {
			// Clear any Google Ads stored data
			ExactMetrics_Google_Ads::clear_data();
		}

		// Check site and property timezone.
		$this->check_property_timezone();

		$url = $this->is_network_admin() ? network_admin_url( 'admin.php?page=exactmetrics_network' ) : admin_url( 'admin.php?page=exactmetrics_settings' );
		$url = add_query_arg( array(
			'mi_action' => 'reauth',
			'success'   => 'true',
		), $url );

		$url = apply_filters( 'exactmetrics_reauth_success_redirect_url', $url );

		wp_safe_redirect( $url );
		exit;
	}

	public function maybe_verify() {

		// Check nonce
		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		// current user can verify
		if ( ! current_user_can( 'exactmetrics_save_settings' ) ) {
			// Translators: Link tag starts with url and link tag ends.
			$message = sprintf(
				__( 'You don\'t have the correct user permissions to verify the ExactMetrics license you are trying to use. Please check with your site administrator that your role is included in the ExactMetrics permissions settings. %1$sClick here for more information%2$s.', 'google-analytics-dashboard-for-wp' ),
				'<a target="_blank" rel="noopener" href="' . exactmetrics_get_url( 'notice', 'cannot-save-settings', 'https://www.exactmetrics.com/docs/how-to-allow-user-roles-to-access-the-exactmetrics-reports-and-settings/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		if ( ! empty( $_REQUEST['isnetwork'] ) && filter_var($_REQUEST['isnetwork'], FILTER_VALIDATE_BOOL) ) {
			define( 'WP_NETWORK_ADMIN', true );
		}

		// we have an auth to verify
		if ( $this->is_network_admin() && ! ExactMetrics()->auth->is_network_authed() ) {
			// Translators: Support Link tag starts with url and Support link tag ends.
			$message = sprintf(
				__( 'Please enter a valid license within the ExactMetrics settings panel. You can check your license by logging into your ExactMetrics account by %1$sclicking here%2$s.', 'google-analytics-dashboard-for-wp' ),
				'<a target="_blank" rel="noopener" href="' . exactmetrics_get_url( 'notice', 'cannot-verify-license', 'https://www.exactmetrics.com/my-account/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		} else if ( ! $this->is_network_admin() && ! ExactMetrics()->auth->is_authed() ) {
			// Translators: Support Link tag starts with url and Support link tag ends.
			$message = sprintf(
				__( 'Please enter a valid license within the ExactMetrics settings panel. You can check your license by logging into your ExactMetrics account by %1$sclicking here%2$s.', 'google-analytics-dashboard-for-wp' ),
				'<a target="_blank" rel="noopener" href="' . exactmetrics_get_url( 'notice', 'cannot-verify-license', 'https://www.exactmetrics.com/my-account/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		if ( exactmetrics_is_pro_version() ) {
			$valid = is_network_admin() ? ExactMetrics()->license->is_network_licensed() : ExactMetrics()->license->is_site_licensed();
			if ( ! $valid ) {
				// Translators: Support Link tag starts with url and Support link tag ends.
				$message = sprintf(
					__( 'Please enter a valid license within the ExactMetrics settings panel. You can check your license by logging into your ExactMetrics account by %1$sclicking here%2$s.', 'google-analytics-dashboard-for-wp' ),
					'<a target="_blank" rel="noopener" href="' . exactmetrics_get_url( 'notice', 'cannot-verify-license', 'https://www.exactmetrics.com/my-account/' ) . '">',
					'</a>'
				);
				wp_send_json_error( array( 'message' => $message ) );
			}
		}

		$worked = $this->verify_auth();
		if ( $worked && ! is_wp_error( $worked ) ) {
			wp_send_json_success( array( 'message' => __( "Successfully verified.", 'google-analytics-dashboard-for-wp' ) ) );
		} else {
			// Translators: Support Link tag starts with url and Support link tag ends.
			$message = sprintf(
				__( 'Oops! There has been an error while trying to verify your license. Please try again or contact our support team by %1$sclicking here%2$s.', 'google-analytics-dashboard-for-wp' ),
				'<a target="_blank" href="' . exactmetrics_get_url( 'notice', 'cannot-verify-license', 'https://www.exactmetrics.com/my-account/support/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}
	}

	public function verify_auth( $credentials = array() ) {
		$creds = ! empty( $credentials ) ? $credentials : ( $this->is_network_admin() ? ExactMetrics()->auth->get_network_analytics_profile( true ) : ExactMetrics()->auth->get_analytics_profile( true ) );

		if ( empty( $creds['key'] ) ) {
			// Translators: Support Link tag starts with url and Support link tag ends.
			$message = sprintf(
				__( 'Oops! There has been an error while trying to verify your license. Please try again or contact our support team by %1$sclicking here%2$s.', 'google-analytics-dashboard-for-wp' ),
				'<a target="_blank" href="' . exactmetrics_get_url( 'notice', 'cannot-verify-license', 'https://www.exactmetrics.com/my-account/support/' ) . '">',
				'</a>'
			);

			return new WP_Error( 'validation-error', $message );
		}

		$network = ! empty( $_REQUEST['network'] ) ? $_REQUEST['network'] === 'network' : $this->is_network_admin();
		$api     = new ExactMetrics_API_Request( $this->get_route( 'auth/verify/{type}/' ), array(
			'network' => $network,
			'tt'      => $this->get_tt(),
			'key'     => $creds['key'],
			'token'   => $creds['token'],
			'testurl' => 'https://' . exactmetrics_get_api_url() . 'test/'
		) );
		$ret     = $api->request();

		$this->rotate_tt();
		if ( is_wp_error( $ret ) ) {
			return $ret;
		} else {
			return true;
		}
	}

	public function maybe_delete() {

		// Check nonce
		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		$url = network_admin_url( 'admin.php?page=exactmetrics-onboarding' );

		// current user can delete
		if ( ! current_user_can( 'exactmetrics_save_settings' ) ) {
			// Translators: Link tag starts with url and link tag ends.
			$message = sprintf(
				__( 'You don\'t have the correct WordPress user permissions to deauthenticate into ExactMetrics. Please check with your site administrator that your role is included in the ExactMetrics permissions settings. %1$sClick here for more information%2$s.', 'google-analytics-dashboard-for-wp' ),
				'<a target="_blank" href="' . exactmetrics_get_url( 'notice', 'cannot-save-settings', 'https://www.exactmetrics.com/docs/how-to-allow-user-roles-to-access-the-exactmetrics-reports-and-settings/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		if ( ! empty( $_REQUEST['isnetwork'] ) && filter_var($_REQUEST['isnetwork'], FILTER_VALIDATE_BOOL) ) {
			define( 'WP_NETWORK_ADMIN', true );
		}

		// we have an auth to delete
		if ( $this->is_network_admin() && ! ExactMetrics()->auth->is_network_authed() ) {
			// Translators: Setup Wizard link tag starts, Setup Wizard link tag end, Support link tag starts with url and support link tag ends.
			$message = sprintf(
				__( 'Could not disconnect as you are not currently authenticated properly. Please try to authenticate again with our ExactMetrics %1$ssetup wizard%2$s.  If you are still having problems, please %3$scontact our support%4$s team.', 'google-analytics-dashboard-for-wp' ),
				'<a href="' . esc_url( $url ) . '">',
				'</a>',
				'<a target="_blank" href="' . exactmetrics_get_url( 'notice', 'cannot-de-authenticate-license', 'https://www.exactmetrics.com/my-account/support/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		} else if ( ! $this->is_network_admin() && ! ExactMetrics()->auth->is_authed() ) {
			// Translators: Setup Wizard link tag starts, Setup Wizard link tag end, Support link tag starts with url and support link tag ends.
			$message = sprintf(
				__( 'Could not disconnect as you are not currently authenticated properly. Please try to authenticate again with our ExactMetrics %1$ssetup wizard%2$s.  If you are still having problems, please %3$scontact our support%4$s team.', 'google-analytics-dashboard-for-wp' ),
				'<a href="' . esc_url( $url ) . '">',
				'</a>',
				'<a target="_blank" href="' . exactmetrics_get_url( 'notice', 'cannot-de-authenticate-license', 'https://www.exactmetrics.com/my-account/support/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		if ( exactmetrics_is_pro_version() ) {
			$valid = is_network_admin() ? ExactMetrics()->license->is_network_licensed() : ExactMetrics()->license->is_site_licensed();
			if ( ! $valid ) {
				// Translators: Setup Wizard link tag starts, Setup Wizard link tag end, Support link tag starts with url and support link tag ends.
				$message = sprintf(
					__( 'Could not disconnect your account, as you are not currently authenticated properly. Please try to authenticate again with our %1$sExactMetrics setup wizard%2$s.  If you are still having problems, please %3$scontact our support%4$s team.', 'google-analytics-dashboard-for-wp' ),
					'<a href="' . esc_url( $url ) . '">',
					'</a>',
					'<a target="_blank" href="' . exactmetrics_get_url( 'notice', 'cannot-de-authenticate-license', 'https://www.exactmetrics.com/my-account/support/' ) . '">',
					'</a>'
				);
				wp_send_json_error( array( 'message' => $message ) );
			}
		}

		$force = ! empty( $_REQUEST['forcedelete'] ) && $_REQUEST['forcedelete'] === 'true';

		$worked = $this->delete_auth( $force );
		if ( $worked && ! is_wp_error( $worked ) ) {
			if ( class_exists( 'ExactMetrics_Google_Ads' ) ) {
				// Clear any Google Ads stored data
				ExactMetrics_Google_Ads::clear_data();
			}
			
			wp_send_json_success( array( 'message' => __( "Successfully deauthenticated.", 'google-analytics-dashboard-for-wp' ) ) );
		} else {
			if ( $force ) {
				wp_send_json_success( array( 'message' => __( "Successfully force deauthenticated.", 'google-analytics-dashboard-for-wp' ) ) );
			} else {
				// Translators: Support link tag starts with url and support link tag ends.
				$message = sprintf(
					__( 'Oops! There has been an error while trying to deauthenticate. Please try again. If the issue persists, please %1$scontact our support%2$s team.', 'google-analytics-dashboard-for-wp' ),
					'<a target="_blank" href="' . exactmetrics_get_url( 'notice', 'cannot-de-authenticate-license', 'https://www.exactmetrics.com/my-account/support/' ) . '">',
					'</a>'
				);
				wp_send_json_error( array( 'message' => $message ) );
			}
		}
	}

	public function delete_auth( $force = false ) {
		if ( $this->is_network_admin() && ! ExactMetrics()->auth->is_network_authed() ) {
			return false;
		} else if ( ! $this->is_network_admin() && ! ExactMetrics()->auth->is_authed() ) {
			return false;
		}

		$creds = $this->is_network_admin() ? ExactMetrics()->auth->get_network_analytics_profile( true ) : ExactMetrics()->auth->get_analytics_profile( true );

		if ( empty( $creds['key'] ) ) {
			return false;
		}

		// If we have a new siteurl enabled option and the profile site doesn't match the current site, deactivate anyways
		if ( is_network_admin() ) {
			$siteurl = network_admin_url();
			if ( ! empty( $creds['neturl'] ) && $creds['neturl'] !== $siteurl ) {
				ExactMetrics()->auth->delete_network_analytics_profile( true );

				return true;
			}
		} else {
			$siteurl = home_url();
			if ( ! empty( $creds['siteurl'] ) && $creds['siteurl'] !== $siteurl ) {
				ExactMetrics()->auth->delete_analytics_profile( true );

				return true;
			}
		}

		$api = new ExactMetrics_API_Request( $this->get_route( 'auth/delete/{type}/' ), array(
			'network' => $this->is_network_admin(),
			'tt'      => $this->get_tt(),
			'key'     => $creds['key'],
			'token'   => $creds['token'],
			'testurl' => 'https://' . exactmetrics_get_api_url() . 'test/'
		) );
		$ret = $api->request();

		$this->rotate_tt();
		if ( is_wp_error( $ret ) && ! $force ) {
			return false;
		} else {
			if ( $this->is_network_admin() ) {
				ExactMetrics()->auth->delete_network_analytics_profile( true );
			} else {
				ExactMetrics()->auth->delete_analytics_profile( true );
			}

			return true;
		}
	}

	/**
	 * Function to delete network auth in the uninstall process where we can't check if is network admin.
	 *
	 * @return bool
	 */
	public function uninstall_network_auth() {

		if ( ! ExactMetrics()->auth->is_network_authed() ) {
			return false;
		}

		$creds = ExactMetrics()->auth->get_network_analytics_profile( true );

		$api = new ExactMetrics_API_Request( $this->get_route( 'auth/delete/{type}/' ), array(
			'network' => true,
			'tt'      => $this->get_tt(),
			'key'     => $creds['key'],
			'token'   => $creds['token'],
			'testurl' => 'https://' . exactmetrics_get_api_url() . 'test/'
		) );
		// Force the network admin url otherwise this will fail not finding the url in relay.
		$api->site_url = network_admin_url();
		$ret           = $api->request();

		$this->rotate_tt();
		if ( is_wp_error( $ret ) ) {
			return false;
		} else {
			ExactMetrics()->auth->delete_network_analytics_profile( true );

			return true;
		}
	}

	public function get_type() {
		$base = exactmetrics_is_pro_version() ? 'pro' : 'lite';

		return apply_filters( 'exactmetrics_api_auth_get_type', $base );
	}

	public function get_route( $route = '' ) {
		$route = str_replace( '{type}', $this->get_type(), $route );
		$route = trailingslashit( $route );

		return $route;
	}

	public function is_network_admin() {
		return is_multisite() && is_network_admin();
	}

	public function get_sitei() {
		// $sitei = get_network_option(  get_current_network_id(), 'exactmetrics_network_sitei', false );
		// if ( ! empty( $sitei ) && strlen( $sitei ) >= 1 ) {
		// 	return $sitei;
		// }

		return exactmetrics_get_sitei();
	}

	/**
	 * Logic to run before serving the redirect url during auth.
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	public function before_redirect( $url ) {

		// If Bad Behavior plugin is installed.
		if ( function_exists( 'bb2_read_settings' ) ) {
			// Make sure the offsite_forms option is enabled to allow auth.
			$bb_settings = get_option( 'bad_behavior_settings' );
			if ( empty( $bb_settings['offsite_forms'] ) || false === $bb_settings['offsite_forms'] ) {
				$bb_settings['offsite_forms'] = true;
				update_option( 'bad_behavior_settings', $bb_settings );
			}
		}

		return $url;
	}

	/**
	 * Delete auth-related options from the db - should be run at site level.
	 */
	public function uninstall_auth() {
		delete_option( 'exactmetrics_site_profile' );
		delete_option( 'exactmetrics_site_tt' );
	}

	/**
	 * Save the measurement protocol that Relay pushes to this site
	 */
	public function handle_relay_mp_token_push() {
		$mp_token  = sanitize_text_field( $_POST['mp_token'] ); // phpcs:ignore
		$timestamp = (int) sanitize_text_field( $_POST['timestamp'] ); // phpcs:ignore
		$signature = sanitize_text_field( $_POST['signature'] ); // phpcs:ignore

		// check if expired
		if ( time() > $timestamp + 1000 ) {
			wp_send_json_error( new WP_Error( 'exactmetrics_mp_token_timestamp_expired' ) );
		}

		// Check hashed signature
		$auth = ExactMetrics()->auth;

		$is_network = is_multisite();
		$public_key = $is_network
			? $auth->get_network_key()
			: $auth->get_key();

		$hashed_data = array(
			'mp_token'  => !empty($_POST['mp_token']) ? sanitize_text_field($_POST['mp_token']) : '', // phpcs:ignore WordPress.Security.NonceVerification.Missing
			'timestamp' => $timestamp,
		);

		// These `hash_` functions are polyfilled by WP in wp-includes/compat.php
		$expected_signature = hash_hmac( 'md5', http_build_query( $hashed_data ), $public_key );
		if ( ! hash_equals( $signature, $expected_signature ) ) {
			wp_send_json_error( new WP_Error( 'exactmetrics_mp_token_invalid_signature' ) );
		}

		// Save measurement protocol token
		if ( $is_network ) {
			$auth->set_network_measurement_protocol_secret( $mp_token );
		} else {
			$auth->set_measurement_protocol_secret( $mp_token );
		}
		wp_send_json_success();
	}

	/**
	 * Check site and property timezone.
	 *
	 * @return void
	 */
	private function check_property_timezone() {
		if ( empty( $_REQUEST['timezone'] ) ) {
			return;
		}

		try {
			$timezone_string = sanitize_text_field( $_REQUEST['timezone'] );
			$prop_time_now   = new DateTime( "now", new DateTimeZone( $timezone_string ) );
			$wp_time_now     = new DateTimeImmutable( "now", wp_timezone() );

			// If there offset don't match it means they are not in same timezone.
			if ( $wp_time_now->getOffset() != $prop_time_now->getOffset() ) {
				$notification = array(
					'id'       => 'exactmetrics_property_different_timezone',
					'type'     => array( 'basic', 'lite', 'master', 'plus', 'pro' ),
					'start'    => $wp_time_now->format( 'Y-m-d H:i:s' ),
					'end'      => $wp_time_now->modify( "+1 month" )->format( 'Y-m-d' ),
					'title'    => esc_html__( 'Website & Google Analytics Timezone Mismatch', 'google-analytics-dashboard-for-wp' ),
					// Translators: Placeholders add a link to the settings page.
					'content'  => sprintf(
						esc_html__( 'We detected your Google Analytics property is set to a different timezone than your website. This may slightly impact your stats, especially when comparing day over day performance. You may want to adjust your website\'s time zone %1$shere%2$s.', 'google-analytics-dashboard-for-wp' ),
						'<a href="' . admin_url( 'options-general.php' ) . '">',
						'</a>'
					),
					'priority' => 1,
				);

				// Add the notification.
				ExactMetrics()->notifications->add( $notification );
			}
		} catch ( Exception $e ) {
			return;
		}
	}
	/**
	 * Process the authentication step from the new onboarding.
	 */
	public function onboarding_maybe_authenticate() {
		if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'onboarding' ) ) { //phpcs:ignore
			wp_send_json_error( array( 'message' => 'Nonce not valid' ) );
		}
		if ( ! empty( $_REQUEST['isnetwork'] ) && $_REQUEST['isnetwork'] ) { // phpcs:ignore
			define( 'WP_NETWORK_ADMIN', true );
		}

		// Only for Pro users, require a license key to be entered first so we can link to things.
		if ( exactmetrics_is_pro_version() ) {
			$valid = is_network_admin() ? ExactMetrics()->license->is_network_licensed() : ExactMetrics()->license->is_site_licensed();
			if ( ! $valid ) {
				wp_send_json_error( array( 'message' => __( 'Cannot authenticate. Please enter a valid, active license key for ExactMetrics Pro into the settings page.', 'google-analytics-dashboard-for-wp' ) ) );
			}
		}

		if ( ! $this->is_network_admin() && ExactMetrics()->auth->is_authed() ) {
			// Translators: Support link tag starts with url, Support link tag ends.
			$message = sprintf(
				__( 'Oops! There has been an error authenticating. Please try again in a few minutes. If the problem persists, please %1$scontact our support%2$s team.', 'google-analytics-dashboard-for-wp' ),
				'<a target="_blank" href="' . exactmetrics_get_url( 'notice', 'error-authenticating', 'https://www.exactmetrics.com/my-account/support/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		} else if ( $this->is_network_admin() && ExactMetrics()->auth->is_network_authed() ) {
			// Translators: Support link tag starts with url, Support link tag ends.
			$message = sprintf(
				__( 'Oops! There has been an error authenticating. Please try again in a few minutes. If the problem persists, please %1$scontact our support%2$s team.', 'google-analytics-dashboard-for-wp' ),
				'<a target="_blank" href="' . exactmetrics_get_url( 'notice', 'error-authenticating', 'https://www.exactmetrics.com/my-account/support/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		$sitei = $this->get_sitei();

		$site_type = exactmetrics_get_option( 'site_type' );

		$auth_request_args = array(
			'tt'        => $this->get_tt(),
			'sitei'     => $sitei,
			'miversion' => EXACTMETRICS_VERSION,
			'ajaxurl'   => admin_url( 'admin-ajax.php' ),
			'network'   => is_network_admin() ? 'network' : 'site',
			'siteurl'   => is_network_admin() ? network_admin_url() : home_url(),
			'return'    => is_network_admin() ? network_admin_url( 'admin.php?page=exactmetrics_network' ) : admin_url( 'admin.php?page=exactmetrics_settings' ),
			'testurl'   => 'https://' . exactmetrics_get_api_url() . 'test/',
			'site_type' => $site_type ?? 'business',
		);
		$auth_request_args = apply_filters( 'exactmetrics_auth_request_body', $auth_request_args );

		$siteurl = add_query_arg( $auth_request_args, $this->get_route( 'https://' . exactmetrics_get_api_url() . 'auth/new/{type}' ) );

		if ( exactmetrics_is_pro_version() ) {
			$key     = is_network_admin() ? ExactMetrics()->license->get_network_license_key() : ExactMetrics()->license->get_site_license_key();
			$siteurl = add_query_arg( 'license', $key, $siteurl );
		}

		$siteurl = apply_filters( 'exactmetrics_maybe_authenticate_siteurl', $siteurl );
		wp_send_json_success( array( 'redirect' => $siteurl ) );
	}

}
