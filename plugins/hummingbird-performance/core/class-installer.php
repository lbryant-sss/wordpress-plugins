<?php
/**
 * Manages activation/deactivation and upgrades of Hummingbird
 *
 * @author: WPMUDEV, Ignacio Cruz (igmoweb)
 * @package Hummingbird\Core
 */

namespace Hummingbird\Core;

use Hummingbird\WP_Hummingbird;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Installer
 */
class Installer {

	/**
	 * Plugin activation
	 */
	public static function activate() {
		if ( ! defined( 'WPHB_ACTIVATING' ) ) {
			define( 'WPHB_ACTIVATING', true );
		}

		// Save plugin activated date time.
		$version = get_site_option( 'wphb_version' );
		$action  = ! empty( $version ) ? 'plugin_activated' : 'plugin_installed';
		self::save_plugin_timestamps( $action );

		update_site_option( 'wphb_version', WPHB_VERSION );
		update_site_option( 'wphb-notice-uptime-info-show', 'yes' ); // Add uptime notice.
		update_site_option( 'wphb-notice-connect-for-site-monitoring-show', 'yes' ); // Add connect notice.

		// From get_site_option() docs, false = if Option not exists.
		if ( false === get_site_option( 'wphb_run_onboarding' ) ) {
			update_site_option( 'wphb_run_onboarding', true );
		}
	}

	/**
	 * Plugin activation in a blog (if the site is a multisite)
	 */
	public static function activate_blog() {
		update_option( 'wphb_version', WPHB_VERSION );

		if ( false === get_option( 'wphb_run_onboarding' ) ) {
			update_option( 'wphb_run_onboarding', true );
		}

		do_action( 'wphb_activate' );
	}

	/**
	 * Plugin deactivation
	 */
	public static function deactivate() {
		// Avoid executing this over an over in same thread execution.
		if ( defined( 'WPHB_SWITCHING_VERSION' ) ) {
			return;
		}

		if ( ! empty( get_site_option( 'wphb_run_onboarding' ) ) ) {
			update_site_option( 'wphb_run_onboarding', null );
		}

		$settings = Settings::get_settings( 'settings' );
		WP_Hummingbird::flush_cache( $settings['remove_data'], $settings['remove_settings'] );

		Utils::get_module( 'page_cache' )->toggle_service( false, true );

		wp_clear_scheduled_hook( 'wphb_clear_logs' );

		if ( $settings['remove_settings'] ) {
			// Completely remove hummingbird-asset folder.
			Filesystem::instance()->purge_ao_cache();
		}

		do_action( 'wphb_deactivate' );
	}

	/**
	 * Plugin upgrades
	 */
	public static function maybe_upgrade() {
		// Avoid executing this over an over in same thread execution.
		if ( defined( 'WPHB_ACTIVATING' ) ) {
			return;
		}

		if ( defined( 'WPHB_UPGRADING' ) && WPHB_UPGRADING ) {
			return;
		}

		self::upgrade();
	}

