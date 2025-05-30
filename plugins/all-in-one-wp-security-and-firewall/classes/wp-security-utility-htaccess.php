<?php
if (!defined('ABSPATH')) {
	exit;//Exit if accessed directly
}

class AIOWPSecurity_Utility_Htaccess {

	// The following variables will store the comment markers for each of features added to the .htacces file
	// This will make it easy to locate the blocks of code for deletion if someone disables a feature
	public static $ip_blacklist_marker_start = '#AIOWPS_IP_BLACKLIST_START';

	public static $ip_blacklist_marker_end = '#AIOWPS_IP_BLACKLIST_END';

	public static $prevent_wp_file_access_marker_start = '#AIOWPS_BLOCK_WP_FILE_ACCESS_START';

	public static $prevent_wp_file_access_marker_end = '#AIOWPS_BLOCK_WP_FILE_ACCESS_END';

	public static $basic_htaccess_rules_marker_start = '#AIOWPS_BASIC_HTACCESS_RULES_START';

	public static $basic_htaccess_rules_marker_end = '#AIOWPS_BASIC_HTACCESS_RULES_END';

	public static $debug_log_block_htaccess_rules_marker_start = '#AIOWPS_DEBUG_LOG_BLOCK_HTACCESS_RULES_START';

	public static $debug_log_block_htaccess_rules_marker_end = '#AIOWPS_DEBUG_LOG_BLOCK_HTACCESS_RULES_END';

	public static $user_agent_blacklist_marker_start = '#AIOWPS_USER_AGENT_BLACKLIST_START';

	public static $user_agent_blacklist_marker_end = '#AIOWPS_USER_AGENT_BLACKLIST_END';

	public static $enable_brute_force_attack_prevention_marker_start = '#AIOWPS_ENABLE_BRUTE_FORCE_PREVENTION_START';

	public static $enable_brute_force_attack_prevention_marker_end = '#AIOWPS_ENABLE_BRUTE_FORCE_PREVENTION_END';

	public static $disable_index_views_marker_start = '#AIOWPS_DISABLE_INDEX_VIEWS_START';

	public static $disable_index_views_marker_end = '#AIOWPS_DISABLE_INDEX_VIEWS_END';

	public static $disable_trace_track_marker_start = '#AIOWPS_DISABLE_TRACE_TRACK_START';

	public static $disable_trace_track_marker_end = '#AIOWPS_DISABLE_TRACE_TRACK_END';

	public static $five_g_blacklist_marker_start = '#AIOWPS_FIVE_G_BLACKLIST_START';

	public static $five_g_blacklist_marker_end = '#AIOWPS_FIVE_G_BLACKLIST_END';

	public static $six_g_blacklist_marker_start = '#AIOWPS_SIX_G_BLACKLIST_START';

	public static $six_g_blacklist_marker_end = '#AIOWPS_SIX_G_BLACKLIST_END';

	public static $block_spambots_marker_start = '#AIOWPS_BLOCK_SPAMBOTS_START';

	public static $block_spambots_marker_end = '#AIOWPS_BLOCK_SPAMBOTS_END';

	public static $enable_login_whitelist_marker_start = '#AIOWPS_LOGIN_WHITELIST_START';

	public static $enable_login_whitelist_marker_end = '#AIOWPS_LOGIN_WHITELIST_END';

	public static $prevent_image_hotlinks_marker_start = '#AIOWPS_PREVENT_IMAGE_HOTLINKS_START';

	public static $prevent_image_hotlinks_marker_end = '#AIOWPS_PREVENT_IMAGE_HOTLINKS_END';

	public static $custom_rules_marker_start = '#AIOWPS_CUSTOM_RULES_START';

	public static $custom_rules_marker_end = '#AIOWPS_CUSTOM_RULES_END';

	/**
	 * TODO - enter more markers as new .htaccess features are added
	 */
	public function __construct() {
		//NOP
	}

