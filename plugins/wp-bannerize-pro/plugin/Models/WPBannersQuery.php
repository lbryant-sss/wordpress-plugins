<?php

namespace WPBannerize\Models;

class WPBannersQuery
{
    protected $args;

    protected $posts;

    protected $defaultArgs = [
        'category' => '', // deprecated since 1.5.0 - use 'categories' instead
        'random' => false, // deprecated since 1.3.5 - use 'orderby' instead
        'categories' => '', // deprecated since 1.8.0 - use 'campaigns' instead
        'numbers' => 10,
        'campaigns' => '',
        'order' => 'ASC',
        'rank_seed' => true,
        'orderby' => 'menu_order',
        'post_categories' => '',
        'layout' => 'vertical',
    ];

    public function __construct($args = [])
    {
        $this->args = $this->setDefault($args);

        // Check for deprecated attributes
        if (!empty($this->args['random'])) {
            _deprecated_argument('random', '1.3.5', __('Use "orderby" instead of "random"', 'wp-bannerize'));
            $this->args['orderby'] = 'random';
        }

        if (!empty($this->args['category'])) {
            _deprecated_argument('category', '1.5.0', __('Use "categories" instead of "category"', 'wp-bannerize'));
            $this->args['categories'] = $this->args['category'];
        }

        if (!empty($this->args['categories'])) {
            _deprecated_argument('categories', '1.8.0', __('Use "campaigns" instead of "categories"', 'wp-bannerize'));
            $this->args['campaigns'] = $this->args['categories'];
        }
    }

    protected function setDefault($args)
    {
        $keys = array_keys($this->defaultArgs);
        foreach ($args as $key => $value) {
            if (!in_array($key, $keys)) {
                unset($args[$key]);
            }
        }

        return array_merge($this->defaultArgs, $args);
    }

    public static function query($args = [])
    {
        return new self($args);
    }

    public function isEmpty()
    {
        return is_null($this->posts) || empty($this->posts);
    }

    public function __toString()
    {
        $content = '';

        if (is_null($this->posts)) {
            $this->select();
        }

        if (!is_null($this->posts)) {
            ob_start(); ?>
            <div class="wp_bannerize_container wp_bannerize_layout_<?php echo esc_attr($this->args['layout']); ?>">

                <?php foreach ($this->posts as $post) {
                    echo WPBannerizePost::find($post->ID);
                } ?>
            </div>

            <?php
            $content = ob_get_contents();
            ob_end_clean();

        }

        return $content;
    }

    public function select($args = [])
    {
        if (!empty($args)) {
            $this->args = $this->setDefault($args);
        }

        $this->posts = null;

        // Check for post categories
        $is_category = get_the_category();

        if (!empty($is_category) && !empty($this->args['post_categories'])) {
            $post_categories = $this->args['post_categories'];
            if (is_string($post_categories)) {
                $post_categories = explode(',', $post_categories);
            }
            if (!empty($post_categories)) {
                if (!is_category($post_categories) && !in_category($post_categories)) {
                    return $this;
                }
            }
        }

        // Prepare query
        $query = [
            'numberposts' => $this->args['numbers'],
            'order' => $this->args['order'],
            'orderby' => $this->args['orderby'],
            'post_type' => 'wp_bannerize',

            // Best practices
            'suppress_filters' => false,
            'no_found_rows' => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ];

        // Query tax
        if (!empty($this->args['campaigns'])) {
            $categories_array = is_string($this->args['campaigns'])
                ? explode(',', $this->args['campaigns'])
                : (array)$this->args['campaigns'];

            // Prepare a sanitize array with terms id
            $categories = [];

            // Sanitize input bannerize categories
            foreach ($categories_array as $term_id) {
                if (is_numeric($term_id)) {
                    $categories[] = (int)$term_id;
                } else {
                    $term = get_term_by('slug', $term_id, 'wp_bannerize_tax');
                    if (!empty($term)) {
                        $categories[] = $term->term_id;
                    }
                }
            }

            // Set taxonomy params
            $query['tax_query'] = [
                [
                    'taxonomy' => 'wp_bannerize_tax',
                    'terms' => $categories,
                ],
            ];
        }

        // Sanitize the 'orderby'
        if (in_array($this->args['orderby'], ['random', 'impressions', 'clicks', 'ctr'])) {
            if ('random' == $this->args['orderby']) {
                // Convert
                $query['orderby'] = 'rand';
            } else {
                // Filter the ORDER BY clause of the query.
                add_filter('posts_orderby_request', [$this, 'posts_orderby_request']);

                // Filter the SELECT clause of the query.
                add_filter('posts_fields_request', [$this, 'posts_fields_request']);
            }
        }

        // Filter the JOIN clause of the query.
        add_filter('posts_join_request', [$this, 'posts_join_request']);

        // Filter the WHERE clause of the query.
        add_filter('posts_where_request', [$this, 'posts_where_request']);

        // comment out to debug query
        //add_filter( 'posts_request', create_function( '$a', 'echo( $a ); return $a;' ) );

        // Get posts
        $this->posts = get_posts($query);

        // Remove all previous filters
        remove_filter('posts_orderby_request', [$this, 'posts_orderby_request']);
        remove_filter('posts_fields_request', [$this, 'posts_fields_request']);
        remove_filter('posts_join_request', [$this, 'posts_join_request']);
        remove_filter('posts_where_request', [$this, 'posts_where_request']);

        return $this;
    }

