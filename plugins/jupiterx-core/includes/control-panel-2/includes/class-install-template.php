<?php
/**
 * This class is responsible manage all jupiter templates
 * it will communicate with artbees API and get list of templates , install them or remove them.
 *
 * @author       Artbees <info@artbees.net>
 * @copyright    Artbees LTD (c)
 *
 * @link         https://artbees.net
 * @since        1.0
 * @version      1.0
 *
 * @todo Clean up.
 *
 * phpcs:ignoreFile
 * @SuppressWarnings(PHPMD)
 */
if ( ! class_exists( 'JupiterX_Core_Control_Panel_Install_Template' ) ) {
	class JupiterX_Core_Control_Panel_Install_Template {


		private $layer_slider_slug = 'layerslider';

		private $theme_name;

		public $tgmpa;

		public function setThemeName( $theme_name ) {
			$this->theme_name = $theme_name;
		}

		public function getThemeName() {
			return $this->theme_name;
		}

		private $api_url;

		public function setApiURL( $api_url ) {
			$this->api_url = $api_url;
		}

		public function getApiURL() {
			return $this->api_url;
		}

		private $template_id;

		public function setTemplateID( $template_id ) {
			$this->template_id = $template_id;
		}

		public function getTemplateID() {
			return intval( $this->template_id );
		}

		private $template_name;

		public function setTemplateName( $template_name ) {
			$this->template_name = $template_name;
		}

		public function getTemplateName() {
			return strtolower( $this->template_name );
		}

		private $template_file_name;

		public function setTemplateFileName( $template_file_name ) {
			$this->template_file_name = $template_file_name;
		}

		public function getTemplateFileName() {
			return $this->template_file_name;
		}

		private $template_remote_address;

		public function setTemplateRemoteAddress( $template_remote_address ) {
			$this->template_remote_address = $template_remote_address;
		}

		public function getTemplateRemoteAddress() {
			return $this->template_remote_address;
		}

		private $template_content_file_name;

		public function setTemplateContentFileName( $template_content_file_name ) {
			$this->template_content_file_name = $template_content_file_name;
		}

		public function getTemplateContentFileName() {
			return $this->template_content_file_name;
		}

		private $widget_file_name;

		public function setWidgetFileName( $widget_file_name ) {
			$this->widget_file_name = $widget_file_name;
		}

		public function getWidgetFileName() {
			return $this->widget_file_name;
		}

		/**
		 * Settings filename.
		 *
		 * @since 1.0
		 * @var string
		 */
		private $settings_file_name;

		/**
		 * Set Settings filename.
		 *
		 * @since 1.0
		 * @param string $settings_file_name Settings filename.
		 */
		public function set_settings_file_name( $settings_file_name ) {
			$this->settings_file_name = $settings_file_name;
		}

		/**
		 * Get Settings filename.
		 *
		 * @since 1.0
		 * @return string Settings filename.
		 */
		public function get_settings_file_name() {
			return $this->settings_file_name;
		}

		private $upload_dir;

		public function setUploadDir( $upload_dir ) {
			$this->upload_dir = $upload_dir;
		}

		public function getUploadDir() {
			return $this->upload_dir;
		}

		private $base_path;

		public function setBasePath( $base_path ) {
			$this->base_path = $base_path;
		}

		public function getBasePath() {
			return $this->base_path;
		}

		private $base_url;

		public function setBaseUrl( $base_url ) {
			$this->base_url = $base_url;
		}

		public function getBaseUrl() {
			return $this->base_url;
		}

		private $message;

		public function setMessage( $message ) {
			$this->message = $message;
		}

		public function getMessage() {
			return $this->message;
		}

		/**
		 * Construct.
		 *
		 * @param bool $system_text_env if you want to create an instance of this method for phpunit it should be true
		 */
		public function __construct() {

			// Get TGMPA.
			if ( class_exists( 'TGM_Plugin_Activation' ) ) {
				$this->tgmpa = isset( $GLOBALS['tgmpa'] ) ? $GLOBALS['tgmpa'] : TGM_Plugin_Activation::get_instance();
			}

			$menu_items_access = get_site_option( 'menu_items' );

			$this->activate_required_jet_modules();

			$this->setThemeName( 'jupiterx' );

			$this->setApiURL( 'https://artbees.net/api/v2/' );

			$this->setUploadDir( wp_upload_dir() );
			$this->setBasePath( $this->getUploadDir()['basedir'] . '/jupiterx_templates/' );
			$this->setBaseUrl( $this->getUploadDir()['baseurl'] . '/jupiterx_templates/' );

			$this->setTemplateContentFileName( 'theme_content.xml' );
			$this->setWidgetFileName( 'widget_data.wie' );
			$this->set_settings_file_name( 'settings.json' );
			global $wpdb;

			if ( ! defined( 'JupiterX_LOAD_IMPORTERS' ) ) {
				define( 'JupiterX_LOAD_IMPORTERS', true );
			}

			add_filter( 'tgmpa_load', '__return_true', 10, 1 );

			add_action( 'wp_ajax_jupiterx_core_cp_template_lazy_load', array( &$this, 'loadTemplatesFromApi' ) );
			add_action( 'wp_ajax_abb_install_template_procedure', array( &$this, 'install_template_procedure' ) );

			// Action only for importing theme content with Server-Sent Event.
			add_action( 'wp_ajax_abb_install_template_sse', array( &$this, 'import_theme_content_sse' ) );

			add_action( 'wp_ajax_abb_get_templates_categories', array( &$this, 'getTemplateCategoryListFromApi' ) );
			add_action( 'wp_ajax_abb_restore_latest_db', array( &$this, 'restoreLatestDB' ) );
			add_action( 'wp_ajax_abb_is_restore_db', array( &$this, 'isRestoreDB' ) );

			add_action( 'wp_ajax_jupiterx_core_cp_uninstall_template', array( &$this, 'uninstallTemplate' ) );
			add_action( 'wp_ajax_abb_get_template_psd_link', array( &$this, 'get_template_psd_link' ) );
		}

		public function enable_svg_support( $mimes ) {
			$mimes['svg'] = 'image/svg+xml';
			$mimes['zip'] = 'application/zip';

			return $mimes;
		}

		public function install_template_procedure() {
			check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'You do not have access to this section.', 'jupiterx-core' );
			}

			$template_id    = ( isset( $_POST['template_id'] ) ? intval( $_POST['template_id'] ) : 0 );
			$this->setTemplateID( $template_id );
			$template_name  = ( isset( $_POST['template_name'] ) ? sanitize_text_field( $_POST['template_name'] ) : null );
			$import_media   = ( isset( $_POST['import_media'] ) ? sanitize_text_field( $_POST['import_media'] ) : false );
			$type           = ( isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : null );
			$partial_import = ( isset( $_POST['partial_import'] ) ? filter_var( $_POST['partial_import'], FILTER_VALIDATE_BOOLEAN ) : false );

			jupiterx_log(
				"[Control Panel > Templates] To install a template (step: {$type}), the following data is expected to be an array consisting of 'template_id', 'template_name', 'import_media', 'type' and 'partial_import'.",
				$_POST
			);

			if ( $import_media ) {
				if (
					empty( array_key_exists( 'svg_support', get_option( 'jupiterx' ) ) ) ||
					! empty( jupiterx_get_option( 'svg_support' ) )
				) {
					add_filter( 'upload_mimes', [ $this, 'enable_svg_support' ] );
					jupiterx_update_option( 'svg_support', 1 );
				}

				if ( ! empty( get_option( 'elementor_unfiltered_files_upload' ) ) ) {
					update_option( 'elementor_unfiltered_files_upload', 1 );
				}
			}

			if ( is_null( $template_name ) || is_null( $type ) ) {
				$this->message( 'A system problem occur while installing, please contact Artbees support.', false );
				return false;
			}

			switch ( $type ) {
				case 'preparation':
					$this->preparation( $template_name );
					break;
				case 'backup_db':
					$this->backupDB();
					break;
				case 'backup_media_records':
					$this->backup_media_records();
					break;
				case 'restore_media_records':
					$this->restore_media_records();
					break;
				case 'reset_db':
					$this->resetDB();
					break;
				case 'upload':
					$this->uploadTemplateToServer( $template_name );
					break;
				case 'unzip':
					$this->unzipTemplateInServer( $template_name );
					break;
				case 'validate':
					$this->validateTemplateFiles( $template_name );
					break;
				case 'install_plugins':
					$this->installRequiredPlugins( $template_name );
					break;
				case 'activate_plugins':
					$this->activateRequiredPlugins( $template_name );
					break;
				case 'custom_tables':
					$this->import_custom_tables( $template_name );
					break;
				case 'theme_content':
					$this->importThemeContent( $template_name, $import_media, $partial_import );
					break;
				case 'setup_pages':
					$this->setUpPages( $template_name );
					break;
				case 'plugins_content':
					$this->import_plugins_content( $template_name );
					break;
				case 'settings':
					$this->import_settings( $template_name );
					break;
				case 'menu_locations':
					$this->importMenuLocations( $template_name );
					break;
				case 'theme_widget':
					$this->importThemeWidgets( $template_name );
					break;
				case 'finalize':
					$this->finalizeImporting( $template_name, $partial_import );
					break;
			}
		}
		public function reinitializeData( $template_name ) {
			try {
				if ( empty( $template_name ) ) {
					throw new Exception( 'Choose template first' );
				}
				$this->setTemplateName( $template_name );
				if (
					file_exists( $this->getAssetsAddress( 'template_content_path', $this->getTemplateName() ) ) == false ||
					file_exists( $this->getAssetsAddress( 'widget_path', $this->getTemplateName() ) ) == false ||
					file_exists( $this->getAssetsAddress( 'settings_path', $this->getTemplateName() ) ) == false
				) {
					throw new Exception( "Some template assets are missing Template Name : $template_name, Contact support." );
				} else {
					return true;
				}
			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );
				return false;
			}
		}

		/**
		 * Reinitilize Template file is exist or not for SEE request.
		 *
		 * @since 1.0
		 *
		 * @throws Exception If template name empty.
		 * @throws Exception If template file is not exist.
		 *
		 * @param  string $template_name The template name will be imported.
		 * @param  string $template_id   The template ID will be imported.
		 * @return boolean               File status.
		 */
		public function reinitialize_data_sse( $template_name, $template_id ) {
			try {

				// Check template name and ID.
				if ( empty( $template_name ) || empty( $template_id ) ) {
					throw new Exception( 'Choose template first!' );
				}

				$this->setTemplateName( $template_name );
				$this->setTemplateID( $template_id );

				// Check template file exist or not.
				if ( false === file_exists( $this->getAssetsAddress( 'template_content_path', $this->getTemplateName() ) ) ) {
					throw new Exception( 'Template content does not exist - Contact support.' );
				}

				return true;
			} catch ( Exception $e ) {
				$this->message_sse( $e->getMessage(), true );
				exit;
			}
		}

		/**
		 * Method that is resposible to pass plugin list to UI base on lazy load condition.
		 *
		 * @param str $_POST[from]  from number.
		 * @param str $_POST[count] how many.
		 *
		 * @return bool will return boolean status of action , all message is setted to $this->message()
		 */
		public function loadTemplatesFromApi() {
			check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'You do not have access to this section.', 'jupiterx-core' );
			}

			try {
				$from              = ( isset( $_POST['from'] ) ? intval( $_POST['from'] ) : null );
				$count             = ( isset( $_POST['count'] ) ? intval( $_POST['count'] ) : null );
				$template_id       = ( isset( $_POST['template_id'] ) ? intval( $_POST['template_id'] ) : 0 );
				$template_name     = ( isset( $_POST['template_name'] ) ? sanitize_text_field( $_POST['template_name'] ) : null );
				$template_category = ( isset( $_POST['template_category'] ) ? sanitize_text_field( $_POST['template_category'] ) : null );

				if ( is_null( $from ) || is_null( $count ) ) {
					throw new Exception( 'System problem , please contact support', 1001 );
					return false;
				}
				$getTemplateListArgs = [
					'pagination_start'  => $from,
					'pagination_count'  => $count,
					'template_category' => $template_category,
					'template_name'     => $template_name,
					'template_id'       => $template_id,
				];
				$list_of_templates = $this->getTemplateListFromApi( $getTemplateListArgs );

				if ( ! is_array( $list_of_templates ) ) {
					throw new Exception( 'Template list is not what we expected' );
				}

				if ( jupiterx_is_pro() ) {
					foreach ( $list_of_templates as $index => $template ) {
						$list_of_templates[ $index ]->free_template = '1';
					}
				}

				$db_manager = new JupiterX_Core_Control_Panel_Database_Manager();
				$backups    = $db_manager->is_restore_db();
				$this->message(
					'Successfull', true, array(
						'templates' => $list_of_templates,
						'backups'   => $backups,
					)
				);
				return true;

			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );
				return false;
			}
		}
		public function preparation( $template_name ) {
			try {
				$this->message( 'All is ready.', true );
				return true;
			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );
				return false;
			}
		}
		public function backupDB() {
			try {
				$db_manager  = new JupiterX_Core_Control_Panel_Database_Manager();
				$dm_response = $db_manager->backup_db();
				if ( false == $dm_response ) {
					throw new Exception( $db_manager->get_error_message() );
				}

				$this->message( 'Backup created.', true );
				return true;

			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );
				return false;
			}
		}
		public function backup_media_records() {
			try {
				$db_manager = new JupiterX_Core_Control_Panel_Database_Manager();

				$dm_response = $db_manager->backup_media_records();

				if ( false == $dm_response ) {
					throw new Exception( $db_manager->get_error_message() );
				}
				$this->message( 'Media records backup created.', true );
				return true;

			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );
				return false;
			}
		}
		public function restore_media_records() {
			try {
				$db_manager = new JupiterX_Core_Control_Panel_Database_Manager();

				$dm_response = $db_manager->restore_media_records();

				if ( false == $dm_response ) {
					throw new Exception( $db_manager->get_error_message() );
				}
				$this->message( 'Media records restored successfully', true );
				return true;

			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );
				return false;
			}
		}
		public function isRestoreDB() {
			check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'You do not have access to this section.', 'jupiterx-core' );
			}

			try {
				$db_manager = new JupiterX_Core_Control_Panel_Database_Manager();
				$result     = $db_manager->is_restore_db();
				if ( is_array( $result ) ) {
					$this->message( 'Successfull', true, $result );
					return true;
				} else {
					throw new Exception( 'Result is not what we expected' );
				}
			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );
				return false;
			}
		}
		public function restoreLatestDB() {
			check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'You do not have access to this section.', 'jupiterx-core' );
			}

			try {
				$db_manager = new JupiterX_Core_Control_Panel_Database_Manager();
				$return     = $db_manager->restore_latest_db();
				if ( false == $return ) {
					throw new Exception( $db_manager->get_error_message() );
				}
				JupiterX_Core_Control_Panel_Helpers::prevent_cache_plugins();
				$this->message( 'Restore completed!', true );
				return true;
			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );
				return false;
			}
		}
		public function resetDB() {
			try {
				$tables = array(
					'comments',
					'commentmeta',
					'links',
					'postmeta',
					'posts',
					'term_relationships',
					'termmeta',
					'terms',
					'term_taxonomy',
				);

				if ( class_exists( 'WooCommerce' ) ) {
					$tables = array_merge( $tables, [ 'woocommerce_attribute_taxonomies' ] );
				}

				include_once ABSPATH . 'wp-admin/includes/plugin.php';
				if ( jupiterx_is_callable( 'SitePress' ) ) {
					$tables[] = 'icl_translations';
				}

				$this->resetWordpressDatabase( $tables, array(), false );
				$this->message( 'Database reset completed', true );

				return true;
			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );

				return false;
			}
		}
		public function uploadTemplateToServer( $template_name ) {
			try {
				$this->setTemplateName( $template_name );
				$getTemplateName = $this->getTemplateName();
				if ( empty( $getTemplateName ) ) {
					throw new Exception( 'Choose one template first' );
				}
				$url                = $this->getTemplateDownloadLink( $this->getTemplateName(), 'download' );
				$template_file_name = $this->getTemplateDownloadLink( $this->getTemplateName(), 'filename' );
				$this->setTemplateRemoteAddress( $url );
				if ( filter_var( $url, FILTER_VALIDATE_URL ) === false ) {
					throw new Exception( 'Template source URL is not validate' );
				}
				JupiterX_Core_Control_Panel_Helpers::upload_from_url( $this->getTemplateRemoteAddress(), $template_file_name, $this->getBasePath() );
				$this->message( 'Uploaded to server', true );
				return true;
			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );
				return false;
			}
		}
		public function unzipTemplateInServer( $template_name ) {
			try {
				$this->setTemplateName( $template_name );
				$getTemplateName = $this->getTemplateName();
				if ( empty( $getTemplateName ) ) {
					throw new Exception( 'Choose one template first' );
				}

				$response = $this->getTemplateDownloadLink( $this->getTemplateName(), 'filename' );

				$this->setTemplateFileName( $response );

				$jupiterx_filesystem = new JupiterX_Core_Control_Panel_Filesystem(
					array(
						'context' => $this->getBasePath(),
					)
				);

				if ( $jupiterx_filesystem->get_error_code() ) {
					throw new Exception( $jupiterx_filesystem->get_error_message() );
					return false;
				}

				if ( ! $jupiterx_filesystem->exists( $this->getBasePath() . $this->getTemplateName() ) ) {
					JupiterX_Core_Control_Panel_Helpers::un_zip( $this->getBasePath() . $this->getTemplateFileName(), $this->getBasePath() );
				} else {
					if ( $jupiterx_filesystem->rmdir( $this->getBasePath() . $this->getTemplateName(), true ) ) {
						JupiterX_Core_Control_Panel_Helpers::un_zip( $this->getBasePath() . $this->getTemplateFileName(), $this->getBasePath() );
					}
				}

				$jupiterx_filesystem->delete( $this->getBasePath() . $this->getTemplateFileName() );

				$this->message( 'Completed', true );

				return true;
			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );

				return false;
			}
		}
		public function validateTemplateFiles( $template_name ) {
			try {
				if ( empty( $template_name ) ) {
					throw new Exception( 'Choose template first' );
				}

				$jupiterx_filesystem = new JupiterX_Core_Control_Panel_Filesystem(
					array(
						'context' => $this->getBasePath(),
					)
				);

				if ( $jupiterx_filesystem->get_error_code() ) {
					throw new Exception( $jupiterx_filesystem->get_error_message() );
					return false;
				}

				$this->setTemplateName( $template_name );
				if (
					$jupiterx_filesystem->exists( $this->getAssetsAddress( 'template_content_path', $this->getTemplateName() ) ) == false ||
					$jupiterx_filesystem->exists( $this->getAssetsAddress( 'widget_path', $this->getTemplateName() ) ) == false ||
					$jupiterx_filesystem->exists( $this->getAssetsAddress( 'settings_path', $this->getTemplateName() ) ) == false
				) {
					throw new Exception( "Some template assets are missing Template Name : $template_name, Contact support." );
				} else {
					$this->message( 'Completed', true );
					return true;
				}
			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );

				return false;
			}
		}

		public function installRequiredPlugins( $template_name ) {

			$plugin_install_access = is_multisite() ? is_super_admin() : ( current_user_can( 'install_themes' ) && current_user_can( 'activate_plugins' ) );
			$single_site_message   = 'You are not allowed to install a new plugin or template because your user role does not have required permissions.';
			$multi_site_message    = 'Template installation is only allowed for user with Super Admin role. Please contact your website\'s administrator. <a target="_blank" href="https://themes.artbees.net/docs/installing-a-template/">Learn More</a>';

			if ( ! $plugin_install_access ) {
				$message = $single_site_message;
				if ( is_multisite() ) {
					$message = $multi_site_message;
				}
				$this->message( $message, false );
			}

			$actions            = [];
			$plugins_to_install = [];
			$tgmpa_url          = $this->tgmpa->get_tgmpa_url();
			$template_plugins   = $this->get_template_used_plugin_list( $template_name );

			$template_plugins = array_diff( $template_plugins, ['jupiterx-pro', 'advanced-custom-fields-pro'] );

			$template_plugins[] = 'advanced-custom-fields';

			foreach ( $template_plugins as $slug ) {

				if ( ! $this->tgmpa->is_plugin_active( $slug ) || false !== $this->tgmpa->does_plugin_have_update( $slug ) ) {
					if ( ! $this->tgmpa->is_plugin_installed( $slug ) ) {
						$plugins_to_install[] = $slug;
					}
				}
			}

			$plugins_to_install  = apply_filters( 'jupiterx_cp_template_install_required_plugins', $plugins_to_install );

			if ( ! empty( $plugins_to_install ) ) {
				$actions['install'] = [
					'url'           => $tgmpa_url,
					'plugin'        => $plugins_to_install,
					'tgmpa-page'    => $this->tgmpa->menu,
					'plugin_status' => 'all',
					'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
					'action'        => 'tgmpa-bulk-install',
					'action2'       => - 1,
				];
			}

			$actions['message'] = esc_html__( 'Completed', 'jupiterx-core' );
			$actions['url'] = $tgmpa_url;
			$actions['status'] = true;

			wp_send_json( $actions );
		}

		public function activateRequiredPlugins( $template_name ) {

			$template_plugins = $this->get_template_used_plugin_list( $template_name );

			$template_plugins = array_diff( $template_plugins, [ 'jupiterx-pro', 'advanced-custom-fields-pro' ] );
			$template_plugins[] = 'advanced-custom-fields';

			$template_plugins  = apply_filters( 'jupiterx_cp_template_activate_required_plugins', $template_plugins );

			foreach ( $template_plugins as $slug ) {
				if ( isset( $this->tgmpa->plugins[ $slug ] ) ) {
					activate_plugin( $this->tgmpa->plugins[ $slug ]['file_path'] );

					if ( 'revslider' === $this->tgmpa->plugins[ $slug ]['slug'] ) {
						delete_transient( '_revslider_welcome_screen_activation_redirect' );
					}
				}
			}

			wp_send_json( [
				'status'  => true,
				'message' => esc_html__( 'Plugins are activated,', 'jupiterx-core' ),
			] );
		}


		/**
		 * Import plugins content.
		 *
		 * @since 1.0.3
		 */
		public function import_plugins_content( $template_name ) {

			try {
				$this->setTemplateName( $template_name );
				// Get template settings.
				$settings = $this->getSettingsData( $this->getTemplateName() );

				// Supported plugins list.
				$supported_plugins = $settings['options']['jupiterx_support_plugins'];

				// Run plugins importer.
				foreach ( $supported_plugins as $plugin ) {
					if ( is_callable( [ $this, "import_{$plugin}_content" ] ) ) {
						call_user_func( [ $this, "import_{$plugin}_content" ] );
					}
				}

				$this->message( esc_html__( 'Data of plugins have imported.', 'jupiterx-core' ), true );

				return true;
			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );
				return false;
			}

		}

		/**
		 * Import Revolution Slider content.
		 *
		 * @since 1.0.3
		 */
		public function import_revslider_content() {
			if ( ! class_exists( 'RevSliderSlider' ) ) {
				return;
			}

			$filesystem = new JupiterX_Core_Control_Panel_Filesystem( [
				'context' => $this->getBasePath(),
			] );

			$revslider_folder = $this->getBasePath() . sanitize_title( $this->getTemplateName() ) . '/revslider';

			// Check extracted template if `revslider` folder exists inside.
			if ( ! $filesystem->exists( $revslider_folder ) ) {
				return;
			}

			$revslider = new RevSlider();

			$sliders = glob( $revslider_folder . '/*.zip' );

			if ( empty( $sliders ) ) {
				return;
			}

			global $wpdb;

			$tables = [
				'revslider_css',
				'revslider_layer_animations',
				'revslider_navigations',
				'revslider_sliders',
				'revslider_slides',
				'revslider_static_slides',
			];

			// Truncate tables.
			foreach	( $tables as $table ) {
				$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}{$table}" );
			}

			// Import sliders.
			foreach ( $sliders as $slide ) {
				/**
				 * Start import slider.
				 *
				 * @param boolean Update animation.
				 * @param boolean Deprecated static param.
				 * @param mixed   Slider file path.
				 * @param boolean Template slide.
				 * @param boolean Single slide.
				 * @param boolean Update navigation.
				 */
				$revslider->importSliderFromPost( true, true, $slide, false, false, true );
			}
		}

		/**
		 * Import theme content via Server-Sent Events request.
		 *
		 *
		 * @throws Exception If template data is empty.
		 * @throws Exception If preliminary data is empty.
		 */
		public function import_theme_content_sse() {
			check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'You do not have access to this section.', 'jupiterx-core' );
			}

			try {
				/*
				* Filter data input from GET method. Eventsource doesn't allow us to use
				* POST method.
				*/
				$template_name = '';
				if ( ! empty( $_GET['template_name'] ) ) {
					// WPCS: XSS ok, CSRF ok.
					$template_name = sanitize_text_field( $_GET['template_name'] );
				}

				$template_id = '';
				if ( ! empty( $_GET['template_id'] ) ) {
					// WPCS: XSS ok, CSRF ok.
					$template_id = sanitize_text_field( $_GET['template_id'] );
				}

				$fetch_attachments = 'false';
				if ( ! empty( $_GET['fetch_attachments'] ) ) {
					// WPCS: XSS ok, CSRF ok.
					$fetch_attachments = sanitize_text_field( $_GET['fetch_attachments'] );
				} elseif ( ! empty( $_GET['import_media'] ) ) {
					$fetch_attachments = sanitize_text_field( $_GET['import_media'] );
				}

				$partial_import = false;
				if ( ! empty( $_GET['partial_import'] ) ) {
					// phpcs:ignore
					$partial_import = filter_var( $_GET['partial_import'], FILTER_VALIDATE_BOOLEAN );
				}

				// Include wordpress-importer class.
				JupiterX_Core_Control_Panel_Helpers::include_wordpress_importer();
				$this->reinitialize_data_sse( $template_name, $template_id );

				// Set importer options as an array.
				$options = array(
					'fetch_attachments' => filter_var( $fetch_attachments, FILTER_VALIDATE_BOOLEAN ),
					'default_author'    => get_current_user_id(),
					'demo'              => filter_input( INPUT_GET, 'template_demo', FILTER_SANITIZE_URL ),
				);

				// Create new instance for Importer.
				$importer = new JupiterX_Core_Control_Panel_Importer( $options, $partial_import );
				$logger   = new JupiterX_Core_Control_Panel_Importer_Logger_ServerSentEvents();
				$importer->set_logger( $logger );

				// Get preliminary information.
				$file     = $this->getAssetsAddress( 'template_content_path', $this->getTemplateName() );
				$pre_data = $importer->get_preliminary_information( $file );
				if ( is_wp_error( $pre_data ) ) {
					throw new Exception( $pre_data->get_error_message() );
				}

				// @codingStandardsIgnoreStart
				// Turn off PHP output compression, allow us to print the log.
				$previous = error_reporting(error_reporting() ^ E_WARNING);

				// Configuration disabled for theme check plugin.
				// ini_set('output_buffering', 'off');
				ini_set('zlib.output_compression', 0);

				error_reporting($previous);
				// @codingStandardsIgnoreEnd

				if ( $GLOBALS['is_nginx'] ) {
					// Setting this header instructs Nginx to disable fastcgi_buffering
					// and disable gzip for this request.
					header( 'X-Accel-Buffering: no' );
					header( 'Content-Encoding: none' );
				}

				// Start the event stream here to record all the logs.
				header( 'Content-Type: text/event-stream' );
				header( 'Cache-Control: no-cache' );

				// Time to run the import!
				set_time_limit( 0 );

				$zlib_oc = ini_get( 'zlib.output_compression' );

				if ( 1 !== intval( $zlib_oc ) ) {
					// Ensure we're not buffered.
					wp_ob_end_flush_all();
					flush();
				}

				// Run import process.
				$process = $importer->import( $file );

				// Setup complete response.
				$complete = array(
					'status'  => true, // The process is complete no matter success or not.
					'error'   => false, // Message error if any.
					'data'    => null, // Compatibility with current Ajax.
					'message' => 'Template contents were imported.',
				);

				// Check if the request is error, then set the message.
				if ( is_wp_error( $process ) ) {
					$complete['error'] = $process->get_error_message();
				}

				$this->message_sse( $complete );
				exit;

			} catch ( Exception $e ) {
				$this->message_sse( $e->getMessage(), true );
				exit;
			}
		}

		/**
		 * Get settings.json data.
		 *
		 */
		public function getSettingsData( $template_name ) {

			$this->setTemplateName( $template_name );
			$settings_url  = $this->getAssetsAddress( 'settings_url', $this->getTemplateName() );
			$settings_path = $this->getAssetsAddress( 'settings_path', $this->getTemplateName() );
			$response  = JupiterX_Core_Control_Panel_Helpers::getFileBody( $settings_url, $settings_path );

			return json_decode( $response, true );
		}

		/**
		 * Send a Server-Sent Events message.
		 *
		 *
		 * @param mixed   $message     Data to be JSON-encoded and sent in the message.
		 * @param boolean $need_header Send response along with the header.
		 */
		public function message_sse( $message, $need_header = false ) {
			// Add header to start event stream only if needed.
			if ( $need_header ) {
				// Start the event stream.
				header( 'Content-Type: text/event-stream' );
				header( 'Cache-Control: no-cache' );
			}

			// Convert any message data as an array.
			if ( ! is_array( $message ) ) {
				$message = array(
					'message' => $message,
				);
			}

			// Set message event and pass the data.
			echo "event: message\n";
			echo 'data: ' . wp_json_encode( $message ) . "\n\n";

			flush();
		}

		public function importThemeContent( $template_name, $fetch_attachments = false, $partial_import = false ) {
			try {

				// Include wordpress-importer class.
				JupiterX_Core_Control_Panel_Helpers::include_wordpress_importer();
				$this->reinitializeData( $template_name );
				// Set importer options as an array.
				$options = array(
					'fetch_attachments' => filter_var( $fetch_attachments, FILTER_VALIDATE_BOOLEAN ),
					'default_author'    => get_current_user_id(),
				);

				// Create new instance for Importer.
				$importer = new JupiterX_Core_Control_Panel_WXR_Importer( $options, $partial_import );
				$logger   = new JupiterX_Core_Control_Panel_Importer_Logger_ServerSentEvents();
				$importer->set_logger( $logger );

				// Get preliminary information.
				$file = $this->getAssetsAddress( 'template_content_path', $this->getTemplateName() );
				$data = $importer->get_preliminary_information( $file );
				if ( is_wp_error( $data ) ) {
					$this->message( 'Error in parsing theme_content.xml!', false );
					return false;
				}

				// Time to run the import!
				set_time_limit( 0 );

				// Run import process.
				ob_start();
				$importer->import( $file );
				ob_end_clean();

				$this->message( 'Template contents were imported.', true );
				return true;

			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );
				return false;
			}
		}
		public function importMenuLocations( $template_name ) {
			try {
				$settings = $this->getSettingsData( $template_name );

				$nav_menus = wp_get_nav_menus();

				if ( ! isset( $settings['options']['jupiterx_menu_locations'] ) || empty( $settings['options']['jupiterx_menu_locations'] ) || empty( $nav_menus ) ) {
					$this->message( 'There were no menu locations to import.', true );
				}

				$menu_locations = $settings['options']['jupiterx_menu_locations'];

				$locations = [];

				foreach ( $nav_menus as $menu ) {
					if ( in_array( $menu->name, $menu_locations, true ) ) {
						$location_key = array_search( $menu->name, $menu_locations, true );
						$locations[ $location_key ] = $menu->term_id;
					}
				}

				set_theme_mod( 'nav_menu_locations', $locations );

				$this->message( 'Navigation locations is configured.', true, [ $locations ] );

				return true;
			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );

				return false;
			} // End try().
		}

		public function setUpPages( $template_name ) {
			try {
				$package_data = $this->getSettingsData( $template_name );

				// Set homepage.
				if(isset($package_data['options']['page_on_front'])) {
					$homepage_title = $package_data['options']['page_on_front'];
					if ( ! empty( $homepage_title ) ) {
						$query_args = [
							'post_type'      => 'page',
							's'              => $homepage_title,
							'posts_per_page' => 1,
						];

						$query    = new WP_Query( $query_args );
						$homepage = (object) $query->get_posts()[0];
					}

					if ( ! empty( $homepage->ID ) ) {
						update_option( 'page_on_front', $homepage->ID );
						update_option( 'show_on_front', 'page' );
					}
				}

				// Set shop page.
				if(isset($package_data['options']['woocommerce_shop_page_id'])) {
					$shop_title = $package_data['options']['woocommerce_shop_page_id'];
					if ( ! empty( $shop_title ) ) {
						$query_args = [
							'post_type'      => 'page',
							's'              => $shop_title,
							'posts_per_page' => 1,
						];

						$query     = new WP_Query( $query_args );
						$shop_page = (object) $query->get_posts()[0];
					}

					if ( ! empty( $shop_page->ID ) ) {
						update_option( 'woocommerce_shop_page_id', $shop_page->ID );
					}
				}

				// Set cart page.
				if(isset($package_data['options']['woocommerce_cart_page_id'])) {
					$cart_title = $package_data['options']['woocommerce_cart_page_id'];
					if ( ! empty( $cart_title ) ) {
						$query_args = [
							'post_type'      => 'page',
							's'              => $cart_title,
							'posts_per_page' => 1,
						];

						$query     = new WP_Query( $query_args );
						$cart_page = (object) $query->get_posts()[0];
					}

					if ( ! empty( $cart_page->ID ) ) {
						update_option( 'woocommerce_cart_page_id', $cart_page->ID );
					}
				}

				// Set Checkout page.
				if(isset($package_data['options']['woocommerce_checkout_page_id'])) {
					$checkout_title = $package_data['options']['woocommerce_checkout_page_id'];
					if ( ! empty( $checkout_title ) ) {
						$query_args = [
							'post_type'      => 'page',
							's'              => $checkout_title,
							'posts_per_page' => 1,
						];

						$query         = new WP_Query( $query_args );
						$checkout_page = (object) $query->get_posts()[0];
					}

					if ( ! empty( $checkout_page->ID ) ) {
						update_option( 'woocommerce_checkout_page_id', $checkout_page->ID );
					}
				}

				// Set My Account page.
				if ( isset( $package_data['options']['woocommerce_myaccount_page_id'] ) ) {
					$myaccount_title = $package_data['options']['woocommerce_myaccount_page_id'];

					if ( ! empty( $myaccount_title ) ) {
						$query_args = [
							'post_type'      => 'page',
							's'              => $myaccount_title,
							'posts_per_page' => 1,
						];

						$query          = new WP_Query( $query_args );
						$myaccount_page = (object) $query->get_posts()[0];
					}

					if ( ! empty( $myaccount_page->ID ) ) {
						update_option( 'woocommerce_myaccount_page_id', $myaccount_page->ID );
					}
				}

				$query_args = [
					'post_type'      => 'jet-woo-builder',
					's'              => 'Shop Single',
					'posts_per_page' => 1,
				];

				$query            = new WP_Query( $query_args );
				$shop_single_page = ! empty( $query->get_posts() ) ? (object) $query->get_posts()[0] : [];
				$shop_custom_page = isset( $package_data['options']['jet_custom_shop_page'] ) ? $package_data['options']['jet_custom_shop_page'] : null;

				if ( class_exists( 'Jet_Woo_Builder' ) ) {
					$jet_woo_options = get_option('jet_woo_builder', []);

					if ( ! empty( $shop_single_page ) ) {
						$jet_woo_options['custom_single_page'] = 'yes';
						$jet_woo_options['single_template']    = $shop_single_page->ID;
					}

					if ( ! empty( $shop_custom_page ) ) {
						$jet_woo_options['custom_shop_page'] = $shop_custom_page;
					}

					update_option( 'jet_woo_builder', $jet_woo_options );
 				}

				$this->message( 'Pages are configured.', true );

				return true;
			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );

				return false;
			} // End try().
		}
		/**
		 * Import Settings options.
		 *
		 * @param  string $template_name Name of template.
		 * @return mixed
		 * @throws Exception When Settings file is empty.
		 */
		public function import_settings( $template_name ) {
			try {
				$this->reinitializeData( $template_name );
				$data = $this->getSettingsData( $template_name );

				// Data checks.
				if ( 'array' != gettype( $data ) ) {
					throw new Exception(
						sprintf( esc_html__( 'Error importing settings! Please check that you uploaded (%s) a settings export file.', 'jupiterx-core' ), $file_name )
					);
				}
				if ( ! isset( $data['template'] ) || ! isset( $data['mods'] ) ) {
					throw new Exception(
						sprintf( esc_html__( 'Error importing settings! template Please check that you uploaded (%s) a settings export file.', 'jupiterx-core' ), $file_name )
					);
				}

				// Clear theme mods.
				remove_theme_mods();

				$data['mods'] = JupiterX_Core_Control_Panel_Export_Import::_import_images( $data['mods'] );

				// If wp_css is set then import it.
				if ( function_exists( 'wp_update_custom_css_post' ) && isset( $data['wp_css'] ) && '' !== $data['wp_css'] ) {
					wp_update_custom_css_post( $data['wp_css'] );
				}

				// Exclude nav menu locations in this process.
				if ( isset( $data['mods']['nav_menu_locations'] ) ) {
					unset( $data['mods']['nav_menu_locations'] );
				}

				// Loop through the mods.
				foreach ( $data['mods'] as $key => $val ) {
					set_theme_mod( $key, $val );
				}

				// Set Jet Engine options.
				if ( isset( $data['options']['jet_engine_modules'] ) ) {
					update_option( 'jet_engine_modules', $data['options']['jet_engine_modules'] );
				}

				// Set Jet Booking options.
				if ( isset( $data['options']['jet-abaf'] ) ) {
					update_option( 'jet-abaf', $data['options']['jet-abaf'] );
				}

				// Set Jet Menu options.
				if ( isset( $data['options']['jet_menu_options'] ) ) {
					update_option( 'jet_menu_options', $data['options']['jet_menu_options'] );
				}

				// Set Jet Filter options.
				if ( isset( $data['options']['jet-smart-filters-settings'] ) ) {
					update_option( 'jet-smart-filters-settings', $data['options']['jet-smart-filters-settings'] );
				}

				// Set Jet Popup options.
				if ( isset( $data['options']['jet_popup_conditions'] ) ) {
					update_option( 'jet_popup_conditions', $data['options']['jet_popup_conditions'] );
				}

				// Set Jet Blog options.
				if ( isset( $data['options']['jet_blog_settings'] ) ) {
					update_option( 'jet-blog-settings', $data['options']['jet_blog_settings'] );
				}

				// Set Jupiter X custom siderbars option.
				if ( isset( $data['options']['jupiterx_custom_sidebars'] ) ) {
					jupiterx_update_option( 'custom_sidebars', $data['options']['jupiterx_custom_sidebars'] );
				}

				// Set elementor default kit.
				if ( isset( $data['options']['elementor_active_kit'] ) ) {
					update_option( 'elementor_active_kit', $data['options']['elementor_active_kit'] );
				} else if ( class_exists( 'Elementor\Plugin' ) ) {
					\Elementor\Core\Kits\Manager::create_default_kit();
				}

				// Set extra options.
				if ( ! empty( $data['options']['extra'] ) ) {

					if (
						! isset( $data['options']['extra']['elementor_cpt_support'] ) ||
						! is_array( $data['options']['extra']['elementor_cpt_support'] )
					) {
						$data['options']['extra']['elementor_cpt_support'] = [];
					}

					array_push( $data['options']['extra']['elementor_cpt_support'], 'post', 'page' );

					foreach( $data['options']['extra'] as $key => $val ) {

						if ( 'elementor_global_image_lightbox' === $key && is_bool( $val ) ) {
							continue;
						}

						update_option( $key, $val );
					}
				}

				$this->message( 'Settings are imported.', true );
				return true;

			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );

				return false;
			}
		}
		public function importThemeWidgets( $template_name ) {
			$this->reinitializeData( $template_name );
			try {
				$data = JupiterX_Core_Control_Panel_Helpers::getFileBody(
					$this->getAssetsAddress( 'widget_url', $this->getTemplateName() ),
					$this->getAssetsAddress( 'widget_path', $this->getTemplateName() )
				);
				$data = json_decode( $data, true );
				$this->import_widget_data( $data );

				$this->message( 'Widgets are imported.', true );

				return true;
			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );

				return false;
			}
		}
		public function finalizeImporting( $template_name, $partial_import = false ) {
			$this->reinitializeData( $template_name );
			$template_name = sanitize_title( $template_name );
			// Check if it had something to import.
			try {

				if ( ! $this->cleanInstallFiles( $template_name ) ) {
					throw new Exception( 'Can not remove installation source files' );
					return false;
				}

				// Regenrate Woocommerce Table.
				if ( class_exists( 'WooCommerce' ) ) {
					if ( ! $partial_import ) {
						delete_transient( 'wc_count_comments' );
					}

					wc_update_product_lookup_tables();
					delete_transient( 'wc_attribute_taxonomies' );
				}

				if ( ! $partial_import ) {
					jupiterx_update_option( 'template_installed', $this->getTemplateName() );
					jupiterx_update_option( 'template_installed_id', $this->getTemplateID() );
				}

				jupiterx_update_option( 'jupiterx_selected_google_fonts', null );

				// Enable simplicity mode.
				update_option( 'jupiterx_disable_theme_default_settings', '0' );
				jupiterx_update_option( 'disable_theme_default_settings', '0' );

				wp_cache_flush();
				jupiterx_core_flush_cache();
				flush_rewrite_rules();

				$this->message( 'Data imported successfully', true );
				return true;

			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );

				return false;
			}
		}

		/**
		 * Set default value Raven nav menus recursively.
		 *
		 * @access public
		 * @since 1.4.0
		 *
		 * @param array $element Template element.
		 * @param array  $list Raven menu default list.
		 * @return void
		 */
		public function set_default_raven_menu_list( &$element, $list )
		{
			if (
				isset( $element['elType'] ) &&
				$element['elType'] === 'widget' &&
				isset( $element['widgetType'] ) &&
				$element['widgetType'] === 'raven-nav-menu' &&
				! isset( $element['settings']['list'] )
			) {
				$element['settings']['list'] = $list;
				return;
			}

			foreach( $element['elements'] as &$inner_element ) {
				$this->set_default_raven_menu_list( $inner_element, $list );
			}
		}

		/**
		 * Clean install files
		 *
		 * @param $template_name
		 * @author Artbees Team
		 * @return bool
		 */
		private function cleanInstallFiles( $template_name ) {
			$jupiterx_filesystem = new JupiterX_Core_Control_Panel_Filesystem(
				array(
					'context' => $this->getBasePath(),
				)
			);

			// Deleting Template Source Folder.
			$template_path = $this->getBasePath() . sanitize_title( $template_name );
			if ( $jupiterx_filesystem->exists( $template_path ) && $jupiterx_filesystem->is_dir( $template_path ) && ! $jupiterx_filesystem->delete( $template_path, true ) ) {
				return false;
			}

			// Deleting Template Source Zip file.
			$template_zip = $template_path . '.zip';
			if ( $jupiterx_filesystem->exists( $template_zip ) && $jupiterx_filesystem->is_file( $template_zip ) && ! $jupiterx_filesystem->delete( $template_zip ) ) {
				return false;
			}

			return true;
		}
		public function uninstallTemplate() {
			check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'You do not have access to this section.', 'jupiterx-core' );
			}

			try {
				$tables = array(
					'comments',
					'commentmeta',
					'links',
					'options',
					'postmeta',
					'posts',
					'term_relationships',
					'termmeta',
					'terms',
					'term_taxonomy',
				);

				if ( class_exists( 'WooCommerce' ) ) {
					$tables = array_merge( $tables, [ 'woocommerce_attribute_taxonomies' ] );
				}

				$db_manager = new JupiterX_Core_Control_Panel_Database_Manager();

				$db_manager->backup_db();

				$db_manager->backup_media_records();

				$reset = $this->resetWordpressDatabase( $tables, array(), true );

				$db_manager->restore_media_records();

				if ( ! $reset ) {
					throw new Exception( 'Failed to uninstall template. Please try again.' );
				}

				$this->message( 'Template uninstall success.', true );
				return true;
			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );

				return false;
			}
		}
		public function availableWidgets() {
			global $wp_registered_widget_controls;
			$widget_controls   = $wp_registered_widget_controls;
			$available_widgets = array();
			foreach ( $widget_controls as $widget ) {
				if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[ $widget['id_base'] ] ) ) {
					$available_widgets[ $widget['id_base'] ]['id_base'] = $widget['id_base'];
					$available_widgets[ $widget['id_base'] ]['name']    = $widget['name'];
				}
			}

			return apply_filters( 'available_widgets', $available_widgets );
		}

		/**
		 * Import widgets' data.
		 *
		 * @throws Exception If can not read widget data.
		 *
		 * @param  array $data Widgets' data.
		 * @return boolean
		 */
		public function import_widget_data( $data ) {
			global $wp_registered_sidebars;

			$available_widgets = $this->availableWidgets();
			$widget_instances  = array();
			foreach ( $available_widgets as $widget_data ) {
				$widget_instances[ $widget_data['id_base'] ] = get_option( 'widget_' . $widget_data['id_base'] );
			}
			if ( empty( $data ) || ! is_array( $data ) ) {
				throw new Exception( 'Widget data could not be read. Please try a different file.' );
			}
			$results = array();
			foreach ( $data as $sidebar_id => $widgets ) {
				if ( 'wp_inactive_widgets' == $sidebar_id ) {
					continue;
				}
				if ( isset( $wp_registered_sidebars[ $sidebar_id ] ) ) {
					$sidebar_available    = true;
					$use_sidebar_id       = $sidebar_id;
					$sidebar_message_type = 'success';
					$sidebar_message      = '';
				} else {
					$sidebar_available    = false;
					$use_sidebar_id       = 'wp_inactive_widgets';
					$sidebar_message_type = 'error';
					$sidebar_message      = 'Sidebar does not exist in theme (using Inactive)';
				}
				$results[ $sidebar_id ]['name']         = ! empty( $wp_registered_sidebars[ $sidebar_id ]['name'] ) ? $wp_registered_sidebars[ $sidebar_id ]['name'] : $sidebar_id;
				$results[ $sidebar_id ]['message_type'] = $sidebar_message_type;
				$results[ $sidebar_id ]['message']      = $sidebar_message;
				$results[ $sidebar_id ]['widgets']      = array();
				foreach ( $widgets as $widget_instance_id => $widget ) {
					$fail               = false;
					$id_base            = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
					$instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );
					if ( ! $fail && ! isset( $available_widgets[ $id_base ] ) ) {
						$fail                = true;
						$widget_message_type = 'error';
						$widget_message      = 'Site does not support widget';
					}
					$widget = apply_filters( 'jupiterx_widget_settings', $widget );
					if ( ! $fail && isset( $widget_instances[ $id_base ] ) ) {
						$sidebars_widgets        = get_option( 'sidebars_widgets' );
						$sidebar_widgets         = isset( $sidebars_widgets[ $use_sidebar_id ] ) ? $sidebars_widgets[ $use_sidebar_id ] : array();
						$single_widget_instances = ! empty( $widget_instances[ $id_base ] ) ? $widget_instances[ $id_base ] : array();
						foreach ( $single_widget_instances as $check_id => $check_widget ) {
							if ( in_array( "$id_base-$check_id", $sidebar_widgets ) && (array) $widget == $check_widget ) {
								$fail                = true;
								$widget_message_type = 'warning';
								$widget_message      = 'Widget already exists';
								break;
							}
						}
					}
					if ( ! $fail ) {
						$single_widget_instances = get_option( 'widget_' . $id_base );
						$single_widget_instances = ! empty( $single_widget_instances ) ? $single_widget_instances : array(
							'_multiwidget' => 1,
						);
						$single_widget_instances[] = (array) $widget;
						end( $single_widget_instances );
						$new_instance_id_number = key( $single_widget_instances );
						if ( '0' === strval( $new_instance_id_number ) ) {
							$new_instance_id_number                           = 1;
							$single_widget_instances[ $new_instance_id_number ] = $single_widget_instances[0];
							unset( $single_widget_instances[0] );
						}
						if ( isset( $single_widget_instances['_multiwidget'] ) ) {
							$multiwidget = $single_widget_instances['_multiwidget'];
							unset( $single_widget_instances['_multiwidget'] );
							$single_widget_instances['_multiwidget'] = $multiwidget;
						}
						update_option( 'widget_' . $id_base, $single_widget_instances );
						$sidebars_widgets                    = get_option( 'sidebars_widgets', [] );
						$new_instance_id                     = $id_base . '-' . $new_instance_id_number;
						$sidebars_widgets[ $use_sidebar_id ][] = $new_instance_id;
						update_option( 'sidebars_widgets', $sidebars_widgets );
						if ( $sidebar_available ) {
							$widget_message_type = 'success';
							$widget_message      = 'Imported';
						} else {
							$widget_message_type = 'warning';
							$widget_message      = 'Imported to Inactive';
						}
					}
					$results[ $sidebar_id ]['widgets'][ $widget_instance_id ]['name']         = isset( $available_widgets[ $id_base ]['name'] ) ? $available_widgets[ $id_base ]['name'] : $id_base;
					$results[ $sidebar_id ]['widgets'][ $widget_instance_id ]['title']        = ! empty( $widget->title ) ? $widget->title : '';
					$results[ $sidebar_id ]['widgets'][ $widget_instance_id ]['message_type'] = $widget_message_type;
					$results[ $sidebar_id ]['widgets'][ $widget_instance_id ]['message']      = $widget_message;
				} // End foreach().
			} // End foreach().

			return true;
		}
		/**
		 * It will empty all or custom database tables of WordPress and install WordPress again if needed.
		 *
		 * @param array $table          which table need to be empty ? example : array('user' , 'usermeta')
		 *                              table names should be without any prefix
		 * @param bool  $install_needed if WordPress need to be installed after reseting database
		 *                              it should be false or true
		 *
		 * @return bool return if everything looks good and throwing errors on problems
		 */
		public function resetWordpressDatabase( $tables = array(), $exclude_tables = array(), $install_needed = false ) {
			global $wpdb, $reactivate_wp_reset_additional, $current_user;

			if ( $install_needed ) {

				require_once ABSPATH . '/wp-admin/includes/upgrade.php';

				$new_options = array();

				$old_options = array(
					'active_plugins',
				);

				$blogname = get_option( 'blogname' );
				$blog_public = get_option( 'blog_public' );
				$site_url = site_url();
				$current_theme = wp_get_theme();

				foreach ( $old_options as $old_option_key ) {
					$new_options[ $old_option_key ] = get_option( $old_option_key );
				}

				$keep_options = [
					'api_key',
					'api_access_token',
					'envato_purchase_code_5177775',
					'setup_wizard_current_page',
					'setup_wizard_hide_notice',
				];

				$jupiterx_options = get_option( 'jupiterx', [] );

				$new_options['jupiterx'] = array_intersect_key( $jupiterx_options, array_flip( $keep_options ) );

				if ( 'admin' != $current_user->user_login ) {
					$user = get_user_by( 'login', 'admin' );
				}

				if ( empty( $user->user_level ) || $user->user_level < 10 ) {
					$user = $current_user;
					$session_tokens = get_user_meta( $user->ID, 'session_tokens', true );
				}

				// Check if we need all the tables or specific table.
				if ( is_array( $tables ) && count( $tables ) > 0 ) {
					array_walk(
						$tables, function ( &$value, $key ) use ( $wpdb ) {
							$value = $wpdb->prefix . $value;
						}
					);
				} else {
					$prefix = str_replace( '_', '\_', $wpdb->prefix );
					$tables = $wpdb->get_col( "SHOW TABLES LIKE '{$prefix}%'" );
				}

				// exclude table if its valued.
				if ( is_array( $exclude_tables ) && count( $exclude_tables ) > 0 ) {
					array_walk(
						$exclude_tables, function ( &$ex_value, $key ) use ( $wpdb ) {
							$ex_value = $wpdb->prefix . $ex_value;
						}
					);
					$tables = array_diff( $tables, $exclude_tables );
				}
				// Removing data from WordPress tables.
				foreach ( $tables as $table ) {
					$wpdb->query( "DROP TABLE $table" );
				}

				$this->remove_elementor_action();

				$result = wp_install( $blogname, $user->user_login, $user->user_email, $blog_public );
				switch_theme( $current_theme->get_stylesheet() );

				/* GoDaddy Patch => GD have a problem of cleaning siteurl option value after reseting database */
				if ( site_url() == '' ) {
					$wpdb->update(
						$wpdb->options, array(
							'option_value' => $site_url,
						),array(
							'option_name' => 'siteurl',
						)
					);
				}
				extract( $result, EXTR_SKIP );

				$query = $wpdb->prepare( "UPDATE $wpdb->users SET user_pass = %s, user_activation_key = '' WHERE ID = %d", $user->user_pass, $user_id );
				$wpdb->query( $query );

				$get_user_meta = function_exists( 'get_user_meta' ) ? 'get_user_meta' : 'get_usermeta';
				$update_user_meta = function_exists( 'update_user_meta' ) ? 'update_user_meta' : 'update_usermeta';

				if ( $get_user_meta($user_id, 'default_password_nag') ) {
					$update_user_meta($user_id, 'default_password_nag', false);
				}

				if ( $get_user_meta($user_id, $wpdb->prefix . 'default_password_nag') ) {
					$update_user_meta($user_id, $wpdb->prefix . 'default_password_nag', false);
				}

				wp_clear_auth_cookie();
				wp_set_current_user( $user_id, $user->user_login );
				if ( $session_tokens ) {
					delete_user_meta( $user->ID, 'session_tokens' );
					update_user_meta( $user->ID, 'session_tokens', $session_tokens );
				}

				wp_set_auth_cookie( $user_id, true );
				do_action( 'wp_login', $user->user_login, $user );

				if ( $new_options ) {
					foreach ( $new_options as $key => $value ) {
						update_option( $key, $value );
					}
				}
				return true;
			} else {

				$jupiterx_temp_installed = jupiterx_get_option( 'template_installed' );

				if ( $jupiterx_temp_installed ) {

					// Delete option data for page_on_front.
					if ( get_option( 'page_on_front' ) ) {
						delete_option( 'page_on_front' );
					}

					// Delete option data for show_on_front.
					if ( get_option( 'show_on_front' ) ) {
						delete_option( 'show_on_front' );
					}

					// Delete option data for woocommerce_shop_page_id.
					if ( get_option( 'woocommerce_shop_page_id' ) ) {
						delete_option( 'woocommerce_shop_page_id' );
					}

					// Delete widgets.
					$wpdb->query( "DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE '%widget%';" );

				}// End if().

				// truncate tables.
				foreach ( $tables as $table ) {
					$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}{$table}" );
				}

				return true;
			}// End if().
		}

		private function setResponseForApiTemplateList( $url, $configs ) {
			$headers = array(
				'theme-name'       => $this->getThemeName(),
				'pagination-start' => isset( $configs['pagination_start'] ) ? $configs['pagination_start'] : 0,
				'pagination-count' => isset( $configs['pagination_count'] ) ? $configs['pagination_count'] : 1,
			);

			if ( isset( $configs['template_id'] ) && is_null( $configs['template_id'] ) == false ) {
				$headers['template-id'] = $configs['template_id'];
			}

			if ( isset( $configs['template_name'] ) && is_null( $configs['template_name'] ) == false ) {
				$headers['template-name'] = $configs['template_name'];
			}

			if ( isset( $configs['template_category'] ) && is_null( $configs['template_category'] ) == false ) {
				$headers['template-category'] = $configs['template_category'];
			}

			return $this->wp_remote_get( $url, $headers );
		}
		/**
		 * This method is resposible to get template list from api and create download link if template need to extract from WordPress repo.
		 *
		 * @param str $template_name if template name is valued it will return array of information about the this template.
		 *                           but if template is valued as false it will return all templates information
		 *
		 * @return array will return array of templates
		 */
		public function getTemplateListFromApi( $configs ) {
			if ( ! is_array( $configs ) ) {
				$configs = array();
			}
			$url      = $this->getApiURL() . 'theme/templates';
			$response = $this->setResponseForApiTemplateList( $url, $configs );
			if ( false == isset( $response->bool ) || false == $response->bool ) {
				throw new Exception( $response->message );
			}
			return $response->data;
		}
		public function getTemplateDownloadLink( $template_name = '', $type = 'download' ) {
			$url      = $this->getApiURL() . 'theme/download-template';
			$response = $this->wp_remote_get( $url,	array(
				'template-name' => $template_name,
				'type'          => $type,
			) );

			if ( false == isset( $response->bool ) || false == $response->bool ) {
				throw new Exception( $response->message );
			}

			/**
			 * Filters the template download url.
			 *
			 * @param string $response->data Download url.
			 */
			return apply_filters( 'jupiterx_template_download_url', $response->data, $type );
		}

		/**
		 * Gets psd file download link.
		 *
		 */
		public function get_template_psd_link() {
			$template_name = sanitize_text_field( $_POST['template_name'] );
			try {
				$response = $this->getTemplateDownloadLink( $template_name . ' jupiterx', 'download-psd' );
				$this->message(
					'Successfull', true, array(
						'psd_link' => $response,
					)
				);
				return true;
			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );
				return false;
			} // End try().
		}

		/**
		 * Gets sketch file download link.
		 *
		 */
		public function get_template_sketch_link() {
			$template_name = sanitize_text_field( $_POST['template_name'] );
			try {
				$response = $this->getTemplateDownloadLink( $template_name . ' jupiterx', 'download-sketch' );
				$this->message(
					'Successfull', true, array(
						'sketch_link' => $response,
					)
				);
				return true;
			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );
				return false;
			} // End try().
		}

		/**
		 * This method is resposible to get templates categories list from api
		 *
		 * @param str $template_name if template name is valued it will return array of information about the this template.
		 * but if template is valued as false it will return all templates information.
		 *
		 * @return array will return array of plugins.
		 */
		public function getTemplateCategoryListFromApi() {
			try {
				$url      = $this->getApiURL() . 'theme/template-categories';
				$response = $this->wp_remote_get( $url );
				if ( false == isset( $response->bool ) || false == $response->bool ) {
					throw new Exception( $response->message );
				}
				$this->message( 'Successfull', true, $response->data );
				return true;
			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );
				return false;
			}
		}
		/**
		 * We need to make assets addresses dynamic and fully proccess.
		 * in one method for future development
		 * it will get the type of address and will return full address in string
		 * example :
		 * for (options_url) type , it will return something like this
		 * (http://localhost/jupiter/wp-content/uploads/jupiterx_templates/dia/options.txt).
		 *
		 * For (options_path) type , it will return something like this.
		 * (/usr/apache/www/wp-content/uploads/jupiterx_templates/dia/options.txt)
		 *
		 * @param str $which_one     Which address do you need.
		 * @param str $template_name such as.
		 */
		public function getAssetsAddress( $which_one, $template_name ) {
			$template_name = sanitize_title( $template_name );
			switch ( $which_one ) {
				case 'template_content_url':
					return $this->getBaseUrl() . $template_name . '/' . $this->getTemplateContentFileName();
					break;
				case 'template_content_path':
					return $this->getBasePath() . $template_name . '/' . $this->getTemplateContentFileName();
					break;
				case 'widget_url':
					return $this->getBaseUrl() . $template_name . '/' . $this->getWidgetFileName();
					break;
				case 'widget_path':
					return $this->getBasePath() . $template_name . '/' . $this->getWidgetFileName();
					break;
				case 'settings_url':
					return $this->getBaseUrl() . $template_name . '/' . $this->get_settings_file_name();
					break;
				case 'settings_path':
					return $this->getBasePath() . $template_name . '/' . $this->get_settings_file_name();
					break;
				default:
					throw new Exception( 'File name you are looking for is not introduced.' );

					return false;
					break;
			}
		}

		public function find_plugin_path( $plugin_slug ) {
			$plugins = get_plugins();
			foreach ( $plugins as $plugin_address => $plugin_data ) {

				// Extract slug from address
				if ( strlen( $plugin_address ) == basename( $plugin_address ) ) {
					$slug = strtolower( str_replace( '.php', '', $plugin_address ) );
				} else {
					$slug = strtolower( str_replace( '/' . basename( $plugin_address ), '', $plugin_address ) );
				}
				// Check if slug exists
				if ( strtolower( $plugin_slug ) == $slug ) {
					return $plugin_address;
				}
			}
			return false;
		}

		public function importLayerSliderContent( $content_path ) {
			global $wpdb;
			$ls_path = $this->find_plugin_path( $this->layer_slider_slug );

			if ( $ls_path == false ) {
				throw new Exception( 'LayerSlider is not installed , install it first' );
				return false;
			}

			if ( defined( LS_PLUGIN_VERSION ) ) {
				throw new Exception( 'LayerSlider is installed but not activated , activate it first' );
				return false;
			}
			// Empty layerslider table first.
			$table = $wpdb->prefix . 'layerslider';
			$wpdb->query( "TRUNCATE TABLE $table" );

			// Try to import configs.
			$ls_plugin_root_path = pathinfo( $plugin->get_plugins_dir() . $ls_path );
			include $ls_plugin_root_path['dirname'] . '/classes/class.ls.importutil.php';
			new LS_ImportUtil( $content_path );
			return true;
		}

		/**
		 * Remove elementor action from update_option_blogname action.
		 *
		 * In elementor/core/kits/manager.php, Elementor is registering an anonymous action
		 * to update kit when blogname is getting update. It causes wp_install() function to fail.
		 *
		 * @since 2.0.0
		 *
		 * @return void
		 */
		private function remove_elementor_action() {
			global $wp_filter;

			// Bail early if no action is registered.
			if ( empty( $wp_filter['update_option_blogname'] ) ) {
				return;
			}

			// Get all the callbacks.
			$callbacks = array_values( $wp_filter['update_option_blogname']->callbacks );

			foreach ( $callbacks as $callback_priorites ) {
				foreach ( $callback_priorites as $callback_key => $callback ) {
					// Bail early if callback is not a closure.
					if ( ! is_object( $callback['function'] ) ) {
						continue;
					}

					// It's required to use Reflection to be able to access closure info.
					$callback_closure = new ReflectionFunction( $callback['function'] );

					// Bail early if callback does not belong to elementor.
					if ( 'Elementor\Core\Kits\{closure}' !== $callback_closure->getName() ) {
						continue;
					}

					remove_action( 'update_option_blogname', $callback_key );
				}
			}
		}

		/**
		 * Deactive automatic compression
		 *
		 * @since 1.22.0
		 *
		 * @return void
		 */
		public function jupiterx_smush_check() {
			if ( ! class_exists( 'WP_Smush' ) ) {
				return;
			}

			$smush_settings = get_option( 'wp-smush-settings' );

			if ( false === $smush_settings['auto'] ) {
				return;
			}

			$smush_settings['auto'] = 'false';
			update_option( 'wp-smush-settings', $smush_settings );
		}

		/**
		 * Import templates's custom tables.
		 *
		 * @since 1.11.0
		 *
		 * @param string $template_name Template name.
		 */
		public function import_custom_tables( $template_name ) {
			$this->reinitializeData( $template_name );
			if (
				! empty( get_option( 'elementor_experiment-e_dom_optimization' ) ) &&
				'active' === get_option( 'elementor_experiment-e_dom_optimization' )
			)
			{
				update_option( 'elementor_experiment-e_dom_optimization','inactive' );
			}

			try {
				$template_name = sanitize_title( $template_name );
				$import_path   = $this->getBasePath() . $template_name;
				$file          = $import_path . '/tables.sql';
				$db_manager    = new JupiterX_Core_Control_Panel_PHP_DB_Manager();

				if ( file_exists( $file ) ) {
					$import_tables = $db_manager->import_tables( $file );

					if ( $import_tables !== true ) {
						throw new Exception( $import_tables );
					}
				}
				$this->jupiterx_smush_check();
				$this->message( 'Custom tables are imported.', true );
			} catch ( Exception $e ) {
				$this->message( $e->getMessage(), false );
			}
		}

		/**
		 * Reusable wrapper method for WP remote getter.
		 *
		 * Method only returns response body.
		 */
		public function wp_remote_get( $url = '', $headers = [] ) {
			$required_headers = [
				'api-key' => jupiterx_get_option( 'api_key' ),
				'domain'  => esc_url_raw( $_SERVER['SERVER_NAME'] ),
			];

			// Combined headers.
			$headers = array_merge( $headers, $required_headers );

			$result = wp_remote_get( $url, [
				'sslverify' => false,
				'headers'   => $headers,
			] );

			if ( is_wp_error( $result ) ) {
				$result->message = __( 'There is a problem in downloading the template.', 'jupiterx-core' );
				return $result;
			}

			$response = json_decode( wp_remote_retrieve_body( $result ) );

			return $response;
		}

		/**
		 * This method is resposible to manage all the classes messages.
		 */
		public function message( $message, $status, $data = null ) {
			$response = [
				'message' => jupiterx_logic_message_helper( 'template-management', $message ),
				'status'  => $status,
				'data'    => $data,
			];

			wp_send_json( $response );
		}

		/**
		 * Activate required jet modules.
		 *
		 * @since 1.18.0
		 *
		 * @return void
		 */
		private function activate_required_jet_modules() {
			$required_modules = [ 'booking-forms', 'listing-injections', 'calendar' ];
			$modules          = get_option( 'jet_engine_modules', [] );
			$modules          = array_merge( $modules, $required_modules );
			$modules          = array_unique( $modules );

			update_option( 'jet_engine_modules', $modules );
		}

		/**
		 * Get the list of required plugins for a template.
		 *
		 * Previously we only got this list from settings.json of the template zip file.
		 * But due to some problems with those files not including the updated list of plugins,
		 * we adopted this way to first try retrieving the list from API and if it doesn't exist,
		 * retort to getting them from settings.json. Maybe in further releases it will be limited
		 * to use only one of these two methods.
		 *
		 * @param string $template_name Title of the template (not slug)
		 * @return false|array List of template required plugins slugs.
		 *
		 * @since 2.5.0
		 */
		public function get_template_used_plugin_list( $template_name ) {
			$data = wp_remote_get( 'https://my.artbees.net/wp-json/templates/v1/list?posts_per_page=10000' );

			if ( is_wp_error( $data ) ) {
				return $this->getSettingsData( $template_name )['options']['jupiterx_support_plugins'];
			}

			$body = json_decode( wp_remote_retrieve_body( $data ), true );

			if ( ! $body ) {
				return $this->getSettingsData( $template_name )['options']['jupiterx_support_plugins'];
			}

			$index = array_search( $template_name, array_column( $body['posts'], 'title' ), true );

			if ( empty( $index ) && 0 !== $index ) {
				return $this->getSettingsData( $template_name )['options']['jupiterx_support_plugins'];
			}

			$plugin_titles = $body['posts'][ $index ]['list_of_used_plugins'];

			// Convert titles to slugs.
			return array_map( function( $title ) {
				$lower_cased = strtolower( $title );

				return str_replace( ' ', '-', $lower_cased );
			}, $plugin_titles );
		}
	}
}

if ( ! function_exists( 'jupiterx_disable_woocommerce' ) ) {
	/* Disable woocommerce redirection */
	add_action( 'admin_init', 'jupiterx_disable_woocommerce', 5 );
	/**
	 * Disable Woocommerce redirect for template install
	 *
	 */
	function jupiterx_disable_woocommerce() {
		delete_transient( '_wc_activation_redirect' );
		add_filter(
			'woocommerce_prevent_automatic_wizard_redirect', function () {
				return true;
			}
		);
	}
}

add_filter(
	'pre_transient__wc_activation_redirect', function () {
		return 0;
	}
);

add_filter(
	'pre_transient__vc_page_welcome_redirect', function () {
		return 0;
	}
);

global $abb_phpunit;
if ( empty( $abb_phpunit ) || $abb_phpunit == false ) {
	new JupiterX_Core_Control_Panel_Install_Template();
}
