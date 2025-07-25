<?php
/**
 * Media Library Assistant Core objects
 *
 * @package Media Library Assistant
 * @since 2.20
 */
defined( 'ABSPATH' ) or die();

/**
 * Class MLA (Media Library Assistant) Core is the minimum support required for all other MLA features
 *
 * @package Media Library Assistant
 * @since 2.20
 */
class MLACore {
	/**
	 * Current version number (moved from class-mla-main.php)
	 *
	 * @since 2.30
	 *
	 * @var	string
	 */
	const CURRENT_MLA_VERSION = '3.27';

	/**
	 * Current date for Development Versions, empty for production versions
	 *
	 * @since 2.99
	 *
	 * @var	string
	 */
	const MLA_DEVELOPMENT_VERSION = '20250719';

	/**
	 * Slug for registering and enqueueing plugin style sheets (moved from class-mla-main.php)
	 *
	 * @since 2.30
	 *
	 * @var	string
	 */
	const STYLESHEET_SLUG = 'mla-style';

	/**
	 * Original PHP error_log path and file
	 *
	 * @since 2.20
	 *
	 * @var	string
	 */
	public static $original_php_log = '?';

	/**
	 * Original PHP error_reporting value
	 *
	 * @since 2.20
	 *
	 * @var	string
	 */
	public static $original_php_reporting = '?';

	/**
	 * Constant to log "any" debug activity
	 *
	 * @since 2.25
	 *
	 * @var	integer
	 */
	const MLA_DEBUG_CATEGORY_ANY = 0x00000001;

	/**
	 * Constant to log Ajax debug activity
	 *
	 * @since 2.13
	 *
	 * @var	integer
	 */
	const MLA_DEBUG_CATEGORY_AJAX = 0x00000002;

	/**
	 * Constant to log WPML/Polylang action/filter activity
	 *
	 * @since 2.15
	 *
	 * @var	integer
	 */
	const MLA_DEBUG_CATEGORY_LANGUAGE = 0x00000004;

	/**
	 * Constant to log Ghostscript/Imagick activity
	 *
	 * @since 2.23
	 *
	 * @var	integer
	 */
	const MLA_DEBUG_CATEGORY_THUMBNAIL = 0x00000008;

	/**
	 * Constant to log IPTC/EXIF/WP/XMP/PDF metadata activity
	 *
	 * @since 2.41
	 *
	 * @var	integer
	 */
	const MLA_DEBUG_CATEGORY_METADATA = 0x00000010;

	/**
	 * Constant to log WP REST activity
	 *
	 * @since 2.41
	 *
	 * @var	integer
	 */
	const MLA_DEBUG_CATEGORY_REST = 0x00000020;

	/**
	 * Constant to log where-used activity
	 *
	 * @since 2.41
	 *
	 * @var	integer
	 */
	const MLA_DEBUG_CATEGORY_WHERE_USED = 0x00000040;

	/**
	 * Constant to log Uploads and Views MIME Type activity activity
	 *
	 * @since 2.71
	 *
	 * @var	integer
	 */
	const MLA_DEBUG_CATEGORY_MIME_TYPE = 0x00000080;

	/**
	 * Constant to log Media Manager "query_attachments" activity
	 *
	 * @since 2.84
	 *
	 * @var	integer
	 */
	const MLA_DEBUG_CATEGORY_MMMW = 0x00000100;

	/**
	 * Constant to log Intermediate Image Size activity
	 *
	 * @since 3.25
	 *
	 * @var	integer
	 */
	const MLA_DEBUG_CATEGORY_IMAGE_SIZE = 0x00000200;

	/**
	 * Slug for adding plugin submenu
	 *
	 * @since 0.1
	 *
	 * @var	string
	 */
	const ADMIN_PAGE_SLUG = 'mla-menu';

	/**
	 * mla_admin_action value to display a single item for editing/viewing
	 *
	 * @since 0.1
	 *
	 * @var	string
	 */
	const MLA_ADMIN_SINGLE_EDIT_DISPLAY = 'single_item_edit_display';

	/**
	 * mla_admin_action value to install an example plugin
	 *
	 * @since 2.40
	 *
	 * @var	string
	 */
	const MLA_ADMIN_SINGLE_EDIT_INSTALL = 'single_item_edit_install';

	/**
	 * mla_admin_action value for updating a single item
	 *
	 * @since 0.1
	 *
	 * @var	string
	 */
	const MLA_ADMIN_SINGLE_EDIT_UPDATE = 'single_item_edit_update';

	/**
	 * mla_admin_action value for mapping Custom Field metadata
	 *
	 * @since 1.10
	 *
	 * @var	string
	 */
	const MLA_ADMIN_SINGLE_CUSTOM_FIELD_MAP = 'single_item_custom_field_map';

	/**
	 * mla_admin_action value for purging Custom Field values
	 *
	 * @since 2.50
	 *
	 * @var	string
	 */
	const MLA_ADMIN_SINGLE_CUSTOM_FIELD_PURGE = 'single_item_custom_field_purge';

	/**
	 * mla_admin_action value for mapping IPTC/EXIF/WP metadata
	 *
	 * @since 1.00
	 *
	 * @var	string
	 */
	const MLA_ADMIN_SINGLE_MAP = 'single_item_map';

	/**
	 * mla_admin_action value for purging IPTC/EXIF/WP metadata
	 *
	 * @since 2.60
	 *
	 * @var	string
	 */
	const MLA_ADMIN_SINGLE_PURGE = 'single_item_purge';

	/**
	 * mla_admin_action value for setting an item's parent object
	 *
	 * @since 1.82
	 *
	 * @var	string
	 */
	const MLA_ADMIN_SET_PARENT = 'set_parent';

	/**
	 * mla_admin_action value for searching taxonomy terms
	 *
	 * @since 1.90
	 *
	 * @var	string
	 */
	const MLA_ADMIN_TERMS_SEARCH = 'terms_search';

	/**
	 * mla_admin_action value for permanently deleting a single item
	 *
	 * @since 0.1
	 *
	 * @var	string
	 */
	const MLA_ADMIN_SINGLE_DELETE = 'single_item_delete';

	/**
	 * mla_admin_action value for moving a single item to the trash
	 *
	 * @since 0.1
	 *
	 * @var	string
	 */
	const MLA_ADMIN_SINGLE_TRASH = 'single_item_trash';

	/**
	 * mla_admin_action value for restoring a single item from the trash
	 *
	 * @since 0.1
	 *
	 * @var	string
	 */
	const MLA_ADMIN_SINGLE_RESTORE = 'single_item_restore';

	/**
	 * mla_admin_action value for copying a single item
	 *
	 * @since 2.40
	 *
	 * @var	string
	 */
	const MLA_ADMIN_SINGLE_COPY = 'single_item_copy';

	/**
	 * mla_admin_action value for copying a single item
	 *
	 * @since 2.40
	 *
	 * @var	string
	 */
	const MLA_ADMIN_SINGLE_ADD = 'single_item_add';

	/**
	 * Action name; gives a context for the 'download-zip'/'mla_download_file nonce
	 *
	 * @since 3.00
	 *
	 * @var	string
	 */
	const MLA_DOWNLOAD_NONCE_ACTION = 'mla_download_nonce_action';

	/**
	 * Action name; gives a context for the 'mla_download_example_plugin' nonce
	 *
	 * @since 3.00
	 *
	 * @var	string
	 */
	const MLA_DOWNLOAD_EXAMPLE_NONCE_ACTION = 'mla_download_example_nonce_action';

	/**
	 * Action name; gives a context for the 'mla_download_error_log' nonce
	 *
	 * @since 3.00
	 *
	 * @var	string
	 */
	const MLA_ERROR_LOG_NONCE_ACTION = 'mla_error_log_nonce_action';

	/**
	 * Action name; gives a context for the nonce
	 *
	 * @since 0.1
	 *
	 * @var	string
	 */
	const MLA_ADMIN_NONCE_ACTION = 'mla_admin_nonce_action';

	/**
	 * Nonce name; uniquely identifies the nonce
	 *
	 * @since 2.13
	 *
	 * @var	string
	 */
	const MLA_ADMIN_NONCE_NAME = 'mla_admin_nonce';

	/**
	 * Slug for localizing and enqueueing JavaScript - MLA List Table
	 *
	 * @since 0.20
	 *
	 * @var	string
	 */
	const JAVASCRIPT_INLINE_EDIT_SLUG = 'mla-inline-edit-scripts';

	/**
	 * Slug for "Find Posts" - fetch candidates for the "Set Parent" popup window
	 *
	 * @since 2.99
	 *
	 * @var	string
	 */
	const JAVASCRIPT_FIND_POSTS_SLUG = 'mla-find-posts';

	/**
	 * Slug for the Upload Bulk Edit presets "export" action
	 *
	 * @since 2.99
	 *
	 * @var	string
	 */
	const JAVASCRIPT_EXPORT_PRESETS_SLUG = 'mla-export-presets';

	/**
	 * Slug for the "query attachments" action - Add Media and related dialogs
	 *
	 * @since 1.80
	 *
	 * @var	string
	 */
	const JAVASCRIPT_QUERY_ATTACHMENTS_ACTION = 'mla-query-attachments';

	/**
	 * Slug for the "fill compat-attachment-fields" action - Add Media and related dialogs
	 *
	 * @since 1.80
	 *
	 * @var	string
	 */
	const JAVASCRIPT_FILL_COMPAT_ACTION = 'mla-fill-compat-fields';

	/**
	 * Slug for the "update compat-attachment-fields" action - Add Media and related dialogs
	 *
	 * @since 1.80
	 *
	 * @var	string
	 */
	const JAVASCRIPT_UPDATE_COMPAT_ACTION = 'mla-update-compat-fields';

	/**
	 * Option setting for "Featured in" reporting
	 *
	 * This setting is false if the "Featured in" database access setting is "disabled", else true.
	 *
	 * @since 1.00
	 *
	 * @var	boolean
	 */
	public static $process_featured_in = true;

	/**
	 * Option setting for "Inserted in" reporting
	 *
	 * This setting is false if the "Inserted in" database access setting is "disabled", else true.
	 *
	 * @since 1.00
	 *
	 * @var	boolean
	 */
	public static $process_inserted_in = true;

	/**
	 * Option setting for "Gallery in" reporting
	 *
	 * This setting is false if the "Gallery in" database access setting is "disabled", else true.
	 *
	 * @since 1.00
	 *
	 * @var	boolean
	 */
	public static $process_gallery_in = true;

