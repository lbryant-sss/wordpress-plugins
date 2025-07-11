<?php
/*
Widget Name: Tabs
Description: Create tabbed content panels with customizable titles, content, initial tab, and design settings.
Author: SiteOrigin
Author URI: https://siteorigin.com
Documentation: https://siteorigin.com/widgets-bundle/tabs-widget/
Keywords: collapsible, content, expandable, faq, panel, section, tabbed, title, toggle, show/hide
*/

class SiteOrigin_Widget_Tabs_Widget extends SiteOrigin_Widget {
	public function __construct() {
		parent::__construct(
			'sow-tabs',
			__( 'SiteOrigin Tabs', 'so-widgets-bundle' ),
			array(
				'description' => __( 'Create tabbed content panels with customizable titles, content, initial tab, and design settings.', 'so-widgets-bundle' ),
				'help' => 'https://siteorigin.com/widgets-bundle/tabs-widget/',
			),
			array(),
			false,
			plugin_dir_path( __FILE__ )
		);
	}

	/**
	 * Initialize the Tabs Widget.
	 */
	public function initialize() {
		$this->register_frontend_scripts(
			array(
				array(
					'sow-tabs',
					plugin_dir_url( __FILE__ ) . 'js/tabs' . SOW_BUNDLE_JS_SUFFIX . '.js',
					array( 'jquery' ),
					SOW_BUNDLE_VERSION,
				),
			)
		);

		add_action( 'siteorigin_widgets_enqueue_frontend_scripts_sow-tabs', array( $this, 'enqueue_widget_scripts' ) );
	}

	public function get_settings_form() {
		return array(
			'scrollto_after_change' => array(
				'type'        => 'checkbox',
				'label'       => __( 'Scroll top', 'so-widgets-bundle' ),
				'default'     => true,
				'description' => __( 'When opening a tab, scroll the user to the top of the tab.', 'so-widgets-bundle' ),
			),
		);
	}

	public function enqueue_widget_scripts() {
		$global_settings = $this->get_global_settings();
		wp_localize_script(
			'sow-tabs',
			'sowTabs',
			array(
				'scrollto_after_change' => ! empty( $global_settings['scrollto_after_change'] ),
				'scrollto_offset' => (int) apply_filters( 'siteorigin_widgets_tabs_scrollto_offset', 90 ),
				'always_scroll' => (bool) apply_filters( 'siteorigin_widgets_tabs_always_scroll', false ),
			)
		);
	}

	public function get_widget_form() {
		return array(
			'title' => array(
				'type' => 'text',
				'label' => __( 'Title', 'so-widgets-bundle' ),
			),
			'tabs' => array(
				'type' => 'repeater',
				'label' => __( 'Tabs', 'so-widgets-bundle' ),
				'item_label' => array(
					'selector' => "[id*='tabs-title']",
					'update_event' => 'change',
					'value_method' => 'val',
				),
				'fields' => array(
					'title' => array(
						'type' => 'text',
						'label' => __( 'Title', 'so-widgets-bundle' ),
					),
					'content_text' => array(
						'type'  => 'tinymce',
						'label' => __( 'Content', 'so-widgets-bundle' ),
					),
				),
			),
			'initial_tab_position' => array(
				'type' => 'number',
				'label' => __( 'Initially selected tab', 'so-widgets-bundle' ),
				'default' => 1,
				'description' => __( 'The position of the tab to be selected when the page first loads.', 'so-widgets-bundle' ),
			),
			'design' => array(
				'type' =>  'section',
				'label' => __( 'Design', 'so-widgets-bundle' ),
				'hide' => true,
				'fields' => array(
					'tabs_container' => array(
						'type' => 'section',
						'label' => __( 'Tabs container', 'so-widgets-bundle' ),
						'hide' => true,
						'fields' => array(
							'background_color' => array(
								'type' => 'color',
								'label' => __( 'Background color', 'so-widgets-bundle' ),
								'default' => '#757575',
							),
							'border_color' => array(
								'type' => 'color',
								'label' => __( 'Border color', 'so-widgets-bundle' ),
							),
							'border_width' => array(
								'type' => 'measurement',
								'label' => __( 'Border width', 'so-widgets-bundle' ),
							),
						),
					),
					'tabs' => array(
						'type' => 'section',
						'label' => __( 'Tabs', 'so-widgets-bundle' ),
						'hide' => true,
						'fields' => array(
							'background_color' => array(
								'type' => 'color',
								'label' => __( 'Background color', 'so-widgets-bundle' ),
							),
							'background_hover_color' => array(
								'type' => 'color',
								'label' => __( 'Background hover color', 'so-widgets-bundle' ),
								'default' => '#f9f9f9',
							),
							'title_color' => array(
								'type' => 'color',
								'label' => __( 'Title color', 'so-widgets-bundle' ),
								'default' => '#fff',
							),
							'title_hover_color' => array(
								'type' => 'color',
								'label' => __( 'Title hover color', 'so-widgets-bundle' ),
								'default' => '#2d2d2d',
							),
							'border_color' => array(
								'type' => 'color',
								'label' => __( 'Border color', 'so-widgets-bundle' ),
								'default' => '#828282',
							),
							'border_hover_color' => array(
								'type' => 'color',
								'label' => __( 'Border hover color', 'so-widgets-bundle' ),
								'default' => '#f9f9f9',
							),
							'border_width' => array(
								'type' => 'measurement',
								'label' => __( 'Border width', 'so-widgets-bundle' ),
							),
							'border_hover_width' => array(
								'type' => 'measurement',
								'label' => __( 'Border hover width', 'so-widgets-bundle' ),
							),
						),
					),
					'panels' => array(
						'type' => 'section',
						'label' => __( 'Panels', 'so-widgets-bundle' ),
						'hide' => true,
						'fields' => array(
							'background_color' => array(
								'type' => 'color',
								'label' => __( 'Background color', 'so-widgets-bundle' ),
								'default' => '#f9f9f9',
							),
							'font_color' => array(
								'type' => 'color',
								'label' => __( 'Font color', 'so-widgets-bundle' ),
							),
							'border_color' => array(
								'type' => 'color',
								'label' => __( 'Border color', 'so-widgets-bundle' ),
							),
							'border_width' => array(
								'type' => 'measurement',
								'label' => __( 'Border width', 'so-widgets-bundle' ),
							),
						),
					),
				),
			),
		);
	}

