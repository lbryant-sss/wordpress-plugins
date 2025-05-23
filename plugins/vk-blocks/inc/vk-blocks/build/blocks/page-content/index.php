<?php
/**
 * Page Content Block
 *
 * @package VK Blocks
 */

/**
 * Registers the `vk-blocks/page-content` block.
 *
 * @return void
 */
function vk_blocks_register_block_page_content() {
	global $vk_blocks_common_attributes;
	register_block_type(
		__DIR__,
		array(
			'editor_style'    => 'vk-blocks-build-editor-css',
			'editor_script'   => 'vk-blocks-build-js',
			'attributes'      => array_merge(
				array(
					'className'  => array(
						'type'    => 'string',
						'default' => '',
					),
					'TargetPost' => array(
						'type'    => 'number',
						'default' => -1,
					),
				),
				$vk_blocks_common_attributes
			),
			'render_callback' => 'vk_blocks_page_content_render_callback',
		)
	);
}
add_action( 'init', 'vk_blocks_register_block_page_content', 99 );

// Add fiter for render post content
add_filter( 'vk_page_content', 'do_blocks', 9 );
add_filter( 'vk_page_content', 'wptexturize' );
add_filter( 'vk_page_content', 'convert_smilies', 20 );
add_filter( 'vk_page_content', 'shortcode_unautop' );
add_filter( 'vk_page_content', 'prepend_attachment' );
add_filter( 'vk_page_content', 'wp_filter_content_tags' );
add_filter( 'vk_page_content', 'do_shortcode', 11 );
add_filter( 'vk_page_content', 'capital_P_dangit', 11 );

/**
 * Get Page Content Private Alert Message
 *
 * @return string
 */
function vk_blocks_get_page_content_private_alert() {
	$alert  = __( "The Page Content block from VK Blocks version 1.95.0 onwards, non-public or password protected page's content can no longer be displayed.", 'vk-blocks' );
	$alert .= __( 'If you want to display non-public content in multiple locations, please create it as a Synced pattern(Reusable block) and place it in the desired locations instead of using Page Content block.', 'vk-blocks' );
	return $alert;
}

/**
 * Render Callback of Page Content Block
 *
 * @param array $attributes attributes.
 * @return string
 */
