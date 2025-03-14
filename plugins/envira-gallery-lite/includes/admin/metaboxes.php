<?php
/**
 * Metabox class.
 *
 * @since 1.0.0
 *
 * @package Envira_Gallery
 * @author  Envira Gallery Team
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Envira Metabox Class
 *
 * @since 1.0.0
 */
class Envira_Gallery_Metaboxes {

	/**
	 * Holds the class object.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Path to the file.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $file = __FILE__;

	/**
	 * Holds the base class object.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public $base;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Load the base class object.
		$this->base = Envira_Gallery_Lite::get_instance();

		// Output a notice if missing cropping extensions because Envira needs them.
		if ( ! $this->has_gd_extension() && ! $this->has_imagick_extension() ) {
			add_action( 'admin_notices', [ $this, 'notice_missing_extensions' ] );
		}

		// Scripts and styles.
		add_action( 'admin_enqueue_scripts', [ $this, 'styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'scripts' ] );

		// Conflict resolvers and plugin busters.
		add_action( 'admin_enqueue_scripts', [ $this, 'fix_plugin_js_conflicts' ], 100 );
		add_action( 'wp_print_scripts', [ $this, 'fix_plugin_js_conflicts' ], 100 );

		// Metaboxes.
		add_action( 'add_meta_boxes_envira', [ $this, 'add_meta_boxes' ], 1 );

		// Add the envira-gallery class to the form, so our styles can be applied.
		add_action( 'post_edit_form_tag', [ $this, 'add_form_class' ] );

		// Modals.
		add_filter( 'media_view_strings', [ $this, 'media_view_strings' ] );

		// Load all tabs.
		add_action( 'envira_gallery_tab_images', [ $this, 'images_tab' ] );
		add_action( 'envira_gallery_tab_config', [ $this, 'config_tab' ] );
		add_action( 'envira_gallery_tab_lightbox', [ $this, 'lightbox_tab' ] );
		add_action( 'envira_gallery_tab_misc', [ $this, 'misc_tab' ] );

		add_filter( 'envira_gallery_tab_nav', [ $this, 'lite_tabs' ] );
		add_action( 'envira_gallery_tab_mobile', [ $this, 'lite_mobile_tab' ] );
		add_action( 'envira_gallery_tab_videos', [ $this, 'lite_videos_tab' ] );
		add_action( 'envira_gallery_tab_social', [ $this, 'lite_social_tab' ] );
		add_action( 'envira_gallery_tab_tags', [ $this, 'lite_tags_tab' ] );
		add_action( 'envira_gallery_tab_animations', [ $this, 'lite_animations_tab' ] );
		add_action( 'envira_gallery_tab_pagination', [ $this, 'lite_pagination_tab' ] );
		add_action( 'envira_gallery_tab_comments', [ $this, 'lite_comments_tab' ] );
		add_action( 'envira_gallery_tab_search', [ $this, 'lite_search_tab' ] );

		// Save Gallery.
		add_action( 'save_post', [ $this, 'save_meta_boxes' ], 10, 2 );
	}

	/**
	 * Outputs a notice when the GD and Imagick PHP extensions aren't installed.
	 *
	 * @since 1.0.0
	 */
	public function notice_missing_extensions() {

		?>
		<div class="error">
			<p><strong><?php esc_html_e( 'The GD or Imagick libraries are not installed on your server. Envira Gallery requires at least one (preferably Imagick) in order to crop images and may not work properly without it. Please contact your webhost and ask them to compile GD or Imagick for your PHP install.', 'envira-gallery-lite' ); ?></strong></p>
		</div>
		<?php
	}

	/**
	 * Changes strings in the modal image selector if we're editing an Envira Gallery.
	 *
	 * @since 1.4.0
	 *
	 * @param    array $strings    Media View Strings.
	 * @return   array               Media View Strings.
	 */
	public function media_view_strings( $strings ) {

		// Check if we can get a current screen.
		// If not, we're not on an Envira screen so we can bail.
		if ( ! function_exists( 'get_current_screen' ) ) {
			return $strings;
		}

		// Get the current screen.
		$screen = get_current_screen();

		// Check we're editing an Envira CPT.
		if ( ! $screen ) {
			return $strings;
		}
		if ( 'envira' !== $screen->post_type ) {
			return $strings;
		}

		// If here, we're editing an Envira CPT.
		// Modify some of the media view's strings.
		$strings['insertIntoPost'] = __( 'Insert into Gallery', 'envira-gallery-lite' );
		$strings['inserting']      = __( 'Inserting...', 'envira-gallery-lite' );

		// Allow addons to filter strings.
		$strings = apply_filters( 'envira_gallery_media_view_strings', $strings, $screen );

		// Return.
		return $strings;
	}

	/**
	 * Appends the "Select Files From Other Sources" button to the Media Uploader, which is called using WordPress'
	 * media_upload_form() function.
	 *
	 * Also appends a hidden upload progress bar, which is displayed by js/media-upload.js when the user uploads images
	 * from their computer.
	 *
	 * CSS positions this button to improve the layout.
	 *
	 * @since 1.5.0
	 */
	public function append_media_upload_form() {

		?>
		<!-- Add from Media Library -->
		<a href="#" class="envira-media-library button" title="<?php esc_attr_e( 'Click Here to Insert from Other Image Sources', 'envira-gallery-lite' ); ?>" style="vertical-align: baseline;">
			<?php esc_html_e( 'Select Files from Other Sources', 'envira-gallery-lite' ); ?>
		</a>

		<!-- Progress Bar -->
		<div class="envira-progress-bar">
			<div class="envira-progress-bar-inner"></div>
			<div class="envira-progress-bar-status">
				<span class="uploading">
					<?php esc_html_e( 'Uploading Image', 'envira-gallery-lite' ); ?>
					<span class="current">1</span>
					<?php esc_html_e( 'of', 'envira-gallery-lite' ); ?>
					<span class="total">3</span>
				</span>

				<span class="done"><?php esc_html_e( 'All images uploaded.', 'envira-gallery-lite' ); ?></span>
			</div>
		</div>

		<div class="envira-progress-adding-images">
			<div class="envira-progress-status">
				<span class="spinner"></span><span class="adding_images"><?php esc_html_e( 'Adding items to gallery.', 'envira-gallery-lite' ); ?></span>
			</div>
		</div>
		<?php
	}

	/**
	 * Loads styles for our metaboxes.
	 *
	 * @since 1.0.0
	 *
	 * @return void Return early if not on the proper screen.
	 */
	public function styles() {

		// Get current screen.
		$screen = get_current_screen();

		// Bail if we're not on the Envira Post Type screen.
		if ( 'envira' !== $screen->post_type ) {
			return;
		}

		// Bail if we're not on an editing screen.
		if ( 'post' !== $screen->base ) {
			return;
		}

		// Load necessary metabox styles.
		wp_register_style( $this->base->plugin_slug . '-metabox-style', plugins_url( 'assets/css/metabox.css', $this->base->file ), [], $this->base->version );
		wp_enqueue_style( $this->base->plugin_slug . '-metabox-style' );

		// Fire a hook to load in custom metabox styles.
		do_action( 'envira_gallery_metabox_styles' );
	}

	/**
	 * Loads scripts for our metaboxes.
	 *
	 * @since 1.0.0
	 *
	 * @global int $id      The current post ID.
	 * @global object $post The current post object.
	 * @param string $hook  Screen hook.
	 *
	 * @return void         Return early if not on the proper screen.
	 */
	public function scripts( $hook ) {

		global $id, $post;

		// Get current screen.
		$screen = get_current_screen();

		// Bail if we're not on the Envira Post Type screen.
		if ( 'envira' !== $screen->post_type ) {
			return;
		}

		// Bail if we're not on an editing screen.
		if ( 'post' !== $screen->base ) {
			return;
		}

		// Set the post_id for localization.
		$post_id = isset( $post->ID ) ? $post->ID : (int) $id;

		// Sortables.
		wp_enqueue_script( 'jquery-ui-sortable' );

		// Image Uploader.
		wp_enqueue_media(
			[
				'post' => $post_id,
			]
		);
		add_filter( 'plupload_init', [ $this, 'plupload_init' ] );

		// Tabs.
		wp_register_script( $this->base->plugin_slug . '-tabs-script', plugins_url( 'assets/js/min/tabs-min.js', $this->base->file ), [ 'jquery' ], $this->base->version, true );
		wp_enqueue_script( $this->base->plugin_slug . '-tabs-script' );

		// Conditional Fields.
		wp_register_script( $this->base->plugin_slug . '-conditional-fields-script', plugins_url( 'assets/js/min/conditional-fields-min.js', $this->base->file ), [ 'jquery' ], $this->base->version, true );
		wp_enqueue_script( $this->base->plugin_slug . '-conditional-fields-script' );

		// Gallery / Album Selection.
		wp_enqueue_script( $this->base->plugin_slug . '-gallery-select-script', plugins_url( 'assets/js/gallery-select.js', $this->base->file ), [ 'jquery' ], $this->base->version, true );
		wp_localize_script(
			$this->base->plugin_slug . '-gallery-select-script',
			'envira_gallery_select',
			[
				'get_galleries_nonce' => wp_create_nonce( 'envira-gallery-editor-get-galleries' ),
				'modal_title'         => __( 'Insert', 'envira-gallery-lite' ),
				'insert_button_label' => __( 'Insert', 'envira-gallery-lite' ),
			]
		);

		// Metaboxes.
		wp_register_script( $this->base->plugin_slug . '-metabox-script', plugins_url( 'assets/js/min/metabox-min.js', $this->base->file ), [ 'jquery', 'plupload-handlers', 'quicktags', 'jquery-ui-sortable' ], $this->base->version, true );
		wp_enqueue_script( $this->base->plugin_slug . '-metabox-script' );
		wp_localize_script(
			$this->base->plugin_slug . '-metabox-script',
			'envira_gallery_metabox',
			[
				'ajax'                           => admin_url( 'admin-ajax.php' ),
				'change_nonce'                   => wp_create_nonce( 'envira-gallery-change-type' ),
				'id'                             => $post_id,
				'import'                         => __( 'You must select a file to import before continuing.', 'envira-gallery-lite' ),
				'insert_nonce'                   => wp_create_nonce( 'envira-gallery-insert-images' ),
				'inserting'                      => __( 'Inserting...', 'envira-gallery-lite' ),
				'library_search'                 => wp_create_nonce( 'envira-gallery-library-search' ),
				'load_gallery'                   => wp_create_nonce( 'envira-gallery-load-gallery' ),
				'load_image'                     => wp_create_nonce( 'envira-gallery-load-image' ),
				'move_media_nonce'               => wp_create_nonce( 'envira-gallery-move-media' ),
				'move_media_modal_title'         => __( 'Move Media to Gallery', 'envira-gallery-lite' ),
				'move_media_insert_button_label' => __( 'Move Media to Selected Gallery', 'envira-gallery-lite' ),
				'preview_nonce'                  => wp_create_nonce( 'envira-gallery-change-preview' ),
				'refresh_nonce'                  => wp_create_nonce( 'envira-gallery-refresh' ),
				'remove'                         => __( 'Are you sure you want to remove this image from the gallery?', 'envira-gallery-lite' ),
				'remove_multiple'                => __( 'Are you sure you want to remove these images from the gallery?', 'envira-gallery-lite' ),
				'remove_nonce'                   => wp_create_nonce( 'envira-gallery-remove-image' ),
				'save_nonce'                     => wp_create_nonce( 'envira-gallery-save-meta' ),
				'set_user_setting_nonce'         => wp_create_nonce( 'envira-gallery-set-user-setting' ),
				'saving'                         => __( 'Saving...', 'envira-gallery-lite' ),
				'saved'                          => __( 'Saved!', 'envira-gallery-lite' ),
				'sort'                           => wp_create_nonce( 'envira-gallery-sort' ),
				'uploader_files_computer'        => __( 'Select Files from Your Computer', 'envira-gallery-lite' ),
			]
		);

		// Link Search.
		wp_enqueue_script( 'wp-link' );

		// Add custom CSS for hiding specific things.
		add_action( 'admin_head', [ $this, 'meta_box_css' ] );

		// Fire a hook to load custom metabox scripts.
		do_action( 'envira_gallery_metabox_scripts' );
	}

