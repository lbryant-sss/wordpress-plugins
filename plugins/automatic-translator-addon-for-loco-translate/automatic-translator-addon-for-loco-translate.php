<?php
/*
Plugin Name: Automatic Translate Addon For Loco Translate
Description: Loco Translate plugin addon to automatic translate plugins and themes translatable string with one click in any language.
Version: 2.4.7
License: GPL2
Text Domain: loco-auto-translate
Domain Path: languages
Author: Cool Plugins
Author URI: https://coolplugins.net/?utm_source=atlt_plugin&utm_medium=inside&utm_campaign=author_page&utm_content=dashboard
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'ATLT_FILE', __FILE__ );
define( 'ATLT_URL', plugin_dir_url( ATLT_FILE ) );
define( 'ATLT_PATH', plugin_dir_path( ATLT_FILE ) );
define( 'ATLT_VERSION', '2.4.7' );

/**
 * @package Loco Automatic Translate Addon
 * @version 2.4
 */

if ( ! class_exists( 'LocoAutoTranslateAddon' ) ) {

	/** Singleton ************************************/
	final class LocoAutoTranslateAddon {

		/**
		 * The unique instance of the plugin.
		 *
		 * @var LocoAutoTranslateAddon
		 */
		private static $instance;

		/**
		 * Gets an instance of plugin.
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();

				// register all hooks
				self::$instance->register();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 */
		public function __construct() {
			// Add CPT Dashboard initialization
			if (!class_exists('Cpt_Dashboard')) {
				require_once ATLT_PATH . 'admin/cpt_dashboard/cpt_dashboard.php';
				$dashboard = Cpt_Dashboard::instance();
			}

		}

		/**
		 * Registers our plugin with WordPress.
		 */
		public static function register() {
			$thisPlugin = self::$instance;
			register_activation_hook( ATLT_FILE, array( $thisPlugin, 'atlt_activate' ) );
			register_deactivation_hook( ATLT_FILE, array( $thisPlugin, 'atlt_deactivate' ) );

			// run actions and filter only at admin end.
			if ( is_admin() ) {
				add_action( 'plugins_loaded', array( $thisPlugin, 'atlt_check_required_loco_plugin' ) );
				add_action( 'init', array( $thisPlugin, 'atlt_load_textdomain' ) );
				// add notice to use latest loco translate addon
				add_action( 'init', array( $thisPlugin, 'atlt_verify_loco_version' ) );

				add_action( 'init', array( $thisPlugin, 'onInit' ) );

				/*** Plugin Setting Page Link inside All Plugins List */
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $thisPlugin, 'atlt_settings_page_link' ) );
				add_action( 'init', array( $thisPlugin, 'updateSettings' ) );

				add_action( 'plugins_loaded', array( $thisPlugin, 'atlt_include_files' ) );

				add_action( 'admin_enqueue_scripts', array( $thisPlugin, 'atlt_enqueue_scripts' ) );

				/* since version 2.1 */
				add_filter( 'loco_api_providers', array( $thisPlugin, 'atlt_register_api' ), 10, 1 );
				add_action( 'loco_api_ajax', array( $thisPlugin, 'atlt_ajax_init' ), 0, 0 );
				add_action( 'wp_ajax_save_all_translations', array( $thisPlugin, 'save_translations_handler' ) );

				/*
				since version 2.0
				Yandex translate widget integration
				*/
				// add no translate attribute in html tag
				if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'file-edit' ) {
					add_action( 'admin_footer', array( $thisPlugin, 'atlt_load_ytranslate_scripts' ), 100 );
					add_filter( 'admin_body_class', array( $thisPlugin, 'atlt_add_custom_class' ) );
				}

				add_action( 'admin_menu', array( $thisPlugin, 'atlt_add_locotranslate_sub_menu' ), 101 );
			}
		}

		/*
		|----------------------------------------------------------------------
		| Register API Manager inside Loco Translate Plugin
		|----------------------------------------------------------------------
		*/
		function atlt_register_api( array $apis ) {
			$apis[] = array(
				'id'   => 'loco_auto',
				'key'  => '122343',
				'url'  => 'https://locoaddon.com/',
				'name' => 'Automatic Translate Addon',
			);
			return $apis;
		}

		/*
		|----------------------------------------------------------------------
		| Auto Translate Request handler
		|----------------------------------------------------------------------
		*/
		function atlt_ajax_init() {
			if( version_compare( loco_plugin_version(), '2.7', '>=' ) ){
				add_filter( 'loco_api_translate_loco_auto', array( self::$instance, 'loco_auto_translator_process_batch' ), 0, 4 );
			}
			else {
				add_filter('loco_api_translate_loco_auto',array( self::$instance, 'loco_auto_translator_process_batch_legacy' ), 0,3);
			}
		}
		
		public function loco_auto_translator_process_batch_legacy( array $sources, Loco_Locale $locale, array $config ) {
			$items = [];
			foreach( $sources as $text ){
				$items[] = [ 'source' => $text ];
			}
			return $this->loco_auto_translator_process_batch( [], $items, $locale, $config );
		}

		/**
		 * Hook fired as a filter for the "loco_auto" translation api
		 *
		 * @param string[] input strings
		 * @param Loco_Locale target locale for translations
		 * @param array our own api configuration
		 * @return string[] output strings
		 */
		function loco_auto_translator_process_batch(array $targets, array $items, Loco_Locale $locale, array $config) {
			// Extract domain from the referrer URL
			$url_data   = self::$instance->atlt_parse_query( $_SERVER['HTTP_REFERER'] );
			$domain     = isset( $url_data['domain'] ) && ! empty( $url_data['domain'] ) ? sanitize_text_field( $url_data['domain'] ) : 'temp';
			$lang       = sanitize_text_field( $locale->lang );
			$region     = sanitize_text_field( $locale->region );
			$project_id = $domain . '-' . $lang . '-' . $region;




			// Combine transient parts if available
			$allStrings = array();
			$translationData = array();
			
			for ( $i = 0; $i <= 4; $i++ ) {
				$transient_data = get_transient( $project_id . '-part-' . $i );
				
				if ( ! empty( $transient_data ) ) {
					if (isset( $transient_data['strings'] )) {
						$allStrings = array_merge( $allStrings, $transient_data['strings'] );
					}
					if (isset( $transient_data['translation_data'] )) {
						$translationData = array_merge( $translationData, $transient_data['translation_data'] );
					}
				}

			}

			if ( ! empty( $allStrings ) ) {
				foreach ( $items as $i => $item ) {
					// Find the index of the source string in the cached strings
					$index = array_search( $item['source'], array_column( $allStrings, 'source' ) );

					if (is_numeric($index) && isset($allStrings[$index]['target'])) {
						$targets[$i] = sanitize_text_field($allStrings[$index]['target']);
					} else {
						$targets[$i] = '';
					}
				}

				// Safely extract and sanitize translation metadata
				$metadata = array(
					'translation_provider' => isset($translationData['translation_provider']) ? sanitize_text_field($translationData['translation_provider']) : '',
					'string_count' => isset($translationData['string_count']) ? absint($translationData['string_count']) : 0,
					'character_count' => isset($translationData['character_count']) ? absint($translationData['character_count']) : 0,
					'time_taken' => isset($translationData['time_taken']) ? absint($translationData['time_taken']) : 0,
					'pluginORtheme' => isset($translationData['pluginORtheme']) ? sanitize_text_field($translationData['pluginORtheme']) : '',
					'pluginORthemeName' => isset($translationData['pluginORthemeName']) ? sanitize_text_field($translationData['pluginORthemeName']) : '',
					'target_language' => $locale->region ? sanitize_key($lang . '_' . $locale->region) : sanitize_key($lang)
				);

				if (class_exists('Cpt_Dashboard')) {
					Cpt_Dashboard::store_options(
						'atlt',
						'plugins_themes',
						'update',
						array(
							'plugins_themes' => $metadata['pluginORthemeName'],
							'service_provider' => $metadata['translation_provider'],
							'source_language' => 'en',
							'target_language' => $metadata['target_language'],
							'time_taken' => $metadata['time_taken'],
							'string_count' => $metadata['string_count'],
							'character_count' => $metadata['character_count'],
							'date_time' => date('Y-m-d H:i:s'),
							'version_type' => 'free'
						)
					);
				}

				return $targets;
			} else {
				throw new Loco_error_Exception( 'Please translate strings using the Auto Translate addon button first.' );
			}
		}

		function atlt_parse_query( $var ) {
			/**
			 *  Use this function to parse out the query array element from
			 *  the output of parse_url().
			 */

			$var = parse_url( $var, PHP_URL_QUERY );
			$var = html_entity_decode( $var );
			$var = explode( '&', $var );
			$arr = array();

			foreach ( $var as $val ) {
				$x            = explode( '=', $val );
				if ( isset( $x[1] ) ) {
					$arr[ sanitize_text_field( $x[0] ) ] = sanitize_text_field( $x[1] );
				}
			}
			unset( $val, $x, $var );
			return $arr;
		}

		/*
		|----------------------------------------------------------------------
		| Save string translation inside cache for later use
		|----------------------------------------------------------------------
		*/
		// save translations inside transient cache for later use
		function save_translations_handler() {

			check_ajax_referer( 'loco-addon-nonces', 'wpnonce' );

			if ( isset( $_POST['data'] ) && ! empty( $_POST['data'] ) && isset( $_POST['part'] ) ) {

				$allStrings = json_decode( stripslashes( $_POST['data'] ), true );
				$translationData = isset($_POST['translation_data']) ? json_decode(stripslashes($_POST['translation_data']), true) : null;

				if ( empty( $allStrings ) ) {
					echo json_encode(
						array(
							'success' => false,
							'error'   => 'No data found in the request. Unable to save translations.',
						)
					);
					wp_die();
				}

				// Determine the project ID based on the loop value
				$projectId = $_POST['project-id'] . $_POST['part'];
				
				$dataToStore = array(
					'strings' => $allStrings,
					'translation_data' => $translationData
				);

				// Save the combined data in transient
				$rs = set_transient( $projectId, $dataToStore, 5 * MINUTE_IN_SECONDS );
				echo json_encode(
					array(
						'success'  => true,
						'message'  => 'Translations successfully stored in the cache.',
						'response' => $rs == true ? 'saved' : 'cache already exists',
					)
				);

			} else {
				// Security check failed or missing parameters
				echo json_encode( array( 'error' => 'Invalid request. Missing required parameters.' ) );
			}
			wp_die();
		}

		/*
		|----------------------------------------------------------------------
		| Yandex Translate Widget Integrations
		| add no translate attribute in html tag
		|----------------------------------------------------------------------
		*/
		function atlt_load_ytranslate_scripts() {
			if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'file-edit' ) {
				echo "<script>document.getElementsByTagName('html')[0].setAttribute('translate', 'no');</script>";
			}
		}
		// add no translate class in admin body to disable whole page translation
		function atlt_add_custom_class( $classes ) {
			return "$classes notranslate";
		}

		/*
		|----------------------------------------------------------------------
		| check if required "Loco Translate" plugin is active
		| also register the plugin text domain
		|----------------------------------------------------------------------
		*/

		public function atlt_load_textdomain() {
			// load language files
			load_plugin_textdomain( 'loco-auto-translate', false, basename( dirname( __FILE__ ) ) . '/languages/' );
		}
		
		public function atlt_check_required_loco_plugin() {
			if ( ! function_exists( 'loco_plugin_self' ) ) {
				add_action( 'admin_notices', array( self::$instance, 'atlt_plugin_required_admin_notice' ) );
			}
		}
		/*
		|----------------------------------------------------------------------
		| Notice to 'Admin' if "Loco Translate" is not active
		|----------------------------------------------------------------------
		*/
		public function atlt_plugin_required_admin_notice() {
			if ( current_user_can( 'activate_plugins' ) ) {
				$url         = 'plugin-install.php?tab=plugin-information&plugin=loco-translate&TB_iframe=true';
				$title       = 'Loco Translate';
				$plugin_info = get_plugin_data( __FILE__, true, true );
				echo '<div class="error"><p>' .
				sprintf(
					__(
						'In order to use <strong>%1$s</strong> plugin, please install and activate the latest version  of <a href="%2$s" class="thickbox" title="%3$s">%4$s</a>',
						'automatic-translator-addon-for-loco-translate'
					),
					esc_attr( $plugin_info['Name'] ),
					esc_url( $url ),
					esc_attr( $title ),
					esc_attr( $title )
				) . '.</p></div>';

				 deactivate_plugins( __FILE__ );
			}
		}
		/*
		|----------------------------------------------------------------------
		| create 'settings' link in plugins page
		|----------------------------------------------------------------------
		*/
		public function atlt_settings_page_link( $links ) {
			$links[] = '<a style="font-weight:bold" href="' . esc_url( get_admin_url( null, 'admin.php?page=loco-atlt-register' ) ) . '">Buy PRO</a>';
			return $links;
		}

		/*
		|----------------------------------------------------------------------
		| Update and remove old review settings
		|----------------------------------------------------------------------
		*/
		public function updateSettings() {
			if ( get_option( 'atlt-ratingDiv' ) ) {
				update_option( 'atlt-already-rated', get_option( 'atlt-ratingDiv' ) );
				delete_option( 'atlt-ratingDiv' );
			}
		}

		/*
		|----------------------------------------------------------------------
		| check User Status
		|----------------------------------------------------------------------
		*/
		public function atlt_verify_loco_version() {
			if ( function_exists( 'loco_plugin_version' ) ) {
				$locoV = loco_plugin_version();
				if ( version_compare( $locoV, '2.4.0', '<' ) ) {
					add_action( 'admin_notices', array( self::$instance, 'use_loco_latest_version_notice' ) );
				}
			}
		}
		/*
		|----------------------------------------------------------------------
		| Notice to use latest version of Loco Translate plugin
		|----------------------------------------------------------------------
		*/
		public function use_loco_latest_version_notice() {
			if ( current_user_can( 'activate_plugins' ) ) {
				$url         = 'plugin-install.php?tab=plugin-information&plugin=loco-translate&TB_iframe=true';
				$title       = 'Loco Translate';
				$plugin_info = get_plugin_data( __FILE__, true, true );
				echo '<div class="error"><p>' .
				sprintf(
					__(
						'In order to use <strong>%1$s</strong> (version <strong>%2$s</strong>), Please update <a href="%3$s" class="thickbox" title="%4$s">%5$s</a> official plugin to a latest version (2.4.0 or upper)',
						'automatic-translator-addon-for-loco-translate'
					),
					esc_attr( $plugin_info['Name'] ),
					esc_attr( $plugin_info['Version'] ),
					esc_url( $url ),
					esc_attr( $title ),
					esc_attr( $title )
				) . '.</p></div>';
			}
		}

		/*
		|----------------------------------------------------------------------
		| required php files
		|----------------------------------------------------------------------
		*/
		public function atlt_include_files() {
			if ( is_admin() ) {
				require_once ATLT_PATH . 'includes/Helpers/Helpers.php';
				
				// Check if user has already rated
				$alreadyRated = get_option('atlt-already-rated') != false ? get_option('atlt-already-rated') : "no";
				
				// Only show review notice if user hasn't rated yet
				if ($alreadyRated != "yes") {
					//  Display review notice
					if (class_exists('Cpt_Dashboard')) {
						Cpt_Dashboard::review_notice(
							'atlt', // Required
							'Automatic Translate Addon For Loco Translate', // Required
							'https://wordpress.org/plugins/automatic-translator-addon-for-loco-translate/reviews/#new-post', // Required
							ATLT_URL . '/assets/images/atlt-logo.png' // Optional
						);
					}
				}

				require_once ATLT_PATH . 'includes/Feedback/class.feedback-form.php';
				new ATLT_FeedbackForm();
			}
		}

		/*
		|------------------------------------------------------------------------
		|  Enqueue required JS file
		|------------------------------------------------------------------------
		*/
		function atlt_enqueue_scripts( $hook ) {
			// load assets only on editor page
			if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'file-edit' ) {
				wp_register_script( 'loco-addon-custom', ATLT_URL . 'assets/js/custom.min.js', array( 'loco-translate-admin' ), ATLT_VERSION, true );
				wp_register_style(
					'loco-addon-custom-css',
					ATLT_URL . 'assets/css/custom.min.css',
					null,
					ATLT_VERSION,
					'all'
				);
				// load yandex widget
				wp_register_script( 'atlt-yandex-widget', ATLT_URL . 'assets/js/widget.js?widgetId=ytWidget&pageLang=en&widgetTheme=light&autoMode=false', array( 'loco-translate-admin' ), ATLT_VERSION, true );

				wp_enqueue_script( 'loco-addon-custom' );
				wp_enqueue_script( 'atlt-yandex-widget' );
				wp_enqueue_style( 'loco-addon-custom-css' );

				$extraData['ajax_url']        = admin_url( 'admin-ajax.php' );
				$extraData['nonce']           = wp_create_nonce( 'loco-addon-nonces' );
				$extraData['ATLT_URL']        = ATLT_URL;
				$extraData['preloader_path']  = 'preloader.gif';
				$extraData['gt_preview']      = 'google-translate-logo.png';
				$extraData['dpl_preview']     = 'deepl-translate-logo.png';
				$extraData['yt_preview']      = 'yandex-translate-logo.png';
				$extraData['chatGPT_preview'] = 'chatgpt-logo.png';
				$extraData['geminiAI_preview'] = 'gemini-ai-logo.png';
				$extraData['chromeAi_preview']      = 'chrome-built-in-ai-logo.png';
				$extraData['document_preview'] = 'document.svg';
				$extraData['information_preview'] = 'information.svg';
				$extraData['extra_class']     = is_rtl() ? 'atlt-rtl' : '';

				$extraData['loco_settings_url'] = admin_url( 'admin.php?page=loco-config&action=apis' );

				wp_localize_script( 'loco-addon-custom', 'extradata', $extraData );
				// copy object
				wp_add_inline_script(
					'loco-translate-admin',
					'
            var returnedTarget = JSON.parse(JSON.stringify(window.loco));
            window.locoConf=returnedTarget;'
		            );

			}
		}

		/*
		|------------------------------------------------------
		|   show message if PRO has already active
		|------------------------------------------------------
		*/
		public function onInit() {
			if ( in_array(
				'loco-automatic-translate-addon-pro/loco-automatic-translate-addon-pro.php',
				apply_filters( 'active_plugins', get_option( 'active_plugins' ) )
			) ) {

				if ( get_option( 'atlt-pro-version' ) != false &&
				  version_compare( get_option( 'atlt-pro-version' ), '1.4', '<' ) ) {

					  add_action( 'admin_notices', array( self::$instance, 'atlt_use_pro_latest_version' ) );
				} else {
					add_action( 'admin_notices', array( self::$instance, 'atlt_pro_already_active_notice' ) );
					return;
				}
			}
		}

		public function atlt_pro_already_active_notice() {
			echo '<div class="error loco-pro-missing" style="border:2px solid;border-color:#dc3232;"><p><strong>Loco Automatic Translate Addon Pro</strong> is already active so no need to activate free anymore.</p> </div>';
		}

		public function atlt_use_pro_latest_version() {
			echo '<div class="error loco-pro-missing" style="border:2px solid;border-color:#dc3232;"><p><strong>Please use <strong>Loco Automatic Translate Addon Pro</strong> latest version 1.4 or higher to use auto translate premium features.</p> </div>';
		}

		/*
		|------------------------------------------------------
		|    Plugin activation
		|------------------------------------------------------
		*/
		public function atlt_activate() {
			update_option( 'atlt-version', ATLT_VERSION );
			update_option( 'atlt-installDate', gmdate( 'Y-m-d h:i:s' ) );
			update_option( 'atlt-type', 'free' );
		}

		/*
		|-------------------------------------------------------
		|    Plugin deactivation
		|-------------------------------------------------------
		*/
		public function atlt_deactivate() {
			delete_option( 'atlt-version' );
			delete_option( 'atlt-installDate' );
			delete_option( 'atlt-type' );
		}

		/*
		|-------------------------------------------------------
		|   Automatic Translate Addon For Loco Translate  admin page
		|-------------------------------------------------------
		*/
		function atlt_add_locotranslate_sub_menu() {
			add_submenu_page(
				'loco',
				'Loco Automatic Translate',
				'Auto Translate Addon',
				'manage_options',
				'loco-atlt-register',
				array( self::$instance, 'atlt_options_page' )
			);
		}

		function atlt_options_page() {
			$text_domain = 'loco-auto-translate';
			?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<div class="el-license-container">
				<h3 class="el-license-title"><i class="dashicons-before dashicons-translation"></i> <?php _e( 'Automatic Translate Addon For Loco Translate', $text_domain ); ?></h3>
				<div class="el-license-content">
					
					<div class="el-license-textbox">
						<a class="button button-primary" href='<?php echo esc_url( admin_url( 'admin.php?page=loco-theme' ) ); ?>'>Translate Themes</a> <a class="button button-secondary" href='<?php echo esc_url( admin_url( 'admin.php?page=loco-plugin' ) ); ?>'>Translate Plugins</a>
						<h3>Compare Free vs Pro (<a href='https://coolplugins.net/?utm_source=atlt_plugin&utm_medium=inside&utm_campaign=author_page&utm_content=dashboard' target='_blank'>Buy Pro Plugin</a>)</h3>
						<table class="loco-addon-license">
						<tr>
						<th>Features</th>
						<th>Free Plugin</th>
						<th>Premium Plugin</th>
						</tr>
						<tr>
						<td>Yandex Translate Widget Support<br/><img style="border: 1px solid;" src="<?php echo ATLT_URL . '/assets/images/yandex-translate-logo.png'; ?>"/></td>
						<td><span style="color:green;font-size:1.4em;">✔</span> Available</td>
						<td><span style="color:green;font-size:1.4em;">✔</span> Available</td>
						</tr>
						<tr>
						<td>Unlimited Translations</td>
						<td><span style="color:green;font-size:1.4em;">✔</span> Available<br/><span style="font-size:11px;font-weight:bold;">(Via Yandex Only)</span></td>
						<td><span style="color:green;font-size:1.4em;">✔</span> Available<br/><span style="font-size:11px;font-weight:bold;">(Via Yandex, Google & AI)</td>
						</tr>
						<tr>
						<td>No API Key Required</td>
						<td><span style="color:green;font-size:1.4em;">✔</span> API Not Required<br/><span style="font-size:11px;font-weight:bold;">(Support's Only Yandex)</span></td>
						<td><span style="color:green;font-size:1.4em;">✔</span> API Not Required<br/><span style="font-size:11px;font-weight:bold;">(Support's Yandex, Google, DeepL & AI)</span></td>
						</tr>
						<tr style="background:#fffb7a;font-weight: bold;">
						<td>Google Translate Widget Support<br/><img style="border: 1px solid;" src="<?php echo ATLT_URL . '/assets/images/google-translate-logo.png'; ?>"/></td>
						<td>❌ Not Available</td>
						<td><span style="color:green;font-size:1.4em;">✔</span> Available<br/><span style="font-size:11px;font-weight:bold;">(Better than Yandex)</span></td>
						</tr>
						<tr style="background:#fffb7a;font-weight: bold;">
						<td>DeepL Doc Translator Support<br/><img style="border: 1px solid;" src="<?php echo ATLT_URL . '/assets/images/deepl-translate-logo.png'; ?>"/></td>
						<td>❌ Not Available</td>
						<td><span style="color:green;font-size:1.4em;">✔</span> Available<br/><span style="font-size:11px;font-weight:bold;">(Limited Free Docs Translations / Day)</span></td>
						</tr>
						<tr style="background:#fffb7a;font-weight: bold;">
						<td>AI Translator Support<br/><img style="border: 1px solid;" src="<?php echo ATLT_URL . '/assets/images/chatgpt-logo.png'; ?>"/></td>
						<td>❌ Not Available</td>
						<td><span style="color:green;font-size:1.4em;">✔</span> Available<br/></td>
						</tr>
						<tr style="background:#fffb7a;font-weight: bold;">
						<td>AI Translator Support<br/><img style="border: 1px solid;" src="<?php echo ATLT_URL . '/assets/images/gemini-ai-logo.png'; ?>"/></td>
						<td>❌ Not Available</td>
						<td><span style="color:green;font-size:1.4em;">✔</span> Available<br/><span style="font-size:11px;font-weight:bold;"><a href="https://ai.google.dev/gemini-api/docs/api-key" target="_blank">(Get free API key)</a></span></td>
						</tr>
						<tr style="background:#fffb7a;font-weight: bold;">
						<td>AI Translator Support<br/><img style="border: 1px solid;" src="<?php echo ATLT_URL . '/assets/images/chrome-built-in-ai-logo.png'; ?>"/></td>
						<td>❌ Not Available</td>
						<td><span style="color:green;font-size:1.4em;">✔</span> Available<br/></td>
						</tr>
						<tr>
						<td><strong>Premium Support</strong></td>
						<td>❌ Not Available<br/><strong>(Support Time: 7 – 10 days)</strong></td>
						<td><span style="color:green;font-size:1.4em;">✔</span> Available<br/><strong>(Support Time: 24 - 48 Hrs)</strong></td>
						</tr>
						
						</table>
						
					</div>
					<div class="el-license-form">
						<strong style="color:#e00b0b;">*Important Points</strong>
						<ol>
						<li>Premium version supports <b>Google Translate</b> for better translations.</li>
						<li>Automatic translate providers do not support HTML and special characters translations. So plugin will not automatic translate any string that contains HTML or special characters.</li>
						<li>If any auto-translation provider stops any of its free translation service then plugin will not support that translation service provider.</li>
						<li>DeepL Translate provides better translations than Google, Yandex or other machine translation providers. <a href="https://techcrunch.com/2017/08/29/deepl-schools-other-online-translators-with-clever-machine-learning/" target="_blank"><b>Read review by Techcrunch!</b></a></li>
						<li>Currently DeepL Doc Translator provides limited number of free docs translations per day. You can purchase to <a href="https://www.deepl.com/pro?cta=homepage-free-trial#pricing" target="_blank">DeepL Pro</a> to increase this limit.</li>
						<li>To start translation using Gemini <a href="https://ai.google.dev/gemini-api/docs/api-key" target="_blank">get free Gemini API key</a></li>
						</ol>
						<br/>
						<a class="button button-primary" href='https://locoaddon.com/pricing/?utm_source=atlt_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=dashboard' target='_blank'>Buy Pro Plugin</a>
						
						<h3>Explore Our Other Automatic Translation Plugins</h3>
						<ul style="list-style:disc">
							<li><strong><a target="_blank" href="https://coolplugins.net/product/automatic-translate-addon-for-translatepress-pro/?utm_source=atlt_plugin&utm_medium=inside&utm_campaign=tpa_get_pro&utm_content=dashboard">AI Translation for TranslatePress</a></strong>
							It enables you to translate entire page content with a single click, removing the need to manually translate each element individually.
							</br><a style="margin-top:5px" class="button button-primary" target="_blank" href="https://coolplugins.net/product/automatic-translate-addon-for-translatepress-pro/?utm_source=atlt_plugin&utm_medium=inside&utm_campaign=tpa_get_pro&utm_content=dashboard">Try It Now</a>
							</li>
							<li><strong><a target="_blank" href="https://coolplugins.net/product/automatic-translations-for-polylang/?utm_source=atlt_plugin&utm_medium=inside&utm_campaign=atfp_get_pro&utm_content=dashboard">AI Translation for Polylang</a></strong>
								It allows you to translate entire Polylang-created pages, posts, and meta fields with a single click, eliminating the need for manual translation of each element. Compatible with Gutenberg and popular blocks, it makes managing multilingual websites effortless.
								</br><a  style="margin-top:5px" class="button button-primary" target="_blank" href="https://coolplugins.net/product/automatic-translations-for-polylang/?utm_source=atlt_plugin&utm_medium=inside&utm_campaign=atfp_get_pro&utm_content=dashboard">Try It Now</a>
							</li>
						</ul>

						
						<div class="el-pluginby">
							Plugin by<br/>
							<a href="https://coolplugins.net/?utm_source=atlt_plugin&utm_medium=inside&utm_campaign=author_page&utm_content=dashboard" target="_blank"><img src="<?php echo ATLT_URL . '/assets/images/coolplugin-logo.png'; ?>"/></a>
						</div>

					</div>
				</div>
			</div>
			</form>
			<style type="text/css">
			  .el-license-container{margin-top:20px;padding:0;display:inline-block;margin:15px auto;box-sizing:border-box;width:calc(100% - 20px);background:#fff;border-radius:10px;border:1px solid #ddd;box-shadow:0 0 10px -5px #afafaf;overflow:hidden;position:relative}.el-license-container *{box-sizing:border-box}
			  .el-license-container h3.el-license-title{background-color:#5cb85c;background:linear-gradient(to right,#5cb85c,#1f9e5e);padding:20px 10px;margin:0;display:inline-block;width:100%;color:#fff;font-size:22px;line-height:22px}
				.el-license-form,
				.el-license-textbox {
				display: inline-block;
				width: calc(50% - 5px);
				vertical-align: top;
				}.el-license-textbox {
				padding-right: 40px;
				}.el-license-container .el-license-content{padding:25px;width:100%;display:inline-block}.el-license-container .el-license-title{margin-top:0;font-size:30px}table.loco-addon-license{width:100%;table-layout:fixed !IMPORTANT}table.loco-addon-license tr th,table.loco-addon-license tr td{border:1px solid #bbb;padding:12px;text-align:center;width:33%}table.loco-addon-license img{margin-top:5px;height:21px!important;width: auto !important;border:0 !important;}table.loco-addon-license tr td strong img{height:28px;width:auto;vertical-align:middle}.el-pluginby{width:100%;display:block;text-align:right;font-style:italic}.el-pluginby img{max-width:100px}@media only screen and (max-width:940px){.el-license-form,.el-license-textbox{width:100%}.el-license-form{padding-right:0}}
		   </style>
			<?php
		}

		/**
		 * Throw error on object clone.
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'loco-auto-translate' ), '2.3' );
		}

		/**
		 * Disable unserializing of the class.
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'loco-auto-translate' ), '2.3' );
		}

	}

	function ATLT() {
		return LocoAutoTranslateAddon::get_instance();
	}
	ATLT();

}

