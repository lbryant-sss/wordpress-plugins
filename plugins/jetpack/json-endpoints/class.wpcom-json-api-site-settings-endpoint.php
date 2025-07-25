<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Manage settings via the WordPress.com REST API.
 *
 * @package automattic/jetpack
 */

use Automattic\Jetpack\Waf\Brute_Force_Protection\Brute_Force_Protection_Shared_Functions;

new WPCOM_JSON_API_Site_Settings_Endpoint(
	array(
		'description'      => 'Get detailed settings information about a site.',
		'group'            => '__do_not_document',
		'stat'             => 'sites:X',
		'max_version'      => '1.1',
		'new_version'      => '1.2',
		'method'           => 'GET',
		'path'             => '/sites/%s/settings',
		'path_labels'      => array(
			'$site' => '(int|string) Site ID or domain',
		),

		'query_parameters' => array(
			'context' => false,
		),

		'response_format'  => WPCOM_JSON_API_Site_Settings_Endpoint::$site_format,

		'example_request'  => 'https://public-api.wordpress.com/rest/v1/sites/en.blog.wordpress.com/settings',
	)
);

new WPCOM_JSON_API_Site_Settings_Endpoint(
	array(
		'description'         => 'Update settings for a site.',
		'group'               => '__do_not_document',
		'stat'                => 'sites:X',
		'max_version'         => '1.1',
		'new_version'         => '1.2',
		'method'              => 'POST',
		'path'                => '/sites/%s/settings',
		'a_new_very_long_key' => 'blabla',
		'path_labels'         => array(
			'$site' => '(int|string) Site ID or domain',
		),

		'request_format'      => array(
			'migration_source_site_domain'              => '(string) The source site URL, from the migration flow',
			'in_site_migration_flow'                    => '(string) The migration flow the site is in',
			'blogname'                                  => '(string) Blog name',
			'blogdescription'                           => '(string) Blog description',
			'default_pingback_flag'                     => '(bool) Notify blogs linked from article?',
			'default_ping_status'                       => '(bool) Allow link notifications from other blogs?',
			'default_comment_status'                    => '(bool) Allow comments on new articles?',
			'blog_public'                               => '(string) Site visibility; -1: private, 0: discourage search engines, 1: allow search engines',
			'wpcom_data_sharing_opt_out'                => '(bool) Did the site opt out of sharing public content with third parties and research partners?',
			'jetpack_sync_non_public_post_stati'        => '(bool) allow sync of post and pages with non-public posts stati',
			'jetpack_relatedposts_enabled'              => '(bool) Enable related posts?',
			'jetpack_relatedposts_show_context'         => '(bool) Show post\'s tags and category in related posts?',
			'jetpack_relatedposts_show_date'            => '(bool) Show date in related posts?',
			'jetpack_relatedposts_show_headline'        => '(bool) Show headline in related posts?',
			'jetpack_relatedposts_show_thumbnails'      => '(bool) Show thumbnails in related posts?',
			'jetpack_protect_whitelist'                 => '(array) List of IP addresses to always allow',
			'instant_search_enabled'                    => '(bool) Enable the new Jetpack Instant Search interface',
			'jetpack_search_enabled'                    => '(bool) Enable Jetpack Search',
			'jetpack_search_supported'                  => '(bool) Jetpack Search is supported',
			'infinite_scroll'                           => '(bool) Support infinite scroll of posts?',
			'default_category'                          => '(int) Default post category',
			'default_post_format'                       => '(string) Default post format',
			'require_name_email'                        => '(bool) Require comment authors to fill out name and email?',
			'comment_registration'                      => '(bool) Require users to be registered and logged in to comment?',
			'close_comments_for_old_posts'              => '(bool) Automatically close comments on old posts?',
			'close_comments_days_old'                   => '(int) Age at which to close comments',
			'thread_comments'                           => '(bool) Enable threaded comments?',
			'thread_comments_depth'                     => '(int) Depth to thread comments',
			'page_comments'                             => '(bool) Break comments into pages?',
			'comments_per_page'                         => '(int) Number of comments to display per page',
			'default_comments_page'                     => '(string) newest|oldest Which page of comments to display first',
			'comment_order'                             => '(string) asc|desc Order to display comments within page',
			'comments_notify'                           => '(bool) Email me when someone comments?',
			'moderation_notify'                         => '(bool) Email me when a comment is helf for moderation?',
			'social_notifications_like'                 => '(bool) Email me when someone likes my post?',
			'social_notifications_reblog'               => '(bool) Email me when someone reblogs my post?',
			'social_notifications_subscribe'            => '(bool) Email me when someone subscribes to my blog?',
			'comment_moderation'                        => '(bool) Moderate comments for manual approval?',
			'comment_previously_approved'               => '(bool) Moderate comments unless author has a previously-approved comment?',
			'comment_max_links'                         => '(int) Moderate comments that contain X or more links',
			'moderation_keys'                           => '(string) Words or phrases that trigger comment moderation, one per line',
			'disallowed_keys'                           => '(string) Words or phrases that mark comment spam, one per line',
			'lang_id'                                   => '(int) ID for language blog is written in',
			'wga'                                       => '(array) Google Analytics Settings',
			'disabled_likes'                            => '(bool) Are likes globally disabled (they can still be turned on per post)?',
			'disabled_reblogs'                          => '(bool) Are reblogs disabled on posts?',
			'jetpack_comment_likes_enabled'             => '(bool) Are comment likes enabled for all comments?',
			'sharing_button_style'                      => '(string) Style to use for sharing buttons (icon-text, icon, text, or official)',
			'sharing_label'                             => '(string) Label to use for sharing buttons, e.g. "Share this:"',
			'sharing_show'                              => '(string|array:string) Post type or array of types where sharing buttons are to be displayed',
			'sharing_open_links'                        => '(string) Link target for sharing buttons (same or new)',
			'twitter_via'                               => '(string) Twitter username to include in tweets when people share using the Twitter button',
			'jetpack-twitter-cards-site-tag'            => '(string) The Twitter username of the owner of the site\'s domain.',
			'eventbrite_api_token'                      => '(int) The Keyring token ID for an Eventbrite token to associate with the site',
			'timezone_string'                           => '(string) PHP-compatible timezone string like \'UTC-5\'',
			'gmt_offset'                                => '(int) Site offset from UTC in hours',
			'date_format'                               => '(string) PHP Date-compatible date format',
			'time_format'                               => '(string) PHP Date-compatible time format',
			'start_of_week'                             => '(int) Starting day of week (0 = Sunday, 6 = Saturday)',
			'jetpack_testimonial'                       => '(bool) Whether testimonial custom post type is enabled for the site',
			'jetpack_testimonial_posts_per_page'        => '(int) Number of testimonials to show per page',
			'jetpack_portfolio'                         => '(bool) Whether portfolio custom post type is enabled for the site',
			'jetpack_portfolio_posts_per_page'          => '(int) Number of portfolio projects to show per page',
			Jetpack_SEO_Utils::FRONT_PAGE_META_OPTION   => '(string) The seo meta description for the site.',
			Jetpack_SEO_Titles::TITLE_FORMATS_OPTION    => '(array) SEO meta title formats. Allowed keys: front_page, posts, pages, groups, archives',
			'verification_services_codes'               => '(array) Website verification codes. Allowed keys: google, pinterest, bing, yandex, facebook',
			'markdown_supported'                        => '(bool) Whether markdown is supported for this site',
			'wpcom_publish_posts_with_markdown'         => '(bool) Whether markdown is enabled for posts',
			'wpcom_publish_comments_with_markdown'      => '(bool) Whether markdown is enabled for comments',
			'site_icon'                                 => '(int) Media attachment ID to use as site icon. Set to zero or an otherwise empty value to clear',
			'api_cache'                                 => '(bool) Turn on/off the Jetpack JSON API cache',
			'posts_per_page'                            => '(int) Number of posts to show on blog pages',
			'posts_per_rss'                             => '(int) Number of posts to show in the RSS feed',
			'rss_use_excerpt'                           => '(bool) Whether the RSS feed will use post excerpts',
			'launchpad_screen'                          => '(string) Whether or not launchpad is presented and what size it will be',
			'sm_enabled'                                => '(bool) Whether the newsletter subscribe modal is enabled',
			'jetpack_subscribe_overlay_enabled'         => '(bool) Whether the newsletter subscribe overlay is enabled',
			'jetpack_subscribe_floating_button_enabled' => '(bool) Whether the newsletter floating subscribe button is enabled',
			'jetpack_subscriptions_subscribe_post_end_enabled' => '(bool) Whether the Subscribe block at the end of each post placement is enabled',
			'jetpack_subscriptions_login_navigation_enabled' => '(bool) Whether the Subscriber Login block navigation placement is enabled',
			'jetpack_subscriptions_subscribe_navigation_enabled' => '(Bool) Whether the Subscribe block navigation placement is enabled',
			'wpcom_ai_site_prompt'                      => '(string) User input in the AI site prompt',
			'jetpack_waf_automatic_rules'               => '(bool) Whether the WAF should enforce automatic firewall rules',
			'jetpack_waf_ip_allow_list'                 => '(string) List of IP addresses to always allow',
			'jetpack_waf_ip_allow_list_enabled'         => '(bool) Whether the IP allow list is enabled',
			'jetpack_waf_ip_block_list'                 => '(string) List of IP addresses the WAF should always block',
			'jetpack_waf_ip_block_list_enabled'         => '(bool) Whether the IP block list is enabled',
			'jetpack_waf_share_data'                    => '(bool) Whether the WAF should share basic data with Jetpack',
			'jetpack_waf_share_debug_data'              => '(bool) Whether the WAF should share debug data with Jetpack',
			'jetpack_waf_automatic_rules_last_updated_timestamp' => '(int) Timestamp of the last time the automatic rules were updated',
		),

		'response_format'     => array(
			'updated' => '(array)',
		),

		'example_request'     => 'https://public-api.wordpress.com/rest/v1/sites/en.blog.wordpress.com/settings',
	)
);

