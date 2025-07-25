<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$np_basePath = dirname( __FILE__ ) . '/';
require_once $np_basePath . 'nitropack-sdk/autoload.php';
require_once $np_basePath . 'constants.php';

$np_originalRequestCookies = $_COOKIE;
$np_customExpirationTimes = array();
$np_queriedObj = NULL;
$np_loggedPurges = array();
$np_loggedInvalidations = array();
$np_integrationSetupEvent = "muplugins_loaded";

function nitropack_is_logged_in() {
	$nitro = get_nitropack_sdk();
	$useAccountOverride = $nitro !== NULL && $nitro->isStatefulCacheSatisfied( "account" );
	if ( $useAccountOverride ) {
		return false;
	}

	$loginCookies = array( defined( 'NITROPACK_LOGGED_IN_COOKIE' ) ? NITROPACK_LOGGED_IN_COOKIE : ( defined( 'LOGGED_IN_COOKIE' ) ? LOGGED_IN_COOKIE : '' ) );
	foreach ( $loginCookies as $loginCookie ) {
		if ( ! empty( $_COOKIE[ $loginCookie ] ) ) {
			$parts = explode( '|', urldecode( $_COOKIE[ $loginCookie ] ) );
			if ( count( $parts ) < 3 ) {
				continue; // Invalid cookie
			}

			return time() <= (int) $parts[1];
		}
	}
	$cookieStr = implode( "|", array_keys( $_COOKIE ) );
	return strpos( $cookieStr, "wordpress_logged_in_" ) !== false;
}

function nitropack_passes_cookie_requirements() {
	$isUserLoggedIn = nitropack_is_logged_in();
	$cookieStr = implode( "|", array_keys( $_COOKIE ) );
	$safeCookie = (
		( strpos( $cookieStr, "comment_author" ) === false || ! ! get_nitropack()->setDisabledReason( "comment author" ) )
		&& ( strpos( $cookieStr, "wp-postpass_" ) === false || ! ! get_nitropack()->setDisabledReason( "password protected page" ) )
	);

	$isItemsInCart = ! empty( $_COOKIE["woocommerce_items_in_cart"] );
	$useCartOverride = nitropack_is_cart_cache_active();

	if ( $isUserLoggedIn ) {
		get_nitropack()->setDisabledReason( "logged in" );
	}

	if ( $isItemsInCart && ! $useCartOverride ) {
		get_nitropack()->setDisabledReason( "items in cart" );
	}

	// allow registering filters to "nitropack_passes_cookie_requirements"
	return apply_filters( "nitropack_passes_cookie_requirements", $safeCookie && ( ! $isItemsInCart || $useCartOverride ) && ( ! $isUserLoggedIn ) );
}

function nitropack_activate() {
	nitropack_set_wp_cache_const( true );

	$htaccessFile = nitropack_trailingslashit( NITROPACK_DATA_DIR ) . ".htaccess";
	if ( ! file_exists( $htaccessFile ) && get_nitropack()->initDataDir() ) {
		file_put_contents( $htaccessFile, "deny from all" );
	}

	$pluginHtaccessFile = nitropack_trailingslashit( NITROPACK_PLUGIN_DATA_DIR ) . ".htaccess";
	if ( ! file_exists( $pluginHtaccessFile ) && get_nitropack()->initPluginDataDir() ) {
		file_put_contents( $pluginHtaccessFile, "deny from all" ); // TODO: Convert this to use the Filesystem abstraction for better Redis support
	}

	nitropack_install_advanced_cache();

	// Htaccess mods need to happen after installing the advanced cache file so the healthcheck can execute fast
	nitropack_set_htaccess_rules( true );

	try {
		do_action( 'nitropack_integration_purge_all' );
	} catch (\Exception $e) {
		// Exception while signaling our 3rd party integration addons to purge their cache
	}

	if ( get_nitropack()->isConnected() ) {
		nitropack_event( "enable_extension" );
		// Refresh needed to make sure we have the latest config
		get_nitropack()->settings->set_required_settings();
	} else {
		setcookie( "nitropack_after_activate_notice", 1, time() + 3600 );
	}

	if ( function_exists( "opcache_reset" ) ) {
		opcache_reset();
	}
	( new \NitroPack\WordPress\Cron() )->schedule_events();
}

function nitropack_deactivate() {
	nitropack_set_htaccess_rules( false );
	nitropack_set_wp_cache_const( false );
	nitropack_uninstall_advanced_cache();

	try {
		do_action( 'nitropack_integration_purge_all' );
	} catch (\Exception $e) {
		// Exception while signaling our 3rd party integration addons to purge their cache
	}

	if ( get_nitropack()->isConnected() ) {
		nitropack_event( "disable_extension" );
	}

	if ( function_exists( "opcache_reset" ) ) {
		opcache_reset();
	}
	// Unscheduling events from the cron.
	\NitroPack\WordPress\Cron::unschedule_events();
}

function nitropack_install_advanced_cache() {
	$conflictingPlugins = \NitroPack\WordPress\ConflictingPlugins::getInstance();
	$nitropack_is_conflicting_plugin_active = $conflictingPlugins->nitropack_is_conflicting_plugin_active();
	if ( $nitropack_is_conflicting_plugin_active )
		return false;
	if ( ! nitropack_is_advanced_cache_allowed() )
		return false;

	$templatePath = nitropack_trailingslashit( __DIR__ ) . "advanced-cache.php";
	if ( file_exists( $templatePath ) ) {
		$contents = file_get_contents( $templatePath );
		$contents = str_replace( "/*NITROPACK_FUNCTIONS_FILE*/", __FILE__, $contents );
		$contents = str_replace( "/*NITROPACK_ABSPATH*/", ABSPATH, $contents );
		$contents = str_replace( "/*LOGIN_COOKIES*/", defined( "LOGGED_IN_COOKIE" ) ? LOGGED_IN_COOKIE : "", $contents );
		$contents = str_replace( "/*NP_VERSION*/", NITROPACK_VERSION, $contents );

		$advancedCacheFile = nitropack_trailingslashit( WP_CONTENT_DIR ) . 'advanced-cache.php';
		if ( WP_DEBUG ) {
			return file_put_contents( $advancedCacheFile, $contents );
		} else {
			return @file_put_contents( $advancedCacheFile, $contents );
		}
	}
}

function nitropack_uninstall_advanced_cache() {
	$advancedCacheFile = nitropack_trailingslashit( WP_CONTENT_DIR ) . 'advanced-cache.php';
	if ( file_exists( $advancedCacheFile ) ) {
		if ( WP_DEBUG ) {
			return file_put_contents( $advancedCacheFile, "" );
		} else {
			return @file_put_contents( $advancedCacheFile, "" );
		}
	}
}

function nitropack_set_wp_cache_const( $status ) {
	if ( \NitroPack\Integration\Hosting\Flywheel::detect() ) { // Flywheel: This is configured throught the FW control panel
		return true;
	}

	if ( \NitroPack\Integration\Hosting\Pressable::detect() ) { // Pressable: We need to deal with Batcache here
		return nitropack_set_batcache_compat( $status );
	}

	$configFilePath = nitropack_get_wpconfig_path();
	if ( ! $configFilePath )
		return false;

	$newVal = sprintf( "define( 'WP_CACHE', %s /* Modified by NitroPack */ );\n", ( $status ? "true" : "false" ) );
	$replacementVal = sprintf( " %s /* Modified by NitroPack */ ", ( $status ? "true" : "false" ) );
	$lines = file( $configFilePath );

	if ( empty( $lines ) )
		return false;

	$wpCacheFound = false;
	$phpOpeningTagLine = false;

	foreach ( $lines as $lineIndex => &$line ) {
		if ( strpos( $line, "<?php" ) !== false && strpos( $line, "?>" ) === false ) {
			$phpOpeningTagLine = $lineIndex;
		}

		if ( ! $wpCacheFound && preg_match( "/define\s*\(\s*['\"](.*?)['\"].?,(.*?)\)/", $line, $matches ) ) {
			if ( $matches[1] == "WP_CACHE" ) {
				$line = str_replace( $matches[2], $replacementVal, $line );
				$wpCacheFound = true;
			}
		}

		if ( $phpOpeningTagLine !== false && $wpCacheFound !== false )
			break;
	}

	if ( ! $wpCacheFound ) {
		if ( ! $status )
			return true; // No need to modify the file at all

		if ( $phpOpeningTagLine !== false ) {
			array_splice( $lines, $phpOpeningTagLine + 1, 0, [ $newVal ] );
		} else {
			array_unshift( $lines, "<?php " . trim( $newVal ) . " ?>\n" );
		}
	}

	return WP_DEBUG ? file_put_contents( $configFilePath, implode( "", $lines ) ) : @file_put_contents( $configFilePath, implode( "", $lines ) );
}

function nitropack_set_htaccess_rules( $status ) {
	if ( ! apply_filters( 'nitropack_should_modify_htaccess', false ) )
		return true;

	$htaccessFilePath = nitropack_get_htaccess_path();
	if ( ! $htaccessFilePath )
		return false;

	$htaccessBackupFilePath = $htaccessFilePath . ".nitrobackup";
	$backupExists = WP_DEBUG ? file_exists( $htaccessBackupFilePath ) : @file_exists( $htaccessBackupFilePath );
	if ( ! $backupExists ) {
		$isBackupSuccess = WP_DEBUG ? copy( $htaccessFilePath, $htaccessBackupFilePath ) : @copy( $htaccessFilePath, $htaccessBackupFilePath );
		if ( ! $isBackupSuccess )
			return false;
	}

	$lines = file( $htaccessFilePath );
	$linesBackup = $lines;

	if ( empty( $lines ) )
		return false; // We might want to remove this check

	// Remove the old LiteSpeed rules. We need to do this because LiteSpeed's rules are not compatible with NitroPack's rules.
	if ( $status ) {

		$nitroLsOpenLine = false;
		$nitroLsCloseLine = false;

		foreach ( $lines as $lineIndex => &$line ) {
			if ( trim( $line ) == "# BEGIN LSCACHE" ) {
				$nitroLsOpenLine = $lineIndex;
			}

			if ( trim( $line ) == "# END LSCACHE" ) {
				$nitroLsCloseLine = $lineIndex;
			}
		}

		if ( $nitroLsOpenLine !== false && $nitroLsCloseLine !== false && $nitroLsCloseLine > $nitroLsOpenLine ) {

			array_splice( $lines, $nitroLsOpenLine, $nitroLsCloseLine - $nitroLsOpenLine + 1 );
		}
	}

	$nitroOpenLine = false;
	$nitroCloseLine = false;

	foreach ( $lines as $lineIndex => &$line ) {
		if ( trim( $line ) == "# BEGIN NITROPACK" ) {
			$nitroOpenLine = $lineIndex;
		}

		if ( trim( $line ) == "# END NITROPACK" ) {
			$nitroCloseLine = $lineIndex;
		}
	}

	$nitroLines = [];

	if ( // We either didn't find the NitroPack markers or we found both in the correct order
		( $nitroOpenLine === false && $nitroCloseLine === false ) ||
		( $nitroOpenLine !== false && $nitroCloseLine !== false && $nitroCloseLine > $nitroOpenLine )
	) {
		$nitroLines[] = "# BEGIN NITROPACK";
		if ( $status ) {
			$rules = apply_filters( "nitropack_htaccess_rules", [] );

			if ( is_string( $rules ) ) {
				$rules = explode( "\n", $rules );
			}

			if ( is_array( $rules ) ) {
				$nitroLines = array_merge( $nitroLines, $rules );
			}
		}
		$nitroLines[] = "# END NITROPACK";
		$nitroLines = array_map( function ($line) {
			return trim( $line ) . "\n";
		}, $nitroLines );

		// Begin .htaccess modification
		$offset = $nitroOpenLine !== false ? $nitroOpenLine : 0;
		$length = $nitroOpenLine !== false ? $nitroCloseLine - $nitroOpenLine + 1 : 0;
		array_splice( $lines, $offset, $length, $nitroLines );
		$writeResult = WP_DEBUG ? file_put_contents( $htaccessFilePath, implode( "", $lines ) ) : @file_put_contents( $htaccessFilePath, implode( "", $lines ) );
		if ( $writeResult ) {
			$homeUrl = NULL;
			$siteConfig = get_nitropack()->getSiteConfig();

			if ( $siteConfig && ! empty( $siteConfig["home_url"] ) ) {
				$homeUrl = $siteConfig["home_url"];
			} else if ( function_exists( get_home_url() ) ) {
				$homeUrl = get_home_url();
			}

			if ( $homeUrl ) {
				$homeUrl .= ( strpos( $homeUrl, "?" ) === false ? "?" : "&" ) . "nitroHealthcheck=1";
				try {
					$client = new \NitroPack\HttpClient\HttpClient( $homeUrl );
					$client->timeout = 5;
					$client->setHeader( "Accept", "text/html" );
					$client->fetch();
					if ( $client->getStatusCode() != 200 ) {
						// Restore the initial version of the file
						WP_DEBUG ? file_put_contents( $htaccessFilePath, implode( "", $linesBackup ) ) : @file_put_contents( $htaccessFilePath, implode( "", $linesBackup ) );
						return false;
					}
				} catch (\Exception $e) {
					return false;
					// Unfortunately we can't be certain whether an issue appeared due to the .htaccess mods
					// There are no known cases of this happening, so it's fairly safe to assume that all is fine
					// There are server setups which do not allow loopback requests, which is the more likely reason to end up here
					// However we can't be certain which one it is, so we are taking the safer approach 
				}
			} else {
				return false;
			}
		}
		return $writeResult;
	}

	return true;
}

function nitropack_set_batcache_compat( $status ) {
	$currentCompatStatus = defined( "NITROPACK_BATCACHE_COMPAT" ) && NITROPACK_BATCACHE_COMPAT;
	if ( $currentCompatStatus === $status )
		return true;

	$configFilePath = nitropack_get_wpconfig_path();
	if ( ! $configFilePath )
		return false;

	$batCacheFilePath = NITROPACK_PLUGIN_DIR . "batcache-compat.php";
	$compatInclude = sprintf( "if (file_exists(\"%s\")) { require_once \"%s\"; } // NitroPack compatibility with Batcache\n", $batCacheFilePath, $batCacheFilePath );
	$lines = file( $configFilePath );

	if ( empty( $lines ) )
		return false;

	foreach ( $lines as $lineIndex => &$line ) {
		if ( preg_match( "/nitropack.*?batcache/i", $line ) ) {
			$line = "//REMOVE AT FILTER";
		}
	}

	$newLines = array_filter( $lines, function ($line) {
		return $line != "//REMOVE AT FILTER";
	} );

	if ( $status ) {
		$phpOpeningTagLine = false;

		unset( $line );
		foreach ( $newLines as $lineIndex => $line ) {
			if ( strpos( $line, "<?php" ) !== false && strpos( $line, "?>" ) === false ) {
				$phpOpeningTagLine = $lineIndex;
				break;
			}
		}

		if ( $phpOpeningTagLine !== false ) {
			array_splice( $newLines, $phpOpeningTagLine + 1, 0, [ $compatInclude ] );
		} else {
			array_unshift( $newLines, "<?php " . trim( $compatInclude ) . " ?>\n" );
		}
	}

	return WP_DEBUG ? file_put_contents( $configFilePath, implode( "", $newLines ) ) : @file_put_contents( $configFilePath, implode( "", $newLines ) );
}

function is_valid_nitropack_webhook() {
	return ! empty( $_GET["nitroWebhook"] ) && ! empty( $_GET["token"] ) && nitropack_validate_webhook_token( $_GET["token"] );
}

function is_valid_nitropack_beacon() {
	if ( ! isset( $_POST["nitroBeaconUrl"] ) || ! isset( $_POST["nitroBeaconHash"] ) )
		return false;

	$siteConfig = nitropack_get_site_config();
	if ( ! $siteConfig || empty( $siteConfig["siteSecret"] ) )
		return false;


	if ( function_exists( "hash_hmac" ) && function_exists( "hash_equals" ) ) {
		$url = base64_decode( $_POST["nitroBeaconUrl"] );
		$cookiesJson = ! empty( $_POST["nitroBeaconCookies"] ) ? base64_decode( $_POST["nitroBeaconCookies"] ) : ""; // We need to fall back to empty string to remain backwards compatible. Otherwise cache files invalidated before an upgrade will never get updated :(
		$layout = ! empty( $_POST["layout"] ) ? $_POST["layout"] : "";
		$localHash = hash_hmac( "sha512", $url . $cookiesJson . $layout, $siteConfig["siteSecret"] );
		return hash_equals( $_POST["nitroBeaconHash"], $localHash );
	} else {
		return ! empty( $_POST["nitroBeaconUrl"] );
	}
}

function nitropack_handle_beacon() {
	global $np_originalRequestCookies;
	if ( ! defined( "NITROPACK_BEACON_HANDLED" ) ) {
		define( "NITROPACK_BEACON_HANDLED", 1 );
	} else {
		return;
	}

	$siteConfig = nitropack_get_site_config();
	if ( $siteConfig && ! empty( $siteConfig["siteId"] ) && ! empty( $siteConfig["siteSecret"] ) && ! empty( $_POST["nitroBeaconUrl"] ) ) {
		$url = base64_decode( $_POST["nitroBeaconUrl"] );

		if ( ! empty( $_POST["nitroBeaconCookies"] ) ) {
			$np_originalRequestCookies = json_decode( base64_decode( $_POST["nitroBeaconCookies"] ), true );
		}

		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'Beacon request received for URL: ' . $url );

		if ( null !== $nitro = get_nitropack_sdk( $siteConfig["siteId"], $siteConfig["siteSecret"], $url ) ) {
			try {
				$hasLocalCache = $nitro->hasLocalCache( false );
				$needsHeartbeat = nitropack_is_heartbeat_needed();
				$proxyPurgeOnly = ! empty( $_POST["proxyPurgeOnly"] );
				$layout = ! empty( $_POST["layout"] ) ? $_POST["layout"] : "default";
				$output = "";

				if ( ! $proxyPurgeOnly ) {
					if ( ! $hasLocalCache ) {
						nitropack_header( "X-Nitro-Beacon: FORWARD" );
						try {
							$hasCache = $nitro->hasRemoteCache( $layout, false ); // Download the new cache file
							$hasLocalCache = $hasCache;
							$output = sprintf( "Cache %s", $hasCache ? "fetched" : "requested" );
						} catch (\Exception $e) {
							// not a critical error, do nothing
						}
					} else {
						nitropack_header( "X-Nitro-Beacon: SKIP" );
						$output = sprintf( "Cache exists already" );
					}
				}

				if ( $hasLocalCache || $proxyPurgeOnly/* || $needsHeartbeat*/ ) { // proxyPurgeOnly is set for unsupported browsers, in which case we need to purge the cache regardless of the existence of local NP cache
					nitropack_header( "X-Nitro-Proxy-Purge: true" );
					$nitro->purgeProxyCache( $url );
					do_action( 'nitropack_integration_purge_url', $url );
				}

				\NitroPack\ModuleHandler::onShutdown( function () use ($output) {
					echo $output;
				} );
			} catch (Exception $e) {
				// not a critical error, do nothing
			}
		}
	}
	\NitroPack\ModuleHandler::onCriticalInit( function () {
		exit;
	} );
}

/**
 * Handle NitroPack webhooks
 *
 * @return void
 */
/**
 * Handles the NitroPack webhook request
 *
 * @return void
 */
