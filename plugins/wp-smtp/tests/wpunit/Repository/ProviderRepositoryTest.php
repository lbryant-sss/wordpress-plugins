<?php

namespace wpunit\Repository;

use lucatume\WPBrowser\TestCase\WPTestCase;
use SolidWP\Mail\Connectors\ConnectorBrevo;
use SolidWP\Mail\Connectors\ConnectorMailGun;
use SolidWP\Mail\Connectors\ConnectorSendGrid;
use SolidWP\Mail\Connectors\ConnectorSES;
use SolidWP\Mail\Connectors\ConnectorSMTP;
use SolidWP\Mail\Repository\ProvidersRepository;

class ProviderRepositoryTest extends WPTestCase {

	protected $providers;
	protected $repository;

	public function setUp(): void {
		parent::setUp();
		$this->repository = new ProvidersRepository();
		$this->initializeProviders();
		$this->saveProviders();
	}

	public function tearDown(): void {
		// Clean up after each test.
		delete_option( ProvidersRepository::OPTION_NAME );
		parent::tearDown();
	}

	protected function initializeProviders() {
		$this->providers = [
			new ConnectorSendGrid(
				[
					'id'         => 'provider_sendgrid',
					'name'       => 'sendgrid',
					'from_email' => 'sendgrid@example.com',
					'from_name'  => 'SendGrid Sender',
					'is_active'  => false,
				]
			),
			new ConnectorMailGun(
				[
					'id'         => 'provider_mailgun',
					'name'       => 'mailgun',
					'from_email' => 'mailgun@example.com',
					'from_name'  => 'MailGun Sender',
					'is_active'  => false,
				]
			),
			new ConnectorSES(
				[
					'id'         => 'provider_ses',
					'name'       => 'ses',
					'from_email' => 'ses@example.com',
					'from_name'  => 'SES Sender',
					'is_active'  => false,
				]
			),
			new ConnectorBrevo(
				[
					'id'         => 'provider_brevo',
					'name'       => 'brevo',
					'from_email' => 'brevo@example.com',
					'from_name'  => 'Brevo Sender',
					'is_active'  => false,
				]
			),
			new ConnectorSMTP(
				[
					'id'         => 'provider_smtp',
					'name'       => 'other',
					'from_email' => 'smtp@example.com',
					'from_name'  => 'SMTP Sender',
					'is_active'  => true,
				]
			),
		];
	}

	protected function saveProviders() {
		foreach ( $this->providers as $provider ) {
			$this->repository->save( $provider );
		}
	}

	public function testGetActiveProvider() {
		$activeProvider = $this->repository->get_active_provider();
		$this->assertInstanceOf( ConnectorSMTP::class, $activeProvider );
		$this->assertEquals( 'provider_smtp', $activeProvider->get_id() );
	}

	public function testSave() {
		$savedProviders = get_option( ProvidersRepository::OPTION_NAME, [] );

		foreach ( $this->providers as $provider ) {
			$providerId = $provider->get_id();
			$this->assertArrayHasKey( $providerId, $savedProviders );
			$this->assertEquals( $provider->get_from_email(), $savedProviders[ $providerId ]['from_email'] );
			$this->assertEquals( $provider->get_from_name(), $savedProviders[ $providerId ]['from_name'] );
		}
	}

	public function testGetAllProviders() {
		$retrievedProviders = $this->repository->get_all_providers();

		foreach ( $this->providers as $provider ) {
			$providerId = $provider->get_id();
			$this->assertArrayHasKey( $providerId, $retrievedProviders );
			$this->assertInstanceOf( get_class( $provider ), $retrievedProviders[ $providerId ] );
		}
	}

	public function testGetAllProvidersAsArray() {
		$providersArray = $this->repository->get_all_providers_as_array();

		foreach ( $this->providers as $provider ) {
			$providerId = $provider->get_id();
			$this->assertArrayHasKey( $providerId, $providersArray );
			$this->assertEquals( $provider->get_name(), $providersArray[ $providerId ]['name'] );
			$this->assertEquals( $provider->get_from_email(), $providersArray[ $providerId ]['from_email'] );
			$this->assertEquals( $provider->get_from_name(), $providersArray[ $providerId ]['from_name'] );
		}
	}

	public function testGetProviderById() {
		foreach ( $this->providers as $provider ) {
			$retrievedProvider = $this->repository->get_provider_by_id( $provider->get_id() );
			$this->assertInstanceOf( get_class( $provider ), $retrievedProvider );
			$this->assertEquals( $provider->get_id(), $retrievedProvider->get_id() );
		}
	}

	public function testSetActiveProvider() {
		$this->repository->set_active_provider( 'provider_sendgrid' );

		foreach ( $this->providers as $provider ) {
			$retrievedProvider = $this->repository->get_provider_by_id( $provider->get_id() );
			if ( $provider->get_id() === 'provider_sendgrid' ) {
				$this->assertTrue( $retrievedProvider->is_active() );
			} else {
				$this->assertFalse( $retrievedProvider->is_active() );
			}
		}
	}

	public function testDeleteProviderById() {
		$this->repository->delete_provider_by_id( 'provider_smtp' );

		$providers = get_option( ProvidersRepository::OPTION_NAME, [] );
		$this->assertArrayNotHasKey( 'provider_smtp', $providers );
	}

}
