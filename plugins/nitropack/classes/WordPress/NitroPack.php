<?php

/**
 * Nitropack Class
 *
 * @package nitropack
 */

namespace NitroPack\WordPress;

use \NitroPack\SDK\Filesystem;
use \NitroPack\Feature\Logger\Logger as Logger;
use \NitroPack\Feature\Logger\LoggingEvents as LoggingEvents;

class NitroPack {
	private static $instance = NULL;
	public static $nitroDirMigrated = false;
	public static $nitroConfigMigrated = false;
	public static $preUpdatePosts = array();
	public static $preUpdateTaxonomies = array();
	public static $preUpdateMeta = array();
	public static $ignoreUpdatePostIDs = array();
	public static $np_loggedWarmups = array();
	public static $optionsToCache = [ 
		'cache_handler_cache_handler',
		'woocommerce_default_customer_address',
		[ 'ajaxShortcodes' => [ 'enabled' => false, 'shortcodes' => [] ] ],
		[ "wc_aelia_currency_switcher" => "ipgeolocation_enabled" ]
	];

	public static function getInstance() {
		if ( ! self::$instance ) {
			self::$instance = new NitroPack();
		}

		return self::$instance;
	}

	private $sdkObjects;
	private $disabledReason;

	/**
	 * Page type.
	 *
	 * @var null
	 */
	private $pageType;

	/**
	 * Logger instance.
	 *
	 * @var Logger
	 */
	private $logger;
	/**
	 * LoggingEvents instance.
	 *
	 * @var LoggingEvents
	 */
	private $loggingEvents;
	/**
	 * Config instance.
	 *
	 * @var Config
	 */
	public $Config;

	/**
	 * Notifications instance.
	 *
	 * @var Notifications
	 */
	public $Notifications;

	/**
	 * Notifications instance.
	 *
	 * @var AppNotifications
	 */
	public $AppNotifications;
	/**
	 * Get instance.
	 *
	 * @return NitroPack|null
	 */

	public static function getDataDir() {
		$isRaidBoxes = \NitroPack\Integration\Hosting\Raidboxes::detect();
		$isPantheon = \NitroPack\Integration\Hosting\Pantheon::detect();
		$isWpe = \NitroPack\Integration\Hosting\WPEngine::detect();
		$isCloudways = \NitroPack\Integration\Hosting\Cloudways::detect();
		$isRocketNet = \NitroPack\Integration\Hosting\RocketNet::detect();
		$currentFilePath = __FILE__;
		$wpContentDir = WP_CONTENT_DIR;
		$pathResolved = self::resolvePathNavigation( $wpContentDir );

		if ( $pathResolved != $wpContentDir ) {
			// Handle existing installations which used to have either /./ or /../ in their content dir paths
			$wpContentDir = $pathResolved;
			self::$nitroDirMigrated = true;
		}

		if ( $isWpe ) {
			$currentFilePath = preg_replace( "@^/sites/@", "/nas/content/live/", $currentFilePath );
			$wpContentDir = preg_replace( "@^/sites/@", "/nas/content/live/", $wpContentDir );
		}

		$oldNitroDirs = [ 
			nitropack_trailingslashit( $wpContentDir ) . 'nitropack',
			nitropack_trailingslashit( $wpContentDir ) . 'cache/' . substr( md5( $currentFilePath ), 0, 7 ) . "-nitropack",
		];
		$newNitroDir = nitropack_trailingslashit( $wpContentDir ) . 'cache/' . NITROPACK_CACHE_DIR_NAME;
		$nitroDir = $newNitroDir;

		if ( $isRaidBoxes || $isRocketNet || $isCloudways ) {
			$nitroDir = $oldNitroDirs[0];
		} else if ( $isPantheon ) {
			$nitroDir = nitropack_trailingslashit( $wpContentDir ) . 'uploads/' . NITROPACK_CACHE_DIR_NAME;
		}

		$possibleDirs = array_unique( array_merge( $oldNitroDirs, [ $newNitroDir ] ) );
		$existingDirs = [];

		foreach ( $possibleDirs as $possibleDir ) {
			if ( Filesystem::fileExists( $possibleDir ) ) {
				$existingDirs[] = $possibleDir;
			}
		}

		if ( count( $existingDirs ) == 1 ) {
			$existingDir = array_shift( $existingDirs );
			if ( is_link( $existingDir ) ) {
				$existingDir = readlink( $existingDir );
			}

			if ( $existingDir != $nitroDir ) {
				if ( ! Filesystem::fileExists( $nitroDir ) && ! NITROPACK_USE_REDIS ) {
					// Existing installation, move to the new location

					if ( Filesystem::createDir( dirname( $nitroDir ) ) && Filesystem::rename( $existingDir, $nitroDir ) ) {
						self::$nitroDirMigrated = true;
					} else {
						define( 'NITROPACK_DATA_DIR_WARNING', 'Unable to initialize cache dir because the PHP user does not have permission to create/rename directories under wp-content/. Running in legacy mode. Please contact support for help.' );
						$nitroDir = $existingDir;
					}
				}
			}
		}

		return $nitroDir;
	}

