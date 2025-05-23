<?php

declare(strict_types=1);

namespace Prli\Caseproof\GrowthTools;

/**
 * Returns the main instance of the plugin.
 *
 * @param  array $config Config data.
 * @return Prli\Caseproof\GrowthTools\Bootstrap
 */
function instance(array $config = []): App
{
    static $instance = null;

    if (is_null($instance)) {
        $instance = new App(new Config($config));
    }

    return $instance;
}
