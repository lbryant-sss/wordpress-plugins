<?php

namespace WPBannerize\Models;

use WPBannerize\WPBones\Support\Str;
use WPBannerize\Models\WPBannerizeClicks;
use WPBannerize\Models\WPBannerizeImpressions;

class WPBannerizePost
{
  use WordPressPostTrait;

  public $postType = 'wp_bannerize';

  protected $imageMimeTypes = ['image/gif', 'image/png', 'image/jpeg', 'image/jpeg', 'image/jpeg'];

  protected $post;

  public function __get($name)
  {
    if ($this->post) {
      $metaKey = "wp_bannerize_{$name}";

      foreach ($this->post as $key => $value) {
        if ($key == $name) {
          return $this->post->{$name};
        }
      }

      //error_log("GET META: $name");

      $value = get_post_meta($this->post->ID, $metaKey, true);

      //error_log("RESULT: $value");

      if (empty($value)) {
        // check for default value
        $defaultValueMethod = 'get' . Str::studly($name) . 'Attribute';
        if (method_exists($this, $defaultValueMethod)) {
          return $this->{$defaultValueMethod}();
        }
      }

      return $value;
    }

    return null;
  }

  /**
   * Get the banner clicks when if meta is empty
   *
   * @return string
   */
  public function getBannerClicksAttribute()
  {
    $clicks = WPBannerizeClicks::countByBannerId($this->post->ID);

    update_post_meta($this->post->ID, 'wp_bannerize_banner_clicks', $clicks);

    return $clicks;
  }

  /**
   * Get the banner clicks when if meta is empty
   *
   * @return string
   */
  public function getBannerImpressionsAttribute()
  {
    $clicks = WPBannerizeImpressions::countByBannerId($this->post->ID);

    update_post_meta($this->post->ID, 'wp_bannerize_banner_impressions', $clicks);

    return $clicks;
  }

  public function __toString()
  {
    ob_start();

    $this->display();

    $content = ob_get_contents();
    ob_end_clean();

    return $content;
  }

  /**
   * Display the banner.
   *
   * @return void
   */
  public function display()
  {
    $content = $this->getContentBasedOnMimeType();
    $classes = $this->getBannerClasses();
    $styles = $this->getBannerStyles();
    $attributes = $this->getBannerAttributes();

    echo $this->renderBanner($classes, $styles, $attributes, $content);
  }

  /**
   * Get banner content based on mime type.
   *
   * @return string
   */
  private function getContentBasedOnMimeType()
  {
    if (in_array($this->banner_mime_type, $this->imageMimeTypes)) {
      return $this->getImage();
    } elseif ($this->banner_mime_type == 'text/plain') {
      return $this->getText();
    }
    return '';
  }

  /**
   * Get banner classes.
   *
   * @return array
   */
  private function getBannerClasses()
  {
    $classes = ['wp_bannerize_banner_box'];
    $terms = wp_get_post_terms($this->post->ID, 'wp_bannerize_tax');

    foreach ($terms as $term) {
      $classes[] = "wp_bannerize_category_{$term->slug}";
    }

    return apply_filters('wp_bannerize_classes', $classes);
  }

  /**
   * Get banner styles.
   *
   * @return string
   */
  private function getBannerStyles()
  {
    $stack = [];
    foreach (['top', 'right', 'bottom', 'left'] as $key) {
      $value = WPBannerize()->options["Layout.{$key}"];
      if (!empty($value)) {
        $stack[] = "margin-{$key}:{$value}px";
      }
    }
    return implode(';', $stack);
  }

  /**
   * Get banner attributes.
   *
   * @return array
   */
  private function getBannerAttributes()
  {
    $attributes = [
      'data-title' => esc_attr($this->post_title),
      'data-mime_type' => esc_attr(sanitize_title($this->banner_mime_type)),
      'data-banner_id' => esc_attr($this->ID),
      'id' => "wpbanner-" . esc_attr($this->ID),
    ];

    if (!empty(wpbones_value($this->banner_impressions_enabled))) {
      $attributes['data-impressions_enabled'] = 'true';
    }

    if (!empty(wpbones_value($this->banner_clicks_enabled))) {
      $attributes['data-clicks_enabled'] = 'true';
    }

    return $attributes;
  }

