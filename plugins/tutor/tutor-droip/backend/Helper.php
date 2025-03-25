<?php
/**
 * Preview script for html markup generator
 *
 * @package tutor-droip-elements
 */

namespace TutorLMSDroip;

use Droip\Ajax\ExportImport;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Helper
 * This class is used to define all helper functions.
 */
class Helper {

	/**
	 * Function to activate droip elements plugin
	 */
	public static function t_d_e_activate() {
		self::get_course_template_posts();
	}

	/**
	 * This function will verify nonce
	 * ACT like API calls auth middleware
	 *
	 * @param string $action ajax action name.
	 *
	 * @return void
	 */
	public static function verify_nonce( $action = -1 ) {
		$nonce = sanitize_text_field( isset( $_SERVER['HTTP_X_WP_NONCE'] ) ? wp_unslash( $_SERVER['HTTP_X_WP_NONCE'] ) : null );
		if ( ! wp_verify_nonce( $nonce, $action ) ) {
			wp_send_json_error( 'Not authorized' );
			exit;
		}
	}

	/**
	 * Get course template post
	 *
	 * @return mixed
	 */
	public static function get_course_template_posts() {
		$all_templates = get_posts(
			array(
				'post_type'      => 'droip_template',
				'posts_per_page' => -1,
				'post_status'    => ['draft', 'publish'],
			)
		);
		$data=[];
		foreach ($all_templates as $key => $template) {
			$conditions = get_post_meta($template->ID, 'droip_template_conditions', true);
			if($conditions){
				foreach ($conditions as $key2 => $condition) {
					if($condition['category'] === 'courses'){
						$data[] = $template;
					}
				}
			}
		}

		if(count($data) === 0){
			//create a course template
			$post_id = wp_insert_post(
				array(
					'post_title' => 'Course Details',
					'post_name'  => 'Course Details',
					'post_type'  => 'droip_template'
				)
			);
			$conditions = array(
				array(
					'category'=>'courses',
					'taxonomy' => '*', 
					'visibility' => 'show'
				)
			);
			update_post_meta( $post_id, 'droip_template_conditions', $conditions );
			
			if (class_exists('Droip\Ajax\ExportImport')) {
				$template_path = TDE_ROOT_PATH . '/assets/course-details.zip';
				ExportImport::process_droip_template_zip($template_path, false, $post_id);
			}
			
			$data[] = get_post($post_id);
		}
		return $data;
	}
}
