<?php if (!defined('ABSPATH')) {
  exit();
} ?>
<!-- main tabs container -->
<div class="wpbones-tabs">

  <?php
  $views = ['cpt.bannertype', 'cpt.settings', 'cpt.rules', 'cpt.size', 'cpt.analytics'];

  foreach ($views as $view) {
    WPBannerize()->view($view)->with('banner', $banner)->render();
  }
  ?>

</div>
