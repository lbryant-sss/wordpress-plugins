<?php
/*
Widget Name: Headline
Description: Engage visitors with a prominent, stylish headline and optional divider and sub-headline to convey key messages.
Author: SiteOrigin
Author URI: https://siteorigin.com
Documentation: https://siteorigin.com/widgets-bundle/headline-widget/
Keywords: divider, heading, message, subheading, text, title
*/

class SiteOrigin_Widget_Headline_Widget extends SiteOrigin_Widget {
	public function __construct() {
		parent::__construct(
			'sow-headline',
			__( 'SiteOrigin Headline', 'so-widgets-bundle' ),
			array(
				'description' => __( 'Engage visitors with a prominent, stylish headline and optional divider and sub-headline to convey key messages.', 'so-widgets-bundle' ),
				'help' => 'https://siteorigin.com/widgets-bundle/headline-widget/',
			),
			array(),
			false,
			plugin_dir_path( __FILE__ )
		);
	}

	public function initialize() {
		add_action( 'siteorigin_widgets_enqueue_frontend_scripts_' . $this->id_base, array( $this, 'enqueue_widget_scripts' ) );
		add_filter( 'siteorigin_widgets_wrapper_classes_' . $this->id_base, array( $this, 'wrapper_class_filter' ), 10, 2 );
		add_filter( 'siteorigin_widgets_wrapper_data_' . $this->id_base, array( $this, 'wrapper_data_filter' ), 10, 2 );
	}

	public function get_settings_form() {
		return array(
			'responsive_breakpoint' => array(
				'type' => 'measurement',
				'label' => __( 'Responsive Breakpoint', 'so-widgets-bundle' ),
				'default' => '780px',
				'description' => __( 'The pixel resolution when the mobile alignment settings will be applied.', 'so-widgets-bundle' ),
			),
		);
	}

