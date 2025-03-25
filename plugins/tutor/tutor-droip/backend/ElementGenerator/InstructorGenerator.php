<?php
/**
 * Preview script for html markup generator
 *
 * @package tutor-droip-elements
 */

namespace TutorLMSDroip\ElementGenerator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class GradebookGenerator
 * This class is used to define all helper functions.
 */
trait InstructorGenerator {

	/**
	 * Generate instructor markup
	 *
	 * @return string
	 */
	private function generate_instructor_all_markup() {
		switch ( $this->element['name'] ) {
			case TDE_APP_PREFIX . '-instructor-list':
				return $this->generate_instructor_list_markup();

			case TDE_APP_PREFIX . '-instructor-list-item':
				return $this->generate_instructor_list_item_markup();

			case TDE_APP_PREFIX . '-instructor-avatar':
				return $this->generate_instructor_avatar_markup();

			case TDE_APP_PREFIX . '-instructor-name':
				return $this->generate_instructor_name_markup();

		}
	}

	/**
	 * Generate instructor list markup
	 *
	 * @return string
	 */
	private function generate_instructor_list_markup() {
		$course_id = isset( $this->options['post'] ) ? $this->options['post']->ID : get_the_ID();
		$instructors = tutor_utils()->get_instructors_by_course($course_id);

		$children = '';

		if ( is_array( $instructors ) ) {
			foreach ( $instructors as $instructor ) {
				$children .= call_user_func(
					$this->generate_child_element,
					$this->element['children'][0],
					array_merge(
						$this->options,
						array( 'instructor' => $instructor )
					)
				);
			}
		}

		return "<div $this->attributes>$children</div>";
	}

	/**
	 * Generate instructor list item markup
	 *
	 * @return string
	 */
	private function generate_instructor_list_item_markup() {
		$instructor = $this->options['instructor'];

		$instructor_profile_url = tutor_utils()->profile_url( $instructor->ID, true );

		$children = '';

		if ( is_array( $this->element['children'] ) ) {
			foreach ( $this->element['children'] as $child ) {
				$children .= call_user_func(
					$this->generate_child_element,
					$child,
					array_merge(
						$this->options,
						array( 'instructor' => $instructor )
					)
				);
			}
		}

		return "<a $this->attributes href='$instructor_profile_url' >$children</a>";
	}

	/**
	 * Generate instructor name markup
	 *
	 * @return string
	 */
	private function generate_instructor_name_markup() {
		$instructor = $this->options['instructor'];

		return "<div $this->attributes >$instructor->display_name</div>";
	}

	/**
	 * Generate instructor avatar markup
	 *
	 * @return string
	 */
	private function generate_instructor_avatar_markup() {
		$instructor = $this->options['instructor'];

		$profile_photo = get_user_meta( $instructor->ID, '_tutor_profile_photo', true );

		$avatar = '';

		if ( $profile_photo ) {
			$avatar_src = wp_get_attachment_image_url( $instructor->tutor_profile_photo, 'thumbnail' );

			$instructor_name = $instructor->display_name;

			$avatar = "<img src=$avatar_src alt=$instructor_name />";
		} else {
			$arr = explode( ' ', trim( $instructor->display_name ) );

			$first_char = ! empty( $arr[0] ) ? tutor_utils()->str_split( $arr[0] )[0] : '';

			$second_char = ! empty( $arr[1] ) ? tutor_utils()->str_split( $arr[1] )[0] : '';

			$initial_avatar = strtoupper( $first_char . $second_char );

			$avatar = $initial_avatar;
		}

		return "<div $this->attributes >$avatar</div>";
	}
}
