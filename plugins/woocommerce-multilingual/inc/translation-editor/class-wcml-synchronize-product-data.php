<?php

use WCML\Terms\SuspendWpmlFiltersFactory;
use WCML\Utilities\DB;
use WCML\Utilities\SyncHash;
use WPML\FP\Fns;
use function WCML\functions\isCli;

class WCML_Synchronize_Product_Data {

	const CUSTOM_FIELD_KEY_SEPARATOR = ':::';

	const PRIORITY_BEFORE_STOCK_EMAIL_TRIGGER = 9;

	/** @var woocommerce_wpml */
	private $woocommerce_wpml;
	/** @var SitePress */
	private $sitepress;
	/** @var WPML_Post_Translation */
	private $post_translations;
	/** @var wpdb */
	private $wpdb;
	/** @var SyncHash */
	private $syncHashManager;

	/**
	 * WCML_Synchronize_Product_Data constructor.
	 *
	 * @param woocommerce_wpml      $woocommerce_wpml
	 * @param SitePress             $sitepress
	 * @param WPML_Post_Translation $post_translations
	 * @param wpdb                  $wpdb
	 * @param SyncHash              $syncHashManager
	 */
	public function __construct( woocommerce_wpml $woocommerce_wpml, SitePress $sitepress, WPML_Post_Translation $post_translations, wpdb $wpdb, SyncHash $syncHashManager ) {
		$this->woocommerce_wpml  = $woocommerce_wpml;
		$this->sitepress         = $sitepress;
		$this->post_translations = $post_translations;
		$this->wpdb              = $wpdb;
		$this->syncHashManager   = $syncHashManager;
	}

	public function add_hooks() {
		if ( is_admin() || wpml_is_rest_request() ) {
			add_action( 'icl_pro_translation_completed', [ $this, 'icl_pro_translation_completed' ] );
		}

		if ( is_admin() || isCli() ) {
			// filters to sync variable products.
			add_action( 'save_post', [ $this, 'synchronize_products' ], PHP_INT_MAX, 2 ); // After WPML.

			add_filter( 'icl_make_duplicate', [ $this, 'icl_make_duplicate' ], 110, 4 );

			// quick & bulk edit.
			add_action( 'woocommerce_product_quick_edit_save', [ $this, 'woocommerce_product_quick_edit_save' ] );
			add_action( 'woocommerce_product_bulk_edit_save', [ $this, 'woocommerce_product_quick_edit_save' ] );

			add_action( 'wpml_translation_update', [ $this, 'icl_connect_translations_action' ] );

			add_action( 'deleted_term_relationships', [ $this, 'delete_term_relationships_update_term_count' ], 10, 2 );
		}

		add_action( 'woocommerce_product_set_visibility', [ $this, 'sync_product_translations_visibility' ] );

		add_action( 'woocommerce_product_set_stock', [ $this, 'sync_product_stock_hook' ], self::PRIORITY_BEFORE_STOCK_EMAIL_TRIGGER );
		add_action( 'woocommerce_variation_set_stock', [ $this, 'sync_product_stock_hook' ], self::PRIORITY_BEFORE_STOCK_EMAIL_TRIGGER );
		add_action( 'woocommerce_recorded_sales', [ $this, 'sync_product_total_sales' ] );

		add_action( 'woocommerce_product_set_stock_status', [ $this, 'sync_stock_status_for_translations' ], 100, 2 );
		add_action( 'woocommerce_variation_set_stock_status', [ $this, 'sync_stock_status_for_translations' ], 10, 2 );

		add_filter( 'future_product', [ $this, 'set_schedule_for_translations' ], 10, 2 );
	}

