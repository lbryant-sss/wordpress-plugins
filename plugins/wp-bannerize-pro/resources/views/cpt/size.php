<?php
if (!defined('ABSPATH')) {
  exit;
}

WPBannerize\PureCSSTabs\PureCSSTabsProvider::openTab(__('Size', 'wp-bannerize')) ?>

<div>

  <?php if (!function_exists('getimagesize')) : ?>
    <div>
      <?php echo wp_kses_data('The function <code>getimagesize()</code> is not available. PHP GD Library are not installed. Please contact your administrator site to fix it.', 'wp-bannerize') ?>
    </div>
  <?php endif; ?>


  <p><?php echo wp_kses_data('You now can enter your custom width and height with measure units. For example you can use <code>100px</code> or <code>100%</code> or <code>auto</code>. Leave blank to auto get the right size. Set a unit of measurement. If blank, we\'ll set pixels.', 'wp-bannerize') ?></p>

  <label for="wp_bannerize_banner_width">
    <?php esc_attr_e('Custom width', 'wp-bannerize') ?>:
    <input name="wp_bannerize_banner_width" id="wp_bannerize_banner_width" type="text" value="<?php echo esc_attr($banner->banner_width) ?>" placeholder="<?php esc_attr_e('eg: 100%', 'wp-bannerize') ?>" />

  </label>

  <label for="wp_bannerize_banner_height">
    <?php esc_attr_e('Custom height', 'wp-bannerize') ?>:
    <input name="wp_bannerize_banner_height" id="wp_bannerize_banner_height" type="text" value="<?php echo esc_attr($banner->banner_height) ?>" placeholder="<?php esc_attr_e('eg: 100%', 'wp-bannerize') ?>" />

  </label>

</div>

<?php WPBannerize\PureCSSTabs\PureCSSTabsProvider::closeTab();