	public function get_widget_form() {
		return array(
			'headline' => array(
				'type' => 'section',
				'label'  => __( 'Headline', 'so-widgets-bundle' ),
				'hide'   => false,
				'fields' => array(
					'text' => array(
						'type' => 'text',
						'label' => __( 'Text', 'so-widgets-bundle' ),
					),
					'destination_url' => array(
						'type' => 'link',
						'label' => __( 'Destination URL', 'so-widgets-bundle' ),
					),
					'new_window' => array(
						'type' => 'checkbox',
						'default' => false,
						'label' => __( 'Open in a new window', 'so-widgets-bundle' ),
					),
					'tag' => array(
						'type' => 'select',
						'label' => __( 'HTML Tag', 'so-widgets-bundle' ),
						'default' => 'h2',
						'options' => array(
							'h1' => __( 'H1', 'so-widgets-bundle' ),
							'h2' => __( 'H2', 'so-widgets-bundle' ),
							'h3' => __( 'H3', 'so-widgets-bundle' ),
							'h4' => __( 'H4', 'so-widgets-bundle' ),
							'h5' => __( 'H5', 'so-widgets-bundle' ),
							'h6' => __( 'H6', 'so-widgets-bundle' ),
							'p' => __( 'Paragraph', 'so-widgets-bundle' ),
						),
					),
					'color' => array(
						'type' => 'color',
						'label' => __( 'Color', 'so-widgets-bundle' ),
					),
					'hover_color' => array(
						'type' => 'color',
						'label' => __( 'Hover Color', 'so-widgets-bundle' ),
					),
					'font' => array(
						'type' => 'font',
						'label' => __( 'Font', 'so-widgets-bundle' ),
						'default' => 'default',
					),
					'font_size' => array(
						'type' => 'measurement',
						'label' => __( 'Font Size', 'so-widgets-bundle' ),
					),
					'align' => array(
						'type' => 'select',
						'label' => __( 'Alignment', 'so-widgets-bundle' ),
						'default' => 'center',
						'options' => array(
							'center' => __( 'Center', 'so-widgets-bundle' ),
							'left' => __( 'Left', 'so-widgets-bundle' ),
							'right' => __( 'Right', 'so-widgets-bundle' ),
							'justify' => __( 'Justify', 'so-widgets-bundle' ),
						),
					),
					'mobile_align' => array(
						'type' => 'select',
						'label' => __( 'Mobile alignment', 'so-widgets-bundle' ),
						'options' => array(
							'center' => __( 'Center', 'so-widgets-bundle' ),
							'left' => __( 'Left', 'so-widgets-bundle' ),
							'right' => __( 'Right', 'so-widgets-bundle' ),
							'justify' => __( 'Justify', 'so-widgets-bundle' ),
						),
					),
					'line_height' => array(
						'type' => 'measurement',
						'label' => __( 'Line Height', 'so-widgets-bundle' ),
					),
					'margin' => array(
						'type' => 'measurement',
						'label' => __( 'Top and Bottom Margin', 'so-widgets-bundle' ),
						'default' => '',
					),
				),
			),
			'sub_headline' => array(
				'type' => 'section',
				'label'  => __( 'Sub headline', 'so-widgets-bundle' ),
				'hide'   => true,
				'fields' => array(
					'text' => array(
						'type' => 'text',
						'label' => __( 'Text', 'so-widgets-bundle' ),
					),
					'destination_url' => array(
						'type' => 'link',
						'label' => __( 'Destination URL', 'so-widgets-bundle' ),
					),
					'new_window' => array(
						'type' => 'checkbox',
						'default' => false,
						'label' => __( 'Open in a new window', 'so-widgets-bundle' ),
					),
					'tag' => array(
						'type' => 'select',
						'label' => __( 'HTML Tag', 'so-widgets-bundle' ),
						'default' => 'h3',
						'options' => array(
							'h1' => __( 'H1', 'so-widgets-bundle' ),
							'h2' => __( 'H2', 'so-widgets-bundle' ),
							'h3' => __( 'H3', 'so-widgets-bundle' ),
							'h4' => __( 'H4', 'so-widgets-bundle' ),
							'h5' => __( 'H5', 'so-widgets-bundle' ),
							'h6' => __( 'H6', 'so-widgets-bundle' ),
							'p' => __( 'Paragraph', 'so-widgets-bundle' ),
						),
					),
					'color' => array(
						'type' => 'color',
						'label' => __( 'Color', 'so-widgets-bundle' ),
					),
					'hover_color' => array(
						'type' => 'color',
						'label' => __( 'Hover Color', 'so-widgets-bundle' ),
					),
					'font' => array(
						'type' => 'font',
						'label' => __( 'Font', 'so-widgets-bundle' ),
						'default' => 'default',
					),
					'font_size' => array(
						'type' => 'measurement',
						'label' => __( 'Font Size', 'so-widgets-bundle' ),
					),
					'align' => array(
						'type' => 'select',
						'label' => __( 'Alignment', 'so-widgets-bundle' ),
						'default' => 'center',
						'options' => array(
							'center' => __( 'Center', 'so-widgets-bundle' ),
							'left' => __( 'Left', 'so-widgets-bundle' ),
							'right' => __( 'Right', 'so-widgets-bundle' ),
							'justify' => __( 'Justify', 'so-widgets-bundle' ),
						),
					),
					'mobile_align' => array(
						'type' => 'select',
						'label' => __( 'Mobile alignment', 'so-widgets-bundle' ),
						'options' => array(
							'center' => __( 'Center', 'so-widgets-bundle' ),
							'left' => __( 'Left', 'so-widgets-bundle' ),
							'right' => __( 'Right', 'so-widgets-bundle' ),
							'justify' => __( 'Justify', 'so-widgets-bundle' ),
						),
					),
					'line_height' => array(
						'type' => 'measurement',
						'label' => __( 'Line Height', 'so-widgets-bundle' ),
					),
					'margin' => array(
						'type' => 'measurement',
						'label' => __( 'Top and Bottom Margin', 'so-widgets-bundle' ),
						'default' => '',
					),
				),
			),
			'divider' => array(
				'type' => 'section',
				'label' => __( 'Divider', 'so-widgets-bundle' ),
				'hide' => true,
				'fields' => array(
					'style' => array(
						'type' => 'select',
						'label' => __( 'Style', 'so-widgets-bundle' ),
						'default' => 'solid',
						'options' => array(
							'none' => __( 'None', 'so-widgets-bundle' ),
							'solid' => __( 'Solid', 'so-widgets-bundle' ),
							'dotted' => __( 'Dotted', 'so-widgets-bundle' ),
							'dashed' => __( 'Dashed', 'so-widgets-bundle' ),
							'double' => __( 'Double', 'so-widgets-bundle' ),
							'groove' => __( 'Groove', 'so-widgets-bundle' ),
							'ridge' => __( 'Ridge', 'so-widgets-bundle' ),
							'inset' => __( 'Inset', 'so-widgets-bundle' ),
							'outset' => __( 'Outset', 'so-widgets-bundle' ),
						),
					),
					'color' => array(
						'type' => 'color',
						'label' => __( 'Color', 'so-widgets-bundle' ),
						'default' => '#eee',
					),
					'thickness' => array(
						'type' => 'slider',
						'label' => __( 'Thickness', 'so-widgets-bundle' ),
						'min' => 0,
						'max' => 20,
						'default' => 1,
					),
					'align' => array(
						'type' => 'select',
						'label' => __( 'Alignment', 'so-widgets-bundle' ),
						'default' => 'center',
						'options' => array(
							'center' => __( 'Center', 'so-widgets-bundle' ),
							'left' => __( 'Left', 'so-widgets-bundle' ),
							'right' => __( 'Right', 'so-widgets-bundle' ),
						),
					),
					'mobile_align' => array(
						'type' => 'select',
						'label' => __( 'Mobile alignment', 'so-widgets-bundle' ),
						'options' => array(
							'center' => __( 'Center', 'so-widgets-bundle' ),
							'left' => __( 'Left', 'so-widgets-bundle' ),
							'right' => __( 'Right', 'so-widgets-bundle' ),
						),
					),
					'width' => array(
						'type' => 'measurement',
						'label' => __( 'Divider Width', 'so-widgets-bundle' ),
						'default' => '80%',
					),
					'margin' => array(
						'type' => 'measurement',
						'label' => __( 'Top and Bottom Margin', 'so-widgets-bundle' ),
						'default' => '',
					),
				),
			),

			'order' => array(
				'type' => 'order',
				'label' => __( 'Element Order', 'so-widgets-bundle' ),
				'options' => array(
					'headline' => __( 'Headline', 'so-widgets-bundle' ),
					'divider' => __( 'Divider', 'so-widgets-bundle' ),
					'sub_headline' => __( 'Sub Headline', 'so-widgets-bundle' ),
				),
				'default' => array( 'headline', 'divider', 'sub_headline' ),
			),

			'fittext' => array(
				'type' => 'checkbox',
				'label' => __( 'Use FitText', 'so-widgets-bundle' ),
				'description' => __( 'Dynamically adjust your heading font size based on screen size.', 'so-widgets-bundle' ),
				'default' => false,
				'state_emitter' => array(
					'callback' => 'conditional',
					'args'     => array(
						'use_fittext[show]: val',
						'use_fittext[hide]: ! val',
					),
				),
			),

			'fittext_compressor' => array(
				'type' => 'number',
				'label' => __( 'FitText Compressor Strength', 'so-widgets-bundle' ),
				'description' => __( 'The higher the value, the more your headings will be scaled down. Values above 1 are allowed.', 'so-widgets-bundle' ),
				'default' => 0.85,
				'step' => 0.01,
				'state_handler' => array(
					'use_fittext[show]' => array( 'show' ),
					'use_fittext[hide]' => array( 'hide' ),
				),
			),
		);
	}

