<?php
/**
 * Provides examples of the filters provided for Field-level Substitution Parameters
 *
 * In this example:
 *     - a "page:" prefix accesses the featured image ID of the parent page/post, i.e. [+page:featured+]
 *     - an "conditional:" prefix returns a value when a condition is true, e.g., during the upload process
 *     - a "wp_query_vars:" prefix accesses all of the "global $wp_query->query_vars" properties
 *     - a "current_term:" prefix accesses the term named in a $_REQUEST variable
 *         e.g. {+current_term:taxonomy.default_value(term_field)+}
 *     - a "ucwords" custom format value uppercases the first character of each word in a string
 *     - a "computed_orientation" custom data source derives "Landscape" or "Portrait" from image height and width values
 *
 * Created for support topic "Parent category tag"
 * opened on 5/20/2016 by "Levy":
 * https://wordpress.org/support/topic/parent-category-tag
 *
 * Enhanced for support topic "Automatically adding the author as a category"
 * opened on 6/27/2016 by "badger41":
 * https://wordpress.org/support/topic/automatically-adding-the-author-as-a-category
 *
 * Enhanced for support topic "Apply Category to JPG images only on Upload"
 * opened on 7/11/2016 by "dg_Amanda":
 * https://wordpress.org/support/topic/apply-category-to-jpg-images-only-on-upload
 *
 * Enhanced for support topic "How to add a number to the title of images inserted in same post?"
 * opened on 7/19/2016 by "Levy":
 * https://wordpress.org/support/topic/how-to-add-a-number-to-the-title-of-images-inserted-in-same-post
 *
 * Enhanced for support topic "What are the default values for the markup template?"
 * opened on 9/21/2016 by "cconstantine":
 * https://wordpress.org/support/topic/what-are-the-default-values-for-the-markup-template/
 *
 * Enhanced for support topic "Maping Image ALT Tags to Product Meta Title"
 * opened on 12/6/2016 by "webpresencech":
 * https://wordpress.org/support/topic/maping-image-alt-tags-to-product-meta-title/
 *
 * Enhanced for support topic "$wp_query->query_vars in query"
 * opened on 3/1/2017 by "mbruxelle":
 * https://wordpress.org/support/topic/wp_query-query_vars-in-query/
 *
 * Enhanced for support topic "Sorting items in Tag cloud by parent/child?"
 * opened on 5/9/2018 by "antonstepichev":
 * https://wordpress.org/support/topic/sorting-items-in-tag-cloud-by-parent-child/
 *
 * Enhanced for support topic "Auto alt text from field in exif or iptc"
 * opened on 8/1/2020 by "perchera":
 * https://wordpress.org/support/topic/auto-alt-text-from-field-in-exif-or-iptc/
 *
 * Enhanced for support topic "Perform Comparison (If Then Else Logic) when Mapping Metadata to Custom Field"
 * opened on 2/27/2022  by "tplunkett87":
 * https://wordpress.org/support/topic/perform-calculation-when-mapping-metadata-to-custom-field/
 *
 * Enhanced for support topic "how to extract from the parameters field"
 * opened on 3/25/2023 by "reassure".
 * https://wordpress.org/support/topic/how-to-extract-from-the-parameters/
 *
 * @package MLA Substitution Parameter Hooks Example
 * @version 1.16
 */

/*
Plugin Name: MLA Substitution Parameter Hooks Example
Plugin URI: http://davidlingren.com/
Description: Adds "parent_terms:", "page_terms:", "parent:", "author:", "conditional:", "wp_query_vars" and "current_term" Field-level Substitution Parameters and "ucwords" custom format value
Author: David Lingren
Version: 1.16
Author URI: http://davidlingren.com/

Copyright 2016-2023 David Lingren

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You can get a copy of the GNU General Public License by writing to the
	Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
*/

/**
 * Class MLA Substitution Parameter Hooks Example hooks four of the filters provided
 * by the "Field-level substitution parameter filters (Hooks)"
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding
 * everything else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Substitution Parameter Hooks Example
 * @since 1.00
 */
class MLASubstitutionParameterExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * Installs filters and actions that handle the MLA hooks for the
	 * "Field-level substitution parameters"
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize() {
		// Defined in /media-library-assistant/includes/class-mla-data.php
		add_filter( 'mla_expand_custom_data_source', 'MLASubstitutionParameterExample::mla_expand_custom_data_source', 10, 9 );
		add_filter( 'mla_expand_custom_prefix', 'MLASubstitutionParameterExample::mla_expand_custom_prefix', 10, 8 );
		add_filter( 'mla_apply_custom_format', 'MLASubstitutionParameterExample::mla_apply_custom_format', 10, 2 );

		add_filter( 'mla_fetch_attachment_image_metadata_raw', 'MLASubstitutionParameterExample::mla_fetch_attachment_image_metadata_raw', 10, 3 );
		add_filter( 'mla_fetch_attachment_image_metadata_final', 'MLASubstitutionParameterExample::mla_fetch_attachment_image_metadata_final', 10, 3 );

		// Defined in /media-library-assistant/includes/class-mla-data-source.php
		add_filter( 'mla_evaluate_custom_data_source', 'MLASubstitutionParameterExample::mla_evaluate_custom_data_source', 10, 5 );


		/*
		 * Additional hooks defined in "MLA Custom Field and IPTC/EXIF Mapping Actions and Filters (Hooks)".
		 * These are only required for the "conditional:is_upload" prefix processing.
		 */
		add_filter( 'mla_update_attachment_metadata_prefilter', 'MLASubstitutionParameterExample::mla_update_attachment_metadata_prefilter', 10, 3 );
		add_filter( 'mla_update_attachment_metadata_postfilter', 'MLASubstitutionParameterExample::mla_update_attachment_metadata_postfilter', 10, 3 );
	} // initialize

	/**
	 * MLA Update Attachment Metadata Prefilter
	 *
	 * Used in this example to set the "is_upload" status before mapping rules are run.
	 *
	 * @since 1.01
	 *
	 * @param	array	attachment metadata
	 * @param	integer	The Post ID of the new/updated attachment
	 * @param	array	Processing options, e.g., 'is_upload'
	 */
	public static function mla_update_attachment_metadata_prefilter( $data, $post_id, $options ) {
		self::$is_upload = $options['is_upload'];

		return $data;
	} // mla_update_attachment_metadata_prefilter

	/**
	 * Share the upload status among mla_update_attachment_metadata_prefilter, mla_expand_custom_prefix
	 * and mla_update_attachment_metadata_postfilter
	 *
	 * @since 1.01
	 *
	 * @var	boolean	Upload status
	 */
	private static $is_upload = false;

	/**
	 * MLA Update Attachment Metadata Postfilter
	 *
	 * Used in this example to clear the "is_upload" status after mapping rules are run.
	 *
	 * @since 1.01
	 *
	 * @param	array	attachment metadata
	 * @param	integer	The Post ID of the new/updated attachment
	 * @param	array	Processing options, e.g., 'is_upload'
	 */
	public static function mla_update_attachment_metadata_postfilter( $data, $post_id, $options ) {
		self::$is_upload = false;

		return $data;
	} // mla_update_attachment_metadata_postfilter

	/**
	 * MLA Expand Custom Data Source Filter
	 *
	 * For shortcode and Content Template processing, gives you an opportunity to generate a custom data value.
	 *
	 * @since 1.00
	 *
	 * @param	string	NULL, indicating that by default, no custom value is available
	 * @param	string	the entire data-source text including option/format and any arguments 
	 * @param	string	the data-source name 
	 * @param	array	data-source components; prefix (empty), value, option, format and args (if present)
	 * @param	array	values from the query, if any, e.g. shortcode parameters
	 * @param	array	item-level markup template values, if any
	 * @param	integer	attachment ID for attachment-specific values
	 * @param	boolean	for option 'multi', retain existing values
	 * @param	string	default option value
	 */
	public static function mla_expand_custom_data_source( $custom_value, $key, $candidate, $value, $query, $markup_values, $post_id, $keep_existing, $default_option ) {
		// Uncomment the error_log statements in any of the filters to see what's passed in
		//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_data_source( {$key}, {$candidate}, {$post_id}, {$keep_existing}, {$default_option} ) value = " . var_export( $value, true ), 0 );
		//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_data_source( {$candidate}, {$post_id} ) query = " . var_export( $query, true ), 0 );
		//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_data_source( {$candidate}, {$post_id} ) markup_values = " . var_export( $markup_values, true ), 0 );

		if ( 'computed_orientation' === $candidate ) {
			if ( isset( $markup_values['width'] ) && isset( $markup_values['height'] ) && 0 < $markup_values['height'] ) {
				return ( ( $markup_values['width'] / $markup_values['height'] ) > 1.0 ) ? 'Landscape' : 'Portrait';
			}
		}
		
		return $custom_value;
	} // mla_expand_custom_data_source

	/**
	 * MLA Expand Custom Prefix Filter
	 *
	 * Gives you an opportunity to generate your custom data value when a parameter's prefix value is not recognized.
	 *
	 * @since 1.00
	 *
	 * @param	string	NULL, indicating that by default, no custom value is available
	 * @param	string	the data-source name, as entered, including prefix
	 * @param	array	data-source components; prefix, value, qualifier (if present), option, format and args (if present)
	 * @param	array	values from the query, if any, e.g. shortcode parameters
	 * @param	array	item-level markup template values, if any
	 * @param	integer	attachment ID for attachment-specific values
	 * @param	boolean	for option 'multi', retain existing values
	 * @param	string	default option value
	 */
	public static function mla_expand_custom_prefix( $custom_value, $key, $value, $query, $markup_values, $post_id, $keep_existing, $default_option ) {
		static $author_cache = array();

		//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$key}, {$post_id}, {$keep_existing}, {$default_option} ) value = " . var_export( $value, true ), 0 );
		//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$key}, {$post_id} ) query = " . var_export( $query, true ), 0 );
		//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$key}, {$post_id} ) markup_values = " . var_export( $markup_values, true ), 0 );

		// Look for field/value qualifier
		$match_count = preg_match( '/^(.+)\((.+)\)/', $value['value'], $matches );
		if ( $match_count ) {
			$field = $matches[1];
			$qualifier = $matches[2];
		} else {
			$field = $value['value'];
			$qualifier = '';
		}

		// Set debug mode
		$debug_active = isset( $query['mla_debug'] ) && ( 'false' !== trim( strtolower( $query['mla_debug'] ) ) );
		if ( $debug_active ) {
			$old_mode = MLACore::mla_debug_mode( 'log' );
			MLACore::mla_debug_add( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$key}, {$post_id}, {$keep_existing}, {$default_option} ) \$_REQUEST = " . var_export( $_REQUEST, true ) );
			MLACore::mla_debug_add( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$field}, {$qualifier} ) \$value = " . var_export( $value, true ) );
			MLACore::mla_debug_add( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix() \$query = " . var_export( $query, true ) );
			MLACore::mla_debug_add( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix() \$markup_values = " . var_export( $markup_values, true ) );
		}

		if ( 'page' === $value['prefix'] ) {
			if ( 'featured' === $value['value'] ) {
				$featured = absint( get_post_thumbnail_id( absint( $markup_values['page_ID'] ) ) ); 
				if ( 0 < $featured ) {
					$custom_value = (string) $featured;
				}
			}
		} elseif ( 'current_term' === $value['prefix'] ) {
			// Look for compound names, e.g., taxonomy.default_value
			$key_array = explode( '.', $field );
			if ( 1 < count( $key_array ) ) {
				$field = $key_array[0];
				$custom_value = $key_array[1];
			} else {
				$custom_value = '';
			}

			// Look in $_REQUEST for simple taxonomy query, then tax_input query
			if ( isset( $_REQUEST[ $field ] ) ) {
				$current_terms = explode( ',', trim( $_REQUEST[ $field ] ) );
			} elseif ( isset( $_REQUEST['tax_input'] ) && isset( $_REQUEST['tax_input'][ $field ] )) {
				$current_terms = $_REQUEST['tax_input'][ $field ];
			} else {
				return $custom_value;
			}

			if ( empty( $qualifier ) ) {
				$qualifier = 'name';
			}

			if ( $debug_active ) {
				MLACore::mla_debug_add( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$field}, {$qualifier} ) \$current_terms = " . var_export( $current_terms, true ) );
			}
			
			$results = '';
			foreach( $current_terms as $current_term ) {
				// Find the current term
				if ( ctype_digit( $current_term ) ) {
					$current_term = get_term_by( 'id', absint( $current_term ), $field, 'ARRAY_A' );
				} else {
					$current_term = get_term_by( 'slug', sanitize_title_for_query( $current_term ), $field, 'ARRAY_A' );
				}

				// If the terms does not exist, skip it			
				if ( false === $current_term ) {
					continue;
				}

				// Extract the desired term field
				$new_value = isset( $current_term[ $qualifier ] ) ? $current_term[ $qualifier ] : $current_term['name'];
				$new_value =  sanitize_term_field( $qualifier, $new_value, absint( $current_term['term_id'] ), $field, 'display' );
				$results .= strlen( $results ) ? ',' . $new_value : $new_value;
			} // foreach term

			if ( $debug_active ) {
				MLACore::mla_debug_add( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$field}, {$qualifier} ) \$results = " . var_export( $results, true ) );
			}
			
			if ( strlen( $results ) ) {
				$custom_value =  $results;
			}
		}

		if ( $debug_active ) {
			MLACore::mla_debug_mode( $old_mode );
		}
		
		if ( 0 === absint( $post_id ) ) {
			return $custom_value;
		}

		if ( 'conditional' === $value['prefix'] ) {
			if ( empty( $value['args'] ) ) {
				return $custom_value;
			}

			$true_value = ( isset( $value['args'][0] ) && !empty( $value['args'][0] ) ) ? $value['args'][0] : '';
			$false_value = ( isset( $value['args'][1] ) && !empty( $value['args'][1] ) ) ? $value['args'][1] : '';
			$qualifier = ( isset( $value['args'][2] ) && !empty( $value['args'][2] ) ) ? $value['args'][2] : '';

			switch ( $value['value'] ) {
				case 'is_upload':
					if ( self::$is_upload ) {
						// Optional MIME type qualifier
						if ( !empty( $qualifier ) ) {
							$item = get_post( $post_id );
							$post_mime_type = explode( '/', sanitize_mime_type( $item->post_mime_type ) );
//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$key}, {$post_id} ) post_mime_type = " . var_export( $post_mime_type, true ), 0 );
							$qualifier = explode( '/', sanitize_mime_type( $qualifier ) );
//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$key}, {$post_id} ) qualifier = " . var_export( $qualifier, true ), 0 );

							if ( isset( $qualifier[1] ) && isset( $post_mime_type[1] ) && ( $qualifier[1]!== $post_mime_type[1] ) ) {
								$custom_value = $false_value;
								break;
							}

							if ( isset( $qualifier[0] ) && isset( $post_mime_type[0] ) && ( $qualifier[0]!== $post_mime_type[0] ) ) {
								$custom_value = $false_value;
								break;
							}
						}

						$custom_value = $true_value;
					} else {
						$custom_value = $false_value;
					}
//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$key}, {$post_id} ) value = '{$custom_value}', is_upload = " . var_export( self::$is_upload, true ), 0 );
					break;
				default:
					// ignore anything else
			}
		} elseif ( 'wp_query_vars' === $value['prefix'] ) {
			global $wp_query;
			//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$key}, {$post_id} ) wp_query->query_vars = " . var_export( $wp_query->query_vars , true ), 0 );

			if ( !empty( $wp_query->query_vars ) ) {
				$custom_value = MLAData::mla_find_array_element( $value['value'], $wp_query->query_vars, $value['option'], $keep_existing );
			}
		}

		return $custom_value;
	} // mla_expand_custom_prefix

	/**
	 * MLA Apply Custom Format Filter
	 *
	 * Gives you an opportunity to apply your custom option/format to the data value.
	 *
	 * @since 1.00
	 *
	 * @param	string	the data-source value
	 * @param	array	data-source components; prefix (empty), value, option, format and args (if present)
	 */
	public static function mla_apply_custom_format( $value, $args ) {
		//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_apply_custom_format( {$value} ) args = " . var_export( $args, true ), 0 );

		if ( 'ucwords' === $args['format'] ) {
			if ( isset( $args['args'] ) ) {
				if ( is_array( $args['args'] ) ) {
					$delimiters = stripslashes( $args['args'][0] );
				} else {
					$delimiters = stripslashes( $args['args'] );
				}
			} else {
				$delimiters = " \t\r\n\f\v";
			}
			
			$value = ucwords( $value, $delimiters );
		}
		
		//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_apply_custom_format( {$value} ) delimiters = " . var_export( $delimiters, true ), 0 );
		return $value;
	} // mla_apply_custom_format

	/**
	 * MLA Evaluate Custom Data Source Filter
	 *
	 * For metadata mapping rules, gives you an opportunity to generate a custom data value.
	 *
	 * @since 1.00
	 *
	 * @param	string	NULL, indicating that by default, no custom value is available
	 * @param	integer	attachment ID for attachment-specific values
	 * @param	string 	category/scope to evaluate against: custom_field_mapping or single_attachment_mapping
	 * @param	array	data source specification ( name, *data_source, *keep_existing, *format, mla_column, quick_edit, bulk_edit, *meta_name, *option, no_null )
	 * @param	array 	_wp_attachment_metadata, default NULL (use current postmeta database value)
	 */
	public static function mla_evaluate_custom_data_source( $custom_value, $post_id, $category, $data_value, $attachment_metadata ) {
		//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_evaluate_custom_data_source( {$post_id}, {$category} ) data_value = " . var_export( $data_value, true ), 0 );
		//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_evaluate_custom_data_source( {$post_id} ) attachment_metadata = " . var_export( $attachment_metadata, true ), 0 );

		if ( 'computed_orientation' === $data_value['data_source'] ) {
			if ( !is_array( $attachment_metadata ) ) {
				if ( 0 < $post_id ) {
					$attachment_metadata = get_metadata( 'post', $post_id, '_wp_attachment_metadata', true );
				} else {
					$attachment_metadata = array();
				}
			}
			//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_evaluate_custom_data_source( {$post_id} ) attachment_metadata = " . var_export( $attachment_metadata, true ), 0 );

			if ( isset( $attachment_metadata['width'] ) && isset( $attachment_metadata['height'] ) && 0 < $attachment_metadata['height'] ) {
				return ( ( $attachment_metadata['width'] / $attachment_metadata['height'] ) > 1.0 ) ? 'Landscape' : 'Portrait';
			}
		}

		return $custom_value;
	} // mla_evaluate_custom_data_source

	/**
	 * MLA Fetch Attachment Image Metadata Raw
	 *
	 * For metadata extraction, gives you an opportunity to add or modify elements before MLA's EXIF CAMERA and GPS enhancementws are added.
	 *
	 * @since 1.15
	 *
	 * @param array $metadata The metadata MLA extracted from the attached file
	 * @param array $post_id The ID of the attachment representing the file
	 * @param array $path The location of the attached file
	 */
	public static function mla_fetch_attachment_image_metadata_raw( $metadata, $post_id, $path ) {
		//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_fetch_attachment_image_metadata_raw( {$post_id}, {$path} ) metadata = " . var_export( $metadata, true ), 0 );

		return $metadata;
	} // mla_fetch_attachment_image_metadata_raw

	/**
	 * MLA Fetch Attachment Image Metadata Final
	 *
	 * For metadata extraction, gives you an opportunity to add or modify elements after MLA's EXIF CAMERA and GPS enhancementws are added.
	 *
	 * @since 1.15
	 *
	 * @param array $metadata The metadata MLA extracted from the attached file
	 * @param array $post_id The ID of the attachment representing the file
	 * @param array $path The location of the attached file
	 */
	public static function mla_fetch_attachment_image_metadata_final( $metadata, $post_id, $path ) {
		error_log( __LINE__ . " MLASubstitutionParameterExample::mla_fetch_attachment_image_metadata_final( {$post_id}, {$path} ) metadata = " . var_export( $metadata, true ), 0 );

		return $metadata;
	} // mla_fetch_attachment_image_metadata_final
} //MLASubstitutionParameterExample

// Install the filters at an early opportunity
add_action('init', 'MLASubstitutionParameterExample::initialize');
?>