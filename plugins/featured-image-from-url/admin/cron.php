<?php

function fifu_add_cron_schedules($schedules) {
    if (!isset($schedules["fifu_schedule_cloud_upload_auto"])) {
        $schedules['fifu_schedule_cloud_upload_auto'] = array(
            'interval' => 5 * 60,
            'display' => 'fifu-cloud-upload-auto'
        );
    }
    if (!isset($schedules["fifu_schedule_cloud_delete_auto"])) {
        $schedules['fifu_schedule_cloud_delete_auto'] = array(
            'interval' => 24 * 60 * 60,
            'display' => 'fifu-cloud-delete-auto'
        );
    }
    return $schedules;
}

add_filter('cron_schedules', 'fifu_add_cron_schedules');

function fifu_create_cloud_upload_auto_hook() {
    if (fifu_active_job('fifu_cloud_upload_auto_semaphore', 5))
        return;

    $urls = fifu_db_get_all_urls(0, null, null);

    // Limit the number of URLs to 100
    $urls = array_slice($urls, 0, 100);

    fifu_create_thumbnails_list($urls, true);

    fifu_delete_transient('fifu_cloud_upload_auto_semaphore');
}

add_action('fifu_create_cloud_upload_auto_event', 'fifu_create_cloud_upload_auto_hook');

function fifu_create_cloud_delete_auto_hook() {
    if (fifu_active_job('fifu_cloud_delete_auto_semaphore', 5))
        return;

    $hex_ids = fifu_db_get_all_hex_ids();

    fifu_delete_thumbnails($hex_ids);

    fifu_delete_transient('fifu_cloud_delete_auto_semaphore');
}

add_action('fifu_create_cloud_delete_auto_event', 'fifu_create_cloud_delete_auto_hook');

function fifu_active_job($semaphore, $minutes) {
    $date = fifu_get_transient($semaphore);
    if (!$date)
        return false;

    if (gettype($date) != 'object') {
        fifu_set_transient($semaphore, new DateTime(), 0);
        return true;
    }

    return date_diff(new DateTime(), $date)->format('%i') < $minutes;
}

function fifu_stop_job($option_name) {
    $field = $option_name . '_stop';
    update_option($field, true, 'no');
}

function fifu_should_stop_job($option_name) {
    $field = $option_name . '_stop';

    global $wpdb;
    if ($wpdb->get_col("SELECT 1 FROM " . $wpdb->options . " WHERE option_name = '" . $field . "'")) {
        delete_option($field);
        return true;
    }
    return false;
}

function fifu_run_cron_now() {
    wp_remote_request(site_url('wp-cron.php'));
}

