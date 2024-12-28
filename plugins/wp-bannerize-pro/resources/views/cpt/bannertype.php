<?php WPBannerize\PureCSSTabs\PureCSSTabsProvider::openTab(__('Choose a Banner type', 'wp-bannerize'), null, true) ?>

<div class="wp-bannerize-editor">

  <p>
    <?php esc_attr_e('Select the type of Banner', 'wp-bannerize') ?>
  </p>

  <div class="wp-bannerize-btn-group">

    <?php
    $types = [
      'local'  => __('Local', 'wp-bannerize'),
      'remote' => __('Remote', 'wp-bannerize'),
      'text'   => __('Text', 'wp-bannerize'),
    ];

    foreach ($types as $type => $label) : ?>
      <label for="<?php echo esc_attr($type) ?>">
        <input type="radio" id="<?php echo esc_attr($type) ?>" data-tab="#wp-bannerize-tab-<?php echo esc_attr($type) ?>" <?php checked($type, $banner->banner_type) ?> value="<?php echo esc_attr($type) ?>" name="wp_bannerize_banner_type" />
        <?php echo esc_attr($label) ?>
      </label>
    <?php endforeach; ?>
  </div>

  <div class="wp-bannerize-tab" <?php echo ('local' !== $banner->banner_type) ? 'style="display:none"' : '' ?> id="wp-bannerize-tab-local">

    <h4>
      <?php esc_attr_e('From this panel you can upload a local media into your WordPress installation.', 'wp-bannerize') ?>
    </h4>

    <input type="hidden" id="wp_bannerize_banner_url" name="wp_bannerize_banner_url" value="<?php echo esc_url($banner->banner_url) ?>" />

    <button data-uploader_title="<?php esc_attr_e('Choose a file', 'wp-bannerize') ?>" data-uploader_button_text="<?php esc_attr_e('Select', 'wp-bannerize') ?>" class="upload_image_button button button-primary button-large">
      <?php esc_attr_e('Choose a file', 'wp-bannerize') ?>
    </button>
  </div>

  <div class="wp-bannerize-tab" <?php echo ('remote' !== $banner->banner_type) ? 'style="display:none"' : '' ?> id="wp-bannerize-tab-remote">

    <h4>
      <?php esc_attr_e('Use this panel to insert a remote URL for your media.', 'wp-bannerize') ?>
    </h4>

    <label for="wp_bannerize_banner_external_url" class="">
      <?php esc_attr_e('URL', 'wp-bannerize') ?>
    </label>:

    <input class="" size="64" placeholder="http://" type="url" name="wp_bannerize_banner_external_url" id="wp_bannerize_banner_external_url" value="<?php echo esc_url($banner->banner_external_url) ?>" />
  </div>

  <div class="wp-bannerize-tab" <?php echo ('text' !== $banner->banner_type) ? 'style="display:none"' : '' ?> id="wp-bannerize-tab-text">

    <h4>
      <?php esc_attr_e('This is an amazing feature! You can create your own banner with this free rich text editor.', 'wp-bannerize') ?>
    </h4>

    <div id="wp-bannerize-post-content-container">
      <?php wp_editor($banner->post_content, 'post_content') ?>
    </div>
  </div>

</div>

<?php WPBannerize\PureCSSTabs\PureCSSTabsProvider::closeTab();
