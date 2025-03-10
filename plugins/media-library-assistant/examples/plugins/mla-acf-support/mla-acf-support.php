<?php
/**
 * Adds MLA-style taxonomy to the ACF Gallerycustom field handler
 *
 * Created for support topic "Apply MLA Taxonomy Picker to an ACF Gallery Field?"
 * opened on 1/30/2025 by "kingofmycastle".
 * https://wordpress.org/support/topic/apply-mla-taxonomy-picker-to-an-acf-gallery-field/
 *
 * @package MLA ACF Support
 * @version 1.00
 */

/*
Plugin Name: MLA ACF Support
Plugin URI: http://davidlingren.com/
Description: Adds MLA-style taxonomy to the ACF Gallery custom field handler.
Author: David Lingren
Version: 1.00
Author URI: http://davidlingren.com/

Copyright 2025 David Lingren

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
 * Class MLA ACF Support MLA-style taxonomy to the ACF Gallerycustom field handler=
 *
 * @package MLA ACF Support
 * @since 1.00
 */
class MLAACFSupport {
	/**
	 * Current version number
	 *
	 * @since 1.00
	 *
	 * @var	string
	 */
	const PLUGIN_VERSION = '1.00';

	/**
	 * Slug prefix for registering and enqueueing submenu pages, style sheets, scripts and settings
	 *
	 * @since 1.00
	 *
	 * @var	string
	 */
	const SLUG_PREFIX = 'mlaacfsupport';

	/**
	 * Constant to log this plugin's debug activity
	 *
	 * @since 1.00
	 *
	 * @var	integer
	 */
	const MLA_DEBUG_CATEGORY = 0x00008000;

	/**
	 * Constant to enqueue CSS styles
	 *
	 * @since 1.00
	 *
	 * @var	string
	 */
	const MLA_ACF_MEDIA_MODAL_STYLES = 'mla-acf-media-modal-styles';

	/**
	 * Constant to enqueue scripts
	 *
	 * @since 1.00
	 *
	 * @var	string
	 */
	const MLA_ACF_MEDIA_MODAL_SCRIPTS = 'mla-acf-media-modal-scripts';

	/**
	 * Settings Management object
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $plugin_settings = NULL;

	/**
	 * In-memory representation of the option settings
	 *
	 * @since 1.00
	 *
	 * @var array $_settings {
	 *     @see $_default_settings
	 *     }
	 */
	private static $_settings = NULL;

	/**
	 * Configuration values for the Settings Management object
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $settings_arguments = array(
				'slug_prefix' => self::SLUG_PREFIX,
				'plugin_title' => 'MLA ACF Support',
				'menu_title' => 'MLA ACF Support',
				'plugin_file_name_only' => 'mla-acf-support',
				'plugin_version' => self::PLUGIN_VERSION,
				'template_file' => '/admin-settings-page.tpl', // Add the path at runtime, in initialize()
				'messages' => '',
				'options' => array(
					// 'slug' => array( 'type' => 'checkbox|text|select|textarea', 'default' => 'text|boolean(0|1)' )
					// See the default values in $_default_settings below
				),
				'general_tab_values' => array(), // additional page_values for 'page-level-options' template
				'documentation_tab_values' => array(
					'plugin_title' => 'MLA ACF Support',
					'settingsURL' => '', // Set at runtime in initialize()
				), // page_values for 'documentation-tab' template
			);

	/**
	 * Default processing options
	 *
	 * @since 1.00
	 *
	 * @var array $_default_settings {
	 *     @type boolean $checkbox_slug Checkbox description
	 *     @type string  $text_slug Text field description
	 *     @type string  $static_select_slug Static dropdown control description
	 *     @type string  $textarea_slug Textarea description
	 *     }
	 */
	private static $_default_settings = array (
					);

