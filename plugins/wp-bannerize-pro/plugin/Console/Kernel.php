<?php

namespace WPBannerize\Console;

use WPBannerize\WPBones\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

  protected $commands = [
    'WPBannerize\Console\Commands\WPBannerizeFakerCommand',
  ];

}