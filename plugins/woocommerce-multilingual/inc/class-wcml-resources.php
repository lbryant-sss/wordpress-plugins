<?php

use function WCML\functions\isStandAlone;
use WCML\Utilities\AdminPages;
use WCML\Utilities\AdminUrl;
use WPML\API\Sanitize;
use WPML\FP\Relation;

class WCML_Resources {

	/** @var string */
	private static $pagenow;

	/** @var woocommerce_wpml */
	private static $woocommerce_wpml;

	/** @var SitePress */
	private static $sitepress;

	public static function add_hooks() {
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'admin_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'front_scripts' ] );
	}

	/**
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param SitePress        $sitepress
	 */
	public static function set_up_resources( $woocommerce_wpml, $sitepress ) {
		global $pagenow;

		self::$woocommerce_wpml = $woocommerce_wpml;
		self::$sitepress        = $sitepress;
		self::$pagenow          = $pagenow;

		self::load_css();

		if ( isStandAlone() ) {
			return;
		}

		/** phpcs:disable WordPress.VIP.SuperGlobalInputUsage.AccessDetected */
		$is_edit_product     = 'post.php' === self::$pagenow && isset( $_GET['post'] ) && 'product' === get_post_type( (int) $_GET['post'] );
		$is_original_product = isset( $_GET['post'] ) && ! is_array( $_GET['post'] ) && self::$woocommerce_wpml->products->is_original_product( (int) $_GET['post'] );
		$is_new_product      = 'post-new.php' === self::$pagenow && isset( $_GET['source_lang'] ) && isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'];
		/** phpcs:enable WordPress.VIP.SuperGlobalInputUsage.AccessDetected */

		if ( self::$woocommerce_wpml->is_wpml_prior_4_2() ) {
			$is_using_native_editor = ! self::$woocommerce_wpml->settings['trnsl_interface'];
		} else {
			$tm_settings = $sitepress->get_setting( 'translation-management', [] );
			if ( $is_edit_product ) {
				$is_using_native_editor = WPML_TM_Post_Edit_TM_Editor_Mode::is_using_tm_editor( self::$sitepress, filter_var( $_GET['post'], FILTER_SANITIZE_NUMBER_INT ) );
			} else {
				$is_using_native_editor = isset( $tm_settings[ WPML_TM_Post_Edit_TM_Editor_Mode::TM_KEY_FOR_POST_TYPE_USE_NATIVE ]['product'] ) ? $tm_settings[ WPML_TM_Post_Edit_TM_Editor_Mode::TM_KEY_FOR_POST_TYPE_USE_NATIVE ]['product'] : false;

				if ( ! $is_using_native_editor ) {
					$is_using_native_editor = isset( $tm_settings[ WPML_TM_Post_Edit_TM_Editor_Mode::TM_KEY_GLOBAL_USE_NATIVE ] ) ? $tm_settings[ WPML_TM_Post_Edit_TM_Editor_Mode::TM_KEY_GLOBAL_USE_NATIVE ] : false;
				}
			}
		}

		if ( ( $is_edit_product && ! $is_original_product ) || $is_new_product && $is_using_native_editor ) {
			add_action( 'init', [ __CLASS__, 'load_lock_fields_js' ] );
			add_action( 'admin_footer', [ __CLASS__, 'hidden_label' ] );
		}
	}

	private static function load_css() {

		if ( AdminPages::isWcmlSettings() || AdminPages::isTranslationQueue() ) {

			self::load_management_css();

			if ( AdminPages::isMultiCurrency() || AdminPages::isTab( AdminUrl::TAB_STORE_URL ) ) {
				wp_register_style( 'wcml-dialogs', WCML_PLUGIN_URL . '/res/css/dialogs.css', [ 'wpml-dialog' ], WCML_VERSION );
				wp_enqueue_style( 'wcml-dialogs' );
			}

			wp_enqueue_style( 'wp-color-picker' );
		}

		if ( is_admin() ) {
			wp_register_style( 'wcml_admin', WCML_PLUGIN_URL . '/res/css/admin.css', [ 'wp-pointer' ], WCML_VERSION );
			wp_enqueue_style( 'wcml_admin' );
		}
	}

	public static function load_management_css() {
		wp_register_style( 'wpml-wcml', WCML_PLUGIN_URL . '/res/css/management.css', [], WCML_VERSION );
		wp_enqueue_style( 'wpml-wcml' );
	}

	public static function load_taxonomy_translation_scripts() {
		wp_register_script( 'wcml-taxonomy-translation-scripts', WCML_PLUGIN_URL . '/res/js/taxonomy_translation' . WCML_JS_MIN . '.js', [ 'jquery' ], WCML_VERSION, true );
		wp_enqueue_script( 'wcml-taxonomy-translation-scripts' );
	}

