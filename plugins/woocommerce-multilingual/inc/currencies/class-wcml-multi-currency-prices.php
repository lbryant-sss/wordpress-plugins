<?php

use WCML\COT\Helper as COTHelper;
use WCML\Orders\Helper as OrdersHelper;
use WCML\Orders\Legacy\Helper as LegacyHelper;
use WPML\FP\Fns;
use WPML\FP\Just;
use WPML\FP\Logic;
use WPML\FP\Maybe;
use WPML\FP\Nothing;
use WPML\FP\Obj;
use WPML\FP\Str;
use WPML\LIB\WP\Hooks;
use function WCML\functions\getSitePress;
use function WPML\FP\pipe;

class WCML_Multi_Currency_Prices {

	const WC_DEFAULT_STEP = 10;

	/**
	 * @var array
	 */
	private $currency_options;

	/**
	 * @var WCML_Multi_Currency
	 */
	private $multi_currency;

	/**
	 * @var bool
	 */
	private $isSavingPost = false;

	public function __construct( WCML_Multi_Currency $multi_currency, array $currency_options ) {
		$this->multi_currency   = $multi_currency;
		$this->currency_options = $currency_options;
	}

	public function add_hooks() {
		add_filter( 'wcml_raw_price_amount', [ $this, 'raw_price_filter' ], 10, 2 );

		add_filter( 'woocommerce_currency', [ $this, 'currency_filter' ] );
		add_filter( 'wcml_price_currency', [ $this, 'price_currency_filter' ] );
		add_filter( 'get_post_metadata', [ $this, 'product_price_filter' ], 10, 4 );
		add_filter( 'get_post_metadata', [ $this, 'variation_prices_filter' ], 12, 4 );
		add_filter( 'wcml_formatted_price', [ $this, 'formatted_price' ], 10, 2 );

		if ( $this->multi_currency->load_filters ) {
			add_filter(
				'wcml_product_price_by_currency',
				[
					$this,
					'get_product_price_in_currency',
				],
				10,
				2
			);  // WCML filters.
			add_filter( 'woocommerce_price_filter_widget_max_amount', [ $this, 'filter_widget_max_amount' ], 99 );
			add_filter( 'woocommerce_price_filter_widget_min_amount', [ $this, 'filter_widget_min_amount' ], 99 );

			add_filter( 'woocommerce_adjust_price', [ $this, 'raw_price_filter' ], 10 );

			// Shipping prices.
			add_filter( 'woocommerce_paypal_args', [ $this, 'filter_price_woocommerce_paypal_args' ] );
			add_filter(
				'woocommerce_get_variation_prices_hash',
				[
					$this,
					'add_currency_to_variation_prices_hash',
				]
			);
			add_filter(
				'woocommerce_cart_contents_total',
				[
					$this,
					'filter_woocommerce_cart_contents_total',
				],
				100
			);
			add_filter( 'woocommerce_cart_subtotal', [ $this, 'filter_woocommerce_cart_subtotal' ], 100, 3 );

			add_filter( 'posts_clauses', [ $this, 'price_filter_post_clauses' ], 100, 2 );

			add_action(
				'woocommerce_cart_loaded_from_session',
				[
					$this,
					'filter_currency_num_decimals_in_cart',
				]
			);

			add_filter( 'wc_price_args', [ $this, 'filter_wc_price_args' ] );
		}

		add_filter( 'wc_price_args', [ $this, 'filter_wc_price_args_on_order_admin_screen' ] );

		add_action( 'woocommerce_cart_loaded_from_session', [ $this, 'recalculate_totals' ], PHP_INT_MAX );

		// formatting options.
		add_filter( 'option_woocommerce_price_thousand_sep', [ $this, 'filter_currency_thousand_sep_option' ] );
		add_filter( 'option_woocommerce_price_decimal_sep', [ $this, 'filter_currency_decimal_sep_option' ] );
		add_filter( 'option_woocommerce_price_num_decimals', [ $this, 'filter_currency_num_decimals_option' ] );
		add_filter( 'option_woocommerce_currency_pos', Fns::withoutRecursion( Fns::identity(), [ $this, 'filter_currency_position_option' ] ) );

		// Set a flag to skip the currency filter while applying a translation.
		add_filter( 'wpml_pre_save_pro_translation', Fns::tap( [ $this, 'enableSavingPost' ] ) );
		add_action( 'wpml_pro_translation_completed', [ $this, 'disableSavingPost' ] );
	}

