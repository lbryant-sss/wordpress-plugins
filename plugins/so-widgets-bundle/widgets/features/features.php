<?php
/*
Widget Name: Features
Description: Showcase features with icons, titles, text, and links in a customizable grid layout.
Author: SiteOrigin
Author URI: https://siteorigin.com
Documentation: https://siteorigin.com/widgets-bundle/features-widget-documentation/
Keywords: grid, icon, layout, link, text, title
*/

class SiteOrigin_Widget_Features_Widget extends SiteOrigin_Widget {
	public function __construct() {
		parent::__construct(
			'sow-features',
			__( 'SiteOrigin Features', 'so-widgets-bundle' ),
			array(
				'description'  => __( 'Showcase features with icons, titles, text, and links in a customizable grid layout.', 'so-widgets-bundle' ),
				'help'         => 'https://siteorigin.com/widgets-bundle/features-widget-documentation/',
				'panels_title' => false,
			),
			array(),
			false,
			plugin_dir_path( __FILE__ )
		);
	}

	public function initialize() {
		$this->register_frontend_styles(
			array(
				array(
					'siteorigin-widgets',
					plugin_dir_url( __FILE__ ) . 'css/style.css',
					array(),
					SOW_BUNDLE_VERSION,
				),
			)
		);
	}

	public function get_widget_form() {
		$useable_units = array( 'px', '%' );
		return array(
			'features' => array(
				'type' => 'repeater',
				'label' => __( 'Features', 'so-widgets-bundle' ),
				'item_name' => __( 'Feature', 'so-widgets-bundle' ),
				'item_label' => array(
					'selectorArray' => array(
						array(
							'selector' => '[id*="features-title"]',
							'valueMethod' => 'val',
						),
						array(
							'selector' => '[id*="features-icon"]',
							'valueMethod' => 'val',
						),
					),
				),
				'fields' => array(
					// The container shape
					'container_color' => array(
						'type' => 'color',
						'label' => __( 'Icon container color', 'so-widgets-bundle' ),
						'default' => '#404040',
					),

					// Left and right array keys are swapped due to a mistake that couldn't be corrected without disturbing existing users.
					'container_position' => array(
						'type' => 'select',
						'label' => __( 'Icon container position', 'so-widgets-bundle' ),
						'options' => array(
							'top'    => __( 'Top', 'so-widgets-bundle' ),
							'left'  => __( 'Right', 'so-widgets-bundle' ),
							'bottom' => __( 'Bottom', 'so-widgets-bundle' ),
							'right'   => __( 'Left', 'so-widgets-bundle' ),
						),
						'default' => 'top',
					),

					// The icon.
					'icon' => array(
						'type' => 'icon',
						'label' => __( 'Icon', 'so-widgets-bundle' ),
					),

					'icon_title' => array(
						'type' => 'text',
						'label' => __( 'Icon title', 'so-widgets-bundle' ),
					),

					'icon_color' => array(
						'type' => 'color',
						'label' => __( 'Icon color', 'so-widgets-bundle' ),
						'default' => '#fff',
					),

					'icon_image' => array(
						'type' => 'media',
						'library' => 'image',
						'label' => __( 'Icon image', 'so-widgets-bundle' ),
						'description' => __( 'Use your own icon image.', 'so-widgets-bundle' ),
						'fallback' => true,
					),

					'icon_image_size' => array(
						'type' => 'image-size',
						'label' => __( 'Icon image size', 'so-widgets-bundle' ),
					),

					// The text under the icon.
					'title' => array(
						'type' => 'text',
						'label' => __( 'Title text', 'so-widgets-bundle' ),
					),

					'text' => array(
						'type' => 'tinymce',
						'label' => __( 'Text', 'so-widgets-bundle' ),
					),

					'more_text' => array(
						'type' => 'text',
						'label' => __( 'More link text', 'so-widgets-bundle' ),
					),

					'more_url' => array(
						'type' => 'link',
						'label' => __( 'More link URL', 'so-widgets-bundle' ),
					),
				),
			),

			'fonts' => array(
				'type' => 'section',
				'label' => __( 'Font Design', 'so-widgets-bundle' ),
				'hide' => true,
				'fields' => array(
					'title_options' => array(
						'type' => 'section',
						'label' => __( 'Title', 'so-widgets-bundle' ),
						'hide' => true,
						'fields' => array(
							'tag' => array(
								'type' => 'select',
								'label' => __( 'HTML tag', 'so-widgets-bundle' ),
								'default' => 'h5',
								'options' => array(
									'h1' => __( 'H1', 'so-widgets-bundle' ),
									'h2' => __( 'H2', 'so-widgets-bundle' ),
									'h3' => __( 'H3', 'so-widgets-bundle' ),
									'h4' => __( 'H4', 'so-widgets-bundle' ),
									'h5' => __( 'H5', 'so-widgets-bundle' ),
									'h6' => __( 'H6', 'so-widgets-bundle' ),
								),
							),
							'font' => array(
								'type' => 'font',
								'label' => __( 'Font', 'so-widgets-bundle' ),
								'default' => 'default',
							),
							'size' => array(
								'type' => 'measurement',
								'label' => __( 'Size', 'so-widgets-bundle' ),
							),
							'color' => array(
								'type' => 'color',
								'label' => __( 'Color', 'so-widgets-bundle' ),
							),
						),
					),

					'text_options' => array(
						'type' => 'section',
						'label' => __( 'Text', 'so-widgets-bundle' ),
						'hide' => true,
						'fields' => array(
							'font' => array(
								'type' => 'font',
								'label' => __( 'Font', 'so-widgets-bundle' ),
								'default' => 'default',
							),
							'size' => array(
								'type' => 'measurement',
								'label' => __( 'Size', 'so-widgets-bundle' ),
							),
							'color' => array(
								'type' => 'color',
								'label' => __( 'Color', 'so-widgets-bundle' ),
							),
						),
					),

					'more_text_options' => array(
						'type' => 'section',
						'label' => __( 'More Link', 'so-widgets-bundle' ),
						'hide' => true,
						'fields' => array(
							'font' => array(
								'type' => 'font',
								'label' => __( 'Font', 'so-widgets-bundle' ),
								'default' => 'default',
							),
							'size' => array(
								'type' => 'measurement',
								'label' => __( 'Size', 'so-widgets-bundle' ),
							),
							'color' => array(
								'type' => 'color',
								'label' => __( 'Color', 'so-widgets-bundle' ),
							),
						),
					),
				),
			),

			'container_shape' => array(
				'type' => 'select',
				'label' => __( 'Icon container shape', 'so-widgets-bundle' ),
				'default' => 'round',
				'options' => include __DIR__ . '/inc/containers.php',
			),

			'container_size' => array(
				'type' => 'measurement',
				'label' => __( 'Icon container size', 'so-widgets-bundle' ),
				'default' => '84px',
			),

			'icon_size' => array(
				'type' => 'measurement',
				'label' => __( 'Icon size', 'so-widgets-bundle' ),
				'default' => '24px',
			),

			'icon_size_custom' => array(
				'type' => 'checkbox',
				'label' => __( 'Use icon size for custom icon', 'so-widgets-bundle' ),
				'default' => false,
			),

			'per_row' => array(
				'type' => 'number',
				'label' => __( 'Features per row', 'so-widgets-bundle' ),
				'default' => 3,
			),

			'center_items' => array(
				'type' => 'checkbox',
				'label' => __( 'Center items', 'so-widgets-bundle' ),
				'description' => __( 'If fewer features are on a line than the maximum, center the items.', 'so-widgets-bundle' ),
			),

			'feature_spacing' => array(
				'type' => 'multi-measurement',
				'label' => __( 'Space between each feature', 'so-widgets-bundle' ),
				'default' => '25px 25px',
				'measurements' => array(
					'vertical' => array(
						'label' => __( 'Vertical', 'so-widgets-bundle' ),
						'units' => $useable_units,
						'classes' => array(
							'sow-input-vertical',
						),
					),
					'horizontal' => array(
						'label' => __( 'Horizontal', 'so-widgets-bundle' ),
						'units' => $useable_units,
						'classes' => array(
							'sow-input-horizontal',
						),
					),
				),
			),

			'feature_spacing_mobile' => array(
				'type' => 'multi-measurement',
				'label' => __( 'Space between each feature on mobile', 'so-widgets-bundle' ),
				'measurements' => array(
					'vertical' => array(
						'label' => __( 'Vertical', 'so-widgets-bundle' ),
						'units' => $useable_units,
						'classes' => array(
							'sow-input-vertical',
						),
					),
					'horizontal' => array(
						'label' => __( 'Horizontal', 'so-widgets-bundle' ),
						'units' => $useable_units,
						'classes' => array(
							'sow-input-horizontal',
						),
					),
				),
				'state_handler' => array(
					'responsive[hide]' => array( 'hide' ),
					'responsive[show]' => array( 'show' ),
				),
			),

			'responsive' => array(
				'type' => 'checkbox',
				'label' => __( 'Responsive layout', 'so-widgets-bundle' ),
				'default' => true,
				'state_emitter' => array(
					'callback' => 'conditional',
					'args' => array(
						'responsive[show]: val',
						'responsive[hide]: ! val',
					),
				),
			),

			'more_text_bottom_align' => array(
				'type' => 'checkbox',
				'label' => __( 'Bottom align More link text', 'so-widgets-bundle' ),
			),

			'title_link' => array(
				'type' => 'checkbox',
				'label' => __( 'Link feature title to more URL', 'so-widgets-bundle' ),
				'default' => false,
				'state_handler' => array(
					'link_feature[hide]' => array( 'hide' ),
					'link_feature[show]' => array( 'show' ),
				),
			),

			'icon_link' => array(
				'type' => 'checkbox',
				'label' => __( 'Link icon to more URL', 'so-widgets-bundle' ),
				'default' => false,
				'state_handler' => array(
					'link_feature[hide]' => array( 'hide' ),
					'link_feature[show]' => array( 'show' ),
				),
			),

			'link_feature' => array(
				'type' => 'checkbox',
				'label' => __( 'Link feature column to more URL', 'so-widgets-bundle' ),
				'state_emitter' => array(
					'callback' => 'conditional',
					'args' => array(
						'link_feature[show]: ! val',
						'link_feature[hide]: val',
					),
				),
			),

			'new_window' => array(
				'type' => 'checkbox',
				'label' => __( 'Open more URL in a new window', 'so-widgets-bundle' ),
				'default' => false,
			),
		);
	}

