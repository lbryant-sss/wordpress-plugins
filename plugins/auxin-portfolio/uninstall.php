<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * 
 * @package    Auxin
 * @license    LICENSE.txt
 * @author     averta
 * @link       https://phlox.pro
 * @copyright  (c) 2010-2025 averta
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit( 'No Naughty Business Please !' );
}
