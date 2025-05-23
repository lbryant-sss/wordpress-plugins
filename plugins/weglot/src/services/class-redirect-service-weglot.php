<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Redirect URL
 *
 * @since 2.0
 */
class Redirect_Service_Weglot {

	/**
	 *
	 * @var boolean
	 */
	protected $no_redirect = false;
	/**
	 * @var Option_Service_Weglot
	 */
	private $option_services;
	/**
	 * @var Request_Url_Service_Weglot
	 */
	private $request_url_services;
	/**
	 * @var Language_Service_Weglot
	 */
	private $language_services;

	/**
	 * @since 2.0
	 */
	public function __construct() {
		$this->option_services      = weglot_get_service( 'Option_Service_Weglot' );
		$this->request_url_services = weglot_get_service( 'Request_Url_Service_Weglot' );
		$this->language_services    = weglot_get_service( 'Language_Service_Weglot' );
	}

	/**
	 * @return bool
	 * @since 2.0
	 *
	 */
	public function get_no_redirect() {
		return $this->no_redirect;
	}

	/**
	 * Returns a code if the navigator languages matches some patterns. For instance return zh-tw if navigator is zh-Hant-TW
	 *
	 * @param string $navigator_language the navigator language code.
	 *
	 * @return string
	 * @since 2.3.0
	 */
	protected function language_exception( $navigator_language ) {

		$exceptions = array(
			array(
				'code'   => 'no',
				'detect' => '/^(nn|nb)(-[a-z]+)?$/i',
			),
			array(
				'code'   => 'zh',
				'detect' => '/^zh(-hans(-\w{2})?)?(-(cn|sg))?$/i',
			),
			array(
				'code'   => 'zh-tw',
				'detect' => '/^zh-(hant)?-?(tw|hk|mo)?$/i',
			),
		);

		foreach ( $exceptions as $exception ) {
			if ( preg_match( $exception['detect'], $navigator_language, $matches ) ) {
				return $exception['code'];
			}
		}
		return $navigator_language;
	}

