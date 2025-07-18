<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Order_Export_Order_Fields {
    /**
     * @var WC_Order
     */
	var $order;
	var $order_type;
	var $is_refund;
	var $order_id;
	var $parent_order;
	var $main_order;
	var $order_status;
	var $order_meta;
	var $billing_country;
	var $shipping_country;
	var $billing_state;
	var $shipping_state;
	var $user;
	var $data;
	var $post;
	var $static_vals;
	var $options;
	var $export;

	public function __construct($order, $static_vals,
	$options, $export) {
		$legacy_mode = ( get_option("woocommerce_custom_orders_table_enabled") != 'yes');
		$this->static_vals = $static_vals;
		$this->options = $options;
		$this->export = $export;
		$this->order = $order;
		$this->order_id = method_exists( $this->order, 'get_id' ) ? $order->get_id() : $order->id;

		$order_data_store = WC_Data_Store::load( 'order' );
		$this->order_type = $order_data_store->get_order_type( $this->order_id );
		$this->is_refund = ( $this->order_type == 'shop_order_refund' );

        //for refunds
		$parent_order_id = method_exists( $this->order,
			'get_parent_id' ) ? $this->order->get_parent_id() : $this->order->post->post_parent;
		$this->parent_order    = $parent_order_id ? new WC_Order( $parent_order_id ) : false;
		$this->post            = method_exists( $this->order, 'get_id' ) ? get_post( $this->order->get_id() ) : $this->order->post;

		//fix if refund has NO parent!
		//we need it as main_order calls functions from class WC_Order
		if( $this->is_refund AND !$this->parent_order) {
			$this->order = new WC_Order($this->order_id);
		}

		//address details from this order
		$this->main_order  = $this->parent_order ? $this->parent_order : $this->order;

		// get order meta
		$this->order_meta = array();
		if ( !$legacy_mode ) {// HPOS
			foreach ( $this->order->get_meta_data() as $meta_data ) {
                $meta_key = $meta_data->key;
                $meta_value = $meta_data->value;
                if ( is_array($meta_value) OR is_object($meta_value) )
					$meta_value = json_encode($meta_value);
				if( !isset($this->order_meta[$meta_key]) )
					$this->order_meta[$meta_key] = $meta_value;
				elseif (!apply_filters('woe_use_first_order_meta', false))
                     $this->order_meta[$meta_key] .= WC_Order_Export_Data_Extractor::$export_custom_fields_separator . $meta_value;
			}
		} else { //Legacy
			foreach ( get_post_meta( $this->order_id ) as $meta_key => $meta_values ) {
				if( apply_filters('woe_use_first_order_meta', false) )
					$this->order_meta[ $meta_key ] = array_shift( $meta_values );
				else
					$this->order_meta[ $meta_key ] = join( WC_Order_Export_Data_Extractor::$export_custom_fields_separator, $meta_values );
			}
		}

        // get billing email via wc method that needed for other fields, if it isn't in meta
		if (!isset($this->order_meta['_billing_email'])) {
			if( $this->is_refund )
				$this->order_meta['_billing_email'] =  $this->parent_order ? $this->parent_order->get_billing_email() : '';
			else
				$this->order_meta['_billing_email'] =  $this->order->get_billing_email();
        }

		// correct meta for child orders
		if ( $parent_order_id ) {
			// overwrite child values for refunds
			$overwrite_child_order_meta = apply_filters( 'woe_overwrite_child_order_meta', $this->is_refund );

			if ( !$legacy_mode ) { //HPOS
				//reformat $parent_order_meta
				$formatted_order_meta = array();
				foreach( $this->parent_order->get_meta_data() as $parent_meta) {
					$key = $parent_meta->key;
					$value = $parent_meta->value;
					if( !is_string($value))
						$value = json_encode($value);
					if( !isset($formatted_order_meta[$key]) )
						$formatted_order_meta[$key] = array($value);
					else
						$formatted_order_meta[$key][] = $value;
				}
				foreach ( $formatted_order_meta as $meta_key => $meta_values ) {
					if ( $overwrite_child_order_meta OR ! isset( $this->order_meta[ $meta_key ] ) ) {
						$this->order_meta[ $meta_key ] = join( WC_Order_Export_Data_Extractor::$export_custom_fields_separator, $meta_values );
					}
				}
			} else { //Legacy
				foreach (get_post_meta( $parent_order_id ) as $meta_key => $meta_values ) {
					if ( $overwrite_child_order_meta OR ! isset( $this->order_meta[ $meta_key ] ) ) {
						$this->order_meta[ $meta_key ] = join( WC_Order_Export_Data_Extractor::$export_custom_fields_separator, $meta_values );
					}
				}
			}

			//refund status
			if ( $this->is_refund ) {
				$this->order_status = 'refunded';
			}
		}

		// add fields for WC 3.0
		$billing_fields  = array( "billing_country", "billing_state" );
		$shipping_fields = array( "shipping_country", "shipping_state" );
		$fields_30       = array_merge( $billing_fields, $shipping_fields );
		foreach ( $fields_30 as $field_30 ) {
			$this->$field_30 = method_exists( $this->main_order,
				'get_' . $field_30 ) ? $this->main_order->{'get_' . $field_30}() : $this->main_order->$field_30;

		}

		// extra WP_User
		$this->user = ! empty( $this->main_order->get_customer_id() ) ? get_userdata( $this->main_order->get_customer_id() ) : false;
		// setup missed fields for full addresses
		$optional_billing_fields = array( '_billing_address_1', '_billing_address_2', '_billing_first_name', '_billing_company', '_billing_last_name', '_billing_city', '_billing_postcode', '_billing_country', '_billing_state', '_billing_phone' );
		$optional_shipping_fields = array( '_shipping_address_1', '_shipping_address_2', '_shipping_first_name', '_shipping_company', '_shipping_last_name', '_shipping_city', '_shipping_postcode', '_shipping_country', '_shipping_state', '_shipping_phone' );
		$optional_fields = array_merge( $optional_billing_fields, $optional_shipping_fields );
		foreach ( $optional_fields as $optional_field ) {
			if ( ! isset( $this->order_meta[ $optional_field ] ) AND method_exists( $this->main_order, 'get'.$optional_field) ) {
				$this->order_meta[ $optional_field ] = $this->main_order->{'get'.$optional_field}();
			}
		}

		//method WC_Order::has_shipping_address checks only these 2 fields, so we have to add filter
		$has_shipping_address = false;
		$has_shipping_validate_keys = apply_filters( "woe_has_shipping_validate_keys", array( "_shipping_address_1", "_shipping_address_2" ) );
		foreach($has_shipping_validate_keys as $shippping_key ) {
			if( !empty($this->main_order->{'get'.$shippping_key}()) )
				$has_shipping_address = true;
		}
		if ( $this->options['billing_details_for_shipping'] && !$has_shipping_address ) {
			$this->set_shipping_fields( $optional_shipping_fields );
		}

		$this->order_meta = apply_filters( 'woe_fetch_order_meta', $this->order_meta, $this->order_id );
		//$optional_billing_fields = array( 'billing_country', 'billing_state', '_billing_address_1', '_billing_address_2', '_billing_first_name', '_billing_last_name', '_billing_city', '_billing_postcode', '_billing_country', '_billing_state' );
	}

	public function set_shipping_fields( $shippings_fields ) {

	    foreach ( $shippings_fields as $shipping_field ) {

		$billing_field = str_replace( "shipping_", "billing_", $shipping_field );

		if( isset($this->order_meta[ $billing_field ]) )
			$this->order_meta[ $shipping_field ] = $this->order_meta[ $billing_field ];

		$_shipping_field = substr($shipping_field, 1);
		$_billing_field  = substr($billing_field, 1);
		if (method_exists( $this->main_order, 'set_' . $_shipping_field )) {
		    $this->main_order->{ 'set_' . $_shipping_field }( $this->main_order->{ 'get_' . $_billing_field }() );
		} elseif( isset($this->order_meta[ $billing_field ]) ) {
		    $this->main_order->$_shipping_field = $this->order_meta[ $billing_field ];
		}
	    }

	    $this->shipping_country = $this->billing_country;
	    $this->shipping_state   = $this->billing_state;
	}

	public function set_data($data) {
		$this->data = $data;
	}

	public function get_parent_order() {
		return $this->parent_order;
	}

	public function get_one_field($field) {
		$row = array( $field=>'');
		$row = $this->get($row, $field);
		return $row[$field];
	}

	public function get($row, $field) {
		global $wpdb;
		global $wp_roles;

		if ( substr( $field, 0, 5 ) == "USER_" ) { //user field
			$key = substr( $field, 5 );
			$row[$field] =  $this->user ? $this->user->get( $key ) : '';
		} elseif ( substr( $field, 0, 4 ) == "FEE_" ) {

			$key = substr( $field, 4 );

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$value = $wpdb->get_col( $wpdb->prepare(
			"SELECT
				itemmeta.meta_value
			FROM
				{$wpdb->prefix}woocommerce_order_items items
			INNER JOIN
				{$wpdb->prefix}woocommerce_order_itemmeta itemmeta
			ON
				items.order_item_id = itemmeta.order_item_id AND itemmeta.meta_key = '_line_total'
			WHERE
				items.order_id = %s AND items.order_item_type = 'fee' AND items.order_item_name = %s",
			$this->order_id,
			$key
			) );

			$row[$field] =  isset($value[0]) ? $value[0] : '';

		} elseif ( substr( $field, 0, 9 ) == "SHIPPING_" ) {

			$key = substr( $field, 9 );

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$value = $wpdb->get_col( $wpdb->prepare(
			"SELECT
				itemmeta.meta_value
			FROM
				{$wpdb->prefix}woocommerce_order_items items
			INNER JOIN
				{$wpdb->prefix}woocommerce_order_itemmeta itemmeta
			ON
				items.order_item_id = itemmeta.order_item_id AND itemmeta.meta_key = 'cost'
			WHERE
				items.order_id = %s AND items.order_item_type = 'shipping' AND items.order_item_name = %s",
			$this->order_id,
			$key
			) );

			$row[$field] =  isset($value[0]) ? $value[0] : '';

		} elseif ( substr( $field, 0, 4 ) == "TAX_" ) {

			$key = substr( $field, 4 );

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$value = $wpdb->get_col( $wpdb->prepare(
			"SELECT
				SUM(itemmeta.meta_value)
			FROM
				{$wpdb->prefix}woocommerce_order_items items
			INNER JOIN
				{$wpdb->prefix}woocommerce_order_itemmeta itemmeta
			ON
				items.order_item_id = itemmeta.order_item_id AND (itemmeta.meta_key = 'tax_amount' OR itemmeta.meta_key = 'shipping_tax_amount')
			WHERE
				items.order_id = %s AND items.order_item_type = 'tax' AND items.order_item_name = %s",
			$this->order_id,
			$key
			) );

			$row[$field] =  isset($value[0]) ? $value[0] : '';

		} elseif ( $field == 'order_id' ) {
			$row[$field] =  $this->order_id;
		} elseif ( $field == 'origin' ) {
			$row[$field] =  WC_Order_Export_Data_Extractor::get_origin_for_order($this->main_order);
		} elseif ( $field == 'order_date' ) {
			$row[$field] =  ! method_exists( $this->order,
				"get_date_created" ) ? $this->order->order_date : ( $this->order->get_date_created() ? gmdate( 'Y-m-d H:i:s',
				$this->order->get_date_created()->getOffsetTimestamp() ) : '' );
        } elseif ( $field == 'orig_order_date' ) {
            $parent_id = $this->order->get_parent_id();
            if( $parent_id ) {
                $parent_order = wc_get_order($parent_id);
                $row[$field] = $parent_order->get_date_created()->format("Y-m-d H:i");
            }
        } elseif ( $field == 'modified_date' ) {
			$row[$field] = ! method_exists( $this->order,
				"get_date_modified" ) ? $this->order->modified_date : ( $this->order->get_date_modified() ? gmdate( 'Y-m-d H:i:s',
				$this->order->get_date_modified()->getOffsetTimestamp() ) : '' );
		} elseif ( $field == 'completed_date' ) {
			$row[$field] = ! method_exists( $this->order,
				"get_date_completed" ) ? $this->order->completed_date : ( $this->order->get_date_completed() ? gmdate( 'Y-m-d H:i:s',
				$this->order->get_date_completed()->getOffsetTimestamp() ) : '' );
		} elseif ( $field == 'paid_date' ) {
			$row[$field] = ! method_exists( $this->order,
				"get_date_paid" ) ? $this->order->paid_date : ( $this->order->get_date_paid() ? gmdate( 'Y-m-d H:i:s',
				$this->order->get_date_paid()->getOffsetTimestamp() ) : '' );
		} elseif ( $field == 'order_number' ) {
			$row[$field] = $this->parent_order ? $this->parent_order->get_order_number() : $this->order->get_order_number(); // use parent order number
		} elseif ( $field == 'order_subtotal' ) {
			$row[$field] = wc_round_tax_total( $this->order->get_subtotal() );
		} elseif ( $field == 'order_subtotal_plus_cart_tax' ) {
			$row[$field] = wc_round_tax_total( $this->order->get_subtotal() + floatval($this->order->get_cart_tax()) );
		} elseif ( $field == 'order_subtotal_minus_discount' ) {
			$row[$field] = $this->order->get_subtotal() - $this->order->get_total_discount();
		} elseif ( $field == 'order_subtotal_refunded' AND !$this->is_refund ) {
			$row[$field] = wc_round_tax_total( WC_Order_Export_Data_Extractor::get_order_subtotal_refunded( $this->order ) );
		} elseif ( $field == 'order_subtotal_minus_refund' AND !$this->is_refund ) {
			$row[$field] = wc_round_tax_total( $this->order->get_subtotal() - WC_Order_Export_Data_Extractor::get_order_subtotal_refunded( $this->order ) );
			//order total
		} elseif ( $field == 'order_total' ) {
			$row[$field] = $this->order->get_total();
		} elseif ( $field == 'order_total_no_tax' ) {
			$row[$field] = $this->order->get_total() - $this->order->get_total_tax();
		} elseif ( $field == 'order_refund'  AND !$this->is_refund) {
			$row[$field] = $this->order->get_total_refunded();
		} elseif ( $field == 'order_total_inc_refund'  AND !$this->is_refund) {
			$row[$field] = $this->order->get_total() - $this->order->get_total_refunded();
			//shipping
		} elseif ( $field == 'order_shipping' ) {
			$row[$field] = method_exists($this->order,"get_shipping_total") ? $this->order->get_shipping_total() : $this->order->get_total_shipping();
		} elseif ( $field == 'order_shipping_plus_tax' ) {
			$row[$field] = ( method_exists($this->order,"get_shipping_total") ? floatval($this->order->get_shipping_total()) : floatval( $this->order->get_total_shipping() ) ) + floatval( $this->order->get_shipping_tax() );
		} elseif ( $field == 'order_shipping_refunded' AND !$this->is_refund ) {
			$row[$field] = $this->order->get_total_shipping_refunded();
		} elseif ( $field == 'order_shipping_minus_refund'  AND !$this->is_refund ) {
			$row[$field] = floatval( method_exists($this->order,"get_shipping_total") ? $this->order->get_shipping_total() : $this->order->get_total_shipping() ) - $this->order->get_total_shipping_refunded();
			//shipping tax
		} elseif ($field == 'order_shipping_tax') {
            $row[$field] = $this->order->get_shipping_tax();
        } elseif ( $field == 'order_shipping_tax_refunded'  AND !$this->is_refund ) {
			$row[$field] = WC_Order_Export_Data_Extractor::get_order_shipping_tax_refunded( $this->order_id );
		} elseif ( $field == 'order_shipping_tax_minus_refund' AND !$this->is_refund ) {
			$row[$field] = floatval($this->order->get_shipping_tax()) - WC_Order_Export_Data_Extractor::get_order_shipping_tax_refunded( $this->order_id );
			//order tax
		} elseif ( $field == 'order_tax' ) {
			$row[$field] = wc_round_tax_total( $this->order->get_cart_tax() );
		} elseif ( $field == 'order_total_fee' ) {
			$row[ $field ] = array_sum( array_map( function ( $item ) {
				return $item->get_total();
			}, $this->order->get_fees() ) );
		} elseif ( $field == 'order_total_tax' ) {
			$row[$field] = wc_round_tax_total( $this->order->get_total_tax() );
		} elseif ( $field == 'order_total_tax_refunded' AND !$this->is_refund ) {
			$row[$field] = wc_round_tax_total( $this->order->get_total_tax_refunded() );
		} elseif ( $field == 'order_total_tax_minus_refund' AND !$this->is_refund ) {
			$row[$field] = wc_round_tax_total( $this->order->get_total_tax() - $this->order->get_total_tax_refunded() );
		} elseif ( $field == 'order_status' ) {
			$status        = empty( $this->order_status ) ? $this->order->get_status() : $this->order_status;
			$status        = 'wc-' === substr( $status, 0, 3 ) ? substr( $status, 3 ) : $status;
			$row[$field] = isset( WC_Order_Export_Data_Extractor::$statuses[ 'wc-' . $status ] ) ? WC_Order_Export_Data_Extractor::$statuses[ 'wc-' . $status ] : $status;
		} elseif ( $field == 'user_login' OR $field == 'user_email' OR $field == 'user_url' ) {
			$row[$field] = $this->user ? $this->user->$field : "";
		} elseif ( $field == 'user_role' ) {
			$roles         = $wp_roles->roles;
			if( $this->user ) {
				$role = reset($this->user->roles); // take first role Name
				$row[$field] =  isset( $roles[ $role ] ) ? $roles[ $role ]['name'] : $role;
				$row[$field] =  translate_user_role( $row[$field] );
			}
			else
				$row[$field] =  "";
		} elseif ( $field == 'returning_customer' ) {
			$row[$field] = $this->get_returning_customer( $this->order_id );
		} elseif ($field == 'customer_user') {
            $row[$field] = isset ($this->user->ID) ? $this->user->ID : 0;
        } elseif ( $field == 'customer_total_orders' ) {
			$row[$field] = ( isset( $this->user->ID ) ) ? wc_get_customer_order_count( $this->user->ID ) : WC_Order_Export_Data_Extractor::get_customer_order_count_by_email( $this->order_meta["_billing_email"] );
		} elseif ( $field == 'customer_paid_orders' ) {
			$row[$field] = WC_Order_Export_Data_Extractor::get_customer_paid_orders_count( isset($this->user->ID) ? $this->user->ID : false, $this->order_meta["_billing_email"] );
		} elseif ( $field == 'customer_total_spent' ) {
			$row[$field] = ( isset( $this->user->ID ) ) ? wc_get_customer_total_spent( $this->user->ID ) : WC_Order_Export_Data_Extractor::get_customer_total_spent_by_email( $this->order_meta["_billing_email"] );
		} elseif ( $field == 'customer_first_order_date' ) {
			$first_order = WC_Order_Export_Data_Extractor::get_customer_order( $this->user, $this->order_meta, 'first' );
			$row[$field] = $first_order ? ( $first_order->get_date_created() ? gmdate( 'Y-m-d H:i:s',
				$first_order->get_date_created()->getOffsetTimestamp() ) : '' ) : '';
		} elseif ( $field == 'customer_last_order_date' ) {
			$last_order = WC_Order_Export_Data_Extractor::get_customer_order( $this->user, $this->order_meta, 'last' );
			$row[$field] = $last_order? ( $last_order->get_date_created() ? gmdate( 'Y-m-d H:i:s',
				$last_order->get_date_created()->getOffsetTimestamp() ) : '' ) : '';
		} elseif ( $field == 'billing_address' ) {
			$row[$field] = join( ", ",
				array_filter( array( $this->main_order->get_billing_address_1(), $this->main_order->get_billing_address_2() ) ) );
		} elseif ( $field == 'shipping_address' ) {
			$row[$field] = join( ", ",
				array_filter( array(  $this->main_order->get_shipping_address_1(), $this->main_order->get_shipping_address_2() ) ) );
		} elseif ( $field == 'billing_full_name' ) {
			$row[$field] = trim( $this->main_order->get_billing_first_name() . ' ' . $this->main_order->get_billing_last_name() );
		} elseif ( $field == 'shipping_full_name' ) {
			$row[$field] = trim( $this->main_order->get_shipping_first_name() . ' ' . $this->main_order->get_shipping_last_name() );
		} elseif ( $field == 'billing_country_full' ) {
			$row[$field] = isset( WC_Order_Export_Data_Extractor::$countries[ $this->billing_country ] ) ? WC_Order_Export_Data_Extractor::$countries[ $this->billing_country ] : $this->billing_country;
		} elseif ( $field == 'shipping_country_full' ) {
			$row[$field] = isset( WC_Order_Export_Data_Extractor::$countries[ $this->shipping_country ] ) ? WC_Order_Export_Data_Extractor::$countries[ $this->shipping_country ] : $this->shipping_country;
		} elseif ( $field == 'billing_state_full' ) {
			$country_states = WC()->countries->get_states( $this->billing_country );
			$row[$field] = isset( $country_states[ $this->billing_state ] ) ? html_entity_decode( $country_states[ $this->billing_state ] ) : $this->billing_state;
		} elseif ( $field == 'shipping_state_full' ) {
			$country_states = WC()->countries->get_states( $this->shipping_country );
			$row[$field] = isset( $country_states[ $this->shipping_state ] ) ? html_entity_decode( $country_states[ $this->shipping_state ] ) : $this->shipping_state;
		} elseif ( $field == 'billing_citystatezip' ) {
			$row[$field] = WC_Order_Export_Data_Extractor::get_city_state_postcode_field_value( $this->main_order, 'billing' );
		} elseif ($field == 'fulladdress_shipping') {
			$row[$field] = str_replace("<br/>", "\n", $this->main_order->get_formatted_shipping_address());
		}elseif ($field == 'fulladdress_billing') {
			$row[$field] = str_replace("<br/>", "\n", $this->main_order->get_formatted_billing_address());
		} elseif ( $field == 'billing_citystatezip_us' ) {
			$row[$field] = WC_Order_Export_Data_Extractor::get_city_state_postcode_field_value( $this->main_order, 'billing', true );
		} elseif ( $field == 'shipping_citystatezip' ) {
			$row[$field] = WC_Order_Export_Data_Extractor::get_city_state_postcode_field_value( $this->main_order, 'shipping' );
		} elseif ( $field == 'shipping_citystatezip_us' ) {
			$row[$field] = WC_Order_Export_Data_Extractor::get_city_state_postcode_field_value( $this->main_order, 'shipping', true );
		} elseif ( $field == 'products' OR $field == 'coupons' ) {
			if ( isset( $this->data[ $field ] ) ) {
				$row[$field] = $this->data[ $field ];
			}
		} elseif ( $field == 'shipping_method_title' ) {
			$row[$field] = $this->main_order->get_shipping_method();
		} elseif ( $field == 'shipping_method' OR $field == 'shipping_method_only') {
			$shipping_methods = $this->main_order->get_items( 'shipping' );
			$shipping_method  = reset( $shipping_methods ); // take first entry
			if ( ! empty( $shipping_method ) ) {
				$row[$field] = $field == 'shipping_method_only' ? $shipping_method['method_id'] : $shipping_method['method_id'] . ':' . $shipping_method['instance_id'];
			}
		} elseif ( $field == 'shipping_zone' ) {
			$row[$field] = WC_Order_Export_Data_Extractor::get_shipping_zone($this->main_order);
		} elseif ( $field == 'coupons_used' ) {
			$row[$field] = count( $this->data['coupons'] );
		} elseif ( $field == 'total_weight_items' ) {
			$total_weight = 0;
			foreach ( $this->data['products'] as $product ) {
				$total_weight += (float) $product['qty'] * (float) $product['weight'];
			}
			$row[$field] = $total_weight;
		} elseif ( $field == 'count_total_items' ) {
			$row[$field] = $this->order->get_item_count();
		} elseif ( $field == 'count_exported_items' ) {
			$count = 0; // count only exported!
			if ( $this->export['products'] ) {
				foreach ( $this->data['products'] as $product ) {
					$count += $product['qty'];
				}
				$row[$field] = $count;
			}
		} elseif ( $field == 'count_unique_products' ) { // speed! replace with own counter ?
            $row[$field] = count( $this->data['products'] );
        } elseif ( $field == 'total_volume' ) {
            $value = 0;
            foreach ( $this->order->get_items() as $item ) {
                $product   = $item->get_product();
                if ( !$product )  continue;
                $value +=  $item->get_quantity() * floatval($product->get_width()) * floatval($product->get_height()) * floatval($product->get_length());
            }
            $row[$field] = $value;
		} elseif ( $field == 'customer_note' AND !$this->is_refund) {
			$notes = array( $this->order->get_customer_note() );
			if ( $this->options['export_refund_notes'] ) {
				$refunds = $this->order->get_refunds();
				foreach ( $refunds as $refund ) {
					// added get_reason for WC 3.0
					$notes[] = method_exists( $refund,
						'get_reason' ) ? $refund->get_reason() : $refund->get_refund_reason();
				}
			}
			$row[$field] = implode( "\n", array_filter( $notes ) );
		} elseif ( $field == 'first_refund_date' AND !$this->is_refund) {
			$value = '';
			foreach ( $this->order->get_refunds() as $refund ) {
				if(!$refund) continue;//bug -- we get null object?
				$value = ! method_exists( $refund,
					"get_date_created" ) ? $refund->date : ( $refund->get_date_created() ? gmdate( 'Y-m-d H:i:s',
					$refund->get_date_created()->getOffsetTimestamp() ) : '' );
				break;// take only first
			}
			$row[$field] = $value;
		} elseif ( isset( $this->static_vals['order'][ $field ] ) ) {
			$row[$field] = $this->static_vals['order'][ $field ];
		} elseif ( $field == 'order_notes' ) {
			remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10 );
			$args  = array(
				'post_id' => $this->order_id,
				'approve' => 'approve',
				'type'    => 'order_note',
			);
			$notes = get_comments( $args );
			add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );
			$comments = array();
			if ( $notes ) {
				foreach ( $notes as $note ) {
					if ( ! empty( $this->options['export_all_comments'] ) || $note->comment_author !== __( 'WooCommerce',
							'woo-order-export-lite' ) ) { // skip system notes by default
						$comments[] = apply_filters( 'woe_get_order_notes', $note->comment_content, $note, $this->order );
					}
				}
			}
			$row[$field] = implode( "\n", array_filter( $comments ) );
		} elseif ( $field == 'embedded_edit_order_link' ) {
			$post_type_object = get_post_type_object( $this->order_type );
			if ( $post_type_object AND $post_type_object->_edit_link){
				$edit_link = admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=edit', $this->order_id ) );
				$row[$field] = sprintf(
				'<a href="%s" target="_blank">%s</a>',
				$edit_link,
				__( 'Edit order', 'woo-order-export-lite' )
				);
			}
		} elseif ( $field == 'subscription_relationship' AND function_exists("wcs_order_contains_subscription")) {
			//copied logic from class WC_Subscriptions_Order
			if ( wcs_order_contains_subscription( $this->order_id, 'renewal' ) ) {
				$row[$field] = __( 'Renewal Order', 'woo-order-export-lite' );
			} elseif ( wcs_order_contains_subscription( $this->order_id, 'resubscribe' ) ) {
				$row[$field] = __( 'Resubscribe Order', 'woo-order-export-lite' );
			} elseif ( wcs_order_contains_subscription( $this->order_id, 'parent' ) ) {
				$row[$field] = __( 'Parent Order', 'woo-order-export-lite' );
			} else {
				$row[$field] = "";
			}
		} elseif ( $field == 'order_currency' ) {
			$row[$field] = $this->order->get_currency();
		} elseif( $field == 'order_currency_symbol' ){
			$row[$field] = $this->get_woocommerce_currency_symbol( $this->order->get_currency() );
		} elseif ($field == 'cart_discount') {
            $row[$field] = $this->order->get_discount_total();
		} elseif ($field == 'cart_discount_inc_tax') {
            $row[$field] = (float)$this->order->get_discount_total() + (float)$this->order->get_discount_tax();
        } elseif ($field == 'cart_discount_tax') {
            $row[$field] = $this->order->get_discount_tax();
        } elseif( method_exists( $this->order, 'get_' . $field ) ) {  // order_date...
				if ( $this->order_type == 'shop_order_refund' AND $this->parent_order )
					$row[$field] = $this->parent_order->{'get_' . $field}(); //use main order details for refund
				else
					$row[$field] = $this->order->{'get_' . $field}();
		} elseif ( isset( $this->order_meta[ $field ] ) ) {
			$field_data = array();
			do_action( 'woocommerce_order_export_add_field_data', $field_data, $this->order_meta[ $field ], $field );
			if ( empty( $field_data ) ) {
				$field_data[ $field ] = $this->order_meta[ $field ];
			}
			$row = array_merge( $row, $field_data );
		} elseif ( isset( $this->order_meta[ "_" . $field ] ) ) { // or hidden field
			$row[$field] = $this->order_meta[ "_" . $field ];
		} else { // order_date...
            $row[$field] = $this->order->get_meta('_' . $field);
		}
		return $row;

	}

	function get_woocommerce_currency_symbol( $currency = '' ) {
		if ( ! $currency ) {
			$currency = get_woocommerce_currency();
		}

		$symbols = apply_filters( 'woocommerce_currency_symbols', array(
			'AED' => 'د.إ',
			'AFN' => '؋',
			'ALL' => 'L',
			'AMD' => 'AMD',
			'ANG' => 'ƒ',
			'AOA' => 'Kz',
			'ARS' => '$',
			'AUD' => '$',
			'AWG' => 'ƒ',
			'AZN' => 'AZN',
			'BAM' => 'KM',
			'BBD' => '$',
			'BDT' => '৳ ',
			'BGN' => 'лв.',
			'BHD' => '.د.ب',
			'BIF' => 'Fr',
			'BMD' => '$',
			'BND' => '$',
			'BOB' => 'Bs.',
			'BRL' => 'R$',
			'BSD' => '$',
			'BTC' => '฿',
			'BTN' => 'Nu.',
			'BWP' => 'P',
			'BYR' => 'Br',
			'BZD' => '$',
			'CAD' => '$',
			'CDF' => 'Fr',
			'CHF' => 'CHF',
			'CLP' => '$',
			'CNY' => '¥',
			'COP' => '$',
			'CRC' => '₡',
			'CUC' => '$',
			'CUP' => '$',
			'CVE' => '$',
			'CZK' => 'Kč',
			'DJF' => 'Fr',
			'DKK' => 'DKK',
			'DOP' => 'RD$',
			'DZD' => 'د.ج',
			'EGP' => 'EGP',
			'ERN' => 'Nfk',
			'ETB' => 'Br',
			'EUR' => '€',
			'FJD' => '$',
			'FKP' => '£',
			'GBP' => '£',
			'GEL' => 'ლ',
			'GGP' => '£',
			'GHS' => '₵',
			'GIP' => '£',
			'GMD' => 'D',
			'GNF' => 'Fr',
			'GTQ' => 'Q',
			'GYD' => '$',
			'HKD' => '$',
			'HNL' => 'L',
			'HRK' => 'Kn',
			'HTG' => 'G',
			'HUF' => 'Ft',
			'IDR' => 'Rp',
			'ILS' => '₪',
			'IMP' => '£',
			'INR' => '₹',
			'IQD' => 'ع.د',
			'IRR' => '﷼',
			'IRT' => 'تومان',
			'ISK' => 'kr.',
			'JEP' => '£',
			'JMD' => '$',
			'JOD' => 'د.ا',
			'JPY' => '¥',
			'KES' => 'KSh',
			'KGS' => 'сом',
			'KHR' => '៛',
			'KMF' => 'Fr',
			'KPW' => '₩',
			'KRW' => '₩',
			'KWD' => 'د.ك',
			'KYD' => '$',
			'KZT' => 'KZT',
			'LAK' => '₭',
			'LBP' => 'ل.ل',
			'LKR' => 'රු',
			'LRD' => '$',
			'LSL' => 'L',
			'LYD' => 'ل.د',
			'MAD' => 'د.م.',
			'MDL' => 'MDL',
			'MGA' => 'Ar',
			'MKD' => 'ден',
			'MMK' => 'Ks',
			'MNT' => '₮',
			'MOP' => 'P',
			'MRO' => 'UM',
			'MUR' => '₨',
			'MVR' => '.ރ',
			'MWK' => 'MK',
			'MXN' => '$',
			'MYR' => 'RM',
			'MZN' => 'MT',
			'NAD' => '$',
			'NGN' => '₦',
			'NIO' => 'C$',
			'NOK' => 'kr',
			'NPR' => '₨',
			'NZD' => '$',
			'OMR' => 'ر.ع.',
			'PAB' => 'B/.',
			'PEN' => 'S/.',
			'PGK' => 'K',
			'PHP' => '₱',
			'PKR' => '₨',
			'PLN' => 'zł',
			'PRB' => 'р.',
			'PYG' => '₲',
			'QAR' => 'ر.ق',
			'RMB' => '¥',
			'RON' => 'lei',
			'RSD' => 'дин.',
			'RUB' => '₽',
			'RWF' => 'Fr',
			'SAR' => 'ر.س',
			'SBD' => '$',
			'SCR' => '₨',
			'SDG' => 'ج.س.',
			'SEK' => 'kr',
			'SGD' => '$',
			'SHP' => '£',
			'SLL' => 'Le',
			'SOS' => 'Sh',
			'SRD' => '$',
			'SSP' => '£',
			'STD' => 'Db',
			'SYP' => 'ل.س',
			'SZL' => 'L',
			'THB' => '฿',
			'TJS' => 'ЅМ',
			'TMT' => 'm',
			'TND' => 'د.ت',
			'TOP' => 'T$',
			'TRY' => '₺',
			'TTD' => '$',
			'TWD' => 'NT$',
			'TZS' => 'Sh',
			'UAH' => '₴',
			'UGX' => 'UGX',
			'USD' => '$',
			'UYU' => '$',
			'UZS' => 'UZS',
			'VEF' => 'Bs F',
			'VND' => '₫',
			'VUV' => 'Vt',
			'WST' => 'T',
			'XAF' => 'Fr',
			'XCD' => '$',
			'XOF' => 'Fr',
			'XPF' => 'Fr',
			'YER' => '﷼',
			'ZAR' => 'R',
			'ZMW' => 'ZK',
	) );

		$currency_symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '';

		return apply_filters( 'woocommerce_currency_symbol', $currency_symbol, $currency );
	}

	function get_returning_customer( $order_id ){
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$returning_customer = $wpdb->get_var(
			$wpdb->prepare("SELECT returning_customer FROM {$wpdb->prefix}wc_order_stats WHERE order_id = %d",$order_id )
		);
		if ($returning_customer === "1")
			$value = __( 'Returning', 'woo-order-export-lite' );
		elseif ($returning_customer === "0")
			$value = __( 'New', 'woo-order-export-lite' );
		else
			$value = __( 'Unknown', 'woo-order-export-lite' );
		return $value;
	}
}
