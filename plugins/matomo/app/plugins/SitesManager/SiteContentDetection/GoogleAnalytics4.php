<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\SitesManager\SiteContentDetection;

class GoogleAnalytics4 extends \Piwik\Plugins\SitesManager\SiteContentDetection\SiteContentDetectionAbstract
{
    public static function getName() : string
    {
        return 'Google Analytics 4';
    }
    public static function getContentType() : int
    {
        return self::TYPE_TRACKER;
    }
    public function isDetected(?string $data = null, ?array $headers = null) : bool
    {
        if (empty($data)) {
            return \false;
        }
        if (strpos($data, 'gtag.js') !== \false) {
            return \true;
        }
        $tests = ["/properties\\/[^\\/]/", "/G-[A-Z0-9]{7,10}/", "/gtag\\/js\\?id=G-/"];
        foreach ($tests as $test) {
            if (preg_match($test, $data) === 1) {
                return \true;
            }
        }
        return \false;
    }
}
