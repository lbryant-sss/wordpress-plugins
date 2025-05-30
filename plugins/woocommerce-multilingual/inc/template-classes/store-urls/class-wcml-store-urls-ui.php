<?php

/**
 * Created by OnTheGo Systems
 */
class WCML_Store_URLs_UI extends WCML_Templates_Factory {

	private $woocommerce_wpml;
	private $sitepress;
	private $active_languages;

	/**
	 * WCML_Store_URLs_UI constructor.
	 *
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param SitePress        $sitepress
	 */
	public function __construct( $woocommerce_wpml, $sitepress ) {
		parent::__construct();

		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress        = $sitepress;
		$this->active_languages = $this->sitepress->get_active_languages();
	}

	public function get_model() {

		$model = [
			'data'            => [
				'flags' => $this->woocommerce_wpml->products->get_translation_flags( $this->active_languages, false, false ),
			],
			'shop_base'       => [
				'flag'       => $this->sitepress->get_flag_url( $this->woocommerce_wpml->url_translation->get_source_slug_language( 'shop' ) ),
				'orig_value' => urldecode( get_post( wc_get_page_id( 'shop' ) )->post_name ),
				'statuses'   => $this->get_base_translations_statuses( 'shop', $this->active_languages ),
			],
			'product_base'    => [
				'flag'       => $this->sitepress->get_flag_url( $this->woocommerce_wpml->url_translation->get_source_slug_language( 'product' ) ),
				'orig_value' => $this->woocommerce_wpml->url_translation->get_woocommerce_product_base(),
				'statuses'   => $this->get_base_translations_statuses( 'product', $this->active_languages ),
			],
			'cat_base'        => [
				'flag'       => $this->sitepress->get_flag_url( $this->woocommerce_wpml->url_translation->get_source_slug_language( 'product_cat' ) ),
				'orig_value' => ! empty( $this->woocommerce_wpml->url_translation->wc_permalinks['category_base'] ) ? trim( $this->woocommerce_wpml->url_translation->wc_permalinks['category_base'], '/' ) : 'product-category',
				'statuses'   => $this->get_base_translations_statuses( 'product_cat', $this->active_languages ),
			],
			'tag_base'        => [
				'flag'       => $this->sitepress->get_flag_url( $this->woocommerce_wpml->url_translation->get_source_slug_language( 'product_tag' ) ),
				'orig_value' => ! empty( $this->woocommerce_wpml->url_translation->wc_permalinks['tag_base'] ) ? trim( $this->woocommerce_wpml->url_translation->wc_permalinks['tag_base'], '/' ) : 'product-tag',
				'statuses'   => $this->get_base_translations_statuses( 'product_tag', $this->active_languages ),
			],
			'attr_base'       => [
				'flag'       => $this->sitepress->get_flag_url( $this->woocommerce_wpml->url_translation->get_source_slug_language( 'attribute' ) ),
				'orig_value' => trim( $this->woocommerce_wpml->url_translation->wc_permalinks['attribute_base'], '/' ),
				'statuses'   => $this->get_base_translations_statuses( 'attribute', $this->active_languages, $this->woocommerce_wpml->url_translation->wc_permalinks['attribute_base'] ),
			],
			'endpoints_base'  => $this->get_endpoint_info(),
			'attribute_slugs' => $this->get_attribute_slugs_info(),
			'strings'         => [
				'notice'           => __( 'This page allows you to translate all strings that are being used by WooCommerce in building different type of urls. Translating them enables you to have fully localized urls that match the language of the pages.', 'woocommerce-multilingual' ),
				'notice_defaults'  => sprintf(
					/* translators: %1$s/%2$s and %3$s/%4$s are opening and closing HTML link tags */
					__(
						'You can enter or edit your default values on the %1$sPermalinks settings%2$s page or, for the endpoints, on the WooCommerce %3$sAdvanced settings%4$s page.',
						'woocommerce-multilingual'
					),
					'<a href="' . admin_url( 'options-permalink.php' ) . '">',
					'</a>',
					'<a href="' . \WCML\Utilities\AdminUrl::getWooSettings( 'advanced' ) . '">',
					'</a>'
				),
				'perm_settings'    => '<a href="' . admin_url( 'options-permalink.php' ) . '" >' . __( 'permalinks settings', 'woocommerce-multilingual' ) . '</a>',
				'account_settings' => '<a href="' . \WCML\Utilities\AdminUrl::getWooSettings( 'account' ) . '" >' . __( 'Account settings', 'woocommerce-multilingual' ) . '</a>',
				'slug_type'        => __( 'Slug type', 'woocommerce-multilingual' ),
				'orig_slug'        => __( 'Original Slug', 'woocommerce-multilingual' ),
				'shop'             => __( 'Shop page', 'woocommerce-multilingual' ),
				'product'          => __( 'Product base', 'woocommerce-multilingual' ),
				'category'         => __( 'Product category base', 'woocommerce-multilingual' ),
				'tag'              => __( 'Product tag base', 'woocommerce-multilingual' ),
				'attr'             => __( 'Product attribute base', 'woocommerce-multilingual' ),
				/* translators: %s is a slug */
				'endpoint'         => __( 'Endpoint: %s', 'woocommerce-multilingual' ),
				/* translators: %s is a slug */
				'attribute_slug'   => __( 'Attribute slug: %s', 'woocommerce-multilingual' ),
			],
			'nonces'          => [
				'edit_base'   => wp_nonce_field( 'wcml_edit_base', 'wcml_edit_base_nonce', true, false ),
				'update_base' => wp_nonce_field( 'wcml_update_base_translation', 'wcml_update_base_nonce', true, false ),
			],
		];

		return $model;
	}

