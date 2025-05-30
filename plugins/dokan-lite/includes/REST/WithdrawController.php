<?php

namespace WeDevs\Dokan\REST;

use Cassandra\Date;
use Exception;
use stdClass;
use WeDevs\Dokan\Cache;
use WeDevs\Dokan\Withdraw\Withdraw;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WeDevs\Dokan\Exceptions\DokanException;
use WeDevs\Dokan\Traits\RESTResponseError;
use WeDevs\Dokan\Admin\WithdrawLogExporter;

class WithdrawController extends WP_REST_Controller {

    use RESTResponseError;

    /**
     * Endpoint namespace.
     *
     * @var string
     */
    protected $namespace = 'dokan/v1';

    /**
     * Route base.
     *
     * @var string
     */
    protected $rest_base = 'withdraw';

    /**
     * Register all routes related with withdraw.
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace, '/' . $this->rest_base, [
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_items' ],
					'args'                => array_merge(
						$this->get_collection_params(), [
							'ids' => [
								'description' => __( 'IDs of withdraws', 'dokan-lite' ),
								'type'        => 'array',
								'context'     => [ 'view' ],
								'items'       => [
									'type'    => 'integer',
								],
							],
                            'is_export' => [
                                'description' => __( 'Is withdraws exportable', 'dokan-lite' ),
                                'type'        => 'boolean',
                                'context'     => [ 'view' ],
                                'default'     => false,
                            ],
						]
					),
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
				],
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [ $this, 'create_item' ],
                    'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
                    'permission_callback' => [ $this, 'create_item_permissions_check' ],
                ],
			]
        );

        register_rest_route(
            $this->namespace, '/' . $this->rest_base . '/balance', [
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_balance' ],
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
				],
			]
        );

        register_rest_route(
            $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)/', [
				'args' => [
					'id' => [
						'description'       => __( 'Unique identifier for the object.', 'dokan-lite' ),
						'type'              => 'integer',
						'validate_callback' => [ $this, 'withdraw_exists' ],
					],
				],
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_item' ],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				[
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'update_item' ],
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
					'permission_callback' => [ $this, 'update_item_permissions_check' ],
				],
				[
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => [ $this, 'delete_item' ],
					'permission_callback' => [ $this, 'delete_item_permissions_check' ],
				],

			]
        );

        register_rest_route(
            $this->namespace, '/' . $this->rest_base . '/payment_methods', [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_payment_method_items' ],
                    'permission_callback' => [ $this, 'get_payment_method_items_permissions_check' ],
                ],
                'schema' => [ $this, 'get_payment_method_item_schema' ],
            ]
        );

        $batch_items_schema = $this->get_public_batch_schema();
        register_rest_route(
            $this->namespace, '/' . $this->rest_base . '/batch', [
				[
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'batch_items' ],
					'permission_callback' => [ $this, 'batch_items_permissions_check' ],
					'args'                => $batch_items_schema['properties'],
				],
				'schema' => [ $this, 'get_public_batch_schema' ],
			]
        );

        register_rest_route(
            $this->namespace, '/' . $this->rest_base . '/charges', [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_all_method_charges' ],
                    'permission_callback' => [ $this, 'get_items_permissions_check' ],
                ],
            ]
        );

        $methods = array_keys( dokan_withdraw_get_methods() );

        register_rest_route(
            $this->namespace, '/' . $this->rest_base . '/charge', [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_method_charge' ],
                    'permission_callback' => [ $this, 'get_items_permissions_check' ],
                    'args'                => [
                        'method' => [
                            'description' => __( 'Withdraw method key', 'dokan-lite' ),
                            'type'        => 'string',
                            'context'     => [ 'view' ],
                            'enum'        => $methods,
                            'required'    => true,
                        ],
                        'amount' => [
                            'description' => __( 'Withdraw amount', 'dokan-lite' ),
                            'type'        => 'number',
                            'context'     => [ 'view' ],
                            'required'    => true,
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Check permission for getting withdraw
     *
     * @since 2.8.0
     *
     * @return bool
     */
    public function get_items_permissions_check( $request ) {
        return current_user_can( 'dokan_manage_withdraw' );
    }

