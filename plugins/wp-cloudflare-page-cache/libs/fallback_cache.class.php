<?php

use SPC\Constants;
use SPC\Services\Settings_Store;
use SPC\Utils\Helpers;

defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' );

class SWCFPC_Fallback_Cache {


	/**
	 * The main plugin class.
	 *
	 * @var \SW_CLOUDFLARE_PAGECACHE
	 */
	private $main_instance               = null;
	private $modules                     = false;
	private $fallback_cache_ttl_registry = false;
	private $fallback_cache              = false;

	function __construct( $main_instance ) {

		$this->main_instance = $main_instance;

		if ( $this->main_instance->get_single_config( 'cf_fallback_cache', 0 ) > 0 ) {

			if ( ! $this->fallback_cache_init_ttl_registry() ) {
				$this->fallback_cache_ttl_registry = [];
				$this->fallback_cache_update_ttl_registry();
			}

			if ( $this->main_instance->get_single_config( 'cf_fallback_cache_ttl', 0 ) > 0 ) {
				$this->fallback_cache_delete_expired_pages();
			}       
		}

		if ( $this->main_instance->get_single_config( 'cf_cache_enabled', 0 ) == 0 || $this->main_instance->get_single_config( 'cf_fallback_cache', 0 ) == 0 || ( $this->main_instance->get_single_config( 'cf_fallback_cache', 0 ) > 0 && $this->main_instance->get_single_config( 'cf_fallback_cache_curl', 0 ) > 0 ) ) {
			$this->fallback_cache_advanced_cache_disable();
		}

		if ( $this->main_instance->get_single_config( 'cf_cache_enabled', 0 ) > 0 && $this->main_instance->get_single_config( 'cf_fallback_cache', 0 ) > 0 && $this->main_instance->get_single_config( 'cf_fallback_cache_curl', 0 ) == 0 ) {
			$this->fallback_cache_advanced_cache_enable();
		}

		$this->actions();

	}


	function actions() {
		if ( (int) $this->main_instance->get_single_config( 'cf_fallback_cache', 0 ) > 0 && ! is_admin() && ! $this->main_instance->is_login_page() && (int) $this->main_instance->get_single_config( 'cf_fallback_cache_curl', 0 ) > 0 ) {
			add_action( 'shutdown', [ $this, 'shutdown_add_url_to_cache' ], PHP_INT_MAX );
		}
	}


	function fallback_cache_enable() {
		$this->fallback_cache = true;
	}


	function fallback_cache_disable() {
		$this->fallback_cache = false;
	}


	function fallback_cache_save_config() {

		$cache_path = $this->main_instance->get_plugin_wp_content_directory() . '/';

		file_put_contents("{$cache_path}main_config.php", '<?php $swcfpc_config=\'' . addslashes(json_encode(Settings_Store::get_instance()->get_all(true))) . '\'; ?>');

		if ( is_array( $this->fallback_cache_ttl_registry ) ) {
			file_put_contents( "{$cache_path}ttl_registry.json", json_encode( $this->fallback_cache_ttl_registry ) );
		}

	}


	function fallback_cache_delete_config() {

		$cache_path = $this->main_instance->get_plugin_wp_content_directory() . '/';

		if ( file_exists( "{$cache_path}ttl_registry.json" ) ) {
			@unlink( "{$cache_path}ttl_registry.json" );
		}

		if ( file_exists( "{$cache_path}ttl_registry.php" ) ) {
			@unlink( "{$cache_path}ttl_registry.php" );
		}

		if ( file_exists( "{$cache_path}main_config.php" ) ) {
			@unlink( "{$cache_path}main_config.php" );
		}

	}


