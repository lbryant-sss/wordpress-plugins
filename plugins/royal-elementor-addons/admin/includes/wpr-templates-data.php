<?php

namespace WprAddons\Admin\Includes;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WPR_Templates_Data setup
 *
 * @since 1.0
 */
class WPR_Templates_Data {

	/**
	** List of Predefined Templates
	*/
	public static function get( $template ) {

		// Headers
		if ( $template === 'header' ) {
			$templates = array(
			    'Header v1',
			    'Header v2',
			    'Header v3',
			    'Header v4',
			);

		// Footers
		} elseif ( $template === 'footer' ) {
			$templates = array(
			    'Footer v1',
			    'Footer v2',
			    'Footer v3',
			    'Footer v4',
			);

		// Blog Posts
		} elseif ( $template === 'blog-post' ) {
			$templates = array(
			    'Blog Post v1',
			    'Blog Post v2',
			    'Blog Post v3',
			);
			
		// Portfolio Posts
		} elseif ( $template === 'portfolio-post' ) {
			$templates = array(
			    'Portfolio Post v1',
			    'Portfolio Post v2',
			    'Portfolio Post v3',
			);
		
		// WooCommerce Products
		} elseif ( $template === 'woocommerce-product' ) {
			$templates = array(
			    'WooCommerce Product v1',
			    'WooCommerce Product v2',
			    'WooCommerce Product v3',
			);

		// 404 Pages
		} elseif ( $template === '404-page' ) {
			$templates = array(
			    '404 Page v1',
			    '404 Page v2',
			    '404 Page v3',
			);

		// Blog Archives
		} elseif ( $template === 'blog-archive' ) {
			$templates = array(
			    'Blog Archive v1',
			    'Blog Archive v2',
			    'Blog Archive v3',
			);
			
		// Portfolio Archives
		} elseif ( $template === 'portfolio-archive' ) {
			$templates = array(
			    'Portfolio Archive v1',
			    'Portfolio Archive v2',
			    'Portfolio Archive v3',
			);
		
		// WooCommerce Archives
		} elseif ( $template === 'woocommerce-archive' ) {
			$templates = array(
			    'WooCommerce Archive v1',
			    'WooCommerce Archive v2',
			    'WooCommerce Archive v3',
			);
		
		// Popups
		} elseif ( $template === 'popup' ) {
			$templates = array(
			    'Popup v1',
			    'Popup v2',
			    'Popup v3',
			);
		}

		return $templates;
	}

}