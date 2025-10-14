<?php
/**
 * Handles fake bot detection functionality.
 *
 * @package WP_Defender\Component
 */

namespace WP_Defender\Component;

use WP_Defender\Component;
use WP_Defender\Controller\Firewall;
use WP_Defender\Model\Lockout_Log;
use WP_Defender\Model\Setting\User_Agent_Lockout;
use WP_Defender\Model\Lockout_Ip;
use WP_Defender\Traits\Country;

/**
 * Class Fake_Bot_Detection
 *
 * Detects whether the current HTTP request comes from a legitimate search engine
 * or monitoring crawler (Googlebot, Bingbot, Yandex, etc.).
 */
class Fake_Bot_Detection extends Component {
	use Country;

	public const SCENARIO_FAKE_BOT = 'fake_bot';

	/**
	 * Remote JSON URL containing crawler definitions.
	 *
	 * @var string
	 */
	public const REMOTE_URL = 'https://gist.githubusercontent.com/incsub/8ab5815980fc8af44fe0223f8dcddb66/raw/3ddcc5be3b007afc8cd4f4af4b45f36d5c60508e/crawlers.json';

	/**
	 * Local fallback path to crawler definitions JSON.
	 *
	 * @var string
	 */
	public const LOCAL_FILE = 'includes/crawlers.json';

	/**
	 * WP transient key for caching crawler data.
	 */
	public const CACHE_KEY = 'wpmu_crawlers';

	/**
	 * Cache expiration (12 hours).
	 */
	public const CACHE_TTL = 12 * HOUR_IN_SECONDS;

	/**
	 * Array of crawler definitions loaded from JSON.
	 *
	 * Format example:
	 * [
	 *   "Google" => [
	 *      "ips" => ["ipv4" => [], "ipv6" => []],
	 *      "hostname_pattern" => "googlebot.com",
	 *      "user_agents" => ["Googlebot"]
	 *   ],
	 *   ...
	 * ]
	 *
	 * @var array<string, array>
	 */
	protected array $crawlers = array();

	/**
	 * The model for handling the data.
	 *
	 * @var User_Agent_Lockout
	 */
	protected $model;

	/**
	 * Fake_Bot_Detection constructor.
	 *
	 * @param User_Agent_Lockout $model The model instance for fake bot detection functionality.
	 */
	public function __construct( User_Agent_Lockout $model ) {
		$this->model = $model;
	}

	/**
	 * Check if the fake bot detection is enabled.
	 */
	public function is_enabled(): bool {
		return $this->model->enabled && $this->model->fake_bots_enabled;
	}

	/**
	 * Determine if the current HTTP request is from a legitimate crawler.
	 *
	 * The detection flow is:
	 *  1. Check User-Agent against known crawler UAs.
	 *  2. If UA matches, check the request IP against the crawler's allowlist.
	 *  3. If not in allowlist, perform reverse DNS lookup (IP → hostname).
	 *  4. Verify that the hostname matches known crawler domain patterns.
	 *  5. Perform forward DNS lookup (hostname → IP) and ensure the original IP matches.
	 *
	 * Crawler definitions (UA, IP ranges, hostname patterns) are loaded from
	 * a remote JSON file (e.g. GitHub Gist). If that fails, a local fallback
	 * JSON file bundled with the plugin is used.
	 *
	 * @return void
	 */
	public function validate_legit_crawler(): void {
		$ips   = $this->get_user_ip();
		$agent = defender_get_data_from_request( 'HTTP_USER_AGENT', 's' );

		if ( array() === $ips || '' === $agent ) {
			return;
		}

		foreach ( $this->crawlers as $name => $data ) {
			// 1. User-Agent check.
			if ( ! $this->match_user_agent( $agent, $data['user_agents'] ?? array() ) ) {
				continue;
			}

			// Check each IP against the allowed list.
			foreach ( $ips as $ip ) {
				// 2. IP allowlist check.
				$allowed_ips = array_merge(
					$data['ips']['ipv4'] ?? array(),
					$data['ips']['ipv6'] ?? array()
				);

				if ( array() !== $allowed_ips && $this->is_ip_in_format( $ip, $allowed_ips ) ) {
					return;
				}

				// 3. Reverse DNS lookup.
				$hostname = gethostbyaddr( $ip );
				if ( ! $hostname || $hostname === $ip ) {
					$this->block_ip( $ip, $agent, $name );
					return;
				}

				$pattern = $data['hostname_pattern'] ?? '';
				if ( '' === $pattern || ! preg_match( '#' . $pattern . '#i', $hostname ) ) {
					$this->block_ip( $ip, $agent, $name );
					return; // Hostname doesn't match.
				}

				// 4. Forward DNS lookup
				$resolved_ips = gethostbynamel( $hostname );
				if ( is_array( $resolved_ips ) && in_array( $ip, $resolved_ips, true ) ) {
					return; // Verified by forward + reverse DNS.
				}
			}

			$this->block_ip( $ip, $agent, $name );
			return; // UA matched, but DNS validation failed.
		}
	}

