<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class HTMega_Elementor_Widget_Instragram_Feed extends Widget_Base {

    public function get_name() {
        return 'htmega-instragramfeed-addons';
    }
    
    public function get_title() {
        return __( 'Instagram Feed', 'htmega-addons' );
    }

    public function get_icon() {
        return 'htmega-icon eicon-photo-library';
    }

    public function get_categories() {
        return [ 'htmega-addons' ];
    }

    public function get_keywords() {
        return ['instagram', 'instagram feed', 'instagram grid', 'htmega', 'social media', 'ht mega', 'addons', 'widget'];
    }

    public function get_help_url() {
        return 'https://wphtmega.com/docs/social-widgets/instagram-feed-widget/';
    }
    protected function register_controls() {
        if ( ! is_plugin_active('instagram-feed/instagram-feed.php') ) {
            $this->messing_parent_plg_notice();
        } else {
            $this->instagram_feed_regster_fields();
        }
    }
    protected function messing_parent_plg_notice() {

        $this->start_controls_section(
            'messing_parent_plg_notice_section',
            [
                'label' => __( 'Instragram Feed', 'htmega-addons' ),
            ]
        );
        $this->add_control(
            'htmega_plugin_parent_missing_notice',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => esc_html__( 'It appears that Instragram Feed is not currently installed on your site. Please install or activate Instragram Feed, and remember to refresh the page after installation or activation.', 'htmega-addons' ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-danger',
            ]
        );
        
        $this->end_controls_section();

    }

    protected function instagram_feed_regster_fields() {

        $this->start_controls_section(
            'instragram_feed_content',
            [
                'label' => __( 'Instagram Feed', 'htmega-addons' ),
            ]
        );
        $this->add_control(
            'htmega_feed_id',
            [
                'label' => __( 'Select Feed', 'htmega-addons' ),
                'type' => Controls_Manager::SELECT,
                'options' => htmega_instagram_feed_list(),
            ]
        );
            $this->add_control(
                'feed_limit',
                [
                    'label' => esc_html__( 'Feed Limit', 'htmega-addons' ),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 8,
                ]
            );

            $this->add_control(
                'feed_cols',
                [
                    'label' => esc_html__( 'Number of Column', 'htmega-addons' ),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 4,
                ]
            );

            $this->add_control(
                'feed_imageres_size',
                [
                    'label'   => esc_html__( 'Image Size', 'htmega-addons' ),
                    'type'    => Controls_Manager::SELECT,
                    'default' => 'full',
                    'options' => [
                        'auto'   => esc_html__( 'Auto', 'htmega-addons' ),
                        'full'   => esc_html__( 'Full', 'htmega-addons' ),
                        'medium' => esc_html__( 'Medium', 'htmega-addons' ),
                        'thumb'  => esc_html__( 'Thumb', 'htmega-addons' ),
                    ],
                ]
            );

            $this->add_control(
                'show_feed_header',
                [
                    'label'     => esc_html__( 'Show Header', 'htmega-addons' ),
                    'type'      => Controls_Manager::SWITCHER,
                    'default'   => 'no',
                    'label_off' => esc_html__( 'no', 'htmega-addons' ),
                    'label_on'  => esc_html__( 'yes', 'htmega-addons' ),
                ]
            );

            $this->add_control(
                'show_feed_follow',
                [
                    'label'     => esc_html__( 'Show Follow Text', 'htmega-addons' ),
                    'type'      => Controls_Manager::SWITCHER,
                    'default'   => 'no',
                    'label_off' => esc_html__( 'no', 'htmega-addons' ),
                    'label_on'  => esc_html__( 'yes', 'htmega-addons' ),
                ]
            );

            $this->add_control(
                'follow_text',
                [
                    'label'       => esc_html__( 'Follow Text', 'htmega-addons' ),
                    'type'        => Controls_Manager::TEXT,
                    'placeholder' => esc_html__( 'Follow on Instagram', 'htmega-addons' ),
                    'default'     => esc_html__( 'Follow on Instagram', 'htmega-addons' ),
                    'label_block' => true,
                    'condition' => [
                        'show_feed_follow' =>'yes',
                    ],
                ]
            );
            
        $this->end_controls_section();

        // Style tab section
        $this->start_controls_section(
            'instragram_feed_style_section',
            [
                'label' => __( 'Style', 'htmega-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
            
            $this->add_control(
                'imagepadding',
                [
                    'label' => esc_html__( 'Image Padding', 'htmega-addons' ),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 8,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 300,
                        ],
                    ],
                ]
            );

            $this->add_control(
                'headercolor',
                [
                    'label' => esc_html__( 'Header Color', 'htmega-addons' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'condition' => [
                        'show_feed_header' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'followcolor',
                [
                    'label' => esc_html__( 'Follow Color', 'htmega-addons' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'condition' => [
                        'show_feed_follow' =>'yes',
                    ],
                ]
            );

            $this->add_control(
                'followtextcolor',
                [
                    'label' => esc_html__( 'Follow Text Color', 'htmega-addons' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'condition' => [
                        'show_feed_follow' =>'yes',
                    ],
                ]
            );
        $this->end_controls_section();

    }

    protected function render( $instance = [] ) {
        if ( ! is_plugin_active('instagram-feed/instagram-feed.php') ) {
            htmega_plugin_missing_alert( __('Instragram Feed', 'htmega-addons') );
            return;
        }

        $sbi_statuses = get_option( 'sbi_statuses', array() );
        $sbi_statuses['support_legacy_shortcode'] = true;
        update_option( 'sbi_statuses', $sbi_statuses );

        $settings   = $this->get_settings_for_display();

        $instagram_attributes = [
            'num'              => absint( $settings['feed_limit'] ),
            'cols'             => absint( $settings['feed_cols'] ),
            'user'             => esc_attr( $settings['htmega_feed_id'] ),
            'imageres'         => esc_attr( $settings['feed_imageres_size'] ),
            'imagepadding'     => absint( $settings['imagepadding']['size'] ),
            'imagepaddingunit' => 'px',
            'showheader'       => ($settings['show_feed_header'] =='yes') ? 'true' : 'false',
            'showbutton'       => 'false',
            'showfollow'       => ($settings['show_feed_follow'] =='yes') ? 'true' : 'false',
            'headercolor'      => esc_attr( $settings['headercolor'] ),
            'followcolor'      => esc_attr( $settings['followcolor'] ),
            'followtextcolor'  => esc_attr( $settings['followtextcolor'] ),
            'followtext'       => esc_html( $settings['follow_text'] ),
        ];

        $this->add_render_attribute( 'shortcode', $instagram_attributes );

        echo do_shortcode( sprintf( '[instagram-feed %s]', $this->get_render_attribute_string( 'shortcode' ) ) );

    }

}

