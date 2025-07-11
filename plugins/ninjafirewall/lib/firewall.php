<?php
/*
 +=====================================================================+
 |    _   _ _        _       _____ _                        _ _        |
 |   | \ | (_)_ __  (_) __ _|  ___(_)_ __ _____      ____ _| | |       |
 |   |  \| | | '_ \ | |/ _` | |_  | | '__/ _ \ \ /\ / / _` | | |       |
 |   | |\  | | | | || | (_| |  _| | | | |  __/\ V  V / (_| | | |       |
 |   |_| \_|_|_| |_|/ |\__,_|_|   |_|_|  \___| \_/\_/ \__,_|_|_|       |
 |                |__/                                                 |
 |  (c) NinTechNet Limited ~ https://nintechnet.com/                   |
 +=====================================================================+
*/
if ( strpos($_SERVER['SCRIPT_NAME'], '/nfwlog/') !== FALSE ||
	strpos($_SERVER['SCRIPT_NAME'], '/ninjafirewall/') !== FALSE ) {
	die('Forbidden');
}
if ( defined('NFW_STATUS') ) { return; }
if ( defined('WP_CLI') && WP_CLI && PHP_SAPI === 'cli' ) {
	if (! defined('NFW_UWL') ) {
		define('NFW_UWL', true);
	}
	return;
}

$nfw_ = [];
$nfw_['fw_starttime'] = nfw_fc_metrics('start');

/**
 * Optional NinjaFirewall configuration file.
 * See https://blog.nintechnet.com/ninjafirewall-wp-edition-the-htninja-configuration-file/
 */
if ( @is_file( $nfw_['file'] = $_SERVER['DOCUMENT_ROOT'] .'/.htninja') ||
	@is_file( $nfw_['file'] = dirname( $_SERVER['DOCUMENT_ROOT'] ) .'/.htninja') ) {

	$nfw_['res'] = @include_once $nfw_['file'];
	/**
	 * Allow and stop filtering.
	 */
	if ( $nfw_['res'] == 'ALLOW') {
		if (! defined('NFW_UWL') ) {
			define('NFW_UWL', true );
		}
		nfw_quit( 20 );
		return;
	}
	/**
	 * Reject immediately.
	 */
	if ( $nfw_['res'] == 'BLOCK') {
		header('HTTP/1.1 403 Forbidden');
		header('Status: 403 Forbidden');
		header('Pragma: no-cache');
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Expires: 0');
		die('403 Forbidden');
	}
}
// Clear warning if there's an open_basedir restriction
if ( function_exists('error_clear_last') ) { // PHP 7.0+
	error_clear_last();
}

$nfw_['wp_content'] = dirname(dirname(dirname( __DIR__ )));
// Check if we have a user-defined log directory
// (see "Path to NinjaFirewall's log and cache directory"
// at https://blog.nintechnet.com/ninjafirewall-wp-edition-the-htninja-configuration-file/ ) :
if ( defined('NFW_LOG_DIR') ) {
	$nfw_['log_dir'] = NFW_LOG_DIR . '/nfwlog';
} else {
	$nfw_['log_dir'] = $nfw_['wp_content'] . '/nfwlog';
}
if (! is_dir($nfw_['log_dir']) ) {
	if (! mkdir( $nfw_['log_dir'] . '/cache', 0755, true) ) {
		define( 'NFW_STATUS', 13 );
		return;
	}
}

/**
 * Select whether we want to use PHP or NinjaFirewall sessions.
 */
if ( defined('NFWSESSION') ) {
	if (! defined('NFWSESSION_DIR') ) {
		/**
		 * NFWSESSION_DIR can be defined in the .htninja.
		 */
		define('NFWSESSION_DIR', "{$nfw_['log_dir']}/session" );
	}
	require_once __DIR__ .'/class-nfw-session.php';
} else {
	require_once __DIR__ .'/class-php-session.php';
}

// Get/set PID
if ( is_file( "{$nfw_['log_dir']}/cache/.pid" ) ) {
	define( 'NFW_PID', file_get_contents( "{$nfw_['log_dir']}/cache/.pid" ) );
}

// Check if we are connecting over HTTPS
nfw_is_https();

if ( strpos($_SERVER['SCRIPT_NAME'], 'wp-login.php' ) !== FALSE ) {
	nfw_bfd(1);
} elseif ( strpos($_SERVER['SCRIPT_NAME'], 'xmlrpc.php' ) !== FALSE ) {
	nfw_bfd(2);
}

if (empty ($wp_config)) {
	$wp_config = dirname($nfw_['wp_content']) . '/wp-config.php';
}

// Connection
$ret = nfw_connect();
if ( $ret !== true ) {
	nfw_quit( $ret );
	return;
}

// Fetch options
$ret = nfw_get_data( 'nfw_options' );
if ( $ret !== true ) {
	nfw_quit( $ret );
	return;
}

if (! empty($nfw_['nfw_options']['clogs_pubkey']) && isset($_POST['clogs_req']) ) {
	include_once 'fw_centlog.php';
	fw_centlog();
	exit;
}

if ( empty($nfw_['nfw_options']['enabled']) ) {
	nfw_quit( 20 );
	return;
}

// HTTP response headers
if ( (! empty( $nfw_['nfw_options']['response_headers'] ) || ! empty($nfw_['nfw_options']['custom_headers']) )
	&& function_exists('header_register_callback') ) {

	if (! empty( $nfw_['nfw_options']['response_headers'] ) ) {
		define('NFW_RESHEADERS', $nfw_['nfw_options']['response_headers']);
		if (! empty( $nfw_['nfw_options']['response_headers'][6] ) && ! empty( $nfw_['nfw_options']['csp_frontend_data'] ) ) {
			define( 'CSP_FRONTEND_DATA', $nfw_['nfw_options']['csp_frontend_data']);
		}
		if (! empty( $nfw_['nfw_options']['response_headers'][7] ) && ! empty( $nfw_['nfw_options']['csp_backend_data'] )  ) {
			define( 'CSP_BACKEND_DATA', $nfw_['nfw_options']['csp_backend_data'] );
		}
	}
	if (! empty( $nfw_['nfw_options']['custom_headers'] ) ) {
		define('NFW_CUSTHEADERS', $nfw_['nfw_options']['custom_headers']);
	}
	header_register_callback('nfw_response_headers');
}

if (! empty($nfw_['nfw_options']['force_ssl']) ) {
	define('FORCE_SSL_ADMIN', true);
}
if (! empty($nfw_['nfw_options']['disallow_edit']) ) {
	define('DISALLOW_FILE_EDIT', true);
}
if (! empty($nfw_['nfw_options']['disallow_mods']) ) {
	define('DISALLOW_FILE_MODS', true);
}
if (! empty($nfw_['nfw_options']['disable_error_handler']) ) {
	define('WP_DISABLE_FATAL_ERROR_HANDLER', true);
}

nfw_check_ip();

// Superglobals override
if (! empty($nfw_['nfw_options']['php_superglobals']) ) {
	$sgs = [
		'_GET', '_POST', '_SESSION', '_COOKIE',
		'_SERVER', '_FILES', '_ENV',  '_REQUEST', 'GLOBALS'
	];
	foreach( $sgs as $sg ) {
		if ( isset( $_GET[$sg] ) ) {
			nfw_log('Superglobals override attempt', "\$_GET[$sg]: ". serialize( $_GET[$sg] ), 6, 0);
			unset( $_GET[$sg] );
		}
		if ( isset( $_POST[$sg] ) ) {
			nfw_log('Superglobals override attempt', "\$_POST[$sg]: ". serialize( $_POST[$sg] ), 6, 0);
			unset( $_POST[$sg] );
		}
		if ( isset( $_COOKIE[$sg] ) ) {
			nfw_log('Superglobals override attempt', "\$_COOKIE[$sg]: ". serialize( $_COOKIE[$sg] ), 6, 0);
			unset( $_COOKIE[$sg] );
		}
	}
}

// We only start a session if users already have a session
// cookie because we don't need write access yet
$session_name = NinjaFirewall_session::name();
if ( isset( $_COOKIE[ $session_name ] ) ) {
	NinjaFirewall_session::start();
}

if (! empty( NinjaFirewall_session::read('nfw_goodguy') ) ) {
	// Look for Live Log AJAX request
	if (! empty( NinjaFirewall_session::read('nfw_livelog') ) &&
		isset( $_POST['livecls'] ) && isset( $_POST['lines'] ) ) {

		include_once 'fw_livelog.php';
		fw_livelog_show();
	}

	// Fetch admin rules
	$ret = nfw_get_data( 'nfw_rules' );
	if ( $ret !== true ) {
		nfw_quit( $ret );
		return;
	}
	nfw_check_admin_request();

	nfw_quit( 20 );
	return;
}
define('NFW_SWL', 1);

if ( is_file($nfw_['log_dir'] .'/cache/livelogrun.php')) {
	include_once 'fw_livelog.php';
	fw_livelog_record();
}

if (! empty($nfw_['nfw_options']['php_errors']) ) {
	@error_reporting(0);
	@ini_set('display_errors', 0);
}

if ( empty($nfw_['nfw_options']['allow_local_ip']) && NFW_REMOTE_ADDR_PRIVATE == true ) {
	nfw_quit(20);
	return;
}

if ( NFW_REMOTE_ADDR_PRIVATE == true && strpos( $_SERVER['SCRIPT_NAME'], '/wp-cron.php' ) !== FALSE ) {
	nfw_quit(20);
	return;
}

if ( @$nfw_['nfw_options']['scan_protocol'] == 1 && NFW_IS_HTTPS == true ) {
	nfw_quit(20);
	return;
}
if ( @$nfw_['nfw_options']['scan_protocol'] == 2 && NFW_IS_HTTPS == false ) {
	nfw_quit(20);
	return;
}

if (! empty($nfw_['nfw_options']['fg_enable']) && ! defined('NFW_WPWAF') ) {
	include_once 'fw_fileguard.php';
	fw_fileguard();
}

if (! empty($nfw_['nfw_options']['no_host_ip']) && @filter_var(parse_url('http://'.$_SERVER['HTTP_HOST'], PHP_URL_HOST), FILTER_VALIDATE_IP) ) {
	nfw_log('HTTP_HOST is an IP', $_SERVER['HTTP_HOST'], 1, 0);
   nfw_block();
}

