<?php

/**
 * bbPress Filters
 *
 * This file contains the filters that are used through-out bbPress. They are
 * consolidated here to make searching for them easier, and to help developers
 * understand at a glance the order in which things occur.
 *
 * There are a few common places that additional filters can currently be found
 *
 *  - bbPress: In {@link bbPress::setup_actions()} in bbpress.php
 *  - Admin: More in {@link BBP_Admin::setup_actions()} in admin.php
 *
 * @package bbPress
 * @subpackage Core
 *
 * @see /core/actions.php
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Attach bbPress to WordPress
 *
 * bbPress uses its own internal actions to help aid in third-party plugin
 * development, and to limit the amount of potential future code changes when
 * updates to WordPress core occur.
 *
 * These actions exist to create the concept of 'plugin dependencies'. They
 * provide a safe way for plugins to execute code *only* when bbPress is
 * installed and activated, without needing to do complicated guesswork.
 *
 * For more information on how this works, see the 'Plugin Dependency' section
 * near the bottom of this file.
 *
 *           v--WordPress Actions       v--bbPress Sub-actions
 */
add_filter( 'request',                 'bbp_request',            10    );
add_filter( 'template_include',        'bbp_template_include',   10    );
add_filter( 'wp_mail',                 'bbp_mail',               10, 3 );
add_filter( 'wp_title',                'bbp_title',              10, 3 );
add_filter( 'body_class',              'bbp_body_class',         10, 2 );
add_filter( 'map_meta_cap',            'bbp_map_meta_caps',      10, 4 );
add_filter( 'allowed_themes',          'bbp_allowed_themes',     10    );
add_filter( 'redirect_canonical',      'bbp_redirect_canonical', 10    );
add_filter( 'login_redirect',          'bbp_redirect_login',     2,  3 );
add_filter( 'logout_url',              'bbp_logout_url',         2,  2 );
add_filter( 'plugin_locale',           'bbp_plugin_locale',      10, 2 );

// Fix post author id for anonymous posts (set it back to 0) when the post status is changed
add_filter( 'wp_insert_post_data', 'bbp_fix_post_author', 30, 2 );

// Fix untrash post status after a topic or reply is re-instated
add_filter( 'wp_untrash_post_status', 'bbp_fix_untrash_post_status', 10, 3 );

// Force comments_status on bbPress post types
add_filter( 'comments_open', 'bbp_force_comment_status' );

// Remove forums roles from list of all roles
add_filter( 'editable_roles', 'bbp_filter_blog_editable_roles' );

// Reply title fallback
add_filter( 'the_title', 'bbp_get_reply_title_fallback', 2, 2 );

// Special title formatting
add_filter( 'protected_title_format', 'bbp_no_title_status_hints', 10, 2 );
add_filter( 'private_title_format',   'bbp_no_title_status_hints', 10, 2 );

// Avoid queries & 404s
add_filter( 'pre_handle_404',  'bbp_pre_handle_404',  10, 2 );
add_action( 'posts_pre_query', 'bbp_posts_pre_query', 10, 2 );

// User Creation
add_filter( 'signup_user_meta', 'bbp_user_add_role_to_signup_meta', 10 );

/**
 * Emails
 *
 * bbPress sends emails for a few different reasons, largely related to user
 * notifications or account changes. Because the `wp_mail` filter can be a
 * crowded space, the `bbp_mail` subfilter should be used in conjunction with
 * bbp_get_email_header() to narrow the results to only bbPress emails.
 */
add_filter( 'bbp_mail', 'bbp_chunk_emails' );

/**
 * Feeds
 *
 * bbPress comes with a number of custom RSS2 feeds that get handled outside
 * the normal scope of feeds that WordPress would normally serve. To do this,
 * we filter every page request, listen for a feed request, and trap it.
 */
add_filter( 'bbp_request', 'bbp_request_feed_trap' );

/**
 * Template Compatibility
 *
 * If you want to completely bypass this and manage your own custom bbPress
 * template hierarchy, start here by removing this filter, then look at how
 * bbp_template_include() works and do something similar. :)
 */
add_filter( 'bbp_template_include', 'bbp_template_include_theme_supports', 2, 1 );
add_filter( 'bbp_template_include', 'bbp_template_include_theme_compat',   4, 2 );

// Filter bbPress template locations
add_filter( 'bbp_get_template_stack', 'bbp_add_template_stack_locations' );

