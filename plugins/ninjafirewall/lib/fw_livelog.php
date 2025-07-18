<?php
/*
 +---------------------------------------------------------------------+
 | NinjaFirewall (WP Edition)                                          |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
 +---------------------------------------------------------------------+
 | This program is free software: you can redistribute it and/or       |
 | modify it under the terms of the GNU General Public License as      |
 | published by the Free Software Foundation, either version 3 of      |
 | the License, or (at your option) any later version.                 |
 |                                                                     |
 | This program is distributed in the hope that it will be useful,     |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of      |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the       |
 | GNU General Public License for more details.                        |
 +---------------------------------------------------------------------+ i18n+ / sa
*/

if (! isset( $nfw_['nfw_options']['enabled']) ) {
	header('HTTP/1.1 404 Not Found');
	header('Status: 404 Not Found');
	exit;
}

/* ------------------------------------------------------------------ */

function fw_livelog_show() {

	global $nfw_;

	$nfw_['livelog'] = $nfw_['log_dir'] . '/cache/livelog.php';
	if ( is_file( $nfw_['livelog'] ) ) {
		// Check if we need to flush it :
		if ($_POST['livecls'] > 0) {
			@file_put_contents( $nfw_['livelog'], '<?php exit; ?>', LOCK_EX);
		}
		$count = 0;
		$buffer = '';
		if ( $fh = fopen($nfw_['livelog'], 'r' ) ) {
			while (! feof($fh) ) {
				if ( $count >= $_POST['lines'] ) {
					$buffer .= fgets($fh);
				} else {
					fgets($fh);
				}
				++$count;
			}
			fclose($fh);
		}

		// Return the log content :
		header('HTTP/1.0 200 OK');
		if ( $buffer ) {
			echo '^'.$buffer;
		} else {
			echo '*';
		}
		touch($nfw_['log_dir'] .'/cache/livelogrun.php');
	} else {
		// Something went wrong :
		header('HTTP/1.0 503 Service Unavailable');
	}
	exit;
}

/* ------------------------------------------------------------------ */
function fw_livelog_record() {

	global $nfw_;

	$nfw_['mtime']	= filemtime($nfw_['log_dir'] .'/cache/livelogrun.php');
	$now = time();

	// If the file was not accessed for more than 100s, we assume
	// the admin has stopped using live log from WordPress
	// dashboard (max refresh rate is 45s) :
	if ( $now - $nfw_['mtime'] > 100 ) {
		unlink($nfw_['log_dir'] .'/cache/livelogrun.php');
		// If the log was not modified for the past 10mn, we delete it as well :
		$nfw_['livelog'] = $nfw_['log_dir'] . '/cache/livelog.php';
		if ( is_file( $nfw_['livelog'] ) ) {
			$nfw_['mtime'] = filemtime($nfw_['livelog']);
			if ( $now - $nfw_['mtime'] > 600 ) {
				unlink( $nfw_['livelog'] );
			}
		}
	} else {

		// Check if we are supposed to log the request (http/https) :
		if ( empty($nfw_['nfw_options']['liveport']) ||
			($nfw_['nfw_options']['liveport'] == 1 && NFW_IS_HTTPS == false) ||
			($nfw_['nfw_options']['liveport'] == 2 && NFW_IS_HTTPS == true) ) {

			// Inclusion and exclusion rules:
			if (! empty( $nfw_['nfw_options']['liverules'] ) && ! empty( $nfw_['nfw_options']['liverulespath'] ) ) {
				$liverulespath = preg_quote( $nfw_['nfw_options']['liverulespath'], '/' );
				$liverulespath = str_replace(',', '|', $liverulespath);

				// Must include:
				if ( $nfw_['nfw_options']['liverules'] == 1 ) {
					if (! preg_match("/$liverulespath/", $_SERVER['REQUEST_URI']) ) { return; }
				// Must not include:
				} else {
					if ( preg_match("/$liverulespath/", $_SERVER['REQUEST_URI']) ) { return; }
				}
			}

			if ( empty($_SERVER['PHP_AUTH_USER']) ) { $PHP_AUTH_USER = '-'; }
			else { $PHP_AUTH_USER = $_SERVER['PHP_AUTH_USER']; }
			if ( empty($_SERVER['HTTP_REFERER']) ) { $HTTP_REFERER = '-'; }
			else { $HTTP_REFERER = $_SERVER['HTTP_REFERER']; }
			if ( empty($_SERVER['HTTP_USER_AGENT']) ) {	$HTTP_USER_AGENT = '-'; }
			else { $HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT']; }
			if ( empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) { $HTTP_X_FORWARDED_FOR = '-'; }
			else { $HTTP_X_FORWARDED_FOR = $_SERVER['HTTP_X_FORWARDED_FOR']; }
			if ( empty($_SERVER['HTTP_HOST']) ) { $HTTP_HOST = '-'; }
			else { $HTTP_HOST = $_SERVER['HTTP_HOST']; }

			// Set the user-defined timezone
			if (! empty($nfw_['nfw_options']['livetz']) ) {
				@date_default_timezone_set($nfw_['nfw_options']['livetz']);
			}

			// Log the request :
			if (! empty($nfw_['nfw_options']['liveformat']) ) {
				// User-defined format :
				$nfw_['tmp'] = str_replace(
					array( '%time', '%name', '%client', '%method', '%uri', '%referrer', '%ua', '%forward', '%host' ),
					array( date('d/M/y:H:i:s O', time()), $PHP_AUTH_USER, $_SERVER["REMOTE_ADDR"], $_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"], $HTTP_REFERER, $HTTP_USER_AGENT, $HTTP_X_FORWARDED_FOR, $HTTP_HOST ), $nfw_['nfw_options']['liveformat']	);
				@file_put_contents( $nfw_['log_dir'] . '/cache/livelog.php', htmlentities($nfw_['tmp'], ENT_NOQUOTES) ."\n", FILE_APPEND | LOCK_EX);
			} else {
				// Default format :
				@file_put_contents( $nfw_['log_dir'] . '/cache/livelog.php',
				'['. @date('d/M/y:H:i:s O', time()) .'] '.	htmlentities(
				$PHP_AUTH_USER .' '.	$_SERVER['REMOTE_ADDR'] .' "'. $_SERVER['REQUEST_METHOD'] .' '.
				$_SERVER['REQUEST_URI'] .'" "'. $HTTP_REFERER .'" "'. $HTTP_USER_AGENT .'" "'.
				$HTTP_X_FORWARDED_FOR .'" "'. $HTTP_HOST, ENT_NOQUOTES) ."\"\n", FILE_APPEND | LOCK_EX);
			}
		}
	}
}
/* ------------------------------------------------------------------ */
// EOF
