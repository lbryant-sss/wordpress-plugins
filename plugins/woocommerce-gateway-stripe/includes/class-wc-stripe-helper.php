<?php

use Automattic\WooCommerce\Enums\OrderStatus;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides static methods as helpers.
 *
 * @since 4.0.0
 */
class WC_Stripe_Helper {
	const SETTINGS_OPTION              = 'woocommerce_stripe_settings';
	const LEGACY_META_NAME_FEE         = 'Stripe Fee';
	const LEGACY_META_NAME_NET         = 'Net Revenue From Stripe';
	const META_NAME_FEE                = '_stripe_fee';
	const META_NAME_NET                = '_stripe_net';
	const META_NAME_STRIPE_CURRENCY    = '_stripe_currency';
	const PAYMENT_AWAITING_ACTION_META = '_stripe_payment_awaiting_action';

	/**
	 * The identifier for the official Affirm gateway plugin.
	 *
	 * @var string
	 */
	const OFFICIAL_PLUGIN_ID_AFFIRM = 'affirm';

	/**
	 * The identifier for the official Klarna gateway plugin.
	 *
	 * @var string
	 */
	const OFFICIAL_PLUGIN_ID_KLARNA = 'klarna_payments';

	/**
	 * List of legacy Stripe gateways.
	 *
	 * @var array
	 */
	public static $stripe_legacy_gateways = [];

	/**
	 * Get the main Stripe settings option.
	 *
	 * @param string $method (Optional) The payment method to get the settings from.
	 * @return array $settings The Stripe settings.
	 */
	public static function get_stripe_settings( $method = null ) {
		$settings = null === $method ? get_option( self::SETTINGS_OPTION, [] ) : get_option( 'woocommerce_stripe_' . $method . '_settings', [] );
		if ( ! is_array( $settings ) ) {
			$settings = [];
		}
		return $settings;
	}

	/**
	 * Update the main Stripe settings option.
	 *
	 * @param $options array The Stripe settings.
	 * @return void
	 */
	public static function update_main_stripe_settings( $options ) {
		update_option( self::SETTINGS_OPTION, $options );
	}

	/**
	 * Delete the main Stripe settings option.
	 *
	 * @return void
	 */
	public static function delete_main_stripe_settings() {
		delete_option( self::SETTINGS_OPTION );
	}

	/**
	 * Gets the Stripe currency for order.
	 *
	 * @since 4.1.0
	 * @param object $order
	 * @return string $currency
	 */
	public static function get_stripe_currency( $order = null ) {
		if ( is_null( $order ) ) {
			return false;
		}

		return $order->get_meta( self::META_NAME_STRIPE_CURRENCY, true );
	}

	/**
	 * Updates the Stripe currency for order.
	 *
	 * @since 4.1.0
	 * @param object $order
	 * @param string $currency
	 */
	public static function update_stripe_currency( $order, $currency ) {
		if ( is_null( $order ) ) {
			return false;
		}

		$order->update_meta_data( self::META_NAME_STRIPE_CURRENCY, $currency );
	}

	/**
	 * Gets the Stripe fee for order. With legacy check.
	 *
	 * @since 4.1.0
	 * @param object $order
	 * @return string $amount
	 */
	public static function get_stripe_fee( $order = null ) {
		if ( is_null( $order ) ) {
			return false;
		}

		$amount = $order->get_meta( self::META_NAME_FEE, true );

		// If not found let's check for legacy name.
		if ( empty( $amount ) ) {
			$amount = $order->get_meta( self::LEGACY_META_NAME_FEE, true );

			// If found update to new name.
			if ( $amount ) {
				self::update_stripe_fee( $order, $amount );
			}
		}

		return $amount;
	}

	/**
	 * Updates the Stripe fee for order.
	 *
	 * @since 4.1.0
	 * @param object $order
	 * @param float  $amount
	 */
	public static function update_stripe_fee( $order = null, $amount = 0.0 ) {
		if ( is_null( $order ) ) {
			return false;
		}

		$order->update_meta_data( self::META_NAME_FEE, $amount );
	}

	/**
	 * Deletes the Stripe fee for order.
	 *
	 * @since 4.1.0
	 * @param object $order
	 */
	public static function delete_stripe_fee( $order = null ) {
		if ( is_null( $order ) ) {
			return false;
		}

		$order->delete_meta_data( self::META_NAME_FEE );
		$order->delete_meta_data( self::LEGACY_META_NAME_FEE );
	}

	/**
	 * Gets the Stripe net for order. With legacy check.
	 *
	 * @since 4.1.0
	 * @param object $order
	 * @return string $amount
	 */
	public static function get_stripe_net( $order = null ) {
		if ( is_null( $order ) ) {
			return false;
		}

		$amount = $order->get_meta( self::META_NAME_NET, true );

		// If not found let's check for legacy name.
		if ( empty( $amount ) ) {
			$amount = $order->get_meta( self::LEGACY_META_NAME_NET, true );

			// If found update to new name.
			if ( $amount ) {
				self::update_stripe_net( $order, $amount );
			}
		}

		return $amount;
	}

	/**
	 * Updates the Stripe net for order.
	 *
	 * @since 4.1.0
	 * @param object $order
	 * @param float  $amount
	 */
	public static function update_stripe_net( $order = null, $amount = 0.0 ) {
		if ( is_null( $order ) ) {
			return false;
		}

		$order->update_meta_data( self::META_NAME_NET, $amount );
	}

	/**
	 * Deletes the Stripe net for order.
	 *
	 * @since 4.1.0
	 * @param object $order
	 */
	public static function delete_stripe_net( $order = null ) {
		if ( is_null( $order ) ) {
			return false;
		}

		$order->delete_meta_data( self::META_NAME_NET );
		$order->delete_meta_data( self::LEGACY_META_NAME_NET );
	}

	/**
	 * Get Stripe amount to pay
	 *
	 * @param float  $total Amount due.
	 * @param string $currency Accepted currency.
	 *
	 * @return float|int
	 */
	public static function get_stripe_amount( $total, $currency = '' ) {
		if ( ! $currency ) {
			$currency = get_woocommerce_currency();
		}

		$currency = strtolower( $currency );

		if ( in_array( $currency, self::no_decimal_currencies(), true ) ) {
			return absint( $total );
		} elseif ( in_array( $currency, self::three_decimal_currencies(), true ) ) {
			$price_decimals = wc_get_price_decimals();
			$amount         = absint( wc_format_decimal( ( (float) $total * 1000 ), $price_decimals ) ); // For tree decimal currencies.
			return $amount - ( $amount % 10 ); // Round the last digit down. See https://docs.stripe.com/currencies?presentment-currency=AE#three-decimal
		} else {
			return absint( wc_format_decimal( ( (float) $total * 100 ), wc_get_price_decimals() ) ); // In cents.
		}
	}

	/**
	 * Localize Stripe messages based on code
	 *
	 * @since 3.0.6
	 * @version 3.0.6
	 * @return array
	 */
	public static function get_localized_messages() {
		return apply_filters(
			'wc_stripe_localized_messages',
			[
				'invalid_number'                        => __( 'The card number is not a valid credit card number.', 'woocommerce-gateway-stripe' ),
				'invalid_expiry_month'                  => __( 'The card\'s expiration month is invalid.', 'woocommerce-gateway-stripe' ),
				'invalid_expiry_year'                   => __( 'The card\'s expiration year is invalid.', 'woocommerce-gateway-stripe' ),
				'invalid_cvc'                           => __( 'The card\'s security code is invalid.', 'woocommerce-gateway-stripe' ),
				'incorrect_number'                      => __( 'The card number is incorrect.', 'woocommerce-gateway-stripe' ),
				'incomplete_number'                     => __( 'The card number is incomplete.', 'woocommerce-gateway-stripe' ),
				'incomplete_cvc'                        => __( 'The card\'s security code is incomplete.', 'woocommerce-gateway-stripe' ),
				'incomplete_expiry'                     => __( 'The card\'s expiration date is incomplete.', 'woocommerce-gateway-stripe' ),
				'expired_card'                          => __( 'The card has expired.', 'woocommerce-gateway-stripe' ),
				'incorrect_cvc'                         => __( 'The card\'s security code is incorrect.', 'woocommerce-gateway-stripe' ),
				'incorrect_zip'                         => __( 'The card\'s zip code failed validation.', 'woocommerce-gateway-stripe' ),
				'postal_code_invalid'                   => __( 'Invalid zip code, please correct and try again', 'woocommerce-gateway-stripe' ),
				'invalid_expiry_year_past'              => __( 'The card\'s expiration year is in the past', 'woocommerce-gateway-stripe' ),
				'card_declined'                         => __( 'The card was declined.', 'woocommerce-gateway-stripe' ),
				'missing'                               => __( 'There is no card on a customer that is being charged.', 'woocommerce-gateway-stripe' ),
				'processing_error'                      => __( 'An error occurred while processing the card.', 'woocommerce-gateway-stripe' ),
				'invalid_sofort_country'                => __( 'The billing country is not accepted by Sofort. Please try another country.', 'woocommerce-gateway-stripe' ),
				'email_invalid'                         => __( 'Invalid email address, please correct and try again.', 'woocommerce-gateway-stripe' ),
				'invalid_request_error'                 => is_add_payment_method_page()
					? __( 'Unable to save this payment method, please try again or use alternative method.', 'woocommerce-gateway-stripe' )
					: __( 'Unable to process this payment, please try again or use alternative method.', 'woocommerce-gateway-stripe' ),
				'amount_too_large'                      => __( 'The order total is too high for this payment method', 'woocommerce-gateway-stripe' ),
				'amount_too_small'                      => __( 'The order total is too low for this payment method', 'woocommerce-gateway-stripe' ),
				'country_code_invalid'                  => __( 'Invalid country code, please try again with a valid country code', 'woocommerce-gateway-stripe' ),
				'tax_id_invalid'                        => __( 'Invalid Tax Id, please try again with a valid tax id', 'woocommerce-gateway-stripe' ),
				'invalid_wallet_type'                   => __( 'Invalid wallet payment type, please try again or use an alternative method.', 'woocommerce-gateway-stripe' ),
				'payment_intent_authentication_failure' => __( 'We are unable to authenticate your payment method. Please choose a different payment method and try again.', 'woocommerce-gateway-stripe' ),
				'insufficient_funds'                    => __( 'Your card has insufficient funds.', 'woocommerce-gateway-stripe' ),
			]
		);
	}

