<?php
/**
 * Post Type Switcher
 *
 * A simple way to change a post's type in WordPress
 *
 * @package Plugins/Admin/Post/TypeSwitcher
 */

/**
 * Plugin Name:       Post Type Switcher
 * Description:       A simple way to change a post's type in WordPress
 * Plugin URI:        https://wordpress.org/plugins/post-type-switcher/
 * Author:            Triple J Software, Inc.
 * Author URI:        https://jjj.software
 * License:           GNU General Public License v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       post-type-switcher
 * Domain Path:       /assets/lang/
 * Requires at least: 6.2
 * Requires PHP:      8.0
 * Tested up to:      6.9
 * Version:           4.0.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * The main post type switcher class
 *
 * @since 1.0.0
 */
final class Post_Type_Switcher {

	/**
	 * Asset version, for cache busting
	 *
	 * @since 3.0.1
	 *
	 * @var string
	 */
	private $asset_version = '202507200015';

	/**
	 * Hook in the basic early actions
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Initialization
	 *
	 * @since 4.0.0
	 */
	public function init() {

		// Load the plugin textdomain
		load_plugin_textdomain( 'post-type-switcher' );

		// Initialize admin
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_init', array( $this, 'admin_done' ) );
	}

	/**
	 * Admin initialization
	 *
	 * @since 1.7.0
	 *
	 * @return void
	 */
	public function admin_init() {

		// Sponsor
		if ( ! defined( 'JJJ_NO_SPONSOR' ) ) {

			// Get basename
			$basename = plugin_basename( __FILE__ );

			add_filter( "plugin_action_links_{$basename}",               array( $this, 'filter_plugin_action_links' ), 20 );
			add_filter( "network_admin_plugin_action_links_{$basename}", array( $this, 'filter_plugin_action_links' ), 20 );
		}

		// Bail if page not allowed
		if ( ! $this->is_allowed_page() ) {
			return;
		}

		// Add column for quick-edit support
		$post_type_names = $this->get_post_types( 'names' );

		// Add filters if post-types are switchable
		if ( ! empty( $post_type_names ) ) {
			foreach ( $post_type_names as $name ) {
				add_filter( "manage_{$name}_posts_columns",       array( $this, 'add_column'    )        );
				add_action( "manage_{$name}_posts_custom_column", array( $this, 'manage_column' ), 10, 2 );
			}
		}

		// Default to "post_type" column being hidden
		add_filter( 'default_hidden_columns', array( $this, 'default_hidden_columns' ) );

		// Add UI to "Publish" metabox
		add_action( 'admin_head',                  array( $this, 'admin_head'        ) );
		add_action( 'post_submitbox_misc_actions', array( $this, 'metabox'           ) );
		add_action( 'quick_edit_custom_box',       array( $this, 'quick_edit'        ) );
		add_action( 'bulk_edit_custom_box',        array( $this, 'quick_edit_bulk'   ) );
		add_action( 'admin_enqueue_scripts',       array( $this, 'quick_edit_script' ) );

		// Add UI to the block editor
		add_action( 'enqueue_block_editor_assets', array( $this, 'block_editor_assets' ) );

		// AJAX handler
		add_action( 'wp_ajax_post_type_switcher', array( $this, 'handle_ajax' ) );

		// Maybe override type on admin-area inserts, when requested
		add_filter( 'wp_insert_attachment_data', array( $this, 'override_type' ), 10, 2 );
		add_filter( 'wp_insert_post_data',       array( $this, 'override_type' ), 10, 2 );

		// Compatibility
		add_action( 'post_type_after_switch', array( $this, 'wpml_sync_type' ), 10, 3 );
	}

	/**
	 * Admin initialized
	 *
	 * @since 4.0.0
	 */
	public function admin_done() {

		/**
		 * Admin initialization complete
		 *
		 * Use this action to unhook parts of this plugin if necessary
		 *
		 * @since 2.0.0
		 * @since 4.0.0 Now fires on all admin requests (not just allowed pages)
		 *
		 * @param Post_Type_Switcher $this Self
		 */
		do_action( 'post_type_switcher', $this );
	}