	/**
	 * Remove plugins scripts that break Envira's admin.
	 *
	 * @access public
	 * @return void
	 */
	public function fix_plugin_js_conflicts() {

		global $id, $post;

		// Get current screen.

		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();

		// Bail if we're not on the Envira Post Type screen.
		if ( 'envira' !== $screen->post_type ) {
			return;
		}

		wp_dequeue_style( 'thrive-theme-options' );
		wp_dequeue_script( 'thrive-theme-options' );
		wp_dequeue_script( 'ngg-igw' );
		wp_dequeue_script( 'yoast_ga_admin' ); /* Yoast Clicky Plugin */
	}

	/**
	 * Amends the default Plupload parameters for initialising the Media Uploader, to ensure
	 * the uploaded image is attached to our Envira CPT
	 *
	 * @since 1.0.0
	 *
	 * @param array $params Params.
	 * @return array Params
	 */
	public function plupload_init( $params ) {

		global $post_ID;

		// Define the Envira Gallery Post ID, so Plupload attaches the uploaded images.
		// to this Envira Gallery.
		$params['multipart_params']['post_id'] = $post_ID;

		// Build an array of supported file types for Plupload.
		$supported_file_types = Envira_Gallery_Common::get_instance()->get_supported_filetypes();

		// Assign supported file types and return.
		$params['filters']['mime_types'] = $supported_file_types;

		// Return and apply a custom filter to our init data.
		$params = apply_filters( 'envira_gallery_plupload_init', $params, $post_ID );
		return $params;
	}

	/**
	 * Hides unnecessary meta box items on Envira post type screens.
	 *
	 * @since 1.0.0
	 */
	public function meta_box_css() {

		?>
		<style type="text/css">.misc-pub-section:not(.misc-pub-post-status) { display: none; }</style>
		<?php

		// Fire action for CSS on Envira post type screens.
		do_action( 'envira_gallery_admin_css' );
	}

	/**
	 * Creates metaboxes for handling and managing galleries.
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {

		global $post;

		// Check we're on an Envira Gallery.
		if ( 'envira' !== $post->post_type ) {
			return;
		}

		// Let's remove all of those dumb metaboxes from our post type screen to control the experience.
		$this->remove_all_the_metaboxes();

		// Add our metaboxes to Envira CPT.

		// Types Metabox.
		// Allows the user to upload images or choose an External Gallery Type.
		// We don't display this if the Gallery is a Dynamic or Default Gallery, as these settings don't apply.
		$type = $this->get_config( 'type', $this->get_config_default( 'type' ) );
		if ( ! in_array( $type, [ 'defaults', 'dynamic' ], true ) ) {
			add_meta_box( 'envira-gallery', __( 'Envira Gallery', 'envira-gallery-lite' ), [ $this, 'meta_box_gallery_callback' ], 'envira', 'normal', 'high' );
		}

		// Settings Metabox.
		add_meta_box( 'envira-gallery-settings', __( 'Envira Gallery Settings', 'envira-gallery-lite' ), [ $this, 'meta_box_callback' ], 'envira', 'normal', 'high' );

		// Display the Gallery Code metabox if we're editing an existing Gallery.
		if ( 'auto-draft' !== $post->post_status ) {
			add_meta_box( 'envira-gallery-code', __( 'Envira Gallery Code', 'envira-gallery-lite' ), [ $this, 'meta_box_gallery_code_callback' ], 'envira', 'side', 'default' );
		}

		// Output 'Select Files from Other Sources' button on the media uploader form.
		add_action( 'post-plupload-upload-ui', [ $this, 'append_media_upload_form' ], 1 );
		add_action( 'post-html-upload-ui', [ $this, 'append_media_upload_form' ], 1 );
	}

	/**
	 * Removes all the metaboxes except the ones I want on MY POST TYPE. RAGE.
	 *
	 * @since 1.0.0
	 *
	 * @global array $wp_meta_boxes Array of registered metaboxes.
	 * @return void
	 */
	public function remove_all_the_metaboxes() {

		global $wp_meta_boxes;

		// This is the post type you want to target. Adjust it to match yours.
		$post_type = 'envira';

		// These are the metabox IDs you want to pass over. They don't have to match exactly. preg_match will be run on them.
		$pass_over_defaults = [ 'submitdiv', 'envira' ];
		$pass_over          = apply_filters( 'envira_gallery_metabox_ids', $pass_over_defaults );

		// All the metabox contexts you want to check.
		$contexts_defaults = [ 'normal', 'advanced', 'side' ];
		$contexts          = apply_filters( 'envira_gallery_metabox_contexts', $contexts_defaults );

		// All the priorities you want to check.
		$priorities_defaults = [ 'high', 'core', 'default', 'low' ];
		$priorities          = apply_filters( 'envira_gallery_metabox_priorities', $priorities_defaults );

		// Loop through and target each context.
		foreach ( $contexts as $context ) {
			// Now loop through each priority and start the purging process.
			foreach ( $priorities as $priority ) {
				if ( isset( $wp_meta_boxes[ $post_type ][ $context ][ $priority ] ) ) {
					foreach ( (array) $wp_meta_boxes[ $post_type ][ $context ][ $priority ] as $id => $metabox_data ) {
						// If the metabox ID to pass over matches the ID given, remove it from the array and continue.
						if ( in_array( $id, $pass_over, true ) ) {
							unset( $pass_over[ $id ] );
							continue;
						}

						// Otherwise, loop through the pass_over IDs and if we have a match, continue.
						foreach ( $pass_over as $to_pass ) {
							if ( preg_match( '#^' . $id . '#i', $to_pass ) ) {
								continue;
							}
						}

						// If we reach this point, remove the metabox completely.
						unset( $wp_meta_boxes[ $post_type ][ $context ][ $priority ][ $id ] );
					}
				}
			}
		}
	}

	/**
	 * Adds an envira-gallery class to the form when adding or editing an Album,
	 * so our plugin's CSS and JS can target a specific element and its children.
	 *
	 * @since 1.5.0
	 *
	 * @param   WP_Post $post   WordPress Post.
	 */
	public function add_form_class( $post ) {

		// Check the Post is a Gallery.
		if ( 'envira' !== get_post_type( $post ) ) {
			return;
		}

		echo ' class="envira-gallery"';
	}

	/**
	 * Callback for displaying the Gallery Type section.
	 *
	 * @since 1.5.0
	 *
	 * @param object $post The current post object.
	 */
	public function meta_box_gallery_callback( $post ) {

		// Load view.
		$this->base->load_admin_partial(
			'metabox-gallery-type',
			[
				'post'     => $post,
				'types'    => $this->get_envira_types( $post ),
				'instance' => $this,
			]
		);
	}

	/**
	 * Callback for displaying the Gallery Settings section.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post The current post object.
	 */
	public function meta_box_callback( $post ) {

		// Keep security first.
		wp_nonce_field( 'envira-gallery', 'envira-gallery' );

		// Load view.
		$this->base->load_admin_partial(
			'metabox-gallery-settings',
			[
				'post' => $post,
				'tabs' => $this->get_envira_tab_nav(),
			]
		);
	}

	/**
	 * Callback for displaying the Gallery Code metabox.
	 *
	 * @since 1.5.0
	 *
	 * @param object $post The current post object.
	 */
	public function meta_box_gallery_code_callback( $post ) {

		// Load view.
		$this->base->load_admin_partial(
			'metabox-gallery-code',
			[
				'post'         => $post,
				'gallery_data' => get_post_meta( $post->ID, '_eg_gallery_data', true ),
			]
		);
	}

	/**
	 * Returns the types of galleries available.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post The current post object.
	 * @return array       Array of gallery types to choose.
	 */
	public function get_envira_types( $post ) {

		$types = [
			'default' => __( 'Default', 'envira-gallery-lite' ),
		];

		return apply_filters( 'envira_gallery_types', $types, $post );
	}

	/**
	 * Returns the tabs to be displayed in the settings metabox.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of tab information.
	 */
	public function get_envira_tab_nav() {

		$tabs = [
			'images'   => __( 'Gallery', 'envira-gallery-lite' ),
			'config'   => __( 'Config', 'envira-gallery-lite' ),
			'lightbox' => __( 'Lightbox', 'envira-gallery-lite' ),
			'mobile'   => __( 'Mobile', 'envira-gallery-lite' ),
		];

		$tabs = apply_filters( 'envira_gallery_tab_nav', $tabs );

		// "Misc" tab is required.
		$tabs['misc'] = __( 'Misc', 'envira-gallery-lite' );

		return $tabs;
	}

	/**
	 * Callback for displaying the settings UI for the Gallery tab.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post The current post object.
	 */
	public function images_tab( $post ) {

		// Output the display based on the type of slider being created.
		echo '<div id="envira-gallery-main" class="envira-clear">';

		// Allow Addons to display a WordPress-style notification message.
		echo apply_filters( 'envira_gallery_images_tab_notice', '', $post ); //@codingStandardsIgnoreLine

		// Output the tab panel for the Gallery Type.
		$this->images_display( $this->get_config( 'type', $this->get_config_default( 'type' ) ), $post );

		echo '</div>
              <div class="spinner"></div>';
	}

	/**
	 * Determines the Images tab display based on the type of gallery selected.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type The type of display to output.
	 * @param object $post The current post object.
	 */
	public function images_display( $type = 'default', $post = false ) {

		// Output a unique hidden field for settings save testing for each type of slider.
		echo '<input type="hidden" name="_envira_gallery[type_' . esc_attr( $type ) . ']" value="1" />';

		// Output the display based on the type of slider available.
		switch ( $type ) {
			case 'default':
				$this->do_default_display( $post );
				break;
			default:
				do_action( 'envira_gallery_display_' . $type, $post );
				break;
		}
	}

	/**
	 * Determines the Preview metabox display based on the type of gallery selected.
	 *
	 * @since 1.5.0
	 *
	 * @param string $type The type of display to output.
	 * @param object $data Gallery Data.
	 */
	public function preview_display( $type = 'default', $data = false ) {

		// Output the display based on the type of slider available.
		switch ( $type ) {
			case 'default':
				// Don't preview anything.
				break;
			default:
				do_action( 'envira_gallery_preview_' . $type, $data );
				break;
		}
	}

	/**
	 * Callback for displaying the default gallery UI.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post The current post object.
	 */
	public function do_default_display( $post ) {

		// Prepare output data.
		$gallery_data = get_post_meta( $post->ID, '_eg_gallery_data', true );

		// Determine whether to use the list or grid layout, depending on the user's setting.
		$layout = get_user_setting( 'envira_gallery_image_view', 'grid' );

		?>

		<!-- Title and Help -->
		<p class="envira-intro">
			<?php esc_html_e( 'Currently in your Gallery', 'envira-gallery-lite' ); ?>
			<small>
				<?php esc_html_e( 'Need some help?', 'envira-gallery-lite' ); ?>
				<a href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/docs/', 'adminpage', 'readthedocumentation' ) ); ?>" class="envira-doc" target="_blank">
					<?php esc_html_e( 'Read the Documentation', 'envira-gallery-lite' ); ?>
				</a>
				<?php esc_html_e( 'or', 'envira-gallery-lite' ); ?>
				<a href="https://www.youtube.com/embed/F9_wOefuBaw?autoplay=1&amp;rel=0" class="envira-video" target="_blank">
					<?php esc_html_e( 'Watch a Video', 'envira-gallery-lite' ); ?>
				</a>
			</small>
		</p>

		<?php do_action( 'envira_gallery_do_default_display', $post ); ?>

		<ul id="envira-gallery-output" class="envira-gallery-images-output <?php echo esc_attr( $layout ); ?>">
			<?php
			if ( ! empty( $gallery_data['gallery'] ) ) {
				foreach ( $gallery_data['gallery'] as $id => $data ) {
					echo $this->get_gallery_item( $id, $data, $post->ID ); //@codingStandardsIgnoreLine
				}
			}
			?>
		</ul>

		<?php
		// Output an upgrade notice.
		Envira_Gallery_Notice_Admin::get_instance()->display_inline_notice(
			'envira_gallery_images_tab',
			__( 'Want to make your gallery workflow and presentation even better?', 'envira-gallery-lite' ),
			'<p>By upgrading to Envira Pro, you can get access to numerous other features, including:</p><div class="two-column-list">
            <ul>
                <li><a target="_blank" href="' . Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagegallery', 'albums' ) . '">Albums</a></li>
                <li><a target="_blank" href="' . Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagegallery', 'gallerythemesandlayouts' ) . '">Gallery Themes/Layouts</a></li>
                <li><a target="_blank" href="' . Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagegallery', 'imageproofing' ) . '">Image Proofing</a></li>
            </ul>
            <ul>
                <li><a target="_blank" href="' . Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagegallery', 'eCommerce' ) . '">eCommerce</a></li>
                <li>Dedicated customer support and so much more!</li>
            </ul>
        </div><p class="no-margin-top"><strong>Bonus:</strong> Envira Lite users get a discount code for <span class="envira-green">50% off</span> regular price.</p>',
			'warning',
			__( 'Click here to Upgrade', 'envira-gallery-lite' ),
			Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( false, 'adminpagegallery', 'clickheretoupgradebutton' ),
			false
		);
	}

