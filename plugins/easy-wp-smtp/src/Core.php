<?php

namespace EasyWPSMTP;

use EasyWPSMTP\Admin\Area as AdminArea;
use EasyWPSMTP\Admin\DashboardWidget;
use EasyWPSMTP\Admin\DebugEvents\DebugEvents;
use EasyWPSMTP\Admin\Notifications;
use EasyWPSMTP\Compatibility\Compatibility;
use EasyWPSMTP\Migrations\Migrations;
use EasyWPSMTP\Providers\Loader as ProvidersLoader;
use EasyWPSMTP\Providers\Outlook\Provider as OutlookProvider;
use EasyWPSMTP\Queue\Queue;
use EasyWPSMTP\Reports\Reports;
use EasyWPSMTP\Tasks\Meta;
use EasyWPSMTP\Tasks\Tasks;
use EasyWPSMTP\UsageTracking\UsageTracking;
use Exception;
use ReflectionFunction;

/**
 * Class Core to handle all plugin initialization.
 *
 * @since 2.0.0
 */
class Core {

	/**
	 * URL to plugin directory.
	 *
	 * @since 2.0.0
	 *
	 * @var string Without trailing slash.
	 */
	public $plugin_url;

	/**
	 * URL to Lite plugin assets directory.
	 *
	 * @since 2.0.0
	 *
	 * @var string Without trailing slash.
	 */
	public $assets_url;

	/**
	 * Path to plugin directory.
	 *
	 * @since 2.0.0
	 *
	 * @var string Without trailing slash.
	 */
	public $plugin_path;

	/**
	 * Shortcut to get access to Pro functionality using easy_wp_smtp()->pro->example().
	 *
	 * @since 2.1.0
	 *
	 * @var \EasyWPSMTP\Pro\Pro
	 */
	public $pro;

	/**
	 * Core constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->plugin_url  = rtrim( plugin_dir_url( __DIR__ ), '/\\' );
		$this->assets_url  = $this->plugin_url . '/assets';
		$this->plugin_path = rtrim( plugin_dir_path( __DIR__ ), '/\\' );

		if ( $this->is_not_loadable() ) {
			add_action( 'admin_notices', 'easy_wp_smtp_insecure_php_version_notice' );

			if ( WP::use_global_plugin_settings() ) {
				add_action( 'network_admin_notices', 'easy_wp_smtp_insecure_php_version_notice' );
			}

			return;
		}

		// Finally, load all the plugin.
		$this->hooks();
		$this->init_early();
	}

	/**
	 * Currently used for Pro version only.
	 *
	 * @since 2.1.0
	 *
	 * @return bool
	 */
	protected function is_not_loadable() {

		// Check the Pro.
		if (
			is_readable( $this->plugin_path . '/src/Pro/Pro.php' ) &&
			! $this->is_pro_allowed()
		) {
			// So there is a Pro version, but its PHP version check failed.
			return true;
		}

		return false;
	}

	/**
	 * Assign all hooks to proper places.
	 *
	 * @since 2.0.0
	 */
	public function hooks() {

		// Activation hook.
		register_activation_hook( EasyWPSMTP_PLUGIN_FILE, [ $this, 'activate' ] );

		// Initialize DB migrations.
		add_action( 'plugins_loaded', [ $this, 'get_migrations' ] );

		// Load Pro if available.
		add_action( 'plugins_loaded', [ $this, 'get_pro' ] );

		// Redefine PHPMailer.
		add_action( 'plugins_loaded', [ $this, 'get_processor' ] );
		add_action( 'plugins_loaded', [ $this, 'replace_phpmailer' ] );

		// Various notifications.
		add_action( 'admin_init', [ $this, 'init_notifications' ] );

		add_action( 'init', [ $this, 'init' ] );

		// Initialize Action Scheduler tasks.
		add_action( 'init', [ $this, 'get_tasks' ], 5 );

		add_action( 'plugins_loaded', [ $this, 'get_usage_tracking' ] );
		add_action( 'plugins_loaded', [ $this, 'get_notifications' ] );
		add_action( 'plugins_loaded', [ $this, 'get_connect' ], 15 );
		add_action( 'plugins_loaded', [ $this, 'get_compatibility' ], 0 );
		add_action( 'plugins_loaded', [ $this, 'get_dashboard_widget' ], 20 );
		add_action( 'plugins_loaded', [ $this, 'get_reports' ] );
		add_action( 'plugins_loaded', [ $this, 'get_db_repair' ] );
		add_action( 'plugins_loaded', [ $this, 'get_connections_manager' ], 20 );
		add_action( 'plugins_loaded', [ $this, 'get_wp_mail_initiator' ] );
		add_action( 'plugins_loaded', [ $this, 'get_queue' ] );
		add_action(
			'plugins_loaded',
			function () {
				( new OptimizedEmailSending() )->hooks();
				( new OutlookProvider() )->hooks();
			}
		);
	}

