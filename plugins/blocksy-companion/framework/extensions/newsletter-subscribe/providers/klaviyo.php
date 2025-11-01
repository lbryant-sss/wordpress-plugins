<?php

namespace Blocksy\Extensions\NewsletterSubscribe;

class KlaviyoProvider extends Provider {
	public function __construct() {
	}

	public function fetch_lists($api_key, $api_url = '') {
		$response = wp_remote_get(
			'https://a.klaviyo.com/api/lists',
			[
				'timeout' => 2,
				'headers' => [
					'Authorization' => 'Klaviyo-API-Key ' . $api_key,
					'accept' => 'application/json',
					'revision' => gmdate('Y-m-d')
				]
			]
		);

		if (! is_wp_error($response)) {
			if (200 !== wp_remote_retrieve_response_code($response)) {
				return 'api_key_invalid';
			}

			$body = json_decode(wp_remote_retrieve_body($response), true);

			if (! $body) {
				return 'api_key_invalid';
			}

			if (! isset($body['data'])) {
				return 'api_key_invalid';
			}
		}

		return array_map(function($list) {
			return [
				'name' => $list['attributes']['name'],
				'id' => $list['id'],
			];
		}, $body['data']);
	}

	public function get_form_url_and_gdpr_for($maybe_custom_list = null) {
		return [
			'form_url' => '#',
			'has_gdpr_fields' => false,
			'provider' => 'klaviyo'
		];
	}

	public function subscribe_form($args = []) {
		$args = wp_parse_args($args, [
			'email' => '',
			'name' => '',
			'group' => ''
		]);

		$settings = $this->get_settings();

		$lname = '';
		$fname = '';

		if (! empty($args['name'])) {
			$parts = explode(' ', $args['name']);

			$lname = array_pop($parts);
			$fname = implode(' ', $parts);
		}

		$list_ids = [$args['group']];

		$subscriber = [
			'email' => $args['email'],
			'first_name' => $fname,
			'last_name' => $lname
		];

		// phpcs:ignore WordPress.WP.AlternativeFunctions.curl_curl_init
		$curl = curl_init();

		// phpcs:ignore WordPress.WP.AlternativeFunctions.curl_curl_setopt_array
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://a.klaviyo.com/api/profile-import',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 2,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode([
				'data' => [
					'type' => 'profile',
					'attributes' => [
						'list_ids' => $list_ids,
						'email' => $subscriber['email'],
						'first_name' => $subscriber['first_name'],
						'last_name' => $subscriber['last_name'],
					]
				]
			]),
			CURLOPT_HTTPHEADER => array(
				'Authorization: Klaviyo-API-Key ' . $settings['api_key'],
				'accept: application/vnd.api+json',
				'content-type: application/vnd.api+json',
				'revision: ' . gmdate('Y-m-d')
			),
		));


		return [
			'result' => 'yes',
			'message' => __('Thank you for subscribing to our newsletter!', 'blocksy-companion')
		];
	}
}

