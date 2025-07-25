<?php
/**
 * PA Helper Functions.
 */

namespace PremiumAddons\Includes;

// Premium Addons Pro Classes.
use PremiumAddonsPro\Includes\White_Label\Helper;

// Elementor Classes.
use Elementor\Icons_Manager;
use Elementor\Core\Settings\Manager as SettingsManager;
use Elementor\Plugin;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Helper_Functions.
 */
class Helper_Functions {

	/**
	 * A list of safe tage for `validate_html_tag` method.
	 */
	const ALLOWED_HTML_WRAPPER_TAGS = array(
		'article',
		'aside',
		'div',
		'footer',
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'h6',
		'header',
		'main',
		'nav',
		'p',
		'section',
		'span',
	);

	/**
	 * Theme
	 *
	 * @var theme
	 */
	private static $current_theme = null;

	/**
	 * Google maps prefixes
	 *
	 * @var google_localize
	 */
	private static $google_localize = null;

	/**
	 * SVG Shapes
	 *
	 * @var shapes
	 */
	private static $shapes = null;

	/**
	 * WP lang prefixes
	 *
	 * @var lang_locales
	 */
	private static $lang_locales = null;

	/**
	 * Script debug enabled
	 *
	 * @var script_debug
	 */
	private static $script_debug = null;

	/**
	 * JS scripts directory
	 *
	 * @var js_dir
	 */
	private static $js_dir = null;

	/**
	 * CSS fiels directory
	 *
	 * @var js_dir
	 */
	private static $css_dir = null;

	/**
	 * JS Suffix
	 *
	 * @var js_suffix
	 */
	private static $assets_suffix = null;

	/**
	 * Get Icon SVG Data
	 *
	 * @since 4.10.72
	 * @access private
	 *
	 * @return array icon data.
	 */
	private static function get_icon_svg_data( $icon ) {

		preg_match( '/fa(.*) fa-/', $icon['value'], $icon_name_matches );

		if ( empty( $icon_name_matches ) ) {
			return;
		}

		$icon_name = str_replace( $icon_name_matches[0], '', $icon['value'] );

		$icon_key = str_replace( ' fa-', '-', $icon['value'] );

		$icon_file_name = str_replace( 'fa-', '', $icon['library'] );

		$path = ELEMENTOR_ASSETS_PATH . 'lib/font-awesome/json/' . $icon_file_name . '.json';

		$data = file_get_contents( $path );

		if ( ! $data ) {
			return;
		}

		$data = json_decode( $data, true );

		$svg_data = $data['icons'][ $icon_name ];

		return array(
			'width'  => $svg_data[0],
			'height' => $svg_data[1],
			'path'   => $svg_data[4],
		);
	}

