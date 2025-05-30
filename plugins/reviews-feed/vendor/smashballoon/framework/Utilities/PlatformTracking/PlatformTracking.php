<?php

namespace SmashBalloon\Reviews\Vendor\Smashballoon\Framework\Utilities\PlatformTracking;

use SmashBalloon\Reviews\Vendor\Smashballoon\Framework\Utilities\PlatformTracking\Platforms\Bluehost;
use SmashBalloon\Reviews\Vendor\Smashballoon\Framework\Utilities\PlatformTracking\Platforms\Flywheel;
use SmashBalloon\Reviews\Vendor\Smashballoon\Framework\Utilities\PlatformTracking\Platforms\GoDadddy;
use SmashBalloon\Reviews\Vendor\Smashballoon\Framework\Utilities\PlatformTracking\Platforms\Kinsta;
use SmashBalloon\Reviews\Vendor\Smashballoon\Framework\Utilities\PlatformTracking\Platforms\SiteGround;
use SmashBalloon\Reviews\Vendor\Smashballoon\Framework\Utilities\PlatformTracking\Platforms\WPEngine;
/** @internal */
class PlatformTracking
{
    /**
     * PlatformTracking constructor.
     */
    public function __construct()
    {
        $this->register_platforms();
    }
    /**
     * Register the hosting platforms.
     *
     * @return void
     */
    public function register_platforms()
    {
        $kinsta = new Kinsta();
        $wpengine = new WPEngine();
        $godaddy = new GoDadddy();
        $bluehost = new Bluehost();
        $flywheel = new Flywheel();
        $siteground = new SiteGround();
        $kinsta->register();
        $wpengine->register();
        $godaddy->register();
        $bluehost->register();
        $flywheel->register();
        $siteground->register();
    }
    /**
     * Get the current hosting platform.
     *
     * @return string
     */
    public static function get_platform()
    {
        return apply_filters('sb_hosting_platform', 'unknown');
    }
}
