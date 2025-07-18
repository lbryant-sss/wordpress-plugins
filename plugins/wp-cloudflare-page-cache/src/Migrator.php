<?php

namespace SPC;

use SPC\Modules\Frontend;
use SPC\Services\Settings_Store;
use SW_CLOUDFLARE_PAGECACHE;

class Migrator {

	/**
	 * @var SW_CLOUDFLARE_PAGECACHE $plugin Plugin instance.
	 */
	private $plugin;
	/**
	 * @var string $previous_version String in semver format.
	 */
	private $previous_version = '';
	/**
	 * @var \SWCFPC_Logs $logger Logger instance.
	 */
	private $logger;

	/**
	 * @var \SPC\Services\Settings_Store $settings_store Settings store instance.
	 */
	private $settings_store;

	/**
	 * Constructor
	 *
	 * @param SW_CLOUDFLARE_PAGECACHE $plugin Plugin instance.
	 */
	public function __construct( SW_CLOUDFLARE_PAGECACHE $plugin ) {
		$this->plugin           = $plugin;
		$this->settings_store   = Settings_Store::get_instance();
		$this->previous_version = get_option( 'swcfpc_version', '' );
		$this->logger           = $this->plugin->get_logger();
	}

	/**
	 * Upgrade plugin.
	 *
	 * @return void
	 */
	public function run_update_migrations() {
		// If the plugin is being installed for the first time we don't have CF enabled.
		if ( empty( $this->previous_version ) ) {
			$this->logger->add_log( 'upgrader::run_update_migrations', 'Previous version is empty. Skipping upgrade.' );

			return;
		}

		$this->logger->add_log( 'upgrader::run_update_migrations', 'Running migrations.' );
		$this->maybe_update_cache_rule();
		$this->relink_cache_file();

		if ( version_compare( $this->previous_version, '5.0.5', '<' ) ) {
			$this->migrate_to_5_0_5();
		}

		if ( version_compare( $this->previous_version, '5.0.6', '<' ) ) {
			$this->migrate_to_5_0_6();
		}

		if ( version_compare( $this->previous_version, '5.1.0', '<' ) ) {
			$this->migrate_to_5_1_0();
		}

		if ( version_compare( $this->previous_version, '5.1.2', '<' ) ) {
			$this->migrate_to_5_1_2();
		}

		do_action( 'swcfpc_after_plugin_upgrader_run' );
	}

	/**
	 * Maybe update cache rule.
	 *
	 * @return void
	 */
	private function maybe_update_cache_rule() {
		$this->logger->add_log( 'upgrader::update_cache_rule', 'Start cache rule upgrade.' );

		$this->plugin->get_cloudflare_handler()->update_cache_rule_if_diff( $error );

		if ( $error ) {
			$this->logger->add_log( 'upgrader::update_cache_rule', sprintf( 'Error upgrading cache rule: %s', $error ), true );
		}

		$this->logger->add_log( 'upgrader::update_cache_rule', 'Cache rule updated.' );
	}
	/**
	 * Migrate to 5.1.0 from previous versions.
	 *
	 * @return void
	 */
	private function migrate_to_5_1_0() {
		$this->plugin->get_logger()->add_log( 'upgrader::migrate_to_5_1_0', 'Migrating to 5.1.0' );

		$setting = $this->settings_store->get( Constants::SETTING_LAZY_LOAD_SKIP_IMAGES, 2 );
		if ( $setting > 0 ) {
			$this->settings_store->set( Constants::SETTING_LAZY_LOAD_BEHAVIOUR, Frontend::LAZY_LOAD_BEHAVIOUR_FIXED )->save();
		} else {
			$this->settings_store->set( Constants::SETTING_LAZY_LOAD_BEHAVIOUR, Frontend::LAZY_LOAD_BEHAVIOUR_ALL )->save();
		}
	}

	/**
	 * Migrate to 5.0.6 from previous versions.
	 *
	 * Updates the excluded cookies list with additional ones.
	 *
	 * @return void
	 */
	private function migrate_to_5_0_6() {
		$this->plugin->get_logger()->add_log( 'upgrader::migrate_to_5_0_6', 'Migrating to 5.0.6' );

		$setting_data = $this->settings_store->get( Constants::SETTING_EXCLUDED_COOKIES, Constants::DEFAULT_EXCLUDED_COOKIES );

		if ( ! is_array( $setting_data ) ) {
			return;
		}

		$setting_data = array_filter(
			$setting_data,
			function ( $cookie ) {
				return ! in_array( $cookie, [ 'wp-', '^wp-' ], true );
			}
		);

		$this->settings_store
			->set( Constants::SETTING_EXCLUDED_COOKIES, $setting_data )
			->save();
	}

	/**
	 * Migrate to 5.0.5 from previous versions.
	 *
	 * Updates the excluded cookies list with additional ones.
	 *
	 * @return void
	 */
	private function migrate_to_5_0_5() {
		$this->plugin->get_logger()->add_log( 'upgrader::migrate_to_5_0_5', 'Migrating to 5.0.5' );

		$new_values = [
			'wordpress',
			'comment_',
			'woocommerce_',
			'xf_',
			'edd_',
			'jetpack',
			'yith_wcwl_session_',
			'yith_wrvp_',
			'wpsc_',
			'ecwid',
			'ec_',
			'bookly',
		];

		$old_setting = $this->settings_store->get( Constants::SETTING_EXCLUDED_COOKIES, Constants::DEFAULT_EXCLUDED_COOKIES );
		$old_setting = array_filter(
			$old_setting,
			function ( $cookie ) use ( $new_values ) {
				if ( in_array( trim( $cookie, '^' ), $new_values, true ) ) {
					return false;
				}

				return true;
			}
		);

		$new_setting = array_unique( array_merge( $old_setting, $new_values ) );

		$this->settings_store
			->set( Constants::SETTING_EXCLUDED_COOKIES, $new_setting )
			->save();
	}

	/**
	 * Migrate to 5.1.2 from previous versions.
	 *
	 * "ENABLE_CACHE_RULE" was previously not saved and its display value was based on the presence of a rule ID.
	 *
	 * @return void
	 */
	private function migrate_to_5_1_2() {
		$this->plugin->get_logger()->add_log( 'upgrader::migrate_to_5_1_2', 'Migrating to 5.1.2' );

		$enable_cache_rule = (bool) $this->settings_store->get( Constants::ENABLE_CACHE_RULE );

		// Already enabled. Bail.
		if ( $enable_cache_rule ) {
			return;
		}

		$rule_id = $this->settings_store->get( Constants::RULE_ID_CACHE, '' );

		if ( empty( $rule_id ) ) {
			return;
		}

		$this->settings_store->set( Constants::ENABLE_CACHE_RULE, 1 )->save();
	}

	/**
	 * Relink the advanced-cache.php drop-in.
	 *
	 * @return void
	 */
	private function relink_cache_file() {
		if ( ! defined( 'SWCFPC_ADVANCED_CACHE' ) ) {
			return;
		}

		$cache_enabled  = $this->settings_store->get( Constants::SETTING_CF_CACHE_ENABLED );
		$fallback_cache = $this->settings_store->get( Constants::SETTING_ENABLE_FALLBACK_CACHE );
		$curl_enabled   = $this->settings_store->get( Constants::SETTING_FALLBACK_CACHE_CURL );

		if ( $cache_enabled > 0 && $fallback_cache > 0 && ! $curl_enabled ) {
			$handler = $this->plugin->get_fallback_cache_handler();

			$handler->fallback_cache_advanced_cache_disable();
			$handler->fallback_cache_advanced_cache_enable();
		}
	}
}
