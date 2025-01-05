<?php

namespace SolidWP\Mail\Service;

use PHPMailer\PHPMailer\PHPMailer;
use SolidWP\Mail\Connectors\ConnectorSMTP;
use SolidWP\Mail\Repository\ProvidersRepository;
use SolidWP\Mail\StellarWP\Validation\Validator;

/**
 * Class ConnectionService
 *
 * This class handles the business logic for managing SMTP connections.
 *
 * @package Solid_SMTP\Service
 */
class ConnectionService {

	/**
	 * The repository for managing SMTP mailers.
	 *
	 * @var ProvidersRepository
	 */
	protected ProvidersRepository $providers_repository;

	/**
	 * Constructor for the class.
	 *
	 * @param ProvidersRepository $providers_repository The repository instance for managing providers.
	 */
	public function __construct( ProvidersRepository $providers_repository ) {
		$this->providers_repository = $providers_repository;
	}

	/**
	 * Saves a new SMTP connection.
	 *
	 * @param array $data The data for the new SMTP connection.
	 *
	 * @return bool|\WP_Error The result of the save operation, either the updated providers or validation errors.
	 */
	public function save_connection( array $data ) {
		$name = $data['name'] ?? '';

		if ( ! empty( $data['id'] ) ) {
			$model = $this->providers_repository->get_provider_by_id( $data['id'] );
		} else {
			$model = $this->providers_repository->factory( $name );
		}

		if ( ! is_object( $model ) ) {
			$wp_error = new \WP_Error();
			$wp_error->add( 'invalid_model', __( 'Invalid model object.', 'wp-smtp' ) );

			return $wp_error;
		}

		$model->process_data( $data );

		if ( $model->validation() ) {
			$this->providers_repository->save( $model );

			$is_active = filter_var( $data['is_active'] ?? false, FILTER_VALIDATE_BOOLEAN );

			if ( $is_active ) {
				// let make this active and turn other off.
				$this->make_provider_active( $model->get_id() );
			} else {
				$model->set_is_active( $is_active );
				$this->providers_repository->save( $model );
			}

			return true;
		}

		// Convert validation errors to WP_Error.
		$errors   = $model->get_errors();
		$wp_error = new \WP_Error();

		foreach ( $errors as $field => $error_message ) {
			$wp_error->add( $field, $error_message );
		}

		return $wp_error;
	}

	/**
	 * Deletes an SMTP connection.
	 *
	 * @param string $provider_id The ID of the provider to delete.
	 *
	 * @return array The updated list of providers.
	 */
	public function delete_connection( string $provider_id ): array {
		$this->providers_repository->delete_provider_by_id( $provider_id );

		return $this->providers_repository->get_all_providers_as_array();
	}

	/**
	 * Makes an SMTP provider active.
	 *
	 * @param string $provider_id The ID of the provider to activate.
	 *
	 * @return array The updated list of providers or an error message.
	 */
	public function make_provider_active( string $provider_id ): array {
		$provider = $this->providers_repository->get_provider_by_id( $provider_id );
		if ( empty( $provider ) ) {
			return [ 'error' => 'Provider not found.' ];
		}

		$this->providers_repository->set_active_provider( $provider_id );

		return $this->providers_repository->get_all_providers_as_array();
	}

	/**
	 * Validates the test email input.
	 *
	 * @param array $data The data for the test email.
	 *
	 * @return bool|array The result of the validation, either validated data or errors.
	 */
	public function validate_test_email_input( array $data ) {
		$rules = [
			'to_email' => [ 'required', 'email' ],
			'subject'  => [ 'required' ],
			'message'  => [ 'required' ],
		];

		$labels = [
			'to_email' => 'To Email',
			'subject'  => 'Subject',
			'message'  => 'Message',
		];

		$validator = new Validator( $rules, $data, $labels );

		if ( $validator->passes() ) {
			return true;
		}

		return $validator->errors();
	}

	/**
	 * Tests the SMTP connection using the provided connector.
	 *
	 * @param ConnectorSMTP $connector An instance of the ConnectorSMTP class containing SMTP credentials and settings.
	 *
	 * @global PHPMailer $phpmailer The PHPMailer instance used to send emails.
	 *
	 * @return bool|\WP_Error True if the connection is successful; WP_Error if the connection fails or an exception is thrown.
	 */
	public function test_smtp_connection( ConnectorSMTP $connector ) {
		global $phpmailer;

		if ( ! ( $phpmailer instanceof PHPMailer ) ) {
			require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
			require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
			require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
			$phpmailer = new PHPMailer( true );
		}

		try {
			// SMTP configuration
			$phpmailer->isSMTP();
			$phpmailer->Timeout    = 5;
			$phpmailer->Host       = $connector->get_host();
			$phpmailer->SMTPAuth   = $connector->is_authentication();
			$phpmailer->Username   = $connector->get_username();
			$phpmailer->Password   = $connector->get_password();
			$phpmailer->SMTPSecure = $connector->get_secure();
			$phpmailer->Port       = $connector->get_port();

			// Attempt to connect to SMTP server
			if ( $phpmailer->smtpConnect() ) {
				$phpmailer->smtpClose();

				return true;
			} else {
				return new \WP_Error( 'smtp_creds_fail', $phpmailer->ErrorInfo );
			}
		} catch ( \Exception $e ) {
			return new \WP_Error( 'smtp_exception', $e->getMessage() );
		}
	}
}