	public function get_less_variables( $instance ) {
		$less_vars = array(
			'responsive_breakpoint' => $this->get_global_settings( 'responsive_breakpoint' ),
		);

		// All the headline attributes.
		$less_vars['headline_tag'] = isset( $instance['headline']['tag'] ) ? $instance['headline']['tag'] : false;
		$less_vars['headline_hover_color'] = isset( $instance['headline']['hover_color'] ) ? $instance['headline']['hover_color'] : false;
		$less_vars['headline_align'] = isset( $instance['headline']['align'] ) ? $instance['headline']['align'] : false;
		$less_vars['headline_mobile_align'] = isset( $instance['headline']['mobile_align'] ) ? $instance['headline']['mobile_align'] : false;
		$less_vars['headline_color'] = isset( $instance['headline']['color'] ) ? $instance['headline']['color'] : false;
		$less_vars['headline_font_size'] = isset( $instance['headline']['font_size'] ) ? $instance['headline']['font_size'] : false;
		$less_vars['headline_line_height'] = isset( $instance['headline']['line_height'] ) ? $instance['headline']['line_height'] : false;
		$less_vars['headline_margin'] = isset( $instance['headline']['margin'] ) ? $instance['headline']['margin'] : false;

		// Headline font family and weight.
		if ( ! empty( $instance['headline']['font'] ) ) {
			$font = siteorigin_widget_get_font( $instance['headline']['font'] );
			$less_vars['headline_font'] = $font['family'];

			if ( ! empty( $font['weight'] ) ) {
				$less_vars['headline_font_style'] = $font['style'];
				$less_vars['headline_font_weight'] = $font['weight_raw'];
			}
		}

		// Set the sub headline attributes.
		$less_vars['sub_headline_align'] = isset( $instance['sub_headline']['align'] ) ? $instance['sub_headline']['align'] : false;
		$less_vars['sub_headline_mobile_align'] = isset( $instance['sub_headline']['mobile_align'] ) ? $instance['sub_headline']['mobile_align'] : false;
		$less_vars['sub_headline_hover_color'] = isset( $instance['sub_headline']['hover_color'] ) ? $instance['sub_headline']['hover_color'] : false;
		$less_vars['sub_headline_tag'] = isset( $instance['sub_headline']['tag'] ) ? $instance['sub_headline']['tag'] : false;
		$less_vars['sub_headline_color'] = isset( $instance['sub_headline']['color'] ) ? $instance['sub_headline']['color'] : false;
		$less_vars['sub_headline_font_size'] = isset( $instance['sub_headline']['font_size'] ) ? $instance['sub_headline']['font_size'] : false;
		$less_vars['sub_headline_line_height'] = isset( $instance['sub_headline']['line_height'] ) ? $instance['sub_headline']['line_height'] : false;
		$less_vars['sub_headline_margin'] = isset( $instance['sub_headline']['margin'] ) ? $instance['sub_headline']['margin'] : false;

		// Sub headline font family and weight.
		if ( ! empty( $instance['sub_headline']['font'] ) ) {
			$font = siteorigin_widget_get_font( $instance['sub_headline']['font'] );
			$less_vars['sub_headline_font'] = $font['family'];

			if ( ! empty( $font['weight'] ) ) {
				$less_vars['sub_headline_font_style'] = $font['style'];
				$less_vars['sub_headline_font_weight'] = $font['weight_raw'];
			}
		}

		$less_vars['divider_style'] = isset( $instance['divider']['style'] ) ? $instance['divider']['style'] : false;
		$less_vars['divider_width'] = isset( $instance['divider']['width'] ) ? $instance['divider']['width'] : false;
		$less_vars['divider_thickness'] = isset( $instance['divider']['thickness'] ) ? (int) $instance['divider']['thickness'] . 'px' : false;
		$less_vars['divider_align'] = isset( $instance['divider']['align'] ) ? $instance['divider']['align'] : false;
		$less_vars['divider_mobile_align'] = isset( $instance['divider']['mobile_align'] ) ? $instance['divider']['mobile_align'] : false;
		$less_vars['divider_color'] = isset( $instance['divider']['color'] ) ? $instance['divider']['color'] : false;
		$less_vars['divider_margin'] = isset( $instance['divider']['margin'] ) ? $instance['divider']['margin'] : false;

		return $less_vars;
	}

