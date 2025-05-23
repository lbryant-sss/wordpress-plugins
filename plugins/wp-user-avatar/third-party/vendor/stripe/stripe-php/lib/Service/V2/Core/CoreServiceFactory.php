<?php

namespace ProfilePressVendor\Stripe\Service\V2\Core;

/**
 * Service factory class for API resources in the root namespace.
 * // Doc: The beginning of the section generated from our OpenAPI spec.
 *
 * @property EventDestinationService $eventDestinations
 * @property EventService $events
 * // Doc: The end of the section generated from our OpenAPI spec
 */
class CoreServiceFactory extends \ProfilePressVendor\Stripe\Service\AbstractServiceFactory
{
    /**
     * @var array<string, string>
     */
    private static $classMap = [
        // Class Map: The beginning of the section generated from our OpenAPI spec
        'eventDestinations' => EventDestinationService::class,
        'events' => EventService::class,
    ];
    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
