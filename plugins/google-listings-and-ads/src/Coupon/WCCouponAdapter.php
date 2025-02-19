<?php
declare(strict_types = 1);
namespace Automattic\WooCommerce\GoogleListingsAndAds\Coupon;

use Automattic\WooCommerce\GoogleListingsAndAds\Exception\InvalidValue;
use Automattic\WooCommerce\GoogleListingsAndAds\PluginHelper;
use Automattic\WooCommerce\GoogleListingsAndAds\Product\WCProductAdapter;
use Automattic\WooCommerce\GoogleListingsAndAds\Validator\Validatable;
use Automattic\WooCommerce\GoogleListingsAndAds\Vendor\Google\Service\ShoppingContent\PriceAmount as GooglePriceAmount;
use Automattic\WooCommerce\GoogleListingsAndAds\Vendor\Google\Service\ShoppingContent\Promotion as GooglePromotion;
use Automattic\WooCommerce\GoogleListingsAndAds\Vendor\Google\Service\ShoppingContent\TimePeriod as GoogleTimePeriod;
use DateInterval;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use WC_DateTime;
use WC_Coupon;
defined( 'ABSPATH' ) || exit();

/**
 * Class WCCouponAdapter
 *
 * This class adapts the WooCommerce coupon class to the Google's Promotion class by mapping their attributes.
 *
 * @package Automattic\WooCommerce\GoogleListingsAndAds\Coupon
 */
class WCCouponAdapter extends GooglePromotion implements Validatable {
	use PluginHelper;

	public const CHANNEL_ONLINE = 'ONLINE';

	public const PRODUCT_APPLICABILITY_ALL_PRODUCTS = 'ALL_PRODUCTS';

	public const PRODUCT_APPLICABILITY_SPECIFIC_PRODUCTS = 'SPECIFIC_PRODUCTS';

	public const OFFER_TYPE_GENERIC_CODE = 'GENERIC_CODE';

	public const PROMOTION_DESTINATION_ADS = 'Shopping_ads';

	public const PROMOTION_DESTINATION_FREE_LISTING = 'Free_listings';

	public const WC_DISCOUNT_TYPE_PERCENT = 'percent';

	public const WC_DISCOUNT_TYPE_FIXED_CART = 'fixed_cart';

	public const WC_DISCOUNT_TYPE_FIXED_PRODUCT = 'fixed_product';

	public const COUPON_VALUE_TYPE_MONEY_OFF = 'MONEY_OFF';

	public const COUPON_VALUE_TYPE_PERCENT_OFF = 'PERCENT_OFF';

	protected const DATE_TIME_FORMAT = 'Y-m-d h:i:sa';

	public const COUNTRIES_WITH_FREE_SHIPPING_DESTINATION = [ 'BR', 'IT', 'ES', 'JP', 'NL', 'KR', 'US' ];

	/**
	 *
	 * @var int wc_coupon_id
	 */
	protected $wc_coupon_id;

	/**
	 * Initialize this object's properties from an array.
	 *
	 * @param array $properties Used to seed this object's properties.
	 *
	 * @return void
	 *
	 * @throws InvalidValue When a WooCommerce coupon is not provided or it is invalid.
	 */
	public function mapTypes( $properties ) {
		if ( empty( $properties['wc_coupon'] ) ||
			! $properties['wc_coupon'] instanceof WC_Coupon ) {
				throw InvalidValue::not_instance_of( WC_Coupon::class, 'wc_coupon' );
		}

		$wc_coupon          = $properties['wc_coupon'];
		$this->wc_coupon_id = $wc_coupon->get_id();
		$this->map_woocommerce_coupon( $wc_coupon, $this->get_coupon_destinations( $properties ) );

		// Google doesn't expect extra fields, so it's best to remove them
		unset( $properties['wc_coupon'] );

		parent::mapTypes( $properties );
	}

