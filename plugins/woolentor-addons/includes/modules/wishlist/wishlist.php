<?php
use WooLentor\Traits\ModuleBase;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Plugin Main Class
 */
class Woolentor_WishSuite_Base{
    use ModuleBase;
    
    /**
     * [__construct] Class Constructor
     */
    private function __construct(){
        $this->define_constants();
        $this->includes();
        if( self::$_enabled ){
            if( get_option('woolentor_wishsuite_status', 'no') === 'no' ){
                add_action( 'wp_loaded',[ $this, 'activate' ] );
                update_option( 'woolentor_wishsuite_status','yes' );
            }   
        }
        $this->init_plugin();
    }

    /**
     * Define the required plugin constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'WISHSUITE_FILE', __FILE__ );
        define( 'WISHSUITE_MODULE_PATH', __DIR__ );
        define( 'WISHSUITE_URL', plugins_url( '', WISHSUITE_FILE ) );
        define( 'WISHSUITE_DIR', plugin_dir_path( WISHSUITE_FILE ) );
        define( 'WISHSUITE_ASSETS', WISHSUITE_URL . '/assets' );
        define( 'WISHSUITE_BASE', plugin_basename( WISHSUITE_FILE ) );
        define( 'WISHSUITE_BLOCKS_PATH', WISHSUITE_MODULE_PATH. "/includes/blocks" );
        define( 'WISHSUITE_ENABLED', self::$_enabled );
    }

    /**
     * [includes] Load file
     * @return [void]
     */
    public function includes(){
        require_once(__DIR__ . '/includes/classes/Installer.php');
        require_once(__DIR__ . '/includes/helper-functions.php');
        require_once( __DIR__. '/includes/classes/Manage_Data.php' );
        require_once(__DIR__ . '/includes/classes/Assets.php');
        require_once(__DIR__ . '/includes/classes/Admin.php');
        require_once(__DIR__ . '/includes/classes/Frontend.php');
        require_once(__DIR__ . '/includes/classes/Ajax.php');
        require_once(__DIR__ . '/includes/classes/Widgets_And_Blocks.php');

    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init_plugin() {

        if ( $this->is_request( 'admin' ) || $this->is_request( 'rest' ) ) {
            WishSuite\Admin::instance();
        }

        if( self::$_enabled ){

            WishSuite\Assets::instance();

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
                WishSuite\Ajax::instance();
            }

            WishSuite\Frontend::instance();
            WishSuite\Widgets_And_Blocks::instance();

            // add image size
            $this->set_image_size();

            // let's filter the woocommerce image size
            add_filter( 'woocommerce_get_image_size_wishsuite-image', [ $this, 'wc_image_filter_size' ], 10, 1 );

        }

    }

    /**
     * Do stuff upon plugin activation
     *
     * @return void
     */
    public function activate() {
        $installer = new WishSuite\Installer();
        $installer->run();
    }

    /**
     * [set_image_size] Set Image Size
     */
    public function set_image_size(){

        $image_dimention = woolentor_get_option( 'image_size', 'wishsuite_table_settings_tabs', array( 'width'=>80,'height'=>80 ) );
        if( isset( $image_dimention ) && is_array( $image_dimention ) ){
            $hard_crop = !empty( woolentor_get_option( 'hard_crop', 'wishsuite_table_settings_tabs' ) ) ? true : false;
            add_image_size( 'wishsuite-image', absint( $image_dimention['width'] ), absint( $image_dimention['height'] ), $hard_crop );
        }

    }

    /**
     * [wc_image_filter_size]
     * @return [array]
     */
    public function wc_image_filter_size(){

        $image_dimention = woolentor_get_option( 'image_size', 'wishsuite_table_settings_tabs', array( 'width'=>80,'height'=>80 ) );
        $hard_crop = !empty( woolentor_get_option( 'hard_crop', 'wishsuite_table_settings_tabs' ) ) ? true : false;

        if( isset( $image_dimention ) && is_array( $image_dimention ) ){
            return array(
                'width'  => isset( $image_dimention['width'] ) ? absint( $image_dimention['width'] ) : 80,
                'height' => isset( $image_dimention['height'] ) ? absint( $image_dimention['height'] ) : 80,
                'crop'   => isset( $hard_crop ) ? 1 : 0,
            );
        }
        
    }

}