	public function enableSavingPost() {
		$this->isSavingPost = true;
	}

	public function disableSavingPost() {
		$this->isSavingPost = false;
	}

	public function currency_filter( $currency ) {

		if ( $this->is_multi_currency_filters_loaded() ) {
			$currency = apply_filters( 'wcml_price_currency', $currency );
		}

		return $currency;
	}

	public function price_currency_filter( $currency ) {

		if ( empty( $currency ) || $this->is_multi_currency_filters_loaded() ) {
			$currency = $this->multi_currency->get_client_currency();
		}

		return $currency;
	}

	/**
	 * @param float $min_amount
	 *
	 * @return float
	 */
	public function filter_widget_min_amount( $min_amount ) {
		$step = $this->get_filter_widget_amount_step();

		return floor( $this->raw_price_filter( $min_amount ) / $step ) * $step;
	}

	/**
	 * @param float $max_price
	 *
	 * @return float
	 */
	public function filter_widget_max_amount( $max_price ) {
		$step = $this->get_filter_widget_amount_step();
		return ceil( $this->raw_price_filter( $max_price ) / $step ) * $step;
	}

	/**
	 * @return int
	 */
	private function get_filter_widget_amount_step() {
		return max( apply_filters( 'woocommerce_price_filter_widget_step', self::WC_DEFAULT_STEP ), 1 );
	}

	public function raw_price_filter( $price, $currency = false ) {

		if ( false === $currency ) {
			$currency = $this->multi_currency->get_client_currency();
		}

		if ( wcml_get_woocommerce_currency_option() !== $currency ) {
			$price = $this->convert_price_amount( $price, $currency );
			$price = $this->apply_rounding_rules( $price, $currency );
		}

		return $price;

	}

	public function get_product_price_in_currency( $product_id, $currency = false ) {

		if ( ! $currency ) {
			$currency = $this->multi_currency->get_client_currency();
		}

		remove_filter(
			'get_post_metadata',
			[
				$this,
				'product_price_filter',
			],
			10
		);

		$manual_prices = $this->multi_currency->custom_prices->get_product_custom_prices( $product_id, $currency );

		if ( $manual_prices && isset( $manual_prices['_price'] ) ) {

			$price = $manual_prices['_price'];

		} else {

			$product = wc_get_product( $product_id );
			$price   = $this->raw_price_filter( wc_get_price_including_tax( $product ), $currency );

		}

		add_filter(
			'get_post_metadata',
			[
				$this,
				'product_price_filter',
			],
			10,
			4
		);

		return $price;

	}

	/**
	 * @param mixed|null $null
	 * @param int        $object_id
	 * @param string     $meta_key
	 * @param bool       $single
	 *
	 * @return mixed
	 */
	public function product_price_filter( $null, $object_id, $meta_key, $single ) {
		static $unlocked = true;

		if (
			$unlocked
			&& ! $this->isSavingPost
			&& $this->is_multi_currency_filters_loaded()
			&& in_array( get_post_type( $object_id ), [ 'product', 'product_variation' ], true )
			&& in_array( $meta_key, wcml_price_custom_fields( $object_id ), true )
		) {
			$unlocked = false;
			$currency = $this->multi_currency->get_client_currency();

			// $get_price_by_legacy_ccr :: void -> float|void
			$get_price_by_legacy_ccr = function() use ( $object_id, $meta_key, $single, $currency ) {
				// exception for products migrated from before WCML 3.1 with independent prices.
				// legacy prior 3.1.
				$original_object_id = apply_filters( 'wpml_object_id', $object_id, get_post_type( $object_id ), false, getSitePress()->get_default_language() );
				$ccr_rate           = Obj::path( [ $meta_key, $currency ], (array) get_post_meta( $original_object_id, '_custom_conversion_rate', true ) );

				if (
					$ccr_rate
					&& in_array( $meta_key, [ '_price', '_regular_price', '_sale_price' ], true )
				) {
					$price_original = get_post_meta( $original_object_id, $meta_key, $single );
					if ( is_numeric( $price_original ) ) {
						return $price_original * $ccr_rate;
					}
				}
			};

			// $get_manual_price :: void -> float|void
			$get_manual_price = function() use ( $object_id, $meta_key, $currency ) {
				return Obj::prop( $meta_key, (array) $this->multi_currency->custom_prices->get_product_custom_prices( $object_id, $currency ) );
			};

			// $get_price_by_auto_conversion :: void -> float|void
			$get_price_by_auto_conversion = function() use ( $object_id, $meta_key, $single ) {
				$price_original = get_post_meta( $object_id, $meta_key, $single );
				if ( is_numeric( $price_original ) ) {
					return apply_filters( 'wcml_raw_price_amount', $price_original );
				}
			};

			$get_price = Logic::firstSatisfying(
				Logic::isNotNull(),
				[
					$get_price_by_legacy_ccr,
					$get_manual_price,
					$get_price_by_auto_conversion,
				]
			);

			$price = $get_price( null );

			$unlocked = true;
		}

		return isset( $price ) ? $price : $null;
	}

