<?php
/**
 * LiteSpeed Cache CLI Crawler Commands
 *
 * Provides WP-CLI commands for managing LiteSpeed Cache crawlers.
 *
 * @package LiteSpeed
 * @since 1.1.0
 */

namespace LiteSpeed\CLI;

defined('WPINC') || exit();

use LiteSpeed\Debug2;
use LiteSpeed\Base;
use LiteSpeed\Task;
use LiteSpeed\Crawler as Crawler2;
use WP_CLI;

/**
 * Crawler
 */
class Crawler extends Base {
	/**
	 * Crawler instance
	 *
	 * @var Crawler2 $crawler
	 */
	private $crawler;

	/**
	 * Constructor for Crawler CLI commands
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		Debug2::debug('CLI_Crawler init');

		$this->crawler = Crawler2::cls();
	}

	/**
	 * List all crawlers
	 *
	 * Displays a table of all crawlers with their details.
	 *
	 * ## OPTIONS
	 *
	 * ## EXAMPLES
	 *
	 *     # List all crawlers
	 *     $ wp litespeed-crawler l
	 *
	 * @since 1.1.0
	 */
	public function l() {
		$this->list();
	}

	/**
	 * List all crawlers
	 *
	 * Displays a table of all crawlers with their details.
	 *
	 * ## OPTIONS
	 *
	 * ## EXAMPLES
	 *
	 *     # List all crawlers
	 *     $ wp litespeed-crawler list
	 *
	 * @since 1.1.0
	 */
	public function list() {
		$crawler_list = $this->crawler->list_crawlers();
		$summary      = Crawler2::get_summary();
		if ($summary['curr_crawler'] >= count($crawler_list)) {
			$summary['curr_crawler'] = 0;
		}
		$is_running = time() - $summary['is_running'] <= 900;

		$CRAWLER_RUN_INTERVAL = defined('LITESPEED_CRAWLER_RUN_INTERVAL') ? LITESPEED_CRAWLER_RUN_INTERVAL : 600; // Specify time in seconds for the time between each run interval
		if ($CRAWLER_RUN_INTERVAL > 0) {
			$recurrence = '';
			$hours      = (int) floor($CRAWLER_RUN_INTERVAL / 3600);
			if ($hours) {
				if ($hours > 1) {
					$recurrence .= sprintf(__('%d hours', 'litespeed-cache'), $hours);
				} else {
					$recurrence .= sprintf(__('%d hour', 'litespeed-cache'), $hours);
				}
			}
			$minutes = (int) floor(($CRAWLER_RUN_INTERVAL % 3600) / 60);
			if ($minutes) {
				$recurrence .= ' ';
				if ($minutes > 1) {
					$recurrence .= sprintf(__('%d minutes', 'litespeed-cache'), $minutes);
				} else {
					$recurrence .= sprintf(__('%d minute', 'litespeed-cache'), $minutes);
				}
			}
		}

		$list = array();
		foreach ($crawler_list as $i => $v) {
			$hit  = !empty($summary['crawler_stats'][$i][Crawler2::STATUS_HIT]) ? $summary['crawler_stats'][$i][Crawler2::STATUS_HIT] : 0;
			$miss = !empty($summary['crawler_stats'][$i][Crawler2::STATUS_MISS]) ? $summary['crawler_stats'][$i][Crawler2::STATUS_MISS] : 0;

			$blacklisted  = !empty($summary['crawler_stats'][$i][Crawler2::STATUS_BLACKLIST]) ? $summary['crawler_stats'][$i][Crawler2::STATUS_BLACKLIST] : 0;
			$blacklisted += !empty($summary['crawler_stats'][$i][Crawler2::STATUS_NOCACHE]) ? $summary['crawler_stats'][$i][Crawler2::STATUS_NOCACHE] : 0;

			if (isset($summary['crawler_stats'][$i][Crawler2::STATUS_WAIT])) {
				$waiting = $summary['crawler_stats'][$i][Crawler2::STATUS_WAIT] ?? 0;
			} else {
				$waiting = $summary['list_size'] - $hit - $miss - $blacklisted;
			}

			$analytics  = 'Waiting: ' . $waiting;
			$analytics .= '     Hit: ' . $hit;
			$analytics .= '     Miss: ' . $miss;
			$analytics .= '     Blocked: ' . $blacklisted;

			$running = '';
			if ($i === $summary['curr_crawler']) {
				$running = 'Pos: ' . ($summary['last_pos'] + 1);
				if ($is_running) {
					$running .= '(Running)';
				}
			}

			$status = $this->crawler->is_active($i) ? '✅' : '❌';

			$list[] = array(
				'ID' => $i + 1,
				'Name' => wp_strip_all_tags($v['title']),
				'Frequency' => $recurrence,
				'Status' => $status,
				'Analytics' => $analytics,
				'Running' => $running,
			);
		}

		WP_CLI\Utils\format_items('table', $list, array( 'ID', 'Name', 'Frequency', 'Status', 'Analytics', 'Running' ));
	}

