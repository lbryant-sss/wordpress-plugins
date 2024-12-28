<?php

/*
|--------------------------------------------------------------------------
| Plugin Menus routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the menu routes for a plugin.
| In this context the route are the menu link.
|
*/

return [
    'edit.php?post_type=wp_bannerize' => [
        "menu_title" => "WP Bannerize Settings",
        'capability' => 'manage_banners',
        'items' => [

            'overview' => [
                "menu_title" => __("Analytics", 'wp-bannerize'),
                'capability' => 'view_analytics',
                'route' => [
                    'get' => 'WPBannerizeAnalyticsController@index'
                ],
            ],

            'settings' => [
                "menu_title" => __("Settings"),
                'capability' => 'manage_banners',
                'route' => [
                    'get' => 'WPBannerizeSettingsController@index',
                    'post' => 'WPBannerizeSettingsController@update',
                ],
            ],
            'import' => get_option('wp_bannerize_old_table', false) ?
                [
                    "menu_title" => __("Import"),
                    'route' => [
                        'resource' => 'WPBannerizeImporterController',
                    ],
                ]
                : null,
        ]
    ]
];
