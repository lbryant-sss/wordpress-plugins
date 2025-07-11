<?php

use WPML\Convert\Ids;
use WPML\FP\Obj;

class WCML_Store_Pages {
	const PRIORITY_SWITCH_PAGES_LANGUAGE_PRE = 9;
	const PRIORITY_INSTALL_PAGES_ACTION_POST = 11;

	/**
	 * Required character to search in MO files: chr(4)
	 */
	const SPECIAL_CHAR_EOT = '';

	/** @var woocommerce_wpml $woocommerce_wpml */
	private $woocommerce_wpml;
	/** @var SitePress $sitepress */
	private $sitepress;

	/** @var int|string $front_page_id */
	private $front_page_id;
	/** @var int $shop_page_id */
	private $shop_page_id;
	/** @var WP_Post|null $shop_page */
	private $shop_page;

	public function __construct( woocommerce_wpml $woocommerce_wpml, SitePress $sitepress ) {

		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress        = $sitepress;

	}

	public function add_hooks() {
		global $pagenow;

		add_action( 'init', [ $this, 'init' ] );

		$this->add_hooks_multilingual_woocommerce_create_pages();

		// update wc pages ids after change default language or create new if not exists.
		add_action( 'icl_after_set_default_language', [ $this, 'after_set_default_language' ], 10, 2 );

		add_filter( 'template_include', [ $this, 'template_loader' ], 100 );

		$is_admin = is_admin();

		if ( $is_admin ) {
			add_action( 'icl_post_languages_options_before', [ $this, 'show_translate_shop_pages_notice' ] );
		}

		/* phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected */
		$getData              = wpml_collect( $_GET );
		$isTranslationPreview = $getData->get( 'preview' ) && $getData->get( 'jobId' );
		if (
			! ( $is_admin || $isTranslationPreview ) ||
			( 'admin.php' === $pagenow && \WCML\Utilities\AdminUrl::PAGE_WOO_SETTINGS === $getData->get( 'page' ) ) ||
			( 'edit.php' === $pagenow && 'page' === $getData->get( 'post_type' ) )
		) {
			// Translate shop page ids
			$this->add_filter_to_get_shop_translated_page_id();
		}

		add_filter( 'woocommerce_get_checkout_url', [ $this, 'get_checkout_page_url' ] );

		add_filter( 'post_type_archive_link', [ $this, 'filter_shop_archive_link' ], 10, 2 );
	}

	private function add_hooks_multilingual_woocommerce_create_pages() {
		add_filter( 'woocommerce_create_pages', [ $this, 'switch_pages_language' ], self::PRIORITY_SWITCH_PAGES_LANGUAGE_PRE );
		add_filter( 'woocommerce_create_pages', [ $this, 'install_pages_action' ], self::PRIORITY_INSTALL_PAGES_ACTION_POST );
	}

	public function init() {

		if ( ! is_admin() ) {
			add_filter( 'pre_get_posts', [ $this, 'shop_page_query' ], 9 );
			add_filter( 'icl_ls_languages', [ $this, 'translate_ls_shop_url' ] );
		}

		$nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( isset( $_POST['create_pages'] ) && wp_verify_nonce( $nonce, 'create_pages' ) ) {
			$this->create_missing_store_pages_with_redirect();
		}

		$this->front_page_id = get_option( 'page_on_front' );
		$this->shop_page_id  = wc_get_page_id( 'shop' );
		$this->shop_page     = get_post( $this->shop_page_id );
	}

	public function switch_pages_language( $pages ) {

		$default_language = $this->sitepress->get_default_language();

		if ( $this->sitepress->get_current_language() !== $default_language ) {
			foreach ( $pages as $key => $page ) {

				switch ( $key ) {
					case 'shop':
						$page['name']  = 'shop';
						$page['title'] = 'Shop';
						break;
					case 'cart':
						$page['name']  = 'cart';
						$page['title'] = 'Cart';
						break;
					case 'checkout':
						$page['name']  = 'checkout';
						$page['title'] = 'Checkout';
						break;
					case 'myaccount':
						$page['name']  = 'my-account';
						$page['title'] = 'My account';
						break;
				}
				if ( 'en' !== $default_language ) {
					$page['name']  = $this->woocommerce_wpml->strings->get_translation_from_woocommerce_mo_file( 'Page slug' . self::SPECIAL_CHAR_EOT . $page['name'], $default_language );
					$page['title'] = $this->woocommerce_wpml->strings->get_translation_from_woocommerce_mo_file( 'Page title' . self::SPECIAL_CHAR_EOT . $page['title'], $default_language );
				}
			}
		}
		return $pages;
	}

