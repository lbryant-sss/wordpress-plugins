<?php
/**
 * Logs Table.
 *
 * @package     EDD\Database\Tables
 * @copyright   Copyright (c) 2018, Sandhills Development, LLC
 * @license     https://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

namespace EDD\Database\Tables;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

use EDD\Database\Table;

/**
 * Setup the global "edd_logs" database table
 *
 * @since 3.0
 */
final class Logs extends Table {

	/**
	 * Table name.
	 *
	 * @access protected
	 * @since 3.0
	 * @var string
	 */
	protected $name = 'logs';

	/**
	 * Database version.
	 *
	 * @access protected
	 * @since 3.0
	 * @var int
	 */
	protected $version = 202002141;

	/**
	 * Array of upgrade versions and methods
	 *
	 * @since 3.0
	 *
	 * @var array
	 */
	protected $upgrades = array(
		'202002141' => 202002141,
	);

	/**
	 * Setup the database schema.
	 *
	 * @access protected
	 * @since 3.0
	 * @return void
	 */
	protected function set_schema() {
		$this->schema = "id bigint(20) unsigned NOT NULL auto_increment,
		object_id bigint(20) unsigned NOT NULL default '0',
		object_type varchar(20) DEFAULT NULL,
		user_id bigint(20) unsigned NOT NULL default '0',
		type varchar(20) DEFAULT NULL,
		title varchar(200) DEFAULT NULL,
		content longtext DEFAULT NULL,
		date_created datetime NOT NULL default CURRENT_TIMESTAMP,
		date_modified datetime NOT NULL default CURRENT_TIMESTAMP,
		uuid varchar(100) NOT NULL default '',
		PRIMARY KEY (id),
		KEY object_id_type (object_id,object_type(20)),
		KEY user_id (user_id),
		KEY type (type(20)),
		KEY date_created (date_created)";
	}

	/**
	 * Upgrade to version 202002141
	 *  - Change default value to `CURRENT_TIMESTAMP` for columns `date_created` and `date_modified`.
	 *
	 * @since 3.0
	 * @return bool
	 */
	protected function __202002141() {

		// Update `date_created`.
		$result = $this->get_db()->query(
			"
			ALTER TABLE {$this->table_name} MODIFY COLUMN `date_created` datetime NOT NULL default CURRENT_TIMESTAMP;
		"
		);

		// Update `date_modified`.
		$result = $this->get_db()->query(
			"
			ALTER TABLE {$this->table_name} MODIFY COLUMN `date_modified` datetime NOT NULL default CURRENT_TIMESTAMP;
		"
		);

		return $this->is_success( $result );
	}
}
