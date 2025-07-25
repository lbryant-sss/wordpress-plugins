<?php
namespace JupiterX_Core\Raven\Modules\Product_Gallery\Widgets;

use JupiterX_Core\Raven\Base\Base_Widget;
use Elementor\Plugin as Elementor;
use Elementor\Group_Control_Image_Size;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use JupiterX_Core\Raven\Utils;


defined( 'ABSPATH' ) || die();

/**
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Product_Gallery extends Base_Widget {

	/**
	 * Current product object.
	 *
	 * @var object
	 * @since 4.5.0
	 */
	private $current_product;

	public function get_name() {
		return 'raven-product-gallery';
	}

	public function get_title() {
		return esc_html__( 'Product Gallery', 'jupiterx-core' );
	}

	public function get_icon() {
		return 'raven-element-icon raven-element-icon-product-gallery';
	}

	public function get_categories() {
		return [ 'jupiterx-core-raven-woo-elements' ];
	}

	public function get_script_depends() {
		return [
			'wc-single-product',
			'flexslider',
			'zoom',
			'photoswipe',
			'photoswipe-ui-default',
			'jupiterx-core-raven-object-fit',
		];
	}

	public function get_style_depends() {
		return [ 'photoswipe-default-skin' ];
	}

	protected function register_controls() {
		$this->register_section_content();
		$this->register_section_product_image();
		$this->register_section_product_thumbnail();
	}

	private function register_section_content() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'video_enable',
			[
				'type' => 'hidden',
				'default' => jupiterx_core_get_option( 'enable_media_controls' ),
				'frontend_available' => 'true',
			]
		);

		$this->add_control(
			'gallery_layout',
			[
				'label' => esc_html__( 'Gallery Layout', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'standard',
				'options' => [
					'standard' => esc_html__( 'Standard (Slider)', 'jupiterx-core' ),
					'stack' => esc_html__( 'Stack', 'jupiterx-core' ),
				],
				'frontend_available' => 'true',
			]
		);

		$this->add_control(
			'thumbnails',
			[
				'label' => esc_html__( 'Thumbnails', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'horizontal',
				'options' => [
					'horizontal' => esc_html__( 'Horizontal', 'jupiterx-core' ),
					'left' => esc_html__( 'Vertical (Left)', 'jupiterx-core' ),
					'right' => esc_html__( 'Vertical (Right)', 'jupiterx-core' ),
				],
				'condition' => [
					'gallery_layout' => 'standard',
				],
				'frontend_available' => 'true',
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => esc_html__( 'Columns', 'jupiterx-core' ),
				'type' => 'select',
				'default' => '2',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => [
					'1' => esc_html__( '1', 'jupiterx-core' ),
					'2' => esc_html__( '2', 'jupiterx-core' ),
					'3' => esc_html__( '3', 'jupiterx-core' ),
				],
				'condition' => [
					'gallery_layout' => 'stack',
				],
				'frontend_available' => true,
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .raven-product-gallery-stack-wrapper' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				],
			]
		);

		$this->add_control(
			'lightbox', [
				'label'        => esc_html__( 'Lightbox', 'jupiterx-core' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Yes', 'jupiterx-core' ),
				'label_off'    => esc_html__( 'No', 'jupiterx-core' ),
				'return_value' => 'true',
				'default'      => 'true',
				'frontend_available' => 'true',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'zoom', [
				'label'        => esc_html__( 'Zoom', 'jupiterx-core' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Yes', 'jupiterx-core' ),
				'label_off'    => esc_html__( 'No', 'jupiterx-core' ),
				'return_value' => 'true',
				'default'      => 'true',
				'frontend_available' => 'true',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'exclude_featured_image', [
				'label'        => esc_html__( 'Exclude Featured Image', 'jupiterx-core' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Yes', 'jupiterx-core' ),
				'label_off'    => esc_html__( 'No', 'jupiterx-core' ),
				'return_value' => 'true',
				'default'      => '',
				'frontend_available' => 'true',
				'render_type' => 'template',
				'condition' => [
					'gallery_layout' => 'stack',
				],
			]
		);

		$this->add_control(
			'carousel', [
				'label'        => esc_html__( 'Carousel', 'jupiterx-core' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Yes', 'jupiterx-core' ),
				'label_off'    => esc_html__( 'No', 'jupiterx-core' ),
				'return_value' => 'true',
				'default'      => '',
				'frontend_available' => 'true',
				'condition' => [
					'gallery_layout' => 'standard',
				],
				'render_type' => 'template',
			]
		);

		$this->end_controls_section();
	}

	private function register_section_product_image() {
		$this->start_controls_section(
			'section_product_image',
			[
				'label' => esc_html__( 'Product image', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_responsive_control(
			'spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
				'default' => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-gallery-stack-wrapper' => 'grid-gap: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'gallery_layout' => 'stack',
				],
			]
		);

		$this->add_group_control(
			'image-size',
			[
				'name' => 'image',
				'default' => 'woocommerce_thumbnail',
			]
		);

		$this->add_control(
			'enable_aspect_ratio', [
				'label'        => esc_html__( 'Enable Aspect Ratio', 'jupiterx-core' ),
				'type'         => 'switcher',
				'label_on'     => esc_html__( 'Yes', 'jupiterx-core' ),
				'label_off'    => esc_html__( 'No', 'jupiterx-core' ),
				'return_value' => 'true',
				'default'      => '',
				'render_type' => 'template',
				'frontend_available' => true,
				'condition' => [
					'gallery_layout' => 'stack',
				],
			]
		);

		$this->add_responsive_control(
			'aspect_ratio',
			[
				'label' => esc_html__( 'Aspect Ratio', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
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
				'condition' => [
					'gallery_layout' => 'stack',
					'enable_aspect_ratio!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-gallery-stack-wrapper .raven-image-fit' => 'padding-bottom: calc( {{SIZE}} * 100% );',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'image_border',
				'selector' => '{{WRAPPER}} .raven-product-gallery-stack-wrapper li, {{WRAPPER}} .raven-product-gallery-standard .flex-viewport',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-gallery-stack-wrapper li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
					'{{WRAPPER}} .raven-product-gallery-standard .flex-viewport' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'standard_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-gallery-left .flex-viewport' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-product-gallery-left .flex-direction-nav' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-product-gallery-right .flex-viewport' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-product-gallery-right .flex-direction-nav' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-product-gallery-horizontal .flex-viewport' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-product-gallery-horizontal > .flex-direction-nav' => 'top:calc(48.5% - {{SIZE}}{{UNIT}});',
				],
				'condition' => [
					'gallery_layout' => 'standard',
				],
				'frontend_available' => true,
			]
		);

		$this->add_group_control(
			'box-shadow',
			[
				'name' => 'image_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .raven-product-gallery-stack-wrapper li',
				'condition' => [
					'gallery_layout' => 'stack',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_section_product_thumbnail() {
		$this->start_controls_section(
			'section_product_thumbnail',
			[
				'label' => esc_html__( 'Thumbnail', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'gallery_layout' => 'standard',
				],
			]
		);

		$this->add_responsive_control(
			'standard_gallery_image_spacing',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-gallery-wrapper .raven-product-gallery-horizontal .flex-control-thumbs .slick-track' => 'column-gap: {{SIZE}}{{UNIT}}!important;',
					'{{WRAPPER}} .raven-product-gallery-wrapper .raven-product-gallery-left .flex-control-thumbs li' => 'margin-bottom:{{SIZE}}{{UNIT}}!important;',
					'{{WRAPPER}} .raven-product-gallery-wrapper .raven-product-gallery-left .flex-control-thumbs li:last-child' => 'margin-bottom:0 !important;',
					'{{WRAPPER}} .raven-product-gallery-wrapper .raven-product-gallery-right .flex-control-thumbs li' => 'margin-bottom:{{SIZE}}{{UNIT}}!important;',
					'{{WRAPPER}} .raven-product-gallery-wrapper .raven-product-gallery-right .flex-control-thumbs li:last-child' => 'margin-bottom:0 !important;',
				],
				'condition' => [
					'gallery_layout' => 'standard',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'product_thumbnail', //product_thumbnail_size.
				'exclude' => [ 'custom' ],
				'include' => [],
				'default' => 'woocommerce_thumbnail',
				'condition' => [
					'gallery_layout' => 'standard',
				],
			]
		);

		$this->add_responsive_control(
			'product_thumbnail_width',
			[
				'label' => esc_html__( 'Image Width', 'jupiterx-core' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} li.slick-slide' => 'width: inherit !important',
					'{{WRAPPER}} .raven-product-gallery-left ol.flex-control-nav' => 'width: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .raven-product-gallery-right ol.flex-control-nav' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'gallery_layout' => 'standard',
					'thumbnails!' => 'horizontal',
				],
				'render_type' => 'template',
			]
		);

		$this->add_responsive_control(
			'thumbnails_per_row',
			[
				'label' => esc_html__( 'Thumbnails Per Row', 'jupiterx-core' ),
				'type' => Controls_Manager::SELECT,
				'default' => '6',
				'options' => [
					'2' => esc_html__( '2', 'jupiterx-core' ),
					'3' => esc_html__( '3', 'jupiterx-core' ),
					'4' => esc_html__( '4', 'jupiterx-core' ),
					'5' => esc_html__( '5', 'jupiterx-core' ),
					'6' => esc_html__( '6', 'jupiterx-core' ),
					'7' => esc_html__( '7', 'jupiterx-core' ),
					'8' => esc_html__( '8', 'jupiterx-core' ),
				],
				'selectors' => [
					'{{WRAPPER}} .slick-track' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				],
				'condition' => [
					'gallery_layout' => 'standard',
					'thumbnails' => 'horizontal',
				],
			]
		);

		$this->add_responsive_control(
			'standard_gallery_thumbnail_height',
			[
				'label' => esc_html__( 'Thumbnail Height', 'jupiterx-core' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-slide' => 'height: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'gallery_layout' => 'standard',
					'thumbnails' => 'horizontal',
				],
			]
		);

		$this->start_controls_tabs(
			'product_thumbnails_tabs'
		);

		$this->start_controls_tab(
			'product_thumbnails_tabs_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'thumbnail_border',
				'selector' => '{{WRAPPER}} .raven-product-gallery-standard .flex-control-thumbs li',
				'condition' => [
					'gallery_layout' => 'standard',
				],
			]
		);

		$this->add_control(
			'thumbnail_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-gallery-standard .flex-control-thumbs li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
					'{{WRAPPER}} .raven-product-gallery-standard .flex-control-thumbs li img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [
					'gallery_layout' => 'standard',
				],
			]
		);

		$this->add_responsive_control(
			'product_thumbnail_opacity',
			[
				'label' => esc_html__( 'Opacity', 'jupiterx-core' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-gallery-standard .flex-control-thumbs li img' => 'opacity: {{SIZE}} !important;',
				],
				'condition' => [
					'gallery_layout' => 'standard',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'product_thumbnails_tabs_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'thumbnail_border_hover',
				'selector' => '{{WRAPPER}} .raven-product-gallery-standard .flex-control-thumbs li:hover',
				'condition' => [
					'gallery_layout' => 'standard',
				],
			]
		);

		$this->add_control(
			'thumbnail_border_radius_hover',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-gallery-standard .flex-control-thumbs li:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
					'{{WRAPPER}} .raven-product-gallery-standard .flex-control-thumbs li img:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [
					'gallery_layout' => 'standard',
				],
			]
		);

		$this->add_responsive_control(
			'product_thumbnail_opacity_hover',
			[
				'label' => esc_html__( 'Opacity', 'jupiterx-core' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-gallery-standard .flex-control-thumbs li img:hover' => 'opacity: {{SIZE}} !important;',
				],
				'condition' => [
					'gallery_layout' => 'standard',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'product_thumbnails_tabs_selected',
			[
				'label' => esc_html__( 'Selected', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'thumbnail_border_selected',
				'selector' => '{{WRAPPER}} .raven-product-gallery-standard .flex-control-thumbs li.active-slick-slide',
				'condition' => [
					'gallery_layout' => 'standard',
				],
			]
		);

		$this->add_control(
			'thumbnail_border_radius_selected',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-gallery-standard .flex-control-thumbs li.active-slick-slide' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
					'{{WRAPPER}} .raven-product-gallery-standard .flex-control-thumbs li img.flex-active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [
					'gallery_layout' => 'standard',
				],
			]
		);

		$this->add_responsive_control(
			'product_thumbnail_opacity_selected',
			[
				'label' => esc_html__( 'Opacity', 'jupiterx-core' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-product-gallery-standard .flex-control-thumbs li img.flex-active' => 'opacity: {{SIZE}} !important;',
				],
				'condition' => [
					'gallery_layout' => 'standard',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$settings      = $this->get_settings_for_display();
		$options_class = '';

		$this->get_product();

		if ( empty( $this->current_product ) ) {
			return;
		}

		$gallery_ids = $this->current_product->get_gallery_image_ids();

		if ( ! empty( $settings['lightbox'] ) ) {
			$options_class .= 'raven-product-gallery-lightbox ';
		}

		if ( ! empty( $settings['zoom'] ) ) {
			$options_class .= 'raven-product-gallery-zoom ';
		}

		$this->add_render_attribute( 'wrapper', [
			'class' => 'raven-product-gallery-wrapper raven-product-gallery-' . $settings['gallery_layout'] . ' ' . $options_class,
			'data-has-gallery' => ! empty( $gallery_ids ) ? '1' : '0',
		] );

		$this->add_render_attribute( 'images', [
			'class' => 'elementor-clickable',
			'data-elementor-open-lightbox' => $settings['lightbox'],
		] );

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
		<?php
			if ( 'standard' === $settings['gallery_layout'] ) {
				$thumbnails = $settings['thumbnails'];

				if ( empty( $gallery_ids ) ) {
					$thumbnails = 'horizontal';
				}

				echo '<div class="woocommerce-product-gallery-raven-widget raven-product-gallery-' . esc_attr( $thumbnails ) . '">';
				self::render_standard( $settings );
				echo '</div>';
			} else {
				$this->render_stack( $settings );
			}
		?>
		</div>
		<?php
	}

	/**
	 * Get Product object.
	 *
	 * @since 4.5.0
	 * @return void
	 */
	private function get_product() {
		if ( ! empty( $this->current_product ) ) {
			return;
		}

		Utils::get_product();

		global $product;

		if ( empty( $product ) ) {
			return;
		}

		$this->current_product = $product;
	}

	/**
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	private function render_standard( $settings ) {
		$required_options = [];

		if ( ! empty( $settings['zoom'] ) && empty( current_theme_supports( 'wc-product-gallery-zoom' ) ) ) {
			$required_options['data-wc-disable-zoom'] = 1;
		}

		if ( ! empty( $settings['lightbox'] ) && empty( current_theme_supports( 'wc-product-gallery-lightbox' ) ) ) {
			$required_options['data-wc-disable-lightbox'] = 1;

			add_action( 'wp_footer', function() {
				wc_get_template( 'single-product/photoswipe.php' );
			} );
		}

		$variable_form_id = $this->check_if_default_product_variation_selected( $this->current_product );

		$this->add_render_attribute(
			'_wrapper',
			$required_options
		);

		$post_thumbnail_id  = $this->current_product->get_image_id();
		$product_main_image = $post_thumbnail_id;

		// If user has set a default variation for the form.
		if ( false !== $variable_form_id ) {
			$post_thumbnail_id = get_post_thumbnail_id( $variable_form_id );
		}

		// Double check if variation has main picture, if not we get product main image as replacement.
		if ( false !== $variable_form_id && 0 === $post_thumbnail_id ) {
			$post_thumbnail_id = $product_main_image;
		}

		$columns         = 1;
		$wrapper_classes = apply_filters(
			'woocommerce_single_product_image_gallery_classes',
			[
				'raven-product-gallery-slider-wrapper',
				'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
				'woocommerce-product-gallery--columns-1',
				'images',
			]
		);

		add_filter( 'woocommerce_gallery_thumbnail_size', function() use ( $settings ) {
			return $settings['product_thumbnail_size'];
		} );

		add_filter( 'woocommerce_gallery_image_size', function() use ( $settings ) {
			return $settings['image_size'];
		} );

		?>
		<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
			<figure class="woocommerce-product-gallery__wrapper">
				<?php
				if ( $post_thumbnail_id ) {
					$html = wc_get_gallery_image_html( $post_thumbnail_id, true );
				} else {
					$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
					$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'noks-core' ) );
					$html .= '</div>';
				}

				echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

				$attachment_ids = $this->current_product->get_gallery_image_ids();

				if ( false !== $variable_form_id ) {
					$variation_gallery = get_post_meta( $variable_form_id, 'jupiterx_variation_gallery_image_id', true );
					$variation_gallery = explode( ',', $variation_gallery );
					$variation_gallery = array_filter( $variation_gallery, function( $value ) {
						// Remove empty and false values.
						return ! empty( $value ) || 0 === $value || false === $value;
					} );

					if ( count( $variation_gallery ) > 0 ) {
						$attachment_ids = $variation_gallery;
					}
				}

				if ( $attachment_ids && $this->current_product->get_image_id() ) {
					foreach ( $attachment_ids as $attachment_id ) {
						echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', wc_get_gallery_image_html( $attachment_id ), $attachment_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
				}
				?>
			</figure>
			<input type="hidden" name="post_id" value="<?php echo esc_attr( Utils::get_current_post_id() ); ?>" />
			<input type="hidden" name="form_id" value="<?php echo esc_attr( $this->get_id() ); ?>" />
			<input type="hidden" name="product_id" value="<?php echo esc_attr( $this->current_product->get_id() ); ?>" />
		</div>
		<?php
	}

	private function render_stack( $settings ) {
		$images = $this->get_images( $settings );

		if ( empty( $images ) ) {
			$output  = '<input type="hidden" name="post_id" value=" ' . esc_attr( Utils::get_current_post_id() ) . '" />';
			$output .= '<input type="hidden" name="form_id" value="' . esc_attr( $this->get_id() ) . '" />';
			$output .= '<input type="hidden" name="product_id" value="' . esc_attr( $this->current_product->get_id() ) . '" />';
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		$this->add_render_attribute( 'columns', [
			'class' => 'raven-product-gallery-stack-wrapper',
		] );

		$html = '<ul ' . $this->get_render_attribute_string( 'columns' ) . '>';
		foreach ( $images as $image ) {
			$media = $this->render_media( $image, $settings );
			$html .= sprintf(
				'<li class="jupiterx-product-gallery-stack-item %1$s %2$s">%3$s</li>',
				esc_attr( 'jupiterx-product-gallery-stack-' . $media['type'] ),
				! empty( $settings['enable_aspect_ratio'] ) && 'image' === $media['type'] ? 'raven-image-fit' : '',
				$media['content']
			);
		}

		$html .= '</ul>';
		$html .= '<input type="hidden" name="post_id" value=" ' . esc_attr( Utils::get_current_post_id() ) . '" />';
		$html .= '<input type="hidden" name="form_id" value="' . esc_attr( $this->get_id() ) . '" />';
		$html .= '<input type="hidden" name="product_id" value="' . esc_attr( $this->current_product->get_id() ) . '" />';

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Check if current product has a default value for the variable form.
	 *
	 * @param object $product product object.
	 * @since 3.1.0
	 */
	private function check_if_default_product_variation_selected( $product ) {
		$product_id = $product->get_id(); // replace with the ID of the product
		$attributes = get_post_meta( $product_id, '_default_attributes', true );
		$new        = [];

		if ( empty( $attributes ) ) {
			return false;
		}

		foreach ( $attributes as $key => $value ) {
			$new[ 'attribute_' . $key ] = $value;
		}

		$id = ( new \WC_Product_Data_Store_CPT() )->find_matching_product_variation(
			new \WC_Product( $product_id ), $new
		);

		return $id;
	}

	private function get_images( $settings ) {
		$this->get_product();

		$images = [];

		if ( empty( $this->current_product ) ) {
			return;
		}

		$variable_form_id = $this->check_if_default_product_variation_selected( $this->current_product );

		if ( ! empty( $this->current_product->get_image_id() ) && empty( $settings['exclude_featured_image'] ) ) {
			$images[] = $this->current_product->get_image_id();
		}

		$gallery_ids = $this->current_product->get_gallery_image_ids();

		if ( false !== $variable_form_id ) {
			$variation_gallery    = get_post_meta( $variable_form_id, 'jupiterx_variation_gallery_image_id', true );
			$variation_main_image = get_post_thumbnail_id( $variable_form_id );
			$variation_gallery    = explode( ',', $variation_gallery );
			$variation_gallery    = array_filter( $variation_gallery, function( $value ) {
				// Remove empty and false values.
				return ! empty( $value ) || 0 === $value || false === $value;
			} );

			if ( count( $variation_gallery ) > 0 ) {
				$gallery_ids = $variation_gallery;
			}

			if ( 0 !== $variation_main_image ) {
				$images   = [];
				$images[] = $variation_main_image;
			}
		}

		if ( empty( $gallery_ids ) ) {
			return $images;
		}

		foreach ( $gallery_ids as $id ) {
			$images[] = $id;
		}

		return array_unique( $images );
	}

	private function render_media( $image_id, $settings ) {
		if ( empty( jupiterx_get_option( 'enable_media_controls', 0 ) ) ) {
			return [
				'content' => $this->get_thumbnail( $image_id, $settings ),
				'type' => 'image',
			];
		}

		$data = get_post_meta( $image_id, '_jupiterx_attachment_meta', true );

		if ( empty( $data ) ) {
			return [
				'content' => $this->get_thumbnail( $image_id, $settings ),
				'type' => 'image',
			];
		}

		return [
			'content' => $this->get_video( $image_id, $data, $settings ),
			'type' => 'video',
		];
	}

	private function get_thumbnail( $image, $settings ) {
		$image_src = Group_Control_Image_Size::get_attachment_image_src( $image, 'image', $settings );

		$image_data = [
			'id' => $image,
			'url' => $image_src,
		];

		$image_tag = sprintf(
			'<img class="%4$s" src="%1$s" title="%2$s" alt="%3$s" %5$s />',
			esc_attr( $image_src ),
			Control_Media::get_image_title( $image ),
			Control_Media::get_image_alt( $image_data ),
			'raven-product-gallery-stack-image',
			$this->get_image_size( $image, $settings )
		);

		if ( ! empty( $settings['lightbox'] ) ) {
			$result = sprintf(
				'<a %1$s href="%2$s">%3$s</a>',
				$this->get_render_attribute_string( 'images' ),
				$this->get_thumbnail_src( $image ),
				$image_tag
			);

			return $result;
		}

		return $image_tag;
	}

	private function get_video( $image, $data, $settings ) {
		if ( ! class_exists( 'JupiterX_Core_Product_Gallery_Video' ) ) {
			return $this->get_thumbnail( $image, $settings );
		}

		$video_gallery = new \JupiterX_Core_Product_Gallery_Video();
		$video_content = $video_gallery->get_meta_data( $data, $image );

		if ( empty( $video_content ) ) {
			return $this->get_thumbnail( $image, $settings );
		}

		return $video_content['content'];
	}

	private function get_thumbnail_src( $image ) {
		if ( empty( $image ) ) {
			return;
		}

		$image = wp_get_attachment_image_src( $image, 'full' );

		if ( ! isset( $image[0] ) ) {
			return '';
		}

		return $image[0];
	}

	/**
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	private function get_image_size( $image, $settings ) {
		$size      = $settings['image_size'];
		$dimension = [];

		$image_attachment_src = wp_get_attachment_image_src( $image, 'full' );

		$original_image = [
			'data-src' => isset( $image_attachment_src[0] ) ? $image_attachment_src[0] : '',
			'data-large_image_width' => isset( $image_attachment_src[1] ) ? $image_attachment_src[1] : '',
			'data-large_image_height' => isset( $image_attachment_src[2] ) ? $image_attachment_src[2] : '',
		];

		if ( 'custom' === $size ) {
			$image_size = $settings['image_custom_dimension'];

			$dimension = [
				'width' => ! empty( $image_size['width'] ) ? $image_size['width'] : 0,
				'height' => ! empty( $image_size['height'] ) ? $image_size['width'] : 0,
			];
		} else {
			$image_size = image_downsize( $image, $size );

			$dimension = [
				'width' => ! empty( $image_size[1] ) ? $image_size[1] : 0,
				'height' => ! empty( $image_size[2] ) ? $image_size[2] : 0,
			];
		}

		$dimension = array_merge( $dimension, $original_image );

		if ( empty( $dimension ) ) {
			return;
		}

		$attribute = '';

		foreach ( $dimension as $key => $value ) {
			if ( empty( $value ) ) {
				continue;
			}

			$attribute .= $key . '="' . $value . '" ';
		}

		return $attribute;
	}
}
