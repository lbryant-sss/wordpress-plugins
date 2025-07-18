<?php
/*
Widget Name: Hero Image
Description: Build an impressive hero image section with custom content, buttons, background image, color, and video.
Author: SiteOrigin
Author URI: https://siteorigin.com
Documentation: https://siteorigin.com/widgets-bundle/hero-image-widget/
Keywords: background, button, content, image, video
*/

if ( ! class_exists( 'SiteOrigin_Widget_Base_Slider' ) ) {
	include_once plugin_dir_path( SOW_BUNDLE_BASE_FILE ) . '/base/inc/widgets/base-slider.class.php';
}

class SiteOrigin_Widget_Hero_Widget extends SiteOrigin_Widget_Base_Slider {
	protected $buttons = array();

	public function __construct() {
		parent::__construct(
			'sow-hero',
			__( 'SiteOrigin Hero', 'so-widgets-bundle' ),
			array(
				'description' => __( 'Build an impressive hero image section with custom content, buttons, background image, color, and video.', 'so-widgets-bundle' ),
				'help' => 'https://siteorigin.com/widgets-bundle/hero-image-widget/',
				'panels_title' => false,
			),
			array( ),
			false,
			plugin_dir_path( __FILE__ )
		);
	}

	public function initialize() {
		// This widget requires the button widget
		if ( ! class_exists( 'SiteOrigin_Widget_Button_Widget' ) ) {
			SiteOrigin_Widgets_Bundle::single()->include_widget( 'button' );
		}

		add_action( 'siteorigin_widgets_enqueue_frontend_scripts_' . $this->id_base, array( $this, 'enqueue_widget_scripts' ) );

		add_filter( 'siteorigin_widgets_wrapper_classes_' . $this->id_base, array( $this, 'wrapper_class_filter' ), 10, 2 );
		add_filter( 'siteorigin_widgets_wrapper_data_' . $this->id_base, array( $this, 'wrapper_data_filter' ), 10, 2 );

		// Let the slider base class do its initialization
		parent::initialize();
	}

