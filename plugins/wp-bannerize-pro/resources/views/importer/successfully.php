<?php if (!defined('ABSPATH')) {
  exit;
} ?>

<div class="wrap">

  <h2><?php esc_attr_e('WP Bannerize Importer') ?></h2>

  <div style="text-align: center">
    <h3><?php esc_attr_e('Previous table imported successfully!', 'wp-bannerize') ?></h3>

    <p>
      <a class="button button-primary button-hero" href="<?php echo esc_attr(admin_url('edit.php?post_type=wp_bannerize')) ?>">
        <?php esc_attr_e('Manage your new banners', 'wp-bannerize') ?>
      </a>
    </p>
  </div>

</div>