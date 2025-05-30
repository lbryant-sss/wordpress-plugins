<?php
if (!defined('ABSPATH') && !defined('MCDATAPATH')) exit;

if (!class_exists('BVProtectLP_V602')) :
class BVProtectLP_V602 {
	private $ip;
	private $time;
	private $ipstore;
	private $logger;
	private $brand_name;

	private $mode              = BVProtectLP_V602::MODE_DISABLED;
	private $captcha_limit     = 3;
	private $temp_block_limit  = 10;
	private $block_all_limit   = 100;
	private $failed_login_gap  = 1800;
	private $success_login_gap = 1800;
	private $all_blocked_gap   = 1800;

	private $category          = BVProtectLP_V602::CATEGORY_ALLOWED;
	private $username          = '';
	private $message           = '';

	private static $instance;

	const TABLE_NAME                  = 'lp_requests';
	const UNBLOCK_IP_TRANSIENT_PREFIX = 'bvlp_unblock_ip';

	const MODE_DISABLED = 1;
	const MODE_AUDIT    = 2;
	const MODE_PROTECT  = 3;

	const LOGIN_STATUS_FAILURE = 1;
	const LOGIN_STATUS_SUCCESS = 2;
	const LOGIN_STATUS_BLOCKED = 3;

	const CATEGORY_CAPTCHA_BLOCK = 1;
	const CATEGORY_TEMP_BLOCK    = 2;
	const CATEGORY_ALL_BLOCKED   = 3;
	const CATEGORY_UNBLOCKED     = 4;
	const CATEGORY_BLACKLISTED   = 5;
	const CATEGORY_BYPASSED      = 6;
	const CATEGORY_ALLOWED       = 7;
	const CATEGORY_PRIVATEIP     = 8;

	private function __construct($request, $config, $brand_name) {
		$this->ip = $request->getIP();
		$this->brand_name = $brand_name;
		$this->ipstore = new BVProtectIpstore_V602();
		$this->logger = new BVProtectLogger_V602(BVProtectLP_V602::TABLE_NAME);
		$this->time = strtotime(gmdate("Y-m-d H:i:s"));

		if (is_array($config)) {
			if (array_key_exists('mode', $config) && is_int($config['mode'])) {
				$this->mode = $config['mode'];
			}

			if (array_key_exists('captchalimit', $config) && is_int($config['captchalimit'])) {
				$this->captcha_limit = $config['captchalimit'];
			}

			if (array_key_exists('tempblocklimit', $config) && is_int($config['tempblocklimit'])) {
				$this->temp_block_limit = $config['tempblocklimit'];
			}

			if (array_key_exists('blockalllimit', $config) && is_int($config['blockalllimit'])) {
				$this->block_all_limit = $config['blockalllimit'];
			}

			if (array_key_exists('failedlogingap', $config) && is_int($config['failedlogingap'])) {
				$this->failed_login_gap = $config['failedlogingap'];
			}

			if (array_key_exists('successlogingap', $config) && is_int($config['successlogingap'])) {
				$this->success_login_gap = $config['successlogingap'];
			}

			if (array_key_exists('allblockedgap', $config) && is_int($config['allblockedgap'])) {
				$this->all_blocked_gap = $config['allblockedgap'];
			}
		}
	}

	public static function getInstance($request, $config, $brand_name) {
		if (!isset(self::$instance)) {
			self::$instance = new self($request, $config, $brand_name);
		}

		return self::$instance;
	}

	public static function uninstall() {
		BVProtect_V602::$db->dropBVTable(BVProtectLP_V602::TABLE_NAME);
	}

	public function init() {
		if ($this->isActive()) {
			add_filter('authenticate', array($this, 'loginInit'), 30, 3);
			add_action('wp_login', array($this, 'loginSuccess'));
			add_action('wp_login_failed', array($this, 'loginFailed'));
		}
	}

	private function getCaptchaLink() {
		$account = BVAccount::apiPublicAccount(BVProtect_V602::$settings);

		$url = $account->authenticatedUrl('/captcha/solve');
		$url .= "&adminurl=".base64_encode(get_admin_url());

		return $url;
	}

	private function getAllowLoginsTransient() {
		return BVProtect_V602::$settings->getTransient('bvlp_allow_logins');
	}

	private function getBlockLoginsTransient() {
		return BVProtect_V602::$settings->getTransient('bvlp_block_logins');
	}

	private function terminateTemplate() {
		$templates = array (
			1 => "<p>Too many failed attempts, You are barred from logging into this site.</p>" .
						"<a href=" . esc_url($this->getCaptchaLink()) . " class='btn btn-default'>Click here</a>" .
						" to unblock yourself.",
			2 => "You cannot login to this site for 30 minutes because of too many failed login attempts.",
			3 => "<p>Logins to this site are currently blocked.</p><a href=" . esc_url($this->getCaptchaLink()) .
						" class='btn btn-default'>Click here</a> to unblock yourself.",
			5 => "Your IP is blacklisted."
		);

		return "
			<div style='height: 98vh;'>
				<div style='text-align: center; padding: 10% 0; font-family: Arial, Helvetica, sans-serif;'>
					<div><p><img src=". plugins_url('/../img/icon.png', __FILE__) . "><h2>Login Protection</h2><h3>powered by</h3><h2>"
							. esc_html($this->brand_name) . " Firewall</h2></p><div>
					<p>" . $templates[$this->category] . "</p>
					<p>Reference ID: " . esc_html(BVInfo::getRequestID()) . "</p>
				</div>
			</div>";
	}