	public function modify_instance( $instance ) {
		if ( empty( $instance ) ) {
			return array();
		}

		// Migrate legacy Title Tag setting to new Title Options Tag setting.
		if ( ! empty( $instance['title_tag'] ) ) {
			$instance['fonts']['title_options']['tag'] = $instance['title_tag'];
			unset( $instance['title_tag'] );
		}

		if ( isset( $instance['feature_space'] ) ) {
			$instance['feature_spacing'] = '25px ' . $instance['feature_space'];
		} elseif ( ! isset( $instance['feature_spacing'] ) ) {
			$instance['feature_spacing'] = '25px 25px';
		}


		return $instance;
	}

	public function get_template_variables( $instance, $args ) {
		$feature_width = $this->calculate_feature_width( $instance );

		$tag = siteorigin_widget_valid_tag(
			$instance['fonts']['title_options']['tag'],
			'h5'
		);

		if ( ! empty( $instance ) && ! empty( $instance['link_feature'] ) ) {
			wp_enqueue_style( 'siteorigin-accessibility' );
		}

		return array(
			'feature_width' => $feature_width,
			'tag' => $tag,
		);
	}

	public function get_less_variables( $instance ) {
		$less_vars = array();

		if ( empty( $instance ) ) {
			return $less_vars;
		}

		$fonts = $instance['fonts'];
		$styleable_text_fields = array( 'title', 'text', 'more_text' );

		foreach ( $styleable_text_fields as $field_name ) {
			if ( ! empty( $fonts[$field_name . '_options'] ) ) {
				$styles = $fonts[$field_name . '_options'];

				if ( ! empty( $styles['size'] ) ) {
					$less_vars[$field_name . '_size'] = $styles['size'];
				}

				if ( ! empty( $styles['color'] ) ) {
					$less_vars[$field_name . '_color'] = $styles['color'];
				}

				if ( ! empty( $styles['font'] ) ) {
					$font = siteorigin_widget_get_font( $styles['font'] );
					$less_vars[$field_name . '_font'] = $font['family'];

					if ( ! empty( $font['weight'] ) ) {
						$less_vars[ $field_name . '_font_weight' ] = $font['weight_raw'];
						$less_vars[ $field_name . '_font_style' ] = $font['style'];
					}
				}
			}
		}

		$less_vars['container_size'] = $instance['container_size'];
		$less_vars['icon_size'] = $instance['icon_size'];
		$less_vars['title_tag'] = ! empty( $instance['fonts']['title_options']['tag'] ) ? $instance['fonts']['title_options']['tag'] : 'h5';
		$less_vars['per_row'] = $instance['per_row'];
		$less_vars['center_items'] = ! empty( $instance['center_items'] );
		$less_vars['feature_spacing'] = ! empty( $instance['feature_spacing'] ) ? $instance['feature_spacing'] : '25px';
		$less_vars['feature_spacing_mobile'] = ! empty( $instance['feature_spacing_mobile'] ) ? $instance['feature_spacing_mobile'] : '25px';
		$less_vars['use_icon_size'] = empty( $instance['icon_size_custom'] ) ? 'false' : 'true';
		$less_vars['link_feature'] = ! empty( $instance['link_feature'] );
		$less_vars['more_text_bottom_align'] = ! empty( $instance['more_text_bottom_align'] ) ? 'true' : 'false';

		$global_settings = $this->get_global_settings();

		if ( ! empty( $global_settings['responsive_breakpoint'] ) ) {
			$less_vars['responsive_breakpoint'] = $global_settings['responsive_breakpoint'];
		}

		return $less_vars;
	}

