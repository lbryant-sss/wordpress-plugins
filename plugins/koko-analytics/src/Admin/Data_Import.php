<?php

/**
 * @package koko-analytics
 * @license GPL-3.0+
 * @author Danny van Kooten
 */

namespace KokoAnalytics\Admin;

use Exception;

class Data_Import
{
    public static function action_listener(): void
    {
        if (!current_user_can('manage_koko_analytics')) {
            return;
        }

        check_admin_referer('koko_analytics_import_data');
        $settings_page = admin_url('/index.php?page=koko-analytics&tab=settings');

        if (empty($_FILES['import-file']) || $_FILES['import-file']['error'] !== UPLOAD_ERR_OK) {
            wp_safe_redirect(add_query_arg(['notice' => ['type' => 'warning', 'message' => __('Something went wrong trying to process your import file.', 'koko-analytics') ]], $settings_page));
            exit;
        }

        // don't accept MySQL blobs over 16 MB
        if ($_FILES['import-file']['size'] > 16000000) {
            wp_safe_redirect(add_query_arg(['notice' => ['type' => 'warning', 'message' => __('Sorry, your import file is too large. Please import it into your database in some other way.', 'koko-analytics') ]], $settings_page));
            exit;
        }

        // try to increase time limit
        @set_time_limit(300);

        // read SQL from upload file
        $sql = file_get_contents($_FILES['import-file']['tmp_name']);

        // verify file looks like a Koko Analytics export file
        if (!preg_match('/^(--|DELETE|SELECT|INSERT|TRUNCATE|CREATE|DROP)/', $sql)) {
            wp_safe_redirect(add_query_arg(['notice' => ['type' => 'warning', 'message' => __('Sorry, the uploaded import file does not look like a Koko Analytics export file', 'koko-analytics') ]], $settings_page));
            exit;
        }

        // good to go, let's run the SQL
        try {
            self::run($sql);
        } catch (\Exception $e) {
            wp_safe_redirect(add_query_arg(['notice' => ['type' => 'warning', 'title' => __('Something went wrong trying to process your import file.', 'koko-analytics'), 'message' => $e->getMessage() ]], $settings_page));
            exit;
        }

        // unlink tmp file
        unlink($_FILES['import-file']['tmp_name']);

        // redirect with success message
        wp_safe_redirect(add_query_arg(['notice' => ['type' => 'success', 'message' => __('Database was successfully imported from the given file', 'koko-analytics') ]], $settings_page));
        exit;
    }

    protected static function run(string $sql): void
    {
        if ($sql === '') {
            return;
        }

        /** @var \wpdb $wpdb */
        global $wpdb;
        $statements = explode(';', $sql);
        foreach ($statements as $statement) {
            // skip over empty statements
            $statement = trim($statement);
            if (!$statement) {
                continue;
            }

            $result = $wpdb->query($statement);

            if ($result === false) {
                throw new Exception($wpdb->last_error);
            }
        }
    }
}
