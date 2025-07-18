<?php

use WCML\COT\Helper as COTHelper;
use function WCML\functions\isStandAlone;

class WCML_Install {

	const CHUNK_SIZE = 1000;

	/**
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param SitePress        $sitepress
	 */
	public static function initialize( $woocommerce_wpml, $sitepress ) {
		if ( is_admin() ) {

			if ( isStandAlone() ) {
				self::initialize_standalone( $woocommerce_wpml );
			} else {
				self::initialize_full( $woocommerce_wpml, $sitepress );
			}
		}
	}

	/**
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param SitePress        $sitepress
	 */
	private static function initialize_full( $woocommerce_wpml, $sitepress ) {
		// Install routine.
		if ( empty( $woocommerce_wpml->settings['set_up'] ) ) { // from 3.2.

			if ( $woocommerce_wpml->settings['is_term_order_synced'] !== 'yes' ) {
				// global term ordering resync when moving to >= 3.3.x.
				add_action( 'init', [ $woocommerce_wpml->terms, 'sync_term_order_globally' ], 20 );
			}

			if ( ! isset( $woocommerce_wpml->settings['wc_admin_options_saved'] ) ) {
				self::handle_admin_texts();
				$woocommerce_wpml->settings['wc_admin_options_saved'] = 1;
			}

			if ( $woocommerce_wpml->is_wpml_prior_4_2() ) {
				if ( ! isset( $woocommerce_wpml->settings['trnsl_interface'] ) ) {
					$woocommerce_wpml->settings['trnsl_interface'] = 1;
				}
			} else {
				global $iclTranslationManagement;
				$iclTranslationManagement->settings[ WPML_TM_Post_Edit_TM_Editor_Mode::TM_KEY_FOR_POST_TYPE_USE_NATIVE ]['product'] = false;
				$iclTranslationManagement->save_settings();
			}

			if ( ! isset( $woocommerce_wpml->settings['products_sync_date'] ) ) {
				$woocommerce_wpml->settings['products_sync_date'] = 1;
			}

			if ( ! isset( $woocommerce_wpml->settings['products_sync_order'] ) ) {
				$woocommerce_wpml->settings['products_sync_order'] = 1;
			}

			if ( ! isset( $woocommerce_wpml->settings['display_custom_prices'] ) ) {
				$woocommerce_wpml->settings['display_custom_prices'] = 0;
			}

			if ( ! isset( $woocommerce_wpml->settings['sync_taxonomies_checked'] ) ) {
				$woocommerce_wpml->terms->check_if_sync_terms_needed();
				$woocommerce_wpml->settings['sync_taxonomies_checked'] = 1;
			}

			WCML_Capabilities::set_up_capabilities();

			self::set_language_information( $sitepress );
			self::check_product_type_terms();

			set_transient( '_wcml_activation_redirect', 1, 30 );

			// Before the setup wizard redirects from plugins.php, allow WPML to scan the wpml-config.xml file.
			WPML_Config::load_config_run();

			add_action( 'init', [ __CLASS__, 'insert_default_categories' ] );

			self::set_language_to_existing_orders( $sitepress->get_default_language() );

			wp_schedule_single_event( time() + 10, 'generate_category_lookup_table' );

			$woocommerce_wpml->settings['set_up'] = 1;
			$woocommerce_wpml->update_settings();
		}

		if ( empty( $woocommerce_wpml->settings['downloaded_translations_for_wc'] ) ) { // from 3.3.3.
			$woocommerce_wpml->languages_upgrader->download_woocommerce_translations_for_active_languages();
			$woocommerce_wpml->settings['downloaded_translations_for_wc'] = 1;
			$woocommerce_wpml->update_settings();
		}

		if ( empty( $woocommerce_wpml->settings['rewrite_rules_flashed'] ) ) {
			flush_rewrite_rules();
			$woocommerce_wpml->settings['rewrite_rules_flashed'] = 1;
		}

		add_filter(
			'wpml_tm_dashboard_translatable_types',
			[
				__CLASS__,
				'hide_variation_type_on_tm_dashboard',
			]
		);

		$WCML_Setup_UI = new WCML_Setup_UI( $woocommerce_wpml );
		$WCML_Setup_UI->add_hooks();
		$WCML_Setup = new WCML_Setup( $WCML_Setup_UI, new WCML_Setup_Handlers( $woocommerce_wpml ), $woocommerce_wpml, $sitepress );
		$WCML_Setup->setup_redirect();
		$WCML_Setup->add_hooks();

		$translated_product_type_terms = self::translated_product_type_terms();
		if ( ! empty( $translated_product_type_terms ) ) {
			add_action( 'admin_notices', [ __CLASS__, 'admin_translated_product_type_terms_notice' ] );
		} elseif ( $sitepress->is_translated_taxonomy( 'product_type' ) ) {
			add_action( 'admin_notices', [ __CLASS__, 'admin_translated_product_type_notice' ] );
		}
	}

