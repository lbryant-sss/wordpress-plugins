<?php
/**
 * Loads WooCommece packages from the /packages directory. These are packages developed outside of core.
 *
 * @package Vendidero/Germanized
 */

namespace Vendidero\Germanized;

defined( 'ABSPATH' ) || exit;

/**
 * Packages class.
 *
 * @since 3.7.0
 */
class Shopmarks {

	protected static $shopmarks = array();

	protected static function register_single_product() {
		/**
		 * Filter to adjust default shopmark configuration for the single product page.
		 *
		 * @param array $defaults Array containing the default configuration.
		 *
		 * @since 3.0.0
		 */
		$shopmarks_single_product = apply_filters(
			'woocommerce_gzd_shopmark_single_product_defaults',
			array(
				'unit_price'                 => array(
					'default_filter'   => 'woocommerce_single_product_summary',
					'default_priority' => 11,
					'callback'         => 'woocommerce_gzd_template_single_price_unit',
				),
				'legal'                      => array(
					'default_filter'   => 'woocommerce_single_product_summary',
					'default_priority' => 12,
					'callback'         => 'woocommerce_gzd_template_single_legal_info',
				),
				'delivery_time'              => array(
					'default_filter'   => 'woocommerce_single_product_summary',
					'default_priority' => 27,
					'callback'         => 'woocommerce_gzd_template_single_delivery_time_info',
				),
				'units'                      => array(
					'default_filter'   => 'woocommerce_product_meta_start',
					'default_priority' => 5,
					'callback'         => 'woocommerce_gzd_template_single_product_units',
				),
				'manufacturer'               => array(
					'default_filter'   => 'woocommerce_gzd_single_product_safety_information',
					'default_priority' => 10,
					'callback'         => 'woocommerce_gzd_template_single_manufacturer',
				),
				'product_safety_attachments' => array(
					'default_filter'   => 'woocommerce_gzd_single_product_safety_information',
					'default_priority' => 11,
					'callback'         => 'woocommerce_gzd_template_single_product_safety_attachments',
				),
				'safety_instructions'        => array(
					'default_filter'   => 'woocommerce_gzd_single_product_safety_information',
					'default_priority' => 12,
					'callback'         => 'woocommerce_gzd_template_single_safety_instructions',
				),
				'defect_description'         => array(
					'default_filter'   => 'woocommerce_single_product_summary',
					'default_priority' => 21,
					'callback'         => 'woocommerce_gzd_template_single_defect_description',
				),
				'power_supply'               => array(
					'default_filter'   => 'woocommerce_product_thumbnails',
					'default_priority' => 11,
					'callback'         => 'woocommerce_gzd_template_single_product_power_supply',
				),
				'deposit'                    => array(
					'default_filter'   => 'woocommerce_single_product_summary',
					'default_priority' => 13,
					'callback'         => 'woocommerce_gzd_template_single_deposit',
				),
				'deposit_packaging_type'     => array(
					'default_filter'   => 'woocommerce_single_product_summary',
					'default_priority' => 10,
					'callback'         => 'woocommerce_gzd_template_single_deposit_packaging_type',
				),
				'nutri_score'                => array(
					'default_filter'   => 'woocommerce_single_product_summary',
					'default_priority' => 15,
					'callback'         => 'woocommerce_gzd_template_single_nutri_score',
				),
			)
		);

		self::$shopmarks['single_product'] = array();

		foreach ( $shopmarks_single_product as $type => $args ) {
			$args['location'] = 'single_product';
			$args['type']     = $type;

			self::$shopmarks['single_product'][] = new Shopmark( $args );
		}
	}

	protected static function register_single_product_grouped() {
		/**
		 * Filter to adjust default shopmark configuration for the single product page specifically for grouped products.
		 *
		 * @param array $defaults Array containing the default configuration.
		 *
		 * @since 3.0.0
		 *
		 */
		$shopmarks_single_product_grouped = apply_filters(
			'woocommerce_gzd_shopmark_single_product_grouped_defaults',
			array(
				'unit_price'             => array(
					'default_filter'   => 'woocommerce_grouped_product_list_column_price',
					'default_priority' => 11,
					'callback'         => 'woocommerce_gzd_template_grouped_single_price_unit',
				),
				'legal'                  => array(
					'default_filter'   => 'woocommerce_grouped_product_list_column_price',
					'default_priority' => 12,
					'callback'         => 'woocommerce_gzd_template_grouped_single_legal_info',
				),
				'delivery_time'          => array(
					'default_filter'   => 'woocommerce_grouped_product_list_column_price',
					'default_priority' => 15,
					'callback'         => 'woocommerce_gzd_template_grouped_single_delivery_time_info',
				),
				'units'                  => array(
					'default_filter'   => 'woocommerce_grouped_product_list_column_price',
					'default_priority' => 20,
					'callback'         => 'woocommerce_gzd_template_grouped_single_product_units',
				),
				'defect_description'     => array(
					'default_filter'   => 'woocommerce_grouped_product_list_column_label',
					'default_priority' => 11,
					'callback'         => 'woocommerce_gzd_template_grouped_single_defect_description',
				),
				'deposit'                => array(
					'default_filter'   => 'woocommerce_grouped_product_list_column_price',
					'default_priority' => 13,
					'callback'         => 'woocommerce_gzd_template_grouped_single_deposit_amount',
				),
				'deposit_packaging_type' => array(
					'default_filter'   => 'woocommerce_grouped_product_list_column_price',
					'default_priority' => 10,
					'callback'         => 'woocommerce_gzd_template_grouped_single_deposit_packaging_type',
				),
				'nutri_score'            => array(
					'default_filter'   => 'woocommerce_grouped_product_list_column_label',
					'default_priority' => 20,
					'callback'         => 'woocommerce_gzd_template_grouped_single_nutri_score',
				),
			)
		);

		self::$shopmarks['single_product_grouped'] = array();

		foreach ( $shopmarks_single_product_grouped as $type => $args ) {
			$args['location'] = 'single_product_grouped';
			$args['type']     = $type;

			self::$shopmarks['single_product_grouped'][] = new Shopmark( $args );
		}
	}

