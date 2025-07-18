<?php

use WPML\FP\Fns;
use WPML\FP\Lst;
use WPML\FP\Str;

class WCML_WC_Strings {

	/** @see \WPML_ST_Taxonomy_Strings::LEGACY_STRING_DOMAIN */
	const DOMAIN_WORDPRESS              = 'WordPress';
	/** @see \WPML_ST_Taxonomy_Strings::LEGACY_NAME_PREFIX_SINGULAR */
	const TAXONOMY_SINGULAR_NAME_PREFIX = 'taxonomy singular name: ';

	private $translations_from_mo_file = [];
	private $mo_files                  = [];
	private $current_language;

	/** @var woocommerce_wpml */
	private $woocommerce_wpml;
	/** @var SitePress */
	private $sitepress;
	/** @var wpdb */
	private $wpdb;

	public $settings = [];

	/**
	 * WCML_WC_Strings constructor.
	 *
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param SitePress        $sitepress
	 * @param wpdb             $wpdb
	 */
	public function __construct( woocommerce_wpml $woocommerce_wpml, \WPML\Core\ISitePress $sitepress, wpdb $wpdb ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress        = $sitepress;
		$this->wpdb             = $wpdb;
	}

	public function add_hooks() {

		add_action( 'init', [ $this, 'add_on_init_hooks' ] );

		// Needs to run before WC registers taxonomies on init priority 5.
		foreach ( wc_get_attribute_taxonomies() as $tax ) {
			add_filter(
				'woocommerce_taxonomy_args_' . wc_attribute_taxonomy_name( $tax->attribute_name ),
				function ( $args ) use ( $tax ) {
					return $this->translate_attribute_labels( $args, $tax->attribute_label );
				}
			);
		}
	}

	public function add_on_init_hooks() {
		global $pagenow;

		$this->current_language = $this->sitepress->get_current_language();
		if ( 'all' === $this->current_language ) {
			$this->current_language = $this->sitepress->get_default_language();
		}

		// translate attribute label.
		add_filter( 'woocommerce_attribute_label', Fns::withoutRecursion( Fns::identity(), [ $this, 'translated_attribute_label' ] ), 10, 3 );
		add_filter( 'woocommerce_checkout_product_title', [ $this, 'translated_checkout_product_title' ], 10, 2 );
		add_filter( 'woocommerce_cart_item_name', [ $this, 'translated_cart_item_name' ], -1, 2 );

		if ( is_admin() ) {

			if ( 'edit.php' !== $pagenow && ! wpml_is_ajax() ) {
				add_filter(
					'woocommerce_attribute_taxonomies',
					[
						$this,
						'translate_attribute_taxonomies_labels',
					]
				);
			}
			if ( 'options-permalink.php' === $pagenow ) {
				add_filter( 'gettext_with_context', [ $this, 'category_base_in_strings_language' ], 99, 3 );
				add_action( 'admin_footer', [ $this, 'show_custom_url_base_language_requirement' ] );
			}
		}

		add_action(
			'woocommerce_product_options_attributes',
			[
				$this,
				'notice_after_woocommerce_product_options_attributes',
			]
		);

		add_filter( 'woocommerce_get_breadcrumb', [ $this, 'filter_woocommerce_breadcrumbs' ] );
	}