function nitropack_handle_webhook() {
	if ( defined( 'NITROPACK_DEBUG_MODE' ) ) {
		do_action( 'nitropack_debug_webhook', $_REQUEST );
	}
	if ( ! defined( "NITROPACK_WEBHOOK_HANDLED" ) ) {
		define( "NITROPACK_WEBHOOK_HANDLED", 1 );
	} else {
		return;
	}

	$siteConfig = nitropack_get_site_config();
	if ( $siteConfig && $siteConfig["webhookToken"] == $_GET["token"] ) {
		switch ( $_GET["nitroWebhook"] ) {
			case "config":
				nitropack_fetch_config();
				get_nitropack()->resetSdkInstances(); // This is needed in order to obtain a new SDK instance with the fresh config
				nitropack_set_htaccess_rules( true );
				if ( null !== $nitro = get_nitropack_sdk() ) {
					$nitro->purgeProxyCache();
				}
				do_action( 'nitropack_integration_purge_all' );
				break;
			case "cache_ready":
				if ( isset( $_POST['url'] ) ) {
					$urls = array( $_POST['url'] );
				} elseif ( isset( $_POST['urls'] ) ) {
					$urls = $_POST['urls'];
				} else {
					$urls = array();
				}
				if ( ! empty( $urls ) ) {
					$readyUrls = [];
					foreach ( $urls as $url ) {
						$readyUrl = nitropack_sanitize_url_input( $url );
						if ( $readyUrl ) {
							$readyUrls[] = $readyUrl;
						}
					}

					if ( $readyUrls && null !== $nitro = get_nitropack_sdk( $siteConfig["siteId"], $siteConfig["siteSecret"], $readyUrls[0] ) ) {
						$hasCache = $nitro->hasRemoteCacheMulti( $readyUrls, "default", false ); // Download the new cache file
						foreach ( $readyUrls as $readyUrl ) {
							$nitro->purgeProxyCache( $readyUrl );
							do_action( 'nitropack_integration_purge_url', $readyUrl );
						}
					}
				}
				break;
			case "cache_clear":
				if ( isset( $_POST['url'] ) ) {
					$urls = array( $_POST['url'] );
				} elseif ( isset( $_POST['urls'] ) ) {
					$urls = $_POST['urls'];
				}

				$proxyPurgeOnly = ! empty( $_POST["proxyPurgeOnly"] );
				$doAction = ! empty( $_POST['useInvalidate'] )
					? static function ($url = null) {
						nitropack_sdk_invalidate_local( $url );
					}
					: static function ($url = null) {
						nitropack_sdk_purge_local( $url );
					};

				if ( ! empty( $_POST["url"] ) ) {
					$urls = is_array( $_POST["url"] ) ? $_POST["url"] : array( $_POST["url"] );
					foreach ( $urls as $url ) {
						$sanitizedUrl = nitropack_sanitize_url_input( $url );
						if ( $proxyPurgeOnly ) {
							if ( null !== $nitro = get_nitropack_sdk( $siteConfig["siteId"], $siteConfig["siteSecret"] ) ) {
								$nitro->purgeProxyCache( $sanitizedUrl );
							}
							do_action( 'nitropack_integration_purge_url', $sanitizedUrl );
						} else {
							$doAction( $sanitizedUrl );
						}
					}
				} else {
					if ( $proxyPurgeOnly ) {
						if ( null !== $nitro = get_nitropack_sdk( $siteConfig["siteId"], $siteConfig["siteSecret"] ) ) {
							$nitro->purgeProxyCache();
						}
						do_action( 'nitropack_integration_purge_all' );
					} else {
						$doAction();
						nitropack_sdk_delete_backlog();
					}
				}
				break;
		}
	}
	\NitroPack\ModuleHandler::onCriticalInit( function () {
		nitropack_json_and_exit( array(
			"type" => "success",
		) );
	} );
}

function nitropack_sanitize_url_input( $url ) {
	$result = NULL;
	if ( ! function_exists( "esc_url" ) ) {
		$sanitizedUrl = filter_var( $url, FILTER_SANITIZE_URL );
		if ( $sanitizedUrl !== false && filter_var( $sanitizedUrl, FILTER_VALIDATE_URL ) !== false ) {
			$result = $sanitizedUrl;
		}
	} else if ( $validatedUrl = esc_url( $url, array( "http", "https" ), "notdisplay" ) ) {
		$result = $validatedUrl;
	}

	return $result;
}

function nitropack_is_amp_page() {
	return ( function_exists( 'amp_is_request' ) && amp_is_request() && ! get_nitropack()->setDisabledReason( "amp page" ) ) ||
		( function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint() && ! get_nitropack()->setDisabledReason( "amp page" ) );
}

function nitropack_passes_page_requirements( $detectIfNoCachedResult = true ) {
	static $cachedResult = NULL;
	$reduceCheckoutChecks = defined( "NITROPACK_REDUCE_CHECKOUT_CHECKS" ) && NITROPACK_REDUCE_CHECKOUT_CHECKS;
	$reduceCartChecks = defined( "NITROPACK_REDUCE_CART_CHECKS" ) && NITROPACK_REDUCE_CART_CHECKS;

	if ( $cachedResult === NULL && $detectIfNoCachedResult ) {

		$cachedResult = ! (
			( is_404() && ! get_nitropack()->setDisabledReason( "404" ) ) ||
			( is_preview() && ! get_nitropack()->setDisabledReason( "preview page" ) ) ||
			( is_feed() && ! get_nitropack()->setDisabledReason( "feed" ) ) ||
			( is_comment_feed() && ! get_nitropack()->setDisabledReason( "comment feed" ) ) ||
			( is_trackback() && ! get_nitropack()->setDisabledReason( "trackback" ) ) ||
			( nitropack_is_logged_in() && ! get_nitropack()->setDisabledReason( "logged in" ) ) ||
			( is_search() && ! get_nitropack()->setDisabledReason( "search" ) ) ||
			( nitropack_is_ajax() && ! get_nitropack()->setDisabledReason( "ajax" ) ) ||
			( nitropack_is_post() && ! get_nitropack()->setDisabledReason( "post request" ) ) ||
			( nitropack_is_xmlrpc() && ! get_nitropack()->setDisabledReason( "xmlrpc" ) ) ||
			( nitropack_is_robots() && ! get_nitropack()->setDisabledReason( "robots" ) ) ||
			nitropack_is_amp_page() ||
			! nitropack_is_allowed_request() ||
			( nitropack_is_wp_cron() && ! get_nitropack()->setDisabledReason( "doing cron" ) ) || // CRON request
			( nitropack_is_wp_cli() ) || // CLI request
			( defined( 'WC_PLUGIN_FILE' ) && ( is_page( 'cart' ) || ( ! $reduceCartChecks && is_cart() ) ) && ! get_nitropack()->setDisabledReason( "cart page" ) ) || // WooCommerce
			( defined( 'WC_PLUGIN_FILE' ) && ( is_page( 'checkout' ) || ( ! $reduceCheckoutChecks && is_checkout() ) ) && ! get_nitropack()->setDisabledReason( "checkout page" ) ) || // WooCommerce
			( defined( 'WC_PLUGIN_FILE' ) && is_account_page() && ! get_nitropack()->setDisabledReason( "account page" ) ) // WooCommerce
		);
	}

	return $cachedResult;
}

function nitropack_is_home() {
	if ( 'posts' == get_option( 'show_on_front' ) ) {
		return is_front_page() || is_home();
	} else {
		return is_front_page();
	}
}

function nitropack_is_blogindex() {
	return is_home();
}

function nitropack_is_archive() {
	return apply_filters( "nitropack_is_archive_page", is_author() || is_archive() );
}

function nitropack_is_allowed_request() {
	global $np_queriedObj;
	$cacheableObjectTypes = nitropack_get_cacheable_object_types();
	if ( is_array( $cacheableObjectTypes ) ) {
		if ( nitropack_is_home() ) {
			if ( ! in_array( 'home', $cacheableObjectTypes ) ) {
				get_nitropack()->setDisabledReason( "page type not allowed (home)" );
				return false;
			}
		} else {
			if ( is_tax() || is_category() || is_tag() ) {
				$np_queriedObj = get_queried_object();
				if ( ! empty( $np_queriedObj ) && ! in_array( $np_queriedObj->taxonomy, $cacheableObjectTypes ) ) {
					get_nitropack()->setDisabledReason( "page type not allowed ({$np_queriedObj->taxonomy})" );
					return false;
				}
			} else {
				if ( nitropack_is_archive() ) {
					if ( ! in_array( 'archive', $cacheableObjectTypes ) ) {
						get_nitropack()->setDisabledReason( "page type not allowed (archive)" );
						return false;
					}
				} else {
					$postType = get_post_type();
					if ( ! empty( $postType ) && ! in_array( $postType, $cacheableObjectTypes ) ) {
						get_nitropack()->setDisabledReason( "page type not allowed ($postType)" );
						return false;
					}
				}
			}
		}
	}

	$test_mode = get_option( 'nitropack-safeModeStatus');
	if ( $test_mode ) {
		get_nitropack()->setDisabledReason( "Test Mode" );
		return false;
	}

	if ( null !== $nitro = get_nitropack_sdk() ) {
		return ( $nitro->isAllowedUrl( $nitro->getUrl() ) || get_nitropack()->setDisabledReason( "url not allowed" ) ) &&
			( $nitro->isAllowedRequest( true ) || get_nitropack()->setDisabledReason( "request type not allowed" ) );
	}

	get_nitropack()->setDisabledReason( "site not connected" );
	return false;
}

function nitropack_is_ajax() {
	return ( function_exists( "wp_doing_ajax" ) && wp_doing_ajax() ) ||
		( defined( 'DOING_AJAX' ) && DOING_AJAX ) ||
		( ! empty( $_SERVER["HTTP_X_REQUESTED_WITH"] ) && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest" ) ||
		( ! empty( $_SERVER["REQUEST_URI"] ) && basename( $_SERVER["REQUEST_URI"] ) == "admin-ajax.php" ) ||
		! empty( $_GET["wc-ajax"] );
}

/**
 * Checking if the current request is wp-cli request
 *
 * @return bool
 */
function nitropack_is_wp_cli() {
	return NitroPack\WordPress\NitroPack::isWpCli();
}

function nitropack_is_wp_cron() {
	return defined( 'DOING_CRON' ) && DOING_CRON;
}

function nitropack_is_rest() {
	// Source: https://wordpress.stackexchange.com/a/317041
	$prefix = rest_get_url_prefix();
	if (
		defined( 'REST_REQUEST' ) && REST_REQUEST // (#1)
		|| isset( $_GET['rest_route'] ) // (#2)
		&& strpos( trim( $_GET['rest_route'], '\\/' ), $prefix, 0 ) === 0
	)
		return true;
	// (#3)
	global $wp_rewrite;
	if ( $wp_rewrite === null )
		$wp_rewrite = new WP_Rewrite();

	// (#4)
	$rest_url = wp_parse_url( trailingslashit( rest_url() ) );
	$current_url = wp_parse_url( add_query_arg( array() ) );
	return strpos( $current_url['path'], $rest_url['path'], 0 ) === 0;
}

function nitropack_is_post() {
	return ( ! empty( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) || ( empty( $_SERVER['REQUEST_METHOD'] ) && ! empty( $_POST ) );
}

function nitropack_is_xmlrpc() {
	return defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST;
}

function nitropack_is_robots() {
	return is_robots() || ( ! empty( $_SERVER["REQUEST_URI"] ) && basename( parse_url( $_SERVER["REQUEST_URI"], PHP_URL_PATH ) ) === "robots.txt" );
}

// IMPORTANT: This function should only be trusted if NitroPack is connected. Otherwise we may not have information about the admin URL in the config file and it may return an incorrect result
function nitropack_is_admin() {
	if ( ( nitropack_is_ajax() || nitropack_is_rest() ) && ! empty( $_SERVER["HTTP_REFERER"] ) ) {
		$adminUrl = NULL;
		$siteConfig = nitropack_get_site_config();
		if ( $siteConfig && ! empty( $siteConfig["admin_url"] ) ) {
			$adminUrl = $siteConfig["admin_url"];
		} else if ( function_exists( "admin_url" ) ) {
			$adminUrl = admin_url();
		} else {
			return is_admin();
		}

		return strpos( $_SERVER["HTTP_REFERER"], $adminUrl ) === 0;
	} else {
		return is_admin();
	}
}

function nitropack_is_warmup_request() {
	return ! empty( $_SERVER["HTTP_X_NITRO_WARMUP"] );
}

function nitropack_is_lighthouse_request() {
	return ! empty( $_SERVER["HTTP_USER_AGENT"] ) && stripos( $_SERVER["HTTP_USER_AGENT"], "lighthouse" ) !== false;
}

function nitropack_is_gtmetrix_request() {
	return ! empty( $_SERVER["HTTP_USER_AGENT"] ) && stripos( $_SERVER["HTTP_USER_AGENT"], "gtmetrix" ) !== false;
}

function nitropack_is_pingdom_request() {
	return ! empty( $_SERVER["HTTP_USER_AGENT"] ) && stripos( $_SERVER["HTTP_USER_AGENT"], "pingdom" ) !== false;
}

function nitropack_is_optimizer_request() {
	return isset( $_SERVER["HTTP_X_NITROPACK_REQUEST"] );
}

function nitropack_init() {
	global $np_queriedObj;
	nitropack_header( 'X-Nitro-Cache: MISS' );
	$GLOBALS["NitroPack.tags"] = array();

	if ( is_valid_nitropack_webhook() ) {
		nitropack_handle_webhook();
	} else {
		if ( is_valid_nitropack_beacon() ) {
			nitropack_handle_beacon();
		} else {
			/* The following if statement should stay as it is written.
			 * is_archive() can return true if visiting a tax, category or tag page, so is_acrchive must be checked last
			 */
			if ( is_tax() || is_category() || is_tag() ) {
				$np_queriedObj = get_queried_object();
				get_nitropack()->setPageType( $np_queriedObj->taxonomy );
			} else {
				$layout = nitropack_get_layout();
				get_nitropack()->setPageType( $layout );
			}

			add_action( 'wp_footer', 'nitropack_print_element_override', 9999999 );
			if ( ! isset( $_GET["wpf_action"] ) && nitropack_passes_cookie_requirements() && nitropack_passes_page_requirements() ) {
				add_action( 'wp_footer', 'nitropack_print_beacon_script' );
				add_action( 'get_footer', 'nitropack_print_beacon_script' );

				if ( nitropack_is_optimizer_request() ) { // Only care about tags for requests coming from our service. There is no need to do an API request when handling a standard client request.
					if ( defined( 'FUSION_BUILDER_VERSION' ) ) {
						add_filter( 'do_shortcode_tag', 'nitropack_handle_fusion_builder_conatainer_expiration', 10, 3 );
						add_action( 'wp_footer', 'nitropack_set_custom_expiration' );
					} else {
						nitropack_set_custom_expiration();
					}

					$GLOBALS["NitroPack.tags"][ "pageType:" . get_nitropack()->getPageType()] = 1;

					/* The following if statement should stay as it is written.
					 * is_archive() can return true if visiting a tax, category or tag page, so is_acrchive must be checked last
					 */
					if ( is_tax() || is_category() || is_tag() ) {
						$np_queriedObj = get_queried_object();
						$GLOBALS["NitroPack.tags"][ "tax:" . $np_queriedObj->term_taxonomy_id ] = 1;
					} else {
						if ( is_single() || is_page() || is_attachment() ) {
							$singlePost = get_post();
							if ( $singlePost ) {
								$GLOBALS["NitroPack.tags"][ "single:" . $singlePost->ID ] = 1;
							}
						}
					}

					// Uncomment the code below in case object cache interferes with correct URL taggig
					// The code below will attempt to temporarily disable using the object cache only for the requests coming from NitroPack
					//wp_using_ext_object_cache(false);
					//add_action("pre_get_posts", function($query) {
					//    $query->query_vars["cache_results"] = false;
					//});
					//
					//add_filter("all", function() {
					//    $args = func_get_args();
					//    if (count($args) > 1) {
					//        list($filterName, $value) = func_get_args();
					//        if (preg_match("/^transient_(.*)/", $filterName, $matches) && $value) {
					//            return false;
					//        }
					//    }
					//}, 10, 2);

					add_filter( 'post_link', 'nitropack_post_link_listener', 10, 3 );
					add_action( 'the_post', 'nitropack_handle_the_post' );
					add_action( 'wp_footer', 'nitropack_log_tags' );
				}
			} else {
				nitropack_header( "X-Nitro-Disabled: 1" );
				if ( ( null !== $nitro = get_nitropack_sdk() ) && ! $nitro->isAllowedBrowser() ) { // This clears any proxy cache when a proxy cached non-optimized request due to unsupported browser
					add_action( 'wp_footer', 'nitropack_print_beacon_script' );
					add_action( 'get_footer', 'nitropack_print_beacon_script' );
				}
			}

			if ( ! nitropack_is_optimizer_request() ) {
				add_action( 'wp_head', 'nitropack_print_generic_nitro_script' );
			}
		}
	}
}

function nitropack_handle_fusion_builder_conatainer_expiration( $output, $tag, $attr ) {
	global $np_customExpirationTimes;
	if ( $tag == "fusion_builder_container" ) {
		if ( ! empty( $attr["publish_date"] ) && ! empty( $attr["status"] ) && in_array( $attr["status"], array( "published_until", "publish_after" ) ) ) {
			$timezone = get_option( 'timezone_string' );
			$offset = get_option( 'gmt_offset' );
			$dt = new DateTime( $attr["publish_date"] );
			if ( $timezone ) {
				$timeZone = new DateTimeZone( $timezone );
				$timeZoneOffset = $timeZone->getOffset( $dt );
			} else if ( $offset ) {
				$timeZoneOffset = (int) $offset * 3600;
			}
			$time = $dt->getTimestamp() - $timeZoneOffset;
			if ( $time > time() ) { // We only need to look at future dates
				$np_customExpirationTimes[] = $time;
			}
		}
	}
	return $output;
}
function nitropack_set_custom_expiration() {

	//check which CPTs are marked for optimization
	$get_optimized_CPTs = nitropack_get_optimized_CPTs();
	if ( empty( $get_optimized_CPTs ) )
		return;

	global $np_customExpirationTimes, $wpdb;

	$placeholders = implode( ',', array_fill( 0, count( $get_optimized_CPTs ), '%s' ) );
	$currentDate = date( "Y-m-d H:i:s" );

	//For better security, we use prepared statements
	$unmodifiedPosts_query = $wpdb->prepare(
		"SELECT ID, post_date 
        FROM {$wpdb->prefix}posts 
        WHERE {$wpdb->prefix}posts.post_date > %s
        AND {$wpdb->prefix}posts.post_type IN ($placeholders)
        AND ({$wpdb->prefix}posts.post_status = 'future')
        ORDER BY {$wpdb->prefix}posts.post_date ASC 
        LIMIT 0, 1",
		array_merge( [ $currentDate ], $get_optimized_CPTs )
	);
	$unmodifiedPosts = $wpdb->get_results( $unmodifiedPosts_query );

	if ( ! empty( $unmodifiedPosts ) && strtotime( $unmodifiedPosts[0]->post_date ) > time() ) {
		$np_customExpirationTimes[] = strtotime( $unmodifiedPosts[0]->post_date );
	}

	if ( ! empty( $np_customExpirationTimes ) ) {
		sort( $np_customExpirationTimes, SORT_NUMERIC );
		nitropack_header( "X-Nitro-Expires: " . $np_customExpirationTimes[0] );
	}
}

function nitropack_print_beacon_script() {
	if ( defined( "NITROPACK_BEACON_PRINTED" ) || ! nitropack_passes_page_requirements() )
		return;
	define( "NITROPACK_BEACON_PRINTED", true );
	echo apply_filters( "nitro_script_output", nitropack_get_beacon_script() );
}

function nitropack_get_beacon_script() {
	$siteConfig = nitropack_get_site_config();
	if ( $siteConfig && ! empty( $siteConfig["siteId"] ) && ! empty( $siteConfig["siteSecret"] ) ) {
		if ( null !== $nitro = get_nitropack_sdk( $siteConfig["siteId"], $siteConfig["siteSecret"] ) ) {
			$url = $nitro->getUrl();
			$cookiesJson = json_encode( $nitro->supportedCookiesFilter( NitroPack\SDK\NitroPack::getCookies() ) );
			$layout = nitropack_get_layout();

			if ( function_exists( "hash_hmac" ) && function_exists( "hash_equals" ) ) {
				$hash = hash_hmac( "sha512", $url . $cookiesJson . $layout, $siteConfig["siteSecret"] );
			} else {
				$hash = "";
			}
			$url = base64_encode( $url ); // We want only ASCII
			$cookiesb64 = base64_encode( $cookiesJson );
			$proxyPurgeOnly = ! $nitro->isAllowedBrowser();

			return "
<script nitro-exclude>
    if (!window.NITROPACK_STATE || window.NITROPACK_STATE != 'FRESH') {
        var proxyPurgeOnly = " . ( $proxyPurgeOnly ? 1 : 0 ) . ";
        if (typeof navigator.sendBeacon !== 'undefined') {
            var nitroData = new FormData(); nitroData.append('nitroBeaconUrl', '$url'); nitroData.append('nitroBeaconCookies', '$cookiesb64'); nitroData.append('nitroBeaconHash', '$hash'); nitroData.append('proxyPurgeOnly', '$proxyPurgeOnly'); nitroData.append('layout', '$layout'); navigator.sendBeacon(location.href, nitroData);
        } else {
            var xhr = new XMLHttpRequest(); xhr.open('POST', location.href, true); xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); xhr.send('nitroBeaconUrl={$url}&nitroBeaconCookies={$cookiesb64}&nitroBeaconHash={$hash}&proxyPurgeOnly={$proxyPurgeOnly}&layout={$layout}');
        }
    }
</script>";
		}
	}
}

function nitropack_print_cookie_handler_script() {

	if ( defined( "NITROPACK_COOKIE_HANDLER_PRINTED" ) )
		return;
	define( "NITROPACK_COOKIE_HANDLER_PRINTED", true );

	echo apply_filters( "nitro_script_output", nitropack_get_cookie_handler_script() );
}

function nitropack_get_cookie_handler_script() {
	return "
<script nitro-exclude>
    document.cookie = 'nitroCachedPage=' + (!window.NITROPACK_STATE ? '0' : '1') + '; path=/; SameSite=Lax';
</script>";
}

function nitropack_print_generic_nitro_script() {
	if ( defined( "NITROPACK_GENERIC_NITRO_SCRIPT_PRINTED" ) )
		return;
	define( "NITROPACK_GENERIC_NITRO_SCRIPT_PRINTED", true );
	echo apply_filters( "nitro_script_output", nitropack_get_telemetry_meta() );
	echo apply_filters( "nitro_script_output", nitropack_get_generic_nitro_script() );
}

function nitropack_get_generic_nitro_script() {
	$siteConfig = nitropack_get_site_config();
	if ( $siteConfig && ! empty( $siteConfig["siteId"] ) && ! empty( $siteConfig["siteSecret"] ) ) {
		if ( null !== $nitro = get_nitropack_sdk( $siteConfig["siteId"], $siteConfig["siteSecret"] ) ) {
			$config = $nitro->getConfig();
			if ( ! empty( $config->GenericNitroScript->Script ) ) {
				return "<script id='nitro-generic' nitro-exclude>" . $config->GenericNitroScript->Script . "</script>";
			}
		}
	}

	return "";
}

function nitropack_get_telemetry_meta() {
	$disabledReason = get_nitropack()->getDisabledReason();
	$missReason = $disabledReason !== NULL ? $disabledReason : "cache not found";
	$pageType = get_nitropack()->getPageType();
	$isEligibleForOptimization = nitropack_passes_page_requirements();
	$metaObj = "window.NPTelemetryMetadata={";

	if ( $missReason ) {
		$metaObj .= "missReason: (!window.NITROPACK_STATE ? '$missReason' : 'hit'),";
	}

	if ( $pageType ) {
		$metaObj .= "pageType: '$pageType',";
	}

	$metaObj .= "isEligibleForOptimization: " . ( $isEligibleForOptimization ? "true" : "false" ) . ",";

	$metaObj .= "}";

	return "<script id='nitro-telemetry-meta' nitro-exclude>$metaObj</script>";
}

function nitropack_print_element_override() {
	if ( defined( "NITROPACK_ELEMENT_OVERRIDE_PRINTED" ) )
		return;
	define( "NITROPACK_ELEMENT_OVERRIDE_PRINTED", true );
	echo apply_filters( "nitro_script_output", nitropack_get_element_override_script() );
}

function nitropack_get_element_override_script() {
	$nitro = get_nitropack_sdk();
	return $nitro !== NULL ? $nitro->getStatefulCacheHandlerScript() : "";
}

function nitropack_has_advanced_cache() {
	return defined( 'NITROPACK_ADVANCED_CACHE' );
}

function nitropack_validate_site_id( $siteId ) {
	return preg_match( "/^([a-zA-Z]{32})$/", trim( $siteId ) );
}

function nitropack_validate_site_secret( $siteSecret ) {
	return preg_match( "/^([a-zA-Z0-9]{64})$/", trim( $siteSecret ) );
}

function nitropack_validate_webhook_token( $token ) {
	return preg_match( "/^([abcdef0-9]{32})$/", strtolower( $token ) );
}

function nitropack_validate_wc_currency( $cookieValue ) {
	return preg_match( "/^([a-z]{3})$/", strtolower( $cookieValue ) );
}

function nitropack_validate_wc_currency_language( $cookieValue ) {
	return preg_match( "/^([a-z_\\-]{2,})$/", strtolower( $cookieValue ) );
}

function nitropack_get_default_cacheable_object_types() {
	$result = array( "home", "archive" );
	$postTypes = get_post_types( array( 'public' => true ), 'names' );
	$result = array_merge( $result, $postTypes );
	foreach ( $postTypes as $postType ) {
		$result = array_merge( $result, get_taxonomies( array( 'object_type' => array( $postType ), 'public' => true ), 'names' ) );
	}
	return $result;
}

function nitropack_get_object_types() {
	$objectTypes = get_post_types( array( 'public' => true ), 'objects' );
	$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );

	foreach ( $objectTypes as &$objectType ) {
		$objectType->taxonomies = [];
		foreach ( $taxonomies as $tax ) {
			if ( in_array( $objectType->name, $tax->object_type ) ) {
				$objectType->taxonomies[] = $tax;
			}
		}
	}

	return $objectTypes;
}

