<?php

namespace AdTribes\PFP\REST;

use AdTribes\PFP\Abstracts\Abstract_REST;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Class API
 *
 * This class creates an API for the plugin.
 */
class API extends Abstract_REST {

    /**
     * Register the routes.
     */
    public function register_routes() {
        $this->rest_base = 'pfp';

        register_rest_route(
            $this->namespace,
            "/$this->rest_base",
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'version' ),
                'permission_callback' => array( $this, 'create_item_permissions_check' ),
            )
        );
    }

    /**
     * Plugin version.
     *
     * @return WP_REST_Response
     */
    public function version() {
        $version = defined( 'ADT_PFP_OPTION_INSTALLED_VERSION' ) ? ADT_PFP_OPTION_INSTALLED_VERSION : '';
        return new WP_REST_Response( array( 'version' => $version ), 200 );
    }
}