	public function get_endpoint_info() {

		$is_original_slug    = function( $endpoint ) {
			return (bool) apply_filters( 'wpml_get_string_language', '', WCML_Endpoints::STRING_CONTEXT, $endpoint );
		};
		$filtered_query_vars = wpml_collect( WC()->query->query_vars )->filter( $is_original_slug )->toArray();

		$endpoints_info = [];
		foreach ( $filtered_query_vars as $key => $endpoint ) {
			if ( class_exists( 'WPML_Endpoints_Support' ) ) {
				$key = $endpoint;
			}

			$endpoints_info[ $key ]['key']        = $key;
			$endpoints_info[ $key ]['orig_value'] = $endpoint;
			$endpoints_info[ $key ]['flag']       = $this->sitepress->get_flag_url( $this->woocommerce_wpml->url_translation->get_source_slug_language( $key ) );
			$endpoints_info[ $key ]['statuses']   = $this->get_base_translations_statuses( $key, $this->active_languages, $endpoint );

		}

		return $endpoints_info;

	}

	private function get_attribute_slugs_info() {
		$product_attributes = $this->woocommerce_wpml->attributes->get_translatable_attributes();

		$attributes_info = [];
		foreach ( $product_attributes as $attribute ) {

			if ( $attribute->attribute_public ) {
				$language = $this->woocommerce_wpml->strings->get_string_language(
					$attribute->attribute_name,
					$this->woocommerce_wpml->url_translation->url_strings_context(),
					$this->woocommerce_wpml->url_translation->url_string_name( 'attribute_slug', $attribute->attribute_name )
				);

				// $this->woocommerce_wpml->url_translation
				$attributes_info[ $attribute->attribute_name ] = [
					'label'      => $attribute->attribute_label,
					'orig_value' => $attribute->attribute_name,
					'flag'       => $this->sitepress->get_flag_url( $language ),
					'statuses'   => $this->get_base_translations_statuses( 'attribute_slug-' . $attribute->attribute_name, $this->active_languages, $attribute->attribute_name ),
				];

			}
		}

		return $attributes_info;
	}

	public function get_base_translations_statuses( $base, $active_languages, $value = true ) {

		$statuses = new WCML_Store_URLs_Translation_Statuses_UI( $base, $active_languages, $value, $this->woocommerce_wpml, $this->sitepress );

		return $statuses->get_view();

	}

	public function init_template_base_dir() {
		$this->template_paths = [
			WCML_PLUGIN_PATH . '/templates/store-urls/',
		];
	}

	public function get_template() {
		return 'store-urls.twig';
	}
}
