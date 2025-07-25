<?php
/**
 * Install addon.
 *
 * @since 1.0.0
 */
function seedprod_lite_install_addon() {
	// Run a security check.
	check_ajax_referer( 'seedprod_lite_install_addon', 'nonce' );

	// Check for permissions.
	if ( ! current_user_can( 'install_plugins' ) ) {
		wp_send_json_error();
	}

	// Install the addon.
	if ( isset( $_POST['plugin'] ) ) {
		$download_url = sanitize_text_field( wp_unslash( $_POST['plugin'] ) );

		global $hook_suffix;

		// Set the current screen to avoid undefined notices.
		set_current_screen();

		// Prepare variables.
		$method = '';
		$url    = add_query_arg(
			array(
				'page' => 'seedprod_lite',
			),
			admin_url( 'admin.php' )
		);
		$url    = esc_url( $url );

		// Start output bufferring to catch the filesystem form if credentials are needed.
		ob_start();
		$creds = request_filesystem_credentials( $url, $method, false, false, null );
		if ( false === $creds ) {
			$form = ob_get_clean();
			echo wp_json_encode( array( 'form' => $form ) );
			wp_die();
		}

		// If we are not authenticated, make it happen now.
		if ( ! WP_Filesystem( $creds ) ) {
			ob_start();
			request_filesystem_credentials( $url, $method, true, false, null );
			$form = ob_get_clean();
			echo wp_json_encode( array( 'form' => $form ) );
			wp_die();
		}

		// We do not need any extra credentials if we have gotten this far, so let's install the plugin.
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		global $wp_version;
		if ( version_compare( $wp_version, '5.3.0' ) >= 0 ) {
			require_once SEEDPROD_PLUGIN_PATH . 'app/includes/skin53.php';
		} else {
			require_once SEEDPROD_PLUGIN_PATH . 'app/includes/skin.php';
		}

		// Create the plugin upgrader with our custom skin.
		$installer = new Plugin_Upgrader( new SeedProd_Skin() );
		$installer->install( $download_url );

		// Set referrer if one exists
		if ( ! empty( $_POST['referrer'] ) ) {
			$referrer = sanitize_text_field( wp_unslash( $_POST['referrer'] ) );
			update_option( 'optinmonster_referred_by', $referrer );
		}

		// Flush the cache and return the newly installed plugin basename.
		wp_cache_flush();
		if ( $installer->plugin_info() ) {
			$plugin_basename = $installer->plugin_info();
			echo wp_json_encode( array( 'plugin' => $plugin_basename ) );
			wp_die();
		}
	}

	// Send back a response.
	echo wp_json_encode( true );
	wp_die();
}


/**
 * Deactivate addon.
 *
 * @since 1.0.0
 */
function seedprod_lite_deactivate_addon() {
	// Run a security check.
	check_ajax_referer( 'seedprod_lite_deactivate_addon', 'nonce' );

	// Check for permissions.
	if ( ! current_user_can( 'activate_plugins' ) ) {
		wp_send_json_error();
	}

	$type = 'addon';
	if ( ! empty( $_POST['type'] ) ) {
		$type = sanitize_key( wp_unslash( $_POST['type'] ) );
	}

	if ( isset( $_POST['plugin'] ) ) {
		$plugin = sanitize_text_field( wp_unslash( $_POST['plugin'] ) );
		deactivate_plugins( $plugin );

		if ( 'plugin' === $type ) {
			wp_send_json_success( esc_html__( 'Plugin deactivated.', 'coming-soon' ) );
		} else {
			wp_send_json_success( esc_html__( 'Addon deactivated.', 'coming-soon' ) );
		}
	}

	wp_send_json_error( esc_html__( 'Could not deactivate the addon. Please deactivate from the Plugins page.', 'coming-soon' ) );
}


/**
 * Activate addon.
 *
 * @since 1.0.0
 */
