<?php
namespace WPSMTP\Logger;

class Table {

	public static $name;

	public static function install() {

		global $wpdb;

		self::$name = $wpdb->prefix . 'wpsmtp_logs';

		$sql = 'CREATE TABLE IF NOT EXISTS `' . self::$name . "` (
				`mail_id` INT NOT NULL AUTO_INCREMENT,
				`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`to` VARCHAR(200) NOT NULL DEFAULT '0',
				`subject` VARCHAR(200) NOT NULL DEFAULT '0',
				`message` TEXT NULL,
				`headers` TEXT NULL,
				`error` TEXT NULL,
				PRIMARY KEY (`mail_id`)
			) DEFAULT CHARACTER SET = utf8 DEFAULT COLLATE utf8_general_ci;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
}
