<?php

namespace WP_Rplg_Google_Reviews\Includes\Core;

class Database {

    const BUSINESS_TABLE = 'grp_google_place';

    const REVIEW_TABLE = 'grp_google_review';

    const STATS_TABLE = 'grp_google_stats';

    public function create() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        if (!function_exists('dbDelta')) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        }

        $sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . self::BUSINESS_TABLE . " (".
               "id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,".
               "place_id VARCHAR(80) NOT NULL,".
               "name VARCHAR(255) NOT NULL,".
               "photo VARCHAR(255),".
               "icon VARCHAR(255),".
               "address VARCHAR(255),".
               "rating DOUBLE PRECISION,".
               "url VARCHAR(255),".
               "map_url VARCHAR(512),".
               "website VARCHAR(255),".
               "review_count INTEGER,".
               "updated BIGINT(20),".
               "PRIMARY KEY (`id`),".
               "UNIQUE INDEX grp_place_id (`place_id`)".
               ") " . $charset_collate . ";";

        $this->execsql($sql);

        $sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . self::REVIEW_TABLE . " (".
               "id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,".
               "google_place_id BIGINT(20) UNSIGNED NOT NULL,".
               "rating INTEGER NOT NULL,".
               "text VARCHAR(10000),".
               "time INTEGER NOT NULL,".
               "language VARCHAR(10),".
               "author_name VARCHAR(255),".
               "author_url VARCHAR(127),".
               "profile_photo_url VARCHAR(255),".
               "images TEXT,".
               "reply TEXT,".
               "reply_time INTEGER,".
               "hide VARCHAR(1) DEFAULT '' NOT NULL,".
               "PRIMARY KEY (`id`),".
               "UNIQUE INDEX grp_author_url_lang (`google_place_id`, `author_url`, `language`),".
               "INDEX grp_google_place_id (`google_place_id`)".
               ") " . $charset_collate . ";";

        $this->execsql($sql);

        $sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . self::STATS_TABLE . " (".
               "id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,".
               "google_place_id BIGINT(20) UNSIGNED NOT NULL,".
               "time INTEGER NOT NULL,".
               "rating DOUBLE PRECISION,".
               "review_count INTEGER,".
               "PRIMARY KEY (`id`),".
               "INDEX grp_google_place_id (`google_place_id`)".
               ") " . $charset_collate . ";";

        $this->execsql($sql);
    }

    private function execsql($sql) {
        global $wpdb;

        dbDelta($sql);
        $last_error = $wpdb->last_error;
        if (isset($last_error) && strlen($last_error) > 0) {
            $now = floor(microtime(true) * 1000);
            update_option('grw_last_error', $now . ': ' . $last_error);
        }
    }

    public function drop() {
        global $wpdb;

        $wpdb->query("DROP TABLE " . $wpdb->prefix . self::BUSINESS_TABLE . ";");
        $wpdb->query("DROP TABLE " . $wpdb->prefix . self::REVIEW_TABLE . ";");
        $wpdb->query("DROP TABLE " . $wpdb->prefix . self::STATS_TABLE . ";");
    }

}
