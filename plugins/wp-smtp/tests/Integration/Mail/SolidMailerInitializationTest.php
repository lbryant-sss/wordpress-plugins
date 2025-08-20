<?php

namespace Integration\Mail;

use IntegrationTester;
use lucatume\WPBrowser\TestCase\WPTestCase;
use SolidWP\Mail\SolidMailer;

/**
 * @property IntegrationTester $tester
 */
class SolidMailerInitializationTest extends WPTestCase {

	public function setUp(): void {
		parent::setUp();
		reset_phpmailer_instance();
	}

	public function testPhpmailerReplacement(): void {
		$this->tester->haveSuccessfulConnection(
			[
				'is_active'  => true,
				'is_default' => true,
				'from_email' => 'solid@solidwp.com',
				'from_name'  => 'SolidWP',
			]
		);

		wp_mail( 'test@test.com', 'Subject', 'Test' );
		/** @var SolidMailer $php_mailer */
		$php_mailer = tests_retrieve_phpmailer_instance();
		$this->assertInstanceOf( SolidMailer::class, $php_mailer );
		$this->assertEquals( 'solid@solidwp.com', $php_mailer->From );
		$this->assertEquals( 'SolidWP', $php_mailer->FromName );
	}

	public function testNoConnectionDetected(): void {
		$this->tester->haveSuccessfulConnection(
			[
				'is_active'  => false,
				'is_default' => false,
				'from_email' => 'solid@solidwp.com',
				'from_name'  => 'SolidWP',
			]
		);

		wp_mail( 'test@test.com', 'Subject', 'Test' );
		$php_mailer = tests_retrieve_phpmailer_instance();
		
		$this->assertNotInstanceOf( SolidMailer::class, $php_mailer );
	}
}
