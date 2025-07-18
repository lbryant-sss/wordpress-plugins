<?php
/*
Widget Name: Blog
Description: Showcase blog content in personalized list or grid layouts with flexible design and display settings.
Author: SiteOrigin
Author URI: https://siteorigin.com
Documentation: https://siteorigin.com/widgets-bundle/blog-widget/
Keywords: list, post, query
*/

class SiteOrigin_Widget_Blog_Widget extends SiteOrigin_Widget {
	public function __construct() {
		parent::__construct(
			'sow-blog',
			__( 'SiteOrigin Blog', 'so-widgets-bundle' ),
			array(
				'description' => __( 'Showcase blog content in personalized list or grid layouts with flexible design and display settings.', 'so-widgets-bundle' ),
				'help' => 'https://siteorigin.com/widgets-bundle/blog-widget/',
				'instance_storage' => true,
				'panels_title' => false,
			),
			array(),
			false,
			plugin_dir_path( __FILE__ )
		);
	}

	public function initialize() {
		add_action( 'wp_loaded', array( $this, 'register_image_sizes' ) );
		$this->register_frontend_styles(
			array(
				array(
					'sow-blog',
					plugin_dir_url( __FILE__ ) . 'css/style.css',
				),
			)
		);
		$this->register_frontend_scripts(
			array(
				array(
					'sow-blog',
					plugin_dir_url( __FILE__ ) . 'js/blog' . SOW_BUNDLE_JS_SUFFIX . '.js',
					array( 'jquery' ),
					SOW_BUNDLE_VERSION
				),
			)
		);

		add_action( 'siteorigin_widgets_enqueue_frontend_scripts_sow-blog', array( $this, 'localize_scrollto' ), 10, 2 );

		add_action( 'wp_enqueue_scripts', array( $this, 'register_template_assets' ) );
		add_filter( 'siteorigin_widgets_blog_query', array( $this, 'portfolio_filter_posts' ), 8, 2 );
	}

	public function register_image_sizes() {
		$image_sizes = apply_filters( 'siteorigin_widgets_blog_image_sizes', array(
			'portfolio' => array(
				375,
				375,
			),
			'grid' => array(
				720,
				480,
			),
			'alternate' => array(
				950,
				630,
			),
		) );

		foreach ( $image_sizes as $k => $size ) {
			add_image_size( 'sow-blog-' . $k, (int) $size[0], (int) $size[1], true );
		}
	}

