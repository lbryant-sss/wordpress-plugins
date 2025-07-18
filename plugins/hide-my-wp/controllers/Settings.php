<?php
/**
 * Settings Class
 * Called when the plugin setting is loaded
 *
 * @file The Settings file
 * @package HMWP/Settings
 * @since 4.0.0
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class HMWP_Controllers_Settings extends HMWP_Classes_FrontController {

	/**
	 * List of events/actions
	 *
	 * @var $listTable HMWP_Models_ListTable
	 */
	public $listTable;

	/**
	 * Class constructor
	 * Initiates the class by calling the parent constructor, adding necessary filters and actions, checking options, and performing various setup tasks.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function __construct() {
		parent::__construct();

		//If save settings is required, show the alert
		if ( HMWP_Classes_Tools::getOption( 'changes' ) ) {
			add_action( 'admin_notices', array( $this, 'showSaveRequires' ) );
			HMWP_Classes_Tools::saveOptions( 'changes', false );
		}

		if ( ! HMWP_Classes_Tools::getOption( 'hmwp_valid' ) ) {
			add_action( 'admin_notices', array( $this, 'showPurchaseRequires' ) );
		}


		//Add the Settings class only for the plugin settings page
		add_filter( 'admin_body_class', array(
			HMWP_Classes_ObjController::getClass( 'HMWP_Models_Menu' ),
			'addSettingsClass'
		) );

		//If the option to prevent broken layout is on
		if ( HMWP_Classes_Tools::getOption( 'prevent_slow_loading' ) ) {

			//check the frontend on settings successfully saved
			add_action( 'hmwp_confirmed_settings', function () {
				//check the frontend and prevent from showing brake websites
				$url      = _HMWP_URL_ . '/view/assets/img/logo.svg?hmwp_preview=1&test=' . mt_rand( 11111, 99999 );
				$url      = HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rewrite' )->find_replace_url( $url );
				$response = HMWP_Classes_Tools::hmwp_localcall( $url, array( 'redirection' => 0, 'cookies' => false ) );

				//If the plugin logo is not loading correctly, switch off the path changes
				if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) == 404 ) {
					HMWP_Classes_Tools::saveOptions( 'file_mappings', array( home_url() ) );
				}
			} );

		}

		//save the login path on Cloud
		add_action( 'hmwp_apply_permalink_changes', function () {
			HMWP_Classes_Tools::sendLoginPathsApi();
		} );

	}

	/**
	 * Initialize the plugin and perform various setup tasks.
	 *
	 * This method:
	 * - Retrieves the current page and handles its corresponding tab function if available.
	 * - Ensures the 'is_plugin_active_for_network' function is available.
	 * - Configures NGINX specific settings and alerts based on the environment.
	 * - Sets alerts based on transient values for restore settings.
	 * - Displays config rules for validation.
	 * - Loads necessary media files for settings pages.
	 * - Checks for plugin activation and displays a connect prompt if necessary.
	 * - Displays error notifications if any configuration issues are detected.
	 * - Disables certain options for specific environments such as WPEngine.
	 * - Checks compatibility with other plugins and displays alerts.
	 * - Ensures necessary JavaScript is enabled in the browser.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function init() {
		/////////////////////////////////////////////////
		// Get the current Page
		$page = HMWP_Classes_Tools::getValue( 'page' );

		if ( strpos( $page, '_' ) !== false ) {
			$tab = substr( $page, ( strpos( $page, '_' ) + 1 ) );

			if ( method_exists( $this, $tab ) ) {
				call_user_func( array( $this, $tab ) );
			}
		}
		/////////////////////////////////////////////////

		// We need that function so make sure is loaded
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			include_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		if ( HMWP_Classes_Tools::isNginx() && HMWP_Classes_Tools::getOption( 'test_frontend' ) && HMWP_Classes_Tools::getOption( 'hmwp_mode' ) <> 'default' ) {
			$config_file = HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rules' )->getConfFile();
			if ( HMWP_Classes_Tools::isLocalFlywheel() ) {
				if ( strpos( $config_file, '/includes/' ) !== false ) {
					$config_file = substr( $config_file, strpos( $config_file, '/includes/' ) + 1 );
				}
				HMWP_Classes_Error::setNotification( sprintf( esc_html__( "Local & NGINX detected. In case you didn't add the code in the NGINX config already, please add the following line. %s", 'hide-my-wp' ), '<br /><br /><code><strong>include ' . $config_file . ';</strong></code> <br /><strong><br /><a href="' . esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/how-to-setup-hide-my-wp-on-local-flywheel/' ) . '" target="_blank">' . esc_html__( "Learn how to setup on Local & Nginx", 'hide-my-wp' ) . ' >></a></strong>' ), 'notice', false );
			} else {
				HMWP_Classes_Error::setNotification( sprintf( esc_html__( "NGINX detected. In case you didn't add the code in the NGINX config already, please add the following line. %s", 'hide-my-wp' ), '<br /><br /><code><strong>include ' . $config_file . ';</strong></code> <br /><strong><br /><a href="' . esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/how-to-setup-hide-my-wp-on-nginx-server/' ) . '" target="_blank">' . esc_html__( "Learn how to setup on Nginx server", 'hide-my-wp' ) . ' >></a></strong>' ), 'notice', false );
			}
		}

		// Setting Alerts based on Logout and Error statements
		if ( get_transient( 'hmwp_restore' ) == 1 ) {
			$restoreLink = '<a href="' . esc_url( add_query_arg( array( 'hmwp_nonce' => wp_create_nonce( 'hmwp_restore_settings' ), 'action'     => 'hmwp_restore_settings' ) ) ) . '" class="btn btn-default btn-sm ml-3" />' . esc_html__( "Restore Settings", 'hide-my-wp' ) . '</a>';
			HMWP_Classes_Error::setNotification( esc_html__( 'Do you want to restore the last saved settings?', 'hide-my-wp' ) . $restoreLink );
		}

		// Show the config rules to make sure they are okay
		if ( HMWP_Classes_Tools::getValue( 'hmwp_config' ) ) {
			// Initialize WordPress Filesystem
			$wp_filesystem = HMWP_Classes_ObjController::initFilesystem();

			$config_file = HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rules' )->getConfFile();
			if ( $config_file <> '' && $wp_filesystem->exists( $config_file ) ) {
				$rules = $wp_filesystem->get_contents( HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rules' )->getConfFile() );
				HMWP_Classes_Error::setNotification( '<pre>' . $rules . '</pre>' );
			}

			HMWP_Classes_Error::setNotification( '<pre>' . print_r( $_SERVER, true ) . '</pre>' );
		}

		// Load the css for Settings
		HMWP_Classes_ObjController::getClass( 'HMWP_Classes_DisplayController' )->loadMedia( 'popper' );

		if ( is_rtl() ) {
			HMWP_Classes_ObjController::getClass( 'HMWP_Classes_DisplayController' )->loadMedia( 'bootstrap.rtl' );
			HMWP_Classes_ObjController::getClass( 'HMWP_Classes_DisplayController' )->loadMedia( 'rtl' );
		} else {
			HMWP_Classes_ObjController::getClass( 'HMWP_Classes_DisplayController' )->loadMedia( 'bootstrap' );
		}

		HMWP_Classes_ObjController::getClass( 'HMWP_Classes_DisplayController' )->loadMedia( 'bootstrap-select' );
		HMWP_Classes_ObjController::getClass( 'HMWP_Classes_DisplayController' )->loadMedia( 'font-awesome' );
		HMWP_Classes_ObjController::getClass( 'HMWP_Classes_DisplayController' )->loadMedia( 'switchery' );
		HMWP_Classes_ObjController::getClass( 'HMWP_Classes_DisplayController' )->loadMedia( 'alert' );
		HMWP_Classes_ObjController::getClass( 'HMWP_Classes_DisplayController' )->loadMedia( 'clipboard' );
		HMWP_Classes_ObjController::getClass( 'HMWP_Classes_DisplayController' )->loadMedia( 'settings' );

		// Show connect for activation
		if ( ! HMWP_Classes_Tools::getOption( 'hmwp_token' ) ) {
			$this->show( 'Connect' );

			return;
		}

		if ( HMWP_Classes_Tools::getOption( 'error' ) ) {
			HMWP_Classes_Error::setNotification( esc_html__( 'There is a configuration error in the plugin. Please Save the settings again and follow the instruction.', 'hide-my-wp' ) );
		}

		if ( HMWP_Classes_Tools::isWpengine() ) {
			add_filter( 'hmwp_option_hmwp_mapping_url_show', "__return_false" );
		}

		// Check compatibilities with other plugins
		HMWP_Classes_ObjController::getClass( 'HMWP_Models_Compatibility' )->getAlerts();

		// Show errors on top
		HMWP_Classes_ObjController::getClass( 'HMWP_Classes_Error' )->hookNotices();

		echo '<meta name="viewport" content="width=640">';
		echo '<noscript><div class="alert-danger text-center py-3">' . sprintf( esc_html__( "Javascript is disabled on your browser! You need to activate the javascript in order to use %s plugin.", 'hide-my-wp' ), HMWP_Classes_Tools::getOption( 'hmwp_plugin_name' ) ) . '</div></noscript>';
		$this->show( ucfirst( str_replace( 'hmwp_', '', $page ) ) );
		$this->show( 'blocks/Upgrade' );

	}

	/**
	 * Logs relevant data for the application, including URLs of sites if multisite is enabled,
	 * and sets the log table data by fetching information from a remote API.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function log() {
		$this->listTable = HMWP_Classes_ObjController::getClass( 'HMWP_Models_ListTable' );

		if ( apply_filters( 'hmwp_showlogs', true ) ) {

			$args           = $urls = array();
			$args['search'] = HMWP_Classes_Tools::getValue( 's', false );
			//If it's multisite
			if ( is_multisite() ) {
				if ( function_exists( 'get_sites' ) && class_exists( 'WP_Site_Query' ) ) {
					$sites = get_sites();
					if ( ! empty( $sites ) ) {
						foreach ( $sites as $site ) {
							$urls[] = ( _HMWP_CHECK_SSL_ ? 'https://' : 'http://' ) . rtrim( $site->domain . $site->path, '/' );
						}
					}
				}
			} else {
				$urls[] = home_url();
			}
			//pack the urls
			$args['urls'] = json_encode( array_unique( $urls ) );

			//Set the log table data
			$logs = HMWP_Classes_Tools::hmwp_remote_get( _HMWP_API_SITE_ . '/api/log', $args );

			if ( $logs = json_decode( $logs, true ) ) {

				if ( isset( $logs['data'] ) && ! empty( $logs['data'] ) ) {
					$logs = $logs['data'];
				} else {
					$logs = array();
				}

			} else {
				$logs = array();
			}

			$this->listTable->setData( $logs );
		}

	}

	/**
	 * Handle temporary login when the advanced pack is not installed.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function templogin() {

		if ( ! HMWP_Classes_Tools::isAdvancedpackInstalled() ) {

			add_filter( 'hmwp_getview', function ( $output, $block ) {
				if ( $block == 'Templogin' ) {
					return '<div id="hmwp_wrap" class="d-flex flex-row p-0 my-3">
                        <div class="hmwp_row d-flex flex-row p-0 m-0">
                            <div class="hmwp_col flex-grow-1 px-3 py-3 mr-2 mb-3 bg-white">
                                ' . $this->getView( 'blocks/Install' ) . '
                            </div>
                        </div>
                    </div>';
				}

				return $output;

			}, PHP_INT_MAX, 2 );
		}

	}

	/**
	 * Handles two-factor authentication feature if the advanced pack is installed.
	 * Modifies the view output to prompt installation if advanced pack is not present.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function twofactor() {

		if ( ! HMWP_Classes_Tools::isAdvancedpackInstalled() ) {

			add_filter( 'hmwp_getview', function ( $output, $block ) {
				if ( $block == 'Twofactor' ) {
					return '<div id="hmwp_wrap" class="d-flex flex-row p-0 my-3">
                        <div class="hmwp_row d-flex flex-row p-0 m-0">
                            <div class="hmwp_col flex-grow-1 px-3 py-3 mr-2 mb-3 bg-white">
                                ' . $this->getView( 'blocks/Install' ) . '
                            </div>
                        </div>
                    </div>';
				}

				return $output;

			}, PHP_INT_MAX, 2 );
		}

	}

	/**
	 * Load media header
	 */
	public function hookHead() {
	}

	/**
	 * Show this message to notify the user when to update the settings
	 *
	 * @return void
	 * @throws Exception
	 */
	public function showSaveRequires() {
		if ( HMWP_Classes_Tools::getOption( 'hmwp_hide_plugins' ) || HMWP_Classes_Tools::getOption( 'hmwp_hide_themes' ) ) {
			global $pagenow;
			if ( $pagenow == 'plugins.php' ) {

				HMWP_Classes_ObjController::getClass( 'HMWP_Classes_DisplayController' )->loadMedia( 'alert' );

				?>
                <div class="notice notice-warning is-dismissible">
                    <div style="display: inline-block;">
                        <form action="<?php echo HMWP_Classes_Tools::getSettingsUrl( 'hmwp_permalinks' ) ?>" method="POST">
							<?php wp_nonce_field( 'hmwp_newpluginschange', 'hmwp_nonce' ) ?>
                            <input type="hidden" name="action" value="hmwp_newpluginschange"/>
                            <p>
								<?php echo sprintf( esc_html__( "New Plugin/Theme detected! Update %s settings to hide it. %sClick here%s", 'hide-my-wp' ), HMWP_Classes_Tools::getOption( 'hmwp_plugin_name' ), '<button type="submit" style="color: blue; text-decoration: underline; cursor: pointer; background: none; border: none;">', '</button>' ); ?>
                            </p>
                        </form>

                    </div>
                </div>
				<?php
			}
		}
	}

	/**
	 * Display a notification if the purchase requires renewal.
	 *
	 * @return void
	 */
	public function showPurchaseRequires() {
		global $pagenow;

		$expires = (int) HMWP_Classes_Tools::getOption( 'hmwp_expires' );

		if ( $expires > 0 ) {
			$error = sprintf( esc_html__( "Your %s %s license expired on %s %s. To keep your website security up to date please make sure you have a valid subscription on %saccount.hidemywpghost.com%s", 'hide-my-wp' ), '<strong>', HMWP_Classes_Tools::getOption( 'hmwp_plugin_name' ), date( 'd M Y', $expires ), '</strong>', '<a href="' . HMWP_Classes_Tools::getCloudUrl( 'orders' ) . '" style="line-height: 30px;" target="_blank">', '</a>' );

			if ( $pagenow == 'plugins.php' || $pagenow == 'index.php' ) {
				?>
                <div class="col-sm-12 mx-0 hmwp_notice error notice">
                    <div style="display: inline-block;"><p> <?php echo esc_html( $error ) ?> </p></div>
                </div>
				<?php
			} else {
				HMWP_Classes_Error::setNotification( $error );
			}
		}
	}

	/**
	 * Get the Admin Menu Tabs
	 *
	 * @param  string|null  $current  The currently selected tab, if any.
	 *
	 * @return string Returns the HTML content for the admin tabs.
	 * @throws Exception
	 */
	public function getAdminTabs( $current = null ) {

		//Add the Menu Sub Tabs in the selected page
		$subtabs = HMWP_Classes_ObjController::getClass( 'HMWP_Models_Menu' )->getSubMenu( $current );

		$content = '<div class="hmwp_nav d-flex flex-column bd-highlight mb-3">';
		$content .= '<div  class="m-0 px-3 pt-2 pb-3 font-dark font-weight-bold text-logo"><a href="' . esc_url( HMWP_Classes_Tools::getOption( 'hmwp_plugin_website' ) ) . '" target="_blank"><img src="' . esc_url( HMWP_Classes_Tools::getOption( 'hmwp_plugin_logo' ) ? HMWP_Classes_Tools::getOption( 'hmwp_plugin_logo' ) : _HMWP_ASSETS_URL_ . 'img/logo.svg' ) . '" class="ml-0 mr-2" style="height:35px; max-width: 140px;" alt=""></a></div>';

		foreach ( $subtabs as $tab ) {
			$content .= '<a href="#' . esc_attr( $tab['tab'] ) . '" class="m-0 px-3 py-3 font-dark hmwp_nav_item" data-tab="' . esc_attr( $tab['tab'] ) . '">' . wp_kses_post( $tab['title'] ) . '</a>';
		}

		$content .= '</div>';

		return $content;
	}

	/**
	 * Called when an action is triggered
	 *
	 * @throws Exception
	 */
	public function action() {
		parent::action();

		if ( ! HMWP_Classes_Tools::userCan( HMWP_CAPABILITY ) ) {
			return;
		}

		switch ( HMWP_Classes_Tools::getValue( 'action' ) ) {
			case 'hmwp_settings':

				//Save the settings
				if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {

					// Save the whitelist IPs
					$this->saveWhiteListIps();

					// Save the whitelist paths
					$this->saveWhiteListPaths();

					/**  @var $this ->model HMWP_Models_Settings */
					$this->model->savePermalinks( $_POST );
				}

				//load the after saving settings process
				if ( $this->model->applyPermalinksChanged() ) {
					HMWP_Classes_Error::setNotification( esc_html__( 'Saved' ), 'success' );

					// Add action hook for later use
					do_action( 'hmwp_settings_saved' );
				}

				break;
			case 'hmwp_tweakssettings':

				// Save the settings
				if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
					$this->model->saveValues( $_POST );
				}

				HMWP_Classes_Tools::saveOptions( 'hmwp_disable_click_message', HMWP_Classes_Tools::getValue( 'hmwp_disable_click_message', '', true ) );
				HMWP_Classes_Tools::saveOptions( 'hmwp_disable_inspect_message', HMWP_Classes_Tools::getValue( 'hmwp_disable_inspect_message', '', true ) );
				HMWP_Classes_Tools::saveOptions( 'hmwp_disable_source_message', HMWP_Classes_Tools::getValue( 'hmwp_disable_source_message', '', true ) );
				HMWP_Classes_Tools::saveOptions( 'hmwp_disable_copy_paste_message', HMWP_Classes_Tools::getValue( 'hmwp_disable_copy_paste_message', '', true ) );
				HMWP_Classes_Tools::saveOptions( 'hmwp_disable_drag_drop_message', HMWP_Classes_Tools::getValue( 'hmwp_disable_drag_drop_message', '', true ) );

				// Load the after saving settings process
				if ( $this->model->applyPermalinksChanged() ) {
					HMWP_Classes_Error::setNotification( esc_html__( 'Saved' ), 'success' );

					// Add action for later use
					do_action( 'hmwp_tweakssettings_saved' );
				}

				break;
			case 'hmwp_mappsettings':
				// Save Mapping for classes and ids
				HMWP_Classes_Tools::saveOptions( 'hmwp_mapping_classes', HMWP_Classes_Tools::getValue( 'hmwp_mapping_classes' ) );
				HMWP_Classes_Tools::saveOptions( 'hmwp_mapping_file', HMWP_Classes_Tools::getValue( 'hmwp_mapping_file' ) );
				HMWP_Classes_Tools::saveOptions( 'hmwp_file_cache', HMWP_Classes_Tools::getValue( 'hmwp_file_cache' ) );

				// Save the patterns as array
				// Save CDN URLs
				if ( $urls = HMWP_Classes_Tools::getValue( 'hmwp_cdn_urls' ) ) {
					$hmwp_cdn_urls = array();
					foreach ( $urls as $row ) {
						if ( $row <> '' ) {
							$row = preg_replace( '/[^A-Za-z0-9-_.:\/]/', '', $row );
							if ( $row <> '' ) {
								$hmwp_cdn_urls[] = $row;
							}
						}
					}
					HMWP_Classes_Tools::saveOptions( 'hmwp_cdn_urls', json_encode( $hmwp_cdn_urls ) );
				}

				// Save Text Mapping
				if ( $hmwp_text_mapping_from = HMWP_Classes_Tools::getValue( 'hmwp_text_mapping_from' ) ) {
					if ( $hmwp_text_mapping_to = HMWP_Classes_Tools::getValue( 'hmwp_text_mapping_to' ) ) {
						$this->model->saveTextMapping( $hmwp_text_mapping_from, $hmwp_text_mapping_to );
					}
				}

				// Save URL mapping
				if ( $hmwp_url_mapping_from = HMWP_Classes_Tools::getValue( 'hmwp_url_mapping_from' ) ) {
					if ( $hmwp_url_mapping_to = HMWP_Classes_Tools::getValue( 'hmwp_url_mapping_to' ) ) {
						$this->model->saveURLMapping( $hmwp_url_mapping_from, $hmwp_url_mapping_to );
					}
				}

				// Load the after saving settings process
				if ( $this->model->applyPermalinksChanged( true ) ) {
					HMWP_Classes_Error::setNotification( esc_html__( 'Saved' ), 'success' );

					// Add action for later use
					do_action( 'hmwp_mappsettings_saved' );

				}

				break;
			case 'hmwp_firewall':
				// Save the settings
				if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {

					// Save the whitelist IPs
					$this->saveWhiteListIps();

					// Blacklist ips,hostnames, user agents, referrers
					$this->saveBlackListIps();
					$this->saveBlackListHostnames();
					$this->saveBlackListUserAgents();
					$this->saveBlackListReferrers();

					// Save the whitelist paths
					$this->saveWhiteListPaths();

                    // Save the rest of the settings
					$this->model->saveValues( $_POST );

					// Save CDN URLs
					if ( $codes = HMWP_Classes_Tools::getValue( 'hmwp_geoblock_countries' ) ) {
						$countries = array();
						foreach ( $codes as $code ) {
							if ( $code <> '' ) {
								$code = preg_replace( '/[^A-Za-z]/', '', $code );
								if ( $code <> '' ) {
									$countries[] = $code;
								}
							}
						}

						HMWP_Classes_Tools::saveOptions( 'hmwp_geoblock_countries', json_encode( $countries ) );
					} else {
						HMWP_Classes_Tools::saveOptions( 'hmwp_geoblock_countries', array() );
					}

					// If no change is made on settings, just return
					if ( ! $this->model->checkOptionsChange() ) {
						return;
					}

					// Save the rules and add the rewrites
					$this->model->saveRules();

					// Load the after saving settings process
					if ( $this->model->applyPermalinksChanged() ) {
						HMWP_Classes_Error::setNotification( esc_html__( 'Saved' ), 'success' );

						//add action for later use
						do_action( 'hmwp_firewall_saved' );

					}
				}

				break;
			case 'hmwp_advsettings':

				if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
					$this->model->saveValues( $_POST );

					// Save the loading moment
					HMWP_Classes_Tools::saveOptions( 'hmwp_firstload', in_array( 'first', HMWP_Classes_Tools::getOption( 'hmwp_loading_hook' ) ) );
					HMWP_Classes_Tools::saveOptions( 'hmwp_priorityload', in_array( 'priority', HMWP_Classes_Tools::getOption( 'hmwp_loading_hook' ) ) );
					HMWP_Classes_Tools::saveOptions( 'hmwp_laterload', in_array( 'late', HMWP_Classes_Tools::getOption( 'hmwp_loading_hook' ) ) );

					// Send the notification email in case of Weekly report
					if ( HMWP_Classes_Tools::getValue( 'hmwp_send_email' ) && HMWP_Classes_Tools::getValue( 'hmwp_email_address' ) ) {
						$args = array( 'email' => HMWP_Classes_Tools::getValue( 'hmwp_email_address' ) );
						HMWP_Classes_Tools::hmwp_remote_post( _HMWP_ACCOUNT_SITE_ . '/api/log/settings', $args, array( 'timeout' => 5 ) );
					}

					if ( HMWP_Classes_Tools::getOption( 'hmwp_firstload' ) ) {
						// Add the must-use plugin to force loading before all others plugins
						HMWP_Classes_ObjController::getClass( 'HMWP_Models_Compatibility' )->addMUPlugin();
					} else {
						HMWP_Classes_ObjController::getClass( 'HMWP_Models_Compatibility' )->deleteMUPlugin();
					}

					// Load the after saving settings process
					if ( $this->model->applyPermalinksChanged() ) {
						HMWP_Classes_Error::setNotification( esc_html__( 'Saved' ), 'success' );

						// Add action hook for later use
						do_action( 'hmwp_advsettings_saved' );

					}

				}

				break;
			case 'hmwp_savecachepath':

				// Save the option to change the paths in the cache file
				HMWP_Classes_Tools::saveOptions( 'hmwp_change_in_cache', HMWP_Classes_Tools::getValue( 'hmwp_change_in_cache' ) );

				// Save the cache directory
				$directory = HMWP_Classes_Tools::getValue( 'hmwp_change_in_cache_directory' );

				if ( $directory <> '' ) {
					$directory = trim( $directory, '/' );

					// Remove sub dirs
					if ( strpos( $directory, '/' ) !== false ) {
						$directory = substr( $directory, 0, strpos( $directory, '/' ) );
					}

					if ( ! in_array( $directory, array(
						'languages',
						'mu-plugins',
						'plugins',
						'themes',
						'upgrade',
						'uploads'
					) ) ) {
						HMWP_Classes_Tools::saveOptions( 'hmwp_change_in_cache_directory', $directory );
					} else {
						wp_send_json_error( esc_html__( 'Path not allowed. Avoid paths like plugins and themes.', 'hide-my-wp' ) );
					}
				} else {
					HMWP_Classes_Tools::saveOptions( 'hmwp_change_in_cache_directory', '' );
				}

				if ( HMWP_Classes_Tools::isAjax() ) {
					wp_send_json_success( esc_html__( 'Saved', 'hide-my-wp' ) );
				}
				break;

			case 'hmwp_devsettings':

				// Set dev settings
				HMWP_Classes_Tools::saveOptions( 'hmwp_debug', HMWP_Classes_Tools::getValue( 'hmwp_debug' ) );

				break;
			case 'hmwp_devdownload':
				// Initialize WordPress Filesystem
				$wp_filesystem = HMWP_Classes_ObjController::initFilesystem();

				// Set header as text
				HMWP_Classes_Tools::setHeader( 'text' );
				$filename = preg_replace( '/[-.]/', '_', wp_parse_url( home_url(), PHP_URL_HOST ) );
				header( "Content-Disposition: attachment; filename=" . $filename . "_wghost_debug.txt" );

				if ( function_exists( 'glob' ) ) {
					$pattern = _HMWP_CACHE_DIR_ . '*.log';
					$files   = glob( $pattern, 0 );
					if ( ! empty( $files ) ) {
						foreach ( $files as $file ) {
							echo basename( $file ) . PHP_EOL;
							echo "---------------------------" . PHP_EOL;
							echo $wp_filesystem->get_contents( $file ) . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
						}
					}
				}

				exit();
			case 'hmwp_ignore_errors':
				// Empty WordPress rewrites count for 404 error.
				// This happens when the rules are not saved through config file
				HMWP_Classes_Tools::saveOptions( 'file_mappings', array() );

				break;
			case 'hmwp_abort':
			case 'hmwp_restore_settings':

				// Get keys that should not be replaced
				$tmp_options = array(
					'hmwp_token',
					'api_token',
					'hmwp_plugin_name',
					'hmwp_plugin_menu',
					'hmwp_plugin_logo',
					'hmwp_plugin_website',
					'hmwp_plugin_account_show',
				);

				$tmp_options = array_fill_keys( $tmp_options, true );
				foreach ( $tmp_options as $keys => &$value ) {
					$value = HMWP_Classes_Tools::getOption( $keys );
				}

				// Get the safe options from database
				HMWP_Classes_Tools::$options = HMWP_Classes_Tools::getOptions( true );

				// Set tmp data back to options
				foreach ( $tmp_options as $keys => $value ) {
					HMWP_Classes_Tools::$options[ $keys ] = $value;
				}
				HMWP_Classes_Tools::saveOptions();


				//set frontend, error & logout to false
				HMWP_Classes_Tools::saveOptions( 'test_frontend', false );
				HMWP_Classes_Tools::saveOptions( 'file_mappings', array() );
				HMWP_Classes_Tools::saveOptions( 'error', false );
				HMWP_Classes_Tools::saveOptions( 'logout', false );

				// Load the after saving settings process
				$this->model->applyPermalinksChanged( true );

				break;
			case 'hmwp_newpluginschange':
				// Reset the change notification
				HMWP_Classes_Tools::saveOptions( 'changes', 0 );
				remove_action( 'admin_notices', array( $this, 'showSaveRequires' ) );

				// Generate unique names for plugins if needed
				if ( HMWP_Classes_Tools::getOption( 'hmwp_hide_plugins' ) ) {
					HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rewrite' )->hidePluginNames();
				}
				if ( HMWP_Classes_Tools::getOption( 'hmwp_hide_themes' ) ) {
					HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rewrite' )->hideThemeNames();
				}

				// Load the after saving settings process
				if ( $this->model->applyPermalinksChanged() ) {
					HMWP_Classes_Error::setNotification( esc_html__( 'The list of plugins and themes was updated with success!' ), 'success' );
				}

				break;
			case 'hmwp_confirm':
				HMWP_Classes_Tools::saveOptions( 'error', false );
				HMWP_Classes_Tools::saveOptions( 'logout', false );
				HMWP_Classes_Tools::saveOptions( 'test_frontend', false );
				HMWP_Classes_Tools::saveOptions( 'file_mappings', array() );

				//Send email notification about the path changed
				HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rewrite' )->sendEmail();

				// Save to safe mode in case of db
				if ( ! HMWP_Classes_Tools::getOption( 'logout' ) ) {
					HMWP_Classes_Tools::saveOptionsBackup();
				}

				// Force the rechck security notification
				delete_option( HMWP_SECURITY_CHECK_TIME );

				HMWP_Classes_Tools::saveOptions( 'download_settings', true );

				// Add action hook for later use
				do_action( 'hmwp_confirmed_settings' );

				break;
			case 'hmwp_manualrewrite':
				HMWP_Classes_Tools::saveOptions( 'error', false );
				HMWP_Classes_Tools::saveOptions( 'logout', false );
				HMWP_Classes_Tools::saveOptions( 'test_frontend', true );
				HMWP_Classes_Tools::saveOptions( 'file_mappings', array() );

				// Save to safe mode in case of db
				if ( ! HMWP_Classes_Tools::getOption( 'logout' ) ) {
					HMWP_Classes_Tools::saveOptionsBackup();
				}

				// Clear the cache if there are no errors
				HMWP_Classes_Tools::emptyCache();

				if ( HMWP_Classes_Tools::isNginx() ) {
					@shell_exec( 'nginx -s reload' );
				}

				break;
			case 'hmwp_changepathsincache':
				// Check the cache plugin
				HMWP_Classes_ObjController::getClass( 'HMWP_Models_Compatibility' )->checkCacheFiles();

				HMWP_Classes_Error::setNotification( esc_html__( 'Paths changed in the existing cache files', 'hide-my-wp' ), 'success' );
				break;
			case 'hmwp_backup':
				// Save the Settings into backup
				if ( ! HMWP_Classes_Tools::userCan( HMWP_CAPABILITY ) ) {
					return;
				}
				HMWP_Classes_Tools::getOptions();
				HMWP_Classes_Tools::setHeader( 'text' );
				$filename = preg_replace( '/[-.]/', '_', wp_parse_url( home_url(), PHP_URL_HOST ) );
				header( "Content-Disposition: attachment; filename=" . $filename . "_wghost_backup.txt" );

				if ( function_exists( 'base64_encode' ) ) {
					echo base64_encode( json_encode( HMWP_Classes_Tools::$options ) );
				}
				exit();
			case 'hmwp_rollback':

				$hmwp_token = HMWP_Classes_Tools::getOption( 'hmwp_token' );
				$api_token  = HMWP_Classes_Tools::getOption( 'api_token' );

				$options = HMWP_Classes_Tools::$default;
				// Prevent duplicates
				foreach ( $options as $key => $value ) {
					// Set the default params from tools
					HMWP_Classes_Tools::saveOptions( $key, $value );
					HMWP_Classes_Tools::saveOptions( 'hmwp_token', $hmwp_token );
					HMWP_Classes_Tools::saveOptions( 'api_token', $api_token );
				}

				// Remove the custom rules
				HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rules' )->writeToFile( '', 'HMWP_VULNERABILITY' );
				HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rules' )->writeToFile( '', 'HMWP_RULES' );

				HMWP_Classes_Error::setNotification( esc_html__( 'Great! The initial values are restored.', 'hide-my-wp' ) . " <br /> ", 'success' );

				break;
			case 'hmwp_rollback_stable':
				HMWP_Classes_Tools::setHeader( 'html' );
				$plugin_slug = 'hide-my-wp';
				$rollback    = HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rollback' );

				$rollback->set_plugin( array(
						'version'     => HMWP_STABLE_VERSION,
						'plugin_name' => _HMWP_ROOT_DIR_,
						'plugin_slug' => $plugin_slug,
						'package_url' => sprintf( 'https://downloads.wordpress.org/plugin/%s.%s.zip', $plugin_slug, HMWP_STABLE_VERSION ),
					) );

				$rollback->run();

				wp_die( '', esc_html__( "Rollback to Previous Version", 'hide-my-wp' ), [
						'response' => 200,
					] );
			case 'hmwp_restore':

				// Initialize WordPress Filesystem
				$wp_filesystem = HMWP_Classes_ObjController::initFilesystem();

				// Restore the backup
				if ( ! HMWP_Classes_Tools::userCan( HMWP_CAPABILITY ) ) {
					return;
				}

				if ( ! empty( $_FILES['hmwp_options'] ) && $_FILES['hmwp_options']['tmp_name'] <> '' ) {
					$options = $wp_filesystem->get_contents( $_FILES['hmwp_options']['tmp_name'] );
					try {
						if ( function_exists( 'base64_encode' ) && base64_decode( $options ) <> '' ) {
							$options = base64_decode( $options );
						}
						$options = json_decode( $options, true );

						if ( is_array( $options ) && isset( $options['hmwp_ver'] ) ) {

							foreach ( $options as $key => $value ) {
								if ( $key <> 'hmwp_token' && $key <> 'api_token' ) {
									HMWP_Classes_Tools::saveOptions( $key, $value );
								}
							}

							//load the after saving settings process
							if ( $this->model->applyPermalinksChanged( true ) ) {
								HMWP_Classes_Error::setNotification( esc_html__( 'Great! The backup is restored.', 'hide-my-wp' ) . " <br /> ", 'success' );
							}

						} else {
							HMWP_Classes_Error::setNotification( esc_html__( 'Error! The backup is not valid.', 'hide-my-wp' ) . " <br /> " );
						}
					} catch ( Exception $e ) {
						HMWP_Classes_Error::setNotification( esc_html__( 'Error! The backup is not valid.', 'hide-my-wp' ) . " <br /> " );
					}
				} else {
					HMWP_Classes_Error::setNotification( esc_html__( 'Error! No backup to restore.', 'hide-my-wp' ) );
				}
				break;
			case 'hmwp_download_settings':
				// Save the Settings into backup
				if ( ! HMWP_Classes_Tools::userCan( HMWP_CAPABILITY ) ) {
					return;
				}

				HMWP_Classes_Tools::saveOptions( 'download_settings', false );

				HMWP_Classes_Tools::getOptions();
				HMWP_Classes_Tools::setHeader( 'text' );
				$filename = preg_replace( '/[-.]/', '_', wp_parse_url( home_url(), PHP_URL_HOST ) );
				header( "Content-Disposition: attachment; filename=" . $filename . "_wghost_login.txt" );

				$line    = "\n" . "________________________________________" . PHP_EOL;
				$message = sprintf( esc_html__( "Thank you for using %s!", 'hide-my-wp' ), HMWP_Classes_Tools::getOption( 'hmwp_plugin_name' ) ) . PHP_EOL;
				$message .= $line;
				$message .= esc_html__( "Your new site URLs are", 'hide-my-wp' ) . ':' . PHP_EOL . PHP_EOL;
				$message .= esc_html__( "Admin URL", 'hide-my-wp' ) . ': ' . admin_url() . PHP_EOL;
				$message .= esc_html__( "Login URL", 'hide-my-wp' ) . ': ' . site_url( HMWP_Classes_Tools::$options['hmwp_login_url'] ) . PHP_EOL;
				$message .= $line;
				$message .= esc_html__( "Note: If you can`t login to your site, just access this URL", 'hide-my-wp' ) . ':' . PHP_EOL . PHP_EOL;
				$message .= site_url() . "/wp-login.php?" . HMWP_Classes_Tools::getOption( 'hmwp_disable_name' ) . "=" . HMWP_Classes_Tools::$options['hmwp_disable'] . PHP_EOL . PHP_EOL;
				$message .= $line;
				$message .= esc_html__( "Best regards", 'hide-my-wp' ) . ',' . PHP_EOL;
				$message .= HMWP_Classes_Tools::getOption( 'hmwp_plugin_name' ) . PHP_EOL;

				// Echo the new paths in a txt file
				echo $message;
				exit();
			case 'hmwp_advanced_install':

				if ( ! HMWP_Classes_Tools::userCan( HMWP_CAPABILITY ) ) {
					return;
				}

				// Check the version
				$response = wp_remote_get( 'https://account.hidemywpghost.com/updates-hide-my-wp-pack.json?rnd=' . wp_rand( 1111, 9999 ) );

				if ( is_wp_error( $response ) ) {
					HMWP_Classes_Error::setNotification( $response->get_error_message() );
				} elseif ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
					HMWP_Classes_Error::setNotification( esc_html__( "Can't download the plugin.", 'hide-my-wp' ) );
				} else {
					if ( $data = json_decode( wp_remote_retrieve_body( $response ) ) ) {

						$rollback = HMWP_Classes_ObjController::getClass( 'HMWP_Models_Rollback' );

						$output = $rollback->install( array(
							'version'     => $data->version,
							'plugin_name' => $data->name,
							'plugin_slug' => $data->slug,
							'package_url' => $data->download_url,
						) );

						if ( ! is_wp_error( $output ) ) {
							$rollback->activate( $data->slug . '/index.php' );

							wp_redirect( HMWP_Classes_Tools::getSettingsUrl( HMWP_Classes_Tools::getValue( 'page' ) . '#tab=' . HMWP_Classes_Tools::getValue( 'tab' ), true ) );
							exit();
						} else {
							HMWP_Classes_Error::setNotification( $output->get_error_message() );
						}

					}

				}
				break;
			case 'hmwp_pause_enable':

				if ( ! HMWP_Classes_Tools::userCan( HMWP_CAPABILITY ) ) {
					return;
				}

				set_transient( 'hmwp_disable', 1, 300 );

				break;
			case 'hmwp_pause_disable':

				if ( ! HMWP_Classes_Tools::userCan( HMWP_CAPABILITY ) ) {
					return;
				}

				delete_transient( 'hmwp_disable' );

				break;

			case 'hmwp_update_product_name':
				if(HMWP_Classes_Tools::getOption('hmwp_plugin_name') == 'Hide My WP Ghost'){
					HMWP_Classes_Tools::saveOptions('hmwp_plugin_name', _HMWP_PLUGIN_FULL_NAME_);
				}
				if(HMWP_Classes_Tools::getOption('hmwp_plugin_menu') == 'Hide My WP'){
					HMWP_Classes_Tools::saveOptions('hmwp_plugin_menu', _HMWP_PLUGIN_FULL_NAME_);
				}
				if(HMWP_Classes_Tools::getOption('hmwp_plugin_website') == 'https://hidemywpghost.com'){
					HMWP_Classes_Tools::saveOptions('hmwp_plugin_website', 'https://wpghost.com');
				}
				break;
		}

	}

	/**
	 * Save the whitelist IPs into database
	 *
	 * @return void
	 */
	private function saveWhiteListIps() {

		$whitelist = HMWP_Classes_Tools::getValue( 'whitelist_ip', '', true );

		// Is there are separated by commas
		if ( strpos( $whitelist, ',' ) !== false ) {
			$whitelist = str_replace( ',', PHP_EOL, $whitelist );
		}

		$ips = explode( PHP_EOL, $whitelist );

		if ( ! empty( $ips ) ) {
			foreach ( $ips as &$ip ) {
				$ip = trim( $ip );

				// Check for IPv4 IP cast as IPv6
				if ( preg_match( '/^::ffff:(\d+\.\d+\.\d+\.\d+)$/', $ip, $matches ) ) {
					$ip = $matches[1];
				}
			}

			$ips = array_unique( $ips );
			HMWP_Classes_Tools::saveOptions( 'whitelist_ip', json_encode( $ips ) );
		}

	}

	/**
	 * Save the whitelist Paths into database
	 *
	 * @return void
	 */
	private function saveWhiteListPaths() {

		$whitelist = HMWP_Classes_Tools::getValue( 'whitelist_urls', '', true );

		// Is there are separated by commas
		if ( strpos( $whitelist, ',' ) !== false ) {
			$whitelist = str_replace( ',', PHP_EOL, $whitelist );
		}

		$urls = explode( PHP_EOL, $whitelist );

		if ( ! empty( $urls ) ) {
			foreach ( $urls as &$url ) {
				$url = trim( $url );
			}

			$urls = array_unique( $urls );
			HMWP_Classes_Tools::saveOptions( 'whitelist_urls', json_encode( $urls ) );
		}

	}

	/**
	 * Save the whitelist IPs into database
	 *
	 * @return void
	 */
	private function saveBlackListIps() {

		$banlist = HMWP_Classes_Tools::getValue( 'banlist_ip', '', true );

		// Is there are separated by commas
		if ( strpos( $banlist, ',' ) !== false ) {
			$banlist = str_replace( ',', PHP_EOL, $banlist );
		}

		$ips = explode( PHP_EOL, $banlist );

		if ( ! empty( $ips ) ) {
			foreach ( $ips as &$ip ) {
				$ip = trim( $ip );

				// Check for IPv4 IP cast as IPv6
				if ( preg_match( '/^::ffff:(\d+\.\d+\.\d+\.\d+)$/', $ip, $matches ) ) {
					$ip = $matches[1];
				}
			}

			$ips = array_unique( $ips );
			HMWP_Classes_Tools::saveOptions( 'banlist_ip', json_encode( $ips ) );
		}

	}

	/**
	 * Save the Hostnames to Blacklist
	 *
	 * @return void
	 */
	private function saveBlackListHostnames() {

		$banlist = HMWP_Classes_Tools::getValue( 'banlist_hostname', '', true );

		//is there are separated by commas
		if ( strpos( $banlist, ',' ) !== false ) {
			$banlist = str_replace( ',', PHP_EOL, $banlist );
		}

		$list = explode( PHP_EOL, $banlist );

		if ( ! empty( $list ) ) {
			foreach ( $list as $index => &$row ) {
				$row = trim( $row );

				if ( preg_match( '/^[a-z0-9\.\*\-]+$/i', $row, $matches ) ) {
					$row = $matches[0];
				} else {
					unset( $list[ $index ] );
				}
			}

			$list = array_unique( $list );
			HMWP_Classes_Tools::saveOptions( 'banlist_hostname', json_encode( $list ) );
		}

	}

	/**
	 * Save the User Agents to the blacklist.
	 *
	 * @return void
	 */
	private function saveBlackListUserAgents() {

		$banlist = HMWP_Classes_Tools::getValue( 'banlist_user_agent', '', true );

		//is there are separated by commas
		if ( strpos( $banlist, ',' ) !== false ) {
			$banlist = str_replace( ',', PHP_EOL, $banlist );
		}

		$list = explode( PHP_EOL, $banlist );

		if ( ! empty( $list ) ) {
			foreach ( $list as $index => &$row ) {
				$row = trim( $row );

				if ( preg_match( '/^[a-z0-9\.\*\-]+$/i', $row, $matches ) ) {
					$row = $matches[0];
				} else {
					unset( $list[ $index ] );
				}
			}

			$list = array_unique( $list );
			HMWP_Classes_Tools::saveOptions( 'banlist_user_agent', json_encode( $list ) );
		}

	}

	/**
	 * Save the Referrers
	 *
	 * @return void
	 */
	private function saveBlackListReferrers() {

		$banlist = HMWP_Classes_Tools::getValue( 'banlist_referrer', '', true );

		// Is there are separated by commas
		if ( strpos( $banlist, ',' ) !== false ) {
			$banlist = str_replace( ',', PHP_EOL, $banlist );
		}

		$list = explode( PHP_EOL, $banlist );

		if ( ! empty( $list ) ) {
			foreach ( $list as $index => &$row ) {
				$row = trim( $row );

				if ( preg_match( '/^[a-z0-9\.\*\-]+$/i', $row, $matches ) ) {
					$row = $matches[0];
				} else {
					unset( $list[ $index ] );
				}
			}

			$list = array_unique( $list );
			HMWP_Classes_Tools::saveOptions( 'banlist_referrer', json_encode( $list ) );
		}

	}

	/**
	 * Adds a noscript tag to the footer to handle scenarios where JavaScript is disabled.
	 *
	 * @return void
	 */
	public function hookFooter() {
		echo '<noscript><style>.tab-panel {display: block;}</style></noscript>';
	}

}