	/**
	 * This function takes care of synchronizing products
	 *
	 * @param int               $post_id
	 * @param WP_Post           $post
	 * @param bool              $force_valid_context
	 * @param ?\WP_REST_Request $wpRestRequest
	 */
	public function synchronize_products( $post_id, $post, $force_valid_context = false, $wpRestRequest = null ) {
		global $pagenow, $wp;

		$original_language   = $this->woocommerce_wpml->products->get_original_product_language( $post_id );
		$current_language    = $this->sitepress->get_current_language();
		$original_product_id = $this->woocommerce_wpml->products->get_original_product_id( $post_id );

		// check its a product.
		$post_type = get_post_type( $post_id );
		// set trid for variations.
		if ( 'product_variation' === $post_type ) {
			$var_lang                   = $this->sitepress->get_language_for_element( wp_get_post_parent_id( $post_id ), 'post_product' );
			$is_parent_original         = $this->woocommerce_wpml->products->is_original_product( wp_get_post_parent_id( $post_id ) );
			$variation_language_details = $this->sitepress->get_element_language_details( $post_id, 'post_product_variation' );
			if ( $is_parent_original && ! $variation_language_details && $var_lang ) {
				$this->sitepress->set_element_language_details( $post_id, 'post_product_variation', false, $var_lang );
			}
		}

		// exceptions.
		/* phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected */
		$ajax_call        = ( ! empty( $_POST['icl_ajx_action'] ) && 'make_duplicates' === $_POST['icl_ajx_action'] );
		$api_call         = ! empty( $wp->query_vars['wc-api-version'] );
		$auto_draft       = 'auto-draft' === $post->post_status;
		$trashing         = isset( $_GET['action'] ) && 'trash' === $_GET['action'];
		$is_valid_context = isCli()
							|| $force_valid_context
							|| $ajax_call
							|| $api_call
							|| in_array( $pagenow, [ 'post.php', 'post-new.php', 'admin.php' ], true );

		if (
			'product' !== $post_type ||
			empty( $original_product_id ) ||
			/* phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected, WordPress.CSRF.NonceVerification.NoNonceVerification */
			isset( $_POST['autosave'] ) ||
			! $is_valid_context ||
			$trashing ||
			$auto_draft
		) {
			return;
		}

		do_action( 'wcml_before_sync_product', $original_product_id, $post_id );

		// trnsl_interface option
		if ( $this->woocommerce_wpml->is_wpml_prior_4_2() ) {
			$is_using_native_editor = ! $this->woocommerce_wpml->settings['trnsl_interface'];
		} else {
			$is_using_native_editor = ! WPML_TM_Post_Edit_TM_Editor_Mode::is_using_tm_editor( $this->sitepress, $original_product_id );
		}

		if ( $is_using_native_editor && $original_language != $current_language ) {
			if ( ! isset( $_POST['wp-preview'] ) || empty( $_POST['wp-preview'] ) ) {
				// make sure we sync post in current language
				$post_id = apply_filters( 'translate_object_id', $post_id, 'product', false, $current_language );
				$this->sync_product_data( $original_product_id, $post_id, $current_language );
			}
			return;
		}

		// update products order
		$this->woocommerce_wpml->products->update_order_for_product_translations( $original_product_id );

		// pick posts to sync
		$translations = $this->post_translations->get_element_translations( $original_product_id, false, true );

		foreach ( $translations as $translation ) {
			$this->sync_product_data( $original_product_id, $translation, $this->post_translations->get_element_lang_code( $translation ) );
		}

		// save custom options for variations
		$this->woocommerce_wpml->sync_variations_data->sync_product_variations_custom_data( $original_product_id );

		if ( $this->woocommerce_wpml->settings['enable_multi_currency'] == WCML_MULTI_CURRENCIES_INDEPENDENT ) {
			// save custom prices
			if( $wpRestRequest instanceof WP_REST_Request ) {
				$this->woocommerce_wpml->multi_currency->custom_prices->save_custom_prices_ajax( $original_product_id, $wpRestRequest );
			} else {
				$this->woocommerce_wpml->multi_currency->custom_prices->save_custom_prices( $original_product_id );
			}
		}

		// save files option
		$this->woocommerce_wpml->downloadable->save_files_option( $original_product_id );

	}

	public function sync_product_data( $original_product_id, $tr_product_id, $lang, $duplicate = false ) {

		do_action( 'wcml_before_sync_product_data', $original_product_id, $tr_product_id, $lang );

		$this->sync_downloadable_files( $original_product_id, $tr_product_id );

		// In general, products do not have a post parent; variatios do. Keeping for legacy safery.
		$this->sync_date_and_parent( $original_product_id, $tr_product_id, $lang );

		$this->woocommerce_wpml->attributes->sync_product_attr( $original_product_id, $tr_product_id, $lang );
		$this->woocommerce_wpml->attributes->sync_default_product_attr( $original_product_id, $tr_product_id, $lang );

		$this->woocommerce_wpml->media->sync_thumbnail_id( $original_product_id, $tr_product_id, $lang );
		$this->woocommerce_wpml->media->sync_product_gallery( $original_product_id, $tr_product_id, $lang );

		$this->sync_product_taxonomies( $original_product_id, $tr_product_id, $lang );

		$this->woocommerce_wpml->sync_variations_data->sync_product_variations( $original_product_id, $tr_product_id, $lang, [ 'is_duplicate' => $duplicate ] );

		$this->sync_linked_products( $original_product_id, $tr_product_id, $lang );

		$this->sync_product_stock( wc_get_product( $original_product_id ), wc_get_product( $tr_product_id ) );

		$wcml_data_store = wcml_product_data_store_cpt();
		$wcml_data_store->update_lookup_table_data( $tr_product_id );

		wc_delete_product_transients( $tr_product_id );

		do_action( 'wcml_after_sync_product_data', $original_product_id, $tr_product_id, $lang );
	}