	public function variation_prices_filter( $null, $object_id, $meta_key, $single ) {

		if ( empty( $meta_key ) && get_post_type( $object_id ) === 'product_variation' ) {
			static $no_filter = false;

			if ( empty( $no_filter ) && $this->is_multi_currency_filters_loaded() ) {
				$no_filter = true;

				$variation_fields = get_post_meta( $object_id );

				$manual_prices = $this->multi_currency->custom_prices->get_product_custom_prices( $object_id, $this->multi_currency->get_client_currency() );

				foreach ( $variation_fields as $k => $v ) {

					if ( in_array( $k, [ '_price', '_regular_price', '_sale_price' ], true ) ) {

						foreach ( $v as $j => $amount ) {

							if ( isset( $manual_prices[ $k ] ) ) {
								$variation_fields[ $k ][ $j ] = $manual_prices[ $k ];     // manual price.

							} elseif ( $amount ) {
								$variation_fields[ $k ][ $j ] = apply_filters( 'wcml_raw_price_amount', $amount );   // automatic conversion.
							}
						}
					}
				}

				$no_filter = false;
			}
		}

		return isset( $variation_fields ) ? $variation_fields : $null;

	}

	/**
	 * @param mixed       $amount
	 * @param bool|string $currency
	 *
	 * @return mixed
	 */
	public function convert_price_amount( $amount, $currency = false ) {

		if ( empty( $currency ) ) {
			$currency = $this->multi_currency->get_client_currency();
		}

		if ( wcml_get_woocommerce_currency_option() !== $currency ) {

			$amount = $this->calculate_exchange_rate_price( $amount, $currency, '*' );

		}

		return $amount;

	}

	/**
	 * @param mixed  $amount
	 * @param string $from_currency
	 * @param string $to_currency
	 *
	 * @return mixed
	 */
	public function convert_price_amount_by_currencies( $amount, $from_currency, $to_currency ) {

		if ( wcml_get_woocommerce_currency_option() !== $to_currency ) {
			$amount = $this->calculate_exchange_rate_price( $amount, $to_currency, '*' );
		} else {
			$amount = $this->calculate_exchange_rate_price( $amount, $from_currency, '/' );
		}

		return $amount;
	}

	/**
	 * @param mixed  $amount
	 * @param string $currency
	 * @param string $operator
	 *
	 * @return mixed
	 */
	private function calculate_exchange_rate_price( $amount, $currency, $operator ) {

		$exchange_rates = $this->multi_currency->get_exchange_rates();

		$initialType = gettype( $amount );

		if ( isset( $exchange_rates[ $currency ] ) && is_numeric( $amount ) ) {

			if ( '*' === $operator ) {
				$amount = $amount * $exchange_rates[ $currency ];
			} elseif ( '/' === $operator ) {
				$amount = $amount / $exchange_rates[ $currency ];
			}

			// exception - currencies_without_cents.
			if ( in_array( $currency, $this->multi_currency->get_currencies_without_cents(), true ) ) {
				$amount = $this->round_up( $amount );
			}
		} else {
			$amount = 0;
		}

		if ( 'string' === $initialType ) {
			$amount = (string) $amount;
		}

		return $amount;

	}