    /**
     * Filter the WHERE clause of the query.
     *
     * For use by caching plugins.
     *
     * @param string $where The WHERE clause of the query.
     *
     * @return string
     *
     */
    public function posts_where_request($where)
    {
        // Date range
        $data_from = 'wp_bannerize_pm_wp_bannerize_banner_date_from';
        $data_expiry = 'wp_bannerize_pm_wp_bannerize_banner_date_expiry';

        $where .= " AND ( {$data_from}.meta_value = '' OR {$data_from}.meta_value <= UNIX_TIMESTAMP() ) ";
        $where .= " AND ( {$data_expiry}.meta_value = '' OR {$data_expiry}.meta_value >= UNIX_TIMESTAMP() ) ";

        return $where;
    }

    /**
     * Filter the ORDER BY clause of the query.
     *
     * For use by caching plugins.
     *
     * @param string $orderby The ORDER BY clause of the query.
     *
     * @return string
     */
    public function posts_orderby_request($orderby)
    {
        $allowed_orders = [
            'impressions' => 'wp_bannerize_pm_wp_bannerize_banner_impressions.meta_value+0',
            'clicks' => 'wp_bannerize_pm_wp_bannerize_banner_clicks.meta_value+0',
            'ctr' => 'CTR+0',
        ];

        // Note the rank seed
        $orderby = sprintf('rank_seed * %s %s', $allowed_orders[$this->args['orderby']], $this->args['order']);

        return $orderby;
    }

    /**
     * Filter the SELECT clause of the query.
     *
     * For use by caching plugins.
     *
     * @param string $fields The SELECT clause of the query.
     *
     * @return string
     */
    public function posts_fields_request($fields)
    {
        // Prepare addition fields
        $fields_array = explode(',', $fields);

        if ('ctr' == $this->args['orderby']) {
            // Calculate CTR
            $clicks = 'wp_bannerize_pm_wp_bannerize_banner_clicks';
            $impression = 'wp_bannerize_pm_wp_bannerize_banner_impressions';
            $fields_array[] = sprintf(' ( %s.meta_value / %s.meta_value ) AS CTR ', $clicks, $impression);
        }

        /**
         * Filter the rank seed expressions.
         *
         * @param string $rank_seed The rank seed expression. Default '1' or '( 0 + ( RAND() * 1  ) )'
         */
        $rank_seed = apply_filters('wp_bannerize_banners_rank_seed', ' 1 ');

        // If rank seed exists
        if (!empty($rank_seed)) {
            $fields_array[] = sprintf(' %s AS rank_seed ', $rank_seed);
        }

        // Concat
        return implode(',', $fields_array);
    }

    /**
     * Filter the JOIN clause of the query.
     *
     * For use by caching plugins.
     *
     * @param string $join The JOIN clause of the query.
     *
     * @return string
     */
    public function posts_join_request($join)
    {
        /**
         * @var wpdb $wpdb
         */
        global $wpdb;

        // Array stack to additional join
        $joins = [];

        // Impressions
        $meta_key = 'wp_bannerize_banner_impressions';
        $pm = 'wp_bannerize_pm_' . $meta_key;
        // Remember to keep a space at begin and end of string
        $joins[] = " LEFT JOIN {$wpdb->postmeta} AS {$pm} ON ( {$pm}.post_id = {$wpdb->posts}.ID AND {$pm}.meta_key = '{$meta_key}' ) ";

        // Clicks
        $meta_key = 'wp_bannerize_banner_clicks';
        $pm = 'wp_bannerize_pm_' . $meta_key;
        // Remember to keep a space at begin and end of string
        $joins[] = " LEFT JOIN {$wpdb->postmeta} AS {$pm} ON ( {$pm}.post_id = {$wpdb->posts}.ID AND {$pm}.meta_key = '{$meta_key}' ) ";

        // Date from
        $meta_key = 'wp_bannerize_banner_date_from';
        $pm = 'wp_bannerize_pm_' . $meta_key;
        // Remember to keep a space at begin and end of string
        $joins[] = " LEFT JOIN {$wpdb->postmeta} AS {$pm} ON ( {$pm}.post_id = {$wpdb->posts}.ID AND {$pm}.meta_key = '{$meta_key}' ) ";

        // Date expiry
        $meta_key = 'wp_bannerize_banner_date_expiry';
        $pm = 'wp_bannerize_pm_' . $meta_key;
        // Remember to keep a space at begin and end of string
        $joins[] = " LEFT JOIN {$wpdb->postmeta} AS {$pm} ON ( {$pm}.post_id = {$wpdb->posts}.ID AND {$pm}.meta_key = '{$meta_key}' ) ";

        $join .= implode(' ', $joins);

        return $join;
    }
}