    /**
     * Check permission for get a withdraw
     *
     * @since 3.0.0
     *
     * @param WP_REST_Request $request
     *
     * @return bool
     */
    public function get_item_permissions_check( $request ) {
        if ( current_user_can( 'manage_woocommerce' ) ) {
            return true;
        }

        $withdraw = dokan()->withdraw->get( $request['id'] );

        if ( $withdraw ) {
            $user_id = get_current_user_id();
            return $withdraw->get_user_id() === $user_id;
        }

        return false;
    }

    /**
     * Check permission for creating a withdraw request
     *
     * @since 3.0.0
     *
     * @return bool
     */
    public function create_item_permissions_check( $request ) {
        return current_user_can( 'manage_woocommerce' ) || current_user_can( 'dokan_manage_withdraw' );
    }

    /**
     * Check permission for update a withdraw
     *
     * @since 3.0.0
     *
     * @param WP_REST_Request $request
     *
     * @return bool
     */
    public function update_item_permissions_check( $request ) {
        return $this->get_item_permissions_check( $request );
    }

    /**
     * Check permission for deleting withdraw
     *
     * @since 2.8.0
     *
     * @return bool
     */
    public function delete_item_permissions_check( $request ) {
        return current_user_can( 'manage_woocommerce' );
    }

    /**
     * Check Permission for Wthdraw Payment Method Items.
     *
     * @since 3.8.3
     *
     * @return bool
     */
    public function get_payment_method_items_permissions_check( $request ) {
        return current_user_can( 'dokan_manage_withdraw' );
    }

    /**
     * Check permission for getting withdraw
     *
     * @since 2.8.0
     *
     * @return bool
     */
    public function batch_items_permissions_check() {
        return current_user_can( 'manage_woocommerce' );
    }

    /**
     * Validate a withdraw is exists
     *
     * @since 3.0.0
     *
     * @param int $id
     *
     * @return bool|\WP_Error
     */
    public function withdraw_exists( $id ) {
        try {
            $withdraw = dokan()->withdraw->get( $id );

            if ( $withdraw ) {
                return true;
            }

            throw new DokanException( 'dokan_rest_withdraw_error', __( 'Withdraw not found', 'dokan-lite' ), 404 );
        } catch ( Exception $e ) {
            return $this->send_response_error( $e );
        }
    }

    /**
     * Get user data
     *
     * @since 2.8.0
     *
     * @return array
     */
    public function get_user_data( $user_id ) {
        $vendor = dokan()->vendor->get( $user_id );

        return $vendor->to_array();
    }

    /**
     * Get withdraws
     *
     * @since 3.0.0
     *
     * @return WP_REST_Response|WP_Error
     */
    public function get_items( $request ) {
        $args = [
            'status'   => dokan()->withdraw->get_status_code( $request['status'] ),
            'paginate' => true,
            'page'     => $request['page'],
            'limit'    => $request['per_page'],
            'orderby'  => 'DESC',
        ];

        $user_id = null;

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            // Vendors can only see their own requests
            $user_id = dokan_get_current_user_id();

            if ( empty( $user_id ) ) {
                return new WP_Error( 'dokan_rest_withdraw_no_vendor_found', __( 'No vendor found', 'dokan-lite' ), [ 'status' => 404 ] );
            }
        } elseif ( isset( $request['user_id'] ) ) {
            // Allow manager to filter request with user or vendor id
            $user_id = $request['user_id'];
        }

        if ( $user_id ) {
            $args['user_id'] = $user_id;
        }

        if ( isset( $request['ids'] ) ) {
            $args['ids'] = $request['ids'];
        }

