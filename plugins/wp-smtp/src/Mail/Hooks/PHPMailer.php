<?php

namespace SolidWP\Mail\Hooks;

use SolidWP\Mail\AbstractController;
use SolidWP\Mail\App;
use SolidWP\Mail\Repository\ProvidersRepository;

/**
 * Class MailerController
 *
 * This class is responsible for handling email functionality within the Solid_SMTP plugin.
 *
 * @package Solid_SMTP\Controller
 */
class PHPMailer extends AbstractController {

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
	 * Register hooks.
	 *
	 * Implementing the InterfaceController interface, this method registers hooks related to email functionality.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'phpmailer_init', [ $this, 'wp_smtp' ], 9999 );
		add_action( 'wp_mail_failed', [ $this, 'maybe_capture_sending_error' ] );
	}

	/**
	 * Captures the sending error if one occurs.
	 *
	 * This function sets the PHPMailer send error variable in the application with the provided WP_Error object.
	 *
	 * @param \WP_Error $error The error object to capture.
	 */
	public function maybe_capture_sending_error( \WP_Error $error ) {
		App::setVar( 'phpmailer_send_error', $error );
	}

	/**
	 * Configure PHPMailer for SMTP.
	 *
	 * This method is invoked when PHPMailer is initialized to configure it for SMTP usage.
	 *
	 * @param \PHPMailer $phpmailer The PHPMailer instance.
	 *
	 * @return void
	 */
	public function wp_smtp( $phpmailer ) {
		$default_provider = $this->providers_repository->get_active_provider();
		// make sure the provider data right.
		if ( is_object( $default_provider ) && $default_provider->validation() === true ) {
			// now bind the SMTP info to wp phpmailer.
			$phpmailer->Mailer   = 'smtp';
			$phpmailer->From     = $default_provider->get_from_email();
			$phpmailer->FromName = $default_provider->get_from_name();
			$phpmailer->Sender   = $phpmailer->From;
			$phpmailer->AddReplyTo( $phpmailer->From, $phpmailer->FromName );
			$phpmailer->Host       = $default_provider->get_host();
			$phpmailer->SMTPSecure = $default_provider->get_secure();
			$phpmailer->Port       = $default_provider->get_port();
			$phpmailer->SMTPAuth   = $default_provider->is_authentication();

			if ( $phpmailer->SMTPAuth ) {
				$phpmailer->Username = $default_provider->get_username();
				$phpmailer->Password = $default_provider->get_password();
			}
		}
	}
}
