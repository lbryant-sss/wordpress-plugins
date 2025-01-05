<?php

namespace wpunit\Repository;

use lucatume\WPBrowser\TestCase\WPTestCase;
use SolidWP\Mail\Repository\LogsRepository;
use SolidWP\Mail\App;

class LogsRepositoryTest extends WPTestCase {

	protected $repository;

	public function setUp(): void {
		parent::setUp();
		$this->repository = new LogsRepository();
		App::container()->setVar( 'LOG_LIMIT', 2 ); // Set the log limit for pagination tests
		$this->populateTestData();
	}

	public function tearDown(): void {
		$this->clearTestData();
		parent::tearDown();
	}

	protected function populateTestData() {
		global $wpdb;
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

	public function testGetEmailLogs() {
		// Test default sorting (by timestamp DESC) with pagination
		$logs = $this->repository->get_email_logs( 0, 'timestamp', 'desc' );

		$this->assertCount( 2, $logs ); // LOG_LIMIT is set to 2
		$this->assertEquals( 'Test Subject 5', $logs[0]['subject'] );
		$this->assertEquals( 'Test Subject 4', $logs[1]['subject'] );

		// Test sorting by timestamp ASC with pagination
		$logs = $this->repository->get_email_logs( 0, 'timestamp', 'asc' );

		$this->assertCount( 2, $logs ); // LOG_LIMIT is set to 2
		$this->assertEquals( 'Test Subject 1', $logs[0]['subject'] );
		$this->assertEquals( 'Test Subject 2', $logs[1]['subject'] );
	}

	public function testPaging() {
		// Test first page
		$logs = $this->repository->get_email_logs( 0, 'timestamp', 'desc' );
		$this->assertCount( 2, $logs );
		$this->assertEquals( 'Test Subject 5', $logs[0]['subject'] );
		$this->assertEquals( 'Test Subject 4', $logs[1]['subject'] );

		// Test second page
		$logs = $this->repository->get_email_logs( 1, 'timestamp', 'desc' );
		$this->assertCount( 2, $logs );
		$this->assertEquals( 'Test Subject 3', $logs[0]['subject'] );
		$this->assertEquals( 'Test Subject 2', $logs[1]['subject'] );

		// Test third page
		$logs = $this->repository->get_email_logs( 2, 'timestamp', 'desc' );
		$this->assertCount( 1, $logs );
		$this->assertEquals( 'Test Subject 1', $logs[0]['subject'] );
	}

	public function testSearch() {
		$logs = $this->repository->search( 'Test Subject 1' );

		$this->assertCount( 1, $logs );
		$this->assertEquals( 'Test Subject 1', $logs[0]['subject'] );
	}


	public function testDeleteLog() {
		global $wpdb;

		$log_id  = $wpdb->get_var( "SELECT mail_id FROM {$wpdb->prefix}wpsmtp_logs WHERE subject = 'Test Subject 1'" );
		$deleted = $this->repository->delete_log( (int) $log_id );

		$this->assertTrue( $deleted );
		$total_logs = $this->repository->count_all_logs();
		$this->assertEquals( 4, $total_logs );
	}

	public function testCountAllLogs() {
		$total_logs = $this->repository->count_all_logs();

		$this->assertEquals( 5, $total_logs );
	}
}
