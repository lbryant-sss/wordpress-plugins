<?php
/**
 * Plugin Name: Flexible Checkout Fields
 * Plugin URI: https://www.wpdesk.net/products/flexible-checkout-fields-pro-woocommerce/
 * Description: Manage your WooCommerce checkout fields. Change order, labels, placeholders and add new fields.
 * Version: 4.1.24
 * Author: WP Desk
 * Author URI: https://www.wpdesk.net/
 * Text Domain: flexible-checkout-fields
 * Domain Path: /lang/
 * Requires at least: 6.4
 * Tested up to: 6.8
 * WC requires at least: 9.6
 * WC tested up to: 10.0
 * Requires PHP: 7.4
 *
 * Copyright 2023 WP Desk Ltd.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package Flexible Checkout Fields
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/* THIS VARIABLE CAN BE CHANGED AUTOMATICALLY */
$plugin_version = '4.1.24';

/*
 * Update when conditions are met:
 * - major version: no compatibility (disables dependent plugins)
 * - minor version: compatibility problems (displays notice in dependent plugins)
 */
$plugin_version_dev = '3.0';

define( 'FLEXIBLE_CHECKOUT_FIELDS_VERSION', $plugin_version );
define( 'FLEXIBLE_CHECKOUT_FIELDS_VERSION_DEV', $plugin_version_dev );

if ( ! defined( 'FCF_VERSION' ) ) {
	define( 'FCF_VERSION', FLEXIBLE_CHECKOUT_FIELDS_VERSION );
}

$plugin_name        = 'Flexible Checkout Fields';
$plugin_class_name  = 'Flexible_Checkout_Fields_Plugin';
$plugin_text_domain = 'flexible-checkout-fields';
$product_id         = 'Flexible Checkout Fields';
$plugin_file        = __FILE__;
$plugin_dir         = __DIR__;

define( $plugin_class_name, $plugin_version );

$requirements = [
	'php'     => '7.4',
	'wp'      => '5.2',
	'plugins' => [
		[
			'name'      => 'woocommerce/woocommerce.php',
			'nice_name' => 'WooCommerce',
		],
	],
];

add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, false );
		}
	}
);

require_once __DIR__ . '/inc/wpdesk-woo27-functions.php';
require __DIR__ . '/vendor_prefixed/wpdesk/wp-plugin-flow-common/src/plugin-init-php52-free.php';
