<?php
/**
 * UserRegistration UR_AJAX
 *
 * AJAX Event Handler
 *
 * @package UserRegistration/Classes
 * @class   UR_AJAX
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * UR_AJAX Class
 */
class UR_AJAX {

	/**
	 * Field key array
	 *
	 * @var array
	 */
	private static $field_key_aray = array();
	/**
	 * Check whether is field key pass
	 *
	 * @var bool
	 */
	private static $is_field_key_pass = true;
	/**
	 * Field key value
	 *
	 * @var array
	 */
	private static $failed_key_value = array();

	/**
	 * Initialization of ajax.
	 */
	public static function init() {
		self::add_ajax_events();
	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax)
	 */
	public static function add_ajax_events() {
		$ajax_events = array(
			'user_input_dropped'                => true,
			'user_form_submit'                  => true,
			'update_profile_details'            => true,
			'profile_pic_upload'                => true,
			'ajax_login_submit'                 => true,
			'send_test_email'                   => false,
			'create_form'                       => false,
			'rated'                             => false,
			'dashboard_widget'                  => false,
			'dismiss_notice'                    => false,
			'import_form_action'                => false,
			'template_licence_check'            => false,
			'captcha_setup_check'               => false,
			'install_extension'                 => false,
			'profile_pic_remove'                => false,
			'form_save_action'                  => false,
			'login_settings_save_action'        => false,
			'embed_form_action'                 => false,
			'embed_page_list'                   => false,
			'allow_usage_dismiss'               => false,
			'cancel_email_change'               => false,
			'email_setting_status'              => false,
			'locked_form_fields_notice'         => false,
			'search_global_settings'            => false,
			'php_notice_dismiss'                => false,
			'locate_form_action'                => false,
			'form_preview_save'                 => false,
			'captcha_test'                      => false,
			'generate_row_settings'             => false,
			'my_account_selection_validator'    => false,
			'lost_password_selection_validator' => false,
			'save_payment_settings'             => false
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_user_registration_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_user_registration_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * Triggered when admin search for the global settings.
	 */
	public static function search_global_settings() {
		check_ajax_referer( 'user_registration_search_global_settings', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( - 1 );
		}
		UR_Admin_Settings::search_settings();
	}

	/**
	 * Triggered when clicking the allow usage notice allow or deny buttons.
	 */
	public static function allow_usage_dismiss() {
		check_ajax_referer( 'allow_usage_nonce', '_wpnonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( - 1 );
		}

		$allow_usage_tracking = isset( $_POST['allow_usage_tracking'] ) ? sanitize_text_field( wp_unslash( $_POST['allow_usage_tracking'] ) ) : false;

		update_option( 'user_registration_allow_usage_notice_shown', true );

		if ( ur_string_to_bool( $allow_usage_tracking ) ) {
			update_option( 'user_registration_allow_usage_tracking', true );
		} else {
			update_option( 'user_registration_allow_usage_tracking', false );
		}

		wp_die();
	}

	/**
	 * Get Post data on frontend form submit
	 *
	 * @return void
	 */
	public static function user_form_submit() {
		/**
		 * Filter to modify user capability.
		 * Default value is 'create_users'.
		 */
		$logger = ur_get_logger();
		$logger->info( __( 'Checking permissions.', 'user-registration' ), array( 'source' => 'form-submission' ) );
		$current_user_capability = apply_filters( 'ur_registration_user_capability', 'create_users' );

		if ( is_user_logged_in() && ! current_user_can( 'administrator' ) && ! current_user_can( $current_user_capability ) ) { //phpcs:ignore
			$logger->warning( __( 'User is already logged in and lacks permission.', 'user-registration' ), array( 'source' => 'form-submission' ) );
			wp_send_json_error(
				array(
					'message' => __( 'You are already logged in.', 'user-registration' ),
				)
			);
		}

		if ( ! check_ajax_referer( 'user_registration_form_data_save_nonce', 'security', false ) ) {
			$logger->error( __( 'Nonce verification failed.', 'user-registration' ), array( 'source' => 'form-submission' ) );
			wp_send_json_error(
				array(
					'message' => __( 'Nonce error, please reload.', 'user-registration' ),
				)
			);
		}

		$form_id = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
		$logger->info( __( 'Processing form submission.', 'user-registration' ), array(
			'source'  => 'form-submission',
			'form_id' => $form_id
		) );
		$nonce               = isset( $_POST['ur_frontend_form_nonce'] ) ? wp_unslash( sanitize_key( $_POST['ur_frontend_form_nonce'] ) ) : '';
		$captcha_response    = isset( $_POST['captchaResponse'] ) ? ur_clean( wp_unslash( $_POST['captchaResponse'] ) ) : ''; //phpcs:ignore
		$flag                = wp_verify_nonce( $nonce, 'ur_frontend_form_id-' . $form_id );
		$recaptcha_enabled   = ur_string_to_bool( ur_get_form_setting_by_key( $form_id, 'user_registration_form_setting_enable_recaptcha_support', false ) );
		$recaptcha_type      = get_option( 'user_registration_captcha_setting_recaptcha_version', 'v2' );
		$recaptcha_type      = ur_get_single_post_meta( $form_id, 'user_registration_form_setting_configured_captcha_type', $recaptcha_type );
		$invisible_recaptcha = ur_option_checked( 'user_registration_captcha_setting_invisible_recaptcha_v2', false );

		if ( 'v2' === $recaptcha_type && ! $invisible_recaptcha ) {
			$site_key   = get_option( 'user_registration_captcha_setting_recaptcha_site_key' );
			$secret_key = get_option( 'user_registration_captcha_setting_recaptcha_site_secret' );
		} elseif ( 'v2' === $recaptcha_type && $invisible_recaptcha ) {
			$site_key   = get_option( 'user_registration_captcha_setting_recaptcha_invisible_site_key' );
			$secret_key = get_option( 'user_registration_captcha_setting_recaptcha_invisible_site_secret' );
		} elseif ( 'v3' === $recaptcha_type ) {
			$site_key   = get_option( 'user_registration_captcha_setting_recaptcha_site_key_v3' );
			$secret_key = get_option( 'user_registration_captcha_setting_recaptcha_site_secret_v3' );
		} elseif ( 'hCaptcha' === $recaptcha_type ) {
			$site_key   = get_option( 'user_registration_captcha_setting_recaptcha_site_key_hcaptcha' );
			$secret_key = get_option( 'user_registration_captcha_setting_recaptcha_site_secret_hcaptcha' );
		} elseif ( 'cloudflare' === $recaptcha_type ) {
			$site_key   = get_option( 'user_registration_captcha_setting_recaptcha_site_key_cloudflare' );
			$secret_key = get_option( 'user_registration_captcha_setting_recaptcha_site_secret_cloudflare' );
		}
		if ( $recaptcha_enabled && ! empty( $site_key ) && ! empty( $secret_key ) ) {
			if ( ! empty( $captcha_response ) ) {
				if ( 'hCaptcha' === $recaptcha_type ) {
					$data = wp_safe_remote_get( 'https://hcaptcha.com/siteverify?secret=' . $secret_key . '&response=' . $captcha_response );
					$data = json_decode( wp_remote_retrieve_body( $data ) );
					/**
					 * Filter to modify hcaptcha threshold.
					 * Default value is 0.5
					 */
					if ( empty( $data->success ) || ( isset( $data->score ) && $data->score < apply_filters( 'user_registration_hcaptcha_threshold', 0.5 ) ) ) {
						$logger->error( __( 'Error on hCaptcha.', 'user-registration' ), array( 'source' => 'form-submission' ) );
						wp_send_json_error(
							array(
								'message' => __( 'Error on hCaptcha. Contact your site administrator.', 'user-registration' ),
							)
						);
					}
				} elseif ( 'cloudflare' === $recaptcha_type ) {
					$url    = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
					$params = array(
						'method' => 'POST',
						'body'   => array(
							'secret'   => $secret_key,
							'response' => $captcha_response,
						),
					);
					$data   = wp_safe_remote_post( $url, $params );
					$data   = json_decode( wp_remote_retrieve_body( $data ) );
					if ( empty( $data->success ) ) {
						$logger->error( __( 'Error on Cloudflare Turnstile', 'user-registration' ), array( 'source' => 'form-submission' ) );
						wp_send_json_error(
							array(
								'message' => __( 'Error on Cloudflare Turnstile. Contact your site administrator.', 'user-registration' ),
							)
						);
					}
				} else {
					$data = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $captcha_response );
					$data = json_decode( wp_remote_retrieve_body( $data ) );
					/**
					 * Filter to modify V3 recaptcha threshold.
					 * Default value is 0.5
					 */
					if ( empty( $data->success ) || ( isset( $data->score ) && $data->score < apply_filters( 'user_registration_recaptcha_v3_threshold', 0.5 ) ) ) {
						$logger->error( __( 'Error on google reCaptcha.', 'user-registration' ), array( 'source' => 'form-submission' ) );
						wp_send_json_error(
							array(
								'message' => __( 'Error on google reCaptcha. Contact your site administrator.', 'user-registration' ),
							)
						);
					}
				}
			} else {
				$logger->error( __( 'Captcha code error.', 'user-registration' ), array( 'source' => 'form-submission' ) );
				wp_send_json_error(
					array(
						'message' => get_option( 'user_registration_form_submission_error_message_recaptcha', __( 'Captcha code error, please try again.', 'user-registration' ) ),
					)
				);
			}
		}

		if ( true != $flag || is_wp_error( $flag ) ) {
			$logger->error( __( 'Nonce error, please reload.', 'user-registration' ), array( 'source' => 'form-submission' ) );
			wp_send_json_error(
				array(
					'message' => __( 'Nonce error, please reload.', 'user-registration' ),
				)
			);
		}
		/**
		 * Filter to override the register settings.
		 * Default value is the get_option('users_can_register')
		 */
		$users_can_register = apply_filters( 'ur_register_setting_override', get_option( 'users_can_register' ) );

		if ( ! is_user_logged_in() ) {
			if ( ! $users_can_register ) {
				$logger->error( __( 'Only administrators can add new users.', 'user-registration' ), array( 'source' => 'form-submission' ) );
				wp_send_json_error(
					array(
						/**
						 * Filter to modify register pre form message.
						 * Default value is the 'Only administrators can add new users'.
						 */
						'message' => apply_filters( 'ur_register_pre_form_message', __( 'Only administrators can add new users.', 'user-registration' ) ),
					)
				);
			}
		} else {
			/**
			 * Filter to modify user capability.
			 * Default value is 'create_users'.
			 */
			$current_user_capability = apply_filters( 'ur_registration_user_capability', 'create_users' );

			if ( ! current_user_can( $current_user_capability ) ) {
				global $wp;

				$user_ID      = get_current_user_id();
				$user         = get_user_by( 'ID', $user_ID );
				$current_url  = home_url( add_query_arg( array(), $wp->request ) );
				$display_name = ! empty( $user->data->display_name ) ? $user->data->display_name : $user->data->user_email;

				wp_send_json_error(
					array(
						/**
						 * Filter to modify register pre form message.
						 */
						'message' => apply_filters(
							'ur_register_pre_form_message',
							'<p class="alert" id="ur_register_pre_form_message">' .
							/* translators: %1$1s - Link to logout. */
							sprintf( __( 'You are currently logged in as %1$1s. %2$2s', 'user-registration' ), '<a href="#" title="' . $display_name . '">' . $display_name . '</a>', '<a href="' . wp_logout_url( $current_url ) . '" title="' . __( 'Log out of this account.', 'user-registration' ) . '">' . __( 'Logout', 'user-registration' ) . '  &raquo;</a>' ) . '</p>',
							$user_ID
						),
					)
				);
			}
		}

		$form_data = array();
		$logger->info( __( 'Form data receiving', 'user-registration' ), array( 'source' => 'form-submission' ) );
		if ( isset( $_POST['form_data'] ) ) {
			$form_data = json_decode( wp_unslash( $_POST['form_data'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		}
		$logger->info( __( 'Form data received', 'user-registration' ), array( 'source' => 'form-submission' ) );
		UR_Frontend_Form_Handler::handle_form( $form_data, $form_id );
		$logger->info( __( 'Form submission processed successfully.', 'user-registration' ), array( 'source' => 'form-submission' ) );
	}


	/**
	 * Get Post data on frontend form submit
	 *
	 * @return void
	 */
	public static function update_profile_details() {

		if ( ! check_ajax_referer( 'user_registration_profile_details_save_nonce', 'security', false ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Nonce error, please reload.', 'user-registration' ),
				)
			);
		}

		// Current user id.
		$user_id = ! empty( $_REQUEST['user_id'] ) ? absint( $_REQUEST['user_id'] ) : get_current_user_id();

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'You are not allowed to edit this user.', 'user-registration' ),
				)
			);
		}

