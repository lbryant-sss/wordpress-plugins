<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * Starter Register Demos
 */
function botiga_demos_list() {

	$plugins = array();

	$plugins[] = array(
		'name'     => 'WooCommerce',
		'slug'     => 'woocommerce',
		'path'     => 'woocommerce/woocommerce.php',
		'required' => true
	);

	$plugins[] = array(
		'name'     => 'Merchant',
		'slug'     => 'merchant',
		'path'     => 'merchant/merchant.php',
		'required' => false
	);

	$demos = array(
		'beauty'      => array(
			'name'       => esc_html__( 'Beauty', 'botiga' ),
			'type'       => 'free',
			'categories' => array( 'ecommerce' ),
			'builders'   => array(
				'gutenberg',
				'elementor',
			),
			'preview'    => 'https://demo.athemes.com/botiga/',
			'thumbnail'  => 'https://athemes.com/themes-demo-content/botiga/beauty/thumb.png',
			'plugins'    => array_merge(
				$plugins,
				array(
					array(
						'name'     => 'WPForms',
						'slug'     => 'wpforms-lite',
						'path'     => 'wpforms-lite/wpforms.php',
						'required' => false
					)
				),
			),
			'import'         => array(
				'gutenberg'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/beauty/botiga-dc-beauty.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/beauty/botiga-w-beauty.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/beauty/botiga-c-beauty.dat'
				),
				'elementor'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/elementor/beauty/botiga-dc-beauty-el.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/elementor/beauty/botiga-w-beauty-el.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/elementor/beauty/botiga-c-beauty-el.dat'
				),
			),
		),
		'apparel'   => array(
			'name'       => esc_html__( 'Apparel', 'athemes-starter-sites' ),
			'type'       => 'pro',
			'categories' => array( 'ecommerce' ),
			'builders'   => array(
				'gutenberg',
				'elementor',
			),
			'preview'    => 'https://demo.athemes.com/botiga-apparel/',
			'thumbnail'  => 'https://athemes.com/themes-demo-content/botiga/apparel/thumb.png',
			'plugins'    => array_merge(
				$plugins,
				array(
					array(
						'name'     => 'WPForms',
						'slug'     => 'wpforms-lite',
						'path'     => 'wpforms-lite/wpforms.php',
						'required' => false
					)
				),
			),
			'import'         => array(
				'gutenberg'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/apparel/botiga-dc-apparel.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/apparel/botiga-w-apparel.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/apparel/botiga-c-apparel.dat'
				),
				'elementor'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/elementor/apparel/botiga-dc-apparel-el.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/elementor/apparel/botiga-w-apparel-el.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/elementor/apparel/botiga-c-apparel-el.dat'
				),
			),
		),
		'furniture'   => array(
			'name'       => esc_html__( 'Furniture', 'athemes-starter-sites' ),
			'type'       => 'pro',
			'categories' => array( 'ecommerce' ),
			'builders'   => array(
				'gutenberg',
				'elementor',
			),
			'preview'    => 'https://demo.athemes.com/botiga-furniture/',
			'thumbnail'  => 'https://athemes.com/themes-demo-content/botiga/furniture/thumb.png',
			'plugins'    => array_merge(
				$plugins,
				array(
					array(
						'name'     => 'WPForms',
						'slug'     => 'wpforms-lite',
						'path'     => 'wpforms-lite/wpforms.php',
						'required' => false
					)					
				),
			),
			'import'         => array(
				'gutenberg'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/furniture/botiga-dc-furniture.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/furniture/botiga-w-furniture.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/furniture/botiga-c-furniture.dat'
				),
				'elementor'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/elementor/furniture/botiga-dc-furniture-el.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/elementor/furniture/botiga-w-furniture-el.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/elementor/furniture/botiga-c-furniture-el.dat'
				),
			),
		),
		'jewelry'   => array(
			'name'       => esc_html__( 'Jewelry', 'athemes-starter-sites' ),
			'type'       => 'pro',
			'categories' => array( 'ecommerce' ),
			'builders'   => array(
				'gutenberg',
				'elementor',
			),
			'preview'    => 'https://demo.athemes.com/botiga-jewelry/',
			'thumbnail'  => 'https://athemes.com/themes-demo-content/botiga/jewelry/thumb.png',
			'plugins'    => array_merge(
				$plugins,
				array(
					array(
						'name'     => 'WPForms',
						'slug'     => 'wpforms-lite',
						'path'     => 'wpforms-lite/wpforms.php',
						'required' => false
					)					
				),
			),
			'import'         => array(
				'gutenberg'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/jewelry/botiga-dc-jewelry.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/jewelry/botiga-w-jewelry.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/jewelry/botiga-c-jewelry.dat'
				),
				'elementor'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/elementor/jewelry/botiga-dc-jewelry-el.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/elementor/jewelry/botiga-w-jewelry-el.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/elementor/jewelry/botiga-c-jewelry-el.dat'
				),
			),
		),
		'single-product'   => array(
			'name'       => esc_html__( 'Single Product', 'athemes-starter-sites' ),
			'type'       => 'pro',
			'categories' => array( 'ecommerce' ),
			'builders'   => array(
				'gutenberg',
				'elementor',
			),
			'preview'    => 'https://demo.athemes.com/botiga-single-product/',
			'thumbnail'  => 'https://athemes.com/themes-demo-content/botiga/single-product/thumb.png',
			'plugins'    => array_merge(
				$plugins,
				array(
					array(
						'name'     => 'WPForms',
						'slug'     => 'wpforms-lite',
						'path'     => 'wpforms-lite/wpforms.php',
						'required' => false
					)					
				),
			),
			'import'         => array(
				'gutenberg'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/single-product/botiga-dc-single-product.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/single-product/botiga-w-single-product.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/single-product/botiga-c-single-product.dat'
				),
				'elementor'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/elementor/single-product/botiga-dc-single-product-el.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/elementor/single-product/botiga-w-single-product-el.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/elementor/single-product/botiga-c-single-product-el.dat'
				),
			),
		),
		'multi-vendor' => array(
			'name'       => esc_html__( 'Multi Vendor', 'athemes-starter-sites' ),
			'type'       => 'pro',
			'categories' => array( 'ecommerce' ),
			'builders'   => array(
				'gutenberg',
				'elementor',
			),
			'preview'    => 'https://demo.athemes.com/botiga-multi-vendor/',
			'thumbnail'  => 'https://athemes.com/themes-demo-content/botiga/multi-vendor/thumb.png',
			'plugins'    => array_merge(
				$plugins,
				array(
					array(
						'name'     => 'WPForms',
						'slug'     => 'wpforms-lite',
						'path'     => 'wpforms-lite/wpforms.php',
						'required' => false
					),
					array(
						'name'     => 'Dokan',
						'slug'     => 'dokan-lite',
						'path'     => 'dokan-lite/dokan.php',
						'required' => false
					)
				),
			),
			'import'         => array(
				'gutenberg'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/multi-vendor/botiga-dc-multi-vendor.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/multi-vendor/botiga-w-multi-vendor.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/multi-vendor/botiga-c-multi-vendor.dat'
				),
				'elementor'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/elementor/multi-vendor/botiga-dc-multi-vendor-el.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/elementor/multi-vendor/botiga-w-multi-vendor-el.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/elementor/multi-vendor/botiga-c-multi-vendor-el.dat'
				),
			),
		),
		'wine' => array(
			'name'       => esc_html__( 'Wine', 'athemes-starter-sites' ),
			'type'       => 'pro',
			'categories' => array( 'ecommerce' ),
			'builders'   => array(
				'gutenberg',
				'elementor',
			),
			'preview'    => 'https://demo.athemes.com/botiga-wine/',
			'thumbnail'  => 'https://athemes.com/themes-demo-content/botiga/wine/thumb.png',
			'plugins'    => array_merge(
				$plugins,
				array(
					array(
						'name'     => 'WPForms',
						'slug'     => 'wpforms-lite',
						'path'     => 'wpforms-lite/wpforms.php',
						'required' => false
					),
				),
			),
			'import'         => array(
				'gutenberg'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/wine/botiga-dc-wine.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/wine/botiga-w-wine.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/wine/botiga-c-wine.dat'
				),
				'elementor'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/elementor/wine/botiga-dc-wine-el.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/elementor/wine/botiga-w-wine-el.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/elementor/wine/botiga-c-wine-el.dat'
				),
			),
		),
		'plants' => array(
			'name'       => esc_html__( 'Plants', 'athemes-starter-sites' ),
			'type'       => 'pro',
			'categories' => array( 'ecommerce' ),
			'builders'   => array(
				'gutenberg',
				'elementor',
			),
			'preview'    => 'https://demo.athemes.com/botiga-plants/',
			'thumbnail'  => 'https://athemes.com/themes-demo-content/botiga/plants/thumb.png',
			'plugins'    => array_merge(
				$plugins,
				array(
					array(
						'name'     => 'WPForms',
						'slug'     => 'wpforms-lite',
						'path'     => 'wpforms-lite/wpforms.php',
						'required' => false
					),
				),
			),
			'import'         => array(
				'gutenberg'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/plants/botiga-dc-plants.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/plants/botiga-w-plants.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/plants/botiga-c-plants.dat'
				),
				'elementor'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/elementor/plants/botiga-dc-plants-el.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/elementor/plants/botiga-w-plants-el.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/elementor/plants/botiga-c-plants-el.dat'
				),
			),
		),
		'shoes' => array(
			'name'       => esc_html__( 'Shoes', 'athemes-starter-sites' ),
			'type'       => 'pro',
			'categories' => array( 'ecommerce' ),
			'builders'   => array(
				'gutenberg',
				'elementor',
			),
			'preview'    => 'https://demo.athemes.com/botiga-shoes/',
			'thumbnail'  => 'https://athemes.com/themes-demo-content/botiga/shoes/thumb.png',
			'plugins'    => array_merge(
				$plugins,
				array(
					array(
						'name'     => 'WPForms',
						'slug'     => 'wpforms-lite',
						'path'     => 'wpforms-lite/wpforms.php',
						'required' => false
					),
				),
			),
			'import'         => array(
				'gutenberg'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/shoes/botiga-dc-shoes.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/shoes/botiga-w-shoes.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/shoes/botiga-c-shoes.dat'
				),
				'elementor'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/elementor/shoes/botiga-dc-shoes-el.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/elementor/shoes/botiga-w-shoes-el.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/elementor/shoes/botiga-c-shoes-el.dat'
				),
			),
		),
		'books' => array(
			'name'       => esc_html__( 'Books', 'athemes-starter-sites' ),
			'type'       => 'pro',
			'categories' => array( 'ecommerce' ),
			'builders'   => array(
				'gutenberg',
				'elementor',
			),
			'preview'    => 'https://demo.athemes.com/botiga-books/',
			'thumbnail'  => 'https://athemes.com/themes-demo-content/botiga/books/thumb.png',
			'plugins'    => array_merge(
				$plugins,
				array(
					array(
						'name'     => 'WPForms',
						'slug'     => 'wpforms-lite',
						'path'     => 'wpforms-lite/wpforms.php',
						'required' => false
					),
				),
			),
			'import'         => array(
				'gutenberg'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/books/botiga-dc-books.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/books/botiga-w-books.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/books/botiga-c-books.dat'
				),
				'elementor'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/elementor/books/botiga-dc-books-el.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/elementor/books/botiga-w-books-el.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/elementor/books/botiga-c-books-el.dat'
				),
			),
		),
		'fashion' => array(
			'name'       => esc_html__( 'Fashion', 'athemes-starter-sites' ),
			'type'       => 'pro',
			'categories' => array( 'ecommerce' ),
			'builders'   => array(
				'gutenberg',
				'elementor',
			),
			'preview'    => 'https://demo.athemes.com/botiga-fashion/',
			'thumbnail'  => 'https://athemes.com/themes-demo-content/botiga/fashion/thumb.png',
			'plugins'    => array_merge(
				$plugins,
				array(
					array(
						'name'     => 'WPForms',
						'slug'     => 'wpforms-lite',
						'path'     => 'wpforms-lite/wpforms.php',
						'required' => false
					),
				),
			),
			'import'         => array(
				'gutenberg'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/fashion/botiga-dc-fashion.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/fashion/botiga-w-fashion.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/fashion/botiga-c-fashion.dat'
				),
				'elementor'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/elementor/fashion/botiga-dc-fashion-el.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/elementor/fashion/botiga-w-fashion-el.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/elementor/fashion/botiga-c-fashion-el.dat'
				),
			),
		),
		'handbags' => array(
			'name'       => esc_html__( 'Handbags', 'athemes-starter-sites' ),
			'type'       => 'pro',
			'categories' => array( 'ecommerce' ),
			'builders'   => array(
				'gutenberg',
				'elementor',
			),
			'preview'    => 'https://demo.athemes.com/botiga-handbags/',
			'thumbnail'  => 'https://athemes.com/themes-demo-content/botiga/handbags/thumb.png',
			'plugins'    => array_merge(
				$plugins,
				array(
					array(
						'name'     => 'WPForms',
						'slug'     => 'wpforms-lite',
						'path'     => 'wpforms-lite/wpforms.php',
						'required' => false
					),
				),
			),
			'import'         => array(
				'gutenberg'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/handbags/botiga-dc-handbags.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/handbags/botiga-w-handbags.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/handbags/botiga-c-handbags.dat'
				),
				'elementor'    => array(
					'content'    => 'https://athemes.com/themes-demo-content/botiga/elementor/handbags/botiga-dc-handbags-el.xml',
					'widgets'    => 'https://athemes.com/themes-demo-content/botiga/elementor/handbags/botiga-w-handbags-el.wie',
					'customizer' => 'https://athemes.com/themes-demo-content/botiga/elementor/handbags/botiga-c-handbags-el.dat'
				),
			),
		),
	);

	return $demos;

}
add_filter( 'atss_register_demos_list', 'botiga_demos_list' );