	/**
	 * List of currencies supported by Stripe that has no decimals
	 * https://docs.stripe.com/currencies#zero-decimal from https://docs.stripe.com/currencies#presentment-currencies
	 * ugx is an exception and not in this list for being a special cases in Stripe https://docs.stripe.com/currencies#special-cases
	 *
	 * @return array $currencies
	 */
	public static function no_decimal_currencies() {
		return [
			'bif', // Burundian Franc
			'clp', // Chilean Peso
			'djf', // Djiboutian Franc
			'gnf', // Guinean Franc
			'jpy', // Japanese Yen
			'kmf', // Comorian Franc
			'krw', // South Korean Won
			'mga', // Malagasy Ariary
			'pyg', // Paraguayan Guaraní
			'rwf', // Rwandan Franc
			'vnd', // Vietnamese Đồng
			'vuv', // Vanuatu Vatu
			'xaf', // Central African Cfa Franc
			'xof', // West African Cfa Franc
			'xpf', // Cfp Franc
		];
	}

	/**
	 * List of currencies supported by Stripe that has three decimals
	 * https://docs.stripe.com/currencies?presentment-currency=AE#three-decimal
	 *
	 * @return array $currencies
	 */
	public static function three_decimal_currencies() {
		return [
			'bhd', // Bahraini Dinar
			'jod', // Jordanian Dinar
			'kwd', // Kuwaiti Dinar
			'omr', // Omani Rial
			'tnd', // Tunisian Dinar
		];
	}

	/**
	 * Stripe uses smallest denomination in currencies such as cents.
	 * We need to format the returned currency from Stripe into human readable form.
	 * The amount is not used in any calculations so returning string is sufficient.
	 *
	 * @param object $balance_transaction
	 * @param string $type Type of number to format
	 * @return string
	 */
	public static function format_balance_fee( $balance_transaction, $type = 'fee' ) {
		if ( ! is_object( $balance_transaction ) ) {
			return;
		}

		if ( in_array( strtolower( $balance_transaction->currency ), self::no_decimal_currencies() ) ) {
			if ( 'fee' === $type ) {
				return $balance_transaction->fee;
			}

			return $balance_transaction->net;
		}

		if ( 'fee' === $type ) {
			return number_format( $balance_transaction->fee / 100, 2, '.', '' );
		}

		return number_format( $balance_transaction->net / 100, 2, '.', '' );
	}

	/**
	 * Checks Stripe minimum order value authorized per currency
	 */
	public static function get_minimum_amount() {
		// Check order amount
		switch ( get_woocommerce_currency() ) {
			case WC_Stripe_Currency_Code::UNITED_STATES_DOLLAR:
			case WC_Stripe_Currency_Code::CANADIAN_DOLLAR:
			case WC_Stripe_Currency_Code::EURO:
			case WC_Stripe_Currency_Code::SWISS_FRANC:
			case WC_Stripe_Currency_Code::AUSTRALIAN_DOLLAR:
			case WC_Stripe_Currency_Code::SINGAPORE_DOLLAR:
				$minimum_amount = 50;
				break;
			case WC_Stripe_Currency_Code::POUND_STERLING:
				$minimum_amount = 30;
				break;
			case WC_Stripe_Currency_Code::DANISH_KRONE:
				$minimum_amount = 250;
				break;
			case WC_Stripe_Currency_Code::NORWEGIAN_KRONE:
			case WC_Stripe_Currency_Code::SWEDISH_KRONA:
				$minimum_amount = 300;
				break;
			case WC_Stripe_Currency_Code::JAPANESE_YEN:
				$minimum_amount = 5000;
				break;
			case WC_Stripe_Currency_Code::MEXICAN_PESO:
				$minimum_amount = 1000;
				break;
			case WC_Stripe_Currency_Code::HONG_KONG_DOLLAR:
				$minimum_amount = 400;
				break;
			default:
				$minimum_amount = 50;
				break;
		}

		return $minimum_amount;
	}

	/**
	 * Gets all the saved setting options from a specific method.
	 * If specific setting is passed, only return that.
	 *
	 * @since 4.0.0
	 * @version 4.0.0
	 * @param string $method The payment method to get the settings from.
	 * @param string $setting The name of the setting to get.
	 */
	public static function get_settings( $method = null, $setting = null ) {
		$all_settings = self::get_stripe_settings( $method );

		if ( null === $setting ) {
			return $all_settings;
		}

		return $all_settings[ $setting ] ?? '';
	}

	/**
	 * List of legacy payment method classes.
	 *
	 * @return array
	 */
	public static function get_legacy_payment_method_classes() {
		$payment_method_classes = [
			WC_Gateway_Stripe_Alipay::class,
			WC_Gateway_Stripe_Bancontact::class,
			WC_Gateway_Stripe_Boleto::class,
			WC_Gateway_Stripe_Eps::class,
			WC_Gateway_Stripe_Giropay::class,
			WC_Gateway_Stripe_Ideal::class,
			WC_Gateway_Stripe_Multibanco::class,
			WC_Gateway_Stripe_Oxxo::class,
			WC_Gateway_Stripe_P24::class,
			WC_Gateway_Stripe_Sepa::class,
		];

		/** Show Sofort if it's already enabled. Hide from the new merchants and keep it for the old ones who are already using this gateway, until we remove it completely.
		 * Stripe is deprecating Sofort https://support.stripe.com/questions/sofort-is-being-deprecated-as-a-standalone-payment-method.
		 */
		$sofort_settings = get_option( 'woocommerce_stripe_sofort_settings', [] );
		if ( isset( $sofort_settings['enabled'] ) && 'yes' === $sofort_settings['enabled'] ) {
			$payment_method_classes[] = WC_Gateway_Stripe_Sofort::class;
		}

		return $payment_method_classes;
	}

	/**
	 * List of legacy payment methods.
	 *
	 * @return array
	 */
	public static function get_legacy_payment_methods() {
		if ( ! empty( self::$stripe_legacy_gateways ) ) {
			return self::$stripe_legacy_gateways;
		}

		$payment_gateways        = WC()->payment_gateways()->payment_gateways();
		$payment_gateway_classes = array_map( 'get_class', $payment_gateways );

		foreach ( self::get_legacy_payment_method_classes() as $payment_method_class ) {
			// If the payment method is already registered, use it, otherwise create a new instance.
			if ( in_array( $payment_method_class, $payment_gateway_classes, true ) ) {
				$gateway_id     = array_search( $payment_method_class, $payment_gateway_classes, true );
				$payment_method = $payment_gateways[ $gateway_id ];
			} else {
				$payment_method = new $payment_method_class();
			}

			self::$stripe_legacy_gateways[ $payment_method->id ] = $payment_method;
		}

		return self::$stripe_legacy_gateways;
	}

