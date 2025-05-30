<?php

namespace DevOwl\RealCookieBanner\view;

use DevOwl\RealCookieBanner\Vendor\DevOwl\CacheInvalidate\CacheInvalidator;
use DevOwl\RealCookieBanner\Vendor\DevOwl\Customize\Utils;
use DevOwl\RealCookieBanner\Vendor\DevOwl\HeadlessContentBlocker\AttributesHelper;
use DevOwl\RealCookieBanner\Vendor\DevOwl\HeadlessContentBlocker\Constants;
use DevOwl\RealCookieBanner\Vendor\DevOwl\Multilingual\AbstractOutputBufferPlugin;
use DevOwl\RealCookieBanner\base\UtilsProvider;
use DevOwl\RealCookieBanner\Core;
use DevOwl\RealCookieBanner\settings\Consent;
use DevOwl\RealCookieBanner\settings\General;
use DevOwl\RealCookieBanner\Utils as RealCookieBannerUtils;
use DevOwl\RealCookieBanner\view\customize\banner\BasicLayout;
use DevOwl\RealCookieBanner\view\customize\banner\CustomCss;
use DevOwl\RealCookieBanner\view\customize\banner\FooterDesign;
use DevOwl\RealCookieBanner\view\customize\banner\Texts;
use DevOwl\RealCookieBanner\Vendor\MatthiasWeb\Utils\Constants as UtilsConstants;
use WP_Admin_Bar;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Main banner.
 * @internal
 */