	/**
	 * Check if white labeling - Free version author field is set
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function author() {

		$author_free = 'Leap13';

		if ( self::check_papro_version() ) {

			$white_label = Helper::get_white_labeling_settings();

			$author_free = $white_label['premium-wht-lbl-name'];

		}

		return '' !== $author_free ? $author_free : 'Leap13';
	}

	/**
	 * Check if white labeling - Free version name field is set
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function name() {

		$name_free = 'Premium Addons';

		if ( self::check_papro_version() ) {

			$white_label = Helper::get_white_labeling_settings();

			$name_free = $white_label['premium-wht-lbl-plugin-name'];

		}

		return '' !== $name_free ? $name_free : 'Premium Addons';
	}

	/**
	 * Check if white labeling - Hide row meta option is checked
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	public static function is_hide_row_meta() {

		$hide_meta = false;

		if ( self::check_papro_version() ) {

			$white_label = Helper::get_white_labeling_settings();

			$hide_meta = $white_label['premium-wht-lbl-row'];

		}

		return $hide_meta;
	}

	/**
	 * Check if white labeling - Hide plugin logo option is checked
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public static function is_hide_logo() {

		if ( self::check_papro_version() ) {

			if ( isset( get_option( 'pa_wht_lbl_save_settings' )['premium-wht-lbl-logo'] ) ) {

				$hide_logo = get_option( 'pa_wht_lbl_save_settings' )['premium-wht-lbl-logo'];

			}
		}

		return isset( $hide_logo ) ? $hide_logo : false;
	}

	/**
	 * Get White Labeling - Widgets Category string
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function get_category() {

		$category = __( 'Premium Addons', 'premium-addons-for-elementor' );

		if ( self::check_papro_version() ) {

			$white_label = Helper::get_white_labeling_settings();

			$category = $white_label['premium-wht-lbl-short-name'];

		}

		return '' !== $category ? $category : __( 'Premium Addons', 'premium-addons-for-elementor' );
	}

	/**
	 * Get White Labeling - Widgets Prefix string
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function get_prefix() {

		$prefix = __( 'Premium', 'premium-addons-for-elementor' );

		if ( self::check_papro_version() ) {

			$white_label = Helper::get_white_labeling_settings();

			$prefix = $white_label['premium-wht-lbl-prefix'];

		}

		return '' !== $prefix ? $prefix : __( 'Premium', 'premium-addons-for-elementor' );
	}

	/**
	 * Check if white labeling - Future notification checked
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	public static function check_hide_notifications() {

		if ( self::check_papro_version() ) {

			$white_label = Helper::get_white_labeling_settings();

			$hide_notification = isset( $white_label['premium-wht-lbl-not'] ) ? $white_label['premium-wht-lbl-not'] : false;

		}

		return isset( $hide_notification ) ? $hide_notification : false;
	}

	/**
	 * Get White Labeling - Widgets Badge string
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function get_badge() {

		$badge = 'PA';

		if ( self::check_papro_version() ) {

			$white_label = Helper::get_white_labeling_settings();

			$badge = $white_label['premium-wht-lbl-badge'];

		}

		return '' !== $badge ? $badge : 'PA';
	}

	/**
	 * Get Google Maps localization prefixes
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public static function get_google_maps_prefixes() {

		if ( null === self::$google_localize ) {

			self::$google_localize = array(
				'ar'    => __( 'Arabic', 'premium-addons-for-elementor' ),
				'eu'    => __( 'Basque', 'premium-addons-for-elementor' ),
				'bg'    => __( 'Bulgarian', 'premium-addons-for-elementor' ),
				'bn'    => __( 'Bengali', 'premium-addons-for-elementor' ),
				'ca'    => __( 'Catalan', 'premium-addons-for-elementor' ),
				'cs'    => __( 'Czech', 'premium-addons-for-elementor' ),
				'da'    => __( 'Danish', 'premium-addons-for-elementor' ),
				'de'    => __( 'German', 'premium-addons-for-elementor' ),
				'el'    => __( 'Greek', 'premium-addons-for-elementor' ),
				'en'    => __( 'English', 'premium-addons-for-elementor' ),
				'en-AU' => __( 'English (australian)', 'premium-addons-for-elementor' ),
				'en-GB' => __( 'English (great britain)', 'premium-addons-for-elementor' ),
				'es'    => __( 'Spanish', 'premium-addons-for-elementor' ),
				'fa'    => __( 'Farsi', 'premium-addons-for-elementor' ),
				'fi'    => __( 'Finnish', 'premium-addons-for-elementor' ),
				'fil'   => __( 'Filipino', 'premium-addons-for-elementor' ),
				'fr'    => __( 'French', 'premium-addons-for-elementor' ),
				'gl'    => __( 'Galician', 'premium-addons-for-elementor' ),
				'gu'    => __( 'Gujarati', 'premium-addons-for-elementor' ),
				'hi'    => __( 'Hindi', 'premium-addons-for-elementor' ),
				'hr'    => __( 'Croatian', 'premium-addons-for-elementor' ),
				'hu'    => __( 'Hungarian', 'premium-addons-for-elementor' ),
				'id'    => __( 'Indonesian', 'premium-addons-for-elementor' ),
				'it'    => __( 'Italian', 'premium-addons-for-elementor' ),
				'iw'    => __( 'Hebrew', 'premium-addons-for-elementor' ),
				'ja'    => __( 'Japanese', 'premium-addons-for-elementor' ),
				'kn'    => __( 'Kannada', 'premium-addons-for-elementor' ),
				'ko'    => __( 'Korean', 'premium-addons-for-elementor' ),
				'lt'    => __( 'Lithuanian', 'premium-addons-for-elementor' ),
				'lv'    => __( 'Latvian', 'premium-addons-for-elementor' ),
				'ml'    => __( 'Malayalam', 'premium-addons-for-elementor' ),
				'mr'    => __( 'Marathi', 'premium-addons-for-elementor' ),
				'nl'    => __( 'Dutch', 'premium-addons-for-elementor' ),
				'no'    => __( 'Norwegian', 'premium-addons-for-elementor' ),
				'pl'    => __( 'Polish', 'premium-addons-for-elementor' ),
				'pt'    => __( 'Portuguese', 'premium-addons-for-elementor' ),
				'pt-BR' => __( 'Portuguese (brazil)', 'premium-addons-for-elementor' ),
				'pt-PT' => __( 'Portuguese (portugal)', 'premium-addons-for-elementor' ),
				'ro'    => __( 'Romanian', 'premium-addons-for-elementor' ),
				'ru'    => __( 'Russian', 'premium-addons-for-elementor' ),
				'sk'    => __( 'Slovak', 'premium-addons-for-elementor' ),
				'sl'    => __( 'Slovenian', 'premium-addons-for-elementor' ),
				'sr'    => __( 'Serbian', 'premium-addons-for-elementor' ),
				'sv'    => __( 'Swedish', 'premium-addons-for-elementor' ),
				'tl'    => __( 'Tagalog', 'premium-addons-for-elementor' ),
				'ta'    => __( 'Tamil', 'premium-addons-for-elementor' ),
				'te'    => __( 'Telugu', 'premium-addons-for-elementor' ),
				'th'    => __( 'Thai', 'premium-addons-for-elementor' ),
				'tr'    => __( 'Turkish', 'premium-addons-for-elementor' ),
				'uk'    => __( 'Ukrainian', 'premium-addons-for-elementor' ),
				'vi'    => __( 'Vietnamese', 'premium-addons-for-elementor' ),
				'zh-CN' => __( 'Chinese (simplified)', 'premium-addons-for-elementor' ),
				'zh-TW' => __( 'Chinese (traditional)', 'premium-addons-for-elementor' ),
			);
		}

		return self::$google_localize;
	}

	/**
	 * Checks if a plugin is installed
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $plugin_path plugin path.
	 *
	 * @return boolean
	 */
	public static function is_plugin_installed( $plugin_path ) {

		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		$plugins = get_plugins();

		return isset( $plugins[ $plugin_path ] );
	}

	/**
	 * Check Plugin Active
	 *
	 * @since 4.2.5
	 * @access public
	 *
	 * @param string $slug plugin slug.
	 *
	 * @return boolean $is_active plugin active.
	 */
	public static function check_plugin_active( $slug = '' ) {

		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		$is_active = in_array( $slug, (array) get_option( 'active_plugins', array() ), true );

		return $is_active;
	}

	/**
	 * Check if script debug mode enabled.
	 *
	 * @since 3.11.1
	 * @access public
	 *
	 * @return boolean is debug mode enabled
	 */
	public static function is_debug_enabled() {

		if ( null === self::$script_debug ) {

			self::$script_debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
		}

		return self::$script_debug;
	}

	/**
	 * Get scripts dir.
	 *
	 * @access public
	 *
	 * @return string JS scripts directory.
	 */
	public static function get_scripts_dir() {

		if ( null === self::$js_dir ) {

			self::$js_dir = self::is_debug_enabled() ? 'js' : 'min-js';
		}

		return self::$js_dir;
	}

	/**
	 * Get styles dir.
	 *
	 * @access public
	 *
	 * @return string CSS files directory.
	 */
	public static function get_styles_dir() {

		if ( null === self::$css_dir ) {

			self::$css_dir = self::is_debug_enabled() ? 'css' : 'min-css';
		}

		return self::$css_dir;
	}

	/**
	 * Get assets suffix.
	 *
	 * @access public
	 *
	 * @return string JS scripts suffix.
	 */
	public static function get_assets_suffix() {

		if ( null === self::$assets_suffix ) {

			self::$assets_suffix = self::is_debug_enabled() ? '' : '.min';
		}

		return self::$assets_suffix;
	}

