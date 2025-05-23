<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

use Automattic\WooCommerce\Blocks\Utils\StyleAttributesUtils;

/**
 * ProductPrice class.
 */
class ProductPrice extends AbstractBlock {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'product-price';

	/**
	 * API version name.
	 *
	 * @var string
	 */
	protected $api_version = '3';

	/**
	 * Get the frontend style handle for this block type.
	 *
	 * @return null
	 */
	protected function get_block_type_style() {
		return null;
	}

	/**
	 * Overwrite parent method to prevent script registration.
	 *
	 * It is necessary to register and enqueues assets during the render
	 * phase because we want to load assets only if the block has the content.
	 */
	protected function register_block_type_assets() {
		return null;
	}

	/**
	 * Register the context.
	 */
	protected function get_block_type_uses_context() {
		return [ 'query', 'queryId', 'postId' ];
	}

	/**
	 * Include and render the block.
	 *
	 * @param array    $attributes Block attributes. Default empty array.
	 * @param string   $content    Block content. Default empty string.
	 * @param WP_Block $block      Block instance.
	 * @return string Rendered block type output.
	 */
	protected function render( $attributes, $content, $block ) {
		if ( ! empty( $content ) ) {
			parent::register_block_type_assets();
			$this->register_chunk_translations( [ $this->block_name ] );
			return $content;
		}

		$post_id = isset( $block->context['postId'] ) ? $block->context['postId'] : '';
		$product = wc_get_product( $post_id );

		if ( $product ) {
			$styles_and_classes            = StyleAttributesUtils::get_classes_and_styles_by_attributes( $attributes );
			$text_align_styles_and_classes = StyleAttributesUtils::get_text_align_class_and_style( $attributes );

			return sprintf(
				'<div class="wp-block-woocommerce-product-price"><div class="wc-block-components-product-price wc-block-grid__product-price %1$s %2$s" style="%3$s">
					%4$s
				</div></div>',
				esc_attr( $text_align_styles_and_classes['class'] ?? '' ),
				esc_attr( $styles_and_classes['classes'] ),
				esc_attr( $styles_and_classes['styles'] ?? '' ),
				$product->get_price_html()
			);
		}
	}
}