	/**
	 * Upgrade
	 */
	public static function upgrade() {
		$version = get_site_option( 'wphb_version' );

		if ( false === $version ) {
			self::activate();
		}

		if ( is_multisite() ) {
			$blog_version = get_option( 'wphb_version' );
			if ( false === $blog_version ) {
				self::activate_blog();
			}

			if ( false !== $blog_version && WPHB_VERSION !== $blog_version ) {
				if ( version_compare( $blog_version, '2.6.0', '<' ) ) {
					self::upgrade_2_6_0_multi();
				}

				if ( version_compare( $version, '2.6.2', '<' ) ) {
					self::upgrade_2_6_2_multi();
				}

				if ( version_compare( $version, '2.7.1', '<' ) ) {
					self::upgrade_2_7_1_multi();
				}

				if ( version_compare( $version, '2.7.2', '<' ) ) {
					self::upgrade_2_7_2_multi();
				}

				if ( version_compare( $version, '3.0.0', '<' ) ) {
					self::upgrade_3_0_0_multi();
				}
			}
		}

		if ( false !== $version && WPHB_VERSION !== $version ) {
			if ( ! defined( 'WPHB_UPGRADING' ) ) {
				define( 'WPHB_UPGRADING', true );
			}

			// Save plugin upgraded time.
			self::save_plugin_timestamps( 'plugin_upgraded' );

			if ( version_compare( $version, '2.6.0', '<' ) ) {
				self::upgrade_2_6_0();
			}

			if ( version_compare( $version, '2.6.2', '<' ) ) {
				self::upgrade_2_6_2();
			}

			if ( version_compare( $version, '2.7.1', '<' ) ) {
				self::upgrade_2_7_1();
				self::upgrade_2_7_1_multi();
			}

			if ( version_compare( $version, '2.7.2', '<' ) ) {
				// Need to refresh the data.
				delete_option( 'wphb-caching-data' );
				self::upgrade_2_7_2_multi();
			}

			if ( version_compare( $version, '3.0.0', '<' ) ) {
				self::upgrade_3_0_0();
			}

			if ( version_compare( $version, '3.0.1', '<' ) ) {
				self::upgrade_3_0_1();
			}

			if ( version_compare( $version, '3.1.0', '<' ) ) {
				self::upgrade_3_1_0();
			}

			if ( version_compare( $version, '3.2.0', '<' ) ) {
				self::upgrade_3_2_0();
			}

			if ( version_compare( $version, '3.3.0', '<' ) ) {
				delete_site_option( 'wp-smush-show-black-friday' );
			}

			if ( version_compare( $version, '3.3.4', '<' ) ) {
				self::upgrade_3_3_4();
			}

			if ( version_compare( $version, '3.4.0', '<' ) ) {
				self::upgrade_3_4_0();
			}

			if ( version_compare( $version, '3.5.0', '<' ) ) {
				self::upgrade_3_5_0();
			}

			if ( version_compare( $version, '3.6.0', '<' ) ) {
				self::upgrade_3_6_0();
			}

			if ( version_compare( $version, '3.7.3', '<' ) ) {
				self::upgrade_3_7_3();
			}

			if ( version_compare( $version, '3.8.0', '<' ) ) {
				self::upgrade_3_8_0();
			}

			if ( version_compare( $version, '3.9.0', '<' ) ) {
				self::upgrade_3_9_0();
			}

			if ( version_compare( $version, '3.9.3', '<' ) ) {
				self::upgrade_3_9_3_0();
			}

			if ( version_compare( $version, '3.10.0', '<' ) ) {
				self::upgrade_3_10_0();
			}

			if ( version_compare( $version, '3.15.0', '<' ) ) {
				self::upgrade_3_15_0();
			}

			update_site_option( 'wphb_version', WPHB_VERSION );
		}
	}

	/**
	 * Upgrades a single blog in a multisite
	 */
	public static function maybe_upgrade_blog() {
		$version = get_option( 'wphb_version' );

		if ( WPHB_VERSION === $version ) {
			return;
		}

		update_option( 'wphb_version', WPHB_VERSION );
	}

	/**
	 * This is a fixed up mechanism for only updating the required stuff.
	 *
	 * Common for both subsites and single sites.
	 *
	 * @since 2.6.0
	 * @deprecated
	 */
	private static function upgrade_2_6_0_multi() {
		add_option( 'wphb-minification-show-config_modal', true );

		// Add AO upgrade modal.
		if ( Settings::get_setting( 'minify_blog', 'minify' ) ) {
			add_option( 'wphb_do_minification_upgrade', true );
			self::upgrade_minification_structure();
		}
	}

	/**
	 * Upgrade to 2.6.0
	 *
	 * @since 2.6.0
	 * @deprecated
	 */
	private static function upgrade_2_6_0() {
		// Show upgrade summary, Currently only for v2.6.0.
		add_site_option( 'wphb_show_upgrade_summary', true );
		add_option( 'wphb-minification-show-config_modal', true );

		// Add AO upgrade modal.
		if ( Settings::get_setting( 'enabled', 'minify' ) ) {
			add_option( 'wphb_do_minification_upgrade', true );
			self::upgrade_minification_structure();
		}

		// Change default value for "File change detection".
		Settings::update_setting( 'detection', 'auto', 'page_cache' );

		if ( ! Settings::get_setting( 'enabled', 'page_cache' ) ) {
			return;
		}

		// Update Page Cache exclusion rule for split sitemap.
		$settings = Utils::get_module( 'page_cache' )->get_settings();
		if ( is_array( $settings['exclude']['url_strings'] ) && ! in_array( 'sitemap[0-9]*\.xml', $settings['exclude']['url_strings'], true ) ) {
			// Remove old value.
			$key = array_search( 'sitemap.xml', $settings['exclude']['url_strings'], true );
			if ( false !== $key && isset( $settings['exclude']['url_strings'][ $key ] ) ) {
				unset( $settings['exclude']['url_strings'][ $key ] );
			}

			// Add new one.
			$settings['exclude']['url_strings'][] = 'sitemap[0-9]*\.xml';
			Utils::get_module( 'page_cache' )->save_settings( $settings );
		}
	}