	/**
	 * Get legacy payment method by id.
	 *
	 * @return object|null
	 */
	public static function get_legacy_payment_method( $id ) {
		$payment_methods = self::get_legacy_payment_methods();

		if ( ! isset( $payment_methods[ $id ] ) ) {
			return null;
		}

		return $payment_methods[ $id ];
	}

	/**
	 * List of available legacy payment method ids.
	 * It returns the order saved in the `stripe_legacy_method_order` option in Stripe settings.
	 * If the `stripe_legacy_method_order` option is not set, it returns the default order.
	 *
	 * The ids are mapped to the corresponding equivalent UPE method ids for rendeing on the frontend.
	 *
	 * @return array
	 */
	public static function get_legacy_available_payment_method_ids() {
		$stripe_settings            = self::get_stripe_settings();
		$payment_method_classes     = self::get_legacy_payment_method_classes();
		$ordered_payment_method_ids = isset( $stripe_settings['stripe_legacy_method_order'] ) ? $stripe_settings['stripe_legacy_method_order'] : [];

		// If the legacy method order is not set, return the default order.
		if ( ! empty( $ordered_payment_method_ids ) ) {
			$payment_method_ids = array_map(
				function ( $payment_method_id ) {
					if ( 'stripe' === $payment_method_id ) {
						return WC_Stripe_Payment_Methods::CARD;
					} else {
						return str_replace( 'stripe_', '', $payment_method_id );
					}
				},
				$ordered_payment_method_ids
			);

			// Cover the edge case when new Stripe payment methods are added to the plugin which do not exist in
			// the `stripe_legacy_method_order` option.
			if ( count( $payment_method_ids ) - 1 !== count( $payment_method_classes ) ) {
				foreach ( $payment_method_classes as $payment_method_class ) {
					$id = str_replace( 'stripe_', '', $payment_method_class::ID );
					if ( ! in_array( $id, $payment_method_ids, true ) ) {
						$payment_method_ids[] = $id;
					}
				}

				// Update the `stripe_legacy_method_order` option with the new order including missing payment methods from the option.
				$stripe_settings['stripe_legacy_method_order'] = $payment_method_ids;
				self::update_main_stripe_settings( $stripe_settings );
			}
		} else {
			$payment_method_ids = array_map(
				function ( $payment_method_class ) {
					return str_replace( 'stripe_', '', $payment_method_class::ID );
				},
				$payment_method_classes
			);
			$payment_method_ids = array_merge( [ WC_Stripe_Payment_Methods::CARD ], $payment_method_ids );
		}

		return $payment_method_ids;
	}

	/**
	 * List of enabled legacy payment methods.
	 *
	 * @return array
	 */
	public static function get_legacy_enabled_payment_methods() {
		$payment_methods = self::get_legacy_payment_methods();

		$enabled_payment_methods = [];

		foreach ( $payment_methods as $payment_method ) {
			if ( ! $payment_method->is_enabled() ) {
				continue;
			}
			$enabled_payment_methods[ $payment_method->id ] = $payment_method;
		}

		return $enabled_payment_methods;
	}

	/**
	 * List of enabled legacy payment method ids.
	 *
	 * @return array
	 */
	public static function get_legacy_enabled_payment_method_ids() {
		$is_stripe_enabled = self::get_settings( null, 'enabled' );

		// In legacy mode (when UPE is disabled), Stripe refers to card as payment method.
		$enabled_payment_method_ids = 'yes' === $is_stripe_enabled ? [ WC_Stripe_Payment_Methods::CARD ] : [];

		$payment_methods                   = self::get_legacy_payment_methods();
		$mapped_enabled_payment_method_ids = [];

		foreach ( $payment_methods as $payment_method ) {
			if ( ! $payment_method->is_enabled() ) {
				continue;
			}
			$payment_method_id = str_replace( 'stripe_', '', $payment_method->id );

			$mapped_enabled_payment_method_ids[] = $payment_method_id;
		}

		return array_merge( $enabled_payment_method_ids, $mapped_enabled_payment_method_ids );
	}

	/**
	 * Get settings of individual legacy payment methods.
	 *
	 * @return array
	 *
	 * @deprecated 9.6.0 The customization of individual payment methods is now deprecated.
	 */
	public static function get_legacy_individual_payment_method_settings() {
		$stripe_settings = self::get_stripe_settings();
		$payment_methods = self::get_legacy_payment_methods();

		$payment_method_settings = [
			WC_Stripe_Payment_Methods::CARD => [
				'name'        => isset( $stripe_settings['title'] ) ? $stripe_settings['title'] : '',
				'description' => isset( $stripe_settings['description'] ) ? $stripe_settings['description'] : '',
			],
		];

		foreach ( $payment_methods as $payment_method ) {
			$settings = [
				'name'        => $payment_method->get_option( 'title' ),
				'description' => $payment_method->get_option( 'description' ),
			];

			$unique_settings = $payment_method->get_unique_settings();
			if ( isset( $unique_settings[ $payment_method->id . '_expiration' ] ) ) {
				$settings['expiration'] = $unique_settings[ $payment_method->id . '_expiration' ];
			}

			$payment_method_id = str_replace( 'stripe_', '', $payment_method->id );

			$payment_method_settings[ $payment_method_id ] = $settings;
		}

		return $payment_method_settings;
	}

	/**
	 * Get settings of individual upe payment methods.
	 *
	 * @param WC_Stripe_Payment_Gateway $gateway Stripe payment gateway.
	 * @return array
	 *
	 * @deprecated 9.6.0 The customization of individual payment methods is now deprecated.
	 */
	public static function get_upe_individual_payment_method_settings( $gateway ) {
		$payment_method_settings = [];
		$available_gateways      = $gateway->get_upe_available_payment_methods();

		foreach ( $available_gateways as $gateway ) {
			$individual_gateway_settings = get_option( 'woocommerce_stripe_' . $gateway . '_settings', [] );

			$settings = [
				'name'        => isset( $individual_gateway_settings['title'] ) ? $individual_gateway_settings['title'] : '',
				'description' => isset( $individual_gateway_settings['description'] ) ? $individual_gateway_settings['description'] : '',
			];

			if ( in_array( $gateway, [ WC_Stripe_Payment_Methods::BOLETO ], true ) ) {
				$settings['expiration'] = isset( $individual_gateway_settings['expiration'] ) ? $individual_gateway_settings['expiration'] : '';
			}

			$payment_method_settings[ $gateway ] = $settings;
		}

		// If card settings are not set, get it from the default Stripe settings which might be set before enabling UPE.
		if ( ! isset( $payment_method_settings['card']['title'] ) && ! isset( $payment_method_settings['card']['description'] ) ) {
			$stripe_settings = self::get_stripe_settings();
			$title           = isset( $stripe_settings['title'] ) ? $stripe_settings['title'] : '';
			$description     = isset( $stripe_settings['description'] ) ? $stripe_settings['description'] : '';

			$payment_method_settings['card'] = [
				'name'        => $title,
				'description' => $description,
			];
			// Save the title and description to the card settings option.
			update_option(
				'woocommerce_stripe_card_settings',
				[
					'title'       => $title,
					'description' => $description,
				]
			);
		}

		return $payment_method_settings;
	}

	/**
	 * Returns the list of ordered payment methods for the settings page when UPE is enabled.
	 * It returns the order saved in the `stripe_upe_payment_method_order` option in Stripe settings.
	 * If the `stripe_upe_payment_method_order` option is not set, it returns the default order of available gateways.
	 *
	 * @param WC_Stripe_Payment_Gateway $gateway Stripe payment gateway.
	 * @return string[]
	 */
	public static function get_upe_ordered_payment_method_ids( $gateway ) {
		$stripe_settings            = self::get_stripe_settings();
		$testmode                   = WC_Stripe_Mode::is_test();
		$ordered_payment_method_ids = isset( $stripe_settings['stripe_upe_payment_method_order'] ) ? $stripe_settings['stripe_upe_payment_method_order'] : [];

		// When switched to the new checkout experience, the UPE method order is not set. Copy the legacy order to the UPE order to persist previous settings.
		if ( empty( $stripe_settings['stripe_upe_payment_method_order'] ) && ! empty( $stripe_settings['stripe_legacy_method_order'] ) ) {
			$ordered_payment_method_ids = array_map(
				function ( $payment_method_id ) {
					if ( 'stripe' === $payment_method_id ) {
						return WC_Stripe_Payment_Methods::CARD;
					} elseif ( 'stripe_sepa' === $payment_method_id ) {
						return WC_Stripe_Payment_Methods::SEPA_DEBIT;
					}
					return str_replace( 'stripe_', '', $payment_method_id );
				},
				$stripe_settings['stripe_legacy_method_order']
			);

		}

		// The `stripe_upe_payment_method_order` option has the order of the UPE methods set by the user.
		// This list is filtered on the basis of the capabilities set in the Stripe account data on the frontend before saving.
		// If the list is empty or we have any new available payment methods, we need to update the list by including the available payment methods having capabilities.
		$available_methods_with_capability = self::filter_payment_methods_with_capabilities( $gateway->get_upe_available_payment_methods(), $testmode );

		$ordered_payment_method_ids_with_capability = array_filter(
			$ordered_payment_method_ids,
			function ( $payment_method_id ) use ( $available_methods_with_capability ) {
				return in_array( $payment_method_id, $available_methods_with_capability, true );
			}
		);

		if ( count( $ordered_payment_method_ids_with_capability ) === count( $available_methods_with_capability ) ) {
			return $ordered_payment_method_ids_with_capability;
		}

		// Update the `stripe_upe_payment_method_order` option with the new order including rest of the available methods with capabilities.
		$additional_methods = array_diff( $available_methods_with_capability, $ordered_payment_method_ids_with_capability );
		$updated_order      = array_merge( $ordered_payment_method_ids_with_capability, $additional_methods );

		$stripe_settings['stripe_upe_payment_method_order'] = $updated_order;
		self::update_main_stripe_settings( $stripe_settings );

		return $updated_order;
	}