	/**
	 * Initial plugin actions.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		// Load translations just in case.
		load_plugin_textdomain( 'easy-wp-smtp', false, plugin_basename( easy_wp_smtp()->plugin_path ) . '/assets/languages' );

		/*
		 * Constantly check in admin area, that we don't need to upgrade DB.
		 * Do not wait for the `admin_init` hook, because some actions are already done
		 * on `plugins_loaded`, so migration has to be done before.
		 * We should not fire this in AJAX requests.
		 */
		if ( WP::in_wp_admin() ) {
			$this->detect_conflicts();
		}

		// In admin area, regardless of AJAX or not AJAX request.
		if ( is_admin() ) {
			$this->get_admin();
			$this->get_site_health()->init();

			// Register Debug Event hooks.
			( new DebugEvents() )->hooks();
		}

		// Plugin admin area notices. Display to "admins" only.
		if ( current_user_can( easy_wp_smtp()->get_capability_manage_options() ) ) {
			add_action( 'admin_notices', [ '\EasyWPSMTP\WP', 'display_admin_notices' ] );
			add_action( 'admin_notices', [ $this, 'display_general_notices' ] );

			if ( WP::use_global_plugin_settings() ) {
				add_action( 'network_admin_notices', [ '\EasyWPSMTP\WP', 'display_admin_notices' ] );
				add_action( 'network_admin_notices', [ $this, 'display_general_notices' ] );
			}
		}
	}

	/**
	 * Whether the Pro part of the plugin is allowed to be loaded.
	 *
	 * @since 2.1.0
	 *
	 * @return bool
	 */
	protected function is_pro_allowed() {

		$is_allowed = true;

		if ( ! is_readable( $this->plugin_path . '/src/Pro/Pro.php' ) ) {
			$is_allowed = false;
		}

		return apply_filters( 'easy_wp_smtp_core_is_pro_allowed', $is_allowed );
	}

	/**
	 * Get/Load the Pro code of the plugin if it exists.
	 *
	 * @since 2.1.0
	 *
	 * @return \EasyWPSMTP\Pro\Pro
	 */
	public function get_pro() {

		if ( ! $this->is_pro_allowed() ) {
			return $this->pro;
		}

		if ( ! $this->is_pro() ) {
			$this->pro = new \EasyWPSMTP\Pro\Pro();
		}

		return $this->pro;
	}

	/**
	 * Get/Load the Tasks code of the plugin.
	 *
	 * @since 2.0.0
	 *
	 * @return Tasks
	 */
	public function get_tasks() {

		static $tasks = null;

		if ( is_null( $tasks ) ) {

			/**
			 * Filter the tasks class name.
			 *
			 * @since 2.0.0
			 *
			 * @param Tasks $tasks_class_name The tasks class name to be instantiated.
			 */
			$class_name = apply_filters( 'easy_wp_smtp_core_get_tasks', Tasks::class );
			$tasks      = new $class_name();

			if ( method_exists( $tasks, 'init' ) ) {
				$tasks->init();
			}
		}

		return $tasks;
	}

	/**
	 * Get/Load the Migrations code of the plugin.
	 *
	 * @since 2.0.0
	 *
	 * @return Migrations
	 */
	public function get_migrations() {

		static $migrations = null;

		if ( is_null( $migrations ) ) {

			/**
			 * Filter the migrations class name.
			 *
			 * @since 2.0.0
			 *
			 * @param Migrations $migrations_class_name The migrations class name to be instantiated.
			 */
			$class_name = apply_filters( 'easy_wp_smtp_core_get_migrations', Migrations::class );
			$migrations = new $class_name();

			if ( method_exists( $migrations, 'hooks' ) ) {
				$migrations->hooks();
			}
		}

		return $migrations;
	}

	/**
	 * This method allows to overwrite certain core WP functions, because it's fired:
	 *  - after `muplugins_loaded` hook,
	 *  - before WordPress own `wp-includes/pluggable.php` file include,
	 *  - before `plugin_loaded` and `plugins_loaded` hooks.
	 *
	 * @since 2.0.0
	 */
	protected function init_early() {

		// Action Scheduler requires a special early loading procedure.
		$this->load_action_scheduler();

		// Load Pro specific files early.
		$pro_files = $this->is_pro_allowed() ? \EasyWPSMTP\Pro\Pro::PLUGGABLE_FILES : [];

		$files = (array) apply_filters( 'easy_wp_smtp_core_init_early_include_files', $pro_files );

		foreach ( $files as $file ) {
			$path = $this->plugin_path . '/' . $file;

			if ( is_readable( $path ) ) {
				/** @noinspection PhpIncludeInspection */
				include_once $path;
			}
		}

		// Include deprecated classes.
		require_once $this->plugin_path . '/inc/deprecated/class-easywpsmtp.php';
	}

	/**
	 * Load the plugin core processor.
	 *
	 * @since 2.0.0
	 *
	 * @return Processor
	 */
	public function get_processor() {

		static $processor = null;

		if ( is_null( $processor ) ) {

			/**
			 * Filter the processor class name.
			 *
			 * @since 2.0.0
			 *
			 * @param Processor $processor_class_name The processor class name to be instantiated.
			 */
			$class_name = apply_filters( 'easy_wp_smtp_core_get_processor', Processor::class );
			$processor  = new $class_name();

			if ( method_exists( $processor, 'hooks' ) ) {
				$processor->hooks();
			}
		}

		return $processor;
	}

	/**
	 * Load the plugin admin area.
	 *
	 * @since 2.0.0
	 *
	 * @return AdminArea
	 */
	public function get_admin() {

		static $admin = null;

		if ( is_null( $admin ) ) {

			/**
			 * Filter the admin class name.
			 *
			 * @since 2.0.0
			 *
			 * @param AdminArea $admin_class_name The admin class name to be instantiated.
			 */
			$class_name = apply_filters( 'easy_wp_smtp_core_get_admin', AdminArea::class );
			$admin      = new $class_name();

			if ( method_exists( $admin, 'hooks' ) ) {
				$admin->hooks();
			}
		}

		return $admin;
	}

	/**
	 * Load the plugin providers loader.
	 *
	 * @since 2.0.0
	 *
	 * @return ProvidersLoader
	 */
	public function get_providers() {

		static $providers = null;

		if ( is_null( $providers ) ) {

			/**
			 * Filter the providers loader class name.
			 *
			 * @since 2.0.0
			 *
			 * @param ProvidersLoader $providers_class_name The providers loader class name to be instantiated.
			 */
			$class_name = apply_filters( 'easy_wp_smtp_core_get_providers', ProvidersLoader::class );
			$providers  = new $class_name();
		}

		return $providers;
	}

	/**
	 * Get the plugin's WP Site Health object.
	 *
	 * @since 2.1.0
	 *
	 * @return SiteHealth
	 */
	public function get_site_health() {

		static $site_health;

		if ( ! isset( $site_health ) ) {
			$site_health = apply_filters( 'easy_wp_smtp_core_get_site_health', new SiteHealth() );
		}

		return $site_health;
	}

	/**
	 * Display various notifications to a user.
	 *
	 * @since 2.0.0
	 */
	public function init_notifications() {}

	/**
	 * Display all debug mail-delivery related notices.
	 *
	 * @since 2.0.0
	 */
	public static function display_general_notices() {

		if ( easy_wp_smtp()->is_blocked() ) {
			?>

			<div class="notice easy-wp-smtp-notice <?php echo esc_attr( WP::ADMIN_NOTICE_ERROR ); ?>">
				<p>
					<?php
					$notices[] = sprintf(
					/* translators: %s - plugin name and its version. */
						__( '<strong>EMAILING DISABLED:</strong> The %s is currently blocking all emails from being sent.', 'easy-wp-smtp' ),
						esc_html( 'Easy WP SMTP v' . EasyWPSMTP_PLUGIN_VERSION )
					);

					if ( Options::init()->is_const_defined( 'general', 'do_not_send' ) ) {
						$notices[] = sprintf(
						/* translators: %1$s - constant name; %2$s - constant value. */
							__( 'To send emails, change the value of the %1$s constant to %2$s.', 'easy-wp-smtp' ),
							'<code>EASY_WP_SMTP_DO_NOT_SEND</code>',
							'<code>false</code>'
						);
					} else {
						$notices[] = sprintf(
						/* translators: %s - plugin Misc settings page URL. */
							__( 'To send emails, go to plugin <a href="%s">Misc settings</a> and disable the "Do Not Send" option.', 'easy-wp-smtp' ),
							esc_url( add_query_arg( 'tab', 'misc', easy_wp_smtp()->get_admin()->get_admin_page_url() ) )
						);
					}

					if (
						easy_wp_smtp()->get_admin()->is_admin_page( 'tools' ) &&
						(
							! isset( $_GET['tab'] ) ||
							( isset( $_GET['tab'] ) && $_GET['tab'] === 'test' )
						)
					) {
						$notices[] = esc_html__( 'If you create a test email on this page, it will still be sent.', 'easy-wp-smtp' );
					}

					echo wp_kses_post( implode( ' ', $notices ) );
					?>
				</p>
			</div>

			<?php
			return;
		}

		if ( easy_wp_smtp()->get_admin()->is_admin_page() ) {
			easy_wp_smtp()->wp_mail_function_incorrect_location_notice();
		}

		if ( easy_wp_smtp()->get_admin()->is_error_delivery_notice_enabled() ) {
			$screen = get_current_screen();

			// Skip the error notice if not on plugin page.
			if (
				is_object( $screen ) &&
				strpos( $screen->id, 'page_easy-wp-smtp' ) === false
			) {
				return;
			}

			$notice = apply_filters(
				'easy_wp_smtp_core_display_general_notices_email_delivery_error_notice',
				Debug::get_last()
			);

			if ( ! empty( $notice ) ) {
				?>

				<div class="notice easy-wp-smtp-notice <?php echo esc_attr( WP::ADMIN_NOTICE_ERROR ); ?>">
					<p>
						<?php
						echo wp_kses(
							__( '<strong>Heads up!</strong> The last email your site attempted to send was unsuccessful.', 'easy-wp-smtp' ),
							[
								'strong' => [],
							]
						);
						?>
					</p>

					<blockquote>
						<pre><?php echo wp_kses_post( $notice ); ?></pre>
					</blockquote>

					<p>
						<?php
						if ( ! easy_wp_smtp()->get_admin()->is_admin_page() ) {
							printf(
								wp_kses( /* translators: %s - plugin admin page URL. */
									__( 'Please review your Easy WP SMTP settings in <a href="%s">plugin admin area</a>.', 'easy-wp-smtp' ) . ' ',
									[
										'a' => [
											'href' => [],
										],
									]
								),
								esc_url( easy_wp_smtp()->get_admin()->get_admin_page_url() )
							);
						}

						printf(
							wp_kses( /* translators: %s - URL to the debug events page. */
								__( 'For more details please try running an Email Test or reading the latest <a href="%s">error event</a>.', 'easy-wp-smtp' ),
								[
									'a' => [
										'href' => [],
									],
								]
							),
							esc_url( DebugEvents::get_page_url() )
						);
						?>
					</p>

					<?php
					echo wp_kses(
						apply_filters(
							'easy_wp_smtp_core_display_general_notices_email_delivery_error_notice_footer',
							''
						),
						[
							'p' => [],
							'a' => [
								'href'   => [],
								'target' => [],
								'class'  => [],
								'rel'    => [],
							],
						]
					);
					?>
				</div>

				<?php
			}
		}
	}

	/**
	 * Check whether we are working with a new plugin install.
	 *
	 * @since 2.1.0
	 *
	 * @return bool
	 */
	protected function is_new_install() {

		/*
		 * No previously installed 0.*.
		 * 'easy_wp_smtp_initial_version' option appeared in 1.3.0. So we make sure it exists.
		 * No previous plugin upgrades.
		 */
		if (
			! get_option( 'mailer', false ) &&
			get_option( 'easy_wp_smtp_initial_version', false ) &&
			version_compare( EasyWPSMTP_PLUGIN_VERSION, get_option( 'easy_wp_smtp_initial_version' ), '=' )
		) {
			return true;
		}

		return false;
	}

	/**
	 * Detect if there are plugins activated that will cause a conflict.
	 *
	 * @since 2.0.0
	 */
	public function detect_conflicts() {

		// Display only for those who can actually deactivate plugins.
		if ( ! current_user_can( easy_wp_smtp()->get_capability_manage_options() ) ) {
			return;
		}

		$conflicts = new Conflicts();

		if ( $conflicts->is_detected() ) {
			$conflicts->notify();
		}
	}

	/**
	 * Init the \PHPMailer replacement.
	 *
	 * @since 2.0.0
	 *
	 * @return MailCatcherInterface
	 */
	public function replace_phpmailer() {

		global $phpmailer;

		return $this->replace_w_fake_phpmailer( $phpmailer );
	}

	/**
	 * Overwrite default PhpMailer with our MailCatcher.
	 *
	 * @since 2.0.0
	 *
	 * @param null $obj PhpMailer object to override with own implementation.
	 *
	 * @return MailCatcherInterface
	 */
	protected function replace_w_fake_phpmailer( &$obj = null ) {

		$obj = $this->generate_mail_catcher( true );

		return $obj;
	}

	/**
	 * What to do on plugin activation.
	 *
	 * @since 2.0.0
	 */
	public function activate() {

		// Store the plugin version when initial install occurred.
		add_option( 'easy_wp_smtp_initial_version', EasyWPSMTP_PLUGIN_VERSION, '', false );

		// Store the plugin version activated to reference with upgrades.
		update_option( 'easy_wp_smtp_version', EasyWPSMTP_PLUGIN_VERSION, false );

		// Don't save default options after upgrade from 1.x version.
		if ( empty( get_option( 'swpsmtp_options' ) ) ) {
			// Save default options, only once.
			Options::init()->set( Options::get_defaults(), true );
		}

		/**
		 * Store the timestamp of first plugin activation.
		 *
		 * @since 2.0.0
		 */
		add_option( 'easy_wp_smtp_activated_time', time(), '', false );

		/**
		 * Store the timestamp of the first plugin activation by license type.
		 *
		 * @since 2.1.0
		 */
		$license_type = is_readable( $this->plugin_path . '/src/Pro/Pro.php' ) ? 'pro' : 'lite';
		$activated    = get_option( 'easy_wp_smtp_activated', [] );

		if ( empty( $activated[ $license_type ] ) ) {
			$activated[ $license_type ] = time();
			update_option( 'easy_wp_smtp_activated', $activated );
		}

		set_transient( 'easy_wp_smtp_just_activated', true, 60 );

		// Add transient to trigger redirect to the Setup Wizard.
		set_transient( 'easy_wp_smtp_activation_redirect', true, 30 );
	}

	/**
	 * Whether this is a Pro version of a plugin.
	 *
	 * @since 2.1.0
	 *
	 * @return bool
	 */
	public function is_pro() {

		return apply_filters( 'easy_wp_smtp_core_is_pro', ! empty( $this->pro ) );
	}

	/**
	 * Get the current license type.
	 *
	 * @since 2.1.0
	 *
	 * @return string Default value: lite.
	 */
	public function get_license_type() {

		$type = Options::init()->get( 'license', 'type' );

		if ( empty( $type ) ) {
			$type = 'lite';
		}

		return strtolower( $type );
	}

	/**
	 * Get the current license key.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_license_key() {

		$key = Options::init()->get( 'license', 'key' );

		if ( empty( $key ) ) {
			$key = '';
		}

		return $key;
	}

	/**
	 * Upgrade link used within the various admin pages.
	 *
	 * @since 2.1.0
	 *
	 * @param array|string $utm Array of UTM params, or if string provided - utm_content URL parameter.
	 *
	 * @return string
	 */
	public function get_upgrade_link( $utm ) {

		$url = $this->get_utm_url( 'https://easywpsmtp.com/lite-upgrade/', $utm );

		/**
		 * Filters upgrade link.
		 *
		 * @since 2.1.0
		 *
		 * @param string $url Upgrade link.
		 */
		return apply_filters( 'easy_wp_smtp_core_get_upgrade_link', $url );
	}

	/**
	 * Get UTM URL.
	 *
	 * @since 2.0.0
	 *
	 * @param string       $url Base url.
	 * @param array|string $utm Array of UTM params, or if string provided - utm_content URL parameter.
	 *
	 * @return string
	 */
	public function get_utm_url( $url, $utm ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		// Defaults.
		$source   = 'WordPress';
		$medium   = 'plugin-settings';
		$campaign = $this->is_pro() ? 'plugin' : 'liteplugin';
		$content  = 'general';
		$locale   = get_user_locale();

		if ( is_array( $utm ) ) {
			if ( isset( $utm['source'] ) ) {
				$source = $utm['source'];
			}
			if ( isset( $utm['medium'] ) ) {
				$medium = $utm['medium'];
			}
			if ( isset( $utm['campaign'] ) ) {
				$campaign = $utm['campaign'];
			}
			if ( isset( $utm['content'] ) ) {
				$content = $utm['content'];
			}
			if ( isset( $utm['locale'] ) ) {
				$locale = $utm['locale'];
			}
		} elseif ( is_string( $utm ) ) {
			$content = $utm;
		}

		$query_args = [
			'utm_source'   => esc_attr( rawurlencode( $source ) ),
			'utm_medium'   => esc_attr( rawurlencode( $medium ) ),
			'utm_campaign' => esc_attr( rawurlencode( $campaign ) ),
			'utm_locale'   => esc_attr( sanitize_key( $locale ) ),
		];

		if ( ! empty( $content ) ) {
			$query_args['utm_content'] = esc_attr( rawurlencode( $content ) );
		}

		return add_query_arg( $query_args, $url );
	}

	/**
	 * Whether the emailing functionality is blocked, with either an option or a constatnt.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_blocked() {

		return (bool) Options::init()->get( 'general', 'do_not_send' );
	}

	/**
	 * Require the action scheduler in an early plugins_loaded hook (-10).
	 *
	 * @see   https://actionscheduler.org/usage/#load-order
	 *
	 * @since 2.0.0
	 */
	public function load_action_scheduler() {

		require_once $this->plugin_path . '/vendor/woocommerce/action-scheduler/action-scheduler.php';
	}

	/**
	 * Get the list of all custom DB tables that should be present in the DB.
	 *
	 * @since 2.1.0
	 *
	 * @return array List of table names.
	 */
	public function get_custom_db_tables() {

		$tables = [
			Meta::get_table_name(),
			DebugEvents::get_table_name(),
		];

		if ( $this->get_queue()->is_enabled() ) {
			$tables[] = Queue::get_table_name();
		}

		return apply_filters( 'easy_wp_smtp_core_get_custom_db_tables', $tables );
	}

	/**
	 * Generate the correct MailCatcher object based on the PHPMailer version used in WP.
	 *
	 * Also conditionally require the needed class files.
	 *
	 * @see   https://make.wordpress.org/core/2020/07/01/external-library-updates-in-wordpress-5-5-call-for-testing/
	 *
	 * @since 2.0.0
	 *
	 * @param bool $exceptions True if external exceptions should be thrown.
	 *
	 * @return MailCatcherInterface
	 */
	public function generate_mail_catcher( $exceptions = null ) {

		$is_old_version = version_compare( get_bloginfo( 'version' ), '5.5-alpha', '<' );

		if ( $is_old_version ) {
			if ( ! class_exists( '\PHPMailer', false ) ) {
				require_once ABSPATH . WPINC . '/class-phpmailer.php';
			}

			$class_name = MailCatcher::class;
		} else {
			if ( ! class_exists( '\PHPMailer\PHPMailer\PHPMailer', false ) ) {
				require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
			}

			if ( ! class_exists( '\PHPMailer\PHPMailer\Exception', false ) ) {
				require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
			}

			if ( ! class_exists( '\PHPMailer\PHPMailer\SMTP', false ) ) {
				require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
			}

			$class_name = MailCatcherV6::class;
		}

		/**
		 * Filters MailCatcher class name.
		 *
		 * @since 2.0.0
		 *
		 * @param string $mail_catcher_class_name The MailCatcher class name.
		 */
		$class_name = apply_filters( 'easy_wp_smtp_core_generate_mail_catcher', $class_name );

		$mail_catcher = new $class_name( $exceptions );

		if ( $is_old_version ) {
			$mail_catcher::$validator = static function ( $email ) {
				return (bool) is_email( $email );
			};
		}

		return $mail_catcher;
	}

	/**
	 * Check if the passed object is a valid PHPMailer object.
	 *
	 * @since 2.0.0
	 *
	 * @param object $phpmailer A potential PHPMailer object to be tested.
	 *
	 * @return bool
	 */
	public function is_valid_phpmailer( $phpmailer ) {

		return $phpmailer instanceof MailCatcherInterface ||
		       $phpmailer instanceof \PHPMailer ||
		       $phpmailer instanceof \PHPMailer\PHPMailer\PHPMailer;
	}

	/**
	 * Load the plugin usage tracking.
	 *
	 * @since 2.1.0
	 *
	 * @return UsageTracking
	 */
	public function get_usage_tracking() {

		static $usage_tracking;

		if ( ! isset( $usage_tracking ) ) {
			$usage_tracking = apply_filters( 'easy_wp_smtp_core_get_usage_tracking', new UsageTracking() );

			if ( method_exists( $usage_tracking, 'load' ) ) {
				add_action( 'after_setup_theme', [ $usage_tracking, 'load' ] );
			}
		}

		return $usage_tracking;
	}

	/**
	 * Load the plugin admin notifications functionality and initializes it.
	 *
	 * @since 2.0.0
	 *
	 * @return Notifications
	 */
	public function get_notifications() {

		static $notifications = null;

		if ( is_null( $notifications ) ) {

			/**
			 * Filter the notifications class name.
			 *
			 * @since 2.0.0
			 *
			 * @param Notifications $notifications_class_name The notifications class name to be instantiated.
			 */
			$class_name    = apply_filters( 'easy_wp_smtp_core_get_notifications', Notifications::class );
			$notifications = new $class_name();

			if ( method_exists( $notifications, 'init' ) ) {
				$notifications->init();
			}
		}

		return $notifications;
	}

	/**
	 * Prepare the HTML output for a plugin loader/spinner.
	 *
	 * @since 2.0.0
	 *
	 * @param string $color The color of the loader ('', 'blue' or 'white'), where '' is default orange.
	 * @param string $size  The size of the loader ('lg', 'md', 'sm').
	 *
	 * @return string
	 */
	public function prepare_loader( $color = '', $size = 'md' ) {

		$svg_name = 'loading';

		if ( in_array( $color, [ 'blue', 'white' ], true ) ) {
			$svg_name .= '-' . $color;
		}

		if ( ! in_array( $size, [ 'lg', 'md', 'sm' ], true ) ) {
			$size = 'md';
		}

		return '<img src="' . esc_url( $this->plugin_url . '/assets/images/loaders/' . $svg_name . '.svg' ) . '" alt="' . esc_attr__( 'Loading', 'easy-wp-smtp' ) . '" class="easy-wp-smtp-loading easy-wp-smtp-loading-' . $size . '">';
	}

	/**
	 * Initialize the Connect functionality.
	 * This has to execute after pro was loaded, since we need check for plugin license type (if pro or not).
	 * That's why it's hooked to the same WP hook (`plugins_loaded`) as `get_pro` with lower priority.
	 *
	 * @since 2.1.0
	 */
	public function get_connect() {

		static $connect;

		if ( ! isset( $connect ) && ! $this->is_pro() ) {
			$connect = apply_filters( 'easy_wp_smtp_core_get_connect', new Connect() );

			if ( method_exists( $connect, 'hooks' ) ) {
				$connect->hooks();
			}
		}

		return $connect;
	}

	/**
	 * Load the plugin compatibility functionality and initializes it.
	 *
	 * @since 2.1.0
	 *
	 * @return Compatibility
	 */
	public function get_compatibility() {

		static $compatibility;

		if ( ! isset( $compatibility ) ) {

			/**
			 * Filters compatibility instance.
			 *
			 * @since 2.1.0
			 *
			 * @param \EasyWPSMTP\Compatibility\Compatibility $compatibility Compatibility instance.
			 */
			$compatibility = apply_filters( 'easy_wp_smtp_core_get_compatibility', new Compatibility() );

			if ( method_exists( $compatibility, 'init' ) ) {
				$compatibility->init();
			}
		}

		return $compatibility;
	}

	/**
	 * Get the Dashboard Widget object (lite or pro version).
	 *
	 * @since 2.1.0
	 *
	 * @return DashboardWidget
	 */
	public function get_dashboard_widget() {

		static $dashboard_widget;

		if ( ! isset( $dashboard_widget ) ) {

			/**
			 * Filter the dashboard widget class name.
			 *
			 * @since 2.1.0
			 *
			 * @param DashboardWidget $class_name The dashboard widget class name to be instantiated.
			 */
			$class_name       = apply_filters( 'easy_wp_smtp_core_get_dashboard_widget', DashboardWidget::class );
			$dashboard_widget = new $class_name();

			if ( method_exists( $dashboard_widget, 'init' ) ) {
				$dashboard_widget->init();
			}
		}

		return $dashboard_widget;
	}

	/**
	 * Get the reports object (lite or pro version).
	 *
	 * @since 2.1.0
	 *
	 * @return Reports
	 */
	public function get_reports() {

		static $reports;

		if ( ! isset( $reports ) ) {

			/**
			 * Filter the reports class name.
			 *
			 * @since 2.1.0
			 *
			 * @param Reports $class_name The reports class name to be instantiated.
			 */
			$class_name = apply_filters( 'easy_wp_smtp_core_get_reports', Reports::class );
			$reports    = new $class_name();

			if ( method_exists( $reports, 'init' ) ) {
				$reports->init();
			}
		}

		return $reports;
	}

	/**
	 * Get the DBRepair object (lite or pro version).
	 *
	 * @since 2.1.0
	 *
	 * @return DBRepair
	 */
	public function get_db_repair() {

		static $db_repair;

		if ( ! isset( $db_repair ) ) {

			/**
			 * Filter the DBRepair class name.
			 *
			 * @since 2.1.0
			 *
			 * @param DBRepair $class_name The reports class name to be instantiated.
			 */
			$class_name = apply_filters( 'easy_wp_smtp_core_get_db_repair', DBRepair::class );
			$db_repair  = new $class_name();

			if ( method_exists( $db_repair, 'hooks' ) ) {
				$db_repair->hooks();
			}
		}

		return $db_repair;
	}

	/**
	 * Get connections manager.
	 *
	 * @since 2.0.0
	 *
	 * @return ConnectionsManager
	 */
	public function get_connections_manager() {

		static $connections_manager = null;

		if ( is_null( $connections_manager ) ) {

			/**
			 * Filter the connections manager class name.
			 *
			 * @since 2.0.0
			 *
			 * @param ConnectionsManager $connections_manager_class_name The connections manager class name to be instantiated.
			 */
			$class_name          = apply_filters( 'easy_wp_smtp_core_get_connections_manager', ConnectionsManager::class );
			$connections_manager = new $class_name();

			if ( method_exists( $connections_manager, 'hooks' ) ) {
				$connections_manager->hooks();
			}
		}

		return $connections_manager;
	}

	/**
	 * Get the `wp_mail` function initiator.
	 *
	 * @since 2.0.0
	 *
	 * @return WPMailInitiator
	 */
	public function get_wp_mail_initiator() {

		static $wp_mail_initiator = null;

		if ( is_null( $wp_mail_initiator ) ) {

			/**
			 * Filter the `wp_mail` function initiator class name.
			 *
			 * @since 2.0.0
			 *
			 * @param WPMailInitiator $wp_mail_initiator_class_name The `wp_mail` function initiator class name to be instantiated.
			 */
			$class_name        = apply_filters( 'easy_wp_smtp_core_get_wp_mail_initiator', WPMailInitiator::class );
			$wp_mail_initiator = new $class_name();

			if ( method_exists( $wp_mail_initiator, 'hooks' ) ) {
				$wp_mail_initiator->hooks();
			}
		}

		return $wp_mail_initiator;
	}

	/**
	 * Detect incorrect `wp_mail` function location and display warning.
	 *
	 * @since 2.0.0
	 */
	private function wp_mail_function_incorrect_location_notice() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		/**
		 * Filters whether to display incorrect `wp_mail` function location warning.
		 *
		 * @since 2.0.0
		 *
		 * @param bool $display Whether to display incorrect `wp_mail` function location warning.
		 */
		$display_notice = apply_filters( 'easy_wp_smtp_core_wp_mail_function_incorrect_location_notice', true );

		if ( ! $display_notice || ! defined( 'ABSPATH' ) || ! defined( 'WPINC' ) ) {
			return;
		}

		try {
			$wp_mail_reflection = new ReflectionFunction( 'wp_mail' );
			$wp_mail_filepath   = $wp_mail_reflection->getFileName();
			$separator          = defined( 'DIRECTORY_SEPARATOR' ) ? DIRECTORY_SEPARATOR : '/';

			$wp_mail_original_filepath = ABSPATH . WPINC . $separator . 'pluggable.php';

			if ( str_replace( '\\', '/', $wp_mail_filepath ) === str_replace( '\\', '/', $wp_mail_original_filepath ) ) {
				return;
			}

			if ( strpos( $wp_mail_filepath, WPINC . $separator . 'pluggable.php' ) !== false ) {
				return;
			}

			$conflict = WP::get_initiator( $wp_mail_filepath );

			$message = esc_html__( 'Easy WP SMTP has detected incorrect "wp_mail" function location. Usually, this means that emails will not be sent successfully!', 'easy-wp-smtp' );

			if ( $conflict['type'] === 'plugin' ) {
				$message .= '<br><br>' . sprintf(
					/* translators: %s - plugin name. */
						esc_html__( 'It looks like the "%s" plugin is overwriting the "wp_mail" function. Please reach out to the plugin developer on how to disable or remove the "wp_mail" function overwrite to prevent conflicts with Easy WP SMTP.', 'easy-wp-smtp' ),
						esc_html( $conflict['name'] )
					);
			} elseif ( $conflict['type'] === 'mu-plugin' ) {
				$message .= '<br><br>' . sprintf(
					/* translators: %s - must-use plugin name. */
						esc_html__( 'It looks like the "%s" must-use plugin is overwriting the "wp_mail" function. Please reach out to your hosting provider on how to disable or remove the "wp_mail" function overwrite to prevent conflicts with Easy WP SMTP.', 'easy-wp-smtp' ),
						esc_html( $conflict['name'] )
					);
			} elseif ( $wp_mail_filepath === ABSPATH . 'wp-config.php' ) {
				$message .= '<br><br>' . esc_html__( 'It looks like it\'s overwritten in the "wp-config.php" file. Please reach out to your hosting provider on how to disable or remove the "wp_mail" function overwrite to prevent conflicts with Easy WP SMTP.', 'easy-wp-smtp' );
			}

			$message .= '<br><br>' . sprintf(
				/* translators: %s - path. */
					esc_html__( 'Current function path: %s', 'easy-wp-smtp' ),
					$wp_mail_filepath . ':' . $wp_mail_reflection->getStartLine()
				);

			printf(
				'<div class="notice easy-wp-smtp-notice %1$s"><p>%2$s</p></div>',
				esc_attr( WP::ADMIN_NOTICE_ERROR ),
				wp_kses( $message, [ 'br' => [] ] )
			);
		} catch ( Exception $e ) {
			return;
		}
	}

	/**
	 * Get the default capability to manage everything for Easy WP SMTP.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_capability_manage_options() {

		/**
		 * Filters the default capability to manage everything for Easy WP SMTP.
		 *
		 * @since 2.3.0
		 *
		 * @param string $capability The default capability to manage everything for Easy WP SMTP.
		 */
		return apply_filters( 'easy_wp_smtp_core_get_capability_manage_options', 'manage_options' );
	}

	/**
	 * Load the queue functionality.
	 *
	 * @since 2.6.0
	 *
	 * @return Queue
	 */
	public function get_queue() {

		static $queue;

		if ( ! isset( $queue ) ) {
			/**
			 * Filter the Queue object.
			 *
			 * @since 2.6.0
			 *
			 * @param Queue $queue The Queue object.
			 */
			$queue = apply_filters( 'easy_wp_smtp_core_get_queue', new Queue() );
		}

		return $queue;
	}
}
