<?php

/**
 * Save the product data meta box.
 *
 * @access public
 *
 * @param int   $post_id
 * @param array $data
 *
 * @throws WC_Data_Exception
 * @return void
 */
function dokan_process_product_meta( int $post_id, array $data = [] ) {
    if ( ! $post_id || ! $data ) {
        return;
    }

    global $woocommerce_errors;

    $product_type = empty( $data['product_type'] ) ? 'simple' : sanitize_text_field( $data['product_type'] );

    // Add any default post meta
    add_post_meta( $post_id, 'total_sales', '0', true );

    $is_downloadable = isset( $data['_downloadable'] ) ? 'yes' : 'no';
    $is_virtual      = isset( $data['_virtual'] ) ? 'yes' : 'no';

    // Product type + Downloadable/Virtual
    update_post_meta( $post_id, '_downloadable', $is_downloadable );
    update_post_meta( $post_id, '_virtual', $is_virtual );

    // Gallery Images
    if ( isset( $data['product_image_gallery'] ) ) {
        $data = apply_filters( 'dokan_restrict_product_image_gallery_on_edit', $data );

        $attachment_ids = array_filter( explode( ',', wc_clean( $data['product_image_gallery'] ) ) );
        update_post_meta( $post_id, '_product_image_gallery', implode( ',', $attachment_ids ) );
    }

    // Check product visibility and purchase note
    $data['_visibility']    = isset( $data['_visibility'] ) ? sanitize_text_field( $data['_visibility'] ) : '';
    $data['_purchase_note'] = isset( $data['_purchase_note'] ) ? sanitize_textarea_field( $data['_purchase_note'] ) : '';

    // Set visibility for WC 3.0.0+
    $terms = [];

    switch ( $data['_visibility'] ) {
        case 'hidden':
            $terms[] = 'exclude-from-search';
            $terms[] = 'exclude-from-catalog';
            break;
        case 'catalog':
            $terms[] = 'exclude-from-search';
            break;
        case 'search':
            $terms[] = 'exclude-from-catalog';
            break;
    }

    $product_visibility = get_the_terms( $post_id, 'product_visibility' );
    $term_names         = is_array( $product_visibility ) ? wp_list_pluck( $product_visibility, 'name' ) : [];
    $featured           = in_array( 'featured', $term_names, true );

    if ( $featured ) {
        $terms[] = 'featured';
    }

    wp_set_post_terms( $post_id, $terms, 'product_visibility' );
    update_post_meta( $post_id, '_visibility', $data['_visibility'] );

    // Update post meta
    if ( isset( $data['_regular_price'] ) ) {
        update_post_meta( $post_id, '_regular_price', ( $data['_regular_price'] === '' ) ? '' : wc_format_decimal( $data['_regular_price'] ) );
    }

    if ( isset( $data['_sale_price'] ) ) {
        //if regular price is lower than sale price then we are setting it to empty
        if ( (float) wc_format_decimal( $data['_regular_price'] ) <= (float) wc_format_decimal( $data['_sale_price'] ) ) {
            $data['_sale_price'] = '';
        }

        update_post_meta( $post_id, '_sale_price', ( $data['_sale_price'] === '' ? '' : wc_format_decimal( $data['_sale_price'] ) ) );
    }

    // Update post meta
    if ( isset( $data['_tax_status'] ) ) {
        update_post_meta( $post_id, '_tax_status', wc_clean( $data['_tax_status'] ) );
    }

    if ( isset( $data['_tax_class'] ) ) {
        update_post_meta( $post_id, '_tax_class', wc_clean( $data['_tax_class'] ) );
    }

    if ( isset( $data['_purchase_note'] ) ) {
        update_post_meta( $post_id, '_purchase_note', wp_kses_post( $data['_purchase_note'] ) );
    }

    // Save Attributes
    $attributes = [];

    if ( isset( $data['attribute_names'] ) && is_array( $data['attribute_names'] ) && isset( $data['attribute_values'] ) && is_array( $data['attribute_values'] ) ) {
        $attribute_names  = array_map( 'wc_clean', $data['attribute_names'] );
        $attribute_values = array_map(
            function ( $value ) {
                return $value;
            }, $data['attribute_values']
        );

        if ( isset( $data['attribute_visibility'] ) ) {
            $attribute_visibility = array_map( 'absint', $data['attribute_visibility'] );
        }

        if ( isset( $data['attribute_variation'] ) ) {
            $attribute_variation = array_map( 'absint', $data['attribute_variation'] );
        }

        $attribute_is_taxonomy   = array_map( 'absint', $data['attribute_is_taxonomy'] );
        $attribute_position      = array_map( 'absint', $data['attribute_position'] );
        $attribute_names_max_key = max( array_keys( $attribute_names ) );

        for ( $i = 0; $i <= $attribute_names_max_key; $i++ ) {
            if ( empty( $attribute_names[ $i ] ) ) {
                continue;
            }

            $is_visible   = isset( $attribute_visibility[ $i ] ) ? 1 : 0;
            $is_variation = isset( $attribute_variation[ $i ] ) ? 1 : 0;
            $is_taxonomy  = $attribute_is_taxonomy[ $i ] ? 1 : 0;

            if ( $is_taxonomy ) {
                if ( isset( $attribute_values[ $i ] ) ) {

                    // Select based attributes - Format values (posted values are slugs)
                    if ( is_array( $attribute_values[ $i ] ) ) {
                        $values = $attribute_values[ $i ]; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

                        // Text based attributes - Posted values are term names, wp_set_object_terms wants ids or slugs.
                    } else {
                        $values     = [];
                        $raw_values = explode( WC_DELIMITER, $attribute_values[ $i ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

                        foreach ( $raw_values as $value ) {
                            $term = get_term_by( 'name', $value, $attribute_names[ $i ] );
                            if ( ! $term ) {
                                $term = wp_insert_term( $value, $attribute_names[ $i ] );

                                if ( $term && ! is_wp_error( $term ) ) {
                                    $values[] = $term['term_id'];
                                }
                            } else {
                                $values[] = $term->term_id;
                            }
                        }
                    }

                    // Remove empty items in the array
                    $values = array_filter( $values, 'strlen' );
                } else {
                    $values = [];
                }

                // Update post terms
                if ( taxonomy_exists( $attribute_names[ $i ] ) ) {
                    wp_set_object_terms( $post_id, $values, $attribute_names[ $i ] );
                }

                if ( ! empty( $values ) ) {
                    // Add attribute to array, but don't set values
                    $attributes[ $attribute_names[ $i ] ] = [
                        'name'         => $attribute_names[ $i ],
                        'value'        => '',
                        'position'     => $attribute_position[ $i ],
                        'is_visible'   => $is_visible,
                        'is_variation' => $is_variation,
                        'is_taxonomy'  => $is_taxonomy,
                    ];
                }
            } elseif ( isset( $attribute_values[ $i ] ) ) {

                // Text based, possibly separated by pipes (WC_DELIMITER). Preserve line breaks in non-variation attributes.
                $values = implode( ' ' . WC_DELIMITER . ' ', array_map( 'wc_clean', array_map( 'stripslashes', $attribute_values[ $i ] ) ) );

                // Custom attribute - Add attribute to array and set the values
                $attributes[ $attribute_names[ $i ] ] = [
                    'name'         => $attribute_names[ $i ],
                    'value'        => $values,
                    'position'     => $attribute_position[ $i ],
                    'is_visible'   => $is_visible,
                    'is_variation' => $is_variation,
                    'is_taxonomy'  => $is_taxonomy,
                ];
            }
        }
    }

    uasort( $attributes, 'wc_product_attribute_uasort_comparison' );

    /**
     * Unset removed attributes by looping over previous values and
     * unsetting the terms.
     */
    $old_attributes = array_filter( (array) maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) ) );

    if ( ! empty( $old_attributes ) ) {
        foreach ( $old_attributes as $key => $value ) {
            if ( empty( $attributes[ $key ] ) && ! empty( $value['is_taxonomy'] ) && taxonomy_exists( $key ) ) {
                wp_set_object_terms( $post_id, [], $key );
            }
        }
    }

    update_post_meta( $post_id, '_product_attributes', $attributes );

    if ( in_array( $product_type, [ 'variable', 'grouped' ], true ) ) {
        // Variable and grouped products have no prices
        update_post_meta( $post_id, '_regular_price', '' );
        update_post_meta( $post_id, '_sale_price', '' );
        update_post_meta( $post_id, '_sale_price_dates_from', '' );
        update_post_meta( $post_id, '_sale_price_dates_to', '' );
    } else {
        // Sales and prices
        $date_from     = (string) isset( $data['_sale_price_dates_from'] ) ? wc_clean( $data['_sale_price_dates_from'] ) : '';
        $date_to       = (string) isset( $data['_sale_price_dates_to'] ) ? wc_clean( $data['_sale_price_dates_to'] ) : '';
        $regular_price = (string) isset( $data['_regular_price'] ) ? wc_clean( $data['_regular_price'] ) : '';
        $sale_price    = (string) isset( $data['_sale_price'] ) ? wc_clean( $data['_sale_price'] ) : '';
        $now           = dokan_current_datetime();

        // Update price if on sale
        if ( '' !== $sale_price && '' === $date_to && '' === $date_from ) {
            update_post_meta( $post_id, '_price', wc_format_decimal( $sale_price ) );
        } elseif ( '' !== $sale_price && $date_from && $now->modify( $date_from )->getTimestamp() <= $now->getTimestamp() ) {
            update_post_meta( $post_id, '_price', wc_format_decimal( $sale_price ) );
        } else {
            update_post_meta( $post_id, '_price', '' === $regular_price ? '' : wc_format_decimal( $regular_price ) );
        }

        //update product price if date to is smaller than current date
        if ( $date_to && $now->modify( $date_to )->getTimestamp() < $now->getTimestamp() ) {
            update_post_meta( $post_id, '_price', $regular_price );
        }
    }

    //enable reviews
    $comment_status = 'closed';

    if ( 'yes' === $data['_enable_reviews'] ) {
        $comment_status = 'open';
    }

    // Update the post into the database
    wp_update_post(
        [
            'ID'             => $post_id,
            'comment_status' => $comment_status,
        ]
    );

    // Sold Individually
    $sold_individually = ! empty( $data['_sold_individually'] ) && 'yes' === $data['_sold_individually'] ? 'yes' : 'no';
    update_post_meta( $post_id, '_sold_individually', $sold_individually );

    // Stock Data
    $manage_stock      = ! empty( $data['_manage_stock'] ) && 'grouped' !== $product_type ? 'yes' : 'no';
    $backorders        = ! empty( $data['_backorders'] ) && 'yes' === $manage_stock ? wc_clean( $data['_backorders'] ) : 'no';
    $stock_status      = ! empty( $data['_stock_status'] ) ? wc_clean( $data['_stock_status'] ) : 'instock';
    $stock_amount      = isset( $data['_stock'] ) ? wc_clean( $data['_stock'] ) : '';
    $stock_amount      = 'yes' === $manage_stock ? wc_stock_amount( wp_unslash( $stock_amount ) ) : '';
    $_low_stock_amount = isset( $data['_low_stock_amount'] ) ? wc_clean( $data['_low_stock_amount'] ) : '';
    $_low_stock_amount = 'yes' === $manage_stock ? wc_stock_amount( wp_unslash( $_low_stock_amount ) ) : '';

    // Stock Data
    if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {
        $manage_stock = 'no';
        $backorders   = 'no';
        $stock_status = wc_clean( $data['_stock_status'] );
        if ( 'external' === $product_type ) {
            $stock_status = 'instock';
        } elseif ( 'variable' === $product_type ) {
            // Stock status is always determined by children so sync later
            $stock_status = '';
            if ( ! empty( $data['_manage_stock'] ) && $data['_manage_stock'] === 'yes' ) {
                $manage_stock = 'yes';
                $backorders   = wc_clean( $data['_backorders'] );
            }
        } elseif ( 'grouped' !== $product_type && ! empty( $data['_manage_stock'] ) ) {
            $manage_stock = $data['_manage_stock'];
            $backorders   = wc_clean( $data['_backorders'] );
        }

        update_post_meta( $post_id, '_manage_stock', $manage_stock );
        update_post_meta( $post_id, '_backorders', $backorders );
        if ( $stock_status ) {
            try {
                wc_update_product_stock_status( $post_id, $stock_status );
            } catch ( Exception $ex ) {
                dokan_log( 'product stock update exception' );
            }
        }

        // Retrieve original stock value from the hidden field
        $original_stock = isset( $data['_original_stock'] ) ? wc_stock_amount( wc_clean( $data['_original_stock'] ) ) : '';
        // Clean the current stock value
        $stock_amount = isset( $data['_stock'] ) ? wc_clean( $data['_stock'] ) : '';
        $stock_amount = 'yes' === $manage_stock ? wc_stock_amount( wp_unslash( $stock_amount ) ) : '';
        // Only update the stock amount if it has changed
        if ( $original_stock != $stock_amount ) {
            if ( 'variable' === $product_type ) {
                update_post_meta( $post_id, '_stock', $stock_amount );
            } else {
                wc_update_product_stock( $post_id, $stock_amount );
            }
        }

        // Update low stock amount regardless of stock changes
        $_low_stock_amount = isset( $data['_low_stock_amount'] ) ? wc_clean( $data['_low_stock_amount'] ) : '';
        $_low_stock_amount = 'yes' === $manage_stock ? wc_stock_amount( wp_unslash( $_low_stock_amount ) ) : '';
        update_post_meta( $post_id, '_low_stock_amount', $_low_stock_amount );
    } else {
        wc_update_product_stock_status( $post_id, wc_clean( $data['_stock_status'] ) );
    }

    // Downloadable options
    if ( 'yes' === $is_downloadable ) {
        $_download_limit = intval( $data['_download_limit'] );

        if ( ! $_download_limit || -1 === $_download_limit ) {
            $_download_limit = ''; // 0 or blank = unlimited
        }

        $_download_expiry = intval( $data['_download_expiry'] );
        if ( ! $_download_expiry || -1 === $_download_expiry ) {
            $_download_expiry = ''; // 0 or blank = unlimited
        }

        // file paths will be stored in an array keyed off md5(file path)
        if ( isset( $data['_wc_file_urls'] ) ) {
            $files = [];

            $file_names    = isset( $data['_wc_file_names'] ) ? array_map( 'wc_clean', $data['_wc_file_names'] ) : [];
            $file_urls     = array_map( 'esc_url_raw', array_map( 'trim', $data['_wc_file_urls'] ) );
            $file_url_size = count( $file_urls );

            for ( $i = 0; $i < $file_url_size; $i++ ) {
                if ( ! empty( $file_urls[ $i ] ) ) {
                    $files[ md5( $file_urls[ $i ] ) ] = [
                        'name' => $file_names[ $i ],
                        'file' => $file_urls[ $i ],
                    ];
                }
            }

            // grant permission to any newly added files on any existing orders for this product prior to saving
            do_action( 'dokan_process_file_download', $post_id, 0, $files );

            update_post_meta( $post_id, '_downloadable_files', $files );
        } else {
            update_post_meta( $post_id, '_downloadable_files', '' );
        }

        update_post_meta( $post_id, '_download_limit', $_download_limit );
        update_post_meta( $post_id, '_download_expiry', $_download_expiry );

        if ( isset( $data['_download_limit'] ) ) {
            update_post_meta( $post_id, '_download_limit', sanitize_text_field( $_download_limit ) );
        }
        if ( isset( $data['_download_expiry'] ) ) {
            update_post_meta( $post_id, '_download_expiry', sanitize_text_field( $_download_expiry ) );
        }

        if ( isset( $data['_download_type'] ) ) {
            update_post_meta( $post_id, '_download_type', wc_clean( $data['_download_type'] ) );
        }
    }

    // Update SKU
    $old_sku = get_post_meta( $post_id, '_sku', true );
    delete_post_meta( $post_id, '_sku' );

    $product = wc_get_product( $post_id );

    $sku = trim( wp_unslash( $data['_sku'] ) ) !== '' ? sanitize_text_field( wp_unslash( $data['_sku'] ) ) : '';
    try {
        $product->set_sku( $sku );
    } catch ( WC_Data_Exception $e ) {
        $product->set_sku( $old_sku );
        $woocommerce_errors[] = __( 'Product SKU must be unique', 'dokan-lite' );
    }

    // Set Sales and prices
    $product->set_regular_price( $regular_price );
    $product->set_sale_price( $sale_price );

    // Site timezone
    $tz_string = wc_timezone_string();
    $timezone  = $tz_string ? new DateTimeZone( $tz_string ) : new DateTimeZone( 'UTC' );

    // Sale starting date
    if ( ! empty( $date_from ) ) {
        try {
            $from_dt = new WC_DateTime( $date_from . ' 00:00:00', $timezone );
            $product->set_date_on_sale_from( $from_dt );
        } catch ( Exception $e ) {
            error_log( 'Invalid date_from: ' . $date_from . ' | ' . $e->getMessage() );
            $product->set_date_on_sale_from( null );
        }
    } else {
        $product->set_date_on_sale_from( null );
    }

    // Sale ending date
    if ( ! empty( $date_to ) ) {
        try {
            $to_dt = new WC_DateTime( $date_to . ' 23:59:59', $timezone );
            $product->set_date_on_sale_to( $to_dt );

            if ( empty( $date_from ) ) {
                // Automatically add date of today if start date is empty
                $from_obj = new WC_DateTime( 'now', $timezone );
                $product->set_date_on_sale_from( $from_obj );
            }
        } catch ( Exception $e ) {
            error_log( 'Invalid date_to: ' . $date_to . ' | ' . $e->getMessage() );
            $product->set_date_on_sale_to( null );
        }
    } else {
        $product->set_date_on_sale_to( null );
    }

    // save the product
    $product->save();

    // Do action for product type
    do_action( 'woocommerce_process_product_meta_' . $product_type, $post_id );
    do_action( 'dokan_process_product_meta', $post_id );

    // Clear cache/transients
    wc_delete_product_transients( $post_id );
}

