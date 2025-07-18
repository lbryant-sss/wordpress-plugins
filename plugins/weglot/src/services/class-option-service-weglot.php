<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Exception;
use http\Env\Request;
use Morphism\Morphism;
use Weglot\Util\Regex;
use Weglot\Util\Regex\RegexEnum;
use WeglotWP\Helpers\Helper_Is_Admin;
use WeglotWP\Models\Schema_Option_V3;
use WeglotWP\Helpers\Helper_Flag_Type;
use WeglotWP\Helpers\Helper_API;


/**
 * Option services
 *
 * @since 2.0
 */
class Option_Service_Weglot {
	/**
	 * @var string|null|array<string, mixed>
	 */
	protected $options_cdn = null;
	/**
	 * @var null|mixed
	 */
	protected $slugs_cache = null;
	/**
	 * @var null|array<string, mixed>
	 */
	protected $options_from_api = null;
	/**
	 * @var null|array<string, mixed>
	 */
	protected $slugs_from_api = null;

	const NO_OPTIONS = 'OPTIONS_NOT_FOUND';

	/**
	 * @var array<string,mixed>
	 */
	protected $options_default = array(
		'api_key_private'         => '',
		'api_key'                 => '',
		'language_from'           => 'en',
		'languages'               => array(),
		'auto_switch'             => false,
		'auto_switch_fallback'    => null,
		'excluded_blocks'         => array(),
		'excluded_paths'          => array(),
		'custom_css'  => '',
		'switchers'        => array(),
		'custom_settings'         => array(
			'translate_email'  => false,
			'translate_amp'    => false,
			'translate_search' => false,
			'button_style'     => array(
				'full_name'   => true,
				'with_name'   => true,
				'is_dropdown' => true,
				'with_flags'  => true,
				'flag_type'   => Helper_Flag_Type::RECTANGLE_MAT,
				'custom_css'  => '',
			),
			'switchers'        => array(),
			'rtl_ltr_style'    => '',
			'active_wc_reload' => true,
			'flag_css'         => '',
		),
		'media_enabled'           => false,
		'external_enabled'        => false,
		'page_views_enabled'      => false,
		'allowed'                 => true,
		'has_first_settings'      => true,
		'show_box_first_settings' => false,
		'version'                 => 1,
	);

	/**
	 * @var array<string,mixed>
	 */
	protected $options_bdd_default = array(
		'has_first_settings'      => true,
		'show_box_first_settings' => false,
		'menu_switcher'           => array(),
		'custom_urls'             => array(),
		'flag_css'                => '',
		'active_wc_reload'        => true,
	);

	/**
	 * @throws Exception
	 * @since 3.0.0
	 */
	public function __construct() {
		Morphism::setMapper( 'WeglotWP\Models\Schema_Option_V3', Schema_Option_V3::get_schema_options_v3_compatible() );
	}


	/**
	 * Get options default
	 *
	 * @return array<string,mixed>
	 * @since 2.0
	 */
	public function get_options_default() {
		return $this->options_default;
	}

