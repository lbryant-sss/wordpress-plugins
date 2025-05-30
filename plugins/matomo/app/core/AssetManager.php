<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik;

use Exception;
use Piwik\AssetManager\UIAsset;
use Piwik\AssetManager\UIAsset\InMemoryUIAsset;
use Piwik\AssetManager\UIAsset\OnDiskUIAsset;
use Piwik\AssetManager\UIAssetCacheBuster;
use Piwik\AssetManager\UIAssetFetcher\JScriptUIAssetFetcher;
use Piwik\AssetManager\UIAssetFetcher\StaticUIAssetFetcher;
use Piwik\AssetManager\UIAssetFetcher\StylesheetUIAssetFetcher;
use Piwik\AssetManager\UIAssetFetcher\PluginUmdAssetFetcher;
use Piwik\AssetManager\UIAssetFetcher;
use Piwik\AssetManager\UIAssetMerger\JScriptUIAssetMerger;
use Piwik\AssetManager\UIAssetMerger\StylesheetUIAssetMerger;
use Piwik\Container\StaticContainer;
use Piwik\Plugin\Manager;
/**
 * AssetManager is the class used to manage the inclusion of UI assets:
 * JavaScript and CSS files.
 *
 * It performs the following actions:
 *  - Identifies required assets
 *  - Includes assets in the rendered HTML page
 *  - Manages asset merging and minifying
 *  - Manages server-side cache
 *
 * Whether assets are included individually or as merged files is defined by
 * the global option 'disable_merged_assets'. See the documentation in the global
 * config for more information.
 */