	public function get_widget_form() {
		$units = siteorigin_widgets_get_measurements_list();
		unset( $units[1] ); // Remove %;

		return parent::widget_form( array(
			'frames' => array(
				'type' => 'repeater',
				'label' => __( 'Hero frames', 'so-widgets-bundle' ),
				'item_name' => __( 'Frame', 'so-widgets-bundle' ),
				'item_label' => array(
					'selectorArray' => array(
						array(
							'selector' => '.siteorigin-widget-field-background .media-field-wrapper .current .title',
							'valueMethod' => 'html',
						),
						array(
							'selector' => '.siteorigin-widget-field-videos .siteorigin-widget-field-repeater-items .media-field-wrapper .current .title',
							'valueMethod' => 'html',
						),
						array(
							'selector' => ".siteorigin-widget-field-videos [id*='url']",
							'update_event' => 'change',
							'value_method' => 'val',
						),
					),
				),
				'fields' => array(
					'content' => array(
						'type' => 'tinymce',
						'label' => __( 'Content', 'so-widgets-bundle' ),
					),

					'autop' => array(
						'type' => 'checkbox',
						'default' => false,
						'label' => __( 'Automatically add paragraphs', 'so-widgets-bundle' ),
					),

					'buttons' => array(
						'type' => 'repeater',
						'label' => __( 'Buttons', 'so-widgets-bundle' ),
						'item_name' => __( 'Button', 'so-widgets-bundle' ),
						'description' => __( 'Add [buttons] shortcode to the content to insert these buttons.', 'so-widgets-bundle' ),

						'item_label' => array(
							'selector' => "[id*='buttons-button-text']",
							'update_event' => 'change',
							'value_method' => 'val',
						),
						'fields' => array(
							'button' => array(
								'type' => 'widget',
								'class' => 'SiteOrigin_Widget_Button_Widget',
								'label' => __( 'Button', 'so-widgets-bundle' ),
								'form_filter' => array( $this, 'filter_button_widget_form' ),
								'collapsible' => false,
							),
						),
					),

					'background' => array(
						'type' => 'section',
						'label' => __( 'Background', 'so-widgets-bundle' ),
						'fields' => array(
							'image' => array(
								'type' => 'media',
								'label' => __( 'Background image', 'so-widgets-bundle' ),
								'library' => 'image',
								'fallback' => true,
								'state_emitter' => array(
									'callback' => 'conditional',
									'args'     => array(
										'has_background_image[show]: val',
										'has_background_image[hide]: ! val',
									),
								),
							),

							'alt' => array(
								'type' => 'text',
								'label' => __( 'Image Alt Text', 'so-widgets-bundle' ),
								'description' => __( 'Leave empty for decorative images.', 'so-widgets-bundle' ),
								'state_handler' => array(
									'has_background_image[show]' => array( 'show' ),
									'has_background_image[hide]' => array( 'hide' ),
								),
							),

							'size' => array(
								'type' => 'image-size',
								'label' => __( 'Image size', 'so-widgets-bundle' ),
								'state_handler' => array(
									'has_background_image[show]' => array( 'show' ),
									'has_background_image[hide]' => array( 'hide' ),
								),
							),

							'image_type' => array(
								'type' => 'select',
								'label' => __( 'Background image type', 'so-widgets-bundle' ),
								'options' => array(
									'cover' => __( 'Cover', 'so-widgets-bundle' ),
								),
								'default' => 'cover',
								'state_handler' => array(
									'has_background_image[show]' => array( 'show' ),
									'has_background_image[hide]' => array( 'hide' ),
								),
							),

							'opacity' => array(
								'label' => __( 'Background image opacity', 'so-widgets-bundle' ),
								'type' => 'slider',
								'min' => 0,
								'max' => 100,
								'default' => 100,
								'state_handler' => array(
									'has_background_image[show]' => array( 'show' ),
									'has_background_image[hide]' => array( 'hide' ),
								),
							),

							'color' => array(
								'type' => 'color',
								'label' => __( 'Background color', 'so-widgets-bundle' ),
								'default' => '#333333',
								'alpha' => true,
							),

							'url' => array(
								'type' => 'link',
								'label' => __( 'Destination URL', 'so-widgets-bundle' ),
							),

							'new_window' => array(
								'type' => 'checkbox',
								'label' => __( 'Open URL in a new window', 'so-widgets-bundle' ),
							),

							'videos' => array(
								'type' => 'repeater',
								'item_name' => __( 'Video', 'so-widgets-bundle' ),
								'label' => __( 'Background videos', 'so-widgets-bundle' ),
								'item_label' => array(
									'selectorArray' => array(
										array(
											'selector' => "[id*='url']",
											'update_event' => 'change',
											'value_method' => 'val',
										),
										array(
											'selector' => '.siteorigin-widget-field-file .media-field-wrapper .current .title',
											'valueMethod' => 'html',
										),
									),
								),
								'fields' => $this->video_form_fields(),
							),
						),
					),
				),
			),

			'controls' => array(
				'type' => 'section',
				'label' => __( 'Slider Controls', 'so-widgets-bundle' ),
				'fields' => $this->control_form_fields(),
			),

			'layout' => array(
				'type' => 'section',
				'label' => __( 'Layout', 'so-widgets-bundle' ),
				'fields' => array(
					'desktop' => array(
						'type' => 'section',
						'label' => __( 'Desktop', 'so-widgets-bundle' ),
						'fields' => array(
							'height' => array(
								'type' => 'measurement',
								'label' => __( 'Height', 'so-widgets-bundle' ),
								'units' => $units,
							),

							'padding' => array(
								'type' => 'measurement',
								'label' => __( 'Top and bottom padding', 'so-widgets-bundle' ),
								'default' => '50px',
							),

							'padding_extra_top' => array(
								'type' => 'measurement',
								'label' => __( 'Extra top padding', 'so-widgets-bundle' ),
								'description' => __( 'Additional padding added to the top of the slider', 'so-widgets-bundle' ),
								'default' => '0px',
							),

							'padding_sides' => array(
								'type' => 'measurement',
								'label' => __( 'Side padding', 'so-widgets-bundle' ),
								'default' => '20px',
							),

							'width' => array(
								'type' => 'measurement',
								'label' => __( 'Maximum container width', 'so-widgets-bundle' ),
								'default' => '1280px',
							),
						),
					),
					'mobile' => array(
						'type' => 'section',
						'label' => __( 'Mobile', 'so-widgets-bundle' ),
						'fields' => array(
							'height_responsive' => array(
								'type' => 'measurement',
								'label' => __( 'Height', 'so-widgets-bundle' ),
								'units' => $units,
							),

							'padding' => array(
								'type' => 'measurement',
								'label' => __( 'Top and bottom padding', 'so-widgets-bundle' ),
							),

							'padding_extra_top' => array(
								'type' => 'measurement',
								'label' => __( 'Extra top padding', 'so-widgets-bundle' ),
								'description' => __( 'Additional padding added to the top of the slider', 'so-widgets-bundle' ),
							),

							'padding_sides' => array(
								'type' => 'measurement',
								'label' => __( 'Side padding', 'so-widgets-bundle' ),
							),
						),
					),
					'vertically_align' => array(
						'type' => 'checkbox',
						'label' => __( 'Vertically center align slide contents', 'so-widgets-bundle' ),
						'description' => __( 'For perfect centering, consider setting the Extra top padding setting to 0 when enabling this setting.', 'so-widgets-bundle' ),
					),
				),
			),

			'design' => array(
				'type' => 'section',
				'label' => __( 'Design', 'so-widgets-bundle' ),
				'fields' => array(
					'heading_font' => array(
						'type' => 'font',
						'label' => __( 'Heading font', 'so-widgets-bundle' ),
						'default' => '',
					),

					'heading_color' => array(
						'type' => 'color',
						'label' => __( 'Heading color', 'so-widgets-bundle' ),
						'default' => '#fff',
					),

					'heading_size' => array(
						'type' => 'measurement',
						'label' => __( 'Heading size', 'so-widgets-bundle' ),
						'description' => __( 'Enter the h1 font size. h2 - h6 will be proportionally sized based on this value.', 'so-widgets-bundle' ),
						'default' => '38px',
					),

					'fittext' => array(
						'type' => 'checkbox',
						'label' => __( 'Use FitText', 'so-widgets-bundle' ),
						'description' => __( 'Dynamically adjust your heading font size based on screen size.', 'so-widgets-bundle' ),
						'default' => true,
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
						'label' => __( 'FitText compressor strength', 'so-widgets-bundle' ),
						'description' => __( 'The higher the value, the more your headings will be scaled down. Values above 1 are allowed.', 'so-widgets-bundle' ),
						'default' => 0.85,
						'step' => 0.01,
						'state_handler' => array(
							'use_fittext[show]' => array( 'show' ),
							'use_fittext[hide]' => array( 'hide' ),
						),
					),

					'heading_shadow' => array(
						'type' => 'slider',
						'label' => __( 'Heading shadow intensity', 'so-widgets-bundle' ),
						'max' => 100,
						'min' => 0,
						'default' => 50,
					),

					'text_color' => array(
						'type' => 'color',
						'label' => __( 'Text color', 'so-widgets-bundle' ),
						'default' => '#f6f6f6',
					),
					'text_size' => array(
						'type' => 'measurement',
						'label' => __( 'Text size', 'so-widgets-bundle' ),
						'default' => '16px',
					),
					'text_font' => array(
						'type' => 'font',
						'label' => __( 'Text font', 'so-widgets-bundle' ),
						'default' => '',
					),
					'text_shadow' => array(
						'type' => 'slider',
						'label' => __( 'Text shadow intensity', 'so-widgets-bundle' ),
						'max' => 100,
						'min' => 0,
						'default' => 25,
					),

					'link_color' => array(
						'type' => 'color',
						'label' => __( 'Link color', 'so-widgets-bundle' ),
					),

					'link_color_hover' => array(
						'type' => 'color',
						'label' => __( 'Link hover color', 'so-widgets-bundle' ),
					),
				),
			),
		) );
	}