class Banner
{
    use UtilsProvider;
    /**
     * The design version is incremented each time, we need to distinguish between some
     * UI elements within our cookie banner and content blockers. For example, in a recent
     * version of Real Cookie Banner we removed the footer for content blockers completely, but
     * to be compliant with our documentation we still want to show footers for older consents
     * in our "List of consents".
     *
     * For the chronically changes have a look at the `@devowl-wp/react-cookie-banner` package.
     */
    const DESIGN_VERSION = 12;
    const ACTION_CLEAR_CURRENT_COOKIE = 'rcb-clear-current-cookie';
    /**
     * Example:
     *
     * ```html
     * <script unique-write-name="gtag">console.log("this gets written to DOM");</script>
     * <script unique-write-name="gtag">console.log("this gets skipped");</script>
     * ```
     */
    const HTML_ATTRIBUTE_UNIQUE_WRITE_NAME = 'unique-write-name';
    /**
     * The customize handler
     *
     * @var BannerCustomize
     */
    private $customize;
    /**
     * C'tor.
     */
    private function __construct()
    {
        $this->customize = \DevOwl\RealCookieBanner\view\BannerCustomize::instance();
    }
    /**
     * Show a "Show banner again" button in the admin toolbar in frontend.
     *
     * @param WP_Admin_Bar $admin_bar
     */
    public function admin_bar_menu($admin_bar)
    {
        if (!\is_admin() && $this->shouldLoadAssets(UtilsConstants::ASSETS_TYPE_FRONTEND) && \current_user_can(Core::MANAGE_MIN_CAPABILITY)) {
            if (isset($_GET[self::ACTION_CLEAR_CURRENT_COOKIE])) {
                RealCookieBannerUtils::setCookie(Core::getInstance()->getCookieConsentManagement()->getFrontend()->getCookieName(), '', -1, \constant('COOKIEPATH'), \constant('COOKIE_DOMAIN'), \is_ssl(), \false, 'None');
                \wp_safe_redirect(\esc_url_raw(\add_query_arg(self::ACTION_CLEAR_CURRENT_COOKIE, \false)));
                exit;
            }
            $admin_bar->add_menu(['parent' => Core::getInstance()->getConfigPage()->ensureAdminBarTopLevelNode(), 'id' => self::ACTION_CLEAR_CURRENT_COOKIE, 'title' => Consent::getInstance()->isBannerLessConsent() ? \__('Reset cookie banner consent to default', RCB_TD) : \__('Show cookie banner again', RCB_TD), 'href' => \esc_url_raw(\add_query_arg(self::ACTION_CLEAR_CURRENT_COOKIE, \true))]);
        }
    }
    /**
     * Checks if the banner is active for the current page. This does not check any
     * user relevant conditions because they need to be done in frontend (caching).
     *
     * @param string $context The context passed to `Assets#enqueue_script_and_styles`
     * @see https://app.clickup.com/t/5yty88
     */
    public function shouldLoadAssets($context)
    {
        // Are we on website frontend?
        if (!\in_array($context, [UtilsConstants::ASSETS_TYPE_FRONTEND, UtilsConstants::ASSETS_TYPE_LOGIN], \true) || RealCookieBannerUtils::isPageBuilder()) {
            return \false;
        }
        // ALways show in customize preview
        if (\is_customize_preview()) {
            return \true;
        }
        // Is the banner activated?
        if (!General::getInstance()->isBannerActive()) {
            return \false;
        }
        return RealCookieBannerUtils::isFrontend();
    }
    /**
     * The `codeOnPageLoad` can be directly rendered to the Output Buffer cause
     * it does not stand in conflict with any caching plugin (no conditional rendering).
     */
    public function wp_head()
    {
        $additionalTags = CacheInvalidator::getInstance()->getExcludeHtmlAttributesString();
        $frontend = Core::getInstance()->getCookieConsentManagement()->getFrontend();
        $output = $frontend->generateCodeOnPageLoad(function ($html, $service) use($additionalTags) {
            return AttributesHelper::skipHtmlTagsInContentBlocker($html, $additionalTags);
        });
        echo \join('', $output);
        // Web vitals: Avoid large rerenderings when the content blocker gets overlapped the original item
        // E.g. SVGs are loaded within the blocked element.
        // Additionally, when a visual parent is used with the `childrenSelector` option, we need to hide all children of the blocked element
        // when the specified children are not found.
        // TODO: Move this to a frontend package?
        echo \sprintf('<style>[%s]:not(.rcb-content-blocker):not([%s]):not([%s^="children:"]):not([%s]){opacity:0!important;}
.rcb-content-blocker+.rcb-content-blocker-children-fallback~*{display:none!important;}</style>', Constants::HTML_ATTRIBUTE_BLOCKER_ID, Constants::HTML_ATTRIBUTE_UNBLOCKED_TRANSACTION_COMPLETE, Constants::HTML_ATTRIBUTE_VISUAL_PARENT, Constants::HTML_ATTRIBUTE_CONFIRM);
    }
    /**
     * Print out the overlay at `wp_body_open`. See also `wp_footer` for backwards-compatibilty.
     */
    public function wp_body_open()
    {
        $this->printOverlay();
    }
    /**
     * Print out the overlay at `wp_footer` time but only when `wp_body_open` was not yet called (some
     * themes do not support it).
     */
    public function wp_footer()
    {
        // Some themes or caching mechanism lead to output the footer multiple times.
        // Instead of finding the root cause itself, we could workaround this. In our case,
        // the `div[id]` should be available only once.
        \remove_action('wp_footer', [$this, 'wp_footer']);
        if (!\did_action('wp_body_open')) {
            $this->printOverlay();
        }
        $shouldLoadAssets = $this->shouldLoadAssets(UtilsConstants::ASSETS_TYPE_FRONTEND);
        if ($shouldLoadAssets && !\is_customize_preview() && \get_option(FooterDesign::SETTING_POWERED_BY_LINK) && General::getInstance()->isBannerActive()) {
            echo $this->poweredLink();
        }
    }
    /**
     * Print out the overlay so it is server-side rendered (avoid flickering as early as possible).
     *
     * See also inlineStyle.tsx#overlay for more information!
     */
    protected function printOverlay()
    {
        $customize = $this->getCustomize();
        $shouldLoadAssets = $this->shouldLoadAssets(UtilsConstants::ASSETS_TYPE_FRONTEND);
        if ($shouldLoadAssets && !\is_customize_preview()) {
            $type = $customize->getSetting(BasicLayout::SETTING_TYPE);
            $showOverlay = $customize->getSetting(BasicLayout::SETTING_OVERLAY);
            $antiAdBlocker = \bool_from_yn($customize->getSetting(CustomCss::SETTING_ANTI_AD_BLOCKER));
            $overlayBlur = $customize->getSetting(BasicLayout::SETTING_OVERLAY_BLUR);
            // Calculate background color
            $bgStyle = '';
            if ($showOverlay) {
                $overlayBg = $customize->getSetting(BasicLayout::SETTING_OVERLAY_BG);
                $overlayBgAlpha = $customize->getSetting(BasicLayout::SETTING_OVERLAY_BG_ALPHA);
                $bgStyle = \sprintf('background-color: %s;', Utils::calculateOverlay($overlayBg, $overlayBgAlpha));
            }
            echo \sprintf('<div id="%s" %s="%s" class="%s" data-bg="%s" style="%s %s position:fixed;top:0;left:0;right:0;bottom:0;z-index:999999;pointer-events:%s;display:none;filter:none;max-width:100vw;max-height:100vh;transform:translateZ(0);" %s></div>', Core::getInstance()->getPageRequestUuid4(), Constants::HTML_ATTRIBUTE_CONSENT_SKIP_BLOCKER, Constants::HTML_ATTRIBUTE_CONSENT_SKIP_BLOCKER_VALUE, $antiAdBlocker ? '' : \sprintf('rcb-banner rcb-banner-%s %s', $type, empty($bgStyle) ? 'overlay-deactivated' : ''), $bgStyle, $bgStyle, $showOverlay && $this->isPro() ? \join('', \array_map(function ($prefix) use($overlayBlur) {
                return \sprintf('%sbackdrop-filter:blur(%spx);', $prefix, $overlayBlur);
            }, ['-moz-', '-o-', '-webkit-', ''])) : '', empty($bgStyle) ? 'none' : 'all', Core::getInstance()->getCompLanguage()->getSkipHTMLForTag());
        }
    }
    /**
     * Get the "Powered by" link.
     */
    protected function poweredLink()
    {
        $compLanguage = Core::getInstance()->getCompLanguage();
        // TranslatePress / Weglot: We need to ensure that the powered by texts are not translated through `gettext`
        // to avoid tags like `data-gettext`
        $poweredByTexts = Texts::getPoweredByLinkTexts(!$compLanguage instanceof AbstractOutputBufferPlugin);
        $currentPoweredByText = \get_option(Texts::SETTING_POWERED_BY_TEXT, 0);
        $footerText = $compLanguage->translateArray([$poweredByTexts[$currentPoweredByText]])[0];
        return \sprintf('<a href="%s" target="_blank" id="%s-powered-by" %s>%s</a>', \__('https://devowl.io/wordpress-real-cookie-banner/', RCB_TD), Core::getInstance()->getPageRequestUuid4(), $compLanguage->getSkipHTMLForTag(), $footerText);
    }
    /**
     * Getter.
     *
     * @codeCoverageIgnore
     */
    public function getCustomize()
    {
        return $this->customize;
    }
    /**
     * New instance.
     *
     * @codeCoverageIgnore
     */
    public static function instance()
    {
        return new \DevOwl\RealCookieBanner\view\Banner();
    }
}
