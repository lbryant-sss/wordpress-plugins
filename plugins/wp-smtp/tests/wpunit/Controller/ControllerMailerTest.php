<?php

namespace Controller;

use SolidWP\Mail\Connectors\ConnectorSMTP;
use SolidWP\Mail\Hooks\PHPMailer;
use SolidWP\Mail\Repository\ProvidersRepository;

class ControllerMailerTest extends \lucatume\WPBrowser\TestCase\WPTestCase {
	/**
	 * @var \WpunitTester
	 */
	protected $tester;

	/**
	 * @var PHPMailer
	 */
	protected $mailerController;

	/**
	 * @var ProvidersRepository
	 */
	protected $repository;

	public function setUp(): void {
		parent::setUp();
		$this->repository       = new ProvidersRepository();
		$this->mailerController = new PHPMailer();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	/**
	 * Test the wp_smtp method to configure PHPMailer for SMTP.
	 */
	public function test_wp_smtp() {
		$repository = new ProvidersRepository();
		$repository->save( new ConnectorSMTP( $this->get_base_post_data() ) );

		reset_phpmailer_instance();
		$ret = wp_mail( 'hoang1231@dasdas.com', 'adasd', 'dasdasd' );
		var_dump( $ret );
		$test = tests_retrieve_phpmailer_instance()->get_sent();
		var_dump( $test );
		die;
	}
}