	/**
	 * Enable one crawler
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The ID of the crawler to enable.
	 *
	 * ## EXAMPLES
	 *
	 *     # Turn on 2nd crawler
	 *     $ wp litespeed-crawler enable 2
	 *
	 * @since 1.1.0
	 * @param array $args Command arguments.
	 */
	public function enable( $args ) {
		$id = $args[0] - 1;
		if ($this->crawler->is_active($id)) {
			WP_CLI::error('ID #' . $id . ' had been enabled');
			return;
		}

		$this->crawler->toggle_activeness($id);
		WP_CLI::success('Enabled crawler #' . $id);
	}

	/**
	 * Disable one crawler
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The ID of the crawler to disable.
	 *
	 * ## EXAMPLES
	 *
	 *     # Turn off 1st crawler
	 *     $ wp litespeed-crawler disable 1
	 *
	 * @since 1.1.0
	 * @param array $args Command arguments.
	 */
	public function disable( $args ) {
		$id = $args[0] - 1;
		if (!$this->crawler->is_active($id)) {
			WP_CLI::error('ID #' . $id . ' has been disabled');
			return;
		}

		$this->crawler->toggle_activeness($id);
		WP_CLI::success('Disabled crawler #' . $id);
	}

	/**
	 * Run crawling
	 *
	 * ## OPTIONS
	 *
	 * ## EXAMPLES
	 *
	 *     # Start crawling
	 *     $ wp litespeed-crawler r
	 *
	 * @since 1.1.0
	 */
	public function r() {
		$this->run();
	}

	/**
	 * Run crawling
	 *
	 * ## OPTIONS
	 *
	 * ## EXAMPLES
	 *
	 *     # Start crawling
	 *     $ wp litespeed-crawler run
	 *
	 * @since 1.1.0
	 */
	public function run() {
		self::debug('⚠️⚠️⚠️ Forced take over lane (CLI)');
		$this->crawler->Release_lane();

		Task::async_call('crawler');

		$summary = Crawler2::get_summary();

		WP_CLI::success('Start crawling. Current crawler #' . ($summary['curr_crawler'] + 1) . ' [position] ' . $summary['last_pos'] . ' [total] ' . $summary['list_size']);
	}

	/**
	 * Reset crawler position
	 *
	 * ## OPTIONS
	 *
	 * ## EXAMPLES
	 *
	 *     # Reset crawler position
	 *     $ wp litespeed-crawler reset
	 *
	 * @since 1.1.0
	 */
	public function reset() {
		$this->crawler->reset_pos();

		$summary = Crawler2::get_summary();

		WP_CLI::success('Reset position. Current crawler #' . ($summary['curr_crawler'] + 1) . ' [position] ' . $summary['last_pos'] . ' [total] ' . $summary['list_size']);
	}
}
