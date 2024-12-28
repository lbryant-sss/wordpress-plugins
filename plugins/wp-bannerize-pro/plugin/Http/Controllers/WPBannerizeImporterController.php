<?php

namespace WPBannerize\Http\Controllers;

use WPBannerize\PureCSSTabs\PureCSSTabsProvider;
use WPBannerize\Models\WPBannerizePost;

class WPBannerizeImporterController extends Controller
{
  protected $tableName;
  protected $totalRecords;
  protected $totalEnabled;
  protected $totalDisabled;
  protected $totalTrash;
  protected $groups;

  public function load()
  {
    PureCSSTabsProvider::enqueueStyles();

    if ($this->request->isVerb('post') && $this->request->verifyNonce('wp_bannerize_importer')) {
      // button actions
      $import = $this->request->get('wp_bannerize_import');

      // data
      $groups = $this->request->get('wp_bannerize_importer_groups');
      $types = $this->request->get('wp_bannerize_importer_types');
      $trash = $this->request->get('wp_bannerize_importer_trash');
      $disabled = $this->request->get('wp_bannerize_importer_disabled');

      $this->getTableInformation();

      if ($import) {
        $this->convert($groups, $types, $trash, $disabled);

        if ($this->request->get('wp_bannerize_importer_drop_table')) {
          $this->dropTable();
        }
      }

      $destroy = $this->request->get('wp_bannerize_destroy_previous_table');

      if ($destroy) {
        $this->dropTable();
      }
    }
  }

  public function index()
  {
    $this->getTableInformation();

    PureCSSTabsProvider::enqueueStyles();

    return WPBannerize()->view('importer.index')->withAdminStyles('wp-bannerize-common')->with('importer', $this);
  }

  public function store()
  {
    // button actions
    $import = $this->request->get('wp_bannerize_import');

    $this->getTableInformation();

    if ($import) {
      return WPBannerize()
        ->view('importer.successfully')
        ->withAdminStyles('wp-bannerize-common')
        ->with('importer', $this);
    }

    $destroy = $this->request->get('wp_bannerize_destroy_previous_table');

    if ($destroy) {
      return WPBannerize()->view('importer.dropped')->withAdminStyles('wp-bannerize-common');
    }

    return WPBannerize()->view('importer.index')->withAdminStyles('wp-bannerize-common')->with('importer', $this);
  }

  public function update()
  {
    // PUT AND PATCH
  }

  public function destroy()
  {
    // DELETE
  }

  protected function getTotalrecordsAttribute()
  {
    return $this->totalRecords;
  }

  protected function getTablenameAttribute()
  {
    return $this->tableName;
  }

  protected function getGroupsAttribute()
  {
    return $this->groups;
  }

  protected function getTotalenabledAttribute()
  {
    return $this->totalEnabled;
  }

  protected function getTotaldisabledAttribute()
  {
    return $this->totalDisabled;
  }

  protected function getTotaltrashAttribute()
  {
    return $this->totalTrash;
  }

  protected function getTableInformation()
  {
    /**
     * @var wpdb $wpdb
     */
    global $wpdb;

    $this->tableName = "{$wpdb->prefix}bannerize";

    // Get total of records
    $this->totalRecords = $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM %i', $this->tableName));

    // Get total of enabled records
    $this->totalEnabled = $wpdb->get_var(
      $wpdb->prepare("SELECT COUNT(*) FROM %i WHERE enabled = '1'", $this->tableName)
    );

    // Get total of disabled records
    $this->totalDisabled = $wpdb->get_var(
      $wpdb->prepare("SELECT COUNT(*) FROM %i WHERE enabled = '0'", $this->tableName)
    );

    // Get total of trash records
    $this->totalTrash = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i WHERE trash = '1'", $this->tableName));

    // Gets groups array
    $groups = $wpdb->get_results(
      $wpdb->prepare('SELECT p.group FROM %i AS p GROUP BY p.group ORDER BY p.group', $this->tableName),
      OBJECT_K
    );

    foreach ($groups as $key => $group) {
      $this->groups[$key] = $group->group;
    }
  }

  protected function dropTable()
  {
    /**
     * @var wpdb $wpdb
     */
    global $wpdb;

    $result = $wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %i', $this->tableName));

    delete_option('wp_bannerize_old_table');
    delete_option('wp_bannerize_do_import');

    return $result;
  }