class AssetManager extends \Piwik\Singleton
{
    public const MERGED_CSS_FILE = "asset_manager_global_css.css";
    public const MERGED_CORE_JS_FILE = "asset_manager_core_js.js";
    public const MERGED_NON_CORE_JS_FILE = "asset_manager_non_core_js.js";
    public const CSS_IMPORT_DIRECTIVE = "<link rel=\"stylesheet\" type=\"text/css\" href=\"%s\" />\n";
    public const JS_IMPORT_DIRECTIVE = "<script type=\"text/javascript\" src=\"%s\"></script>\n";
    public const JS_DEFER_IMPORT_DIRECTIVE = "<script type=\"text/javascript\" src=\"%s\" defer></script>\n";
    public const GET_CSS_MODULE_ACTION = "index.php?module=Proxy&action=getCss";
    public const GET_CORE_JS_MODULE_ACTION = "index.php?module=Proxy&action=getCoreJs";
    public const GET_NON_CORE_JS_MODULE_ACTION = "index.php?module=Proxy&action=getNonCoreJs";
    public const GET_JS_UMD_MODULE_ACTION = "index.php?module=Proxy&action=getUmdJs&chunk=";
    /**
     * @var UIAssetCacheBuster
     */
    private $cacheBuster;
    /**
     * @var UIAssetFetcher
     */
    private $minimalStylesheetFetcher;
    /**
     * @var Theme
     */
    private $theme;
    public function __construct()
    {
        $this->cacheBuster = UIAssetCacheBuster::getInstance();
        $this->minimalStylesheetFetcher = new StaticUIAssetFetcher(array(), array(), $this->theme);
        $theme = Manager::getInstance()->getThemeEnabled();
        if (!empty($theme)) {
            $this->theme = new \Piwik\Theme();
        }
    }
    /**
     * @inheritDoc
     * @return AssetManager
     */
    public static function getInstance()
    {
        $assetManager = parent::getInstance();
        /**
         * Triggered when creating an instance of the asset manager. Lets you overwrite the
         * asset manager behavior.
         *
         * @param AssetManager &$assetManager
         *
         * @ignore
         * This event is not a public event since we don't want to make the asset manager itself public
         * API
         */
        \Piwik\Piwik::postEvent('AssetManager.makeNewAssetManagerObject', array(&$assetManager));
        return $assetManager;
    }
    /**
     * @param UIAssetCacheBuster $cacheBuster
     */
    public function setCacheBuster($cacheBuster)
    {
        $this->cacheBuster = $cacheBuster;
    }
    /**
     * @param UIAssetFetcher $minimalStylesheetFetcher
     */
    public function setMinimalStylesheetFetcher($minimalStylesheetFetcher)
    {
        $this->minimalStylesheetFetcher = $minimalStylesheetFetcher;
    }
    /**
     * @param Theme $theme
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }
    /**
     * Return CSS file inclusion directive(s) using the markup <link>
     *
     * @return string
     */
    public function getCssInclusionDirective()
    {
        return sprintf(self::CSS_IMPORT_DIRECTIVE, self::GET_CSS_MODULE_ACTION);
    }
    /**
     * Return JS file inclusion directive(s) using the markup <script>
     *
     * @param bool $deferJS
     * @return string
     */
    public function getJsInclusionDirective(bool $deferJS = \false) : string
    {
        $result = "<script type=\"text/javascript\">\n" . StaticContainer::get('Piwik\\Translation\\Translator')->getJavascriptTranslations() . "\n</script>";
        if ($this->isMergedAssetsDisabled()) {
            $this->getMergedCoreJSAsset()->delete();
            $this->getMergedNonCoreJSAsset()->delete();
            $result .= $this->getIndividualCoreAndNonCoreJsIncludes();
        } else {
            $result .= sprintf($deferJS ? self::JS_DEFER_IMPORT_DIRECTIVE : self::JS_IMPORT_DIRECTIVE, self::GET_CORE_JS_MODULE_ACTION);
            $result .= sprintf($deferJS ? self::JS_DEFER_IMPORT_DIRECTIVE : self::JS_IMPORT_DIRECTIVE, self::GET_NON_CORE_JS_MODULE_ACTION);
            $result .= $this->getPluginUmdChunks();
        }
        return $result;
    }
    protected function getPluginUmdChunks()
    {
        $fetcher = $this->getPluginUmdJScriptFetcher();
        $chunks = $fetcher->getChunkFiles();
        $result = '';
        foreach ($chunks as $chunk) {
            $src = self::GET_JS_UMD_MODULE_ACTION . urlencode($chunk->getChunkName());
            $result .= sprintf(self::JS_DEFER_IMPORT_DIRECTIVE, $src);
        }
        return $result;
    }
    /**
     * Return the base.less compiled to css
     *
     * @return UIAsset
     */
    public function getCompiledBaseCss()
    {
        $mergedAsset = new InMemoryUIAsset();
        $assetMerger = new StylesheetUIAssetMerger($mergedAsset, $this->minimalStylesheetFetcher, $this->cacheBuster);
        $assetMerger->generateFile();
        return $mergedAsset;
    }
    /**
     * Return the css merged file absolute location.
     * If there is none, the generation process will be triggered.
     *
     * @return UIAsset
     */
    public function getMergedStylesheet()
    {
        $mergedAsset = $this->getMergedStylesheetAsset();
        $assetFetcher = new StylesheetUIAssetFetcher(Manager::getInstance()->getLoadedPluginsName(), $this->theme);
        $assetMerger = new StylesheetUIAssetMerger($mergedAsset, $assetFetcher, $this->cacheBuster);
        $assetMerger->generateFile();
        return $mergedAsset;
    }
    /**
     * Return the core js merged file absolute location.
     * If there is none, the generation process will be triggered.
     *
     * @return UIAsset
     */
    public function getMergedCoreJavaScript()
    {
        return $this->getMergedJavascript($this->getCoreJScriptFetcher(), $this->getMergedCoreJSAsset());
    }
    /**
     * Return the non core js merged file absolute location.
     * If there is none, the generation process will be triggered.
     *
     * @return UIAsset
     */
    public function getMergedNonCoreJavaScript()
    {
        return $this->getMergedJavascript($this->getNonCoreJScriptFetcher(), $this->getMergedNonCoreJSAsset());
    }
    /**
     * Return a chunk JS merged file absolute location.
     * If there is none, the generation process will be triggered.
     *
     * @param string $chunk The name of the chunk. Will either be a plugin name or an integer.
     * @return UIAsset
     */
    public function getMergedJavaScriptChunk($chunk)
    {
        $assetFetcher = $this->getPluginUmdJScriptFetcher($chunk);
        $outputFile = $assetFetcher->getRequestedChunkOutputFile();
        return $this->getMergedJavascript($assetFetcher, $this->getMergedUIAsset($outputFile));
    }
    /**
     * @param boolean|"all" $core
     * @return string[]
     */
    public function getLoadedPlugins($core)
    {
        $loadedPlugins = array();
        foreach (Manager::getInstance()->getPluginsLoadedAndActivated() as $plugin) {
            $pluginName = $plugin->getPluginName();
            $pluginIsCore = Manager::getInstance()->isPluginBundledWithCore($pluginName);
            if ($core === 'all' || $pluginIsCore && $core || !$pluginIsCore && !$core) {
                $loadedPlugins[] = $pluginName;
            }
        }
        return $loadedPlugins;
    }
    /**
     * Remove previous merged assets
     */
    public function removeMergedAssets($pluginName = \false)
    {
        $assetsToRemove = array($this->getMergedStylesheetAsset());
        if ($pluginName) {
            if ($this->pluginContainsJScriptAssets($pluginName)) {
                if (Manager::getInstance()->isPluginBundledWithCore($pluginName)) {
                    $assetsToRemove[] = $this->getMergedCoreJSAsset();
                } else {
                    $assetsToRemove[] = $this->getMergedNonCoreJSAsset();
                }
                $assetFetcher = $this->getPluginUmdJScriptFetcher();
                foreach ($assetFetcher->getChunkFiles() as $chunk) {
                    $files = $chunk->getFiles();
                    $foundInChunk = \false;
                    foreach ($files as $file) {
                        if (strpos($file, "/{$pluginName}.umd.") !== \false) {
                            $foundInChunk = \true;
                        }
                    }
                    if ($foundInChunk) {
                        $outputFile = $chunk->getOutputFile();
                        $asset = $this->getMergedUIAsset($outputFile);
                        if ($asset->exists()) {
                            $assetsToRemove[] = $asset;
                        }
                        break;
                    }
                }
            }
        } else {
            $assetsToRemove[] = $this->getMergedCoreJSAsset();
            $assetsToRemove[] = $this->getMergedNonCoreJSAsset();
            $assetFetcher = $this->getPluginUmdJScriptFetcher();
            foreach ($assetFetcher->getChunkFiles() as $chunk) {
                $outputFile = $chunk->getOutputFile();
                $asset = $this->getMergedUIAsset($outputFile);
                if ($asset->exists()) {
                    $assetsToRemove[] = $asset;
                }
            }
        }
        $this->removeAssets($assetsToRemove);
    }
    /**
     * Check if the merged file directory exists and is writable.
     *
     * @return string The directory location
     * @throws Exception if directory is not writable.
     */
    public function getAssetDirectory()
    {
        $mergedFileDirectory = StaticContainer::get('path.tmp') . '/assets';
        if (!is_dir($mergedFileDirectory)) {
            \Piwik\Filesystem::mkdir($mergedFileDirectory);
        }
        if (!is_writable($mergedFileDirectory)) {
            throw new Exception("Directory " . $mergedFileDirectory . " has to be writable.");
        }
        return $mergedFileDirectory;
    }
    /**
     * Return the global option disable_merged_assets
     *
     * @return boolean
     */
    public function isMergedAssetsDisabled()
    {
        if (\Piwik\Config::getInstance()->Development['disable_merged_assets'] == 1) {
            return \true;
        }
        if (isset($_GET['disable_merged_assets']) && $_GET['disable_merged_assets'] == 1) {
            return \true;
        }
        return \false;
    }
    /**
     * @param UIAssetFetcher $assetFetcher
     * @param UIAsset $mergedAsset
     * @return UIAsset
     */
    private function getMergedJavascript($assetFetcher, $mergedAsset)
    {
        $assetMerger = new JScriptUIAssetMerger($mergedAsset, $assetFetcher, $this->cacheBuster);
        $assetMerger->generateFile();
        return $mergedAsset;
    }
    /**
     * Return individual JS file inclusion directive(s) using the markup <script>
     *
     * @return string
     */
    protected function getIndividualCoreAndNonCoreJsIncludes() : string
    {
        return $this->getIndividualJsIncludesFromAssetFetcher($this->getCoreJScriptFetcher()) . $this->getIndividualJsIncludesFromAssetFetcher($this->getNonCoreJScriptFetcher()) . $this->getIndividualJsIncludesFromAssetFetcher($this->getPluginUmdJScriptFetcher());
    }
    /**
     * @param UIAssetFetcher $assetFetcher
     * @return string
     */
    protected function getIndividualJsIncludesFromAssetFetcher($assetFetcher) : string
    {
        $jsIncludeString = '';
        $assets = $assetFetcher->getCatalog()->getAssets();
        foreach ($assets as $jsFile) {
            $jsFile->validateFile();
            $jsIncludeString = $jsIncludeString . sprintf(self::JS_IMPORT_DIRECTIVE, $jsFile->getRelativeLocation());
        }
        return $jsIncludeString;
    }
    private function getCoreJScriptFetcher()
    {
        return new JScriptUIAssetFetcher($this->getLoadedPlugins(\true), $this->theme);
    }
    protected function getNonCoreJScriptFetcher()
    {
        return new JScriptUIAssetFetcher($this->getLoadedPlugins(\false), $this->theme);
    }
    protected function getPluginUmdJScriptFetcher($chunk = null)
    {
        return new PluginUmdAssetFetcher($this->getLoadedPlugins('all'), $this->theme, $chunk);
    }
    /**
     * @param string $pluginName
     * @return boolean
     */
    private function pluginContainsJScriptAssets($pluginName)
    {
        $fetcher = new JScriptUIAssetFetcher(array($pluginName), $this->theme);
        try {
            $assets = $fetcher->getCatalog()->getAssets();
        } catch (\Exception $e) {
            // This can happen when a plugin is not valid (eg. Piwik 1.x format)
            // When posting the event to the plugin, it returns an exception "Plugin has not been loaded"
            return \false;
        }
        $pluginManager = Manager::getInstance();
        $plugin = null;
        if ($pluginManager->isPluginLoaded($pluginName)) {
            $plugin = $pluginManager->getLoadedPlugin($pluginName);
        }
        if ($plugin && $plugin->isTheme()) {
            $theme = $pluginManager->getTheme($pluginName);
            $javaScriptFiles = $theme->getJavaScriptFiles();
            if (!empty($javaScriptFiles)) {
                $assets = array_merge($assets, $javaScriptFiles);
            }
        }
        return !empty($assets);
    }
    /**
     * @param UIAsset[] $uiAssets
     */
    public function removeAssets($uiAssets)
    {
        foreach ($uiAssets as $uiAsset) {
            $uiAsset->delete();
        }
    }
    /**
     * @return UIAsset
     */
    public function getMergedStylesheetAsset()
    {
        return $this->getMergedUIAsset(self::MERGED_CSS_FILE);
    }
    /**
     * @return UIAsset
     */
    private function getMergedCoreJSAsset()
    {
        return $this->getMergedUIAsset(self::MERGED_CORE_JS_FILE);
    }
    /**
     * @return UIAsset
     */
    protected function getMergedNonCoreJSAsset()
    {
        return $this->getMergedUIAsset(self::MERGED_NON_CORE_JS_FILE);
    }
    /**
     * @param string $fileName
     * @return UIAsset
     */
    private function getMergedUIAsset($fileName)
    {
        return new OnDiskUIAsset($this->getAssetDirectory(), $fileName);
    }
    public static function compileCustomStylesheets($files)
    {
        $assetManager = new \Piwik\AssetManager();
        $fetcher = new StaticUIAssetFetcher($files, $priorityOrder = array(), $theme = null);
        $assetManager->setMinimalStylesheetFetcher($fetcher);
        return $assetManager->getCompiledBaseCss()->getContent();
    }
    public static function compileCustomJs($files)
    {
        $mergedAsset = new InMemoryUIAsset();
        $fetcher = new StaticUIAssetFetcher($files, $priorityOrder = array(), $theme = null);
        $cacheBuster = UIAssetCacheBuster::getInstance();
        $assetMerger = new JScriptUIAssetMerger($mergedAsset, $fetcher, $cacheBuster);
        $assetMerger->generateFile();
        return $mergedAsset->getContent();
    }
}
