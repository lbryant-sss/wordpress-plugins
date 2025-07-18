<?php

namespace DevOwl\RealCookieBanner\view;

use DevOwl\RealCookieBanner\Vendor\DevOwl\CookieConsentManagement\frontend\SavingConsentViaRestApiEndpointChecker;
use DevOwl\RealCookieBanner\Vendor\DevOwl\CookieConsentManagement\services\Service;
use DevOwl\RealCookieBanner\Vendor\DevOwl\Multilingual\Iso3166OneAlpha2;
use DevOwl\RealCookieBanner\base\UtilsProvider;
use DevOwl\RealCookieBanner\Core;
use DevOwl\RealCookieBanner\lite\settings\TcfVendorConfiguration;
use DevOwl\RealCookieBanner\scanner\Persist;
use DevOwl\RealCookieBanner\settings\Blocker;
use DevOwl\RealCookieBanner\settings\Consent;
use DevOwl\RealCookieBanner\settings\Cookie;
use DevOwl\RealCookieBanner\settings\CookieGroup;
use DevOwl\RealCookieBanner\settings\General;
use DevOwl\RealCookieBanner\settings\TCF;
use DevOwl\RealCookieBanner\templates\StorageHelper;
use DevOwl\RealCookieBanner\templates\TemplateConsumers;
use DevOwl\RealCookieBanner\Utils;
use DevOwl\RealCookieBanner\view\checklist\Scanner;
use DevOwl\RealCookieBanner\Vendor\MatthiasWeb\Utils\KeyValueMapOption;
use DevOwl\RealCookieBanner\Vendor\MatthiasWeb\Utils\Service as UtilsService;
use DevOwl\RealCookieBanner\Vendor\MatthiasWeb\Utils\Utils as UtilsUtils;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Notices management.
 * @internal
 */
