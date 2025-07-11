<?php

if( ! class_exists( 'CF7Apps' ) ):
class CF7Apps {
    private static $instance;

    /**
     * Initialize the class
     * 
     * @since 2.2.0
     */
    public static function instance() {
        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof CF7Apps ) ) {
            self::$instance = new CF7Apps;
        }

        return self::$instance;
    }

    /**
     * Constructor
     * 
     * @since 2.2.0
     */
    public function __construct() {
        $this->init();
    }

    /**
     * Initialize the plugin
     * 
     * @since 2.2.0
     */
    public function init() {
        $this->includes();
        $this->register_hooks();
        $this->add_actions();
    }

    /**
     * Include necessary files
     * 
     * @since 2.2.0
     */
    public function includes() {
        require_once 'cf7apps-functions.php';
        require_once 'abstract/abstract-cf7apps-app.php';
        require_once 'apps/cf7apps-init-apps.php';
        require_once 'rest-api/class-cf7apps-base-rest-api.php';
        require_once 'rest-api/wp-admin/v1/rest-api.php';
    }

    /**
     * Register necessary hooks
     * 
     * @since 2.2.0
     */
    public function register_hooks() {
        register_activation_hook( CF7APPS_PLUGIN, array( $this, 'activate' ) );
    }

    /**
     * Activation hook
     * 
     * @since 2.2.0
     */
    public function activate() {
        $cf7apps_settings = get_option( 'cf7apps_settings' );
        $legacy_settings = get_option( 'honeypot4cf7_config' );

        if( $legacy_settings ) {
            // Update to new system.
            cf7apps_migrate_legacy_settings();
        }
        elseif( ! $cf7apps_settings ) {
            // Save new settings.
            $default_settings = cf7apps_get_default_settings();

            cf7apps_save_app_settings( 'honeypot', $default_settings );
        }
    }

    /**
     * Add necessary actions
     * 
     * @since 2.2.0
     */
    public function add_actions() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }

    /**
     * Add admin menu | Action Callback
     * 
     * @since 2.2.0
     */
    public function admin_menu() {
        $page_id = add_menu_page( 
            'CF7Apps', 
            'CF7Apps', 
            'manage_options', 
            'cf7apps', 
            array( $this, 'cf7_apps' ), 
            CF7APPS_PLUGIN_DIR_URL . '/assets/images/menu-icon.png' 
        );

        add_submenu_page(
            'cf7apps',
            __( 'All Apps', 'cf7apps' ),
            __( 'All Apps', 'cf7apps' ),
            'manage_options',
            'cf7apps',
            null
        );

        add_submenu_page(
            'cf7apps',
            __( 'Contact Forms', 'cf7apps' ),
            __( 'Contact Forms', 'cf7apps' ),
            'manage_options',
            'wpcf7',
            null
        );
        
        add_submenu_page(
            'cf7apps',
            __( 'Add Contact Form', 'cf7apps' ),
            __( 'Add Contact Form', 'cf7apps' ),
            'manage_options',
            'wpcf7-new',
            function() {
                wp_safe_redirect( admin_url( 'admin.php?page=wpcf7-new' ) );
                exit;
            }
        );

        add_action( "admin_print_styles-{$page_id}", array( $this, 'admin_styles' ) );
    }

    /**
     * CF7 Apps Page | Action Callback
     * 
     * @since 2.2.0
     */
    public function admin_styles() {
        $asset_file = CF7APPS_PLUGIN_DIR . '/build/index.asset.php';
        
        if ( ! file_exists( $asset_file ) ) {
            return;
        }

        $asset = include $asset_file;

        wp_enqueue_script(
            'cf7apps-script',
            CF7APPS_PLUGIN_DIR_URL . '/build/index.js',
            $asset['dependencies'],
            $asset['version'],
            array(
                'in_footer' => true
            )
        );

        wp_localize_script(
            'cf7apps-script',
            'CF7Apps',
            array(
                'hasPro'        => true,
                'restURL'       => rest_url(),
                'nonce'         => wp_create_nonce( 'wp_rest' ),
                'assetsURL'     => CF7APPS_PLUGIN_DIR_URL . '/assets',
                'appIndexURL'   =>  admin_url( 'admin.php?page=cf7apps' )
            )
        );

        $css_handle = is_rtl() ? 'cf7apps-style-rtl' : 'cf7apps-style';
        $css_file = is_rtl() ? '/build/index-rtl.css' : '/build/index.css';
        wp_enqueue_style(
            $css_handle,
            CF7APPS_PLUGIN_DIR_URL . $css_file,
            array_filter(
                $asset['dependencies'],
                function ( $style ) {
                    return wp_style_is( $style, 'registered' );
                }
            ),
            $asset['version']
        );
    }

    /**
     * CF7 Apps Page | Callback
     * 
     * @since 2.2.0
     */
    public function cf7_apps() {
        echo '<div id="root"></div>';
    }
}
endif;