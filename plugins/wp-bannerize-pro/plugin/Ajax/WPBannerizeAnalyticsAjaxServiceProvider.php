<?php

namespace WPBannerize\Ajax;

use WPBannerize\Models\WPBannerizeClicks;
use WPBannerize\Models\WPBannerizeCTR;
use WPBannerize\Models\WPBannerizeImpressions;


class WPBannerizeAnalyticsAjaxServiceProvider extends AjaxServiceProvider
{

  /**
   * List of the ajax actions executed by both logged and not logged users.
   * Here you will use a methods list.
   *
   * @var array
   */
  protected $trusted = ['wp_bannerize_add_clicks', 'wp_bannerize_add_impressions'];

  /**
   * List of the ajax actions executed only by logged-in users.
   * Here you will use a methods list.
   *
   * @var array
   */
  protected $logged = [
    'wp_bannerize_overall_clicks',
    'wp_bannerize_overall_impressions',
    'wp_bannerize_overall_top_most_impressions',
    'wp_bannerize_overall_top_most_impressions_campaign',
    'wp_bannerize_overall_top_most_clicks',
    'wp_bannerize_overall_top_most_clicks_campaign',
    'wp_bannerize_get_clicks',
    'wp_bannerize_get_clicks_count',
    'wp_bannerize_get_clicks_trends',
    'wp_bannerize_get_impressions',
    'wp_bannerize_get_impressions_count',
    'wp_bannerize_get_impressions_trends',
    'wp_bannerize_get_ctr_trends',
    'wp_bannerize_get_campaigns',
    'wp_bannerize_get_banners',
    'wp_bannerize_delete_impressions',
    'wp_bannerize_delete_clicks',
    'wp_bannerize_keep_clean_clicks',
    'wp_bannerize_keep_clean_impressions',
    'wp_bannerize_export_sql_clicks',
    'wp_bannerize_export_sql_impressions',
    'wp_bannerize_export_csv_clicks',
    'wp_bannerize_export_csv_impressions',
  ];

  /**
   * Keep clean clicks
   */
  public function wp_bannerize_keep_clean_clicks()
  {
    [$mode] = $this->useHTTPPost('mode');
    switch ($mode) {
      case 'delete_max_records_exceeded':
        $result = WPBannerizeClicks::cleanUpOldRecords();
        break;
      case 'retain_within_recent_months':
        $result = WPBannerizeClicks::retainWithinRecentMonths();
        break;
    }

    wp_send_json($result);
  }

  /**
   * Keep clean impressions
   */
  public function wp_bannerize_keep_clean_impressions()
  {
    [$mode] = $this->useHTTPPost('mode');
    switch ($mode) {
      case 'delete_max_records_exceeded':
        $result = WPBannerizeImpressions::cleanUpOldRecords();
        break;
      case 'retain_within_recent_months':
        $result = WPBannerizeImpressions::retainWithinRecentMonths();
        break;
    }

    wp_send_json($result);
  }


  /**
   * Returns the impressions count
   */
  public function wp_bannerize_get_impressions_count()
  {
    $result = WPBannerizeImpressions::count();

    wp_send_json($result);
  }

  /**
   * Returns the clicks count
   */
  public function wp_bannerize_get_clicks_count()
  {
    $result = WPBannerizeClicks::count();

    wp_send_json($result);
  }

  public function wp_bannerize_delete_clicks()
  {
    [$id] = $this->useHTTPPost('id');

    if (empty($id)) {
      wp_send_json_error([
        'description' => __('No id set', 'wp-bannerize'),
      ]);
    }

    $result = WPBannerizeClicks::delete($id);

    wp_send_json($result);
  }

  public function wp_bannerize_delete_impressions()
  {
    [$id] = $this->useHTTPPost('id');

    if (empty($id)) {
      wp_send_json_error([
        'description' => __('No id set', 'wp-bannerize'),
      ]);
    }

    $result = WPBannerizeImpressions::delete($id);

    wp_send_json($result);
  }

  /**
   * Return the campaigns
   *
   * @return void
   */
  public function wp_bannerize_get_campaigns()
  {
    $args = array(
      'hide_empty' => true,
    );

    $terms = get_terms('wp_bannerize_tax', $args);

    wp_send_json($terms);
  }

  /**
   * Return the banners
   *
   * @return void
   */
  public function wp_bannerize_get_banners()
  {
    $args = array(
      'post_type' => 'wp_bannerize',
      'posts_per_page' => -1,
      'post_status' => 'publish',
    );

    $banners = get_posts($args);

    wp_send_json($banners);
  }

  /**
   * Adds a new click
   */
  public function wp_bannerize_add_clicks()
  {
    [$banner_id, $referrer] = $this->useHTTPPost('banner_id', 'referrer');

    if (empty($banner_id)) {
      wp_send_json_error([
        'description' => __('No banner id set', 'wp-bannerize'),
      ]);
    }

    if (empty($referrer)) {
      wp_send_json_error([
        'description' => __('No referrer set', 'wp-bannerize'),
      ]);
    }

    WPBannerizeClicks::create([
      'banner_id' => absint($banner_id),
      'referrer' => esc_url($referrer ?? ''),
      'ip' => $_SERVER['REMOTE_ADDR'],
      'user_agent' => $_SERVER['HTTP_USER_AGENT'],
    ]);

    wp_send_json_success();
  }

