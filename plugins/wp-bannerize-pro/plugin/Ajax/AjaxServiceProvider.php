<?php

namespace WPBannerize\Ajax;

use WPBannerize\WPBones\Foundation\WordPressAjaxServiceProvider as ServiceProvider;

class AjaxServiceProvider extends ServiceProvider
{
  /**
   * The capability required to execute the action.
   * Of course, this is only for logged-in users.
   *
   * @var string
   */
  protected $capability = 'view_analytics';

  /**
   * The nonce key used to verify the request.
   *
   * @var string
   */
  protected $nonceKey = 'nonce';

  /**
   * The nonce hash used to verify the request.
   *
   * @var string
   */
  protected $nonceHash = 'wp-bannerize-pro';
}
