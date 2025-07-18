<?php

namespace PaymentPlugins\WooCommerce\PPCP\Factories;

use PaymentPlugins\PayPalSDK\OrderApplicationContext;
use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\AdvancedSettings;
use PaymentPlugins\WooCommerce\PPCP\Utilities\LocaleUtil;

class ApplicationContextFactory extends AbstractFactory {

	private $settings;

	public function __construct( AdvancedSettings $settings, ...$args ) {
		$this->settings = $settings;
		parent::__construct( ...$args );
	}

	/**
	 * @param false $needs_shipping
	 *
	 * @return \PaymentPlugins\PayPalSDK\OrderApplicationContext
	 */
	public function get( $needs_shipping = false, $set_provided = false ) {
		$context = new OrderApplicationContext();
		if ( $needs_shipping ) {
			if ( $set_provided ) {
				$context->setShippingPreference( OrderApplicationContext::SET_PROVIDED_ADDRESS );
			} else {
				$context->setShippingPreference( OrderApplicationContext::GET_FROM_FILE );
			}
		} else {
			$context->setShippingPreference( OrderApplicationContext::NO_SHIPPING );
		}
		if ( $this->get_order() ) {
			$context->setReturnUrl( add_query_arg( [
				'order_id'       => $this->order->get_id(),
				'order_key'      => $this->order->get_order_key(),
				'payment_method' => $this->payment_method ? $this->payment_method->id : 'ppcp'
			], WC()->api_request_url( 'ppcp_order_return' ) ) );
			$context->setCancelUrl( add_query_arg( [
				'ppcp_action' => 'canceled',
				'order_id'    => $this->order->get_id(),
			], wc_get_checkout_url() ) );
		} else {
			$context->setReturnUrl( add_query_arg( [
				'_checkoutnonce' => wp_create_nonce( 'checkout-nonce' )
			], WC()->api_request_url( 'ppcp_checkout_return' ) ) );
			$context->setCancelUrl( add_query_arg( [
				'ppcp_action' => 'canceled'
			], wc_get_checkout_url() ) );
		}

		// The display name must have length of 1 or greater
		if ( $this->settings->get_option( 'display_name' ) ) {
			$context->setBrandName( substr( $this->settings->get_option( 'display_name' ), 0, 127 ) );
		}

		if ( $this->settings->is_site_locale() ) {
			$locale = LocaleUtil::get_site_locale( true );
			if ( LocaleUtil::is_locale_supported( $locale, true ) ) {
				$context->setLocale( $locale );
			}
		}

		return $context;
	}

}