/**
 * Retrievs all CPTs eligable for optimization and checking if they are optimized
 */
function nitropack_get_CPTs_with_optimization_status() {

	$cacheableObjectTypes = nitropack_get_cacheable_object_types();
	$objectTypes = nitropack_get_object_types();

	$result = [ 
		"home" => [ 
			'name' => 'Home',
			'isOptimized' => in_array( 'home', $cacheableObjectTypes ) ? 1 : 0,
		],
		"archive" => [ 
			'name' => 'Archive',
			'isOptimized' => in_array( 'archive', $cacheableObjectTypes ) ? 1 : 0,
		]
	];

	// Determine new object types by comparing current with stored
	foreach ( $objectTypes as $slug => $objectType ) {
		$isOptimized = in_array( $objectType->name, $cacheableObjectTypes );
		$taxonomies = [];

		if ( ! empty( $objectType->taxonomies ) ) {
			foreach ( $objectType->taxonomies as $tax ) {
				$taxName = $tax->name;
				$taxonomies[ $taxName ] = [ 
					'isOptimized' => in_array( $taxName, $cacheableObjectTypes ) ? 1 : 0,
					'name' => $tax->labels->name
				];
			}
		}

		$result[ $slug ] = [ 
			'isOptimized' => $isOptimized ? 1 : 0,
			'name' => $objectType->labels->name,
			'taxonomies' => $taxonomies
		];
	}

	return $result;
}
/**
 * Filters the CPTs which are not optimized.
 * Show it only once.
 */
function nitropack_filter_non_optimized() {
	$filteredArr = [];
	$objectTypes = nitropack_get_CPTs_with_optimization_status();
	foreach ( $objectTypes as $slug => $objectType ) {
		$includeObjectType = ! $objectType['isOptimized'];
		$filteredTaxonomies = [];
		if ( isset( $objectType['taxonomies'] ) ) {
			foreach ( $objectType['taxonomies'] as $taxSlug => $taxonomy ) {
				if ( ! $taxonomy['isOptimized'] ) {
					$filteredTaxonomies[ $taxSlug ] = $taxonomy;
					$includeObjectType = true; // Include CPT if it has any non-optimized taxonomy
				}
			}
		}
		if ( $includeObjectType ) {
			$filteredArr[ $slug ] = [ 
				'isOptimized' => $objectType['isOptimized'],
				'name' => $objectType['name'],
				'taxonomies' => $filteredTaxonomies
			];
		}
	}
	return $filteredArr;
}
/**
 * Retrieve the list of optimized Custom Post Types (CPTs).
 *
 * This function fetches the CPTs with their optimization status and returns an array of CPT keys
 * that are marked as optimized. It excludes 'home' and 'archive' as they are not valid CPTs.
 *
 * @return array An array of optimized CPT keys.
 */
function nitropack_get_optimized_CPTs() {
	$get_CPTs_with_optimization_status = nitropack_get_CPTs_with_optimization_status();

	$optimizedKeys = [];

	foreach ( $get_CPTs_with_optimization_status as $key => $value ) {

		if ( $key === 'home' || $key === 'archive' )
			continue; //not valid CPTs

		if ( isset( $value['isOptimized'] ) && $value['isOptimized'] === 1 ) {
			$optimizedKeys[] = $key;
		}
	}

	return $optimizedKeys;
}

function nitropack_autooptimize_new_post_types_and_taxonomies() {
	//start optimizing after the notice is shown
	$notices = get_option( 'nitropack-dismissed-notices', [] );
	if ( ! $notices || ! in_array( 'OptimizeCPT', $notices ) )
		return;
	//check if the non-optimized CPTs are stored, if not store them
	$nonCacheableObjectTypes = get_option( 'nitropack-nonCacheableObjectTypes' );
	if ( ! $nonCacheableObjectTypes ) {
		$notOptimizedCPTs = nitropack_filter_non_optimized();
		$nonCacheableObjectTypes = [];
		foreach ( $notOptimizedCPTs as $slug => $objectType ) {
			if ( ! $objectType['isOptimized'] ) {
				$nonCacheableObjectTypes[] = $slug;
			}
			foreach ( $objectType['taxonomies'] as $taxSlug => $taxonomy ) {
				if ( ! $taxonomy['isOptimized'] ) {
					$nonCacheableObjectTypes[] = $taxSlug;
				}
			}
		}
		update_option( 'nitropack-nonCacheableObjectTypes', $nonCacheableObjectTypes );
	}

	$postTypes = get_post_types( array( 'public' => true ), 'objects' );
	$cacheableObjectTypes = nitropack_get_cacheable_object_types();
	foreach ( $postTypes as $slug => $postType ) {
		if ( $postType->public ) {
			// Check and add post type to cacheable object types
			if ( ! in_array( $slug, $nonCacheableObjectTypes ) && ! in_array( $slug, $cacheableObjectTypes ) ) {
				$cacheableObjectTypes[] = $slug;
			}

			// Fetch and add connected taxonomies
			$taxonomies = get_object_taxonomies( $slug, 'names' );
			foreach ( $taxonomies as $taxonomy ) {
				if ( ! in_array( $taxonomy, $nonCacheableObjectTypes ) && ! in_array( $taxonomy, $cacheableObjectTypes ) ) {
					$cacheableObjectTypes[] = $taxonomy;
				}
			}
		}
	}

	update_option( 'nitropack-cacheableObjectTypes', $cacheableObjectTypes );
}




function nitropack_get_cacheable_object_types() {
	return apply_filters( "nitropack_cacheable_post_types", get_option( "nitropack-cacheableObjectTypes", nitropack_get_default_cacheable_object_types() ) );
}

/** Step 3. */
function nitropack_options() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	wp_enqueue_script( 'jquery-form' );

	// Manually add home and archive page object
	$homeCustomObject = new stdClass();
	$homeCustomObject->name = 'home';
	$homeCustomObject->label = 'Home';
	$homeCustomObject->taxonomies = array();

	$archiveCustomObject = new stdClass();
	$archiveCustomObject->name = 'archive';
	$archiveCustomObject->label = 'Archive';
	$archiveCustomObject->taxonomies = array();
	$objectTypes = array_merge( array( 'home' => $homeCustomObject, 'archive' => $archiveCustomObject ), nitropack_get_object_types() );

	$enableCompression = get_option( 'nitropack-enableCompression' );
	$canEditorClearCache = get_option( 'nitropack-canEditorClearCache' );
	$autoCachePurge = get_option( 'nitropack-autoCachePurge', 1 );
	$bbCacheSyncPurge = get_option( 'nitropack-bbCacheSyncPurge', 0 );
	$legacyPurge = get_option( 'nitropack-legacyPurge', 0 );
	$checkedCompression = get_option( 'nitropack-checkedCompression' );
	$stockReduceStatus = get_option( 'nitropack-stockReduceStatus' );
	$cacheableObjectTypes = nitropack_get_cacheable_object_types();

	if ( get_nitropack()->isConnected() ) {
		$planDetailsUrl = get_nitropack_integration_url( "plan_details_json" );
		$optimizationDetailsUrl = get_nitropack_integration_url( "optimization_details_json" );
		$quickSetupUrl = get_nitropack_integration_url( "quicksetup_json" );
		$quickSetupSaveUrl = get_nitropack_integration_url( "quicksetup" );
		$helpLabels = [ 'wordpress_plugin_help' ];


		if ( get_nitropack()->getDistribution() == "oneclick" ) {
			$helpLabels = [ 'wordpress_plugin_help_oneclick' ];
			$oneClickVendorWidget = apply_filters( "nitropack_oneclick_vendor_widget", "" );
			include plugin_dir_path( __FILE__ ) . nitropack_trailingslashit( 'view' ) . 'oneclick.php';
		} else {
			include plugin_dir_path( __FILE__ ) . nitropack_trailingslashit( 'view' ) . 'admin.php';
		}
	} else {
		if ( get_nitropack()->getDistribution() == "oneclick" ) {
			$oneClickConnectUrl = apply_filters( "nitropack_oneclick_connect_url", "" );
			include plugin_dir_path( __FILE__ ) . nitropack_trailingslashit( 'view' ) . 'connect-oneclick.php';
		} else {
			include plugin_dir_path( __FILE__ ) . nitropack_trailingslashit( 'view' ) . 'connect.php';
		}
	}
}
function load_nitropack_scripts_styles( $page ) {
	//global WP
	wp_enqueue_style( 'nitropack-notifications', plugin_dir_url( __FILE__ ) . 'view/stylesheet/nitro-notifications.min.css', array(), NITROPACK_VERSION );
	wp_enqueue_script( 'nitropack_notices_js', plugin_dir_url( __FILE__ ) . 'view/javascript/np_notices.js', array(), NITROPACK_VERSION, true );
	wp_localize_script( 'nitropack_notices_js', 'nitropack_notices_vars', array(
		'nonce' => wp_create_nonce( NITROPACK_NONCE ),
	) );
	//plugin only
	if ( $page === 'toplevel_page_nitropack' ) {
		//css
		wp_enqueue_style( 'nitropack', plugin_dir_url( __FILE__ ) . 'view/stylesheet/style.min.css', array(), NITROPACK_VERSION );
		//js
		wp_enqueue_script( 'nitropack_flowbite_js', plugin_dir_url( __FILE__ ) . 'view/javascript/flowbite.min.js', array(), NITROPACK_VERSION, true );
		wp_enqueue_script( 'nitropack_ui', plugin_dir_url( __FILE__ ) . 'view/javascript/nitropackUI.js', array(), NITROPACK_VERSION, true );

		if ( get_nitropack()->isConnected() ) {
			wp_enqueue_script( 'nitropack_settings', plugin_dir_url( __FILE__ ) . 'view/javascript/np_settings.js', array(), NITROPACK_VERSION, true );
			wp_localize_script(
				'nitropack_settings',
				'np_settings',
				array(
					'nitroNonce' => wp_create_nonce( NITROPACK_NONCE ),
					'nitro_plugin_url' => plugin_dir_url( __FILE__ ),
					'error_msg' => esc_html__( 'Something went wrong.', 'nitropack' ),
					'success_msg' => esc_html__( 'Settings updated.', 'nitropack' ),
					'est_cachewarmup_msg' => esc_html__( 'Estimating optimizations usage', 'nitropack' ),
				)
			);
			//select2
			wp_register_script( 'np-select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', array( 'jquery' ), NITROPACK_VERSION, true );
			wp_enqueue_script( 'np-select2' );
		}
	}
}
add_action( 'admin_enqueue_scripts', 'load_nitropack_scripts_styles' );


function nitropack_is_advanced_cache_allowed() {
	return ! in_array( nitropack_detect_hosting(), array(
		"pressable"
	) );
}


function nitropack_get_hosting_notice_file() {
	return nitropack_trailingslashit( NITROPACK_DATA_DIR ) . "hosting_notice";
}



function nitropack_dismiss_hosting_notice() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	$hostingNoticeFile = nitropack_get_hosting_notice_file();
	if ( WP_DEBUG ) {
		touch( $hostingNoticeFile );
	} else {
		@touch( $hostingNoticeFile );
	}
}




function nitropack_is_config_up_to_date() {
	$siteConfig = nitropack_get_site_config();
	return ! empty( $siteConfig ) && ! empty( $siteConfig["pluginVersion"] ) && $siteConfig["pluginVersion"] == NITROPACK_VERSION;
}

function nitropack_filter_non_original_cookies( &$cookies ) {
	global $np_originalRequestCookies;
	$ogNames = is_array( $np_originalRequestCookies ) ? array_keys( $np_originalRequestCookies ) : array();
	foreach ( $cookies as $name => $val ) {
		if ( ! in_array( $name, $ogNames ) ) {
			unset( $cookies[ $name ] );
		}
	}
}

function nitropack_add_meta_box() {
	$editor = get_option( "nitropack-canEditorClearCache" );
	$allowed_capabilities = current_user_can( 'manage_options' ) || current_user_can( 'nitropack_meta_box' ) || ( $editor && current_user_can( 'editor' ) );
	if ( $allowed_capabilities ) {
		foreach ( nitropack_get_cacheable_object_types() as $objectType ) {
			add_meta_box( 'nitropack_manage_cache_box', 'NitroPack', 'nitropack_print_meta_box', $objectType, 'side' );
		}
	}
}


// This is only used for post types that can have "single" pages
function nitropack_print_meta_box( $post ) {
	wp_enqueue_script( 'nitropack_metabox_js', plugin_dir_url( __FILE__ ) . 'view/javascript/metabox.js?np_v=' . NITROPACK_VERSION, true );
	wp_localize_script( 'nitropack_metabox_js', 'metaboxdata', array( 'nitroNonce' => wp_create_nonce( NITROPACK_NONCE ), 'nitro_plugin_url' => plugin_dir_url( __FILE__ ) ) );

	$html = '';
	$html .= '<p><a class="button nitropack-invalidate-single" data-post_id="' . $post->ID . '" data-post_url="' . get_permalink( $post ) . '" style="width:100%;text-align:center;padding: 3px 0;">Invalidate cache</a></p>';
	$html .= '<p><a class="button nitropack-purge-single" data-post_id="' . $post->ID . '" data-post_url="' . get_permalink( $post ) . '" style="width:100%;text-align:center;padding: 3px 0;">Purge cache</a></p>';
	$html .= '<p id="nitropack-status-msg" style="display:none;"></p>';
	echo $html;
}

function get_nitropack_sdk( $siteId = null, $siteSecret = null, $urlOverride = NULL, $forwardExceptions = false ) {
	return get_nitropack()->getSdk( $siteId, $siteSecret, $urlOverride, $forwardExceptions );
}

function get_nitropack_integration_url( $integration, $nitro = null ) {
	if ( $nitro || ( null !== $nitro = get_nitropack_sdk() ) ) {
		return $nitro->integrationUrl( $integration );
	}

	return "#";
}

function register_nitropack_settings() {
	register_setting( NITROPACK_OPTION_GROUP, 'nitropack-enableCompression', array( 'default' => -1 ) );
}

function nitropack_get_layout() {
	$layout = "default";

	if ( nitropack_is_home() ) {
		$layout = "home";
	} else if ( nitropack_is_blogindex() ) {
		$layout = "blogindex";
	} else if ( is_page() ) {
		$layout = "page";
	} else if ( is_attachment() ) {
		$layout = "attachment";
	} else if ( is_author() ) {
		$layout = "author";
	} else if ( is_search() ) {
		$layout = "search";
	} else if ( is_tag() ) {
		$layout = "tag";
	} else if ( is_tax() ) {
		$layout = "taxonomy";
	} else if ( is_category() ) {
		$layout = "category";
	} else if ( nitropack_is_archive() ) {
		$layout = "archive";
	} else if ( is_feed() ) {
		$layout = "feed";
	} else if ( is_page() ) {
		$layout = "page";
	} else if ( is_single() ) {
		$layout = get_post_type();
	}

	return $layout;
}