	public function get_less_variables( $instance ) {
		if ( empty( $instance ) || empty( $instance['design'] ) ) {
			return array();
		}

		$design = $instance['design'];

		return array(
			'tabs_container_background_color' => $design['tabs_container']['background_color'],
			'tabs_container_border_color' => $design['tabs_container']['border_color'],
			'tabs_container_border_width' => $design['tabs_container']['border_width'],
			'has_tabs_container_border_width' => empty( $design['tabs_container']['border_width'] ) ? 'false' : 'true',
			'tabs_background_color' => $design['tabs']['background_color'],
			'tabs_background_hover_color' => $design['tabs']['background_hover_color'],
			'tabs_title_color' => $design['tabs']['title_color'],
			'tabs_title_hover_color' => $design['tabs']['title_hover_color'],
			'tabs_border_color' => $design['tabs']['border_color'],
			'tabs_border_hover_color' => $design['tabs']['border_hover_color'],
			'tabs_border_width' => $design['tabs']['border_width'],
			'has_tabs_border_width' => empty( $design['tabs']['border_width'] ) ? 'false' : 'true',
			'tabs_border_hover_width' => $design['tabs']['border_hover_width'],
			'has_tabs_border_hover_width' => empty( $design['tabs']['border_hover_width'] ) ? 'false' : 'true',
			'panels_background_color' => $design['panels']['background_color'],
			'panels_font_color' => $design['panels']['font_color'],
			'panels_border_color' => $design['panels']['border_color'],
			'panels_border_width' => $design['panels']['border_width'],
			'has_panels_border_width' => empty( $design['panels']['border_width'] ) ? 'false' : 'true',
		);
	}

	public function get_template_variables( $instance, $args ) {
		if ( empty( $instance ) ) {
			return array();
		}

		$tabs = empty( $instance['tabs'] ) ? array() : $instance['tabs'];

		foreach ( $tabs as $i => &$tab ) {
			if ( empty( $tab['before_title'] ) ) {
				$tab['before_title'] = '';
			}

			if ( empty( $tab['after_title'] ) ) {
				$tab['after_title'] = '';
			}

			if ( empty( $tab['title'] ) ) {
				$id = $this->id_base;

				if ( ! empty( $instance['_sow_form_id'] ) ) {
					$id .= '-' . $instance['_sow_form_id'];
				} elseif ( ! empty( $args['widget_id'] ) ) {
					$id .= '-' . $args['widget_id'];
				}
				$tab['anchor'] = $id . '-' . $i;
			} else {
				$tab['anchor'] = $tab['title'];
			}
		}

		if ( empty( $instance['initial_tab_position'] ) ||
			 $instance['initial_tab_position'] < 1 ||
			 $instance['initial_tab_position'] > count( $tabs ) ) {
			$init_tab_index = 0;
		} else {
			$init_tab_index = $instance['initial_tab_position'] - 1;
		}

		return array(
			'tabs' => $tabs,
			'initial_tab_index' => $init_tab_index,
		);
	}

	public function render_panel_content( $panel, $instance ) {
		$content = wp_kses_post( $panel['content_text'] );

		echo apply_filters( 'siteorigin_widgets_tabs_render_panel_content', $content, $panel, $instance );
	}

	public function get_form_teaser() {
		if ( class_exists( 'SiteOrigin_Premium' ) ) {
			return false;
		}

		return array(
			sprintf(
				__( 'Get more customization options and the ability to use widgets and layouts as your tabs content with %sSiteOrigin Premium%s', 'so-widgets-bundle' ),
				'<a href="https://siteorigin.com/downloads/premium/?featured_addon=plugin/tabs" target="_blank">',
				'</a>'
			),
			sprintf(
				__( 'Use Google Fonts right inside the Tabs Widget with %sSiteOrigin Premium%s', 'so-widgets-bundle' ),
				'<a href="https://siteorigin.com/downloads/premium/?featured_addon=plugin/web-font-selector" target="_blank" rel="noopener noreferrer">',
				'</a>'
			),
		);
	}
}

siteorigin_widget_register( 'sow-tabs', __FILE__, 'SiteOrigin_Widget_Tabs_Widget' );