	/**
	 * Load crawler definitions from remote URL with local fallback.
	 *
	 * @return void
	 */
	public function load_crawlers(): void {
		// 1. Try cache first.
		$cached = get_site_transient( self::CACHE_KEY );
		if ( is_array( $cached ) ) {
			$this->crawlers = $cached;
			return;
		}

		// 2. Attempt remote fetch.
		$response = wp_remote_get( self::REMOTE_URL, array( 'timeout' => 5 ) );

		if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
			$json = wp_remote_retrieve_body( $response );
			$data = json_decode( $json, true );

			if ( is_array( $data ) ) {
				$this->crawlers = $data;

				// Cache remote result for 12h.
				set_site_transient( self::CACHE_KEY, $this->crawlers, self::CACHE_TTL );
				return;
			}
		}

		// 3. Remote failed → fallback to local file (not cached).
		$local_path = defender_path( self::LOCAL_FILE );
		if ( file_exists( $local_path ) ) {
			$json           = file_get_contents( $local_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$data           = json_decode( $json, true );
			$this->crawlers = is_array( $data ) ? $data : array();
		} else {
			$this->crawlers = array();
		}
	}


	/**
	 * Test User-Agent string against known crawler substrings.
	 *
	 * @param string $agent    Full User-Agent string from request.
	 * @param array  $patterns List of substrings to match against.
	 *
	 * @return bool True if UA contains one of the patterns, false otherwise.
	 */
	protected function match_user_agent( string $agent, array $patterns ): bool {
		foreach ( $patterns as $ua ) {
			if ( stripos( $agent, $ua ) !== false ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Block the given IP address according to settings.
	 *
	 * @param string $ip         The IP address to block.
	 * @param string $user_agent The User-Agent string from the request.
	 * @param string $bot_name   The User-Agent label impersonated (e.g. "Googlebot").
	 */
	protected function block_ip( string $ip, string $user_agent, string $bot_name ): void {
		$lockout_model  = Lockout_Ip::get( $ip );
		$remaining_time = 0;
		if ( 'permanent' === $this->model->fake_bots_lockout_type ) {
			$lockout_model->attempt       = 0;
			$lockout_model->meta['login'] = array();
			$lockout_model->meta['nf']    = array();
			$lockout_model->save();
			// We block IP here unlike other UA lockout cases.
			do_action( 'wd_blacklist_this_ip', $ip );
		} else {
			$lockout_model->status    = Lockout_Ip::STATUS_BLOCKED;
			$lockout_model->lock_time = time();

			$this->create_blocked_lockout(
				$lockout_model,
				$this->model->fake_bots_message,
				strtotime( '+' . $this->model->fake_bots_lockout_duration . ' ' . $this->model->fake_bots_lockout_duration_unit )
			);

			$remaining_time = $lockout_model->remaining_release_time();
		}

		$this->log_event( $ip, self::SCENARIO_FAKE_BOT, $bot_name );

		wd_di()->get( Firewall::class )->actions_for_blocked(
			$this->model->fake_bots_message,
			$remaining_time,
			self::SCENARIO_FAKE_BOT,
			$this->get_user_ip()
		);
	}

	/**
	 * Log the event into db, we will use the data in logs page later.
	 *
	 * @param  string $ip        The IP address involved in the event.
	 * @param  string $scenario  The scenario under which the event is logged.
	 * @param  string $bot_name  The name of the bot being impersonated.
	 */
	public function log_event( $ip, $scenario, $bot_name ) {
		$model             = new Lockout_Log();
		$model->ip         = $ip;
		$user_agent        = defender_get_data_from_request( 'HTTP_USER_AGENT', 's' );
		$model->user_agent = isset( $user_agent ) ? User_Agent::fast_cleaning( $user_agent ) : null;
		$model->date       = time();
		$model->tried      = $user_agent;
		$model->blog_id    = get_current_blog_id();

		$ip_to_country = $this->ip_to_country( $ip );

		if ( ! empty( $ip_to_country ) && isset( $ip_to_country['iso'] ) ) {
			$model->country_iso_code = $ip_to_country['iso'];
		}

		switch ( $scenario ) {
			case self::SCENARIO_FAKE_BOT:
			default:
				$model->type = Lockout_Log::LOCKOUT_FAKE_BOT;
				$model->log  = sprintf(
				/* translators: %s: The name of the bot being impersonated. */
					esc_html__( 'Lockout occurred: Fake bot impersonated %s.', 'defender-security' ),
					$bot_name
				);
				break;
		}
		$model->save();
	}

	/**
	 * Creates a lockout for a blocked IP.
	 *
	 * @param  Lockout_Ip $model    The lockout IP model.
	 * @param  string     $message  The lockout message.
	 * @param  int        $time     The timestamp when the lockout will be lifted.
	 */
	protected function create_blocked_lockout( &$model, $message, $time ) {
		$model->lockout_message = $message;
		$model->release_time    = $time;
		$model->save();
	}
}