	/**
	 * @param string $api_key
	 *
	 * @return array<string,mixed>
	 * @since 3.0.0
	 */
	protected function get_options_from_cdn_with_api_key( $api_key ) {
		if ( $this->options_cdn === self::NO_OPTIONS ) {
			return array( 'success' => false );
		}
		if ( $this->options_cdn ) {
			return array(
				'success' => true,
				'result'  => $this->options_cdn,
			);
		}

		$cache_transient = apply_filters( 'weglot_get_options_from_cdn_cache', true );

		if ( $cache_transient ) {
			$options = get_transient( 'weglot_cache_cdn' );
			if ( $options ) {
				$this->options_cdn = $options;
				if ( $this->options_cdn === self::NO_OPTIONS ) {
					return array( 'success' => false );
				}

				return array(
					'success' => true,
					'result'  => $this->options_cdn,
				);
			}
		}

		$key = str_replace( 'wg_', '', $api_key );
		$url = sprintf( '%s%s.json', Helper_API::get_cdn_url(), $key );

		$response = wp_remote_get( // phpcs:ignore
			$url,
			array(
				'timeout' => 3,
			)
		);

		try {
			if ( is_wp_error( $response ) ) {
				$response = $this->get_options_from_api_with_api_key( $this->get_api_key_private() );
				$body     = $response['result'];
			} elseif ( wp_remote_retrieve_response_code( $response ) === 403 ) {
				set_transient( 'weglot_cache_cdn', self::NO_OPTIONS, 0 );
				$this->options_cdn = self::NO_OPTIONS;

				return array( 'success' => false );
			} else {
				$body = json_decode( $response['body'], true );
				set_transient( 'weglot_cache_cdn', $body, apply_filters( 'weglot_get_options_from_cdn_cache_duration', 300 ) );
			}

			$this->options_cdn = $body;

			return array(
				'success' => true,
				'result'  => $body,
			);
		} catch ( Exception $th ) {
			return array(
				'success' => false,
			);
		}
	}

	/**
	 * @param string $api_key
	 * @param array<int|string,mixed> $destinations_languages
	 *
	 * @return array<string,string>
	 * @since 3.0.0
	 */
	protected function get_slugs_from_cache_with_api_key( $api_key, $destinations_languages ) {
		if ( $this->slugs_cache ) {
			return $this->slugs_cache;
		}

		$cache_transient = apply_filters( 'weglot_get_slugs_from_cache', true );

		if ( $cache_transient ) {
			$slugs = get_transient( 'weglot_slugs_cache' );
			if ( false !== $slugs ) {
				$this->slugs_cache = $slugs;

				return $this->slugs_cache;
			}
		}

		try {
			$body              = $this->get_slugs_from_api_with_api_key( $api_key, $destinations_languages );
			$this->slugs_cache = $body;

			return $body;
		} catch ( Exception $th ) {
			return array();
		}
	}

	/**
	 * @param string $api_key
	 *
	 * @return array<string,mixed>
	 * @since 3.0.0
	 */
	public function get_options_from_api_with_api_key( $api_key ) {
		if ( $this->options_from_api ) {
			return array(
				'success' => true,
				'result'  => $this->options_from_api,
			);
		}

		$url = sprintf( '%s/projects/settings?api_key=%s', Helper_API::get_api_url(), $api_key );

		$response = wp_remote_get( // phpcs:ignore
			$url,
			array(
				'timeout' => 3,
			)
		);

		if ( is_wp_error( $response ) ) {
			return array(
				'success' => false,
				'result'  => $this->get_options_default(),
			);
		}

		try {
			$body = json_decode( $response['body'], true );
			if ( null === $body || ! is_array( $body ) ) {
				return array(
					'success' => false,
					'result'  => $this->get_options_default(),
				);
			}

			$options                    = apply_filters( 'weglot_get_options', array_merge( $this->get_options_bdd_v3(), $body ) );
			$options['api_key_private'] = $this->get_api_key_private();
			if ( empty( $options['custom_settings']['menu_switcher'] ) ) {
				/** @var Menu_Options_Service_Weglot $menu_options_services */
				$menu_options_services                       = weglot_get_service( 'Menu_Options_Service_Weglot' );
				$options['custom_settings']['menu_switcher'] = $menu_options_services->get_options_default();
			}
			$this->options_from_api = $options;
			set_transient( 'weglot_cache_cdn', $options, apply_filters( 'weglot_get_options_from_cdn_cache_duration', 300 ) );

			return array(
				'success' => true,
				'result'  => $options,
			);
		} catch ( Exception $e ) {
			return array(
				'success' => false,
			);
		}
	}