		if ( $user_id <= 0 ) {
			return;
		}

		// Get form id of the form from which current user is registered.
		$form_id_array = get_user_meta( $user_id, 'ur_form_id' );
		$form_id       = 0;

		if ( isset( $form_id_array[0] ) ) {
			$form_id = $form_id_array[0];
		}

		// Make the schema of form data compatible with processing below.
		$form_data    = array();
		$single_field = array();

		if ( isset( $_POST['form_data'] ) ) {
			$form_data = json_decode( wp_unslash( $_POST['form_data'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			foreach ( $form_data as $data ) {
				$single_field[ $data->field_name ] = isset( $data->value ) ? $data->value : '';
				$data->field_name                  = trim( str_replace( 'user_registration_', '', $data->field_name ) );
			}
		}

		$profile = user_registration_form_data( $user_id, $form_id );

		$is_admin_user = $_POST['is_admin_user'] ?? false;
		foreach ( $profile as $key => $field ) {

			if ( ! isset( $field['type'] ) ) {
				$field['type'] = 'text';
			}
			// Unset hidden field value.
			if ( ( isset( $field['field_key'] ) && 'hidden' === $field['field_key'] ) || ( 'range' === $field['type'] && ur_string_to_bool( $field['enable_payment_slider'] ) ) ) {
				self::unset_field( $field, $profile );
			}
			if ( ! $is_admin_user && 'hidden' === $field['type'] ) {
				self::unset_field( $field, $profile );
			}
			// Get Value.
			switch ( $field['type'] ) {
				case 'checkbox':
					if ( isset( $single_field[ $key ] ) ) {
						// Serialize values fo checkbox field.
						$single_field[ $key ] = ( json_decode( $single_field[ $key ] ) !== null ) ? json_decode( $single_field[ $key ] ) : sanitize_text_field( $single_field[ $key ] );
					}
					break;
				case 'wysiwyg':
					if ( isset( $single_field[ $key ] ) ) {
						$single_field[ $key ] = sanitize_text_field( htmlentities( $single_field[ $key ] ) );
					} else {
						$single_field[ $key ] = '';
					}
					break;
				case 'signature':
					if ( isset( $single_field[ $key ] ) ) {
						$single_field[ $key ] = apply_filters( 'user_registration_process_signature_field_data', $single_field[ $key ] );
					} else {
						$single_field[ $key ] = $field['default'];
					}
					break;
				default:
					if ( 'repeater' !== $field['type'] ) {
						$single_field[ $key ] = isset( $single_field[ $key ] ) ? $single_field[ $key ] : '';
					}
					break;
			}
		}

		/**
		 * Action hook to perform validation of edit profile form.
		 *
		 * @param array $profile User profile data.
		 * @param array $form_data The form data.
		 * @param int $form_id The form ID.
		 * @param int $user_id The user id.
		 */
		do_action( 'user_registration_validate_profile_update', $profile, $form_data, $form_id, $user_id );
		/**
		 * Action after the save profile validation.
		 *
		 * @param int The user ID.
		 * @param array The profile data.
		 */
		do_action( 'user_registration_after_save_profile_validation', $user_id, $profile );

		if ( 0 === ur_notice_count( 'error' ) ) {
			$user_data = array();
			/**
			 * Filter to modify the email change confirmation.
			 * Default vallue is 'true'.
			 */
			$is_email_change_confirmation = (bool) apply_filters( 'user_registration_email_change_confirmation', true );
			$email_updated                = false;
			$pending_email                = '';
			$user                         = get_userdata( $user_id );
			/**
			 * Filter to modify the field settings.
			 *
			 * The dynamic portion of the hook name, $value->field_key.
			 *
			 * @param array $value The field value.
			 */
			$profile = apply_filters( 'user_registration_before_save_profile_details', $profile, $user_id, $form_id );

			foreach ( $profile as $key => $field ) {
				$new_key = str_replace( 'user_registration_', '', $key );

				if ( $is_email_change_confirmation && 'user_email' === $new_key ) {
					if ( $user ) {
						if ( sanitize_email( wp_unslash( $single_field[ $key ] ) ) !== $user->user_email ) {
							$email_updated = true;
							$pending_email = sanitize_email( wp_unslash( $single_field[ $key ] ) );
						}
						continue;
					}
				}

				if ( in_array( $new_key, ur_get_user_table_fields() ) ) {
					if ( 'display_name' === $new_key ) {
						$user_data['display_name'] = sanitize_text_field( ( $single_field[ $key ] ) );
					} else {
						$user_data[ $new_key ] = sanitize_text_field( $single_field[ $key ] );
					}
				} else {
					$update_key = $key;

					if ( in_array( $new_key, ur_get_registered_user_meta_fields() ) ) {
						$update_key = str_replace( 'user_', '', $new_key );
					}
					$disabled = isset( $field['custom_attributes']['disabled'] ) ? $field['custom_attributes']['disabled'] : '';

					if ( 'disabled' !== $disabled ) {
						update_user_meta( $user_id, $update_key, $single_field[ $key ] );
					}
				}
			}

			if ( count( $user_data ) > 0 ) {
				$user_data['ID'] = $user_id;
				wp_update_user( $user_data );
			}
			/**
			 * Filter to modify the profile update success message.
			 */
			$message = apply_filters( 'user_registration_profile_update_success_message', __( 'User profile updated successfully.', 'user-registration' ) );
			/**
			 * Action to modify the save profile details.
			 *
			 * @param int $user_id The user ID.
			 * @param int $form_id The form ID.
			 */
			do_action( 'user_registration_save_profile_details', $user_id, $form_id );

			$profile_pic_id = get_user_meta( $user_id, 'user_registration_profile_pic_url' );
			$profile_pic_id = ! empty( $profile_pic_id ) ? $profile_pic_id[0] : '';
			$response       = array(
				'message'        => $message,
				'profile_pic_id' => $profile_pic_id,
			);

			if ( $email_updated ) {
				UR_Form_Handler::send_confirmation_email( $user, $pending_email, $form_id );
				$response['oldUserEmail'] = $user->user_email;
				/* translators: %s : user email */
				$response['userEmailUpdateMessage'] = sprintf( __( 'Your email address has not been updated yet. Please check your inbox at <strong>%s</strong> for a confirmation email.', 'user-registration' ), $pending_email );

				$cancel_url = esc_url(
					add_query_arg(
						array(
							'cancel_email_change' => $user_id,
							'_wpnonce'            => wp_create_nonce( 'cancel_email_change_nonce' ),
						),
						ur_get_my_account_url() . get_option( 'user_registration_myaccount_edit_profile_endpoint', 'edit-profile' )
					)
				);

				$response['userEmailPendingMessage'] = sprintf(
				/* translators: %s - Email Change Pending Message. */
					'<div class="email-updated inline"><p>%s</p></div>',
					sprintf(
					/* translators: 1: Pending email message 2: Cancel Link */
						__( 'There is a pending change of your email to <code>%1$s</code>. <a href="%2$s">Cancel</a>', 'user-registration' ),
						$pending_email,
						$cancel_url
					)
				);
			}
			/**
			 * Filter to modify profile update response.
			 *
			 * @param array $response The profile update response.
			 */
			$response = apply_filters( 'user_registration_profile_update_response', $response );

			wp_send_json_success(
				$response
			);
		} else {
			$errors = ur_get_notices( 'error' );
			ur_clear_notices();
			wp_send_json_error(
				array(
					'message' => $errors,
				)
			);
		}
	}

	public static function unset_field( $field, $profile ) {
		$key = array_search( $field, $profile, true );
		if ( false !== ( $key ) ) {
			unset( $profile[ $key ] );
		}
	}

	/**
	 * Get Post data on frontend form submit
	 *
	 * @return void
	 */
	public static function profile_pic_upload() {

		check_ajax_referer( 'user_registration_profile_picture_upload_nonce', 'security' );

		$nonce = isset( $_REQUEST['security'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['security'] ) ) : false;

		$flag = wp_verify_nonce( $nonce, 'user_registration_profile_picture_upload_nonce' );

		if ( true != $flag || is_wp_error( $flag ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Nonce error, please reload.', 'user-registration' ),
				)
			);
		}
		$user_id = get_current_user_id();

		if ( $user_id <= 0 ) {
			return;
		}

		if ( isset( $_FILES['file']['size'] ) && wp_unslash( sanitize_key( $_FILES['file']['size'] ) ) ) {
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				include_once ABSPATH . 'wp-admin/includes/file.php';
			}

			$upload = isset( $_FILES['file'] ) ? $_FILES['file'] : array(); // phpcs:ignore

			// valid extension for image.
			$valid_extensions = 'image/jpeg,image/gif,image/png';
			$form_id          = ur_get_form_id_by_userid( $user_id );

			if ( class_exists( 'UserRegistrationAdvancedFields' ) ) {
				$field_data       = ur_get_field_data_by_field_name( $form_id, 'profile_pic_url' );
				$valid_extensions = isset( $field_data['advance_setting']->valid_file_type ) ? implode( ', ', $field_data['advance_setting']->valid_file_type ) : $valid_extensions;
			}

			$valid_extension_type = explode( ',', $valid_extensions );
			$valid_ext            = array();

			foreach ( $valid_extension_type as $key => $value ) {
				$image_extension   = explode( '/', $value );
				$valid_ext[ $key ] = isset( $image_extension[1] ) ? $image_extension[1] : '';

				if ( 'jpeg' === $valid_ext[ $key ] ) {
					$index               = count( $valid_extension_type );
					$valid_ext[ $index ] = 'jpg';
				}
			}

			$src_file_name  = isset( $upload['name'] ) ? $upload['name'] : '';
			$file_extension = strtolower( pathinfo( $src_file_name, PATHINFO_EXTENSION ) );
			$file_mime_type = isset( $upload['tmp_name'] ) ? mime_content_type( $upload['tmp_name'] ) : '';

			if ( ! in_array( $file_mime_type, $valid_extension_type ) ) {
				wp_send_json_error(
					array(
						'message' => __( 'Invalid file type, please contact with site administrator.', 'user-registration' ),
					)
				);
			}
			// Validates if the uploaded file has the acceptable extension.
			if ( ! in_array( $file_extension, $valid_ext ) ) {
				wp_send_json_error(
					array(
						'message' => __( 'Invalid file type, please contact with site administrator.', 'user-registration' ),
					)
				);
			}

			$upload_path = ur_get_tmp_dir();

			// Checks if the upload directory has the write premission.
			if ( ! wp_is_writable( $upload_path ) ) {
				wp_send_json_error(
					array(
						'message' => __( 'Upload path permission deny.', 'user-registration' ),
					)
				);
			}
			$upload_path = $upload_path . '/';
			$file_name   = wp_unique_filename( $upload_path, $upload['name'] );
			$file_path   = $upload_path . sanitize_file_name( $file_name );
			if ( move_uploaded_file( $upload['tmp_name'], $file_path ) ) {
				$files = array(
					'file_name'      => $file_name,
					'file_path'      => $file_path,
					'file_extension' => $file_extension,
				);

				$attachment_id = wp_rand();

				ur_clean_tmp_files();
				$url = UR_UPLOAD_URL . 'temp-uploads/' . sanitize_file_name( $file_name );
				wp_send_json_success(
					array(
						'attachment_id' => $attachment_id,
						'upload_files'  => crypt_the_string( maybe_serialize( $files ), 'e' ),
						'url'           => $url,
					)
				);
			} else {
				wp_send_json_error(
					array(
						'message' => __( 'File cannot be uploaded.', 'user-registration' ),
					)
				);
			}
		} elseif ( isset( $_FILES['file']['error'] ) && UPLOAD_ERR_NO_FILE !== $_FILES['file']['error'] ) {
			switch ( $_FILES['file']['error'] ) {
				case UPLOAD_ERR_INI_SIZE:
					wp_send_json_error(
						array(
							'message' => __( 'File size exceed, please check your file size.', 'user-registration' ),
						)
					);
					break;
				default:
					wp_send_json_error(
						array(
							'message' => __( 'Something went wrong while uploading, please contact your site administrator.', 'user-registration' ),
						)
					);
					break;
			}
		}
	}

