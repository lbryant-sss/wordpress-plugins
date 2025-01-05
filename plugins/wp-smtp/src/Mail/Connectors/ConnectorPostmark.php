<?php
/**
 * ConnectorPostmark class for managing Postmark SMTP connection settings.
 *
 * @package SolidWP\Mail\Connectors
 *
 * @since 2.1.0
 */

namespace SolidWP\Mail\Connectors;

class ConnectorPostmark extends ConnectorSMTP {

	/**
	 * ConnectorPostmark constructor.
	 * @param array $data
	 */
	public function __construct( array $data = [] ) {
		parent::__construct( $data );

		// Prefill the needed data for Postmark.
		$this->host           = 'smtp.postmarkapp.com';
		$this->port           = 587;
		$this->authentication = 'yes';
		$this->secure         = 'tls';
		$this->name           = 'postmark';
	}

	/**
	 * Processes the data for SMTP configuration.
	 *
	 * @param array $data The data array containing the email, name, and API key.
	 *
	 * @return void
	 */
	public function process_data( array $data ) {
		$this->from_email    = $data['from_email'] ?? '';
		$this->from_name     = $data['from_name'] ?? '';
		// this provider uses the API key for both username and password.
		$this->smtp_username = $data['smtp_username'] ?? '';
		$this->smtp_password = $data['smtp_username'] ?? '';
		$this->description   = 'Postmark';
	}
}