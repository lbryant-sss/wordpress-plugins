<?php

if (!defined('ABSPATH') && !defined('MCDATAPATH')) exit;

if (!class_exists('BVProtectRequest_V602')) :
class BVProtectRequest_V602 {
	public $ip;
	public $host = '';
	public $uri;
	public $method = '';
	public $path = '';
	public $timestamp;
	public $get_params;
	public $post_params;
	public $cookies;
	public $headers = array();
	public $file_names = array();
	public $json_params = array();
	public $raw_body = '';
	public $files;
	public $respcode;
	public $status = BVProtectRequest_V602::STATUS_ALLOWED;
	public $category = BVProtectRequest_V602::CATEGORY_NORMAL;

	public $wp_user;

	private $can_get_raw_body = false;
	private $max_raw_body_length = 1000000;
	private $can_decode_json = false;
	private $max_json_decode_depth = 512;

	#XNOTE: SHould be part of Protect.
	const STATUS_ALLOWED  = 1;
	const STATUS_BLOCKED  = 2;
	const STATUS_BYPASSED = 3;

	const CATEGORY_BLACKLISTED        = 1;
	const CATEGORY_NORMAL             = 10;
	const CATEGORY_WHITELISTED        = 20;
	const CATEGORY_BOT_BLOCKED        = 30;
	const CATEGORY_COUNTRY_BLOCKED    = 40;
	const CATEGORY_USER_BLACKLISTED   = 50;
	const CATEGORY_RULE_BLOCKED       = 60;
	const CATEGORY_RULE_ALLOWED       = 70;
	const CATEGORY_PRIVATEIP          = 80;
	const CATEGORY_GLOBAL_BOT_BLOCKED = 90;