	public static function getPluginDataDir() {
		$isPantheon = \NitroPack\Integration\Hosting\Pantheon::detect();
		$isWpe = \NitroPack\Integration\Hosting\WPEngine::detect();
		$nitroDir = nitropack_trailingslashit( WP_CONTENT_DIR ) . 'config-' . NITROPACK_CACHE_DIR_NAME;
		$expectedNitroDir = $nitroDir;

		$nitroDir = self::resolvePathNavigation( $nitroDir );
		if ( $nitroDir != $expectedNitroDir ) {
			// Handle existing installations which used to have either /./ or /../ in their config paths
			$expectedNitroDir = $nitroDir;
			self::$nitroConfigMigrated = true;
		}

		if ( $isWpe ) {
			$nitroDir = preg_replace( "@^/sites/@", "/nas/content/live/", $nitroDir );
			$expectedNitroDir = $nitroDir;
		}

		if ( $isPantheon ) {
			$nitroDir = nitropack_trailingslashit( WP_CONTENT_DIR ) . 'uploads/config-' . NITROPACK_CACHE_DIR_NAME;
			$expectedNitroDir = $nitroDir;
		}

		$oldConfigFile = nitropack_trailingslashit( NITROPACK_DATA_DIR ) . 'config.json';
		$newConfigFile = nitropack_trailingslashit( $nitroDir ) . 'config.json';

		if ( $isWpe ) {
			if ( ! Filesystem::fileExists( $newConfigFile ) && Filesystem::fileExists( preg_replace( "@^/nas/content/live/@", "/sites/", $oldConfigFile ) ) ) {
				$newConfigFile = preg_replace( "@^/nas/content/live/@", "/sites/", $newConfigFile );
			}
		}

		if ( Filesystem::fileExists( $oldConfigFile ) && ! Filesystem::fileExists( $newConfigFile ) ) {
			// Existing installation, move config to the new location

			if ( Filesystem::createDir( $nitroDir ) && Filesystem::rename( $oldConfigFile, $newConfigFile ) ) {
				self::$nitroConfigMigrated = true;
				$oldHtaccessFile = nitropack_trailingslashit( NITROPACK_DATA_DIR ) . '.htaccess';
				$newHtaccessFile = nitropack_trailingslashit( $nitroDir ) . '.htaccess';
				if ( Filesystem::fileExists( $oldHtaccessFile ) && ! Filesystem::fileExists( $newHtaccessFile ) ) {
					Filesystem::filePutContents( $newHtaccessFile, Filesystem::fileGetContents( $oldHtaccessFile ) );
				}
			} else {
				define( 'NITROPACK_PLUGIN_DATA_DIR_WARNING', 'Unable to initialize plugin data dir because the PHP user does not have permission to create/rename directories under wp-content/. Running in legacy mode. Please contact support for help.' );
				$nitroDir = NITROPACK_DATA_DIR;
			}
		}

		return $nitroDir;
	}

	public static function isWpCli() {
		return defined( "WP_CLI" ) && WP_CLI;
	}
	/**
	 * @var array $settings Configuration settings for NitroPack.
	 */
	public $settings;
	public function __construct() {
		$this->Config = new Config();
		$this->Notifications = Notifications\Notifications::getInstance();
		$this->settings = new Settings( $this->Config );
		$this->logger = new Logger( $this );
		$this->loggingEvents = new LoggingEvents( $this->logger );
		$this->sdkObjects = array();
		$this->disabledReason = NULL;
		$this->pageType = NULL;
	}

