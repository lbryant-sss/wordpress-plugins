<?php

namespace JupiterX_Core\Raven\Modules\Add_To_Cart\Widgets;

use JupiterX_Core\Raven\Base\Base_Widget;
use JupiterX_Core\Raven\Utils;
use Elementor\Plugin;
use Elementor\Icons_Manager;

defined( 'ABSPATH' ) || die();

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class Add_To_Cart extends Base_Widget {

	public function get_title() {
		return esc_html__( 'Add To Cart', 'jupiterx-core' );
	}

	public function get_name() {
		return 'raven-product-add-to-cart';
	}

	public function get_icon() {
		return 'raven-element-icon raven-element-icon-add-to-cart';
	}

	public function get_script_depends() {
		return [ 'wc-add-to-cart-variation' ];
	}

	public function get_categories() {
		return [ 'jupiterx-core-raven-woo-elements' ];
	}

	public function register_controls() {
		$this->add_style_general_controls();
		$this->add_style_button_controls();
		$this->add_style_quantity_controls();
		$this->add_style_variation_controls();
		$this->add_style_color_swatch_controls();
		$this->add_style_image_swatch_controls();
		$this->add_style_text_swatch_controls();
		$this->add_style_select_swatch_controls();
	}

	/**
	 * @return void
	 */
	public function add_style_general_controls() {
		$this->start_controls_section(
			'section_style_general',
			[
				'label' => esc_html__( 'Layout', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'cart_view',
			[
				'label' => esc_html__( 'View', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'stacked',
				'options' => [
					'stacked' => esc_html__( 'Stacked', 'jupiterx-core' ),
					'inline' => esc_html__( 'Inline', 'jupiterx-core' ),
				],
				'render_type' => 'template',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	public function add_style_button_controls() {
		$this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__( 'Button', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'button_icon',
			[
				'label' => esc_html__( 'Button Icon', 'jupiterx-core' ),
				'type' => 'icons',
				'skin' => 'inline',
				'label_block' => false,
				'default' => [
					'value'   => 'fas fa-shopping-cart',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_responsive_control(
			'button_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'button_icon!' => '',
				],
				'default' => [
					'size' => 17,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder button.single_add_to_cart_button .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-product-add-to-cart .woocommerce-grouped-product-list .add_to_cart_button .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_alignment',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justify', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => 'left',
				'prefix_class' => 'raven-product-add-to-cart%s--align-',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'button_width',
			[
				'label' => esc_html__( 'Width', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', 'em', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'cart_view' => 'stacked',
				],
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder button[type=submit]' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder button.single_add_to_cart_button',
			]
		);

		$this->add_group_control(
			'text-shadow',
			[
				'name' => 'button_text_shadow',
				'selector' => '{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder button.single_add_to_cart_button',
			]
		);

		$this->start_controls_tabs( 'button_style_tabs' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder button.single_add_to_cart_button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder button.single_add_to_cart_button i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-product-add-to-cart .woocommerce-grouped-product-list .add_to_cart_button i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder button.single_add_to_cart_button svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .raven-product-add-to-cart .woocommerce-grouped-product-list .add_to_cart_button svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder button.single_add_to_cart_button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'button_text_color_hover',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder button.single_add_to_cart_button:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder button.single_add_to_cart_button:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-product-add-to-cart .woocommerce-grouped-product-list .add_to_cart_button:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder button.single_add_to_cart_button:hover svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .raven-product-add-to-cart .woocommerce-grouped-product-list .add_to_cart_button:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_color_hover',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder button.single_add_to_cart_button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_transition',
			[
				'label' => esc_html__( 'Transition Duration', 'jupiterx-core' ),
				'type' => 'slider',
				'default' => [
					'size' => 0.2,
				],
				'range' => [
					'px' => [
						'max' => 2,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder button.single_add_to_cart_button' => 'transition: all {{SIZE}}s',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'after_tabs_divider',
			[
				'type' => 'divider',
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'button_border_type',
				'selector' => '{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder button.single_add_to_cart_button',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [
					'px',
					'em',
					'%',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder button.single_add_to_cart_button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'button_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .raven-product-add-to-cart form.cart .raven-qty-button-holder .button[type=submit]',
			]
		);

		$this->add_control(
			'after_button_border_divider',
			[
				'type' => 'divider',
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder button.single_add_to_cart_button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'button_margin',
			[
				'label' => esc_html__( 'Margin', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder button.single_add_to_cart_button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function add_style_quantity_controls() {
		$this->start_controls_section(
			'section_style_quantity',
			[
				'label' => esc_html__( 'Quantity', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'quantity_view',
			[
				'label' => esc_html__( 'View', 'jupiterx-core' ),
				'type' => 'select',
				'options' => [
					'up_down' => esc_html__( 'Up / Down', 'jupiterx-core' ),
					'plus_minus' => esc_html__( 'Plus / Minus', 'jupiterx-core' ),
				],
				'default' => 'up_down',
			]
		);

		$this->add_responsive_control(
			'quantity_width',
			[
				'label' => esc_html__( 'Width', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', 'em', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'cart_view' => 'stacked',
				],
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-qty-button-holder .quantity' => 'width: 100%',
					'{{WRAPPER}} .raven-qty-button-holder .qty' => 'width: {{SIZE}}{{UNIT}} !important',
					'{{WRAPPER}} .raven-qty-button-holder .raven-qty-button-holder-inner' => 'width: {{SIZE}}{{UNIT}} !important',
				],
			]
		);

		$this->add_control(
			'quantity_margin',
			[
				'type' => 'dimensions',
				'label' => esc_html__( 'Margin', 'jupiterx-core' ),
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart.stacked .raven-qty-button-holder .quantity' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important',
					'{{WRAPPER}} .raven-product-add-to-cart.inline .raven-qty-button-holder .quantity' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'quantity_typography',
				'selector' => '{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder .quantity input.qty',
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'quantity_border_type',
				'selector' => '{{WRAPPER}} .raven-product-add-to-cart.plus_minus .raven-qty-button-holder .quantity .raven-qty-button-holder-inner, {{WRAPPER}} .raven-product-add-to-cart.up_down .raven-qty-button-holder .quantity input.qty',
				'exclude' => [ 'color' ],
			]
		);

		$this->add_control(
			'quantity_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [
					'px',
					'em',
					'%',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart.up_down  .raven-qty-button-holder .quantity input.qty' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important',
					'{{WRAPPER}} .raven-product-add-to-cart.plus_minus .raven-qty-button-holder .quantity .raven-qty-button-holder-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important',
				],
			]
		);

		$this->add_responsive_control(
			'quantity_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder .quantity input.qty' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'quantity_input_width',
			[
				'label' => esc_html__( 'Input Width', 'jupiterx-core' ),
				'type' => 'slider',
				'default' => [
					'size' => 72,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'condition' => [
					'quantity_view' => 'up_down',
					'cart_view' => 'inline',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder .quantity input.qty' => 'width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder .quantity .qty' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'quantity_input_width_plus_minus',
			[
				'label' => esc_html__( 'Input Width', 'jupiterx-core' ),
				'type' => 'slider',
				'default' => [
					'size' => 92,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'condition' => [
					'quantity_view' => 'plus_minus',
					'cart_view' => 'inline',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart.plus_minus .raven-qty-button-holder .quantity .raven-qty-button-holder-inner' => 'width: {{SIZE}}{{UNIT}} !important',
				],
			]
		);

		$this->start_controls_tabs( 'quantity_style_tabs' );

		$this->start_controls_tab(
			'tab_quantity_normal',
			[
				'label' => esc_html__( 'NORMAL', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'quantity_text_color_normal',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder .quantity input.qty' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'quantity_background_color_normal',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart.up_down .raven-qty-button-holder .quantity input.qty' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .raven-product-add-to-cart.plus_minus .raven-qty-button-holder .quantity .raven-qty-button-holder-inner' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'quantity_border_color_normal',
			[
				'label' => esc_html__( 'Border Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart.up_down .raven-qty-button-holder .quantity input.qty' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .raven-product-add-to-cart.plus_minus .raven-qty-button-holder .quantity .raven-qty-button-holder-inner' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_quantity_focus',
			[
				'label' => esc_html__( 'FOCUS', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'quantity_text_color_focus',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder .quantity input.qty:focus' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'quantity_background_color_focus',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart.up_down .raven-qty-button-holder .quantity input.qty:focus' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .raven-product-add-to-cart.plus_minus .raven-qty-button-holder .quantity .raven-qty-button-holder-inner.focused' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'quantity_border_color_focus',
			[
				'label' => esc_html__( 'Border Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart.up_down .raven-qty-button-holder .quantity input.qty:focus' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .raven-product-add-to-cart.plus_minus .raven-qty-button-holder .quantity .raven-qty-button-holder-inner.focused' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'after_quantity_tabs_divider',
			[
				'label' => esc_html__( 'Plus / Minus buttons', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
				'condition' => [
					'quantity_view' => 'plus_minus',
				],
			]
		);

		$this->add_control(
			'plus_minus_buttons_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder .quantity button.plus-minus-btn' => 'color: {{VALUE}}',
				],
				'condition' => [
					'quantity_view' => 'plus_minus',
				],
			]
		);

		$this->add_control(
			'plus_minus_buttons_size',
			[
				'label' => esc_html__( 'Size', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [
					'px',
					'em',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder .quantity button.plus-minus-btn' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'quantity_view' => 'plus_minus',
				],
			]
		);

		$this->add_control(
			'plus_minus_buttons_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [
					'px',
					'em',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-qty-button-holder .quantity button.plus-minus-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition' => [
					'quantity_view' => 'plus_minus',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	public function add_style_variation_controls() {
		$this->start_controls_section(
			'section_style_variation',
			[
				'label' => esc_html__( 'Variations', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'variation_view',
			[
				'label' => esc_html__( 'View', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'variations-stacked',
				'prefix_class' => 'raven-add-to-cart-variation-view-',
				'options' => [
					'variations-inline' => esc_html__( 'Inline', 'jupiterx-core' ),
					'variations-stacked' => esc_html__( 'Stacked', 'jupiterx-core' ),
				],
			]
		);

		$this->add_control(
			'variation_margin',
			[
				'label' => esc_html__( 'Margin', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [
					'px',
					'em',
					'%',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-variations-form-holder' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'variation_space_between',
			[
				'label' => esc_html__( 'Space Between', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [
					'px',
					'em',
					'%',
				],
				'range' => [
					'px' => [
						'max' => 500,
						'min' => 0,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart.variations-stacked .raven-variations-form-holder table.variations tr' => 'padding-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-product-add-to-cart.variations-inline .raven-variations-form-holder table.variations tr' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'variation_alignment',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .variations .artbees-was-setting-horizontal' => 'justify-content: {{VALUE}};',
				],
				'prefix_class' => 'raven-product-add-to-cart-variation%s-align-',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'variation_heading_label',
			[
				'label' => esc_html__( 'Label', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'variation_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#111111',
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-variations-form-holder table.variations th.label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'variation_label_typography',
				'fields_options' => [
					'typography' => [
						'default' => 'yes',
					],
					'font_weight' => [
						'default' => '700',
					],
					'line_height' => [
						'default' => [
							'size' => 19,
							'unit' => 'px',
						],
					],
					'font_size' => [
						'default' => [
							'size' => 16,
							'unit' => 'px',
						],
					],
				],
				'selector' => '{{WRAPPER}} .raven-product-add-to-cart .raven-variations-form-holder table.variations th.label label',
			]
		);

		$this->add_control(
			'variation_label_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', 'em', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .variations-inline .raven-variations-form-holder .label label' => 'margin-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .variations-stacked .raven-variations-form-holder .label label' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->variation_clear_button_containers();

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	public function variation_clear_button_containers() {
		$this->add_control(
			'variation_heading_clear_button',
			[
				'label' => esc_html__( 'Clear Button', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'variation_clear_button_typography',
				'fields_options' => [
					'typography' => [
						'default' => 'yes',
					],
					'font_weight' => [
						'default' => '400',
					],
					'line_height' => [
						'default' => [
							'size' => 19,
							'unit' => 'px',
						],
					],
					'font_size' => [
						'default' => [
							'size' => 16,
							'unit' => 'px',
						],
						'selectors' => [
							'{{WRAPPER}} .raven-product-add-to-cart .raven-variations-form-holder table.variations .reset_variations' => 'font-size: {{SIZE}}{{UNIT}} !important',
						],
					],
				],
				'selector' => '{{WRAPPER}} .raven-product-add-to-cart .raven-variations-form-holder table.variations .reset_variations',
			]
		);

		$this->start_controls_tabs( 'variation_button_tabs' );

		$this->start_controls_tab(
			'variation_clear_button_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'variation_clear_button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#1890ff',
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-variations-form-holder table.variations .reset_variations' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'variation_clear_button_background_color',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-variations-form-holder table.variations .reset_variations' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'variation_clear_button_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'variation_clear_button_hover_text_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-variations-form-holder table.variations .reset_variations:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'variation_clear_button_hover_background_color',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-variations-form-holder table.variations .reset_variations:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'after_variation_clear_btn_tabs_divider',
			[
				'type' => 'divider',
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'variation_clear_button_border',
				'selector' => '{{WRAPPER}} .raven-product-add-to-cart .raven-variations-form-holder table.variations .reset_variations',
			]
		);

		$this->add_control(
			'variation_clear_button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [
					'px',
					'%',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-variations-form-holder table.variations .reset_variations' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'variation_clear_button_margin',
			[
				'label' => esc_html__( 'Margin', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-variations-form-holder table.variations .reset_variations' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'variation_heading_price',
			[
				'label' => esc_html__( 'Variation Price', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'variation_price_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .single_variation_wrap .price' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'variation_price_typography',
				'scheme' => '1',
				'selector' => '{{WRAPPER}} .raven-product-add-to-cart .single_variation_wrap .price',
			]
		);

		$this->add_responsive_control(
			'variation_price_margin',
			[
				'label' => esc_html__( 'Margin', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .single_variation_wrap .price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);
	}

	/**
	 * @return void
	 */
	public function add_style_color_swatch_controls() {
		$this->start_controls_section(
			'section_style_color_swatch',
			[
				'label' => esc_html__( 'Color Swatch', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'variation_swatch_color_size',
			[
				'label' => esc_html__( 'Size', 'jupiterx-core' ),
				'type' => 'slider',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-color li.artbees-was-swatches-item a span span' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}}' => '--raven-add-to-cart-swatch-color-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'variation_swatch_color_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'slider',
				'default' => [
					'size' => 18,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--variation-swatch-color-spacing: {{SIZE}}px',
				],
			]
		);

		$this->start_controls_tabs(
			'variation_swatch_color_states_tabs'
		);

		$this->start_controls_tab(
			'variation_swatch_color_state_unselected_tab',
			[
				'label' => esc_html__( 'Unselected', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'variation_swatch_color_unselected_border',
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-color .artbees-was-content:not(.selected-attribute) span',
			]
		);

		$this->add_responsive_control(
			'variation_swatch_color_unselected_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-color .artbees-was-content:not(.selected-attribute) span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'variation_swatch_color_state_selected_tab',
			[
				'label' => esc_html__( 'Selected', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'variation_swatch_color_selected_border',
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-color .artbees-was-content.selected-attribute span',
			]
		);

		$this->add_responsive_control(
			'variation_swatch_color_selected_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-color .artbees-was-content.selected-attribute span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important',
				],
			]
		);

		$this->add_control(
			'variation_swatch_color_checkmark',
			[
				'label' => esc_html__( 'Add Checkmark', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => '',
				'separator' => 'before',
				'prefix_class' => 'raven-add-to-cart-checkmark-',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'variation_swatch_color_checkmark_size',
			[
				'label' => esc_html__( 'Icon Size', 'jupiterx-core' ),
				'type' => 'slider',
				'range' => [
					'px' => [
						'min' => 30,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 55,
				],
				'condition'   => [
					'variation_swatch_color_checkmark!' => '',
				],
				'selectors' => [
					'{{WRAPPER}}.raven-add-to-cart-checkmark-yes .artbees-was-type-color .artbees-was-content span:before' => 'font-size:calc( var(--raven-add-to-cart-swatch-color-size,30px) * calc( {{SIZE}} / 100 ) )',
				],
			]
		);

		$this->add_control(
			'variation_swatch_color_checkmark_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#fff',
				'condition'   => [
					'variation_swatch_color_checkmark!' => '',
				],
				'selectors' => [
					'{{WRAPPER}}.raven-add-to-cart-checkmark-yes .artbees-was-type-color .artbees-was-content span:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'text-shadow',
			[
				'label' => esc_html__( 'Shadow', 'jupiterx-core' ),
				'name' => 'variation_swatch_color_checkmark_shadow',
				'condition'   => [
					'variation_swatch_color_checkmark!' => '',
				],
				'selector' => '{{WRAPPER}}.raven-add-to-cart-checkmark-yes .artbees-was-type-color .artbees-was-content span:before',
			]
		);

		$this->add_group_control(
			'text-stroke',
			[
				'label' => esc_html__( 'Stroke', 'jupiterx-core' ),
				'name' => 'variation_swatch_color_checkmark_stroke',
				'condition'   => [
					'variation_swatch_color_checkmark!' => '',
				],
				'selector' => '{{WRAPPER}}.raven-add-to-cart-checkmark-yes .artbees-was-type-color .artbees-was-content span:before',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	public function add_style_image_swatch_controls() {
		$this->start_controls_section(
			'section_style_image_swatch',
			[
				'label' => esc_html__( 'Image Swatch', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'variation_swatch_image_size',
			[
				'label' => esc_html__( 'Size', 'jupiterx-core' ),
				'type' => 'slider',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-image li.artbees-was-swatches-item a div img' => 'width: 100% !important; height: 100% !important;',
					'{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-image li.artbees-was-swatches-item a div' => 'max-width: {{SIZE}}{{UNIT}} !important; width: auto !important; height: auto !important;',
				],
			]
		);

		$this->add_control(
			'variation_swatch_image_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'slider',
				'default' => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--variation-swatch-image-spacing: {{SIZE}}px',
				],
			]
		);

		$this->start_controls_tabs(
			'variation_swatch_image_states_tabs'
		);

		$this->start_controls_tab(
			'variation_swatch_image_state_unselected_tab',
			[
				'label' => esc_html__( 'Unselected', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'variation_swatch_image_unselected_border',
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-image .artbees-was-content:not(.selected-attribute) img',
			]
		);

		$this->add_responsive_control(
			'variation_swatch_image_unselected_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => 4,
					'right' => 4,
					'bottom' => 4,
					'left' => 4,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-image .artbees-was-content:not(.selected-attribute) img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'variation_swatch_image_state_selected_tab',
			[
				'label' => esc_html__( 'Selected', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'variation_swatch_image_selected_border',
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-image .artbees-was-content.selected-attribute img',
			]
		);

		$this->add_responsive_control(
			'variation_swatch_image_selected_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => 4,
					'right' => 4,
					'bottom' => 4,
					'left' => 4,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-image .artbees-was-content.selected-attribute img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	public function add_style_text_swatch_controls() {
		$this->start_controls_section(
			'section_style_text_swatch',
			[
				'label' => esc_html__( 'Text Swatch', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'variation_swatch_text_typography',
				'selector' => '{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-text span',
			]
		);

		$this->add_control(
			'variation_swatch_text_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'slider',
				'default' => [
					'size' => 16,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--variation-swatch-text-spacing: {{SIZE}}px',
				],
			]
		);

		$this->add_responsive_control(
			'variation_swatch_text_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-text span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important',
				],
			]
		);

		$this->start_controls_tabs(
			'variation_swatch_text_states_tabs'
		);

		$this->start_controls_tab(
			'variation_swatch_text_state_unselected_tab',
			[
				'label' => esc_html__( 'Unselected', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'variation_swatch_text_state_unselected_text_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-text .artbees-was-content:not(.selected-attribute)' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'variation_swatch_text_unselected_border',
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-text .artbees-was-content:not(.selected-attribute)',
			]
		);

		$this->add_responsive_control(
			'variation_swatch_text_unselected_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-text .artbees-was-content:not(.selected-attribute)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'variation_swatch_text_state_selected_tab',
			[
				'label' => esc_html__( 'Selected', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'variation_swatch_text_state_selected_text_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-text .artbees-was-content.selected-attribute' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'variation_swatch_text_selected_border',
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-text .artbees-was-content.selected-attribute',
			]
		);

		$this->add_responsive_control(
			'variation_swatch_text_selected_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-text .artbees-was-content.selected-attribute' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'variation_swatch_text_width',
			[
				'label' => esc_html__( 'Swatch Item Width', 'jupiterx-core' ),
				'type' => 'slider',
				'separator' => 'before',
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'variation_view' => 'variations-stacked',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-text .artbees-was-swatches-item' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'variation_swatch_text_width_inline',
			[
				'label' => esc_html__( 'Swatch Item Width', 'jupiterx-core' ),
				'type' => 'slider',
				'separator' => 'before',
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'condition' => [
					'variation_view' => 'variations-inline',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-text .artbees-was-swatches-item' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'variation_swatch_text_height',
			[
				'label' => esc_html__( 'Swatch Item Height', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', 'vh' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-variations-form-holder .artbees-was-type-text .artbees-was-swatches-item' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	public function add_style_select_swatch_controls() {
		$this->start_controls_section(
			'section_style_select_swatch',
			[
				'label' => esc_html__( 'Select Swatch', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'variation_text_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#111111',
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-variations-form-holder table.variations select' => 'color: {{VALUE}}',
					'{{WRAPPER}}' => '--raven-add-to-cart-select-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'variation_background_color',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-variations-form-holder table.variations select' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'variation_select_border_color',
			[
				'label' => esc_html__( 'Border Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-variations-form-holder table.variations select' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'variation_select_typography',
				'selector' => '{{WRAPPER}} .raven-product-add-to-cart .raven-variations-form-holder table.variations select',
				'exclude' => [
					'line_height',
				],
			]
		);

		$this->add_control(
			'variation_select_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [
					'px',
					'em',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-variations-form-holder table.variations select' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'variation_select_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-variations-form-holder table.variations select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					'{{WRAPPER}}' => '--raven-add-to-cart-select-swatch-padding-right: {{RIGHT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'variation_select_width',
			[
				'label' => esc_html__( 'Width', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', '%', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'variation_view' => 'variations-stacked',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-variations-form-holder table.variations select' => 'width: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};max-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}' => '--raven-add-to-cart-select-swatch-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'variation_select_width_inline',
			[
				'label' => esc_html__( 'Width', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'condition' => [
					'variation_view' => 'variations-inline',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-add-to-cart .raven-variations-form-holder table.variations select' => 'width: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};max-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}' => '--raven-add-to-cart-select-swatch-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Add an ID attribute to the clear button.
	 *
	 * @since 3.1.0
	 */
	public function add_extra_class_to_clear_button( $html ) {
		$html = str_replace( 'reset_variations"', 'reset_variations" id="jupiterx-clear-variable-button-single-page"', $html );

		return $html;
	}

	/**
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function render() {
		Utils::get_product();

		global $product;

		if ( empty( $product ) ) {
			return;
		}

		$settings               = $this->get_settings_for_display();
		$quantity_view          = $settings['quantity_view'];
		$cart_view              = $settings['cart_view'];
		$variation_view         = $settings['variation_view'];
		$has_default_attributes = '';

		$sellkit_pro_active = $this->check_sellkit_swatches();

		if ( $product->is_type( 'variable' ) && $product->get_default_attributes() ) {
			$has_default_attributes = 'has-default-attributes';
		}

		$this->add_render_attribute(
			'wrapper',
			'class',
			'raven-product-add-to-cart ' . $sellkit_pro_active . ' ' . $cart_view . ' ' . $quantity_view . ' ' . $variation_view . ' ' . $has_default_attributes . ' raven-product-' . esc_attr( $product->get_type() )
		);

		add_filter( 'woocommerce_reset_variations_link', [ $this, 'add_extra_class_to_clear_button' ], 99 );
		add_filter( 'woocommerce_add_to_cart_form_action', [ $this, 'product_add_to_cart_action' ] );
		add_filter( 'woocommerce_dropdown_variation_attribute_options_html', [ $this, 'add_select_arrow' ], 9, 1 );
		add_filter( 'jupiterx_add_to_cart_variation_button', [ $this, 'variation_product_custom_add_to_cart_button' ] );
		add_filter( 'jupiterx_add_to_cart_simple_button', [ $this, 'simple_product_custom_add_to_cart_button' ] );

		// Group product.
		add_filter( 'jupiterx_add_to_cart_group_button', [ $this, 'group_product_custom_add_to_cart_button' ] );
		add_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'group_product_custom_add_to_cart_args' ], 10, 3 );

		if ( in_array( $cart_view, [ 'stacked', 'inline' ], true ) ) {
			//Group product.
			if ( 'grouped' === $product->get_type() ) {
				add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'before_add_to_cart_quantity' ], 95 );
				add_action( 'woocommerce_after_add_to_cart_button', [ $this, 'after_add_to_cart_button' ], 5 );
			}

			add_action( 'woocommerce_before_add_to_cart_quantity', [ $this, 'before_add_to_cart_quantity' ], 95 );
			add_action( 'woocommerce_after_add_to_cart_button', [ $this, 'after_add_to_cart_button' ], 5 );

			add_action( 'woocommerce_before_variations_form', [ $this, 'add_before_variations_form' ], 99 );
			add_action( 'woocommerce_after_variations_table', [ $this, 'add_after_variations_table' ], 99 );

			if ( 'plus_minus' === $quantity_view ) {
				add_action( 'woocommerce_before_quantity_input_field', [ $this, 'add_quantity_minus_sign' ], 99 );
				add_action( 'woocommerce_after_quantity_input_field', [ $this, 'add_quantity_plus_sign' ], 99 );
			}
		}

		$this->add_render_attribute( 'wrapper', 'data-price', false );

		if ( Plugin::instance()->editor->is_edit_mode() && $product->get_price() ) {
			$this->add_render_attribute( 'wrapper', 'data-price', $product->get_price() );
		}
		?>

		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php woocommerce_template_single_add_to_cart(); ?>
		</div>

		<?php
		remove_filter( 'woocommerce_add_to_cart_form_action', [ $this, 'product_add_to_cart_action' ] );
		remove_filter( 'woocommerce_dropdown_variation_attribute_options_html', [ $this, 'add_select_arrow' ], 9, 1 );
		remove_filter( 'jupiterx_add_to_cart_variation_button', [ $this, 'variation_product_custom_add_to_cart_button' ] );
		remove_filter( 'jupiterx_add_to_cart_simple_button', [ $this, 'simple_product_custom_add_to_cart_button' ] );

		// Group product.
		remove_filter( 'jupiterx_add_to_cart_group_button', [ $this, 'group_product_custom_add_to_cart_button' ] );
		remove_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'group_product_custom_add_to_cart_args' ], 10, 3 );

		if ( in_array( $cart_view, [ 'stacked', 'inline' ], true ) ) {
			//Group product.
			if ( 'grouped' === $product->get_type() ) {
				remove_action( 'woocommerce_before_add_to_cart_button', [ $this, 'before_add_to_cart_quantity' ], 95 );
				remove_action( 'woocommerce_after_add_to_cart_button', [ $this, 'after_add_to_cart_button' ], 5 );
			}

			remove_action( 'woocommerce_before_add_to_cart_quantity', [ $this, 'before_add_to_cart_quantity' ], 95 );
			remove_action( 'woocommerce_after_add_to_cart_button', [ $this, 'after_add_to_cart_button' ], 5 );

			remove_action( 'woocommerce_before_variations_form', [ $this, 'add_before_variations_form' ], 99 );
			remove_action( 'woocommerce_after_variations_table', [ $this, 'add_after_variations_table' ], 99 );

			if ( 'plus_minus' === $quantity_view ) {
				remove_action( 'woocommerce_before_quantity_input_field', [ $this, 'add_quantity_minus_sign' ], 99 );
				remove_action( 'woocommerce_after_quantity_input_field', [ $this, 'add_quantity_plus_sign' ], 99 );
			}
		}
	}

	public function product_add_to_cart_action( $permalink ) {
		if ( is_product() ) {
			return $permalink;
		}

		return get_the_permalink();
	}

	public function simple_product_custom_add_to_cart_button( $data ) {
		$icon = '<div class="elementor-icon">' . $this->render_icon( 'button_icon' ) . '</div>';

		$button = sprintf(
			'<button type="submit" name="add-to-cart" value="%1$s" class="single_add_to_cart_button button alt">%2$s %3$s</button>',
			esc_attr( $data['id'] ),
			$icon,
			esc_html( $data['text'] )
		);

		return [
			'text' => $data['text'],
			'id' => $data['id'],
			'button' => $button,
		];
	}

	public function variation_product_custom_add_to_cart_button( $data ) {
		$icon = '<div class="elementor-icon">' . $this->render_icon( 'button_icon' ) . '</div>';

		$button = sprintf(
			'<button type="submit" class="single_add_to_cart_button button alt ">%1$s %2$s</button>',
			$icon,
			esc_html( $data['text'] )
		);

		return [
			'text' => $data['text'],
			'button' => $button,
		];
	}

	public function group_product_custom_add_to_cart_button( $data ) {
		$icon = '<div class="elementor-icon">' . $this->render_icon( 'button_icon' ) . '</div>';

		$button = sprintf(
			'<button type="submit" class="single_add_to_cart_button button alt">%1$s %2$s</button>',
			$icon,
			esc_html( $data['text'] )
		);

		return [
			'text' => $data['text'],
			'button' => $button,
		];
	}

	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function group_product_custom_add_to_cart_args( $html, $product, $args ) {
		$icon = '<div class="elementor-icon">' . $this->render_icon( 'button_icon' ) . '</div>';

		return sprintf(
			'<a href="%1$s" data-quantity="%2$s" class="%3$s" %4$s>%6$s %5$s</a>',
			esc_url( $product->add_to_cart_url() ),
			esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
			esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			esc_html( $product->add_to_cart_text() ),
			$icon
		);
	}

	public function render_icon( $name ) {
		$settings = $this->get_settings_for_display();

		ob_start();

		Icons_Manager::render_icon( $settings[ $name ] );

		return ob_get_clean();
	}

	public function add_select_arrow( $html ) {
		$html .= '<svg aria-hidden="true" style="display:none;" class="e-font-icon-svg e-fas-chevron-down" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M207.029 381.476L12.686 187.132c-9.373-9.373-9.373-24.569 0-33.941l22.667-22.667c9.357-9.357 24.522-9.375 33.901-.04L224 284.505l154.745-154.021c9.379-9.335 24.544-9.317 33.901.04l22.667 22.667c9.373 9.373 9.373 24.569 0 33.941L240.971 381.476c-9.373 9.372-24.569 9.372-33.942 0z"></path></svg>';

		return $html;
	}

	/**
	 * Before Add to Cart Quantity
	 *
	 * @return void
	 */
	public function before_add_to_cart_quantity() {
		echo '<div class="raven-qty-button-holder">';
	}

	/**
	 * After Add to Cart Button
	 *
	 * @return void
	 */
	public function after_add_to_cart_button() {
		echo '</div>';
	}

	/**
	 * Add plus button before quantity input field.
	 *
	 * @return void
	 */
	public function add_quantity_plus_sign() {
		echo '<button type="button" class="plus plus-minus-btn" >+</button></div>';
	}

	/**
	 * Add minus button before quantity input field.
	 *
	 * @return void
	 */
	public function add_quantity_minus_sign() {
		echo '<div class="raven-qty-button-holder-inner has-plus-minus-button"><button type="button" class="minus plus-minus-btn" >-</button>';
	}

	/**
	 * Add wrapper tag around the variations form.
	 *
	 * @return void
	 */
	public function add_before_variations_form() {
		echo '<div class="raven-variations-form-holder">';
	}

	/**
	 * Add wrapper tag around the variations form.
	 *
	 * @return void
	 */
	public function add_after_variations_table() {
		echo '</div>';
	}

	/**
	 * Check sellkit swatches is activated.
	 *
	 * @return string
	 */
	private function check_sellkit_swatches() {
		if ( ! class_exists( 'Sellkit_Pro' ) ) {
			return 'sellkit-pro-deactive';
		}

		$options = get_option( 'sellkit', [] );

		if ( empty( $options ) ) {
			return '';
		}

		if ( ! isset( $options['variation_swatches_activity_status'] ) ) {
			return '';
		}

		if ( empty( $options['variation_swatches_activity_status'] ) ) {
			return 'sellkit-pro-deactive';
		}

		return '';
	}
}
