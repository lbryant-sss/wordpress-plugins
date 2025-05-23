<?php

namespace EasyWPSMTP\Providers\Outlook;

use EasyWPSMTP\WP;

/**
 * Class Provider.
 *
 * @since 2.9.0
 */
class Provider {

	/**
	 * Dismissed basic auth deprecation notice user meta.
	 *
	 * @since 2.9.0
	 *
	 * @var string
	 */
	private $dismissed_notice_key = 'easy_wp_smtp_microsoft_basic_auth_deprecation_general_notice_dismissed';

	/**
	 * Register hooks.
	 *
	 * @since 2.9.0
	 */
	public function hooks() {

		// Maybe display basic auth deprecation notice.
		add_action( 'admin_init', [ $this, 'maybe_display_basic_auth_notice' ] );

		// AJAX callback for basic auth deprecation notice dismissal.
		add_action( 'wp_ajax_easy_wp_smtp_microsoft_basic_auth_deprecation_notice_dismiss', [ $this, 'dismiss_basic_auth_notice' ] );
	}

	/**
	 * Display basic auth deprecation notice.
	 *
	 * @since 2.9.0
	 */
	public function maybe_display_basic_auth_notice() {

		$connection = easy_wp_smtp()->get_connections_manager()->get_primary_connection();

		// Bail if Other SMTP is not the current mailer.
		if ( $connection->get_mailer_slug() !== 'smtp' ) {
			return;
		}

		$host        = $connection->get_options()->get( 'smtp', 'host' );
		$host_suffix = strtolower( implode( '.', array_slice( explode( '.', $host ), - 2 ) ) );
		$domains     = [
			'live.com',
			'hotmail.com',
			'outlook.com',
			'office.com',
			'office365.com',
		];

		// Bail if current SMTP host is not Microsoft-related.
		if ( ! in_array( $host_suffix, $domains, true ) ) {
			return;
		}

		// Bail if the notice has been dismissed.
		if ( metadata_exists( 'user', get_current_user_id(), $this->dismissed_notice_key ) ) {
			return;
		}

		$message = wp_kses(
			sprintf( /* translators: %1$s - documentation link. */
				__( '<strong>%1$s</strong><br>Heads up! Microsoft is <a href="%2$s" target="_blank" rel="noopener noreferrer">discontinuing support for basic SMTP connections</a>. To continue using Outlook or Hotmail, switch to our Outlook mailer for uninterrupted email sending.', 'easy-wp-smtp' ),
				esc_html__( 'Easy WP SMTP', 'easy-wp-smtp' ),
				easy_wp_smtp()->get_utm_url(
					'https://easywpsmtp.com/blog/microsoft-365-basic-authentication-end-of-life/',
					[
						'medium'  => 'outlook-smtp-notice',
						'content' => 'other-smtp-lite-to-outlook',
					]
				)
			),
			[
				'strong' => [],
				'br'     => [],
				'a'      => [
					'href'   => [],
					'rel'    => [],
					'target' => [],
				],
			]
		);

		if ( ! easy_wp_smtp()->is_pro() ) {
			$message = wp_kses(
				sprintf( /* translators: %1$s - Notice message; %2$s - upgrade link. */
					__( '%1$s <a href="%2$s" target="_blank" rel="noopener noreferrer">Upgrade to Pro now for easy, one-click Outlook setup</a>.', 'easy-wp-smtp' ),
					$message,
					easy_wp_smtp()->get_upgrade_link( [ 'medium' => 'outlook-smtp-notice', 'content' => 'other-smtp-lite-to-outlook' ] ) // phpcs:ignore WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
				),
				[
					'strong' => [],
					'br'     => [],
					'a'      => [
						'href'   => [],
						'rel'    => [],
						'target' => [],
					],
				]
			);
		}

		WP::add_admin_notice( $message, implode( ' ', [ WP::ADMIN_NOTICE_ERROR, 'microsoft_basic_auth_deprecation_notice' ] ) );
	}

	/**
	 * Dismiss basic auth deprecation notice.
	 *
	 * @since 2.9.0
	 */
	public function dismiss_basic_auth_notice() {

		if ( ! current_user_can( easy_wp_smtp()->get_capability_manage_options() ) ) {
			wp_send_json_error();
		}

		if ( ! check_ajax_referer( 'easy-wp-smtp-admin', 'nonce', false ) ) {
			return;
		}

		update_user_meta( get_current_user_id(), $this->dismissed_notice_key, true );

		wp_send_json_success();
	}
}
