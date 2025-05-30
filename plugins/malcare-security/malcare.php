<?php
/*
Plugin Name: MalCare WordPress Security Plugin - Malware Scanner, Cleaner, Security Firewall
Plugin URI: https://www.malcare.com
Description: MalCare WordPress Security Plugin - Malware Scanner, Cleaner, Security Firewall
Author: MalCare Security
Author URI: https://www.malcare.com
Version: 6.02
Network: True
License: GPLv2 or later
License URI: [http://www.gnu.org/licenses/gpl-2.0.html](http://www.gnu.org/licenses/gpl-2.0.html)
 */

/*  Copyright 2017  MalCare  (email : support@malcare.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Global response array */

if (!defined('ABSPATH')) exit;
##OLDWPR##

require_once dirname( __FILE__ ) . '/wp_settings.php';
require_once dirname( __FILE__ ) . '/wp_site_info.php';
require_once dirname( __FILE__ ) . '/wp_db.php';
require_once dirname( __FILE__ ) . '/wp_api.php';
require_once dirname( __FILE__ ) . '/wp_actions.php';
require_once dirname( __FILE__ ) . '/info.php';
require_once dirname( __FILE__ ) . '/account.php';
require_once dirname( __FILE__ ) . '/helper.php';
require_once dirname( __FILE__ ) . '/wp_file_system.php';
require_once dirname( __FILE__ ) . '/wp_2fa/wp_2fa.php';

require_once dirname( __FILE__ ) . '/wp_login_whitelabel.php';

##WPCACHEMODULE##


$bvsettings = new MCWPSettings();
$bvsiteinfo = new MCWPSiteInfo();
$bvdb = new MCWPDb();


$bvapi = new MCWPAPI($bvsettings);
$bvinfo = new MCInfo($bvsettings);
$wp_action = new MCWPAction($bvsettings, $bvsiteinfo, $bvapi);

register_uninstall_hook(__FILE__, array('MCWPAction', 'uninstall'));
register_activation_hook(__FILE__, array($wp_action, 'activate'));
register_deactivation_hook(__FILE__, array($wp_action, 'deactivate'));


add_action('wp_footer', array($wp_action, 'footerHandler'), 100);
add_action('mc_clear_bv_services_config', array($wp_action, 'clear_bv_services_config'));

##SOADDUNINSTALLACTION##

##DISABLE_OTHER_OPTIMIZATION_PLUGINS##

if (defined('WP_CLI') && WP_CLI) {
		require_once dirname( __FILE__ ) . '/wp_cli.php';
		$wp_cli = new MCWPCli($bvsettings, $bvinfo, $bvsiteinfo, $bvapi);
		WP_CLI::add_command("malcare", $wp_cli);
}

if (is_admin()) {
	require_once dirname( __FILE__ ) . '/wp_admin.php';
	$wpadmin = new MCWPAdmin($bvsettings, $bvsiteinfo);
	add_action('admin_init', array($wpadmin, 'initHandler'));
	add_filter('all_plugins', array($wpadmin, 'initWhitelabel'));
	add_filter('plugin_row_meta', array($wpadmin, 'hidePluginDetails'), 10, 2);
	add_filter('debug_information', array($wpadmin, 'handlePluginHealthInfo'), 10, 1);
	if ($bvsiteinfo->isMultisite()) {
		add_action('network_admin_menu', array($wpadmin, 'menu'));
	} else {
		add_action('admin_menu', array($wpadmin, 'menu'));
	}
	add_filter('plugin_action_links', array($wpadmin, 'settingsLink'), 10, 2);
	add_action('admin_head', array($wpadmin, 'removeAdminNotices'), 3);
	##POPUP_ON_DEACTIVATION##
	add_action('admin_notices', array($wpadmin, 'activateWarning'));
	add_action('admin_enqueue_scripts', array($wpadmin, 'mcsecAdminMenu'));
	##ALPURGECACHEFUNCTION##
	##ALADMINMENU##
}

if ((array_key_exists('bvreqmerge', $_POST)) || (array_key_exists('bvreqmerge', $_GET))) { // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
	$_REQUEST = array_merge($_GET, $_POST); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
}

#Service active check
if ($bvinfo->config != false) {
	add_action('mc_remove_bv_preload_include', array($wp_action, 'removeBVPreload'));
}

require_once dirname( __FILE__ ) . '/php_error_monitoring/monitoring.php';
MCWPPHPErrorMonitoring::init();

if ($bvinfo->hasValidDBVersion()) {
	if ($bvinfo->isServiceActive('activity_log')) {
		require_once dirname( __FILE__ ) . '/wp_actlog.php';
		$bvconfig = $bvinfo->config;
		$actlog = new BVWPActLog($bvdb, $bvsettings, $bvinfo, $bvconfig['activity_log']);
		$actlog->init();
	}

	##MAINTENANCEMODULE##
}

