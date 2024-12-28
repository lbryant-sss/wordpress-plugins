<?php if (!defined('ABSPATH')) {
  exit;
} ?>

<h2><?php esc_attr_e('Display', 'wp-bannerize') ?></h2>

<p>
  <label for="<?php echo esc_attr($widget->get_field_name('title')) ?>">
    <?php esc_attr_e('Title', 'wp-bannerize') ?>:
    <input type="text" size="20" placeholder="<?php esc_attr_e('eg: title on top', 'wp-bannerize') ?>" value="<?php echo esc_attr($instance['title']) ?>" id="<?php echo esc_attr($widget->get_field_name('title')) ?>" name="<?php echo esc_attr($widget->get_field_name('title')) ?>" />
  </label>
</p>

<p>
  <label for="<?php echo esc_attr($widget->get_field_name('layout')) ?>">
    <?php esc_attr_e('Layout', 'wp-bannerize') ?>:
    <select id="<?php echo esc_attr($widget->get_field_name('layout')) ?>" name="<?php echo esc_attr($widget->get_field_name('layout')) ?>">
      <option <?php selected('vertical', $instance['layout']) ?> value="vertical"><?php esc_attr_e('Vertical', 'wp-bannerize') ?></option>
      <option <?php selected('horizontal', $instance['layout']) ?> value="horizontal"><?php esc_attr_e('Horizontal', 'wp-bannerize') ?></option>
    </select>
  </label>
</p>

<p>
  <strong>
    <?php esc_attr_e('Maximum number of Banners to display: use -1 to display all banners', 'wp-bannerize') ?>
  </strong>
</p>
<p>
  <label for="<?php echo esc_attr($widget->get_field_name('numbers')) ?>">
    <?php esc_attr_e('Numbers', 'wp-bannerize') ?>:
    <input type="number" placeholder="<?php esc_attr_e('Maximum number of Banners to display: use -1 to display all banners', 'wp-bannerize') ?>" size="3" min="-1" value="<?php echo esc_attr($instance['numbers']) ?>" id="<?php echo esc_attr($widget->get_field_name('numbers')) ?>" name="<?php echo esc_attr($widget->get_field_name('numbers')) ?>" />
  </label>
</p>

<p>
  <strong>
    <?php esc_attr_e('Warning! The "Order" param will ignored when RANDOM "Order by" is selected.', 'wp-bannerize') ?>
  </strong>
</p>
<p>
  <label for="<?php echo esc_attr($widget->get_field_name('orderby')) ?>">
    <?php esc_attr_e('Order By', 'wp-bannerize') ?>:
    <select id="<?php echo esc_attr($widget->get_field_name('orderby')) ?>" name="<?php echo esc_attr($widget->get_field_name('orderby')) ?>">
      <option <?php selected('menu_order', $instance['orderby']) ?> value="menu_order"><?php esc_attr_e('Manual', 'wp-bannerize') ?></option>
      <option <?php selected('random', $instance['orderby']) ?> value="random"><?php esc_attr_e('Random', 'wp-bannerize') ?></option>
      <option <?php selected('impressions', $instance['orderby']) ?> value="impressions"><?php esc_attr_e('Impressions', 'wp-bannerize') ?></option>
      <option <?php selected('clicks', $instance['orderby']) ?> value="clicks"><?php esc_attr_e('Clicks', 'wp-bannerize') ?></option>
      <option <?php selected('ctr', $instance['orderby']) ?> value="ctr"><?php esc_attr_e('CTR', 'wp-bannerize') ?></option>
    </select>
  </label>

  <label for="<?php echo esc_attr($widget->get_field_name('order')) ?>">
    <?php esc_attr_e('Order', 'wp-bannerize') ?>:
    <select id="<?php echo esc_attr($widget->get_field_name('order')) ?>" name="<?php echo esc_attr($widget->get_field_name('order')) ?>">
      <option <?php selected('DESC', $instance['order']) ?> value="DESC"><?php esc_attr_e('Descending', 'wp-bannerize') ?></option>
      <option <?php selected('ASC', $instance['order']) ?> value="ASC"><?php esc_attr_e('Ascending', 'wp-bannerize') ?></option>
    </select>
  </label>
