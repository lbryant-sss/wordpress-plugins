<?php
/**
 * Sorting Class.
 *
 * @package RT_Team
 */

namespace RT\Team\Controllers\Admin;

use RT\Team\Helpers\Fns;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Sorting Class.
 */
class TaxSorting {
	use \RT\Team\Traits\SingletonTrait;

	/**
	 * Class Init.
	 *
	 * @return void
	 */
	protected function init() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if (isset($_GET['page'] ) && isset($_GET['post_type']) && 'ttp_taxonomy_order' === $_GET['page'] && 'team' === $_GET['post_type']){
			add_action( 'admin_init', [ $this, 'refresh' ] );
		}

		$taxo = [
			'team_department',
			'team_designation',
		];

		foreach ( $taxo as $tx ) {
			add_filter( 'manage_edit-' . $tx . '_columns', [ &$this, 'term_column_header' ], 10, 1 );
			add_filter( 'manage_' . $tx . '_custom_column', [ &$this, 'term_column_value' ], 10, 3 );
		}

		add_action( 'pre_get_posts', [ $this, 'tlp_pre_get_posts' ] );
		add_action( 'wp_ajax_tlp-team-update-menu-order', [ $this, 'tlp_team_update_menu_order' ] );
		add_action( 'wp_ajax_ttp-term-update-order', [ $this, 'ttp_term_update_order' ] );
		add_action( 'wp_ajax_ttp-get-term-list', [ $this, 'ttp_get_term_list' ] );
	}


	function ttp_get_term_list() {

		$html  = $msg = null;
		$error = true;
		if ( ! ( current_user_can( 'manage_options' ) || current_user_can( 'edit_pages' ) ) ) {
            wp_send_json( [
                'error' => $error,
                'msg'   => esc_html__( 'Permission denied', 'tlp-team' ),
            ] );
        }
		if ( wp_verify_nonce( Fns::getNonce(), Fns::nonceText()) ) {
			$tax = ! empty( $_REQUEST['tax'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tax'] ) ) : null;
			if ( $tax ) {
				$error = false;
				/*old code*/
//				$terms = get_terms(
//					$tax,
//					[
//						'orderby'    => 'meta_value_num',
//						'meta_key'   => '_rt_order',
//						'order'      => 'ASC',
//						'hide_empty' => false,
//					]
//				);
				$terms = get_terms( array(
					'taxonomy'   => $tax,
					'orderby'    => 'meta_value_num',
					'meta_key'   => '_rt_order', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					'order'      => 'ASC',
					'hide_empty' => false,
				) );
				if ( ! empty( $terms ) ) {
					$html .= "<ul id='order-target' data-taxonomy='{$tax}'>";
					foreach ( $terms as $term ) {
						$html .= "<li data-id='{$term->term_id}'><span>{$term->name}</span></li>";
					}
					$html .= '</ul>';
				} else {
					$html .= '<p>' . esc_html__( 'No term found', 'tlp-team' ) . '</p>';
				}
			} else {
				$html .= '<p>' . esc_html__( 'Select a taxonomy', 'tlp-team' ) . '</p>';
			}
		} else {
			$html .= '<p>' . esc_html__( 'Security error', 'tlp-team' ) . '</p>';
		}

		wp_send_json(
			[
				'data'  => $html,
				'error' => $error,
				'msg'   => $msg,
			]
		);
		die();
	}

	function term_column_header( $columns ) {
		$columns['order'] = esc_html__( 'Order', 'tlp-team' );

		return $columns;
	}

	function term_column_value( $empty, $custom_column, $term_id ) {
		$empty = '';

		if ( 'order' == $custom_column ) {
			return get_term_meta( $term_id, '_rt_order', true );
		}
	}

	function tlp_pre_get_posts( $wp_query ) {
		if ( is_admin() ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( isset( $wp_query->query['post_type'] ) && ! isset( $_GET['orderby'] ) && $wp_query->query['post_type'] == 'team' && $wp_query->is_main_query() ) {
				$wp_query->set( 'orderby', 'menu_order' );
				$wp_query->set( 'order', 'ASC' );
			}
		}
	}

	function tlp_team_update_menu_order() {
		if ( ! ( current_user_can( 'manage_options' ) || current_user_can( 'edit_pages' ) ) ) {
            wp_send_json( [
                'error' => true,
                'msg'   => esc_html__( 'Permission denied', 'tlp-team' ),
            ] );
        }
		if ( ! wp_verify_nonce( Fns::getNonce(), Fns::nonceText() ) ) {
			wp_send_json_error(
				[
					'data' => __('Security Issue','tlp-team')
				]
			);
		}
		global $wpdb;

		$data = ( ! empty( $_POST['post'] ) ? array_map( 'absint', $_POST['post'] ) : [] );

		if ( ! is_array( $data ) ) {
			return false;
		}

		$id_arr = [];
		foreach ( $data as $position => $id ) {
			$id_arr[] = $id;
		}

		$menu_order_arr = [];
		foreach ( $id_arr as $key => $id ) {
			$tlp_tax_cache_key = 'tlp_team_menu_order_cache_'.$id;
			$results = wp_cache_get($tlp_tax_cache_key);
			if (false === $results){
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results = $wpdb->get_results(
					$wpdb->prepare("SELECT menu_order FROM $wpdb->posts WHERE ID = " . intval( $id ) )
				);
				wp_cache_set($tlp_tax_cache_key,$results);
			}
			foreach ( $results as $result ) {
				$menu_order_arr[] = $result->menu_order;
			}
		}

		sort( $menu_order_arr );

		foreach ( $data as $position => $id ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->update( $wpdb->posts, [ 'menu_order' => $menu_order_arr[ $position ] ], [ 'ID' => intval( $id ) ] );
		}

		wp_send_json_success();
	}

	/**
	 *
	 */
	function refresh() {
		global $wpdb;
		$tlp_refresh_cache_key = 'tlp_team_post_refresh';
		$results = wp_cache_get($tlp_refresh_cache_key);
		if (false === $results){
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$results = $wpdb->get_results(
				$wpdb->prepare(
					"
			        SELECT ID
			        FROM $wpdb->posts
			        WHERE post_type = %s AND post_status IN ('publish', 'pending', 'draft', 'private', 'future')
			        ORDER BY menu_order ASC
			        ",
					rttlp_team()->post_type
				)
			);
			wp_cache_set($tlp_refresh_cache_key,$results);
		}

		foreach ( $results as $key => $result ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->update( $wpdb->posts, [ 'menu_order' => $key + 1 ], [ 'ID' => $result->ID ] );
		}
	}

	/**
	 * @return bool
	 */
	function ttp_term_update_order() {

		if ( ! ( current_user_can( 'manage_options' ) || current_user_can( 'edit_pages' ) ) ) {
            wp_send_json( [
                'error' => true,
                'msg'   => esc_html__( 'Permission denied', 'tlp-team' ),
            ] );
        }

		if (wp_verify_nonce( Fns::getNonce(), Fns::nonceText() )){
			$data = ( ! empty( $_POST['terms'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['terms'] ) ) ) : [] );

			if ( ! is_array( $data ) && empty( $data ) ) {
				return false;
			}
			// sort( $order_arr );

			foreach ( $data as $position => $id ) {
				update_term_meta( intval( $id ), '_rt_order', $position );
			}
		}else{
			wp_send_json_error(
				[
					'data' => __('Security Issue','tlp-team')
				]
			);
		}

		die();
	}
}