function nitropack_sdk_invalidate( $url = NULL, $tag = NULL, $reason = NULL ) {

	$status = false;

	if ( null !== $nitro = get_nitropack_sdk() ) {
		try {
			$siteConfig = nitropack_get_site_config();
			$homeUrl = $siteConfig && ! empty( $siteConfig["home_url"] ) ? $siteConfig["home_url"] : get_home_url();

			if ( $tag ) {
				if ( is_array( $tag ) ) {
					$tag = array_map( 'nitropack_filter_tag', $tag );
				} else {
					$tag = nitropack_filter_tag( $tag );
				}
			}

			$nitro->invalidateCache( $url, $tag, $reason );

			try {

				if ( defined( 'NITROPACK_DEBUG_MODE' ) ) {
					do_action( 'nitropack_debug_invalidate', $url, $tag, $reason );
				}

				do_action( 'nitropack_integration_purge_url', $homeUrl );

				if ( $tag ) {
					do_action( 'nitropack_integration_purge_all' );
				} else if ( $url ) {
					do_action( 'nitropack_integration_purge_url', $url );
				} else {
					do_action( 'nitropack_integration_purge_all' );
				}
			} catch (\Exception $e) {
				// Exception while signaling 3rd party integration addons to purge their cache
			}
		} catch (\Exception $e) {
			$status = false;
		}

		$status = true;
	}

	return $status;
}

/* Start Heartbeat Related Functions */
function nitropack_is_heartbeat_needed() {
	return ! nitropack_is_optimizer_request() &&
		! nitropack_is_amp_page() &&
		! nitropack_is_heartbeat_running() &&
		( ! nitropack_is_heartbeat_completed() || time() - nitropack_last_heartbeat() > NITROPACK_HEARTBEAT_INTERVAL );
}

function nitropack_print_heartbeat_script() {
	if ( nitropack_is_heartbeat_needed() ) {
		if ( defined( "NITROPACK_HEARTBEAT_PRINTED" ) )
			return;
		define( "NITROPACK_HEARTBEAT_PRINTED", true );
		echo apply_filters( "nitro_script_output", nitropack_get_heartbeat_script() );
	}
}

function nitropack_get_heartbeat_script() {
	$siteConfig = nitropack_get_site_config();
	if ( $siteConfig && ! empty( $siteConfig["siteId"] ) && ! empty( $siteConfig["siteSecret"] ) ) {
		if ( null !== $nitro = get_nitropack_sdk( $siteConfig["siteId"], $siteConfig["siteSecret"] ) ) {
			if ( is_admin() ) {
				$credentials = "same-origin";
			} else {
				$credentials = "omit";
			}

			return "
<script nitro-exclude>
    var heartbeatData = new FormData(); heartbeatData.append('nitroHeartbeat', '1');
    fetch(location.href, {method: 'POST', body: heartbeatData, credentials: '$credentials'});
</script>";
		}
	}
}

function is_valid_nitropack_heartbeat() {
	return ! empty( $_POST['nitroHeartbeat'] );
}

function nitropack_get_heartbeat_file() {
	if ( null !== $nitro = get_nitropack_sdk() ) {
		return nitropack_trailingslashit( $nitro->getCacheDir() ) . "heartbeat";
	} else {
		return nitropack_trailingslashit( NITROPACK_DATA_DIR ) . "heartbeat";
	}
}

function nitropack_last_heartbeat() {
	if ( null !== $nitro = get_nitropack_sdk() ) {
		try {
			return \NitroPack\SDK\Filesystem::fileMTime( nitropack_get_heartbeat_file() );
		} catch (\Exception $e) {
			return 0;
		}
	}
}

function nitropack_is_heartbeat_running() {
	if ( null !== $nitro = get_nitropack_sdk() ) {
		try {
			$heartbeatContent = \NitroPack\SDK\Filesystem::fileGetContents( nitropack_get_heartbeat_file() );
			if ( $heartbeatContent == "1" ) {
				return time() - nitropack_last_heartbeat() < NITROPACK_HEARTBEAT_INTERVAL;
			}
		} catch (\Exception $e) {
			return false;
		}
	}
}

function nitropack_is_heartbeat_completed() {
	if ( null !== $nitro = get_nitropack_sdk() ) {
		try {
			$heartbeatContent = \NitroPack\SDK\Filesystem::fileGetContents( nitropack_get_heartbeat_file() );
			return $heartbeatContent == "0"; // 0 - Job Done, 1 - Job Running, 2 - Job Needs Repeat
		} catch (\Exception $e) {
			return true;
		}
	}
}

function nitropack_handle_heartbeat() {
	// TODO: Lock the file before checking this
	if ( nitropack_is_heartbeat_running() )
		return;

	session_write_close();
	if ( null !== $nitro = get_nitropack_sdk() ) {
		try {
			$success = true;
			\NitroPack\SDK\Filesystem::filePutContents( nitropack_get_heartbeat_file(), 1 );
			if ( nitropack_healthcheck() ) {
				$success &= nitropack_flush_backlog();
			}
			$success &= nitropack_cache_cleanup();

			if ( $success ) {
				\NitroPack\SDK\Filesystem::filePutContents( nitropack_get_heartbeat_file(), 0 );
			} else {
				\NitroPack\SDK\Filesystem::filePutContents( nitropack_get_heartbeat_file(), 2 );
			}
		} catch (\Exception $e) {
			return false;
		}
	}
	exit;
}

function nitropack_healthcheck() {
	if ( null !== $nitro = get_nitropack_sdk() ) {
		return $nitro->getHealthStatus() == \NitroPack\SDK\HealthStatus::HEALTHY || $nitro->checkHealthStatus() == \NitroPack\SDK\HealthStatus::HEALTHY;
	}
	return true;
}

function nitropack_flush_backlog() {
	if ( null !== $nitro = get_nitropack_sdk() ) {
		try {
			if ( $nitro->backlog->exists() ) {
				return $nitro->backlog->replay( 30 );
			}
		} catch (\NitroPack\SDK\BacklogReplayTimeoutException $e) {
			$nitro->backlog->delete();
			return nitropack_sdk_purge( NULL, NULL, "Full purge after backlog timeout" );
		} catch (\Exception $e) {
			return false;
		}
	}
	return true;
}

function nitropack_cache_cleanup() {
	if ( null !== $nitro = get_nitropack_sdk() ) {
		$cacheDirParent = dirname( $nitro->getCacheDir() );
		$entries = scandir( $cacheDirParent );
		foreach ( $entries as $entry ) {
			if ( strpos( $entry, ".stale." ) !== false ) {
				$cacheDir = nitropack_trailingslashit( $cacheDirParent ) . $entry;
				try {
					\NitroPack\SDK\Filesystem::deleteDir( $cacheDir );
				} catch (\Exception $e) {
					// TODO: Log this
					return false;
				}
			}
		}
	}
	return true;
}
/* End Heartbeat Related Functions */

function nitropack_sdk_purge( $url = NULL, $tag = NULL, $reason = NULL, $type = \NitroPack\SDK\PurgeType::COMPLETE ) {

	$status = false;

	if ( null !== $nitro = get_nitropack_sdk() ) {
		try {
			$siteConfig = nitropack_get_site_config();
			$homeUrl = $siteConfig && ! empty( $siteConfig["home_url"] ) ? $siteConfig["home_url"] : get_home_url();

			if ( $tag ) {
				if ( is_array( $tag ) ) {
					$tag = array_map( 'nitropack_filter_tag', $tag );
				} else {
					$tag = nitropack_filter_tag( $tag );
				}
			}

			if ( ! $url && ! $tag ) {
				$nitro->purgeLocalCache( true );
			}

			$nitro->purgeCache( $url, $tag, $type, $reason );

			if ( defined( 'NITROPACK_DEBUG_MODE' ) ) {
				do_action( 'nitropack_debug_purge', $url, $tag, $reason );
			}

			try {
				do_action( 'nitropack_integration_purge_url', $homeUrl );

				if ( $tag ) {
					do_action( 'nitropack_integration_purge_all' );
				} else if ( $url ) {
					do_action( 'nitropack_integration_purge_url', $url );
				} else {
					do_action( 'nitropack_integration_purge_all' );
				}
			} catch (\Exception $e) {
				// Exception while signaling 3rd party integration addons to purge their cache
			}
		} catch (\Exception $e) {
			$status = false;
		}

		$status = true;
	}

	return $status;
}

/**
 * @param string|null $url
 * @return bool
 */
function nitropack_sdk_purge_local( $url = NULL ) {
	if ( null === $nitro = get_nitropack_sdk() ) {
		return false;
	}

	try {
		if ( $url ) {
			$nitro->purgeLocalUrlCache( $url );
			do_action( 'nitropack_integration_purge_url', $url );
			return true;
		}

		$nitro->purgeLocalCache( true );

		try {
			do_action( 'nitropack_integration_purge_all' );
		} catch (\Exception $e) {
			// Exception while signaling our 3rd party integration addons to purge their cache
		}

		return true;
	} catch (\Exception $e) {
		return false;
	}
}

/**
 * @param string|null $url
 * @return bool
 */
function nitropack_sdk_invalidate_local( $url = NULL ) {
	if ( null === $nitro = get_nitropack_sdk() ) {
		return false;
	}

	try {
		if ( $url ) {
			$nitro->invalidateLocalUrlCache( $url );
			do_action( 'nitropack_integration_purge_url', $url );
			return true;
		}

		$nitro->invalidateLocalCache( true );

		try {
			do_action( 'nitropack_integration_purge_all' );
		} catch (\Exception $e) {
			// Exception while signaling our 3rd party integration addons to purge their cache
		}

		return true;
	} catch (\Exception $e) {
		return false;
	}
}

function nitropack_sdk_delete_backlog() {
	if ( null !== $nitro = get_nitropack_sdk() ) {
		try {
			if ( $nitro->backlog->exists() ) {
				$nitro->backlog->delete();
			}
		} catch (\Exception $e) {
			return false;
		}

		return true;
	}

	return false;
}

function nitropack_purge( $url = NULL, $tag = NULL, $reason = NULL ) {
	if ( $tag != "pageType:home" ) {
		$siteConfig = nitropack_get_site_config();
		$homeUrl = $siteConfig && ! empty( $siteConfig["home_url"] ) ? $siteConfig["home_url"] : get_home_url();
		nitropack_log_invalidate( $homeUrl, "pageType:home", $reason );
	}

	if ( $tag != "pageType:archive" ) {
		nitropack_log_invalidate( NULL, "pageType:archive", $reason );
	}

	nitropack_log_purge( $url, $tag, $reason );
}

function nitropack_log_purge( $url = NULL, $tag = NULL, $reason = NULL ) {
	global $np_loggedPurges;
	if ( $tag && is_array( $tag ) ) {
		foreach ( $tag as $tagSingle ) {
			nitropack_log_purge( $url, $tagSingle, $reason );
		}
		return;
	}

	$keyBase = "";
	if ( $url ) {
		$keyBase .= $url;
	}

	if ( $tag ) {
		$tag = nitropack_filter_tag( $tag );
		$keyBase .= $tag;
	}

	$purgeRequestKey = md5( $keyBase );
	if ( is_array( $np_loggedPurges ) && array_key_exists( $purgeRequestKey, $np_loggedPurges ) ) {
		$np_loggedPurges[ $purgeRequestKey ]["reason"] = $reason;
		$np_loggedPurges[ $purgeRequestKey ]["priority"]++;
	} else {
		$np_loggedPurges[ $purgeRequestKey ] = array(
			"url" => $url,
			"tag" => $tag,
			"reason" => $reason,
			"priority" => 1
		);
	}
}

function nitropack_invalidate( $url = NULL, $tag = NULL, $reason = NULL ) {
	if ( $tag != "pageType:home" ) {
		$siteConfig = nitropack_get_site_config();
		$homeUrl = $siteConfig && ! empty( $siteConfig["home_url"] ) ? $siteConfig["home_url"] : get_home_url();
		nitropack_log_invalidate( $homeUrl, "pageType:home", $reason );
	}

	if ( $tag != "pageType:archive" ) {
		nitropack_log_invalidate( NULL, "pageType:archive", $reason );
	}

	nitropack_log_invalidate( $url, $tag, $reason );
}

function nitropack_log_invalidate( $url = NULL, $tag = NULL, $reason = NULL ) {
	global $np_loggedInvalidations;
	if ( $tag && is_array( $tag ) ) {
		foreach ( $tag as $tagSingle ) {
			nitropack_log_invalidate( $url, $tagSingle, $reason );
		}
		return;
	}

	$keyBase = "";
	if ( $url ) {
		$keyBase .= $url;
	}

	if ( $tag ) {
		$tag = nitropack_filter_tag( $tag );
		$keyBase .= $tag;
	}

	$invalidateRequestKey = md5( $keyBase );
	if ( is_array( $np_loggedInvalidations ) && array_key_exists( $invalidateRequestKey, $np_loggedInvalidations ) ) {
		$np_loggedInvalidations[ $invalidateRequestKey ]["reason"] = $reason;
		$np_loggedInvalidations[ $invalidateRequestKey ]["priority"]++;
	} else {
		$np_loggedInvalidations[ $invalidateRequestKey ] = array(
			"url" => $url,
			"tag" => $tag,
			"reason" => $reason,
			"priority" => 1
		);
	}
}

function nitropack_queue_sort( $a, $b ) {
	if ( $a["priority"] == $b["priority"] ) {
		return 0;
	}
	return ( $a["priority"] < $b["priority"] ) ? -1 : 1;
}

function nitropack_execute_purges() {
	global $np_loggedPurges;
	// if (!empty($_GET["action"]) && ($_GET["action"] === "edit") && !empty($_GET["meta-box-loader"])) {
	//     return;
	// }
	if ( ! empty( $np_loggedPurges ) ) {
		uasort( $np_loggedPurges, "nitropack_queue_sort" );
		foreach ( $np_loggedPurges as $requestKey => $data ) {
			nitropack_sdk_purge( $data["url"], $data["tag"], $data["reason"] );
		}
	}
}

function nitropack_execute_invalidations() {
	global $np_loggedInvalidations;
	// if (!empty($_GET["action"]) && ($_GET["action"] === "edit") && !empty($_GET["meta-box-loader"])) {
	//     return;
	// }
	if ( ! empty( $np_loggedInvalidations ) ) {
		uasort( $np_loggedInvalidations, "nitropack_queue_sort" );
		foreach ( $np_loggedInvalidations as $requestKey => $data ) {
			nitropack_sdk_invalidate( $data["url"], $data["tag"], $data["reason"] );
		}
	}
}

function nitropack_execute_warmups() {
	if ( ! empty( $_GET["action"] ) && ( $_GET["action"] === "edit" ) && ! empty( $_GET["meta-box-loader"] ) ) {
		return;
	}

	try {
		if ( ! empty( \NitroPack\WordPress\NitroPack::$np_loggedWarmups ) && ( null !== $nitro = get_nitropack_sdk() ) ) {
			$warmupStats = $nitro->getApi()->getWarmupStats();
			if ( ! empty( $warmupStats["status"] ) ) {
				foreach ( array_unique( \NitroPack\WordPress\NitroPack::$np_loggedWarmups ) as $url ) {
					$nitro->getApi()->runWarmup( $url );
				}
			}
		}
	} catch (\Exception $e) {
	}
}

function nitropack_fetch_config() {
	if ( null !== $nitro = get_nitropack_sdk() ) {
		try {
			$nitro->fetchConfig();
		} catch (\Exception $e) {
		}
	}
}

function nitropack_theme_handler( $event = NULL ) {
	if ( ! get_option( "nitropack-autoCachePurge", 1 ) )
		return;

	$msg = $event ? $event : 'Theme switched';

	try {
		nitropack_sdk_purge( NULL, NULL, $msg ); // purge entire cache
	} catch (\Exception $e) {
	}
}

function nitropack_purge_cache() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	try {
		if ( nitropack_sdk_purge( NULL, NULL, 'Light purge of all caches', \NitroPack\SDK\PurgeType::LIGHT_PURGE ) ) {
			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'Light purge of all caches' );
			nitropack_json_and_exit( array(
				"type" => "success",
				"message" => __( 'Cache has been purged successfully!', 'nitropack' )
			) );
		}
	} catch (\Exception $e) {
		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Light purge of all caches. Error: ' . $e );
	}
	nitropack_json_and_exit( array(
		"type" => "error",
		"message" => __( 'Error! There was an error and the cache was not purged!', 'nitropack' )
	) );
}

function nitropack_invalidate_cache() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	try {
		if ( nitropack_sdk_invalidate( NULL, NULL, 'Manual invalidation of all pages' ) ) {
			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'Manual invalidation of all pages' );
			nitropack_json_and_exit( array(
				"type" => "success",
				"message" => __( 'Cache has been invalidated successfully!', 'nitropack' )

			) );
		}
	} catch (\Exception $e) {
		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Manual invalidation of all pages. Error: ' . $e );
	}

	nitropack_json_and_exit( array(
		"type" => "error",
		"message" => __( 'There was an error and the cache was not invalidated!', 'nitropack' )
	) );
}

function nitropack_clear_residual_cache() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	$gde = ! empty( $_POST["gde"] ) ? $_POST["gde"] : NULL;
	if ( $gde && array_key_exists( $gde, NitroPack\Integration\Plugin\RC::$modules ) ) {
		$result = call_user_func( array( NitroPack\Integration\Plugin\RC::$modules[ $gde ], "clearCache" ) ); // This needs to be like this because of compatibility with PHP 5.6
		if ( ! in_array( false, $result ) ) {
			nitropack_json_and_exit( array(
				"type" => "success",
				"message" => __( 'Success! The residual cache has been cleared successfully!', 'nitropack' )
			) );
		}
	}
	nitropack_json_and_exit( array(
		"type" => "error",
		"message" => __( 'Error! There was an error clearing the residual cache!', 'nitropack' )
	) );
}

function nitropack_json_and_exit( $array ) {
	if ( nitropack_is_wp_cli() ) {
		$type = NULL;
		if ( array_key_exists( "status", $array ) ) {
			$type = $array["status"];
		} else if ( array_key_exists( "type", $array ) ) {
			$type = $array["type"];
		}

		if ( $type && array_key_exists( "message", $array ) ) {
			if ( $type == "success" ) {
				WP_CLI::success( $array["message"] );
			} else {
				WP_CLI::error( $array["message"] );
			}
		}
	} else {
		echo json_encode( $array );
	}
	exit;
}
function nitropack_admin_toast_msgs( $type ) {
	if ( $type === 'success' ) {
		$msg = esc_html__( 'Settings updated.', 'nitropack' );
	} else {
		$msg = esc_html__( 'Something went wrong.', 'nitropack' );
	}
	return $msg;
}
function nitropack_verify_ajax_nonce( $request_data ) {
	// If not an ajax request
	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
		return;
	}

	// If nonce fails verification
	if ( empty( $request_data['nonce'] ) || ! wp_verify_nonce( $request_data['nonce'], NITROPACK_NONCE ) ) {
		wp_die( 'Unauthorized request' );
	}
}

function nitropack_has_post_important_change( $post ) {
	$prevPost = nitropack_get_post_pre_update( $post );
	return $prevPost && ( $prevPost->post_title != $post->post_title || $prevPost->post_name != $post->post_name || $prevPost->post_excerpt != $post->post_excerpt );
}

