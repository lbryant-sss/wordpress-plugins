<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ES_DB_Campaigns extends ES_DB {

	const STATUS_ACTIVE = 1;

	const STATUS_INACTIVE = 0;
	/**
	 * Tabl Name
	 *
	 * @since 4.2.1
	 * @var string $table_name
	 */
	public $table_name;

	/**
	 * Version
	 *
	 * @since 4.2.1
	 * @var string $version
	 */
	public $version;

	/**
	 * Primary Key
	 *
	 * @since 4.2.1
	 * @var string
	 */
	public $primary_key;

	public function __construct() {
		global $wpdb;

		parent::__construct();

		$this->table_name = $wpdb->prefix . 'ig_campaigns';

		$this->primary_key = 'id';

		$this->version = '1.0';

	}

	/**
	 * Get columns and formats
	 *
	 * @since  4.0.0
	 */
	public function get_columns() {
		return array(
		'id'               => '%d',
		'slug'             => '%s',
		'name'             => '%s',
		'type'             => '%s',
		'parent_id'        => '%d',
		'parent_type'      => '%s',
		'subject'          => '%s',
		'body'             => '%s',
		'from_name'        => '%s',
		'from_email'       => '%s',
		'reply_to_name'    => '%s',
		'reply_to_email'   => '%s',
		'categories'       => '%s',
		'list_ids'         => '%s',
		'base_template_id' => '%d',
		'status'           => '%d',
		'created_at'       => '%s',
		'updated_at'       => '%s',
		'deleted_at'       => '%s',
		'meta'             => '%s',
		);
	}

	/**
	 * Get default column values
	 *
	 * @since  4.0.0
	 */
	public function get_column_defaults() {

		$from_name  = ES_Common::get_ig_option( 'from_name' );
		$from_email = ES_Common::get_ig_option( 'from_email' );

		return array(
		'slug'             => null,
		'name'             => null,
		'type'             => null,
		'parent_id'        => null,
		'parent_type'      => null,
		'subject'          => null,
		'body'             => '',
		'from_name'        => $from_name,
		'from_email'       => $from_email,
		'reply_to_name'    => $from_name,
		'reply_to_email'   => $from_email,
		'categories'       => '',
		'list_ids'         => '',
		'base_template_id' => 0,
		'status'           => 0,
		'created_at'       => ig_get_current_date_time(),
		'updated_at'       => null,
		'deleted_at'       => null,
		'meta'             => null,
		);
	}

	/**
	 * Get template id by campaign id
	 *
	 * @param $id
	 *
	 * @return array|string|null
	 *
	 * @since 4.2.1
	 */
	public function get_template_id_by_campaign( $id ) {
		return $this->get_column( 'base_template_id', $id );
	}

	/**
	 * Save Campaign
	 *
	 * @param $data
	 * @param null $id
	 *
	 * @return false|int
	 *
	 * @since 4.0.0
	 */
	public function save_campaign( $data, $id = null ) {

		$insert = is_null( $id );

		if ( ! empty( $data['list_ids'] ) && is_array( $data['list_ids'] ) ) {
			$data['list_ids'] = array_unique( $data['list_ids'] );
			$data['list_ids'] = implode( ',', $data['list_ids'] );
		}

		if ( $insert ) {
			$result = $this->insert( $data );
		} else {
			// Set updated_at if not set
			$data['updated_at'] = ! empty( $data['updated_at'] ) ? $data['updated_at'] : ig_get_current_date_time();

			$result = $this->update( $id, $data );
		}

		return $result;
	}

	/**
	 * Get campaign type by campaign id
	 *
	 * @param $id
	 *
	 * @return string|null
	 *
	 * @since 4.0.0
	 *
	 * @modify 4.2.1
	 */
	public function get_campaign_type_by_id( $id ) {
		return $this->get_column( 'type', $id );
	}

	/**
	 * Migrate post notification from ES 3.5.x
	 *
	 * @since 4.0.0
	 */
	public function migrate_post_notifications() {
		global $wpbd;

		$campaigns_data = array();
		$template_ids   = array();

		$from_name        = ES_Common::get_ig_option( 'from_name' );
		$from_email       = ES_Common::get_ig_option( 'from_email' );
		$list_is_name_map = ES()->lists_db->get_list_id_name_map( '', true );

		$es_notification_table = EMAIL_SUBSCRIBERS_NOTIFICATION_TABLE;

		$total = $wpbd->get_var( $wpbd->prepare( "SELECT count(*) as total FROM {$wpbd->prefix}es_notification WHERE %d", 1 ) );

		if ( $total > 0 ) {
			$batch_size = IG_DEFAULT_BATCH_SIZE;

			$total_batches = ( $total > IG_DEFAULT_BATCH_SIZE ) ? ceil( $total / $batch_size ) : 1;

			for ( $i = 0; $i < $total_batches; $i ++ ) {
				$batch_start = $i * $batch_size;
				// $query         = 'SELECT * FROM ' . EMAIL_SUBSCRIBERS_NOTIFICATION_TABLE . " LIMIT {$batch_start}, {$batch_size}";
				// $notifications = $wpbd->get_results( $query, ARRAY_A ); // WPCS: cache ok, DB call ok, unprepared SQL ok.
				$notifications = $wpbd->get_results( $wpbd->prepare( "SELECT * FROM {$wpbd->prefix}es_notification LIMIT %d, %d", $batch_start, $batch_size ), ARRAY_A );
				if ( count( $notifications ) > 0 ) {
					foreach ( $notifications as $key => $notification ) {
						$categories = ! empty( $notification['es_note_cat'] ) ? $notification['es_note_cat'] : '';
						if ( ! empty( $categories ) ) {
							$categories = explode( '--', $categories );
							$categories = array_map( array( 'ES_Common', 'temp_filter_category' ), $categories );
							$categories = ES_Common::convert_categories_array_to_string( $categories );
						}

						$template_id = 0;
						if ( ! empty( $notification['es_note_templ'] ) ) {
							$template_id = $notification['es_note_templ'];

							if ( ! in_array( $template_id, $template_ids ) ) {
								$template_ids[] = $template_id;
							}
						}

						$campaigns_data[ $key ]['slug']             = $template_id; // We don't have slug at this moment. So, we will fetch template's slug and store it later
						$campaigns_data[ $key ]['name']             = $template_id; // We don't have name at this moment. So, we will fetch template's name and store it later
						$campaigns_data[ $key ]['type']             = IG_CAMPAIGN_TYPE_POST_NOTIFICATION;
						$campaigns_data[ $key ]['from_name']        = $from_name;
						$campaigns_data[ $key ]['from_email']       = $from_email;
						$campaigns_data[ $key ]['reply_to_name']    = $from_name; // We don't have this option avaialble. So, setting from_name as reply_to_name
						$campaigns_data[ $key ]['reply_to_email']   = $from_email; // We don't have this option available. So, setting from_email as reply_to_email
						$campaigns_data[ $key ]['categories']       = $categories;
						$campaigns_data[ $key ]['list_ids']         = ( ! empty( $notification['es_note_group'] ) && ! empty( $list_is_name_map[ $notification['es_note_group'] ] ) ) ? $list_is_name_map[ $notification['es_note_group'] ] : 0;
						$campaigns_data[ $key ]['base_template_id'] = $template_id;
						$campaigns_data[ $key ]['status']           = ( ! empty( $notification['es_note_status'] ) && ( 'Disable' === $notification['es_note_status'] ) ) ? 0 : 1;
						$campaigns_data[ $key ]['created_at']       = ig_get_current_date_time();
						$campaigns_data[ $key ]['updated_at']       = null;
						$campaigns_data[ $key ]['deleted_at']       = null;
					}

					$templates_data = array();
					// Get Template Name & Slug
					if (count($template_ids) > 0) {
						$placeholders = implode(',', array_fill(0, count($template_ids), '%d'));
						$query = $wpbd->prepare("SELECT ID, post_name, post_title FROM {$wpbd->prefix}posts WHERE id IN ({$placeholders})", $template_ids);
						$templates = $wpbd->get_results($query, ARRAY_A);
					
						foreach ( $templates as $template ) {
							$templates_data[ $template['ID'] ] = $template;
						}
					}				

					// Do Batach Insert
					$values        = array();
					$place_holders = array();
					$columns       = $this->get_columns();
					unset( $columns['id'] );
					$fields = array_keys( $columns );

					foreach ( $campaigns_data as $campaign_data ) {
						$campaign_data['slug'] = ! empty( $templates_data[ $campaign_data['slug'] ] ) ? $templates_data[ $campaign_data['slug'] ]['post_name'] : '';
						$campaign_data['name'] = ! empty( $templates_data[ $campaign_data['name'] ] ) ? $templates_data[ $campaign_data['name'] ]['post_title'] : '';

						$campaign_data = wp_parse_args( $campaign_data, $this->get_column_defaults() );

						$formats = array();
						foreach ( $columns as $column => $format ) {
							$values[]  = $campaign_data[ $column ];
							$formats[] = $format;
						}

						$place_holders[] = '( ' . implode( ', ', $formats ) . ' )';
					}

					ES_DB::do_insert( IG_CAMPAIGNS_TABLE, $fields, $place_holders, $values );
				}
			}
		}
	}

	/**
	 * Migrate Newsletters from ES 3.5.x
	 *
	 * @since 4.0.0
	 */
	public function migrate_newsletters() {
		global $wpdb;

		// Check if es_sentdetails table exists or not.
		if ( $this->table_exists( $wpdb->prefix . 'es_sentdetails' ) ) {
			$from_name  = ES_Common::get_ig_option( 'from_name' );
			$from_email = ES_Common::get_ig_option( 'from_email' );
// phpcs:disable
			$total = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) as total FROM {$wpdb->prefix}es_sentdetails WHERE es_sent_source = %s", 'Newsletter' ) );
// phpcs:enable
			if ( $total > 0 ) {

				$list_is_name_map = ES()->lists_db->get_list_id_name_map( '', true );
				$batch_size       = IG_DEFAULT_BATCH_SIZE;
				$total_batches    = ceil( $total / $batch_size );

				$values        = array();
				$place_holders = array();
				$columns       = $this->get_columns();
				unset( $columns['id'] );
				$fields = array_keys( $columns );
				for ( $i = 0; $i <= $total_batches; $i ++ ) {
					$batch_start = $i * $batch_size;
// phpcs:disable
					$newsletters = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}es_sentdetails WHERE es_sent_source = %s LIMIT %d, %d", 'Newsletter', $batch_start, $batch_size ), ARRAY_A );
// phpcs:enable
					if ( count( $newsletters ) > 0 ) {
						$campaign_data = array();
						$values        = array();
						$place_holders = array();
						foreach ( $newsletters as $key => $newsletter ) {
							$campaign_data['slug']           = sanitize_title( $newsletter['es_sent_subject'] );
							$campaign_data['name']           = $newsletter['es_sent_subject'];
							$campaign_data['type']           = IG_CAMPAIGN_TYPE_NEWSLETTER;
							$campaign_data['from_name']      = $from_name;
							$campaign_data['from_email']     = $from_email;
							$campaign_data['reply_to_name']  = $from_name; // We don't have this option avaialble. So, setting from_name as reply_to_name
							$campaign_data['reply_to_email'] = $from_email; // We don't have this option available. So, setting from_email as reply_to_email
							$campaign_data['list_ids']       = ( ! empty( $newsletter['es_note_group'] ) && ! empty( $list_is_name_map[ $newsletter['es_note_group'] ] ) ) ? $list_is_name_map[ $newsletter['es_note_group'] ] : 0;
							$campaign_data['status']         = 1;
							$campaign_data['created_at']     = $newsletter['es_sent_starttime'];

							$campaign_data = wp_parse_args( $campaign_data, $this->get_column_defaults() );

							$formats = array();
							foreach ( $columns as $column => $format ) {
								$values[]  = $campaign_data[ $column ];
								$formats[] = $format;
							}

							$place_holders[] = '( ' . implode( ', ', $formats ) . ' )';
						}

						ES_DB::do_insert( IG_CAMPAIGNS_TABLE, $fields, $place_holders, $values );
					}
				}
			}
		}
	}

	/**
	 * After migration we are not able to get the campaign_id in mailing queue
	 * table. So, we are fetching it now and set campaign_id based on subject match.
	 * If not match, set as 0.
	 */
	public function update_campaign_id_in_mailing_queue() {
		global $wpdb;
// phpcs:disable
		$campaigns = $wpdb->get_results( $wpdb->prepare( "SELECT id, name FROM {$wpdb->prefix}ig_campaigns WHERE %d", 1 ), ARRAY_A );
// phpcs:enable
		$data_to_update = array();
		if ( count( $campaigns ) > 0 ) {
			// phpcs:disable
			$mailing_queue_results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ig_mailing_queue WHERE %d", 1 ), ARRAY_A );
			// phpcs:enable
			if ( count( $mailing_queue_results ) > 0 ) {
				foreach ( $mailing_queue_results as $result ) {
					$subject = trim( $result['subject'] );
					foreach ( $campaigns as $campaign ) {
						$campaign_name = trim( $campaign['name'] );
						if ( $campaign_name == $subject ) {
							$data_to_update[ $result['id'] ] = $campaign['id'];
							break;
						}
					}
				}
			}
		}

		if ( ! empty( $data_to_update ) ) {
			foreach ( $data_to_update as $mailing_queue_id => $campaign_id ) {
				// phpcs:disable
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}ig_mailing_queue SET campaign_id = %d WHERE id = %d", array( $campaign_id, $mailing_queue_id ) ) );
				// phpcs:enable
			}
		}
	}

	/**
	 * Get total campaigns
	 *
	 * @return string|null
	 *
	 * @since 4.2.1
	 * @since 4.3.4 Removed deleted_at $where condition
	 */
	public function get_total_campaigns( $where = '' ) {
		return $this->count( $where );
	}

	/**
	 * Get total campaigns by type
	 *
	 * @param string $type
	 *
	 * @return string|null
	 *
	 * @since 4.2.1
	 */
	public function get_total_campaigns_by_type( $type = 'newsletter' ) {
		global $wpdb;

		$where = $wpdb->prepare( 'type = %s', array( $type ) );

		$campaigns = $this->get_total_campaigns( $where );

		return $campaigns;
	}

	/**
	 * Get total post notifications
	 *
	 * @return string|null
	 *
	 * @since 4.2.1
	 */
	public function get_total_post_notifications() {
		return $this->get_total_campaigns_by_type( 'post_notification' );
	}

	/**
	 * Get total newsletters
	 *
	 * @return string|null
	 *
	 * @since 4.2.1
	 */
	public function get_total_newsletters() {
		return $this->get_total_campaigns_by_type();
	}

	/**
	 * Get total sequence
	 *
	 * @return string|null
	 *
	 * @since 4.6.6
	 */
	public function get_total_sequences() {
		return $this->get_total_campaigns_by_type( 'sequence' );
	}

	/**
	 * Get campaign meta data
	 *
	 * @param $id
	 *
	 * @return mixed|string|null
	 *
	 * @since 4.2.0
	 */
	public function get_campaign_meta_by_id( $id ) {
		$meta = $this->get_column( 'meta', $id );

		if ( $meta ) {
			$meta = maybe_unserialize( $meta );
		}

		return $meta;
	}

	/**
	 * Get campaign categories string
	 *
	 * @param $id
	 *
	 * @return mixed|string|null
	 *
	 * @since 4.2.0
	 */
	public function get_campaign_categories_str_by_id( $id ) {
		$categories_str = $this->get_column( 'categories', $id );

		return $categories_str;
	}


	/**
	 * Get campaigns by id
	 *
	 * @param int $id
	 *
	 * @return array|object|null
	 */
	public function get_campaign_by_id( $id = 0, $status = 1 ) {
		global $wpdb;

		if ( empty( $id ) ) {
			return array();
		}

		$where = $wpdb->prepare( 'id = %d', $id );
		
		if ( - 1 !== $status ) {
			$where .= $wpdb->prepare( ' AND status = %d', $status );
		}

		$campaigns = $this->get_by_conditions( $where );

		$campaign = array();
		if ( ! empty( $campaigns ) ) {
			$campaign = array_shift( $campaigns );
		}

		return $campaign;
	}
	/**
	 * Get campaigns by parent id
	 *
	 * @param int $id
	 *
	 * @return array|object|null
	 *
	 * @since 4.2.1
	 * @since 4.3.4 Removed deleted_at condition
	 */
	public function get_campaign_by_parent_id( $id = 0 ) {
		global $wpdb;

		if ( empty( $id ) ) {
			return array();
		}
// phpcs:disable
		//$where = $wpdb->prepare( "parent_id = %d AND status = %d AND ( deleted_at IS NULL OR deleted_at = '0000-00-00 00:00:00' )", $id, self::STATUS_ACTIVE );
		$where = $wpdb->prepare( "parent_id = %d AND ( deleted_at IS NULL OR deleted_at = '0000-00-00 00:00:00' )", $id);
// phpcs:enable
		$campaigns = $this->get_by_conditions( $where );

		return $campaigns;

	}

	/**
	 * Get posts by post type
	 *
	 * @param int $post_types
	 *
	 * @return array|object|null
	 */

	public function get_posts_by_type( $post_types ) {

		global $wpbd;
		if ( empty( $post_types['postsType'] ) ) {
			return array();
		}
		
		$posts_type_count       = count( $post_types['postsType'] );
		$post_type_placeholders = array_fill( 0, $posts_type_count, '%s' );
		// phpcs:disable
		$query                  = $wpbd->prepare(
			"SELECT ID, post_title, post_content FROM {$wpbd->posts} WHERE post_type IN (" . implode( ',', $post_type_placeholders ) . ') AND post_status = "publish"',
			$post_types['postsType']
		);
		// phpcs:enable
		$posts                  = $wpbd->get_results( $query, ARRAY_A );
		return $posts;
	}

	
	/**
	 * Get Active Campaigns
	 *
	 * @return array|object|null
	 *
	 * @since 4.2.0
	 * @since 4.3.4 Removed deleted_at condition
	 */
	public function get_active_campaigns( $type = '' ) {
		global $wpdb;
// phpcs:disable
		if ( empty( $type ) ) {
			$where = $wpdb->prepare( "status = %d AND (deleted_at IS NULL OR deleted_at = '0000-00-00 00:00:00')", self::STATUS_ACTIVE );
		} else {
			$where = $wpdb->prepare( "status = %d AND type = %s AND (deleted_at IS NULL OR deleted_at = '0000-00-00 00:00:00')", self::STATUS_ACTIVE, $type );
		}
// phpcs:enable
		return $this->get_by_conditions( $where );
	}

	/**
	 * Update meta value
	 *
	 * @param int   $campaign_id
	 * @param array $meta_data
	 *
	 * @return bool|false|int
	 *
	 * @sine 4.2.0
	 */
	public function update_campaign_meta( $campaign_id = 0, $meta_data = array() ) {

		$update = false;
		if ( ! empty( $campaign_id ) && ! empty( $meta_data ) ) {
			$campaign = $this->get_campaign_by_id( $campaign_id );

			if ( ! empty( $campaign ) ) {

				if ( isset( $campaign['meta'] ) ) {
					$meta = maybe_unserialize( $campaign['meta'] );

					foreach ( $meta_data as $meta_key => $meta_value ) {
						$meta[ $meta_key ] = $meta_value;
					}

					$campaign['meta'] = maybe_serialize( $meta );

					$update = $this->save_campaign( $campaign, $campaign_id );

				}
			}
		}

		return $update;

	}

	/**
	 * Delete Campaigns
	 *
	 * @param $ids
	 *
	 * @since 4.3.4
	 */
	public function delete_campaigns( $ids = array() ) {
		
		if ( ! is_array( $ids ) ) {
			$ids = array( absint( $ids ) );
		}

		if ( is_array( $ids ) && count( $ids ) > 0 ) {
			$status = -1;
			foreach ( $ids as $id ) {
				
				$campaign = self::get_campaign_by_id($id, $status);
				if (!empty($campaign) && ( IG_CAMPAIGN_TYPE_SEQUENCE !== $campaign['type']  || IG_ES_CAMPAIGN_STATUS_IN_ACTIVE !== $campaign['status'] )) {
					self::delete_report_data($id);
				}
				$this->delete( absint( $id ) );

				/**
				 * Take necessary cleanup steps using this hook
				 *
				 * @since 4.3.4
				 */
				do_action( 'ig_es_campaign_deleted', $id );
			}

			return true;
		}

		return false;
	}

	/**
	 * Get all campaign ids by parent_id
	 *
	 * @param int $parent_campaign_id
	 *
	 * @return array|string|null
	 *
	 * @since 4.3.4
	 */
	public function get_campaigns_by_parent_id( $parent_campaign_id = 0 ) {

		if ( empty( $parent_campaign_id ) || 0 == absint( $parent_campaign_id ) ) {
			return array();
		}

		return $this->get_column_by( 'id', 'parent_id', $parent_campaign_id, false );
	}

	/**
	 * Get Post Notifications (Campaigns) based on post_id
	 *
	 * @param int $post_id
	 *
	 * @return array|object|null
	 *
	 * @since 4.3.6
	 */
	public function get_campaigns_by_post_id( $post_id = 0 ) {

		global $wpdb;

		$campaigns = array();

		if ( $post_id > 0 ) {
			$post_type = get_post_type( $post_id );
// phpcs:disable
			$where = $wpdb->prepare( "status = %d AND type = %s AND (deleted_at IS NULL OR deleted_at = '0000-00-00 00:00:00')", 1, 'post_notification' );
// phpcs:enable
			$new_flow_campaign_ids = get_option( 'ig_es_new_category_format_campaign_ids', array() );
			// Run old logic for campaign
			if ( empty( $new_flow_campaign_ids ) ) {
				if ( 'post' === $post_type ) {
					$categories       = get_the_category( $post_id );
					$total_categories = count( $categories );
					if ( $total_categories > 0 ) {
						for ( $i = 0; $i < $total_categories; $i ++ ) {
							if ( 0 === $i ) {
								$where .= ' and (';
							} else {
								$where .= ' or';
							}
	
							$category_str = ES_Common::prepare_category_string( $categories[ $i ]->term_id );
	
							$where .= " categories LIKE '%" . $category_str . "%'";
							if ( ( $total_categories - 1 ) === $i ) {
								$where .= " OR categories LIKE '%all%'";
								$where .= ") AND categories NOT LIKE '%none%'";
							}
						}
					} else {
						// no categories fround for post
						return $campaigns;
					}
				} else {
					$post_type = ES_Common::prepare_custom_post_type_string( $post_type );
					$where    .= " and categories LIKE '%" . wp_specialchars_decode( addslashes( $post_type ) ) . "%'";
				}
			}

			$campaigns = $this->get_by_conditions( $where, ARRAY_A );
			if ( ! empty( $campaigns ) ) {
				$current_post_type  = get_post_type( $post_id );
				$current_categories = get_the_category( $post_id );
				$current_categories_lists = array();
				foreach ( $current_categories as $current_category ) {
					$current_categories_lists[] = $current_category->term_id;
				}
				foreach ( $campaigns as $index => $campaign ) {
					$campaign_id = $campaign['id'];
					$categories  = $campaign['categories'];
					$is_categories_matching = false;
					$categories = explode( '##', trim( trim( $categories, '##' ) ) );
					if ( ! empty( $categories ) ) {
						foreach ( $categories as $category ) {
							if ( ! empty( $category ) ) {
								if ( ! empty( $new_flow_campaign_ids ) && in_array( (int) $campaign_id, $new_flow_campaign_ids, true ) ) {
									$post_categories = explode( '|', $category );
									foreach ( $post_categories as $post_category ) {
										list( $post_type, $categories_list ) = explode( ':', $post_category );
										if ( $post_type === $current_post_type ) {
											if ( 'post' === $current_post_type ) {
												if ( 'none' !== $categories_list ) {
													if ( 'all' === $categories_list ) {
														$is_categories_matching = true;
														break;
													} else {
														$categories_list = array_map( 'absint', explode( ',', $categories_list ) );
														$is_categories_matching = count( array_intersect( $categories_list, $current_categories_lists ) ) > 0;
														if ( $is_categories_matching ) {
															break;
														}
													}
												}
											} else {
												if ( 'all' === $categories_list ) {
													$is_categories_matching = true;
													break;
												} else {
													$term_ids = array_map( 'absint', explode( ',', $categories_list ) );
													$taxonomies = get_object_taxonomies( $post_type, 'objects' );
													if ( ! empty( $taxonomies ) ) {
														$taxonomies_slug = array_keys( $taxonomies );
														$post_term_ids = array();
														foreach ( $taxonomies_slug as $taxonomy_slug ) {
															$post_terms = get_the_terms( $post_id, $taxonomy_slug );
															if ( ! $post_terms ) {
																continue;
															}

															$taxonomy_term_ids = wp_list_pluck( $post_terms, 'term_id' );
															$post_term_ids = array_merge( $post_term_ids, $taxonomy_term_ids );
														}
														$is_categories_matching = count( array_intersect( $term_ids, $post_term_ids ) ) > 0;
														if ( $is_categories_matching ) {
															break;
														}
													}
												}
											}
										}
									}
								} else {
									if ( 'post' === $current_post_type ) {
										if ( is_numeric( $category ) && in_array( ( int ) $category, $current_categories_lists, true ) ) {
											$is_categories_matching = true;
											break;
										} elseif ( '{a}All{a}' === $category ) {
											$is_categories_matching = true;
											break;
										}
									} elseif ( '{T}' . $current_post_type . '{T}' === $category ) {
										$is_categories_matching = true;
										break;
									}
								}
							} 
						} 
					}
					
					if ( ! $is_categories_matching ) {
						unset( $campaigns[ $index ] );
					}
				}
			}

			$campaigns = apply_filters( 'ig_es_campaigns_for_post', $campaigns, $post_id );
		}

		return $campaigns;

	}

	/**
	 * Method to update campaign status
	 *
	 * @param array   $campaign_ids Campaign IDs.
	 * @param integer $status New status.
	 *
	 * @return bool $updated        Update status
	 *
	 * @since 4.4.4
	 */
	public function update_status( $campaign_ids = array(), $status = 0 ) {
		global $wpbd;

		$updated = false;
		if ( empty( $campaign_ids ) ) {
			return $updated;
		}

		$id_str       = '';
		$campaign_ids = esc_sql( $campaign_ids );
		if ( is_array( $campaign_ids ) && count( $campaign_ids ) > 0 ) {
			$id_str = implode( ',', $campaign_ids );
		} elseif ( is_numeric( $campaign_ids ) ) {
			$id_str = $campaign_ids;
		}

		if ( ! empty( $id_str ) ) {

			$query = $wpbd->prepare( "UPDATE {$wpbd->prefix}ig_campaigns SET status = %d WHERE id IN({$id_str})", $status );
			$updated = $wpbd->query( $wpbd->prepare( "UPDATE {$wpbd->prefix}ig_campaigns SET status = %d WHERE id IN({$id_str})", $status ) );

			// Changing status of child campaigns along with its parent campaign id
			$wpbd->query( $wpbd->prepare( "UPDATE {$wpbd->prefix}ig_campaigns SET status = %d WHERE parent_id IN({$id_str})", $status ) );
		}

		if ( $updated ) {
			do_action( 'ig_es_after_campaign_status_updated', $campaign_ids, $status );
		}

		return $updated;

	}

	/**
	 * Duplicate Campaign
	 *
	 * @param $id
	 *
	 * @since 4.6.3
	 *
	 * @modify 5.4.9
	 */
	public function duplicate_campaign( $id = null ) {

		if ( ! empty( $id ) ) {

			$campaign           = $this->get( $id );
			$campaign['status'] = IG_ES_CAMPAIGN_STATUS_IN_ACTIVE;
			$campaign['name']   = __( 'Copy', 'email-subscribers' ) . ' - ' . $campaign['name'];

			if ( 'newsletter' === $campaign['type'] ) {
				$campaign['subject'] = __( 'Copy', 'email-subscribers' ) . ' - ' . $campaign['subject'];
			}
			$campaign_id = $campaign['id'];
			unset( $campaign['id'] );
			unset( $campaign['created_at'] );

			$campaign_meta = maybe_unserialize( $campaign['meta'] );
			unset( $campaign_meta['date'], $campaign_meta['es_schedule_date'], $campaign_meta['es_schedule_time'] );
			$campaign['meta'] = maybe_serialize( $campaign_meta );

			$duplicate_campaign_id = $this->save_campaign( $campaign );

			if ( 'sequence' === $campaign['type'] ) {
				$sequence_campaigns = $this->get_campaign_by_parent_id( $campaign_id );

				foreach ( $sequence_campaigns as $index => $child_campaign ) {
					$child_campaign['parent_id'] = $duplicate_campaign_id;
					unset( $child_campaign['id'] );
					unset( $child_campaign['created_at'] );
					$this->save_campaign( $child_campaign );
				}
			}

			return $duplicate_campaign_id;
		}

		return false;
	}

	/**
	 * Get all campaigns based on passed arguements
	 *
	 * @param array $args Campaing arguements
	 *
	 * @return array Array of campaigns
	 *
	 * @since 4.6.11
	 */
	public function get_campaigns( $args = array() ) {
		global $wpbd;
		$where = '';
		$conditions = array(
			'include_types'    => 'type IN',
			'exclude_types'    => 'type NOT IN',
			'status'           => 'status IN',
			'campaigns_in'     => 'id IN',
			'campaigns_not_in' => 'id NOT IN',
		);
	
		foreach ( $conditions as $arg_key => $sql_operator ) {
			if ( ! empty( $args[ $arg_key ] ) ) {
				$count        = count( $args[ $arg_key ] );
				$placeholders = array_fill( 0, $count, '%s' );
				$where .= ( empty( $where ) ? ' ' : ' AND ' ) . $wpbd->prepare( "{$sql_operator} ( " . implode( ',', $placeholders ) . ' )', $args[ $arg_key ] );
			}
		}
	
		$output          = ! empty( $args['output'] ) ? $args['output'] : ARRAY_A;
		$use_cache       = false;
		$order_by_column = ! empty( $args['order_by_column'] ) ? sanitize_key( $args['order_by_column'] ) : '';
		$order           = ! empty( $args['order'] ) ? $args['order'] : '';
	
		if (! empty( $args['is_campaigns_listing'] )) {
			$order .= ' LIMIT %d, %d';
			$order = $wpbd->prepare( $order, $args['offset'], $args['per_page'] );
		} elseif (!empty($args['limit'])) {
			$order .= ' LIMIT %d';
			$order = $wpbd->prepare( $order, $args['limit'] );
		}
	
		return $this->get_by_conditions( $where, $output, $use_cache, $order_by_column, $order );
	}

	public static function get_lists( $per_page = 5, $page_number = 1, $do_count_only = false, $args = array()) {

		global $wpdb;
	
		$order_by = sanitize_sql_orderby(ig_es_get_request_data('orderby'));
		$order = ig_es_get_request_data('order');
	
		if ($do_count_only) {
			$sql = 'SELECT count(*) as total FROM ' . IG_CAMPAIGNS_TABLE;
		} else {
			$sql = 'SELECT * FROM ' . IG_CAMPAIGNS_TABLE;
		}
	
		$query = array();
		$values = array();
		$add_where_clause = true;
	
		$query[] = "(deleted_at IS NULL OR deleted_at = '0000-00-00 00:00:00')";
		$query[] = "type != 'workflow_email'";
	
		if (!empty($args['search_text'])) {
			$query[] = 'name LIKE %s';
			$values[] = '%' . $wpdb->esc_like($args['search_text']) . '%';
		}
		$query = apply_filters('ig_es_campaign_list_where_clause', $query);
	
		if ($add_where_clause) {
			$sql .= ' WHERE ';
	
			if (count($query) > 0) {
				$sql .= implode(' AND ', $query);
	
				if (count($values) > 0) {
					$sql = $wpdb->prepare($sql, $values);
				}
			}
		}
	
		if (isset($args['campaign_type']) && ( !empty($args['campaign_type']) || ( '0' === $args['campaign_type'] ) )) {
			if ($add_where_clause) {
				$sql .= $wpdb->prepare(' AND type = %s', $args['campaign_type']);
			} else {
				$sql .= $wpdb->prepare(' WHERE type = %s', $args['campaign_type']);
			}
		}
	
		if (isset($args['campaign_status']) && ( !empty($args['campaign_status']) || ( '0' === $args['campaign_status'] ) )) {
			if ($add_where_clause) {
				$sql .= $wpdb->prepare(' AND status = %s', $args['campaign_status']);
			} else {
				$sql .= $wpdb->prepare(' WHERE status = %s', $args['campaign_status']);
			}
		}
	
		if (!$do_count_only) {
			$order = !empty($order) ? strtolower($order) : 'desc';
			$expected_order_values = array('asc', 'desc');
			if (!in_array($order, $expected_order_values)) {
				$order = 'desc';
			}
	
			$default_order_by = esc_sql('created_at');
	
			$expected_order_by_values = array('name', 'type', 'created_at');
			if (!in_array($order_by, $expected_order_by_values)) {
				$order_by_clause = " ORDER BY {$default_order_by} DESC";
			} else {
				$order_by = esc_sql($order_by);
				$order_by_clause = " ORDER BY {$order_by} {$order}, {$default_order_by} DESC";
			}
	
			$sql .= $order_by_clause;
			$sql .= " LIMIT $per_page";
			$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
	
			$result = $wpdb->get_results($sql, 'ARRAY_A');
		} else {
			$result = $wpdb->get_var($sql);
		}
	
		return $result;
	}
	
	
	

	/**
	 * Get selected lists ids in the campaign
	 *
	 * @param int $campaign_id
	 *
	 * @return array $list_ids
	 *
	 * @since 4.7.6
	 */
	public function get_list_ids( $campaign_id ) {

		$list_ids = array();

		// If $campaign_id is numeric, then fetch the campaign data based on campaign_id else $campaign_id is campaign data.
		if ( is_numeric( $campaign_id ) ) {
			$campaign = $this->get( $campaign_id );
		} else {
			$campaign = $campaign_id;
		}

		if ( ! empty( $campaign ) ) {
			// Check list ids column since prior to campaign rules features, list ids were being stored in list_ids column.
			if ( ! empty( $campaign['list_ids'] ) ) {
				$list_ids = explode( ',', $campaign['list_ids'] );
			}

			$campaign_meta = maybe_unserialize( $campaign['meta'] );
			$conditions    = isset( $campaign_meta['list_conditions'] ) ? $campaign_meta['list_conditions'] : array();
			if ( ! empty( $conditions ) ) {
				foreach ( $conditions as $i => $condition_group ) {
					if ( ! empty( $condition_group ) ) {
						foreach ( $condition_group as $j => $condition ) {
							$condition_field = isset( $condition['field'] ) ? $condition['field'] : '';
							if ( '_lists__in' === $condition_field ) {
								if ( ! empty( $condition['value'] ) ) {
									if ( is_array( $condition['value'] ) ) {
										$list_ids = array_merge( $list_ids, $condition['value'] );
									} else {
										$list_ids = array( $condition['value'] );
									}
								}
							}
						}
					}
				}
			}
		}

		return $list_ids;
	}

	/**
	 * Get total post_digests count
	 *
	 * @return string|null
	 *
	 * @since 
	 */
	public function get_total_post_digests() {
		return $this->get_total_campaigns_by_type( 'post_digest' );
	}

	/**
	 * Get count of editor type used from campaign meta
	 * 
	 * @return array editor type count
	 *
	 * @since 5.5.7
	 */
	public function get_count_by_editor_type() {

		$campaign_editor_count = array(
			'classic' => 0,
			'dnd'	  => 0,
		);

		$campaign_types = array(
			'include_types' => array( 'newsletter','post_notification','post_digest'),
		);
	
		$campaigns = self::get_campaigns($campaign_types);

		if ( count($campaigns) > 0 ) {
			foreach ($campaigns as $campaign) {
				$campaign_meta = ( !empty( $campaign['meta'] ) ) ? maybe_unserialize($campaign['meta']) : null;

				if ( !empty( $campaign_meta ) ) {
					$editor_type = ! empty( $campaign_meta['editor_type'] ) ? $campaign_meta['editor_type'] : IG_ES_CLASSIC_EDITOR;
					if ( IG_ES_DRAG_AND_DROP_EDITOR === $editor_type ) {
						$campaign_editor_count['dnd']++;
					} else {
						$campaign_editor_count['classic']++;
					}
				}
			}
		}

		return $campaign_editor_count;
	}

	public static function delete_report_data( $campaign_id) {
		global $wpdb; 
		if (!empty($campaign_id)) { 
			$wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}ig_mailing_queue` WHERE campaign_id = %d", $campaign_id));
			$wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}ig_sending_queue` WHERE campaign_id = %d", $campaign_id));
		}
	}
	
}
