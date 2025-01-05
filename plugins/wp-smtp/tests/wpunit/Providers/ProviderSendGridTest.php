<?php

namespace Providers;

use lucatume\WPBrowser\TestCase\WPTestCase;
use SolidWP\Mail\Connectors\ConnectorSendGrid;

class ProviderSendGridTest extends WPTestCase {

	/**
	 * Test the constructor and default settings.
	 */
	public function testConstructorDefaultSettings() {
		$provider = new ConnectorSendGrid();

		$this->assertEquals( 'smtp.sendgrid.net', $provider->get_host() );
		$this->assertEquals( 587, $provider->get_port() );
		$this->assertTrue( $provider->is_authentication() );
		$this->assertEquals( 'tls', $provider->get_secure() );
		$this->assertEquals( 'sendgrid', $provider->get_name() );
		$this->assertEquals( 'apikey', $provider->get_username() );
	}

	/**
	 * Test the constructor with custom data.
	 */
	public function testConstructorWithCustomData() {
		$data = [
			'from_email'    => 'custom@example.com',
			'from_name'     => 'Custom Sender',
			'smtp_password' => 'custom_pass',
		];

		$provider = new ConnectorSendGrid( $data );

		$this->assertEquals( 'smtp.sendgrid.net', $provider->get_host() );
		$this->assertEquals( 587, $provider->get_port() );
		$this->assertTrue( $provider->is_authentication() );
		$this->assertEquals( 'tls', $provider->get_secure() );
		$this->assertEquals( 'sendgrid', $provider->get_name() );
		$this->assertEquals( 'apikey', $provider->get_username() );
		$this->assertEquals( 'custom@example.com', $provider->get_from_email() );
		$this->assertEquals( 'Custom Sender', $provider->get_from_name() );
		$this->assertEquals( 'custom_pass', $provider->get_password() );
	}

	/**
	 * Test the process_data method.
	 */
	public function testProcessData() {
		$data = [
			'from_email'    => 'process@example.com',
			'from_name'     => 'Process Sender',
			'smtp_password' => 'process_pass',
		];

		$provider = new ConnectorSendGrid( [] );
		$provider->process_data( $data );

		$this->assertEquals( 'process@example.com', $provider->get_from_email() );
		$this->assertEquals( 'Process Sender', $provider->get_from_name() );
		$this->assertEquals( 'process_pass', $provider->get_password() );
	}
}
