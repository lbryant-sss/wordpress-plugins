<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings = $attributes;

$is_editor = ( isset( $_GET['is_editor_mode'] ) && $_GET['is_editor_mode'] == 'yes' ) ? true : false;

$uniqClass 	 = 'woolentorblock-'.$settings['blockUniqId'];
$areaClasses = array( $uniqClass, 'woolentor_block_wishsuite_counter' );
!empty( $settings['align'] ) ? $areaClasses[] = 'align'.$settings['align'] : '';
!empty( $settings['className'] ) ? $areaClasses[] = esc_attr( $settings['className'] ) : '';


echo '<div class="'.esc_attr( implode(' ', $areaClasses ) ).'">';

    $short_code_attributes = [
        'text' => isset($settings['counterAfterText']) ? $settings['counterAfterText'] : '',
    ];
    echo woolentor_do_shortcode( 'wishsuite_counter', $short_code_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

echo '</div>';