/**
 * Grant downloadable file access to any newly added files on any existing.
 * orders for this product that have previously been granted downloadable file access.
 *
 * @param int   $product_id         product identifier
 * @param int   $variation_id       optional product variation identifier
 * @param array $downloadable_files newly set files
 *
 * @deprecated 3.8.0
 *
 * @return void
 */
function dokan_process_product_file_download_paths( int $product_id, int $variation_id, array $downloadable_files ) {
    wc_deprecated_function( 'dokan_process_product_file_download_paths', '3.8.0' );
    global $wpdb;

    if ( $variation_id ) {
        $product_id = $variation_id;
    }

    $product               = wc_get_product( $product_id );
    $existing_download_ids = array_keys( (array) $product->get_files() );
    $updated_download_ids  = array_keys( (array) $downloadable_files );
    $new_download_ids      = array_filter( array_diff( $updated_download_ids, $existing_download_ids ) );
    $removed_download_ids  = array_filter( array_diff( $existing_download_ids, $updated_download_ids ) );

    if ( ! empty( $new_download_ids ) || ! empty( $removed_download_ids ) ) {
        // determine whether downloadable file access has been granted via the typical order completion, or via the admin ajax method
        $permission_query = $wpdb->prepare( "SELECT * from {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE product_id = %d GROUP BY order_id", $product_id );
        $existing_permissions = $wpdb->get_results( $permission_query ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

        foreach ( $existing_permissions as $existing_permission ) {
            $order = wc_get_order( $existing_permission->order_id );

            if ( ! empty( dokan_get_prop( $order, 'id' ) ) ) {
                // Remove permissions
                if ( ! empty( $removed_download_ids ) ) {
                    foreach ( $removed_download_ids as $download_id ) {
                        if ( apply_filters( 'woocommerce_process_product_file_download_paths_remove_access_to_old_file', true, $download_id, $product_id, $order ) ) {
                            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                            $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE order_id = %d AND product_id = %d AND download_id = %s", dokan_get_prop( $order, 'id' ), $product_id, $download_id ) );
                        }
                    }
                }
                // Add permissions
                if ( ! empty( $new_download_ids ) ) {
                    foreach ( $new_download_ids as $download_id ) {
                        if ( apply_filters( 'woocommerce_process_product_file_download_paths_grant_access_to_new_file', true, $download_id, $product_id, $order ) ) {
                            // grant permission if it doesn't already exist
                            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                            if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT 1=1 FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE order_id = %d AND product_id = %d AND download_id = %s", dokan_get_prop( $order, 'id' ), $product_id, $download_id ) ) ) {
                                wc_downloadable_file_permission( $download_id, $product_id, $order );
                            }
                        }
                    }
                }
            }
        }
    }
}

