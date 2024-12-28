<?php if (!defined('ABSPATH')) {
  exit();
}

WPBannerize\PureCSSTabs\PureCSSTabsProvider::openTab(__('Analytics', 'wp-bannerize'));
?>

  <p>
    <?php esc_attr_e('Of course, you can turn off the analytics for a single banner', 'wp-bannerize'); ?>
  </p>

  <p>
    <?php WPBannerize\PureCSSSwitch\Html\HtmlTagSwitchButton::name('wp_bannerize_banner_impressions_enabled')
      ->checked($banner->banner_impressions_enabled)
      ->right_label(__('Enable Impressions', 'wp-bannerize'))
      ->render(); ?>
  </p>
  <p>
    <?php WPBannerize\PureCSSSwitch\Html\HtmlTagSwitchButton::name('wp_bannerize_banner_clicks_enabled')
      ->checked($banner->banner_clicks_enabled)
      ->right_label(__('Enable Clicks', 'wp-bannerize'))
      ->render(); ?>
  </p>

<?php WPBannerize\PureCSSTabs\PureCSSTabsProvider::closeTab();