// Links
add_filter( 'paginate_links',          'bbp_add_view_all' );
add_filter( 'bbp_get_topic_permalink', 'bbp_add_view_all' );
add_filter( 'bbp_get_reply_permalink', 'bbp_add_view_all' );
add_filter( 'bbp_get_forum_permalink', 'bbp_add_view_all' );

// wp_filter_kses on new/edit forum/topic/reply title
add_filter( 'bbp_new_forum_pre_title',  'wp_filter_kses' );
add_filter( 'bbp_new_reply_pre_title',  'wp_filter_kses' );
add_filter( 'bbp_new_topic_pre_title',  'wp_filter_kses' );
add_filter( 'bbp_edit_forum_pre_title', 'wp_filter_kses' );
add_filter( 'bbp_edit_reply_pre_title', 'wp_filter_kses' );
add_filter( 'bbp_edit_topic_pre_title', 'wp_filter_kses' );

// Prevent posting malicious or malformed content on new/edit topic/reply
add_filter( 'bbp_new_reply_pre_content',  'bbp_encode_bad',  10 );
add_filter( 'bbp_new_reply_pre_content',  'bbp_code_trick',  20 );
add_filter( 'bbp_new_reply_pre_content',  'bbp_filter_kses', 30 );
add_filter( 'bbp_new_reply_pre_content',  'balanceTags',     40 );
add_filter( 'bbp_new_topic_pre_content',  'bbp_encode_bad',  10 );
add_filter( 'bbp_new_topic_pre_content',  'bbp_code_trick',  20 );
add_filter( 'bbp_new_topic_pre_content',  'bbp_filter_kses', 30 );
add_filter( 'bbp_new_topic_pre_content',  'balanceTags',     40 );
add_filter( 'bbp_new_forum_pre_content',  'bbp_encode_bad',  10 );
add_filter( 'bbp_new_forum_pre_content',  'bbp_code_trick',  20 );
add_filter( 'bbp_new_forum_pre_content',  'bbp_filter_kses', 30 );
add_filter( 'bbp_new_forum_pre_content',  'balanceTags',     40 );
add_filter( 'bbp_edit_reply_pre_content', 'bbp_encode_bad',  10 );
add_filter( 'bbp_edit_reply_pre_content', 'bbp_code_trick',  20 );
add_filter( 'bbp_edit_reply_pre_content', 'bbp_filter_kses', 30 );
add_filter( 'bbp_edit_reply_pre_content', 'balanceTags',     40 );
add_filter( 'bbp_edit_topic_pre_content', 'bbp_encode_bad',  10 );
add_filter( 'bbp_edit_topic_pre_content', 'bbp_code_trick',  20 );
add_filter( 'bbp_edit_topic_pre_content', 'bbp_filter_kses', 30 );
add_filter( 'bbp_edit_topic_pre_content', 'balanceTags',     40 );
add_filter( 'bbp_edit_forum_pre_content', 'bbp_encode_bad',  10 );
add_filter( 'bbp_edit_forum_pre_content', 'bbp_code_trick',  20 );
add_filter( 'bbp_edit_forum_pre_content', 'bbp_filter_kses', 30 );
add_filter( 'bbp_edit_forum_pre_content', 'balanceTags',     40 );

// No follow and wp_unslash on links
add_filter( 'bbp_get_reply_author_link',      'bbp_rel_nofollow' );
add_filter( 'bbp_get_reply_author_link',      'wp_unslash'       );
add_filter( 'bbp_get_reply_to_link',          'bbp_rel_nofollow' );
add_filter( 'bbp_get_reply_to_link',          'wp_unslash'       );
add_filter( 'bbp_get_topic_author_link',      'bbp_rel_nofollow' );
add_filter( 'bbp_get_topic_author_link',      'wp_unslash'       );
add_filter( 'bbp_get_topic_reply_link',       'bbp_rel_nofollow' );
add_filter( 'bbp_get_topic_reply_link',       'wp_unslash'       );
add_filter( 'bbp_get_user_favorites_link',    'bbp_rel_nofollow' );
add_filter( 'bbp_get_user_favorites_link',    'wp_unslash'       );
add_filter( 'bbp_get_user_subscribe_link',    'bbp_rel_nofollow' );
add_filter( 'bbp_get_user_subscribe_link',    'wp_unslash'       );
add_filter( 'bbp_get_user_profile_link',      'bbp_rel_nofollow' );
add_filter( 'bbp_get_user_profile_link',      'wp_unslash'       );
add_filter( 'bbp_get_user_profile_edit_link', 'bbp_rel_nofollow' );
add_filter( 'bbp_get_user_profile_edit_link', 'wp_unslash'       );
add_filter( 'bbp_get_cancel_reply_to_link',   'bbp_rel_nofollow' );
add_filter( 'bbp_get_cancel_reply_to_link',   'wp_unslash'       );