/**
 * Get discount coupon total from an order
 *
 * @param int $order_id
 *
 * @deprecated 3.8.0
 *
 * @return int
 */
function dokan_sub_order_get_total_coupon( int $order_id ): int {
    wc_deprecated_function( 'dokan_sub_order_get_total_coupon', '3.8.0' );
    global $wpdb;

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $result = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT SUM(oim.meta_value) FROM {$wpdb->prefix}woocommerce_order_itemmeta oim
            LEFT JOIN {$wpdb->prefix}woocommerce_order_items oi ON oim.order_item_id = oi.order_item_id
            WHERE oi.order_id = %d AND oi.order_item_type = 'coupon'",
            $order_id
        )
    );

    if ( $result ) {
        return $result;
    }

    return 0;
}

/**
 * Change seller display name to store name
 *
 * @since 2.4.10 [Change seller display name to store name]
 *
 * @param string $display_name
 *
 * @return string $display_name
 */
function dokan_seller_displayname( $display_name ) {
    if ( current_user_can( 'seller' ) && ! is_admin() ) {
        $seller_info  = dokan_get_store_info( dokan_get_current_user_id() );
        $display_name = ( ! empty( $seller_info['store_name'] ) ) ? $seller_info['store_name'] : $display_name;
    }

    return $display_name;
}