	public static function admin_scripts() {

		if ( AdminPages::isWcmlSettings() ) {

			wp_register_script( 'wcml-scripts', WCML_PLUGIN_URL . '/res/js/scripts' . WCML_JS_MIN . '.js', [ 'jquery', 'jquery-ui-core', 'jquery-ui-resizable' ], WCML_VERSION, true );

			self::load_taxonomy_translation_scripts();

			wp_register_script( 'wcml-dialogs', WCML_PLUGIN_URL . '/res/js/dialogs' . WCML_JS_MIN . '.js', [ 'jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'underscore' ], WCML_VERSION, true );
			wp_register_script( 'wcml-troubleshooting', WCML_PLUGIN_URL . '/res/js/troubleshooting' . WCML_JS_MIN . '.js', [ 'jquery' ], WCML_VERSION, true );

			if ( ! isStandalone() && self::$woocommerce_wpml->is_wpml_prior_4_2() ) {
				wp_register_script( 'wcml-translation-interface-dialog-warning', WCML_PLUGIN_URL . '/res/js/trnsl_interface_dialog_warning' . WCML_JS_MIN . '.js', [ 'jquery' ], WCML_VERSION, true );
				wp_enqueue_script( 'wcml-translation-interface-dialog-warning' );
			}

			wp_enqueue_script( 'wcml-scripts' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'wcml-dialogs' );
			wp_enqueue_script( 'wcml-troubleshooting' );

			wp_localize_script(
				'wcml-scripts',
				'wcml_settings',
				[
					'nonce' => wp_create_nonce( 'woocommerce_multilingual' ),
				]
			);

			self::load_tooltip_resources();
		}

		if ( AdminPages::isTranslationsDashboard() ) {
			wp_register_script( 'wpml_tm', WCML_PLUGIN_URL . '/res/js/wpml_tm' . WCML_JS_MIN . '.js', [ 'jquery' ], WCML_VERSION, true );
			wp_enqueue_script( 'wpml_tm' );
		}

		if ( self::$pagenow == 'widgets.php' ) {
			wp_register_script( 'wcml_widgets', WCML_PLUGIN_URL . '/res/js/widgets' . WCML_JS_MIN . '.js', [ 'jquery' ], WCML_VERSION, true );
			wp_enqueue_script( 'wcml_widgets' );
		}

		if ( AdminPages::isMultiCurrency() ) {
			wp_register_script( 'multi-currency', WCML_PLUGIN_URL . '/res/js/multi-currency' . WCML_JS_MIN . '.js', [ 'jquery', 'jquery-ui-sortable' ], WCML_VERSION, true );
			wp_enqueue_script( 'multi-currency' );

			wp_register_script( 'currency-switcher-settings', WCML_PLUGIN_URL . '/res/js/currency-switcher-settings' . WCML_JS_MIN . '.js', [ 'jquery', 'jquery-ui-sortable', 'underscore' ], WCML_VERSION, true );
			wp_enqueue_script( 'currency-switcher-settings' );
			wp_localize_script(
				'currency-switcher-settings',
				'settings',
				[
					'pre_selected_colors' => WCML_Currency_Switcher_Options_Dialog::currency_switcher_pre_selected_colors(),
				]
			);

			wp_register_script( 'exchange-rates', WCML_PLUGIN_URL . '/res/js/exchange-rates' . WCML_JS_MIN . '.js', [ 'jquery' ], WCML_VERSION, true );
			wp_enqueue_script( 'exchange-rates' );
		}

		wp_enqueue_script(
			'wcml-pointer',
			WCML_PLUGIN_URL . '/res/js/pointer' . WCML_JS_MIN . '.js',
			[ 'wp-pointer' ],
			WCML_VERSION,
			true
		);

		wp_register_script( 'wcml-messages', WCML_PLUGIN_URL . '/res/js/wcml-messages' . WCML_JS_MIN . '.js', [ 'jquery' ], WCML_VERSION, true );
		wp_enqueue_script( 'wcml-messages' );

		$is_attr_page = apply_filters( 'wcml_is_attributes_page', AdminPages::isPage( 'product_attributes' ) && Relation::propEq( 'post_type', 'product', $_GET ) );

		if ( $is_attr_page ) {
			wp_register_script( 'wcml-attributes', WCML_PLUGIN_URL . '/res/js/wcml-attributes' . WCML_JS_MIN . '.js', [ 'jquery' ], WCML_VERSION, true );
			wp_enqueue_script( 'wcml-attributes' );
		}

		if ( AdminPages::isTranslationQueue() ) {

			self::load_tooltip_resources();
			wp_enqueue_media();
			wp_register_script( 'wcml-editor', WCML_PLUGIN_URL . '/res/js/wcml-translation-editor' . WCML_JS_MIN . '.js', [ 'jquery', 'jquery-ui-core' ], WCML_VERSION, true );
			wp_enqueue_script( 'wcml-editor' );
			wp_localize_script(
				'wcml-editor',
				'wcml_settings',
				[
					'strings'     => [
						'choose'         => __( 'Choose a file', 'woocommerce-multilingual' ),
						'save_tooltip'   => __( 'At least one of these fields is required: title, content or excerpt', 'woocommerce-multilingual' ),
						'resign_tooltip' => __( 'This translation job will no longer be assigned to you. Other translators will be able take it and continue the translation.', 'woocommerce-multilingual' ),
					],
					'hide_resign' => self::$woocommerce_wpml->products->is_hide_resign_button(),
				]
			);
		}

		if ( isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'] && 'edit.php' === self::$pagenow ) {
			self::load_tooltip_resources();
			wp_enqueue_script( 'products-screen-options', WCML_PLUGIN_URL . '/res/js/products-screen-option.js', [ 'jquery', 'wcml-tooltip-init' ], WCML_VERSION, true );
			wp_localize_script( 'products-screen-options', 'products_screen_option', [ 'nonce' => wp_create_nonce( 'products-screen-option-action' ) ] );
		}
	}

	public static function front_scripts() {

		if ( self::$pagenow !== 'wp-login.php' ) {

			$referer = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';

			wcml_register_script( 'cart-widget', 'res/js/cart_widget' . WCML_JS_MIN . '.js', [], [ 'strategy' => 'defer', 'in_footer' => true ] );
			wp_enqueue_script( 'cart-widget' );
			wp_localize_script(
				'cart-widget',
				'actions',
				[
					'is_lang_switched' => self::$sitepress->get_language_from_url( $referer ) != self::$sitepress->get_current_language() ? 1 : 0,
					'force_reset'      => apply_filters( 'wcml_force_reset_cart_fragments', 0 ),
				]
			);
		}

	}

	public static function load_tooltip_resources() {

		if ( class_exists( 'WooCommerce' ) && function_exists( 'WC' ) ) {
			wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', [ 'jquery' ], WC_VERSION, true );
			wp_register_script( 'wcml-tooltip-init', WCML_PLUGIN_URL . '/res/js/tooltip_init' . WCML_JS_MIN . '.js', [ 'jquery' ], WCML_VERSION, true );
			wp_enqueue_script( 'jquery-tiptip' );
			wp_enqueue_script( 'wcml-tooltip-init' );
			wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', [], WC_VERSION );
		}

	}

	public static function load_lock_fields_js() {
		global $pagenow;

		wp_register_script( 'wcml-lock-script', WCML_PLUGIN_URL . '/res/js/lock_fields' . WCML_JS_MIN . '.js', [ 'jquery' ], WCML_VERSION, true );
		wp_enqueue_script( 'wcml-lock-script' );

		$product_id = false;
		if ( $pagenow === 'post.php' && isset( $_GET['post'] ) ) {
			$product_id = $_GET['post'];
		} elseif ( isset( $_POST['product_id'] ) ) {
			$product_id = $_POST['product_id'];
		}

		$file_path_sync = WCML_Downloadable_Products::isDownloadableFilesSetToUseSame( $product_id );

		wp_localize_script(
			'wcml-lock-script',
			'unlock_fields',
			[
				'menu_order' => self::$woocommerce_wpml->settings['products_sync_order'],
				'file_paths' => $file_path_sync,
			]
		);
		wp_localize_script(
			'wcml-lock-script',
			'non_standard_fields',
			[
				'ids'         => apply_filters( 'wcml_js_lock_fields_ids', [] ),
				'classes'     => apply_filters( 'wcml_js_lock_fields_classes', [] ),
				'input_names' => apply_filters( 'wcml_js_lock_fields_input_names', [] ),
			]
		);

		do_action( 'wcml_after_load_lock_fields_js' );

	}

	public static function hidden_label() {
		global $sitepress;

		echo '<img src="' . WCML_PLUGIN_URL . '/res/images/locked.png" class="wcml_lock_img wcml_lock_icon" alt="' .
			__( 'This field is locked for editing because WPML will copy its value from the original language.', 'woocommerce-multilingual' ) .
			'" title="' . __( 'This field is locked for editing because WPML will copy its value from the original language.', 'woocommerce-multilingual' ) .
			'" style="display: none;position:relative;left:2px;top:2px;">';

		if ( isset( $_GET['post'] ) ) {
			$original_id = self::$woocommerce_wpml->products->get_original_product_id( sanitize_text_field( $_GET['post'] ) );
		} elseif ( isset( $_GET['trid'] ) ) {
			$original_id = $sitepress->get_original_element_id_by_trid( sanitize_text_field( $_GET['trid'] ) );
		}

		if ( ! isset( $_GET['lang'], $original_id ) ) {
			return;
		}

		$language = Sanitize::stringProp( 'lang', $_GET );

		echo '<h3 class="wcml_prod_hidden_notice">' .
			sprintf(
				/* translators: %1$s is the post title inside a HTML link pointing to the post edit screen, %2$s and %3$s are opening and closing HTML link tags */
				esc_html__(
					"This is a translation of %1\$s. Some of the fields are not editable. It's recommended that you translate products from the %2\$sTranslation Dashboard%3\$s.",
					'woocommerce-multilingual'
				),
				'<a href="' . esc_url( get_edit_post_link( $original_id ) ) . '" >' . get_the_title( $original_id ) . '</a>',
				'<a href="' . esc_url( AdminUrl::getWPMLTMDashboardProducts() ) . '">',
				'</a>'
			) . '</h3>';
	}
}
