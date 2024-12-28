<?php

namespace WPBannerize\Providers;

use WPBannerize\WPBones\Support\ServiceProvider;

class WPBannerizeServiceProvider extends ServiceProvider
{

    protected $import_page_url = '';
    protected $setting_page_url = '';

    public function register()
    {
        $this->import_page_url = admin_url('edit.php?post_type=wp_bannerize&page=wpbannerize_import', false);
        $this->setting_page_url = admin_url('edit.php?post_type=wp_bannerize&page=wpbannerize_settings', false);

        //plugin list
        add_action('plugin_action_links_' . WPBannerize()->pluginBasename, [$this, 'plugin_action_links'], 10, 4);

        // check for old wp bannerize table
        $result = get_option('wp_bannerize_do_import', false);

        if ($result) {
            add_action('admin_notices', [$this, 'admin_notices_import']);
        } else {

            // check for old wp bannerize table
            $result = get_option('wp_bannerize_old_table', false);

            if ($result) {
                add_action('admin_notices', [$this, 'admin_notices_table']);
            }
        }

        // Load all necessary admin bar items.
        add_action('admin_bar_menu', [$this, 'admin_bar_menu'], 100);

        WPBannerize()->css('wp-bannerize-admin-bar.css');
    }

    /**
     * Load all necessary admin bar items.
     *
     * This is the hook used to add, remove, or manipulate admin bar items.
     *
     * @since 3.1.0
     *
     * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance, passed by reference
     */
    public function admin_bar_menu($wp_admin_bar)
    {
        // administrator only
        if (current_user_can('activate_plugins')) {

            $icon  = '<span class="ab-icon"></span>';
            $title = '<span class="ab-label" aria-hidden="true">' . __('Bannerize', 'wp-bannerize') . '</span>';


            $parentID = 'wp-bannerize-pro-parent-admin-menu';
            $nonce     = 'wp-bannerize-pro';

            $adminMenu = [
              [
                'id' => $parentID,
                'title' => $icon . $title,
                'href' => '#',
                'meta' => [
                  'title' => __('Bannerize', 'wp-bannerize'),
                ],
              ],
            ];

            $adminMenu[] = [
              'parent' => $parentID,
              'id' => 'wp-bannerize-pro-admin-menu-add-banner',
              'title' => __('Add new Banner', 'wp-bannerize'),
              'href' => admin_url('post-new.php?post_type=wp_bannerize'),
              'meta' => [
                'title' => __('Add new Banner', 'wp-bannerize'),
              ],
            ];

            $adminMenu[] = [
              'parent' => $parentID,
              'id' => 'wp-bannerize-pro-admin-menu-banners',
              'title' => __('List', 'wp-bannerize'),
              'href' => admin_url('edit.php?post_type=wp_bannerize'),
              'meta' => [
                'title' => __('Banners List', 'wp-bannerize'),
              ],
            ];

            foreach ($adminMenu as $menu) {
                $wp_admin_bar->add_node($menu);
            }
        }
    }

    public function admin_notices_table()
    {


        $import_link = '<a class="button button-primary" href="' . $this->import_page_url . '">' . __('Click Here', 'wp-bannerize') . '</a>';

        ?>
    <div class="notice notice-info is-dismissible">
      <h2>WP Bannerize</h2>
      <p><?php esc_attr_e('You still got the previous version of WP Bannerize database table! Please', 'wp-bannerize'); ?> </p>
      <?php echo wp_kses_post($import_link); ?>
      <p> <?php esc_attr_e('to remove!', 'wp-bannerize'); ?></p>

    </div>
  <?php
    }

    public function admin_notices_import()
    {
        $import_link = '<a class="button button-primary" href="' . $this->import_page_url . '">' . __('Import', 'wp-bannerize') . '</a>';

        ?>
    <div class="notice notice-warning is-dismissible">
      <h2>WP Bannerize</h2>

      <p><?php esc_attr_e('You still have to complete the import of the previous version of database table! Please,', 'wp-bannerize'); ?></p>
      <?php echo wp_kses_post($import_link); ?>
      <p> <?php esc_attr_e('to complete the import!', 'wp-bannerize'); ?></p>

    </div>
<?php
    }

    public function plugin_action_links($links)
    {

        $settings_link = '<a href="' . $this->setting_page_url . '">' . __('Settings', 'wp-bannerize') . '</a>';

        array_unshift($links, $settings_link);

        if (get_option('wp_bannerize_do_import', false)) {
            $import_link = '<a href="' . $this->import_page_url . '">' . __('Import', 'wp-bannerize') . '</a>';
            array_unshift($links, $import_link);
        }

        return $links;
    }
}