	public function filter_button_widget_form( $form_fields ) {
		unset( $form_fields['design']['fields']['align'] );
		unset( $form_fields['design']['fields']['mobile_align'] );

		return $form_fields;
	}

	/**
	 * Get everything necessary for the background image.
	 *
	 * @return array
	 */
	public function get_frame_background( $i, $frame ) {
		$background_image = siteorigin_widgets_get_attachment_image_src(
			$frame['background']['image'],
			! empty( $frame['background']['size'] ) ? $frame['background']['size'] : 'full',
			! empty( $frame['background']['image_fallback'] ) ? $frame['background']['image_fallback'] : ''
		);

		return array(
			'color' => ! empty( $frame['background']['color'] ) ? $frame['background']['color'] : false,
			'image' => ! empty( $background_image[0] ) ? $background_image[0] : false,
			'image-alt' => ! empty( $frame['background']['alt'] ) ? $frame['background']['alt'] : '',
			'image-width' => ! empty( $background_image[1] ) ? $background_image[1] : 0,
			'image-height' => ! empty( $background_image[2] ) ? $background_image[2] : 0,
			'image-sizing' => $frame['background']['image_type'],
			'url' => ! empty( $frame['background']['url'] ) ? $frame['background']['url'] : false,
			'new_window' => ! empty( $frame['background']['new_window'] ),
			'videos' => $frame['background']['videos'],
			'video-sizing' => 'background',
			'opacity' => (int) $frame['background']['opacity'] / 100,
		);
	}