	/**
	 * This is minimal version of full initialization.
	 * It has a different flag, so the full initialization
	 * might run later.
	 *
	 * @param woocommerce_wpml $woocommerce_wpml
	 */
	private static function initialize_standalone( $woocommerce_wpml ) {
		if ( empty( $woocommerce_wpml->settings['set_up_standalone'] ) ) {
			if ( ! isset( $woocommerce_wpml->settings['display_custom_prices'] ) ) {
				$woocommerce_wpml->settings['display_custom_prices'] = 0;
			}

			WCML_Capabilities::set_up_capabilities();

			set_transient( '_wcml_activation_redirect', 1, 30 );

			$woocommerce_wpml->settings['set_up_standalone'] = 1;
			$woocommerce_wpml->update_settings();
		}
	}

	/**
	 * @param SitePress $sitepress
	 */
	private static function set_language_information( $sitepress ) {
		global $wpdb;

		$def_lang = $sitepress->get_default_language();
		// set language info for products.
		$products = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_type = 'product' AND post_status <> 'auto-draft'" );
		foreach ( $products as $product ) {
			$exist = $sitepress->get_language_for_element( $product->ID, 'post_product' );
			if ( ! $exist ) {
				$sitepress->set_element_language_details( $product->ID, 'post_product', false, $def_lang );
			}
		}

		// set language info for taxonomies.
		$terms = $wpdb->get_results( "SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'product_cat'" );
		foreach ( $terms as $term ) {
			$exist = $sitepress->get_language_for_element( $term->term_taxonomy_id, 'tax_product_cat' );
			if ( ! $exist ) {
				$sitepress->set_element_language_details( $term->term_taxonomy_id, 'tax_product_cat', false, $def_lang );
			}
		}
		$terms = $wpdb->get_results( "SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'product_tag'" );
		foreach ( $terms as $term ) {
			$exist = $sitepress->get_language_for_element( $term->term_taxonomy_id, 'tax_product_tag' );
			if ( ! $exist ) {
				$sitepress->set_element_language_details( $term->term_taxonomy_id, 'tax_product_tag', false, $def_lang );
			}
		}

		$terms = $wpdb->get_results( "SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'product_shipping_class'" );
		foreach ( $terms as $term ) {
			$exist = $sitepress->get_language_for_element( $term->term_taxonomy_id, 'tax_product_shipping_class' );
			if ( ! $exist ) {
				$sitepress->set_element_language_details( $term->term_taxonomy_id, 'tax_product_shipping_class', false, $def_lang );
			}
		}
	}

