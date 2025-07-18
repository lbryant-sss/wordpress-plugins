<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Client\Api\Exception\ApiError;
use Weglot\Client\Api\LanguageEntry;
use WeglotWP\Helpers\Helper_Json_Inline_Weglot;
use WeglotWP\Helpers\Helper_API;


/**
 * @since 2.3.0
 */
class Translate_Service_Weglot {
	/**
	 * @var Parser_Service_Weglot
	 */
	private $parser_services;
	/**
	 * @var string
	 */
	private $current_language;
	/**
	 * @var string
	 */
	private $original_language;
	/**
	 * @var Request_Url_Service_Weglot
	 */
	private $request_url_services;
	/**
	 * @var Language_Service_Weglot
	 */
	private $language_services;
	/**
	 * @var Replace_Url_Service_Weglot
	 */
	private $replace_url_services;
	/**
	 * @var Option_Service_Weglot
	 */
	private $option_services;
	/**
	 * @var Generate_Switcher_Service_Weglot
	 */
	private $generate_switcher_service;


	/**
	 * @since 2.3.0
	 */
	public function __construct() {
		$this->option_services           = weglot_get_service( 'Option_Service_Weglot' );
		$this->request_url_services      = weglot_get_service( 'Request_Url_Service_Weglot' );
		$this->replace_url_services      = weglot_get_service( 'Replace_Url_Service_Weglot' );
		$this->parser_services           = weglot_get_service( 'Parser_Service_Weglot' );
		$this->generate_switcher_service = weglot_get_service( 'Generate_Switcher_Service_Weglot' );
		$this->language_services         = weglot_get_service( 'Language_Service_Weglot' );
	}


	/**
	 * @return void
	 * @since 2.3.0
	 */
	public function weglot_translate() {
		ob_start( array( $this, 'weglot_treat_page' ) );
	}

	/**
	 * @param LanguageEntry $current_language
	 *
	 * @return Translate_Service_Weglot
	 * @since 2.3.0
	 */
	public function set_current_language( $current_language ) {
		$this->current_language = $current_language->getInternalCode();

		return $this;
	}

	/**
	 * @param LanguageEntry $original_language
	 *
	 * @return Translate_Service_Weglot
	 * @since 2.3.0
	 */
	public function set_original_language( $original_language ) {
		$this->original_language = $original_language->getInternalCode();

		return $this;
	}

	/**
	 * @param string $content
	 *
	 * @return string
	 */
	public function get_canonical_url_from_content( $content ) {
		$check_canonical = preg_match( '/<link rel="canonical"(.*?)?href=(\"|\')([^\s\>]+?)(\"|\')/', $content, $matches );

		if ( 1 === $check_canonical ) {
			if ( isset( $matches[3] ) && ! empty( $matches[3] ) ) {
				return $matches[3];
			} else {
				return '';
			}
		} else {
			return '';
		}
	}

