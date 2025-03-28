<?php
/************************************************************
 * This plugin was modified by Revmakx                      *
 * Copyright (c) 2012 Revmakx                               *
 * www.revmakx.com                                          *
 *                                                          *
 ************************************************************/

if ( ! defined('ABSPATH') )
    die();

class IWP_MMB_FixCompatibility 
{

    public function fixWpSpamShieldBan()
    {
        $wpss_ubl_cache = get_option('spamshield_ubl_cache');

        if (empty($wpss_ubl_cache)){
            return;
        }

        $serverIp = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;

        foreach ($wpss_ubl_cache as $key => $singleIp) {
            if ($singleIp !== $serverIp) {
                continue;
            }

            unset($wpss_ubl_cache[$key]);
        }

        update_option('spamshield_ubl_cache', array_values($wpss_ubl_cache));
    }

    public function fixSpamShield()
    {
        if (!defined('WPSS_IP_BAN_CLEAR')) {
            define('WPSS_IP_BAN_CLEAR', true);
        }
    }

    public function fixSidekickPlugin()
    {
        add_action('init', array($this, '_fixSidekickPlugin'), -1);
    }

    public function _fixSidekickPlugin()
    {
        $this->removeByPluginClass('admin_init', 'Sidekick', 'redirect', true);
    }

    public function fixShieldUserManagementICWP()
    {
        add_filter('icwp-wpsf-visitor_is_whitelisted', '__return_true');
    }

    public function fixDuoFactor()
    {

        if (!is_plugin_active('duo-universal/duouniversal-wordpress.php')) {
            return;
        }
        add_action('init', array($this, '_fixDuoFactor'), -1);
    }

    function remove_by_plugin_class($tag, $class_name, $functionName, $isAction = false, $priority = 10) {
        if (!class_exists($class_name)) {
            return null;
        }
    
        global $wp_filter;
    
        if (empty($wp_filter[$tag][$priority])) {
            return null;
        }
    
        foreach ($wp_filter[$tag][$priority] as $callable) {
            if (empty($callable['function']) || !is_array($callable['function']) || count($callable['function']) < 2) {
                continue;
            }
    
            if (!is_a($callable['function'][0], $class_name)) {
                continue;
            }
    
            if ($callable['function'][1] !== $functionName) {
                continue;
            }
    
            if ($isAction) {
                remove_action($tag, $callable['function'], $priority);
            } else {
                remove_filter($tag, $callable['function'], $priority);
            }
    
            return $callable['function'];
        }
    
        return null;
    }

    /**
     * @internal
     */
    public function _fixDuoFactor()
    {
        $this->remove_by_plugin_class('init','Duo\DuoUniversalWordpress\DuoUniversal_WordpressPlugin' ,'duo_verify_auth', 10);
    }

    public function fixAllInOneSecurity()
    {
        if (!is_plugin_active('all-in-one-wp-security-and-firewall/wp-security.php')) {
            return;
        }

        add_action('init', array($this, '_fixAllInOneSecurity'), -1);
    }

    /**
     * @internal
     */
    public function _fixAllInOneSecurity()
    {
        $user = wp_get_current_user();

        if (empty($user->ID)) {
            return;
        }
        $time = new DateTime(current_time('mysql', true));
        update_user_meta($user->ID, 'aiowps_last_login_time', $time->format('Y-m-d H:i:s'));
    }

    public function fixWpSimpleFirewall()
    {
        if (!is_plugin_active('wp-simple-firewall/icwp-wpsf.php')) {
            return;
        }

        /** @handled function */
        IWP_FixCompatibility_ICWP_WPSF();
    }

    private function removeByPluginClass($tag, $class_name, $functionName, $isAction = false, $priority = 10)
    {
        if (!class_exists($class_name)) {
            return null;
        }

        global $wp_filter;

        if (empty($wp_filter[$tag][$priority])) {
            return null;
        }

        foreach ($wp_filter[$tag][$priority] as $callable) {
            if (empty($callable['function']) || !is_array($callable['function']) || count($callable['function']) < 2) {
                continue;
            }

            if (!is_a($callable['function'][0], $class_name)) {
                continue;
            }

            if ($callable['function'][1] !== $functionName) {
                continue;
            }

            if ($isAction) {
                remove_action($tag, $callable['function'], $priority);
            } else {
                remove_filter($tag, $callable['function'], $priority);
            }

            return $callable['function'];
        }

        return null;
    }

    public function fixGlobals()
    {
        if (!isset($GLOBALS['hook_suffix'])) {
            $GLOBALS['hook_suffix'] = null;
        }
    }
}

function IWP_FixCompatibility_ICWP_WPSF()
{
    if (class_exists('ICWP_WPSF_Processor_LoginProtect_TwoFactorAuth', false)) {
        return;
    }

    class ICWP_WPSF_Processor_LoginProtect_TwoFactorAuth
    {
        public function run()
        {
        }
    }
}