	public function install_pages_action( $pages ) {
		/** @var wpdb $wpdb */
		global $wpdb;

		foreach ( $pages as $key => $page ) {

			if ( strlen( $page['content'] ) > 0 ) {
				// Search for an existing page with the specified page content (typically a shortcode)
				$page_found = $wpdb->get_var(
					$wpdb->prepare(
						'
                    SELECT ID FROM ' . $wpdb->posts . " as p 
                    LEFT JOIN {$wpdb->prefix}icl_translations AS icl ON icl.element_id = p.id 
                    WHERE post_type='page' 
                        AND post_content LIKE %s 
                        AND icl.element_type = 'post_page' 
                        AND icl.language_code = %s LIMIT 1;
                    ",
						"%{$page[ 'content' ]}%",
						$this->sitepress->get_default_language()
					)
				);
			} else {
				// Search for an existing page with the specified page slug
				$page_found = $wpdb->get_var(
					$wpdb->prepare(
						'
                    SELECT ID FROM ' . $wpdb->posts . " as p 
                    LEFT JOIN {$wpdb->prefix}icl_translations AS icl ON icl.element_id = p.id 
                    WHERE post_type='page' 
                        AND post_name = %s 
                        AND icl.element_type = 'post_page' 
                        AND icl.language_code = %s LIMIT 1;
                    ",
						$page['name'],
						$this->sitepress->get_default_language()
					)
				);
			}

			if ( ! $page_found ) {
				$page_data = [
					'post_status'    => 'publish',
					'post_type'      => 'page',
					'post_author'    => 1,
					'post_name'      => $page['name'],
					'post_title'     => $page['title'],
					'post_content'   => $page['content'],
					'post_parent'    => ! empty( $page['parent'] ) ? wc_get_page_id( $page['parent'] ) : '',
					'comment_status' => 'closed',
				];
				$page_id   = wp_insert_post( $page_data );

				if ( 'woocommerce_' . $key . '_page_id' ) {
					update_option( 'woocommerce_' . $key . '_page_id', $page_id );
				}
			}

			unset( $pages[ $key ] );
		}

		return $pages;
	}

	public function add_filter_to_get_shop_translated_page_id() {
		$slugs = [
			'shop',
			'cart',
			'checkout',
			'myaccount',
			'terms',
			'refund_returns',
		];

		$prefetchTranslationIds = function() use ( $slugs ) {
			// $getOption :: string -> int|false
			$getOption = function( $slug ) {
				return get_option( 'woocommerce_' . $slug . '_page_id' );
			};

			$ids = wpml_collect( $slugs )
				->map( $getOption )
				->filter()
				->toArray();

			$this->sitepress->post_translations()->prefetch_ids( $ids );
		};

		$addFilters = function() use ( $slugs ) {
			// $convertPageId :: int|string -> int|string
			$convertPageId = function( $id ) {
				return $id ? Ids::convert( $id, 'page', true ) : $id;
			};

			// $addFilter :: string -> void
			$addFilter = function( $slug ) use ( $convertPageId ) {
				add_filter( 'option_woocommerce_' . $slug . '_page_id', $convertPageId );
			};

			wpml_collect( $slugs )->map( $addFilter );
		};

		$prefetchTranslationIds();
		$addFilters();
	}


