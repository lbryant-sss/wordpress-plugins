<?php

namespace WPBannerize\Shortcodes;

use WPBannerize\GeoLocalizer\GeoLocalizerProvider;
use WPBannerize\WPBones\Foundation\WordPressShortcodesServiceProvider as ServiceProvider;

class WPBannerizeShortcode extends ServiceProvider
{

    /**
     * List of registered shortcodes. {shortcode}/method
     *
     * @var array
     */
    protected $shortcodes = [
        'wp_bannerize_pro' => 'wp_bannerize',
        'wp_bannerize_pro_geo' => 'wp_bannerize_pro_geo',
        'wp_bannerize_pro_mobile' => 'wp_bannerize_pro_mobile',
        'wp_bannerize_pro_desktop' => 'wp_bannerize_pro_desktop',
    ];


    /**
     * WP Bannerize Pro shortcode.
     *
     * @param array $atts Optional.Attribute into the shortcode
     * @param null $content Optional. HTML content
     *
     * @return string
     */
    public function wp_bannerize($atts = [], $content = null)
    {
        // Default values for shortcode
        $defaults = [
            'random' => false, // deprecated since 1.3.5 - use 'orderby' instead
            'category' => '', // deprecated since 1.5.0 - use 'categories' instead
            'categories' => '', // deprecated since 1.8.0 - use 'campaigns' instead
            'id' => false,
            'numbers' => 10,
            'order' => 'DESC',
            'campaigns' => '',
            'rank_seed' => true,
            'orderby' => 'menu_order', // 'impressions', 'clicks', 'ctr', 'random'
            'post_categories' => '',
            'layout' => 'vertical',
            'mobile' => false,
            'desktop' => false,
        ];

        $atts = shortcode_atts($defaults, $atts, 'wp_bannerize');

        // Check for deprecated attributes
        if (!empty($atts['random'])) {
            _deprecated_argument('random', '1.3.5', __('Use "orderby" instead of "random"', 'wp-bannerize'));
            $atts['orderby'] = 'random';
        }

        if (!empty($atts['category'])) {
            _deprecated_argument('category', '1.5.0', __('Use "categories" instead of "category"', 'wp-bannerize'));
            $atts['categories'] = $atts['category'];
        }

        if (!empty($atts['categories'])) {
            _deprecated_argument('categories', '1.8.0', __('Use "campaigns" instead of "categories"', 'wp-bannerize'));
            $atts['campaigns'] = $atts['categories'];
        }

        ob_start();

        if (function_exists('wp_bannerize_pro')) {
            wp_bannerize_pro($atts);
        }

        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    public function wp_bannerize_pro_geo($atts = [], $content = null)
    {
        return GeoLocalizerProvider::shortcode($atts, $content);
    }

    public function wp_bannerize_pro_mobile($atts = [], $content = null)
    {
        if (wpbones_user_agent()->isMobile()) {
            return do_shortcode($content);
        }
    }

    public function wp_bannerize_pro_desktop($atts = [], $content = null)
    {
        if (!wpbones_user_agent()->isMobile()) {
            return do_shortcode($content);
        }
    }
}