function seedprod_lite_activate_addon() {
	// Run a security check.
	if ( check_ajax_referer( 'seedprod_lite_activate_addon', 'nonce' ) ) {
		// Check for permissions.
		if ( ! current_user_can( 'activate_plugin' ) ) {
			wp_send_json_error( esc_html__( 'Could not activate addon. Please check user permissions.', 'coming-soon' ) );
		}

		if ( isset( $_POST['plugin'] ) ) {
			$type = 'addon';
			if ( ! empty( $_POST['type'] ) ) {
				$type = sanitize_key( wp_unslash( $_POST['type'] ) );
			}

			$plugin   = sanitize_text_field( wp_unslash( $_POST['plugin'] ) );
			$activate = activate_plugin( $plugin, '', false, true );

			if ( ! is_wp_error( $activate ) ) {
				if ( 'plugin' === $type ) {
					wp_send_json_success( esc_html__( 'Plugin activated.', 'coming-soon' ) );
				} else {
					wp_send_json_success( esc_html__( 'Addon activated.', 'coming-soon' ) );
				}
			}
		}

		wp_send_json_error( esc_html__( 'Could not activate addon. Please activate from the Plugins page.', 'coming-soon' ) );
	}

	wp_send_json_error( esc_html__( 'Could not activate addon. Please refresh page and try again.', 'coming-soon' ) );
}

/**
 * Get plugin list.
 *
 * @return void
 */
function seedprod_lite_get_plugins_list() {
	check_ajax_referer( 'seedprod_lite_get_plugins_list', 'nonce' );

	$am_plugins  = array(
		'google-analytics-for-wordpress/googleanalytics.php' => 'monsterinsights',
		'google-analytics-premium/googleanalytics-premium.php' => 'monsterinsights-pro',
		'optinmonster/optin-monster-wp-api.php'            => 'optinmonster',
		'wp-mail-smtp/wp_mail_smtp.php'                    => 'wpmailsmtp',
		'wp-mail-smtp-pro/wp_mail_smtp.php'                => 'wpmailsmtp-pro',
		'wpforms-lite/wpforms.php'                         => 'wpforms',
		'wpforms/wpforms.php'                              => 'wpforms-pro',
		'envira-gallery-lite/envira-gallery-lite.php'      => 'envira',
		'envira-gallery/envira-gallery.php'                => 'envira-pro',
		'rafflepress/rafflepress.php'                      => 'rafflepress',
		'rafflepress-pro/rafflepress-pro.php'              => 'rafflepress-pro',
		'mypaykit-payment-forms-for-square/mypaykit-payment-forms-for-square.php' => 'mypaykit',
		'trustpulse-api/trustpulse.php'                    => 'trustpulse',
		'google-analytics-dashboard-for-wp/gadwp.php'      => 'exactmetrics',
		'exactmetrics-premium/exactmetrics-premium.php'    => 'exactmetrics-pro',
		'all-in-one-seo-pack/all_in_one_seo_pack.php'      => 'all-in-one',
		'all-in-one-seo-pack-pro/all_in_one_seo_pack.php'  => 'all-in-one-pro',
		'seo-by-rank-math/rank-math.php'                   => 'rank-math',
		'wordpress-seo/wp-seo.php'                         => 'yoast',
		'autodescription/autodescription.php'              => 'seo-framework',
		'instagram-feed/instagram-feed.php'                => 'instagramfeed',
		'instagram-feed-pro/instagram-feed.php'            => 'instagramfeed-pro',
		'custom-facebook-feed/custom-facebook-feed.php'    => 'customfacebookfeed',
		'custom-facebook-feed-pro/custom-facebook-feed.php' => 'customfacebookfeed-pro',
		'custom-twitter-feeds/custom-twitter-feed.php'     => 'customtwitterfeeds',
		'custom-twitter-feeds-pro/custom-twitter-feed.php' => 'customtwitterfeeds-pro',
		'feeds-for-youtube/youtube-feed.php'               => 'feedsforyoutube',
		'youtube-feed-pro/youtube-feed.php'                => 'feedsforyoutube-pro',
		'pushengage/main.php'                              => 'pushengage',
		'sugar-calendar-lite/sugar-calendar-lite.php'      => 'sugarcalendar',
		'sugar-calendar/sugar-calendar.php'                => 'sugarcalendar-pro',
		'stripe/stripe-checkout.php'                       => 'wpsimplepay',
		'wp-simple-pay-pro-3/simple-pay.php'               => 'wpsimplepay-pro',
		'easy-digital-downloads/easy-digital-downloads.php' => 'easydigitaldownloads',
		'easy-digital-downloads-pro/easy-digital-downloads.php' => 'easydigitaldownloads-pro',
		'searchwp/index.php'                               => 'searchwp',
		'affiliate-wp/affiliate-wp.php'                    => 'affiliatewp',
		'insert-headers-and-footers/ihaf.php'              => 'wpcode',
		'wpcode-premium/wpcode.php'                        => 'wpcode-pro',
	);
	$all_plugins = get_plugins();

	$response = array();

	foreach ( $am_plugins as $slug => $label ) {
		if ( array_key_exists( $slug, $all_plugins ) ) {
			if ( is_plugin_active( $slug ) ) {
				$response[ $label ] = array(
					'label'  => __( 'Active', 'coming-soon' ),
					'status' => 1,
				);
			} else {
				$response[ $label ] = array(
					'label'  => __( 'Inactive', 'coming-soon' ),
					'status' => 2,
				);
			}
		} else {
			$response[ $label ] = array(
				'label'  => __( 'Not Installed', 'coming-soon' ),
				'status' => 0,
			);
		}
	}

	wp_send_json( $response );
}