class Notices
{
    use UtilsProvider;
    const OPTION_NAME = RCB_OPT_PREFIX . '-notice-states';
    const NOTICE_SCANNER_RERUN_AFTER_PLUGIN_TOGGLE = 'scanner-rerun-after-plugin-toggle';
    const NOTICE_GET_PRO_MAIN_BUTTON = 'get-pro-main-button';
    const NOTICE_GET_PRO_MAIN_BUTTON_NEXT = 60 * 60 * 24 * 30;
    // 30 days
    const NOTICE_REVISON_REQUEST_NEW_CONSENT_PREFIX = 'revision-request-new-consent-';
    const NOTICE_SERVICE_DATA_PROCESSING_IN_UNSAFE_COUNTRIES = 'service-data-processing-in-unsafe-countries';
    const NOTICE_USING_TEMPLATES_WHICH_GOT_DELETED = 'using-templates-which-got-deleted';
    const NOTICE_TCF_TOO_MUCH_VENDORS = 'tcf-too-much-vendors';
    const NOTICE_SERVICES_WITH_EMPTY_PRIVACY_POLICY = 'services-with-empty-privacy-policy';
    const NOTICE_SERVICES_WITH_UPDATED_TEMPLATES = 'services-with-updated-templates';
    const NOTICE_SERVICES_WITH_SUCCESSOR_TEMPLATES = 'services-with-successor-templates';
    const NOTICE_SERVICES_WITH_GOOGLE_CONSENT_MODE_ADJUSTMENTS = 'services-with-gcm-adjustments';
    const NOTICE_CHECK_SAVING_CONSENT_VIA_REST_API_ENDPOINT_WORKING = 'check-saving-consent-via-rest-api-endpoint-working-result';
    const NOTICE_SCANNER_EXPLICIT_EXTERNAL_URL_COVERAGE = 'scanner-explicit-external-url-coverage';
    const SCANNER_EXPLICIT_EXTERNAL_URL_COVERAGE_STATE_DISMISSED = \false;
    const SCANNER_EXPLICIT_EXTERNAL_URL_COVERAGE_STATE_SCANNED = 'scanned';
    const SCANNER_EXPLICIT_EXTERNAL_URL_COVERAGE_STATE_MANUAL_SCAN_REQUIRED = 'manual';
    const TCF_TOO_MUCH_VENDORS = 30;
    const CHECKLIST_PREFIX = 'checklist-';
    const MODAL_HINT_PREFIX = 'modal-hint-';
    const SCANNER_IGNORED_TEMPALTES = 'scanner-ignored-templates';
    const SCANNER_IGNORED_EXTERNAL_HOSTS = 'scanner-ignored-hosts';
    const DISMISS_SERVICES_WITH_UPDATED_TEMPLATES_NOTICE_QUERY_ARG = 'rcb-dismiss-upgrade-notice';
    const DISMISS_SERVICES_WITH_SUCCESSOR_TEMPLATES_NOTICE_QUERY_ARG = 'rcb-dismiss-successor-notice';
    const DISMISS_SERVICES_USING_GOOGLE_CONSENT_MODE_NO_CONSENT_TYPES = 'rcb-dismiss-gcm-no-consent-types';
    const DISMISS_SERVICES_USING_GOOGLE_CONSENT_MODE_NO_LEGITIMATE_INTEREST = 'rcb-dismiss-gcm-no-legitimate-interest';
    const DISMISS_SERVICES_REQUIRING_GOOGLE_CONSENT_MODE_ACTIVE = 'rcb-dismiss-gcm-required';
    const DISMISS_SCANNER_EXPLICIT_EXTERNAL_URL_COVERAGE_NOTICE_QUERY_ARG = 'rcb-dismiss-scanner-explicit-external-url-coverage-notice';
    const DISMISS_SCANNER_EXPLICIT_EXTERNAL_URL_COVERAGE_NOTICE_START_SCAN_QUERY_ARG = 'rcb-dismiss-scanner-explicit-external-url-coverage-notice-start-scan';
    const DISMISSED_SUCCESSORS = 'dismissed-successors';
    const DISMISSED_SERVICES_USING_GOOGLE_CONSENT_MODE_NO_CONSENT_TYPES = 'dismissed-services-using-gcm-and-no-consent-types';
    const DISMISSED_SERVICES_USING_GOOGLE_CONSENT_MODE_NO_LEGITIMATE_INTEREST = 'dismissed-services-using-gcm-and-no-legitimate-interest';
    const DISMISSED_SERVICES_REQUIRING_GOOGLE_CONSENT_MODE_ACTIVE = 'dismissed-services-requiring-gcm';
    const DISMISSED_BANNERLESS_CONSENT_LEGINT_SERVICES = 'dismissed-bannerless-consent-legint-services';
    const DISMISSED_BANNERLESS_CONSENT_SERVICES_WITHOUT_VISUAL_CONTENT_BLOCKER = 'dismissed-bannerless-consent-services-without-visual-content-blocker';
    private $states;
    /**
     * See `http_api_debug` method description.
     */
    private $nonBlockingRequestStarted = \false;
    /**
     * C'tor.
     *
     * @param Core $core
     */
    public function __construct($core)
    {
        $this->states = new KeyValueMapOption(self::OPTION_NAME, $core);
        $this->createStates();
    }
    /**
     * Create the notice states key-value option with migrations from older Real Cookie Banner versions.
     */
    protected function createStates()
    {
        $this->states->registerModifier(function ($key, $value) {
            return $key === self::NOTICE_GET_PRO_MAIN_BUTTON && $value === \true ? \time() + self::NOTICE_GET_PRO_MAIN_BUTTON_NEXT : $value;
        })->registerMigrationForKey(self::NOTICE_SCANNER_RERUN_AFTER_PLUGIN_TOGGLE, function () {
            $optionName = RCB_OPT_PREFIX . '-any-plugin-toggle-state';
            $result = \boolval(\get_option($optionName));
            \delete_option($optionName);
            return $result;
        })->registerMigrationForKey(self::NOTICE_GET_PRO_MAIN_BUTTON, function () {
            $optionName = RCB_OPT_PREFIX . '-config-next-pro-notice';
            $result = \get_option($optionName, \false);
            if ($result !== \false) {
                $result = \intval($result);
            }
            \delete_option($optionName);
            return $result;
        })->registerMigrationForKey(self::SCANNER_IGNORED_EXTERNAL_HOSTS, function () {
            global $wpdb;
            $table_name = $this->getTableName(Persist::TABLE_NAME);
            // phpcs:disable WordPress.DB.PreparedSQL
            $existingColumns = $wpdb->get_results("SHOW COLUMNS FROM {$table_name}", ARRAY_A);
            // phpcs:enable WordPress.DB.PreparedSQL
            $ignoredExternalHosts = [];
            // This column is only available in older versions of Real Cookie Banner
            foreach ($existingColumns as $existingColumn) {
                if (\in_array(\strtolower($existingColumn['Field']), ['ignored'], \true)) {
                    // phpcs:disable WordPress.DB.PreparedSQL
                    $ignoredExternalHosts = $wpdb->get_col("SELECT DISTINCT(blocked_url_host) FROM {$table_name} WHERE ignored = 1 AND preset = ''");
                    // phpcs:enable WordPress.DB.PreparedSQL
                    break;
                }
            }
            return $ignoredExternalHosts;
        })->registerMigration(function ($result) {
            global $wpdb;
            $table_name = $wpdb->options;
            // phpcs:disable WordPress.DB.PreparedSQL
            $checklistItems = $wpdb->get_col($wpdb->prepare("SELECT option_name FROM {$table_name} WHERE option_value = '1' AND option_name LIKE %s", RCB_OPT_PREFIX . '-checklist-%'));
            // phpcs:enable WordPress.DB.PreparedSQL
            foreach ($checklistItems as $checklistItem) {
                $name = \substr($checklistItem, \strlen(RCB_OPT_PREFIX . '-checklist-'));
                $result[self::CHECKLIST_PREFIX . $name] = \true;
            }
            // phpcs:disable WordPress.DB.PreparedSQL
            $checklistItems = $wpdb->query($wpdb->prepare("DELETE FROM {$table_name} WHERE option_name LIKE %s OR option_name LIKE %s", RCB_OPT_PREFIX . '-checklist-%', RCB_OPT_PREFIX . '-revision-dismissed-hash-%'));
            // phpcs:enable WordPress.DB.PreparedSQL
            return $result;
        })->registerMigration(function ($result) {
            $optionName = RCB_OPT_PREFIX . '-modal-hints';
            $modalHints = \json_decode(\get_option($optionName, '[]'), ARRAY_A);
            foreach ($modalHints as $modalHint) {
                $result[self::MODAL_HINT_PREFIX . $modalHint] = \true;
            }
            \delete_option($optionName);
            return $result;
        })->registerRestForKey(Core::MANAGE_MIN_CAPABILITY, \sprintf('/%s[a-z_-]+/', self::MODAL_HINT_PREFIX), ['type' => 'boolean'])->registerRestForKey(Core::MANAGE_MIN_CAPABILITY, self::NOTICE_SCANNER_RERUN_AFTER_PLUGIN_TOGGLE, ['type' => 'boolean'])->registerRestForKey(Core::MANAGE_MIN_CAPABILITY, self::NOTICE_TCF_TOO_MUCH_VENDORS, ['type' => 'boolean'])->registerRestForKey(Core::MANAGE_MIN_CAPABILITY, self::NOTICE_GET_PRO_MAIN_BUTTON, ['type' => 'boolean'])->registerRestForKey(Core::MANAGE_MIN_CAPABILITY, self::DISMISSED_BANNERLESS_CONSENT_SERVICES_WITHOUT_VISUAL_CONTENT_BLOCKER, ['type' => 'boolean', 'sanitize_callback' => function () {
            $dismissedServices = $this->getStates()->get(self::DISMISSED_BANNERLESS_CONSENT_SERVICES_WITHOUT_VISUAL_CONTENT_BLOCKER, []);
            $dismissedServices = \array_values(\array_unique(\array_merge($dismissedServices, \wp_list_pluck($this->servicesWithBannerlessConsentAdjustments()['legalBasisConsentWithoutVisualContentBlocker'], 'id'))));
            return $dismissedServices;
        }])->registerRestForKey(Core::MANAGE_MIN_CAPABILITY, self::DISMISSED_BANNERLESS_CONSENT_LEGINT_SERVICES, ['type' => 'boolean', 'sanitize_callback' => function () {
            $dismissedServices = $this->getStates()->get(self::DISMISSED_BANNERLESS_CONSENT_LEGINT_SERVICES, []);
            $dismissedServices = \array_values(\array_unique(\array_merge($dismissedServices, \wp_list_pluck($this->servicesWithBannerlessConsentAdjustments()['legalBasisLegitimateInterest'], 'id'))));
            return $dismissedServices;
        }])->registerRestForKey(Core::MANAGE_MIN_CAPABILITY, self::NOTICE_SERVICE_DATA_PROCESSING_IN_UNSAFE_COUNTRIES, ['type' => 'boolean'])->registerRestForKey(Core::MANAGE_MIN_CAPABILITY, self::NOTICE_USING_TEMPLATES_WHICH_GOT_DELETED, ['type' => 'boolean']);
        if (isset($_GET[self::NOTICE_CHECK_SAVING_CONSENT_VIA_REST_API_ENDPOINT_WORKING])) {
            $this->getStates()->set(self::NOTICE_CHECK_SAVING_CONSENT_VIA_REST_API_ENDPOINT_WORKING, \false);
        }
    }
    /**
     * We have mechanism to check if the WordPress REST API is working as expected. But when WordPress spawns the WP cron request
     * via `spawn_cron()` it starts a loopback request. That loopback request is non-blocking and when it takes longer than 20 seconds
     * (e.g. checking for updates of plugins, themes, ...) it leads to the following error for all follow-up loopback requests:
     *
     * > cURL error 28: Operation timed out after x milliseconds with 0 bytes received
     *
     * For this reason, we track all non-blocking requests and when a non-blocking request got started, we postpone the follow-up REST API check.
     *
     * @see https://app.clickup.com/t/8694nu6cm
     * @param array|WP_Error $response HTTP response or WP_Error object.
     * @param string $context Context under which the hook is fired.
     * @param string $class HTTP transport used.
     * @param array $args HTTP request arguments.
     * @param string $url The request URL.
     */
    public function http_api_debug($response, $context, $class, $args, $url)
    {
        if ($context === 'response' && isset($args['blocking']) && !$args['blocking']) {
            $parsed_url = \parse_url($url);
            $request_host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
            $site_host = \parse_url(\home_url(), \PHP_URL_HOST);
            if ($request_host === $site_host) {
                $this->nonBlockingRequestStarted = \true;
            }
        }
    }
    /**
     * When a plugin got toggled show scanner notice.
     *
     * @param string $plugin Plugin slug
     * @param bool $network_wide Is it activated network wide
     */
    public function anyPluginToggledState($plugin, $network_wide)
    {
        $isScannerChecked = \DevOwl\RealCookieBanner\view\Checklist::getInstance()->isChecked(Scanner::IDENTIFIER);
        if (!Utils::startsWith($plugin, RCB_SLUG) && $isScannerChecked) {
            if ($network_wide) {
                $network_blogs = \get_sites(['number' => 0, 'fields' => 'ids']);
                foreach ($network_blogs as $blog) {
                    $blogId = \intval($blog);
                    \switch_to_blog($blogId);
                    $this->getStates()->set(self::NOTICE_SCANNER_RERUN_AFTER_PLUGIN_TOGGLE, \true);
                    \restore_current_blog();
                }
            } else {
                $this->getStates()->set(self::NOTICE_SCANNER_RERUN_AFTER_PLUGIN_TOGGLE, \true);
            }
        }
        $this->getStates()->set(self::NOTICE_CHECK_SAVING_CONSENT_VIA_REST_API_ENDPOINT_WORKING, null);
    }
    /**
     * When the cookie banner gets enabled or disabled, do some checks again.
     */
    public function update_option_banner_active()
    {
        $this->getStates()->set(self::NOTICE_CHECK_SAVING_CONSENT_VIA_REST_API_ENDPOINT_WORKING, null);
    }
    /**
     * When a service / TCF vendor got updated check if the service is now processing data in unsafe countries.
     *
     * @param null|boolean $check
     * @param int $object_id
     * @param string $meta_key
     * @param mixed $meta_value
     */
    public function update_post_meta_data_processing_in_unsafe_countries($check, $object_id, $meta_key, $meta_value)
    {
        $postType = \get_post_type($object_id);
        if (\in_array($meta_key, [Cookie::META_NAME_DATA_PROCESSING_IN_COUNTRIES, Cookie::META_NAME_DATA_PROCESSING_IN_COUNTRIES_SPECIAL_TREATMENTS], \true) && ($postType === Cookie::CPT_NAME || $this->isPro() && $postType === TcfVendorConfiguration::CPT_NAME)) {
            $prev_value = \get_post_meta($object_id, Cookie::META_NAME_DATA_PROCESSING_IN_COUNTRIES, \true);
            if (empty($prev_value)) {
                return $check;
            }
            // Reset the notice at the end of the request, as we need to get special treatments meta, too
            \add_action('shutdown', function () use($meta_value, $prev_value, $object_id) {
                $currentCountries = UtilsUtils::isJson($meta_value, []);
                $oldCountries = UtilsUtils::isJson($prev_value, []);
                $specialTreatments = UtilsUtils::isJson(\get_post_meta($object_id, Cookie::META_NAME_DATA_PROCESSING_IN_COUNTRIES_SPECIAL_TREATMENTS, \true));
                $addedCountries = \array_values(\array_diff($currentCountries, $oldCountries));
                $addedUnsafeCountries = Service::calculateUnsafeCountries($addedCountries, $specialTreatments === \false ? [] : $specialTreatments);
                if (\count($addedUnsafeCountries) > 0) {
                    $this->getStates()->set(self::NOTICE_SERVICE_DATA_PROCESSING_IN_UNSAFE_COUNTRIES, \true);
                }
            });
        }
        return $check;
    }
    /**
     * When a service / TCF vendor got created check if the services does processing data in unsafe countries.
     *
     * @param int $meta_id
     * @param int $object_id
     * @param string $meta_key
     * @param mixed $meta_value
     */
    public function added_post_meta_data_processing_in_unsafe_countries($meta_id, $object_id, $meta_key, $meta_value)
    {
        $postType = \get_post_type($object_id);
        if (\in_array($meta_key, [Cookie::META_NAME_DATA_PROCESSING_IN_COUNTRIES, Cookie::META_NAME_DATA_PROCESSING_IN_COUNTRIES_SPECIAL_TREATMENTS], \true) && ($postType === Cookie::CPT_NAME || $this->isPro() && $postType === TcfVendorConfiguration::CPT_NAME)) {
            // Reset the notice at the end of the request, as we need to get special treatments meta, too
            \add_action('shutdown', function () use($meta_value, $object_id) {
                $currentCountries = UtilsUtils::isJson($meta_value, []);
                $specialTreatments = UtilsUtils::isJson(\get_post_meta($object_id, Cookie::META_NAME_DATA_PROCESSING_IN_COUNTRIES_SPECIAL_TREATMENTS, \true));
                $unsafeCountries = Service::calculateUnsafeCountries($currentCountries, $specialTreatments === \false ? [] : $specialTreatments);
                if (\count($unsafeCountries)) {
                    $this->getStates()->set(self::NOTICE_SERVICE_DATA_PROCESSING_IN_UNSAFE_COUNTRIES, \true);
                }
            });
        }
    }
    /**
     * Show a notice of services and content blockers with a template update.
     */
    public function admin_notice_services_with_updated_templates()
    {
        $needsUpdate = $this->servicesWithUpdatedTemplates();
        if (isset($_GET[self::DISMISS_SERVICES_WITH_UPDATED_TEMPLATES_NOTICE_QUERY_ARG])) {
            foreach ($needsUpdate as $update) {
                \update_post_meta($update->post_id, Blocker::META_NAME_PRESET_VERSION, $update->should);
            }
            $this->getStates()->set(self::NOTICE_SERVICES_WITH_UPDATED_TEMPLATES, []);
            return;
        }
        if (\current_user_can(Core::MANAGE_MIN_CAPABILITY) && \count($needsUpdate) > 0 && !Core::getInstance()->getConfigPage()->isVisible()) {
            echo \sprintf('<div class="notice notice-warning">%s</div>', $this->servicesWithUpdatedTemplatesHtml($needsUpdate));
        }
    }
    /**
     * Show a notice of services and content blockers with a successor template.
     */
    public function admin_notice_services_with_successor_templates()
    {
        $successors = $this->servicesWithSuccessorTemplates();
        if (isset($_GET[self::DISMISS_SERVICES_WITH_SUCCESSOR_TEMPLATES_NOTICE_QUERY_ARG])) {
            $dismissed = $this->getStates()->get(self::DISMISSED_SUCCESSORS, []);
            foreach ($successors as $successor) {
                $dismissed = \array_values(\array_merge($dismissed, \array_keys($successor)));
            }
            $this->getStates()->set(self::DISMISSED_SUCCESSORS, \array_unique($dismissed));
            $this->getStates()->set(self::NOTICE_SERVICES_WITH_SUCCESSOR_TEMPLATES, null);
            return;
        }
        if (\current_user_can(Core::MANAGE_MIN_CAPABILITY) && \count($successors) > 0 && !Core::getInstance()->getConfigPage()->isVisible()) {
            echo \sprintf('<div class="notice notice-warning">%s</div>', $this->servicesWithSuccessorTemplatesHtml($successors));
        }
    }
    /**
     * Show a notice of services with Google Consent Mode adjustments.
     */
    public function admin_notice_services_with_google_consent_mode_adjustments()
    {
        $adjustments = $this->servicesWithGoogleConsentModeAdjustments();
        $output = $this->servicesWithGoogleConsentModeAdjustmentsHtml($adjustments);
        $show = \current_user_can(Core::MANAGE_MIN_CAPABILITY) && !Core::getInstance()->getConfigPage()->isVisible();
        $resetCache = \false;
        if (isset($_GET[self::DISMISS_SERVICES_USING_GOOGLE_CONSENT_MODE_NO_CONSENT_TYPES])) {
            $this->getStates()->set(self::DISMISSED_SERVICES_USING_GOOGLE_CONSENT_MODE_NO_CONSENT_TYPES, \true);
            unset($output['noConsentTypes']);
            $resetCache = \true;
        }
        if (isset($_GET[self::DISMISS_SERVICES_USING_GOOGLE_CONSENT_MODE_NO_LEGITIMATE_INTEREST])) {
            $dismissed = $this->getStates()->get(self::DISMISSED_SERVICES_USING_GOOGLE_CONSENT_MODE_NO_LEGITIMATE_INTEREST, []);
            $dismissed = \array_values(\array_merge($dismissed, \array_column($adjustments['noLegitimateInterest'], 'id')));
            $this->getStates()->set(self::DISMISSED_SERVICES_USING_GOOGLE_CONSENT_MODE_NO_LEGITIMATE_INTEREST, \array_unique($dismissed));
            unset($output['noLegitimateInterest']);
            $resetCache = \true;
        }
        if (isset($_GET[self::DISMISS_SERVICES_REQUIRING_GOOGLE_CONSENT_MODE_ACTIVE]) && \in_array($_GET[self::DISMISS_SERVICES_REQUIRING_GOOGLE_CONSENT_MODE_ACTIVE], ['requiresGoogleConsentModeGa', 'requiresGoogleConsentModeAdsConversionTracking'], \true)) {
            $dismissed = $this->getStates()->get(self::DISMISSED_SERVICES_REQUIRING_GOOGLE_CONSENT_MODE_ACTIVE, []);
            $service = $_GET[self::DISMISS_SERVICES_REQUIRING_GOOGLE_CONSENT_MODE_ACTIVE];
            $dismissed = \array_values(\array_merge($dismissed, [$service]));
            $this->getStates()->set(self::DISMISSED_SERVICES_REQUIRING_GOOGLE_CONSENT_MODE_ACTIVE, \array_unique($dismissed));
            unset($output[$service]);
            $resetCache = \true;
        }
        if ($resetCache) {
            $this->getStates()->set(self::NOTICE_SERVICES_WITH_GOOGLE_CONSENT_MODE_ADJUSTMENTS, null);
        }
        foreach ($output as $key => $value) {
            if ($show && !\in_array($key, ['noConsentTypes', 'noLegitimateInterest', 'gtmAndGcmWithGtmActive'], \true)) {
                echo \sprintf('<div class="notice notice-warning">%s</div>', $value);
            }
        }
    }
    /**
     * Get the notice HTML for a check if saving of a consent via the REST API works as expected.
     */
    public function checkSavingConsentViaRestApiEndpointWorkingHtml()
    {
        if (!General::getInstance()->isBannerActive()) {
            return null;
        }
        $result = $this->getStates()->get(self::NOTICE_CHECK_SAVING_CONSENT_VIA_REST_API_ENDPOINT_WORKING, \false);
        $checker = new SavingConsentViaRestApiEndpointChecker();
        if ($checker->shouldInvalidate($result)) {
            // See https://github.com/WordPress/WordPress/blob/8fbd2fc6f40ea1f2ad746758b7111a66ab134e19/wp-admin/includes/class-wp-site-health.php#L2136-L2137
            $checker->setRequestArgument('sslverify', \apply_filters('https_local_ssl_verify', \false));
            $consentEndpoint = UtilsService::getNamespace($this) . '/consent';
            $url = \rest_url($consentEndpoint);
            if ($checker->start($url, $this->nonBlockingRequestStarted)) {
                $response = \wp_remote_post($url, $checker->getRequestArguments());
                if (\is_wp_error($response)) {
                    $checker->addError($url, [$response->get_error_message(), \sprintf(
                        // translators:
                        \__('There seems to be something generally wrong with the REST API of your WordPress instance. Please deactivate Real Cookie Banner and then check under <a href="%s">Tools > Site Health</a> whether errors regarding the REST API are listed there.', RCB_TD),
                        \admin_url('site-health.php')
                    )]);
                } else {
                    require_once ABSPATH . 'wp-admin/includes/file.php';
                    $htaccess = \get_home_path() . '.htaccess';
                    $hostname = \gethostname();
                    $checker->received($url, $response['body'], $response['headers']->getAll(), $response['response']['code'], ['htaccess' => \is_readable($htaccess) ? \file_get_contents($htaccess) : null, 'internalIps' => \is_string($hostname) ? \gethostbynamel($hostname) : \false]);
                }
            }
            $result = $checker->teardown();
            $this->getStates()->set(self::NOTICE_CHECK_SAVING_CONSENT_VIA_REST_API_ENDPOINT_WORKING, $result);
        }
        $tests = $result['tests'];
        if (\count(Utils::array_flatten($tests)) > 0) {
            $html = \sprintf('<p>%1$s</p>', \__('<strong>Consent cannot be saved in the cookie banner:</strong> Website visitors (not logged into WordPress) currently cannot save consents in the cookie banner. This may result in the cookie banner being displayed again each time the website visitor visits a page.', RCB_TD));
            foreach ($tests as $url => $errors) {
                $url = \base64_decode($url);
                $html .= \sprintf('<p>%s</p><ul style="list-style: initial;padding-inline-start:27px;"><li>%2$s</li></ul>', \sprintf(
                    // translators:
                    \__('The consent would be saved via the WP REST API route %s. The following problems with your WordPress installation were detected during the automatic error analysis:', RCB_TD),
                    \sprintf('<a style="word-break:break-all;" target="_blank" href="%s">%s</a>', \add_query_arg(\array_merge($checker->getRequestArguments()['body'], ['_method' => 'POST']), $url), $url)
                ), \join('</li><li>', \array_map(function ($error) {
                    if (\is_array($error)) {
                        $code = $error[0];
                        switch ($code) {
                            case SavingConsentViaRestApiEndpointChecker::ERROR_DIAGNOSTIC_ERROR_CODE:
                                return \sprintf(
                                    // translators:
                                    \__('HTTP error code <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/%1$d" target="_blank"><code>%1$d</code></a> has been returned by the server. This indicates that the <a href="%2$s" target="_blank">WP REST API may have been proactively blocked</a>.', RCB_TD),
                                    $error[1],
                                    \__('https://devowl.io/knowledge-base/wordpress-rest-api-does-not-respond/#how-can-i-enable-the-wordpress-rest-api-in-my-website', RCB_TD)
                                );
                            case SavingConsentViaRestApiEndpointChecker::ERROR_DIAGNOSTIC_RESPONSE_BODY:
                                return \sprintf(
                                    // translators:
                                    \__('Response from the server is malformed. This indicates, for example, that another plugin is manipulating the response. In the following, you find the server response:', RCB_TD),
                                    $error[1]
                                ) . \sprintf('<br /><code>%s</code>', \htmlentities($error[1]));
                            case SavingConsentViaRestApiEndpointChecker::ERROR_DIAGNOSTIC_NO_COOKIES:
                                return \sprintf(
                                    // translators:
                                    \__('Cookie with the saved consent (name starts with <code>real_cookie_banner</code>) was not part of the server response. Perhaps you have configured your <a href="%s" target="_blank">webserver to drop all cookies before sending the response</a>.', RCB_TD),
                                    \__('https://devowl.io/knowledge-base/cookie-banner-displayed-every-subpage/#cause-4-server-discards-all-cookies', RCB_TD)
                                );
                            case SavingConsentViaRestApiEndpointChecker::ERROR_DIAGNOSTIC_COOKIE_PATH:
                                return \sprintf(
                                    // translators:
                                    \__('Cookie <code>%1$s</code> with saved consent has been responded by server, but with the invalid cookie path <code>%2$s</code>. You may have an <a href="%3$s" target="_blank">error in your WordPress configuration file</a>.', RCB_TD),
                                    $error[1],
                                    $error[2],
                                    \__('https://devowl.io/knowledge-base/cookie-banner-displayed-every-subpage/#cause-2-incorrect-cookie-path', RCB_TD)
                                );
                            case SavingConsentViaRestApiEndpointChecker::ERROR_DIAGNOSTIC_COOKIE_HTTP_ONLY:
                                return \sprintf(
                                    // translators:
                                    \__('Cookie <code>%1$s</code> with saved consent has been responded to by the server, but with a <code>HttpOnly</code> flag, which means that the Real Cookie Banner JavaScript does not have permission to read it. You probably have <a href="%2$s" target="_blank">too strict security settings configured in your web server</a>.', RCB_TD),
                                    $error[1],
                                    \__('https://devowl.io/knowledge-base/cookie-banner-displayed-every-subpage/#cause-3-cookie-not-accessible-via-javascript', RCB_TD)
                                );
                            case SavingConsentViaRestApiEndpointChecker::ERROR_DIAGNOSTIC_REDIRECT:
                                return \sprintf(
                                    // translators:
                                    \__('Response of the server contains a <code>Location</code> header, which leads to a redirection of the save request instead of saving. You probably have an <a href="%2$s" target="_blank">incorrect trailing slash configuration in your web server</a>.', RCB_TD),
                                    $error[1],
                                    \__('https://devowl.io/knowledge-base/wordpress-rest-api-does-not-respond/#i-can-read-data-but-not-write', RCB_TD)
                                );
                            case SavingConsentViaRestApiEndpointChecker::ERROR_DIAGNOSTIC_403_FORBIDDEN_HTACCESS_DENY:
                                return \sprintf(
                                    // translators:
                                    \__('Since the request returned a <code>403</code> error, it is possible that the IP of your server has been blocked for internal loopback requests. We have checked the <code>.htaccess</code> file and found that the following line could be the trigger for this, which you should check and remove: <code>%s</code>.', RCB_TD),
                                    $error[1]
                                );
                            default:
                                return \json_encode($error);
                        }
                    } else {
                        return $error;
                    }
                }, $errors)));
            }
            $html .= \sprintf('<p>%s</p>', \sprintf(
                // translators:
                \__('We have <a href="%s" target="_blank">explained typical misconfigurations and solutions</a> for this issue in a detailed article. If you do not know how to solve this problem, please contact the technical contact person for your website!', RCB_TD),
                \__('https://devowl.io/knowledge-base/cookie-banner-displayed-every-subpage/', RCB_TD)
            ));
            $dismissLink = \add_query_arg(self::NOTICE_CHECK_SAVING_CONSENT_VIA_REST_API_ENDPOINT_WORKING, '1', UtilsUtils::isRest() ? Core::getInstance()->getConfigPage()->getUrl() : $_SERVER['REQUEST_URI']);
            $html .= '<p><a class="button button-primary" href="' . \esc_url($dismissLink) . '">' . \__('Check consent storage process again', RCB_TD) . '</a></p>';
            return $html;
        }
        return null;
    }
    /**
     * Get the notice HTML of services and content blockers with a template update.
     *
     * @param array $needsUpdate
     * @param string $context Can be `notice` or `tile-migration`
     */
    public function servicesWithUpdatedTemplatesHtml($needsUpdate, $context = 'notice')
    {
        $configPage = Core::getInstance()->getConfigPage();
        $compLanguage = Core::getInstance()->getCompLanguage();
        $configPageUrl = $configPage->getUrl();
        $output = $context === 'notice' ? '<p>' . \__('Changes have been made to the templates you use in Real Cookie Banner. You should review the proposed changes and adjust your services and/or content blockers if necessary to be able to remain legally compliant. The following services are affected:', RCB_TD) . '</p><ul>' : '<ul>';
        foreach ($needsUpdate as $update) {
            $postLanguage = $compLanguage->getPostLanguage($update->post_id);
            switch ($update->post_type) {
                case Blocker::CPT_NAME:
                    $typeLabel = \__('Content Blocker', RCB_TD);
                    $editLink = $configPageUrl . '#/blocker/edit/' . $update->post_id;
                    break;
                case Cookie::CPT_NAME:
                    $groupIds = \wp_get_post_terms($update->post_id, CookieGroup::TAXONOMY_NAME, ['fields' => 'ids']);
                    $typeLabel = \__('Service (Cookie)', RCB_TD);
                    $editLink = $configPageUrl . '#/cookies/' . $groupIds[0] . '/edit/' . $update->post_id;
                    break;
                default:
                    break;
            }
            $output .= \sprintf('<li><strong>%s</strong> (%s) &bull;%s <a href="%s">%s</a></li>', \esc_html($update->post_title), $typeLabel, !empty($postLanguage) ? \sprintf(' (%s) &bull;', $compLanguage->getTranslatedName($postLanguage)) : '', $editLink, \__('Review changes', RCB_TD));
        }
        $dismissLink = \add_query_arg(self::DISMISS_SERVICES_WITH_UPDATED_TEMPLATES_NOTICE_QUERY_ARG, '1', UtilsUtils::isRest() ? $configPageUrl : $_SERVER['REQUEST_URI']);
        $output .= $context === 'notice' ? '</ul><p><a href="' . \esc_url($dismissLink) . '">' . \__('Dismiss notice', RCB_TD) . '</a></p>' : '</ul>';
        return $output;
    }
    /**
     * Get the notice HTML of services and content blockers with a successor template.
     *
     * @param array $successors
     */
    public function servicesWithSuccessorTemplatesHtml($successors)
    {
        $configPage = Core::getInstance()->getConfigPage();
        $configPageUrl = $configPage->getUrl();
        $output = '<p>' . \__('Templates in Real Cookie Banner have been replaced by new templates. You should check whether you want to replace the new service and/or content blocker with the new one in order to remain legally compliant. The following services are affected:', RCB_TD) . '</p><ul>';
        foreach ($successors as $postType => $successor) {
            foreach ($successor as $successorIdentifier => $row) {
                switch ($postType) {
                    case Blocker::CPT_NAME:
                        $typeLabel = \__('Content Blocker', RCB_TD);
                        $replaceLink = $configPageUrl . '#/blocker/new?force=' . $successorIdentifier;
                        break;
                    case Cookie::CPT_NAME:
                        $typeLabel = \__('Service (Cookie)', RCB_TD);
                        $replaceLink = $configPageUrl . '#/cookies/' . CookieGroup::getInstance()->getEssentialGroupId() . '/new?force=' . $successorIdentifier;
                        break;
                    default:
                        break;
                }
                $output .= \sprintf('<li>%s: <strong>%s</strong> replaces %s &bull; <a href="%s">%s</a></li>', $typeLabel, $row['name'], UtilsUtils::joinWithAndSeparator(\array_map(function ($replaces) use($postType, $configPageUrl) {
                    $postId = $replaces[0];
                    $postTitle = $replaces[1];
                    switch ($postType) {
                        case Blocker::CPT_NAME:
                            $editLink = $configPageUrl . '#/blocker/edit/' . $postId;
                            break;
                        case Cookie::CPT_NAME:
                            $groupIds = \wp_get_post_terms($postId, CookieGroup::TAXONOMY_NAME, ['fields' => 'ids']);
                            $editLink = $configPageUrl . '#/cookies/' . $groupIds[0] . '/edit/' . $postId;
                            break;
                        default:
                            break;
                    }
                    return \sprintf('<a href="%s" target="_blank">%s</a>', $editLink, $postTitle);
                }, $row['replaces']), \__(' and ', RCB_TD)), $replaceLink, \__('Replace', RCB_TD));
            }
        }
        $dismissLink = \add_query_arg(self::DISMISS_SERVICES_WITH_SUCCESSOR_TEMPLATES_NOTICE_QUERY_ARG, '1', UtilsUtils::isRest() ? $configPageUrl : $_SERVER['REQUEST_URI']);
        $output .= '</ul><p><a href="' . \esc_url($dismissLink) . '">' . \__('Dismiss notice', RCB_TD) . '</a></p>';
        return $output;
    }
    /**
     * Get multiple notice HTMLs of services with Google Consent Mode adjustments.
     *
     * @param array $adjustments
     * @return string[]
     */
    public function servicesWithGoogleConsentModeAdjustmentsHtml($adjustments)
    {
        $configPage = Core::getInstance()->getConfigPage();
        $configPageUrl = UtilsUtils::isRest() ? $configPage->getUrl() : $_SERVER['REQUEST_URI'];
        $settingsPage = $configPageUrl . '#/settings/gcm';
        $activateGcmUrl = \sprintf('<a href="%s">%s</a>', $settingsPage, \__('Activate Google Consent Mode', RCB_TD));
        $learnMoreUrl = \sprintf('<a href="%s" target="_blank">%s</a>', \__('https://devowl.io/knowledge-base/real-cookie-banner-google-consent-mode-setup/', RCB_TD), \__('Learn more', RCB_TD));
        $result = [];
        if (\count($adjustments['missingConsentTypes']) > 0) {
            $output = '<p>' . \__('You are obtaining consent for Google services and have activated Google Consent Mode at the same time. However, you have not yet specified in the named services which consent types should be requested in accordance with Google Consent Mode. Please add these in the following services:', RCB_TD) . '</p><ul>';
            foreach ($adjustments['missingConsentTypes'] as $row) {
                $editLink = $configPageUrl . '#/cookies/' . $row['groupId'] . '/edit/' . $row['id'] . '?initiallyScrollToField=googleConsentModeConsentTypes';
                $output .= \sprintf('<li><strong>%s</strong> &bull; <a href="%s">%s</a></li>', \esc_html($row['title']), $editLink, \__('Edit service', RCB_TD));
            }
            $dismissLink = \add_query_arg(self::DISMISS_SERVICES_USING_GOOGLE_CONSENT_MODE_NO_CONSENT_TYPES, '1', $configPageUrl);
            $output .= '</ul><p><a href="' . \esc_url($dismissLink) . '">' . \__('Dismiss notice', RCB_TD) . '</a></p>';
            $result['noConsentTypes'] = $output;
        }
        if (\count($adjustments['noLegitimateInterest']) > 0) {
            $output = '<p>' . \__('You have activated Google Consent Mode and agreed to recommendations for the use of Google services without consent. At the same time, you only allow the following services to be loaded after obtaining consent. Please check whether you want to use the following services on your website based on a legitimate interest (even before Google receiving consent into consent types accordance with Google Consent Mode and therefore e.g. not setting advertising cookies):', RCB_TD) . '</p><ul>';
            foreach ($adjustments['noLegitimateInterest'] as $row) {
                $editLink = $configPageUrl . '#/cookies/' . $row['groupId'] . '/edit/' . $row['id'] . '?initiallyScrollToField=legalBasis';
                $output .= \sprintf('<li><strong>%s</strong> &bull; <a href="%s">%s</a></li>', \esc_html($row['title']), $editLink, \__('Edit service', RCB_TD));
            }
            $dismissLink = \add_query_arg(self::DISMISS_SERVICES_USING_GOOGLE_CONSENT_MODE_NO_LEGITIMATE_INTEREST, '1', $configPageUrl);
            $output .= '</ul><p><a href="' . \esc_url($dismissLink) . '">' . \__('Dismiss notice', RCB_TD) . '</a></p>';
            $result['noLegitimateInterest'] = $output;
        }
        if (isset($adjustments['requiresGoogleConsentModeGa'])) {
            $output = \sprintf('<p>' . \__('You obtain consent for <strong>Google Analytics 4</strong> using Real Cookie Banner. In order to continue to create audiences in Google Analytics 4, you must have configured Google Consent Mode in your cookie banner as of March 2024.', RCB_TD) . '</p><p>%s</p>', \join(' &bull; ', [$activateGcmUrl, \sprintf('<a href="%s">%s</a>', \add_query_arg(self::DISMISS_SERVICES_REQUIRING_GOOGLE_CONSENT_MODE_ACTIVE, 'requiresGoogleConsentModeGa', $configPageUrl), \__('I do not use "Audiences" in Google Analytics 4', RCB_TD)), $learnMoreUrl]));
            $result['requiresGoogleConsentModeGa'] = $output;
        }
        if (isset($adjustments['requiresGoogleConsentModeAdsConversionTracking'])) {
            $output = \sprintf('<p>' . \__('You obtain consent for <strong>Google Ads Conversion Tracking and Remarketing</strong> using Real Cookie Banner. From March 2024, conversions will only be processed by Google Ads if consent is obtained using Google Consent Mode. This data is also required in order to be able to continue to run remarketing campaigns in Google Ads.', RCB_TD) . '</p><p>%s</p>', \join(' &bull; ', [$activateGcmUrl, \sprintf('<a href="%s">%s</a>', \add_query_arg(self::DISMISS_SERVICES_REQUIRING_GOOGLE_CONSENT_MODE_ACTIVE, 'requiresGoogleConsentModeAdsConversionTracking', $configPageUrl), \__('Ignore', RCB_TD)), $learnMoreUrl]));
            $result['requiresGoogleConsentModeAdsConversionTracking'] = $output;
        }
        return $result;
    }
    /**
     * Read all services and content blockers which have an updated template version.
     *
     * @return array
     */
    public function servicesWithUpdatedTemplates()
    {
        global $wpdb;
        $noticeState = $this->getStates()->get(self::NOTICE_SERVICES_WITH_UPDATED_TEMPLATES, null);
        if (\is_array($noticeState)) {
            return $noticeState;
        }
        $table_name = $this->getTableName(StorageHelper::TABLE_NAME);
        // Probably refresh template cache
        $serviceConsumer = TemplateConsumers::getCurrentServiceConsumer();
        if ($serviceConsumer->getStorage()->shouldInvalidate()) {
            $serviceConsumer->retrieve();
        }
        $needsUpdate = $wpdb->get_results(
            // phpcs:disable WordPress.DB.PreparedSQL
            $wpdb->prepare("SELECT\n                    pm.meta_id AS post_version_meta_id,\n                    pm.post_id,\n                    pm.meta_value AS post_template_version,\n                    prid.meta_value AS post_template_identifier,\n                    p.post_title, p.post_type,\n                    templates.version as should\n                FROM {$wpdb->postmeta} pm\n                INNER JOIN {$wpdb->postmeta} prid\n                    ON prid.post_id = pm.post_id\n                INNER JOIN {$wpdb->posts} p\n                    ON p.ID = pm.post_id\n                INNER JOIN {$table_name} templates\n                    ON BINARY templates.identifier = BINARY prid.meta_value\n                    AND templates.context = %s\n                    AND templates.is_outdated = 0\n                    AND templates.type = (\n                        CASE\n                            WHEN p.post_type = %s THEN %s\n                            ELSE %s\n                        END\n                    )\n                WHERE pm.meta_key = %s\n                    AND pm.meta_value > 0\n                    AND prid.meta_key = %s\n                    AND p.post_type IN (%s, %s)\n                    AND p.post_status NOT IN ('trash')\n                    AND templates.version <> pm.meta_value", TemplateConsumers::getContext(), Cookie::CPT_NAME, StorageHelper::TYPE_SERVICE, StorageHelper::TYPE_BLOCKER, Blocker::META_NAME_PRESET_VERSION, Blocker::META_NAME_PRESET_ID, Blocker::CPT_NAME, Cookie::CPT_NAME)
        );
        // Remove rows of languages other than current and cast to correct types
        foreach ($needsUpdate as $key => &$row) {
            $row->post_version_meta_id = \intval($row->post_version_meta_id);
            $row->post_id = \intval($row->post_id);
            $row->post_template_version = \intval($row->post_template_version);
            $row->should = \intval($row->should);
            if (\intval(Core::getInstance()->getCompLanguage()->getCurrentPostId($row->post_id, $row->post_type)) !== \intval($row->post_id)) {
                unset($needsUpdate[$key]);
            }
        }
        $result = \array_values($needsUpdate);
        $this->getStates()->set(self::NOTICE_SERVICES_WITH_UPDATED_TEMPLATES, $result);
        return $result;
    }
    /**
     * Read all services and content blockers which have an successor.
     *
     * @return array
     */
    public function servicesWithSuccessorTemplates()
    {
        global $wpdb;
        $dismissed = $this->getStates()->get(self::DISMISSED_SUCCESSORS, []);
        $noticeState = $this->getStates()->get(self::NOTICE_SERVICES_WITH_SUCCESSOR_TEMPLATES, null);
        if (\is_array($noticeState)) {
            return $noticeState;
        }
        $table_name = $this->getTableName(StorageHelper::TABLE_NAME);
        // Probably refresh template cache
        $serviceConsumer = TemplateConsumers::getCurrentServiceConsumer();
        if ($serviceConsumer->getStorage()->shouldInvalidate()) {
            $serviceConsumer->retrieve();
        }
        $successors = $wpdb->get_results(
            // phpcs:disable WordPress.DB
            $wpdb->prepare("SELECT\n                    p.post_type,\n                    successors.identifier AS successor,\n                    CONCAT(successors.headline, CASE WHEN successors.sub_headline <> '' THEN CONCAT(' (', successors.sub_headline, ')') ELSE '' END) AS successor_name,\n                    pm.post_id AS replaces_id,\n                    p.post_title AS replaces_title,\n                    successors.successor_of_identifiers as successor_json\n                FROM {$wpdb->postmeta} pm\n                INNER JOIN {$wpdb->postmeta} prid\n                    ON prid.post_id = pm.post_id\n                INNER JOIN {$wpdb->posts} p\n                    ON p.ID = pm.post_id\n                INNER JOIN {$table_name} successors\n                    ON successors.context = %s\n                    AND successors.type = (\n                        CASE\n                            WHEN p.post_type = %s THEN %s\n                            ELSE %s\n                        END\n                    )\n                    AND successors.is_outdated = 0\n                    AND successors.successor_of_identifiers IS NOT NULL\n                WHERE pm.meta_key = %s\n                    AND pm.meta_value > 0\n                    AND prid.meta_key = %s\n                    AND p.post_type IN (%s, %s)\n                    AND p.post_status NOT IN ('trash')\n                    AND successors.successor_of_identifiers LIKE BINARY CONCAT('%\"', prid.meta_value, '\"%')", TemplateConsumers::getContext(), Cookie::CPT_NAME, StorageHelper::TYPE_SERVICE, StorageHelper::TYPE_BLOCKER, Blocker::META_NAME_PRESET_VERSION, Blocker::META_NAME_PRESET_ID, Blocker::CPT_NAME, Cookie::CPT_NAME)
        );
        // Map to a result grouped by post type (service, content blocker) and successor identifier
        $result = [];
        foreach ($successors as $key => $row) {
            if (\intval(Core::getInstance()->getCompLanguage()->getCurrentPostId($row->replaces_id, $row->post_type)) === \intval($row->replaces_id) && !\in_array($row->successor, $dismissed, \true)) {
                $result[$row->post_type] = $result[$row->post_type] ?? [];
                $result[$row->post_type][$row->successor] = $result[$row->post_type][$row->successor] ?? ['name' => $row->successor_name, 'replaces' => []];
                $result[$row->post_type][$row->successor]['replaces'][] = [$row->replaces_id, $row->replaces_title];
            }
        }
        $this->getStates()->set(self::NOTICE_SERVICES_WITH_SUCCESSOR_TEMPLATES, $result);
        return $result;
    }
    /**
     * Read all services and calculate adjustments which needs to be done when bannerless consent is used.
     * This includes services which are using bannerless consent for legitimate interest and services without
     * visual content blockers.
     *
     * @return array
     */
    public function servicesWithBannerlessConsentAdjustments()
    {
        $dismissedLegInt = $this->getStates()->get(self::DISMISSED_BANNERLESS_CONSENT_LEGINT_SERVICES, []);
        $dismissedConsentWithoutVisualContentBlocker = $this->getStates()->get(self::DISMISSED_BANNERLESS_CONSENT_SERVICES_WITHOUT_VISUAL_CONTENT_BLOCKER, []);
        $checks = Consent::getInstance()->calculateBannerlessConsentChecks();
        // Remove services which are dismissed
        foreach ($checks['legalBasisLegitimateInterest'] as $key => $check) {
            if (\in_array($check['id'], $dismissedLegInt, \true)) {
                unset($checks['legalBasisLegitimateInterest'][$key]);
            }
        }
        foreach ($checks['legalBasisConsentWithoutVisualContentBlocker'] as $key => $check) {
            if (\in_array($check['id'], $dismissedConsentWithoutVisualContentBlocker, \true)) {
                unset($checks['legalBasisConsentWithoutVisualContentBlocker'][$key]);
            }
        }
        return $checks;
    }
    /**
     * Read all services and calculate adjustments which needs to be done when Google Consent Mode is active
     * or needs to be activated.
     *
     * @return array
     */
    public function servicesWithGoogleConsentModeAdjustments()
    {
        $dismissedNoConsentTypes = $this->getStates()->get(self::DISMISSED_SERVICES_USING_GOOGLE_CONSENT_MODE_NO_CONSENT_TYPES, \false);
        $dismissedNoLegitimateInterest = $this->getStates()->get(self::DISMISSED_SERVICES_USING_GOOGLE_CONSENT_MODE_NO_LEGITIMATE_INTEREST, []);
        $dismissedRequiringGcmActive = $this->getStates()->get(self::DISMISSED_SERVICES_REQUIRING_GOOGLE_CONSENT_MODE_ACTIVE, []);
        $noticeState = $this->getStates()->get(self::NOTICE_SERVICES_WITH_GOOGLE_CONSENT_MODE_ADJUSTMENTS, null);
        if (\is_array($noticeState)) {
            return $noticeState;
        }
        $result = Core::getInstance()->getCookieConsentManagement()->getSettings()->getGoogleConsentMode()->calculateRecommandations($dismissedNoConsentTypes, $dismissedNoLegitimateInterest, $dismissedRequiringGcmActive);
        $this->getStates()->set(self::NOTICE_SERVICES_WITH_GOOGLE_CONSENT_MODE_ADJUSTMENTS, $result);
        return $result;
    }
    /**
     * Create an admin notice for services without privacy policy set.
     */
    public function admin_notices_services_with_empty_privacy_policy()
    {
        if (!\current_user_can(Core::MANAGE_MIN_CAPABILITY) || Core::getInstance()->getConfigPage()->isVisible()) {
            return;
        }
        $html = $this->serviceWithEmptyPrivacyPolicyNoticeHtml();
        if (\is_string($html)) {
            echo \sprintf('<div class="notice notice-warning">%s</div>', $html);
        }
    }
    /**
     * Create an admin notice for scanner external URLs which are now covered by newly published service templates.
     */
    public function admin_notices_scanner_explicit_external_url_coverage()
    {
        global $wpdb;
        if (isset($_GET[self::DISMISS_SCANNER_EXPLICIT_EXTERNAL_URL_COVERAGE_NOTICE_QUERY_ARG])) {
            $this->getStates()->set(self::NOTICE_SCANNER_EXPLICIT_EXTERNAL_URL_COVERAGE, \array_fill_keys(\array_keys($this->getStates()->get(self::NOTICE_SCANNER_EXPLICIT_EXTERNAL_URL_COVERAGE, [])), self::SCANNER_EXPLICIT_EXTERNAL_URL_COVERAGE_STATE_DISMISSED));
            return;
        }
        if (isset($_GET[self::DISMISS_SCANNER_EXPLICIT_EXTERNAL_URL_COVERAGE_NOTICE_START_SCAN_QUERY_ARG])) {
            $dismissed = $this->dismissScannerExplicitExternalUrlCoverageNotice(self::SCANNER_EXPLICIT_EXTERNAL_URL_COVERAGE_STATE_MANUAL_SCAN_REQUIRED);
            $table_name = $this->getTableName(Persist::TABLE_NAME);
            // phpcs:disable WordPress.DB
            $sourceUrls = $wpdb->get_col(\sprintf("SELECT DISTINCT source_url_hash, source_url FROM {$table_name} WHERE blocked_url_host IN (%s)", \join(',', \array_map(function ($host) use($wpdb) {
                return $wpdb->prepare('%s', $host);
            }, $dismissed))), 1, ARRAY_A);
            // phpcs:enable WordPress.DB
            if (\count($sourceUrls) > 0) {
                Core::getInstance()->getScanner()->addUrlsToQueue($sourceUrls);
            }
            return;
        }
        if (!\current_user_can(Core::MANAGE_MIN_CAPABILITY) || Core::getInstance()->getConfigPage()->isVisible()) {
            return;
        }
        $html = $this->scannerExplicitExternalUrlCoverageNoticeHtml();
        if (\is_string($html)) {
            echo \sprintf('<div class="notice notice-warning">%s</div>', $html);
        }
    }
    /**
     * Create an admin notice for the REST API check if consents can be saved.
     */
    public function admin_notices_check_saving_consent_via_rest_api_endpoint_working()
    {
        if (Core::getInstance()->getConfigPage()->isVisible() || !\current_user_can(Core::MANAGE_MIN_CAPABILITY)) {
            return;
        }
        $html = $this->checkSavingConsentViaRestApiEndpointWorkingHtml();
        if (\is_string($html) && !empty($html)) {
            echo \sprintf('<div class="notice notice-error">%s</div>', $html);
        }
    }
    /**
     * Create an admin notice for services without privacy policy set and return the HTML.
     */
    public function serviceWithEmptyPrivacyPolicyNoticeHtml()
    {
        global $typenow;
        $url = Core::getInstance()->getConfigPage()->getUrl();
        $noticeState = $this->getStates()->get(self::NOTICE_SERVICES_WITH_EMPTY_PRIVACY_POLICY, null);
        if (!\is_array($noticeState)) {
            // Divi & Elementor Pro overrides the meta query in a wrong way, but instead of contacting them (slow response)
            // we just reset the post type which gets read by their filters. Learn more about it here: https://app.clickup.com/t/2jzg30c
            $oldTypeNow = $typenow;
            $oldWpQueryVarTypeNow = \get_query_var('post_type');
            $typenow = Cookie::CPT_NAME;
            \set_query_var('post_type', Cookie::CPT_NAME);
            $servicesWithoutPrivacyPolicy = \get_posts(Core::getInstance()->queryArguments(['post_type' => Cookie::CPT_NAME, 'fields' => 'ids', 'numberposts' => -1, 'nopaging' => \true, 'meta_query' => ['relation' => 'OR', ['key' => Cookie::META_NAME_PROVIDER_PRIVACY_POLICY_URL, 'value' => '', 'compare' => '='], ['key' => Cookie::META_NAME_PROVIDER_PRIVACY_POLICY_URL, 'value' => '', 'compare' => 'NOT EXISTS']], 'post_status' => ['publish', 'private', 'draft']], 'ConfigPage::admin_notices_services_with_empty_privacy_policy'));
            $typenow = $oldTypeNow;
            \set_query_var('post_type', $oldWpQueryVarTypeNow);
            if (\count($servicesWithoutPrivacyPolicy) === 0) {
                return;
            }
            $noticeState = [];
            foreach ($servicesWithoutPrivacyPolicy as $serviceId) {
                $service = \get_post($serviceId);
                // Does the service still exist?
                if (\is_wp_error($service)) {
                    continue;
                }
                $cookieGroup = \get_the_terms($service, CookieGroup::TAXONOMY_NAME)[0] ?? null;
                $isProviderCurrentWebsite = \get_post_meta($serviceId, Cookie::META_NAME_IS_PROVIDER_CURRENT_WEBSITE, \true);
                if ($cookieGroup === null || $service === null || $isProviderCurrentWebsite) {
                    continue;
                }
                $noticeState[] = ['id' => $serviceId, 'groupId' => $cookieGroup->term_id, 'title' => $service->post_title];
            }
            $this->getStates()->set(self::NOTICE_SERVICES_WITH_EMPTY_PRIVACY_POLICY, $noticeState);
        }
        if (\count($noticeState) > 0) {
            return \sprintf('<p>%s</p><ul>%s</ul>', \__('There are no privacy policies with further information linked for the following services in your cookie banner. We now consider these to be mandatory in order to comply with the information obligations under the GDPR. Please provide a privacy policy for each service!', RCB_TD), \join('', \array_map(function ($row) use($url) {
                return \sprintf('<li data-id="%d">%s &bull; <a href="%s">%s</a></li>', $row['id'], $row['title'], \esc_attr($url . '#/cookies/' . $row['groupId'] . '/edit/' . $row['id']), \__('Set privacy policy URL', RCB_TD));
            }, $noticeState)));
        }
        return null;
    }
    /**
     * Create an admin notice for scanner external URLs which are now covered by newly published service templates.
     */
    public function scannerExplicitExternalUrlCoverageNoticeHtml()
    {
        global $wpdb;
        $noticeState = $this->getStates()->get(self::NOTICE_SCANNER_EXPLICIT_EXTERNAL_URL_COVERAGE, []);
        $active = \array_filter($noticeState, function ($state) {
            return $state !== self::SCANNER_EXPLICIT_EXTERNAL_URL_COVERAGE_STATE_DISMISSED;
        });
        // Short circuit
        if (\count($active) === 0) {
            return null;
        }
        // Read the current scanner state
        $table_name = $this->getTableName(Persist::TABLE_NAME);
        // phpcs:disable WordPress.DB.PreparedSQL
        $existing = $wpdb->get_results(\sprintf("SELECT blocked_url_host, IFNULL(GROUP_CONCAT(DISTINCT IF(preset <> '', preset, NULL) ORDER BY preset), '') AS templates\n                FROM {$table_name} WHERE blocked_url_host IN (%s) GROUP BY blocked_url_host", \join(',', \array_map(function ($host) use($wpdb) {
            return $wpdb->prepare('%s', $host);
        }, \array_keys($active)))), ARRAY_A);
        // phpcs:enable WordPress.DB.PreparedSQL
        $existing = \array_column($existing, 'templates', 'blocked_url_host');
        $existing = \array_map(function ($templates) {
            return empty($templates) ? [] : \explode(',', $templates);
        }, $existing);
        // Get the template instances and overwrite them with the string values
        $identifiers = Utils::array_flatten(\array_values($existing));
        $templates = \array_column(TemplateConsumers::getCurrentBlockerConsumer()->retrieveBy('identifier', $identifiers), null, 'identifier');
        $existing = \array_map(function ($existingTemplates) use($templates) {
            return \array_filter(\array_map(function ($template) use($templates) {
                return $templates[$template] ?? null;
            }, $existingTemplates));
        }, $existing);
        $noLongerExisting = \array_diff_key($active, $existing);
        // Update the state when there are external URLs hosts which do no longer exist
        if (\count($noLongerExisting) > 0) {
            foreach ($noLongerExisting as $host => $templates) {
                unset($active[$host]);
                unset($noticeState[$host]);
            }
            $this->getStates()->set(self::NOTICE_SCANNER_EXPLICIT_EXTERNAL_URL_COVERAGE, $active);
        }
        // Check if for already scanned elements there is already a template found
        foreach ($active as $host => $state) {
            $existingTemplates = $existing[$host] ?? [];
            if ($state === self::SCANNER_EXPLICIT_EXTERNAL_URL_COVERAGE_STATE_SCANNED && \count($existingTemplates) === 0) {
                unset($active[$host]);
            }
        }
        // Short circuit
        if (\count($active) === 0) {
            return null;
        }
        $resultScanned = Utils::array_flatten(\array_values(\array_filter(\array_map(function ($host) use($existing) {
            $templates = \wp_list_pluck($existing[$host], 'headline');
            return \count($templates) > 0 ? $templates : null;
        }, \array_keys($existing)))));
        $resultManualTrigger = Utils::array_flatten(\array_values(\array_filter(\array_map(function ($host) use($existing) {
            $templates = \wp_list_pluck($existing[$host], 'headline');
            return \count($templates) > 0 ? null : $host;
        }, \array_keys($existing)))));
        $resultScannedJoined = UtilsUtils::joinWithAndSeparator($resultScanned, \__(' and ', RCB_TD));
        $resultManualTriggerJoined = UtilsUtils::joinWithAndSeparator($resultManualTrigger, \__(' and ', RCB_TD));
        $configPageUrl = Core::getInstance()->getConfigPage()->getUrl();
        $startScannerLink = \add_query_arg(self::DISMISS_SCANNER_EXPLICIT_EXTERNAL_URL_COVERAGE_NOTICE_START_SCAN_QUERY_ARG, '1', $configPageUrl . '#/scanner');
        $dismissLink = \add_query_arg(self::DISMISS_SCANNER_EXPLICIT_EXTERNAL_URL_COVERAGE_NOTICE_QUERY_ARG, '1', UtilsUtils::isRest() ? $configPageUrl : $_SERVER['REQUEST_URI']);
        switch (\true) {
            case \count($resultManualTrigger) > 0 && \count($resultScanned) > 0:
                $text = \sprintf(
                    // translators:
                    \__('Real Cookie Banner offers new service templates for <strong>%1$s</strong> and for handling external URLs <strong>%2$s</strong>, which may be of interest to you.', RCB_TD),
                    $resultScannedJoined,
                    $resultManualTriggerJoined
                );
                break;
            case \count($resultScanned) > 0:
                $text = \sprintf(
                    // translators:
                    \__('Real Cookie Banner offers new service templates for <strong>%s</strong> that may be of interest to you.', RCB_TD),
                    $resultScannedJoined
                );
                break;
            case \count($resultManualTrigger) > 0:
                $text = \sprintf(
                    // translators:
                    \__('Real Cookie Banner offers new service templates for handling external URLs <strong>%s</strong>, which may be of interest to you.', RCB_TD),
                    $resultManualTriggerJoined
                );
                break;
            default:
                $text = '';
        }
        return \sprintf('<p>%s</p><p>%s</p>', \join(' ', [$text, \count($resultManualTrigger) > 0 ? \sprintf(\__('Start the scanner to check if the service template can be used for your case and will be suggested in the scanner!', RCB_TD)) : '']), \join('&nbsp;&bull;&nbsp;', \array_filter([\count($resultManualTrigger) > 0 ? \sprintf('<a href="%s">%s</a>', \esc_url($startScannerLink), \__('Start scanner', RCB_TD)) : '', \sprintf('<a href="%s">%s</a>', \esc_url($configPageUrl . '#/scanner'), \__('Open scanner', RCB_TD)), \sprintf('<a href="%s">%s</a>', \esc_url($dismissLink), \__('Dismiss notice', RCB_TD))])));
    }
    /**
     * Checks if the notice about services which are processing data in unsafe countries should be shown,
     * and returns the titles of the services which process data in unsafe countries
     */
    public function servicesDataProcessingInUnsafeCountriesNoticeHtml()
    {
        $noticeState = $this->getStates()->get(self::NOTICE_SERVICE_DATA_PROCESSING_IN_UNSAFE_COUNTRIES, null);
        // Should it be recalculated by metadata change or should it be initially checked?
        if (($noticeState === \true || $noticeState === null) && !Consent::getInstance()->isDataProcessingInUnsafeCountries()) {
            $iso3166OneAlpha2 = Iso3166OneAlpha2::getSortedCodes();
            $servicesHtml = [];
            foreach (Core::getInstance()->getCookieConsentManagement()->getSettings()->getConsent()->calculateServicesWithDataProcessingInUnsafeCountries() as $candidate) {
                $servicesHtml[] = \sprintf(
                    // translators:s
                    \__('<strong>%1$s</strong> is processing data to %2$s', RCB_TD),
                    \esc_html($candidate['name']),
                    \join(', ', \array_map(function ($country) use($iso3166OneAlpha2) {
                        return $iso3166OneAlpha2[$country] ?? $country;
                    }, $candidate['unsafeCountries']))
                );
            }
            if (\count($servicesHtml) > 0) {
                return \sprintf('<p>%s</p><ul><li>%s</li></ul>', \__('Some services perform data processing in unsafe third countries within the meaning of data protection regulations.  In the service configurations, you have stated that no security mechanisms are in place for this data processing. You should obtain special consent for this or only use services with data processing in secure countries or with appropriate security mechanisms.', RCB_TD), \join('</li><li>', $servicesHtml));
            }
        }
        return null;
    }
    /**
     * Checks if the notice about service and blocker templates which got deleted should be shown,
     * and returns the titles of the services with some special instructions.
     */
    public function admin_notice_service_using_template_which_got_deleted()
    {
        $state = $this->getStates()->get(self::NOTICE_USING_TEMPLATES_WHICH_GOT_DELETED, \true);
        if ($state) {
            $posts = \get_posts(Core::getInstance()->queryArguments(['post_type' => [Cookie::CPT_NAME, Blocker::CPT_NAME], 'fields' => 'ids', 'numberposts' => -1, 'nopaging' => \true, 'meta_query' => [['key' => Blocker::META_NAME_PRESET_ID, 'compare' => 'IN', 'value' => ['google-adsense']]], 'post_status' => ['publish', 'private', 'draft']], 'admin_notice_service_using_template_which_got_deleted'));
            if (\count($posts) > 0) {
                echo \sprintf('<div class="notice notice-warning" style="position:relative"><p>%s</p><p>%s</p><p><a target="_blank" href="%s">%s</a></p>%s</div>', \__('<strong>[ACTION REQUIRED]</strong> As of January 16, 2024, Google AdSense will only display advertising on your website if you obtain consent in accordance with the TCF standard. You must act now to continue earning advertising revenue!', RCB_TD), \__('You are currently obtaining non-standard compliant consent for Google Adsense via Real Cookie Banner. Please delete the Google AdSense service and Google AdSense content blocker in the Real Cookie Banner settings and then configure the TCF integration.', RCB_TD), \__('https://devowl.io/knowledge-base/google-adsense-tcf-consent-wordpress/', RCB_TD), \__('Read instructions for Google AdSense configuration with TCF consents', RCB_TD), \sprintf('<button type="button" class="notice-dismiss" onClick="%s"></button>', \esc_js($this->getStates()->noticeDismissOnClickHandler(self::NOTICE_USING_TEMPLATES_WHICH_GOT_DELETED, 'false'))));
            } else {
                $this->getStates()->set(self::NOTICE_USING_TEMPLATES_WHICH_GOT_DELETED, \false);
            }
        }
        return null;
    }
    /**
     * Check if the Pro notice should be shown in config page. See `proHeadlineButton.tsx`.
     */
    public function isConfigProNoticeVisible()
    {
        $next = $this->getStates()->get(self::NOTICE_GET_PRO_MAIN_BUTTON, 0);
        if ($next === 0) {
            $this->getStates()->set(self::NOTICE_GET_PRO_MAIN_BUTTON, \true);
            return \false;
        }
        return \time() > $next;
    }
    /**
     * Create an notice that the scanner should be rerun after any plugin got toggled.
     */
    public function admin_notice_scanner_rerun_after_plugin_toggle()
    {
        if ($this->getStates()->get(self::NOTICE_SCANNER_RERUN_AFTER_PLUGIN_TOGGLE, \false) && \current_user_can(Core::MANAGE_MIN_CAPABILITY)) {
            echo \sprintf('<div class="notice notice-warning" style="position:relative"><p>%s &bull; <a onClick="%s" href="#">%s</a></p>%s</div>', \__('You have enabled or disabled plugins on your website, which may require your cookie banner to be adjusted. Please scan your website again as soon as you have finished the changes!', RCB_TD), \esc_js($this->getStates()->noticeDismissOnClickHandler(self::NOTICE_SCANNER_RERUN_AFTER_PLUGIN_TOGGLE, 'false', Core::getInstance()->getConfigPage()->getUrl() . '#/scanner?start=1')), \__('Scan website again', RCB_TD), \sprintf('<button type="button" class="notice-dismiss" onClick="%s"></button>', \esc_js($this->getStates()->noticeDismissOnClickHandler(self::NOTICE_SCANNER_RERUN_AFTER_PLUGIN_TOGGLE, 'false'))));
        }
    }
    /**
     * Create an notice and inform the user about too many TCF vendors.
     */
    public function admin_notice_tcf_too_much_vendors()
    {
        if ($this->isPro() && TCF::getInstance()->isActive() && $this->getStates()->get(self::NOTICE_TCF_TOO_MUCH_VENDORS, \false) && TcfVendorConfiguration::getInstance()->getAllCount() > self::TCF_TOO_MUCH_VENDORS) {
            echo \sprintf('<div class="notice notice-warning" style="position:relative"><p>%s &bull; <a href="%s">%s</a></p>%s</div>', \__('Are you really embedding ads and content from all created TCF vendors on your website? <strong>Asking for consent from vendors you don\'t use could be an abuse of rights and could make the entire consent ineffective.</strong> You make it difficult for your website visitors to make an informed choice by using too many vendors. We therefore recommend you to create only TCF vendors that you actually use!', RCB_TD), Core::getInstance()->getConfigPage()->getUrl() . '#/cookies/tcf-vendors', \__('Configure TCF vendors', RCB_TD), \sprintf('<button type="button" class="notice-dismiss" onClick="%s"></button>', \esc_js($this->getStates()->noticeDismissOnClickHandler(self::NOTICE_TCF_TOO_MUCH_VENDORS, 'false'))));
        }
    }
    /**
     * Set a scan result as ignored/unignored.
     *
     * @param string $type Can be `template` or `host`
     * @param string $value
     * @param boolean $state
     */
    public function setScannerIgnored($type, $value, $state)
    {
        $useKey = $type === 'template' ? self::SCANNER_IGNORED_TEMPALTES : self::SCANNER_IGNORED_EXTERNAL_HOSTS;
        $ignored = $this->getStates()->get($useKey, []);
        if ($state) {
            $ignored[] = $value;
        } else {
            $searchIdx = \array_search($value, $ignored, \true);
            if ($searchIdx !== \false) {
                unset($ignored[$searchIdx]);
            }
        }
        return $this->getStates()->set($useKey, \array_values(\array_unique($ignored)));
    }
    /**
     * Get a list of ignored scanner results mapped by template and external host.
     */
    public function getScannerIgnored()
    {
        return ['templates' => $this->getStates()->get(self::SCANNER_IGNORED_TEMPALTES, []), 'hosts' => $this->getStates()->get(self::SCANNER_IGNORED_EXTERNAL_HOSTS, [])];
    }
    /**
     * Set checklist item as open/closed.
     *
     * @param string $id
     * @param boolean $state
     */
    public function setChecklistItem($id, $state)
    {
        return $this->getStates()->set(\DevOwl\RealCookieBanner\view\Notices::CHECKLIST_PREFIX . $id, $state);
    }
    /**
     * Get checklist item state.
     *
     * @param string $id
     * @param boolean $state
     */
    public function isChecklistItem($id)
    {
        return $this->getStates()->get(\DevOwl\RealCookieBanner\view\Notices::CHECKLIST_PREFIX . $id);
    }
    /**
     * Check if a non-blocking request has started.
     */
    public function isNonBlockingRequestStarted()
    {
        return $this->nonBlockingRequestStarted;
    }
    /**
     * Get list of clicked modal hints.
     *
     * @return string[]
     */
    public function getClickedModalHints()
    {
        return \array_keys($this->getStates()->getKeysStartingWith(self::MODAL_HINT_PREFIX));
    }
    /**
     * Dismiss a scanner explicit external URL coverage notice by type.
     *
     * @param string $state
     */
    public function dismissScannerExplicitExternalUrlCoverageNotice($state)
    {
        // Remove explicit notices of external URLs which do not require a manual scan
        $noticeState = $this->getStates()->get(\DevOwl\RealCookieBanner\view\Notices::NOTICE_SCANNER_EXPLICIT_EXTERNAL_URL_COVERAGE, []);
        $doUpdate = \false;
        $dismissed = [];
        foreach ($noticeState as $host => $oldState) {
            if ($oldState === $state) {
                $noticeState[$host] = \DevOwl\RealCookieBanner\view\Notices::SCANNER_EXPLICIT_EXTERNAL_URL_COVERAGE_STATE_DISMISSED;
                $dismissed[] = $host;
                $doUpdate = \true;
            }
        }
        if ($doUpdate) {
            $this->getStates()->set(\DevOwl\RealCookieBanner\view\Notices::NOTICE_SCANNER_EXPLICIT_EXTERNAL_URL_COVERAGE, $noticeState);
        }
        return $dismissed;
    }
    /**
     * Reset states of notices which needs to get recalculated when a service / content blocker got updated / deleted.
     */
    public function recalculatePostDependingNotices()
    {
        $states = $this->getStates();
        $states->set(self::NOTICE_SERVICES_WITH_EMPTY_PRIVACY_POLICY, null);
        $states->set(self::NOTICE_SERVICES_WITH_UPDATED_TEMPLATES, null);
        $states->set(self::NOTICE_SERVICES_WITH_SUCCESSOR_TEMPLATES, null);
        $states->set(self::NOTICE_SERVICES_WITH_GOOGLE_CONSENT_MODE_ADJUSTMENTS, null);
    }
    /**
     * Getter.
     *
     * @codeCoverageIgnore
     */
    public function getStates()
    {
        return $this->states;
    }
}