/**
 * Get featured products
 *
 * Shown on homepage
 *
 * @param int $per_page
 *
 * @return WP_Query
 */
function dokan_get_featured_products( $per_page = 9, $seller_id = '', $page = 1 ) {
    $args = [
        'posts_per_page'      => $per_page,
        'paged'               => $page,
        'post_status'         => 'publish',
        'ignore_sticky_posts' => 1,
        'tax_query'           => [ //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
            'relation' => 'AND',
        ],
    ];

    if ( ! empty( $seller_id ) ) {
        $args['author'] = (int) $seller_id;
    }

    return dokan()->product->featured( apply_filters( 'dokan_get_featured_products', $args ) );
}

/**
 * Get the latest products
 *
 * Shown on homepage
 *
 * @param int $per_page
 *
 * @return WP_Query
 */
function dokan_get_latest_products( $per_page = 9, $seller_id = '', $page = 1 ) {
    $args = [
        'posts_per_page'      => $per_page,
        'paged'               => $page,
        'post_status'         => 'publish',
        'orderby'             => 'publish_date',
        'ignore_sticky_posts' => 1,
    ];

    if ( ! empty( $seller_id ) ) {
        $args['author'] = (int) $seller_id;
    }

    return dokan()->product->latest( apply_filters( 'dokan_get_latest_products', $args ) );
}

