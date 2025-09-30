<?php
/**
 * Security helpers for Click to Chat REST endpoints.
 *
 * @package Click_To_Chat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'HT_CTC_Security' ) ) {

	/**
	 * Provides validation utilities for REST requests.
	 */
	class HT_CTC_Security {


		/**
		 * Validate the incoming REST request.
		 *
		 * @param WP_REST_Request $request Request instance to validate.
		 * @return WP_REST_Response|true Response when invalid or true when valid.
		 */
		public static function validate_rest_request( $request ) {

			try {
				$site_url = get_site_url();
				$referer  = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';

				// Referer check
				if ( strpos( $referer, $site_url ) === false ) {
					return new WP_REST_Response( array( 'error' => 'Invalid referer' ), 403 );
				}

				// Nonce check (optional, only if frontend sends it)
				$nonce = $request->get_header( 'x_wp_nonce' );

				// ht_ctc_nonce
				if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
					return new WP_REST_Response( array( 'error' => 'Invalid nonce' ), 403 );
				}

				// Optional: Bounce or User-Agent logic (custom abuse logic)
				$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
				if ( empty( $user_agent ) ) {
					return new WP_REST_Response( array( 'error' => 'Invalid user agent' ), 403 );
				}
			} catch ( Throwable $e ) {
				return new WP_REST_Response( array( 'Catch: error' => 'Server error' ), 500 );
			}

			return true; // All checks passed
		}
	}

	// new HT_CTC_Security();

} // END class_exists check