	protected static function register_product_loop() {
		/**
		 * Filter to adjust default shopmark configuration for the product loop.
		 *
		 * @param array $defaults Array containing the default configuration.
		 *
		 * @since 3.0.0
		 *
		 */
		$shopmarks_product_loop = apply_filters(
			'woocommerce_gzd_shopmark_product_loop_defaults',
			array(
				'unit_price'             => array(
					'default_filter'   => 'woocommerce_after_shop_loop_item_title',
					'default_priority' => 11,
					'callback'         => 'woocommerce_gzd_template_loop_price_unit',
				),
				'tax'                    => array(
					'default_filter'   => 'woocommerce_after_shop_loop_item',
					'default_priority' => 6,
					'callback'         => 'woocommerce_gzd_template_loop_tax_info',
				),
				'shipping_costs'         => array(
					'default_filter'   => 'woocommerce_after_shop_loop_item',
					'default_priority' => 7,
					'callback'         => 'woocommerce_gzd_template_loop_shipping_costs_info',
				),
				'delivery_time'          => array(
					'default_filter'   => 'woocommerce_after_shop_loop_item',
					'default_priority' => 8,
					'callback'         => 'woocommerce_gzd_template_loop_delivery_time_info',
				),
				'units'                  => array(
					'default_filter'   => 'woocommerce_after_shop_loop_item',
					'default_priority' => 9,
					'callback'         => 'woocommerce_gzd_template_loop_product_units',
				),
				'deposit'                => array(
					'default_filter'   => 'woocommerce_after_shop_loop_item_title',
					'default_priority' => 12,
					'callback'         => 'woocommerce_gzd_template_loop_deposit',
				),
				'deposit_packaging_type' => array(
					'default_filter'   => 'woocommerce_after_shop_loop_item_title',
					'default_priority' => 10,
					'callback'         => 'woocommerce_gzd_template_loop_deposit_packaging_type',
				),
				'nutri_score'            => array(
					'default_filter'   => 'woocommerce_after_shop_loop_item',
					'default_priority' => 15,
					'callback'         => 'woocommerce_gzd_template_loop_nutri_score',
				),
			)
		);

		self::$shopmarks['product_loop'] = array();

		foreach ( $shopmarks_product_loop as $type => $args ) {
			$args['location'] = 'product_loop';
			$args['type']     = $type;

			self::$shopmarks['product_loop'][] = new Shopmark( $args );
		}
	}

	protected static function register_product_block() {
		/**
		 * Filter to adjust default shopmark configuration for product grid blocks.
		 *
		 * @param array $defaults Array containing the default configuration.
		 *
		 * @since 3.0.0
		 *
		 */
		$shopmarks_product_loop = apply_filters(
			'woocommerce_gzd_shopmark_product_block_defaults',
			array(
				'unit_price'             => array(
					'default_filter'   => 'woocommerce_gzd_after_product_grid_block_after_price',
					'default_priority' => 5,
					'callback'         => 'woocommerce_gzd_template_loop_price_unit',
				),
				'tax'                    => array(
					'default_filter'   => 'woocommerce_gzd_after_product_grid_block_after_price',
					'default_priority' => 10,
					'callback'         => 'woocommerce_gzd_template_loop_tax_info',
				),
				'shipping_costs'         => array(
					'default_filter'   => 'woocommerce_gzd_after_product_grid_block_after_price',
					'default_priority' => 11,
					'callback'         => 'woocommerce_gzd_template_loop_shipping_costs_info',
				),
				'delivery_time'          => array(
					'default_filter'   => 'woocommerce_gzd_after_product_grid_block_after_price',
					'default_priority' => 12,
					'callback'         => 'woocommerce_gzd_template_loop_delivery_time_info',
				),
				'units'                  => array(
					'default_filter'   => 'woocommerce_gzd_after_product_grid_block_after_price',
					'default_priority' => 15,
					'callback'         => 'woocommerce_gzd_template_loop_product_units',
					'default_enabled'  => false,
				),
				'deposit'                => array(
					'default_filter'   => 'woocommerce_gzd_after_product_grid_block_after_price',
					'default_priority' => 6,
					'callback'         => 'woocommerce_gzd_template_loop_deposit',
				),
				'deposit_packaging_type' => array(
					'default_filter'   => 'woocommerce_gzd_after_product_grid_block_after_title',
					'default_priority' => 10,
					'callback'         => 'woocommerce_gzd_template_loop_deposit_packaging_type',
				),
				'nutri_score'            => array(
					'default_filter'   => 'woocommerce_gzd_after_product_grid_block_after_price',
					'default_priority' => 20,
					'callback'         => 'woocommerce_gzd_template_loop_nutri_score',
				),
			)
		);

		self::$shopmarks['product_block'] = array();

		foreach ( $shopmarks_product_loop as $type => $args ) {
			$args['location'] = 'product_block';
			$args['type']     = $type;

			self::$shopmarks['product_block'][] = new Shopmark( $args );
		}
	}

