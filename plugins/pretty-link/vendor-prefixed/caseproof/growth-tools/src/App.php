<?php

declare(strict_types=1);

namespace Prli\Caseproof\GrowthTools;

use Prli\Caseproof\GrowthTools\Helper\AddonHelper;

/**
 * Main plugin application.
 *
 * @see \Prli\Caseproof\GrowthTools\instance() Instead of instantiating this class directly,
 *                                       retrieve the main instance using this function.
 */
class App
{
    /**
     * Configuration for the App.
     *
     * @var Config
     */
    protected Config $config;

    /**
     * Ajax Action prefix.
     *
     * @var string
     */
    private const AJAX_ACTION_PREFIX = 'caseproof_growth_tool_addon_action_';

    /**
     * Constructor.
     *
     * @param Config $config Config object.
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->addHooks();
    }

    /**
     * Registers WordPress hooks necessary to bootstrap the plugin.
     */
    public function addHooks(): void
    {
        add_action('admin_menu', [$this, 'addMenu'], 9999);
        add_action('wp_ajax_' . self::AJAX_ACTION_PREFIX . $this->config->instanceId, [$this, 'addonAction']);
    }

    /**
     * Add menu into WordPress admin.
     */
    public function addMenu()
    {
        add_submenu_page(
            $this->config->parentMenuSlug ?? 'tools.php',
            __('Growth Tools', 'pretty-link'),
            __('Growth Tools', 'pretty-link'),
            'activate_plugins',
            $this->config->menuSlug ?? 'growth-tools',
            [$this, 'renderPage']
        );
    }

    /**
     * Add inline CSS.
     *
     * @param string $inlineCSS CSS styles as string.
     */
    protected function addInlineCSS(string $inlineCSS)
    {
        wp_add_inline_style('caseproof_grtl-growth-tools-style', $inlineCSS);
    }

    /**
     * Render html page.
     *
     * @return void
     */
    public function renderPage()
    {
        wp_enqueue_script('caseproof_grtl-growth-tools-script', $this->config->assetsUrl . '/main.min.js', []);
        wp_enqueue_style('caseproof_grtl-growth-tools-style', $this->config->assetsUrl . '/main.min.css', []);
        $inlineCSS = $this->config->customInlineCSS;
        $this->addInlineCSS(is_callable($inlineCSS) ? $inlineCSS() : $inlineCSS);
        $growthToolsData   = $this->config->getAddonsConfig();
        $addonsPluginsData = is_array($growthToolsData['plugins'] ?? false) ?
                $growthToolsData['plugins'] : [];
        $addonsThemesData  = is_array($growthToolsData['themes'] ?? false) ?
                $growthToolsData['themes'] : [];
        $addons            = array_merge($addonsPluginsData, $addonsThemesData);
        $addonsStatusData  = $this->config->getAddonsStatus();
        $addonsStatus      = array_merge(
            $addonsStatusData['plugins'] ?? [],
            $addonsStatusData['themes'] ?? []
        );

        // Sort addons.
        $addons = AddonHelper::sortAddons($addons);

        $labels      = [
            'notinstalled' => esc_html(__('Not Installed', 'pretty-link')),
            'installed'    => esc_html(__('Installed', 'pretty-link')),
            'activated'    => esc_html(__('Active', 'pretty-link')),
            'active'       => esc_html(__('Activate', 'pretty-link')),
            'deactive'     => esc_html(__('Deactivate', 'pretty-link')),
            'install'      => esc_html(__('Install', 'pretty-link')),
        ];
        $ajaxAction  = self::AJAX_ACTION_PREFIX . $this->config->instanceId;
        $baseLogoUrl = $this->config->imageBaseUrl;
        $buttonCSS   = $this->config->buttonCSSClasses;
        $headerHTML  = $this->config->headerHtmlCallback;

        require 'views/list.phtml';
    }

    /**
     * Ajax handler for install/activate plugin.
     *
     * Deprecated function.
     */
    public function pluginAction()
    {
        _deprecated_function(__METHOD__, '1.4.0', 'App::addonAction'); // Send a deprecation warning.
        $this->addonAction();
    }

    /**
     * Ajax handler for install/activate addon.
     */
    public function addonAction()
    {
        $growthToolsData   = $this->config->getAddonsConfig();
        $addonsPluginsData = is_array($growthToolsData['plugins'] ?? false) ?
                $growthToolsData['plugins'] : [];
        $addonsThemesData  = is_array($growthToolsData['themes'] ?? false) ?
                $growthToolsData['themes'] : [];
        $addons            = array_merge($addonsPluginsData, $addonsThemesData);
        if (empty($addons)) {
            return;
        }

        $type      = sanitize_text_field($_REQUEST['type']);
        $addonMain = sanitize_text_field($_REQUEST['addon']);

        if ($type === 'install') {
            foreach ($addons as $addon) {
                if (($addon['main']['free'] ?? null) === $addonMain) {
                    $this->installAddon($addon['download_url'], $addon['addon_type']);
                }
            }
        } elseif ($type === 'activate') {
            foreach ($addons as $addon) {
                if (in_array($addonMain, $addon['main'], true)) {
                    $this->activateAddon($addonMain, $addon['addon_type']);
                }
            }
        } elseif ($type === 'deactivate') {
            foreach ($addons as $addon) {
                if (in_array($addonMain, $addon['main'], true)) {
                    $this->deactivateAddon($addonMain, $addon['addon_type']);
                }
            }
        }
    }

    /**
     * Install plugin.
     *
     * @param string $link      Download link.
     * @param string $addonType Type of addon.
     */
    protected function installAddon(string $link, string $addonType = 'plugin')
    {
        AddonHelper::installAddon($link, $addonType);
    }

    /**
     * Activate plugin.
     *
     * @param string $file      Name of the plugin.
     * @param string $addonType Type of addon.
     */
    protected function activateAddon(string $file, string $addonType = 'plugin')
    {
        AddonHelper::activateAddon($file, $addonType);
    }

    /**
     * Deactivate plugin.
     *
     * @param string $file      Name of the plugin.
     * @param string $addonType Type of addon.
     */
    protected function deactivateAddon(string $file, string $addonType = 'plugin')
    {
        AddonHelper::deactivateAddon($file, $addonType);
    }

    /**
     * Render header contents.
     *
     * @return string
     */
    public static function getHeaderHtml(): string
    {
        return '<h1 class="wp-heading-inline">' . esc_html__('Growth Tools', 'pretty-link') . '</h1>';
    }
}