	/**
	 * Render the actual content of the frame
	 */
	public function render_frame_contents( $i, $frame ) {
		?>
		<div class="sow-slider-image-container">
			<div class="sow-slider-image-wrapper">
				<?php echo $this->process_content( $frame['content'], $frame ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Process the content. Most importantly add the buttons by replacing [buttons] in the content
	 *
	 * @return string
	 */
	public function process_content( $content, $frame ) {
		$content = wp_kses_post( $content );

		if ( ! empty( $frame['autop'] ) ) {
			$content = wpautop( $content );
		}

		if ( strpos( $content, '[buttons]' ) !== false ) {
			// Replace [buttons] with button wrapper.
			$content = preg_replace( '/(?:<(?:p|h\d|em|strong|li|blockquote) *([^>]*)> *)?\[ *buttons *\](:? *<\/(?:p|h\d|em|strong|li|blockquote)>)?/i', '<div class="sow-hero-buttons" $1>[SiteOriginHeroButton]</div>', $content );

			$button_code = '';
			// Generate buttons.
			foreach ( $frame['buttons'] as $button ) {
				$button_code .= $this->sub_widget( 'SiteOrigin_Widget_Button_Widget', array(), $button['button'], true );
			}

			// Add buttons to wrapper.
			$content = str_replace( '[SiteOriginHeroButton]', $button_code, $content );
		}

		// Process normal shortcodes
		$content = do_shortcode( shortcode_unautop( $content ) );

		return apply_filters( 'siteorigin_hero_frame_content', $content, $frame );
	}

	/**
	 * Migrate Slider settings.
	 *
	 * @return mixed
	 */
	public function modify_instance( $instance ) {
		if ( empty( $instance ) ) {
			return array();
		}

		// Migrate Text shadow intensity setting to the 0 - 100 range.
		if (
			! empty( $instance['design'] ) &&
			! empty( $instance['design']['text_shadow'] ) &&
			(int) $instance['design']['text_shadow'] != $instance['design']['text_shadow']
		) {
			$instance['design']['text_shadow'] *= 100;
		}

		// Run general slider migrations.
		$instance = parent::modify_instance( $instance );

		return $instance;
	}

	/**
	 * The less variables to control the design of the slider
	 *
	 * @return array
	 */
	public function get_less_variables( $instance ) {
		$less = array();

		if ( empty( $instance ) ) {
			return $less;
		}

		// Slider navigation controls
		$less['nav_color_hex'] = $instance['controls']['nav_color_hex'];
		$less['nav_size'] = $instance['controls']['nav_size'];
		$less['nav_align'] = ! empty( $instance['controls']['nav_align'] ) ? $instance['controls']['nav_align'] : 'right';

		// Measurement field type options
		$meas_options = array();

		// Layouts settings.
		if ( ! empty( $instance['layout'] ) ) {
			if ( ! empty( $instance['layout']['desktop'] ) ) {
				$settings = $instance['layout']['desktop'];

				$meas_options['slide_height'] = ! empty( $settings['height'] ) ? $settings['height'] : '';
				$meas_options['slide_padding'] = ! empty( $settings['padding'] ) ? $settings['padding'] : '';
				$meas_options['slide_padding_extra_top'] = ! empty( $settings['padding_extra_top'] ) ? $settings['padding_extra_top'] : '';
				$meas_options['slide_padding_sides'] = ! empty( $settings['padding_sides'] ) ? $settings['padding_sides'] : '';
				$meas_options['slide_width'] = ! empty( $settings['width'] ) ? $settings['width'] : '';
			}

			if ( ! empty( $instance['layout']['mobile'] ) ) {
				$settings = $instance['layout']['mobile'];
				$meas_options['slide_height_responsive'] = ! empty( $settings['height_responsive'] ) ? $settings['height_responsive'] : '';

				if ( isset( $settings['padding'] ) ) {
					$meas_options['slide_padding_responsive'] = ! empty( $settings['padding'] ) ? $settings['padding'] : '0px';
				}

				if ( isset( $settings['padding_extra_top'] ) ) {
					$meas_options['slide_padding_extra_top_responsive'] = ! empty( $settings['padding_extra_top'] ) ? $settings['padding_extra_top'] : '0px';
				}

				// If neither padding is set, we need to unset them both to prevent an override.
				if (
					(
						empty( $settings['slide_padding_responsive'] ) ||
						$meas_options['slide_padding_responsive'] == '0px'
					) &&
					(
						empty( $settings['slide_padding_extra_top_responsive'] ) ||
						$meas_options['slide_padding_extra_top_responsive'] == '0px'
					)
				) {
					unset( $meas_options['slide_padding_responsive'] );
					unset( $meas_options['slide_padding_extra_top_responsive'] );
				}
				$meas_options['slide_padding_sides_responsive'] = ! empty( $settings['padding_sides'] ) ? $settings['padding_sides'] : '';
			}
		}

		$meas_options['heading_size'] = $instance['design']['heading_size'];
		$meas_options['text_size'] = $instance['design']['text_size'];

		foreach ( $meas_options as $key => $val ) {
			$less[ $key ] = $this->add_default_measurement_unit( $val );
		}

		$less['vertically_align'] = empty( $instance['layout']['vertically_align'] ) ? 'false' : 'true';

		$less['heading_shadow'] = (int) $instance['design']['heading_shadow'];
		$less['heading_color'] = $instance['design']['heading_color'];
		$less['text_shadow'] = isset( $instance['design']['text_shadow'] ) ? (float) $instance['design']['text_shadow'] : 25;
		$less['text_color'] = $instance['design']['text_color'];

		$less['link_color'] = ! empty( $instance['design']['link_color'] ) ? $instance['design']['link_color'] : '';
		$less['link_color_hover'] = ! empty( $instance['design']['link_color_hover'] ) ? $instance['design']['link_color_hover'] : '';

		$heading_font = siteorigin_widget_get_font( $instance['design']['heading_font'] );
		$less['heading_font'] = $heading_font['family'];

		if ( ! empty( $heading_font['weight'] ) ) {
			$less['heading_font_weight'] = $heading_font['weight_raw'];
			$less['heading_font_style'] = $heading_font['style'];
		}

		if ( ! empty( $instance['design']['text_font'] ) ) {
			$text_font = siteorigin_widget_get_font( $instance['design']['text_font'] );
			$less['text_font'] = $text_font['family'];

			if ( ! empty( $text_font['weight'] ) ) {
				$less['text_font_weight'] = $text_font['weight_raw'];
				$less['text_font_style'] = $text_font['style'];
			}
		}

		$global_settings = $this->get_global_settings();

		if ( ! empty( $global_settings['responsive_breakpoint'] ) ) {
			$less['responsive_breakpoint'] = $global_settings['responsive_breakpoint'];
		}

		return $less;
	}

	public function add_default_measurement_unit( $val ) {
		if ( ! empty( $val ) ) {
			if ( !preg_match( '/\d+([a-zA-Z%]+)/', $val ) ) {
				$val .= 'px';
			}
		}

		return $val;
	}

	public function wrapper_class_filter( $classes, $instance ) {
		if ( ! empty( $instance['design']['fittext'] ) ) {
			$classes[] = 'so-widget-fittext-wrapper';
		}

		return $classes;
	}

	public function wrapper_data_filter( $data, $instance ) {
		if ( ! empty( $instance['design']['fittext'] ) && ! empty( $instance['design']['fittext_compressor'] ) ) {
			$data['fit-text-compressor'] = $instance['design']['fittext_compressor'];
		}

		return $data;
	}

	public function enqueue_widget_scripts( $instance ) {
		if ( ! empty( $instance['design']['fittext'] ) || $this->is_preview( $instance ) ) {
			wp_enqueue_script( 'sowb-fittext' );
		}
	}

	public function get_form_teaser() {
		if ( class_exists( 'SiteOrigin_Premium' ) ) {
			return false;
		}

		return array(
			sprintf(
				__( 'Add multiple Hero frames in one go with %sSiteOrigin Premium%s', 'so-widgets-bundle' ),
				'<a href="https://siteorigin.com/downloads/premium/?featured_addon=plugin/multiple-media" target="_blank" rel="noopener noreferrer">',
				'</a>'
			),
			sprintf(
				__( 'Add Hero frame content animation effects with %sSiteOrigin Premium%s', 'so-widgets-bundle' ),
				'<a href="https://siteorigin.com/downloads/premium/?featured_addon=plugin/hero" target="_blank" rel="noopener noreferrer">',
				'</a>'
			),
			sprintf(
				__( 'Add parallax and fixed background images with %sSiteOrigin Premium%s', 'so-widgets-bundle' ),
				'<a href="https://siteorigin.com/downloads/premium/?featured_addon=plugin/parallax-sliders" target="_blank" rel="noopener noreferrer">',
				'</a>'
			),
			sprintf(
				__( 'Use Google Fonts right inside the Hero Widget with %sSiteOrigin Premium%s', 'so-widgets-bundle' ),
				'<a href="https://siteorigin.com/downloads/premium/?featured_addon=plugin/web-font-selector" target="_blank" rel="noopener noreferrer">',
				'</a>'
			),
		);
	}
}

siteorigin_widget_register( 'sow-hero', __FILE__, 'SiteOrigin_Widget_Hero_Widget' );
