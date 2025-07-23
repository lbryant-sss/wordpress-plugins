<?php
/**
 * Tracking API Client class for ExactMetrics.
 *
 * @since 8.0.0
 *
 * @package ExactMetrics
 */

class ExactMetrics_API_Tracking extends ExactMetrics_API_Client {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->base_url = ''; // TODO: Plug in the correct URL
		parent::__construct();
	}
} 