	public function getDistribution() {
		$dist = "regular";
		$dbDist = NULL;

		try {
			if ( function_exists( "get_option" ) ) {
				$dbDist = get_option( "nitropack-distribution", NULL );
			}

			if ( $this->isConnected() ) {
				$sdk = $this->getSdk();
				if ( ! $sdk )
					return $dbDist ? $dbDist : $dist;

				$config = $sdk->getConfig();
				if ( $config ) {
					$dist = $config->Distribution;
				}
			} else if ( $dbDist !== NULL ) {
				$dist = $dbDist;
			}

			if ( $dbDist != $dist && function_exists( "update_option" ) ) {
				update_option( "nitropack-distribution", $dist );
			}

			return $dist;
		} catch (Exception $e) {
			return $dist;
		}
	}

	public function getSiteConfig() {
		$siteConfig = null;
		$npConfig = $this->Config->get();
		$currentUrl = $this->getCurrentUrl();

		$matchLength = 0;

		foreach ( $npConfig as $siteUrl => $config ) {
			if ( stripos( $siteUrl, "www." ) === 0 ) {
				$siteUrl = substr( $siteUrl, 4 );
			}

			if ( stripos( $currentUrl, $siteUrl ) === 0 && strlen( $siteUrl ) > $matchLength ) {
				$siteConfig = $config;
				$matchLength = strlen( $siteUrl );
			}
		}

		if ( ! $siteConfig ) {
			$matchLength = 0;
			foreach ( $npConfig as $siteUrl => $config ) {
				if ( isset( $config['additional_domains'] ) ) {
					foreach ( $config['additional_domains'] as $additionalDomain ) {
						if ( stripos( $additionalDomain, "www." ) === 0 ) {
							$additionalDomain = substr( $additionalDomain, 4 );
						}

						if ( stripos( $currentUrl, $additionalDomain ) === 0 && strlen( $additionalDomain ) > $matchLength ) {
							$siteConfig = $config;
							$matchLength = strlen( $additionalDomain );
						}
					}
				}
			}
		}

		return $siteConfig;
	}

	public function getSiteId() {
		$siteConfig = $this->getSiteConfig();
		return $siteConfig ? $siteConfig["siteId"] : NULL;
	}

	public function getSiteSecret() {
		$siteConfig = $this->getSiteConfig();
		return $siteConfig ? $siteConfig["siteSecret"] : NULL;
	}

	/**
	 * Bear in mind that get_home_url() is not defined in the context of advanced_cache.php
	 * so this will throw a fatal error if you call it at that point!
	 */
	public static function getConfigKey() {
		return preg_replace( "/^https?:\/\/(.*)/", "$1", get_home_url() );
	}

	public function getAdditionalDomains( $siteId, $siteSecret ) {
		if ( null !== $nitro = $this->getSdk( $siteId, $siteSecret ) ) {
			$config = $nitro->getConfig();
			if ( ! property_exists( $config->AdditionalDomains, 'Domains' ) ) {
				$nitro->fetchConfig();
			}
			return $config->AdditionalDomains->Domains;
		}

		return [];
	}

	public function isConnected() {
		return ! empty( $this->getSiteId() ) && ! empty( $this->getSiteSecret() );
	}

