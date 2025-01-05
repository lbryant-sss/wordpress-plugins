<?php

namespace Providers;

use SolidWP\Mail\Connectors\ConnectorMailGun;

class ProviderMailGunTest extends \lucatume\WPBrowser\TestCase\WPTestCase {

	/**
	 * Test the constructor and default settings.
	 */
	public function testConstructorDefaultSettings() {
		$provider = new ConnectorMailGun();

		$this->assertEquals( 'smtp.mailgun.org', $provider->get_host() );
		$this->assertEquals( 587, $provider->get_port() );
		$this->assertTrue( $provider->is_authentication() );
		$this->assertEquals( 'tls', $provider->get_secure() );
		$this->assertEquals( 'mailgun', $provider->get_name() );
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

		$provider = new ConnectorMailGun( $data );

		$this->assertEquals( 'smtp.mailgun.org', $provider->get_host() );
		$this->assertEquals( 587, $provider->get_port() );
		$this->assertTrue( $provider->is_authentication() );
		$this->assertEquals( 'tls', $provider->get_secure() );
		$this->assertEquals( 'mailgun', $provider->get_name() );
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

		$provider = new ConnectorMailGun( [] );
		$provider->process_data( $data );

		$this->assertEquals( 'process@example.com', $provider->get_from_email() );
		$this->assertEquals( 'Process Sender', $provider->get_from_name() );
		$this->assertEquals( 'process_user', $provider->get_username() );
		$this->assertEquals( 'process_pass', $provider->get_password() );
	}
}
