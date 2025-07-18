<?php

use Weglot\Client\Api\LanguageCollection;
use Weglot\Client\Api\LanguageEntry;
use WeglotWP\Services\Request_Url_Service_Weglot;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get a service Weglot
 * @param string $service
 * @return object
 * @throws Exception
 * @since 2.0
 *
 */
function weglot_get_service( $service ) {
	return Context_Weglot::weglot_get_context()->get_service( $service );
}

/**
 * Get all options
 * @return array<string, mixed>
 * @throws Exception
 * @since 2.0
 *
 */
function weglot_get_options() {
	return Context_Weglot::weglot_get_context()->get_service( 'Option_Service_Weglot' )->get_options();
}

/**
 * Get option
 * @param string $key
 * @return mixed
 * @throws Exception
 * @since 2.0
 */
function weglot_get_option( $key ) {
	return Context_Weglot::weglot_get_context()->get_service( 'Option_Service_Weglot' )->get_option( $key );
}

/**
 * Get original language
 * @return string
 * @throws Exception
 * @since 2.0
 */
function weglot_get_original_language() {
	return weglot_get_option( 'original_language' );
}

/**
 * Get current language
 * @return string
 * @throws Exception
 * @since 2.0
 */
function weglot_get_current_language() {
	return Context_Weglot::weglot_get_context()->get_service( 'Request_Url_Service_Weglot' )->get_current_language()->getInternalCode();
}

/**
 * Get current language
 * @return string
 * @throws Exception
 * @since 2.0
 */
function weglot_get_current_language_custom() {
	return Context_Weglot::weglot_get_context()->get_service( 'Request_Url_Service_Weglot' )->get_current_language()->getExternalCode();
}

/**
 * Get current language code from custom language
 * @return string
 * @throws Exception
 * @since 2.0
 */
function weglot_get_current_language_code_from_custom_language() {
	return Context_Weglot::weglot_get_context()->get_service( 'Request_Url_Service_Weglot' )->get_current_language()->getExternalCode();
}


/**
 * Get destination languages available for translation
 *
 * This method retrieves the destination languages available for translation from Weglot service.
 *
 * @return string[] An array of destination languages
 * @throws Exception If unable to retrieve destination languages
 */
function weglot_get_destination_languages() {
	return Context_Weglot::weglot_get_context()->get_service( 'Option_Service_Weglot' )->get_destination_languages();
}

/**
 * Get Request Url Service
 * @return object
 * @throws Exception
 * @since 2.0
 */
function weglot_get_request_url_service() {
	return Context_Weglot::weglot_get_context()->get_service( 'Request_Url_Service_Weglot' );
}

/**
 * Get languages available on Weglot
 * @return LanguageCollection
 * @throws Exception
 * @since 2.0
 */
function weglot_get_languages_available() {
	return Context_Weglot::weglot_get_context()->get_service( 'Language_Service_Weglot' )->get_languages_available();
}

/**
 * Get button selector HTML
 *
 * @param string $add_class
 *
 * @return string
 * @throws Exception
 * @since 2.0
 */
function weglot_get_button_selector_html( $add_class = '' ) {
	return Context_Weglot::weglot_get_context()->get_service( 'Button_Service_Weglot' )->get_html( $add_class );
}


/**
 * Get exclude urls
 * @return array<string, mixed>
 * @throws Exception
 * @since 2.0
 */
function weglot_get_exclude_urls() {
	return Context_Weglot::weglot_get_context()->get_service( 'Option_Service_Weglot' )->get_exclude_urls();
}

/**
 * Get translate AMP option
 * @return bool
 * @throws Exception
 * @since 2.0
 */
function weglot_get_translate_amp_translation() {
	return Context_Weglot::weglot_get_context()->get_service( 'Option_Service_Weglot' )->get_option_custom_settings( 'translate_amp' );
}

/**
 * Get current full url
 * @since 2.0
 * @return string
 */
function weglot_get_current_full_url() {
	return weglot_create_url_object( weglot_get_request_url_service()->get_full_url() )->getForLanguage( weglot_get_request_url_service()->get_current_language() );
}

/**
 * Is eligible url
 *
 * @param string $url
 *
 * @return boolean
 * @throws Exception
 * @since 2.0
 */
function weglot_is_eligible_url( $url ) {
	return Context_Weglot::weglot_get_context()->get_service( 'Request_Url_Service_Weglot' )->is_eligible_url( $url );
}

/**
 * Get API KEY Weglot
 * @return string
 * @throws Exception
 * @since 2.0
 * @version 3.0.0
 */
function weglot_get_api_key() {
	return weglot_get_option( 'api_key_private' );
}

/**
 * Get auto redirect option
 * @return boolean
 * @throws Exception
 * @since 2.0
 */
function weglot_has_auto_redirect() {
	return weglot_get_option( 'auto_redirect' );
}

/**
 * @since 2.0.4
 * @param string $url
 * @return Weglot\Util\Url
 */
function weglot_create_url_object( $url ) {
	return weglot_get_request_url_service()->create_url_object( $url );
}

/**
 * @return bool|string
 * @throws Exception
 * @since 2.0.4
 *
 */
function weglot_get_full_url_no_language() {
	return weglot_create_url_object( weglot_get_request_url_service()->get_full_url() )->getForLanguage( weglot_get_service('Language_Service_Weglot')->get_original_language() );
}

/**
 * @return int
 * @throws Exception
 * @since 2.0.4
 *
 */
function weglot_get_postid_from_url() {
	return url_to_postid( weglot_get_full_url_no_language() ); //phpcs:ignore
}
/**
 * @since 2.4.0
 * @return string
 */
function weglot_get_rest_current_url_path() {
	$current_url = wp_parse_url( add_query_arg( array() ) );
	return apply_filters( 'weglot_get_rest_current_url_path', $current_url['path'] );
}