	/**
	 * Filters WooCommerce query for translated shop page.
	 *
	 * @param WP_Query $q
	 */
	public function shop_page_query( $q ) {
		if ( ! $q->is_main_query() ) {
			return;
		}

		if (
			current_theme_supports( 'woocommerce' ) &&
			! empty( $this->shop_page ) &&
			$this->shop_page->post_status == 'publish' &&
			! empty( $this->front_page_id ) &&
			$q->get( 'post_type' ) !== 'product' &&
			$q->get( 'page_id' ) !== $this->front_page_id &&
			$this->shop_page_id == $q->get( 'page_id' )
		) {
			// do not alter query_object and query_object_id (part 1 of 2)
			global $wp_query;
			$queried_object_original    = isset( $wp_query->queried_object ) ? $wp_query->queried_object : null;
			$queried_object_id_original = isset( $wp_query->queried_object_id ) ? $wp_query->queried_object_id : null;

			$q->set( 'post_type', 'product' );
			$q->set( 'page_id', '' );
			if ( isset( $q->query['paged'] ) ) {
				$q->set( 'paged', $q->query['paged'] );
			}

			// Define a variable so we know this is the front page shop later on.
			wc_maybe_define_constant( 'SHOP_IS_ON_FRONT', true );

			// Get the actual WP page to avoid errors
			// This is hacky but works. Awaiting http://core.trac.wordpress.org/ticket/21096
			global $wp_post_types;

			$q->is_page = true;

			$wp_post_types['product']->ID         = $this->shop_page->ID;
			$wp_post_types['product']->post_title = $this->shop_page->post_title;
			$wp_post_types['product']->post_name  = $this->shop_page->post_name;
			$wp_post_types['product']->post_type  = $this->shop_page->post_type;
			$wp_post_types['product']->ancestors  = get_ancestors( $this->shop_page->ID, $this->shop_page->post_type );

			// Fix conditional functions
			$q->is_singular          = false;
			$q->is_post_type_archive = true;
			$q->is_archive           = true;

			add_filter( 'post_type_archive_title', '__return_empty_string', 5 );

			// do not alter query_object and query_object_id (part 2 of 2)
			if ( is_null( $queried_object_original ) ) {
				unset( $wp_query->queried_object );
			} else {
				$wp_query->queried_object = $queried_object_original;
			}
			if ( is_null( $queried_object_id_original ) ) {
				unset( $wp_query->queried_object_id );
			} else {
				$wp_query->queried_object_id = $queried_object_id_original;
			}
		}
	}

	/**
	 * Translate shop url
	 */
	public function translate_ls_shop_url( $languages, $debug_mode = false ) {

		$shop_id  = $this->shop_page_id;
		$front_id = apply_filters( 'wpml_object_id', $this->front_page_id, 'page' );

		foreach ( $languages as $language ) {
			// shop page
			// obsolete?
			if ( is_post_type_archive( 'product' ) || $debug_mode ) {
				if ( $front_id == $shop_id ) {
					$url = $this->sitepress->language_url( $language['language_code'] );
				} else {
					$this->sitepress->switch_lang( $language['language_code'] );
					$url = get_permalink( apply_filters( 'wpml_object_id', $shop_id, 'page', true, $language['language_code'] ) );
					$this->sitepress->switch_lang();
				}

				$languages[ $language['language_code'] ]['url'] = $url;
			}
		}

		// copy get parameters?
		$gets_passed       = [];
		$parameters_copied = apply_filters(
			'icl_lang_sel_copy_parameters',
			array_map(
				'trim',
				explode(
					',',
					wpml_get_setting_filter(
						'',
						'icl_lang_sel_copy_parameters'
					)
				)
			)
		);
		if ( $parameters_copied ) {
			foreach ( $_GET as $k => $v ) {
				if ( in_array( $k, $parameters_copied ) ) {
					$gets_passed[ $k ] = $v;
				}
			}

			foreach ( $languages as $code => $language ) {
				$languages[ $code ]['url'] = add_query_arg( $gets_passed, $language['url'] );
			}
		}

		return $languages;
	}

	public function create_missing_store_pages_with_redirect() {
		$this->create_missing_store_pages();

		wcml_safe_redirect( \WCML\Utilities\AdminUrl::getStatusTab() );
	}

	private function fetch_wc_pages(): array {
		remove_filter( 'woocommerce_create_pages', [ $this, 'switch_pages_language' ], self::PRIORITY_SWITCH_PAGES_LANGUAGE_PRE );
		remove_filter( 'woocommerce_create_pages', [ $this, 'install_pages_action' ], self::PRIORITY_INSTALL_PAGES_ACTION_POST );

		$pages_translated = [];

		/**
		 * @param array $pages
		 */
		$extract_pages = function ( $pages ) use ( &$pages_translated ): array {
			$pages_translated = $pages;

			// by returning an empty array, the code won't try to add pages (we just want the list of pages for ourselves)
			return [];
		};

		add_filter( 'woocommerce_create_pages', $extract_pages, PHP_INT_MAX );

		WC_Install::create_pages();

		remove_filter( 'woocommerce_create_pages', $extract_pages, PHP_INT_MAX );

		$this->add_hooks_multilingual_woocommerce_create_pages();

		return $pages_translated;
	}

