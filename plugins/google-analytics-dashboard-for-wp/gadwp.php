<?php

/**
 * Plugin Name: Google Analytics Dashboard for WP (GADWP)
 * Plugin URI: https://exactmetrics.com
 * Description: Displays Google Analytics Reports and Real-Time Statistics in your Dashboard. Automatically inserts the tracking code in every page of your website.
 * Author: ExactMetrics
 * Version: 8.7.1
 * Requires at least: 5.6.0
 * Requires PHP: 7.2
 * Author URI: https://exactmetrics.com/lite/?utm_source=liteplugin&utm_medium=pluginheader&utm_campaign=authoruri&utm_content=7%2E0%2E0
 * Text Domain: google-analytics-dashboard-for-wp
 * Domain Path: /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 *
 * @since 6.0.0
 *
 * @package ExactMetrics
 * @author  Chris Christoff
 * @access public
 */
final class ExactMetrics_Lite {


	/**
	 * Holds the class object.
	 *
	 * @since 6.0.0
	 * @access public
	 * @var object Instance of instantiated ExactMetrics class.
	 */
	public static $instance;

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since 6.0.0
	 * @access public
	 * @var string $version Plugin version.
	 */

	public $version = '8.7.1';

	/**
	 * Plugin file.
	 *
	 * @since 6.0.0
	 * @access public
	 * @var string $file PHP File constant for main file.
	 */
	public $file;

	/**
	 * The name of the plugin.
	 *
	 * @since 6.0.0
	 * @access public
	 * @var string $plugin_name Plugin name.
	 */
	public $plugin_name = 'ExactMetrics Lite';

	/**
	 * Unique plugin slug identifier.
	 *
	 * @since 6.0.0
	 * @access public
	 * @var string $plugin_slug Plugin slug.
	 */
	public $plugin_slug = 'exactmetrics-lite';

	/**
	 * Holds instance of ExactMetrics License class.
	 *
	 * @since 6.0.0
	 * @access public
	 * @var ExactMetrics_License $license Instance of License class.
	 */
	protected $license;

	/**
	 * Holds instance of ExactMetrics Admin Notice class.
	 *
	 * @since 6.0.0
	 * @access public
	 * @var ExactMetrics_Admin_Notice $notices Instance of Admin Notice class.
	 */
	public $notices;

	/**
	 * Holds instance of ExactMetrics Notifications class.
	 *
	 * @since 6.1
	 * @access public
	 * @var ExactMetrics_Notifications $notifications Instance of Notifications class.
	 */
	public $notifications;

	/**
	 * Holds instance of ExactMetrics Notification Events
	 *
	 * @since 6.2.3
	 * @access public
	 * @var ExactMetrics_Notification_Event $notification_event Instance of ExactMetrics_Notification_Event class.
	 */
	public $notification_event;

	/**
	 * Holds instance of ExactMetrics Reporting class.
	 *
	 * @since 6.0.0
	 * @access public
	 * @var ExactMetrics_Reporting $reporting Instance of Reporting class.
	 */
	public $reporting;

	/**
	 * Holds instance of ExactMetrics Auth class.
	 *
	 * @since 6.0.0
	 * @access public
	 * @var ExactMetrics_Auth $auth Instance of Auth class.
	 */
	protected $auth;

	/**
	 * Holds instance of ExactMetrics API Auth class.
	 *
	 * @since 6.0.0
	 * @access public
	 * @var ExactMetrics_Auth $api_auth Instance of APIAuth class.
	 */
	public $api_auth;

	/**
	 * Holds instance of ExactMetrics API Rest Routes class.
	 *
	 * @since 6.0.0
	 * @access public
	 * @var ExactMetrics_Rest_Routes $routes Instance of rest routes.
	 */
	public $routes;

	/**
	 * The tracking mode used in the frontend.
	 *
	 * @since 7.15.0
	 * @accces public
	 * @var string
	 * @deprecated Since 8.3 with the removal of ga compatibility
	 */
	public $tracking_mode;

	/**
	 * Setup checklist class property.
	 *
	 * @var ExactMetrics_Setup_Checklist
	 */
	public $setup_checklist;

