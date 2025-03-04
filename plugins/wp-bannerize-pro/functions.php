<?php

use WPBannerize\Models\WPBannerizePost;
use WPBannerize\Models\WPBannersQuery;

if (!function_exists('wp_bannerize_get_page_by_title')) {
  /**
   * Retrieves a page by its title and type.
   *
   * @param string $title The title of the page to retrieve.
   * @param string $type  The type of the page to retrieve.
   *
   * @return WP_Post|null The retrieved page object, or null if not found.
   */
  function wp_bannerize_get_page_by_title($title, $type)
  {
    $posts = get_posts([
      'post_type' => $type,
      'title' => $title,
      'post_status' => 'all',
      'numberposts' => 1,
      'update_post_term_cache' => false,
      'update_post_meta_cache' => false,
      'orderby' => 'post_date ID',
      'order' => 'ASC',

    ]);

    if (!empty($posts)) {
      $page_got_by_title = $posts[0];
    } else {
      $page_got_by_title = null;
    }

    return $page_got_by_title;
  }
}

if (!function_exists('wp_bannerize_pro_sanitize_mysql_datetime')) {

  /**
   * Sanitize a MySQL datetime string.
   *
   */
  function wp_bannerize_pro_sanitize_mysql_datetime($value)
  {
    $result = '';

    if (!empty($value)) {
      if (false !== strpos($value, '-')) {
        $time = strtotime($value);
        $result = gmdate('Y-m-d H:i:s', $time);
        $check = strtotime($result);
      }

      if (empty($check)) {
        return gmdate('Y-m-d H:i:s');
      }
    }

    return $result;
  }
}

if (!function_exists('wp_bannerize_pro')) {

  /**
   * Display a banner or a group of banners.
   *
   * @param array $args {
   *     Optional. Array of arguments.
   *
   *     @type int|string $id The banner id or slug.
   *     @type string $layout The layout of the banner.
   *     @type string $orderby The order of the banner.
   *     @type bool $mobile Display the banner on mobile.
   *     @type bool $desktop Display the banner on desktop.
   * }
   */
  function wp_bannerize_pro($args = [])
  {
    // Backward compatibility
    $args = wp_parse_args($args);

    if (!function_exists('useDestructuringWithArray')) {
      function useDestructuringWithArray($array, ...$keys)
      {
        $result = [];
        foreach ($keys as $key) {
          $result[] = $array[$key] ?? null;
        }
        return $result;
      }
    }

    [$id, $layout, $orderby, $mobile, $desktop] = useDestructuringWithArray($args, 'id', 'layout', 'orderby', 'mobile', 'desktop');

    // user agent detect
    if (!empty($mobile)) {
      if (!wpbones_user_agent()->isMobile()) {
        return;
      }
    }

    if (!empty($desktop)) {
      if (wpbones_user_agent()->isMobile()) {
        return;
      }
    }

    // Check for single banner
    if (!empty($id)) {

      // Check for string
      if (is_string($id)) {
        $ids = explode(',', $id);
      } else {
        $ids = (array)$id;
      }

      // Support random order
      if (!empty($orderby) && 'random' == $orderby) {
        shuffle($ids);
      }

      if (!empty($layout)) {
        $layout = 'vertical';
      }
?>
      <div class="wp_bannerize_container wp_bannerize_layout_<?php echo esc_attr($layout); ?>">

        <?php
        // Loop into the banner id or slug

        foreach ($ids as $id) {
          // Create a banner instance
          $banner = WPBannerizePost::find($id, 'wp_bannerize');

          // If no banner found return the content
          if (!is_null($banner)) {
            $banner->display();
          }
        } ?>
      </div>

<?php
    } else {
      // For stability reason remove the id
      unset($id);

      // Select banners
      echo wp_kses_post(WPBannersQuery::query($args));
    }
  }
}

if (!function_exists('get_wp_bannerize_pro')) {
  function get_wp_bannerize_pro($args = [])
  {
    ob_start();
    wp_bannerize_pro($args);
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
  }
}

add_filter('wpbones_geolocalizer_ipstack_api_key', function () {
  return WPBannerize::$plugin->options->get('geolocalization.ipstack.api_key');
});

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function wp_bannerize_register_block_type()
{
  register_block_type(__DIR__ . '/public/block');

  $script_handle = generate_block_asset_handle('wp-bannerize/block', 'editorScript');

  $wp_block = wp_set_script_translations(
    $script_handle,
    'wp-bannerize',
    plugin_dir_path(__FILE__) . 'localization'
  );
}

add_action('init', 'wp_bannerize_register_block_type');

function wp_bannerize_wp_set_script_translations()
{
  $script_handle = generate_block_asset_handle('wp-bannerize/block', 'editorScript');

  $wp_block = wp_set_script_translations(
    $script_handle,
    'wp-bannerize',
    plugin_dir_path(__FILE__) . 'localization'
  );

  error_log("Wp Block $wp_block");
}
add_action('enqueue_block_editor_assets', 'wp_bannerize_wp_set_script_translations');

function rest_wp_bannerize_collection_params($params)
{
  $params['orderby']['enum'][] = 'menu_order';
  $params['orderby']['enum'][] = 'rand';

  return $params;
}

add_filter('rest_wp_bannerize_collection_params', 'rest_wp_bannerize_collection_params', 10, 1);
