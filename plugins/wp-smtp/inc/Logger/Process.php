<?php

namespace WPSMTP\Logger;

use SolidWP\Mail\Admin\SettingsScreen;
use WP_Error;
use WPSMTP\Admin;

class Process {

	private $mail_id;
	private $wsOptions;

	/**
	 * The new solid mail settings.
	 *
	 * @var false|mixed|null
	 */
	private $solidMailOptions;

	public function __construct() {
		$this->wsOptions = get_option( 'wp_smtp_options' );

		$this->solidMailOptions = get_option( SettingsScreen::SETTINGS_SLUG );

		if ( ! isset( $this->solidMailOptions['disable_logs'] ) || 'yes' !== $this->solidMailOptions['disable_logs'] ) {
			add_filter( 'wp_mail', [ $this, 'log_mails' ], PHP_INT_MAX );
		}

		add_action( 'wp_mail_failed', [ $this, 'update_failed_status' ], PHP_INT_MAX );
	}

	public function log_mails( $parts ) {

		$data = $parts;

		unset( $data['attachments'] );

		$this->mail_id = Db::get_instance()->insert( $data );

		return $parts;
	}

	/**
	 * @param WP_Error $wp_error
	 */
	public function update_failed_status( $wp_error ) {

		Admin::$phpmailer_error = $wp_error;

		if ( ! isset( $this->wsOptions['disable_logs'] ) || 'yes' !== $this->wsOptions['disable_logs'] ) {

			$data = $wp_error->get_error_data( 'wp_mail_failed' );

			unset( $data['phpmailer_exception_code'] );
			unset( $data['attachments'] );

			$data['error'] = $wp_error->get_error_message();

			if ( ! is_numeric( $this->mail_id ) ) {
				Db::get_instance()->insert( $data );
			} else {
				Db::get_instance()->update( $data, [ 'mail_id' => $this->mail_id ] );
			}
		}
	}
}