	/**
	 * Handle situation when product_type terms translated before activating WCML.
	 */
	public static function check_product_type_terms() {
		global $wpdb;
		// check if terms were translated.
		$translations = self::translated_product_type_terms();

		if ( $translations ) {
			foreach ( $translations as $translation ) {
				if ( ! is_null( $translation->source_language_code ) ) {
					// check relationships.
					$term_relationships = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->term_relationships} WHERE term_taxonomy_id = %d", $translation->element_id ) );
					if ( $term_relationships ) {
						$orig_term = $wpdb->get_var( $wpdb->prepare( "SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE element_type = 'tax_product_type' AND trid = %d AND source_language_code IS NULL", $translation->trid ) );
						if ( $orig_term ) {
							foreach ( $term_relationships as $term_relationship ) {
								$wpdb->update(
									$wpdb->term_relationships,
									[
										'term_taxonomy_id' => $orig_term,
									],
									[
										'object_id'        => $term_relationship->object_id,
										'term_taxonomy_id' => $translation->element_id,
									]
								);
							}
						}
					}
					$term_id = $wpdb->get_var( $wpdb->prepare( "SELECT term_id FROM {$wpdb->term_taxonomy} WHERE term_taxonomy_id = %d", $translation->element_id ) );

					if ( $term_id ) {
						$wpdb->delete(
							$wpdb->terms,
							[
								'term_id' => $term_id,
							]
						);

						$wpdb->delete(
							$wpdb->term_taxonomy,
							[
								'term_taxonomy_id' => $translation->element_id,
							]
						);
					}
				}
			}

			foreach ( $translations as $translation ) {
				$wpdb->delete(
					$wpdb->prefix . 'icl_translations',
					[
						'translation_id' => $translation->translation_id,
					]
				);
			}
		}
	}

	public static function translated_product_type_terms() {
		global $wpdb;
		// check if terms were translated.
		$translations = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}icl_translations WHERE element_type = 'tax_product_type'" );

		return $translations;
	}

	private static function handle_admin_texts() {
		if ( class_exists( 'WooCommerce' ) ) {
			// emails texts.
			$emails = new WC_Emails();
			foreach ( $emails->emails as $email ) {
				$option_name = $email->plugin_id . $email->id . '_settings';
				if ( ! get_option( $option_name ) ) {
					add_option( $option_name, $email->settings );
				}
			}
		}
	}

	public static function admin_translated_product_type_notice() {
		?>

		<div id="message" class="updated error">
			<p>
				<?php
				/* translators: %1$s and %2$s are opening and closing HTML italic tags and %3$s and %4$s are opening and closing HTML link tags */
				printf( esc_html__( 'We detected a problem in your WPML configuration: the %1$sproduct_type%2$s taxonomy is set as translatable and this would cause problems with translated products. You can fix this in the %3$sMultilingual Content Setup page%4$s.', 'woocommerce-multilingual' ), '<i>', '</i>', '<a href="' . esc_url( admin_url( 'admin.php?page=' . WPML_TM_FOLDER . '/menu/main.php&sm=mcsetup#ml-content-setup-sec-8' ) ) . '">', '</a>' );
				?>
			</p>
		</div>

		<?php
	}

	public static function admin_translated_product_type_terms_notice() {
		?>

		<div id="message" class="updated error">
			<p>
				<?php
				printf(
					/* translators: %1$s and %2$s are opening and closing HTML italic tags and %3$s and %4$s are opening and closing HTML link tags */
					esc_html__( 'We detected that the %1$sproduct_type%2$s field was set incorrectly for some product translations. This happened because the product_type taxonomy was translated. You can fix this in the WooCommerce Multilingual & Multicurrency %3$stroubleshooting page%4$s.', 'woocommerce-multilingual' ),
					'<i>',
					'</i>',
					'<a href="' . esc_url( \WCML\Utilities\AdminUrl::getTroubleshootingTab() ) . '">',
					'</a>'
				);
				?>
			</p>
		</div>

		<?php
	}

	public static function hide_variation_type_on_tm_dashboard( $types ) {
		unset( $types['product_variation'] );
		return $types;
	}

	public static function insert_default_categories() {
		global $sitepress, $woocommerce_wpml;

		$settings = $woocommerce_wpml->get_settings();

		$default_language   = $sitepress->get_default_language();
		$default_categories = isset( $settings['default_categories'] ) ? $settings['default_categories'] : [];

		foreach ( $sitepress->get_active_languages() as $language ) {
			if ( isset( $default_categories[ $language['code'] ] ) ) {
				continue;
			}

			$sitepress->switch_locale( $language['code'] );
			$translated_cat_name = __( 'Uncategorized', 'sitepress' );
			$translated_cat_name = 'Uncategorized' === $translated_cat_name && 'en' !== $language['code'] ? 'Uncategorized @' . $language['code'] : $translated_cat_name;
			$translated_term     = get_term_by( 'name', $translated_cat_name, 'product_cat', ARRAY_A );
			$sitepress->switch_locale();

			// check if the term already exists.
			if ( ! $translated_term ) {
				$translated_term = wp_insert_term( $translated_cat_name, 'product_cat' );
			}

			if ( $translated_term && ! is_wp_error( $translated_term ) ) {
				// add it to settings.
				$settings['default_categories'][ $language['code'] ] = $translated_term['term_taxonomy_id'];

				// update translations table.
				$default_category_trid = $sitepress->get_element_trid(
					get_option( 'default_product_cat' ),
					'tax_product_cat'
				);
				$sitepress->set_element_language_details(
					$translated_term['term_taxonomy_id'],
					'tax_product_cat',
					$default_category_trid,
					$language['code'],
					$default_language
				);
			}
		}

		$woocommerce_wpml->update_settings( $settings );
	}

	/**
	 * @param string $default_language
	 */
	public static function set_language_to_existing_orders( $default_language ) {
		/** @var \wpdb $wpdb */
		global $wpdb;

		// Set default language for old orders before WCML was installed.
		$orders_needs_set_language = $wpdb->get_col(
			"SELECT DISTINCT( pm.post_id ) FROM {$wpdb->postmeta} AS pm 
					INNER JOIN {$wpdb->posts} AS p ON pm.post_id = p.ID 
					WHERE p.post_type = 'shop_order' AND pm.post_id NOT IN 
					( SELECT DISTINCT( post_id ) FROM {$wpdb->postmeta} WHERE meta_key = '" . WCML_Orders::KEY_LANGUAGE . "' )"
		);

		$values_query = function ( $order_id ) use ( $wpdb, $default_language ) {
			return $wpdb->prepare(
				'(%d, %s, %s)',
				$order_id,
				WCML_Orders::KEY_LANGUAGE,
				$default_language
			);
		};

		wpml_collect( array_chunk( $orders_needs_set_language, self::CHUNK_SIZE ) )->each( function ( $chunk ) use ( $values_query, $wpdb ) {

			$query  = "INSERT IGNORE INTO {$wpdb->postmeta} (`post_id`, `meta_key`, `meta_value`) VALUES ";
			$query .= implode( ',', array_map( $values_query, $chunk ) );

			$wpdb->query( $query );
		} );

		if ( COTHelper::getTableExists() ) {
			$orderTable     = COTHelper::getTableName();
			$orderMetaTable = COTHelper::getMetaTableName();

			// phpcs:disable WordPress.WP.PreparedSQL.NotPrepared
			// phpcs:disable WordPress.VIP.DirectDatabaseQuery.NoCaching
			$wpdb->query(
				$wpdb->prepare(
					"
						INSERT IGNORE INTO {$orderMetaTable} (order_id, meta_key, meta_value)
						SELECT o.id, '" . WCML_Orders::KEY_LANGUAGE . "' AS meta_key, %s AS meta_value
						FROM {$orderTable} o
						LEFT JOIN {$orderMetaTable} om ON o.id = om.order_id AND om.meta_key = '" . WCML_Orders::KEY_LANGUAGE . "'
						WHERE om.id IS NULL
					",
					$default_language
				)
			);
			// phpcs::enable.
		}
	}

}