	/**
	 * @return boolean
	 */
	public function check_ajax_exclusion_before_treat() { //phpcs:ignore
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			if ( ! $this->request_url_services->create_url_object( wp_get_referer() )->getForLanguage( $this->request_url_services->get_current_language(), false ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return boolean
	 */
	public function check_404_exclusion_before_treat() { //phpcs:ignore
		if ( http_response_code() == 404 ) {
			$excluded_urls = $this->option_services->get_exclude_urls();
			foreach ( $excluded_urls as $item ) {
				if ( '^/404$' === $item[0] ) {
					return true;
				}
			}
			return false;
		} else {
			return false;
		}
	}

	/**
	 * @param string $content
	 *
	 * @return string
	 * @throws \Exception
	 * @since 2.3.0
	 * @see weglot_init / ob_start
	 */
	public function weglot_treat_page( $content ) {

		$active_translation = apply_filters( 'weglot_active_translation', true );
		if ( empty( $content ) || ! $active_translation ) {
			return $content;
		}
		$this->set_original_language( $this->language_services->get_original_language() );
		$this->set_current_language( $this->request_url_services->get_current_language() ); // Need to reset.

		// Choose type translate.
		$type = ( Helper_Json_Inline_Weglot::is_json( $content ) ) ? 'json' : 'html';
		if ( 'json' !== $type ) {
			$type = ( Helper_Json_Inline_Weglot::is_xml( $content ) ) ? 'xml' : 'html';
		}

		$type      = apply_filters( 'weglot_type_treat_page', $type );
		$canonical = $this->get_canonical_url_from_content( $content );
		$force_request_url = apply_filters( 'weglot_get_current_canonical_url', false );

		$weglot_force_translate_cart = apply_filters( 'weglot_force_translate_cart', false );
		if( $weglot_force_translate_cart){
			$content   = $this->force_translate_cart($content);
		}
		// No need to translate but prepare new dom with button.
		if (
			$this->current_language === $this->original_language
			|| $this->check_404_exclusion_before_treat()
			|| ! $this->request_url_services->get_weglot_url()->getForLanguage( $this->request_url_services->get_current_language(), false )
		) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				if ( ! $this->request_url_services->create_url_object( wp_get_referer() )->getForLanguage( $this->request_url_services->get_current_language(), false ) ) { //phpcs:ignore
					// do nothing because the ajax referer are not exclude!
				} else {
					return $content;
				}
			} else {
				// if type is xml we render the content without treatment.
				if ( 'xml' === $type || 'json' === $type ) {
					return $content;
				} else {
					//we check if we need to translate some forced child
					$translate_inside_exclusions_blocks   = $this->option_services->get_translate_inside_exclusions_blocks();
					if(0 < count($translate_inside_exclusions_blocks)){
						add_filter('weglot_parser_whitelist', function ($whitelist) {
							$translate_inside_exclusions_blocks = $this->option_services->get_translate_inside_exclusions_blocks();
							return array_merge($whitelist, $translate_inside_exclusions_blocks);
						});
						$parser = $this->parser_services->get_parser();
						if($force_request_url){
							$request_url = $this->request_url_services->get_current_canonical_url();
							$translated_content = $parser->translate( $content, $this->original_language, $this->current_language, array(), $canonical, $request_url);
						}else{
							$translated_content = $parser->translate( $content, $this->original_language, $this->current_language, array(), $canonical);
						}
						return $this->weglot_render_dom( $translated_content, $canonical );
					}else{
						return $this->weglot_render_dom( $content, $canonical );
					}
				}
			}
		}
		do_action('weglot_treat_page_hook', $this->current_language);

		$parser = $this->parser_services->get_parser();

		try {
			switch ( $type ) {
				case 'json':
					$extra_keys         = apply_filters( 'weglot_add_json_keys', array() );
					if ( apply_filters( 'weglot_escape_attribute_in_json', false ) ) {
						$content = $this->parser_services->preserve_attributes( $content ); // List attributes you want to escape
					}
					$translated_content = $parser->translate( $content, $this->original_language,$this->current_language, $extra_keys );
					$translated_content = wp_json_encode( $this->replace_url_services->replace_link_in_json( json_decode( $translated_content, true ) ) );
					if ( apply_filters( 'weglot_escape_attribute_in_json', false ) ) {
						$translated_content = $this->parser_services->restore_preserved_attributes( $translated_content );
					}
					return apply_filters( 'weglot_json_treat_page', $translated_content );
				case 'xml':
					$translated_content = $parser->translate( $content, $this->original_language, $this->current_language, array(), $canonical );
					if ( $this->current_language !== $this->original_language ) {
						$translated_content = $this->replace_url_services->replace_link_in_xml( $translated_content );
					}
					$translated_content = apply_filters( 'weglot_html_treat_page', $translated_content );

					return apply_filters( 'weglot_xml_treat_page', $translated_content );
				case 'html':

					if ( apply_filters( 'weglot_escape_attribute_in_html', false ) ) {
						$content = $this->parser_services->preserve_attributes( $content );
					}

					if ( apply_filters( 'weglot_escape_vue_js', false ) ) {
						// Escape the Vue.js attributes before processing.
						$content = $this->parser_services->escape_vue_attributes( $content );
					}

					if($force_request_url){
						$request_url = $this->request_url_services->get_current_canonical_url();
						$translated_content = $parser->translate( $content, $this->original_language, $this->current_language, array(), $canonical, $request_url);
					}else{
						$translated_content = $parser->translate( $content, $this->original_language, $this->current_language, array(), $canonical);
					}
					if ( apply_filters( 'weglot_escape_vue_js', false ) ) {
						$translated_content = $this->parser_services->restore_vue_attributes( $translated_content );
					}

					if ( apply_filters( 'weglot_escape_attribute_in_html', false ) ) {
						$translated_content = $this->parser_services->restore_preserved_attributes( $translated_content );
					}

					$translated_content = apply_filters( 'weglot_html_treat_page', $translated_content );
					$translated_content = $this->replace_url_services->proxify_url( $translated_content );
					$translated_content = $this->disable_automated_translation_services( $translated_content );

					return $this->weglot_render_dom( $translated_content, $canonical );
				default:
					$name_filter = sprintf( 'weglot_%s_treat_page', $type );

					return apply_filters( $name_filter, $content, $parser, $this->original_language, $this->current_language );
			}
		} catch ( ApiError $e ) {
			if ( 'json' !== $type ) {
				if ( ! defined( 'DONOTCACHEPAGE' ) ) {
					define( 'DONOTCACHEPAGE', 1 );
				}
				nocache_headers();
				$content .= '<!--Weglot error API : ' . $this->remove_comments( $e->getMessage() ) . '-->';
			}

			return $content;
		} catch ( \Exception $e ) {
			if ( 'json' !== $type ) {
				if ( ! defined( 'DONOTCACHEPAGE' ) ) {
					define( 'DONOTCACHEPAGE', 1 );
				}
				nocache_headers();
				$content .= '<!--Weglot error : ' . $this->remove_comments( $e->getMessage() ) . '-->';
			}

			return $content;
		}
	}


	/**
	 * Remove comments from HTML.
	 *
	 * @param string $html the HTML string.
	 *
	 * @return string
	 * @since 2.3.0
	 */
	private function remove_comments( $html ) {
		return preg_replace( '/<!--(.*)-->/Uis', '', $html );
	}

	/**
	 * Force translate woocommerce cart.
	 *
	 * @param string $content the HTML string.
	 *
	 * @return string
	 * @since 2.3.0
	 */
	private function force_translate_cart( $content ) {
		if ( false !== strpos( wp_get_referer(), '/cart/' ) ) {
			// This is the cart page
			$parser = $this->parser_services->get_parser();
			$current_language = $this->request_url_services->create_url_object( wp_get_referer() )->getCurrentLanguage();
			if($current_language->getInternalCode() != $this->original_language){
				$translated_content = $parser->translate( $content, $this->original_language, $current_language->getInternalCode() );
				$translated_content = apply_filters( 'weglot_html_treat_page', $translated_content );
				return $this->weglot_render_dom( $translated_content );
			}
		}
		return $content;
	}

	/**
	 * Replace links and add switcher on the final HTML.
	 *
	 * @param string $dom the final translated HTML.
	 * @param string $canonical the canonical link.
	 *
	 * @return string
	 * @since 2.3.0
	 */
	public function weglot_render_dom( $dom, $canonical = '' ) {
		$dom = $this->generate_switcher_service->generate_switcher_from_dom( $dom );

		// We only need this on translated page.
		if ( $this->current_language !== $this->original_language ) {
			$dom = $this->replace_url_services->replace_link_in_dom( $dom );
		}

		// Remove hreflangs if non-canonical page.
		if ( '' !== $canonical ) {
			$canonical   = urldecode( $canonical );
			$current_url = $this->request_url_services->get_weglot_url();
			if ( $current_url->getPath() !== $this->request_url_services->create_url_object( $canonical )->getPath() ) {
				$dom = preg_replace( '/<link rel="alternate" href=(\"|\')([^\s\>]+?)(\"|\') hreflang=(\"|\')([^\s\>]+?)(\"|\')\/>/', '', $dom );
			}

			// update canonical if page excluded page.
			if ( ! $current_url->getForLanguage( $this->request_url_services->get_current_language(), false ) ) {
				$dom = preg_replace( '/<link rel="canonical"(.*?)?href=(\"|\')([^\s\>]+?)(\"|\')/', '<link rel="canonical" href="' . esc_url( $current_url->getForLanguage( $this->language_services->get_original_language() ) ) . '"', $dom );
			}
		}

		return apply_filters( 'weglot_render_dom', $dom );
	}

	/**
	 * Disable automated translation services adding translate="no" attributes.
	 *
	 * @param string $html the HTML string.
	 *
	 * @return string
	 * @since 2.3.0
	 */
	private function disable_automated_translation_services( $html ) {
		$remove_auto_service_translate = apply_filters( 'weglot_remove_google_translate', true );
		if($remove_auto_service_translate){
			$pattern = '/<html(\s*>|\s+)/i';
			$replacement = '<html translate="no"$1';
			return preg_replace($pattern, $replacement, $html);
		}

		return $html;
	}

	/**
	 * @param string $api_key Weglot API key
	 * @param string $l_from Source language
	 * @param string $l_to Target language
	 * @param string $request_url Request URL
	 * @param string $word Word to translate
	 * @param int $t Text type
	 *
	 * @return string
	 * @since 2.4.0
	 */
	public function reverseTranslate($api_key, $l_from, $l_to, $request_url, $word, $t) {

		$requestBody = wp_json_encode([
			"l_from" => $l_from,
			"l_to" => $l_to,
			"request_url" => $request_url,
			"words" => [
				["w" => $word, "t" => $t]
			]
		]);

		$url = sprintf('%s/translate?api_key=%s', Helper_API::get_api_url(), $api_key);

		$args = [
			'body'        => $requestBody,
			'headers'     => [
				'Content-Type' => 'application/json',
			],
			'method'      => 'POST',
			'data_format' => 'body',
		];

		$response = wp_remote_post($url, $args);

		if (is_wp_error($response)) {
			return "WP Error: " . $response->get_error_message();
		}

		$response_body = wp_remote_retrieve_body($response);

		$responseData = json_decode($response_body, true);

		if (!$responseData || !isset($responseData['ids'])) {
			return "Error: Invalid response from API";
		}

		return $responseData['to_words'][0];
	}
}