	/**
	 * @param string $api_key
	 * @param array<int|string,mixed>$destinations_languages
	 *
	 * @return array<int|string,mixed>
	 * @since 3.0.0
	 */
	public function get_slugs_from_api_with_api_key( $api_key, $destinations_languages ) {
		$active_slugs = apply_filters( 'weglot_active_slugs', true );
		if ( $this->slugs_from_api || ! $active_slugs) {
			return $this->slugs_from_api;
		}
		$custom_timeout = apply_filters('custom_http_request_timeout', 3);
		$slugs = array();
		$settings = get_transient( 'weglot_cache_cdn' );
		if ( empty( $settings ) ) {
			$settings = $this->get_options();
		}
		$slug_translation_version = $settings['versions']['slugTranslation'] ?? null;
		foreach ( $destinations_languages as $destinations_language ) {

			if($slug_translation_version != null){
				$url = sprintf(
					'%s/translations/slugs?api_key=%s&language_to=%s&v=%s',
					Helper_API::get_api_url(),
					$api_key,
					$destinations_language,
					$slug_translation_version
				);
			}else{
				$url = sprintf(
					'%s/translations/slugs?api_key=%s&language_to=%s',
					Helper_API::get_api_url(),
					$api_key,
					$destinations_language
				);
			}

			$response = wp_remote_get( $url, array( 'timeout' => $custom_timeout ) ); // phpcs:ignore

			if ( is_wp_error( $response ) ) {
				continue;
			}
			try {
				$body = json_decode( $response['body'], true );

				if ( is_array( $body ) ) {
					// We remove slug where original = translated slug or if slug is empty
					foreach ( $body as $key => $slug ) {
						if ( $key === $slug || empty( $slug ) ) {
							unset( $body[ $key ] );
						}
					}
					$slugs[ $destinations_language ] = array_flip( $body );

				}
			} catch ( Exception $e ) {
				continue;
			}
		}

		set_transient( 'weglot_slugs_cache', $slugs, apply_filters( 'weglot_get_slugs_cache_duration', 0 ) );
		$this->slugs_from_api = $slugs;

		return $slugs;
	}

	/**
	 * @return array<string,mixed>
	 * @throws Exception
	 * @since 3.0.0
	 */
	public function get_options_from_v2() {
		$options_v2 = get_option( WEGLOT_SLUG );

		if ( $options_v2 ) {

			if ( array_key_exists( 'api_key', $options_v2 ) ) {
				$options_v2['api_key_private'] = $options_v2['api_key'];
			}
			if ( ! array_key_exists( 'custom_urls', $options_v2 ) || ! $options_v2['custom_urls'] ) {
				$options_v2['custom_urls'] = array();
			}

			return $options_v2;
		}

		return (array) Morphism::map( 'WeglotWP\Models\Schema_Option_V3', $this->get_options_default() );
	}

	/**
	 * @param bool $compatibility
	 *
	 * @return string
	 * @throws Exception
	 * @since 3.0.0
	 */
	public function get_api_key( $compatibility = false ) {
		$api_key = get_option( sprintf( '%s-%s', WEGLOT_SLUG, 'api_key' ), false );

		if ( ! $compatibility || $api_key ) {
			return apply_filters( 'weglot_get_api_key', $api_key );
		}

		$options = $this->get_options_from_v2();

		return apply_filters( 'weglot_get_api_key', $options['api_key'] );
	}

	/**
	 *
	 * @return string
	 * @throws Exception
	 * @since 3.0.0
	 */
	public function get_version() {
		$options = $this->get_options();
		if ( ! isset( $options['versions']['translation'] ) ) {
			return apply_filters( 'weglot_get_version', 1 );
		}

		return apply_filters( 'weglot_get_version', $options['versions']['translation'] );
	}

	/**
	 * @param bool $compatibility
	 *
	 * @return bool
	 * @throws Exception
	 * @since 3.0.0
	 */
	public function get_has_first_settings( $compatibility = false ) {
		$options = $this->get_options();

		if ( ! $compatibility || array_key_exists( 'has_first_settings', $options ) ) {
			return $options['has_first_settings'];
		}

		$options = $this->get_options_from_v2();

		return $options['has_first_settings'];
	}