function nitropack_purge_single_cache() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	if ( ! empty( $_POST["postId"] ) && is_numeric( $_POST["postId"] ) ) {
		$postId = $_POST["postId"];
		$postUrl = ! empty( $_POST["postUrl"] ) ? $_POST["postUrl"] : NULL;
		$reason = sprintf( "Manual purge of post %s via the WordPress admin panel", $postId );
		$tag = $postId > 0 ? "single:$postId" : NULL;

		if ( $postUrl ) {
			if ( is_array( $postUrl ) ) {
				foreach ( $postUrl as &$url ) {
					$url = nitropack_sanitize_url_input( $url );
				}
			} else {
				$postUrl = nitropack_sanitize_url_input( $postUrl );
				$reason = "Manual purge of " . $postUrl;
			}
		}

		try {
			if ( nitropack_sdk_purge( $postUrl, $tag, $reason ) ) {
				NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'Manual purge of post ' . $postId . ' via WordPress.' );
				nitropack_json_and_exit( array(
					"type" => "success",
					"message" => __( 'Success! Cache has been purged successfully!', 'nitropack' )
				) );
			}
		} catch (\Exception $e) {
			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Manual purge of post ' . $postId . ' via WordPress. Error: ' . $e );
		}
	}

	nitropack_json_and_exit( array(
		"type" => "error",
		"message" => __( 'Error! There was an error and the cache was not purged!', 'nitropack' )
	) );
}

function nitropack_purge_entire_cache() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	try {
		if ( nitropack_sdk_purge( null, null, 'Manual purge of all pages' ) ) {
			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'Manual purge of all pages' );
			nitropack_json_and_exit( [ 
				"type" => "success",
				"message" => __( 'Success! Cache has been purged successfully!', 'nitropack' )
			] );
		}
	} catch (\Exception $e) {
		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Manual purge of all pages. Error: ' . $e );
	}

	nitropack_json_and_exit( [ 
		"type" => "error",
		"message" => __( 'Error! There was an error and the cache was not purged!', 'nitropack' )
	] );
}

function nitropack_invalidate_single_cache() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	if ( ! empty( $_POST["postId"] ) && is_numeric( $_POST["postId"] ) ) {
		$postId = $_POST["postId"];
		$postUrl = ! empty( $_POST["postUrl"] ) ? $_POST["postUrl"] : NULL;
		$reason = sprintf( "Manual invalidation of post %s via the WordPress admin panel", $postId );
		$tag = $postId > 0 ? "single:$postId" : NULL;

		if ( $postUrl ) {
			if ( is_array( $postUrl ) ) {
				foreach ( $postUrl as &$url ) {
					$url = nitropack_sanitize_url_input( $url );
				}
			} else {
				$postUrl = nitropack_sanitize_url_input( $postUrl );
				$reason = "Manual invalidation of " . $postUrl;
			}
		}

		try {
			if ( nitropack_sdk_invalidate( $postUrl, $tag, $reason ) ) {
				NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'Manual invalidation of post ' . $postId . ' via WordPress.' );
				nitropack_json_and_exit( array(
					"type" => "success",
					"message" => __( 'Success! Cache has been invalidated successfully!', 'nitropack' )
				) );
			}
		} catch (\Exception $e) {
			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Manual invalidation of post ' . $postId . ' via WordPress. Error: ' . $e );
		}
	}

	nitropack_json_and_exit( array(
		"type" => "error",
		"message" => __( 'Error! There was an error and the cache was not invalidated!', 'nitropack' )
	) );
}

function nitropack_clean_post_cache( $post, $taxonomies = NULL, $hasImportantChangeInPost = NULL, $reason = NULL, $usePurge = false ) {
	try {
		$postID = $post->ID;
		$postType = isset( $post->post_type ) ? $post->post_type : "post";
		$nicePostTypeLabel = nitropack_get_nice_post_type_label( $postType );
		$reason = $reason ? $reason : sprintf( "Updated %s '%s'", $nicePostTypeLabel, $post->post_title );
		$cacheableObjectTypes = nitropack_get_cacheable_object_types();

		if ( in_array( $postType, $cacheableObjectTypes ) ) {
			if ( $usePurge ) {
				// We only purge the single pages because they have to immediately stop serving cache
				// These pages no longer exists and if their URL is requested we must not server cache
				nitropack_purge( NULL, "single:$postID", $reason );
			} else {
				nitropack_invalidate( NULL, "single:$postID", $reason );
			}

			nitropack_invalidate( NULL, "post:$postID", $reason );

			if ( $hasImportantChangeInPost === NULL ) {
				$hasImportantChangeInPost = nitropack_has_post_important_change( $post );
			}
			if ( $taxonomies === NULL ) {
				if ( $hasImportantChangeInPost ) { // This change should be reflected in all taxonomy pages
					$taxonomies = array( 'related' => nitropack_get_taxonomies( $post ) );
				} else { // No important change, so only update taxonomy pages which have been added or removed from the post
					$taxonomies = nitropack_get_taxonomies_for_update( $post );
				}
			}
			if ( $taxonomies ) {
				if ( ! empty( $taxonomies['added'] ) ) { // taxonomies that the post was just added to, must purge all pages for these taxonomies
					foreach ( $taxonomies['added'] as $term_taxonomy_id ) {
						nitropack_invalidate( NULL, "tax:$term_taxonomy_id", $reason );
					}
				}
				if ( ! empty( $taxonomies['deleted'] ) ) { // taxonomy pages that the post was just removed from (also accounts for paginations via the taxpost: tag instead of only tax:)
					foreach ( $taxonomies['deleted'] as $term_taxonomy_id ) {
						nitropack_invalidate( NULL, "taxpost:$term_taxonomy_id:$postID", $reason );
					}
				}
				if ( ! empty( $taxonomies['related'] ) ) { // taxonomy pages that the post is linked to (also accounts for paginations via the taxpost: tag instead of only tax:)
					foreach ( $taxonomies['related'] as $term_taxonomy_id ) {
						nitropack_invalidate( NULL, "taxpost:$term_taxonomy_id:$postID", $reason );
					}
				}
			}
		} else {
			if ( $post->public ) {
				nitropack_invalidate( NULL, "post:$postID", $reason );
			}

			$posts = get_post_ancestors( $postID );
			foreach ( $posts as $parentID ) {
				$parent = get_post( $parentID );
				nitropack_clean_post_cache( $parent, false, false, $reason );
			}
		}
	} catch (\Exception $e) {
	}
}

function nitropack_get_nice_post_type_label( $postType ) {
	$postTypes = get_post_types( array(
		"name" => $postType
	), "objects" );

	return ! empty( $postTypes[ $postType ] ) && ! empty( $postTypes[ $postType ]->labels ) ? $postTypes[ $postType ]->labels->singular_name : $postType;
}

function nitropack_handle_comment_transition( $new, $old, $comment ) {
	if ( ! get_option( "nitropack-autoCachePurge", 1 ) )
		return;

	try {
		$postID = $comment->comment_post_ID;
		$post = get_post( $postID );
		$postType = isset( $post->post_type ) ? $post->post_type : "post";
		$cacheableObjectTypes = nitropack_get_cacheable_object_types();

		if ( in_array( $postType, $cacheableObjectTypes ) ) {
			nitropack_invalidate( NULL, "single:" . $postID, sprintf( "Invalidation of '%s' due to changing related comment status", $post->post_title ) );
		}
	} catch (\Exception $e) {
		// TODO: Log the error
	}
}

function nitropack_handle_comment_post( $commentID, $isApproved ) {
	if ( ! get_option( "nitropack-autoCachePurge", 1 ) || $isApproved !== 1 )
		return;

	try {
		$comment = get_comment( $commentID );
		$postID = $comment->comment_post_ID;
		$post = get_post( $postID );
		nitropack_invalidate( NULL, "single:" . $postID, sprintf( "Invalidation of '%s' due to posting a new approved comment", $post->post_title ) );
	} catch (\Exception $e) {
		// TODO: Log the error
	}
}

function custom_reduce_stock_after_order_placed( $order ) {

	if ( (int) get_option( 'nitropack-stockReduceStatus' ) !== 1 )
		return;

	$items = $order->get_items();

	foreach ( $items as $item ) {
		$product_id = $item->get_product_id();
		$product_url = get_permalink( $product_id );

		$stock_quantity = get_post_meta( $product_id, '_stock', true );

		if ( $stock_quantity > 0 ) {
			nitropack_sdk_invalidate( $product_url, NULL, 'Invalidated to adjust order quantity' );
		}
	}
}

function nitropack_detect_changes_and_clean_post_cache( $post ) {

	$post_before = nitropack_get_post_pre_update( $post );
	$ignoredComparisonKeys = array( 'post_modified', 'post_modified_gmt' );
	$canCleanPostCache = false;
	$postStatesEqual = nitropack_compare_posts( (array) $post_before, (array) $post, $ignoredComparisonKeys );

	if ( $postStatesEqual ) {
		$taxCurrent = nitropack_get_taxonomies( $post );
		$taxPreUpdate = nitropack_get_taxonomies_pre_update( $post );
		$taxAreEqual = nitropack_compare_posts( $taxCurrent, $taxPreUpdate );
		if ( $taxAreEqual ) {
			$metaCurrent = get_post_meta( $post->ID );
			$metaPreUpdate = nitropack_get_meta_pre_update( $post );
			$metaIsEqual = nitropack_compare_posts( $metaCurrent, $metaPreUpdate );
			if ( ! $metaIsEqual ) {
				$canCleanPostCache = true;
			}
		} else {
			$canCleanPostCache = true;
		}
	} else {
		$canCleanPostCache = true;
	}

	if ( $canCleanPostCache ) {
		\NitroPack\WordPress\NitroPack::$np_loggedWarmups[] = get_permalink( $post );
		nitropack_clean_post_cache( $post );
		define( 'NITROPACK_PURGE_CACHE', true );
	}
}


function nitropack_handle_post_transition( $new, $old, $post ) {
	if ( wp_is_post_revision( $post ) )
		return;
	if ( ! empty( $post->ID ) && in_array( $post->ID, \NitroPack\WordPress\NitroPack::$ignoreUpdatePostIDs ) )
		return;
	if ( ! get_option( "nitropack-autoCachePurge", 1 ) )
		return;

	try {
		if ( $new === "auto-draft" || ( $new === "draft" && $old === "auto-draft" ) || ( $new === "draft" && $old != "publish" ) || $new === "inherit" ) { // Creating a new post or draft, don't do anything for now. 
			return;
		}

		$ignoredPostTypes = array(
			"revision",
			"scheduled-action",
			"flamingo_contact",
			"carts"/*WooCommerce Cart Reports*/
		);

		$nicePostTypes = array(
			"post" => "Post",
			"page" => "Page",
			"tribe_events" => "Calendar Event",
		);
		$postType = isset( $post->post_type ) ? $post->post_type : "post";
		$nicePostTypeLabel = nitropack_get_nice_post_type_label( $postType );

		if ( in_array( $postType, $ignoredPostTypes ) )
			return;

		switch ( $postType ) {
			case "nav_menu_item":
				nitropack_invalidate( NULL, NULL, sprintf( "Invalidation of all pages due to modifying menu entries" ) );
				break;
			case "customize_changeset":
				nitropack_invalidate( NULL, NULL, sprintf( "Invalidation of all pages due to applying appearance customization" ) );
				break;
			case "custom_css":
				nitropack_invalidate( NULL, NULL, sprintf( "Invalidation of all pages due to modifying custom CSS" ) );
				break;
			default:
				if ( $new == "future" ) {
					nitropack_clean_post_cache( $post, array( 'added' => nitropack_get_taxonomies( $post ) ), true, sprintf( "Invalidate related pages due to scheduling %s '%s'", $nicePostTypeLabel, $post->post_title ) );
				} else if ( $new === 'publish' && $old === 'trash' ) {
					nitropack_clean_post_cache( $post, array( 'added' => nitropack_get_taxonomies( $post ) ), true, sprintf( "Invalidate related pages due to restoring %s '%s'", $nicePostTypeLabel, $post->post_title ) );
				} else if ( $new == "publish" && $old != "publish" ) {
					/* Handle first publish */
					// set_transient($post->ID . '_np_first_publish', $post, 120);
					\NitroPack\WordPress\NitroPack::$np_loggedWarmups[] = get_permalink( $post->ID );
					if ( ! defined( 'NITROPACK_PURGE_CACHE' ) ) {
						nitropack_clean_post_cache( $post, array( 'added' => nitropack_get_taxonomies( $post ) ), true, sprintf( "Invalidate related pages due to publishing %s '%s'", $nicePostTypeLabel, $post->post_title ), true );
					}
					if ( $post->post_type === 'post' ) {
						nitropack_invalidate( NULL, "pageType:blogindex", 'Invalidation of blog page due to changing related post status' );
					}
				} else if ( $new == "trash" && $old == "publish" ) {
					nitropack_clean_post_cache( $post, array( 'deleted' => nitropack_get_taxonomies( $post ) ), true, sprintf( "Invalidate related pages due to deleting %s '%s'", $nicePostTypeLabel, $post->post_title ), true );
				} else if ( $new == "private" && $old == "publish" ) {
					nitropack_clean_post_cache( $post, array( 'deleted' => nitropack_get_taxonomies( $post ) ), true, sprintf( "Invalidate related pages due to making %s '%s' private", $nicePostTypeLabel, $post->post_title ), true );
				} else if ( $new == "draft" && $old == "publish" ) {
					nitropack_clean_post_cache( $post, array( 'deleted' => nitropack_get_taxonomies( $post ) ), true, sprintf( "Invalidate related pages due to making %s '%s' a draft", $nicePostTypeLabel, $post->post_title ), true );
				} else if ( $new != "trash" ) {
					if ( ! defined( 'NITROPACK_PURGE_CACHE' ) ) {
						nitropack_detect_changes_and_clean_post_cache( $post );
					}
					if ( $new == 'publish' ) {
						\NitroPack\WordPress\NitroPack::$np_loggedWarmups[] = get_permalink( $post->ID );
					}
				}
				break;
		}
	} catch (\Exception $e) {
		// TODO: Log the error
	}
}

function nitropack_handle_first_publish( $post_id ) {
	$first_publish_post = get_transient( $post_id . '_np_first_publish', false );

	if ( ! $first_publish_post ) {
		return;
	}

	try {
		nitropack_clean_post_cache( $first_publish_post, array( 'added' => nitropack_get_taxonomies( $first_publish_post ) ), true, sprintf( "Invalidate related pages due to publishing %s '%s'", $first_publish_post->nicePostTypeLabel, $first_publish_post->post_title ) );
		nitropack_invalidate( NULL, "pageType:blogindex", 'Invalidation of blog page due to changing related post status' );
		delete_transient( $post_id . '_np_first_publish' );
	} catch (\Exception $e) {
		// TODO: Log the error
	}
}

function nitropack_postmeta_updated( $meta_id, $object_id, $meta_key, $meta_value ) {
	if ( $meta_key !== '_edit_lock' ) {
		if ( get_post_type( $object_id ) === 'product' ) {
			! defined( 'NITROPACK_PURGE_PRODUCT' ) && define( 'NITROPACK_PURGE_PRODUCT', true );
		}
	}
}

function nitropack_sot( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
	if ( ! get_option( "nitropack-autoCachePurge", 1 ) )
		return;

	$post = get_post( $object_id );
	$post_status = $post->post_status;

	if ( $post_status === 'auto-draft' || $post_status === 'draft' ) {
		return;
	}

	if ( ! defined( 'NITROPACK_PURGE_CACHE' ) ) {
		$purgeCache = ! nitropack_compare_posts( $tt_ids, $old_tt_ids );
		$cleanCache = $purgeCache ? "YES" : "NO";
		if ( $purgeCache ) {
			\NitroPack\WordPress\NitroPack::$np_loggedWarmups[] = get_permalink( $post );
			nitropack_clean_post_cache( $post );
			define( 'NITROPACK_PURGE_CACHE', true );
		}
	}
}

function nitropack_handle_product_updates( $product, $updated ) {
	if ( ! get_option( "nitropack-autoCachePurge", 1 ) )
		return;

	if ( ! defined( 'NITROPACK_PURGE_CACHE' ) && defined( 'NITROPACK_PURGE_PRODUCT' ) ) {
		try {
			$post = get_post( $product->get_id() );
			$reasons = 'updated ';
			$reasons .= implode( ',', $updated );
			\NitroPack\WordPress\NitroPack::$np_loggedWarmups[] = get_permalink( $post );
			nitropack_clean_post_cache( $post, NULL, true, sprintf( "Invalidate product '%s'. Reason '%s'", $product->get_name(), $reasons ) ); // Update the product page and all related pages, because a quantity change might have to add/remove "Out of stock" labels
			define( 'NITROPACK_PURGE_CACHE', true );
		} catch (\Exception $e) {
			// TODO: Log the error
		}
	}
}

function nitropack_post_link_listener( $permalink, $post, $leavename ) {
	if ( is_object( $post ) ) {
		nitropack_handle_the_post( $post );
	}

	return $permalink;
}

function nitropack_handle_the_post( $post ) {
	global $np_customExpirationTimes, $np_queriedObj;
	if ( defined( 'POSTEXPIRATOR_VERSION' ) ) {
		$postExpiryDate = get_post_meta( $post->ID, "_expiration-date", true );
		if ( ! empty( $postExpiryDate ) && $postExpiryDate > time() ) { // We only need to look at future dates
			$np_customExpirationTimes[] = $postExpiryDate;
		}
	}

	if ( function_exists( "sort_portfolio" ) ) { // Portfolio Sorting plugin
		$portfolioStartDate = get_post_meta( $post->ID, "start_date", true );
		$portfolioEndDate = get_post_meta( $post->ID, "end_date", true );
		if ( ! empty( $portfolioStartDate ) && strtotime( $portfolioStartDate ) > time() ) { // We only need to look at future dates
			$np_customExpirationTimes[] = strtotime( $portfolioStartDate );
		} else if ( ! empty( $portfolioEndDate ) && strtotime( $portfolioEndDate ) > time() ) { // We only need to look at future dates
			$np_customExpirationTimes[] = strtotime( $portfolioEndDate );
		}
	}

	$GLOBALS["NitroPack.tags"][ "post:" . $post->ID ] = 1;
	$GLOBALS["NitroPack.tags"][ "author:" . $post->post_author ] = 1;
	if ( $np_queriedObj ) {
		$GLOBALS["NitroPack.tags"][ "taxpost:" . $np_queriedObj->term_taxonomy_id . ":" . $post->ID ] = 1;
	}
}

function nitropack_ignore_post_updates( $postID ) {
	\NitroPack\WordPress\NitroPack::$ignoreUpdatePostIDs[] = $postID;
}

function nitropack_get_taxonomies( $post ) {
	$term_taxonomy_ids = array();
	$taxonomies = get_object_taxonomies( $post->post_type );
	foreach ( $taxonomies as $taxonomy ) {
		$terms = get_the_terms( $post->ID, $taxonomy );
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$term_taxonomy_ids[] = $term->term_taxonomy_id;
			}
		}
	}
	return $term_taxonomy_ids;
}

function nitropack_get_taxonomies_for_update( $post ) {
	$prevTaxonomies = nitropack_get_taxonomies_pre_update( $post );
	$newTaxonomies = nitropack_get_taxonomies( $post );
	$intersection = array_intersect( $newTaxonomies, $prevTaxonomies );
	$prevTaxonomies = array_diff( $prevTaxonomies, $intersection );
	$newTaxonomies = array_diff( $newTaxonomies, $intersection );
	return array(
		"added" => array_diff( $newTaxonomies, $prevTaxonomies ),
		"deleted" => array_diff( $prevTaxonomies, $newTaxonomies )
	);
}

function nitropack_get_post_pre_update( $post ) {
	return ! empty( \NitroPack\WordPress\NitroPack::$preUpdatePosts[ $post->ID ] ) ? \NitroPack\WordPress\NitroPack::$preUpdatePosts[ $post->ID ] : NULL;
}

function nitropack_get_taxonomies_pre_update( $post ) {
	return ! empty( \NitroPack\WordPress\NitroPack::$preUpdateTaxonomies[ $post->ID ] ) ? \NitroPack\WordPress\NitroPack::$preUpdateTaxonomies[ $post->ID ] : array();
}

function nitropack_get_meta_pre_update( $post ) {
	return ! empty( \NitroPack\WordPress\NitroPack::$preUpdateMeta[ $post->ID ] ) ? \NitroPack\WordPress\NitroPack::$preUpdateMeta[ $post->ID ] : array();
}

