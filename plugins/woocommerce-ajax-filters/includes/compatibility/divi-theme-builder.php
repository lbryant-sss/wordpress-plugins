<?php
if( ! class_exists('BeRocket_AAPF_compat_Divi_theme_builder') ) {
    class BeRocket_AAPF_compat_Divi_theme_builder {
        public $attributes;
        function __construct() {
            add_filter('et_pb_all_fields_unprocessed_et_pb_shop', array($this, 'add_field'));
            add_filter('et_pb_module_shortcode_attributes', array($this, 'attribute_get'), 10, 5);
            add_filter('bapf_isoption_ajax_site', array($this, 'enable_for_builder'));
            add_filter('aapf_localize_widget_script', array($this, 'modify_products_selector'));
            if(defined('DOING_AJAX') && in_array(berocket_isset($_REQUEST['action']), array('et_fb_ajax_render_shortcode', 'brapf_get_single_filter', 'brapf_get_group_filter'))) {
                add_filter('braapf_check_widget_by_instance_single', array($this, 'disable_conditions'));
                add_filter('braapf_check_widget_by_instance_group', array($this, 'disable_conditions'));
            }
            if( br_get_value_from_array($_GET,'et_fb') == 1 || ( ! empty($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'et_body_layout' ) ) {
                add_action('wp_footer', array($this, 'apply_styles'));
            }
        }
        function modify_products_selector($args) {
            if( ! empty($args['products_holder_id']) ) {
                $args['products_holder_id'] .= ',';
            }
            $args['products_holder_id'] .= '.bapf_products_divi_apply_filters  .products';
            if( ! empty($args['pagination_class']) ) {
                $args['pagination_class'] .= ',';
            }
            $args['pagination_class'] .= '.bapf_products_divi_apply_filters  .woocommerce-pagination';
            return $args;
        }
        function attribute_get($props, $attrs, $render_slug, $_address, $content) {
            remove_filter('berocket_aapf_wcshortcode_is_filtering', array($this, 'enable_filtering'), 1000);
            remove_filter('et_builder_shop_classes', array($this, 'add_class'), 1000);
            if( $render_slug == 'et_pb_shop' ) {
                $this->attributes = $props;
                add_filter('berocket_aapf_wcshortcode_is_filtering', array($this, 'enable_filtering'), 1000);
                add_filter('et_builder_shop_classes', array($this, 'add_class'), 1000);
            }
            return $props;
        }
        function add_class($classes) {
            $enabled = braapf_is_shortcode_must_be_filtered();
            if( ! empty($this->attributes['bapf_apply']) && $this->attributes['bapf_apply'] == 'enable' ) {
                $enabled = true;
            } elseif( ! empty($this->attributes['bapf_apply']) && $this->attributes['bapf_apply'] == 'disable' ) {
                $enabled = false;
            } elseif( ! empty($this->attributes['use_current_loop']) && $this->attributes['use_current_loop'] == 'on' && ( is_post_type_archive( 'product' ) || is_search() || et_is_product_taxonomy() ) ) {
                $enabled = true;
            }
            if( $enabled ) {
                $classes[] = 'bapf_products_divi_apply_filters';
            }
            return $classes;
        }
        function add_field($fields) {
            $fields = berocket_insert_to_array(
                $fields,
                'type',
                array(
                    'bapf_apply' => array(
                        'label'            => esc_html__( 'Apply BeRocket AJAX Filters', 'BeRocket_AJAX_domain' ),
                        'type'             => 'select',
                        'option_category'  => 'basic_option',
                        'options'          => array(
                            'default'          => esc_html__( 'Default', 'BeRocket_AJAX_domain' ),
                            'enable'           => esc_html__( 'Enable', 'BeRocket_AJAX_domain' ),
                            'disable'          => esc_html__( 'Disable', 'BeRocket_AJAX_domain' ),
                        ),
                        'default_on_front' => 'default',
                        'description'      => esc_html__( 'All Filters will be applied to this module. You need correct unique selectors to work correct', 'BeRocket_AJAX_domain' ),
                        'toggle_slug'      => 'main_content',
                        'computed_affects' => array(
                            '__shop',
                        ),
                    )
                )
            );
            return $fields;
        }
        function enable_filtering($enabled) {
            if( ! empty($this->attributes['bapf_apply']) && $this->attributes['bapf_apply'] == 'enable' ) {
                $enabled = true;
            } elseif( ! empty($this->attributes['bapf_apply']) && $this->attributes['bapf_apply'] == 'disable' ) {
                $enabled = false;
            } elseif( ! empty($this->attributes['use_current_loop']) && $this->attributes['use_current_loop'] == 'on' && ( is_post_type_archive( 'product' ) || is_search() || et_is_product_taxonomy() ) ) {
                $enabled = true;
            }
            return $enabled;
        }
        function disable_conditions($return) {
            return false;
        }
        function enable_for_builder($enabled) {
            if( br_get_value_from_array($_GET,'et_fb') == 1 || (defined('DOING_AJAX') 
                && in_array(berocket_isset($_REQUEST['action']), array('et_fb_ajax_render_shortcode', 'brapf_get_single_filter', 'brapf_get_group_filter'))) ) {
                $enabled = true;
                
            }
            return $enabled;
        }
        function apply_styles() {
            ?>
            <script>
            function braapf_init_for_iframes_divi() {
                jQuery('iframe').each(function() {
                    if( jQuery(this).contents().length ) {
                        berocket_do_action('braapf_init_for_parent', jQuery(this).contents());
                    }
                });
            }
            jQuery(document).ready(function() {
                var bapf_et_check = setInterval(function() {
                    if( typeof(ET_Builder) != 'undefined' 
                    && typeof(ET_Builder.Frames) != 'undefined'
                    && typeof(ET_Builder.Frames.app) != 'undefined'
                    && typeof(ET_Builder.Frames.app.window) != 'undefined' ) {
                        clearInterval(bapf_et_check);
                        berocket_add_filter('braapf_init', braapf_init_for_iframes_divi);
                        jQuery(ET_Builder.Frames.app.window).on('et_fb_module_did_mount_et_pb_br_filter_single et_fb_module_did_update_et_pb_br_filter_single', function(){braapf_init_load();});
                        jQuery(ET_Builder.Frames.app.window).on('bapf_update_et_pb_br_filter_single', function() {setTimeout(function() {braapf_init_load();}, 10);});
                        berocket_remove_filter('update_products', braapf_update_products);
                        braapf_init_load();
                    }
                }, 500);
            });
            jQuery(document).ajaxComplete( function() {
                setTimeout(braapf_init_load, 100);
            });
            </script>
            <?php
        }
    }
    new BeRocket_AAPF_compat_Divi_theme_builder();
}
