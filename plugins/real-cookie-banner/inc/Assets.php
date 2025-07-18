<?php

namespace DevOwl\RealCookieBanner;

use DevOwl\RealCookieBanner\Vendor\DevOwl\CacheInvalidate\CacheInvalidator;
use DevOwl\RealCookieBanner\Vendor\DevOwl\Customize\Assets as CustomizeAssets;
use DevOwl\RealCookieBanner\Vendor\DevOwl\Freemium\Assets as FreemiumAssets;
use DevOwl\RealCookieBanner\Vendor\DevOwl\Multilingual\Iso3166OneAlpha2;
use DevOwl\RealCookieBanner\base\UtilsProvider;
use DevOwl\RealCookieBanner\lite\settings\TcfVendorConfiguration;
use DevOwl\RealCookieBanner\settings\Cookie;
use DevOwl\RealCookieBanner\settings\Consent;
use DevOwl\RealCookieBanner\settings\CookieGroup;
use DevOwl\RealCookieBanner\settings\CountryBypass;
use DevOwl\RealCookieBanner\settings\Revision;
use DevOwl\RealCookieBanner\settings\General;
use DevOwl\RealCookieBanner\settings\Reset;
use DevOwl\RealCookieBanner\view\Blocker;
use DevOwl\RealCookieBanner\settings\TCF;
use DevOwl\RealCookieBanner\view\Banner;
use DevOwl\RealCookieBanner\view\customize\banner\BasicLayout;
use DevOwl\RealCookieBanner\view\customize\banner\CustomCss;
use DevOwl\RealCookieBanner\view\customize\banner\StickyLinks;
use DevOwl\RealCookieBanner\view\customize\banner\Texts;
use DevOwl\RealCookieBanner\Vendor\DevOwl\RealProductManagerWpClient\Core as RpmWpClientCore;
use DevOwl\RealCookieBanner\Vendor\DevOwl\RealProductManagerWpClient\license\License;
use DevOwl\RealCookieBanner\Vendor\MatthiasWeb\Utils\Utils as UtilsUtils;
use DevOwl\RealCookieBanner\Vendor\MatthiasWeb\Utils\Assets as UtilsAssets;
use DevOwl\RealCookieBanner\Vendor\MatthiasWeb\Utils\Constants;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Asset management for frontend scripts and styles.
 * @internal
 */
