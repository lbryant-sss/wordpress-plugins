<?php

namespace WPBannerize\CustomPostTypes;

use WP_Query;
use WPBannerize\Models\WPBannerizePost;
use WPBannerize\PureCSSSwitch\PureCSSSwitchProvider;
use WPBannerize\PureCSSTabs\PureCSSTabsProvider;
use WPBannerize\WPBones\Foundation\WordPressCustomPostTypeServiceProvider;

class WPBannerizeCustomPostType extends WordPressCustomPostTypeServiceProvider
{
  protected $id = 'wp_bannerize';
  protected $name = 'Banner';
  protected $plural = 'Banners';
  protected $hierarchical = true;
  protected $public = true;
  protected $queryVar = 'wp_bannerize';
  protected $capabilityType = 'page';
  protected $mapMetaCap = true;
  protected $menuIcon = 'dashicons-images-alt';
  protected $showInRest = true;

  protected $supports = ['title', 'author', 'thumbnail', 'revisions', 'custom-fields'];

  protected $rewrite = [
    'slug' => 'banner',
    'with_front' => true,
    'pages' => true,
    'ep_mask' => EP_PERMALINK,
  ];

  protected $excludeFromSearch = false;

  protected $postMeta = [
    'wp_bannerize_banner_type',
    'wp_bannerize_banner_url',
    'wp_bannerize_banner_external_url',
    'wp_bannerize_banner_link',
    'wp_bannerize_banner_description',
    'wp_bannerize_banner_no_follow',
    'wp_bannerize_banner_target',
    'wp_bannerize_banner_width',
    'wp_bannerize_banner_height',
    'wp_bannerize_banner_mime_type',
    'wp_bannerize_banner_impressions_enabled',
    'wp_bannerize_banner_clicks_enabled',
    'wp_bannerize_banner_date_from',
    'wp_bannerize_banner_date_expiry',
    'wp_bannerize_banner_max_impressions',
    'wp_bannerize_banner_max_clicks',
    'wp_bannerize_preview_background_color',
  ];

  //    protected $capabilities = [
  //        'edit_post' => 'edit_banner',
  //        'read_post' => 'read_banner',
  //        'delete_post' => 'delete_banner',
  //        'edit_posts' => 'edit_banners',
  //        'edit_others_posts' => 'edit_others_banners',
  //        'publish_posts' => 'publish_banners',
  //        'read_private_posts' => 'read_private_banners',
  //        'create_posts' => 'edit_banners',
  //    ];

  /**
   * Used to cache the enqueue styles and scripts.
   *
   * @var string
   */
  private string $_version;

  /**
   * An instance of WPBannerizePost class.
   *
   * @var WPBannerizePost $_banner
   */
  private $_banner;