</p>

<p>
  <strong>
    <?php esc_attr_e('Switch on to give some chances to the banners to be showed when use Impressions, Click or CTR order by. Switch off to absolute order.', 'wp-bannerize') ?>
  </strong>
</p>
<p>
  <label for="<?php echo esc_attr($widget->get_field_name('rank_seed')) ?>">
    <?php esc_attr_e('Rank', 'wp-bannerize') ?>:
    <input type="checkbox" <?php checked("true", $instance['rank_seed']) ?> value="<?php echo esc_attr($instance['rank_seed']) ?>" id="<?php echo esc_attr($widget->get_field_name('rank_seed')) ?>" name="<?php echo esc_attr($widget->get_field_name('rank_seed')) ?>" />
  </label>
</p>

<h2><?php esc_attr_e('Devices', 'wp-bannerize') ?></h2>

<div>
  <div>
    <label for="<?php echo esc_attr($widget->get_field_name('device')) ?>-any">
      <input type="radio" <?php checked('any', $instance['device']) ?> id="<?php echo esc_attr($widget->get_field_name('device')) ?>-any" value="any" name="<?php echo esc_attr($widget->get_field_name('device')) ?>" />
      <?php esc_attr_e('Any', 'wp-bannerize') ?>
    </label>
  </div>
  <div>
    <label for="<?php echo esc_attr($widget->get_field_name('device')) ?>-mobile">
      <input type="radio" <?php checked('mobile', $instance['device']) ?> id="<?php echo esc_attr($widget->get_field_name('device')) ?>-mobile" value="mobile" name="<?php echo esc_attr($widget->get_field_name('device')) ?>" />
      <?php esc_attr_e('Mobile', 'wp-bannerize') ?>
    </label>
  </div>

  <div>
    <label for="<?php echo esc_attr($widget->get_field_name('device')) ?>-desktop">
      <input type="radio" <?php checked('desktop', $instance['device']) ?> id="<?php echo esc_attr($widget->get_field_name('device')) ?>-desktop" value="desktop" name="<?php echo esc_attr($widget->get_field_name('device')) ?>" />
      <?php esc_attr_e('Desktop', 'wp-bannerize') ?>
    </label>
  </div>
</div>

<h2><?php esc_attr_e('Filters', 'wp-bannerize') ?></h2>

<p>
  <strong>
    Your banners will be visible only for the following filters.
  </strong>
</p>

<hr />

<h4>
  <?php esc_attr_e('Geo Localization', 'wp-bannerize') ?>
  <label style="float:right">
    <?php $displayGepSelectedId = 'wpxbz-dgs-' . uniqid(); ?>
    <input type="checkbox" id="<?php echo esc_attr($displayGepSelectedId) ?>" />
    <?php esc_attr_e('Display selected', 'wp-bannerize') ?>
  </label>
</h4>

<div class="wp-bannerize-scroll">

  <?php

  $countries = \WPBannerize\GeoLocalizer\GeoLocalizerProvider::countries();
  $displayGeoUlId   = 'wpxbz-geo-ul-' . uniqid();
  $deselectButtonId = 'wpxbz-geo-deselect-' . uniqid();
  ?>

  <ul id="<?php echo esc_attr($displayGeoUlId) ?>" class="wp-bannerize-ul-column"><?php
                                                                                  foreach (array_values($countries) as $country) : ?>

      <li>
        <label>
          <input name="<?php echo esc_attr($widget->get_field_name('geo_countries')) ?>[]" <?php if (!empty($instance['geo_countries'])) {
                                                                                              wpbones_checked($instance['geo_countries'], $country->country);
                                                                                            } ?> type="checkbox" value="<?php echo esc_attr($country->country) ?>" />
          <?php echo esc_attr($country->country) ?>
        </label>
      </li>

    <?php endforeach; ?>
  </ul>

</div>

