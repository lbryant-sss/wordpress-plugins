<?php
if (!defined('ABSPATH') && !defined('MCDATAPATH')) exit;

if (!class_exists('BVProtectIpstoreDB_V602')) :
class BVProtectIpstoreDB_V602 {
		const TABLE_NAME = 'ip_store';

		const CATEGORY_FW = 3;
		const CATEGORY_LP = 4;

		#XNOTE: check this. 
		public static function blacklistedTypes() {
			return BVProtectRequest_V602::blacklistedCategories();
		}

		public static function whitelistedTypes() {
			return BVProtectRequest_V602::whitelistedCategories();
		}

		public static function uninstall() {
			BVProtect_V602::$db->dropBVTable(BVProtectIpstoreDB_V602::TABLE_NAME);
		}

		public function isLPIPBlacklisted($ip) {
			return $this->checkIPPresent($ip, self::blacklistedTypes(), BVProtectIpstoreDB_V602::CATEGORY_LP);
		}

		public function isLPIPWhitelisted($ip) {
			return $this->checkIPPresent($ip, self::whitelistedTypes(), BVProtectIpstoreDB_V602::CATEGORY_LP);
		}

		public function getTypeIfBlacklistedIP($ip) {
			return $this->getIPType($ip, self::blacklistedTypes(), BVProtectIpstoreDB_V602::CATEGORY_FW);
		}

		public function isFWIPBlacklisted($ip) {
			return $this->checkIPPresent($ip, self::blacklistedTypes(), BVProtectIpstoreDB_V602::CATEGORY_FW);
		}

		public function isFWIPWhitelisted($ip) {
			return $this->checkIPPresent($ip, self::whitelistedTypes(), BVProtectIpstoreDB_V602::CATEGORY_FW);
		}

		private function checkIPPresent($ip, $types, $category) {
			$ip_category = $this->getIPType($ip, $types, $category);

			return isset($ip_category) ? true : false;
		}

		#XNOTE: getIPCategory or getIPType?
		private function getIPType($ip, $types, $category) {
			$table = BVProtect_V602::$db->getBVTable(BVProtectIpstoreDB_V602::TABLE_NAME);

			if (BVProtect_V602::$db->isTablePresent($table)) {
				$binIP = BVProtectUtils_V602::bvInetPton($ip);
				$is_v6 = BVProtectUtils_V602::isIPv6($ip);

				if ($binIP !== false) {
					$query_str = "SELECT * FROM $table WHERE %s >= `start_ip_range` && %s <= `end_ip_range` && ";
					if ($category == BVProtectIpstoreDB_V602::CATEGORY_FW) {
						$query_str .= "`is_fw` = true";
					} else {
						$query_str .= "`is_lp` = true";
					}
					$query_str .= " && `type` in (" . implode(',', $types) . ") && `is_v6` = %d LIMIT 1;";

					$query = BVProtect_V602::$db->prepare($query_str, array($binIP, $binIP, $is_v6));

					return BVProtect_V602::$db->getVar($query, 5);
				}
			}
		}
	}
endif;