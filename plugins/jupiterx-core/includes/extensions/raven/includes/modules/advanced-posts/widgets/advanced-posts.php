<?php

namespace JupiterX_Core\Raven\Modules\Advanced_Posts\Widgets;

defined( 'ABSPATH' ) || die();

use JupiterX_Core\Raven\Base\Base_Widget;
use JupiterX_Core\Raven\Controls\Query as Control_Query;
use JupiterX_Core\Raven\Modules\Advanced_Posts\Functionality\Frontend;
use JupiterX_Core\Raven\Modules\Advanced_Posts\Module;
use JupiterX_Core\Raven\Utils;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class Advanced_Posts extends Base_Widget {
	public function get_name() {
		return 'raven-advanced-posts';
	}

	public function get_title() {
		return esc_html__( 'Advanced Posts', 'jupiterx-core' );
	}

	public function get_icon() {
		return 'raven-element-icon raven-element-icon-advanced-posts';
	}

	public function get_style_depends() {
		return [
			'dashicons',
			'e-animation-grow',
			'e-animation-shrink',
			'e-animation-pulse',
			'e-animation-pop',
			'e-animation-grow-rotate',
			'e-animation-wobble-skew',
			'e-animation-buzz-out',
		];
	}

	public function get_script_depends() {
		return [
			'imagesloaded',
			'jupiterx-core-raven-savvior',
			'jupiterx-core-raven-object-fit',
			'jupiterx-core-raven-isotope',
			'jupiterx-core-raven-packery',
		];
	}

	protected function register_controls() {
		$this->register_layout_controls();
		$this->register_settings_controls();
		$this->register_query_controls();
		$this->register_sort_and_filter_controls();
		$this->register_block_style();
		$this->register_block_featured_image_style();
		$this->register_overlay_style();
		$this->register_title_style();
		$this->register_meta_style();
		$this->register_excerpt_style();
		$this->register_cta_button_style();
		$this->register_pagination_style();
		$this->register_sortable_style();
		$this->register_author_apotlight_style();
		$this->register_tags_style();
	}

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function register_layout_controls() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Layout', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'general_layout',
			[
				'label' => esc_html__( 'General Layout', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'grid',
				'options' => [
					'grid' => esc_html__( 'Grid', 'jupiterx-core' ),
					'masonry' => esc_html__( 'Masonry', 'jupiterx-core' ),
					'matrix' => esc_html__( 'Matrix', 'jupiterx-core' ),
					'metro' => esc_html__( 'Metro', 'jupiterx-core' ),
				],
				'frontend_available' => true,
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'content_layout',
			[
				'label' => esc_html__( 'Content Layout', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'under-image',
				'options' => [
					'overlay' => esc_html__( 'Content Overlay', 'jupiterx-core' ),
					'under-image' => esc_html__( 'Content Under Image', 'jupiterx-core' ),
					'side' => esc_html__( 'Content on the Side', 'jupiterx-core' ),
				],
				'condition' => [
					'general_layout' => [ 'grid', 'masonry' ],
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'metro_matrix_content_layout',
			[
				'label' => esc_html__( 'Content Layout', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'under-image',
				'options' => [
					'overlay' => esc_html__( 'Content Overlay', 'jupiterx-core' ),
					'under-image' => esc_html__( 'Content Under Image', 'jupiterx-core' ),
				],
				'condition' => [
					'general_layout' => [ 'metro', 'matrix' ],
				],
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => esc_html__( 'Columns', 'jupiterx-core' ),
				'type' => 'select',
				'default' => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => [
					'1' => esc_html__( '1', 'jupiterx-core' ),
					'2' => esc_html__( '2', 'jupiterx-core' ),
					'3' => esc_html__( '3', 'jupiterx-core' ),
					'4' => esc_html__( '4', 'jupiterx-core' ),
					'5' => esc_html__( '5', 'jupiterx-core' ),
					'6' => esc_html__( '6', 'jupiterx-core' ),
				],
				'frontend_available' => true,
				'condition' => [
					'general_layout' => [ 'grid', 'masonry' ],
				],
			]
		);

		$this->add_responsive_control(
			'stroke_width',
			[
				'label' => esc_html__( 'Media Aspect Ratio', 'jupiterx-core' ),
				'type' => 'slider',
				'range' => [
					'px' => [
						'min' => 0.1,
						'max' => 3,
						'step' => 0.1,
					],
				],
				'default' => [
					'size' => '0.63',
				],
				'tablet_default' => [
					'size' => '0.63',
				],
				'mobile_default' => [
					'size' => '0.63',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-image' => 'padding-bottom: calc( {{SIZE}} * 100% );',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => '===',
									'value' => 'masonry',
								],
								[
									'name' => 'content_layout',
									'operator' => '===',
									'value' => 'side',
								],
							],

						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => '===',
									'value' => 'grid',
								],
								[
									'name' => 'content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],

						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'media_position',
			[
				'label' => esc_html__( 'Media Position', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'center center',
				'options' => [
					'center center' => esc_html__( 'Center Center', 'jupiterx-core' ),
					'center left' => esc_html__( 'Center Left', 'jupiterx-core' ),
					'center right' => esc_html__( 'Center Right', 'jupiterx-core' ),
					'top center' => esc_html__( 'Top Center', 'jupiterx-core' ),
					'top left' => esc_html__( 'Top Left', 'jupiterx-core' ),
					'top right' => esc_html__( 'Top Right', 'jupiterx-core' ),
					'bottom center' => esc_html__( 'Bottom Center', 'jupiterx-core' ),
					'bottom left' => esc_html__( 'Bottom Left', 'jupiterx-core' ),
					'bottom right' => esc_html__( 'Bottom Right', 'jupiterx-core' ),
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-image img' => '-o-object-position: {{VALUE}}; object-position: {{VALUE}};',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => '===',
									'value' => 'masonry',
								],
								[
									'name' => 'content_layout',
									'operator' => '===',
									'value' => 'side',
								],
							],

						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => '===',
									'value' => 'grid',
								],
								[
									'name' => 'content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],

						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'metro_matrix_large_aspect_ratio',
			[
				'label' => esc_html__( 'Large Media Aspect Ratio', 'jupiterx-core' ),
				'type' => 'slider',
				'range' => [
					'px' => [
						'min' => 0.1,
						'max' => 3,
						'step' => 0.1,
					],
				],
				'default' => [
					'size' => '0.63',
				],
				'tablet_default' => [
					'size' => '0.63',
				],
				'mobile_default' => [
					'size' => '0.63',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-posts-item.raven-posts-full-width .raven-post-image' => 'padding-bottom: calc( {{SIZE}} * 100% );',
				],
				'condition' => [
					'general_layout' => [ 'matrix', 'metro' ],
					'metro_matrix_content_layout!' => 'overlay',
				],
			]
		);

		$this->add_responsive_control(
			'large_media_position',
			[
				'label' => esc_html__( 'Large Media Position', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'center center',
				'options' => [
					'center center' => esc_html__( 'Center Center', 'jupiterx-core' ),
					'center left' => esc_html__( 'Center Left', 'jupiterx-core' ),
					'center right' => esc_html__( 'Center Right', 'jupiterx-core' ),
					'top center' => esc_html__( 'Top Center', 'jupiterx-core' ),
					'top left' => esc_html__( 'Top Left', 'jupiterx-core' ),
					'top right' => esc_html__( 'Top Right', 'jupiterx-core' ),
					'bottom center' => esc_html__( 'Bottom Center', 'jupiterx-core' ),
					'bottom left' => esc_html__( 'Bottom Left', 'jupiterx-core' ),
					'bottom right' => esc_html__( 'Bottom Right', 'jupiterx-core' ),
				],
				'selectors' => [
					'{{WRAPPER}} .raven-posts-item.raven-posts-full-width .raven-post-image img' => '-o-object-position: {{VALUE}}; object-position: {{VALUE}};',
				],
				'condition' => [
					'general_layout' => [ 'matrix', 'metro' ],
					'metro_matrix_content_layout!' => 'overlay',
				],
			]
		);

		$this->add_responsive_control(
			'metro_matrix_small_aspect_ratio',
			[
				'label' => esc_html__( 'Small Media Aspect Ratio', 'jupiterx-core' ),
				'type' => 'slider',
				'range' => [
					'px' => [
						'min' => 0.1,
						'max' => 3,
						'step' => 0.1,
					],
				],
				'default' => [
					'size' => '0.63',
				],
				'tablet_default' => [
					'size' => '0.63',
				],
				'mobile_default' => [
					'size' => '0.63',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-posts-item:not(.raven-posts-full-width) .raven-post-image' => 'padding-bottom: calc( {{SIZE}} * 100% );',
				],
				'condition' => [
					'general_layout' => [ 'matrix', 'metro' ],
					'metro_matrix_content_layout!' => 'overlay',
				],
			]
		);

		$this->add_responsive_control(
			'small_media_position',
			[
				'label' => esc_html__( 'Small Media Position', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'center center',
				'options' => [
					'center center' => esc_html__( 'Center Center', 'jupiterx-core' ),
					'center left' => esc_html__( 'Center Left', 'jupiterx-core' ),
					'center right' => esc_html__( 'Center Right', 'jupiterx-core' ),
					'top center' => esc_html__( 'Top Center', 'jupiterx-core' ),
					'top left' => esc_html__( 'Top Left', 'jupiterx-core' ),
					'top right' => esc_html__( 'Top Right', 'jupiterx-core' ),
					'bottom center' => esc_html__( 'Bottom Center', 'jupiterx-core' ),
					'bottom left' => esc_html__( 'Bottom Left', 'jupiterx-core' ),
					'bottom right' => esc_html__( 'Bottom Right', 'jupiterx-core' ),
				],
				'selectors' => [
					'{{WRAPPER}} .raven-posts-item:not(.raven-posts-full-width) .raven-post-image img' => '-o-object-position: {{VALUE}}; object-position: {{VALUE}};',
				],
				'condition' => [
					'general_layout' => [ 'matrix', 'metro' ],
					'metro_matrix_content_layout!' => 'overlay',
				],
			]
		);

		$this->add_group_control(
			'image-size',
			[
				'name' => 'image',
				'default' => 'large',
			]
		);

		$this->add_control(
			'equal_height',
			[
				'label' => esc_html__( 'Equal Columns Height', 'jupiterx-core' ),
				'type' => 'switcher',
				'selectors' => [
					'{{WRAPPER}} .raven-grid-item' => 'align-items: stretch',
				],
				'prefix_class' => 'raven-advaned-posts-equal-height-',
				'condition' => [
					'general_layout' => 'grid',
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'mirror_rows',
			[
				'label' => esc_html__( 'Mirror Rows', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => '',
				'condition' => [
					'general_layout' => [ 'grid', 'masonry' ],
					'content_layout' => 'side',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'block_hover',
			[
				'label' => esc_html__( 'Block Hover', 'jupiterx-core' ),
				'type' => 'raven_hover_effect',
			]
		);

		$this->add_control(
			'featured_image_hover',
			[
				'label' => esc_html__( 'Featured Image Hover', 'jupiterx-core' ),
				'type' => 'select',
				'default' => '',
				'options' => [
					'' => esc_html__( 'None', 'jupiterx-core' ),
					'zoom-move' => esc_html__( 'Zoom & Move', 'jupiterx-core' ),
					'slide-right' => esc_html__( 'Slide Right', 'jupiterx-core' ),
					'slide-down' => esc_html__( 'Slide Down', 'jupiterx-core' ),
					'scale-down' => esc_html__( 'Scale Down', 'jupiterx-core' ),
					'scale-up' => esc_html__( 'Scale Up', 'jupiterx-core' ),
					'blur' => esc_html__( 'Blur', 'jupiterx-core' ),
					'grayscale-reverse' => esc_html__( 'Grayscale to Color', 'jupiterx-core' ),
					'grayscale' => esc_html__( 'Color to Grayscale', 'jupiterx-core' ),
				],
				'prefix_class' => 'raven-hover-',
				'frontend_available' => true,
				'render_type' => 'template',
				'condition' => [
					'show_image' => 'yes',
				],
			]
		);

		$this->add_control(
			'load_effect',
			[
				'label' => esc_html__( 'Load Effect', 'jupiterx-core' ),
				'type' => 'select',
				'default' => '',
				'options' => [
					'' => esc_html__( 'None', 'jupiterx-core' ),
					'fade-in' => esc_html__( 'Fade In', 'jupiterx-core' ),
					'slide-down' => esc_html__( 'Slide Down', 'jupiterx-core' ),
					'slide-up' => esc_html__( 'Slide Up', 'jupiterx-core' ),
					'slide-right' => esc_html__( 'Slide Left', 'jupiterx-core' ),
					'slide-left' => esc_html__( 'Slide Right', 'jupiterx-core' ),
					'scale-up' => esc_html__( 'Scale Up', 'jupiterx-core' ),
					'scale-down' => esc_html__( 'Scale Down', 'jupiterx-core' ),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'show_overlay_on_hover',
			[
				'label' => esc_html__( 'Show Overlay Content On Hover', 'jupiterx-core' ),
				'type' => 'switcher',
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'grid', 'masonry' ],
								],
								[
									'name' => 'content_layout',
									'operator' => '===',
									'value' => 'overlay',
								],
							],

						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'matrix', 'metro' ],
								],
								[
									'name' => 'metro_matrix_content_layout',
									'operator' => '===',
									'value' => 'overlay',
								],
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'author_spotlight',
			[
				'label' => esc_html__( 'Author Spotlight', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => 'yes',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function register_settings_controls() {
		$this->start_controls_section(
			'section_settings',
			[
				'label' => esc_html__( 'Settings', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'post_title_tag',
			[
				'label' => esc_html__( 'Title HTML Tag', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'h3',
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
			]
		);

		$this->add_control(
			'meta_position',
			[
				'label' => esc_html__( 'Meta Position', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'before_title',
				'options' => [
					'before_title'  => esc_html__( 'Before Title', 'jupiterx-core' ),
					'after_title'   => esc_html__( 'After Title', 'jupiterx-core' ),
				],
			]
		);

		$this->add_control(
			'show_image',
			[
				'label' => esc_html__( 'Featured Image', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => 'yes',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'show_title',
			[
				'label' => esc_html__( 'Post Title', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => 'yes',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'show_date',
			[
				'label' => esc_html__( 'Date', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => 'yes',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'date_type',
			[
				'label' => esc_html__( 'Date Type', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'published',
				'options' => [
					'published'  => esc_html__( 'Published Date', 'jupiterx-core' ),
					'last_modified'   => esc_html__( 'Last Modified Date', 'jupiterx-core' ),
				],
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);

		$this->add_control(
			'date_format',
			[
				'label' => esc_html__( 'Date Format', 'jupiterx-core' ),
				'type' => 'select',
				'default' => '1',
				'options' => [
					'1'  => esc_html__( 'March 6, 2023', 'jupiterx-core' ),
					'2'   => esc_html__( 'March 23rd, 2023', 'jupiterx-core' ),
					'3'   => esc_html__( 'Mar 6, 2023', 'jupiterx-core' ),
					'4'   => esc_html__( '2023/03/23', 'jupiterx-core' ),
					'5'   => esc_html__( '23/03/2023', 'jupiterx-core' ),
					'6'   => esc_html__( '23.03.2023', 'jupiterx-core' ),
					'7'   => esc_html__( '03.23.2023', 'jupiterx-core' ),
					'custom'   => esc_html__( 'Custom', 'jupiterx-core' ),
				],
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);

		$this->add_control(
			'custom_format',
			[
				'label' => esc_html__( 'Custom Format', 'jupiterx-core' ),
				'type' => 'text',
				'default' => __( 'F j,Y', 'jupiterx-core' ),
				'description' => sprintf(
					/* translators: %1$s: open anchor tag, %2$s: close anchor tag. */
					esc_html__( 'Refer to PHP date formats %1$s here %2$s.', 'jupiterx-core' ),
					'<a href="https://wordpress.org/support/article/formatting-date-and-time/" target="_blank">',
					'</a>'
				),
				'condition' => [
					'show_date' => 'yes',
					'date_format' => 'custom',
				],
			]
		);

		$this->add_control(
			'date_divider',
			[
				'type' => 'divider',
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);

		$this->add_control(
			'post_meta_heading',
			[
				'label' => esc_html__( 'Post Meta', 'jupiterx-core' ),
				'type' => 'heading',
			]
		);

		$this->add_control(
			'show_author',
			[
				'label' => esc_html__( 'Author', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => '',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'show_categories',
			[
				'label' => esc_html__( 'Categories', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => '',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'show_tags',
			[
				'label' => esc_html__( 'Tags', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => 'yes',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'condition' => [
					'show_image' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_comments',
			[
				'label' => esc_html__( 'Comments', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => '',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'show_reading_time',
			[
				'label' => esc_html__( 'Reading Time', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => '',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'show_excerpt',
			[
				'label' => esc_html__( 'Excerpt', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => 'yes',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'show_button',
			[
				'label' => esc_html__( 'CTA Button', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => '',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'post_pagination_heading',
			[
				'label' => esc_html__( 'Pagination', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label' => esc_html__( 'Show Pagination', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => '',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'pagination_type',
			[
				'label' => esc_html__( 'Pagination Style', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'page_based',
				'options' => [
					'page_based' => esc_html__( 'Page Based', 'jupiterx-core' ),
					'load_more' => esc_html__( 'Load More', 'jupiterx-core' ),
					'infinite_load' => esc_html__( 'Infinite Load', 'jupiterx-core' ),
				],
				'condition' => [
					'show_pagination' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'post_sortable_heading',
			[
				'label' => esc_html__( 'Sortable', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
				'condition' => [
					'is_archive_template' => '',
				],
			]
		);

		$this->add_control(
			'show_sortable',
			[
				'label' => esc_html__( 'Show Sortable', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => '',
				'frontend_available' => true,
				'condition' => [
					'is_archive_template' => '',
				],
			]
		);

		$this->add_control(
			'show_all_title',
			[
				'label' => esc_html__( 'Show All Titles', 'jupiterx-core' ),
				'type' => 'switcher',
				'default' => 'yes',
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'frontend_available' => true,
				'condition' => [
					'show_sortable' => 'yes',
					'is_archive_template' => '',
				],
			]
		);

		$this->add_control(
			'sortable_all_text',
			[
				'label' => esc_html__( 'All Text', 'jupiterx-core' ),
				'type' => 'text',
				'default' => esc_html__( 'All', 'jupiterx-core' ),
				'condition' => [
					'show_sortable' => 'yes',
					'show_all_title' => 'yes',
					'is_archive_template' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_query_controls() {
		$this->start_controls_section(
			'section_query',
			[
				'label' => esc_html__( 'Query', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'query_posts_per_page',
			[
				'label' => __( 'Posts per Page', 'jupiterx-core' ),
				'description' => __( 'Use -1 to show all posts.', 'jupiterx-core' ),
				'type' => 'number',
				'default' => 6,
				'min' => -1,
				'max' => 50,
				'frontend_available' => true,
				'condition'    => [
					'is_archive_template' => '',
				],
			]
		);

		$this->add_control(
			'is_archive_template', [
				'label'              => esc_html__( 'Is Archive Template', 'jupiterx-core' ),
				'type'               => 'switcher',
				'label_on'           => esc_html__( 'Yes', 'jupiterx-core' ),
				'label_off'          => esc_html__( 'No', 'jupiterx-core' ),
				'return_value'       => 'true',
				'default'            => '',
				'frontend_available' => true,
			]
		);

		$this->add_group_control(
			'raven-posts',
			[
				'name' => 'query',
				'post_type' => Module::get_post_types(),
				'condition' => [
					'is_archive_template' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_sort_and_filter_controls() {
		$this->start_controls_section(
			'section_sort_and_filter',
			[
				'label' => esc_html__( 'Sort & Filter', 'jupiterx-core' ),
				'condition'    => [
					'is_archive_template' => '',
				],
			]
		);

		$this->add_control(
			'query_orderby',
			[
				'label' => esc_html__( 'Order By', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'date',
				'options' => [
					'date' => esc_html__( 'Date', 'jupiterx-core' ),
					'title' => esc_html__( 'Title', 'jupiterx-core' ),
					'menu_order' => esc_html__( 'Menu Order', 'jupiterx-core' ),
					'rand' => esc_html__( 'Random', 'jupiterx-core' ),
				],
			]
		);

		$this->add_control(
			'query_order',
			[
				'label' => esc_html__( 'Order', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'DESC',
				'options' => [
					'ASC' => esc_html__( 'ASC', 'jupiterx-core' ),
					'DESC' => esc_html__( 'DESC', 'jupiterx-core' ),
				],
			]
		);

		$this->add_control(
			'query_offset',
			[
				'label' => esc_html__( 'Offset', 'jupiterx-core' ),
				'description' => esc_html__( 'Use this setting to skip over posts (e.g. \'4\' to skip over 4 posts).', 'jupiterx-core' ),
				'type' => 'number',
				'default' => 0,
				'min' => 0,
				'max' => 100,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'query_excludes',
			[
				'label' => esc_html__( 'Excludes', 'jupiterx-core' ),
				'type' => 'select2',
				'multiple' => true,
				'label_block' => true,
				'default' => [ 'current_post' ],
				'options' => [
					'current_post' => esc_html__( 'Current Post', 'jupiterx-core' ),
					'manual_selection' => esc_html__( 'Manual Selection', 'jupiterx-core' ),
				],
			]
		);

		$this->add_control(
			'query_excludes_ids',
			[
				'label' => esc_html__( 'Search & Select', 'jupiterx-core' ),
				'type' => 'raven_query',
				'options' => [],
				'label_block' => true,
				'multiple' => true,
				'condition' => [
					'query_excludes' => 'manual_selection',
				],
				'query' => [
					'source' => Control_Query::QUERY_SOURCE_POST,
					'control_query' => [
						'post_type' => 'query_post_type',
					],
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function register_block_style() {
		$this->start_controls_section(
			'section_block',
			[
				'label' => esc_html__( 'Block', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_responsive_control(
			'block_column_spacing',
			[
				'label' => esc_html__( 'Column Spacing', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => '35',
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => '35',
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => '35',
					'unit' => 'px',
				],
				'device_args' => [
					'desktop' => [
						'selectors' => [
							'{{WRAPPER}} .raven-grid, {{WRAPPER}} .raven-masonry' => 'margin-left: calc( -{{SIZE}}{{UNIT}} / 2 ); margin-right: calc( -{{SIZE}}{{UNIT}} / 2 );',
							'{{WRAPPER}} .raven-grid-item, {{WRAPPER}} .raven-masonry-item' => 'padding-left: calc( {{SIZE}}{{UNIT}} / 2 ); padding-right: calc( {{SIZE}}{{UNIT}} / 2 );',
							'{{WRAPPER}} .raven-grid.raven-grid-1, {{WRAPPER}} .raven-masonry.raven-masonry-1' => 'margin-left: 0; margin-right: 0;',
							'{{WRAPPER}} .raven-grid.raven-grid-1 .raven-grid-item, {{WRAPPER}} .raven-masonry.raven-masonry-1 .raven-masonry-item' => 'padding-left: 0; padding-right: 0;',
						],
					],
					'tablet' => [
						'selectors' => [
							'{{WRAPPER}} .raven-grid' => 'margin-left: calc( -{{SIZE}}{{UNIT}} / 2 ); margin-right: calc( -{{SIZE}}{{UNIT}} / 2 );',
							'{{WRAPPER}} .raven-grid-item, {{WRAPPER}} .raven-masonry-item' => 'padding-left: calc( {{SIZE}}{{UNIT}} / 2 ); padding-right: calc( {{SIZE}}{{UNIT}} / 2 );',
							'{{WRAPPER}} .raven-grid.raven-grid-tablet-1, {{WRAPPER}} .raven-masonry.raven-masonry-tablet-1' => 'margin-left: 0; margin-right: 0;',
							'{{WRAPPER}} .raven-grid.raven-grid-tablet-1 .raven-grid-item, {{WRAPPER}} .raven-masonry.raven-masonry-tablet-1 .raven-masonry-item' => 'padding-left: 0; padding-right: 0;',
						],
					],
					'mobile' => [
						'selectors' => [
							'{{WRAPPER}} .raven-grid, {{WRAPPER}} .raven-masonry' => 'margin-left: calc( -{{SIZE}}{{UNIT}} / 2 ); margin-right: calc( -{{SIZE}}{{UNIT}} / 2 );',
							'{{WRAPPER}} .raven-grid-item, {{WRAPPER}} .raven-masonry-item,' => 'padding-left: calc( {{SIZE}}{{UNIT}} / 2 ); padding-right: calc( {{SIZE}}{{UNIT}} / 2 );',
							'{{WRAPPER}} .raven-grid.raven-grid-mobile-1, {{WRAPPER}} .raven-masonry.raven-masonry-mobile-1' => 'margin-left: 0; margin-right: 0;',
							'{{WRAPPER}} .raven-grid.raven-grid-mobile-1 .raven-grid-item, {{WRAPPER}} .raven-masonry.raven-masonry-mobile-1 .raven-masonry-item' => 'padding-left: 0; padding-right: 0;',
						],
					],
				],
				'condition' => [
					'general_layout' => [ 'grid', 'masonry' ],
				],
			]
		);

		$this->add_responsive_control(
			'rows_spacing',
			[
				'label' => esc_html__( 'Row Spacing', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => '35',
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => '35',
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => '35',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-posts-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'general_layout' => [ 'grid', 'masonry' ],
				],
			]
		);

		$this->add_responsive_control(
			'metro_matrix_rows_gap',
			[
				'label' => esc_html__( 'Gap', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-posts-metro .raven-metro' => ' margin-right: calc( -{{SIZE}}{{UNIT}}*2 ); margin-top: -{{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .raven-posts-metro .raven-metro-item ' => 'padding-right: {{SIZE}}{{UNIT}};margin-top: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .raven-posts-matrix .raven-matrix' => ' margin-right: calc( -{{SIZE}}{{UNIT}}*2 ); margin-top: -{{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .raven-posts-matrix .raven-matrix-item ' => 'padding-right: {{SIZE}}{{UNIT}};margin-top: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'general_layout' => [ 'metro', 'matrix' ],
				],
				'frontend_available' => true,
			]
		);

		$this->start_controls_tabs( 'block_tabs' );

		$this->start_controls_tab(
			'block_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'block_background_normal',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background Color Type', 'jupiterx-core' ),
						'default' => 'classic',
					],
					'color' => [
						'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
						'default' => '#fff',
					],
				],
				'selector' => '{{WRAPPER}} .raven-post-wrapper',
			]
		);

		$this->add_control(
			'block_border_color-normal',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'block_border_width_normal_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-wrapper' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'block_border_width_normal',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .raven-post-wrapper',
			]
		);

		$this->add_control(
			'post_border_radius_normal',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => '6',
					'right' => '6',
					'bottom' => '6',
					'left' => '6',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'post_box_shadow_normal',
				'selector' => '{{WRAPPER}} .raven-post-wrapper',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'block_tab_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'block_background_hover',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background Color Type', 'jupiterx-core' ),
					],
					'color' => [
						'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .raven-post-wrapper:hover',
			]
		);

		$this->add_control(
			'block_border_color-hover',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'block_border_width_hover_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-wrapper:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'block_border_width_hover',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .raven-post-wrapper:hover',
			]
		);

		$this->add_control(
			'post_border_radius_hover',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-wrapper:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'post_box_shadow_hover',
				'selector' => '{{WRAPPER}} .raven-post-wrapper:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'block_content',
			[
				'label' => esc_html__( 'Content', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'block_content_h_alignment',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
				'label_block' => false,
				'default' => '',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-posts-item' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .raven-posts-item .raven-post-tags' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .raven-posts-item .raven-post-author-spotlight' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'block_content_v_alignment',
			[
				'label' => esc_html__( 'Vertical Alignment', 'jupiterx-core' ),
				'type' => 'choose',
				'label_block' => false,
				'default' => '',
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Top', 'jupiterx-core' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Middle', 'jupiterx-core' ),
						'icon' => 'eicon-v-align-middle',
					],
					'end' => [
						'title' => esc_html__( 'Bottom', 'jupiterx-core' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'grid', 'masonry' ],
								],
								[
									'name' => 'content_layout',
									'operator' => '===',
									'value' => 'side',
								],
							],
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'matrix', 'metro' ],
								],
								[
									'name' => 'metro_matrix_content_layout',
									'operator' => '===',
									'value' => 'overlay',
								],
							],
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-inline' => 'align-items: {{VALUE}};',
					'{{WRAPPER}} .raven-posts-metro .content-layout-overlay .raven-post-content' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .raven-posts-matrix .content-layout-overlay .raven-post-content' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'block_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}}:not(.raven-advaned-posts-equal-height-yes) .raven-post .raven-post-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}}.raven-advaned-posts-equal-height-yes .raven-post .raven-post-content-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function register_block_featured_image_style() {
		$this->start_controls_section(
			'section_featured_image',
			[
				'label' => esc_html__( 'Featured Image', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'show_image' => 'yes',
				],
			]
		);

		$this->add_control(
			'section_featured_background_position',
			[
				'label' => esc_html__( 'Background Position', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'center center',
				'options' => [
					'center center' => esc_html__( 'Center Center', 'jupiterx-core' ),
					'center left' => esc_html__( 'Center Left', 'jupiterx-core' ),
					'center right' => esc_html__( 'Center Right', 'jupiterx-core' ),
					'top center' => esc_html__( 'Top Center', 'jupiterx-core' ),
					'top left' => esc_html__( 'Top Left', 'jupiterx-core' ),
					'top right' => esc_html__( 'Top Right', 'jupiterx-core' ),
					'bottom center' => esc_html__( 'Bottom Center', 'jupiterx-core' ),
					'bottom left' => esc_html__( 'Bottom Left', 'jupiterx-core' ),
					'bottom right' => esc_html__( 'Bottom Right', 'jupiterx-core' ),
				],
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'grid', 'masonry' ],
								],
								[
									'name' => 'content_layout',
									'operator' => '===',
									'value' => 'overlay',
								],
							],
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'matrix', 'metro' ],
								],
								[
									'name' => 'metro_matrix_content_layout',
									'operator' => '===',
									'value' => 'overlay',
								],
							],
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .content-layout-overlay .raven-post-image img' => '-o-object-position: {{VALUE}}; object-position: {{VALUE}};',
					'{{WRAPPER}} .content-layout-overlay .raven-posts-zoom-move-wrapper' => 'background-position: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'post_image_background_size',
			[
				'label' => esc_html__( 'Background Size', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'cover',
				'options' => [
					'auto' => esc_html__( 'Auto', 'jupiterx-core' ),
					'cover' => esc_html__( 'Cover', 'jupiterx-core' ),
					'contain' => esc_html__( 'Contain', 'jupiterx-core' ),
				],
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'grid', 'masonry' ],
								],
								[
									'name' => 'content_layout',
									'operator' => '===',
									'value' => 'overlay',
								],
							],
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'matrix', 'metro' ],
								],
								[
									'name' => 'metro_matrix_content_layout',
									'operator' => '===',
									'value' => 'overlay',
								],
							],
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .content-layout-overlay .raven-post-image img' => '-o-object-fit: {{VALUE}}; object-fit: {{VALUE}};',
					'{{WRAPPER}} .content-layout-overlay .raven-posts-zoom-move-wrapper' => 'background-size: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'featured_image_width',
			[
				'label' => esc_html__( 'Width', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ '%', 'px', 'vh' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'default' => [
					'size' => '100',
					'unit' => '%',
				],
				'tablet_default' => [
					'size' => '100',
					'unit' => '%',
				],
				'mobile_default' => [
					'size' => '100',
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post:not(.raven-post-inline) .raven-post-image, {{WRAPPER}} .raven-post-inline .raven-post-image-wrap' => 'width: {{SIZE}}{{UNIT}};',
				],
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'grid', 'masonry' ],
								],
								[
									'name' => 'content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'matrix', 'metro' ],
								],
								[
									'name' => 'metro_matrix_content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
					],
				],
				'condition' => [
					'content_layout' => 'side',
				],
				'default' => [
					'size' => '50',
					'unit' => '%',
				],
				'tablet_default' => [
					'size' => '50',
					'unit' => '%',
				],
				'mobile_default' => [
					'size' => '100',
					'unit' => '%',
				],
			]
		);

		$this->add_responsive_control(
			'featured_image_height',
			[
				'label' => esc_html__( 'Height', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ '%', 'px', 'vh' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'default' => [
					'size' => '100',
					'unit' => 'vh',
				],
				'tablet_default' => [
					'size' => '100',
					'unit' => 'vh',
				],
				'mobile_default' => [
					'size' => '100',
					'unit' => 'vh',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-matrix .raven-posts-item.raven-posts-full-width .raven-post-inside' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'general_layout' => 'matrix',
					'metro_matrix_content_layout' => 'overlay',
				],
			]
		);

		$this->add_responsive_control(
			'featured_small_image_height',
			[
				'label' => esc_html__( 'Small Images Height', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ '%', 'px', 'vh' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'default' => [
					'size' => '65',
					'unit' => 'vh',
				],
				'tablet_default' => [
					'size' => '65',
					'unit' => 'vh',
				],
				'mobile_default' => [
					'size' => '65',
					'unit' => 'vh',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-matrix .raven-posts-item:not(.raven-posts-full-width) .raven-post-inside' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'general_layout' => 'matrix',
					'metro_matrix_content_layout' => 'overlay',
				],
			]
		);

		$this->add_control(
			'featured_image_position',
			[
				'label' => esc_html__( 'Image Position', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'left',
				'options' => [
					'left' => esc_html__( 'Left', 'jupiterx-core' ),
					'right' => esc_html__( 'Right', 'jupiterx-core' ),
				],
				'condition' => [
					'general_layout' => [ 'grid', 'masonry' ],
					'content_layout' => 'side',
				],
			]
		);

		$this->add_responsive_control(
			'featured_image_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-image-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} [data-mirrored] .raven-post-inline-left .raven-post-image-wrap' => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
					'{{WRAPPER}} [data-mirrored] .raven-post-inline-right .raven-post-image-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'grid', 'masonry' ],
								],
								[
									'name' => 'content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'matrix', 'metro' ],
								],
								[
									'name' => 'metro_matrix_content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'featured_image_align',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
				'label_block' => false,
				'default' => '',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'jupiterx-core' ),
						'icon' => ' eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-image-wrap' => 'text-align: {{VALUE}};',
				],
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'grid', 'masonry' ],
								],
								[
									'name' => 'content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'matrix', 'metro' ],
								],
								[
									'name' => 'metro_matrix_content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
					],
				],
			]
		);

		$this->start_controls_tabs( 'featured_image_tabs' );

		$this->start_controls_tab(
			'featured_image_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_responsive_control(
			'featured_image_opacity_normal',
			[
				'label' => esc_html__( 'Opacity', 'jupiterx-core' ),
				'type' => 'slider',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.01,
					],
				],
				'default' => [
					'size' => '1',
				],
				'tablet_default' => [
					'size' => '1',
				],
				'mobile_default' => [
					'size' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-image img' => 'opacity: {{SIZE}};',
					'{{WRAPPER}} .content-layout-overlay .raven-posts-zoom-move-wrapper' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'featured_image_border_heading_normal',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'grid', 'masonry' ],
								],
								[
									'name' => 'content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'matrix', 'metro' ],
								],
								[
									'name' => 'metro_matrix_content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'featured_image_border_color_normal',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-post-image' => 'border-color: {{VALUE}};',
				],
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'grid', 'masonry' ],
								],
								[
									'name' => 'content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'matrix', 'metro' ],
								],
								[
									'name' => 'metro_matrix_content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
					],
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'featured_image_border_normal',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .raven-post-image',
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'grid', 'masonry' ],
								],
								[
									'name' => 'content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'matrix', 'metro' ],
								],
								[
									'name' => 'metro_matrix_content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'featured_image_border_radius_normal',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'grid', 'masonry' ],
								],
								[
									'name' => 'content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'matrix', 'metro' ],
								],
								[
									'name' => 'metro_matrix_content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'featured_image_tab_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_responsive_control(
			'featured_image_opacity_hover',
			[
				'label' => esc_html__( 'Opacity', 'jupiterx-core' ),
				'type' => 'slider',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.01,
					],
				],
				'default' => [
					'size' => '1',
				],
				'tablet_default' => [
					'size' => '1',
				],
				'mobile_default' => [
					'size' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-image:hover img' => 'opacity: {{SIZE}};',
					'{{WRAPPER}} .raven-post-inside:hover .raven-post-image img' => 'opacity: {{SIZE}};',
					'{{WRAPPER}} .raven-post-image:hover .raven-posts-zoom-move-wrapper' => 'opacity: {{SIZE}};',
					'{{WRAPPER}} .raven-post-inside:hover .raven-post-image .raven-posts-zoom-move-wrapper' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'featured_image_border_heading_hover',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'grid', 'masonry' ],
								],
								[
									'name' => 'content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'matrix', 'metro' ],
								],
								[
									'name' => 'metro_matrix_content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'featured_image_border_color_hover',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-post-image:hover' => 'border-color: {{VALUE}};',
				],
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'grid', 'masonry' ],
								],
								[
									'name' => 'content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'matrix', 'metro' ],
								],
								[
									'name' => 'metro_matrix_content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
					],
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'featured_image_border_hover',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .raven-post-image:hover',
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'grid', 'masonry' ],
								],
								[
									'name' => 'content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'matrix', 'metro' ],
								],
								[
									'name' => 'metro_matrix_content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'featured_image_border_radius_hover',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-image:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'grid', 'masonry' ],
								],
								[
									'name' => 'content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'matrix', 'metro' ],
								],
								[
									'name' => 'metro_matrix_content_layout',
									'operator' => '!==',
									'value' => 'overlay',
								],
							],
						],
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_overlay_style() {
		$this->start_controls_section(
			'section_overlay',
			[
				'label' => esc_html__( 'Overlay', 'jupiterx-core' ),
				'tab' => 'style',
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'grid', 'masonry' ],
								],
								[
									'name' => 'content_layout',
									'operator' => '===',
									'value' => 'overlay',
								],
							],
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'general_layout',
									'operator' => 'in',
									'value' => [ 'matrix', 'metro' ],
								],
								[
									'name' => 'metro_matrix_content_layout',
									'operator' => '===',
									'value' => 'overlay',
								],
							],
						],
					],
				],
			]
		);

		$this->start_controls_tabs( 'overlay_tabs' );

		$this->start_controls_tab(
			'overlay_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'posts_featured_image_overlay_normal',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Color Type', 'jupiterx-core' ),
						'default' => 'classic',
					],
					'color' => [
						'label' => esc_html__( 'Color', 'jupiterx-core' ),
						'default' => 'rgba(0,0,0,0)',
					],
				],
				'selector' => '{{WRAPPER}} .content-layout-overlay .raven-post .raven-post-image-overlay',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'overlay_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'posts_featured_image_overlay_hover',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Color Type', 'jupiterx-core' ),
						'default' => 'classic',
					],
					'color' => [
						'label' => esc_html__( 'Color', 'jupiterx-core' ),
						'default' => 'rgba(0,0,0,0)',
					],
				],
				'selector' => '{{WRAPPER}} .content-layout-overlay .raven-post .raven-post-image-overlay:before',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'posts_featured_image_overlay_transition_duration',
			[
				'label' => esc_html__( 'Transition Duration', 'jupiterx-core' ),
				'type' => 'slider',
				'default' => [
					'size' => 0.5,
				],
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .content-layout-overlay .raven-post .raven-post-image-overlay' => 'transition-duration: {{SIZE}}s',
					'{{WRAPPER}} .content-layout-overlay .raven-post .raven-post-image-overlay:before' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_title_style() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Post Title', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'posts_title_typography',
				'scheme' => '1',
				'selector' => '{{WRAPPER}} .raven-post-title, {{WRAPPER}} .raven-post-title a',
			]
		);

		$this->add_responsive_control(
			'post_title_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => '0',
					'right' => '23',
					'bottom' => '21',
					'left' => '23',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'post_title_align',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
				'default' => '',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'posts_title_tabs' );

		$this->start_controls_tab(
			'posts_title_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'posts_title_color_normal',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => Utils::set_old_default_value( '#000000' ),
				'global' => Utils::set_default_value( 'primary' ),
				'selectors' => [
					'{{WRAPPER}} .raven-post-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-post-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'posts_title_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'posts_title_color_hover',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-post-title:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-post-title:hover a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'metro_posts_title_heading',
			[
				'label' => esc_html__( 'Metro Post Title', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
				'condition' => [
					'general_layout' => 'metro',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'metro_posts_title_typography',
				'scheme' => '1',
				'selector' => '{{WRAPPER}} .raven-metro-item:not(.raven-posts-full-width) .raven-post-title, {{WRAPPER}} .raven-metro-item:not(.raven-posts-full-width) .raven-post-title a',
				'condition' => [
					'general_layout' => 'metro',
				],
			]
		);

		$this->add_control(
			'matrix_posts_title_heading',
			[
				'label' => esc_html__( 'Matrix Post Title', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
				'condition' => [
					'general_layout' => 'matrix',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'matrix_posts_title_typography',
				'scheme' => '1',
				'selector' => '{{WRAPPER}} .raven-matrix-item:not(.raven-posts-full-width) .raven-post-title, {{WRAPPER}} .raven-matrix-item:not(.raven-posts-full-width) .raven-post-title a',
				'condition' => [
					'general_layout' => 'matrix',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_meta_style() {
		$conditions = [
			'relation' => 'or',
			'terms' => [
				[
					'name' => 'show_categories',
					'operator' => '===',
					'value' => 'yes',
				],
				[
					'name' => 'show_tags',
					'operator' => '===',
					'value' => 'yes',
				],
				[
					'name' => 'show_author',
					'operator' => '===',
					'value' => 'yes',
				],
				[
					'name' => 'show_date',
					'operator' => '===',
					'value' => 'yes',
				],
				[
					'name' => 'show_comments',
					'operator' => '===',
					'value' => 'yes',
				],
			],
		];

		$this->start_controls_section(
			'section_meta',
			[
				'label' => esc_html__( 'Meta', 'jupiterx-core' ),
				'tab' => 'style',
				'conditions' => $conditions,
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'posts_meta_typography',
				'scheme' => '3',
				'conditions' => $conditions,
				'selector' => '{{WRAPPER}} .raven-post-meta, {{WRAPPER}} .raven-post-meta a',
			]
		);

		$this->add_control(
			'posts_meta_divider',
			[
				'label' => esc_html__( 'Meta Divider', 'jupiterx-core' ),
				'type' => 'text',
				'default' => '/',
				'conditions' => $conditions,
			]
		);

		$this->add_responsive_control(
			'posts_meta_divider_spacing',
			[
				'label' => esc_html__( 'Divider Spacing', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'conditions' => $conditions,
				'selectors' => [
					'{{WRAPPER}} .raven-post-meta-divider' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'posts_meta_spacing',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => '32',
					'right' => '23',
					'bottom' => '16',
					'left' => '23',
					'unit' => 'px',
				],
				'conditions' => $conditions,
				'selectors' => [
					'{{WRAPPER}} .raven-post-meta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'posts_meta_margin',
			[
				'label' => esc_html__( 'Margin', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => '0',
					'right' => '0',
					'bottom' => '20',
					'left' => '0',
					'unit' => 'px',
				],
				'conditions' => $conditions,
				'selectors' => [
					'{{WRAPPER}} .raven-post-meta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'posts_meta_align',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
				'default' => '',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'conditions' => $conditions,
				'selectors' => [
					'{{WRAPPER}} .raven-post-meta' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'posts_meta_tabs' );

		$this->start_controls_tab(
			'posts_meta_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'posts_meta_color_normal',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => Utils::set_old_default_value( '#000000' ),
				'global' => Utils::set_default_value( 'text' ),
				'conditions' => $conditions,
				'selectors' => [
					'{{WRAPPER}} .raven-post-meta' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'posts_meta_links_color_normal',
			[
				'label' => esc_html__( 'Links Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => Utils::set_old_default_value( '#000000' ),
				'global' => Utils::set_default_value( 'text' ),
				'conditions' => $conditions,
				'selectors' => [
					'{{WRAPPER}} .raven-post-meta a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'posts_meta_divider_color_normal',
			[
				'label' => esc_html__( 'Divider Color', 'jupiterx-core' ),
				'type' => 'color',
				'conditions' => $conditions,
				'selectors' => [
					'{{WRAPPER}} .raven-post-meta .raven-post-meta-divider' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'posts_meta_tab_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'posts_meta_color_hover',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'conditions' => $conditions,
				'selectors' => [
					'{{WRAPPER}} .raven-post-meta:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'posts_meta_links_color_hover',
			[
				'label' => esc_html__( 'Links Color', 'jupiterx-core' ),
				'type' => 'color',
				'conditions' => $conditions,
				'selectors' => [
					'{{WRAPPER}} .raven-post-meta a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'posts_meta_divider_color_hover',
			[
				'label' => esc_html__( 'Divider Color', 'jupiterx-core' ),
				'type' => 'color',
				'conditions' => $conditions,
				'selectors' => [
					'{{WRAPPER}} .raven-post-meta:hover .raven-post-meta-divider' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_excerpt_style() {
		$this->start_controls_section(
			'section_excerpt',
			[
				'label' => esc_html__( 'Excerpt', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->add_control(
			'excerpt_length',
			[
				'label' => esc_html__( 'Excerpt Length', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
				'default' => [
					'size' => 18,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 150,
						'step' => 1,
					],
				],
			]
		);

		$this->add_control(
			'custom_excerpt',
			[
				'label' => esc_html__( 'Custom Excerpt', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_on' => esc_html__( 'Yes', 'jupiterx-core' ),
				'label_off' => esc_html__( 'No', 'jupiterx-core' ),
				'default' => 'yes',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'posts_excerpt_typography',
				'scheme' => '3',
				'selector' => '{{WRAPPER}} .raven-post-excerpt',
			]
		);

		$this->add_responsive_control(
			'posts_excerpt_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => '0',
					'right' => '23',
					'bottom' => '27',
					'left' => '23',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-excerpt' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'posts_excerpt_align',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
				'default' => '',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-excerpt' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'posts_excerpt_tabs' );

		$this->start_controls_tab(
			'posts_excerpt_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'posts_excerpt_normal',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => Utils::set_old_default_value( '#555555' ),
				'global' => Utils::set_default_value( 'text' ),
				'selectors' => [
					'{{WRAPPER}} .raven-post-excerpt' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'posts_excerpt_tab_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'post_excerpt_color_hover',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-post-excerpt:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function register_cta_button_style() {
		$this->start_controls_section(
			'section_cta_button',
			[
				'label' => esc_html__( 'CTA Button', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'show_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'posts_cta_button_text',
			[
				'label' => esc_html__( 'Text', 'jupiterx-core' ),
				'type' => 'text',
				'default' => esc_html__( 'Read More', 'jupiterx-core' ),
			]
		);

		$this->add_responsive_control(
			'posts_cta_button_width',
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
						'min' => 1,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-button' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'posts_button_height',
			[
				'label' => esc_html__( 'Height', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', 'em' ],
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
						'min' => 1,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-button' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'posts_cta_button_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-post-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'posts_cta_button_align',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
				'prefix_class' => 'raven%s-button-align-',
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
				'selectors' => [
					'{{WRAPPER}} .raven-post-read-more' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'cta_button_tabs' );

		$this->start_controls_tab(
			'cta_button_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'posts_cta_button_color_normal',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#1890FF',
				'selectors' => [
					'{{WRAPPER}} .raven-post-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'posts_cta_button_typography_normal',
				'scheme' => '3',
				'selector' => '{{WRAPPER}} .raven-post .raven-post-read-more a.raven-post-button',
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'posts_cta_button_background_normal',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background Color Type', 'jupiterx-core' ),
					],
					'color' => [
						'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .raven-post-button',
			]
		);

		$this->add_control(
			'posts_cta_button_border_heading_normal',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'posts_cta_button_border_color_normal',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'posts_cta_button_border_normal_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'posts_cta_button_border_normal',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .raven-post-button',
			]
		);

		$this->add_control(
			'posts_cta_button_border_radius_normal',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'posts_cta_button_box_shadow_normal',
				'selector' => '{{WRAPPER}} .raven-post-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'posts_cta_button_tab_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'posts_cta_button_color_hover',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-post-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'posts_cta_button_typography_hover',
				'scheme' => '3',
				'selector' => '{{WRAPPER}} .raven-post .raven-post-read-more a.raven-post-button:hover',
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'posts_cta_button_background_hover',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background Color Type', 'jupiterx-core' ),
					],
					'color' => [
						'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .raven-post-button:hover',
			]
		);

		$this->add_control(
			'posts_cta_button_border_heading_hover',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'posts_cta_button_border_color_hover',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'posts_cta_button_border_hover_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'posts_cta_button_border_hover',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .raven-post-button:hover',
			]
		);

		$this->add_control(
			'posts_cta_button_border_radius_hover',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'posts_cta_button_box_shadow_hover',
				'selector' => '{{WRAPPER}} .raven-post-button:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function register_pagination_style() {
		$this->start_controls_section(
			'section_pagination',
			[
				'label' => esc_html__( 'Pagination', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'pagination_type!' => 'infinite_load',
					'show_pagination' => 'yes',
				],
			]
		);

		$this->add_control(
			'page_based_pages_visible',
			[
				'label' => esc_html__( 'Pages Visible', 'jupiterx-core' ),
				'type' => 'number',
				'default' => 7,
				'min' => 1,
				'max' => 20,
				'condition' => [
					'pagination_type' => 'page_based',
				],
			]
		);

		$this->add_control(
			'page_based_prev_text',
			[
				'label' => esc_html__( 'Previous Label', 'jupiterx-core' ),
				'type' => 'text',
				'default' => esc_html__( '&laquo; Prev', 'jupiterx-core' ),
				'condition' => [
					'pagination_type' => 'page_based',
				],
			]
		);

		$this->add_control(
			'page_based_next_text',
			[
				'label' => esc_html__( 'Next Label', 'jupiterx-core' ),
				'type' => 'text',
				'default' => esc_html__( 'Next &raquo;', 'jupiterx-core' ),
				'condition' => [
					'pagination_type' => 'page_based',
				],
			]
		);

		$this->add_responsive_control(
			'page_based_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', '%' ],
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
						'min' => 1,
						'max' => 100,
					],
				],
				'condition' => [
					'pagination_type' => 'page_based',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'page_based_space_between',
			[
				'label' => esc_html__( 'Space Between', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
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
						'min' => 1,
						'max' => 100,
					],
				],
				'condition' => [
					'pagination_type' => 'page_based',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-pagination-item' => 'margin-left: calc({{SIZE}}{{UNIT}} / 2); margin-right: calc({{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}} .raven-pagination-prev' => 'margin-left: 0;',
					'{{WRAPPER}} .raven-pagination-next' => 'margin-right: 0;',
				],
			]
		);

		$this->add_responsive_control(
			'page_based_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'condition' => [
					'pagination_type' => 'page_based',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-pagination-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'page_based_align',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
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
				],
				'condition' => [
					'pagination_type' => 'page_based',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-pagination-items' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_page_based' );

		$this->start_controls_tab(
			'tabs_page_based_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
				'condition' => [
					'pagination_type' => 'page_based',
				],
			]
		);

		$this->add_control(
			'page_based_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'pagination_type' => 'page_based',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-pagination-item' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'page_based_typography',
				'scheme' => '3',
				'condition' => [
					'pagination_type' => 'page_based',
				],
				'selector' => '{{WRAPPER}} .raven-pagination-item',
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'page_based_background',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background Color Type', 'jupiterx-core' ),
					],
					'color' => [
						'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
					],
				],
				'condition' => [
					'pagination_type' => 'page_based',
				],
				'selector' => '{{WRAPPER}} .raven-pagination-item',
			]
		);

		$this->add_control(
			'page_based_border_heading',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
				'condition' => [
					'pagination_type' => 'page_based',
				],
			]
		);

		$this->add_control(
			'page_based_border_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'page_based_border_border!' => '',
					'pagination_type' => 'page_based',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-pagination-item' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'page_based_border',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
					],
				],
				'condition' => [
					'pagination_type' => 'page_based',
				],
				'selector' => '{{WRAPPER}} .raven-pagination-item',
			]
		);

		$this->add_control(
			'page_based_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'condition' => [
					'pagination_type' => 'page_based',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-pagination-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'page_based_box_shadow',
				'selector' => '{{WRAPPER}} .raven-pagination-item',
				'condition' => [
					'pagination_type' => 'page_based',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_page_based_active',
			[
				'label' => esc_html__( 'Active', 'jupiterx-core' ),
				'condition' => [
					'pagination_type' => 'page_based',
				],
			]
		);

		$this->add_control(
			'active_page_based_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'pagination_type' => 'page_based',
				],
				'selectors' => [
					'{{WRAPPER}} a.raven-pagination-active, {{WRAPPER}} a.raven-pagination-disabled' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'active_page_based_typography',
				'scheme' => '3',
				'condition' => [
					'pagination_type' => 'page_based',
				],
				'selector' => '{{WRAPPER}} a.raven-pagination-active, {{WRAPPER}} a.raven-pagination-disabled',
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'active_page_based_background',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background Color Type', 'jupiterx-core' ),
					],
					'color' => [
						'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
					],
				],
				'condition' => [
					'pagination_type' => 'page_based',
				],
				'selector' => '{{WRAPPER}} a.raven-pagination-active, {{WRAPPER}} a.raven-pagination-disabled',
			]
		);

		$this->add_control(
			'active_page_based_border_heading',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
				'condition' => [
					'pagination_type' => 'page_based',
				],
			]
		);

		$this->add_control(
			'active_page_based_border_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'active_page_based_border_border!' => '',
					'pagination_type' => 'page_based',
				],
				'selectors' => [
					'{{WRAPPER}} a.raven-pagination-active, {{WRAPPER}} a.raven-pagination-disabled' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'active_page_based_border',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
					],
				],
				'condition' => [
					'pagination_type' => 'page_based',
				],
				'selector' => '{{WRAPPER}} a.raven-pagination-active, {{WRAPPER}} a.raven-pagination-disabled',
			]
		);

		$this->add_control(
			'active_page_based_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'condition' => [
					'pagination_type' => 'page_based',
				],
				'selectors' => [
					'{{WRAPPER}} a.raven-pagination-active, {{WRAPPER}} a.raven-pagination-disabled' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'active_page_based_box_shadow',
				'selector' => '{{WRAPPER}} a.raven-pagination-active, {{WRAPPER}} a.raven-pagination-disabled',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_page_based_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
				'condition' => [
					'pagination_type' => 'page_based',
				],
			]
		);

		$this->add_control(
			'hover_page_based_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'pagination_type' => 'page_based',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-pagination-item:not(.raven-pagination-active):not(.raven-pagination-disabled):hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'hover_page_based_typography',
				'scheme' => '3',
				'condition' => [
					'pagination_type' => 'page_based',
				],
				'selector' => '{{WRAPPER}} .raven-pagination-item:not(.raven-pagination-active):not(.raven-pagination-disabled):hover',
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'hover_page_based_background',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background Color Type', 'jupiterx-core' ),
					],
					'color' => [
						'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
					],
				],
				'condition' => [
					'pagination_type' => 'page_based',
				],
				'selector' => '{{WRAPPER}} .raven-pagination-item:not(.raven-pagination-active):not(.raven-pagination-disabled):hover',
			]
		);

		$this->add_control(
			'hover_page_based_border_heading',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
				'condition' => [
					'pagination_type' => 'page_based',
				],
			]
		);

		$this->add_control(
			'hover_page_based_border_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'hover_page_based_border_border!' => '',
					'pagination_type' => 'page_based',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-pagination-item:not(.raven-pagination-active):not(.raven-pagination-disabled):hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'hover_page_based_border',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
					],
				],
				'condition' => [
					'pagination_type' => 'page_based',
				],
				'selector' => '{{WRAPPER}} .raven-pagination-item:not(.raven-pagination-active):not(.raven-pagination-disabled):hover',
			]
		);

		$this->add_control(
			'hover_page_based_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'condition' => [
					'pagination_type' => 'page_based',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-pagination-item:not(.raven-pagination-active):not(.raven-pagination-disabled):hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'hover_page_based_box_shadow',
				'selector' => '{{WRAPPER}} .raven-pagination-item:not(.raven-pagination-active):not(.raven-pagination-disabled):hover',
				'condition' => [
					'pagination_type' => 'page_based',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'load_more_text',
			[
				'label' => esc_html__( 'Button Label', 'jupiterx-core' ),
				'type' => 'text',
				'default' => esc_html__( 'Load More', 'jupiterx-core' ),
				'condition' => [
					'pagination_type' => 'load_more',
				],
			]
		);

		$this->add_responsive_control(
			'load_more_width',
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
						'min' => 1,
						'max' => 1000,
					],
				],
				'condition' => [
					'pagination_type' => 'load_more',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-load-more-button' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'load_more_height',
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
						'min' => 1,
						'max' => 1000,
					],
				],
				'condition' => [
					'pagination_type' => 'load_more',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-load-more-button' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'load_more_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', '%' ],
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
						'min' => 1,
						'max' => 100,
					],
				],
				'condition' => [
					'pagination_type' => 'load_more',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-load-more' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'load_more_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'condition' => [
					'pagination_type' => 'load_more',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-load-more-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'load_more_align',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
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
				],
				'condition' => [
					'pagination_type' => 'load_more',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-load-more' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_load_more' );

		$this->start_controls_tab(
			'tabs_load_more_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
				'condition' => [
					'pagination_type' => 'load_more',
				],
			]
		);

		$this->add_control(
			'load_more_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'pagination_type' => 'load_more',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-load-more-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'load_more_typography',
				'scheme' => '3',
				'condition' => [
					'pagination_type' => 'load_more',
				],
				'selector' => '{{WRAPPER}} .raven-load-more-button',
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'load_more_background',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background Color Type', 'jupiterx-core' ),
					],
					'color' => [
						'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
					],
				],
				'condition' => [
					'pagination_type' => 'load_more',
				],
				'selector' => '{{WRAPPER}} .raven-load-more-button',
			]
		);

		$this->add_control(
			'load_more_border_heading',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
				'condition' => [
					'pagination_type' => 'load_more',
				],
			]
		);

		$this->add_control(
			'load_more_border_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'load_more_border_border!' => '',
					'pagination_type' => 'load_more',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-load-more-button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'load_more_border',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
					],
				],
				'condition' => [
					'pagination_type' => 'load_more',
				],
				'selector' => '{{WRAPPER}} .raven-load-more-button',
			]
		);

		$this->add_control(
			'load_more_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'condition' => [
					'pagination_type' => 'load_more',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-load-more-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'load_more_box_shadow',
				'condition' => [
					'pagination_type' => 'load_more',
				],
				'selector' => '{{WRAPPER}} .raven-load-more-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_load_more_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
				'condition' => [
					'pagination_type' => 'load_more',
				],
			]
		);

		$this->add_control(
			'hover_load_more_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'pagination_type' => 'load_more',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-load-more-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'hover_load_more_typography',
				'scheme' => '3',
				'condition' => [
					'pagination_type' => 'load_more',
				],
				'selector' => '{{WRAPPER}} .raven-load-more-button:hover',
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'hover_load_more_background',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background Color Type', 'jupiterx-core' ),
					],
					'color' => [
						'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
					],
				],
				'condition' => [
					'pagination_type' => 'load_more',
				],
				'selector' => '{{WRAPPER}} .raven-load-more-button:hover',
			]
		);

		$this->add_control(
			'hover_load_more_border_heading',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
				'condition' => [
					'pagination_type' => 'load_more',
				],
			]
		);

		$this->add_control(
			'hover_load_more_border_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'hover_load_more_border_border!' => '',
					'pagination_type' => 'load_more',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-load-more-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'hover_load_more_border',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
					],
				],
				'condition' => [
					'pagination_type' => 'load_more',
				],
				'selector' => '{{WRAPPER}} .raven-load-more-button:hover',
			]
		);

		$this->add_control(
			'hover_load_more_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'condition' => [
					'pagination_type' => 'load_more',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-load-more-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'hover_load_more_box_shadow',
				'condition' => [
					'pagination_type' => 'load_more',
				],
				'selector' => '{{WRAPPER}} .raven-load-more-button:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function register_sortable_style() {
		$this->start_controls_section(
			'section_sortable',
			[
				'label' => esc_html__( 'Sortable', 'jupiterx-core' ),
				'tab' => 'style',
				'condition'    => [
					'show_sortable' => 'yes',
					'is_archive_template' => '',
				],
			]
		);

		$this->add_responsive_control(
			'sortable_container_spacing',
			[
				'label' => esc_html__( 'Container Spacing', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => '10',
					'right' => '0',
					'bottom' => '20',
					'left' => '0',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-sortable' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'sortable_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-sortable-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'is_archive_template' => '',
				],
			]
		);

		$this->add_responsive_control(
			'sortable_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-sortable-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'sortable_align',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
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
				],
				'selectors' => [
					'{{WRAPPER}} .raven-sortable-items' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_sortable' );

		$this->start_controls_tab(
			'tabs_sortable_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'sortable_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-sortable-item' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'sortable_typography',
				'scheme' => '3',
				'selector' => '{{WRAPPER}} .raven-sortable-item',
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'sortable_background',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background Color Type', 'jupiterx-core' ),
					],
					'color' => [
						'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .raven-sortable-item',
			]
		);

		$this->add_control(
			'sortable_border_heading',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sortable_border_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'sortable_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-sortable-item' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'sortable_border',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .raven-sortable-item',
			]
		);

		$this->add_control(
			'sortable_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-sortable-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'sortable_box_shadow',
				'selector' => '{{WRAPPER}} .raven-sortable-item',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_sortable_active',
			[
				'label' => esc_html__( 'Active', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'active_sortable_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-sortable-active' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'active_sortable_typography',
				'scheme' => '3',
				'selector' => '{{WRAPPER}} .raven-sortable-active',
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'active_sortable_background',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background Color Type', 'jupiterx-core' ),
					],
					'color' => [
						'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .raven-sortable-active',
			]
		);

		$this->add_control(
			'active_sortable_border_heading',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'active_sortable_border_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'active_sortable_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-sortable-active' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'active_sortable_border',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .raven-sortable-active',
			]
		);

		$this->add_control(
			'active_sortable_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-sortable-active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'active_sortable_box_shadow',
				'selector' => '{{WRAPPER}} .raven-sortable-active',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_sortable_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'hover_sortable_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-sortable-item:not(.raven-sortable-active):hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'hover_sortable_typography',
				'scheme' => '3',
				'selector' => '{{WRAPPER}} .raven-sortable-item:not(.raven-sortable-active):hover',
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'hover_sortable_background',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background Color Type', 'jupiterx-core' ),
					],
					'color' => [
						'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .raven-sortable-item:not(.raven-sortable-active):hover',
			]
		);

		$this->add_control(
			'hover_sortable_border_heading',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'hover_sortable_border_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'condition' => [
					'hover_sortable_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-sortable-item:not(.raven-sortable-active):hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'hover_sortable_border',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .raven-sortable-item:not(.raven-sortable-active):hover',
			]
		);

		$this->add_control(
			'hover_sortable_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-sortable-item:not(.raven-sortable-active):hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'hover_sortable_box_shadow',
				'selector' => '{{WRAPPER}} .raven-sortable-item:not(.raven-sortable-active):hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_author_apotlight_style() {
		$this->start_controls_section(
			'section_author_spotlight',
			[
				'label' => esc_html__( 'Author Spotlight', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'author_spotlight' => 'yes',
				],
			]
		);

		$this->add_control(
			'author_spotlight_name_heading',
			[
				'label' => esc_html__( 'Name', 'jupiterx-core' ),
				'type' => 'heading',
			]
		);

		$this->add_control(
			'author_spotlight_name_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .raven-post-author-spotlight' => 'color: {{VALUE}};',
					'{{WRAPPER}} .raven-post-author-spotlight a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'author_spotlight_name_typography',
				'scheme' => '3',
				'selector' => '{{WRAPPER}} .raven-post-author-spotlight a',
			]
		);

		$this->add_responsive_control(
			'author_spotlight_name_gap',
			[
				'label' => esc_html__( 'Gap', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => '18',
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => '18',
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => '18',
					'unit' => 'px',
				],
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}} .raven-post-author-spotlight img' => 'margin-right: {{SIZE}}{{UNIT}};',
					'body.rtl {{WRAPPER}} .raven-post-author-spotlight img' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'author_spotlight_name_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => '20',
					'right' => '23',
					'bottom' => '20',
					'left' => '23',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-author-spotlight' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'author_spotlight_image_heading',
			[
				'label' => esc_html__( 'Image', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'author_spotlight_image_width',
			[
				'label' => esc_html__( 'Image Size', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ '%', 'px' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'default' => [
					'size' => '38',
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => '38',
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => '38',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-author-spotlight img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'author_spotlight_divider_heading',
			[
				'label' => esc_html__( 'Divider', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'author_spotlight_divider_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#EEEEEE',
				'selectors' => [
					'{{WRAPPER}} .raven-post-author-spotlight' => 'border-top-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'author_spotlight_divider',
			[
				'label' => esc_html__( 'Weight', 'jupiterx-core' ),
				'type' => 'slider',
				'default' => [
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-author-spotlight' => 'border-top-width: {{SIZE}}px;',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function register_tags_style() {
		$this->start_controls_section(
			'section_tags',
			[
				'label' => esc_html__( 'Tags', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'show_image' => 'yes',
					'show_tags' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'posts_tag_items_gap',
			[
				'label' => esc_html__( 'Items Gap', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 7,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-tags li' => 'padding-left: calc( {{SIZE}}{{UNIT}} / 2 ); padding-right: calc( {{SIZE}}{{UNIT}} / 2 );',
					'{{WRAPPER}} .raven-post-tags' => 'margin-left: calc( -{{SIZE}}{{UNIT}} / 2 ); margin-right: calc( -{{SIZE}}{{UNIT}} / 2 );',
				],
			]
		);

		$this->add_responsive_control(
			'posts_tag_rows_gap',
			[
				'label' => esc_html__( 'Rows Gap', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 7,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-tags li' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tags_alignment',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
				'label_block' => false,
				'default' => '',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-posts-item .raven-post-tags' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'tags_horizontal_offset',
			[
				'label' => esc_html__( 'Horizontal Offset', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', '%' ],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
					],
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'default' => [
					'size' => '14',
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => '14',
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => '14',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-meta-item.raven-post-tags' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tags_vertical_offset',
			[
				'label' => esc_html__( 'Vertical Offset', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', '%' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default' => [
					'size' => '14',
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => '14',
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => '14',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-meta-item.raven-post-tags' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'posts_tags_typography',
				'scheme' => '3',
				'selector' => '{{WRAPPER}} .raven-post .raven-post-tags li a',
			]
		);

		$this->start_controls_tabs( 'tags_tabs' );

		$this->start_controls_tab(
			'tags_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'posts_tags_color_normal',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .raven-post-tags a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'posts_tags_background_normal',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Color Type', 'jupiterx-core' ),
						'default' => 'classic',
					],
					'color' => [
						'label' => esc_html__( 'Color', 'jupiterx-core' ),
						'default' => 'rgba(0, 0, 0, 0.7)',
					],
				],
				'selector' => '{{WRAPPER}} .raven-post-tags a',
			]
		);

		$this->add_control(
			'posts_tags_border_normal_heading',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'posts_tags_border_normal',
				'placeholder' => '1px',
				'selector' => '{{WRAPPER}} .raven-post-tags a',
			]
		);

		$this->add_control(
			'posts_tags_border_radius_normal',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'default' => [
					'top' => '4',
					'right' => '4',
					'bottom' => '4',
					'left' => '4',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-tags a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'posts_tags_box_shadow_normal',
				'selector' => '{{WRAPPER}} .raven-post-tags a',
			]
		);

		$this->add_responsive_control(
			'posts_tags_padding_normal',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => '5',
					'right' => '7',
					'bottom' => '5',
					'left' => '7',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-tags a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tags_tab_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'posts_tags_color_hover',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-post-tags a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'posts_tags_background_hover',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Color Type', 'jupiterx-core' ),
						'default' => 'classic',
					],
					'color' => [
						'label' => esc_html__( 'Color', 'jupiterx-core' ),
						'default' => 'rgba(0, 0, 0, 0.7)',
					],
				],
				'selector' => '{{WRAPPER}} .raven-post-tags a:hover',
			]
		);

		$this->add_control(
			'posts_tags_border_hover_heading',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'posts_tags_border_hover',
				'placeholder' => '1px',
				'selector' => '{{WRAPPER}} .raven-post-tags a:hover',
			]
		);

		$this->add_control(
			'posts_tags_border_radius_hover',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-post-tags a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'posts_tags_box_shadow_hover',
				'selector' => '{{WRAPPER}} .raven-post-tags a:hover',
			]
		);

		$this->add_responsive_control(
			'posts_tags_padding_hover',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-post-tags a:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function ajax_get_queried_posts( $archive_query ) {
		return ( new Frontend( $this ) )->get_queried_posts( $archive_query );
	}

	protected function render() {
		global $wp_query;

		$settings              = $this->get_settings_for_display();
		$general_layout        = $settings['general_layout'];
		$content_layout        = $settings['content_layout'];
		$featured_image_hover  = ! empty( $settings['featured_image_hover'] ) ? $settings['featured_image_hover'] : '';
		$show_overlay_on_hover = ! empty( $settings['show_overlay_on_hover'] ) ? 'content-layout-overlay-on-hover' : '';
		$archive_query         = ( is_archive() || is_search() ) ? htmlspecialchars( wp_json_encode( $wp_query->query_vars ) ) : '';

		if ( ! empty( $settings['metro_matrix_content_layout'] ) || empty( $settings['content_layout'] ) ) {
			$content_layout = $settings['metro_matrix_content_layout'];
		}

		$this->add_render_attribute(
			'posts-wrapper',
			'class',
			[
				'raven-posts',
				"raven-posts-{$general_layout}",
			]
		);

		$this->add_render_attribute(
			'posts-content', [
				'class' => [
					'advanced-posts-content',
					"content-layout-{$content_layout}",
					"raven-{$general_layout}",
					Utils::get_responsive_class( 'raven-' . $general_layout . '%s-', 'columns', $this->get_settings() ),
					$featured_image_hover,
					$show_overlay_on_hover,
				],
				'data-post-id' => Utils::get_current_post_id(),
				'data-archive-query' => $archive_query,
			]
		);
		?>
		<div <?php echo $this->get_render_attribute_string( 'posts-wrapper' ); ?>>
			<?php echo ( new Frontend( $this ) )->render_sortable(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div <?php echo $this->get_render_attribute_string( 'posts-content' ); ?>>
				<?php ( new Frontend( $this ) )->render_content(); ?>
			</div>
			<?php echo ( new Frontend( $this ) )->render_pagination(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<?php
	}
}