	/**
	 * This function is used to scan the htaccess file for valid markers
	 *
	 * @param string $htaccess - The htaccess file to scan
	 * @return boolean
	 */
	public static function htaccess_has_valid_markers($htaccess) {

		$markers = array(
			'# BEGIN WordPress',
			'# END WordPress',
			'# BEGIN All In One WP Security',
			'# END All In One WP Security',
			self::$ip_blacklist_marker_start,
			self::$ip_blacklist_marker_end,
			self::$prevent_wp_file_access_marker_start,
			self::$prevent_wp_file_access_marker_end,
			self::$basic_htaccess_rules_marker_start,
			self::$basic_htaccess_rules_marker_end,
			self::$debug_log_block_htaccess_rules_marker_start,
			self::$debug_log_block_htaccess_rules_marker_end,
			self::$user_agent_blacklist_marker_start,
			self::$user_agent_blacklist_marker_end,
			self::$enable_brute_force_attack_prevention_marker_start,
			self::$enable_brute_force_attack_prevention_marker_end,
			self::$disable_index_views_marker_start,
			self::$disable_index_views_marker_end,
			self::$disable_trace_track_marker_start,
			self::$disable_trace_track_marker_end,
			self::$five_g_blacklist_marker_start,
			self::$five_g_blacklist_marker_end,
			self::$six_g_blacklist_marker_start,
			self::$six_g_blacklist_marker_end,
			self::$block_spambots_marker_start,
			self::$block_spambots_marker_end,
			self::$enable_login_whitelist_marker_start,
			self::$enable_login_whitelist_marker_end,
			self::$prevent_image_hotlinks_marker_start,
			self::$prevent_image_hotlinks_marker_end,
			self::$custom_rules_marker_start,
			self::$custom_rules_marker_end,
		);
		
		$markerCounts = array();
		$htaccessContent = file_get_contents($htaccess);

		foreach ($markers as $marker) {
			$count = preg_match_all('/^' . preg_quote($marker, '/') . '$/m', $htaccessContent);
			$markerCounts[$marker] = $count;
		}

		foreach ($markerCounts as $count) {
			if ($count > 1) return false;
		}

		return true;
	}

	/**
	 * This function checks if .htaccess file exists and is readable
	 *
	 * @param string $htaccess - The path to .htaccess file
	 * @return boolean
	 */
	public static function htaccess_exist_and_readable($htaccess) {
		global $aio_wp_security;

		if (!file_exists($htaccess)) {
			$aio_wp_security->debug_logger->log_debug("The .htaccess file is missing", 4);
			return false;
		} elseif (!is_readable($htaccess)) {
			$aio_wp_security->debug_logger->log_debug("The .htaccess file exists, but it is not readable.", 4);
			return false;
		}
		return true;
	}

