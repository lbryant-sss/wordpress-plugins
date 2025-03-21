<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\UserCountry\LocationProvider;

use Piwik\Piwik;
use Piwik\Plugins\UserCountry\LocationProvider;
use Piwik\Url;
/**
 * The disabled LocationProvider, this LocationProvider always returns an empty result set.
 *
 */
class DisabledProvider extends LocationProvider
{
    public const ID = 'disabled';
    public const TITLE = 'General_Disabled';
    /**
     * Guesses a visitor's location using a visitor's browser language.
     *
     * @param array $info Contains 'ip' & 'lang' keys.
     * @return false.
     */
    public function getLocation($info)
    {
        return \false;
    }
    /**
     * Returns whether this location provider is available.
     *
     * This implementation is always available.
     *
     * @return bool  always true
     */
    public function isAvailable()
    {
        return \true;
    }
    /**
     * Returns whether this location provider is working correctly.
     *
     * This implementation is always working correctly.
     *
     * @return bool  always true
     */
    public function isWorking()
    {
        return \true;
    }
    /**
     * Returns an array describing the types of location information this provider will
     * return.
     *
     * @return array
     */
    public function getSupportedLocationInfo()
    {
        return [];
    }
    /**
     * Returns information about this location provider. Contains an id, title & description:
     *
     * array(
     *     'id' => 'default',
     *     'title' => '...',
     *     'description' => '...'
     * );
     *
     * @return array
     */
    public function getInfo()
    {
        $desc = Piwik::translate('UserCountry_DisabledLocationProvider');
        return array('id' => self::ID, 'title' => self::TITLE, 'description' => $desc, 'order' => 0);
    }
    public function getUsageWarning() : ?string
    {
        $comment = Piwik::translate('UserCountry_DefaultLocationProviderDesc1') . ' ';
        $comment .= Piwik::translate('UserCountry_DefaultLocationProviderDesc2', array('<a href="' . Url::addCampaignParametersToMatomoLink('https://matomo.org/docs/geo-locate/') . '" rel="noreferrer noopener" target="_blank">', '', '', '</a>'));
        return $comment;
    }
}
