<?php

namespace SolidWP\Mail;

use LogicException;
use PHPMailer\PHPMailer\Exception;
use SolidWP\Mail\Connectors\ConnectionPool;
use SolidWP\Mail\Connectors\ConnectorSMTP;
use SolidWP\Mail\Contracts\Api_Connector;
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
 * @extends \WP_PHPMailer
 */
class SolidMailer extends \WP_PHPMailer {

	/**
	 * Whether to throw exceptions for errors.
	 *
	 * @var bool
	 */
	protected $exceptions = true;

	/**
	 * Pool of connections available for the mail sending.
	 *
	 * @var ConnectionPool | null
	 */
	protected ?ConnectionPool $connection_pool = null;

	/**
	 * The email "from name" that the user passed.
	 *
	 * @var string
	 */
	protected string $initial_from_name = '';

	/**
	 * Sets the SMTP connector instance.
	 *
	 * @param ConnectionPool|null $connection_pool Pool of connections available for the mail sending.
	 *                                          or null to reset the pool.
	 *
	 * @since 2.1.3
	 * @return void
	 */
	public function set_pool( ConnectionPool $connection_pool ): void {
		$this->connection_pool = $connection_pool;
	}

	/**
	 * Define the initial "from name" for the current email request.
	 *
	 * @param string $initial_from_name
	 *
	 * @return void
	 */
	public function set_initial_from_name( string $initial_from_name ): void {
		$this->initial_from_name = $initial_from_name;
	}

	/**
	 * Current connection.
	 *
	 * @return ConnectorSMTP|null
	 */
	public function get_connection(): ?ConnectorSMTP {
		if ( ! $this->connection_pool instanceof ConnectionPool ) {
			return null;
		}

		$current = $this->connection_pool->current();
		return $current instanceof ConnectorSMTP ? $current : null;
	}

	/**
	 * If Solid Mail is used for the current request
	 *
	 * @return bool
	 */
	public static function is_solid_mail_configured(): bool {
		global $phpmailer;
		return $phpmailer instanceof self && $phpmailer->get_connection() instanceof ConnectorSMTP;
	}

	/**
	 * Create a message and send it.
	 * Uses the sending method specified by $Mailer.
	 *
	 * @throws Exception
	 *
	 * @return bool false on error - See the ErrorInfo property for details of the error
	 */
	public function send(): bool {
		if ( ! $this->connection_pool instanceof ConnectionPool || $this->connection_pool->count() === 0 ) {
			// Pool is empty, send email without Solid Mail
			return parent::send();
		}

		try {
			$connection = $this->get_connection();
			if ( ! $connection instanceof ConnectorSMTP ) {
				throw new LogicException( 'Connection is not defined' );
			}

			$this->configure_for_connection( $connection );

			if ( ! $this->preSend() ) {
				return false;
			}

			return $this->postSend();
		} catch ( Exception $exc ) {
			/**
			 * As we have an `exceptions` property enabled, `preSend` and `postSend` methods throw an exception on error.
			 * So we can handle a negative path only once in the catch construction.
			 */
			if ( $this->connection_pool->hasNext() ) {
				$this->connection_pool->next();
				return $this->send();
			}

			$this->mailHeader = '';
			$this->setError( $exc->getMessage() );

			throw $exc;
		}
	}

	private function configure_for_connection( ConnectorSMTP $connection ): void {
		// now bind the SMTP info to wp phpmailer.
		$this->Mailer = $connection->isAPI() ? 'api' : 'smtp';
		$this->From   = $connection->get_from_email();
		// Don't override from name if it was defined already
		$this->FromName = $this->initial_from_name === 'WordPress' ? $connection->get_from_name() : $this->initial_from_name;
		$this->Sender   = $this->From;
		$this->clearReplyTos();
		$this->AddReplyTo( $this->From, $this->FromName );
		$this->Host       = $connection->get_host();
		$this->SMTPSecure = $connection->get_secure();
		$this->Port       = $connection->get_port();
		$this->SMTPAuth   = $connection->is_authentication();
		$this->Username   = $this->SMTPAuth ? $connection->get_username() : '';
		$this->Password   = $this->SMTPAuth ? $connection->get_password() : '';
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
		$connector = $this->get_connection();
		if ( ! $connector instanceof Api_Connector ) {
			throw new Exception( 'API connector is not defined', self::STOP_CRITICAL );
		}

		$email_data = $this->getEmailData( $header, $body );
		if ( is_wp_error( $email_data ) ) {
			throw new Exception( $email_data->get_error_message(), self::STOP_CRITICAL );
		}

		$result = $connector->send_use_api( $email_data );
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