/**
 * Manage Site settings endpoint.
 *
 * @phan-constructor-used-for-side-effects
 */
class WPCOM_JSON_API_Site_Settings_Endpoint extends WPCOM_JSON_API_Endpoint {

	/**
	 * Site format.
	 *
	 * @var array
	 */
	public static $site_format = array(
		'ID'             => '(int) Site ID',
		'name'           => '(string) Title of site',
		'description'    => '(string) Tagline or description of site',
		'URL'            => '(string) Full URL to the site',
		'lang'           => '(string) Primary language code of the site',
		'locale_variant' => '(string) Locale variant code for the site, if set',
		'settings'       => '(array) An array of options/settings for the blog. Only viewable by users with post editing rights to the site.',
	);

	/**
	 * Endpoint response
	 *
	 * GET /sites/%s/settings
	 * POST /sites/%s/settings
	 *
	 * @param string $path    Path.
	 * @param int    $blog_id Blog ID.
	 */
	public function callback( $path = '', $blog_id = 0 ) {
		$blog_id = $this->api->switch_to_blog_and_validate_user( $this->api->get_blog_id( $blog_id ) );
		if ( is_wp_error( $blog_id ) ) {
			return $blog_id;
		}

		if ( defined( 'IS_WPCOM' ) && IS_WPCOM ) {
			// Source & include the infinite scroll compatibility files prior to loading theme functions.
			add_filter( 'restapi_theme_action_copy_dirs', array( 'WPCOM_JSON_API_Site_Settings_Endpoint', 'wpcom_restapi_copy_theme_plugin_actions' ) );
			$this->load_theme_functions();
		}

		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'Unauthorized', 'You must be logged-in to manage settings.', 401 );
		} elseif ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error( 'Forbidden', 'You do not have the capability to manage settings for this site.', 403 );
		}

		if ( 'GET' === $this->api->method ) {
			/**
			 * Fires on each GET request to a specific endpoint.
			 *
			 * @module json-api
			 *
			 * @since 3.2.0
			 *
			 * @param string sites.
			 */
			do_action( 'wpcom_json_api_objects', 'sites' );
			return $this->get_settings_response();
		} elseif ( 'POST' === $this->api->method ) {
			return $this->update_settings();
		} else {
			return new WP_Error( 'bad_request', 'An unsupported request method was used.' );
		}
	}

	/**
	 * Includes additional theme-specific files to be included in REST API theme
	 * context loading action copying.
	 *
	 * @see WPCOM_JSON_API_Endpoint#load_theme_functions
	 * @see the_neverending_home_page_theme_support
	 *
	 * @param array $copy_dirs Array of files to be included in theme context.
	 */
	public static function wpcom_restapi_copy_theme_plugin_actions( $copy_dirs ) {
		$theme_name        = get_stylesheet();
		$default_file_name = WP_CONTENT_DIR . "/mu-plugins/infinity/themes/{$theme_name}.php";

		/**
		 * Filter the path to the Infinite Scroll compatibility file.
		 *
		 * @module infinite-scroll
		 *
		 * @since 2.0.0
		 *
		 * @param string $str IS compatibility file path.
		 * @param string $theme_name Theme name.
		 */
		$customization_file = apply_filters( 'infinite_scroll_customization_file', $default_file_name, $theme_name );

		if ( is_readable( $customization_file ) ) {
			require_once $customization_file;
			$copy_dirs[] = $customization_file;
		}

		return $copy_dirs;
	}

	/**
	 * Determines whether jetpack_relatedposts is supported
	 *
	 * @return bool
	 */
	public function jetpack_relatedposts_supported() {
		$wpcom_related_posts_theme_blacklist = array(
			'Expound',
			'Traveler',
			'Opti',
			'Currents',
		);
		return ( ! in_array( wp_get_theme()->get( 'Name' ), $wpcom_related_posts_theme_blacklist, true ) );
	}

	/**
	 * Returns category details
	 *
	 * @param WP_Term $category Category object.
	 *
	 * @return array
	 */
	public function get_category_details( $category ) {
		return array(
			'value' => $category->term_id,
			'name'  => $category->name,
		);
	}

	/**
	 * Returns an option value as the result of the callable being applied to
	 * it if a value is set, otherwise null.
	 *
	 * @param string   $option_name   Option name.
	 * @param callable $cast_callable Callable to invoke on option value.
	 *
	 * @return int|null Numeric option value or null.
	 */
	protected function get_cast_option_value_or_null( $option_name, $cast_callable ) {
		$option_value = get_option( $option_name, null );
		if ( $option_value === null ) {
			return $option_value;
		}

		return call_user_func( $cast_callable, $option_value );
	}

	/**
	 * Collects the necessary information to return for a get settings response.
	 *
	 * @return array
	 */
	public function get_settings_response() {
		$response = array();

		// Allow update in later versions.
		/**
		 * Filter the structure of site settings to return.
		 *
		 * @module json-api
		 *
		 * @since 3.9.3
		 *
		 * @param array $site_format Data structure.
		 */
		$response_format = apply_filters( 'site_settings_site_format', self::$site_format );

		$blog_id = (int) $this->api->get_blog_id_for_output();
		$site    = $this->get_platform()->get_site( $blog_id );

		foreach ( array_keys( $response_format ) as $key ) {

			// refactoring to change lang parameter to locale in 1.2.
			$lang_or_locale = $this->get_locale( $key );
			if ( $lang_or_locale ) {
				$response[ $key ] = $lang_or_locale;
				continue;
			}

			switch ( $key ) {
				case 'ID':
					$response[ $key ] = $blog_id;
					break;
				case 'name':
					$response[ $key ] = (string) htmlspecialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
					break;
				case 'description':
					$response[ $key ] = (string) htmlspecialchars_decode( get_bloginfo( 'description' ), ENT_QUOTES );
					break;
				case 'URL':
					$response[ $key ] = (string) home_url();
					break;
				case 'locale_variant':
					if ( function_exists( 'wpcom_l10n_get_blog_locale_variant' ) ) {
						$blog_locale_variant = wpcom_l10n_get_blog_locale_variant();
						if ( $blog_locale_variant ) {
							$response[ $key ] = $blog_locale_variant;
						}
					}
					break;
				case 'settings':
					$jetpack_relatedposts_options = Jetpack_Options::get_option( 'relatedposts', array() );
					// If the option's enabled key is NOT SET, it is considered enabled by the plugin.
					if ( ! isset( $jetpack_relatedposts_options['enabled'] ) ) {
						$jetpack_relatedposts_options['enabled'] = true;
					}

					$jetpack_relatedposts_options['enabled'] =
						$jetpack_relatedposts_options['enabled']
						&& $site->is_module_active( 'related-posts' );

					$jetpack_search_supported = false;
					if ( function_exists( 'wpcom_is_jetpack_search_supported' ) ) {
						$jetpack_search_supported = wpcom_is_jetpack_search_supported( $blog_id );
					}

					$jetpack_search_active =
						$jetpack_search_supported
						&& $site->is_module_active( 'search' );

					// array_values() is necessary to ensure the array starts at index 0.
					$post_categories = array_values(
						array_map(
							array( $this, 'get_category_details' ),
							get_categories( array( 'hide_empty' => false ) )
						)
					);

					$newsletter_categories   = maybe_unserialize( get_option( 'wpcom_newsletter_categories', array() ) );
					$newsletter_category_ids = array_filter(
						array_map(
							function ( $newsletter_category ) {
								if ( is_array( $newsletter_category ) && isset( $newsletter_category['term_id'] ) ) {
									// This is the expected format.
									return (int) $newsletter_category['term_id'];
								} elseif ( is_numeric( $newsletter_category ) ) {
									// This is a previous format caused by a bug.
									return (int) $newsletter_category;
								}
								return null;
							},
							$newsletter_categories
						)
					);

					$api_cache = $site->is_jetpack() ? (bool) get_option( 'jetpack_api_cache_enabled' ) : true;

					$response[ $key ] = array(
						// also exists as "options".
						'admin_url'                        => get_admin_url(),
						'default_ping_status'              => 'closed' !== get_option( 'default_ping_status' ),
						'default_comment_status'           => 'closed' !== get_option( 'default_comment_status' ),

						// new stuff starts here.
						'instant_search_enabled'           => (bool) get_option( 'instant_search_enabled' ),
						'blog_public'                      => (int) get_option( 'blog_public' ),
						'wpcom_data_sharing_opt_out'       => (bool) get_option( 'wpcom_data_sharing_opt_out' ),
						'jetpack_sync_non_public_post_stati' => (bool) Jetpack_Options::get_option( 'sync_non_public_post_stati' ),
						'jetpack_relatedposts_allowed'     => (bool) $this->jetpack_relatedposts_supported(),
						'jetpack_relatedposts_enabled'     => (bool) $jetpack_relatedposts_options['enabled'],
						'jetpack_relatedposts_show_context' => ! empty( $jetpack_relatedposts_options['show_context'] ),
						'jetpack_relatedposts_show_date'   => ! empty( $jetpack_relatedposts_options['show_date'] ),
						'jetpack_relatedposts_show_headline' => ! empty( $jetpack_relatedposts_options['show_headline'] ),
						'jetpack_relatedposts_show_thumbnails' => ! empty( $jetpack_relatedposts_options['show_thumbnails'] ),
						'jetpack_search_enabled'           => (bool) $jetpack_search_active,
						'jetpack_search_supported'         => (bool) $jetpack_search_supported,
						'default_category'                 => (int) get_option( 'default_category' ),
						'post_categories'                  => (array) $post_categories,
						'default_post_format'              => get_option( 'default_post_format' ),
						'default_pingback_flag'            => (bool) get_option( 'default_pingback_flag' ),
						'require_name_email'               => (bool) get_option( 'require_name_email' ),
						'comment_registration'             => (bool) get_option( 'comment_registration' ),
						'close_comments_for_old_posts'     => (bool) get_option( 'close_comments_for_old_posts' ),
						'close_comments_days_old'          => (int) get_option( 'close_comments_days_old' ),
						'thread_comments'                  => (bool) get_option( 'thread_comments' ),
						'thread_comments_depth'            => (int) get_option( 'thread_comments_depth' ),
						'page_comments'                    => (bool) get_option( 'page_comments' ),
						'comments_per_page'                => (int) get_option( 'comments_per_page' ),
						'default_comments_page'            => get_option( 'default_comments_page' ),
						'comment_order'                    => get_option( 'comment_order' ),
						'comments_notify'                  => (bool) get_option( 'comments_notify' ),
						'moderation_notify'                => (bool) get_option( 'moderation_notify' ),
						'social_notifications_like'        => ( 'on' === get_option( 'social_notifications_like' ) ),
						'social_notifications_reblog'      => ( 'on' === get_option( 'social_notifications_reblog' ) ),
						'social_notifications_subscribe'   => ( 'on' === get_option( 'social_notifications_subscribe' ) ),
						'comment_moderation'               => (bool) get_option( 'comment_moderation' ),
						'comment_whitelist'                => (bool) get_option( 'comment_previously_approved' ),
						'comment_previously_approved'      => (bool) get_option( 'comment_previously_approved' ),
						'comment_max_links'                => (int) get_option( 'comment_max_links' ),
						'moderation_keys'                  => get_option( 'moderation_keys' ),
						'blacklist_keys'                   => get_option( 'disallowed_keys' ),
						'disallowed_keys'                  => get_option( 'disallowed_keys' ),
						'lang_id'                          => defined( 'IS_WPCOM' ) && IS_WPCOM
						? get_lang_id_by_code( wpcom_l10n_get_blog_locale_variant( $blog_id, true ) )
						: get_option( 'lang_id' ),
						'site_vertical_id'                 => (string) get_option( 'site_vertical_id' ),
						'jetpack_cloudflare_analytics'     => get_option( 'jetpack_cloudflare_analytics' ),
						'disabled_likes'                   => (bool) get_option( 'disabled_likes' ),
						'disabled_reblogs'                 => (bool) get_option( 'disabled_reblogs' ),
						'jetpack_comment_likes_enabled'    => (bool) get_option( 'jetpack_comment_likes_enabled', false ),
						'twitter_via'                      => (string) get_option( 'twitter_via' ),
						'jetpack-twitter-cards-site-tag'   => (string) get_option( 'jetpack-twitter-cards-site-tag' ),
						'eventbrite_api_token'             => $this->get_cast_option_value_or_null( 'eventbrite_api_token', 'intval' ),
						'gmt_offset'                       => get_option( 'gmt_offset' ),
						'timezone_string'                  => get_option( 'timezone_string' ),
						'date_format'                      => get_option( 'date_format' ),
						'time_format'                      => get_option( 'time_format' ),
						'start_of_week'                    => get_option( 'start_of_week' ),
						'woocommerce_onboarding_profile'   => (array) get_option( 'woocommerce_onboarding_profile', array() ),
						'woocommerce_store_address'        => (string) get_option( 'woocommerce_store_address' ),
						'woocommerce_store_address_2'      => (string) get_option( 'woocommerce_store_address_2' ),
						'woocommerce_store_city'           => (string) get_option( 'woocommerce_store_city' ),
						'woocommerce_default_country'      => (string) get_option( 'woocommerce_default_country' ),
						'woocommerce_store_postcode'       => (string) get_option( 'woocommerce_store_postcode' ),
						'jetpack_testimonial'              => (bool) get_option( 'jetpack_testimonial', '0' ),
						'jetpack_testimonial_posts_per_page' => (int) get_option( 'jetpack_testimonial_posts_per_page', '10' ),
						'jetpack_portfolio'                => (bool) get_option( 'jetpack_portfolio', '0' ),
						'jetpack_portfolio_posts_per_page' => (int) get_option( 'jetpack_portfolio_posts_per_page', '10' ),
						'markdown_supported'               => true,
						'site_icon'                        => $this->get_cast_option_value_or_null( 'site_icon', 'intval' ),
						Jetpack_SEO_Utils::FRONT_PAGE_META_OPTION => get_option( Jetpack_SEO_Utils::FRONT_PAGE_META_OPTION, '' ),
						Jetpack_SEO_Titles::TITLE_FORMATS_OPTION => get_option( Jetpack_SEO_Titles::TITLE_FORMATS_OPTION, array() ),
						'api_cache'                        => $api_cache,
						'posts_per_page'                   => (int) get_option( 'posts_per_page' ),
						'posts_per_rss'                    => (int) get_option( 'posts_per_rss' ),
						'rss_use_excerpt'                  => (bool) get_option( 'rss_use_excerpt' ),
						'launchpad_screen'                 => (string) get_option( 'launchpad_screen' ),
						'wpcom_featured_image_in_email'    => ( function () use ( $site ) {
							if ( defined( 'IS_WPCOM' ) && IS_WPCOM ) {
								$registered_date = method_exists( $site, 'get_registered_date' ) ? $site->get_registered_date() : '';
								// Compare to May 2, 2025 (ISO 8601 format)
								if ( $registered_date && $registered_date !== '0000-00-00T00:00:00+00:00' && strtotime( $registered_date ) >= strtotime( '2025-05-02T00:00:00+00:00' ) ) {
									return (bool) get_option( 'wpcom_featured_image_in_email', true );
								}
							}
							// For all other sites, use the saved value or default to false for legacy behavior.
							return (bool) get_option( 'wpcom_featured_image_in_email', false );
						} )(),
						'jetpack_gravatar_in_email'        => (bool) get_option( 'jetpack_gravatar_in_email', true ),
						'jetpack_author_in_email'          => (bool) get_option( 'jetpack_author_in_email', true ),
						'jetpack_post_date_in_email'       => (bool) get_option( 'jetpack_post_date_in_email', true ),
						'wpcom_newsletter_categories'      => $newsletter_category_ids,
						'wpcom_newsletter_categories_enabled' => (bool) get_option( 'wpcom_newsletter_categories_enabled' ),
						'sm_enabled'                       => (bool) get_option( 'sm_enabled' ),
						'jetpack_subscribe_overlay_enabled' => (bool) get_option( 'jetpack_subscribe_overlay_enabled' ),
						'jetpack_subscribe_floating_button_enabled' => (bool) get_option( 'jetpack_subscribe_floating_button_enabled' ),
						'jetpack_subscriptions_subscribe_post_end_enabled' => (bool) get_option( 'jetpack_subscriptions_subscribe_post_end_enabled' ),
						'jetpack_subscriptions_login_navigation_enabled' => (bool) get_option( 'jetpack_subscriptions_login_navigation_enabled' ),
						'jetpack_subscriptions_subscribe_navigation_enabled' => (bool) get_option( 'jetpack_subscriptions_subscribe_navigation_enabled' ),
						'wpcom_gifting_subscription'       => (bool) get_option( 'wpcom_gifting_subscription', $this->get_wpcom_gifting_subscription_default() ),
						'wpcom_reader_views_enabled'       => (bool) get_option( 'wpcom_reader_views_enabled', true ),
						'wpcom_subscription_emails_use_excerpt' => (bool) get_option( 'wpcom_subscription_emails_use_excerpt' ),
						'jetpack_subscriptions_reply_to'   => (string) $this->get_subscriptions_reply_to_option(),
						'jetpack_subscriptions_from_name'  => (string) get_option( 'jetpack_subscriptions_from_name' ),
						'show_on_front'                    => (string) get_option( 'show_on_front' ),
						'page_on_front'                    => (string) get_option( 'page_on_front' ),
						'page_for_posts'                   => (string) get_option( 'page_for_posts' ),
						'subscription_options'             => (array) get_option( 'subscription_options' ),
						'jetpack_verbum_subscription_modal' => (bool) get_option( 'jetpack_verbum_subscription_modal', true ),
						'enable_verbum_commenting'         => (bool) get_option( 'enable_verbum_commenting', true ),
						'enable_blocks_comments'           => (bool) get_option( 'enable_blocks_comments', true ),
						'highlander_comment_form_prompt'   => $this->get_highlander_comment_form_prompt_option(),
						'jetpack_comment_form_color_scheme' => (string) get_option( 'jetpack_comment_form_color_scheme' ),
						'in_site_migration_flow'           => (string) get_option( 'in_site_migration_flow', '' ),
						'migration_source_site_domain'     => (string) get_option( 'migration_source_site_domain' ),
						'jetpack_waf_automatic_rules'      => (bool) get_option( 'jetpack_waf_automatic_rules' ),
						'jetpack_waf_ip_allow_list'        => (string) get_option( 'jetpack_waf_ip_allow_list' ),
						'jetpack_waf_ip_allow_list_enabled' => (bool) get_option( 'jetpack_waf_ip_allow_list_enabled' ),
						'jetpack_waf_ip_block_list'        => (string) get_option( 'jetpack_waf_ip_block_list' ),
						'jetpack_waf_ip_block_list_enabled' => (bool) get_option( 'jetpack_waf_ip_block_list_enabled' ),
						'jetpack_waf_share_data'           => (bool) get_option( 'jetpack_waf_share_data' ),
						'jetpack_waf_share_debug_data'     => (bool) get_option( 'jetpack_waf_share_debug_data' ),
						'jetpack_waf_automatic_rules_last_updated_timestamp' => (int) get_option( 'jetpack_waf_automatic_rules_last_updated_timestamp' ),
						'is_fully_managed_agency_site'     => (bool) get_option( 'is_fully_managed_agency_site' ),
						'wpcom_hide_action_bar'            => (bool) get_option( 'wpcom_hide_action_bar' ),
					);

					require_once JETPACK__PLUGIN_DIR . '/modules/memberships/class-jetpack-memberships.php';
					if ( class_exists( 'Jetpack_Memberships' ) ) {
						$response[ $key ]['newsletter_has_active_plan'] = count( Jetpack_Memberships::get_all_newsletter_plan_ids( false ) ) > 0;
					}

					if ( defined( 'IS_WPCOM' ) && IS_WPCOM ) {
						$response[ $key ]['wpcom_publish_posts_with_markdown']    = (bool) WPCom_Markdown::get_instance()->is_posting_enabled();
						$response[ $key ]['wpcom_publish_comments_with_markdown'] = (bool) WPCom_Markdown::get_instance()->is_commenting_enabled();

						// WPCOM-specific Infinite Scroll Settings.
						if ( is_callable( array( 'The_Neverending_Home_Page', 'get_settings' ) ) ) {
							/**
							 * Clear the cached copy of widget info so it's pulled fresh from blog options.
							 * It was primed during the initial load under the __REST API site__'s context.
							 *
							 * @see wp_get_sidebars_widgets https://core.trac.wordpress.org/browser/trunk/src/wp-includes/widgets.php?rev=42374#L931
							 */
							$GLOBALS['_wp_sidebars_widgets'] = array(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

							$infinite_scroll_settings            = The_Neverending_Home_Page::get_settings();
							$response[ $key ]['infinite_scroll'] = get_option( 'infinite_scroll', true ) && 'scroll' === $infinite_scroll_settings->type;
							if ( $infinite_scroll_settings->footer_widgets || 'click' === $infinite_scroll_settings->requested_type ) {
								// The blog has footer widgets -- infinite scroll is blocked.
								$response[ $key ]['infinite_scroll_blocked'] = 'footer';
							} else {
								$response[ $key ]['infinite_scroll_blocked'] = false;
							}
						}
					}

					// allow future versions of this endpoint to support additional settings keys.
					/**
					 * Filter the current site setting in the returned response.
					 *
					 * @module json-api
					 *
					 * @since 3.9.3
					 * @since 13.6 Added the API object parameter.
					 *
					 * @param mixed $response_item A single site setting.
					 * @param WPCOM_JSON_API_Site_Settings_Endpoint $this The API object.
					 */
					$response[ $key ] = apply_filters( 'site_settings_endpoint_get', $response[ $key ], $this );

					if ( class_exists( 'Sharing_Service' ) ) {
						$ss                                       = new Sharing_Service();
						$sharing                                  = $ss->get_global_options();
						$response[ $key ]['sharing_button_style'] = (string) $sharing['button_style'];
						$response[ $key ]['sharing_label']        = (string) $sharing['sharing_label'];
						$response[ $key ]['sharing_show']         = (array) $sharing['show'];
						$response[ $key ]['sharing_open_links']   = (string) $sharing['open_links'];
					}

					$response[ $key ]['jetpack_protect_whitelist'] = Brute_Force_Protection_Shared_Functions::format_allow_list();

					if ( ! current_user_can( 'edit_posts' ) ) {
						unset( $response[ $key ] );
					}
					break;
			}
		}
		return $response;
	}

	/**
	 * Get the default value for the wpcom_gifting_subscription option.
	 * The default value is the inverse of the plan's auto_renew setting.
	 *
	 * @return bool
	 */
	protected function get_wpcom_gifting_subscription_default() {
		if ( function_exists( 'wpcom_get_site_purchases' ) && function_exists( 'wpcom_purchase_has_feature' ) ) {
			$purchases = wpcom_get_site_purchases();

			foreach ( $purchases as $purchase ) {
				if ( wpcom_purchase_has_feature( $purchase, \WPCOM_Features::SUBSCRIPTION_GIFTING ) ) {
					/*
					 * We set default value as false when expiration date not match the following:
					 * - 54 days before the annual plan expiration.
					 * - 5 days before the monthly plan expiration.
					 * This is to match the gifting banner logic.
					 */
					$days_of_warning          = str_contains( $purchase->product_slug, 'monthly' ) ? 5 : 54;
					$seconds_until_expiration = strtotime( $purchase->expiry_date ) - time();
					if ( $seconds_until_expiration >= $days_of_warning * DAY_IN_SECONDS ) {
						return false;
					}

					// We set default to the inverse of auto-renew.
					if ( isset( $purchase->auto_renew ) ) {
						return ! $purchase->auto_renew;
					} elseif ( isset( $purchase->user_allows_auto_renew ) ) {
						return ! $purchase->user_allows_auto_renew;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Get locale.
	 *
	 * @param string $key Language.
	 */
	protected function get_locale( $key ) {
		if ( 'lang' === $key ) {
			if ( defined( 'IS_WPCOM' ) && IS_WPCOM ) {
				return (string) get_blog_lang_code();
			} else {
				return get_locale();
			}
		}

		return false;
	}

	/**
	 * Updates site settings for authorized users
	 *
	 * @return array|WP_Error
	 */
	public function update_settings() {
		/*
		 * $this->input() retrieves posted arguments whitelisted and casted to the $request_format
		 * specs that get passed in when this class is instantiated
		 */
		$input            = $this->input();
		$unfiltered_input = $this->input( false, false );
		/**
		 * Filters the settings to be updated on the site.
		 *
		 * @module json-api
		 *
		 * @since 3.6.0
		 * @since 6.1.1 Added $unfiltered_input parameter.
		 *
		 * @param array $input              Associative array of site settings to be updated.
		 *                                  Cast and filtered based on documentation.
		 * @param array $unfiltered_input   Associative array of site settings to be updated.
		 *                                  Neither cast nor filtered. Contains raw input.
		 */
		$input = apply_filters( 'rest_api_update_site_settings', $input, $unfiltered_input );

		$blog_id = get_current_blog_id();

		$jetpack_relatedposts_options = array();
		$sharing_options              = array();
		$updated                      = array();

		foreach ( $input as $key => $value ) {

			if ( ! is_array( $value ) ) {
				$value = trim( $value );
			}

			// preserve the raw value before unslashing the value. The slashes need to be preserved for date and time formats.
			$raw_value = $value;
			$value     = wp_unslash( $value );

			switch ( $key ) {

				case 'default_ping_status':
				case 'default_comment_status':
					// settings are stored as closed|open.
					$coerce_value = ( $value ) ? 'open' : 'closed';
					if ( update_option( $key, $coerce_value ) ) {
						$updated[ $key ] = $value;
					}
					break;
				case 'launchpad_screen':
					if ( in_array( $value, array( 'full', 'off', 'minimized' ), true ) ) {
						if ( update_option( $key, $value ) ) {
							$updated[ $key ] = $value;
						}
					}
					break;
				case 'jetpack_protect_whitelist':
					if ( class_exists( 'Brute_Force_Protection_Shared_Functions' ) ) {
						$result = Brute_Force_Protection_Shared_Functions::save_allow_list( $value );
						if ( is_wp_error( $result ) ) {
							return $result;
						}
						$updated[ $key ] = Brute_Force_Protection_Shared_Functions::format_allow_list();
					}
					break;
				case 'jetpack_sync_non_public_post_stati':
					Jetpack_Options::update_option( 'sync_non_public_post_stati', $value );
					break;
				case 'jetpack_search_enabled':
					if ( $value ) {
						Jetpack::activate_module( $blog_id, 'search' );
					} else {
						// @phan-suppress-next-line PhanParamTooMany -- Phan doesn't know about the WP.com variant of the Jetpack class.
						Jetpack::deactivate_module( $blog_id, 'search' );
					}
					$updated[ $key ] = (bool) $value;
					break;
				case 'jetpack_relatedposts_enabled':
				case 'jetpack_relatedposts_show_context':
				case 'jetpack_relatedposts_show_date':
				case 'jetpack_relatedposts_show_thumbnails':
				case 'jetpack_relatedposts_show_headline':
					if ( ! $this->jetpack_relatedposts_supported() ) {
						break;
					}
					if ( 'jetpack_relatedposts_enabled' === $key ) {
						if ( $value ) {
							Jetpack::activate_module( $blog_id, 'related-posts' );
						} else {
							// @phan-suppress-next-line PhanParamTooMany -- Phan doesn't know about the WP.com variant of the Jetpack class.
							Jetpack::deactivate_module( $blog_id, 'related-posts' );
						}
					}
					$just_the_key                                  = substr( $key, 21 );
					$jetpack_relatedposts_options[ $just_the_key ] = $value;
					break;

				case 'social_notifications_like':
				case 'social_notifications_reblog':
				case 'social_notifications_subscribe':
					// settings are stored as on|off.
					$coerce_value = ( $value ) ? 'on' : 'off';
					if ( update_option( $key, $coerce_value ) ) {
						$updated[ $key ] = $value;
					}
					break;

				case 'cloudflare_analytics':
					if ( ! isset( $value['code'] ) || ! preg_match( '/^$|^[a-fA-F0-9]+$/i', $value['code'] ) ) {
						return new WP_Error( 'invalid_code', __( 'Invalid Cloudflare Analytics ID', 'jetpack' ) );
					}

					if ( update_option( $key, $value ) ) {
						$updated[ $key ] = $value;
					}
					break;

				case 'jetpack_testimonial':
				case 'jetpack_portfolio':
				case 'jetpack_comment_likes_enabled':
				case 'wpcom_reader_views_enabled':
				case 'jetpack_verbum_subscription_modal':
					// settings are stored as 1|0.
					$coerce_value = (int) $value;
					if ( update_option( $key, $coerce_value ) ) {
						$updated[ $key ] = (bool) $value;
					}
					break;

				case 'jetpack_testimonial_posts_per_page':
				case 'jetpack_portfolio_posts_per_page':
					// settings are stored as numeric.
					$coerce_value = (int) $value;
					if ( update_option( $key, $coerce_value ) ) {
						$updated[ $key ] = $coerce_value;
					}
					break;

				// Sharing options.
				case 'sharing_button_style':
				case 'sharing_show':
				case 'sharing_open_links':
					$sharing_options[ preg_replace( '/^sharing_/', '', $key ) ] = $value;
					break;
				case 'sharing_label':
					$sharing_options[ $key ] = $value;
					break;

				// Keyring token option.
				case 'eventbrite_api_token':
					// These options can only be updated for sites hosted on WordPress.com.
					if ( defined( 'IS_WPCOM' ) && IS_WPCOM ) {
						if ( empty( $value ) || WPCOM_JSON_API::is_falsy( $value ) ) {
							if ( delete_option( $key ) ) {
								$updated[ $key ] = null;
							}
						} elseif ( update_option( $key, $value ) ) {
							$updated[ $key ] = (int) $value;
						}
					}
					break;

				case 'api_cache':
					if ( empty( $value ) || WPCOM_JSON_API::is_falsy( $value ) ) {
						if ( delete_option( 'jetpack_api_cache_enabled' ) ) {
							$updated[ $key ] = false;
						}
					} elseif ( update_option( 'jetpack_api_cache_enabled', true ) ) {
						$updated[ $key ] = true;
					}
					break;

				case 'timezone_string':
					/*
					 * Map UTC+- timezones to gmt_offsets and set timezone_string to empty
					 * https://github.com/WordPress/WordPress/blob/4.4.2/wp-admin/options.php#L175
					 */
					if ( ! empty( $value ) && preg_match( '/^UTC[+-]/', $value ) ) {
						$gmt_offset = preg_replace( '/UTC\+?/', '', $value );
						if ( update_option( 'gmt_offset', $gmt_offset ) ) {
							$updated['gmt_offset'] = $gmt_offset;
						}

						$value = '';
					}

					/*
					 * Always set timezone_string either with the given value or with an
					 * empty string
					 */
					if ( update_option( $key, $value ) ) {
						$updated[ $key ] = $value;
					}
					break;

				case 'subscription_options':
					if ( ! is_array( $value ) ) {
						break;
					}

					$allowed_keys   = array( 'invitation', 'comment_follow', 'welcome' );
					$filtered_value = array_filter(
						$value,
						function ( $key ) use ( $allowed_keys ) {
							return in_array( $key, $allowed_keys, true );
						},
						ARRAY_FILTER_USE_KEY
					);

					if ( empty( $filtered_value ) ) {
						break;
					}

					array_walk_recursive(
						$filtered_value,
						function ( &$value ) {
							$value = wp_kses(
								$value,
								array(
									'a' => array(
										'href' => array(),
									),
								)
							);
						}
					);

					$old_subscription_options = get_option( 'subscription_options' );
					$new_subscription_options = array_merge( $old_subscription_options, $filtered_value );

					if ( update_option( $key, $new_subscription_options ) ) {
						$updated[ $key ] = $filtered_value;
					}
					break;

				case 'woocommerce_onboarding_profile':
					// Allow boolean values but sanitize_text_field everything else.
					$sanitized_value = (array) $value;
					array_walk_recursive(
						$sanitized_value,
						function ( &$value ) {
							if ( ! is_bool( $value ) ) {
								$value = sanitize_text_field( $value );
							}
						}
					);
					if ( update_option( $key, $sanitized_value ) ) {
						$updated[ $key ] = $sanitized_value;
					}
					break;

				case 'woocommerce_store_address':
				case 'woocommerce_store_address_2':
				case 'woocommerce_store_city':
				case 'woocommerce_default_country':
				case 'woocommerce_store_postcode':
					$sanitized_value = sanitize_text_field( $value );
					if ( update_option( $key, $sanitized_value ) ) {
						$updated[ $key ] = $sanitized_value;
					}
					break;

				case 'date_format':
				case 'time_format':
					// settings are stored as strings.
					// raw_value is used to help preserve any escaped characters that might exist in the formatted string.
					$sanitized_value = sanitize_text_field( $raw_value );
					if ( update_option( $key, $sanitized_value ) ) {
						$updated[ $key ] = $sanitized_value;
					}
					break;

				case 'start_of_week':
					// setting is stored as int in 0-6 range (days of week).
					$coerce_value = (int) $value;
					$limit_value  = ( $coerce_value >= 0 && $coerce_value <= 6 ) ? $coerce_value : 0;
					if ( update_option( $key, $limit_value ) ) {
						$updated[ $key ] = $limit_value;
					}
					break;

				case 'site_icon':
					/*
					 * settings are stored as deletable numeric (all empty
					 * values as delete intent), validated as media image
					 */
					if ( empty( $value ) || WPCOM_JSON_API::is_falsy( $value ) ) {
						/**
						 * Fallback mechanism to clear a third party site icon setting. Can be used
						 * to unset the option when an API request instructs the site to remove the site icon.
						 *
						 * @module json-api
						 *
						 * @since 4.10
						 */
						if ( delete_option( $key ) || apply_filters( 'rest_api_site_icon_cleared', false ) ) {
							$updated[ $key ] = null;
						}
					} elseif ( is_numeric( $value ) ) {
						$coerce_value = (int) $value;
						if ( wp_attachment_is_image( $coerce_value ) && update_option( $key, $coerce_value ) ) {
							$updated[ $key ] = $coerce_value;
						}
					}
					break;

				case Jetpack_SEO_Utils::FRONT_PAGE_META_OPTION:
					if ( ! Jetpack_SEO_Utils::is_enabled_jetpack_seo() && ! Jetpack_SEO_Utils::has_legacy_front_page_meta() ) {
						return new WP_Error( 'unauthorized', __( 'SEO tools are not enabled for this site.', 'jetpack' ), 403 );
					}

					if ( ! is_string( $value ) ) {
						return new WP_Error( 'invalid_input', __( 'Invalid SEO meta description value.', 'jetpack' ), 400 );
					}

					$new_description = Jetpack_SEO_Utils::update_front_page_meta_description( $value );

					if ( ! empty( $new_description ) ) {
						$updated[ $key ] = $new_description;
					}
					break;

				case Jetpack_SEO_Titles::TITLE_FORMATS_OPTION:
					if ( ! Jetpack_SEO_Utils::is_enabled_jetpack_seo() ) {
						if ( Jetpack_SEO_Utils::has_legacy_front_page_meta() ) {
							break;
						}
						return new WP_Error( 'unauthorized', __( 'SEO tools are not enabled for this site.', 'jetpack' ), 403 );
					}

					if ( ! Jetpack_SEO_Titles::are_valid_title_formats( $value ) ) {
						return new WP_Error( 'invalid_input', __( 'Invalid SEO title format.', 'jetpack' ), 400 );
					}

					$new_title_formats = Jetpack_SEO_Titles::update_title_formats( $value );

					if ( ! empty( $new_title_formats ) ) {
						$updated[ $key ] = $new_title_formats;
					}
					break;

				case 'verification_services_codes':
					$verification_codes = jetpack_verification_validate( $value );

					if ( update_option( 'verification_services_codes', $verification_codes ) ) {
						$updated[ $key ] = $verification_codes;
					}
					break;

				case 'wpcom_publish_posts_with_markdown':
				case 'wpcom_publish_comments_with_markdown':
					$coerce_value = (bool) $value;
					if ( update_option( $key, $coerce_value ) ) {
						$updated[ $key ] = $coerce_value;
					}
					break;

				case 'wpcom_gifting_subscription':
					$coerce_value = (bool) $value;

					/*
					 * get_option returns a boolean false if the option doesn't exist, otherwise it always returns
					 * a serialized value. Knowing that we can check if the option already exists.
					 */
					$gift_toggle = get_option( $key );
					if ( false === $gift_toggle ) {
						// update_option will not create a new option if the initial value is false. So use add_option.
						if ( add_option( $key, $coerce_value ) ) {
							$updated[ $key ] = $coerce_value;
						}
					} elseif ( update_option( $key, $coerce_value ) ) { // If the option already exists use update_option.
						$updated[ $key ] = $coerce_value;
					}
					break;

				case 'rss_use_excerpt':
					$sanitized_value = (int) (bool) $value;
					update_option( $key, $sanitized_value );
					$updated[ $key ] = $sanitized_value;
					break;

				case 'wpcom_subscription_emails_use_excerpt':
					update_option( 'wpcom_subscription_emails_use_excerpt', (bool) $value );
					$updated[ $key ] = (bool) $value;
					break;

				case 'jetpack_subscriptions_reply_to':
					require_once JETPACK__PLUGIN_DIR . 'modules/subscriptions/class-settings.php';
					$to_set_value = Automattic\Jetpack\Modules\Subscriptions\Settings::is_valid_reply_to( $value )
						? (string) $value
						: Automattic\Jetpack\Modules\Subscriptions\Settings::$default_reply_to;

					if ( update_option( $key, $to_set_value ) ) {
						$updated[ $key ] = $to_set_value;
					}
					break;

				case 'jetpack_subscriptions_from_name':
					$sanitized_value = sanitize_text_field( $value );
					if ( update_option( $key, $sanitized_value ) ) {
						$updated[ $key ] = $sanitized_value;
					}
					break;

				case 'instant_search_enabled':
					update_option( 'instant_search_enabled', (bool) $value );
					$updated[ $key ] = (bool) $value;
					break;

				case 'lang_id':
					/*
					 * Due to the fact that locale variants are set in a locale_variant option,
					 * changing locale from variant to primary
					 * would look like the same lang_id is being saved and update_option would return false,
					 * even though the correct options would be set by pre_update_option_lang_id,
					 * so we should always return lang_id as updated.
					 */
					update_option( 'lang_id', (int) $value );
					$updated[ $key ] = (int) $value;
					break;

				case 'wpcom_featured_image_in_email':
					update_option( 'wpcom_featured_image_in_email', (int) (bool) $value );
					$updated[ $key ] = (int) (bool) $value;
					break;

				case 'wpcom_newsletter_categories':
					$sanitized_category_ids = (array) $value;

					array_walk_recursive(
						$sanitized_category_ids,
						function ( &$value ) {
							if ( is_int( $value ) && $value > 0 ) {
								return;
							}

							$value = (int) $value;
							if ( $value <= 0 ) {
								$value = null;
							}
						}
					);

					$sanitized_category_ids = array_unique(
						array_filter(
							$sanitized_category_ids,
							function ( $category_id ) {
								return $category_id !== null;
							}
						)
					);

					$new_value = array_map(
						function ( $category_id ) {
							return array( 'term_id' => $category_id );
						},
						$sanitized_category_ids
					);

					if ( update_option( $key, $new_value ) ) {
						$updated[ $key ] = $sanitized_category_ids;
					}
					break;

				case 'wpcom_newsletter_categories_enabled':
					update_option( 'wpcom_newsletter_categories_enabled', (int) (bool) $value );
					$updated[ $key ] = (int) (bool) $value;
					break;

				case 'sm_enabled':
					update_option( 'sm_enabled', (int) (bool) $value );
					$updated[ $key ] = (int) (bool) $value;
					break;

				case 'jetpack_subscribe_overlay_enabled':
					update_option( 'jetpack_subscribe_overlay_enabled', (int) (bool) $value );
					$updated[ $key ] = (int) (bool) $value;
					break;

				case 'jetpack_subscribe_floating_button_enabled':
					update_option( 'jetpack_subscribe_floating_button_enabled', (int) (bool) $value );
					$updated[ $key ] = (int) (bool) $value;
					break;

				case 'jetpack_subscriptions_subscribe_post_end_enabled':
					update_option( 'jetpack_subscriptions_subscribe_post_end_enabled', (int) (bool) $value );
					$updated[ $key ] = (int) (bool) $value;
					break;

				case 'jetpack_subscriptions_login_navigation_enabled':
					update_option( 'jetpack_subscriptions_login_navigation_enabled', (int) (bool) $value );
					$updated[ $key ] = (int) (bool) $value;
					break;

				case 'jetpack_subscriptions_subscribe_navigation_enabled':
					update_option( 'jetpack_subscriptions_subscribe_navigation_enabled', (int) (bool) $value );
					$updated[ $key ] = (int) (bool) $value;
					break;

				case 'show_on_front':
					if ( in_array( $value, array( 'page', 'posts' ), true ) && update_option( $key, $value ) ) {
							$updated[ $key ] = $value;
					}
					break;

				case 'page_on_front':
				case 'page_for_posts':
					if ( $value === '' ) { // empty function is not applicable here because '0' may be a valid page id
						if ( delete_option( $key ) ) {
							$updated[ $key ] = null;
						}

						break;
					}

					if ( ! $this->is_valid_page_id( $value ) ) {
						break;
					}

					$related_option_key   = $key === 'page_on_front' ? 'page_for_posts' : 'page_on_front';
					$related_option_value = get_option( $related_option_key );
					if ( $related_option_value === $value ) {
						// page_on_front and page_for_posts are not allowed to be the same
						break;
					}

					if ( update_option( $key, $value ) ) {
						$updated[ $key ] = $value;
					}

					break;

				case 'in_site_migration_flow':
					if ( empty( $value ) ) {
						delete_option( 'in_site_migration_flow' );
						break;
					}

					$migration_flow_whitelist = array(
						'site-migration',
						'migration-signup',
					);

					if ( ! in_array( $value, $migration_flow_whitelist, true ) ) {
						break;
					}

					update_option( 'in_site_migration_flow', $value );
					$updated[ $key ] = $value;
					break;

				case 'migration_source_site_domain':
					// If we get an empty value, delete the option
					if ( empty( $value ) ) {
						delete_option( 'migration_source_site_domain' );
						break;
					}

					// If we get a non-url value, don't update the option.
					if ( wp_http_validate_url( $value ) === false ) {
						break;
					}

					update_option( 'migration_source_site_domain', $value );
					$updated[ $key ] = $value;
					break;

				case 'is_fully_managed_agency_site':
				case 'wpcom_hide_action_bar':
					$coerce_value = (int) (bool) $value;
					if ( update_option( $key, $coerce_value ) ) {
						$updated[ $key ] = (bool) $coerce_value;
					}
					break;

				default:
					// allow future versions of this endpoint to support additional settings keys.
					if ( has_filter( 'site_settings_endpoint_update_' . $key ) ) {
						/**
						 * Filter current site setting value to be updated.
						 *
						 * @module json-api
						 *
						 * @since 3.9.3
						 * @since 13.6 Added the API object parameter.
						 *
						 * @param mixed $response_item A single site setting value.
						 * @param WPCOM_JSON_API_Site_Settings_Endpoint The API object parameter.
						 */
						$value = apply_filters( 'site_settings_endpoint_update_' . $key, $value, $this );

						if ( is_wp_error( $value ) ) {
							return $value;
						}

						if ( $value ) {
							$updated[ $key ] = $value;
						}
						break;
					}
					// no worries, we've already whitelisted and casted arguments above.
					if ( update_option( $key, $value ) ) {
						$updated[ $key ] = $value;
					}
			}
		}

		if ( $jetpack_relatedposts_options !== array() ) {
			// track new jetpack_relatedposts options against old.
			$old_relatedposts_options = Jetpack_Options::get_option( 'relatedposts' );

			$jetpack_relatedposts_options_to_save = $old_relatedposts_options;
			foreach ( $jetpack_relatedposts_options as $key => $value ) {
				$jetpack_relatedposts_options_to_save[ $key ] = $value;
			}

			if ( Jetpack_Options::update_option( 'relatedposts', $jetpack_relatedposts_options_to_save ) ) {
				foreach ( $jetpack_relatedposts_options as $key => $value ) {
					if ( in_array( $key, array( 'show_context', 'show_date' ), true ) ) {
						$has_initialized_option = ! isset( $old_relatedposts_options[ $key ] ) && $value;
						$has_updated_option     = isset( $old_relatedposts_options[ $key ] ) && $value !== $old_relatedposts_options[ $key ];

						if ( $has_initialized_option || $has_updated_option ) {
							$updated[ 'jetpack_relatedposts_' . $key ] = (bool) $value;
						}
					} elseif ( isset( $old_relatedposts_options[ $key ] ) && $value !== $old_relatedposts_options[ $key ] ) {
						$updated[ 'jetpack_relatedposts_' . $key ] = $value;
					}
				}
			}
		}

		if ( ! empty( $sharing_options ) && class_exists( 'Sharing_Service' ) ) {
			$ss = new Sharing_Service();

			/*
			 * Merge current values with updated, since Sharing_Service expects
			 * all values to be included when updating
			 */
			$current_sharing_options = $ss->get_global_options();
			foreach ( $current_sharing_options as $key => $val ) {
				if ( ! isset( $sharing_options[ $key ] ) ) {
					$sharing_options[ $key ] = $val;
				}
			}

			$updated_social_options = $ss->set_global_options( $sharing_options );

			if ( isset( $input['sharing_button_style'] ) ) {
				$updated['sharing_button_style'] = (string) $updated_social_options['button_style'];
			}
			if ( isset( $input['sharing_label'] ) ) {
				// Sharing_Service won't report label as updated if set to default.
				$updated['sharing_label'] = (string) $sharing_options['sharing_label'];
			}
			if ( isset( $input['sharing_show'] ) ) {
				$updated['sharing_show'] = (array) $updated_social_options['show'];
			}
			if ( isset( $input['sharing_open_links'] ) ) {
				$updated['sharing_open_links'] = (string) $updated_social_options['open_links'];
			}
		}

		return array(
			'updated' => $updated,
		);
	}

	/**
	 * Get the string value of the jetpack_subscriptions_reply_to option.
	 * When the option is not set, it will retun 'no-reply'.
	 *
	 * @return string
	 */
	protected function get_subscriptions_reply_to_option() {
		$reply_to = get_option( 'jetpack_subscriptions_reply_to', null );
		if ( $reply_to === null ) {
			require_once JETPACK__PLUGIN_DIR . 'modules/subscriptions/class-settings.php';
			return Automattic\Jetpack\Modules\Subscriptions\Settings::$default_reply_to;
		}
		return $reply_to;
	}

	/**
	 * Check if the given value is a valid page ID for the current site.
	 *
	 * @param mixed $value The value to check.
	 * @return bool True if the value is a valid page ID for the current site, false otherwise.
	 */
	protected function is_valid_page_id( $value ) {
		$all_page_ids = get_all_page_ids();

		$valid_page_id = false;
		foreach ( $all_page_ids as $page_id ) {
			if ( $page_id === (string) $value ) {
				$valid_page_id = true;
				break;
			}
		}

		return $valid_page_id;
	}

	/**
	 * Get the value of the highlander_comment_form_prompt option.
	 * When the option is not set, it will return the default value.
	 *
	 * @return string
	 */
	protected function get_highlander_comment_form_prompt_option() {
		$highlander_comment_form_prompt_option = get_option( 'highlander_comment_form_prompt' );

		if ( empty( $highlander_comment_form_prompt_option ) ) {
			return (string) __( 'Leave a comment', 'jetpack' );
		}

		return (string) $highlander_comment_form_prompt_option;
	}
}