	function fallback_cache_advanced_cache_enable( $force_wp_cache = false ) {

		$this->modules = $this->main_instance->get_modules();
		$this->fallback_cache_init_directory();

		$advanced_cache_dest = WP_CONTENT_DIR . '/advanced-cache.php';

		if ( ! defined( 'SWCFPC_ADVANCED_CACHE' ) || ! file_exists( $advanced_cache_dest ) ) {

			$advanced_cache_source = SWCFPC_PLUGIN_PATH . 'assets/advanced-cache.php';

			if ( file_exists( $advanced_cache_dest ) && ! @unlink( $advanced_cache_dest ) ) {
				$this->modules['logs']->add_log( 'fallback_cache::fallback_cache_advanced_cache_enable', 'Unable to remove the old advanced-cache.php from wp-content directory' );
				return false;
			}

			if ( file_put_contents( $advanced_cache_dest, file_get_contents( $advanced_cache_source ) ) === false ) {
				$this->modules['logs']->add_log( 'fallback_cache::fallback_cache_advanced_cache_enable', 'Unable to copy advanced-cache.php to wp-content directory' );
				return false;
			}

			if ( $force_wp_cache || ! defined( 'WP_CACHE' ) || ( defined( 'WP_CACHE' ) && WP_CACHE === false ) ) {

				if ( ! $this->fallback_cache_add_define_cache_wp_config() ) {
					return false;
				}           
			}

			$this->fallback_cache_save_config();

		} else {

			$this->fallback_cache_save_config();

			if ( ! defined( 'WP_CACHE' ) || ( defined( 'WP_CACHE' ) && WP_CACHE === false ) ) {

				if ( ! $this->fallback_cache_add_define_cache_wp_config() ) {
					return false;
				}           
			}       
		}

		do_action( 'swcfpc_advanced_cache_after_enable' );

		return true;

	}


	function fallback_cache_advanced_cache_disable() {

		if ( defined( 'SWCFPC_ADVANCED_CACHE' ) ) {

			if ( file_exists( WP_CONTENT_DIR . '/advanced-cache.php' ) ) {
				@unlink( WP_CONTENT_DIR . '/advanced-cache.php' );
			}

			$this->fallback_cache_delete_config();

			if ( ! is_multisite() && defined( 'WP_CACHE' ) ) {
				$this->fallback_cache_add_define_cache_wp_config( false );
			}

			$this->fallback_cache_purge_all();

		}

		do_action( 'swcfpc_advanced_cache_after_disable' );

		return true;

	}


	function fallback_cache_add_define_cache_wp_config( $turn_it_on = true ) {

		$this->modules = $this->main_instance->get_modules();

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$this->modules['logs']->add_log( 'fallback_cache::fallback_cache_add_define_cache_wp_config', 'AJAX Request' );
			return false;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			$this->modules['logs']->add_log( 'fallback_cache::fallback_cache_add_define_cache_wp_config', 'AJAX Request' );
		}

		if ( $turn_it_on && defined( 'WP_CACHE' ) && WP_CACHE ) {
			$this->modules['logs']->add_log( 'fallback_cache::fallback_cache_add_define_cache_wp_config', 'WP_CACHE already defined' );
			return false;
		}

		if ( defined( 'IS_PRESSABLE' ) && IS_PRESSABLE ) {
			$this->modules['logs']->add_log( 'fallback_cache::fallback_cache_add_define_cache_wp_config', 'IS_PRESSABLE defined' );
			return false;
		}


		$config_file_path = ABSPATH . 'wp-config.php';

		if ( ! file_exists( $config_file_path ) ) {
			$this->modules['logs']->add_log( 'fallback_cache::fallback_cache_add_define_cache_wp_config', 'Unable to find wp-config.php' );
			return false;
		}

		if ( ! $this->fallback_cache_is_wp_config_writable() ) {
			$this->modules['logs']->add_log( 'fallback_cache::fallback_cache_add_define_cache_wp_config', 'wp-config.php is not writable' );
			return false;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
		global $wp_filesystem;

		// Get content of the config file.
		$config_contents   = $wp_filesystem->get_contents( $config_file_path );
		if( $config_contents === false ) {
			$this->modules['logs']->add_log( 'fallback_cache::fallback_cache_add_define_cache_wp_config', 'Unable to read wp-config.php' );
			return false;
		}
		if( empty( $config_contents ) ) {
			$this->modules['logs']->add_log( 'fallback_cache::fallback_cache_add_define_cache_wp_config', 'wp-config.php is empty' );
			return false;
		}
		$config_file       = preg_split( '/\R/u', $config_contents );
		$config_file_count = count( $config_file );

		// Get the value of WP_CACHE constant.
		$turn_it_on = $turn_it_on ? 'true' : 'false';

		/**
		 * Filter allow to change the value of WP_CACHE constant
		 *
		 * @since 2.1
		 *
		 * @param string $turn_it_on The value of WP_CACHE constant.
		 */
		$turn_it_on = apply_filters( 'swcfpc_set_wp_cache_define', $turn_it_on );

		// Lets find out if the constant WP_CACHE is defined or not.
		$is_wp_cache_exist = false;

		// Get WP_CACHE constant define.
		$constant = "define('WP_CACHE', {$turn_it_on}); // Added by WP Cloudflare Super Page Cache";

		for ( $i = 0; $i < $config_file_count; ++$i ) {

			if ( ! preg_match( '/^define\(\s*\'([A-Z_]+)\',(.*)\)/', $config_file[ $i ], $match ) ) {
				continue;
			}

			if ( 'WP_CACHE' === $match[1] ) {

				$is_wp_cache_exist = true;
			}
		}

		if ( ! $is_wp_cache_exist && 'true' === $turn_it_on ) {
			$new_config_contents = preg_replace(
				'/(<\?php)/i',
				"<?php\r\n{$constant}\r\n",
				$config_contents,
				1
			);
		} else {
			$new_config_contents = preg_replace( '/^\s*define\(\s*\'WP_CACHE\'\s*,\s*([^\s\)]*)\s*\).+/m', $constant, $config_contents );
		}

		// Check if preg_replace succeeded
		if ( $new_config_contents === null ) {
			$this->modules['logs']->add_log( 'fallback_cache::fallback_cache_add_define_cache_wp_config', 'preg_replace failed with error: ' . preg_last_error() );
			return false;
		}

		return $wp_filesystem->put_contents( $config_file_path, $new_config_contents, FS_CHMOD_FILE );
	}

