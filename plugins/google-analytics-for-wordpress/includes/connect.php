<?php
/**
 * MonsterInsights Connect is our service that makes it easy for non-techy users to
 * upgrade to MonsterInsights Pro without having to manually install the MonsterInsights Pro plugin.
 *
 * @package MonsterInsights
 * @since 7.7.2
 */
/**
 * Class MonsterInsights_Connect
 */
class MonsterInsights_Connect {

	/**
	 * MonsterInsights_Connect constructor.
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Add hooks for Connect.
	 */
	public function hooks() {

		add_action( 'wp_ajax_monsterinsights_connect_url', array( $this, 'generate_connect_url' ) );
		add_action( 'wp_ajax_nopriv_monsterinsights_connect_process', array( $this, 'process' ) );
	}

	/**
	 * Generate the connect URL with the given key and network status
	 *
	 * @param string $key License key
	 * @param bool $network Whether this is a network-wide connection
	 * @return array Array containing the URL and the one time hash
	 */
	public static function generate_connect_url_data( $key, $network = false ) {
		if ( empty( $key ) ) {
			return false;
		}

		// Generate and store hash
		$oth = hash( 'sha512', wp_rand() );
		$hashed_oth = hash_hmac( 'sha512', $oth, wp_salt() );
		
		update_option( 'monsterinsights_connect', array(
			'key'     => $key,
			'time'    => time(),
			'network' => $network,
		));
		update_option( 'monsterinsights_connect_token', $oth );
		
		// Generate URL
		$version  = MonsterInsights()->version;
		$siteurl  = admin_url();
		$endpoint = admin_url( 'admin-ajax.php' );
		$redirect = $network ? network_admin_url( 'admin.php?page=monsterinsights_network' ) : admin_url( 'admin.php?page=monsterinsights_settings' );

		$url = add_query_arg(
			array(
				'key'      => $key,
				'oth'      => $hashed_oth,
				'endpoint' => $endpoint,
				'version'  => $version,
				'siteurl'  => $siteurl,
				'homeurl'  => home_url(),
				'redirect' => rawurldecode( base64_encode( $redirect ) ),
				'v'        => 2,
			),
			'https://upgrade.monsterinsights.com'
		);

		return array(
			'url' => $url,
			'oth' => $oth,
		);
	}

	/**
	 * Generates and returns MonsterInsights Connect URL.
	 */
	public function generate_connect_url() {
		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		// Check for permissions.
		if ( ! monsterinsights_can_install_plugins() ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Oops! You are not allowed to install plugins. Please contact your site administrator.', 'google-analytics-for-wordpress' ) ) );
		}

		if ( monsterinsights_is_dev_url( home_url() ) ) {
			wp_send_json_success( array(
				'url' => 'https://www.monsterinsights.com/docs/go-lite-pro/#manual-upgrade',
			) );
		}
		$key = ! empty( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

		if ( empty( $key ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Please enter your license key to connect.', 'google-analytics-for-wordpress' ),
				)
			);
		}

		// Verify pro version is not installed.
		$active = activate_plugin( 'google-analytics-premium/googleanalytics-premium.php', false, false, true );
		if ( ! is_wp_error( $active ) ) {
			// Deactivate plugin.
			deactivate_plugins( plugin_basename( MONSTERINSIGHTS_PLUGIN_FILE ), false, false );
			wp_send_json_error( array(
				'message' => esc_html__( 'You already have MonsterInsights Pro installed.', 'google-analytics-for-wordpress' ),
				'reload'  => true,
			) );
		}

		// Network?
		$network = ! empty( $_POST['network'] ) && $_POST['network']; // phpcs:ignore

		$url_data = self::generate_connect_url_data( $key, $network );
		if ( empty( $url_data ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Please enter your license key to connect.', 'google-analytics-for-wordpress' ),
				)
			);
		}