	/**
	 * Write all active rules to .htaccess file.
	 *
	 * @param boolean $show_error - if the error should be shown
	 *
	 * @return boolean True on success, false on failure.
	 */
	public static function write_to_htaccess($show_error = true) {
		global $aio_wp_security;

		if (!class_exists('AIOWPSecurity_Admin_Menu')) {
			include_once AIO_WP_SECURITY_PATH . '/admin/wp-security-admin-menu.php';
		}

		// figure out what server is being used
		$serverType = AIOWPSecurity_Utility::get_server_type();
		$error_msg = __('An error has occurred while writing to the .htaccess file.', 'all-in-one-wp-security');

		if (in_array($serverType, array('-1', 'nginx', 'iis')) && !defined('WP_CLI')) {
			AIOWPSecurity_Admin_Menu::show_msg_error_st($error_msg . ' ' . __('The .htaccess file is not supported by your web server.', 'all-in-one-wp-security'), !$show_error);
			$aio_wp_security->debug_logger->log_debug("Unable to write to .htaccess - server type not supported.", 4);
			return false; // unable to write to the file
		}

		$home_path = AIOWPSecurity_Utility_File::get_home_path();
		$htaccess = $home_path . '.htaccess';
		if (!self::htaccess_exist_and_readable($htaccess)) {
			AIOWPSecurity_Admin_Menu::show_msg_error_st($error_msg . ' ' . __('The .htaccess file either does not exist or is unreadable', 'all-in-one-wp-security'), !$show_error);
			$aio_wp_security->debug_logger->log_debug("The .htaccess file either does not exist or is unreadable", 4);
			return false;
		} // check the existence of the file and if its readable

		// confirm the hataccess has valid markers
		if (!self::htaccess_has_valid_markers($htaccess)) {
			AIOWPSecurity_Admin_Menu::show_msg_error_st($error_msg . ' ' . __('The .htaccess file contains invalid content, please manually verify the file contents', 'all-in-one-wp-security'), !$show_error);
			$aio_wp_security->debug_logger->log_debug("Unable to edit the .htaccess file as it contains invalid content, please manually verify the file contents", 4);
			return false;
		}

		AIOWPSecurity_Utility_File::backup_and_rename_htaccess($htaccess);

		// creating a copy of htaccess file to work on
		$temp_htaccess = $home_path.'.htaccess_temp';
		if (!copy($htaccess, $temp_htaccess)) {
			AIOWPSecurity_Admin_Menu::show_msg_error_st($error_msg . ' ' . __('A copy of the file could not be created', 'all-in-one-wp-security'), !$show_error);
			$aio_wp_security->debug_logger->log_debug("Write operation on .htaccess file failed, unable to create a copy of the file", 4);
			return false;
		}

		// clean up old rules first
		if (-1 == AIOWPSecurity_Utility_Htaccess::delete_from_htaccess($temp_htaccess)) {
			AIOWPSecurity_Admin_Menu::show_msg_error_st($error_msg . __("Unable to delete plugin's content from .htaccess file.", 'all-in-one-wp-security'), !$show_error);
			$aio_wp_security->debug_logger->log_debug("Unable to delete plugin's content from .htaccess file.", 4);
			return false; //unable to write to the file
		}

		
		$ht = explode("\n", implode('', file($temp_htaccess))); // parse each line of file into array

		$rules = AIOWPSecurity_Utility_Htaccess::getrules();

		$rulesarray = explode("\n", $rules);
		$rulesarray = apply_filters('aiowps_htaccess_rules_before_writing', $rulesarray);
		$contents = array_merge($rulesarray, $ht);

		$f = @fopen($temp_htaccess, 'w+'); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- ignore warning as we try to handle it below
		if (!$f) {
			AIOWPSecurity_Admin_Menu::show_msg_error_st($error_msg, !$show_error);
			$aio_wp_security->debug_logger->log_debug("Write operation on .htaccess failed.", 4);
			return false; //we can't write to the file
		}

		$blank = false;

		// write each line to file
		foreach ($contents as $insertline) {
			if (trim($insertline) == '') {
				if (false == $blank) {
					fwrite($f, "\n" . trim($insertline));
				}
				$blank = true;
			} else {
				$blank = false;
				fwrite($f, "\n" . trim($insertline));
			}
		}
		if (is_resource($f)) @fclose($f);

		// before writing into the live htaccess confirm the markers still valid
		if (!self::htaccess_has_valid_markers($temp_htaccess)) {
			AIOWPSecurity_Admin_Menu::show_msg_error_st($error_msg .__('The .htaccess file has invalid content, please manually verify that the file is properly formatted', 'all-in-one-wp-security'), !$show_error);
			$aio_wp_security->debug_logger->log_debug("The .htaccess file has invalid content, please manually verify that the file is properly formatted", 4);
			return false;
		}
		
		// copy the changes from the temp htaccess into the live htaccess from here
		if (!copy($temp_htaccess, $htaccess)) {
			AIOWPSecurity_Admin_Menu::show_msg_error_st($error_msg, !$show_error);
			$aio_wp_security->debug_logger->log_debug("Failed to write to the .htaccess file", 4);
			return false;
		}
		// Remove the temp htaccess file created
		unlink($temp_htaccess);

		return true; //success
	}