/**
 * Get best-selling products
 *
 * Shown on homepage
 *
 * @param int $per_page
 *
 * @return WP_Query
 */
function dokan_get_best_selling_products( $per_page = 8, $seller_id = '', $page = 1, $hide_outofstock = false ) {
    $args = [
        'post_type'           => 'product',
        'post_status'         => 'publish',
        'ignore_sticky_posts' => 1,
        'posts_per_page'      => $per_page,
        'paged'               => $page,
    ];

    if ( ! empty( $seller_id ) ) {
        $args['author'] = (int) $seller_id;
    }

    if ( $hide_outofstock ) {
        $args['meta_query'] = [ //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
            [
                'key'     => '_stock_status',
                'value'   => 'outofstock',
                'compare' => '!=',
            ],
        ];
    }

    return dokan()->product->best_selling( apply_filters( 'dokan_best_selling_query', $args ) );
}


/**
 * Check More product from Seller tab is active or not.
 *
 * @since 2.5
 *
 * @return boolean
 */
function check_more_seller_product_tab() {
    return 'on' === dokan_get_option( 'enabled_more_products_tab', 'dokan_general', 'on' );
}

/**
 * Check if Vendor Info tab enabled in single product page.
 *
 * @since 3.9.0
 *
 * @return boolean
 */