/**
 * Get plugins array.
 *
 * @return array $response Contains plugins and their installation states as an associative array.
 */
function seedprod_lite_get_plugins_array() {
	$am_plugins  = array(
		'google-analytics-for-wordpress/googleanalytics.php' => 'monsterinsights',
		'google-analytics-premium/googleanalytics-premium.php' => 'monsterinsights-pro',
		'optinmonster/optin-monster-wp-api.php'            => 'optinmonster',
		'wp-mail-smtp/wp_mail_smtp.php'                    => 'wpmailsmtp',
		'wp-mail-smtp-pro/wp_mail_smtp.php'                => 'wpmailsmtp-pro',
		'wpforms-lite/wpforms.php'                         => 'wpforms',
		'wpforms/wpforms.php'                              => 'wpforms-pro',
		'envira-gallery-lite/envira-gallery-lite.php'      => 'envira',
		'envira-gallery/envira-gallery.php'                => 'envira-pro',
		'rafflepress/rafflepress.php'                      => 'rafflepress',
		'rafflepress-pro/rafflepress-pro.php'              => 'rafflepress-pro',
		'mypaykit-payment-forms-for-square/mypaykit-payment-forms-for-square.php' => 'mypaykit',
		'trustpulse-api/trustpulse.php'                    => 'trustpulse',
		'google-analytics-dashboard-for-wp/gadwp.php'      => 'exactmetrics',
		'exactmetrics-premium/exactmetrics-premium.php'    => 'exactmetrics-pro',
		'all-in-one-seo-pack/all_in_one_seo_pack.php'      => 'all-in-one',
		'all-in-one-seo-pack-pro/all_in_one_seo_pack.php'  => 'all-in-one-pro',
		'seo-by-rank-math/rank-math.php'                   => 'rank-math',
		'wordpress-seo/wp-seo.php'                         => 'yoast',
		'autodescription/autodescription.php'              => 'seo-framework',
		'instagram-feed/instagram-feed.php'                => 'instagramfeed',
		'instagram-feed-pro/instagram-feed.php'            => 'instagramfeed-pro',
		'custom-facebook-feed/custom-facebook-feed.php'    => 'customfacebookfeed',
		'custom-facebook-feed-pro/custom-facebook-feed.php' => 'customfacebookfeed-pro',
		'custom-twitter-feeds/custom-twitter-feed.php'     => 'customtwitterfeeds',
		'custom-twitter-feeds-pro/custom-twitter-feed.php' => 'customtwitterfeeds-pro',
		'feeds-for-youtube/youtube-feed.php'               => 'feedsforyoutube',
		'youtube-feed-pro/youtube-feed.php'                => 'feedsforyoutube-pro',
		'pushengage/main.php'                              => 'pushengage',
		'sugar-calendar-lite/sugar-calendar-lite.php'      => 'sugarcalendar',
		'sugar-calendar/sugar-calendar.php'                => 'sugarcalendar-pro',
		'stripe/stripe-checkout.php'                       => 'wpsimplepay',
		'wp-simple-pay-pro-3/simple-pay.php'               => 'wpsimplepay-pro',
		'easy-digital-downloads/easy-digital-downloads.php' => 'easydigitaldownloads',
		'easy-digital-downloads-pro/easy-digital-downloads.php' => 'easydigitaldownloads-pro',
		'searchwp/index.php'                               => 'searchwp',
		'affiliate-wp/affiliate-wp.php'                    => 'affiliatewp',
		'insert-headers-and-footers/ihaf.php'              => 'wpcode',
		'wpcode-premium/wpcode.php'                        => 'wpcode-pro',
	);
	$all_plugins = get_plugins();

	$response = array();

	foreach ( $am_plugins as $slug => $label ) {
		if ( array_key_exists( $slug, $all_plugins ) ) {
			if ( is_plugin_active( $slug ) ) {
				$response[ $label ] = array(
					'label'  => __( 'Active', 'coming-soon' ),
					'status' => 1,
				);
			} else {
				$response[ $label ] = array(
					'label'  => __( 'Inactive', 'coming-soon' ),
					'status' => 2,
				);
			}
		} else {
			$response[ $label ] = array(
				'label'  => __( 'Not Installed', 'coming-soon' ),
				'status' => 0,
			);
		}
	}

	return $response;
}

