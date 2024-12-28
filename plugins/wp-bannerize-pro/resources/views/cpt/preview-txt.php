<?php
if (!defined('ABSPATH')) {
  exit;
}

// Browser detect
global $is_chrome, $is_gecko, $is_opera;

// Firefox and Chrome supports a native color picker input field. Others browsers will be use the WordPress Color Picker
$type = ($is_chrome || $is_gecko || $is_opera) ? 'color' : 'text';

// Get size
$width  = empty($banner->banner_width) ? '100%' : $banner->banner_width;
$height = empty($banner->banner_height) ? '100%' : $banner->banner_height;

// Preview color
$color       = '';
$color_style = '';

if (!empty($banner->preview_background_color)) {
  $color       = $banner->preview_background_color;
  $color_style = sprintf('background-color:%s', $color);
}
?>
<div style="<?php echo esc_attr($color_style) ?>" class="wp-bannerize-image-preview">
  <iframe src="/wp_bannerize_pro?id=<?php echo esc_attr($banner->ID) ?>" style="width:<?php echo esc_attr($width) ?>;height:<?php echo esc_attr($height) ?>;<?php echo esc_attr($color_style) ?>" id="wp-bannerize-iframe-preview"></iframe>
</div>
<p class="text-center">
  <strong>
    <?php esc_attr_e('Type', 'wp-bannerize') ?>
  </strong>: <?php echo esc_attr($banner->banner_mime_type) ?> - <strong><?php esc_attr_e('Size', 'wp-bannerize') ?></strong>: <?php echo esc_attr($width) ?> x <?php echo esc_attr($height) ?>
</p>
<input value="<?php echo esc_attr($color) ?>" type="<?php echo esc_attr($type) ?>" id="wp_bannerize_preview_background_color" name="wp_bannerize_preview_background_color" />