	/**
	 * create missing pages
	 */
	public function create_missing_store_pages() {
		global $wp_rewrite;
		$miss_lang = $this->get_missing_store_pages();

		// dummy array for names
		$names = [
			__( 'Cart', 'woocommerce-multilingual' ),
			__( 'Checkout', 'woocommerce-multilingual' ),
			__( 'Checkout &rarr; Pay', 'woocommerce-multilingual' ),
			__( 'Order Received', 'woocommerce-multilingual' ),
			__( 'My Account', 'woocommerce-multilingual' ),
			__( 'Change Password', 'woocommerce-multilingual' ),
			__( 'Edit My Address', 'woocommerce-multilingual' ),
			__( 'Logout', 'woocommerce-multilingual' ),
			__( 'Lost Password', 'woocommerce-multilingual' ),
			__( 'View Order', 'woocommerce-multilingual' ),
			__( 'Shop', 'woocommerce-multilingual' ),
		];

		if ( isset( $miss_lang['codes'] ) ) {
			$wp_rewrite = new WP_Rewrite();

			$check_pages      = $this->get_wc_pages();
			$default_language = $this->sitepress->get_default_language();
			if ( in_array( $default_language, $miss_lang['codes'] ) ) {
				$miss_lang['codes'] = array_merge( [ $default_language ], array_diff( $miss_lang['codes'], [ $default_language ] ) );
			}

			foreach ( $miss_lang['codes'] as $mis_lang ) {
				$args = [];

				$this->switch_lang( $mis_lang );

				$pages_translated = $this->fetch_wc_pages();

				foreach ( $check_pages as $page ) {
					$orig_id       = wc_get_page_id( $page );
					$trid          = $this->sitepress->get_element_trid( $orig_id, 'post_page' );
					$translations  = $this->sitepress->get_element_translations( $trid, 'post_page', true );
					$translationId = Obj::path( [ $mis_lang, 'element_id' ], $translations );

					if ( ! $translationId ) {
						$orig_page = get_post( $orig_id );

						if ( isset( $pages_translated[ $page ] ) ) {
							$translated_page = $pages_translated[ $page ];

							$page_title   = $translated_page['title'];
							$page_content = $translated_page['content'];
						} else {
							$page_title = $mis_lang !== 'en' ? translate( $orig_page->post_title, 'woocommerce-multilingual' ) : $orig_page->post_title;
							$page_content = $orig_page->post_content;
						}

						$page_content = $this->legacy_check_if_page_use_shortcode( $page, $orig_page, $page_content );

						$args['post_title']     = $page_title;
						$args['post_content']   = $page_content;
						$args['post_type']      = $orig_page->post_type;
						$args['post_excerpt']   = $orig_page->post_excerpt;
						$args['post_status']    = $orig_page->post_status;
						$args['menu_order']     = $orig_page->menu_order;
						$args['ping_status']    = $orig_page->ping_status;
						$args['comment_status'] = $orig_page->comment_status;
						$post_parent            = apply_filters( 'wpml_object_id', $orig_page->post_parent, 'page', false, $mis_lang );
						$args['post_parent']    = is_null( $post_parent ) ? 0 : $post_parent;

						WCML\Utilities\Post::insert( $args, $mis_lang, $trid );
					} elseif ( get_post_status( $translationId ) !== 'publish' ) {
						if ( get_post_status( $translationId ) == 'trash' && $mis_lang == $default_language ) {
							update_option( 'woocommerce_' . $page . '_page_id', $translationId );
						}

						$this->sitepress->set_element_language_details( $translationId, 'post_page', $trid, $mis_lang );

						$args = [
							'ID'          => $translationId,
							'post_status' => 'publish',
						];

						wp_update_post( $args );
					}


				}
				$this->switch_lang();
			}
		}
	}

	private function switch_lang( $lang_code = false ) {
		$this->sitepress->switch_lang( $lang_code );
	}

