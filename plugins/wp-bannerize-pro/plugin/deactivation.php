<?php

use WPBannerize\Providers\WPBannerizeRolesServiceProvider;

/*
|--------------------------------------------------------------------------
| Plugin deactivation
|--------------------------------------------------------------------------
|
| This file is included when the plugin is deactivated.
| Usually here you may enter a flush_rewrite_rules();
|
*/

WPBannerizeRolesServiceProvider::init()->deactivated();

flush_rewrite_rules();

