<?php

namespace WPSMTP\Logger;

class Db {

	private $db;

	private $table;

	private static $instance;

	public static function get_instance() {

		if ( ! self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	private function __construct() {
		global $wpdb;

		$this->db    = $wpdb;
		$this->table = $wpdb->prefix . 'wpsmtp_logs';
	}

	public function insert( $data ) {

		array_walk(
			$data,
			static function ( &$value ) {
				if ( is_array( $value ) ) {
					$value = maybe_serialize( $value );
				}
			}
		);

		$result_set = $this->db->insert(
			$this->table,
			$data,
			array_fill( 0, count( $data ), '%s' )
		);

		if ( ! $result_set ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'WP SMTP Log insert error: ' . $this->db->last_error );
			// @TODO: do we have a better way to log errors?

			return false;
		}

		return $this->db->insert_id;
	}

	public function update( $data, $where = [] ): void {
		array_walk(
			$data,
			static function ( &$value ) {
				if ( is_array( $value ) ) {
					$value = maybe_serialize( $value );
				}
			}
		);

		$this->db->update(
			$this->table,
			$data,
			$where,
			array_fill( 0, count( $data ), '%s' ),
			[ '%d' ]
		);
	}
}