	/**
	 * Get the template variables for the headline
	 *
	 * @return array
	 */
	public function get_template_variables( $instance, $args ) {
		if ( empty( $instance ) ) {
			return array();
		}

		return array(
			'headline' => $instance['headline']['text'],
			'headline_destination_url' => $instance['headline']['destination_url'],
			'headline_new_window' => $instance['headline']['new_window'],
			'headline_tag' => siteorigin_widget_valid_tag(
				$instance['headline']['tag'],
				'h2'
			),
			'sub_headline' => $instance['sub_headline']['text'],
			'sub_headline_destination_url' => $instance['sub_headline']['destination_url'],
			'sub_headline_new_window' => $instance['sub_headline']['new_window'],
			'sub_headline_tag' => siteorigin_widget_valid_tag(
				$instance['sub_headline']['tag'],
				'h3'
			),
			'order' => $instance['order'],
			'has_divider' => ! empty( $instance['divider'] ) && $instance['divider']['style'] != 'none',
		);
	}

	public function wrapper_class_filter( $classes, $instance ) {
		if ( ! empty( $instance[ 'fittext' ] ) ) {
			$classes[] = 'so-widget-fittext-wrapper';
		}

		return $classes;
	}

	public function wrapper_data_filter( $data, $instance ) {
		if ( ! empty( $instance['fittext'] ) ) {
			$data['fit-text-compressor'] = $instance['fittext_compressor'];
		}

		return $data;
	}

