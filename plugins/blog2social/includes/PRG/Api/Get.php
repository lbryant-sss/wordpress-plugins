<?php

class PRG_Api_Get {

    public static function get($url = '', $header = array("Content-Type:application/x-www-form-urlencoded"), $timeout = 30) {
        if (empty($url)) {
            return false;
        }

        $wpVersion = get_bloginfo('version');
        $pluginVersion = implode('.', str_split((string) B2S_PLUGIN_VERSION));
        $ua = sprintf(
                'Blog2SocialBot/1.0 (WP/%s; Plugin/%s; +https://en.blog2social.com/bot-info; bot@blog2social.com)',
                $wpVersion,
                $pluginVersion
        );

        $args = array(
            'timeout' => $timeout,
            'redirection' => '5',
            'user-agent' => $ua,
            'headers' => $header
        );

        return wp_remote_retrieve_body(wp_remote_get($url, $args));
    }
}
