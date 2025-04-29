<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Wpcf7_Redirect
 * @subpackage Wpcf7_Redirect
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wpcf7_Redirect
 * @subpackage Wpcf7_Redirect
 * @author     Lior Regev <regevlio@gmail.com>
 */
class Wpcf7_Redirect {

	const LEGACY_FIRE_SCRIPT_OPTION_FLAG = 'wpcf7-fire-script-legacy-user-flag';

	/**
	 * Instance of the main plugin class object.
	 *
	 * @var [object]
	 */
	public $cf7_redirect_base;

	/**
	 * Constructor
	 */
	public function init() {
		$this->define();
		$this->load_dependencies();
		$this->cf7_redirect_base = new WPCF7R_Base();

		add_action( 'plugins_loaded', array( $this, 'notice_to_remove_old_plugin' ) );

		add_filter( 'redirection_for_contact_form_7_about_us_metadata', array( $this, 'about_page' ) );
		add_action( 'admin_menu', array( $this, 'handle_upgrade_link' ) );
		add_action( 'admin_menu', array( $this, 'register_dashboard' ) );
		add_filter( 'wpcf7_get_extensions', array( $this, 'filter_deprecated_addons' ) );

		add_filter( 'redirection_for_contact_form_7_float_widget_metadata', array( $this, 'float_widget_data' ) );
		add_filter( 'wpcf7_redirect_float_widget_metadata', array( $this, 'float_widget_data' ) );

		$dashboard = new \WPCF7R_Dashboard();
		$dashboard->register_endpoints();
		$dashboard->load_update_hooks();
		$dashboard->redirect_to_license_page_notices();

		$save_file_helper = new \WPCF7R_Save_File();
		$save_file_helper->register_endpoints();
		$save_file_helper->register_file_deletion();

		$this->mark_legacy_users();

		WPCF7r_Survey::get_instance()->init();
	}

	/**
	 * Add style to highlight the upgrade sub-menu page.
	 *
	 * @return void
	 */
	public function handle_upgrade_link() {

		wp_register_style( 'wpcf7-admin-menu-upgrade', false );
		wp_enqueue_style( 'wpcf7-admin-menu-upgrade' );

		$custom_css = 'ul#adminmenu a[href*="wpcf7r-upgrade"] { color: #adff2e; }';
		wp_add_inline_style( 'wpcf7-admin-menu-upgrade', $custom_css );
	}

