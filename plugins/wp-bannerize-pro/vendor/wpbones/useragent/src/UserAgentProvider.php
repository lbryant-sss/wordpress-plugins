<?php

namespace WPBannerize\UserAgent;

use Detection\MobileDetect;

class UserAgentProvider
{
  static $instance;

  public static function init()
  {
    if (!self::$instance) {
      self::$instance = new MobileDetect();
    }

    return self::$instance;
  }
}
