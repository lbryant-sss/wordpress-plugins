<?php
if (!defined('ABSPATH')) {
    exit;
}

// Browser detect
global $is_chrome, $is_gecko, $is_opera;

// Firefox and Chrome supports a native color picker input field. Others browsers will be use the WordPress Color Picker
$type = ($is_chrome || $is_gecko || $is_opera) ? 'color' : 'text';

$style = sprintf('style="width:%s;height:%s"', $banner->banner_width, $banner->banner_height);

$size = $banner->getSizeWithURL($banner->getUrl());

?>
<label for="">
    <?php esc_attr_e('Background Color Preview', 'wp-bannerize') ?>:
    <input name="wp_bannerize_preview_background_color" id="wp_bannerize_preview_background_color"
           value="<?php echo esc_attr($banner->preview_background_color) ?>" type="<?php echo esc_attr($type) ?>"/>
</label>

<div class="wp-bannerize-image-preview"
     style="background-color: <?php echo esc_attr($banner->preview_background_color) ?>">
    <img alt="<?php echo esc_attr($banner->getDescription()) ?>" <?php echo wp_kses_post($style) ?>
         src="<?php echo esc_url($banner->getUrl()) ?>"/>
</div>
<p class="text-center">
    <strong><?php esc_attr_e('Type', 'wp-bannerize') ?></strong>: <?php echo esc_attr($banner->banner_mime_type) ?> -
    <strong><?php esc_attr_e('Size', 'wp-bannerize') ?></strong>: <?php echo esc_attr(sprintf('%sx%s', $size[0], $size[1])) ?>
    pixel
</p>