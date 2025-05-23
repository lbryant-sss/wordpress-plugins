<?php

class DSM_Text_Badges extends ET_Builder_Module {

	public $slug       = 'dsm_text_badges';
	public $vb_support = 'on';
	public $icon_path;

	protected $module_credits = array(
		'module_uri' => 'https://divisupreme.com/',
		'author'     => 'Divi Supreme',
		'author_uri' => 'https://divisupreme.com/',
	);

	public function init() {
		$this->name                   = esc_html__( 'Supreme Text Badges', 'supreme-modules-for-divi' );
		$this->icon_path              = plugin_dir_path( __FILE__ ) . 'icon.svg';
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'supreme-modules-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(),
			),
		);
	}

	public function get_advanced_fields_config() {
		return array(
			'fonts'          => array(
				'header' => array(
					'label'           => esc_html__( 'Main', 'supreme-modules-for-divi' ),
					'css'             => array(
						'main' => '%%order_class%% h1.et_pb_module_header, %%order_class%% h2.et_pb_module_header, %%order_class%% h3.et_pb_module_header, %%order_class%% h4.et_pb_module_header, %%order_class%% h5.et_pb_module_header, %%order_class%% h6.et_pb_module_header',
					),
					'font_size'       => array(
						'default' => '18px',
					),
					'line_height'     => array(
						'default' => '1em',
					),
					'letter_spacing'  => array(
						'default' => '0px',
					),
					'header_level'    => array(
						'default' => 'h4',
					),
					'hide_text_align' => true,
				),
				'badges' => array(
					'label'            => esc_html__( 'Badges', 'supreme-modules-for-divi' ),
					'css'              => array(
						'main' => '%%order_class%% .dsm-badges',
					),
					'text_color'       => array(
						'default' => '#ffffff',
					),
					'hide_font_size'   => true,
					'hide_line_height' => true,
					'hide_text_align'  => true,
					'letter_spacing'   => array(
						'default' => '0px',
					),
				),
			),
			'text'           => array(
				'use_text_orientation'  => true,
				'use_background_layout' => true,
				'css'                   => array(
					'text_shadow' => '%%order_class%% .dsm-text-badge',
				),
				'options'               => array(
					'background_layout' => array(
						'default' => 'light',
					),
				),
			),
			'background'     => array(
				'css'     => array(
					'main' => '%%order_class%% .dsm-text-badges',
				),
				'options' => array(
					'parallax_method' => array(
						'default' => 'off',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'main' => '%%order_class%% .dsm-text-badges',
				),
			),
			'borders'        => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => '%%order_class%%',
							'border_styles' => '%%order_class%%',
						),
					),
				),
				'image'   => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => '%%order_class%% .dsm-badges',
							'border_styles' => '%%order_class%% .dsm-badges',
						),
					),
					'label_prefix' => esc_html__( 'Badge', 'supreme-modules-for-divi' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'badges',
				),
			),
			'box_shadow'     => array(
				'default' => array(
					'css' => array(
						'main' => '%%order_class%% .dsm-text-badges',
					),
				),
			),
		);
	}

	public function get_fields() {
		$et_accent_color = et_builder_accent_color();
		return array(
			'main_text'               => array(
				'label'            => esc_html__( 'Main Text', 'supreme-modules-for-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
				'default_on_front' => 'Badges',
				'mobile_options'   => true,
				'dynamic_content'  => 'text',
			),
			'badges_text'             => array(
				'label'            => esc_html__( 'Badges Text', 'supreme-modules-for-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
				'default_on_front' => 'New',
				'mobile_options'   => true,
				'dynamic_content'  => 'text',
			),
			'badges_placement'        => array(
				'label'           => esc_html__( 'Badges Placement', 'supreme-modules-for-divi' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'before' => esc_html__( 'Before', 'supreme-modules-for-divi' ),
					'after'  => esc_html__( 'After', 'supreme-modules-for-divi' ),
				),
				'default'         => 'after',
				'description'     => esc_html__( 'Here you can choose the placement of the badges to be before or after the Main Text.', 'supreme-modules-for-divi' ),
				'toggle_slug'     => 'main_content',
			),
			'badges_background_color' => array(
				'label'        => esc_html__( 'Background Color', 'supreme-modules-for-divi' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'default'      => $et_accent_color,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'badges',
				'description'  => esc_html__( 'Here you can define a custom background color for the badge', 'supreme-modules-for-divi' ),
			),
			'badges_gap'              => array(
				'label'            => esc_html__( 'Gap', 'supreme-modules-for-divi' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'badges',
				'mobile_options'   => true,
				'validate_unit'    => true,
				'default'          => '7px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'allow_empty'      => true,
				'responsive'       => true,
				'description'      => esc_html__( 'Here you can define a gap between the text and the badge', 'supreme-modules-for-divi' ),
			),
		);
	}

	public function render( $attrs, $content, $render_slug ) {
		$multi_view              = et_pb_multi_view_options( $this );
		$main_text               = $this->props['main_text'];
		$badges_text             = $this->props['badges_text'];
		$badges_placement        = $this->props['badges_placement'];
		$badges_background_color = $this->props['badges_background_color'];
		$badges_gap              = $this->props['badges_gap'];
		$badges_gap_tablet       = $this->props['badges_gap_tablet'];
		$badges_gap_phone        = $this->props['badges_gap_phone'];
		$badges_gap_last_edited  = $this->props['badges_gap_last_edited'];
		$background_layout       = $this->props['background_layout'];
		$header_level            = $this->props['header_level'];

		$badges_text = $multi_view->render_element(
			array(
				'tag'     => 'span',
				'content' => '{{badges_text}}',
				'attrs'   => array(
					'class' => 'dsm-badges dsm-badges-' . esc_attr( $badges_placement ),
				),
			)
		);

		$main_text = $multi_view->render_element(
			array(
				'content' => '{{main_text}}',
				'attrs'   => array(
					'class' => 'dsm-text-badges-main',
				),
			)
		);

		if ( '' !== $main_text ) {
			$main_text = sprintf(
				'<%1$s class="dsm-text-badges et_pb_module_header">%3$s%2$s%4$s</%1$s>',
				et_pb_process_header_level( $header_level, 'h4' ),
				et_core_esc_previously( $main_text ),
				( 'before' === $badges_placement ? $badges_text : '' ),
				( 'after' === $badges_placement ? $badges_text : '' )
			);
		}

		if ( '' !== $badges_background_color ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .dsm-badges',
					'declaration' => sprintf(
						'background-color: %1$s;',
						esc_html( $badges_background_color )
					),
				)
			);
		}

		if ( '' !== $badges_gap_tablet || '' !== $badges_gap_phone || '7px' !== $badges_gap ) {
			$badges_gap_responsive_active = et_pb_get_responsive_status( $badges_gap_last_edited );

			$badges_gap_values = array(
				'desktop' => $badges_gap,
				'tablet'  => $badges_gap_responsive_active ? $badges_gap_tablet : '',
				'phone'   => $badges_gap_responsive_active ? $badges_gap_phone : '',
			);
			if ( 'after' === $badges_placement ) {
				et_pb_responsive_options()->generate_responsive_css( $badges_gap_values, '%%order_class%% .dsm-badges-after', 'margin-left', $render_slug );
			} else {
				et_pb_responsive_options()->generate_responsive_css( $badges_gap_values, '%%order_class%% .dsm-badges-before', 'margin-right', $render_slug );
			}
		}

		$this->add_classname(
			array(
				$this->get_text_orientation_classname(),
				"et_pb_bg_layout_{$background_layout}",
			)
		);

		if ( isset( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) && ! empty( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) && 'on' === get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) {
			if ( isset( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets_compatibility'] ) && ! empty( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) && 'on' === get_option( 'dsm_settings_misc' )['dsm_dynamic_assets_compatibility'] ) {
				wp_enqueue_style( 'dsm-text-badges', plugin_dir_url( __DIR__ ) . 'Badges/style.css', array(), DSM_VERSION, 'all' );
			} else {
				add_filter( 'et_global_assets_list', array( $this, 'dsm_load_required_divi_assets' ), 10, 3 );
				add_filter( 'et_late_global_assets_list', array( $this, 'dsm_load_required_divi_assets' ), 10, 3 );
			}
		}

		// Render module content.
		$output = sprintf(
			'%1$s',
			$main_text
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
	public function dsm_load_required_divi_assets( $assets_list, $assets_args, $instance ) {
		$assets_prefix     = et_get_dynamic_assets_path();
		$all_shortcodes    = $instance->get_saved_page_shortcodes();
		$this->_cpt_suffix = et_builder_should_wrap_styles() && ! et_is_builder_plugin_active() ? '_cpt' : '';

		if ( ! isset( $assets_list['et_jquery_magnific_popup'] ) ) {
			$assets_list['et_jquery_magnific_popup'] = array(
				'css' => "{$assets_prefix}/css/magnific_popup.css",
			);
		}

		if ( ! isset( $assets_list['et_pb_overlay'] ) ) {
			$assets_list['et_pb_overlay'] = array(
				'css' => "{$assets_prefix}/css/overlay{$this->_cpt_suffix}.css",
			);
		}

		// Badges.
		if ( ! isset( $assets_list['dsm_text_badges'] ) ) {
			$assets_list['dsm_text_badges'] = array(
				'css' => plugin_dir_url( __DIR__ ) . 'Badges/style.css',
			);
		}

		return $assets_list;
	}
}

new DSM_Text_Badges();