  /**
   * Adds a new impression
   */
  public function wp_bannerize_add_impressions()
  {
    [$banner_id, $referrer] = $this->useHTTPPost('banner_id', 'referrer');

    if (empty($banner_id)) {
      wp_send_json_error([
        'description' => __('No banner id set', 'wp-bannerize'),
      ]);
    }

    if (empty($referrer)) {
      wp_send_json_error([
        'description' => __('No referrer set', 'wp-bannerize'),
      ]);
    }

    $ids = (array)$banner_id;

    foreach ($ids as $bannerId) {
      WPBannerizeImpressions::create([
        'banner_id' => absint($bannerId),
        'referrer' => esc_url($referrer ?? ''),
        'ip' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
      ]);
    }

    wp_send_json_success();
  }

  /**
   * Get the overall clicks
   */
  public function wp_bannerize_overall_clicks()
  {
    $result = WPBannerizeClicks::getOverall();

    wp_send_json($result);
  }

  /**
   * Get the overall impressions
   */
  public function wp_bannerize_overall_impressions()
  {
    $result = WPBannerizeImpressions::getOverall();

    wp_send_json($result);
  }

  /**
   * Get the overall top most impressions
   */
  public function wp_bannerize_overall_top_most_impressions()
  {
    $result = WPBannerizeImpressions::getMost();

    wp_send_json($result);
  }

  public function wp_bannerize_overall_top_most_impressions_campaign()
  {
    $result = WPBannerizeImpressions::getMostCampaigns();

    wp_send_json($result);
  }

  /**
   * Get the overall top most clicked
   */
  public function wp_bannerize_overall_top_most_clicks()
  {
    $result = WPBannerizeClicks::getMost();

    wp_send_json($result);
  }

  public function wp_bannerize_overall_top_most_clicks_campaign()
  {
    $result = WPBannerizeClicks::getMostCampaigns();

    wp_send_json($result);
  }

  /**
   * Get the paginate clicks
   */
  public function wp_bannerize_get_clicks()
  {

    [$orderBy, $pageSize, $page, $campaigns, $banners] = $this->useHTTPPost('orderBy', 'pageSize', 'page', 'campaigns', 'banners');

    $result = WPBannerizeClicks::getWith(
      [
        'orderBy' => $orderBy,
        'pageSize' => $pageSize,
        'page' => $page,
        'campaigns' => $campaigns,
        'banners' => $banners,
      ]
    );

    wp_send_json($result);
  }


  /**
   * Get the paginate impressions
   */
  public function wp_bannerize_get_impressions()
  {
    [
      $orderBy,
      $pageSize,
      $page,
      $campaigns,
      $banners
    ] = $this->useHTTPPost('orderBy', 'pageSize', 'page', 'campaigns', 'banners');

    $result = WPBannerizeImpressions::getWith(
      [
        'orderBy' => $orderBy,
        'pageSize' => $pageSize,
        'page' => $page,
        'campaigns' => $campaigns,
        'banners' => $banners,
      ]
    );

    wp_send_json($result);
  }

  /**
   * Get the charts impressions
   */
  public function wp_bannerize_get_impressions_trends()
  {
    [$accuracy, $campaigns, $banners] = $this->useHTTPPost('accuracy', 'campaigns', 'banners');

    $result = WPBannerizeImpressions::getTrends(
      [
        'accuracy' => $accuracy,
        'campaigns' => $campaigns,
        'banners' => $banners
      ]
    );

    wp_send_json($result);
  }

  /**
   * Get the charts clicks
   */
  public function wp_bannerize_get_clicks_trends()
  {
    [$accuracy, $campaigns, $banners] = $this->useHTTPPost('accuracy', 'campaigns', 'banners');

    $result = WPBannerizeClicks::getTrends(
      [
        'accuracy' => $accuracy,
        'campaigns' => $campaigns,
        'banners' => $banners
      ]
    );

    wp_send_json($result);
  }

  /**
   * Get the charts CTR
   */
  public function wp_bannerize_get_ctr_trends()
  {
    [$accuracy, $campaigns, $banners] = $this->useHTTPPost('accuracy', 'campaigns', 'banners');

    $result = WPBannerizeCTR::getTrends(
      [
        'accuracy' => $accuracy,
        'campaigns' => $campaigns,
        'banners' => $banners
      ]
    );

    wp_send_json($result);
  }

  /**
   * Export SQL Clicks
   */
  public function wp_bannerize_export_sql_clicks()
  {
    WPBannerizeClicks::exportSQL();
  }

  /**
   * Export SQL Impressions
   */
  public function wp_bannerize_export_sql_impressions()
  {
    WPBannerizeImpressions::exportSQL();
  }

  /**
   * Export CSV Clicks
   */
  public function wp_bannerize_export_csv_clicks()
  {
    WPBannerizeClicks::exportCSV();
  }

  /**
   * Export CSV Impressions
   */
  public function wp_bannerize_export_csv_impressions()
  {
    WPBannerizeImpressions::exportCSV();
  }
}