	protected static function register_cart() {
		/**
		 * Filter to adjust default shopmark configuration for the cart.
		 *
		 * @param array $defaults Array containing the default configuration.
		 *
		 * @since 3.0.0
		 *
		 */
		$shopmarks_cart = apply_filters(
			'woocommerce_gzd_shopmark_cart_defaults',
			array(
				'unit_price'             => array(
					'default_filter'   => 'woocommerce_cart_item_price',
					'default_priority' => 5000,
					'callback'         => 'wc_gzd_cart_product_unit_price',
				),
				'delivery_time'          => array(
					'default_filter'   => 'woocommerce_after_cart_item_name',
					'default_priority' => 1,
					'callback'         => 'wc_gzd_cart_product_delivery_time',
				),
				'units'                  => array(
					'default_filter'   => 'woocommerce_after_cart_item_name',
					'default_priority' => 2,
					'callback'         => 'wc_gzd_cart_product_units',
				),
				'item_desc'              => array(
					'default_filter'   => 'woocommerce_after_cart_item_name',
					'default_priority' => 3,
					'callback'         => 'wc_gzd_cart_product_item_desc',
				),
				'defect_description'     => array(
					'default_filter'   => 'woocommerce_after_cart_item_name',
					'default_priority' => 4,
					'callback'         => 'wc_gzd_cart_product_defect_description',
				),
				'deposit'                => array(
					'default_filter'   => 'woocommerce_cart_item_subtotal',
					'default_priority' => 5000,
					'callback'         => 'wc_gzd_cart_product_deposit_amount',
				),
				'deposit_packaging_type' => array(
					'default_filter'   => 'woocommerce_after_cart_item_name',
					'default_priority' => 5,
					'callback'         => 'wc_gzd_cart_product_deposit_packaging_type',
				),
			)
		);

		self::$shopmarks['cart'] = array();

		foreach ( $shopmarks_cart as $type => $args ) {
			$args['location'] = 'cart';
			$args['type']     = $type;

			self::$shopmarks['cart'][] = new Shopmark( $args );
		}
	}

	protected static function register_mini_cart() {
		/**
		 * Filter to adjust default shopmark configuration for the mini cart (cart dropdown).
		 *
		 * @param array $defaults Array containing the default configuration.
		 *
		 * @since 3.0.0
		 *
		 */
		$shopmarks_cart = apply_filters(
			'woocommerce_gzd_shopmark_mini_cart_defaults',
			array(
				'unit_price'             => array(
					'default_filter'   => 'woocommerce_cart_item_price',
					'default_priority' => 5000,
					'callback'         => 'wc_gzd_cart_product_unit_price',
				),
				'delivery_time'          => array(
					'default_filter'   => 'woocommerce_cart_item_name',
					'default_priority' => 10,
					'callback'         => 'wc_gzd_cart_product_delivery_time',
				),
				'units'                  => array(
					'default_filter'   => 'woocommerce_cart_item_name',
					'default_priority' => 11,
					'callback'         => 'wc_gzd_cart_product_units',
				),
				'item_desc'              => array(
					'default_filter'   => 'woocommerce_cart_item_name',
					'default_priority' => 12,
					'callback'         => 'wc_gzd_cart_product_item_desc',
				),
				'defect_description'     => array(
					'default_filter'   => 'woocommerce_cart_item_name',
					'default_priority' => 13,
					'callback'         => 'wc_gzd_cart_product_defect_description',
				),
				'deposit'                => array(
					'default_filter'   => 'woocommerce_cart_item_price',
					'default_priority' => 5000,
					'callback'         => 'wc_gzd_cart_product_deposit_amount',
				),
				'deposit_packaging_type' => array(
					'default_filter'   => 'woocommerce_cart_item_name',
					'default_priority' => 9,
					'callback'         => 'wc_gzd_cart_product_deposit_packaging_type',
				),
			)
		);

		self::$shopmarks['mini_cart'] = array();

		foreach ( $shopmarks_cart as $type => $args ) {
			$args['location'] = 'mini_cart';
			$args['type']     = $type;

			self::$shopmarks['mini_cart'][] = new Shopmark( $args );
		}
	}