function is_enabled_vendor_info_product_tab() {
    return 'on' === dokan_get_option( 'show_vendor_info', 'dokan_general', 'off' );
}

/**
 * Get top-rated products
 *
 * Shown on homepage
 *
 * @param int $per_page
 *
 * @return WP_Query
 */
function dokan_get_top_rated_products( $per_page = 8, $seller_id = '', $page = 1 ) {
    $args = [
        'post_type'           => 'product',
        'post_status'         => 'publish',
        'ignore_sticky_posts' => 1,
        'posts_per_page'      => $per_page,
        'paged'               => $page,
    ];

    if ( ! empty( $seller_id ) ) {
        $args['author'] = (int) $seller_id;
    }

    return dokan()->product->top_rated( apply_filters( 'dokan_top_rated_query', $args ) );
}

/**
 * Get products on-sale
 *
 * Shown on homepage
 *
 * @param int $per_page
 * @param int $paged
 * @param int $seller_id
 *
 * @return WP_Query
 */
function dokan_get_on_sale_products( int $per_page = 10, int $paged = 1, int $seller_id = 0 ): WP_Query {
    // Get products on sale
    $product_ids_on_sale = wc_get_product_ids_on_sale();

    $args = [
        'posts_per_page' => $per_page,
        'no_found_rows'  => 1,
        'paged'          => $paged,
        'post_status'    => 'publish',
        'post_type'      => 'product',
        'post__in'       => array_merge( [ 0 ], $product_ids_on_sale ),
        'meta_query'     => [ //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
            [
                'key'     => '_visibility',
                'value'   => [ 'catalog', 'visible' ],
                'compare' => 'IN',
            ],
            [
                'key'     => '_stock_status',
                'value'   => 'instock',
                'compare' => '=',
            ],
        ],
    ];

    if ( ! empty( $seller_id ) ) {
        $args['author'] = (int) $seller_id;
    }

    return new WP_Query( apply_filters( 'dokan_on_sale_products_query', $args ) );
}

/**
 * Get current balance of a seller
 *
 * Total = SUM(net_amount) - SUM(withdraw)
 *
 * @param int  $seller_id
 * @param bool $formatted
 *
 * @return float|string float if formatted is false, string otherwise
 */
function dokan_get_seller_balance( $seller_id, $formatted = true ) {
    $vendor = dokan()->vendor->get( $seller_id );

    return $vendor->get_balance( $formatted );
}

/**
 * Get Seller Earned amount
 *
 * @since 2.5.4
 *
 * @param boolean $formatted
 * @param string  $on_date
 *
 * @param int     $seller_id
 *
 * @return float|null
 */
function dokan_get_seller_earnings( $seller_id, $formatted = true, $on_date = '' ) {
    $vendor = dokan()->vendor->get( $seller_id );

    if ( $vendor->id === 0 ) {
        return null;
    }

    return $vendor->get_earnings( $formatted, $on_date );
}

/**
 * Get seller rating
 *
 * @param int $seller_id
 *
 * @return array
 */
function dokan_get_seller_rating( $seller_id ) {
    $vendor = dokan()->vendor->get( $seller_id );

    return $vendor->get_rating();
}

/**
 * Get seller rating in a readable rating format
 *
 * @param int $seller_id
 *
 * @return string
 */
function dokan_get_readable_seller_rating( $seller_id ) {
    $vendor = dokan()->vendor->get( $seller_id );

    return $vendor->get_readable_rating( false );
}

add_filter( 'woocommerce_dashboard_status_widget_sales_query', 'dokan_filter_woocommerce_dashboard_status_widget_sales_query' );

/**
 * Woocommerce Admin dashboard Sales Report Synced with Dokan Dashboard report
 *
 * @since  2.4.3
 *
 * @param array $query
 *
 * @return array
 */
function dokan_filter_woocommerce_dashboard_status_widget_sales_query( $query ) {
    global $wpdb;

    $query['where'] .= " AND posts.ID NOT IN ( SELECT post_parent FROM {
    $wpdb->posts} WHERE post_type IN ( '" . implode( "','", array_merge( wc_get_order_types( 'sales-reports' ), [ 'shop_order_refund' ] ) ) . "' ) )";

    return $query;
}