	/**
	 * Output meta box fields on New/Edit Post screen
	 *
	 * @since 1.0.0
	 */
	public function metabox() {

		// Post types
		$post_types = $this->get_post_types();
		$post_type  = get_post_type();
		$cpt_object = get_post_type_object( $post_type );

		// Bail if object does not exist or produces an error
		if ( ! $cpt_object instanceof \WP_Post_Type ) {
			return;
		}

		// Force-add current post type if it's not in the list
		// https://wordpress.org/support/topic/dont-show-for-non-public-post-types?replies=4#post-5849287
		if ( ! in_array( $cpt_object, $post_types, true ) ) {
			$post_types[ $post_type ] = $cpt_object;
		}

		?><div class="misc-pub-section misc-pub-section-last post-type-switcher">
			<label for="pts_post_type"><?php esc_html_e( 'Post Type:', 'post-type-switcher' ); ?></label>
			<span id="post-type-display"><?php echo esc_html( $cpt_object->labels->singular_name ); ?></span>

			<?php if ( current_user_can( $cpt_object->cap->publish_posts ) ) : ?>

				<a href="#" id="edit-post-type-switcher" class="hide-if-no-js"><?php esc_html_e( 'Edit', 'post-type-switcher' ); ?></a>
				<div id="post-type-select">
					<select name="pts_post_type" id="pts_post_type"><?php

						foreach ( $post_types as $_post_type => $pt ) :

							if ( ! current_user_can( $pt->cap->publish_posts ) ) :
								continue;
							endif;

							?><option value="<?php echo esc_attr( $pt->name ); ?>" <?php selected( $post_type, $_post_type ); ?>><?php echo esc_html( $pt->labels->singular_name ); ?></option><?php

						endforeach;

					?></select>
					<a href="#" id="save-post-type-switcher" class="hide-if-no-js button"><?php esc_html_e( 'OK', 'post-type-switcher' ); ?></a>
					<a href="#" id="cancel-post-type-switcher" class="hide-if-no-js"><?php esc_html_e( 'Cancel', 'post-type-switcher' ); ?></a>
				</div><?php

				wp_nonce_field( 'post-type-selector', 'pts-nonce-select' );

			endif;

		?></div>

	<?php
	}

	/**
	 * Adds the post type column
	 *
	 * @since 1.2.0
	 *
	 * @param array $columns Array of registered columns
	 *
	 * @return array
	 */
	public function add_column( $columns = array() ) {

		// Ensure columns is an array
		if ( empty( $columns ) || ! is_array( $columns ) ) {
			$columns = array();
		}

		// Define new column
		$new_column = array( 'post_type' => esc_html__( 'Type', 'post-type-switcher' ) );

		// Merge new column with existing columns
		return array_merge( $columns, $new_column );
	}

	/**
	 * Adds "post_type" column to array of hidden columns by default
	 *
	 * @since 3.1.0
	 *
	 * @param array $hidden Array of hidden columns
	 *
	 * @return array
	 */
	public function default_hidden_columns( $hidden = array() ) {

		// Ensure hidden is an array
		if ( empty( $hidden ) || ! is_array( $hidden ) ) {
			$hidden = array();
		}

		// Add "post_type" to hidden columns
		$hidden[] = 'post_type';

		// Return hidden columns
		return $hidden;
	}

	/**
	 * Manages the post type column
	 *
	 * @since 1.1.1
	 *
	 * @param string $column_name Name of column
	 * @param int    $post_id     ID of post
	 */
	public function manage_column( $column_name = '', $post_id = 0 ) {

		// Bail if not the post_type column
		if ( 'post_type' !== $column_name ) {
			return;
		}

		// Get the post type object to get the names from it
		$post_type = get_post_type_object( get_post_type( $post_id ) ); ?>

		<span data-post-type="<?php echo esc_attr( $post_type->name ); ?>"><?php echo esc_html( $post_type->labels->singular_name ); ?></span>

		<?php
	}

	/**
	 * Adds quick-edit button for bulk-editing post types
	 *
	 * @since 1.2.0
	 *
	 * @param string $column_name Name of column
	 */
	public function quick_edit( $column_name = '' ) {

		// Bail to prevent multiple dropdowns in each column
		if ( 'post_type' !== $column_name ) {
			return;
		} ?>

		<div id="pts_quick_edit" class="inline-edit-group wp-clearfix">
			<label class="alignleft">
				<span class="title"><?php esc_html_e( 'Post Type', 'post-type-switcher' ); ?></span><?php

				wp_nonce_field( 'post-type-selector', 'pts-nonce-select' );

				$this->select_box();

			?></label>
		</div>

	<?php
	}