	/**
	 * Callback for displaying the settings UI for the Configuration tab.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post The current post object.
	 */
	public function config_tab( $post ) {

		?>
		<div id="envira-config">
			<!-- Title and Help -->
			<p class="envira-intro">
				<?php esc_html_e( 'Gallery Settings', 'envira-gallery-lite' ); ?>
				<small>
					<?php esc_html_e( 'The settings below adjust the basic configuration options for the gallery.', 'envira-gallery-lite' ); ?><br />
					<?php esc_html_e( 'Need some help?', 'envira-gallery-lite' ); ?>
					<a href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/docs/', 'adminpageconfig', 'readthedocumentation' ) ); ?>" class="envira-doc" target="_blank">
						<?php esc_html_e( 'Read the Documentation', 'envira-gallery-lite' ); ?>
					</a>
					or
					<a href="https://www.youtube.com/embed/F9_wOefuBaw?autoplay=1&amp;rel=0" class="envira-video" target="_blank">
						<?php esc_html_e( 'Watch a Video', 'envira-gallery-lite' ); ?>
					</a>
				</small>
			</p>
			<table class="form-table" style="margin-bottom: 0;">
				<tbody>
					<tr id="envira-config-columns-box">
						<th scope="row">
							<label for="envira-config-columns"><?php esc_html_e( 'Gallery Layout', 'envira-gallery-lite' ); ?></label>
						</th>
						<td>
							<div class="gallery-layout-container">
								<div id="upsell-prompt" class="upsell-prompt">
									<a href="javascript:void(0);" id="close-upsell" class="close-button">&times;</a>
									<div class="slogan-text-left">
										<h3><?php esc_html_e( 'Upgrade to Pro!', 'envira-gallery-lite' ); ?></h3>
										<p>
											<?php
											echo wp_kses_post(
												__( 'Access <span class="module-number">10+</span> creative layouts. <span class="offer">Get 50%</span> off regular price, automatically applied at checkout', 'envira-gallery-lite' )
											);
											?>
										</p>
										<a href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpageconfig', 'upgradetopro' ) ); ?>" class="button button-primary" target="_blank"><?php esc_html_e( 'Unlock all Layouts', 'envira-gallery-lite' ); ?></a>
									</div>
								</div>
								<ul class="thumbnails image_picker_selector">
									<li>
										<div class="thumbnail selected">
											<img class="image_picker_image" src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/automatic-layout.png' ); ?>">
											<p><?php esc_html_e( 'Automatic', 'envira-gallery-lite' ); ?></p>
										</div>
									</li>
									<li>
										<div class="thumbnail upgrade-to-pro">
											<img class="image_picker_image" src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/mason-layout.png' ); ?>">
											<p><?php esc_html_e( 'Mason', 'envira-gallery-lite' ); ?></p>
										</div>
									</li>
									<li>
										<div class="thumbnail upgrade-to-pro">
											<img class="image_picker_image" src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/grid-layout.png' ); ?>">
											<p><?php esc_html_e( 'Grid', 'envira-gallery-lite' ); ?></p>
										</div>
									</li>
									<li>
										<div class="thumbnail upgrade-to-pro">
											<img class="image_picker_image" src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/square-layout.png' ); ?>">
											<p><?php esc_html_e( 'Square', 'envira-gallery-lite' ); ?></p>
										</div>
									</li>
									<li>
										<div class="thumbnail upgrade-to-pro">
											<img class="image_picker_image" src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/blogroll-layout.png' ); ?>">
											<p><?php esc_html_e( 'Blogroll', 'envira-gallery-lite' ); ?></p>
										</div>
									</li>
									<li>
										<div class="thumbnail upgrade-to-pro">
											<img class="image_picker_image" src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/bnb-layout.png' ); ?>">
											<p><?php esc_html_e( 'BnB', 'envira-gallery-lite' ); ?></p>
										</div>
									</li>
									<li>
										<div class="thumbnail upgrade-to-pro">
											<img class="image_picker_image" src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/polaroid-layout.png' ); ?>">
											<p><?php esc_html_e( 'Polaroid', 'envira-gallery-lite' ); ?></p>
										</div>
									</li>
									<li>
										<div class="thumbnail upgrade-to-pro">
											<img class="image_picker_image" src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/creative-layout.png' ); ?>">
											<p><?php esc_html_e( 'Creative', 'envira-gallery-lite' ); ?></p>
										</div>
									</li>
									<li>
										<div class="thumbnail upgrade-to-pro">
											<img class="image_picker_image" src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/vertical-focus-layout.png' ); ?>">
											<p><?php esc_html_e( 'Vertical Focus', 'envira-gallery-lite' ); ?></p>
										</div>
									</li>
								</ul>
							</div>
						</td>
					</tr>
					<tr id="envira-config-columns-box">
						<th scope="row">
							<label for="envira-config-columns"><?php esc_html_e( 'Number of Gallery Columns', 'envira-gallery-lite' ); ?></label>
						</th>
						<td>
							<select id="envira-config-columns" name="_envira_gallery[columns]">
								<?php foreach ( (array) $this->get_columns() as $i => $data ) : ?>
									<option value="<?php echo esc_attr( $data['value'] ); ?>"<?php selected( $data['value'], $this->get_config( 'columns', $this->get_config_default( 'columns' ) ) ); ?>><?php echo esc_html( $data['name'] ); ?></option>
								<?php endforeach; ?>
							</select>
							<p class="description"><?php esc_html_e( 'Determines the number of columns in the gallery. Automatic will attempt to fill each row as much as possible before moving on to the next row.', 'envira-gallery-lite' ); ?></p>
						</td>
					</tr>

					<?php

					if ( ! isset( $post ) || 'auto-draft' === $post->post_status ) {
						// make the lazy loading checkbox "checked", otherwise if this is a previous post don't force it.
						?>
					<tr id="envira-config-lazy-loading-box">
						<th scope="row">
							<label for="envira-config-lazy-loading"><?php esc_html_e( 'Enable Lazy Loading?', 'envira-gallery-lite' ); ?></label>
						</th>
						<td>
						<label class="envira-toggle">
							<input id="envira-config-lazy-loading" type="checkbox" class="envira-checkbox" name="_envira_gallery[lazy_loading]" value="<?php echo esc_attr( $this->get_config( 'lazy_loading', $this->get_config_default( 'lazy_loading' ) ) ); ?>" <?php checked( $this->get_config( 'lazy_loading', $this->get_config_default( 'lazy_loading' ) ), 1 ); ?> />
							<span class="envira-switch"></span>
							<span class="description"><?php esc_html_e( 'Enables or disables lazy loading, which helps with performance by loading thumbnails only when they are visible. See our documentation for more information.', 'envira-gallery-lite' ); ?></span>
						</label>
						</td>
					</tr>

					<?php } else { ?>

					<tr id="envira-config-lazy-loading-box">
						<th scope="row">
							<label for="envira-config-lazy-loading"><?php esc_html_e( 'Enable Lazy Loading?', 'envira-gallery-lite' ); ?></label>
						</th>
						<td>
						<label class="envira-toggle">
							<input id="envira-config-lazy-loading" type="checkbox" class="envira-checkbox" name="_envira_gallery[lazy_loading]" value="<?php echo esc_attr( $this->get_config( 'lazy_loading', $this->get_config_default( 'lazy_loading' ) ) ); ?>" <?php checked( $this->get_config( 'lazy_loading' ), 1 ); ?> />
							<span class="envira-switch"></span>
							<span class="description"><?php esc_html_e( 'Enables or disables lazy loading, which helps with performance by loading thumbnails only when they are visible. See our documentation for more information.', 'envira-gallery-lite' ); ?></span>
						</label>
						</td>
					</tr>

					<?php } ?>

					<tr id="envira-config-lazy-loading-delay">
						<th scope="row">
							<label for="envira-config-lazy-loading-delay"><?php esc_html_e( 'Lazy Loading Delay', 'envira-gallery-lite' ); ?></label>
						</th>
							<td>
								<input id="envira-config-lazy-loading-delay" type="number" name="_envira_gallery[lazy_loading_delay]" value="<?php echo esc_attr( $this->get_config( 'lazy_loading_delay', $this->get_config_default( 'lazy_loading_delay' ) ) ); ?>" /> <span class="envira-unit"><?php esc_html_e( 'milliseconds', 'envira-gallery-lite' ); ?></span>
								<p class="description"><?php esc_html_e( 'Set a delay when new images are loaded', 'envira-gallery-lite' ); ?></p>
							</td>
					</tr>
				</tbody>
			</table>
			<?php // New Automatic Layout / Justified Layout Options. ?>
			<div id="envira-config-justified-settings-box">
				<table class="form-table" style="margin-bottom: 0;">
					<tbody>
						<tr id="envira-config-justified-row-height">
							<th scope="row">
								<label for="envira-config-justified-row-height"><?php esc_html_e( 'Automatic Layout: Row Height', 'envira-gallery-lite' ); ?></label>
							</th>
							<td>
								<input id="envira-config-justified-row-height" type="number" name="_envira_gallery[justified_row_height]" value="<?php echo esc_attr( $this->get_config( 'justified_row_height', $this->get_config_default( 'justified_row_height' ) ) ); ?>" /> <span class="envira-unit"><?php esc_html_e( 'px', 'envira-gallery-lite' ); ?></span>
								<p class="description"><?php esc_html_e( 'Determines how high (in pixels) each row will be. 150px is default. ', 'envira-gallery-lite' ); ?></p>
							</td>
						</tr>
						<tr id="envira-config-justified-margins">
							<th scope="row">
								<label for="envira-config-justified-margins"><?php esc_html_e( 'Automatic Layout: Margins', 'envira-gallery-lite' ); ?></label>
							</th>
							<td>
								<input id="envira-config-justified-margins" type="number" name="_envira_gallery[justified_margins]" value="<?php echo esc_attr( $this->get_config( 'justified_margins', $this->get_config_default( 'justified_margins' ) ) ); ?>" /> <span class="envira-unit"><?php esc_html_e( 'px', 'envira-gallery-lite' ); ?></span>
								<p class="description"><?php esc_html_e( 'Sets the space between the images (defaults to 1)', 'envira-gallery-lite' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div id="envira-config-standard-settings-box">
				<table class="form-table">
					<tbody>

						<tr id="envira-config-gallery-theme-box">
							<th scope="row">
								<label for="envira-config-gallery-theme"><?php esc_html_e( 'Gallery Theme', 'envira-gallery-lite' ); ?></label>
							</th>
							<td>
								<select id="envira-config-gallery-theme" name="_envira_gallery[gallery_theme]">
									<?php foreach ( (array) $this->get_gallery_themes() as $i => $data ) : ?>
										<option value="<?php echo esc_attr( $data['value'] ); ?>"<?php selected( $data['value'], $this->get_config( 'gallery_theme', $this->get_config_default( 'gallery_theme' ) ) ); ?>><?php echo esc_html( $data['name'] ); ?></option>
									<?php endforeach; ?>
								</select>
								<p class="description"><?php esc_html_e( 'Sets the theme for the gallery display.', 'envira-gallery-lite' ); ?></p>
							</td>
						</tr>
						<tr id="envira-config-gutter-box">
							<th scope="row">
								<label for="envira-config-gutter"><?php esc_html_e( 'Column Gutter Width', 'envira-gallery-lite' ); ?></label>
							</th>
							<td>
								<input id="envira-config-gutter" type="number" name="_envira_gallery[gutter]" value="<?php echo esc_attr( $this->get_config( 'gutter', $this->get_config_default( 'gutter' ) ) ); ?>" /> <span class="envira-unit"><?php esc_html_e( 'px', 'envira-gallery-lite' ); ?></span>
								<p class="description"><?php esc_html_e( 'Sets the space between the columns (defaults to 10).', 'envira-gallery-lite' ); ?></p>
							</td>
						</tr>
						<tr id="envira-config-margin-box">
							<th scope="row">
								<label for="envira-config-margin"><?php esc_html_e( 'Margin Below Each Image', 'envira-gallery-lite' ); ?></label>
							</th>
							<td>
								<input id="envira-config-margin" type="number" name="_envira_gallery[margin]" value="<?php echo esc_attr( $this->get_config( 'margin', $this->get_config_default( 'margin' ) ) ); ?>" /> <span class="envira-unit"><?php esc_html_e( 'px', 'envira-gallery-lite' ); ?></span>
								<p class="description"><?php esc_html_e( 'Sets the space below each item in the gallery.', 'envira-gallery-lite' ); ?></p>
							</td>
						</tr>

						<?php do_action( 'envira_gallery_config_box', $post ); ?>
					</tbody>
				</table>
			</div>


			<div id="envira-image-settings-box">
				<table class="form-table">
					<tbody>
						<!-- Dimensions -->
						<tr id="envira-config-image-size-box">
							<th scope="row">
								<label for="envira-config-image-size"><?php esc_html_e( 'Image Size', 'envira-gallery-lite' ); ?></label>
							</th>
							<td>
								<select id="envira-config-image-size" name="_envira_gallery[image_size]">
									<?php
									foreach ( (array) $this->get_image_sizes() as $i => $data ) {
										?>
										<option value="<?php echo esc_attr( $data['value'] ); ?>"<?php selected( $data['value'], $this->get_config( 'image_size', $this->get_config_default( 'image_size' ) ) ); ?>><?php echo esc_html( $data['name'] ); ?></option>
										<?php
									}
									?>
								</select>
								<p class="description"><?php esc_html_e( 'Define the maximum image size for the Gallery view. Default will use the below Image Dimensions; Random will allow you to choose one or more WordPress image sizes, which will be used for the gallery output.', 'envira-gallery-lite' ); ?></p>
							</td>
						</tr>
						<tr id="envira-config-crop-size-box">
							<th scope="row">
								<label for="envira-config-crop-width"><?php esc_html_e( 'Image Dimensions', 'envira-gallery-lite' ); ?></label>
							</th>
							<td>
								<input id="envira-config-crop-width" type="number" name="_envira_gallery[crop_width]" value="<?php echo esc_attr( $this->get_config( 'crop_width', $this->get_config_default( 'crop_width' ) ) ); ?>" /> &#215; <input id="envira-config-crop-height" type="number" name="_envira_gallery[crop_height]" value="<?php echo esc_attr( $this->get_config( 'crop_height', $this->get_config_default( 'crop_height' ) ) ); ?>" /> <span class="envira-unit"><?php esc_html_e( 'px', 'envira-gallery-lite' ); ?></span>
								<p class="description"><?php esc_html_e( 'You should adjust these dimensions based on the number of columns in your gallery. This does not affect the full size lightbox images.', 'envira-gallery-lite' ); ?></p>
							</td>
						</tr>
						<tr id="envira-config-crop-box">
							<th scope="row">
								<label for="envira-config-crop"><?php esc_html_e( 'Crop Images?', 'envira-gallery-lite' ); ?></label>
							</th>
							<td>
							<label class="envira-toggle">
								<input id="envira-config-crop" type="checkbox" class="envira-checkbox" name="_envira_gallery[crop]" value="<?php echo esc_attr( $this->get_config( 'crop', $this->get_config_default( 'crop' ) ) ); ?>" <?php checked( $this->get_config( 'crop', $this->get_config_default( 'crop' ) ), 1 ); ?> />
								<span class="envira-switch"></span>
								<span class="description"><?php esc_html_e( 'If enabled, forces images to exactly match the sizes defined above for Image Dimensions and Mobile Dimensions.', 'envira-gallery-lite' ); ?></span>
								<span class="description"><?php esc_html_e( 'If disabled, images will be resized to maintain their aspect ratio.', 'envira-gallery-lite' ); ?></span>
							</label>
							</td>
						</tr>
					</tbody>
				</table>

		</div>
		</div>
		<?php

		// Output an upgrade notice.
		Envira_Gallery_Notice_Admin::get_instance()->display_inline_notice(
			'envira_gallery_images_tab',
			'',
			'<h2 style="margin: -10px 0 0 0; font-weight: 600; font-size: 21px; padding: 0;">Improve Your Galleries With Our Premium Addons:</h2>
            <div class="two-column-list">
            <ul>
                <li><a target="_blank" href="' . esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpageconfig', 'customgallerythemes' ) ) . '">Custom Gallery Themes</a></li>
                <li><a target="_blank" href="' . esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpageconfig', 'mobileoptimizedgalleries' ) ) . '">Mobile Optimized Galleries</a></li>
                <li><a target="_blank" href="' . esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpageconfig', 'adobelightroomintegration' ) ) . '">Adobe Lightroom Integration</a></li>
            </ul>
            <ul>
                <li><a target="_blank" href="' . esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpageconfig', 'woocommerceintegration' ) ) . '">Woocommerce Integration</a></li>
                <li><a target="_blank" href="' . esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpageconfig', 'imageprotection' ) ) . '">Image Protection</a></li>
                <li><a target="_blank" href="' . esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite/', 'adminpageconfig', 'prioritytechnicalsupport' ) ) . '">Priority Technical Support</a></li>
            </ul>
            </div>
            <p class="no-margin-top">Join 2,500,000 + Professionals Who Are Using Envira Gallery to Create Beautiful Photo and Video Galleries.</p>',
			'warning',
			__( 'Click here to Upgrade Today', 'envira-gallery-lite' ),
			esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( false, 'adminpageconfig', 'clickheretoupgradetodaybutton' ) ),
			false
		);
	}

