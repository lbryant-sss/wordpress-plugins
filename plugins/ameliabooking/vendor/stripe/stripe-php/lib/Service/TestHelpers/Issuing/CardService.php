<?php

// File generated from our OpenAPI spec

namespace AmeliaStripe\Service\TestHelpers\Issuing;

/**
 * @phpstan-import-type RequestOptionsArray from \AmeliaStripe\Util\RequestOptions
 *
 * @psalm-import-type RequestOptionsArray from \AmeliaStripe\Util\RequestOptions
 */
class CardService extends \AmeliaStripe\Service\AbstractService
{
    /**
     * Updates the shipping status of the specified Issuing <code>Card</code> object to
     * <code>delivered</code>.
     *
     * @param string $id
     * @param null|array{expand?: string[]} $params
     * @param null|RequestOptionsArray|\AmeliaStripe\Util\RequestOptions $opts
     *
     * @return \AmeliaStripe\Issuing\Card
     *
     * @throws \AmeliaStripe\Exception\ApiErrorException if the request fails
     */
    public function deliverCard($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/test_helpers/issuing/cards/%s/shipping/deliver', $id), $params, $opts);
    }

    /**
     * Updates the shipping status of the specified Issuing <code>Card</code> object to
     * <code>failure</code>.
     *
     * @param string $id
     * @param null|array{expand?: string[]} $params
     * @param null|RequestOptionsArray|\AmeliaStripe\Util\RequestOptions $opts
     *
     * @return \AmeliaStripe\Issuing\Card
     *
     * @throws \AmeliaStripe\Exception\ApiErrorException if the request fails
     */
    public function failCard($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/test_helpers/issuing/cards/%s/shipping/fail', $id), $params, $opts);
    }

    /**
     * Updates the shipping status of the specified Issuing <code>Card</code> object to
     * <code>returned</code>.
     *
     * @param string $id
     * @param null|array{expand?: string[]} $params
     * @param null|RequestOptionsArray|\AmeliaStripe\Util\RequestOptions $opts
     *
     * @return \AmeliaStripe\Issuing\Card
     *
     * @throws \AmeliaStripe\Exception\ApiErrorException if the request fails
     */
    public function returnCard($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/test_helpers/issuing/cards/%s/shipping/return', $id), $params, $opts);
    }

    /**
     * Updates the shipping status of the specified Issuing <code>Card</code> object to
     * <code>shipped</code>.
     *
     * @param string $id
     * @param null|array{expand?: string[]} $params
     * @param null|RequestOptionsArray|\AmeliaStripe\Util\RequestOptions $opts
     *
     * @return \AmeliaStripe\Issuing\Card
     *
     * @throws \AmeliaStripe\Exception\ApiErrorException if the request fails
     */
    public function shipCard($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/test_helpers/issuing/cards/%s/shipping/ship', $id), $params, $opts);
    }

    /**
     * Updates the shipping status of the specified Issuing <code>Card</code> object to
     * <code>submitted</code>. This method requires Stripe Version ‘2024-09-30.acacia’
     * or later.
     *
     * @param string $id
     * @param null|array{expand?: string[]} $params
     * @param null|RequestOptionsArray|\AmeliaStripe\Util\RequestOptions $opts
     *
     * @return \AmeliaStripe\Issuing\Card
     *
     * @throws \AmeliaStripe\Exception\ApiErrorException if the request fails
     */
    public function submitCard($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/test_helpers/issuing/cards/%s/shipping/submit', $id), $params, $opts);
    }
}
