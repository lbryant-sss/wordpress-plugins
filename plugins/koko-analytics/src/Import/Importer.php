<?php

namespace KokoAnalytics\Import;

use Exception;
use KokoAnalytics\Path_Repository;
use KokoAnalytics\Referrer_Repository;

abstract class Importer
{
    abstract protected static function show_page_content();

    public static function show_page()
    {
        if (!current_user_can('manage_koko_analytics')) {
            return;
        }

        ?>
        <div class="wrap" style="max-width: 760px;">
            <?php if (isset($_GET['error'])) { ?>
                    <div class="notice notice-error is-dismissible">
                        <p>
                            <?php esc_html_e('An error occurred trying to import your statistics.', 'koko-analytics'); ?>
                            <?php echo ' '; ?>
                            <?php echo wp_kses(stripslashes(trim($_GET['error'])), [ 'br' => []]); ?>
                        </p>
                    </div>
            <?php } ?>
            <?php if (isset($_GET['success']) && $_GET['success'] == 1) { ?>
                    <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Big success! Your stats are now imported into Koko Analytics.', 'koko-analytics'); ?></p></div>
            <?php } ?>

            <?php static::show_page_content(); ?>
        </div>
        <?php
    }

    protected static function redirect_with_error(string $redirect_url, string $error_message): void
    {
        $redirect_url = add_query_arg([ 'error' => urlencode($error_message)], $redirect_url);
        wp_safe_redirect($redirect_url);
        exit;
    }


    /**
     * @param array $rows An array of arrays with the following elements: date, path, post_id, visitors, pageviews
     */
    protected static function bulk_insert_page_stats(array $rows): void
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        // return early if nothing to do
        if (count($rows) == 0) {
            return;
        }

        $path_ids = Path_Repository::upsert(array_map(function ($r) {
            return $r[1];
        }, $rows));

        $values = [];
        foreach ($rows as $r) {
            array_push($values, $r[0], $path_ids[$r[1]], $r[2], $r[3], $r[4]);
        }
        $placeholders = rtrim(str_repeat('(%s,%d,%d,%d,%d),', count($rows)), ',');

        $query = $wpdb->prepare("INSERT INTO {$wpdb->prefix}koko_analytics_post_stats(date, path_id, post_id, visitors, pageviews) VALUES {$placeholders} ON DUPLICATE KEY UPDATE visitors = visitors + VALUES(visitors), pageviews = pageviews + VALUES(pageviews)", $values);
        $wpdb->query($query);

        if ($wpdb->last_error !== '') {
            throw new Exception(__("A database error occurred: ", 'koko-analytics') . " {$wpdb->last_error}");
        }
    }

    /**
     * @param array $rows An array of arrays with the following elements: date, referrer, visitors, pageviews
     */
    protected static function bulk_insert_referrer_stats(array $rows): void
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        // return early if nothing to do
        if (count($rows) == 0) {
            return;
        }

        $ids = Referrer_Repository::upsert(array_map(function ($r) {
            return $r[1];
        }, $rows));

        $values = [];
        foreach ($rows as $r) {
            array_push($values, $r[0], $ids[$r[1]], $r[2], $r[3]);
        }
        $placeholders = rtrim(str_repeat('(%s,%d,%d,%d),', count($rows)), ',');

        $query = $wpdb->prepare("INSERT INTO {$wpdb->prefix}koko_analytics_referrer_stats(date, id, visitors, pageviews) VALUES {$placeholders} ON DUPLICATE KEY UPDATE visitors = visitors + VALUES(visitors), pageviews = pageviews + VALUES(pageviews)", $values);
        $wpdb->query($query);

        if ($wpdb->last_error !== '') {
            throw new Exception(__("A database error occurred: ", 'koko-analytics') . " {$wpdb->last_error}");
        }
    }
}