	public function sync_product_taxonomies( $original_product_id, $tr_product_id, $lang ) {
		$returnTrue = Fns::always( true );
		add_filter( 'wpml_disable_term_adjust_id', $returnTrue );
		$filtersSuspend = SuspendWpmlFiltersFactory::create();

		$taxonomy_exceptions = [ 'product_type', 'product_visibility' ];
		$taxonomies          = $taxonomy_exceptions;
		if ( $this->sitepress->get_setting( 'sync_post_taxonomies' ) ) {
			$taxonomies = get_object_taxonomies( 'product' );
		}

		$found     = false;
		$all_terms = WPML_Non_Persistent_Cache::get( $original_product_id, __CLASS__, $found );
		if ( ! $found ) {
			$all_terms = wp_get_object_terms( $original_product_id, $taxonomies );
			WPML_Non_Persistent_Cache::set( $original_product_id, $all_terms, __CLASS__ );
		}
		if ( ! is_wp_error( $all_terms ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$tt_ids   = [];
				$tt_names = [];
				$terms    = array_filter(
					$all_terms,
					function ( $term ) use ( $taxonomy ) {
						return $term->taxonomy === $taxonomy;
					}
				);
				if ( ! $terms ) {
					continue;
				}
				foreach ( $terms as $term ) {
					if ( in_array( $term->taxonomy, $taxonomy_exceptions, true ) ) {
						$tt_names[] = $term->name;
						continue;
					}
					$tt_ids[] = $term->term_taxonomy_id;
				}

				if ( ! $this->woocommerce_wpml->terms->is_translatable_wc_taxonomy( $taxonomy ) ) {
					wp_set_post_terms( $tr_product_id, $tt_names, $taxonomy );
				} else {
					$this->wcml_update_term_count_by_ids( $tt_ids, $lang, $taxonomy, $tr_product_id );
				}
			}
		}

		remove_filter( 'wpml_disable_term_adjust_id', $returnTrue );
		$filtersSuspend->resume();
	}

	/**
	 * @param int   $object_id
	 * @param int[] $tt_ids    An array of term taxonomy IDs.
	 */
	public function delete_term_relationships_update_term_count( $object_id, $tt_ids ) {

		if ( get_post_type( $object_id ) === 'product' ) {

			$original_product_id = $this->post_translations->get_original_element( $object_id );
			$translations        = $this->post_translations->get_element_translations( $original_product_id, false, true );

			$filtersSuspend = SuspendWpmlFiltersFactory::create();
			foreach ( $translations as $translation ) {
				$this->wcml_update_term_count_by_ids( $tt_ids, $this->post_translations->get_element_lang_code( $translation ) );
			}
			$filtersSuspend->resume();
		}
	}


	/**
	 * @param int[]     $tt_ids    An array of term_taxonomy_id values - NOT term_id values!!!!
	 * @param string    $language
	 * @param string    $taxonomy
	 * @param int|false $tr_product_id
	 */
	public function wcml_update_term_count_by_ids( $tt_ids, $language, $taxonomy = '', $tr_product_id = false ) {
		global $wpml_term_translations;
		$tt_ids_trans = [];

		foreach ( $tt_ids as $tt_id ) {
			// Avoid the translate_object_id filter to escape from the WPML_Term_Translations::maybe_warm_term_id_cache() hell
			// given that we invalidate the cache at every step on wp_set_post_terms().
			$tt_id_trans = $wpml_term_translations->element_id_in( $tt_id, $language );
			if ( $tt_id_trans ) {
				$tt_ids_trans[] = $tt_id_trans;
			}
		}

		$tt_ids_trans = array_values( array_unique( array_map( 'intval', $tt_ids_trans ) ) );
		
		if ( empty( $tt_ids_trans ) ) {
			return;
		}

		if ( in_array( $taxonomy, [ 'product_cat', 'product_tag' ] ) ) {
			$this->sitepress->switch_lang( $language );
			wp_update_term_count( $tt_ids_trans, $taxonomy );
			$this->sitepress->switch_lang();
		}

		if ( $tr_product_id ) {
			$t_ids = $this->wpdb->get_col(
				$this->wpdb->prepare(
					"SELECT term_id FROM {$this->wpdb->term_taxonomy} WHERE term_taxonomy_id IN (" . DB::prepareIn( $tt_ids_trans, '%d' ) . ") LIMIT %d",
					count( $tt_ids_trans )
				)
			);
			// Make sure that $t_ids is int[], otherwise wp_set_post_terms will try to insert new terms for non-hierarchical taxonomies.
			$t_ids = array_unique( array_map( 'intval', $t_ids ) );
			wp_set_post_terms( $tr_product_id, $t_ids, $taxonomy );
		}
	}

	public function sync_linked_products( $product_id, $translated_product_id, $lang ) {

		$this->sync_up_sells_products( $product_id, $translated_product_id, $lang );
		$this->sync_cross_sells_products( $product_id, $translated_product_id, $lang );
		$this->sync_grouped_products( $product_id, $translated_product_id, $lang );

		// refresh parent-children transients (e.g. this child goes to private or draft)
		$translated_product_parent_id = wp_get_post_parent_id( $translated_product_id );
		if ( $translated_product_parent_id ) {
			// Those store the list of variations for a variable product
			// Considering that this is NOT running when syncing variations...
			// ... when is this running, and what for?
			// Keeping for backward compatibility, just in case.
			delete_transient( 'wc_product_children_' . $translated_product_parent_id );
			delete_transient( '_transient_wc_product_children_ids_' . $translated_product_parent_id );
		}

	}

