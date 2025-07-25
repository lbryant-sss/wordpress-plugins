<?php
/*
Plugin Name: Backuply
Plugin URI: http://wordpress.org/plugins/backuply/
Description: Backuply is a Wordpress Backup plugin. Backups are the best form of security and safety a website can have.
Version: 1.4.7
Author: Softaculous
Author URI: https://backuply.com
License: LGPL v2.1
License URI: http://www.gnu.org/licenses/lgpl-2.1.html
*/

// We need the ABSPATH
if (!defined('ABSPATH')) exit;

if(!function_exists('add_action')){
	echo 'You are not allowed to access this page directly.';
	exit;
}

$backuply_tmp_plugins = get_option('active_plugins', []);

// Is the premium plugin loaded ?
if(in_array('backuply-pro/backuply-pro.php', $backuply_tmp_plugins)){
	
	// The following variable was not there prior to 1.2.1 in the pro version when there was no dependence on the free version
	$backuply_pro_version = get_option('backuply_pro_version');
	if(empty($backuply_pro_version)){
		return;
	}
}

// If BACKUPLY_VERSION exists then the plugin is loaded already !
if(defined('BACKUPLY_VERSION')) {
	return;
}

define('BACKUPLY_FILE', __FILE__);

include_once(dirname(__FILE__).'/init.php');