function nitropack_log_post_pre_update( $postID ) {
	if ( in_array( $postID, \NitroPack\WordPress\NitroPack::$ignoreUpdatePostIDs ) )
		return;


	$post = get_post( $postID );
	\NitroPack\WordPress\NitroPack::$preUpdatePosts[ $postID ] = $post;
	\NitroPack\WordPress\NitroPack::$preUpdateTaxonomies[ $postID ] = nitropack_get_taxonomies( $post );
	//Is post meta updated at this point? Or maybe this block should be moved to a different action?
	\NitroPack\WordPress\NitroPack::$preUpdateMeta[ $postID ] = get_post_meta( $postID );
}

function nitropack_log_product_pre_api_update( $product, $request, $creating ) {

	if ( ! $creating ) {

		$postID = $product->get_id();
		if ( in_array( $postID, \NitroPack\WordPress\NitroPack::$ignoreUpdatePostIDs ) )
			return;


		$post = get_post( $postID );
		\NitroPack\WordPress\NitroPack::$preUpdatePosts[ $postID ] = $post;
		\NitroPack\WordPress\NitroPack::$preUpdateTaxonomies[ $postID ] = nitropack_get_taxonomies( $post );
		//Is post meta updated at this point? Or maybe this block should be moved to a different action?
		\NitroPack\WordPress\NitroPack::$preUpdateMeta[ $postID ] = get_post_meta( $postID );
	}

	return $product;
}

function nitropack_compare_posts( array $p1, array $p2, $ignoredKeys = null ) {
	$p1keys = array_keys( $p1 );
	$p2keys = array_keys( $p2 );
	if ( count( $p1keys ) !== count( $p2keys ) ) {
		return false;
	}
	if ( array_diff( $p1keys, $p2keys ) ) {
		return false;
	}

	$isP1assoc = false;
	$expectedKey = 0;
	foreach ( $p2 as $i => $_ ) {
		if ( $i !== $expectedKey ) {
			$isP1assoc = true;
		}
		$expectedKey++;
	}

	$isP2assoc = false;
	$expectedKey = 0;
	foreach ( $p2 as $i => $_ ) {
		if ( $i !== $expectedKey ) {
			$isP2assoc = true;
		}
		$expectedKey++;
	}

	if ( $isP1assoc !== $isP2assoc ) {
		return false;
	}

	if ( ! $isP1assoc && ! $isP2assoc ) {
		sort( $p1 );
		sort( $p2 );
	}

	foreach ( $p1 as $poKey => $poVal ) {
		if ( $ignoredKeys && in_array( $poKey, $ignoredKeys, true ) ) {
			continue;
		}
		$checkpoint01 = is_array( $poVal );
		$checkpoint02 = is_array( $p2[ $poKey ] );
		if ( $checkpoint01 && $checkpoint02 ) {
			if ( ! nitropack_compare_posts( $poVal, $p2[ $poKey ], $ignoredKeys, 'Re:' ) ) { //'Re:' left for debug purpose to destinguish between main and recursive call
				return false;
			}
		} elseif ( ! $checkpoint01 && ! $checkpoint02 ) {
			if ( $poVal != $p2[ $poKey ] ) {
				return false;
			}
		} else {
			return false;
		}
	}
	return true;
}

function nitropack_filter_tag( $tag ) {
	return preg_replace( "/[^a-zA-Z0-9:]/", ":", $tag );
}

function nitropack_log_tags() {
	if ( ! empty( $GLOBALS["NitroPack.instance"] ) && ! empty( $GLOBALS["NitroPack.tags"] ) ) {
		$nitro = $GLOBALS["NitroPack.instance"];
		$layout = nitropack_get_layout();
		try {
			$config = $nitro->getConfig();
			$useHeader = ! empty( $config->TagsViaHeader );

			if ( $layout == "home" ) {
				if ( $useHeader ) {
					nitropack_header( "x-nitro-tags:pageType:home" );
				} else {
					$nitro->getApi()->tagUrl( $nitro->getUrl(), "pageType:home" );
				}
			} else if ( $layout == "archive" ) {
				if ( $useHeader ) {
					nitropack_header( "x-nitro-tags:pageType:archive" );
				} else {
					$nitro->getApi()->tagUrl( $nitro->getUrl(), "pageType:archive" );
				}
			} else {
				if ( $useHeader && count( $GLOBALS["NitroPack.tags"] ) <= 100 ) {
					nitropack_header( "x-nitro-tags:" . implode( "|", array_map( "nitropack_filter_tag", array_keys( $GLOBALS["NitroPack.tags"] ) ) ) );
				} else {
					$nitro->getApi()->tagUrl( $nitro->getUrl(), array_map( "nitropack_filter_tag", array_keys( $GLOBALS["NitroPack.tags"] ) ) );
				}
			}
		} catch (\Exception $e) {
		}
	}
}

function nitropack_extend_nonce_life( $life ) {
	// Nonce life should be extended only:
	//  - if NitroPack is connected for this site
	//  - if the current value is shorter than the life time of a cache file
	//  - if no user is logged in
	//  - for cacheable requests
	//
	// Reasons why we might need to extend the nonce life time even for requests that are not cacheable:
	//  - a request may be cachable at first, but become uncachable during changes at runtime or user actions on the page (example: log in via AJAX on a category page. Once logged in the page will not redirect, but if there is an infinite scroll it will stop working if we stop extending the nonce life time)
	//  - a request may seem cachable at first, but be determined uncachable during runtime (example: visit to a URL of a page whose post type does not match the enabled cacheable post types, or a cart, checkout page, etc.)

	if ( ( null !== $nitro = get_nitropack_sdk() ) ) {
		$siteConfig = nitropack_get_site_config();
		if ( $siteConfig && ! empty( $siteConfig["isDlmActive"] ) && ! empty( $siteConfig["dlm_downloading_url"] ) && ! empty( $siteConfig["dlm_download_endpoint"] ) ) {
			$currentUrl = $nitro->getUrl();
			if ( strpos( $currentUrl, $siteConfig["dlm_downloading_url"] ) !== false || strpos( $currentUrl, $siteConfig["dlm_download_endpoint"] ) !== false ) {
				// Do not modify the nonce times on pages of Download Monitor
				return $life;
			}
		}
		$cacheExpiration = $nitro->getConfig()->PageCache->ExpireTime;
		return $cacheExpiration > $life ? $cacheExpiration : $life; // Extend the life of cacheable nonces up to the cache expiration time if needed
	}
	return $life;
}

function nitropack_reconfigure_webhooks() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	$siteConfig = nitropack_get_site_config();

	if ( $siteConfig && ! empty( $siteConfig["siteId"] ) ) {
		$siteId = $siteConfig["siteId"];
		if ( null !== $nitro = get_nitropack_sdk() ) {
			$token = nitropack_generate_webhook_token( $siteId );
			try {
				nitropack_setup_webhooks( $nitro, $token );
				update_option( "nitropack-webhookToken", $token );
				nitropack_json_and_exit( array( "status" => "success", 'message' => __( 'Connection reconfigured successfully', 'nitropack' ) ) );
			} catch (\NitroPack\SDK\WebhookException $e) {
				NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Webhook Error: ' . $e );
				nitropack_json_and_exit( array( "status" => "error", "message" => __( 'Webhook Error: ', 'nitropack' ) . $e->getTraceAsString() ) );
			}
		} else {
			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Unable to get SDK instance' );
			nitropack_json_and_exit( array( "status" => "error", "message" => __( 'Unable to get SDK instance', 'nitropack' ) ) );
		}
	} else {
		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Incomplete site config. Please reinstall the plugin' );
		nitropack_json_and_exit( array( "status" => "error", "message" => __( 'Incomplete site config. Please reinstall the plugin!', 'nitropack' ) ) );
	}
}

function nitropack_generate_webhook_token( $siteId ) {
	return md5( __FILE__ . ":" . $siteId );
}

function nitropack_verify_connect_ajax() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	$siteId = ! empty( $_POST["siteId"] ) ? $_POST["siteId"] : "";
	$siteSecret = ! empty( $_POST["siteSecret"] ) ? $_POST["siteSecret"] : "";
	nitropack_verify_connect( $siteId, $siteSecret );
}

function nitropack_check_func_availability( $func_name ) {
	if ( function_exists( 'ini_get' ) ) {
		$existsResult = stripos( ini_get( 'disable_functions' ), $func_name ) === false;
	} else {
		$existsResult = function_exists( $func_name );
	}
	return $existsResult;
}

function nitropack_prevent_connecting( $nitroSDK ) {
	$remoteUrl = $nitroSDK->getApi()->getWebhook( "config" );
	if ( empty( $remoteUrl ) ) {
		return false;
	}
	$siteConfig = nitropack_get_site_config();
	$localUrl = new \NitroPack\Url\Url( $siteConfig && ! empty( $siteConfig["home_url"] ) ? $siteConfig["home_url"] : get_home_url() );
	$localHome = strtolower( $localUrl->getHost() . $localUrl->getPath() );
	$storedUrl = new \NitroPack\Url\Url( $remoteUrl );
	$remoteHome = strtolower( $storedUrl->getHost() . $storedUrl->getPath() );
	if ( $localHome === $remoteHome ) {
		return false;
	}
	return array( 'local' => $localHome, 'remote' => $remoteHome );
}

function nitropack_verify_connect( $siteId, $siteSecret ) {

	NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'Verifying connection to NitroPack API' );

	if ( ! nitropack_check_func_availability( 'stream_socket_client' ) ) {

		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'stream_socket_client function is not allowed by your host.' );

		nitropack_json_and_exit( array( "status" => "error", "message" => "stream_socket_client function is not allowed by your host. <a href=\"https://support.nitropack.io/hc/en-us/articles/360020898137\" target=\"_blank\" rel=\"noreferrer noopener\">Read more</a>" ) );
	}

	if ( ! nitropack_check_func_availability( 'stream_context_create' ) ) {
		// <a href=\"https://support.nitropack.io/hc/en-us/articles/360020898137\" target=\"_blank\" rel=\"noreferrer noopener\">Read more</a>
		// ^ Similar article needed on website for stream_context_create function
		nitropack_json_and_exit( array( "status" => "error", "message" => "stream_context_create function is not allowed by your host." ) );
	}

	if ( empty( $siteId ) || empty( $siteSecret ) ) {

		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Site ID and Site Secret cannot be empty' );

		nitropack_json_and_exit( array( "status" => "error", "message" => __( 'Site ID and Site Secret cannot be empty', 'nitropack' ) ) );
	}

	//remove tags and whitespaces
	$siteId = trim( esc_attr( $siteId ) );
	$siteSecret = trim( esc_attr( $siteSecret ) );

	if ( ! nitropack_validate_site_id( $siteId ) || ! nitropack_validate_site_secret( $siteSecret ) ) {

		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Invalid Site ID or Site Secret value' );

		nitropack_json_and_exit( array( "status" => "error", "message" => __( 'Invalid Site ID or Site Secret value', 'nitropack' ) ) );
	}

	try {
		$blogId = get_current_blog_id();
		if ( null !== $nitro = get_nitropack_sdk( $siteId, $siteSecret, NULL, true ) ) {
			if ( ! $nitro->checkHealthStatus() ) {

				NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Error when trying to communicate with NitroPack\'s servers. Current health status: ' . $nitro->getHealthStatus() );

				nitropack_json_and_exit( array(
					"status" => "error",
					"message" => __( 'Error when trying to communicate with NitroPack\'s servers. Please try again in a few minutes. If the issue persists, please', 'nitropack' ) . " <a href='https://support." . NITROPACK_HOST . "/hc/en-us' target='_blank'>contact us</a>."
				) );
			}

			$preventParing = apply_filters( 'nitropack_prevent_connect', nitropack_prevent_connecting( $nitro ) );
			if ( $preventParing ) {

				NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'It looks like another site ' . $preventParing['remote'] . ' is already connected using these credentials. Either disconnect it or register a new site in your NitroPack dashboard.' );

				nitropack_json_and_exit( array(
					"status" => "error",
					"message" => "It looks like another site <strong>({$preventParing['remote']})</strong> is already connected using these credentials. Either disconnect it or register a new site in your NitroPack dashboard.<br/>
                    <a href='https://support.nitropack.io/hc/en-us/articles/4405254569745' target='_blank' rel='noreferrer noopener'>Read more</a>"
				) );
			}
			$token = nitropack_generate_webhook_token( $siteId );
			get_nitropack()->settings->set_required_settings( $token );

			nitropack_setup_webhooks( $nitro, $token );

			// _icl_current_language is WPML cookie, it is added here for compatibility with this module
			$customVariationCookies = array( "np_wc_currency", "np_wc_currency_language", "_icl_current_language" );
			$variationCookies = $nitro->getApi()->getVariationCookies();
			foreach ( $variationCookies as $cookie ) {
				$index = array_search( $cookie["name"], $customVariationCookies );
				if ( $index !== false ) {
					array_splice( $customVariationCookies, $index, 1 );
				}
			}

			foreach ( $customVariationCookies as $cookieName ) {
				$nitro->getApi()->setVariationCookie( $cookieName );
			}

			$nitro->fetchConfig(); // Reload the variation cookies

			get_nitropack()->updateCurrentBlogConfig( $siteId, $siteSecret, $blogId );
			nitropack_install_advanced_cache();

			try {
				do_action( 'nitropack_integration_purge_all' );
			} catch (\Exception $e) {
				// Exception while signaling our 3rd party integration addons to purge their cache
			}

			nitropack_event( "connect", $nitro );
			nitropack_event( "enable_extension", $nitro );

			// Optimize front page
			$siteConfig = nitropack_get_site_config();
			if ( $siteConfig ) {
				$nitro->getApi()->runWarmup( [ $siteConfig['home_url'] ], true ); // force run a warmup on the home page
			}

			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'NitroPack connected' );
			nitropack_json_and_exit( array(
				"status" => "success",
				"message" => __( "Connected", "nitropack" )
			) );
		}
	} catch (\NitroPack\SDK\WebhookException $e) {

		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( $e );

		nitropack_json_and_exit( array( "status" => "error", "message" => $e ) );
	} catch (\NitroPack\SDK\StorageException $e) {

		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Permission Error: ' . $e );

		nitropack_json_and_exit( array( "status" => "error", "message" => __( 'Permission Error: ', 'nitropack' ) . $e ) );
	} catch (\NitroPack\SDK\EmptyConfigException $e) {

		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Error while fetching remote config: ' . $e );

		nitropack_json_and_exit( array( "status" => "error", "message" => __( 'Error while fetching remote config: ', 'nitropack' ) . $e ) );
	} catch (\NitroPack\SocketOpenException $e) {

		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Can\'t establish connection with NitroPack\'s servers. ' . $e );

		nitropack_json_and_exit( array( "status" => "error", "message" => __( 'Can\'t establish connection with NitroPack\'s servers', 'nitropack' ) ) );
	} catch (\Exception $e) {

		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Incorrect API credentials. Please make sure that you copied them correctly and try again. ' . $e );

		nitropack_json_and_exit( array( "status" => "error", "message" => __( 'Incorrect API credentials. Please make sure that you copied them correctly and try again.', 'nitropack' ) ) );
	}

	NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Error verifying connection to NitroPack.' );

	nitropack_json_and_exit( array( "status" => "error" ) );
}

function nitropack_reset_webhooks( $nitroSDK ) {
	$nitroSDK->getApi()->unsetWebhook( "config" );
	$nitroSDK->getApi()->unsetWebhook( "cache_clear" );
	$nitroSDK->getApi()->unsetWebhook( "cache_ready" );
}

function nitropack_setup_webhooks( $nitro, $token = NULL ) {
	if ( ! $nitro || ! $token ) {
		throw new \NitroPack\SDK\WebhookException( 'Webhook token cannot be empty.' );
	}

	$homeUrl = strtolower( get_home_url() );
	$configUrl = new \NitroPack\Url\Url( $homeUrl . "?nitroWebhook=config&token=$token" );
	$cacheClearUrl = new \NitroPack\Url\Url( $homeUrl . "?nitroWebhook=cache_clear&token=$token" );
	$cacheReadyUrl = new \NitroPack\Url\Url( $homeUrl . "?nitroWebhook=cache_ready&token=$token" );

	$nitro->getApi()->setWebhook( "config", $configUrl );
	$nitro->getApi()->setWebhook( "cache_clear", $cacheClearUrl );
	$nitro->getApi()->setWebhook( "cache_ready", $cacheReadyUrl );
}

function nitropack_disconnect() {
	nitropack_verify_ajax_nonce( $_REQUEST );

	nitropack_uninstall_advanced_cache();

	try {
		nitropack_event( "disconnect" );
		if ( null !== $nitro = get_nitropack_sdk() ) {
			nitropack_reset_webhooks( $nitro );
		}
	} catch (\Exception $e) {
		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'NitroPack cannot be disconnected. Error: ' . $e );
		nitropack_json_and_exit( array( "status" => "error", "message" => $e ) );
	}

	get_nitropack()->unsetCurrentBlogConfig();

	$hostingNoticeFile = nitropack_get_hosting_notice_file();
	if ( file_exists( $hostingNoticeFile ) ) {
		if ( WP_DEBUG ) {
			unlink( $hostingNoticeFile );
		} else {
			@unlink( $hostingNoticeFile );
		}
	}
	NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'NitroPack disconnected' );
	nitropack_json_and_exit( array( "status" => "success", "message" => __( "Disconnected", "nitropack" ) ) );
}


function nitropack_set_compression_ajax() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	$option = (int) ! empty( $_POST["data"]["compressionStatus"] );
	$updated = update_option( "nitropack-enableCompression", $option );

	if ( $updated ) {
		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'HTML Compression is ' . ( $option === 1 ? 'enabled' : 'disabled' ) );
		nitropack_json_and_exit( array( "type" => "success", "message" => nitropack_admin_toast_msgs( 'success' ), "hasCompression" => $option ) );
	} else {
		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'HTML Compression cannot be ' . ( $option === 1 ? 'enabled' : 'disabled' ) );
		nitropack_json_and_exit( array(
			"type" => "error",
			"message" => nitropack_admin_toast_msgs( 'error' )
		) );
	}
}
function nitropack_set_can_editor_clear_cache() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	$option = (int) ! empty( $_POST["data"]["canEditorClearCache"] );
	$updated = update_option( "nitropack-canEditorClearCache", $option );
	if ( $updated ) {
		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'Allow Editors to purge cache is ' . ( $option === 1 ? 'enabled' : 'disabled' ) );
		nitropack_json_and_exit( array( "type" => "success", "message" => nitropack_admin_toast_msgs( 'success' ), "allowedEditors" => $option ) );
	} else {
		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Allow Editors to purge cache cannot be ' . ( $option === 1 ? 'enabled' : 'disabled' ) );
		nitropack_json_and_exit( array(
			"type" => "error",
			"message" => nitropack_admin_toast_msgs( 'error' )
		) );
	}
}