	/**
	 * Primary class constructor.
	 *
	 * @since 6.0.0
	 * @access public
	 */
	public function __construct() {
		// We don't use this
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @access public
	 * @return object The ExactMetrics_Lite object.
	 * @since 6.0.0
	 *
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ExactMetrics_Lite ) ) {
			self::$instance       = new ExactMetrics_Lite();
			self::$instance->file = __FILE__;

			// Detect Pro version and return early
			if ( defined( 'EXACTMETRICS_PRO_VERSION' ) ) {
				add_action( 'admin_notices', array( self::$instance, 'exactmetrics_pro_notice' ) );

				return self::$instance;
			}

			// Define constants
			self::$instance->define_globals();

			// Load in settings
			self::$instance->load_settings();

			// Compatibility check
			if ( ! self::$instance->check_compatibility() ) {
				return self::$instance;
			}

			// Load in Licensing
			self::$instance->load_licensing();

			// Load in Auth
			self::$instance->load_auth();

			// Load files
			self::$instance->require_files();

			// This does the version to version background upgrade routines and initial install
			$em_version = get_option( 'exactmetrics_current_version', '5.5.3' );
			if ( version_compare( $em_version, '7.13.1', '<' ) ) {
				exactmetrics_lite_call_install_and_upgrade();
			}

			// Load admin only components.
			if ( is_admin() || ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
				self::$instance->notices            = new ExactMetrics_Notice_Admin();
				self::$instance->reporting          = new ExactMetrics_Reporting();
				self::$instance->api_auth           = new ExactMetrics_API_Auth();
				self::$instance->routes             = new ExactMetrics_Rest_Routes();
				self::$instance->notifications      = new ExactMetrics_Notifications();
				self::$instance->notification_event = new ExactMetrics_Notification_Event();
				self::$instance->setup_checklist    = new ExactMetrics_Setup_Checklist();
			}

			if ( exactmetrics_is_pro_version() ) {
				require_once EXACTMETRICS_PLUGIN_DIR . 'pro/includes/load.php';
			} else {
				require_once EXACTMETRICS_PLUGIN_DIR . 'lite/includes/load.php';
			}
		}
		return self::$instance;
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @return void
	 * @since 6.0.0
	 * @access public
	 *
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'google-analytics-dashboard-for-wp' ), '6.0.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * Attempting to wakeup an ExactMetrics instance will throw a doing it wrong notice.
	 *
	 * @return void
	 * @since 6.0.0
	 * @access public
	 *
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'google-analytics-dashboard-for-wp' ), '6.0.0' );
	}

	/**
	 * Magic get function.
	 *
	 * We use this to lazy load certain functionality. Right now used to lazyload
	 * the API & Auth frontend, so it's only loaded if user is using a plugin
	 * that requires it.
	 *
	 * @return void
	 * @since 6.0.0
	 * @access public
	 *
	 */
	public function __get( $key ) {
		if ( $key === 'auth' ) {
			if ( empty( self::$instance->auth ) ) {
				// LazyLoad Auth for Frontend
				require_once EXACTMETRICS_PLUGIN_DIR . 'includes/auth.php';
				self::$instance->auth = new ExactMetrics_Auth();
			}

			return self::$instance->$key;
		} else {
			return self::$instance->$key;
		}
	}

	/**
	 * Check compatibility with PHP and WP, and display notices if necessary
	 *
	 * @return bool
	 * @since 7.0.0
	 */
	private function check_compatibility() {
		if ( defined( 'EXACTMETRICS_FORCE_ACTIVATION' ) && EXACTMETRICS_FORCE_ACTIVATION ) {
			return true;
		}

		require_once plugin_dir_path( __FILE__ ) . 'includes/compatibility-check.php';
		$compatibility = ExactMetrics_Compatibility_Check::get_instance();
		$compatibility->maybe_display_notice();

		return $compatibility->is_php_compatible() && $compatibility->is_wp_compatible();
	}

	/**
	 * Define ExactMetrics constants.
	 *
	 * This function defines all of the ExactMetrics PHP constants.
	 *
	 * @return void
	 * @since 6.0.0
	 * @access public
	 *
	 */
	public function define_globals() {

		if ( ! defined( 'EXACTMETRICS_VERSION' ) ) {
			define( 'EXACTMETRICS_VERSION', $this->version );
		}

		if ( ! defined( 'EXACTMETRICS_VERSION' ) ) {
			define( 'EXACTMETRICS_VERSION', $this->version );
		}

		if ( ! defined( 'EXACTMETRICS_LITE_VERSION' ) ) {
			define( 'EXACTMETRICS_LITE_VERSION', EXACTMETRICS_VERSION );
		}

		if ( ! defined( 'EXACTMETRICS_PLUGIN_NAME' ) ) {
			define( 'EXACTMETRICS_PLUGIN_NAME', $this->plugin_name );
		}

		if ( ! defined( 'EXACTMETRICS_PLUGIN_SLUG' ) ) {
			define( 'EXACTMETRICS_PLUGIN_SLUG', $this->plugin_slug );
		}

		if ( ! defined( 'EXACTMETRICS_PLUGIN_FILE' ) ) {
			define( 'EXACTMETRICS_PLUGIN_FILE', $this->file );
		}

		if ( ! defined( 'EXACTMETRICS_PLUGIN_DIR' ) ) {
			define( 'EXACTMETRICS_PLUGIN_DIR', plugin_dir_path( $this->file ) );
		}

		if ( ! defined( 'EXACTMETRICS_PLUGIN_URL' ) ) {
			define( 'EXACTMETRICS_PLUGIN_URL', plugin_dir_url( $this->file ) );
		}
	}

	/**
	 * Output a nag notice if the user has both Lite and Pro activated
	 *
	 * @access public
	 * @return    void
	 * @since 6.0.0
	 *
	 */
	public function exactmetrics_pro_notice() {
		$url = admin_url( 'plugins.php' );
		// Check for MS dashboard
		if ( is_network_admin() ) {
			$url = network_admin_url( 'plugins.php' );
		}
		?>
		<div class="error">
			<p>
				<?php
				// Translators: Adds a link to the plugins page.
				echo sprintf(esc_html__('Please %1$suninstall%2$s the ExactMetrics Lite Plugin. Your Pro version of ExactMetrics may not work as expected until the Lite version is uninstalled.', 'google-analytics-dashboard-for-wp'), '<a href="' . $url . '">', '</a>'); // phpcs:ignore
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Loads ExactMetrics settings
	 *
	 * Adds the items to the base object, and adds the helper functions.
	 *
	 * @return void
	 * @since 6.0.0
	 * @access public
	 *
	 */
	public function load_settings() {
		global $exactmetrics_settings;
		require_once EXACTMETRICS_PLUGIN_DIR . 'includes/options.php';
		require_once EXACTMETRICS_PLUGIN_DIR . 'includes/helpers.php';
		require_once EXACTMETRICS_PLUGIN_DIR . 'includes/deprecated.php';
		$exactmetrics_settings = exactmetrics_get_options();
	}


	/**
	 * Loads ExactMetrics License
	 *
	 * Loads license class used by ExactMetrics
	 *
	 * @return void
	 * @since 6.0.0
	 * @access public
	 *
	 */
	public function load_licensing() {
		if ( is_admin() || ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
			require_once EXACTMETRICS_PLUGIN_DIR . 'lite/includes/license-compat.php';
			self::$instance->license = new ExactMetrics_License_Compat();
		}
	}

	/**
	 * Loads ExactMetrics Auth
	 *
	 * Loads auth used by ExactMetrics
	 *
	 * @return void
	 * @since 6.0.0
	 * @access public
	 *
	 */
	public function load_auth() {
		if ( is_admin() || ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/auth.php';
			self::$instance->auth = new ExactMetrics_Auth();
		}
	}

	/**
	 * Loads all files into scope.
	 *
	 * @access public
	 * @return    void
	 * @since 6.0.0
	 *
	 */
	public function require_files() {

		require_once EXACTMETRICS_PLUGIN_DIR . 'includes/capabilities.php';

		if ( is_admin() || ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {

			// Lite and Pro files
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/ajax.php';
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/admin.php';
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/em-admin.php';
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/common.php';
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/notice.php';
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/licensing/autoupdate.php';
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/review.php';
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/setup-checklist.php';

			// Pages
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/pages/settings.php';
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/pages/tools.php';
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/pages/reports.php';
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/pages/addons.php';

			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/api-auth.php';

			// Reports
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/reports/abstract-report.php';
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/reports/overview.php';
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/reports/site-summary.php';

			// Reporting Functionality
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/reporting.php';

			// Routes used by Vue
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/routes.php';

			// Load gutenberg editor functions
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/gutenberg/gutenberg.php';

			// Emails
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/emails/class-emails.php';

			// Notifications class.
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/notifications.php';
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/notification-event.php';
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/notification-event-runner.php';
			// Add notification manual events for lite version.
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/notifications/notification-events.php';
		}

		require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/exclude-page-metabox.php';
		require_once EXACTMETRICS_PLUGIN_DIR . 'includes/frontend/verified-badge/Controller.php';
		require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/site-notes/Controller.php';
		require_once EXACTMETRICS_PLUGIN_DIR . 'includes/api-request.php';

		if ( is_admin() || ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
			// Late loading classes (self instantiating)
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/tracking.php';
		}

		if (is_admin()) {
			require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/class-exactmetrics-am-deactivation-survey.php';
			add_action('admin_menu', function () {

				new \ExactMetrics_AM_Deactivation_Survey(
					apply_filters(
						'exactmetrics_deactivation_survey_url',
						'https://exactmetrics.com/wp-json/am-deactivate-survey/v1/deactivation-data'
					),
					'ExactMetrics Lite',
					'google-analytics-dashboard-for-wp'
				);
			}, 100);
		}

		require_once EXACTMETRICS_PLUGIN_DIR . 'includes/frontend/frontend.php';
		require_once EXACTMETRICS_PLUGIN_DIR . 'includes/frontend/seedprod.php';
		require_once EXACTMETRICS_PLUGIN_DIR . 'includes/measurement-protocol-v4.php';
		require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/feature-feedback/class-monsterInsights-feature-feedback.php';
		require_once EXACTMETRICS_PLUGIN_DIR . 'includes/admin/class-exactmetrics-onboarding.php';
	}

	/**
	 * Get the tracking mode for the frontend scripts.
	 *
	 * @return string
	 * @deprecated Since 8.3 with the removal of ga compatibility
	 */
	public function get_tracking_mode() {

		if ( ! isset( $this->tracking_mode ) ) {
			// This will already be set to 'analytics' to anybody already using the plugin before 7.15.0.
			$this->tracking_mode = exactmetrics_get_option( 'tracking_mode', 'gtag' );
		}

		return $this->tracking_mode;
	}
}

/**
 * Fired when the plugin is activated.
 *
 * @access public
 *
 * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false otherwise.
 *
 * @return void
 * @global object $wpdb The WordPress database object.
 * @since 6.0.0
 *
 * @global int $wp_version The version of WordPress for this install.
 */
function exactmetrics_lite_activation_hook( $network_wide ) {
	$url = admin_url( 'plugins.php' );
	// Check for MS dashboard
	if ( is_network_admin() ) {
		$url = network_admin_url( 'plugins.php' );
	}

	if ( class_exists( 'ExactMetrics' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die(sprintf(esc_html__('Please uninstall and remove ExactMetrics Pro before activating Google Analytics Dashboard for WP (GADWP). The Lite version has not been activated. %1$sClick here to return to the Dashboard%2$s.', 'google-analytics-by-wordpress'), '<a href="' . $url . '">', '</a>')); // phpcs:ignore
	}

	require_once plugin_dir_path( __FILE__ ) . 'includes/compatibility-check.php';
	$compatibility = ExactMetrics_Compatibility_Check::get_instance();
	$compatibility->maybe_deactivate_plugin( plugin_basename( __FILE__ ) );

	// Add transient to trigger redirect.
	set_transient( '_exactmetrics_activation_redirect', 1, 30 );
}

register_activation_hook( __FILE__, 'exactmetrics_lite_activation_hook' );

/**
 * Fired when the plugin is uninstalled.
 *
 * @access public
 * @return    void
 * @since 6.0.0
 *
 */
function exactmetrics_lite_uninstall_hook() {
	wp_cache_flush();

	// Note, if both MI Pro and Lite are active, this is an MI Pro instance
	// Therefore MI Lite can only use functions of the instance common to
	// both plugins. If it needs to be pro specific, then include a file that
	// has that method.
	$instance = ExactMetrics();

	$instance->define_globals();
	$instance->load_settings();

	// If uninstalling via wp-cli load admin-specific files only here.
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		define( 'WP_ADMIN', true );
		$instance->require_files();
		$instance->load_auth();
		$instance->notices   = new ExactMetrics_Notice_Admin();
		$instance->reporting = new ExactMetrics_Reporting();
		$instance->api_auth  = new ExactMetrics_API_Auth();
	}

	// Don't delete any data if the PRO version is already active.
	if ( exactmetrics_is_pro_version() ) {
		return;
	}

	if ( is_multisite() ) {
		$site_list = get_sites();
		foreach ( (array) $site_list as $site ) {
			switch_to_blog( $site->blog_id );

			// Delete auth
			$instance->api_auth->delete_auth();

			// Delete data
			$instance->reporting->delete_aggregate_data( 'site' );

			restore_current_blog();
		}
		// Delete network auth using a custom function as some variables are not initiated.
		$instance->api_auth->uninstall_network_auth();

		// Delete network data
		$instance->reporting->delete_aggregate_data( 'network' );
	} else {
		// Delete auth
		$instance->api_auth->delete_auth();

		// Delete data
		$instance->reporting->delete_aggregate_data( 'site' );
	}

	// Clear notification cron schedules
	$schedules = wp_get_schedules();

	if ( is_array( $schedules ) && ! empty( $schedules ) ) {
		foreach ( $schedules as $key => $value ) {
			if ( 0 === strpos( $key, 'exactmetrics_notification_' ) ) {
				$cron_hook = implode( '_', explode( '_', $key, -2 ) ) . '_cron';
				wp_clear_scheduled_hook( $cron_hook );
			}
		}
	}
}

register_uninstall_hook( __FILE__, 'exactmetrics_lite_uninstall_hook' );

/**
 * The main function responsible for returning the one true ExactMetrics_Lite
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $exactmetrics = ExactMetrics_Lite(); ?>
 *
 * @return ExactMetrics_Lite The singleton ExactMetrics_Lite instance.
 * @uses ExactMetrics_Lite::get_instance() Retrieve ExactMetrics_Lite instance.
 *
 * @since 6.0.0
 *
 */
function ExactMetrics_Lite() {
	return ExactMetrics_Lite::get_instance();
}

/**
 * ExactMetrics Install and Updates.
 *
 * This function is used install and upgrade ExactMetrics. This is used for upgrade routines
 * that can be done automatically, behind the scenes without the need for user interaction
 * (for example pagination or user input required), as well as the initial install.
 *
 * @return void
 * @global string $wp_version WordPress version (provided by WordPress core).
 * @uses ExactMetrics_Lite::load_settings() Loads ExactMetrics settings
 * @uses ExactMetrics_Install::init() Runs upgrade process
 *
 * @since 6.0.0
 * @access public
 *
 */
function exactmetrics_lite_install_and_upgrade() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/compatibility-check.php';
	$compatibility = ExactMetrics_Compatibility_Check::get_instance();

	// If the WordPress site doesn't meet the correct WP or PHP version requirements, don't activate ExactMetrics
	if ( ! $compatibility->is_php_compatible() || ! $compatibility->is_wp_compatible() ) {
		if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
			return;
		}
	}

	// Don't run if ExactMetrics Pro is installed
	if ( class_exists( 'ExactMetrics' ) ) {
		if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
			return;
		}
	}

	// Load settings and globals (so we can use/set them during the upgrade process)
	ExactMetrics_Lite()->define_globals();
	ExactMetrics_Lite()->load_settings();

	// Load in Auth
	ExactMetrics()->load_auth();

	// Load upgrade file
	require_once EXACTMETRICS_PLUGIN_DIR . 'includes/em-install.php';

	// Run the ExactMetrics upgrade routines
	$updates = new ExactMetrics_Install();
	$updates->init();
}

