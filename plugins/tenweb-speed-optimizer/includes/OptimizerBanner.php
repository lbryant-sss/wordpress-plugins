<?php

namespace TenWebOptimizer;

class OptimizerBanner
{
    private $autoUpdateBanner = false;

    public function __construct()
    {
        $this->setArgs();
    }

    public function printBanners()
    {
        if ($this->autoUpdateBanner) {
            $this->AutoUpdateBanner();
        }
    }

    private function setArgs()
    {
        global $pagenow;
        $auto_update_banner_array = ['plugins.php', 'edit.php'];
        $this->autoUpdateBanner = empty(get_option('two_auto_update_banner_was_shown'))
            && in_array($pagenow, $auto_update_banner_array)
            && \TenWebOptimizer\OptimizerUtils::is_tenweb_booster_connected();
    }

    private function enqueueMainScripts()
    {
        wp_enqueue_style(
            'two_banner_css',
            TENWEB_SO_URL . '/assets/css/banner_main.css',
            [],
            TENWEB_SO_VERSION
        );
        wp_enqueue_script(
            'banner_main_js',
            TENWEB_SO_URL . '/assets/js/banner_main.js',
            ['jquery', 'two_speed_js'],
            TENWEB_SO_VERSION
        );
    }

    private function enqueueAutoUpdateScripts()
    {
        $this->enqueueMainScripts();
        wp_enqueue_style(
            'two_autoupdate_banner_css',
            TENWEB_SO_URL . '/assets/css/autoupdate_banner.css',
            ['two_banner_css'],
            TENWEB_SO_VERSION
        );

        wp_enqueue_script(
            'two_autoupdate_banner_js',
            TENWEB_SO_URL . '/assets/js/autoupdate_banner.js',
            ['jquery', 'two_speed_js', 'banner_main_js'],
            TENWEB_SO_VERSION
        );
    }

    private function AutoUpdateBanner()
    {
        if (strtolower(TWO_SO_ORGANIZATION_NAME) == '10web') {
            $this->enqueueAutoUpdateScripts();
            require_once TENWEB_SO_PLUGIN_DIR . '/views/autoupdate_banner.php';
        }
    }

    public static function two_set_autoupdate_from_banner()
    {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';

        if (!wp_verify_nonce($nonce, 'two_ajax_nonce') || !OptimizerUtils::check_admin_capabilities()) {
            die('Permission Denied.');
        }
        $auto_update = isset($_POST['auto_update']) ? sanitize_text_field($_POST['auto_update']) : '';

        if ($auto_update == 'enable') {
            global $TwoSettings;
            $TwoSettings->update_setting('two_enable_plugin_autoupdate', 'on');
        }
        add_option( //show autoupdate banner only once and don/t delete this option even on disconnect
            'two_auto_update_banner_was_shown',
            1,
            false
        );

        wp_send_json_success(['status' => 'success']);
    }
}