/* Verification is handled in JS, therefore there are no errors here to be handled here. Used for logging purposes. */
function nitropack_set_optimization_mode() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	$option = ! empty( $_POST["mode"] && is_numeric( $_POST['mode'] ) ) ? (int) $_POST["mode"] : null;
	$modes = [ 1 => 'Standard', 2 => 'Medium', 3 => 'Strong', 4 => 'Ludicrous', 5 => 'Custom' ];
	NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'Optimization mode set to: ' . $modes[ $option ] );
	nitropack_json_and_exit( array( "type" => "success", "message" => nitropack_admin_toast_msgs( 'success' ), "mode" => $option ) );
}
function nitropack_set_auto_cache_purge_ajax() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	$option = (int) ! empty( $_POST["autoCachePurgeStatus"] );
	$updated = update_option( "nitropack-autoCachePurge", $option );
	if ( $updated ) {
		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'Auto cache purge is ' . ( $option === 1 ? 'enabled' : 'disabled' ) );
		nitropack_json_and_exit( [ "type" => "success", "message" => nitropack_admin_toast_msgs( 'success' ), 'autoCachePurgeStatus' => $option ] );
	} else {
		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Auto cache purge cannot be ' . ( $option === 1 ? 'enabled' : 'disabled' ) );
		nitropack_json_and_exit( [ 
			"type" => "error",
			"message" => nitropack_admin_toast_msgs( 'error' )
		] );
	}
}
function nitropack_set_ajax_shortcodes_ajax() {
	nitropack_verify_ajax_nonce( $_REQUEST );

	$new_shortcodes = isset( $_POST['shortcodes'] ) ? $_POST['shortcodes'] : null;
	$enabled = isset( $_POST['enabled'] ) ? $_POST['enabled'] : null;

	if ( $new_shortcodes === null && $enabled === null ) {
		nitropack_json_and_exit( array(
			"type" => "error",
			"message" => nitropack_admin_toast_msgs( 'error' )
		) );
	}
	$nitropack = get_nitropack();
	$siteConfig = $nitropack->Config->get();
	$configKey = \NitroPack\WordPress\NitroPack::getConfigKey();

	if ( ! is_null( $enabled ) ) {
		$siteConfig[ $configKey ]['options_cache']['ajaxShortcodes']['enabled'] = $enabled === '1';
	}

	if ( ! is_null( $new_shortcodes ) ) {
		$existing_options = ( is_array( $new_shortcodes ) && $new_shortcodes[0] === '' ) ? [] : $new_shortcodes;
		if ( $existing_options )
			$siteConfig[ $configKey ]['options_cache']['ajaxShortcodes']['shortcodes'] = $existing_options;
	}
	$updated = $nitropack->Config->set( $siteConfig );

	if ( $updated ) {

		if ( $enabled ) {
			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'Shortcodes are enabled' );
		} elseif ( ! $enabled && ! $new_shortcodes ) {
			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'Shortcodes are disabled' );
		}

		if ( is_array( $new_shortcodes ) && $new_shortcodes[0] === '' ) {
			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( "Shortcodes are empty" );
		} elseif ( is_array( $new_shortcodes ) ) {
			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( "Shortcodes are: " . implode( ", ", $new_shortcodes ) );
		}

		nitropack_json_and_exit( array( "type" => "success", "message" => nitropack_admin_toast_msgs( 'success' ) ) );
	} else {

		if ( $enabled ) {
			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Shortcodes are ' . $enabled ? 'enabled' : 'disabled' );
		} elseif ( ! $enabled && ! $new_shortcodes ) {
			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Shortcodes are disabled' );
		}
		if ( is_array( $new_shortcodes ) && $new_shortcodes[0] === '' ) {
			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( "Shortcodes are empty" );
		} elseif ( is_array( $new_shortcodes ) ) {
			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( "Shortcodes are: " . implode( ", ", $new_shortcodes ) );
		}

		nitropack_json_and_exit( array(
			"type" => "error",
			"message" => nitropack_admin_toast_msgs( 'error' )
		) );
	}
}


function nitropack_set_cart_cache_ajax() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	if ( get_nitropack()->isConnected() && nitropack_render_woocommerce_cart_cache_option() ) {
		$cartCacheStatus = (int) ( ! empty( $_POST["cartCacheStatus"] ) );

		if ( $cartCacheStatus == 1 ) {
			nitropack_enable_cart_cache();
		} else {
			nitropack_disable_cart_cache();
		}
	}

	nitropack_json_and_exit( array(
		"type" => "error",
		"message" => nitropack_admin_toast_msgs( 'error' )
	) );
}

function nitropack_set_stock_reduce_status() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	$option = (int) ! empty( $_POST["data"]["stockReduceStatus"] );
	$updated = update_option( "nitropack-stockReduceStatus", $option );
	if ( $updated ) {
		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'Stock Reduce Status is ' . ( $option === 1 ? 'enabled' : 'disabled' ) );
		nitropack_json_and_exit( array( "type" => "success", "message" => nitropack_admin_toast_msgs( 'success' ), "stockReduceStatus" => $option ) );
	} else {
		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Stock Reduce Status cannot be' . ( $option === 1 ? 'enabled' : 'disabled' ) );
		nitropack_json_and_exit( array(
			"type" => "error",
			"message" => nitropack_admin_toast_msgs( 'error' )
		) );
	}
}

function nitropack_set_bb_cache_purge_sync_ajax() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	$option = (int) ! empty( $_POST["bbCachePurgeSyncStatus"] );
	$updated = update_option( "nitropack-bbCacheSyncPurge", $option );
	if ( $updated ) {
		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'Beaver Builder Status is ' . ( $option === 1 ? 'enabled' : 'disabled' ) );
		nitropack_json_and_exit( array( "type" => "success", "message" => nitropack_admin_toast_msgs( 'success' ), 'bbCacheSyncPurgeStatus' => $option ) );
	} else {
		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Beaver Builder Status cannot be ' . ( $option === 1 ? 'enabled' : 'disabled' ) );
		nitropack_json_and_exit( array(
			"type" => "error",
			"message" => nitropack_admin_toast_msgs( 'error' )
		) );
	}
}

function nitropack_set_cacheable_post_types() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	$currentCacheableObjectTypes = nitropack_get_cacheable_object_types();
	$cacheableObjectTypes = ! empty( $_POST["cacheableObjectTypes"] ) ? $_POST["cacheableObjectTypes"] : array();
	$noncacheableObjectTypes = ! empty( $_POST["noncacheableObjectTypes"] ) ? $_POST["noncacheableObjectTypes"] : array();

	/* Used for non optimized CPT modal which is shown only once */
	if ( isset( $_POST["append"] ) && $_POST["append"] === "yes" ) {
		$cacheableObjectTypes = array_merge( $currentCacheableObjectTypes, $cacheableObjectTypes );
	}

	update_option( "nitropack-cacheableObjectTypes", $cacheableObjectTypes );
	update_option( "nitropack-nonCacheableObjectTypes", $noncacheableObjectTypes );

	foreach ( $currentCacheableObjectTypes as $objectType ) {
		if ( ! in_array( $objectType, $cacheableObjectTypes ) ) {
			nitropack_purge( NULL, "pageType:" . $objectType, "Optimizing '$objectType' pages was manually disabled" );
		}
	}

	NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( "Optimized CPTs: " . implode( ", ", $cacheableObjectTypes ) );
	NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( "Non-optimized CPTs: " . ( ! empty( $noncacheableObjectTypes ) && is_array( $noncacheableObjectTypes ) ? implode( ", ", $noncacheableObjectTypes ) : 'N/A' ) );

	nitropack_json_and_exit( array(
		"type" => "success",
		"message" => nitropack_admin_toast_msgs( 'success' )
	) );
}

function nitropack_test_compression_ajax() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	$hasCompression = true;
	try {
		if ( \NitroPack\Integration\Hosting\Flywheel::detect() ) { // Flywheel: Compression is enabled by default
			$updated = update_option( "nitropack-enableCompression", 0 );
		} else {
			require_once plugin_dir_path( __FILE__ ) . nitropack_trailingslashit( 'nitropack-sdk' ) . 'autoload.php';
			$http = new NitroPack\HttpClient\HttpClient( get_site_url() );
			$http->setHeader( "X-NitroPack-Request", 1 );
			$http->timeout = 25;
			$http->fetch();
			$headers = $http->getHeaders();
			if ( ! empty( $headers["content-encoding"] ) && strtolower( $headers["content-encoding"] ) == "gzip" ) { // compression is present, so there is no need to enable it in NitroPack. We only check for GZIP, because this is the only supported compression in the HttpClient
				$updated = update_option( "nitropack-enableCompression", 0 );
				$hasCompression = true;
			} else { // no compression, we must enable it from NitroPack
				$updated = update_option( "nitropack-enableCompression", 1 );
				$hasCompression = false;
			}
		}
		update_option( "nitropack-checkedCompression", 1 );
	} catch (\Exception $e) {
		nitropack_json_and_exit( array( "type" => "error", "message" => nitropack_admin_toast_msgs( 'error' ) ) );
	}
	if ( $updated )
		nitropack_json_and_exit( array( "type" => "success", "message" => nitropack_admin_toast_msgs( 'success' ), "hasCompression" => $hasCompression ) );
}

function nitropack_render_woocommerce_cart_cache_option() {
	return class_exists( 'WooCommerce' );
}

function nitropack_is_cart_cache_active() {
	$nitro = get_nitropack()->getSdk();
	if ( $nitro ) {
		$config = $nitro->getConfig();
		if ( ! empty( $config->StatefulCache->Status ) && ! empty( $config->StatefulCache->CartCache ) ) {
			return nitropack_is_cart_cache_available();
		}
	}
	return false;
}

function nitropack_is_cart_cache_available() {
	$nitro = get_nitropack()->getSdk();
	if ( $nitro ) {
		$config = $nitro->getConfig();
		if ( ! empty( $config->StatefulCache->isCartCacheAvailable ) ) {
			return true;
		}
	}
	return false;
}

function nitropack_handle_compression_toggle( $old_value, $new_value ) {
	nitropack_update_blog_compression( $new_value == 1 );
}

function nitropack_update_blog_compression( $enableCompression = false ) {
	if ( get_nitropack()->isConnected() ) {
		$siteConfig = nitropack_get_site_config();
		$siteId = $siteConfig["siteId"];
		$siteSecret = $siteConfig["siteSecret"];
		$blogId = get_current_blog_id();
		get_nitropack()->updateCurrentBlogConfig( $siteId, $siteSecret, $blogId, $enableCompression );
	}
}

function nitropack_enable_warmup() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	$append = '';
	if ( nitropack_is_wp_cli() )
		$append = '[CLI] ';
	if ( null !== $nitro = get_nitropack_sdk() ) {
		try {
			$nitro->getApi()->enableWarmup();
			$nitro->getApi()->setWarmupHomepage( get_home_url() );
			$nitro->getApi()->runWarmup();
		} catch (\Exception $e) {
		}
		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'Cache warmup is enabled' );
		nitropack_json_and_exit( array(
			"type" => "success",
			"message" => __( 'Cache warmup has been enabled successfully!', 'nitropack' )
		) );
	}
	NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'Cache warmup cannot be enabled' );
	nitropack_json_and_exit( array(
		"type" => "error",
		"message" => __( 'There was an error while enabling the cache warmup!', 'nitropack' )
	) );
}

function nitropack_disable_warmup() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	if ( null !== $nitro = get_nitropack_sdk() ) {
		try {
			$nitro->getApi()->disableWarmup();
			$nitro->getApi()->resetWarmup();
			delete_option( 'np_warmup_sitemap' );
		} catch (\Exception $e) {
			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Cache warmup cannot be disabled. Error: ' . $e->getTraceAsString() );
		}
		NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'Cache warmup is disabled' );
		nitropack_json_and_exit( array(
			"type" => "success",
			"message" => __( 'Cache warmup has been disabled successfully!', 'nitropack' )
		) );
	}
	nitropack_json_and_exit( array(
		"type" => "error",
		"message" => __( 'There was an error while disabling the cache warmup!', 'nitropack' )
	) );
}

function nitropack_run_warmup() {
	nitropack_verify_ajax_nonce( $_REQUEST );

	NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'Running warmup' );

	if ( null !== $nitro = get_nitropack_sdk() ) {
		try {
			$nitro->getApi()->runWarmup();
			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'Cache warmup has been started' );
			nitropack_json_and_exit( array(
				"type" => "success",
				"message" => __( 'Success! Cache warmup has been started successfully!', 'nitropack' )
			) );
		} catch (\Exception $e) {
			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'There was an error while starting the cache warmup. Error: ' . $e->getTraceAsString() );
		}
	}

	nitropack_json_and_exit( array(
		"type" => "error",
		"message" => __( 'Error! There was an error while starting the cache warmup!', 'nitropack' )
	) );
}

function nitropack_estimate_warmup() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	if ( null !== $nitro = get_nitropack_sdk() ) {
		try {
			if ( ! session_id() ) {
				session_start();
			}
			$id = ! empty( $_POST["estId"] ) ? preg_replace( "/[^a-fA-F0-9]/", "", (string) $_POST["estId"] ) : NULL;
			if ( $id !== NULL && ( ! is_string( $id ) || $id != $_SESSION["nitroEstimateId"] ) ) {
				nitropack_json_and_exit( array(
					"type" => "error",
					"message" => __( 'Invalid estimation ID!', 'nitropack' )
				) );
			}

			$sitemapUrls = nitropack_active_sitemap_plugins() ? nitropack_get_site_maps() : NULL;
			$configuredSitemap = false;

			if ( $sitemapUrls === NULL ) {

				$defaultSitemap = get_default_sitemap();
				if ( $defaultSitemap ) {
					$nitro->getApi()->setWarmupSitemap( $defaultSitemap );
					$configuredSitemap = true;
				}

				delete_option( 'np_warmup_sitemap' );
			} else {

				$warmupSitemap = evaluate_warmup_sitemap( $sitemapUrls );
				if ( $warmupSitemap ) {
					$nitro->getApi()->setWarmupSitemap( $warmupSitemap );
					$configuredSitemap = true;
				}
			}

			if ( ! $configuredSitemap ) {
				$nitro->getApi()->setWarmupSitemap( NULL );
			}

			$nitro->getApi()->setWarmupHomepage( get_home_url() );

			$optimizationsEstimate = $nitro->getApi()->estimateWarmup( $id );

			if ( $id === NULL ) {
				$_SESSION["nitroEstimateId"] = $optimizationsEstimate; // When id is NULL, $optimizationsEstimate holds the ID for the newly started estimate
			}
		} catch (\Exception $e) {
		}
		$json_data = array(
			"type" => "success",
			"res" => $optimizationsEstimate,
			"sitemap_indication" => get_option( 'np_warmup_sitemap', false ),
			"message" => __( 'Warmup estimation failed.', 'nitropack' ),
		);
		if ( is_int( $optimizationsEstimate ) && $optimizationsEstimate > 0 ) {
			$json_data["message"] = __( 'Cache warmup has been estimated successfully.', 'nitropack' );
		} else if ( $optimizationsEstimate === 0 ) {
			$json_data["message"] = __( 'We could not find any links for warming up on your home page.', 'nitropack' );
		} else {
			$json_data["message"] = __( 'Warmup estimation failed. Please try again or contact support if the issue persists', 'nitropack' );
		}
		nitropack_json_and_exit( $json_data );
	}

	nitropack_json_and_exit( array(
		"type" => "error",
		"message" => __( 'Warmup estimation failed.', 'nitropack' )
	) );
}

function nitropack_warmup_stats() {
	nitropack_verify_ajax_nonce( $_REQUEST );
	if ( null !== $nitro = get_nitropack_sdk() ) {
		try {
			$stats = $nitro->getApi()->getWarmupStats();
		} catch (\Exception $e) {
			nitropack_json_and_exit( array(
				"type" => "error",
				"message" => __( 'Error! There was an error while fetching warmup stats!', 'nitropack' )
			) );
		}

		nitropack_json_and_exit( array(
			"type" => "success",
			"stats" => $stats
		) );
	}

	nitropack_json_and_exit( array(
		"type" => "error",
		"message" => __( 'Error! There was an error while fetching warmup stats!', 'nitropack' )
	) );
}


function nitropack_enable_cart_cache() {
	if ( null !== $nitro = get_nitropack_sdk() ) {
		try {
			$nitro->enableCartCache();
			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'Cart cache is enabled' );
			nitropack_json_and_exit( array(
				"type" => "success",
				"message" => nitropack_admin_toast_msgs( 'success' )
			) );
		} catch (\Exception $e) {
			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Cart cache cannot be enabled. Error: ' . $e );
		}
	}

	nitropack_json_and_exit( array(
		"type" => "error",
		"message" => nitropack_admin_toast_msgs( 'error' )
	) );
}

function nitropack_disable_cart_cache() {
	if ( null !== $nitro = get_nitropack_sdk() ) {
		try {
			$nitro->disableCartCache();
			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->notice( 'Cart cache is be disabled' );
			nitropack_json_and_exit( array(
				"type" => "success",
				"message" => nitropack_admin_toast_msgs( 'success' )
			) );
		} catch (\Exception $e) {
			NitroPack\WordPress\NitroPack::getInstance()->getLogger()->error( 'Cart cache cannot be disabled. Error: ' . $e );
		}
	}

	nitropack_json_and_exit( array(
		"type" => "error",
		"message" => nitropack_admin_toast_msgs( 'error' )
	) );
}



function nitropack_get_site_config() {
	return get_nitropack()->getSiteConfig();
}

function nitropack_get_current_site_id() {

	$site_config = nitropack_get_site_config();

	if ( $site_config && isset( $site_config['siteId'] ) ) {
		return $site_config['siteId'];
	}
}

function get_nitropack() {
	return \NitroPack\WordPress\NitroPack::getInstance();
}

function nitropack_event( $event, $nitro = null, $additional_meta_data = null ) {
	global $wp_version;

	try {
		$eventUrl = get_nitropack_integration_url( "extensionEvent", $nitro );
		$domain = ! empty( $_SERVER["HTTP_HOST"] ) ? $_SERVER["HTTP_HOST"] : "Unknown";


		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$platform = 'WooCommerce';
		} else {
			$platform = 'WordPress';
		}

		$query_data = array(
			'event' => $event,
			'platform' => $platform,
			'platform_version' => $wp_version,
			'nitropack_extension_version' => NITROPACK_VERSION,
			'additional_meta_data' => $additional_meta_data ? json_encode( $additional_meta_data ) : "{}",
			'domain' => $domain
		);

		$client = new NitroPack\HttpClient\HttpClient( $eventUrl . '&' . http_build_query( $query_data ) );
		$client->doNotDownload = true;
		$client->fetch();
	} catch (\Exception $e) {
	}
}

function nitropack_get_wpconfig_path() {
	$configFilePath = nitropack_trailingslashit( ABSPATH ) . "wp-config.php";
	if ( ! file_exists( $configFilePath ) ) {
		$configFilePath = nitropack_trailingslashit( dirname( ABSPATH ) ) . "wp-config.php";
		$settingsFilePath = nitropack_trailingslashit( dirname( ABSPATH ) ) . "wp-settings.php"; // We need to check for this file to avoid confusion if the current installation is a nested directory of another WP installation. Refer to wp-load.php for more information.
		if ( ! file_exists( $configFilePath ) || file_exists( $settingsFilePath ) ) {
			return false;
		}
	}


	if ( ! is_writable( $configFilePath ) ) {
		return false;
	}

	return $configFilePath;
}

function nitropack_get_htaccess_path() {
	$configFilePath = nitropack_trailingslashit( ABSPATH ) . ".htaccess";
	if ( ! file_exists( $configFilePath ) ) {
		return false;
	}


	if ( ! is_writable( $configFilePath ) ) {
		return false;
	}

	return $configFilePath;
}

function nitropack_detect_hosting() {
	if ( \NitroPack\Integration\Hosting\Flywheel::detect() ) {
		return "flywheel";
	} else if ( \NitroPack\Integration\Hosting\Cloudways::detect() ) {
		return "cloudways";
	} else if ( \NitroPack\Integration\Hosting\WPEngine::detect() ) {
		return "wpengine";
	} else if ( \NitroPack\Integration\Hosting\SiteGround::detect() ) {
		return "siteground";
	} else if ( \NitroPack\Integration\Hosting\GoDaddyWPaaS::detect() ) {
		return "godaddy_wpaas";
	} else if ( \NitroPack\Integration\Hosting\GridPane::detect() ) {
		return "gridpane";
	} else if ( \NitroPack\Integration\Hosting\Kinsta::detect() ) {
		return "kinsta";
	} else if ( \NitroPack\Integration\Hosting\Closte::detect() ) {
		return "closte";
	} else if ( \NitroPack\Integration\Hosting\Pagely::detect() ) {
		return "pagely";
	} else if ( \NitroPack\Integration\Hosting\WPX::detect() ) {
		return "wpx";
	} else if ( \NitroPack\Integration\Hosting\Vimexx::detect() ) {
		return "vimexx";
	} else if ( \NitroPack\Integration\Hosting\Pressable::detect() ) {
		return "pressable";
	} else if ( \NitroPack\Integration\Hosting\RocketNet::detect() ) {
		return "rocketnet";
	} else if ( \NitroPack\Integration\Hosting\Savvii::detect() ) {
		return "savvii";
	} else if ( \NitroPack\Integration\Hosting\DreamHost::detect() ) {
		return "dreamhost";
	} else if ( \NitroPack\Integration\Hosting\Raidboxes::detect() ) {
		return "raidboxes";
	} else {
		return "unknown";
	}
}

