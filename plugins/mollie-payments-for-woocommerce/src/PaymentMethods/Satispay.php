<?php

declare (strict_types=1);
namespace Mollie\WooCommerce\PaymentMethods;

class Satispay extends \Mollie\WooCommerce\PaymentMethods\AbstractPaymentMethod implements \Mollie\WooCommerce\PaymentMethods\PaymentMethodI
{
    protected function getConfig(): array
    {
        return ['id' => 'satispay', 'defaultTitle' => __('Satispay', 'mollie-payments-for-woocommerce'), 'settingsDescription' => '', 'defaultDescription' => '', 'paymentFields' => \false, 'instructions' => \false, 'supports' => ['products', 'refunds'], 'filtersOnBuild' => \false, 'confirmationDelayed' => \false, 'SEPA' => \false, 'docs' => 'https://www.mollie.com/gb/payments/satispay'];
    }
    public function getFormFields($generalFormFields): array
    {
        return $generalFormFields;
    }
}
