<?php

/*
|--------------------------------------------------------------------------
| Plugin options
|--------------------------------------------------------------------------
|
| Here is where you can insert the options model of your plugin.
| These options model will store in WordPress options table
| (usually wp_options).
| You'll get these options by using `$plugin->options` property
|
*/

return [
    'General' => [
        'impressions_enabled' => true,
        'clicks_enabled' => true,
    ],
    'impressions' => [
        'enabled' => true,
        'keep_clean' => 'disabled', // 'delete_max_records_exceeded'  | 'retain_within_recent_months'
        'max_records' => 1000,
        'num_months' => 3,
        'schedules' => 'twicedaily',
    ],
    'clicks' => [
        'enabled' => true,
        'keep_clean' => 'disabled', // 'delete_max_records_exceeded'  | 'retain_within_recent_months'
        'max_records' => 1000,
        'num_months' => 3,
        'schedules' => 'twicedaily',
    ],
    'geolocalization' => [
        'ipstack' => [
            'api_key' => '',
        ],
    ],
    'Layout' => [
        'top' => 0,
        'right' => 0,
        'bottom' => 0,
        'left' => 0,
    ],
    'theme' => [
        'campaigns' => [
            'custom_template' => [
                'enabled' => false,
                'header' => true,
                'footer' => true,
                'sidebar' => true,
                'file' => 'custom-taxonomy-template.php',
            ],
            'custom_file' => ''
        ],
        'banner' => [
            'custom_template' => [
                'enabled' => false,
                'header' => true,
                'footer' => true,
                'sidebar' => true,
                'file' => 'custom-single-template.php',
            ],
            'custom_file' => ''
        ],
    ]
];