  /**
   * You may override this method in order to register your own actions and filters.
   *
   */
  public function boot()
  {
    $this->_version = WPBannerize()->Version;

    // You may override this method
    $this->registerMetaBoxCallback = [$this, 'register_meta_box_cb'];

    foreach ($this->postMeta as $meta) {
      register_post_meta('wp_bannerize', $meta, [
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
      ]);
    }

    $this->labels = [
      'name' => __('Banners', 'wp-bannerize'),
      'singular_name' => __('Banner', 'wp-bannerize'),
      'menu_name' => __('WP Bannerize', 'wp-bannerize'),
      'name_admin_bar' => __('Banner', 'wp-bannerize'),
      'add_new' => __('Add New', 'wp-bannerize'),
      'add_new_item' => __('Add New Banner', 'wp-bannerize'),
      'edit_item' => __('Edit Banner', 'wp-bannerize'),
      'new_item' => __('New Banner', 'wp-bannerize'),
      'view_item' => __('View Banner', 'wp-bannerize'),
      'search_items' => __('Search Banner', 'wp-bannerize'),
      'not_found' => __('No Banner found', 'wp-bannerize'),
      'not_found_in_trash' => __('No Banners found in trash', 'wp-bannerize'),
      'all_items' => __('Banners', 'wp-bannerize'),
      'archive_title' => __('Banner', 'wp-bannerize'),
      'parent_item_colon' => '',
    ];

    if (is_admin()) {
      // Fires after the title field.
      add_action('edit_form_after_title', [$this, 'edit_form_after_title']);

      // Filter the title field placeholder text.
      add_filter('enter_title_here', [$this, 'enter_title_here']);

      // help
      add_action('load-edit.php', [$this, 'load_edit_php']);
      add_action('load-post.php', [$this, 'load_post_php']);
      add_action('load-post-new.php', [$this, 'load_post_php']);

      // Post edit

      // Fires when styles are printed for a specific admin page based on $hook_suffix.
      add_action('admin_print_styles-post.php', [$this, 'admin_print_styles_post_php']);

      // Prints scripts and data queued for the footer.
      add_action('admin_print_footer_scripts-post.php', [$this, 'admin_print_footer_scripts_post_php']);

      // Post new

      // Fires when styles are printed for a specific admin page based on $hook_suffix.
      add_action('admin_print_styles-post-new.php', [$this, 'admin_print_styles_post_php']);

      // Prints scripts and data queued for the footer.
      add_action('admin_print_footer_scripts-post-new.php', [$this, 'admin_print_footer_scripts_post_php']);

      // Post List

      // Fires when styles are printed for a specific admin page based on $hook_suffix.
      add_action('admin_print_styles-edit.php', [$this, 'admin_print_styles_edit_php']);

      // Prints scripts and data queued for the footer.
      add_action('admin_print_footer_scripts-edit.php', [$this, 'admin_print_footer_scripts_edit_php']);

      // Filters the columns displayed in the Posts list table for a specific post type.
      add_filter("manage_{$this->id}_posts_columns", [$this, 'manage_posts_columns']);

      // Fires for each custom column of a specific post type in the Posts list table.
      add_action("manage_{$this->id}_posts_custom_column", [$this, 'manage_posts_custom_column']);

      // Fires immediately after a post is deleted from the database.
      add_action('deleted_post', [$this, 'deleted_post']);

      // Fires before the Filter button on the Posts and Pages list tables.
      add_action('restrict_manage_posts', [$this, 'restrict_manage_posts']);

      // Fires after the main query vars have been parsed.
      add_action('parse_request', function () {
        add_filter('parse_query', [$this, 'parse_query']);
      });

      add_filter("views_edit-{$this->id}", [$this, 'filterTimed']);
      add_filter("views_edit-{$this->id}", [$this, 'filterExpired']);
      add_filter("views_edit-{$this->id}", [$this, 'filterScheduled']);
    }
  }

