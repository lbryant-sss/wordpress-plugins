<?php

// File generated from our OpenAPI spec

namespace AmeliaStripe\Billing;

/**
 * A credit grant is an API resource that documents the allocation of some billing credits to a customer.
 *
 * Related guide: <a href="https://docs.stripe.com/billing/subscriptions/usage-based/billing-credits">Billing credits</a>
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property (object{monetary: null|(object{currency: string, value: int}&\AmeliaStripe\StripeObject), type: string}&\AmeliaStripe\StripeObject) $amount
 * @property (object{scope: (object{price_type?: string, prices?: ((object{id: null|string}&\AmeliaStripe\StripeObject))[]}&\AmeliaStripe\StripeObject)}&\AmeliaStripe\StripeObject) $applicability_config
 * @property string $category The category of this credit grant. This is for tracking purposes and isn't displayed to the customer.
 * @property int $created Time at which the object was created. Measured in seconds since the Unix epoch.
 * @property string|\AmeliaStripe\Customer $customer ID of the customer receiving the billing credits.
 * @property null|int $effective_at The time when the billing credits become effective-when they're eligible for use.
 * @property null|int $expires_at The time when the billing credits expire. If not present, the billing credits don't expire.
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property \AmeliaStripe\StripeObject $metadata Set of <a href="https://stripe.com/docs/api/metadata">key-value pairs</a> that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
 * @property null|string $name A descriptive name shown in dashboard.
 * @property null|int $priority The priority for applying this credit grant. The highest priority is 0 and the lowest is 100.
 * @property null|string|\AmeliaStripe\TestHelpers\TestClock $test_clock ID of the test clock this credit grant belongs to.
 * @property int $updated Time at which the object was last updated. Measured in seconds since the Unix epoch.
 * @property null|int $voided_at The time when this credit grant was voided. If not present, the credit grant hasn't been voided.
 */
class CreditGrant extends \AmeliaStripe\ApiResource
{
    const OBJECT_NAME = 'billing.credit_grant';

    use \AmeliaStripe\ApiOperations\Update;

    const CATEGORY_PAID = 'paid';
    const CATEGORY_PROMOTIONAL = 'promotional';

    /**
     * Creates a credit grant.
     *
     * @param null|array{amount: array{monetary?: array{currency: string, value: int}, type: string}, applicability_config: array{scope: array{price_type?: string, prices?: array{id: string}[]}}, category: string, customer: string, effective_at?: int, expand?: string[], expires_at?: int, metadata?: array<string, string>, name?: string, priority?: int} $params
     * @param null|array|string $options
     *
     * @return CreditGrant the created resource
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

    /**
     * Retrieve a list of credit grants.
     *
     * @param null|array{customer?: string, ending_before?: string, expand?: string[], limit?: int, starting_after?: string} $params
     * @param null|array|string $opts
     *
     * @return \AmeliaStripe\Collection<CreditGrant> of ApiResources
     *
     * @throws \AmeliaStripe\Exception\ApiErrorException if the request fails
     */
    public static function all($params = null, $opts = null)
    {
        $url = static::classUrl();

        return static::_requestPage($url, \AmeliaStripe\Collection::class, $params, $opts);
    }

    /**
     * Retrieves a credit grant.
     *
     * @param array|string $id the ID of the API resource to retrieve, or an options array containing an `id` key
     * @param null|array|string $opts
     *
     * @return CreditGrant
     *
     * @throws \AmeliaStripe\Exception\ApiErrorException if the request fails
     */
    public static function retrieve($id, $opts = null)
    {
        $opts = \AmeliaStripe\Util\RequestOptions::parse($opts);
        $instance = new static($id, $opts);
        $instance->refresh();

        return $instance;
    }

    /**
     * Updates a credit grant.
     *
     * @param string $id the ID of the resource to update
     * @param null|array{expand?: string[], expires_at?: null|int, metadata?: array<string, string>} $params
     * @param null|array|string $opts
     *
     * @return CreditGrant the updated resource
     *
     * @throws \AmeliaStripe\Exception\ApiErrorException if the request fails
     */
    public static function update($id, $params = null, $opts = null)
    {
        self::_validateParams($params);
        $url = static::resourceUrl($id);

        list($response, $opts) = static::_staticRequest('post', $url, $params, $opts);
        $obj = \AmeliaStripe\Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }

    /**
     * @param null|array $params
     * @param null|array|string $opts
     *
     * @return CreditGrant the expired credit grant
     *
     * @throws \AmeliaStripe\Exception\ApiErrorException if the request fails
     */
    public function expire($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/expire';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

    /**
     * @param null|array $params
     * @param null|array|string $opts
     *
     * @return CreditGrant the voided credit grant
     *
     * @throws \AmeliaStripe\Exception\ApiErrorException if the request fails
     */
    public function voidGrant($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/void';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }
}