	/**
	 * get missing pages
	 * return array;
	 */
	public function get_missing_store_pages() {

		$check_pages = $this->get_wc_pages();

		$missing_lang      = [];
		$pages_in_progress = [];

		foreach ( $check_pages as $page ) {
			$page_id  = wc_get_page_id( $page );
			$page_obj = get_post( $page_id );
			if ( ! $page_id || ! $page_obj || $page_obj->post_status !== 'publish' ) {
				return 'non_exist';
			}
		}

		$languages = $this->sitepress->get_active_languages();

		$missing_lang_codes = [];

		foreach ( $check_pages as $page ) {
			$store_page_id               = wc_get_page_id( $page );
			$trid                        = $this->sitepress->get_element_trid( $store_page_id, 'post_page' );
			$translations                = $this->sitepress->get_element_translations( $trid, 'post_page', true );
			$pages_in_progress_miss_lang = '';
			foreach ( $languages as $language ) {
				if ( ! in_array( $language['code'], $missing_lang_codes ) &&
					 ( ! isset( $translations[ $language['code'] ] ) || ( ! is_null( $translations[ $language['code'] ]->element_id ) && get_post_status( $translations[ $language['code'] ]->element_id ) !== 'publish' ) ) ) {

					$missing_lang_codes[] = $language['code'];

					$missing_lang[] = $language;

					continue;
				}

				if ( isset( $translations[ $language['code'] ] ) && is_null( $translations[ $language['code'] ]->element_id ) ) {

					$pages_in_progress[ $store_page_id ][] = $language;

				}
			}
		}

		foreach ( $pages_in_progress as $key => $page_in_progress ) {
			$pages_in_progress_notice[ $key ]['page'] = get_the_title( $key ) . ' :';
			$pages_in_progress_notice[ $key ]['lang'] = $page_in_progress;

		}

		$status = [];

		if ( ! empty( $missing_lang ) ) {
			$status['lang']  = $missing_lang;
			$status['codes'] = $missing_lang_codes;
		}

		if ( ! empty( $pages_in_progress_notice ) ) {
			$status['in_progress'] = $pages_in_progress_notice;
		}

		if ( ! empty( $status ) ) {
			return $status;
		} else {
			return false;
		}
	}

	/**
	 * Filters WooCommerce checkout link.
	 */
	public function get_checkout_page_url() {
		return get_permalink( apply_filters( 'wpml_object_id', wc_get_page_id( 'checkout' ), 'page', true ) );
	}

	public function get_wc_pages() {
		$pages = apply_filters(
			'wcml_wc_installed_pages',
			[
				'woocommerce_shop_page_id',
				'woocommerce_cart_page_id',
				'woocommerce_checkout_page_id',
				'woocommerce_myaccount_page_id',
			]
		);

		foreach ( $pages as &$page ) {
			$page = preg_replace( '/(woocommerce_)(.*)(_page_id)/', '$2', $page );
		}

		return $pages;
	}

	public function after_set_default_language( $code, $previous_code ) {
		/** @var wpdb $wpdb */
		global $wpdb;

		$this->create_missing_store_pages();
		$pages = $this->get_wc_pages();

		foreach ( $pages as $page ) {
			if ( $page_id = wc_get_page_id( $page ) ) {
				$trnsl_id = apply_filters( 'wpml_object_id', $page_id, 'page', false, $code );
				if ( ! is_null( $trnsl_id ) ) {
					$wpdb->update( $wpdb->options, [ 'option_value' => $trnsl_id ], [ 'option_name' => 'woocommerce_' . $page . '_page_id' ] );
				}
			}
		}

		// Clear any unwanted data
		wc_delete_product_transients();
		delete_transient( 'woocommerce_cache_excluded_uris' );
	}