	/**
	 * Returns the list of payment methods that have capabilities set in the Stripe account data.
	 *
	 * @param string[] $payment_method_ids Payment method ids to filter by capabilities.
	 * @param bool     $testmode Whether stripe is in test mode.
	 * @return string[]
	 */
	public static function filter_payment_methods_with_capabilities( $payment_method_ids, $testmode ) {
		$account = WC_Stripe::get_instance()->account;
		$data    = $account->get_cached_account_data();

		// return empty array if capabilities are not set.
		if ( empty( $data ) || ! isset( $data['capabilities'] ) ) {
			return [];
		}

		// Return all payment methods if in test mode.
		if ( $testmode ) {
			return $payment_method_ids;
		}

		$payment_method_ids_with_capability = [];

		foreach ( $payment_method_ids as $payment_method_id ) {
			$key = self::get_payment_method_capability_id( $payment_method_id );
			// Check if the payment method has capabilities set in the account data.
			// Generally the key is the payment method id appended with '_payments' (i.e. 'card_payments', 'sepa_debit_payments', 'klarna_payments').
			// In some cases, the Stripe account might have the legacy key set. For example, for Klarna, the legacy key is 'klarna'.
			// For card, the legacy key is 'legacy_payments'.
			$has_capability = isset( $data['capabilities'][ $key ] ) || isset( $data['capabilities'][ $payment_method_id ] ) || ( WC_Stripe_Payment_Methods::CARD === $payment_method_id && isset( $data['capabilities']['legacy_payments'] ) );
			if ( $has_capability ) {
				$payment_method_ids_with_capability[] = $payment_method_id;
			}
		}

		return $payment_method_ids_with_capability;
	}

	/**
	 * Returns the list of enabled payment methods for the settings page when UPE is enabled.
	 *
	 * @param WC_Stripe_Payment_Gateway $gateway Stripe payment gateway.
	 * @return string[]
	 */
	public static function get_upe_settings_enabled_payment_method_ids( $gateway ) {
		$enabled_gateways = $gateway->get_upe_enabled_payment_method_ids();

		return $enabled_gateways;
	}

	/**
	 * Reorders the list of available payment gateways in 'woocommerce_gateway_order' option to include the Stripe methods
	 * in the order merchants have chosen in the settings.
	 *
	 * @param array $ordered_payment_method_ids Ordered Stripe payment method list.
	 */
	public static function add_stripe_methods_in_woocommerce_gateway_order( $ordered_payment_method_ids = [] ) {
		// If the ordered payment method ids are not passed, get them from the relevant settings.
		if ( empty( $ordered_payment_method_ids ) ) {
			$is_upe_enabled  = WC_Stripe_Feature_Flags::is_upe_checkout_enabled();
			$stripe_settings = self::get_stripe_settings();

			if ( $is_upe_enabled ) {
				$ordered_payment_method_ids = $stripe_settings['stripe_upe_payment_method_order'] ?? [];
			} else {
				$ordered_payment_method_ids = $stripe_settings['stripe_legacy_method_order'] ?? [];
			}

			if ( empty( $ordered_payment_method_ids ) ) {
				return;
			}
		}

		$gateway_order = get_option( 'woocommerce_gateway_order', [] );
		asort( $gateway_order );

		$ordered_available_stripe_methods = [];
		// Map the Stripe payment method list to the right format to save in the 'woocommerce_gateway_order' option.
		foreach ( $ordered_payment_method_ids as $payment_method_id ) {
			$gateway_id = 0 === strpos( $payment_method_id, 'stripe' ) ? $payment_method_id : 'stripe_' . $payment_method_id;

			if ( WC_Stripe_Payment_Methods::CARD === $payment_method_id ) {
				$gateway_id = 'stripe';
			}

			$ordered_available_stripe_methods[] = $gateway_id;
		}

		$updated_gateway_order = [];
		$index                 = 0;
		$stripe_gateways_added = false;
		foreach ( array_keys( $gateway_order ) as $gateway ) {
			if ( 0 === strpos( $gateway, 'stripe_' ) ) {
				continue; // Skip the other stripe gateways. We'll add all Stripe methods back in the right order.
			} elseif ( 'stripe' === $gateway ) {
				// When the main Stripe gateway is found in the option, add all the Stripe methods in the right order starting from this index.
				foreach ( $ordered_available_stripe_methods as $ordered_available_stripe_method ) {
					$updated_gateway_order[ $ordered_available_stripe_method ] = (string) $index++;
				}
				$stripe_gateways_added = true;
			} else {
				// Add the rest of the gateways.
				$updated_gateway_order[ $gateway ] = (string) $index++;
			}
		}

		// Stripe may not initially be in the gateway order options even when enabled --
		// we ensure it's added here.
		if ( ! $stripe_gateways_added ) {
			foreach ( $ordered_available_stripe_methods as $ordered_available_stripe_method ) {
				$updated_gateway_order[ $ordered_available_stripe_method ] = (string) $index++;
			}
		}

		update_option( 'woocommerce_gateway_order', $updated_gateway_order );
	}

	/**
	 * Checks if WC version is less than passed in version.
	 *
	 * @since 4.1.11
	 * @param string $version Version to check against.
	 * @return bool
	 */
	public static function is_wc_lt( $version ) {
		return version_compare( WC_VERSION, $version, '<' );
	}

	/**
	 * Gets the webhook URL for Stripe triggers. Used mainly for
	 * asyncronous redirect payment methods in which statuses are
	 * not immediately chargeable.
	 *
	 * @since 4.0.0
	 * @version 4.0.0
	 * @return string
	 */
	public static function get_webhook_url() {
		return wp_sanitize_redirect(
			esc_url_raw(
				add_query_arg( 'wc-api', 'wc_stripe', trailingslashit( get_home_url() ) )
			)
		);
	}

	/**
	 * Gets the order by Stripe source ID.
	 *
	 * @since 4.0.0
	 * @version 4.0.0
	 * @param string $source_id
	 */
	public static function get_order_by_source_id( $source_id ) {
		global $wpdb;

		if ( WC_Stripe_Woo_Compat_Utils::is_custom_orders_table_enabled() ) {
			$orders   = wc_get_orders(
				[
					'limit'      => 1,
					'meta_query' => [
						[
							'key'   => '_stripe_source_id',
							'value' => $source_id,
						],
					],
				]
			);
			$order_id = current( $orders ) ? current( $orders )->get_id() : false;
		} else {
			$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT ID FROM $wpdb->posts as posts LEFT JOIN $wpdb->postmeta as meta ON posts.ID = meta.post_id WHERE meta.meta_value = %s AND meta.meta_key = %s", $source_id, '_stripe_source_id' ) );
		}

		if ( ! empty( $order_id ) ) {
			return wc_get_order( $order_id );
		}

		return false;
	}

	/**
	 * Gets the order by Stripe charge ID.
	 *
	 * @since 4.0.0
	 * @since 4.1.16 Return false if charge_id is empty.
	 * @param string $charge_id
	 */
	public static function get_order_by_charge_id( $charge_id ) {
		global $wpdb;

		if ( empty( $charge_id ) ) {
			return false;
		}

		if ( WC_Stripe_Woo_Compat_Utils::is_custom_orders_table_enabled() ) {
			$orders   = wc_get_orders(
				[
					'transaction_id' => $charge_id,
					'limit'          => 1,
				]
			);
			$order_id = current( $orders ) ? current( $orders )->get_id() : false;
		} else {
			$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT ID FROM $wpdb->posts as posts LEFT JOIN $wpdb->postmeta as meta ON posts.ID = meta.post_id WHERE meta.meta_value = %s AND meta.meta_key = %s", $charge_id, '_transaction_id' ) );
		}

		if ( ! empty( $order_id ) ) {
			return wc_get_order( $order_id );
		}

		return false;
	}