	/**
	 * @return array<int|string,mixed>
	 * @throws Exception
	 * @since 2.0
	 * @version 3.0.0
	 */
	public function get_options() {
		$api_key         = $this->get_api_key();
		$api_key_private = $this->get_api_key_private();

		$is_weglot_settings_page = isset( $_GET['page'] ) && strpos( $_GET['page'], 'weglot-settings' ) !== false; //phpcs:ignore

		if ( Helper_Is_Admin::is_wp_admin() && $api_key_private && $is_weglot_settings_page ) {
			$response = $this->get_options_from_api_with_api_key( $api_key_private );
		} else {
			if ( ( ! Helper_Is_Admin::is_wp_admin() && $api_key ) || ( Helper_Is_Admin::is_wp_admin() && ! $is_weglot_settings_page && $api_key ) ) {
				$response = $this->get_options_from_cdn_with_api_key( $api_key );
			} else {
				return $this->get_options_from_v2();
			}
		}

		if ( ! array_key_exists( 'result', $response ) ) {
			return $this->get_options_from_v2();
		}

		$options = $response['result'];
		if ( $api_key_private ) {
			$options['api_key_private'] = $api_key_private;
		}

		if ( ! isset( $options['api_key'] ) ) {
			return $options;
		}


		$options = apply_filters( 'weglot_get_options', array_merge( $this->options_bdd_default, $this->get_options_bdd_v3(), $options ) );
		$options = (array) Morphism::map( 'WeglotWP\Models\Schema_Option_V3', $options );

		$destinations_languages = array_column( $options['destination_language'], 'language_to' );

		if ( Helper_Is_Admin::is_wp_admin() && $api_key_private && $is_weglot_settings_page ) {
			$slugs = $this->get_slugs_from_api_with_api_key( $api_key_private, $destinations_languages );
		} else {
			if ( ( ! Helper_Is_Admin::is_wp_admin() && $api_key ) || ( Helper_Is_Admin::is_wp_admin() && ! $is_weglot_settings_page ) ) {
				$slugs = $this->get_slugs_from_cache_with_api_key( $api_key_private, $destinations_languages );
			}
		}
		if ( isset( $slugs ) && is_array( $slugs ) ) {
			$options['custom_urls'] = $this->array_merge_recursive_ex( $options['custom_urls'], $slugs );
		}

		return $options;
	}

	/**
	 * @param array<int|string,mixed> $array1
	 * @param array<int|string,mixed> $array2
	 * @return array<string,mixed>
	 * @throws Exception
	 * @since 2.0
	 * @version 3.0.0
	 */
	public function array_merge_recursive_ex( array $array1, array $array2 ) {
		$merged = $array1;

		foreach ( $array2 as $key => & $value ) {
			if ( is_array( $value ) && isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) {
				$merged[ $key ] = $this->array_merge_recursive_ex( $merged[ $key ], $value );
			} elseif ( is_numeric( $key ) ) {
				if ( ! in_array( $value, $merged ) ) {
					$merged[] = $value;
				}
			} else {
				$merged[ $key ] = $value;
			}
		}

		return $merged;
	}

	/**
	 * @return string
	 * @since 3.0.0
	 */
	public function get_api_key_private() {
		return get_option( sprintf( '%s-%s', WEGLOT_SLUG, 'api_key_private' ) );
	}


