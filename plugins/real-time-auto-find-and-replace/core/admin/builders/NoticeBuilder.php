<?php namespace RealTimeAutoFindReplace\admin\builders;

/**
 * Custom Notice
 *
 * @package Notices
 * @since 1.0.0
 * @author M.Tuhin <tuhin@codesolz.net>
 */

if ( ! defined( 'CS_RTAFAR_VERSION' ) ) {
	exit;
}


class NoticeBuilder {

	private static $_instance;
	private $admin_notices;
	const TYPES = 'error,warning,info,success';

	private function __construct() {
		$this->admin_notices = new \stdClass();
		foreach ( explode( ',', self::TYPES ) as $type ) {
			$this->admin_notices->{$type} = array();
		}

		add_action( 'admin_init', array( $this, 'action_admin_init' ) );
		add_action( 'admin_notices', array( $this, 'action_admin_notices' ) );
	}

	/**
	 * generate instance
	 *
	 * @return void
	 */
	public static function get_instance() {
		if ( ! ( self::$_instance instanceof self ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Admin init
	 *
	 * @return void
	 */
	public function action_admin_init() {
		if ( isset( $_GET[ CS_NOTICE_ID ] ) ) {
			$dismiss_option = filter_input( INPUT_GET, CS_NOTICE_ID, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

			if ( empty( $dismiss_option ) ) {
				return false;
			}

			update_option( CS_NOTICE_ID . 'ed_' . $dismiss_option, true );
			return wp_send_json(
				array(
					'status' => 'success',
				)
			);
		}
	}

	/**
	 * Init notices
	 *
	 * @return void
	 */
	public function action_admin_notices() {

		global $my_admin_page, $rtafr_menu;
		$screen = get_current_screen();

		if ( \in_array( $screen->id, $rtafr_menu ) ) {
			return;
		}

		foreach ( explode( ',', self::TYPES ) as $type ) {
			foreach ( $this->admin_notices->{$type} as $admin_notice ) {

				$dismiss_url = add_query_arg(
					array(
						CS_NOTICE_ID => $admin_notice->dismiss_option,
					),
					admin_url()
				);

				if ( ! get_option( CS_NOTICE_ID . "ed_{$admin_notice->dismiss_option}" ) &&
					! get_option( CS_NOTICE_ID . "ed_{$admin_notice->dismiss_option}_offPerm" )

					) {

					$dissmissUrl = '';
					$canDissmiss = '';
					if ( $admin_notice->dismiss_option ) {
						$canDissmiss = ' is-dismissible';
						$dissmissUrl = ' data-dismiss-url=' . esc_url( $dismiss_url );
					}

					?><div class="notice cs-notice notice-<?php echo \esc_attr( $type . $canDissmiss ); ?>" <?php echo \esc_attr( $dissmissUrl ); ?>>
						<p>
							<strong><?php echo \esc_html( CS_RTAFAR_PLUGIN_NAME ); ?></strong>
						</p>
						<p><?php echo \wp_kses_post( $admin_notice->message ); ?></p>

					</div>
						<?php
				}
			}
		}
	}

	public function error( $message, $dismiss_option = false ) {
		$this->notice( 'error', $message, $dismiss_option );
	}

	public function warning( $message, $dismiss_option = false ) {
		$this->notice( 'warning', $message, $dismiss_option );
	}

	public function success( $message, $dismiss_option = false ) {
		$this->notice( 'success', $message, $dismiss_option );
	}

	public function info( $message, $dismiss_option = false ) {
		$this->notice( 'info', $message, $dismiss_option );
	}

	private function notice( $type, $message, $dismiss_option ) {
		$notice                 = new \stdClass();
		$notice->message        = $message;
		$notice->dismiss_option = $dismiss_option;

		$this->admin_notices->{$type}[] = $notice;
	}
}