/**
 * Handle password edit and name update functions
 *
 * @since 2.4.10
 *
 * @return void
 */
function dokan_save_account_details() {
    if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'dokan_save_account_details' ) ) {
        return;
    }

    $errors = new WP_Error();
    $user   = new stdClass();

    $user->ID     = (int) get_current_user_id();
    $current_user = get_user_by( 'id', $user->ID );

    if ( $user->ID <= 0 ) {
        return;
    }

    $account_first_name = ! empty( $_POST['account_first_name'] ) ? wc_clean( wp_unslash( $_POST['account_first_name'] ) ) : '';
    $account_last_name  = ! empty( $_POST['account_last_name'] ) ? wc_clean( wp_unslash( $_POST['account_last_name'] ) ) : '';
    $account_email      = ! empty( $_POST['account_email'] ) ? sanitize_email( wp_unslash( $_POST['account_email'] ) ) : '';
    $pass_cur           = ! empty( $_POST['password_current'] ) ? wp_unslash( $_POST['password_current'] ) : ''; // phpcs:ignore
    $pass1              = ! empty( $_POST['password_1'] ) ? wp_unslash( $_POST['password_1'] ) : ''; // phpcs:ignore
    $pass2              = ! empty( $_POST['password_2'] ) ? wp_unslash( $_POST['password_2'] ) : ''; // phpcs:ignore
    $save_pass          = true;

    $user->first_name = $account_first_name;
    $user->last_name  = $account_last_name;

    // Prevent emails being displayed, or leave alone.
    $user->display_name = is_email( $current_user->display_name ) ? $user->first_name : $current_user->display_name;

    // Handle required fields
    $required_fields = apply_filters(
        'woocommerce_save_account_details_required_fields', [
            'account_first_name' => __( 'First Name', 'dokan-lite' ),
            'account_last_name'  => __( 'Last Name', 'dokan-lite' ),
            'account_email'      => __( 'Email address', 'dokan-lite' ),
        ]
    );

    foreach ( $required_fields as $field_key => $field_name ) {
        if ( empty( $_POST[ $field_key ] ) ) {
            wc_add_notice( '<strong>' . esc_html( $field_name ) . '</strong> ' . __( 'is a required field.', 'dokan-lite' ), 'error' );
        }
    }

    if ( $account_email ) {
        if ( ! is_email( $account_email ) ) {
            wc_add_notice( __( 'Please provide a valid email address.', 'dokan-lite' ), 'error' );
        } elseif ( email_exists( $account_email ) && $account_email !== $current_user->user_email ) {
            wc_add_notice( __( 'This email address is already registered.', 'dokan-lite' ), 'error' );
        }
        $user->user_email = $account_email;
    }

    if ( ! empty( $pass1 ) && ! wp_check_password( $pass_cur, $current_user->user_pass, $current_user->ID ) ) {
        wc_add_notice( __( 'Your current password is incorrect.', 'dokan-lite' ), 'error' );
        $save_pass = false;
    }

    if ( ! empty( $pass_cur ) && empty( $pass1 ) && empty( $pass2 ) ) {
        wc_add_notice( __( 'Please fill out all password fields.', 'dokan-lite' ), 'error' );
        $save_pass = false;
    } elseif ( ! empty( $pass1 ) && empty( $pass_cur ) ) {
        wc_add_notice( __( 'Please enter your current password.', 'dokan-lite' ), 'error' );
        $save_pass = false;
    } elseif ( ! empty( $pass1 ) && empty( $pass2 ) ) {
        wc_add_notice( __( 'Please re-enter your password.', 'dokan-lite' ), 'error' );
        $save_pass = false;
    } elseif ( ( ! empty( $pass1 ) || ! empty( $pass2 ) ) && $pass1 !== $pass2 ) {
        wc_add_notice( __( 'New passwords do not match.', 'dokan-lite' ), 'error' );
        $save_pass = false;
    }

    if ( $pass1 && $save_pass ) {
        $user->user_pass = $pass1;
    }

    // Allow plugins to return their own errors.
    do_action_ref_array( 'woocommerce_save_account_details_errors', [ &$errors, &$user ] );

    if ( $errors->get_error_messages() ) {
        foreach ( $errors->get_error_messages() as $error ) {
            wc_add_notice( $error, 'error' );
        }
    }

    if ( wc_notice_count( 'error' ) === 0 ) {
        wp_update_user( $user );

        wc_add_notice( __( 'Account details changed successfully.', 'dokan-lite' ) );

        do_action( 'woocommerce_save_account_details', $user->ID );

        wp_safe_redirect( dokan_get_navigation_url( 'edit-account' ) );
        exit;
    }
}

add_action( 'template_redirect', 'dokan_save_account_details' );

/**
 * Remove banner when without banner layout selected for profile
 *
 * @param array $progress_values
 *
 * @return array
 */
