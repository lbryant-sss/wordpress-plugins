<?php
/**
 * Handles the parameters and URLs
 *
 * @file The Tools file
 * @package HMWP/Tools
 * @since 4.0.0
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class HMWP_Classes_Tools {

	/**
	 * Saved options in database.
	 *
	 * @var array
	 */
	public static $init = array(), $default = array(), $lite = array();
	/**
	 * Configuration settings for the application.
	 *
	 * @var array
	 */
	public static $options = array();
	/**
	 * Stores debugging information and configurations.
	 *
	 * @var array
	 */
	public static $debug = array();
	/**
	 * List of active plugins in the system.
	 *
	 * @var array
	 */
	public static $active_plugins;

	/**
	 * Represents the role of the current user.
	 *
	 * @var string
	 */
	static $current_user_role = 'default';

	/**
	 * Plugin constructor to initialize options, load multilanguage support, and handle admin-specific tasks.
	 *
	 * @return void
	 */
	public function __construct() {

		// Get the plugin options from database
		self::$options = self::getOptions();

		// Load multilanguage
		add_action( "init", array( $this, 'loadMultilanguage' ) );

		// If it's admin panel
		if ( is_admin() || is_network_admin() ) {
			// Check the Plugin database update
			self::updateDatabase();

			// Add setting link in plugin
			add_filter( 'plugin_action_links_' . HMWP_BASENAME, array( $this, 'hookActionlink' ) );
			add_filter( 'network_admin_plugin_action_links_' . HMWP_BASENAME, array( $this, 'hookActionlink' ) );

			// Check plugin license
			add_action( 'request_metadata_http_result', array( $this, 'checkLicenseOnUpdate' ) );

		}

	}

	/**
	 * Load the Options from user option table in DB
	 *
	 * @param  bool  $safe
	 *
	 * @return array
	 */
	public static function getOptions( $safe = false ) {
		// Set key metadata based on safe parameter.
		$keymeta = $safe ? HMWP_OPTION_SAFE : HMWP_OPTION;

		// Parse the site URL and plugin/content paths.
		$homepath   = wp_parse_url( site_url(), PHP_URL_PATH ) ? ltrim( wp_parse_url( site_url(), PHP_URL_PATH ), '/' ) : '';
		$pluginurl  = ltrim( wp_parse_url( plugins_url(), PHP_URL_PATH ), '/' );
		$contenturl = ltrim( wp_parse_url( content_url(), PHP_URL_PATH ), '/' );

		// Get relative paths for plugin and content URLs.
		$plugin_relative_url  = trim( preg_replace( '/' . str_replace( '/', '\/', $homepath ) . '/', '', $pluginurl, 1 ), '/' );
		$content_relative_url = trim( preg_replace( '/' . str_replace( '/', '\/', $homepath ) . '/', '', $contenturl, 1 ), '/' );

		// Set default options.
		self::$init = array(
			'hmwp_ver'                       => 0,
			//--
			'api_token'                      => false,
			'hmwp_token'                     => false,
			//--
			'hmwp_valid'                     => 1,
			'hmwp_expires'                   => 0,
			'hmwp_disable'                   => HMWP_Classes_Tools::generateRandomString( 16 ),
			'hmwp_disable_name'              => HMWP_Classes_Tools::generateRandomString( 16 ),
			//--
			'hmwp_plugin_name'               => _HMWP_PLUGIN_FULL_NAME_,
			'hmwp_plugin_menu'               => _HMWP_PLUGIN_FULL_NAME_,
			'hmwp_plugin_logo'               => false,
			'hmwp_plugin_icon'               => 'dashicons-shield-alt',
			'hmwp_plugin_website'            => 'https://wpghost.com',
			'hmwp_plugin_account_show'       => 1,
			//--
			'logout'                         => 0,
			'error'                          => 0,
			'file_mappings'                  => array(),
			'test_frontend'                  => 0,
			'changes'                        => 0,
			'admin_notice'                   => array(),
			'prevent_slow_loading'           => 1,
			'hmwp_rewrites_in_wp_rules'      => 0,
			'hmwp_server_type'               => 'auto',
			//--
			'hmwp_loading_hook'              => array( 'normal' ), //load when the other plugins are initialized
			'hmwp_firstload'                 => 0, //load the plugin as Must Use Plugin
			'hmwp_priorityload'              => 0, //load the plugin on plugin start
			'hmwp_laterload'                 => 0, //load the plugin on template redirect
			//--
			'hmwp_fix_relative'              => 0,
			'hmwp_remove_third_hooks'        => 0,
			'hmwp_send_email'                => 0,
			'hmwp_activity_log'              => 0,
			'hmwp_activity_log_roles'        => array(),
			'hmwp_email_address'             => '',

			//-- Firewall
			'whitelist_ip'                   => array(),
			'whitelist_paths'                => 0,
			'whitelist_urls'                 => array(),
			'banlist_ip'                     => array(),
			'banlist_hostname'               => array(),
			'banlist_user_agent'             => array(),
			'banlist_referrer'               => array(),

			//Temporary Login
			'hmwp_templogin'                 => 0,
			'hmwp_templogin_role'            => 'administrator',
			'hmwp_templogin_redirect'        => false,
			'hmwp_templogin_delete_uninstal' => false,

			//Geoblock Login
			'hmwp_geoblock'                  => 0,
			'hmwp_geoblock_countries'        => array(),
			'hmwp_geoblock_urls'             => array(),

			//2FA Login
			'hmwp_2falogin'                  => 0,
			'hmwp_2falogin_status'           => 1,
			'hmwp_2fa_totp'                  => 1,
			'hmwp_2fa_email'                 => 0,
			'hmwp_2falogin_max_attempts'     => 5,
			'hmwp_2falogin_max_timeout'      => 900,
			'hmwp_2falogin_message'          => '',
			'hmwp_2falogin_fail_message'     => '',

			//-- Brute Force
			'hmwp_bruteforce'                => 0,
			'hmwp_bruteforce_login'          => 1,
			'hmwp_bruteforce_lostpassword'   => 0,
			'hmwp_bruteforce_register'       => 0,
			'hmwp_bruteforce_comments'       => 0,
			'hmwp_bruteforce_woocommerce'    => 0,
			'hmwp_bruteforce_username'       => 0,
			'hmwp_brute_message'             => 'Your IP has been flagged for potential security violations. Please try again in a little while.',
			'hmwp_hide_classes'              => json_encode( array() ),
			'trusted_ip_header'              => '',

			//Unique Login
			'hmwp_uniquelogin'               => 0,
			'hmwp_uniquelogin_woocommerce'   => 0,

			//Math reCaptcha
			'brute_use_math'                 => 1,
			'brute_max_attempts'             => 5,
			'brute_max_timeout'              => 3600,
			//reCaptcha Google
			'brute_use_google_enterprise'    => 0,
			'brute_use_google'               => 0,
			'brute_google_checkbox'          => 0,
			'brute_google_project_id'        => '',
			'brute_google_api_key'           => '',
			'brute_google_site_key'          => '',
			'brute_google_language'          => '',
			//reCaptcha V2
			'brute_use_captcha'              => 0,
			'brute_captcha_site_key'         => '',
			'brute_captcha_secret_key'       => '',
			'brute_captcha_theme'            => 'light',
			'brute_captcha_language'         => '',
			//reCaptcha V2
			'brute_use_captcha_v3'           => 0,
			'brute_captcha_site_key_v3'      => '',
			'brute_captcha_secret_key_v3'    => '',

			//tweaks
			'hmwp_hide_admin_toolbar'        => 0,
			'hmwp_hide_admin_toolbar_roles'  => array( 'customer', 'subscriber' ),
			//--
			'hmwp_change_in_cache'           => ( ( defined( 'WP_CACHE' ) && WP_CACHE ) ? 1 : 0 ),
			'hmwp_change_in_cache_directory' => '',
			'hmwp_hide_loggedusers'          => 1,
			'hmwp_hide_version'              => 1,
			'hmwp_hide_version_random'       => 1,
			'hmwp_hide_generator'            => 1,
			'hmwp_hide_prefetch'             => 1,
			'hmwp_hide_comments'             => 1,
			'hmwp_hide_wp_text'              => 0,

			'hmwp_hide_feed'              => 0,
			'hmwp_hide_in_feed'           => 0,
			'hmwp_hide_in_sitemap'        => 0,
			'hmwp_hide_author_in_sitemap' => 1,
			'hmwp_robots'                 => 0,

			'hmwp_disable_emojicons'         => 0,
			'hmwp_disable_manifest'          => 1,
			'hmwp_disable_embeds'            => 0,
			'hmwp_disable_debug'             => 1,
			//--
			'hmwp_disable_click'             => 0,
			'hmwp_disable_click_loggedusers' => 0,
			'hmwp_disable_click_roles'       => array( 'subscriber' ),
			'hmwp_disable_click_message'     => "Right click is disabled!",

			'hmwp_disable_inspect'             => 0,
			'hmwp_disable_inspect_blank'       => 0,
			'hmwp_disable_inspect_loggedusers' => 0,
			'hmwp_disable_inspect_roles'       => array( 'subscriber' ),
			'hmwp_disable_inspect_message'     => "Inspect Element is disabled!",

			'hmwp_disable_source'             => 0,
			'hmwp_disable_source_loggedusers' => 0,
			'hmwp_disable_source_roles'       => array( 'subscriber' ),
			'hmwp_disable_source_message'     => "View Source is disabled!",

			'hmwp_disable_copy_paste'             => 0,
			'hmwp_disable_paste'                  => 1,
			'hmwp_disable_copy_paste_loggedusers' => 0,
			'hmwp_disable_copy_paste_roles'       => array( 'subscriber' ),
			'hmwp_disable_copy_paste_message'     => "Copy/Paste is disabled!",

			'hmwp_disable_drag_drop'             => 0,
			'hmwp_disable_drag_drop_loggedusers' => 0,
			'hmwp_disable_drag_drop_roles'       => array( 'subscriber' ),
			'hmwp_disable_drag_drop_message'     => "Drag-n-Drop is disabled!",

			'hmwp_disable_recording'             => 0,
			'hmwp_disable_recording_loggedusers' => 0,
			'hmwp_disable_recording_roles'       => array( 'subscriber' ),
			'hmwp_disable_recording_message'     => "Screen Recording is disabled!",
			//--
			'hmwp_disable_screen_capture'        => 0,
			'hmwp_file_cache'                    => 0,
			'hmwp_url_mapping'                   => json_encode( array() ),
			'hmwp_mapping_classes'               => 1,
			'hmwp_mapping_file'                  => 0,
			'hmwp_text_mapping'                  => json_encode( array(
				'from' => array(),
				'to'   => array(),
			) ),
			'hmwp_cdn_urls'                      => json_encode( array() ),
			'hmwp_security_alert'                => 1,
			//--
			'hmwp_hide_plugins_advanced'         => 0,
			'hmwp_hide_themes_advanced'          => 0,
			//--

			//redirects
			'hmwp_url_redirect'                  => 'NFError',
			'hmwp_do_redirects'                  => 0,
			'hmwp_logged_users_redirect'         => 0,
			'hmwp_url_redirects'                 => array( 'default' => array( 'login' => '', 'logout' => '' ) ),
			'hmwp_signup_template'               => 0,

			'hmwp_mapping_text_show' => 1,
			'hmwp_mapping_url_show'  => 1,
			'hmwp_mapping_cdn_show'  => 1,

		);

		// Set WordPress options when security is disables.
		self::$default = array(
			'hmwp_mode'             => 'default',
			'hmwp_admin_url'        => 'wp-admin',
			'hmwp_login_url'        => 'wp-login.php',
			'hmwp_activate_url'     => 'wp-activate.php',
			'hmwp_lostpassword_url' => '',
			'hmwp_register_url'     => '',
			'hmwp_logout_url'       => '',

			'hmwp_plugin_url'                => $plugin_relative_url,
			'hmwp_plugins'                   => array(),
			'hmwp_themes_url'                => 'themes',
			'hmwp_themes'                    => array(),
			'hmwp_upload_url'                => 'uploads',
			'hmwp_admin-ajax_url'            => 'admin-ajax.php',
			'hmwp_wp-signup_url'             => 'wp-signup.php',
			'hmwp_hideajax_paths'            => 0,
			'hmwp_hideajax_admin'            => 0,
			'hmwp_tags_url'                  => 'tag',
			'hmwp_wp-content_url'            => $content_relative_url,
			'hmwp_wp-includes_url'           => 'wp-includes',
			'hmwp_author_url'                => 'author',
			'hmwp_hide_authors'              => 0,
			'hmwp_wp-comments-post'          => 'wp-comments-post.php',
			'hmwp_themes_style'              => 'style.css',
			'hmwp_hide_img_classes'          => 0,
			'hmwp_hide_styleids'             => 0,
			'hmwp_noncekey'                  => '_wpnonce',
			'hmwp_wp-json'                   => 'wp-json',
			'hmwp_hide_rest_api'             => 0,
			'hmwp_disable_rest_api'          => 0,
			'hmwp_disable_rest_api_param'    => 0,
			'hmwp_disable_xmlrpc'            => 0,
			'hmwp_hide_rsd'                  => 0,
			'hmwp_hide_admin'                => 0,
			'hmwp_hide_newadmin'             => 0,
			'hmwp_hide_admin_loggedusers'    => 0,
			'hmwp_hide_login'                => 0,
			'hmwp_hide_wplogin'              => 0,
			'hmwp_hide_newlogin'             => 0,
			'hmwp_disable_language_switcher' => 0,
			'hmwp_hide_plugins'              => 0,
			'hmwp_hide_all_plugins'          => 0,
			'hmwp_hide_themes'               => 0,
			'hmwp_hide_all_themes'           => 0,
			'hmwp_emulate_cms'               => '',

			//--secure headers
			'hmwp_sqlinjection'              => 0,
			'hmwp_sqlinjection_location'     => 'onload',
			'hmwp_sqlinjection_level'        => 2,
			'hmwp_security_header'           => 0,
			'hmwp_hide_unsafe_headers'       => 0,
			'hmwp_security_headers'          => array(
				"Strict-Transport-Security" => "max-age=15768000;includeSubdomains",
				"Content-Security-Policy"   => "object-src 'none'",
				"X-XSS-Protection"          => "1; mode=block",
			),
			//--
			'hmwp_detectors_block'           => 0,
			'hmwp_hide_commonfiles'          => 0,
			'hmwp_disable_browsing'          => 0,
			'hmwp_hide_oldpaths'             => 0,
			'hmwp_hide_oldpaths_plugins'     => 0,
			'hmwp_hide_oldpaths_themes'      => 0,
			'hmwp_hide_oldpaths_types'       => array( 'css', 'js', 'php', 'txt', 'html' ),
			'hmwp_hide_commonfiles_files'    => array(
				'wp-config-sample.php',
				'readme.html',
				'readme.txt',
				'install.php',
				'license.txt',
				'php.ini',
				'upgrade.php',
				'bb-config.php',
				'error_log'
			),
		);

		// Set options for "Lite Mode".
		self::$lite = array(
			'hmwp_mode'                      => 'lite',
			'hmwp_login_url'                 => 'newlogin',
			'hmwp_activate_url'              => 'activate',
			'hmwp_lostpassword_url'          => 'lostpass',
			'hmwp_register_url'              => 'register',
			'hmwp_logout_url'                => '',
			'hmwp_admin-ajax_url'            => 'admin-ajax.php',
			'hmwp_hideajax_admin'            => 0,
			'hmwp_hideajax_paths'            => 0,
			'hmwp_plugin_url'                => 'core/modules',
			'hmwp_themes_url'                => 'core/views',
			'hmwp_upload_url'                => 'storage',
			'hmwp_wp-content_url'            => 'core',
			'hmwp_wp-includes_url'           => 'lib',
			'hmwp_author_url'                => 'writer',
			'hmwp_hide_authors'              => 1,
			'hmwp_wp-comments-post'          => 'comments',
			'hmwp_themes_style'              => 'design.css',
			'hmwp_wp-json'                   => 'wp-json',
			'hmwp_hide_admin'                => 1,
			'hmwp_hide_newadmin'             => 0,
			'hmwp_hide_admin_loggedusers'    => 0,
			'hmwp_hide_login'                => 1,
			'hmwp_hide_wplogin'              => 1,
			'hmwp_hide_newlogin'             => 1,
			'hmwp_disable_language_switcher' => 0,
			'hmwp_hide_plugins'              => 1,
			'hmwp_hide_all_plugins'          => ( self::isMultisites() ? 1 : 0 ),
			'hmwp_hide_themes'               => 1,
			'hmwp_hide_all_themes'           => ( self::isMultisites() ? 1 : 0 ),
			'hmwp_emulate_cms'               => 'drupal11',
			//
			'hmwp_hide_img_classes'          => 1,
			'hmwp_hide_rest_api'             => 1,
			'hmwp_disable_rest_api'          => 0,
			'hmwp_disable_rest_api_param'    => 1,
			'hmwp_disable_xmlrpc'            => 1,
			'hmwp_hide_rsd'                  => 1,
			//
			'hmwp_sqlinjection'              => 1,
			'hmwp_security_header'           => 1,
			'hmwp_hide_unsafe_headers'       => 1,
			'hmwp_detectors_block'           => 1,

			//PRO
			'hmwp_hide_styleids'             => 0,
			'hmwp_disable_browsing'          => 0,
			'hmwp_hide_commonfiles'          => 0,
			'hmwp_hide_oldpaths'             => 0,
			'hmwp_hide_oldpaths_plugins'     => 0,
			'hmwp_hide_oldpaths_themes'      => 0,
		);

		// Fetch the options based on whether it's a multisite and merge with defaults.
		if ( self::isMultisites() && defined( 'BLOG_ID_CURRENT_SITE' ) ) {
			$options = json_decode( get_blog_option( BLOG_ID_CURRENT_SITE, $keymeta ), true );
		} else {
			$options = json_decode( get_option( $keymeta ), true );
		}

		// Ensure compatibility with WP Client plugin.
		if ( self::isPluginActive( 'wp-client/wp-client.php' ) ) {
			self::$lite['hmwp_wp-content_url'] = 'include';
		}

		// Merge the options with initial and default values.
		if ( is_array( $options ) ) {
			$options = @array_merge( self::$init, self::$default, $options );
		} else {
			$options = @array_merge( self::$init, self::$default );
		}

		// Validate the custom cache directory and reset if it contains 'wp-content'.
		if ( isset( $options['hmwp_change_in_cache_directory'] ) && $options['hmwp_change_in_cache_directory'] <> '' ) {
			if ( strpos( $options['hmwp_change_in_cache_directory'], 'wp-content' ) !== false ) {
				$options['hmwp_change_in_cache_directory'] = '';
			}
		}

		// Update the whitelist level based on whitelist paths setting.
		if ( isset( $options['whitelist_paths'] ) && ! isset( $options['whitelist_level'] ) ) {
			$options['whitelist_level'] = ( $options['whitelist_paths'] == 1 ? 2 : 1 );
		}

		// Set priority and rewrite rules settings if defined constants are set.
		if ( HMW_PRIORITY ) {
			$options['hmwp_priorityload'] = 1;
		}
		if ( HMW_RULES_IN_WP_RULES ) {
			$options['hmwp_rewrites_in_wp_rules'] = 1;
		}
		if ( HMW_DYNAMIC_FILES ) {
			$options['hmwp_mapping_file'] = 1;
		}

		// Return the final options array.
		return $options;
	}

	/**
	 * Update the database configuration and options for the plugin.
	 *
	 * This method is called during a plugin update to migrate existing settings and set new defaults.
	 * It handles various tasks such as upgrading from a lite version, migrating specific options,
	 * and initializing default values where necessary.
	 *
	 * @return void
	 */
	private static function updateDatabase() {
		// Check if the plugin version is updated
		if ( self::$options['hmwp_ver'] < HMWP_VERSION_ID ) {

			// Upgrade from Old Version if hmwp_options exist in the database
			if ( get_option( 'hmw_options' ) ) {
				$options = json_decode( get_option( 'hmw_options' ), true );
				// If options are not empty, migrate them to the new format
				if ( ! empty( $options ) ) {
					foreach ( $options as $key => $value ) {
						self::$options[ str_replace( 'hmw_', 'hmwp_', $key ) ] = $value;
					}
				}
				// Delete old options to prevent conflicts
				delete_option( 'hmw_options' );
			}

			// Set default value for hmwp_hide_wplogin if it's not set and hmwp_hide_login is set
			if ( ! isset( self::$options['hmwp_hide_wplogin'] ) && isset( self::$options['hmwp_hide_login'] ) && self::$options['hmwp_hide_login'] ) {
				self::$options['hmwp_hide_wplogin'] = self::$options['hmwp_hide_login'];
			}

			// Initialize the account show option if not set
			if ( ! isset( self::$options['hmwp_plugin_account_show'] ) ) {
				self::$options['hmwp_plugin_account_show'] = 1;
			}

			// Upgrade logout redirect options to the new format
			if ( isset( self::$options['hmwp_logout_redirect'] ) && self::$options['hmwp_logout_redirect'] ) {
				self::$options['hmwp_url_redirects']['default']['logout'] = self::$options['hmwp_logout_redirect'];
				unset( self::$options['hmwp_logout_redirect'] );
			}

			// Upgrade admin toolbar visibility option to the new format
			if ( isset( self::$options['hmwp_in_dashboard'] ) && self::$options['hmwp_in_dashboard'] ) {
				self::$options['hmwp_hide_admin_toolbar'] = self::$options['hmwp_in_dashboard'];
				unset( self::$options['hmwp_in_dashboard'] );
			}

			// Upgrade sitemap visibility option to the new format
			if ( isset( self::$options['hmwp_shutdownload'] ) && self::$options['hmwp_shutdownload'] ) {
				self::$options['hmwp_hide_in_sitemap'] = self::$options['hmwp_shutdownload'];
				unset( self::$options['hmwp_shutdownload'] );
			}

			// Remove old whitelist_paths option
			if ( isset( self::$options['whitelist_paths'] ) ) {
				unset( self::$options['whitelist_paths'] );
			}

			// Update the login paths on Cloud when the plugin is updated
			self::sendLoginPathsApi();

			// Set the current version ID
			self::$options['hmwp_ver'] = HMWP_VERSION_ID;
			// Save updated options
			self::saveOptions();
		}
	}

	/**
	 * Get the default value for a given key
	 *
	 * @param  string  $key  The key for which default value is to be retrieved
	 *
	 * @return mixed The default value associated with the key, or false if the key does not exist
	 * @since 5.0.19
	 */
	public static function getDefault( $key ) {
		if ( isset( self::$default[ $key ] ) ) {
			return self::$default[ $key ];
		}

		return false;

	}

	/**
	 * Retrieve an option value by key.
	 *
	 * @param  string  $key  The key for the option to retrieve.
	 *
	 * @return mixed The value of the option, possibly filtered.
	 */
	public static function getOption( $key ) {
		if ( ! isset( self::$options[ $key ] ) ) {
			self::$options = self::getOptions();

			if ( ! isset( self::$options[ $key ] ) ) {
				self::$options[ $key ] = 0;
			}
		}

		return apply_filters( 'hmwp_option_' . $key, self::$options[ $key ] );
	}

	/**
	 * Save the specified options in the WordPress options table
	 *
	 * @param  string|null  $key  The key of the option to save. If null, no key will be set.
	 * @param  mixed  $value  The value of the option to save.
	 * @param  bool  $safe  Whether to save the option safely or not.
	 *
	 * @return void
	 */
	public static function saveOptions( $key = null, $value = '', $safe = false ) {
		// Default option key
		$keymeta = HMWP_OPTION;

		// Use a different option key if the $safe parameter is true
		if ( $safe ) {
			$keymeta = HMWP_OPTION_SAFE;
		}

		// If a specific key is provided, update the value in the options array
		if ( isset( $key ) ) {
			self::$options[ $key ] = $value;
		}

		// If the site is a multisite and BLOG_ID_CURRENT_SITE is defined
		if ( self::isMultisites() && defined( 'BLOG_ID_CURRENT_SITE' ) ) {
			// Update the option for the current blog in the network
			update_blog_option( BLOG_ID_CURRENT_SITE, $keymeta, json_encode( self::$options ) );
		} else {
			// Otherwise, update the option normally
			update_option( $keymeta, json_encode( self::$options ) );
		}
	}

	/**
	 * Save the current working options into a backup storage.
	 *
	 * @return void
	 */
	public static function saveOptionsBackup() {
		// Save the working options into backup
		foreach ( self::$options as $key => $value ) {
			HMWP_Classes_Tools::saveOptions( $key, $value, true );
		}
	}

	/**
	 * Add a link to settings in the plugin list
	 *
	 * @param  array  $links
	 *
	 * @return array
	 */
	public function hookActionlink( $links ) {
		if ( HMWP_Classes_Tools::userCan( HMWP_CAPABILITY ) ) {
			if ( get_transient( 'hmwp_disable' ) ) {
				$links[] = '<a href="' . esc_url( add_query_arg( array( 'hmwp_nonce' => wp_create_nonce( 'hmwp_pause_disable' ), 'action' => 'hmwp_pause_disable' ) ) ) . '" class="btn btn-default btn-sm mt-3" />' . esc_html__( "Resume Security", 'hide-my-wp' ) . '</a>';
			} else {
				$links[] = '<a href="' . esc_url( add_query_arg( array( 'hmwp_nonce' => wp_create_nonce( 'hmwp_pause_enable' ), 'action' => 'hmwp_pause_enable' ) ) ) . '" class="btn btn-default btn-sm mt-3" />' . esc_html__( "Pause for 5 minutes", 'hide-my-wp' ) . '</a>';
			}
			$links[] = '<a href="' . esc_url( self::getSettingsUrl() ) . '">' . esc_html__( 'Settings', 'hide-my-wp' ) . '</a>';
		}
		$links[] = '<a href="https://hidemywpghost.com/hide-my-wp-pricing/" target="_blank" style="font-weight: bold;color: #007cba">' . esc_html__( 'Go PRO', 'hide-my-wp' ) . '</a>';

		return array_reverse( $links );
	}


	/**
	 * Load the plugin text domain for multilanguage support.
	 *
	 * @return void
	 */
	public static function loadMultilanguage() {

		if ( function_exists('get_locale') ){
			$locale = get_locale();

			if ( $locale !== 'en_US' ) {
				load_plugin_textdomain( dirname( HMWP_BASENAME ), false, dirname( HMWP_BASENAME ) . '/languages/' );
			}
		}

	}

	/**
	 * Check if it's Rest Api call
	 *
	 * @return bool
	 */
	public static function isApi() {
		if ( isset( $_SERVER['REQUEST_URI'] ) && strpos( $_SERVER['REQUEST_URI'], '/' . HMWP_Classes_Tools::getOption( 'hmwp_wp-json' ) . '/' ) !== false ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if it's Ajax call
	 *
	 * @return bool
	 */
	public static function isAjax() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if it's Cron call
	 *
	 * @return bool
	 */
	public static function isCron() {
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if it's valid to load firewall on the page
	 *
	 * @return bool
	 */
	public static function doFirewall() {

		// If safe URL is called
		if ( self::calledSafeUrl() ){
			return false;
		}

		//If allways change paths admin & frontend
		if ( defined( 'HMW_ALWAYS_RUN_FIREWALL' ) && HMW_ALWAYS_RUN_FIREWALL ) {
			return true;
		}

		//If firewall process is activated
		if ( ! apply_filters( 'hmwp_process_firewall', true ) ) {
			return false;
		}

		if ( HMWP_Classes_Tools::isApi() ) {
			return false;
		}

		//If not admin
		if ( ! is_admin() && ! is_network_admin() ) {
			//if user is not logged in
			if ( function_exists( 'is_user_logged_in' ) && ! is_user_logged_in() ) {
				return true;
			}

		}

		return false;
	}

	/**
	 * Check if it's valid for changing the paths
	 * Change the paths in admin, logged users or visitors
	 *
	 * @return bool
	 */
	public static function doChangePaths() {

		//If allways change paths admin & frontend
		if ( HMW_ALWAYS_CHANGE_PATHS ) {
			return true;
		}

		// If safe URL is called
		if ( self::calledSafeUrl() ){
			return false;
		}

		if ( HMWP_Classes_Tools::isApi() ) {
			return false;
		}

		//If not admin
		if ( ( ! is_admin() && ! is_network_admin() ) || HMWP_Classes_Tools::isAjax() ) {

			//if process the change paths
			if ( HMWP_Classes_Tools::getOption( 'hmwp_hide_loggedusers' ) || ( function_exists( 'is_user_logged_in' ) && ! is_user_logged_in() ) ) {
				return true;
			}

		}

		return false;
	}

	/**
	 * Check if it's valid for hiding and disable things in site
	 *
	 * @return bool
	 */
	public static function doHideDisable() {

		//Check if is valid for moving on
		if ( ! apply_filters( 'hmwp_process_hide_disable', true ) ) {
			return false;
		}

		// If safe URL is called
		if ( self::calledSafeUrl() ){
			return false;
		}

		if ( self::isCron() ) {
			return false;
		}

		if ( HMWP_Classes_Tools::isAjax() || HMWP_Classes_Tools::isApi() ) {
			return false;
		}

		//If not admin
		if ( ! is_admin() && ! is_network_admin() ) {
			//if process the change paths
			if ( HMWP_Classes_Tools::getOption( 'hmwp_hide_loggedusers' ) || ( function_exists( 'is_user_logged_in' ) && ! is_user_logged_in() ) ) {
				return true;
			}

		}

		return false;
	}

	/**
	 * Check if it's valid for click disabl, source code and inspect element
	 *
	 * @return bool
	 */
	public static function doDisableClick() {

		//Check if is valid for moving on
		if ( ! apply_filters( 'hmwp_process_hide_disable', true ) ) {
			return false;
		}

		// If safe URL is called
		if ( self::calledSafeUrl() ){
			return false;
		}

		if ( self::isCron() ) {
			return false;
		}

		//If not admin
		if ( ! is_admin() && ! is_network_admin() ) {

			if ( function_exists( 'is_user_logged_in' ) && ( HMWP_Classes_Tools::getOption( 'hmwp_disable_click' ) || HMWP_Classes_Tools::getOption( 'hmwp_disable_inspect' ) || HMWP_Classes_Tools::getOption( 'hmwp_disable_source' ) || HMWP_Classes_Tools::getOption( 'hmwp_disable_copy_paste' ) || HMWP_Classes_Tools::getOption( 'hmwp_disable_drag_drop' ) ) ) {

				return true;
			}

		}

		return false;
	}

	/**
	 * Check if the option to hide the URLs is active
	 *
	 * @return bool
	 */
	public static function doHideURLs() {

		//Check if is valid for moving on
		if ( ! apply_filters( 'hmwp_process_hide_urls', true ) ) {
			return false;
		}

		// If safe URL is called
		if ( self::calledSafeUrl() ){
			return false;
		}

		//make sure the function is loaded
		if ( ! function_exists( 'is_user_logged_in' ) ) {
			include_once ABSPATH . WPINC . '/pluggable.php';
		}

		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		if ( self::isCron() ) {
			return false;
		}

		return true;
	}

	/**
	 * Called the Safe URL
	 *
	 * @return bool
	 */
	public static function calledSafeUrl() {

		// If safe parameter is set, clear the banned IPs and let the default paths
		if ( HMWP_Classes_Tools::getIsset( HMWP_Classes_Tools::getOption( 'hmwp_disable_name' ) ) ) {
			if ( HMWP_Classes_Tools::getValue( HMWP_Classes_Tools::getOption( 'hmwp_disable_name' ) ) == HMWP_Classes_Tools::getOption( 'hmwp_disable' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the plugin settings URL
	 *
	 * @param  string  $page
	 * @param  string  $relative
	 *
	 * @return string
	 */
	public static function getSettingsUrl( $page = 'hmwp_settings', $relative = false ) {
		if ( $relative ) {
			return 'admin.php?page=' . $page;
		} else {
			if ( ! self::isMultisites() ) {
				return admin_url( 'admin.php?page=' . $page );
			} else {
				return network_admin_url( 'admin.php?page=' . $page );
			}
		}
	}

	public static function getCloudUrl( $page = 'login' ) {
		return _HMWP_ACCOUNT_SITE_ . '/user/' . $page;
	}

	/**
	 * Get the absolute filesystem path to the config root of the WordPress installation
	 *
	 * @return string Full filesystem path to the root of the WordPress installation
	 */
	public static function getRootPath() {

		// Get the absolute path by default
		$root_path = str_replace( '\\', '/', ABSPATH );

		if ( _HMWP_CONFIG_DIR_ ) {

			// If it's defined by the user in wp-config.php
			$root_path = str_replace( '\\', '/', _HMWP_CONFIG_DIR_ );

		} elseif ( HMWP_Classes_Tools::isMultisites() ) {

			// Fix the root path on Multisite
			$document_root_fix = str_replace( '\\', '/', realpath( $_SERVER['DOCUMENT_ROOT'] ) );
			$slashed_home      = trailingslashit( get_option( 'home' ) );
			$base              = parse_url( $slashed_home, PHP_URL_PATH );
			$root_path         = ('' === $document_root_fix || 0 === strpos( $root_path, $document_root_fix )) ? $document_root_fix . $base : get_home_path();

		} elseif ( self::isFlywheel() && defined( 'WP_CONTENT_DIR' ) && dirname( WP_CONTENT_DIR ) ) {

			// If is Flywheel server and the content dir is defined
			$root_path = str_replace( '\\', '/', dirname( WP_CONTENT_DIR ) );

		}

		// Let third party to modify the config root path
		return apply_filters( 'hmwp_root_path', trailingslashit( $root_path ) );

	}

	/**
	 * Get the relative path to the home root of the WordPress installation
	 *
	 * @return string Full filesystem path to the root of the WordPress installation
	 */
	public static function getHomeRootPath() {
		$home_root = '/';

		// If it's multisite amd the main site path is defined
		if ( HMWP_Classes_Tools::isMultisites() && defined( 'PATH_CURRENT_SITE' ) ) {
			// Set the home root path as the main website
			$path = PATH_CURRENT_SITE;
		} else {
			// Set the home root path from the site url
			$path = wp_parse_url( site_url(), PHP_URL_PATH );
		}

		if ( $path ) {
			// If there is a sub-path ...
			$home_root = trailingslashit( $path );
		}

		return apply_filters( 'hmwp_home_root', $home_root );
	}

	/**
	 * Get the config file for WordPress
	 *
	 * @return string
	 */
	public static function getConfigFile() {

		//Initialize WordPress Filesystem
		$wp_filesystem = HMWP_Classes_ObjController::initFilesystem();

		// Check config file in the root directory
		if ( $wp_filesystem->exists( trailingslashit(self::getRootPath()) . 'wp-config.php' ) ) {
			return trailingslashit(self::getRootPath()) . 'wp-config.php';
		}

		// Get the absolute path by default
		$abs_path = str_replace( '\\', '/', ABSPATH );

		// Check config file in absolute path
		if ( $wp_filesystem->exists( $abs_path . 'wp-config.php' ) ) {
			return $abs_path . 'wp-config.php' ;
		}

		// Check config file in the parent path
		if ( $wp_filesystem->exists( dirname( $abs_path ) . '/wp-config.php' ) ) {
			return dirname( $abs_path ) . '/wp-config.php' ;
		}

		return false;
	}

	/**
	 * Set the header type
	 *
	 * @param  string  $type
	 */
	public static function setHeader( $type ) {
		switch ( $type ) {
			case 'json':
				header( 'Content-Type: application/json' );
				break;
			case 'html':
				header( "Content-type: text/html" );
				break;
			case 'text':
				header( "Content-type: text/plain" );
				break;
		}
	}

	/**
	 * Get a value from $_POST / $_GET
	 * if unavailable, take a default value
	 *
	 * @param  string  $key  Value key
	 * @param  boolean  $keep_newlines  Keep the new lines in variable in case of texareas
	 * @param  mixed  $defaultValue  (optional)
	 *
	 * @return array|false|string Value
	 */
	public static function getValue( $key = null, $defaultValue = false, $keep_newlines = false ) {
		if ( ! isset( $key ) || $key == '' ) {
			return false;
		}

		//Get the parameters based on the form method
		//Sanitize each parameter based on the parameter type
		$ret = ( isset( $_POST[ $key ] ) ? $_POST[ $key ] : ( isset( $_GET[ $key ] ) ? $_GET[ $key ] : $defaultValue ) );

		if ( is_string( $ret ) === true ) {
			if ( $keep_newlines === false ) {
				//Validate the param based on its type
				if ( in_array( $key, array( 'hmwp_email_address', 'hmwp_email', 'whitelist_ip', 'banlist_ip' ) ) ) { //validate email address
					$ret = preg_replace( '/[^A-Za-z0-9-_.+*#:~@\!\'\/]/', '', $ret );
				} elseif ( in_array( $key, array( 'hmwp_disable_name' ) ) ) { //validate url parameter
					$ret = preg_replace( '/[^A-Za-z0-9-_]/', '', $ret );
				} elseif ( in_array( $key, array( 'hmwp_admin_url' ) ) ) { //validate url parameter
					$ret = preg_replace( '/[^A-Za-z0-9-_.]/', '', $ret );
				} else {
					$ret = preg_replace( '/[^A-Za-z0-9-_.\/]/', '', $ret ); //validate fields
				}
				//Sanitize the text field
				$ret = sanitize_text_field( $ret );

			} else {

				//Validate the text areas
				$ret = preg_replace( '/[^A-Za-z0-9-_.+*#:~\!\'\n\r\s\/]@/', '', $ret );

				//Sanitize the textarea
				if ( function_exists( 'sanitize_textarea_field' ) ) {
					$ret = sanitize_textarea_field( $ret );
				}
			}
		}

		//Return the unsplas validated and sanitized value
		return wp_unslash( $ret );
	}

	/**
	 * Check if the parameter is set
	 *
	 * @param  string  $key
	 *
	 * @return boolean
	 */
	public static function getIsset( $key = null ) {
		if ( ! isset( $key ) || $key == '' ) {
			return false;
		}

		return isset( $_POST[ $key ] ) || isset( $_GET[ $key ] );
	}

	/**
	 * Show the notices to WP
	 *
	 * @param  string  $message
	 * @param  string  $type
	 *
	 * @return string
	 */
	public static function showNotices( $message, $type = '' ) {

		//Initialize WordPress Filesystem
		$wp_filesystem = HMWP_Classes_ObjController::initFilesystem();

		if ( $wp_filesystem->exists( _HMWP_THEME_DIR_ . 'Notices.php' ) ) {
			ob_start();
			include _HMWP_THEME_DIR_ . 'Notices.php';
			$message = ob_get_contents();
			ob_end_clean();
		}

		return $message;
	}

	/**
	 * Connect remote with wp_remote_get
	 *
	 * @param $url
	 * @param  array  $params
	 * @param  array  $options
	 *
	 * @return bool|string
	 */
	public static function hmwp_remote_get( $url, $params = array(), $options = array() ) {

		$parameters = '';
		if ( ! empty( $params ) ) {
			foreach ( $params as $key => $value ) {
				if ( $key <> '' ) {
					$parameters .= ( $parameters == "" ? "" : "&" ) . $key . "=" . $value;
				}
			}

			if ( $parameters <> '' ) {
				$url .= ( ( strpos( $url, "?" ) === false ) ? "?" : "&" ) . $parameters;
			}
		}

		$response = self::hmwp_wpcall( $url, $params, $options );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		return self::cleanResponce( wp_remote_retrieve_body( $response ) ); //clear and get the body

	}


	/**
	 * Connect remote with wp_remote_get
	 *
	 * @param $url
	 * @param  array  $params
	 * @param  array  $options
	 *
	 * @return bool|string
	 */
	public static function hmwp_remote_post( $url, $params = array(), $options = array() ) {
		$options['method'] = 'POST';

		$response = self::hmwp_wpcall( $url, $params, $options );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		return self::cleanResponce( wp_remote_retrieve_body( $response ) ); //clear and get the body

	}

	/**
	 * Use the WP remote call
	 *
	 * @param  string  $url
	 * @param  array  $params
	 * @param  array  $options
	 *
	 * @return array|WP_Error The response or WP_Error on failure.
	 */
	public static function hmwp_wpcall( $url, $params, $options ) {
		//predefined options
		$options = array_replace_recursive( array(
			'sslverify' => _HMWP_CHECK_SSL_,
			'method'    => 'GET',
			'timeout'   => 30,
			'headers'   => array(
				'TOKEN'     => HMWP_Classes_Tools::getOption( 'hmwp_token' ),
				'API-TOKEN' => HMWP_Classes_Tools::getOption( 'api_token' ),
				'USER-URL'  => home_url(),
				'LANG'      => get_bloginfo( 'language' ),
				'VER'       => HMWP_VERSION
			)
		), $options );

		if ( $options['method'] == 'POST' ) {

			$options['body'] = $params;
			unset( $options['method'] );
			$response = wp_remote_post( $url, $options );
		} else {

			unset( $options['method'] );
			$response = wp_remote_get( $url, $options );

		}

		if ( is_wp_error( $response ) ) {
			//For debugging
			do_action( 'hmwp_debug_request', $url, $options, $response );
		}

		return $response;
	}

	/**
	 * Call the local URLs for Security Check
	 *
	 * @param $url
	 * @param $options
	 *
	 * @return array|WP_Error
	 */
	public static function hmwp_localcall( $url, $options = array() ) {
		//predefined options
		$options = array_merge( array(
			'sslverify' => false,
			'timeout'   => 10,
		), $options );

		$response = wp_remote_get( $url, $options );

		if ( is_wp_error( $response ) ) {
			//For debugging
			do_action( 'hmwp_debug_local_request', $url, $options, $response );
		}

		return $response;
	}

	/**
	 * Get the Json from responce if any
	 *
	 * @param  string  $response
	 *
	 * @return string
	 */
	private static function cleanResponce( $response ) {
		return trim( $response, '()' );
	}

	/**
	 * Check if HTML Headers to prevent chenging the code for other file extension
	 *
	 * @param  array  $types
	 *
	 * @return bool
	 * @throws Exception
	 */
	public static function isContentHeader( $types = array( 'text/html', 'text/xml' ) ) {
		$headers = headers_list();

		//check the Content Type
		if ( ! empty( $headers ) && ! empty( $types ) ) {
			foreach ( $headers as $value ) {
				if ( strpos( $value, ':' ) !== false ) {
					if ( stripos( $value, 'Content-Type' ) !== false ) {

						foreach ( $types as $type ) {
							if ( stripos( $value, $type ) !== false ) {
								return true;
							}
						}

						return false;

					}
				}
			}
		}

		return false;
	}


	/**
	 * Returns true if server is Apache
	 *
	 * @return boolean
	 */
	public static function isApache() {
		global $is_apache;

		//If custom defined
		if ( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ) <> 'auto' ) {
			return in_array( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ), array(
				'apache',
				'litespeed',
				'siteground'
			) );
		}

		//If custom defined
		if ( defined( 'HMWP_SERVER_TYPE' ) && strtolower( HMWP_SERVER_TYPE ) == 'apache' ) {
			return true;
		}

		if ( self::isFlywheel() ) { //force Nginx on Flywheel server
			return false;
		}

		return $is_apache;
	}

	/**
	 * Check if mode rewrite is on
	 *
	 * @return bool
	 */
	public static function isModeRewrite() {
		if ( function_exists( 'apache_get_modules' ) ) {
			$modules = apache_get_modules();
			if ( ! empty( $modules ) ) {
				return in_array( 'mod_rewrite', $modules );
			}
		}

		return true;
	}

	/**
	 * Check whether server is LiteSpeed
	 *
	 * @return bool
	 */
	public static function isLitespeed() {
		$litespeed = false;

		//If custom defined
		if ( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ) <> 'auto' ) {
			return ( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ) == 'litespeed' );
		}

		//If custom defined
		if ( defined( 'HMWP_SERVER_TYPE' ) && strtolower( HMWP_SERVER_TYPE ) == 'litespeed' ) {
			return true;
		}

		if ( isset( $_SERVER['SERVER_SOFTWARE'] ) && stripos( $_SERVER['SERVER_SOFTWARE'], 'LiteSpeed' ) !== false ) {
			$litespeed = true;
		} elseif ( isset( $_SERVER['SERVER_NAME'] ) && stripos( $_SERVER['SERVER_NAME'], 'LiteSpeed' ) !== false ) {
			$litespeed = true;
		} elseif ( isset( $_SERVER['X-Litespeed-Cache-Control'] ) ) {
			$litespeed = true;
		}

		if ( self::isFlywheel() ) {
			return false;
		}

		return $litespeed;
	}

	/**
	 * Check whether server is Lighthttp
	 *
	 * @return bool
	 */
	public static function isLighthttp() {
		return ( isset( $_SERVER['SERVER_SOFTWARE'] ) && stripos( $_SERVER['SERVER_SOFTWARE'], 'lighttpd' ) !== false );
	}

	/**
	 * Check whether server is AWS StoreFront Bitnami
	 *
	 * @return bool
	 */
	public static function isAWS() {
		//If custom defined
		if ( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ) <> 'auto' ) {
			return ( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ) == 'bitnami' );
		}

		if ( isset( $_SERVER["DOCUMENT_ROOT"] ) && strpos( $_SERVER["DOCUMENT_ROOT"], "/bitnami/" ) ) {
			return true;
		}

		$headers = headers_list();

		foreach ( $headers as $header ) {
			if ( strpos( $header, 'x-amz-cf-id' ) !== false ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if multisites
	 *
	 * @return bool
	 */
	public static function isMultisites() {
		return is_multisite();
	}

	/**
	 * Check if multisites with path
	 *
	 * @return bool
	 */
	public static function isMultisiteWithPath() {
		return ( is_multisite() && ( ( defined( 'SUBDOMAIN_INSTALL' ) && ! SUBDOMAIN_INSTALL ) || ( defined( 'VHOST' ) && VHOST == 'no' ) ) );
	}

	/**
	 * Returns true if server is nginx
	 *
	 * @return boolean
	 */
	public static function isNginx() {
		global $is_nginx;

		//If custom defined
		if ( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ) <> 'auto' ) {
			if ( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ) == 'nginx' ) {
				return true;
			}
		}

		//If custom defined
		if ( defined( 'HMWP_SERVER_TYPE' ) && strtolower( HMWP_SERVER_TYPE ) == 'nginx' ) {
			return true;
		}

		if ( HMWP_Classes_Tools::isWpengine() ){
			return false;
		}

		return ( $is_nginx || ( isset( $_SERVER['SERVER_SOFTWARE'] ) && ( stripos( $_SERVER['SERVER_SOFTWARE'], 'nginx' ) !== false || stripos( $_SERVER['SERVER_SOFTWARE'], 'TasteWP' ) !== false ) ) );
	}

	/**
	 * Returns true if server is Wpengine
	 *
	 * @return boolean
	 */
	public static function isWpengine() {

		// Check if a custom-defined server type matches WPEngine
		if ( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ) <> 'auto' ) {
			return ( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ) == 'wpengine' );
		}

		// Return true if the custom server type constant matches WPEngine
		if ( defined( 'HMWP_SERVER_TYPE' ) && strtolower( HMWP_SERVER_TYPE ) == 'wpengine' ) {
			return true;
		}

		return ( isset( $_SERVER['IS_WPE'] ) || isset( $_SERVER['HTTP_X_WPE_SSL'] ) || isset( $_SERVER['HTTP_X_WPENGINE_PHP_VERSION'] ) );
	}

	/**
	 * Returns true if server is Local by Flywheel
	 *
	 * @return boolean
	 */
	public static function isLocalFlywheel() {

		//If custom defined
		if ( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ) <> 'auto' ) {
			return ( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ) == 'local' );
		}

		return false;
	}

	/**
	 * Returns true if server is Wpengine
	 *
	 * @return boolean
	 */
	public static function isFlywheel() {

		//If custom defined
		if ( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ) <> 'auto' ) {
			return ( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ) == 'flywheel' );
		}

		//If custom defined
		if ( defined( 'HMWP_SERVER_TYPE' ) && strtolower( HMWP_SERVER_TYPE ) == 'flywheel' ) {
			return true;
		}

		if ( isset( $_SERVER['SERVER'] ) && stripos( $_SERVER['SERVER'], 'Flywheel' ) !== false ) {
			return true;
		}

		return ( isset( $_SERVER['SERVER_SOFTWARE'] ) && stripos( $_SERVER['SERVER_SOFTWARE'], 'Flywheel' ) !== false );
	}

	/**
	 * Returns true if server is Inmotion
	 *
	 * @return boolean
	 */
	public static function isInmotion() {

		//If custom defined
		if ( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ) <> 'auto' ) {
			return ( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ) == 'inmotion' );
		}

		//If custom defined
		if ( defined( 'HMWP_SERVER_TYPE' ) && strtolower( HMWP_SERVER_TYPE ) == 'inmotion' ) {
			return true;
		}

		return ( isset( $_SERVER['SERVER_ADDR'] ) && stripos( @gethostbyaddr( $_SERVER['SERVER_ADDR'] ), 'inmotionhosting.com' ) !== false );
	}

	/**
	 * Returns true if server is Godaddy
	 *
	 * @return boolean
	 */
	public static function isGodaddy() {

		//If custom defined
		if ( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ) <> 'auto' ) {
			return ( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ) == 'godaddy' );
		}

		//If custom defined
		if ( defined( 'HMWP_SERVER_TYPE' ) && strtolower( HMWP_SERVER_TYPE ) == 'godaddy' ) {
			return true;
		}

		return ( file_exists( ABSPATH . 'gd-config.php' ) );
	}

	/**
	 * Returns true if server is IIS
	 *
	 * @return boolean
	 */
	public static function isIIS() {
		global $is_IIS, $is_iis7;

		//If custom defined
		if ( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ) <> 'auto' ) {
			return ( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ) == 'iis' );
		}

		//If custom defined
		if ( defined( 'HMWP_SERVER_TYPE' ) && strtolower( HMWP_SERVER_TYPE ) == 'iis' ) {
			return true;
		}

		return ( $is_iis7 || $is_IIS || ( isset( $_SERVER['SERVER_SOFTWARE'] ) && stripos( $_SERVER['SERVER_SOFTWARE'], 'microsoft-iis' ) !== false ) );
	}

	/**
	 * Returns true if windows
	 *
	 * @return bool
	 */
	public static function isWindows() {
		return ( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' );
	}

	/**
	 * Check if IIS has rewritten 2 structure enabled
	 *
	 * @return bool
	 */
	public static function isPHPPermalink() {
		if ( get_option( 'permalink_structure' ) ) {
			if ( strpos( get_option( 'permalink_structure' ), 'index.php' ) !== false || stripos( get_option( 'permalink_structure' ), 'index.html' ) !== false || strpos( get_option( 'permalink_structure' ), 'index.htm' ) !== false ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns true if server is Godaddy
	 *
	 * @return boolean
	 */
	public static function isCloudPanel() {

		global $is_nginx;

		//If custom defined
		if ( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ) <> 'auto' ) {
			if ( HMWP_Classes_Tools::getOption( 'hmwp_server_type' ) == 'cloudpanel' ) {
				$is_nginx = true;

				return true;
			}
		}

		return false;
	}

	/**
	 * Is a cache plugin installed in WordPress?
	 *
	 * @return bool
	 */
	public static function isCachePlugin() {
		return ( HMWP_Classes_Tools::isPluginActive( 'autoptimize/autoptimize.php' ) || HMWP_Classes_Tools::isPluginActive( 'beaver-builder-lite-version/fl-builder.php' ) || HMWP_Classes_Tools::isPluginActive( 'beaver-builder/fl-builder.php' ) || HMWP_Classes_Tools::isPluginActive( 'breeze/breeze.php' ) || HMWP_Classes_Tools::isPluginActive( 'cache-enabler/cache-enabler.php' ) || HMWP_Classes_Tools::isPluginActive( 'comet-cache/comet-cache.php' ) || HMWP_Classes_Tools::isPluginActive( 'hummingbird-performance/wp-hummingbird.php' ) || HMWP_Classes_Tools::isPluginActive( 'hyper-cache/plugin.php' ) || HMWP_Classes_Tools::isPluginActive( 'jch-optimize/jch-optimize.php' ) || HMWP_Classes_Tools::isPluginActive( 'litespeed-cache/litespeed-cache.php' ) || HMWP_Classes_Tools::isPluginActive( 'powered-cache/powered-cache.php' ) || HMWP_Classes_Tools::isPluginActive( 'sg-cachepress/sg-cachepress.php' ) || HMWP_Classes_Tools::isPluginActive( 'w3-total-cache/w3-total-cache.php' ) || HMWP_Classes_Tools::isPluginActive( 'wp-asset-clean-up/wpacu.php' ) || HMWP_Classes_Tools::isPluginActive( 'wp-fastest-cache/wpFastestCache.php' ) || HMWP_Classes_Tools::isPluginActive( 'wp-rocket/wp-rocket.php' ) || HMWP_Classes_Tools::isPluginActive( 'wp-super-cache/wp-cache.php' ) || HMWP_Classes_Tools::isPluginActive( 'swift-performance/performance.php' ) || HMWP_Classes_Tools::isPluginActive( 'swift-performance-lite/performance.php' ) || HMWP_Classes_Tools::isPluginActive( 'nitropack/main.php' ) || WP_CACHE );
	}

	/**
	 * Check whether the plugin is active by checking the active_plugins list.
	 *
	 * @source wp-admin/includes/plugin.php
	 *
	 * @param  string  $plugin  Plugin folder/main file.
	 *
	 * @return boolean
	 */
	public static function isPluginActive( $plugin ) {

		if ( empty( self::$active_plugins ) ) {

			if ( self::isMultisites() ) {

				if ( ! $sitewide_plugins = get_site_option( 'active_sitewide_plugins' ) ) {
					$sitewide_plugins = array();
				}

				self::$active_plugins = array_keys( $sitewide_plugins );

				$sites = get_sites( array( 'number' => 10000, 'public' => 1, 'deleted' => 0, ) );
				foreach ( $sites as $site ) {
					switch_to_blog( $site->blog_id );

					$active_plugins = (array) get_option( 'active_plugins', array() );

					self::$active_plugins = array_merge( self::$active_plugins, $active_plugins );

					restore_current_blog();
				}

				if ( ! empty( self::$active_plugins ) ) {
					self::$active_plugins = array_unique( self::$active_plugins );
				}

			} else {
				self::$active_plugins = (array) get_option( 'active_plugins', array() );
			}

		}

		return in_array( $plugin, self::$active_plugins, true );
	}

	/**
	 * Check whether the theme is active.
	 *
	 * @param  string  $name  Theme folder/main file.
	 *
	 * @return boolean
	 */
	public static function isThemeActive( $name ) {
		$theme = get_option( 'template' );

		if ( $theme ) {
			if ( strtolower( $theme ) == strtolower( $name ) || strtolower( $theme ) == strtolower( $name ) . ' child' || strtolower( $theme ) == strtolower( $name ) . ' child theme' ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get all the plugin names
	 *
	 * @return array
	 */
	public static function getAllPlugins() {

		// Check if the HMWP option to hide all plugins is enabled
		if ( HMWP_Classes_Tools::getOption( 'hmwp_hide_all_plugins' ) ) {
			// Ensure the get_plugins() function is included before use
			if ( ! function_exists( 'get_plugins' ) ) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			// Retrieve all plugin file paths from WordPress
			$plugins = array_keys( get_plugins() );
		} else {
			// Retrieve only the active plugins from WordPress options
			$plugins = (array) get_option( 'active_plugins', array() );
		}

		// Check if WordPress is running as a multisite
		if ( self::isMultisites() ) {
			// Merge active plugins with any sitewide active plugins
			$plugins = array_merge( array_values( $plugins ), array_keys( get_site_option( 'active_sitewide_plugins' ) ) );
		}

		// Remove duplicate entries from the plugins array
		if ( ! empty( $plugins ) ) {
			$plugins = array_unique( $plugins );
		}

		return $plugins;
	}

	/**
	 * Get all the themes names
	 *
	 * @return array
	 */
	public static function getAllThemes() {

		if ( HMWP_Classes_Tools::getOption( 'hmwp_hide_all_themes' ) ) {
			// Get the all network themes
			$themes = search_theme_directories();
		} else {

			// Get only the active theme
			$theme = wp_get_theme();

			if ( $theme->exists() && $theme->get_stylesheet() <> '' ) {
				$themes[ $theme->get_stylesheet() ] = array(
					'theme_root' => $theme->get_theme_root()
				);

				// If it's a child theme, search and include also the parent
				if( stripos( $theme->get_stylesheet(), '-child' ) !== false ) {
					$parent_theme = str_ireplace( '-child', '', $theme->get_stylesheet() );
					$all_themes = search_theme_directories();

					if (!empty($all_themes)){
						foreach ( $all_themes as $theme => $value ) {
							if( stripos( $theme, $parent_theme ) !== false ) {
								$themes[ $theme ] = $value;
							}
						}
					}

				}
			}
		}

		return $themes;
	}

	/**
	 * Get Relative path for the current blog in case of WP Multisite
	 *
	 * @param $url
	 *
	 * @return string
	 */
	public static function getRelativePath( $url ) {

		if ( $url <> '' ) {
			// Get the relative url path
			$url = wp_make_link_relative( $url );

			// Get the relative domain
			$domain = site_url();

			// f WP Multisite, get the root domain
			if ( self::isMultisiteWithPath() ) {
				$domain = network_site_url();
			}

			// Get relative path and exclude any root domain from URL
			if($domain = wp_make_link_relative( trim($domain , '/') )){
				$url = str_replace( $domain, '', $url );
			}

			//remove the domain path if exists
			if ( self::isMultisiteWithPath() && defined( 'PATH_CURRENT_SITE' ) && PATH_CURRENT_SITE <> '/' ) {
				$url = str_replace( rtrim( PATH_CURRENT_SITE, '/' ), '', $url );
			}
		}

		return trailingslashit( $url );
	}

	/**
	 * Check if wp-content is changed and set in a different location
	 *
	 * @ver 7.0.12
	 *
	 * @return bool
	 */
	public static function isDifferentWPContentPath() {
		$homepath = '';
		if ( wp_parse_url( site_url(), PHP_URL_PATH ) ) {
			$homepath = ltrim( wp_parse_url( site_url(), PHP_URL_PATH ), '/' );
		}

		if ( $homepath <> '/' ) {
			$contenturl = ltrim( wp_parse_url( content_url(), PHP_URL_PATH ), '/' );

			return ( strpos( $contenturl, $homepath . '/' ) === false );
		}

		return false;
	}

	/**
	 * Empty the cache from other cache plugins when save the settings
	 */
	public static function emptyCache() {

		try {
			//Empty WordPress rewrites count for 404 error.
			//This happens when the rules are not saved through config file
			HMWP_Classes_Tools::saveOptions( 'file_mappings', array() );

			//For debugging
			do_action( 'hmwp_debug_cache', '' );

			if ( class_exists( '\FlyingPress\Purge' ) && method_exists( '\FlyingPress\Purge', 'purge_everything' ) ) {
				\FlyingPress\Purge::purge_everything();
			}

			if ( class_exists( '\JchOptimize\Platform\Cache' ) && method_exists( '\JchOptimize\Platform\Cache', 'deleteCache' ) ) {
				\JchOptimize\Platform\Cache::deleteCache();
			}

			//////////////////////////////////////////////////////////////////////////////
			if ( function_exists( 'w3tc_pgcache_flush' ) ) {
				w3tc_pgcache_flush();
			}

			if ( function_exists( 'w3tc_minify_flush' ) ) {
				w3tc_minify_flush();
			}
			if ( function_exists( 'w3tc_dbcache_flush' ) ) {
				w3tc_dbcache_flush();
			}
			if ( function_exists( 'w3tc_objectcache_flush' ) ) {
				w3tc_objectcache_flush();
			}
			//////////////////////////////////////////////////////////////////////////////

			if ( function_exists( 'wp_cache_clear_cache' ) ) {
				wp_cache_clear_cache();
			}

			if ( function_exists( 'rocket_clean_domain' ) && function_exists( 'rocket_clean_minify' ) && function_exists( 'rocket_clean_cache_busting' ) ) {
				// Remove all cache files
				rocket_clean_domain();
				rocket_clean_minify();
				rocket_clean_cache_busting();
			}
			//////////////////////////////////////////////////////////////////////////////

			if ( function_exists( 'apc_clear_cache' ) ) {
				// Remove all apc if enabled
				apc_clear_cache();
			}
			//////////////////////////////////////////////////////////////////////////////

			if ( class_exists( 'Cache_Enabler_Disk' ) && method_exists( 'Cache_Enabler_Disk', 'clear_cache' ) ) {
				// clear disk cache
				Cache_Enabler_Disk::clear_cache();
			}
			//////////////////////////////////////////////////////////////////////////////
			if ( self::isPluginActive( 'litespeed-cache/litespeed-cache.php' ) ) {
				header( "X-LiteSpeed-Purge: *" );
			}
			//////////////////////////////////////////////////////////////////////////////

			if ( self::isPluginActive( 'hummingbird-performance/wp-hummingbird.php' ) ) {
				do_action( 'wphb_clear_page_cache' );
			}
			//////////////////////////////////////////////////////////////////////////////

			if ( class_exists( 'WpeCommon' ) ) {
				if ( method_exists( 'WpeCommon', 'purge_memcached' ) ) {
					WpeCommon::purge_memcached();
				}
				if ( method_exists( 'WpeCommon', 'clear_maxcdn_cache' ) ) {
					WpeCommon::clear_maxcdn_cache();
				}
				if ( method_exists( 'WpeCommon', 'purge_varnish_cache' ) ) {
					WpeCommon::purge_varnish_cache();
				}
			}
			//////////////////////////////////////////////////////////////////////////////

			if ( self::isPluginActive( 'sg-cachepress/sg-cachepress.php' ) && class_exists( 'Supercacher' ) ) {
				if ( method_exists( 'Supercacher', 'purge_cache' ) && method_exists( 'Supercacher', 'delete_assets' ) ) {
					Supercacher::purge_cache();
					Supercacher::delete_assets();
				}
			}

			//Clear the fastest cache
			global $wp_fastest_cache;
			if ( isset( $wp_fastest_cache ) && method_exists( $wp_fastest_cache, 'deleteCache' ) ) {
				$wp_fastest_cache->deleteCache();
			}
			//////////////////////////////////////////////////////////////////////////////
		} catch ( Exception $e ) {
			// handle exception
		}

	}

	/**
	 * Flush the WordPress rewrites
	 */
	public static function flushWPRewrites() {
		if ( HMWP_Classes_Tools::isPluginActive( 'woocommerce/woocommerce.php' ) ) {
			update_option( 'woocommerce_queue_flush_rewrite_rules', 'yes' );
		}

	}

	/**
	 * Called on plugin activation
	 *
	 * @throws Exception
	 */
	public function hmwp_activate() {
		set_transient( 'hmwp_activate', true );

		//set restore settings option on plugin activate
		$lastsafeoptions = self::getOptions( true );
		if ( isset( $lastsafeoptions['hmwp_mode'] ) && ( $lastsafeoptions['hmwp_mode'] == 'ninja' || $lastsafeoptions['hmwp_mode'] == 'lite' ) ) {
			set_transient( 'hmwp_restore', true );
		}

		//Initialize the compatibility with other plugins
		HMWP_Classes_ObjController::getClass( 'HMWP_Models_Compatibility' )->install();
	}

	/**
	 * Called on plugin deactivation
	 * Remove all the rewrite rules on deactivation
	 *
	 * @throws Exception
	 */
	public function hmwp_deactivate() {
		$options = self::$default;
		//Prevent duplicates
		foreach ( $options as $key => $value ) {
			//set the default params from tools
			self::saveOptions( $key, $value );
		}

		//remove the custom rules
		HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rules' )->writeToFile( '', 'HMWP_VULNERABILITY' );
		HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rules' )->writeToFile( '', 'HMWP_RULES' );

		//clear the locked ips
		HMWP_Classes_ObjController::getClass( 'HMWP_Models_Bruteforce_Database' )->clearBlockedIPs();

		//Build the redirect table
		HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rewrite' )->flushChanges();

		//Delete the compatibility with other plugins
		HMWP_Classes_ObjController::getClass( 'HMWP_Models_Compatibility' )->uninstall();
	}

	/**
	 * Call this function on rewrite update from other plugins
	 *
	 * @param  array  $wp_rules
	 *
	 * @return array
	 * @throws Exception
	 */
	public function checkRewriteUpdate( $wp_rules = array() ) {
		try {
			if ( ! HMWP_Classes_Tools::getOption( 'error' ) && ! HMWP_Classes_Tools::getOption( 'logout' ) ) {

				//Build the redirect table
				HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rewrite' )->clearRedirect()->setRewriteRules()->flushRewrites();

				//INSERT SEURITY RULES
				if ( ! HMWP_Classes_Tools::isIIS() ) {
					//For Nginx and Apache the rules can be inserted separately
					$rules = HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rules' )->getInjectionRewrite();

					HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rules' )->writeToFile( $rules, 'HMWP_VULNERABILITY' );

				}
			}

		} catch ( Exception $e ) {

		}

		return $wp_rules;
	}

	/**
	 * Check if new themes or plugins are added in WordPress
	 */
	public function checkPluginsThemesUpdates() {

		try {
			//Check if tere are plugins added to website
			if ( HMWP_Classes_Tools::getOption( 'hmwp_hide_plugins' ) ) {
				$plugins = HMWP_Classes_Tools::getAllPlugins();
				$dbplugins   = HMWP_Classes_Tools::getOption( 'hmwp_plugins' );
				foreach ( $plugins as $plugin ) {
					if ( ! empty( $dbplugins['from'] ) ) {
						if ( ! in_array( plugin_dir_path( $plugin ), $dbplugins['from'] ) ) {
							HMWP_Classes_Tools::saveOptions( 'changes', true );
						}
					}
				}
			}

			//Check if there are themes added to website
			if ( HMWP_Classes_Tools::getOption( 'hmwp_hide_themes' ) ) {
				$themes = HMWP_Classes_Tools::getAllThemes();
				$dbthemes   = HMWP_Classes_Tools::getOption( 'hmwp_themes' );
				foreach ( $themes as $theme => $value ) {
					if ( ! empty( $dbthemes['from'] ) ) {
						if ( ! in_array( $theme . '/', $dbthemes['from'] ) ) {
							HMWP_Classes_Tools::saveOptions( 'changes', true );
						}
					}
				}
			}

			//If there are changed (new plugins, new themes)
			if ( self::getOption( 'changes' ) ) {
				//Initialize the compatibility with other plugins
				HMWP_Classes_ObjController::getClass( 'HMWP_Models_Compatibility' )->install();
			}
		} catch ( Exception $e ) {

		}
	}

	/**
	 * Send the login URL to Cloud for this URL
	 *
	 * @return void
	 */
	public static function sendLoginPathsApi() {

		if ( HMWP_Classes_Tools::getOption( 'api_token' ) ) {

			$domain  = ( self::isMultisites() && defined( 'BLOG_ID_CURRENT_SITE' ) ) ? get_home_url( BLOG_ID_CURRENT_SITE ) : home_url();
			$options = array( 'timeout' => 10, 'headers' => array( 'USER-URL' => $domain ) );

			$login = array(
				'path'      => HMWP_Classes_Tools::getOption( 'hmwp_login_url' ),
				'parameter' => HMWP_Classes_Tools::getOption( 'hmwp_disable_name' ),
				'value'     => HMWP_Classes_Tools::getOption( 'hmwp_disable' ),
			);

			self::hmwp_remote_post( _HMWP_API_SITE_ . '/api/settings', array(
				'login' => wp_json_encode( $login ),
				'url'   => $domain
			), $options );
		}
	}

	/**
	 * Call Account API Server
	 *
	 * @param  string  $email
	 * @param  string  $redirect_to
	 *
	 * @return array|mixed|void
	 */
	public static function checkAccountApi( $email = null, $redirect_to = '' ) {

		$check   = array();
		$howtolessons = HMWP_Classes_Tools::getValue( 'howtolessons', 1 );
		$domain  = ( self::isMultisites() && defined( 'BLOG_ID_CURRENT_SITE' ) ) ? get_home_url( BLOG_ID_CURRENT_SITE ) : home_url();

		if ( isset( $email ) && $email <> '' ) {
			$args     = array(
				'email'        => $email,
				'url'          => $domain,
				'howtolessons' => (int) $howtolessons,
				'monitor'      => 0,
				'source'       => 'hide-my-wp'
			);
			$response = HMWP_Classes_Tools::hmwp_remote_get( _HMWP_API_SITE_ . '/api/free/token', $args, array( 'timeout' => 10 ) );
		} elseif ( HMWP_Classes_Tools::getOption( 'hmwp_token' ) ) {
			$args     = array(
				'token'        => self::getOption( 'hmwp_token' ),
				'url'          => $domain,
				'howtolessons' => (int) $howtolessons,
				'source'       => 'hide-my-wp'
			);
			$response = HMWP_Classes_Tools::hmwp_remote_get( _HMWP_API_SITE_ . '/api/free/token', $args, array( 'timeout' => 10 ) );
		} else {
			return $check;
		}

		if ( $response && $response = json_decode( $response, true ) ) {

			HMWP_Classes_Tools::saveOptions( 'hmwp_token', ( isset( $response['token'] ) ? $response['token'] : 0 ) );
			HMWP_Classes_Tools::saveOptions( 'api_token', ( isset( $response['api_token'] ) ? $response['api_token'] : false ) );
			HMWP_Classes_Tools::saveOptions( 'error', isset( $response['error'] ) );

			if ( ! isset( $response['error'] ) ) {
				if ( $redirect_to <> '' ) {
					wp_redirect( $redirect_to );
					exit();
				}
			} elseif ( isset( $response['message'] ) ) {
				HMWP_Classes_Error::setNotification( $response['message'], 'notice', false );
			}
		} else {
			HMWP_Classes_Error::setNotification( sprintf( __( 'CONNECTION ERROR! Make sure your website can access: %s', 'hide-my-wp' ), '<a href="' . _HMWP_ACCOUNT_SITE_ . '" target="_blank">' . _HMWP_ACCOUNT_SITE_ . '</a>' ), 'notice', false );
		}

		return $response;

	}

	/**
	 * Verify the API response on update
	 *
	 * @param  $result
	 */
	public function checkLicenseOnUpdate( $result ) {

		// check the token
		if ( ! self::getOption( 'hmwp_token' ) ) {
			return;
		}

		if ( $body = json_decode( wp_remote_retrieve_body( $result ) ) ) {

			//if data received is valid
			HMWP_Classes_Tools::saveOptions( 'hmwp_valid', 1 );

			if ( isset( $body->expires ) && (int) $body->expires > 0 && (int) $body->expires < time() ) {
				HMWP_Classes_Tools::saveOptions( 'hmwp_valid', 0 );
				HMWP_Classes_Tools::saveOptions( 'hmwp_expires', $body->expires );
			} elseif ( isset( $body->download_url ) && ! $body->download_url ) {
				HMWP_Classes_Tools::saveOptions( 'hmwp_valid', 0 );
				HMWP_Classes_Tools::saveOptions( 'hmwp_expires', 0 );
			}

		} else {
			HMWP_Classes_Tools::saveOptions( 'hmwp_valid', 0 );
			HMWP_Classes_Tools::saveOptions( 'hmwp_expires', 0 );
		}

	}

	/**
	 * Send the email is case there are major changes
	 *
	 * @return bool
	 */
	public static function sendEmail() {
		$email = self::getOption( 'hmwp_email_address' );
		if ( $email == '' ) {
			global $current_user;
			$email = $current_user->user_email;
		}

		$line    = "\n" . "________________________________________" . "\n";
		$to      = $email;
		$subject = self::getOption( 'hmwp_plugin_name' ) . ' - ' . esc_html__( 'New Login Data', 'hide-my-wp' );
		$message = sprintf( esc_html__( "Thank you for using %s!", 'hide-my-wp' ), self::getOption( 'hmwp_plugin_name' ) ) . "\n";
		$message .= $line;
		$message .= esc_html__( "Your new site URLs are", 'hide-my-wp' ) . ':' . "\n";
		$message .= esc_html__( "Admin URL", 'hide-my-wp' ) . ': ' . admin_url() . "\n";
		$message .= esc_html__( "Login URL", 'hide-my-wp' ) . ': ' . site_url( self::$options['hmwp_login_url'] ) . "\n";
		$message .= $line;
		$message .= esc_html__( "Note: If you can`t login to your site, just access this URL", 'hide-my-wp' ) . ':' . "\n";
		$message .= site_url() . "/wp-login.php?" . self::getOption( 'hmwp_disable_name' ) . "=" . self::$options['hmwp_disable'] . "\n\n";
		$message .= $line;
		$message .= esc_html__( "Best regards", 'hide-my-wp' ) . ',' . "\n";
		$message .= self::getOption( 'hmwp_plugin_name' ) . "\n";

		$headers   = array();
		$headers[] = sprintf( esc_html__( "From: %s <%s>", 'hide-my-wp' ), self::getOption( 'hmwp_plugin_name' ), $email );
		$headers[] = 'Content-type: text/plain';

		add_filter( 'wp_mail_content_type', array( 'HMWP_Classes_Tools', 'setContentType' ) );

		if ( @wp_mail( $to, $subject, $message, $headers ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Set the content type to text/plain
	 *
	 * @return string
	 */
	public static function setContentType() {
		return "text/plain";
	}

	/**
	 * Set the current user role for later use
	 *
	 * @param  WP_User  $user
	 *
	 * @return string
	 */
	public static function setCurrentUserRole( $user = null ) {
		$roles = array();

		if ( isset( $user ) && isset( $user->roles ) && is_array( $user->roles ) ) {
			$roles = $user->roles;
		} elseif ( function_exists( 'wp_get_current_user' ) ) {
			$user = wp_get_current_user();

			if ( isset( $user->roles ) && is_array( $user->roles ) ) {
				$roles = $user->roles;
			}
		}

		if ( ! empty( $roles ) ) {
			self::$current_user_role = current( $roles );
		}

		return self::$current_user_role;
	}

	/**
	 * Get the user main Role or default
	 *
	 * @return string
	 */
	public static function getUserRole() {
		return self::$current_user_role;
	}

	/**
	 * Check the user capability for the roles attached
	 *
	 * @param  $cap
	 *
	 * @return bool
	 */
	public static function userCan( $cap ) {

		if ( function_exists( 'current_user_can' ) ) {

			if ( current_user_can( $cap ) ) {
				return true;
			}

			//Get the current user roles
			$user = wp_get_current_user();

			//If the user has multiple roles
			if ( isset( $user->roles ) && is_array( $user->roles ) && count( $user->roles ) > 1 ) {
				foreach ( $user->roles as $role ) {

					//Get the role
					$role_object = get_role( $role );

					//Check if it has capability
					if ( $role_object->has_cap( $cap ) ) {
						return true;
					}
				}
			}

		}

		return false;
	}

	/**
	 * Search for a substring within an array of strings.
	 *
	 * @param  string  $needle  The substring to search for.
	 * @param  array  $haystack  The array of strings to search within.
	 *
	 * @return bool  True if the substring is found in any of the strings in the array, false otherwise.
	 */
	public static function searchInString( $needle, $haystack ) {
		foreach ( $haystack as $value ) {
			if ( $needle && $value && $needle <> '' && $value <> '' ) {

				//add trail slash to make sure the path matches entirely
				$needle = trailingslashit( $needle );
				$value  = trailingslashit( $value );

				//use mb_stripos is possible
				if ( function_exists( 'mb_stripos' ) ) {
					if ( mb_stripos( $needle, $value ) !== false ) {
						return true;
					}
				} elseif ( stripos( $needle, $value ) !== false ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Customize the redirect for the logout process
	 *
	 * @param  $redirect
	 *
	 * @return mixed
	 */
	public static function getCustomLogoutURL( $redirect ) {
		//Get Logout based on user Role
		$role         = HMWP_Classes_Tools::getUserRole();
		$urlRedirects = HMWP_Classes_Tools::getOption( 'hmwp_url_redirects' );
		if ( isset( $urlRedirects[ $role ]['logout'] ) && $urlRedirects[ $role ]['logout'] <> '' ) {
			$redirect = $urlRedirects[ $role ]['logout'];
		} elseif ( isset( $urlRedirects['default']['logout'] ) && $urlRedirects['default']['logout'] <> '' ) {
			$redirect = $urlRedirects['default']['logout'];
		}

		return $redirect;
	}

	/**
	 * Customize the redirect for the login process
	 *
	 * @param  string  $redirect
	 *
	 * @return string
	 */
	public static function getCustomLoginURL( $redirect ) {

		//Get Logout based on user Role
		$role         = HMWP_Classes_Tools::getUserRole();
		$urlRedirects = HMWP_Classes_Tools::getOption( 'hmwp_url_redirects' );
		if ( isset( $urlRedirects[ $role ]['login'] ) && $urlRedirects[ $role ]['login'] <> '' ) {
			$redirect = $urlRedirects[ $role ]['login'];
		} elseif ( isset( $urlRedirects['default']['login'] ) && $urlRedirects['default']['login'] <> '' ) {
			$redirect = $urlRedirects['default']['login'];
		}

		return $redirect;
	}

	/**
	 * Generate a string
	 *
	 * @param  int  $length
	 *
	 * @return bool|string
	 */
	public static function generateRandomString( $length = 10 ) {
		return substr( str_shuffle( str_repeat( $x = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil( $length / strlen( $x ) ) ) ), 1, $length );
	}

	/**
	 * make this plugin the first plugin that loads
	 */
	public static function movePluginFirst() {
		//Make sure the plugin is loaded first
		$plugin         = dirname( HMWP_BASENAME ) . '/index.php';
		$active_plugins = get_option( 'active_plugins' );

		if ( ! empty( $active_plugins ) ) {

			$this_plugin_key = array_search( $plugin, $active_plugins );

			if ( $this_plugin_key > 0 ) {
				array_splice( $active_plugins, $this_plugin_key, 1 );
				array_unshift( $active_plugins, $plugin );
				update_option( 'active_plugins', $active_plugins );


			}

		}
	}

	/**
	 * Instantiates the WordPress filesystem
	 *
	 * @static
	 * @access public
	 * @return WP_Filesystem_Base|WP_Filesystem_Direct
	 */
	public static function initFilesystem() {
		return HMWP_Classes_ObjController::initFilesystem();
	}

	public static function isWhitelistedIP( $ip ) {
		$wl_items = array();

		if ( ! filter_var( $ip, FILTER_VALIDATE_IP ) ) {
			return true;
		}

		//jetpack whitelist
		$wl_jetpack = array(
			'122.248.245.244/32',
			'54.217.201.243/32',
			'54.232.116.4/32',
			'185.64.140.0/22',
			'76.74.255.0/22',
			'192.0.64.0/18',
			'192.0.65.0/22',
			'192.0.80.0/22',
			'192.0.96.0/22',
			'192.0.112.0/20',
			'192.0.123.0/22',
			'195.234.108.0/22',
			'54.148.171.133',//wordfence
			'35.83.41.128', //wordfence
			'52.25.185.95', //wordfence
		);

		$domain = ( self::isMultisites() && defined( 'BLOG_ID_CURRENT_SITE' ) ) ? get_home_url( BLOG_ID_CURRENT_SITE ) : site_url();

		if ( filter_var( $domain, FILTER_VALIDATE_URL ) !== false && strpos( $domain, '.' ) !== false ) {
			if ( ! self::isLocalFlywheel() ) {
				$wl_jetpack[] = '127.0.0.1';

				//set local domain IP
				if ( HMWP_Classes_Tools::getOption( 'hmwp_disable_rest_api' ) ) {
					if( $local_ip = get_transient('hmwp_local_ip') ){
						$wl_jetpack[] = $local_ip;
					}elseif( $local_ip = @gethostbyname( wp_parse_url($domain, PHP_URL_HOST) ) ) {
						set_transient( 'hmwp_local_ip', $local_ip );
						$wl_jetpack[] = $local_ip;
					}
				}
			}
		}

		if ( HMWP_Classes_Tools::getOption( 'whitelist_ip' ) ) {
			$wl_items = (array) json_decode( HMWP_Classes_Tools::getOption( 'whitelist_ip' ), true );
		}

		//merge all the whitelisted ips and also add the hook for users
		$wl_items = apply_filters( 'hmwp_whitelisted_ips', array_merge( $wl_jetpack, $wl_items ) );

		try {
			foreach ( $wl_items as $item ) {
				$item = trim( $item );

				if ( filter_var( $item, FILTER_VALIDATE_IP ) && $ip == $item ) {
					return true;
				}

				if ( strpos( $item, '*' ) === false && strpos( $item, '/' ) === false ) { //no match, no wildcard
					continue;
				}

				if ( strpos( $ip, '.' ) !== false ) {

					if ( strpos( $item, '/' ) !== false ) {
						list( $range, $bits ) = explode( '/', $item, 2 );

						if ( 0 == (int) $bits ) {
							continue;
						}

						if ( (int) $bits < 0 || (int) $bits > 32 ) {
							continue;
						}

						$subnet = ip2long( $range );
						$iplong = ip2long( $ip );
						$mask   = - 1 << ( 32 - $bits );
						$subnet &= $mask;

						if ( ( $iplong & $mask ) == $subnet ) {
							return true;
						}
					}

					$iplong  = ip2long( $ip );
					$ip_low  = ip2long( str_replace( '*', '0', $item ) );
					$ip_high = ip2long( str_replace( '*', '255', $item ) );

					if ( $iplong >= $ip_low && $iplong <= $ip_high ) {//IP is within wildcard range
						return true;
					}
				}

			}
		} catch ( Exception $e ) {
		}

		return false;
	}

	/**
	 * Check if there are banned IPs for accessing the hidden paths
	 *
	 * @return bool
	 */
	public static function isBlacklistedIP( $ip ) {
		$bl_items = array();

		$bl_blacklisted = array(
			'35.214.130.0/22', // detector
			'54.86.50.0/22', // detector
			'172.105.48.0/22', // detector
			'15.235.50.223', // detector
			'192.185.4.40', // detector
			'172.105.48.130', // detector
			'167.99.233.123', // detector
		);

		if ( HMWP_Classes_Tools::getOption( 'banlist_ip' ) ) {
			$bl_items = (array) json_decode( HMWP_Classes_Tools::getOption( 'banlist_ip' ), true );
		}

		//merge all the whitelisted ips and also add the hook for users
		$bl_items = apply_filters( 'hmwp_banlist_ips', array_merge( $bl_blacklisted, $bl_items ) );

		try {
			foreach ( $bl_items as $item ) {
				$item = trim( $item );

				if ( filter_var( $item, FILTER_VALIDATE_IP ) && $ip == $item ) {
					return true;
				}

				if ( strpos( $item, '*' ) === false && strpos( $item, '/' ) === false ) { //no match, no wildcard
					continue;
				}

				if ( strpos( $ip, '.' ) !== false ) {

					if ( strpos( $item, '/' ) !== false ) {
						list( $range, $bits ) = explode( '/', $item, 2 );

						if ( 0 == (int) $bits ) {
							continue;
						}

						if ( (int) $bits < 0 || (int) $bits > 32 ) {
							continue;
						}

						$subnet = ip2long( $range );
						$iplong = ip2long( $ip );
						$mask   = - 1 << ( 32 - $bits );
						$subnet &= $mask;

						if ( ( $iplong & $mask ) == $subnet ) {
							return true;
						}
					}

					$iplong  = ip2long( $ip );
					$ip_low  = ip2long( str_replace( '*', '0', $item ) );
					$ip_high = ip2long( str_replace( '*', '255', $item ) );

					if ( $iplong >= $ip_low && $iplong <= $ip_high ) {//IP is within wildcard range
						return true;
					}
				}

			}
		} catch ( Exception $e ) {
		}

		return false;
	}

	/**
	 * Check if the Advanced Pack is installed by verifying the definition of a constant.
	 *
	 * @return bool
	 */
	public static function isAdvancedpackInstalled() {
		return ( defined( 'HMWPP_VERSION' ) );
	}
}
