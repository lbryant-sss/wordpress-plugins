<?php

namespace SolidWP\Mail;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use SolidWP\Mail\Connectors\ConnectorSMTP;
use WP_Error;

/**
 * Extension of PHPMailer that adds API mailing capabilities.
 *
 * This class extends the base PHPMailer functionality to support sending emails
 * through API endpoints in addition to traditional SMTP methods. It provides
 * methods for configuring API connections and formatting email data for API
 * transmission.
 *
 * @since   2.1.3
 * @package SolidWP\Mail\Pro
 * @extends PHPMailer
 */
class SolidMailer extends PHPMailer {

	/**
	 * SMTP connector instance.
	 *
	 * @since 2.1.3
	 * @var ConnectorSMTP
	 */
	protected ConnectorSMTP $connector;

	/**
	 * Sets the SMTP connector instance.
	 *
	 * @param ConnectorSMTP $connector The connector instance to use for email transmission.
	 *
	 * @since 2.1.3
	 * @return void
	 */
	public function set_connector( ConnectorSMTP $connector ): void {
		$this->connector = $connector;
	}

	/**
	 * Sets the mailer type to API.
	 *
	 * @since 2.1.3
	 * @return void
	 */
	public function isAPI() {
		$this->Mailer = 'api';
	}

	/**
	 * Sends an email using the API connector.
	 *
	 * @param string $header The email headers
	 * @param string $body   The email body
	 *
	 * @since 2.1.3
	 * @return bool | WP_Error The result from the API send operation
	 * @throws \Exception
	 */
	public function apiSend( $header, $body ) {
		$email_data = $this->getEmailData( $header, $body );
		if ( is_wp_error( $email_data ) ) {
			throw new Exception( $email_data->get_error_message(), self::STOP_CRITICAL );
		}

		$result = $this->connector->send_use_api( $email_data );
		if ( is_wp_error( $result ) ) {
			throw new Exception( $result->get_error_message(), self::STOP_CRITICAL );
		}

		return $result;
	}

	/**
	 * Gets formatted email data including recipients, headers, and body content.
	 * This method extracts and organizes all relevant email sending information.
	 *
	 * @param string $header The email headers.
	 * @param string $body   The email body content.
	 *
	 * @since 2.1.3
	 *
	 * @return WP_Error | array{
	 *     to: array<array{0: string, 1: string}>,
	 *     cc: array<array{0: string, 1: string}>,
	 *     bcc: array<array{0: string, 1: string}>,
	 *     from: string,
	 *     sender: string,
	 *     subject: string,
	 *     headers: string,
	 *     body: string,
	 *     custom_headers: array<string, string>,
	 *     reply_to: array<array{0: string, 1: string}>,
	 *     all_recipients: array<string>
	 * }
	 */
	protected function getEmailData( string $header, string $body ): array {
		// Format header with proper line endings
		$formatted_header = static::stripTrailingWSP( $header ) . static::$LE . static::$LE;
		// Determine sender
		$sender = '' === $this->Sender ? $this->From : $this->Sender;

		// Get all custom headers
		$custom_headers = [];
		foreach ( $this->CustomHeader as $header ) {
			$custom_headers[ $header[0] ] = $header[1];
		}

		// Collect all recipients
		$all_recipients = array_merge(
			array_column( $this->to, 0 ),
			array_column( $this->cc, 0 ),
			array_column( $this->bcc, 0 )
		);

		$email_data = [
			// phpcs:ignore Squiz.PHP.CommentedOutCode.Found
			// Recipients with format: [[email, name], [email, name], ...]
			'to'             => $this->to,
			'cc'             => $this->cc,
			'bcc'            => $this->bcc,

			// Sender information
			'from'           => $this->From,
			'sender'         => $sender,

			// Content
			'subject'        => $this->Subject,
			'headers'        => $formatted_header,
			'body'           => $body,
			'raw_body'       => $this->encodeString( $this->Body, $this->Encoding ),

			// Additional data
			'custom_headers' => $custom_headers,
			'reply_to'       => $this->ReplyTo,
			'all_recipients' => array_unique( $all_recipients ),

			// Optional metadata if available
			'message_type'   => $this->ContentType ?? 'text/plain',
			'charset'        => $this->CharSet ?? 'utf-8',
			'encoding'       => $this->Encoding ?? '8bit',

			// Attachments
			'attachments'    => $this->attachment,
		];

		// Validate required data
		if ( empty( $email_data['from'] ) ) {
			return new WP_Error(
				'email_missing_from',
				'From address is required',
				[ 'status' => self::STOP_CRITICAL ]
			);
		}

		if ( empty( $email_data['all_recipients'] ) ) {
			return new WP_Error(
				'email_missing_recipients',
				'At least one recipient is required',
				[ 'status' => self::STOP_CRITICAL ]
			);
		}


		return $email_data;
	}
}