	public function sync_up_sells_products( $product_id, $translated_product_id, $lang ) {

		$original_up_sells = maybe_unserialize( get_post_meta( $product_id, '_upsell_ids', true ) );
		$trnsl_up_sells    = [];
		if ( $original_up_sells ) {
			foreach ( $original_up_sells as $original_up_sell_product ) {
				$trnsl_up_sells[] = apply_filters( 'translate_object_id', $original_up_sell_product, get_post_type( $original_up_sell_product ), false, $lang );
			}
		}
		update_post_meta( $translated_product_id, '_upsell_ids', $trnsl_up_sells );

	}

	public function sync_cross_sells_products( $product_id, $translated_product_id, $lang ) {

		$original_cross_sells = maybe_unserialize( get_post_meta( $product_id, '_crosssell_ids', true ) );
		$trnsl_cross_sells    = [];
		if ( $original_cross_sells ) {
			foreach ( $original_cross_sells as $original_cross_sell_product ) {
				$trnsl_cross_sells[] = apply_filters( 'translate_object_id', $original_cross_sell_product, get_post_type( $original_cross_sell_product ), false, $lang );
			}
		}
		update_post_meta( $translated_product_id, '_crosssell_ids', $trnsl_cross_sells );

	}

	public function sync_grouped_products( $product_id, $translated_product_id, $lang ) {

		$original_children   = maybe_unserialize( get_post_meta( $product_id, '_children', true ) );
		$translated_children = [];
		if ( $original_children ) {
			foreach ( $original_children as $original_children_product ) {
				$translated_children[] = apply_filters( 'translate_object_id', $original_children_product, get_post_type( $original_children_product ), false, $lang );
			}
		}
		update_post_meta( $translated_product_id, '_children', $translated_children );

	}

	/**
	 * @param WC_Product       $product
	 * @param WC_Product|false $translated_product
	 */
	public function sync_product_stock( $product, $translated_product = false ) {
		$stock = $product->get_stock_quantity();

		if ( ! is_null( $stock ) ) {
			$product_id = $product->get_id();

			remove_action( 'woocommerce_product_set_stock', [ $this, 'sync_product_stock_hook' ], self::PRIORITY_BEFORE_STOCK_EMAIL_TRIGGER );
			remove_action( 'woocommerce_variation_set_stock', [ $this, 'sync_product_stock_hook' ], self::PRIORITY_BEFORE_STOCK_EMAIL_TRIGGER );

			if ( $translated_product ) {
				$this->update_stock_value( $translated_product, $stock );
				$this->woocommerce_wpml->products->update_stock_status( $translated_product->get_id(), $product->get_stock_status() );
			} else {
				$translations = $this->post_translations->get_element_translations( $product_id );
				foreach ( $translations as $translation ) {
					if ( $product_id !== (int)$translation ) {
						$_product = wc_get_product( $translation );
						$this->update_stock_value( $_product, $stock );
						$this->woocommerce_wpml->products->update_stock_status( $translation, $product->get_stock_status() );
					}
				}
			}

			add_action( 'woocommerce_product_set_stock', [ $this, 'sync_product_stock_hook' ], self::PRIORITY_BEFORE_STOCK_EMAIL_TRIGGER );
			add_action( 'woocommerce_variation_set_stock', [ $this, 'sync_product_stock_hook' ], self::PRIORITY_BEFORE_STOCK_EMAIL_TRIGGER );
		}
	}

	/**
	 * @param WC_Product $product
	 * @param int        $stock_quantity
	 */
	private function update_stock_value( $product, $stock_quantity ) {

		$product_id_with_stock = $product->get_stock_managed_by_id();

		/** @var WC_Product_Data_Store_CPT */
		$data_store            = WC_Data_Store::load( 'product' );
		$data_store->update_product_stock( $product_id_with_stock, $stock_quantity, 'set' );

		delete_transient( 'wc_low_stock_count' );
		delete_transient( 'wc_outofstock_count' );
		delete_transient( 'wc_product_children_' . ( $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id() ) );
		wp_cache_delete( 'product-' . $product_id_with_stock, 'products' );
	}

	/**
	 * @param WC_Product $product
	 */
	public function sync_product_stock_hook( $product ) {
		$is_posts_hook_removed                = remove_action(
			'save_post',
			[
				$this->post_translations,
				'save_post_actions',
				100,
			]
		);
		$is_synchronize_products_hook_removed = remove_action( 'save_post', [ $this, 'synchronize_products' ], PHP_INT_MAX );

		$this->sync_product_stock( $product );

		if ( $is_posts_hook_removed ) {
			add_action( 'save_post', [ $this->post_translations, 'save_post_actions' ], 100, 2 );
		}

		if ( $is_synchronize_products_hook_removed ) {
			add_action( 'save_post', [ $this, 'synchronize_products' ], PHP_INT_MAX, 2 );
		}
	}

