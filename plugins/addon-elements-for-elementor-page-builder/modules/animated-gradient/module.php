<?php

namespace WTS_EAE\Modules\AnimatedGradient;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

class Module {
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
	private static $_instance = null;

	public function __construct() {
		add_action( 'elementor/element/after_section_end', [ $this, 'register_controls' ], 10, 3 );
		add_action( 'elementor/element/print_template', [ $this, '_print_template' ], 10, 2 );
		add_action( 'elementor/section/print_template', [ $this, '_print_template' ], 10, 2 );
		add_action( 'elementor/column/print_template', [ $this, '_print_template' ], 10, 2 );
		add_action( 'elementor/container/print_template', [ $this, '_print_template' ], 10, 2 );
		add_action( 'elementor/frontend/before_render', [ $this, '_before_render' ] );
	}

	public function register_controls( $element, $section_id, $args ) {
		if ( ( 'section' === $element->get_name() && 'section_background' === $section_id ) || ( 'column' === $element->get_name() && 'section_style' === $section_id ) || ( 'container' === $element->get_name() && 'section_background' === $section_id ) ) {

			$element->start_controls_section(
				'eae_animated_gradient',
				[
					'tab'   => Controls_Manager::TAB_STYLE,
					'label' => __( 'EAE - Animated Gradient Background', 'wts-eae' ),
				]
			);
			$element->add_control(
				'eae_animated_gradient_enable',
				[
					'type'         => Controls_Manager::SWITCHER,
					'label'        => __( 'Enable', 'wts-eae' ),
					'default'      => '',
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'label_off'    => __( 'No', 'wts-eae' ),
					'return_value' => 'yes',
					'prefix_class' => 'eae-animated-gradient-',
					'render_type'  => 'template',
				]
			);
			$element->add_control(
				'gradient_background_angle',
				[
					'label'      => __( 'Angle', 'wts-eae' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'deg' ],
					'range'      => [
						'deg' => [
							'min'  => -45,
							'max'  => 180,
							'step' => 2,
						],
					],
					'default'    => [
						'unit' => 'deg',
						'size' => -45,
					],
					'selectors'  => [
						'{{WRAPPER}} .box' => 'width: {{SIZE}}{{UNIT}};',
					],
					'condition'  => [
						'eae_animated_gradient_enable' => 'yes',
					],
				]
			);
			$repeater = new Repeater();

			$repeater->add_control(
				'eae_animated_gradient_color',
				[
					'label' => __( 'Add Color', 'wts-eae' ),
					'type'  => Controls_Manager::COLOR,
				]
			);

			$element->add_control(
				'gradient_color_list',
				[
					'label'       => __( 'Color', 'wts-eae' ),
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'title_field' => 'Color {{{eae_animated_gradient_color}}}',
					'show_label'  => true,

					'default'     => [
						[
							'eae_animated_gradient_color' => '#00a6d8',
						],
						[
							'eae_animated_gradient_color' => '#b800c9',
						],
						[
							'eae_animated_gradient_color' => '#e57600',
						],
					],

					'condition'   => [
						'eae_animated_gradient_enable' => 'yes',
					],
				]
			);

			$element->end_controls_section();
		}
	}

	public function _before_render( $element ) {
		if ( $element->get_name() !== 'section' && $element->get_name() !== 'column' && $element->get_name() !== 'container' ) {
			return;
		}

		$settings = $element->get_settings();

		if ( $settings['eae_animated_gradient_enable'] === 'yes' ) {
			$angle = -45;
			if(isset($settings['gradient_background_angle']['size'])){
				$angle = $settings['gradient_background_angle']['size'];
			}
			$element->add_render_attribute( '_wrapper', 'data-angle', $angle . 'deg' );
			$gradient_color_list = $settings['gradient_color_list'];
			$color =[];
			foreach ( $gradient_color_list as $gradient_color ) {
				if(!empty($gradient_color['eae_animated_gradient_color'])){
					$color[] = $gradient_color['eae_animated_gradient_color'];
				}
				
			};
			$colors = implode( ',', $color );
			$element->add_render_attribute( '_wrapper', 'data-color', $colors );
		}
	}

	public function _print_template( $template, $widget ) {
		?>
		<?php
		if ( $widget->get_name() !== 'section' && $widget->get_name() !== 'column' && $widget->get_name() !== 'container' ) {
			return $template;
		}
		$old_template = $template;
		ob_start();
		?>
		<#
			function sanitizeColor(color) {
				return _.escape(color);
			}

			color_list = settings.gradient_color_list;
			angle = settings.gradient_background_angle.size;
			var colors = [];
			var i = 0;
			_.each(color_list, function(color_item) {
				if (color_item.eae_animated_gradient_color) {
					colors.push(sanitizeColor(color_item.eae_animated_gradient_color));
				}
			});
			view.addRenderAttribute('_wrapper', 'data-color', colors);
		#>
		<div class="animated-gradient" data-angle="{{{ angle }}}deg" data-color="{{{ colors.join(',') }}}"></div>
		<?php
		$slider_content = ob_get_contents();
		ob_end_clean();
		$template = $slider_content . $old_template;
		return $template;
	}



	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}

