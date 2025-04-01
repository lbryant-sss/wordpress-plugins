<?php
if (!defined('ABSPATH') && !defined('MCDATAPATH')) exit;

if (!class_exists('BVProtectLoggerDB_V593')) :
class BVProtectLoggerDB_V593 {
	private $tablename;
	private $bv_tablename;

	const MAXROWCOUNT = 100000;

	function __construct($tablename) {
		$this->tablename = $tablename;
		$this->bv_tablename = BVProtect_V593::$db->getBVTable($tablename);
	}

	public function log($data) {
		if (is_array($data)) {
			if (BVProtect_V593::$db->rowsCount($this->bv_tablename) > BVProtectLoggerDB_V593::MAXROWCOUNT) {
				BVProtect_V593::$db->deleteRowsFromtable($this->tablename, 1);
			}

			BVProtect_V593::$db->replaceIntoBVTable($this->tablename, $data);
		}
	}
}
endif;