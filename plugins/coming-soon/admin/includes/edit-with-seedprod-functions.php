<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Edit with SeedProd - Version 2 (Complete Rewrite)
 *
 * This file handles the "Edit with SeedProd" button functionality in the WordPress page editor.
 *
 * TWO PATHS:
 * 1. Template Mode: Convert WordPress page to SeedProd landing page (standalone)
 * 2. Builder Mode: Edit existing SeedProd page (landing page or theme page)
 *
 * METADATA:
 * - _seedprod_page = 1                    -> Standalone landing page
 * - _seedprod_edited_with_seedprod = 1   -> Theme page (edited with SeedProd)
 *
 * These are mutually exclusive - a page is either standalone OR part of theme.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ============================================================================
 * SECTION 1: PHP - Determine Button State and Generate Data
 * ============================================================================
 */

/**
 * Add body class and enqueue CSS for pages managed by SeedProd
 */
function seedprod_lite_v2_admin_body_class( $classes ) {
	$screen = get_current_screen();

	// Only on page editor
	if ( ! $screen || 'page' !== $screen->post_type ) {
		return $classes;
	}

	// Check if we're editing an existing page
	if ( empty( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return $classes;
	}

	$post_id = absint( wp_unslash( $_GET['post'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	// Check if page is managed by SeedProd
	$is_seedprod_page        = get_post_meta( $post_id, '_seedprod_page', true );
	$is_edited_with_seedprod = get_post_meta( $post_id, '_seedprod_edited_with_seedprod', true );
	$has_seedprod_content    = ! empty( get_post_field( 'post_content_filtered', $post_id ) );


	$is_managed_by_seedprod = ( '1' === $is_seedprod_page ) ||
	                           ( '1' === $is_edited_with_seedprod ) ||
	                           $has_seedprod_content;

	if ( $is_managed_by_seedprod ) {
		$classes .= ' seedprod-editor-active';
	}

	return $classes;
}
add_filter( 'admin_body_class', 'seedprod_lite_v2_admin_body_class' );


/**
 * Enqueue CSS for hiding editor and showing message
 */
function seedprod_lite_v2_enqueue_admin_css() {
	$screen = get_current_screen();

	// Only on page editor
	if ( ! $screen || 'page' !== $screen->post_type ) {
		return;
	}

	// Enqueue directly as inline style in head
	add_action( 'admin_head', function() {
		?>
		<style type="text/css">
		/* Hide WordPress editor when SeedProd is active */
		body.seedprod-editor-active .block-editor-block-list__layout,
		body.seedprod-editor-active .editor-styles-wrapper,
		body.seedprod-editor-active .editor-styles-wrapper > *,
		body.seedprod-editor-active .block-editor-writing-flow,
		body.seedprod-editor-active .is-root-container,
		body.seedprod-editor-active iframe[name="editor-canvas"],
		body.seedprod-editor-active #postdivrich {
			display: none !important;
		}

		/* Ensure the visual editor area is also hidden */
		body.seedprod-editor-active .edit-post-visual-editor,
		body.seedprod-editor-active .block-editor-editor-skeleton__content {
			background: transparent !important;
		}

		/* Show SeedProd message */
		#seedprod-managed-message {
			text-align: center;
			padding: 60px 20px;
			background: #f9f9f9;
			border: 1px solid #ddd;
			margin: 20px;
		}

		#seedprod-managed-message h2 {
			margin: 0 0 20px 0;
			font-size: 24px;
			color: #333;
		}

		#seedprod-managed-message .button {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			font-size: 16px;
			padding: 12px 24px;
			height: auto;
		}

		#seedprod-managed-message .button img {
			margin-right: 8px;
			width: 20px;
			height: 20px;
		}
		</style>
		<?php
	});
}
add_action( 'admin_enqueue_scripts', 'seedprod_lite_v2_enqueue_admin_css' );


