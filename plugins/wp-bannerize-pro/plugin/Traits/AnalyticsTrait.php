<?php

namespace WPBannerize\Traits;

trait AnalyticsTrait
{
  /**
   * Create a new record for clicks or impressions and update the post meta
   *
   * @param array $values
   * @param array $formats
   */
  public static function create($values, $formats = [])
  {
    global $wpdb;

    $instance = new static();

    $analytic = $instance->analytic;

    $meta_key = 'wp_bannerize_banner_' . $analytic;

    $result = $wpdb->insert($instance->table, $values, $formats);

    $post_id = absint($values['banner_id']);

    // get current click
    $value = (int)get_post_meta($post_id, $meta_key, true);

    if (!$value) {
      $value = 0;
    }

    $value++;

    update_post_meta($post_id, $meta_key, $value);
  }

  /**
   * Get the total number of clicks
   *
   * @return string|null
   */
  public static function count()
  {
    global $wpdb;

    $instance = new static();

    return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i", $instance->table));
  }

  /**
   * Get the total number of clicks by banner id
   *
   * @param int $banner_id
   * @return string|null
   */
  public static function countByBannerId($banner_id)
  {
    global $wpdb;

    $instance = new static();

    return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i WHERE banner_id = %d", $instance->table, $banner_id));
  }

  /**
   * Clean up the clicks table
   * Used to keep the table clean
   * Used by the cron job
   */
  public static function cleanUpOldRecords()
  {
    global $wpdb;

    $instance = new static();
    $optionMaxRecord = "$instance->analytic.max_records";

    $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i", $instance->table));
    $max_records = WPBannerize()->options->get($optionMaxRecord) ?? 1000;

    if ($count > $max_records) {
      delete_post_meta_by_key("wp_bannerize_banner_$instance->analytic");
      $num_to_delete = $count - $max_records;
      $wpdb->query(
        $wpdb->prepare(
          "DELETE FROM %i ORDER BY id ASC LIMIT %1s",
          $instance->table,
          $num_to_delete
        )
      );
      return $num_to_delete;
    }
    return 0;
  }


  public static function retainWithinRecentMonths()
  {
    global $wpdb;

    $instance = new static();

    $optionNumMonths = "$instance->analytic.num_months";
    $num_months = WPBannerize()->options->get($optionNumMonths) ?? 3;

    // Step 1: Retrieve the most recent date from the impressions table
    $max_date_query = "SELECT MAX(date) as max_date FROM $instance->table";
    $max_date = $wpdb->get_var($max_date_query);

    if ($max_date) {
      // Step 2: Calculate the cutoff date
      $cutoff_date = date('Y-m-d H:i:s', strtotime("-$num_months months", strtotime($max_date)));

      // Step 2.1: Get the count of row will be deleted
      $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $instance->table WHERE date < %s", $cutoff_date));

      // Step 3: Delete impressions older than the cutoff date
      $delete_query = $wpdb->prepare("DELETE FROM $instance->table WHERE date < %s", $cutoff_date);

      if ($wpdb->query($delete_query) !== false) {
        delete_post_meta_by_key("wp_bannerize_banner_$instance->analytic");
        error_log("Records older than $cutoff_date successfully deleted.");
        return $count;
      } else {
        error_log("Error deleting records: " . $wpdb->last_error);
      }
    } else {
      error_log("No records found in the impressions table.");
      return 0;
    }
  }


  /**
   * Delete one or more record
   *
   * @param string $id The ids of the record to delete comma separated
   * @return int
   */
  public static function delete($id)
  {
    global $wpdb;

    $instance = new static();

    $ids_to_delete = explode(',', $id);

    $placeholders = implode(',', array_fill(0, count($ids_to_delete), '%d'));

    $query = $wpdb->prepare(
      "DELETE FROM %i WHERE ID IN ($placeholders)",
      $instance->table,
      ...$ids_to_delete
    );

    delete_post_meta_by_key("wp_bannerize_banner_$instance->analytic");

    $rows_deleted = $wpdb->query($query);

    return $rows_deleted;
  }