	public function __construct($ip_header, $config) {
		$this->ip = BVProtectUtils_V602::getIP($ip_header);
		$this->timestamp = time();
		$this->get_params = $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$this->cookies = $_COOKIE;
		$this->post_params = $_POST; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$this->files = $_FILES; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if (array_key_exists('cangetrawbody', $config) && is_bool($config['cangetrawbody'])) {
			$this->can_get_raw_body = $config['cangetrawbody'];
		}

		if (array_key_exists('maxrawbodylength', $config) && is_int($config['maxrawbodylength'])) {
			$this->max_raw_body_length = $config['maxrawbodylength'];
		}

		if (array_key_exists('candecodejson', $config) && is_bool($config['candecodejson'])) {
			$this->can_decode_json = $config['candecodejson'];
		}

		if (array_key_exists('maxjsondecodedepth', $config) && is_int($config['maxjsondecodedepth'])) {
			$this->max_json_decode_depth = $config['maxjsondecodedepth'];
		}

		if (!empty($_FILES)) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			foreach ($_FILES as $input => $file) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				$this->file_names[$input] = $file['name'];
			}
		}
		if (is_array($_SERVER)) {
			foreach ($_SERVER as $key => $value) {
				if (strpos($key, 'HTTP_') === 0) {
					$header = substr($key, 5);
					$header = str_replace(array(' ', '_'), array('', ' '), $header);
					$header = ucwords(strtolower($header));
					$header = str_replace(' ', '-', $header);
					$this->headers[$header] = $value;
				}
			}
			$content_type = BVHelper::getRawParam('SERVER', 'CONTENT_TYPE');
			if (isset($content_type)) {
				$this->headers['Content-Type'] = $content_type;
			}
			$content_length = BVHelper::getRawParam('SERVER', 'CONTENT_LENGTH');
			if (isset($content_length)) {
				$this->headers['Content-Length'] = $content_length;
			}
			$referer = BVHelper::getRawParam('SERVER', 'REFERER');
			if (isset($referer)) {
				$this->headers['Referer'] = $referer;
			}
			$http_user_agent = BVHelper::getRawParam('SERVER', 'HTTP_USER_AGENT');
			if (isset($http_user_agent)) {
				$this->headers['User-Agent'] = $http_user_agent;
			}

			if (array_key_exists('Host', $this->headers)) {
				$this->host = $this->headers['Host'];
			} elseif (array_key_exists('SERVER_NAME', $_SERVER)) {
				$this->host = BVHelper::getRawParam('SERVER', 'SERVER_NAME');
			}

			$request_method = BVHelper::getRawParam('SERVER', 'REQUEST_METHOD');
			$this->method = isset($request_method) ? $request_method : 'GET';
			$request_uri = BVHelper::getRawParam('SERVER', 'REQUEST_URI');
			$this->uri = isset($request_uri) ? $request_uri : '';
			$_uri = parse_url($this->uri);
			$this->path = (is_array($_uri) && array_key_exists('path', $_uri)) ? $_uri['path']  : $this->uri;
		}

		if ($this->can_get_raw_body) {
			$_raw_body = file_get_contents("php://input", false, null, 0, $this->max_raw_body_length);
			if ($_raw_body !== false) {
				$this->raw_body = $_raw_body;
			}
		}

		if ($this->can_decode_json) {
			if ($this->getContentType() === "application/json" && !empty($this->raw_body)) {
				$_json_params = BVProtectUtils_V602::safeDecodeJSON($this->raw_body,
						true, $this->max_json_decode_depth);
				if (isset($_json_params)) {
					$this->json_params['JSON'] = $_json_params;
				}
			}
		}
	}

	public static function blacklistedCategories() {
		return array(
			BVProtectRequest_V602::CATEGORY_BOT_BLOCKED,
			BVProtectRequest_V602::CATEGORY_COUNTRY_BLOCKED,
			BVProtectRequest_V602::CATEGORY_USER_BLACKLISTED,
			BVProtectRequest_V602::CATEGORY_GLOBAL_BOT_BLOCKED
		);
	}

	public static function whitelistedCategories() {
		return array(BVProtectRequest_V602::CATEGORY_WHITELISTED);
	}

	public function setRespCode($code) {
		$this->respcode = $code;
	}

	public function getRespCode() {
		if (!isset($this->respcode) && function_exists('http_response_code')) {
			$this->respcode = http_response_code();
		}

		return $this->respcode;
	}

	public function getStatus() {
		return $this->status;
	}

	public function getCategory() {
		return $this->category;
	}

	private function getKeyVal($array, $key) {
		if (is_array($array)) {
			if (is_array($key)) {
				$_key = array_shift($key);
				if (array_key_exists($_key, $array)) {
					if (count($key) > 0) {
						return $this->getKeyVal($array[$_key], $key);
					} else {
						return $array[$_key];
					}
				}
			} else {
				return array_key_exists($key, $array) ? $array[$key] : null;
			}
		}
		return null;
	}

	public function getPostParams() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			return $this->getKeyVal($this->post_params, $args);
		}
		return $this->post_params;
	}

	public function getCookies() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			return $this->getKeyVal($this->cookies, $args);
		}
		return $this->cookies;
	}
	
	public function getGetParams() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			return $this->getKeyVal($this->get_params, $args);
		}
		return $this->get_params;
	}

	public function getAllParams() {
		return array("getParams" => $this->get_params, "postParams" => $this->post_params, "jsonParams" => $this->json_params);
	}

	public function getHeader($key) {
		if (array_key_exists($key, $this->headers)) {
			return $this->headers[$key];
		}
		return null;
	}

	public function getHeaders() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			return $this->getKeyVal($this->headers, $args);
		}
		return $this->headers;
	}

	public function getFiles() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			return $this->getKeyVal($this->files, $args);
		}
		return $this->files;
	}

	public function getFileNames() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			return $this->getKeyVal($this->file_names, $args);
		}
		return $this->file_names;
	}

	public function getHost() {
		return $this->host;
	}

	public function getURI() {
		return $this->uri;
	}

	public function getAction() {
		$post_action = $this->getPostParams('action');
		if (isset($post_action)) {
			return $post_action;
		} else {
			return $this->getGetParams('action');
		}
	}

	public function getPath() {
		return $this->path;
	}

	public function getIP() {
		return $this->ip;
	}

	public function getMethod() {
		return $this->method;
	}

	public function getTimestamp() {
		return $this->timestamp;
	}

	public function getRequestID() {
		if (!defined("BV_REQUEST_ID")) {
			define("BV_REQUEST_ID", uniqid(mt_rand())); // phpcs:ignore WordPress.WP.AlternativeFunctions.rand_mt_rand
		}

		return BV_REQUEST_ID;
	}

	public function getServerValue($key) {
		$val = BVHelper::getRawParam('SERVER', $key);
		return isset($val) ? $val : false;
	}

	public function getHeadersV2() {
		return $this->headers;
	}

	public function getFilesV2() {
		return $this->files;
	}

	public function getFileNamesV2() {
		return $this->file_names;
	}

	public function getPostParamsV2() {
		return $this->post_params;
	}

	public function getGetParamsV2() {
		return $this->get_params;
	}

	public function getCookiesV2() {
		return $this->cookies;
	}

	public function getJsonParams() {
		return $this->json_params;
	}

	public function getRawBody() {
		return $this->raw_body;
	}

	public function getContentType() {
		if (array_key_exists('Content-Type', $this->headers)) {
			return $this->headers['Content-Type'];
		}
	}

	public function getContentLength() {
		if (array_key_exists('Content-Length', $this->headers)) {
			return $this->headers['Content-Length'];
		}
	}
}
endif;