	/**
	 * This function will delete the code which has been added to the .htaccess file by this plugin
	 * It will try to find the comment markers "# BEGIN All In One WP Security" and "# END All In One WP Security" and delete contents in between
	 *
	 * @param string $htaccess - The htaccess file path to manipulate
	 * @param string $section  - All in One Security
	 * @return Integer {-1,1} -1 for failure, 1 for success.
	 */
	public static function delete_from_htaccess($htaccess = '', $section = 'All In One WP Security') {

		if (empty($htaccess)) {
			$home_path = AIOWPSecurity_Utility_File::get_home_path();
			$htaccess = $home_path . '.htaccess';
		}

		if (!file_exists($htaccess)) {
			$ht = @fopen($htaccess, 'a+');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- ignore warning as we try to handle it below
			if (false === $ht) {
				global $aio_wp_security;
				$aio_wp_security->debug_logger->log_debug('Failed to create .htaccess file', 4);
				return -1;
			}
			if (is_resource($ht)) @fclose($ht);
		}

		// Bug Fix: On some environments such as windows (xampp) this function was clobbering the non-aiowps-related .htaccess contents for certain cases.
		// In some cases when WordPress saves the .htaccess file (eg, when saving permalink settings),
		// the line endings differ from the expected "\n" endings. (WordPress saves with "\n" (UNIX style) but "\n" may be set as "\r\n" (WIN/DOS))
		// In this case exploding via "\n" may not yield the result we expect.
		// Therefore we need to do the following extra checks.
		$ht_contents_imploded = implode('', file($htaccess));
		if (empty($ht_contents_imploded)) {
			return 1;
		} elseif (strstr($ht_contents_imploded, "\n")) {
			$ht_contents = explode("\n", $ht_contents_imploded); //parse each line of file into array
		} elseif (strstr($ht_contents_imploded, "\r")) {
			$ht_contents = explode("\r", $ht_contents_imploded); //parse each line of file into array
		} elseif (strstr($ht_contents_imploded, "\r\n")) {
			$ht_contents = explode("\r\n", $ht_contents_imploded); //parse each line of file into array
		}
		
		if ($ht_contents) { //as long as there are lines in the file
			$state = true;
			$f = @fopen($htaccess, 'w+'); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- ignore warning as we try to handle it below
			if (!$f) {
				@chmod($htaccess, 0644);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- ignore warning as we try to handle it below
				$f = @fopen($htaccess, 'w+'); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- ignore warning as we try to handle it below
				if (!$f) return -1;
			}

			foreach ($ht_contents as $markerline) { //for each line in the file
				if (strpos($markerline, '# BEGIN ' . $section) !== false) { //if we're at the beginning of the section
					$state = false;
				}
				if (true == $state) { //as long as we're not in the section keep writing
					fwrite($f, trim($markerline) . "\n");
				}
				if (strpos($markerline, '# END ' . $section) !== false) { //see if we're at the end of the section
					$state = true;
				}
			}
			if (is_resource($f)) @fclose($f);
			return 1;
		}
		return 1;
	}

	public static function getrules() {
		global $aio_wp_security;
		$rules = "";
		$rules .= AIOWPSecurity_Utility_Htaccess::getrules_basic_htaccess();
		$rules .= AIOWPSecurity_Utility_Htaccess::getrules_block_debug_log_access_htaccess();
		$rules .= AIOWPSecurity_Utility_Htaccess::getrules_disable_index_views();
		$rules .= AIOWPSecurity_Utility_Htaccess::getrules_disable_trace_and_track();
		$rules .= AIOWPSecurity_Utility_Htaccess::getrules_5g_blacklist();
		$rules .= AIOWPSecurity_Utility_Htaccess::prevent_image_hotlinks();
		$custom_rules = AIOWPSecurity_Utility_Htaccess::getrules_custom_rules();
		if ($aio_wp_security->configs->get_value('aiowps_place_custom_rules_at_top')=='1') {
			$rules = $custom_rules . $rules;
		} else {
			$rules .= $custom_rules;
		}
		
		//TODO: The following utility functions are ready to use when we write the menu pages for these features

		//Add more functions for features as needed
		//$rules .= AIOWPSecurity_Utility_Htaccess::getrules_somefeature();

		//Add outer markers if we have rules
		if ('' != $rules) {
			$rules = "# BEGIN All In One WP Security" . "\n" . $rules . "# END All In One WP Security" . "\n";
		}

		return $rules;
	}

