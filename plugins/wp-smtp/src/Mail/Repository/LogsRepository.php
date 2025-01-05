<?php
/**
 * Repository class for handling operations on the wp_wpsmtp_logs table.
 *
 * @package Solid_SMTP\Repository
 */

namespace SolidWP\Mail\Repository;

use SolidWP\Mail\App;

/**
 * Class LogsRepository
 *
 * Handles operations related to the wp_wpsmtp_logs table, including
 * retrieving email logs and searching the logs by term.
 */
class LogsRepository {
	/**
	 * Name of the table to interact with.
	 *
	 * @var string
	 */
	private string $table;

	/**
	 * LogsRepository constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->table = $wpdb->prefix . 'wpsmtp_logs';
	}

	/**
	 * Retrieves email logs from the database.
	 *
	 * This function fetches email logs based on the provided pagination, search term,
	 * and sorting options. It sanitizes and validates the input parameters and constructs
	 * the SQL query accordingly.
	 *
	 * @param array $params {
	 *     Array of parameters for fetching email logs.
	 *
	 * @type int $page The page number for pagination (0-based index).
	 * @type string $search_term The term to search for in email subjects, messages, and recipients.
	 * @type string $orderby The column to order the results by. Default is 'timestamp'.
	 * @type string $order The order direction ('ASC' or 'DESC'). Default is 'desc'.
	 * @type int $per_page The number of items per page.
	 * }
	 *
	 * @return array The array of email logs.
	 */
	public function get_email_logs( array $params = [] ): array {
		$page        = isset( $params['page'] ) ? max( $params['page'], 1 ) : 1;
		$search_term = isset( $params['search_term'] ) ? trim( $params['search_term'] ) : '';
		$orderby     = $params['orderby'] ?? 'timestamp';
		$order       = $params['order'] ?? 'desc';
		$per_page    = $params['per_page'] ?? App::get( 'LOGS_PER_PAGE' );

		$offset = ( $page - 1 ) * $per_page;
		global $wpdb;

		$valid_orderby = in_array( $orderby, [ 'timestamp', 'to', 'subject' ], true ) ? $orderby : 'timestamp';
		$valid_order   = in_array( strtoupper( $order ), [ 'ASC', 'DESC' ], true ) ? strtoupper( $order ) : 'DESC';

		$search_sql  = '';
		$search_term = trim( $search_term );

		if ( ! empty( $search_term ) ) {
			$search_term = '%' . $wpdb->esc_like( $search_term ) . '%';
			$search_sql  = $wpdb->prepare(
				'WHERE `subject` LIKE %s OR `message` LIKE %s OR `to` LIKE %s',
				$search_term,
				$search_term,
				$search_term
			);
		}

		$data = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$this->table} {$search_sql} ORDER BY {$valid_orderby} {$valid_order} LIMIT %d OFFSET %d",
				$per_page,
				$offset
			),
			ARRAY_A
		);
		// sometimes we need to serialize the array.
		foreach ( $data as &$val ) {
			$val['to'] = maybe_unserialize( $val['to'] );
			$val['to'] = is_array( $val['to'] ) ? implode( ', ', $val['to'] ) : $val['to'];
		}

		return $data;
	}

	/**
	 * Retrieves email logs within a specified date range.
	 *
	 * @param int $from Unix timestamp representing the start of the date range.
	 * @param int $to   Unix timestamp representing the end of the date range.
	 *
	 * @return array Array of email logs, each log being an associative array containing
	 *               the log details. Returns an empty array if the input parameters are invalid.
	 */
	public function get_email_logs_by_date( int $from, int $to ): array {
		if ( $from <= 0 || $to <= 0 || $from > $to ) {
			// should not come to this.
			return [];
		}

		global $wpdb;

		$data = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$this->table} WHERE `timestamp` BETWEEN %s AND %s ORDER BY `timestamp` ASC",
				wp_date('Y-m-d H:i:s', $from),
				wp_date('Y-m-d H:i:s', $to)
			),
			ARRAY_A
		);

		// Process each result to format the 'to' field properly.
		foreach ( $data as &$val ) {
			$val['to'] = maybe_unserialize( $val['to'] );
			$val['to'] = is_array( $val['to'] ) ? implode( ', ', $val['to'] ) : $val['to'];
		}

		return $data;
	}

	/**
	 * Deletes a specific email log by ID.
	 *
	 * @param int $log_id ID of the log to delete.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function delete_log( int $log_id ): bool {
		global $wpdb;

		return (bool) $wpdb->delete( $this->table, [ 'mail_id' => $log_id ], [ '%d' ] );
	}

	/**
	 * Deletes multiple logs from the database.
	 *
	 * This function performs a mass delete of logs whose IDs are specified
	 * in the input array. It constructs a single SQL query using the `IN` clause
	 * to delete all specified logs in one go, which is more efficient than deleting
	 * each log individually.
	 *
	 * @param array $ids Array of log IDs to be deleted.
	 *
	 * @return bool True if the logs were successfully deleted, false otherwise.
	 */
	public function delete_logs( array $ids ): bool {
		global $wpdb;

		if ( empty( $ids ) ) {
			return false;
		}

		$placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
		$results      = $wpdb->query(
			$wpdb->prepare( "DELETE FROM {$this->table} WHERE mail_id IN ($placeholders)", $ids )
		);

		return $results !== false;
	}


	/**
	 * Counts all email log records.
	 *
	 * @param string $search_term Optional. The search term used to filter email log records.
	 *
	 * @return int Total number of email log records.
	 */
	public function count_all_logs( string $search_term = '' ): int {
		global $wpdb;

		$search_sql  = '';
		$search_term = trim( $search_term );

		if ( ! empty( $search_term ) ) {
			$search_term = '%' . $wpdb->esc_like( $search_term ) . '%';
			$search_sql  = $wpdb->prepare(
				'WHERE `subject` LIKE %s OR `message` LIKE %s OR `to` LIKE %s',
				$search_term,
				$search_term,
				$search_term
			);
		}

		return absint(
			$wpdb->get_var(
				"SELECT COUNT(*) FROM {$this->table} $search_sql"
			)
		);
	}
}