function dokan_split_profile_completion_value( $progress_values ) {
    $store_banner = dokan_get_option( 'store_header_template', 'dokan_appearance' );

    if ( 'layout3' === $store_banner ) {
        unset( $progress_values['banner_val'] );

        $progress_values['store_name_val'] = 15;
        $progress_values['phone_val']      = 15;
        $progress_values['address_val']    = 15;
    }

    return $progress_values;
}

add_filter( 'dokan_profile_completion_values', 'dokan_split_profile_completion_value', 10 );

/**
 * Set More products from seller tab on Single Product Page
 *
 * @since 2.5
 *
 * @param array $tabs
 *
 * @return array
 */
function dokan_set_more_from_seller_tab( $tabs ) {
    if ( check_more_seller_product_tab() ) {
        $tabs['more_seller_product'] = [
            'title'    => __( 'More Products', 'dokan-lite' ),
            'priority' => 99,
            'callback' => 'dokan_get_more_products_from_seller',
        ];
    }

    return $tabs;
}

add_action( 'woocommerce_product_tabs', 'dokan_set_more_from_seller_tab', 10 );

/**
 * Show more products from current seller
 *
 * @since 2.5
 * @since 3.2.2 added filter 'dokan_get_more_products_per_page'
 *
 * @param int|string $seller_id
 * @param int|string $posts_per_page
 *
 * @return void
 */
function dokan_get_more_products_from_seller( $seller_id = 0, $posts_per_page = 6 ) {
    global $product, $post;

    if ( $seller_id === 0 || 'more_seller_product' === $seller_id ) {
        $seller_id = $post->post_author;
    }

    if ( ! is_int( $posts_per_page ) ) {
        $posts_per_page = apply_filters( 'dokan_get_more_products_per_page', 6 );
    }

    $args = [
        'post_type'      => 'product',
        'posts_per_page' => $posts_per_page,
        'orderby'        => 'rand',
        'post__not_in'   => [ $post->ID ],
        'author'         => $seller_id,
    ];

    $products = new WP_Query( $args );

    if ( $products->have_posts() ) {
        woocommerce_product_loop_start();

        while ( $products->have_posts() ) {
            $products->the_post();
            wc_get_template_part( 'content', 'product' );
        }

        woocommerce_product_loop_end();
    } else {
        esc_html_e( 'No product has been found!', 'dokan-lite' );
    }

    wp_reset_postdata();
}

/**
 * Keep old vendor after duplicate any product
 *
 * @param WC_Product $duplicate
 * @param WC_Product $product
 *
 * @return void
 */
function dokan_keep_old_vendor_woocommerce_duplicate_product( $duplicate, $product ) {
    $old_author = get_post_field( 'post_author', $product->get_id() );
    $new_author = get_post_field( 'post_author', $duplicate->get_id() );

    if ( absint( $old_author ) === absint( $new_author ) ) {
        return;
    }

    dokan_override_product_author( $duplicate, absint( $old_author ) );
}

add_action( 'woocommerce_product_duplicate', 'dokan_keep_old_vendor_woocommerce_duplicate_product', 35, 2 );

/**
 * @since 3.7.24
 *
 * @param boolean $is_purchasable
 * @param object $product
 *
 * @return boolean
 */
function dokan_vendor_own_product_purchase_restriction( bool $is_purchasable, $product ): bool {
    if ( false === $is_purchasable || dokan_is_product_author( $product->get_id() ) ) {
        $is_purchasable = false;
    }

    /**
     * Determines if a vendor can purchase their own products.
     *
     * This filter allows altering the purchasable status of a product based on whether
     * the vendor is attempting to purchase their own product. It can be used to restrict
     * or allow such purchases according to business rules.
     *
     * @since 3.10.3
     *
     * @param bool    $is_purchasable Indicates if the product is purchasable. True by default.
     * @param WP_Post $product        The product object being evaluated for purchasability.
     *
     * @return bool Modified purchasability status.
     */
    return apply_filters( 'dokan_vendor_own_product_purchase_restriction', $is_purchasable, $product );
}

add_filter( 'woocommerce_is_purchasable', 'dokan_vendor_own_product_purchase_restriction', 10, 2 );

/**
 * Restricts vendor from reviewing own product
 *
 * @since 3.7.24
 *
 * @param array $data
 * @return array
 */
function dokan_vendor_product_review_restriction( array $data ): array {
    global $product;
    if ( ! is_user_logged_in() ) {
        return $data;
    }
    if ( dokan_is_product_author( $product->get_id() ) ) {
        $data['title_reply'] = __( 'Reviews cannot be posted for products that you own.', 'dokan-lite' );
        $data['comment_field'] = '';
        $data['fields'] = [];
        $data['submit_field'] = '';
        $data['submit_button'] = '';
    }
    return $data;
}
add_filter( 'woocommerce_product_review_comment_form_args', 'dokan_vendor_product_review_restriction' );
