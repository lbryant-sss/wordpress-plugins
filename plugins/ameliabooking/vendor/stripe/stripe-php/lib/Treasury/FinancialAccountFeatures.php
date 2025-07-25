<?php

// File generated from our OpenAPI spec

namespace AmeliaStripe\Treasury;

/**
 * Encodes whether a FinancialAccount has access to a particular Feature, with a <code>status</code> enum and associated <code>status_details</code>.
 * Stripe or the platform can control Features via the requested field.
 *
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property null|(object{requested: bool, status: string, status_details: ((object{code: string, resolution: null|string, restriction?: string}&\AmeliaStripe\StripeObject))[]}&\AmeliaStripe\StripeObject) $card_issuing Toggle settings for enabling/disabling a feature
 * @property null|(object{requested: bool, status: string, status_details: ((object{code: string, resolution: null|string, restriction?: string}&\AmeliaStripe\StripeObject))[]}&\AmeliaStripe\StripeObject) $deposit_insurance Toggle settings for enabling/disabling a feature
 * @property null|(object{aba?: (object{requested: bool, status: string, status_details: ((object{code: string, resolution: null|string, restriction?: string}&\AmeliaStripe\StripeObject))[]}&\AmeliaStripe\StripeObject)}&\AmeliaStripe\StripeObject) $financial_addresses Settings related to Financial Addresses features on a Financial Account
 * @property null|(object{ach?: (object{requested: bool, status: string, status_details: ((object{code: string, resolution: null|string, restriction?: string}&\AmeliaStripe\StripeObject))[]}&\AmeliaStripe\StripeObject)}&\AmeliaStripe\StripeObject) $inbound_transfers InboundTransfers contains inbound transfers features for a FinancialAccount.
 * @property null|(object{requested: bool, status: string, status_details: ((object{code: string, resolution: null|string, restriction?: string}&\AmeliaStripe\StripeObject))[]}&\AmeliaStripe\StripeObject) $intra_stripe_flows Toggle settings for enabling/disabling a feature
 * @property null|(object{ach?: (object{requested: bool, status: string, status_details: ((object{code: string, resolution: null|string, restriction?: string}&\AmeliaStripe\StripeObject))[]}&\AmeliaStripe\StripeObject), us_domestic_wire?: (object{requested: bool, status: string, status_details: ((object{code: string, resolution: null|string, restriction?: string}&\AmeliaStripe\StripeObject))[]}&\AmeliaStripe\StripeObject)}&\AmeliaStripe\StripeObject) $outbound_payments Settings related to Outbound Payments features on a Financial Account
 * @property null|(object{ach?: (object{requested: bool, status: string, status_details: ((object{code: string, resolution: null|string, restriction?: string}&\AmeliaStripe\StripeObject))[]}&\AmeliaStripe\StripeObject), us_domestic_wire?: (object{requested: bool, status: string, status_details: ((object{code: string, resolution: null|string, restriction?: string}&\AmeliaStripe\StripeObject))[]}&\AmeliaStripe\StripeObject)}&\AmeliaStripe\StripeObject) $outbound_transfers OutboundTransfers contains outbound transfers features for a FinancialAccount.
 */
class FinancialAccountFeatures extends \AmeliaStripe\ApiResource
{
    const OBJECT_NAME = 'treasury.financial_account_features';
}
