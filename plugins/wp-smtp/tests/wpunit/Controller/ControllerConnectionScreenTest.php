<?php

namespace wpunit\Controller;

use SolidWP\Mail\AbstractController;
use SolidWP\Mail\Connectors\ConnectorSMTP;
use SolidWP\Mail\Repository\ProvidersRepository;

class ControllerConnectionScreenTest extends \lucatume\WPBrowser\TestCase\WPAjaxTestCase {
	/**
	 * @var \WpunitTester
	 */
	protected $tester;

	protected $providers = [];

	public function setUp(): void {
		// update for prevent the migration script triggered.
		update_option( AbstractController::OPTION_VERSION_NAME, WPSMTP_VERSION );
		parent::setUp();
	}

	public function tearDown(): void {
		delete_option( ProvidersRepository::OPTION_NAME );
		parent::tearDown();
	}

	/**
	 * Returns a base $_POST array with default values.
	 *
	 * @param array $overrides Array of values to override in the base array.
	 *
	 * @return array
	 */
	private function get_base_post_data( array $overrides = [] ): array {
		$base_post = [
			'id'            => '',
			'from_email'    => 'test@wordpress.test',
			'from_name'     => 'Solid Email Test',
			'smtp_host'     => 'smtp.freesmtpservers.com',
			'smtp_port'     => '25',
			'smtp_secure'   => '',
			'smtp_auth'     => 'no',
			'smtp_username' => 'smtp@example.com',
			'smtp_password' => 'smtp_password',
			'name'          => 'other',
			'description'   => '',
			'_solidnonce'   => wp_create_nonce( 'save_connection' ),
		];

		return array_merge( $base_post, $overrides );
	}