	/**
	 * @param array<string,mixed> $options
	 *
	 * @return array<string,mixed>
	 * @since 3.0.0
	 */
	public function save_options_to_weglot( $options ) {

		$response = wp_remote_post( // phpcs:ignore
			sprintf( '%s/projects/settings?api_key=%s', Helper_API::get_api_url(), $options['api_key_private'] ),
			array(
				'body'    => wp_json_encode( $options ), // phpcs:ignore.
				'timeout' => 60, // phpcs:ignore
				'headers' => array(
					'technology'   => 'wordpress',
					'Content-Type' => 'application/json; charset=utf-8',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return array(
				'success' => false,
			);
		}

		return array(
			'success' => true,
			'result'  => json_decode( $response['body'], true ),
		);
	}

	/**
	 * @param string $key
	 *
	 * @return string|null
	 * @throws Exception
	 * @since 3.0.0
	 */
	public function get_option_custom_settings( $key ) {
		$options = $this->get_options();

		if ( ! array_key_exists( 'custom_settings', $options ) ) {
			return $this->get_option( $key );
		}

		if ( ! array_key_exists( $key, $options['custom_settings'] ) ) {
			return null;
		}

		return $options['custom_settings'][ $key ];
	}

	/**
	 * @param string $key
	 *
	 * @return mixed
	 * @throws Exception
	 * @since 2.0
	 */
	public function get_option( $key ) {
		$options = $this->get_options();
		if ( ! array_key_exists( $key, $options ) ) {
			return null;
		}

		return apply_filters( 'weglot_get_option', $options[ $key ], $key, $options );
	}

	/**
	 * @param string $key
	 *
	 * @return mixed
	 * @throws Exception
	 * @since 3.0.0
	 */
	public function get_option_button( $key ) {
		$options = $this->get_options();
		if (
			array_key_exists( 'custom_settings', $options ) &&
			is_array( $options['custom_settings'] ) &&
			array_key_exists( $key, $options['custom_settings']['button_style'] )
		) {
			return $options['custom_settings']['button_style'][ $key ];
		}

		// Retrocompatibility v2
		if ( ! array_key_exists( $key, $options ) ) {
			return null;
		}

		return $options[ $key ];
	}

	/**
	 *
	 * Returns the array "button_style" and validate it to avoid empty option button
	 *
	 * @return void
	 * @throws Exception
	 * @since 3.0.0
	 */
	public function validate_button_option() {
		$options = $this->get_options();
		if (
			array_key_exists( 'custom_settings', $options ) &&
			is_array( $options['custom_settings'] ) &&
			! empty( $options['custom_settings']['button_style'] )
		) {
			add_filter( 'weglot_get_options_from_cdn_cache', '__return_false' );
			$button_options = $options['custom_settings']['button_style'];
			if (
				$button_options['is_dropdown'] === false &&
				$button_options['with_flags'] === false &&
				$button_options['with_name'] === false &&
				$button_options['full_name'] === false
			) {
				$options['custom_settings']['button_style']['is_dropdown'] = true;
				$options['custom_settings']['button_style']['with_name'] = true;
				$options['custom_settings']['button_style']['full_name'] = true;
				$options['custom_settings']['button_style']['with_flags'] = true;

				$response           = $this->save_options_to_weglot( $options );
				if ( $response['success'] && is_array( $response['result'] ) ) {

					$options_bdd = $this->get_options_bdd_v3();
					$options_bdd['custom_settings']['button_style']['is_dropdown'] = true;
					$options_bdd['custom_settings']['button_style']['with_name'] = true;
					$options_bdd['custom_settings']['button_style']['full_name'] = true;
					$options_bdd['custom_settings']['button_style']['with_flags'] = true;
					$this->set_options( $options_bdd );
					delete_transient( 'weglot_cache_cdn' );
				}
			}
		}
	}

	/**
	 *
	 * Returns the array "switchers" from the custom_settings or an empty array
	 *
	 * @return array<string,mixed>|boolean|int
	 * @since 3.0.0
	 */
	public function get_switchers_editor_button() {
		$options = $this->get_options();

		if (!empty($options['switchers']) && is_array($options['switchers'])) {
			return $options['switchers'];
		}

		if (
			array_key_exists('custom_settings', $options) &&
			is_array($options['custom_settings']) &&
			!empty($options['custom_settings']['switchers'])
		) {
			$options['switchers'] = $options['custom_settings']['switchers'];
			return $options['switchers'];
		}

		// If neither exists, return an empty array
		return [];
	}

	/**
	 * @param string $key
	 * @param array<string,mixed> $switcher
	 *
	 * @return mixed
	 * @throws Exception
	 * @since 3.0.0
	 */
	public function get_switcher_editor_option( $key, $switcher ) {

		if ( ! empty( $switcher ) ) {
			if (
				array_key_exists( $key, $switcher )
			) {
				return $switcher[ $key ];
			}
		}

		return null;
	}

	/**
	 * @return array<int,string>
	 * @throws Exception
	 * @since 2.0
	 */
	public function get_translate_inside_exclusions_blocks(){

		$inside_exclusions_blocks = $this->get_option( 'translate_inside_exclusions' );
		$transformed_array = [];
		if(empty($inside_exclusions_blocks)){
			return [];
		}
		if(count($inside_exclusions_blocks) > 0){
			$transformed_array = array_map(function ($item) {
				return $item['value'];
			}, $inside_exclusions_blocks);
		}
		return apply_filters( 'weglot_inside_exclusions_block', $transformed_array );
	}

	/**
	 * @return array<int,string>
	 * @throws Exception
	 * @since 2.0
	 */
	public function get_exclude_blocks() {
		$exclude_blocks = $this->get_option( 'exclude_blocks' );

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// WordPress.
		$exclude_blocks[] = '#wpadminbar';

		// Weglot Switcher.
		$exclude_blocks[] = '.menu-item-weglot a';

		// Material Icons.
		$exclude_blocks[] = '.material-icons';

		// Font Awesome.
		$exclude_blocks[] = '.fas';
		$exclude_blocks[] = '.far';
		$exclude_blocks[] = '.fad';

		// Plugin Query Monitor.
		if ( is_plugin_active( 'query-monitor/query-monitor.php' ) ) {
			$exclude_blocks[] = '#query-monitor';
			$exclude_blocks[] = '#query-monitor-main';
		}

		// Plugin Woocommerce.
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$exclude_blocks[] = '.mini-cart-counter';
			$exclude_blocks[] = '.amount'; // Added to prevent prices to pass.
			$exclude_blocks[] = 'address';
		}

		// Plugin SecuPress.
		if ( is_plugin_active( 'secupress/secupress.php' ) ) {
			$exclude_blocks[] = '#secupress-donttranslate';
		}

		// Plugin Gamipress.
		if ( is_plugin_active( 'gamipress/gamipress.php' ) ) {
			$exclude_blocks[] = '.gamipress-share-button';
		}

		return apply_filters( 'weglot_exclude_blocks', $exclude_blocks );
	}

	/**
	 * @return array<string,mixed>
	 * @throws Exception
	 * @version 3.0.0
	 * @since 3.2.1
	 */

	public function get_destination_languages() {
		$destination_languages = $this->get_option( 'destination_language' );

		return apply_filters( 'weglot_destination_languages_full', $destination_languages );
	}

	/**
	 * @return array<int,mixed>
	 * @throws Exception
	 * @since 2.0
	 */
	public function get_exclude_urls() {
		$list_exclude_urls = $this->get_option( 'exclude_urls' );

		/** @var Request_Url_Service_Weglot $request_url_services */
		$request_url_services = weglot_get_service( 'Request_Url_Service_Weglot' );
		$exclude_urls         = array();

		if ( ! empty( $list_exclude_urls ) ) {
			foreach ( $list_exclude_urls as $item ) {
				if ( is_array( $item ) ) {
					$excluded_languages = null;
					if ( ! empty( $item['excluded_languages'] ) && is_array( $item['excluded_languages'] ) ) {
						foreach ( $item['excluded_languages'] as $excluded_language ) {
							/** @var Language_Service_Weglot $language_service */
							$language_service     = weglot_get_service( 'Language_Service_Weglot' );
							$excluded_languages[] = $language_service->get_language_from_internal( $excluded_language );
						}
					}
					$regex          = new Regex( $item['type'], $request_url_services->url_to_relative( $item['value'] ) );
					$exclude_urls[] = array(
						$regex,
						$excluded_languages,
						$item['exclusion_behavior'],
						$item['language_button_displayed'],
					);
				}
			}
		}

		$exclude_urls[] = array( new Regex(RegexEnum::CONTAIN, '/wp-login.php'), null );
		$exclude_urls[] = array( new Regex(RegexEnum::CONTAIN, '/sitemaps_xsl.xsl'), null );
		$exclude_urls[] = array( new Regex(RegexEnum::CONTAIN, '/sitemaps.xml'), null );
		$exclude_urls[] = array( new Regex(RegexEnum::CONTAIN, '/wp-cron.php'), null );
		$exclude_urls[] = array( new Regex(RegexEnum::CONTAIN, '/wp-comments-post.php'), null );
		$exclude_urls[] = array( new Regex(RegexEnum::CONTAIN, '/ct_template'), null );
		$exclude_urls[] = array( new Regex(RegexEnum::CONTAIN, '/main-sitemap.xsl'), null );

		if ( ! weglot_get_translate_amp_translation() ) {
			$amp_regex = weglot_get_service( 'Amp_Service_Weglot' )->get_regex();
			$exclude_urls[] = array( new Regex(RegexEnum::CONTAIN, $amp_regex), null );
		}

		return apply_filters( 'weglot_exclude_urls', $exclude_urls );
	}

	/**
	 * @return string
	 * @throws Exception
	 * @since 2.0
	 */
	public function get_css_custom_inline() {
		return apply_filters( 'weglot_css_custom_inline', $this->get_option( 'override_css' ) );
	}

	/**
	 * @return string
	 * @throws Exception
	 * @since 2.0
	 */
	public function get_flag_css() {
		return apply_filters( 'weglot_flag_css', $this->get_option( 'flag_css' ) );
	}

	/**
	 * @return int
	 * @throws Exception
	 * @since 3.0.0
	 */
	public function get_translation_engine() {
		return apply_filters( 'weglot_get_translation_engine', $this->get_option( 'translation_engine' ) );
	}


	/**
	 * @param array<string,mixed> $options
	 *
	 * @return Option_Service_Weglot
	 * @since 2.0
	 */
	public function set_options( $options ) {

		$key = sprintf( '%s-%s', WEGLOT_SLUG, 'v3' );

		update_option( $key, $options );

		wp_cache_delete( $key );

		return $this;
	}

	/**
	 * @return array<string,mixed>|false
	 * @since 3.0.0
	 */
	public function get_options_bdd_v3() {
		return get_option( sprintf( '%s-%s', WEGLOT_SLUG, 'v3' ), $this->options_bdd_default );
	}

	/**
	 *
	 * @param string $key
	 * @param mixed $value
	 *
	 * @return Option_Service_Weglot
	 */
	public function set_option_by_key( $key, $value ) {

		$options = $this->get_options_bdd_v3();

		$options[ $key ] = $value;

		$this->set_options( $options );

		return $this;
	}

	/**
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get_option_by_key_v3( $key ) {
		$options = $this->get_options_bdd_v3();

		if ( ! array_key_exists( $key, $options ) ) {
			return null;
		}

		return $options[ $key ];
	}

	/**
	 * @return string
	 * @throws Exception
	 * @since 2.0
	 */
	public function get_switcher_editor_css() {
		$switcher_editor_css = '';
		if ( ! empty( $this->get_switchers_editor_button() ) ) {
			foreach ( $this->get_switchers_editor_button() as $switcher ) {
				if ( ! empty( $switcher['style']['custom_css'] ) ) {
					$switcher_editor_css .= $switcher['style']['custom_css'];
				}
			}
		}

		return $switcher_editor_css;
	}
}
