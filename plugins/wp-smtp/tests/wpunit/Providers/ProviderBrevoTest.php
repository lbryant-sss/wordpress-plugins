<?php

namespace Providers;

use SolidWP\Mail\Connectors\ConnectorBrevo;

class ProviderBrevoTest extends \lucatume\WPBrowser\TestCase\WPTestCase {

	/**
	 * Test the constructor and default settings.
	 */
	public function testConstructorDefaultSettings() {
		$provider = new ConnectorBrevo();

		$this->assertEquals( 'smtp-relay.brevo.com', $provider->get_host() );
		$this->assertEquals( 587, $provider->get_port() );
		$this->assertTrue( $provider->is_authentication() );
		$this->assertEquals( 'tls', $provider->get_secure() );
		$this->assertEquals( 'brevo', $provider->get_name() );
	}

	/**
	 * Test the constructor with custom data.
	 */
	public function testConstructorWithCustomData() {
		$data = [
			'from_email'    => 'custom@example.com',
			'from_name'     => 'Custom Sender',
			'smtp_username' => 'custom_user',
			'smtp_password' => 'custom_pass',
		];

		$provider = new ConnectorBrevo( $data );

		$this->assertEquals( 'smtp-relay.brevo.com', $provider->get_host() );
		$this->assertEquals( 587, $provider->get_port() );
		$this->assertTrue( $provider->is_authentication() );
		$this->assertEquals( 'tls', $provider->get_secure() );
		$this->assertEquals( 'brevo', $provider->get_name() );
		$this->assertEquals( 'custom@example.com', $provider->get_from_email() );
		$this->assertEquals( 'Custom Sender', $provider->get_from_name() );
		$this->assertEquals( 'custom_user', $provider->get_username() );
		$this->assertEquals( 'custom_pass', $provider->get_password() );
	}

	/**
	 * Test the process_data method.
	 */
	public function testProcessData() {
		$data = [
			'from_email'    => 'process@example.com',
			'from_name'     => 'Process Sender',
			'smtp_username' => 'process_user',
			'smtp_password' => 'process_pass',
		];

		$provider = new ConnectorBrevo( [] );
		$provider->process_data( $data );

		$this->assertEquals( 'process@example.com', $provider->get_from_email() );
		$this->assertEquals( 'Process Sender', $provider->get_from_name() );
		$this->assertEquals( 'process_user', $provider->get_username() );
		$this->assertEquals( 'process_pass', $provider->get_password() );
	}
}
