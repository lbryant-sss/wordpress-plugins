<?php
if (!defined('ABSPATH')) {
  exit;
}

$size = $banner->getSizeWithURL($banner->getUrl());
?>

<div class="wp-bannerize-image-thumbnail">
  <a rel="gallery" title="<?php echo esc_attr($banner->getDescription()) ?>" href="<?php echo esc_url($banner->getUrl()) ?>" class="thickbox">
    <img alt="<?php echo esc_attr($banner->getDescription()) ?>" border="0" src="<?php echo esc_url($banner->getUrl()) ?>" />
  </a>
</div>