  public function verifyNonce($post_id)
  {
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], "update-post_{$post_id}")) {
      wp_die(__('You are not allowed to do this', 'wp-bannerize'));
    }
  }

  public function filterTimed($views)
  {
    return $this->filter($views, 'timed', __('Timed', 'wp-bannerize'));
  }

  protected function filter($views, $type, $label)
  {
    $posts = WPBannerizePost::{$type}();

    $count = count($posts);

    $current = isset($_REQUEST[$type]) ? 'current' : '';

    if (!empty($count)) {
      $views[$type] = sprintf(
        '<a href="%s" class="%s">%s <span class="count">(%d)</span></a>',
        admin_url("edit.php?post_type={$this->id}&{$type}=1"),
        $current,
        $label,
        $count
      );
    }

    return $views;
  }

  public function filterExpired($views)
  {
    return $this->filter($views, 'expired', __('Expired', 'wp-bannerize'));
  }

  public function filterScheduled($views)
  {
    return $this->filter($views, 'scheduled', __('Scheduled', 'wp-bannerize'));
  }

  /**
   * Check if the given URL is a remote image.
   *
   * @param string $url The URL to check.
   * @return bool True if the URL is a remote image, false otherwise.
   */
  private function wp_bannerize_is_remote_image($url)
  {
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
      return false;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    curl_exec($ch);

    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($httpCode === 200 && in_array($contentType, ['image/jpeg', 'image/png', 'image/gif'])) {
      return true;
    }

    return false;
  }

  /**
   * Override this method to save/update your custom data.
   * This method is called by hook action save_post_{post_type}`
   *
   * @param int|string $post_id Post ID
   * @param object $post Optional. Post object
   *
   */
  public function update($post_id, $post)
  {
    // You can override this method to save your own data

    if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], "update-post_{$post_id}")) {
      $type = esc_attr($_POST['wp_bannerize_banner_type']);
      $url = esc_url($_POST['wp_bannerize_banner_url']);
      $urlExt = esc_url($_POST['wp_bannerize_banner_external_url']);
      $link = esc_url($_POST['wp_bannerize_banner_link']);
      $description = sanitize_title($_POST['wp_bannerize_banner_description']);
      $no_follow = wpbones_is_true($_POST['wp_bannerize_banner_no_follow']);
      $target = esc_attr($_POST['wp_bannerize_banner_target']);
      $urlMine = $type == 'local' ? $url : $urlExt;
      $size = $this->getBanner($post_id)->getSizeWithURL($urlMine);

      // SSRF fix
      if (!empty($urlExt)) {
        if (!$this->wp_bannerize_is_remote_image($urlExt)) {
          // Remove or do not save the invalid URL
          delete_post_meta($post_id, 'wp_bannerize_banner_external_url');
          // Show an error message to the user
          add_filter('redirect_post_location', function ($location) {
            return add_query_arg('banner_image_error', 1, $location);
          });
        }
      }

      if (isset($size) && is_array($size) && count($size) >= 2) {
        $width = !empty($size[0]) ? filter_var($size[0], FILTER_SANITIZE_NUMBER_INT) . 'px' : null;
        $height = !empty($size[1]) ? filter_var($size[1], FILTER_SANITIZE_NUMBER_INT) . 'px' : null;
      }

      $w = filter_var($_POST['wp_bannerize_banner_width'], FILTER_SANITIZE_NUMBER_INT);
      $h = filter_var($_POST['wp_bannerize_banner_height'], FILTER_SANITIZE_NUMBER_INT);

      $width = empty($w) ? $width : $w . 'px';
      $height = empty($h) ? $height : $h . 'px';

      $mime_type = $size['mime'];
      $impressions_enabled = wpbones_is_true($_POST['wp_bannerize_banner_impressions_enabled']);
      $clicks_enabled = wpbones_is_true($_POST['wp_bannerize_banner_clicks_enabled']);
      $date_from = strtotime($_POST['wp_bannerize_banner_date_from']);
      $date_expiry = strtotime($_POST['wp_bannerize_banner_date_expiry']);
      $background_color = sanitize_hex_color(isset($_POST['wp_bannerize_preview_background_color'])
        ? esc_attr($_POST['wp_bannerize_preview_background_color'])
        : '#ffffff');

      $max_impressions = isset($_POST['wp_bannerize_banner_max_impressions'])
        ? absint($_POST['wp_bannerize_banner_max_impressions'])
        : 0;

      $max_clicks = isset($_POST['wp_bannerize_banner_max_clicks'])
        ? absint($_POST['wp_bannerize_banner_max_clicks'])
        : 0;

      $meta = [
        'wp_bannerize_banner_type' => $type,
        'wp_bannerize_banner_url' => $url,
        'wp_bannerize_banner_external_url' => $urlExt,
        'wp_bannerize_banner_link' => $link,
        'wp_bannerize_banner_description' => $description,
        'wp_bannerize_banner_no_follow' => $no_follow,
        'wp_bannerize_banner_target' => $target,
        'wp_bannerize_banner_width' => $width,
        'wp_bannerize_banner_height' => $height,
        'wp_bannerize_banner_mime_type' => $mime_type,
        'wp_bannerize_banner_impressions_enabled' =>  $impressions_enabled,
        'wp_bannerize_banner_clicks_enabled' => $clicks_enabled,
        'wp_bannerize_banner_date_from' => $date_from,
        'wp_bannerize_banner_date_expiry' => $date_expiry,
        'wp_bannerize_preview_background_color' => $background_color,
        'wp_bannerize_banner_max_impressions' => $max_impressions,
        'wp_bannerize_banner_max_clicks' => $max_clicks,
      ];

      foreach ($meta as $key => $value) {
        update_post_meta($post_id, $key, $value);
      }
    }
  }

  protected function getBanner($post)
  {
    if (is_null($this->_banner)) {
      if (is_numeric($post)) {
        $post_id = $post;
      } elseif (is_object($post) && isset($post->ID)) {
        $post_id = $post->ID;
      } else {
        $post_id = null;
      }

      $this->_banner = WPBannerizePost::find($post_id);
    }

    return $this->_banner;
  }

  /*
    |--------------------------------------------------------------------------
    | Actions and filters
    |--------------------------------------------------------------------------
    |
    | Here is where you can insert your actions and filters.
    |
    */

  /**
   * This action is called when you can add the meta box
   */
  public function register_meta_box_cb()
  {
    global $post;

    // Init metabox

    add_meta_box(
      'wp_bannerize_preview',
      __('Preview', 'wp-bannerize'),
      [$this, 'metaBoxViewPreview'],
      $this->id,
      'normal',
      'high'
    );
  }

  public function load_edit_php()
  {
    if ($this->is()) {
      get_current_screen()->add_help_tab([
        'id' => 'wp_bannerize-overview',
        'title' => __('Overview'),
        'content' => WPBannerize()->view('help.banners-list'),
      ]);
    }
  }

  public function load_post_php()
  {
    if ($this->is()) {
      get_current_screen()->add_help_tab([
        'id' => 'wp_bannerize-overview',
        'title' => __('Overview'),
        'content' => WPBannerize()->view('help.banners-edit'),
      ]);
    }
  }

  /**
   * Fires after the title field.
   */
  public function edit_form_after_title()
  {
    global $post;

    // Only for this custom post
    if ($this->is()) {
      echo WPBannerize()
        ->view('cpt.edit')
        ->with('banner', $this->getBanner($post->ID));
    }
  }

  /**
   * Filter the title field placeholder text.
   *
   * @param string $text Placeholder text. Default 'Enter title here'.
   *
   * @return string
   */
  public function enter_title_here($text)
  {
    if (!$this->is()) {
      return $text;
    }

    $text = __('Enter Banner name', 'wp-bannerize');

    return $text;
  }

  // Fires when styles are printed for a specific admin page based on $hook_suffix.
  public function admin_print_styles_post_php()
  {
    if (!$this->is()) {
      return;
    }

    // Embed lightbox
    add_thickbox();

    // pure css tabs
    PureCSSTabsProvider::enqueueStyles();
    PureCSSSwitchProvider::enqueueStyles();

    // Override thickbox styles
    wp_enqueue_style('wp-bannerize-thickbox', WPBannerize()->css . '/wp-bannerize-thickbox.css', [], $this->_version);
    wp_enqueue_style(
      'wp-bannerize-admin-cpt',
      WPBannerize()->css . '/wp-bannerize-admin-cpt.css',
      ['wp-color-picker'],
      $this->_version
    );
  }

  // Prints scripts and data queued for the footer.
  public function admin_print_footer_scripts_post_php()
  {
    if (!$this->is()) {
      return;
    }

    WPBannerize()->js('wp-bannerize-admin-cpt.js', 'wp-color-picker');
  }

  // Fires when styles are printed for a specific admin page based on $hook_suffix.
  public function admin_print_styles_edit_php()
  {
    // Embed lightbox
    add_thickbox();

    // Override thickbox styles
    wp_enqueue_style('wp-bannerize-thickbox', WPBannerize()->css . '/wp-bannerize-thickbox.css', [], $this->_version);
    wp_enqueue_style(
      'wp-bannerize-admin-cpt',
      WPBannerize()->css . '/wp-bannerize-admin-cpt.css',
      ['wp-color-picker'],
      $this->_version
    );
  }

  // Prints scripts and data queued for the footer.
  public function admin_print_footer_scripts_edit_php()
  {
    if (!$this->is()) {
      return;
    }

    WPBannerize()->js('wp-bannerize-admin-cpt.js', [
      'wp-color-picker',
      'jquery-ui-core',
      'jquery-ui-sortable',
      'jquery-ui-draggable',
    ]);

    wp_localize_script('wp_bannerize_pro_slugwp-bannerize-admin-cptjs', 'wpBannerizePro', [
      'ajaxurl' => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce('wp-bannerize-pro'),
    ]);
  }

  /**
   * Filters the columns displayed in the Posts list table for a specific post type.
   *
   * The dynamic portion of the hook name, `$post_type`, refers to the post type slug.
   *
   * @param array $post_columns An array of column names.
   *
   * @return array
   */
  public function manage_posts_columns($post_columns)
  {
    $post_columns = wpbones_array_insert(
      $post_columns,
      'wp_bannerize_column_menu_order',
      '<i class="dashicons dashicons-editor-ol"></i>'
    );

    $post_columns = wpbones_array_insert(
      $post_columns,
      'wp_bannerize_column_thumbnail',
      __('Thumbnail', 'wp-bannerize'),
      2
    );

    $post_columns = wpbones_array_insert(
      $post_columns,
      'wp_bannerize_column_impressions',
      __('Impressions', 'wp-bannerize'),
      count($post_columns) - 2
    );

    $post_columns = wpbones_array_insert(
      $post_columns,
      'wp_bannerize_column_clicks',
      __('Clicks', 'wp-bannerize'),
      count($post_columns) - 2
    );

    $post_columns = wpbones_array_insert(
      $post_columns,
      'wp_bannerize_column_ctr',
      __('CTR', 'wp-bannerize'),
      count($post_columns) - 2
    );

    $post_columns = wpbones_array_insert(
      $post_columns,
      'wp_bannerize_column_date_from',
      __('Visible from', 'wp-bannerize'),
      count($post_columns) - 2
    );

    $post_columns = wpbones_array_insert(
      $post_columns,
      'wp_bannerize_column_date_expiry',
      __('Expires', 'wp-bannerize'),
      count($post_columns) - 2
    );

    return $post_columns;
  }

  /**
   * Fires for each custom column of a specific post type in the Posts list table.
   *
   * The dynamic portion of the hook name, `$post->post_type`, refers to the post type.
   *
   * @param string $column_name The name of the column to display.
   */
  public function manage_posts_custom_column($column_name)
  {
    global $post;

    $banner = WPBannerizePost::find($post->ID);

    switch ($column_name) {
      // Thumbnail
      case 'wp_bannerize_column_thumbnail':
        echo $banner->thumbnail();

        break;

      // order
      case 'wp_bannerize_column_menu_order':
        printf('<i data-order="%s" class="dashicons dashicons-move"/>', esc_attr($post->menu_order));
        break;

      // impressions
      case 'wp_bannerize_column_impressions':
        echo esc_attr($banner->banner_impressions);
        break;

      // clicks
      case 'wp_bannerize_column_clicks':
        echo esc_attr($banner->banner_clicks);
        break;

      // ctr
      case 'wp_bannerize_column_ctr':
        $impressions = intval($banner->banner_impressions);
        $clicks = intval($banner->banner_clicks);

        if (!empty($impressions)) {
          echo number_format(($clicks / $impressions) * 100, 2) . ' %';
        }

        break;

      // date from
      case 'wp_bannerize_column_date_from':
        $this->dateFrom($banner->banner_date_from);
        break;

      // date expiry
      case 'wp_bannerize_column_date_expiry':
        $this->dateExpiry($banner->banner_date_expiry);
        echo esc_attr($banner->banner_max_impressions) > 0 ? ($banner->banner_date_expiry ? ' or ' : '') . 'Impressions > ' . esc_attr($banner->banner_max_impressions) : '';
        echo esc_attr($banner->banner_max_clicks) > 0 ? ($banner->banner_date_expiry ? ' or ' : '') . 'Clicks > ' . esc_attr($banner->banner_max_clicks) : '';

        break;

      default:
        echo 'todo';
        break;
    }
  }

  protected function dateFrom($date)
  {
    if ($date) {
      $diff = human_time_diff($date);
      $diff_str = __('in %s', 'wp-bannerize');
      echo $date <= time() ? $diff : sprintf($diff_str, $diff);
    }
  }

  protected function dateExpiry($date)
  {
    if ($date) {
      $diff = human_time_diff($date);
      $ago_str = __('%s ago', 'wp-bannerize');
      $in_str = __('in %s', 'wp-bannerize');
      echo $date <= time() ? sprintf($ago_str, $diff) : sprintf($in_str, $diff);
    }
  }

  /**
   * Fires immediately after a post is deleted from the database.
   *
   * Used to delete impressions and clicks when a post is permanently deleted. Remember that all post meta are auto
   * delete by WordPress.
   *
   * @param int $post_id Post ID.
   */
  public function deleted_post($post_id)
  {
    /**
     * @var wpdb $wpdb
     */
    global $wpdb;
    global $post_type;

    // Only for bannerize custom post
    if ($post_type == $this->id) {
      // TODO: remake
      // Delete impressions
      //      $sql = sprintf( 'DELETE FROM %s WHERE banner_id = %s', WPXBannerizeImpressions::init()->table->table_name, $post_id );
      //      $wpdb->query( $sql );
      //
      //      // Delete clicks
      //      $sql = sprintf( 'DELETE FROM %s WHERE banner_id = %s', WPXBannerizeClicks::init()->table->table_name, $post_id );
      //      $wpdb->query( $sql );
    }
  }

  /**
   * Fires before the Filter button on the Posts and Pages list tables.
   *
   * The Filter button allows sorting by date and/or category on the
   * Posts list table, and sorting by date on the Pages list table.
   *
   * @since WP 2.1.0
   */
  public function restrict_manage_posts()
  {
    global $typenow, $per_page;

    // Get the post type
    $cpt = get_post_type_object($typenow);

    // Enabled drag & drop menu order only for post type page
    if (!empty($cpt) && is_object($cpt) && post_type_supports($typenow, 'page-attributes')) {
      // Build info on pagination. Useful for sorter
      $paged = absint(esc_attr(isset($_REQUEST['paged']) ? $_REQUEST['paged'] : '1')); ?>
      <input rel="<?php echo esc_attr(
                    $typenow
                  ); ?>" type="hidden" name="wp-bannerize-per-page" id="wp-bannerize-per-page" value="<?php echo esc_attr(
                                                                                                        $per_page
                                                                                                      ); ?>" />
      <input type="hidden" name="wp-bannerize-paged" id="wp-bannerize-paged"
        value="<?php echo esc_attr($paged); ?>" />
<?php
    }

    /**
     * If you only want this to work for your specific post type, check for that $type here and then return.
     * This function, if unmodified, will add the dropdown for each post type / taxonomy combination.
     *
     * // Return the registered custom post types; exclude the builtin
     * $post_types = get_post_types( array( '_builtin' => false ) );
     *
     */

    if ($this->id == $typenow) {
      $filters = get_object_taxonomies($typenow);

      foreach ($filters as $tax_slug) {
        $tax_obj = get_taxonomy($tax_slug);

        $args = [
          'show_option_all' => __('Show All') . ' ' . $tax_obj->label,
          'taxonomy' => $tax_slug,
          'name' => $tax_obj->query_var,
          'orderby' => 'name',
          'selected' => esc_attr($_REQUEST[$tax_obj->query_var] ?? ''),
          'hierarchical' => $tax_obj->hierarchical,
          'show_count' => true,
          'hide_empty' => false,
          'hide_if_empty' => true,
          'value_field' => 'slug',
        ];
        wp_dropdown_categories($args);
      }
    }
  }

  /**
   * Fires after the main query vars have been parsed.
   *
   * @param WP_Query &$query The WP_Query instance (passed by reference).
   * @since WP 1.5.0
   *
   */
  public function parse_query($query)
  {
    global $pagenow, $typenow;

    if ('edit.php' == $pagenow && $this->id == $typenow) {
      if (isset($_REQUEST['expired'])) {
        $query->set('meta_query', WPBannerizePost::metaQuery('expired'));
      } elseif (isset($_REQUEST['scheduled'])) {
        $query->set('meta_query', WPBannerizePost::metaQuery('scheduled'));
      } elseif (isset($_REQUEST['timed'])) {
        $query->set('meta_query', WPBannerizePost::metaQuery('timed'));
      }
    }
  }

  public function metaBoxViewPreview()
  {
    global $post;

    echo WPBannerize()->view('cpt.preview')->with('banner', $this->getBanner($post));
  }
}
