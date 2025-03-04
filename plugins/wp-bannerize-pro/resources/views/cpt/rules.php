<?php
if (!defined('ABSPATH')) {
  exit();
}

WPBannerize\PureCSSTabs\PureCSSTabsProvider::openTab(__('Rules', 'wp-bannerize'));
?>

<div>

  <h3><?php _e('Date range rules', 'wp-bannerize'); ?></h3>
  <p>
    <?php esc_attr_e(
      'This banner will be visible on for the following date range. Of course, you can left any fields blank for no date range.',
      'wp-bannerize'
    ); ?>
  </p>
  <p>
    <label for=""><?php esc_attr_e('Date from', 'wp-bannerize'); ?></label>:
    <?php WPBannerize\Html::datetime()
      ->name('wp_bannerize_banner_date_from')
      ->value($banner->banner_date_from)
      ->now(true)
      ->clear(true)
      ->render(); ?>
  </p>

  <p>
    <label for=""><?php esc_attr_e('Date to', 'wp-bannerize'); ?>:
      <?php WPBannerize\Html::datetime()
        ->name('wp_bannerize_banner_date_expiry')
        ->value($banner->banner_date_expiry)
        ->now(true)
        ->clear(true)
        ->render(); ?>
    </label>
  </p>

  <h3><?php _e('Impression rules', 'wp-bannerize'); ?></h3>

  <p>
    <?php _e('Here you can set the maximum number of impressions for this banner.', 'wp-bannerize'); ?>
  </p>

  <p>
    <label for=""><?php esc_attr_e('Maximum impressions', 'wp-bannerize'); ?>:
      <?php WPBannerize\Html::input()
        ->type('number')
        ->name('wp_bannerize_banner_max_impressions')
        ->value($banner->banner_max_impressions)
        ->min(0)
        ->render(); ?>
      <?php
      _e('Current impressions:', 'wp-bannerize');
      echo $banner->banner_impressions; ?>
    </label>
  </p>

  <h3><?php _e('Click rules', 'wp-bannerize'); ?></h3>

  <p>
    <?php _e('Here you can set the maximum number of clicks for this banner.', 'wp-bannerize'); ?>
  </p>

  <p>
    <label for=""><?php esc_attr_e('Maximum clicks', 'wp-bannerize'); ?>:
      <?php WPBannerize\Html::input()
        ->type('number')
        ->name('wp_bannerize_banner_max_clicks')
        ->value($banner->banner_max_clicks)
        ->min(0)
        ->render(); ?>
      <?php
      _e('Current clicks:', 'wp-bannerize');
      echo $banner->banner_clicks; ?>
    </label>
  </p>



</div>

<?php WPBannerize\PureCSSTabs\PureCSSTabsProvider::closeTab();
