<?php
if (!defined('ABSPATH')) {
  exit();
}

WPBannerize\PureCSSTabs\PureCSSTabsProvider::openTab(__('Rules', 'wp-bannerize'));
?>

<div>
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
    <label for=""><?php esc_attr_e('Date to', 'wp-bannerize'); ?></label>:
    <?php WPBannerize\Html::datetime()
      ->name('wp_bannerize_banner_date_expiry')
      ->value($banner->banner_date_expiry)
      ->now(true)
      ->clear(true)
      ->render(); ?>
  </p>

</div>

<?php WPBannerize\PureCSSTabs\PureCSSTabsProvider::closeTab();