	/**
	 * Test the save_connection AJAX handler.
	 */
	/**
	 * Test the save_connection AJAX handler for successful save.
	 */
	public function test_save_connection_success() {
		// Become an administrator.
		$this->_setRole( 'administrator' );

		// Simulate $_POST data for a successful save.
		$_POST = $this->get_base_post_data();

		// Make the request.
		try {
			$this->_handleAjax( 'solid_email_save_connection' );
		} catch ( \WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		// Verify the response for successful save.
		$response = json_decode( $this->_last_response, true );

		$this->assertArrayHasKey( 'success', $response );
		$this->assertTrue( $response['success'] );
		$this->assertArrayHasKey( 'data', $response );
		$this->assertNotEmpty( $response['data'] );
		$this->assertEquals( 1, count( $response['data'] ) );

		// Check the returned data structure.
		$savedConnection = reset( $response['data'] );
		$this->assertArrayHasKey( 'id', $savedConnection );
		$this->assertArrayHasKey( 'name', $savedConnection );
		$this->assertEquals( 'other', $savedConnection['name'] );
		$this->assertArrayHasKey( 'from_email', $savedConnection );
		$this->assertEquals( 'test@wordpress.test', $savedConnection['from_email'] );
		$this->assertArrayHasKey( 'from_name', $savedConnection );
		$this->assertEquals( 'Solid Email Test', $savedConnection['from_name'] );
		$this->assertArrayHasKey( 'smtp_host', $savedConnection );
		$this->assertEquals( 'smtp.freesmtpservers.com', $savedConnection['smtp_host'] );
		$this->assertArrayHasKey( 'smtp_port', $savedConnection );
		$this->assertEquals( 25, $savedConnection['smtp_port'] );
		$this->assertArrayHasKey( 'smtp_auth', $savedConnection );
		$this->assertEquals( 'no', $savedConnection['smtp_auth'] );
		$this->assertArrayHasKey( 'smtp_username', $savedConnection );
		$this->assertEquals( 'smtp@example.com', $savedConnection['smtp_username'] );
		$this->assertArrayHasKey( 'smtp_password', $savedConnection );
		$this->assertEquals( 'smtp_password', $savedConnection['smtp_password'] );

		delete_option( ProvidersRepository::OPTION_NAME );
	}

	/**
	 * Test the save_connection AJAX handler for missing from_email.
	 */
	public function test_save_connection_missing_from_email() {
		// Become an administrator.
		$this->_setRole( 'administrator' );

		// Simulate $_POST data with missing from_email.
		$_POST = $this->get_base_post_data( [ 'from_email' => '' ] );

		// Make the request.
		try {
			$this->_handleAjax( 'solid_email_save_connection' );
		} catch ( \WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		// Verify the response for validation error.
		$response = json_decode( $this->_last_response, true );
		$this->assertArrayHasKey( 'success', $response );
		$this->assertFalse( $response['success'] );
		$this->assertEquals( 'From Email is not a valid email address', $response['data']['from_email'] );
	}

	/**
	 * Test the save_connection AJAX handler for invalid from_email.
	 */
	public function test_save_connection_invalid_from_email() {
		// Become an administrator.
		$this->_setRole( 'administrator' );

		// Simulate $_POST data with invalid from_email.
		$_POST = $this->get_base_post_data( [ 'from_email' => 'invalid-email' ] );

		// Make the request.
		try {
			$this->_handleAjax( 'solid_email_save_connection' );
		} catch ( \WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		// Verify the response for validation error.
		$response = json_decode( $this->_last_response, true );
		$this->assertArrayHasKey( 'success', $response );
		$this->assertFalse( $response['success'] );
		$this->assertEquals( 'From Email is not a valid email address', $response['data']['from_email'] );
	}

	/**
	 * Test the save_connection AJAX handler for missing from_name.
	 */
	public function test_save_connection_missing_from_name() {
		// Become an administrator.
		$this->_setRole( 'administrator' );

		// Simulate $_POST data with missing from_name.
		$_POST = $this->get_base_post_data( [ 'from_name' => '' ] );

		// Make the request.
		try {
			$this->_handleAjax( 'solid_email_save_connection' );
		} catch ( \WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		// Verify the response for validation error.
		$response = json_decode( $this->_last_response, true );
		$this->assertArrayHasKey( 'success', $response );
		$this->assertFalse( $response['success'] );
		$this->assertEquals( 'From Name is required', $response['data']['from_name'] );
	}

	/**
	 * Test the save_connection AJAX handler for missing smtp_host.
	 */
	public function test_save_connection_missing_smtp_host() {
		// Become an administrator.
		$this->_setRole( 'administrator' );

		// Simulate $_POST data with missing smtp_host.
		$_POST = $this->get_base_post_data( [ 'smtp_host' => '' ] );

		// Make the request.
		try {
			$this->_handleAjax( 'solid_email_save_connection' );
		} catch ( \WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		// Verify the response for validation error.
		$response = json_decode( $this->_last_response, true );
		$this->assertArrayHasKey( 'success', $response );
		$this->assertFalse( $response['success'] );
		$this->assertEquals( 'SMTP Host is required', $response['data']['smtp_host'] );
	}

	/**
	 * Test the save_connection AJAX handler for missing smtp_username.
	 */
	public function test_save_connection_missing_smtp_username() {
		// Become an administrator.
		$this->_setRole( 'administrator' );

		// Simulate $_POST data with missing smtp_username.
		$_POST = $this->get_base_post_data( [ 'smtp_username' => '' ] );

		// Make the request.
		try {
			$this->_handleAjax( 'solid_email_save_connection' );
		} catch ( \WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		// Verify the response for validation error.
		$response = json_decode( $this->_last_response, true );
		$this->assertArrayHasKey( 'success', $response );
		$this->assertFalse( $response['success'] );
		$this->assertEquals( 'SMTP Username is required', $response['data']['smtp_username'] );
	}

	/**
	 * Test the save_connection AJAX handler for missing smtp_password.
	 */
	public function test_save_connection_missing_smtp_password() {
		// Become an administrator.
		$this->_setRole( 'administrator' );

		// Simulate $_POST data with missing smtp_password.
		$_POST = $this->get_base_post_data( [ 'smtp_password' => '' ] );

		// Make the request.
		try {
			$this->_handleAjax( 'solid_email_save_connection' );
		} catch ( \WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		// Verify the response for validation error.
		$response = json_decode( $this->_last_response, true );
		$this->assertArrayHasKey( 'success', $response );
		$this->assertFalse( $response['success'] );
		$this->assertEquals( 'SMTP Password is required', $response['data']['smtp_password'] );
	}

	/**
	 * Test the save_connection AJAX handler for updating an existing connection.
	 */
	public function test_update_connection() {
		// Create something first.
		$repository = new ProvidersRepository();

		$repository->save( new ConnectorSMTP( $this->get_base_post_data() ) );

		$providers = $repository->get_all_providers();
		$created   = array_shift( $providers );

		// Become an administrator.
		$this->_setRole( 'administrator' );

		// Simulate $_POST data for updating a connection.
		$_POST = $this->get_base_post_data(
			[
				'id'            => $created->get_id(),
				'from_email'    => 'updated@wordpress.test',
				'from_name'     => 'Updated Solid Email Test',
				'smtp_host'     => 'updated.smtp.freesmtpservers.com',
				'smtp_port'     => '587',
				'smtp_secure'   => 'tls',
				'smtp_auth'     => 'yes',
				'smtp_username' => 'updated.smtp@example.com',
				'smtp_password' => 'updated_smtp_password',
				'name'          => 'other',
				'description'   => '',
			]
		);

		// Make the request.
		try {
			$this->_handleAjax( 'solid_email_save_connection' );
		} catch ( \WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		// Verify the response for successful update.
		$response = json_decode( $this->_last_response, true );
		$this->assertArrayHasKey( 'success', $response );
		$this->assertTrue( $response['success'] );
		$this->assertArrayHasKey( 'data', $response );

		// Check the returned data structure.
		$updatedConnection = reset( $response['data'] );
		$this->assertArrayHasKey( 'id', $updatedConnection );
		$this->assertEquals( $created->get_id(), $updatedConnection['id'] );
		$this->assertArrayHasKey( 'name', $updatedConnection );
		$this->assertEquals( 'other', $updatedConnection['name'] );
		$this->assertArrayHasKey( 'from_email', $updatedConnection );
		$this->assertEquals( 'updated@wordpress.test', $updatedConnection['from_email'] );
		$this->assertArrayHasKey( 'from_name', $updatedConnection );
		$this->assertEquals( 'Updated Solid Email Test', $updatedConnection['from_name'] );
		$this->assertArrayHasKey( 'smtp_host', $updatedConnection );
		$this->assertEquals( 'updated.smtp.freesmtpservers.com', $updatedConnection['smtp_host'] );
		$this->assertArrayHasKey( 'smtp_port', $updatedConnection );
		$this->assertEquals( 587, $updatedConnection['smtp_port'] );
		$this->assertArrayHasKey( 'smtp_auth', $updatedConnection );
		$this->assertEquals( 'yes', $updatedConnection['smtp_auth'] );
		$this->assertArrayHasKey( 'smtp_username', $updatedConnection );
		$this->assertEquals( 'updated.smtp@example.com', $updatedConnection['smtp_username'] );
		$this->assertArrayHasKey( 'smtp_password', $updatedConnection );
		$this->assertEquals( 'updated_smtp_password', $updatedConnection['smtp_password'] );
		$this->assertArrayHasKey( 'smtp_secure', $updatedConnection );
		$this->assertEquals( 'tls', $updatedConnection['smtp_secure'] );
	}
}