	/**
	 * Map the WooCommerce coupon attributes to the current class.
	 *
	 * @param WC_Coupon $wc_coupon
	 * @param string[]  $destinations The destination ID's for the coupon
	 *
	 * @return void
	 */
	protected function map_woocommerce_coupon( WC_Coupon $wc_coupon, array $destinations ) {
		$this->setRedemptionChannel( self::CHANNEL_ONLINE );
		$this->setPromotionDestinationIds( $destinations );

		$content_language = empty( get_locale() ) ? 'en' : strtolower(
			substr( get_locale(), 0, 2 )
		); // ISO 639-1.
		$this->setContentLanguage( $content_language );

		$this->map_wc_coupon_id( $wc_coupon )
			->map_wc_general_attributes( $wc_coupon )
			->map_wc_usage_restriction( $wc_coupon );
	}

	/**
	 * Map the WooCommerce coupon ID.
	 *
	 * @param WC_Coupon $wc_coupon
	 *
	 * @return $this
	 */
	protected function map_wc_coupon_id( WC_Coupon $wc_coupon ): WCCouponAdapter {
		$coupon_id = "{$this->get_slug()}_{$wc_coupon->get_id()}";
		$this->setPromotionId( $coupon_id );

		return $this;
	}

	/**
	 * Map the general WooCommerce coupon attributes.
	 *
	 * @param WC_Coupon $wc_coupon
	 *
	 * @return $this
	 */
	protected function map_wc_general_attributes( WC_Coupon $wc_coupon ): WCCouponAdapter {
		$this->setOfferType( self::OFFER_TYPE_GENERIC_CODE );
		$this->setGenericRedemptionCode( $wc_coupon->get_code() );

		$coupon_amount = $wc_coupon->get_amount();
		if ( $wc_coupon->is_type( self::WC_DISCOUNT_TYPE_PERCENT ) ) {
			$this->setCouponValueType( self::COUPON_VALUE_TYPE_PERCENT_OFF );
			$percent_off = round( floatval( $coupon_amount ) );
			$this->setPercentOff( $percent_off );
			$this->setLongtitle( sprintf( '%d%% off', $percent_off ) );
		} elseif ( $wc_coupon->is_type(
			[
				self::WC_DISCOUNT_TYPE_FIXED_CART,
				self::WC_DISCOUNT_TYPE_FIXED_PRODUCT,
			]
		) ) {
			$this->setCouponValueType( self::COUPON_VALUE_TYPE_MONEY_OFF );
			$this->setMoneyOffAmount(
				$this->map_google_price_amount( $coupon_amount )
			);
			$this->setLongtitle(
				sprintf(
					'%d %s off',
					$coupon_amount,
					get_woocommerce_currency()
				)
			);
		}

		$this->setPromotionEffectiveTimePeriod(
			$this->get_wc_coupon_effective_dates( $wc_coupon )
		);

		return $this;
	}

	/**
	 * Return the effective time period for the WooCommerce coupon.
	 *
	 * @param WC_Coupon $wc_coupon
	 *
	 * @return GoogleTimePeriod
	 */
	protected function get_wc_coupon_effective_dates( WC_Coupon $wc_coupon ): GoogleTimePeriod {
		$start_date = $this->get_wc_coupon_start_date( $wc_coupon );

		$end_date = $wc_coupon->get_date_expires();
		// If there is no expiring date, set to promotion maximumal effective days allowed by Google.\
		// Refer to https://support.google.com/merchants/answer/2906014?hl=en
		if ( empty( $end_date ) ) {
			$end_date = clone $start_date;
			$end_date->add( new DateInterval( 'P183D' ) );
		}

		// If the coupon is already expired. set the coupon expires immediately after start date.
		if ( $end_date < $start_date ) {
			$end_date = clone $start_date;
			$end_date->add( new DateInterval( 'PT1S' ) );
		}
		return new GoogleTimePeriod(
			[
				'startTime' => (string) $start_date,
				'endTime'   => (string) $end_date,
			]
		);
	}

	/**
	 * Return the start date for the WooCommerce coupon.
	 *
	 * @param WC_Coupon $wc_coupon
	 *
	 * @return WC_DateTime
	 */
	protected function get_wc_coupon_start_date( $wc_coupon ): WC_DateTime {
		new WC_DateTime();
		$post_time = get_post_time( self::DATE_TIME_FORMAT, true, $wc_coupon->get_id(), false );
		if ( ! empty( $post_time ) ) {
			return new WC_DateTime( $post_time );
		} else {
			return new WC_DateTime();
		}
	}

