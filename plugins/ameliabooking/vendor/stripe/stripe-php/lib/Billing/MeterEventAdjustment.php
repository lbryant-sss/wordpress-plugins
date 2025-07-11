<?php

// File generated from our OpenAPI spec

namespace AmeliaStripe\Billing;

/**
 * A billing meter event adjustment is a resource that allows you to cancel a meter event. For example, you might create a billing meter event adjustment to cancel a meter event that was created in error or attached to the wrong customer.
 *
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property null|(object{identifier: null|string}&\AmeliaStripe\StripeObject) $cancel Specifies which event to cancel.
 * @property string $event_name The name of the meter event. Corresponds with the <code>event_name</code> field on a meter.
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property string $status The meter event adjustment's status.
 * @property string $type Specifies whether to cancel a single event or a range of events for a time period. Time period cancellation is not supported yet.
 */
class MeterEventAdjustment extends \AmeliaStripe\ApiResource
{
    const OBJECT_NAME = 'billing.meter_event_adjustment';

    const STATUS_COMPLETE = 'complete';
    const STATUS_PENDING = 'pending';

    /**
     * Creates a billing meter event adjustment.
     *
     * @param null|array{cancel?: array{identifier?: string}, event_name: string, expand?: string[], type: string} $params
     * @param null|array|string $options
     *
     * @return MeterEventAdjustment the created resource
     *
     * @throws \AmeliaStripe\Exception\ApiErrorException if the request fails
     */
    public static function create($params = null, $options = null)
    {
        self::_validateParams($params);
        $url = static::classUrl();

        list($response, $opts) = static::_staticRequest('post', $url, $params, $options);
        $obj = \AmeliaStripe\Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }
}
