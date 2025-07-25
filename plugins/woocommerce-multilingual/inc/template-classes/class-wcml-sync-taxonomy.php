<?php

use WCML\Utilities\WCTaxonomies;

class WCML_Sync_Taxonomy extends WCML_Templates_Factory {

	private $woocommerce_wpml;
	private $taxonomy;
	private $taxonomy_obj;

	/**
	 * WCML_Sync_Taxonomy constructor.
	 *
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param string           $taxonomy
	 * @param WP_Taxonomy      $taxonomy_obj
	 */
	public function __construct( $woocommerce_wpml, $taxonomy, $taxonomy_obj ) {
		// @todo Cover by tests, required for wcml-3037.
		parent::__construct();

		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->taxonomy         = $taxonomy;
		$this->taxonomy_obj     = $taxonomy_obj;
	}

	public function get_model() {

		$wcml_settings        = $this->woocommerce_wpml->get_settings();
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		foreach ( $attribute_taxonomies as $a ) {
			$attribute_taxonomies_arr[] = WCTaxonomies::TAXONOMY_PREFIX_ATTRIBUTE . $a->attribute_name;
		}

		$model = [
			'taxonomy'             => $this->taxonomy,
			'attribute_taxonomies' => isset( $attribute_taxonomies_arr ) && in_array( $this->taxonomy, $attribute_taxonomies_arr ),
			'display_attr'         => isset( $wcml_settings['sync_variations'] ) && $wcml_settings['sync_variations'] ? '' : 'display: none',
			'display_tax'          => ( isset( $wcml_settings[ 'sync_' . $this->taxonomy ] ) && $wcml_settings[ 'sync_' . $this->taxonomy ] ) ? '' : 'display: none',
			'loader_url'           => \WCML\functions\assetLink( '/res/img/ajax-loader.gif' ),
			'vars_to_create'       => isset( $wcml_settings['variations_needed'][ $this->taxonomy ] ) ? $wcml_settings['variations_needed'][ $this->taxonomy ] : false,
			'tax_name'             => $this->taxonomy_obj->labels->name,
			'tax_singular_name'    => '<i>' . $this->taxonomy_obj->labels->singular_name . '</i>',
			'strings'              => [
				'sync_update'       => __( 'Synchronize attributes and update product variations', 'woocommerce-multilingual' ),
				'auto_generate'     => __( 'This will automatically generate variations for translated products corresponding to recently translated attributes.', 'woocommerce-multilingual' ),
				/* translators: %s is a number of product variations */
				'vars_to_create'    => __( 'Currently, there are %s variations that need to be created.', 'woocommerce-multilingual' ),
				/* translators: %s is a taxonomy name */
				'sync_in_cont'      => __( 'Synchronize %s assignment in content', 'woocommerce-multilingual' ),
				/* translators: %s is a taxonomy name */
				'auto_apply'        => __( 'This action lets you automatically apply the %s taxonomy to your content in different languages. It will scan the original content and apply the same taxonomy to translated content.', 'woocommerce-multilingual' ),
				'untranslated_warn' => __( 'You have untranslated terms!', 'woocommerce-multilingual' ),
			],
			'nonces'               => [
				'sync_product_variations' => wp_nonce_field( 'wcml_sync_product_variations', 'wcml_nonce', $referrer = false, $echo = false ),
				'sync_taxonomies'         => wp_nonce_field( 'wcml_sync_taxonomies_in_content_preview', 'wcml_sync_taxonomies_in_content_preview_nonce', $referrer = false, $echo = false ),
			],
		];

		return $model;

	}

	protected function init_template_base_dir() {
		$this->template_paths = [
			WCML_PLUGIN_PATH . '/templates/',
		];
	}

	public function get_template() {
		return 'sync-taxonomy.twig';
	}

}
