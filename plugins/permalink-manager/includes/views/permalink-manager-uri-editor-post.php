<?php

/**
 * Use WP_List_Table to display the "Bulk URI Editor" for post items
 */
class Permalink_Manager_URI_Editor_Post extends WP_List_Table {

	public $displayed_post_types, $displayed_post_statuses;

	public function __construct() {
		global $permalink_manager_options, $active_subsection;

		parent::__construct( array(
			'singular' => 'slug',
			'plural'   => 'slugs'
		) );

		$this->displayed_post_statuses = ( isset( $permalink_manager_options['screen-options']['post_statuses'] ) ) ? "'" . implode( "', '", $permalink_manager_options['screen-options']['post_statuses'] ) . "'" : "'no-post-status'";
		$this->displayed_post_types    = ( $active_subsection == 'all' ) ? "'" . implode( "', '", $permalink_manager_options['screen-options']['post_types'] ) . "'" : "'{$active_subsection}'";
	}

	/**
	 * Get the HTML output with the whole WP_List_Table
	 *
	 * @return string
	 */
	public function display_admin_section() {
		$output = "<form id=\"permalinks-post-types-table\" class=\"slugs-table\" method=\"post\">";
		$output .= wp_nonce_field( 'permalink-manager', 'uri_editor' );
		$output .= Permalink_Manager_UI_Elements::generate_option_field( 'pm_session_id', array( 'value' => uniqid(), 'type' => 'hidden' ) );

		// Bypass
		ob_start();

		$this->prepare_items();
		$this->display();
		$output .= ob_get_contents();

		ob_end_clean();

		$output .= "</form>";

		return $output;
	}

	/**
	 * Return an array of classes to be used in the HTML table
	 *
	 * @return array
	 */
	function get_table_classes() {
		return array( 'widefat', 'striped', $this->_args['plural'] );
	}

	/**
	 * Add columns to the table
	 *
	 * @return array
	 */
	public function get_columns() {
		return apply_filters( 'permalink_manager_uri_editor_columns', array(
			'item_title' => __( 'Post title', 'permalink-manager' ),
			'item_uri'   => __( 'Custom permalink', 'permalink-manager' )
		) );
	}

