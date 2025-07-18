<?php

use SPC\Constants;
use SPC\Services\Settings_Store;
use SPC\Utils\Helpers;

defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' );

class SWCFPC_Html_Cache {
	/**
	 * The main plugin class.
	 *
	 * @var \SW_CLOUDFLARE_PAGECACHE
	 */
	private $main_instance              = null;
	private $current_page_can_be_cached = false;

	public function __construct( $main_instance ) {
		$this->main_instance = $main_instance;
		$this->actions();
	}

	private function actions() {
		if ( ! Settings_Store::get_instance()->get( Constants::SETTING_PURGE_ONLY_HTML ) ) {
			return;
		}

		add_action( 'shutdown', [ $this, 'add_current_url_to_cache' ], PHP_INT_MAX );
	}

	public function cache_current_page() {
		$this->current_page_can_be_cached = true;
	}

	public function do_not_cache_current_page() {
		$this->current_page_can_be_cached = false;
	}

	private function add_url_to_cache( $url ) {
		$cache_path = $this->init_directory();
		$cache_key  = $this->get_cache_key( $url );

		$filename     = $cache_path . $cache_key;
		$file_content = "{$url}|" . time();

		file_put_contents( $cache_path . $cache_key, $file_content );

		return $filename;
	}

	public function add_current_url_to_cache() {
		global $wp_query;

		// First check for WP CLI as $_SERVER is not available for WP_CLI
		if ( defined( 'WP_CLI' ) && WP_CLI === true ) {
			$this->main_instance->get_logger()->add_log( 'html_cache::add_current_url_to_cache', 'The URL cannot be cached due to WP CLI.', true );

			return;
		}

		$parts       = parse_url( home_url() );
		$current_url = "{$parts['scheme']}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

		if ( isset( $wp_query ) && function_exists( 'is_404' ) && is_404() ) {
			$this->main_instance->get_logger()->add_log( 'html_cache::add_current_url_to_cache', "The URL {$current_url} cannot be cached because it returns 404.", true );

			return;
		}

		if ( $this->current_page_can_be_cached == false ) {
			$this->main_instance->get_logger()->add_log( 'html_cache::add_current_url_to_cache', "The URL {$current_url} cannot be cached due to caching rules.", true );

			return;
		}

		if ( strcasecmp( $_SERVER['HTTP_HOST'], $parts['host'] ) !== 0 ) {
			$this->main_instance->get_logger()->add_log( 'html_cache::add_current_url_to_cache', "The URL {$current_url} cannot be cached because the host does not match with the one of home_url() function ({$parts['host']}).", true );

			return;
		}

		/**
		 * Now check if the $current_url has any unwanted query parameters like (v=, ref=, aff=, utm_*=)
		 * If the URL has such things, then don't add them to the html_cache as html_cache should only hold
		 * proper URLs like /example-car/ and not /example-car/?v=123&aff=123&ref=h65
		 */
		$current_url_parsed       = parse_url( $current_url );
		$current_url_query_params = [];

		if ( array_key_exists( 'query', $current_url_parsed ) ) {

			if ( $current_url_parsed['query'] === '' ) {
				$this->main_instance->get_logger()->add_log( 'html_cache::add_current_url_to_cache', "This URL {$current_url} cannot be cached because the URL has no query param but has the question mark at the end of the URL without actually having any query params. It makes no sense to cache this page as the proper version of the URL will be considered for caching.", true );

				return;
			} else {

				// First parse the query params to an array to manage it better
				parse_str( $current_url_parsed['query'], $current_url_query_params );

				// Get the array of query params that would be ignored
				$ignored_query_params = $this->main_instance->get_ignored_query_params();

				// Loop though $ignored_query_params
				foreach ( $ignored_query_params as $ignored_query_param ) {

					// Check if that query param is present in $current_url_query_params
					if ( array_key_exists( $ignored_query_param, $current_url_query_params ) ) {

						// The ignored query param is present in the $current_url_query_params. So, unset it from there
						unset( $current_url_query_params[ $ignored_query_param ] );
					}
				}

				// Now lets check if we have any query params left in $current_url_query_params
				if ( count( $current_url_query_params ) > 0 ) {

					$new_current_url_query_params = http_build_query( $current_url_query_params );
					$current_url_parsed['query']  = $new_current_url_query_params;
				} else {
					// Remove the query section from parsed URL
					unset( $current_url_parsed['query'] );
				}

				// Get the new current URL without the marketing query params
				$current_url = $this->main_instance->get_unparsed_url( $current_url_parsed );
			}
		}

		// Time to add the URL to HTML cache
		$filename = $this->add_url_to_cache( $current_url );

		$this->main_instance->get_logger()->add_log( 'html_cache::add_current_url_to_cache', "Created the file {$filename} for the URL {$current_url}", true );
	}

