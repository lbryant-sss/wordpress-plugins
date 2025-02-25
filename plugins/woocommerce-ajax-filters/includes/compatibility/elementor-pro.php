<?php
if( ! class_exists('BeRocket_AAPF_compat_Elementor_pro') ) {
    class BeRocket_AAPF_compat_Elementor_pro {
        public $attributes;
        function __construct() {
            add_action("elementor/element/woocommerce-products/section_content/before_section_end", array($this, 'add_control'), 10, 2);
            add_action("elementor/element/loop-grid/section_query/before_section_end", array($this, 'add_control_loop_grid'), 10, 2);
            add_action("elementor/element/loop-carousel/section_query/before_section_end", array($this, 'add_control_loop_carousel'), 10, 2);
            add_action('elementor/widget/before_render_content', array($this, 'before_render_content'), 10, 1);
            add_filter('aapf_localize_widget_script', array($this, 'modify_products_selector'));
        }
        function modify_products_selector($args) {
            if( ! empty($args['products_holder_id']) ) {
                $args['products_holder_id'] .= ',';
            }
            $args['products_holder_id'] .= '.bapf_products_apply_filters  .elementor-loop-container';
            if( ! empty($args['pagination_class']) ) {
                $args['pagination_class'] .= ',';
            }
            $args['pagination_class'] .= '.bapf_products_apply_filters  .elementor-pagination, .bapf_products_apply_filters .e-load-more-anchor';
            return $args;
        }
        function add_control($element, $args) {
            $element->add_control(
                'bapf_apply',
                [
                    'label' => __( 'Apply BeRocket AJAX Filters', 'BeRocket_AJAX_domain' ),
                    'type' => Elementor\Controls_Manager::SELECT,
                    'description' => __( 'All Filters will be applied to this module. You need correct unique selectors to work correct', 'BeRocket_AJAX_domain' ),
                    'default' => 'default',
                    'options' => [
                        'default' => __( 'Default', 'BeRocket_AJAX_domain' ),
                        'enable'  => __( 'Enable', 'BeRocket_AJAX_domain' ),
                        'disable' => __( 'Disable', 'BeRocket_AJAX_domain' ),
                    ],
                ]
            );
        }
        function add_control_loop_grid($element, $args) {
            $element->add_control(
                'bapf_apply',
                [
                    'label' => __( 'Apply BeRocket AJAX Filters', 'BeRocket_AJAX_domain' ),
                    'type' => Elementor\Controls_Manager::SELECT,
                    'description' => __( 'All Filters will be applied to this module. You need correct unique selectors to work correct', 'BeRocket_AJAX_domain' ),
                    'default' => 'default',
                    'options' => [
                        'default' => __( 'Default', 'BeRocket_AJAX_domain' ),
                        'enable'  => __( 'Enable', 'BeRocket_AJAX_domain' ),
                        'disable' => __( 'Disable', 'BeRocket_AJAX_domain' ),
                    ],
                    'condition' => [
                        '_skin' => 'product',
                    ],
                ]
            );
        }
        function add_control_loop_carousel($element, $args) {
            $element->add_control(
                'bapf_apply',
                [
                    'label' => __( 'Apply BeRocket AJAX Filters', 'BeRocket_AJAX_domain' ),
                    'type' => Elementor\Controls_Manager::SELECT,
                    'description' => __( 'All Filters will be applied to this module. You need correct unique selectors to work correct', 'BeRocket_AJAX_domain' ),
                    'default' => 'disable',
                    'options' => [
                        'default' => __( 'Default', 'BeRocket_AJAX_domain' ),
                        'enable'  => __( 'Enable', 'BeRocket_AJAX_domain' ),
                        'disable' => __( 'Disable', 'BeRocket_AJAX_domain' ),
                    ],
                    'condition' => [
                        '_skin' => 'product',
                    ],
                ]
            );
        }
        function before_render_content($element) {
            remove_filter('berocket_aapf_wcshortcode_is_filtering', array($this, 'enable_filtering'), 1000);
            $element_name = $element->get_name();
            if( $element_name == 'woocommerce-products' || $element_name == 'loop-grid' || $element_name == 'loop-carousel' ) {
                $this->attributes = $element->get_settings();
                add_filter('berocket_aapf_wcshortcode_is_filtering', array($this, 'enable_filtering'), 1000);
                $enabled = (! is_shop() && ! is_product_taxonomy() && ! is_product_category() && ! is_product_tag());
                if( ! empty($this->attributes['bapf_apply']) && $this->attributes['bapf_apply'] == 'enable' ) {
                    $enabled = true;
                } elseif( ! empty($this->attributes['bapf_apply']) && $this->attributes['bapf_apply'] == 'disable' ) {
                    $enabled = false;
                } elseif( ! empty($this->attributes['query_post_type']) && $this->attributes['query_post_type'] == 'current_query' ) {
                    $enabled = true;
                }
                if( $enabled ) {
                    $element->add_render_attribute(
                        '_wrapper',
                        [
                            'class' => 'bapf_products_apply_filters',
                        ]
                    );
                }
            }
        }
        function enable_filtering($enabled) {
            if( ! empty($this->attributes['bapf_apply']) && $this->attributes['bapf_apply'] == 'enable' ) {
                $enabled = true;
            } elseif( ! empty($this->attributes['bapf_apply']) && $this->attributes['bapf_apply'] == 'disable' ) {
                $enabled = false;
            } elseif( ! empty($this->attributes['query_post_type']) && $this->attributes['query_post_type'] == 'current_query' ) {
                $enabled = true;
            }
            return $enabled;
        }
    }
    new BeRocket_AAPF_compat_Elementor_pro();
}