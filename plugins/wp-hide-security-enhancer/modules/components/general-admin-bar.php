<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_admin_bar extends WPH_module_component
        {
            
            var $_initialized   =   FALSE;
            
            function get_component_title()
                {
                    return "Admin Bar";
                }
            
                                    
            function get_module_settings()
                {
                    
                    global $wpdb;
                    
                    $wp_roles   =   get_option( $wpdb->base_prefix . 'user_roles');
                    
                    if ( ! is_array ( $wp_roles ) )
                        return $this->module_settings;
                    
                    $first  =   TRUE;
                    foreach  ( $wp_roles     as  $role_slug  =>  $role )
                        {
                    
                            $this->module_settings[]                  =   $this->_prepare_modle_setting( $role_slug, $role['name'], $first );
                            $first  =   FALSE;
                        }
                                                                    
                    return $this->module_settings;   
                }
                
            
            function _prepare_modle_setting( $role_slug, $role_name, $first )
                {
                    $module_setting     =   array(
                                                                    'id'                    =>  'remove_admin_bar_' . $role_slug,
                                                                    
                                                                    'input_type'            =>  'radio',
                                                    
                                                                    'default_value'         =>  'no',
                                                                    
                                                                    'sanitize_type'         =>  array('sanitize_title', 'strtolower'),
                                                                    
                                                                    'callback'              =>  '_init_remove_admin_bar',
                                                                    'callback_arguments'    =>  array('role_slug'  =>  $role_slug ),
                                                                    
                                                                    );
                    
                    return $module_setting; 
         
                }
                
                
            /**
            * Add the descriptin options to the component
            * 
            * @param mixed $component_settings
            */
            function set_module_components_description( $component_settings )
                {
                    global $wpdb;
                    
                    $wp_roles   =   get_option( $wpdb->prefix . 'user_roles');
                    
                    $first  =   TRUE;
                    
                    foreach ( $component_settings   as  $component_key  =>  $component_setting )
                        {
                    
                            foreach  ( $wp_roles     as  $role_slug  =>  $role )
                                {
                                    if ( strcmp( 'remove_admin_bar_' . $role_slug , $component_setting['id'] ) !== 0 )
                                        continue;
                                    
                                    $help_description   =   '';
                    
                                    if  ( $first )
                                        $help_description   =   __( 'The admin bar is a floating bar that contains useful administration screen links such as add a new post, see pending comments, edit your profile etc. It can be extended by plugins to add additional functionality for example SEO and more.',    'wp-hide-security-enhancer') . ' <br /><br />';
                                        
                                    $help_description   .=  __('Remove WordPress Admin Bar for ',    'wp-hide-security-enhancer') . $role['name'] . ' ' .  __('role' ,    'wp-hide-security-enhancer');
                                    
                                    
                                    $component_setting =   array_merge ( $component_setting , array(
                                                                                                        'label'                 =>  __('Remove Admin Bar for ',    'wp-hide-security-enhancer') . $role['name'],
                                                                                                        'description'           =>  __('Remove WordPress Admin Bar for ',    'wp-hide-security-enhancer') . $role['name'] . ' ' .  __('role, which is being displayed by default on front side of your website.',    'wp-hide-security-enhancer'),
                                                                                                        
                                                                                                        'help'          =>  array(
                                                                                                                                    'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Remove Admin Bar for ',    'wp-hide-security-enhancer') . $role['name'] . ' ' .  __('role.',    'wp-hide-security-enhancer'),
                                                                                                                                    'description'               =>  $help_description,
                                                                                                                                    'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/general-html-admin-bar/'
                                                                                                                                    ),
                                                                                                        
                                                                                                        'options'               =>  array(
                                                                                                                                            'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                                                            'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                                                            ),                        
                                                                                                                                ) );
                                                                                                                                
                                    $component_settings[ $component_key ]   =   $component_setting;
                                    
                                    $first  =   FALSE;
                                    
                                    break;
                                }
                        }    
                    
                    return $component_settings;
                    
                }
                
                
                
            function _init_remove_admin_bar( $saved_field_data )
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    //trigger once
                    if  ( $this->_initialized )
                        return;   
                         
                    $this->_initialized =   TRUE;
                        
                    add_action('init',          array($this, 'remove_admin_bar'));
                }
                
                
            function remove_admin_bar()
                {
                    if ( is_user_logged_in()    === FALSE )
                        return;
                    
                    $current_user   = wp_get_current_user();
                    $user_role      =   isset(  $current_user->roles[0] ) ?     $current_user->roles[0] :   '';
                    if ( empty ( $user_role ))
                        return;
                         
                    $role_hide_admin_bar    =   $this->wph->functions->get_module_item_setting('remove_admin_bar_' . $user_role );
                    if( $role_hide_admin_bar    !=  'yes')
                        return;
                        
                    add_filter('show_admin_bar', '__return_false');                    
                    
                }
            
           


        }
        
?>