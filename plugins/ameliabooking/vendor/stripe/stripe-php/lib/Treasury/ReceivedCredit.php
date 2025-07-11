<?php

// File generated from our OpenAPI spec

namespace AmeliaStripe\Treasury;

/**
 * ReceivedCredits represent funds sent to a <a href="https://stripe.com/docs/api#financial_accounts">FinancialAccount</a> (for example, via ACH or wire). These money movements are not initiated from the FinancialAccount.
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property int $amount Amount (in cents) transferred.
 * @property int $created Time at which the object was created. Measured in seconds since the Unix epoch.
 * @property string $currency Three-letter <a href="https://www.iso.org/iso-4217-currency-codes.html">ISO currency code</a>, in lowercase. Must be a <a href="https://stripe.com/docs/currencies">supported currency</a>.
 * @property string $description An arbitrary string attached to the object. Often useful for displaying to users.
 * @property null|string $failure_code Reason for the failure. A ReceivedCredit might fail because the receiving FinancialAccount is closed or frozen.
 * @property null|string $financial_account The FinancialAccount that received the funds.
 * @property null|string $hosted_regulatory_receipt_url A <a href="https://stripe.com/docs/treasury/moving-money/regulatory-receipts">hosted transaction receipt</a> URL that is provided when money movement is considered regulated under Stripe's money transmission licenses.
 * @property (object{balance?: string, billing_details: (object{address: (object{city: null|string, country: null|string, line1: null|string, line2: null|string, postal_code: null|string, state: null|string}&\AmeliaStripe\StripeObject), email: null|string, name: null|string}&\AmeliaStripe\StripeObject), financial_account?: (object{id: string, network: string}&\AmeliaStripe\StripeObject), issuing_card?: string, type: string, us_bank_account?: (object{bank_name: null|string, last4: null|string, routing_number: null|string}&\AmeliaStripe\StripeObject)}&\AmeliaStripe\StripeObject) $initiating_payment_method_details
 * @property (object{credit_reversal: null|string, issuing_authorization: null|string, issuing_transaction: null|string, source_flow: null|string, source_flow_details?: null|(object{credit_reversal?: CreditReversal, outbound_payment?: OutboundPayment, outbound_transfer?: OutboundTransfer, payout?: \AmeliaStripe\Payout, type: string}&\AmeliaStripe\StripeObject), source_flow_type: null|string}&\AmeliaStripe\StripeObject) $linked_flows
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property string $network The rails used to send the funds.
 * @property null|(object{deadline: null|int, restricted_reason: null|string}&\AmeliaStripe\StripeObject) $reversal_details Details describing when a ReceivedCredit may be reversed.
 * @property string $status Status of the ReceivedCredit. ReceivedCredits are created either <code>succeeded</code> (approved) or <code>failed</code> (declined). If a ReceivedCredit is declined, the failure reason can be found in the <code>failure_code</code> field.
 * @property null|string|Transaction $transaction The Transaction associated with this object.
 */
class ReceivedCredit extends \AmeliaStripe\ApiResource
{
    const OBJECT_NAME = 'treasury.received_credit';

    const FAILURE_CODE_ACCOUNT_CLOSED = 'account_closed';
    const FAILURE_CODE_ACCOUNT_FROZEN = 'account_frozen';
    const FAILURE_CODE_INTERNATIONAL_TRANSACTION = 'international_transaction';
    const FAILURE_CODE_OTHER = 'other';

    const NETWORK_ACH = 'ach';
    const NETWORK_CARD = 'card';
    const NETWORK_STRIPE = 'stripe';
    const NETWORK_US_DOMESTIC_WIRE = 'us_domestic_wire';

    const STATUS_FAILED = 'failed';
    const STATUS_SUCCEEDED = 'succeeded';

    /**
     * Returns a list of ReceivedCredits.
     *
     * @param null|array{ending_before?: string, expand?: string[], financial_account: string, limit?: int, linked_flows?: array{source_flow_type: string}, starting_after?: string, status?: string} $params
     * @param null|array|string $opts
     *
     * @return \AmeliaStripe\Collection<ReceivedCredit> of ApiResources
     *
     * @throws \AmeliaStripe\Exception\ApiErrorException if the request fails
     */
    public static function all($params = null, $opts = null)
    {
        $url = static::classUrl();

        return static::_requestPage($url, \AmeliaStripe\Collection::class, $params, $opts);
    }

    /**
     * Retrieves the details of an existing ReceivedCredit by passing the unique
     * ReceivedCredit ID from the ReceivedCredit list.
     *
     * @param array|string $id the ID of the API resource to retrieve, or an options array containing an `id` key
     * @param null|array|string $opts
     *
     * @return ReceivedCredit
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
}