function nitropack_removeCacheBustParam( $content ) {
	$content = preg_replace( "/(\?|%26|&#0?38;|&#x0?26;|&(amp;)?)ignorenitro(%3D|=)[a-fA-F0-9]{32}(?!%26|&#0?38;|&#x0?26;|&(amp;)?)\/?/mu", "", $content );
	return preg_replace( "/(\?|%26|&#0?38;|&#x0?26;|&(amp;)?)ignorenitro(%3D|=)[a-fA-F0-9]{32}(%26|&#0?38;|&#x0?26;|&(amp;)?)/mu", "$1", $content );
}

function nitropack_handle_request( $servedFrom = "unknown" ) {
	global $np_integrationSetupEvent;

	if ( isset( $_GET["ignorenitro"] ) ) {
		unset( $_GET["ignorenitro"] );
	}

	if ( defined( "NITROPACK_STRIP_IGNORENITRO" ) && NITROPACK_STRIP_IGNORENITRO && $_SERVER['REQUEST_URI'] != '' ) {
		$_SERVER['REQUEST_URI'] = nitropack_removeCacheBustParam( $_SERVER['REQUEST_URI'] );
	}

	nitropack_header( 'Cache-Control: no-cache' );
	do_action( "nitropack_early_cache_headers" ); // Overrides the Cache-Control header on supported platforms
	$isManageWpRequest = ! empty( $_GET["mwprid"] );
	$isWpCli = nitropack_is_wp_cli();

	if ( file_exists( NITROPACK_CONFIG_FILE ) && ! empty( $_SERVER["HTTP_HOST"] ) && ! empty( $_SERVER["REQUEST_URI"] ) && ! $isManageWpRequest && ! $isWpCli ) {
		try {
			$siteConfig = nitropack_get_site_config();
			if ( $siteConfig && null !== $nitro = get_nitropack_sdk( $siteConfig["siteId"], $siteConfig["siteSecret"] ) ) {
				if ( is_valid_nitropack_webhook() ) {
					nitropack_handle_webhook();
				} else if ( is_valid_nitropack_beacon() ) {
					nitropack_handle_beacon();
				} else if ( is_valid_nitropack_heartbeat() ) {
					nitropack_handle_heartbeat();
				} else {
					$GLOBALS["NitroPack.instance"] = $nitro;

					if ( nitropack_passes_cookie_requirements() || ( nitropack_is_ajax() && ! empty( $_COOKIE["nitroCachedPage"] ) ) ) {
						// Check whether the current URL is cacheable
						// If this is an AJAX request, check whether the referer is cachable - this is needed for cases when NitroPack's "Enabled URLs" option is being used to whitelist certain URLs. 
						// If we are not checking the referer, the AJAX requests on these pages can fail.
						$urlToCheck = nitropack_is_ajax() && ! empty( $_SERVER["HTTP_REFERER"] ) ? $_SERVER["HTTP_REFERER"] : $nitro->getUrl();
						if ( $nitro->isAllowedUrl( $urlToCheck ) ) {
							add_filter( 'nonce_life', 'nitropack_extend_nonce_life' );
						}
					}

					if ( nitropack_passes_cookie_requirements() && apply_filters( "nitropack_can_serve_cache", true ) ) {
						if ( $nitro->isCacheAllowed() ) {
							if ( ! nitropack_is_ajax() ) {
								do_action( "nitropack_cacheable_cache_headers" );
							}

							if ( ! empty( $siteConfig["compression"] ) ) {
								$nitro->enableCompression();
							}

							if ( $nitro->hasLocalCache() ) {
								// TODO: Make this work so we can provide the reverse proxies with this information $remainingTtl = $nitr->pageCache->getRemainingTtl();
								do_action( "nitropack_cachehit_cache_headers" ); // TODO: Pass the remaining TTL here
								$cacheControlOverride = defined( "NITROPACK_CACHE_CONTROL_OVERRIDE" ) ? NITROPACK_CACHE_CONTROL_OVERRIDE : NULL;
								if ( $cacheControlOverride ) {
									nitropack_header( 'Cache-Control: ' . $cacheControlOverride );
								}

								nitropack_header( 'X-Nitro-Cache: HIT' );
								nitropack_header( 'X-Nitro-Cache-From: ' . $servedFrom );
								$cjHandler = new \NitroPack\SDK\Utils\CjHandler( $nitro );
								$cjHandler->handleQueryParams();
								$nitro->pageCache->readfile();
								exit;
							} else {
								// We need the following if..else block to handle bot requests which will not be firing our beacon
								if ( nitropack_is_warmup_request() ) {
									if ( ! empty( $_SERVER["HTTP_ACCEPT_LANGUAGE"] ) ) {
										add_action( "init", function () use ($nitro) {
											$nitro->hasRemoteCache( "default" ); // Only ping the API letting our service know that this page must be cached.
											exit;
										}, 9999 );
										return; // We need to wait for a language plugin (if present) to redirect
									} else {
										$nitro->hasRemoteCache( "default" ); // Only ping the API letting our service know that this page must be cached.
										exit; // No need to continue handling this request. The response is not important.
									}
								} else if ( nitropack_is_lighthouse_request() || nitropack_is_gtmetrix_request() || nitropack_is_pingdom_request() ) {
									$nitro->hasRemoteCache( "default" ); // Ping the API letting our service know that this page must be cached.
								}

								$nitro->pageCache->useInvalidated( true );
								if ( $nitro->hasLocalCache() ) {
									nitropack_header( 'X-Nitro-Cache: STALE' );
									nitropack_header( 'X-Nitro-Cache-From: ' . $servedFrom );
									$cjHandler = new \NitroPack\SDK\Utils\CjHandler( $nitro );
									$cjHandler->handleQueryParams();
									$nitro->pageCache->readfile();
									exit;
								} else {
									$nitro->pageCache->useInvalidated( false );
								}
							}
						}
					}
				}
			}
		} catch (\Exception $e) {
			// Do nothing, cache serving will be handled by nitropack_init
		}
	}
}

function nitropack_is_dropin_cache_allowed() {
	$siteConfig = nitropack_get_site_config();
	return $siteConfig && empty( $siteConfig["isEzoicActive"] );
}



function nitropack_admin_bar_menu( $wp_admin_bar ) {
	if ( nitropack_is_amp_page() )
		return;
	$notifications = get_nitropack()->Notifications;
	$counter_data = $notifications->admin_bar_notices_counter();

	if ( ! get_nitropack()->isConnected() ) {
		$node = array(
			'id' => 'nitropack-top-menu',
			'title' => '&nbsp;&nbsp;<i style="" class="circle nitro nitro-status nitro-status-not-connected" aria-hidden="true"></i>&nbsp;&nbsp;NitroPack is disconnected',
			'href' => admin_url( 'admin.php?page=nitropack' ),
			'meta' => array(
				'class' => 'nitropack-menu'
			)
		);

		$wp_admin_bar->add_node(
			array(
				'parent' => 'nitropack-top-menu',
				'id' => 'optimizations-plugin-status',
				'title' => __( 'Connect NitroPack&nbsp;&nbsp;', 'nitropack' ),
				'href' => admin_url( 'admin.php?page=nitropack' ),
				'meta' => array(
					'class' => 'nitropack-plugin-status',
				)
			)
		);
	} else {
		$title = '&nbsp;&nbsp;<i style="" class="circle nitro nitro-status nitro-status-' . $counter_data['status'] . '" aria-hidden="true"></i>&nbsp;&nbsp;NitroPack';
		if ( $counter_data['status'] != "ok" ) {
			$title .= ' <span class="circle-with-text nitro-color-issues" id="nitro-total-issues-count">' . ( $counter_data['issues'] + $counter_data['notifications'] ) . '</span>';
		} else if ( $counter_data['notifications'] > 0 ) {
			$title .= ' <span class="circle-with-text nitro-color-notification" id="nitro-total-issues-count">' . $counter_data['notifications'] . '</span>';
		}
		$node = array(
			'id' => 'nitropack-top-menu',
			'title' => $title,
			'href' => admin_url( 'admin.php?page=nitropack' ),
			'meta' => array(
				'class' => 'nitropack-menu'
			)
		);

		$settings_extra = '';
		if ( $counter_data['notifications'] ) {
			$settings_extra .= ' <span class="circle-with-text nitro-color-notification" id="nitro-notification-issues-count">' . $counter_data['notifications'] . '</span>';
		}
		$wp_admin_bar->add_node( array(
			'id' => 'nitropack-top-menu-settings',
			'parent' => 'nitropack-top-menu',
			'title' => __( 'Settings', 'nitropack' ) . ' ' . $settings_extra . '',
			'href' => admin_url( 'admin.php?page=nitropack' ),
			'meta' => array(
				'class' => 'nitropack-settings'
			)

		) );

		$wp_admin_bar->add_node(
			array(
				'id' => 'nitropack-top-menu-purge-entire-cache',
				'parent' => 'nitropack-top-menu',
				'title' => __( 'Purge Entire Cache', 'nitropack' ),
				'href' => '#',
				'meta' => array(
					'class' => 'nitropack-purge-cache-entire-site',
				),
			)
		);

		if ( ! is_admin() ) { // menu otions available when browsing front-end pages

			$wp_admin_bar->add_node(
				array(
					'parent' => 'nitropack-top-menu',
					'id' => 'optimizations-purge-cache',
					'title' => __( 'Purge Current Page', 'nitropack' ),
					'href' => "#",
					'meta' => array(
						'class' => 'nitropack-purge-cache',
					)
				)
			);

			$wp_admin_bar->add_node(
				array(
					'parent' => 'nitropack-top-menu',
					'id' => 'optimizations-invalidate-cache',
					'title' => __( 'Invalidate Current Page', 'nitropack' ),
					'href' => "#",
					'meta' => array(
						'class' => 'nitropack-invalidate-cache',
					)
				)
			);
		}

		if ( $counter_data['status'] != "ok" ) {
			$wp_admin_bar->add_node(
				array(
					'parent' => 'nitropack-top-menu',
					'id' => 'optimizations-plugin-status',
					'title' => 'Issues <span class="circle-with-text nitro-color-issues">' . $counter_data['issues'] . '</span>',
					'href' => admin_url( 'admin.php?page=nitropack' ),
					'meta' => array(
						'class' => 'nitropack-plugin-status',
					)
				)
			);
		}
	}

	$wp_admin_bar->add_node( $node );
}

function nitropack_admin_bar_script( $hook ) {
	if ( ! nitropack_is_amp_page() ) {
		wp_enqueue_script( 'nitropack_admin_bar_menu_script', plugin_dir_url( __FILE__ ) . 'view/javascript/admin_bar_menu.js?np_v=' . NITROPACK_VERSION, [ 'jquery' ], false, true );
		wp_localize_script( 'nitropack_admin_bar_menu_script', 'frontendajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'nitroNonce' => wp_create_nonce( NITROPACK_NONCE ), 'nitro_plugin_url' => plugin_dir_url( __FILE__ ) ) );
	}
}


function enqueue_nitropack_admin_bar_menu_stylesheet() {
	if ( ! nitropack_is_amp_page() ) {
		wp_enqueue_style( 'nitropack_admin_bar_menu_stylesheet', plugin_dir_url( __FILE__ ) . 'view/stylesheet/admin_bar_menu.min.css?np_v=' . NITROPACK_VERSION );
	}
}

function nitropack_cookiepath() {
	$siteConfig = nitropack_get_site_config();
	$homeUrl = $siteConfig && ! empty( $siteConfig["home_url"] ) ? $siteConfig["home_url"] : get_home_url();
	$url = new \NitroPack\Url\Url( $homeUrl );
	return $url ? $url->getPath() : "/";
}

function nitropack_setcookie( $name, $value, $expires = NULL, $options = [] ) {
	if ( headers_sent() )
		return;
	$cookie_options = '';
	$cookie_path = nitropack_cookiepath();

	if ( $expires && is_numeric( $expires ) ) {
		$options["Expires"] = date( "D, d M Y H:i:s", (int) $expires ) . ' GMT';
	}

	if ( empty( $options["SameSite"] ) ) {
		$options["SameSite"] = "Lax";
	}

	foreach ( $options as $optName => $optValue ) {
		$cookie_options .= "$optName=$optValue; ";
	}
	nitropack_header( "set-cookie: $name=$value; Path=$cookie_path; " . $cookie_options, false );
}

function nitropack_header( $header, $replace = true, $response_code = 0 ) {
	if ( ! nitropack_is_wp_cron() && ! nitropack_is_wp_cli() ) {
		header( $header, $replace, $response_code );
	}
}


function nitropack_upgrade_handler( $entity ) {
	$np = 'nitropack/main.php';
	$trigger = $entity;
	if ( $entity instanceof Plugin_Upgrader ) {
		$trigger = $entity->plugin_info();
		if ( ! is_plugin_active( $trigger ) ) {
			return;
		}
	}

	if ( $entity instanceof Theme_Upgrader ) {
		if ( $entity->theme_info()->Name === wp_get_theme()->Name ) {
			nitropack_theme_handler( 'Theme updated' );
		}
		return;
	}

	if ( $trigger !== $np ) {
		$cookie_expires = date( "D, d M Y H:i:s", time() + 600 ) . ' GMT';
		nitropack_setcookie( 'nitropack_apwarning', "1", time() + 600 );
	}
}
/**
 * Caches some options in the config so that we can access them before get_option() is defined
 * which is in advanced_cache.php, functions.php and Integrations
 */
function nitropack_updated_option( $option, $oldValue, $value ) {
	$neededOptions = \NitroPack\WordPress\NitroPack::$optionsToCache;
	if ( ! in_array( $option, $neededOptions ) )
		return;

	$np = get_nitropack();
	$siteConfig = $np->Config->get();

	if ( function_exists( 'get_home_url' ) ) {
		$configKey = \NitroPack\WordPress\NitroPack::getConfigKey();
		$siteConfig[ $configKey ]['options_cache'][ $option ] = $value;
		$np->Config->set( $siteConfig );
	}
}

function nitropack_is_late_integration_init_required() {
	return \NitroPack\Integration\Plugin\NginxHelper::isActive() || \NitroPack\Integration\Plugin\Cloudflare::isApoActive();
}

function nitropack_get_notice_id( $message ) {
	return md5( $message );
}

function nitropack_active_sitemap_plugins() {
	return
		NitroPack\Integration\Plugin\YoastSEO::isActive() ||
		NitroPack\Integration\Plugin\JetPackNP::isActive() ||
		NitroPack\Integration\Plugin\SquirrlySEO::isActive() ||
		NitroPack\Integration\Plugin\RankMathNP::isActive();
}

function nitropack_get_site_maps() {
	$sitemapUrls['YoastSEO'] = NitroPack\Integration\Plugin\YoastSEO::getSitemapURL();
	$sitemapUrls['JetPack'] = NitroPack\Integration\Plugin\JetPackNP::getSitemapURL();
	$sitemapUrls['SquirrlySEO'] = NitroPack\Integration\Plugin\SquirrlySEO::getSitemapURL();
	$sitemapUrls['RankMath'] = NitroPack\Integration\Plugin\RankMathNP::getSitemapURL();

	return $sitemapUrls;
}

function get_default_sitemap() {

	$defaultSiteMap = NitroPack\Integration\Plugin\WPCacheHelper::getSitemapURL();
	if ( $defaultSiteMap ) {
		set_sitemap_indication_msg( 'WordPress', $defaultSiteMap );
		return $defaultSiteMap;
	}

	return false;
}

function evaluate_warmup_sitemap( $sitemapUrls ) {

	$sitemapProviders = array(
		'YoastSEO' => 'Yoast!',
		'SquirrlySEO' => 'Squirrly SEO',
		'RankMath' => 'Rank Math',
		'JetPack' => 'Jetpack',
	);

	foreach ( $sitemapProviders as $provider => $name ) {
		if ( isset( $sitemapUrls[ $provider ] ) && $sitemapUrls[ $provider ] ) {
			set_sitemap_indication_msg( $name, $sitemapUrls[ $provider ] );
			return $sitemapUrls[ $provider ];
		}
	}

	return get_default_sitemap();
}

function set_sitemap_indication_msg( $pluginName, $sitemapURL ) {
	$sitemapURI = explode( "/", parse_url( $sitemapURL, PHP_URL_PATH ) );
	$msg = $sitemapURI[1] . ' used by ' . $pluginName;
	update_option( 'np_warmup_sitemap', $msg );
}



function get_date_midpoint( $endDate ) {
	return ( time() + strtotime( $endDate ) ) / 2;
}


function initVariationCookies( $customVariationCookies ) {
	$api = get_nitropack_sdk()->getApi();
	try {
		$variationCookies = $api->getVariationCookies();
		foreach ( $variationCookies as $cookie ) {
			$index = array_search( $cookie["name"], $customVariationCookies );
			if ( $index !== false ) {
				array_splice( $customVariationCookies, $index, 1 );
			}
		}

		foreach ( $customVariationCookies as $cookieName ) {
			$api->setVariationCookie( $cookieName );
		}
	} catch (\Exception $e) {
		// what to do here? possible reason for exception is the API not responding
		return false;
	}
}

function removeVariationCookies( $cookiesToRemove ) {
	$api = get_nitropack_sdk()->getApi();
	try {
		$variationCookies = $api->getVariationCookies();
		foreach ( $variationCookies as $cookie ) {
			if ( in_array( $cookie["name"], $cookiesToRemove ) ) {
				$api->unsetVariationCookie( $cookie["name"] );
			}
		}
	} catch (\Exception $e) {
		// what to do here? possible reason for exception is the API not responding
		return false;
	}
}

function getNewCookie( $name ) {
	$cookies = getNewCookies();
	return ! empty( $cookies[ $name ] ) ? $cookies[ $name ] : null;
}

/**
 * Returns an array of newly set cookies (not in $_COOKIE), that will be sent along with the headers of the current response.
 */
function getNewCookies() {
	$cookies = [];
	$headers = headers_list();
	foreach ( $headers as $header ) {
		if ( strpos( $header, 'Set-Cookie: ' ) === 0 ) {
			$value = str_replace( '&', urlencode( '&' ), substr( $header, 12 ) );
			parse_str( current( explode( ';', $value ) ), $pair );
			$cookies = array_merge_recursive( $cookies, $pair );
		}
	}

	return array_filter( array_map( function ($val) {
		if ( is_array( $val ) ) {
			$lastEl = end( $val );
			if ( is_array( $lastEl ) ) {
				return NULL;
			}
			return $lastEl;
		}

		return $val;
	}, $cookies ) );
}

/**
 * Purge entire cache when permalink structure is changed.
 *
 * @param string $old_permalink_structure The previous permalink structure.
 * @param string $permalink_structure     The new permalink structure.
 *
 * @return void
 */
function nitropack_permalink_structure_changed_handler( $old_permalink_structure, $permalink_structure ) {

	if ( $old_permalink_structure != $permalink_structure && get_option( "nitropack-autoCachePurge", 1 ) ) {
		$msg = 'The permalink structure is changed. Purging the cache for the home page.';
		$url = get_home_url();

		try {
			nitropack_sdk_purge( $url, NULL, $msg ); // purge cache for the home page
		} catch (\Exception $e) {
		}

		// run warmup
		if ( null !== $nitro = get_nitropack_sdk() ) {
			try {
				$nitro->getApi()->runWarmup();
			} catch (\Exception $e) {
			}
		}
	}
}

add_action( 'permalink_structure_changed', 'nitropack_permalink_structure_changed_handler', 10, 2 );

/**
 * Purge entire cache when front page is changed.
 *
 * @param array $old_value An array of previous settings values.
 * @param array $value An array of submitted settings values.
 *
 * @return void
 */
function nitropack_frontpage_changed_handler( $old_value, $value ) {

	if ( $old_value !== $value ) {
		$msg = 'The front page is changed';
		$url = get_home_url();

		try {
			nitropack_sdk_purge( $url, NULL, $msg ); // purge entire cache
		} catch (\Exception $e) {
		}
	}
}

add_action( 'update_option_show_on_front', 'nitropack_frontpage_changed_handler', 10, 2 );
add_action( 'update_option_page_on_front', 'nitropack_frontpage_changed_handler', 10, 2 );
add_action( 'update_option_page_for_posts', 'nitropack_frontpage_changed_handler', 10, 2 );

// Init integration action handlers
$modHandler = NitroPack\ModuleHandler::getInstance();
$modHandler->init();
