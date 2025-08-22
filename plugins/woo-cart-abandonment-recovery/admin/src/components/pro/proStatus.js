/**
 * WCAR Pro Status - Minimal Implementation
 * Only the two functions needed for pro feature access control
 *
 * @package
 * @since 1.0.0
 */

import { __ } from '@wordpress/i18n';

/**
 * Check if pro features are fully accessible
 * Requires: Pro plugin installed + activated + valid license
 *
 * @return {boolean} true if pro features should be accessible
 */
export const canAccessProFeatures = () => {
	const adminData = cart_abandonment_admin || {};
	// Simple check: is_pro AND license_status both true
	return Boolean( adminData.is_pro ) && Boolean( adminData.license_status );
};

/**
 * Check if pro features should be blocked
 * Inverse of canAccessProFeatures() for easier conditional logic
 *
 * @return {boolean} true if pro features should be blocked
 */
export const shouldBlockProFeatures = () => {
	return ! canAccessProFeatures();
};

/**
 * Get upgrade message based on pro status
 *
 * @param {string} subTitle - The SubTitle message.
 * @return {string} Upgrade message.
 */
export const getUpgradeMessage = ( subTitle ) => {
	const adminData = cart_abandonment_admin || {};

	if ( ! adminData.is_pro ) {
		return subTitle;
	} else if ( ! adminData.license_status ) {
		return __(
			'Activate your license to unlock pro features',
			'woo-cart-abandonment-recovery'
		);
	}

	return __(
		'Upgrade to Pro for advanced features',
		'woo-cart-abandonment-recovery'
	);
};

/**
 * Get pro status message
 *
 * @return {string} Status message
 */
export const getProStatusMessage = () => {
	const adminData = cart_abandonment_admin || {};

	if ( ! adminData.is_pro ) {
		return __(
			'Pro plugin not installed',
			'woo-cart-abandonment-recovery'
		);
	} else if ( ! adminData.license_status ) {
		return __(
			'Pro plugin installed but license not activated',
			'woo-cart-abandonment-recovery'
		);
	}

	return '';
};

/**
 * Get action button text based on status
 *
 * @return {string} Button text
 */
export const getActionButtonText = () => {
	const adminData = cart_abandonment_admin || {};

	if ( ! adminData.is_pro ) {
		return __( 'Get Pro Version', 'woo-cart-abandonment-recovery' );
	} else if ( ! adminData.license_status ) {
		return __( 'Activate License', 'woo-cart-abandonment-recovery' );
	}

	return __( 'Upgrade Now', 'woo-cart-abandonment-recovery' );
};

/**
 * Get recommended action
 *
 * @return {string} Recommended action
 */
export const getRecommendedAction = () => {
	return 'upgrade';
};

/**
 * Get status severity
 *
 * @return {string} Status severity
 */
export const getStatusSeverity = () => {
	return 'info';
};

/**
 * Upgrade actions enum
 */
export const UPGRADE_ACTIONS = {
	PURCHASE: 'purchase',
	ACTIVATE: 'activate',
	UPGRADE: 'upgrade',
};

export const getUpgradeToProUrl = function ( args = '', customUrl = '' ) {
	let baseUrl = customUrl
		? customUrl
		: cart_abandonment_admin?.upgrade_to_pro_url;

	// Check if the URL has the '?' in the URL
	const hasQuestionMark = baseUrl.includes( '?' );

	if ( '' !== args ) {
		if ( hasQuestionMark ) {
			baseUrl += '&' + args;
		} else {
			baseUrl += '?' + args;
		}
	}

	// Return the fully modified URL
	return baseUrl;
};