	public function get_widget_form() {
		$templates = apply_filters( 'siteorigin_widgets_blog_templates', json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'data/templates.json' ), true ) );

		return $this->dynamic_preset_state_handler(
			'active_template',
			$templates,
			array(
				'title' => array(
					'type' => 'text',
					'label' => __( 'Title', 'so-widgets-bundle' ),
				),
				'template' => array(
					'type' => 'presets',
					'label' => __( 'Template', 'so-widgets-bundle' ),
					'default_preset' => 'standard',
					'options' => $templates,
					'state_emitter' => array(
						'callback' => 'select',
						'args' => array( 'active_template' ),
					),
				),
				'settings' => array(
					'type' => 'section',
					'label' => __( 'Settings', 'so-widgets-bundle' ),
					'hide' => true,
					'fields' => array(
						'columns' => array(
							'type' => 'number',
							'label' => __( 'Column Count', 'so-widgets-bundle' ),
						),
						'featured_image' => array(
							'type' => 'checkbox',
							'label' => __( 'Featured Image', 'so-widgets-bundle' ),
							'default' => true,
							'state_emitter' => array(
								'callback' => 'conditional',
								'args' => array(
									'featured_image[show]: val',
									'featured_image[hide]: ! val',
								),
							),
						),
						'featured_image_empty' => array(
							'type' => 'checkbox',
							'label' => __( 'Skip Post if No Featured Image', 'so-widgets-bundle' ),
							'default' => true,
							'state_handler' => array(
								'active_template[portfolio]' => array( 'slideDown' ),
								'_else[active_template]' => array( 'slideUp' ),
							),
						),
						'featured_image_size' => array(
							'type' => 'image-size',
							'label' => __( 'Featured Image Size', 'so-widgets-bundle' ),
							'custom_size' => true,
							'state_handler' => array(
								'featured_image[show]' => array( 'show' ),
								'featured_image[hide]' => array( 'hide' ),
							),
						),
						'tag' => array(
							'type' => 'select',
							'label' => __( 'Post Title HTML Tag', 'so-widgets-bundle' ),
							'default' => 'h2',
							'options' => array(
								'h1' => __( 'H1', 'so-widgets-bundle' ),
								'h2' => __( 'H2', 'so-widgets-bundle' ),
								'h3' => __( 'H3', 'so-widgets-bundle' ),
								'h4' => __( 'H4', 'so-widgets-bundle' ),
								'h5' => __( 'H5', 'so-widgets-bundle' ),
								'h6' => __( 'H6', 'so-widgets-bundle' ),
								'p' => __( 'Paragraph', 'so-widgets-bundle' ),
							)
						),
						'content' => array(
							'type' => 'select',
							'label' => __( 'Post Content ', 'so-widgets-bundle' ),
							'description' => __( 'Choose how to display your post content. Select Full Post Content if using the "more" quicktag.', 'so-widgets-bundle' ),
							'default' => 'full',
							'options' => array(
								'excerpt' => __( 'Post Excerpt', 'so-widgets-bundle' ),
								'full' => __( 'Full Post Content', 'so-widgets-bundle' ),
							),
							'state_emitter' => array(
								'callback' => 'select',
								'args' => array( 'content_type' ),
							),
							'state_handler' => array(
								'active_template[standard,masonry,grid,offset,alternate]' => array( 'slideDown' ),
								'_else[active_template]' => array( 'slideUp' ),
							),
						),
						'trim_manual_excerpt' => array(
							'type' => 'checkbox',
							'label' => __( 'Trim Manual Excerpt', 'so-widgets-bundle' ),
							'description' => __( 'Trim the excerpt length even if a manual excerpt has been added to the post.', 'so-widgets-bundle' ),
							'state_handler' => array(
								'content_type[excerpt]' => array( 'show' ),
								'_else[content_type]' => array( 'hide' ),
							),
						),
						'read_more' => array(
							'type' => 'checkbox',
							'label' => __( 'Post Excerpt Read More Link', 'so-widgets-bundle' ),
							'description' => __( 'Display the Read More link below the post excerpt.', 'so-widgets-bundle' ),
							'state_handler' => array(
								'content_type[excerpt]' => array( 'show' ),
								'_else[content_type]' => array( 'hide' ),
							),
						),
						'excerpt_length' => array(
							'type' => 'number',
							'label' => __( 'Excerpt Length', 'so-widgets-bundle' ),
							'default' => 55,
							'state_handler' => array(
								'content_type[excerpt]' => array( 'show' ),
								'_else[content_type]' => array( 'hide' ),
							),
						),
						'date' => array(
							'type' => 'checkbox',
							'label' => __( 'Post Date', 'so-widgets-bundle' ),
							'default' => true,
						),
						'author' => array(
							'type' => 'checkbox',
							'label' => __( 'Post Author', 'so-widgets-bundle' ),
							'default' => true,
						),
						'filter_categories' => array(
							'type' => 'checkbox',
							'label' => __( 'Filter Categories ', 'so-widgets-bundle' ),
							'state_emitter' => array(
								'callback' => 'conditional',
								'args' => array(
									'filter_categories[show]: val',
									'filter_categories[hide]: ! val',
								),
							),
						),
						'categories' => array(
							'type' => 'checkbox',
							'label' => __( 'Post Categories', 'so-widgets-bundle' ),
							'default' => true,
						),
						'tags' => array(
							'type' => 'checkbox',
							'label' => __( 'Post Tags', 'so-widgets-bundle' ),
							'default' => false,
						),
						'comment_count' => array(
							'type' => 'checkbox',
							'label' => __( 'Post Comment Count', 'so-widgets-bundle' ),
							'default' => true,
						),
					),
				),

				'design' => array(
					'type' => 'section',
					'label' => __( 'Design', 'so-widgets-bundle' ),
					'hide' => true,
					'fields' => array(
						'post' => array(
							'type' => 'section',
							'label' => __( 'Post', 'so-widgets-bundle' ),
							'hide' => true,
							'fields' => array(
								'border' => array(
									'type' => 'color',
									'label' => __( 'Border Color', 'so-widgets-bundle' ),
									'default' => '#e6e6e6',
								),
								'background' => array(
									'type' => 'color',
									'label' => __( 'Background Color', 'so-widgets-bundle' ),
									'default' => '#fff',
								),
							),
						),
						'title' => array(
							'type' => 'section',
							'label' => __( 'Post Title', 'so-widgets-bundle' ),
							'hide' => true,
							'fields' => array(
								'font' => array(
									'type' => 'font',
									'label' => __( 'Font', 'so-widgets-bundle' ),
								),
								'font_size' => array(
									'type' => 'measurement',
									'label' => __( 'Font Size', 'so-widgets-bundle' ),
									'default' => '24px',
								),
								'color' => array(
									'type' => 'color',
									'label' => __( 'Color', 'so-widgets-bundle' ),
									'default' => '#2d2d2d',
								),
								'color_hover' => array(
									'type' => 'color',
									'label' => __( 'Hover Color', 'so-widgets-bundle' ),
									'default' => '#626262',
								),
							),
						),

						'meta' => array(
							'type' => 'section',
							'label' => __( 'Post Meta', 'so-widgets-bundle' ),
							'hide' => true,
							'fields' => array(
								'font' => array(
									'type' => 'font',
									'label' => __( 'Font', 'so-widgets-bundle' ),
								),
								'font_size' => array(
									'type' => 'measurement',
									'label' => __( 'Font Size', 'so-widgets-bundle' ),
									'default' => '13px',
								),
								'color' => array(
									'type' => 'color',
									'label' => __( 'Color', 'so-widgets-bundle' ),
									'default' => '#929292',
								),
								'color_hover' => array(
									'type' => 'color',
									'label' => __( 'Hover Color', 'so-widgets-bundle' ),
									'default' => '#f14e4e',
								),
							),
						),

						'offset_post_meta' => array(
							'type' => 'section',
							'label' => __( 'Offset Post Meta', 'so-widgets-bundle' ),
							'hide' => true,
							'fields' => array(
								'font' => array(
									'type' => 'font',
									'label' => __( 'Font', 'so-widgets-bundle' ),
								),
								'font_size' => array(
									'type' => 'measurement',
									'label' => __( 'Font Size', 'so-widgets-bundle' ),
									'default' => '13px',
								),
								'color' => array(
									'type' => 'color',
									'label' => __( 'Color', 'so-widgets-bundle' ),
									'default' => '#929292',
								),
								'link_color' => array(
									'type' => 'color',
									'label' => __( 'Link Color', 'so-widgets-bundle' ),
									'default' => '#2d2d2d',
								),
								'link_color_hover' => array(
									'type' => 'color',
									'label' => __( 'Link Color Hover', 'so-widgets-bundle' ),
									'default' => '#f14e4e',
								),
								'link_font_size' => array(
									'type' => 'measurement',
									'label' => __( 'Link Font Size', 'so-widgets-bundle' ),
									'default' => '14px',
								),
							),
						),

						'overlay_post_category' => array(
							'type' => 'section',
							'label' => __( 'Overlay Post Category', 'so-widgets-bundle' ),
							'hide' => true,
							'fields' => array(
								'font' => array(
									'type' => 'font',
									'label' => __( 'Font', 'so-widgets-bundle' ),
								),
								'font_size' => array(
									'type' => 'measurement',
									'label' => __( 'Font Size', 'so-widgets-bundle' ),
									'default' => '11px',
								),
								'color' => array(
									'type' => 'color',
									'label' => __( 'Color', 'so-widgets-bundle' ),
									'default' => '#fff',
								),
								'color_hover' => array(
									'type' => 'color',
									'label' => __( 'Hover Color', 'so-widgets-bundle' ),
									'default' => '#fff',
								),
								'background' => array(
									'type' => 'color',
									'label' => __( 'Background', 'so-widgets-bundle' ),
									'default' => 'rgba(0,0,0,0.7)',
									'alpha' => true,
								),
								'background_hover' => array(
									'type' => 'color',
									'label' => __( 'Hover Background', 'so-widgets-bundle' ),
									'default' => 'rgba(0,0,0,0.75)',
									'alpha' => true,
								),
							),
						),

						'content' => array(
							'type' => 'section',
							'label' => __( 'Post Content', 'so-widgets-bundle' ),
							'hide' => true,
							'fields' => array(
								'font' => array(
									'type' => 'font',
									'label' => __( 'Font', 'so-widgets-bundle' ),
								),
								'font_size' => array(
									'type' => 'measurement',
									'label' => __( 'Font Size', 'so-widgets-bundle' ),
									'default' => '15px',
								),
								'color' => array(
									'type' => 'color',
									'label' => __( 'Color', 'so-widgets-bundle' ),
									'default' => '#626262',
								),
								'link_color' => array(
									'type' => 'color',
									'label' => __( 'Link Color', 'so-widgets-bundle' ),
									'default' => '#f14e4e',
								),
								'link_color_hover' => array(
									'type' => 'color',
									'label' => __( 'Link Hover Color', 'so-widgets-bundle' ),
									'default' => '#626262',
								),
							),
						),

						'filter_categories' => array(
							'type' => 'section',
							'label' => __( 'Filter Categories', 'so-widgets-bundle' ),
							'hide' => true,
							'fields' => array(
								'font' => array(
									'type' => 'font',
									'label' => __( 'Font', 'so-widgets-bundle' ),
									'state_handler' => array(
										'filter_categories[show]' => array( 'show' ),
										'filter_categories[hide]' => array( 'hide' ),
									),
								),
								'font_size' => array(
									'type' => 'measurement',
									'label' => __( 'Font Size', 'so-widgets-bundle' ),
									'default' => '11px',
									'state_handler' => array(
										'filter_categories[show]' => array( 'show' ),
										'filter_categories[hide]' => array( 'hide' ),
									),
								),
								'text_transform' => array(
									'type' => 'checkbox',
									'label' => __( 'Capitalize Categories', 'so-widgets-bundle' ),
									'default' => true,
									'state_handler' => array(
										'filter_categories[show]' => array( 'show' ),
										'filter_categories[hide]' => array( 'hide' ),
									),
								),
								'color' => array(
									'type' => 'color',
									'label' => __( 'Color', 'so-widgets-bundle' ),
									'default' => '#929292',
									'state_handler' => array(
										'filter_categories[show]' => array( 'show' ),
										'filter_categories[hide]' => array( 'hide' ),
									),
								),
								'color_hover' => array(
									'type' => 'color',
									'label' => __( 'Hover Color', 'so-widgets-bundle' ),
									'default' => '#2d2d2d',
									'state_handler' => array(
										'filter_categories[show]' => array( 'show' ),
										'filter_categories[hide]' => array( 'hide' ),
									),
								),
								'selected_border_color' => array(
									'type' => 'color',
									'label' => __( 'Selected Border Color', 'so-widgets-bundle' ),
									'default' => '#2d2d2d',
									'state_handler' => array(
										'filter_categories[show]' => array( 'show' ),
										'filter_categories[hide]' => array( 'hide' ),
									),
								),
								'selected_border_thickness' => array(
									'type' => 'measurement',
									'label' => __( 'Selected Border Thickness', 'so-widgets-bundle' ),
									'default' => '2px',
									'state_handler' => array(
										'filter_categories[show]' => array( 'show' ),
										'filter_categories[hide]' => array( 'hide' ),
									),
								),
							),
						),

						'featured_image' => array(
							'type' => 'section',
							'label' => __( 'Featured Image', 'so-widgets-bundle' ),
							'hide' => true,
							'fields' => array(
								'border_color' => array(
									'type' => 'color',
									'label' => __( 'Border Color', 'so-widgets-bundle' ),
									'default' => '#929292',
								),
								'hover_overlay_color' => array(
									'type' => 'color',
									'label' => __( 'Hover Overlay Color', 'so-widgets-bundle' ),
									'default' => 'rgba(255,255,255,0.9)',
									'alpha' => true,
								),
								'post_title_font' => array(
									'type' => 'font',
									'label' => __( 'Post Title Font', 'so-widgets-bundle' ),
								),
								'post_title_font_size' => array(
									'type' => 'measurement',
									'label' => __( 'Post Title Font Size', 'so-widgets-bundle' ),
									'default' => '15px',
								),
								'post_title_color' => array(
									'type' => 'color',
									'label' => __( 'Post Title Color', 'so-widgets-bundle' ),
									'default' => '#2d2d2d',
								),
								'divider_border_color' => array(
									'type' => 'color',
									'label' => __( 'Divider Border Color', 'so-widgets-bundle' ),
									'default' => '#2d2d2d',
								),
								'divider_border_thickness' => array(
									'type' => 'measurement',
									'label' => __( 'Divider Border Thickness', 'so-widgets-bundle' ),
									'default' => '1px',
								),
								'divider_border_margin' => array(
									'type' => 'measurement',
									'label' => __( 'Divider Border Margin', 'so-widgets-bundle' ),
									'default' => '13px',
								),
								'post_meta_font' => array(
									'type' => 'font',
									'label' => __( 'Post Meta Font', 'so-widgets-bundle' ),
								),
								'post_meta_font_size' => array(
									'type' => 'measurement',
									'label' => __( 'Post Meta Font Size', 'so-widgets-bundle' ),
									'default' => '11px',
								),
								'post_meta_color' => array(
									'type' => 'color',
									'label' => __( 'Post Meta Color', 'so-widgets-bundle' ),
									'default' => '#929292',
								),
							),
						),

						'pagination' => array(
							'type' => 'section',
							'label' => __( 'Pagination', 'so-widgets-bundle' ),
							'hide' => true,
							'fields' => array(
								'top_margin' => array(
									'type' => 'measurement',
									'label' => __( 'Top Margin', 'so-widgets-bundle' ),
									'default' => '30px',
								),
								'link_margin' => array(
									'type' => 'measurement',
									'label' => __( 'Link Margin', 'so-widgets-bundle' ),
									'default' => '8px',
								),
								'border_color' => array(
									'type' => 'color',
									'label' => __( 'Border Color', 'so-widgets-bundle' ),
									'default' => '#626262',
								),
								'border_color_hover' => array(
									'type' => 'color',
									'label' => __( 'Border Hover Color', 'so-widgets-bundle' ),
									'default' => '#f14e4e',
								),
								'background' => array(
									'type' => 'color',
									'label' => __( 'Background', 'so-widgets-bundle' ),
								),
								'background_hover' => array(
									'type' => 'color',
									'label' => __( 'Hover Background', 'so-widgets-bundle' ),
								),
								'border_radius' => array(
									'type' => 'slider',
									'label' => __( 'Border Radius', 'so-widgets-bundle' ),
									'max' => 50,
									'min' => 0,
									'step' => 1,
								),
								'font' => array(
									'type' => 'font',
									'label' => __( 'Font', 'so-widgets-bundle' ),
								),
								'font_size' => array(
									'type' => 'measurement',
									'label' => __( 'Font Size', 'so-widgets-bundle' ),
									'default' => '14px',
								),
								'link_color' => array(
									'type' => 'color',
									'label' => __( 'Link Color', 'so-widgets-bundle' ),
									'default' => '#626262',
								),
								'link_color_hover' => array(
									'type' => 'color',
									'label' => __( 'Link Hover Color', 'so-widgets-bundle' ),
									'default' => '#f14e4e',
								),
								'dots_color' => array(
									'type' => 'color',
									'label' => __( 'Dots Color', 'so-widgets-bundle' ),
									'default' => '#626262',
								),
								'width' => array(
									'type' => 'measurement',
									'label' => __( 'Width', 'so-widgets-bundle' ),
									'units' => array( 'px', 'vh', 'vw', 'vmin', 'vmax' ),
									'default' => '40px',
								),
								'height' => array(
									'type' => 'measurement',
									'label' => __( 'Height', 'so-widgets-bundle' ),
									'units' => array( 'px', 'vh', 'vw', 'vmin', 'vmax' ),
									'default' => '43px',
								),
							),
						),
					),
				),

				'posts' => array(
					'type' => 'posts',
					'label' => __( 'Posts Query', 'so-widgets-bundle' ),
					'hide' => true,
				),
			)
		);
	}

	public function get_settings_form() {
		return array(
			'responsive_breakpoint' => array(
				'type'        => 'measurement',
				'label'       => __( 'Responsive Breakpoint', 'so-widgets-bundle' ),
				'default'     => '780px',
				'description' => __( 'Device width, in pixels, to collapse into a mobile view.', 'so-widgets-bundle' ),
			),
			'scrollto' => array(
				'type'        => 'checkbox',
				'label'       => __( 'Scroll Top', 'so-widgets-bundle' ),
				'default'     => true,
				'description' => __( 'Scroll the user to the top of the Blog Widget after pagination links are clicked.', 'so-widgets-bundle' ),
			),
		);
	}

	public function register_template_assets() {
		wp_register_script( 'sow-blog-template-masonry', plugin_dir_url( __FILE__ ) . 'js/masonry' . SOW_BUNDLE_JS_SUFFIX . '.js', array( 'jquery', 'jquery-isotope' ) );
		wp_register_script( 'sow-blog-template-portfolio', plugin_dir_url( __FILE__ ) . 'js/portfolio' . SOW_BUNDLE_JS_SUFFIX . '.js', array( 'jquery', 'jquery-isotope' ) );

		wp_register_script( 'jquery-isotope', plugin_dir_url( SOW_BUNDLE_BASE_FILE ) . 'js/lib/isotope.pkgd' . SOW_BUNDLE_JS_SUFFIX . '.js', array( 'jquery' ), '3.0.4', true );

		do_action( 'siteorigin_widgets_blog_template_stylesheets' );
	}

	public function localize_scrollto( $instance, $widget ) {
		$global_settings = $this->get_global_settings();
		wp_localize_script(
			'sow-blog',
			'soBlogWidget',
			array(
				'scrollto' => ! empty( $global_settings['scrollto'] ),
				'scrollto_offset' => ( int ) apply_filters( 'siteorigin_widgets_blog_scrollto_offset', 90 ),
			)
		);

	}

	public function get_template_name( $instance ) {
		return 'base';
	}

	public function get_style_name( $instance ) {
		$template = empty( $instance['template'] ) ? 'standard' : $instance['template'];

		wp_enqueue_style( 'sow-blog-template-' . $template );
		wp_enqueue_script( 'sow-blog-template-' . $template );

		return $template;
	}

	public function get_less_variables( $instance ) {
		if ( empty( $instance ) ) {
			return array();
		}

		$less_vars = array(
			'responsive_breakpoint' => $this->get_global_settings( 'responsive_breakpoint' ),
			'categories' => ! empty( $instance['settings']['categories'] ) ? $instance['settings']['categories'] : false,
			'author' => ! empty( $instance['settings']['author'] ) ? $instance['settings']['author'] : false,
			'columns' => isset( $instance['settings']['columns'] ) && (int) $instance['settings']['columns'] > 0 ? (int) $instance['settings']['columns'] : 1,
		);

		if ( $instance['template'] == 'masonry' ) {
			$less_vars['column_width'] = 100 / $less_vars['columns'] - $less_vars['columns'] * 0.5 . '%';
		} elseif ( $instance['template'] == 'grid' && $less_vars['columns'] > 2 ) {
			$less_vars['column_spacing'] = $less_vars['columns'] * 0.5 . '%';
		}

		if ( $instance['template'] != 'portfolio' ) {
			// Post.
			$less_vars['post_border_color'] = ! empty( $instance['design']['post']['border'] ) ? $instance['design']['post']['border'] : '';
			$less_vars['post_background'] = ! empty( $instance['design']['post']['background'] ) ? $instance['design']['post']['background'] : '';

			// Post Title.
			if ( ! empty( $instance['design']['title']['font'] ) ) {
				$font = siteorigin_widget_get_font( $instance['design']['title']['font'] );
				$less_vars['title_font'] = $font['family'];

				if ( ! empty( $font['weight'] ) ) {
					$less_vars['title_font_style'] = $font['style'];
					$less_vars['title_font_weight'] = $font['weight_raw'];
				}
			}
			$less_vars['title_font_size'] = ! empty( $instance['design']['title']['font_size'] ) ? $instance['design']['title']['font_size'] : '';
			$less_vars['title_color'] = ! empty( $instance['design']['title']['color'] ) ? $instance['design']['title']['color'] : '';
			$less_vars['title_color_hover'] = ! empty( $instance['design']['title']['color_hover'] ) ? $instance['design']['title']['color_hover'] : '';

			// Post Meta.
			if ( ! empty( $instance['design']['meta']['font'] ) ) {
				$font = siteorigin_widget_get_font( $instance['design']['meta']['font'] );
				$less_vars['meta_font'] = $font['family'];

				if ( ! empty( $font['weight'] ) ) {
					$less_vars['meta_font_style'] = $font['style'];
					$less_vars['meta_font_weight'] = $font['weight_raw'];
				}
			}
			$less_vars['meta_font_size'] = ! empty( $instance['design']['meta']['font_size'] ) ? $instance['design']['meta']['font_size'] : '';
			$less_vars['meta_color'] = ! empty( $instance['design']['meta']['color'] ) ? $instance['design']['meta']['color'] : '';
			$less_vars['meta_color_hover'] = ! empty( $instance['design']['meta']['color_hover'] ) ? $instance['design']['meta']['color_hover'] : '';

			if ( $instance['template'] == 'offset' ) {
				// Offset Post Meta.
				if ( ! empty( $instance['design']['offset_post_meta']['font'] ) ) {
					$font = siteorigin_widget_get_font( $instance['design']['offset_post_meta']['font'] );
					$less_vars['offset_post_meta_font'] = $font['family'];

					if ( ! empty( $font['weight'] ) ) {
						$less_vars['offset_post_meta_font_style'] = $font['style'];
						$less_vars['offset_post_meta_font_weight'] = $font['weight_raw'];
					}
				}
				$less_vars['offset_post_meta_font_size'] = ! empty( $instance['design']['offset_post_meta']['font_size'] ) ? $instance['design']['offset_post_meta']['font_size'] : '';
				$less_vars['offset_post_meta_color'] = ! empty( $instance['design']['offset_post_meta']['color'] ) ? $instance['design']['offset_post_meta']['color'] : '';
				$less_vars['offset_post_meta_link_color'] = ! empty( $instance['design']['offset_post_meta']['link_color'] ) ? $instance['design']['offset_post_meta']['link_color'] : '';
				$less_vars['offset_post_meta_link_color_hover'] = ! empty( $instance['design']['offset_post_meta']['link_color_hover'] ) ? $instance['design']['offset_post_meta']['link_color_hover'] : '';
				$less_vars['offset_post_meta_link_font_size'] = ! empty( $instance['design']['offset_post_meta']['link_font_size'] ) ? $instance['design']['offset_post_meta']['link_font_size'] : '';
			}

			// Content.
			if ( ! empty( $instance['design']['content']['font'] ) ) {
				$font = siteorigin_widget_get_font( $instance['design']['content']['font'] );
				$less_vars['content_font'] = $font['family'];

				if ( ! empty( $font['weight'] ) ) {
					$less_vars['content_font_style'] = $font['style'];
					$less_vars['content_font_weight'] = $font['weight_raw'];
				}
			}
			$less_vars['content_font_size'] = ! empty( $instance['design']['content']['font_size'] ) ? $instance['design']['content']['font_size'] : '';
			$less_vars['content_color'] = ! empty( $instance['design']['content']['color'] ) ? $instance['design']['content']['color'] : '';
			$less_vars['content_link'] = ! empty( $instance['design']['content']['link_color'] ) ? $instance['design']['content']['link_color'] : '';
			$less_vars['content_link_hover'] = ! empty( $instance['design']['content']['link_color_hover'] ) ? $instance['design']['content']['link_color_hover'] : '';
		} else {
			global $_wp_additional_image_sizes;
			if ( ! empty( $instance['settings']['featured_image_size'] ) ) {
				$less_vars['image_size'] = $instance['settings']['featured_image_size'] == 'custom_size' ? $instance['settings']['featured_image_size_width'] : $instance['settings']['featured_image_size'];
				if ( isset( $_wp_additional_image_sizes[ $less_vars['image_size'] ] ) ) {
					$less_vars['image_size'] = $_wp_additional_image_sizes[ $less_vars['image_size'] ]['width'] . 'px';
				}
			}

			$less_vars['column_width'] = number_format( 98.8333 / $less_vars['columns'], 2 ) . '%';

			if ( empty( $less_vars['categories'] ) && ! empty( $instance['settings']['filter_categories'] ) ) {
				$less_vars['categories'] = 1;
			}
		}

		// Pagination.
		$less_vars['pagination_top_margin'] = ! empty( $instance['design']['pagination']['top_margin'] ) ? $instance['design']['pagination']['top_margin'] : '';
		$less_vars['pagination_link_margin'] = ! empty( $instance['design']['pagination']['link_margin'] ) ? $instance['design']['pagination']['link_margin'] : '';
		$less_vars['pagination_link_margin_offset'] = ! empty( $instance['design']['pagination']['link_margin'] ) ? '-' . $instance['design']['pagination']['link_margin'] : '';
		$less_vars['pagination_border_color'] = ! empty( $instance['design']['pagination']['border_color'] ) ? $instance['design']['pagination']['border_color'] : '';
		$less_vars['pagination_border_color_hover'] = ! empty( $instance['design']['pagination']['border_color_hover'] ) ? $instance['design']['pagination']['border_color_hover'] : '';
		$less_vars['pagination_background'] = ! empty( $instance['design']['pagination']['background'] ) ? $instance['design']['pagination']['background'] : '';
		$less_vars['pagination_background_hover'] = ! empty( $instance['design']['pagination']['background_hover'] ) ? $instance['design']['pagination']['background_hover'] : '';
		$less_vars['pagination_border_radius'] = ! empty( $instance['design']['pagination']['border_radius'] ) ? $instance['design']['pagination']['border_radius'] . 'px' : '';

		if ( ! empty( $instance['design']['pagination']['font'] ) ) {
			$font = siteorigin_widget_get_font( $instance['design']['pagination']['font'] );
			$less_vars['pagination_font'] = $font['family'];

			if ( ! empty( $font['weight'] ) ) {
				$less_vars['pagination_font_style'] = $font['style'];
				$less_vars['pagination_font_weight'] = $font['weight_raw'];
			}
		}
		$less_vars['pagination_font_size'] = ! empty( $instance['design']['pagination']['font_size'] ) ? $instance['design']['pagination']['font_size'] : '';
		$less_vars['pagination_link_color'] = ! empty( $instance['design']['pagination']['link_color'] ) ? $instance['design']['pagination']['link_color'] : '';
		$less_vars['pagination_link_color_hover'] = ! empty( $instance['design']['pagination']['link_color_hover'] ) ? $instance['design']['pagination']['link_color_hover'] : '';
		$less_vars['pagination_dots_color'] = ! empty( $instance['design']['pagination']['dots_color'] ) ? $instance['design']['pagination']['dots_color'] : '';
		$less_vars['pagination_width'] = ! empty( $instance['design']['pagination']['width'] ) ? $instance['design']['pagination']['width'] : '';
		$less_vars['pagination_height'] = ! empty( $instance['design']['pagination']['height'] ) ? $instance['design']['pagination']['height'] : '';

		if ( $instance['template'] == 'masonry' ) {
			// Overlay Post Category.
			if ( ! empty( $instance['design']['overlay_post_category']['font'] ) ) {
				$font = siteorigin_widget_get_font( $instance['design']['overlay_post_category']['font'] );
				$less_vars['overlay_post_category_font'] = $font['family'];

				if ( ! empty( $font['weight'] ) ) {
					$less_vars['overlay_post_category_font_style'] = $font['style'];
					$less_vars['overlay_post_category_font_weight'] = $font['weight_raw'];
				}
			}
			$less_vars['overlay_post_category_font_size'] = ! empty( $instance['design']['overlay_post_category']['font_size'] ) ? $instance['design']['overlay_post_category']['font_size'] : '';
			$less_vars['overlay_post_category_color'] = ! empty( $instance['design']['overlay_post_category']['color'] ) ? $instance['design']['overlay_post_category']['color'] : '';
			$less_vars['overlay_post_category_color_hover'] = ! empty( $instance['design']['overlay_post_category']['color_hover'] ) ? $instance['design']['overlay_post_category']['color_hover'] : '';

			$less_vars['overlay_post_category_background'] = ! empty( $instance['design']['overlay_post_category']['background'] ) ? $instance['design']['overlay_post_category']['background'] : 'rgba(0,0,0,0.80)';

			$less_vars['overlay_post_category_background_hover'] = ! empty( $instance['design']['overlay_post_category']['background_hover'] ) ? $instance['design']['overlay_post_category']['background_hover'] : 'rgba(0,0,0,0.75)';
		}

		if ( $instance['template'] == 'portfolio' ) {
			// Filter Categories.
			if ( ! empty( $instance['design']['filter_categories']['font'] ) ) {
				$font = siteorigin_widget_get_font( $instance['design']['filter_categories']['font'] );
				$less_vars['filter_categories_font'] = $font['family'];

				if ( ! empty( $font['weight'] ) ) {
					$less_vars['filter_categories_font_style'] = $font['style'];
					$less_vars['filter_categories_font_weight'] = $font['weight_raw'];
				}
			}
			$less_vars['filter_categories_font_size'] = ! empty( $instance['design']['filter_categories']['font_size'] ) ? $instance['design']['filter_categories']['font_size'] : '';
			$less_vars['filter_categories_color'] = ! empty( $instance['design']['filter_categories']['color'] ) ? $instance['design']['filter_categories']['color'] : '';
			$less_vars['filter_categories_color_hover'] = ! empty( $instance['design']['filter_categories']['color_hover'] ) ? $instance['design']['filter_categories']['color_hover'] : '';
			$less_vars['filter_categories_text_transform'] = ! empty( $instance['design']['filter_categories']['text_transform'] ) ? 'uppercase' : '';
			$less_vars['filter_categories_selected_border_color'] = ! empty( $instance['design']['filter_categories']['selected_border_color'] ) ? $instance['design']['filter_categories']['selected_border_color'] : '';
			$less_vars['filter_categories_selected_border_thickness'] = ! empty( $instance['design']['filter_categories']['selected_border_thickness'] ) ? $instance['design']['filter_categories']['selected_border_thickness'] : '';

			// Featured Images.
			$less_vars['featured_image_border_color'] = ! empty( $instance['design']['featured_image']['border_color'] ) ? $instance['design']['featured_image']['border_color'] : '';
			$less_vars['featured_image_hover_overlay_color'] = ! empty( $instance['design']['featured_image']['hover_overlay_color'] ) ? $instance['design']['featured_image']['hover_overlay_color'] : '';


			if ( ! empty( $instance['design']['featured_image']['post_title_font'] ) ) {
				$font = siteorigin_widget_get_font( $instance['design']['featured_image']['post_title_font'] );
				$less_vars['featured_image_post_title_font'] = $font['family'];

				if ( ! empty( $font['weight'] ) ) {
					$less_vars['featured_image_post_title_font_style'] = $font['style'];
					$less_vars['featured_image_post_title_font_weight'] = $font['weight_raw'];
				}
			}
			$less_vars['featured_image_post_title_font_size'] = ! empty( $instance['design']['featured_image']['post_title_font_size'] ) ? $instance['design']['featured_image']['post_title_font_size'] : '';
			$less_vars['featured_image_post_title_color'] = ! empty( $instance['design']['featured_image']['post_title_color'] ) ? $instance['design']['featured_image']['post_title_color'] : '';
			$less_vars['featured_image_divider_border_color'] = ! empty( $instance['design']['featured_image']['divider_border_color'] ) ? $instance['design']['featured_image']['divider_border_color'] : '';
			$less_vars['featured_image_divider_border_thickness'] = ! empty( $instance['design']['featured_image']['divider_border_thickness'] ) ? $instance['design']['featured_image']['divider_border_thickness'] : '';
			$less_vars['featured_image_divider_border_margin'] = ! empty( $instance['design']['featured_image']['divider_border_margin'] ) ? $instance['design']['featured_image']['divider_border_margin'] : '';

			if ( ! empty( $instance['design']['featured_image']['post_meta_font'] ) ) {
				$font = siteorigin_widget_get_font( $instance['design']['featured_image']['post_meta_font'] );
				$less_vars['featured_image_post_meta_font'] = $font['family'];

				if ( ! empty( $font['weight'] ) ) {
					$less_vars['featured_image_post_meta_font_style'] = $font['style'];
					$less_vars['featured_image_post_meta_font_weight'] = $font['weight_raw'];
				}
			}
			$less_vars['featured_image_post_meta_font_size'] = ! empty( $instance['design']['featured_image']['post_meta_font_size'] ) ? $instance['design']['featured_image']['post_meta_font_size'] : '';
			$less_vars['featured_image_post_meta_color'] = ! empty( $instance['design']['featured_image']['post_meta_color'] ) ? $instance['design']['featured_image']['post_meta_color'] : '';
		}

		return $less_vars;
	}

	/**
	 * Get terms for a given taxonomy.
	 *
	 * This method retrieves the terms for a given taxonomy. If a post ID is provided,
	 * it retrieves the terms associated with that post. Otherwise, it retrieves
	 * all terms for the taxonomy.
	 *
	 * @param string $taxonomy The taxonomy to retrieve terms for.
	 * @param int $post_id The post ID. Defaults to 0.
	 * @param bool $error_check Whether to check for errors. Defaults to true.
	 *
	 * @return array|false The terms for the taxonomy or false if an error occurs.
	 */
	public static function get_terms_for_taxonomy( $taxonomy, $post_id = 0, $error_check = true ) {
		$terms = $post_id ? get_the_terms( (int) $post_id, $taxonomy ) : get_terms( $taxonomy );

		set_query_var( 'siteorigin_widgets_portfolio_taxonomy', $taxonomy );

		if ( $error_check && ( empty( $terms ) || is_wp_error( $terms ) ) ) {
			return false;
		}

		return $terms;
	}

	/**
	 * Legacy method to get the terms for the portfolio.
	 *
	 * This method retrieves the terms for a given query. It is the legacy version of
	 * `get_query_terms`, and ideally should be replaced with the newer method.
	 *
	 * @param array $instance The instance settings.
	 * @param int $post_id Optional. The post ID to retrieve terms for. Default is 0.
	 *
	 * @return array The terms for the query.
	 */
	public static function portfolio_get_terms( $instance, $post_id = 0 ) {
		$query = wp_parse_args( siteorigin_widget_post_selector_process_query( $instance['posts'] ) );

		return self::get_query_terms( $instance, $query, $post_id );
	}

	/**
	 * Get query terms for the instance.
	 *
	 * This method retrieves the terms for the portfolio based on the instance
	 * and post ID. It checks for developer-set terms, Jetpack Portfolio terms,
	 * and other possible taxonomies. If no terms are found, it uses a fallback term.
	 * The fallback term is `category`. The fallback term can be filtered using
	 * the `siteorigin_widgets_blog_portfolio_fallback_term` filter.
	 *
	* @param array $instance The instance configuration array.
	* @param array $query The Blog query that'll be used to retrieve the posts.
	* @param int $post_id Optional. The post ID to retrieve terms for. Default is 0.
	 *
	 * @return array|false The terms for the portfolio, or false if no terms are found.
	 */
	public static function get_query_terms( $instance, $query, $post_id = 0 ) {
		$terms = array();

		$query['post_type'] = ! empty( $query['post_type'] ) ? $query['post_type'] : array( 'post' );

		if ( ! empty( $post_id ) ) {

			// Check if a developer has set terms for this post type.
			$taxonomy = apply_filters(
				'siteorigin_widgets_blog_portfolio_taxonomy',
				'',
				$instance,
				$query['post_type']
			);
			if ( ! empty( $taxonomy ) && is_array( $taxonomy ) ) {
				return $taxonomy;
			}
		}

		// Has user set terms? If so, let's use that if a post id hasn't be set.
		// Individual posts should use their terms, rather than one set in the widget.
		if (
			empty( $post_id ) &&
			! empty( $query ) &&
			! empty( $query['tax_query'] )
		 ) {
			foreach ( $query['tax_query'] as $tax ) {
				if ( isset( $tax['terms'] ) ) {
					$terms[] = $tax['terms'];
				}
			}

			// We need to set a taxonomy so we can hide/show terms.
			// Let's use the first term's taxonomy.
			if (
				! empty( $terms ) &&
				! empty( $query['tax_query'][1] ) &&
				! empty( $query['tax_query'][1]['taxonomy'] )
			) {
				set_query_var( 'siteorigin_widgets_portfolio_taxonomy',
					$query['tax_query'][1]['taxonomy']
				);
			}

			return $terms;
		}

		// Check for Jetpack Portfolio terms.
		if (
			post_type_exists( 'jetpack-portfolio' ) &&
			$query['post_type'] === 'jetpack-portfolio'
		) {
			$terms = self::get_terms_for_taxonomy(
				'jetpack-portfolio-type',
				$post_id
			);

			if ( $terms ) {
				return $terms;
			}
		}

		// Check for terms in other possible taxonomies.
		$possible_tax = get_object_taxonomies( $query['post_type'] );
		foreach ( $possible_tax as $tax ) {
			$terms = self::get_terms_for_taxonomy( $tax, $post_id );

			if ( $terms ) {
				return $terms;
			}
		}

		// Use fallback term if no terms are found.
		$fallback = apply_filters(
			'siteorigin_widgets_blog_portfolio_fallback_term',
			'category',
			$instance
		);

		return self::get_terms_for_taxonomy( $fallback, $post_id, false );
	}

	/**
	 * Retrieves a list of terms for the given instance for the purpose of filtering.
	 *
	 * This method retrieves the terms for the given instance and posts. It first finds
	 * the query terms, ensures that the terms are valid and (optionally) filters out any
	 * terms that aren't associated with the posts.
	 *
	 * @param array $instance The instance configuration array.
	 * @param WP_Query $posts The query object containing the posts.
	 * @param array $query The query array.
	 *
	 * @return array The filtered terms for the instance and posts.
	 */
	private static function get_filter_categories( $instance, $posts, $query ) {
		$terms = self::get_query_terms( $instance, $query );

		if ( ! apply_filters(
			'siteorigin_widgets_blog_portfolio_ensure_valid_terms',
			true
		) ) {
			return $terms;
		}

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return $terms;
		}

		$taxonomy = apply_filters(
			'siteorigin_widgets_blog_portfolio_taxonomy_filter',
			get_query_var( 'siteorigin_widgets_portfolio_taxonomy' )
		);

		// Get all of the terms for the posts.
		$all_terms = array();
		foreach ( $posts->posts as $post ) {
			$post_terms = wp_get_post_terms( $post->ID, $taxonomy );

			foreach ( $post_terms as $post_term ) {
				$all_terms[] = $post_term->slug;
			}
		}

		$all_terms = array_unique( $all_terms );

		// Filter out any terms that aren't in the posts.
		$filtered_terms = array();
		foreach ( $terms as $term ) {
			$slug = is_object( $term ) ? $term->slug : $term;

			if ( in_array( $slug, $all_terms ) ) {
				$filtered_terms[] = $term;
			}
		}

		return $filtered_terms;
	}

	public function modify_instance( $instance ) {
		if ( empty( $instance ) || ! is_array( $instance ) ) {
			return array();
		}

		if ( ! isset( $instance['settings'] ) || ! is_array( $instance['settings'] ) ) {
			$instance['settings'] = array();
		}

		if ( ! isset( $instance['settings']['tag'] ) ) {
			$instance['settings']['tag'] = 'h2';
		}

		if ( empty( $instance['template'] ) ) {
			$instance['template'] = 'standard';
		} else {
			$custom_template = apply_filters( 'siteorigin_widgets_blog_custom_template', false, $instance );

			if ( ! $custom_template ) {
				// Ensure selected template is valid.
				switch ( $instance['template'] ) {
					case 'alternate':
					case 'grid':
					case 'masonry':
					case 'offset':
					case 'portfolio':
					case 'standard':
						break;
					default:
						$instance['template'] = 'standard';
						break;
				}
			}
		}

		// Add featured image default for instances of the Blog widget created prior to the Featured Image Size setting.
		if ( ! isset( $instance['settings']['featured_image_size'] ) ) {
			if ( $instance['template'] == 'grid' || $instance['template'] == 'alternate' || $instance['template'] == 'portfolio' ) {
				$instance['settings']['featured_image_size'] = 'sow-blog-' . $instance['template'];
			} else {
				$instance['settings']['featured_image_size'] = 'full';
			}
			$instance['settings']['featured_image_size_width'] = 0;
			$instance['settings']['featured_image_size_height'] = 0;
		}


		// Migrate old opacity fields.
		if ( ! empty( $instance['design']['featured_image']['hover_overlay_opacity'] ) ) {
			$color = ! empty( $instance['design']['featured_image']['hover_overlay_color'] ) ? $instance['design']['featured_image']['hover_overlay_color'] : '#fff';
			$color = ltrim( $instance['design']['featured_image']['hover_overlay_color'], '#' );
			$rgb = array_map( 'hexdec', str_split( $color , strlen( $color ) == 6 ? 2 : 1 ) );
			$opacity = ! empty( $instance['design']['featured_image']['hover_overlay_opacity'] ) ? $instance['design']['featured_image']['hover_overlay_opacity'] : 0.9;
			$instance['design']['featured_image']['hover_overlay_color'] = "rgba( $rgb[0], $rgb[1], $rgb[2], $opacity )";
			unset( $instance['design']['featured_image']['hover_overlay_opacity'] );
		}

		if ( ! empty( $instance['design']['overlay_post_category']['background_opacity'] ) ) {
			$color = ! empty( $instance['design']['overlay_post_category']['background'] ) ? $instance['design']['overlay_post_category']['background'] : '#000';
			$color = ltrim( $color, '#' );
			$rgb = array_map( 'hexdec', str_split( $color , strlen( $color ) == 6 ? 2 : 1 ) );
			$opacity = $instance['design']['overlay_post_category']['background_opacity'];
			$instance['design']['overlay_post_category']['background'] = "rgba( $rgb[0], $rgb[1], $rgb[2], $opacity )";
			unset( $instance['design']['overlay_post_category']['background_opacity'] );
		}

		if ( ! empty( $instance['design']['overlay_post_category']['background_opacity_hover'] ) ) {
			$color = ! empty( $instance['design']['overlay_post_category']['background_hover'] ) ? $instance['design']['overlay_post_category']['background_hover'] : '#000';
			$color = ltrim( $color, '#' );
			$rgb = array_map( 'hexdec', str_split( $color , strlen( $color ) == 6 ? 2 : 1 ) );
			$opacity = $instance['design']['overlay_post_category']['background_opacity_hover'];
			$instance['design']['overlay_post_category']['background_hover'] = "rgba( $rgb[0], $rgb[1], $rgb[2], $opacity )";
			unset( $instance['design']['overlay_post_category']['background_opacity_hover'] );
		}

		$instance['paged_id'] = $this->get_style_hash( $instance );

		// Ensure Filter Categories is disabled for templates that
		// don't support it.
		if (
			$instance['template'] !== 'portfolio' &&
			$instance['template'] !== 'masonry'
		) {
			$instance['settings']['filter_categories'] = false;
		}

		// Prevent WAF PHP function block.
		if (
			is_array( $instance['settings'] ) &&
			isset( $instance['settings']['date_format'] )
		) {
			$instance['settings']['date_output_format'] = $instance['settings']['date_format'];
			unset( $instance['settings']['date_format'] );
		}

		return $instance;
	}

	public static function get_template( $instance ) {
		$template_file = plugin_dir_path( __FILE__ ) . 'tpl/' . sanitize_file_name( $instance['template'] ) . '.php';

		$override_file = apply_filters(
			'siteorigin_widgets_blog_template_file',
			$template_file,
			$instance
		);

		// If any of the below checks fail, return the default template.
		// Otherwise, allow the override.
		if ( empty( $override_file ) ) {
			return $template_file;
		}

		// File name must end in '-sow-blog.php'
		if ( substr( $override_file, -13 ) != '-sow-blog.php' ) {
			return $template_file;
		}

		if ( ! file_exists( $override_file ) ) {
			return $template_file;
		}

		return $override_file;
	}

	public function get_template_variables( $instance, $args ) {
		if ( ! isset( $instance['paged'] ) ) {
			$instance['paged'] = ! empty( $_GET['sow-' . $instance['paged_id'] ] ) ? (int) $_GET['sow-' . $instance['paged_id'] ] : 1;
		}
		$query = wp_parse_args(
			array(
				'paged' => $instance['paged'],
			),
			siteorigin_widget_post_selector_process_query( $instance['posts'] )
		);

		// If the user has set an offset, account for it after the first page.
		if ( isset( $query['offset'] ) && $instance['paged'] > 1 ) {
			$query['offset'] = $offset = ( $query['paged'] - 1 ) * $query['posts_per_page'] + $query['offset'];
		}

		if ( $instance['template'] == 'portfolio' && ! empty( $instance['featured_image_fallback'] ) ) {
			// The portfolio template relies on each post having an image so exclude any posts that don't.
			$query['meta_query'] = array(
				array(
					'key' => '_thumbnail_id',
					'compare' => 'EXISTS',
				),
			);
		}

		// Add template specific settings.
		$template_settings = array(
			'date_output_format' => isset( $instance['settings']['date_output_format'] ) ? $instance['settings']['date_output_format'] : null,
		);

		if ( $instance['template'] == 'offset' ) {
			if ( $instance['settings']['date'] ) {
				if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
					$template_settings['time_string'] = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
				} else {
					$template_settings['time_string'] = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
				}
			}
		}

		$posts = new WP_Query( apply_filters( 'siteorigin_widgets_blog_query', $query, $instance ) );

		$template_settings['filter_categories'] = ! empty( $instance['settings']['filter_categories'] );
		if ( $template_settings['filter_categories'] === true ) {
			$template_settings['terms'] = self::get_filter_categories( $instance, $posts, $query );
		}

		// Add the current template to the settings array to allow for easier referencing.
		$instance['settings']['template'] = $instance['template'];

		return array(
			'title' => $instance['title'],
			'settings' => $instance['settings'],
			'template_settings' => $template_settings,
			'posts' => $posts,
			'query' => $query,
		);
	}

	public function portfolio_filter_posts( $query, $instance ) {
		if (
			$instance['template'] == 'portfolio' &&
			! empty( $instance['settings']['featured_image_empty'] ) &&
			empty( $instance['settings']['featured_image_fallback'] )
		) {
			$query['meta_query'] = array(
				array(
					'key' => '_thumbnail_id',
					'compare' => 'EXISTS',
				),
			);
		}

		return $query;
	}

	public static function post_meta( $settings ) {
		ob_start();
		if ( is_sticky() ) {
			?>
			<span class="sow-featured-post"><?php esc_html_e( 'Sticky', 'so-widgets-bundle' ); ?></span>
			<?php
		}

		if ( $settings['date'] ) {
			$date_output_format = isset( $settings['date_output_format'] ) ? $settings['date_output_format'] : null;
			?>
			<span class="sow-entry-date">
				<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
					<time class="published" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
						<?php echo esc_html( get_the_date( $date_output_format ) ); ?>
					</time>
					<time class="updated" datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>">
						<?php echo esc_html( get_the_modified_date() ); ?>
					</time>
				</a>
			</span>
		<?php } ?>

		<?php if ( $settings['author'] ) { ?>
			<span class="sow-entry-author-link byline">
				<?php if ( function_exists( 'coauthors_posts_links' ) ) { ?>
					<?php coauthors_posts_links(); ?>
				<?php } else { ?>
					<span class="sow-author author vcard">
						<a class="url fn n" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
							<?php echo esc_html( get_the_author() ); ?>
						</a>
					</span>
				<?php } ?>
			</span>
		<?php } ?>

		<?php
		if ( $settings['categories'] && has_category() ) {

			$categories = get_the_terms( get_the_ID(), 'category' );
			$categories = apply_filters(
				'siteorigin_widgets_blog_filter_categories_output',
				$categories,
				$settings
			);

			if ( ! empty( $categories ) ) {
				echo '<span class="sow-entry-categories">';
				$category_links = [];

				foreach ( $categories as $category ) {
					$category_link = get_category_link( $category->term_id );
					$category_links[] = '<a href="' . esc_url( $category_link ) . '">' .
						esc_html__( $category->name, 'so-widgets-bundle' ) . '</a>';
				}

				/* translators: used between list items, there is a space after the comma */
				echo implode( esc_html__( ', ', 'so-widgets-bundle' ), $category_links );
				echo '</span>';
			}
		}

		if ( ! empty( $settings['tags'] ) && has_tag() ) {
			?>
			<span class="sow-entry-tags">
				<?php the_tags( '' ); ?>
			</span>
		<?php } ?>

		<?php if ( comments_open() && $settings['comment_count'] ) { ?>
			<span class="sow-entry-comments">
				<?php
			comments_popup_link(
				esc_html__( 'Leave a comment', 'so-widgets-bundle' ),
				esc_html__( 'One Comment', 'so-widgets-bundle' ),
				esc_html__( '% Comments', 'so-widgets-bundle' )
			);
			?>
			</span>
		<?php
		}

		$post_meta = ob_get_clean();

		echo apply_filters( 'siteorigin_widgets_blog_post_meta', $post_meta, $settings );
	}

	static public function post_featured_image( $settings, $categories = false, $size = 'full' ) {
		if ( $settings['featured_image'] ) {
			if ( ! has_post_thumbnail() && ! empty( $settings['featured_image_fallback'] ) ) {
				$featured_image = apply_filters( 'siteorigin_widgets_blog_featured_image_fallback', false, $settings );
			}
			if ( has_post_thumbnail() || ! empty( $featured_image ) ) {
				ob_start();
				?>
				<div class="sow-entry-thumbnail">
					<?php if ( $categories && $settings['categories'] && has_category() ) { ?>
						<div class="sow-thumbnail-meta">
							<?php echo get_the_category_list(); ?>
						</div>
					<?php } ?>
					<a href="<?php echo esc_url( get_the_permalink() ); ?>">
						<?php
						if ( has_post_thumbnail() ) {
							if ( ! empty( $settings['featured_image_size'] ) ) {
								$size = $settings['featured_image_size'] == 'custom_size' ? array( $settings['featured_image_size_width'], $settings['featured_image_size_height'] ) : $settings['featured_image_size'];
							} else {
								// Check if this template has a different default image size.
								if (
									$size == 'full' &&
									has_image_size( 'sow-blog-' . $settings['template'] )
								) {
									$size = 'sow-blog-' . $settings['template'];
								}
							}
							the_post_thumbnail( $size );
						} elseif( ! empty( $featured_image ) ) {
							echo $featured_image;
						}
						?>
					</a>
				</div>
				<?php
				echo apply_filters( 'siteorigin_widgets_blog_featured_image_markup', ob_get_clean(), $settings, $categories = false, $size = 'full' );
			}
		}
	}

	static public function generate_post_title( $settings ) {
		$tag = siteorigin_widget_valid_tag(
			$settings['tag'],
			'h2'
		);

		the_title(
			'<' . $tag  . ' class="sow-entry-title" style="margin: 0 0 5px;"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">',
			'</a></' . $tag  . '>'
		);
	}

	public function override_read_more( $settings, $setup = true ) {
		// Read More Override.
		if ( $settings['content'] == 'full' && apply_filters( 'siteorigin_widgets_blog_full_content_read_more', true ) ) {
			if ( $setup ) {
				set_query_var( 'siteorigin_blog_read_more', ! empty( $settings['read_more_text'] ) ? $settings['read_more_text'] : __( 'Continue reading', 'so-widgets-bundle' ) );
				add_filter( 'the_content_more_link', array( $this, 'alter_read_more_link' ) );
			} else {
				remove_filter( 'the_content_more_link', array( $this, 'alter_read_more_link' ) );
			}
		}

		if ( $setup ) {
			set_query_var(
				'siteorigin_blog_excerpt_length',
				apply_filters( 'siteorigin_widgets_blog_excerpt_length', isset( $settings['excerpt_length'] ) ? $settings['excerpt_length'] : 55 )
			);
			add_filter( 'excerpt_length', array( $this, 'alter_excerpt_length' ), 1000 );
			add_filter( 'excerpt_more', array( $this, 'alter_excerpt_more_indicator' ) );
		} else {
			remove_filter( 'excerpt_length', array( $this, 'alter_excerpt_length' ), 1000 );
			remove_filter( 'the_content_more_link', array( $this, 'alter_excerpt_more_indicator' ) );
		}
	}

	public function alter_read_more_link( $link ) {
		return '<a class="sow-more-link" href="' . esc_url( get_permalink() ) . '"> ' . esc_html( get_query_var( 'siteorigin_blog_read_more' ) ) . '<span class="sow-more-link-arrow">&rarr;</span></a>';
	}

	public function alter_excerpt_more_indicator( $indicator ) {
		return apply_filters( 'siteorigin_widgets_blog_excerpt_trim', get_query_var( 'siteorigin_blog_excerpt_length' ) == 0 ? '' : '...' );
	}

	public function alter_excerpt_length( $length = 55 ) {
		return get_query_var( 'siteorigin_blog_excerpt_length' );
	}

	public static function output_content( $settings, $space_above = 20 ) {
		if ( apply_filters( 'siteorigin_widgets_blog_show_content', true, $settings ) ) {
			?>
			<div
				class="sow-entry-content"
				style="margin-top: <?php echo (int) $space_above; ?>px;"
			>
				<?php
				if ( $settings['content'] == 'full' ) {
					the_content();
				} else {
					self::generate_excerpt( $settings );
				}
				?>
			</div>
			<?php
		}
	}

	public static function content_wrapper( $settings, $styles = array() ) {
		$styles = apply_filters( 'siteorigin_widgets_blog_content_wrapper_styles', $styles, $settings );
		?>
		<div class="sow-blog-content-wrapper"
		<?php
		if ( ! empty( $styles ) ) {
			echo ' style="';
			foreach ( $styles as $key => $val ) {
				echo siteorigin_sanitize_attribute_key( $key ) . ': ' . esc_attr( $val ) . ';';
			}
			echo '"';
		}
		?>>
		<?php
	}

	/**
	 * Checks if a read more link should be displayed.
	 *
	 * Determines display by checking:
	 * 1. Read more enabled in widget settings.
	 * 2. Filter to override default behavior (true).
	 * 3. Post has manual excerpt or auto-excerpt exceeds word limit
	 *
	 * @param array $settings The current Blog widget settings.
	 * @return bool True if read more link should be displayed, false otherwise.
	 */
	private static function maybe_add_read_more( $settings ): bool {
		if ( ! $settings['read_more'] ) {
			return false;
		}

		if ( apply_filters( 'siteorigin_widgets_blog_always_add_read_more', false ) ) {
			return true;
		}

		return has_excerpt() ||
			count( preg_split( '~[^\p{L}\p{N}\']+~u', get_the_excerpt() ) ) >= get_query_var( 'siteorigin_blog_excerpt_length' );
	}

	public static function generate_excerpt( $settings ) {
		$length = get_query_var( 'siteorigin_blog_excerpt_length' );
		$excerpt = get_the_excerpt();

		if (
			! has_excerpt() ||
			! empty( $settings['trim_manual_excerpt'] )
		) {
			$excerpt = wp_trim_words(
				$excerpt,
				$length,
				apply_filters( 'siteorigin_widgets_blog_excerpt_trim', '...' )
			);
		}

		if ( self::maybe_add_read_more( $settings ) ) {
			$read_more_text = ! empty( $settings['read_more_text'] ) ?
				$settings['read_more_text'] :
				__( 'Continue reading', 'so-widgets-bundle' );

			$read_more_text = '<a class="sow-more-link more-link excerpt" href="' .
				esc_url( get_permalink() ) . '">' .
				esc_html( $read_more_text ) .
				'<span class="sow-more-link-arrow">&rarr;</span></a>';

			$excerpt .= $read_more_text;
		}

		echo '<p>' . wp_kses_post( $excerpt ) . '</p>';
	}

	public function paginate_links( $settings, $posts, $instance ) {
		$addon_active = class_exists( 'SiteOrigin_Premium' ) && ! empty( SiteOrigin_Premium::single()->get_active_addons()['plugin/blog'] );

		if ( $addon_active ) {
			$pagination_markup = apply_filters( 'siteorigin_widgets_blog_pagination_markup', false, $settings, $posts, $instance );
		}

		if ( empty( $pagination_markup ) ) {
			if ( $addon_active && isset( $settings['pagination_reload'] ) && $settings['pagination_reload'] == 'ajax' ) {
				$current = 99999;
				$show_all_prev_next = true;
			} else {
				$current = max( 1, $posts->query['paged'] );
				$show_all_prev_next = false;
			}

			wp_reset_query();

			$pagination_markup = paginate_links( array(
				'format' => '?sow-' . $instance['paged_id'] . '=%#%',
				'total' => $posts->max_num_pages,
				'current' => $current,
				'show_all' => $show_all_prev_next,
				'prev_next' => ! $show_all_prev_next,
				'prev_text' => is_rtl() ? '&rarr;' : '&larr;',
				'next_text' => is_rtl() ? '&larr;' : '&rarr;',
				// Prevent multiple Blog widgets from "stacking" pagination.
				'base' => get_the_permalink() . '%_%',
			) );
		}

		if (
			! empty( $pagination_markup ) &&
			$settings['pagination'] != 'disabled'
		) {
			// To resolve a potential issue with the Block Editor, we need to override REST URLs with the actual permalink.
			if (
				defined( 'REST_REQUEST' ) &&
				strpos( $pagination_markup, 'sowb/v1/widgets/previews' ) !== false
			) {
				$pagination_markup = str_replace(
					// All non-standard pagination won't have the full URL present so what we replace changes.
					strpos( $pagination_markup, rest_url() ) !== false ? esc_url_raw( rest_url() ) . 'sowb/v1/widgets/previews/' : '/wp-json/sowb/v1/widgets/previews',
					get_the_permalink(),
					$pagination_markup
				);
			}
			?>
			<nav class="sow-post-navigation">
				<h3 class="screen-reader-text"><?php esc_html_e( 'Pagination', 'so-widgets-bundle' ); ?></h3>
				<div class="sow-nav-links<?php if ( ! empty( $settings['pagination'] ) ) {
					echo ' sow-post-pagination-' . esc_attr( $settings['pagination'] );
				} ?>">
					<?php echo $pagination_markup; ?>
				</div>
			</nav>
			<?php
		}
	}

	public function total_pages( $posts ) {
		// WP Query's max_num_pages doesn't account for offset, so let's do that now.
		if (
			! empty( $posts->query['offset'] ) &&
			is_numeric( $posts->query['offset'] )
		) {
			return ceil( max( $posts->found_posts - $posts->query['offset'], 1 ) / $posts->query['posts_per_page'] );
		} else {
			return $posts->max_num_pages;
		}
	}

	public function get_form_teaser() {
		if ( class_exists( 'SiteOrigin_Premium' ) ) {
			return false;
		}

		return array(
			sprintf(
				__( 'Get more pagination themes and Ajax reloading with %sSiteOrigin Premium%s', 'so-widgets-bundle' ),
				'<a href="https://siteorigin.com/downloads/premium/?featured_addon=plugin/blog" target="_blank" rel="noopener noreferrer">',
				'</a>'
			),
			sprintf(
				__( 'Adjust the post Read More link text and choose a custom post date format with %sSiteOrigin Premium%s', 'so-widgets-bundle' ),
				'<a href="https://siteorigin.com/downloads/premium/?featured_addon=plugin/blog" target="_blank" rel="noopener noreferrer">',
				'</a>'
			),
		);
	}
}

siteorigin_widget_register( 'sow-blog', __FILE__, 'SiteOrigin_Widget_Blog_Widget' );
