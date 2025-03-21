<?php
/**
 * Plugin Name: Custom Sidebars
 * Plugin URI:  https://wordpress.org/plugins/custom-sidebars/
 * Description: Allows you to create widgetized areas and custom sidebars. Replace whole sidebars or single widgets for specific posts and pages.
 * Version:     3.38
 * Author:      WebFactory Ltd
 * Author URI:  https://www.webfactoryltd.com/
 * Textdomain:  custom-sidebars
 * License: GPLv2 or later
 * Tested up to: 6.7
 */

/*
Copyright Incsub 2017 - 2020 (https://incsub.com)
Copyright WebFactory Ltd 2020 - 2025 (https://www.webfactoryltd.com/)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

This plugin was originally developed by Javier Marquez. http://arqex.com/
*/

function inc_sidebars_init() {
	if ( class_exists( 'CustomSidebars' ) ) {
		return;
	}

	/**
	 * Do not load plugin when saving file in WP Editor
	 */
	if ( isset( $_REQUEST['action'] ) && 'edit-theme-plugin-file' == $_REQUEST['action'] ) {
		return;
	}

	/**
	 * if admin, load only on proper pages
	 */
	if ( is_admin() && isset( $_SERVER['SCRIPT_FILENAME'] ) ) {
		$file = basename( $_SERVER['SCRIPT_FILENAME'] );
		$allowed = array(
			'edit.php',
			'admin-ajax.php',
			'post.php',
			'plugins.php',
			'post-new.php',
			'widgets.php',
		);
		/**
		 * Allowed pages array.
		 *
		 * To change where Custom Sidebars is loaded, use this filter.
		 *
		 * @since 3.2.3
		 *
		 * @param array $allowed Allowed pages list.
		 */
		$allowed = apply_filters( 'custom_sidebars_allowed_pages_array', $allowed );
		if ( ! in_array( $file, $allowed ) ) {
			return;
		}
	}

	$plugin_dir = dirname( __FILE__ );
	$plugin_dir_rel = dirname( plugin_basename( __FILE__ ) );
	$plugin_url = plugin_dir_url( __FILE__ );

	define( 'CSB_PLUGIN', __FILE__ );
	define( 'CSB_IS_PRO', false );
	define( 'CSB_VIEWS_DIR', $plugin_dir . '/views/' );
	define( 'CSB_INC_DIR', $plugin_dir . '/inc/' );
	define( 'CSB_JS_URL', $plugin_url . 'assets/js/' );
	define( 'CSB_CSS_URL', $plugin_url . 'assets/css/' );
	define( 'CSB_IMG_URL', $plugin_url . 'assets/img/' );

	// Include function library.
	$modules[] = CSB_INC_DIR . 'external/wpmu-lib/core.php';
	$modules[] = CSB_INC_DIR . 'class-custom-sidebars.php';


	// Free-version configuration - no drip campaign yet...
	$cta_label = false;
	$drip_param = false;




	foreach ( $modules as $path ) {
		if ( file_exists( $path ) ) { require_once $path; }
	}

	// Register the current plugin, for pro and free plugins!
	do_action(
		'wdev-register-plugin',
		/*             Plugin ID */ plugin_basename( __FILE__ ),
		/*          Plugin Title */ 'CustomSidebars',
		/* https://wordpress.org */ '/plugins/custom-sidebars/',
		/*      Email Button CTA */ $cta_label,
		/*  getdrip Plugin param */ $drip_param
	);

	// Initialize the plugin
	CustomSidebars::instance();
}

inc_sidebars_init();

if ( ! class_exists( 'CustomSidebarsEmptyPlugin' ) ) {
	class CustomSidebarsEmptyPlugin extends WP_Widget {
		public function __construct() {
			parent::__construct( false, $name = 'CustomSidebarsEmptyPlugin' );
		}
		public function form( $instance ) {
			//Nothing, just a dummy plugin to display nothing
		}
		public function update( $new_instance, $old_instance ) {
			//Nothing, just a dummy plugin to display nothing
		}
		public function widget( $args, $instance ) {
			echo '';
		}
	} //end class
} //end if class exists


// Translation.
function inc_sidebars_init_translation() {
	load_plugin_textdomain( 'custom-sidebars', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'inc_sidebars_init_translation' );

// since the notification needs to be global and show everywhere we'll add it outside the plugin's class
add_action('init', function() {
  add_action('admin_notices', function() {
    global $wp_version;
    
    if ( !class_exists( 'CustomSidebars' ) ) {
        return;
    }

    if ((false == is_plugin_active('classic-widgets/classic-widgets.php') && apply_filters('use_widgets_block_editor', true)) && version_compare($wp_version, '5.8', '>=') == true) {
      CustomSidebars::wp_kses_wf('<div class="error notice" style="max-width: 700px;"><p><b>🔥 IMPORTANT 🔥</b><br><br>Custom Sidebars plugin is NOT compatible with the new widgets edit screen (powered by Gutenberg).<br>Install the official <a href="' . admin_url('plugin-install.php?s=classic%20widgets&tab=search&type=term') . '">Classic Widgets</a> plugin if you want to continue using it.</p></div>');
    }
  });
}, 1000, 0);
