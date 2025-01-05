<?php
/**
 * Class Login_Controller
 *
 * @package AIO Login
 */

namespace AIO_Login\Login_Controller;

use AIO_Login\Helper\Helper;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'AIO_Login\\Login_Controller\\Login_Controller' ) ) {
	/**
	 * Class Login_Controller
	 */
	class Login_Controller {

		/**
		 * Limit attempts enabled.
		 *
		 * @var bool $limit_attempts_enabled Limit attempts enabled.
		 */
		private $limit_attempts_enabled;

		/**
		 * Limit attempts maximum attempts.
		 *
		 * @var int $limit_attempts_maximum_attempts Maximum attempts.
		 */
		private $limit_attempts_maximum_attempts = 5;

		/**
		 * Limit attempts timeout.
		 *
		 * @var int $limit_attempts_timeout Timeout.
		 */
		private $limit_attempts_timeout = 5;

		/**
		 * Lockout message.
		 *
		 * @var string $lockout_message Lockout message.
		 */
		private $lockout_message = '';

		/**
		 * Login_Controller constructor.
		 */
		private function __construct() {
			$this->limit_attempts_enabled          = get_option( 'aio_login_limit_attempts_enable', 'off' );
			$this->limit_attempts_maximum_attempts = get_option( 'aio_login_limit_attempts_maximum_attempts', 5 );
			$this->limit_attempts_timeout          = get_option( 'aio_login_limit_attempts_timeout', 5 );
			$this->lockout_message                 = get_option(
				'aio_login_limit_attempts_lockout_message',
				// translators: %d: Remaining minutes.
				__( 'You have been blocked due to too many unsuccessful login attempts. Please try again in %d minutes.', 'change-wp-admin-login' )
			);

			/**
			 * Setting the default values for each field.
			 */
			$this->limit_attempts_enabled          = 'on' === $this->limit_attempts_enabled;
			$this->limit_attempts_maximum_attempts = empty( $this->limit_attempts_maximum_attempts ) ? 5 : $this->limit_attempts_maximum_attempts;
			$this->limit_attempts_timeout          = empty( $this->limit_attempts_timeout ) ? 5 : $this->limit_attempts_timeout;

			if ( empty( $this->lockout_message ) ) {
				$this->lockout_message = // translators: %d: Remaining minutes.
					__( 'You have been blocked due to too many unsuccessful login attempts. Please try again in %d minutes.', 'change-wp-admin-login' );
			}

			add_action( 'login_enqueue_scripts', array( $this, 'login_enqueue_scripts' ) );
			add_filter( 'wp_authenticate_user', array( $this, 'wp_authenticate_user' ), 999, 2 );
			add_action( 'wp_login_failed', array( $this, 'wp_login_failed' ), 10, 2 );
			add_filter( 'login_errors', array( $this, 'wp_login_failed_message' ) );
			add_action( 'login_form', array( $this, 'add_hidden_fields' ) );

            add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
		}

		/**
		 * Login enqueue scripts
		 */
		public function login_enqueue_scripts() {
			wp_register_script( 'aio-login--detect-js', AIO_LOGIN__DIR_URL . 'assets/js/detect.js', array(), AIO_LOGIN__VERSION, true );
			wp_enqueue_script( 'aio-login--login-js', AIO_LOGIN__DIR_URL . 'assets/js/login.js', array( 'jquery', 'aio-login--detect-js' ), AIO_LOGIN__VERSION, true );
		}

		/**
		 * WP Authenticate User
		 *
		 * @param \WP_User $wp_user WP_User.
		 * @param string   $password Password.
		 *
		 * @return \WP_User|\WP_Error
		 */
		public function wp_authenticate_user( $wp_user, $password ) {
			if ( $this->is_enabled() ) {
				if ( Helper::is_ip_blocked() ) {
					$message = $this->get_message_after_calculation();

					return new \WP_Error(
						'aio_login__blocked',
						$message
					);
				}
			}

			// fixing the support ticket by adding the check user instance.
			if ( $wp_user instanceof \WP_User ) {
				if ( wp_check_password( $password, $wp_user->user_pass, $wp_user->ID ) ) {
					self::insert_log( $wp_user->user_login, 'success' );
					Helper::update_user_attempt_count( '', true );
				}
			}

			$wp_user = apply_filters( 'aio_login__wp_authenticate_user', $wp_user );
			return $wp_user;
		}

		/**
		 * Add error message
		 *
		 * @param string $username Username.
		 */
		public function wp_login_failed( $username ) {
			$this->insert_log( $username, 'failed' );

			if ( $this->is_enabled() ) {
				if ( ! Helper::is_ip_blocked() ) {
					Helper::update_user_attempt_count();

					$attempts = Helper::get_user_attempt_count();
					if ( $attempts >= $this->limit_attempts_maximum_attempts ) {
						Helper::block_ip();
						Helper::update_user_attempt_count( '', true );
					}
				}
			}
		}

		/**
		 * Get message after calculation
		 *
		 * @return string
		 */
		private function get_message_after_calculation() {
			$blocked_data = Helper::is_ip_blocked();
			$timeout      = $this->limit_attempts_timeout * 60;
			$remaining    = $timeout - ( current_time( 'timestamp' ) - $blocked_data['time'] ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
			$remaining    = round( $remaining / 60 );
			$remaining    = max( $remaining, 1 );

			return sprintf(
				$this->lockout_message,
				$remaining
			);
		}

		/**
		 * Insert failed login log
		 *
		 * @param string $username Username.
		 * @param string $status Status.
		 *
		 * @return int
		 */
		private function insert_log( $username, $status = '' ) {
			$ip_address = Helper::get_ip();
			$location   = Helper::get_location( $ip_address );
			$country    = $location['country'] ?? '';
			$city       = $location['city'] ?? '';
			$user_agent = 'UNKNOWN';

			if ( isset( $_POST['aio_login__user_agent'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$user_agent = sanitize_text_field( wp_unslash( $_POST['aio_login__user_agent'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
			}

			$login_details = array(
				'user_login' => $username,
				'ip_address' => $ip_address,
				'country'    => $country,
				'city'       => $city,
				'user_agent' => $user_agent,
				'status'     => $status,
				'time'       => current_time( 'timestamp' ), // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
			);

			return Failed_Logins::insert_logs( $login_details );
		}

		/**
		 * Add error message
		 *
		 * @param string $error Error message.
		 *
		 * @return string
		 */
		public function wp_login_failed_message( $error ) {
			if ( $this->is_enabled() ) {
				if ( ! isset( $_GET['action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					if ( Helper::is_ip_blocked() ) {
						$error = $this->get_message_after_calculation();

					} else {
						$attempts = Helper::get_user_attempt_count();
						$error   .= '<br>';
						$error   .= '<b>' . sprintf(
							// translators: %d: Remaining attempts.
							__( 'You have %d attempts remaining.', 'change-wp-admin-login' ),
							absint( $this->limit_attempts_maximum_attempts ) - absint( $attempts )
						) . '</b>';
					}
				}
			}
			return $error;
		}

		/**
		 * Add hidden fields
		 */
		public function add_hidden_fields() {
			echo '<input type="hidden" name="aio_login__user_agent" id="aio_login__user_agent" />';
		}

		/**
		 * Is enabled
		 *
		 * @return bool
		 */
		public function is_enabled() {
			$this->limit_attempts_enabled = apply_filters(
				'aio_login__limit_attempts_enabled',
				$this->limit_attempts_enabled
			);

			return $this->limit_attempts_enabled;
		}

        public function rest_api_init() {
	        register_rest_route(
                'aio-login/limit-login-attempts',
                '/get-settings',
                array(
                    'methods'             => 'GET',
                    'callback'            => array( $this, 'get_settings' ),
                    'permission_callback' => array( Helper::class, 'get_api_permission' ),
                )
            );

            register_rest_route(
                'aio-login/limit-login-attempts',
                '/save-settings',
                array(
                    'methods'             => 'POST',
                    'callback'            => array( $this, 'save_settings' ),
                    'permission_callback' => array( Helper::class, 'get_api_permission' ),
                )
            );

            register_rest_route(
                'aio-login/logs',
                '/failed-login',
                array(
                    'methods'             => 'GET',
                    'callback'            => array( $this, 'get_failed_logs' ),
                    'permission_callback' => array( Helper::class, 'get_api_permission' ),
                )
            );

			register_rest_route(
				'aio-login/logs',
				'/lockouts',
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'lockouts' ),
					'permission_callback' => array( Helper::class, 'get_api_permission' ),
				)
			);
        }

        public function get_settings() {
            $settings = array(
                'enabled'          => $this->limit_attempts_enabled,
                'maximum_attempts' => $this->limit_attempts_maximum_attempts,
                'timeout'          => $this->limit_attempts_timeout,
                'lockout_message'  => $this->lockout_message,
                'nonce'            => wp_create_nonce( 'limit-login-attempts' ),
            );

            return rest_ensure_response( $settings );
        }

        public function save_settings( \WP_REST_Request $request ) {
	        $params          = $request->get_params();
            $max_attempts    = sanitize_text_field( wp_unslash( $params['maximum_attempts'] ) );
            $timeout         = sanitize_text_field( wp_unslash( $params['timeout'] ) );
            $lockout_message = sanitize_text_field( wp_unslash( $params['lockout_message'] ) );

            if ( isset( $params['_wpnonce'] ) && wp_verify_nonce( $params['_wpnonce'], 'limit-login-attempts' ) ) {
                $enabled = 'off';
                if ( isset( $params['enabled'] ) && true === $params['enabled'] ) {
                    $enabled = 'on';
                }

                if ( empty( $max_attempts ) ) {
                    $max_attempts = 5;
                }

                if ( empty( $timeout ) ) {
                    $timeout = 5;
                }

                if ( empty( $lockout_message ) ) {
                    $lockout_message = // translators: %d: Remaining minutes.
                        __( 'You have been blocked due to too many unsuccessful login attempts. Please try again in %d minutes.', 'change-wp-admin-login' );
                }

                update_option( 'aio_login_limit_attempts_enable', $enabled );
                update_option( 'aio_login_limit_attempts_maximum_attempts', $max_attempts );
                update_option( 'aio_login_limit_attempts_timeout', $timeout );
                update_option( 'aio_login_limit_attempts_lockout_message', $lockout_message );

                return rest_ensure_response(
                    array(
                        'success' => true,
                        'message' => __( 'Settings saved successfully', 'change-wp-admin-login' ),
                    )
                );
            }

            return new \WP_Error(
                    'invalid_nonce',
                    __( 'Nonce verification failed', 'change-wp-admin-login' ),
                    array( 'status' => 401 )
            );
        }

        public function get_failed_logs() {
	        $logs = Helper::get_logs( 'failed' );

            $logs = array_map( function( $log ) {
                $log['time'] = date( 'F j, Y, g:i a', $log['time'] );
                return $log;
            }, $logs );

            return rest_ensure_response( $logs );
        }

		public function lockouts() {
			$logs = Helper::get_logs( 'lockout' );

			$logs = array_map( function( $log ) {
				$log['time'] = date( 'F j, Y, g:i a', $log['time'] );
				return $log;
			}, $logs );

			return rest_ensure_response( $logs );
		}

		/**
		 * Get instance.
		 *
		 * @return Login_Controller
		 */
		public static function get_instance() {
			static $instance = null;

			if ( is_null( $instance ) ) {
				$instance = new Login_Controller();
			}

			return $instance;
		}
	}
}