/**
 * Add "Page managed by SeedProd" message in editor
 */
function seedprod_lite_v2_add_managed_message() {
	$screen = get_current_screen();

	// Only on page editor
	if ( ! $screen || 'page' !== $screen->post_type ) {
		return;
	}

	// Check if we're editing an existing page
	if ( empty( $_GET['post'] ) ) {
		return;
	}

	$post_id = absint( $_GET['post'] );

	// Check if page is managed by SeedProd
	$is_seedprod_page        = get_post_meta( $post_id, '_seedprod_page', true );
	$is_edited_with_seedprod = get_post_meta( $post_id, '_seedprod_edited_with_seedprod', true );
	$has_seedprod_content    = ! empty( get_post_field( 'post_content_filtered', $post_id ) );

	$is_managed_by_seedprod = ( '1' === $is_seedprod_page ) ||
	                           ( '1' === $is_edited_with_seedprod ) ||
	                           $has_seedprod_content;

	if ( ! $is_managed_by_seedprod ) {
		return;
	}

	// Build edit URL
	$edit_url = admin_url() . 'admin.php?page=seedprod_lite_builder&id=' . $post_id . '#/setup/' . $post_id;

	?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		var messageHtml = '<div id="seedprod-managed-message">' +
			'<h2><?php echo esc_js( __( 'This page is managed by SeedProd', 'coming-soon' ) ); ?></h2>' +
			'<a href="<?php echo esc_url( $edit_url ); ?>" class="button button-primary button-large">' +
				'<img src="<?php echo esc_url( SEEDPROD_PLUGIN_URL . 'public/svg/admin-bar-icon.svg' ); ?>" />' +
				'<?php echo esc_js( __( 'Edit with SeedProd', 'coming-soon' ) ); ?>' +
			'</a>' +
		'</div>';

		// Wait for Gutenberg to load
		var checkExist = setInterval(function() {
			// Target the specific Gutenberg content container
			var targetContainer = $('.interface-interface-skeleton__content[aria-label="Editor content"]');

			if (targetContainer.length && !$('#seedprod-managed-message').length) {
				targetContainer.prepend(messageHtml);
				clearInterval(checkExist);
				return;
			}

			// Check for Classic editor
			if ($('#postdivrich').length && !$('#seedprod-managed-message').length) {
				$('#postdivrich').after(messageHtml);
				clearInterval(checkExist);
				return;
			}
		}, 100);
	});
	</script>
	<?php
}
add_action( 'admin_footer', 'seedprod_lite_v2_add_managed_message' );


/**
 * ============================================================================
 * SECTION 2: Enqueue Gutenberg Toolbar Script
 * ============================================================================
 */

/**
 * Enqueue script for Gutenberg toolbar button
 * Runs on enqueue_block_editor_assets for Gutenberg pages
 */