	/**
	 * Define sortable columns
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'item_title' => array( 'post_title', false )
		);
	}

	/**
	 * Data inside the columns
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		global $permalink_manager_options;

		if ( Permalink_Manager_Helper_Functions::is_front_page( $item['ID'] ) ) {
			$uri           = '';
			$permalink     = Permalink_Manager_Helper_Functions::get_permalink_base( $item['ID'] );
			$is_front_page = true;
		} else {
			$is_draft      = ( $item["post_status"] == 'draft' ) ? true : false;
			$uri           = Permalink_Manager_URI_Functions_Post::get_post_uri( $item['ID'], true, $is_draft );
			$uri           = ( ! empty( $permalink_manager_options['general']['decode_uris'] ) ) ? urldecode( $uri ) : $uri;
			$permalink     = get_permalink( $item['ID'] );
			$is_front_page = false;
		}

		$field_args_base = array( 'type' => 'text', 'value' => $uri, 'without_label' => true, 'input_class' => 'custom_uri', 'extra_atts' => "data-element-id=\"{$item['ID']}\"" );
		$post_title      = sanitize_text_field( $item['post_title'] );

		$post_statuses_array            = Permalink_Manager_Helper_Functions::get_post_statuses();
		$post_statuses_array['inherit'] = __( 'Inherit (Attachment)', 'permalink-manager' );

		$output = apply_filters( 'permalink_manager_uri_editor_column_content', '', $column_name, get_post( $item['ID'] ) );
		if ( ! empty( $output ) ) {
			return $output;
		}

		switch ( $column_name ) {
			case 'item_uri':
				// Get auto-update settings
				$auto_update_val = get_post_meta( $item['ID'], "auto_update_uri", true );
				$auto_update_uri = ( ! empty( $auto_update_val ) ) ? $auto_update_val : $permalink_manager_options["general"]["auto_update_uris"];

				if ( $is_front_page ) {
					$field_args_base['disabled']       = true;
					$field_args_base['append_content'] = sprintf( '<p class="small uri_locked">%s %s</p>', '<span class="dashicons dashicons-lock"></span>', __( 'URI Editor is disabled because a custom permalink cannot be set for a front page.', 'permalink-manager' ) );
				} else if ( Permalink_Manager_Helper_Functions::is_draft_excluded( (int) $item['ID'] ) ) {
					$field_args_base['disabled']       = true;
					$field_args_base['append_content'] = sprintf( '<p class="small uri_locked">%s %s</p>', '<span class="dashicons dashicons-lock"></span>', __( 'URI Editor disabled due to "Exclude drafts & pending posts" setting and the post status.', 'permalink-manager' ) );
				} else if ( $auto_update_uri == 1 ) {
					$field_args_base['readonly']       = true;
					$field_args_base['append_content'] = sprintf( '<p class="small uri_locked">%s %s</p>', '<span class="dashicons dashicons-lock"></span>', __( 'The above permalink will be automatically updated and is locked for editing.', 'permalink-manager' ) );
				} else if ( $auto_update_uri == 2 ) {
					$field_args_base['disabled']       = true;
					$field_args_base['append_content'] = sprintf( '<p class="small uri_locked">%s %s</p>', '<span class="dashicons dashicons-lock"></span>', __( 'URI Editor disabled due to "Permalink update" setting.', 'permalink-manager' ) );
				}

				$output = '<div class="custom_uri_container">';
				$output .= Permalink_Manager_UI_Elements::generate_option_field( "uri[{$item['ID']}]", $field_args_base );
				$output .= "<span class=\"duplicated_uri_alert\"></span>";
				$output .= sprintf( "<a class=\"small post_permalink\" href=\"%s\" target=\"_blank\"><span class=\"dashicons dashicons-admin-links\"></span> %s</a>", $permalink, urldecode( $permalink ) );
				$output .= '</div>';

				return $output;

			case 'item_title':
				$output = $post_title;
				$output .= '<div class="extra-info small">';
				$output .= sprintf( "<span><strong>%s:</strong> %s</span>", __( "Slug", "permalink-manager" ), urldecode( $item['post_name'] ) );
				$output .= sprintf( " | <span><strong>%s:</strong> {$post_statuses_array[$item["post_status"]]}</span>", __( "Post status", "permalink-manager" ) );
				$output .= apply_filters( 'permalink_manager_uri_editor_extra_info', '', $column_name, get_post( $item['ID'] ) );
				$output .= '</div>';

				$output .= '<div class="row-actions">';
				$output .= sprintf( "<span class=\"edit\"><a href=\"%s\" title=\"%s\">%s</a> | </span>", get_edit_post_link( $item['ID'] ), __( 'Edit', 'permalink-manager' ), __( 'Edit', 'permalink-manager' ) );
				$output .= '<span class="view"><a target="_blank" href="' . $permalink . '" title="' . __( 'View', 'permalink-manager' ) . ' ' . $post_title . '" rel="permalink">' . __( 'View', 'permalink-manager' ) . '</a> | </span>';
				$output .= '<span class="id">#' . $item['ID'] . '</span>';
				$output .= '</div>';

				return $output;

			default:
				return $item[ $column_name ];
		}
	}

	/**
	 * The button that allows to save updated slugs
	 */
	function extra_tablenav( $which ) {
		global $wpdb, $active_section, $active_subsection;

		if ( $which == "top" ) {
			$button_text = __( 'Save all the permalinks below', 'permalink-manager' );
			$button_name = 'update_all_slugs[top]';
		} else {
			$button_text = __( 'Save all the permalinks above', 'permalink-manager' );
			$button_name = 'update_all_slugs[bottom]';
		}

		$html = "<div class=\"alignleft actions\">";
		$html .= get_submit_button( $button_text, 'primary alignleft', $button_name, false, array( 'id' => 'doaction', 'value' => 'update_all_slugs' ) );
		$html .= "</div>";

		if ( $which == "top" ) {
			// Filter by date
			$months = $wpdb->get_results( "SELECT DISTINCT month(post_date) AS m, year(post_date) AS y FROM {$wpdb->posts} WHERE post_status IN ($this->displayed_post_statuses) AND post_type IN ($this->displayed_post_types) ORDER BY post_date DESC", ARRAY_A );

			if ( $months ) {
				$choices = array( __( 'All dates', 'permalink-manager' ) );

				foreach ( $months as $month ) {
					$month_raw             = sprintf( "%s-%s", $month['y'], $month['m'] );
					$choices[ $month_raw ] = date_i18n( "F Y", strtotime( $month_raw ) );
				}

				$select_field = Permalink_Manager_UI_Elements::generate_option_field( 'month', array(
					'type'    => 'select',
					'choices' => $choices,
					'value'   => ( isset( $_REQUEST['month'] ) ) ? esc_attr( $_REQUEST['month'] ) : ''
				) );

				$html .= sprintf( '<div id=\"months-filter\" class="alignleft actions">%s</div>', $select_field );
			}

			$extra_fields = apply_filters( 'permalink_manager_uri_editor_extra_fields', '', 'posts' );

			if ( $months || $extra_fields ) {
				$html .= $extra_fields;

				$html .= '<div class="alignleft">';
				$html .= get_submit_button( __( "Filter", "permalink-manager" ), 'button', false, false, array( 'id' => 'filter-button', 'name' => 'filter-button' ) );
				$html .= "</div>";
			}

			$html .= '<div class="alignright">';
			$html .= $this->search_box( __( 'Search', 'permalink-manager' ), 'search-input' );
			$html .= '</div>';
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $html;
	}

	/**
	 * Display the search input field
	 *
	 * @return string
	 */
	public function search_box( $text = '', $input_id = '' ) {
		$search_query = ( ! empty( $_REQUEST['s'] ) ) ? esc_attr( $_REQUEST['s'] ) : "";

		$output = "<p class=\"search-box\">";
		$output .= "<label class=\"screen-reader-text\" for=\"{$input_id}\">{$text}:</label>";
		$output .= Permalink_Manager_UI_Elements::generate_option_field( 's', array( 'value' => $search_query, 'type' => 'search' ) );
		$output .= get_submit_button( $text, 'button', false, false, array( 'id' => 'search-submit', 'name' => 'search-submit' ) );
		$output .= "</p>";

		return $output;
	}

	/**
	 * Prepare the items for the table to process
	 */
	public function prepare_items() {
		global $wpdb;

		$columns      = $this->get_columns();
		$hidden       = $this->get_hidden_columns();
		$sortable     = $this->get_sortable_columns();
		$current_page = $this->get_pagenum();

		// SQL query parameters
		$order        = ( isset( $_REQUEST['order'] ) && in_array( $_REQUEST['order'], array( 'asc', 'desc' ) ) ) ? sanitize_sql_orderby( $_REQUEST['order'] ) : 'desc';
		$orderby      = ( isset( $_REQUEST['orderby'] ) ) ? sanitize_sql_orderby( $_REQUEST['orderby'] ) : 'ID';
		$search_query = ( ! empty( $_REQUEST['s'] ) ) ? esc_sql( $_REQUEST['s'] ) : "";

		// Extra filters
		$extra_filters = $attachment_support = '';
		if ( ! empty( $_GET['month'] ) ) {
			$month = date( "n", strtotime( $_GET['month'] ) );
			$year  = date( "Y", strtotime( $_GET['month'] ) );

			$extra_filters .= "AND month(post_date) = {$month} AND year(post_date) = {$year}";
		}

		// Support for attachments
		if ( strpos( $this->displayed_post_types, 'attachment' ) !== false ) {
			$attachment_support = " OR (post_type = 'attachment')";
		}

		// Grab posts from database
		$sql_parts['start'] = "SELECT * FROM {$wpdb->posts} AS p ";
		if ( $search_query ) {
			$sql_parts['where'] = "WHERE (LOWER(post_title) LIKE LOWER('%{$search_query}%') ";

			// Search in array with custom URIs
			$found = Permalink_Manager_URI_Functions::find_uri( $search_query, false, 'posts' );
			if ( $found ) {
				$sql_parts['where'] .= sprintf( "OR ID IN (%s)", implode( ',', $found ) );
			}
			$sql_parts['where'] .= " ) AND ((post_status IN ($this->displayed_post_statuses) AND post_type IN ($this->displayed_post_types)) {$attachment_support}) {$extra_filters} ";
		} else {
			$sql_parts['where'] = "WHERE ((post_status IN ($this->displayed_post_statuses) AND post_type IN ($this->displayed_post_types)) {$attachment_support}) {$extra_filters} ";
		}

		// Do not display excluded posts in Bulk URI Editor
		$excluded_posts = Permalink_Manager_Helper_Functions::get_excluded_post_ids();
		if ( ! empty( $excluded_posts ) && is_array( $excluded_posts ) ) {
			$sql_parts['where'] .= sprintf( "AND ID NOT IN ('%s') ", implode( "', '", $excluded_posts ) );
		}

		$sql_parts['end'] = "ORDER BY {$orderby} {$order}";

		list( $all_items, $total_items, $per_page ) = Permalink_Manager_URI_Editor::prepare_sql_query( $sql_parts, $current_page, false );

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page
		) );

		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $all_items;
	}

	/**
	 * Define hidden columns
	 *
	 * @return array
	 */
	public function get_hidden_columns() {
		return array();
	}

	/**
	 * Sort the data
	 *
	 * @param mixed $a
	 * @param mixed $b
	 *
	 * @return int
	 */
	private function sort_data( $a, $b ) {
		// Set defaults
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? sanitize_sql_orderby( $_GET['orderby'] ) : 'post_title';
		$order   = ( ! empty( $_GET['order'] ) ) ? sanitize_sql_orderby( $_GET['order'] ) : 'asc';
		$result  = strnatcasecmp( $a[ $orderby ], $b[ $orderby ] );

		return ( $order === 'asc' ) ? $result : - $result;
	}

}