	public function translated_attribute_label( $label, $name, $product_obj = false ) {
		global $product;

		$product_id = false;
		$lang       = $this->sitepress->get_current_language();

		if ( \WCML\Orders\Helper::isOrderEditAdminScreen() ) {
			$lang = $this->sitepress->get_user_admin_language( get_current_user_id(), true );
		}

		if ( is_numeric( $product_obj ) ) {
			$product_id = $product_obj;
		} elseif ( $product_obj && is_object( $product_obj ) ) {
			$product_id = $product_obj->get_id();
		} elseif ( $product && is_object( $product ) ) {
			$product_id = $product->get_id();
		}

		/**
		 * Adjusts the product being considered when translating variable product attribute labels.
		 *
		 * Sometimes, WooCommerce or its addons prints an attribute label without referencing the relevant product;
		 * in those cases, WCML might fail to translate local attribute labels if the current product is not the relevant one
		 * (for example, on bundled or composited products containing such variable products).
		 * This filter helps setting the right product to translate local attribute labels.
		 *
		 * @param int|false $product_id
		 * @param string    $label
		 * @param string    $name
		 * @param mixed     $product_obj
		 *
		 * @return int|false
		 */
		$product_id = apply_filters( 'wcml_translated_attribute_label_product_id', $product_id, $label, $name, $product_obj );

		$name = $this->woocommerce_wpml->attributes->filter_attribute_name(
			$name,
			$product_id,
			/**
			 * This filter allows to override the attribute name sanitization, used only for legacy and specific purposes.
			 *
			 *
			 * @param  bool   $state Whether we should run sanitization.
			 * @param  string $name  Attribute name
			 * @param  string $label Attribute label
			 *
			 * @return string
			 */
			apply_filters( 'wcml_sanitize_name_for_translated_attribute_label', true, $name, $label )
		);

		if ( $product_id ) {

			$custom_attr_translation = $this->woocommerce_wpml->attributes->get_attr_label_translations( $product_id, $lang );

			if ( $custom_attr_translation ) {
				if ( isset( $custom_attr_translation[ $name ] ) ) {
					return $custom_attr_translation[ $name ];
				}
			}
		}

		$trnsl_label = apply_filters( 'wpml_translate_single_string', $label, self::DOMAIN_WORDPRESS, self::TAXONOMY_SINGULAR_NAME_PREFIX . $label, $lang );

		if ( $label != $trnsl_label ) {
			return $trnsl_label;
		}

		if ( is_admin() && ! wpml_is_ajax() ) {

			$string_language = $this->get_string_language( self::TAXONOMY_SINGULAR_NAME_PREFIX . $label, self::DOMAIN_WORDPRESS );

			if ( $this->sitepress->get_user_admin_language( get_current_user_id(), true ) != $string_language ) {
				$string_id = icl_get_string_id( self::TAXONOMY_SINGULAR_NAME_PREFIX . $label, self::DOMAIN_WORDPRESS );
				$strings   = icl_get_string_translations_by_id( $string_id );
				if ( $strings ) {
					return $strings[ $this->sitepress->get_user_admin_language( get_current_user_id(), true ) ]['value'];
				}
			} else {
				return $label;
			}
		}

		// backward compatibility for WCML < 3.6.1.
		$trnsl_labels = get_option( 'wcml_custom_attr_translations' );

		if ( isset( $trnsl_labels[ $lang ][ $name ] ) && ! empty( $trnsl_labels[ $lang ][ $name ] ) ) {
			return $trnsl_labels[ $lang ][ $name ];
		}

		return $label;
	}

	/**
	 * @param string $title
	 * @param array  $values
	 *
	 * @return string
	 */
	public function translated_cart_item_name( $title, array $values ) {

		if ( $values ) {

			$product_id = $values['variation_id'] ? $values['variation_id'] : $values['product_id'];

			$translated_product_id = apply_filters( 'wpml_object_id', $product_id, 'product', true );
			$translated_product = wc_get_product( $translated_product_id );
			$translated_title   = $translated_product ? $translated_product->get_name() : '';

			if ( strstr( $title, '</a>' ) ) {
				$title = sprintf( '<a href="%s">%s</a>', $values['data']->get_permalink(), $translated_title );
			} else {
				$title = $translated_title . '&nbsp;';
			}
		}

		return $title;
	}

	public function translated_checkout_product_title( $title, $product ) {

		if ( $product ) {
			$tr_product_id = apply_filters( 'wpml_object_id', $product->get_id(), 'product', true, $this->current_language );
			$title         = get_the_title( $tr_product_id );
		}

		return $title;
	}

	// Catch the default slugs for translation.
	public function translate_default_slug( $translation, $text, $context, $domain ) {

		if ( 'slug' === $context || 'default-slug' === $context ) {
			$wc_slug          = $this->woocommerce_wpml->url_translation->get_woocommerce_product_base();
			$admin_language   = is_admin() ? $this->sitepress->get_admin_language() : null;
			$current_language = $this->sitepress->get_current_language();

			$strings_language = $this->get_domain_language( 'woocommerce' );

			if ( $text == $wc_slug && 'woocommerce' === $domain && $strings_language ) {
				$this->sitepress->switch_lang( $strings_language );
				$translation = _x( $text, 'URL slug', $domain );
				$this->sitepress->switch_lang( $current_language );
				if ( $admin_language ) {
					$this->sitepress->set_admin_language( $admin_language );
				}
			} else {
				$translation = $text;
			}

			if ( ! is_admin() ) {
				$this->sitepress->switch_lang( $current_language );
			}
		}

		return $translation;

	}


	public function show_custom_url_base_language_requirement() {
		$category_base   = ( $c = get_option( 'category_base' ) ) ? $c : 'category';
		$category_notice = __( 'You are using the same value as for the regular category base. This is known to create conflicts resulting in urls not working properly.', 'woocommerce-multilingual' );
		?>
		<script>
			if (jQuery('input[name="woocommerce_product_category_slug"]').length && jQuery('input[name="woocommerce_product_category_slug"]').val() == '<?php echo esc_js( $category_base ); ?>') {
				jQuery('input[name="woocommerce_product_category_slug"]').parent().append('<br><i class="icon-warning-sign"><?php echo esc_js( $category_notice ); ?></i>');
			}
		</script>
		<?php

	}

