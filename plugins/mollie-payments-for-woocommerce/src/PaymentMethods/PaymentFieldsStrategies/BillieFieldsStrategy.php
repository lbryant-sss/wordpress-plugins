<?php

declare (strict_types=1);
namespace Mollie\WooCommerce\PaymentMethods\PaymentFieldsStrategies;

class BillieFieldsStrategy implements \Mollie\WooCommerce\PaymentMethods\PaymentFieldsStrategies\PaymentFieldsStrategyI
{
    public const FIELD_COMPANY = 'billing_company_billie';
    public function execute($gateway, $dataHelper)
    {
        $showCompanyField = \false;
        if (is_checkout_pay_page()) {
            $order = $this->getOrderIdOnPayForOrderPage();
            $showCompanyField = empty($order->get_billing_company());
        }
        $companyFieldIsRequiredByWoo = $this->isCompanyFieldIsRequiredByWoo();
        $hideCompanyFieldFilter = apply_filters('mollie_wc_hide_company_field', \false);
        if (is_checkout() && !is_checkout_pay_page() && !$companyFieldIsRequiredByWoo && !$hideCompanyFieldFilter) {
            $showCompanyField = \true;
        }
        if ($showCompanyField) {
            $this->company();
        }
    }
    protected function getOrderIdOnPayForOrderPage()
    {
        global $wp;
        $orderId = absint($wp->query_vars['order-pay']);
        return wc_get_order($orderId);
    }
    protected function company()
    {
        ?>
        <p class="form-row form-row-wide" id="billing_company_field">
            <label for="<?php 
        echo esc_attr(self::FIELD_COMPANY);
        ?>" class=""><?php 
        echo esc_html__('Company', 'mollie-payments-for-woocommerce');
        ?>
                <abbr class="required" title="required">*</abbr>
            </label>
            <span class="woocommerce-input-wrapper">
        <input type="tel" class="input-text " name="<?php 
        echo esc_attr(self::FIELD_COMPANY);
        ?>" id="<?php 
        echo esc_attr(self::FIELD_COMPANY);
        ?>"
               placeholder="Company name"
               value="" autocomplete="organization">
        </span>
        </p>
        <?php 
    }
    public function getFieldMarkup($gateway, $dataHelper)
    {
        return "";
    }
    /**
     *
     * @return bool
     */
    public function isCompanyFieldIsRequiredByWoo(): bool
    {
        $checkoutFields = WC()->checkout()->get_checkout_fields();
        $billingCompanyFieldIsRequiredByWoo = isset($checkoutFields['billing']['billing_company']['required']) && $checkoutFields['billing']['billing_company']['required'] === \true;
        $shippingCompanyFieldIsRequiredByWoo = isset($checkoutFields['shipping']['shipping_company']['required']) && $checkoutFields['shipping']['shipping_company']['required'] === \true;
        return $billingCompanyFieldIsRequiredByWoo || $shippingCompanyFieldIsRequiredByWoo;
    }
}
