<?php

class WCML_wcExporter implements \IWPML_Action {

	/**
	 * @var SitePress
	 */
	private $sitepress;
	/**
	 * @var woocommerce_wpml
	 */
	private $woocommerce_wpml;

	/**
	 * WCML_wcExporter constructor.
	 *
	 * @param SitePress $sitepress
	 * @param woocommerce_wpml $woocommerce_wpml
	 */
	public function __construct( SitePress $sitepress, woocommerce_wpml $woocommerce_wpml ) {
		$this->sitepress        = $sitepress;
		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function add_hooks() {

		add_filter( 'woo_ce_product_fields', [ $this, 'woo_ce_fields' ] );
		add_filter( 'woo_ce_category_fields', [ $this, 'woo_ce_fields' ] );
		add_filter( 'woo_ce_tag_fields', [ $this, 'woo_ce_fields' ] );
		add_filter( 'woo_ce_order_fields', [ $this, 'woo_ce_order_fields' ] );
		add_filter( 'woo_ce_product_item', [ $this, 'woo_ce_product_item' ], 10, 2 );
		add_filter( 'woo_ce_category_item', [ $this, 'woo_ce_category_item' ], 10 );
		add_filter( 'woo_ce_tags', [ $this, 'woo_ce_tags' ], 10 );

	}

	public function woo_ce_fields( $fields ) {
		$fields[] = [
			'name'    => 'language',
			'label'   => __( 'Language', 'woo_ce' ),
			'default' => 1
		];
		$fields[] = [
			'name'    => 'translation_of',
			'label'   => __( 'Translation of', 'woo_ce' ),
			'default' => 1
		];

		return $fields;
	}

	public function woo_ce_order_fields( $fields ) {
		$fields[] = [
			'name'    => 'language',
			'label'   => __( 'Language', 'woo_ce' ),
			'default' => 1
		];

		return $fields;
	}

	public function woo_ce_product_item( $data, $product_id ) {

		$data->language       = $this->sitepress->get_language_for_element( $product_id, 'post_' . get_post_type( $product_id ) );
		$data->translation_of = $this->woocommerce_wpml->products->get_original_product_id( $product_id );

		return $data;
	}

	public function woo_ce_category_item( $data ) {

		$data->language       = $this->sitepress->get_language_for_element( $data->term_taxonomy_id, 'tax_product_cat' );
		$data->translation_of = apply_filters( 'wpml_object_id', $data->term_taxonomy_id, 'tax_product_cat', true, $this->sitepress->get_default_language() );

		return $data;
	}

	public function woo_ce_tags( $tags ) {

		foreach ( $tags as $key => $tag ) {
			$tags[ $key ]->language       = $this->sitepress->get_language_for_element( $tag->term_taxonomy_id, 'tax_product_tag' );
			$tags[ $key ]->translation_of = apply_filters( 'wpml_object_id', $tag->term_taxonomy_id, 'tax_product_tag', true, $this->sitepress->get_default_language() );
		}

		return $tags;
	}

}
