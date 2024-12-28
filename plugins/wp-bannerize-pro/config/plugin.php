<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log settings for your plugin.
    |
    | Available Settings: "single", "daily", "errorlog".
    |
    | Set to false or 'none' to stop logging.
    |
    */

    'log' => 'errorlog',

    'log_level' => 'debug',

    /*
    |--------------------------------------------------------------------------
    | Screen options
    |--------------------------------------------------------------------------
    |
    | Here is where you can register the screen options for List Table.
    |
    */

    'screen_options' => [],

    /*
    |--------------------------------------------------------------------------
    | Custom Post Types
    |--------------------------------------------------------------------------
    |
    | Here is where you can register the Custom Post Types.
    |
    */

    'custom_post_types' => ['\WPBannerize\CustomPostTypes\WPBannerizeCustomPostType'],

    /*
    |--------------------------------------------------------------------------
    | Custom Taxonomies
    |--------------------------------------------------------------------------
    |
    | Here is where you can register the Custom Taxonomy Types.
    |
    */

    'custom_taxonomy_types' => ['\WPBannerize\CustomTaxonomyTypes\WPBannerizeCustomTaxonomyType'],


    /*
    |--------------------------------------------------------------------------
    | Shortcodes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register the Shortcodes.
    |
    */

    'shortcodes' => ['\WPBannerize\Shortcodes\WPBannerizeShortcode'],

    /*
    |--------------------------------------------------------------------------
    | Widgets
    |--------------------------------------------------------------------------
    |
    | Here is where you can register all of the Widget for a plugin.
    |
    */

    'widgets' => ['\WPBannerize\Widgets\WPBannerizeWidget'],


    /*
    |--------------------------------------------------------------------------
    | Ajax
    |--------------------------------------------------------------------------
    |
    | Here is where you can register your own Ajax actions.
    |
    */

    'ajax' => [
        '\WPBannerize\Ajax\WPBannerizeAjax',
        '\WPBannerize\Ajax\WPBannerizeAnalyticsAjaxServiceProvider',
        '\WPBannerize\Ajax\OptionsAjaxServiceProvider',
        '\WPBannerize\Ajax\GeoAjaxServiceProvider',
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloader Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | init to your plugin. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [
        '\WPBannerize\Providers\WPBannerizeServiceProvider',
        '\WPBannerize\Providers\WPBannerizeFrontendServiceProvider'
    ]

];
