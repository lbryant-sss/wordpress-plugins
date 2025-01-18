<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Advanced_Shipment_Tracking_Admin_Notice {

	/**
	 * Instance of this class.
	 *
	 * @var object Class Instance
	 */
	private static $instance;
	
	/**
	 * Initialize the main plugin function
	*/
	public function __construct() {
		$this->init();	
	}
	
	/**
	 * Get the class instance
	 *
	 * @return WC_Advanced_Shipment_Tracking_Admin_Notice
	*/
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	
	/*
	* init from parent mail class
	*/
	public function init() {

		add_action( 'ast_settings_admin_notice', array( $this, 'ast_settings_admin_notice' ) );
		add_action( 'admin_init', array( $this, 'ast_settings_admin_notice_ignore' ) );

		add_action( 'admin_notices', array( $this, 'ast_trackship_notice' ) );	
		add_action( 'admin_init', array( $this, 'ast_trackship_notice_ignore' ) );

	}
	
	public function ast_settings_admin_notice() {

		$ignore = get_transient( 'ast_settings_admin_notice_ignore' );
		if ( 'yes' == $ignore ) {
			return;
		}

		include 'views/admin_message_panel.php';
	}

	public function ast_settings_admin_notice_ignore() {
		if ( isset( $_GET['ast-pro-settings-ignore-notice'] ) ) {
			// if (isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'ast_dismiss_notice')) {
			// 	set_transient( 'ast_settings_admin_notice_ignore', 'yes', 2592000 );
			// }

			if (isset($_GET['nonce'])) {
				$nonce = sanitize_text_field($_GET['nonce']);
				if (wp_verify_nonce($nonce, 'ast_dismiss_notice')) {
					set_transient( 'ast_settings_admin_notice_ignore', 'yes', 2592000 );
				}
			}
		}
	}

	/*
	* Display admin notice on plugin install or update
	*/
	public function ast_trackship_notice() { 		
		
		$ts4wc_installed = ( function_exists( 'trackship_for_woocommerce' ) ) ? true : false;
		if ( $ts4wc_installed ) {
			return;
		}
		
		if ( get_option('ast_trackship_notice_ignore') ) {
			return;
		}	
		
		$nonce = wp_create_nonce('ast_trackship_dismiss_notice');
		$dismissable_url = esc_url(add_query_arg(['ast-trackship-notice' => 'true', 'nonce' => $nonce]));

		?>
		<style>		
		.wp-core-ui .notice.ast-dismissable-notice{
			position: relative;
			padding-right: 38px;
			border-left-color: #005B9A;
		}
		.wp-core-ui .notice.ast-dismissable-notice h3{
			margin-bottom: 5px;
		} 
		.wp-core-ui .notice.ast-dismissable-notice a.notice-dismiss{
			padding: 9px;
			text-decoration: none;
		} 
		.wp-core-ui .button-primary.ast_notice_btn {
			background: #005B9A;
			color: #fff;
			border-color: #005B9A;
			text-transform: uppercase;
			padding: 0 11px;
			font-size: 12px;
			height: 30px;
			line-height: 28px;
			margin: 5px 0 15px;
		}
		</style>
		<div class="notice updated notice-success ast-dismissable-notice">			
			<a href="<?php esc_html_e( $dismissable_url ); ?>" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a>			
			<h3>Supercharge Customer Experience!</h3>
			<p>Enhance your WooCommerce store with TrackShip's powerful shipment tracking features! Streamline your post-purchase experience, reduce customer inquiries, boost customer satisfaction and build long lasting relationships with your customers</p>				
			
			<a class="button-primary ast_notice_btn" target="blank" href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=search&s=TrackShip For WooCommerce&plugin-search-input=Search+Plugins' ) ); ?>">Install TrackShip for WooCommerce</a>
			<a class="button-primary ast_notice_btn" href="<?php esc_html_e( $dismissable_url ); ?>">Dismiss</a>				
		</div>	
		<?php 				
	}	
	
	/*
	* Dismiss admin notice for trackship
	*/
	public function ast_trackship_notice_ignore() {
		if ( isset( $_GET['ast-trackship-notice'] ) ) {
			// if (isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'ast_trackship_dismiss_notice')) {
			// 	update_option( 'ast_trackship_notice_ignore', 'true' );
			// }
			
			if (isset($_GET['nonce'])) {
				$nonce = sanitize_text_field($_GET['nonce']);
				if (wp_verify_nonce($nonce, 'ast_trackship_dismiss_notice')) {
					update_option('ast_trackship_notice_ignore', 'true');
				}
			}
			
		}
	}
}