	protected static function register_checkout() {
		/**
		 * Filter to adjust default shopmark configuration for the checkout.
		 *
		 * @param array $defaults Array containing the default configuration.
		 *
		 * @since 3.0.0
		 *
		 */
		$shopmarks_checkout = apply_filters(
			'woocommerce_gzd_shopmark_checkout_defaults',
			array(
				'unit_price'             => array(
					'default_filter'   => 'woocommerce_cart_item_subtotal',
					'default_priority' => 0,
					'callback'         => 'wc_gzd_cart_product_unit_price',
				),
				'delivery_time'          => array(
					'default_filter'   => 'woocommerce_checkout_cart_item_quantity',
					'default_priority' => 10,
					'callback'         => 'wc_gzd_cart_product_delivery_time',
				),
				'units'                  => array(
					'default_filter'   => 'woocommerce_checkout_cart_item_quantity',
					'default_priority' => 11,
					'callback'         => 'wc_gzd_cart_product_units',
				),
				'item_desc'              => array(
					'default_filter'   => 'woocommerce_checkout_cart_item_quantity',
					'default_priority' => 12,
					'callback'         => 'wc_gzd_cart_product_item_desc',
				),
				'defect_description'     => array(
					'default_filter'   => 'woocommerce_checkout_cart_item_quantity',
					'default_priority' => 13,
					'callback'         => 'wc_gzd_cart_product_defect_description',
				),
				'deposit'                => array(
					'default_filter'   => 'woocommerce_cart_item_subtotal',
					'default_priority' => 5,
					'callback'         => 'wc_gzd_cart_product_deposit_amount',
				),
				'deposit_packaging_type' => array(
					'default_filter'   => 'woocommerce_checkout_cart_item_quantity',
					'default_priority' => 9,
					'callback'         => 'wc_gzd_cart_product_deposit_packaging_type',
				),
			)
		);

		self::$shopmarks['checkout'] = array();

		foreach ( $shopmarks_checkout as $type => $args ) {
			$args['location'] = 'checkout';
			$args['type']     = $type;

			self::$shopmarks['checkout'][] = new Shopmark( $args );
		}
	}

	protected static function register_order() {
		/**
		 * Filter to adjust default shopmark configuration for the order (thankyou, pay for order).
		 *
		 * @param array $defaults Array containing the default configuration.
		 *
		 * @since 3.0.0
		 */
		$shopmarks_order = apply_filters(
			'woocommerce_gzd_shopmark_order_defaults',
			array(
				'unit_price'             => array(
					'default_filter'   => 'woocommerce_order_formatted_line_subtotal',
					'default_priority' => 0,
					'callback'         => 'wc_gzd_cart_product_unit_price',
				),
				'delivery_time'          => array(
					'default_filter'   => 'woocommerce_order_item_meta_start',
					'default_priority' => 20,
					'callback'         => 'wc_gzd_cart_product_delivery_time',
				),
				'units'                  => array(
					'default_filter'   => 'woocommerce_order_item_meta_start',
					'default_priority' => 25,
					'callback'         => 'wc_gzd_cart_product_units',
				),
				'item_desc'              => array(
					'default_filter'   => 'woocommerce_order_item_meta_start',
					'default_priority' => 10,
					'callback'         => 'wc_gzd_cart_product_item_desc',
				),
				'defect_description'     => array(
					'default_filter'   => 'woocommerce_order_item_meta_start',
					'default_priority' => 15,
					'callback'         => 'wc_gzd_cart_product_defect_description',
				),
				'deposit'                => array(
					'default_filter'   => 'woocommerce_order_formatted_line_subtotal',
					'default_priority' => 5,
					'callback'         => 'wc_gzd_cart_product_deposit_amount',
				),
				'deposit_packaging_type' => array(
					'default_filter'   => 'woocommerce_order_item_meta_start',
					'default_priority' => 9,
					'callback'         => 'wc_gzd_cart_product_deposit_packaging_type',
				),
			)
		);

		self::$shopmarks['order'] = array();

		foreach ( $shopmarks_order as $type => $args ) {
			$args['location'] = 'order';
			$args['type']     = $type;

			self::$shopmarks['order'][] = new Shopmark( $args );
		}
	}

	/**
	 * @param Shopmark $shopmark1
	 * @param Shopmark $shopmark2
	 *
	 * @return int
	 */
	protected static function uasort_callback( $shopmark1, $shopmark2 ) {
		if ( $shopmark1->get_priority() === $shopmark2->get_priority() ) {
			return 0;
		}

		return ( $shopmark1->get_priority() < $shopmark2->get_priority() ) ? - 1 : 1;
	}

