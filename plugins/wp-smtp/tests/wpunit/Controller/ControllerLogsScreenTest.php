<?php

use lucatume\WPBrowser\TestCase\WPAjaxTestCase;
use SolidWP\Mail\Admin\LogsScreen;
use SolidWP\Mail\Repository\LogsRepository;
use SolidWP\Mail\App;

class LogsScreenTest extends WPAjaxTestCase {

	protected $repository;
	protected $logsScreen;

	public function setUp(): void {
		parent::setUp();
		$this->repository = new LogsRepository();
		$this->logsScreen = new LogsScreen();
		App::container()->setVar( 'LOG_LIMIT', 2 );
		$this->populateTestData();
	}

	public function tearDown(): void {
		$this->clearTestData();
		parent::tearDown();
	}

	protected function populateTestData() {
		global $wpdb;

		// Insert multiple test data entries into the wp_wpsmtp_logs table
		for ( $i = 1; $i <= 5; $i ++ ) {
			$wpdb->insert(
				$wpdb->prefix . 'wpsmtp_logs',
				[
					'timestamp' => date( 'Y-m-d H:i:s', strtotime( "2023-01-01 00:00:0{$i}" ) ),
					'to'        => "test{$i}@example.com",
					'subject'   => "Test Subject {$i}",
					'message'   => "Test Message {$i}",
					'headers'   => "Header {$i}",
					'error'     => "Error {$i}",
				]
			);
		}
	}

	protected function clearTestData() {
		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->prefix}wpsmtp_logs" );
	}

	public function testLogsFetch() {
		$this->_setRole( 'administrator' );

		$_POST = [
			'action'                  => 'solidwp_mail_logs_fetch',
			'page'                    => 0,
			'sortby'                  => 'timestamp',
			'sort'                    => 'desc',
			'solidwp-mail-logs-nonce' => wp_create_nonce( 'fetch_logs' ),
		];

		try {
			$this->_handleAjax( 'solidwp_mail_logs_fetch' );
		} catch ( \WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		$response = json_decode( $this->_last_response, true );
		$this->assertArrayHasKey( 'success', $response );
		$this->assertTrue( $response['success'] );
		$this->assertArrayHasKey( 'data', $response );
		$this->assertArrayHasKey( 'logs', $response['data'] );
		$this->assertCount( 2, $response['data']['logs'] );
		$this->assertEquals( 3, $response['data']['total_pages'] );
		$this->assertEquals( 5, $response['data']['total'] );
		$this->assertEquals( 'Test Subject 5', $response['data']['logs'][0]['subject'] );
		$this->assertEquals( 'Test Subject 4', $response['data']['logs'][1]['subject'] );
	}

	public function testLogsSearch() {
		$this->_setRole( 'administrator' );

		$_POST = [
			'action'                  => 'solidwp_mail_logs_search',
			'term'                    => 'Test Subject 1',
			'solidwp-mail-logs-nonce' => wp_create_nonce( 'search_logs' ),
		];

		try {
			$this->_handleAjax( 'solidwp_mail_logs_search' );
		} catch ( \WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		$response = json_decode( $this->_last_response, true );

		$this->assertArrayHasKey( 'success', $response );
		$this->assertTrue( $response['success'] );
		$this->assertArrayHasKey( 'data', $response );
		$this->assertArrayHasKey( 'logs', $response['data'] );
		$this->assertCount( 1, $response['data']['logs'] );
		$this->assertEquals( 'Test Subject 1', $response['data']['logs'][0]['subject'] );
	}

	public function testLogsDelete() {
		$this->_setRole( 'administrator' );

		global $wpdb;
		$log_id = $wpdb->get_var( "SELECT mail_id FROM {$wpdb->prefix}wpsmtp_logs WHERE subject = 'Test Subject 1'" );

		$_POST = [
			'action'                  => 'solidwp_mail_logs_delete',
			'logIds'                  => [ $log_id ],
			'solidwp-mail-logs-nonce' => wp_create_nonce( 'delete_logs' ),
		];

		try {
			$this->_handleAjax( 'solidwp_mail_logs_delete' );
		} catch ( \WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		$response = json_decode( $this->_last_response, true );

		$this->assertArrayHasKey( 'success', $response );
		$this->assertTrue( $response['success'] );
		$this->assertArrayHasKey( 'data', $response );
		$this->assertEquals( 'Selected logs deleted successfully.', $response['data']['message'] );

		// Verify log is deleted
		$count = $this->repository->count_all_logs();
		$this->assertEquals( 4, $count );
	}
}
