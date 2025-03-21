<?php

namespace JupiterX_Core\Raven\Modules\Cart\Widgets;

use JupiterX_Core\Raven\Base\Base_Widget;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

defined( 'ABSPATH' ) || die();

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Cart extends Base_Widget {
	public function get_name() {
		return 'raven-cart';
	}

	public function get_title() {
		return esc_html__( 'Cart', 'jupiterx-core' );
	}


	public function get_icon() {
		return 'raven-element-icon raven-element-icon-cart';
	}

	public function get_script_depends() {
		return [
			'wc-cart',
			'selectWoo',
		];
	}

	public function get_style_depends() {
		return [ 'select2' ];
	}

	protected function register_controls() {
		$this->register_section_content();
		$this->register_sections_style();
		$this->regsiter_typography_style();
		$this->register_button_style();
		$this->register_forms_fields_style();
		$this->register_cart_divider_style();
		$this->register_cart_remove_link_style();
		$this->register_cart_customize_style();
		$this->register_cart_table_heading_style();
		$this->register_cart_quantity_style();
		$this->register_cart_coupon_style();
		$this->register_cart_thumbnails_style();
		$this->register_cart_update_cart_button_style();
	}

	protected function register_section_content() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'General', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'cart_layout',
			[
				'label' => esc_html__( 'Layout', 'jupiterx-core' ),
				'type' => 'select',
				'options' => [
					'one-column' => esc_html__( 'One column', 'jupiterx-core' ),
					'two-column' => esc_html__( 'Two columns', 'jupiterx-core' ),
				],
				'default' => 'two-column',
				'prefix_class' => 'raven-cart-layout-',
			]
		);

		$this->add_control(
			'cart_vertical_alignment',
			[
				'label' => esc_html__( 'Vertical Alignment', 'jupiterx-core' ),
				'type' => 'select',
				'options' => [
					'top' => esc_html__( 'Top', 'jupiterx-core' ),
					'middle' => esc_html__( 'Middle', 'jupiterx-core' ),
				],
				'default' => 'top',
				'prefix_class' => 'raven-cart-vertical-align-',
				'condition' => [
					'cart_layout' => 'two-column',
				],
			]
		);

		$this->add_responsive_control(
			'cart_main_column_size',
			[
				'label' => esc_html__( 'Main Column Size', 'jupiterx-core' ),
				'type' => 'select',
				'default' => '70%',
				'options' => [
					'20%' => esc_html__( '20%', 'jupiterx-core' ),
					'30%' => esc_html__( '30%', 'jupiterx-core' ),
					'40%' => esc_html__( '40%', 'jupiterx-core' ),
					'50%' => esc_html__( '50%', 'jupiterx-core' ),
					'60%' => esc_html__( '60%', 'jupiterx-core' ),
					'70%' => esc_html__( '70%', 'jupiterx-core' ),
					'80%' => esc_html__( '80%', 'jupiterx-core' ),
					'90%' => esc_html__( '90%', 'jupiterx-core' ),
				],
				'selectors' => [
					'{{WRAPPER}}' => '--main-column-size: {{VALUE}};',
				],
				'condition' => [
					'cart_layout' => 'two-column',
				],
			]
		);

		$this->add_control(
			'general_cart_heading',
			[
				'label' => esc_html__( 'Cart', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cart_heading',
			[
				'label' => esc_html__( 'Heading', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'cart_table_heading',
			[
				'label' => esc_html__( 'Table Header', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'cart_items_thumbnail',
			[
				'label' => esc_html__( 'Thumbnail', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'prefix_class' => 'raven-cart-items-thumbnail-',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'cart_items_layout',
			[
				'label' => esc_html__( 'Show Remove Link as', 'jupiterx-core' ),
				'type' => 'select',
				'options' => [
					'standard' => esc_html__( 'Icon', 'jupiterx-core' ),
					'compact' => esc_html__( 'Text', 'jupiterx-core' ),
				],
				'default' => 'standard',
				'prefix_class' => 'raven-cart-items-layout-',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'cart_coupon',
			[
				'label' => esc_html__( 'Coupon Form', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'general_cart_totals_heading',
			[
				'label' => esc_html__( 'Cart totals', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cart_total_heading',
			[
				'label' => esc_html__( 'Heading', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'cart_items_continue_shopping',
			[
				'label' => esc_html__( 'Continue Shopping Button', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'render_type' => 'template',
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_custom_texts',
			[
				'label' => esc_html__( 'Custom Texts', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'apply_coupon_button_text',
			[
				'label' => esc_html__( 'Apply Coupon Button', 'jupiterx-core' ),
				'label_block' => true,
				'type' => 'text',
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Apply Coupon', 'jupiterx-core' ),
				'default' => esc_html__( 'Apply Coupon', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'update_cart_button_text',
			[
				'label' => esc_html__( 'Update Cart Button', 'jupiterx-core' ),
				'label_block' => true,
				'type' => 'text',
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Update Cart', 'jupiterx-core' ),
				'default' => esc_html__( 'Update Cart', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'update_shipping_button_text',
			[

				'label' => esc_html__( 'Update Shipping Button', 'jupiterx-core' ),
				'label_block' => true,
				'type' => 'text',
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Update', 'jupiterx-core' ),
				'default' => esc_html__( 'Update', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'totals_section_title',
			[
				'label' => esc_html__( 'Cart Total Title', 'jupiterx-core' ),
				'label_block' => true,
				'type' => 'text',
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Cart Totals', 'jupiterx-core' ),
				'default' => esc_html__( 'Cart Totals', 'jupiterx-core' ),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'checkout_button_text',
			[
				'label' => esc_html__( 'Checkout Button', 'jupiterx-core' ),
				'label_block' => true,
				'type' => 'text',
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Proceed to Checkout', 'jupiterx-core' ),
				'default' => esc_html__( 'Proceed to Checkout', 'jupiterx-core' ),
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();
	}

	protected function register_sections_style() {
		$this->start_controls_section(
			'section_cart_style',
			[
				'label' => esc_html__( 'Sections', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'first_sections_heading',
			[
				'label' => esc_html__( 'Cart', 'jupiterx-core' ),
				'type' => 'heading',
			]
		);

		$this->add_control(
			'first_sections_background_color',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'hidden',
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .raven-cart__column-start .raven-cart-section' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name'      => 'first_sections_background_type',
				'label'     => esc_html__( 'Background Type', 'jupiterx-core' ),
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .raven-cart__column-start .raven-cart-section',
			]
		);

		$this->add_responsive_control(
			'first_sections_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '27',
					'right' => '17',
					'bottom' => '27',
					'left' => '17',
					'unit' => 'px',
				],
				'tablet_default' => [
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-cart__column-start .raven-cart-section' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'first_sections_margin',
			[
				'label' => esc_html__( 'Margin', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-cart__column-start .raven-cart-section' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'first_sections_border_type',
			[
				'label'     => esc_html__( 'Border Type', 'jupiterx-core' ),
				'type'      => 'select',
				'options'   => [
					'none'   => esc_html__( 'None', 'jupiterx-core' ),
					'solid'  => esc_html__( 'Solid', 'jupiterx-core' ),
					'double' => esc_html__( 'Double', 'jupiterx-core' ),
					'dotted' => esc_html__( 'Dotted', 'jupiterx-core' ),
					'dashed' => esc_html__( 'Dashed', 'jupiterx-core' ),
					'groove' => esc_html__( 'Groove', 'jupiterx-core' ),
				],
				'default'   => 'none',
				'selectors' => [
					'{{WRAPPER}} .raven-cart__column-start .raven-cart-section' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'first_sections_border_width',
			[
				'label'      => esc_html__( 'Width', 'jupiterx-core' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .raven-cart__column-start .raven-cart-section' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'first_sections_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'first_sections_border_color',
			[
				'label'     => esc_html__( 'Color', 'jupiterx-core' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-cart__column-start .raven-cart-section' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'first_sections_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'first_sections_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .raven-cart__column-start .raven-cart-section' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'first_sections_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .raven-cart__column-start .raven-cart-section',
			]
		);

		$this->add_control(
			'second_sections_heading',
			[
				'label' => esc_html__( 'Cart totals', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'second_sections_background_color',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .raven-cart__column-end .raven-cart-section' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'second_sections_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '27',
					'right' => '17',
					'bottom' => '27',
					'left' => '17',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-cart__column-end .raven-cart-section' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'second_sections_margin',
			[
				'label' => esc_html__( 'Margin', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-cart__column-end .raven-cart-section' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'second_sections_border_type',
			[
				'label'     => esc_html__( 'Border Type', 'jupiterx-core' ),
				'type'      => 'select',
				'options'   => [
					'none'   => esc_html__( 'None', 'jupiterx-core' ),
					'solid'  => esc_html__( 'Solid', 'jupiterx-core' ),
					'double' => esc_html__( 'Double', 'jupiterx-core' ),
					'dotted' => esc_html__( 'Dotted', 'jupiterx-core' ),
					'dashed' => esc_html__( 'Dashed', 'jupiterx-core' ),
					'groove' => esc_html__( 'Groove', 'jupiterx-core' ),
				],
				'default'   => 'none',
				'selectors' => [
					'{{WRAPPER}} .raven-cart__column-end .raven-cart-section' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'second_sections_border_width',
			[
				'label'      => esc_html__( 'Width', 'jupiterx-core' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .raven-cart__column-end .raven-cart-section' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'second_sections_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'second_sections_border_color',
			[
				'label'     => esc_html__( 'Color', 'jupiterx-core' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-cart__column-end .raven-cart-section' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'second_sections_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'second_sections_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .raven-cart__column-end .raven-cart-section' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'second_sections_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .raven-cart__column-end .raven-cart-section',
			]
		);

		$this->end_controls_section();
	}

	protected function regsiter_typography_style() {
		$this->start_controls_section(
			'section_cart_tabs_typography',
			[
				'label' => esc_html__( 'Typography', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'section_heaeding',
			[
				'type' => 'heading',
				'separator' => 'before',
				'label' => esc_html__( 'Heading', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'cart_heading_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#111111',
				'selectors' => [
					'{{WRAPPER}} .raven-cart__container h2' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'cart_heading_typography',
				'selector' => '{{WRAPPER}} .raven-cart__container h2',
			]
		);

		$this->add_responsive_control(
			'cart_heading_margin',
			[
				'label' => esc_html__( 'Margin', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '0',
					'right' => '0',
					'bottom' => '23',
					'left' => '0',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-cart__container h2' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'table_header_heaeding',
			[
				'type' => 'heading',
				'separator' => 'before',
				'label' => esc_html__( 'Header', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'table_header_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#888888',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-cart-form__contents th' => 'color: {{VALUE}};',
					'{{WRAPPER}} tr.cart_item td:before' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'table_header_typography',
				'selector' => '{{WRAPPER}} .woocommerce-cart-form__contents th, {{WRAPPER}} tr.cart_item td:before',
			]
		);

		$this->add_control(
			'sections_typography',
			[
				'type' => 'heading',
				'separator' => 'before',
				'label' => esc_html__( 'Product Name', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'sections_titles_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#111111',
				'selectors' => [
					'{{WRAPPER}} .woocommerce .product-name a' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .woocommerce .product-name a:hover' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .woocommerce .product-name .product-variations' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .woocommerce .product-name .product-variations span' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'sections_titles_typography',
				'selector' => '{{WRAPPER}} .woocommerce .product-name a, {{WRAPPER}} .woocommerce .product-name a:hover, {{WRAPPER}} .woocommerce .product-name .product-variations, {{WRAPPER}} .woocommerce .product-name .product-variations span',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'sections_descriptions_heading',
			[
				'type' => 'heading',
				'separator' => 'before',
				'label' => esc_html__( 'Descriptions', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'sections_descriptions_heading_description',
			[
				'raw' => esc_html__( 'Price, subtotal and shipping options.', 'jupiterx-core' ),
				'type' => 'raw_html',
				'content_classes' => 'elementor-control-field-description',
			]
		);

		$this->add_control(
			'sections_descriptions_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#111111',
				'selectors' => [
					'{{WRAPPER}} .woocommerce .woocommerce-cart-form__cart-item span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .cart-subtotal span > *' => 'color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce .cart_totals .woocommerce-shipping-methods li > *' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .woocommerce .cart_totals .woocommerce-shipping-methods li .amount' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .woocommerce .cart_totals .woocommerce-shipping-methods li .amount::before' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'sections_descriptions_typography',
				'selector' => '{{WRAPPER}} .woocommerce .woocommerce-cart-form__cart-item span,{{WRAPPER}} .woocommerce .cart_totals tr.woocommerce-shipping-totals #shipping_method label, {{WRAPPER}} .cart-subtotal span > *,{{WRAPPER}} .woocommerce .cart_totals tr.woocommerce-shipping-totals .woocommerce-shipping-methods li label > *,{{WRAPPER}} .woocommerce .cart_totals .woocommerce-shipping-methods li .amount,{{WRAPPER}} .woocommerce .cart_totals .woocommerce-shipping-methods li .amount::before',
			]
		);

		$this->add_control(
			'sections_links_title',
			[
				'type' => 'heading',
				'separator' => 'before',
				'label' => esc_html__( 'Links', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'links_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#1890FF',
				'selectors' => [
					'{{WRAPPER}} .cart_totals  .woocommerce-shipping-calculator a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'links_typography',
				'selector' => '{{WRAPPER}} .cart_totals .woocommerce-shipping-calculator a',
			]
		);

		$this->add_control(
			'sections_info_heading',
			[
				'type' => 'heading',
				'separator' => 'before',
				'label' => esc_html__( 'Info', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'info_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#888888',
				'selectors' => [
					'{{WRAPPER}} .cart_totals p,{{WRAPPER}} .woocommerce .cart_totals table.shop_table_responsive tr.woocommerce-shipping-totals td .woocommerce-shipping-destination' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'info_typography',
				'selector' => '{{WRAPPER}} .cart_totals p,{{WRAPPER}} .woocommerce .cart_totals table.shop_table_responsive tr.woocommerce-shipping-totals td .woocommerce-shipping-destination',
			]
		);

		$this->add_control(
			'sections_total_title',
			[
				'type' => 'heading',
				'label' => esc_html__( 'Total', 'jupiterx-core' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sections_total_color',
			[

				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#111111',
				'selectors' => [
					'{{WRAPPER}} .cart_totals .order-total > *' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'sections_total_typography',
				'selector' => '{{WRAPPER}} .cart_totals .order-total > *',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function register_button_style() {
		$this->start_controls_section(
			'section_cart_button',
			[
				'label' => esc_html__( 'Button', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'cart_primary_button_heading',
			[
				'type' => 'heading',
				'label' => esc_html__( 'Primary Button', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'cart_primary_button_typography',
				'selector' => '{{WRAPPER}} .wc-proceed-to-checkout .checkout-button',
			]
		);

		$this->start_controls_tabs( 'primary_tabs' );

		$this->start_controls_tab(
			'primary_button_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'primary_button_normal_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout .checkout-button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name'      => 'primary_button_normal_background',
				'label'     => esc_html__( 'Background Type', 'jupiterx-core' ),
				'types'     => [ 'classic', 'gradient' ],
				'fields_options' => [
					'color' => [
						'default' => '#111111',
					],
					'background' => [
						'default' => 'classic',
					],
				],
				'selector'  => '{{WRAPPER}} .wc-proceed-to-checkout .checkout-button',
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'primary_button_box_shadow',
				'selector' => '{{WRAPPER}} .wc-proceed-to-checkout .checkout-button',
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'primary_button_border',
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .wc-proceed-to-checkout .checkout-button',
			]
		);

		$this->add_responsive_control(
			'primary_button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout .checkout-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'primary_button_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '13',
					'right' => '29',
					'bottom' => '13',
					'left' => '29',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout .checkout-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'primary_button_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'primary_button_normal_color_hover',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout .checkout-button:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name' => 'primary_button_normal_background_hover',
				'label' => esc_html__( 'Background Type', 'jupiterx-core' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .wc-proceed-to-checkout .checkout-button:hover',
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'primary_button_box_shadow_hover',
				'selector' => '{{WRAPPER}} .wc-proceed-to-checkout .checkout-button:hover',
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'primary_button_border_hover',
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .wc-proceed-to-checkout .checkout-button:hover',
			]
		);

		$this->add_responsive_control(
			'primary_button_border_radius_hover',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout .checkout-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'primary_button_padding_hover',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout .checkout-button:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'cart_secondary_button_heading',
			[
				'type' => 'heading',
				'separator' => 'before',
				'label' => esc_html__( 'Secondary Buttons', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'cart_secondary_button_typography',
				'selector' => '{{WRAPPER}} .shop_table button,{{WRAPPER}} .cart_totals .wc-proceed-to-checkout a:not(.checkout-button)',
			]
		);

		$this->start_controls_tabs( 'secondary_tabs' );

		$this->start_controls_tab(
			'secondary_button_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'secondary_button_normal_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#666666',
				'selectors' => [
					'{{WRAPPER}} .shop_table button' => 'color: {{VALUE}}',
					'{{WRAPPER}} .shop_table button[name="update_cart"]:before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .cart_totals .wc-proceed-to-checkout a:not(.checkout-button)' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name'      => 'secondary_button_normal_background',
				'label'     => esc_html__( 'Background Type', 'jupiterx-core' ),
				'types'     => [ 'classic', 'gradient' ],
				'fields_options' => [
					'color' => [
						'default' => '#F1F1F1',
					],
					'background' => [
						'default' => 'classic',
					],
				],
				'selector' => '{{WRAPPER}} .shop_table button,{{WRAPPER}} .cart_totals .wc-proceed-to-checkout a:not(.checkout-button)',
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'secondary_button_box_shadow',
				'selector' => '{{WRAPPER}} .woocommerce table.shop_table.cart .actions button,{{WRAPPER}} .cart_totals .wc-proceed-to-checkout a:not(.checkout-button)',
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'secondary_button_border',
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .shop_table button,{{WRAPPER}} .cart_totals .wc-proceed-to-checkout a:not(.checkout-button)',
			]
		);

		$this->add_responsive_control(
			'secondary_button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => '5',
					'right' => '5',
					'bottom' => '5',
					'left' => '5',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .shop_table button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .cart_totals .wc-proceed-to-checkout a:not(.checkout-button)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'secondary_button_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '13',
					'right' => '22',
					'bottom' => '13',
					'left' => '22',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .shop_table button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .cart_totals .wc-proceed-to-checkout a:not(.checkout-button)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'secondary_button_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'secondary_button_normal_color_hover',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .shop_table button:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .shop_table button[name="update_cart"]:hover:before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .cart_totals .wc-proceed-to-checkout a:not(.checkout-button):hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name' => 'secondary_button_normal_background_hover',
				'label' => esc_html__( 'Background Type', 'jupiterx-core' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .shop_table button:hover,{{WRAPPER}} .cart_totals .wc-proceed-to-checkout a:not(.checkout-button):hover',
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'secondary_button_box_shadow_hover',
				'selector' => '{{WRAPPER}} .woocommerce table.shop_table.cart .actions button:hover,{{WRAPPER}} .cart_totals .wc-proceed-to-checkout a:not(.checkout-button):hover',
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'secondary_button_border_hover',
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .shop_table button:hover,{{WRAPPER}} .cart_totals .wc-proceed-to-checkout a:not(.checkout-button):hover',
			]
		);

		$this->add_responsive_control(
			'secondary_button_border_radius_hover',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .shop_table button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .cart_totals .wc-proceed-to-checkout a:not(.checkout-button):hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'secondary_button_padding_hover',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .shop_table button:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .cart_totals .wc-proceed-to-checkout a:not(.checkout-button):hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'cart_place_order_button_heading',
			[
				'type' => 'heading',
				'separator' => 'before',
				'label' => esc_html__( 'Checkout Button', 'jupiterx-core' ),
			]
		);

		$this->add_responsive_control(
			'cart_place_order_button_width',
			[
				'label' => esc_html__( 'Width', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ '%', 'px' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .wc-proceed-to-checkout .checkout-button' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function register_forms_fields_style() {
		$this->start_controls_section(
			'section_cart_tabs_forms',
			[
				'label' => esc_html__( 'Form Fields', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'section_cart_form_fields',
			[
				'type' => 'heading',
				'label' => esc_html__( 'Fields', 'jupiterx-core' ),
			]
		);

		$this->start_controls_tabs( 'forms_fields_tabs' );

		$this->start_controls_tab(
			'forms_fields_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'forms_fields_normal_color',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}}' => '--forms-fields-normal-background-color: {{VALUE}};',
					'.raven-woo-select2-wrapper .select2-results__option' => 'background-color: {{VALUE}};',
					// style select2 arrow
					'{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__arrow b' => 'border-color: {{VALUE}} transparent transparent transparent;',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'forms_fields_normal_border',
				'selector' => '{{WRAPPER}} table.shop_table.cart .actions .coupon input, {{WRAPPER}} .product-quantity .quantity input, {{WRAPPER}} .coupon .input-text, {{WRAPPER}} .cart-collaterals .input-text, {{WRAPPER}} select, {{WRAPPER}} .select2-selection--single, {{WRAPPER}} .input-text.qty .form-control',
			]
		);

		$this->add_responsive_control(
			'forms_fields_normal_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'default' => [
					'top' => '5',
					'right' => '5',
					'bottom' => '5',
					'left' => '5',
					'unit' => 'px',
				],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .cart-collaterals .input-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .select2-selection--single' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .select2-selection__rendered' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .input-text.qty .form-control' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .product-quantity .quantity input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} table.shop_table.cart .actions .coupon input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'forms_fields_normal_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .cart-collaterals .input-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .select2-selection--single' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .select2-selection__rendered' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .input-text.qty .form-control' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .product-quantity .quantity input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} table.shop_table.cart .actions .coupon input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'forms_fields_normal_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} table.shop_table.cart .actions .coupon input, {{WRAPPER}} .product-quantity .quantity input, {{WRAPPER}} .coupon .input-text, {{WRAPPER}} .raven-cart-totals .input-text, {{WRAPPER}} select, {{WRAPPER}} .select2-selection--single',
			]
		);

		$this->add_control(
			'forms_fields_placeholder',
			[
				'type' => 'heading',
				'separator' => 'before',
				'label' => esc_html__( 'Placeholder', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'forms_fields_placeholder_typography',
				'selector' => '{{WRAPPER}} .coupon .input-text::placeholder, {{WRAPPER}} .raven-cart-totals .input-text::placeholder, {{WRAPPER}} select::placeholder, {{WRAPPER}} .select2-selection--single::placeholder,{{WRAPPER}} .product-quantity .quantity input::placeholder',
			]
		);

		$this->add_control(
			'forms_fields_placeholder_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#999999',
				'selectors' => [
					'{{WRAPPER}} .coupon .input-text::placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .product-quantity .quantity input::placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} select::placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .select2-selection--single::placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .input-text.qty .form-control::placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .shipping-calculator-form p input::placeholder' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'forms_fields_value',
			[
				'type' => 'heading',
				'separator' => 'before',
				'label' => esc_html__( 'Value', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'forms_fields_value_typography',
				'selector' => '{{WRAPPER}} .product-quantity .quantity input, {{WRAPPER}} .coupon .input-text, {{WRAPPER}} .raven-cart-totals .input-text, {{WRAPPER}} select, {{WRAPPER}} .select2-selection--single',
			]
		);

		$this->add_control(
			'forms_fields_value_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#111111',
				'selectors' => [
					'{{WRAPPER}} .coupon .input-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .cart-collaterals .input-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .select2-selection__rendered' => 'color: {{VALUE}};',
					'{{WRAPPER}} select' => 'color: {{VALUE}};',
					'{{WRAPPER}} .select2-selection--single' => 'color: {{VALUE}};',
					'{{WRAPPER}} .product-quantity .quantity input' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'forms_fields_focus_styles',
			[
				'label' => esc_html__( 'Focus', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'forms_fields_focus_color',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}}' => '--forms-fields-focus-background-color: {{VALUE}};',
					'.raven-woo-select2-wrapper .select2-results__option:focus' => 'background-color: {{VALUE}};',
					// style select2 arrow
					'{{WRAPPER}} .select2-container--default .select2-selection--single:focus .select2-selection__arrow b' => 'border-color: {{VALUE}} transparent transparent transparent;',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'forms_fields_focus_border',
				'selector' => '{{WRAPPER}} .woocommerce .input-text.qty:focus, {{WRAPPER}} .actions .coupon .input-text:focus, {{WRAPPER}} .cart-collaterals .input-text:focus, {{WRAPPER}} select:focus, {{WRAPPER}} .select2-selection--single:focus',
			]
		);

		$this->add_responsive_control(
			'forms_fields_focus_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .actions .coupon .input-text:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
					'{{WRAPPER}} .cart-collaterals .input-text:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} select:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .select2-selection--single:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .woocommerce .input-text.qty:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'forms_fields_focus_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .cart-collaterals .input-text:focus' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} select:focus' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .select2-selection--single:focus' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .select2-selection__rendered:focus' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .input-text.qty .form-control:focus' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .product-quantity .quantity input:focus' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} table.shop_table.cart .actions .coupon input:focus' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'forms_fields_focus_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .product-quantity .quantity .input-text.qty:focus, {{WRAPPER}} .actions .coupon .input-text:focus, {{WRAPPER}} .raven-cart-totals .input-text:focus, {{WRAPPER}} select:focus, {{WRAPPER}} .select2-selection--single:focus',
			]
		);

		$this->add_control(
			'forms_fields_placeholder_focus',
			[
				'type' => 'heading',
				'separator' => 'before',
				'label' => esc_html__( 'Placeholder', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'forms_fields_placeholder_typography_focus',
				'selector' => '.product-quantity .quantity .input-text.qty:focus, {{WRAPPER}} .coupon .input-text:focus::placeholder, {{WRAPPER}} .raven-cart-totals .input-text:focus::placeholder, {{WRAPPER}} select:focus:::placeholder, {{WRAPPER}} .select2-selection--single:focus:::placeholder',
			]
		);

		$this->add_control(
			'forms_fields_placeholder_color_focus',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#999999',
				'selectors' => [
					'{{WRAPPER}} .coupon .input-text:focus:::placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .product-quantity .quantity input:focus:::placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} select:focus:::placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .select2-selection--single:focus:::placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .input-text.qty .form-control:focus::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'forms_fields_value_focus',
			[
				'type' => 'heading',
				'separator' => 'before',
				'label' => esc_html__( 'Value', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'forms_fields_value_typography_focus',
				'selector' => '{{WRAPPER}} .coupon .input-text:focus:, {{WRAPPER}} .raven-cart-totals .input-text:focus:, {{WRAPPER}} select:focus:, {{WRAPPER}} .select2-selection--single:focus:',
			]
		);

		$this->add_control(
			'forms_fields_value_color_focus',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#111111',
				'selectors' => [
					'{{WRAPPER}} .coupon .input-text:focus:' => 'color: {{VALUE}};',
					'{{WRAPPER}} .cart-collaterals .input-text:focus:' => 'color: {{VALUE}};',
					'{{WRAPPER}} .select2-selection__rendered:focus:' => 'color: {{VALUE}};',
					'{{WRAPPER}} select:focus:' => 'color: {{VALUE}};',
					'{{WRAPPER}} .select2-selection--single:focus:' => 'color: {{VALUE}};',
					'{{WRAPPER}} .product-quantity .quantity input:focus:' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'forms_fields_radio',
			[
				'type' => 'heading',
				'separator' => 'before',
				'label' => esc_html__( 'Radio', 'jupiterx-core' ),
			]
		);

		$this->add_responsive_control(
			'forms_fields_radio_size',
			[
				'label' => esc_html__( 'Size', 'jupiterx-core' ),
				'type' => 'slider',
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 18,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--raven-cart-input-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .cart_totals .woocommerce-shipping-methods input + .raven-cart-shipping-method-radio:before' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'forms_fields_radio_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'slider',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--raven-cart-input-spacing: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'forms_fields_radio_space_between',
			[
				'label' => esc_html__( 'Spacing Between', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '0',
					'right' => '15',
					'bottom' => '0',
					'left' => '0',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .cart_totals .woocommerce-shipping-methods .raven-cart-shipping-method-radio + label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->start_controls_tabs( 'forms_fields_radio_tabs' );

		$this->start_controls_tab(
			'forms_fields_radio_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'forms_fields_radio_background_color_normal',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .cart_totals .woocommerce-shipping-methods input + .raven-cart-shipping-method-radio:before' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'forms_fields_radio_border_normal',
				'selector' => '{{WRAPPER}} .woocommerce .cart_totals .woocommerce-shipping-methods input + .raven-cart-shipping-method-radio:before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'forms_fields_radio_tab_checked',
			[
				'label' => esc_html__( 'Checked', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'forms_fields_radio_background_color_checked',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#2B2B2B',
				'selectors' => [
					'{{WRAPPER}} .cart_totals .woocommerce-shipping-methods input:checked + .raven-cart-shipping-method-radio:after' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'forms_fields_radio_border_checked',
				'selector' => '{{WRAPPER}} .woocommerce .cart_totals .woocommerce-shipping-methods input:checked + .raven-cart-shipping-method-radio:before',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_cart_divider_style() {
		$this->start_controls_section(
			'cart_divider',
			[
				'label' => esc_html__( 'Divider', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'cart_divider_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#E3E3E3',
				'selectors' => [
					'{{WRAPPER}} .shop_table thead th' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .shop_table .cart-subtotal' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} .shop_table .order-total' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} .woocommerce-cart-form__contents tbody tr:after' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'cart_divider_weight',
			[
				'label' => esc_html__( 'Weight', 'jupiterx-core' ),
				'type' => 'slider',
				'default' => [
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .shop_table thead th' => 'border-top-width: {{SIZE}}px; border-bottom-width: {{SIZE}}px;',
					'{{WRAPPER}} .shop_table .cart-subtotal' => 'border-top-width: {{SIZE}}px !important;border-bottom-width: {{SIZE}}px !important;',
					'{{WRAPPER}} .shop_table .order-total' => 'border-top-width: {{SIZE}}px !important;',
					'{{WRAPPER}} .woocommerce-cart-form__contents tbody tr:after' => 'border-bottom-width: {{SIZE}}px !important;',
				],
				'render_type' => 'template',
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'cart_divider_gap',
			[
				'label' => esc_html__( 'Gap', 'jupiterx-core' ),
				'type' => 'slider',
				'default' => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .shop_table thead th' => 'padding-top: {{SIZE}}px !important;padding-bottom: {{SIZE}}px !important;',
					'{{WRAPPER}} .shop_table .cart-subtotal > td' => 'padding-top: {{SIZE}}px !important;padding-bottom: {{SIZE}}px !important;',
					'{{WRAPPER}} .shop_table .order-total > td' => 'padding-top: {{SIZE}}px !important;',
					'{{WRAPPER}} .woocommerce-cart-form__contents .woocommerce-cart-form__cart-item td' => 'padding-top: {{SIZE}}px;padding-bottom: {{SIZE}}px;',
					'{{WRAPPER}} .cart_totals tr.woocommerce-shipping-totals td' => 'padding-top: {{SIZE}}px !important;padding-bottom: {{SIZE}}px !important;',
				],
				'render_type' => 'template',
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();
	}

	protected function register_cart_remove_link_style() {
		$this->start_controls_section(
			'cart_remove_link',
			[
				'label' => esc_html__( 'Remove Link', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'cart_remove_link_typography',
				'selector' => '{{WRAPPER}} .product-remove a.jupiterx-icon-times,{{WRAPPER}} .raven-cart-compact-name a.remove',
			]
		);

		$this->start_controls_tabs( 'cart_remove_link_tabs' );

		$this->start_controls_tab(
			'cart_remove_link_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'cart_remove_link_normal_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#111111',
				'selectors' => [
					'{{WRAPPER}} .product-remove a.jupiterx-icon-times' => 'color: {{VALUE}} !important',
					'{{WRAPPER}} .raven-cart-compact-name a.remove' => 'color: {{VALUE}} !important',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cart_remove_link_tab_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'cart_remove_link_hover_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .product-remove a.jupiterx-icon-times:hover' => 'color: {{VALUE}} !important',
					'{{WRAPPER}} .raven-cart-compact-name a.remove:hover' => 'color: {{VALUE}} !important',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_cart_customize_style() {
		$this->start_controls_section(
			'cart_customize_style',
			[
				'label' => esc_html__( 'Customize', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'customize_description',
			[
				'raw' => esc_html__( 'Add more level of customization for different sections of your Cart widget', 'jupiterx-core' ),
				'type' => 'raw_html',
				'content_classes' => 'elementor-control-field-description',
			]
		);

		$this->add_control(
			'customize_sections',
			[
				'label' => esc_html__( 'Section', 'jupiterx-core' ),
				'type' => 'select2',
				'multiple' => true,
				'label_block' => true,
				'options' => [
					'table_heading' => esc_html__( 'Table Heading', 'jupiterx-core' ),
					'quantity_inputs' => esc_html__( 'Quantity Inputs', 'jupiterx-core' ),
					'coupon_form' => esc_html__( 'Coupon Form', 'jupiterx-core' ),
					'update_cart_button' => esc_html__( 'Update Cart Button', 'jupiterx-core' ),
					'thumbnails' => esc_html__( 'Thumbnails', 'jupiterx-core' ),
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_cart_table_heading_style() {
		$this->start_controls_section(
			'cart_table_heading_style',
			[
				'label' => esc_html__( 'Table Heading', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'customize_sections' => 'table_heading',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cart_table_heading_typography',
				'selector' => '{{WRAPPER}} .woocommerce-cart-form__contents thead th',
			]
		);

		$this->add_control(
			'cart_table_heading_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-cart-form__contents thead th' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'cart_table_heading_background',
				'label'     => esc_html__( 'Background Type', 'jupiterx-core' ),
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .woocommerce-cart-form__contents thead',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'cart_table_heading_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .woocommerce-cart-form__contents thead tr',
			]
		);

		$this->add_control(
			'cart_table_heading_border_type',
			[
				'label'     => esc_html__( 'Border Type', 'jupiterx-core' ),
				'type'      => 'select',
				'options'   => [
					'none'   => esc_html__( 'None', 'jupiterx-core' ),
					'solid'  => esc_html__( 'Solid', 'jupiterx-core' ),
					'double' => esc_html__( 'Double', 'jupiterx-core' ),
					'dotted' => esc_html__( 'Dotted', 'jupiterx-core' ),
					'dashed' => esc_html__( 'Dashed', 'jupiterx-core' ),
					'groove' => esc_html__( 'Groove', 'jupiterx-core' ),
				],
				'separator' => 'before',
				'default'   => 'solid',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-cart-form__contents thead th' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'cart_table_heading_border_width',
			[
				'label'      => esc_html__( 'Width', 'jupiterx-core' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-cart-form__contents thead th' => 'border-top-width: {{TOP}}{{UNIT}}; border-bottom-width: {{BOTTOM}}{{UNIT}};',
					'{{WRAPPER}} .woocommerce-cart-form__contents thead th:last-child' => 'border-right-width: {{RIGHT}}{{UNIT}};',
					'{{WRAPPER}}.raven-cart-items-layout-standard .woocommerce-cart-form__contents thead th:first-child' => 'border-left-width: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}}.raven-cart-items-layout-compact .woocommerce-cart-form__contents thead th:nth-child(2)' => 'border-left-width: {{LEFT}}{{UNIT}};',

				],
				'condition' => [
					'cart_table_heading_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_table_heading_border_color',
			[
				'label'     => esc_html__( 'Color', 'jupiterx-core' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-cart-form__contents thead th' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'cart_table_heading_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'cart_table_heading_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}}.raven-cart-items-layout-standard .woocommerce-cart-form__contents thead th:first-child' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}}.raven-cart-items-layout-compact .woocommerce-cart-form__contents thead th:nth-child(2)' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .woocommerce-cart-form__contents thead th:last-child' => 'border-top-right-radius: {{RIGHT}}{{UNIT}}; border-bottom-right-radius: {{BOTTOM}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cart_table_heading_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-cart-form__contents thead th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'cart_table_heading_gap',
			[
				'label'      => esc_html__( 'Gap', 'jupiterx-core' ),
				'type'       => 'slider',
				'selectors'  => [
					'{{WRAPPER}} .woocommerce-cart-form__contents tbody:before' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_cart_quantity_style() {
		$this->start_controls_section(
			'cart_quantity_style',
			[
				'label' => esc_html__( 'Quantity', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'customize_sections' => 'quantity_inputs',
				],
			]
		);

		$this->add_control(
			'cart_quantity_view',
			[
				'label' => esc_html__( 'View', 'jupiterx-core' ),
				'type' => 'select',
				'options' => [
					'plus_minus' => esc_html__( 'Plus / Minus', 'jupiterx-core' ),
					'up_down' => esc_html__( 'Up / Down', 'jupiterx-core' ),
				],
				'default' => 'up_down',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'cart_quantity_width',
			[
				'label'      => esc_html__( 'Width', 'jupiterx-core' ),
				'type'       => 'slider',
				'size_units' => [ 'px', 'em', '%' ],
				'range' => [
					'em' => [
						'min' => 0,
						'max' => 1,
					],
				],
				'frontend_available' => true,
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'cart_quantity_typography',
				'exclude' => [ 'text_transform', 'word_spacing' ],
				'selector' => '{{WRAPPER}} .woocommerce table.shop_table.cart .product-quantity .quantity input',
			]
		);

		$this->add_control(
			'cart_quantity_border_type',
			[
				'label'     => esc_html__( 'Border Type', 'jupiterx-core' ),
				'type'      => 'select',
				'options'   => [
					'none'   => esc_html__( 'None', 'jupiterx-core' ),
					'solid'  => esc_html__( 'Solid', 'jupiterx-core' ),
					'double' => esc_html__( 'Double', 'jupiterx-core' ),
					'dotted' => esc_html__( 'Dotted', 'jupiterx-core' ),
					'dashed' => esc_html__( 'Dashed', 'jupiterx-core' ),
					'groove' => esc_html__( 'Groove', 'jupiterx-core' ),
				],
				'default'   => 'solid',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .product-quantity .quantity input' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'cart_quantity_border_width',
			[
				'label'      => esc_html__( 'Width', 'jupiterx-core' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .product-quantity .quantity input' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'cart_quantity_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'cart_quantity_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .product-quantity .quantity input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cart_quantity_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .product-quantity .quantity input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->start_controls_tabs( 'cart_quantity_tabs' );

		$this->start_controls_tab(
			'cart_quantity_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'cart_quantity_normal_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .product-quantity .quantity input' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_quantity_normal_background_color',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .product-quantity .quantity input' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'cart_quantity_normal_border_color',
			[
				'label' => esc_html__( 'Border Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .product-quantity .quantity input' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cart_quantity_tab_focus',
			[
				'label' => esc_html__( 'Focus', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'cart_quantity_focus_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .product-quantity .quantity input:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_quantity_focus_background_color',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .product-quantity .quantity input:focus' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'cart_quantity_focus_border_color',
			[
				'label' => esc_html__( 'Border Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .product-quantity .quantity input:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'cart_quantity_plus_minus_buttons_heading',
			[
				'type' => 'heading',
				'label' => esc_html__( 'Plus / Minus buttons', 'jupiterx-core' ),
				'separator' => 'before',
				'condition' => [
					'cart_quantity_view' => 'plus_minus',
				],
			]
		);

		$this->add_control(
			'cart_quantity_plus_minus_buttons_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .product-quantity .custom-qty-input span' => 'color: {{VALUE}};',
				],
				'condition' => [
					'cart_quantity_view' => 'plus_minus',
				],
			]
		);

		$this->add_control(
			'cart_quantity_plus_minus_buttons_size',
			[
				'label'      => esc_html__( 'Size', 'jupiterx-core' ),
				'type'       => 'slider',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .product-quantity .custom-qty-input button span' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'cart_quantity_view' => 'plus_minus',
				],
			]
		);

		$this->add_responsive_control(
			'cart_quantity_plus_minus_buttons_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .product-quantity .custom-qty-input button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition' => [
					'cart_quantity_view' => 'plus_minus',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function register_cart_coupon_style() {
		$this->start_controls_section(
			'cart_coupon_style',
			[
				'label' => esc_html__( 'Coupon Form', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'customize_sections' => 'coupon_form',
					'cart_coupon' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'cart_coupon_space_between',
			[
				'label' => esc_html__( 'Space Between', 'jupiterx-core' ),
				'type' => 'slider',
				'separator' => 'after',
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon input' => 'margin-right: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_control(
			'cart_coupon_text_field_heading',
			[
				'type' => 'heading',
				'label' => esc_html__( 'Text Field', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'cart_coupon_typography',
				'selector' => '{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon input',
			]
		);

		$this->start_controls_tabs( 'cart_coupon_tabs' );

		$this->start_controls_tab(
			'cart_coupon_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'cart_coupon_normal_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon input::placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon input' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_coupon_normal_background_color',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon input' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_coupon_normal_border_type',
			[
				'label'     => esc_html__( 'Border Type', 'jupiterx-core' ),
				'type'      => 'select',
				'options'   => [
					'none'   => esc_html__( 'None', 'jupiterx-core' ),
					'solid'  => esc_html__( 'Solid', 'jupiterx-core' ),
					'double' => esc_html__( 'Double', 'jupiterx-core' ),
					'dotted' => esc_html__( 'Dotted', 'jupiterx-core' ),
					'dashed' => esc_html__( 'Dashed', 'jupiterx-core' ),
					'groove' => esc_html__( 'Groove', 'jupiterx-core' ),
				],
				'default'   => 'solid',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon input' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'cart_coupon_normal_border_width',
			[
				'label'      => esc_html__( 'Width', 'jupiterx-core' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon input' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'cart_coupon_normal_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_coupon_normal_border_color',
			[
				'label'     => esc_html__( 'Color', 'jupiterx-core' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon input' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'cart_coupon_normal_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'cart_coupon_normal_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'cart_coupon_normal_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon input',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cart_coupon_tab_focus',
			[
				'label' => esc_html__( 'Focus', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'cart_coupon_focus_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon input:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon input:focus::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_coupon_focus_background_color',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon input:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cart_coupon_focus_border_type',
			[
				'label'     => esc_html__( 'Border Type', 'jupiterx-core' ),
				'type'      => 'select',
				'options'   => [
					'none'   => esc_html__( 'None', 'jupiterx-core' ),
					'solid'  => esc_html__( 'Solid', 'jupiterx-core' ),
					'double' => esc_html__( 'Double', 'jupiterx-core' ),
					'dotted' => esc_html__( 'Dotted', 'jupiterx-core' ),
					'dashed' => esc_html__( 'Dashed', 'jupiterx-core' ),
					'groove' => esc_html__( 'Groove', 'jupiterx-core' ),
				],
				'default'   => 'solid',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon input:focus' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'cart_coupon_focus_border_width',
			[
				'label'      => esc_html__( 'Width', 'jupiterx-core' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon input:focus' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'cart_coupon_focus_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_coupon_focus_border_color',
			[
				'label'     => esc_html__( 'Color', 'jupiterx-core' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon input:focus' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'cart_coupon_focus_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'cart_coupon_focus_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon input:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'cart_coupon_focus_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon input:focus',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'cart_coupon_button_heading',
			[
				'type' => 'heading',
				'label' => esc_html__( 'Button', 'jupiterx-core' ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'cart_coupon_button_typography',
				'selector' => '{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon .button',
			]
		);

		$this->start_controls_tabs( 'cart_coupon_button_tabs' );

		$this->start_controls_tab(
			'cart_coupon_button_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'cart_coupon_button_normal_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon .button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name'      => 'cart_coupon_button_normal_background',
				'label'     => esc_html__( 'Background Type', 'jupiterx-core' ),
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon .button',
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'cart_coupon_button_normal_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon .button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cart_coupon_button_tab_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'cart_coupon_button_hover_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon .button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name'      => 'cart_coupon_button_hover_background',
				'label'     => esc_html__( 'Background Type', 'jupiterx-core' ),
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon .button:hover',
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'cart_coupon_button_hover_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon .button:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'cart_coupon_button_border_type',
			[
				'label'     => esc_html__( 'Border Type', 'jupiterx-core' ),
				'type'      => 'select',
				'separator' => 'before',
				'options'   => [
					'none'   => esc_html__( 'None', 'jupiterx-core' ),
					'solid'  => esc_html__( 'Solid', 'jupiterx-core' ),
					'double' => esc_html__( 'Double', 'jupiterx-core' ),
					'dotted' => esc_html__( 'Dotted', 'jupiterx-core' ),
					'dashed' => esc_html__( 'Dashed', 'jupiterx-core' ),
					'groove' => esc_html__( 'Groove', 'jupiterx-core' ),
				],
				'default'   => 'none',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon .button' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'cart_coupon_button_border_width',
			[
				'label'      => esc_html__( 'Width', 'jupiterx-core' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon .button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'cart_coupon_button_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_coupon_button_border_color',
			[
				'label'     => esc_html__( 'Color', 'jupiterx-core' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon .button' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'cart_coupon_button_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'cart_coupon_button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cart_coupon_button_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .coupon .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_cart_thumbnails_style() {
		$this->start_controls_section(
			'cart_thumbnails_style',
			[
				'label' => esc_html__( 'Thumbnails', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'customize_sections' => 'thumbnails',
					'cart_items_thumbnail' => 'yes',
				],
			]
		);

		$this->add_group_control(
			'image-size',
			[
				'name' => 'cart_thumbnails_image',
				'default' => 'thumbnail',
			]
		);

		$this->add_responsive_control(
			'cart_thumbnails_max_width',
			[
				'label' => esc_html__( 'Max Width', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ '%', 'px', 'vw' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce .product-name img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'cart_thumbnails_border_type',
			[
				'label'     => esc_html__( 'Border Type', 'jupiterx-core' ),
				'type'      => 'select',
				'options'   => [
					'none'   => esc_html__( 'None', 'jupiterx-core' ),
					'solid'  => esc_html__( 'Solid', 'jupiterx-core' ),
					'double' => esc_html__( 'Double', 'jupiterx-core' ),
					'dotted' => esc_html__( 'Dotted', 'jupiterx-core' ),
					'dashed' => esc_html__( 'Dashed', 'jupiterx-core' ),
					'groove' => esc_html__( 'Groove', 'jupiterx-core' ),
				],
				'default'   => 'none',
				'selectors' => [
					'{{WRAPPER}} .woocommerce .product-name img' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'cart_thumbnails_border_width',
			[
				'label'      => esc_html__( 'Width', 'jupiterx-core' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce .product-name img' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'cart_thumbnails_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_thumbnails_border_color',
			[
				'label'     => esc_html__( 'Color', 'jupiterx-core' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce .product-name img' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'cart_thumbnails_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'cart_thumbnails_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce .product-name img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'cart_thumbnails_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .woocommerce .product-name img',
			]
		);

		$this->end_controls_section();
	}

	protected function register_cart_update_cart_button_style() {
		$this->start_controls_section(
			'cart_update_cart_button_style',
			[
				'label' => esc_html__( 'Update Cart Button', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'customize_sections' => 'update_cart_button',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'cart_update_cart_button_typography',
				'selector' => '{{WRAPPER}} .woocommerce table.shop_table.cart .actions .button[name=update_cart]',
			]
		);

		$this->start_controls_tabs( 'cart_update_cart_button_tabs' );

		$this->start_controls_tab(
			'cart_update_cart_button_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'cart_update_cart_button_normal_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .button[name=update_cart]' => 'color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .button[name=update_cart]:before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name'      => 'cart_update_cart_button_normal_background',
				'label'     => esc_html__( 'Background Type', 'jupiterx-core' ),
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .woocommerce table.shop_table.cart .actions .button[name=update_cart]',
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'cart_update_cart_button_normal_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .woocommerce table.shop_table.cart .actions .button[name=update_cart]',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cart_update_cart_button_tab_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'cart_update_cart_button_hover_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .button[name=update_cart]:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .button[name=update_cart]:hover:before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name'      => 'cart_update_cart_button_hover_background',
				'label'     => esc_html__( 'Background Type', 'jupiterx-core' ),
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .woocommerce table.shop_table.cart .actions .button[name=update_cart]:hover',
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'cart_update_cart_button_hover_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .woocommerce table.shop_table.cart .actions .button[name=update_cart]:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'cart_update_cart_button_border_type',
			[
				'label'     => esc_html__( 'Border Type', 'jupiterx-core' ),
				'type'      => 'select',
				'options'   => [
					'none'   => esc_html__( 'None', 'jupiterx-core' ),
					'solid'  => esc_html__( 'Solid', 'jupiterx-core' ),
					'double' => esc_html__( 'Double', 'jupiterx-core' ),
					'dotted' => esc_html__( 'Dotted', 'jupiterx-core' ),
					'dashed' => esc_html__( 'Dashed', 'jupiterx-core' ),
					'groove' => esc_html__( 'Groove', 'jupiterx-core' ),
				],
				'separator' => 'before',
				'default'   => 'none',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .button[name=update_cart]' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'cart_update_cart_button_border_width',
			[
				'label'      => esc_html__( 'Width', 'jupiterx-core' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .button[name=update_cart]' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'cart_update_cart_button_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_update_cart_button_border_color',
			[
				'label'     => esc_html__( 'Color', 'jupiterx-core' ),
				'type'      => 'color',
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .button[name=update_cart]' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'cart_update_cart_button_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'cart_update_cart_button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type'       => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .button[name=update_cart]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cart_update_cart_button_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce table.shop_table.cart .actions .button[name=update_cart]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function is_wc_feature_active( $feature ) {
		if ( 'coupons' === $feature ) {
			return function_exists( 'wc_coupons_enabled' ) && wc_coupons_enabled();
		}

		return false;
	}

	protected $gettext_modifications;

	public function filter_gettext( $translation, $text ) {
		if ( ! isset( $this->gettext_modifications ) ) {
			$this->init_gettext_modifications();
		}

		return array_key_exists( $text, $this->gettext_modifications ) ? $this->gettext_modifications[ $text ] : $translation;
	}

	/**
	 * Init Gettext Modifications
	 *
	 * Sets the `$gettext_modifications` property used with the `filter_gettext()` in the extended Base_Widget.
	 */
	protected function init_gettext_modifications() {
		$instance = $this->get_settings_for_display();

		$this->gettext_modifications = [
			'Update cart' => isset( $instance['update_cart_button_text'] ) ? $instance['update_cart_button_text'] : '',
			'Cart totals' => isset( $instance['totals_section_title'] ) && 'yes' === $instance['cart_total_heading'] ? $instance['totals_section_title'] : '',
			'Proceed to checkout' => isset( $instance['checkout_button_text'] ) ? $instance['checkout_button_text'] : '',
			'Update' => isset( $instance['update_shipping_button_text'] ) ? $instance['update_shipping_button_text'] : '',
			'Apply coupon' => isset( $instance['apply_coupon_button_text'] ) ? $instance['apply_coupon_button_text'] : '',
		];
	}

	public function hide_coupon_field_on_cart( $enabled ) {
		$settings = $this->get_settings_for_display();
		return is_cart() && 'yes' !== $settings['cart_coupon'] ? false : $enabled;
	}

	/**
	 * Woocommerce Before Cart
	 * Output containing elements. Callback function for the woocommerce_before_cart hook
	 *
	 * This eliminates the need for template overrides.
	 */
	public function woocommerce_before_cart() {
		$settings = $this->get_settings_for_display();

		$disable_haeding = [
			'heading' => 'yes' !== $settings['cart_heading'] || empty( $settings['totals_section_title'] ) ? 'raven-cart-heading-disabled' : '',
		];

		$custom_texts = [
			'cart_table_heading' => 'raven-cart-table-header-disabled',
			'update_cart_button_text' => 'raven-cart-update-cart-disabled',
			'apply_coupon_button_text' => 'raven-cart-apply-coupon-disabled',
			'update_shipping_button_text' => 'raven-cart-shipping-button-disabled',
			'checkout_button_text' => 'raven-cart-procced-to-checkout-disabled',
		];

		foreach ( $custom_texts as $key => $value ) {
			if ( ! empty( $settings[ $key ] ) ) {
				continue;
			}

			$disable_haeding[ $key ] = $value;
		}

		?>
		<div class="raven-cart__container <?php echo esc_attr( implode( ' ', $disable_haeding ) ); ?>">
			<!--open container-->
			<div class="raven-cart__column raven-cart__column-start">
				<!--open column-1-->
		<?php
	}

	/**
	 * Woocommerce Before Cart Table
	 *
	 * Output containing elements. Callback function for the woocommerce_before_cart_table hook
	 *
	 * This eliminates the need for template overrides.
	 */
	public function woocommerce_before_cart_table() {
		$settings = $this->get_settings_for_display();

		$haeding = 'yes' === $settings['cart_heading'] ? esc_html__( 'Cart', 'jupiterx-core' ) : '';
		?>
			<div class="e-shop-table raven-cart-section">
				<h2><?php echo wp_kses_post( $haeding ); ?></h2>
				<div class="raven-cart-table-wrapper">
				<!--open shop table div -->
		<?php
	}

	/**
	 * Woocommerce After Cart Table
	 *
	 * Output containing elements. Callback function for the woocommerce_after_cart_table hook
	 *
	 * This eliminates the need for template overrides.
	 */
	public function woocommerce_after_cart_table() {
		?>
			</div>
		</div>
			<!--close shop table div -->
		<div class="e-clear"></div>
		<?php
	}

	/**
	* Woocommerce Before Cart Collaterals
	*
	* Output containing elements. * Callback function for the woocommerce_before_cart_collaterals hook
	*
	* This eliminates the need for template overrides.
	*/
	public function woocommerce_before_cart_collaterals() {
		?>
			<!--close column-1-->
		</div>
		<div class="raven-cart__column raven-cart__column-end">
			<!--open column-2-->
			<div class="raven-cart__column-inner e-sticky-right-column">
				<!--open column-inner-->
				<div class="raven-cart-totals raven-cart-section">
					<!--open cart-totals-->
		<?php
	}

	/**
	* Woocommerce After Cart
	*
	* Output containing elements. Callback function for the woocommerce_after_cart hook.
	*
	* This eliminates the need for template overrides.
	*/
	public function woocommerce_after_cart() {
		?>
						<!--close cart-totals-->
					</div>
					<!--close column-inner-->
				</div>
				<!--close column-2-->
			</div>
			<!--close container-->
		</div>
		<?php
	}

	/**
	* WooCommerce Get Remove URL.
	*
	* When in the Editor or (wp preview) and the uer clicks to remove an item from the cart, WooCommerce uses
	* the`_wp_http_referer` url during the ajax call to genrate the new cart html. So when we're in the Editor
	* or (wp preview) we modify the `_wp_http_referer` to use the `get_wp_preview_url()` which will have
	* the new cart content.
	*/
	public function woocommerce_get_remove_url( $url ) {
		$url_components = wp_parse_url( $url );

		if ( ! isset( $url_components['query'] ) ) {
			return $url;
		}

		$params = [];

		parse_str( html_entity_decode( $url_components['query'] ), $params );

		$params['_wp_http_referer'] = rawurlencode( \Elementor\Plugin::$instance->documents->get_current()->get_wp_preview_url() );

		return add_query_arg( $params, get_site_url() );
	}

	public function cart_coupon_return_false() {
		return false;
	}

	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function product_thumbnail( $image, $cart_item, $cart_item_key ) {
		$settings = $this->get_settings_for_display();

		if ( 'yes' === $settings['cart_items_thumbnail'] && 'standard' === $settings['cart_items_layout'] ) {
			$image_settings = [
				'image_size' => $settings['cart_thumbnails_image_size'],
				'image' => [
					'id' => get_post_thumbnail_id( $cart_item['product_id'] ),
				],
				'image_custom_dimension' => $settings['cart_thumbnails_image_custom_dimension'],
			];

			$image = \Elementor\Group_Control_Image_Size::get_attachment_image_html( $image_settings );

			return $image;
		}

		return '';
	}

	private function disbale_input_spinner() {
		ob_start();
		?>
		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				if ( typeof $.fn.InputSpinner === 'undefined' ) {
					return;
				}

				$.fn.InputSpinner = () => {
					return;
				}
			} );
		</script>
		<?php
		echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function product_item( $item_name, $cart_item, $cart_item_key ) {
		$product = wc_get_product( $cart_item['product_id'] );

		// Check if product has variations.
		if ( $product->is_type( 'variable' ) ) {
			// Get variation data.
			$variation_data = $cart_item['variation'];

			// Initialize empty string for variation display.
			$variation_display = '';

			// Loop through variation data.
			foreach ( $variation_data as $attribute => $value ) {

				// Get attribute name.
				$attribute_name = wc_attribute_label( str_replace( 'attribute_', '', $attribute ), $product );

				// Add variation data to display string.
				$variation_display .= '<div class="variation-' . esc_attr( $attribute ) . '"><span>' . esc_html( $attribute_name ) . ':</span> ' . wp_kses_post( $value ) . '</div>';
			}

			// Add variations display after item name.
			$item_name .= '<div class="product-variations">' . $variation_display . '</div>';
		}

		$settings = $this->get_settings_for_display();

		$remove_icon = sprintf(
			'<a href="%1$s" class="remove" aria-label="%2$s" data-product_id="%3$s" data-product_sku="%4$s">%5$s</a>',
			esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
			esc_html__( 'Remove this item', 'jupiterx-core' ),
			esc_attr( $cart_item['product_id'] ),
			esc_attr( $cart_item['data']->get_sku() ),
			esc_html__( 'Remove', 'jupiterx-core' )
		);

		if ( 'standard' === $settings['cart_items_layout'] ) {
			$remove_icon = '';
		}

		$image = '';

		if ( 'yes' === $settings['cart_items_thumbnail'] && 'compact' === $settings['cart_items_layout'] ) {
			$image_settings = [
				'image_size' => $settings['cart_thumbnails_image_size'],
				'image' => [
					'id' => get_post_thumbnail_id( $cart_item['product_id'] ),
				],
				'image_custom_dimension' => $settings['cart_thumbnails_image_custom_dimension'],
			];

			$image = \Elementor\Group_Control_Image_Size::get_attachment_image_html( $image_settings );
		}

		$html = sprintf(
			'<div class="raven-cart-compact-product">%1$s<div class="raven-cart-compact-name">%2$s %3$s</div></div>',
			$image,
			$item_name,
			$remove_icon
		);

		return wp_kses_post( $html );
	}

	public function compact_product_remove_icon( $remove_html ) {
		$settings = $this->get_settings_for_display();

		if ( 'standard' === $settings['cart_items_layout'] ) {
			echo wp_kses_post( $remove_html );
			return;
		}

		echo '';
	}

	private function handle_continue_shopping_remove_actions() {
		$settings = $this->get_settings_for_display();

		if ( 'yes' === $settings['cart_items_continue_shopping'] || ! function_exists( 'jupiterx_wc_continue_shopping_button' ) ) {
			return;
		}

		remove_action( 'woocommerce_proceed_to_checkout', 'jupiterx_wc_continue_shopping_button', 5 );
		remove_action( 'woocommerce_review_order_after_submit', 'jupiterx_wc_continue_shopping_button' );
	}

	private function handle_continue_shopping_add_actions() {
		$settings = $this->get_settings_for_display();

		if ( 'yes' === $settings['cart_items_continue_shopping'] || ! function_exists( 'jupiterx_wc_continue_shopping_button' ) ) {
			return;
		}

		add_action( 'woocommerce_proceed_to_checkout', 'jupiterx_wc_continue_shopping_button', 5 );
		add_action( 'woocommerce_review_order_after_submit', 'jupiterx_wc_continue_shopping_button' );
	}

	protected function render() {
		wc_maybe_define_constant( 'WOOCOMMERCE_CART', true );
		do_action( 'jupiterx_cart_widget_before_render' );

		$is_editor  = Plugin::$instance->editor->is_edit_mode();
		$is_preview = Plugin::$instance->preview->is_preview_mode();

		$this->disbale_input_spinner();
		$this->handle_continue_shopping_remove_actions();

		/**
		 * Add actions & filters before displaying our Widget.
		 */
		add_filter( 'gettext', [ $this, 'filter_gettext' ], 999, 2 );
		add_filter( 'woocommerce_cart_item_name', [ $this, 'product_item' ], 10, 3 );
		add_filter( 'woocommerce_cart_item_remove_link', [ $this, 'compact_product_remove_icon' ], 10, 1 );
		add_filter( 'woocommerce_cart_item_thumbnail', [ $this, 'product_thumbnail' ], 10, 3 );
		add_filter( 'woocommerce_coupons_enabled', [ $this, 'hide_coupon_field_on_cart' ], 10, 1 );
		add_action( 'woocommerce_before_cart', [ $this, 'woocommerce_before_cart' ] );
		add_action( 'woocommerce_after_cart_table', [ $this, 'woocommerce_after_cart_table' ] );
		add_action( 'woocommerce_before_cart_table', [ $this, 'woocommerce_before_cart_table' ] );
		add_action( 'woocommerce_before_cart_collaterals', [ $this, 'woocommerce_before_cart_collaterals' ] );
		add_action( 'woocommerce_after_cart', [ $this, 'woocommerce_after_cart' ] );

		if ( $is_editor || $is_preview ) {
			add_filter( 'woocommerce_get_remove_url', [ $this, 'woocommerce_get_remove_url' ] );
		}

		// Remove cross-sells in cart.
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

		/**
		 * Display our Widget.
		 */
		echo do_shortcode( '[woocommerce_cart]' );

		/**
		 * Remove actions & filters after displaying our Widget.
		 */
		remove_filter( 'gettext', [ $this, 'filter_gettext' ], 20 );
		remove_filter( 'woocommerce_cart_item_name', [ $this, 'product_item' ] );
		remove_filter( 'woocommerce_cart_item_name', [ $this, 'remove_variation_display_from_cart' ] );
		remove_filter( 'woocommerce_cart_item_thumbnail', [ $this, 'product_thumbnail' ] );
		remove_action( 'woocommerce_before_cart', [ $this, 'woocommerce_before_cart' ] );
		remove_action( 'woocommerce_after_cart_table', [ $this, 'woocommerce_after_cart_table' ] );
		remove_action( 'woocommerce_before_cart_table', [ $this, 'woocommerce_before_cart_table' ] );
		remove_action( 'woocommerce_before_cart_collaterals', [ $this, 'woocommerce_before_cart_collaterals' ] );
		remove_action( 'woocommerce_after_cart', [ $this, 'woocommerce_after_cart' ] );
		remove_filter( 'woocommerce_coupons_enabled', [ $this, 'hide_coupon_field_on_cart' ] );
		remove_filter( 'woocommerce_get_remove_url', [ $this, 'woocommerce_get_remove_url' ] );
		add_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

		$this->handle_continue_shopping_add_actions();
		?>
		<?php
	}
}