	public static function get_locations() {
		if ( did_action( 'init' ) || doing_action( 'init' ) ) {
			$locations = array(
				'single_product'         => __( 'Single Product', 'woocommerce-germanized' ),
				'single_product_grouped' => __( 'Single Product (Grouped)', 'woocommerce-germanized' ),
				'product_loop'           => __( 'Product Loop', 'woocommerce-germanized' ),
				'product_block'          => __( 'Blocks', 'woocommerce-germanized' ),
				'cart'                   => __( 'Cart', 'woocommerce-germanized' ),
				'mini_cart'              => __( 'Mini Cart', 'woocommerce-germanized' ),
				'checkout'               => __( 'Checkout', 'woocommerce-germanized' ),
				'order'                  => __( 'Order', 'woocommerce-germanized' ),
			);
		} else {
			$locations = array(
				'single_product'         => 'Single Product',
				'single_product_grouped' => 'Single Product (Grouped)',
				'product_loop'           => 'Product Loop',
				'product_block'          => 'Blocks',
				'cart'                   => 'Cart',
				'mini_cart'              => 'Mini Cart',
				'checkout'               => 'Checkout',
				'order'                  => 'Order',
			);
		}

		return $locations;
	}

	public static function get_location_title( $location ) {
		$locations = self::get_locations();

		return isset( $locations[ $location ] ) ? $locations[ $location ] : '';
	}