	/**
	 * Upgrade the pre 2.5 settings to new 2.6 format.
	 *
	 * @since 2.6.0
	 * @deprecated
	 */
	private static function upgrade_minification_structure() {
		$options = Settings::get_settings( 'minify' );

		$collections = Modules\Minify\Sources_Collector::get_collection();

		foreach ( array( 'scripts', 'styles' ) as $type ) {
			$keys = array_keys( $collections[ $type ] );

			if ( ! isset( $options['minify'] ) || ! isset( $options['minify'][ $type ] ) ) {
				continue;
			}

			$options['dont_minify'][ $type ]  = array_diff( $keys, $options['minify'][ $type ] );
			$options['dont_combine'][ $type ] = array_diff( $keys, $options['combine'][ $type ] );
		}

		unset( $options['minify'] );
		unset( $options['combine'] );

		Settings::update_settings( $options, 'minify' );
	}

	/**
	 * Upgrade sub sites to 2.6.2
	 *
	 * @since 2.6.2
	 * @deprecated
	 */
	private static function upgrade_2_6_2_multi() {
		// Remove AO upgrade modal for sub sites that do not have AO enabled.
		if ( ! Settings::get_setting( 'minify_blog', 'minify' ) ) {
			delete_option( 'wphb_do_minification_upgrade' );
		}
	}

	/**
	 * Upgrade to 2.6.2
	 *
	 * @since 2.6.2
	 * @deprecated
	 */
	private static function upgrade_2_6_2() {
		Logger::cleanup();

		// Remove unused database entries.
		delete_option( 'wphb-new-user-tour' );

		// Enhance page cache sitemaps support.
		if ( Settings::get_setting( 'enabled', 'page_cache' ) ) {
			$settings = Utils::get_module( 'page_cache' )->get_settings();
			if ( isset( $settings['exclude']['url_strings'] ) && is_array( $settings['exclude']['url_strings'] ) ) {
				// Remove old value.
				$key = array_search( 'sitemap[0-9]*\.xml', $settings['exclude']['url_strings'], true );
				if ( false !== $key && isset( $settings['exclude']['url_strings'][ $key ] ) ) {
					unset( $settings['exclude']['url_strings'][ $key ] );
				}

				// We double search, one of the previous updates, added an extra value.
				$key = array_search( 'sitemap[0-9]*.xml', $settings['exclude']['url_strings'], true );
				if ( false !== $key && isset( $settings['exclude']['url_strings'][ $key ] ) ) {
					unset( $settings['exclude']['url_strings'][ $key ] );
				}

				$key = array_search( 'sitemap[0-9]*.xml', $settings['exclude']['url_strings'], true );
				if ( false !== $key && isset( $settings['exclude']['url_strings'][ $key ] ) ) {
					unset( $settings['exclude']['url_strings'][ $key ] );
				}

				// Add new one.
				$settings['exclude']['url_strings'][] = 'sitemap[^\/.]*\.(?:xml|xsl)';
				Utils::get_module( 'page_cache' )->save_settings( $settings );
			}
		}
	}

	/**
	 * Upgrade to 2.7.1
	 *
	 * @since 2.7.1
	 * @deprecated
	 */
	private static function upgrade_2_7_1() {
		if ( Settings::get_setting( 'enabled', 'page_cache' ) ) {
			unlink( WP_CONTENT_DIR . '/advanced-cache.php' );
		}
	}

	/**
	 * Upgrade to 2.7.1
	 *
	 * @since 2.7.1
	 * @deprecated
	 */
	private static function upgrade_2_7_1_multi() {
		wp_cache_delete( 'wphb_process_queue', 'options' );
	}

