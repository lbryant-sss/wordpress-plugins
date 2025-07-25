<?php
/**
 * Class for requests to WPMU DEV API.
 *
 * @package Hummingbird\Core\Api\Request
 */

namespace Hummingbird\Core\Api\Request;

use Hummingbird\Core\Api\Exception;
use Hummingbird\Core\Api\Service\Hosting;
use Hummingbird\Core\Api\Service\Performance;
use Hummingbird\Core\Api\Service\Uptime;
use WPMUDEV_Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WPMUDEV
 */
class WPMUDEV extends Request {
	/**
	 * Get API key.
	 *
	 * @return string
	 */
	public function get_api_key() {
		global $wpmudev_un;

		if ( ! is_object( $wpmudev_un ) && class_exists( 'WPMUDEV_Dashboard' ) && method_exists( 'WPMUDEV_Dashboard', 'instance' ) ) {
			$wpmudev_un = WPMUDEV_Dashboard::instance();
		}

		if ( defined( 'WPHB_API_KEY' ) ) {
			$api_key = WPHB_API_KEY;
		} elseif ( is_object( $wpmudev_un ) && method_exists( $wpmudev_un, 'get_apikey' ) ) {
			$api_key = $wpmudev_un->get_apikey();
		} elseif ( class_exists( 'WPMUDEV_Dashboard' ) && is_object( WPMUDEV_Dashboard::$api ) && method_exists( WPMUDEV_Dashboard::$api, 'get_key' ) ) {
			$api_key = WPMUDEV_Dashboard::$api->get_key();
		} elseif ( class_exists( 'WPMUDEV\Hub\Connector\API' ) && \WPMUDEV\Hub\Connector\API::get()->is_logged_in() ) {
			$api_key = \WPMUDEV\Hub\Connector\API::get()->get_api_key();
		} else {
			$api_key = '';
		}

		return $api_key;
	}

	/**
	 * Get API URL.
	 *
	 * @param string $path  API path.
	 *
	 * @return string
	 */
	public function get_api_url( $path = '' ) {
		/**
		 * Service.
		 *
		 * @var Performance|Uptime|Hosting $service
		 */
		$service = $this->get_service();

		if ( defined( 'WPHB_TEST_API_URL' ) && WPHB_TEST_API_URL ) {
			$url = WPHB_TEST_API_URL . $service->get_name() . '/' . $service->get_version() . '/';
			// Sync with WPMUDEV_Dashboard.
		} elseif ( defined( 'WPMUDEV_CUSTOM_API_SERVER' ) && WPMUDEV_CUSTOM_API_SERVER ) {
			$url = WPMUDEV_CUSTOM_API_SERVER . '/api/' . $service->get_name() . '/' . $service->get_version() . '/';
		} else {
			$url = 'https://wpmudev.com/api/' . $service->get_name() . '/' . $service->get_version() . '/';
		}

		return trailingslashit( $url . $path );
	}

	/**
	 * Get the current Site URL
	 *
	 * The network_site_url() of the WP installation. (Or network_home_url if not passing an API key).
	 *
	 * @return string
	 */
	public function get_this_site() {
		if ( ! is_multisite() || is_main_site() ) {
			if ( defined( 'WPHB_API_DOMAIN' ) ) {
				$domain = WPHB_API_DOMAIN;
			} else {
				$key = $this->get_api_key();
				if ( ! empty( $key ) ) {
					$domain = network_site_url();
				} else {
					$domain = network_home_url();
				}
			}
		} else {
			if ( defined( 'WPHB_API_SUBDOMAIN' ) ) {
				$domain = WPHB_API_SUBDOMAIN;
			} else {
				$domain = get_site_url();
			}
		}

		return $domain;
	}

	/**
	 * Sign request.
	 */
	protected function sign_request() {
		$key = $this->get_api_key();
		if ( ! empty( $key ) ) {
			$this->add_header_argument( 'Authorization', 'Basic ' . $this->get_api_key() );
		}
	}

	/**
	 * Do request.
	 *
	 * @param string $path    API path.
	 * @param array  $data    Request data.
	 * @param string $method  Method type.
	 * @param array  $extra   Extra data.
	 *
	 * @return array|mixed|object
	 * @throws Exception  Exception.
	 */
	public function request( $path, $data = array(), $method = 'post', $extra = array() ) {
		$response = parent::request( $path, $data, $method );

		if ( is_wp_error( $response ) ) {
			throw new Exception( $response->get_error_message(), $response->get_error_code() );
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ) );
		/* translators: %s: error code */
		$message = isset( $body->message ) ? $body->message : sprintf( __( 'Unknown Error. Code: %s', 'wphb' ), $code );

		if ( 200 !== (int) $code ) {
			throw new Exception( $message, $code );
		} else {
			if ( is_object( $body ) && isset( $body->error ) && $body->error ) {
				throw new Exception( $message, $code );
			}
			return $body;
		}

	}

}
