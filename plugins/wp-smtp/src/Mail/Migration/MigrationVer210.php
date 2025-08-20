<?php

namespace SolidWP\Mail\Migration;

use SolidWP\Mail\AbstractController;
use SolidWP\Mail\Repository\ProvidersRepository;
use SolidWP\Mail\Service\ConnectionService;

/**
 * Class MigrationVer210
 *
 * @package SolidWP\Mail\Migration
 */
class MigrationVer210 extends AbstractController {
	const FLAG_NAME = 'solid_mail_maybe_show_200210_notice';

	/**
	 * The repository for managing SMTP mailers.
	 *
	 * @var ProvidersRepository
	 */
	protected ProvidersRepository $providers_repository;

	/**
	 * The service class.
	 *
	 * @var ConnectionService
	 */
	protected ConnectionService $service;

	/**
	 * Constructor for MigrationVer210.
	 *
	 * Initializes the migration class and sets up dependencies.
	 *
	 * @param ProvidersRepository $providers_repository The repository instance for managing providers.
	 * @param ConnectionService $service The connection service instance.
	 */
	public function __construct( ProvidersRepository $providers_repository, ConnectionService $service ) {
		$this->providers_repository = $providers_repository;
		$this->service              = $service;
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
	 * Migration logic for version 2.1.0.
	 *
	 * Checks if the plugin version is 2.1.0, and if a bug caused the active SMTP provider to be turned off.
	 * If certain conditions are met, it reactivates the legacy SMTP provider and updates the plugin version.
	 *
	 * @return void
	 */
	public function migration() {
		// Get the current plugin version from the WordPress options.
		$version = get_option( self::OPTION_VERSION_NAME, '' );

		// Check if the current version is 2.1.0
		if ( version_compare( $version, '2.1.0', '==' ) ) {

			// Retrieve the current active provider, legacy provider, and all providers.
			$active_provider = current( $this->providers_repository->get_active_providers() );
			$legacy_provider = $this->providers_repository->get_provider_by_id( 'legacy_smtp_id' );
			$all_providers   = $this->providers_repository->get_all_providers();

			// Ensure there is no active provider, the legacy provider exists, and there is only one provider in total.
			if (
				! is_object( $active_provider )          // No active provider should be present
				&& is_object( $legacy_provider )         // Legacy SMTP provider should exist
				&& count( $all_providers ) === 1         // Only one provider should exist
			) {
				// add a flag for showing the notice.
				update_option( self::FLAG_NAME, 'yes' );
			}
		}

		// Update the plugin version in the WordPress options to reflect the successful migration.
		update_option( self::OPTION_VERSION_NAME, WPSMTP_VERSION );
	}
}