	/**
	 * TODO - info
	 */
	public static function getrules_basic_htaccess() {
		global $aio_wp_security;

		$rules = '';
		if ($aio_wp_security->configs->get_value('aiowps_enable_basic_firewall') == '1') {
			$rules .= AIOWPSecurity_Utility_Htaccess::$basic_htaccess_rules_marker_start . "\n"; //Add feature marker start
			//protect the htaccess file - this is done by default with apache config file but we are including it here for good measure
			$rules .= self::create_apache2_access_denied_rule('.htaccess');

			//disable the server signature
			$rules .= 'ServerSignature Off' . "\n";

			//limit file upload size
			$upload_limit = $aio_wp_security->configs->get_value('aiowps_max_file_upload_size');
			//Shouldn't be empty but just in case
			$upload_limit = empty($upload_limit) ? AIOS_FIREWALL_MAX_FILE_UPLOAD_LIMIT_MB : $upload_limit;
			$upload_limit = $upload_limit * 1024 * 1024; // Convert from MB to Bytes - approx but close enough
			
			$rules .= 'LimitRequestBody '.$upload_limit . "\n";

			// protect wpconfig.php.
			$rules .= self::create_apache2_access_denied_rule('wp-config.php');

			$rules .= AIOWPSecurity_Utility_Htaccess::$basic_htaccess_rules_marker_end . "\n"; //Add feature marker end
		}
		return $rules;
	}

	public static function getrules_block_debug_log_access_htaccess() {
		global $aio_wp_security;

		$rules = '';
		if ($aio_wp_security->configs->get_value('aiowps_block_debug_log_file_access') == '1') {
			$rules .= AIOWPSecurity_Utility_Htaccess::$debug_log_block_htaccess_rules_marker_start . "\n"; //Add feature marker start
			$rules .= self::create_apache2_access_denied_rule('debug.log');
			$rules .= AIOWPSecurity_Utility_Htaccess::$debug_log_block_htaccess_rules_marker_end . "\n"; //Add feature marker end
		}
		return $rules;
	}

	/**
	 * This function will disable directory listings for all directories, add this line to the
	 * site’s root .htaccess file.
	 * NOTE: AllowOverride must be enabled in the httpd.conf file for this to work!
	 */
	public static function getrules_disable_index_views() {
		global $aio_wp_security;
		$rules = '';
		if ($aio_wp_security->configs->get_value('aiowps_disable_index_views') == '1') {
			$rules .= AIOWPSecurity_Utility_Htaccess::$disable_index_views_marker_start . "\n"; //Add feature marker start
			$rules .= 'Options -Indexes' . "\n";
			$rules .= AIOWPSecurity_Utility_Htaccess::$disable_index_views_marker_end . "\n"; //Add feature marker end
		}

		return $rules;
	}

	/**
	 * This function will write rules to disable trace and track.
	 * HTTP Trace attack (XST) can be used to return header requests
	 * and grab cookies and other information and is used along with
	 * a cross site scripting attacks (XSS)
	 */
	public static function getrules_disable_trace_and_track() {
		global $aio_wp_security;
		$rules = '';
		if ($aio_wp_security->configs->get_value('aiowps_disable_trace_and_track') == '1') {
			$rules .= AIOWPSecurity_Utility_Htaccess::$disable_trace_track_marker_start . "\n"; //Add feature marker start
			$rules .= '<IfModule mod_rewrite.c>' . "\n";
			$rules .= 'RewriteEngine On' . "\n";
			$rules .= 'RewriteCond %{REQUEST_METHOD} ^(TRACE|TRACK)' . "\n";
			$rules .= 'RewriteRule .* - [F]' . "\n";
			$rules .= '</IfModule>' . "\n";
			$rules .= AIOWPSecurity_Utility_Htaccess::$disable_trace_track_marker_end . "\n"; //Add feature marker end
		}

		return $rules;
	}


