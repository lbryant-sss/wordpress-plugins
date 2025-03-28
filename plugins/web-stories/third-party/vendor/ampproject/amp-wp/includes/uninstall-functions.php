<?php

/**
 * Helper function for removing plugin data during uninstalling.
 *
 * @package AMP
 */
namespace Google\Web_Stories_Dependencies\AmpProject\AmpWP;

/**
 * Delete data from option table.
 *
 * @return void
 * @internal
 */
function delete_options()
{
    $options = \get_option('amp-options');
    \delete_option('amp-options');
    \delete_option('amp_css_transient_monitor_time_series');
    \delete_option('amp_url_validation_queue');
    // See Validation\URLValidationCron::OPTION_KEY.
    $theme_mod_name = 'amp_customize_setting_modified_timestamps';
    \remove_theme_mod($theme_mod_name);
    if (!empty($options['reader_theme']) && 'legacy' !== $options['reader_theme']) {
        $reader_theme_mods_option_name = \sprintf('theme_mods_%s', $options['reader_theme']);
        $reader_theme_mods = \get_option($reader_theme_mods_option_name);
        if (\is_array($reader_theme_mods) && isset($reader_theme_mods[$theme_mod_name])) {
            unset($reader_theme_mods[$theme_mod_name]);
            \update_option($reader_theme_mods_option_name, $reader_theme_mods);
        }
    }
}
/**
 * Delete AMP user meta.
 *
 * @return void
 * @internal
 */
function delete_user_metadata()
{
    $keys = ['amp_dev_tools_enabled', 'amp_review_panel_dismissed_for_template_mode'];
    foreach ($keys as $key) {
        \delete_metadata('user', 0, $key, '', \true);
    }
}
/**
 * Delete AMP Validated URL posts.
 *
 * @return void
 * @internal
 */
function delete_posts()
{
    /** @var \wpdb */
    global $wpdb;
    $post_type = 'amp_validated_url';
    // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    // Delete all post meta data related to "amp_validated_url" post_type.
    $wpdb->query($wpdb->prepare("\n\t\t\tDELETE meta\n\t\t\tFROM {$wpdb->postmeta} AS meta\n\t\t\t\tINNER JOIN {$wpdb->posts} AS posts\n\t\t\t\t\tON posts.ID = meta.post_id\n\t\t\tWHERE posts.post_type = %s;\n\t\t\t", $post_type));
    // Delete all amp_validated_url posts.
    $wpdb->delete($wpdb->posts, \compact('post_type'));
    // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
}
/**
 * Delete AMP validation error terms.
 *
 * @return void
 * @internal
 */
function delete_terms()
{
    /** @var \wpdb */
    global $wpdb;
    $taxonomy = 'amp_validation_error';
    // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    // Delete term meta (added in WP 4.4).
    if (!empty($wpdb->termmeta)) {
        $wpdb->query($wpdb->prepare("\n\t\t\t\tDELETE tm\n\t\t\t\tFROM {$wpdb->termmeta} AS tm\n\t\t\t\t\tINNER JOIN {$wpdb->term_taxonomy} AS tt\n\t\t\t\t\t\tON tm.term_id = tt.term_id\n\t\t\t\tWHERE tt.taxonomy = %s;\n\t\t\t\t", $taxonomy));
    }
    // Delete term relationship.
    $wpdb->query($wpdb->prepare("\n\t\t\tDELETE tr\n\t\t\tFROM {$wpdb->term_relationships} AS tr\n\t\t\t\tINNER JOIN {$wpdb->term_taxonomy} AS tt\n\t\t\t\t\tON tr.term_taxonomy_id = tt.term_taxonomy_id\n\t\t\tWHERE tt.taxonomy = %s;\n\t\t\t", $taxonomy));
    // Delete terms.
    $wpdb->query($wpdb->prepare("\n\t\t\tDELETE terms\n\t\t\tFROM {$wpdb->terms} AS terms\n\t\t\t\tINNER JOIN {$wpdb->term_taxonomy} AS tt\n\t\t\t\t\tON terms.term_id = tt.term_id\n\t\t\tWHERE tt.taxonomy = %s;\n\t\t\t", $taxonomy));
    // Delete term taxonomy.
    $wpdb->delete($wpdb->term_taxonomy, \compact('taxonomy'));
    // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
}
/**
 * Delete transient data from option table if object cache is not available.
 *
 * @return void
 * @internal
 */
function delete_transients()
{
    // Transients are not stored in the options table if an external object cache is used,
    // in which case they cannot be queried for deletion.
    if (\wp_using_ext_object_cache()) {
        return;
    }
    /** @var \wpdb */
    global $wpdb;
    $transient_groups = ['Google\\Web_Stories_Dependencies\\AmpProject\\AmpWP\\DevTools\\BlockSourcesamp_block_sources', 'amp-parsed-stylesheet-v%', 'amp_error_index_counts', 'amp_img_%', 'amp_lock_%', 'amp_new_validation_error_urls_count', 'amp_plugin_activation_validation_errors', 'amp_remote_request_%', 'amp_themes_wporg'];
    $where_clause = [];
    foreach ($transient_groups as $transient_group) {
        if (\false !== \strpos($transient_group, '%')) {
            $where_clause[] = $wpdb->prepare(' option_name LIKE %s OR option_name LIKE %s ', "_transient_{$transient_group}", "_transient_timeout_{$transient_group}");
        } else {
            $where_clause[] = $wpdb->prepare(' option_name = %s OR option_name = %s ', "_transient_{$transient_group}", "_transient_timeout_{$transient_group}");
        }
    }
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Cannot cache result since we're deleting the records.
    $wpdb->query(
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- See use of prepare in foreach loop above.
        "DELETE FROM {$wpdb->options} WHERE " . \implode(' OR ', $where_clause)
    );
}
/**
 * Remove plugin data.
 *
 * @return void
 * @internal
 */
function remove_plugin_data()
{
    $options = \get_option('amp-options');
    if (\is_array($options) && \array_key_exists('delete_data_at_uninstall', $options) ? $options['delete_data_at_uninstall'] : \true) {
        \delete_options();
        \delete_user_metadata();
        \delete_posts();
        \delete_terms();
        \delete_transients();
        // Clear any cached data that has been removed.
        \wp_cache_flush();
    }
}