  /**
   * Importer
   *
   * @param array $groups   List of groups to import
   * @param array $types    List of types to import
   * @param bool  $trash    Optional. TRUE to import trash banner
   * @param bool  $disabled Optional. TRUE to import disabled banner
   *
   * @return int|\WP_Error
   */
  protected function convert($groups = [], $types = [], $trash = false, $disabled = false)
  {
    /**
     * @var wpdb $wpdb
     */
    global $wpdb;

    $this->tableName = "{$wpdb->prefix}bannerize";

    $where = [
      'enabled' => "t.enabled = '1'",
      'trash' => "t.trash = '0'",
    ];

    if ($trash) {
      unset($where['trash']);
    }

    if ($disabled) {
      unset($where['enabled']);
    }

    if (!empty($groups)) {
      $groups = array_map('wp_strip_all_tags', $groups);
      $groups = array_map('trim', $groups);
      $where[] = 't.group IN (\'' . implode('\', \'', $groups) . '\')';
    }

    if (!empty($types)) {
      $types = array_map('wp_strip_all_tags', $types);
      $types = array_map('trim', $types);
      $where[] = 't.banner_type IN (\'' . implode('\', \'', $types) . '\')';
    }

    $whereStr = implode(' AND ', $where);

    $results = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT t.*,
  IF( ( t.start_date = '0000-00-00 00:00:00' ), '', UNIX_TIMESTAMP( t.start_date ) ) AS date_from,
  IF( ( t.end_date = '0000-00-00 00:00:00' ), '', UNIX_TIMESTAMP( t.end_date ) ) AS date_expiry FROM %i AS t WHERE 1 AND %1s",
        $this->tableName,
        $whereStr
      )
    );

    $counts = 0;

    if ($results) {
      foreach ($results as $row) {
        $post_id = $this->insertBanner($row);
        if (is_wp_error($post_id)) {
          return $post_id;
        }
        $counts++;
      }
    }

    delete_option('wp_bannerize_do_import');

    return $counts;
  }

  /**
   * Insert a new WP Bannerize Custom Post Type from old WP Bannerize row record,
   * Return the post ID or WP_Error
   *
   * @param object $row WP Bannerize row record
   *
   * @return int|WP_Error
   */
  protected function insertBanner($row)
  {
    // Get the current user ID
    $user_id = get_current_user_id();

    // Status
    $status = 'publish';
    if (!empty($row->trash)) {
      $status = 'trash';
    }
    if (empty($row->enabled)) {
      $status = 'draft';
    }

    // Group category banner
    $term = term_exists($row->group, 'wp_bannerize_tax');

    if (empty($term)) {
      $args_term = [
        'description' => __('This term has been import from WP Bannerize Group', 'wp-bannerize'),
        'slug' => sprintf('wp-bannerize-%s', sanitize_title($row->group)),
      ];
      $term_id = wp_insert_term($row->group, 'wp_bannerize_tax', $args_term);
      if (is_wp_error($term_id)) {
        return $term_id;
      }
    } elseif (is_array($term)) {
      $term_id = absint($term['term_id']);
    } elseif (is_numeric($term)) {
      $term_id = absint($term);
    } else {
      return new WP_Error('term-failure', 'else in term_exists()');
    }

    // Prepare args
    $args_post = [
      'post_type' => 'wp_bannerize',
      'post_author' => $user_id,
      'post_status' => $status,
      'post_title' => $row->description,
      'post_content' => $row->free_html,
      'menu_order' => $row->sorter,
    ];

    $post_id = wp_insert_post($args_post, true);

    if (is_wp_error($post_id)) {
      return $post_id;
    }

    // Set category
    $wp_error = wp_set_post_terms($post_id, $term_id, 'wp_bannerize_tax', true);
    if (is_wp_error($wp_error)) {
      return $wp_error;
    }

    // Converter old type
    $type = [
      1 => 'local',
      2 => 'remote',
      3 => 'text',
    ];

    // Sanitize width and height
    $width = absint($row->width) . 'px';
    $height = absint($row->height) . 'px';

    // get banner
    $banner = WPBannerizePost::find($post_id);

    // Get the file mime type
    $sizes = $banner->getSizeWithURL($row->filename);
    $mime_type = $sizes['mime'];

    // Post Meta
    update_post_meta($post_id, 'wp_bannerize_banner_type', $type[$row->banner_type]);
    update_post_meta($post_id, 'wp_bannerize_banner_date_from', $row->date_from);
    update_post_meta($post_id, 'wp_bannerize_banner_date_expiry', $row->date_expiry);
    update_post_meta($post_id, 'wp_bannerize_banner_link', $row->url);
    update_post_meta($post_id, 'wp_bannerize_banner_target', $row->target);
    update_post_meta($post_id, 'wp_bannerize_banner_no_follow', $row->nofollow);
    update_post_meta($post_id, 'wp_bannerize_banner_width', $width);
    update_post_meta($post_id, 'wp_bannerize_banner_height', $height);
    update_post_meta($post_id, 'wp_bannerize_banner_url', $row->filename);
    update_post_meta($post_id, 'wp_bannerize_banner_external_url', $row->filename);
    update_post_meta($post_id, 'wp_bannerize_banner_mime_type', $mime_type);

    return $post_id;
  }
}