	/**
	 * Get Installed Theme
	 *
	 * Returns the active theme slug
	 *
	 * @access public
	 *
	 * @return string theme slug
	 */
	public static function get_installed_theme() {

		if ( null === self::$current_theme ) {

			$theme = wp_get_theme();

			if ( $theme->parent() ) {

				$theme_name = sanitize_key( $theme->parent()->get( 'Name' ) );

			} else {

				$theme_name = $theme->get( 'Name' );

				$theme_name = sanitize_key( $theme_name );

			}

			self::$current_theme = $theme_name;

		}

		return self::$current_theme;
	}

	/**
	 * Get Vimeo Video Data
	 *
	 * Get video data using Vimeo API
	 *
	 * @since 3.11.4
	 * @access public
	 *
	 * @param string $video_id video ID.
	 */
	public static function get_vimeo_video_data( $video_id ) {

		$vimeo_data = wp_remote_get( 'http://www.vimeo.com/api/v2/video/' . intval( $video_id ) . '.php' );

		if ( is_wp_error( $vimeo_data ) ) {
			return false;
		}

		if ( isset( $vimeo_data['response']['code'] ) ) {

			if ( 200 === $vimeo_data['response']['code'] ) {

				$response  = maybe_unserialize( $vimeo_data['body'] );
				$thumbnail = isset( $response[0]['thumbnail_large'] ) ? $response[0]['thumbnail_large'] : false;

				$data = array(
					'src'      => $thumbnail,
					'url'      => $response[0]['user_url'],
					'portrait' => $response[0]['user_portrait_huge'],
					'title'    => $response[0]['title'],
					'user'     => $response[0]['user_name'],
				);

				return $data;

			}
		}

		return false;
	}

	/**
	 * Get Video Thumbnail
	 *
	 * Get thumbnail URL for embed or self hosted
	 *
	 * @since 3.7.0
	 * @access public
	 *
	 * @param string $video_id video ID.
	 * @param string $type embed type.
	 * @param string $size youtube thumbnail size.
	 */
	public static function get_video_thumbnail( $video_id, $type, $size = '' ) {

		$thumbnail_src = 'transparent';

		if ( 'youtube' === $type ) {
			if ( '' === $size ) {
				$size = 'maxresdefault';
			}
			$thumbnail_src = sprintf( 'https://i.ytimg.com/vi/%s/%s.jpg', $video_id, $size );

		} elseif ( 'vimeo' === $type ) {

			$vimeo = self::get_vimeo_video_data( $video_id );

			$thumbnail_src = is_array( $vimeo ) ? $vimeo['src'] : '';

		} elseif ( 'dailymotion' === $type ) {
			$video_data = rplg_urlopen( 'https://api.dailymotion.com/video/' . $video_id . '?fields=thumbnail_url' );

			if ( isset( $video_data['code'] ) ) {
				if ( 404 === $video_data['code'] ) {
					return $thumbnail_src;
				}
			}

			$thumbnail_src = rplg_json_decode( $video_data['data'] )->thumbnail_url;
		}

		return $thumbnail_src;
	}

	/**
	 * Transient Expire
	 *
	 * Gets expire time of transient.
	 *
	 * @since 3.20.8
	 * @access public
	 *
	 * @param string $period transient expiration period.
	 *
	 * @return string $expire_time expire time in seconds.
	 */
	public static function transient_expire( $period ) {

		$expire_time = 24 * HOUR_IN_SECONDS;

		switch ( $period ) {
			case 'minute':
				$expire_time = MINUTE_IN_SECONDS;
				break;
			case 'minutes':
				$expire_time = 5 * MINUTE_IN_SECONDS;
				break;
			case 'hour':
				$expire_time = 60 * MINUTE_IN_SECONDS;
				break;
			case 'week':
				$expire_time = 7 * DAY_IN_SECONDS;
				break;
			case 'month':
				$expire_time = 30 * DAY_IN_SECONDS;
				break;
			case 'year':
				$expire_time = 365 * DAY_IN_SECONDS;
				break;
			default:
				$expire_time = 24 * HOUR_IN_SECONDS;
		}

		return $expire_time;
	}

	/**
	 * Get Campaign Link
	 *
	 * @since 3.20.9
	 * @access public
	 *
	 * @param string $link page link.
	 * @param string $source source.
	 * @param string $medium  media.
	 * @param string $campaign campaign name.
	 *
	 * @return string $link campaign URL
	 */
	public static function get_campaign_link( $link, $source, $medium, $campaign = '' ) {

		if ( null === self::$current_theme ) {
			self::get_installed_theme();
		}

		$url = add_query_arg(
			array(
				'utm_source'   => $source,
				'utm_medium'   => $medium,
				'utm_campaign' => $campaign,
				'utm_term'     => self::$current_theme,
			),
			$link
		);

		return $url;
	}

	/**
	 * Get Elementor UI Theme
	 *
	 * Detects user setting for UI theme
	 *
	 * @since 3.21.1
	 * @access public
	 *
	 * @return string $theme UI Theme
	 */
	public static function get_elementor_ui_theme() {

		$theme = SettingsManager::get_settings_managers( 'editorPreferences' )->get_model()->get_settings( 'ui_theme' );

		return $theme;
	}

	/**
	 * Check PAPRO Version
	 *
	 * Check if PAPRO version is updated
	 *
	 * @since 3.21.6
	 * @access public
	 *
	 * @return boolen
	 */
	public static function check_papro_version() {
		return defined( 'PREMIUM_PRO_ADDONS_VERSION' );
	}

	/**
	 * Valide HTML Tag
	 *
	 * Validates an HTML tag against a safe allowed list.
	 *
	 * @param string $tag HTML tag.
	 *
	 * @return string
	 */
	public static function validate_html_tag( $tag ) {
		return in_array( strtolower( $tag ), self::ALLOWED_HTML_WRAPPER_TAGS, true ) ? $tag : 'div';
	}

