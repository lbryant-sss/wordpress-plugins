<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qi_blocks_check_qi_blocks_premium_requirements' ) ) {
	/**
	 * Function that checks plugin requirements
	 */
	function qi_blocks_check_qi_blocks_premium_requirements() {

		if ( version_compare( QI_BLOCKS_VERSION, '1.3.5', '>' ) && defined( 'QI_BLOCKS_PREMIUM_VERSION' ) && version_compare( QI_BLOCKS_PREMIUM_VERSION, '1.0.7', '<' ) ) {
			echo sprintf( '<div class="notice notice-error"><p>%s</p></div>', esc_html__( 'Please upgrade Qi Blocks Premium plugin to the latest version in order to work properly.', 'qi-blocks' ) );

			if ( function_exists( 'deactivate_plugins' ) ) {
				deactivate_plugins( 'qi-blocks-premium/class-qi-blocks-premium.php' );
			}
		}
	}

	add_action( 'plugins_loaded', 'qi_blocks_check_qi_blocks_premium_requirements' );
}