	public function updateCurrentBlogConfig( $siteId, $siteSecret, $blogId, $enableCompression = null ) {
		if ( $enableCompression === null ) {
			$enableCompression = ( get_option( 'nitropack-enableCompression' ) == 1 );
		}

		$webhookToken = get_option( 'nitropack-webhookToken' );
		$hosting = nitropack_detect_hosting();

		$home_url = get_home_url();
		$admin_url = admin_url();
		$alwaysBuffer = defined( "NITROPACK_ALWAYS_BUFFER" ) ? NITROPACK_ALWAYS_BUFFER : true;
		$configKey = self::getConfigKey();
		$staticConfig = $this->Config->get();
		//grab existing ajaxShortcodes settings
		if ( isset( $staticConfig[ $configKey ]['options_cache']['ajaxShortcodes'] ) ) {
			$ajaxShortcodes_settings = $staticConfig[ $configKey ]['options_cache']['ajaxShortcodes'];
		}
		//default value is null, stored is int       
		$minimumLogLevel = (int) get_option( 'nitropack_minimumLogLevel', null );
		if ( ! $minimumLogLevel ) {
			$minimumLogLevel = null;
		}
		$staticConfig[ $configKey ] = array(
			"siteId" => $siteId,
			"siteSecret" => $siteSecret,
			"blogId" => $blogId,
			"compression" => $enableCompression,
			"webhookToken" => $webhookToken,
			"home_url" => $home_url,
			"admin_url" => $admin_url,
			"hosting" => $hosting,
			"alwaysBuffer" => $alwaysBuffer,
			"isEzoicActive" => \NitroPack\Integration\Plugin\Ezoic::isActive(),
			"isApoActive" => \NitroPack\Integration\Plugin\Cloudflare::isApoActive(),
			"isNginxHelperActive" => \NitroPack\Integration\Plugin\NginxHelper::isActive(),
			"isLateIntegrationInitRequired" => nitropack_is_late_integration_init_required(),
			"isDlmActive" => \NitroPack\Integration\Plugin\DownloadManager::isActive(),
			"isWoocommerceCacheHandlerActive" => \NitroPack\Integration\Plugin\WoocommerceCacheHandler::isActive(),
			"isWoocommerceActive" => \NitroPack\Integration\Plugin\WooCommerce::isActive(),
			"isAeliaCurrencySwitcherActive" => \NitroPack\Integration\Plugin\AeliaCurrencySwitcher::isActive(),
			"isGeoTargetingWPActive" => \NitroPack\Integration\Plugin\GeoTargetingWP::isActive(),
			"dlm_downloading_url" => \NitroPack\Integration\Plugin\DownloadManager::isActive() ? \NitroPack\Integration\Plugin\DownloadManager::downloadingUrl() : NULL,
			"dlm_download_endpoint" => \NitroPack\Integration\Plugin\DownloadManager::isActive() ? \NitroPack\Integration\Plugin\DownloadManager::downloadEndpoint() : NULL,
			"pluginVersion" => NITROPACK_VERSION,
			"options_cache" => [],
			"additional_domains" => $this->getAdditionalDomains( $siteId, $siteSecret ),
			"minimumLogLevel" => $minimumLogLevel,
		);

		foreach ( self::$optionsToCache as $opt ) {
			if ( is_array( $opt ) ) {
				foreach ( $opt as $option => $suboption ) {
					// Ensure the top-level option array exists
					if ( ! isset( $staticConfig[ $configKey ]["options_cache"][ $option ] ) || ! is_array( $staticConfig[ $configKey ]["options_cache"][ $option ] ) ) {
						$staticConfig[ $configKey ]["options_cache"][ $option ] = [];
					}

					// Handle cases where the suboption is itself an array (e.g., 'shortcodes')
					if ( is_array( $suboption ) ) {
						foreach ( $suboption as $suboptKey => $suboptValue ) {
							// Ensure the nested array exists
							if ( ! isset( $staticConfig[ $configKey ]["options_cache"][ $option ][ $suboptKey ] ) || ! is_array( $staticConfig[ $configKey ]["options_cache"][ $option ][ $suboptKey ] ) ) {
								$staticConfig[ $configKey ]["options_cache"][ $option ][ $suboptKey ] = [];
							}
							$optionValue = get_option( $option );

							if ( ! empty( $optionValue ) && is_array( $optionValue ) && isset( $optionValue[ $suboptKey ] ) ) {
								$staticConfig[ $configKey ]["options_cache"][ $option ][ $suboptKey ] = $optionValue[ $suboptKey ];
							} else {
								$staticConfig[ $configKey ]["options_cache"][ $option ][ $suboptKey ] = $suboptValue;
							}
						}
					} else {
						$optionValue = get_option( $option );

						if ( ! empty( $optionValue ) && is_array( $optionValue ) && isset( $optionValue[ $suboption ] ) ) {
							$staticConfig[ $configKey ]["options_cache"][ $option ][ $suboption ] = $optionValue[ $suboption ];
						} else {
							$staticConfig[ $configKey ]["options_cache"][ $option ][ $suboption ] = null;
						}
					}
				}
			} else {
				// Handle non-array options directly
				$staticConfig[ $configKey ]["options_cache"][ $opt ] = get_option( $opt );
			}
		}
		//handle existing options which we want to re-use
		if ( ! empty( $ajaxShortcodes_settings ) )
			$staticConfig[ $configKey ]["options_cache"]['ajaxShortcodes'] = $ajaxShortcodes_settings;

		$configSetResult = $this->Config->set( $staticConfig );

		if ( \NitroPack\Integration\Plugin\GeoTargetingWP::isActive() ) {
			try {
				\NitroPack\Integration\Plugin\GeoTargetingWP::configureVariationCookies();
			} catch (\Exception $e) {
				$this->getLogger()->error( $e->getMessage() );
			}
		}

		return $configSetResult;
	}