	/**
	 * Callback for displaying the settings UI for the Lightbox tab.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post The current post object.
	 */
	public function lightbox_tab( $post ) {

		?>
		<div id="envira-lightbox">
			<p class="envira-intro">
				<?php esc_html_e( 'Lightbox Settings', 'envira-gallery-lite' ); ?>
				<small>
					<?php esc_html_e( 'The settings below adjust the lightbox output.', 'envira-gallery-lite' ); ?>
					<br />
					<?php esc_html_e( 'Need some help?', 'envira-gallery-lite' ); ?>
					<a href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/docs/', 'adminpagelightbox', 'readthedocumentation' ) ); ?>" class="envira-doc" target="_blank">
						<?php esc_html_e( 'Read the Documentation', 'envira-gallery-lite' ); ?>
					</a>
					or
					<a href="https://www.youtube.com/embed/4jHG3LOmV-c?autoplay=1&amp;rel=0" class="envira-video" target="_blank">
						<?php esc_html_e( 'Watch a Video', 'envira-gallery-lite' ); ?>
					</a>
				</small>
			</p>

			<table class="form-table no-margin">
				<tbody>
					<tr id="envira-config-lightbox-enabled-box">
						<th scope="row">
							<label for="envira-config-lightbox-enabled"><?php esc_html_e( 'Enable Lightbox?', 'envira-gallery-lite' ); ?></label>
						</th>
						<td>
							<label class="envira-toggle">
								<input id="envira-config-lightbox-enabled" type="checkbox" class="envira-checkbox" name="_envira_gallery[lightbox_enabled]" value="<?php echo esc_attr( $this->get_config( 'lightbox_enabled', $this->get_config_default( 'lightbox_enabled' ) ) ); ?>" <?php checked( $this->get_config( 'lightbox_enabled', $this->get_config_default( 'lightbox_enabled' ) ), 1 ); ?> />
								<span class="envira-switch"></span>
								<span class="description"><?php esc_html_e( 'Enables or disables the gallery lightbox.', 'envira-gallery-lite' ); ?></span>
							</label>
						</td>
					</tr>
					<tr id="envira-config-lightbox-enabled-link">
						<th scope="row">
							<label for="envira-config-lightbox-enable-links"><?php esc_html_e( 'Enable Links?', 'envira-gallery' ); ?></label>
						</th>
						<td>
							<label class="envira-toggle">
								<input id="envira-config-lightbox-enable-links" type="checkbox" class="envira-checkbox" name="_envira_gallery[gallery_link_enabled]" value="<?php echo esc_html( $this->get_config_default( 'gallery_link_enabled' ) ); ?>" <?php checked( $this->get_config( 'gallery_link_enabled', $this->get_config_default( 'gallery_link_enabled' ) ), 1 ); ?> />
								<span class="envira-switch"></span>
								<span class="description"><?php esc_html_e( 'Enables or disables links only when the gallery lightbox is disabled.', 'envira-gallery' ); ?></span>
							</label>
						</td>
					</tr>
				</tbody>
			</table>

			<div id="envira-lightbox-settings">
				<table class="form-table">
					<tbody>
						<tr id="envira-config-lightbox-theme-box">
							<th scope="row">
								<label for="envira-config-lightbox-theme"><?php esc_html_e( 'Gallery Lightbox Theme', 'envira-gallery-lite' ); ?></label>
							</th>
							<td>
								<select id="envira-config-lightbox-theme" name="_envira_gallery[lightbox_theme]">
									<?php foreach ( (array) $this->get_lightbox_themes() as $i => $data ) : ?>
										<option value="<?php echo esc_attr( $data['value'] ); ?>"<?php selected( $data['value'], $this->get_config( 'lightbox_theme', $this->get_config_default( 'lightbox_theme' ) ) ); ?>><?php echo esc_html( $data['name'] ); ?></option>
									<?php endforeach; ?>
								</select>
								<p class="description"><?php esc_html_e( 'Sets the theme for the gallery lightbox display.', 'envira-gallery-lite' ); ?></p>
							</td>
						</tr>
						<tr id="envira-config-lightbox-image-size-box">
							<th scope="row">
								<label for="envira-config-lightbox-image-size"><?php esc_html_e( 'Image Size', 'envira-gallery-lite' ); ?></label>
							</th>
							<td>
								<select id="envira-config-lightbox-image-size" name="_envira_gallery[lightbox_image_size]">
									<?php foreach ( (array) $this->get_image_sizes() as $i => $data ) : ?>
										<option value="<?php echo esc_attr( $data['value'] ); ?>" <?php selected( $data['value'], $this->get_config( 'lightbox_image_size', $this->get_config_default( 'lightbox_image_size' ) ) ); ?>><?php echo esc_html( $data['name'] ); ?></option>
									<?php endforeach; ?>
								</select><br>
								<p class="description"><?php esc_html_e( 'Define the maximum image size for the Lightbox view. Default will display the original, full size image.', 'envira-gallery-lite' ); ?></p>
							</td>
						</tr>
						<tr id="envira-config-lightbox-title-display-box">
							<th scope="row">
								<label for="envira-config-lightbox-title-display"><?php esc_html_e( 'Caption Position', 'envira-gallery-lite' ); ?></label>
							</th>
							<td>
								<select id="envira-config-lightbox-title-display" name="_envira_gallery[title_display]">
									<?php foreach ( (array) $this->get_title_displays() as $i => $data ) : ?>
										<option value="<?php echo esc_attr( $data['value'] ); ?>"<?php selected( $data['value'], $this->get_config( 'title_display', $this->get_config_default( 'title_display' ) ) ); ?>><?php echo esc_html( $data['name'] ); ?></option>
									<?php endforeach; ?>
								</select>
								<p class="description"><?php esc_html_e( 'Sets the display of the lightbox image\'s caption.', 'envira-gallery-lite' ); ?></p>
							</td>
						</tr>

						<?php do_action( 'envira_gallery_lightbox_box', $post ); ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php

		// Output an upgrade notice.
		Envira_Gallery_Notice_Admin::get_instance()->display_inline_notice(
			'envira_gallery_images_tab',
			__( 'Want even more fine tuned control over your lightbox display?', 'envira-gallery-lite' ),
			'<p>By upgrading to Envira Gallery Pro, you can get access to numerous other Lightbox features, including:</p><div class="two-column-list">
            <ul>
                <li><a target="_blank" href="' . Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagelightbox', 'addgallerylightboxthemes' ) . '">All Gallery Lightbox Themes</a></li>
                <li><a target="_blank" href="' . Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagelightbox', 'customlightboxtitles' ) . '">Custom Lightbox titles</a></li>
                <li><a target="_blank" href="' . Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagelightbox', 'enabledisablelightboxcontrols', '#!enviragallery381249-497120' ) . '">Enable/disable Lightbox controls (arrow, keyboard and mouse wheel navigation)</a></li>
                <li><a target="_blank" href="' . Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagelightbox', 'customlightboxtransitioneffects' ) . '">Custom Lightbox transition effects</a></li>
                <li><a target="_blank" href="' . Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagelightbox', 'nativefullscreensupport' ) . '">Native fullscreen support</a></li>
            </ul>
            <ul>
                <li><a target="_blank" href="' . Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagelightbox', 'gallerydeeplinking' ) . '">Gallery deeplinking</a></li>
                <li><a target="_blank" href="' . Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagelightbox', 'imageprotection' ) . '">Image protection</a></li>
                <li><a target="_blank" href="' . Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagelightbox', 'lightboxsupersizeeffects' ) . '">Lightbox supersize effects</a></li>
                <li><a target="_blank" href="' . Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagelightbox', 'lightboxslideshows' ) . '">Lightbox slideshows</a> and so much more!</li>
            </ul>
        </div><p class="no-margin-top"><strong>Bonus:</strong> Envira Lite users get a discount code for <span class="envira-green">50% off</span> regular price.</p>',
			'warning',
			__( 'Click here to Upgrade', 'envira-gallery-lite' ),
			Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( false, 'adminpagelightbox', 'clickheretoupgradebutton' ),
			false
		);
	}

