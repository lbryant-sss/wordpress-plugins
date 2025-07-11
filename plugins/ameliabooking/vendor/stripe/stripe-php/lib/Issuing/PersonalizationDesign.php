<?php

// File generated from our OpenAPI spec

namespace AmeliaStripe\Issuing;

/**
 * A Personalization Design is a logical grouping of a Physical Bundle, card logo, and carrier text that represents a product line.
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property null|string|\AmeliaStripe\File $card_logo The file for the card logo to use with physical bundles that support card logos. Must have a <code>purpose</code> value of <code>issuing_logo</code>.
 * @property null|(object{footer_body: null|string, footer_title: null|string, header_body: null|string, header_title: null|string}&\AmeliaStripe\StripeObject) $carrier_text Hash containing carrier text, for use with physical bundles that support carrier text.
 * @property int $created Time at which the object was created. Measured in seconds since the Unix epoch.
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property null|string $lookup_key A lookup key used to retrieve personalization designs dynamically from a static string. This may be up to 200 characters.
 * @property \AmeliaStripe\StripeObject $metadata Set of <a href="https://stripe.com/docs/api/metadata">key-value pairs</a> that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
 * @property null|string $name Friendly display name.
 * @property PhysicalBundle|string $physical_bundle The physical bundle object belonging to this personalization design.
 * @property (object{is_default: bool, is_platform_default: null|bool}&\AmeliaStripe\StripeObject) $preferences
 * @property (object{card_logo: null|string[], carrier_text: null|string[]}&\AmeliaStripe\StripeObject) $rejection_reasons
 * @property string $status Whether this personalization design can be used to create cards.
 */
class PersonalizationDesign extends \AmeliaStripe\ApiResource
{
    const OBJECT_NAME = 'issuing.personalization_design';

    use \AmeliaStripe\ApiOperations\Update;

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_REJECTED = 'rejected';
    const STATUS_REVIEW = 'review';

    /**
     * Creates a personalization design object.
     *
     * @param null|array{card_logo?: string, carrier_text?: array{footer_body?: null|string, footer_title?: null|string, header_body?: null|string, header_title?: null|string}, expand?: string[], lookup_key?: string, metadata?: array<string, string>, name?: string, physical_bundle: string, preferences?: array{is_default: bool}, transfer_lookup_key?: bool} $params
     * @param null|array|string $options
     *
     * @return PersonalizationDesign the created resource
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
     * Returns a list of personalization design objects. The objects are sorted in
     * descending order by creation date, with the most recently created object
     * appearing first.
     *
     * @param null|array{ending_before?: string, expand?: string[], limit?: int, lookup_keys?: string[], preferences?: array{is_default?: bool, is_platform_default?: bool}, starting_after?: string, status?: string} $params
     * @param null|array|string $opts
     *
     * @return \AmeliaStripe\Collection<PersonalizationDesign> of ApiResources
     *
     * @throws \AmeliaStripe\Exception\ApiErrorException if the request fails
     */
    public static function all($params = null, $opts = null)
    {
        $url = static::classUrl();

        return static::_requestPage($url, \AmeliaStripe\Collection::class, $params, $opts);
    }

    /**
     * Retrieves a personalization design object.
     *
     * @param array|string $id the ID of the API resource to retrieve, or an options array containing an `id` key
     * @param null|array|string $opts
     *
     * @return PersonalizationDesign
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
     * Updates a card personalization object.
     *
     * @param string $id the ID of the resource to update
     * @param null|array{card_logo?: null|string, carrier_text?: null|array{footer_body?: null|string, footer_title?: null|string, header_body?: null|string, header_title?: null|string}, expand?: string[], lookup_key?: null|string, metadata?: array<string, string>, name?: null|string, physical_bundle?: string, preferences?: array{is_default: bool}, transfer_lookup_key?: bool} $params
     * @param null|array|string $opts
     *
     * @return PersonalizationDesign the updated resource
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
}
