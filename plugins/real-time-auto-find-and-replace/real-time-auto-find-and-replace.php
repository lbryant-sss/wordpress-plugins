<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Better Find and Replace
 * Plugin URI:        https://codesolz.net/our-products/wordpress-plugin/real-time-auto-find-and-replace/
 * Description:       The plugin has the capability to automatically search for specific words and replace them with your preferred words. You have the ability to create your own search and replace rules for real-time replacement. These rules will be applied before the page is displayed in the browser, as well as during any background interactions with other social plugins.
 * Version:           1.7.4
 * Author:            CodeSolz
 * Author URI:        https://www.codesolz.net
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl.txt
 * Domain Path:       /languages
 * Text Domain:       real-time-auto-find-and-replace
 * Requires PHP: 7.0
 * Requires At Least: 4.0
 * Tested Up To: 6.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Real_Time_Auto_Find_And_Replace' ) ) {

	class Real_Time_Auto_Find_And_Replace {

		/**
		 * Hold actions hooks
		 *
		 * @var array
		 */
		private static $rtaafr_hooks = array();

		/**
		 * Hold hooks status
		 */
		static $hooks_loaded = false;

		/**
		 * Hold version
		 *
		 * @var String
		 */
		private static $version = '1.7.4';

		/**
		 * Hold version
		 *
		 * @var String
		 */
		private static $db_version = '1.0.3';

		/**
		 * Hold nameSpace
		 *
		 * @var string
		 */
		private static $namespace = 'RealTimeAutoFindReplace';

		/**
		 * Constructor
		 */
		public function __construct() {

			// load plugins constant.
			self::set_constants();

			// load core files.
			self::load_core_framework();

			// load init.
			self::load_hooks();

			/** Called during the plugin activation */
			self::on_activate();

			/** Load textdomain */
			add_action( 'init', array( __CLASS__, 'init_textdomain' ), 15 );

			/**Init necessary functions */
			add_action( 'plugins_loaded', array( __CLASS__, 'rtaafr_init_function' ), 14 );

			/**Check plugin db*/
			add_action( 'plugins_loaded', array( __CLASS__, 'rtaafr_check_db' ), 17 );
		}

		/**
		 * Set constant data
		 */
		private static function set_constants() {

			$constants = array(
				'CS_RTAFAR_VERSION'           => self::$version, // Define current version.
				'CS_RTAFAR_DB_VERSION'        => self::$db_version, // Define current db version.
				'CS_RTAFAR_HOOKS_DIR'         => untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/core/actions/', // plugin hooks dir.
				'CS_RTAFAR_BASE_DIR_PATH'     => untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/', // Hold plugins base dir path.
				'CS_RTAFAR_PLUGIN_ASSET_URI'  => plugin_dir_url( __FILE__ ) . 'assets/', // Define asset uri.
				'CS_RTAFAR_PLUGIN_LIB_URI'    => plugin_dir_url( __FILE__ ) . 'lib/', // Library uri.
				'CS_RTAFAR_PLUGIN_IDENTIFIER' => plugin_basename( __FILE__ ), // plugins identifier - base dir.
				'CS_RTAFAR_PLUGIN_NAME'       => 'Better Find And Replace', // Plugin name.
				'CS_NOTICE_ID'                => 'rtarar_notice_dismiss', // Plugin Notice id.
			);

			foreach ( $constants as $name => $value ) {
				self::set_constant( $name, $value );
			}

			return true;
		}

		/**
		 * Set constant
		 *
		 * @param String $name store constant names.
		 * @param String $value store values.
		 * @return boolean
		 */
		private static function set_constant( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
			return true;
		}


		/**
		 * Autoload core framework.
		 */
		private static function load_core_framework() {
			require_once CS_RTAFAR_BASE_DIR_PATH . 'vendor/autoload.php';
		}

		/**
		 * Load Action Files.
		 *
		 * @return classes
		 */
		private static function load_hooks() {
			if ( self::$hooks_loaded ) {
				return self::$rtaafr_hooks; // Avoid reloading
			}

			$namespace = self::$namespace . '\\actions\\';
			$hooks_dir = realpath( CS_RTAFAR_HOOKS_DIR );

			if ( $hooks_dir === false || ! is_dir( $hooks_dir ) ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( 'Invalid hooks directory: ' . CS_RTAFAR_HOOKS_DIR );
				}
				return array();
			}

			$iterator = new \DirectoryIterator( $hooks_dir );
			foreach ( $iterator as $file ) {
				if ( $file->isDot() || $file->isDir() || $file->getExtension() !== 'php' ) {
					continue;
				}

				$class_name = basename( $file->getFilename(), '.php' );
				$class      = $namespace . $class_name;

				if ( class_exists( $class ) && ! isset( self::$rtaafr_hooks[ $class ] ) ) {
					self::$rtaafr_hooks[ $class ] = new $class();
				}
			}

			self::$hooks_loaded = true; // Mark as loaded
			return self::$rtaafr_hooks;
		}

		/**
		 * Init activation hook
		 */
		private static function on_activate() {

			// activation hook.
			register_activation_hook( __FILE__, array( self::$namespace . '\\install\\Activate', 'on_activate' ) );

			// deactivation hook.
			register_deactivation_hook( __FILE__, array( self::$namespace . '\\install\\Activate', 'on_deactivate' ) );

			return true;
		}

		/**
		 * Init textdomain
		 */
		public static function init_textdomain() {
			load_plugin_textdomain( 'real-time-auto-find-and-replace', false, CS_RTAFAR_BASE_DIR_PATH . '/languages/' );
		}

		/**
		 * Init plugin's functions
		 *
		 * @return void
		 */
		public static function rtaafr_init_function() {

			// init notices.
			\RealTimeAutoFindReplace\admin\notices\RtafarNotices::init();
		}

		/**
		 * Check DB
		 *
		 * @return void
		 */
		public static function rtaafr_check_db() {
			$cls_install = self::$namespace . '\install\Activate';
			$cls_install::check_db_status();
		}
	}

	global $RTAFAF;
	$RTAFAF = new Real_Time_Auto_Find_And_Replace();
}
