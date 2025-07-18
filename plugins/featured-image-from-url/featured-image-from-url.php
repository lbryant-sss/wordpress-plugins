<?php

/*
 * Plugin Name: Featured Image from URL (FIFU)
 * Plugin URI: https://fifu.app/
 * Description: Use a remote image or video as featured image of a post or WooCommerce product.
 * Version: 5.2.1
 * Author: fifu.app
 * Author URI: https://fifu.app/
 * WC requires at least: 4.0
 * WC tested up to: 10.0.2
 * Text Domain: featured-image-from-url
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

define('FIFU_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FIFU_INCLUDES_DIR', FIFU_PLUGIN_DIR . 'includes');
define('FIFU_ADMIN_DIR', FIFU_PLUGIN_DIR . 'admin');
define('FIFU_ELEMENTOR_DIR', FIFU_PLUGIN_DIR . 'elementor');
define('FIFU_GRAVITY_DIR', FIFU_PLUGIN_DIR . 'gravity-forms');
define('FIFU_LANGUAGES_DIR', WP_CONTENT_DIR . '/uploads/fifu/languages/');
define('FIFU_DELETE_ALL_URLS', false);
define('FIFU_CLOUD_DEBUG', false);

$FIFU_SESSION = array();

// Required includes with error handling
$required_includes = [
    FIFU_INCLUDES_DIR . '/attachment.php',
    FIFU_INCLUDES_DIR . '/convert-url.php',
    FIFU_INCLUDES_DIR . '/external-post.php',
    FIFU_INCLUDES_DIR . '/jetpack.php',
    FIFU_INCLUDES_DIR . '/speedup.php',
    FIFU_INCLUDES_DIR . '/thumbnail.php',
    FIFU_INCLUDES_DIR . '/thumbnail-category.php',
    FIFU_INCLUDES_DIR . '/util.php',
    FIFU_INCLUDES_DIR . '/woo.php'
];

foreach ($required_includes as $file) {
    if (file_exists($file)) {
        require_once $file;
    }
}

$required_admin = [
    FIFU_ADMIN_DIR . '/api.php',
    FIFU_ADMIN_DIR . '/category.php',
    FIFU_ADMIN_DIR . '/column.php',
    FIFU_ADMIN_DIR . '/cron.php',
    FIFU_ADMIN_DIR . '/db.php',
    FIFU_ADMIN_DIR . '/debug.php',
    FIFU_ADMIN_DIR . '/dimensions.php',
    FIFU_ADMIN_DIR . '/languages.php',
    FIFU_ADMIN_DIR . '/log.php',
    FIFU_ADMIN_DIR . '/menu.php',
    FIFU_ADMIN_DIR . '/meta-box.php',
    FIFU_ADMIN_DIR . '/rsa.php',
    FIFU_ADMIN_DIR . '/strings.php',
    FIFU_ADMIN_DIR . '/sheet-editor.php',
    FIFU_ADMIN_DIR . '/transient.php',
    FIFU_ADMIN_DIR . '/widgets.php',
];

foreach ($required_admin as $file) {
    if (file_exists($file)) {
        require_once $file;
    }
}

if (file_exists(FIFU_ELEMENTOR_DIR . '/elementor-fifu-extension.php')) {
    require_once (FIFU_ELEMENTOR_DIR . '/elementor-fifu-extension.php');
}

if (function_exists('fifu_is_gravity_forms_active') && fifu_is_gravity_forms_active()) {
    $gravity_forms_file = WP_PLUGIN_DIR . '/gravityforms/gravityforms.php';
    if (file_exists($gravity_forms_file)) {
        require_once $gravity_forms_file;
    }
    if (class_exists('GFForms') && file_exists(FIFU_GRAVITY_DIR . '/fifufieldaddon.php')) {
        require_once (FIFU_GRAVITY_DIR . '/fifufieldaddon.php');
    }
}

if (defined('WP_CLI') && WP_CLI && file_exists(FIFU_ADMIN_DIR . '/cli-commands.php'))
    require_once (FIFU_ADMIN_DIR . '/cli-commands.php');

register_activation_hook(__FILE__, 'fifu_activate');

function fifu_activate($network_wide) {
    if (is_multisite() && $network_wide) {
        global $wpdb;
        foreach ($wpdb->get_col("SELECT blog_id FROM $wpdb->blogs") as $blog_id) {
            switch_to_blog($blog_id);
            fifu_activate_actions();
            fifu_set_author();
            restore_current_blog();
        }
        // Execute network-wide operations on main site
        switch_to_blog(get_main_site_id());
        restore_current_blog();
    } else {
        fifu_activate_actions();
        fifu_set_author();
    }
}

function fifu_activate_actions() {
    fifu_db_create_table_invalid_media_su();
    fifu_db_maybe_create_table_meta_in();
    fifu_db_maybe_create_table_meta_out();
}

register_deactivation_hook(__FILE__, 'fifu_deactivation');

function fifu_deactivation() {
    wp_clear_scheduled_hook('fifu_create_cloud_upload_auto_event');
    wp_clear_scheduled_hook('fifu_create_cloud_delete_auto_event');
}

add_action('upgrader_process_complete', 'fifu_upgrade', 10, 2);

function fifu_upgrade($upgrader_object, $options) {
    $current_plugin_path_name = plugin_basename(__FILE__);
    if (($options['action'] ?? '') == 'update' && ($options['type'] ?? '') == 'plugin') {
        if (isset($options['plugins'])) {
            foreach ((array) $options['plugins'] as $each_plugin) {
                if ($each_plugin == $current_plugin_path_name) {
                    if (is_multisite()) {
                        global $wpdb;
                        foreach ($wpdb->get_col("SELECT blog_id FROM $wpdb->blogs") as $blog_id) {
                            switch_to_blog($blog_id);
                            fifu_upgrade_actions();
                            restore_current_blog();
                        }
                    } else {
                        fifu_upgrade_actions();
                    }
                }
            }
        }
    }
}

function fifu_upgrade_actions() {
    fifu_db_create_table_invalid_media_su();
    fifu_db_maybe_create_table_meta_in();
    fifu_db_maybe_create_table_meta_out();
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'fifu_action_links');
add_filter('network_admin_plugin_action_links_' . plugin_basename(__FILE__), 'fifu_action_links');

function fifu_action_links($links) {
    $strings = fifu_get_strings_plugins();
    $links[] = '<a href="' . esc_url(get_admin_url(null, 'admin.php?page=featured-image-from-url')) . '">' . $strings['settings']() . '</a>';
    return $links;
}

add_filter('plugin_row_meta', 'fifu_row_meta', 10, 4);

function fifu_row_meta($plugin_meta, $plugin_file, $plugin_data, $status) {
    if (strpos($plugin_file, 'featured-image-from-url.php') !== false) {
        $email = '<a style="color:#2271b1">support@fifu.app</a>';
        $new_links = array(
            'email' => $email,
        );
        $plugin_meta = array_merge($plugin_meta, $new_links);
    }
    return $plugin_meta;
}

function fifu_uninstall() {
    global $pagenow;
    if ($pagenow !== 'plugins.php')
        return;

    $strings = fifu_get_strings_uninstall();

    wp_enqueue_script('jquery-block-ui', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js');
    wp_enqueue_style('fancy-box-css', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.css');
    wp_enqueue_script('fancy-box-js', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js');
    wp_enqueue_style('fifu-uninstall-css', plugins_url('includes/html/css/uninstall.css', __FILE__), array(), fifu_version_number_enq());
    wp_enqueue_script('fifu-uninstall-js', plugins_url('includes/html/js/uninstall.js', __FILE__), array('jquery'), fifu_version_number_enq());
    wp_localize_script('fifu-uninstall-js', 'fifuUninstallVars', [
        'restUrl' => esc_url_raw(rest_url()),
        'nonce' => wp_create_nonce('wp_rest'),
        'buttonTextClean' => $strings['button']['text']['clean'](),
        'buttonTextDeactivate' => $strings['button']['text']['deactivate'](),
        'buttonDescriptionClean' => $strings['button']['description']['clean'](),
        'buttonDescriptionDeactivate' => $strings['button']['description']['deactivate'](),
        'textWhy' => $strings['text']['why'](),
        'textEmail' => $strings['text']['email'](),
        'textReasonConflict' => $strings['text']['reason']['conflict'](),
        'textReasonPro' => $strings['text']['reason']['pro'](),
        'textReasonSeo' => $strings['text']['reason']['seo'](),
        'textReasonLocal' => $strings['text']['reason']['local'](),
        'textReasonUndestand' => $strings['text']['reason']['undestand'](),
        'textReasonOthers' => $strings['text']['reason']['others'](),
    ]);
}

add_action('admin_footer', 'fifu_uninstall');

// https://developer.woocommerce.com/docs/hpos-extension-recipe-book/
add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});
