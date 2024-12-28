<?php

namespace WPBannerize\Providers;

use WPBannerize\WPBones\Support\ServiceProvider;

class WPBannerizeFrontendServiceProvider extends ServiceProvider
{

  protected string $impressions_event = 'wp_bannerize_delete_impressions_exceeded_event';
  protected string $clicks_event = 'wp_bannerize_delete_clicks_exceeded_event';
  protected bool $clicksEnabled = false;
  protected bool $impressionsEnabled = false;
  protected string $keepCleanClicks = 'disabled';
  protected string $keepCleanImpressions = 'disabled';
  protected string $schedulesClicks = 'twicedaily';
  protected string $schedulesImpressions = 'twicedaily';

  public function register()
  {
    add_action('wp_loaded', [$this, 'wp_loaded'], 99);
    add_action('wp_head', [$this, 'wp_head']);

    if (!is_admin()) {
      WPBannerize()->css('wp-bannerize.css');
      WPBannerize()->js('wp-bannerize-impressions.js', ['jquery']);
    }

    $this->clicksEnabled = WPBannerize()->options->get('clicks.enabled') ?? false;
    $this->impressionsEnabled = WPBannerize()->options->get('impressions.enabled') ?? false;

    $this->keepCleanClicks = WPBannerize()->options->get('clicks.keep_clean') ?? 'disabled';
    $this->keepCleanImpressions = WPBannerize()->options->get('impressions.keep_clean') ?? 'disabled';

    $this->schedulesClicks = WPBannerize()->options->get('clicks.schedules') ?? 'twicedaily';
    $this->schedulesImpressions = WPBannerize()->options->get('impressions.schedules') ?? 'twicedaily';

    if ($this->impressionsEnabled) {
      if (!wp_next_scheduled($this->impressions_event)) {
        wp_schedule_event(time(), $this->schedulesImpressions, $this->impressions_event);
      }
      switch ($this->keepCleanImpressions) {
        case 'delete_max_records_exceeded':
          add_action($this->impressions_event, ['WPBannerize\\Models\\WPBannerizeImpressions', 'cleanUpOldRecords']);
          break;
        case 'retain_within_recent_months':
          add_action($this->impressions_event, ['WPBannerize\\Models\\WPBannerizeImpressions', 'retainWithinRecentMonths']);
          break;
      }
    }

    if ($this->clicksEnabled) {
      if (!wp_next_scheduled($this->clicks_event)) {
        wp_schedule_event(time(), $this->schedulesClicks, $this->clicks_event);
      }
      switch ($this->keepCleanClicks) {
        case 'delete_max_records_exceeded':
          add_action($this->clicks_event, ['WPBannerize\\Models\\WPBannerizeClicks', 'cleanUpOldRecords']);
          break;
        case 'retain_within_recent_months':
          add_action($this->clicks_event, ['WPBannerize\\Models\\WPBannerizeClicks', 'retainWithinRecentMonths']);
          break;
      }
    }

    // add filter to the_content
    add_filter('the_content', [$this, 'the_content']);
    add_filter('the_excerpt', [$this, 'the_content']);

    // add filter to title
    add_filter('the_title', [$this, 'the_title']);

    // add filter to template_include to use a custom template for the taxonomy

    $taxonomy_custom_template = WPBannerize()->options->get('theme.campaigns.custom_template.enabled') ?? false;
    if ($taxonomy_custom_template) {
      add_filter('template_include', [$this, 'taxonomy_template_include']);
    }

    $single_custom_template = WPBannerize()->options->get('theme.banner.custom_template.enabled') ?? false;
    if ($single_custom_template) {
      add_filter('template_include', [$this, 'single_post_template_include']);
    }
  }

  public function single_post_template_include($template): string
  {
    if (is_singular('wp_bannerize')) {
      $path = WPBannerize()->getBasePath();
      $file = WPBannerize()->options->get('theme.banner.custom_template.file') ?? 'custom-single-template.php';
      $default = $file === 'custom-single-template.php';
      // Set the path to your custom template in the plugin
      $custom_template = $default ? "{$path}/templates/{$file}" : $file;

      // Check if the custom template exists, otherwise use the default template
      if (file_exists($custom_template)) {
        return $custom_template;
      }
    }
    return $template;
  }

  /**
   * Use a custom template for the taxonomy
   */
  public function taxonomy_template_include($template): string
  {
    if (is_tax('wp_bannerize_tax')) {
      $path = WPBannerize()->getBasePath();
      $file = WPBannerize()->options->get('theme.campaigns.custom_template.file') ?? 'custom-taxonomy-template.php';
      $default = $file === 'custom-taxonomy-template.php';
      // Set the path to your custom template in the plugin
      $custom_template = $default ? "{$path}/templates/{$file}" : $file;

      // Check if the custom template exists, otherwise use the default template
      if (file_exists($custom_template)) {
        return $custom_template;
      }
    }
    return $template;
  }

  /**
   * Return an empty string for the title
   */
  public function the_title($title): string
  {
    if (is_admin()) {
      return $title;
    }
    // check if the post type is wp_bannerize
    $post_type = get_post_type();

    if ($post_type !== 'wp_bannerize') {
      return $title;
    }

    return '';
  }

  /**
   * Display the banner content
   */
  public function the_content($content)
  {
    // check if the post type is wp_bannerize
    $post_type = get_post_type();

    if ($post_type !== 'wp_bannerize') {
      return $content;
    }
    // get the post id
    $post_id = get_the_ID();
    // get the post meta
    return get_wp_bannerize_pro(array('id' => $post_id));
  }

  /**
   * Add the necessary variables to the head of the page
   */
  public function wp_head()
  {
?>
    <script>
      window.ajaxurl =
        "<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
      window.WPBannerize = <?php echo WPBannerize()->options; ?>;
      window.WPBannerize.nonce = '<?php echo wp_create_nonce('wp-bannerize-pro'); ?>';
    </script>
    <?php
  }

  /**
   * Handle the request for the banner
   */
  public function wp_loaded()
  {
    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? '';
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $queryString = $_SERVER['QUERY_STRING'] ?? '';

    if (strtolower($requestMethod) === 'get' && substr($requestUri, 0, 18) === '/wp_bannerize_pro?') {
      $queryParams = [];
      parse_str($queryString, $queryParams);

      if (isset($queryParams['id']) && !empty($queryParams['id'])) {
        $post = get_post($queryParams['id']); ?>
        <!DOCTYPE html>
        <html>

        <body>
          <?php echo do_shortcode($post->post_content); ?>
        </body>

        </html>
<?php die();
      }
    }
  }
}
