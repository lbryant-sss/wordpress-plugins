<?php
/**
 * Intelligent Starter Templates Loader
 *
 * @since  3.0.0-beta.1
 * @package Astra Sites
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Astra Sites Importer
 */
class Intelligent_Starter_Templates_Loader {

	/**
	 * Instance
	 *
	 * @since 3.0.0
	 * @access private
	 * @var object Class object.
	 */
    private static $instance = null;

    /**
     * Initiator
     *
     * @since 3.0.0
	 * @return mixed 
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

	/**
	 * List of hosting providers.
	 * 
	 * @var array<int, string> Hosting Provider.
	 */
	private $hosting_providers = array(
		'unaux',
		'epizy',
		'ezyro',
	);

	/**
	 * Constructor.
	 *
	 * @since  3.0.0-beta.1
	 */
	public function __construct() {
		// Starter Content.
		require_once INTELLIGENT_TEMPLATES_DIR . 'classes/class-astra-sites-onboarding-setup.php';
		require_once INTELLIGENT_TEMPLATES_DIR . 'classes/class-astra-sites-reporting.php';
		require_once INTELLIGENT_TEMPLATES_DIR . 'classes/class-astra-sites-zipwp-helper.php';
		require_once INTELLIGENT_TEMPLATES_DIR . 'classes/class-astra-sites-zipwp-integration.php';
		require_once INTELLIGENT_TEMPLATES_DIR . 'classes/class-astra-sites-zipwp-api.php';
		require_once INTELLIGENT_TEMPLATES_DIR . 'classes/class-astra-sites-install-plugin.php';

		// Admin Menu.
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );

		// Assets loading.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'admin_init' , array( $this, 'page_builder_field' )  );

	}

	/**
	 * Checks if legacy Beaver Builder support is enabled.
	 *
	 * This method applies a filter to allow enabling support for Beaver Builder, which is being gradually deprecated.
	 *
	 * @since 4.4.16
	 * @return bool Returns `true` if legacy Beaver Builder support is enabled, `false` otherwise.
	 */
	public static function is_legacy_beaver_builder_enabled() {
		/**
		 * Filter to enable legacy Beaver Builder support.
		 *
		 * @param bool $enabled Default value indicating if Beaver Builder support is enabled. Default to `false`.
		 *
		 * @since 4.4.16
		 * @return bool Returns `true` if Beaver Builder support is enabled, `false` otherwise.
		 *
		 * Note: this filter is also added in Ai Builder library at ai-builder/ai-builder-plugin-loader.php file.
		 */
		return boolval( apply_filters( 'astra_sites_enable_legacy_beaver_builder_support', false ) );
	}

	/**
	 * Add main menu
	 *
	 * @since 3.0.0-beta.1
	 * 
	 * @return void
	 */
	public function admin_menu() {
		$page_title = apply_filters( 'astra_sites_menu_page_title', esc_html__( 'Starter Templates', 'astra-sites' ) );

		add_theme_page( $page_title, $page_title, 'manage_options', 'starter-templates', array( $this, 'menu_callback' ) );
	}

	/**
	 * Menu callback
	 *
	 * @since 3.0.0-beta.1
	 * 
	 * @return void
	 */
	public function menu_callback() {
		?>
		<div class="astra-sites-menu-page-wrapper">
			<div id="astra-sites-menu-page">
				<div id="starter-templates-ai-root"></div>
			</div>
		</div>
		<?php
	}

	/**
	 * Admin Body Classes
	 *
	 * @since 3.0.0-beta.1
	 * @param string $classes Space separated class string.
	 * 
	 * @return string
	 */
	public function admin_body_class( $classes = '' ) {
		$onboarding_class = isset( $_GET['page'] ) && 'starter-templates' === $_GET['page'] ? 'intelligent-starter-templates-onboarding' : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Fetching a $_GET value, no nonce available to validate.
		$classes .= ' ' . $onboarding_class . ' ';

		return $classes;

	}

	/**
	 * Enqueue scripts in the admin area.
	 *
	 * @param string $hook Current screen hook.
	 *
	 * @return void
	 */
	public function enqueue_scripts( $hook = '' ) {

		// After activating the starter template from Astra notice for the first time, the templates was not displayed because of template import process not fully done.
		if( isset( $_GET['ast-disable-activation-notice'] ) ){
			$current_url = home_url( $_SERVER['REQUEST_URI'] );
			$current_url = str_replace( '&ast-disable-activation-notice', '', $current_url );
			wp_safe_redirect( $current_url );
			exit;
		}

		if ( 'appearance_page_starter-templates' !== $hook ) {
			return;
		}

		remove_all_actions( 'admin_notices' );

		$data = Astra_Sites::get_instance()->get_local_vars();

		wp_localize_script( 'jquery', 'astraSitesVars', $data );

		$file = INTELLIGENT_TEMPLATES_DIR . 'assets/dist/onboarding/main.asset.php';
		if ( ! file_exists( $file ) ) {
			return;
		}

		$asset = require_once $file;

		if ( ! isset( $asset ) ) {
			return;
		}

		wp_register_script(
			'starter-templates-onboarding',
			INTELLIGENT_TEMPLATES_URI . 'assets/dist/onboarding/main.js',
			array_merge( $asset['dependencies'], array('updates') ),
			$asset['version'],
			true
		);

		$partner_id = apply_filters( 'zipwp_partner_url_param', '' );
		$zipwp_auth = array(
			'screen_url'   => ZIPWP_APP,
			'redirect_url' => admin_url( 'themes.php?page=ai-builder' ),
		);

		if( !empty( $partner_id ) ) {
			$zipwp_auth[ 'partner_id' ] = $partner_id;
		}

		wp_localize_script(
			'starter-templates-onboarding', 'wpApiSettings', array(
				'root' => esc_url_raw( get_rest_url() ),
				'nonce' => ( wp_installing() && ! is_multisite() ) ? '' : wp_create_nonce( 'wp_rest' ),
				'zipwp_auth' => $zipwp_auth,
			)
		);

		wp_localize_script( 'starter-templates-onboarding', 'starterTemplates', $this->get_starter_templates_onboarding_localized_array() );

		wp_enqueue_media();
		wp_enqueue_script( 'starter-templates-onboarding' );
		// Set the script translations.
		wp_set_script_translations( 'starter-templates-onboarding', 'astra-sites', ASTRA_SITES_DIR . 'languages' );
		
		wp_enqueue_style( 'starter-templates-onboarding', INTELLIGENT_TEMPLATES_URI . 'assets/dist/onboarding/style-main.css', array(), $asset['version'] );
		wp_style_add_data( 'starter-templates-onboarding', 'rtl', 'replace' );

		// Load fonts from Google.
		wp_enqueue_style( 'starter-templates-onboarding-google-fonts', $this->google_fonts_url(), array( 'starter-templates-onboarding' ), 'all' );
	}

	/**
	 * Get localized array for starter templates.
	 *
	 * @return array<string, mixed>
	 */
	private function get_starter_templates_onboarding_localized_array() {
		$current_user = wp_get_current_user();

		$site_url = add_query_arg(
			array(
				'preview-nonce' => wp_create_nonce( 'starter-templates-preview' ),
			), site_url( '/' )
		);

		$spectraTheme = 'not-installed';
		$themeStatus = Astra_Sites::get_instance()->get_theme_status();
		// Theme installed and activate.
		if ( 'spectra-one' === get_option( 'stylesheet', 'astra' ) ) {
			$spectraTheme = 'installed-and-active';
			$themeStatus = 'installed-and-active';
		}

		$data = array(
			'imageDir' => INTELLIGENT_TEMPLATES_URI . 'assets/images/',
			'logoUrl' => apply_filters( 'st_ai_onboarding_logo' , INTELLIGENT_TEMPLATES_URI . 'assets/images/build-with-ai/st-logo-dark.svg' ),
			'URI' => INTELLIGENT_TEMPLATES_URI,
			'buildDir' => INTELLIGENT_TEMPLATES_URI . 'assets/dist/',
			'previewUrl' => $site_url,
			'adminUrl' => admin_url(),
			'demoId' => 0,
			'skipImport' => false,
			'adminEmail' => $current_user->user_email,
			'themeStatus' => $themeStatus,
			'spectraTheme' => $spectraTheme,
			'nonce' => wp_create_nonce( 'astra-sites-set-ai-site-data' ),
			'restNonce' => wp_create_nonce( 'wp_rest' ),
			'retryTimeOut' => 5000, // 10 Seconds.
			'siteUrl' => get_site_url(),
			'searchData' => Astra_Sites::get_instance()->get_api_domain() . 'wp-json/starter-templates/v1/ist-data',
			'firstImportStatus' => get_option( 'astra_sites_import_complete', false ),
			'supportLink' => 'https://wpastra.com/starter-templates-support/?ip=' . Astra_Sites_Helper::get_client_ip(),
			'isElementorDisabled'=> get_option( 'st-elementor-builder-flag' ),
			'isBeaverBuilderDisabled'=> get_option( 'st-beaver-builder-flag' ) || ! self::is_legacy_beaver_builder_enabled(),
			'analytics' => get_site_option( 'bsf_analytics_optin', false ),
			'phpVersion' => PHP_VERSION,
			'reportError' => $this->should_report_error(),
			'bsfUsageTracking' => get_site_option( 'bsf_analytics_optin', 'no' ) === 'yes',
			'showOtherBuilders' => get_option( 'st-elementor-builder-flag', false ) || ( self::is_legacy_beaver_builder_enabled() && get_option( 'st-beaver-builder-flag', false ) ),
		);

		return apply_filters( 'starter_templates_onboarding_localize_vars', $data );
	}

	/**
	 * Check if we should report error or not.
	 * Skipping error reporting for a few hosting providers.
	 * 
	 * @return bool
	 */
	public function should_report_error() {

		/**
		 * Byassing error reporting for a few hosting providers.
		 */
		foreach( $this->hosting_providers as $provider ) {
			if ( strpos( ABSPATH, $provider ) !== false ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Genereate and return the Google fonts url.
	 *
	 * @since 3.0.0-beta.1
	 * @return string
	 */
	public function google_fonts_url() {

		$fonts_url = '';
		$font_families = array(
			'Inter:400,500,600',
			'Figtree:400,500,600,700'
		);

		$query_args = array(
			'family' => rawurlencode( implode( '|', $font_families ) ),
			'subset' => rawurlencode( 'latin,latin-ext' ),
		);

		$fonts_url = add_query_arg( $query_args, '//fonts.googleapis.com/css' );

		return $fonts_url;
	}

	/**
	 * Register page builder templates flag.
	 *
	 * @return void
	 */
	public function page_builder_field() {
		register_setting( 'general', 'st-elementor-builder-flag', array( 'sanitize_callback' => 'esc_attr' ) );
		register_setting( 'general', 'st-beaver-builder-flag', array( 'sanitize_callback' => 'esc_attr' ) );
		add_settings_field('' , '<label>'. 'Starter Templates' . '</label>' , array($this, 'page_builders_enable_disable_option') , 'general' );
	}

	/**
	 * Enable page builder templates flag markup.
	 *
	 * @return void
	 */
	// page_builders_enable_disable_option
	public function page_builders_enable_disable_option() {
		$elementor_value = get_option( 'st-elementor-builder-flag');
		$beaver_builder_value = get_option( 'st-beaver-builder-flag');
		ob_start();
		?>
			<div style="display:flex;flex-direction:column;gap:15px;">
				<label>
					<input id='st-elementor-builder-flag' type='checkbox' name='st-elementor-builder-flag' value='1' <?php checked(1, $elementor_value, true); ?>>
					<?php _e('Disable Elementor Page Builder Templates in Starter Templates','astra-sites'); ?>
				</label>
				<?php
				if ( self::is_legacy_beaver_builder_enabled() ) {
					?>
					<label>
						<input id='st-beaver-builder-flag' type='checkbox' name='st-beaver-builder-flag' value='1' <?php checked(1, $beaver_builder_value, true); ?>>
						<?php _e('Disable Beaver Builder Page Builder Templates in Starter Templates','astra-sites'); ?>
					</label>
					<?php
				}
				?>
			</div>	
		<?php
		echo ob_get_clean();
	}
}

new Intelligent_Starter_Templates_Loader();