	/**
	 * Get Image Data
	 *
	 * Returns image data based on image id.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @param int    $image_id Image ID.
	 * @param string $image_url Image URL.
	 * @param array  $image_size Image sizes array.
	 *
	 * @return array $data image data.
	 */
	public static function get_image_data( $image_id, $image_url, $image_size ) {

		if ( ! $image_id && ! $image_url ) {
			return false;
		}

		$data = array();

		$image_url = esc_url_raw( $image_url );

		if ( ! empty( $image_id ) ) { // Existing attachment.

			$attachment = get_post( $image_id );

			if ( is_object( $attachment ) ) {
				$data['id']  = $image_id;
				$data['url'] = $image_url;

				$data['image']       = wp_get_attachment_image( $attachment->ID, $image_size, true );
				$data['image_size']  = $image_size;
				$data['caption']     = $attachment->post_excerpt;
				$data['title']       = $attachment->post_title;
				$data['description'] = $attachment->post_content;

			}
		} else { // Placeholder image, most likely.

			if ( empty( $image_url ) ) {
				return;
			}

			$data['id']          = false;
			$data['url']         = $image_url;
			$data['image']       = '<img src="' . $image_url . '" alt="" title="" />';
			$data['image_size']  = $image_size;
			$data['caption']     = '';
			$data['title']       = '';
			$data['description'] = '';
		}

		return $data;
	}

	/**
	 * Returns the current page id.
	 * get_the_ID returns the first product ID in the loop if it's the shop page.
	 * and it sometimes returns a wrong ID in the other cases.
	 *
	 * @access public
	 * @since 4.11.8
	 * @return int
	 */
	public static function pa_get_current_page_id() {

		if ( class_exists( 'woocommerce' ) ) {

			switch ( true ) {
				case is_shop():
					$page_id = wc_get_page_id( 'shop' );
					break;

				case is_cart():
					$page_id = wc_get_page_id( 'cart' );
					break;

				case is_checkout():
					$page_id = wc_get_page_id( 'checkout' );
					break;

				case is_account_page():
					$page_id = wc_get_page_id( 'myaccount' );
					break;

				default:
					$page_id = get_the_ID();
					break;
			}
		} else {
			$page_id = get_the_ID();
		}

		return $page_id;
	}

	/**
	 * Get Final Result.
	 *
	 * @access public
	 * @since 4.4.8
	 *
	 * @param bool   $condition_result  result.
	 * @param string $operator          operator.
	 *
	 * @return bool
	 */
	public static function get_final_result( $condition_result, $operator ) {

		if ( 'is' === $operator ) {
			return true === $condition_result;
		} else {
			return true !== $condition_result;
		}
	}

	/**
	 * Get Local Time ( WordPress TimeZone Setting ).
	 *
	 * @access public
	 * @since 4.4.8
	 *
	 * @param string $format  format.
	 */
	public static function get_local_time( $format ) {

		$local_time_zone = isset( $_COOKIE['localTimeZone'] ) && ! empty( $_COOKIE['localTimeZone'] ) ?
			str_replace( 'GMT ', 'GMT+', sanitize_text_field( wp_unslash( $_COOKIE['localTimeZone'] ) ) )
				: self::get_location_time_zone();

		// $today = new \DateTime( 'now', new \DateTimeZone( $local_time_zone ) );

		try {
			$today = new \DateTime( 'now', new \DateTimeZone( $local_time_zone ) );
		} catch ( \Exception $e ) {
			$today = new \DateTime( 'now', new \DateTimeZone( 'UTC' ) );
		}

		return $today->format( $format );
	}

	/**
	 * Gets the user's timezone based on his ip address.
	 *
	 * @access public
	 * @since 4.10.26
	 *
	 * @return string
	 */
	public static function get_location_time_zone() {

		$ip_address = self::get_user_ip_address();

		return self::get_timezone_by_ip( $ip_address );
	}

