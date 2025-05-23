<?php
/**
 * Jetpack forms dashboard.
 *
 * @package automattic/jetpack-forms
 */

namespace Automattic\Jetpack\Forms\Dashboard;

use Automattic\Jetpack\Assets;
use Automattic\Jetpack\Connection\Initial_State as Connection_Initial_State;
use Automattic\Jetpack\Connection\Manager as Connection_Manager;
use Automattic\Jetpack\Forms\ContactForm\Contact_Form_Plugin;
use Automattic\Jetpack\Forms\Jetpack_Forms;
use Automattic\Jetpack\Forms\Service\Google_Drive;
use Automattic\Jetpack\Redirect;
use Automattic\Jetpack\Status;
use Automattic\Jetpack\Status\Host;
use Automattic\Jetpack\Tracking;

/**
 * Handles the Jetpack Forms dashboard.
 */
class Dashboard {
	/**
	 * Script handle for the JS file we enqueue in the Feedback admin page.
	 *
	 * @var string
	 */
	const SCRIPT_HANDLE = 'jp-forms-dashboard';

	/**
	 * Priority for the dashboard menu.
	 * Needs to be high enough for us to be able to unregister the default edit.php menu item.
	 *
	 * @var int
	 */
	const MENU_PRIORITY = 999;

	/**
	 * Dashboard_View_Switch instance
	 *
	 * @var Dashboard_View_Switch
	 */
	private $switch;

	/**
	 * Creates a new Dashboard instance.
	 *
	 * @param Dashboard_View_Switch $switch Dashboard_View_Switch instance to use.
	 */
	public function __construct( Dashboard_View_Switch $switch ) {
		$this->switch = $switch;
	}

	/**
	 * Initialize the dashboard.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_admin_submenu' ), self::MENU_PRIORITY );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );

		$this->switch->init();
	}

	/**
	 * Load JavaScript for the dashboard.
	 */
	public function load_admin_scripts() {
		if ( ! ( new Dashboard_View_Switch() )->is_modern_view() ) {
			return;
		}

		Assets::register_script(
			self::SCRIPT_HANDLE,
			'../../dist/dashboard/jetpack-forms-dashboard.js',
			__FILE__,
			array(
				'in_footer'    => true,
				'textdomain'   => 'jetpack-forms',
				'enqueue'      => true,
				'dependencies' => array( 'wp-api-fetch' ),
			)
		);

		if ( Contact_Form_Plugin::can_use_analytics() ) {
			Tracking::register_tracks_functions_scripts( true );
		}

		// Adds Connection package initial state.
		Connection_Initial_State::render_script( self::SCRIPT_HANDLE );

		$api_root = defined( 'IS_WPCOM' ) && IS_WPCOM
			? sprintf( '/wpcom/v2/sites/%s/', esc_url_raw( rest_url() ) )
			: '/wp-json/wpcom/v2/';

		wp_add_inline_script(
			self::SCRIPT_HANDLE,
			'window.jetpackFormsData = ' . wp_json_encode( array( 'apiRoot' => $api_root ) ) . ';',
			'before'
		);
	}

	/**
	 * Register the dashboard admin submenu.
	 */
	public function add_admin_submenu() {
		if ( $this->switch->get_preferred_view() === Dashboard_View_Switch::CLASSIC_VIEW ) {
			// We still need to register the jetpack forms page so it can be accessed manually.
			// NOTE: adding submenu this (parent = '') way DOESN'T SHOW ANYWHERE,
			// it's done just so the page URL doesn't break.
			add_submenu_page(
				'',
				__( 'Form Responses', 'jetpack-forms' ),
				_x( 'Form Responses', 'menu label for form responses', 'jetpack-forms' ),
				'edit_pages',
				'jetpack-forms',
				array( $this, 'render_dashboard' )
			);

			return;
		}

		$is_wpcom = ( new Host() )->is_wpcom_simple();

		// MODERN VIEW -- remove the old submenu and add the new one.
		// Check if Polldaddy/Crowdsignal plugin is active
		if ( ! $is_wpcom && ! is_plugin_active( 'polldaddy/polldaddy.php' ) ) {
			remove_menu_page( 'feedback' );

			add_menu_page(
				__( 'Form Responses', 'jetpack-forms' ),
				_x( 'Feedback', 'post type name shown in menu', 'jetpack-forms' ),
				'edit_pages',
				'jetpack-forms',
				array( $this, 'render_dashboard' ),
				'dashicons-feedback',
				25 // Places 'Feedback' under 'Comments' in the menu
			);
		} else {
			remove_submenu_page( 'feedback', 'edit.php?post_type=feedback' );

			add_submenu_page(
				'feedback',
				__( 'Form Responses', 'jetpack-forms' ),
				_x( 'Form Responses', 'menu label for form responses', 'jetpack-forms' ),
				'edit_pages',
				'jetpack-forms',
				array( $this, 'render_dashboard' ),
				0 // as far top as we can go since responses are the default feedback page.
			);
		}
	}

	/**
	 * Render the dashboard.
	 */
	public function render_dashboard() {
		if ( ! class_exists( 'Jetpack_AI_Helper' ) ) {
			require_once JETPACK__PLUGIN_DIR . '_inc/lib/class-jetpack-ai-helper.php';
		}

		$ai_feature = \Jetpack_AI_Helper::get_ai_assistance_feature();
		$has_ai     = ! is_wp_error( $ai_feature ) ? $ai_feature['has-feature'] : false;

		$jetpack_connected = ( defined( 'IS_WPCOM' ) && IS_WPCOM ) || ( new Connection_Manager( 'jetpack-forms' ) )->is_user_connected( get_current_user_id() );
		$user_id           = (int) get_current_user_id();

		$config = array(
			'blogId'                  => get_current_blog_id(),
			'exportNonce'             => wp_create_nonce( 'feedback_export' ),
			'newFormNonce'            => wp_create_nonce( 'create_new_form' ),
			'gdriveConnection'        => $jetpack_connected && Google_Drive::has_valid_connection( $user_id ),
			'gdriveConnectURL'        => esc_url( Redirect::get_url( 'jetpack-forms-responses-connect' ) ),
			'gdriveConnectSupportURL' => esc_url( Redirect::get_url( 'jetpack-support-contact-form-export' ) ),
			'checkForSpamNonce'       => wp_create_nonce( 'grunion_recheck_queue' ),
			'pluginAssetsURL'         => Jetpack_Forms::assets_url(),
			'siteURL'                 => ( new Status() )->get_site_suffix(),
			'hasFeedback'             => $this->has_feedback(),
			'hasAI'                   => $has_ai,
		);
		?>
		<div id="jp-forms-dashboard" data-config="<?php echo esc_attr( wp_json_encode( $config, JSON_FORCE_OBJECT ) ); ?>"></div>
		<?php
	}

	/**
	 * Returns true if there are any feedback posts on the site.
	 *
	 * @return boolean
	 */
	private function has_feedback() {
		$posts = new \WP_Query(
			array(
				'post_type'   => 'feedback',
				'post_status' => array( 'publish', 'draft', 'spam', 'trash' ),
			)
		);

		return $posts->found_posts > 0;
	}
}
