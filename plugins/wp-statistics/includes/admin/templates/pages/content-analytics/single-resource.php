<?php
use WP_STATISTICS\Helper;
use WP_Statistics\Components\View;
?>

<div class="metabox-holder wps-content-analytics">
    <div class="postbox-container" id="wps-postbox-container-1">
        <?php

        $metrics = [
            [
                'label'  => esc_html__('Visitors', 'wp-statistics'),
                'value'  => Helper::formatNumberWithUnit($data['glance']['visitors']['value']),
                'change' => $data['glance']['visitors']['change']
            ],
            [
                'label'  => esc_html__('Views', 'wp-statistics'),
                'value'  => Helper::formatNumberWithUnit($data['glance']['views']['value']),
                'change' => $data['glance']['views']['change']
            ],
        ];
        View::load("components/objects/glance-card", ['metrics' => $metrics , 'two_column' => true]);

        $operatingSystems = [
            'title'     => esc_html__('Operating Systems', 'wp-statistics'),
            'tooltip'   => esc_html__('Distribution of visitors by their operating systems.', 'wp-statistics'),
            'unique_id' => 'content_operating_systems'
        ];
        View::load("components/charts/horizontal-bar", $operatingSystems);

        $browsers = [
            'title'     => esc_html__('Browsers', 'wp-statistics'),
            'tooltip'   => esc_html__('Distribution of visitors by their web browsers.', 'wp-statistics'),
            'unique_id' => 'content_browsers'
        ];
        View::load("components/charts/horizontal-bar", $browsers);

        $deviceModels = [
            'title'     => esc_html__('Device Models', 'wp-statistics'),
            'tooltip'   => esc_html__('Distribution of visitors by their device models.', 'wp-statistics'),
            'unique_id' => 'content_device_models'
        ];
        View::load("components/charts/horizontal-bar", $deviceModels);

        $deviceUsage = [
            'title'     => esc_html__('Device Usage', 'wp-statistics'),
            'tooltip'   => esc_html__('Distribution of visitors by their device types.', 'wp-statistics'),
            'unique_id' => 'content_device_usage'
        ];
        View::load("components/charts/horizontal-bar", $deviceUsage);
        ?>
    </div>

    <div class="postbox-container" id="wps-postbox-container-2">
        <?php
        $traffic = [
            'title'       => esc_html__('Traffic Trends', 'wp-statistics'),
            'type'        => 'single',
            'data'        => $data['performance']
        ];
        View::load("components/charts/performance", $traffic);

        $summary = [
            'title'   => esc_html__('Summary', 'wp-statistics'),
            'tooltip' => esc_html__('From today to last year, a breakdown of visitors and views.', 'wp-statistics'),
            'data'    => $data['visits_summary']
        ];
        View::load("components/tables/summary", $summary);

        $topCountries = [
            'tooltip' => esc_html__('The countries from which the most visitors are coming.', 'wp-statistics'),
            'data'    => $data['visitors_country']
        ];
        View::load("components/tables/top-countries", $topCountries);

        $engines = [
            'title'     => esc_html__('Search Engines', 'wp-statistics'),
            'tooltip'   => esc_html__('Search engine traffic over the selected period.', 'wp-statistics'),
            'unique_id' => 'content-search-engines-chart'
        ];
        View::load("components/charts/search-engines", $engines);

        $topReferring = [
            'tooltip' => esc_html__('The top referring domains.', 'wp-statistics'),
            'data'    => $data['referrers']
        ];
        View::load("components/tables/top-referring", $topReferring);
        ?>
    </div>

</div>