function seedprod_lite_v2_enqueue_toolbar_script() {
	$screen = get_current_screen();

	// Only run on page editor
	if ( ! $screen || 'page' !== $screen->post_type ) {
		return;
	}

	// Check user permissions
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	// Check if we should show the button
	$theme_enabled = get_option( 'seedprod_theme_enabled' );

	// If editing an existing page, check if it's already a SeedProd page
	$post_id               = 0;
	$is_seedprod_page      = false;
	$is_edited_with_seedprod = false;

	if ( ! empty( $_GET['post'] ) ) {
		$post_id                 = absint( wp_unslash( $_GET['post'] ) );
		$is_seedprod_page        = get_post_meta( $post_id, '_seedprod_page', true );
		$is_edited_with_seedprod = get_post_meta( $post_id, '_seedprod_edited_with_seedprod', true );
	}

	$is_already_seedprod = ( '1' === $is_seedprod_page ) || ( '1' === $is_edited_with_seedprod );

	// Only show button if:
	// 1. Theme builder is enabled, OR
	// 2. This page is already a SeedProd page
	if ( empty( $theme_enabled ) && ! $is_already_seedprod ) {
		return;
	}

	// Register a dummy script handle to attach inline script to
	wp_register_script( 'seedprod-gutenberg-button', '', array( 'jquery', 'wp-data', 'wp-editor' ), SEEDPROD_VERSION, true );
	wp_enqueue_script( 'seedprod-gutenberg-button' );

	// Localize translatable strings
	$translations = array(
		'buttonText'   => __( 'Edit with SeedProd', 'coming-soon' ),
		'defaultTitle' => __( 'SeedProd', 'coming-soon' ),
	);

	wp_localize_script( 'seedprod-gutenberg-button', 'seedprodGutenbergStrings', $translations );

	// Localize AJAX settings
	wp_localize_script(
		'seedprod-gutenberg-button',
		'seedprodGutenbergAjax',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'seedprod_gutenberg_nonce' ),
		)
	);

	// Add inline JavaScript
	wp_add_inline_script(
		'seedprod-gutenberg-button',
		'(function($, wp) {
			"use strict";

			var SeedProdGutenberg = {
				buttonAdded: false,

				init: function() {
					this.addButton();

					// Re-add button when Gutenberg re-renders (but only if it was removed)
					var self = this;
					wp.data.subscribe(function() {
						setTimeout(function() {
							// Only try to add if button was previously added but is now missing
							if (self.buttonAdded && !$("#seedprod-edit-button").length) {
								self.addButton();
							} else if (!self.buttonAdded) {
								// First time, try to add
								self.addButton();
							}
						}, 1);
					});
				},

				addButton: function() {
					var toolbar = $(".edit-post-header-toolbar");

					// Check if button already exists
					if ($("#seedprod-edit-button").length) {
						return;
					}

					// Check if toolbar exists
					if (!toolbar.length) {
						return;
					}

					this.buttonAdded = true;

					// Create button element (safer than HTML string)
					var button = $("<button>")
						.attr("id", "seedprod-edit-button")
						.attr("type", "button")
						.addClass("components-button is-primary")
						.css("margin-left", "10px")
						.html(
							\'<img src="' . esc_url( SEEDPROD_PLUGIN_URL . 'public/svg/admin-bar-icon.svg' ) . '" style="margin-right: 7px; width: 20px; height: 20px; vertical-align: middle;" />\' +
							\'<span>\' + seedprodGutenbergStrings.buttonText + \'</span>\'
						);

					toolbar.append(button);

					// Bind click event
					button.on("click", function(e) {
						e.preventDefault();
						SeedProdGutenberg.handleClick();
					});
				},

				handleClick: function() {
					// Get post data
					var currentPost = wp.data.select("core/editor").getCurrentPost();
					var postStatus = currentPost.status;
					var postId = currentPost.id;

					// Get the edited title (what user typed), not the saved title
					var postTitle = wp.data.select("core/editor").getEditedPostAttribute("title");
					var isAutoDraft = postStatus === "auto-draft";

					// Check if new unsaved post
					if (isAutoDraft) {
						// Set title only if empty
						if (!postTitle || postTitle.trim() === "") {
							var newTitle = seedprodGutenbergStrings.defaultTitle + " #" + postId;
							wp.data.dispatch("core/editor").editPost({ title: newTitle });
						}

						// Save the post
						wp.data.dispatch("core/editor").savePost();

						// Wait for save to complete
						this.waitForSave();
					} else {
						this.getRedirectUrl(postId);
					}
				},

				waitForSave: function() {
					var self = this;

					var checkSave = function() {
						var isSaving = wp.data.select("core/editor").isSavingPost();

						if (isSaving) {
							setTimeout(checkSave, 300);
						} else {
							// Save completed - get updated post data
							var savedPost = wp.data.select("core/editor").getCurrentPost();

							// Now get redirect URL from server
							self.getRedirectUrl(savedPost.id);
						}
					};

					setTimeout(checkSave, 300);
				},

				getRedirectUrl: function(postId) {
					$.ajax({
						url: seedprodGutenbergAjax.ajaxUrl,
						type: "POST",
						data: {
							action: "seedprod_lite_v2_get_redirect_url",
							nonce: seedprodGutenbergAjax.nonce,
							post_id: postId
						},
						success: function(response) {
							if (response.success) {
								// Redirect to SeedProd
								location.href = response.data.redirect_url;
							} else {
								console.error("Error:", response.data.message);
								alert("Error: " + response.data.message);
							}
						},
						error: function(xhr, status, error) {
							console.error("AJAX Error:", status, error);
							alert("Failed to get redirect URL. Check console for details.");
						}
					});
				}
			};

			// Initialize when DOM is ready
			$(function() {
				SeedProdGutenberg.init();
			});

		})(jQuery, wp);'
	);
}
add_action( 'enqueue_block_editor_assets', 'seedprod_lite_v2_enqueue_toolbar_script' );