	/**
	 * @param string $location
	 *
	 * @return string[]
	 */
	public static function get_filters( $location = 'single_product' ) {
		$load_translation = doing_action( 'init' ) || did_action( 'init' );

		$filters = array(
			'single_product'         => array(
				'woocommerce_single_product_summary'       => array(
					'title'            => $load_translation ? __( 'Summary', 'woocommerce-germanized' ) : 'Summary',
					'is_action'        => true,
					'number_of_params' => 1,
				),
				'woocommerce_product_meta_start'           => array(
					'title'            => $load_translation ? __( 'Meta', 'woocommerce-germanized' ) : 'Meta',
					'is_action'        => true,
					'number_of_params' => 1,
				),
				'woocommerce_product_meta_end'             => array(
					'title'            => $load_translation ? __( 'After Meta', 'woocommerce-germanized' ) : 'After Meta',
					'is_action'        => true,
					'number_of_params' => 1,
				),
				'woocommerce_before_add_to_cart_form'      => array(
					'title'            => $load_translation ? __( 'Before add to cart', 'woocommerce-germanized' ) : 'Before add to cart',
					'is_action'        => true,
					'number_of_params' => 1,
				),
				'woocommerce_after_add_to_cart_form'       => array(
					'title'            => $load_translation ? __( 'After add to cart', 'woocommerce-germanized' ) : 'After add to cart',
					'is_action'        => true,
					'number_of_params' => 1,
				),
				'woocommerce_before_add_to_cart_quantity'  => array(
					'title'            => $load_translation ? __( 'Before add to cart quantity', 'woocommerce-germanized' ) : 'Before add to cart quantity',
					'is_action'        => true,
					'number_of_params' => 1,
				),
				'woocommerce_after_add_to_cart_quantity'   => array(
					'title'            => $load_translation ? __( 'After add to cart quantity', 'woocommerce-germanized' ) : 'After add to cart quantity',
					'is_action'        => true,
					'number_of_params' => 1,
				),
				'woocommerce_after_single_product_summary' => array(
					'title'            => $load_translation ? __( 'After Summary', 'woocommerce-germanized' ) : 'After Summary',
					'is_action'        => true,
					'number_of_params' => 1,
				),
				'woocommerce_gzd_single_product_safety_information' => array(
					'title'            => $load_translation ? __( 'Product safety tab', 'woocommerce-germanized' ) : 'Product safety tab',
					'is_action'        => true,
					'number_of_params' => 1,
				),
				'woocommerce_product_additional_information' => array(
					'title'            => $load_translation ? __( 'Additional information tab', 'woocommerce-germanized' ) : 'Additional information tab',
					'is_action'        => true,
					'number_of_params' => 1,
				),
				'woocommerce_product_thumbnails'           => array(
					'title'            => $load_translation ? __( 'After thumbnails', 'woocommerce-germanized' ) : 'After thumbnails',
					'is_action'        => true,
					'number_of_params' => 1,
				),
			),
			'single_product_grouped' => array(
				'woocommerce_grouped_product_list_column_price'    => array(
					'title'            => $load_translation ? __( 'Price Column', 'woocommerce-germanized' ) : 'Price Column',
					'is_action'        => false,
					'number_of_params' => 2,
				),
				'woocommerce_grouped_product_list_column_label'    => array(
					'title'            => $load_translation ? __( 'Label Column', 'woocommerce-germanized' ) : 'Label Column',
					'is_action'        => false,
					'number_of_params' => 2,
				),
				'woocommerce_grouped_product_list_column_quantity' => array(
					'title'            => $load_translation ? __( 'Quantity Column', 'woocommerce-germanized' ) : 'Quantity Column',
					'is_action'        => false,
					'number_of_params' => 2,
				),
			),
			'product_loop'           => array(
				'woocommerce_after_shop_loop_item_title'  => array(
					'title'            => $load_translation ? __( 'After Item Title', 'woocommerce-germanized' ) : 'After Item Title',
					'is_action'        => true,
					'number_of_params' => 1,
				),
				'woocommerce_before_shop_loop_item_title' => array(
					'title'            => $load_translation ? __( 'Before Item Title', 'woocommerce-germanized' ) : 'Before Item Title',
					'is_action'        => true,
					'number_of_params' => 1,
				),
				'woocommerce_shop_loop_item_title'        => array(
					'title'            => $load_translation ? __( 'Item Title', 'woocommerce-germanized' ) : 'Item Title',
					'is_action'        => true,
					'number_of_params' => 1,
				),
				'woocommerce_after_shop_loop_item'        => array(
					'title'            => $load_translation ? __( 'After Item', 'woocommerce-germanized' ) : 'After Item',
					'is_action'        => true,
					'number_of_params' => 1,
				),
			),
			'product_block'          => array(
				'woocommerce_gzd_after_product_grid_block_after_title'  => array(
					'title'            => $load_translation ? __( 'After Item Title', 'woocommerce-germanized' ) : 'After Item Title',
					'is_action'        => true,
					'number_of_params' => 1,
				),
				'woocommerce_gzd_after_product_grid_block_after_price' => array(
					'title'            => $load_translation ? __( 'After Item Price', 'woocommerce-germanized' ) : 'After Item Price',
					'is_action'        => true,
					'number_of_params' => 1,
				),
			),
			'cart'                   => array(
				'woocommerce_cart_item_price'      => array(
					'title'            => $load_translation ? __( 'Item Price', 'woocommerce-germanized' ) : 'Item Price',
					'is_action'        => false,
					'number_of_params' => 3,
				),
				'woocommerce_cart_item_name'       => array(
					'title'            => $load_translation ? __( 'Item Name', 'woocommerce-germanized' ) : 'Item Name',
					'is_action'        => false,
					'number_of_params' => 3,
				),
				'woocommerce_after_cart_item_name' => array(
					'title'            => $load_translation ? __( 'After Item Name', 'woocommerce-germanized' ) : 'After Item Name',
					'is_action'        => true,
					'number_of_params' => 2,
				),
				'woocommerce_cart_item_subtotal'   => array(
					'title'            => $load_translation ? __( 'Subtotal', 'woocommerce-germanized' ) : 'Subtotal',
					'is_action'        => false,
					'number_of_params' => 3,
				),
			),
			'mini_cart'              => array(
				'woocommerce_cart_item_price' => array(
					'title'            => $load_translation ? __( 'Item Price', 'woocommerce-germanized' ) : 'Item Price',
					'is_action'        => false,
					'number_of_params' => 3,
				),
				'woocommerce_cart_item_name'  => array(
					'title'            => $load_translation ? __( 'Item Name', 'woocommerce-germanized' ) : 'Item Name',
					'is_action'        => false,
					'number_of_params' => 3,
				),
			),
			'checkout'               => array(
				'woocommerce_cart_item_subtotal'          => array(
					'title'            => $load_translation ? __( 'Subtotal', 'woocommerce-germanized' ) : 'Subtotal',
					'is_action'        => false,
					'number_of_params' => 3,
				),
				'woocommerce_cart_item_name'              => array(
					'title'            => $load_translation ? __( 'Item Name', 'woocommerce-germanized' ) : 'Item Name',
					'is_action'        => false,
					'number_of_params' => 3,
				),
				'woocommerce_checkout_cart_item_quantity' => array(
					'title'            => $load_translation ? __( 'After Item Quantity', 'woocommerce-germanized' ) : 'After Item Quantity',
					'is_action'        => false,
					'number_of_params' => 3,
				),
			),
			'order'                  => array(
				'woocommerce_order_formatted_line_subtotal' => array(
					'title'            => $load_translation ? __( 'Subtotal', 'woocommerce-germanized' ) : 'Subtotal',
					'is_action'        => false,
					'number_of_params' => 3,
				),
				'woocommerce_order_item_name'          => array(
					'title'            => $load_translation ? __( 'Item Name', 'woocommerce-germanized' ) : 'Item Name',
					'is_action'        => false,
					'number_of_params' => 3,
				),
				'woocommerce_order_item_quantity_html' => array(
					'title'            => $load_translation ? __( 'After Item Quantity', 'woocommerce-germanized' ) : 'After Item Quantity',
					'is_action'        => false,
					'number_of_params' => 3,
				),
				'woocommerce_order_item_meta_end'      => array(
					'title'            => $load_translation ? __( 'After Meta', 'woocommerce-germanized' ) : 'After Meta',
					'is_action'        => true,
					'number_of_params' => 3,
				),
				'woocommerce_order_item_meta_start'    => array(
					'title'            => $load_translation ? __( 'Before Meta', 'woocommerce-germanized' ) : 'Before Meta',
					'is_action'        => true,
					'number_of_params' => 3,
				),
			),
		);

		$filter_data = isset( $filters[ $location ] ) ? $filters[ $location ] : array();

		/**
		 * Filter to adjust available hook names for a certain location.
		 *
		 * The dynamic portion of the hook name, `$location` refers to the
		 * shopmark location e.g. single_product
		 *
		 * @param array $hook_names Array containing available hook names.
		 * @param boolean $load_translation Whether to load translations
		 *
		 * @since 3.0.0
		 */
		return apply_filters( "woocommerce_gzd_shopmark_{$location}_filters", $filter_data, $load_translation );
	}