/**
 * Get form plugins list.
 *
 * @return array $response Contains array of plugins and installation states as integers.
 */
function seedprod_lite_get_form_plugins_list() {
	$am_plugins  = array(
		'wpforms/wpforms.php'      => 'wpforms',
		'wpforms-lite/wpforms.php' => 'wpforms-lite',
	);
	$all_plugins = get_plugins();

	$response = array();

	foreach ( $am_plugins as $slug => $label ) {
		if ( array_key_exists( $slug, $all_plugins ) ) {
			if ( is_plugin_active( $slug ) ) {
				$response[ $label ] = 1; // Active
			} else {
				$response[ $label ] = 2; // InActive
			}
		} else {
			$response[ $label ] = 0; // Not installed
		}
	}

	return $response;
}

/**
 * Get push notifications plugins list.
 *
 * @return array $response Contains array of plugins and installation states as integers.
 */
function seedprod_lite_get_push_notification_plugins_list() {
	$am_plugins  = array(
		'pushengage/main.php' => 'pushengage',
	);
	$all_plugins = get_plugins();

	$response = array();

	foreach ( $am_plugins as $slug => $label ) {
		if ( array_key_exists( $slug, $all_plugins ) ) {
			if ( is_plugin_active( $slug ) ) {
				$response[ $label ] = 1; // Active
			} else {
				$response[ $label ] = 2; // InActive
			}
		} else {
			$response[ $label ] = 0; // Not installed
		}
	}

	return $response;
}

/**
 * Get giveaway plugins list.
 *
 * @return array $response An array of giveaway plugins and their installation statuses.
 */
function seedprod_lite_get_giveaway_plugins_list() {
	$am_plugins  = array(
		'rafflepress-pro/rafflepress-pro.php' => 'rafflepress-pro',
		'rafflepress/rafflepress.php'         => 'rafflepress',
	);
	$all_plugins = get_plugins();

	$response = array();

	foreach ( $am_plugins as $slug => $label ) {
		if ( array_key_exists( $slug, $all_plugins ) ) {
			if ( is_plugin_active( $slug ) ) {
				$response[ $label ] = 1; // Active
			} else {
				$response[ $label ] = 2; // InActive
			}
		} else {
			$response[ $label ] = 0; // Not installed
		}
	}

	return $response;
}

/**
 * Get SEO Plugins list.
 *
 * @return array $response An array of SEO plugins and their installation statuses.
 */
function seedprod_lite_get_seo_plugins_list() {
	$am_plugins  = array(
		'all-in-one-seo-pack/all_in_one_seo_pack.php'     => 'all-in-one',
		'all-in-one-seo-pack-pro/all_in_one_seo_pack.php' => 'all-in-one-pro',
		'seo-by-rank-math/rank-math.php'                  => 'rank-math',
		'wordpress-seo/wp-seo.php'                        => 'yoast',
		'wordpress-seo-premium/wp-seo-premium.php'        => 'yoast-pro',
		'autodescription/autodescription.php'             => 'seo-framework',
	);
	$all_plugins = get_plugins();

	$response = array();

	foreach ( $am_plugins as $slug => $label ) {
		if ( array_key_exists( $slug, $all_plugins ) ) {
			if ( is_plugin_active( $slug ) ) {
				$response[ $label ] = 1; // Active
			} else {
				$response[ $label ] = 2; // InActive
			}
		} else {
			$response[ $label ] = 0; // Not installed
		}
	}

	return $response;
}

