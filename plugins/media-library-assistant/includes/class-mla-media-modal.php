<?php
/**
 * Media Library Assistant Media Manager enhancements
 *
 * @package Media Library Assistant
 * @since 1.20
 */
 
/**
 * Class MLA (Media Library Assistant) Modal contains enhancements for the WordPress 3.5+ Media Manager
 *
 * @package Media Library Assistant
 * @since 1.20
 */
class MLAModal {
	/**
	 * Slug for localizing and enqueueing CSS - Add Media and related dialogs
	 *
	 * @since 1.20
	 *
	 * @var	string
	 */
	const JAVASCRIPT_MEDIA_MODAL_STYLES = 'mla-media-modal-style';

	/**
	 * Slug for enqueueing JavaScript - Define ajaxurl if required
	 *
	 * @since 2.79
	 *
	 * @var	string
	 */
	const JAVASCRIPT_DEFINE_AJAXURL_SLUG = 'mla-define-ajaxurl-scripts';

	/**
	 * Slug for localizing and enqueueing JavaScript - Add Media and related dialogs
	 *
	 * @since 1.20
	 *
	 * @var	string
	 */
	const JAVASCRIPT_MEDIA_MODAL_SLUG = 'mla-media-modal-scripts';

	/**
	 * Object name for localizing JavaScript - Add Media and related dialogs
	 *
	 * @since 1.20
	 *
	 * @var	string
	 */
	const JAVASCRIPT_MEDIA_MODAL_OBJECT = 'mla_media_modal_vars';

