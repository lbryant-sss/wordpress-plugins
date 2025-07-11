<?php
class WCML_Requests {

	public function __construct() {

		add_action( 'init', [ $this, 'run' ] );

	}

	public function run() {
		global $woocommerce_wpml;

		$nonce                 = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$settings_needs_update = false;

		if ( isset( $_GET['wcml_action'] ) ) {
			$settings_needs_update = true;

			if ( 'dismiss' === $_GET['wcml_action'] ) {
				$woocommerce_wpml->settings['dismiss_doc_main'] = 1;
			} elseif ( 'dismiss_tm_warning' === $_GET['wcml_action'] ) {
				$woocommerce_wpml->settings['dismiss_tm_warning'] = 1;
			} elseif ( 'dismiss_cart_warning' === $_GET['wcml_action'] ) {
				$woocommerce_wpml->settings['dismiss_cart_warning'] = 1;
			} else {
				$settings_needs_update = false;
			}
		}

		if ( isset( $_POST['wcml_save_settings'] ) && wp_verify_nonce( $nonce, 'wcml_save_settings_nonce' ) ) {
			global $sitepress,$sitepress_settings;

			if ( isset( $_POST['trnsl_interface'] ) ) {
				$woocommerce_wpml->settings['trnsl_interface'] = filter_input( INPUT_POST, 'trnsl_interface', FILTER_SANITIZE_NUMBER_INT );
			}

			$woocommerce_wpml->settings['products_sync_date']  = empty( $_POST['products_sync_date'] ) ? 0 : 1;
			$woocommerce_wpml->settings['products_sync_order'] = empty( $_POST['products_sync_order'] ) ? 0 : 1;

			$wcml_sync_media = empty( $_POST['sync_media'] ) ? 0 : 1;
			$woocommerce_wpml->update_setting( 'sync_media', $wcml_sync_media, true );

			$reviews_in_all_languages = ! empty( $_POST['reviews_in_all_languages'] );
			$woocommerce_wpml->update_setting( 'reviews_in_all_languages', $reviews_in_all_languages, true );

			$wcml_file_path_sync = filter_input( INPUT_POST, 'wcml_file_path_sync', FILTER_SANITIZE_NUMBER_INT );

			$woocommerce_wpml->settings[ \WCML_Downloadable_Products::SYNC_MODE_SETTING_KEY ] = $wcml_file_path_sync;

			if ( isset( $_POST['cart_sync_lang'] ) && isset( $_POST['cart_sync_currencies'] ) ) {
				$woocommerce_wpml->settings['cart_sync']['lang_switch']     = (int) filter_input( INPUT_POST, 'cart_sync_lang', FILTER_SANITIZE_NUMBER_INT );
				$woocommerce_wpml->settings['cart_sync']['currency_switch'] = (int) filter_input( INPUT_POST, 'cart_sync_currencies', FILTER_SANITIZE_NUMBER_INT );
			}

			$new_value = $wcml_file_path_sync == 0 ? 2 : $wcml_file_path_sync;
			$sitepress_settings['translation-management']['custom_fields_translation']['_downloadable_files'] = $new_value;
			$sitepress_settings['translation-management']['custom_fields_translation']['_file_paths']         = $new_value;

			$sitepress->save_settings( $sitepress_settings );

			$message = [
				'id'            => 'wcml-settings-saved',
				'text'          => __( 'Your settings have been saved.', 'woocommerce-multilingual' ),
				'group'         => 'wcml-settings',
				'admin_notice'  => true,
				'limit_to_page' => true,
				'classes'       => [ 'updated', 'notice', 'notice-success' ],
				'show_once'     => true,
			];
			ICL_AdminNotifier::add_message( $message );

			$settings_needs_update = true;
		}

		if ( $settings_needs_update ) {
			$woocommerce_wpml->update_settings();
		}

		add_action( 'wp_ajax_wcml_ignore_warning', [ $this, 'update_settings_from_warning' ] );

		// Override cached widget id.
		add_filter( 'woocommerce_cached_widget_id', [ $this, 'override_cached_widget_id' ] );
	}

	public function update_settings_from_warning() {
		$nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wcml_ignore_warning' ) ) {
			die( 'Invalid nonce' );
		}
		global $woocommerce_wpml;

		$woocommerce_wpml->settings[ $_POST['setting'] ] = 1;
		$woocommerce_wpml->update_settings();

	}

	public function override_cached_widget_id( $widget_id ) {

		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$widget_id .= ':' . ICL_LANGUAGE_CODE;
		}

		return $widget_id;
	}

}