  /**
   * Render banner.
   *
   * @param array $classes
   * @param string $styles
   * @param array $attributes
   * @param string $content
   * @return string
   */
  private function renderBanner($classes, $styles, $attributes, $content)
  {
    $classString = esc_attr(implode(' ', $classes));
    $styleString = esc_attr($styles);
    $attributeString = '';

    foreach ($attributes as $key => $value) {
      $attributeString .= sprintf(' %s="%s"', $key, esc_attr($value));
    }

    return sprintf(
      '<div class="%s" style="%s"%s>%s</div>',
      $classString,
      $styleString,
      $attributeString,
      $content
    );
  }


  protected function getImage()
  {
    $src = $this->getUrl();
    $title = $this->getDescription();
    $link = $this->banner_link;

    $target = '';
    if (!empty(wpbones_value($this->banner_target))) {
      $target = sprintf('target="%s"', $this->banner_target);
    }

    // Get width and height
    $width = $this->getWidth();
    $height = $this->getHeight();

    // Stack
    $size = [];

    if (!empty($width)) {
      $size[] = sprintf('width="%s"', $width);
    }

    if (!empty($height)) {
      $size[] = sprintf('height="%s"', $height);
    }

    // Build size string and remove px
    $size_string = str_replace('px', '', implode(' ', $size));

    $nofollow = !empty(wpbones_value($this->banner_no_follow)) ? 'rel="nofollow"' : '';

    $html = sprintf('<img border="0" %s src="%s" alt="%s" title="%s" />', $size_string, $src, $title, $title);
    if (!empty($link)) {
      $html = sprintf('<a href="%s" %s %s>%s</a>', $link, $target, $nofollow, $html);
    }

    return $html;
  }

  public function getBannerTypeAttribute()
  {
    return empty($this->banner_type) ? 'local' : $this->banner_type;
  }

  public function getUrl()
  {
    return 'local' == $this->banner_type ? $this->banner_url : $this->banner_external_url;
  }

  public function getDescription()
  {
    $description = $this->banner_description;

    return empty($description) ? $this->post_title : $description;
  }

  public function getWidth()
  {
    if (empty(wpbones_value($this->banner_width))) {
      return '';
    }

    return is_numeric($this->banner_width) ? $this->banner_width . 'px' : $this->banner_width;
  }

  public function getHeight()
  {
    if (empty(wpbones_value($this->banner_height))) {
      return '';
    }

    return is_numeric($this->banner_height) ? $this->banner_height . 'px' : $this->banner_height;
  }

  protected function getText()
  {
    // Get size
    $width = empty(wpbones_value($this->banner_width)) ? '100%' : $this->banner_width;
    $height = empty(wpbones_value($this->banner_height)) ? '100%' : $this->banner_height;

    $html = do_shortcode($this->post_content);

    return sprintf('<div style="width:%s;height:%s">%s</div>', $width, $height, $html);
  }

  public static function all()
  {
    return get_posts([
      'numberposts' => -1,
      'post_type' => 'wp_bannerize',
    ]);
  }

  /**
   * Description
   *
   * @param $vars
   * @return int[]|\WP_Post[]
   */
  public static function expired($vars = [])
  {
    remove_all_filters('parse_query');

    $args = [
      'meta_query' => self::metaQuery('expired'),
      'post_type' => 'wp_bannerize',
      'posts_per_page' => -1,
    ];

    $args = array_merge($args, $vars);

    $posts = get_posts($args);

    return $posts;
  }

  public static function metaQuery($value)
  {
    $metaQuery = [
      'expired' => [
        'relation' => 'AND',
        [
          'key' => 'wp_bannerize_banner_date_expiry',
          'value' => '',
          'compare' => '!=',
        ],
        [
          'key' => 'wp_bannerize_banner_date_expiry',
          'value' => time(),
          'compare' => '<',
          'type' => 'NUMERIC',
        ],
      ],
      'max_impressions' => [
        'relation' => 'AND',
        [
          'key' => 'wp_bannerize_banner_max_impressions',
          'value' => 0,
          'compare' => '!=',
          'type' => 'NUMERIC',
        ],
      ],
      'max_clicks' => [
        'relation' => 'AND',
        [
          'key' => 'wp_bannerize_banner_max_clicks',
          'value' => 0,
          'compare' => '!=',
          'type' => 'NUMERIC',
        ],
      ],
      'scheduled' => [
        'relation' => 'AND',
        [
          'key' => 'wp_bannerize_banner_date_from',
          'value' => '',
          'compare' => '!=',
        ],
        [
          'key' => 'wp_bannerize_banner_date_from',
          'value' => time(),
          'compare' => '>',
          'type' => 'NUMERIC',
        ],
      ],
      'timed' => [
        'relation' => 'OR',
        [
          'key' => 'wp_bannerize_banner_date_from',
          'value' => '',
          'compare' => '!=',
        ],
        [
          'key' => 'wp_bannerize_banner_date_expiry',
          'value' => '',
          'compare' => '!=',
        ],
      ],
    ];

    return $metaQuery[$value] ?? [];
  }

