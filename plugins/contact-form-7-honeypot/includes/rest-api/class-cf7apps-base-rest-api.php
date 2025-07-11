<?php

class CF7Apps_Base_Rest_API {
    
    /**
     * Authenticate User
     * 
     * @since 2.2.0
     */
    public function authenticate() {
        return current_user_can( 'manage_options' );
    }

    /**
     * Return Apps
     * 
     * @since 2.2.0
     */
    protected function get_registered_apps() {
        return apply_filters( 'cf7apps_apps', array() );
    }
}