function vk_blocks_page_content_render_callback( $attributes ) {
	$page_content_id = ! empty( $attributes['TargetPost'] ) ? $attributes['TargetPost'] : -1;
	$post            = get_post( $page_content_id );

	$is_rest_request = defined( 'REST_REQUEST' ) && REST_REQUEST;

	// 投稿が存在し、公開されているか、またはパスワード保護されていないかを確認
	if ( ! $post || 'publish' !== $post->post_status || ! empty( $post->post_password ) ) {
		if ( is_admin() || $is_rest_request ) {
			return '<div class="alert alert-danger" style="padding:1.5rem;"><p class="text-center" style="font-weight:bold;">' . __( 'Post not found, not public, or password protected', 'vk-blocks' ) . '</p><p class="mb-0">' . vk_blocks_get_page_content_private_alert() . '</p></div>';
		} else {
			// Front Page
			return '';
		}
	}

	$page_content = $post->post_content;
	vk_blocks_content_enqueue_scripts( $page_content );

	$vk_blocks_options = VK_Blocks_Options::get_options();
	if ( has_block( 'vk-blocks/faq2', $page_content ) || has_block( 'vk-blocks/faq', $page_content ) ) {
		if ( 'open' === $vk_blocks_options['new_faq_accordion'] ) {
			$page_content = str_replace( '[accordion_trigger_switch]', 'vk_faq-accordion vk_faq-accordion-open', $page_content );
		} elseif ( 'close' === $vk_blocks_options['new_faq_accordion'] ) {
			$page_content = str_replace( '[accordion_trigger_switch]', 'vk_faq-accordion vk_faq-accordion-close', $page_content );
		} else {
			$page_content = str_replace( '[accordion_trigger_switch]', '', $page_content );
		}
	}
	$page_content = str_replace( '[br-xs]', '<br class="vk_responsive-br vk_responsive-br-xs"/>', $page_content );
	$page_content = str_replace( '[br-sm]', '<br class="vk_responsive-br vk_responsive-br-sm"/>', $page_content );
	$page_content = str_replace( '[br-md]', '<br class="vk_responsive-br vk_responsive-br-md"/>', $page_content );
	$page_content = str_replace( '[br-lg]', '<br class="vk_responsive-br vk_responsive-br-lg"/>', $page_content );
	$page_content = str_replace( '[br-xl]', '<br class="vk_responsive-br vk_responsive-br-xl"/>', $page_content );
	$page_content = str_replace( '[br-xxl]', '<br class="vk_responsive-br vk_responsive-br-xxl"/>', $page_content );

	$classes   = '';
	$page_html = '';

	if ( -1 !== $page_content_id ) {
		$classes .= 'vk_pageContent';
		if ( isset( $attributes['TargetPost'] ) ) {
			$classes .= ' vk_pageContent-id-' . $page_content_id;
		}
		if ( isset( $attributes['vkb_hidden'] ) && $attributes['vkb_hidden'] ) {
			$classes .= ' vk_hidden';
		}
		if ( isset( $attributes['vkb_hidden_xxl'] ) && $attributes['vkb_hidden_xxl'] ) {
			$classes .= ' vk_hidden-xxl';
		}
		if ( isset( $attributes['vkb_hidden_xl_v2'] ) && $attributes['vkb_hidden_xl_v2'] ) {
			$classes .= ' vk_hidden-xl';
		}
		if ( isset( $attributes['vkb_hidden_lg'] ) && $attributes['vkb_hidden_lg'] ) {
			$classes .= ' vk_hidden-lg';
		}
		if ( isset( $attributes['vkb_hidden_md'] ) && $attributes['vkb_hidden_md'] ) {
			$classes .= ' vk_hidden-md';
		}
		if ( isset( $attributes['vkb_hidden_sm'] ) && $attributes['vkb_hidden_sm'] ) {
			$classes .= ' vk_hidden-sm';
		}
		if ( isset( $attributes['vkb_hidden_xs'] ) && $attributes['vkb_hidden_xs'] ) {
			$classes .= ' vk_hidden-xs';
		}
		if ( isset( $attributes['marginTop'] ) && $attributes['marginTop'] ) {
			$classes .= ' ' . $attributes['marginTop'];
		}
		if ( isset( $attributes['marginBottom'] ) && $attributes['marginBottom'] ) {
			$classes .= ' ' . $attributes['marginBottom'];
		}

		$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $classes ) );

		$page_html .= '<div ' . $wrapper_attributes . '>';
		// Warning : 'vk_page_content' is old hook name that this line is old filter name fall back.
		$page_content = apply_filters( 'vk_page_content', $page_content ); //phpcs:ignore
		$page_html   .= apply_filters( 'vk_blocks_page_content', $page_content );
		$page_html   .= '</div>';

		$url = get_edit_post_link( $page_content_id );
		if ( $url ) {
			$page_html .= '<a href="' . esc_url( $url ) . '" class="vk_pageContent_editBtn btn btn-outline-primary btn-sm veu_adminEdit" target="_blank">' . __( 'Edit this area', 'vk-blocks' ) . '</a>';
		}
	}

	return $page_html;
}


/**
 * Load Scripts
 *
 * @param string $page_content Contents.
 */
function vk_blocks_content_enqueue_scripts( $page_content ) {
	if ( has_block( 'vk-blocks/faq2', $page_content ) || has_block( 'vk-blocks/faq', $page_content ) ) {
		wp_enqueue_script( 'vk-blocks-faq2', VK_BLOCKS_DIR_URL . 'build/faq2.min.js', array(), VK_BLOCKS_VERSION, true );
	}
	if ( has_block( 'vk-blocks/animation', $page_content ) ) {
		wp_enqueue_script( 'vk-blocks-animation', VK_BLOCKS_DIR_URL . 'build/vk-animation.min.js', array(), VK_BLOCKS_VERSION, true );
	}
	if ( has_block( 'vk-blocks/slider', $page_content ) ) {
		wp_enqueue_style( 'vk-blocks-swiper', VK_BLOCKS_DIR_URL . 'build/swiper.min.css', array(), VK_BLOCKS_VERSION );
		wp_enqueue_script( 'vk-blocks-swiper', VK_BLOCKS_DIR_URL . 'build/swiper.min.js', array(), VK_BLOCKS_VERSION, true );
		wp_enqueue_script( 'vk-blocks-slider', VK_BLOCKS_DIR_URL . 'build/vk-slider.min.js', array( 'vk-blocks-swiper' ), VK_BLOCKS_VERSION, true );
	}
}
add_action( 'wp_enqueue_scripts', 'vk_blocks_content_enqueue_scripts' );

// 非公開の投稿を参照して表示していないかのチェック
// Check if it is displaying content from non-public pages.
require_once plugin_dir_path( __FILE__ ) . 'class-vk-blocks-check-using-vk-page-content-block.php';
VK_Blocks_Check_Using_VK_Page_Content_Block::activate();
