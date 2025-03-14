<?php
namespace JupiterX_Core\Raven\Modules\Shopping_Cart\Widgets;

use JupiterX_Core\Raven\Base\Base_Widget;
use JupiterX_Core\Raven\Utils;
use JupiterX_Core\Raven\Modules\Shopping_Cart\Module;

defined( 'ABSPATH' ) || die();

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class Shopping_Cart extends Base_Widget {

	public static function is_active() {
		return class_exists( 'woocommerce' );
	}

	public function get_name() {
		return 'raven-shopping-cart';
	}

	public function get_title() {
		return __( 'Shopping Cart', 'jupiterx-core' );
	}

	public function get_icon() {
		return 'raven-element-icon raven-element-icon-shopping-cart';
	}

	public function get_script_depends() {
		return [
			'wc-add-to-cart',
		];
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_custom_texts_controls();
		$this->register_icon_controls();
		$this->register_number_controls();
		$this->register_cart_quick_view_controls();
		$this->register_cart_quick_view_content_controls();
		$this->register_content_effects_controls();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => __( 'Choose Icon', 'jupiterx-core' ),
				'type' => 'icon',
				'default' => 'jupiterx-icon-shopping-cart-6', // Use Jupiter X icons as default.
			]
		);

		$this->add_control(
			'skin',
			[
				'label' => esc_html__( 'Default Skin', 'jupiterx-core' ),
				'type' => 'select',
				'options' => [
					'light' => esc_html__( 'Light', 'jupiterx-core' ),
					'dark' => esc_html__( 'Dark', 'jupiterx-core' ),
				],
				'render_type' => 'template',
				'prefix_class' => 'raven-shopping-cart-skin-',
				'default' => 'light',
			]
		);

		$this->add_control(
			'show_cart_quick_view',
			[
				'label' => esc_html__( 'Cart Quick View', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_off' => esc_html__( 'No', 'jupiterx-core' ),
				'label_on' => esc_html__( 'Yes', 'jupiterx-core' ),
				'default' => 'yes',
				'render_type' => 'template',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'enable_ajax_add_to_cart',
			[
				'label' => esc_html__( 'Enable Ajax', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_off' => esc_html__( 'No', 'jupiterx-core' ),
				'label_on' => esc_html__( 'Yes', 'jupiterx-core' ),
				'default' => 'yes',
				'render_type' => 'template',
				'frontend_available' => true,
				'condition' => [
					'show_cart_quick_view!' => '',
				],
			]
		);

		$this->add_control(
			'show_cart_quick_view_thumbnail',
			[
				'label' => esc_html__( 'Thumbnail', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'prefix_class' => 'raven-shopping-cart-remove-thumbnail-',
				'default' => 'yes',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'show_cart_quick_view_button',
			[
				'label' => esc_html__( 'View Cart Button', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'prefix_class' => 'raven-shopping-cart-remove-view-cart-',
				'default' => 'yes',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'tap_outside_close',
			[
				'label' => esc_html__( 'Tap Outside to Close', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_off' => esc_html__( 'No', 'jupiterx-core' ),
				'label_on' => esc_html__( 'Yes', 'jupiterx-core' ),
				'default' => 'no',
				'return_value'       => 'yes',
				'render_type'        => 'template',
				'frontend_available' => true,
				'description'    => esc_html__( 'Close the Cart Quick view when clicking outside of it.', 'jupiterx-core' ),
				'condition' => [
					'show_cart_quick_view!' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_custom_texts_controls() {
		$this->start_controls_section(
			'section_cutom_texts',
			[
				'label' => esc_html__( 'Custom Texts', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'view_cart_button_text',
			[
				'label' => esc_html__( 'View Cart Button', 'jupiterx-core' ),
				'label_block' => true,
				'type' => 'text',
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Edit Cart', 'jupiterx-core' ),
				'default' => esc_html__( 'Edit Cart', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'quick_view_heading',
			[
				'label' => esc_html__( 'Quick View Heading', 'jupiterx-core' ),
				'label_block' => true,
				'type' => 'text',
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Cart', 'jupiterx-core' ),
				'default' => esc_html__( 'Cart', 'jupiterx-core' ),
			]
		);

		$this->end_controls_section();
	}

	protected function register_icon_controls() {
		$this->start_controls_section(
			'section_icon',
			[
				'label' => __( 'Icon', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-shopping-cart-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'padding',
			[
				'label' => __( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-shopping-cart-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
				'options' => [
					'left' => [
						'title' => __( 'Left', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => Utils::get_direction( 'left' ),
				'selectors' => [
					'{{WRAPPER}} .raven-shopping-cart-wrap' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->start_controls_tabs( 'icon_tabs' );

		$this->start_controls_tab(
			'icon_tab_normal',
			[
				'label' => __( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'color',
			[
				'label' => __( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-shopping-cart-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => __( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-shopping-cart-icon' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'border_heading',
			[
				'label' => __( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => __( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-shopping-cart-icon' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'border',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => __( 'Border Width', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .raven-shopping-cart-icon',
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => __( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-shopping-cart-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'box_shadow',
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .raven-shopping-cart-icon',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_tab_hover',
			[
				'label' => __( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label' => __( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-shopping-cart-icon:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_background_color',
			[
				'label' => __( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-shopping-cart-icon:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_border_heading',
			[
				'label' => __( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
			]
		);

		$this->add_control(
			'hover_border_color',
			[
				'label' => __( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-shopping-cart-icon:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'hover_border',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => __( 'Border Width', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .raven-shopping-cart-icon:hover',
			]
		);

		$this->add_control(
			'hover_border_radius',
			[
				'label' => __( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-shopping-cart-icon:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'hover_box_shadow',
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .raven-shopping-cart-icon:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_number_controls() {
		$this->start_controls_section(
			'section_number',
			[
				'label' => __( 'Number', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'number_typography',
				'scheme' => '3',
				'selector' => '{{WRAPPER}} .raven-shopping-cart-count',
			]
		);

		$this->add_control(
			'number_color',
			[
				'label' => __( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-shopping-cart-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'number_background_color',
			[
				'label' => __( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-shopping-cart-count' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'number_width',
			[
				'label' => __( 'Width', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-shopping-cart-count' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'number_height',
			[
				'label' => __( 'Height', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-shopping-cart-count' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'number_spacing',
			[
				'label' => __( 'Spacing', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-shopping-cart-count' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'number_padding',
			[
				'label' => __( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-shopping-cart-count' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'number_border_heading',
			[
				'label' => __( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'number_border_radius',
			[
				'label' => __( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-shopping-cart-count' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_cart_quick_view_controls() {
		$this->start_controls_section(
			'section_cart_quick_view',
			[
				'label' => esc_html__( 'Quick View Container', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'cart_quick_view_position',
			[
				'label' => __( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
				'options' => [
					'left' => [
						'title' => __( 'Left', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-left',
					],
					'right' => [
						'title' => __( 'Right', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'right',
				'render_type' => 'template',
				'prefix_class' => 'raven-shopping-quick-view-align-',
			]
		);

		$this->add_responsive_control(
			'cart_quick_view_width',
			[
				'label' => esc_html__( 'Width', 'jupiterx-core' ),
				'type' => 'slider',
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
				'default' => [
					'size' => '400',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}}.jupiterx-raven-cart-quick-view-overlay .jupiterx-cart-quick-view' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.jupiterx-raven-cart-quick-view-overlay .jupiterx-shopping-cart-content-effect-enabled-overlay' => 'width: calc( 100% - {{SIZE}}{{UNIT}} );',
				],
			]
		);

		$this->add_control(
			'cart_quick_view_background_color',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#fffff',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-cart-quick-view' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_control(
			'cart_quick_view_background_color_dark',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#232323',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-cart-quick-view' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->add_responsive_control(
			'cart_quick_view_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'separator' => 'before',
				'default' => [
					'top' => '0',
					'right' => '30',
					'bottom' => '15',
					'left' => '30',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .jupiterx-cart-quick-view .widget_shopping_cart_content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'cart_quick_view_border_size',
			[
				'label' => esc_html__( 'Border Size', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
						'step' => 1,
					],
					'em' => [
						'min' => 0.1,
						'max' => 2,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jupiterx-cart-quick-view[data-position="right"]' => 'border-width: 0 0 0 {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .jupiterx-cart-quick-view[data-position="left"]' => 'border-left-width: 0;border-right-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'cart_quick_view_border_color',
			[
				'label' => esc_html__( 'Border Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#E3E3E3',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-cart-quick-view' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_control(
			'cart_quick_view_border_color_dark',
			[
				'label' => esc_html__( 'Border Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#3A3A3A',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-cart-quick-view' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_content_effects_controls() {
		$this->start_controls_section(
			'section_content_effect',
			[
				'label' => esc_html__( 'Content Effect', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control( 'content_effect_blur_content', [
			'label'              => esc_html__( 'Blur Content', 'jupiterx-core' ),
			'type'               => 'switcher',
			'prefix_class'       => 'raven-shopping-cart-blur-content-',
			'return_value'       => 'enabled',
			'render_type'        => 'template',
			'frontend_available' => true,
		] );

		$this->add_control( 'content_effect_blur_intensity', [
			'label'      => esc_html__( 'Blur Intensity', 'jupiterx-core' ),
			'type'       => 'slider',
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min' => 1,
					'max' => 20,
				],
			],
			'default'    => [ 'size' => 5 ],
			'frontend_available' => true,
			'condition'   => [
				'content_effect_blur_content' => 'enabled',
			],
			'selectors' => [
				'{{WRAPPER}} .jupiterx-shopping-cart-content-effect-enabled-overlay' => 'backdrop-filter: blur({{SIZE}}{{UNIT}});',
			],
		] );

		$this->add_control( 'content_effect_content_overlay', [
			'label'              => esc_html__( 'Content Overlay', 'jupiterx-core' ),
			'type'               => 'switcher',
			'prefix_class'       => 'raven-shopping-cart-blur-content-overlay-',
			'return_value'       => 'enabled',
			'render_type'        => 'template',
			'frontend_available' => true,
		] );

		$this->add_control( 'content_effect_content_overlay_color',
			[
				'label'     => esc_html__( 'Overlay Color', 'jupiterx-core' ),
				'type'      => 'color',
				'default'   => 'rgba(0,0,0,0.5)',
				'condition'   => [
					'content_effect_content_overlay' => 'enabled',
				],
				'selectors' => [
					'{{WRAPPER}} .jupiterx-shopping-cart-content-effect-enabled-overlay' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function register_cart_quick_view_content_controls() {
		$this->start_controls_section(
			'section_cart_quick_view_content',
			[
				'label' => esc_html__( 'Quick View Content', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_heading',
			[
				'label' => esc_html__( 'Heading', 'jupiterx-core' ),
				'type' => 'heading',
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_heading_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-mini-cart-title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_heading_color_dark',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-mini-cart-title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'section_cart_quick_view_content_heading_typography',
				'selector' => '{{WRAPPER}} .jupiterx-mini-cart-title',
			]
		);

		$this->add_responsive_control(
			'section_cart_quick_view_content_heading_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => '25',
					'right' => '30',
					'bottom' => '25',
					'left' => '30',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .jupiterx-mini-cart-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'section_cart_quick_view_content_heading_margin',
			[
				'label' => esc_html__( 'Margin', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .jupiterx-mini-cart-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_close_button',
			[
				'label' => esc_html__( 'Close Button', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_close_button_size',
			[
				'label' => esc_html__( 'Size', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jupiterx-mini-cart-header .jupiterx-icon-x svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'section_cart_quick_view_content_close_button_tabs' );

		$this->start_controls_tab(
			'section_cart_quick_view_content_close_button_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_close_button_normal_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#ADADAD',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-mini-cart-header .jupiterx-icon-x svg' => 'color: {{VALUE}};fill: {{VALUE}};',
				],
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_close_button_normal_color_dark',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#737373',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-mini-cart-header .jupiterx-icon-x svg' => 'color: {{VALUE}}; fill: {{VALUE}};',
				],
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'section_cart_quick_view_content_close_button_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_close_button_hover_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#ADADAD',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-mini-cart-header .jupiterx-icon-x:hover svg' => 'color: {{VALUE}}; fill: {{VALUE}};;',
				],
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_close_button_hover_color_dark',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#737373',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-mini-cart-header .jupiterx-icon-x:hover svg' => 'color: {{VALUE}}; fill: {{VALUE}};;',
				],
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'section_cart_quick_view_content_dividers',
			[
				'label' => esc_html__( 'Dividers', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_dividers_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#E3E3E3',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-mini-cart-header' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .widget_shopping_cart_content li.mini_cart_item' => 'border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce.widget_shopping_cart .total' => 'border-top-style: solid;border-top-color: {{VALUE}} !important;border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_dividers_color_dark',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#3A3A3A',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-mini-cart-header' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .widget_shopping_cart_content li.mini_cart_item' => 'border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce.widget_shopping_cart .total' => 'border-top-style: solid;border-top-color: {{VALUE}} !important;border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_dividers_weight',
			[
				'label' => esc_html__( 'Weight', 'jupiterx-core' ),
				'type' => 'slider',
				'default' => [
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .jupiterx-mini-cart-header' => 'border-width: 0 0 {{SIZE}}{{UNIT}} 0;',
					'{{WRAPPER}} .widget_shopping_cart_content li.mini_cart_item' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .woocommerce.widget_shopping_cart .total' => 'border-top-style: solid;border-top-width: {{SIZE}}{{UNIT}} !important;border-bottom-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'section_cart_quick_view_content_dividers_gap',
			[
				'label' => esc_html__( 'Gap', 'jupiterx-core' ),
				'type' => 'slider',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-mini-cart-header' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .widget_shopping_cart_content li.mini_cart_item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .woocommerce.widget_shopping_cart .total' => 'margin-top: {{SIZE}}{{UNIT}} !important;margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_texts',
			[
				'label' => esc_html__( 'Texts', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_texts_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart-item .quantity' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .woocommerce-mini-cart-item .woocommerce-mini-cart-item-attributes span' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_texts_color_dark',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart-item .quantity' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .woocommerce-mini-cart-item .woocommerce-mini-cart-item-attributes span' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'section_cart_quick_view_content_texts_typography',
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart-item .quantity, {{WRAPPER}} .woocommerce-mini-cart-item .woocommerce-mini-cart-item-attributes span',
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_links',
			[
				'label' => esc_html__( 'Links', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'section_cart_quick_view_content_links_typography',
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart-item a:not(.remove_from_cart_button)',
			]
		);

		$this->start_controls_tabs( 'section_cart_quick_view_content_links_tabs' );

		$this->start_controls_tab(
			'section_cart_quick_view_content_links_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_links_normal_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart-item a:not(.remove_from_cart_button)' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_links_normal_color_dark',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart-item a:not(.remove_from_cart_button)' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'section_cart_quick_view_content_links_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_links_hover_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart-item a:not(.remove_from_cart_button):hover' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_links_hover_color_dark',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart-item a:not(.remove_from_cart_button):hover' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'section_cart_quick_view_content_subtotal',
			[
				'label' => esc_html__( 'Subtotal', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_subtotal_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart__total > *' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_subtotal_color_dark',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart__total > *' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'section_cart_quick_view_content_subtotal_typography',
				'selector' => '{{WRAPPER}} .widget_shopping_cart_content .woocommerce-mini-cart__total.total > *',
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_checkout_button',
			[
				'label' => esc_html__( 'Checkout Button', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'section_cart_quick_view_content_checkout_button_typography',
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a.checkout',
			]
		);

		$this->start_controls_tabs( 'section_cart_quick_view_content_checkout_button_tabs' );

		$this->start_controls_tab(
			'section_cart_quick_view_content_checkout_button_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_checkout_button_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a.checkout' => 'color: {{VALUE}};',
				],
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_checkout_button_color_dark',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#232323',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a.checkout' => 'color: {{VALUE}};',
				],
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name' => 'section_cart_quick_view_content_checkout_button_background',
				'label' => esc_html__( 'Background Type', 'jupiterx-core' ),
				'types' => [ 'classic', 'gradient' ],
				'fields_options' => [
					'color' => [
						'default' => '#232323',
					],
					'background' => [
						'default' => 'classic',
					],
				],
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a.checkout',
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name' => 'section_cart_quick_view_content_checkout_button_background_dark',
				'label' => esc_html__( 'Background Type', 'jupiterx-core' ),
				'types' => [ 'classic', 'gradient' ],
				'fields_options' => [
					'color' => [
						'default' => '#FFFFFF',
					],
					'background' => [
						'default' => 'classic',
					],
				],
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a.checkout',
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'section_cart_quick_view_content_checkout_button_box_shadow',
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a.checkout',
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'section_cart_quick_view_content_checkout_button_box_shadow_dark',
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a.checkout',
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'section_cart_quick_view_content_checkout_button_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_checkout_button_color_hover',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a.checkout:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_checkout_button_color_hover_dark',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#232323',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a.checkout:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name' => 'section_cart_quick_view_content_checkout_button_background_hover',
				'label' => esc_html__( 'Background Type', 'jupiterx-core' ),
				'types' => [ 'classic', 'gradient' ],
				'fields_options' => [
					'color' => [
						'default' => '#000000',
					],
					'background' => [
						'default' => 'classic',
					],
				],
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a.checkout:hover',
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name' => 'section_cart_quick_view_content_checkout_button_background_hover_dark',
				'label' => esc_html__( 'Background Type', 'jupiterx-core' ),
				'types' => [ 'classic', 'gradient' ],
				'fields_options' => [
					'color' => [
						'default' => '#F3F3F3',
					],
					'background' => [
						'default' => 'classic',
					],
				],
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a.checkout:hover',
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'section_cart_quick_view_content_checkout_button_box_shadow_hover',
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a.checkout:hover',
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'section_cart_quick_view_content_checkout_button_box_shadow_hover_dark',
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a.checkout:hover',
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			'border',
			[
				'name' => 'section_cart_quick_view_content_checkout_button_border',
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a.checkout, {{WRAPPER}} .woocommerce-mini-cart__buttons a.checkout:hover',
			]
		);

		$this->add_responsive_control(
			'section_cart_quick_view_content_checkout_button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => '4',
					'right' => '4',
					'bottom' => '4',
					'left' => '4',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a.checkout' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'section_cart_quick_view_content_checkout_button_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '15',
					'right' => '30',
					'bottom' => '15',
					'left' => '30',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a.checkout' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_view_cart_button',
			[
				'label' => esc_html__( 'View Cart Button', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'section_cart_quick_view_content_view_cart_button_typography',
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a:not(.checkout)',
			]
		);

		$this->start_controls_tabs( 'section_cart_quick_view_content_view_cart_button_tabs' );

		$this->start_controls_tab(
			'section_cart_quick_view_content_view_cart_button_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_view_cart_button_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#232323',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a:not(.checkout)' => 'color: {{VALUE}};',
				],
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_view_cart_button_color_dark',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a:not(.checkout)' => 'color: {{VALUE}};',
				],
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name' => 'section_cart_quick_view_content_view_cart_button_background',
				'label' => esc_html__( 'Background Type', 'jupiterx-core' ),
				'types' => [ 'classic', 'gradient' ],
				'fields_options' => [
					'color' => [
						'default' => '#FFFFFF',
					],
					'background' => [
						'default' => 'classic',
					],
				],
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a:not(.checkout)',
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name' => 'section_cart_quick_view_content_view_cart_button_background_dark',
				'label' => esc_html__( 'Background Type', 'jupiterx-core' ),
				'types' => [ 'classic', 'gradient' ],
				'fields_options' => [
					'color' => [
						'default' => '#232323',
					],
					'background' => [
						'default' => 'classic',
					],
				],
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a:not(.checkout)',
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'section_cart_quick_view_content_view_cart_button_box_shadow',
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a:not(.checkout)',
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'section_cart_quick_view_content_view_cart_button_box_shadow_dark',
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a:not(.checkout)',
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'section_cart_quick_view_content_view_cart_button_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_view_cart_button_color_hover',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a:not(.checkout):hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_control(
			'section_cart_quick_view_content_view_cart_button_color_hover_dark',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#232323',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a:not(.checkout):hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name' => 'section_cart_quick_view_content_view_cart_button_background_hover',
				'label' => esc_html__( 'Background Type', 'jupiterx-core' ),
				'types' => [ 'classic', 'gradient' ],
				'fields_options' => [
					'color' => [
						'default' => '#232323',
					],
					'background' => [
						'default' => 'classic',
					],
				],
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a:not(.checkout):hover',
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name' => 'section_cart_quick_view_content_view_cart_button_background_hover_dark',
				'label' => esc_html__( 'Background Type', 'jupiterx-core' ),
				'types' => [ 'classic', 'gradient' ],
				'fields_options' => [
					'color' => [
						'default' => '#FFFFFF',
					],
					'background' => [
						'default' => 'classic',
					],
				],
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a:not(.checkout):hover',
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'section_cart_quick_view_content_view_cart_button_box_shadow_hover',
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a:not(.checkout):hover',
				'condition' => [
					'skin' => 'light',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'section_cart_quick_view_content_view_cart_button_box_shadow_hover_dark',
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a:not(.checkout):hover',
				'condition' => [
					'skin' => 'dark',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			'border',
			[
				'name' => 'section_cart_quick_view_content_view_cart_button_border',
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .woocommerce-mini-cart__buttons a:not(.checkout)',
			]
		);

		$this->add_responsive_control(
			'section_cart_quick_view_content_view_cart_button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => '4',
					'right' => '4',
					'bottom' => '4',
					'left' => '4',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a:not(.checkout)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'section_cart_quick_view_content_view_cart_button_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '15',
					'right' => '30',
					'bottom' => '15',
					'left' => '30',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-mini-cart__buttons a:not(.checkout)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$editor     = \Elementor\Plugin::$instance->editor;
		$settings   = $this->get_settings_for_display();
		$cart_url   = ( 'yes' === $settings['show_cart_quick_view'] ) ? '#' : esc_url( wc_get_cart_url() );
		$cart_count = ! $editor->is_edit_mode() ? WC()->cart->cart_contents_count : 0;

		if (
			$editor->is_edit_mode() &&
			function_exists( 'wc' ) &&
			is_object( WC()->cart )
		) {
			$cart_count = WC()->cart->cart_contents_count;
		}

		if ( $editor->is_edit_mode() ) {
			$cart_url = '#';
		}

		remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_button_view_cart', 10 );
		add_action( 'woocommerce_widget_shopping_cart_buttons', [ $this, 'shopping_cart_widget_button_view_cart' ], 10 );

		$is_product_page             = is_product();
		$is_product_addons_activated = class_exists( 'WC_Product_Addons' ) ? 'yes' : 'no';
		?>
		<div class="raven-shopping-cart-wrap" data-is-product="<?php echo esc_attr( $is_product_page ); ?>" data-is-product-addons-activated="<?php echo esc_attr( $is_product_addons_activated ); ?>">
			<a class="raven-shopping-cart" href="<?php echo esc_url( $cart_url ); ?>">
				<span class="raven-shopping-cart-icon <?php echo esc_attr( $settings['icon'] ); ?>"></span>
				<span class="raven-shopping-cart-count"><?php echo wp_kses_post( $cart_count ); ?></span>
			</a>
			<?php $this->render_quick_cart_view(); ?>
		</div>
		<?php

		add_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_button_view_cart', 10 );
		add_action( 'woocommerce_widget_shopping_cart_total', 'woocommerce_widget_shopping_cart_subtotal', 10 );
		remove_action( 'woocommerce_widget_shopping_cart_buttons', [ $this, 'shopping_cart_widget_button_view_cart' ], 10 );
		remove_action( 'woocommerce_widget_shopping_cart_total', [ $this, 'shopping_cart_subtotal' ], 10 );
	}

	public function shopping_cart_widget_button_view_cart() {
		$settings = $this->get_settings_for_display();

		$wp_button_class = wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '';
		echo '<a href="' . esc_url( wc_get_cart_url() ) . '" class="button view-cart wc-forward' . esc_attr( $wp_button_class ) . '">' . esc_html( $settings['view_cart_button_text'] ) . '</a>';
	}

	protected function render_quick_cart_view() {
		$settings = $this->get_settings_for_display();
		$editor   = \Elementor\Plugin::$instance->editor;

		$cart_qucik_view = 'yes' !== $settings['show_cart_quick_view'];

		if ( $editor->is_edit_mode() ) {
			$cart_qucik_view = false;
		}

		$enable_quick_view = apply_filters( 'jupiterx_shopping_cart_enable_quick_view', true );

		if ( $cart_qucik_view && $enable_quick_view ) {
			return;
		}

		$close_icon = '<svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.50012 4.29297L2.20715 0L0.792939 1.41421L5.08591 5.70718L0.793091 10L2.2073 11.4142L6.50012 7.1214L10.7929 11.4142L12.2072 10L7.91434 5.70718L12.2073 1.41421L10.7931 0L6.50012 4.29297Z" fill="currentColor"/></svg>';

		jupiterx_open_markup_e(
			'jupiterx_cart_quick_view',
			'div',
			[
				'class' => 'jupiterx-cart-quick-view',
				'data-position' => $settings['cart_quick_view_position'],
			]
		);

			jupiterx_open_markup_e( 'jupiterx_mini_cart_header', 'div', 'class=jupiterx-mini-cart-header' );

				jupiterx_open_markup_e( 'jupiterx_mini_cart_title', 'p', 'class=jupiterx-mini-cart-title' );

					jupiterx_output_e( 'jupiterx_mini_cart_title_text', $settings['quick_view_heading'] );

				jupiterx_close_markup_e( 'jupiterx_mini_cart_title', 'p' );

				jupiterx_open_markup_e(
					'jupiterx_mini_cart_close',
					'button',
					[
						'class' => 'btn jupiterx-raven-mini-cart-close jupiterx-icon-x',
						'role' => 'button',
					]
				);

				jupiterx_output_e( 'jupiterx_mini_cart_close_icon', $close_icon );

				jupiterx_close_markup_e( 'jupiterx_mini_cart_close', 'button' );

			jupiterx_close_markup_e( 'jupiterx_mini_cart_header', 'div' );

			if ( ! empty( WC()->cart ) ) {
				echo '<div class="widget woocommerce widget_shopping_cart"><div class="widget_shopping_cart_content">' . Module::render_mini_cart() . '</div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

		jupiterx_close_markup_e( 'jupiterx_cart_quick_view', 'div' );

		jupiterx_open_markup_e( 'jupiterx_mini_cart_overlay', 'div', 'class=jupiterx-shopping-cart-content-effect-enabled-overlay' );
		jupiterx_close_markup_e( 'jupiterx_mini_cart_overlay', 'div' );
	}
}
