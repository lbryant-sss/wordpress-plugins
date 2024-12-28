<?php
if (!defined('ABSPATH')) {
  exit();
}

WPBannerize\PureCSSTabs\PureCSSTabsProvider::openTab(__('Settings', 'wp-bannerize'));
?>

<p>
  <?php esc_attr_e('Here you can set the link and other attributes', 'wp-bannerize'); ?>
</p>

<div>
  <label for="wp_bannerize_banner_link">
    <?php esc_attr_e('Link', 'wp-bannerize'); ?>:
    <input type="url" title="<?php esc_attr_e(
      'This is the URL that allows you to link the banner',
      'wp-bannerize'
    ); ?>" placeholder="<?php esc_attr_e('http://', 'wp-bannerize'); ?>" value="<?php echo esc_url(
  $banner->banner_link
); ?>" name="wp_bannerize_banner_link" />
  </label>

  <label for="wp_bannerize_banner_target">
    <?php esc_attr_e('Target', 'wp-bannerize'); ?>:
    <?php $target = [
      '' => __('None', 'wp-bannerize'),
      '_blank' => __('_blank', 'wp-bannerize'),
      '_parent' => __('_parent', 'wp-bannerize'),
      '_self' => __('_self', 'wp-bannerize'),
      '_top' => __('_top', 'wp-bannerize'),
    ]; ?>

    <select name="wp_bannerize_banner_target" id="wp_bannerize_banner_target">
      <?php foreach ($target as $key => $value): ?>
        <option value="<?php echo esc_attr($key); ?>" <?php selected($key, $banner->banner_target); ?>>
          <?php echo esc_attr($value); ?>
        </option>
      <?php endforeach; ?>
    </select>

  </label>

  <p>
    <?php WPBannerize\PureCSSSwitch\Html\HtmlTagSwitchButton::name('wp_bannerize_banner_no_follow')
      ->checked($banner->banner_no_follow)
      ->right_label(__('Add "nofollow"', 'wp-bannerize'))
      ->render(); ?>
  </p>

</div>

<div>
  <p>
    <?php esc_attr_e(
      'Use this field as alternative alt/title description for banner. If you leave this field blank the title of banner will be use instead.',
      'wp-bannerize'
    ); ?>
  </p>

  <textarea id="wp_bannerize_banner_description" name="wp_bannerize_banner_description"><?php echo esc_attr(
    $banner->banner_description
  ); ?></textarea>

</div>

<?php WPBannerize\PureCSSTabs\PureCSSTabsProvider::closeTab();
