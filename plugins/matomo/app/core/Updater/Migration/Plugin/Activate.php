<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Updater\Migration\Plugin;

use Piwik\Config;
use Piwik\Plugin;
use Piwik\Updater\Migration;
/**
 * Activates the given plugin during the update
 */
class Activate extends Migration
{
    /**
     * @var string
     */
    private $pluginName;
    /**
     * @var Plugin\Manager
     */
    private $pluginManager;
    public function __construct(Plugin\Manager $pluginManager, $pluginName)
    {
        $this->pluginManager = $pluginManager;
        $this->pluginName = $pluginName;
    }
    public function __toString()
    {
        $domain = Config::getLocalConfigPath() === Config::getDefaultLocalConfigPath() ? '' : Config::getHostname();
        $domainArg = !empty($domain) ? "--matomo-domain=\"{$domain}\" " : '';
        return sprintf('./console %splugin:activate "%s"', $domainArg, $this->pluginName);
    }
    public function shouldIgnoreError($exception)
    {
        return \true;
    }
    public function exec()
    {
        if (!$this->pluginManager->isPluginActivated($this->pluginName)) {
            $this->pluginManager->activatePlugin($this->pluginName);
        }
    }
}