	/**
	 * Adds quick-edit button for bulk-editing post types
	 *
	 * @since 1.2.0
	 *
	 * @param string $column_name Name of column
	 */
	public function quick_edit_bulk( $column_name = '' ) {

		// Bail to prevent multiple dropdowns in each column
		if ( 'post_type' !== $column_name ) {
			return;
		} ?>

		<label id="pts_bulk_edit" class="alignleft">
			<span class="title"><?php esc_html_e( 'Post Type', 'post-type-switcher' ); ?></span><?php

			wp_nonce_field( 'post-type-selector', 'pts-nonce-select' );

			$this->select_box( true );

		?></label>

	<?php
	}

	/**
	 * Adds quick-edit script for getting values into quick-edit box
	 *
	 * @since 1.2
	 */
	public function quick_edit_script( $hook = '' ) {

		// Bail if not edit.php admin page
		if ( 'edit.php' !== $hook ) {
			return;
		}

		// Enqueue quick edit JS
		wp_enqueue_script( 'pts_quickedit', plugin_dir_url( __FILE__ ) . 'assets/js/quickedit.js', array( 'jquery' ), $this->asset_version, true );
	}

	/**
	 * Enqueues modifications to the block editor.
	 *
	 * @since 3.2.0
	 */
	public function block_editor_assets() {

		// Bail if current user cannot publish the current post type
		$current_post_type        = get_post_type();
		$current_post_type_object = get_post_type_object( $current_post_type );
		if ( ! current_user_can( $current_post_type_object->cap->publish_posts ) ) {
			return;
		}

		// Get all switchable types
		$switchable_types = $this->get_post_types();

		// Bail if no switchable types
		if ( empty( $switchable_types ) ) {
			return;
		}

		// Default empty available types array
		$available_post_types = array();

		// Loop through switchable types
		foreach ( $switchable_types as $post_type ) {

			// Skip if user cannot switch to
			if ( ! current_user_can( $post_type->cap->publish_posts ) ) {
				continue;
			}

			// Setup value/label to be localized below
			$available_post_types[] = array(
				'value' => $post_type->name,
				'label' => $post_type->labels->singular_name,
			);
		}

		// Setup the AJAX URL used to send the change request to
		$change_url = add_query_arg(
			array(
				'action'           => 'post_type_switcher',
				'pts-nonce-select' => wp_create_nonce( 'post-type-selector' ),
				'post_id'          => get_the_ID(),
			),
			admin_url( 'admin-ajax.php' )
		);

		// Enqueue the block editor script
		wp_enqueue_script(
			'pts_blockeditor',
			plugin_dir_url( __FILE__ ) . 'assets/js/block.js',
			array( 'wp-components', 'wp-edit-post', 'wp-element', 'wp-i18n', 'wp-plugins' ),
			$this->asset_version
		);

		// Localize the block editor script variables
		wp_localize_script(
			'pts_blockeditor',
			'ptsBlockEditor',
			array(
				'availablePostTypes'   => $available_post_types,
				'currentPostType'      => $current_post_type,
				'currentPostTypeLabel' => $current_post_type_object->labels->singular_name,
				'changeUrl'            => $change_url
			)
		);
	}

	/**
	 * Output a post-type dropdown
	 *
	 * @since 1.2
	 */
	public function select_box( $bulk = false ) {

		// Get post type specific data
		$post_types = $this->get_post_types();
		$post_type  = get_post_type();
		$selected   = '';

		// Start an output buffer
		ob_start();

		// Output
		?><select name="pts_post_type" id="pts_post_type"><?php

			// Maybe include "No Change" option for bulk
			if ( true === $bulk ) :
				?><option value="-1"><?php esc_html_e( '&mdash; No Change &mdash;', 'post-type-switcher' ); ?></option><?php
			endif;

			// Loop through post types
			foreach ( $post_types as $_post_type => $pt ) :

				// Skip if user cannot publish this type of post
				if ( ! current_user_can( $pt->cap->publish_posts ) ) :
					continue;
				endif;

				// Only select if not bulk
				if ( false === $bulk ) :
					$selected = selected( $post_type, $_post_type, false );
				endif;

				// Output option
				?><option value="<?php echo esc_attr( $pt->name ); ?>" <?php echo $selected; // Do not escape ?>><?php echo esc_html( $pt->labels->singular_name ); ?></option><?php

			endforeach;

		?></select><?php

		// Output the current buffer
		echo ob_get_clean();
	}

