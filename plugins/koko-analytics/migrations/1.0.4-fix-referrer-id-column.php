<?php

defined('ABSPATH') or exit;

/** @var \wpdb $wpdb */
global $wpdb;

$wpdb->query("ALTER TABLE {$wpdb->prefix}koko_analytics_referrer_urls MODIFY id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT");