	/**
	 * Upgrade to 2.7.1 (single and subsites).
	 *
	 * @since 2.7.2
	 * @deprecated
	 */
	private static function upgrade_2_7_2_multi() {
		$minify  = Utils::get_module( 'minify' );
		$options = $minify->get_options();

		// Asset optimization not enabled - skip.
		if ( ! $options['enabled'] ) {
			return;
		}

		// If not enabled on subsites - skip.
		if ( is_multisite() && ! is_network_admin() && ! $options['minify_blog'] ) {
			return;
		}

		// Not Auto Basic mode - exit.
		if ( ! isset( $options['type'] ) || 'basic' !== $options['type'] ) {
			return;
		}

		$collections = \Hummingbird\Core\Modules\Minify\Sources_Collector::get_collection();

		// Fix styles and scripts from combining on basic mode.
		$options['dont_combine']['styles']  = array_keys( $collections['styles'] );
		$options['dont_combine']['scripts'] = array_keys( $collections['scripts'] );

		$minify->update_options( $options );
		$minify->clear_cache( false );
	}

	/**
	 * Upgrade to 3.0.0.
	 *
	 * @since 3.0.0
	 */
	private static function upgrade_3_0_0() {
		// Summary upgrade modal.
		add_site_option( 'wphb_show_upgrade_summary', true );

		// Force browser caching refresh.
		delete_site_option( 'wphb-caching-data' );

		// Force performance report refresh.
		Settings::delete( 'wphb-last-report' );

		// Remove deprecated hub options.
		$options = Utils::get_module( 'performance' )->get_options();
		if ( isset( $options['hub'] ) ) {
			unset( $options['hub'] );
		}

		unset( $options['widget'] );

		Utils::get_module( 'performance' )->update_options( $options );
	}

	/**
	 * Upgrade to 3.0.0 on multisite.
	 *
	 * @since 3.0.0
	 */
	private static function upgrade_3_0_0_multi() {
		// Force performance report refresh.
		Settings::delete( 'wphb-last-report' );
	}

	/**
	 * Upgrade to 3.0.1
	 */
	private static function upgrade_3_0_1() {
		// Move cache control from Caching - Settings to Settings - General.
		$settings = Settings::get_settings();

		if ( ! isset( $settings['page_cache']['control'] ) ) {
			return;
		}

		$settings['settings']['control'] = $settings['page_cache']['control'];
		unset( $settings['page_cache']['control'] );

		Settings::update_settings( $settings );
	}

	/**
	 * Upgrade to 3.1.0
	 */
	private static function upgrade_3_1_0() {
		// Summary upgrade modal.
		add_site_option( 'wphb_show_upgrade_summary', true );

		// For account where no zone_key defined, we need to refresh the zone data.
		$settings = Settings::get_settings( 'cloudflare' );

		if ( ! $settings['enabled'] ) {
			return;
		}

		// Account ID already set.
		if ( isset( $settings['account_id'] ) && ! empty( $settings['account_id'] ) ) {
			return;
		}

		$zones = Utils::get_module( 'cloudflare' )->get_zones_list();

		if ( ! is_wp_error( $zones ) ) {
			Utils::get_module( 'cloudflare' )->process_zones( $zones, $settings['zone_name'] );
		}
	}