if (! empty($nfw_['nfw_options']['referer_post']) && $_SERVER['REQUEST_METHOD'] == 'POST' && ! isset($_SERVER['HTTP_REFERER']) ) {
	nfw_log('POST method without Referer header', $_SERVER['REQUEST_METHOD'], 1, 0);
   nfw_block();
}

if (! empty($nfw_['nfw_options']['admin_ajax']) && strpos( $_SERVER['SCRIPT_NAME'], 'wp-admin/admin-ajax.php' ) !== FALSE ) {
	nfw_is_bot( 'admin-ajax.php' );
}

if ( strpos($_SERVER['SCRIPT_NAME'], '/xmlrpc.php' ) !== FALSE ) {
	if (! empty($nfw_['nfw_options']['no_xmlrpc']) ) {
		nfw_log('Access to WordPress XML-RPC API', $_SERVER['SCRIPT_NAME'], 2, 0);
		nfw_block();
	}
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		if (! isset( $HTTP_RAW_POST_DATA ) ) {
			@$HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
		}

		if (! empty($nfw_['nfw_options']['no_xmlrpc_multi']) ) {

			if ( @strpos( $HTTP_RAW_POST_DATA, '<methodName>system.multicall</methodName>') !== FALSE ) {
				nfw_log('Access to WordPress XML-RPC API (system.multicall method)', $_SERVER['SCRIPT_NAME'], 2, 0);
				nfw_block();
			}
		}

		if (! empty($nfw_['nfw_options']['no_xmlrpc_pingback']) ) {

			if ( @strpos( $HTTP_RAW_POST_DATA, '<methodName>pingback.ping</methodName>') !== FALSE ) {
				nfw_log('Access to WordPress XML-RPC API (pingback.ping)', $_SERVER['SCRIPT_NAME'], 2, 0);
				nfw_block();
			}
		}
	}
}
if (! empty($nfw_['nfw_options']['no_xmlrpc_pingback']) && strpos($_SERVER['HTTP_USER_AGENT'], '; verifying pingback from ') !== FALSE) {
	nfw_log('Blocked pingback verification', $_SERVER['HTTP_USER_AGENT'], 2, 0);
   nfw_block();
}

// WordPress Aplication Passwords
if (! empty($nfw_['nfw_options']['no_appswd']) && strpos( $_SERVER['SCRIPT_NAME'], '/wp-admin/authorize-application.php' ) !== FALSE ) {
	nfw_log('Access to WordPress Application Passwords', $_SERVER['SCRIPT_NAME'], 2, 0);
	nfw_block();
}

if (! empty($nfw_['nfw_options']['no_post_themes']) && $_SERVER['REQUEST_METHOD'] == 'POST' && strpos($_SERVER['SCRIPT_NAME'], $nfw_['nfw_options']['no_post_themes']) !== FALSE ) {
	nfw_log('POST request in the themes folder', $_SERVER['SCRIPT_NAME'], 2, 0);
   nfw_block();
}

if (! empty($nfw_['nfw_options']['wp_dir']) && preg_match( '`' . $nfw_['nfw_options']['wp_dir'] . '`', $_SERVER['SCRIPT_NAME']) ) {
	nfw_log('Forbidden direct access to PHP script', $_SERVER['SCRIPT_NAME'], 2, 0);
   nfw_block();
}

nfw_check_upload();

// Fetch rules
$ret = nfw_get_data( 'nfw_rules' );
if ( $ret !== true ) {
	nfw_quit( $ret );
	return;
}

nfw_check_request( $nfw_['nfw_rules'], $nfw_['nfw_options'] );

if (! empty($nfw_['nfw_options']['get_sanitise']) && ! empty($_GET) ){
	$_GET = nfw_sanitise( $_GET, 1, 'GET');
}
if (! empty($nfw_['nfw_options']['post_sanitise']) && ! empty($_POST) ){
	$_POST = nfw_sanitise( $_POST, 1, 'POST');
}
if (! empty($nfw_['nfw_options']['request_sanitise']) && ! empty($_REQUEST) ){
	$_REQUEST = nfw_sanitise( $_REQUEST, 1, 'REQUEST');
}
if (! empty($nfw_['nfw_options']['cookies_sanitise']) && ! empty($_COOKIE) ) {
	$_COOKIE = nfw_sanitise( $_COOKIE, 3, 'COOKIE');
}
if (! empty($nfw_['nfw_options']['ua_sanitise']) && ! empty($_SERVER['HTTP_USER_AGENT']) ) {
	$_SERVER['HTTP_USER_AGENT'] = nfw_sanitise( $_SERVER['HTTP_USER_AGENT'], 1, 'HTTP_USER_AGENT');
}
if (! empty($nfw_['nfw_options']['referer_sanitise']) && ! empty($_SERVER['HTTP_REFERER']) ) {
	$_SERVER['HTTP_REFERER'] = nfw_sanitise( $_SERVER['HTTP_REFERER'], 1, 'HTTP_REFERER');
}
if (! empty($nfw_['nfw_options']['php_path_i']) && ! empty($_SERVER['PATH_INFO']) ) {
	$_SERVER['PATH_INFO'] = nfw_sanitise( $_SERVER['PATH_INFO'], 2, 'PATH_INFO');
}
if (! empty($nfw_['nfw_options']['php_path_t']) && ! empty($_SERVER['PATH_TRANSLATED']) ) {
	$_SERVER['PATH_TRANSLATED'] = nfw_sanitise( $_SERVER['PATH_TRANSLATED'], 2, 'PATH_TRANSLATED');
}
if (! empty($nfw_['nfw_options']['php_self']) && ! empty($_SERVER['PHP_SELF']) ) {
	$_SERVER['PHP_SELF'] = nfw_sanitise( $_SERVER['PHP_SELF'], 2, 'PHP_SELF');
}

nfw_quit(20);
return;

// =====================================================================
// Close the SQL link, set the firewall status, clear the $nfw_ array
// and close the session before leaving.

function nfw_quit( $status ) {

	global $nfw_;
	define( 'NFW_STATUS', $status );

	if ( isset( $nfw_['mysqli'] ) ) {
		$nfw_['mysqli']->close();
	}
	$nfw_ = [];
}

// =====================================================================
// Connect to the DB.

function nfw_connect() {

	global $nfw_, $wp_config;

	// WPWAF mode?
	if ( defined('NFW_WPWAF') && NFW_WPWAF == 2 ) {
		$nfw_['wp_waf'] = 2;
		return true;
	}

	// Check if we have a SQL link that was defined in the .htninja.
	// See "Giving NinjaFirewall a MySQLi link identifier"
	// at https://blog.nintechnet.com/ninjafirewall-wp-edition-the-htninja-configuration-file/
	if (! empty( $GLOBALS['nfw_mysqli'] ) && ! empty( $GLOBALS['nfw_table_prefix'] ) ) {
		$nfw_['mysqli'] = $GLOBALS['nfw_mysqli'];
		$nfw_['table_prefix'] = $GLOBALS['nfw_table_prefix'];
		return true;
	}

	// DB
	if (! is_file( $wp_config ) ) {
		if (! @is_file( $wp_config = dirname( dirname($nfw_['wp_content']) ) . '/wp-config.php') ) {
			return 1;
		}
	}
	if (! $nfw_['fh'] = fopen($wp_config, 'r') ) {
		return 2;
	}

	// Potential SQL flags
	$nfw_['MYSQL_CLIENT_FLAGS'] = 0;

	while (! feof($nfw_['fh'])) {
		$nfw_['line'] = fgets($nfw_['fh']);
		if ( preg_match('/^\s*define\s*\(\s*[\'"]DB_NAME[\'"]\s*,\s*[\'"](.+?)[\'"]/', $nfw_['line'], $nfw_['match']) ) {
			$nfw_['DB_NAME'] = $nfw_['match'][1];
		} elseif ( preg_match('/^\s*define\s*\(\s*[\'"]DB_USER[\'"]\s*,\s*[\'"](.+?)[\'"]/', $nfw_['line'], $nfw_['match']) ) {
			$nfw_['DB_USER'] = $nfw_['match'][1];
		} elseif ( preg_match('/^\s*define\s*\(\s*[\'"]DB_PASSWORD[\'"]\s*,\s*([\'"])(.+?)\1\s*\);/', $nfw_['line'], $nfw_['match']) ) {
			$nfw_['DB_PASSWORD'] = str_replace( '\\'.$nfw_['match'][1], $nfw_['match'][1], $nfw_['match'][2] );
			if ( $nfw_['match'][1] == '"' ) {
				$nfw_['DB_PASSWORD'] = str_replace( '\$', '$', $nfw_['DB_PASSWORD'] );
			}
		} elseif ( preg_match('/^\s*define\s*\(\s*[\'"]DB_HOST[\'"]\s*,\s*[\'"](.+?)[\'"]/', $nfw_['line'], $nfw_['match']) ) {
			$nfw_['DB_HOST'] = $nfw_['match'][1];
		} elseif ( preg_match('/^\s*\$table_prefix\s*=\s*[\'"](.*?)[\'"]/', $nfw_['line'], $nfw_['match']) ) {
			$nfw_['table_prefix'] = $nfw_['match'][1];
		} elseif ( preg_match('/^\s*define\s*\(\s*[\'"]MYSQL_CLIENT_FLAGS[\'"]\s*,\s*(.+?)\s*\)/', $nfw_['line'], $nfw_['match']) ) {
			if ( empty( $nfw_['MYSQL_CLIENT_FLAGS'] ) ) {
				$available_flags = [
					'MYSQLI_CLIENT_COMPRESS' => MYSQLI_CLIENT_COMPRESS,
					'MYSQLI_CLIENT_FOUND_ROWS' => MYSQLI_CLIENT_FOUND_ROWS,
					'MYSQLI_CLIENT_IGNORE_SPACE' => MYSQLI_CLIENT_IGNORE_SPACE,
					'MYSQLI_CLIENT_INTERACTIVE' => MYSQLI_CLIENT_INTERACTIVE,
					'MYSQLI_CLIENT_SSL' => MYSQLI_CLIENT_SSL,
					'MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT' => MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT
				];
				// There could be one or more flags, e.g., 'MYSQLI_CLIENT_SSL | MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT'
				$tmp_flags = explode( '|', $nfw_['match'][1] );
				foreach( $tmp_flags as $tmp_flag ) {
					$tmp_flag = trim( $tmp_flag );
					if ( isset( $available_flags[$tmp_flag] ) ) {
						$nfw_['MYSQL_CLIENT_FLAGS'] += $available_flags[$tmp_flag];
					}
				}
			}
		}
	}
	fclose($nfw_['fh']);
	unset($wp_config);
	if (! isset($nfw_['DB_NAME']) || ! isset($nfw_['DB_USER']) || ! isset($nfw_['DB_PASSWORD']) || ! isset($nfw_['DB_HOST']) || ! isset($nfw_['table_prefix']) ) {
		return 3;
	}

	nfw_check_dbhost();
	// Make sure mysqli extension is loaded
	if (! function_exists( 'mysqli_real_connect' ) ) {
		return 14;
	}
	@$nfw_['mysqli'] = mysqli_init();
	@mysqli_real_connect( $nfw_['mysqli'], $nfw_['DB_HOST'], $nfw_['DB_USER'], $nfw_['DB_PASSWORD'], $nfw_['DB_NAME'], $nfw_['port'], $nfw_['socket'], $nfw_['MYSQL_CLIENT_FLAGS'] );
	if ($nfw_['mysqli']->connect_error) {
		return 4;
	}

	return true;
}

