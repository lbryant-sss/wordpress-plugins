<?php
/**
 * Smart Popup Ajax Class.
 *
 * @package RT_Team
 */

namespace RT\Team\Controllers\Frontend\Ajax;

use RT\Team\Helpers\Fns;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Smart Popup Ajax Class.
 */
class SmartPopup {
	use \RT\Team\Traits\SingletonTrait;

	/**
	 * Class Init.
	 *
	 * @return void
	 */
	protected function init() {
		add_action( 'wp_ajax_tlp_team_smart_popup', [ $this, 'response' ] );
		add_action( 'wp_ajax_nopriv_tlp_team_smart_popup', [ $this, 'response' ] );
	}

	/**
	 * Ajax Response.
	 *
	 * @return void
	 */
	public function response() {
		$html    = $htmlCInfo = null;
		$success = false;
		$error   = true;
		if ( ! wp_verify_nonce( Fns::getNonce(), Fns::nonceText() ) ) {
			wp_send_json_error( [
				'data'  => __('Security Issue','tlp-team'),
				'error' => $error,
			] );
		}
		if ( ! empty( $_REQUEST['id'] ) ) {
			global $post;
			$post = get_post( absint( $_REQUEST['id'] ) );
			setup_postdata( $post );
            $settings     = get_option( rttlp_team()->options['settings'] );
            $resume_btn_text = isset( $settings['resume_btn_text'] ) ? $settings['resume_btn_text'] : "Resume";
            $hire_btn_text = isset( $settings['hire_me_text'] ) ? $settings['hire_me_text'] : "Hire Me";
            $resume_url      = get_post_meta( $post->ID, 'ttp_my_resume', true );
            $hire_me_url     = get_post_meta( $post->ID, 'ttp_hire_me', true );

			$fields                   = isset( $settings['detail_page_fields'] ) ? $settings['detail_page_fields'] : [];
			$sLink                    = get_post_meta(
				$post->ID,
				'social',
				true
			);
			$tlp_skill                = unserialize( get_post_meta( $post->ID, 'skill', true ) );
			$name                     = $post->post_title;
			$tlp_member_content       = wpautop( get_the_content() );
			$email                    = get_post_meta( $post->ID, 'email', true );
			$web_url                  = get_post_meta( $post->ID, 'web_url', true );
			$telephone                = get_post_meta( $post->ID, 'telephone', true );
			$mobile                   = get_post_meta( $post->ID, 'mobile', true );
			$fax                      = get_post_meta( $post->ID, 'fax', true );
			$location                 = get_post_meta( $post->ID, 'location', true );
			$experience_year          = get_post_meta( $post->ID, 'experience_year', true );
			$short_bio                = get_post_meta( $post->ID, 'short_bio', true );
			$designation              = wp_strip_all_tags(
				get_the_term_list(
					$post->ID,
					rttlp_team()->taxonomies['designation'],
					null,
					', '
				)
			);
			$tag_line                 = get_post_meta( $post->ID, 'ttp_tag_line', true );
			$qualifications           = get_post_meta( $post->ID, 'ttp_qualifications', true );
			$professional_memberships = get_post_meta( $post->ID, 'ttp_professional_memberships', true );
			$area_of_expertise        = get_post_meta( $post->ID, 'ttp_area_of_expertise', true );

			if ( in_array( 'name', $fields ) ) {
				$html .= "<h3 class='member-name'>{$name}</h3>";
			}

			$html .= Fns::get_formatted_designation( $designation, $fields, $experience_year );

			if ( $tag_line && in_array( 'ttp_tag_line', $fields ) ) {
				$html .= "<div class='tlp-tag-line'>{$tag_line}</div>";
			}

			if ( $tlp_member_content && in_array( 'content', $fields ) ) {
				$html .= '<div class="tlp-member-detail">' . wp_kses_post( $tlp_member_content ) . '</div>';
			}

			if ( $qualifications && in_array( 'ttp_qualifications', $fields ) ) {
				$html .= "<div class='rt-extra-curriculum'><strong>" . esc_html__(
					'Qualifications : ',
					'tlp-team'
				) . "</strong>{$qualifications}</div>";
			}

			if ( $professional_memberships && in_array( 'ttp_professional_memberships', $fields ) ) {
				$html .= "<div class='rt-extra-curriculum'><strong>" . esc_html__(
					'Professional Memberships : ',
					'tlp-team'
				) . "</strong>{$professional_memberships}</div>";
			}

			if ( $area_of_expertise && in_array( 'ttp_area_of_expertise', $fields ) ) {
				$html .= "<div class='rt-extra-curriculum'><strong>" . esc_html__(
					'Area of Expertise : ',
					'tlp-team'
				) . "</strong>{$area_of_expertise}</div>";
			}

			$html .= Fns::get_formatted_short_bio( $short_bio, $fields );
			$html .= Fns::get_formatted_contact_info(
				[
					'email'     => $email,
					'telephone' => $telephone,
					'mobile'    => $mobile,
					'fax'       => $fax,
					'location'  => $location,
					'web_url'   => $web_url,
				],
				$fields
			);

			$html .= Fns::get_formatted_skill( $tlp_skill, $fields );
			$html .= Fns::get_formatted_social_link( $sLink, $fields );
            $resume  = $resume_url && in_array( 'resume_btn', $fields );
            $hire_me = $hire_me_url && in_array( 'hire_me_btn', $fields );
            if( ( $resume && $resume_btn_text ) || ( $hire_me && $hire_btn_text ) ) {
                $html .= '<div class="rt-team-container ptl-padding-0">';
                $html .= '<div class="readmore-btn">';
                if( $resume && $resume_btn_text ){
                    $html .= '<a class="rt-resume-btn" data-id="480" target="_self" title="'. esc_attr( $resume_btn_text ) .'" href="'. esc_url( $resume_url ) .'" class="rt-resume-btn">'. esc_html( $resume_btn_text ) .'</a>';
                }
                if( $hire_me && $hire_btn_text ){
                    $html .= '<a class="rt-hire-btn" data-id="480" target="_self" title="'. esc_attr( $hire_btn_text ) .'" href="'. esc_url( $hire_me_url ) .'" class="rt-resume-btn">'. esc_html( $hire_btn_text ) .'</a>';
                }
                $html .= '</div>';
                $html .= '</div>';
            }

			if ( in_array( 'author_post', $fields ) ) {
				$html .= Fns::memberDetailPosts( $post->ID );
			}

			$html    = sprintf(
				"<div class='rt-smart-modal-main-content'>
					<div class='rt-team-container'>
						<div class='team-images'>%s</div>
						<div class='member-details'>%s</div>
					</div>
				</div>",
				Fns::memberDetailGallery( $post->ID ),
				$html
			);
			$success = true;
			wp_reset_postdata();
		} else {
			$html .= '<p>' . esc_html__( 'No item id found', 'tlp-team' ) . '</p>';
		}

		wp_send_json(
			[
				'data'    => wp_kses_post( $html ),
				'error'   => $error,
				'success' => $success,
			]
		);
	}
}