	public function unsetCurrentBlogConfig() {
		$configKey = self::getConfigKey();
		$staticConfig = $this->Config->get();
		if ( ! empty( $staticConfig[ $configKey ] ) ) {
			unset( $staticConfig[ $configKey ] );
			return $this->Config->set( $staticConfig );
		}

		return true;
	}

	public function resetSdkInstances() {
		$this->sdkObjects = [];
	}

	public function getSdk( $siteId = null, $siteSecret = null, $urlOverride = NULL, $forwardExceptions = false ) {
		$siteConfig = $this->getSiteConfig();

		$siteId = $siteId ?: ( ! empty( $siteConfig ) ? $siteConfig['siteId'] : NULL );
		$siteSecret = $siteSecret ?: ( ! empty( $siteConfig ) ? $siteConfig['siteSecret'] : NULL );

		if ( $siteId && $siteSecret ) {
			try {
				$userAgent = NULL; // It will be automatically detected by the SDK
				$dataDir = nitropack_trailingslashit( NITROPACK_DATA_DIR ) . $siteId; // dir without a trailing slash, because this is how the SDK expects it
				$cacheKey = "{$siteId}:{$siteSecret}:{$dataDir}";

				if ( $urlOverride ) {
					$cacheKey .= ":{$urlOverride}";
				}

				if ( ! empty( $this->sdkObjects[ $cacheKey ] ) ) {
					$nitro = $this->sdkObjects[ $cacheKey ];
				} else {
					if ( ! defined( "NP_COOKIE_FILTER" ) ) {
						\NitroPack\SDK\NitroPack::addCookieFilter( "nitropack_filter_non_original_cookies" );
						define( "NP_COOKIE_FILTER", true );
						do_action( 'np_set_cookie_filter' );
					}
					if ( ! defined( "NP_STORAGE_CONFIGURED" ) ) {
						if ( defined( "NITROPACK_USE_REDIS" ) && NITROPACK_USE_REDIS ) {
							\NitroPack\SDK\Filesystem::setStorageDriver( new \NitroPack\SDK\StorageDriver\Redis(
								NITROPACK_REDIS_HOST,
								NITROPACK_REDIS_PORT,
								NITROPACK_REDIS_PASS,
								NITROPACK_REDIS_DB
							) );
						}
						define( "NP_STORAGE_CONFIGURED", true );
					}

					if ( ! defined( 'NP_GEOLOCATION_PREFIX_DEFINED' ) ) {
						do_action( 'set_nitropack_geo_cache_prefix' );
						define( 'NP_GEOLOCATION_PREFIX_DEFINED', true );
					}

					if ( defined( "NITROPACK_CUSTOM_CACHE_PREFIX" ) && ! defined( 'NP_CUSTOM_CACHE_PREFIX_SET' ) ) {
						\NitroPack\SDK\NitroPack::addCustomCachePrefix( NITROPACK_CUSTOM_CACHE_PREFIX );
						define( 'NP_CUSTOM_CACHE_PREFIX_SET', true );
					}

					$nitro = new \NitroPack\SDK\NitroPack( $siteId, $siteSecret, $userAgent, $urlOverride, $dataDir );
					$this->sdkObjects[ $cacheKey ] = $nitro;
				}
			} catch (\Exception $e) {
				if ( $forwardExceptions ) {
					throw $e;
				}
				return NULL;
			}

			return $nitro;
		}

		return NULL;
	}

