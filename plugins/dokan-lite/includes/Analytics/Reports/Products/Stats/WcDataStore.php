<?php

namespace WeDevs\Dokan\Analytics\Reports\Products\Stats;

use Automattic\WooCommerce\Admin\API\Reports\Products\Stats\DataStore as ProductStatsDataStore;
use WeDevs\Dokan\Analytics\Reports\WcSqlQuery;

/**
 * DataStore class to override the default handling of WC SQL clauses.
 *
 * @since 3.13.0
 */
class WcDataStore extends ProductStatsDataStore {
	/**
	 * Override the $total_query and $interval_query properties to customize query behavior.
	 * This modification replaces the Automattic\WooCommerce\Admin\API\Reports\SqlQuery class with
	 * WeDevs\Dokan\Analytics\Reports\WcSqlQuery to apply specific filters to the queries.
	 * The change is necessary because the "get_sql_clause" method's second parameter defaults to "unfiltered,"
	 * which prevents the filters required to add JOIN and WHERE clauses for the dokan_order_stats table.
	 *
	 * @return void
	 */
	protected function initialize_queries() {
		$this->clear_all_clauses();
		$this->total_query = new WcSqlQuery( $this->context . '_total' );
		$this->total_query->add_sql_clause( 'from', self::get_db_table_name() );

		$this->interval_query = new WcSqlQuery( $this->context . '_interval' );
		$this->interval_query->add_sql_clause( 'from', self::get_db_table_name() );
		$this->interval_query->add_sql_clause( 'group_by', 'time_interval' );
	}
}
