<?php
/**
 * Contain main functions to work with plugin, post, custom fields...
 *
 * @package   PT_Content_Views
 * @author    PT Guy <http://www.contentviewspro.com/>
 * @license   GPL-2.0+
 * @link      http://www.contentviewspro.com/
 * @copyright 2014 PT Guy
 */
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !function_exists( 'get_plugin_data' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}
if ( !class_exists( 'PT_CV_Functions' ) ) {

	/**
	 * @name PT_CV_Functions
	 * @todo Utility functions
	 */
	class PT_CV_Functions {

		static $prev_random_string = '';

		/**
		 * Compare current Wordpress version with a version
		 *
		 * @global string $wp_version
		 *
		 * @param string  $version_to_compare
		 * @param string  $operator
		 *
		 * @return boolean
		 */
		static function wp_version_compare( $version_to_compare, $operator = '>=' ) {
			if ( empty( $version_to_compare ) ) {
				return true;
			}

			global $wp_version;

			// Check if using Wordpress version 3.7 or higher
			return version_compare( $wp_version, $version_to_compare, $operator );
		}

		/**
		 * Get current language of site
		 */
		static function get_language() {
			$language = '';

			// WPML
			global $sitepress;
			if ( $sitepress && method_exists( $sitepress, 'get_current_language' ) ) {
				$language = $sitepress->get_current_language();
			}

			/**
			 * qTranslate-X (and qTranslate, mqTranslate)
			 * @since 1.5.3
			 */
			global $q_config;
			if ( $q_config && !empty( $q_config[ 'language' ] ) ) {
				$language = $q_config[ 'language' ];
			}

			// Polylang
			if ( empty( $language ) && function_exists( 'pll_current_language' ) ) {
				$language = pll_current_language();
			}

			return $language;
		}

		/**
		 * Switch language
		 *
		 * @global type $sitepress
		 * @param string $language Current language
		 */
		static function switch_language( $language ) {
			if ( !$language )
				return;

			// WPML
			global $sitepress;
			if ( $sitepress && $language ) {
				$sitepress->switch_lang( $language, true );
			}

			/**
			 * qTranslate-X (and qTranslate, mqTranslate)
			 * @since 1.5.3
			 */
			global $q_config;
			if ( $q_config ) {
				$q_config[ 'language' ] = $language;
			}

			// If 'lang' variable in POST/GET is set, Polylang automatically load it
		}

		/**
		 * Get plugin info
		 *
		 * @param string $file Absolute path to the plugin file
		 * @param string $data Field of plugin data want to get
		 *
		 * @return array | null
		 */
		static function plugin_info( $file, $data = '' ) {
			$plugin_data = get_plugin_data( $file );

			return isset( $plugin_data[ $data ] ) ? $plugin_data[ $data ] : NULL;
		}

		/**
		 * Add sub menu page
		 *
		 * @param string $parent_slug Slug of parent menu
		 * @param string $page_title  Title of page
		 * @param string $menu_title  Title of menu
		 * @param string $user_role   Required role to see this menu
		 * @param string $sub_page    Slug of sub menu
		 * @param string $class       Class name which contains function to output content of page created by this menu
		 */
		static function menu_add_sub( $parent_slug, $page_title, $menu_title, $user_role, $sub_page, $class ) {
			return add_submenu_page(
				$parent_slug, $page_title, $menu_title, $user_role, $parent_slug . '-' . $sub_page, array( $class, 'display_sub_page_' . $sub_page )
			);
		}

		/**
		 * Get current post type in Admin
		 *
		 * @global type $post
		 * @global type $typenow
		 * @global type $current_screen
		 * @return type
		 */
		static function admin_current_post_type() {
			global $post, $typenow, $current_screen;

			//we have a post so we can just get the post type from that
			if ( $post && $post->post_type ) {
				return $post->post_type;
			} //check the global $typenow - set in admin.php
			elseif ( $typenow ) {
				return $typenow;
			} //check the global $current_screen object - set in sceen.php
			elseif ( $current_screen && isset( $current_screen->post_type ) ) {
				return $current_screen->post_type;
			}
		}

		/**
		 * Include content of file
		 *
		 * @param string $file_path Absolute path of file
		 *
		 * @return NULL | string Content of file
		 */
		static function file_include_content( $file_path ) {
			$content = NULL;

			if ( file_exists( $file_path ) ) {
				ob_start();
				include_once $file_path;
				$content = ob_get_clean();
			}

			return $content;
		}

		/**
		 * Get value of option Content Views Settings page
		 * @param string $option_name
		 * @param mixed $default
		 * @return mixed
		 */
		static function get_option_value( $option_name, $default = '' ) {
			$options = get_option( PT_CV_OPTION_NAME );
			return isset( $options[ $option_name ] ) ? $options[ $option_name ] : $default;
		}

		/**
		 * Generate random string
		 *
		 * @param bool $prev_return Return previous generated string
		 *
		 * @return string
		 */
		static function string_random( $prev_return = false ) {
			if ( $prev_return ) {
				return PT_CV_Functions::$prev_random_string;
			}
			// Don't use uniqid(), it will cause bug when multiple elements have same ID
			$str	 = '0123456789abcdefghijklmnopqrstuvwxyz';
			$rand	 = substr( md5( mt_rand() ), 0, 7 ) . substr( str_shuffle( $str ), 0, 3 );

			PT_CV_Functions::$prev_random_string = $rand;

			return PT_CV_Functions::$prev_random_string;
		}

		/**
		 * Create array from string, use explode function
		 *
		 * @param string $string    String to explode
		 * @param string $delimiter Delimiter to explode string
		 *
		 * @return array
		 */
		static function string_to_array( $string, $delimiter = ',' ) {
			return is_array( $string ) ? $string : (array) explode( $delimiter, (string) str_replace( ' ', '', (string) $string ) );
		}

		/**
		 * Slug to nice String
		 *
		 * @param string $slug Slug string
		 *
		 * @return string
		 */
		static function string_slug_to_text( $slug ) {
			$slug = preg_replace( '/[_\-]+/', ' ', $slug );

			return ucwords( $slug );
		}

		/**
		 * Trims text to a certain number of words.
		 * @since 1.4.3
		 * @param string $text
		 * @param int $num_words
		 * @return string
		 */
		static function cv_trim_words( $text, $num_words = 500 ) {
			$text = apply_filters( PT_CV_PREFIX_ . 'before_generate_excerpt', $text );

			if ( apply_filters( PT_CV_PREFIX_ . 'excerpt_strip_shortcode', true ) ) {
				$text = self::cv_strip_shortcodes( $text );
			} else {
				$text = do_shortcode( $text );
			}

			// Prevent embedded URLs from showing
			global $wp_embed;
			if ( method_exists( $wp_embed, 'autoembed' ) ) {
				$text = $wp_embed->autoembed( $text );
			}

			$result = self::cv_strip_tags( $text );
			return self::trim_words( $result, $num_words );
		}

		/**
		 * Strip shortcodes in CV way, to replace WP strip_shortcodes()
		 * @after 1.8.3
		 * @param string $text
		 */
		static function cv_strip_shortcodes( $text, $strip_all = true ) {
			if ( apply_filters( PT_CV_PREFIX_ . 'excerpt_strip_all_shortcodes', false ) ) {
				return strip_shortcodes( $text );
			}

			global $shortcode_tags, $cv_shortcode_tags_backup;
			if ( !isset( $cv_shortcode_tags_backup ) ) {
				$tagnames	 = array_keys( $shortcode_tags );
				$tagregexp	 = join( '|', array_map( 'preg_quote', $tagnames ) );
			} else {
				$tagregexp = $cv_shortcode_tags_backup;
			}

			if ( $strip_all ) {
				// Strip some shortcodes
				$temp_shortcode_tags = $shortcode_tags;
				$shortcode_tags		 = apply_filters( PT_CV_PREFIX_ . 'shortcode_to_strip', array( 'caption' => '', 'embed' => '' ) );
				$text				 = strip_shortcodes( $text );
				$shortcode_tags		 = $temp_shortcode_tags;
			}

			// Keep other shortcodes' content
			return preg_replace( '/'
				. '\[' // Opening bracket
				. '(\/?)'
				. "($tagregexp)" // All shortcode tags
				. '[^\]]*' // Shortcode parameters
				. '\]' // Closing bracket
				. '/', '', $text );
		}

		/**
		 * Trim words in CV way
		 * @return string
		 */
		static function trim_words( $text, $num_words ) {
			$more	 = '';
			$text	 = apply_filters( PT_CV_PREFIX_ . 'before_trim_words', $text );
			$num_words	 = apply_filters( PT_CV_PREFIX_ . 'trim_words_number', $num_words );

			/*
			 * translators: If your word count is based on single characters (e.g. East Asian characters),
			 * enter 'characters_excluding_spaces' or 'characters_including_spaces'. Otherwise, enter 'words'.
			 * Do not translate into your own language.
			 */
			/**
			 * Do not use "[\r\n\t ]|\xC2\xA0|&nbsp;" as regex pattern, it can stop PHP process
			 * @since 1.9.5
			 */
			$text = str_replace( array( '\xC2\xA0', '&nbsp;' ), ' ', $text );
			if ( strpos( _x( 'words', 'Word count type. Do not translate!' ), 'characters' ) === 0 && preg_match( '/^utf\-?8$/i', get_option( 'blog_charset' ) ) ) {
				$text		 = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
				preg_match_all( '/./u', $text, $words_array );
				$words_array = array_slice( $words_array[ 0 ], 0, $num_words + 1 );
				$sep		 = '';
			} else {
				$words_array = preg_split( "/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY );
				$sep		 = ' ';
			}

			if ( count( $words_array ) > $num_words ) {
				array_pop( $words_array );
				$text	 = implode( $sep, $words_array );
				$text	 = $text . $more;
			} else {
				$text = implode( $sep, $words_array );
			}

			return apply_filters( PT_CV_PREFIX_ . 'after_trim_words', $text );
		}

		/**
		 * Custom strip tags, allow some tags
		 *
		 * @since 1.4.6
		 * @param string $string
		 * @return string
		 */
		static function cv_strip_tags( $string ) {
			// Changes double line-breaks in the text into HTML paragraphs (<p>, <br>)
			if ( apply_filters( PT_CV_PREFIX_ . 'wpautop', 0 ) ) {
				$string = wpautop( $string );
			}

			// Remove entire tag content
			$tags_to_rm	 = apply_filters( PT_CV_PREFIX_ . 'tag_to_remove', array( 'script', 'style' ) );
			$string		 = preg_replace( array( '@<(' . implode( '|', $tags_to_rm ) . ')[^>]*(?>.*?</\\1>|/?>)@si' ), '', $string );

			// Strip HTML tags
			if ( apply_filters( PT_CV_PREFIX_ . 'strip_tags', 1 ) ) {
				$allowed_tags = '';
				if ( PT_CV_Functions::setting_value( PT_CV_PREFIX . 'field-excerpt-allow_html' ) ) {
					$allowable_tags	 = (array) apply_filters( PT_CV_PREFIX_ . 'allowable_tags', array( '<a>', '<br>', '<strong>', '<em>', '<strike>', '<i>', '<ul>', '<ol>', '<li>' ) );
					$allowed_tags	 = implode( '', $allowable_tags );
				}

				$string = strip_tags( $string, $allowed_tags );
			}

			return trim( $string );
		}

		/**
		 * Handle slug of non-latin languages
		 * used in CVP < 4.1
		 *
		 * @param string $slug
		 * @param boolean $sanitize
		 * @return string
		 */
		static function term_slug_sanitize( $slug, $sanitize = false ) {
			if ( $sanitize && preg_match( '/%[0-9a-f][0-9a-f]/', $slug ) ) {
				$slug = str_replace( '%', '@', $slug );
			}

			return $slug;
		}

		/**
		 * Get thumbnail dimensions
		 *
		 * @param array $fargs The settings of thumbnail
		 *
		 * @return array
		 */
		static function field_thumbnail_dimensions( $fargs ) {
			$size = $fargs[ 'size' ];

			return (array) explode( '&times;', str_replace( ' ', '', $size ) );
		}

		/**
		 * Get value of a setting from global settings array
		 *
		 * @param string     $field        The full name of setting to get value
		 * @param array      $array_to_get Array to get values of wanted setting
		 * @param mixed|null $assign       The value to assign if setting is not found
		 */
		static function setting_value( $field, $array_to_get = NULL, $assign = NULL ) {
			if ( empty( $array_to_get ) ) {
				$array_to_get = PT_CV_Functions::get_global_variable( 'view_settings' );
			}

			return isset( $array_to_get[ $field ] ) ? $array_to_get[ $field ] : $assign;
		}

		/**
		 * Get values of settings from global settings array
		 *
		 * @param array  $fields        Array of setting fields to get value
		 * @param array  $array_to_save Array to save values of wanted setting fields
		 * @param array  $array_to_get  Array to get values of wanted setting fields
		 * @param string $prefix        Prefix string to looking for fields in $array_to_get
		 */
		static function settings_values( $fields, &$array_to_save, $array_to_get, $prefix ) {
			foreach ( $fields as $tsetting ) {
				$array_to_save[ $tsetting ] = PT_CV_Functions::setting_value( $prefix . $tsetting, $array_to_get );
			}
		}

		/**
		 * Get names of options for a setting group (setting name started by a prefix)
		 *
		 * @param string $prefix  The prefix in name of settings
		 * @param array  $options The options array (contain full paramaters of settings)
		 */
		static function settings_keys( $prefix, $options ) {
			$result = array();
			foreach ( $options as $option ) {
				if ( isset( $option[ 'params' ] ) ) {
					foreach ( $option[ 'params' ] as $params ) {
						// If name of setting match with prefix string, got it name
						if ( isset( $params[ 'name' ] ) && substr( $params[ 'name' ], 0, strlen( $prefix ) ) === $prefix ) {
							$result[] = substr( $params[ 'name' ], strlen( $prefix ) );
						}
					}
				}
			}

			return $result;
		}

		/**
		 * Get value of some setting options by prefix
		 *
		 * @param string $prefix  The prefix in name of setting options
		 * @param bool   $backend Get settings from Backend form
		 */
		static function settings_values_by_prefix( $prefix, $backend = FALSE ) {
			$view_settings = PT_CV_Functions::get_global_variable( 'view_settings' );

			if ( !$view_settings && $backend ) {
				global $pt_cv_admin_settings;
				$view_settings = $pt_cv_admin_settings;
			}

			$result	 = array();
			$strlen	 = strlen( $prefix );
			foreach ( (array) $view_settings as $name => $value ) {
				// If name of setting match with prefix string, got it
				if ( strpos( $name, $prefix ) === 0 ) {
					$result[ substr( $name, $strlen ) ] = $value;
				}
			}

			return $result;
		}

		/**
		 * Get terms list of a post
		 *
		 * @param object $post The post object
		 *
		 * @return string
		 */
		static function post_terms( $post, $taxo = null, $sepechar = ', ' ) {
			global $pt_cv_glb;

			$links				 = array();
			$taxonomy_terms		 = array();
			$post_terms			 = array();
			$taxonomies			 = $taxo ? (array) $taxo : get_taxonomies( array( 'public' => true ), 'names' );
			$taxonomies_to_show	 = $taxo ? $taxonomies : apply_filters( PT_CV_PREFIX_ . 'taxonomies_to_show', $taxonomies );
			$post_id			 = is_object( $post ) ? $post->ID : $post;
			$terms				 = wp_get_object_terms( $post_id, $taxonomies );

			foreach ( $terms as $term ) {
				$term_html		 = '';
				$term_slug		 = $term->slug;
				$include_this	 = apply_filters( PT_CV_PREFIX_ . 'terms_include_this', true, $term ) && in_array( $term->taxonomy, $taxonomies_to_show );
				if ( $include_this ) {
					$href		 = esc_url( get_term_link( $term, $term->taxonomy ) );
					$term_name	 = esc_attr( $term->name );
					$class		 = esc_attr( PT_CV_PREFIX . 'tax-' . $term_slug );
					$term_html	 = apply_filters( PT_CV_PREFIX_ . 'post_term_html', "<a href='$href' title='$term_name' class='$class'>{$term->name}</a>", $term );
					$links[]	 = $term_html;
				}

				$term_info							 = apply_filters( PT_CV_PREFIX_ . 'post_term', array( 'key' => $term_slug, 'value' => $term->name ), $term );
				$post_terms[ $term_info[ 'key' ] ]	 = $term_info[ 'value' ];

				// Add this term to terms list of an item
				if ( !isset( $taxonomy_terms[ $term->taxonomy ] ) ) {
					$taxonomy_terms[ $term->taxonomy ] = array();
				}
				$taxonomy_terms[ $term->taxonomy ][] = $term_html;
			}

			if ( $post_terms ) {
				if ( !isset( $pt_cv_glb[ 'item_terms' ] ) ) {
					$pt_cv_glb[ 'item_terms' ] = array();
				}

				$pt_cv_glb[ 'item_terms' ][ $post_id ] = $post_terms;
			}

			return apply_filters( PT_CV_PREFIX_ . 'post_terms_output', implode( $sepechar, $links ), $links, $taxonomy_terms );
		}

		/**
		 * Insert/Update post
		 *
		 * @param string $arr Array of post data
		 */
		static function post_insert( $arr ) {
			if ( !isset( $arr[ 'ID' ] ) ) {
				return;
			}
			// Create post object
			$my_post = array(
				'ID'			 => (int) $arr[ 'ID' ],
				'post_type'		 => PT_CV_POST_TYPE,
				'post_content'	 => '',
				'post_title'	 => !empty( $arr[ 'title' ] ) ? $arr[ 'title' ] : __( '(no title)', 'content-views-query-and-display-post-page' ),
				'post_status'	 => 'publish',
			);

			// Insert the post into the database
			return wp_insert_post( $my_post );
		}

		/**
		 * Get View id in post table, from "id" meta key value
		 *
		 * @param string $meta_id ID of custom field
		 *
		 * @return int Return Post ID of this view
		 */
		static function post_id_from_meta_id( $meta_id ) {

			$post_id = 0;
			if ( !$meta_id ) {
				return $post_id;
			}

			// Query view which has view id = $meta_id
			$pt_query = new WP_Query(
				array(
				'suppress_filters'	 => true,
				'post_type'			 => PT_CV_POST_TYPE,
				'post_status'		 => 'publish',
				'meta_key'			 => PT_CV_META_ID,
				'meta_value'		 => cv_esc_sql( $meta_id ),
				'cv_get_view'		 => true,
				)
			);

			if ( $pt_query->have_posts() ) :
				while ( $pt_query->have_posts() ):
					$pt_query->the_post();
					$post_id = get_the_ID();
				endwhile;
			endif;

			self::reset_query();

			return $post_id;
		}

		/**
		 * Get first key of array
		 *
		 * @param array $args Array data
		 *
		 * @return string
		 */
		static function array_get_first_key( $args ) {
			return current( array_keys( (array) $args ) );
		}

		/**
		 * Get view settings from view ID
		 *
		 * @param string $meta_id ID of custom field
		 *
		 * @return array
		 */
		static function view_get_settings( $meta_id, &$post_id = null ) {
			if ( !$meta_id ) {
				return;
			}

			do_action( PT_CV_PREFIX_ . 'get_view_settings' );

			$view_settings = apply_filters( PT_CV_PREFIX_ . 'set_view_settings', array() );

			if ( !$view_settings ) {
				$post_id = PT_CV_Functions::post_id_from_meta_id( $meta_id );
				if ( $post_id ) {
					$view_settings = get_post_meta( $post_id, PT_CV_META_SETTINGS, true );
				}
			}

			$GLOBALS[ 'cv_current_view' ] = $meta_id;
			return apply_filters( PT_CV_PREFIX_ . 'view_settings', $view_settings );
		}

		/**
		 * Process view settings, return View output
		 *
		 * @param string $view_id  View id
		 * @param array  $settings Settings array
		 * @param array  $pargs    Pagination settings
		 * @param array  $sc_params    Shortcode parameters
		 *
		 * @return string View output
		 */
		static function view_process_settings( $view_id, $settings, $pargs = array(), $sc_params = NULL ) {
			if ( !defined( 'PT_CV_DOING_PREVIEW' ) && empty( $settings[ PT_CV_PREFIX . 'view-type' ] ) ) {
				return sprintf( __( 'Error: View %s may not exist', 'content-views-query-and-display-post-page' ), "<strong>$view_id</strong>" );
			}

			do_action( PT_CV_PREFIX_ . 'view_process_start' );

			global $pt_cv_glb, $pt_cv_id;

			if ( !isset( $pt_cv_glb ) ) {
				$pt_cv_glb = array();
			}

			$pt_cv_id				 = $view_id;
			$pt_cv_glb[ $pt_cv_id ]	 = array();

			$view_settings								 = array_map( 'cv_esc_sql', $settings );
			$pt_cv_glb[ $pt_cv_id ][ 'view_settings' ]	 = $view_settings;

			$content_type	 = PT_CV_Functions::setting_value( PT_CV_PREFIX . 'content-type', $view_settings );
			$view_type		 = PT_CV_Functions::setting_value( PT_CV_PREFIX . 'view-type', $view_settings );
			$view_type		 = apply_filters( PT_CV_PREFIX_ . 'view_layout', $view_type, $view_id, $view_settings, $sc_params );
			$current_page	 = self::get_current_page( $pargs );

			$pt_cv_glb[ $pt_cv_id ][ 'content_type' ]	 = $content_type;
			$pt_cv_glb[ $pt_cv_id ][ 'view_type' ]		 = $view_type;
			$pt_cv_glb[ $pt_cv_id ][ 'current_page' ]	 = $current_page;

			# Important: store current View ID, prevent it from being modified when process posts which contains View shortcode
			$cv_live_id = $view_id;

			$rebuild = isset( $view_settings[ PT_CV_PREFIX . 'rebuild' ] ) ? $view_settings[ PT_CV_PREFIX . 'rebuild' ] : false;
			$dargs	 = PT_CV_Functions::view_display_settings( $view_type, $dargs );
			$args	 = $rebuild ? $rebuild : PT_CV_Functions::view_filter_settings( $content_type, $view_settings );

			if ( $sc_params ) {
				$view_settings[ PT_CV_PREFIX . 'shortcode_atts' ] = $sc_params;
			} elseif ( isset( $view_settings[ PT_CV_PREFIX . 'shortcode_atts' ] ) ) {
				$sc_params = $view_settings[ PT_CV_PREFIX . 'shortcode_atts' ];
			}
			$pt_cv_glb[ $pt_cv_id ][ 'shortcode_params' ] = $sc_params;

			PT_CV_Functions::view_get_pagination_settings( $dargs, $args, $pargs );

			$dargs								 = apply_filters( PT_CV_PREFIX_ . 'all_display_settings', $dargs );
			$args								 = apply_filters( PT_CV_PREFIX_ . 'query_parameters', $args );
			$pt_cv_glb[ $pt_cv_id ][ 'dargs' ]	 = $dargs;
			$pt_cv_glb[ $pt_cv_id ][ 'args' ]	 = $args;
			do_action( PT_CV_PREFIX_ . 'add_global_variables' );

			// Validate settings, if some required parameters are missing, show error and exit
			$error = !$rebuild ? apply_filters( PT_CV_PREFIX_ . 'validate_settings', array(), $args ) : false;
			if ( $error ) {
				return ( implode( '</p><p>', $error ) );
			}

			$content_items	 = $pt_query		 = $empty_result	 = null;

			// What kind of content to display
			$pt_cv_glb[ $pt_cv_id ][ 'display_what' ] = apply_filters( PT_CV_PREFIX_ . 'display_what', 'post' );
			if ( $pt_cv_glb[ $pt_cv_id ][ 'display_what' ] === 'post' ) {
				extract( self::get_posts_list( $args, $view_type ) );
			} else {
				$content_items = apply_filters( PT_CV_PREFIX_ . 'view_content', array() );
			}

			$pt_cv_id = $cv_live_id;

			if ( apply_filters( PT_CV_PREFIX_ . 'hide_empty_result', false ) && $empty_result ) {
				$html = '';
			} else {
				// Wrap items
				$html = PT_CV_Html::content_items_wrap( $content_items, $current_page, $args[ 'posts_per_page' ], $pt_cv_id );

				// Show pagination
				if ( $pt_query && apply_filters( PT_CV_PREFIX_ . 'show_pagination', PT_CV_Functions::nonajax_or_firstpage( $dargs, $current_page ) ) ) {
					// Total post founds
					$found_posts = (int) apply_filters( PT_CV_PREFIX_ . 'found_posts', $pt_query->found_posts );

					// Total number of items
					$total_items = ( $args[ 'limit' ] > 0 && $found_posts > $args[ 'limit' ] ) ? $args[ 'limit' ] : $found_posts;
					$total_items = apply_filters( PT_CV_PREFIX_ . 'total_posts', $total_items );

					// Total number of pages
					$items_per_page	 = (int) PT_CV_Functions::setting_value( PT_CV_PREFIX . 'pagination-items-per-page', $view_settings );
					$max_num_pages	 = ceil( $total_items / $items_per_page );
					$max_num_pages	 = (int) $max_num_pages;

					$pt_cv_glb[ $pt_cv_id ][ 'pagination_info' ] = compact( "total_items", "max_num_pages", "current_page" );

					// Output pagination
					if ( $max_num_pages > 1 ) {
						if ( $dargs[ 'pagination-settings' ][ 'type' ] === 'ajax' ) {
							cv_comp_pagination_settings( 'set', $view_settings );
						}

						$html .= "\n" . PT_CV_Html::pagination_output( $max_num_pages, $current_page, $pt_cv_id );
					} else {
						if ( $max_num_pages == 1 && defined( 'PT_CV_DOING_PREVIEW' ) ) {
							$reason = sprintf( '%s %s', ($total_items == $found_posts) ? __( 'the number of results', 'content-views-query-and-display-post-page' ) : __( 'the <b>Limit</b> value', 'content-views-query-and-display-post-page' ), __( 'is less than or equal to (&le;) the <b>Items per page</b> value', 'content-views-query-and-display-post-page' ) );
							$html .= "\n" . sprintf( '<p class="alert alert-warning" style="padding: 10px 5px">%s %s.</p>', __( '[Preview only] Pagination is disabled when there is only 1 page, because of', 'content-views-query-and-display-post-page' ), $reason );
						}
					}
				}
			}

			do_action( PT_CV_PREFIX_ . 'view_process_end' );

			return apply_filters( PT_CV_PREFIX_ . 'view_html', $html );
		}

		/**
		 * Query posts
		 *
		 * @global mixed $post
		 * @param array $args
		 * @param string $view_type
		 * @return array
		 */
		static function get_posts_list( $args, $view_type ) {
			$empty_result	 = false;
			$content_items	 = array();
			$args			 = apply_filters( PT_CV_PREFIX_ . 'query_params', $args );

			// The Query
			do_action( PT_CV_PREFIX_ . 'before_query' );
			$pt_query = new WP_Query( $args );
			do_action( PT_CV_PREFIX_ . 'after_query', $pt_query->request );

			//DEBUG_QUERY
			//print_r( $pt_query->request );
			// The Loop
			if ( $pt_query->have_posts() ) {
				do_action( PT_CV_PREFIX_ . 'before_process_item' );

				$all_posts	 = array();
				$post_idx	 = 0;
				while ( $pt_query->have_posts() ) {
					$pt_query->the_post();
					global $post;

					if ( apply_filters( PT_CV_PREFIX_ . 'show_this_post', $post ) ) {
						$content_items[ $post->ID ]	 = PT_CV_Html::view_type_output( $view_type, $post, $post_idx++ );
						$all_posts[ $post->ID ]		 = $post;
					}
				}
				$GLOBALS[ 'cv_posts' ] = $all_posts;

				do_action( PT_CV_PREFIX_ . 'after_process_item' );
			} else {
				$_class			 = apply_filters( PT_CV_PREFIX_ . 'content_no_post_found_class', PT_CV_PREFIX . 'no-post' );
				$_text			 = PT_CV_Functions::setting_value( PT_CV_PREFIX . 'noPostFound' ) ? trim( PT_CV_Functions::setting_value( PT_CV_PREFIX . 'noPostText' ) ) : PT_CV_Html::no_post_found();
				$content_items[] = sprintf( '<div class="%s">%s</div>', esc_attr( $_class ), wp_kses_post( $_text ) );
				$empty_result	 = true;
				PT_CV_Functions::set_global_variable( 'no_post_found', $empty_result );
			}

			self::reset_query();

			return array( 'content_items' => apply_filters( PT_CV_PREFIX_ . 'content_items', $content_items, $view_type ), 'pt_query' => $pt_query, 'empty_result' => $empty_result );
		}

		/**
		 * Get query parameters of View
		 *
		 * @param string $content_type     The current content type
		 * @param array  $view_settings The settings of View
		 *
		 * @return array
		 */
		static function view_filter_settings( $content_type, $view_settings ) {
			/**
			 * Get Query parameters
			 * Set default values
			 */
			$args = array(
				'post_type'		 => apply_filters( PT_CV_PREFIX_ . 'post_type', $content_type ),
				'post_status'	 => apply_filters( PT_CV_PREFIX_ . 'post_status', array( 'publish' ) ),
				'post__not_in'	 => array(),
			);

			// Ignore sticky posts
			$args[ 'ignore_sticky_posts' ] = apply_filters( PT_CV_PREFIX_ . 'ignore_sticky_posts', 1 );

			$post_in = PT_CV_Functions::setting_value( PT_CV_PREFIX . 'post__in', $view_settings );
			$post_in = array_filter( PT_CV_Functions::string_to_array( $post_in ) );
			if ( $post_in ) {
				$args[ 'post__in' ] = array_map( 'intval', $post_in );
			}

			$post_not_in = PT_CV_Functions::setting_value( PT_CV_PREFIX . 'post__not_in', $view_settings );
			$post_not_in = array_filter( PT_CV_Functions::string_to_array( $post_not_in ) );
			if ( $post_not_in ) {
				$args[ 'post__not_in' ] = array_map( 'intval', $post_not_in );
			}
			$args[ 'post__not_in' ] = apply_filters( PT_CV_PREFIX_ . 'post__not_in', $args[ 'post__not_in' ], $view_settings );

			// Parent page
			if ( in_array( $content_type, apply_filters( PT_CV_PREFIX_ . 'hierarchical_post_type', array( 'page' ) ) ) ) {
				$post_parent = apply_filters( PT_CV_PREFIX_ . 'post_parent_id', PT_CV_Functions::setting_value( PT_CV_PREFIX . 'post_parent', $view_settings ) );
				if ( !empty( $post_parent ) ) {
					$args[ 'post_parent' ] = (int) $post_parent;
				}
			}

			$args[ 'suppress_filters' ] = true;

			PT_CV_Functions::view_get_advanced_settings( $args, $content_type );

			return $args;
		}

		/**
		 * Get display parameters of View
		 *
		 * @param string $view_type The view type of View
		 *
		 * @return array
		 */
		static function view_display_settings( $view_type, &$dargs = null ) {
			$dargs = array();

			$dargs[ 'view-type' ] = $view_type;

			// Field settings of a item
			PT_CV_Functions::view_get_display_settings( $dargs );

			// Other settings
			PT_CV_Functions::view_get_other_settings( $dargs );

			// View type settings
			$dargs[ 'view-type-settings' ] = PT_CV_Functions::settings_values_by_prefix( PT_CV_PREFIX . $view_type . '-' );

			PT_CV_Functions::set_global_variable( 'dargs', $dargs );

			return $dargs;
		}

		/**
		 * Get Advance settings
		 *
		 * @param array  $args         The parameters array
		 * @param string $content_type The content type
		 */
		static function view_get_advanced_settings( &$args, $content_type ) {
			$view_settings		 = PT_CV_Functions::get_global_variable( 'view_settings' );
			$advanced_settings	 = (array) PT_CV_Functions::setting_value( PT_CV_PREFIX . 'advanced-settings', $view_settings );

			if ( $advanced_settings ) {
				foreach ( $advanced_settings as $setting ) {
					switch ( $setting ) {

						case 'author':
							$wp37			 = PT_CV_Functions::wp_version_compare( '3.7' );
							$author_in		 = array_filter( PT_CV_Functions::string_to_array( PT_CV_Functions::setting_value( PT_CV_PREFIX . 'author__in', $view_settings ) ) );
							$author_not_in	 = array_filter( PT_CV_Functions::string_to_array( PT_CV_Functions::setting_value( PT_CV_PREFIX . 'author__not_in', $view_settings ) ) );

							if ( $author_in ) {
								$args = array_merge(
									$args, $wp37 ? array( 'author__in' => array_map( 'intval', $author_in ) ) : array( 'author' => intval( $author_in[ 0 ] ) )
								);
							}

							if ( $author_not_in && $wp37 ) {
								$args = array_merge(
									$args, array( 'author__not_in' => array_map( 'intval', $author_not_in ) )
								);
							}

							break;

						case 'status':
							$status	 = PT_CV_Functions::string_to_array( PT_CV_Functions::setting_value( PT_CV_PREFIX . 'post_status', $view_settings, 'publish' ) );
							$args	 = array_merge(
								$args, array(
								'post_status' => apply_filters( PT_CV_PREFIX_ . 'post_status', $status ),
								)
							);
							break;

						case 'search':
							if ( $search_val = PT_CV_Functions::setting_value( PT_CV_PREFIX . 's', $view_settings ) ) {
								$args = array_merge(
									$args, array(
									's' => $search_val,
									)
								);
							}
							break;

						case 'taxonomy':
							$taxonomies = PT_CV_Functions::setting_value( PT_CV_PREFIX . 'taxonomy', $view_settings );
							if ( !$taxonomies ) {
								break;
							}

							$tax_settings = array();
							foreach ( $taxonomies as $taxonomy ) {
								$terms	 = (array) PT_CV_Functions::setting_value( PT_CV_PREFIX . $taxonomy . '-terms', $view_settings );
								$terms	 = apply_filters( PT_CV_PREFIX_ . 'selected_terms', $terms, $taxonomy );
								if ( $terms ) {
									$operator = PT_CV_Functions::setting_value( PT_CV_PREFIX . $taxonomy . '-operator', $view_settings, 'IN' );
									if ( empty( $operator ) || ($operator === 'AND' && count( $terms ) == 1) ) {
										$operator = 'IN';
									}

									$tax_settings[] = array(
										'taxonomy'			 => $taxonomy,
										'field'				 => 'slug',
										'terms'				 => $terms,
										'operator'			 => $operator,
										/**
										 * @since 1.7.2
										 * Bug: "No post found" when one of selected terms is hierarchical & operator is AND
										 */
										'include_children'	 => apply_filters( PT_CV_PREFIX_ . 'include_children', $operator == 'AND' ? false : true  )
									);
								}
							}

							if ( count( $tax_settings ) > 1 ) {
								$tax_settings[ 'relation' ] = PT_CV_Functions::setting_value( PT_CV_PREFIX . 'taxonomy-relation', $view_settings, 'AND' );
							}

							$args = array_merge( $args, array( 'tax_query' => apply_filters( PT_CV_PREFIX_ . 'taxonomy_setting', $tax_settings ) ) );
							break;

						case 'order':
							$orderby		 = PT_CV_Functions::setting_value( PT_CV_PREFIX . 'orderby', $view_settings );
							$order			 = PT_CV_Functions::setting_value( PT_CV_PREFIX . 'order', $view_settings );
							$order_settings	 = apply_filters( PT_CV_PREFIX_ . 'order_setting', array(
								'orderby'	 => $orderby,
								'order'		 => $orderby ? $order : '',
								) );
							$args			 = array_merge( $args, $order_settings );
							break;
					}
				}
			}
		}

		/**
		 * Get Fields settings
		 *
		 * @param array $dargs The settings array of Fields
		 */
		static function view_get_display_settings( &$dargs ) {
			$view_settings	 = PT_CV_Functions::get_global_variable( 'view_settings' );
			$view_type		 = $dargs[ 'view-type' ];

			$dargs[ 'layout-format' ]	 = PT_CV_Functions::setting_value( PT_CV_PREFIX . 'layout-format', $view_settings );
			$dargs[ 'number-columns' ]	 = apply_filters( PT_CV_PREFIX_ . 'item_per_row', PT_CV_Functions::setting_value( PT_CV_PREFIX . $view_type . '-' . 'number-columns', $view_settings, 1 ) );
			$dargs[ 'number-rows' ]		 = PT_CV_Functions::setting_value( PT_CV_PREFIX . $view_type . '-' . 'number-rows', $view_settings, 1 );

			$cfields_settings	 = PT_CV_Functions::settings_values_by_prefix( PT_CV_PREFIX . 'show-field-' );
			$cfields			 = (array) array_keys( (array) $cfields_settings );
			foreach ( $cfields as $field ) {
				if ( PT_CV_Functions::setting_value( PT_CV_PREFIX . 'show-field-' . $field, $view_settings ) ) {
					$dargs[ 'fields' ][] = $field;

					switch ( $field ) {
						case 'title':
							$prefix			 = PT_CV_PREFIX . 'field-title-';
							$field_setting	 = PT_CV_Functions::settings_values_by_prefix( $prefix );

							$dargs[ 'field-settings' ][ $field ] = apply_filters( PT_CV_PREFIX_ . 'field_title_setting_values', $field_setting, $prefix );

							break;

						case 'thumbnail':
							$prefix			 = PT_CV_PREFIX . 'field-thumbnail-';
							$field_setting	 = PT_CV_Functions::settings_values_by_prefix( $prefix );

							$dargs[ 'field-settings' ][ $field ] = apply_filters( PT_CV_PREFIX_ . 'field_thumbnail_setting_values', $field_setting, $prefix );

							break;

						case 'meta-fields':
							$prefix			 = PT_CV_PREFIX . 'meta-fields-';
							$field_setting	 = PT_CV_Functions::settings_values_by_prefix( $prefix );

							$dargs[ 'field-settings' ][ $field ] = apply_filters( PT_CV_PREFIX_ . 'field_meta_fields_setting_values', $field_setting, $prefix );

							break;

						case 'content':
							$prefix			 = PT_CV_PREFIX . 'field-content-';
							$field_setting	 = PT_CV_Functions::settings_values_by_prefix( $prefix );

							if ( $field_setting[ 'show' ] == 'excerpt' ) {
								$field_setting = array_merge( $field_setting, PT_CV_Functions::settings_values_by_prefix( PT_CV_PREFIX . 'field-excerpt-' ) );
							}

							$dargs[ 'field-settings' ][ $field ] = apply_filters( PT_CV_PREFIX_ . 'field_content_setting_values', $field_setting, $prefix );

							break;
					}
				}
			}
		}

		/**
		 * Get Pagination settings
		 *
		 * @param array $dargs The settings array of Fields
		 * @param array $args  The parameters array
		 * @param array $pargs The pagination settings array
		 */
		static function view_get_pagination_settings( &$dargs, &$args, $pargs ) {
			$view_settings				 = PT_CV_Functions::get_global_variable( 'view_settings' );
			$limit						 = trim( PT_CV_Functions::setting_value( PT_CV_PREFIX . 'limit', $view_settings ) );
			$limit						 = ( empty( $limit ) || $limit === '-1' ) ? 10000000 : $limit;
			$limit						 = (int) apply_filters( PT_CV_PREFIX_ . 'settings_args_limit', $limit );
			$args[ 'limit' ]			 = $args[ 'posts_per_page' ]	 = $limit;
			$offset						 = 0;

			$pagination = PT_CV_Functions::setting_value( PT_CV_PREFIX . 'enable-pagination', $view_settings );
			if ( $pagination || ContentViews_Block::is_pure_block() ) {
				$prefix							 = PT_CV_PREFIX . 'pagination-';
				$field_setting					 = PT_CV_Functions::settings_values_by_prefix( $prefix );
				$dargs[ 'pagination-settings' ]	 = apply_filters( PT_CV_PREFIX_ . 'pagination_settings', $field_setting, $prefix );
				if ( !isset( $dargs[ 'pagination-settings' ][ 'type' ] ) ) {
					$dargs[ 'pagination-settings' ][ 'type' ] = 'ajax';
				}

				if ( $dargs[ 'pagination-settings' ][ 'type' ] === 'normal' ) {
					$dargs[ 'pagination-settings' ][ 'style' ] = '';
				}

				$ppp	 = $og_ppp	 = isset( $dargs[ 'pagination-settings' ][ 'items-per-page' ] ) ? (int) $dargs[ 'pagination-settings' ][ 'items-per-page' ] : $limit;
				if ( $ppp > $limit ) {
					$ppp = $limit;
				}
				$args[ 'posts_per_page' ] = $ppp;

				$paged = (int) self::get_current_page( $pargs );

				// Prevent out of range pages show beyond posts
				if ( $og_ppp && $paged > ceil( $limit / $og_ppp ) ) {
					$paged = ceil( $limit / $og_ppp );
				}

				$offset	 = $ppp * ( $paged - 1 );
				if ( intval( $args[ 'posts_per_page' ] ) > $limit - $offset ) {
					$args[ 'posts_per_page' ] = $limit - $offset;
				}
			}

			$args[ 'offset' ]			 = apply_filters( PT_CV_PREFIX_ . 'settings_args_offset', $offset );
			$args[ 'by_contentviews' ]	 = true;
		}

		/**
		 * Get Other settings
		 *
		 * @param array $dargs The settings array of Fields
		 */
		static function view_get_other_settings( &$dargs ) {
			$prefix						 = PT_CV_PREFIX . 'other-';
			$field_setting				 = PT_CV_Functions::settings_values_by_prefix( $prefix );
			$dargs[ 'other-settings' ]	 = apply_filters( PT_CV_PREFIX_ . 'other_settings', $field_setting );
		}

		/**
		 * Process data when submit form add/edit view
		 *
		 * @return void
		 */
		static function view_submit() {
			if ( empty( $_POST ) ) {
				return;
			}

			if ( !isset( $_POST[ PT_CV_PREFIX_ . 'form_nonce' ] ) || !wp_verify_nonce( $_POST[ PT_CV_PREFIX_ . 'form_nonce' ], PT_CV_PREFIX_ . 'view_submit' ) ) {
				return;
			}

			// Insert View
			$title	 = cv_esc_sql( $_POST[ PT_CV_PREFIX . 'view-title' ] );
			$cur_pid = cv_esc_sql( $_POST[ PT_CV_PREFIX . 'post-id' ] );
			if ( !$cur_pid ) {
				$post_id = PT_CV_Functions::post_insert( array( 'ID' => 0, 'title' => $title ) );
			} else {
				$post_id = absint( $cur_pid );
			}

			// Add/Update View data
			$view_id = empty( $_POST[ PT_CV_PREFIX . 'view-id' ] ) ? PT_CV_Functions::string_random() : cv_sanitize_vid( $_POST[ PT_CV_PREFIX . 'view-id' ] );
			update_post_meta( $post_id, PT_CV_META_ID, $view_id );
			update_post_meta( $post_id, PT_CV_META_SETTINGS, apply_filters( PT_CV_PREFIX_ . 'pre_save_view_data', $_POST ) );

			// Update View title
			if ( strpos( $title, '[ID:' ) === false ) {
				PT_CV_Functions::post_insert( array( 'ID' => $post_id, 'title' => sprintf( '%s [ID: %s]', $title, $view_id ) ) );
			}

			$edit_link = PT_CV_Functions::view_link( $view_id );
			?>
			<script type="text/javascript">
				window.location = '<?php echo str_replace( '&#038;', '&', esc_url( $edit_link ) ); ?>';
			</script>
			<?php
			exit;
		}

		/**
		 * Add shortcode
		 *
		 * @param array  $atts    Array of setting parameters for shortcode
		 * @param string $content Content of shortcode
		 */
		static function view_output( $atts ) {
			$atts	 = shortcode_atts( apply_filters( PT_CV_PREFIX_ . 'shortcode_params', array( 'id' => 0 ) ), $atts );
			$id		 = cv_sanitize_vid( $atts[ 'id' ] );
			if ( $id && !self::duplicated_process( $id, $atts ) ) {
				# Backup the global post
				if ( isset( $GLOBALS[ 'post' ] ) ) {
					$GLOBALS[ 'cv_gpost_bak' ] = $GLOBALS[ 'post' ];
				}

				$result = apply_filters( PT_CV_PREFIX_ . 'view_shortcode_output', null, $atts );
				if ( empty( $result ) ) {
					$settings	 = PT_CV_Functions::view_get_settings( $id );
					$view_html	 = PT_CV_Functions::view_process_settings( $id, $settings, null, $atts );
					$result		 = PT_CV_Functions::view_final_output( $view_html );
					do_action( PT_CV_PREFIX_ . 'flushed_output', $result );
				}

				# Restore the global post
				if ( isset( $GLOBALS[ 'cv_gpost_bak' ] ) ) {
					$GLOBALS[ 'post' ] = $GLOBALS[ 'cv_gpost_bak' ];
					unset( $GLOBALS[ 'cv_gpost_bak' ] );
				}

				return $result;
			}
		}

		/**
		 * Final output of View: HTML & Assets
		 *
		 * @param string $html
		 */
		static function view_final_output( $html ) {
			ob_start();
			PT_CV_Html::assets_of_view_types();
			$view_assets = ob_get_clean();

			$class = apply_filters( PT_CV_PREFIX_ . 'wrapper_class', PT_CV_PREFIX . 'wrapper' );
			return sprintf( '<div class="%s">%s</div>', $class, $html ) . $view_assets;
		}

		/**
		 * Generate link to View page: Add view/ Edit view
		 *
		 * @param string $view_id The view id
		 * @param array  $action  Custom parameters
		 *
		 * @return string
		 */
		public static function view_link( $view_id, $action = array() ) {
			$edit_link = admin_url( 'admin.php?page=' . PT_CV_DOMAIN . '-add' );
			if ( !empty( $view_id ) ) {
				$query_args	 = apply_filters( PT_CV_PREFIX_ . 'view_link_args', array( 'id' => $view_id ) + $action );
				$edit_link	 = add_query_arg( $query_args, $edit_link );
			}

			return $edit_link;
		}

		/**
		 * Callback function for ajax Preview action 'preview_request'
		 */
		static function ajax_callback_preview_request() {
			// Validate request
			check_ajax_referer( PT_CV_PREFIX_ . 'ajax_nonce', 'ajax_nonce' );

			if ( !empty( $_POST[ 'data' ] ) ) {
				define( 'PT_CV_DOING_PREVIEW', true );
				do_action( PT_CV_PREFIX_ . 'preview_header' );

				$settings = array();
				parse_str( $_POST[ 'data' ], $settings );

				$view_id = cv_sanitize_vid( PT_CV_Functions::url_extract_param( 'id' ) );
				if ( empty( $view_id ) ) {
					$view_id = PT_CV_Functions::string_random();
				}

				$settings = apply_filters( PT_CV_PREFIX_ . 'preview_settings', $settings, $view_id );

				// Show output
				echo PT_CV_Functions::view_process_settings( $view_id, $settings );

				do_action( PT_CV_PREFIX_ . 'preview_footer' );
			}

			// Must exit
			die;
		}

		/**
		 * Callback function for ajax Pagination action 'pagination_request'
		 */
		static function ajax_callback_pagination_request() {
			// Validate request
			#check_ajax_referer( PT_CV_PREFIX_ . 'ajax_nonce', 'ajax_nonce' ); //disabled since 1.7.9 due to output -1 when use cache plugin, or nonce expired

			if ( !isset( $_POST[ 'sid' ] ) )
				return 'Empty View ID';

			define( 'PT_CV_DOING_PAGINATION', true );

			$view_id	 = cv_sanitize_vid( $_POST[ 'sid' ] );
			$settings	 = cv_comp_pagination_settings( 'get', NULL );
			if ( !$settings ) {
				$settings = PT_CV_Functions::view_get_settings( $view_id );
			}

			// Switch language
			$language = empty( $_POST[ 'lang' ] ) ? '' : cv_esc_sql( $_POST[ 'lang' ] );
			self::switch_language( $language );

			// Show output
			echo PT_CV_Functions::view_process_settings( $view_id, $settings, array( 'page' => absint( $_POST[ 'page' ] ) ) );

			// Must exit
			die;
		}

		/**
		 * Pagination output
		 *
		 * @param int $total_pages   Total pages
		 * @param int $current_page  Current page number
		 */
		static function pagination_links( $total_pages, $current_page = 1 ) {
			if ( $total_pages == 1 )
				return '';

			$links = PT_CV_Functions::get_pagination_links( $current_page, $total_pages, 'array' );

			// Generate the links
			$html = '';
			foreach ( $links as $link ) {
				$class	 = strpos( $link, 'current' ) !== false ? 'class="active"' : '';
				$link	 = preg_replace( '/<span[^>]*>/', '<a href="#">', $link );
				$link	 = str_replace( '</span>', '</a>', $link );
				$link	 = str_replace( array( 'page-numbers', 'prev', 'next' ), '', $link );
				$html .= "<li $class>" . $link . "</li>\n\t";
			}

			return $html;
		}

		/** Remove pagination parameters from URL
		 * @param string $link
		 * @return string
		 */
		static function remove_pagination_params( $link ) {
			$params = apply_filters( PT_CV_PREFIX_ . 'pagination_params_removed', array( 'vpage', 'page' ) );
			return remove_query_arg( $params, $link );
		}

		/** Get general page number from an URL
		 * @return int
		 */
		static function get_pagination_number() {
			$paged = null;

			// Get old params
			foreach ( array( 'vpage', '_page' ) as $op ) {
				if ( !empty( $_GET[ $op ] ) ) {
					$paged = absint( $_GET[ $op ] );
				}
			}

			// Get WP parameter
			if ( !$paged ) {
				if ( get_query_var( 'paged' ) ) {
					$paged = get_query_var( 'paged' );
				} elseif ( get_query_var( 'page' ) ) {
					$paged = get_query_var( 'page' );
				}
			}

			return $paged;
		}

		/** Get current page number of a View
		 *
		 * @param array $pargs Pagination settings of the View
		 * @return int
		 */
		static function get_current_page( $pargs ) {
			$paged = 1;

			if ( PT_CV_Functions::setting_value( PT_CV_PREFIX . 'enable-pagination' ) ) {
				if ( !empty( $pargs[ 'page' ] ) ) {
					$paged = absint( $pargs[ 'page' ] );
				}
				$pagenum = self::get_pagination_number();
				if ( !empty( $pagenum ) ) {
					$paged = absint( $pagenum );
				}
			}

			return apply_filters( PT_CV_PREFIX_ . 'set_current_page', $paged );
		}

		/**
		 * Check if using pagination: Non-ajax or First page (of Ajax pagination)
		 *
		 * @param array $dargs
		 * @param int $current_page
		 * @return bool
		 */
		static function nonajax_or_firstpage( $dargs, $current_page ) {
			if ( !PT_CV_Functions::setting_value( PT_CV_PREFIX . 'enable-pagination' ) ) {
				return false;
			}

			if ( $dargs[ 'pagination-settings' ][ 'type' ] === 'normal' ) {
				return true;
			} else if ( !(defined( 'DOING_AJAX' ) && DOING_AJAX && !empty( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'pagination_request') ) {
                // Not requested by Ajax => show pagination
                return true;
			} else {
				return false;
			}
		}

		/** Get list of pagination links
		 * @since 2.3.0
		 *
		 * @param int $current_page
		 * @param int $total_pages
		 * @param string $type
		 * @return string/array
		 */
		static function get_pagination_links( $current_page = 1, $total_pages = 2, $type = 'array' ) {
			// Custom filters
			$pages_to_show	 = apply_filters( PT_CV_PREFIX_ . 'pages_to_show', 4 );
			$labels			 = apply_filters( PT_CV_PREFIX_ . 'pagination_label', array(
				'prev'	 => '&lsaquo;',
				'next'	 => '&rsaquo;',
			) );

			// Call WP functions
			global $wp_filter;
			$filters_bak = $wp_filter;

			remove_all_filters( 'paginate_links' );
			if ( !cv_is_active_plugin( 'polylang' ) ) {
				remove_all_filters( 'get_pagenum_link' );
			}
			add_filter( 'paginate_links', array( __CLASS__, 'remove_pagination_params' ) );

			$params = array(
				'format'	 => apply_filters( PT_CV_PREFIX_ . 'pagination_link_format', '?_page=%#%' ),
				'current'	 => $current_page,
				'total'		 => $total_pages,
				'type'		 => $type,
				'prev_text'	 => $labels[ 'prev' ],
				'next_text'	 => $labels[ 'next' ],
				'mid_size'	 => $pages_to_show ? absint( $pages_to_show ) / 2 : 2,
			);

			$links = paginate_links( $params );

			$wp_filter = $filters_bak;

			return $links;
		}

		/** Get the link for a page number
		 * @param int $page_num The page number
		 * @since 2.3.0
		 */
		static function get_pagination_url( $page_num = 0 ) {
			$links = self::get_pagination_links( 2, 3, 'list' );

			$matches = array();
			preg_match_all( '/href=["\']([^"\']+)["\']/', $links, $matches );

			if ( !isset( $matches[ 1 ] ) ) {
				return null;
			} else {
				$pattern = '@([/?&](page|paged|_page)[/=])([0-9]+)@i';
				$page1	 = html_entity_decode( $matches[ 1 ][ 0 ] );
				$pagen	 = html_entity_decode( $matches[ 1 ][ 3 ] );

				if ( !empty( $page_num ) ) {
					return esc_url_raw( preg_replace( $pattern, '${1}' . absint( $page_num ), $pagen ) );
				} else {
					return array(
						'page_1' => esc_url_raw( $page1 ),
						'page_n' => esc_url_raw( preg_replace( $pattern, '${1}' . '_CVNUMBER_', $pagen ) ),
					);
				}
			}
		}

		/**
		 * Extract param's value from URL
		 *
		 * @param string $pname Name of parameter
		 * @return string
		 */
		static function url_extract_param( $pname, $default = null ) {
			$query	 = array();
			$url	 = $_SERVER[ 'REQUEST_URI' ];
			if ( strpos( $url, 'admin-ajax.php' ) !== false ) {
				$url = $_SERVER[ 'HTTP_REFERER' ];
			}

			$parts = parse_url( $url );
			if ( isset( $parts[ 'query' ] ) ) {
				parse_str( $parts[ 'query' ], $query );

				return !empty( $query[ $pname ] ) ? $query[ $pname ] : $default;
			}

			return $default;
		}

		/**
		 * Set global variable
		 *
		 * @global array $pt_cv_glb
		 * @global string $pt_cv_id
		 * @param type $variable
		 * @param type $value
		 */
		static function set_global_variable( $variable, $value ) {
			global $pt_cv_glb, $pt_cv_id;
			$pt_cv_glb[ $pt_cv_id ][ $variable ] = $value;
		}

		/**
		 * Get global variable
		 *
		 * @global array $pt_cv_glb
		 * @global string $pt_cv_id
		 * @param string $variable
		 * @return mixed
		 */
		static function get_global_variable( $variable, $unset = false ) {
			global $pt_cv_glb, $pt_cv_id;
			if ( !$pt_cv_glb || !$pt_cv_id )
				return null;

			$value = isset( $pt_cv_glb[ $pt_cv_id ][ $variable ] ) ? $pt_cv_glb[ $pt_cv_id ][ $variable ] : null;

			// Unset after get
			if ( $unset && $value ) {
				unset( $pt_cv_glb[ $pt_cv_id ][ $variable ] );
			}

			return $value;
		}

		/**
		 * Output debug message (if debug is enable) / nice message (otherwise)
		 * @param string $log		Raw log for debugging
		 * @param string $message	Nice message for user
		 */
		static function debug_output( $log, $message = '' ) {
			return defined( 'PT_CV_DEBUG' ) ? ( PT_CV_DEBUG ? $log : $message ) : $message;
		}

		// Reset WP query
		static function reset_query() {
			if ( apply_filters( PT_CV_PREFIX_ . 'reset_query', true ) ) {
				wp_reset_postdata();
			}
		}

		/**
		 * Check duplicated View
		 * @return bool
		 */
		static function duplicated_process( $view_id, $sc_params ) {
			$duplicated = false;

			if ( apply_filters( PT_CV_PREFIX_ . 'check_duplicate', 0, $view_id, $sc_params ) ) {
				global $pt_cv_views;

				$vid = $view_id . '-' . md5( serialize( $sc_params ) );
				if ( !empty( $pt_cv_views[ $vid ] ) ) {
					$duplicated = true;
				} else {
					$pt_cv_views[ $vid ] = 1;
				}
			}

			return $duplicated;
		}

		/**
		 * Disable View shortcode in posts of current View
		 * @since 1.7.6
		 *
		 * @global array $shortcode_tags
		 * @global array $shortcode_tags_backup
		 * @param string $action
		 */
		static function disable_view_shortcode( $action = 'disable' ) {
			if ( apply_filters( PT_CV_PREFIX_ . 'disable_child_shortcode', true ) ) {
				global $shortcode_tags, $shortcode_tags_backup;

				if ( $action == 'disable' ) {
					$shortcode_tags_backup		 = $shortcode_tags;
					$shortcode_tags[ 'pt_view' ] = '__return_false';
				} else {
					$shortcode_tags = $shortcode_tags_backup;
				}
			}
		}

		static function is_view( $arr = null ) {
			// is 0 (not saved view), string (saved view), null (block)
			$view_id = PT_CV_Functions::setting_value( PT_CV_PREFIX . 'view-id', $arr );
			return ($view_id === null) ? false : true;
		}

		/* Get taxonomies of selected post type
		 * @since 4.0
		 */
		static function get_taxonomies_by_post_type( $data ) {
			$post_types = $data[ 'postType' ];
			if ( $post_types === 'any' ) {
				$post_types = $data[ 'multipostType' ];
			}

			// Ensure to get all post types, for both Elementor widget & Block
			// Elementor: cvElementor available in both widget/preview/frontend
			// Block: cvBlock available in editor only
			$arr			 = PT_CV_Values::post_types_vs_taxonomies( true );
			$matched_taxo	 = [];
			foreach ( (array) $post_types as $post_type ) {
				if ( is_array( $arr[ $post_type ] ) ) {
					$matched_taxo = array_merge( $matched_taxo, $arr[ $post_type ] );
				}
			}

			return $matched_taxo;
		}

		static function has_pro() {
			return class_exists( 'PT_Content_Views_Pro' ) || get_option( 'pt_cv_version_pro' );
		}

	}

}