		wp_send_json_success( array(
			'url' => $url_data['url'],
		) );
	}

	/**
	 * Process MonsterInsights Connect.
	 */
	public function process() {
		// Translators: Link tag starts with url and link tag ends.
		$error = sprintf(
			esc_html__( 'Oops! We could not automatically install an upgrade. Please install manually by visiting %1$smonsterinsights.com%2$s.', 'google-analytics-for-wordpress' ),
			'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'could-not-upgrade', 'https://www.monsterinsights.com/' ) . '">',
			'</a>'
		);

		// verify params present (oth & download link).
		$post_oth = ! empty( $_REQUEST['oth'] ) ? sanitize_text_field($_REQUEST['oth']) : '';
		$post_url = ! empty( $_REQUEST['file'] ) ? sanitize_url($_REQUEST['file']) : '';
		$license  = get_option( 'monsterinsights_connect', false );
		$network  = ! empty( $license['network'] ) ? (bool) $license['network'] : false;
		if ( empty( $post_oth ) || empty( $post_url ) ) {
			wp_send_json_error( $error );
		}
		// Verify oth.
		$oth = get_option( 'monsterinsights_connect_token' );
		if ( empty( $oth ) ) {
			wp_send_json_error( $error );
		}
		if ( hash_hmac( 'sha512', $oth, wp_salt() ) !== $post_oth ) {
			wp_send_json_error( $error );
		}
		// Delete so cannot replay.
		delete_option( 'monsterinsights_connect_token' );
		// Set the current screen to avoid undefined notices.
		set_current_screen( 'insights_page_monsterinsights_settings' );
		// Prepare variables.
		$url = esc_url_raw(
			add_query_arg(
				array(
					'page' => 'monsterinsights-settings',
				),
				admin_url( 'admin.php' )
			)
		);
		// Verify pro not activated.
		if ( monsterinsights_is_pro_version() ) {
			wp_send_json_success( esc_html__( 'Plugin installed & activated.', 'google-analytics-for-wordpress' ) );
		}
		// Verify pro not installed.
		$active = activate_plugin( 'google-analytics-premium/googleanalytics-premium.php', $url, $network, true );
		if ( ! is_wp_error( $active ) ) {
			deactivate_plugins( plugin_basename( MONSTERINSIGHTS_PLUGIN_FILE ), false, $network );
			wp_send_json_success( esc_html__( 'Plugin installed & activated.', 'google-analytics-for-wordpress' ) );
		}
		$creds = request_filesystem_credentials( $url, '', false, false, null );
		// Check for file system permissions.
		if ( false === $creds ) {
			wp_send_json_error( $error );
		}
		if ( ! WP_Filesystem( $creds ) ) {
			wp_send_json_error( $error );
		}
		// We do not need any extra credentials if we have gotten this far, so let's install the plugin.
		monsterinsights_require_upgrader();
		// Do not allow WordPress to search/download translations, as this will break JS output.
		remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );
		// Create the plugin upgrader with our custom skin.
		$installer = new MonsterInsights_Plugin_Upgrader( new MonsterInsights_Skin() );
		// Error check.
		if ( ! method_exists( $installer, 'install' ) ) {
			wp_send_json_error( $error );
		}

		// Check license key.
		if ( empty( $license['key'] ) ) {
			wp_send_json_error( new WP_Error( '403', esc_html__( 'You are not licensed.', 'google-analytics-for-wordpress' ) ) );
		}

		$installer->install( $post_url ); // phpcs:ignore
		// Flush the cache and return the newly installed plugin basename.
		wp_cache_flush();

		if ( $installer->plugin_info() ) {
			$plugin_basename = $installer->plugin_info();

			// Check this before deactivating plugin.
			$is_authed = MonsterInsights()->auth->is_authed();

			// Deactivate the lite version first.
			deactivate_plugins( plugin_basename( MONSTERINSIGHTS_PLUGIN_FILE ), false, $network );

			// Activate the plugin silently.
			$activated = activate_plugin( $plugin_basename, '', $network, true );
			if ( ! is_wp_error( $activated ) ) {
				// Pro upgrade successful.
				$over_time = get_option( 'monsterinsights_over_time', array() );

				if ( empty( $over_time['installed_pro'] ) ) {
					$over_time['installed_pro'] = time();
					if ( $is_authed ) {
						$over_time['connected_upgrade'] = time();
					}
					update_option( 'monsterinsights_over_time', $over_time );
				}

				wp_send_json_success( esc_html__( 'Plugin installed & activated.', 'google-analytics-for-wordpress' ) );
			} else {
				// Reactivate the lite plugin if pro activation failed.
				activate_plugin( plugin_basename( MONSTERINSIGHTS_PLUGIN_FILE ), '', $network, true );
				wp_send_json_error( esc_html__( 'Please activate MonsterInsights Pro from your WordPress plugins page.', 'google-analytics-for-wordpress' ) );
			}
		}
		wp_send_json_error( $error );
	}

}

new MonsterInsights_Connect();