	/**
	 * @param int $order_id
	 */
	public function sync_product_total_sales( $order_id ) {

		$order = wc_get_order( $order_id );

		foreach ( $order->get_items() as $item ) {

			if ( $item instanceof WC_Order_Item_Product ) {
				$product_id = $item->get_product_id();
				$qty        = $item->get_quantity();
			} else {
				$product_id = $item['product_id'];
				$qty        = $item['qty'];
			}

			$qty = apply_filters( 'wcml_order_item_quantity', $qty, $order, $item );

			/** @var WC_Product_Data_Store_CPT */
			$data_store   = WC_Data_Store::load( 'product' );
			$translations = $this->post_translations->get_element_translations( $product_id );
			foreach ( $translations as $translation ) {
				if ( $product_id !== (int) $translation ) {
					$data_store->update_product_sales( (int) $translation, absint( $qty ), 'increase' );
				}
			}
		}
	}

	public function sync_stock_status_for_translations( $product_id, $status ) {

		if ( $this->woocommerce_wpml->products->is_original_product( $product_id ) ) {

			$translations = $this->post_translations->get_element_translations( $product_id, false, true );

			foreach ( $translations as $translation ) {
				$this->woocommerce_wpml->products->update_stock_status( $translation, $status );
				$this->wc_taxonomies_recount_after_stock_change( $translation );
			}
		}
	}

	/**
	 * @param int $product_id
	 */
	private function wc_taxonomies_recount_after_stock_change( $product_id ) {

		remove_filter( 'get_term', [ $this->sitepress, 'get_term_adjust_id' ], 1 );

		wp_cache_delete( $product_id, 'product_cat_relationships' );
		wp_cache_delete( $product_id, 'product_tag_relationships' );

		wc_recount_after_stock_change( $product_id );

		add_filter( 'get_term', [ $this->sitepress, 'get_term_adjust_id' ], 1, 1 );

	}

	// sync product parent & post_status
	public function sync_date_and_parent( $original_product_id, $tr_product_id, $lang ) {
		$tr_parent_id = apply_filters( 'translate_object_id', wp_get_post_parent_id( $original_product_id ), 'product', false, $lang );
		$tr_parent_id = is_null( $tr_parent_id ) ? 0 : (int) $tr_parent_id;
		$args         = [];
		if ( wp_get_post_parent_id( $tr_product_id ) !== $tr_parent_id ) {
			$args['post_parent'] = $tr_parent_id;
		}
		// sync product date
		if ( ! empty( $this->woocommerce_wpml->settings['products_sync_date'] ) ) {
			$orig_product      = get_post( $original_product_id );
			$args['post_date'] = $orig_product->post_date;
		}
		if ( ! empty( $args ) ) {
			$this->wpdb->update(
				$this->wpdb->posts,
				$args,
				[ 'id' => $tr_product_id ]
			);
		}
	}

	public function set_schedule_for_translations( $deprecated, $post ) {

		if ( $this->woocommerce_wpml->products->is_original_product( $post->ID ) ) {
			$translations = $this->post_translations->get_element_translations( $post->ID, false, true );
			foreach ( $translations as $translation ) {
				wp_clear_scheduled_hook( 'publish_future_post', [ $translation ] );
				wp_schedule_single_event( strtotime( get_gmt_from_date( $post->post_date ) . ' GMT' ), 'publish_future_post', [ $translation ] );
			}
		}
	}

	public function icl_pro_translation_completed( $tr_product_id ) {
		if ( get_post_type( $tr_product_id ) === 'product' ) {
			$original_product_id = $this->post_translations->get_original_element( $tr_product_id );

			if ( $original_product_id ) {
				$this->sync_product_data( $original_product_id, $tr_product_id, $this->post_translations->get_element_lang_code( $tr_product_id ) );
			}
		}
	}

	public function icl_make_duplicate( $master_post_id, $lang, $postarr, $id ) {
		if ( get_post_type( $master_post_id ) === 'product' ) {

			$master_post_id = $this->woocommerce_wpml->products->get_original_product_id( $master_post_id );

			$this->sync_product_data( $master_post_id, $id, $lang, true );
		}
	}

	public function woocommerce_product_quick_edit_save( $product ) {

		$product_id          = $product->get_id();
		$is_original         = $this->woocommerce_wpml->products->is_original_product( $product_id );
		$original_product_id = $this->woocommerce_wpml->products->get_original_product_id( $product_id );

		$translations = $this->post_translations->get_element_translations( $product_id );

		if ( $translations ) {
			foreach ( $translations as $translation ) {
				if ( $is_original && $product_id !== (int) $translation ) {
					$language_code = $this->post_translations->get_element_lang_code( $translation );
					$this->sync_product_data( $product_id, $translation, $language_code );
				} elseif ( ! $is_original && $original_product_id === (int) $translation ) {
					$language_code = $this->post_translations->get_element_lang_code( $product_id );
					$this->sync_product_data( $translation, $product_id, $language_code );
				}
			}
		}
	}

