<?php
/**
 * Wp ULike FrontEnd Scripts Class.
 * 
 * @package    wp-ulike
 * @author     TechnoWich 2025
 * @link       https://wpulike.com
*/

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'wp_ulike_frontend_assets' ) ) {
	/**
	 *  Class to load and print the front-end scripts
	 */
	class wp_ulike_frontend_assets {

	  	private $hook;

	  	/**
	   	 * __construct
	   	 */
	  	function __construct() {
			// general assets
        	add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	  	}

		public function enqueue(){
	    	// If user has been disabled this page in options, then return.
			if( ! is_wp_ulike( wp_ulike_get_option( 'disable_plugin_files' ), array(), true ) ) {
				return;
			}
	        // general assets
	        $this->load_styles();
	        $this->load_scripts();
		}

	  	/**
	  	 * Styles for admin
	   	 *
	   	 * @return void
	   	 */
	  	public function load_styles() {

	        wp_enqueue_style( WP_ULIKE_SLUG, WP_ULIKE_ASSETS_URL . '/css/wp-ulike.min.css', array(), WP_ULIKE_VERSION );

			// load custom.css if the directory is writable. else use inline css fallback - Make sure the auxin-elements is installed
			if( ! wp_ulike_is_true( get_option( 'wp_ulike_use_inline_custom_css', true ) ) &&
			! wp_ulike_is_true( wp_ulike_get_option( 'enable_inline_custom_css', false ) ) ){
				$uploads   = wp_get_upload_dir();
				$css_file  = $uploads['baseurl'] . '/' . WP_ULIKE_SLUG . '/custom.css';

				wp_enqueue_style( WP_ULIKE_SLUG . '-custom', $css_file, array( WP_ULIKE_SLUG ), WP_ULIKE_VERSION );
			} else {
				//add your custom style from setting panel.
				wp_add_inline_style( WP_ULIKE_SLUG, wp_ulike_get_custom_style() );
			}

	  	}

	    /**
	     * Scripts for admin
	     *
	     * @return void
	     */
	  	public function load_scripts() {
			// Return if pro assets exist
			if( defined( 'WP_ULIKE_PRO_VERSION' ) && version_compare( WP_ULIKE_PRO_VERSION, '1.5.3' ) > 0 ){
				return;
			}

			//Add wp_ulike script file with special functions.
			wp_enqueue_script( 'wp_ulike', WP_ULIKE_ASSETS_URL . '/js/wp-ulike.min.js', array( 'jquery' ), WP_ULIKE_VERSION, true );

			//localize script
			wp_localize_script( 'wp_ulike', 'wp_ulike_params', array(
				'ajax_url'      => admin_url( 'admin-ajax.php' ),
				'notifications' => wp_ulike_get_option( 'enable_toast_notice' )
			));
	  	}

	}

}