	/**
	 * Gets the order by Stripe refund ID.
	 *
	 * @since 7.5.0
	 * @param string $refund_id
	 */
	public static function get_order_by_refund_id( $refund_id ) {
		global $wpdb;

		if ( WC_Stripe_Woo_Compat_Utils::is_custom_orders_table_enabled() ) {
			$orders   = wc_get_orders(
				[
					'limit'      => 1,
					'meta_query' => [
						[
							'key'   => '_stripe_refund_id',
							'value' => $refund_id,
						],
					],
				]
			);
			$order_id = current( $orders ) ? current( $orders )->get_id() : false;
		} else {
			$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT ID FROM $wpdb->posts as posts LEFT JOIN $wpdb->postmeta as meta ON posts.ID = meta.post_id WHERE meta.meta_value = %s AND meta.meta_key = %s", $refund_id, '_stripe_refund_id' ) );
		}

		if ( ! empty( $order_id ) ) {
			return wc_get_order( $order_id );
		}

		return false;
	}

	/**
	 * Gets the order by Stripe PaymentIntent ID.
	 *
	 * @since 4.2
	 * @param string $intent_id The ID of the intent.
	 * @return WC_Order|bool Either an order or false when not found.
	 */
	public static function get_order_by_intent_id( $intent_id ) {
		global $wpdb;

		if ( WC_Stripe_Woo_Compat_Utils::is_custom_orders_table_enabled() ) {
			$orders   = wc_get_orders(
				[
					'limit'      => 1,
					'meta_query' => [
						[
							'key'   => '_stripe_intent_id',
							'value' => $intent_id,
						],
					],
				]
			);
			$order_id = current( $orders ) ? current( $orders )->get_id() : false;
		} else {
			$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT ID FROM $wpdb->posts as posts LEFT JOIN $wpdb->postmeta as meta ON posts.ID = meta.post_id WHERE meta.meta_value = %s AND meta.meta_key = %s", $intent_id, '_stripe_intent_id' ) );
		}

		if ( ! empty( $order_id ) ) {
			$order = wc_get_order( $order_id );
		}

		if ( ! empty( $order ) && $order->get_status() !== OrderStatus::TRASH ) {
			return $order;
		}

		return false;
	}

	/**
	 * Gets the order by Stripe SetupIntent ID.
	 *
	 * @since 4.3
	 * @param string $intent_id The ID of the intent.
	 * @return WC_Order|bool Either an order or false when not found.
	 */
	public static function get_order_by_setup_intent_id( $intent_id ) {
		global $wpdb;

		if ( WC_Stripe_Woo_Compat_Utils::is_custom_orders_table_enabled() ) {
			$orders   = wc_get_orders(
				[
					'limit'      => 1,
					'meta_query' => [
						[
							'key'   => '_stripe_setup_intent',
							'value' => $intent_id,
						],
					],
				]
			);
			$order_id = current( $orders ) ? current( $orders )->get_id() : false;
		} else {
			$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT ID FROM $wpdb->posts as posts LEFT JOIN $wpdb->postmeta as meta ON posts.ID = meta.post_id WHERE meta.meta_value = %s AND meta.meta_key = %s", $intent_id, '_stripe_setup_intent' ) );
		}

		if ( ! empty( $order_id ) ) {
			return wc_get_order( $order_id );
		}

		return false;
	}

	/**
	 * Gets the dynamic bank statement descriptor suffix.
	 *
	 * Stripe will automatically append this suffix to the merchant account's bank statement prefix.
	 *
	 * @param WC_Order $order The order to generate the suffix for.
	 * @return string The statement descriptor suffix ("#{order-number}").
	 */
	public static function get_dynamic_statement_descriptor_suffix( $order ) {
		$prefix = WC_Stripe::get_instance()->account->get_card_statement_prefix();
		$suffix = '';

		if ( method_exists( $order, 'get_order_number' ) && ! empty( $order->get_order_number() ) ) {
			$suffix = '#' . $order->get_order_number();

			// Stripe requires at least 1 latin (alphabet) character in the suffix so we add an extra `O` before the order number.
			if ( 0 === preg_match( '/[a-zA-Z]/', $suffix ) ) {
				$suffix = 'O ' . $suffix;
			}
		}

		// Make sure that the prefix + suffix is limited at 22 characters.
		return self::clean_statement_descriptor( substr( trim( $suffix ), 0, 22 - strlen( $prefix . '* ' ) ) );
	}

	/**
	 * Sanitize statement descriptor text.
	 *
	 * Stripe requires max of 22 characters and no special characters.
	 *
	 * @since 4.0.0
	 * @param string $statement_descriptor Statement descriptor.
	 * @return string $statement_descriptor Sanitized statement descriptor.
	 */
	public static function clean_statement_descriptor( $statement_descriptor = '' ) {
		$disallowed_characters = [ '<', '>', '\\', '*', '"', "'", '/', '(', ')', '{', '}' ];

		// Strip any tags.
		$statement_descriptor = strip_tags( $statement_descriptor );

		// Strip any HTML entities.
		// Props https://stackoverflow.com/questions/657643/how-to-remove-html-special-chars .
		$statement_descriptor = preg_replace( '/&#?[a-z0-9]{2,8};/i', '', $statement_descriptor );

		// Next, remove any remaining disallowed characters.
		$statement_descriptor = str_replace( $disallowed_characters, '', $statement_descriptor );

		// Remove non-Latin characters, excluding numbers, whitespaces and especial characters.
		$statement_descriptor = preg_replace( '/[^a-zA-Z0-9\s\x{00C0}-\x{00FF}\p{P}]/u', '', $statement_descriptor );

		// Trim any whitespace at the ends and limit to 22 characters.
		$statement_descriptor = substr( trim( $statement_descriptor ), 0, 22 );

		return $statement_descriptor;
	}

	/**
	 * Converts a WooCommerce locale to the closest supported by Stripe.js.
	 *
	 * Stripe.js supports only a subset of IETF language tags, if a country specific locale is not supported we use
	 * the default for that language (https://docs.stripe.com/js/appendix/supported_locales).
	 * If no match is found we return 'auto' so Stripe.js uses the browser locale.
	 *
	 * @param string $wc_locale The locale to convert.
	 *
	 * @return string Closest locale supported by Stripe ('auto' if NONE).
	 */
	public static function convert_wc_locale_to_stripe_locale( $wc_locale ) {
		// List copied from: https://docs.stripe.com/js/appendix/supported_locales.
		$supported = [
			'ar',     // Arabic.
			'bg',     // Bulgarian (Bulgaria).
			'cs',     // Czech (Czech Republic).
			'da',     // Danish.
			'de',     // German (Germany).
			'el',     // Greek (Greece).
			'en',     // English.
			'en-GB',  // English (United Kingdom).
			'es',     // Spanish (Spain).
			'es-419', // Spanish (Latin America).
			'et',     // Estonian (Estonia).
			'fi',     // Finnish (Finland).
			'fr',     // French (France).
			'fr-CA',  // French (Canada).
			'he',     // Hebrew (Israel).
			'hu',     // Hungarian (Hungary).
			'id',     // Indonesian (Indonesia).
			'it',     // Italian (Italy).
			'ja',     // Japanese.
			'lt',     // Lithuanian (Lithuania).
			'lv',     // Latvian (Latvia).
			'ms',     // Malay (Malaysia).
			'mt',     // Maltese (Malta).
			'nb',     // Norwegian Bokmål.
			'nl',     // Dutch (Netherlands).
			'pl',     // Polish (Poland).
			'pt-BR',  // Portuguese (Brazil).
			'pt',     // Portuguese (Brazil).
			'ro',     // Romanian (Romania).
			'ru',     // Russian (Russia).
			'sk',     // Slovak (Slovakia).
			'sl',     // Slovenian (Slovenia).
			'sv',     // Swedish (Sweden).
			'th',     // Thai.
			'tr',     // Turkish (Turkey).
			'zh',     // Chinese Simplified (China).
			'zh-HK',  // Chinese Traditional (Hong Kong).
			'zh-TW',  // Chinese Traditional (Taiwan).
		];

		// Stripe uses '-' instead of '_' (used in WordPress).
		$locale = str_replace( '_', '-', $wc_locale );

		if ( in_array( $locale, $supported, true ) ) {
			return $locale;
		}

		// The plugin has been fully translated for Spanish (Ecuador), Spanish (Mexico), and
		// Spanish(Venezuela), and partially (88% at 2021-05-14) for Spanish (Colombia).
		// We need to map these locales to Stripe's Spanish (Latin America) 'es-419' locale.
		// This list should be updated if more localized versions of Latin American Spanish are
		// made available.
		$lowercase_locale                  = strtolower( $wc_locale );
		$translated_latin_american_locales = [
			'es_co', // Spanish (Colombia).
			'es_ec', // Spanish (Ecuador).
			'es_mx', // Spanish (Mexico).
			'es_ve', // Spanish (Venezuela).
		];
		if ( in_array( $lowercase_locale, $translated_latin_american_locales, true ) ) {
			return 'es-419';
		}

		// Finally, we check if the "base locale" is available.
		$base_locale = substr( $wc_locale, 0, 2 );
		if ( in_array( $base_locale, $supported, true ) ) {
			return $base_locale;
		}

		// Default to 'auto' so Stripe.js uses the browser locale.
		return 'auto';
	}