	/**
	 * @return \WCML_Url_Translation
	 */
	public function getUrlTranslation() {
		return $this->woocommerce_wpml->url_translation;
	}

	public function category_base_in_strings_language( $text, $original_value, $context ) {
		if ( $context === 'slug' && ( $original_value === 'product-category' || $original_value === 'product-tag' ) ) {
			$text = $original_value;
		}

		return $text;
	}

	public function product_permalink_slug() {
		$permalinks = get_option( 'woocommerce_permalinks' );
		$slug       = empty( $permalinks['product_base'] ) ? 'product' : trim( $permalinks['product_base'], '/' );

		return $slug;
	}

	public function get_domain_language( $domain ) {

		$lang_of_domain = new WPML_Language_Of_Domain( $this->sitepress );
		$domain_lang    = $lang_of_domain->get_language( $domain );

		if ( $domain_lang ) {
			$source_lang = $domain_lang;
		} else {
			$source_lang = 'en';
		}

		return $source_lang;
	}

	public function get_string_language( $value, $context, $name = false ) {

		if ( $name !== false ) {

			$string_language = apply_filters( 'wpml_get_string_language', null, $context, $name );

		} else {

			$string_id = icl_get_string_id( $value, $context, $name );

			if ( ! $string_id ) {
				return 'en';
			}

			$string_object   = new WPML_ST_String( $string_id, $this->wpdb );
			$string_language = $string_object->get_language();

		}

		if ( ! $string_language ) {
			return 'en';
		}

		return $string_language;

	}

	public function set_string_language( $value, $context, $name, $language ) {

		$string_id = icl_get_string_id( $value, $context, $name );

		$string_object   = new WPML_ST_String( $string_id, $this->wpdb );
		$string_language = $string_object->set_language( $language );

		return $string_language;
	}


	/*
	 * Filter breadcrumbs
	 *
	 */
	public function filter_woocommerce_breadcrumbs( $breadcrumbs ) {

		$current_language = $this->sitepress->get_current_language();
		$default_language = $this->sitepress->get_default_language();

		$woocommerce_shop_page = wc_get_page_id( 'shop' );

		if ( $woocommerce_shop_page ) {

			$is_shop_page_active = get_post_status( $woocommerce_shop_page );

			if ( ( $current_language != $default_language || $default_language != 'en' ) && $is_shop_page_active === 'publish' ) {

				$shop_page = get_post( $woocommerce_shop_page );

				// If permalinks contain the shop page in the URI prepend the breadcrumb with shop.
				// Similar to WC_Breadcrumb::prepend_shop_page
				$trnsl_base = $this->woocommerce_wpml->url_translation->get_base_translation( 'product', $current_language );
				if ( $trnsl_base['translated_base'] === '' ) {
					$trnsl_base['translated_base'] = $trnsl_base['original_value'];
				}

				if ( is_woocommerce() && $shop_page->ID && strstr( $trnsl_base['translated_base'], urldecode( $shop_page->post_name ) ) && get_option( 'page_on_front' ) != $shop_page->ID ) {
					$breadcrumbs_buff = [];
					$i                = 0;

					foreach ( $breadcrumbs as $key => $breadcrumb ) {

						// Prepend the shop page to shop breadcrumbs
						if ( $key === 0 ) {

							if ( isset( $breadcrumbs[1][1] ) && $breadcrumbs[1][1] != get_post_type_archive_link( 'product' ) ) {

								if ( get_home_url() === $breadcrumbs[0][1] ) {
									$breadcrumbs_buff[ $i ] = $breadcrumb;
									$i ++;
								}

								$breadcrumbs_buff[ $i ] = [
									$shop_page->post_title,
									get_post_type_archive_link( 'product' ),
								];
								$i ++;
							}
						}

						if ( ! in_array( $breadcrumb, $breadcrumbs_buff ) ) {
							$breadcrumbs_buff[ $i ] = $breadcrumb;
						}
						$i ++;
					}

					$breadcrumbs = $breadcrumbs_buff;

					$breadcrumbs = array_values( $breadcrumbs );
				}
			}
		}

		return $breadcrumbs;
	}

	/*
	 * Add notice message to users
	 */
	public function notice_after_woocommerce_product_options_attributes() {

		if ( isset( $_GET['post'] ) && $this->sitepress->get_default_language() != $this->sitepress->get_current_language() ) {
			$original_product_id = apply_filters( 'wpml_object_id', $_GET['post'], 'product', true, $this->sitepress->get_default_language() );

			//The message used to include a link to translate THIS product, not sure if this will be doable when linking to the TM dashboard
			$pointerFactory = new WCML\PointerUi\Factory();
			$pointerFactory
				->create( [
					'content'    => sprintf(
						/* translators: %1$s and %2$s are opening and closing HTML link tags */
						esc_html__( 'To translate attributes, go to the %1$sTranslation Dashboard%2$s and send the associated product for translation.', 'woocommerce-multilingual' ),
						'<a href="' . esc_url( \WCML\Utilities\AdminUrl::getWPMLTMDashboardProducts() ) . '">',
						'</a>'
					),
					'selectorId' => 'product_attributes',
					'method'     => 'append',
				] )
				->show();
		}
	}