// =====================================================================
// Fetch rules and options.

function nfw_get_data( $what ) {

	global $nfw_;

	if ( $what != 'nfw_rules' ) {
		$what = 'nfw_options';
	}

	// WP API
	if ( isset( $nfw_['wp_waf'] ) && $nfw_['wp_waf'] == 2 ) {
		if ( is_multisite() ) {
			$nfw_[ $what ] = get_site_option( $what );
		} else {
			$nfw_[ $what ] = get_option( $what );
		}
		return true;

	// DB
	} else {
		// Rules
		if ( $what == 'nfw_rules' ) {
			if (! $nfw_['result'] = @$nfw_['mysqli']->query('SELECT * FROM `' . $nfw_['mysqli']->real_escape_string($nfw_['table_prefix']) . "options` WHERE `option_name` = 'nfw_rules'") ) {
				return 7;
			}
			if (! $nfw_['rules'] = @$nfw_['result']->fetch_object() ) {
				return 8;
			}
			if (! $nfw_['nfw_rules'] = @unserialize( $nfw_['rules']->option_value ) ) {
				return 12;
			}
		// Options
		} else {
			if (! $nfw_['result'] = @$nfw_['mysqli']->query('SELECT * FROM `' . $nfw_['mysqli']->real_escape_string($nfw_['table_prefix']) . "options` WHERE `option_name` = 'nfw_options'") ) {

			// Maybe this is an old multisite install where the main site
			// options table is named 'wp_1_options' instead of 'wp_options'
				if (! $nfw_['result'] = @$nfw_['mysqli']->query('SELECT * FROM `' . $nfw_['mysqli']->real_escape_string($nfw_['table_prefix']) . "1_options` WHERE `option_name` = 'nfw_options'") ) {
					return 5;
				}
				// Change the table prefix to match 'wp_1_options'
				$nfw_['table_prefix'] = "{$nfw_['table_prefix']}1_";
			}
			if (! $nfw_['options'] = @$nfw_['result']->fetch_object() ) {
				return 6;
			}
			if (! $nfw_['nfw_options'] = @unserialize( $nfw_['options']->option_value ) ) {
				return 11;
			}
		}

		// Make sure we have something or return an error
		if ( $what == 'nfw_rules' && ! isset( $nfw_['nfw_rules']['1'] ) ) {
			return 16;
		} elseif ( $what == 'nfw_options' && ! isset( $nfw_['nfw_options']['enabled'] ) ) {
			return 15;
		}

		// All good
		return true;
	}
}

// =====================================================================
// Check for HTTPS.