// Run filters on reply content
add_filter( 'bbp_get_reply_content', 'wptexturize',        6  );
add_filter( 'bbp_get_reply_content', 'convert_chars',      8  );
add_filter( 'bbp_get_reply_content', 'capital_P_dangit',   10 );
add_filter( 'bbp_get_reply_content', 'convert_smilies',    20 );
add_filter( 'bbp_get_reply_content', 'force_balance_tags', 30 );
add_filter( 'bbp_get_reply_content', 'bbp_make_clickable', 40 );
add_filter( 'bbp_get_reply_content', 'wpautop',            50 );
add_filter( 'bbp_get_reply_content', 'bbp_rel_nofollow',   60 );

// Run filters on topic content
add_filter( 'bbp_get_topic_content', 'wptexturize',        6  );
add_filter( 'bbp_get_topic_content', 'convert_chars',      8  );
add_filter( 'bbp_get_topic_content', 'capital_P_dangit',   10 );
add_filter( 'bbp_get_topic_content', 'convert_smilies',    20 );
add_filter( 'bbp_get_topic_content', 'force_balance_tags', 30 );
add_filter( 'bbp_get_topic_content', 'bbp_make_clickable', 40 );
add_filter( 'bbp_get_topic_content', 'wpautop',            50 );
add_filter( 'bbp_get_topic_content', 'bbp_rel_nofollow',   60 );

// Admin-only
if ( is_admin() ) {

	// Run wp_kses_data on topic/reply content in admin section
	add_filter( 'bbp_get_reply_content', 'bbp_kses_data' );
	add_filter( 'bbp_get_topic_content', 'bbp_kses_data' );

// Filters outside of wp-admin
} else {

	// WordPress 5.5.x and above
	if ( function_exists( 'wp_filter_content_tags' ) ) {

		// Responsive images
		add_filter( 'bbp_get_reply_content', 'wp_filter_content_tags', 60 );
		add_filter( 'bbp_get_topic_content', 'wp_filter_content_tags', 60 );

	// WordPress 5.4.x and below
	} else {

		// Responsive images
		add_filter( 'bbp_get_reply_content', 'wp_make_content_images_responsive', 60 );
		add_filter( 'bbp_get_topic_content', 'wp_make_content_images_responsive', 60 );
	}

	// Revisions
	add_filter( 'bbp_get_reply_content', 'bbp_reply_content_append_revisions',  99,  2 );
	add_filter( 'bbp_get_topic_content', 'bbp_topic_content_append_revisions',  99,  2 );
}

// Form textarea output - undo the code-trick done pre-save, and sanitize
add_filter( 'bbp_get_form_forum_content', 'bbp_code_trick_reverse' );
add_filter( 'bbp_get_form_forum_content', 'esc_textarea'           );
add_filter( 'bbp_get_form_forum_content', 'trim'                   );
add_filter( 'bbp_get_form_topic_content', 'bbp_code_trick_reverse' );
add_filter( 'bbp_get_form_topic_content', 'esc_textarea'           );
add_filter( 'bbp_get_form_topic_content', 'trim'                   );
add_filter( 'bbp_get_form_reply_content', 'bbp_code_trick_reverse' );
add_filter( 'bbp_get_form_reply_content', 'esc_textarea'           );
add_filter( 'bbp_get_form_reply_content', 'trim'                   );

// Form input/output - sanitize
add_filter( 'bbp_get_form_reply_edit_reason', 'esc_attr' );
add_filter( 'bbp_get_form_reply_edit_reason', 'trim'     );
add_filter( 'bbp_get_form_topic_edit_reason', 'esc_attr' );
add_filter( 'bbp_get_form_topic_edit_reason', 'trim'     );
add_filter( 'bbp_get_form_topic_title',       'esc_attr' );
add_filter( 'bbp_get_form_topic_title',       'trim'     );
add_filter( 'bbp_get_form_topic_tags',        'esc_attr' );
add_filter( 'bbp_get_form_topic_tags',        'trim'     );
add_filter( 'bbp_get_form_forum_type',        'esc_attr' );
add_filter( 'bbp_get_form_forum_type',        'trim'     );
add_filter( 'bbp_get_form_forum_visibility',  'esc_attr' );
add_filter( 'bbp_get_form_forum_visibility',  'trim'     );
add_filter( 'bbp_get_form_forum_moderators',  'esc_attr' );
add_filter( 'bbp_get_form_forum_moderators',  'trim'     );
add_filter( 'bbp_get_form_topic_forum',       'intval'   );
add_filter( 'bbp_get_form_forum_parent',      'intval'   );
add_filter( 'bbp_get_form_reply_to',          'intval'   );