	/**
	 * Get user's IP address.
	 *
	 * @access public
	 * @since 4.10.26
	 *
	 * @return string
	 */
	public static function get_user_ip_address() {

		if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {

			$x_forward = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );

			if ( is_array( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {

				$http_x_headers         = explode( ',', filter_var_array( $x_forward ) );
				$_SERVER['REMOTE_ADDR'] = $http_x_headers[0];
			} else {
				$_SERVER['REMOTE_ADDR'] = $x_forward;
			}
		}

		return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	}

	/**
	 * Get timezone by ip address.
	 *
	 * @access public
	 * @since 4.10.26
	 *
	 * @param string $ip_address user's ip address.
	 *
	 * @return string
	 */
	public static function get_timezone_by_ip( $ip_address ) {

		if ( '127.0.0.1' === $ip_address || empty( $ip_address ) ) {
			return date_default_timezone_get();
		}

		$location_data = wp_remote_get(
			'https://api.findip.net/' . $ip_address . '/?token=e21d68c353324af0af206c907e77ff97',
			array(
				'timeout'   => 15,
				'sslverify' => false,
			)
		);

		if ( is_wp_error( $location_data ) || empty( $location_data ) ) {
			return date_default_timezone_get(); // localhost.
		}

		$location_data = json_decode( wp_remote_retrieve_body( $location_data ), true );

		$time_zone = strtolower( $location_data['location']['time_zone'] );

		return $time_zone;
	}

	/**
	 * Get Site Server Time ( WordPress TimeZone Setting ).
	 *
	 * @access public
	 * @since 4.4.8
	 *
	 * @param string $format  format.
	 */
	public static function get_site_server_time( $format ) {

		$today = gmdate( $format, strtotime( 'now' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );

		return $today;
	}

	/**
	 * Get All Breakpoints.
	 *
	 * @param string $type result return type.
	 *
	 * @access public
	 * @since 4.6.1
	 *
	 * @return array $devices enabled breakpoints.
	 */
	public static function get_all_breakpoints( $type = 'assoc' ) {

		$devices = array(
			'desktop' => __( 'Desktop', 'elementor' ),
			'tablet'  => __( 'Tablet', 'elementor' ),
			'mobile'  => __( 'Mobile', 'elementor' ),
		);

		$method_available = method_exists( Plugin::$instance->breakpoints, 'has_custom_breakpoints' );

		if ( ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.4.0', '>' ) ) && $method_available ) {

			if ( Plugin::$instance->breakpoints->has_custom_breakpoints() ) {
				$devices = array_merge(
					$devices,
					array(
						'widescreen'   => __( 'Widescreen', 'elementor' ),
						'laptop'       => __( 'Laptop', 'elementor' ),
						'tablet_extra' => __( 'Tablet Extra', 'elementor' ),
						'mobile_extra' => __( 'Mobile Extra', 'elementor' ),
					)
				);
			}
		}

		if ( 'keys' === $type ) {
			$devices = array_keys( $devices );
		}

		return $devices;
	}

	/**
	 * Get WordPress language prefixes.
	 *
	 * @since 4.4.8
	 * @access public
	 *
	 * @return array
	 */
	public static function get_lang_prefixes() {

		if ( null === self::$lang_locales ) {

			$langs = require_once PREMIUM_ADDONS_PATH . 'includes/pa-display-conditions/lang-locale.php';

			foreach ( $langs as $lang => $props ) {
				/* translators: %s: Language Name */
				$val                         = ucwords( $props['name'] );
				self::$lang_locales[ $lang ] = $val;
			}
		}

		return self::$lang_locales;
	}

	/**
	 * Get Woocommerce Categories.
	 *
	 * @access public
	 * @since 4.4.8
	 *
	 * @param string $id array key.
	 *
	 * @return array
	 */
	public static function get_woo_categories( $id = 'slug' ) {

		$product_cat = array();

		$cat_args = array(
			'taxonomy'   => 'product_cat',
			'orderby'    => 'name',
			'order'      => 'asc',
			'hide_empty' => false,
		);

		$product_categories = get_terms( $cat_args );

		if ( ! empty( $product_categories ) ) {

			foreach ( $product_categories as $key => $category ) {

				$cat_id                 = 'slug' === $id ? $category->slug : $category->term_id;
				$product_cat[ $cat_id ] = $category->name;

			}
		}

		return $product_cat;
	}

	/**
	 * Check Elementor Experiment
	 *
	 * Check if an Elementor experiment is enabled.
	 *
	 * @since 4.8.6
	 * @access public
	 *
	 * @param string $experiment feature ID.
	 *
	 * @return boolean $is_enabled is feature enabled.
	 */
	public static function check_elementor_experiment( $experiment ) {

		$experiments_manager = Plugin::$instance->experiments;

		$is_enabled = $experiments_manager->is_feature_active( $experiment );

		return $is_enabled;
	}

	/**
	 * Is Edit Mode.
	 *
	 * @access public
	 * @since 4.6.1
	 *
	 * @return boolean
	 */
	public static function is_edit_mode() {
		return isset( $_REQUEST['elementor-preview'] ) && ! empty( $_REQUEST['elementor-preview'] ); // phpcs:ignore WordPress.Security.NonceVerification
	}

	/**
	 * Generate Unique ID
	 *
	 * Generates a unique ID for the current page.
	 *
	 * @since 4.6.9
	 * @access public
	 *
	 * @param string $id page ID.
	 *
	 * @return string unique ID.
	 */
	public static function generate_unique_id( $id ) {
		return substr( md5( $id ), 0, 9 );
	}

	/**
	 * Get Safe Path
	 *
	 * @since 4.6.9
	 * @access public
	 *
	 * @param string $file_path unsafe file path.
	 *
	 * @return string safe file path.
	 */
	public static function get_safe_path( $file_path ) {

		$path = str_replace( array( '//', '\\\\' ), array( '/', '\\' ), $file_path );

		return str_replace( array( '/', '\\' ), DIRECTORY_SEPARATOR, $path );
	}

	/**
	 * Check if the current post type should include addons.
	 *
	 * @param string $id current post ID.
	 *
	 * @since  4.9.18
	 * @access public
	 */
	public static function check_post_type( $id ) {

		if ( ! $id ) {
			return false;
		}

		$template_name = get_post_meta( $id, '_elementor_template_type', true );

		$template_list = array(
			'header',
			'footer',
			'single',
			'post',
			'page',
			'archive',
			'search-results',
			'error-404',
			'product',
			'product-archive',
			'section',
		);

		return in_array( $template_name, $template_list );
	}

	/**
	 * Get Draw SVG Notice
	 *
	 * @since 4.9.26
	 * @access public
	 *
	 * @param object $elem element object.
	 * @param string $search search query.
	 * @param array  $conditions control conditions
	 */
	public static function get_draw_svg_notice( $elem, $search, $conditions, $index = 0, $nested = 'condition' ) {

		$url = add_query_arg(
			array(
				'page'   => 'premium-addons',
				'search' => $search,
				'#tab'   => 'elements',
			),
			esc_url( admin_url( 'admin.php' ) )
		);

		$control_attr = array(
			'type'            => Controls_Manager::RAW_HTML,
			'raw'             => __( 'You need first to enable SVG Draw option checkbox from ', 'premium-addons-for-elementor' ) . '<a href="' . esc_url( $url ) . '" target="_blank">' . __( 'here.', 'premium-addons-for-elementor' ) . '</a>',
			'classes'         => 'editor-pa-control-notice',
			'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
		);

		$control_attr[ $nested ] = $conditions;

		$elem->add_control(
			'draw_svg_notice_' . $index,
			$control_attr
		);
	}

	/**
	 * Checks if Elementor PRO 3.8 or higher is activated && if the Loop expirement is activated.
	 *
	 * @since 4.9.45
	 * @access public
	 *
	 * @return bool
	 */
	public static function is_loop_exp_enabled() {

		if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {

			if ( version_compare( ELEMENTOR_PRO_VERSION, '3.16.0', '>=' ) ) {
				return true;
			} elseif ( version_compare( ELEMENTOR_PRO_VERSION, '3.8', '>=' ) ) {
				$is_loop_enabled = self::check_elementor_experiment( 'loop' );

				if ( $is_loop_enabled ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Get Element Classes.
	 *
	 * @access private
	 * @since 2.8.22
	 *
	 * @param array $devices  devices to hide on.
	 *
	 * @return array
	 */
	public static function get_element_classes( $devices, $default = array() ) {

		$classes = $default;

		if ( count( $devices ) ) {
			foreach ( $devices as $index => $device ) {
				array_push( $classes, 'elementor-hidden-' . $device );
			}

			array_push( $classes, 'premium-addons-element' );
		}

		return $classes;
	}

	/**
	 * Round Numbers In A Reading-friendly Format.
	 *
	 * @param integer $num followers number.
	 */
	public static function premium_format_numbers( $num ) {
		$num    = intval( $num );
		$result = '';

		if ( $num >= 1000000000 ) {
			$tmp    = round( ( $num / 1000000 ), 1 );
			$result = $tmp . 'B';
			return $result;
		}

		if ( $num >= 1000000 ) {
			$tmp    = round( ( $num / 1000000 ), 1 );
			$result = $tmp . 'M';
			return $result;
		}

		if ( $num >= 1000 ) {
			$tmp    = round( ( $num / 1000 ), 1 );
			$result = $tmp . 'K';

			return $result;
		}

		return round( $num, 1 );
	}

	/**
	 * Get Contact Form Body
	 *
	 * @since 4.10.2
	 * @access public
	 *
	 * @param string $preset form preset.
	 *
	 * @return void
	 */
	public static function get_cf_form_body( $preset ) {

		$forms_array = array(

			'preset1' => '<div class="premium-cf-full"><label class="premium-cf-label">Email</label>
            [email* email-1 class:premium-cf-field placeholder "john@smith.com"]</div>
            [submit "Subscribe"]',

			'preset2' => '<div class="premium-cf-full"><label class="premium-cf-label">Name</label>
            [text* text-1 class:premium-cf-field placeholder "John Smith"]</div>

            <div class="premium-cf-full"><label class="premium-cf-label">Email</label>
            [email* email-1 class:premium-cf-field placeholder "john@smith.com"]</div>

            [submit "Send"]',

			'preset3' => '<div class="premium-cf-full"><label class="premium-cf-label">Name</label>
            [text* text-1 class:premium-cf-field placeholder "John Smith"]</div>

            <div class="premium-cf-full"><label class="premium-cf-label">Email</label>
            [email* email-1 class:premium-cf-field placeholder "john@smith.com"]</div>

            <div class="premium-cf-full"><label class="premium-cf-label">Message</label>
            [textarea* textarea-1 class:premium-cf-field placeholder "Enter your message here..."]</div>

            [submit "Send"]',

			'preset4' => '<div class="premium-cf-half"><label class="premium-cf-label">Name</label>
            [text* text-1 class:premium-cf-field placeholder "John Smith"]</div>

            <div class="premium-cf-half"><label class="premium-cf-label">Email</label>
            [email* email-1 class:premium-cf-field placeholder "john@smith.com"]</div>

            <div class="premium-cf-full"><label class="premium-cf-label">Message</label>
            [textarea* textarea-1 class:premium-cf-field placeholder "Enter your message here..."]</div>

            [submit "Send"]',

			'preset5' => '<div class="premium-cf-half"><label class="premium-cf-label">First Name</label>
            [text* text-1 class:premium-cf-field placeholder "John"]</div>

            <div class="premium-cf-half"><label class="premium-cf-label">Last Name</label>
            [text* text-2 class:premium-cf-field placeholder "Smith"]</div>

            <div class="premium-cf-half"><label class="premium-cf-label">Email</label>
            [email* email-1 class:premium-cf-field placeholder "john@smith.com"]</div>

            <div class="premium-cf-half"><label class="premium-cf-label">Phone</label>
            [tel* tel-1 class:premium-cf-field placeholder "+13137262547"]</div>

            <div class="premium-cf-full"><label class="premium-cf-label">Gender</label>
            [select menu-1 "Male" "Female"]</div>

            <div class="premium-cf-full"><label class="premium-cf-label">Message</label>
            [textarea* textarea-1 class:premium-cf-field placeholder "Enter your message here..."]</div>
            [submit "Send"]',

			'preset6' => '<div class="premium-cf-half"><label class="premium-cf-label">First Name</label>
            [text* text-1 class:premium-cf-field placeholder "John"]</div>

            <div class="premium-cf-half"><label class="premium-cf-label">Last Name</label>
            [text* text-2 class:premium-cf-field placeholder "Smith"]</div>

            <div class="premium-cf-half"><label class="premium-cf-label">Email</label>
            [email* email-1 class:premium-cf-field placeholder "john@smith.com"]</div>

            <div class="premium-cf-half"><label class="premium-cf-label">Phone</label>
            [tel* tel-1 class:premium-cf-field placeholder "+13137262547"]</div>

			<div class="premium-cf-full"><label class="premium-cf-label">Company Size</label>
            [radio radio-1 default:1 "1-10 employees" "11-30 employees" "30-50 employees" "Above 50 employee"]
			</div>

            <div class="premium-cf-full"><label class="premium-cf-label">Message</label>
            [textarea* textarea-1 class:premium-cf-field placeholder "Enter your message here..."]</div>
            [submit "Send"]',

		);

		return $forms_array[ $preset ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render Rating Stars
	 *
	 * @since 4.10.13
	 * @access public
	 *
	 * @param float  $rating rating score.
	 * @param string $fill_color fill color.
	 * @param string $empty_color empty color.
	 * @param float  $star_size star size.
	 */
	public static function render_rating_stars( $rating, $fill_color, $empty_color, $star_size ) {

		?>

		<span class="premium-fb-rev-stars">
		<?php

		foreach ( array( 1, 2, 3, 4, 5 ) as $val ) {
			$score = round( ( $rating - $val ), 2 );

			if ( $score >= -0.2 ) {

				?>
					<span class="premium-fb-rev-star"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="<?php echo esc_attr( $star_size ); ?>" height="<?php echo esc_attr( $star_size ); ?>" viewBox="0 0 1792 1792"><path d="M1728 647q0 22-26 48l-363 354 86 500q1 7 1 20 0 21-10.5 35.5t-30.5 14.5q-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z" fill="<?php echo esc_attr( $fill_color ); ?>"></path></svg></span>
				<?php
			} elseif ( $score > -0.8 && $score < -0.2 ) {
				?>
					<span class="premium-fb-rev-star"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="<?php echo esc_attr( $star_size ); ?>" height="<?php echo esc_attr( $star_size ); ?>" viewBox="0 0 1792 1792"><path d="M1250 957l257-250-356-52-66-10-30-60-159-322v963l59 31 318 168-60-355-12-66zm452-262l-363 354 86 500q5 33-6 51.5t-34 18.5q-17 0-40-12l-449-236-449 236q-23 12-40 12-23 0-34-18.5t-6-51.5l86-500-364-354q-32-32-23-59.5t54-34.5l502-73 225-455q20-41 49-41 28 0 49 41l225 455 502 73q45 7 54 34.5t-24 59.5z" fill="<?php echo esc_attr( $fill_color ); ?>"></path></svg></span>
			<?php } else { ?>
					<span class="premium-fb-rev-star"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="<?php echo esc_attr( $star_size ); ?>" height="<?php echo esc_attr( $star_size ); ?>" viewBox="0 0 1792 1792"><path d="M1201 1004l306-297-422-62-189-382-189 382-422 62 306 297-73 421 378-199 377 199zm527-357q0 22-26 48l-363 354 86 500q1 7 1 20 0 50-41 50-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z" fill="<?php echo esc_attr( $empty_color ); ?>"></path></svg></span>
					<?php
			}
		}
		?>
		</span>

		<?php
	}


	/**
	 * Get SVG Shapes
	 *
	 * @since 4.10.13
	 * @access public
	 */
	public static function get_svg_shapes( $shape = '' ) {

		if ( null === self::$shapes ) {

			self::$shapes = require PREMIUM_ADDONS_PATH . 'modules/premium-shape-divider/shapes.php';

		}

		$shapes = self::$shapes;

		if ( empty( $shape ) ) {
			return $shapes;
		} else {
			return $shapes[ $shape ]['imagesmall'];
		}
	}

	public static function get_btn_svgs( $style = 'line1' ) {

		$html = '';

		switch ( $style ) {
			case 'line1':
				$html = '<div class="premium-btn-line-wrap"><svg class="premium-btn-svg" width="100%" height="9" viewBox="0 0 101 9">
                <path d="M.426 1.973C4.144 1.567 17.77-.514 21.443 1.48 24.296 3.026 24.844 4.627 27.5 7c3.075 2.748 6.642-4.141 10.066-4.688 7.517-1.2 13.237 5.425 17.59 2.745C58.5 3 60.464-1.786 66 2c1.996 1.365 3.174 3.737 5.286 4.41 5.423 1.727 25.34-7.981 29.14-1.294" pathLength="1"></path>
                </svg></div>';
				break;

			case 'line3':
				$html = '<div class="premium-btn-line-wrap"><svg class="premium-btn-svg" width="100%" height="18" viewBox="0 0 59 18">
                <path d="M.945.149C12.3 16.142 43.573 22.572 58.785 10.842" pathLength="1"></path>
                </svg></div>';
				break;

			case 'line4':
				$html = '<svg class="premium-btn-svg" width="300%" height="100%" viewBox="0 0 1200 60" preserveAspectRatio="none">
                <path d="M0,56.5c0,0,298.666,0,399.333,0C448.336,56.5,513.994,46,597,46c77.327,0,135,10.5,200.999,10.5c95.996,0,402.001,0,402.001,0"></path>
                </svg>';
				break;

			default:
				// code...
				break;
		}

		return $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Add Button Hover Controls
	 *
	 * @since 4.10.17
	 * @access public
	 *
	 * @param object $elem widget object.
	 * @param array  $conditions controls conditions.
	 */
	public static function add_btn_hover_controls( $elem, $conditions ) {

		$elem->add_control(
			'premium_button_hover_effect',
			array(
				'label'       => __( 'Hover Effect', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'none',
				'options'     => array(
					'none'   => __( 'None', 'premium-addons-for-elementor' ),
					'style1' => __( 'Slide', 'premium-addons-for-elementor' ),
					'style2' => __( 'Shutter', 'premium-addons-for-elementor' ),
					'style5' => apply_filters( 'pa_pro_label', __( 'In & Out (Pro)', 'premium-addons-for-elementor' ) ),
					'style6' => apply_filters( 'pa_pro_label', __( 'Grow (Pro)', 'premium-addons-for-elementor' ) ),
					'style7' => apply_filters( 'pa_pro_label', __( 'Double Layers (Pro)', 'premium-addons-for-elementor' ) ),
					'style8' => apply_filters( 'pa_pro_label', __( 'Animated Underline (Pro)', 'premium-addons-for-elementor' ) ),
				),
				'separator'   => 'before',
				'label_block' => true,
				'condition'   => $conditions,
			)
		);

		do_action( 'pa_button_hover_controls', $elem, $conditions );

		$elem->add_control(
			'premium_button_style1_dir',
			array(
				'label'       => __( 'Slide Direction', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'bottom',
				'options'     => array(
					'bottom' => __( 'Top to Bottom', 'premium-addons-for-elementor' ),
					'top'    => __( 'Bottom to Top', 'premium-addons-for-elementor' ),
					'left'   => __( 'Right to Left', 'premium-addons-for-elementor' ),
					'right'  => __( 'Left to Right', 'premium-addons-for-elementor' ),
				),
				'condition'   => array_merge(
					$conditions,
					array(
						'premium_button_hover_effect' => 'style1',
					)
				),
				'label_block' => true,
			)
		);

		$elem->add_control(
			'premium_button_style2_dir',
			array(
				'label'       => __( 'Shutter Direction', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'shutouthor',
				'options'     => array(
					'shutinhor'    => __( 'Shutter in Horizontal', 'premium-addons-for-elementor' ),
					'shutinver'    => __( 'Shutter in Vertical', 'premium-addons-for-elementor' ),
					'shutoutver'   => __( 'Shutter out Horizontal', 'premium-addons-for-elementor' ),
					'shutouthor'   => __( 'Shutter out Vertical', 'premium-addons-for-elementor' ),
					'scshutoutver' => __( 'Scaled Shutter Vertical', 'premium-addons-for-elementor' ),
					'scshutouthor' => __( 'Scaled Shutter Horizontal', 'premium-addons-for-elementor' ),
					'dshutinver'   => __( 'Tilted Left', 'premium-addons-for-elementor' ),
					'dshutinhor'   => __( 'Tilted Right', 'premium-addons-for-elementor' ),
				),
				'condition'   => array_merge(
					$conditions,
					array(
						'premium_button_hover_effect' => 'style2',
					)
				),
				'label_block' => true,
			)
		);
	}

	/**
	 * Get Button Class
	 *
	 * @since 4.10.17
	 * @access public
	 *
	 * @param $settings object widget settings.
	 *
	 * @return string $class css class.
	 */
	public static function get_button_class( $settings ) {

		$class = '';

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( ! $papro_activated && ! in_array( $settings['premium_button_hover_effect'], array( 'none', 'style1', 'style2' ) ) ) {
			return '';
		}

		if ( 'style1' === $settings['premium_button_hover_effect'] ) {
			$class = 'premium-button-style1-' . $settings['premium_button_style1_dir'];
		} elseif ( 'style2' === $settings['premium_button_hover_effect'] ) {
			$class = 'premium-button-style2-' . $settings['premium_button_style2_dir'];
		} elseif ( 'style5' === $settings['premium_button_hover_effect'] ) {
			$class = 'premium-button-style5-' . $settings['premium_button_style5_dir'];
		} elseif ( 'style6' === $settings['premium_button_hover_effect'] ) {
			$class = 'premium-button-style6';
		} elseif ( 'style7' === $settings['premium_button_hover_effect'] ) {
			$class = 'premium-button-style7-' . $settings['premium_button_style7_dir'];
		} elseif ( 'style8' === $settings['premium_button_hover_effect'] ) {
			$class = 'premium-button-' . $settings['underline_style'];
		}

		return 'premium-button-' . $settings['premium_button_hover_effect'] . ' ' . $class;
	}


	/**
	 * Get Empty Query Message
	 *
	 * Written in PHP and used to generate the final HTML when the query is empty
	 *
	 * @since 4.10.29
	 * @access protected
	 *
	 * @param string $notice empty query notice.
	 */
	public static function render_empty_query_message( $notice ) {

		if ( empty( $notice ) ) {
			$notice = __( 'The current query has no posts. Please make sure you have published items matching your query.', 'premium-addons-for-elementor' );
		}

		?>
		<div class="premium-error-notice">
			<?php echo wp_kses_post( $notice ); ?>
		</div>
		<?php
	}

	/**
	 * Check Capability
	 *
	 * @since 4.10.28
	 * @access public
	 *
	 * @param string $check capability.
	 */
	public static function check_capability( $capability ) {

		$post_author_id = get_the_author_meta( 'ID' );

		$current_user_can = user_can( $post_author_id, $capability );

		return $current_user_can;
	}


	/**
	 * Get Allowed Icon Tags
	 *
	 * Returns an array of allowed HTML tags.
	 *
	 * @since 4.10.69
	 * @access public
	 *
	 * @return array Array of allowed HTML tags.
	 */
	public static function get_allowed_icon_tags() {
		return array(
			'svg'   => array(
				'id'              => array(),
				'class'           => array(),
				'aria-hidden'     => array(),
				'aria-labelledby' => array(),
				'role'            => array(),
				'xmlns'           => array(),
				'width'           => array(),
				'height'          => array(),
				'viewbox'         => array(),
				'data-*'          => true,
			),
			'g'     => array( 'fill' => array() ),
			'title' => array( 'title' => array() ),
			'path'  => array(
				'd'    => array(),
				'fill' => array(),
			),
			'i'     => array(
				'class' => array(),
				'id'    => array(),
				'style' => array(),
			),
		);
	}

	/**
	 * Get SVG By Icon
	 *
	 * @since 4.10.69
	 * @access public
	 */
	public static function get_svg_by_icon( $icon, $attributes = array() ) {

		if ( empty( $icon ) || empty( $icon['value'] ) || empty( $icon['library'] ) ) {
			return '';
		}

		// If icon library is SVG, then go to Elementor. Used for widgets where this function is called in all cases.
		if ( false === strpos( $icon['library'], 'fa-' ) ) {
			$svg_html = Icons_Manager::try_get_icon_html( $icon, wp_parse_args( $attributes, array( 'aria-hidden' => 'true' ) ) );

			return $svg_html;
		}

		$icon['font_family'] = 'font-awesome';

		$i_class = str_replace( ' ', '-', $icon['value'] );

		$svg_html = '<svg ';

		$icon = self::get_icon_svg_data( $icon );

		if ( ! $icon ) {
			// Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
			Icons_Manager::render_icon( $icon, wp_parse_args( $attributes, array( 'aria-hidden' => 'true' ) ) );
			return;
		}

		$view_box = '0 0 ' . $icon['width'] . ' ' . $icon['height'];

		if ( is_array( $attributes ) ) {

			foreach ( $attributes as $key => $value ) {

				if ( 'class' === $key ) {

					$svg_html .= 'class="svg-inline--' . $i_class . ' ' . $value . '" ';

				} else {
					$svg_html .= " {$key}='{$value}' ";
				}
			}
		} else {

			$attributes = str_replace( 'class="', 'class="svg-inline--' . $i_class . ' ', $attributes );

			$svg_html .= $attributes;
		}

		$svg_html .= " aria-hidden='true' xmlns='http://www.w3.org/2000/svg' viewBox='{$view_box}'>";

		$svg_html .= '<path d="' . esc_attr( $icon['path'] ) . '"></path>';
		$svg_html .= '</svg>';

		return wp_kses( $svg_html, self::get_allowed_icon_tags() );
	}

	/**
	 * Get Elementor Template ID
	 *
	 * @since 4.10.80
	 * @access public
	 *
	 * @param string $title template title.
	 *
	 * @return string $template_id template ID.
	 */
	public static function get_elementor_template_id( $title ) {

		if ( empty( $title ) ) {
			return;
		}

		$args = array(
			'post_type'        => 'elementor_library',
			'post_status'      => 'publish',
			'posts_per_page'   => 1,
			'title'            => $title,
			'suppress_filters' => true,
		);

		$query = new \WP_Query( $args );

		$post_id = '';

		if ( $query->have_posts() ) {
			$post_id = $query->post->ID;

			wp_reset_postdata();
		}

		return $post_id;
	}

	/**
	 * Render Elementor Template
	 *
	 * @since 4.10.80
	 * @access public
	 *
	 * @param string|int $title   Template Title||id.
	 * @param bool       $id          indicates if $title is the template title or id.
	 *
	 * @return $template_content string HTML Markup of the selected template.
	 */
	public static function render_elementor_template( $title, $id = false ) {

		$frontend = Plugin::$instance->frontend;

		$custom_temp = apply_filters( 'pa_temp_id', false );

		if ( $custom_temp ) {
			$id = $title = $custom_temp;
		}

		if ( ! $id ) {
			$id = self::get_elementor_template_id( $title );

			if ( ! $id ) {
				// To replace the &#8211; in templates names with dash.
				$decoded_title = html_entity_decode( $title );
				$id            = self::get_elementor_template_id( $decoded_title );
			}

			$id = apply_filters( 'wpml_object_id', $id, 'elementor_library', true );
		} else {
			$id = $title;
		}

		// $template_content = $frontend->get_builder_content( $id, true );
		$template_content = $frontend->get_builder_content_for_display( $id, true );

		return $template_content;
	}
}
