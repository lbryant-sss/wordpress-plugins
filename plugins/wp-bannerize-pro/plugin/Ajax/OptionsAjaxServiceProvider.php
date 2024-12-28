<?php

namespace WPBannerize\Ajax;

class OptionsAjaxServiceProvider extends AjaxServiceProvider
{

  /**
   * List of the ajax actions executed only by logged-in users.
   * Here you will use a methods list.
   *
   * @var array
   */
  protected $logged = ['wp_bannerize_get_options', 'wp_bannerize_update_options'];

  /**
   * Returns the options of the plugin
   *
   * @return void
   */
  public function wp_bannerize_get_options()
  {
    $options = WPBannerize()->options->toArray();
    wp_send_json($options);
  }

  /**
   * Update the options of the plugin
   *
   * @return void
   */
  public function wp_bannerize_update_options()
  {
    if (current_user_can('manage_banners')) {
      [$options] = $this->useHTTPPost('options');

      $object = json_decode(stripslashes($options), true);

      WPBannerize()->options->update($object);
      wp_send_json_success();
    }
    $this->permissionDenied();
  }
}