	/**
	 * Checks if this page is a cart or checkout page.
	 *
	 * @since 5.2.3
	 * @return boolean
	 */
	public static function has_cart_or_checkout_on_current_page() {
		return is_cart() || is_checkout() || has_block( 'woocommerce/cart' ) || has_block( 'woocommerce/checkout' );
	}

	/**
	 * Return true if the current_tab and current_section match the ones we want to check against.
	 *
	 * @param string $tab
	 * @param string $section
	 * @return boolean
	 */
	public static function should_enqueue_in_current_tab_section( $tab, $section ) {
		global $current_tab, $current_section;

		if ( ! isset( $current_tab ) || $tab !== $current_tab ) {
			return false;
		}

		if ( ! isset( $current_section ) || $section !== $current_section ) {
			return false;
		}

		return true;
	}

	/**
	 * Returns true if the Stripe JS should be loaded on product pages.
	 *
	 * The critical part here is running the filter to allow merchants to disable Stripe's JS to
	 * improve their store's performance when PRBs are disabled.
	 *
	 * @since 5.8.0
	 * @return boolean True if Stripe's JS should be loaded, false otherwise.
	 */
	public static function should_load_scripts_on_product_page() {
		if ( self::should_load_scripts_for_prb_location( 'product' ) ) {
			return true;
		}

		return apply_filters( 'wc_stripe_load_scripts_on_product_page_when_prbs_disabled', true );
	}

	/**
	 * Returns true if the Stripe JS should be loaded on the cart page.
	 *
	 * The critical part here is running the filter to allow merchants to disable Stripe's JS to
	 * improve their store's performance when PRBs are disabled.
	 *
	 * @since 5.8.0
	 * @return boolean True if Stripe's JS should be loaded, false otherwise.
	 */
	public static function should_load_scripts_on_cart_page() {
		if ( self::should_load_scripts_for_prb_location( 'cart' ) ) {
			return true;
		}

		return apply_filters( 'wc_stripe_load_scripts_on_cart_page_when_prbs_disabled', true );
	}

	/**
	 * Returns true if the Stripe JS should be loaded for the provided location.
	 *
	 * @since 5.8.1
	 * @param string $location  Either 'product' or 'cart'. Used to specify which location to check.
	 * @return boolean True if Stripe's JS should be loaded for the provided location, false otherwise.
	 */
	private static function should_load_scripts_for_prb_location( $location ) {
		// Make sure location parameter is sanitized.
		$location         = in_array( $location, [ 'product', 'cart' ], true ) ? $location : '';
		$are_prbs_enabled = self::get_settings( null, 'payment_request' ) ?? 'yes';
		$prb_locations    = self::get_settings( null, 'payment_request_button_locations' ) ?? [ 'product', 'cart' ];

		// The scripts should be loaded when all of the following are true:
		//   1. The PRBs are enabled; and
		//   2. The PRB location settings have an array value (saving an empty option in the GUI results in non-array value); and
		//   3. The PRBs are enabled at $location.
		return 'yes' === $are_prbs_enabled && is_array( $prb_locations ) && in_array( $location, $prb_locations, true );
	}

	/**
	 * Adds payment intent id and order note to order if payment intent is not already saved
	 *
	 * @param $payment_intent_id
	 * @param $order
	 */
	public static function add_payment_intent_to_order( $payment_intent_id, $order ) {

		$old_intent_id = $order->get_meta( '_stripe_intent_id' );

		if ( $old_intent_id === $payment_intent_id ) {
			return;
		}

		$order->add_order_note(
			sprintf(
			/* translators: $1%s payment intent ID */
				__( 'Stripe payment intent created (Payment Intent ID: %1$s)', 'woocommerce-gateway-stripe' ),
				$payment_intent_id
			)
		);

		$order->update_meta_data( '_stripe_intent_id', $payment_intent_id );
		$order->save();
	}

	/**
	 * Adds a source or payment method argument to the request array depending on what sort of
	 * payment method ID is provided. If ID is neither a source or a payment method ID then nothing
	 * is added.
	 *
	 * @param string $payment_method_id  The payment method ID that should be added to the request array.
	 * @param array $request             The request representing the arguments that will be sent in the request.
	 *
	 * @return array  The updated request array.
	 */
	public static function add_payment_method_to_request_array( string $payment_method_id, array $request ): array {
		// Extract the payment method prefix using the first '_' character
		$payment_method_type = substr( $payment_method_id, 0, strpos( $payment_method_id, '_' ) );

		switch ( $payment_method_type ) {
			case 'src':
				$request['source'] = $payment_method_id;
				break;
			case 'pm':
			case 'card':
				$request['payment_method'] = $payment_method_id;
				break;
		}

		return $request;
	}

	/**
	 * Evaluates whether the object passed to this function is a Stripe Payment Method.
	 *
	 * @param stdClass $object  The object that should be evaluated.
	 * @return bool             Returns true if the object is a Payment Method; false otherwise.
	 */
	public static function is_payment_method_object( stdClass $payment_method ): bool {
		return isset( $payment_method->object ) && 'payment_method' === $payment_method->object;
	}

	/**
	 * Evaluates whether a given Stripe Source (or Stripe Payment Method) is reusable.
	 * Payment Methods are always reusable; Sources are only reusable when the appropriate
	 * usage metadata is provided.
	 *
	 * @param stdClass $payment_method  The source or payment method to be evaluated.

	 * @return bool  Returns true if the source is reusable; false otherwise.
	 */
	public static function is_reusable_payment_method( stdClass $payment_method ): bool {
		return self::is_payment_method_object( $payment_method ) || ( isset( $payment_method->usage ) && 'reusable' === $payment_method->usage );
	}

	/**
	 * Returns true if the provided payment method is a card, false otherwise.
	 *
	 * @param stdClass $payment_method  The provided payment method object. Can be a Source or a Payment Method.
	 *
	 * @return bool  True if payment method is a card, false otherwise.
	 */
	public static function is_card_payment_method( stdClass $payment_method ): bool {
		if ( ! isset( $payment_method->object ) || ! isset( $payment_method->type ) ) {
			return false;
		}

		if ( 'payment_method' !== $payment_method->object && 'source' !== $payment_method->object ) {
			return false;
		}

		return WC_Stripe_Payment_Methods::CARD === $payment_method->type;
	}

	/**
	 * Returns a source or payment method from a given intent object.
	 *
	 * @param stdClass|object $intent  The intent that contains the payment method.
	 *
	 * @return stdClass|string|null  The payment method if found, null otherwise.
	 */
	public static function get_payment_method_from_intent( $intent ) {
		if ( ! empty( $intent->source ) ) {
			return $intent->source;
		}

		if ( ! empty( $intent->payment_method ) ) {
			return $intent->payment_method;
		}

		return null;
	}

	/**
	 * Returns the payment intent or setup intent ID from a given order object.
	 *
	 * @param WC_Order $order The order to fetch the Stripe intent from.
	 *
	 * @return string|bool  The intent ID if found, false otherwise.
	 */
	public static function get_intent_id_from_order( $order ) {
		$intent_id = $order->get_meta( '_stripe_intent_id' );

		if ( ! $intent_id ) {
			$intent_id = $order->get_meta( '_stripe_setup_intent' );
		}

		return $intent_id ?? false;
	}

	/**
	 * Fetches a list of all Stripe gateway IDs.
	 *
	 * @return array An array of all Stripe gateway IDs.
	 */
	public static function get_stripe_gateway_ids() {
		$main_gateway = WC_Stripe::get_instance()->get_main_stripe_gateway();
		$gateway_ids  = [ 'stripe' => $main_gateway->id ];

		if ( is_a( $main_gateway, 'WC_Stripe_UPE_Payment_Gateway' ) ) {
			$gateways = $main_gateway->payment_methods;
		} else {
			$gateways = self::get_legacy_payment_methods();
		}

		return array_merge( $gateway_ids, wp_list_pluck( $gateways, 'id', 'id' ) );
	}