	/**
	 * Login from Using Ajax
	 */
	public static function ajax_login_submit() {

		check_ajax_referer( 'ur_login_form_save_nonce', 'security' );

		$nonce = isset( $_REQUEST['security'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['security'] ) ) : false;
		$flag  = wp_verify_nonce( $nonce, 'ur_login_form_save_nonce' );

		if ( false === $flag || is_wp_error( $flag ) ) {

			wp_send_json_error(
				array(
					'message' => esc_html__( 'Nonce error, please reload.', 'user-registration' ),
				)
			);
		}

		ur_process_login( $nonce );
	}

	/**
	 * Send test email.
	 *
	 * @since 1.9.9
	 */
	public static function send_test_email() {
		check_ajax_referer( 'test_email_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to send test email.', 'user-registration' ) ) );
			wp_die( - 1 );
		}
		/**
		 * Filter to test mail from name.
		 * Default value is get_option('user_registration_email_from_name').
		 */
		$from_name = apply_filters( 'wp_mail_from_name', get_option( 'user_registration_email_from_name', esc_attr( get_bloginfo( 'name', 'display' ) ) ) );
		do_action( 'user_registration_email_send_before' );

		/**
		 * Filter to test mail from address.
		 * Default value is get_option('user_registration_email_from_address').
		 */
		$sender_email = apply_filters( 'wp_mail_from', get_option( 'user_registration_email_from_address', get_option( 'admin_email' ) ) );
		$email        = sanitize_email( isset( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification
		/* translators: %s - WP mail from name */
		$subject = 'User Registration & Membership: ' . sprintf( esc_html__( 'Test email from %s', 'user-registration' ), $from_name );
		$header  = array(
			'From:' . $from_name . ' <' . $sender_email . '>',
			'Reply-To:' . $sender_email,
			'Content-Type:text/html; charset=UTF-8',
		);
		$message =
			'Congratulations,<br>
		Your test email has been received successfully.<br>
		We thank you for trying out User Registration & Membership and joining our mission to make sure you get your emails delivered.<br>
		Regards,<br>
		User Registration & Membership Team';

		$status = wp_mail( $email, $subject, $message, $header );

		if ( $status ) {
			wp_send_json_success( array( 'message' => __( 'Test email was sent successfully! Please check your inbox to make sure it is delivered.', 'user-registration' ) ) );
		}
		{
			$error_message = apply_filters( 'user_registration_email_send_failed_message', '' );
			wp_send_json_error( array( 'message' => sprintf( __( 'Test email was unsuccessful!. %s', 'user-registration' ), $error_message ) ) );
		}
	}

	/**
	 * Locate form.
	 */
	public static function locate_form_action() {
		global $wpdb;
		try {
			check_ajax_referer( 'process-locate-ajax-nonce', 'security' );
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => __( 'You do not have permission.', 'user-registration' ) ) );
				wp_die( - 1 );
			}
			$id                          = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
			$user_registration_shortcode = '%[user_registration_form id="' . $id . '"%';
			$form_id_shortcode           = '%{"formId":"' . $id . '"%';
			$pages                       = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}posts WHERE post_content LIKE %s OR post_content LIKE %s", $user_registration_shortcode, $form_id_shortcode ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$page_list                   = array();
			foreach ( $pages as $page ) {
				if ( '0' === $page->post_parent ) {
					$page_title               = $page->post_title;
					$page_guid                = $page->guid;
					$page_list[ $page_title ] = $page_guid;
				}
			}
			wp_send_json_success( $page_list );
		} catch ( Exception $e ) {
			wp_send_json_error(
				array(
					'message' => $e->getMessage(),
				)
			);
		}
	}

	/**
	 * Get form settings theme styles
	 */
	public static function form_preview_save() {
		check_ajax_referer( 'ur_form_preview_nonce', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission.', 'user-registration' ) ) );
			wp_die( - 1 );
		}
		$form_id = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
		$theme   = isset( $_POST['theme'] ) ? sanitize_text_field( $_POST['theme'] ) : '';

		if ( empty( $form_id ) || empty( $theme ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient information', 'user-registration' ) ) );
		}

		$default_theme = ( 'default' === $theme ) ? 'default' : 'theme';
		update_post_meta( $form_id, 'user_registration_enable_theme_style', $default_theme );

		wp_send_json_success( array( 'message' => __( 'Saved', 'user-registration' ) ) );
	}

	/**
	 * User input dropped function
	 *
	 * @throws Exception Throws If Empty Form Data.
	 */
	public static function user_input_dropped() {

		try {
			check_ajax_referer( 'user_input_dropped_nonce', 'security' );

			$form_field_id = ( isset( $_POST['form_field_id'] ) ) ? $_POST['form_field_id'] : null; //phpcs:ignore

			if ( null == $form_field_id || '' == $form_field_id ) {
				throw new Exception( 'Empty form data' );
			}

			$class_file_name = str_replace( 'user_registration_', '', $form_field_id );
			$class_name      = ur_load_form_field_class( $class_file_name );

			if ( empty( $class_name ) ) {
				throw new Exception( 'class not exists' );
			}

			$templates = $class_name::get_instance()->get_admin_template();

			wp_send_json_success( $templates );
		} catch ( Exception $e ) {
			wp_send_json_error(
				array(
					'error' => $e->getMessage(),
				)
			);
		}
	}

	/**
	 * Import Form ajax.
	 *
	 * @throws Exception Post data mot set.
	 */
	public static function import_form_action() {
		try {
			check_ajax_referer( 'ur_import_form_save_nonce', 'security' );
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => __( 'You do not have permission.', 'user-registration' ) ) );
				wp_die( - 1 );
			}
			UR_Admin_Import_Export_Forms::import_form();
		} catch ( Exception $e ) {
			wp_send_json_error(
				array(
					'message' => $e->getMessage(),
				)
			);
		}
	}

	/**
	 * Form save from backend
	 *
	 * @return void
	 *
	 * @throws Exception Throw if any issue while saving form data.
	 */
	public static function form_save_action() {
		$logger = ur_get_logger();
		try {
			check_ajax_referer( 'ur_form_save_nonce', 'security' );
			// Check permissions.
			$logger->info(
				__( 'Checking permissions.', 'user-registration' ),
				array( 'source' => 'form-save' )
			);
			if ( ! current_user_can( 'manage_options' ) ) {
				$logger->critical(
					__( 'You do not have permission.', 'user-registration' ),
					array( 'source' => 'form-save' )
				);
				throw new Exception( __( "You don't have enough permission to perform this task. Please contact the Administrator.", 'user-registration' ) );
			}

			$logger->info( 'Validating post data.', array( 'source' => 'form-save' ) );

			if ( ! isset( $_POST['data'] ) || ( isset( $_POST['data'] ) && gettype( wp_unslash( $_POST['data'] ) ) != 'array' ) ) { //phpcs:ignore
				throw new Exception( __( 'post data not set', 'user-registration' ) );
			} elseif ( ! isset( $_POST['data']['form_data'] )
			           || ( isset( $_POST['data']['form_data'] )
			                && gettype( wp_unslash( $_POST['data']['form_data'] ) ) != 'string' ) ) { //phpcs:ignore
				$logger->critical(
					__( 'post data not set', 'user-registration' ),
					array( 'source' => 'form-save' )
				);
				throw new Exception( __( 'post data not set', 'user-registration' ) );
			}
			$logger->info( 'Decoding and processing form data.', array( 'source' => 'form-save' ) );
			$post_data = json_decode( wp_unslash( $_POST['data']['form_data'] ) ); //phpcs:ignore
			self::sweep_array( $post_data );

			if ( isset( self::$failed_key_value['value'] ) && '' != self::$failed_key_value['value'] ) {
				if ( in_array( self::$failed_key_value['value'], self::$field_key_aray ) ) {
					$logger->critical(
						sprintf(
							"Could not save form. Duplicate field name <span>%s</span>. Context: %s",
							self::$failed_key_value['value'],
							'user_registration'
						),
						array( 'source' => 'form-save' )
					);
					throw new Exception( sprintf( "Could not save form. Duplicate field name <span style='color:red'>%s</span>", self::$failed_key_value['value'] ) );
				}
			}

			if ( false === self::$is_field_key_pass ) {
				$logger->critical(
					__( 'Could not save form. Invalid field name. Please check all field name', 'user-registration' ),
					array( 'source' => 'form-save' )
				);
				throw new Exception( __( 'Could not save form. Invalid field name. Please check all field name', 'user-registration' ) );
			}
			$logger->info( 'Validating required fields.', array( 'source' => 'form-save' ) );
			$required_fields = array(
				'user_email',
				'user_pass',
			);

			// check captcha configuration before form save action.
			if ( isset( $_POST['data']['form_setting_data'] ) ) {
				foreach ( wp_unslash( $_POST['data']['form_setting_data'] ) as $setting_data ) { //phpcs:ignore
					if ( 'user_registration_form_setting_enable_recaptcha_support' === $setting_data['name'] && ur_string_to_bool( $setting_data['value'] ) && ! ur_check_captch_keys( 'register', $_POST['data']['form_id'], true ) ) {
						$logger->critical(
							__( 'Captcha error', 'user-registration' ),
							array( 'source' => 'form-save' )
						);
						throw new Exception(
							sprintf(
							/* translators: %s - Integration tab url */
								'%s <a href="%s" class="ur-captcha-error" rel="noreferrer noopener" target="_blank">here</a> to add them and save your form.',
								esc_html__( 'Seems like you are trying to enable the captcha feature, but the captcha keys are empty. Please click', 'user-registration' ),
								esc_url( admin_url( 'admin.php?page=user-registration-settings&tab=captcha' ) ) ) ); //phpcs:ignore
					}

					if ( 'user_registration_pro_auto_password_activate' === $setting_data['name'] && ur_string_to_bool( $setting_data['value'] ) ) {
						unset( $required_fields[ array_search( 'user_pass', $required_fields ) ] );
					}
				}
			}

			$contains_search = count( array_intersect( $required_fields, self::$field_key_aray ) ) == count( $required_fields );

			if ( false === $contains_search ) {
				$logger->critical(
					__( 'Required fields are required', 'user-registration' ),
					array( 'source' => 'form-save' )
				);
				throw  new Exception( __( 'Could not save form, ' . join( ', ', $required_fields ) . ' fields are required.! ', 'user-registration' ) ); //phpcs:ignore
			}
			$logger->info( __( 'Saving form data.', 'user-registration' ), array( 'source' => 'form-save' ) );
			/**
			 * Perform validation before form save from form builder.
			 */
			do_action( 'user_registration_admin_backend_validation_before_form_save' );

			$form_name     = sanitize_text_field( $_POST['data']['form_name'] ); //phpcs:ignore
			$form_row_ids  = sanitize_text_field( $_POST['data']['form_row_ids'] ); //phpcs:ignore
			$form_id       = sanitize_text_field( $_POST['data']['form_id'] ); //phpcs:ignore
			$form_row_data = sanitize_text_field( $_POST['data']['row_data'] );

			$post_data = array(
				'post_type'      => 'user_registration',
				'post_title'     => sanitize_text_field( $form_name ),
				'post_content'   => wp_json_encode( $post_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ),
				'post_status'    => 'publish',
				'comment_status' => 'closed',   // if you prefer.
				'ping_status'    => 'closed',      // if you prefer.
			);

			if ( $form_id > 0 && is_numeric( $form_id ) ) {
				$post_data['ID'] = $form_id;
			}

			remove_filter( 'content_save_pre', 'wp_targeted_link_rel' );

			$post_id = wp_insert_post( wp_slash( $post_data ) );

			if ( $post_id > 0 ) {
				$_POST['data']['form_id'] = $post_id; // Form id for new form.

				$post_data_setting = isset( $_POST['data']['form_setting_data'] ) ? $_POST['data']['form_setting_data'] : array(); //phpcs:ignore

				if ( isset( $_POST['data']['form_restriction_submit_data'] ) && ! empty( $_POST['data']['form_restriction_submit_data'] ) ) {
					array_push(
						$post_data_setting,
						array(
							'name'  => 'urfr_qna_restriction_data',
							'value' => sanitize_text_field( wp_unslash( $_POST['data']['form_restriction_submit_data'] ) ),
						)
					);
				}

				ur_update_form_settings( $post_data_setting, $post_id );

				// Form row_id save.
				update_post_meta( $form_id, 'user_registration_form_row_ids', $form_row_ids );

				// Form row_data save.
				update_post_meta( $form_id, 'user_registration_form_row_data', $form_row_data );
			}
			/**
			 * Action after form setting save.
			 * Default is the $_POST['data'].
			 */
			do_action( 'user_registration_after_form_settings_save', wp_unslash( $_POST['data'] ) ); //phpcs:ignore
			$logger->info( __( 'Form successfully saved.', 'user-registration' ), array( 'source' => 'form-save' ) );
			wp_send_json_success(
				array(
					'data'    => $post_data,
					'post_id' => $post_id,
				)
			);
		} catch ( Exception $e ) {
			$logger->error( __( 'Form save failed: ' . $e->getMessage(), 'user-registration' ), array( 'source' => 'form-save' ) );
			wp_send_json_error(
				array(
					'message' => $e->getMessage(),
				)
			);
		}// End try().
	}

	public static function login_settings_save_action() {

		check_ajax_referer( 'ur_login_settings_save_nonce', 'security' );

		$settings_data = $_POST['data']['setting_data'];

		$output = array_combine(
			array_column( $settings_data, 'option' ),
			array_column( $settings_data, 'value' )
		);

		do_action( 'user_registration_validation_before_login_form_save', $output );

		if ( ur_string_to_bool( $output['user_registration_login_options_enable_recaptcha'] ) ) {
			if ( '' === $output['user_registration_login_options_configured_captcha_type'] || ! $output['user_registration_login_options_configured_captcha_type'] ) {
				wp_send_json_error(
					array(
						'message' => esc_html__( "Seems like you haven't selected the reCAPTCHA type (Configured Captcha).", 'user-registration' ),
					)
				);
			}
		}

		if ( ur_string_to_bool( $output['user_registration_login_options_prevent_core_login'] ) ) {

			if ( ( is_numeric( $output['user_registration_login_options_login_redirect_url'] ) ) && ! empty( $output['user_registration_login_options_login_redirect_url'] ) ) {
				$is_page_my_account_page = ur_find_my_account_in_page( sanitize_text_field( wp_unslash( $output['user_registration_login_options_login_redirect_url'] ) ) );
				if ( ! $is_page_my_account_page ) {
					wp_send_json_error(
						array(
							'message' => esc_html__(
								'The selected page is not a User Registration & Membership Login or My Account page.',
								'user-registration'
							),
						)
					);
				}
			} else {
				wp_send_json_error(
					array(
						'message' => esc_html__(
							'Please select a login redirection page.',
							'user-registration'
						),
					)
				);
			}
		}

		foreach ( $output as $key => $settings ) {
			update_option( $key, $settings );
		}

		/**
		 * Action after form setting save.
		 * Default is the $_POST['data'].
		 */
		do_action( 'user_registration_after_login_form_settings_save', wp_unslash( $settings_data ) ); //phpcs:ignore

		wp_send_json_success(
			array()
		);
	}

	/**
	 * Get all pages for embed form form builder to page.
	 *
	 * @since 4.3.0
	 */
	public static function embed_page_list() {
		check_ajax_referer( 'ur_embed_page_list_nonce', 'security' );
		$args  = array(
			'post_status' => 'publish',
			'post_type'   => 'page',
		);
		$pages = get_pages( $args );
		wp_send_json_success( $pages );
	}

	/**
	 * Embed form action.
	 *
	 * @since 4.3.0
	 */
	public static function embed_form_action() {
		check_ajax_referer( 'ur_embed_action_nonce', 'security' );
		$page_id = empty( $_POST['page_id'] ) ? 0 : sanitize_text_field( absint( $_POST['page_id'] ) );
		$form_id = ! empty( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
		if ( empty( $page_id ) ) {
			$url             = add_query_arg( 'post_type', 'page', admin_url( 'post-new.php' ) );
			$meta            = array(
				'embed_page'       => 0,
				'embed_page_title' => ! empty( $_POST['page_title'] ) ? sanitize_text_field( wp_unslash( $_POST['page_title'] ) ) : '',
			);
			$page_url        = add_query_arg(
				array(
					'form' => 'user_registration',
				),
				esc_url_raw( $url )
			);
			$meta['form_id'] = $form_id;
			UR_Admin_Embed_Wizard::set_meta( $meta );

			wp_send_json_success( $page_url );
		} else {
			UR_Admin_Embed_Wizard::delete_meta();
			$url             = get_edit_post_link( $page_id, '' );
			$post            = get_post( $page_id );
			$pattern         = '[user_registration_form id="%d"]';
			$shortcode       = sprintf( $pattern, absint( $form_id ) );
			$updated_content = $post->post_content . "\n\n" . $shortcode;
			wp_update_post(
				array(
					'ID'           => $page_id,
					'post_content' => $updated_content,
				)
			);
			wp_send_json_success( $url );
		}
	}

	/**
	 * Dashboard Widget data.
	 *
	 * @since 1.5.8
	 */
	public static function dashboard_widget() {

		check_ajax_referer( 'dashboard-widget', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission.', 'user-registration' ) ) );
			wp_die( - 1 );
		}

		$form_id = isset( $_POST['form_id'] ) ? wp_unslash( absint( $_POST['form_id'] ) ) : 0;

		$user_report = $form_id ? ur_get_user_report( $form_id ) : array();
		$forms       = ! $form_id ? ur_get_all_user_registration_form() : array();

		wp_send_json(
			array(
				'user_report' => $user_report,
				'forms'       => $forms,
			)
		); // WPCS: XSS OK.
	}

	/**
	 * Checks if the string passes the regex
	 *
	 * @param string $value Value.
	 *
	 * @return boolean
	 */
	private static function is_regex_pass( $value ) {

		$field_regex = "/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/";

		if ( preg_match( $field_regex, $value, $match ) ) :
			if ( null !== $match && count( $match ) == 1 && $match[0] === $value ) {
				return true;
			}
		endif;

		return false;
	}

	/**
	 * Sanitize values of form field in backend
	 *
	 * @param array $array Array.
	 */
	public static function sweep_array( &$array ) {

		foreach ( $array as $key => &$value ) {
			if ( 'field_key' === $key ) {
				$field_key = $value;
			}

			if ( isset( $field_key ) && 'checkbox' === $field_key ) {
				if ( gettype( $value ) == 'object' ) {
					if ( isset( $value->options ) && is_array( $value->options ) && ! empty( $value->options ) ) {
						$sanitized_options = array();
						$allowed_tags      = array(
							array(),
							'a' => array(
								'href'      => true,
								'title'     => true,
								'target'    => true,
								'download'  => true,
								'rel'       => true,
								'hreflang'  => true,
								'type'      => true,
								'name'      => true,
								'accesskey' => true,
								'tabindex'  => true,
							),
						);

						foreach ( $value->options as $index => $option_value ) {
							$option_value = str_replace( '"', "'", $option_value );
							$option_value = wp_kses( trim( $option_value ), $allowed_tags );

							// Check if the option_value contains an open <a> tag but not a closing </a> tag.
							if ( preg_match( '/<a\s[^>]*>/i', $option_value ) && ! preg_match( '/<\/a>/i', $option_value ) ) {
								// Add a closing </a> tag to the end of the option_value.
								$option_value .= '</a>';
							}

							$sanitized_options [] = $option_value;
						}

						$value->options = $sanitized_options;
					}
				}
			} elseif ( is_array( $value ) || gettype( $value ) === 'object' ) {
				if ( isset( $value->field_key ) ) {
					/**
					 * Filter to modify the field settings.
					 *
					 * The dynamic portion of the hook name, $value->field_key.
					 *
					 * @param array $value The field value.
					 */
					$value = apply_filters( 'user_registration_field_setting_' . $value->field_key, $value );
				}
				self::sweep_array( $value );
			} else {
				if ( 'field_name' === $key ) {
					$regex_status = self::is_regex_pass( $value );

					if ( ! $regex_status || in_array( $value, self::$field_key_aray ) ) {
						self::$is_field_key_pass = false;
						self::$failed_key_value  = array(
							'key'   => $key,
							'value' => $value,
						);

						return;
					}
					array_push( self::$field_key_aray, $value );
				}
				if ( 'description' === $key ) {
					$value = str_replace( '"', "'", $value );
					$value = wp_kses(
						$value,
						array(
							'a'          => array(
								'href'   => array(),
								'title'  => array(),
								'target' => array(),
							),
							'br'         => array(),
							'em'         => array(),
							'strong'     => array(),
							'u'          => array(),
							'i'          => array(),
							'q'          => array(),
							'b'          => array(),
							'ul'         => array(),
							'ol'         => array(),
							'li'         => array(),
							'hr'         => array(),
							'blockquote' => array(),
							'del'        => array(),
							'strike'     => array(),
							'code'       => array(),
							'div'        => array(),
							'span'       => array(),
							'p'          => array(),
							'h1'         => array(),
							'h2'         => array(),
							'h3'         => array(),
							'h4'         => array(),
							'h5'         => array(),
							'h6'         => array(),
						)
					);
				} elseif ( 'html' === $key ) {
					if ( ! current_user_can( 'unfiltered_html' ) ) {
						$value = wp_kses_post( $value );
					}
				} else {
					$value = sanitize_text_field( $value );
				}
			}
		}
	}

	/**
	 * Triggered when clicking the rating footer.
	 *
	 * @since 1.1.2
	 */
	public static function rated() {
		if ( ! current_user_can( 'manage_user_registration' ) ) {
			wp_die( - 1 );
		}
		update_option( 'user_registration_admin_footer_text_rated', 1 );
		wp_die();
	}

	/**
	 * Dismiss user registration notices.
	 *
	 * @return void
	 **@since 1.5.8
	 *
	 */
	public static function dismiss_notice() {
		$notice_id   = isset( $_POST['notice_id'] ) ? wp_unslash( sanitize_key( $_POST['notice_id'] ) ) : '';   // phpcs:ignore WordPress.Security.NonceVerification
		$notice_type = isset( $_POST['notice_type'] ) ? wp_unslash( sanitize_key( $_POST['notice_type'] ) ) : '';   // phpcs:ignore WordPress.Security.NonceVerification
		check_admin_referer( $notice_type . '-nonce', 'security' );
		if ( ! empty( $_POST['dismissed'] ) ) {
			if ( ! empty( $_POST['dismiss_forever'] ) && ur_string_to_bool( sanitize_text_field( wp_unslash( $_POST['dismiss_forever'] ) ) ) ) {
				update_option( 'user_registration_' . $notice_id . '_notice_dismissed', true );
			} else {
				$notice_dismissed_temporarily = json_decode( get_option( 'user_registration_' . $notice_id . '_notice_dismissed_temporarily', '' ), true );
				$reopen_times                 = isset( $notice_dismissed_temporarily ) ? $notice_dismissed_temporarily['reopen_times'] : 0;

				$notice_data = array(
					'last_dismiss' => current_time( 'Y-m-d' ),
					'reopen_times' => $reopen_times + 1,
				);
				update_option( 'user_registration_' . $notice_id . '_notice_dismissed_temporarily', json_encode( $notice_data ) );
			}

			// Never display mail send failed notice once dismissed.
			if ( 'info_ur_email_send_failed' === $notice_id ) {
				delete_transient( 'user_registration_mail_send_failed_count' );
			}
		}
	}

	/**
	 * Remove profile picture ajax method.
	 */
	public static function profile_pic_remove() {
		check_ajax_referer( 'user_registration_profile_picture_remove_nonce', 'security' );
		$nonce = isset( $_REQUEST['security'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['security'] ) ) : false;

		$flag = wp_verify_nonce( $nonce, 'user_registration_profile_picture_remove_nonce' );

		if ( true != $flag || is_wp_error( $flag ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Nonce error, please reload.', 'user-registration' ),
				)
			);
		}

		$attachment_id = isset( $_POST['attachment_id'] ) ? intval( wp_unslash( $_POST['attachment_id'] ) ) : '';

		if ( is_user_logged_in() ) {
			$user_id             = get_current_user_id();
			$user_profile_pic_id = get_user_meta( $user_id, 'user_registration_profile_pic_url' );

			if ( $user_profile_pic_id == $attachment_id ) {

				if ( file_exists( get_attached_file( $attachment_id ) ) && ! unlink( get_attached_file( $attachment_id ) ) ) {
					wp_send_json_error(
						array(
							'message' => esc_html__( 'File cannot be removed', 'user-registration' ),
						)
					);
				}
				update_user_meta( $user_id, 'user_registration_profile_pic_url', '' );
			} else {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'File cannot be removed', 'user-registration' ),
					)
				);
			}
		} else {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'File cannot be removed', 'user-registration' ),
				)
			);

		}

		wp_send_json_success(
			array(
				'message' => __( 'User profile picture removed successfully', 'user-registration' ),
			)
		);
	}

	/**
	 * Ajax handler for licence check.
	 *
	 * @global WP_Filesystem_Base $wp_filesystem Subclass
	 */
	public static function template_licence_check() {
		check_ajax_referer( 'user_registration_template_licence_check', 'security' );

		if ( empty( $_POST['plan'] ) ) {
			wp_send_json_error(
				array(
					'plan'         => '',
					'errorCode'    => 'no_plan_specified',
					'errorMessage' => esc_html__( 'No Plan specified.', 'user-registration' ),
				)
			);
		}

		$addons        = array();
		$template_data = UR_Admin_Form_Templates::get_template_data();
		$template_data = is_array( $template_data ) ? $template_data : array();
		if ( ! empty( $template_data ) ) {
			foreach ( $template_data as $template ) {
				if ( isset( $_POST['slug'] ) && $template->slug === $_POST['slug'] && in_array( trim( $_POST['plan'] ), $template->plan, true ) ) {
					$addons = $template->addons;
				}
			}
		}

		$output = '<div class="user-registration-recommend-addons">';
		$output .= '<h3>' . esc_html__( 'This form template requires the following addons.', 'user-registration' ) . '</h3>';
		$output .= '<table class="plugins-list-table widefat striped">';
		$output .= '<thead><tr><th scope="col" class="manage-column required-plugins" colspan="2">' . esc_html__( 'Required Addons', 'user-registration' ) . '</th></tr></thead><tbody id="the-list">';
		$output .= '</div>';

		$activated = true;

		foreach ( $addons as $slug => $addon ) {
			$plugin = 'user-registration-pro' === $slug ? $slug . '/user-registration.php' : $slug . '/' . $slug . '.php';

			if ( is_plugin_active( $plugin ) ) {
				$class        = 'active';
				$parent_class = '';
			} elseif ( file_exists( WP_PLUGIN_DIR . '/' . $plugin ) ) {
				$class        = 'activate-now';
				$parent_class = 'inactive';
				$activated    = false;
			} else {
				$class        = 'install-now';
				$parent_class = 'inactive';
				$activated    = false;
			}

			$output .= '<tr class="plugin-card-' . $slug . ' plugin ' . $parent_class . '" data-slug="' . $slug . '" data-plugin="' . $plugin . '" data-name="' . $addon . '">';
			$output .= '<td class="plugin-name">' . $addon . '</td>';
			$output .= '<td class="plugin-status"><span class="' . esc_attr( $class ) . '"></span></td>';
			$output .= '</tr>';
		}
		$output .= '</tbody></table></div>';

		wp_send_json_success(
			array(
				'html'     => $output,
				'activate' => $activated,
			)
		);
	}

	/**
	 * Check for captcha setup.
	 */
	public static function captcha_setup_check() {
		check_ajax_referer( 'user_registration_captcha_setup_check', 'security' );

		if ( ur_check_captch_keys() ) {
			wp_send_json_success(
				array(
					'is_captcha_setup' => true,
				)
			);
		}

		wp_send_json_error(
			array(
				'is_captcha_setup'        => false,
				'captcha_setup_error_msg' => sprintf(
				/* translators: %s - Integration tab url */
					__( 'Seems like you haven\'t added the reCAPTCHA Keys. <a href="%s" >Add Now.</a>', 'user-registration' ),
					esc_url( admin_url( 'admin.php?page=user-registration-settings&tab=captcha' ) )
				),
			)
		);
	}

	/**
	 * Ajax handler for installing a extension.
	 *
	 * @since 1.2.0
	 *
	 * @see Plugin_Upgrader
	 *
	 * @global WP_Filesystem_Base $wp_filesystem Subclass
	 */
	public static function install_extension() {
		check_ajax_referer( 'updates' );

		if ( empty( $_POST['slug'] ) ) {
			wp_send_json_error(
				array(
					'slug'         => '',
					'errorCode'    => 'no_plugin_specified',
					'errorMessage' => esc_html__( 'No plugin specified.', 'user-registration' ),
				)
			);
		}

		$slug        = sanitize_key( wp_unslash( $_POST['slug'] ) );
		$plugin_slug = 'user-registration-pro' === $slug ? wp_unslash( $_POST['slug'] . '/user-registration.php' ) : wp_unslash( $_POST['slug'] . '/' . $_POST['slug'] . '.php' ); // phpcs:ignore
		$plugin      = plugin_basename( sanitize_text_field( $plugin_slug ) );
		$status      = array(
			'install' => 'plugin',
			'slug'    => sanitize_key( wp_unslash( $_POST['slug'] ) ),
		);

		if ( ! current_user_can( 'install_plugins' ) ) {
			$status['errorMessage'] = esc_html__( 'Sorry, you are not allowed to install plugins on this site.', 'user-registration' );
			wp_send_json_error( $status );
		}

		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

		if ( file_exists( WP_PLUGIN_DIR . '/' . $slug ) ) {
			$plugin_data          = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
			$status['plugin']     = $plugin;
			$status['pluginName'] = $plugin_data['Name'];

			if ( current_user_can( 'activate_plugin', $plugin ) && is_plugin_inactive( $plugin ) ) {
				$result = activate_plugin( $plugin );

				if ( is_wp_error( $result ) ) {
					$status['errorCode']    = $result->get_error_code();
					$status['errorMessage'] = $result->get_error_message();
					wp_send_json_error( $status );
				}

				wp_send_json_success( $status );
			}
		}

		$api = json_decode(
			UR_Updater_Key_API::version(
				array(
					'license'   => get_option( 'user-registration_license_key' ),
					'item_name' => ! empty( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
				)
			)
		);

		if ( is_wp_error( $api ) ) {
			$status['errorMessage'] = $api->get_error_message();
			wp_send_json_error( $status );
		}

		$status['pluginName'] = $api->name;

		$skin     = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$result   = $upgrader->install( $api->download_link );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$status['debug'] = $skin->get_upgrade_messages();
		}

		if ( is_wp_error( $result ) ) {
			$status['errorCode']    = $result->get_error_code();
			$status['errorMessage'] = $result->get_error_message();
			wp_send_json_error( $status );
		} elseif ( is_wp_error( $skin->result ) ) {
			$status['errorCode']    = $skin->result->get_error_code();
			$status['errorMessage'] = $skin->result->get_error_message();
			wp_send_json_error( $status );
		} elseif ( $skin->get_errors()->get_error_code() ) {
			$status['errorMessage'] = $skin->get_error_messages();
			wp_send_json_error( $status );
		} elseif ( is_null( $result ) ) {
			global $wp_filesystem;

			$status['errorCode']    = 'unable_to_connect_to_filesystem';
			$status['errorMessage'] = esc_html__( 'Unable to connect to the filesystem. Please confirm your credentials.', 'user-registration' );

			// Pass through the error from WP_Filesystem if one was raised.
			if ( $wp_filesystem instanceof WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
				$status['errorMessage'] = esc_html( $wp_filesystem->errors->get_error_message() );
			}

			wp_send_json_error( $status );
		}

		$api->version   = isset( $api->new_version ) ? $api->new_version : '';
		$install_status = install_plugin_install_status( $api );

		if ( current_user_can( 'activate_plugin', $install_status['file'] ) && is_plugin_inactive( $install_status['file'] ) ) {
			if ( isset( $_POST['page'] ) && 'user-registration-membership_page_add-new-registration' === $_POST['page'] ) {
				activate_plugin( $install_status['file'] );
			} else {
				$status['activateUrl'] =
					esc_url_raw(
						add_query_arg(
							array(
								'action'   => 'activate',
								'plugin'   => $install_status['file'],
								'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $install_status['file'] ),
							),
							admin_url( 'admin.php?page=user-registration-addons' )
						)
					);
			}
		}

		wp_send_json_success( $status );
	}

	/**
	 * AJAX create new form.
	 */
	public static function create_form() {
		ob_start();

		check_ajax_referer( 'user_registration_create_form', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to create form.', 'user-registration' ) ) );
			wp_die( - 1 );
		}

		$title    = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : esc_html__( 'Blank Form', 'user-registration' );
		$template = isset( $_POST['template'] ) ? sanitize_text_field( wp_unslash( $_POST['template'] ) ) : 'blank';

		$form_id = UR()->form->create( $title, $template );

		if ( $form_id ) {
			$data = array(
				'id'       => $form_id,
				'redirect' => add_query_arg(
					array(
						'tab'     => 'fields',
						'form_id' => $form_id,
					),
					admin_url( 'admin.php?page=add-new-registration&edit-registration=' . $form_id )
				),
			);

			wp_send_json_success( $data );
		}

		wp_send_json_error(
			array(
				'error' => esc_html__( 'Something went wrong, please try again later', 'user-registration' ),
			)
		);
	}

	/**
	 * Cancel a pending email change.
	 *
	 * @return void
	 */
	public static function cancel_email_change() {
		check_ajax_referer( 'cancel_email_change_nonce', '_wpnonce' );

		$user_id = isset( $_POST['cancel_email_change'] ) ? absint( wp_unslash( $_POST['cancel_email_change'] ) ) : false;

		if ( ! $user_id ) {
			wp_die( - 1 );
		}

		// Remove the confirmation key, pending email and expiry date.
		UR_Form_Handler::delete_pending_email_change( $user_id );

		wp_send_json_success(
			array(
				'message' => __( 'Changed email cancelled successfully.', 'user-registration' ),
			)
		);
	}

	/**
	 * Email setting status
	 */
	public static function email_setting_status() {
		$security = isset( $_POST['security'] ) ? sanitize_text_field( wp_unslash( $_POST['security'] ) ) : '';
		if ( '' === $security || ! wp_verify_nonce( $security, 'email_setting_status_nonce' ) ) {
			wp_send_json_error( 'Nonce verification failed' );

			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Permision Denied' );

			return;
		}
		$status = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : null;
		$id     = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : null;
		$value  = ur_string_to_bool( $status );
		$key    = 'user_registration_enable_' . $id;

		$option = get_option( $key, 'NO_OPTION' );
		if ( 'NO_OPTION' === $option ) {
			$status = add_option( $key, $value );
		} else {

			$status = update_option( $key, $value );
		}
		if ( $status ) {
			wp_send_json_success( 'Successfully Updated' );
		} else {
			wp_send_json_error( 'Update failed !' );
		}
	}

	/**
	 * Install or upgrade to premium.
	 */
	public static function locked_form_fields_notice() {
		$security = isset( $_POST['security'] ) ? sanitize_text_field( wp_unslash( $_POST['security'] ) ) : '';
		if ( '' === $security || ! wp_verify_nonce( $security, 'locked_form_fields_notice_nonce' ) ) {
			wp_send_json_error( 'Nonce verification failed' );

			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Permision Denied' );

			return;
		}
		$plan         = isset( $_POST['plan'] ) ? sanitize_text_field( wp_unslash( $_POST['plan'] ) ) : null;
		$slug         = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : null;
		$name         = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : null;
		$video_id     = isset( $_POST['video_id'] ) ? sanitize_text_field( wp_unslash( $_POST['video_id'] ) ) : null;
		$license_data = ur_get_license_plan();
		$button       = '';

		if ( false === $license_data ) {

			if ( is_plugin_active( 'user-registration-pro/user-registration.php' ) ) {
				$button = '<div class="action-buttons"><a class="button activate-license-now" href="' . esc_url( admin_url( 'admin.php?page=user-registration-settings&tab=license' ) ) . '" rel="noreferrer noopener" target="_blank">' . esc_html__( 'Activate License', 'user-registration' ) . '</a></div>';
				wp_send_json_success( array( 'action_button' => $button ) );
			} else {
				$button = '<div class="action-buttons"><a class="button upgrade-now" href="https://wpuserregistration.com/pricing/?utm_source=builder-fields&utm_medium=premium-field-popup&utm_campaign=' . UR()->utm_campaign . '" rel="noreferrer noopener" target="_blank">' . esc_html__( 'Upgrade Plan', 'user-registration' ) . '</a></div>';
				wp_send_json_success( array( 'action_button' => $button ) );
			}
		}
		$license_plan = ! empty( $license_data->item_plan ) ? $license_data->item_plan : false;

		$license_plan = $license_plan . ' plan';
		$license_plan = trim( $license_plan );

		if ( 'themegrill agency plan' === $license_plan || 'professional plan' === $license_plan || 'plus plan' === $license_plan ) {
			$license_plan = 'themegrill agency plan or professional plan or plus plan';
		}
		if ( strtolower( $plan ) === $license_plan ) {
			if ( 'themegrill agency plan or professional plan or plus plan' === $license_plan ) {
				$plan_list = array( 'plus', 'professional', 'personal', 'themegrill agency' );
			} else {
				$plan_list = array( 'personal' );
			}
		} elseif ( strtolower( $plan ) === 'personal plan' && 'themegrill agency plan or professional plan or plus plan' === $license_plan ) {
			$plan_list = array( 'plus', 'professional', 'personal', 'themegrill agency' );
		} else {
			$plan_list = array();
		}
		if ( $plan ) {
			$addon = (object) array(
				'title' => '',
				'slug'  => $slug,
				'name'  => $name,
				'plan'  => $plan_list,
			);
		}

		ob_start();
		/**
		 * Action after addon description.
		 *
		 * @param array $addon The addon's details.
		 */
		do_action( 'user_registration_after_addons_description', $addon );
		$button = ob_get_clean();
		wp_send_json_success( array( 'action_button' => $button ) );
	}


	/**
	 * Handle PHP Deprecated notice dismiss action.
	 *
	 * @return bool
	 */
	public static function php_notice_dismiss() {
		$current_date = gmdate( 'Y-m-d' );
		$prompt_count = get_option( 'user_registration_php_deprecated_notice_prompt_count', 0 );

		update_option( 'user_registration_php_deprecated_notice_last_prompt_date', $current_date );
		update_option( 'user_registration_php_deprecated_notice_prompt_count', ++ $prompt_count );

		return false;
	}

	/**
	 * Handle Testing for Captcha Settings.
	 *
	 * @return bool
	 */
	public static function captcha_test() {

		$security = isset( $_POST['security'] ) ? sanitize_text_field( wp_unslash( $_POST['security'] ) ) : '';
		if ( '' === $security || ! wp_verify_nonce( $security, 'user_registration_captcha_test_nonce' ) ) {
			wp_send_json_error( 'Nonce verification failed' );

			return;
		}

		$captcha_type = isset( $_POST['captcha_type'] ) ? sanitize_text_field( wp_unslash( $_POST['captcha_type'] ) ) : '';
		if ( ! get_option( 'user_registration_captcha_setting_recaptcha_enable_' . $captcha_type, false ) ) {
			wp_send_json_error( 'Please Enable the Captcha first to test and Refresh the page.' );

			return;
		}

		$ur_recaptcha_code = array();

		$invisible_recaptcha = false;
		if ( isset( $_POST['invisible_recaptcha'] ) ) {
			$invisible_recaptcha = $_POST['invisible_recaptcha'];
		}

		if ( '' === $captcha_type ) {
			wp_send_json_error( 'Captcha Test failed' );

			return;
		}

		if ( 'v2' === $captcha_type && 'false' == $invisible_recaptcha ) {
			$recaptcha_site_key    = get_option( 'user_registration_captcha_setting_recaptcha_site_key' );
			$recaptcha_site_secret = get_option( 'user_registration_captcha_setting_recaptcha_site_secret' );
		} elseif ( 'v2' === $captcha_type && 'false' != $invisible_recaptcha ) {
			$recaptcha_site_key    = get_option( 'user_registration_captcha_setting_recaptcha_invisible_site_key' );
			$recaptcha_site_secret = get_option( 'user_registration_captcha_setting_recaptcha_invisible_site_secret' );
		} elseif ( 'v3' === $captcha_type ) {
			$recaptcha_site_key    = get_option( 'user_registration_captcha_setting_recaptcha_site_key_v3' );
			$recaptcha_site_secret = get_option( 'user_registration_captcha_setting_recaptcha_site_secret_v3' );
		} elseif ( 'hCaptcha' === $captcha_type ) {
			$recaptcha_site_key    = get_option( 'user_registration_captcha_setting_recaptcha_site_key_hcaptcha' );
			$recaptcha_site_secret = get_option( 'user_registration_captcha_setting_recaptcha_site_secret_hcaptcha' );
		} elseif ( 'cloudflare' === $captcha_type ) {
			$recaptcha_site_key = get_option( 'user_registration_captcha_setting_recaptcha_site_key_cloudflare' );
			$theme_mod          = get_option( 'user_registration_captcha_setting_recaptcha_cloudflare_theme' );
		}

		$ur_recaptcha_code = array(
			'site_key'     => sanitize_text_field( $recaptcha_site_key ),
			'is_invisible' => $invisible_recaptcha,
			'theme_mode'   => isset( $theme_mod ) ? $theme_mod : '',
		);

		wp_send_json_success(
			array(
				'success'           => true,
				'captcha_type'      => $captcha_type,
				'ur_recaptcha_code' => $ur_recaptcha_code,
			)
		);
	}

	/**
	 * Handle Row settings generation.
	 *
	 * @return bool
	 */
	public static function generate_row_settings() {
		$security = isset( $_POST['security'] ) ? sanitize_text_field( wp_unslash( $_POST['security'] ) ) : '';
		if ( '' === $security || ! wp_verify_nonce( $security, 'ur_new_row_added_nonce' ) ) {
			wp_send_json_error( 'Nonce verification failed' );

			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Permision Denied' );

			return;
		}
		$form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;
		$row_id  = isset( $_POST['row_id'] ) ? intval( $_POST['row_id'] ) : 0;

		ob_start();
		echo "<div class='ur-form-row ur-individual-row-settings' data-row-id='" . esc_attr( $row_id ) . "'>";
		do_action( 'user_registration_get_row_settings', $form_id, $row_id );
		echo '</div>';
		$template = ob_get_clean();

		wp_send_json_success( $template );
	}

	/**
	 * AJAX validate selected my account page.
	 */
	public static function my_account_selection_validator() {
		check_ajax_referer( 'user_registration_my_account_selection_validator', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to edit settings form.', 'user-registration' ) ) );
			wp_die( - 1 );
		}

		// Return if default wp_login is disabled and no redirect url is set.
		if ( isset( $_POST['user_registration_selected_my_account_page'] ) ) {
			if ( is_numeric( $_POST['user_registration_selected_my_account_page'] ) ) {
				$is_page_my_account_page = ur_find_my_account_in_page( sanitize_text_field( wp_unslash( $_POST['user_registration_selected_my_account_page'] ) ) );
				if ( ! $is_page_my_account_page ) {
					wp_send_json_error(
						array(
							'message' => esc_html__(
								'The selected page is not a User Registration & Membership Login or My Account page.',
								'user-registration'
							),
						)
					);
				}
			}
		}

		wp_send_json_success();
	}

	/**
	 * AJAX validate selected lost password page.
	 */
	public static function lost_password_selection_validator() {
		check_ajax_referer( 'user_registration_lost_password_selection_validator', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to edit settings form.', 'user-registration' ) ) );
			wp_die( - 1 );
		}

		// Return if default wp_login is disabled and no redirect url is set.
		if ( isset( $_POST['user_registration_selected_lost_password_page'] ) ) {
			if ( is_numeric( $_POST['user_registration_selected_lost_password_page'] ) ) {
				$is_page_lost_password_page = ur_find_lost_password_in_page( sanitize_text_field( wp_unslash( $_POST['user_registration_selected_lost_password_page'] ) ) );

				if ( ! $is_page_lost_password_page ) {
					wp_send_json_error(
						array(
							'message' => esc_html__(
								'The selected page is not a User Registration & Membership Lost Password page.',
								'user-registration'
							),
						)
					);
				}
			}
		}

		wp_send_json_success();
	}

	public static function save_payment_settings() {
		check_ajax_referer( 'user_registration_validate_payment_settings_none', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to edit payment settings.', 'user-registration' ) ) );
			wp_die( - 1 );
		}
		if ( empty( $_POST['section_data'] ) || empty( $_POST['setting_id'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient Data', 'user-registration' ) ) );
		}

		$setting_id = sanitize_text_field( $_POST['setting_id'] );
		$form_data  = json_decode( wp_unslash( $_POST['section_data'] ), true );

		// Load payment modules to ensure filters are registered
		UR_Admin_Settings::load_payment_modules();

		$validate_before_save = apply_filters( 'urm_validate_' . $setting_id . '_payment_section_before_update', $form_data );

		if ( isset($validate_before_save['status']) && ! $validate_before_save['status'] ) {
			wp_send_json_error(
				array(
					'message' => __( $validate_before_save['message'], "user_registration" )
				)
			);
		}
		update_option('urm_'.$setting_id.'_connection_status', true);

		do_action( 'urm_save_' . $setting_id . '_payment_section', $form_data );
		$message = "payment-settings" === $setting_id ? "Settings has been saved successfully" : sprintf( __( "Payment Setting for %s has been saved successfully.", 'user-registration' ), $setting_id );
		wp_send_json_success( array(
				'message' => $message
			)
		);
	}
}

UR_AJAX::init();