	/**
	 * Handles an admin-ajax request to change post types.
	 *
	 * Note that these use $_GET values specifically, to avoid collisions with
	 * upstream requests.
	 *
	 * @since 3.2.0
	 */
	public function handle_ajax() {

		// Bail if missing data
		if (
			   empty( $_GET['pts_post_type'] )
			|| empty( $_GET['pts-nonce-select'] )
			|| empty( $_GET['post_id'] )
		) {
			return wp_die( esc_html__( 'Missing data.', 'post-type-switcher' ) );
		}

		// Post type information
		$post_id          = absint( $_GET['post_id'] );
		$post_type        = sanitize_key( $_GET['pts_post_type'] );
		$post_type_object = get_post_type_object( $post_type );

		// Bail if user isn't capable or nonce fails
		if ( ! current_user_can( $post_type_object->cap->publish_posts )
			|| ! wp_verify_nonce( $_GET['pts-nonce-select'], 'post-type-selector' ) ) {
			return wp_die( esc_html__( 'Sorry, you cannot do this.', 'post-type-switcher' ) );
		}

		// Retrieve the original post type for later use
		$original_post_type = get_post_type( $post_id );

		// Update the post type
		$this->set_post_type( $post_id, $post_type );

		/**
		 * Allow actions after post type switch
		 *
		 * @since 1.0.0
		 *
		 * @param $updated_post_type string The new post type
		 * @param $post_type The old post type
		 * @param $post_id The post ID
		 */
		do_action( 'post_type_after_switch', $post_type, $original_post_type, $post_id );

		// Redirect
		wp_safe_redirect( get_edit_post_link( $post_id, 'raw' ) );
		exit;
	}

	/**
	 * Override post_type in wp_insert_post()
	 *
	 * We do a bunch of sanity checks here, to make sure we're only changing the
	 * post type when the user explicitly intends to.
	 *
	 * - Not during autosave
	 * - Check nonce
	 * - Check user capabilities
	 * - Check $_POST input name
	 * - Check if revision or current post-type
	 * - Check new post-type exists
	 * - Check that user can publish posts of new type
	 *
	 * @since 2.0.0
	 *
	 * @param  array  $data
	 * @param  array  $postarr
	 *
	 * @return array Maybe modified $data
	 */
	public function override_type( $data = array(), $postarr = array() ) {

		// Bail if form field is missing
		if ( empty( $_REQUEST['pts_post_type'] ) || empty( $_REQUEST['pts-nonce-select'] ) ) {
			return $data;
		}

		// Bail if no specific post ID is being saved
		if ( empty( $postarr['post_ID'] ) ) {
			return $data;
		}

		// Post type information
		$post_id          = absint( $postarr['post_ID'] );
		$post_type        = sanitize_key( $_REQUEST['pts_post_type'] );
		$post_type_object = get_post_type_object( $post_type );

		// Bail if empty post type
		if ( empty( $post_id ) || empty( $post_type ) || empty( $post_type_object ) ) {
			return $data;
		}

		// Bail if no change
		if ( $post_type === $data['post_type'] ) {
			return $data;
		}

		// Bail if posted ID to update does not match the post ID being changed.
		// This is to prevent child posts (or related) from also being changed.
		// See: https://github.com/JJJ/post-type-switcher/issues/9
		if ( $post_id !== $postarr['ID'] ) {
			return $data;
		}

		// Bail if user cannot 'edit_post' on the current post ID
		if ( ! current_user_can( 'edit_post', $postarr['ID'] ) ) {
			return $data;
		}

		// Bail if user cannot 'publish_posts' on the new type
		if ( ! current_user_can( $post_type_object->cap->publish_posts ) ) {
			return $data;
		}

		// Bail if nonce is invalid
		if ( ! wp_verify_nonce( $_REQUEST['pts-nonce-select'], 'post-type-selector' ) ) {
			return $data;
		}

		// Bail if autosave
		if ( wp_is_post_autosave( $postarr['ID'] ) ) {
			return $data;
		}

		// Bail if revision
		if ( wp_is_post_revision( $postarr['ID'] ) ) {
			return $data;
		}

		// Bail if it's a revision
		if ( in_array( $postarr['post_type'], array( $post_type, 'revision' ), true ) ) {
			return $data;
		}

		// Update post type
		$data['post_type'] = $post_type;

		/**
		 * Allow actions after post type switch
		 *
		 * @since 3.0.0
		 *
		 * @param $updated_post_type string The new post type
		 * @param $post_type The old post type
		 * @param $post_id The post ID
		 */
		do_action( 'post_type_after_switch', $post_type, $postarr['post_type'], $postarr['ID'] );

		// Return modified post data
		return $data;
	}