	/**
	 * Object name for localizing JavaScript - Terms Search popup
	 *
	 * @since 1.90
	 *
	 * @var	string
	 */
	const JAVASCRIPT_TERMS_SEARCH_OBJECT = 'mla_terms_search_vars';

	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.20
	 *
	 * @return	void
	 */
	public static function initialize() {
		/*
		 * WordPress 3.5's Media Manager and 4.0's Media Grid are supported on the server
		 * by /wp-includes/media.php function wp_enqueue_media(), which contains:
		 *
		 * $settings = apply_filters( 'media_view_settings', $settings, $post );
		 * $strings  = apply_filters( 'media_view_strings',  $strings,  $post );
		 *
		 * wp_enqueue_media() then contains a require_once for
		 * /wp-includes/media-template.php, which contains:
		 * do_action( 'print_media_templates' );
		 *
 		 * Finally wp_enqueue_media() contains:
		 * do_action( 'wp_enqueue_media' );
		 */

		if ( ( ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_TOOLBAR ) ) || ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_GRID_TOOLBAR ) ) ) ) {
			add_filter( 'media_view_settings', 'MLAModal::mla_media_view_settings_filter', 10, 2 );
			add_filter( 'media_view_strings', 'MLAModal::mla_media_view_strings_filter', 10, 2 );
			add_action( 'wp_enqueue_media', 'MLAModal::mla_wp_enqueue_media_action', 10, 0 );
			add_filter( 'media_library_months_with_files', 'MLAModal::mla_media_library_months_with_files_filter', 10, 1 );

			// print_media_templates is called from wp-includes/media-template.php wp_print_media_templates
			add_action( 'print_media_templates', 'MLAModal::mla_print_media_templates_action', 10, 0 );
			// wp_print_footer_scripts is called from wp-includes/block-editor.php _wp_get_iframed_editor_assets()
			add_action( 'wp_print_footer_scripts', 'MLAModal::mla_print_media_templates_action', 10, 0 );

			add_action( 'admin_init', 'MLAModal::mla_admin_init_action' );
		} // Media Modal support enabled
	}

	/**
	 * Allows overriding the list of months displayed in the media library.
	 *
	 * Called from /wp-includes/media.php function wp_enqueue_media()
	 *
	 * @since 2.66
	 *
	 * @param array|null An array of objects with `month` and `year`
	 *                   properties, or `null` (or any other non-array value)
	 *                   for default behavior.
	 *
	 * @return	array	objects with `month` and `year` properties.
	 */
	public static function mla_media_library_months_with_files_filter( $months = NULL ) {
		global $wpdb;
		static $library_months_with_files = NULL;

		if ( is_array( $library_months_with_files ) ) {
			return $library_months_with_files;
		}

		$library_months_with_files = $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month FROM $wpdb->posts WHERE post_type = %s ORDER BY post_date DESC ", 'attachment' ) ); // phpcs:ignore

		return $library_months_with_files;
	}

	/**
	 * Display a monthly dropdown for filtering items
	 *
	 * Adapted from /wp-admin/includes/class-wp-list-table.php function months_dropdown()
	 *
	 * @since 1.20
	 *
	 * @return	array	( value => label ) pairs
	 */
	private static function _months_dropdown() {
		global $wp_locale;

		$months = self::mla_media_library_months_with_files_filter();

		$month_count = count( $months );
		$month_array = array( '0' => __( 'Show all dates', 'media-library-assistant' ) );

		if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) ) {
			return $month_array;
		}

		foreach ( $months as $index => $arc_row ) {
			if ( 0 == $arc_row->year ) {
				continue;
			}

			$prefix = zeroise( $index, 3 );
			$month = zeroise( $arc_row->month, 2 );
			$year = $arc_row->year;
//			$month_array[ esc_attr( $prefix . $arc_row->year . $month ) ] = 
			$month_array[ 'M' . $prefix . $arc_row->year . $month ] = 
				/* translators: 1: month name, 2: 4-digit year */
				sprintf( __( '%1$s %2$d', 'media-library-assistant' ), $wp_locale->get_month( $month ), $year );
		}

		return apply_filters( 'mla_media_modal_months_dropdown', $month_array, 'attachment' );
	}

	/**
	 * Extract value and text elements from Dropdown HTML option tags
	 *
	 * @since 1.20
	 *
	 * @param	string	HTML markup for taxonomy terms dropdown <select> tag
	 *
	 * @return	array	( 'class' => $class_array, 'value' => $value_array, 'text' => $text_array )
	 */
	public static function mla_terms_options( $markup ) {
		$match_count = preg_match_all( "#\<option(( class=\"([^\"]+)\" )|( ))value=((\'([^\']+)\')|(\"([^\"]+)\"))([^\>]*)\>([^\<]*)\<.*#", $markup, $matches );
		if ( ( $match_count == false ) || ( $match_count == 0 ) ) {
			return array( 'class' => array( '' ), 'value' => array( '0' ), 'text' => array( 'Show all terms' ) );
		}

		$class_array = array();
		$value_array = array();
		$text_array = array();

		foreach ( $matches[11] as $index => $text ) {
			$class_array[ $index ] = $matches[3][ $index ];
			$value_array[ $index ] = ( ! '' == $matches[6][ $index ] )? $matches[7][ $index ] : $matches[9][ $index ];

			$current_version = get_bloginfo( 'version' );
			if ( version_compare( $current_version, '3.9', '<' ) && version_compare( $current_version, '3.6', '>=' ) ) {
				$text_array[ $index ] = str_replace( '&nbsp;', '-', $text);
			} else {
				$text_array[ $index ] = $text;
			}

		} // foreach

		return apply_filters( 'mla_media_modal_terms_options', array( 'class' => $class_array, 'value' => $value_array, 'text' => $text_array ) );
	}

	/**
	 * Share the settings values between mla_media_view_settings_filter
	 * and mla_print_media_templates_action
	 *
	 * @since 1.20
	 *
	 * @var	array
	 */
	private static $mla_media_modal_settings = array(
			'screen' => 'modal',
			'state' => 'initial',
			'comma' => ',',
			'ajaxNonce' => '',
			'prefix' => 0,
			'ajaxFillCompatAction' => MLACore::JAVASCRIPT_FILL_COMPAT_ACTION,
			'ajaxQueryAttachmentsAction' => MLACore::JAVASCRIPT_QUERY_ATTACHMENTS_ACTION,
			'ajaxUpdateCompatAction' => MLACore::JAVASCRIPT_UPDATE_COMPAT_ACTION,
			'enableDetailsCategory' => false,
			'enableDetailsTag' => false,
			'enableMimeTypes' => false,
			'enableMonthsDropdown' => false,
			'enableSearchBox' => false,
			'enableSearchBoxControls' => false,
			'enableTermsDropdown' => false,
			'enableTermsSearch' => false,
			'enableTermsAutofill' => false,
			'query' => array( 'initial' => array (
				// NULL values replaced by filtered initial values in mla_media_view_settings_filter
				'filterMime' => NULL,
				'filterMonth' => NULL,
				'filterTerm' => NULL,
				'searchConnector' => NULL,
				'searchFields' => NULL,
				'searchValue' => NULL,
				'searchClicks' => 0,
			) ),			
			'allMimeTypes' => array(),
			'uploadMimeTypes' => array(),
			'months' => '',
			'termsClass' => array(),
			'termsIndent' => '&nbsp;',
			'termsTaxonomy' => '',
			'termsCustom' => false,
			'termsText' => array(),
			'termsValue' => array(),
			'generateTagButtons' => false,
			'generateTagUl' => false,
			'removeTerm' => '',
			);

	/**
	 * Adds settings values to be passed to the Media Manager in /wp-includes/js/media-views.js.
	 * Declared public because it is a filter.
	 *
	 * @since 1.20
	 *
	 * @param	array	associative array with setting => value pairs
	 * @param	object || NULL	current post object, if available
	 *
	 * @return	array	updated $settings array
	 */
	public static function mla_media_view_settings_filter( $settings, $post ) {
		// If we know what screen we're on we can test our enabling options
		self::$mla_media_modal_settings['screen'] = 'modal';
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();

			if ( is_object( $screen) && 'upload' == $screen->base ) {
				self::$mla_media_modal_settings['screen'] = 'grid';
			}
		}

		$default_types = MLACore::mla_get_option( MLACoreOptions::MLA_POST_MIME_TYPES, true );
		self::$mla_media_modal_settings['comma'] = _x( ',', 'tag_delimiter', 'media-library-assistant' );
		self::$mla_media_modal_settings['ajaxNonce'] = wp_create_nonce( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
		self::$mla_media_modal_settings['prefix'] = 0;
		self::$mla_media_modal_settings['allMimeTypes'] = MLAMime::mla_pluck_table_views();
		self::$mla_media_modal_settings['allMimeTypes']['detached'] = $default_types['detached']['plural'];
		self::$mla_media_modal_settings['allMimeTypes']['attached'] = $default_types['attached']['plural'];
		self::$mla_media_modal_settings['allMimeTypes']['mine'] = $default_types['mine']['plural'];

		// Trash items are allowed in the Media/Library Grid view
		if ( EMPTY_TRASH_DAYS && MEDIA_TRASH ) {
			self::$mla_media_modal_settings['allMimeTypes']['trash'] = $default_types['trash']['plural'];
		}

		// Set Featured Image allows views based on custom field queries
		self::$mla_media_modal_settings['uploadMimeTypes'] = MLAMime::mla_pluck_table_views( true );

		self::$mla_media_modal_settings['months'] = self::_months_dropdown('attachment');

		self::$mla_media_modal_settings['termsTaxonomy'] =  MLACore::mla_taxonomy_support('', 'filter');
		$terms_options = self::mla_terms_options( MLA_List_Table::mla_get_taxonomy_filter_dropdown() );
		self::$mla_media_modal_settings['termsCustom'] = ( MLACoreOptions::MLA_FILTER_METAKEY ==  self::$mla_media_modal_settings['termsTaxonomy'] );
		self::$mla_media_modal_settings['termsClass'] = $terms_options['class'];
		self::$mla_media_modal_settings['termsValue'] = $terms_options['value'];
		self::$mla_media_modal_settings['termsText'] = $terms_options['text'];

		$current_version = get_bloginfo( 'version' );
		if ( version_compare( $current_version, '3.9', '<' ) && version_compare( $current_version, '3.6', '>=' ) ) {
			self::$mla_media_modal_settings['termsIndent'] = '-';
		}

		if ( version_compare( $current_version, '4.6.99', '>' ) ) {
			self::$mla_media_modal_settings['generateTagButtons'] = true;
			self::$mla_media_modal_settings['removeTerm'] = __( 'Remove term', 'media-library-assistant' );
		}
		
		if ( version_compare( $current_version, '4.8.99', '>' ) ) {
			self::$mla_media_modal_settings['generateTagUl'] = true;
		}
		
		self::$mla_media_modal_settings['enableMediaGrid'] = ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_GRID_TOOLBAR ) );
		self::$mla_media_modal_settings['enableMediaModal'] = ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_TOOLBAR ) );
		self::$mla_media_modal_settings['enableDetailsCategory'] = ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_DETAILS_CATEGORY_METABOX ) );
		self::$mla_media_modal_settings['enableDetailsTag'] = ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_DETAILS_TAG_METABOX ) );
		self::$mla_media_modal_settings['enableMimeTypes'] = ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_MIMETYPES ) );
		self::$mla_media_modal_settings['enableMonthsDropdown'] = ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_MONTHS ) );
		self::$mla_media_modal_settings['enableSearchBox'] = ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_SEARCHBOX ) );
		self::$mla_media_modal_settings['enableSearchBoxControls'] = ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_SEARCHBOX_CONTROLS ) );

		$supported_taxonomies = MLACore::mla_supported_taxonomies('support');
		self::$mla_media_modal_settings['enableTermsDropdown'] = ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_TERMS ) ) && ( ! empty( $supported_taxonomies ) );
		self::$mla_media_modal_settings['enableTermsAutofill'] = ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_DETAILS_AUTOFILL ) ) && ( ! empty( $supported_taxonomies ) );
		self::$mla_media_modal_settings['enableTermsAutoopen'] = ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_DETAILS_AUTOOPEN ) ) && ( ! empty( $supported_taxonomies ) );

		$supported_taxonomies = MLACore::mla_supported_taxonomies('term-search');
		self::$mla_media_modal_settings['enableTermsSearch'] = ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_TERMS_SEARCH ) ) && ( ! empty( $supported_taxonomies ) );

		// Compile a list of the enhanced taxonomies
		self::$mla_media_modal_settings['enhancedTaxonomies'] = array();
		foreach ( get_taxonomies( array ( 'show_ui' => true ), 'objects' ) as $key => $value ) {
			if ( MLACore::mla_taxonomy_support( $key ) ) {
				if ( ! $use_checklist = $value->hierarchical ) {
					$use_checklist =  MLACore::mla_taxonomy_support( $key, 'flat-checklist' );
				}

				/*
				 * Make sure the appropriate MMMW Enhancement option has been checked
				 */
				if ( $use_checklist ) {
					if ( 'checked' === MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_DETAILS_CATEGORY_METABOX ) ) {
						self::$mla_media_modal_settings['enhancedTaxonomies'][] = $key;
					}
				} else {
					if ( 'checked' === MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_DETAILS_TAG_METABOX ) ) {
						self::$mla_media_modal_settings['enhancedTaxonomies'][] = $key;
					}
				}
			} // taxonomy_support
		} // each taxonomy

		// Set and filter the initial values for toolbar controls
		$search_defaults = MLACore::mla_get_option( MLACoreOptions::MLA_SEARCH_MEDIA_FILTER_DEFAULTS );
		$initial_values = array(
			'filterMime' => 'all',
			'filterUploaded' => 'all',
			'filterMonth' => 0,
			'filterTerm' => self::$mla_media_modal_settings['termsCustom'] ? MLACoreOptions::ALL_MLA_FILTER_METAKEY : 0,
			'searchConnector' => $search_defaults['search_connector'],
			'searchFields' => $search_defaults['search_fields'],
			'searchValue' => '',
			'searchClicks' => 0,
		);

		$initial_values = apply_filters( 'mla_media_modal_initial_filters', $initial_values, $post );

		// No supported taxonomies implies no "terms" search
		$supported_taxonomies = MLACore::mla_supported_taxonomies('support');
		if ( empty( $supported_taxonomies ) ) {
			$index = array_search( 'terms', $initial_values['searchFields'] );
			if ( false !== $index ) {
				unset( $initial_values['searchFields'][ $index ] );
			}
		}

		/*
		 * Except for filterMime/post_mime_type, these will be passed
		 * back to the server in the query['s'] field.
		 */ 
		self::$mla_media_modal_settings['query']['initial']['filterMime'] = $initial_values['filterMime']; // post_mime_type 'image'; // 
		self::$mla_media_modal_settings['query']['initial']['filterUploaded'] = $initial_values['filterUploaded']; // post_mime_type 'image'; // 
		self::$mla_media_modal_settings['query']['initial']['filterMonth'] = $initial_values['filterMonth']; // mla_filter_month '201404'; // 
		self::$mla_media_modal_settings['query']['initial']['filterTerm'] = $initial_values['filterTerm']; // mla_filter_term '175'; //
		self::$mla_media_modal_settings['query']['initial']['searchConnector'] = $initial_values['searchConnector']; // mla_search_connector 'OR'; //
		self::$mla_media_modal_settings['query']['initial']['searchFields'] = $initial_values['searchFields']; // mla_search_fields array( 'excerpt', 'title', 'content' ); //
		self::$mla_media_modal_settings['query']['initial']['searchValue'] = $initial_values['searchValue']; // mla_search_value 'col'; //
		self::$mla_media_modal_settings['query']['initial']['searchClicks'] = 0; // mla_search_clicks, to force transmission

		$settings = array_merge( $settings, array( 'mla_settings' => self::$mla_media_modal_settings ) );
		return apply_filters( 'mla_media_modal_settings', $settings, $post );
	} // mla_media_view_settings_filter

	/**
	 * Adds string values to be passed to the Media Manager in /wp-includes/js/media-views.js.
	 * Declared public because it is a filter.
	 *
	 * @since 1.20
	 *
	 * @param	array	associative array with string => value pairs
	 * @param	object || NULL	current post object, if available
	 *
	 * @return	array	updated $strings array
	 */
	public static function mla_media_view_strings_filter( $strings, $post ) {
		$mla_strings = array(
			'ajaxurl' => admin_url( 'admin-ajax.php', 'relative' ),
			'searchBoxPlaceholder' => __( 'Search Box', 'media-library-assistant' ),
			'loadingText' => __( 'Loading...', 'media-library-assistant' ),
			'searchBoxControlsStyle' => ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_SEARCHBOX_CONTROLS ) ) ? 'display: inline;' : 'display: none;',
			);

		$strings = array_merge( $strings, array( 'mla_strings' => $mla_strings ) );
		return apply_filters( 'mla_media_modal_strings', $strings, $post );
	} // mla_mla_media_view_strings_filter

	/**
	 * Enqueues the mla-media-modal-scripts.js file, adding it to the Media Manager scripts.
	 * Declared public because it is an action.
	 *
	 * @since 1.20
	 *
	 * @return	void
	 */
	public static function mla_wp_enqueue_media_action( ) {
		global $wp_locale;

		// If we know what screen we're on we can test our enabling options
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();

			if ( is_object( $screen ) ) {
				if ( 'upload' == $screen->base ) {
					if ( 'checked' != MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_GRID_TOOLBAR ) ) {
						return;
					}
				} elseif ( 'checked' != MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_TOOLBAR ) ) {
					return;
				}
			}
		}

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		if ( $wp_locale->is_rtl() ) {
			wp_register_style( self::JAVASCRIPT_MEDIA_MODAL_STYLES, MLA_PLUGIN_URL . 'css/mla-media-modal-style-rtl.css', false, MLACore::mla_script_version() );
		} else {
			wp_register_style( self::JAVASCRIPT_MEDIA_MODAL_STYLES, MLA_PLUGIN_URL . 'css/mla-media-modal-style.css', false, MLACore::mla_script_version() );
		}

		wp_enqueue_style( self::JAVASCRIPT_MEDIA_MODAL_STYLES );

		// Make sure ajaxurl is defined before loading wp-lists and suggest
		wp_enqueue_script( self::JAVASCRIPT_DEFINE_AJAXURL_SLUG, MLA_PLUGIN_URL . "js/mla-define-ajaxurl-scripts{$suffix}.js", array(), MLACore::mla_script_version(), false );

		// Gutenberg Block Editor won't tolerate loading 'wp-lists' in the header section; load in footer section
		if ( ! function_exists( 'use_block_editor_for_post_type' ) || isset( $_GET['classic-editor'] ) ) {
			wp_enqueue_script( self::JAVASCRIPT_MEDIA_MODAL_SLUG, MLA_PLUGIN_URL . "js/mla-media-modal-scripts{$suffix}.js", array( 'media-views', 'wp-lists', 'suggest' ), MLACore::mla_script_version(), false );
		} else {
			wp_enqueue_script( self::JAVASCRIPT_MEDIA_MODAL_SLUG, MLA_PLUGIN_URL . "js/mla-media-modal-scripts{$suffix}.js", array( 'media-views', 'wp-lists', 'suggest' ), MLACore::mla_script_version(), true );
		}
		
		if ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_TERMS_SEARCH ) ) {
			MLAModal::mla_add_terms_search_scripts();
		}
	} // mla_wp_enqueue_media_action

	/**
	 * Prints the templates used in the MLA Media Manager enhancements.
	 * Declared public because it is an action.
	 *
	 * @since 1.20
	 *
	 * @return	void	echoes HTML script tags for the templates
	 */
	public static function mla_print_media_templates_action( ) {
		static $template = NULL;
		
		// If we know what screen we're on we can test our enabling options
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();

			if ( is_object( $screen ) ) {
				if ( 'upload' == $screen->base ) {
					if ( 'checked' != MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_GRID_TOOLBAR ) ) {
						return;
					}
				} elseif ( 'checked' != MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_TOOLBAR ) ) {
					return;
				}
			}
		} else {
			$screen = NULL;
		}

		if ( empty( $template ) ) {
			// Include mla javascript templates
			$template_path = apply_filters( 'mla_media_modal_template_path', MLA_PLUGIN_PATH . 'includes/mla-media-modal-js-template.php', $screen);

			if ( ! empty( $template_path ) ) {
				ob_start();
				require_once $template_path;
				$template = ob_get_clean();
			}
		}

		echo $template;  // phpcs:ignore
	} // mla_print_media_templates_action

	/**
	 * Clean up the 'save-attachment-compat' values, removing taxonomy updates MLA already handled
	 *
	 * @since 1.20
	 *
	 * @return	void	
	 */
	public static function mla_admin_init_action() {
		/*
		 * Build a list of enhanced taxonomies for later $_REQUEST/$_POST cleansing.
		 * Remove "Media Categories" instances, if present.
		 */
		$enhanced_taxonomies = array();
		foreach ( get_taxonomies( array ( 'show_ui' => true ), 'objects' ) as $key => $value ) {
			if ( MLACore::mla_taxonomy_support( $key ) ) {
				if ( ! $use_checklist = $value->hierarchical ) {
					$use_checklist = MLACore::mla_taxonomy_support( $key, 'flat-checklist' );
				}

				if ( $use_checklist ) {
					if ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_DETAILS_CATEGORY_METABOX ) ) {
						$enhanced_taxonomies[] = $key;

						if ( class_exists( 'Media_Categories' ) && is_array( Media_Categories::$instances ) ) {
							foreach( Media_Categories::$instances as $index => $instance ) {
								if ( $instance->taxonomy == $key ) {
									// unset( Media_Categories::$instances[ $index ] );
									Media_Categories::$instances[ $index ]->taxonomy = 'MLA-has-disabled-this-instance';
								}
							}
						} // class_exists
					} // checked
				} // use_checklist
			} // supported
		} // foreach taxonomy 
	} // mla_admin_init_action

	/**
	 * Add the styles and scripts for the "Search Terms" popup modal window,
	 * but only once per page load
	 *
	 * @since 1.90
	 *
	 * @return	void
	 */
	public static function mla_add_terms_search_scripts() {
		global $wp_locale;
		static $add_the_scripts = true;

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		if ( $add_the_scripts ) {
			if ( $wp_locale->is_rtl() ) {
				wp_register_style( MLACore::STYLESHEET_SLUG . '-terms-search', MLA_PLUGIN_URL . 'css/mla-style-terms-search-rtl.css', false, MLACore::mla_script_version() );
			} else {
				wp_register_style( MLACore::STYLESHEET_SLUG . '-terms-search', MLA_PLUGIN_URL . 'css/mla-style-terms-search.css', false, MLACore::mla_script_version() );
			}

			wp_enqueue_style( MLACore::STYLESHEET_SLUG . '-terms-search' );

			wp_enqueue_script( MLACore::JAVASCRIPT_INLINE_EDIT_SLUG . '-terms-search', MLA_PLUGIN_URL . "js/mla-terms-search-scripts{$suffix}.js", 
				array( 'jquery' ), MLACore::mla_script_version(), false );

			$script_variables = array(
				'useDashicons' => false,
				'useSpinnerClass' => false,
			);

			if ( version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ) {
				$script_variables['useDashicons'] = true;
			}

			if ( version_compare( get_bloginfo( 'version' ), '4.2', '>=' ) ) {
				$script_variables['useSpinnerClass'] = true;
			}

			wp_localize_script( MLACore::JAVASCRIPT_INLINE_EDIT_SLUG . '-terms-search', self::JAVASCRIPT_TERMS_SEARCH_OBJECT, $script_variables );

			/*
			 * Insert the hidden form for the Search Terms popup window
			 */
			MLAModal::mla_add_terms_search_form();

			$add_the_scripts = false;
		}
	}

	/**
	 * Add the hidden form for the "Search Terms" popup modal window,
	 * but only once per page load
	 *
	 * @since 1.90
	 *
	 * @return	void
	 */
	public static function mla_add_terms_search_form() {
		static $add_the_form = true;

		if ( $add_the_form ) {
			add_action( 'admin_footer', 'MLAModal::mla_echo_terms_search_form' );
			$add_the_form = false;
		}
	}

	/**
	 * Echo the hidden form for the "Search Terms" popup modal window
	 *
	 * @since 1.90
	 *
	 * @return	void	Echos the HTML <form> markup for hidden form
	 */
	public static function mla_echo_terms_search_form() {
		echo MLAModal::mla_terms_search_form(); // phpcs:ignore
	}

	/**
	 * Get dropdown box of terms to filter the Terms Search by, if available
	 *
	 * @since 3.08
	 *
	 * @param	integer	currently selected term_id || zero (default)
	 * @param	array	additional wp_dropdown_categories options; default empty
	 *
	 * @return	string	HTML markup for dropdown box
	 */
	public static function mla_get_terms_search_filter_dropdown( $selected = 0, $dropdown_options = array() ) {
		$dropdown = '';
		$tax_filter = MLACore::mla_get_option( MLACoreOptions::MLA_TERMS_SEARCH_FILTER_TAXONOMY );

		if ( ( 'none' !== $tax_filter ) && ( is_object_in_taxonomy( 'attachment', $tax_filter ) ) ) {
			$tax_object = get_taxonomy( $tax_filter );
			$dropdown_options = array_merge( array(
				'show_option_all' => __( 'All', 'media-library-assistant' ) . ' ' . $tax_object->labels->name,
				'show_option_none' => _x( 'No', 'show_option_none', 'media-library-assistant' ) . ' ' . $tax_object->labels->name,
				'orderby' => 'name',
				'order' => 'ASC',
				'show_count' => false,
				'hide_empty' => false,
				'child_of' => 0,
				'exclude' => '',
				// 'exclude_tree => '', 
				'echo' => true,
				'depth' => MLACore::mla_get_option( MLACoreOptions::MLA_TERMS_SEARCH_FILTER_DEPTH ),
				'tab_index' => 0,
				'name' => 'mla_terms_search[filter]',
				'id' => 'mla-terms-search-filter',
				'class' => 'postform',
				'selected' => $selected,
				'hierarchical' => true,
				'pad_counts' => false,
				'taxonomy' => $tax_filter,
				'hide_if_empty' => false,
				'update_term_meta_cache' => false,
			), $dropdown_options );

			ob_start();
			wp_dropdown_categories( $dropdown_options );
			$dropdown = ob_get_contents();
			ob_end_clean();
		}

		return $dropdown;
	}

	/**
	 * Build the hidden form for the "Search Terms" popup modal window
	 *
	 * @since 1.90
	 *
	 * @return	string	HTML <form> markup for hidden form
	 */
	public static function mla_terms_search_form() {
		$page_template_array = MLACore::mla_load_template( 'admin-terms-search-form.tpl' );
		if ( ! is_array( $page_template_array ) ) {
			/* translators: 1: ERROR tag 2: function name 3: non-array value */
			MLACore::mla_debug_add( sprintf( _x( '%1$s: %2$s non-array "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'MLA::_build_terms_search_form', var_export( $page_template_array, true ) ), MLACore::MLA_DEBUG_CATEGORY_ANY );
			return '';
		}

		$taxonomies = array();
		foreach( get_object_taxonomies( 'attachment', 'objects' ) as $taxonomy ) {
			if ( MLACore::mla_taxonomy_support( $taxonomy->name, 'support' ) ) {
				$taxonomies[] = $taxonomy;
			}
		}

		if( empty( $taxonomies ) ) {
			$page_values = array(
				'Search Terms' => __( 'Search Terms', 'media-library-assistant' ),
				'message' => __( 'There are no taxonomies to search', 'media-library-assistant' ),
			);
			$terms_search_tpl = MLAData::mla_parse_template( $page_template_array['mla-terms-search-empty-div'], $page_values );
		} else {
			$taxonomy_list = '';
			foreach ( $taxonomies as $taxonomy ) {
				$page_values = array(
					'taxonomy_checked' => MLACore::mla_taxonomy_support( $taxonomy->name, 'term-search' ) ? 'checked="checked"' : '',
					'taxonomy_slug' => $taxonomy->name,
					'taxonomy_label' => esc_attr( $taxonomy->label ),
				);
				$taxonomy_list .= MLAData::mla_parse_template( $page_template_array['mla-terms-search-taxonomy'], $page_values );
			}

			$filter_dropdown = self::mla_get_terms_search_filter_dropdown();
			$filter_style = ( !empty( $filter_dropdown ) ) ? 'style="display:block"' : 'style="display:none"';

			$page_values = array(
				'Search Terms' => __( 'Search Terms', 'media-library-assistant' ),
				'filter_dropdown' => $filter_dropdown,
				'filter_style' => $filter_style,
				'Search' => __( 'Search', 'media-library-assistant' ),
				'phrases_and_checked' => 'checked="checked"',
				'All phrases' => __( 'All phrases', 'media-library-assistant' ),
				'phrases_or_checked' => '',
				'Any phrase' => __( 'Any phrase', 'media-library-assistant' ),
				'terms_and_checked' => '',
				'All terms' => __( 'All terms', 'media-library-assistant' ),
				'terms_or_checked' => 'checked="checked"',
				'Any term' => __( 'Any term', 'media-library-assistant' ),
				'exact_checked' => '',
				'Exact' => __( 'Exact', 'media-library-assistant' ),
				'whole_word_checked' => '',
				'Whole Word' => __( 'Whole Word', 'media-library-assistant' ),
				'mla_terms_search_taxonomies' => $taxonomy_list,
			);
			$terms_search_tpl = MLAData::mla_parse_template( $page_template_array['mla-terms-search-div'], $page_values );
		}

		$page_values = array(
			'mla_terms_search_url' =>  esc_url( add_query_arg( array_merge( MLA_List_Table::mla_submenu_arguments( false ), array( 'page' => MLACore::ADMIN_PAGE_SLUG ) ), admin_url( 'upload.php' ) ) ),
			'mla_terms_search_action' => MLACore::MLA_ADMIN_TERMS_SEARCH,
			'wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'mla_terms_search_div' => $terms_search_tpl,
		);
		$terms_search_tpl = MLAData::mla_parse_template( $page_template_array['mla-terms-search-form'], $page_values );

		return $terms_search_tpl;
	}
} //Class MLAModal
?>