	public function enqueue_widget_scripts( $instance ) {
		if ( ! empty( $instance['fittext'] ) || $this->is_preview( $instance ) ) {
			wp_enqueue_script( 'sowb-fittext' );
		}
	}

	public function modify_instance( $instance ) {
		// Change the old divider weight into a divider thickness.
		if ( isset( $instance['divider']['weight'] ) && ! isset( $instance['divider']['thickness'] ) ) {
			switch( $instance['divider']['weight'] ) {
				case 'medium':
					$instance['divider']['thickness'] = 3;
					break;

				case 'thick':
					$instance['divider']['thickness'] = 5;
					break;

				case 'thin':
				default:
					$instance['divider']['thickness'] = 1;
					break;
			}
			unset( $instance['divider']['weight'] );
		}

		// Change the old divider side margin into overall width.
		if ( isset( $instance['divider']['side_margin'] ) && ! isset( $instance['divider']['width'] ) ) {
			global $content_width;
			$value = (float) $instance['divider']['side_margin'];

			switch( $instance['divider']['side_margin_unit'] ) {
				case 'px':
					$instance['divider']['width'] = ( ( ! empty( $content_width ) ? $content_width : 960 ) - ( 2 * $value ) ) . 'px';
					$instance['divider']['width_unit'] = 'px';
					break;

				case '%':
					$instance['divider']['width'] = ( 100 - ( 2 * $value ) ) . '%';
					$instance['divider']['width_unit'] = '%';
					break;

				default:
					$instance['divider']['width'] = '80%';
					$instance['divider']['width_unit'] = '%';
					break;
			}

			unset( $instance['divider']['side_margin'] );
			unset( $instance['divider']['side_margin_unit'] );
		}

		// Copy top margin over to bottom margin.
		if ( isset( $instance['divider']['top_margin'] ) && ! isset( $instance['divider']['bottom_margin'] ) ) {
			$instance['divider']['bottom_margin'] = $instance['divider']['top_margin'];
			$instance['divider']['bottom_margin_unit'] = $instance['divider']['top_margin_unit'];
		}

		// Set Mobile alignment settings to same value as the Alignment for existing widgets.
		if ( ! empty( $instance['headline']['align'] ) && empty( $instance['headline']['mobile_align'] ) ) {
			$instance['headline']['mobile_align'] = ! empty( $instance['headline']['align'] ) ? $instance['headline']['align'] : 'center';
			$instance['sub_headline']['mobile_align'] = ! empty( $instance['sub_headline']['align'] ) ? $instance['sub_headline']['align'] : 'center';
			$instance['divider']['mobile_align'] = ! empty( $instance['divider']['align'] ) ? $instance['divider']['align'] : 'center';
		}

		return $instance;
	}
}
siteorigin_widget_register( 'sow-headline', __FILE__, 'SiteOrigin_Widget_Headline_Widget' );