	public static function get_types( $location = 'single_product' ) {
		$types = array(
			'single_product'         => array(
				'unit_price'                 => _x( 'Unit Price', 'shopmark', 'woocommerce-germanized' ),
				'delivery_time'              => _x( 'Delivery Time', 'shopmark', 'woocommerce-germanized' ),
				'legal'                      => _x( 'General', 'shopmark', 'woocommerce-germanized' ),
				'units'                      => _x( 'Product Units', 'shopmark', 'woocommerce-germanized' ),
				'defect_description'         => _x( 'Defect Description', 'shopmark', 'woocommerce-germanized' ),
				'deposit'                    => _x( 'Deposit', 'shopmark', 'woocommerce-germanized' ),
				'deposit_packaging_type'     => _x( 'Type of Packaging', 'shopmark', 'woocommerce-germanized' ),
				'nutri_score'                => _x( 'Nutri-Score', 'shopmark', 'woocommerce-germanized' ),
				'manufacturer'               => _x( 'Manufacturer', 'shopmark', 'woocommerce-germanized' ),
				'product_safety_attachments' => _x( 'Product safety attachments', 'shopmark', 'woocommerce-germanized' ),
				'safety_instructions'        => _x( 'Safety instructions', 'shopmark', 'woocommerce-germanized' ),
				'power_supply'               => _x( 'Power supply', 'shopmark', 'woocommerce-germanized' ),
			),
			'single_product_grouped' => array(
				'unit_price'             => _x( 'Unit Price', 'shopmark', 'woocommerce-germanized' ),
				'delivery_time'          => _x( 'Delivery Time', 'shopmark', 'woocommerce-germanized' ),
				'legal'                  => _x( 'General', 'shopmark', 'woocommerce-germanized' ),
				'units'                  => _x( 'Product Units', 'shopmark', 'woocommerce-germanized' ),
				'defect_description'     => _x( 'Defect Description', 'shopmark', 'woocommerce-germanized' ),
				'deposit'                => _x( 'Deposit', 'shopmark', 'woocommerce-germanized' ),
				'deposit_packaging_type' => _x( 'Type of Packaging', 'shopmark', 'woocommerce-germanized' ),
				'nutri_score'            => _x( 'Nutri-Score', 'shopmark', 'woocommerce-germanized' ),
			),
			'product_loop'           => array(
				'unit_price'             => _x( 'Unit Price', 'shopmark', 'woocommerce-germanized' ),
				'delivery_time'          => _x( 'Delivery Time', 'shopmark', 'woocommerce-germanized' ),
				'tax'                    => _x( 'Tax', 'shopmark', 'woocommerce-germanized' ),
				'shipping_costs'         => _x( 'Shipping Costs', 'shopmark', 'woocommerce-germanized' ),
				'units'                  => _x( 'Product Units', 'shopmark', 'woocommerce-germanized' ),
				'deposit'                => _x( 'Deposit', 'shopmark', 'woocommerce-germanized' ),
				'deposit_packaging_type' => _x( 'Type of Packaging', 'shopmark', 'woocommerce-germanized' ),
				'nutri_score'            => _x( 'Nutri-Score', 'shopmark', 'woocommerce-germanized' ),
			),
			'product_block'          => array(
				'unit_price'             => _x( 'Unit Price', 'shopmark', 'woocommerce-germanized' ),
				'delivery_time'          => _x( 'Delivery Time', 'shopmark', 'woocommerce-germanized' ),
				'tax'                    => _x( 'Tax', 'shopmark', 'woocommerce-germanized' ),
				'shipping_costs'         => _x( 'Shipping Costs', 'shopmark', 'woocommerce-germanized' ),
				'units'                  => _x( 'Product Units', 'shopmark', 'woocommerce-germanized' ),
				'deposit'                => _x( 'Deposit', 'shopmark', 'woocommerce-germanized' ),
				'deposit_packaging_type' => _x( 'Type of Packaging', 'shopmark', 'woocommerce-germanized' ),
				'nutri_score'            => _x( 'Nutri-Score', 'shopmark', 'woocommerce-germanized' ),
			),
			'cart'                   => array(
				'unit_price'             => _x( 'Unit Price', 'shopmark', 'woocommerce-germanized' ),
				'units'                  => _x( 'Product Units', 'shopmark', 'woocommerce-germanized' ),
				'delivery_time'          => _x( 'Delivery Time', 'shopmark', 'woocommerce-germanized' ),
				'item_desc'              => _x( 'Cart Description', 'shopmark', 'woocommerce-germanized' ),
				'defect_description'     => _x( 'Defect Description', 'shopmark', 'woocommerce-germanized' ),
				'deposit'                => _x( 'Deposit', 'shopmark', 'woocommerce-germanized' ),
				'deposit_packaging_type' => _x( 'Type of Packaging', 'shopmark', 'woocommerce-germanized' ),
				'nutri_score'            => _x( 'Nutri-Score', 'shopmark', 'woocommerce-germanized' ),
			),
			'mini_cart'              => array(
				'unit_price'             => _x( 'Unit Price', 'shopmark', 'woocommerce-germanized' ),
				'units'                  => _x( 'Product Units', 'shopmark', 'woocommerce-germanized' ),
				'delivery_time'          => _x( 'Delivery Time', 'shopmark', 'woocommerce-germanized' ),
				'item_desc'              => _x( 'Cart Description', 'shopmark', 'woocommerce-germanized' ),
				'defect_description'     => _x( 'Defect Description', 'shopmark', 'woocommerce-germanized' ),
				'deposit'                => _x( 'Deposit', 'shopmark', 'woocommerce-germanized' ),
				'deposit_packaging_type' => _x( 'Type of Packaging', 'shopmark', 'woocommerce-germanized' ),
				'nutri_score'            => _x( 'Nutri-Score', 'shopmark', 'woocommerce-germanized' ),
			),
			'checkout'               => array(
				'unit_price'             => _x( 'Unit Price', 'shopmark', 'woocommerce-germanized' ),
				'units'                  => _x( 'Product Units', 'shopmark', 'woocommerce-germanized' ),
				'delivery_time'          => _x( 'Delivery Time', 'shopmark', 'woocommerce-germanized' ),
				'item_desc'              => _x( 'Cart Description', 'shopmark', 'woocommerce-germanized' ),
				'defect_description'     => _x( 'Defect Description', 'shopmark', 'woocommerce-germanized' ),
				'deposit'                => _x( 'Deposit', 'shopmark', 'woocommerce-germanized' ),
				'deposit_packaging_type' => _x( 'Type of Packaging', 'shopmark', 'woocommerce-germanized' ),
				'nutri_score'            => _x( 'Nutri-Score', 'shopmark', 'woocommerce-germanized' ),
			),
			'order'                  => array(
				'unit_price'             => _x( 'Unit Price', 'shopmark', 'woocommerce-germanized' ),
				'units'                  => _x( 'Product Units', 'shopmark', 'woocommerce-germanized' ),
				'delivery_time'          => _x( 'Delivery Time', 'shopmark', 'woocommerce-germanized' ),
				'item_desc'              => _x( 'Cart Description', 'shopmark', 'woocommerce-germanized' ),
				'defect_description'     => _x( 'Defect Description', 'shopmark', 'woocommerce-germanized' ),
				'deposit'                => _x( 'Deposit', 'shopmark', 'woocommerce-germanized' ),
				'deposit_packaging_type' => _x( 'Type of Packaging', 'shopmark', 'woocommerce-germanized' ),
				'nutri_score'            => _x( 'Nutri-Score', 'shopmark', 'woocommerce-germanized' ),
			),
		);

		$type_data = isset( $types[ $location ] ) ? $types[ $location ] : array();

		/**
		 * Filter to adjust available shopmark types e.g. unit_price for a certain location.
		 *
		 * The dynamic portion of the hook name, `$location` refers to the
		 * shopmark location e.g. single_product
		 *
		 * @param array $hook_names Array containing available shopmark types.
		 *
		 * @since 3.0.0
		 *
		 */
		return apply_filters( "woocommerce_gzd_shopmark_{$location}_types", $type_data );
	}