	public function translate_attribute_taxonomies_labels( $attribute_taxonomies ) {

		foreach ( $attribute_taxonomies as $key => $attribute_taxonomy ) {
			$string_language = $this->get_string_language( $attribute_taxonomy->attribute_label, self::DOMAIN_WORDPRESS, self::TAXONOMY_SINGULAR_NAME_PREFIX . $attribute_taxonomy->attribute_label );

			if ( $this->sitepress->get_current_language() == $string_language ) {
				continue;
			}

			$string_id = icl_get_string_id( $attribute_taxonomy->attribute_label, self::DOMAIN_WORDPRESS, self::TAXONOMY_SINGULAR_NAME_PREFIX . $attribute_taxonomy->attribute_label );
			$strings   = icl_get_string_translations_by_id( $string_id );

			if ( $strings && isset( $strings[ $this->sitepress->get_current_language() ] ) ) {
				$attribute_taxonomies[ $key ]->attribute_label = $strings[ $this->sitepress->get_current_language() ]['value'];
			}
		}

		return $attribute_taxonomies;
	}

	public function get_translation_from_woocommerce_mo_file( $string, $language, $return_original = true ) {

		$original_string = $string;

		if ( ! isset( $this->translations_from_mo_file[ $original_string ][ $language ] ) ) {

			if ( ! isset( $this->translations_from_mo_file[ $original_string ] ) ) {
				$this->translations_from_mo_file[ $original_string ] = [];
			}

			if ( ! isset( $this->mo_files[ $language ] ) ) {
				$mo      = new MO();
				$mo_file = WP_LANG_DIR . '/plugins/woocommerce-' . $this->sitepress->get_locale( $language ) . '.mo';
				if ( ! file_exists( $mo_file ) ) {
					return $return_original ? $this->get_original_string( $original_string ) : null;
				}

				$mo->import_from_file( $mo_file );
				$this->mo_files[ $language ] = $mo->entries;
			}

			if ( in_array( $string, [ 'product', 'product-category', 'product-tag' ] ) ) {
				$string = $this->get_msgid_for_mo( $string, 'slug' );
			}

			if ( isset( $this->mo_files[ $language ][ $string ] ) ) {
				$this->translations_from_mo_file[ $original_string ][ $language ] = $this->mo_files[ $language ][ $string ]->translations[0];
			} else {
				$this->translations_from_mo_file[ $original_string ][ $language ] = $return_original ? $this->get_original_string( $original_string ) : null;
			}
		}

		return $this->translations_from_mo_file[ $original_string ][ $language ];

	}

	/**
	 * @param array  $args
	 * @param string $attribute_label
	 *
	 * @return array
	 */
	public function translate_attribute_labels( $args, $attribute_label ) {
		$singular_label = $this->get_translated_string_by_name_and_context( self::DOMAIN_WORDPRESS, self::TAXONOMY_SINGULAR_NAME_PREFIX . $attribute_label, null, $attribute_label );
		if ( $singular_label ) {
			$args['labels']['singular_name'] = $singular_label;
		}

		$label = sprintf( 'Product %s', $attribute_label );
		$label = $this->get_translated_string_by_name_and_context( self::DOMAIN_WORDPRESS, 'taxonomy general name: ' . $label, null, $label );
		if ( $label ) {
			$args['labels']['name'] = $label;
		}

		return $args;
	}

	/**
	 * @param string $context
	 * @param string $name
	 * @param string $language
	 *
	 * @return string|false
	 */
	public function get_translated_string_by_name_and_context( $context, $name, $language = null, $value = false ) {
		return apply_filters( 'wpml_translate_single_string', $value, $context, $name, $language );
	}

	/**
	 * Return what msgid lookup would be for a specific content in a MO file.
	 *
	 * @param string $string The 'msgid' string to look up translation.
	 * @param string $string_context The string context.
	 * @return string
	 */
	public function get_msgid_for_mo( $string, $string_context ) {
		return $string_context . self::mo_context_separator() . $string;
	}

	/**
	 * The context separator used in MO files
	 *
	 * @return string
	 */
	private static function mo_context_separator() {
		return chr( 4 );
	}

	/**
	 * Return original msgid from modified lookup used in MO file
	 *
	 * @param string $string The 'msgid' string to look up translation.
	 * @return string
	 */
	private function get_original_string( $string ) {
		return Lst::last( Str::split( self::mo_context_separator(), $string ) );
	}
}