	/**
	 * Callback for displaying the settings UI for the Misc tab.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post The current post object.
	 */
	public function misc_tab( $post ) {

		?>
		<div id="envira-misc">
			<p class="envira-intro">
				<?php esc_html_e( 'Miscellaneous Settings', 'envira-gallery-lite' ); ?>
				<small>
					<?php esc_html_e( 'The settings below adjust miscellaneous options for the Gallery.', 'envira-gallery-lite' ); ?>
					<br />
					<?php esc_html_e( 'Need some help?', 'envira-gallery-lite' ); ?>
					<a href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'http://enviragallery.com/docs/', 'adminpagemisc', 'readthedocumentation' ) ); ?>" class="envira-doc" target="_blank">
						<?php esc_html_e( 'Read the Documentation', 'envira-gallery-lite' ); ?>
					</a>
					or
					<a href="https://www.youtube.com/embed/4jHG3LOmV-c?autoplay=1&amp;rel=0" class="envira-video" target="_blank">
						<?php esc_html_e( 'Watch a Video', 'envira-gallery-lite' ); ?>
					</a>
				</small>
			</p>
			<table class="form-table">
				<tbody>
					<tr id="envira-config-title-box">
						<th scope="row">
							<label for="envira-config-title"><?php esc_html_e( 'Gallery Title', 'envira-gallery-lite' ); ?></label>
						</th>
						<td>
							<input id="envira-config-title" type="text" name="_envira_gallery[title]" value="<?php echo esc_attr( $this->get_config( 'title', $this->get_config_default( 'title' ) ) ); ?>" />
							<p class="description"><?php esc_html_e( 'Internal gallery title for identification in the admin.', 'envira-gallery-lite' ); ?></p>
						</td>
					</tr>
					<tr id="envira-config-slug-box">
						<th scope="row">
							<label for="envira-config-slug"><?php esc_html_e( 'Gallery Slug', 'envira-gallery-lite' ); ?></label>
						</th>
						<td>
							<input id="envira-config-slug" type="text" name="_envira_gallery[slug]" value="<?php echo esc_attr( $this->get_config( 'slug', $this->get_config_default( 'slug' ) ) ); ?>" />
							<p class="description">
							<?php
							echo wp_kses(
								__( '<strong>Unique</strong> internal gallery slug for identification and advanced gallery queries.', 'envira-gallery-lite' ),
								[ 'strong' => [] ]
							);
							?>
							</p>
						</td>
					</tr>
					<tr id="envira-config-classes-box">
						<th scope="row">
							<label for="envira-config-classes"><?php esc_html_e( 'Custom Gallery Classes', 'envira-gallery-lite' ); ?></label>
						</th>
						<td>
							<?php
							$classes        = $this->get_config( 'classes', $this->get_config_default( 'classes' ) );
							$classes_output = '';
							// Check if the classes are an array and sanitize each class.
							if ( is_array( $classes ) ) {
								// Sanitize each class using wp_unslash before sanitization.
								$sanitized_classes = array_map(
									function ( $custom_class ) {
										return sanitize_html_class( wp_unslash( $custom_class ) );
									},
									$classes
								);

								$classes_output = implode( "\n", $sanitized_classes ); // Join the sanitized classes.
							} else {
								// If not an array, apply wp_unslash and sanitize directly.
								$classes_output = sanitize_html_class( wp_unslash( $classes ) );
							}
							?>
							<textarea id="envira-config-classes" rows="5" cols="75" name="_envira_gallery[classes]" placeholder="<?php esc_html_e( 'Enter custom gallery CSS classes here, one per line.', 'envira-gallery' ); ?>"><?php echo esc_html( $classes_output ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Adds custom CSS classes to this gallery. Enter one class per line.', 'envira-gallery' ); ?></p>
						</td>
					</tr>
					<tr id="envira-config-rtl-box">
						<th scope="row">
							<label for="envira-config-rtl"><?php esc_html_e( 'Enable RTL Support?', 'envira-gallery-lite' ); ?></label>
						</th>
						<td>
							<label class="envira-toggle">
								<input id="envira-config-rtl" type="checkbox" class="envira-checkbox" name="_envira_gallery[rtl]" value="<?php echo esc_attr( $this->get_config( 'rtl', $this->get_config_default( 'rtl' ) ) ); ?>" <?php checked( $this->get_config( 'rtl', $this->get_config_default( 'rtl' ) ), 1 ); ?> />
								<span class="envira-switch"></span>
								<span class="description"><?php esc_html_e( 'Enables or disables RTL support in Envira for right-to-left languages.', 'envira-gallery-lite' ); ?></span>
							</label>
						</td>
					</tr>
					<?php do_action( 'envira_gallery_misc_box', $post ); ?>

				</tbody>
			</table>
		</div>
		<?php

		// Output an upgrade notice.
		Envira_Gallery_Notice_Admin::get_instance()->display_inline_notice(
			'envira_gallery_images_tab',
			__( 'Want to take your galleries further?', 'envira-gallery-lite' ),
			'<p>By upgrading to Envira Gallery Pro, you can get access to numerous other features, including:</p>
            <div class="two-column-list">
            <ul class="no-margin-top">
                <li><a target="_blank" href="' . Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagemisc', 'fullyintegratedimportexportmoduleforgalleries' ) . '">Fully-integrated import/export module for your galleries</a></li>
            </ul>
            <ul class="no-margin-top">
                <li><a target="_blank" href="' . Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagemisc', 'customcsscontrols' ) . '">Custom CSS controls</a> for each gallery and so much more!</li>
            </ul>
        </div><p class="no-margin-top"><strong>Bonus:</strong> Envira Lite users get a discount code for <span class="envira-green">50% off</span> regular price.</p>',
			'warning',
			__( 'Click here to Upgrade', 'envira-gallery-lite' ),
			Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( false, 'adminpagemisc', 'clickheretoupgradebutton' ),
			false
		);
	}

	/**
	 * Adds Envira Gallery Lite-specific tabs
	 *
	 * @since 1.5.0
	 *
	 * @param   array $tabs   Tabs.
	 * @return  array           Tabs
	 */
	public function lite_tabs( $tabs ) {

		$tabs['mobile']     = __( 'Mobile', 'envira-gallery-lite' );
		$tabs['videos']     = __( 'Videos', 'envira-gallery-lite' );
		$tabs['social']     = __( 'Social', 'envira-gallery-lite' );
		$tabs['tags']       = __( 'Tags', 'envira-gallery-lite' );
		$tabs['animations'] = __( 'Animations', 'envira-gallery-lite' );
		$tabs['pagination'] = __( 'Pagination', 'envira-gallery-lite' );
		$tabs['comments']   = __( 'Comments', 'envira-gallery-lite' );
		$tabs['search']     = __( 'Search', 'envira-gallery-lite' );
		return $tabs;
	}

	/**
	 * Callback for displaying the settings UI for the Mobile tab.
	 *
	 * @since 1.3.2
	 *
	 * @param object $post The current post object.
	 */
	public function lite_mobile_tab( $post ) {

		?>

		<div class="upgrade-header">
			<img src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/responsive-icon.png' ); ?>" width="35" height="35" />
			<h2>Customize A Unique Mobile Experience With Envira Pro!</h2>
		</div>

		<div class="upgrade-content">
			<div class="hero-image-exterior">
				<div class="interior">
				<a href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagemobile', 'mobilefeaturesimage' ) ); ?>" target="_blank"><img src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/mobile.jpg' ); ?>" /></a>
				</div>
			</div>
			<p>Build responsive WordPress galleries that work on mobile, tablet and desktop devices. You can even customize all aspects of your user's mobile gallery display experience to be different than desktop.</p>
			<p><strong>Bonus:</strong> Envira Lite users get a discount code for <span class="envira-green">50% off</span> regular price.</p>
			<div class="cta-buttons">
				<a href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( false, 'adminpagemobile', 'upgradetoenviragallerypro' ) ); ?>" target="_blank" class="button button-primary">Upgrade To Envira Pro</a>
			</div>
		</div>

		<?php
	}
	/**
	 * Lite: Callback for displaying the settings UI for the Mobile tab.
	 *
	 * @since 1.5.0
	 *
	 * @param object $post The current post object.
	 */
	public function lite_videos_tab( $post ) {
		?>

		<div class="upgrade-header">
			<img src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/videos-icon.png' ); ?>" width="35" height="35" />
			<h2>Add Video To Your Galleries With Envira Pro!</h2>
		</div>

		<div class="upgrade-content">
			<div class="hero-image-exterior">
				<div class="interior">
					<a href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagevideos', 'videosaddonimage' ) ); ?>" target="_blank"><img src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/videos-addon.png' ); ?>" /></a>
				</div>
			</div>
			<p>Video platform integrations allow you to add more video sources for your galleries. We’ve added integrations with all the most popular video sharing and video hosting providers.</p>
			<div class="two-column-list">
				<ul>
					<li><a target="_blank" href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagevideos', 'videoplatformlinks' ) ); ?>">Self-hosted Videos</a> (MP4)</li>
					<li><a target="_blank" href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagevideos', 'videoplatformlinks' ) ); ?>">YouTube</a> (with playlist and custom start time support)</li>
					<li><a target="_blank" href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagevideos', 'videoplatformlinks' ) ); ?>">Vimeo</a></li>
					<li><a target="_blank" href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagevideos', 'videoplatformlinks' ) ); ?>">Instagram</a> Feed Videos</li>
					<li><a target="_blank" href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagesocial', 'videoplatformlinks' ) ); ?>">Instagram</a> IGTV</li>
				</ul>
				<ul>
					<li><a target="_blank" href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagevideos', 'videoplatformlinks' ) ); ?>">Twitch</a></li>
					<li><a target="_blank" href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagevideos', 'videoplatformlinks' ) ); ?>">VideoPress</a></li>
					<li><a target="_blank" href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagevideos', 'videoplatformlinks' ) ); ?>">DailyMotion</a></li>
					<li><strong>...and more!</strong></li>
				</ul>
			</div>
			<p><strong>Bonus:</strong> Envira Lite users get a discount code for <span class="envira-green">50% off</span> regular price.</p>
			<div class="cta-buttons">
				<a href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( false, 'adminpagevideos', 'upgradetoenviraprobutton' ) ); ?>" target="_blank" class="button button-primary">Upgrade To Envira Pro</a>
			</div>
		</div>

		<?php
	}

	/**
	 * Lite: Callback for displaying the settings UI for the Mobile tab.
	 *
	 * @since 1.5.0
	 *
	 * @param object $post The current post object.
	 */
	public function lite_social_tab( $post ) {

		?>

		<div class="upgrade-header">
			<img src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/social-icon.png' ); ?>" width="35" height="35" />
			<h2>Have Users Share With Envira Pro!</h2>
		</div>

		<div class="upgrade-content">
			<div class="hero-image-exterior">
				<div class="interior">
				<a href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagesocial', 'socialsharingaddonimage' ) ); ?>" target="_blank"><img src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/socialsharing.png' ); ?>" /></a>
				</div>
			</div>
			<p>You can add social sharing buttons to your Gallery images and Lightbox images. Encourage your users to share your content via:</p>
			<div class="two-column-list">
				<ul>
					<li><a target="_blank" href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagesocial', 'socialsharinglinks' ) ); ?>">Facebook</a></li>
					<li><a target="_blank" href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagesocial', 'socialsharinglinks' ) ); ?>">Twitter</a></li>
					<li><a target="_blank" href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagesocial', 'socialsharinglinks' ) ); ?>">PInterest</a></li>
					<li><a target="_blank" href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagesocial', 'socialsharinglinks' ) ); ?>">LinkedIn</a></li>
				</ul>
				<ul>
					<li><a target="_blank" href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagesocial', 'socialsharinglinks' ) ); ?>">WhatsApp</a></li>
					<li><a target="_blank" href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagesocial', 'socialsharinglinks' ) ); ?>">Email</a></li>
					<li><strong>...and more!</strong></li>
				</ul>
			</div>
			<p><strong>Bonus:</strong> Envira Lite users get a discount code for <span class="envira-green">50% off</span> regular price.</p>
			<div class="cta-buttons">
				<a href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( false, 'adminpagesocial', 'upgradetoenviraprobutton' ) ); ?>" target="_blank" class="button button-primary">Upgrade To Envira Pro</a>
			</div>
		</div>

		<?php
	}

	/**
	 * Lite: Callback for displaying the settings UI for the Mobile tab.
	 *
	 * @since 1.5.0
	 *
	 * @param object $post The current post object.
	 */
	public function lite_tags_tab( $post ) {

		?>

		<div class="upgrade-header">
			<img src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/tags-icon.png' ); ?>" width="35" height="35" />
			<h2>Tag And Filter Images With Envira Pro!</h2>
		</div>

		<div class="upgrade-content">
			<div class="hero-image-exterior">
				<div class="interior">
				<a href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagetags', 'tagsaddonimage' ) ); ?>" target="_blank"><img src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/tags-addon.png' ); ?>" /></a>
				</div>
			</div>
			<p>By upgrading to Envira Gallery Pro, you can add Tags to your gallery images (and categories to Albums). Allow users to filter your galleries by tag and so much more!</p>

			<p><strong>Bonus:</strong> Envira Lite users get a discount code for <span class="envira-green">50% off</span> regular price.</p>

			<div class="cta-buttons">
				<a href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( false, 'adminpagetags', 'upgradetoenviraprobutton' ) ); ?>" target="_blank" class="button button-primary">Upgrade To Envira Pro</a>
			</div>
		</div>

		<?php
	}

	/**
	 * Lite: Callback for displaying the settings UI for the Mobile tab.
	 *
	 * @since 1.5.0
	 *
	 * @param object $post The current post object.
	 */
	public function lite_animations_tab( $post ) {

		?>

		<div class="upgrade-header">
			<img src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/animations-icon.png' ); ?>" width="35" height="35" />
			<h2>Animate your galleries</h2>
		</div>

		<div class="upgrade-content">
			<div class="hero-image-exterior">
				<div class="interior">
				<a href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpageanimations', 'animationsaddonimage' ) ); ?>" target="_blank"><img src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/animations-addon.png' ); ?>" /></a>
				</div>
			</div>
			<p>By upgrading to Envira Gallery Pro, you can add Animations to your gallery.</p>

			<p><strong>Bonus:</strong> Envira Lite users get a discount code for <span class="envira-green">50% off</span> regular price.</p>

			<div class="cta-buttons">
				<a href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( false, 'adminpageanimations', 'upgradetoenviraprobutton' ) ); ?>" target="_blank" class="button button-primary">Upgrade To Envira Pro</a>
			</div>
		</div>

		<?php
	}

	/**
	 * Lite: Callback for displaying the settings UI for the Mobile tab.
	 *
	 * @since 1.5.0
	 *
	 * @param object $post The current post object.
	 */
	public function lite_pagination_tab( $post ) {

		?>

		<div class="upgrade-header">
			<img src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/pagination-icon.png' ); ?>" width="35"  />
			<h2>Improve Large Gallery Experiences With Envira Pro!</h2>
		</div>

		<div class="upgrade-content">
			<div class="hero-image-exterior">
				<div class="interior">
				<a href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagepagination', 'paginationaddonimage' ) ); ?>" target="_blank"><img src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/pagination-addon.png' ); ?>" /></a>
				</div>
			</div>
			<p>If you have a lot of photos in your gallery, you can split your Gallery across multiple pages! Customize a variety of aspects including how many items are shown per page, button text, left/right arrows, and more.</p>
			<p>Available pagination options include:</p>
			<div class="two-column-list">
				<ul>
					<li><a target="_blank" href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagepagination', 'bypageajaxorstandard', '#envira-pagination-click-ajax' ) ); ?>">By Page (Ajax Or Standard)</a></li>
					<li><a target="_blank" href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagepagination', 'onscroll', '#envira-pagination-scroll' ) ); ?>">On Scroll</a></li>
				</ul>
				<ul>
					<li><a target="_blank" href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagepagination', 'clickmorebutton' ) ); ?>">Click More Button</a> (with customizable text)</li>
					<li><strong>...and more!</strong></li>
				</ul>
			</div>
			<p><strong>Bonus:</strong> Envira Lite users get a discount code for <span class="envira-green">50% off</span> regular price.</p>

			<div class="cta-buttons">
				<a href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( false, 'adminpagepagination', 'upgradetoenviraprobutton' ) ); ?>" target="_blank" class="button button-primary">Upgrade To Envira Pro</a>
			</div>
		</div>

		<?php
	}

	/**
	 * Lite: Callback for displaying the settings UI for the Comments tab.
	 *
	 * @since 1.5.0
	 *
	 * @param object $post The current post object.
	 */
	public function lite_comments_tab( $post ) {

		?>

		<div class="upgrade-header">
			<img src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/comments-icon.png' ); ?>" width="35"  />
			<h2 class="upgrade-to-pro">Increase user engagement with the comments addon</h2>
		</div>

		<div class="upgrade-content ">
			<div class="hero-image-exterior">
				<div class="interior">
					<a href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagecomments', 'commentsaddonimage' ) ); ?>" target="_blank"><img src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/comments-addon.png' ); ?>" /></a>
				</div>
			</div>
			<p></p>
			<p>Available comments options include:</p>
			<div class="two-column-list">
				<ul>
					<li><a target="_blank" href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagecomments', 'galleries', '#envira-comments-galleries' ) ); ?>">Galleries and Albums</a></li>
					<li><a target="_blank" href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagecomments', 'visibility', '#envira-comments-visibility' ) ); ?>">Show/Hide Comments Panel</a></li>
				</ul>
				<ul>
					<li><a target="_blank" href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagecomments', 'moderation' ) ); ?>">Comments Moderation</a></li>
					<li><strong>...and more!</strong></li>
				</ul>
			</div>
			<p class="upsell-comments"><strong>Bonus:</strong> Envira Lite users get a discount code for <span class="envira-green">50% off</span> regular price.</p>

			<div class="cta-buttons">
				<a href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( false, 'adminpagecomments', 'upgradetoenviraprobutton' ) ); ?>" target="_blank" class="button button-primary">Upgrade To Envira Pro</a>
			</div>
		</div>

		<?php
	}

	/**
	 * Lite: Callback for displaying the settings UI for the Search tab.
	 *
	 * @since 1.9.2
	 *
	 * @param object $post The current post object.
	 */
	public function lite_search_tab( $post ) {
		?>

		<div class="upgrade-header">
			<img src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/search-icon.png' ); ?>" width="35" />
			<h2><?php esc_html_e( 'Search images with Envira Pro', 'envira-gallery-lite' ); ?></h2>
		</div>

		<div class="upgrade-content">
			<div class="hero-image-exterior">
				<div class="interior">
				<a href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( 'https://enviragallery.com/lite', 'adminpagesearch', 'searchaddonimage' ) ); ?>" target="_blank"><img src="<?php echo esc_url( trailingslashit( ENVIRA_LITE_URL ) . 'assets/images/search-addon.png' ); ?>" /></a>
				</div>
			</div>
			<p><?php esc_html_e( 'Upgrade to Envira Gallery Pro to unlock advanced search capabilities, making it effortless for users to find their favorite images in your galleries!', 'envira-gallery-lite' ); ?></p>

			<?php
				printf(
					'<p><strong>%s</strong> %s <span class="envira-green">%s</span> %s.</p>',
					esc_html__( 'Bonus:', 'envira-gallery-lite' ),
					esc_html__( 'Envira Lite users get a discount code for', 'envira-gallery-lite' ),
					esc_html__( '50% off', 'envira-gallery-lite' ),
					esc_html__( 'regular price', 'envira-gallery-lite' )
				);
			?>

			<div class="cta-buttons">
				<a href="<?php echo esc_url( Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link( false, 'adminpagesearch', 'upgradetoenviraprobutton' ) ); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'Upgrade To Envira Pro', 'envira-gallery-lite' ); ?></a>
			</div>
		</div>

		<?php
	}

	/**
	 * Callback for saving values from Envira metaboxes.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $post_id The current post ID.
	 * @param object $post The current post object.
	 */
	public function save_meta_boxes( $post_id, $post ) {

		// Bail out if we fail a security check.
		if ( ! isset( $_POST['envira-gallery'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['envira-gallery'] ) ), 'envira-gallery' ) || ! isset( $_POST['_envira_gallery'] ) ) {
			return;
		}

		// Bail out if running an autosave, ajax, cron or revision.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			// Check if this is a Quick Edit request.
			if ( isset( $_POST['_inline_edit'] ) ) {

				// Just update specific fields in the Quick Edit screen.

				// Get settings.
				$settings = get_post_meta( $post_id, '_eg_gallery_data', true );
				if ( empty( $settings ) ) {
					return;
				}

				// Update Settings.
				$settings['config']['columns']       = isset( $_POST['_envira_gallery']['columns'] ) ? preg_replace( '#[^a-z0-9-_]#', '', sanitize_text_field( wp_unslash( $_POST['_envira_gallery']['columns'] ) ) ) : false;
				$settings['config']['gallery_theme'] = isset( $_POST['_envira_gallery']['gallery_theme'] ) ? preg_replace( '#[^a-z0-9-_]#', '', sanitize_text_field( wp_unslash( $_POST['_envira_gallery']['gallery_theme'] ) ) ) : false;
				$settings['config']['gutter']        = isset( $_POST['_envira_gallery']['gutter'] ) ? absint( $_POST['_envira_gallery']['gutter'] ) : false;
				$settings['config']['margin']        = isset( $_POST['_envira_gallery']['margin'] ) ? absint( $_POST['_envira_gallery']['margin'] ) : false;
				$settings['config']['crop_width']    = isset( $_POST['_envira_gallery']['crop_width'] ) ? absint( $_POST['_envira_gallery']['crop_width'] ) > 0 ? absint( $_POST['_envira_gallery']['crop_width'] ) : $this->get_config_default( 'crop_width' ) : false;
				$settings['config']['crop_height']   = isset( $_POST['_envira_gallery']['crop_height'] ) ? absint( $_POST['_envira_gallery']['crop_height'] ) > 0 ? absint( $_POST['_envira_gallery']['crop_height'] ) : $this->get_config_default( 'crop_height' ) : false;

				// Provide a filter to override settings.
				$settings = apply_filters( 'envira_gallery_quick_edit_save_settings', $settings, $post_id, $post );

				// Update the post meta.
				update_post_meta( $post_id, '_eg_gallery_data', $settings );

				// Finally, flush all gallery caches to ensure everything is up to date.
				Envira_Gallery_Common::get_instance()->flush_gallery_caches( $post_id, $settings['config']['slug'] );

			}

			return;
		}

		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Bail out if the user doesn't have the correct permissions to update the slider.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// If the post has just been published for the first time, set meta field for the gallery meta overlay helper.
		if ( isset( $post->post_date ) && isset( $post->post_modified ) && $post->post_date === $post->post_modified ) {
			update_post_meta( $post_id, '_eg_just_published', true );
		}

		// Sanitize all user inputs.
		$settings = get_post_meta( $post_id, '_eg_gallery_data', true );
		if ( empty( $settings ) ) {
			$settings = [];
		}

		// Force slider ID to match Post ID. This is deliberate; if a gallery is duplicated (either using a duplication)
		// plugin or WPML, the ID remains as the original gallery ID, which breaks things for translations etc.
		$settings['id'] = $post_id;

		// Config.
		$settings['config']['type']               = isset( $_POST['_envira_gallery']['type'] ) ? sanitize_text_field( wp_unslash( $_POST['_envira_gallery']['type'] ) ) : $this->get_config_default( 'type' );
		$settings['config']['columns']            = isset( $_POST['_envira_gallery']['columns'] ) ? preg_replace( '#[^a-z0-9-_]#', '', sanitize_text_field( wp_unslash( $_POST['_envira_gallery']['columns'] ) ) ) : $this->get_config_default( 'columns' );
		$settings['config']['gallery_theme']      = isset( $_POST['_envira_gallery']['gallery_theme'] ) ? preg_replace( '#[^a-z0-9-_]#', '', sanitize_text_field( wp_unslash( $_POST['_envira_gallery']['gallery_theme'] ) ) ) : $this->get_config_default( 'gallery_theme' );
		$settings['config']['crop_width']         = isset( $_POST['_envira_gallery']['crop_width'] ) ? absint( $_POST['_envira_gallery']['crop_width'] ) > 0 ? absint( $_POST['_envira_gallery']['crop_width'] ) : $this->get_config_default( 'crop_width' ) : $this->get_config_default( 'crop_width' );
		$settings['config']['crop_height']        = isset( $_POST['_envira_gallery']['crop_height'] ) ? absint( $_POST['_envira_gallery']['crop_height'] ) > 0 ? absint( $_POST['_envira_gallery']['crop_height'] ) : $this->get_config_default( 'crop_height' ) : $this->get_config_default( 'crop_height' );
		$settings['config']['crop']               = isset( $_POST['_envira_gallery']['crop'] ) ? 1 : 0;
		$settings['config']['justified_margins']  = isset( $_POST['_envira_gallery']['justified_margins'] ) ? absint( $_POST['_envira_gallery']['justified_margins'] ) : $this->get_config_default( 'justified_margins' );
		$settings['config']['lazy_loading']       = isset( $_POST['_envira_gallery']['lazy_loading'] ) ? 1 : 0;
		$settings['config']['lazy_loading_delay'] = isset( $_POST['_envira_gallery']['lazy_loading_delay'] ) ? absint( $_POST['_envira_gallery']['lazy_loading_delay'] ) : $this->get_config_default( 'lazy_loading_delay' );
		$settings['config']['gutter']             = isset( $_POST['_envira_gallery']['gutter'] ) ? absint( $_POST['_envira_gallery']['gutter'] ) : $this->get_config_default( 'gutter' );
		$settings['config']['margin']             = isset( $_POST['_envira_gallery']['margin'] ) ? absint( $_POST['_envira_gallery']['margin'] ) : $this->get_config_default( 'margin' );
		$settings['config']['image_size']         = isset( $_POST['_envira_gallery']['image_size'] ) ? sanitize_text_field( wp_unslash( $_POST['_envira_gallery']['image_size'] ) ) : $this->get_config_default( 'image_size' );

		// Automatic/Justified.
		$settings['config']['justified_row_height'] = isset( $_POST['_envira_gallery']['justified_row_height'] ) ? absint( $_POST['_envira_gallery']['justified_row_height'] ) : 150;

		// Lightbox.
		$settings['config']['lightbox_enabled']     = isset( $_POST['_envira_gallery']['lightbox_enabled'] ) ? 1 : 0;
		$settings['config']['gallery_link_enabled'] = isset( $_POST['_envira_gallery']['gallery_link_enabled'] ) ? 1 : 0;
		$settings['config']['lightbox_theme']       = isset( $_POST['_envira_gallery']['lightbox_theme'] ) ? preg_replace( '#[^a-z0-9-_]#', '', sanitize_text_field( wp_unslash( $_POST['_envira_gallery']['lightbox_theme'] ) ) ) : $this->get_config_default( 'lightbox_theme' );
		$settings['config']['lightbox_image_size']  = isset( $_POST['_envira_gallery']['lightbox_image_size'] ) ? preg_replace( '#[^a-z0-9-_]#', '', sanitize_text_field( wp_unslash( $_POST['_envira_gallery']['lightbox_image_size'] ) ) ) : $this->get_config_default( 'lightbox_image_size' );
		$settings['config']['title_display']        = isset( $_POST['_envira_gallery']['title_display'] ) ? preg_replace( '#[^a-z0-9-_]#', '', sanitize_text_field( wp_unslash( $_POST['_envira_gallery']['title_display'] ) ) ) : $this->get_config_default( 'title_display' );

		// Misc.
		$settings['config']['classes'] = isset( $_POST['_envira_gallery']['classes'] ) ? array_map(
			function ( $custom_class ) {
				return sanitize_html_class( wp_unslash( trim( $custom_class ) ) );
}, explode("\n", $_POST['_envira_gallery']['classes'] ) ) : array(); // @codingStandardsIgnoreLine
		$settings['config']['rtl']     = isset( $_POST['_envira_gallery']['rtl'] ) ? 1 : 0;
		$settings['config']['title']   = isset( $_POST['_envira_gallery']['title'] ) ? trim( wp_strip_all_tags( sanitize_text_field( wp_unslash( $_POST['_envira_gallery']['title'] ) ) ) ) : '';
		$settings['config']['slug']    = isset( $_POST['_envira_gallery']['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['_envira_gallery']['slug'] ) ) : null;

		// If on an envira post type, map the title and slug of the post object to the custom fields if no value exists yet.
		if ( isset( $post->post_type ) && 'envira' === $post->post_type ) {
			if ( empty( $settings['config']['title'] ) ) {
				$settings['config']['title'] = trim( wp_strip_all_tags( $post->post_title ) );
			}
			if ( empty( $settings['config']['slug'] ) ) {
				$settings['config']['slug'] = sanitize_text_field( $post->post_name );
			}
		}

		// Provide a filter to override settings.
		$settings = apply_filters( 'envira_gallery_save_settings', $settings, $post_id, $post );

		// Update the post meta.
		update_post_meta( $post_id, '_eg_gallery_data', $settings );

		// Change states of images in gallery from pending to active.
		$this->change_gallery_states( $post_id );

		// If the thumbnails option is checked, crop images accordingly.
		if ( isset( $settings['config']['thumbnails'] ) && $settings['config']['thumbnails'] ) {
			$args = [
				'position' => 'c',
				'width'    => $this->get_config( 'thumbnails_width', $this->get_config_default( 'thumbnails_width' ) ),
				'height'   => $this->get_config( 'thumbnails_height', $this->get_config_default( 'thumbnails_height' ) ),
				'quality'  => 100,
				'retina'   => false,
			];
			$args = apply_filters( 'envira_gallery_crop_image_args', $args );
			$this->crop_thumbnails( $args, $post_id );
		}

		// If the crop option is checked, crop images accordingly.
		if ( isset( $settings['config']['crop'] ) && $settings['config']['crop'] ) {
			$args = [
				'position' => 'c',
				'width'    => $this->get_config( 'crop_width', $this->get_config_default( 'crop_width' ) ),
				'height'   => $this->get_config( 'crop_height', $this->get_config_default( 'crop_height' ) ),
				'quality'  => 100,
				'retina'   => false,
			];
			$args = apply_filters( 'envira_gallery_crop_image_args', $args );
			$this->crop_images( $args, $post_id );
		}

		// If the mobile option is checked, crop images accordingly.
		if ( isset( $settings['config']['mobile'] ) && $settings['config']['mobile'] ) {
			$args = [
				'position' => 'c',
				'width'    => $this->get_config( 'mobile_width', $this->get_config_default( 'mobile_width' ) ),
				'height'   => $this->get_config( 'mobile_height', $this->get_config_default( 'mobile_height' ) ),
				'quality'  => 100,
				'retina'   => false,
			];
			$args = apply_filters( 'envira_gallery_crop_image_args', $args );
			$this->crop_images( $args, $post_id );
		}

		// Fire a hook for addons that need to utilize the cropping feature.
		do_action( 'envira_gallery_saved_settings', $settings, $post_id, $post );

		// Finally, flush all gallery caches to ensure everything is up to date.
		Envira_Gallery_Common::get_instance()->flush_gallery_caches( $post_id, $settings['config']['slug'] );
	}

	/**
	 * Helper method for retrieving the gallery layout for an item in the admin.
	 *
	 * Also defines the item's model which is used in assets/js/media-edit.js
	 *
	 * @since 1.0.0
	 *
	 * @param int   $id         The ID of the item to retrieve.
	 * @param array $item       The item data (i.e. image / video).
	 * @param int   $post_id    The current post ID.
	 * @return string               The HTML output for the gallery item.
	 */
	public function get_gallery_item( $id, $item, $post_id = 0 ) {

		// Get thumbnail.
		$thumbnail = wp_get_attachment_image_src( $id );

		// Add id to $item for Backbone model.
		$item['id'] = $id;

		// Allow addons to populate the item's data - for example, tags which are stored against the attachment.
		$item               = apply_filters( 'envira_gallery_get_gallery_item', $item, $id, $post_id );
		$item['alt']        = str_replace( '&quot;', '\"', $item['alt'] );
		$item['_thumbnail'] = ( ! empty( $thumbnail[0] ) ) ? $thumbnail[0] : $item['src'];
		// Never saved against the gallery item, just used for the thumbnail output in the Edit Gallery screen.

		// JSON encode based on PHP version.
		$json = version_compare( PHP_VERSION, '5.3.0' ) >= 0 ? wp_json_encode( $item, JSON_HEX_APOS ) : wp_json_encode( $item );

		// Buffer the output.
		ob_start();
		?>
	<li id="<?php echo esc_attr( $id ); ?>" class="envira-gallery-image envira-gallery-status-<?php echo esc_attr( $item['status'] ); ?>" data-envira-gallery-image="<?php echo esc_attr( $id ); ?>" data-envira-gallery-image-model='<?php echo esc_html( htmlspecialchars( $json, ENT_QUOTES, 'UTF-8' ) ); ?>'>
		<img src="<?php echo esc_url( $item['_thumbnail'] ); ?>" alt="<?php echo esc_attr( $item['alt'] ); ?>" />
		<div class="meta">
			<div class="title">
				<span>
					<?php
						$the_title = ( isset( $item['title'] ) ? $item['title'] : '' );

						// Output Title.
						echo esc_html( $the_title );

						// If the title exceeds 20 characters, the grid view will deliberately only show the first line of the title.
						// Therefore we need to make it clear to the user that the full title is there by way of a hint.
					?>
				</span>
				<a class="hint <?php echo ( ( strlen( $the_title ) > 20 ) ? '' : ' hidden' ); ?>" title="<?php echo ( isset( $the_title ) ? esc_attr( $the_title ) : '' ); ?>">...</a>
			</div>
			<div class="additional">
			<?php
				// Addons can add content to this meta section, which is displayed when in the List View.
				echo apply_filters( 'envira_gallery_metabox_output_gallery_item_meta', '', $item, $id, $post_id ); // @codingStandardsIgnoreLine
			?>
			</div>
		</div>

		<a href="#" class="check"><div class="media-modal-icon"></div></a>
		<a href="#" class="dashicons dashicons-trash envira-gallery-remove-image" title="<?php esc_html_e( 'Remove Image from Gallery?', 'envira-gallery-lite' ); ?>"></a>
		<a href="#" class="dashicons dashicons-edit envira-gallery-modify-image" title="<?php esc_html_e( 'Modify Image', 'envira-gallery-lite' ); ?>"></a>
	</li>
		<?php
		return ob_get_clean();
	}

	/**
	 * Helper method to change a gallery state from pending to active. This is done
	 * automatically on post save. For previewing galleries before publishing,
	 * simply click the "Preview" button and Envira will load all the images present
	 * in the gallery at that time.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The current post ID.
	 */
	public function change_gallery_states( $post_id ) {

		$gallery_data = get_post_meta( $post_id, '_eg_gallery_data', true );
		if ( ! empty( $gallery_data['gallery'] ) ) {
			foreach ( (array) $gallery_data['gallery'] as $id => $item ) {
				$gallery_data['gallery'][ $id ]['status'] = 'active';
			}
		}

		update_post_meta( $post_id, '_eg_gallery_data', $gallery_data );
	}

	/**
	 * Helper method to crop gallery thumbnails to the specified sizes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args  Array of args used when cropping the images.
	 * @param int   $post_id The current post ID.
	 * @param bool  $force_overwrite Forces an overwrite even if the thumbnail already exists (useful for applying watermarks).
	 */
	public function crop_thumbnails( $args, $post_id, $force_overwrite = false ) {

		// Gather all available images to crop.
		$gallery_data = get_post_meta( $post_id, '_eg_gallery_data', true );
		$images       = ! empty( $gallery_data['gallery'] ) ? $gallery_data['gallery'] : false;
		$common       = Envira_Gallery_Common::get_instance();

		// Populate variable if we are using <= PHP 5.3.
		$php_version = explode( '.', phpversion() );

		if ( $php_version[0] <= 5 && $php_version[1] <= 3 ) {
			$is_using_old_php = true;
		} else {
			$is_using_old_php = false;
		}

		// Loop through the images and crop them.
		if ( $images ) {
			// Increase the time limit to account for large image sets and suspend cache invalidations.
			if ( ! $is_using_old_php ) {
				set_time_limit( Envira_Gallery_Common::get_instance()->get_max_execution_time() );
			}

			wp_suspend_cache_invalidation( true );

			foreach ( $images as $id => $item ) {
				// Get the full image attachment. If it does not return the data we need, skip over it.
				$image = wp_get_attachment_image_src( $id, 'full' );

				if ( ! is_array( $image ) ) {
					continue;
				}

				// Check the image is a valid URL.
				// Some plugins decide to strip the blog's URL from the start of the URL, which can cause issues for Envira.
				if ( strpos( $image[0], get_bloginfo( 'url' ) ) === false ) {
					$image[0] = get_bloginfo( 'url' ) . '/' . $image[0];
				}

				// Generate the cropped image.
				$cropped_image = $common->resize_image( $image[0], $args['width'], $args['height'], true, $args['position'], $args['quality'], $args['retina'], null, $force_overwrite );

				// If there is an error, possibly output error message, otherwise woot!
				if ( is_wp_error( $cropped_image ) ) {
					// If WP_DEBUG is enabled, and we're logged in, output an error to the user.
					if ( defined( 'WP_DEBUG' ) && WP_DEBUG && is_user_logged_in() ) {
						echo '<pre>Envira: Error occured resizing image (these messages are only displayed to logged in WordPress users):<br />';
						echo 'Error: ' . esc_html( $cropped_image->get_error_message() ) . '<br />';
						echo 'Image: ' . esc_html( var_export( $image, true ) ) . '<br />'; //@codingStandardsIgnoreLine -- Not run in production
						echo 'Args: ' . esc_html( var_export( $args, true ) ) . '</pre>'; //@codingStandardsIgnoreLine -- Not run in production
						die();
					}
				} else {
					$gallery_data['gallery'][ $id ]['thumb'] = $cropped_image;
				}
			}

			// Turn off cache suspension and flush the cache to remove any cache inconsistencies.
			wp_suspend_cache_invalidation( false );
			wp_cache_flush();

			// Update the gallery data.
			update_post_meta( $post_id, '_eg_gallery_data', $gallery_data );
		}
	}

	/**
	 * Helper method to crop gallery images to the specified sizes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args  Array of args used when cropping the images.
	 * @param int   $post_id The current post ID.
	 * @param bool  $force_overwrite Forces an overwrite even if the thumbnail already exists (useful for applying watermarks).
	 */
	public function crop_images( $args, $post_id, $force_overwrite = false ) {

		// Gather all available images to crop.
		$gallery_data = get_post_meta( $post_id, '_eg_gallery_data', true );
		$images       = ! empty( $gallery_data['gallery'] ) ? $gallery_data['gallery'] : false;
		$common       = Envira_Gallery_Common::get_instance();

		// Populate variable if we are using <= PHP 5.3.
		$php_version = explode( '.', phpversion() );

		if ( $php_version[0] <= 5 && $php_version[1] <= 3 ) {
			$is_using_old_php = true;
		} else {
			$is_using_old_php = false;
		}

		// Loop through the images and crop them.
		if ( $images ) {
			// Increase the time limit to account for large image sets and suspend cache invalidations.
			if ( ! $is_using_old_php ) {
				set_time_limit( Envira_Gallery_Common::get_instance()->get_max_execution_time() );
			}
			wp_suspend_cache_invalidation( true );

			foreach ( $images as $id => $item ) {
				// Get the full image attachment. If it does not return the data we need, skip over it.
				$image = wp_get_attachment_image_src( $id, 'full' );
				if ( ! is_array( $image ) ) {
					continue;
				}

				// Check the image is a valid URL.
				// Some plugins decide to strip the blog's URL.
				if ( ! filter_var( $image[0], FILTER_VALIDATE_URL ) ) {
					$image[0] = get_bloginfo( 'url' ) . '/' . $image[0];
				}

				// Generate the cropped image.
				$cropped_image = $common->resize_image( $image[0], $args['width'], $args['height'], true, $args['position'], $args['quality'], $args['retina'], null, $force_overwrite );

				// If there is an error, possibly output error message, otherwise woot!
				if ( is_wp_error( $cropped_image ) ) {
					// If debugging is defined, print out the error.
					if ( defined( 'ENVIRA_GALLERY_CROP_DEBUG' ) && ENVIRA_GALLERY_CROP_DEBUG ) {
						echo '<pre>' . esc_html( var_export( $cropped_image->get_error_message(), true ) ) . '</pre>'; //@codingStandardsIgnoreLine -- Not run in production
					}
				}
			}

			// Turn off cache suspension and flush the cache to remove any cache inconsistencies.
			wp_suspend_cache_invalidation( false );
			wp_cache_flush();
		}
	}

	/**
	 * Helper method for retrieving config values.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key     The config key to retrieve.
	 * @param string $default_value A default value to use.
	 *
	 * @global int $id        The current post ID.
	 * @global object $post The current post object.
	 * @return string         Key value on success, empty string on failure.
	 */
	public function get_config( $key, $default_value = false ) {

		global $id, $post;

		// Get the current post ID. If ajax, grab it from the $_POST variable.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && array_key_exists( 'post_id', $_POST ) ) { // @codingStandardsIgnoreLine
			$post_id = absint( wp_unslash( $_POST['post_id'] ) ); // @codingStandardsIgnoreLine
		} else {
			$post_id = isset( $post->ID ) ? $post->ID : (int) $id;
		}

		// Get config.
		$settings = get_post_meta( $post_id, '_eg_gallery_data', true );

		// Check config key exists.
		if ( isset( $settings['config'][ $key ] ) ) {
			return $settings['config'][ $key ];
		} else {
			return $default_value ? $default_value : '';
		}
	}

	/**
	 * Helper method for setting default config values.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The default config key to retrieve.
	 * @return string Key value on success, false on failure.
	 */
	public function get_config_default( $key ) {

		$instance = Envira_Gallery_Common::get_instance();
		return $instance->get_config_default( $key );
	}

	/**
	 * Helper method for retrieving columns.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of column data.
	 */
	public function get_columns() {

		$instance = Envira_Gallery_Common::get_instance();
		return $instance->get_columns();
	}

	/**
	 * Helper method for retrieving gallery themes.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of gallery theme data.
	 */
	public function get_gallery_themes() {

		$instance = Envira_Gallery_Common::get_instance();
		return $instance->get_gallery_themes();
	}

	/**
	 * Helper method for retrieving justified gallery themes.
	 *
	 * @since 1.1.1
	 *
	 * @return array Array of gallery theme data.
	 */
	public function get_justified_gallery_themes() {

		$instance = Envira_Gallery_Common::get_instance();
		return $instance->get_justified_gallery_themes();
	}

	/**
	 * Helper method for retrieving description options.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of description options.
	 */
	public function get_display_description_options() {

		$instance = Envira_Gallery_Common::get_instance();
		return $instance->get_display_description_options();
	}

	/**
	 * Helper method for retrieving sorting options.
	 *
	 * @since 1.3.8
	 *
	 * @return array Array of sorting options.
	 */
	public function get_sorting_options() {

		$instance = Envira_Gallery_Common::get_instance();
		return $instance->get_sorting_options();
	}

	/**
	 * Helper method for retrieving sorting directions.
	 *
	 * @since 1.3.8
	 *
	 * @return array Array of sorting directions.
	 */
	public function get_sorting_directions() {

		$instance = Envira_Gallery_Common::get_instance();
		return $instance->get_sorting_directions();
	}

	/**
	 * Helper method for retrieving lightbox themes.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of lightbox theme data.
	 */
	public function get_lightbox_themes() {

		$instance = Envira_Gallery_Common::get_instance();
		return $instance->get_lightbox_themes();
	}

	/**
	 * Helper method for retrieving image sizes.
	 *
	 * @since 1.3.6
	 *
	 * @param   bool $wordpress_only     WordPress Only image sizes (default: false).
	 * @return array Array of image size data.
	 */
	public function get_image_sizes( $wordpress_only = false ) {

		$instance = Envira_Gallery_Common::get_instance();
		return $instance->get_image_sizes( $wordpress_only );
	}

	/**
	 * Helper method for retrieving title displays.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of title display data.
	 */
	public function get_title_displays() {

		$instance = Envira_Gallery_Common::get_instance();
		return $instance->get_title_displays();
	}

	/**
	 * Helper method for retrieving arrow positions.
	 *
	 * @since 1.3.3.7
	 *
	 * @return array Array of title display data.
	 */
	public function get_arrows_positions() {

		$instance = Envira_Gallery_Common::get_instance();
		return $instance->get_arrows_positions();
	}

	/**
	 * Helper method for retrieving toolbar positions.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of toolbar position data.
	 */
	public function get_toolbar_positions() {

		$instance = Envira_Gallery_Common::get_instance();
		return $instance->get_toolbar_positions();
	}

	/**
	 * Helper method for retrieving lightbox transition effects.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of transition effect data.
	 */
	public function get_transition_effects() {

		$instance = Envira_Gallery_Common::get_instance();
		return $instance->get_transition_effects();
	}

	/**
	 * Helper method for retrieving lightbox easing transition effects.
	 *
	 * @since 1.4.1.2
	 *
	 * @return array Array of transition effect data.
	 */
	public function get_easing_transition_effects() {

		$instance = Envira_Gallery_Common::get_instance();
		return $instance->get_easing_transition_effects();
	}

	/**
	 * Helper method for retrieving thumbnail positions.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of thumbnail position data.
	 */
	public function get_thumbnail_positions() {

		$instance = Envira_Gallery_Common::get_instance();
		return $instance->get_thumbnail_positions();
	}

	/**
	 * Returns the post types to skip for loading Envira metaboxes.
	 *
	 * @since 1.0.7
	 *
	 * @return array Array of skipped posttypes.
	 */
	public function get_skipped_posttypes() {

		$skipped_posttypes = [ 'attachment', 'revision', 'nav_menu_item', 'soliloquy', 'soliloquyv2', 'envira_album' ];
		return apply_filters( 'envira_gallery_skipped_posttypes', $skipped_posttypes );
	}

	/**
	 * Flag to determine if the GD library has been compiled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if has proper extension, false otherwise.
	 */
	public function has_gd_extension() {

		return extension_loaded( 'gd' ) && function_exists( 'gd_info' );
	}

	/**
	 * Flag to determine if the Imagick library has been compiled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if has proper extension, false otherwise.
	 */
	public function has_imagick_extension() {

		return extension_loaded( 'imagick' );
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return object The Envira_Gallery_Metaboxes object.
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Envira_Gallery_Metaboxes ) ) {
			self::$instance = new Envira_Gallery_Metaboxes();
		}

		return self::$instance;
	}
}

// Load the metabox class.
$envira_gallery_metaboxes = Envira_Gallery_Metaboxes::get_instance();
