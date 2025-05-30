<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CR_Reviews_Product_Feed' ) ):

	class CR_Reviews_Product_Feed {

		/**
		* @var CR_Product_Feed_Admin_Menu The instance of the admin menu
		*/
		protected $product_feed_menu;
		protected $tab;
		protected $settings;
		private $prod_atts;

		public function __construct( $product_feed_menu ) {
			$this->product_feed_menu = $product_feed_menu;
			$this->tab = 'reviews';

			add_filter( 'cr_productfeed_tabs', array( $this, 'register_tab' ) );
			add_action( 'cr_productfeed_display_' . $this->tab, array( $this, 'display' ) );
			add_action( 'cr_save_productfeed_' . $this->tab, array( $this, 'save' ) );
			add_action( 'woocommerce_admin_field_ivole_field_map', array( $this, 'display_field_map' ) );
			add_filter( 'woocommerce_admin_settings_sanitize_option_ivole_google_field_map', array( $this, 'sanitize_field_map' ) );
		}

		public function register_tab( $tabs ) {
			$tabs[$this->tab] = __( 'Reviews', 'customer-reviews-woocommerce' );
			return $tabs;
		}

		public function display() {
			$this->prod_atts = CR_Attributes_Product_Feed::get_product_attributes();
			$this->init_settings();
			WC_Admin_Settings::output_fields( $this->settings );
		}

		public function save() {
			$this->init_settings();
			// make sure that there the minimum length of reviews is not less than 0
			if( !empty( $_POST ) && isset( $_POST['ivole_google_min_review_length'] ) ) {
				if( $_POST['ivole_google_min_review_length'] <= 0 ) {
					$_POST['ivole_google_min_review_length'] = 0;
				}
			}
			WC_Admin_Settings::save_fields( $this->settings );

			$feed = new CR_Google_Shopping_Prod_Feed();
			if ( $feed->is_enabled() ) {
				$feed->activate();
			} else {
				$feed->deactivate();
			}

			$feed_reviews = new CR_Google_Shopping_Feed();
			if ( $feed_reviews->is_enabled() ) {
				$feed_reviews->activate();
			} else {
				$feed_reviews->deactivate();
			}
		}

		protected function init_settings() {
			$field_map = get_option( 'ivole_google_field_map', array(
				'gtin'  => '',
				'mpn'   => '',
				'sku'   => '',
				'brand' => ''
			) );

			$this->settings = array(
				array(
					'title' => __( 'Reviews XML Feed', 'customer-reviews-woocommerce' ),
					'type'  => 'title',
					'desc'  => __( 'Google Shopping is a service that allows merchants to list their products by uploading a product feed in the <a href="https://merchants.google.com/">Merchant Center</a>. Use XML Product Review Feed to enable star ratings for your products in Google Shopping.', 'customer-reviews-woocommerce' ),
					'id'    => 'cr_reviews_xml'
				),
				array(
					'title' => __( 'Variable Product Identifiers', 'customer-reviews-woocommerce' ),
					'type' => 'select',
					'desc' => __( 'Google permits sharing of reviews between variable products and their variations. Use this setting to specify if identifiers (such as GTIN, MPN or SKU) of variable products themselves, their variations or both will be linked to reviews in the XML feed.', 'customer-reviews-woocommerce' ),
					'default'  => 'yes',
					'id' => 'ivole_google_exclude_variable_parent',
					'desc_tip' => true,
					'class'    => 'wc-enhanced-select',
					'options'  => array(
						'no' => 'Include IDs of variable products and their variations',
						'parent' => 'Include IDs of variable products only',
						'yes' => 'Include IDs of variations only'
					),
					'autoload' => false
				),
				array(
					'id'       => 'ivole_google_min_review_length',
					'title'    => __( 'Minimum Length of Reviews', 'customer-reviews-woocommerce' ),
					'desc'     => __( 'Google might reject XML feeds with very short reviews. Use this setting to specify a minimum number of characters that a review should have to be included in the XML feed.', 'customer-reviews-woocommerce' ),
					'default'  => 10,
					'type'     => 'number',
					'desc_tip' => true,
					'autoload' => false
				),
				array(
					'id'        => 'ivole_google_field_map',
					'type'      => 'ivole_field_map',
					'title'     => __( 'Fields Mapping', 'customer-reviews-woocommerce' ),
					'desc'      => __( 'Specify WooCommerce fields that should be mapped to GTIN, MPN, SKU, and Brand fields in XML Product Review Feed for Google Shopping.', 'customer-reviews-woocommerce' ),
					'desc_tip'  => true,
					'field_map' => $field_map,
					'autoload' => false
				),
				array(
					'type' => 'sectionend',
					'id'   => 'cr_reviews_xml'
				)
			);
		}

		public function is_this_tab() {
			return $this->product_feed_menu->is_this_page() && ( $this->product_feed_menu->get_current_tab() === $this->tab );
		}

		public function display_field_map( $options ) {
			$options = wp_parse_args( $options, array(
				'field_map' => array(
					'gtin'  => '',
					'mpn'   => '',
					'sku'   => '',
					'brand' => ''
				)
			) );
			$tmp = CR_Admin::cr_get_field_description( $options );
			$tooltip_html = $tmp['tooltip_html'];
			?>
			<tr valign="top">
				<td colspan="2" style="padding-left:0px;padding-right:0px;padding-bottom:0px;font-weight:600;color:#23282d;">
					<?php echo esc_html( $options['title'] ); ?>
					<?php echo $tooltip_html; ?>
				</td>
			</tr>
			<tr valign="top">
				<td colspan="2" style="padding-left:0px;padding-right:0px;">
					<table class="cr-product-feed-categories widefat">
						<thead>
							<tr>
								<th class="cr-product-feed-categories-th">
									<?php
									esc_html_e( 'XML Feed Field', 'customer-reviews-woocommerce' );
									?>
								</th>
								<th class="cr-product-feed-categories-th">
									<?php
									esc_html_e( 'WooCommerce Field', 'customer-reviews-woocommerce' );
									?>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="cr-product-feed-categories-td">
									<?php echo __( 'GTIN', 'customer-reviews-woocommerce' ); ?>
								</td>
								<td class="cr-product-feed-categories-td">
									<select class="cr-product-feed-identifiers-select" name="ivole_field_wc_target_gtin">
										<option></option>
										<?php foreach ( $this->prod_atts as $attribute_value => $attribute_name ): ?>
											<option value="<?php echo $attribute_value; ?>" <?php if ( $attribute_value == $options['field_map']['gtin'] ) echo "selected"; ?>><?php echo $attribute_name; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
							<tr class="cr-alternate">
								<td class="cr-product-feed-categories-td">
									<?php echo __( 'MPN', 'customer-reviews-woocommerce' ); ?>
								</td>
								<td class="cr-product-feed-categories-td">
									<select class="cr-product-feed-identifiers-select" name="ivole_field_wc_target_mpn">
										<option></option>
										<?php foreach ( $this->prod_atts as $attribute_value => $attribute_name ): ?>
											<option value="<?php echo $attribute_value; ?>" <?php if ( $attribute_value == $options['field_map']['mpn'] ) echo "selected"; ?>><?php echo $attribute_name; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
							<tr>
								<td class="cr-product-feed-categories-td">
									<?php echo __( 'SKU', 'customer-reviews-woocommerce' ); ?>
								</td>
								<td class="cr-product-feed-categories-td">
									<select class="cr-product-feed-identifiers-select" name="ivole_field_wc_target_sku">
										<option></option>
										<?php foreach ( $this->prod_atts as $attribute_value => $attribute_name ): ?>
											<option value="<?php echo $attribute_value; ?>" <?php if ( $attribute_value == $options['field_map']['sku'] ) echo "selected"; ?>><?php echo $attribute_name; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
							<tr class="cr-alternate">
								<td class="cr-product-feed-categories-td">
									<?php echo __( 'Brand', 'customer-reviews-woocommerce' ); ?>
								</td>
								<td class="cr-product-feed-categories-td">
									<select class="cr-product-feed-identifiers-select" name="ivole_field_wc_target_brand">
										<option></option>
										<?php foreach ( $this->prod_atts as $attribute_value => $attribute_name ): ?>
											<option value="<?php echo $attribute_value; ?>" <?php if ( $attribute_value == $options['field_map']['brand'] ) echo "selected"; ?>><?php echo $attribute_name; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<?php
		}

		public function sanitize_field_map( $value ) {
			if ( isset( $_POST['ivole_field_wc_target_gtin'], $_POST['ivole_field_wc_target_mpn'], $_POST['ivole_field_wc_target_sku'], $_POST['ivole_field_wc_target_brand'] ) ) {
				$value = array(
					'gtin'  => sanitize_key( $_POST['ivole_field_wc_target_gtin'] ),
					'mpn'   => sanitize_key( $_POST['ivole_field_wc_target_mpn'] ),
					'sku'   => sanitize_key( $_POST['ivole_field_wc_target_sku'] ),
					'brand' => sanitize_key( $_POST['ivole_field_wc_target_brand'] )
				);
			}
			return $value;
		}

	}

endif;
