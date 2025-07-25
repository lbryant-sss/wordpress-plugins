<?php

// File generated from our OpenAPI spec

namespace AmeliaStripe\Service\Billing;

/**
 * Service factory class for API resources in the Billing namespace.
 *
 * @property AlertService $alerts
 * @property CreditBalanceSummaryService $creditBalanceSummary
 * @property CreditBalanceTransactionService $creditBalanceTransactions
 * @property CreditGrantService $creditGrants
 * @property MeterEventAdjustmentService $meterEventAdjustments
 * @property MeterEventService $meterEvents
 * @property MeterService $meters
 */
class BillingServiceFactory extends \AmeliaStripe\Service\AbstractServiceFactory
{
    /**
     * @var array<string, string>
     */
    private static $classMap = [
        'alerts' => AlertService::class,
        'creditBalanceSummary' => CreditBalanceSummaryService::class,
        'creditBalanceTransactions' => CreditBalanceTransactionService::class,
        'creditGrants' => CreditGrantService::class,
        'meterEventAdjustments' => MeterEventAdjustmentService::class,
        'meterEvents' => MeterEventService::class,
        'meters' => MeterService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