  /**
   * Get the overall impressions
   *
   * @return array|object|null
   */
  public static function getOverall()
  {
    global $wpdb;

    $instance = new static();

    $analytic = $instance->analytic;

    return $wpdb->get_results(
      $wpdb->prepare(
        "SELECT COUNT(*) AS total_$analytic,
          COUNT(DISTINCT banner_id) AS total_banner_$analytic,
          COUNT(DISTINCT referrer) AS total_referrer_$analytic,
          COUNT(DISTINCT ip) AS total_unique_ip_$analytic,
            (SELECT COUNT(*)
            FROM (
                SELECT referrer, banner_id FROM %i
                GROUP BY referrer, banner_id
            ) AS subquery) AS total_banner_count_by_referrer
          FROM %i",
        $instance->table,
        $instance->table
      ),
      ARRAY_A
    );
  }

  /**
   * Get paginate records with optional filters
   *
   * @param array $args
   * @return array
   */
  public static function getWith($args = [])
  {
    global $wpdb;

    $instance = new static();

    $analytic = $instance->analytic;

    [
      'orderBy' => $orderBy,
      'pageSize' => $pageSize,
      'page' => $page,
      'campaigns' => $campaigns,
      'banners' => $banners,
    ] = $args;

    $where = 'WHERE 1';

    if ($campaigns) {
      $where .= " AND t.term_id IN($campaigns)";
    }

    if ($banners) {
      $where .= " AND banner_id IN($banners)";
    }

    $items = $wpdb->get_results($wpdb->prepare(
      "SELECT bannerize_$analytic.*,
    IF( posts.post_title = '', 'Untitled', posts.post_title ) AS title,
    GROUP_CONCAT(t.name SEPARATOR ', ') AS campaigns
    FROM %i AS bannerize_$analytic
    LEFT JOIN $wpdb->posts AS posts ON bannerize_$analytic.banner_id = posts.ID
    LEFT JOIN $wpdb->term_relationships tr ON bannerize_$analytic.banner_id = tr.object_id
    LEFT JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
    LEFT JOIN $wpdb->terms t ON tt.term_id = t.term_id
    %1s
    GROUP BY bannerize_$analytic.id
    ORDER BY %1s
    LIMIT %2s OFFSET %3s",
      $instance->table,
      $where,
      $orderBy,
      $pageSize,
      ($page - 1) * $pageSize
    ), ARRAY_A);

    $total_items = $wpdb->get_var($wpdb->prepare(
      "SELECT COUNT(*)
    FROM (SELECT bannerize_$analytic.id
    FROM %i AS bannerize_$analytic
    LEFT JOIN $wpdb->posts AS posts ON bannerize_$analytic.banner_id = posts.ID
    LEFT JOIN $wpdb->term_relationships tr ON bannerize_$analytic.banner_id = tr.object_id
    LEFT JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
    LEFT JOIN $wpdb->terms t ON tt.term_id = t.term_id
    %1s
    GROUP BY bannerize_$analytic.id) as count_table",
      $instance->table,
      $where
    ));

    return [
      'items' => $items,
      'total' => $total_items,
    ];
  }

  /**
   * Get trends
   *
   * @param array $args
   * @return array
   */
  public static function getTrends($args = [])
  {
    global $wpdb;

    $instance = new static();

    $analytic = $instance->analytic;

    [
      'accuracy' => $accuracy,
      'campaigns' => $campaigns,
      'banners' => $banners,
    ] = $args;

    $where = 'WHERE 1';
    $join = '';

    if ($campaigns) {
      $where .= " AND t.term_id IN($campaigns)";
      $join = "LEFT JOIN $wpdb->posts AS posts ON bannerize_$analytic.banner_id = posts.ID
      LEFT JOIN $wpdb->term_relationships tr ON bannerize_$analytic.banner_id = tr.object_id
      LEFT JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
      LEFT JOIN $wpdb->terms t ON tt.term_id = t.term_id";
      // remove any carriage returns
      $join = str_replace("\n", '', $join);
    }

    if ($banners) {
      $where .= " AND banner_id IN($banners)";
    }

    $date_format = [
      'day' => '%%Y-%%m-%%d',
      'month' => '%%Y-%%m',
      'year' => '%%Y',
    ][$accuracy];

    return $wpdb->get_results($wpdb->prepare(
      "SELECT DATE_FORMAT(date, '$date_format') AS date , COUNT(*) AS $analytic
    FROM %i AS bannerize_$analytic
    %1s
    %2s
    GROUP BY DATE_FORMAT(date, '$date_format')
    ORDER BY DATE_FORMAT(date, '$date_format') ASC",
      $instance->table,
      $join,
      $where
    ), ARRAY_A);
  }

