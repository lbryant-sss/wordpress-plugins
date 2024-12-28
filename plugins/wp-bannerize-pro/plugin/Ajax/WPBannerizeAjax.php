<?php

namespace WPBannerize\Ajax;

use WPBannerize\Models\WPBannerizeImpressions;
use WPBannerize\WPBones\Foundation\WordPressAjaxServiceProvider as ServiceProvider;

class WPBannerizeAjax extends ServiceProvider
{


    /**
     * List of the ajax actions executed only by logged in users.
     * Here you will used a methods list.
     *
     * @var array
     */
    protected $logged = [
        'wp_bannerize_action_sorting_post_page',
        'wp_bannerize_banners_list',
        'wp_bannerize_layout',
    ];

    /**
     * List of the ajax actions executed only by not logged-in user, usually from frontend.
     * Here you will use a methods list.
     *
     * @var array
     */
    protected $notLogged = [];

    protected $nonceKey = 'nonce';

    protected $nonceHash = 'wp-bannerize-pro';

    public function wp_bannerize_banners_list()
    {
        [$dateFrom, $dateTo, $categories] = $this->useHTTPPost('date_from', 'date_to', 'category');

        // get post data
        $dateFrom = wp_bannerize_pro_sanitize_mysql_datetime($dateFrom);
        $dateTo = wp_bannerize_pro_sanitize_mysql_datetime($dateTo);
        $categories = $categories ?? [];

        $banners = WPBannerizeImpressions::groupBy('GROUP BY impressions.banner_id')
            ->dateFrom($dateFrom)
            ->dateTo($dateTo)
            ->categories($categories)
            ->get();

        ob_start();

        echo WPBannerize()->view('analytics.report-banners')->with('banners', $banners);
        $content = ob_get_contents();
        ob_end_clean();

        $result = [
            'html' => $content,
        ];

        wp_send_json_success($result);
    }

    /**
     * Updated `menu_order` field in post table.
     *
     * @internal param $_POST['sorted'] List sequence of sorted items
     * @internal param $_POST['paged'] Pagination value
     * @internal param $_POST['per_page'] Number of items per page
     *
     */
    public function wp_bannerize_action_sorting_post_page()
    {
        /**
         * @var wpdb $wpdb
         */
        global $wpdb;

        [$sorted, $paged, $per_page] = $this->useHTTPPost('sorted', 'paged', 'per_page');

        $sorted = wp_parse_args($sorted);
        $paged = absint(esc_attr($paged));
        $per_page = absint(esc_attr($per_page));

        if (is_array($sorted['post'])) {
            $offset = ($paged - 1) * $per_page;
            foreach ($sorted['post'] as $key => $value) {
                $menu_order = $key + $offset;
                $wpdb->query($wpdb->prepare("UPDATE $wpdb->posts SET menu_order = %d WHERE ID = %d", $menu_order, $value));
            }
        }

        wp_send_json_success();
    }

    public function wp_bannerize_layout()
    {
        [$border, $value] = $this->useHTTPPost('border', 'value');

        $allowed = ['top', 'right', 'bottom', 'left'];

        if (isset($border) && in_array($border, $allowed)) {
            if (empty($value) || !is_numeric($value)) {
                $value = null;
            }

            WPBannerize()->options->set("Layout.{$border}", $value);

            wp_send_json_success(["Layout.{$border}" => $value, WPBannerize()->options->toArray()]);
        }

        wp_send_json_error(['description' => 'Wrong values types']);
    }
}