/**
 * Get analytics plugins list.
 *
 * @return array $response An array of analytics plugins and their installation statuses.
 */
function seedprod_lite_get_analytics_plugins_list() {
	$am_plugins  = array(
		'google-analytics-for-wordpress/googleanalytics.php' => 'monsterinsights',
		'google-analytics-premium/googleanalytics-premium.php' => 'monsterinsights-pro',
		'google-analytics-dashboard-for-wp/gadwp.php'   => 'exactmetrics',
		'exactmetrics-premium/exactmetrics-premium.php' => 'exactmetrics-pro',
	);
	$all_plugins = get_plugins();

	$response = array();

	foreach ( $am_plugins as $slug => $label ) {
		if ( array_key_exists( $slug, $all_plugins ) ) {
			if ( is_plugin_active( $slug ) ) {
				$response[ $label ] = 1; // Active
			} else {
				$response[ $label ] = 2; // InActive
			}
		} else {
			$response[ $label ] = 0; // Not installed
		}
	}

	return $response;
}

/**
 * Get plugins install url.
 *
 * @param string $slug Plugin slug.
 * @return string $url URL with wp_nonce added to query.
 */
function seedprod_lite_get_plugins_install_url( $slug ) {
	$action = 'install-plugin';
	$url    = wp_nonce_url(
		add_query_arg(
			array(
				'action' => $action,
				'plugin' => $slug,
			),
			admin_url( 'update.php' )
		),
		$action . '_' . $slug
	);

	return $url;
}

/**
 * Get plugins activate URL.
 *
 * @param string $slug Plugin slug.
 * @return string $url URL with wp_nonce added to query.
 */
function seedprod_lite_get_plugins_activate_url( $slug ) {
	$url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . rawurlencode( $slug ), 'activate-plugin_' . $slug );
	return $url;
}

/**
 * Get Open AI Users Credits
 *
 * @return array $result An array of remaining open ai credits of users
 */
function seedprod_lite_get_ai_credits() {

		$seedprod_api_key = seedprod_lite_get_api_key();
		$api_key          = $seedprod_api_key;
		$token            = get_option( 'seedprod_token' );
		$api_token        = get_option( 'seedprod_api_token' );

		$data = array(
			'api_token' => $api_token,
			'api_key'   => $api_key,
			'token'     => $token,
		);

		$headers = array(
			'Accept'        => 'application/json',
			'Authorization' => 'Bearer ' . $api_token,
		);

		$url = SEEDPROD_API_URL . 'openaicredits';

		try {

			$response = wp_remote_post(
				$url,
				array(
					'body'      => wp_json_encode( $data ),
					'headers'   => $headers,
					'sslverify' => false,
					'timeout'   => 60,
				)
			);

			if ( is_wp_error( $response ) ) {

				$curl_error = $response->get_error_code();
				if ( 'http_request_failed' === $curl_error ) {
					$result = array( 'error' => __( 'cURL error:', 'coming-soon' ) . $response->get_error_message() );
				} else {
					$result = array( 'error' => $response->get_error_message() );
				}
			} else {
				$http_status = wp_remote_retrieve_response_code( $response );
				if ( 200 === $http_status ) {
					$response_body = wp_remote_retrieve_body( $response );
					$result_data   = json_decode( $response_body, true );

					if ( null === $result_data && json_last_error() !== JSON_ERROR_NONE ) {
						$result = array( 'error' => __( 'Invalid JSON response', 'coming-soon' ) );
					} else {
						$result = $result_data;
					}
				} else {
					// Request timeout error.
					$result = array( 'error' => __( 'Server error or request timeout. Try again later.', 'coming-soon' ) );
				}
			}
		} catch ( Exception $e ) {
			$result = array( 'error' => __( 'Server error or request timeout. Try again later.', 'coming-soon' ) );
		}

		return $result;
}
