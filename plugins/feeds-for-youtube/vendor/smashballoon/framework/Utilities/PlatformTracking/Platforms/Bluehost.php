<?php

namespace Smashballoon\Framework\Utilities\PlatformTracking\Platforms;

/** @internal */
class Bluehost implements \Smashballoon\Framework\Utilities\PlatformTracking\Platforms\PlatformInterface
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        \add_filter('sb_hosting_platform', [$this, 'filter_sb_hosting_platform']);
    }
    /**
     * @inheritDoc
     */
    public function filter_sb_hosting_platform($platform)
    {
        if (\defined('BLUEHOST_PLUGIN_VERSION')) {
            $platform = 'bluehost';
        }
        return $platform;
    }
}
