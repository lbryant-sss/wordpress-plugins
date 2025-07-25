<?php

use WCML\Utilities\WpAdminPages;
use WPML\API\Sanitize;

/**
 * Class WCML_Admin_Currency_Selector
 */
class WCML_Admin_Currency_Selector {

	/** @var woocommerce_wpml */
	private $woocommerce_wpml;
	/** @var  WCML_Admin_Cookie */
	private $currency_cookie;

	const NONCE_KEY = 'wcml-admin-currency-selector';

	/**
	 * WCML_Admin_Currency_Selector constructor.
	 *
	 * @param woocommerce_wpml  $woocommerce_wpml
	 * @param WCML_Admin_Cookie $currency_cookie
	 */
	public function __construct( woocommerce_wpml $woocommerce_wpml, WCML_Admin_Cookie $currency_cookie ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->currency_cookie  = $currency_cookie;
	}

	public function add_hooks() {
		if ( is_admin() ) {

			if ( $this->user_can_manage_woocommerce() ) {
				add_action( 'init', [ $this, 'set_dashboard_currency' ] );
				add_action( 'wp_ajax_wcml_dashboard_set_currency', [ $this, 'set_dashboard_currency_ajax' ] );
				add_filter( 'woocommerce_currency_symbol', [ $this, 'filter_dashboard_currency_symbol' ] );
			}

			add_action(
				'woocommerce_after_dashboard_status_widget',
				[
					$this,
					'show_dashboard_currency_selector',
				]
			);
			add_action( 'admin_enqueue_scripts', [ $this, 'load_js' ] );
		}
	}

	/**
	 * @return bool
	 */
	private function user_can_manage_woocommerce() {
		return current_user_can( 'view_woocommerce_reports' ) ||
			   current_user_can( 'manage_woocommerce' ) ||
			   current_user_can( 'publish_shop_orders' );

	}

	public function load_js() {
		wp_enqueue_script(
			'wcml-admin-currency-selector',
			$this->woocommerce_wpml->plugin_url() .
			'/res/js/admin-currency-selector' . $this->woocommerce_wpml->js_min_suffix() . '.js',
			[ 'jquery' ],
			$this->woocommerce_wpml->version(),
			true
		);
		wp_localize_script(
			'wcml-admin-currency-selector',
			'wcml_admin_currency_selector',
			[
				'nonce' => wp_create_nonce( self::NONCE_KEY ),
			]
		);
	}

	/**
	 * Add currency drop-down on dashboard page ( WooCommerce status block )
	 */
	public function show_dashboard_currency_selector() {

		$current_dashboard_currency = $this->get_cookie_dashboard_currency();

		$wc_currencies  = get_woocommerce_currencies();
		$currency_codes = $this->woocommerce_wpml->multi_currency->get_currency_codes();
		?>
		<select id="dropdown_dashboard_currency" style="display: none; margin : 10px; ">
			<?php if ( empty( $currency_codes ) ) : ?>
				<option value=""><?php _e( 'Currency - no orders found', 'woocommerce-multilingual' ); ?></option>
			<?php else : ?>
				<?php foreach ( $currency_codes as $currency ) : ?>

					<option value="<?php echo esc_html( $currency ); ?>" <?php echo esc_html( $current_dashboard_currency === $currency ? 'selected="selected"' : '' ); ?>>
						<?php echo esc_html( $wc_currencies[ $currency ] ); ?>
					</option>

				<?php endforeach; ?>
			<?php endif; ?>
		</select>
		<?php
	}

	public function set_dashboard_currency_ajax() {
		$nonce = sanitize_text_field( $_POST['wcml_nonce'] );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, self::NONCE_KEY ) ) {
			wp_send_json_error( __( 'Invalid nonce', 'woocommerce-multilingual' ), 403 );
		} else {
			$this->set_dashboard_currency( sanitize_text_field( $_POST['currency'] ) );
			wp_send_json_success();
		}
	}

	/**
	 * Set dashboard currency cookie
	 *
	 * @param string $currency_code
	 */
	public function set_dashboard_currency( $currency_code = '' ) {
		global $pagenow;

		if ( ! $currency_code && 'index.php' === $pagenow && ! headers_sent() ) {
			$currency_code = $this->get_cookie_dashboard_currency();
		}

		if ( $currency_code ) {
			$this->currency_cookie->set_value( $currency_code, time() + DAY_IN_SECONDS );
		}
	}

	/**
	 * Get dashboard currency cookie
	 *
	 * @return string
	 */
	public function get_cookie_dashboard_currency() {

		$currency = $this->currency_cookie->get_value();
		if ( null === $currency ) {
			$currency = wcml_get_woocommerce_currency_option();
		}

		return $currency;
	}

	/**
	 * Filter currency symbol on dashboard page
	 *
	 * @param string $currencySymbol Currency symbol
	 *
	 * @return string
	 */
	public function filter_dashboard_currency_symbol( $currencySymbol ) {
		if (
			( WpAdminPages::isDashboard() && empty( $_REQUEST['action'] ) )
			|| self::isDashboardWidgetRequest()
		) {
			remove_filter( 'woocommerce_currency_symbol', [ $this, 'filter_dashboard_currency_symbol' ] );
			$currencySymbol = get_woocommerce_currency_symbol( $this->get_cookie_dashboard_currency() );
			add_filter( 'woocommerce_currency_symbol', [ $this, 'filter_dashboard_currency_symbol' ] );
		}

		return $currencySymbol;
	}

	/**
	 * @return bool
	 */
	public static function isDashboardWidgetRequest() {
		return wp_doing_ajax()
				&& 'woocommerce_load_status_widget' === Sanitize::stringProp( 'action', $_GET );
	}
}