<div class="clearfix" style="margin:8px 0 16px">
  <?php if (count($instance['geo_countries']) > 0) : ?>
    <span style="vertical-align:middle" class="left"><?php esc_attr_e('Selected countries:', 'wp-bannerize') ?><?php echo count($instance['geo_countries']) ?></span>
    <button id="<?php echo esc_attr($deselectButtonId) ?>" style="vertical-align:middle" class="button button-small button-primary right">
      <?php esc_attr_e('Deselect all', 'wp-bannerize') ?>
    </button>
  <?php endif; ?>
</div>

<hr />

<h4>
  <?php esc_attr_e('Bannerize Campaigns', 'wp-bannerize') ?>
</h4>

<div>

  <?php
  // Get all bannerize categories
  $args  = [
    'hide_empty' => true,
  ];
  $terms = get_terms('wp_bannerize_tax');

  ?>
  <ul class="wp-bannerize-ul-column"><?php
                                      foreach ($terms as $term) : ?>

      <li>
        <label>
          <input name="<?php echo esc_attr($widget->get_field_name('categories')) ?>[]" <?php if (!empty($instance['categories'])) {
                                                                                          wpbones_checked($instance['categories'], $term->term_id);
                                                                                        } ?> type="checkbox" value="<?php echo esc_attr($term->term_id) ?>" />
          <?php echo esc_attr($term->name) ?>
        </label>
      </li>

    <?php endforeach; ?>
  </ul>
</div>

<hr />

<h4>
  <?php esc_attr_e('Post Categories', 'wp-bannerize') ?>
</h4>

<div>

  <?php
  // All post categories list
  $all_categories = get_categories();

  ?>
  <ul class="wp-bannerize-ul-column"><?php
                                      foreach ($all_categories as $category) : ?>

      <li>
        <label>
          <input name="<?php echo esc_attr($widget->get_field_name('post_categories')) ?>[]" <?php if (!empty($instance['post_categories'])) {
                                                                                                wpbones_checked($instance['post_categories'], $category->cat_ID);
                                                                                              } ?> type="checkbox" value="<?php echo esc_attr($category->cat_ID) ?>" />
          <?php echo esc_attr($category->cat_name) ?>
        </label>
      </li>

    <?php endforeach; ?>
  </ul>
</div>

<hr />

<h4>
  <?php esc_attr_e('User Roles', 'wp-bannerize') ?>
</h4>

<div>
  <?php
  // Get all user roles
  $wpRoles = new WP_Roles;

  ?>
  <ul class="wp-bannerize-ul-column"><?php
                                      foreach ($wpRoles->roles as $role => $value) : ?>

      <li>
        <label title="<?php echo esc_attr($value['name']) ?>">
          <input name="<?php echo esc_attr($widget->get_field_name('user_roles')) ?>[]" <?php if (!empty($instance['user_roles'])) {
                                                                                          wpbones_checked($instance['user_roles'], $role);
                                                                                        } ?> type="checkbox" value="<?php echo esc_attr($role) ?>" />
          <?php echo esc_attr($value['name']) ?></label>
      </li>

    <?php endforeach; ?>
  </ul>

</div>

<script type="text/javascript">
  (function($) {
    $(document).on("click", '#<?php echo esc_attr($deselectButtonId) ?>',
      function(event) {
        event.preventDefault()

        $('#<?php echo esc_attr($displayGeoUlId) ?>')
          .find("input[type=\"checkbox\"]")
          .each(
            function(i) {
              $(this).attr("checked", false)
            }
          )
      }
    )

    $(document).on("change", '#<?php echo esc_attr($displayGepSelectedId) ?>',
      function(event) {
        if (event.target.checked) {

          $('#<?php echo esc_attr($displayGeoUlId) ?>')
            .find("input[type=\"checkbox\"]")
            .each(
              function(i) {
                if ($(this).is(":checked")) {
                  $(this).parent("label").show()
                } else {
                  $(this).parent("label").hide()
                }
              }
            )
        } else {
          $('#<?php echo esc_attr($displayGeoUlId) ?>')
            .find("input[type=\"checkbox\"]")
            .each(
              function(i) {
                $(this).parent("label").show()
              }
            )
        }
      }
    )

  }(window.jQuery))
</script>