	/**
	 * Check if the data directory exists
	 *
	 * @return bool
	 */
	public function dataDirExists() {
		return defined( "NITROPACK_DATA_DIR" ) && is_dir( NITROPACK_DATA_DIR ); // TODO: Convert this to use the Filesystem abstraction for better Redis support
	}

	/**
	 * Initialize the data directory
	 *
	 * @return bool
	 */
	public function initDataDir() {
		return $this->dataDirExists() || @mkdir( NITROPACK_DATA_DIR, 0755, true ); // TODO: Convert this to use the Filesystem abstraction for better Redis support
	}

	public function pluginDataDirExists() {
		return defined( "NITROPACK_PLUGIN_DATA_DIR" ) && is_dir( NITROPACK_PLUGIN_DATA_DIR ); // TODO: Convert this to use the Filesystem abstraction for better Redis support
	}

	public function initPluginDataDir() {
		return $this->pluginDataDirExists() || @mkdir( NITROPACK_PLUGIN_DATA_DIR, 0755, true ); // TODO: Convert this to use the Filesystem abstraction for better Redis support
	}

	public function setDisabledReason( $reason ) {
		$this->disabledReason = $reason;
		nitropack_header( "X-Nitro-Disabled-Reason: $reason" );
	}

	public function getDisabledReason() {
		return $this->disabledReason;
	}

	public function setPageType( $type ) {
		$this->pageType = $type;
	}

	public function getPageType() {
		return $this->pageType;
	}

	/**
	 * Get current url
	 *
	 * @return string The current url
	 */
	public function getCurrentUrl() {

		if ( defined( 'NITROPACK_HOST' ) ) {

			return NITROPACK_HOST;
		}

		if ( ! empty( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) {
			$host = $_SERVER['HTTP_X_FORWARDED_HOST'];
		} else {
			$host = ! empty( $_SERVER["HTTP_HOST"] ) ? $_SERVER["HTTP_HOST"] : "";
		}

		$uri = ! empty( $_SERVER["REQUEST_URI"] ) ? $_SERVER["REQUEST_URI"] : "";
		$currentUrl = $host . $uri;

		if ( empty( $currentUrl ) || ( defined( 'WP_CLI' ) && WP_CLI && trim( $currentUrl ) == "localhost" ) ) {

			if ( function_exists( 'get_site_url' ) ) {
				$host = apply_filters( 'nitropack_current_host', get_site_url() );
			} elseif ( function_exists( 'get_option' ) ) {
				$host = apply_filters( 'nitropack_current_host', get_option( 'siteurl' ) );
			}

			if ( $host != '' ) {
				$site_url = parse_url( $host );
				if ( is_array( $site_url ) && isset( $site_url["host"] ) && ! empty( $site_url["host"] ) ) {
					$currentUrl = $site_url["host"];
				}
			}
		}

		if ( stripos( $currentUrl, "www." ) === 0 ) {
			$currentUrl = substr( $currentUrl, 4 );
		}

		return $currentUrl;
	}

	/**
	 * Get current host
	 *
	 * @return string The current host
	 */
	public function getCurrentHost() {

		if ( ! empty( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) {
			$host = $_SERVER['HTTP_X_FORWARDED_HOST'];
		} else {
			$host = ! empty( $_SERVER["HTTP_HOST"] ) ? $_SERVER["HTTP_HOST"] : "";
		}

		if ( empty( $host ) ) {

			$site_url = parse_url( apply_filters( 'nitropack_current_host', ( function_exists( 'get_site_url' ) ? get_site_url() : '' ) ) );
			$host = $site_url["host"];
		}

		return $host;
	}

	/**
	 * Get logger instance
	 *
	 * @return Logger
	 */
	public function getLogger() {
		return $this->logger;
	}

	private static function resolvePathNavigation( $path ) {
		if ( strpos( $path, './' ) !== false ) {
			$path_parts = explode( '/', $path );
			$final_parts = array();

			foreach ( $path_parts as $part ) {
				if ( $part == "." ) {
					continue;
				} else if ( $part == '..' ) {
					array_pop( $final_parts );
				} else {
					$final_parts[] = $part;
				}
			}

			$path = implode( '/', $final_parts );
		}

		return $path;
	}
}