	/**
	 * Option setting for "MLA Gallery in" reporting
	 *
	 * This setting is false if the "MLA Gallery in" database access setting is "disabled", else true.
	 *
	 * @since 1.00
	 *
	 * @var	boolean
	 */
	public static $process_mla_gallery_in = true;

	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize( ) {
		//error_log( __LINE__ . ' DEBUG: MLACore::initialize $_REQUEST = ' . var_export( $_REQUEST, true ), 0 );
		//if ( isset( $_SERVER['REQUEST_URI'] ) ) error_log( __LINE__ . ' DEBUG: MLACore::initialize $_SERVER[REQUEST_URI] = ' . var_export( $_SERVER['REQUEST_URI'], true ), 0 );
		$text_domain = 'media-library-assistant';
		$locale = function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'mla_plugin_locale', $locale, $text_domain );

		if ( is_admin() && 'en_US' === $locale ) {
			$result = unload_textdomain( $text_domain );
		}

		/*
		 * To override the plugin's translation files for one, some or all strings,
		 * create a sub-directory named 'media-library-assistant' in the WordPress
		 * WP_LANG_DIR (e.g., /wp-content/languages) directory.
		 */
		load_textdomain( $text_domain, trailingslashit( WP_LANG_DIR ) . $text_domain . '/' . $text_domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $text_domain, false, MLA_PLUGIN_BASENAME . '/languages/' );
		MLACoreOptions::mla_localize_option_definitions_array();

		if ( 'disabled' == MLACore::mla_get_option( MLACoreOptions::MLA_FEATURED_IN_TUNING ) ) {
			MLACore::$process_featured_in = false;
		}

		if ( 'disabled' == MLACore::mla_get_option( MLACoreOptions::MLA_INSERTED_IN_TUNING ) ) {
			MLACore::$process_inserted_in = false;
		}

		if ( 'disabled' == MLACore::mla_get_option( MLACoreOptions::MLA_GALLERY_IN_TUNING ) ) {
			MLACore::$process_gallery_in = false;
		}

		if ( 'disabled' == MLACore::mla_get_option( MLACoreOptions::MLA_MLA_GALLERY_IN_TUNING ) ) {
			MLACore::$process_mla_gallery_in = false;
		}

		// Load mapping rule support for Postie chron job
		if ( isset( $_REQUEST['doing_wp_cron'] ) && class_exists( 'Postie', false ) ) {
			add_action( 'postie_session_start', 'MLACore::mla_cron_mapping_support' );
		}

		// Load mapping rule support for Bulk Media Register
		if ( ( defined( 'DOING_CRON' ) && DOING_CRON ) && class_exists( 'BulkMediaRegister', false ) ) {
			add_action( 'bmr_regist', 'MLACore::mla_cron_mapping_support', 9 );
		} elseif ( class_exists( 'BulkMediaRegisterCron', false ) ) {
			// Load mapping rule support for Bulk Media Register Add On Wp Cron
			add_action( 'bmr_cron_cli', 'MLACore::mla_cron_mapping_support', 9 );
		}

		/*
		 * Look for redirects from the Media/Edit Media screen when it was picked from the
		 * "Edit" rollover action on the Media/Assistant submenu
		 */
		if ( isset( $_REQUEST['mla_source'] ) ) {
			add_filter( 'wp_redirect', 'MLACore::mla_wp_redirect_filter', 10, 2 );
		}