/**
 * ExactMetrics check for install and update processes.
 *
 * This function is used to call the ExactMetrics automatic upgrade class, which in turn
 * checks to see if there are any update procedures to be run, and if
 * so runs them. Also installs ExactMetrics for the first time.
 *
 * @return void
 * @uses ExactMetrics_Install() Runs install and upgrade process.
 *
 * @since 6.0.0
 * @access public
 *
 */
function exactmetrics_lite_call_install_and_upgrade() {
	add_action( 'wp_loaded', 'exactmetrics_lite_install_and_upgrade' );
}

/**
 * Returns the ExactMetrics combined object that you can use for both
 * ExactMetrics Lite and Pro Users. When both plugins active, defers to the
 * more complete Pro object.
 *
 * Warning: Do not use this in Lite or Pro specific code (use the individual objects instead).
 * Also do not use in the ExactMetrics Lite/Pro upgrade and install routines.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Prevents the need to do conditional global object logic when you have code that you want to work with
 * both Pro and Lite.
 *
 * Example: <?php $exactmetrics = ExactMetrics(); ?>
 *
 * @return ExactMetrics The singleton ExactMetrics instance.
 * @uses ExactMetrics::get_instance() Retrieve ExactMetrics Pro instance.
 * @uses ExactMetrics_Lite::get_instance() Retrieve ExactMetrics Lite instance.
 *
 * @since 6.0.0
 *
 */
if ( ! function_exists( 'ExactMetrics' ) ) {
	function ExactMetrics() {
		return ( class_exists( 'ExactMetrics' ) ? ExactMetrics_Pro() : ExactMetrics_Lite() );
	}

	add_action( 'plugins_loaded', 'ExactMetrics' );
}

/**
 * Remove scheduled cron hooks during deactivation.
 */
function exactmetrics_lite_deactivation_hook() {
	wp_clear_scheduled_hook( 'exactmetrics_usage_tracking_cron' );
	wp_clear_scheduled_hook( 'exactmetrics_email_summaries_cron' );
}

register_deactivation_hook( __FILE__, 'exactmetrics_lite_deactivation_hook' );
