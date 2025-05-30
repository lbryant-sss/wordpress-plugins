<?php

class DSM_FacebookSimpleFeed extends ET_Builder_Module {

	public $slug       = 'dsm_facebook_feed';
	public $vb_support = 'on';
	public $icon_path;

	protected $module_credits = array(
		'module_uri' => 'https://divisupreme.com/',
		'author'     => 'Divi Supreme',
		'author_uri' => 'https://divisupreme.com/',
	);

	public function init() {
		$this->name      = esc_html__( 'Supreme Facebook Feed', 'supreme-modules-for-divi' );
		$this->icon_path = plugin_dir_path( __FILE__ ) . 'icon.svg';
		// Toggle settings
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Facebook Feed Settings', 'supreme-modules-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(),
			),
		);
	}

	public function get_advanced_fields_config() {
		return array(
			'text'       => false,
			'fonts'      => false,
			'background' => array(
				'css'     => array(
					'main' => '%%order_class%%',
				),
				'options' => array(
					'parallax_method' => array(
						'default' => 'off',
					),
				),
			),
			'max_width'  => array(
				'css' => array(
					'main' => '%%order_class%%',
				),
			),
			'borders'    => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => '%%order_class%%',
							'border_styles' => '%%order_class%%',
						),
					),
				),
			),
			'box_shadow' => array(
				'default' => array(
					'css' => array(
						'main' => '%%order_class%%',
					),
				),
			),
			'filters'    => false,
		);
	}

	public function get_fields() {
		return array(
			'fb_app_id_notice' => array(
				'type'        => 'warning',
				'value'       => isset( get_option( 'dsm_settings_social_media' )['dsm_facebook_app_id'] ) && '' !== get_option( 'dsm_settings_social_media' )['dsm_facebook_app_id'] ? true : false,
				'display_if'  => false,
				'message'     => esc_html__(
					sprintf(
						'The Facebook APP ID is currently empty in the <a href="%s" target="_blank">Divi Supreme Plugin Page</a>. This module might not function properly without the Facebook APP ID.',
						admin_url( 'admin.php?page=divi_supreme_settings#dsm_settings_social_media' )
					),
					'supreme-modules-for-divi'
				),
				'toggle_slug' => 'main_content',
			),
			'fb_app_id'        => array(
				'label'            => esc_html__( 'Facebook APP ID', 'supreme-modules-for-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'attributes'       => 'readonly',
				'default_on_front' => isset( get_option( 'dsm_settings_social_media' )['dsm_facebook_app_id'] ) && '' !== get_option( 'dsm_settings_social_media' )['dsm_facebook_app_id'] ? get_option( 'dsm_settings_social_media' )['dsm_facebook_app_id'] : '',
				'description'      => et_get_safe_localization( sprintf( __( 'The Facebook module uses the Facebook APP ID and requires a Facebook APP ID to function. Before using all Facebook module, please make sure you have added your Facebook APP ID inside the Divi Supreme Plugin Page. You can go to <a href="%1$s">Facebook Developer</a> and click on Create New App to get one.', 'supreme-modules-for-divi' ), esc_url( 'https://developers.facebook.com/apps/' ) ) ),
				'toggle_slug'      => 'main_content',
			),
			'fb_page_url'      => array(
				'label'            => esc_html__( 'Facebook Page URL', 'supreme-modules-for-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the Facebook Page URL.', 'supreme-modules-for-divi' ),
				'toggle_slug'      => 'main_content',
				'default_on_front' => 'https://www.facebook.com/divisupreme/',
				'dynamic_content'  => 'url',
			),
			'fb_tabs'          => array(
				'label'           => esc_html__( 'Tabs', 'supreme-modules-for-divi' ),
				'type'            => 'multiple_checkboxes',
				'option_category' => 'configuration',
				'options'         => array(
					'timeline' => esc_html__( 'Timeline', 'supreme-modules-for-divi' ),
					'events'   => esc_html__( 'Events', 'supreme-modules-for-divi' ),
					'messages' => esc_html__( 'Messages', 'supreme-modules-for-divi' ),
				),
				'default'         => 'on|off|off',
				'toggle_slug'     => 'main_content',
				'description'     => esc_html__( 'Here you can choose to show tabs on your facebook page.', 'supreme-modules-for-divi' ),

			),
			'fb_hide_cover'    => array(
				'label'            => esc_html__( 'Hide Cover Photo', 'supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'false' => esc_html__( 'Show', 'supreme-modules-for-divi' ),
					'true'  => esc_html__( 'Hide', 'supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'main_content',
				'description'      => esc_html__( 'Hide cover photo in the header.', 'supreme-modules-for-divi' ),
				'default_on_front' => 'false',
			),
			'fb_small_header'  => array(
				'label'            => esc_html__( 'Use Small Header?', 'supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'false' => esc_html__( 'No', 'supreme-modules-for-divi' ),
					'true'  => esc_html__( 'Yes', 'supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'main_content',
				'description'      => esc_html__( 'Use the small header instead.', 'supreme-modules-for-divi' ),
				'default_on_front' => 'false',
			),
			'fb_show_facepile' => array(
				'label'            => esc_html__( 'Show Face Pile', 'supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'false' => esc_html__( 'Hide', 'supreme-modules-for-divi' ),
					'true'  => esc_html__( 'Show', 'supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'main_content',
				'description'      => esc_html__( 'Show profile photos when friends like this.', 'supreme-modules-for-divi' ),
				'default_on_front' => 'true',
			),
			'fb_width'         => array(
				'label'            => esc_html__( 'Width', 'supreme-modules-for-divi' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'toggle_slug'      => 'main_content',
				'validate_unit'    => true,
				'default'          => '340px',
				'default_unit'     => 'px',
				'default_on_front' => '340px',
				'allow_empty'      => true,
				'range_settings'   => array(
					'min'  => '180',
					'max'  => '500',
					'step' => '1',
				),
				'description'      => esc_html__( 'The pixel width of the Facebook Feed. Min. is 180 & Max. is 500.', 'supreme-modules-for-divi' ),
			),
			'fb_height'        => array(
				'label'           => esc_html__( 'Height', 'supreme-modules-for-divi' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'default_unit'    => 'px',
				'default'         => '500px',
				'range_settings'  => array(
					'min'  => '300',
					'max'  => '600',
					'step' => '1',
				),
			),
			'fb_alignment'     => array(
				'label'           => esc_html__( 'Alignment', 'supreme-modules-for-divi' ),
				'type'            => 'text_align',
				'option_category' => 'configuration',
				'options'         => et_builder_get_text_orientation_options( array( 'justified' ) ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'alignment',
				'description'     => esc_html__( 'Here you can define the alignment of Facebook Feed', 'supreme-modules-for-divi' ),
				'default'         => 'center',
			),
		);
	}

	public function render( $attrs, $content, $render_slug ) {
		$fb_app_id        = $this->props['fb_app_id'];
		$fb_page_url      = $this->props['fb_page_url'];
		$fb_hide_cover    = $this->props['fb_hide_cover'];
		$fb_tabs          = $this->props['fb_tabs'];
		$fb_small_header  = $this->props['fb_small_header'];
		$fb_show_facepile = $this->props['fb_show_facepile'];
		$fb_width         = floatval( $this->props['fb_width'] );
		$fb_height        = floatval( $this->props['fb_height'] );
		$fb_alignment     = $this->props['fb_alignment'];

		$this->add_classname(
			array(
				"et_pb_text_align_{$fb_alignment}",
			)
		);

		if ( ! empty( $fb_tabs ) ) {
			$value_map = array( 'timeline', 'events', 'messages' );
			$fb_tabs   = $this->process_multiple_checkboxes_field_value( $value_map, $fb_tabs );
			$fb_tabs   = str_replace( '|', ',', $fb_tabs );
		} else {
			$fb_tabs = '';
		}

		wp_enqueue_script( 'dsm-facebook' );

		// Render module content
		$output = sprintf(
			'<div class="dsm-facebook-feed">
				<div id="fb-root"></div>
				<div class="fb-page" data-href="%1$s" data-tabs="%7$s" data-width="%6$s" data-height="%5$s" data-small-header="%4$s" data-adapt-container-width="true" data-hide-cover="%2$s" data-show-facepile="%3$s" data-lazy="false">
				</div>
			</div>',
			esc_url( $fb_page_url ),
			esc_attr( $fb_hide_cover ),
			esc_attr( $fb_show_facepile ),
			esc_attr( $fb_small_header ),
			esc_attr( $fb_height ),
			esc_attr( $fb_width ),
			esc_attr( $fb_tabs )
		);

		if ( isset( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) && ! empty( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) && 'on' === get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) {
			if ( isset( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets_compatibility'] ) && ! empty( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) && 'on' === get_option( 'dsm_settings_misc' )['dsm_dynamic_assets_compatibility'] ) {
				wp_enqueue_style( 'dsm-facebook-feed', plugin_dir_url( __DIR__ ) . 'FacebookSimpleFeed/style.css', array(), DSM_VERSION, 'all' );
			} else {
				add_filter( 'et_global_assets_list', array( $this, 'dsm_load_required_divi_assets' ), 10, 3 );
				add_filter( 'et_late_global_assets_list', array( $this, 'dsm_load_required_divi_assets' ), 10, 3 );
			}
		}

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

		// FacebookSimpleFeed.
		if ( ! isset( $assets_list['dsm_facebook_feed'] ) ) {
			$assets_list['dsm_facebook_feed'] = array(
				'css' => plugin_dir_url( __DIR__ ) . 'FacebookSimpleFeed/style.css',
			);
		}

		return $assets_list;
	}
}

new DSM_FacebookSimpleFeed();