// Add number format filter to functions requesting formatted values
add_filter( 'bbp_get_user_topic_count',         'bbp_number_format', 10 );
add_filter( 'bbp_get_user_reply_count',         'bbp_number_format', 10 );
add_filter( 'bbp_get_user_post_count',          'bbp_number_format', 10 );
add_filter( 'bbp_get_forum_subforum_count',     'bbp_number_format', 10 );
add_filter( 'bbp_get_forum_topic_count',        'bbp_number_format', 10 );
add_filter( 'bbp_get_forum_reply_count',        'bbp_number_format', 10 );
add_filter( 'bbp_get_forum_post_count',         'bbp_number_format', 10 );
add_filter( 'bbp_get_topic_voice_count',        'bbp_number_format', 10 );
add_filter( 'bbp_get_topic_reply_count',        'bbp_number_format', 10 );
add_filter( 'bbp_get_topic_post_count',         'bbp_number_format', 10 );
add_filter( 'bbp_get_topic_revision_count',     'bbp_number_format', 10 );
add_filter( 'bbp_get_reply_revision_count',     'bbp_number_format', 10 );
add_filter( 'bbp_get_forum_topic_count_hidden', 'bbp_number_format', 10 );
add_filter( 'bbp_get_topic_reply_count_hidden', 'bbp_number_format', 10 );

// Add number-not-negative filter to values that can never be negative numbers
add_filter( 'bbp_get_user_topic_count',             'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_user_reply_count',             'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_user_post_count',              'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_forum_subforum_count',         'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_forum_topic_count',            'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_forum_reply_count',            'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_forum_post_count',             'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_topic_voice_count',            'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_topic_reply_count',            'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_topic_post_count',             'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_forum_topic_count_hidden',     'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_topic_reply_count_hidden',     'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_topic_revision_count',         'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_reply_revision_count',         'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_user_topic_count_int',         'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_user_reply_count_int',         'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_user_post_count_int',          'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_forum_subforum_count_int',     'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_forum_topic_count_int',        'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_forum_reply_count_int',        'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_forum_post_count_int',         'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_topic_voice_count_int',        'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_topic_reply_count_int',        'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_topic_post_count_int',         'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_forum_topic_count_hidden_int', 'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_topic_reply_count_hidden_int', 'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_topic_revision_count_int',     'bbp_number_not_negative', 8 );
add_filter( 'bbp_get_reply_revision_count_int',     'bbp_number_not_negative', 8 );

// Sanitize displayed user data
add_filter( 'bbp_get_displayed_user_field', 'bbp_sanitize_displayed_user_field', 10, 3 );

// Suppress private forum details
add_filter( 'bbp_get_forum_topic_count',    'bbp_suppress_private_forum_meta',  10, 2 );
add_filter( 'bbp_get_forum_reply_count',    'bbp_suppress_private_forum_meta',  10, 2 );
add_filter( 'bbp_get_forum_post_count',     'bbp_suppress_private_forum_meta',  10, 2 );
add_filter( 'bbp_get_forum_freshness_link', 'bbp_suppress_private_forum_meta',  10, 2 );
add_filter( 'bbp_get_author_link',          'bbp_suppress_private_author_link', 10, 2 );
add_filter( 'bbp_get_topic_author_link',    'bbp_suppress_private_author_link', 10, 2 );
add_filter( 'bbp_get_reply_author_link',    'bbp_suppress_private_author_link', 10, 2 );

// Allow private & hidden forum details for moderators
add_filter( 'bbp_get_excluded_forum_ids', 'bbp_allow_forums_of_user', 10, 2 );

// Topic and reply author display names
add_filter( 'bbp_get_topic_author_display_name', 'bbp_format_user_display_name' );
add_filter( 'bbp_get_topic_author_display_name', 'wptexturize'                  );
add_filter( 'bbp_get_topic_author_display_name', 'convert_chars'                );
add_filter( 'bbp_get_topic_author_display_name', 'esc_html'                     );
add_filter( 'bbp_get_reply_author_display_name', 'bbp_format_user_display_name' );
add_filter( 'bbp_get_reply_author_display_name', 'wptexturize'                  );
add_filter( 'bbp_get_reply_author_display_name', 'convert_chars'                );
add_filter( 'bbp_get_reply_author_display_name', 'esc_html'                     );