	public function template_loader( $template ) {

		if ( is_product_taxonomy() ) {

			$current_language = $this->sitepress->get_current_language();
			$default_language = $this->sitepress->get_default_language();

			if ( $current_language != $default_language ) {

				$term     = get_queried_object();
				$taxonomy = $term->taxonomy;
				$prefix   = 'taxonomy-' . $taxonomy;
				$paths    = [ '', WC()->template_path() ];

				if ( is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) {
					$file = 'taxonomy-' . $taxonomy . '.php';
				} else {
					$file = 'archive-product.php';
				}

				$getTemplates = function( $terms_to_check, $lang ) use ( $file, $prefix, $paths ) {
					$templates = [];
					foreach ( $paths as $path ) {
						foreach ( $terms_to_check as $term_id => $term_slug ) {
							$templates[] = $path . "$prefix-{$lang}-{$term_slug}.php";
							$templates[] = $path . "$prefix-{$lang}-{$term_id}.php";
							$templates[] = $path . "$prefix-{$term_slug}.php";
							$templates[] = $path . "$prefix-{$term_id}.php";
						}
						$templates[] = $path . "$prefix-{$lang}.php";
						$templates[] = $path . "$prefix.php";
						$templates[] = $path . $file;
					}

					return $templates;
				};

				// We don't override if the original `$template` does not look like the template we would like to convert.
				$terms_to_check = [ $term->term_id => $term->slug ];

				if ( ! in_array( $template, $getTemplates( $terms_to_check, $default_language ) ) ) {
					return $template;
				}

				// Add original term and locate the template.
				$original_term_id = $this->sitepress->get_object_id( $term->term_id, $taxonomy, true, $default_language );
				$original_term    = $this->woocommerce_wpml->terms->wcml_get_term_by_id( $original_term_id, $taxonomy );

				if ( $original_term ) {
					$terms_to_check[ $original_term_id ] = $original_term->slug;
				}

				$loaded_template = locate_template( array_unique( $getTemplates( $terms_to_check, $current_language ) ) );

				if ( $loaded_template ) {
					$template = $loaded_template;
				}
			}
		}

		return $template;

	}

	public function show_translate_shop_pages_notice() {
		if ( empty( $this->woocommerce_wpml->settings['set_up_wizard_run'] ) ) {

			$is_shop_page = false;

			$pages           = $this->get_wc_pages();
			$current_page_id = get_the_ID();
			foreach ( $pages as $page ) {
				$page_id = wc_get_page_id( $page );
				if ( $page_id && $page_id === $current_page_id ) {
					$is_shop_page = true;
					break;
				}
			}

			if ( $is_shop_page ) {

				$is_translated    = true;
				$active_languages = array_keys( $this->sitepress->get_active_languages() );

				$trid         = $this->sitepress->get_element_trid( $current_page_id, 'post_page' );
				$translations = $this->sitepress->get_element_translations( $trid, $current_page_id, true, true, true );

				foreach ( $active_languages as $language ) {
					if ( ! isset( $translations[ $language ] ) ) {
						$is_translated = false;
						break;
					}
				}

				if ( ! $is_translated ) {
					$text = sprintf(
						/* translators: %1$s and %2$s are opening and closing HTML link tags */
						__( 'To quickly translate this and other WooCommerce store pages, please run the %1$ssetup wizard%2$s.', 'woocommerce-multilingual' ),
						'<a href="' . \WCML\Utilities\AdminUrl::getSetup() . '">',
						'</a>'
					);

					echo '<div class="notice notice-error inline">';
					echo '<p><i class="otgs-ico-warning"></i> ' . $text . '</p>';
					echo '</div>';
				}
			}
		}

	}

	public function filter_shop_archive_link( $link, $post_type ) {

		if (
			'product' === $post_type &&
			(int) $this->front_page_id === (int) $this->shop_page_id &&
			$this->sitepress->get_current_language() !== $this->sitepress->get_default_language()
		) {
			$link = home_url( '/' );
		}

		return $link;
	}

	/**
	 * if the original page still uses a shortcode, we will use it as a base for the language versions
	 * - shortcode is deprecated since WooC 8.3.0
	 *
	 * @param string $page
	 * @param \WP_Post $orig_page
	 * @param string $page_content
	 */
	private function legacy_check_if_page_use_shortcode( $page, $orig_page, string $page_content ): string {
		if ( $page == 'checkout' ) {
			if ( false !== strpos( '[woocommerce_checkout]', $orig_page->post_content ) ) {
				$page_content = $orig_page->post_content;
			}
		} else if ( $page == 'cart' ) {
			if ( false !== strpos( '[woocommerce_cart]', $orig_page->post_content ) ) {
				$page_content = $orig_page->post_content;
			}
		}

		return (string) $page_content;
	}

}
