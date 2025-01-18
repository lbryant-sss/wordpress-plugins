<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_oembed extends WPH_module_component
        {
            function get_component_title()
                {
                    return "Oembed";
                }
                                    
            function get_module_settings()
                {
                    $this->module_settings[]                  =   array(
                                                                    'id'            =>  'remove_oembed',
                                                                                                             
                                                                    'input_type'    =>  'radio',
                                              
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  75
                                                                    );
                                                                    
                    return $this->module_settings;   
                }
                
        function set_module_components_description( $component_settings )
                {

                    foreach ( $component_settings   as  $component_key  =>  $component_setting )
                        {
                            if ( ! isset ( $component_setting['id'] ) )
                                continue;
                            
                            switch ( $component_setting['id'] )
                                {
                                    case 'remove_oembed' :
                                                                $component_setting =   array_merge ( $component_setting , array(
                                                                                                                                    'label'         =>  __('Remove Oembed',    'wp-hide-security-enhancer'),
                                                                                                                                    'description'   =>  __('Remove Oembed tags from header.', 'wp-hide-security-enhancer'), 
                                                                                                                                    
                                                                                                                                    'help'          =>  array(
                                                                                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Remove Oembed',    'wp-hide-security-enhancer'),
                                                                                                                                                                'description'               =>  __("WordPress oEmbed recognizes URLs to a number of services, for example Youtube videos. When WordPress sees the URL it will connect to the external service (Youtube) and ask for the relevant HTML code to embed the video into the page or post.",    'wp-hide-security-enhancer'),
                                                                                                                                                                ),

                                                                                                                                    'options'       =>  array(
                                                                                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                                                                                ),
                                                                                                                                ) );
                                                                break;
                                                     
                                }
                                
                            $component_settings[ $component_key ]   =   $component_setting;
                        }
                    
                    return $component_settings;
                    
                }
            
                
            function _init_remove_oembed($saved_field_data)
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                        
                    add_filter('wp'                     , array ( $this, '_run'  ) );
                    
                }

                
            function _run()
                {
                    if ( apply_filters ('wph/components/wp_oembed_add_discovery_links', TRUE ) !== FALSE )
                        remove_action( 'wp_head',                'wp_oembed_add_discovery_links'         );
                    
                    if ( apply_filters ('wph/components/wp_oembed_add_host_js', TRUE ) !== FALSE )    
                        remove_action( 'wp_head',                'wp_oembed_add_host_js'                 );
                }

        }
?>