	/**
	 * This function contains the rules for the 5G blacklist produced by Jeff Starr from perishablepress.com
	 * NOTE: Since Jeff regularly updates and evolves his blacklist rules, ie, 5G->6G->7G.... we will update this function to reflect the latest blacklist release
	 */
	public static function getrules_5g_blacklist() {
		global $aio_wp_security;
		$rules = '';
		if ('1' == $aio_wp_security->configs->get_value('aiowps_enable_5g_firewall')) {
			$rules .= AIOWPSecurity_Utility_Htaccess::$five_g_blacklist_marker_start . "\n"; //Add feature marker start

			$rules .= '# 5G BLACKLIST/FIREWALL (2013)
						# @ http://perishablepress.com/5g-blacklist-2013/

						# 5G:[QUERY STRINGS]
						<IfModule mod_rewrite.c>
								RewriteEngine On
								RewriteBase /
								RewriteCond %{QUERY_STRING} (\"|%22).*(<|>|%3) [NC,OR]
								RewriteCond %{QUERY_STRING} (javascript:).*(\;) [NC,OR]
								RewriteCond %{QUERY_STRING} (<|%3C).*script.*(>|%3) [NC,OR]
								RewriteCond %{QUERY_STRING} (\\\|\.\./|`|=\'$|=%27$) [NC,OR]
								RewriteCond %{QUERY_STRING} (\;|\'|\"|%22).*(union|select|insert|drop|update|md5|benchmark|or|and|if) [NC,OR]
								RewriteCond %{QUERY_STRING} (base64_encode|localhost|mosconfig) [NC,OR]
								RewriteCond %{QUERY_STRING} (boot\.ini|echo.*kae|etc/passwd) [NC,OR]
								RewriteCond %{QUERY_STRING} (GLOBALS|REQUEST)(=|\[|%) [NC]
								RewriteRule .* - [F]
						</IfModule>

						# 5G:[USER AGENTS]
						<IfModule mod_setenvif.c>
								# SetEnvIfNoCase User-Agent ^$ keep_out
								SetEnvIfNoCase User-Agent (binlar|casper|cmsworldmap|comodo|diavol|dotbot|feedfinder|flicky|ia_archiver|jakarta|kmccrew|nutch|planetwork|purebot|pycurl|skygrid|sucker|turnit|vikspider|zmeu) keep_out
								<limit GET POST PUT>
										Order Allow,Deny
										Allow from all
										Deny from env=keep_out
								</limit>
						</IfModule>

						# 5G:[REQUEST STRINGS]
						<IfModule mod_alias.c>
								RedirectMatch 403 (https?|ftp|php)\://
								RedirectMatch 403 /(https?|ima|ucp)/
								RedirectMatch 403 /(Permanent|Better)$
								RedirectMatch 403 (\=\\\\\\\'|\=\\\%27|/\\\\\\\'/?|\)\.css\()$
								RedirectMatch 403 (\,|\)\+|/\,/|\{0\}|\(/\(|\.\.\.|\+\+\+|\||\\\\\"\\\\\")
								RedirectMatch 403 \.(cgi|asp|aspx|cfg|dll|exe|jsp|mdb|sql|ini|rar)$
								RedirectMatch 403 /(contac|fpw|install|pingserver|register)\.php$
								RedirectMatch 403 (base64|crossdomain|localhost|wwwroot|e107\_)
								RedirectMatch 403 (eval\(|\_vti\_|\(null\)|echo.*kae|config\.xml)
								RedirectMatch 403 \.well\-known/host\-meta
								RedirectMatch 403 /function\.array\-rand
								RedirectMatch 403 \)\;\$\(this\)\.html\(
								RedirectMatch 403 proc/self/environ
								RedirectMatch 403 msnbot\.htm\)\.\_
								RedirectMatch 403 /ref\.outcontrol
								RedirectMatch 403 com\_cropimage
								RedirectMatch 403 indonesia\.htm
								RedirectMatch 403 \{\$itemURL\}
								RedirectMatch 403 function\(\)
								RedirectMatch 403 labels\.rdf
								RedirectMatch 403 /playing.php
								RedirectMatch 403 muieblackcat
						</IfModule>

						# 5G:[REQUEST METHOD]
						<ifModule mod_rewrite.c>
								RewriteCond %{REQUEST_METHOD} ^(TRACE|TRACK)
								RewriteRule .* - [F]
						</IfModule>' . "\n";
			$rules .= AIOWPSecurity_Utility_Htaccess::$five_g_blacklist_marker_end . "\n"; //Add feature marker end
		}

		return $rules;
	}

	/**
	 * This function will write some directives to prevent image hotlinking
	 */
	public static function prevent_image_hotlinks() {
		global $aio_wp_security;
		$rules = '';
		if ($aio_wp_security->configs->get_value('aiowps_prevent_hotlinking') == '1') {
			$url_string = AIOWPSecurity_Utility_Htaccess::return_regularized_url(AIOWPSEC_WP_HOME_URL);
			if (false == $url_string) {
				$url_string = AIOWPSEC_WP_HOME_URL;
			}
			$rules .= AIOWPSecurity_Utility_Htaccess::$prevent_image_hotlinks_marker_start . "\n"; //Add feature marker start
			$rules .= '<IfModule mod_rewrite.c>' . "\n";
			$rules .= 'RewriteEngine On' . "\n";
			$rules .= 'RewriteCond %{HTTP_REFERER} !^$' . "\n";
			$rules .= 'RewriteCond %{REQUEST_FILENAME} -f' . "\n";
			$rules .= 'RewriteCond %{REQUEST_FILENAME} \.(gif|jpe?g?|png)$ [NC]' . "\n";
			$rules .= 'RewriteCond %{HTTP_REFERER} !^' . $url_string . ' [NC]' . "\n";
			$rules .= 'RewriteRule \.(gif|jpe?g?|png)$ - [F,NC,L]' . "\n";
			$rules .= '</IfModule>' . "\n";
			$rules .= AIOWPSecurity_Utility_Htaccess::$prevent_image_hotlinks_marker_end . "\n"; //Add feature marker end
		}

		return $rules;
	}

	/**
	 * This function will write any custom htaccess rules into the server's .htaccess file
	 *
	 * @return string
	 */
	public static function getrules_custom_rules() {
		global $aio_wp_security;
		$rules = '';
		if ($aio_wp_security->configs->get_value('aiowps_enable_custom_rules') == '1') {
			$custom_rules = $aio_wp_security->configs->get_value('aiowps_custom_rules');
			$rules .= AIOWPSecurity_Utility_Htaccess::$custom_rules_marker_start . "\n"; //Add feature marker start
			$rules .= $custom_rules . "\n";
			$rules .= AIOWPSecurity_Utility_Htaccess::$custom_rules_marker_end . "\n"; //Add feature marker end
		}

		return $rules;
	}

	/**
	 * This function will do a quick check to see if a file's contents are actually .htaccess specific.
	 * At the moment it will look for the following tag somewhere in the file - "# BEGIN WordPress"
	 * If it finds the tag it will deem the file as being .htaccess specific.
	 * This was written to supplement the .htaccess restore functionality
	 *
	 * @param string $file_contents - the contents of the .htaccess file
	 *
	 * @return boolean
	 */
	public static function check_if_htaccess_contents($file_contents) {
		$is_htaccess = false;
		
		if (false === $file_contents || strlen($file_contents) == 0) {
			return -1;
		}

		if ((strpos($file_contents, '# BEGIN WordPress') !== false) || (strpos($file_contents, '# BEGIN') !== false)) {
			$is_htaccess = true; // It appears that we have some sort of .htaccess file
		} else {
			//see if we're at the end of the section
			$is_htaccess = false;
		}

		if ($is_htaccess) {
			return 1;
		} else {
			return -1;
		}
	}

	/**
	 * This function will take a URL string and convert it to a form useful for using in htaccess rules.
	 * Example: If URL passed to function = "http://www.mysite.com"
	 * Result = "http(s)?://(.*)?mysite\.com"
	 *
	 * @param string $url
	 * @return string
	 */
	public static function return_regularized_url($url) {
		if (filter_var($url, FILTER_VALIDATE_URL)) {
			$xyz = explode('.', $url);
			$y = '';
			if (count($xyz) > 1) {
				$j = 1;
				foreach ($xyz as $x) {
					if (strpos($x, 'www') !== false) {
						$y .= str_replace('www', '(.*)?', $x);
					} elseif (1 == $j) {
						$y .= $x;
					} elseif ($j > 1) {
						$y .= '\.' . $x;
					}
					$j++;
				}
				//Now replace the "http" with "http(s)?" to cover both secure and non-secure
				if (strpos($y, 'https') !== false) {
					$y = str_replace('https', 'http(s)?', $y);
				} elseif (strpos($y, 'http') !== false) {
					$y = str_replace('http', 'http(s)?', $y);
				}
				return $y;
			} else {
				return $url;
			}
		} else {
			return false;
		}
	}

	/**
	 * Returns a string with <Files $filename> directive that contains rules
	 * to effectively block access to any file that has basename matching
	 * $filename under Apache webserver.
	 *
	 * @link http://httpd.apache.org/docs/current/mod/core.html#files
	 *
	 * @param string $filename
	 * @return string
	 */
	protected static function create_apache2_access_denied_rule($filename) {
		return <<<END
<Files $filename>
<IfModule mod_authz_core.c>
	Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
	Order deny,allow
	Deny from all
</IfModule>
</Files>

END;
		// Keep the empty line at the end of heredoc string,
		// otherwise the string will not end with end-of-line character!
	}


	/**
	 * Convert an array of optionally asterisk-masked or partial IPv4 addresses
	 * into network/netmask notation. Netmask value for a "full" IP is not
	 * added (see example below)
	 *
	 * Example:
	 * In: array('1.2.3.4', '5.6', '7.8.9.*')
	 * Out: array('1.2.3.4', '5.6.0.0/16', '7.8.9.0/24')
	 *
	 * Simple validation is performed:
	 * In: array('1.2.3.4.5', 'abc', '1.2.xyz.4')
	 * Out: array()
	 *
	 * Simple sanitization is performed:
	 * In: array('6.7.*.9')
	 * Out: array('6.7.0.0/16')
	 *
	 * @param array $ips
	 * @return array
	 */
	protected static function add_netmask($ips = array()) {

		$output = array();
		if (empty($ips)) return array();
		foreach ($ips as $ip) {
			//Check if ipv6
			if (strpos($ip, ':') !== false) {
				//for now support whole ipv6 and CIDR range.
				$checked_ip = AIOWPSecurity_Utility_IP::is_ipv6_address_or_ipv6_range($ip);
				if (false != $checked_ip) {
					$output[] = $ip;
				}
			}
			
			$parts = explode('.', $ip);

			// Skip any IP that is empty, has more parts than expected or has
			// a non-numeric first part.
			if (empty($parts) || (count($parts) > 4) || !is_numeric($parts[0])) {
				continue;
			}

			$ip_out = array($parts[0]);
			$netmask = 8;

			for ($i = 1, $force_zero = false; ($i < 4) && $ip_out; $i++) {
				if ($force_zero || !isset($parts[$i]) || ('' === $parts[$i]) || ('*' === $parts[$i])) {
					$ip_out[$i] = '0';
					$force_zero = true; // Forces all subsequent parts to be a zero
				} elseif (is_numeric($parts[$i])) {
					$ip_out[$i] = $parts[$i];
					$netmask += 8;
				} else {
					// Invalid IP part detected, invalidate entire IP
					$ip_out = false;
				}
			}

			if ($ip_out) {
				// Glue IP back together, add netmask if IP denotes a subnet, store for output.
				$output[] = implode('.', $ip_out) . (($netmask < 32) ? ('/' . $netmask) : '');
			}
		}

		return $output;
	}

	public static function get_htaccess_path() {
		$home_path = AIOWPSecurity_Utility_File::get_home_path();
		return $home_path . '.htaccess';
	}
}