	/**
	 * Convert back to default currency.
	 *
	 * @param float        $amount
	 * @param string|false $currency
	 *
	 * @return float
	 */
	public function unconvert_price_amount( $amount, $currency = false ) {

		if ( empty( $currency ) ) {
			$currency = $this->multi_currency->get_client_currency();
		}

		if ( wcml_get_woocommerce_currency_option() !== $currency ) {

			$exchange_rates = $this->multi_currency->get_exchange_rates();

			if ( isset( $exchange_rates[ $currency ] ) && is_numeric( $amount ) ) {
				$amount = $amount / $exchange_rates[ $currency ];

				// exception - currencies_without_cents.
				if ( in_array( $currency, $this->multi_currency->get_currencies_without_cents(), true ) ) {
					$amount = $this->round_up( $amount );
				}
			} else {
				$amount = 0;
			}
		}

		return $amount;

	}

	public function apply_rounding_rules( $price, $currency = false ) {

		if ( is_null( $this->currency_options ) ) {
			global $woocommerce_wpml;
			$this->currency_options = $woocommerce_wpml->get_setting( 'currency_options' );
		}

		if ( ! $currency ) {
			$currency = $this->multi_currency->get_client_currency();
		}

		$currency_options = $this->currency_options[ $currency ];

		if ( 'disabled' !== $currency_options['rounding'] ) {

			if ( $currency_options['rounding_increment'] > 1 ) {
				$price = $price / $currency_options['rounding_increment'];
			}

			switch ( $currency_options['rounding'] ) {
				case 'up':
					$rounded_price = ceil( $price );
					break;
				case 'down':
					$rounded_price = floor( $price );
					break;
				case 'nearest':
				default:
					$rounded_price = $this->round_up( $price );
					break;
			}

			if ( $rounded_price > 0 ) {
				$price = $rounded_price;
			}

			if ( $currency_options['rounding_increment'] > 1 ) {
				$price = $price * $currency_options['rounding_increment'];
			}

			if ( $currency_options['auto_subtract'] && $currency_options['auto_subtract'] < $price ) {
				$price = $price - $currency_options['auto_subtract'];
			}
		} else {

			// Use configured number of decimals.
			$price = round( $price, $currency_options['num_decimals'] );

		}

		return apply_filters( 'wcml_rounded_price', $price, $currency );

	}

	/**
	 * The PHP 5.2 compatible equivalent to "round($amount, 0, PHP_ROUND_HALF_UP)"
	 *
	 * @param float|int $amount
	 *
	 * @return float|int
	 */
	private function round_up( $amount ) {
		if ( $amount - floor( $amount ) < 0.5 ) {
			$amount = floor( $amount );
		} else {
			$amount = ceil( $amount );
		}

		return $amount;
	}

	/**
	 * Converts the price from the default currency to the given currency and applies the format
	 *
	 * @param float|int    $amount
	 * @param false|string $currency
	 */
	public function formatted_price( $amount, $currency = false ) {

		if ( false === $currency ) {
			$currency = $this->multi_currency->get_client_currency();
		}

		$amount = $this->raw_price_filter( $amount, $currency );

		return $this->format_price_in_currency( $amount, $currency );
	}

	/**
	 * @param float  $price
	 * @param string $currency
	 *
	 * @return string
	 */
	public function format_price_in_currency( $price, $currency ) {
		$currency_details = $this->multi_currency->get_currency_details_by_code( $currency );

		$wc_price_args = [
			'currency'           => $currency,
			'decimal_separator'  => $currency_details['decimal_sep'],
			'thousand_separator' => $currency_details['thousand_sep'],
			'decimals'           => $currency_details['num_decimals'],
			'price_format'       => $this->get_price_format_in_currency( $currency ),
		];

		return wc_price( $price, $wc_price_args );
	}

	public function filter_price_woocommerce_paypal_args( $args ) {

		foreach ( $args as $key => $value ) {
			if ( substr( $key, 0, 7 ) === 'amount_' ) {

				$currency_details = $this->multi_currency->get_currency_details_by_code( $args['currency_code'] );

				$args[ $key ] = number_format( $value, $currency_details['num_decimals'], '.', '' );
			}
		}

		return $args;
	}

	public function add_currency_to_variation_prices_hash( $data ) {

		$data['currency']            = $this->multi_currency->get_client_currency();
		$data['exchange_rates_hash'] = md5( wp_json_encode( $this->multi_currency->get_exchange_rates() ) );

		return $data;

	}

	public function filter_woocommerce_cart_contents_total( $cart_contents_total ) {
		remove_filter(
			'woocommerce_cart_contents_total',
			[
				$this,
				'filter_woocommerce_cart_contents_total',
			],
			100
		);
		$this->recalculate_totals();
		$cart_contents_total = WC()->cart->get_cart_total();
		add_filter( 'woocommerce_cart_contents_total', [ $this, 'filter_woocommerce_cart_contents_total' ], 100 );

		return $cart_contents_total;
	}