		/*
		 * Override the cookie-based Attachment Display Settings, if desired
		 * consider ignoring 'action' => 'send-attachment-to-editor',
		 */
		if ( 'checked' === MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_APPLY_DISPLAY_SETTINGS ) ) {
			$image_default_align = get_option( 'image_default_align' );
			$image_default_link_type = get_option( 'image_default_link_type' );
			$image_default_size = get_option( 'image_default_size' );

			if ( ! ( empty( $image_default_align ) && empty( $image_default_link_type ) && empty( $image_default_size ) ) ) {
				$user_id = get_current_user_id();
				$not_super_admin = ! (is_super_admin() && ! is_user_member_of_blog() );

				if ( $user_id && $not_super_admin ) {
					if ( isset( $_COOKIE['wp-settings-' . $user_id] ) ) {
						$cookie = preg_replace( '/[^A-Za-z0-9=&_]/', '', sanitize_text_field( wp_unslash( $_COOKIE['wp-settings-' . $user_id] ) ) );
					} else {
						$cookie = (string) get_user_option( 'user-settings', $user_id );
					}

					parse_str( $cookie, $cookie_array );
					$cookie_align = isset( $cookie_array['align'] ) ? $cookie_array['align'] : '';
					$cookie_urlbutton = isset( $cookie_array['urlbutton'] ) ? $cookie_array['urlbutton'] : '';
					$cookie_imgsize = isset( $cookie_array['imgsize'] ) ? $cookie_array['imgsize'] : '';
					$changed = false;

					if ( ( ! empty( $image_default_align ) ) && ( $cookie_align !== $image_default_align ) ) {
						$cookie_array['align'] = $image_default_align;
						$changed = true;
					}

					if ( ( ! empty( $image_default_link_type ) ) && ( $cookie_urlbutton !== $image_default_link_type ) ) {
						$cookie_array['urlbutton'] = $image_default_link_type;
						$changed = true;
					}

					if ( ( ! empty( $image_default_size ) ) && ( $cookie_imgsize !== $image_default_size ) ) {
						$cookie_array['imgsize'] = $image_default_size;
						$changed = true;
					}

					if ( $changed ) {
						$cookie = http_build_query( $cookie_array, '', '&' );
						$current = time();
						$secure = ( 'https' === parse_url( admin_url(), PHP_URL_SCHEME ) );
						setcookie( 'wp-settings-' . $user_id, $cookie, time() + YEAR_IN_SECONDS, SITECOOKIEPATH, '', $secure );
						setcookie( 'wp-settings-time-' . $user_id, $current, $current + YEAR_IN_SECONDS, SITECOOKIEPATH, '', $secure );
						$_COOKIE['wp-settings-' . $user_id] = $cookie;
						$_COOKIE['wp-settings-time-' . $user_id] = $current;
					}
				}
			}
		} // MLA_MEDIA_MODAL_APPLY_DISPLAY_SETTINGS

		// Hook wp_enqueue_media() so we can add MLA enhancements, if requested
		if ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_TOOLBAR ) ) {
			add_filter( 'media_view_settings', 'MLACore::mla_media_view_settings_filter', 10, 2 );
		}
	}

	/**
	 * Ensures that MLA mapping rules can be run from the Postie cron job.
	 * Declared public because it is an action.
	 *
	 * @since 2.90
	 */
	public static function mla_cron_mapping_support() {
		static $first_call = true;
		
		if ( $first_call ) {
			// Template file and database access functions.
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data-query.php' );
			MLAQuery::initialize();
	
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data.php' );
			MLAData::initialize();
	
			// Shortcode shim functions
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-shortcodes.php' );
			MLAShortcodes::initialize();
	
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-shortcode-support.php' );
	
			// Plugin settings management
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-options.php' );
			MLAOptions::initialize();
			
			$first_call = false;
		}
	}

	/**
	 * Ensures that MLA media manager enhancements are present when required.
	 * Declared public because it is a filter.
	 *
	 * @since 2.30
	 *
	 * @param	array	associative array with setting => value pairs
	 * @param	object || NULL	current post object, if available
	 */
	public static function mla_media_view_settings_filter( $settings, $post ) {
		if ( class_exists( 'MLAModal' ) ) {
			return $settings;
		}

		// Visual Composer template preview doesn't need MLA enhancements
		if ( defined( 'VC_IS_TEMPLATE_PREVIEW' ) ) {
			return $settings;
		}

		// Media Manager (Modal window) additions
		if ( !class_exists( 'MLAQuery' ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data-query.php' );
			MLAQuery::initialize();
		}

		if ( !class_exists( 'MLAData' ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data.php' );
			MLAData::initialize();
		}

		require_once( MLA_PLUGIN_PATH . 'includes/class-mla-list-table.php' );

		require_once( MLA_PLUGIN_PATH . 'includes/class-mla-media-modal.php' );
		MLAModal::initialize();

		add_action( 'wp_enqueue_media', 'MLACore::mla_wp_enqueue_media_action', 10, 0 );

		return MLAModal::mla_media_view_settings_filter( $settings, $post );
	}

	/**
	 * Registers and enqueues the mla-beaver-builder-style.css file, when needed.
	 * Declared public because it is an action.
	 *
	 * @since 2.30
	 */
	public static function mla_wp_enqueue_media_action( ) {
		global $wp_locale;

		if ( $wp_locale->is_rtl() ) {
			wp_register_style( MLAModal::JAVASCRIPT_MEDIA_MODAL_STYLES . '-bb', MLA_PLUGIN_URL . 'css/mla-beaver-builder-style-rtl.css', false, MLACore::mla_script_version() );
		} else {
			wp_register_style( MLAModal::JAVASCRIPT_MEDIA_MODAL_STYLES . '-bb', MLA_PLUGIN_URL . 'css/mla-beaver-builder-style.css', false, MLACore::mla_script_version() );
		}

		wp_enqueue_style( MLAModal::JAVASCRIPT_MEDIA_MODAL_STYLES . '-bb' );
	} // mla_wp_enqueue_media_action

	/**
	 * Add the WPML suppress All languages filter
	 * 
	 * The "add_action" for this function is in mla-plugin-loader.php, because the "initialize"
	 * function above doesn't run in time.
	 * Defined as public because it's an action.
	 *
	 * @since 2.32
	 *
	 * @return	void
	 */
	public static function mla_plugins_loaded_action_wpml(){
		// Defined in /sitepress-multilingual-cms/sitepress.class.php
		add_filter( 'wpml_unset_lang_admin_bar', 'MLACore::wpml_unset_lang_admin_bar', 10, 1 );
	} // mla_plugins_loaded_action_wpml

	/**
	 * Restores All languages view to Media/Assistant submenu screen
	 *
	 * @since 2.32
	 *
	 * @param	boolean	true to suppress All languages, false to allow it
	 */
	public static function wpml_unset_lang_admin_bar( $suppress_all_languages ) {
		global $pagenow, $mode;

		return $pagenow === 'upload.php' && $mode === 'grid';
	}

	/**
	 * Load a plugin text domain and alternate debug file
	 * 
	 * The "add_action" for this function is in mla-plugin-loader.php, because the "initialize"
	 * function above doesn't run in time.
	 * Defined as public because it's an action.
	 *
	 * @since 1.60
	 *
	 * @return	void
	 */
	public static function mla_plugins_loaded_action(){
	/*	$text_domain = 'media-library-assistant';
		$locale = function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'mla_plugin_locale', $locale, $text_domain );

		if ( is_admin() && 'en_US' === $locale ) {
			$result = unload_textdomain( $text_domain );
		}

		/*
		 * To override the plugin's translation files for one, some or all strings,
		 * create a sub-directory named 'media-library-assistant' in the WordPress
		 * WP_LANG_DIR (e.g., /wp-content/languages) directory.
		 * /
		load_textdomain( $text_domain, trailingslashit( WP_LANG_DIR ) . $text_domain . '/' . $text_domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $text_domain, false, MLA_PLUGIN_BASENAME . '/languages/' );

		// This must/will be repeated in class-mla-tests.php to reflect translations
		MLACoreOptions::mla_localize_option_definitions_array();

		MLACore::$original_php_log = ini_get( 'error_log' );
		MLACore::$original_php_reporting = sprintf( '0x%1$04X', error_reporting() ); // */

		// Do not process debug options unless MLA_DEBUG_LEVEL is set in wp-config.php
		if ( MLA_DEBUG_LEVEL & 1 ) {
			// Set up alternate MLA debug log file
			$error_log_name = MLACore::mla_get_option( MLACoreOptions::MLA_DEBUG_FILE, false, false, MLACoreOptions::$mla_prelocalize_option_definitions ); 
			if ( ! empty( $error_log_name ) ) {
				MLACore::mla_debug_file( $error_log_name );

				// Override PHP error_log file
				if ( 'checked' === MLACore::mla_get_option( MLACoreOptions::MLA_DEBUG_REPLACE_PHP_LOG, false, false, MLACoreOptions::$mla_prelocalize_option_definitions ) ) {
					$result = ini_set('error_log', WP_CONTENT_DIR . self::$mla_debug_file );
				}
			}

			/*
			 * PHP error_reporting must be done later in class-mla-tests.php
			 * Override MLA debug levels
			 */
			MLACore::$mla_debug_level = 0; // MLA_DEBUG_LEVEL;
			$mla_reporting = trim( MLACore::mla_get_option( MLACoreOptions::MLA_DEBUG_REPLACE_LEVEL, false, false, MLACoreOptions::$mla_prelocalize_option_definitions ) );
			if ( strlen( $mla_reporting ) ) {
				if ( ctype_digit( $mla_reporting ) ) {
					$mla_reporting = (int) $mla_reporting; 
				} else{
					$mla_reporting = hexdec( $mla_reporting ); 
				}

				if ( $mla_reporting )  {
					MLACore::$mla_debug_level = $mla_reporting | 1;
					if ( class_exists( 'MLA' ) && ! ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'heartbeat' ) ) {
						MLACore::mla_debug_add( __LINE__ . sprintf( ' MLACore::mla_plugins_loaded_action() MLA %s (%s) mla_debug_level 0x%X', MLACore::CURRENT_MLA_VERSION, MLACore::MLA_DEVELOPMENT_VERSION, MLACore::$mla_debug_level, true ), MLACore::MLA_DEBUG_CATEGORY_ANY );

						if ( ( MLACore::$mla_debug_level & MLACore::MLA_DEBUG_CATEGORY_METADATA ) && isset( $_SERVER['REQUEST_URI'] ) ) {
							$is_wplr_sync = false !== strpos( $_SERVER['REQUEST_URI'], '/?wplr-sync-api' ); // phpcs:ignore
							MLACore::mla_debug_add( __LINE__ . " MLACore::mla_plugins_loaded_action( {$is_wplr_sync} ) \$_SERVER[REQUEST_URI] = " . var_export( $_SERVER['REQUEST_URI'], true ), MLACore::MLA_DEBUG_CATEGORY_METADATA );  // phpcs:ignore
							if ( $is_wplr_sync && isset( $_POST['action'] ) ) {
								MLACore::mla_debug_add( __LINE__ . " MLACore::mla_plugins_loaded_action wplr action = " . var_export( $_POST['action'], true ), MLACore::MLA_DEBUG_CATEGORY_METADATA ); // phpcs:ignore
							}
						}
					}
				} else {
					MLACore::$mla_debug_level = 0;
				}
			}

			// Check for XMLPRC, WP REST API and front end requests
			if( !( defined('WP_ADMIN') && WP_ADMIN ) ) {
				// XMLRPC requests need everything loaded to process uploads
				if ( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST ) {
					MLACore::mla_debug_add( __LINE__ . " MLACore::mla_plugins_loaded_action() XMLRPC_REQUEST \$_REQUEST = " . var_export( $_REQUEST, true ), MLACore::MLA_DEBUG_CATEGORY_ANY );
				}

				// WP REST API calls need everything loaded to process uploads
				if ( isset( $_SERVER['REQUEST_URI'] ) && 0 === strpos( $_SERVER['REQUEST_URI'], '/wp-json/' ) ) {  // phpcs:ignore
					MLACore::mla_debug_add( __LINE__ . " MLACore::mla_plugins_loaded_action() wp-json REQUEST_URI = " . var_export( $_SERVER['REQUEST_URI'], true ), MLACore::MLA_DEBUG_CATEGORY_REST );  // phpcs:ignore
					//MLACore::mla_debug_add( __LINE__ . " MLACore::mla_plugins_loaded_action() wp-json _GET = " . var_export( $_GET, true ), MLACore::MLA_DEBUG_CATEGORY_REST );
					//MLACore::mla_debug_add( __LINE__ . " MLACore::mla_plugins_loaded_action() wp-json _POST = " . var_export( $_POST, true ), MLACore::MLA_DEBUG_CATEGORY_REST );
					MLACore::mla_debug_add( __LINE__ . " MLACore::mla_plugins_loaded_action() wp-json _REQUEST = " . var_export( $_REQUEST, true ), MLACore::MLA_DEBUG_CATEGORY_REST );
					MLACore::mla_debug_add( __LINE__ . " MLACore::mla_plugins_loaded_action() wp-json _COOKIE = " . var_export( $_COOKIE, true ), MLACore::MLA_DEBUG_CATEGORY_REST );
					if ( function_exists( 'apache_request_headers' ) ) {
						MLACore::mla_debug_add( __LINE__ . " MLACore::mla_plugins_loaded_action() wp-json MLAOptions apache_request_headers = " . var_export( apache_request_headers(), true ), MLACore::MLA_DEBUG_CATEGORY_REST );
					}
					MLACore::mla_debug_add( __LINE__ . " MLACore::mla_plugins_loaded_action() wp-json MLAOptions exists = " . var_export( class_exists( 'MLAOptions' ), true ), MLACore::MLA_DEBUG_CATEGORY_REST );
				}
			} // not admin
		} // MLA_DEBUG_LEVEL & 1
	}

	/**
	 * Create version number for script files with/without Development Version date
	 *
	 * @since 2.99
	 *
	 * @return string Version number for wp_enqueue_script()
	 */
	public static function mla_script_version() {
		$script_version =  MLACore::CURRENT_MLA_VERSION;
		$script_version .=  ( strlen( MLACore::MLA_DEVELOPMENT_VERSION ) ) ? '.' . MLACore::MLA_DEVELOPMENT_VERSION : '';
		
		return $script_version;
	}

	/**
	 * Create a NONCE URL that works in WP 3.5.x and later
	 *
	 * @since 2.71
	 *
	 * @param string $actionurl URL to add nonce action.
	 * @param string $action    Optional. Nonce action name. Default -1.
	 * @param string $name      Optional. Nonce name. Default '_wpnonce'.
	 *
	 * @return string Escaped URL with nonce action added.
	 */
	public static function mla_nonce_url( $actionurl, $action = -1, $name = '_wpnonce' ) {
		$actionurl = wp_nonce_url( $actionurl, $action, $name );

		// WP 3.5.x wp_nonce_url() does not accept the third NONCE name argument
		if ( '_wpnonce' !== $name ) {
			$actionurl = str_replace( '_wpnonce', $name, $actionurl );
		}

		return $actionurl;
	}

	/**
	 * Filter the redirect location.
	 *
	 * @since 2.25
	 *
	 * @param string $location The path to redirect to.
	 * @param int    $status   Status code to use.
	 */
	public static function mla_wp_redirect_filter( $location, $status ) {
		// Check for Update, Trash or Delete Permanently on Media/Edit Media screen,
		if ( ( false !== strpos( $location, 'upload.php?' ) ) || ( false !== strpos( $location, 'post.php?' ) ) ) {
			if ( isset( $_REQUEST['mla_source'] ) ) {
				$location = add_query_arg( array( 'mla_source' => esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['mla_source'] ) ) ) ), $location );
			}
		}

		return $location;
	}

	/**
	 * Initialize "tax_checked_on_top" => "checked" default for all supported taxonomies
	 *
	 * Called after all taxonomies are registered, e.g., in MLAObjects::_build_taxonomies.
	 *
	 * @since 2.02
	 *
	 * @return	void
	 */
	public static function mla_initialize_tax_checked_on_top() {
		if ( NULL === MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_checked_on_top'] ) {
			/*
			 * WordPress default is 'checked_ontop' => true
			 * Initialize tax_checked_on_top defaults to true for all supported taxonomies
			 */		
			$checked_on_top = array();
			$taxonomies = MLACore::mla_supported_taxonomies();
			foreach ( $taxonomies as $new_key ) {
				$checked_on_top[ $new_key ] = 'checked';
			}

			MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_checked_on_top'] = $checked_on_top;
		}
	}

	/**
	 * Return the stored value or default value of a defined MLA option
	 *
	 * @since 2.20
	 *
	 * @param	string 	Name of the desired option
	 * @param	boolean	True to ignore current setting and return default values
	 * @param	boolean	True to ignore default values and return only stored values
	 * @param	array	Custom option definitions
	 * 
	 *
	 * @return	mixed	Value(s) for the option or false if the option is not a defined MLA option
	 */
	public static function mla_get_option( $option, $get_default = false, $get_stored = false, &$option_table = NULL ) {
		if ( NULL === $option_table ) {
			if ( empty( MLACoreOptions::$mla_option_definitions ) ) {
				MLACoreOptions::mla_localize_option_definitions_array();
			}

			$option_table =& MLACoreOptions::$mla_option_definitions;
		}

		if ( ! array_key_exists( $option, $option_table ) ) {
			return false;
		}

		if ( $get_default ) {
			if ( array_key_exists( 'std', $option_table[ $option ] ) ) {
				return $option_table[ $option ]['std'];
			}

			return false;
		} // $get_default

		if ( ! $get_stored && array_key_exists( 'std', $option_table[ $option ] ) ) {
			return get_option( MLA_OPTION_PREFIX . $option, $option_table[ $option ]['std'] );
		}

		return get_option( MLA_OPTION_PREFIX . $option, false );
	}

	/**
	 * Add or update the stored value of a defined MLA option
	 *
	 * @since 2.20
	 *
	 * @param	string 	Name of the desired option
	 * @param	mixed 	New value for the desired option
	 * @param	array	Custom option definitions
	 *
	 * @return	boolean	True if the value was changed or false if the update failed
	 */
	public static function mla_update_option( $option, $newvalue, &$option_table = NULL ) {
		if ( NULL == $option_table ) {
			if ( empty( MLACoreOptions::$mla_option_definitions ) ) {
				MLACoreOptions::mla_localize_option_definitions_array();
			}

			$option_table =& MLACoreOptions::$mla_option_definitions;
		}

		if ( array_key_exists( $option, $option_table ) ) {
			if ( isset( $option_table[ $option ]['autoload'] ) ) {
				$autoload = (boolean) $option_table[ $option ]['autoload'];
			} else {
				$autoload = true;
			}

			return update_option( MLA_OPTION_PREFIX . $option, $newvalue, $autoload );
		}

		return false;
	}

	/**
	 * Delete the stored value of a defined MLA option
	 *
	 * @since 2.20
	 *
	 * @param	string 	Name of the desired option
	 * @param	array	Custom option definitions
	 *
	 * @return	boolean	True if the option was deleted, otherwise false
	 */
	public static function mla_delete_option( $option, &$option_table = NULL ) {
		if ( NULL == $option_table ) {
			if ( empty( MLACoreOptions::$mla_option_definitions ) ) {
				MLACoreOptions::mla_localize_option_definitions_array();
			}

			$option_table =& MLACoreOptions::$mla_option_definitions;
		}

		if ( array_key_exists( $option, $option_table ) ) {
			return delete_option( MLA_OPTION_PREFIX . $option );
		}

		return false;
	}

	/**
	 * Load an HTML template from a file
	 *
	 * Loads a template to a string or a multi-part template to an array.
	 * Multi-part templates are divided by comments of the form <!-- template="key" -->,
	 * where "key" becomes the key part of the array.
	 *
	 * @since 0.1
	 *
	 * @param	string 	Complete path and/or name of the template file, option name or the raw template
	 * @param	string 	Optional type of template source; 'path', 'file' (default), 'option', 'string'
	 *
	 * @return	string|array|false|NULL
	 *			string for files that do not contain template divider comments,
	 *			array for files containing template divider comments,
	 *			false if file or option does not exist,
	 *			NULL if file could not be loaded.
	 */
	public static function mla_load_template( $source, $type = 'file' ) {
		switch ( $type ) {
			case 'file':
				/*
				 * Look in three places, in this order:
				 * 1) Custom templates
				 * 2) Language-specific templates
				 * 3) Standard templates
				 */
				$text_domain = 'media-library-assistant';
				$locale = apply_filters( 'mla_plugin_locale', get_locale(), $text_domain );
				$path = trailingslashit( WP_LANG_DIR ) . $text_domain . '/tpls/' . $locale . '/' . $source;
				if ( file_exists( $path ) ) {
					$source = $path;
				} else {
					$path = MLA_PLUGIN_PATH . 'languages/tpls/' . $locale . '/' . $source;
					if ( file_exists( $path ) ) {
						$source = $path;
					} else {
						$source = MLA_PLUGIN_PATH . 'tpls/' . $source;
					}
				}
				// fallthru
			case 'path':
				if ( !file_exists( $source ) ) {
					return false;
				}

				$template = file_get_contents( $source, true );
				if ( $template == false ) {
					/* translators: 1: ERROR tag 2: path and file name */
					MLACore::mla_debug_add( sprintf( _x( '%1$s: mla_load_template file "%2$s" not found.', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), var_export( $source, true ) ), MLACore::MLA_DEBUG_CATEGORY_ANY );
					return NULL;
				}
				break;
			case 'option':
				$template = MLACore::mla_get_option( $source );
				if ( $template == false ) {
					return false;
				}
				break;
			case 'string':
				$template = $source;
				if ( empty( $template ) ) {
					return false;
				}
				break;
			default:
				/* translators: 1: ERROR tag 2: path and file name 3: source type, e.g., file, option, string */
				MLACore::mla_debug_add( sprintf( _x( '%1$s: mla_load_template file "%2$s" bad source type "%3$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $source, $type ), MLACore::MLA_DEBUG_CATEGORY_ANY );
				return NULL;
		}

		$match_count = preg_match_all( '#\<!-- template=".+" --\>#', $template, $matches, PREG_OFFSET_CAPTURE );

		if ( ( $match_count == false ) || ( $match_count == 0 ) ) {
			return $template;
		}

		$matches = array_reverse( $matches[0] );

		$template_array = array();
		$current_offset = strlen( $template );
		foreach ( $matches as $key => $value ) {
			$template_key = preg_split( '#"#', $value[0] );
			$template_key = $template_key[1];
			$template_value = substr( $template, $value[1] + strlen( $value[0] ), $current_offset - ( $value[1] + strlen( $value[0] ) ) );
			/*
			 * Trim exactly one newline sequence from the start of the value
			 */
			if ( 0 === strpos( $template_value, "\r\n" ) ) {
				$offset = 2;
			} elseif ( 0 === strpos( $template_value, "\n\r" ) ) {
				$offset = 2;
			} elseif ( 0 === strpos( $template_value, "\n" ) ) {
				$offset = 1;
			} elseif ( 0 === strpos( $template_value, "\r" ) ) {
				$offset = 1;
			} else {
				$offset = 0;
			}

			$template_value = substr( $template_value, $offset );

			/*
			 * Trim exactly one newline sequence from the end of the value
			 */
			$length = strlen( $template_value );
			if ( $length > 2) {
				$postfix = substr( $template_value, ($length - 2), 2 );
			} else {
				$postfix = $template_value;
			}

			if ( 0 === strpos( $postfix, "\r\n" ) ) {
				$length -= 2;
			} elseif ( 0 === strpos( $postfix, "\n\r" ) ) {
				$length -= 2;
			} elseif ( 0 === strpos( $postfix, "\n" ) ) {
				$length -= 1;
			} elseif ( 0 === strpos( $postfix, "\r" ) ) {
				$length -= 1;
			}

			$template_array[ $template_key ] = substr( $template_value, 0, $length );
			$current_offset = $value[1];
		} // foreach $matches

		return $template_array;
	}

	/**
	 * Determine MLA support for a taxonomy, handling the special case where the
	 * settings are being updated or reset.
 	 *
	 * @since 2.20
	 *
	 * @param	string	Taxonomy name, e.g., attachment_category
	 * @param	string	Optional. 'support' (default), 'quick-edit' or 'filter'
	 *
	 * @return	boolean|string
	 *			true if the taxonomy is supported in this way else false.
	 *			string if $tax_name is '' and $support_type is 'filter', returns the taxonomy to filter by.
	 *			string if $support_type is 'metakey', returns the custom field to filter by.
	 */
	public static function mla_taxonomy_support($tax_name, $support_type = 'support') {
		$tax_options =  MLACore::mla_get_option( MLACoreOptions::MLA_TAXONOMY_SUPPORT );

		switch ( $support_type ) {
			case 'support': 
				if ( !empty( $_REQUEST['mla-general-options-save'] ) ) {
					return isset( $_REQUEST['tax_support'][ $tax_name ] );
				} elseif ( !empty( $_REQUEST['mla-general-options-reset'] ) ) {
					return array_key_exists( $tax_name, MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_support'] );
				}

				$tax_support = isset( $tax_options['tax_support'] ) ? $tax_options['tax_support'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_support'];
				return array_key_exists( $tax_name, $tax_support );
			case 'quick-edit':
				if ( !empty( $_REQUEST['mla-general-options-save'] ) ) {
					return isset( $_REQUEST['tax_quick_edit'][ $tax_name ] );
				} elseif ( !empty( $_REQUEST['mla-general-options-reset'] ) ) {
					return array_key_exists( $tax_name, MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_quick_edit'] );
				}

				$tax_quick_edit = isset( $tax_options['tax_quick_edit'] ) ? $tax_options['tax_quick_edit'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_quick_edit'];
				return array_key_exists( $tax_name, $tax_quick_edit );
			case 'term-search':
				if ( !empty( $_REQUEST['mla-general-options-save'] ) ) {
					return isset( $_REQUEST['tax_term_search'][ $tax_name ] );
				} elseif ( !empty( $_REQUEST['mla-general-options-reset'] ) ) {
					return array_key_exists( $tax_name, MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_term_search'] );
				}

				$tax_term_search = isset( $tax_options['tax_term_search'] ) ? $tax_options['tax_term_search'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_term_search'];
				return array_key_exists( $tax_name, $tax_term_search );
			case 'flat-checklist':
				if ( !empty( $_REQUEST['mla-general-options-save'] ) ) {
					return isset( $_REQUEST['tax_flat_checklist'][ $tax_name ] );
				} elseif ( !empty( $_REQUEST['mla-general-options-reset'] ) ) {
					return array_key_exists( $tax_name, MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_flat_checklist'] );
				}

				$tax_flat_checklist = isset( $tax_options['tax_flat_checklist'] ) ? $tax_options['tax_flat_checklist'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_flat_checklist'];
				return array_key_exists( $tax_name, $tax_flat_checklist );
			case 'checked-on-top':
				if ( !empty( $_REQUEST['mla-general-options-save'] ) ) {
					return isset( $_REQUEST['tax_checked_on_top'][ $tax_name ] );
				} elseif ( !empty( $_REQUEST['mla-general-options-reset'] ) ) {
					return array_key_exists( $tax_name, MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_checked_on_top'] );
				}

				$tax_checked_on_top = isset( $tax_options['tax_checked_on_top'] ) ? $tax_options['tax_checked_on_top'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_checked_on_top'];
				return array_key_exists( $tax_name, $tax_checked_on_top );
			case 'checklist-add-term':
				if ( !empty( $_REQUEST['mla-general-options-save'] ) ) {
					return isset( $_REQUEST['tax_checklist_add_term'][ $tax_name ] );
				} elseif ( !empty( $_REQUEST['mla-general-options-reset'] ) ) {
					return array_key_exists( $tax_name, MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_checklist_add_term'] );
				}

				$tax_checklist_add_term = isset( $tax_options['tax_checklist_add_term'] ) ? $tax_options['tax_checklist_add_term'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_checklist_add_term'];
				return array_key_exists( $tax_name, $tax_checklist_add_term );
			case 'filter':
				$tax_filter = isset( $tax_options['tax_filter'] ) ? $tax_options['tax_filter'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_filter'];
				if ( '' == $tax_name ) {
					return $tax_filter;
				}

				if ( !empty( $_REQUEST['mla-general-options-save'] ) ) {
					$tax_filter = isset( $_REQUEST['tax_filter'] ) ? sanitize_text_field( wp_unslash ( $_REQUEST['tax_filter'] ) ) : '';
					return ( $tax_name == $tax_filter );
				} elseif ( !empty( $_REQUEST['mla-general-options-reset'] ) ) {
					return array_key_exists( $tax_name, MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_filter'] );
				}

				return ( $tax_name == $tax_filter );
			case 'metakey':
				if ( !empty( $_REQUEST['mla-general-options-save'] ) ) {
					return isset( $_REQUEST['tax_metakey'] ) ? sanitize_text_field( wp_unslash ( $_REQUEST['tax_metakey'] ) ) : '';
				} elseif ( !empty( $_REQUEST['mla-general-options-reset'] ) ) {
					return MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_metakey'];
				}

				return isset( $tax_options['tax_metakey'] ) ? $tax_options['tax_metakey'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_metakey'];
			case 'metakey_sort':
				if ( !empty( $_REQUEST['mla-general-options-save'] ) ) {
					return isset( $_REQUEST['tax_metakey_sort'] ) ? sanitize_text_field( wp_unslash ( $_REQUEST['tax_metakey_sort'] ) ) : '';
				} elseif ( !empty( $_REQUEST['mla-general-options-reset'] ) ) {
					return MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_metakey_sort'];
				}

				return isset( $tax_options['tax_metakey_sort'] ) ? $tax_options['tax_metakey_sort'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_metakey_sort'];
			default:
				return false;
		} // $support_type
	} // mla_taxonomy_support

	/**
	 * Returns an array of taxonomy names assigned to $support_type
 	 *
	 * @since 2.20
	 *
	 * @param	string	Optional. 'support' (default), 'quick-edit', 'flat-checklist', 'term-search' or 'filter'
	 *
	 * @return	array|string
	 *			array	taxonomies assigned to $support_type; can be empty.
	 *			string if $support_type is 'metakey', returns the custom field to filter by.
	 */
	public static function mla_supported_taxonomies( $support_type = 'support' ) {
		$tax_options =  MLACore::mla_get_option( MLACoreOptions::MLA_TAXONOMY_SUPPORT );

		switch ( $support_type ) {
			case 'support': 
				if ( !empty( $_REQUEST['mla-general-options-save'] ) ) {
					$tax_support = isset( $_REQUEST['tax_support'] ) ? array_map( 'sanitize_text_field', wp_unslash ( $_REQUEST['tax_support'] ) ) : array();
					return array_keys( $tax_support );
				} elseif ( !empty( $_REQUEST['mla-general-options-reset'] ) ) {
					return array_keys( MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_support'] );
				}

				return array_keys( isset( $tax_options['tax_support'] ) ? $tax_options['tax_support'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_support'] );
			case 'quick-edit':
				if ( !empty( $_REQUEST['mla-general-options-save'] ) ) {
					$tax_quick_edit = isset( $_REQUEST['tax_quick_edit'] ) ? array_map( 'sanitize_text_field', wp_unslash ( $_REQUEST['tax_quick_edit'] ) ) : array();
					return array_keys( $tax_quick_edit );
				} elseif ( !empty( $_REQUEST['mla-general-options-reset'] ) ) {
					return array_keys( MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_quick_edit'] );
				}

				return array_keys( isset( $tax_options['tax_quick_edit'] ) ? $tax_options['tax_quick_edit'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_quick_edit'] );
			case 'term-search':
				if ( !empty( $_REQUEST['mla-general-options-save'] ) ) {
					$tax_term_search = isset( $_REQUEST['tax_term_search'] ) ? array_map( 'sanitize_text_field', wp_unslash ( $_REQUEST['tax_term_search'] ) ) : array();
					return array_keys( $tax_term_search );
				} elseif ( !empty( $_REQUEST['mla-general-options-reset'] ) ) {
					return array_keys( MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_term_search'] );
				}

				return array_keys( isset( $tax_options['tax_term_search'] ) ? $tax_options['tax_term_search'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_term_search'] );
			case 'flat-checklist':
				if ( !empty( $_REQUEST['mla-general-options-save'] ) ) {
					$tax_flat_checklist = isset( $_REQUEST['tax_flat_checklist'] ) ? array_map( 'sanitize_text_field', wp_unslash ( $_REQUEST['tax_flat_checklist'] ) ) : array();
					return array_keys( $tax_flat_checklist );
				} elseif ( !empty( $_REQUEST['mla-general-options-reset'] ) ) {
					return array_keys( MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_flat_checklist'] );
				}

				return array_keys( isset( $tax_options['tax_flat_checklist'] ) ? $tax_options['tax_flat_checklist'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_flat_checklist'] );
			case 'checked-on-top':
				if ( !empty( $_REQUEST['mla-general-options-save'] ) ) {
					$tax_checked_on_top = isset( $_REQUEST['tax_checked_on_top'] ) ? array_map( 'sanitize_text_field', wp_unslash ( $_REQUEST['tax_checked_on_top'] ) ) : array();
					return array_keys( $tax_checked_on_top );
				} elseif ( !empty( $_REQUEST['mla-general-options-reset'] ) ) {
					return array_keys( MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_checked_on_top'] );
				}

				return array_keys( isset( $tax_options['tax_checked_on_top'] ) ? $tax_options['tax_checked_on_top'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_checked_on_top'] );
			case 'checklist-add-term':
				if ( !empty( $_REQUEST['mla-general-options-save'] ) ) {
					$tax_checklist_add_term = isset( $_REQUEST['tax_checklist_add_term'] ) ? array_map( 'sanitize_text_field', wp_unslash ( $_REQUEST['tax_checklist_add_term'] ) ) : array();
					return array_keys( $tax_checklist_add_term );
				} elseif ( !empty( $_REQUEST['mla-general-options-reset'] ) ) {
					return array_keys( MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_checklist_add_term'] );
				}

				return array_keys( isset( $tax_options['tax_checklist_add_term'] ) ? $tax_options['tax_checklist_add_term'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_checklist_add_term'] );
			case 'filter':
				if ( !empty( $_REQUEST['mla-general-options-save'] ) ) {
					return isset( $_REQUEST['tax_filter'] ) ? (array) sanitize_text_field( wp_unslash( $_REQUEST['tax_filter'] ) ) : array();
				} elseif ( !empty( $_REQUEST['mla-general-options-reset'] ) ) {
					return (array) MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_filter'];
				}

				return (array) isset( $tax_options['tax_filter'] ) ? $tax_options['tax_filter'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_filter'];
			case 'metakey':
				if ( !empty( $_REQUEST['mla-general-options-save'] ) ) {
					return isset( $_REQUEST['tax_metakey'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tax_metakey'] ) ) : '';
				} elseif ( !empty( $_REQUEST['mla-general-options-reset'] ) ) {
					return MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_metakey'];
				}

				return isset( $tax_options['tax_metakey'] ) ? $tax_options['tax_metakey'] : MLACoreOptions::$mla_option_definitions[ MLACoreOptions::MLA_TAXONOMY_SUPPORT ]['std']['tax_metakey'];
			default:
				return array();
		} // $support_type
	} // mla_supported_taxonomies

	/**
	 * Evaluate support information for custom field mapping
 	 *
	 * @since 1.10
	 *
	 * @param	string	array format; 'default_columns' (default), 'default_hidden_columns', 'default_sortable_columns', 'quick_edit' or 'bulk_edit'
	 *
	 * @return	array	default, hidden, sortable quick_edit or bulk_edit colums in appropriate format
	 */
	public static function mla_custom_field_support( $support_type = 'default_columns' ) {
		$option_values = MLACore::mla_get_option( 'custom_field_mapping' );
		$results = array();
		$index = 0;

		foreach ( $option_values as $key => $value ) {
			if ( isset( $value['active'] ) &&( false === $value['active'] ) ) {
				continue;
			}
			
			$slug = 'c_' . $index++; // sanitize_title( $key ); Didn't handle HTML in name, e.g., "R><B"

			switch( $support_type ) {
				case 'custom_columns':
					if ( $value['mla_column'] ) {
						$results[ $slug ] = $value['name'];
					}
					break;
				case 'default_columns':
					if ( $value['mla_column'] ) {
						$results[ $slug ] = esc_html( $value['name'] );
					}
					break;
				case 'default_hidden_columns':
					if ( $value['mla_column'] ) {
						$results[] = $slug;
					}
					break;
				case 'custom_sortable_columns':
					if ( $value['mla_column'] ) {
						// columns without NULL values should sort descending
						$results[ $slug ] = array( $value['name'], $value['no_null'] );
					}
					break;
				case 'default_sortable_columns':
					if ( $value['mla_column'] ) {
						// columns without NULL values should sort descending
						$results[ $slug ] = array( $slug, $value['no_null'] );
					}
					break;
				case 'quick_edit':
					if ( $value['quick_edit'] ) {
						$results[ $slug ] = $value;
					}
					break;
				case 'bulk_edit':
					if ( $value['bulk_edit'] ) {
						$results[ $slug ] = $value;
					}
					break;
			} // switch support_type
		} // foreach option_value

		return $results;
	} // mla_custom_field_support

	/**
	 * Convert a Library View/Post MIME Type specification to WP_Query parameters
	 *
	 * @since 1.40
	 *
	 * @param	string	View slug, unique identifier
	 * @param	string	A specification, e.g., "custom:Field,null" or "audio,application/vnd.*ms*"
	 *
	 * @return	array	post_mime_type specification or custom field query
	 */
	public static function mla_prepare_view_query( $slug, $specification ) {
		// For this query we don't need to cache anything, since we won't access the items themselves
		$query = array (
			'cache_results' => 'false',
			'update_post_meta_cache' => 'false',
			'update_post_term_cache' => 'false',
		);

		$specification = self::mla_parse_view_specification( $specification );
		if ( !empty( $specification['mime'] ) ) {
			$query['post_mime_type'] = $specification['mime']['value'];
		}
		
		if ( !empty( $specification['custom'] ) ) {
			$meta_query = array( 'slug' => $slug , 'relation' => 'OR', 'patterns' => array () );
			switch( $specification['custom']['option'] ) {
				case 'match':
					$patterns = array_map( 'trim', explode( ',', $specification['custom']['value'] ) );
					foreach ( (array) $patterns as $pattern ) {
						$meta_key = ( !empty( $specification['custom']['name'] ) ) ? array( 'key' => $specification['custom']['name'] ) : array();
						$pattern = preg_replace( '/\*+/', '%', $pattern );
						if ( false !== strpos( $pattern, '%' ) ) {
							// Preserve the pattern - it will be used in the "where" filter
							$meta_query['patterns'][] = $pattern;
							$meta_query[] = array_merge( $meta_key, array( 'value' => $pattern, 'compare' => 'LIKE' ) );
						} else {
							$meta_query[] = array_merge( $meta_key, array( 'value' => $pattern, 'compare' => '=' ) );
						}
					} // foreach pattern

					if ( empty( $meta_query['patterns'] ) ) {
						unset( $meta_query['patterns'] );
					}

					break;
				case 'null':
					if ( !empty( $specification['custom']['name'] ) ) {
						$meta_query['key'] = $specification['custom']['name'];
					}

					$meta_query['value'] = 'NULL';
					break;
				default: // '', 'any'
					if ( !empty( $specification['custom']['name'] ) ) {
						$meta_query[] = array( 'key' => $specification['custom']['name'], 'value' => NULL, 'compare' => '!=' );
					} else {
						$meta_query[] = array( 'value' => NULL, 'compare' => '!=' );
					}
			}

			$query['meta_query'] = $meta_query;
		} // custom field specification

//error_log( __LINE__ . " MLACore::mla_prepare_view_query( {$slug} ) query = " . var_export( $query, true ), 0 );
		return $query;
	}

	/**
	 * Analyze a Library View/Post MIME Type specification, returning an array of the placeholders it contains
	 *
	 * @since 1.40
	 *
	 * @param	string|array	A specification, e.g., "custom:Field,null" or "audio,application/vnd.*ms*"
	 *
	 * @return	array	( ['prefix'] => string, ['name'] => string, ['value'] => string, ['option'] => string, optional ['error'] => string )
	 */
	public static function mla_parse_view_specification( $specification ) {
		if ( is_array( $specification ) ) {
			$specification = @implode( ',', $specification );
		}
//error_log( __LINE__ . ' MLACore::mla_parse_view_specification specification = ' . var_export( $specification, true ), 0 );

		$result = array( 'mime' => NULL, 'custom' => NULL );

		// look for custom field query, must be at the end of the specification
		$custom_offset = strpos( $specification, 'custom:' );
		if ( false === $custom_offset ) {
			$result['mime'] = array( 'prefix' => 'mime', 'name' => '', 'value' => $specification, 'option' => '' );
		} else {
			$result['custom'] = array( 'prefix' => 'custom', 'name' => '', 'value' => substr( $specification, $custom_offset ), 'option' => '' );

			if ( 0 < $custom_offset ) {
				// A MIME specification can precede the custom field query
				$result['mime'] = array( 'prefix' => 'mime', 'name' => '', 'value' => substr( $specification, 0, $custom_offset - 1 ), 'option' => '' );
			}
		}
//error_log( __LINE__ . ' MLACore::mla_parse_view_specification result = ' . var_export( $result, true ), 0 );
		
		if ( !empty( $result['custom'] ) ) {
			$match_count = preg_match( '/^(.+):(.+)/', $result['custom']['value'], $matches );
			$result['custom']['value'] = '';
			
			if ( 1 == $match_count ) {
//error_log( __LINE__ . ' MLACore::mla_parse_view_specification matches = ' . var_export( $matches, true ), 0 );
				$result['custom']['prefix'] = trim( strtolower( $matches[1] ) );
				$tail = $matches[2];
//error_log( __LINE__ . ' MLACore::mla_parse_view_specification tail = ' . var_export( $tail, true ), 0 );

				// Look for field name(s)
				$flag = '<found multiple names in the tail>';
				$match_count = preg_match( '/([^=]+)((=)(.*))$/', $tail, $matches );
				if ( 1 == $match_count ) {
//error_log( __LINE__ . ' MLACore::mla_parse_view_specification matches = ' . var_export( $matches, true ), 0 );
					$result['custom']['name'] = explode( ',', $matches[1] );
					// Flag multiple field names for preservation
					if ( 1 < count( $result['custom']['name'] ) ) {
						$tail = $flag . $matches[2];
					}
				}

				$match_count = preg_match( '/([^,=]+)((,|=)(.*))$/', $tail, $matches );
				if ( 1 == $match_count ) {
//error_log( __LINE__ . ' MLACore::mla_parse_view_specification matches = ' . var_export( $matches, true ), 0 );
					// Preserve multiple field names, handle "any field"; name = *
					if ( $flag !== $matches[1] ) {
						$result['custom']['name'] = ( '*' === $matches[1] ) ? '' : $matches[1];
					}

					if ( ',' == $matches[3] ) {
						$result['custom']['option'] = trim( strtolower( $matches[4] ));
					} else {
						if ( empty( $matches[4] ) ) {
							$result['custom']['option'] = 'null';
						} elseif ( '*' == $matches[4] ) {
							$result['custom']['option'] = 'any';
						} else {
							$result['custom']['option'] = 'match';
							$result['custom']['value'] = $matches[4];
						}
					}
				} else {
					$result['custom']['option'] = 'any';
					$result['custom']['name'] = $tail;
				}
			}
		} // !empty( $result['custom']

		// Validate the results
		if ( !empty( $result['mime'] ) ) {
			$mime_types = array_map( 'trim', explode( ',', $result['mime']['value'] ) );
			foreach ( (array) $mime_types as $raw_mime_type ) {
				$no_wildcards = str_replace( '*', 'X', $raw_mime_type );
				$clean_mime_type = sanitize_mime_type( $no_wildcards );
				if ( $clean_mime_type != $no_wildcards ) {
					/* translators: 1: ERROR tag 2: raw_mime_type */
					$result['mime']['error'] = '<br>' . sprintf( __( '%1$s: Bad specification part "%2$s"', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $raw_mime_type );
				}
			} // foreach
		} 
			
		if ( !empty( $result['custom'] ) ) {
			if ( 'custom' === $result['custom']['prefix'] ) {
				if ( ! in_array( $result['custom']['option'], array( '', 'any', 'match', 'null' ) ) ) {
					/* translators: 1: ERROR tag 2: option, e.g., any, match, null */
					$result['custom']['error'] = '<br>' . sprintf( __( '%1$s: Bad specification option "%2$s"', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $result['custom']['option'] );
				}
			} else {
				/* translators: 1: ERROR tag 2: prefix, e.g., custom */
				$result['custom']['error'] = '<br>' . sprintf( __( '%1$s: Bad specification prefix "%2$s"', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $result['custom']['prefix'] );
			}
		}

//error_log( __LINE__ . ' MLACore::mla_parse_view_specification result = ' . var_export( $result, true ), 0 );
		return $result;
	}

	/**
	 * Display taxonomy "checklist" form fields
	 *
	 * Adapted from /wp-admin/includes/ajax-actions.php function _wp_ajax_add_hierarchical_term().
	 * Includes the "? Search" area to filter the term checklist by entering part
	 * or all of a word/phrase in the term label.
	 * Output to the Media/Edit Media screen and to the Media Manager Modal Window.
	 *
	 * @since 1.71
	 *
	 * @param object The current post
	 * @param array The meta box parameters
	 * @param string Optional prefix to make HTML ID unique
	 *
	 * @return void Echoes HTML for the form fields
	 */
	public static function mla_checklist_meta_box( $target_post, $box, $prefix = '' ) {
		global $post;

		$defaults = array('taxonomy' => 'category', 'in_modal' => false );
		$post_id = $target_post->ID;

		if ( !isset( $box['args'] ) || !is_array( $box['args'] ) ) {
			$args = array();
		} else {
			$args = $box['args'];
		}

		extract( wp_parse_args( $args, $defaults ), EXTR_SKIP );
		$tax = get_taxonomy( $taxonomy );
		$name = ( $taxonomy == 'category' ) ? 'post_category' : 'tax_input[' . $taxonomy . ']';

		/*
		 * Id and Name attributes in the popup Modal Window must not conflict with
		 * the underlying Edit Post/Page window, so we prefix with "mla-"/"mla_".
		 */
		if ( $in_modal ) {
			if ( empty( $post ) ) {
				$post = $target_post; // for wp_popular_terms_checklist
			}

			$id_prefix = "mla-{$prefix}-{$taxonomy}";
			$div_taxonomy_id = "mla-taxonomy-{$taxonomy}";
			$tabs_ul_id = "{$id_prefix}-tabs";
			$tab_all_id = "{$id_prefix}-all";
			$tab_all_ul_id = "{$id_prefix}-checklist"; // wp-lists
			$tab_pop_id = "{$id_prefix}-pop";
			$tab_pop_ul_id = "{$id_prefix}-checklist-pop";
			$input_terms_name = "mla_attachments[{$post_id}][{$name}][]";
			$input_terms_id = "mla-{$name}-id";
			$div_adder_id = "{$id_prefix}-adder";
			$div_adder_class = "mla-hidden-children";
			$link_adder_id = "{$id_prefix}-add-toggle";
			$link_adder_p_id = "{$id_prefix}-add"; // wp-lists
			$div_search_id = "{$id_prefix}-searcher";
			$div_search_class = "mla-hidden-children";
			$link_search_id = "{$id_prefix}-search-toggle";
			$link_search_p_id = "{$id_prefix}-search";
			$input_new_name = "new{$taxonomy}";
			$input_new_id = "mla-new-{$taxonomy}";
			$input_new_parent_name = "new{$taxonomy}_parent";
			$input_new_submit_id = "{$id_prefix}-add-submit";
			$span_new_ajax_id = "{$id_prefix}-ajax-response";
			$input_search_name = "search-{$taxonomy}";
			$input_search_id = "mla-search-{$taxonomy}";
			$span_search_ajax_id = "{$id_prefix}-search-ajax-response";
		} else {
			$div_taxonomy_id = "taxonomy-{$taxonomy}";
			$tabs_ul_id = "{$taxonomy}-tabs";
			$tab_all_id = "{$taxonomy}-all";
			$tab_all_ul_id = "{$taxonomy}checklist";
			$tab_pop_id = "{$taxonomy}-pop";
			$tab_pop_ul_id = "{$taxonomy}checklist-pop";
			$input_terms_name = "{$name}[]";
			$input_terms_id = "{$name}-id";
			$div_adder_id = "{$taxonomy}-adder";
			$div_adder_class = "wp-hidden-children";
			$link_adder_id = "{$taxonomy}-add-toggle";
			$link_adder_p_id = "{$taxonomy}-add";
			$div_search_id = "{$taxonomy}-searcher";
			$div_search_class = "wp-hidden-children";
			$link_search_id = "{$taxonomy}-search-toggle";
			$link_search_p_id = "{$taxonomy}-search";
			$input_new_name = "new{$taxonomy}";
			$input_new_id = "new{$taxonomy}";
			$input_new_parent_name = "new{$taxonomy}_parent";
			$input_new_submit_id = "{$taxonomy}-add-submit";
			$span_new_ajax_id = "{$taxonomy}-ajax-response";
			$input_search_name = "search-{$taxonomy}";
			$input_search_id = "search-{$taxonomy}";
			$span_search_ajax_id = "{$taxonomy}-search-ajax-response";
		}
		?>
		<div id="<?php echo esc_html( $div_taxonomy_id ); ?>" class="categorydiv">
			<ul id="<?php echo esc_html( $tabs_ul_id ); ?>" class="category-tabs">
				<li class="tabs"><a href="#<?php echo esc_html( $tab_all_id ); ?>"><?php echo esc_html( $tax->labels->all_items ); ?></a></li>
				<li class="hide-if-no-js"><a href="#<?php echo esc_html( $tab_pop_id ); ?>"><?php esc_html_e( 'Most Used' ); ?></a></li>
			</ul>

			<div id="<?php echo esc_html( $tab_pop_id ); ?>" class="tabs-panel" style="display: none;">
				<ul id="<?php echo esc_html( $tab_pop_ul_id ); ?>" class="categorychecklist form-no-clear" >
					<?php $popular_ids = wp_popular_terms_checklist($taxonomy); ?>
				</ul>
			</div>

			<div id="<?php echo esc_html( $tab_all_id ); ?>" class="tabs-panel">
				<?php
				// Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
				echo "<input type='hidden' name='" . esc_html( $input_terms_name) . "' id='" . esc_html( $input_terms_id ) . "' value='0' />";
				?>
				<ul id="<?php echo esc_html( $tab_all_ul_id ); ?>" data-wp-lists="list:<?php echo esc_html( $prefix . '-' . $taxonomy )?>" class="categorychecklist form-no-clear">
					<?php if ( $tax->hierarchical ): ?>
					<?php wp_terms_checklist($post->ID, array( 'taxonomy' => $taxonomy, 'popular_cats' => $popular_ids, 'checked_ontop'=> MLACore::mla_taxonomy_support( $taxonomy, 'checked-on-top' ) ) ) ?>
					<?php else: ?>
                    <?php $checklist_walker = new MLA_Checklist_Walker; ?>
					<?php wp_terms_checklist($post->ID, array( 'taxonomy' => $taxonomy, 'popular_cats' => $popular_ids, 'checked_ontop'=> MLACore::mla_taxonomy_support( $taxonomy, 'checked-on-top' ), 'walker' => $checklist_walker ) ) ?>
					<?php endif; ?>
				</ul>
			</div>
		<?php if ( current_user_can($tax->cap->edit_terms) ) : ?>
				<div id="<?php echo esc_html( $div_adder_id ); ?>" class="<?php echo esc_html( $div_adder_class ); ?>">
					<h4>
						<a id="<?php echo esc_html( $link_adder_id ); ?>" href="#<?php echo esc_html( $link_adder_p_id ); ?>" class="hide-if-no-js">
							<?php
								/* translators: %s: add new taxonomy label */
								echo esc_html( sprintf( __( '+ %s', 'media-library-assistant' ), $tax->labels->add_new_item ) );
							?>
						</a>
						&nbsp;&nbsp;
						<a id="<?php echo esc_html( $link_search_id ); ?>" href="#<?php echo esc_html( $link_search_p_id ); ?>" class="hide-if-no-js">
							<?php
								echo '?&nbsp;' . esc_html( __( 'Search', 'media-library-assistant' ) );
							?>
						</a>
					</h4>
					<p id="<?php echo esc_html( $link_adder_p_id ); ?>" class="category-add wp-hidden-child">
						<label class="screen-reader-text" for="<?php echo esc_html( $input_new_name ); ?>"><?php echo esc_html( $tax->labels->add_new_item ); ?></label>
						<input type="text" name="<?php echo esc_html( $input_new_name ); ?>" id="<?php echo esc_html( $input_new_id ); ?>" class="form-required form-input-tip" value="<?php echo esc_attr( esc_html( $tax->labels->new_item_name ) ); ?>" aria-required="true"/>

						<?php if ( $tax->hierarchical ): ?>
						<label class="screen-reader-text" for="<?php echo esc_html( $input_new_parent_name ); ?>">
							<?php echo esc_html( $tax->labels->parent_item_colon ); ?>
						</label>
						<?php wp_dropdown_categories( array( 'taxonomy' => $taxonomy, 'hide_empty' => 0, 'name' => $input_new_parent_name, 'orderby' => 'name', 'hierarchical' => 1, 'show_option_none' => '&mdash; ' . $tax->labels->parent_item . ' &mdash;' ) ); ?>
						<?php else:
						echo "<input type='hidden' name='" . esc_html( $input_new_parent_name ) . "' id='" . esc_html( $input_new_parent_name ) . "' value='-1' />";	
						endif; ?>
						<input type="button" id="<?php echo esc_html( $input_new_submit_id ); ?>" data-wp-lists="add:<?php echo esc_html( $tab_all_ul_id ); ?>:<?php echo esc_html( $link_adder_p_id ); ?>" class="button category-add-submit mla-taxonomy-add-submit" value="<?php echo esc_html( esc_attr( $tax->labels->add_new_item ) ); ?>" />
						<?php wp_nonce_field( 'add-'.$taxonomy, '_ajax_nonce-add-'.$taxonomy, false ); ?>
						<span id="<?php echo esc_html( $span_new_ajax_id ); ?>"></span>
					</p>
				</div>
				<div id="<?php echo esc_html( $div_search_id ); ?>" class="<?php echo esc_html( $div_search_class ); ?>">
					<p id="<?php echo esc_html( $link_search_p_id ); ?>" class="category-add wp-hidden-child">
						<label class="screen-reader-text" for="<?php echo esc_html( $input_search_name ); ?>"><?php echo esc_html( $tax->labels->search_items ); ?></label>
						<input type="text" name="<?php echo esc_html( $input_search_name ); ?>" id="<?php echo esc_html( $input_search_id ); ?>" class="form-required form-input-tip" value="<?php echo esc_html( esc_attr( $tax->labels->search_items ) ); ?>" aria-required="true"/>
						<?php wp_nonce_field( 'search-'.$taxonomy, '_ajax_nonce-search-'.$taxonomy, false ); ?>
						<span id="<?php echo esc_html( $span_search_ajax_id ); ?>"></span>
					</p>
				</div>
			<?php endif; ?>
		</div>
		<?php
	} // mla_checklist_meta_box

	/**
	 * Decode the list of functions hooking an action/filter
	 * 
	 * @since 2.74
	 * 
	 * @param	string	$filter The name of the action/filter to be decoded
	 *
	 * @return	array	List of functions hooking the action/filter
	 */
	public static function mla_decode_wp_filter( $filter ) {
		global $wp_filter;
//error_log( __LINE__ . " mla_decode_wp_filter( $filter ) wp_filter = " . var_export( $wp_filter[ $filter ], true ), 0 );

		$hook_array = array();
		if ( isset( $wp_filter[ $filter ] ) ) {
			// WordPress 4.7+ uses WP_Hook, earlier versions use an array
			if ( is_object( $wp_filter[ $filter ] ) ) {
				$callback_array = $wp_filter[ $filter ]->callbacks;
			} else {
				$callback_array = $wp_filter[ $filter ];
			}

//error_log( __LINE__ . " mla_decode_wp_filter( $filter ) callback_array = " . var_export( $callback_array, true ), 0 );
			foreach ( $callback_array as $priority => $callbacks ) {
				foreach ( $callbacks as $tag => $reference ) {
					if ( is_string( $reference['function'] ) ) {
						$hook_array[ $priority ][ $tag ] = array( 'class' => '', 'function' => $reference['function'] );
					} elseif ( is_array( $reference['function'] ) ) {
						if ( is_object( $reference['function'][0] ) ) {
							$class = get_class( $reference['function'][0] ) . '->';
						} else {
							$class = $reference['function'][0] . '::';
						}

						$hook_array[ $priority ][ $tag ] = array( 'class' => $class, 'function' => $reference['function'][1] );
					} else {
						$hook_array[ $priority ][ $tag ] = array( 'class' => '', 'function' => 'unknown reference type: ' . gettype( $reference['function'] ) );
					}
				} // foreach tag
			} // foreach proprity
		} // filters exist

//error_log( __LINE__ . " mla_decode_wp_filter( $filter ) hook_array = " . var_export( $hook_array, true ), 0 );
		return $hook_array;
	}

	/**
	 * Display the list of functions hooking an action/filter
	 * 
	 * @since 2.74
	 * 
	 * @param	string	$filter The name of the action/filter to be decoded
	 *
	 * @return	string	List of functions hooking the action/filter
	 */
	public static function mla_display_wp_filter( $filter ) {
		$hook_array = self::mla_decode_wp_filter( $filter );
		$hook_list = '';
		if ( isset( $wp_filter[ $filter ] ) ) {
			foreach ( $hook_array as $priority => $callbacks ) {
				$hook_list .= "Priority: {$priority}\n";
				foreach ( $callbacks as $tag => $reference ) {
					$hook_list .= "{$tag} => " . $reference['class'] . $reference['function'];
				} // foreach tag
			} // foreach proprity
		} // filters exist

		return $hook_list;
	}

	/**
	 * Effective MLA Debug Level, from MLA_DEBUG_LEVEL or override option
	 *
	 * @since 2.15
	 *
	 * @var	integer
	 */
	public static $mla_debug_level = 0;

	/**
	 * Accumulates debug messages
	 *
	 * @since 2.12
	 *
	 * @var	string
	 */
	private static $mla_debug_messages = array();

	/**
	 * Debug information collection mode
	 *
	 * Collection mode: 'buffer', 'console', 'log' or 'none' (default).
	 *
	 * @since 2.12
	 *
	 * @var	string
	 */
	private static $mla_debug_mode = 'none';

	/**
	 * Debug information output file for mode = 'log'
	 *
	 * @since 2.14
	 *
	 * @var	string
	 */
	private static $mla_debug_file = NULL;

	/**
	 * Get/Set debug information collection mode
	 * 
	 * @since 2.12
	 * 
	 * @param	string	$mode Optional. New collection mode: 'none' (default), 'buffer', 'console' or 'log'
	 *
	 * @return	string	The previous mode value, i.e., before the update
	 */
	public static function mla_debug_mode( $mode = false ) {
		$old_mode = self::$mla_debug_mode;

		if ( $mode && in_array( $mode, array( 'none', 'buffer', 'console', 'log' ) ) ) {
			self::$mla_debug_mode = $mode;
		}

		return $old_mode;
	}

	/**
	 * Get/Set debug information collection output file for mode = 'log'
	 * 
	 * Note that WP_CONTENT_DIR will be pre-pended to the value, and a slash
	 * will be added to the front of the value if necessary.
	 *
	 * @since 2.14
	 * 
	 * @param	string	$file Optional. The (optional path and) file name, relative to WP_CONTENT_DIR,
	 * 					or false/empty string to clear the value.
	 *
	 * @return	string	The previous file value, i.e., before the update, relative to WP_CONTENT_DIR
	 */
	public static function mla_debug_file( $file = NULL ) {
		if ( NULL === $file ) {
			return self::$mla_debug_file;
		}

		$old_file = self::$mla_debug_file;

		if ( empty( $file ) ) {
			self::$mla_debug_file = NULL;
		} else {
			$first = substr( $file, 0, 1 );
			if ( ( '/' != $first ) && ( '\\' != $first ) ) {
				$file = '/' . $file;
			}

			self::$mla_debug_file = $file;
		}

		return $old_file;
	}

	/**
	 * Get debug information without clearing the buffer
	 * 
	 * @since 2.12
	 * 
	 * @param	string	$format Return data type: 'string' (default) or 'array'
	 * @param	string	$glue Join array elements with '\n' or '<p>' (default)
	 *
	 * @return	boolean	true if success else false
	 */
	public static function mla_debug_content( $format = 'string', $glue = '<p>' ) {
		if ( 'array' == $format ) {
			return self::$mla_debug_messages;
		}

		// format == 'string'
		if ( '<p>' == $glue ) {
			return '<p>' . implode( '</p><p>', self::$mla_debug_messages ) . '</p>';
		}

		return implode( "\n", self::$mla_debug_messages ) . "\n";
	}

	/**
	 * Flush debug information and clear buffer
	 * 
	 * @since 2.12
	 * 
	 * @param	string	$destination Destination: 'buffer' (default), 'console', 'log' or 'none'
	 * @param	boolean	$stop_collecting true (default) to stop, false to continue collection
	 *
	 * @return	string	debug content if $destination == 'buffer' else empty string
	 */
	public static function mla_debug_flush( $destination = 'buffer', $stop_collecting = true ) {
		$results = '';

		switch ( $destination ) {
			case 'buffer':
				$results = MLACore::mla_debug_content();
				break;
			case 'console':
				foreach( self::$mla_debug_messages as $message ) {
					trigger_error( esc_html( $message ), E_USER_WARNING );
				}
				break;
			case 'log':
				foreach( self::$mla_debug_messages as $message ) {
					self::_debug_log( $message );
				}
				break;
		}

		self::$mla_debug_messages = array();

		if ( $stop_collecting ) {
			self::$mla_debug_mode = 'none';
		}

		return $results;
	}

	/**
	 * Write a debug message to the appropriate log file
	 * 
	 * @since 2.14
	 * 
	 * @param	string	$message Message text
	 */
	private static function _debug_log( $message ) {
		if ( ! empty( self::$mla_debug_file ) ) {
			// 'at' = append mode, text format
			$file_handle = @fopen( WP_CONTENT_DIR . self::$mla_debug_file, 'at' );
			if ( $file_handle ) {
				@fwrite( $file_handle, sprintf( '[%1$s] %2$s%3$s', gmdate( 'd-M-Y H:i:s' ), $message, "\n" ) ); 
				@fclose( $file_handle );

				return;
			}
		}

		error_log( $message, 0 );
	}

	/**
	 * Add a debug message to the collection
	 * 
	 * @since 2.12
	 * 
	 * @param	string	$message Message text
	 * @param	integer	$debug_level Optional. Debug category.
	 */
	public static function mla_debug_add( $message, $debug_level = NULL ) {
		$mode = self::$mla_debug_mode;

		if ( NULL != $debug_level ) {
			if ( ( 0 == ( MLACore::$mla_debug_level & 1 ) ) || ( 0 == ( MLACore::$mla_debug_level & $debug_level ) ) ) {
				return;
			}

			if ( 'none' == self::$mla_debug_mode ) {
				$mode = 'log';
			}
		}

		switch ( $mode ) {
			case 'buffer':
				self::$mla_debug_messages[] = $message;
				break;
			case 'console':
				trigger_error( esc_html( $message ), E_USER_WARNING );
				break;
			case 'log':
				self::_debug_log( $message );
				break;
		}
	}

	/**
	 * Admin Columns support storage model object for the Media/Assistant submenu
	 *
	 * @since 2.22
	 *
	 * @var	object
	 */
	public static $admin_columns_storage_model = NULL;

	/**
	 * Define the Media/Assistant submenu screen to the (old) Admin Columns plugin
	 * Supports Admin Columns before 3.0 and Admin Columns Pro before 4.0
	 *
	 * @since 2.22
	 *
	 * @param	array	$storage_models List of storage model class instances ( [key] => [CPAC_Storage_Model object] )
	 * @param	object	$cpac CPAC, the root CodePress Admin Columns object
	 */
	public static function admin_columns_support_deprecated( $storage_models, $cpac ) {
		require_once( MLA_PLUGIN_PATH . 'includes/class-mla-admin-columns-support-deprecated.php' );
		MLACore::$admin_columns_storage_model = new CPAC_Deprecated_Storage_Model_MLA();

		// Put MLA before/after WP Media Library so is_columns_screen() will work
		$new_models = array();
		foreach ( $storage_models as $key => $model ) {
			if ( 'wp-media' == $key ) {
				if ( version_compare( CPAC_VERSION, '2.4.9', '>=' ) ) {
					$new_models[ $key ] = $model;
					$new_models[  MLACore::$admin_columns_storage_model->key ] = MLACore::$admin_columns_storage_model;
				} else {
					$new_models[  MLACore::$admin_columns_storage_model->key ] = MLACore::$admin_columns_storage_model;
					$new_models[ $key ] = $model;
				}
			} else {
				$new_models[ $key ] = $model;
			}
		}

		// If we didn't find wp-media, add our entry to the end
		if ( count( $storage_models ) == count( $new_models ) ) {
			$new_models[ $storage_model->key ] = MLACore::$admin_columns_storage_model;
		}

		return $new_models;
	}

	/**
	 * Create and register MLA-specific list screen handler for Admin Columns
	 * Supports Admin Columns 3.0+ and Admin Columns Pro 4.0+
	 *
	 * @since 2.50
	 */
	public static function register_list_screen() {
		require_once( MLA_PLUGIN_PATH . 'includes/class-mla-admin-columns-support.php' );

		if ( function_exists( 'ACP' ) ) {
			$legacy_version = version_compare( ACP()->get_version(), '5.0.0', '<' );

			if ( version_compare( ACP()->get_version(), '6.0', '>=' ) ) {
				// Load the latest version, with PHP 7.2 changes
				require_once( MLA_PLUGIN_PATH . 'includes/class-mla-admin-columns-pro-support.php' );
			} elseif ( version_compare( ACP()->get_version(), '4.5', '>=' ) ) {
				// Load the interim version, with bulk edit support
				require_once( MLA_PLUGIN_PATH . 'includes/class-mla-admin-columns-pro-support-45.php' );
			} elseif ( version_compare( ACP()->get_version(), '4.3', '>=' ) ) {
				// Load the interim version, with namespace support, without bulk edit support
				require_once( MLA_PLUGIN_PATH . 'includes/class-mla-admin-columns-pro-support-44.php' );
			} elseif ( version_compare( ACP()->get_version(), '4.2.3', '>=' ) ) {
				// Load the interim version, with export support
				require_once( MLA_PLUGIN_PATH . 'includes/class-mla-admin-columns-pro-support-423.php' );
			} elseif ( version_compare( ACP()->get_version(), '4.2.0', '>=' ) ) {
				// Load the interim version, with inline editing support
				require_once( MLA_PLUGIN_PATH . 'includes/class-mla-admin-columns-pro-support-42.php' );
			} else {
				// Load the legacy version
				require_once( MLA_PLUGIN_PATH . 'includes/class-mla-admin-columns-pro-support-40.php' );
			}

			if ( $legacy_version ) {
				AC()->register_list_screen( new ACP_Addon_MLA_ListScreen );
			} else {
				AC\ListScreenTypes::instance()->register_list_screen( new ACP_Addon_MLA_ListScreen );
			}
		} else {
			$legacy_version = version_compare( AC()->get_version(), '4.1.0', '<' );

			if ( $legacy_version ) {
				AC()->register_list_screen( new AC_Addon_MLA_ListScreen );
			} else {
				AC\ListScreenTypes::instance()->register_list_screen( new AC_Addon_MLA_ListScreen );
			}
		}
	}
} // Class MLACore

/*
 * Option definitions and default values.
 */
require_once( MLA_PLUGIN_PATH . 'includes/class-mla-core-options.php' );

/**
 * Class MLA (Media Library Assistant) Checklist Walker replaces term_id with slug in checklist output
 *
 * This walker is used to build the meta boxes for flat taxonomies, e.g., Tags, Att. Tags.
 * Class Walker_Category is defined in /wp-includes/category-template.php.
 * Class Walker is defined in /wp-includes/class-wp-walker.php.
 *
 * @package Media Library Assistant
 * @since 1.80
 */
class MLA_Checklist_Walker extends Walker_Category {
	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 1.80
	 *
	 * @param string Passed by reference. Used to append additional content.
	 * @param object Taxonomy data object.
	 * @param int    Depth of category in reference to parents. Default 0.
	 * @param array  An array of arguments. @see wp_list_categories()
	 * @param int    ID of the current category.
	 */
	function start_el( &$output, $taxonomy_object, $depth = 0, $args = array(), $id = 0 ) {
		extract($args);

		if ( empty( $taxonomy ) ) {
			$taxonomy = 'category';
		}

		if ( 'category' == $taxonomy ) {
			$name = 'post_category';
		} else {
			$name = 'tax_input['.$taxonomy.']';
		}

		$class = in_array( $taxonomy_object->term_id, $popular_cats ) ? ' class="popular-category"' : '';
        
		// For flat taxonomies, <input> value is $taxonomy_object->name instead of $taxonomy_object->term_id
		$output .= "\n<li id='{$taxonomy}-{$taxonomy_object->term_id}'$class>" . '<label class="selectit MLA"><input value="' . esc_attr( $taxonomy_object->name ) . '" type="checkbox" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $taxonomy_object->term_id . '"' . checked( in_array( $taxonomy_object->term_id, $selected_cats ), true, false ) . disabled( empty( $args['disabled'] ), false, false ) . ' /> ' . esc_html( apply_filters('the_category', $taxonomy_object->name )) . '</label>';
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @see Walker::end_el()
	 *
	 * @since 1.80
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category The current term object.
	 * @param int    $depth    Depth of the term in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_terms_checklist()
	 */
	function end_el( &$output, $category, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}// Class MLA_Checklist_Walker

// Custom Taxonomies and WordPress objects.
require_once( MLA_PLUGIN_PATH . 'includes/class-mla-objects.php' );
add_action( 'init', 'MLAObjects::mla_build_taxonomies', 5 );
add_action( 'init', 'MLAObjects::initialize', 0x7FFFFFFF );

// MIME Type functions; some filters required in all modes.
require_once( MLA_PLUGIN_PATH . 'includes/class-mla-mime-types.php' );
add_action( 'init', 'MLAMime::initialize', 0x7FFFFFFF );

// Intermediate image sizes functions; some filters required in all modes.
require_once( MLA_PLUGIN_PATH . 'includes/class-mla-image-sizes.php' );
add_action( 'init', 'MLAImage_Size::initialize', 0x7FFFFFFF );

// Admin Columns plugin support
add_filter( 'cac/storage_models', 'MLACore::admin_columns_support_deprecated', 10, 2 );
add_action( 'ac/list_screens', 'MLACore::register_list_screen', 10, 0 );
?>