	/**
	 * Upgrade to 3.1.1
	 */
	private static function upgrade_3_2_0() {
		$settings = Settings::get_settings();

		if ( isset( $settings['performance'] ) && isset( $settings['performance']['reports'] ) ) {
			if ( isset( $settings['performance']['reports']['enabled'] ) && false === $settings['performance']['reports']['enabled'] ) {
				$settings['performance']['reports']['recipients'] = array();
			} elseif ( ! empty( $settings['performance']['reports']['recipients'] ) ) {
				foreach ( $settings['performance']['reports']['recipients'] as $key => $recipient ) {
					$user = get_user_by( 'email', $recipient['email'] );

					$settings['performance']['reports']['recipients'][ $key ]['id']   = false === $user ? 0 : $user->ID;
					$settings['performance']['reports']['recipients'][ $key ]['role'] = false === $user || empty( $user->roles ) ? '' : ucfirst( $user->roles[0] );
				}
			}
		}

		if ( isset( $settings['uptime'] ) && isset( $settings['uptime']['reports'] ) ) {
			if ( isset( $settings['uptime']['reports']['enabled'] ) && false === $settings['uptime']['reports']['enabled'] ) {
				$settings['uptime']['reports']['recipients'] = array();
			} elseif ( ! empty( $settings['uptime']['reports']['recipients'] ) ) {
				foreach ( $settings['uptime']['reports']['recipients'] as $key => $recipient ) {
					$user = get_user_by( 'email', $recipient['email'] );

					$settings['uptime']['reports']['recipients'][ $key ]['id']   = false === $user ? 0 : $user->ID;
					$settings['uptime']['reports']['recipients'][ $key ]['role'] = false === $user || empty( $user->roles ) ? '' : ucfirst( $user->roles[0] );
				}
			}
		}

		if ( isset( $settings['uptime'] ) && isset( $settings['uptime']['notifications'] ) ) {
			if ( isset( $settings['uptime']['notifications']['enabled'] ) && false === $settings['uptime']['notifications']['enabled'] ) {
				$settings['uptime']['notifications']['recipients'] = array();
			} elseif ( ! empty( $settings['uptime']['notifications']['recipients'] ) ) {
				foreach ( $settings['uptime']['notifications']['recipients'] as $key => $recipient ) {
					$user = get_user_by( 'email', $recipient['email'] );

					$settings['uptime']['notifications']['recipients'][ $key ]['id']   = false === $user ? 0 : $user->ID;
					$settings['uptime']['notifications']['recipients'][ $key ]['role'] = false === $user || empty( $user->roles ) ? '' : ucfirst( $user->roles[0] );

					// We need to do some converting.
					if ( ! isset( $settings['uptime']['notifications']['recipients'][ $key ]['is_pending'] ) ) {
						$settings['uptime']['notifications']['recipients'][ $key ]['is_pending'] = false;
					} else {
						$settings['uptime']['notifications']['recipients'][ $key ]['is_pending'] = filter_var( $settings['uptime']['notifications']['recipients'][ $key ]['is_pending'], FILTER_VALIDATE_BOOLEAN );
					}

					if ( isset( $settings['uptime']['notifications']['recipients'][ $key ]['is_subscribed'] ) ) {
						$settings['uptime']['notifications']['recipients'][ $key ]['is_subscribed'] = filter_var( $settings['uptime']['notifications']['recipients'][ $key ]['is_subscribed'], FILTER_VALIDATE_BOOLEAN );
					}
					if ( isset( $settings['uptime']['notifications']['recipients'][ $key ]['is_can_resend_confirmation'] ) ) {
						$settings['uptime']['notifications']['recipients'][ $key ]['is_can_resend_confirmation'] = filter_var( $settings['uptime']['notifications']['recipients'][ $key ]['is_can_resend_confirmation'], FILTER_VALIDATE_BOOLEAN );
					}
				}
			}
		}

		if ( isset( $settings['advanced'] ) ) {
			$new_options = array();

			$new_options['enabled'] = isset( $settings['advanced']['db_cleanups'] ) ? $settings['advanced']['db_cleanups'] : false;
			unset( $settings['advanced']['db_cleanups'] );

			if ( isset( $settings['advanced']['db_frequency'] ) ) {
				$new_options['frequency'] = $settings['advanced']['db_frequency'];
				unset( $settings['advanced']['db_frequency'] );
			}

			if ( isset( $settings['advanced']['db_tables'] ) ) {
				$new_options['tables'] = $settings['advanced']['db_tables'];
				unset( $settings['advanced']['db_tables'] );
			}

			// Migrate to new schedule format.
			if ( $new_options['enabled'] ) {
				$timestamp = wp_next_scheduled( 'wphb_database_cleanup' );
				if ( $timestamp ) {
					$new_options['time'] = wp_date( 'H:i', $timestamp );

					if ( isset( $new_options['frequency'] ) && 7 === (int) $new_options['frequency'] ) {
						$new_options['day'] = wp_date( 'l', $timestamp );
					}

					if ( isset( $new_options['frequency'] ) && 30 === (int) $new_options['frequency'] ) {
						$new_options['day'] = wp_date( 'j', $timestamp );
					}

					wp_unschedule_event( $timestamp, 'wphb_database_cleanup' );
					wp_schedule_single_event( $timestamp, 'wphb_database_report' );
				}
			}

			$settings['database']['reports'] = $new_options;
		}

		Settings::update_settings( $settings );
	}

