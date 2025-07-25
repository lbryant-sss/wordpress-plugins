<?php
/*
Plugin Name: zipaddr-jp
Plugin URI: https://zipaddr2.com/wordpress/
Description: The input convert an address from a zip code automatically.
Version: 1.40
Author: Tatsuro, Terunuma
Author URI: https://pierre-soft.com/
License: GPLv2 or later
*/
define('zipaddr_VERS', '1.40');
define('zipaddr_KEYS', 'zipaddr-config');
define('zipaddr_SYS',  'sys_');
define('zipaddr_COM',  'https://zipaddr.com/');
define('zipaddr2COM',  'https://zipaddr2.com/');
define('zipaddr_git',  'https://zipaddr.github.io/');
define('zipaddr_DEFINE', 'zipaddr_define');
define('zipaddrMei', plugin_basename(dirname(__FILE__)));
define('zipaddr_PLUGIN_DIR', plugin_dir_path(__FILE__)); // /myplugin/
define('zipaddr_FILE1', str_replace("/".zipaddrMei."/","",zipaddr_PLUGIN_DIR)."/zipaddr_define.txt"); //旧
define('zipaddr_FILE2', zipaddr_PLUGIN_DIR."include/zipaddrjp_define.php"); //新

	$plugin_name= "";
	$keywd= "usces_";
	if( !empty($_SERVER["REQUEST_URI"]) ){
		$wk= trim( sanitize_text_field(wp_unslash($_SERVER["REQUEST_URI"])) );
		$wk= htmlspecialchars($wk, ENT_QUOTES);
		if( strpos($wk,'?page='.$keywd) !== false ) $plugin_name= $keywd;
	}
	require_once zipaddr_PLUGIN_DIR.'include/zipaddrjp_config.php';

if( is_admin() && $plugin_name == $keywd ){     // welcart
	define( 'zipaddr_IDENT', '3');
	require_once zipaddr_PLUGIN_DIR.'zipaddr.php';
	add_filter('usces_filter_apply_admin_addressform', 'zipaddr_jp_usces', 99999, 3);// welcart
}
else
if( is_admin() ){                                 // admin
	define( 'zipaddr_IDENT', '2');
	require_once zipaddr_PLUGIN_DIR.'admin.php';
	add_action('admin_menu', 'zipaddr_admin_menu');
	if( function_exists('register_uninstall_hook') ) {register_uninstall_hook( __FILE__, 'zipaddr_uninstall' );} // uninstall呼出し
}
else {                                            // user
	define( 'zipaddr_IDENT', '1');
	require_once zipaddr_PLUGIN_DIR.'zipaddr.php';
	add_filter('usces_filter_apply_addressform', 'zipaddr_jp_usces', 99999, 3);// welcart
	add_filter('usces_filter_cart_delivery_script','zipaddr_jp_welcart', 99999, 3);// welcart
	add_filter('the_content', 'zipaddr_jp_change', 99999); // html change
}

function zipaddr_jp_usces($formtag,$type,$data) {return zipaddr_jp_change($formtag,"1");}
function zipaddr_jp_welcart($script) {return $script;
	$keywd1="if(delivery_days[selected]";
$addon="
if(typeof Zip.welorder==='function'){
	var wk1= $('#delivery_country').val();
	var wk2= $('#delivery_pref').val();
	if( wk1!='' && wk2!='' ) {delivery_country=wk1; delivery_pref=wk2;}
}
";
	$wk0= strpos($script,$keywd1);
	if( $wk0!==false ) {$script= str_replace($keywd1, $addon.$keywd1, $script);}
	return $script;
}
function zipaddr_uninstall() {delete_option(zipaddr_DEFINE);} // uninstall処理
?>
