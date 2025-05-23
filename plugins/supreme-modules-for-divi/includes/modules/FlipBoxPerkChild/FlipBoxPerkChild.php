<?php

class DSM_FlipBox_Perk_Child extends ET_Builder_Module {

	public $slug       = 'dsm_flipbox_child';
	public $vb_support = 'on';
	public $type       = 'child';
	public $icon_element_selector;
	public $icon_element_classname;

	protected $module_credits = array(
		'module_uri' => 'https://divisupreme.com/',
		'author'     => 'Divi Supreme',
		'author_uri' => 'https://divisupreme.com/',
	);

	public function init() {
		$this->name                        = esc_html__( 'Flipbox Item', 'supreme-modules-for-divi' );
		$this->advanced_setting_title_text = esc_html__( 'Flipbox Item', 'supreme-modules-for-divi' );
		$this->settings_text               = esc_html__( 'Flipbox Item Settings', 'supreme-modules-for-divi' );
		$this->main_css_element            = '%%order_class%%';
		$this->child_title_var             = 'admin_title';
		$this->child_title_fallback_var    = 'title';
		$this->icon_element_selector       = '%%order_class%% .et-pb-icon';
		$this->icon_element_classname      = 'et-pb-icon';

		$this->settings_modal_toggles = array(
			'general'    => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'supreme-modules-for-divi' ),
					'link'         => esc_html__( 'Link', 'supreme-modules-for-divi' ),
					'image'        => esc_html__( 'Image & Icon', 'supreme-modules-for-divi' ),
				),
			),
			'advanced'   => array(
				'toggles' => array(
					'icon_settings' => esc_html__( 'Image & Icon', 'supreme-modules-for-divi' ),
					'text'          => array(
						'title'    => esc_html__( 'Text', 'supreme-modules-for-divi' ),
						'priority' => 49,
					),
					'width'         => array(
						'title'    => esc_html__( 'Sizing', 'supreme-modules-for-divi' ),
						'priority' => 65,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'attributes' => array(
						'title'    => esc_html__( 'Attributes', 'supreme-modules-for-divi' ),
						'priority' => 95,
					),
				),
			),
		);
	}

	public function get_advanced_fields_config() {
		return array(
			'fonts'           => array(
				'header'  => array(
					'label'          => esc_html__( 'Title', 'supreme-modules-for-divi' ),
					'css'            => array(
						'main' => '.dsm_flipbox %%order_class%% h1.et_pb_module_header, .dsm_flipbox %%order_class%% h2.et_pb_module_header, .dsm_flipbox %%order_class%% h3.et_pb_module_header, .dsm_flipbox %%order_class%% h4.et_pb_module_header, .dsm_flipbox %%order_class%% h5.et_pb_module_header, .dsm_flipbox %%order_class%% h6.et_pb_module_header',
					),
					'font_size'      => array(
						'default' => '18px',
					),
					'line_height'    => array(
						'default' => '1em',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
					'header_level'   => array(
						'default' => 'h4',
					),
				),
				'content' => array(
					'label'          => esc_html__( 'Body', 'supreme-modules-for-divi' ),
					'css'            => array(
						'main' => '.dsm_flipbox %%order_class%% .dsm-content',
					),
					'font_size'      => array(
						'default' => '14px',
					),
					'line_height'    => array(
						'default' => '1.7em',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
						'css'               => array(
							'main' => "{$this->main_css_element} .dsm-content",
						),
					),
				),
				'subhead' => array(
					'label'          => esc_html__( 'Subhead', 'supreme-modules-for-divi' ),
					'css'            => array(
						'main' => '.dsm_flipbox %%order_class%% .dsm-subtitle',
					),
					'font_size'      => array(
						'default' => '14px',
					),
					'line_height'    => array(
						'default' => '1em',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
			),
			'text'            => array(
				'use_background_layout' => true,
				'css'                   => array(
					'text_shadow' => '%%order_class%% .dsm_flipbox_wrapper',
				),
				'options'               => array(
					'background_layout' => array(
						'default_on_front' => 'light',
					),
					'text_orientation'  => array(
						'default_on_front' => 'left',
					),
				),
			),
			'borders'         => array(
				'default' => array(),
				'image'   => array(
					'css'             => array(
						'main' => array(
							'border_radii'  => '%%order_class%% .dsm_flipbox_child_image .dsm_flipbox_child_image_wrap',
							'border_styles' => '%%order_class%% .dsm_flipbox_child_image .dsm_flipbox_child_image_wrap',
						),
					),
					'label_prefix'    => esc_html__( 'Image', 'supreme-modules-for-divi' ),
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'icon_settings',
					'depends_on'      => array( 'use_icon' ),
					'depends_show_if' => 'off',
				),
			),
			'box_shadow'      => array(
				'default' => array(),
				'image'   => array(
					'label'             => esc_html__( 'Image Box Shadow', 'supreme-modules-for-divi' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'icon_settings',
					'depends_show_if'   => 'off',
					'css'               => array(
						'main'        => '%%order_class%% .dsm_flipbox_child_image .dsm_flipbox_child_image_wrap',
						'show_if_not' => array(
							'use_icon' => 'on',
						),
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'button'          => array(
				'button' => array(
					'label'          => esc_html__( 'Button', 'supreme-modules-for-divi' ),
					'css'            => array(
						'main'         => "{$this->main_css_element} .et_pb_button",
						'limited_main' => "{$this->main_css_element} .et_pb_button",
						'alignment'    => "{$this->main_css_element} .et_pb_button_wrapper",
					),
					'use_alignment'  => true,
					'box_shadow'     => array(
						'css' => array(
							'main' => '%%order_class%% .et_pb_button',
						),
					),
					'margin_padding' => array(
						'css' => array(
							'main'      => "{$this->main_css_element} .et_pb_button_wrapper .et_pb_button",
							'important' => 'all',
						),
					),
				),
			),
			'filters'         => array(
				'child_filters_target' => array(
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'icon_settings',
					'depends_show_if' => 'off',
				),
			),
			'icon_settings'   => array(
				'css' => array(
					'main' => '%%order_class%% .dsm_flipbox_child_image',
				),
			),
			'position_fields' => false,
		);
	}

	public function get_fields() {
		$et_accent_color = et_builder_accent_color();

		$image_icon_placement = array(
			'top' => esc_html__( 'Top', 'supreme-modules-for-divi' ),
		);

		if ( ! is_rtl() ) {
			$image_icon_placement['left'] = esc_html__( 'Left', 'supreme-modules-for-divi' );
		} else {
			$image_icon_placement['right'] = esc_html__( 'Right', 'supreme-modules-for-divi' );
		}

		return array(
			'admin_title'           => array(
				'label'       => esc_html__( 'Admin Label', 'supreme-modules-for-divi' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the flip box item in the builder for easy identification.', 'supreme-modules-for-divi' ),
				'toggle_slug' => 'admin_label',
			),
			'title'                 => array(
				'label'           => esc_html__( 'Title', 'supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Text entered here will appear as title.', 'supreme-modules-for-divi' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'subtitle'              => array(
				'label'           => esc_html__( 'Sub Title', 'supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Text entered here will appear as subtitle.', 'supreme-modules-for-divi' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'use_icon'              => array(
				'label'            => esc_html__( 'Use Icon', 'supreme-modules-for-divi' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'basic_option',
				'options'          => array(
					'off' => esc_html__( 'No', 'supreme-modules-for-divi' ),
					'on'  => esc_html__( 'Yes', 'supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'image',
				'default_on_front' => 'off',
				'default'          => 'off',
				'affects'          => array(
					'border_radii_image',
					'border_styles_image',
					'box_shadow_style_image',
					'font_icon',
					'image_max_width',
					'use_icon_font_size',
					'use_circle',
					'icon_color',
					'image',
					'alt',
					'child_filter_hue_rotate',
					'child_filter_saturate',
					'child_filter_brightness',
					'child_filter_contrast',
					'child_filter_invert',
					'child_filter_sepia',
					'child_filter_opacity',
					'child_filter_blur',
					'child_mix_blend_mode',
				),
				'description'      => esc_html__( 'Here you can choose whether icon set below should be used.', 'supreme-modules-for-divi' ),
			),
			'font_icon'             => array(
				'label'           => esc_html__( 'Icon', 'supreme-modules-for-divi' ),
				'type'            => 'select_icon',
				'option_category' => 'basic_option',
				'class'           => array( 'et-pb-font-icon' ),
				'toggle_slug'     => 'image',
				'description'     => esc_html__( 'Choose an icon to display with your Flipbox.', 'supreme-modules-for-divi' ),
				'depends_show_if' => 'on',
			),
			'icon_color'            => array(
				'default'         => $et_accent_color,
				'label'           => esc_html__( 'Icon Color', 'supreme-modules-for-divi' ),
				'type'            => 'color-alpha',
				'description'     => esc_html__( 'Here you can define a custom color for your icon.', 'supreme-modules-for-divi' ),
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'icon_settings',
			),
			'use_circle'            => array(
				'label'            => esc_html__( 'Circle Icon', 'supreme-modules-for-divi' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'No', 'supreme-modules-for-divi' ),
					'on'  => esc_html__( 'Yes', 'supreme-modules-for-divi' ),
				),
				'affects'          => array(
					'use_circle_border',
					'circle_color',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon_settings',
				'description'      => esc_html__( 'Here you can choose whether icon set above should display within a circle.', 'supreme-modules-for-divi' ),
				'depends_show_if'  => 'on',
				'default_on_front' => 'off',
			),
			'circle_color'          => array(
				'default'         => $et_accent_color,
				'label'           => esc_html__( 'Circle Color', 'supreme-modules-for-divi' ),
				'type'            => 'color-alpha',
				'description'     => esc_html__( 'Here you can define a custom color for the icon circle.', 'supreme-modules-for-divi' ),
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'icon_settings',
			),
			'use_circle_border'     => array(
				'label'            => esc_html__( 'Show Circle Border', 'supreme-modules-for-divi' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'layout',
				'options'          => array(
					'off' => esc_html__( 'No', 'supreme-modules-for-divi' ),
					'on'  => esc_html__( 'Yes', 'supreme-modules-for-divi' ),
				),
				'affects'          => array(
					'circle_border_color',
				),
				'description'      => esc_html__( 'Here you can choose whether if the icon circle border should display.', 'supreme-modules-for-divi' ),
				'depends_show_if'  => 'on',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon_settings',
				'default_on_front' => 'off',
			),
			'circle_border_color'   => array(
				'default'         => $et_accent_color,
				'label'           => esc_html__( 'Circle Border Color', 'supreme-modules-for-divi' ),
				'type'            => 'color-alpha',
				'description'     => esc_html__( 'Here you can define a custom color for the icon circle border.', 'supreme-modules-for-divi' ),
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'icon_settings',
			),
			'image'                 => array(
				'label'              => esc_html__( 'Image', 'supreme-modules-for-divi' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'supreme-modules-for-divi' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'supreme-modules-for-divi' ),
				'update_text'        => esc_attr__( 'Set As Image', 'supreme-modules-for-divi' ),
				'depends_show_if'    => 'off',
				'description'        => esc_html__( 'Upload an image to display at the top of your Flipbox.', 'supreme-modules-for-divi' ),
				'toggle_slug'        => 'image',
				'dynamic_content'    => 'image',
			),
			'alt'                   => array(
				'label'           => esc_html__( 'Image Alt Text', 'supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define the HTML ALT text for your image here.', 'supreme-modules-for-divi' ),
				'depends_show_if' => 'off',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
				'dynamic_content' => 'text',
			),
			'icon_placement'        => array(
				'label'            => esc_html__( 'Image/Icon Placement', 'supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'layout',
				'options'          => $image_icon_placement,
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon_settings',
				'description'      => esc_html__( 'Here you can choose where to place the icon.', 'supreme-modules-for-divi' ),
				'default_on_front' => 'top',
			),
			'content'               => array(
				'label'           => esc_html__( 'Content', 'supreme-modules-for-divi' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Content entered here will appear inside the module.', 'supreme-modules-for-divi' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'image_max_width'       => array(
				'label'            => esc_html__( 'Image Width', 'supreme-modules-for-divi' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'width',
				'mobile_options'   => true,
				'validate_unit'    => true,
				'depends_show_if'  => 'off',
				'default'          => '100%',
				'default_unit'     => '%',
				'default_on_front' => '',
				'allow_empty'      => true,
				'range_settings'   => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'responsive'       => true,
			),
			'use_icon_font_size'    => array(
				'label'            => esc_html__( 'Use Icon Font Size', 'supreme-modules-for-divi' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'font_option',
				'options'          => array(
					'off' => esc_html__( 'No', 'supreme-modules-for-divi' ),
					'on'  => esc_html__( 'Yes', 'supreme-modules-for-divi' ),
				),
				'affects'          => array(
					'icon_font_size',
				),
				'depends_show_if'  => 'on',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon_settings',
				'default_on_front' => 'off',
			),
			'icon_font_size'        => array(
				'label'            => esc_html__( 'Icon Font Size', 'supreme-modules-for-divi' ),
				'type'             => 'range',
				'option_category'  => 'font_option',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon_settings',
				'default'          => '96px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'range_settings'   => array(
					'min'  => '1',
					'max'  => '120',
					'step' => '1',
				),
				'mobile_options'   => true,
				'depends_show_if'  => 'on',
				'responsive'       => true,
			),
			'button_text'           => array(
				'label'           => esc_html__( 'Button Text', 'supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your desired button text, or leave blank for no button.', 'supreme-modules-for-divi' ),
				'toggle_slug'     => 'button',
				'dynamic_content' => 'text',
			),
			'button_url'            => array(
				'label'           => esc_html__( 'Button URL', 'supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input URL for your button.', 'supreme-modules-for-divi' ),
				'toggle_slug'     => 'button',
				'dynamic_content' => 'url',
			),
			'button_url_new_window' => array(
				'default'          => 'off',
				'default_on_front' => true,
				'label'            => esc_html__( 'Url Opens', 'supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'In The Same Window', 'supreme-modules-for-divi' ),
					'on'  => esc_html__( 'In The New Tab', 'supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'button',
				'description'      => esc_html__( 'Choose whether your link opens in a new window or not', 'supreme-modules-for-divi' ),
			),
			'content_orientation'   => array(
				'label'           => esc_html__( 'Text Vertical Alignment', 'supreme-modules-for-divi' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'flex-start' => esc_html__( 'Top', 'supreme-modules-for-divi' ),
					'center'     => esc_html__( 'Center', 'supreme-modules-for-divi' ),
					'flex-end'   => esc_html__( 'Bottom', 'supreme-modules-for-divi' ),
				),
				'default'         => 'center',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'This setting determines the vertical alignment of your content. Your content can either be align to the top, vertically centered, or aligned to the bottom.', 'supreme-modules-for-divi' ),
			),
		);
	}

	public function get_transition_fields_css_props() {
		return array();
	}

	public function render( $attrs, $content, $render_slug ) {
		global $dsm_parent_level;

		$parent_header_level = self::$_->array_get( $dsm_parent_level, 'header_level', '' );

		$title                       = $this->props['title'];
		$subtitle                    = $this->props['subtitle'];
		$button_text                 = $this->props['button_text'];
		$image                       = $this->props['image'];
		$alt                         = $this->props['alt'];
		$icon_placement              = $this->props['icon_placement'];
		$font_icon                   = $this->props['font_icon'];
		$use_icon                    = $this->props['use_icon'];
		$use_circle                  = $this->props['use_circle'];
		$use_circle_border           = $this->props['use_circle_border'];
		$icon_color                  = $this->props['icon_color'];
		$circle_color                = $this->props['circle_color'];
		$circle_border_color         = $this->props['circle_border_color'];
		$use_icon_font_size          = $this->props['use_icon_font_size'];
		$icon_font_size              = $this->props['icon_font_size'];
		$icon_font_size_tablet       = $this->props['icon_font_size_tablet'];
		$icon_font_size_phone        = $this->props['icon_font_size_phone'];
		$icon_font_size_last_edited  = $this->props['icon_font_size_last_edited'];
		$image_max_width             = $this->props['image_max_width'];
		$image_max_width_tablet      = $this->props['image_max_width_tablet'];
		$image_max_width_phone       = $this->props['image_max_width_phone'];
		$image_max_width_last_edited = $this->props['image_max_width_last_edited'];
		$button_url                  = $this->props['button_url'];
		$button_url_new_window       = $this->props['button_url_new_window'];
		$button_custom               = $this->props['custom_button'];
		$button_rel                  = $this->props['button_rel'];
		$custom_icon                 = $this->props['button_icon'];
		$content_orientation         = $this->props['content_orientation'];
		$background_layout           = $this->props['background_layout'];
		$text_orientation            = $this->props['text_orientation'];
		$header_level                = $this->props['header_level'];

		// A Fix for empty value for contents line-height value for exisitng flipbox module.
		if ( isset( $this->props['header_line_height'] ) && '' === $this->props['header_line_height'] ) {
			if ( '' === $this->props['header_line_height'] ) {
				$this->props['header_line_height'] = '1em';
			}
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% h1.et_pb_module_header, %%order_class%% h2.et_pb_module_header, %%order_class%% h3.et_pb_module_header, %%order_class%% h4.et_pb_module_header, %%order_class%% h5.et_pb_module_header, %%order_class%% h6.et_pb_module_header',
					'declaration' => sprintf(
						'line-height: %1$s;',
						esc_attr( $this->props['header_line_height'] )
					),
				)
			);
		}

		if ( isset( $this->props['subhead_line_height'] ) && '' === $this->props['subhead_line_height'] ) {
			if ( '' === $this->props['subhead_line_height'] ) {
				$this->props['subhead_line_height'] = '1em';
			}
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .dsm-subtitle',
					'declaration' => sprintf(
						'line-height: %1$s;',
						esc_attr( $this->props['subhead_line_height'] )
					),
				)
			);
		}

		if ( isset( $this->props['content_line_height'] ) && '' === $this->props['content_line_height'] ) {
			if ( '' === $this->props['content_line_height'] ) {
				$this->props['content_line_height'] = '1.7em';
			}
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .dsm-content',
					'declaration' => sprintf(
						'line-height: %1$s;',
						esc_attr( $this->props['content_line_height'] )
					),
				)
			);
		}

		$image_pathinfo = pathinfo( $image );
		$is_image_svg   = isset( $image_pathinfo['extension'] ) ? 'svg' === $image_pathinfo['extension'] : false;

		if ( '' !== $image_max_width_tablet || '' !== $image_max_width_phone || '' !== $image_max_width || $is_image_svg ) {
			$is_size_px = false;

			// If size is given in px, we want to override parent width
			if (
				false !== strpos( $image_max_width, 'px' ) ||
				false !== strpos( $image_max_width_tablet, 'px' ) ||
				false !== strpos( $image_max_width_phone, 'px' )
			) {
				$is_size_px = true;
			}
			// SVG image overwrite. SVG image needs its value to be explicit
			if ( '' === $image_max_width && $is_image_svg ) {
				$image_max_width = '100%';
			}

			$image_max_width_selector = 'top' === $icon_placement && $is_image_svg ? '%%order_class%% .dsm_flipbox_child_image' : '%%order_class%% .dsm_flipbox_child_image .dsm_flipbox_child_image_wrap';
			$image_max_width_property = ( $is_image_svg || $is_size_px ) ? 'width' : 'max-width';

			$image_max_width_responsive_active = et_pb_get_responsive_status( $image_max_width_last_edited );

			$image_max_width_values = array(
				'desktop' => $image_max_width,
				'tablet'  => $image_max_width_responsive_active ? $image_max_width_tablet : '',
				'phone'   => $image_max_width_responsive_active ? $image_max_width_phone : '',
			);

			et_pb_responsive_options()->generate_responsive_css( $image_max_width_values, $image_max_width_selector, $image_max_width_property, $render_slug );
		}

		if ( '' !== $title ) {
			$title = sprintf( '<%1$s class="dsm-title et_pb_module_header">%2$s</%1$s>', '' === $header_level ? $parent_header_level : et_pb_process_header_level( $header_level, 'h4' ), $title );
		}

		if ( '' !== $subtitle ) {
			$subtitle = sprintf( '<span class="dsm-subtitle">%1$s</span>', $subtitle );
		}

		// Render button
		$button = $this->render_button(
			array(
				'button_classname' => array(),
				'button_custom'    => $button_custom,
				'button_rel'       => $button_rel,
				'button_text'      => $button_text,
				'button_url'       => $button_url,
				'custom_icon'      => $custom_icon,
				'url_new_window'   => $button_url_new_window,
				'display_button'   => '' !== $button_url && '' !== $button_text,
			)
		);

		if ( 'center' !== $content_orientation ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%%',
					'declaration' => sprintf(
						'align-items: %1$s;',
						esc_attr( $content_orientation )
					),
				)
			);
		}

		if ( 'off' === $use_icon ) {
			$image = ( '' !== trim( $image ) ) ? sprintf(
				'<img src="%1$s" alt="%2$s" />',
				esc_url( $image ),
				esc_attr( $alt )
				// esc_attr( " et_pb_animation_{$animation}" )
			) : '';
		} else {
			$icon_style = sprintf( 'color: %1$s;', esc_attr( $icon_color ) );

			if ( 'on' === $use_circle ) {
				$icon_style .= sprintf( ' background-color: %1$s;', esc_attr( $circle_color ) );

				if ( 'on' === $use_circle_border ) {
					$icon_style .= sprintf( ' border-color: %1$s;', esc_attr( $circle_border_color ) );
				}
			}

			$icon_hover_selector = str_replace( $this->icon_element_classname, $this->icon_element_classname . ':hover', $this->icon_element_selector );

			// Font Icon Style.
			$this->generate_styles(
				array(
					'utility_arg'    => 'icon_font_family',
					'render_slug'    => $render_slug,
					'base_attr_name' => 'font_icon',
					'important'      => true,
					'selector'       => $this->icon_element_selector,
					'hover_selector' => $icon_hover_selector,
					'processor'      => array(
						'ET_Builder_Module_Helper_Style_Processor',
						'process_extended_icon',
					),
				)
			);

			if ( 'off' !== $use_icon_font_size ) {
				// Font Icon Size Style.
				$this->generate_styles(
					array(
						'base_attr_name' => 'icon_font_size',
						'selector'       => $this->icon_element_selector,
						'css_property'   => 'font-size',
						'render_slug'    => $render_slug,
						'type'           => 'range',
						'hover_selector' => $icon_hover_selector,
					)
				);
			}

			$image = ( '' !== $font_icon ) ? sprintf(
				'<span class="et-pb-icon%2$s%3$s" style="%4$s">%1$s</span>',
				esc_attr( et_pb_process_font_icon( $font_icon ) ),
				// esc_attr( " et_pb_animation_{$animation}" ),
				( 'on' === $use_circle ? ' et-pb-icon-circle' : '' ),
				( 'on' === $use_circle && 'on' === $use_circle_border ? ' et-pb-icon-circle-border' : '' ),
				$icon_style
			) : '';
		}

		// Images: Add CSS Filters and Mix Blend Mode rules (if set)
		$generate_css_image_filters = '';
		if ( $image && array_key_exists( 'icon_settings', $this->advanced_fields ) && array_key_exists( 'css', $this->advanced_fields['icon_settings'] ) ) {
			$generate_css_image_filters = $this->generate_css_filters(
				$render_slug,
				'child_',
				self::$data_utils->array_get( $this->advanced_fields['icon_settings']['css'], 'main', '%%order_class%%' )
			);
		}

		$image = $image ? sprintf( '<span class="dsm_flipbox_child_image_wrap">%1$s</span>', $image ) : '';
		$image = $image ? sprintf(
			'<div class="dsm_flipbox_child_image%2$s">%1$s</div>',
			$image,
			esc_attr( $generate_css_image_filters )
		) : '';

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$this->add_classname(
			array(
				"et_pb_bg_layout_{$background_layout}",
				sprintf( ' dsm_flipbox_icon_position_%1$s', esc_attr( $icon_placement ) ),
			)
		);

		add_filter( 'et_global_assets_list', array( $this, 'dsm_load_required_divi_assets' ), 10 );

		$output = sprintf(
			'
			%3$s
			<div class="dsm_flipbox_wrapper%6$s">
				%1$s
				%2$s
				%4$s
				%5$s
			</div>',
			$title,
			$subtitle,
			$image,
			'' !== $this->content ? sprintf(
				'<div class="dsm-content">%1$s</div>',
				et_core_sanitized_previously( $this->content )
			) : '',
			$button,
			$this->get_text_orientation_classname()
		);

		return $output;
	}

	/**
	 * Force load global styles.
	 *
	 * @param array $assets_list Current global assets on the list.
	 *
	 * @return array
	 */
	public function dsm_load_required_divi_assets( $assets_list ) {
		if ( isset( $assets_list['et_icons_all'] ) && isset( $assets_list['et_icons_fa'] ) ) {
			return $assets_list;
		}

		$assets_prefix = et_get_dynamic_assets_path();

		if ( ! isset( $assets_list['et_icons_all'] ) ) {
			$assets_list['et_icons_all'] = array(
				'css' => "{$assets_prefix}/css/icons_all.css",
			);
		}

		if ( ! isset( $assets_list['et_icons_fa'] ) ) {
			$assets_list['et_icons_fa'] = array(
				'css' => "{$assets_prefix}/css/icons_fa_all.css",
			);
		}

		return $assets_list;
	}

	/**
	 * Filter multi view value.
	 *
	 * @since 3.27.1
	 *
	 * @see ET_Builder_Module_Helper_MultiViewOptions::filter_value
	 *
	 * @param mixed $raw_value Props raw value.
	 * @param array $args {
	 *     Context data.
	 *
	 *     @type string $context      Context param: content, attrs, visibility, classes.
	 *     @type string $name         Module options props name.
	 *     @type string $mode         Current data mode: desktop, hover, tablet, phone.
	 *     @type string $attr_key     Attribute key for attrs context data. Example: src, class, etc.
	 *     @type string $attr_sub_key Attribute sub key that availabe when passing attrs value as array such as styes. Example: padding-top, margin-botton, etc.
	 * }
	 *
	 * @return mixed
	 */
	public function multi_view_filter_value( $raw_value, $args ) {

		$name = isset( $args['name'] ) ? $args['name'] : '';

		if ( $raw_value && 'font_icon' === $name ) {
			return et_pb_get_extended_font_icon_value( $raw_value, true );
		}
		return $raw_value;
	}
}

new DSM_FlipBox_Perk_Child();