	function fallback_cache_is_wp_config_writable() {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
		global $wp_filesystem;
		$config_file_path = ABSPATH . 'wp-config.php';

		return $wp_filesystem->is_writable( $config_file_path );
	}

	function fallback_cache_is_wp_content_writable() {
		return is_writable( WP_CONTENT_DIR );
	}

	function shutdown_add_url_to_cache() {
		$this->fallback_cache_add_current_url_to_cache();
	}

	function fallback_cache_add_current_url_to_cache( $url = null, $force_cache = false ) {
		if ( ! $force_cache && (
				! $this->fallback_cache || // Cache not enabled
				$this->fallback_cache_is_url_to_exclude() || // URL excluded
				! isset( $_SERVER['REQUEST_METHOD'] ) || // No request method
				strcasecmp( $_SERVER['REQUEST_METHOD'], 'GET' ) !== 0 // Not GET
			) ) {
			return;
		}

		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) &&
			strcasecmp( $_SERVER['HTTP_USER_AGENT'], 'ua-swcfpc-fc' ) == 0 ) {
			return;
		}

		$cache_path = $this->fallback_cache_init_directory();
		$cache_key  = $this->fallback_cache_get_current_page_cache_key( $url );

		if ( file_exists( $cache_path . $cache_key ) && ! $this->fallback_cache_is_expired_page( $cache_key ) ) {
			return;
		}

		// absolute URI in multisite aware environment
		if ( empty( $url ) ) {
			$parts = parse_url( home_url() );
			$url   = "{$parts['scheme']}://{$parts['host']}" . add_query_arg( null, null );
		}

		$response = wp_remote_get(
			$url,
			[
				'timeout'    => defined( 'SWCFPC_CURL_TIMEOUT' ) ? SWCFPC_CURL_TIMEOUT : 10,
				'sslverify'  => false,
				'user-agent' => 'ua-swcfpc-fc',
			] 
		);

		if ( is_wp_error( $response ) ) {
			return;
		}

		$response_code = wp_remote_retrieve_response_code( $response );

		if ( $response_code !== 200 ) {
			return;
		}
		$ttl = (int) $this->main_instance->get_single_config( 'cf_fallback_cache_ttl', 0 ) !== 0 ? time() + $this->main_instance->get_single_config( 'cf_fallback_cache_ttl', 0 ) : 0;

		$body = wp_remote_retrieve_body( $response );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$body .= "\n<!-- Page retrieved from Super Page Cache fallback cache via cURL - page generated @ " . date( 'Y-m-d H:i:s' ) . ' - fallback cache expiration @ ' . ( 0 < $ttl ? date( 'Y-m-d H:i:s', $ttl ) : 'never expires' ) . " - cache key {$cache_key} -->";
		}

		// Provide a filter to modify the HTML before it is cached
		$body = apply_filters( 'swcfpc_curl_fallback_cache_html', $body, $cache_key );

		if ( ! is_string( $body ) ) {
			return;
		}

		file_put_contents( $cache_path . $cache_key, $body );

		// Update TTL
		$this->fallback_cache_set_single_ttl( $cache_key, $ttl );
		$this->fallback_cache_update_ttl_registry();

		// Store headers
		if ( $this->main_instance->get_single_config( 'cf_fallback_cache_save_headers', 0 ) > 0 ) {
			$this->fallback_cache_save_headers( $cache_path, $cache_key );
		}
	}


	function fallback_cache_remove_url_parameters( $url ) {
		$url_parsed       = parse_url( $url );
		$url_query_params = [];

		if ( array_key_exists( 'query', $url_parsed ) ) {

			if ( $url_parsed['query'] === '' ) {

				// this means the URL ends with just ? i.e. /example-page/? - so just remove the last character ? from the URL
				$url = substr( trim( $url ), 0, -1 );

			} else {

				// First parse the query params to an array to manage it better
				parse_str( $url_parsed['query'], $url_query_params );

				// Get the array of query params that would be ignored
				$ignored_query_params = $this->main_instance->get_ignored_query_params();

				// Loop though $ignored_query_params
				foreach ( $ignored_query_params as $ignored_query_param ) {

					// Check if that query param is present in $url_query_params
					if ( array_key_exists( $ignored_query_param, $url_query_params ) ) {

						// The ignored query param is present in the $url_query_params. So, unset it from there
						unset( $url_query_params[ $ignored_query_param ] );
					}
				}

				// Now lets check if we have any query params left in $url_query_params
				if ( count( $url_query_params ) > 0 ) {

					$new_url_query_params = http_build_query( $url_query_params );
					$url_parsed['query']  = $new_url_query_params;

				} else {
					// Remove the query section from parsed URL
					unset( $url_parsed['query'] );
				}

				// Get the new current URL without the marketing query params
				$url = $this->main_instance->get_unparsed_url( $url_parsed );
			}
		}

		return $url;

	}


	function fallback_cache_get_current_page_cache_key( $url = null ) {    
		$replacements = [ '://', '/', '?', '#', '&', '.', ',', '@', '-', '\'', '"', '%', ' ', '\\', '=' ];

		if ( ! is_null( $url ) ) {

			$parts = parse_url( strtolower( $url ) );

			if ( ! $parts ) {
				return false;
			}

			$current_uri = isset( $parts['path'] ) ? $parts['path'] : '/';

			if ( isset( $parts['query'] ) ) {
				$current_uri .= "?{$parts['query']}";
			}

			if ( $current_uri == '/' ) {
				$current_uri = $parts['host'];
			}       
		} else {        
			$current_uri = $_SERVER['REQUEST_URI'];

			if ( $current_uri == '/' ) {
				$current_uri = $_SERVER['HTTP_HOST'];
			}

			$current_uri = trim( $current_uri, '/' );

			if ( strpos( $current_uri, '?' ) === 0 ) {
				$current_uri = $_SERVER['HTTP_HOST'] . $current_uri;
			}
		}

		$cache_key = str_replace( $replacements, '_', $this->fallback_cache_remove_url_parameters( $current_uri ) );
		$cache_key = trim( $cache_key, '_' );
		$cache_key = sha1( $cache_key );

		return $cache_key . '.html';
	}


	function fallback_cache_init_directory() {

		$cache_path = $this->main_instance->get_plugin_wp_content_directory() . '/fallback_cache/';

		if ( ! file_exists( $cache_path ) ) {
			wp_mkdir_p( $cache_path );
		}

		if ( file_exists( $cache_path ) && ! file_exists( "{$cache_path}index.php" ) ) {
			file_put_contents( "{$cache_path}index.php", '<?php // Silence is golden' );
		}

		return $cache_path;

	}


	function fallback_cache_init_ttl_registry() {

		$this->fallback_cache_ttl_registry = get_option( 'swcfpc_fc_ttl_registry', false );

		if ( ! $this->fallback_cache_ttl_registry ) {
			return false;
		}

		// If the option exists, return true
		return true;

	}

	function fallback_cache_update_ttl_registry() {
		update_option( 'swcfpc_fc_ttl_registry', $this->fallback_cache_ttl_registry );
	}

	function fallback_cache_set_single_ttl( $name, $value ) {

		if ( ! is_array( $this->fallback_cache_ttl_registry ) ) {
			$this->fallback_cache_ttl_registry = [];
		}

		if ( is_array( $value ) ) {
			$this->fallback_cache_ttl_registry[ trim( $name ) ] = $value;
		} else {
			$this->fallback_cache_ttl_registry[ trim( $name ) ] = (int) $value;
		}

	}

	function fallback_cache_get_single_ttl( $name, $default = false ) {
		if ( ! is_array( $this->fallback_cache_ttl_registry ) || ! isset( $this->fallback_cache_ttl_registry[ $name ] ) ) {
			return $default;
		}

		if ( is_array( $this->fallback_cache_ttl_registry[ $name ] ) ) {
			return $this->fallback_cache_ttl_registry[ $name ];
		}

		return (int) $this->fallback_cache_ttl_registry[ $name ];
	}

	function fallback_cache_is_expired_page( $cache_key ) {
		$current_ttl = $this->fallback_cache_get_single_ttl( $cache_key, 0 );

		if ( $current_ttl > 0 && time() > $current_ttl ) {
			return true;
		}

		return false;
	}


	function fallback_cache_delete_expired_pages() {
		if ( is_array( $this->fallback_cache_ttl_registry ) ) {

			$cache_path = $this->fallback_cache_init_directory();

			foreach ( $this->fallback_cache_ttl_registry as $cache_key => $ttl ) {

				if ( $this->fallback_cache_is_expired_page( $cache_key ) && file_exists( $cache_path . $cache_key ) ) {
					@unlink( $cache_path . $cache_key );
					unset( $this->fallback_cache_ttl_registry[ $cache_key ] );
				}           
			}

			$this->fallback_cache_update_ttl_registry();
		}
	}


	function fallback_cache_purge_all() {

		$cache_path = $this->fallback_cache_init_directory();

		// Get a list of all of the file names in the folder.
		$files = glob( $cache_path . '/*' );

		foreach ( $files as $file ) {

			if ( is_file( $file ) ) {
				@unlink( $file );
			}       
		}

		$this->fallback_cache_ttl_registry = [];
		$this->fallback_cache_update_ttl_registry();
		do_action( 'swcfpc_fallback_cache_purged_all' );
	}


	function fallback_cache_purge_urls( $urls ) {

		$cache_path = $this->fallback_cache_init_directory();

		if ( is_array( $urls ) ) {

			foreach ( $urls as $single_url ) {

				$cache_key    = $this->fallback_cache_get_current_page_cache_key( $single_url );
				$headers_file = "{$cache_path}{$cache_key}.headers.json";

				if ( file_exists( $cache_path . $cache_key ) ) {
					@unlink( $cache_path . $cache_key );
					unset( $this->fallback_cache_ttl_registry[ $cache_key ] );
				}

				if ( file_exists( $headers_file ) ) {
					@unlink( $headers_file );
				}      
				do_action( 'swcfpc_fallback_cache_purged_url', $cache_key );
			}

			$this->fallback_cache_update_ttl_registry();

		}

	}

	function fallback_cache_retrive_current_page() {
		$cache_path = $this->fallback_cache_init_directory();
		$cache_key  = $this->fallback_cache_get_current_page_cache_key();

		if ( $this->should_prevent_cache_because_of_trailingslash() ) {
			Helpers::bypass_reason_header( 'Not a slashed URL' );

			return false;
		}

		if ( $this->fallback_cache_is_cookie_to_exclude() ) {
			Helpers::bypass_reason_header( 'Excluded cookie' );

			return false;
		}

		if ( file_exists( $cache_path . $cache_key ) && ! $this->fallback_cache_is_expired_page( $cache_key ) ) {

			$this->modules  = $this->main_instance->get_modules();
			$stored_headers = $this->fallback_cache_get_stored_headers( $cache_path, $cache_key );

			if ( $this->main_instance->get_single_config( 'cf_strip_cookies', 0 ) > 0 ) {
				header_remove( 'Set-Cookie' );
			}

			header_remove( 'Pragma' );
			header_remove( 'Expires' );
			header_remove( 'Cache-Control' );
			header( 'Cache-Control: ' . $this->modules['cache_controller']->get_cache_control_value() );
			header( 'X-WP-SPC-Disk-Cache: HIT' );
			header( 'X-WP-CF-Super-Cache-Active: 1' );
			header('X-WP-CF-Super-Cache-Cache-Control: ' . $this->modules['cache_controller']->get_cache_control_value());

			if ( $stored_headers ) {

				foreach ( $stored_headers as $single_header ) {
					header( $single_header, false );
				}           
			}

			die( file_get_contents( $cache_path . $cache_key ) );

		}

		return false;

	}

	/**
	 * Check if the current page is cached and not expired.
	 *
	 * @param string $url The URL to check.
	 *
	 * @return bool
	 */
	function fallback_cache_check_cached_page( $url ) {
		$cache_path = $this->fallback_cache_init_directory();
		$cache_key  = $this->fallback_cache_get_current_page_cache_key( $url );

		return file_exists( $cache_path . $cache_key ) && ! $this->fallback_cache_is_expired_page( $cache_key );
	}


	function fallback_cache_save_headers( $cache_path, $cache_key ) {

		$headers_file = "{$cache_path}{$cache_key}.headers.json";

		$headers_list  = headers_list();
		$headers_count = count( $headers_list );

		for ( $i = 0; $i < $headers_count; ++$i ) {

			list($header_name, $header_value) = explode( ':', $headers_list[ $i ] );

			if (
				strcasecmp( $header_name, 'cache-control' ) == 0 ||
				strcasecmp( $header_name, 'set-cookie' ) == 0 ||
				strcasecmp( substr( $header_name, 0, 19 ), 'X-WP-CF-Super-Cache' ) == 0
			) {
				unset( $headers_list[ $i ] );
				continue;
			}       
		}

		if ( count( $headers_list ) == 0 ) {

			if ( file_exists( $headers_file ) ) {
				@unlink( $headers_file );
			}

			return false;

		}

		file_put_contents( $headers_file, json_encode( $headers_list ) );

		return true;

	}


	function fallback_cache_get_stored_headers( $cache_path, $cache_key ) {

		$headers_file = "{$cache_path}{$cache_key}.headers.json";

		if ( file_exists( $headers_file ) ) {

			$swcfpc_headers = json_decode( file_get_contents( $headers_file ), true );

			if ( is_array( $swcfpc_headers ) && count( $swcfpc_headers ) > 0 ) {
				return $swcfpc_headers;
			}       
		}

		return false;

	}


	function fallback_cache_is_cookie_to_exclude() {

		if ( count( $_COOKIE ) == 0 ) {
			return false;
		}

		$excluded_cookies = $this->main_instance->get_single_config( Constants::SETTING_EXCLUDED_COOKIES, [] );

		if ( count( $excluded_cookies ) == 0 ) {
			return false;
		}

		$cookies = array_keys( $_COOKIE );

		foreach ( $excluded_cookies as $single_cookie ) {

			if ( count( preg_grep( "#{$single_cookie}#", $cookies ) ) > 0 ) {
				Helpers::bypass_reason_header( sprintf( 'Cookie - %s', $single_cookie ) );

				return true;
			}       
		}

		return false;

	}


	function fallback_cache_is_url_to_exclude( $url = false ) {
		if ( $this->should_prevent_cache_because_of_trailingslash() ) {
			return true;
		}

		$excluded_urls = $this->main_instance->get_single_config( 'cf_fallback_cache_excluded_urls', [] );

		if ( is_array( $excluded_urls ) && count( $excluded_urls ) > 0 ) {

			if ( $url === false ) {

				$current_url = $_SERVER['REQUEST_URI'];

				if ( isset( $_SERVER['QUERY_STRING'] ) && strlen( $_SERVER['QUERY_STRING'] ) > 0 ) {
					$current_url .= "?{$_SERVER['QUERY_STRING']}";
				}           
			} else {
				$current_url = $url;
			}

			foreach ( $excluded_urls as $url_to_exclude ) {

				if ( $this->main_instance->wildcard_match( $url_to_exclude, $current_url ) ) {
					return true;
				}

				/*
				if( fnmatch($url_to_exclude, $current_url, FNM_CASEFOLD) ) {
					return true;
				}
				*/

			}       
		}

		return false;
	}

	/**
	 * Check if the current URL should be prevented from being cached or delivered from cache because of the trailing slash.
	 * 
	 * @return bool
	 */
	private function should_prevent_cache_because_of_trailingslash() {
		return $this->main_instance->get_single_config( 'cf_fallback_cache_prevent_cache_urls_without_trailing_slash', 1 ) > 0 &&
		! $this->main_instance->does_current_url_have_trailing_slash() &&
		apply_filters( 'swcfpc_fallback_cache_skip_unslashed', true, $_SERVER['REQUEST_URI'] );
	}

}