	private function get_cache_key( $url ) {
		$cache_key  = sha1( $url );
		$cache_key .= '.tmp';

		return $cache_key;
	}

	private function init_directory() {
		$cache_path = Helpers::get_plugin_content_dir() . '/cached_html_pages/';

		if ( ! file_exists( $cache_path ) ) {
			wp_mkdir_p( $cache_path );
		}

		if ( file_exists( $cache_path ) && ! file_exists( "{$cache_path}index.php" ) ) {
			file_put_contents( "{$cache_path}index.php", '<?php // Silence is golden' );
		}

		return $cache_path;
	}

	public function delete_all_cached_urls() {

		$cache_path = $this->init_directory();

		// Get a list of all of the file names in the folder.
		$files = glob( $cache_path . '/*.tmp' );

		foreach ( $files as $single_file ) {

			if ( is_file( $single_file ) ) {
				@unlink( $single_file );
			}
		}
	}

	public function get_cached_urls() {
		$cache_path = $this->init_directory();
		$urls       = [];

		// Get a list of all of the file names in the folder.
		$files = glob( $cache_path . '/*.tmp' );

		// keep only 1000 files
		$files = array_slice( $files, 0, 1000 );

		if ( ! is_array( $files ) ) {
			return [];
		}

		foreach ( $files as $single_file ) {
			if ( ! is_file( $single_file ) ) {
				continue;
			}

			list($single_url, $single_timestamp) = explode( '|', file_get_contents( $single_file ) );

			if ( strlen( $single_url ) > 1 ) {
				$urls[] = [
					'url'       => $single_url,
					'timestamp' => $single_timestamp,
				];
			}
		}

		return $urls;
	}

	public function get_cached_urls_by_timestamp( $timestamp ) {
		$cache_path = $this->init_directory();
		$urls       = [];

		// Get a list of all of the file names in the folder.
		$files = glob( $cache_path . '/*.tmp' );
		foreach ( $files as $single_file ) {
			if ( is_file( $single_file ) ) {

				list($single_url, $single_timestamp) = explode( '|', file_get_contents( $single_file ) );

				if ( $single_timestamp <= $timestamp && strlen( $single_url ) > 1 ) {
					$urls[] = $single_url;
				}
			}
		}

		return $urls;
	}

	public function delete_cached_urls_by_timestamp( $timestamp ) {
		$cache_path = $this->init_directory();

		// Get a list of all of the file names in the folder.
		$files = glob( $cache_path . '/*.tmp' );

		foreach ( $files as $single_file ) {

			if ( is_file( $single_file ) ) {

				list($single_url, $single_timestamp) = explode( '|', file_get_contents( $single_file ) );

				if ( $single_timestamp <= $timestamp ) {
					unlink( $single_file );
				}
			}
		}
	}

	public function delete_cached_urls_by_urls_list( $urls ) {
		if ( ! is_array( $urls ) ) {
			return false;
		}

		$cache_path = $this->init_directory();
		// Get a list of all of the file names in the folder.
		$files = glob( $cache_path . '/*.tmp' );

		foreach ( $files as $single_file ) {
			if ( is_file( $single_file ) ) {

				list($single_url, $single_timestamp) = explode( '|', file_get_contents( $single_file ) );

				if ( in_array( $single_url, $urls, true ) ) {
					unlink( $single_file );
				}
			}
		}
	}
}
