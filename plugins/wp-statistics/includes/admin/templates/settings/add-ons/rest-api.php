<?php

use WP_STATISTICS\Admin_Template;
use WP_Statistics\Components\View;
use WP_Statistics\Service\Admin\LicenseManagement\LicenseHelper;

$isLicenseValid  = LicenseHelper::isPluginLicenseValid('wp-statistics-rest-api');
$isRestApiActive = WP_STATISTICS\Helper::isAddOnActive('rest-api');
?>
    <h2 class="wps-settings-box__title"><span><?php esc_html_e('REST API', 'wp-statistics'); ?></span></h2>

<?php
if (!$isRestApiActive) echo Admin_Template::get_template('layout/partials/addon-premium-feature',
    ['addon_slug'                => esc_url(WP_STATISTICS_SITE_URL . '/add-ons/wp-statistics-rest-api/?utm_source=wp-statistics&utm_medium=link&utm_campaign=rest-api'),
     'addon_title'               => __('Rest API Add-on', 'wp-statistics'),
     'addon_modal_target'        => 'wp-statistics-rest-api',
     'addon_description'         => __('The settings on this page are part of the REST API add-on, which enables the following endpoints in the WordPress REST API:', 'wp-statistics'),
     'addon_features'            => [
         __('Browsers', 'wp-statistics'),
         __('Hits', 'wp-statistics'),
         __('Referrers', 'wp-statistics'),
         __('Search Engines', 'wp-statistics'),
         __('Summary', 'wp-statistics'),
         __('Visitors', 'wp-statistics'),
         __('Pages', 'wp-statistics'),
     ],
     'addon_info'                => __('For more information about the API and endpoints, please refer to the', 'wp-statistics'),
     'addon_documentation_title' => __('API documentation', 'wp-statistics'),
     'addon_documentation_slug'  => esc_url('https://documenter.getpostman.com/view/3239688/2s8Z6vZER4'),

    ], true);

if ($isRestApiActive && !$isLicenseValid) {
    View::load("components/lock-sections/notice-inactive-license-addon");
}
?>


    <div class="postbox">
        <table class="form-table <?php echo !$isRestApiActive ? 'form-table--preview' : '' ?>">
            <tbody>
            <tr class="wps-settings-box_head">
                <th scope="row" colspan="2"><h3><?php esc_html_e('WordPress REST API Integration', 'wp-statistics'); ?></h3></th>
            </tr>

            <tr data-id="api_service_status_tr">
                <th scope="row">
                    <span class="wps-setting-label"><?php esc_html_e('API Service Status', 'wp-statistics'); ?></span>
                </th>

                <td>
                    <input id="wps_addon_settings[rest_api][status]" name="wps_addon_settings[rest_api][status]" type="checkbox" value="1" <?php checked(WP_STATISTICS\Option::getByAddon('status', 'rest_api')) ?>>
                    <label for="wps_addon_settings[rest_api][status]"><?php esc_html_e('Enable', 'wp-statistics'); ?></label>
                    <p class="description"><?php _e(sprintf('Enable or disable WP Statistics API endpoints. For more information, visit the %1$s.', '<a href="https://documenter.getpostman.com/view/3239688/2s8Z6vZER4" target="_blank">API documentation</a>'), 'wp-statistics'); ?></p>
                </td>
            </tr>

            <tr data-id="authentication_token_tr">
                <th scope="row">
                    <label for="wps_addon_settings[rest_api][token_auth]"><?php esc_html_e('Authentication Token', 'wp-statistics'); ?></label>
                </th>

                <td>
                    <div class="wps-input-group wps-input-group__action">
                        <input type="text" name="wps_addon_settings[rest_api][token_auth]" id="wps_addon_settings[rest_api][token_auth]" class="regular-text wps-input-group__field" value="<?php echo esc_attr(WP_STATISTICS\Option::getByAddon('token_auth', 'rest_api')) ?>"/>
                        <button type="button" id="copy-text" class="button has-icon wps-input-group__label wps-input-group__copy"  style="margin: 0; "><?php esc_html_e('Copy', 'wp-statistics'); ?></button>
                    </div>
                    <p class="description"><?php esc_html_e('Enter your unique token here to secure and authorize API requests.', 'wp-statistics'); ?></p>
                </td>
            </tr>

            </tbody>
        </table>
    </div>

<?php
if ($isRestApiActive) {
    submit_button(__('Update', 'wp-statistics'), 'wps-button wps-button--primary', 'submit', '', array('OnClick' => "var wpsCurrentTab = getElementById('wps_current_tab'); wpsCurrentTab.value='rest-api-settings'"));
}
?>