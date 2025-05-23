<?php
namespace JupiterX_Core\Raven\Modules\Button\Widgets;

use JupiterX_Core\Raven\Base\Base_Widget;
use Elementor\Plugin as Elementor;
use JupiterX_Core\Raven\Utils;
use JupiterX_Core\Raven\Modules\Button\Module;
use Elementor\Utils as ElementorUtils;

defined( 'ABSPATH' ) || die();

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Button extends Base_Widget {

	protected $_has_template_content = false;

	public function get_name() {
		return 'raven-button';
	}

	public function get_title() {
		return esc_html__( 'Button', 'jupiterx-core' );
	}

	public function get_icon() {
		return 'raven-element-icon raven-element-icon-button';
	}

	public function get_style_depends() {
		return [
			'e-animation-grow',
			'e-animation-shrink',
			'e-animation-pulse',
			'e-animation-pop',
			'e-animation-grow-rotate',
			'e-animation-wobble-skew',
			'e-animation-buzz-out',
		];
	}

	protected function register_controls() {
		$this->register_section_content();
		$this->register_section_container();
		$this->register_section_content_style();
		$this->register_section_icon();
		$this->register_popup_controls();
		$post_id = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );

		if ( 'jupiterx-popups' === get_post_type( $post_id ) ) {
			if ( class_exists( 'WooCommerce' ) ) {
				$this->update_control(
					'link',
					[
						'condition' => [
							'show_as_add_to_cart!' => 'yes',
							'turn_to_popup_action_button!' => 'yes',
						],
					]
				);
			} else {
				$this->update_control(
					'link',
					[
						'condition' => [
							'turn_to_popup_action_button!' => 'yes',
						],
					]
				);
			}
		} else {
			$this->update_control(
				'turn_to_popup_action_button',
				[
					'type' => 'hidden',
				]
			);

			$this->update_control(
				'popup_action_type',
				[
					'type' => 'hidden',
				]
			);
		}
	}

	private function register_section_content() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'jupiterx-core' ),
			]
		);

		$this->add_responsive_control(
			'text_align',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
				'default' => 'center',
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
						'title' => esc_html__( 'Justified', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
			]
		);

		$this->add_control(
			'text',
			[
				'label' => esc_html__( 'Text', 'jupiterx-core' ),
				'type' => 'text',
				'placeholder' => esc_html__( 'Enter your text', 'jupiterx-core' ),
				'default' => esc_html__( 'Click me', 'jupiterx-core' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'subtext',
			[
				'label' => esc_html__( 'Subtext', 'jupiterx-core' ),
				'type' => 'textarea',
				'placeholder' => esc_html__( 'Enter your subtext', 'jupiterx-core' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'icon_new',
			[
				'label' => esc_html__( 'Choose Icon', 'jupiterx-core' ),
				'type' => 'icons',
				'fa4compatibility' => 'icon',
			]
		);

		if ( class_exists( 'WooCommerce' ) ) {
			$this->add_control(
				'show_as_add_to_cart',
				[
					'label' => esc_html__( 'Show as Add to cart button', 'jupiterx-core' ),
					'type' => 'switcher',
					'label_on' => esc_html__( 'Yes', 'jupiterx-core' ),
					'label_off' => esc_html__( 'No', 'jupiterx-core' ),
					'return_value' => 'yes',
					'default' => 'no',
				]
			);

			$this->add_group_control(
				'raven-posts',
				[
					'name' => 'product',
					'post_type' => 'product',
					'exclude' => [ 'authors', 'taxonomies' ],
					'fields_options' => [
						'product_includes' => [
							'label' => esc_html__( 'Product', 'jupiterx-core' ),
							'multiple' => false,
							'render_type' => 'template',
						],
					],
					'condition' => [
						'show_as_add_to_cart' => 'yes',
					],
				]
			);

			$this->add_control(
				'link',
				[
					'label' => esc_html__( 'Link', 'jupiterx-core' ),
					'type' => 'url',
					'placeholder' => esc_html__( 'Enter your web address', 'jupiterx-core' ),
					'default' => [
						'url' => '#',
					],
					'condition' => [
						'show_as_add_to_cart!' => 'yes',
					],
					'dynamic' => [
						'active' => true,
					],
				]
			);
		} else {
			$this->add_control(
				'link',
				[
					'label' => esc_html__( 'Link', 'jupiterx-core' ),
					'type' => 'url',
					'placeholder' => esc_html__( 'Enter your web address', 'jupiterx-core' ),
					'default' => [
						'url' => '#',
					],
					'dynamic' => [
						'active' => true,
					],
				]
			);
		}

		$this->end_controls_section();
	}

	private function register_section_container() {
		$this->start_controls_section(
			'section_container',
			[
				'label' => esc_html__( 'Container', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
				'prefix_class' => 'elementor%s-align-',
				'default' => '',
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
						'title' => esc_html__( 'Justified', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label' => esc_html__( 'Width', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'max' => 1000,
					],
					'em' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} a.raven-button' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-button' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label' => esc_html__( 'Height', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} a.raven-button' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-button' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} a.raven-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .raven-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_content' );

		$this->control_container_style_tabs_normal();

		$this->control_container_style_tabs_hover();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function control_container_style_tabs_normal() {
		$this->start_controls_tab(
			'tab_content_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} a.raven-button .button-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-button .button-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-button-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-button-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-button-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'subtext_color',
			[
				'label' => esc_html__( 'Subtext Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} a.raven-button .button-subtext' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-button .button-subtext' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'background',
				'exclude' => [ 'image' ],
				'fields_options' => Module::get_default_value(),
				'selector' => '{{WRAPPER}} a.raven-button, {{WRAPPER}} .raven-button, {{WRAPPER}} .raven-button-widget-normal-effect-blink:after',
			]
		);

		$this->add_control(
			'border_heading',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} a.raven-button' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .raven-button' => 'border-color: {{VALUE}};',
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
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} a.raven-button:not(:hover), {{WRAPPER}} .raven-button:not(:hover)',
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'separator' => 'after',
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} a.raven-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .raven-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .raven-button:not(:hover).raven-button-widget-normal-effect-shockwave:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .raven-button:not(:hover).raven-button-widget-normal-effect-shockwave:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'box_shadow',
				'selector' => '{{WRAPPER}} a.raven-button, {{WRAPPER}} .raven-button',
			]
		);

		$this->add_control(
			'normal_effect',
			[
				'label' => esc_html__( 'Normal Effects', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'none',
				'options' => [
					'none' => esc_html__( 'None', 'jupiterx-core' ),
					'shine' => esc_html__( 'Shine', 'jupiterx-core' ),
					'shockwave' => esc_html__( 'Shockwave', 'jupiterx-core' ),
					'jump' => esc_html__( 'Jump', 'jupiterx-core' ),
					'blink' => esc_html__( 'Blink', 'jupiterx-core' ),
				],
			]
		);

		$this->add_control(
			'shine_color',
			[
				'label' => esc_html__( 'Shine Effect Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#ffffff',
				'condition' => [
					'normal_effect' => 'shine',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-button:not(:hover).raven-button-widget-normal-effect-shine::before' => 'background: linear-gradient(to right, {{VALUE}}00 0%, {{VALUE}}30 50%, {{VALUE}}00 100%);',
				],
				'alpha' => false,
			]
		);

		$this->end_controls_tab();
	}

	private function control_container_style_tabs_hover() {
		$this->start_controls_tab(
			'tab_content_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'hover_text_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} a.raven-button:hover .button-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-button:hover .button-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} a.raven-button:hover .raven-button-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-button:hover .raven-button-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} a.raven-button:hover .raven-button-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-button:hover .raven-button-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} a.raven-button:hover .raven-button-icon svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .raven-button:hover .raven-button-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_subtext_color',
			[
				'label' => esc_html__( 'Subtext Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} a.raven-button:hover .button-subtext' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-button:hover .button-subtext' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'hover_background',
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .raven-button .raven-button-overlay:before',
			]
		);

		$this->add_control(
			'hover_border_heading',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'hover_border_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'hover_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} a.raven-button:hover' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .raven-button:hover' => 'border-color: {{VALUE}};',
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
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} a.raven-button:hover, {{WRAPPER}} .raven-button:hover',
			]
		);

		$this->add_control(
			'hover_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'separator' => 'after',
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} a.raven-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .raven-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'hover_box_shadow',
				'selector' => '{{WRAPPER}} a.raven-button:hover, {{WRAPPER}} .raven-button:hover',
			]
		);

		$this->add_control(
			'hover_effect',
			[
				'label' => esc_html__( 'Hover Effects', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'none',
				'options' => [
					'none' => esc_html__( 'None', 'jupiterx-core' ),
					'grow' => esc_html__( 'Grow', 'jupiterx-core' ),
					'shrink' => esc_html__( 'Shrink', 'jupiterx-core' ),
					'pulse' => esc_html__( 'Pulse', 'jupiterx-core' ),
					'pop' => esc_html__( 'Pop', 'jupiterx-core' ),
					'grow-rotate' => esc_html__( 'Grow Rotate', 'jupiterx-core' ),
					'wobble-skew' => esc_html__( 'Wobble Skew', 'jupiterx-core' ),
					'buzz-out' => esc_html__( 'Buzz Out', 'jupiterx-core' ),
				],
			]
		);

		$this->end_controls_tab();
	}

	private function register_section_content_style() {
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => esc_html__( 'Content', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'text_typography',
				'label' => esc_html__( 'Text Typography', 'jupiterx-core' ),
				'scheme' => '4',
				'selector' => '{{WRAPPER}} a.raven-button .button-text, {{WRAPPER}} .raven-button .button-text',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'subtext_typography',
				'label' => esc_html__( 'Subtext Typography', 'jupiterx-core' ),
				'scheme' => '4',
				'selector' => '{{WRAPPER}} a.raven-button .button-subtext, {{WRAPPER}} .raven-button .button-subtext',
			]
		);

		$this->add_responsive_control(
			'text_and_subtext_spacing',
			[
				'label' => esc_html__( 'Text and Subtext spacing', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', 'em', 'rem' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-button .button-subtext' => 'padding-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_section_icon() {
		$this->start_controls_section(
			'section_icon',
			[
				'label' => esc_html__( 'Icon', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'icon_new[value]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'icon_align',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
				'default' => 'left',
				'options' => [
					'far-left' => [
						'title' => esc_html__( 'Far Left', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-left',
					],
					'left' => [
						'title' => esc_html__( 'Left', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-right',
					],
					'far-right' => [
						'title' => esc_html__( 'Far Right', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'condition' => [
					'icon_new[value]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__( 'Size', 'jupiterx-core' ),
				'type' => 'slider',
				'default' => [
					'unit' => 'px',
					'size' => 16,
				],
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 200,
					],
				],
				'condition' => [
					'icon_new[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-button .raven-button-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-button .raven-button-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-button' => '--raven-button-widget-icon-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'icon_tabs' );

		$this->start_controls_tab(
			'icon_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'btn_icon_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-button .raven-button-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-button .raven-button-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_tab_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'btn_icon_color_hover',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-button:hover .raven-button-content .raven-button-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-button:hover .raven-button-content .raven-button-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		/**
		 * @todo Deprecate this option.
		 * In this case, deprecation means making this option hidden for users.
		 */
		$this->add_responsive_control(
			'icon_space_between',
			[
				'label' => esc_html__( 'Space Between', 'jupiterx-core' ),
				'description' => esc_html__( 'Space between option is depreciated and will be removed. Use the spacing option for more flexibility.', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'condition' => [
					'icon_new[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-button .raven-button-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-button .raven-button-align-icon-far-left' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-button .raven-button-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-button .raven-button-align-icon-far-right' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-button-icon-position-left' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .raven-button-icon-position-right' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .raven-button-icon-position-far-left' => 'top: clamp(-5px, {{TOP}}{{UNIT}}, calc(100% - var(--raven-button-widget-icon-size))); bottom: clamp(-2px, {{BOTTOM}}{{UNIT}}, calc(100% - var(--raven-button-widget-icon-size))); left: clamp(-2px, {{LEFT}}{{UNIT}}, calc(100% - var(--raven-button-widget-icon-size)));',
					'{{WRAPPER}} .raven-button-icon-position-far-right' => 'top: clamp(-5px, {{TOP}}{{UNIT}}, calc(100% - var(--raven-button-widget-icon-size))); bottom: clamp(-2px, {{BOTTOM}}{{UNIT}}, calc(100% - var(--raven-button-widget-icon-size))); right: clamp(-2px, {{RIGHT}}{{UNIT}}, calc(100% - var(--raven-button-widget-icon-size)));',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_popup_controls() {
		$this->start_injection( [
			'at' => 'before',
			'of' => 'link',
		] );

		$this->add_control(
			'turn_to_popup_action_button',
			[
				'label' => esc_html__( 'Turn to Popup Action Button', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_on' => esc_html__( 'Yes', 'jupiterx-core' ),
				'label_off' => esc_html__( 'No', 'jupiterx-core' ),
				'return_value' => 'yes',
				'default' => 'no',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'popup_action_type',
			[
				'label' => esc_html__( 'Popup Action Type', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'close-popup',
				'options' => [
					'close-popup' => esc_html__( 'Close Popup', 'jupiterx-core' ),
					'close-all-popups' => esc_html__( 'Close All Popups', 'jupiterx-core' ),
					'close-popup-permanently' => esc_html__( 'Close Popup Permanently', 'jupiterx-core' ),
					'close-all-popups-permanently' => esc_html__( 'Close All Popups Permanently', 'jupiterx-core' ),
				],
				'condition' => [
					'turn_to_popup_action_button' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->end_injection();
	}

	protected function render() {
		$settings          = $this->get_settings_for_display();
		$migration_allowed = Elementor::$instance->icons_manager->is_migration_allowed();
		$is_new            = empty( $settings['icon'] ) && $migration_allowed;

		$this->render_button_attributes( $settings, $is_new );
		?>
		<div class="raven-widget-wrapper">
			<?php
			$is_popup_action_activated = 'yes' === $settings['turn_to_popup_action_button'];
			$tag                       = $is_popup_action_activated ? 'div' : 'a';

			printf( '<%s %s>',
				ElementorUtils::validate_html_tag( $tag ),
				$this->get_render_attribute_string( 'button' )
			);
			?>
				<div class="raven-button-overlay" <?php echo $this->get_render_attribute_string( 'button-overlay' ); ?>></div>
				<span class="raven-button-content">
					<?php
					if ( 'far-left' === $settings['icon_align'] ) {
						$this->render_button_icon( $settings, $is_new );
					}
					?>

					<div class="button-text-container">
						<?php
						if ( 'left' === $settings['icon_align'] ) {
							$this->render_button_icon( $settings, $is_new );
						}
						?>
						<div class="raven-button-texts-wrapper">
							<div class="button-text">
								<span <?php echo $this->get_render_attribute_string( 'text' ); ?>>
									<?php echo wp_kses_post( $settings['text'] ); ?>
								</span>
							</div>
							<?php if ( ! empty( $settings['subtext'] ) ) : ?>
								<div class="button-subtext">
									<span <?php echo $this->get_render_attribute_string( 'subtext' ); ?>>
									<?php echo wp_kses_post( $settings['subtext'] ); ?>
								</span>
								</div>
							<?php endif; ?>
						</div>
						<?php
						if ( 'right' === $settings['icon_align'] ) {
							$this->render_button_icon( $settings, $is_new );
						}
						?>
					</div>
					<?php
					if ( 'far-right' === $settings['icon_align'] ) {
						$this->render_button_icon( $settings, $is_new );
					}
					?>

					<?php if ( isset( $product_ajax ) && $product_ajax ) : ?>
						<i class="raven-spinner"></i>
					<?php endif; ?>
				</span>
			<?php
			printf( '</%s>', ElementorUtils::validate_html_tag( $tag ) );
			?>
		</div>
		<?php
	}

	protected function render_button_icon( $settings, $is_new ) {
		if ( ! empty( $settings['icon'] ) || ! empty( $settings['icon_new']['value'] ) ) :
			$migrated = isset( $settings['__fa4_migrated']['icon_new'] );
			?>
			<span <?php echo $this->get_render_attribute_string( 'icon-align' ); ?>>
				<?php
				if ( $is_new || $migrated ) :
					Elementor::$instance->icons_manager->render_icon( $settings['icon_new'], [ 'aria-hidden' => 'true' ] );
				else :
					?>
					<i class="<?php echo esc_attr( $settings['icon'] ); ?>" aria-hidden="true"></i>
				<?php endif; ?>
			</span>
		<?php endif;
	}

	/**
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	protected function render_button_attributes( $settings, $is_new ) {
		if ( ! $is_new && empty( $settings['icon_align'] ) ) {
			$settings['icon_align']         = $this->get_settings( 'icon_align' );
			$settings['text_align']         = $this->get_settings( 'text_align' );
			$settings['icon_space_between'] = $this->get_settings( 'icon_space_between' );
			$settings['icon_size']          = $this->get_settings( 'icon_size' );
		}

		if ( empty( $settings['icon_align'] ) ) {
			$settings['icon_align'] = 'left';
		}

		$this->add_render_attribute( 'button', 'class', 'raven-button' );
		$this->add_render_attribute( 'text', 'class', 'raven-button-text' );
		$this->add_render_attribute( 'icon-align', 'class', 'raven-button-icon' );

		$this->add_render_attribute(
			'button',
			[
				'class' => [
					'raven-button-widget-normal-effect-' . $settings['normal_effect'],
					! Utils::check_fresh_install() ? '' : 'elementor-button',
				],
			]
		);

		$this->add_render_attribute(
			'icon-align',
			'class',
			'raven-button-icon-position-' . $settings['icon_align']
		);

		$this->add_render_attribute(
			'button',
			'class',
			'raven-button-text-align-' . $settings['text_align']
		);

		$this->add_inline_editing_attributes( 'text', 'none' );
		$this->add_inline_editing_attributes( 'subtext', 'none' );

		$this->render_breakpoint_attributes();

		$product_id = isset( $settings['product_product_includes'] ) ? $settings['product_product_includes'] : 0;

		$this->render_woocommerce_related_attributes( $settings, $product_id );

		if ( $settings['hover_effect'] ) {
			$this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings['hover_effect'] );
		}

		if ( array_key_exists( 'raven_animated_gradient_enable', $settings ) && 'yes' === $settings['raven_animated_gradient_enable'] ) {
			$color_list = $settings['raven_animated_gradient_color_list'];
			$direction  = '';
			$speed      = '';

			if ( array_key_exists( 'raven_animated_gradient_direction', $settings ) ) {
				$direction = $settings['raven_animated_gradient_direction'];
			}

			if ( array_key_exists( 'raven_animated_gradient_speed', $settings ) ) {
				$speed = $settings['raven_animated_gradient_speed']['size'] . 's';
			}

			$animated_gradient_attributes = Utils::get_animated_gradient_attributes( $direction, $color_list );

			$data_background_size = $animated_gradient_attributes['data_background_size'];
			$data_animation_name  = $animated_gradient_attributes['data_animation_name'];
			$angle                = $animated_gradient_attributes['angle'];

			$this->add_render_attribute( 'button-overlay', 'data-background-size', $data_background_size );
			$this->add_render_attribute( 'button-overlay', 'data-animation-name', $data_animation_name );

			$color = [];
			$count = count( $color_list );

			for ( $i = 0; $i < $count; $i++ ) {
				$color[ $i ] = $color_list[ $i ]['raven_animated_gradient_color'];
			}

			array_push( $color, $color_list[0]['raven_animated_gradient_color'] );

			if ( ! empty( $color_list[1] ) ) {
				array_push( $color, $color_list[1]['raven_animated_gradient_color'] );
			}

			$color           = implode( ',', $color );
			$gradient_color  = 'linear-gradient( ' . $angle . ',' . $color . ' )';
			$animation_speed = 'animation-duration: ' . $speed;
			$animation_name  = 'animation-name: ' . $data_animation_name;
			$background_size = 'background-size: ' . $data_background_size;

			$this->add_render_attribute( '_wrapper', 'data-color', $color );
			$this->add_render_attribute( 'button-overlay', 'class', 'raven-animated-gradient' );
			$this->add_render_attribute( 'button-overlay', 'data-angle', $angle );
			$this->add_render_attribute( 'button-overlay', 'data-speed', $speed );
			$this->add_render_attribute( 'button-overlay', 'data-color', $color );
			$this->add_render_attribute( 'button-overlay', 'style', 'background-image : ' . $gradient_color . ';' . $animation_speed . ';' . $animation_name . ';' . $background_size . ';' );
		}
	}

	protected function render_woocommerce_related_attributes( $settings, $product_id ) {
		if ( class_exists( 'WooCommerce' ) && 'yes' === $settings['show_as_add_to_cart'] && ! empty( $product_id ) ) {
			$product      = wc_get_product( $product_id );
			$product_type = $product->get_type();
			$product_ajax = $product->supports( 'ajax_add_to_cart' );

			$class = implode( ' ', array_filter( [
				'product_type_' . $product_type,
				$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
				$product_ajax ? 'ajax_add_to_cart' : '',
			] ) );

			$this->add_render_attribute( 'button',
				[
					'rel' => 'nofollow',
					'href' => $product->add_to_cart_url(),
					'data-quantity' => ( isset( $settings['quantity'] ) ? $settings['quantity'] : 1 ),
					'data-product_id' => $product->get_id(),
					'class' => $class,
				]
			);
		} elseif ( ! empty( $settings['link']['url'] ) && 'yes' !== $settings['turn_to_popup_action_button'] ) {
			$this->add_render_attribute( 'button', 'href', $settings['link']['url'] );

			$this->add_render_attribute( 'button', 'class', 'raven-button-link' );

			$this->render_link_properties( $this, $settings['link'], 'button' );
		}

	}

	protected function render_breakpoint_attributes() {
		foreach ( Elementor::$instance->breakpoints->get_active_breakpoints() as $breakpoint ) {
			$breakpoint_name = $breakpoint->get_name();

			if ( empty( $settings[ "icon_align_{$breakpoint_name}" ] ) ) {
				continue;
			}

			$this->add_render_attribute(
				'icon-align',
				'class',
				"raven-button-{$breakpoint_name}-align-icon-" . $settings[ "icon_align_{$breakpoint_name}" ]
			);
		}
	}
}