/**
 * Define actions that happen before import
 */
function botiga_setup_before_import( $demo_id, $builder_type ) {
	$demos_extra_data = array(
		'fashion' => array(
			'extras' => array(
				'gutenberg' => array(
					'product-filter-presets' => 'https://athemes.com/themes-demo-content/botiga/fashion/botiga-filters-presets-fashion.txt',
					'product-filter-data' => 'https://athemes.com/themes-demo-content/botiga/fashion/botiga-filters-data-fashion.txt',
					'templates-builder-data' => 'https://athemes.com/themes-demo-content/botiga/fashion/botiga-tb-fashion.txt' 
				),
				'elementor' => array(
					'product-filter-presets' => 'https://athemes.com/themes-demo-content/botiga/elementor/fashion/botiga-filters-presets-fashion-el.txt',
					'product-filter-data' => 'https://athemes.com/themes-demo-content/botiga/elementor/fashion/botiga-filters-data-fashion-el.txt',
					'templates-builder-data' => 'https://athemes.com/themes-demo-content/botiga/elementor/fashion/botiga-tb-fashion-el.txt'
				),					
			)
		),
		'handbags' => array(
			'extras' => array(
				'gutenberg' => array(
					'product-filter-presets' => 'https://athemes.com/themes-demo-content/botiga/handbags/botiga-filters-presets-handbags.txt',
					'product-filter-data' => 'https://athemes.com/themes-demo-content/botiga/handbags/botiga-filters-data-handbags.txt',
					'templates-builder-data' => 'https://athemes.com/themes-demo-content/botiga/handbags/botiga-tb-handbags.txt' 
				),
				'elementor' => array(
					'product-filter-presets' => 'https://athemes.com/themes-demo-content/botiga/elementor/handbags/botiga-filters-presets-handbags-el.txt',
					'product-filter-data' => 'https://athemes.com/themes-demo-content/botiga/elementor/handbags/botiga-filters-data-handbags-el.txt',
					'templates-builder-data' => 'https://athemes.com/themes-demo-content/botiga/elementor/handbags/botiga-tb-handbags-el.txt'
				),					
			)
		)
	);
	
	// Fashion Demo Extras
	if ( $demo_id === 'fashion' ) {
		$modules = get_option( 'botiga-modules', array() );
		update_option( 'botiga-modules', array_merge( $modules, array( 
			'advanced-reviews' 			=> true,
			'wishlist' 					=> true,
			'sticky-add-to-cart' 		=> true,
			'shop-filters'       		=> true,
			'templates'		       		=> true,
		) ) );

		$shop_filter_presets = ATSS_Core_Helpers::atss_get_remote_file( $demos_extra_data[ $demo_id ]['extras'][ $builder_type ]['product-filter-presets'] );
		$shop_filter_data = ATSS_Core_Helpers::atss_get_remote_file( $demos_extra_data[ $demo_id ]['extras'][ $builder_type ]['product-filter-data'] );

		update_option( 'botiga-shop-filters-presets', $shop_filter_presets );
		update_option( 'botiga-shop-filters-presets-settings', $shop_filter_data );

		$templates_builder_data = ATSS_Core_Helpers::atss_get_remote_file( $demos_extra_data[ $demo_id ]['extras'][ $builder_type ]['templates-builder-data'] );

		// Append custom data to the templates builder data.
		$templates_builder_data = atss_botiga_append_templates_builder_data( json_decode( $templates_builder_data, true ) );
		
		update_option( 'botiga_template_builder_data', $templates_builder_data );
	}

	// Handbags Demo Extras
	if ( $demo_id === 'handbags' ) {
		$modules = get_option( 'botiga-modules', array() );
		update_option( 'botiga-modules', array_merge( $modules, array( 
			'advanced-reviews' 			=> true,
			'wishlist' 					=> true,
			'shop-filters'       		=> true,
			'templates'		       		=> true,
			'mega-menu'		       		=> true,
		) ) );

		$shop_filter_presets = ATSS_Core_Helpers::atss_get_remote_file( $demos_extra_data[ $demo_id ]['extras'][ $builder_type ]['product-filter-presets'] );
		$shop_filter_data = ATSS_Core_Helpers::atss_get_remote_file( $demos_extra_data[ $demo_id ]['extras'][ $builder_type ]['product-filter-data'] );

		update_option( 'botiga-shop-filters-presets', $shop_filter_presets );
		update_option( 'botiga-shop-filters-presets-settings', $shop_filter_data );

		$templates_builder_data = ATSS_Core_Helpers::atss_get_remote_file( $demos_extra_data[ $demo_id ]['extras'][ $builder_type ]['templates-builder-data'] );

		// Append custom data to the templates builder data.
		$templates_builder_data = atss_botiga_append_templates_builder_data( json_decode( $templates_builder_data, true ) );
		
		update_option( 'botiga_template_builder_data', $templates_builder_data );
	}
}
add_action( 'atss_import_start', 'botiga_setup_before_import', 10, 2 );

