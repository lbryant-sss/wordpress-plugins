<?php


namespace PaymentPlugins\WooCommerce\PPCP\Container;


class Container {

	/**
	 * @var BaseResolver[]
	 */
	private $registry = [];

	public function register( $id, $value, $singleton = true ) {
		if ( empty( $this->registry[ $id ] ) ) {
			$this->registry[ $id ] = new BaseResolver( $value, $singleton );
		}
	}

	public function get( $id ) {
		if ( empty( $this->registry[ $id ] ) ) {
			throw new \Exception( sprintf( 'There is no callback registered for id %s', $id ) );
		}

		return $this->registry[ $id ]->get( $this );
	}
}