	public function get_feature_flex_direction( $position, $more_text_bottom_align = false ) {
		switch ( $position ) {
			case 'top':
				$style = 'column';
				break;

			case 'right':
				$style = 'row';
				break;

			case 'bottom':
				$style = $more_text_bottom_align ? 'column' : 'column-reverse';
				break;

			case 'left':
			default:
				$style = 'row-reverse';
				break;
		}

		return $style;
	}

	public function calculate_feature_width( $instance ) {
		$per_row = ! empty( $instance['per_row'] ) ? $instance['per_row'] : 3;

		if ( ! empty( $instance['feature_spacing'] ) ) {
			// It's possible $feature_gap is either a single value, or two.
			// If two, return the second value. Otherwise, return the first.
			$feature_gap = explode( ' ', $instance['feature_spacing'] );
			$feature_gap = count( $feature_gap ) > 1 ? $feature_gap[1] : $feature_gap[0];
		}

		if ( empty( $feature_gap ) ) {
			$feature_gap = '25px';
		}

		return 'calc(' . round( 100 / $per_row, 3 ) . '% - ' . $feature_gap . ')';
	}

	public function get_settings_form() {
		return array(
			'responsive_breakpoint' => array(
				'type'        => 'measurement',
				'label'       => __( 'Responsive Breakpoint', 'so-widgets-bundle' ),
				'default'     => '780px',
				'description' => __( 'This setting controls when the features widget will collapse for mobile devices. The default value is 780px', 'so-widgets-bundle' ),
			),
		);
	}

	public function get_form_teaser() {
		if ( class_exists( 'SiteOrigin_Premium' ) ) {
			return false;
		}

		return array(
			sprintf(
				__( 'Add an feature icon title tooltip with %sSiteOrigin Premium%s', 'so-widgets-bundle' ),
				'<a href="https://siteorigin.com/downloads/premium/?featured_addon=plugin/tooltip" target="_blank">',
				'</a>'
			),
			sprintf(
				__( 'Use Google Fonts right inside the Features Widget with %sSiteOrigin Premium%s', 'so-widgets-bundle' ),
				'<a href="https://siteorigin.com/downloads/premium/?featured_addon=plugin/web-font-selector" target="_blank" rel="noopener noreferrer">',
				'</a>'
			),
		);
	}
}

siteorigin_widget_register( 'sow-features', __FILE__, 'SiteOrigin_Widget_Features_Widget' );
