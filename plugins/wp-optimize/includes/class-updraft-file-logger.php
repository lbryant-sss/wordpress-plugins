<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (class_exists('Updraft_File_Logger')) return;

/**
 * Class Updraft_File_Logger
 */
class Updraft_File_Logger extends Updraft_Abstract_Logger {

	/**
	 * Path to the log file
	 *
	 * @var String
	 */
	private $logfile;

	/**
	 * Updraft_File_Logger constructor
	 */
	public function __construct($logfile) {
		$this->logfile = $logfile;
	}

	/**
	 * Returns logger description
	 *
	 * @return string|void
	 */
	public function get_description() {
		return __('Log events into a log file', 'wp-optimize');
	}

	/**
	 * Emergency message
	 *
	 * @param  string $message
	 * @param  array  $context
	 * @return null|void
	 */
	public function emergency($message, array $context = array()) {
		$this->log($message, Updraft_Log_Levels::EMERGENCY, $context);
	}

	/**
	 * Alert message
	 *
	 * @param  string $message
	 * @param  array  $context
	 * @return null|void
	 */
	public function alert($message, array $context = array()) {
		$this->log($message, Updraft_Log_Levels::ALERT, $context);
	}

	/**
	 * Critical message
	 *
	 * @param  string $message
	 * @param  array  $context
	 * @return null|void
	 */
	public function critical($message, array $context = array()) {
		$this->log($message, Updraft_Log_Levels::CRITICAL, $context);
	}

	/**
	 * Error message
	 *
	 * @param  string $message
	 * @param  array  $context
	 * @return null|void
	 */
	public function error($message, array $context = array()) {
		$this->log($message, Updraft_Log_Levels::ERROR, $context);
	}

	/**
	 * Warning message
	 *
	 * @param  string $message
	 * @param  array  $context
	 * @return null|void
	 */
	public function warning($message, array $context = array()) {
		$this->log($message, Updraft_Log_Levels::WARNING, $context);
	}

	/**
	 * Notice message
	 *
	 * @param  string $message
	 * @param  array  $context
	 * @return null|void
	 */
	public function notice($message, array $context = array()) {
		$this->log($message, Updraft_Log_Levels::NOTICE, $context);
	}

	/**
	 * Info message
	 *
	 * @param  string $message
	 * @param  array  $context
	 * @return null|void
	 */
	public function info($message, array $context = array()) {
		$this->log($message, Updraft_Log_Levels::INFO, $context);
	}

	/**
	 * Debug message
	 *
	 * @param  string $message
	 * @param  array  $context
	 * @return null|void
	 */
	public function debug($message, array $context = array()) {
		$this->log($message, Updraft_Log_Levels::DEBUG, $context);
	}

	/**
	 * Log message with any level
	 *
	 * @param  string $message
	 * @param  mixed  $level
	 * @param  array  $context
	 * @return null|void
	 */
	public function log($message, $level, array $context = array()) {

		if (!$this->is_enabled()) return false;
		
		$message = sprintf("[%s : %s] - %s \n", gmdate("Y-m-d H:i:s"), Updraft_Log_Levels::to_text($level), $this->interpolate($message, $context));
		
		if (false == file_put_contents($this->logfile, $message, FILE_APPEND)) {
			error_log($message); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Using for debugging purpose
		}
	}

	/**
	 * Delete logs older than specified date
	 *
	 * @param  string $how_old
	 * @return boolean Success or failure
	 */
	public function prune_logs($how_old = "5 days ago") {

		// If the $how_old string is invalid revert to default "5 days ago"
		$prune_period = strtotime($how_old);
		if (!$prune_period) {
			$prune_period = strtotime("5 days ago");
		}

		// phpcs:disable

		// We ignore a few lines here to avoid warnings on file operations
		// WP.VIP does not like us writing directly to the filesystem
		if (!is_file($this->logfile)) return false;
		$logfile_handle = fopen($this->logfile, "r");
		if (false === $logfile_handle) return false;
		$temp_file = fopen(preg_replace("/\.log$/", "-temp.log", $this->logfile), "a");

		// Stream is the preferred way because of potentially large file sizes
		while ($line = stream_get_line($logfile_handle, 1024 * 1024, "\n")) {

			$pattern = '/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/';
			if (preg_match($pattern, $line, $matches)) {
				if (strtotime($matches[0]) > $prune_period) {
					fwrite($temp_file, $line."\n");
				}
			}
		}

		fclose($logfile_handle);
		fclose($temp_file);

		return rename(preg_replace("/\.log$/", "-temp.log", $this->logfile), $this->logfile);
		// phpcs:enable
	}
}
