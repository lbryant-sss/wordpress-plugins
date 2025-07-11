<?php

namespace DevOwl\RealCookieBanner\Vendor\DevOwl\HeadlessContentBlocker\plugins\selectorSyntaxFunction;

use DevOwl\RealCookieBanner\Vendor\DevOwl\FastHtmlTag\finder\match\AbstractMatch;
use DevOwl\RealCookieBanner\Vendor\DevOwl\FastHtmlTag\finder\SelectorSyntaxAttributeFunction;
use DevOwl\RealCookieBanner\Vendor\DevOwl\HeadlessContentBlocker\AbstractPlugin;
use DevOwl\RealCookieBanner\Vendor\DevOwl\HeadlessContentBlocker\Constants;
use DevOwl\RealCookieBanner\Vendor\DevOwl\HeadlessContentBlocker\finder\match\MatchPluginCallbacks;
/**
 * This plugin registers the selector syntax `confirm()`.
 *
 * ```
 * a[data-href*="youtube.com"][class*="my-lightbox":confirm()]
 * ```
 *
 * Parameters:
 *
 * - `[fixed=false]` (boolean): If true, the dialog will be fixed to the bottom of the screen
 *
 * When called for an attribute it will mark the individual node in `confirm` mode (similar to `window.confirm()`). That means,
 * instead of rendering a visual content blocker for the blocked node, the blocked node is still rendered as-is but the
 * `click` event gets caught. Afterward, a confirm dialog with a visual content blocker (text-mode) will be shown.
 *
 * Attention: You need to enable the visual content blocker in your blocker settings!
 * @internal
 */
class Confirm extends AbstractPlugin
{
    // Documented in AbstractPlugin
    public function init()
    {
        $this->getHeadlessContentBlocker()->addSelectorSyntaxFunction('confirm', [$this, 'fn']);
    }
    /**
     * Function implementation.
     *
     * @param SelectorSyntaxAttributeFunction $fn
     * @param AbstractMatch $match
     * @param mixed $value
     */
    public function fn($fn, $match, $value)
    {
        $fixed = $fn->getArgument('fixed', 'false') === 'true';
        MatchPluginCallbacks::getFromMatch($match)->addBlockedMatchCallback(function ($result) use($match, $fixed) {
            if ($result->isBlocked()) {
                if (!$match->hasAttribute(Constants::HTML_ATTRIBUTE_DELEGATE_CLICK)) {
                    $match->setAttribute(Constants::HTML_ATTRIBUTE_DELEGATE_CLICK, \json_encode(['selector' => 'self']));
                }
                $match->setAttribute(Constants::HTML_ATTRIBUTE_CONFIRM, \json_encode(['fixed' => $fixed]));
            }
        });
        return \true;
    }
}