/**
 * Add filters to anonymous post author data
 */
// Post author name
add_filter( 'bbp_pre_anonymous_post_author_name',    'trim',                10 );
add_filter( 'bbp_pre_anonymous_post_author_name',    'sanitize_text_field', 10 );
add_filter( 'bbp_pre_anonymous_post_author_name',    'wp_filter_kses',      10 );
add_filter( 'bbp_pre_anonymous_post_author_name',    '_wp_specialchars',    30 );

// Save email
add_filter( 'bbp_pre_anonymous_post_author_email',   'trim',                10 );
add_filter( 'bbp_pre_anonymous_post_author_email',   'sanitize_email',      10 );
add_filter( 'bbp_pre_anonymous_post_author_email',   'wp_filter_kses',      10 );

// Save URL
add_filter( 'bbp_pre_anonymous_post_author_website', 'trim',                10 );
add_filter( 'bbp_pre_anonymous_post_author_website', 'wp_strip_all_tags',   10 );
add_filter( 'bbp_pre_anonymous_post_author_website', 'esc_url_raw',         10 );
add_filter( 'bbp_pre_anonymous_post_author_website', 'wp_filter_kses',      10 );

// Queries
add_filter( 'posts_request', '_bbp_has_replies_where', 10, 2 );

// Capabilities
add_filter( 'bbp_map_meta_caps', 'bbp_map_primary_meta_caps',   10, 4 ); // Primary caps
add_filter( 'bbp_map_meta_caps', 'bbp_map_forum_meta_caps',     10, 4 ); // Forums
add_filter( 'bbp_map_meta_caps', 'bbp_map_topic_meta_caps',     10, 4 ); // Topics
add_filter( 'bbp_map_meta_caps', 'bbp_map_topic_tag_meta_caps', 10, 4 ); // Topic tags
add_filter( 'bbp_map_meta_caps', 'bbp_map_reply_meta_caps',     10, 4 ); // Replies

// Clickables
add_filter( 'bbp_make_clickable', 'bbp_make_urls_clickable',      2 ); // https://bbpress.org
add_filter( 'bbp_make_clickable', 'bbp_make_ftps_clickable',      4 ); // ftps://bbpress.org
add_filter( 'bbp_make_clickable', 'bbp_make_emails_clickable',    6 ); // jjj@bbpress.org
add_filter( 'bbp_make_clickable', 'bbp_make_mentions_clickable',  8 ); // @jjj

/** Deprecated ****************************************************************/

/**
 * The following filters are deprecated.
 *
 * These filters were most likely replaced by bbp_parse_args(), which includes
 * both passive and aggressive filters anywhere parse_args is used to compare
 * default arguments to passed arguments, without sprinkling the project with
 * _before_ and _after_ filters everywhere.
 */

/**
 * Deprecated locale filter
 *
 * @since 2.2.0 bbPress (r4213)
 *
 * @param string $locale
 * @return string  $domain
 */
function _bbp_filter_locale( $locale = '', $domain = '' ) {

	// Only apply to the bbPress text-domain
	if ( bbpress()->domain !== $domain ) {
		return $locale;
	}

	return apply_filters( 'bbpress_locale', $locale, $domain );
}
add_filter( 'bbp_plugin_locale', '_bbp_filter_locale', 10, 1 );

/**
 * Deprecated forums query filter
 *
 * @since 2.1.0 bbPress (r3961)
 *
 * @param array $args
 * @return array
 */
function _bbp_has_forums_query( $args = array() ) {

	// Filter & return
	return (array) apply_filters( 'bbp_has_forums_query', $args );
}
add_filter( 'bbp_after_has_forums_parse_args', '_bbp_has_forums_query' );

/**
 * Deprecated topics query filter
 *
 * @since 2.1.0 bbPress (r3961)
 *
 * @param array $args
 * @return array
 */
function _bbp_has_topics_query( $args = array() ) {

	// Filter & return
	return (array) apply_filters( 'bbp_has_topics_query', $args );
}
add_filter( 'bbp_after_has_topics_parse_args', '_bbp_has_topics_query' );

/**
 * Deprecated replies query filter
 *
 * @since 2.1.0 bbPress (r3961)
 *
 * @param array $args
 * @return array
 */
function _bbp_has_replies_query( $args = array() ) {

	// Filter & return
	return (array) apply_filters( 'bbp_has_replies_query', $args );
}
add_filter( 'bbp_after_has_replies_parse_args', '_bbp_has_replies_query' );