	/**
	 * Return an array of navigator languages
	 *
	 * @return array<int,string>
	 */
	protected function get_navigator_languages() {
		$navigator_languages = array();
		if ( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) { //phpcs:ignore
			$navigator_languages = explode( ',', trim( sanitize_text_field( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) );
			foreach ( $navigator_languages as &$navigator_language ) {
				if ( strpos( $navigator_language, ';' ) !== false ) {
					$navigator_language = substr( $navigator_language, 0, strpos( $navigator_language, ';' ) );
				}
				$navigator_language = strtolower( $navigator_language );
			}
		} else {
			if ( isset( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ) { // Compatibility Cloudflare.
				$navigator_languages = array( strtolower( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ); //phpcs:ignore
			}
		}
		return $navigator_languages;
	}

	/**
	 * Return the best language available based on the navigator language.
	 *
	 * @param  array<int,string> $navigator_languages The list of navigator languages.
	 * @param  array<int,string> $available_languages The list of available languages.
	 * @return string|null
	 */
	public function get_best_available_language( $navigator_languages, $available_languages ) {

		// We add the original in the list of available languages.
		$available_languages[] = $this->language_services->get_original_language()->getExternalCode();

		// We check if user add a custom languages code
		$navigator_languages = apply_filters('weglot_navigator_language', $navigator_languages);

		if ( ! empty( $navigator_languages ) ) {
			$destination_languages_external = array_map( 'strtolower', $available_languages );
			foreach ( $navigator_languages as $navigator_language ) {
				// We try to find an exact match.
				if ( in_array( $navigator_language, $destination_languages_external ) ) {
					return $navigator_language;
				}
				// We try to find an exact match after handling exceptions.
				if ( in_array( $this->language_exception( $navigator_language ), $destination_languages_external ) ) {
					return $this->language_exception( $navigator_language );
				}
				// We try to find a secondary match.
				if ( in_array( substr( $navigator_language, 0, 2 ), $destination_languages_external ) ) {
					return substr( $navigator_language, 0, 2 );
				}

				// We try to find a secondary match.
				foreach ( $destination_languages_external as $destination_language ) {
					if ( substr( $navigator_language, 0, 2 ) === substr( $destination_language, 0, 2 ) ) {
						return $destination_language;
					}
				}
			}
		}
		return null;
	}

	/**
	 * @return void
	 * Redirect the visitor
	 *
	 * @version 2.3.0
	 * @since 2.0
	 */
	public function auto_redirect() {

		if ( ! isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) && ! isset( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ) { //phpcs:ignore
			return;
		}

		// We retrieve the best language based on navigator languages and destination languages.
		$navigator_languages            = $this->get_navigator_languages();
		$destination_languages_external = $this->language_services->get_destination_languages_external( $this->request_url_services->is_allowed_private() );
		$best_external_language         = $this->get_best_available_language( $navigator_languages, $destination_languages_external );
		$best_language                  = $this->language_services->get_language_from_external( $best_external_language );


		// Redirect using the best language.
		if ( !empty($best_language) && $best_language !== $this->language_services->get_original_language() && $this->language_services->get_original_language() === $this->request_url_services->get_current_language() ) {
			//Cancel redirect if excluded page.
			if ( ! $this->request_url_services->get_weglot_url()->getForLanguage( $best_language, false ) ){
				return;
			}

			$url_auto_redirect = apply_filters( 'weglot_url_auto_redirect', $this->request_url_services->get_weglot_url()->getForLanguage( $best_language, true ) );
			$url_auto_redirect = $this->request_url_services->clean_url_slashes($url_auto_redirect);

			header('Vary: Accept-Language');
			header( "Location: $url_auto_redirect", true, 302 );
			exit();
		}

		// If there is no best language, we redirect using the auto switch fallback if there is one in the options.
		if ( empty($best_language) || ( ! in_array( $this->language_services->get_original_language()->getExternalCode(), $navigator_languages ) &&
			$this->option_services->get_option( 'autoswitch_fallback' ) !== null )
		) {
			$fallback_language = $this->language_services->get_language_from_internal( $this->option_services->get_option( 'autoswitch_fallback' ) );
			//Cancel redirect if excluded page.
			if ( ! $this->request_url_services->get_weglot_url()->getForLanguage( $fallback_language, false ) ){
				return;
			}
			$url_auto_redirect = apply_filters( 'weglot_url_auto_redirect', $this->request_url_services->get_weglot_url()->getForLanguage( $fallback_language, true ) );
			$url_auto_redirect = $this->request_url_services->clean_url_slashes($url_auto_redirect);

			header('Vary: Accept-Language');
			header( "Location: $url_auto_redirect", true, 302 );
			exit();
		}
	}

	/**
	 * @return void
	 * @since 2.0
	 *
	 */
	public function verify_no_redirect() {
		if ( isset( $_GET["wg-choose-original"] ) ) { //phpcs:ignore
			$wg_choose_original = $_GET["wg-choose-original"]; //phpcs:ignore
			if ( 'true' === $wg_choose_original ) {
				setcookie( "WG_CHOOSE_ORIGINAL", 'true', time() + 86400 * 2, '/' ); //phpcs:ignore
			} elseif ( 'false' === $wg_choose_original ) {
				setcookie( "WG_CHOOSE_ORIGINAL", '', - 1, '/' ); //phpcs:ignore
			} else {
				return;
			}
			if ( isset( $_SERVER['REQUEST_URI'] ) ) { // phpcs:ignore

				// Remove the 'wg-choose-original' parameter from the query string
				$_SERVER['REQUEST_URI'] = preg_replace(
					'/([&?])wg-choose-original=[^&]*(&|$)/',
					'$1',
					esc_url_raw( $_SERVER['REQUEST_URI'] )
				);

				// Remove any trailing '&' or '?' left in the query string
				$_SERVER['REQUEST_URI'] = rtrim( esc_url_raw( $_SERVER['REQUEST_URI'] ), '&?' );

				// Ensure the URL doesn't end with a '?' if no other query parameters are present
				$_SERVER['REQUEST_URI'] = preg_replace( '/\?$/', '', esc_url_raw( $_SERVER['REQUEST_URI'] ) );

				// Sanitize the URL
				$_SERVER['REQUEST_URI'] = esc_url_raw( $_SERVER['REQUEST_URI'] );

				// Reset the URL as we removed the parameter from URL
				$this->request_url_services->init_weglot_url();
				header( 'Location:' . $this->request_url_services->get_weglot_url()->getUrl(), true, 302 );
				exit();
			}
		}
	}
}


