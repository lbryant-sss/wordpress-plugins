<?php
namespace WPSMTP;

use SolidWP\Mail\Admin\SettingsScreen;
use WPSMTP\Logger\Db;
use WPSMTP\Logger\Table;

class Admin {

	private $wsOptions;

	/**
     * The new solid mail setting.
     *
	 * @var false|mixed|null
	 */
	private $solidMailOptions;

	public static $phpmailer_error;

	public function __construct() {
		$this->wsOptions = get_option( 'wp_smtp_options' );
		$this->solidMailOptions = get_option( SettingsScreen::SETTINGS_SLUG );

		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_wpsmtp_get_logs', array( $this, 'wpsmtp_get_logs' ) );
		add_action( 'wp_ajax_wpsmtp_delete_rows', array( $this, 'wpsmtp_delete_rows' ) );
		add_action( 'wp_ajax_wpsmtp_delete_all_rows', array( $this, 'wpsmtp_delete_all_rows' ) );
	}

	public function add_menu() {
        $icon_url = WPSMTP_ASSETS_URL.'images/Solid-Mail-Icon.svg';

		add_menu_page( __( 'Solid Mail', 'wp-smtp' ), __( 'Solid Mail', 'wp-smtp' ), 'manage_options', 'solidwp-mail', array(
			$this,
			'render_solidsmtp'
		), $icon_url );

		if ( ! isset( $this->solidMailOptions['disable_logs'] ) || 'yes' !== $this->solidMailOptions['disable_logs'] ) {
			add_submenu_page( 'solidwp-mail', __( 'Mail Logs', 'wp-smtp' ), __( 'Mail Logs', 'wp-smtp' ), 'manage_options', 'solidwp-mail-logs', array(
				$this,
				'render_solidsmtp'
			) );
		}

		// Add the Settings submenu
		add_submenu_page( 'solidwp-mail', __( 'Settings', 'wp-smtp' ), __( 'Settings', 'wp-smtp' ), 'manage_options', 'solidwp-mail-settings', array(
			$this,
			'render_solidsmtp'
		) );
	}

	/**
	 * Render the hook point for the app.
	 *
	 * @return void
	 */
	public function render_solidsmtp() {
		require_once WPSMTP_PATH . 'src/admin-views/admin-root.php';
	}

	public function enqueue_scripts() {

		$screen = get_current_screen();

		if ( ! $screen ) {
			return;
		}

		if ( $screen->id === 'wp-smtp_page_wpsmtp_logs' ) {
			wp_enqueue_style( 'wpsmtp-table', WPSMTP_ASSETS_URL . 'css/table.css' );
			wp_enqueue_style( 'datatable', WPSMTP_ASSETS_URL . 'css/jquery.dataTables.min.css' );

			wp_register_script( 'datatable', WPSMTP_ASSETS_URL . 'js/jquery.dataTables.min.js', array( 'jquery' ), false, true );
			wp_register_script( 'dataTables.buttons', WPSMTP_ASSETS_URL . 'js/dataTables.buttons.min.js', array( 'datatable' ), false, true );
			wp_register_script( 'buttons.html5', WPSMTP_ASSETS_URL . 'js/dataTables.buttons.html5.min.js', array( 'datatable', 'dataTables.buttons' ), false, true );
			wp_register_script( 'dataTables.select', WPSMTP_ASSETS_URL . 'js/dataTables.select.min.js', array( 'datatable', 'buttons.html5' ), false, true );

			wp_register_script( 'wpsmtp-table', WPSMTP_ASSETS_URL . 'js/table.js', array('jquery', 'dataTables.select'),false, true );
			wp_localize_script( 'wpsmtp-table', 'wpsmtp', array(
				'ajaxurl' => admin_url('admin-ajax.php'),
			) );

			wp_enqueue_script('wpsmtp-table');
		}
	}

	function render_setup_menu() {
		require_once WPSMTP_PATH . '/wp_smtp_admin.php';
	}

	public function render_log_menu() {
		?>
		<div class="wrap">
			<h1>WPSMTP Email Logs</h1>
			<?php Table::view(); ?>
		</div>
		<?php
	}

	public function wpsmtp_get_logs() {

		check_ajax_referer('wpsmtp', 'security' );

		$result = Db::get_instance()->get();
		$records_count = Db::get_instance()->records_count();

		foreach ( $result as $key => $value ) {
			foreach ( $value as $index => $data ) {
				if ( $index == 'message' ) {

					if ( ! preg_match ('/<br>/', $data, $matches ) && ! preg_match ('/<p>/', $data, $matches ) ) {
						$data = nl2br( $data );
					}

					$result[$key][$index] = wp_kses_post( $data );
				} elseif ( is_serialized( $data ) ) {
					$result[ $key ][ $index ] = implode( ',', array_map( 'esc_html', maybe_unserialize( $data ) ) );
				} else {
					$result[ $key ][ $index ] = esc_html( $data );
				}
			}

        }

		$response = array(
			"draw" => isset( $_GET['draw'] ) ? absint( $_GET['draw'] ) : 1,
			"recordsTotal" => $records_count,
			"recordsFiltered" => $records_count,
			'data' => $result
		);

		if ( isset($_GET['search']['value'] ) && ! empty( $_GET['search']['value'] ) ) {
			$response['recordsFiltered'] = count( $result );
		}

		wp_send_json( $response );
		die();
	}

	public function wpsmtp_delete_rows() {
		check_admin_referer('wpsmtp', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
		    wp_die( 'Permissions Error.' );
        }

		$data = explode( ',', $_GET['ids'] );
		wp_send_json_success( Db::get_instance()->delete_items( array_map( 'absint', $data ) ) );

	}

	public function wpsmtp_delete_all_rows() {
		check_admin_referer('wpsmtp', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Permissions Error.' );
		}

		wp_send_json_success( Db::get_instance()->delete_all_items() );
	}

}