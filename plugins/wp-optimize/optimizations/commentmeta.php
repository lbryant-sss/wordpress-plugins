<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_commentmeta extends WP_Optimization {

	public $ui_sort_order = 9000;

	public $available_for_saving = true;

	private $processed_trash_count;

	private $processed_akismet_count;

	private $found_trash_count;

	private $found_akismet_count;

	/**
	 * Prepare data for preview widget.
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function preview($params) {

		// get clicked comment type link.
		$type = isset($params['type']) && 'akismet' == $params['type'] ? 'akismet' : 'trash';

		// get data requested for preview.
		// Suppress WordPress.DB.PreparedSQL.NotPrepared errors, these are safe and prepared. `$this->wpdb` is $wpdb`
		// phpcs:disable
		$sql = $this->wpdb->prepare(
			"SELECT * FROM `" . $this->wpdb->commentmeta . "`".
			" WHERE ".
			('trash' == $type ? "comment_id NOT IN (SELECT comment_id FROM `" . $this->wpdb->comments . "`)" : "").
			('akismet' == $type ? "meta_key LIKE" : "").
			" %s ORDER BY `comment_ID` LIMIT %d, %d;",
			array(
				('akismet' == $type ? '%akismet%' : ''),
				$params['offset'],
				$params['limit'],
			)
		);

		$posts = $this->wpdb->get_results($sql, ARRAY_A);
		// phpcs:enable

		// fix empty revision titles.
		if (!empty($posts)) {
			foreach ($posts as $key => $post) {
				$posts[$key]['post_title'] = array(
					'text' => '' == $post['post_title'] ? '('.__('no title', 'wp-optimize').')' : $post['post_title'],
					'url' => get_edit_post_link($post['ID']),
				);
			}
		}

		// get total count comments for optimization.
		// Suppress WordPress.DB.PreparedSQL.NotPrepared errors, these are safe and prepared. `$this->wpdb` is $wpdb`
		// phpcs:disable
		$sql = $this->wpdb->prepare(
			"SELECT COUNT(*) FROM `" . $this->wpdb->comments . "`".
				"WHERE ".
				('trash' == $type ? "comment_id NOT IN (SELECT comment_id FROM `" . $this->wpdb->comments . "`)" : "").
				('akismet' == $type ? "meta_key LIKE " : "").
				" %s;",
			array(
				('akismet' == $type ? '%akismet%' : ''),
			)
		);

		$total = $this->wpdb->get_var($sql);
		// phpcs:enable

		return array(
			'id_key' => 'meta_id',
			'columns' => array(
				'meta_id' => __('ID', 'wp-optimize'),
				'comment_id' => __('Comment ID', 'wp-optimize'),
				'meta_key' => __('Meta Key', 'wp-optimize'), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- This is not a query
				'meta_value' => __('Meta Value', 'wp-optimize'), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- This is not a query
			),
			'offset' => $params['offset'],
			'limit' => $params['limit'],
			'total' => $total,
			'data' => $this->htmlentities_array($posts, array('comment_ID')),
			'message' => $total > 0 ? '' : __('No orphaned comment metadata in your database', 'wp-optimize'),
		);
	}

	/**
	 * Do actions before optimize() function.
	 */
	public function before_optimize() {
		$this->processed_trash_count = 0;
		$this->processed_akismet_count = 0;
	}

	/**
	 * Do actions after optimize() function.
	 */
	public function after_optimize() {
		// translators: %s is a number of unused comment metadata item removed
		$message = sprintf(_n('%s unused comment metadata item removed', '%s unused comment metadata items removed', $this->processed_trash_count, 'wp-optimize'), number_format_i18n($this->processed_trash_count));
		// translators: %s is a number of unused akismet comment metadata item removed
		$message1 = sprintf(_n('%s unused akismet comment metadata item removed', '%s unused akismet comment metadata items removed', $this->processed_akismet_count, 'wp-optimize'), number_format_i18n($this->processed_akismet_count));

		if ($this->is_multisite_mode()) {
			// translators: %s is a number of sites
			$blogs_count_text = ' '.sprintf(_n('across %s site', 'across %s sites', count($this->blogs_ids), 'wp-optimize'), count($this->blogs_ids));
			$message .= $blogs_count_text;
			$message1 .= $blogs_count_text;
		}

		$this->logger->info($message);
		$this->register_output($message);

		$this->logger->info($message1);
		$this->register_output($message1);
	}

	/**
	 * This needs reviewing when we review the whole cron-run set of options.
	 */
	public function optimize() {
		$clean = "DELETE FROM `" . $this->wpdb->commentmeta . "` WHERE comment_id NOT IN (SELECT comment_id FROM `" . $this->wpdb->comments . "`)";

		// if posted ids in params, then remove only selected items. used by preview widget.
		if (isset($this->data['ids'])) {
			$clean .= ' AND meta_id in ('.join(',', $this->data['ids']).')';
		}

		$clean .= ";";

		$commentstrash_meta = $this->query($clean);
		$this->processed_trash_count += $commentstrash_meta;

		$clean = "DELETE FROM `" . $this->wpdb->commentmeta . "` WHERE meta_key LIKE '%akismet%'";

		// if posted ids in params, then remove only selected items. used by preview widget.
		if (isset($this->data['ids'])) {
			$clean .= ' AND meta_id in ('.join(',', $this->data['ids']).')';
		}

		$clean .= ";";

		$commentstrash_meta2 = $this->query($clean);
		$this->processed_akismet_count += $commentstrash_meta2;
	}

	/**
	 * Do actions before get_info().
	 */
	public function before_get_info() {

		$this->found_trash_count = 0;
		$this->found_akismet_count = 0;

	}

	/**
	 * Do actions after get_info() function.
	 */
	public function after_get_info() {

		if ($this->found_trash_count > 0) {
			// translators: %s is a number of orphaned comment metadata
			$message = sprintf(_n('%s orphaned comment metadata in your database', '%s orphaned comment metadata in your database', $this->found_trash_count, 'wp-optimize'), number_format_i18n($this->found_trash_count));
		} else {
			$message = __('No orphaned comment metadata in your database', 'wp-optimize');
		}

		if ($this->found_akismet_count > 0) {
			// translators: %s is a number of unused Akismet comment metadata
			$message1 = sprintf(_n('%s unused Akismet comment meta rows in your database', '%s unused Akismet meta rows in your database', $this->found_akismet_count, 'wp-optimize'), number_format_i18n($this->found_akismet_count));
		} else {
			$message1 = __('No Akismet comment meta rows in your database', 'wp-optimize');
		}

		if ($this->is_multisite_mode()) {
			// translators: %s is a number of sites
			$blogs_count_text = ' '.sprintf(_n('across %s site', 'across %s sites', count($this->blogs_ids), 'wp-optimize'), count($this->blogs_ids));
			$message .= $blogs_count_text;
			$message1 .= $blogs_count_text;
		}

		if ($this->found_trash_count > 0) {
			$message = $this->get_preview_link($message, array('data-type' => 'trash'));
		}

		if ($this->found_akismet_count > 0) {
			$message1 = $this->get_preview_link($message1, array('data-type' => 'akismet'));
		}

		$this->register_output($message);
		$this->register_output($message1);

	}

	/**
	 * Get count of unoptimized items.
	 */
	public function get_info() {

		$sql = "SELECT COUNT(*) FROM `" . $this->wpdb->commentmeta . "` WHERE comment_id NOT IN (SELECT comment_id FROM `" . $this->wpdb->comments . "`);";
		$commentmeta = $this->wpdb->get_var($sql); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- this is safe, no user input used
		$this->found_trash_count += $commentmeta;

		$sql = "SELECT COUNT(*) FROM `".$this->wpdb->commentmeta."` WHERE meta_key LIKE '%akismet%';";
		$akismetmeta = $this->wpdb->get_var($sql); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- this is safe, no user input used
		$this->found_akismet_count += $akismetmeta;

	}

	public function settings_label() {
		return __('Clean comment metadata', 'wp-optimize');
	}

	public function get_auto_option_description() {
		return __('Clean comment metadata', 'wp-optimize');
	}
}
