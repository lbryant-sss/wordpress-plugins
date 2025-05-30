<?php
/**
 * Manage Announcements
 *
 * @package Tutor
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 2.0.0
 */

namespace TUTOR;

use Tutor\Helpers\QueryHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Announcements class
 *
 * @since 2.0.0
 */
class Announcements {
	/**
	 * Trait for utilities
	 *
	 * @var $page_title
	 */

	use Backend_Page_Trait;

	/**
	 * Bulk Action
	 *
	 * @var $bulk_action
	 */
	public $bulk_action = true;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {
		/**
		 * Handle bulk action
		 *
		 * @since 2.0.0
		 */
		add_action( 'wp_ajax_tutor_announcement_bulk_action', array( $this, 'announcement_bulk_action' ) );
	}

	/**
	 * Page title fallback
	 *
	 * @since 3.5.0
	 *
	 * @param string $name Property name.
	 *
	 * @return string
	 */
	public function __get( $name ) {
		if ( 'page_title' === $name ) {
			return esc_html__( 'Announcements', 'tutor' );
		}
	}


	/**
	 * Prepare bulk actions that will show on dropdown options
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function prepare_bulk_actions(): array {
		$actions = array(
			$this->bulk_action_default(),
			$this->bulk_action_delete(),
		);
		return $actions;
	}

	/**
	 * Handle bulk action for enrollment cancel | delete
	 *
	 * @since 2.0.0
	 * @return string JSON response.
	 */
	public function announcement_bulk_action() {
		tutor_utils()->checking_nonce();

		// Check if user is privileged.
		if ( ! User::has_any_role( array( User::ADMIN, User::INSTRUCTOR ) ) ) {
			wp_send_json_error( tutor_utils()->error_message() );
		}

		$action   = Input::post( 'bulk-action', '' );
		$bulk_ids = Input::post( 'bulk-ids', '' );

		// prevent instructor to delete admin announcement.
		$bulk_ids = array_filter(
			explode( ',', $bulk_ids ),
			function ( $announcement_id ) {
				return tutor_utils()->can_user_manage( 'announcement', $announcement_id );
			}
		);
		$update   = self::delete_announcements( $action, implode( ',', $bulk_ids ) );
		return true === $update ? wp_send_json_success() : wp_send_json_error();
	}

	/**
	 * Execute bulk action for enrolments ex: complete | cancel
	 *
	 * @since 2.0.0
	 *
	 * @param string $action hold action.
	 * @param string $bulk_ids comma seperated ids.
	 *
	 * @return bool
	 */
	public static function delete_announcements( $action, $bulk_ids ): bool {
		$ids       = array_map( 'intval', explode( ',', $bulk_ids ) );
		$in_clause = QueryHelper::prepare_in_clause( $ids );

		if ( 'delete' === $action ) {
			global $wpdb;
			$post_table = $wpdb->posts;
			$delete     = $wpdb->query(
				$wpdb->prepare(
					"DELETE FROM {$post_table} WHERE ID IN ($in_clause)" //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				)
			);
			return false === $delete ? false : true;
		}
		return false;
	}
}