	public function recalculate_totals() {
		WC()->cart->calculate_totals();
	}

	public function filter_woocommerce_cart_subtotal( $cart_subtotal, $compound, $cart_object ) {

		remove_filter( 'woocommerce_cart_subtotal', [ $this, 'filter_woocommerce_cart_subtotal' ], 100 );

		$cart_subtotal = $cart_object->get_cart_subtotal( $compound );

		add_filter( 'woocommerce_cart_subtotal', [ $this, 'filter_woocommerce_cart_subtotal' ], 100, 3 );

		return $cart_subtotal;
	}

	public function price_filter_post_clauses( $args, $wp_query ) {

		/* phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected */
		if ( ! $wp_query->is_main_query() || ( ! isset( $_GET['max_price'] ) && ! isset( $_GET['min_price'] ) ) ) {
			return $args;
		}

		$currency = $this->multi_currency->get_client_currency();

		if ( $currency !== wcml_get_woocommerce_currency_option() ) {
			global $wpdb;
			$min_price = isset( $_GET['min_price'] ) ? floatval( wp_unslash( $_GET['min_price'] ) ) : 0;
			$max_price = isset( $_GET['max_price'] ) ? floatval( wp_unslash( $_GET['max_price'] ) ) : PHP_INT_MAX;

			$min_price_in_default_currency = $this->unconvert_price_amount( $min_price, $currency );
			$max_price_in_default_currency = $this->unconvert_price_amount( $max_price, $currency );

			$replaceSinceWc5_1 = Str::replace(
				[ $wpdb->prepare( '%f<wc_product_meta_lookup.min_price', $max_price ), $wpdb->prepare( '%f>wc_product_meta_lookup.max_price', $min_price ) ],
				[ $wpdb->prepare( '%f<wc_product_meta_lookup.min_price', $max_price_in_default_currency ), $wpdb->prepare( '%f>wc_product_meta_lookup.max_price', $min_price_in_default_currency ) ]
			);

			$replaceBeforeWc5_1 = Str::replace(
				[ $wpdb->prepare( 'wc_product_meta_lookup.min_price >= %f', $min_price ), $wpdb->prepare( 'wc_product_meta_lookup.max_price <= %f', $max_price ) ],
				[ $wpdb->prepare( 'wc_product_meta_lookup.min_price >= %f', $min_price_in_default_currency ), $wpdb->prepare( 'wc_product_meta_lookup.max_price <= %f', $max_price_in_default_currency ) ]
			);

			return Obj::over(
				Obj::lensProp( 'where' ),
				pipe( $replaceSinceWc5_1, $replaceBeforeWc5_1 ),
				$args
			);
		}

		return $args;
	}

	/**
	 * @param array  $response
	 * @param string $to_currency
	 * @param string $from_currency
	 * @param array  $params
	 *
	 * @return array
	 */
	public function filter_pre_selected_widget_prices_in_new_currency( $response, $to_currency, $from_currency, $params ) {

		wpml_collect( $params )->each(
			function ( $value, $key ) use ( &$response, $from_currency, $to_currency ) {
				if ( wpml_collect( [ 'min_price', 'max_price' ] )->contains( $key ) ) {
					$response[ $key ] = $this->convert_price_amount( $this->unconvert_price_amount( $value, $from_currency ), $to_currency );
				}
			}
		);

		return $response;
	}