  public static function max_impressions($vars = [])
  {
    remove_all_filters('parse_query');

    $args = [
      'meta_query' => self::metaQuery('max_impressions'),
      'post_type' => 'wp_bannerize',
      'posts_per_page' => -1,
    ];

    $args = array_merge($args, $vars);

    $posts = get_posts($args);

    return $posts;
  }

  public static function max_clicks($vars = [])
  {
    remove_all_filters('parse_query');

    $args = [
      'meta_query' => self::metaQuery('max_clicks'),
      'post_type' => 'wp_bannerize',
      'posts_per_page' => -1,
    ];

    $args = array_merge($args, $vars);

    $posts = get_posts($args);

    return $posts;
  }

  public static function scheduled($vars = [])
  {
    remove_all_filters('parse_query');

    $args = [
      'meta_query' => self::metaQuery('scheduled'),
      'post_type' => 'wp_bannerize',
      'posts_per_page' => -1,
    ];

    $args = array_merge($args, $vars);

    $posts = get_posts($args);

    return $posts;
  }

  public static function timed($vars = [])
  {
    remove_all_filters('parse_query');

    $args = [
      'meta_query' => self::metaQuery('timed'),
      'post_type' => 'wp_bannerize',
      'posts_per_page' => -1,
    ];

    $args = array_merge($args, $vars);

    $posts = get_posts($args);

    return $posts;
  }

  public static function where()
  {
    $instance = new self();

    if (func_num_args() > 2) {
      [$field, $cond, $value] = func_get_args();
    } elseif (func_num_args() > 1) {
      [$field, $value] = func_get_args();
      $cond = '=';
    } else {
      return null;
    }

    switch ($field) {
      case 'path':
        $instance->post = get_page_by_path($value, OBJECT, $instance->postType);
        break;
      case 'title':
        $instance->post = wp_bannerize_get_page_by_title($value, $instance->postType);
        break;
    }

    return $instance;
  }

  public function preview()
  {
    if (in_array($this->banner_mime_type, $this->imageMimeTypes)) {
      return WPBannerize()->view('cpt.preview-image')->with('banner', $this);
    } elseif ($this->banner_mime_type == 'text/plain') {
      return WPBannerize()->view('cpt.preview-txt')->with('banner', $this);
    }

    return WPBannerize()->view('cpt.preview-not-available')->with('banner', $this);
  }

  public function thumbnail()
  {
    if (in_array($this->banner_mime_type, $this->imageMimeTypes)) {
      return WPBannerize()->view('cpt.thumbnail-image')->with('banner', $this);
    } elseif ($this->banner_mime_type == 'text/plain') {
      return WPBannerize()->view('cpt.thumbnail-txt')->with('banner', $this);
    }

    return WPBannerize()->view('cpt.thumbnail-not-available')->with('banner', $this);
  }

  public function getSizeWithURL($url)
  {
    // Prepare return
    $size = [0, 0, 'mime' => 'text/plain'];

    // Check for txt
    if ('.txt' === $url) {
      return $size;
    }

    // Check for GD library
    if (function_exists('getimagesize')) {
      // If exists
      if (!empty($url)) {
        // Try directly
        $size = @getimagesize($url);

        // Try by CURL
        if (empty($size) && function_exists('curl_version')) {
          // Try via CURL

          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

          $contents = curl_exec($ch);

          curl_close($ch);

          $uri = 'data://application/octet-stream;base64,' . base64_encode($contents);

          $size = @getimagesize($uri);
        }
      }
    }

    if (empty($size['mime'])) {
      $mime = $this->getMimeTypesWithExtension($this->ext($url));
      if ($mime) {
        $size['mime'] = $mime;
      }
    }

    return $size;
  }

  protected function getMimeTypesWithExtension($ext)
  {
    $mime = [
      'gif' => 'image/gif',
      'png' => 'image/png',
      'jpg' => 'image/jpeg',
      'jpe' => 'image/jpeg',
      'jpeg' => 'image/jpeg',
      'txt' => 'text/plain',
    ];

    if (isset($mime[$ext])) {
      return $mime[$ext];
    }

    return null;
  }

  protected function ext($filename)
  {
    $filename = strtolower(basename($filename));
    $parts = explode('.', $filename);

    if (empty($parts)) {
      return false;
    }

    $ext = end($parts);

    return $ext;
  }
}
