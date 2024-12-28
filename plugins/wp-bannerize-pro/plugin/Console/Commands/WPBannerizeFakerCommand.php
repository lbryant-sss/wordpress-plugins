<?php

namespace WPBannerize\Console\Commands;

use WPBannerize\Models\WPBannerizeClicks;
use WPBannerize\Models\WPBannerizeImpressions;
use WPBannerize\Models\WPBannerizePost;
use WPBannerize\WPBones\Console\Command;

class WPBannerizeFakerCommand extends Command
{

    protected $signature = 'wpbannerize:fake {--clicks : Generate clicks as well}';

    protected $description = 'Generate random impressions/click';

    public function handle()
    {
        $date = time() - (60 * 60 * 24 * 30 * 3);
        $steps = [1, 5, 10, 15, 60, 120,];
        $ips = ['192.168.100.1', '192.168.100.2', '192.168.100.3', '192.168.100.4'];
        $banners = WPBannerizePost::all();
        $numbersClicksForDay = [10, 20, 30, 40, 50];

        for ($i = $date; $i < time(); $i += (60 * 60 * 24)) {

            shuffle($numbersClicksForDay);

            for ($j = 0; $j < $numbersClicksForDay[0]; $j++) {

                shuffle($ips);
                shuffle($banners);
                shuffle($steps);

                $dateFaker = $i + (60 * $steps[0]);

                WPBannerizeImpressions::create(
                    [
                        'banner_id' => $banners[0]->ID,
                        'date' => gmdate('Y-m-d H:i:s', $dateFaker),
                        'referrer' => 'http://wordpress.dev/?p=1',
                        'ip' => $ips[0],
                        'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:49.0) Gecko/20100101 Firefox/49.0'
                    ]
                );
                $this->line("Impressions $j on " . gmdate('Y-m-d H:i:s', $dateFaker));

                if ($this->options('clicks')) {

                    $randomClicks = wp_rand(-10, 10);

                    if ($randomClicks > 0) {

                        for ($k = 0; $k < $randomClicks; $k++) {

                            $dateFaker = $i + (60 * wp_rand(1, 60 * 60));

                            WPBannerizeClicks::create(
                                [
                                    'banner_id' => $banners[0]->ID,
                                    'date' => gmdate('Y-m-d H:i:s', $dateFaker),
                                    'referrer' => 'http://wordpress.dev/?p=1',
                                    'ip' => $ips[0],
                                    'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:49.0) Gecko/20100101 Firefox/49.0'
                                ]
                            );
                            $this->line("Click $j on " . gmdate('Y-m-d H:i:s', $dateFaker));
                        }
                    }
                }
            }
        }
    }
}
