<?php

namespace PaymentPlugins\WooCommerce\PPCP\Factories;

use PaymentPlugins\PayPalSDK\Shipping;

class ShippingFactory extends AbstractFactory {

	public function from_customer( $prefix = 'shipping' ) {
		return ( new Shipping() )
			->setName( $this->factories->name->from_customer( $prefix ) )
			->setAddress( $this->factories->address->from_customer( $prefix ) )
			->setOptions( $this->factories->shippingOptions->from_cart() );
	}

	public function from_order( $prefix = 'shipping', $include_shipping = false ) {
		return ( new Shipping() )
			->setName( $this->factories->name->from_order( $prefix ) )
			->setAddress( $this->factories->address->from_order( $prefix ) );
		//->setOptions( $this->factories->shippingOptions->from_order() );
	}

}