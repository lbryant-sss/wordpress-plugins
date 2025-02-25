<?php
/**
 * ConnectionScreenController.php
 *
 * This file contains the ConnectionScreenController class which handles the connection screen logic
 * for the Solid SMTP plugin.
 *
 * @package Solid_SMTP\Controller
 */

namespace SolidWP\Mail\Admin;

use SolidWP\Mail\Container;
use SolidWP\Mail\AbstractController;
use SolidWP\Mail\Repository\ProvidersRepository;
use SolidWP\Mail\Service\ConnectionService;

/**
 * Class ConnectionScreenController
 *
 * Handles the connection screen logic for the Solid SMTP plugin.
 */
class ScreenConnectors extends AbstractController {

	/**
	 * Nonce name for this screen.
	 *
	 * @var string
	 */
	protected string $nonce_name = 'solidwp_mail_connections_nonce';

	/**
	 * Store the email error if any.
	 *
	 * @var string
	 */
	protected string $email_error = '';

	/**
	 * The service for managing SMTP connections.
	 *
	 * @var ConnectionService
	 */
	protected ConnectionService $connection_service;

	/**
	 * Container.
	 *
	 * @var Container
	 */
	private Container $container;

	/**
	 * ConnectionScreenController constructor.
	 *
	 * @param ConnectionService $connection_service The service for managing SMTP connections.
	 */
	public function __construct( ConnectionService $connection_service, Container $container ) {
		$this->connection_service = $connection_service;
		$this->container          = $container;
	}

	/**
	 * Registers the AJAX hooks for the connection screen.
	 *
	 * @return void
	 */
	public function register_hooks() {
		// record error for debug.
		add_action( 'wp_mail_failed', [ $this, 'record_error' ] );

		// ajax functions.
		add_action( 'wp_ajax_solidwp_mail_save_connection', [ $this, 'save_connection' ] );
		add_action( 'wp_ajax_solidwp_mail_delete_connection', [ $this, 'delete_connection' ] );
		add_action( 'wp_ajax_solidwp_mail_make_provider_active', [ $this, 'make_connection_active' ] );
		add_action( 'wp_ajax_solidwp_mail_send_test_email', [ $this, 'send_test_email' ] );
	}

	/**
	 * Records an error message.
	 *
	 * This method handles recording an error message from a `WP_Error` object.
	 * It extracts the error message from the `WP_Error` object and stores it in
	 * the `email_error` property.
	 *
	 * @param \WP_Error $wp_error The `WP_Error` object containing the error message.
	 *
	 * @return void
	 */
	public function record_error( \WP_Error $wp_error ) {
		$this->email_error = $wp_error->get_error_message();
	}

	/**
	 * Sends a test email.
	 *
	 * This method handles the AJAX request to send a test email. It validates the input,
	 * attempts to send the email using the `wp_mail` function, and returns a JSON response
	 * indicating success or failure.
	 *
	 * @return void
	 */
	public function send_test_email() {
		// Check if the current user has permission to perform this action.
		if ( ! $this->able_to_perform( 'send_test_email' ) ) {
			$this->bail_out_generic_error( __( 'User cannot send test emails.', 'LION' ) );
		}

		// Sanitize and retrieve input data.
		$data = [
			'to_email' => $this->get_and_sanitize_input( 'to_email' ),
			'subject'  => $this->get_and_sanitize_input( 'subject' ),
			'message'  => $this->get_and_sanitize_textarea( 'message' ),
		];

		$result = $this->connection_service->validate_test_email_input( $data );
		if ( is_array( $result ) ) {
			wp_send_json_error(
				[
					'validation' => $result,
				]
			);
		}

		// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_mail_wp_mail
		$sent = wp_mail( $data['to_email'], $data['subject'], $data['message'] );
		// Return a JSON response indicating success or failure.
		if ( $sent ) {
			wp_send_json_success( [ 'message' => __( 'Test email sent successfully.', 'LION' ) ] );
		} else {
			$wp_error = $this->container->get( 'phpmailer_send_error' );
			if ( is_wp_error( $wp_error ) ) {
				// error found, send more detailed version.
				$error_message = $wp_error->get_error_message();
				wp_send_json_error(
					[
						/* translators: %s: PHPMailer error */
						'message' => sprintf( __( 'Failed to send test email. Error: %s', 'LION' ), $error_message ),
					]
				);
			}
			// falling back.
			wp_send_json_error(
				[
					'message' => __( 'Failed to send test email.', 'LION' ),
				]
			);
		}
	}

	/**
	 * Deletes the connection.
	 *
	 * @return void
	 */
	public function delete_connection() {
		if ( ! $this->able_to_perform( 'delete_connection' ) ) {
			$this->bail_out_generic_error( __( 'User cannot delete providers.', 'LION' ) );
		}

		$provider_id = $this->get_and_sanitize_input( 'provider_id' );
		if ( empty( $provider_id ) ) {
			$this->bail_out_generic_error( __( 'Provider ID cannot be empty.', 'LION' ) );
		}

		$model = $this->container->get( ProvidersRepository::class )->get_active_provider();

		if ( is_object( $model ) && $model->get_id() === $provider_id ) {
			// this is the active provider, should not allow for delete.
			wp_send_json_error(
				[
					'message' => __( 'Cannot delete the connection because it is set as the default.', 'LION' ),
				]
			);
		}

		wp_send_json_success( $this->connection_service->delete_connection( $provider_id ) );
	}

	/**
	 * Saves a new SMTP connection.
	 *
	 * Validates the input data, processes it, and saves the SMTP connection model if validation succeeds.
	 * If validation fails or the user lacks the necessary permissions, an error response is sent.
	 *
	 * @return void
	 */
	public function save_connection() {
		if ( ! $this->able_to_perform( 'save_connection' ) ) {
			$this->bail_out_generic_error( __( 'User cannot add providers.', 'LION' ) );
		}

		// populate data.
		$name = $this->get_and_sanitize_input( 'name' );

		if ( empty( $name ) ) {
			$this->bail_out_generic_error( __( 'Name cannot be empty.', 'LION' ) );
		}

		//phpcs:ignore.
		$data = wp_unslash( $_POST );

		$result = $this->connection_service->save_connection( $data );

		if ( true === $result ) {
			wp_send_json_success( $this->container->get( ProvidersRepository::class )->get_all_providers_as_array() );
		}

		wp_send_json_error( $this->wp_error_to_array( $result ) );
	}

	/**
	 * Makes a provider active.
	 *
	 * @return void
	 */
	public function make_connection_active() {
		if ( ! $this->able_to_perform( 'make_connection_active' ) ) {
			$this->bail_out_generic_error( __( 'User cannot change active providers.', 'LION' ) );
		}

		$provider_id = $this->get_and_sanitize_input( 'provider_id' );
		if ( empty( $provider_id ) ) {
			$this->bail_out_generic_error( __( 'Provider cannot be empty.', 'LION' ) );
		}

		$this->connection_service->make_provider_active( $provider_id );

		wp_send_json_success( $this->container->get( ProvidersRepository::class )->get_all_providers_as_array() );
	}
}
