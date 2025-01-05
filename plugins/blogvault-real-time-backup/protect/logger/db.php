<?php
if (!defined('ABSPATH') && !defined('MCDATAPATH')) exit;

if (!class_exists('BVProtectLoggerDB_V588')) :
class BVProtectLoggerDB_V588 {
	private $tablename;
	private $bv_tablename;

	const MAXROWCOUNT = 100000;

	function __construct($tablename) {
		$this->tablename = $tablename;
		$this->bv_tablename = BVProtect_V588::$db->getBVTable($tablename);
	}

	public function log($data) {
		if (is_array($data)) {
			if (BVProtect_V588::$db->rowsCount($this->bv_tablename) > BVProtectLoggerDB_V588::MAXROWCOUNT) {
				BVProtect_V588::$db->deleteRowsFromtable($this->tablename, 1);
			}

			BVProtect_V588::$db->replaceIntoBVTable($this->tablename, $data);
		}
	}
}
endif;