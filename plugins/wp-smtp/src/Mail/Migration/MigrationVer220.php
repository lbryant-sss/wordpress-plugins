<?php

namespace SolidWP\Mail\Migration;

use SolidWP\Mail\AbstractController;
use SolidWP\Mail\Repository\ProvidersRepository;
use WPSMTP\Logger\Table;

/**
 * Class MigrationVer220
 *
 * Migration for version 2.2.0 to add the 'is_default' property to all providers.
 * The currently active provider will be set as default.
 *
 * @package SolidWP\Mail\Migration
 */
class MigrationVer220 extends AbstractController {

	/**
	 * The repository for managing SMTP mailers.
	 *
	 * @var ProvidersRepository
	 */
	protected ProvidersRepository $providers_repository;

	/**
	 * Constructor for MigrationVer220.
	 *
	 * Initializes the migration class and sets up dependencies.
	 *
	 * @param ProvidersRepository $providers_repository The repository instance for managing providers.
	 */
	public function __construct( ProvidersRepository $providers_repository ) {
		$this->providers_repository = $providers_repository;
	}

	/**
	 * Registers hooks for the migration.
	 *
	 * Hooks into the 'wp_loaded' action to run the migration logic after WordPress is loaded.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'wp_loaded', [ $this, 'migration' ], 20 );
	}

	/**
	 * Migration logic for version 2.2.0.
	 *
	 * Adds 'is_default' property to all existing providers.
	 * Sets is_default to true for the currently active provider, false for all others.
	 *
	 * @return void
	 */
	public function migration(): void {
		global $wpdb;

		$version = get_option( self::OPTION_VERSION_NAME, '' );

		if ( version_compare( $version, '2.2.0', '>=' ) ) {
			return;
		}

		$providers_data = get_option( ProvidersRepository::OPTION_NAME, [] );

		$active_provider    = current( $this->providers_repository->get_active_providers() );
		$active_provider_id = $active_provider ? $active_provider->get_id() : null;

		foreach ( $providers_data as $provider_id => &$provider_data ) {
			// Set is_default to true only for the currently active provider
			$provider_data['is_default'] = ( $provider_id === $active_provider_id );
		}
		unset( $provider_data );

		update_option( ProvidersRepository::OPTION_NAME, $providers_data );

		// Upgrade logs table
		Table::install();

		$logsTableName = Table::$name;

		// phpcs:ignore WordPress.DB
		$wpdb->query( "UPDATE $logsTableName SET content_type = 'text/html' WHERE headers LIKE '%html%'" );

		update_option( self::OPTION_VERSION_NAME, WPSMTP_VERSION );
	}
}
