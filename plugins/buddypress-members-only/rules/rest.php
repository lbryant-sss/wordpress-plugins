<?php 
if (!defined('ABSPATH'))
{
	exit;
}

//Prevents the inclusion of the REST API endpoint link in the HTML <head> section, which could potentially expose REST API endpoints.
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
//Disables sending REST API link headers in HTTP responses, further preventing exposure of REST API endpoints.
remove_action( 'template_redirect', 'rest_output_link_header', 11 );
//Prevents the automatic registration of initial REST API routes, which are typically registered by WordPress core at an early stage of the REST API initialization.
remove_action( 'rest_api_init', 'create_initial_rest_routes');
//Disables the output of the RSD XML document, which contains API endpoint information for XML-RPC, thus limiting potential access to API functionality via XML-RPC.
remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
//Remove oEmbed functionality to prevent other websites from using the oEmbed API to embed your protected WordPress content.
remove_action ( 'rest_api_init', 'wp_oembed_register_route' );
//Prevents WordPress from automatically adding <link> tags to the <head> section for oEmbed discovery, which limits how other sites can embed your content via oEmbed.
remove_action ( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action ( 'wp_head', 'wp_oembed_add_host_js' );
remove_filter ( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );
add_filter ( 'embed_oembed_discover', '__return_false' );
//Prevents WordPress from automatically adding <link> tags to the <head> section for oEmbed discovery, which limits how other sites can embed your content via oEmbed.
remove_filter ( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );

//Prevents access to WordPress' REST API via JSON format, restricting non-standard JSON access to content.
add_filter( 'json_enabled', '__return_false' );
//Prevents access to WordPress' REST API via JSONP (JSON with Padding), which could introduce security risks.
add_filter( 'json_jsonp_enabled', '__return_false' );
//Completely disables WordPress' REST API functionality, preventing any attempts to access or manipulate WordPress via REST API.
add_filter( 'rest_enabled', '__return_false' );
//If the REST API is not completely disabled, this setting disables JSONP support specifically for the REST API, preventing potential security vulnerabilities.
add_filter( 'rest_jsonp_enabled', '__return_false' );

add_filter( 'rest_authentication_errors', 'only_allow_logged_in_user_via_rest_access' );

function only_allow_logged_in_user_via_rest_access( $access ) {
    if ( ! is_user_logged_in() ) {
        return new WP_Error( 'rest_api_disabled', 'Only authenticated users can access protected content.', array( 'status' => rest_authorization_required_code() ) );
    }
    return $access;
}

