<?php

declare (strict_types=1);
namespace Mollie\WooCommerce\PaymentMethods;

class Eps extends \Mollie\WooCommerce\PaymentMethods\AbstractPaymentMethod implements \Mollie\WooCommerce\PaymentMethods\PaymentMethodI
{
    protected function getConfig(): array
    {
        return ['id' => 'eps', 'defaultTitle' => __('EPS', 'mollie-payments-for-woocommerce'), 'settingsDescription' => '', 'defaultDescription' => '', 'paymentFields' => \false, 'instructions' => \false, 'supports' => ['products', 'refunds'], 'filtersOnBuild' => \false, 'confirmationDelayed' => \true, 'SEPA' => \true, 'docs' => 'https://www.mollie.com/gb/payments/eps'];
    }
    public function getFormFields($generalFormFields): array
    {
        return $generalFormFields;
    }
}
