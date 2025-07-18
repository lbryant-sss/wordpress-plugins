<?php
/**
 * Recurring Payments
 *
 * Manages automatic activation for Recurring Payments.
 *
 * @package     EDD
 * @subpackage  Recurring
 * @copyright   Copyright (c) 2021, Easy Digital Downloads
 * @license     https://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.11.4
 */

namespace EDD\Admin\Settings;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit; // @codeCoverageIgnore

use EDD\Admin\Extensions\Extension;
use EDD\EventManagement\SubscriberInterface;

/**
 * Recurring Payments
 *
 * @since 2.11.4
 */
class Recurring extends Extension implements SubscriberInterface {

	/**
	 * The product ID on EDD.
	 *
	 * @var integer
	 */
	protected $item_id = 28530;

	/**
	 * The EDD settings tab where this extension should show.
	 *
	 * @since 2.11.4
	 * @var string
	 */
	protected $settings_tab = 'gateways';

	/**
	 * The settings section for this item.
	 *
	 * @since 2.11.5
	 * @var string
	 */
	protected $settings_section = 'recurring';

	/**
	 * Returns an array of events that this subscriber wants to listen to.
	 *
	 * @since 3.1.1
	 * @return array
	 */
	public static function get_subscribed_events() {
		return array(
			'edd_settings_sections_gateways'             => 'add_section',
			'edd_settings_tab_top_gateways_recurring'    => 'settings_field',
			'edd_settings_tab_bottom_gateways_recurring' => 'hide_submit_button',
			'edd_settings_wrap_classes'                  => 'add_wrap_class',
		);
	}

	/**
	 * Gets the custom configuration for Recurring.
	 *
	 * @since 2.11.4
	 * @param \EDD\Admin\Extensions\ProductData $product_data The product data object.
	 * @return array
	 */
	protected function get_configuration( \EDD\Admin\Extensions\ProductData $product_data ) {
		return array(
			'style'       => 'detailed-2col',
			'heading'     => __( 'Increase Revenue By Selling Subscriptions!', 'easy-digital-downloads' ),
			'description' => $this->get_custom_description(),
			'features'    => array(
				__( 'Flexible Recurring Payments', 'easy-digital-downloads' ),
				__( 'Custom Reminder Emails', 'easy-digital-downloads' ),
				__( 'Free Trial Support', 'easy-digital-downloads' ),
				__( 'Signup Fees', 'easy-digital-downloads' ),
				__( 'Recurring Revenue Reports', 'easy-digital-downloads' ),
			),
		);
	}

	/**
	 * Gets a custom description for the Recurring extension card.
	 *
	 * @since 2.11.4
	 * @return string
	 */
	private function get_custom_description() {
		$description = array(
			__( 'Grow stable income by selling subscriptions and make renewals hassle free for your customers.', 'easy-digital-downloads' ),
			__( 'When your customers are automatically billed, you reduce the risk of missed payments and retain more customers.', 'easy-digital-downloads' ),
		);

		return $this->format_description( $description );
	}

	/**
	 * Adds the Recurring Payments section to the settings.
	 *
	 * @param array $sections The existing sections.
	 * @return array
	 */
	public function add_section( $sections ) {
		if ( ! $this->can_show_product_section() ) {
			return $sections;
		}

		$sections[ $this->settings_section ] = __( 'Subscriptions', 'easy-digital-downloads' );

		return $sections;
	}

	/**
	 * Whether EDD Recurring active or not.
	 *
	 * @since 2.11.4
	 *
	 * @return bool True if Recurring is active.
	 */
	protected function is_activated() {
		if ( $this->manager->is_plugin_active( $this->get_product_data() ) ) {
			return true;
		}

		return class_exists( 'EDD_Recurring' );
	}
}