	/**
	 * @param int $originalProductId
	 * @param int $translationId
	 *
	 * @since 5.4.2 Split from duplicate_product_post_meta to avoid the expensive condition on changed values.
	 */
	public function sync_downloadable_files( $originalProductId, $translationId ) {
		global $iclTranslationManagement;
		$settingFactory       = new WPML_Custom_Field_Setting_Factory( $iclTranslationManagement );
		$postmetaFieldSetting = $settingFactory->post_meta_setting( '_downloadable_files' );
		$postmetaFieldStatus  = $postmetaFieldSetting->status();
		if ( WPML_IGNORE_CUSTOM_FIELD === $postmetaFieldStatus ) {
			return;
		}
		$this->woocommerce_wpml->downloadable->sync_files_to_translations( $originalProductId, $translationId );

		// TODO Maybe this is not relevant here, keeping for legacy from the duplicate_product_post_meta split.
		self::syncDeletedCustomFields( $originalProductId, $translationId );

		// Legacy from the duplicate_product_post_meta split, used by compatibility addons.
		do_action( 'wcml_after_duplicate_product_post_meta', $originalProductId, $translationId, false );
	}

	/**
	 * Duplicate the postmeta of a product into one of its translaitons
	 *
	 * Since WCML 5.4.2, fired only when saving a translation from CTE, which will include the translation $data.
	 * Before that, fired instead of sync_downloadable_files(), keeping $data as optional for backward compatibility.
	 *
	 * @param int         $original_product_id
	 * @param int         $translated_product_id
	 * @param array|false $data
	 */
	public function duplicate_product_post_meta( $original_product_id, $translated_product_id, $data = false ) {
		if ( ! $data ) {
			$this->sync_downloadable_files( $original_product_id, $translated_product_id );
			$wcml_data_store = wcml_product_data_store_cpt();
			$wcml_data_store->update_lookup_table_data( $translated_product_id );
			return;
		}

		$custom_fields = get_post_custom( $original_product_id );
		unset( $custom_fields[ SyncHash::META_KEY ] );
		$currentHash = md5( serialize( $custom_fields ) );

		if ( $this->syncHashManager->isNewGroupValue( $translated_product_id, SyncHash::GROUP_FIELDS, $currentHash ) ) {
			global $iclTranslationManagement;
			$all_meta    = get_post_custom( $original_product_id );
			$post_fields = null;

			$settings_factory = new WPML_Custom_Field_Setting_Factory( $iclTranslationManagement );
			// TODO Why we only take out this meta key and not, say '_product_image_gallery'?
			unset( $custom_fields['_thumbnail_id'] );

			foreach ( $custom_fields as $key => $meta ) {
				$setting = $settings_factory->post_meta_setting( $key );

				if ( WPML_IGNORE_CUSTOM_FIELD === $setting->status() ) {
					continue;
				}

				if ( '_downloadable_files' === $key ) {
					$this->woocommerce_wpml->downloadable->sync_files_to_translations( $original_product_id, $translated_product_id, $data );
				} elseif ( WPML_TRANSLATE_CUSTOM_FIELD === $setting->status() ) {
					$post_fields = $this->sync_custom_field_value( $key, $data, $translated_product_id, $post_fields, $original_product_id );
				}
			}

			self::syncDeletedCustomFields( $original_product_id, $translated_product_id );
			$this->syncHashManager->updateGroupValue( $translated_product_id, SyncHash::GROUP_FIELDS, $currentHash );

			$wcml_data_store = wcml_product_data_store_cpt();
			$wcml_data_store->update_lookup_table_data( $translated_product_id );
		}

		do_action( 'wcml_after_duplicate_product_post_meta', $original_product_id, $translated_product_id, $data );
	}

