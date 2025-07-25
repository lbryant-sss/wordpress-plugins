<?php
/**
 * Premium Banner.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Controls\Premium_Post_Filter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Banner
 */
class Premium_Banner extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-addon-banner';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Banner', 'premium-addons-for-elementor' );
	}

	/**
	 * Retrieve Widget Icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string widget icon.
	 */
	public function get_icon() {
		return 'pa-banner';
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return array( 'pa', 'premium', 'premium banner', 'image', 'box', 'info', 'cta' );
	}

	protected function is_dynamic_content(): bool {
		return false;
	}

	public function has_widget_inner_wrapper(): bool {
		return ! Helper_Functions::check_elementor_experiment( 'e_optimized_markup' );
	}

	/**
	 * Retrieve Widget Categories.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'premium-elements' );
	}

	/**
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS style handles.
	 */
	public function get_style_depends() {
		return array(
			'pa-btn',
			'premium-addons',
		);
	}

	/**
	 * Retrieve Widget Dependent JS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array JS script handles.
	 */
	public function get_script_depends() {
		return array(
			'pa-tilt',
			'premium-addons',
		);
	}

	/**
	 * Retrieve Widget Support URL.
	 *
	 * @access public
	 *
	 * @return string support URL.
	 */
	public function get_custom_help_url() {
		return 'https://premiumaddons.com/support/';
	}

	/**
	 * Register Banner controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {  // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'premium_banner_global_settings',
			array(
				'label' => __( 'Image', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_banner_image',
			array(
				'label'         => __( 'Upload Image', 'premium-addons-for-elementor' ),
				'description'   => __( 'Select an image for the Banner', 'premium-addons-for-elementor' ),
				'type'          => Controls_Manager::MEDIA,
				'dynamic'       => array( 'active' => true ),
				'default'       => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'show_external' => true,
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'thumbnail',
				'default'   => 'full',
				'separator' => 'none',
			)
		);

		$this->add_control(
			'premium_banner_link_url_switch',
			array(
				'label' => __( 'Link', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_banner_image_link_switcher',
			array(
				'label'     => __( 'Custom Link', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_banner_link_url_switch' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_banner_image_custom_link',
			array(
				'label'         => __( 'Set custom Link', 'premium-addons-for-elementor' ),
				'type'          => Controls_Manager::URL,
				'dynamic'       => array( 'active' => true ),
				'condition'     => array(
					'premium_banner_image_link_switcher' => 'yes',
					'premium_banner_link_url_switch'     => 'yes',
				),
				'show_external' => false,
			)
		);

		$this->add_control(
			'premium_banner_image_existing_page_link',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-for-elementor' ),
				'type'        => Premium_Post_Filter::TYPE,
				'label_block' => true,
				'multiple'    => false,
				'source'      => array( 'post', 'page' ),
				'condition'   => array(
					'premium_banner_image_link_switcher!' => 'yes',
					'premium_banner_link_url_switch'      => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_banner_image_animation',
			array(
				'label'   => __( 'Effect', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'animation1',
				'options' => array(
					'animation1'  => __( 'Effect 1', 'premium-addons-for-elementor' ),
					'animation5'  => __( 'Effect 2', 'premium-addons-for-elementor' ),
					'animation13' => __( 'Effect 3', 'premium-addons-for-elementor' ),
					'animation2'  => __( 'Effect 4', 'premium-addons-for-elementor' ),
					'animation4'  => __( 'Effect 5', 'premium-addons-for-elementor' ),
					'animation6'  => __( 'Effect 6', 'premium-addons-for-elementor' ),
					'animation7'  => __( 'Effect 7', 'premium-addons-for-elementor' ),
					'animation8'  => __( 'Effect 8', 'premium-addons-for-elementor' ),
					'animation9'  => __( 'Effect 9', 'premium-addons-for-elementor' ),
					'animation10' => __( 'Effect 10', 'premium-addons-for-elementor' ),
					'animation11' => __( 'Effect 11', 'premium-addons-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'premium_banner_active',
			array(
				'label' => __( 'Always Hovered', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_banner_hover_effect',
			array(
				'label'   => __( 'Hover Effect', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'none'      => __( 'None', 'premium-addons-for-elementor' ),
					'zoomin'    => __( 'Zoom In', 'premium-addons-for-elementor' ),
					'zoomout'   => __( 'Zoom Out', 'premium-addons-for-elementor' ),
					'scale'     => __( 'Scale', 'premium-addons-for-elementor' ),
					'grayscale' => __( 'Grayscale', 'premium-addons-for-elementor' ),
					'blur'      => __( 'Blur', 'premium-addons-for-elementor' ),
					'bright'    => __( 'Bright', 'premium-addons-for-elementor' ),
					'sepia'     => __( 'Sepia', 'premium-addons-for-elementor' ),
				),
				'default' => 'none',
			)
		);

		$this->add_control(
			'premium_banner_height',
			array(
				'label'   => __( 'Height', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'default' => __( 'Default', 'premium-addons-for-elementor' ),
					'custom'  => __( 'Custom', 'premium-addons-for-elementor' ),
				),
				'default' => 'default',
			)
		);

		$this->add_responsive_control(
			'premium_banner_custom_height',
			array(
				'label'      => __( 'Min Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'vh', 'custom' ),
				'condition'  => array(
					'premium_banner_height' => 'custom',
				),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 600,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-banner-ib img' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_fit',
			array(
				'label'     => __( 'Image Fit', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'cover'   => __( 'Cover', 'premium-addons-for-elementor' ),
					'fill'    => __( 'Fill', 'premium-addons-for-elementor' ),
					'contain' => __( 'Contain', 'premium-addons-for-elementor' ),
				),
				'default'   => 'fill',
				'selectors' => array(
					'{{WRAPPER}} .premium-banner-ib img' => 'object-fit: {{VALUE}}',
				),
				'condition' => array(
					'premium_banner_height' => 'custom',
				),
			)
		);

		$this->add_control(
			'mouse_tilt',
			array(
				'label'        => __( 'Enable Mouse Tilt', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-banner-tilt-',
			)
		);

		$this->add_control(
			'mouse_tilt_rev',
			array(
				'label'        => __( 'Reverse', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-banner-tilt-rev-',
				'condition'    => array(
					'mouse_tilt' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_banner_extra_class',
			array(
				'label'   => __( 'Extra Class', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_banner_image_section',
			array(
				'label' => __( 'Content', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_banner_title',
			array(
				'label'       => __( 'Title', 'premium-addons-for-elementor' ),
				'placeholder' => __( 'Give a title to this banner', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => __( 'Premium Banner', 'premium-addons-for-elementor' ),
				'label_block' => false,
			)
		);

		$this->add_control(
			'premium_banner_title_tag',
			array(
				'label'       => __( 'HTML Tag', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'h3',
				'options'     => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_banner_description_hint',
			array(
				'label' => __( 'Description', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'premium_banner_description',
			array(
				'label'       => __( 'Description', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::WYSIWYG,
				'dynamic'     => array( 'active' => true ),
				'default'     => __( 'Premium Banner gives you a wide range of styles and options that you will definitely fall in love with', 'premium-addons-for-elementor' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_banner_title_text_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'left',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-banner-ib-title, {{WRAPPER}} .premium-banner-ib-content, {{WRAPPER}} .premium-banner-read-more'   => 'text-align: {{VALUE}} ;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'button_section',
			array(
				'label'     => __( 'Button', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_banner_link_url_switch!' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_banner_link_switcher',
			array(
				'label' => __( 'Button', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_banner_more_text',
			array(
				'label'     => __( 'Text', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'default'   => 'Click Here',
				'condition' => array(
					'premium_banner_link_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_banner_link_selection',
			array(
				'label'       => __( 'Link Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'url'  => __( 'URL', 'premium-addons-for-elementor' ),
					'link' => __( 'Existing Page', 'premium-addons-for-elementor' ),
				),
				'default'     => 'url',
				'label_block' => true,
				'condition'   => array(
					'premium_banner_link_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_banner_link',
			array(
				'label'       => __( 'Link', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => '#',
				),
				'placeholder' => 'https://premiumaddons.com/',
				'label_block' => true,
				'condition'   => array(
					'premium_banner_link_selection' => 'url',
					'premium_banner_link_switcher'  => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_banner_existing_link',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-for-elementor' ),
				'type'        => Premium_Post_Filter::TYPE,
				'label_block' => true,
				'multiple'    => false,
				'source'      => array( 'post', 'page' ),
				'condition'   => array(
					'premium_banner_link_selection' => 'link',
					'premium_banner_link_switcher'  => 'yes',
				),
			)
		);

		Helper_Functions::add_btn_hover_controls( $this, array( 'premium_banner_link_switcher' => 'yes' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_banner_responsive_section',
			array(
				'label' => __( 'Responsive', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_banner_responsive_switcher',
			array(
				'label'       => __( 'Responsive Controls', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'If the description text is not suiting well on specific screen sizes, you may enable this option which will hide the description text.', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_banner_min_range',
			array(
				'label'       => __( 'Minimum Size', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Note: minimum size for extra small screens is 1px.', 'premium-addons-for-elementor' ),
				'default'     => 1,
				'condition'   => array(
					'premium_banner_responsive_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_banner_max_range',
			array(
				'label'       => __( 'Maximum Size', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Note: maximum size for extra small screens is 767px.', 'premium-addons-for-elementor' ),
				'default'     => 767,
				'condition'   => array(
					'premium_banner_responsive_switcher' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Help & Docs', 'premium-addons-for-elementor' ),
			)
		);

		$doc1_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/elementor-banner-widget-tutorial/', 'banner-widget', 'wp-editor', 'get-support' );

		$this->add_control(
			'doc_1',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc1_url, __( 'Getting started »', 'premium-addons-for-elementor' ) ),
				'content_classes' => 'editor-pa-doc',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_banner_opacity_style',
			array(
				'label' => __( 'General', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_banner_image_bg_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-banner-ib' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_banner_image_opacity',
			array(
				'label'     => __( 'Image Opacity', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 1,
				),
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => .1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-banner-ib img' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_control(
			'premium_banner_image_hover_opacity',
			array(
				'label'     => __( 'Hover Opacity', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 1,
				),
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => .1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}:hover .premium-banner-ib img' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_control(
			'premium_banner_title_border_width',
			array(
				'label'      => __( 'Hover Border Width', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'condition'  => array(
					'premium_banner_image_animation' => array( 'animation13', 'animation9', 'animation10' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-banner-animation13 .premium-banner-ib-title::after'    => 'height: {{size}}{{unit}};',
					'{{WRAPPER}} .premium-banner-animation9 .premium-banner-ib-desc::before, {{WRAPPER}} .premium-banner-animation9 .premium-banner-ib-desc::after'    => 'height: {{size}}{{unit}};',
					'{{WRAPPER}} .premium-banner-animation10 .premium-banner-ib-title::after'    => 'height: {{size}}{{unit}};',
				),
			)
		);

		$this->add_control(
			'premium_banner_style3_title_border',
			array(
				'label'     => __( 'Hover Border Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'premium_banner_image_animation' => array( 'animation13', 'animation9', 'animation10' ),
				),
				'separator' => 'after',
				'selectors' => array(
					'{{WRAPPER}} .premium-banner-animation13 .premium-banner-ib-title::after'    => 'background: {{VALUE}};',
					'{{WRAPPER}} .premium-banner-animation9 .premium-banner-ib-desc::before, {{WRAPPER}} .premium-banner-animation9 .premium-banner-ib-desc::after'    => 'background: {{VALUE}};',
					'{{WRAPPER}} .premium-banner-animation10 .premium-banner-ib-title::after'    => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_banner_inner_border_width',
			array(
				'label'      => __( 'Hover Border Width', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'condition'  => array(
					'premium_banner_image_animation' => array( 'animation4', 'animation6', 'animation7', 'animation8' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-banner-animation4 .premium-banner-ib-desc::after, {{WRAPPER}} .premium-banner-animation4 .premium-banner-ib-desc::before, {{WRAPPER}} .premium-banner-animation6 .premium-banner-ib-desc::before' => 'border-width: {{size}}{{unit}};',
					'{{WRAPPER}} .premium-banner-animation7 .premium-banner-br.premium-banner-bleft, {{WRAPPER}} .premium-banner-animation7 .premium-banner-br.premium-banner-bright , {{WRAPPER}} .premium-banner-animation8 .premium-banner-br.premium-banner-bright,{{WRAPPER}} .premium-banner-animation8 .premium-banner-br.premium-banner-bleft' => 'width: {{size}}{{unit}};',
					'{{WRAPPER}} .premium-banner-animation7 .premium-banner-br.premium-banner-btop, {{WRAPPER}} .premium-banner-animation7 .premium-banner-br.premium-banner-bottom , {{WRAPPER}} .premium-banner-animation8 .premium-banner-br.premium-banner-bottom,{{WRAPPER}} .premium-banner-animation8 .premium-banner-br.premium-banner-btop ' => 'height: {{size}}{{unit}};',
				),
			)
		);

		$this->add_control(
			'premium_banner_scaled_border_color',
			array(
				'label'     => __( 'Hover Border Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'premium_banner_image_animation' => array( 'animation4', 'animation6', 'animation7', 'animation8' ),
				),
				'separator' => 'after',
				'selectors' => array(
					'{{WRAPPER}} .premium-banner-animation4 .premium-banner-ib-desc::after, {{WRAPPER}} .premium-banner-animation4 .premium-banner-ib-desc::before, {{WRAPPER}} .premium-banner-animation6 .premium-banner-ib-desc::before' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .premium-banner-animation7 .premium-banner-br, {{WRAPPER}} .premium-banner-animation8 .premium-banner-br' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .premium-banner-ib img',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'hover_css_filters',
				'label'    => __( 'Hover CSS Filters', 'premium-addons-for-elementor' ),
				'selector' => '{{WRAPPER}}:hover .premium-banner-ib img',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_banner_image_border',
				'selector' => '{{WRAPPER}} .premium-banner-ib',
			)
		);

		$this->add_responsive_control(
			'premium_banner_image_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-banner-ib' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'image_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'image_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>' . __( '. See ', 'premium-addons-for-elementor' ) . '<a href="https://www.youtube.com/watch?v=S0BJazLHV-M" target="_blank">tutorial</a>',
			)
		);

		$this->add_control(
			'image_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-banner-ib' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'image_adv_radius' => 'yes',
				),
			)
		);

		$this->add_control(
			'blend_mode',
			array(
				'label'     => __( 'Blend Mode', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''            => __( 'Normal', 'premium-addons-for-elementor' ),
					'multiply'    => 'Multiply',
					'screen'      => 'Screen',
					'overlay'     => 'Overlay',
					'darken'      => 'Darken',
					'lighten'     => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation'  => 'Saturation',
					'color'       => 'Color',
					'luminosity'  => 'Luminosity',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .premium-banner-ib' => 'mix-blend-mode: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_banner_title_style',
			array(
				'label' => __( 'Title', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_banner_color_of_title',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-banner-ib-desc .premium_banner_title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_banner_title_typography',
				'selector' => '{{WRAPPER}} .premium-banner-ib-desc .premium_banner_title',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
			)
		);

		$this->add_control(
			'premium_banner_style2_title_bg',
			array(
				'label'       => __( 'Background', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#f2f2f2',
				'description' => __( 'Choose a background color for the title', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'premium_banner_image_animation' => 'animation5',
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-banner-animation5 .premium-banner-ib-desc'    => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'premium_banner_title_shadow',
				'selector' => '{{WRAPPER}} .premium-banner-ib-desc .premium_banner_title',
			)
		);

		$this->add_responsive_control(
			'premium_banner_title_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-banner-ib-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_banner_styles_of_content',
			array(
				'label' => __( 'Description', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_banner_color_of_content',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium_banner_content' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_banner_content_typhography',
				'selector' => '{{WRAPPER}} .premium_banner_content',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'premium_banner_description_shadow',
				'selector' => '{{WRAPPER}} .premium_banner_content',
			)
		);

		$this->add_responsive_control(
			'premium_banner_desc_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-banner-ib-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'button_style_section',
			array(
				'label'     => __( 'Button', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_banner_link_switcher'    => 'yes',
					'premium_banner_link_url_switch!' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_banner_button_typhography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .premium-banner-link',
			)
		);

		$this->start_controls_tabs( 'button_style_tabs' );

		$this->start_controls_tab(
			'button_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_banner_color_of_button',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-banner-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_banner_backcolor_of_button',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-banner-link, {{WRAPPER}} .premium-button-style2-shutinhor:before , {{WRAPPER}} .premium-button-style2-shutinver:before , {{WRAPPER}} .premium-button-style5-radialin:before , {{WRAPPER}} .premium-button-style5-rectin:before' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_banner_button_border',
				'selector' => '{{WRAPPER}} .premium-banner-link',
			)
		);

		$this->add_control(
			'premium_banner_button_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-banner-link' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'button_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'button_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>' . __( '. See ', 'premium-addons-for-elementor' ) . '<a href="https://www.youtube.com/watch?v=S0BJazLHV-M" target="_blank">tutorial</a>',
			)
		);

		$this->add_control(
			'button_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-banner-link' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'button_adv_radius' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'premium_banner_button_shadow',
				'selector' => '{{WRAPPER}} .premium-banner-link',
			)
		);

		$this->add_responsive_control(
			'premium_banner_button_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-banner-read-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_banner_button_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-banner-link, {{WRAPPER}} .premium-button-line6::after' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_banner_hover_color_of_button',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-banner-link:hover, {{WRAPPER}} .premium-button-line6::after' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'underline_color',
			array(
				'label'     => __( 'Line Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-btn-svg' => 'stroke: {{VALUE}};',
					'{{WRAPPER}} .premium-button-line2::before, {{WRAPPER}} .premium-button-line4::before, {{WRAPPER}} .premium-button-line5::before, {{WRAPPER}} .premium-button-line5::after, {{WRAPPER}} .premium-button-line6::before, {{WRAPPER}} .premium-button-line7::before' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'premium_button_hover_effect' => 'style8',
				),
			)
		);

		$this->add_control(
			'first_layer_hover',
			array(
				'label'     => __( 'Layer #1 Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-button-style7 .premium-button-text-icon-wrapper:before' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'premium_button_hover_effect' => 'style7',

				),
			)
		);

		$this->add_control(
			'second_layer_hover',
			array(
				'label'     => __( 'Layer #2 Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-button-style7 .premium-button-text-icon-wrapper:after' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'premium_button_hover_effect' => 'style7',
				),
			)
		);

		$this->add_control(
			'premium_banner_hover_backcolor_of_button',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-button-none:hover, {{WRAPPER}} .premium-button-style8:hover, {{WRAPPER}} .premium-button-style1:before, {{WRAPPER}} .premium-button-style2-shutouthor:before, {{WRAPPER}} .premium-button-style2-shutoutver:before, {{WRAPPER}} .premium-button-style2-shutinhor, {{WRAPPER}} .premium-button-style2-shutinver, {{WRAPPER}} .premium-button-style2-dshutinhor:before, {{WRAPPER}} .premium-button-style2-dshutinver:before, {{WRAPPER}} .premium-button-style2-scshutouthor:before, {{WRAPPER}} .premium-button-style2-scshutoutver:before, {{WRAPPER}} .premium-button-style5-radialin, {{WRAPPER}} .premium-button-style5-radialout:before, {{WRAPPER}} .premium-button-style5-rectin, {{WRAPPER}} .premium-button-style5-rectout:before, {{WRAPPER}} .premium-button-style6-bg, {{WRAPPER}} .premium-button-style6:before' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'premium_button_hover_effect!' => 'style7',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'button_border_hover',
				'selector' => '{{WRAPPER}} .premium-banner-link:hover',
			)
		);

		$this->add_control(
			'button_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-banner-link:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'button_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-banner-link:hover',
			)
		);

		$this->add_responsive_control(
			'button_margin_hover',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-banner-link:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_padding_hover',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-banner-link:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_banner_container_style',
			array(
				'label' => __( 'Container', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_banner_border',
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'premium_banner_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}}' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_banner_shadow',
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'gradient_color',
				'types'     => array( 'gradient' ),
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .premium-banner-gradient:before, {{WRAPPER}} .premium-banner-gradient:after',
				'condition' => array(
					'premium_banner_image_animation' => 'animation11',
				),
			)
		);

		$this->add_control(
			'first_layer_speed',
			array(
				'label'     => __( 'First Layer Transition Speed (sec)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 0.3,
				),
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 3,
						'step' => .1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-banner-animation11:hover .premium-banner-gradient:after' => 'transition-delay: {{SIZE}}s',
					'{{WRAPPER}} .premium-banner-animation11 .premium-banner-gradient:before' => 'transition: transform 0.3s ease-out {{SIZE}}s',
				),
				'condition' => array(
					'premium_banner_image_animation' => 'animation11',
				),
			)
		);

		$this->add_control(
			'second_layer_speed',
			array(
				'label'     => __( 'Second Layer Transition Delay (sec)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 0.15,
				),
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 3,
						'step' => .1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-banner-animation11:hover .premium-banner-gradient:before' => 'transition-delay: {{SIZE}}s',
					'{{WRAPPER}} .premium-banner-animation11 .premium-banner-gradient:after' => 'transition: transform 0.3s ease-out {{SIZE}}s',

				),
				'condition' => array(
					'premium_banner_image_animation' => 'animation11',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render Banner widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$this->add_inline_editing_attributes( 'premium_banner_title' );
		$this->add_render_attribute(
			'premium_banner_title',
			'class',
			array(
				'premium-banner-ib-title',
				'premium_banner_title',
			)
		);

		$this->add_inline_editing_attributes( 'premium_banner_description', 'advanced' );
		$this->add_render_attribute(
			'premium_banner_description',
			'class',
			array(
				'premium-banner-ib-content',
				'premium_banner_content',
			)
		);

		$title_tag  = $settings['premium_banner_title_tag'];
		$title      = $settings['premium_banner_title'];
		$full_title = '<' . Helper_Functions::validate_html_tag( $title_tag ) . ' ' . $this->get_render_attribute_string( 'premium_banner_title' ) . '>' . $title . '</' . Helper_Functions::validate_html_tag( $title_tag ) . '>';

		$link = 'yes' === $settings['premium_banner_image_link_switcher'] ? $settings['premium_banner_image_custom_link'] : get_permalink( $settings['premium_banner_image_existing_page_link'] );

		if ( 'yes' === $settings['premium_banner_image_link_switcher'] ) {
			$this->add_link_attributes( 'link', $link );
		} else {
			$this->add_render_attribute( 'link', 'href', $link );
		}

		$this->add_render_attribute( 'link', 'class', 'premium-banner-ib-link' );

		$animation_class = 'premium-banner-' . $settings['premium_banner_image_animation'];
		$hover_class     = ' ' . $settings['premium_banner_hover_effect'];
		$extra_class     = ! empty( $settings['premium_banner_extra_class'] ) ? ' ' . $settings['premium_banner_extra_class'] : '';
		$active          = 'yes' === $settings['premium_banner_active'] ? ' active' : '';
		$full_class      = $animation_class . $hover_class . $extra_class . $active;
		$min_size        = $settings['premium_banner_min_range'] . 'px';
		$max_size        = $settings['premium_banner_max_range'] . 'px';

		if ( 'yes' === $settings['premium_banner_link_switcher'] ) {

			$banner_url = 'url' === $settings['premium_banner_link_selection'] ? $settings['premium_banner_link'] : get_permalink( $settings['premium_banner_existing_link'] );

			if ( 'url' === $settings['premium_banner_link_selection'] ) {
				$this->add_link_attributes( 'button', $banner_url );
			} else {
				$this->add_render_attribute( 'button', 'href', $banner_url );
			}

			$effect_class = Helper_Functions::get_button_class( $settings );

			$this->add_render_attribute(
				'button',
				array(
					'class'     => array(
						'premium-banner-link',
						$effect_class,
					),
					'data-text' => $settings['premium_banner_more_text'],
				)
			);

		}

		$image_html = '';

		if ( ! empty( $settings['premium_banner_image']['url'] ) ) {

			$image_id = apply_filters( 'wpml_object_id', $settings['premium_banner_image']['id'], 'attachment', true );

			$settings['premium_banner_image']['id'] = $image_id;

			$image_html = Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'premium_banner_image' );

		}

		$this->add_render_attribute(
			'banner_inner',
			array(
				'class' => array(
					'premium-banner-ib',
					'premium-banner-min-height',
					$full_class,
				),
			)
		);

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'banner_inner' ) ); ?>>
			<?php if ( 'animation7' === $settings['premium_banner_image_animation'] || 'animation8' === $settings['premium_banner_image_animation'] ) : ?>
				<div class="premium-banner-border">
					<div class="premium-banner-br premium-banner-bleft premium-banner-brlr"></div>
					<div class="premium-banner-br premium-banner-bright premium-banner-brlr"></div>
					<div class="premium-banner-br premium-banner-btop premium-banner-brtb"></div>
					<div class="premium-banner-br premium-banner-bottom premium-banner-brtb"></div>
				</div>
			<?php endif; ?>
			<?php if ( ! empty( $settings['premium_banner_image']['url'] ) ) : ?>
				<?php echo wp_kses_post( $image_html ); ?>
			<?php endif; ?>
			<?php if ( 'animation11' === $settings['premium_banner_image_animation'] ) : ?>
				<div class="premium-banner-gradient"></div>
			<?php endif; ?>
			<div class="premium-banner-ib-desc">
				<?php if ( 'animation7' === $settings['premium_banner_image_animation'] ) : ?>
					<div class="premium-banner-desc-centered">
				<?php endif; ?>
					<?php
					echo wp_kses_post( $full_title );
					if ( ! empty( $settings['premium_banner_description'] ) ) :
						?>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_banner_description' ) ); ?>>
							<?php echo $this->parse_text_editor( $settings['premium_banner_description'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endif; ?>
					<?php if ( 'yes' === $settings['premium_banner_link_switcher'] && ! empty( $settings['premium_banner_more_text'] ) ) : ?>
						<div class="premium-banner-read-more">
							<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'button' ) ); ?>>
								<div class="premium-button-text-icon-wrapper">
									<span><?php echo esc_html( $settings['premium_banner_more_text'] ); ?></span>
								</div>

								<?php if ( 'style6' === $settings['premium_button_hover_effect'] && 'yes' === $settings['mouse_detect'] ) : ?>
									<span class="premium-button-style6-bg"></span>
								<?php endif; ?>

								<?php if ( 'style8' === $settings['premium_button_hover_effect'] ) : ?>
									<?php echo Helper_Functions::get_btn_svgs( $settings['underline_style'] ); ?>
								<?php endif; ?>

							</a>
						</div>
					<?php endif; ?>
				<?php if ( 'animation7' === $settings['premium_banner_image_animation'] ) : ?>
				</div>
				<?php endif; ?>
			</div>
			<?php
			if ( 'yes' === $settings['premium_banner_link_url_switch'] && ( ! empty( $settings['premium_banner_image_custom_link']['url'] ) || ! empty( $settings['premium_banner_image_existing_page_link'] ) ) ) :
				?>
				<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?>></a>
			<?php endif; ?>
		</div>
		<?php if ( 'yes' === $settings['premium_banner_responsive_switcher'] ) : ?>
			<style>
				@media( min-width: <?php echo wp_kses_post( $min_size ); ?> ) and (max-width:<?php echo wp_kses_post( $max_size ); ?> ) {
					#premium-banner-<?php echo esc_attr( $this->get_id() ); ?> .premium-banner-ib-content {
						display: none;
					}
				}
			</style>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render Banner widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#

			var active = 'yes' === settings.premium_banner_active ? 'active' : '';

			view.addRenderAttribute( 'banner_inner', 'class', [
				'premium-banner-ib',
				'premium-banner-min-height',
				'premium-banner-' + settings.premium_banner_image_animation,
				settings.premium_banner_hover_effect,
				settings.premium_banner_extra_class,
				active
			] );

			var titleTag = elementor.helpers.validateHTMLTag( settings.premium_banner_title_tag ),
				title    = settings.premium_banner_title;

			view.addRenderAttribute( 'premium_banner_title', 'class', [
				'premium-banner-ib-title',
				'premium_banner_title'
			] );

			view.addInlineEditingAttributes( 'premium_banner_title' );

			view.addInlineEditingAttributes( 'premium_banner_description', 'advanced' );
			view.addRenderAttribute( 'premium_banner_description', 'class', [
				'premium-banner-ib-content',
				'premium_banner_content',
			] );

			var linkSwitcher = settings.premium_banner_link_switcher,
				readMore     = settings.premium_banner_more_text,
				bannerUrl    = 'url' === settings.premium_banner_link_selection ? settings.premium_banner_link.url : settings.premium_banner_existing_link;

			var bannerLink = 'yes' === settings.premium_banner_image_link_switcher ? settings.premium_banner_image_custom_link.url : settings.premium_banner_image_existing_page_link;

			var minSize = settings.premium_banner_min_range + 'px',
				maxSize = settings.premium_banner_max_range + 'px';

			var imageHtml = '';
			if ( settings.premium_banner_image.url ) {
				var image = {
					id: settings.premium_banner_image.id,
					url: settings.premium_banner_image.url,
					size: settings.thumbnail_size,
					dimension: settings.thumbnail_custom_dimension,
					model: view.getEditModel()
				};

				var image_url = elementor.imagesManager.getImageUrl( image );

			}

		#>

			<div {{{ view.getRenderAttributeString( 'banner_inner' ) }}}>
				<# if (settings.premium_banner_image_animation ==='animation7' || settings.premium_banner_image_animation ==='animation8'){ #>
					<div class="premium-banner-border">
						<div class="premium-banner-br premium-banner-bleft premium-banner-brlr"></div>
						<div class="premium-banner-br premium-banner-bright premium-banner-brlr"></div>
						<div class="premium-banner-br premium-banner-btop premium-banner-brtb"></div>
						<div class="premium-banner-br premium-banner-bottom premium-banner-brtb"></div>
					</div>
				<# } #>
				<# if( '' !== settings.premium_banner_image.url ) { #>
					<img src="{{ image_url }}"/>
				<# } #>
				<# if( 'animation11' === settings.premium_banner_image_animation ) { #>
					<div class="premium-banner-gradient"></div>
				<# } #>
				<div class="premium-banner-ib-desc">
				<# if( 'animation7' === settings.premium_banner_image_animation ) { #>
					<div class="premium-banner-desc-centered">
				<# } #>
						<# if( '' !== title ) { #>
							<{{{titleTag}}} {{{ view.getRenderAttributeString('premium_banner_title') }}}>{{{ title }}}</{{{titleTag}}}>
						<# } #>
						<# if( '' !== settings.premium_banner_description ) { #>
							<div {{{ view.getRenderAttributeString( 'premium_banner_description' ) }}}>{{{ settings.premium_banner_description }}}</div>
						<# } #>
					<# if( 'yes' === linkSwitcher && '' !== readMore ) {

						var btnClass = '';

						if ( 'none' === settings.premium_button_hover_effect ) {
							btnClass = 'premium-button-none';
						} else if ( 'style1' === settings.premium_button_hover_effect ) {
							btnClass = 'premium-button-style1-' + settings.premium_button_style1_dir;
						} else if ( 'style2' === settings.premium_button_hover_effect ) {
							btnClass = 'premium-button-style2-' + settings.premium_button_style2_dir;
						} else if ( 'style5' === settings.premium_button_hover_effect ) {
							btnClass = 'premium-button-style5-' + settings.premium_button_style5_dir;
						} else if ( 'style6' === settings.premium_button_hover_effect ) {
							btnClass = 'premium-button-style6';
						} else if ( 'style7' === settings.premium_button_hover_effect ) {
							btnClass = 'premium-button-style7-' + settings.premium_button_style7_dir;
						} else if ( 'style8' === settings.premium_button_hover_effect ) {
							btnClass = 'premium-button-' + settings.underline_style;

							var btnSVG = '';
							switch ( settings.underline_style ) {
								case 'line1':
									btnSVG = '<div class="premium-btn-line-wrap"><svg class="premium-btn-svg" width="100%" height="9" viewBox="0 0 101 9"><path d="M.426 1.973C4.144 1.567 17.77-.514 21.443 1.48 24.296 3.026 24.844 4.627 27.5 7c3.075 2.748 6.642-4.141 10.066-4.688 7.517-1.2 13.237 5.425 17.59 2.745C58.5 3 60.464-1.786 66 2c1.996 1.365 3.174 3.737 5.286 4.41 5.423 1.727 25.34-7.981 29.14-1.294" pathLength="1"></path></svg></div>';
									break;

								case 'line3':
									btnSVG = '<div class="premium-btn-line-wrap"><svg class="premium-btn-svg" width="100%" height="18" viewBox="0 0 59 18"><path d="M.945.149C12.3 16.142 43.573 22.572 58.785 10.842" pathLength="1"></path></svg></div>';
									break;

								case 'line4':
									btnSVG = '<svg class="premium-btn-svg" width="300%" height="100%" viewBox="0 0 1200 60" preserveAspectRatio="none"><path d="M0,56.5c0,0,298.666,0,399.333,0C448.336,56.5,513.994,46,597,46c77.327,0,135,10.5,200.999,10.5c95.996,0,402.001,0,402.001,0"></path></svg>';
									break;

								default:
									break;
							}

						}

						btnClass = 'premium-button-' + settings.premium_button_hover_effect + ' ' + btnClass;

						view.addRenderAttribute( 'button', {
							'class': [
								'premium-banner-link',
								btnClass,
							],
							'href': bannerUrl,
							'data-text': readMore,
						});

					#>
						<div class="premium-banner-read-more">
							<a {{{ view.getRenderAttributeString('button') }}}>
								<div class="premium-button-text-icon-wrapper">
									<span>{{{ readMore }}}</span>
								</div>

								<# if ( 'style6' === settings.premium_button_hover_effect && 'yes' === settings.mouse_detect ) { #>
									<span class="premium-button-style6-bg"></span>
								<# } #>

								<# if( 'style8' === settings.premium_button_hover_effect ) { #>
									{{{ btnSVG }}}
								<# } #>

							</a>
						</div>
					<# } #>
					<# if( 'animation7' === settings.premium_banner_image_animation ) { #>
					</div>
					<# } #>
				</div>
				<# if( 'yes' === settings.premium_banner_link_url_switch  && ( '' !== settings.premium_banner_image_custom_link.url || '' !== settings.premium_banner_image_existing_page_link ) ) {

						view.addRenderAttribute( 'banner_link', {
							'class':  'premium-banner-ib-link',
							'href': bannerLink,
						});

				#>
					<a {{{ view.getRenderAttributeString('banner_link') }}}></a>
				<# } #>
			</div>
			<# if( 'yes' === settings.premium_banner_responsive_switcher ) { #>
			<style>
				@media( min-width: {{minSize}} ) and ( max-width: {{maxSize}} ) {
					#premium-banner-{{ view.getID() }} .premium-banner-ib-content {
						display: none;
					}
				}
			</style>
			<# } #>
		<?php
	}
}
