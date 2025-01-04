<?php
/**
 * Template: Isotope Layout 1.
 *
 * @package RT_Team
 */

use RT\Team\Helpers\Fns;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

$html         = null;
$isoFilter    = isset( $isoFilter ) ? $isoFilter : '';
$wrapperClass = $grid . ' ' . $class . ' ' . $isoFilter;

$html .= '<div class="team-member ' . esc_attr( $wrapperClass ) . '" data-id="' . absint( $mID ) . '">';
$html .= '<figure>';

if ( $imgHtml ) {
	if ( $link ) {
		$html .= '<a class="' . esc_attr( $anchorClass ) . '" data-id="' . absint( $mID ) . '" target="' . esc_attr( $target ) . '" href="' . esc_url( $pLink ) . '">' . Fns::htmlKses( $imgHtml, 'image' ) . '</a>';
	} else {
		$html .= Fns::htmlKses( $imgHtml, 'image' );
	}
}

$html .= '<div class="overlay">';
$html .= '<div class="overlay-element">';

if ( in_array( 'name', $items, true ) && $title ) {
	if ( $link ) {
		$html .= '<h3><span class="team-name"><a class="' . esc_attr( $anchorClass ) . '" data-id="' . absint( $mID ) . '" target="' . esc_attr( $target ) . '" title="' . esc_attr( $title ) . '" href="' . esc_url( $pLink ) . '">' . esc_html( $title ) . '</a></span></h3>';
	} else {
		$html .= '<h3><span class="team-name">' . esc_html( $title ) . '</span></h3>';
	}
}

if ( in_array( 'designation', $items, true ) && $designation ) {
	if ( $link ) {
		$html .= '<div class="tlp-position"><a class="' . esc_attr( $anchorClass ) . '" data-id="' . absint( $mID ) . '" target="' . esc_attr( $target ) . '" title="' . esc_attr( $title ) . '" href="' . esc_url( $pLink ) . '">' . esc_html( $designation ) . '</a></div>';
	} else {
		$html .= '<div class="tlp-position">' . esc_html( $designation ) . '</div>';
	}
}

$html .= Fns::get_formatted_short_bio( $short_bio, $items );
$html .= Fns::get_formatted_social_link( $sLink, $items );

$read_more_btn = isset( $read_more_btn_text ) ? Fns::get_formatted_readmore_text($items, $read_more_btn_text, $anchorClass, $mID, $target, $title, $pLink) : null;
$resume_btn = isset( $ttp_my_resume ) ? Fns::get_formatted_resume( $items, $ttp_my_resume, $my_resume_text ) : null;
$hire_me_btn = isset( $ttp_hire_me ) ? Fns::get_formatted_hire_me( $items, $ttp_hire_me, $hire_me_text ) : null;
if ( $read_more_btn || $resume_btn || $hire_me_btn ) {
    $html .= '<div class="readmore-btn">';
    if( $resume_btn ){
        $html .= $resume_btn;
    }
    if( $hire_me_btn ){
        $html .= $hire_me_btn;
    }
    $html .= '</div>';
    $html .= '<div class="readmore-btn hirme-resume">';
    if( $read_more_btn ){
        $html .= $read_more_btn;
    }
    $html .= '</div>';
}

$html .= '</div>';
$html .= '</div>';
$html .= '</figure>';
$html .= '</div>';

Fns::print_html( $html );