if (MCHelper::getRawParam('REQUEST', 'bvplugname') == "malcare") {
	require_once dirname( __FILE__ ) . '/callback/base.php';
	require_once dirname( __FILE__ ) . '/callback/response.php';
	require_once dirname( __FILE__ ) . '/callback/request.php';
	require_once dirname( __FILE__ ) . '/recover.php';

	$pubkey = MCHelper::getRawParam('REQUEST', 'pubkey');
	$pubkey = isset($pubkey) ? MCAccount::sanitizeKey($pubkey) : '';
	$rcvracc = MCHelper::getRawParam('REQUEST', 'rcvracc');

	if (isset($rcvracc)) {
		$account = MCRecover::find($bvsettings, $pubkey);
	} else {
		$account = MCAccount::find($bvsettings, $pubkey);
	}

	$request = new BVCallbackRequest($account, $_REQUEST, $bvsettings); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$response = new BVCallbackResponse($request->bvb64cksize);

	if ($request->authenticate() === 1) {
		$bv_frm_tstng = MCHelper::getRawParam('REQUEST', 'bv_frm_tstng');
		if (isset($bv_frm_tstng)) {
			require_once dirname(__FILE__) . '/form_testing/form_testing.php';
			$form_testing = new BVFormTesting($_REQUEST); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$form_testing->init();

		} else {
			define('MCBASEPATH', plugin_dir_path(__FILE__));


			require_once dirname( __FILE__ ) . '/callback/handler.php';

			$params = $request->processParams($_REQUEST); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ($params === false) {
				$response->terminate($request->corruptedParamsResp());
			}
			$request->params = $params;
			$callback_handler = new BVCallbackHandler($bvdb, $bvsettings, $bvsiteinfo, $request, $account, $response);
			if ($request->is_afterload) {
				add_action('wp_loaded', array($callback_handler, 'execute'));
			} else if ($request->is_admin_ajax) {
				add_action('wp_ajax_bvadm', array($callback_handler, 'bvAdmExecuteWithUser'));
				add_action('wp_ajax_nopriv_bvadm', array($callback_handler, 'bvAdmExecuteWithoutUser'));
			} else {
				$callback_handler->execute();
			}
		}
	} else {
		$response->terminate($request->authFailedResp());
	}
} else {
	if ($bvinfo->hasValidDBVersion()) {
		if ($bvinfo->isProtectModuleEnabled()) {
			require_once dirname( __FILE__ ) . '/protect/protect.php';
			//For backward compatibility.
			MCProtect_V602::$settings = new MCWPSettings();
			MCProtect_V602::$db = new MCWPDb();
			MCProtect_V602::$info = new MCInfo(MCProtect_V602::$settings);

			add_action('mc_clear_pt_config', array('MCProtect_V602', 'uninstall'));

			if ($bvinfo->isActivePlugin()) {
				MCProtect_V602::init(MCProtect_V602::MODE_WP);
			}
		}

		if ($bvinfo->isDynSyncModuleEnabled()) {
		require_once dirname( __FILE__ ) . '/wp_dynsync.php';
		$bvconfig = $bvinfo->config;
		$dynsync = new BVWPDynSync($bvdb, $bvsettings, $bvconfig['dynsync']);
		$dynsync->init();
	}

	}
	$bv_site_settings = $bvsettings->getOption('bv_site_settings');
	if (isset($bv_site_settings)) {
		if (isset($bv_site_settings['wp_auto_updates'])) {
			$wp_auto_updates = $bv_site_settings['wp_auto_updates'];
			if (array_key_exists('block_auto_update_core', $wp_auto_updates)) {
				add_filter('auto_update_core', '__return_false' );
			}
			if (array_key_exists('block_auto_update_theme', $wp_auto_updates)) {
				add_filter('auto_update_theme', '__return_false' );
				add_filter('themes_auto_update_enabled', '__return_false' );
			}
			if (array_key_exists('block_auto_update_plugin', $wp_auto_updates)) {
				add_filter('auto_update_plugin', '__return_false' );
				add_filter('plugins_auto_update_enabled', '__return_false' );
			}
			if (array_key_exists('block_auto_update_translation', $wp_auto_updates)) {
				add_filter('auto_update_translation', '__return_false' );
			}
		}
	}

	if (is_admin()) {
		add_filter('site_transient_update_plugins', array($wpadmin, 'hidePluginUpdate'));
	}

	##THIRDPARTYCACHINGMODULE##
}

if (MCWP2FA::isEnabled($bvsettings)) {
	$wp_2fa = new MCWP2FA();
	$wp_2fa->init();
}

if (!empty($bvinfo->getLPWhitelabelInfo())) {
	$wp_login_whitelabel = new MCWPLoginWhitelabel();
	$wp_login_whitelabel->init();
}

add_action('mc_clear_wp_2fa_config', array($wp_action, 'clear_wp_2fa_config'));