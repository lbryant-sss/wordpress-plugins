<?php

namespace SolidWP\Mail\Repository;

use SolidWP\Mail\Connectors\ConnectorBrevo;
use SolidWP\Mail\Connectors\ConnectorMailGun;
use SolidWP\Mail\Connectors\ConnectorPostmark;
use SolidWP\Mail\Connectors\ConnectorSendGrid;
use SolidWP\Mail\Connectors\ConnectorSES;
use SolidWP\Mail\Connectors\ConnectorSMTP;

/**
 * Class ProvidersRepository
 *
 * This class handles database-related tasks for managing SMTP providers within the Solid_SMTP plugin.
 * It provides CRUD operations and other database-related functionalities for SMTP providers.
 *
 * @package Solid_SMTP\Repository
 */
class ProvidersRepository {
	const OPTION_NAME = 'solid_smtp_providers';

	/**
	 * Retrieves the active SMTP provider.
	 *
	 * @return ConnectorSMTP|null The active SMTP provider configuration, or null if not found.
	 */
	public function get_active_provider(): ?ConnectorSMTP {
		$providers = $this->get_all_providers();

		foreach ( $providers as $provider ) {
			if ( $provider->is_active() ) {
				return $provider;
			}
		}

		return null;
	}

	/**
	 * Saves a provider configuration.
	 *
	 * @param ConnectorSMTP $provider The SMTP provider configuration to save.
	 *
	 * @return void
	 */
	public function save( ConnectorSMTP $provider ) {
		$providers = get_option( self::OPTION_NAME, [] );

		$providers[ $provider->get_id() ] = $provider->to_array();

		update_option( self::OPTION_NAME, $providers );
	}

	/**
	 * Retrieves all SMTP provider configurations.
	 *
	 * @return ConnectorSMTP[] An array of SMTP provider configurations.
	 */
	public function get_all_providers(): array {
		$providers = get_option( self::OPTION_NAME, [] );
		$data      = [];
		foreach ( $providers as $provider ) {
			$data[ $provider['id'] ] = $this->factory( $provider['name'], $provider );
		}

		return $data;
	}

	/**
	 * Retrieves all SMTP provider configurations as an array of arrays.
	 *
	 * @return array An array of SMTP provider configurations.
	 */
	public function get_all_providers_as_array(): array {
		$providers = $this->get_all_providers();
		$data      = [];

		foreach ( $providers as $provider ) {
			$data[ $provider->get_id() ] = $provider->to_array();
		}

		return $data;
	}

	/**
	 * Retrieves an SMTP provider configuration by its ID.
	 *
	 * @param string $provider_id The ID of the SMTP provider to retrieve.
	 *
	 * @return ConnectorSMTP|null The SMTP provider configuration, or null if not found.
	 */
	public function get_provider_by_id( string $provider_id ): ?ConnectorSMTP {
		$providers = $this->get_all_providers();

		return $providers[ $provider_id ] ?? null;
	}

	/**
	 * Sets a specific SMTP provider as active by its ID.
	 *
	 * @param string $provider_id The ID of the SMTP provider to set as active.
	 *
	 * @return void
	 */
	public function set_active_provider( string $provider_id ): void {
		$providers = $this->get_all_providers();
		foreach ( $providers as $provider ) {
			$provider->set_is_active( $provider_id === $provider->get_id() );
			// though it not performance wise, but it safer.
			$this->save( $provider );
		}
	}

	/**
	 * Deletes a specific SMTP provider by its ID.
	 *
	 * @param string $provider_id The ID of the SMTP provider to delete.
	 *
	 * @return void
	 */
	public function delete_provider_by_id( string $provider_id ): void {
		$providers = $this->get_all_providers_as_array();
		if ( isset( $providers[ $provider_id ] ) ) {
			unset( $providers[ $provider_id ] );
		}
		update_option( self::OPTION_NAME, $providers );
	}

	/**
	 * Creates an instance of a specific SMTP provider based on the provided type.
	 *
	 * @param string $provider_type The type of SMTP provider to create.
	 * @param array  $data The data of the provider.
	 *
	 * @return ?ConnectorSMTP An instance of the specified SMTP provider.
	 */
	public function factory( string $provider_type, array $data = [] ) {
		switch ( $provider_type ) {
			case 'mailgun':
				return new ConnectorMailGun( $data );
			case 'brevo':
				return new ConnectorBrevo( $data );
			case 'sendgrid':
				return new ConnectorSendGrid( $data );
			case 'amazon_ses':
				return new ConnectorSES( $data );
			case 'postmark':
				return new ConnectorPostmark( $data );
			case 'other':
				return new ConnectorSMTP( $data );
			default:
				return null;
		}
	}
}