	/**
	 * Adds metadata to the order to indicate that the payment is awaiting action.
	 *
	 * This meta is primarily used to prevent orders from being cancelled by WooCommerce's hold stock settings.
	 *
	 * @param WC_Order $order The order to add the metadata to.
	 * @param bool     $save  Whether to save the order after adding the metadata.
	 *
	 * @return void
	 */
	public static function set_payment_awaiting_action( $order, $save = true ) {
		$order->update_meta_data( self::PAYMENT_AWAITING_ACTION_META, wc_bool_to_string( true ) );

		if ( $save ) {
			$order->save();
		}
	}

	/**
	 * Removes the metadata from the order that was used to indicate that the payment was awaiting action.
	 *
	 * @param WC_Order $order The order to remove the metadata from.
	 * @param bool     $save  Whether to save the order after removing the metadata.
	 *
	 * @return void
	 */
	public static function remove_payment_awaiting_action( $order, $save = true ) {
		$order->delete_meta_data( self::PAYMENT_AWAITING_ACTION_META );

		if ( $save ) {
			$order->save();
		}
	}

	/**
	 * Returns the list of countries in the European Economic Area (EEA).
	 *
	 * Based on the list documented at https://www.gov.uk/eu-eea.
	 *
	 * @return string[]
	 */
	public static function get_european_economic_area_countries() {
		return [
			'AT', // Austria.
			'BE', // Belgium.
			'BG', // Bulgaria.
			'HR', // Croatia.
			'CY', // Cyprus.
			'CZ', // Czech Republic.
			'DK', // Denmark.
			'EE', // Estonia.
			'FI', // Finland.
			'FR', // France.
			'DE', // Germany.
			'GR', // Greece.
			'HU', // Hungary.
			'IE', // Ireland.
			'IS', // Iceland
			'IT', // Italy.
			'LV', // Latvia.
			'LI', // Liechtenstein.
			'LT', // Lithuania.
			'LU', // Luxembourg.
			'MT', // Malta.
			'NO', // Norway.
			'NL', // Netherlands.
			'PL', // Poland.
			'PT', // Portugal.
			'RO', // Romania.
			'SK', // Slovakia.
			'SI', // Slovenia.
			'ES', // Spain.
			'SE', // Sweden.
		];
	}

	/**
	 * Verifies if the provided payment method ID supports manual capture.
	 *
	 * @param string $payment_method_id Payment method ID.
	 * @return bool Whether the payment method allows manual capture.
	 */
	public static function payment_method_allows_manual_capture( string $payment_method_id ) {
		return in_array(
			$payment_method_id,
			[
				'stripe',
				'stripe_affirm',
				'stripe_klarna',
				'stripe_afterpay_clearpay',
				'stripe_amazon_pay',
			],
			true
		);
	}

	/**
	 * Verifies if the provided order contains the identifier for a wallet method.
	 *
	 * @param WC_Order $order The order.
	 * @return bool
	 *
	 * @deprecated 8.9.0
	 */
	public static function is_wallet_payment_method( $order ) {
		wc_deprecated_function( __METHOD__, '8.9.0', 'in_array( $order->get_meta( \'_stripe_upe_payment_type\' ), WC_Stripe_Payment_Methods::WALLET_PAYMENT_METHODS, true )' );
		return in_array( $order->get_meta( '_stripe_upe_payment_type' ), WC_Stripe_Payment_Methods::WALLET_PAYMENT_METHODS, true );
	}

	/**
	 * Checks if a given URL matches the current site's Webhook URL.
	 *
	 * This function ignores trailing slashes and compares the host and path of the URLs.
	 * The protocol is ignored. It also requires that any query parameters in the
	 * webhook URL are present in the supplied URL, though extra query parameters in the
	 * supplied URL are ignored.
	 * There is one special case: when the supplied URL has the same host and path,
	 * but an empty query string, it is treated as a match. This is to allow for cleanup
	 * of webhook URLs that don't have identifying URL parameters.
	 *
	 * @param string $url         The URL to check.
	 * @param string $webhook_url The webhook URL to compare against.
	 *
	 * @return bool Whether the URL is a matching webhook URL.
	 */
	public static function is_webhook_url( $url, $webhook_url = '' ) {
		if ( empty( $webhook_url ) ) {
			$webhook_url = self::get_webhook_url();
		}

		$url         = trim( strtolower( $url ) );
		$webhook_url = trim( strtolower( $webhook_url ) );

		// If the URLs are the exact same, no need to compare further.
		if ( $url === $webhook_url ) {
			return true;
		}

		$url_parts         = wp_parse_url( $url );
		$webhook_url_parts = wp_parse_url( $webhook_url );

		$url_host      = $url_parts['host'] ?? '';
		$url_path      = $url_parts['path'] ?? '';
		$url_query     = $url_parts['query'] ?? '';
		$webhook_host  = $webhook_url_parts['host'] ?? '';
		$webhook_path  = $webhook_url_parts['path'] ?? '';
		$webhook_query = $webhook_url_parts['query'] ?? '';

		if ( $url_host !== $webhook_host || $url_path !== $webhook_path ) {
			return false;
		}

		// If the supplied URL has an empty query string, we will treat it as a webhook URL for the plugin,
		// as we're guessing that it was created manually in the long-distant past when webhook
		// management was all manual.
		if ( '' === $url_query ) {
			return true;
		}

		// For our standard webhook URL, we should never hit this condition, but we'll treat them as
		// a mismatch, as we already know the supplied URL has a non-empty query.
		if ( '' === $webhook_query ) {
			return false;
		}

		$url_query_parts     = [];
		$webhook_query_parts = [];

		parse_str( $url_query, $url_query_parts );
		parse_str( $webhook_query, $webhook_query_parts );

		if ( [] === $url_query_parts && [] === $webhook_query_parts ) {
			return true;
		}

		// We ignore extra URL parameters in the supplied URL,
		// but we require all query parameters from the webhook URL to
		// be present in the supplied URL.
		foreach ( $webhook_query_parts as $webhook_query_key => $webhook_query_value ) {
			if ( ! isset( $url_query_parts[ $webhook_query_key ] ) ) {
				return false;
			}

			if ( $url_query_parts[ $webhook_query_key ] !== $webhook_query_value ) {
				return false;
			}
		}

		// If we get here, the supplied URL has all the query parameters from the webhook URL.
		return true;
	}

	public static function get_transaction_url( $is_test_mode = false ) {
		if ( $is_test_mode ) {
			return 'https://dashboard.stripe.com/test/payments/%s';
		}

		return 'https://dashboard.stripe.com/payments/%s';
	}

	/**
	 * Returns a supported locale for setting Klarna's "preferred_locale".
	 * While Stripe allows for localization of Klarna's payments page, it still
	 * limits the locale to the billing country's set of supported locales. For example,
	 * we cannot set the locale to "fr-FR" or "fr-US" if the billing country is "US".
	 *
	 * We compute our desired locale by combining the language tag from the store locale
	 * and the billing country. We return that if it is supported.
	 *
	 * @param string $store_locale The WooCommerce store locale.
	 *   Expected format: WordPress locale format, e.g. "en" or "en_US".
	 * @param string $billing_country The billing country code.
	 * @return string|null The Klarna locale or null if not supported.
	 */
	public static function get_klarna_preferred_locale( $store_locale, $billing_country ) {
		// From https://docs.stripe.com/payments/klarna/accept-a-payment?payments-ui-type=direct-api#supported-locales-and-currencies
		$supported_locales = [
			'AU' => [ 'en-AU' ],
			'AT' => [ 'de-AT', 'en-AT' ],
			'BE' => [ 'nl-BE', 'fr-BE', 'en-BE' ],
			'CA' => [ 'en-CA', 'fr-CA' ],
			'CZ' => [ 'en-CZ', 'cs-CZ' ],
			'DK' => [ 'da-DK', 'en-DK' ],
			'FI' => [ 'fi-FI', 'sv-FI', 'en-FI' ],
			'FR' => [ 'fr-FR', 'en-FR' ],
			'DE' => [ 'de-DE', 'en-DE' ],
			'GR' => [ 'en-GR', 'el-GR' ],
			'IE' => [ 'en-IE' ],
			'IT' => [ 'it-IT', 'en-IT' ],
			'NL' => [ 'nl-NL', 'en-NL' ],
			'NZ' => [ 'en-NZ' ],
			'NO' => [ 'nb-NO', 'en-NO' ],
			'PL' => [ 'pl-PL', 'en-PL' ],
			'PT' => [ 'pt-PT', 'en-PT' ],
			'RO' => [ 'ro-RO', 'en-RO' ],
			'ES' => [ 'es-ES', 'en-ES' ],
			'SE' => [ 'sv-SE', 'en-SE' ],
			'CH' => [ 'de-CH', 'fr-CH', 'it-CH', 'en-CH' ],
			'GB' => [ 'en-GB' ],
			'US' => [ 'en-US', 'es-US' ],
		];

		$region = strtoupper( $billing_country );
		if ( ! isset( $supported_locales[ $region ] ) ) {
			return null;
		}

		// Get the language tag e.g. "en" for "en_US".
		$lang          = strtolower( explode( '_', $store_locale )[0] );
		$target_locale = $lang . '-' . $region;

		// Check if the target locale is supported.
		if ( ! in_array( $target_locale, $supported_locales[ $region ], true ) ) {
			return null;
		}

		return $target_locale;
	}

