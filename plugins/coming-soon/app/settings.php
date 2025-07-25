<?php

/**
 * Save Settings: Coming Soon Mode, Maintenance Mode, Login Page, 404 Page
 */
function seedprod_lite_save_settings() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_save_settings_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error( null, 400 );
		}
		if ( ! empty( $_POST['settings'] ) ) {
			$settings = wp_unslash( $_POST['settings'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$s = json_decode( $settings );

			$s->api_key                 = sanitize_text_field( $s->api_key );
			$s->enable_coming_soon_mode = sanitize_text_field( $s->enable_coming_soon_mode );
			$s->enable_maintenance_mode = sanitize_text_field( $s->enable_maintenance_mode );
			$s->enable_login_mode       = sanitize_text_field( $s->enable_login_mode );
			$s->enable_404_mode         = sanitize_text_field( $s->enable_404_mode );

			// Get old settings to check if there has been a change
			$settings_old = get_option( 'seedprod_settings' );
			$s_old        = json_decode( $settings_old );

			// Key is for $settings, Value is for get_option()
			$settings_to_update = array(
				'enable_coming_soon_mode' => 'seedprod_coming_soon_page_id',
				'enable_maintenance_mode' => 'seedprod_maintenance_mode_page_id',
				'enable_login_mode'       => 'seedprod_login_page_id',
				'enable_404_mode'         => 'seedprod_404_page_id',
			);

			foreach ( $settings_to_update as $setting => $option ) {
				$has_changed = ( $s->$setting !== $s_old->$setting ? true : false );
				if ( ! $has_changed ) {
					continue; } // Do nothing if no change

				$id = get_option( $option );

				$post_exists = ! is_null( get_post( $id ) );
				if ( ! $post_exists ) {
					update_option( $option, null );
					continue;
				}

				$update       = array();
				$update['ID'] = $id;

				// Publish page when active
				if ( true === $s->$setting || '1' === $s->$setting ) {
					$update['post_status'] = 'publish';
					wp_update_post( $update );
				}

				// Unpublish page when inactive
				if ( false === $s->$setting ) {
					$update['post_status'] = 'draft';
					wp_update_post( $update );
				}
			}

			update_option( 'seedprod_settings', $settings );

			// Check if we should show AI website builder message (lite users only)
			$show_ai_message = false;
			
			if ( 'lite' === SEEDPROD_BUILD ) {
				// Check if Coming Soon or Maintenance Mode was toggled
				$coming_soon_changed = isset( $s->enable_coming_soon_mode ) && isset( $s_old->enable_coming_soon_mode ) && ( $s->enable_coming_soon_mode !== $s_old->enable_coming_soon_mode );
				$maintenance_changed = isset( $s->enable_maintenance_mode ) && isset( $s_old->enable_maintenance_mode ) && ( $s->enable_maintenance_mode !== $s_old->enable_maintenance_mode );
				
				if ( $coming_soon_changed || $maintenance_changed ) {
					// Check if user has already seen the AI message
					$user_id = get_current_user_id();
					$user_id = absint( $user_id ); // Validate user ID
					
					if ( $user_id > 0 ) {
						$seen_ai_message = get_user_meta( $user_id, 'seedprod_seen_ai_message', true );
						
						if ( ! $seen_ai_message ) {
							$show_ai_message = true;
							// Mark as seen
							update_user_meta( $user_id, 'seedprod_seen_ai_message', true );
						}
					}
				}
			}
			
			$response = array(
				'status' => 'true',
				'msg'    => __( 'Settings Updated', 'coming-soon' ),
				'show_ai_message' => $show_ai_message,
			);
		} else {
			$response = array(
				'status' => 'false',
				'msg'    => __( 'Error Updating Settings', 'coming-soon' ),
			);
		}

		// Send Response
		wp_send_json( $response );
		exit;
	}
}

/**
 * Save App Settings
 */
function seedprod_lite_save_app_settings() {
	if ( check_ajax_referer( 'seedprod_lite_save_app_settings' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_save_app_settings_capability', 'manage_options' ) ) ) {
			wp_send_json_error( null, 400 );
		}
		if ( ! empty( $_POST['app_settings'] ) ) {

			$app_settings = wp_unslash( $_POST['app_settings'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			// security: create new settings array so we make sure we only set/allow our settings
			$new_app_settings = array();

			// Edit Button
			if ( isset( $app_settings['disable_seedprod_button'] ) && 'true' === $app_settings['disable_seedprod_button'] ) {
				$new_app_settings['disable_seedprod_button'] = true;
				update_option( 'seedprod_allow_usage_tracking', true );
			} else {
				$new_app_settings['disable_seedprod_button'] = false;
				update_option( 'seedprod_allow_usage_tracking', false );
			}

			// Usage Tracking
			if ( isset( $app_settings['enable_usage_tracking'] ) && 'true' === $app_settings['enable_usage_tracking'] ) {
				$new_app_settings['enable_usage_tracking'] = true;
				update_option( 'seedprod_allow_usage_tracking', true );
			} else {
				$new_app_settings['enable_usage_tracking'] = false;
				update_option( 'seedprod_allow_usage_tracking', false );
			}

			// Edit Notification
			if ( isset( $app_settings['disable_seedprod_notification'] ) && 'true' === $app_settings['disable_seedprod_notification'] ) {
				$new_app_settings['disable_seedprod_notification'] = true;
			} else {
				$new_app_settings['disable_seedprod_notification'] = false;
			}

			// Facebook ID
			$new_app_settings['facebook_g_app_id']     = sanitize_text_field( $app_settings['facebook_g_app_id'] );
			$new_app_settings['google_places_app_key'] = sanitize_text_field( $app_settings['google_places_app_key'] );
			$new_app_settings['yelp_app_api_key']      = sanitize_text_field( $app_settings['yelp_app_api_key'] );
			$app_settings_encode                       = wp_json_encode( $new_app_settings );

			update_option( 'seedprod_app_settings', $app_settings_encode );
			$response = array(
				'status' => 'true',
				'msg'    => __( 'App Settings Updated', 'coming-soon' ),
			);

		} else {
			$response = array(
				'status' => 'false',
				'msg'    => __( 'Error Updating App Settings', 'coming-soon' ),
			);
		}
			// Send Response
			wp_send_json( $response );
			exit;

	}
}