	private function isProtecting() {
		return $this->mode === BVProtectLP_V602::MODE_PROTECT;
	}

	private function isActive() {
		return $this->mode !== BVProtectLP_V602::MODE_DISABLED;
	}

	private function isBlacklistedIP() {
		return $this->ipstore->isLPIPBlacklisted($this->ip);
	}

	private function isWhitelistedIP() {
		return $this->ipstore->isLPIPWhitelisted($this->ip);
	}

	private function isUnBlockedIP() {
		$transient_name = BVProtectLP_V602::UNBLOCK_IP_TRANSIENT_PREFIX . $this->ip;
		$attempts = BVProtect_V602::$settings->getTransient($transient_name);

		if ($attempts && $attempts > 0) {
			BVProtect_V602::$settings->setTransient($transient_name, $attempts - 1, 600 * $attempts);
			return true;
		}

		return false;
	}

	private function isLoginBlocked() {
		if ($this->getAllowLoginsTransient() ||
				($this->getLoginCount(BVProtectLP_V602::LOGIN_STATUS_FAILURE, null, $this->all_blocked_gap) < $this->block_all_limit)) {
			return false;
		}

		return true;
	}

	private function log($status) {
		$data = array (
			"ip" => $this->ip,
			"status" => $status,
			"time" => $this->time,
			"category" => $this->category,
			"username" => $this->username,
			"request_id" => BVInfo::getRequestID(),
			"message" => $this->message
		);

		$this->logger->log($data);
	}

	private function terminateLogin() {
		$this->message = 'Login Blocked';
		$this->log(BVProtectLP_V602::LOGIN_STATUS_BLOCKED);
		if ($this->isProtecting()) {
			header("Cache-Control: no-cache, no-store, must-revalidate");
			header("Pragma: no-cache");
			header("Expires: 0");
			header('HTTP/1.0 403 Forbidden');
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already Escaped
			die($this->terminateTemplate());
			exit;
		}
	}

	public function loginInit($user, $username = '', $password = '') {
		if ($this->isUnBlockedIP()) {
			$this->category = BVProtectLP_V602::CATEGORY_UNBLOCKED;
		} else {
			$failed_attempts = $this->getLoginCount(BVProtectLP_V602::LOGIN_STATUS_FAILURE,
																							$this->ip, $this->failed_login_gap);

			if ($this->isWhitelistedIP()) {
				$this->category = BVProtectLP_V602::CATEGORY_BYPASSED;
			} elseif (BVProtectUtils_V602::isPrivateIP($this->ip)) {
				$this->category = BVProtectLP_V602::CATEGORY_PRIVATEIP;
			} elseif ($this->isBlacklistedIP()) {
				$this->category = BVProtectLP_V602::CATEGORY_BLACKLISTED;
				$this->terminateLogin();
			} elseif ($this->isKnownLogin()) {
				$this->category = BVProtectLP_V602::CATEGORY_BYPASSED;
			} elseif ($this->isLoginBlocked()) {
				$this->category = BVProtectLP_V602::CATEGORY_ALL_BLOCKED;
				$this->terminateLogin();
			} elseif ($failed_attempts >= $this->temp_block_limit) {
				$this->category = BVProtectLP_V602::CATEGORY_TEMP_BLOCK;
				$this->terminateLogin();
			} elseif ($failed_attempts >= $this->captcha_limit) {
				$this->category = BVProtectLP_V602::CATEGORY_CAPTCHA_BLOCK;
				$this->terminateLogin();
			}
		}

		if (!empty($user) && !empty($password) && is_wp_error($user)) {
			$this->message = $user->get_error_code();
		}

		return $user;
	}

	public function loginFailed($username) {
		$this->username = $username;
		$this->log(BVProtectLP_V602::LOGIN_STATUS_FAILURE);
	}

	public function loginSuccess($username) {
		$this->username = $username;
		$this->message = 'Login Success';
		$this->log(BVProtectLP_V602::LOGIN_STATUS_SUCCESS);
	}

	private function isKnownLogin() {
		return $this->getLoginCount(BVProtectLP_V602::LOGIN_STATUS_SUCCESS,
																$this->ip, $this->success_login_gap) > 0;
	}

	private function getLoginCount($status, $ip, $gap) {
		$table = BVProtect_V602::$db->getBVTable(BVProtectLP_V602::TABLE_NAME);
		$query_str = "SELECT COUNT(*) as count from `$table` WHERE status=%d && time > %d";
		$query_args = array($status, ($this->time - $gap));

		$query = BVProtect_V602::$db->prepare($query_str, $query_args);
		if ($ip) {
			$query .= BVProtect_V602::$db->prepare(" && ip=%s", $ip);
		}

		$rows = BVProtect_V602::$db->getResult($query);
		if (!$rows) {
			return 0;
		}

		return intval($rows[0]['count']);
	}
}
endif;