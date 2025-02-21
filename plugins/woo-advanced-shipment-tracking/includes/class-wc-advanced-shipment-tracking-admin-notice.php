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
	 * List of shipping plugins
	 *
	 * @var array
	 */
	public $shipping_services = array(
		'woocommerce-shipstation-integration/woocommerce-shipstation.php' => 'ShipStation',
		'dokan-pro/dokan-pro.php' => 'Dokan',
		'woocommerce-services/woocommerce-services.php' => 'WooCommerce Services',
		'woocommerce-shipping-ups/woocommerce-shipping-ups.php' => 'WooCommerce Shipping',
		'ali2woo-lite/alinext-lite.php' => 'AliExpress Dropshipping',
		'woo-gls-print-label-and-tracking-code/wp-gls-print-label.php' => 'GLS Print Label',
		'woocommerce-gls/woocommerce-gls.php' => 'WooCommerce GLS by Tehster',
		'woocommerce-gls-premium/woocommerce-gls.php' => 'WooCommerce GLS by Tehster',
		'woocommerce-germanized/woocommerce-germanized.php' => 'WooCommerce Germanized',
	);
	
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

		// add_action( 'ast_settings_admin_notice', array( $this, 'ast_settings_admin_notice' ) );

		add_action( 'admin_notices', array( $this, 'ast_trackship_notice' ) );	
		add_action( 'admin_init', array( $this, 'ast_trackship_notice_ignore' ) );

		add_action( 'admin_notices', array( $this, 'ast_admin_notice_shipping_integration' ) );	
		add_action( 'admin_init', array( $this, 'ast_pro_shipping_integration_notice_ignore' ) );

		add_shortcode( 'ast_settings_admin_notice', array( $this, 'ast_settings_admin_notice' ) );
	}

	/**
	 * Check if any shipping service plugin is active
	 */
	public function is_any_shipping_plugin_active() {
		foreach ( $this->shipping_services as $plugin_file => $service_name ) {
			if ( is_plugin_active( $plugin_file ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Display admin notice for missing shipping integration
	 */
	public function ast_settings_admin_notice() {
		// Only show the notice if no shipping plugins are active AND it's before or on March 15, 2025
		if ( $this->is_any_shipping_plugin_active() && strtotime('now') > strtotime('2025-03-15') ) {
			ob_start();
			include 'views/admin_message_panel.php';
			return ob_get_clean();
		} else if ( ! $this->is_any_shipping_plugin_active() ) {
			ob_start();
			include 'views/admin_message_panel.php';
			return ob_get_clean();
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
			
			if (isset($_GET['nonce'])) {
				$nonce = sanitize_text_field($_GET['nonce']);
				if (wp_verify_nonce($nonce, 'ast_trackship_dismiss_notice')) {
					update_option('ast_trackship_notice_ignore', 'true');
				}
			}
			
		}
	}

	
	/**
	 * Display admin notice on plugin install or update
	 */
	public function ast_admin_notice_shipping_integration() {

		if ( get_option( 'ast_pro_shipping_integration_notice_ignore' ) ) {
			return;
		}

		// Check if the date is past March 15, 2025
		if ( strtotime( 'now' ) > strtotime( '2025-03-15' ) ) {
			return;
		}

		foreach ( $this->shipping_services as $plugin_file => $service_name) {
			if ( is_plugin_active( $plugin_file ) ) {
				$this->display_ast_pro_notice( $service_name );
				break; // Show only one notice
			}
		}
	}

	/**
	 * Display AST PRO upgrade notice
	 */
	private function display_ast_pro_notice( $service_name ) {

		// Check if the date is past March 15, 2025
		if ( strtotime( 'now' ) > strtotime( '2025-03-15' ) ) {
			return;
		}
		
		$nonce = wp_create_nonce('ast_pro_dismiss_notice');
		$dismissable_url = esc_url(add_query_arg(['ast-pro-notice' => 'true', 'nonce' => $nonce]));

		echo '<style>
		.wp-core-ui .notice.ast-dismissable-notice {
			position: relative;
			padding-right: 38px;
			border-left-color: #005B9A;
		}
		.wp-core-ui .notice.ast-dismissable-notice h3 {
			margin-bottom: 5px;
		}
		.wp-core-ui .notice.ast-dismissable-notice a.notice-dismiss {
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
		</style>';
		
		echo '<div class="notice updated notice-success ast-dismissable-notice">  
			<h3><strong>🚀 Automate Your WooCommerce Order Fulfillment with AST PRO! 🎉</strong></h3>
			<p>You\'re using <strong>' . esc_html($service_name) . '</strong>, and with <strong>AST PRO</strong> you can streamline your order fulfillment process by automatically adding tracking information and marking orders as fulfilled—saving you time and effort.</p>
			<ul>
				<li>✅ Auto-add tracking details to orders</li>
				<li>✅ Mark orders as fulfilled instantly</li>
				<li>✅ Eliminate manual updates & reduce errors</li>
			</ul>
			<p>🔥 Limited-Time Offer: Use code <strong>ASTPRO30</strong> to save 30% on your upgrade!</p>
			<p>⏳ Hurry! Offer valid until <strong>March 15, 2025.</strong></p>
			<a href="https://www.zorem.com/product/woocommerce-advanced-shipment-tracking/" class="button button-primary ast_notice_btn">👉 Upgrade Now</a>
			<a class="button-primary ast_notice_btn" href="' . esc_url($dismissable_url) . '">Not interested</a>
		</div>';
	}

	/*
	* Dismiss admin notice for trackship
	*/
	public function ast_pro_shipping_integration_notice_ignore() {
		if ( isset( $_GET['ast-pro-notice'] ) ) {
			// if (isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'ast_pro_dismiss_notice')) {
			// 	update_option( 'ast_pro_shipping_integration_notice_ignore', 'true' );
			// }
			
			if (isset($_GET['nonce'])) {
				$nonce = sanitize_text_field($_GET['nonce']);
				if (wp_verify_nonce($nonce, 'ast_pro_dismiss_notice')) {
					update_option('ast_pro_shipping_integration_notice_ignore', 'true');
				}
			}
			
		}
	}

}