  /**
   * Get the most clicked/impression banners
   *
   * @return array
   */
  public static function getMost()
  {
    global $wpdb;

    $instance = new static();

    $analytic = $instance->analytic;

    return $wpdb->get_results($wpdb->prepare("SELECT banner_id,
    IF(posts.post_title = '', 'Untitled', posts.post_title) AS title,
		COUNT(*) AS $analytic
	  FROM %i as $analytic
	  LEFT JOIN $wpdb->posts AS posts ON $analytic.banner_id = posts.ID
    GROUP BY banner_id
    ORDER BY $analytic DESC
    LIMIT 0, 5", $instance->table), ARRAY_A);
  }

  /**
   * Get the most clicked/impression campaigns
   *
   * @return array
   */
  public static function getMostCampaigns()
  {
    global $wpdb;

    $instance = new static();

    $analytic = $instance->analytic;

    return $wpdb->get_results($wpdb->prepare("SELECT
            terms.term_id AS term_id,
            terms.name AS title,
            COUNT(*) AS $analytic
        FROM %i AS $analytic
        LEFT JOIN $wpdb->term_relationships AS term_rel ON $analytic.banner_id = term_rel.object_id
        LEFT JOIN $wpdb->term_taxonomy AS term_tax ON term_rel.term_taxonomy_id = term_tax.term_taxonomy_id
        LEFT JOIN $wpdb->terms AS terms ON term_tax.term_id = terms.term_id
        WHERE term_tax.taxonomy = 'wp_bannerize_tax'
        GROUP BY terms.term_id
        ORDER BY $analytic DESC
        LIMIT 0, 5", $instance->table), ARRAY_A);
  }


  public static function exportSQL()
  {
    global $wpdb;

    $instance = new static();

    $analytic = $instance->analytic;

    $table_data = $wpdb->get_results("SELECT * FROM $instance->table", ARRAY_A);
    $filename = 'export_' . $analytic . '-' . date('Y-m-d') . '.sql';
    header('Content-Type: application/sql; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    $output = fopen('php://output', 'w');
    $create_table_query = $wpdb->get_row("SHOW CREATE TABLE $instance->table", ARRAY_A);
    fwrite($output, $create_table_query['Create Table'] . ";\n\n");

    foreach ($table_data as $row) {
      $values = array_map(function ($value) {
        return "'" . esc_sql($value) . "'";
      }, array_values($row));
      fwrite($output, "INSERT INTO `$instance->table` VALUES (" . implode(', ', $values) . ");\n");
    }

    fclose($output);
    exit;
  }

  public static function exportCSV()
  {
    global $wpdb;

    $instance = new static();

    $analytic = $instance->analytic;

    $filename = 'export_' . $analytic . '-' . date('Y-m-d') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    $output = fopen('php://output', 'w');

    $columns = $wpdb->get_results("SHOW COLUMNS FROM $instance->table", ARRAY_A);
    $headers = array_map(function ($column) {
      return $column['Field'];
    }, $columns);
    fputcsv($output, $headers);

    $results = $wpdb->get_results("SELECT * FROM $instance->table", ARRAY_A);
    foreach ($results as $row) {
      fputcsv($output, $row);
    }

    fclose($output);
    exit;
  }
}