	/**
	 * Map the WooCommerce coupon usage restriction.
	 *
	 * @param WC_Coupon $wc_coupon
	 *
	 * @return $this
	 */
	protected function map_wc_usage_restriction( WC_Coupon $wc_coupon ): WCCouponAdapter {
		$minimal_spend = $wc_coupon->get_minimum_amount();
		if ( ! empty( $minimal_spend ) ) {
			$this->setMinimumPurchaseAmount(
				$this->map_google_price_amount( $minimal_spend )
			);
		}

		$maximal_spend = $wc_coupon->get_maximum_amount();
		if ( ! empty( $maximal_spend ) ) {
			$this->setLimitValue(
				$this->map_google_price_amount( $maximal_spend )
			);
		}

		$has_product_restriction = false;
		$get_offer_id            = function ( int $product_id ) {
			return WCProductAdapter::get_google_product_offer_id( $this->get_slug(), $product_id );
		};

		$wc_product_ids = $wc_coupon->get_product_ids();
		if ( ! empty( $wc_product_ids ) ) {
			$google_product_ids      = array_map( $get_offer_id, $wc_product_ids );
			$has_product_restriction = true;
			$this->setItemId( $google_product_ids );
		}

		// Currently the brand inclusion restriction will override the product inclustion restriction.
		// It's align with the current coupon discounts behaviour in WooCommerce.
		$wc_product_ids_in_brand = $this->get_product_ids_in_brand( $wc_coupon );
		if ( ! empty( $wc_product_ids_in_brand ) ) {
			$google_product_ids      = array_map( $get_offer_id, $wc_product_ids_in_brand );
			$has_product_restriction = true;
			$this->setItemId( $google_product_ids );
		}

		// Get excluded product IDs and excluded product IDs in brand.
		$wc_excluded_product_ids          = $wc_coupon->get_excluded_product_ids();
		$wc_excluded_product_ids_in_brand = $this->get_product_ids_in_brand( $wc_coupon, true );
		if ( ! empty( $wc_excluded_product_ids ) || ! empty( $wc_excluded_product_ids_in_brand ) ) {
			$google_product_ids = array_merge(
				array_map( $get_offer_id, $wc_excluded_product_ids ),
				array_map( $get_offer_id, $wc_excluded_product_ids_in_brand )
			);
			$google_product_ids = array_values( array_unique( $google_product_ids ) );

			$has_product_restriction = true;
			$this->setItemIdExclusion( $google_product_ids );
		}

		$wc_product_catetories = $wc_coupon->get_product_categories();
		if ( ! empty( $wc_product_catetories ) ) {
			$str_product_categories  =
			WCProductAdapter::convert_product_types( $wc_product_catetories );
			$has_product_restriction = true;
			$this->setProductType( $str_product_categories );
		}

		$wc_excluded_product_catetories = $wc_coupon->get_excluded_product_categories();
		if ( ! empty( $wc_excluded_product_catetories ) ) {
			$str_product_categories  =
			WCProductAdapter::convert_product_types( $wc_excluded_product_catetories );
			$has_product_restriction = true;
			$this->setProductTypeExclusion( $str_product_categories );
		}

		if ( $has_product_restriction ) {
			$this->setProductApplicability(
				self::PRODUCT_APPLICABILITY_SPECIFIC_PRODUCTS
			);
		} else {
			$this->setProductApplicability(
				self::PRODUCT_APPLICABILITY_ALL_PRODUCTS
			);
		}

		return $this;
	}

	/**
	 * Map WooCommerce price number to Google price structure.
	 *
	 * @param float $wc_amount
	 *
	 * @return GooglePriceAmount
	 */
	protected function map_google_price_amount( $wc_amount ): GooglePriceAmount {
		return new GooglePriceAmount(
			[
				'currency' => get_woocommerce_currency(),
				'value'    => $wc_amount,
			]
		);
	}