	/**
	 * Get float widget data.
	 *
	 * @return array
	 */
	public function float_widget_data() {
		$has_legacy = apply_filters( 'wpcf7r_legacy_used', false );

		return array(
			'nice_name'            => __( 'Redirect for Contact Form 7', 'wpcf7-redirect' ),
			'logo'                 => esc_url_raw( WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/wpcf7-help.png' ),
			'primary_color'        => '#4580ff',
			'pages'                => array( 'toplevel_page_wpcf7r-dashboard' ),
			'has_upgrade_menu'     => false,
			'premium_support_link' => $has_legacy ? 'https://users.freemius.com/login' : '',
			'upgrade_link'         => tsdk_utmify( wpcf7_redirect_upgrade_url(), 'floatWidget' ),
			'documentation_link'   => tsdk_utmify( 'https://docs.themeisle.com/collection/2014-redirection-for-contact-form-7', 'floatWidget' ),
		);
	}

	/**
	 * Filter addons that are deprecated.
	 *
	 * @param array $addons List of addons.
	 *
	 * @return array
	 */
	public function filter_deprecated_addons( $addons ) {
		$deprecated = array(
			'wpcf7r-custom-errors',
			'wpcf7r-login',
			'wpcf7r-register',
			'wpcf7r-monday',
			'wpcf7r-slack',
			'wpcf7r-eliminate-duplicates',
		);

		return array_filter(
			$addons,
			function ( $key ) use ( $deprecated ) {
				return ! in_array( $key, $deprecated );
			},
			ARRAY_FILTER_USE_KEY
		);
	}

	/**
	 * Register the Dashboard menu and load its dependencies.
	 */
	public function register_dashboard() {
		$page_title = __( 'Redirection Dashboard', 'wpcf7-redirect' );
		$menu_title = __( 'CF7 Redirection', 'wpcf7-redirect' );
		$capability = 'manage_options';
		$menu_slug  = 'wpcf7r-dashboard';
		$callback   = array( $this, 'render_dashboard_page' );
		$icon_url   = esc_url_raw( WPCF7_PRO_REDIRECT_BASE_URL . 'assets/images/logo.svg' ); // TODO: add icons when we get the svg.
		$position   = 30;

		$hook = add_menu_page(
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			$callback,
			$icon_url,
			$position
		);

		add_action(
			"load-$hook",
			function () {
				( new \WPCF7R_Dashboard() )->load_resources();
			}
		);

		$dashboard_title = __( 'Dashboard', 'wpcf7-redirect' );
		add_submenu_page(
			'wpcf7r-dashboard',
			$dashboard_title,
			$dashboard_title,
			$capability,
			'wpcf7r-dashboard',
			$callback,
			0
		);
	}

	/**
	 * Render dashboard page content.
	 *
	 * @return void
	 */
	public function render_dashboard_page() {
		?>
		<div id="redirect-dashboard"></div>
		<?php
	}

	/**
	 * Add the about page.
	 *
	 * @return array
	 */
	public function about_page() {
		return array(
			'location' => 'wpcf7r-dashboard',
			'logo'     => esc_url_raw( WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/icon-128x128.png' ),
		);
	}

	/**
	 * Load dependencies
	 */
	public function load_dependencies() {
		// Load all actions.
		foreach ( glob( WPCF7_PRO_REDIRECT_BASE_PATH . 'modules/*.php' ) as $filename ) {
			require_once $filename;
		}
		require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-base.php';
	}

	/**
	 * Notice to remove old plugin
	 */
	public function notice_to_remove_old_plugin() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		if ( is_plugin_active( 'cf7-to-api/cf7-to-api.php' ) ) {
			add_action( 'admin_notices', 'wpcf7_remove_contact_form_7_to_api' );
		}
	}

	/**
	 * Defines
	 */
	public function define() {
		define( 'WPCF7_PRO_REDIRECT_BASE_NAME', plugin_basename( __FILE__ ) );
		define( 'WPCF7_PRO_REDIRECT_BASE_PATH', plugin_dir_path( __FILE__ ) );
		define( 'WPCF7_PRO_REDIRECT_BASE_URL', plugin_dir_url( __FILE__ ) );
		define( 'WPCF7_PRO_REDIRECT_PLUGINS_PATH', plugin_dir_path( __DIR__ ) );
		define( 'WPCF7_PRO_REDIRECT_TEMPLATE_PATH', WPCF7_PRO_REDIRECT_BASE_PATH . 'templates/' );
		define( 'WPCF7_PRO_REDIRECT_ACTIONS_TEMPLATE_PATH', WPCF7_PRO_REDIRECT_CLASSES_PATH . 'actions/html/' );
		define( 'WPCF7_PRO_REDIRECT_ADDONS_PATH', WPCF7_PRO_REDIRECT_PLUGINS_PATH . 'wpcf7r-addons/' );
		define( 'WPCF7_PRO_REDIRECT_ACTIONS_PATH', WPCF7_PRO_REDIRECT_CLASSES_PATH . 'actions/' );
		define( 'WPCF7_PRO_REDIRECT_FIELDS_PATH', WPCF7_PRO_REDIRECT_TEMPLATE_PATH . 'fields/' );
		define( 'WPCF7_PRO_REDIRECT_POPUP_TEMPLATES_PATH', WPCF7_PRO_REDIRECT_TEMPLATE_PATH . 'popups/' );
		define( 'WPCF7_PRO_REDIRECT_POPUP_TEMPLATES_URL', WPCF7_PRO_REDIRECT_BASE_URL . '/templates/popups/' );
		define( 'WPCF7_PRO_REDIRECT_ASSETS_PATH', WPCF7_PRO_REDIRECT_BASE_URL . 'assets/' );
		define( 'WPCF7_PRO_REDIRECT_BUILD_PATH', WPCF7_PRO_REDIRECT_BASE_URL . 'build/' );

		define( 'QFORM_BASE', WPCF7_PRO_REDIRECT_BASE_PATH . 'form/' );
	}

	/**
	 * Mark the legacy users.
	 */
	public function mark_legacy_users() {
		$fire_script_flag = get_option( self::LEGACY_FIRE_SCRIPT_OPTION_FLAG, false );
		if ( false === $fire_script_flag && defined( 'WPCF7_BASENAME' ) ) {
			$plugin_install_option    = str_replace( '-', '_', WPCF7_BASENAME );
			$plugin_install_timestamp = get_option( $plugin_install_option . '_install', false );

			if ( false === $plugin_install_timestamp ) {
				update_option( self::LEGACY_FIRE_SCRIPT_OPTION_FLAG, 'no' );
			} else {
				$install_date = $plugin_install_timestamp;
				$one_week_ago = time() - WEEK_IN_SECONDS;

				if ( $install_date < $one_week_ago ) {
					update_option( self::LEGACY_FIRE_SCRIPT_OPTION_FLAG, 'yes' );
				} else {
					update_option( self::LEGACY_FIRE_SCRIPT_OPTION_FLAG, 'no' );
				}
			}
		}
	}
}
