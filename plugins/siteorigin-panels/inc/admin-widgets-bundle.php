<?php

class SiteOrigin_Panels_Admin_Widgets_Bundle {

	public function __construct() {
		if ( isset( $_GET['siteorigin-pa-install'] ) ) {
			add_action( 'admin_menu', array( $this, 'activation_page' ) );
		}
	}

	public static function single() {
		static $single;

		return empty( $single ) ? $single = new self() : $single;
	}

	public function activation_page() {
		add_plugins_page(
			__( 'Install Page Builder Plugin', 'siteorigin-panels' ),
			__( 'Install Page Builder Plugin', 'siteorigin-panels' ),
			'install_plugins',
			'siteorigin_panels_plugin_activation',
			array( $this, 'render_page' )
		);
	}

	public function render_page() {
		?>
		<div class="wrap">
			<?php
			/** All plugin information will be stored in an array for processing */
			$plugin = array();

		/* Checks for actions from hover links to process the installation */
		if ( isset( $_GET[ sanitize_key( 'plugin' ) ] ) && ( isset( $_GET[ sanitize_key( 'siteorigin-pa-install' ) ] ) && 'install-plugin' == $_GET[ sanitize_key( 'siteorigin-pa-install' ) ] ) && current_user_can( 'install_plugins' ) ) {
			check_admin_referer( 'siteorigin-pa-install' );

			$plugin['name'] = sanitize_text_field( wp_unslash( $_GET['plugin_name'] ) ); // Plugin name
			$plugin['slug'] = sanitize_text_field( wp_unslash( $_GET['plugin'] ) ); // Plugin slug

			if ( ! empty( $_GET['plugin_source'] ) ) {
				$plugin['source'] = esc_url_raw( $_GET['plugin_source'] );
			} else {
				$plugin['source'] = false;
			}

			/** Pass all necessary information via URL if WP_Filesystem is needed */
			$url = esc_url( wp_nonce_url(
				add_query_arg(
					array(
						'page'                  => 'siteorigin_panels_plugin_activation',
						'plugin'                => urlencode( $plugin['slug'] ),
						'plugin_name'           => urlencode( $plugin['name'] ),
						'plugin_source'         => urlencode( $plugin['source'] ),
						'siteorigin-pa-install' => 'install-plugin',
					),
					admin_url( 'themes.php' )
				),
				'siteorigin-pa-install'
			) );
			$method = ''; // Leave blank so WP_Filesystem can populate it as necessary
			$fields = array( sanitize_key( 'siteorigin-pa-install' ) ); // Extra fields to pass to WP_Filesystem

			if ( false === ( $creds = request_filesystem_credentials( $url, $method, false, false, $fields ) ) ) {
				return true;
			}

			if ( ! WP_Filesystem( $creds ) ) {
				request_filesystem_credentials( $url, $method, true, false, $fields ); // Setup WP_Filesystem

				return true;
			}

			require_once ABSPATH . 'wp-admin/includes/plugin-install.php'; // Need for plugins_api
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php'; // Need for upgrade classes

			/** Prep variables for Plugin_Installer_Skin class */
			$title = sprintf( __( 'Installing %s', 'siteorigin-panels' ), $plugin['name'] );
			$url = add_query_arg( array(
				'action' => 'install-plugin',
				'plugin' => urlencode( $plugin['slug'] ),
			), 'update.php' );

			if ( isset( $_GET['from'] ) ) {
				$url .= add_query_arg( 'from', urlencode( stripslashes( $_GET['from'] ) ), $url );
			}

			$nonce = 'install-plugin_' . $plugin['slug'];

			// Find the source of the plugin
			$source = ! empty( $plugin['source'] ) ? $plugin['source'] : 'http://downloads.wordpress.org/plugin/' . urlencode( $plugin['slug'] ) . '.zip';

			/** Create a new instance of Plugin_Upgrader */
			$upgrader = new Plugin_Upgrader( $skin = new Plugin_Installer_Skin( compact( 'type', 'title', 'url', 'nonce', 'plugin', 'api' ) ) );

			/* Perform the action and install the plugin from the $source urldecode() */
			$upgrader->install( $source );

			/* Flush plugins cache so we can make sure that the installed plugins list is always up to date */
			wp_cache_flush();
		}
		?>
		</div>
		<?php
	}

	public static function install_url( $plugin, $plugin_name, $source = false ) {
		// This is to prevent the issue where this URL is called from outside the admin
		if ( ! is_admin() || ! function_exists( 'get_plugins' ) ) {
			return false;
		}

		$plugins = get_plugins();
		$plugins = array_keys( $plugins );

		$installed = false;

		foreach ( $plugins as $plugin_path ) {
			if ( strpos( $plugin_path, $plugin . '/' ) === 0 ) {
				$installed = true;
				break;
			}
		}

		if ( $installed && ! is_plugin_active( $plugin ) ) {
			return esc_url( wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . $plugin_path ), 'activate-plugin_' . $plugin_path ) );
		} elseif ( $installed && is_plugin_active( $plugin ) ) {
			return '#';
		} else {
			return esc_url( wp_nonce_url(
				add_query_arg(
					array(
						'page'                  => 'siteorigin_panels_plugin_activation',
						'plugin'                => $plugin,
						'plugin_name'           => $plugin_name,
						'plugin_source'         => ! empty( $source ) ? urlencode( $source ) : false,
						'siteorigin-pa-install' => 'install-plugin',
					),
					admin_url( 'plugins.php' )
				),
				'siteorigin-pa-install'
			) );
		}
	}
}