	/**
	 * Adds mandate data to the request.
	 *
	 * @param array $request The request to add mandate data to.
	 *
	 * @return array The request with mandate data added.
	 */
	public static function add_mandate_data( $request ) {
		$ip_address = WC_Geolocation::get_ip_address();

		// Handle cases where WC_Geolocation::get_ip_address() returns multiple, comma-separated IP addresses.
		// This will be addressed upstream in WooCommerce 9.9.0 as of (https://github.com/woocommerce/woocommerce/pull/57284).
		// TODO: Remove this block when WooCommerce 9.9.0 is released.
		if ( str_contains( $ip_address, ',' ) ) {
			$ip_address = trim( current( preg_split( '/,/', $ip_address ) ) );
		}

		self::maybe_log_ip_issues( $ip_address );

		$request['mandate_data'] = [
			'customer_acceptance' => [
				'type'   => 'online',
				'online' => [
					'ip_address' => $ip_address,
					'user_agent' => 'WooCommerce Stripe Gateway' . WC_STRIPE_VERSION . '; ' . get_bloginfo( 'url' ),
				],
			],
		];

		return $request;
	}

	/**
	 * Logs an invalid IP address.
	 *
	 * @param string $ip_address The IP address to log.
	 * @return void
	 */
	public static function maybe_log_ip_issues( $ip_address ) {
		if ( rest_is_ip_address( $ip_address ) === false ) {
			$log_data = [ 'WC_Geolocation::get_ip_address()' => $ip_address ];
			$headers  = [
				'HTTP_X_REAL_IP',
				'HTTP_X_FORWARDED_FOR',
				'REMOTE_ADDR',
			];
			foreach ( $headers as $header ) {
				$log_data[ $header ] = isset( $_SERVER[ $header ] ) ? sanitize_text_field( wp_unslash( $_SERVER[ $header ] ) ) : 'not set';
			}

			WC_Stripe_Logger::log( 'Invalid IP address detected. Data: ' . wp_json_encode( $log_data ) );
		}
	}

	/**
	 * Return capability ID based on payment method ID.
	 *
	 * @param string $payment_method_id The payment method ID.
	 * @return string The capability ID.
	 */
	public static function get_payment_method_capability_id( $payment_method_id ) {
		// "_payments" is a suffix that comes from Stripe API, except when it is "transfers" or ACH.
		if ( WC_Stripe_UPE_Payment_Method_ACH::STRIPE_ID === $payment_method_id ) {
			return $payment_method_id . '_ach_payments';
		}

		return $payment_method_id . '_payments';
	}

	/**
	 * Renders the admin header with back link consistently across admin pages.
	 *
	 * @param string $header_text The text to display in the header.
	 * @param string $return_text The text for the return link.
	 * @param string $return_url  The URL for the return link.
	 * @return void
	 */
	public static function render_admin_header( $header_text, $return_text, $return_url ) {
		if ( function_exists( 'wc_back_header' ) ) {
			wc_back_header( $header_text, $return_text, $return_url );
		} else {
			// Until the wc_back_header function is available (WC Core 9.9) use the current available version.
			echo '<h2>' . esc_html( $header_text );
			wc_back_link( $return_text, $return_url );
			echo '</h2>';
		}
	}

	/**
	 * Checks if a given currency is supported for Indian recurring payment mandates.
	 *
	 * @since 9.4.0
	 * @param string $currency The currency code to check (e.g., 'usd', 'eur').
	 * @return bool True if the currency is supported, false otherwise.
	 */
	public static function is_currency_supported_for_indian_recurring_payment_mandate( $currency ) {
		// India recurring payment mandates can only be requested for the following currencies.
		$supported_currencies = [
			'inr', // Indian Rupee
			'usd', // US Dollar
			'eur', // Euro
			'gbp', // British Pound
			'sgd', // Singapore Dollar
			'cad', // Canadian Dollar
			'chf', // Swiss Franc
			'sek', // Swedish Krona
			'aed', // UAE Dirham
			'jpy', // Japanese Yen
			'nok', // Norwegian Krone
			'myr', // Malaysian Ringgit
			'hkd', // Hong Kong Dollar
		];

		return in_array( strtolower( $currency ), $supported_currencies, true );
	}

	/**
	 * Checks if the payment method should be saved.
	 *
	 * @since 9.6.0
	 * @param bool $force_save Whether the payment method should be saved.
	 * @param string $order_id Order ID.
	 * @return bool
	 */
	public static function should_force_save_payment_method( $force_save = false, $order_id = null ) {
		// Do not save the payment method if the user is not logged in.
		if ( ! is_user_logged_in() ) {
			return false;
		}

		// Backward compatibility for deprecated 'wc_stripe_force_save_source' filter.
		$force_save_payment_method = apply_filters_deprecated(
			'wc_stripe_force_save_source',
			[ $force_save, $order_id ],
			'9.6.0',
			'wc_stripe_force_save_payment_method',
			'The wc_stripe_force_save_source filter is deprecated since WooCommerce Stripe Gateway 9.6.0. Use wc_stripe_force_save_payment_method instead.'
		);

		/**
		 * Filters the flag that decides if the payment method must be saved in all possible situations.
		 *
		 * @since 9.6.0
		 *
		 * @param bool   $force_save Whether the payment method must be saved.
		 * @param string $order_id   Order ID.
		 *
		 * @return bool Whether the payment method must be saved in all situations.
		*/
		$force_save_payment_method = apply_filters( 'wc_stripe_force_save_payment_method', $force_save_payment_method, $order_id );

		return $force_save_payment_method;
	}

	/**
	 * Returns the description for a refund reason.
	 *
	 * @return string
	 */
	public static function get_refund_reason_description( $refund_reason_key ) {
		switch ( $refund_reason_key ) {
			case 'charge_for_pending_refund_disputed':
				return __( 'The charge has been disputed', 'woocommerce-gateway-stripe' );
			case 'declined':
				return __( 'The refund was declined', 'woocommerce-gateway-stripe' );
			case 'expired_or_canceled_card':
				return __( 'The original payment method has expired or was canceled', 'woocommerce-gateway-stripe' );
			case 'insufficient_funds':
				return __( 'We could not process the refund at this time', 'woocommerce-gateway-stripe' );
			case 'lost_or_stolen_card':
				return __( 'The original payment method was lost or stolen', 'woocommerce-gateway-stripe' );
			case 'merchant_request':
				return __( 'We stopped processing the refund', 'woocommerce-gateway-stripe' );
			case 'unknown':
			default:
				return __( 'Unknown reason', 'woocommerce-gateway-stripe' );
		}
	}

	/**
	 * Checks if there are other Buy Now Pay Later plugins active.
	 *
	 * @return bool
	 */
	public static function has_other_bnpl_plugins_active() {
		$other_bnpl_gateway_ids = [ self::OFFICIAL_PLUGIN_ID_AFFIRM, self::OFFICIAL_PLUGIN_ID_KLARNA ];
		foreach ( $other_bnpl_gateway_ids as $bnpl_gateway_id ) {
			if ( self::has_gateway_plugin_active( $bnpl_gateway_id ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Checks if a given payment gateway plugin is active.
	 *
	 * @param string $plugin_id
	 * @return bool
	 */
	public static function has_gateway_plugin_active( $plugin_id ) {
		$available_payment_gateways = WC()->payment_gateways->payment_gateways ?? [];
		foreach ( $available_payment_gateways as $available_payment_gateway ) {
			if ( $plugin_id === $available_payment_gateway->id && 'yes' === $available_payment_gateway->enabled ) {
				return true;
			}
		}
		return false;
	}
}
