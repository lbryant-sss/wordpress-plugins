<?php
/**
 * Top Five Discounts list table.
 *
 * @package     EDD\Reports\Data\Discounts
 * @copyright   Copyright (c) 2018, Easy Digital Downloads, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

namespace EDD\Reports\Data\Discounts;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit; // @codeCoverageIgnore

use EDD\Reports;
use EDD\Stats;
use EDD\Admin\List_Table;

/**
 * Top_Five_Discounts_List_Table class.
 *
 * @since 3.0
 */
class Top_Five_Discounts_List_Table extends List_Table {

	/**
	 * Query the database and fetch the top five discounts.
	 *
	 * @since 3.0
	 *
	 * @return array $data Discounts.
	 */
	public function get_data() {
		$filter = Reports\get_filter_value( 'dates' );

		$stats = new Stats();

		$discounts = $stats->get_most_popular_discounts(
			array(
				'number' => 5,
				'range'  => $filter['range'],
			)
		);

		$data = array();

		foreach ( $discounts as $result ) {
			if ( empty( $result->object ) ) {
				continue;
			}

			$object            = new \stdClass();
			$object->id        = $result->object->id;
			$object->name      = $result->object->name;
			$object->status    = $result->object->status;
			$object->use_count = $result->count;
			$object->code      = $result->object->code;
			$object->type      = $result->object->type;
			$object->amount    = $result->object->amount;

			$data[] = $object;
		}

		return $data;
	}

	/**
	 * Retrieve the table columns.
	 *
	 * @since 3.0
	 *
	 * @return array $columns Array of all the list table columns
	 */
	public function get_columns() {
		return array(
			'name'      => __( 'Name', 'easy-digital-downloads' ),
			'code'      => __( 'Code', 'easy-digital-downloads' ),
			'use_count' => __( 'Uses', 'easy-digital-downloads' ),
			'amount'    => __( 'Amount', 'easy-digital-downloads' ),
		);
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @since 3.0
	 *
	 * @param \stdClass $discount Discount object.
	 * @param string    $column_name The name of the column.
	 *
	 * @return string Column Name
	 */
	public function column_default( $discount, $column_name ) {
		return property_exists( $discount, $column_name ) ? $discount->$column_name : '';
	}

	/**
	 * This function renders the amount column.
	 *
	 * @access public
	 * @since 3.0
	 *
	 * @param \stdClass $discount Data for the discount code.
	 * @return string Formatted amount.
	 */
	public function column_amount( $discount ) {
		return edd_format_discount_rate( $discount->type, $discount->amount );
	}

	/**
	 * Render the Name Column
	 *
	 * @since 3.0
	 *
	 * @param \stdClass $discount Discount object.
	 * @return string Data shown in the Name column.
	 */
	public function column_name( $discount ) {

		// Bail if current user cannot manage discounts.
		if ( ! current_user_can( 'manage_shop_discounts' ) ) {
			return '';
		}

		// State.
		$state = '';
		if ( 'active' !== $discount->status ) {
			$state = ' &mdash; ' . edd_get_discount_status_label( $discount->id );
		}

		return sprintf(
			'<a class="row-title" href="%s">%s</a>%s',
			esc_url(
				add_query_arg(
					array(
						'view'     => 'edit_discount',
						'discount' => absint( $discount->id ),
					),
					$this->get_base_url()
				)
			),
			stripslashes( $discount->name ),
			esc_html( $state )
		);
	}

	/**
	 * Setup the final data for the table.
	 *
	 * @since 3.0
	 */
	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $this->get_data();
	}

	/**
	 * Get the base URL for the discount list table
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	public function get_base_url() {

		// Remove some query arguments.
		$base = remove_query_arg( edd_admin_removable_query_args(), edd_get_admin_base_url() );

		// Add base query args.
		return add_query_arg(
			array(
				'page' => 'edd-discounts',
			),
			$base
		);
	}

	/**
	 * Message to be displayed when there are no items
	 *
	 * @since 3.0
	 */
	public function no_items() {
		esc_html_e( 'No discounts found.', 'easy-digital-downloads' );
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @since 3.0
	 * @access protected
	 *
	 * @return string Name of the primary column.
	 */
	protected function get_primary_column_name() {
		return 'name';
	}

	/**
	 * Return empty array to disable sorting.
	 *
	 * @since 3.0
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return array();
	}

	/**
	 * Return empty array to remove bulk actions.
	 *
	 * @since 3.0
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return array();
	}

	/**
	 * Hide pagination.
	 *
	 * @since 3.0
	 *
	 * @param string $which Which pagination to show.
	 */
	protected function pagination( $which ) {}

	/**
	 * Hide table navigation.
	 *
	 * @since 3.0
	 *
	 * @param string $which Which pagination to show.
	 */
	protected function display_tablenav( $which ) {}
}