function nfw_is_https() {

	// Can be defined in the .htninja:
	if ( defined('NFW_IS_HTTPS') ) { return; }

	if ( ( isset( $_SERVER['SERVER_PORT'] ) && $_SERVER['SERVER_PORT'] == 443 ) ||
		( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ||
		( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ) ) {
		define('NFW_IS_HTTPS', true);
	} else {
		define('NFW_IS_HTTPS', false);
	}
}

// =====================================================================

function nfw_check_ip() {

	if ( defined('NFW_REMOTE_ADDR') ) { return; }

	global $nfw_;

	if (! isset( $_SERVER['REMOTE_ADDR'] ) ) { $_SERVER['REMOTE_ADDR'] = '127.0.0.1'; }
	if (strpos($_SERVER['REMOTE_ADDR'], ',') !== false) {
		// Ensure we have a proper and single IP (a user may use the .htninja file
		// to redirect HTTP_X_FORWARDED_FOR, which may contain more than one IP,
		// to REMOTE_ADDR):
		$nfw_['match'] = array_map('trim', @explode(',', $_SERVER['REMOTE_ADDR']));
		foreach($nfw_['match'] as $nfw_['m']) {
			if ( filter_var($nfw_['m'], FILTER_VALIDATE_IP) )  {
				define( 'NFW_REMOTE_ADDR', $nfw_['m']);
				break;
			}
		}
	}
	if (! defined('NFW_REMOTE_ADDR') ) {
		define('NFW_REMOTE_ADDR', htmlspecialchars($_SERVER['REMOTE_ADDR']) );
	}

	if ( filter_var( NFW_REMOTE_ADDR, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
		define( 'NFW_REMOTE_ADDR_IPV6', true );
	} else {
		define( 'NFW_REMOTE_ADDR_IPV6', false );
	}

	if (filter_var( NFW_REMOTE_ADDR, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
		define( 'NFW_REMOTE_ADDR_PRIVATE', false );
	} else {
		define( 'NFW_REMOTE_ADDR_PRIVATE', true );
	}
}

// =====================================================================

function nfw_check_upload() {

	if ( defined('NFW_STATUS') ) { return; }

	global $nfw_;

	$f_uploaded = [];
	$f_uploaded = nfw_fetch_uploads();
	$tmp = '';
	if ( empty($nfw_['nfw_options']['uploads']) ) {
		$tmp = '';
		foreach ($f_uploaded as $key => $value) {
			if (! $f_uploaded[$key]['name']) { continue; }
			if ( empty( $f_uploaded[$key]['size'] ) ) { $f_uploaded[$key]['size'] = 0; }
         $tmp .= $f_uploaded[$key]['name'] . ' (' . number_format($f_uploaded[$key]['size']) . ' bytes) ';
      }
      if ( $tmp ) {
			nfw_log('Blocked file upload attempt', rtrim($tmp, ' '), 3, 0);
			nfw_block();
		}
	} else {
		foreach ($f_uploaded as $key => $value) {
			if (! $f_uploaded[$key]['name']) { continue; }
			if ( empty( $f_uploaded[$key]['size'] ) ) { $f_uploaded[$key]['size'] = 0; }
			if ( $f_uploaded[$key]['size'] > 67 && $f_uploaded[$key]['size'] < 129 ) {
				$data = file_get_contents( $f_uploaded[$key]['tmp_name'] );
				if ( preg_match('`^X5O!P%@AP' . '\[4\\\PZX54\(P\^\)7CC\)7}\$EIC' .
				                'AR-STANDARD-ANTIVI' . 'RUS-TEST-FILE!\$H' . '\+H\*' .
				                '[\x09\x10\x13\x20\x1A]*`', $data) ) {
					nfw_log('EICAR Standard Anti-Virus Test File blocked', $f_uploaded[$key]['name'] . ' (' . number_format($f_uploaded[$key]['size']) . ' bytes)', 3, 0);
					nfw_block();
				}
			}

			if (! defined('NFW_NO_MIMECHECK') && isset( $f_uploaded[$key]['type'] ) && ! preg_match('/\/.*\bphp\d?\b/i', $f_uploaded[$key]['type']) &&
			preg_match('/\.ph(?:p([34x7]|5\d?)?|t(ml)?)(?:\.|$)/', $f_uploaded[$key]['name']) ) {
				nfw_log('Blocked file upload attempt (MIME-type mismatch)', $f_uploaded[$key]['name'] .' != '. $f_uploaded[$key]['type'], 3, 0);
				nfw_block();
			}

			if (! empty($nfw_['nfw_options']['sanitise_fn']) ) {
				if ( empty( $nfw_['nfw_options']['substitute'] ) ) {
					$nfw_['nfw_options']['substitute'] = 'X';
				}
				$tmp = '';
				$f_uploaded_name = $f_uploaded[$key]['name'];
				$f_uploaded[$key]['name'] = preg_replace('/[^\w\.\-]/i', $nfw_['nfw_options']['substitute'], $f_uploaded[$key]['name'], -1, $count);

				// Sanitize double (or more) extensions (e.g., foo.php.gif => foo.php_.gif)
				$ret = [];
				$ret = nfw_sanitize_extensions( $f_uploaded[$key]['name'], $nfw_['nfw_options']['substitute'] );
				if (! empty( $ret['count'] ) ) {
					$count += $ret['count'];
					$f_uploaded[$key]['name'] = $ret['name'];
				}

				if ($count) {
					$tmp = ' (sanitising '. $count . ' char. from filename)';
					$_FILES = nfw_sanitize_filename( $_FILES, $f_uploaded_name, $f_uploaded[$key]['name'] );
				}

			}

			if (! isset( $f_uploaded[$key]['size'] ) ) {
				$size = 'n/a';
			} else {
				$size = number_format( $f_uploaded[$key]['size'] );
			}
			nfw_log('File upload detected, no action taken' . $tmp , "{$f_uploaded[$key]['name']} ($size bytes)", 5, 0);
		}
	}
}

// =====================================================================

function nfw_fetch_uploads() {

	global $file_buffer, $upload_array, $prop_key;
	$upload_array = [];

	foreach( $_FILES as $f_key => $f_value ) {

		foreach( $f_value as $prop_key => $prop_value ) {

			// Fetch all but 'error':
			if (! in_array( $prop_key, ['name', 'type', 'tmp_name', 'size'] ) ) { continue; }

			$file_buffer = $f_key;

			if ( is_array( $_FILES[$f_key][$prop_key] ) ) {
				nfw_recursive_upload( $_FILES[$f_key][$prop_key] );
			} else {
				if (! empty( $_FILES[$f_key][$prop_key] ) ) {
					$upload_array[$f_key][$prop_key] = $_FILES[$f_key][$prop_key];
				}
			}
		}
	}
	return $upload_array;
}

// =====================================================================

function nfw_recursive_upload( $data ) {

	global $file_buffer, $upload_array, $prop_key;

	foreach( $data as $data_key => $data_value ) {
		if ( is_array( $data_value ) ) {
			$file_buffer .= "_{$data_key}";
			nfw_recursive_upload( $data_value );
		} else {
			if ( empty( $data_value ) ) { continue; }
			$upload_array["{$file_buffer}_{$data_key}"][$prop_key] = $data_value;
		}
	}
}

// =====================================================================

function nfw_sanitize_filename( $array, $key, $value ) {

	array_walk_recursive(
		$array, function( &$v, $k ) use ( $key, $value ) {
			if (! empty( $v ) && $v == $key ) { $v = $value; }
		}
	);
	return $array;
}

function nfw_sanitize_extensions( $filename, $subs ) {

	$ret = [];
	$ret['count'] = 0;
	$parts = explode( '.', $filename );
	$ret['name'] = array_shift( $parts );
	$extension = array_pop( $parts );
	foreach ( $parts as $part ) {
		if (! empty( $part ) ) {
			$ret['name'] .= ".{$part}{$subs}";
			++$ret['count'];
		}
	}
	if ( $extension ) {
		$ret['name'] .= ".{$extension}";
	}
	return $ret;
}
// =====================================================================

function nfw_check_admin_request() {

	global $nfw_;

	if ( isset( $nfw_['nfw_rules']['999'] ) ) {
		$nfw_['adm_rules'] = [];
		foreach ( $nfw_['nfw_rules']['999'] as $key => $value ) {
			if ( empty( $nfw_['nfw_rules'][$key]['ena'] ) ) { continue; }
			$nfw_['adm_rules'][$key] = $nfw_['nfw_rules'][$key];
		}
		if (! empty( $nfw_['adm_rules'] ) ) {
			nfw_check_request( $nfw_['adm_rules'], $nfw_['nfw_options'] );
		}
	}
}

// =====================================================================

function nfw_check_request( $nfw_rules, $nfw_options ) {

	if ( defined('NFW_STATUS') ) { return; }

	global $nfw_, $HTTP_RAW_POST_DATA;

	foreach ( $nfw_rules as $id => $rules ) {

		if ( empty( $rules['ena']) ) { continue; }

		$wherelist = explode('|', $rules['cha'][1]['whe']);

		foreach ($wherelist as $where) {

			if ( nfw_disabled_scan( $where, $nfw_options ) ) { continue; }

			// =================================================================
			if ( $where == 'RAW' ) {
				if (! isset( $HTTP_RAW_POST_DATA ) ) {
					@$HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
				}

				if ( nfw_matching( 'RAW', $_SERVER['REQUEST_METHOD'], $nfw_rules, $rules, 1, $id, $nfw_options, $HTTP_RAW_POST_DATA ) ) {
					nfw_check_subrule( 'RAW', $_SERVER['REQUEST_METHOD'], $nfw_rules, $nfw_options, $rules, $id );
				}
				continue;
			}

			// =================================================================
			if ( $where == 'POST' || $where == 'GET' || $where == 'COOKIE' ||
				$where == 'SERVER' || $where == 'REQUEST' || $where == 'FILES' ||
				$where == 'SESSION'
			) {

				if (! isset( $GLOBALS['_'. $where ] ) ) { continue; }

				foreach ($GLOBALS['_' . $where] as $key => $val) {

					if ( nfw_matching( $where, $key, $nfw_rules, $rules, 1, $id, $nfw_options ) ) {
						nfw_check_subrule( $where, $key, $nfw_rules, $nfw_options, $rules, $id );
					}

				}
				continue;
			}

			// =================================================================

			if ( isset( $_SERVER[$where] ) ) {

				if ( nfw_matching( 'SERVER', $where, $nfw_rules, $rules, 1, $id, $nfw_options ) ) {
					nfw_check_subrule( 'SERVER', $where, $nfw_rules, $nfw_options, $rules, $id );
				}
				continue;
			}

			// =================================================================

			$w = explode(':', $where);

			// Look for temp hash
			if ( isset( $rules['cha'][1]['tmp'] ) && isset( $w[1] ) ) {
				$w[1] = @nfw_check_temp_hash( $w[0], $w[1] );
			}

			if ( empty($w[1]) || ! isset( $GLOBALS['_'.$w[0]][$w[1]] ) || nfw_disabled_scan( $w[0], $nfw_options ) ) {
				continue;
			}

			if ( nfw_matching( $w[0], $w[1], $nfw_rules, $rules, 1, $id, $nfw_options ) ) {
				nfw_check_subrule( $w[0], $w[1], $nfw_rules, $nfw_options, $rules, $id );
			}

			// =================================================================

		}

	}

}

// =====================================================================
// Check hash found in a temporary rule (used for hotfix, 0-day etc).

function nfw_check_temp_hash( $where, $what ) {

	global $nfw_;

	if (is_array( $GLOBALS["_{$where}"] ) && ! empty( $GLOBALS["_{$where}"] ) ) {
		// Loop
		foreach( $GLOBALS["_{$where}"] as $key => $value ) {
			if ( is_string( $key ) ) {
				// Search in the cache
				if ( isset( $nfw_['hash'][$key] ) ) {
					if ( $nfw_['hash'][$key] == $what ) {
						return $key;
					}
				} else {
					// Save it to the cache
					$nfw_['hash'][$key] = md5( substr_replace( $key, 'nfw', 2, 0 ) );
					if ( $nfw_['hash'][$key] == $what ) {
						return $key;
					}
				}
			}
		}
	}
	return $what;
}

// =====================================================================

function nfw_check_subrule( $w0, $w1, $nfw_rules, $nfw_options, $rules, $id ) {

	if ( isset( $rules['cha'][1]['cap'] ) ) {
		nfw_matching( $w0, $w1, $nfw_rules, $rules, 2, $id, $nfw_options );

	} else {
		$w = explode(':', $rules['cha'][2]['whe']);

		if (! isset( $w[1] ) ) {

			if ( $w[0] == 'RAW' ) {
				if ( nfw_disabled_scan( 'POST', $nfw_options) && $_SERVER['REQUEST_METHOD'] == 'POST' ) {
					return;
				}
				global $HTTP_RAW_POST_DATA;
				if (! isset( $HTTP_RAW_POST_DATA ) ) {
					@$HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
				}
				nfw_matching( $_SERVER['REQUEST_METHOD'], 'RAW', $nfw_rules, $rules, 2, $id, $nfw_options, $HTTP_RAW_POST_DATA );
				return;
			}
			$w[2] = $w[1] = $w[0];
			$w[0] = 'SERVER';
		} else {
			$w[2] = null;

			// Look for temp hash
			if ( isset( $rules['cha'][2]['tmp'] ) ) {
				$w[1] = @nfw_check_temp_hash( $w[0], $w[1] );
			}
		}

		if (! isset( $GLOBALS['_'.$w[0]][$w[1]] ) ) {
			return;
		}

		if ( nfw_disabled_scan( $w[0], $nfw_options, $w[2] ) ) {
			return;
		} else {
			nfw_matching( $w[0], $w[1], $nfw_rules, $rules, 2, $id, $nfw_options);
		}
	}

}

// =====================================================================

function nfw_disabled_scan( $where, $nfw_options, $extra = null ) {

	if ( $extra ) { $where = $extra; }

	if ( $where == 'POST' && empty($nfw_options['post_scan']) ||
		$where == 'GET' && empty($nfw_options['get_scan']) ||
		$where == 'COOKIE' && empty($nfw_options['cookies_scan']) ||
		$where == 'HTTP_USER_AGENT' && empty($nfw_options['ua_scan']) ||
		$where == 'HTTP_REFERER' && empty($nfw_options['referer_scan'])
	) {
		return 1;
	}
	return 0;
}

// =====================================================================

function nfw_matching( $where, $key, $nfw_rules, $rules, $subid, $id, $nfw_options, $RAW_POST = null ) {

	global $nfw_;

	if ( isset( $RAW_POST ) ) {
		$val = $RAW_POST;
	} else {
		$val = $GLOBALS['_'.$where][$key];
	}

	// Check if the user has the required capability, if any
	$allcaps = NinjaFirewall_session::read('allcaps');
	if ( isset( $rules['cpb'] ) && ! empty( $allcaps ) ) {
		$caps = explode( '|', $rules['cpb'] );
		foreach( $caps as $cap ) {
			if ( isset( $allcaps[$cap] ) ) {
				return 0;
			}
		}
	}

	if ( is_array($val) ) {
		if ( isset( $nfw_['flattened'][$where][$key] ) ) {
			$val = $nfw_['flattened'][$where][$key];
		} else {
			$val = nfw_flatten( ' ', $val );
			$nfw_['flattened'][$where][$key] = $val;
		}
	}

	if ( $where == 'POST' && ! empty($nfw_options['post_b64']) && ! isset($nfw_['b64'][$where][$key]) && $val ) {
		nfw_check_b64($key, $val);
		$nfw_['b64'][$where][$key] = 1;
	}

	$transform = 1;
	// NF < 4.1.1:
	if ( isset( $rules['cha'][$subid]['exe'] ) ) {
		$transform = 0;
		if ( function_exists( $rules['cha'][$subid]['exe'] ) ) {
			$val = @$rules['cha'][$subid]['exe']( $val );
		}
	}
	// NF >= 4.1.1:
	if ( isset( $rules['cha'][$subid]['exm'] ) ) {
		$transform = 0;
		$exe = explode( '|', $rules['cha'][$subid]['exm'] );
		foreach ( $exe as $f ) {
			if (! function_exists( $f ) ) { break; }
			$val = @$f( $val );
		}
	}

	$t = '';

	if ( isset( $rules['cha'][$subid]['nor'] ) ) {
		$t .= 'N';
		if ( isset( $nfw_[$t][$where][$key] ) && $transform ) {
			$val = $nfw_[$t][$where][$key];
		} else {
			$val = nfw_normalize( $val, $nfw_rules );
			if ( $transform ) {
				$nfw_[$t][$where][$key] = $val;
			}
		}
	}

	if ( isset( $rules['cha'][$subid]['tra'] ) ) {
		$t .= 'T' . $rules['cha'][$subid]['tra'];
		if ( isset( $nfw_[$t][$where][$key] ) && $transform ) {
			$val = $nfw_[$t][$where][$key];
		} else {
			$val = nfw_transform_string( $val, $rules['cha'][$subid]['tra'] );
			if ( $transform ) {
				$nfw_[$t][$where][$key] = $val;
			}
		}
	}
	if ( empty( $rules['cha'][$subid]['noc']) ) {
		$t .= 'C';
		if ( isset( $nfw_[$t][$where][$key] ) && $transform ) {
			$val = $nfw_[$t][$where][$key];
		} else {
			$val = nfw_compress_string( $val );
			if ( $transform ) {
				$nfw_[$t][$where][$key] = $val;
			}
		}
	}

	if ( nfw_operator( $val, $rules['cha'][$subid]['wha'], $rules['cha'][$subid]['ope']	) ) {
		if ( isset( $rules['cha'][$subid+1]) ) {
			return 1;
		} else {
			if ( isset( $nfw_['flattened'][$where][$key] ) ) {
				nfw_log($rules['why'], $where .':' . $key . ' = ' . $nfw_['flattened'][$where][$key], $rules['lev'], $id);
			} elseif ( isset( $RAW_POST ) ) {
				nfw_log($rules['why'], $where .':' . $key . ' = ' . $RAW_POST, $rules['lev'], $id);
			} else {
				nfw_log($rules['why'], $where .':' . $key . ' = ' . $GLOBALS['_'.$where][$key], $rules['lev'], $id);
			}
			nfw_block();
		}
	}
	return 0;
}

// =====================================================================

function nfw_operator( $val, $what, $op ) {

	if (! $val ) { return false; }

	if ( $op == 2 ) {
		if ( $val != $what ) {
			return true;
		}
	} elseif ( $op == 3 ) {
		if ( strpos($val, $what) !== FALSE ) {
			return true;
		}
	} elseif ( $op == 4 ) {
		if ( stripos($val, $what) !== FALSE ) {
			return true;
		}
	} elseif ( $op == 5 ) {
		if ( preg_match("`$what`", $val ) ) {
			return true;
		}
	} elseif ( $op == 6 ) {
		if (! preg_match("`$what`", $val) ) {
			return true;
		}
	} elseif ( $op == 7 ) {
		return true;

	} elseif ( $op == 8 ) {
		if ( strpos($val, $what) === FALSE ) {
			return true;
		}
	} elseif ( $op == 9 ) {
		if ( stripos($val, $what) === FALSE ) {
			return true;
		}
	} else {
		if ( $val == $what ) {
			return true;
		}
	}
}

// =====================================================================

function nfw_normalize( $string, $nfw_rules ) {

	if ( empty( $string ) ) {
		return;
	}

	$norm = rawurldecode( $string );
	if ( strpos( $norm, '%' ) !== false ) {
		$norm = rawurldecode( $norm );
	}
	if (! $norm ) {
		return $string;
	}

	if ( preg_match('/&(?:#x(?:00)*[0-9a-f]{2}|#0*[12]?[0-9]{2}|amp|[lg]t|nbsp|quot)(?!;|\d)/i', $norm) ) {
		$norm = preg_replace('/&(#x(?:00)*[0-9a-f]{2}|#0*[12]?[0-9]{2}|amp|[lg]t|nbsp|quot)(?!;|\d)/i', '&\1;', $norm);
		if (! $norm ) {
			return $string;
		}
	}

	if ( preg_match('/\\\(?:0?[4-9][0-9]|1[0-7][0-9])/', $norm) ) {
		$norm = preg_replace_callback('/\\\(0?[4-9][0-9]|1[0-7][0-9])/', 'nfw_oct2ascii', $norm);
		if (! $norm ) {
			return $string;
		}
	}

	if ( preg_match('/\\\x[a-f0-9]{2}/i', $norm) ) {
		$norm = preg_replace_callback('/\\\x([a-f0-9]{2})/i', 'nfw_hex2ascii', $norm);
		if (! $norm ) {
			return $string;
		}
	}

	$norm = nfw_html_decode( $norm );
	if (! $norm ) {
		return $string;
	}

	if ( preg_match('/&#x?[0-9a-f]+;/i', $norm) ) {
		$norm = preg_replace('/(&#x?[0-9a-f]+;)/i', '', $norm);
		if (! $norm ) {
			return $string;
		}
	}

	if ( preg_match( '/(?:%|\\\)u(?:[0-9a-f]{4}|\{0*[0-9a-f]{2}\})/i', $norm ) ) {
		$norm = preg_replace_callback('/(?:%|\\\)u(?:([0-9a-f]{4})|\{0*([0-9a-f]{2})\})/i', 'nfw_udecode', $norm);
		if (! $norm ) {
			return $string;
		}
	}

	if ( empty( $nfw_rules[2]['ena'] ) ) {
		$norm = preg_replace('/\x0|%00/', '', $norm);
		if (! $norm ) {
			return $string;
		}
	}

	return $norm;
}

// =====================================================================

function nfw_html_decode( $norm ) {

	global $nfw_;

	$nfw_['entity_in'] = array (
		'&Tab;','&NewLine;','&excl;','&quot;','&QUOT;','&num;','&dollar;',
		'&percnt;','&amp;','&AMP;','&apos;','&lpar;','&rpar;','&ast;',
		'&midast;','&plus;','&comma;','&period;','&sol;','&colon;','&semi;',
		'&lt;','&LT;','&equals;','&gt;','&GT;','&quest;','&commat;','&lsqb;',
		'&lbrack;','&bsol;','&rsqb;','&rbrack;','&Hat;','&lowbar;','&grave;',
		'&DiacriticalGrave;','&lcub;','&lbrace;','&verbar;','&vert;','&VerticalLine;',
		'&rcub;','&rbrace;','&nbsp;','&NonBreakingSpace;','&nvlt;','&nvgt;',"\xa0"
	);

	$nfw_['entity_out'] = array (
		'','','!','"','"','#','$','%','&','&',"'",'(',')','*','*','+',',','.','/',
		':',';','<','<','=','>','>','?','@','[','[','\\',']',']','^','_','`','`',
		'{','{','|','|','|','}','}',' ',' ','','',' '
	);

	$normout = str_replace( $nfw_['entity_in'], $nfw_['entity_out'], $norm);
	$normout = html_entity_decode( $normout, ENT_QUOTES, 'UTF-8' );

	return $normout;

}

// =====================================================================

function nfw_compress_string( $string, $where = null ) {

	if (! $string ) { return; }

	if ( $where == 1 ) {
		$replace = ' ';
	} else {
		$replace = '';
	}

	$string = str_replace( ["\x09", "\x0a","\x0b", "\x0c", "\x0d"],
				$replace, $string);
	$string = trim ( preg_replace('/\x20{2,}/', ' ', $string) );
	return $string;

}

// =====================================================================

function nfw_transform_string( $string, $where ) {

	if (! $string ) { return; }

	if ( $where == 1 ) {
		$norm = trim( preg_replace_callback('((^([^a-z/&|#]*)|([\'"])(?:\\\\.|[^\n\3\\\\])*?\3|(?:[0-9a-z_$]+)|.)'.
			'(?:\s|--[^\n]*+\n|/\*(?:[^*!]|\*(?!/))*+\*/)*'.
			'(?:(?:\#|--(?:[\x00-\x20\x7f]|$)|/\*$)[^\n]*+\n|/\*!(?:\d{5})?|\*/|/\*(?:[^*!]|\*(?!/))*+\*/)*)si',
			'nfw_delcomments1',  $string . "\n") );
		$norm = preg_replace('/[\'"]\x20*\+?\x20*[\'"]/', '', $norm);
		$norm = strtolower( str_replace(	['+', "'", '"', "(", ')', '`', ',', ';'], ' ', $norm) );

	} elseif ( $where == 2 ) {
		$norm = trim( preg_replace_callback('((^|([\'"])(?:\\\\.|[^\n\2\\\\])*?\2|(?:[0-9a-z_$]+)|.)'.
			'(?://[^\n]*+\n|/\*(?:[^*]|\*(?!/))*+\*/)*)si',
			'nfw_delcomments2',  $string . "\n") );
		$norm = preg_replace(
			['/[\n\r\t\f\v]/', '`/\*\s*\*/`', '/[\'"`]\x20*[+.]?\x20*[\'"`]/'],
			['', ' ', ''],
			$norm
		);
	} elseif ( $where == 3 ) {
		$norm = preg_replace(
			['`([\\\"\'^]|\$\w+)`', '`([,;]|\s+)`'],
			['', ' '],
			$string
		);
		$norm = preg_replace(
			['`/(\./)+`','`/{2,}`', '`/(.+?)/\.\./\1\b`', '`\n`', '`\\\`'],
			['/', '/', '/\1', '', ''],
			$norm
		);
	}

	return $norm;

}

// =====================================================================

function nfw_delcomments1 ( $match ) {

	if (! empty($match[2]) ) { return ' '; }
	if ( $match[0] != $match[1] ) {
		return $match[1]. ' ';
	}
	return $match[1];

}

function nfw_delcomments2 ( $match ) {

	if ( $match[0] != $match[1] ) {
		return $match[1]. ' ';
	}
	return $match[1];

}

// ===================================================================== 2023-05-16

function nfw_udecode( $match ) {

	if ( isset( $match[2] ) ) {
		return @json_decode('"\\u00'.$match[2].'"');
	}
	return @json_decode('"\\u'.$match[1].'"');

}

// ===================================================================== 2023-05-16

function nfw_oct2ascii( $match ) {

	return chr( octdec( $match[1] ) );

}

// ===================================================================== 2023-05-16

function nfw_hex2ascii( $match ) {

	return chr( hexdec( $match[1] ) );

}

// ===================================================================== 2023-05-16
// Flatten an array.

function nfw_flatten( $glue, $pieces ) {

	if ( defined('NFW_STATUS') ) {
		return;
	}

	$ret = [];

   foreach ( $pieces as $r_pieces ) {
      if ( is_array( $r_pieces ) ) {
         $ret[] = nfw_flatten( $glue, $r_pieces );
      } else {
			if (! empty( $r_pieces ) ) {
				$ret[] = $r_pieces;
			}
      }
   }
   return implode( $glue, $ret );
}

// =====================================================================

function nfw_check_b64( $key, $string ) {

	if ( defined('NFW_STATUS') || strlen( $string ) < 4 ) {
		return;
	}

	$whitelist = [
		'fpd_print_order' // Fancy Product Designer
	];
	if ( in_array( $key, $whitelist ) ) {
		return;
	}

	$decoded = base64_decode( $string );
	if ( strlen($decoded) < 4 ) {
		return;
	}

	if ( preg_match( '`\b(?:\$?_(COOKIE|ENV|FILES|(?:GE|POS|REQUES)T|SE(RVER|SSION))|HTTP_(?:(?:POST|GET)_VARS|RAW_POST_DATA)|GLOBALS)\s*[=\[)]|\b(?i:array_map|assert|base64_(?:de|en)code|chmod|curl_exec|(?:ex|im)plode|error_reporting|eval|file(?:_get_contents)?|f(?:open|write|close)|fsockopen|function_exists|gzinflate|md5|move_uploaded_file|ob_start|passthru|[ep]reg_replace|phpinfo|stripslashes|strrev|(?:shell_)?exec|substr|system|unlink)\s*\(|[\s;]echo\s*[\'"]|<(?i:applet|embed|i?frame(?:set)?|marquee|object|script)\b|\W\$\{\s*[\'"]\w+[\'"]|<\?(?i:php|=)\s|(?i:(?:\b|\d)select\b.+?from\b.+?(?:\b|\d)where|(?:\b|\d)insert\b.+?into\b|(?:\b|\d)union\b.+?(?:\b|\d)select\b|(?:\b|\d)update\b.+?(?:\b|\d)set\b)|^.{0,25}[;{}]?\b[OC]:\d+:"[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*":\d+:{.*?}`', $decoded ) ) {
		// JetPack
		if ( $key === 'args' && ! defined('NFW_WPWAF') &&
			preg_match( '/^{"query":"SELECT/', $decoded ) &&
			strpos($_SERVER['SCRIPT_NAME'], '/jetpack-temp/jp-helper-') !== FALSE ) {
			return;
		}
		nfw_log('BASE64-encoded injection', 'POST:' . $key . ' = ' . $string, '3', 0 );
		nfw_block();
	}
}

// =====================================================================

function nfw_sanitise( $str, $how, $msg ) {

	if ( defined('NFW_STATUS') ) { return; }

	if ( empty($str) ) { return $str; }

	global $nfw_;

	if (is_string($str) ) {

		if ($how == 1) {
			// Full WAF
			if (! empty( $nfw_['mysqli'] ) ) {
				$str2 = $nfw_['mysqli']->real_escape_string($str);
			// WP WAF
			} else {
				global $wpdb;
				$str2 = @$wpdb->_real_escape($str);
			}
			$str2 = str_replace(	['`', '<', '>'], ['\\`', '&lt;', '&gt;'],	$str2);
			if ( $msg == 'GET' && strpos( $str2, '/') !== false ) {
				$str2 = str_replace( ['*', '?'], ['\*', '\?'], $str2 );
			}
		} elseif ($how == 2) {
			$str2 = str_replace(	['\\', "'", '"', "\x0d", "\x0a", "\x00", "\x1a", '`', '<', '>'],
				['\\\\', "\\'", '\\"', '-', '-', '-', '-', '\\`', '&lt;', '&gt;'],	$str);
		} else {
			$str2 = str_replace(	['\\', "'", "\x00", "\x1a", '`', '<'],
				['\\\\', "\\'", '-', '-', '\\`', '&lt;'],	$str);
		}
		if (! empty($nfw_['nfw_options']['debug']) ) {
			if ($str2 != $str) {
				nfw_log('Sanitising user input', $msg . ': ' . $str, 7, 0); // '7' for debugging mode only
			}
			return $str;
		}
		if ($str2 != $str) {
			nfw_log('Sanitising user input', $msg . ': ' . $str, 6, 0);
		}
		return $str2;

	} else if (is_array($str) ) {
		foreach($str as $key => $value) {
			if ($how == 3) {
				$key2 = str_replace(	['\\', "'", "\x00", "\x1a", '`', '<', '>'],
					['\\\\', "\\'", '-', '-', '\\`', '&lt;', '&gt;'],	$key, $occ);
			} else {
				$key2 = str_replace(	['\\', "'", '"', "\x0d", "\x0a", "\x00", "\x1a", '`', '<', '>'],
					['\\\\', "\\'", '\\"', '-', '-', '-', '-', '&#96;', '&lt;', '&gt;'],	$key, $occ);
			}
			if ($occ) {
				unset($str[$key]);
				nfw_log('Sanitising user input', $msg . ': ' . $key, 6, 0);
			}
			$str[$key2] = nfw_sanitise($value, $how, $msg);
		}
		return $str;
	}
}

// ===================================================================== 2023-05-16
// Block the user and display a message.

function nfw_block() {

	if ( defined('NFW_STATUS') ) {
		return;
	}

	global $nfw_;

	if (! empty( $nfw_['nfw_options']['debug'] ) ) {
		return;
	}

	$http_codes = [
      400 => '400 Bad Request',
      403 => '403 Forbidden',
      404 => '404 Not Found',
      406 => '406 Not Acceptable',
      418 => "418 I'm a teapot",
      500 => '500 Internal Server Error',
      503 => '503 Service Unavailable'
   ];
   if (! isset( $http_codes[$nfw_['nfw_options']['ret_code']] ) ) {
		$nfw_['nfw_options']['ret_code'] = 403;
	}

	if ( empty( $nfw_['num_incident'] ) ) {
		$nfw_['num_incident'] = '000000';
	}

	$tmp = str_replace(
		'%%NUM_INCIDENT%%',
		$nfw_['num_incident'],
		base64_decode( $nfw_['nfw_options']['blocked_msg'] )
	);

	if ( isset( $nfw_['nfw_options']['logo'] ) ) {
		$tmp = str_replace(
			'%%NINJA_LOGO%%',
			"<img alt='NinjaFirewall' src='{$nfw_['nfw_options']['logo']}' />",
			$tmp
		);
	}

	$tmp = str_replace('%%REM_ADDRESS%%', NFW_REMOTE_ADDR, $tmp );

	NinjaFirewall_session::delete();

	if (! headers_sent() ) {
		header("HTTP/1.1 {$http_codes[$nfw_['nfw_options']['ret_code']]}" );
		header("Status: {$http_codes[$nfw_['nfw_options']['ret_code']]}" );
		header('Pragma: no-cache');
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Expires: 0');
	}

	echo "<!DOCTYPE HTML PUBLIC '-//IETF//DTD HTML 2.0//EN'><html><head>".
		"<title>NinjaFirewall {$http_codes[$nfw_['nfw_options']['ret_code']]}</title>".
		"<style>body{font-family:sans-serif;font-size:13px;color:#000;}</style>".
		"<meta http-equiv='Content-Type' content='text/html; charset=utf-8'></head>".
		"<body bgcolor='white'>$tmp</body></html>";
	exit;
}

// =====================================================================

function nfw_log($loginfo, $logdata, $loglevel, $ruleid) {

	if ( defined('NFW_STATUS') ) { return; }

	global $nfw_;

	$nfw_['num_incident'] = mt_rand(1000000, 9000000);

	if ( $loglevel == 6) {
		$http_ret_code = '200';
	} else {
		if (! empty($nfw_['nfw_options']['debug']) ) {
			$loglevel = 7;
			$http_ret_code = '200';
		} else {
			$http_ret_code = $nfw_['nfw_options']['ret_code'];
		}
	}

	if ( defined('NFW_MAXPAYLOAD') ) {
		$NFW_MAXPAYLOAD = (int) NFW_MAXPAYLOAD;
	} else {
		$NFW_MAXPAYLOAD = 200;
	}
   if (strlen($logdata) > $NFW_MAXPAYLOAD ) {
		$logdata = mb_substr($logdata, 0, $NFW_MAXPAYLOAD , 'utf-8') . '...';
	}
	$res = '';
	$string = str_split($logdata);
	foreach ( $string as $char ) {
		if ( ord($char) < 32 || ord($char) > 126 ) {
			$res .= '%' . bin2hex($char);
		} else {
			$res .= $char;
		}
	}

	$cur_month = date('Y-m');

	$stat_file = $nfw_['log_dir']. '/stats_' . $cur_month . '.php';
	$log_file = $nfw_['log_dir']. '/firewall_' . $cur_month . '.php';

	if ( is_file( $stat_file ) ) {
		$nfw_stat = file_get_contents( $stat_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
		$nfw_stat = str_replace( '<?php exit; ?>', '', $nfw_stat );
	} else {
		$nfw_stat = '0:0:0:0:0:0:0:0:0:0';
	}
	$nfw_stat_arr = explode(':', $nfw_stat . ':');
	++$nfw_stat_arr[$loglevel];

	@file_put_contents(
		$stat_file,
		"<?php exit; ?>{$nfw_stat_arr[0]}:{$nfw_stat_arr[1]}:" .
		"{$nfw_stat_arr[2]}:{$nfw_stat_arr[3]}:{$nfw_stat_arr[4]}:" .
		"{$nfw_stat_arr[5]}:{$nfw_stat_arr[6]}:{$nfw_stat_arr[7]}:" .
		"{$nfw_stat_arr[8]}:{$nfw_stat_arr[9]}",
		LOCK_EX
	);

	if (! is_file( $log_file ) ) {
		$tmp = '<?php exit; ?>' . "\n";
	} else {
		$tmp = '';
	}

	if (! defined('NFW_REMOTE_ADDR') ) { define('NFW_REMOTE_ADDR', $_SERVER['REMOTE_ADDR']); }

	// Which encoding to use?
	if ( defined('NFW_LOG_ENCODING') ) {
		if ( NFW_LOG_ENCODING == 'b64') {
			$encoding = '[b64:'. base64_encode( $res ) .']';
		} elseif ( NFW_LOG_ENCODING == 'none' ) {
			$encoding = '['. $res .']';
		} else {
			$unp = unpack('H*', $res);
			$encoding = '[hex:'. array_shift( $unp ) .']';
		}
	} else {
		$unp = unpack('H*', $res);
		$encoding = '[hex:'. array_shift( $unp ) .']';
	}

	$elapse = nfw_fc_metrics('stop', $nfw_['fw_starttime']);
	@file_put_contents( $log_file,
		$tmp . '[' . time() . '] ' . '[' . $elapse .'] '.
      '[' . $_SERVER['SERVER_NAME'] . '] ' . '[#' . $nfw_['num_incident'] . '] ' .
      '[' . $ruleid . '] ' .
      '[' . $loglevel . '] ' . '[' . nfw_anonymize_ip() . '] ' .
      '[' . $http_ret_code . '] ' . '[' . $_SERVER['REQUEST_METHOD'] . '] ' .
      '[' . $_SERVER['SCRIPT_NAME'] . '] ' . '[' . $loginfo . '] ' .
      $encoding . "\n", FILE_APPEND | LOCK_EX );
}

// ===================================================================== 2023-05-16
// Return the time using hrtime (PHP >= 7.3) or microtime.

function nfw_fc_metrics( $action = 'start', $starttime = 0 ) {

	if ( function_exists('hrtime') ) {
		$metrics = 'hrtime';
	} else {
		$metrics = 'microtime';
	}

	// Start the chrono
	if ( $action == 'start') {
		return $metrics(true);
	}

	// Stop the chrono and return the formatted elapsed time
	if ( $metrics == 'hrtime') {
		return number_format( ( $metrics(true) - $starttime ) / 1000000000, 5 );
	} else {
		return number_format( $metrics(true) - $starttime, 5 );
	}
}

// ===================================================================== 2023-05-16
// Anonymize an IP address by hidding it last 3 characters.

function nfw_anonymize_ip() {

	global $nfw_;

	if (! empty( $nfw_['nfw_options']['anon_ip'] ) &&
		NFW_REMOTE_ADDR_PRIVATE === false ) {

		return substr( NFW_REMOTE_ADDR, 0, -3 ) .'xxx';
	}

	return NFW_REMOTE_ADDR;
}

// =====================================================================

function nfw_bfd($where) {

	if ( defined('NFW_STATUS') ) { return; }

	global $nfw_;
	$bf_conf_dir = $nfw_['log_dir'] . '/cache';

	if (! is_file($bf_conf_dir . '/bf_conf.php') ) {
		return;
	}

	$now = time();
	require($bf_conf_dir . '/bf_conf.php');
	if ( empty($bf_enable) ) {
		return;
	}

	if ( $where == 2 && empty($bf_xmlrpc) ) {
		return;
	}

	// NinjaFirewall <= 3.4.2:
	if (! isset( $auth_msgtxt ) ) {
		$auth_msgtxt = $auth_msg;
		$b64 = 0;
	// NinjaFirewall > 3.4.2:
	} else {
		$b64 = 1;
	}
	// NinjaFirewall < 3.5:
	if (! isset( $bf_allow_bot ) ) {
		$bf_allow_bot = 0;
	}
	if (! isset( $bf_type ) ) {
		$bf_type = 0;
	}

	if ( $where == 1 && $bf_allow_bot == 0 ) {
		nfw_is_bot( 'wp-login.php' );
	}

	if ( $where == 1 && isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], ['postpass', 'logout', 'lostpassword', 'retrievepassword', 'resetpass', 'rp', 'register', 'confirmaction'] ) ) {
		return;
	}

	if ( $bf_enable == 2 ) {
		nfw_check_auth($auth_name, $auth_pass, $auth_msgtxt, $bf_rand, $b64, $bf_allow_bot, $bf_type, $captcha_text, $bf_nosig);
		return;
	}


	if ( is_file($bf_conf_dir . '/bf_blocked' . $where . $_SERVER['SERVER_NAME'] . $bf_rand) ) {

		$mtime = filemtime( $bf_conf_dir . '/bf_blocked' . $where . $_SERVER['SERVER_NAME'] . $bf_rand );
		if ( ($now - $mtime) < $bf_bantime * 60 ) {

			nfw_check_auth($auth_name, $auth_pass, $auth_msgtxt, $bf_rand, $b64, $bf_allow_bot, $bf_type, $captcha_text, $bf_nosig);
			return;
		} else {

			@unlink($bf_conf_dir . '/bf_blocked' . $where . $_SERVER['SERVER_NAME'] . $bf_rand);
		}
	}


	if ( strpos($bf_request, $_SERVER['REQUEST_METHOD']) === false ) {
		return;
	}


	if ( is_file($bf_conf_dir . '/bf_' . $where . $_SERVER['SERVER_NAME'] . $bf_rand ) ) {
		$tmp_log = file( $bf_conf_dir . '/bf_' . $where . $_SERVER['SERVER_NAME'] . $bf_rand, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if ( count( $tmp_log) >= $bf_attempt ) {
			if ( ($tmp_log[count($tmp_log) - 1] - $tmp_log[count($tmp_log) - $bf_attempt]) <= $bf_maxtime ) {

				$bfdh = fopen( $bf_conf_dir . '/bf_blocked' . $where . $_SERVER['SERVER_NAME'] . $bf_rand, 'w');
				fclose( $bfdh );

				unlink( $bf_conf_dir . '/bf_' . $where . $_SERVER['SERVER_NAME'] . $bf_rand );
				$nfw_['nfw_options']['ret_code'] = '401';
				if ($where == 1) {
					$where = 'wp-login.php';
				} else {
					$where = 'XML-RPC API';
				}
				nfw_log('Brute-force attack detected on ' . $where, 'enabling HTTP authentication for ' . $bf_bantime . 'mn', 3, 0);
				if (! empty($bf_authlog) ) {
					if (defined('LOG_AUTHPRIV') ) { $tmp = LOG_AUTHPRIV; }
					else { $tmp = LOG_AUTH;	}
					@openlog('ninjafirewall', LOG_NDELAY|LOG_PID, $tmp);
					@syslog(LOG_INFO, 'Possible brute-force attack from '. $_SERVER['REMOTE_ADDR'] .
							' on '. $_SERVER['SERVER_NAME'] .' ('. $where .'). Blocking access for ' . $bf_bantime . 'mn.');
					@closelog();
				}
				nfw_check_auth($auth_name, $auth_pass, $auth_msgtxt, $bf_rand, $b64, $bf_allow_bot, $bf_type, $captcha_text, $bf_nosig);
				return;

			}
		}
		$mtime = filemtime( $bf_conf_dir . '/bf_' . $where . $_SERVER['SERVER_NAME'] . $bf_rand );
		if ( ($now - $mtime) > $bf_bantime * 60 ) {
			unlink( $bf_conf_dir . '/bf_' . $where . $_SERVER['SERVER_NAME'] . $bf_rand );
		}
	}

	@file_put_contents($bf_conf_dir . '/bf_' . $where . $_SERVER['SERVER_NAME'] . $bf_rand, $now . "\n", FILE_APPEND | LOCK_EX);

}

// ===================================================================== 2023-05-16
// Block the request if a bot is detected.

function nfw_is_bot( $block = '') {

	global $nfw_;

	if ( empty( $_SERVER['HTTP_ACCEPT'] ) ||
		empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ||
		empty( $_SERVER['HTTP_USER_AGENT'] ) ||
		stripos( $_SERVER['HTTP_USER_AGENT'], 'Mozilla') === FALSE ) {

		if (! empty( $block ) ) {
			// Whitelist server IP and private addresses calling admin-ajax.php
			if ( $block == 'admin-ajax.php') {
				if ( NFW_REMOTE_ADDR == $_SERVER['SERVER_ADDR'] ||
					NFW_REMOTE_ADDR_PRIVATE == true ) {

					return true;
				}
				$block = 'Blocked access to admin-ajax.php';

			// No whitelist needed for the login page:
			} else {
				$block = 'Blocked access to the login page';
			}

			header('HTTP/1.0 404 Not Found');
			header('Pragma: no-cache');
			header('Cache-Control: no-cache, no-store, must-revalidate');
			header('Expires: 0');
			$nfw_['nfw_options']['ret_code'] = '404';
			nfw_log( $block, 'bot detection is enabled', 1, 0 );
			NinjaFirewall_session::delete();
			exit('404 Not Found');
		}

		return true;
	}
	return false;
}

// =====================================================================

function nfw_check_auth( $auth_name, $auth_pass, $auth_msgtxt, $bf_rand, $b64, $bf_allow_bot, $bf_type, $captcha_text, $bf_nosig ) {

	if ( defined('NFW_STATUS') ) { return; }

	// Prevent favicon.ico 302 redirection to the login page
	// due to plugins that do not handle well the login page access:
	if ( isset( $_GET['redirect_to'] ) && strpos( $_GET['redirect_to'], 'favicon.ico' ) !== FALSE ) {
		exit;
	}

	NinjaFirewall_session::start();

	global $nfw_;

	$nfw_bfd = NinjaFirewall_session::read('nfw_bfd');
	if ( isset( $nfw_bfd ) && $nfw_bfd == $bf_rand ) {
		return;
	}

	if ( $bf_type == 0 ) {
		// Password protection:
		if (! empty($_REQUEST['u']) && ! empty($_REQUEST['p']) ) {
			if ( $_REQUEST['u'] === $auth_name && sha1($_REQUEST['p']) === $auth_pass ) {
				NinjaFirewall_session::write( ['nfw_bfd' => $bf_rand ] );
				return;
			}
		}
	} else {
		// Make sure the GD extension is loaded
		if ( function_exists( 'gd_info' ) ) {
			// Captcha protection
			$nfw_bfd_c = NinjaFirewall_session::read('nfw_bfd_c');
			if (! empty( $_REQUEST['c'] ) && isset( $nfw_bfd_c ) ) {
				if ( $nfw_bfd_c == strtolower( $_REQUEST['c'] ) ) {
					NinjaFirewall_session::write( ['nfw_bfd' => $bf_rand ] );
					NinjaFirewall_session::delete('nfw_bfd_c');
					return;
				}
			}
		} else {
			// Return in no GD extension:
			return;
		}
	}

	NinjaFirewall_session::delete();

	if ( $b64 ) { $auth_msgtxt = base64_decode( $auth_msgtxt ); }

	header('HTTP/1.0 401 Unauthorized');
	header('X-Frame-Options: SAMEORIGIN');
	header('Pragma: no-cache');
	header('Cache-Control: no-cache, no-store, must-revalidate');
	header('Expires: 0');
	if ( empty( $bf_nosig ) ) {
		$bf_nosig = 'Brute-force protection by NinjaFirewall';
	} else {
		$bf_nosig = '';
	}
	if ( $bf_type == 0 ) {
		$message = '<html><head><title>'. $bf_nosig  .'</title><link rel="stylesheet" href="./wp-includes/css/buttons.min.css" type="text/css"><link rel="stylesheet" href="./wp-admin/css/login.min.css" type="text/css"><link rel="stylesheet" href="./wp-admin/css/forms.min.css" type="text/css"><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body class="login wp-core-ui" style="color:#444"><div id="login"><center><h2>' . $auth_msgtxt . '</h2><form method="post"><label>'. $bf_nosig  .'</label><br><br><p><input class="input" type="text" name="u" placeholder="Username"></p><p><input class="input" type="password" name="p" placeholder="Password"></p><p align="right"><input type="submit" value="Login Page&nbsp;&#187;" class="button-secondary"></p><input type="hidden" name="reauth" value="1"></form></center></div></body></html>';
	} else {
		$captcha = nfw_get_captcha();
		if ( $captcha === false ) {
			return;
		}
		$message = '<html><head><title>'. $bf_nosig  .'</title><link rel="stylesheet" href="./wp-includes/css/buttons.min.css" type="text/css"><link rel="stylesheet" href="./wp-admin/css/login.min.css" type="text/css"><link rel="stylesheet" href="./wp-admin/css/forms.min.css" type="text/css"><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body class="login wp-core-ui" style="color:#444"><div id="login"><center><form method="post"><p><label>'. base64_decode( $captcha_text ) .'</label></p><br><p>' . $captcha . '</p><p><input class="input" type="text" name="c" autofocus></p><p align="right"><input type="submit" value="Login Page&nbsp;&#187;" class="button-secondary"></p><input type="hidden" name="reauth" value="1"></form><br><label>'. $bf_nosig  .'</label></center></div></body></html>';
	}
	if ( $bf_allow_bot == 0 ) {
		if ( @ini_set('zlib.output_compression','Off') !== false ) {
			header('Content-Encoding: gzip');
			echo gzencode( $message, 1 );
			exit;
		}
	}
	header('Content-Type: text/html; charset=utf-8');
	echo $message;
	exit;
}

// =====================================================================
function nfw_get_captcha() {

	if (! function_exists( 'imagettftext' ) ) {
		echo "<div id='login_error'>NinjaFirewall error: PHP imagettftext() function doesn't exist, the captcha can't be displayed. Make sure PHP is compiled with freetype support (--with-freetype-dir=DIR).</div>";
		return false;
	}

	NinjaFirewall_session::start();

	$characters  = 'AaBbCcDdEeFfGgHhiIJjKkLMmNnPpRrSsTtUuVvWwXxYyZz123456789';
	$captcha = '';
	while( strlen( $captcha ) < 5 ) {
		$captcha .= substr( $characters, mt_rand() % strlen( $characters ), 1 );
	}

	// Background image with dimensions
	$image = imagecreate( 200, 60 );
	// Background color:
	imagecolorallocate( $image, 255, 255, 255 );
	// Text color:
	$text_color = imagecolorallocate( $image, 77, 77, 77 );
	// Font:
	global $nfw_;
	if ( is_file( "{$nfw_['log_dir']}/font.ttf" ) ) {
		imagettftext( $image, 35, 0, 15, 45, $text_color, "{$nfw_['log_dir']}/font.ttf", $captcha );
	} else {
		imagettftext( $image, 35, 0, 15, 45, $text_color, __DIR__ . '/share/font.ttf', $captcha );
	}

	ob_start();
	imagepng( $image );
	$img_content = ob_get_contents();
	ob_end_clean();
	imagedestroy( $image );

	$res = '<img src="data:image/png;base64,'. base64_encode( $img_content ) .'" />';

	NinjaFirewall_session::write( ['nfw_bfd_c' => strtolower( $captcha ) ] );

	return $res;
}

// ===================================================================== 2023-05-16
// From WP db_connect()

function nfw_check_dbhost() {

	global $nfw_;

	$nfw_['port']		= null;
	$nfw_['socket']	= null;
	$port_or_socket	= strstr( $nfw_['DB_HOST'], ':');
	if ( ! empty( $port_or_socket ) ) {
		$nfw_['DB_HOST']	= substr( $nfw_['DB_HOST'], 0, strpos( $nfw_['DB_HOST'], ':') );
		$port_or_socket	= substr( $port_or_socket, 1 );
		if ( 0 !== strpos( $port_or_socket, '/') ) {
			$nfw_['port']	= intval( $port_or_socket );
			$maybe_socket	= strstr( $port_or_socket, ':');
			if ( ! empty( $maybe_socket ) ) {
				$nfw_['socket'] = substr( $maybe_socket, 1 );
			}
		} else {
			$nfw_['socket'] = $port_or_socket;
		}
	}
}

// ===================================================================== 2023-05-16
// Handle HTTP response headers.

function nfw_response_headers() {

	if ( defined('NFW_CUSTHEADERS') ) {
		nfw_custom_headers();
	}

	if (! defined('NFW_RESHEADERS') ) {
		return;
	}

	$NFW_RESHEADERS = NFW_RESHEADERS;
	// NFW_RESHEADERS:
	// 0000000000
	// ||||||||||_ SameSite[0-2]
	// |||||||||__ Referrer-Policy [0-8]
	// ||||||||___ Content-Security-Policy (backend) [0-1]
	// |||||||____ Content-Security-Policy (frontend) [0-1]
	// ||||||_____ Strict-Transport-Security (includeSubDomains) [0-1]
	// |||||______ Strict-Transport-Security [0-4]
	// ||||_______ X-XSS-Protection [0-3]
	// |||________ X-Frame-Options [0-2]
	// ||_________ X-Content-Type-Options [0-1]
	// |__________ HttpOnly cookies [0-1]

	// Force HttpOnly and/or SameSite cookie
	if (! empty( $NFW_RESHEADERS[0] ) || ! empty( $NFW_RESHEADERS[9] ) ) {
		$rewrite = [];
		// Parse all response headers
		foreach (headers_list() as $header) {
			// Ignore it if it is not a cookie
			if ( strpos( $header, 'Set-Cookie:' ) === false ) { continue; }
			$extra = '';
			// HttpOnly
			if (! empty( $NFW_RESHEADERS[0] ) ) {
				// Does it have the HttpOnly flag on
				if ( stripos( $header, '; HttpOnly') === false) {
					$extra .= '; HttpOnly';
				}
			}
			// SameSite
			if (! empty( $NFW_RESHEADERS[9] ) ) {
				// Lax
				if ( $NFW_RESHEADERS[9] == 1
					&& stripos( $header, '; SameSite=Lax' ) === false ) {

					$extra .= '; SameSite=Lax';
				// Strict
				} elseif ( $NFW_RESHEADERS[9] == 2
					&& stripos( $header, '; SameSite=Strict' ) === false ) {

					$extra .= '; SameSite=Strict';
				}
			}
			// Save cookie
			$rewrite[] = "{$header}{$extra}";
		}

		// Shall we rewrite cookies
		if (! empty( $rewrite ) ) {
			// Remove all original cookies
			header_remove('Set-Cookie');
			foreach( $rewrite as $cookie ) {
				// Inject ours instead
				header( $cookie, false );
			}
		}
	}

	if (! empty( $NFW_RESHEADERS[1] ) ) {
		header('X-Content-Type-Options: nosniff');
	}

	if (! empty( $NFW_RESHEADERS[2] ) ) {
		if ($NFW_RESHEADERS[2] == 1) {
			header('X-Frame-Options: SAMEORIGIN');
		} else {
			header('X-Frame-Options: DENY');
		}
	}

	if ( empty( $NFW_RESHEADERS[3] ) ) {
		header('X-XSS-Protection: 0');
	} elseif ( $NFW_RESHEADERS[3] == 1 ) {
		header('X-XSS-Protection: 1; mode=block');
	} elseif ( $NFW_RESHEADERS[3] == 2 ) {
		header('X-XSS-Protection: 1');
	}

	if (! empty( $NFW_RESHEADERS[6] ) &&
		strpos($_SERVER['SCRIPT_NAME'], '/wp-admin/') === FALSE ) {

		header('Content-Security-Policy: ' . CSP_FRONTEND_DATA);
	}
	if (! empty( $NFW_RESHEADERS[7] ) &&
		strpos($_SERVER['SCRIPT_NAME'], '/wp-admin/') !== FALSE ) {

		header('Content-Security-Policy: ' . CSP_BACKEND_DATA);
	}

	if (! empty( $NFW_RESHEADERS[8] ) ) {
		if ( $NFW_RESHEADERS[8] == 1 ) {
			$rf = 'no-referrer';
		} elseif ( $NFW_RESHEADERS[8] == 2 ) {
			$rf = 'no-referrer-when-downgrade';
		} elseif ( $NFW_RESHEADERS[8] == 3 ) {
			$rf = 'origin';
		} elseif ( $NFW_RESHEADERS[8] == 4 ) {
			$rf = 'origin-when-cross-origin';
		} elseif ( $NFW_RESHEADERS[8] == 5 ) {
			$rf = 'strict-origin';
		} elseif ( $NFW_RESHEADERS[8] == 6 ) {
			$rf = 'strict-origin-when-cross-origin';
		} elseif ( $NFW_RESHEADERS[8] == 7 ) {
			$rf = 'same-origin';
		} else {
			$rf = 'unsafe-url';
		}
		header("Referrer-Policy: $rf");
	}

	// Stop here if no more headers
	if ( empty($NFW_RESHEADERS[4] ) ) {
		return;
	}

	// We don't send HSTS headers over HTTP
	if (! defined('NFW_IS_HTTPS') ) {
		nfw_is_https();
	}
	if ( NFW_IS_HTTPS == false ) {
		return;
	}

	if ($NFW_RESHEADERS[4] == 1) {
		// 1 month
		$max_age = 'max-age=2628000';
	} elseif ($NFW_RESHEADERS[4] == 2) {
		// 6 months
		$max_age = 'max-age=15768000';
	} elseif ($NFW_RESHEADERS[4] == 3) {
		// 12 months
		$max_age = 'max-age=31536000';
	} elseif ($NFW_RESHEADERS[4] == 4) {
		// Send an empty max-age to signal the UA to
		// cease regarding the host as a known HSTS Host
		$max_age = 'max-age=0';
	} else {
		// 24 months
		$max_age = 'max-age=63072000';
	}
	if (! empty( $NFW_RESHEADERS[5] ) ) {
		if ( $NFW_RESHEADERS[5] == 1 ) {
			$max_age .= '; includeSubDomains';
		} elseif ( $NFW_RESHEADERS[5] == 2 ) {
				$max_age .= '; preload';
		} else {
			$max_age .= '; includeSubDomains; preload';
		}
	}
	header('Strict-Transport-Security: '. $max_age);
}

// ===================================================================== 2023-05-16

function nfw_custom_headers() {

	$headers = json_decode( NFW_CUSTHEADERS, true );
	if (! empty( $headers ) ) {
		foreach( $headers as $key => $value ) {
			header( "$key: $value" );
		}
	}
}

// =====================================================================
// EOF