	public function sync_custom_field_value( $custom_field, $translation_data, $trnsl_product_id, $post_fields, $original_product_id = false, $is_variation = false ) {

		if ( is_null( $post_fields ) ) {
			$post_fields = [];
			if ( isset( $_POST['data'] ) && ! is_array( $_POST['data'] ) ) {
				$job_data = [];
				parse_str( $_POST['data'], $job_data );
				$post_fields = $job_data['fields'];
			}
		}

		$custom_filed_key = $is_variation && $original_product_id ? $custom_field . $original_product_id : $custom_field;

		if ( isset( $translation_data[ md5( $custom_filed_key ) ] ) ) {
			$meta_value = $translation_data[ md5( $custom_filed_key ) ];
			$meta_value = apply_filters( 'wcml_meta_value_before_add', $meta_value, $custom_filed_key );
			update_post_meta( $trnsl_product_id, $custom_field, $meta_value );
			unset( $post_fields[ $custom_filed_key ] );
		} else {
			foreach ( $post_fields as $post_field_key => $post_field ) {

				if ( 1 === preg_match( '/field-' . $custom_field . '-.*?/', $post_field_key ) ) {
					delete_post_meta( $trnsl_product_id, $custom_field );

					$custom_fields = get_post_meta( $original_product_id, $custom_field );
					$single        = count( $custom_fields ) === 1;
					$custom_fields = $single ? $custom_fields[0] : $custom_fields;

					$filtered_custom_fields = array_filter( $custom_fields );
					$custom_fields_values   = array_values( $filtered_custom_fields );
					$custom_fields_keys     = array_keys( $filtered_custom_fields );

					foreach ( $custom_fields_values as $custom_field_index => $custom_field_value ) {
						$custom_fields_values =
							$this->get_translated_custom_field_values(
								$custom_fields_values,
								$translation_data,
								$custom_field,
								$custom_field_value,
								$custom_field_index
							);
					}

					$custom_fields_translated = $custom_fields;

					foreach ( $custom_fields_values as $index => $value ) {
						if ( ! $single ) {
							add_post_meta( $trnsl_product_id, $custom_field, $value, $single );
						} else {
							$custom_fields_translated[ $custom_fields_keys[ $index ] ] = $value;
						}
					}
					if ( $single ) {
						update_post_meta( $trnsl_product_id, $custom_field, $custom_fields_translated );
					}
				} else {
					$meta_value = $translation_data[ md5( $post_field_key ) ];
					$field_key  = explode( ':', $post_field_key );
					if ( $field_key[0] == $custom_filed_key ) {
						if ( 'new' === substr( $field_key[1], 0, 3 ) ) {
							add_post_meta( $trnsl_product_id, $custom_field, $meta_value );
						} else {
							update_meta( $field_key[1], $custom_field, $meta_value );
						}
						unset( $post_fields[ $post_field_key ] );
					}
				}
			}
		}

		return $post_fields;
	}

	public function get_translated_custom_field_values( $custom_fields_values, $translation_data, $custom_field, $custom_field_value, $custom_field_index ) {

		if ( is_scalar( $custom_field_value ) ) {
			$key_index            = $custom_field . '-' . $custom_field_index;
			$cf                   = 'field-' . $key_index;
			$meta_keys            = explode( '-', $custom_field_index );
			$meta_keys            = array_map( [ $this, 'replace_separator' ], $meta_keys );
			$custom_fields_values = $this->insert_under_keys(
				$meta_keys,
				$custom_fields_values,
				$translation_data[ md5( $cf ) ]
			);
		} else {
			foreach ( $custom_field_value as $ind => $value ) {
				$field_index          = $custom_field_index . '-' . str_replace( '-', self::CUSTOM_FIELD_KEY_SEPARATOR, $ind );
				$custom_fields_values = $this->get_translated_custom_field_values( $custom_fields_values, $translation_data, $custom_field, $value, $field_index );
			}
		}

		return $custom_fields_values;

	}

	private function replace_separator( $el ) {
		return str_replace( self::CUSTOM_FIELD_KEY_SEPARATOR, '-', $el );
	}

	/**
	 * Inserts an element into an array, nested by keys.
	 * Input ['a', 'b'] for the keys, an empty array for $array and $x for the value would lead to
	 * [ 'a' => ['b' => $x ] ] being returned.
	 *
	 * @param array $keys indexes ordered from highest to lowest level
	 * @param array $array array into which the value is to be inserted
	 * @param mixed $value to be inserted
	 *
	 * @return array
	 */
	private function insert_under_keys( $keys, $array, $value ) {
		$array[ $keys[0] ] = count( $keys ) === 1
			? $value
			: $this->insert_under_keys(
				array_slice( $keys, 1 ),
				( isset( $array[ $keys[0] ] ) ? $array[ $keys[0] ] : [] ),
				$value
			);

		return $array;
	}

	public function icl_connect_translations_action() {
		if ( isset( $_POST['icl_ajx_action'] ) && 'connect_translations' === $_POST['icl_ajx_action'] ) {
			$new_trid      = $_POST['new_trid'];
			$post_type     = $_POST['post_type'];
			$post_id       = $_POST['post_id'];
			$set_as_source = $_POST['set_as_source'];

			if ( 'product' === $post_type ) {
				remove_action( 'wpml_translation_update', [ $this, 'icl_connect_translations_action' ] );
				$translations = $this->sitepress->get_element_translations( $new_trid, 'post_' . $post_type );

				if ( $translations ) {
					foreach ( $translations as $translation ) {
						if ( $set_as_source && ! $translation->original ) {
							$orig_id  = $post_id;
							$trnsl_id = $translation->element_id;
							$lang     = $translation->language_code;
							break;
						} elseif ( ! $set_as_source && $translation->original ) {
							$orig_id  = $translation->element_id;
							$trnsl_id = $post_id;
							$lang     = $this->sitepress->get_current_language();
							break;
						}
					}

					if ( isset( $orig_id, $trnsl_id, $lang ) ) {
						$this->sync_product_data( $orig_id, $trnsl_id, $lang );
						$this->sitepress->copy_custom_fields( $orig_id, $trnsl_id );
						$this->woocommerce_wpml->translation_editor->create_product_translation_package( $orig_id, $new_trid, $lang, ICL_TM_COMPLETE );
					}
				}

				add_action( 'wpml_translation_update', [ $this, 'icl_connect_translations_action' ] );
			}
		}
	}

