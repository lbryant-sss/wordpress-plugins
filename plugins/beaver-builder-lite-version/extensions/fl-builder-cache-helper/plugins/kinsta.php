<?php
namespace FLCacheClear;

class Kinsta {

	public $name = 'Kinsta Hosting';
	public $url  = 'https://kinsta.com/';

	public static function run() {

		if ( ! defined( 'KINSTAMU_VERSION' ) ) {
			return false;
		}

		if ( version_compare( KINSTAMU_VERSION, '3', '>=' ) ) {
			$response = wp_remote_get(
				'https://localhost/kinsta-clear-cache-all',
				array(
					'sslverify' => false,
					'timeout'   => 5,
				)
			);
		} else {
			$config = array(
				'option_name'    => 'kinsta-cache-settings',
				'immediate_path' => 'https://localhost/kinsta-clear-cache/v2/immediate',
				'throttled_path' => 'https://localhost/kinsta-clear-cache/v2/throttled',
			);

			$default_settings = array(
				'version' => '2.0',
				'options' => array(
					'additional_paths' => array(
						'group'  => array(),
						'single' => array(),
					),
				),
			);

			$kinsta_cache = new \Kinsta\Cache( $config, $default_settings );
			$purge        = new \Kinsta\Cache_Purge( $kinsta_cache );
			if ( is_object( $purge ) && method_exists( $purge, 'purge_complete_full_page_cache' ) ) {
				$response = $purge->purge_complete_full_page_cache();
			}
		}
	}
}