	public static function get_type_title( $location, $type ) {
		$types = self::get_types( $location );

		return isset( $types[ $type ] ) ? $types[ $type ] : '';
	}

	public static function get_filter_title( $location, $filter_name ) {
		$filter = self::get_filter( $location, $filter_name );

		return $filter ? $filter['title'] : '';
	}

	public static function get_filter( $location, $filter_name ) {
		$filters = self::get_filters( $location );

		return isset( $filters[ $filter_name ] ) ? $filters[ $filter_name ] : false;
	}

	public static function get_filter_options( $location ) {
		$filters = self::get_filters( $location );
		$options = array();

		foreach ( $filters as $filter => $data ) {
			$options[ $filter ] = $data['title'];
		}

		return $options;
	}

	public static function register( $location = '' ) {
		if ( ! empty( $location ) && is_callable( array( __CLASS__, 'register_' . $location ) ) ) {
			$getter = 'register_' . $location;

			self::$getter();
		} else {
			foreach ( self::get_locations() as $location_type => $title ) {
				$getter = 'register_' . $location_type;

				self::$getter();
			}
		}
	}

	/**
	 * @return Shopmark[]
	 */
	public static function get( $location = '' ) {
		if ( ! empty( $location ) ) {
			$data = array();

			if ( array_key_exists( $location, self::get_locations() ) ) {
				if ( ! isset( self::$shopmarks[ $location ] ) ) {
					self::register( $location );
				}

				$data = isset( self::$shopmarks[ $location ] ) ? self::$shopmarks[ $location ] : array();
			}
		} else {
			$data = self::$shopmarks;
		}

		return $data;
	}
}
