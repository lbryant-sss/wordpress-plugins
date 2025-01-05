<?php

namespace Providers;

use SolidWP\Mail\Connectors\ConnectorSMTP;

class ProviderSMTPTest extends \lucatume\WPBrowser\TestCase\WPTestCase {
	/**
	 * @var \WpunitTester
	 */
	protected $tester;

	/**
	 * Test the constructor and data processing.
	 */
	public function testConstructorAndProcessData() {
		$data = [
			'name'          => 'Test Provider',
			'description'   => 'A test SMTP provider',
			'from_email'    => 'test@example.com',
			'from_name'     => 'Test Sender',
			'smtp_host'     => 'smtp.example.com',
			'smtp_port'     => '587',
			'smtp_auth'     => 'yes',
			'smtp_username' => 'user',
			'smtp_password' => 'pass',
			'disable_logs'  => true,
			'smtp_secure'   => 'tls',
			'is_active'     => true,
		];

		$provider = new ConnectorSMTP( $data );

		$this->assertEquals( 'Test Provider', $provider->get_name() );
		$this->assertEquals( 'test@example.com', $provider->get_from_email() );
		$this->assertEquals( 'Test Sender', $provider->get_from_name() );
		$this->assertEquals( 'smtp.example.com', $provider->get_host() );
		$this->assertEquals( '587', $provider->get_port() );
		$this->assertTrue( $provider->is_authentication() );
		$this->assertEquals( 'user', $provider->get_username() );
		$this->assertEquals( 'pass', $provider->get_password() );
		$this->assertTrue( $provider->get_disable_logs() );
		$this->assertEquals( 'tls', $provider->get_secure() );
		$this->assertTrue( $provider->is_active() );
	}

	public function testConstructorAndProcessWithFalseData() {
		$data = [
			'name'          => 'Test Provider',
			'description'   => 'A test SMTP provider',
			'from_email'    => 'test@example.com',
			'from_name'     => 'Test Sender',
			'smtp_host'     => 'smtp.example.com',
			'smtp_port'     => '587',
			'smtp_auth'     => 'no',
			'smtp_username' => 'user',
			'smtp_password' => 'pass',
			'disable_logs'  => false,
			'smtp_secure'   => 'tls',
			'is_active'     => false,
		];

		$provider = new ConnectorSMTP( $data );

		$this->assertEquals( 'Test Provider', $provider->get_name() );
		$this->assertEquals( 'test@example.com', $provider->get_from_email() );
		$this->assertEquals( 'Test Sender', $provider->get_from_name() );
		$this->assertEquals( 'smtp.example.com', $provider->get_host() );
		$this->assertEquals( '587', $provider->get_port() );
		$this->assertFalse( $provider->is_authentication() );
		$this->assertEquals( 'user', $provider->get_username() );
		$this->assertEquals( 'pass', $provider->get_password() );
		$this->assertFalse( $provider->get_disable_logs() );
		$this->assertEquals( 'tls', $provider->get_secure() );
		$this->assertFalse( $provider->is_active() );
	}

	/**
	 * Test the validation method.
	 */
	public function testValidation() {
		$data = [
			'from_email'    => 'invalid-email',
			'from_name'     => 'Test Sender',
			'smtp_host'     => 'smtp.example.com',
			'smtp_port'     => '587',
			'smtp_username' => 'user',
			'smtp_password' => 'pass',
		];

		$provider = new ConnectorSMTP( $data );
		$is_valid = $provider->validation();

		$this->assertFalse( $is_valid );
		$errors = $provider->get_errors();
		$this->assertArrayHasKey( 'from_email', $errors );
	}

	/**
	 * Test the to_array method.
	 */
	public function testToArray() {
		$data = [
			'id'            => 'unique_id',
			'name'          => 'Test Provider',
			'description'   => '',
			'from_email'    => 'test@example.com',
			'from_name'     => 'Test Sender',
			'smtp_host'     => 'smtp.example.com',
			'smtp_port'     => '587',
			'smtp_auth'     => 'yes',
			'smtp_username' => 'user',
			'smtp_password' => 'pass',
			'disable_logs'  => true,
			'smtp_secure'   => 'tls',
			'is_active'     => true,
		];

		$provider = new ConnectorSMTP( $data );
		$array    = $provider->to_array();

		$this->assertEquals( $data['id'], $array['id'] );
		$this->assertEquals( $data['name'], $array['name'] );
		$this->assertEquals( $data['description'], $array['description'] );
		$this->assertEquals( $data['from_email'], $array['from_email'] );
		$this->assertEquals( $data['from_name'], $array['from_name'] );
		$this->assertEquals( $data['smtp_host'], $array['smtp_host'] );
		$this->assertEquals( $data['smtp_port'], $array['smtp_port'] );
		$this->assertEquals( $data['smtp_auth'], $array['smtp_auth'] );
		$this->assertEquals( $data['smtp_username'], $array['smtp_username'] );
		$this->assertEquals( $data['smtp_password'], $array['smtp_password'] );
		$this->assertEquals( $data['disable_logs'], $array['disable_logs'] );
		$this->assertEquals( $data['smtp_secure'], $array['smtp_secure'] );
		$this->assertEquals( $data['is_active'], $array['is_active'] );
	}

	/**
	 * Test edge cases for process_data method.
	 */
	public function testProcessDataEdgeCases() {
		$data = [
			'name'          => '',
			'description'   => '',
			'from_email'    => '',
			'from_name'     => '',
			'smtp_host'     => '',
			'smtp_port'     => '',
			'smtp_auth'     => '',
			'smtp_username' => '',
			'smtp_password' => '',
			'disable_logs'  => false,
			'smtp_secure'   => '',
			'is_active'     => false,
		];

		$provider = new ConnectorSMTP( $data );

		$this->assertEquals( '', $provider->get_name() );
		$this->assertEquals( '', $provider->get_from_email() );
		$this->assertEquals( '', $provider->get_from_name() );
		$this->assertEquals( '', $provider->get_host() );
		$this->assertEquals( '', $provider->get_port() );
		$this->assertFalse( $provider->is_authentication() );
		$this->assertEquals( '', $provider->get_username() );
		$this->assertEquals( '', $provider->get_password() );
		$this->assertFalse( $provider->get_disable_logs() );
		$this->assertEquals( '', $provider->get_secure() );
		$this->assertFalse( $provider->is_active() );
	}
}
