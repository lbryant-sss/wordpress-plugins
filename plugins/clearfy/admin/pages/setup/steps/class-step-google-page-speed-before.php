<?php

namespace WBCR\Clearfy\Pages;

/**
 * Class Step_Google_Page_Speed_Before
 *
 * Represents a step in the process dedicated to analyzing and displaying
 * Google Page Speed results before optimizations.
 *
 * This class extends the Step_Custom page template and provides functionality
 * to fetch and display Google Page Speed Insights results for the website.
 * It renders a page with pre-analysis statistics for desktop and mobile scores,
 * along with detailed timing metrics such as First Contentful Paint, Speed Index,
 * and Time to Interactive. The data is retrieved via an AJAX request.
 *
 * @author  Alex Kovalev <alex.kovalevv@gmail.com> <Telegram:@alex_kovalevv>
 * @copyright (c) 23.07.2020, Webcraftic
 * @version 1.0
 */
class Step_Google_Page_Speed_Before extends \WBCR\Factory_Templates_134\Pages\Step_Custom
{

    protected $prev_id = 'step0';
    protected $id = 'step1';
    protected $next_id = 'step2';

    /**
     * Retrieves the title of the site.
     *
     * @return string The translated site title.
     */
    public function get_title(): string
    {
        return __("Site test #1", 'clearfy');
    }

    /**
     * Outputs the HTML and JavaScript for the Google Page Speed results interface.
     * This method includes a script to initiate fetching of Google Page Speed audits and displays desktop and mobile score results.
     * It also provides statistical insights and a link to view detailed results on Google PageSpeed Insights.
     *
     * @return void
     * @throws \Exception
     */
    public function html(): void
    {
        ?>
        <script>
            jQuery(document).ready(function ($) {
                wclearfy_fetch_google_pagespeed_audit("<?php echo wp_create_nonce('fetch_google_page_speed_audit') ?>");
            });
        </script>
        <div class="w-factory-templates-134-setup__inner-wrap">
            <h3><?php _e('Google Page Speed', 'clearfy') ?></h3>
            <p style="text-align: left;">
                <?php _e('We analyzed your site on the Google Page Speed service. You can see the test results below. Our plugin
				is to improve the score of your site on Google Page Speed. Memorize the results to make a comparison after
				optimization by the Clearfy plugin.', 'clearfy') ?>
            </p>
            <div class="wclearfy-gogle-page-speed-audit__errors"><?php _e('Memorize the results to make a comparison after
				optimization by the Clearfy plugin.', 'clearfy') ?>
            </div>
            <div class="wclearfy-gogle-page-speed-audit__preloader"></div>
            <div class="wclearfy-gogle-page-speed-audit" style="display: none;">
                <div class="wclearfy-score">
                    <!-- Desktop -->
                    <div class="wclearfy-desktop-score">
                        <h3><?php _e('Desktop score', 'clearfy'); ?></h3>
                        <div class="wclearfy-desktop-score__circle-wrap">
                            <div id="wclearfy-desktop-score__circle" class="wclearfy-score-circle"></div>
                        </div>
                    </div>

                    <!-- Mobile -->
                    <div class="wclearfy-mobile-score">
                        <h3><?php _e('Mobile score', 'clearfy'); ?></h3>
                        <div class="wclearfy-mobile-score__circle-wrap">
                            <div id="wclearfy-mobile-score__circle" class="wclearfy-score-circle"></div>
                        </div>
                    </div>
                </div>


                <!-- Statistics -->
                <div class="wclearfy-statistic">
                    <div class="wclearfy-statistic__line">
                        <span><?php _e('First Contentful Paint', 'clearfy'); ?></span>
                        <div class="wclearfy-statistic__results">
                            <span id="wclearfy-statistic__desktop-first-contentful-paint">??&nbsp;s</span>&nbsp;/&nbsp;<span
                                    id="wclearfy-statistic__mobile-first-contentful-paint">??&nbsp;s</span>
                        </div>
                    </div>
                    <div class="wclearfy-statistic__line">
                        <span><?php _e('Speed Index', 'clearfy'); ?></span>
                        <div class="wclearfy-statistic__results">
                            <span id="wclearfy-statistic__desktop-speed-index">??&nbsp;s</span>&nbsp;/&nbsp;<span
                                    id="wclearfy-statistic__mobile-speed-index">??&nbsp;s</span>
                        </div>
                    </div>
                    <div class="wclearfy-statistic__line">
                        <span><?php _e('Time to Interactive', 'clearfy'); ?></span>
                        <div class="wclearfy-statistic__results">
                            <span id="wclearfy-statistic__desktop-interactive">??&nbsp;s</span>&nbsp;/&nbsp;<span
                                    id="wclearfy-statistic__mobile-interactive">??&nbsp;s</span>
                        </div>
                    </div>

                    <?php
                    $site_url = get_home_url();
                    $google_page_speed_call = "https://developers.google.com/speed/pagespeed/insights/?url=" . esc_url($site_url);
                    ?>

                    <div style="margin-top: 5px;font-size:12px;">
                        <a href="<?php echo esc_url($google_page_speed_call); ?>" target="_blank"
                           style="outline: 0;text-decoration: none;"><?php _e('View complete results', 'clearfy'); ?></a> <?php _e('on Google PageSpeed Insights.', 'clearfy'); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php $this->render_button(); ?>
        <?php
    }
}