	/**
	 * Disable promotion shared in Google by only updating promotion effective end_date
	 * to make the promotion expired.
	 *
	 * @param WC_Coupon $wc_coupon
	 */
	public function disable_promotion( WC_Coupon $wc_coupon ) {
		$start_date = $this->get_wc_coupon_start_date( $wc_coupon );
		// Set promotion to be disabled immediately.
		$end_date = new WC_DateTime();

		// If this coupon is scheduled in the future, disable it right after start date.
		if ( $start_date >= $end_date ) {
			$end_date = clone $start_date;
			$end_date->add( new DateInterval( 'PT1S' ) );
		}

		$this->setPromotionEffectiveTimePeriod(
			new GoogleTimePeriod(
				[
					'startTime' => (string) $start_date,
					'endTime'   => (string) $end_date,
				]
			)
		);
	}

	/**
	 *
	 * @param ClassMetadata $metadata
	 */
	public static function load_validator_metadata( ClassMetadata $metadata ) {
		$metadata->addPropertyConstraint(
			'targetCountry',
			new Assert\NotBlank()
		);
		$metadata->addPropertyConstraint(
			'promotionId',
			new Assert\NotBlank()
		);
		$metadata->addPropertyConstraint(
			'genericRedemptionCode',
			new Assert\NotBlank()
		);
		$metadata->addPropertyConstraint(
			'productApplicability',
			new Assert\NotBlank()
		);
		$metadata->addPropertyConstraint(
			'offerType',
			new Assert\NotBlank()
		);
		$metadata->addPropertyConstraint(
			'redemptionChannel',
			new Assert\NotBlank()
		);
		$metadata->addPropertyConstraint(
			'couponValueType',
			new Assert\NotBlank()
		);
	}

	/**
	 *
	 * @return int $wc_coupon_id
	 */
	public function get_wc_coupon_id(): int {
		return $this->wc_coupon_id;
	}

	/**
	 *
	 * @param string $targetCountry
     *            phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	 */
	public function setTargetCountry( $targetCountry ) {
		// set the new target country
		parent::setTargetCountry( $targetCountry );
	}

	/**
	 * Get the destinations allowed per specific country.
	 *
	 * @param array $coupon_data The coupon data to get the allowed destinations.
	 * @return string[] The destinations country based.
	 */
	private function get_coupon_destinations( array $coupon_data ): array {
		$destinations = [ self::PROMOTION_DESTINATION_ADS ];
		if ( isset( $coupon_data['targetCountry'] ) && in_array( $coupon_data['targetCountry'], self::COUNTRIES_WITH_FREE_SHIPPING_DESTINATION, true ) ) {
			$destinations[] = self::PROMOTION_DESTINATION_FREE_LISTING;
		}

		return apply_filters( 'woocommerce_gla_coupon_destinations', $destinations, $coupon_data );
	}

	/**
	 * Get the product IDs that belongs to a brand.
	 *
	 * @param WC_Coupon $wc_coupon The WC coupon object.
	 * @param bool      $is_exclude If the product IDs are for exclusion.
	 * @return string[] The product IDs that belongs to a brand.
	 */
	private function get_product_ids_in_brand( WC_Coupon $wc_coupon, bool $is_exclude = false ) {
		$coupon_id = $wc_coupon->get_id();
		$meta_key  = $is_exclude ? 'exclude_product_brands' : 'product_brands';

		// Get the brand term IDs if brand restriction is set.
		$brand_term_ids = get_post_meta( $coupon_id, $meta_key );

		if ( ! is_array( $brand_term_ids ) ) {
			return [];
		}

		$product_ids = [];
		foreach ( $brand_term_ids as $brand_term_id ) {
			// Get the product IDs that belongs to the brand.
			$object_ids = get_objects_in_term( $brand_term_id, 'product_brand' );
			if ( is_wp_error( $object_ids ) ) {
				continue;
			}
			$product_ids = array_merge( $product_ids, $object_ids );
		}

		return $product_ids;
	}
}