	/**
	 * Template file for the Settings page(s) and parts
	 *
	 * This array contains all of the template parts for the Settings page(s). The array is built once
	 * each page load and cached for subsequent use.
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	public static $page_template_array = NULL;

	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 */
	public static function initialize() {
		// This plugin requires MLA
		if ( ! class_exists( 'MLACore', false ) ) {
			return;
		}

		// This plugin requires ACF Pro version, which includes the Gallery field type
		if ( ! class_exists( 'acf_pro', false ) ) {
			return;
		}

		// The plugin settings class is shared with other MLA example plugins
		if ( ! class_exists( 'MLAExamplePluginSettings102' ) ) {
			require_once( pathinfo( __FILE__, PATHINFO_DIRNAME ) . '/class-mla-example-plugin-settings-102.php' );
		}

		// Add the run-time values to the arguments
		self::$settings_arguments['template_file'] = dirname( __FILE__ ) . self::$settings_arguments['template_file'];
		self::$settings_arguments['documentation_tab_values']['settingsURL'] = admin_url('options-general.php');

		// Create our own settings object
		self::$plugin_settings = new MLAExamplePluginSettings102( self::$settings_arguments );

		// The filters are only useful in the admin section
		if ( ! is_admin() )
			return;

		if ( ( ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_TOOLBAR ) )
			 || ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_GRID_TOOLBAR ) ) ) ) {
			add_filter( 'mla_media_modal_settings', 'MLAACFSupport::mla_media_view_settings_filter', 10, 2 );
			add_filter( 'mla_media_modal_strings', 'MLAACFSupport::mla_media_view_strings_filter', 10, 2 );
			add_action( 'wp_enqueue_media', 'MLAACFSupport::mla_wp_enqueue_media_action', 10, 0 );

			/*
			 * For each media item found by "query_attachments", these filters are called:
			 *
			 * In /wp-admin/includes/media.php, functions get_media_item() and get_compat_media_markup()
			 * contain "apply_filters( 'get_media_item_args', $args );", documented as:
			 * "Filter the arguments used to retrieve an image for the edit image form."
			 *
			 * In /wp-admin/includes/media.php, functions get_attachment_fields_to_edit()
			 * and get_compat_media_markup() contain
			 * "$form_fields = apply_filters( 'attachment_fields_to_edit', $form_fields, $post );",
			 * documented as: "Filter the attachment fields to edit."
			 */
			add_filter( 'get_media_item_args', 'MLAACFSupport::mla_get_media_item_args_filter', 10, 1 );
			add_filter( 'attachment_fields_to_edit', 'MLAACFSupport::mla_attachment_fields_to_edit_filter', 0x7FFFFFFF, 2 );
		} // Media Modal support enabled

		// Add the run-time values to the settings
		// Load template array and initialize page-level values.
		self::$page_template_array = MLACore::mla_load_template( self::$settings_arguments['template_file'], 'path' );

		//$general_tab_values = self::$plugin_settings->get_plugin_argument('general_tab_values');
		//MLACore::mla_debug_add( __LINE__ . " MLAGalleryDownloadChecklist::initialize \$general_tab_values = " . var_export( $general_tab_values, true ), self::MLA_DEBUG_CATEGORY );
	}

	/**
	 * Adds settings values to be passed to the Media Manager in /wp-includes/js/media-views.js.
	 *
	 * @since 1.00
	 *
	 * @param	array	associative array with setting => value pairs
	 * @param	object || NULL	current post object, if available
	 *
	 * @return	array	updated $settings array
	 */
	public static function mla_media_view_settings_filter( $settings, $post ) {
		$settings = array_merge( $settings, array( 'mla_acf_settings' => array( 'placeholder' => 'Placeholder' ) ) );
error_log( __LINE__ . " DEBUG: class MLAACFSupport::mla_media_view_settings_filter( {$post->ID} ) settings = " . var_export( $settings, true ), 0 );
		return $settings;
	} // mla_mla_media_view_settings_filter

	/**
	 * Adds string values to be passed to the Media Manager in /wp-includes/js/media-views.js.
	 *
	 * @since 1.00
	 *
	 * @param	array	associative array with string => value pairs
	 * @param	object || NULL	current post object, if available
	 *
	 * @return	array	updated $strings array
	 */
	public static function mla_media_view_strings_filter( $strings, $post ) {
		$strings = array_merge( $strings, array( 'mla_acf_strings' => array( 'placeholder' => 'Placeholder' ) ) );
error_log( __LINE__ . " DEBUG: class MLAACFSupport::mla_media_view_strings_filter( {$post->ID} ) strings = " . var_export( $strings, true ), 0 );
		return $strings;
	} // mla_mla_media_view_strings_filter

	/**
	 * Create version number for script files with/without Development Version date
	 *
	 * @since 2.99
	 *
	 * @return string Version number for wp_enqueue_script()
	 */
	private static function _script_version() {
		$script_version =  self::PLUGIN_VERSION;
		$script_version .=  ( strlen( MLACore::MLA_DEVELOPMENT_VERSION ) ) ? '.' . MLACore::MLA_DEVELOPMENT_VERSION : '';
		
		return $script_version;
	}

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

		$plugin_dir_url = plugin_dir_url( __FILE__ );
		$script_version = self::_script_version();
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
error_log( __LINE__ . " DEBUG: class MLAACFSupport::mla_wp_enqueue_media_action( {$plugin_dir_url}, {$script_version}, {$suffix} )", 0 );

		if ( $wp_locale->is_rtl() ) {
			wp_register_style( self::MLA_ACF_MEDIA_MODAL_STYLES, $plugin_dir_url . 'mla-acf-support-rtl.css', false, $script_version );
		} else {
			wp_register_style( self::MLA_ACF_MEDIA_MODAL_STYLES, $plugin_dir_url . 'mla-acf-support.css', false, $script_version );
		}

		wp_enqueue_style( self::MLA_ACF_MEDIA_MODAL_STYLES );

		// These scripts are safe to load in the document header section because they have no depandencies
		wp_enqueue_script( self::MLA_ACF_MEDIA_MODAL_SCRIPTS, $plugin_dir_url . "mla-acf-support-scripts{$suffix}.js", array( MLAModal::JAVASCRIPT_MEDIA_MODAL_SLUG ), $script_version, true );
	} // mla_wp_enqueue_media_action

	/**
	 * Saves the get_media_item_args array for the attachment_fields_to_edit filter
	 *
	 * @since 1.00
	 *
	 * @param	array	arguments for the get_media_item function in /wp-admin/includes/media.php
	 *
	 * @return	array	arguments for the get_media_item function (unchanged)
	 */
	public static function mla_get_media_item_args_filter( $args ) {
error_log( __LINE__ . ' DEBUG: class MLAACFSupport::mla_get_media_item_args_filter $args = ' . var_export( $args, true ), 0 );
		self::$media_item_args = $args;
		return $args;
	} // mla_get_media_item_args_filter

	/**
	 * The get_media_item_args array
	 *
	 * @since 1.71
	 *
	 * @var	array ( 'errors' => array of strings, 'in_modal => boolean )
	 */
	private static $media_item_args = array( 'errors' => NULL, 'in_modal' => false );

	/**
	 * Add enhanced taxonomy fields to the ACF Gallery sidebar
	 *
	 * @since 1.00
	 *
	 * @param	array	descriptors for the "compat-attachment-fields" 
	 * @param	object	the post to be edited
	 *
	 * @return	array	updated descriptors for the "compat-attachment-fields"
	 */
	public static function mla_attachment_fields_to_edit_filter( $form_fields, $post ) {
		$id = $post->ID;
error_log( __LINE__ . ' DEBUG: class MLAACFSupport::mla_attachment_fields_to_edit_filter $form_fields = ' . var_export( $form_fields, true ), 0 );
		
		if ( ! isset( $form_fields['acf-form-data'] ) ) {
			return $form_fields;
		}

		// This logic is only required for the ACF Gallery custom field blocks
		if ( ! isset( $_REQUEST['action'] ) || 'acf/fields/gallery/get_attachment' !== $_REQUEST['action'] ) {
			return $form_fields;
		}

		if ( isset( self::$media_item_args['in_modal'] ) && self::$media_item_args['in_modal'] ) {
			return $form_fields;
		}

		$mla_form_data = '';
		foreach ( get_taxonomies( array ( 'show_ui' => true ), 'objects' ) as $key => $value ) {
			if ( MLACore::mla_taxonomy_support( $key ) ) {
				$field = (array) $value;

				if ( ! $use_checklist = $value->hierarchical ) {
					$use_checklist =  MLACore::mla_taxonomy_support( $key, 'flat-checklist' );
				}

				// Make sure the appropriate MMMW Enhancement option has been checked
				if ( $use_checklist ) {
					if ( 'checked' !== MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_DETAILS_CATEGORY_METABOX ) ) {
						continue;
					}
				} else {
					if ( 'checked' !== MLACore::mla_get_option( MLACoreOptions::MLA_MEDIA_MODAL_DETAILS_TAG_METABOX ) ) {
						continue;
					}
				}

				/*
				 * Simulate the default MMMW text box with a hidden field;
				 * use term names for flat taxonomies and term_ids for hierarchical.
				 */
				$post_id = $post->ID;
				$label = $field['labels']->name;
				$terms = get_object_term_cache( $post_id, $key );

				if ( false === $terms ) {
					$terms = wp_get_object_terms( $post_id, $key );
					wp_cache_add( $post_id, $terms, $key . '_relationships' );
				}

				if ( is_wp_error( $terms ) || empty( $terms ) ) {
					$terms = array();
				}

				$list = array();
				foreach ( $terms as $term ) {
					if ( $value->hierarchical ) {
						$list[] = $term->term_id;
					} else {
						$list[] = $term->name;
					}
				} // foreach $term

				sort( $list );
				$list = join( ',', $list );
				$class = ( $value->hierarchical ) ? 'categorydiv' : 'tagsdiv';

				$row  = "\t\t<tr class='compat-field-{$key} mla-taxonomy-row' style='display: none'>\n";
				$row .= "\t\t<th class='label' valign='top' scope='row'>\n";
				$row .= "\t\t<label for='mla-attachments-{$post_id}-{$key}'>\n";
				$row .= "\t\t<span title='" . __( 'Click to toggle', 'media-library-assistant' ) . "' class='alignleft'>{$label}</span><br class='clear'>\n";
				$row .= "\t\t</label></th>\n";
				$row .= "\t\t<td class='field'>\n";
				$row .= "\t\t<div class='mla-taxonomy-field'>\n";
				$row .= "\t\t<input name='mla_attachments[{$post_id}][{$key}]' class='text' id='mla-attachments-{$post_id}-{$key}' type='hidden' value='{$list}'>\n";
				$row .= "\t\t<div id='mla-taxonomy-{$key}' class='{$class}'>\n";
				$row .= '&lt;- ' . __( 'Click to toggle', 'media-library-assistant' ) . "\n";
				$row .= "\t\t</div>\n";
				$row .= "\t\t</div>\n";
				$row .= "\t\t</td>\n";
				$row .= "\t\t</tr>\n";
				$mla_form_data .= $row;
			} // is supported
		} // foreach

		if ( ! empty( $mla_form_data ) ) {
			$mla_form_data = "\t<table>\n" . $mla_form_data . "\t</table>\n";
			$acf_html = $form_fields['acf-form-data']['html'];
			$offset = strpos( $acf_html, '<tr class="compat-field-acf-blank' );
			if ( $offset ) {
				$form_fields['acf-form-data']['html'] = substr_replace( $acf_html, $mla_form_data, $offset, 0 );
			}
		}

		self::$media_item_args = array( 'errors' => NULL, 'in_modal' => false );
error_log( __LINE__ . ' DEBUG: class MLAACFSupport::mla_attachment_fields_to_edit_filter return $form_fields = ' . var_export( $form_fields, true ), 0 );
		return $form_fields;
	} // mla_attachment_fields_to_edit_filter
} // Class MLAACFSupport

// Install the filters at an early opportunity
add_action('init', 'MLAACFSupport::initialize');
?>