<?php

namespace Unit\Connectors;

use Codeception\Test\Unit;
use SolidWP\Mail\Connectors\ConnectorBrevo;

class ConnectorBrevoTest extends Unit {

	public function testDefaultSettings(): void {
		$provider = new ConnectorBrevo();

		self::assertEquals( 'smtp-relay.brevo.com', $provider->get_host() );
		self::assertEquals( 587, $provider->get_port() );
		self::assertTrue( $provider->is_authentication() );
		self::assertEquals( 'tls', $provider->get_secure() );
		self::assertEquals( 'brevo', $provider->get_name() );
	}

	public function testCustomData(): void {
		$data = [
			'from_email'    => 'custom@example.com',
			'from_name'     => 'Custom Sender',
			'smtp_username' => 'custom_user',
			'smtp_password' => 'custom_pass',
		];

		$provider = new ConnectorBrevo( $data );

		self::assertEquals( 'smtp-relay.brevo.com', $provider->get_host() );
		self::assertEquals( 587, $provider->get_port() );
		self::assertTrue( $provider->is_authentication() );
		self::assertEquals( 'tls', $provider->get_secure() );
		self::assertEquals( 'brevo', $provider->get_name() );
		self::assertEquals( 'custom@example.com', $provider->get_from_email() );
		self::assertEquals( 'Custom Sender', $provider->get_from_name() );
		self::assertEquals( 'custom_user', $provider->get_username() );
		self::assertEquals( 'custom_pass', $provider->get_password() );
	}

	public function testProcessData(): void {
		$data = [
			'from_email'    => 'process@example.com',
			'from_name'     => 'Process Sender',
			'smtp_username' => 'process_user',
			'smtp_password' => 'process_pass',
		];

		$provider = new ConnectorBrevo( [] );
		$provider->process_data( $data );

		self::assertEquals( 'process@example.com', $provider->get_from_email() );
		self::assertEquals( 'Process Sender', $provider->get_from_name() );
		self::assertEquals( 'process_user', $provider->get_username() );
		self::assertEquals( 'process_pass', $provider->get_password() );
	}
}
