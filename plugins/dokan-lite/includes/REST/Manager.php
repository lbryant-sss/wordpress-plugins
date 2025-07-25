<?php

namespace WeDevs\Dokan\REST;

use WC_Data;

/**
 * API_Registrar class
 */
class Manager {

    /**
     * Class dir and class name mapping
     *
     * @var array
     */
    protected $class_map;

    /**
     * Constructor
     */
    public function __construct() {
        if ( ! class_exists( 'WP_REST_Server' ) ) {
            return;
        }

        // Init REST API routes.
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ), 10 );
        add_filter( 'woocommerce_rest_prepare_product_object', array( $this, 'prepare_product_response' ) );
        add_filter( 'dokan_vendor_to_array', array( $this, 'filter_store_open_close_option' ) );

        // Send email to admin on adding a new product
        add_action( 'dokan_rest_insert_product_object', array( $this, 'on_dokan_rest_insert_product' ), 10, 3 );
        add_filter( 'dokan_vendor_to_array', [ $this, 'filter_payment_response' ] );
    }

    /**
     * Register REST API routes.
     *
     * @since 1.2.0
     */
    public function register_rest_routes() {
        // get rest api class map
        $this->get_rest_api_class_map();

        foreach ( $this->class_map as $file_name => $controller ) {
            // return if file not exists
            if ( ! file_exists( $file_name ) ) {
                continue;
            }

            // include file
            require_once $file_name;

            // check if class exists
            if ( ! class_exists( $controller ) ) {
                continue;
            }

            // get controller object
            $object = new $controller();
            // check if object is instance of WP_REST_Controller
            if ( ! is_a( $object, 'WP_REST_Controller' ) ) {
                continue;
            }

            // register routes
            $object->register_routes();
        }
    }

    /**
     * Prepare object for product response
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function prepare_product_response( $response ) {
        $data = $response->get_data();
        $author_id = get_post_field( 'post_author', $data['id'] );

        $store = dokan()->vendor->get( $author_id );

        $data['store'] = array(
            'id'        => $store->get_id(),
            'name'      => $store->get_name(),
            'shop_name' => $store->get_shop_name(),
            'url'       => $store->get_shop_url(),
            'address'   => $store->get_address(),
        );

        $response->set_data( $data );
        return $response;
    }

    /**
     * If store open close is truned off by admin, unset store_open_colse from api response
     *
     * @param  array $data
     *
     * @since  2.9.13
     *
     * @return array
     */
    public function filter_store_open_close_option( $data ) {
        if ( 'on' !== dokan_get_option( 'store_open_close', 'dokan_appearance', 'on' ) ) {
            unset( $data['store_open_close'] );
        }

        $vendor_id = ! empty( $data['id'] ) ? absint( $data['id'] ) : 0;

        if ( current_user_can( 'manage_woocommerce' ) || $vendor_id === absint( dokan_get_current_user_id() ) ) {
            return $data;
        }

        if ( dokan_is_vendor_info_hidden( 'address' ) ) {
            unset( $data['address'] );
        }

        if ( dokan_is_vendor_info_hidden( 'phone' ) ) {
            unset( $data['phone'] );
        }

        if ( dokan_is_vendor_info_hidden( 'email' ) || empty( $data['show_email'] ) ) {
            unset( $data['email'] );
        }

        return $data;
    }

    /**
     * Send email to admin on adding a new product
     *
     * @param WC_Data $data
     * @param  \WP_REST_Request $request
     * @param  Boolean $creating
     *
     * @return void
     */
    public function on_dokan_rest_insert_product( $data, $request, $creating ) {
        // if not creating, meaning product is updating. So return early
        if ( ! $creating ) {
            return;
        }

        do_action( 'dokan_new_product_added', $data->get_id(), $request->get_params() );
    }

    /**
     * Make payment field hidden in api response for other vendor
     *
     * @param array $data
     *
     * @since 2.9.21
     *
     * @return array
     */
    public function filter_payment_response( $data ) {
        if ( current_user_can( 'manage_woocommerce' ) ) {
            return $data;
        }

        $vendor_id = ! empty( $data['id'] ) ? absint( $data['id'] ) : 0;

        if ( $vendor_id !== dokan_get_current_user_id() ) {
            $data['payment'] = '******';
        }

        return $data;
    }

    /**
     * Generate Rest API class map
     *
     * @since 3.5.1
     *
     * @return void
     */
    private function get_rest_api_class_map() {
        if ( ! empty( $this->class_map ) ) {
            return;
        }
        $this->class_map = apply_filters(
            'dokan_rest_api_class_map', array(
                DOKAN_DIR . '/includes/REST/AdminReportController.php'           => 'WeDevs\Dokan\REST\AdminReportController',
                DOKAN_DIR . '/includes/REST/AdminDashboardController.php'        => 'WeDevs\Dokan\REST\AdminDashboardController',
                DOKAN_DIR . '/includes/REST/AdminMiscController.php'             => 'WeDevs\Dokan\REST\AdminMiscController',
                DOKAN_DIR . '/includes/REST/AdminSetupGuideController.php'       => 'WeDevs\Dokan\REST\AdminSetupGuideController',
                DOKAN_DIR . '/includes/REST/StoreController.php'                 => '\WeDevs\Dokan\REST\StoreController',
                DOKAN_DIR . '/includes/REST/ProductController.php'               => '\WeDevs\Dokan\REST\ProductController',
                DOKAN_DIR . '/includes/REST/ProductControllerV2.php'             => '\WeDevs\Dokan\REST\ProductControllerV2',
                DOKAN_DIR . '/includes/REST/ProductAttributeController.php'      => '\WeDevs\Dokan\REST\ProductAttributeController',
                DOKAN_DIR . '/includes/REST/ProductAttributeTermsController.php' => '\WeDevs\Dokan\REST\ProductAttributeTermsController',
                DOKAN_DIR . '/includes/REST/OrderController.php'                 => '\WeDevs\Dokan\REST\OrderController',
                DOKAN_DIR . '/includes/REST/WithdrawController.php'              => '\WeDevs\Dokan\REST\WithdrawController',
                DOKAN_DIR . '/includes/REST/WithdrawControllerV2.php'            => '\WeDevs\Dokan\REST\WithdrawControllerV2',
                DOKAN_DIR . '/includes/REST/StoreSettingController.php'          => '\WeDevs\Dokan\REST\StoreSettingController',
                DOKAN_DIR . '/includes/REST/AdminNoticeController.php'           => '\WeDevs\Dokan\REST\AdminNoticeController',
                DOKAN_DIR . '/includes/REST/ChangeLogController.php'             => '\WeDevs\Dokan\REST\ChangeLogController',
                DOKAN_DIR . '/includes/REST/DummyDataController.php'             => '\WeDevs\Dokan\REST\DummyDataController',
                DOKAN_DIR . '/includes/REST/OrderControllerV2.php'               => '\WeDevs\Dokan\REST\OrderControllerV2',
                DOKAN_DIR . '/includes/REST/StoreSettingControllerV2.php'        => '\WeDevs\Dokan\REST\StoreSettingControllerV2',
                DOKAN_DIR . '/includes/REST/VendorDashboardController.php'       => '\WeDevs\Dokan\REST\VendorDashboardController',
                DOKAN_DIR . '/includes/REST/ProductBlockController.php'          => '\WeDevs\Dokan\REST\ProductBlockController',
                DOKAN_DIR . '/includes/REST/CommissionControllerV1.php'          => '\WeDevs\Dokan\REST\CommissionControllerV1',
                DOKAN_DIR . '/includes/REST/CustomersController.php'             => '\WeDevs\Dokan\REST\CustomersController',
                DOKAN_DIR . '/includes/REST/DokanDataCountriesController.php'    => '\WeDevs\Dokan\REST\DokanDataCountriesController',
                DOKAN_DIR . '/includes/REST/DokanDataContinentsController.php'   => '\WeDevs\Dokan\REST\DokanDataContinentsController',
                DOKAN_DIR . '/includes/REST/OrderControllerV3.php'               => '\WeDevs\Dokan\REST\OrderControllerV3',
                DOKAN_DIR . '/includes/REST/AdminOnboardingController.php'       => '\WeDevs\Dokan\REST\AdminOnboardingController',
                DOKAN_DIR . '/includes/REST/VendorProductCategoriesController.php'  => '\WeDevs\Dokan\REST\VendorProductCategoriesController',
            )
        );
    }
}
