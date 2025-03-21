<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// WP_List_Table is not loaded automatically in the plugins section
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


/*
 * Extent WP default list table for our custom members section
 *
 */
Class PMS_Member_Payments_List_Table extends WP_List_Table {


    /*
     * Constructor function
     *
     */
    public function __construct() {

        global $pagenow, $wp_importers, $hook_suffix, $plugin_page, $typenow, $taxnow;
        $page_hook = get_plugin_page_hook($plugin_page, $plugin_page);

        parent::__construct( array(
            'singular'  => 'member-payment',
            'plural'    => 'member-payments',
            'ajax'      => false,

            // Screen is a must!
            'screen'    => $page_hook
        ));

    }


    /*
     * Overwrites the parent class.
     * Define the columns for the members
     *
     * @return array
     *
     */
    public function get_columns() {

        $columns = array(
            'subscription_plan' => __( 'Subscription', 'paid-member-subscriptions' ),
            'amount'            => __( 'Amount', 'paid-member-subscriptions' ),
            'date'              => __( 'Date', 'paid-member-subscriptions' ),
            'status'   			=> __( 'Status', 'paid-member-subscriptions' ),
            'actions'           => __( 'Actions', 'paid-member-subscriptions' )
        );

        return apply_filters( 'pms_member_payments_list_table_columns', $columns );

    }


    /*
     * Overwrites the parent class.
     * Define which columns to hide
     *
     * @return array
     *
     */
    public function get_hidden_columns() {

        return array();

    }


    /*
     * Overwrites the parent class.
     * Define which columns are sortable
     *
     * @return array
     *
     */
    public function get_sortable_columns() {

        return array();

    }


    /*
     * Returns the table data
     *
     * @return array
     *
     */
    public function get_table_data() {

        $data = array();

        if( !isset( $_REQUEST['member_id'] ) )
            return $data;

        $args = array(
            'user_id' => (int)sanitize_text_field( $_REQUEST['member_id'] ),
            'number'   => 10
        );

        $payments = pms_get_payments( $args );

        foreach( $payments as $payment ) {

            $subscription_plan = pms_get_subscription_plan( $payment->subscription_id );
            $payment_currency = !empty( $payment->currency ) ? $payment->currency : pms_get_active_currency();

            $data[] = apply_filters( 'pms_member_payments_list_table_entry_data', array(
                'subscription_plan' => $subscription_plan->name,
                'amount'            => pms_format_price( $payment->amount, pms_get_currency_symbol( $payment_currency ) ),
                'date'              => apply_filters('pms_match_date_format_to_wp_settings', ucfirst( date_i18n( 'F d, Y H:i:s', strtotime( $payment->date ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ) ), true),
                'status'            => ucfirst( $payment->status ),
                'actions'           => $payment->id
            ), $payment );
            
        }

        return $data;

    }


    /*
     * Populates the items for the table
     *
     */
    public function prepare_items() {

        $columns = $this->get_columns();
        $hidden_columns = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $data = $this->get_table_data();

        $this->_column_headers = array( $columns, $hidden_columns, $sortable );
        $this->items = $data;

    }


    /*
     * Return data that will be displayed in each column
     *
     * @param array $item           - data for the current row
     * @param string $column_name   - name of the current column
     *
     * @return string
     *
     */
    public function column_default( $item, $column_name ) {

        if( !isset( $item[ $column_name ] ) )
            return false;

        return $item[ $column_name ];

    }


    /*
     * Return data that will be displayed in the actions column
     *
     * @param array $item   - data of the current row
     *
     * @return string
     *
     */
    public function column_actions( $item ) {

        $actions = '<a class="button-secondary" href="' . esc_url( add_query_arg( array( 'page' => 'pms-payments-page', 'pms-action' => 'edit_payment', 'payment_id' => $item['actions'] ), admin_url( 'admin.php' ) ) ) . '">' . __( 'View Details', 'paid-member-subscriptions' ) . '</a>';

        return apply_filters( 'pms_member_payments_list_table_actions', $actions, $item );

    }


    /*
     * Display if no items are found
     *
     */
    public function no_items() {

        echo esc_html__( 'No payments found', 'paid-member-subscriptions' );

    }

}
