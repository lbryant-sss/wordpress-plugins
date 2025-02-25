<?php

namespace SolidWP\Mail\Hooks;

use PHPMailer\PHPMailer\SMTP;
use SolidWP\Mail\AbstractController;
use SolidWP\Mail\App;
use SolidWP\Mail\Repository\ProvidersRepository;
use SolidWP\Mail\SolidMailer;

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
		add_filter( 'pre_wp_mail', [ $this, 'init_solidmail_mailer' ] );
		add_action( 'phpmailer_init', [ $this, 'wp_smtp' ], 9999 );
		add_action( 'wp_mail_failed', [ $this, 'maybe_capture_sending_error' ] );
	}

	/**
	 * Initializes the SolidMail mailer integration. Return null so the default behavior continue to run.
	 *
	 * @since 2.1.3
	 *
	 * @return null
	 */
	public function init_solidmail_mailer() {
		$active_provider = $this->providers_repository->get_active_provider();

		if ( ! is_object( $active_provider ) ) {
			// if there is no active provider, or the active provider use SMTP, then dont inject anything.
			return null;
		}

		// Declare the phpmailer instance before the wp_mail does it.
		global $phpmailer;
		if ( ! $phpmailer instanceof SolidMailer ) {
			require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
			require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
			require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';

			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$phpmailer = new SolidMailer( true );
			$phpmailer->set_connector( $active_provider );
		}

		return null;
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
	 * @param SolidMailer $phpmailer The PHPMailer instance.
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

			if ( $default_provider->isAPI() ) {
				// set this as API sender.
				$phpmailer->isAPI();
			}
		}
	}
}
