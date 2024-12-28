<?php
if (!defined('ABSPATH')) {
  exit();
}

if (!empty($instance['device']) && $instance['device'] !== 'any') {
  if ($instance['device'] === 'mobile' && !wpbones_user_agent()->isMobile()) {
    return;
  }

  if ($instance['device'] === 'desktop' && wpbones_user_agent()->isMobile()) {
    return;
  }
}

if (!empty($instance['geo_countries'])) {
  $hasCountries = \WPBannerize\GeoLocalizer\GeoLocalizerProvider::hasCountries($instance['geo_countries']);

  if (!$hasCountries) {
    return;
  }
}

$before_widget = $args['before_widget'];
$after_widget = $args['after_widget'];
$before_title = $args['before_title'];
$after_title = $args['after_title'];

echo wp_kses_post($before_widget);

// @since 1.3.8 - Added title support
if (!empty($instance['title'])) {
  printf('%s%s%s', wp_kses_post($before_title), esc_attr($instance['title']), wp_kses_post($after_title));
}

echo WPBannerize\Models\WPBannersQuery::query($instance);

echo wp_kses_post($after_widget);