class Assets
{
    use UtilsProvider;
    use UtilsAssets;
    use FreemiumAssets;
    use CustomizeAssets;
    const TCF_STUB_PATH = '@iabtechlabtcf/stub/lib/stub.js';
    /**
     * The registered handle name for the enqueued banner.
     */
    public $handleBanner = null;
    /**
     * The registered handle name for the enqueued blocker.
     */
    public $handleBlocker = null;
    /**
     * The current post ID captured by the body_class filter.
     *
     * @var int[]
     */
    protected $currentPostId = [];
    /**
     * Get the current post ID through the wp filter. At this time, the
     * wp filter is the only way to reliably detect the current post ID. Plugins like
     * Visual Composer run into issues in `wp_enqueue_scripts` returning the wrong ID (in Visual Composer,
     * the ID of Theme Builder > Headers is returned).
     */
    public function wp()
    {
        if (\is_singular()) {
            $this->currentPostId = [\get_the_ID()];
        }
    }
    /**
     * See `DeliverAnonymousAsset`.
     */
    public function createHashedAssets()
    {
        $filePath = RCB_PATH . '/' . $this->getPublicFolder() . '%s.' . ($this->isPro() ? 'pro' : 'lite') . '.js';
        $libraryFilePath = RCB_PATH . '/' . $this->getPublicFolder(\true) . '/%s';
        $handleName = RCB_SLUG . '-%s';
        $anonymousAssetsBuilder = \DevOwl\RealCookieBanner\Core::getInstance()->getAnonymousAssetBuilder();
        $isTcf = TCF::getInstance()->isActive() && TcfVendorConfiguration::getInstance()->getAllCount() > 0;
        $bannerName = $isTcf ? 'banner_tcf' : 'banner';
        $blockerName = $isTcf ? 'blocker_tcf' : 'blocker';
        $anonymousAssetsBuilder->build(\sprintf($handleName, 'vendor-' . RCB_SLUG . '-' . $bannerName), \sprintf($filePath, 'vendor-' . $bannerName), 'vendorBanner');
        $anonymousAssetsBuilder->build(\sprintf($handleName, 'vendor-' . RCB_SLUG . '-' . $blockerName), \sprintf($filePath, 'vendor-' . $blockerName), 'vendorBlocker');
        $anonymousAssetsBuilder->build(\sprintf($handleName, $bannerName), \sprintf($filePath, $bannerName), 'banner');
        $anonymousAssetsBuilder->build(\sprintf($handleName, $blockerName), \sprintf($filePath, $blockerName), 'blocker');
        if ($isTcf) {
            $anonymousAssetsBuilder->build('iabtcf-stub', \sprintf($libraryFilePath, self::TCF_STUB_PATH), 'iabtcf-stub');
        }
    }
    /**
     * Enqueue scripts and styles depending on the type. This function is called
     * from both admin_enqueue_scripts and wp_enqueue_scripts. You can check the
     * type through the $type parameter. In this function you can include your
     * external libraries from src/public/lib, too.
     *
     * @param string $type The type (see utils Assets constants)
     * @param string $hook_suffix The current admin page
     */
    public function enqueue_scripts_and_styles($type, $hook_suffix = null)
    {
        // Generally check if an entrypoint should be loaded
        $core = \DevOwl\RealCookieBanner\Core::getInstance();
        $banner = $core->getBanner();
        $isConfigPage = $core->getConfigPage()->isVisible($hook_suffix);
        $shouldLoadAssets = $banner->shouldLoadAssets($type);
        $realUtils = RCB_ROOT_SLUG . '-real-utils-helper';
        // Do not enqueue anything if not needed
        if (!$isConfigPage && !\in_array($type, [Constants::ASSETS_TYPE_CUSTOMIZE], \true) && !$shouldLoadAssets) {
            // We need to enqueue real-utils helper always in backend to keep cross-selling intact
            if ($type === Constants::ASSETS_TYPE_ADMIN) {
                $this->enqueueUtils();
                \wp_enqueue_script($realUtils);
                \wp_enqueue_style($realUtils);
            }
            return;
        }
        // Your assets implementation here... See utils Assets for enqueue* methods
        // $useNonMinifiedSources = $this->useNonMinifiedSources(); // Use this variable if you need to differ between minified or non minified sources
        // Our utils package relies on jQuery, but this shouldn't be a problem as the most themes still use jQuery (might be replaced with https://github.com/github/fetch)
        $scriptDeps = [];
        // Mobx should not be loaded on any frontend page, but in customize preview (see `customize_banner.tsx`)
        if ($type === Constants::ASSETS_TYPE_CUSTOMIZE || $isConfigPage || \is_customize_preview()) {
            $scriptDeps = $this->enqueueUtils();
            $scriptDeps[] = 'moment';
            $scriptDeps[] = 'wp-editor';
        }
        // Enqueue customize helpers and add the handle to our dependencies
        $this->probablyEnqueueCustomizeHelpers($scriptDeps, $isConfigPage);
        // When the banner should be shown, do not enqueue real utils
        if (!$shouldLoadAssets) {
            \array_push($scriptDeps, $realUtils);
        }
        // Enqueue plugin entry points
        if ($isConfigPage) {
            $handle = $this->enqueueAdminPage($scriptDeps);
        } elseif ($type === Constants::ASSETS_TYPE_CUSTOMIZE) {
            $handle = $this->enqueueScript('customize', [[$this->isPro(), 'customize.pro.js'], 'customize.lite.js'], $scriptDeps);
            $this->enqueueStyle('customize', 'customize.css');
        } elseif ($shouldLoadAssets) {
            $handle = $this->enqueueBanner($scriptDeps);
            // Enqueue blocker if enabled
            if (General::getInstance()->isBlockerActive() && \in_array($type, [Constants::ASSETS_TYPE_FRONTEND, Constants::ASSETS_TYPE_LOGIN], \true)) {
                $this->enqueueBlocker(\array_merge($scriptDeps, [$handle]));
            }
        }
        // Localize script with server-side variables
        $this->anonymous_localize_script($handle, 'realCookieBanner', $this->localizeScript($type), [
            'makeBase64Encoded' => [Cookie::META_NAME_CODE_OPT_IN, Cookie::META_NAME_CODE_OPT_OUT, Cookie::META_NAME_CODE_ON_PAGE_LOAD, 'contactEmail'],
            'useCore' => !\in_array($type, [Constants::ASSETS_TYPE_FRONTEND, Constants::ASSETS_TYPE_LOGIN], \true) && !\is_customize_preview(),
            // Only allow lazy parse in frontend (also not in customizer) as this conflicts with Mobx observables
            'lazyParse' => \in_array($type, [Constants::ASSETS_TYPE_FRONTEND], \true) && !\is_customize_preview() ? ['others.frontend.tcf', 'others.frontend.groups', 'others.customizeValuesBanner'] : [],
        ]);
    }
    /**
     * Enqueue admin page (currently only the config).
     *
     * @param string[] $scriptDeps
     */
    public function enqueueAdminPage($scriptDeps)
    {
        $useNonMinifiedSources = $this->useNonMinifiedSources();
        \array_unshift($scriptDeps, 'wp-codemirror', 'jquery-ui-sortable');
        \wp_enqueue_media();
        // Enqueue code mirror to edit JavaScript files
        $cm_settings['codeEditor'] = \wp_enqueue_code_editor(['type' => 'text/html']);
        \wp_localize_script('jquery', 'cm_settings', $cm_settings);
        // real-product-manager-wp-client (for licensing purposes)
        \array_unshift($scriptDeps, RpmWpClientCore::getInstance()->getAssets()->enqueue($this));
        $handle = $this->enqueueScript('admin', [[$this->isPro(), 'admin.pro.js'], 'admin.lite.js'], $scriptDeps);
        $this->enqueueStyle('admin', 'admin.css');
        return $handle;
    }
    /**
     * Enqueue the banner.
     *
     * @param string[] $scriptDeps
     */
    public function enqueueBanner($scriptDeps)
    {
        // Only enqueue once
        static $enqueued = \false;
        if ($enqueued) {
            return;
        }
        $enqueued = \true;
        $excludeAssets = \DevOwl\RealCookieBanner\Core::getInstance()->getExcludeAssets();
        $useNonMinifiedSources = $this->useNonMinifiedSources();
        $isTcf = TCF::getInstance()->isActive() && TcfVendorConfiguration::getInstance()->getAllCount() > 0;
        $anonymousAssetsBuilder = \DevOwl\RealCookieBanner\Core::getInstance()->getAnonymousAssetBuilder();
        $isAntiAdBlock = $this->isAntiAdBlockActive();
        // Enqueue IAB TCF stub
        if ($isTcf) {
            $handle = $this->enqueueLibraryScript('iabtcf-stub', self::TCF_STUB_PATH);
            \array_unshift($scriptDeps, $handle);
            if ($handle !== \false && $isAntiAdBlock) {
                $anonymousAssetsBuilder->ready('iabtcf-stub');
            }
        }
        // Enqueue scripts in customize preview
        if (\is_customize_preview()) {
            $handle = $this->enqueueScript('customize_banner', [[$this->isPro(), 'customize_banner.pro.js'], 'customize_banner.lite.js'], $scriptDeps);
        } else {
            // Enqueue banner in frontend page (determine correct bundle depending on TCF status)
            $handle = $this->enqueueScript($isTcf ? 'banner_tcf' : 'banner', [[$isTcf, 'banner_tcf.pro.js'], [$this->isPro(), 'banner.pro.js'], 'banner.lite.js'], $scriptDeps, \false);
            // Modify the URL so it is obtained by a hashed root URL
            if ($handle !== \false && $isAntiAdBlock) {
                $anonymousAssetsBuilder->ready('banner', !$useNonMinifiedSources);
                $anonymousAssetsBuilder->ready('vendorBanner', !$useNonMinifiedSources);
            }
            // Populate `codeOnPageLoad`
            \add_action('wp_head', [\DevOwl\RealCookieBanner\Core::getInstance()->getBanner(), 'wp_head'], 2);
        }
        // animate.css (only when animations are enabled)
        $customize = \DevOwl\RealCookieBanner\Core::getInstance()->getBanner()->getCustomize();
        $hasAnimations = $customize->getSetting(BasicLayout::SETTING_ANIMATION_IN) !== 'none' || $customize->getSetting(BasicLayout::SETTING_ANIMATION_OUT) !== 'none' || $customize->getSetting(StickyLinks::SETTING_ENABLED);
        if (\is_customize_preview() || $hasAnimations) {
            $handleAnimateCss = $this->enqueueLibraryStyle('animate-css', [[$useNonMinifiedSources, 'animate.css/animate.css'], 'animate.css/animate.min.css']);
            $excludeAssets->byHandle('css', $handleAnimateCss);
        }
        if ($handle !== \false) {
            $preloadJs = ['iabtcf-stub', $handle];
            $preloadCss = ['animate-css'];
            $advancedFeatures = [Constants::ASSETS_ADVANCED_ENQUEUE_FEATURE_PRIORITY_QUEUE];
            if (!$excludeAssets->hasFailureSupportPluginActive()) {
                $advancedFeatures[] = Constants::ASSETS_ADVANCED_ENQUEUE_FEATURE_DEFER;
                $advancedFeatures[] = Constants::ASSETS_ADVANCED_ENQUEUE_FEATURE_PRELOADING;
            }
            // Only enable the advanced enqueue when we are not relying on `react-dom` as this could lead to issues with
            // e.g. WP Fastest Cache which moves `react-dom` to the body footer -> "Undefined variable ReactDOM" error.
            if (!\is_customize_preview()) {
                $this->enableAdvancedEnqueue($preloadJs, $advancedFeatures);
                $this->enableAdvancedEnqueue($preloadCss, $advancedFeatures, 'style');
            }
            $excludeAssets->byHandle('js', $preloadJs);
            $excludeAssets->byHandle('css', $preloadCss);
        }
        // Add window.consentApi stubs
        \wp_add_inline_script($handle, '((a,b)=>{a[b]||(a[b]={unblockSync:()=>undefined},["consentSync"].forEach(c=>a[b][c]=()=>({cookie:null,consentGiven:!1,cookieOptIn:!0})),["consent","consentAll","unblock"].forEach(c=>a[b][c]=(...d)=>new Promise(e=>a.addEventListener(b,()=>{a[b][c](...d).then(e)},{once:!0}))))})(window,"consentApi");', 'before');
        $this->handleBanner = $handle;
        return $handle;
    }
    /**
     * Enqueue the blocker.
     *
     * @param string[] $scriptDeps
     */
    public function enqueueBlocker($scriptDeps)
    {
        $useNonMinifiedSources = $this->useNonMinifiedSources();
        $anonymousAssetsBuilder = \DevOwl\RealCookieBanner\Core::getInstance()->getAnonymousAssetBuilder();
        $isTcf = TCF::getInstance()->isActive() && TcfVendorConfiguration::getInstance()->getAllCount() > 0;
        $isAntiAdBlock = $this->isAntiAdBlockActive();
        $handleName = $isTcf ? 'blocker_tcf' : 'blocker';
        $handle = $this->enqueueScript($handleName, [[$isTcf, 'blocker_tcf.pro.js'], [$this->isPro(), 'blocker.pro.js'], 'blocker.lite.js'], $scriptDeps);
        if ($isAntiAdBlock) {
            $anonymousAssetsBuilder->ready('blocker', !$useNonMinifiedSources);
            $anonymousAssetsBuilder->ready('vendorBlocker', !$useNonMinifiedSources);
        }
        if ($handle !== \false) {
            $this->enableDeferredEnqueue($handle);
            $excludeAssets = \DevOwl\RealCookieBanner\Core::getInstance()->getExcludeAssets();
            $excludeAssets->byHandle('js', [$handle]);
        }
        $this->handleBlocker = $handle;
        return $handle;
    }
    /**
     * Localize the WordPress backend and frontend. If you want to provide URLs to the
     * frontend you have to consider that some JS libraries do not support umlauts
     * in their URI builder. For this you can use utils Assets#getAsciiUrl.
     *
     * Also, if you want to use the options typed in your frontend you should
     * adjust the following file too: src/public/ts/store/option.tsx
     *
     * @param string $context
     * @return array
     */
    public function overrideLocalizeScript($context)
    {
        global $wp_version;
        $result = [];
        $core = \DevOwl\RealCookieBanner\Core::getInstance();
        $cookieConsentManagement = $core->getCookieConsentManagement();
        $frontend = $cookieConsentManagement->getFrontend();
        $banner = $core->getBanner();
        $bannerCustomize = $banner->getCustomize();
        $notices = $core->getNotices();
        $licenseActivation = $core->getRpmInitiator()->getPluginUpdater()->getCurrentBlogLicense()->getActivation();
        $showLicenseFormImmediate = !$licenseActivation->hasInteractedWithFormOnce();
        $isLicensed = !empty($licenseActivation->getCode());
        $isDevLicense = $licenseActivation->getInstallationType() === License::INSTALLATION_TYPE_DEVELOPMENT;
        $frontendJson = $frontend->toJson();
        $lazyLoadedData = $frontend->prepareLazyData($frontendJson, \true);
        $anonymousAssetBuilder = $core->getAnonymousAssetBuilder();
        $pageIds = $this->currentPostId;
        if (\is_singular()) {
            $pageId = \get_the_ID();
            $pageIds[] = $pageId;
            $postType = \get_post_type($pageId);
            if (\is_string($postType)) {
                $pageIdOriginal = $core->getCompLanguage()->getOriginalPostId($pageId, $postType);
                if ($pageIdOriginal !== $pageId) {
                    $pageIds[] = $pageIdOriginal;
                }
            }
        }
        if ($context === Constants::ASSETS_TYPE_ADMIN) {
            $colorScheme = \DevOwl\RealCookieBanner\Utils::get_admin_colors();
            if (\count($colorScheme) < 4) {
                // Backwards-compatibility: The "modern" color scheme has only three colors, but for all
                // our graphs and charts we need at least 4
                $colorScheme[] = $colorScheme[0];
            }
            $dryResetTexts = [];
            Reset::getInstance()->texts(null, $dryResetTexts);
            $result = ['installationDateIso' => \mysql2date('c', \get_option(\DevOwl\RealCookieBanner\Activator::OPTION_NAME_INSTALLATION_DATE, \time())), 'showLicenseFormImmediate' => $showLicenseFormImmediate, 'showNoticeAnonymousScriptNotWritable' => $anonymousAssetBuilder->getContentDir() === \false, 'assetsUrl' => $core->getAdInitiator()->getAssetsUrl(), 'customizeValuesBanner' => $bannerCustomize->localizeValues()['customizeValuesBanner'], 'customizeBannerUrl' => $bannerCustomize->getUrl(), 'adminUrl' => \admin_url(), 'colorScheme' => $colorScheme, 'cachePlugins' => CacheInvalidator::getInstance()->getLabels(), 'modalHints' => $notices->getClickedModalHints(), 'isDemoEnv' => \DevOwl\RealCookieBanner\DemoEnvironment::getInstance()->isDemoEnv(), 'isConfigProNoticeVisible' => $notices->isConfigProNoticeVisible(), 'activePlugins' => UtilsUtils::getActivePluginsMap(), 'ageNoticeCountryAgeMap' => Consent::AGE_NOTICE_COUNTRY_AGE_MAP, 'predefinedCountryBypassLists' => CountryBypass::PREDEFINED_COUNTRY_LISTS, 'defaultCookieGroupTexts' => CookieGroup::getInstance()->getDefaultDescriptions(\true), 'useEncodedStringForScriptInputs' => \version_compare($wp_version, '5.4.0', '>='), 'resetUrl' => \add_query_arg(['_wpnonce' => \wp_create_nonce('rcb-reset-all'), 'rcb-reset-all' => 1], $core->getConfigPage()->getUrl()), 'resetTexts' => ['url' => \add_query_arg(['_wpnonce' => \wp_create_nonce('rcb-reset-texts'), 'rcb-reset-texts' => 1], $core->getConfigPage()->getUrl()), 'dry' => $dryResetTexts], 'capabilities' => ['activate_plugins' => \current_user_can('activate_plugins')]];
        } elseif (\is_customize_preview()) {
            $result = \array_merge($bannerCustomize->localizeIds(), $bannerCustomize->localizeValues(), $bannerCustomize->localizeDefaultValues(), ['poweredByTexts' => $core->getCompLanguage()->translateArray(Texts::getPoweredByLinkTexts()), 'isPoweredByLinkDisabledByException' => $bannerCustomize->isPoweredByLinkDisabledByException()]);
            $frontendJson['lazyLoadedDataForSecondView'] = $lazyLoadedData;
        } elseif ($banner->shouldLoadAssets($context)) {
            $result = $bannerCustomize->localizeValues();
            $bannerCustomize->expandLocalizeValues($result);
            // We do not need this in frontend as the cookie policy is server-side rendered
            unset($result['customizeValuesBanner']['cookiePolicy']);
        }
        if (\in_array($context, [Constants::ASSETS_TYPE_ADMIN, Constants::ASSETS_TYPE_CUSTOMIZE], \true)) {
            /**
             * Create customized hints for specific actions in frontend. Currently supported:
             * `deleteCookieGroup`, `deleteCookie`, `export`, `dashboardTile`, `proDialog`. For detailed
             * structure for this parameters please check out TypeScript typings in `types/otherOptions.tsx`.
             *
             * @hook RCB/Hints
             * @param {array} $hints
             * @return {array}
             * @ignore
             */
            $result['hints'] = \apply_filters('RCB/Hints', ['deleteCookieGroup' => [], 'deleteCookie' => [], 'export' => [], 'dashboardTile' => [], 'proDialog' => null]);
        }
        $isPreventPreDecision = $frontend->isPreventPreDecision($pageIds);
        if (!$isPreventPreDecision) {
            /**
             * Determine, if the predecision handler should be executed in the frontend.
             * If you return `true`, the banner never gets shown.
             *
             * @hook RCB/IsPreventPreDecision
             * @param {boolean} $isPreventPreDecision
             * @return {boolean}
             * @since 2.12.1
             */
            $isPreventPreDecision = \apply_filters('RCB/IsPreventPreDecision', $isPreventPreDecision);
        }
        return \apply_filters('RCB/Localize', \array_merge($result, $this->localizeFreemiumScript(), ['frontend' => $frontendJson, 'anonymousContentUrl' => $anonymousAssetBuilder->generateFolderSrc(), 'anonymousHash' => $anonymousAssetBuilder->getContentDir() && !$this->useNonMinifiedSources() && $this->isAntiAdBlockActive() ? $anonymousAssetBuilder->getHash() : null, 'hasDynamicPreDecisions' => \has_filter('RCB/Consent/DynamicPreDecision'), 'isLicensed' => $isLicensed, 'isDevLicense' => $isDevLicense, 'multilingualSkipHTMLForTag' => $core->getCompLanguage()->getSkipHTMLForTag(), 'isCurrentlyInTranslationEditorPreview' => $core->getCompLanguage()->isCurrentlyInEditorPreview(), 'defaultLanguage' => $core->getCompLanguage()->getDefaultLanguage(), 'currentLanguage' => $core->getCompLanguage()->getCurrentLanguage(), 'activeLanguages' => $core->getCompLanguage()->getActiveLanguages(), 'context' => Revision::getInstance()->getContextVariablesString(), 'iso3166OneAlpha2' => Iso3166OneAlpha2::getSortedCodes(), 'visualParentSelectors' => Blocker::VISUAL_PARENT_SELECTORS, 'isPreventPreDecision' => $isPreventPreDecision, 'isInvalidateImplicitUserConsent' => $frontend->isInvalidateImplicitUserConsent($pageIds), 'dependantVisibilityContainers' => Blocker::DEPENDANT_VISIBILITY_CONTAINERS, 'disableDeduplicateExceptions' => Blocker::DISABLE_DEDUPLICATE_EXCEPTIONS, 'bannerDesignVersion' => Banner::DESIGN_VERSION, 'bannerI18n' => \array_merge($core->getCompLanguage()->translateArray([
            'showMore' => \__('Show more', RCB_TD),
            'hideMore' => \__('Hide', RCB_TD),
            // translators:
            'showLessRelevantDetails' => \_x('Show more details (%s)', 'legal-text', RCB_TD),
            // translators:
            'hideLessRelevantDetails' => \_x('Hide more details (%s)', 'legal-text', RCB_TD),
            'other' => \_x('Other', 'legal-text', RCB_TD),
            'legalBasis' => ['label' => \_x('Use on legal basis of', 'legal-text', RCB_TD), 'consentPersonalData' => \_x('Consent for processing personal data', 'legal-text', RCB_TD), 'consentStorage' => \_x('Consent for storing or accessing information on the terminal equipment of the user', 'legal-text', RCB_TD), 'legitimateInterestPersonalData' => \_x('Legitimate interest for the processing of personal data', 'legal-text', RCB_TD), 'legitimateInterestStorage' => \_x('Provision of explicitly requested digital service for storing or accessing information on the terminal equipment of the user', 'legal-text', RCB_TD), 'legalRequirementPersonalData' => \_x('Compliance with a legal obligation for processing of personal data', 'legal-text', RCB_TD)],
            // See als `useTerritorialLegalBasisArticles.tsx`
            'territorialLegalBasisArticles' => [General::TERRITORIAL_LEGAL_BASIS_GDPR => ['dataProcessingInUnsafeCountries' => \_x('Art. 49 (1) (a) GDPR', 'legal-text', RCB_TD)], General::TERRITORIAL_LEGAL_BASIS_DSG_SWITZERLAND => ['dataProcessingInUnsafeCountries' => \_x('Art. 17 (1) (a) DSG (Switzerland)', 'legal-text', RCB_TD)]],
            'legitimateInterest' => \_x('Legitimate interest', 'legal-text', RCB_TD),
            'consent' => \_x('Consent', 'legal-text', RCB_TD),
            'crawlerLinkAlert' => \_x('We have recognized that you are a crawler/bot. Only natural persons must consent to cookies and processing of personal data. Therefore, the link has no function for you.', 'legal-text', RCB_TD),
            'technicalCookieDefinitions' => \_x('Technical cookie definitions', 'legal-text', RCB_TD),
            'technicalCookieName' => \_x('Technical cookie name', 'legal-text', RCB_TD),
            'usesCookies' => \_x('Uses cookies', 'legal-text', RCB_TD),
            'cookieRefresh' => \_x('Cookie refresh', 'legal-text', RCB_TD),
            'usesNonCookieAccess' => \_x('Uses cookie-like information (LocalStorage, SessionStorage, IndexDB, etc.)', 'legal-text', RCB_TD),
            'host' => \_x('Host', 'legal-text', RCB_TD),
            'duration' => \_x('Duration', 'legal-text', RCB_TD),
            'noExpiration' => \_x('No expiration', 'legal-text', RCB_TD),
            'type' => \_x('Type', 'legal-text', RCB_TD),
            'purpose' => \_x('Purpose', 'legal-text', RCB_TD),
            'purposes' => \_x('Purposes', 'legal-text', RCB_TD),
            'headerTitlePrivacyPolicyHistory' => \_x('History of your privacy settings', 'legal-text', RCB_TD),
            'skipToConsentChoices' => \_x('Skip to consent choices', 'legal-text', RCB_TD),
            'historyLabel' => \_x('Show consent from', 'legal-text', RCB_TD),
            'historyItemLoadError' => \_x('Reading the consent has failed. Please try again later!', 'legal-text', RCB_TD),
            'historySelectNone' => \_x('Not yet consented to', 'legal-text', RCB_TD),
            'provider' => \_x('Provider', 'legal-text', RCB_TD),
            'providerContactPhone' => \_x('Phone', 'legal-text', RCB_TD),
            'providerContactEmail' => \_x('Email', 'legal-text', RCB_TD),
            'providerContactLink' => \_x('Contact form', 'legal-text', RCB_TD),
            'providerPrivacyPolicyUrl' => \_x('Privacy Policy', 'legal-text', RCB_TD),
            'providerLegalNoticeUrl' => \_x('Legal notice', 'legal-text', RCB_TD),
            'nonStandard' => \_x('Non-standardized data processing', 'legal-text', RCB_TD),
            'nonStandardDesc' => \_x('Some services set cookies and/or process personal data without complying with consent communication standards. These services are divided into several groups. So-called "essential services" are used based on legitimate interest and cannot be opted out (an objection may have to be made by email or letter in accordance with the privacy policy), while all other services are used only after consent has been given.', 'legal-text', RCB_TD),
            // translators:
            'dataProcessingInThirdCountries' => \_x('Data processing in third countries', 'legal-text', RCB_TD),
            'safetyMechanisms' => ['label' => \_x('Safety mechanisms for data transmission', 'legal-text', RCB_TD), 'standardContractualClauses' => \_x('Standard contractual clauses', 'legal-text', RCB_TD), 'adequacyDecision' => \_x('Adequacy decision', 'legal-text', RCB_TD), 'eu' => \_x('EU', 'legal-text', RCB_TD), 'switzerland' => \_x('Switzerland', 'legal-text', RCB_TD), 'bindingCorporateRules' => \_x('Binding corporate rules', 'legal-text', RCB_TD), 'contractualGuaranteeSccSubprocessors' => \_x('Contractual guarantee for standard contractual clauses with sub-processors', 'legal-text', RCB_TD)],
            'durationUnit' => ['n1' => ['s' => \__('second', RCB_TD), 'm' => \__('minute', RCB_TD), 'h' => \__('hour', RCB_TD), 'd' => \__('day', RCB_TD), 'mo' => \__('month', RCB_TD), 'y' => \__('year', RCB_TD)], 'nx' => ['s' => \__('seconds', RCB_TD), 'm' => \__('minutes', RCB_TD), 'h' => \__('hours', RCB_TD), 'd' => \__('days', RCB_TD), 'mo' => \__('months', RCB_TD), 'y' => \__('years', RCB_TD)]],
            'close' => \__('Close', RCB_TD),
            'closeWithoutSaving' => \__('Close without saving', RCB_TD),
            'yes' => \__('Yes', RCB_TD),
            'no' => \__('No', RCB_TD),
            'unknown' => \__('Unknown', RCB_TD),
            'none' => \__('None', RCB_TD),
            'noLicense' => \__('No license activated - not for production use!', RCB_TD),
            'devLicense' => \__('Product license not for production use!', RCB_TD),
            'devLicenseLearnMore' => \__('Learn more', RCB_TD),
            'devLicenseLink' => \__('https://devowl.io/knowledge-base/license-installation-type/', RCB_TD),
            // translators:
            'andSeparator' => \__(' and ', RCB_TD),
            'deprecated' => [
                // @deprecated Replaced by `safetyMechanisms`
                'appropriateSafeguard' => \_x('Appropriate safeguard', 'legal-text', RCB_TD),
                // @deprecated Replaced by `dataProcessingInThirdCountries`
                'dataProcessingInUnsafeCountries' => \_x('Data processing in unsafe third countries', 'legal-text', RCB_TD),
                // @deprecated Replaced by `legalRequirementPersonalData`
                'legalRequirement' => \_x('Compliance with a legal obligation', 'legal-text', RCB_TD),
            ],
        ], [], null, ['legal-text'])), 'pageRequestUuid4' => $core->getPageRequestUuid4(), 'pageByIdUrl' => \add_query_arg('page_id', '', \home_url()), 'pluginUrl' => $core->getPluginData('PluginURI')]), $context);
    }
    /**
     * Provide predefined links for `RCB/Hints` `dashboardTile`'s configuration.
     *
     * @param array $hints
     */
    public function hints_dashboard_tile_predefined_links($hints)
    {
        foreach ($hints['dashboardTile'] as &$tile) {
            if (isset($tile['links'])) {
                foreach ($tile['links'] as &$link) {
                    if ($link === 'learnAboutPro') {
                        $link = ['link' => \sprintf('%s&feature=partner-dashboard-tile', RCB_PRO_VERSION), 'linkText' => \__('Learn more', RCB_TD)];
                    }
                }
            }
        }
        return $hints;
    }
    /**
     * Check if the current banner is configured to provide an anti ad block system.
     */
    protected function isAntiAdBlockActive()
    {
        return \bool_from_yn(\DevOwl\RealCookieBanner\Core::getInstance()->getBanner()->getCustomize()->getSetting(CustomCss::SETTING_ANTI_AD_BLOCKER));
    }
    /*
     * Enqueue our `rcb-scan` client-worker for `real-queue`.
     */
    public function real_queue_enqueue_scripts($handle)
    {
        $handle = $this->enqueueScript('queue', [[$this->isPro(), 'queue.pro.js'], 'queue.lite.js'], [$handle]);
        \wp_localize_script($handle, 'realCookieBannerQueue', ['originalHomeUrl' => \DevOwl\RealCookieBanner\Utils::getOriginalHomeUrl(), 'blogId' => \get_current_blog_id()]);
    }
    /**
     * Enqueue assets for Fluent Community. With this hook, we are in the `<head` section.
     *
     * @see https://fluentcommunity.co/
     */
    public function fluent_community_portal_head()
    {
        global $wp_scripts;
        $this->enqueue_scripts_and_styles(Constants::ASSETS_TYPE_FRONTEND);
        // The plugin does not automatically print the head, instead it uses `do_items` explicitly
        $wp_scripts->do_items($this->handleBanner);
        \add_action('fluent_community/portal_html', [\DevOwl\RealCookieBanner\Core::getInstance()->getBanner(), 'wp_body_open']);
        \add_action('fluent_community/portal_footer', function () use($wp_scripts) {
            // With this action, we are in the footer (end of `</body>`)
            $wp_scripts->do_items($this->handleBlocker);
        });
    }
}
