<?php // uninstall remove options

if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) exit();

// delete options
delete_option('gap_options');
delete_option('ga-google-analytics-dismiss-notice');