/**
 * Define actions that happen after import
 */
function botiga_setup_after_import( $demo_id ) {

	// Enable Merchant modules.
	if ( class_exists( 'Merchant' ) ) {
		$modules = get_option( 'merchant-modules', array() );
		
		update_option( 'merchant-modules', array_merge( $modules, array( 
			'inactive-tab-message'    => true,
			'agree-to-terms-checkbox' => true,
			'payment-logos'           => true,
		) ) );
	}

	// Disable WPForms modern markup.
	// This is needed because our demos was built with the old markup.
	if ( in_array( $demo_id, array( 'beauty', 'apparel', 'furniture', 'jewelry', 'single-product', 'multi-vendor', 'wine', 'plants', 'shoes', 'books' ) ) ) {
		$wpforms_settings                    = (array) get_option( 'wpforms_settings', [] );
		$wpforms_settings[ 'modern-markup' ] = false;
	
		update_option( 'wpforms_settings', $wpforms_settings );
	}

	// Assign the menu.
	$main_menu = get_term_by( 'name', 'Main', 'nav_menu' );
	if ( ! empty( $main_menu ) ) {
		$locations = get_theme_mod( 'nav_menu_locations', array() );
		$locations['primary'] = $main_menu->term_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	// Beauty, Furniture and Single Product Demo Extras
	if ( in_array( $demo_id, array( 'beauty', 'furniture', 'single-product', 'multi-vendor' ) ) ) {

		// Set modules.
	  $modules = get_option( 'botiga-modules', array() );
		update_option( 'botiga-modules', array_merge( $modules, array( 'hf-builder' => true ) ) );

	}

	// Multi Vendor Demo Extras
	if ( $demo_id === 'multi-vendor' ) {

		// Set modules.
	  $modules = get_option( 'botiga-modules', array() );
		update_option( 'botiga-modules', array_merge( $modules, array( 'hf-builder' => true, 'mega-menu' => true, 'size-chart' => true, 'product-swatches' => true ) ) );

		// Assign secondary menu
		$secondary_menu = get_term_by( 'name', 'Trending Categories', 'nav_menu' );
		if ( ! empty( $secondary_menu ) ) {
			$locations = get_theme_mod( 'nav_menu_locations', array() );
			$locations['secondary'] = $secondary_menu->term_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}

	}

	// Apparel Demo Extras
	if ( $demo_id === 'apparel' ) {

		// Set modules.
		// The demo apparel uses the old header system, so we need to disable the HF Builder
	  $modules = get_option( 'botiga-modules', array() );
		update_option( 'botiga-modules', array_merge( $modules, array( 'hf-builder' => false ) ) );

		// Assign footer copyright menu
		$copyright_menu = get_term_by( 'name', 'Footer Copyright', 'nav_menu' );
		if ( ! empty( $copyright_menu ) ) {
			$locations = get_theme_mod( 'nav_menu_locations', array() );
			$locations['footer-copyright-menu'] = $copyright_menu->term_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}

	}

	// Jewelry Demo Extras
	if ( $demo_id === 'jewelry' ) {

		// Set modules.
	  	$modules = get_option( 'botiga-modules', array() );
		update_option( 'botiga-modules', array_merge( $modules, array( 'hf-builder' => true, 'mega-menu' => true ) ) );

		// Update custom CSS file with mega menu css
		if ( class_exists( 'Botiga_Mega_menu' ) ) {
			$mega_menu = Botiga_Mega_Menu::get_instance();
			$mega_menu->save_mega_menu_css_as_option();
			$mega_menu->update_custom_css_file();
		}

	}

	// Plants Demo Extras
	if( $demo_id === 'plants' ) {
		// Set modules.
		$modules = get_option( 'botiga-modules', array() );
		update_option( 'botiga-modules', array_merge( $modules, array( 'wishlist' => true, 'advanced-reviews' => true ) ) );
	}

	// Shoes Demo Extras
	if( $demo_id === 'shoes' ) {
		// Set modules.
		$modules = get_option( 'botiga-modules', array() );
		update_option( 'botiga-modules', array_merge( $modules, array( 
			'hf-builder' 	   			=> true, 
			'wishlist' 		   			=> true, 
			'advanced-reviews' 			=> true, 
			'size-chart' 	   			=> true, 
			'product-swatches' 			=> true,
			'add-to-cart-notifications' => true,
			'quick-links'               => true
		) ) );
	}

	// Books Demo Extras
	if( $demo_id === 'books' ) {
		// Set modules.
		$modules = get_option( 'botiga-modules', array() );
		update_option( 'botiga-modules', array_merge( $modules, array( 
			'advanced-reviews' 			=> true,
			'buy-now' 					=> true,
		) ) );
	}

	// "Footer" menu (menu name from import)
	$footer_menu_one = get_term_by( 'name', 'Footer', 'nav_menu' );
	if ( ! empty( $footer_menu_one ) ) {
		$nav_menu_widget = get_option( 'widget_nav_menu' );
		foreach ( $nav_menu_widget as $key => $widget ) {
			if ( $key !== '_multiwidget' ) {
				if ( ( ! empty( $nav_menu_widget[ $key ]['title'] ) && in_array( $nav_menu_widget[ $key ]['title'], array( 'Quick links', 'Quick Links' ) ) ) || ( empty( $nav_menu_widget[ $key ]['title'] ) && $demo_id === 'jewelry' ) || ( empty( $nav_menu_widget[ $key ]['title'] ) && $demo_id === 'wine' ) ) {
					$nav_menu_widget[ $key ]['nav_menu'] = $footer_menu_one->term_id;
					update_option( 'widget_nav_menu', $nav_menu_widget );
				}
			}
		}
	}

	// "Footer 2" menu (menu name from import)
	$footer_menu_two = get_term_by( 'name', 'Footer 2', 'nav_menu' );
	if ( ! empty( $footer_menu_two ) ) {
		$nav_menu_widget = get_option( 'widget_nav_menu' );
		foreach ( $nav_menu_widget as $key => $widget ) {
			if ( $key !== '_multiwidget' ) {
				if ( ! empty( $nav_menu_widget[ $key ]['title'] ) && in_array( $nav_menu_widget[ $key ]['title'], array( 'About' ) ) ) {
					$nav_menu_widget[ $key ]['nav_menu'] = $footer_menu_two->term_id;
					update_option( 'widget_nav_menu', $nav_menu_widget );
				}
			}
		}
	}

	// Asign the front as page.
	update_option( 'show_on_front', 'page' );

	// Asign the front page.
	$front_page = ATSS_Core_Helpers::atss_get_page_by_title( 'Home' );
	if ( ! empty( $front_page ) ) {
		update_option( 'page_on_front', $front_page->ID );
	}

	// Asign the blog page.
	$blog_page  = ATSS_Core_Helpers::atss_get_page_by_title( 'Blog' );
	if ( ! empty( $blog_page ) ) {
		update_option( 'page_for_posts', $blog_page->ID );
	}

	// My wishlist page
	$wishlist_page = ATSS_Core_Helpers::atss_get_page_by_title( 'My Wishlist' );
	if ( ! empty( $wishlist_page ) ) {
		update_option( 'botiga_wishlist_page_id', $wishlist_page->ID );
	}

	// Asign the shop page.
	$shop_page = ( 'single-product' === $demo_id ) ? ATSS_Core_Helpers::atss_get_page_by_title( 'Listing' ) : ATSS_Core_Helpers::atss_get_page_by_title( 'Shop' );
	if ( ! empty( $shop_page ) ) {
		update_option( 'woocommerce_shop_page_id', $shop_page->ID );
	}

	// Asign the cart page.
	$cart_page = ATSS_Core_Helpers::atss_get_page_by_title( 'Cart' );
	if ( ! empty( $cart_page ) ) {
		update_option( 'woocommerce_cart_page_id', $cart_page->ID );
	}

	// Asign the checkout page.
	$checkout_page  = ATSS_Core_Helpers::atss_get_page_by_title( 'Checkout' );
	if ( ! empty( $checkout_page ) ) {
		update_option( 'woocommerce_checkout_page_id', $checkout_page->ID );
	}

	// Asign the myaccount page.
	$myaccount_page = ATSS_Core_Helpers::atss_get_page_by_title( 'My Account' );
	if ( ! empty( $myaccount_page ) ) {
		update_option( 'woocommerce_myaccount_page_id', $myaccount_page->ID );
	}

	// Update custom CSS
	$custom_css = Botiga_Custom_CSS::get_instance();
	$custom_css->update_custom_css_file();

	// Set current starter site
	atss()->current_starter( 'botiga', $demo_id );

}
add_action( 'atss_finish_import', 'botiga_setup_after_import' );

/**
 * Append custom data to the templates builder data.
 * 
 * @param array $templates_builder_data The templates builder data.
 * 
 * @return array
 */
function atss_botiga_append_templates_builder_data( $templates_builder_data ) {
	$new_data = array();

	// Update the templates builder data.
	foreach( $templates_builder_data as $template ) {
		$template['template_preview_url'] = get_bloginfo( 'url' ) . '/athemes_hf/' . $template['id'] . '-content';

		$new_data[] = $template;
	}

	return $new_data;
}

// Do not create default WooCommerce pages when plugin is activated
// The condition avoid the filter being applied in others pages
// Eg: Woo > Status > Tools > Create default pages
if ( isset( $_POST['action'] ) && $_POST['action'] === 'atss_import_plugin' ) {
	add_filter( 'woocommerce_create_pages', '__return_empty_array' );
}
