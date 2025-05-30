<?php
/**
 * WPMU DEV hosting integration.
 *
 * @since 2.6.2
 * @package Hummingbird\Core\Integration
 */

namespace Hummingbird\Core\Integration;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WPMUDev
 */
class WPMUDev {

	/**
	 * WPMUDev constructor.
	 *
	 * @since 2.6.2
	 */
	public function __construct() {
		if ( ! $this->should_run() ) {
			return;
		}

		// Purge FastCGI cache.
		add_action( 'wphb_clear_cache_url', array( $this, 'purge_cache' ) );
	}

	/**
	 * Check if the integration module should be enabled.
	 *
	 * @since 2.6.2
	 * @return bool
	 */
	private function should_run() {
		// Only run on WPMU DEV hosting.
		if ( ! isset( $_SERVER['WPMUDEV_HOSTED'] ) ) {
			return false;
		}

		// Only run on production.
		if ( ! isset( $_SERVER['WPMUDEV_HOSTING_ENV'] ) || 'production' !== $_SERVER['WPMUDEV_HOSTING_ENV'] ) {
			return false;
		}

		return true;
	}

	/**
	 * Purge cache.
	 *
	 * @since 2.6.2
	 *
	 * @param string $path  Path to purge for.
	 */
	public function purge_cache( $path = '' ) {
		if ( isset( $_SERVER['HTTP_HOST'] ) ) {
			$host = htmlentities( wp_unslash( $_SERVER['HTTP_HOST'] ) ); // Input var ok.
			// Determine if HTTPS is being used.
			$protocol = ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ? 'https://' : 'http://';
			$domain   = $protocol . $host;
		} else {
			$domain = untrailingslashit( get_site_url( null, null, 'https' ) );
		}

		$resolver = str_replace( array( 'http://', 'https://' ), '', $domain ) . ':443:127.0.0.1';

		// To purge only the home page / we need to append a $ character as the purge URL is a regex.
		if ( '/' === $path ) {
			$path = '/$';
		}

		$url = empty( $path ) ? $domain . '/*' : $domain . $path;

		$ch = curl_init();
		curl_setopt_array(
			$ch,
			array(
				CURLOPT_URL                  => $url,
				CURLOPT_RETURNTRANSFER       => true,
				CURLOPT_NOBODY               => true,
				CURLOPT_HEADER               => false,
				CURLOPT_CUSTOMREQUEST        => 'PURGE',
				CURLOPT_FOLLOWLOCATION       => true,
				CURLOPT_DNS_USE_GLOBAL_CACHE => false,
				CURLOPT_RESOLVE              => array(
					$resolver,
				),
			)
		);

		curl_exec( $ch );
		curl_close( $ch );
	}
}