	private function get_context_currency_code() {
		global $pagenow;

		if ( ( OrdersHelper::isEditingNewOrderItems() || OrdersHelper::isOrderCreateAdminScreen() ) && isset( $_COOKIE['_wcml_order_currency'] ) ) {
			$currency_code = $_COOKIE['_wcml_order_currency'];
		} elseif ( LegacyHelper::isOrderEditAdminScreen() ) {
			$currency_code = OrdersHelper::getCurrency( $_GET['post'], true );
		} elseif ( COTHelper::isOrderEditAdminScreen() && isset( $_GET['id'] ) ) {
			$currency_code = OrdersHelper::getCurrency( (int) $_GET['id'], true );
		} elseif ( isset( $_GET['page'] ) && $_GET['page'] == 'wc-reports' && isset( $_COOKIE['_wcml_reports_currency'] ) ) {
			$currency_code = $_COOKIE['_wcml_reports_currency'];
		} elseif ( isset( $_COOKIE['_wcml_dashboard_currency'] ) && is_admin() && ! defined( 'DOING_AJAX' ) && $pagenow == 'index.php' ) {
			$currency_code = $_COOKIE['_wcml_dashboard_currency']; // This case might be useless.
		} else {
			$currency_code = $this->multi_currency->get_client_currency();
		}

		return apply_filters( 'wcml_filter_currency_position', $currency_code );

	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	public function filter_currency_thousand_sep_option( $value ) {
		return $this->filter_currency_option_in_global_secondary_currency( 'thousand_sep', $value );
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	public function filter_currency_decimal_sep_option( $value ) {
		return $this->filter_currency_option_in_global_secondary_currency( 'decimal_sep', $value );
	}

	/**
	 * @param int $value
	 *
	 * @return int
	 */
	public function filter_currency_num_decimals_option( $value ) {
		return $this->filter_currency_option_in_global_secondary_currency( 'num_decimals', $value );
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	public function filter_currency_position_option( $value ) {
		return $this->filter_currency_option_in_global_secondary_currency( 'position', $value );
	}

	/**
	 * @param string $option
	 * @param mixed  $value
	 *
	 * @return mixed
	 */
	private function filter_currency_option_in_global_secondary_currency( $option, $value ) {
		$default_currency = $this->multi_currency->get_default_currency();
		$currency_code    = $this->get_context_currency_code();

		if ( $currency_code !== $default_currency ) {
			$value = $this->get_currency_option( $currency_code, $option )->getOrElse( $value );
		}

		return $value;
	}

	/**
	 * @param string $currency
	 * @param string $option
	 *
	 * @return Just|Nothing
	 */
	private function get_currency_option( $currency, $option ) {
		return Maybe::fromNullable( $this->multi_currency->currencies[ $currency ][ $option ] ?? null );
	}

	public function filter_currency_num_decimals_in_cart( $cart ) {
		$cart->dp = wc_get_price_decimals();
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	public function filter_wc_price_args_on_order_admin_screen( $args ) {
		if ( OrdersHelper::isOrderListAdminScreen() ) {
			$args = $this->filter_wc_price_args( $args );
		}
		return $args;
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	public function filter_wc_price_args( $args ) {
		$currency = Obj::prop( 'currency', $args );

		if ( $currency ) {

			foreach ( [
				'decimal_sep'  => 'decimal_sep',
				'thousand_sep' => 'thousand_sep',
				'num_decimals' => 'decimals',
			] as $wcmlOption => $wcOptions ) {
				$filteredOption = $this->get_currency_option( $currency, $wcmlOption )->getOrElse( null );

				if ( $filteredOption ) {
					$args[ $wcOptions ] = $filteredOption;
				}
			}

			if ( $this->get_currency_option( $currency, 'position' )->getOrElse( null ) ) {
				$args['price_format'] = $this->get_price_format_in_currency( $currency );
			}
		}

		return $args;
	}

	/**
	 * @param string $currency
	 *
	 * @return string
	 */
	private function get_price_format_in_currency( $currency ) {
		$useCurrentCurrencyPos = function( $value ) use ( $currency ) {
			return $this->get_currency_option( $currency, 'position' )->getOrElse( $value );
		};

		return Hooks::callWithFilter( 'get_woocommerce_price_format', 'option_woocommerce_currency_pos', $useCurrentCurrencyPos );
	}

	/**
	 * @param float       $price
	 * @param null|string $currency
	 *
	 * @return float
	 */
	public function convert_raw_woocommerce_price( $price, $currency = null ) {
		if ( null === $currency ) {
			$currency = $this->multi_currency->get_client_currency();
		}

		return apply_filters( 'wcml_raw_price_amount', $price, $currency );
	}

	/**
	 * @param float      $value
	 * @param WC_Product $product
	 *
	 * @return float
	 */
	public function get_original_product_price( $value, $product ) {
		return get_post_meta( $product->get_id(), '_price', 1 );
	}

	private function is_multi_currency_filters_loaded() {
		static $filters_loaded;

		if ( ! $filters_loaded ) {
			$filters_loaded = $this->multi_currency->are_filters_need_loading();
		}

		return $filters_loaded;
	}

}