	/**
	 * Upgrade to 3.3.4 version.
	 *
	 * @since 3.3.4
	 *
	 * @return void
	 */
	private static function upgrade_3_3_4() {
		// Rename the default config.
		$stored_configs = get_site_option( 'wphb-preset_configs', false );
		if ( is_array( $stored_configs ) && isset( $stored_configs[0] ) ) {
			if ( isset( $stored_configs[0]['name'] ) && 'Basic config' === $stored_configs[0]['name'] ) {
				$stored_configs[0]['name'] = __( 'Default config', 'wphb' );
				update_site_option( 'wphb-preset_configs', $stored_configs );
			}
		}
	}

	/**
	 * Handle asset optimization settings update.
	 *
	 * @since 3.4.0
	 *
	 * @return void
	 */
	private static function upgrade_3_4_0() {
		// Summary upgrade modal.
		add_site_option( 'wphb_show_upgrade_summary', true );

		$position = Settings::get_setting( 'position', 'minify' );

		$updated = false;
		foreach ( $position as $type => $assets ) {
			if ( empty( $assets ) ) {
				continue;
			}

			$keys = array_keys( $assets );
			if ( ! is_string( $keys[0] ) ) {
				continue;
			}

			$updated = true;

			$position[ $type ] = $keys;
		}

		if ( $updated ) {
			Settings::update_setting( 'position', $position, 'minify' );
		}
	}

	/**
	 * Handle Summary upgrade modal display.
	 *
	 * @since 3.5.0
	 *
	 * @return void
	 */
	private static function upgrade_3_5_0() {
		// Summary upgrade modal.
		add_site_option( 'wphb_show_upgrade_summary', true );
	}

	/**
	 * Handle Summary upgrade modal display for critical css.
	 *
	 * @since 3.6.0
	 *
	 * @return void
	 */
	private static function upgrade_3_6_0() {
		// Summary upgrade modal.
		add_site_option( 'wphb_show_upgrade_summary', true );
	}

	/**
	 * Handle Summary upgrade modal display for INP.
	 *
	 * @since 3.7.3
	 *
	 * @return void
	 */
	private static function upgrade_3_7_3() {
		// Summary upgrade modal.
		add_site_option( 'wphb_show_upgrade_summary', true );
	}

	/**
	 * Handle Summary upgrade modal display for font optimization.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	private static function upgrade_3_8_0() {
		// Summary upgrade modal.
		add_site_option( 'wphb_show_upgrade_summary', true );
	}

	/**
	 * Enable homepage preload caching for existing user.
	 *
	 * @since 3.9.0
	 *
	 * @return void
	 */
	private static function upgrade_3_9_0() {
		// Summary upgrade modal.
		add_site_option( 'wphb_show_upgrade_summary', true );
	}

	/**
	 * Handle Summary upgrade modal display for viewport meta and handle deprecate legacy critical css notice..
	 *
	 * @since 3.9.3
	 *
	 * @return void
	 */
	private static function upgrade_3_9_3_0() {
		update_option( 'wphb-notice-legacy-critical-css-show', 'yes' );

		// Summary upgrade modal.
		add_site_option( 'wphb_show_upgrade_summary', true );
	}

	/**
	 * Handle Summary upgrade modal display.
	 *
	 * @since 3.10.0
	 *
	 * @return void
	 */
	private static function upgrade_3_10_0() {
		// Summary upgrade modal.
		add_site_option( 'wphb_show_upgrade_summary', true );
	}

	/**
	 * Handle Summary upgrade modal display.
	 *
	 * @since 3.15.0
	 *
	 * @return void
	 */
	private static function upgrade_3_15_0() {
		if ( ! Utils::has_access_to_hub() ) {
			add_site_option( 'wphb_show_upgrade_summary', true );
		}
	}

	/**
	 * Save plugin timestamps for actions like activation, deactivation, and upgrade.
	 *
	 * @param string $action The action being performed (e.g., 'plugin_activated', 'plugin_upgraded').
	 */
	private static function save_plugin_timestamps( $action ) {
		$option_key            = 'wphb_plugin_timestamps';
		$timestamps            = get_site_option( $option_key, array() );
		$timestamps[ $action ] = time();
		update_site_option( $option_key, $timestamps );
	}
}