	/**
	 * @deprecated 5.4.2 Use the \WCML\Utilities\SyncHash utility instead.
	 */
	public function check_if_product_fields_sync_needed( $original_id, $trnsl_post_id, $fields_group ) {

		$cache_group         = 'is_product_fields_sync_needed';
		$cache_key           = $trnsl_post_id . $fields_group;
		$temp_is_sync_needed = wp_cache_get( $cache_key, $cache_group );

		if ( false !== $temp_is_sync_needed ) {
			return (bool) $temp_is_sync_needed;
		}

		$is_sync_needed = true;
		$hash           = '';

		switch ( $fields_group ) {
			case 'postmeta_fields':
				$custom_fields = get_post_custom( $original_id );
				unset( $custom_fields[ SyncHash::META_KEY ] );
				$hash = md5( serialize( $custom_fields ) );
				break;
			case 'taxonomies':
				$termIds   = [];
				$found     = false;
				$all_terms = WPML_Non_Persistent_Cache::get( $original_id, __CLASS__, $found );
				if ( ! $found ) {
					$taxonomies = get_object_taxonomies( get_post_type( $original_id ) );
					$all_terms  = wp_get_object_terms( $original_id, $taxonomies );
					WPML_Non_Persistent_Cache::set( $original_id, $all_terms, __CLASS__ );
				}
				if ( is_wp_error( $all_terms ) ) {
					$all_terms = [];
				}
				foreach ( $all_terms as $term ) {
					$termIds[] = $term->term_id;
				}
				$hash = md5( join( ',', $termIds ) );
				break;
			case 'default_attributes':
				$hash = md5( get_post_meta( $original_id, '_default_attributes', true ) );
				break;
		}

		$wcml_sync_hash = get_post_meta( $trnsl_post_id, SyncHash::META_KEY, true );
		$post_md5       = '' === $wcml_sync_hash ? [] : maybe_unserialize( $wcml_sync_hash );

		if ( isset( $post_md5[ $fields_group ] ) && $post_md5[ $fields_group ] === $hash ) {
			$is_sync_needed = false;
		} else {
			$post_md5[ $fields_group ] = $hash;
			update_post_meta( $trnsl_post_id, SyncHash::META_KEY, $post_md5 );
		}

		wp_cache_set( $cache_key, intval( $is_sync_needed ), $cache_group );

		return $is_sync_needed;
	}

	public function sync_product_translations_visibility( $product_id ) {
		$translations = $this->post_translations->get_element_translations( $product_id, false, true );
		if ( $translations ) {

			$product = wc_get_product( $product_id );
			$terms   = [];

			if ( $this->woocommerce_wpml->products->is_original_product( $product_id ) ) {
				if ( $product->is_featured() ) {
					$terms[] = 'featured';
				}
			}

			if ( 'outofstock' === $product->get_stock_status() ) {
				$terms[] = 'outofstock';
			}

			$rating = min( 5, round( $product->get_average_rating(), 0 ) );

			if ( $rating > 0 ) {
				$terms[] = 'rated-' . $rating;
			}

			foreach ( $translations as $translation ) {
				if ( $product_id !== (int) $translation ) {
					wp_set_post_terms( $translation, $terms, 'product_visibility', false );
				}
			}
		}
	}

	/**
	 * @param int $originalId
	 * @param int $translationId
	 */
	public static function syncDeletedCustomFields( $originalId, $translationId ) {
		$settingsFactory = wpml_load_core_tm()->settings_factory();

		// $isCopiedField :: string -> bool
		$isCopiedField = function( $field ) use ( $settingsFactory ) {
			return WPML_COPY_CUSTOM_FIELD === $settingsFactory->post_meta_setting( $field )->status();
		};

		// $deleteFieldInTranslation :: string -> void
		$deleteFieldInTranslation = function( $field ) use ( $translationId ) {
			delete_post_meta( $translationId, $field );
		};

		$deletedInOriginal = wpml_collect( array_diff(
			array_keys( get_post_custom( $translationId ) ),
			array_keys( get_post_custom( $originalId ) )
		) );

		$deletedInOriginal
			->filter( $isCopiedField )
			->map( $deleteFieldInTranslation );
	}
}