        if ( ! empty( $request['payment_method'] ) ) {
            $args['method'] = $request['payment_method'];
        }

        if ( ! empty( $request['start_date'] ) && ! empty( $request['end_date'] ) ) {
            $args['start_date'] = $request['start_date'];
            $args['end_date']   = $request['end_date'];
        }

        $withdraws = dokan()->withdraw->all( $args );
        $data      = [];
        foreach ( $withdraws->withdraws as $withdraw ) {
            $item   = $this->prepare_item_for_response( $withdraw, $request );
            $data[] = $this->prepare_response_for_collection( $item );
        }

        // Get withdraw counts.
        $args['return'] = 'count';
        $withdraw_count = dokan()->withdraw->all( $args );

        // Check if not exportable.
        if ( empty( $request['is_export'] ) ) {
            $response = rest_ensure_response( $data );

            $response->header( 'X-Status-Pending', $withdraw_count['pending'] );
            $response->header( 'X-Status-Completed', $withdraw_count['completed'] );
            $response->header( 'X-Status-Cancelled', $withdraw_count['cancelled'] );

            return $this->format_collection_response( $response, $request, $withdraws->total );
        }

        // Export items.
        $exporter = new WithdrawLogExporter();
        $step     = isset( $params['page'] ) ? absint( $params['page'] ) : 1;
        $statuses = [ 'pending', 'completed', 'cancelled' ];

        $exporter->set_items( $data );
        $exporter->set_page( $step );
        $exporter->set_limit( $args['limit'] );
        $exporter->set_total_rows( $statuses[ $args['status'] ] );
        $exporter->generate_file();

        $percent     = $exporter->get_percent_complete();
        $export_data = [
            'percentage' => $percent,
        ];

        if ( $percent >= 100 ) {
            $export_data['step'] = 'done';
            $export_data['url']  = add_query_arg(
                [
                    'download-withdraw-log-csv'  => wp_create_nonce( 'download-withdraw-log-csv-nonce' ),
                ],
                admin_url( 'admin.php' )
            );
        } else {
            $export_data['step']       = ++$step;
            $export_data['percentage'] = $exporter->get_percent_complete();
            $export_data['columns']    = $exporter->get_column_names();
        }

