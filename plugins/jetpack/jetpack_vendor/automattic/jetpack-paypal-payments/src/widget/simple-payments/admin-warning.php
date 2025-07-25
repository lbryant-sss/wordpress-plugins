<?php
/**
 * Display the Pay with PayPal Form.
 *
 * @html-template Automattic\Jetpack\Paypal_Payments\Widgets\Simple_Payments_Widget::form
 * @package automattic/jetpack
 */

// phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- HTML template, let Phan handle it.

?>
<div class='jetpack-simple-payments-disabled-error'>
	<p>
		<?php
		/**
		 * Show error and help if Pay with PayPal is disabled.
		 *
		 * @package automattic/jetpack
		 */

			$support_url = ( defined( 'IS_WPCOM' ) && IS_WPCOM )
				? 'https://wordpress.com/support/pay-with-paypal/'
				: 'https://jetpack.com/support/pay-with-paypal/';
			printf(
				wp_kses(
					// translators: variable is a link to the support page.
					__( 'Your plan doesn\'t include Pay with PayPal. <a href="%s" rel="noopener noreferrer" target="_blank">Learn more and upgrade</a>.', 'jetpack-paypal-payments' ),
					array(
						'a' => array(
							'href'   => array(),
							'rel'    => array(),
							'target' => array(),
						),
					)
				),
				esc_url( $support_url )
			);
			?>
	</p>
</div>