/**
 * ============================================================================
 * SECTION 3: AJAX Handlers
 * ============================================================================
 */

/**
 * Get redirect URL for "Edit with SeedProd" button
 * Determines whether to go to template picker or builder based on:
 * - Existing SeedProd meta
 * - Theme builder status
 */
function seedprod_lite_v2_get_redirect_url() {
	// Verify nonce
	check_ajax_referer( 'seedprod_gutenberg_nonce', 'nonce' );

	// Get post ID
	$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

	if ( ! $post_id ) {
		wp_send_json_error( array( 'message' => __( 'Invalid post ID', 'coming-soon' ) ) );
	}

	// Check user can edit this post
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied', 'coming-soon' ) ) );
	}

	// Check if theme builder is enabled first
	$theme_enabled = get_option( 'seedprod_theme_enabled' );

	// Check if page already has SeedProd meta
	$is_seedprod_page           = get_post_meta( $post_id, '_seedprod_page', true );
	$is_edited_with_seedprod    = get_post_meta( $post_id, '_seedprod_edited_with_seedprod', true );
	$has_seedprod_content       = ! empty( get_post_field( 'post_content_filtered', $post_id ) );

	$already_seedprod = ( '1' === $is_seedprod_page ) ||
	                     ( '1' === $is_edited_with_seedprod ) ||
	                     $has_seedprod_content;

	// If already a SeedProd page and theme builder is enabled, ensure proper meta
	if ( $already_seedprod && ! empty( $theme_enabled ) ) {
		// Get the post to check for new WordPress content
		$post = get_post( $post_id );

		// Check if there's WordPress content that needs to be merged
		if ( ! empty( $post->post_content ) ) {
			// Load existing SeedProd template
			$existing_data = json_decode( $post->post_content_filtered, true );

			// Count total blocks to see if we need to merge content
			$total_blocks = 0;
			if ( ! empty( $existing_data['document']['sections'] ) && is_array( $existing_data['document']['sections'] ) ) {
				foreach ( $existing_data['document']['sections'] as $section ) {
					if ( isset( $section['rows'] ) && is_array( $section['rows'] ) ) {
						foreach ( $section['rows'] as $row ) {
							if ( isset( $row['cols'] ) && is_array( $row['cols'] ) ) {
								foreach ( $row['cols'] as $col ) {
									if ( isset( $col['blocks'] ) && is_array( $col['blocks'] ) ) {
										$total_blocks += count( $col['blocks'] );
									}
								}
							}
						}
					}
				}
			}

			// Only merge if there are no blocks yet
			if ( $total_blocks === 0 ) {
				// Load basic template if sections are empty
				if ( empty( $existing_data['document']['sections'] ) ) {
					require_once SEEDPROD_PLUGIN_PATH . 'resources/data-templates/basic-page.php';
					$basic_data                            = json_decode( $seedprod_basic_lpage, true );
					$existing_data['document']['sections'] = $basic_data['document']['sections'];
				}

				// Create an HTML block with the existing content
				$html_block = array(
					'id'       => 'sp-' . wp_generate_password( 6, false ),
					'type'     => 'htmlblock',
					'settings' => array(
						'html_content' => $post->post_content,
					),
				);

				// Add the HTML block to the first section's first row
				if ( isset( $existing_data['document']['sections'][0]['rows'][0] ) ) {
					if ( ! isset( $existing_data['document']['sections'][0]['rows'][0]['cols'][0] ) ) {
						$existing_data['document']['sections'][0]['rows'][0]['cols'][0] = array(
							'id'       => 'sp-' . wp_generate_password( 6, false ),
							'blocks'   => array(),
							'settings' => array(),
						);
					}
					$existing_data['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][] = $html_block;

					// Save updated template data
					// Use wp_slash to prevent wp_update_post from stripping slashes that wp_json_encode adds
					wp_update_post(
						array(
							'ID'                    => $post_id,
							'post_content_filtered' => wp_slash( wp_json_encode( $existing_data ) ),
						)
					);
				}
			}
		}

		// Make sure it's marked as a theme page, not a landing page
		update_post_meta( $post_id, '_seedprod_edited_with_seedprod', '1' );
		delete_post_meta( $post_id, '_seedprod_page' );

		$redirect_url = admin_url() . 'admin.php?page=seedprod_lite_builder&id=' . $post_id . '#/setup/' . $post_id;

		wp_send_json_success(
			array(
				'redirect_url' => $redirect_url,
				'reason'       => 'existing_seedprod_page_with_theme_enabled',
				'theme_status' => $theme_enabled,
			)
		);
	}

	// If already a SeedProd page (and theme NOT enabled), go directly to builder
	if ( $already_seedprod ) {
		$redirect_url = admin_url() . 'admin.php?page=seedprod_lite_builder&id=' . $post_id . '#/setup/' . $post_id;

		wp_send_json_success(
			array(
				'redirect_url' => $redirect_url,
				'reason'       => 'existing_seedprod_page',
				'meta'         => array(
					'is_seedprod_page'        => $is_seedprod_page,
					'is_edited_with_seedprod' => $is_edited_with_seedprod,
					'has_content_filtered'    => $has_seedprod_content,
				),
			)
		);
	}

	// New page - check if theme builder is enabled
	if ( ! empty( $theme_enabled ) ) {
		// Theme builder is enabled - seed the page with basic template
		// This replicates the logic from builder.php lines 189-197

		// Get the post to preserve title and copy content
		$post = get_post( $post_id );

		// Load basic template (same as builder.php does)
		require_once SEEDPROD_PLUGIN_PATH . 'resources/data-templates/basic-page.php';
		$settings                            = json_decode( $seedprod_basic_lpage, true );
		$settings['page_type']               = 'post'; // Set to 'post' for theme pages
		$settings['from_edit_with_seedprod'] = true;

		// Preserve the existing title and slug
		$settings['post_title'] = $post->post_title;
		$settings['post_name']  = $post->post_name;

		// Copy existing WordPress content if it exists
		// This uses the same logic as lpage.php lines 1075-1084
		if ( ! empty( $post->post_content ) ) {
			// Load the special current_content template (decode as array to match $settings structure)
			$current_content                  = $post->post_content;
			$settings['document']['sections'] = json_decode( $seedprod_current_content, true );

			// Set the text block content (strip HTML comments with s modifier to handle multiline comments)
			$clean_content = preg_replace( '/<!--(.*?)-->/s', '', $current_content );

			// Verify the structure exists before setting content
			if ( isset( $settings['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][0]['settings'] ) ) {
				$settings['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][0]['settings']['txt'] = $clean_content;
			}
		}

		// Save template data to post_content_filtered
		// Use wp_slash to prevent wp_update_post from stripping slashes that wp_json_encode adds
		wp_update_post(
			array(
				'ID'                    => $post_id,
				'post_content_filtered' => wp_slash( wp_json_encode( $settings ) ),
			)
		);

		// Mark as theme page
		update_post_meta( $post_id, '_seedprod_edited_with_seedprod', '1' );
		delete_post_meta( $post_id, '_seedprod_page' );

		// Redirect to builder (no from=post needed since we pre-seeded)
		$redirect_url = admin_url() . 'admin.php?page=seedprod_lite_builder&id=' . $post_id . '#/setup/' . $post_id;

		wp_send_json_success(
			array(
				'redirect_url' => $redirect_url,
				'reason'       => 'theme_builder_enabled',
				'theme_status' => $theme_enabled,
				'seeded'       => true,
			)
		);
	} else {
		// Theme builder NOT enabled - send to template picker for landing page
		$nonce        = wp_create_nonce( 'seedprod_nonce' );
		$redirect_url = admin_url() . 'admin.php?page=seedprod_lite_template&_wpnonce=' . $nonce . '&from=post&id=' . $post_id . '#/template/' . $post_id;

		wp_send_json_success(
			array(
				'redirect_url' => $redirect_url,
				'reason'       => 'create_landing_page',
				'theme_status' => false,
			)
		);
	}
}
add_action( 'wp_ajax_seedprod_lite_v2_get_redirect_url', 'seedprod_lite_v2_get_redirect_url' );


/**
 * Remove SeedProd post meta when user clicks "Back to WordPress Editor"
 */
function seedprod_lite_v2_remove_post() {
	// TODO: Add logic here
}
add_action( 'wp_ajax_seedprod_lite_v2_remove_post', 'seedprod_lite_v2_remove_post' );


/**
 * ============================================================================
 * SECTION 4: Admin Bar Menu Item
 * ============================================================================
 */

/**
 * Add "SeedProd Landing Page" to "+ New" menu in admin bar
 */
function seedprod_lite_v2_add_menu_item( $wp_admin_bar ) {
	// TODO: Add logic here
}
add_action( 'admin_bar_menu', 'seedprod_lite_v2_add_menu_item', 80 );


/**
 * ============================================================================
 * SECTION 5: Page Row Actions
 * ============================================================================
 */

/**
 * Add "Edit with SeedProd" link to Pages list table
 */
function seedprod_lite_v2_filter_page_row_actions( $actions, $post ) {
	// Check for SeedProd pages
	$is_landing_page = get_post_meta( $post->ID, '_seedprod_page', true );
	$is_theme_page   = get_post_meta( $post->ID, '_seedprod_edited_with_seedprod', true );

	// If it's a SeedProd page, add "Edit with SeedProd" link
	if ( '1' === $is_landing_page || '1' === $is_theme_page ) {
		$edit_url = admin_url() . 'admin.php?page=seedprod_lite_builder&id=' . $post->ID . '#/setup/' . $post->ID;

		// Add the link before the Trash action
		$actions['edit_seedprod'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $edit_url ),
			__( 'Edit with SeedProd', 'coming-soon' )
		);
	}

	return $actions;
}
add_filter( 'page_row_actions', 'seedprod_lite_v2_filter_page_row_actions', 11, 2 );


/**
 * ============================================================================
 * SECTION 6: Page State Label
 * ============================================================================
 */

/**
 * Add "Edit with SeedProd" button to Classic Editor
 * Shows button in the publish meta box for classic editor
 */
function seedprod_lite_v2_classic_editor_button() {
	$screen = get_current_screen();

	// Only run on page editor
	if ( ! $screen || 'page' !== $screen->post_type ) {
		return;
	}

	// Check user permissions
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	// Check if we should show the button (same logic as Gutenberg)
	$theme_enabled = get_option( 'seedprod_theme_enabled' );

	$post_id                 = get_the_ID();
	$is_seedprod_page        = get_post_meta( $post_id, '_seedprod_page', true );
	$is_edited_with_seedprod = get_post_meta( $post_id, '_seedprod_edited_with_seedprod', true );
	$is_already_seedprod     = ( '1' === $is_seedprod_page ) || ( '1' === $is_edited_with_seedprod );

	// Only show button if theme builder is enabled OR page is already SeedProd
	if ( empty( $theme_enabled ) && ! $is_already_seedprod ) {
		return;
	}

	?>
	<div class="misc-pub-section seedprod-classic-editor-button" style="padding: 10px; border-top: 1px solid #ddd;">
		<button type="button" id="seedprod-classic-edit-button" class="button button-primary button-large" style="width: 100%;">
			<img src="<?php echo esc_url( SEEDPROD_PLUGIN_URL . 'public/svg/admin-bar-icon.svg' ); ?>" style="width: 20px; height: 20px; vertical-align: middle; margin-right: 7px;" />
			<?php esc_html_e( 'Edit with SeedProd', 'coming-soon' ); ?>
		</button>
	</div>

	<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('#seedprod-classic-edit-button').on('click', function(e) {
			e.preventDefault();

			var postId = <?php echo absint( $post_id ); ?>;
			var postStatus = '<?php echo esc_js( get_post_status( $post_id ) ); ?>';
			var isAutoDraft = postStatus === 'auto-draft';

			if (isAutoDraft) {
				// For new posts, save first then redirect
				// Get the title
				var title = $('#title').val();
				if (!title) {
					title = 'SeedProd #' + postId;
					$('#title').val(title);
				}

				// Trigger save
				$('#publish').click();

				// Wait a moment then redirect
				setTimeout(function() {
					getRedirectUrl(postId);
				}, 1000);
			} else {
				// Existing post - redirect immediately
				getRedirectUrl(postId);
			}
		});

		function getRedirectUrl(postId) {
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'seedprod_lite_v2_get_redirect_url',
					nonce: '<?php echo esc_js( wp_create_nonce( 'seedprod_gutenberg_nonce' ) ); ?>',
					post_id: postId
				},
				success: function(response) {
					if (response.success) {
						location.href = response.data.redirect_url;
					} else {
						console.error('Error:', response.data.message);
						alert('Error: ' + response.data.message);
					}
				},
				error: function(xhr, status, error) {
					console.error('AJAX Error:', status, error);
					alert('Failed to get redirect URL. Check console for details.');
				}
			});
		}
	});
	</script>
	<?php
}
add_action( 'post_submitbox_misc_actions', 'seedprod_lite_v2_classic_editor_button' );


/**
 * Add "SeedProd" label to page list
 * Shows "SeedProd" for theme pages, "SeedProd Landing Page" for landing pages
 *
 * @param string[] $post_states An array of post display states.
 * @param WP_Post  $post        The current post object.
 * @return string[] $post_states An array of post display states.
 */
function seedprod_lite_v2_add_post_state( $post_states, $post ) {
	// Only for pages
	if ( 'page' !== $post->post_type ) {
		return $post_states;
	}

	// Check for theme page (_seedprod_edited_with_seedprod)
	$is_theme_page = get_post_meta( $post->ID, '_seedprod_edited_with_seedprod', true );
	if ( ! empty( $is_theme_page ) ) {
		$post_states['seedprod-editor'] = __( 'SeedProd', 'coming-soon' );
		return $post_states;
	}

	// Check for landing page (_seedprod_page)
	$is_landing_page = get_post_meta( $post->ID, '_seedprod_page', true );
	if ( ! empty( $is_landing_page ) ) {
		$post_states['seedprod'] = __( 'SeedProd Landing Page', 'coming-soon' );
		return $post_states;
	}

	return $post_states;
}
add_filter( 'display_post_states', 'seedprod_lite_v2_add_post_state', 10, 2 );
