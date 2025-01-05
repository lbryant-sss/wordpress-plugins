<?php
namespace WprAddons\Modules\RandomImage\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Responsive\Responsive;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use WprAddons\Classes\Utilities;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//TODO: create select to switch to custom images uploaded by user


class Wpr_Random_Image extends Widget_Base {
	
	public function get_name() {
		return 'wpr-random-image';
	}

	public function get_title() {
		return esc_html__( 'Random Image', 'wpr-addons' );
	}

	public function get_icon() {
		return 'wpr-icon eicon-image';
	}

	public function get_categories() {
		return [ 'wpr-widgets'];
	}

	public function get_keywords() {
		return [ 'Random Image', 'Image', 'Image', 'Generator', 'Image Generator'];
	}

    public function get_custom_help_url() {
        return 'https://royal-elementor-addons.com/contact/?ref=rea-plugin-panel-random-image-help-btn';
    }

    protected function _register_controls() {
		$this->start_controls_section(
			'section_popup_trigger',
			[
				'label' => esc_html__( 'General', 'wpr-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'category',
			[
				'label' => esc_html__( 'Category', 'wpr-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Type Category Here',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'filter',
			[
				'label' => esc_html__( 'Filter', 'wpr-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Filter Category',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'render_width',
			[
				'label' => esc_html__( 'Render Width', 'wpr-addons' ),
				'type' => Controls_Manager::NUMBER,
                'default' => 1600,
				'min' => 0,
				'step' => 10,
                'selectors' => [
                    '{{WRAPPER}} .wpr-random-image' => 'width: 100%; max-width: 100%; display: block;'
                ],
                'render_type' => 'template',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'render_height',
			[
				'label' => esc_html__( 'Render Height', 'wpr-addons' ),
				'type' => Controls_Manager::NUMBER,
                'default' => 900,
				'min' => 0,
				'step' => 10,
				'separator' => 'before',
			]
		);

        $this->end_controls_section(); // End Controls Section
    }

	protected function render() {
        $settings = $this->get_settings_for_display();

        $topic = $settings['category'];
        $specificity = $settings['filter'];
        $width = '/' . $settings['render_width'];
        $height = 'x' . $settings['render_height'] . '/';

        echo '<img class="wpr-random-image" src="https://source.unsplash.com'. $width . $height .'?'. $topic .','. $specificity .'">';
    }
}