	/**
	 * Switch post translations via WPML
	 *
	 * @param $post_type string The new post type
	 * @param $original_post_type The old post type
	 * @param $post_id The post ID
	 *
	 * @return void
	 */
	public function wpml_sync_type( $post_type, $original_post_type, $post_id ) {
		global $wpdb, $sitepress;

		if ( is_a( $sitepress, '\SitePress' ) ) {

			// Sanitize the post type
			$post_type = sanitize_key( $post_type );

			// Retrieve the translation grouping ID
			// Used to select and update sibling translations
			$trid = $wpdb->get_var( $wpdb->prepare( "
				SELECT 	trid
				FROM 	{$wpdb->prefix}icl_translations
				WHERE 	element_id = %d
			", $post_id ) );

			// Update translation grouping element types
			$wpdb->update(
				$wpdb->prefix . 'icl_translations',
				array( 'element_type' => 'post_' . $post_type ),
				array( 'trid'		  => $trid )
			);

			// Retrieve other posts that are sibling translations
			$translation_items = $wpdb->get_col( $wpdb->prepare( "
				SELECT 	element_id
				FROM 	{$wpdb->prefix}icl_translations
				WHERE 	trid = %d
			", $trid ) );

			// Bail if no translation items
			if ( empty( $translation_items ) || ! is_array( $translation_items ) ) {
				return;
			}

			// Update post type of sibling translations
			foreach ( $translation_items as $_post_id ) {
				$this->set_post_type( $_post_id, $post_type );
			}
		}
	}

	/**
	 * Adds needed JS and CSS to admin header
	 *
	 * @since 1.0.0
	 *
	 * @return void If on post-new.php
	 */
	public function admin_head() {
	?>

		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				jQuery( '.misc-pub-section.curtime.misc-pub-section-last' ).removeClass( 'misc-pub-section-last' );
				jQuery( '#edit-post-type-switcher' ).on( 'click', function(e) {
					jQuery( this ).hide();
					jQuery( '#post-type-select' ).slideDown();
					e.preventDefault();
				});
				jQuery( '#save-post-type-switcher' ).on( 'click', function(e) {
					jQuery( '#post-type-select' ).slideUp();
					jQuery( '#edit-post-type-switcher' ).show();
					jQuery( '#post-type-display' ).text( jQuery( '#pts_post_type :selected' ).text() );
					e.preventDefault();
				});
				jQuery( '#cancel-post-type-switcher' ).on( 'click', function(e) {
					jQuery( '#post-type-select' ).slideUp();
					jQuery( '#edit-post-type-switcher' ).show();
					e.preventDefault();
				});
			});
		</script>
		<style type="text/css">
			#wpbody-content .inline-edit-row .inline-edit-col-right .alignleft + .alignleft {
				float: right;
			}
			#post-type-select {
				line-height: 2.5em;
				margin-top: 3px;
				display: none;
			}
			#post-type-select select#pts_post_type {
				margin-right: 2px;
			}
			#post-type-select a#save-post-type-switcher {
				vertical-align: middle;
				margin-right: 2px;
			}
			#post-type-display {
				font-weight: bold;
			}
			#post-body .post-type-switcher::before {
				content: '\f109';
				font: 400 20px/1 dashicons;
				speak: never;
				display: inline-block;
				padding: 0 2px 0 0;
				top: 0;
				left: -1px;
				position: relative;
				vertical-align: top;
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
				text-decoration: none !important;
				color: #888;
			}
			.wp-list-table .column-post_type {
				width: 10%;
			}
			.edit-post-post-type {
				display: flex;
				-webkit-box-align: center;
				align-items: center;
				flex-direction: row;
				gap: calc(8px);
				-webkit-box-pack: justify;
				justify-content: space-between;
				margin-top: -8px;
				width: 100%;
			}
			.edit-post-post-type span {
				display: inline-block;
				flex-shrink: 0;
				padding: 6px 0;
				width: 45%;
			}
			.components-button.edit-post-post-type__toggle {
				height: auto;
				text-align: left;
				white-space: normal;
				word-break: break-word;
			}
			.editor-post-type__dialog-fieldset {
				margin: 8px;
				min-width: 248px;
			}
			.editor-post-type__dialog-fieldset .editor-post-type__dialog-legend {
				line-height: 1.2;
				margin-top: 0px;
				margin-bottom: 16px;
				color: rgb(30, 30, 30);
				font-size: calc(13px);
				font-weight: 600;
				display: block;
			}
			.editor-post-type__dialog-fieldset .editor-post-type__choice {
				margin: 8px;
				display: block;
			}
			.editor-post-type__dialog-fieldset .editor-post-type__choice:last-child {
				margin-bottom: 0;
			}
			.editor-post-type__dialog-fieldset .editor-post-type__dialog-radio[type=radio] {
				display: inline-block;
			}
			.editor-post-type__dialog-fieldset .editor-post-type__dialog-label {
				margin: -3px 0 0 8px;
				display: inline-block;
			}
		</style>

	<?php
	}

	/**
	 * Whether or not the current file requires the post type switcher
	 *
	 * @since 1.1.0
	 *
	 * @return bool True if it should load, false if not
	 */
	private static function is_allowed_page() {

		if (
			// Only for admin area
			is_blog_admin()

			// OR
			||

			// AJAX with correct action value
			(
				wp_doing_ajax()
				&&
				(
					! empty( $_REQUEST['action'] )
					&&
					(
						in_array( $_REQUEST['action'], array( 'inline-save', 'post_type_switcher' ), true )
					)
				)
			)
		) {

			/**
			 * Allowed admin page file names.
			 *
			 * @since 1.0.0
			 * @param array Array of page file names
			 */
			$pages = apply_filters( 'pts_allowed_pages', array(
				'post.php',
				'edit.php',
				'admin-ajax.php' // Block Editor
			) );

			// Only show switcher when editing
			return (bool) in_array( $GLOBALS['pagenow'], $pages, true );
		}

		// Default to false
		return false;
	}

	/**
	 * Set the `post_type` for a given ID.
	 *
	 * Also stashes the most-previous type in meta.
	 *
	 * @since 4.0.0
	 *
	 * @param int    $post_id   ID of post.
	 * @param string $post_type Type for post.
	 *
	 * @return int Number of updated rows (1, or 0)
	 */
	private function set_post_type( $post_id = 0, $post_type = '' ) {

		// Get the current/previous post_type
		$current  = get_post_type( $post_id );

		// Try to get original & previous
		$original = get_post_meta( $post_id, 'pts_original_type', true );
		$previous = get_post_meta( $post_id, 'pts_previous_type', true );

		// Try to set the post type
		$retval   = set_post_type( $post_id, $post_type );

		// Maybe stash the current/previous in meta
		if ( ! empty( $retval ) ) {

			// Only add the original type once
			if ( empty( $original ) ) {
				add_post_meta( $post_id, 'pts_original_type', $current );

			// Only delete original meta after revert
			} elseif ( $post_type === $original ) {
				delete_post_meta( $post_id, 'pts_original_type' );
			}

			// Update the previous meta key
			if ( $post_type !== $previous ) {
				update_post_meta( $post_id, 'pts_previous_type', $current );

			// Only delete previous meta after revert
			} else {
				delete_post_meta( $post_id, 'pts_previous_type' );
			}
		}

		// Return (number of updated rows, 1 or 0)
		return $retval;
	}

	/**
	 * Get switchable post type objects, based on post-type arguments.
	 *
	 * @since 3.2.0
	 *
	 * @param string $output Optional. The type of output to return. Accepts
	 *                       post type 'names' or 'objects'. Default 'objects'.
	 * @return array
	 */
	private function get_post_types( $output = 'objects' ) {

		// Get switchable types
		$types = get_post_types( $this->get_post_type_args(), $output );

		// Unset attachment types, since support seems to be broken
		if ( isset( $types['attachment'] ) ) {
			unset( $types['attachment'] );
		}

		/**
		 * Filter the post types (usually objects).
		 *
		 * @since 4.0.0
		 *
		 * @param  array  $types Array of post types (usually objects)
		 * @param  string $output The type of output that $types is
		 * @return array
		 */
		return (array) apply_filters( 'pts_get_post_types_filter', $types, $output );
	}

	/**
	 * Returns the array of arguments used to narrow down the switchable post
	 * types from the globally registered $wp_post_types array.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	private function get_post_type_args() {

		// Default arguments
		$args = array(
			'public'  => true,
			'show_ui' => true
		);

		/**
		 * Filter the arguments that get passed into `get_post_types()`.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $args Array of arguments
		 * @return array
		 */
		return (array) apply_filters( 'pts_post_type_filter', $args );
	}

	/**
	 * Filter plugin action links, and add a sponsorship link.
	 *
	 * @since 3.2.1
	 * @param array $actions
	 * @return array
	 */
	public function filter_plugin_action_links( $actions = array() ) {

		// Nothing here anymore. Reserved for later.
		return $actions;
	}
}
new Post_Type_Switcher();
