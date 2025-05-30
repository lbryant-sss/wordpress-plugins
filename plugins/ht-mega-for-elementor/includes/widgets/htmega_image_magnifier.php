<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class HTMega_Elementor_Widget_Image_Magnifier extends Widget_Base {

    public function get_name() {
        return 'htmega-imagemagnifier-addons';
    }
    
    public function get_title() {
        return __( 'Image Magnifier', 'htmega-addons' );
    }

    public function get_icon() {
        return 'htmega-icon eicon-clone';
    }

    public function get_categories() {
        return [ 'htmega-addons' ];
    }

    public function get_style_depends() {
        return [
            'magnifier'
        ];
    }

    public function get_script_depends() {
        return [
            'magnifier',
        ];
    }

    public function get_keywords() {
        return ['image magnifier', 'image zoom', 'image view', 'photo viewer', 'htmega magnifier', 'ht mega magnifier', 'addons','widget'];
    }

    public function get_help_url() {
        return 'https://wphtmega.com/docs/general-widgets/image-magnifier-widget/';
    }
    protected function is_dynamic_content():bool {
		return false;
	}
    protected function register_controls() {

        $this->start_controls_section(
            'magifier_content',
            [
                'label' => __( 'Magnifier', 'htmega-addons' ),
            ]
        );

            $this->add_control(
                'magnifier_image',
                [
                    'label' => __( 'Thumbnail Image', 'htmega-addons' ),
                    'type' => Controls_Manager::MEDIA,
                    'default' => [
                        'url' => Utils::get_placeholder_image_src(),
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Image_Size::get_type(),
                [
                    'name' => 'magnifier_image_size',
                    'default' => 'large',
                    'separator' => 'none',
                ]
            );

            $this->add_control(
                'lens_speed',
                [
                    'label' => __( 'Speed', 'htmega-addons' ),
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 50,
                            'max' => 500,
                            'step' => 10,
                        ],
                    ],
                    'default' => [
                        'size' => 200,
                    ],
                ]
            );
            $this->add_responsive_control(
                'lens_width',
                [
                    'label' => __( 'Lens Width', 'htmega-addons' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%' ],
                    'range' => [
                        'px' => [
                            'min' => 50,
                            'max' => 500,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 5,
                            'max' => 100,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 200,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .magnify-lens' => 'width: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'lens_height',
                [
                    'label' => __( 'Lens Height', 'htmega-addons' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%' ],
                    'range' => [
                        'px' => [
                            'min' => 50,
                            'max' => 500,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 5,
                            'max' => 100,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 200,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .magnify-lens' => 'height: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );
            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'lens_box_shadow',
                    'label' => __( 'Box Shadow', 'htmega-addons' ),
                    'selector' => '{{WRAPPER}} .magnify-lens',
                    'separator' => 'before',
                ]
            );

            $this->add_responsive_control(
                'lens_border_radius',
                [
                    'label' => __( 'Box Radius', 'htmega-addons' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'default' => [
                        'top' => 0,
                        'right' => 0,
                        'bottom' => 0,
                        'left' => 0,
                        'unit' => 'px',
                        'isLinked' => true,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .magnify-lens' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

        $this->end_controls_section();

        // Style tab section
        $this->start_controls_section(
            'imagemagnifier_style_section',
            [
                'label' => __( 'Style', 'htmega-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
            
            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'imagemagnifier_area_border',
                    'label' => __( 'Border', 'htmega-addons' ),
                    'selector' => '{{WRAPPER}} .zoom_thumbnail_area',
                ]
            );

            $this->add_responsive_control(
                'imagemagnifier_area_border_radius',
                [
                    'label' => __( 'Border Radius', 'htmega-addons' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'selectors' => [
                        '{{WRAPPER}} .zoom_thumbnail_area' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                        '{{WRAPPER}} .magnifier-thumb-wrapper img' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                    ],
                    'separator' => 'before',
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'imagemagnifier_area_box_shadow',
                    'label' => __( 'Box Shadow', 'htmega-addons' ),
                    'selector' => '{{WRAPPER}} .zoom_thumbnail_area',
                    'separator' => 'before',
                ]
            );

            $this->add_responsive_control(
                'imagemagnifier_area_padding',
                [
                    'label' => __( 'Padding', 'htmega-addons' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'selectors' => [
                        '{{WRAPPER}} .zoom_thumbnail_area' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                    ],
                    'separator' => 'before',
                ]
            );

            $this->add_responsive_control(
                'imagemagnifier_area_margin',
                [
                    'label' => __( 'Margin', 'htmega-addons' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'selectors' => [
                        '{{WRAPPER}} .zoom_thumbnail_area' => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                    ],
                    'separator' => 'before',
                ]
            );

        $this->end_controls_section();

    }

    protected function render( $instance = [] ) {

        $settings   = $this->get_settings_for_display();
        $id = $this->get_id();
        $image_url = wp_get_attachment_image_src( $settings['magnifier_image']['id'], $settings['magnifier_image_size_size'] );

        $magnifierimg_attr = [
            'id'                    => 'thumb-'. esc_attr( $id ),
            'src'                   => isset( $image_url[0] ) ? esc_url( $image_url[0] ) : $settings['magnifier_image']['url'],
            'alt'                   => isset( $settings['magnifier_image']['alt'] ) ? esc_attr( $settings['magnifier_image']['alt'] ) : '',
            'data-magnify-src'    => esc_url( $settings['magnifier_image']['url'] ),
            'data-speed'             => absint( $settings['lens_speed']['size'] ),
            'class'                 => 'zoom',
        ];
        $this->add_render_attribute( 'zoomimgattr', $magnifierimg_attr );
       
        ?>
            <div class="zoom_image_area">
                <div class="zoom_thumbnail_area">
                    <a class="magnifier-thumb-wrapper"><img <?php echo $this->get_render_attribute_string( 'zoomimgattr' ); ?>></a>
                </div>
            </div>
            <script>
                jQuery(document).ready(function($) {
                    'use strict';
                    $('#thumb-<?php echo esc_attr( $id ); ?>').magnify();
                });
            </script>
        <?php

    }

}
