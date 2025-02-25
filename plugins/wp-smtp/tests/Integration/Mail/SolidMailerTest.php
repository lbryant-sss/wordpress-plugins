<?php

namespace Integration\Mail;

use lucatume\WPBrowser\TestCase\WPTestCase;
use SolidWP\Mail\Connectors\ConnectorSMTP;
use SolidWP\Mail\Repository\ProvidersRepository;
use SolidWP\Mail\SolidMailer;

class SolidMailerTest extends WPTestCase {

	public function testPhpmailerReplacement(): void {
		$repository = new ProvidersRepository();
		$repository->save(
			new ConnectorSMTP(
				[
					'name'       => 'other',
					'is_active'  => true,
					'from_email' => 'solid@solidwp.com',
					'from_name'  => 'SolidWP',
					'smtp_host'  => 'localhost',
				]
			)
		);

		wp_mail( 'test@test.com', 'Subject', 'Test' );
		/** @var SolidMailer $php_mailer */
		$php_mailer = tests_retrieve_phpmailer_instance();
		$this->assertInstanceOf( SolidMailer::class, $php_mailer );
		$this->assertEquals( 'localhost', $php_mailer->Host );
		$this->assertEquals( 'solid@solidwp.com', $php_mailer->From );
		$this->assertEquals( 'SolidWP', $php_mailer->FromName );
	}
}