        return rest_ensure_response( $export_data );
    }

    /**
     * Get vendor balance
     *
     * @return WP_REST_Response
     */
    public function get_balance() {
        $data = [];

        $last_withdraw = dokan()->withdraw->get_withdraw_requests(
            dokan_get_current_user_id(),
            dokan()->withdraw->get_status_code( 'approved' ),
            1
        );
        $last_withdraw   = reset( $last_withdraw );

        if ( is_a( $last_withdraw, \WeDevs\Dokan\Withdraw\Withdraw::class ) ) {
            $last_withdraw = $last_withdraw->get_withdraw();
            $last_withdraw['details'] = isset( $last_withdraw['details'] ) ? maybe_unserialize( $last_withdraw['details'] ) : [];
            $last_withdraw['method_title'] = isset( $last_withdraw['method'] ) ? dokan_withdraw_get_method_title( $last_withdraw['method'] ) : '';
        }

        $data['current_balance']    = dokan_get_seller_balance( dokan_get_current_user_id(), false );
        $data['withdraw_limit']     = dokan_get_option( 'withdraw_limit', 'dokan_withdraw', 0 );
        $data['withdraw_threshold'] = dokan_get_withdraw_threshold( dokan_get_current_user_id() );
        $data['withdraw_methods']   = array_filter( dokan_get_seller_active_withdraw_methods( dokan_get_current_user_id() ) );
        $data['last_withdraw']      = is_array( $last_withdraw ) ? $last_withdraw : new stdClass();

        return rest_ensure_response( $data );
    }
    /**
     * Make a withdraw request
     *
     * @since 3.0.0
     *
     * @return WP_REST_Response
     */
    public function create_item( $request ) {
        try {
            // vendors are only allowed to set amount and method params
            $user_id = dokan_get_current_user_id();

            if ( current_user_can( 'manage_woocommerce' ) ) {
                // A user could be both wc manager and a vendor. In that case,
                // we should allow to set `user_id` param for that manager.
                if ( isset( $request['user_id'] ) ) {
                    $user_id = $request['user_id'];

                    if ( ! user_can( $user_id, 'dokan_manage_withdraw' ) ) {
                        throw new DokanException( 'dokan_rest_withdraw_error', __( 'User does not have permission to withdraw', 'dokan-lite' ), 401 );
                    }
                }

                $note = $request['note'];
            } else {
                $note = '';
            }

            if ( empty( $user_id ) ) {
                throw new DokanException( 'dokan_rest_withdraw_error', __( 'No vendor found', 'dokan-lite' ), 404 );
            }

            if ( dokan()->withdraw->has_pending_request( $user_id ) ) {
                throw new DokanException( 'dokan_rest_withdraw_error', __( 'You already have a pending withdraw request', 'dokan-lite' ), 400 );
            }

            $validate_request = dokan()->withdraw->is_valid_approval_request(
                [
			        'user_id' => $user_id,
			        'amount'  => $request['amount'],
			        'method'  => $request['method'],
				]
            );

            if ( is_wp_error( $validate_request ) ) {
                throw new DokanException( 'dokan_rest_withdraw_error', $validate_request->get_error_message(), 400 );
            }

            $withdraw = new Withdraw();

            $withdraw
                ->set_user_id( $user_id )
                ->set_amount( $request['amount'] )
                ->set_date( dokan_current_datetime()->format( 'Y-m-d H:i:s' ) )
                ->set_status( dokan()->withdraw->get_status_code( 'pending' ) )
                ->set_method( $request['method'] )
                ->set_ip( dokan_get_client_ip() )
                ->set_note( $note );

            $result = $withdraw->save();

            if ( is_wp_error( $result ) ) {
                throw new DokanException( $result->get_error_code(), $result->get_error_message(), 400 );
            }

            $response = $this->prepare_item_for_response( $result, $request );
            $response = rest_ensure_response( $response );

            $response->set_status( 201 );
            $response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $withdraw->get_id() ) ) );

            return $response;
        } catch ( Exception $e ) {
            return $this->send_response_error( $e );
        }
    }

    /**
     * Get a withdraw
     *
     * @since 3.0.0
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response
     */
    public function get_item( $request ) {
        // No need to try-catch here, `withdraw_exists` will handle that
        $withdraw = dokan()->withdraw->get( $request['id'] );
        $data     = $this->prepare_item_for_response( $withdraw, $request );

        return rest_ensure_response( $data );
    }

    /**
     * Cancel withdraw status
     *
     * @since 3.0.0
     *
     * @return WP_REST_Response|WP_Error
     */
    public function update_item( $request ) {
        try {
            /**
             * @var $withdraw_manager \WeDevs\Dokan\Withdraw\Manager()
             */
            $withdraw_manager = dokan()->withdraw;

            /**
             * @var $withdraw \WeDevs\Dokan\Withdraw\Withdraw()
             */
            $withdraw = $withdraw_manager->get( $request['id'] );

            // phpcs:ignore WordPress.WP.Capabilities.Unknown
            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                $validate_request = $withdraw_manager->is_valid_cancellation_request( $withdraw->get_withdraw() );

                if ( is_wp_error( $validate_request ) ) {
                    throw new DokanException( 'dokan_rest_withdraw_error', $validate_request->get_error_message(), 422 );
                }

                // Vendors are allowed to cancel request only
                $withdraw->set_status( $withdraw_manager->get_status_code( 'cancelled' ) );
            } else {
                if ( isset( $request['user_id'] ) ) {
                    $withdraw->set_user_id( $request['user_id'] );
                }

                if ( isset( $request['amount'] ) ) {
                    $withdraw->set_amount( $request['amount'] );
                }

                if ( isset( $request['method'] ) ) {
                    $withdraw->set_method( $request['method'] );
                }

                if ( isset( $request['note'] ) ) {
                    $withdraw->set_note( $request['note'] );
                }

                /**
                 * We are temporarily setting the withdrawal status null because in the `is_valid_approval_request` function
                 * it throws error as 'Withdraw is already approved.' when status is 'approved'. If we don't set null we can't validate
                 * the vendor's withdraw balance and withdrawal limit. If all this information validates properly we will set the real status code
                 * that came from request.
                 */
                if ( isset( $request['status'] ) ) {
                    $withdraw->set_status( $withdraw_manager->get_status_code( null ) );
                }

                $validate_request = $withdraw_manager->is_valid_approval_request( $withdraw->get_withdraw() );

                if ( is_wp_error( $validate_request ) ) {
                    throw new DokanException( 'dokan_rest_withdraw_error', $validate_request->get_error_message(), 400 );
                }

                /**
                 * Setting the real status code that came from request.
                 */
                if ( isset( $request['status'] ) ) {
                    $withdraw->set_status( $withdraw_manager->get_status_code( $request['status'] ) );
                }
            }

            $withdraw = $withdraw->save();

            if ( is_wp_error( $withdraw ) ) {
                throw new DokanException( $withdraw->get_error_code(), $withdraw->get_error_message(), 500 );
            }

            $response = $this->prepare_item_for_response( $withdraw, $request );

            return rest_ensure_response( $response );
        } catch ( Exception $e ) {
            return $this->send_response_error( $e );
        }
    }

    /**
     * Delete a withdraw
     *
     * @since 3.0.0
     *
     * @param  WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error
     */
    public function delete_item( $request ) {
        try {
            $withdraw = dokan()->withdraw->get( $request['id'] );

            $withdraw = $withdraw->delete();

            if ( is_wp_error( $withdraw ) ) {
                throw new DokanException( $withdraw->get_error_code(), $withdraw->get_error_message(), 500 );
            }

            $response = $this->prepare_item_for_response( $withdraw, $request );

            return rest_ensure_response( $response );
        } catch ( Exception $e ) {
            return $this->send_response_error( $e );
        }
    }

    /**
     * Get Withdraw Payment Method Items.
     *
     * @since 3.8.3
     *
     * @param  WP_REST_Request $request
     *
     * @return WP_REST_Response
     */
    public function get_payment_method_items( $request ) {
        global $wpdb;

        $payment_methods = $wpdb->get_col( "SELECT DISTINCT method FROM {$wpdb->prefix}dokan_withdraw" ); //phpcs:ignore
        $data            = [];

        foreach ( $payment_methods as $payment_method ) {
            $item   = $this->prepare_payment_method_item_for_response( $payment_method, $request );
            $data[] = $this->prepare_response_for_collection( $item );
        }

        return rest_ensure_response( $data );
    }

    /**
     * Approve, Pending and cancel bulk action
     *
     * JSON data format for sending to API
     *     {
     *         "approved" : [
     *             "1", "9", "7"
     *         ],
     *         "pending" : [
     *             "2"
     *         ],
     *         "delete" : [
     *             "4"
     *         ],
     *         "cancelled" : [
     *             "5"
     *         ]
     *     }
     *
     * @since 2.8.0
     *
     * @return WP_REST_Response|WP_Error
     */
    public function batch_items( $request ) {
        $success = [];
        $failed  = [];

        foreach ( $request['approved'] as $id ) {
            $withdraw = dokan()->withdraw->get( $id );

            if ( ! $withdraw ) {
                $failed['approved'][] = $id;
            } else {
                $validate_request = dokan()->withdraw->is_valid_approval_request( $withdraw->get_withdraw() );

                if ( is_wp_error( $validate_request ) ) {
                    $failed['approved'][] = $id;
                } else {
                    $withdraw->set_status( dokan()->withdraw->get_status_code( 'approved' ) );
                    $withdraw = $withdraw->save();

                    if ( is_wp_error( $withdraw ) ) {
                        $failed['approved'][] = $id;
                    } else {
                        $success['approved'][] = $id;
                    }
                }
            }
        }

        foreach ( $request['cancelled'] as $id ) {
            $withdraw = dokan()->withdraw->get( $id );

            if ( ! $withdraw ) {
                $failed['cancelled'][] = $id;
            } else {
                $validate_request = dokan()->withdraw->is_valid_cancellation_request( $withdraw->get_withdraw() );

                if ( is_wp_error( $validate_request ) ) {
                    $failed['cancelled'][] = $id;
                } else {
                    $withdraw->set_status( dokan()->withdraw->get_status_code( 'cancelled' ) );
                    $withdraw = $withdraw->save();

                    if ( is_wp_error( $withdraw ) ) {
                        $failed['cancelled'][] = $id;
                    } else {
                        $success['cancelled'][] = $id;
                    }
                }
            }
        }

        foreach ( $request['delete'] as $id ) {
            $withdraw = dokan()->withdraw->get( $id );

            if ( ! $withdraw ) {
                $failed['delete'][] = $id;
            } else {
                $withdraw = $withdraw->delete();

                if ( is_wp_error( $withdraw ) ) {
                    $failed['delete'][] = $id;
                } else {
                    $success['delete'][] = $id;
                }
            }
        }

        return rest_ensure_response(
            [
				'success' => $success,
				'failed'  => $failed,
			]
        );
    }

    /**
     * Get all withdraw method charges.
     *
     * @since 3.9.6
     *
     * @return WP_Error|\WP_HTTP_Response|WP_REST_Response
     */
    public function get_all_method_charges() {
        $all_charges = dokan_withdraw_get_method_charges();

        return rest_ensure_response( $all_charges );
    }

    /**
     * Get withdraw method charge.
     *
     * @since 3.9.6
     *
     * @return WP_Error|\WP_HTTP_Response|WP_REST_Response
     */
    public function get_method_charge( $request ) {
        $method = $request->get_param( 'method' );
        $amount = wc_format_decimal( $request->get_param( 'amount' ) );

        $withdraw = new Withdraw();

        $withdraw->set_method( $method )
                ->set_amount( $amount )
                ->calculate_charge();

        $response = [
            'charge'      => $withdraw->get_charge(),
            'receivable'  => $withdraw->get_receivable_amount(),
            'charge_data' => $withdraw->get_charge_data(),
        ];

        if ( $withdraw->get_receivable_amount() < 0 ) {
            return new WP_Error(
                'invalid-withdraw-amount',
                __( 'Invalid withdraw amount. The withdraw charge is greater than the withdraw amount', 'dokan-lite' ),
                $response
            );
        }

        return rest_ensure_response( $response );
    }

    /**
     * Prepare data for response
     *
     * @since 2.8.0
     *
     * @param $withdraw \WeDevs\Dokan\Withdraw\Withdraw
     * @param $request \WP_REST_Request
     *
     * @return WP_REST_Response|WP_Error
     */
    public function prepare_item_for_response( $withdraw, $request ) {
        $vendor = $this->get_user_data( $withdraw->get_user_id() );

        if ( empty( $vendor ) ) {
            return;
        }

        $methods     = dokan_withdraw_get_methods();
        $get_details = ! empty( $withdraw->get_details() ) ? maybe_unserialize( $withdraw->get_details() ) : array();
        $details     = $vendor['payment'];

        if ( version_compare( DOKAN_PLUGIN_VERSION, '3.2.11', '>' ) && ! empty( $get_details ) ) {
            $details = [ $withdraw->get_method() => $get_details ];
        }

        $data = [
            'id'           => absint( $withdraw->get_id() ),
            'user'         => $vendor,
            'amount'       => floatval( $withdraw->get_amount() ),
            'created'      => mysql_to_rfc3339( $withdraw->get_date() ),
            'status'       => dokan()->withdraw->get_status_name( $withdraw->get_status() ),
            'method'       => $withdraw->get_method(),
            'method_title' => array_key_exists( $withdraw->get_method(), $methods ) ? $methods[ $withdraw->get_method() ] : $withdraw->get_method(),
            'note'         => $withdraw->get_note(),
            'details'      => $details,
            'ip'           => $withdraw->get_ip(),
            'charge'       => $withdraw->get_charge(),
            'receivable'   => $withdraw->get_receivable_amount(),
            'charge_data'  => $withdraw->get_charge_data(),
        ];

        $response = rest_ensure_response( $data );
        $response->add_links( $this->prepare_links( $withdraw, $request ) );

        return apply_filters( 'dokan_rest_prepare_withdraw_object', $response, $withdraw, $request );
    }

    /**
     * Prepare Payment Method Item Data for Response.
     *
     * @since 3.8.3
     *
     * @param string          $payment_method
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error
     */
    public function prepare_payment_method_item_for_response( $payment_method, $request ) {
        $data = [
            'id'    => $payment_method,
            'title' => dokan_withdraw_get_method_title( $payment_method ),
        ];

        $response = rest_ensure_response( $data );

        return apply_filters( 'dokan_rest_prepare_withdraw_payment_method_object', $response, $payment_method, $request );
    }

    /**
     * Format item's collection for response
     *
     * @param  WP_REST_Response $response
     * @param  WP_REST_Request $request
     * @param  array $items
     * @param  int $total_items
     *
     * @return object
     */
    public function format_collection_response( $response, $request, $total_items ) {
        if ( $total_items === 0 ) {
            return $response;
        }

        // Store pagation values for headers then unset for count query.
        $per_page = (int) ( ! empty( $request['per_page'] ) ? $request['per_page'] : 20 );
        $page     = (int) ( ! empty( $request['page'] ) ? $request['page'] : 1 );

        $response->header( 'X-WP-Total', (int) $total_items );

        $max_pages = ceil( $total_items / $per_page );

        $response->header( 'X-WP-TotalPages', (int) $max_pages );
        $base = add_query_arg( $request->get_query_params(), rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );

        if ( $page > 1 ) {
            $prev_page = $page - 1;

            if ( $prev_page > $max_pages ) {
                $prev_page = $max_pages;
            }

            $prev_link = add_query_arg( 'page', $prev_page, $base );
            $response->link_header( 'prev', $prev_link );
        }

        if ( $max_pages > $page ) {
            $next_page = $page + 1;
            $next_link = add_query_arg( 'page', $next_page, $base );
            $response->link_header( 'next', $next_link );
        }

        return $response;
    }

    /**
     * Prepare links for the request.
     *
     * @param \WeDevs\Dokan\Withdraw\Withdraw         $object  Object data.
     * @param WP_REST_Request $request                Request object.
     *
     * @return array Links for the given post.
     */
    protected function prepare_links( $withdraw, $request ) {
        $links = [
            'self' => [
                'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $withdraw->get_id() ) ),
            ],
            'collection' => [
                'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ),
            ],
        ];

        return $links;
    }

    /**
     * Item schema
     *
     * @since DOKAN_LITE
     *
     * @return array
     */
    public function get_item_schema() {
        $schema = [
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => 'Withdraw',
            'type'       => 'object',
            'properties' => [
                'id' => [
                    'description' => __( 'Unique identifier for the object.', 'dokan-lite' ),
                    'type'        => 'integer',
                    'context'     => [ 'view', 'edit' ],
                    'readonly'    => true,
                ],
                'user' => [
                    'description' => __( 'Requested User', 'dokan-lite' ),
                    'type'        => 'object',
                    'context'     => [ 'view' ],
                    'readonly'    => true,
                ],
                'user_id' => [
                    'required'    => false,
                    'description' => __( 'Requested User ID', 'dokan-lite' ),
                    'type'        => 'integer',
                    'context'     => [ 'edit' ],
                ],
                'amount' => [
                    'required'    => true,
                    'description' => __( 'The amount of discount. Should always be numeric, even if setting a percentage.', 'dokan-lite' ),
                    'type'        => 'number',
                    'context'     => [ 'view', 'edit' ],
                ],
                'created_date' => [
                    'description' => __( "The date the withdraw request has beed created in the site's timezone.", 'dokan-lite' ),
                    'type'        => 'date-time',
                    'context'     => [ 'view' ],
                    'readonly'    => true,
                ],
                'status' => [
                    'required'    => false,
                    'description' => __( 'Withdraw status', 'dokan-lite' ),
                    'type'        => 'string',
                    'enum'        => [ 'pending', 'approved', 'cancelled' ],
                    'context'     => [ 'view', 'edit' ],
                    'default'     => 'pending',
                ],
                'method' => [
                    'required'    => true,
                    'description' => __( 'Withdraw Method', 'dokan-lite' ),
                    'type'        => 'string',
                    'enum'        => array_keys( dokan_withdraw_register_methods() ),
                    'context'     => [ 'view', 'edit' ],
                ],
                'note'   => [
                    'required'    => false,
                    'description' => __( 'Withdraw Notes', 'dokan-lite' ),
                    'type'        => 'string',
                    'context'     => [ 'view', 'edit' ],
                    'default'     => '',
                ],
                'ip'     => [
                    'description' => __( 'User IP', 'dokan-lite' ),
                    'type'        => 'string',
                    'context'     => [ 'view' ],
                    'readonly'    => true,
                ],
            ],
        ];

        return $this->add_additional_fields_schema( $schema );
    }

    /**
     * Item Schema for Withdraw Payment Methods.
     *
     * @since DOKAN_LITE
     *
     * @return array
     */
    public function get_payment_method_item_schema() {
        $schema = [
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => 'Withdraw Payment Methods',
            'type'       => 'object',
            'properties' => [
                'id' => [
                    'description' => __( 'Unique identifier for the payment method.', 'dokan-lite' ),
                    'type'        => 'string',
                    'context'     => [ 'view' ],
                    'readonly'    => true,
                    'required'    => true,
                ],
                'title' => [
                    'description' => __( 'Title for the payment method.', 'dokan-lite' ),
                    'type'        => 'string',
                    'context'     => [ 'view' ],
                    'readonly'    => true,
                    'required'    => false,
                ],
            ],
        ];

        return $this->add_additional_fields_schema( $schema );
    }

    /**
     * Schema for batch processing
     *
     * @since 3.0.0
     *
     * @return array
     */
    public function get_public_batch_schema() {
        $schema = [
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => 'batch',
            'type'       => 'object',
            'properties' => [
                'approved'  => [
                    'required'    => false,
                    'description' => __( 'List of withdraw IDs to be approved', 'dokan-lite' ),
                    'type'        => 'array',
                    'context'     => [ 'edit' ],
                    'default'     => [],
                    'items'       => [
                        'type' => 'integer',
                    ],
                ],
                'cancelled' => [
                    'required'    => false,
                    'description' => __( 'List of withdraw IDs to be cancelled', 'dokan-lite' ),
                    'type'        => 'array',
                    'context'     => [ 'edit' ],
                    'default'     => [],
                    'items'       => [
                        'type' => 'integer',
                    ],
                ],
                'delete'    => [
                    'required'    => false,
                    'description' => __( 'List of withdraw IDs to be deleted', 'dokan-lite' ),
                    'type'        => 'array',
                    'context'     => [ 'edit' ],
                    'default'     => [],
                    'items'       => [
                        'type' => 'integer',
                    ],
                ],
            ],
        ];

        return $schema;
    }
}
