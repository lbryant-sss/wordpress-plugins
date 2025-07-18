<?php
/**
 * UserRegistration Functions.
 *
 * General core functions available on both the front-end and admin.
 *
 * @package UserRegistration/Functions
 * @version 1.0.0
 */

use WPEverest\URMembership\Admin\Repositories\MembersOrderRepository;

defined( 'ABSPATH' ) || exit;

// Include core functions (available in both admin and frontend).
require UR_ABSPATH . 'includes/functions-ur-page.php';
require UR_ABSPATH . 'includes/functions-ur-account.php';
require UR_ABSPATH . 'includes/functions-ur-deprecated.php';

/**
 * Define a constant if it is not already defined.
 *
 * @param string $name  Constant name.
 * @param string $value Value.
 */
function ur_maybe_define_constant( $name, $value ) {
	if ( ! defined( $name ) ) {
		define( $name, $value );
	}
}

if ( ! function_exists( 'is_ur_endpoint_url' ) ) {

	/**
	 * Check if an endpoint is showing.
	 *
	 * @param string $endpoint User registration myaccount endpoints.
	 *
	 * @return bool
	 */
	function is_ur_endpoint_url( $endpoint = false ) {
		global $wp;

		$ur_endpoints = UR()->query->get_query_vars();

		if ( false !== $endpoint ) {
			if ( ! isset( $ur_endpoints[ $endpoint ] ) ) {
				return false;
			} else {
				$endpoint_var = $ur_endpoints[ $endpoint ];
			}

			return isset( $wp->query_vars[ $endpoint_var ] );
		} else {
			foreach ( $ur_endpoints as $key => $value ) {
				if ( isset( $wp->query_vars[ $key ] ) ) {
					return true;
				}
			}

			return false;
		}
	}
}

if ( ! function_exists( 'is_ur_account_page' ) ) {

	/**
	 * Returns true when viewing an account page.
	 *
	 * @return bool
	 */
	function is_ur_account_page() {
		/**
		 * Filter hook to modify the result of determining if the current page is an
		 * account page in user registration.
		 *
		 * @param bool $is_account_page The result of determining if the current page is
		 * a user registration account page. Default is false.
		 */
		return is_page( ur_get_page_id( 'myaccount' ) ) || ur_post_content_has_shortcode( 'user_registration_my_account' ) || apply_filters( 'user_registration_is_account_page', false );
	}
}

if ( ! function_exists( 'is_ur_login_page' ) ) {

	/**
	 * Returns true when viewing an login page.
	 *
	 * @return bool
	 */
	function is_ur_login_page() {
		/**
		 * Filter hook to modify the result of determining if the current page is an
		 * login page in user registration.
		 *
		 * @param bool $is_login_page The result of determining if the current page is
		 * a user registration login page. Default is false.
		 */
		return is_page( ur_get_page_id( 'login' ) ) || ur_post_content_has_shortcode( 'user_registration_login' ) || apply_filters( 'user_registration_is_login_page', false );
	}
}

if ( ! function_exists( 'is_ur_edit_account_page' ) ) {

	/**
	 * Check for edit account page.
	 * Returns true when viewing the edit account page.
	 *
	 * @return bool
	 */
	function is_ur_edit_account_page() {
		global $wp;

		return ( is_ur_account_page() && isset( $wp->query_vars['edit-password'] ) );
	}
}

if ( ! function_exists( 'is_ur_lost_password_page' ) ) {

	/**
	 * Returns true when viewing the lost password page.
	 *
	 * @return bool
	 */
	function is_ur_lost_password_page() {
		global $wp;

		$lost_password_page_id = get_option( 'user_registration_lost_password_page_id', false );

		return ( is_ur_account_page() && isset( $wp->query_vars['ur-lost-password'] ) ) || is_page( $lost_password_page_id );
	}
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param  string|array $var Variable.
 *
 * @return string|array
 */
function ur_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'ur_clean', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

/**
 * Sanitize a string destined to be a tooltip.
 *
 * @since  1.0.0  Tooltips are encoded with htmlspecialchars to prevent XSS. Should not be used in conjunction with esc_attr()
 *
 * @param  string $var Value to sanitize.
 *
 * @return string
 */
function ur_sanitize_tooltip( $var ) {
	return htmlspecialchars(
		wp_kses(
			html_entity_decode( $var ),
			array(
				'br'     => array(),
				'em'     => array(),
				'strong' => array(),
				'small'  => array(),
				'span'   => array(),
				'ul'     => array(),
				'li'     => array(),
				'ol'     => array(),
				'p'      => array(),
			)
		)
	);
}

/**
 * Format dimensions for display.
 *
 * @since  1.7.0
 * @param  array $dimensions Array of dimensions.
 * @param  array $unit Unit, defaults to 'px'.
 * @return string
 */
function ur_sanitize_dimension_unit( $dimensions = array(), $unit = 'px' ) {
	return ur_array_to_string( ur_suffix_array( $dimensions, $unit ) );
}

/**
 * Add a suffix into an array.
 *
 * @since  1.7.0
 * @param  array  $array  Raw array data.
 * @param  string $suffix Suffix to be added.
 * @return array Modified array with suffix added.
 */
function ur_suffix_array( $array = array(), $suffix = '' ) {
	return preg_filter( '/$/', $suffix, $array );
}
/**
 * Implode an array into a string by $glue and remove empty values.
 *
 * @since  1.7.0
 * @param  array  $array Array to convert.
 * @param  string $glue  Glue, defaults to ' '.
 * @return string
 */
function ur_array_to_string( $array = array(), $glue = ' ' ) {
	return is_string( $array ) ? $array : implode( $glue, array_filter( $array ) );
}
/**
 * Explode a string into an array by $delimiter and remove empty values.
 *
 * @since  1.7.0
 * @param  string $string    String to convert.
 * @param  string $delimiter Delimiter, defaults to ','.
 * @return array
 */
function ur_string_to_array( $string, $delimiter = ',' ) {
	return is_array( $string ) ? $string : array_filter( explode( $delimiter, $string ) );
}

/**
 * Converts a string (e.g. 'yes' or 'no') to a bool.
 *
 * @param string $string String to convert.
 * @return bool
 */
function ur_string_to_bool( $string ) {
	return is_bool( $string ) ? $string : ( ( 'yes' === $string || 'on' === $string || 1 === $string || 'true' === $string || '1' === $string || 'today' === $string || 'range' === $string ) ? true : ( null === $string ? '0' : false ) );
}

/**
 * Converts a bool to a 'yes' or 'no'.
 *
 * @param bool $bool String to convert.
 * @return string
 */
function ur_bool_to_string( $bool ) {
	if ( ! is_bool( $bool ) ) {
		$bool = ur_string_to_bool( $bool );
	}
	return true === $bool ? 'yes' : 'no';
}

/**
 * Get other templates (e.g. my account) passing attributes and including the file.
 *
 * @param string $template_name Template Name.
 * @param array  $args Extra arguments(default: array()).
 * @param string $template_path Path of template provided (default: '').
 * @param string $default_path  Default path of template provided(default: '').
 */
function ur_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args ); // phpcs:ignore.
	}

	$located = ur_locate_template( $template_name, $template_path, $default_path );

	/** Allow 3rd party plugin filter template file from their plugin.
	 *
	 * @param string $located Template locate.
	 * @param string $template_name Template Name.
	 * @param array  $args Extra arguments(default: array()).
	 * @param string $template_path Path of template provided (default: '').
	 * @param string $default_path  Default path of template provided(default: '').
	 */
	$located = apply_filters( 'ur_get_template', $located, $template_name, $args, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', esc_html( $located ) ), '1.0' );

		return;
	}

	ob_start();
	/**
	 * Executes an action before including a template part.
	 *
	 * @param string $template_name Name of the template part.
	 * @param string $template_path Path to the template part.
	 * @param string $located Path to the located template file.
	 * @param array $args Additional arguments passed to the template part.
	 */
	do_action( 'user_registration_before_template_part', $template_name, $template_path, $located, $args );

	include $located;
	/**
	 * Executes an action after including a template part.
	 *
	 * @param string $template_name Name of the template part.
	 * @param string $template_path Path to the template part.
	 * @param string $located Path to the located template file.
	 * @param array $args Additional arguments passed to the template part.
	 */
	do_action( 'user_registration_after_template_part', $template_name, $template_path, $located, $args );
	$template_content = ob_get_clean();
	/**
	 * Filter hook to process the smart tags in the template content.
	 *
	 * @param string $template_content The template content.
	 */
	$template_content = apply_filters( 'user_registration_process_smart_tags', $template_content, array(), array() );
	echo $template_content;  // phpcs:ignore.
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *        yourtheme        /    $template_path    /    $template_name
 *        yourtheme        /    $template_name
 *        $default_path    /    $template_name
 *
 * @param string $template_name Template Name.
 * @param string $template_path Path of template provided (default: '').
 * @param string $default_path  Default path of template provided(default: '').
 *
 * @return string
 */
function ur_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = UR()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = UR()->plugin_path() . '/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		)
	);

	// Get default template.
	if ( ! $template || UR_TEMPLATE_DEBUG_MODE ) {
		$template = $default_path . $template_name;
	}

	/**
	 * Filters the located template file path before including it.
	 *
	 * @param string $template       The located template file path.
	 * @param string $template_name  The name of the template file.
	 * @param string $template_path  The path to the template file.
	 */
	return apply_filters( 'user_registration_locate_template', $template, $template_name, $template_path );
}

/**
 * Display a UserRegistration help tip.
 *
 * @param  string $tip        Help tip text.
 * @param  bool   $allow_html Allow sanitized HTML if true or escape.
 * @param string $classname Classname.
 *
 * @return string
 */
function ur_help_tip( $tip, $allow_html = false, $classname = 'user-registration-help-tip' ) {
	if ( $allow_html ) {
		$tip = ur_sanitize_tooltip( $tip );
	} else {
		$tip = esc_attr( $tip );
	}

	return sprintf( '<span class="%s" data-tip="%s"></span>', $classname, $tip );
}

/**
 * Checks whether the content passed contains a specific short code.
 *
 * @param  string $tag Shortcode tag to check.
 *
 * @return bool
 */
function ur_post_content_has_shortcode( $tag = '' ) {
	global $post;
	$new_shortcode = '';
	$wp_version    = '5.0';
	if ( version_compare( $GLOBALS['wp_version'], $wp_version, '>=' ) ) {
		if ( is_object( $post ) ) {
			$blocks = parse_blocks( $post->post_content );
			foreach ( $blocks as $block ) {

				if ( ( 'core/shortcode' === $block['blockName'] || 'core/paragraph' === $block['blockName'] ) && isset( $block['innerHTML'] ) ) {
					$new_shortcode = ( 'core/shortcode' === $block['blockName'] ) ? $block['innerHTML'] : wp_strip_all_tags( $block['innerHTML'] );
				} elseif ( 'user-registration/form-selector' === $block['blockName'] && isset( $block['attrs']['shortcode'] ) ) {
					$new_shortcode = '[' . $block['attrs']['shortcode'] . ']';
				}
			}
		}
		return ( is_singular() || is_front_page() ) && is_a( $post, 'WP_Post' ) && has_shortcode( $new_shortcode, $tag );
	} else {
		return ( is_singular() || is_front_page() ) && is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, $tag );
	}
}

/**
 * Wrapper for ur_doing_it_wrong.
 *
 * @since  1.0.0
 *
 * @param  string $function Callback function name.
 * @param  string $message Message to display.
 * @param  string $version Version of the plugin.
 */
function ur_doing_it_wrong( $function, $message, $version ) {
	$message .= ' Backtrace: ' . wp_debug_backtrace_summary();

	if ( defined( 'DOING_AJAX' ) ) {
		/**
		 * The 'doing_it_wrong_run' action is triggered when the function is called incorrectly.
		 *
		 * @param string $function The function that was called incorrectly.
		 * @param string $message Error message providing details about the incorrect usage.
		 * @param string $version The version when the incorrect usage was introduced.
		 */
		do_action( 'doing_it_wrong_run', $function, $message, $version );
		error_log( "{$function} was called incorrectly. {$message}. This message was added in version {$version}." );
	} else {
		_doing_it_wrong( esc_html( $function ), esc_html( $message ), esc_html( $version ) );
	}
}

/**
 * Set a cookie - wrapper for setcookie using WP constants.
 *
 * @param  string  $name   Name of the cookie being set.
 * @param  string  $value  Value of the cookie.
 * @param  integer $expire Expiry of the cookie.
 * @param  string  $secure Whether the cookie should be served only over https.
 */
function ur_setcookie( $name, $value, $expire = 0, $secure = false ) {
	if ( ! headers_sent() ) {
		setcookie( $name, $value, $expire, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, $secure );
	} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		headers_sent( $file, $line );
		trigger_error( "{$name} cookie cannot be set - headers already sent by {$file} on line {$line}", E_USER_NOTICE ); //phpcs:ignore.
	}
}

/**
 * Read in UserRegistration headers when reading plugin headers.
 *
 * @since  1.1.0
 *
 * @param  array $headers header.
 *
 * @return array $headers
 */
function ur_enable_ur_plugin_headers( $headers ) {
	if ( ! class_exists( 'UR_Plugin_Updates', false ) ) {
		include_once __DIR__ . '/admin/updater/class-ur-plugin-updates.php';
	}

	$headers['URRequires'] = UR_Plugin_Updates::VERSION_REQUIRED_HEADER;
	$headers['URTested']   = UR_Plugin_Updates::VERSION_TESTED_HEADER;

	return $headers;
}

add_filter( 'extra_plugin_headers', 'ur_enable_ur_plugin_headers' );

/**
 * Set field type for all registrered field keys
 *
 * @param  string $field_key field's field key.
 * @return string $field_type
 */
function ur_get_field_type( $field_key ) {
	$fields = ur_get_registered_form_fields();
	if ( ur_check_module_activation( 'coupon' ) ) {
		$fields[] = 'coupon';
	}

	$field_type = 'text';

	if ( in_array( $field_key, $fields ) ) {

		switch ( $field_key ) {

			case 'user_email':
			case 'user_confirm_email':
			case 'email':
				$field_type = 'email';
				break;
			case 'user_confirm_password':
			case 'password':
			case 'user_pass':
				$field_type = 'password';
				break;
			case 'user_login':
			case 'nickname':
			case 'first_name':
			case 'last_name':
			case 'display_name':
			case 'text':
				$field_type = 'text';
				break;
			case 'user_url':
				$field_type = 'url';
				break;
			case 'description':
			case 'textarea':
				$field_type = 'textarea';
				break;
			case 'select':
			case 'country':
				$field_type = 'select';
				break;
			case 'file':
				$field_type = 'file';
				break;
			case 'privacy_policy':
			case 'mailchimp':
			case 'mailerlite':
			case 'checkbox':
				$field_type = 'checkbox';
				break;
			case 'number':
				$field_type = 'number';
				break;
			case 'date':
				$field_type = 'date';
				break;
			case 'radio':
				$field_type = 'radio';
				break;
			case 'coupon':
				$field_type = 'coupon';
				break;
		}
	}
	/**
	 * Filters the field keys before rendering or processing.
	 *
	 * @param string $field_type The type of the user registration field.
	 * @param string $field_key  The key identifying the specific field.
	 */
	return apply_filters( 'user_registration_field_keys', $field_type, $field_key );
}

/**
 * Get user table fields.
 *
 * @return array
 */
function ur_get_user_table_fields() {
	/**
	 * Filters the user table fields before rendering or processing.
	 *
	 * @param array $user_table_fields An array of user table fields to be displayed
	 * or processed during user registration.
	 */
	return apply_filters(
		'user_registration_user_table_fields',
		array(
			'user_email',
			'user_pass',
			'user_login',
			'user_url',
			'display_name',
		)
	);
}

/**
 * Get required fields.
 *
 * @return array
 */
function ur_get_required_fields() {
	/**
	 * Filters the list of required form fields during user registration.
	 *
	 * @param array $required_form_fields An array of user fields that are required.
	 */
	return apply_filters(
		'user_registration_required_form_fields',
		array(
			'user_email',
			'user_pass',
		)
	);
}

/**
 * Get one time draggable fields fields.
 *
 * @return array
 */
function ur_get_one_time_draggable_fields() {
	$form_fields = ur_get_user_field_only();
	/**
	 * Filters the list of one-time draggable form fields during user registration.
	 *
	 * @param array $form_fields An array of user fields to be used as one-time draggable form fields.
	 */
	return apply_filters( 'user_registration_one_time_draggable_form_fields', $form_fields );
}

/**
 * Get fields excluding in profile tab
 *
 * @return array
 */
function ur_exclude_profile_details_fields() {

	$fields_to_exclude = array(
		'user_pass',
		'user_confirm_password',
		'user_confirm_email',
		'invite_code',
		'learndash_course',
	);

	// Check if the my account page contains [user_registration_my_account] shortcode.
	if ( ur_post_content_has_shortcode( 'user_registration_my_account' ) || ur_post_content_has_shortcode( 'user_registration_edit_profile' ) ) {
		// Push profile_picture field to fields_to_exclude array.
		array_push( $fields_to_exclude, 'profile_picture' );
	}
	/**
	 * Filters the list of profile fields to be excluded during user registration.
	 *
	 * @param array $fields_to_exclude An array of profile fields to be excluded.
	 */
	return apply_filters(
		'user_registration_exclude_profile_fields',
		$fields_to_exclude
	);
}

/**
 * Get readonly fields in profile tab
 *
 * @return array
 */
function ur_readonly_profile_details_fields() {
	/**
	 * Filters the list of readonly profile fields during user registration.
	 *
	 * @param array $readonly_profile_fields An associative array where keys are the
	 *                                       profile fields to be marked as readonly,
	 *                                       and values are arrays containing optional
	 *                                       messages or values associated with each field.
	 */
	return apply_filters(
		'user_registration_readonly_profile_fields',
		array(
			'user_login'            => array(
				'message' => __( 'Username can not be changed.', 'user-registration' ),
			),
			'user_pass'             => array(
				'value'   => 'password',
				'message' => __( 'Passowrd can not be changed.', 'user-registration' ),
			),
			'user_confirm_password' => array(
				'value'   => 'password',
				'message' => __( 'Confirm password can not be changed.', 'user-registration' ),
			),
			'user_confirm_email'    => array(
				'message' => __( 'Confirm email can not be changed.', 'user-registration' ),
			),
		)
	);
}

/**
 * Get profile detail fields.
 *
 * @deprecated 1.4.1
 * @return void
 */
function ur_get_account_details_fields() {
	ur_deprecated_function( 'ur_get_account_details_fields', '1.4.1', 'ur_exclude_profile_details_fields' );
}

/**
 * Get all fields appearing in profile tab.
 *
 * @return array
 */
function ur_get_user_profile_field_only() {
	$user_fields = array_diff( ur_get_registered_form_fields(), ur_exclude_profile_details_fields() );
	/**
	 * Filters the list of user profile fields during user registration.
	 *
	 * @param array $user_fields An array of user profile fields to be used during user registration.
	 */
	return apply_filters( 'user_registration_user_profile_field_only', $user_fields );
}

/**
 * All fields to update without adding prefix.
 *
 * @return array
 */
function ur_get_fields_without_prefix() {
	$fields = ur_get_user_field_only();
	/**
	 * Filters the list of user registration fields without the field prefix.
	 *
	 * @param array $fields An array of user registration fields without the field prefix.
	 */
	return apply_filters( 'user_registration_fields_without_prefix', $fields );
}

/**
 * Get all default fields by WordPress.
 *
 * @return array
 */
function ur_get_user_field_only() {
	/**
	 * Filters the list of user form fields during user registration.
	 *
	 * @param array $user_form_fields An array of user form fields to be used during user registration.
	 */
	return apply_filters(
		'user_registration_user_form_fields',
		array(
			'user_email',
			'user_confirm_email',
			'user_pass',
			'user_confirm_password',
			'user_login',
			'nickname',
			'first_name',
			'last_name',
			'user_url',
			'display_name',
			'description',
		)
	);
}

/**
 * Get all extra form fields
 *
 * @return array
 */
function ur_get_other_form_fields() {
	$registered  = ur_get_registered_form_fields();
	$user_fields = ur_get_user_field_only();
	$result      = array_diff( $registered, $user_fields );
	/**
	 * Filters the list of other form fields during user registration.
	 *
	 * @param mixed $result The result of processing other form fields during user registration.
	 */
	return apply_filters( 'user_registration_other_form_fields', $result );
}

/**
 * All default fields storing in usermeta table
 *
 * @return mixed|array
 */
function ur_get_registered_user_meta_fields() {
	/**
	 * Filters the list of user meta fields for a registered user during user registration.
	 *
	 * @param array $registered_user_meta_fields An array of user meta fields associated with a registered user.
	 */
	return apply_filters(
		'user_registration_registered_user_meta_fields',
		array(
			'nickname',
			'first_name',
			'last_name',
			'description',
		)
	);
}

if ( ! function_exists( 'ur_get_field_name_with_prefix_usermeta' ) ) {
	/**
	 * Returns user registration meta fields with prefix before registration.
	 *
	 * @param string $field_name Field name.
	 *
	 * @return string
	 */
	function ur_get_field_name_with_prefix_usermeta( $field_name ) {
		$default_fields = array_merge_recursive( ur_get_user_table_fields(), ur_get_registered_user_meta_fields() );
		if ( ! in_array( $field_name, $default_fields ) ) {
			$field_name = 'user_registration_' . $field_name;
		}
		return $field_name;
	}
}

/**
 * All registered form fields
 *
 * @return mixed|array
 */
function ur_get_registered_form_fields() {
	/**
	 * Filters the list of form fields for a registered user during user registration.
	 *
	 * @param array $registered_form_fields An array of form fields associated with a registered user during registration.
	 */
	return apply_filters(
		'user_registration_registered_form_fields',
		array(
			'user_email',
			'user_confirm_email',
			'user_pass',
			'user_confirm_password',
			'user_login',
			'nickname',
			'first_name',
			'last_name',
			'user_url',
			'display_name',
			'description',
			'text',
			'password',
			'email',
			'select',
			'country',
			'textarea',
			'number',
			'date',
			'checkbox',
			'privacy_policy',
			'radio',
		)
	);
}

/**
 * All registered form fields with default labels
 *
 * @return mixed|array
 */
function ur_get_registered_form_fields_with_default_labels() {
	/**
	 * Filters the list of form fields for a registered user with default labels during user registration.
	 *
	 * @param array $registered_form_fields_with_labels An associative array where keys
	 *                                                  are form field keys, and values are
	 *                                                  the corresponding default labels.
	 */
	return apply_filters(
		'user_registration_registered_form_fields_with_default_labels',
		array(
			'user_email'            => __( 'User Email', 'user-registration' ),
			'user_confirm_email'    => __( 'User Confirm Email', 'user-registration' ),
			'user_pass'             => __( 'User Pass', 'user-registration' ),
			'user_confirm_password' => __( 'User Confirm Password', 'user-registration' ),
			'user_login'            => __( 'User Login', 'user-registration' ),
			'nickname'              => __( 'Nickname', 'user-registration' ),
			'first_name'            => __( 'First Name', 'user-registration' ),
			'last_name'             => __( 'Last Name', 'user-registration' ),
			'user_url'              => __( 'User URL', 'user-registration' ),
			'display_name'          => __( 'Display Name', 'user-registration' ),
			'description'           => __( 'Description', 'user-registration' ),
			'text'                  => __( 'Text', 'user-registration' ),
			'password'              => __( 'Password', 'user-registration' ),
			'email'                 => __( 'Secondary Email', 'user-registration' ),
			'select'                => __( 'Select', 'user-registration' ),
			'country'               => __( 'Country', 'user-registration' ),
			'textarea'              => __( 'Textarea', 'user-registration' ),
			'number'                => __( 'Number', 'user-registration' ),
			'date'                  => __( 'Date', 'user-registration' ),
			'checkbox'              => __( 'Checkbox', 'user-registration' ),
			'privacy_policy'        => __( 'Privacy Policy', 'user-registration' ),
			'radio'                 => __( 'Radio', 'user-registration' ),
			'hidden'                => __( 'Hidden', 'user-registration' ),
		)
	);
}

/**
 * General settings for each fields
 *
 * @param string $id id for each field.
 * @return mixed|array
 */
function ur_get_general_settings( $id ) {

	$general_settings = array(
		'label'       => array(
			'setting_id'  => 'label',
			'type'        => 'text',
			'label'       => __( 'Label', 'user-registration' ),
			'name'        => 'ur_general_setting[label]',
			'placeholder' => __( 'Label', 'user-registration' ),
			'required'    => true,
			'tip'         => __( 'Enter text for the form field label. This is recommended and can be hidden in the Advanced Settings.', 'user-registration' ),
		),
		'description' => array(
			'setting_id'  => 'description',
			'type'        => 'textarea',
			'label'       => __( 'Description', 'user-registration' ),
			'name'        => 'ur_general_setting[description]',
			'placeholder' => __( 'Description', 'user-registration' ),
			'required'    => true,
			'tip'         => __( 'Enter text for the form field description.', 'user-registration' ),
		),
		'field_name'  => array(
			'setting_id'  => 'field-name',
			'type'        => 'text',
			'label'       => __( 'Field Name', 'user-registration' ),
			'name'        => 'ur_general_setting[field_name]',
			'placeholder' => __( 'Field Name', 'user-registration' ),
			'required'    => true,
			'tip'         => __( 'Unique key for the field.', 'user-registration' ),
		),

		'placeholder' => array(
			'setting_id'  => 'placeholder',
			'type'        => 'text',
			'label'       => __( 'Placeholder', 'user-registration' ),
			'name'        => 'ur_general_setting[placeholder]',
			'placeholder' => __( 'Placeholder', 'user-registration' ),
			'required'    => true,
			'tip'         => __( 'Enter placeholder for the field.', 'user-registration' ),
		),
		'required'    => array(
			'setting_id'  => 'required',
			'type'        => 'toggle',
			'label'       => __( 'Required', 'user-registration' ),
			'name'        => 'ur_general_setting[required]',
			'placeholder' => '',
			'required'    => true,
			'default'     => 'false',
			'tip'         => __( 'Check this option to mark the field required. A form will not submit unless all required fields are provided.', 'user-registration' ),
		),
		'hide_label'  => array(
			'setting_id'  => 'hide-label',
			'type'        => 'toggle',
			'label'       => __( 'Hide Label', 'user-registration' ),
			'name'        => 'ur_general_setting[hide_label]',
			'placeholder' => '',
			'required'    => true,
			'default'     => 'false',
			'tip'         => __( 'Check this option to hide the label of this field.', 'user-registration' ),
		),
	);
	/**
	 * Filters the list of form field types to exclude placeholders.
	 *
	 * @param array $exclude_placeholder_fields An array of form field types to exclude placeholders.
	 */
	$exclude_placeholder = apply_filters(
		'user_registration_exclude_placeholder',
		array(
			'checkbox',
			'privacy_policy',
			'radio',
			'file',
			'mailchimp',
			'hidden',
			'signature',
		)
	);
	$strip_id            = str_replace( 'user_registration_', '', $id );

	if ( in_array( $strip_id, $exclude_placeholder, true ) ) {
		unset( $general_settings['placeholder'] );
	}

	$choices_fields = array( 'radio', 'select', 'checkbox' );

	if ( in_array( $strip_id, $choices_fields, true ) ) {

		$settings['options'] = array(
			'setting_id'  => 'options',
			'type'        => 'checkbox' === $strip_id ? 'checkbox' : 'radio',
			'label'       => __( 'Options', 'user-registration' ),
			'name'        => 'ur_general_setting[options]',
			'placeholder' => '',
			'required'    => true,
			'options'     => array(
				__( 'First Choice', 'user-registration' ),
				__( 'Second Choice', 'user-registration' ),
				__( 'Third Choice', 'user-registration' ),
			),
		);

		$general_settings = ur_insert_after_helper( $general_settings, $settings, 'field_name' );
	}
	if ( 'privacy_policy' === $strip_id || 'user_confirm_email' === $strip_id || 'user_confirm_password' === $strip_id || in_array( $strip_id, ur_get_required_fields() ) ) {
		$general_settings['required'] = array(
			'setting_id'  => '',
			'type'        => 'hidden',
			'label'       => '',
			'name'        => 'ur_general_setting[required]',
			'placeholder' => '',
			'default'     => true,
			'required'    => true,
		);
	}
	/**
	 * Filters the general settings for a specific field type during user registration.
	 *
	 * @param array $general_settings An array of general settings/options for a specific
	 *                                field type during user registration.
	 * @param string $id              The identifier for the specific field type.
	 */
	return apply_filters( 'user_registration_field_options_general_settings', $general_settings, $id );
}

/**
 * Insert in between the indexes in multidimensional array.
 *
 * @since  1.5.7
 * @param  array  $items      An array of items.
 * @param  array  $new_items  New items to insert inbetween.
 * @param  string $after      Index to insert after.
 *
 * @return array              Ordered array of items.
 */
function ur_insert_after_helper( $items, $new_items, $after ) {

	// Search for the item position and +1 since is after the selected item key.
	$position = array_search( $after, array_keys( $items ), true ) + 1;

	// Insert the new item.
	$return_items  = array_slice( $items, 0, $position, true );
	$return_items += $new_items;
	$return_items += array_slice( $items, $position, count( $items ) - $position, true );

	return $return_items;
}

/**
 * Load form field class.
 *
 * @param string $class_key Class Key.
 */
function ur_load_form_field_class( $class_key ) {
	$exploded_class = explode( '_', $class_key );
	$class_path     = UR_FORM_PATH . 'class-ur-' . join( '-', array_map( 'strtolower', $exploded_class ) ) . '.php';
	$class_name     = 'UR_Form_Field_' . join( '_', array_map( 'ucwords', $exploded_class ) );
	/**
	 * Filter the path of the form field class file and class name before loading.
	 *
	 * Dynamic portion of hook name, $class_key.
	 *
	 * @param string $class_path The path to the form field class file.
	 * @param string $class_key  The key identifying the form field class.
	 */
	$class_path = apply_filters( 'user_registration_form_field_' . $class_key . '_path', $class_path );
	/* Backward Compat since 1.4.0 */
	if ( null != $class_path && file_exists( $class_path ) ) {
		$class_name = 'UR_' . join( '_', array_map( 'ucwords', $exploded_class ) );
		if ( ! class_exists( $class_name ) ) {
			include_once $class_path;
		}
	}
	/* Backward compat end*/
	return $class_name;
}

/**
 * List of all roles
 *
 * @return array $all_roles
 */
function ur_get_default_admin_roles() {
	global $wp_roles;

	if ( ! class_exists( 'WP_Roles' ) ) {
		return;
	}

	if ( ! isset( $wp_roles ) ) {
		$wp_roles = new WP_Roles(); // @codingStandardsIgnoreLine
	}

	$roles     = isset( $wp_roles->roles ) ? $wp_roles->roles : array();
	$all_roles = array();

	foreach ( $roles as $role_key => $role ) {
		$all_roles[ $role_key ] = $role['name'];
	}
	/**
	 * Filters the default user roles available.
	 *
	 * @param array $all_roles An array of all available user roles.
	 */
	return apply_filters( 'user_registration_user_default_roles', $all_roles );
}


/**
 * Random number generated by time()
 *
 * @return int
 */
function ur_get_random_number() {
	return time();
}

/**
 * General Form settings
 *
 * @param int $form_id  Form ID.
 *
 * @since 1.0.1
 *
 * @return array Form settings.
 */
function ur_admin_form_settings_fields( $form_id ) {

	$all_roles = ur_get_default_admin_roles();

	$ur_captchas         = ur_get_captcha_integrations();
	$ur_enabled_captchas = array(
		'' => __( 'Select Enabled Captcha', 'user-registration' ),
	);

	foreach ( $ur_captchas as $key => $value ) {
		if ( get_option( 'user_registration_captcha_setting_recaptcha_enable_' . $key, false ) ) {
			$ur_enabled_captchas[ $key ] = $value;
		}
	}

	$arguments = array(
		'form_id'      => $form_id,

		'setting_data' => array(
			array(
				'type'              => 'toggle',
				'label'             => __( 'Enable form title and description', 'user-registration' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_enable_form_title_description',
				'class'             => array( 'ur-enhanced-select' ),
				'input_class'       => array(),
				'custom_attributes' => array(),
				'default'           => ur_string_to_bool( ur_get_single_post_meta( $form_id, 'user_registration_enable_form_title_description', false ) ),
				'tip'               => __( 'Enable to show form title and description on form', 'user-registration' ),
			),
			array(
				'type'              => 'text',
				'label'             => __( 'Form Title', 'user-registration' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_title',
				'class'             => array( 'ur-enhanced-select' ),
				'input_class'       => array(),
				'custom_attributes' => array(),
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_title', __('Register' , 'user-registration') ),
				'tip'               => __( 'Enter the title of the form.', 'user-registration' ),
			),
			array(
				'type'              => 'textarea',
				'label'             => __( 'Form Description', 'user-registration' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_description',
				'class'             => array( 'ur-enhanced-select' ),
				'input_class'       => array(),
				'custom_attributes' => array(),
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_description', __('Fill the form below to create an account.' , 'user-registration') ),
				'tip'               => __( 'Enter the description of the form.', 'user-registration' ),
			),
			array(
				'label'             => __( 'User Approval And Login Option', 'user-registration' ),
				'description'       => __( 'This option lets you choose login option after user registration.', 'user-registration' ),
				'id'                => 'user_registration_form_setting_login_options',
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_login_options', get_option( 'user_registration_general_setting_login_options' ) ),
				'type'              => 'select',
				'class'             => array( 'ur-enhanced-select' ),
				'custom_attributes' => array(),
				'input_class'       => array(),
				'required'          => false,
				'options'           => ur_login_option(),
				'tip'               => __( 'Login method that should be used by the users registered through this form.', 'user-registration' ),
			),
			array(
				'label'             => __( 'Select Phone Fields for SMS Verification', 'user-registration' ),
				'description'       => '',
				'id'                => 'user_registration_form_setting_default_phone_field',
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_default_phone_field', '' ),
				'type'              => 'select',
				'class'             => array( 'ur-enhanced-select' ),
				'custom_attributes' => array(),
				'input_class'       => array(),
				'required'          => false,
				'options'           => user_registration_get_form_fields_for_dropdown( $form_id ),
				'tip'               => __( 'This option is to map phone field for sms verification.', 'user-registration' ),
			),
			array(
				'label'             => __( 'SMS Verification message', 'user-registration' ),
				'description'       => '',
				'id'                => 'user_registration_form_setting_sms_verification_msg',
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_sms_verification_msg', ur_get_sms_verification_default_message_content() ),
				'type'              => 'textarea',
				'class'             => array(),
				'custom_attributes' => array(),
				'input_class'       => array(),
				'required'          => false,
				'tip'               => __( 'This is sms verification message content.', 'user-registration' ),
			),
			array(
				'type'              => 'select',
				'label'             => __( 'Default User Role', 'user-registration' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_setting_default_user_role',
				'class'             => array( 'ur-enhanced-select' ),
				'input_class'       => array(),
				'options'           => $all_roles,
				'custom_attributes' => array(),
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_default_user_role', get_option( 'user_registration_form_setting_default_user_role', 'subscriber' ) ),
				'tip'               => __( 'Default role for the users registered through this form.', 'user-registration' ),
			),
			array(
				'type'              => 'toggle',
				'label'             => __( 'Enable Strong Password', 'user-registration' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_setting_enable_strong_password',
				'class'             => array( 'ur-enhanced-select' ),
				'input_class'       => array(),
				'custom_attributes' => array(),
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_enable_strong_password', ur_string_to_bool( get_option( 'user_registration_form_setting_enable_strong_password', 1 ) ) ),
				'tip'               => __( 'Make strong password compulsary.', 'user-registration' ),
			),
			array(
				'type'              => 'radio-group',
				'label'             => __( 'Minimum Password Strength', 'user-registration' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_setting_minimum_password_strength',
				'class'             => array( 'ur-enhanced-select' ),
				'input_class'       => array(),
				'options'           => array(
					'0' => __( 'Very Weak', 'user-registration' ),
					'1' => __( 'Weak', 'user-registration' ),
					'2' => __( 'Medium', 'user-registration' ),
					'3' => __( 'Strong', 'user-registration' ),
					'4' => __( 'Custom', 'user-registration' ),
				),
				'custom_attributes' => array(),
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_minimum_password_strength', get_option( 'user_registration_form_setting_minimum_password_strength', '3' ) ),
				'tip'               => __( 'Set minimum required password strength.', 'user-registration' ),
			),
			array(
				'type'              => 'number',
				'label'             => __( 'Minimum Uppercase', 'user-registration' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_setting_form_minimum_uppercase',
				'class'             => array( 'ur-enhanced-select custom-password-params' ),
				'input_class'       => array(),
				'custom_attributes' => array(),
				'min'               => '0',
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_form_minimum_uppercase', '0' ),
				'tip'               => __( 'Enter the minimum amount of uppercase you want to allow for password strength.', 'user-registration' ),
			),
			array(
				'type'              => 'number',
				'label'             => __( 'Minimum digits', 'user-registration' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_setting_form_minimum_digits',
				'class'             => array( 'ur-enhanced-select custom-password-params' ),
				'input_class'       => array(),
				'custom_attributes' => array(),
				'min'               => '0',
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_form_minimum_digits', '0' ),
				'tip'               => __( 'Set the minimum number of digits/numbers required for password strength.', 'user-registration' ),
			),
			array(
				'type'              => 'number',
				'label'             => __( 'Minimum Special Characters', 'user-registration' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_setting_form_minimum_special_chars',
				'class'             => array( 'ur-enhanced-select custom-password-params' ),
				'input_class'       => array(),
				'custom_attributes' => array(),
				'min'               => '0',
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_form_minimum_special_chars', '0' ),
				'tip'               => __( 'Set the minimum number of special characters required for password strength.', 'user-registration' ),
			),
			array(
				'type'              => 'number',
				'label'             => __( 'Minimum Password Length', 'user-registration' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_setting_form_minimum_pass_length',
				'class'             => array( 'ur-enhanced-select custom-password-params' ),
				'input_class'       => array(),
				'custom_attributes' => array(),
				'min'               => '6',
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_form_minimum_pass_length', '6' ),
				'tip'               => __( 'Set the minimum password length required for password strength.', 'user-registration' ),
			),
			array(
				'type'              => 'toggle',
				'label'             => __( 'Limit Repetitive letters', 'user-registration' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_setting_no_repeat_chars',
				'class'             => array( 'ur-enhanced-select custom-password-params' ),
				'input_class'       => array(),
				'custom_attributes' => array(),
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_no_repeat_chars', ur_string_to_bool( get_option( 'user_registration_form_setting_no_repeat_chars', 0 ) ) ),
				'tip'               => __( 'Check repetitive letters.', 'user-registration' ),
			),
			array(
				'type'              => 'number',
				'label'             => __( 'Max Repeat Length', 'user-registration' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_setting_form_max_char_repeat_length',
				'class'             => array( 'ur-enhanced-select custom-password-params' ),
				'input_class'       => array(),
				'custom_attributes' => array(),
				'min'               => '1',
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_form_max_char_repeat_length', '' ),
				'tip'               => __( 'Set the Maximum repeat amount for letters in a password.', 'user-registration' ),
			),
			array(
				'type'              => 'text',
				'label'             => __( 'Submit Button Class', 'user-registration' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_setting_form_submit_class',
				'class'             => array( 'ur-enhanced-select' ),
				'input_class'       => array(),
				'custom_attributes' => array(),
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_form_submit_class', '' ),
				'tip'               => __( 'Enter CSS class names for the Submit Button. Multiple class names should be separated with spaces.', 'user-registration' ),
			),
			array(
				'type'              => 'text',
				'label'             => __( 'Submit Button Text', 'user-registration' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_setting_form_submit_label',
				'class'             => array( 'ur-enhanced-select' ),
				'input_class'       => array(),
				'custom_attributes' => array(),
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_form_submit_label', 'Submit' ),
				'tip'               => __( 'Enter desired text for the Submit Button.', 'user-registration' ),
			),
			array(
				'type'              => 'select',
				'label'             => __( 'Success message display', 'user-registration' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_setting_success_message_position',
				'class'             => array( 'ur-enhanced-select' ),
				'input_class'       => array(),
				'options'           => array(
					'0' => esc_html__( 'Top', 'user-registration' ),
					'1' => esc_html__( 'Bottom', 'user-registration' ),
					'2' => esc_html__( 'Hide Form After Successful Submission', 'user-registration' ),
				),
				'custom_attributes' => array(),
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_success_message_position', '1' ),
				'tip'               => __( 'Display success message either at the top or bottom after successful registration.', 'user-registration' ),
			),
			array(
				'type'              => 'toggle',

				/* translators: 1: Link tag open 2:: Link content 3:: Link tag close */
				'label'             => sprintf( __( 'Enable &nbsp; %1$s %2$s Captcha %3$s &nbsp; Support', 'user-registration' ), '<a title="', 'Please make sure the site key and secret are not empty in setting page." href="' . admin_url() . 'admin.php?page=user-registration-settings&tab=captcha" rel="noreferrer noopener" target="_blank">', '</a>' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_setting_enable_recaptcha_support',
				'class'             => array( 'ur-enhanced-select' ),
				'input_class'       => array(),
				'custom_attributes' => array(),
				'default'           => ur_string_to_bool( ur_get_single_post_meta( $form_id, 'user_registration_form_setting_enable_recaptcha_support', false ) ),
				'tip'               => __( 'Enable Captcha for strong security from spams and bots.', 'user-registration' ),
			),
			array(
				'type'              => 'select',
				'label'             => __( 'Select Configured Captcha', 'user-registration' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_setting_configured_captcha_type',
				'class'             => array( 'ur-enhanced-select' ),
				'input_class'       => array(),
				'options'           => $ur_enabled_captchas,
				'custom_attributes' => array(),
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_configured_captcha_type', '1' ),
				'tip'               => __( 'Select the type of Captcha you want in this form.', 'user-registration' ),
			),
			array(
				'type'              => 'select',
				'label'             => __( 'Form Template', 'user-registration' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_template',
				'class'             => array( 'ur-enhanced-select' ),
				'input_class'       => array(),
				'options'           => array(
					'Default'      => __( 'Default', 'user-registration' ),
					'Bordered'     => __( 'Bordered', 'user-registration' ),
					'Flat'         => __( 'Flat', 'user-registration' ),
					'Rounded'      => __( 'Rounded', 'user-registration' ),
					'Rounded Edge' => __( 'Rounded Edge', 'user-registration' ),
				),
				'custom_attributes' => array(),
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_template', ucwords( str_replace( '_', ' ', get_option( 'user_registration_form_template', 'default' ) ) ) ),
				'tip'               => __( 'Choose form template to use.', 'user-registration' ),
			),
			array(
				'type'              => 'text',
				'label'             => __( 'Form Class', 'user-registration' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_custom_class',
				'class'             => array( 'ur-enhanced-select' ),
				'input_class'       => array(),
				'custom_attributes' => array(),
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_custom_class' ),
				'tip'               => __( 'Enter CSS class names for the Form Wrapper. Multiple class names should be separated with spaces.', 'user-registration' ),
			),
			array(
				'type'              => 'select',
				'label'             => __( 'Redirect After Registration', 'user-registration' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_setting_redirect_after_registration',
				'class'             => array( 'ur-enhanced-select' ),
				'input_class'       => array(),
				/**
				 * Filters the redirection options after user registration.
				 *
				 * @param array $redirection_options An associative array where keys represent
				 *                                   the option values, and values represent the labels
				 *                                   for the redirection options.
				 */
				'options'           => apply_filters(
					'user_registration_redirect_after_registration_options',
					array(
						'no-redirection' => __( 'No Redirection', 'user-registration' ),
						'internal-page'  => __( 'Internal Page', 'user-registration' ),
						'external-url'   => __( 'External URL', 'user-registration' ),
						'previous-page'  => __( 'Previous Page', 'user-registration' ),
					)
				),
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_redirect_after_registration', 'no-redirection' ),
				'tip'               => __( 'Choose where to redirect the user after successful registration.', 'user-registration' ),
				'custom_attributes' => array(),
			),
			array(
				'type'              => 'select',
				'label'             => __( 'Custom Page', 'user-registration' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_setting_redirect_page',
				'class'             => array( 'ur-enhanced-select' ),
				'input_class'       => array(),
				'options'           => ur_get_all_pages(),
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_redirect_page', '' ),
				'tip'               => __( 'Choose the custom page to redirect after registration', 'user-registration' ),
				'custom_attributes' => array(),
			),
			array(
				'type'              => 'text',
				'label'             => __( 'Redirect URL', 'user-registration' ),
				'id'                => 'user_registration_form_setting_redirect_options',
				'class'             => array( 'ur-enhanced-select' ),
				'input_class'       => array(),
				'custom_attributes' => array(),
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_redirect_options', get_option( 'user_registration_general_setting_redirect_options', '' ) ),  // Getting redirect options from global settings for backward compatibility.
				'tip'               => __( 'This option lets you enter redirect path after successful user registration.', 'user-registration' ),
			),
			array(
				'type'              => 'number',
				'label'             => __( 'Waiting Period Before Redirection ( In seconds )', 'user-registration' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_setting_redirect_after',
				'class'             => array(),
				'input_class'       => array(),
				'custom_attributes' => array(),
				'min'               => '0',
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_redirect_after', '2' ),
				'tip'               => __( 'Time to wait after registration before redirecting user to another page.', 'user-registration' ),
			),
			array(
				'type'              => 'toggle',
				'label'             => __( 'Activate Spam Protection By Akismet', 'user-registration' ),
				'required'          => false,
				'id'                => 'user_registration_enable_akismet',
				'class'             => array( 'ur-enhanced-select' ),
				'custom_attributes' => array(),
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_enable_akismet', false ),
				'tip'               => __( 'Enable anti-spam for this form with akismet.', 'user-registration' ),

			),
			array(
				'type'        => 'label',
				'id'          => 'user_registration_akismet_warning',
				'description' => ur_check_akismet_installation(),
			),
		),
	);
	/**
	 * Filters the form settings before processing or rendering.
	 *
	 * @param array $arguments An array of form settings.
	 */
	$arguments                 = apply_filters( 'user_registration_get_form_settings', $arguments );
	$arguments['setting_data'] = apply_filters( 'user_registration_settings_text_format', $arguments['setting_data'] );

	return $arguments['setting_data'];
}

/**
 * User Login Option
 *
 * @return array
 */
function ur_login_option() {
	/**
	 * Filters the login options available during user registration.
	 *
	 * @param array $login_options An associative array where keys represent the
	 *                             option values, and values represent the labels
	 *                             for the login options.
	 */
	return apply_filters(
		'user_registration_login_options',
		array(
			'default'            => __( 'Auto approval and manual login', 'user-registration' ),
			'auto_login'         => __( 'Auto approval and auto login ', 'user-registration' ),
			'admin_approval'     => __( 'Admin approval', 'user-registration' ),
			'email_confirmation' => __( 'Auto approval after email confirmation', 'user-registration' ),
		)
	);
}

/**
 * User Login Option
 *
 * @return array
 */
function ur_login_option_with() {
	/**
	 * Filters the login options with specific identification types during login.
	 *
	 * @param array $login_options_with An associative array where keys represent the
	 *                                  identification types, and values represent the labels
	 *                                  for the corresponding login options.
	 */
	return apply_filters(
		'user_registration_login_options_with',
		array(
			'default'  => __( 'Username or Email', 'user-registration' ),
			'username' => __( 'Username', 'user-registration' ),
			'email'    => __( 'Email', 'user-registration' ),
		)
	);
}

/**
 * Get Post meta value by meta key.
 *
 * @param int    $post_id Post ID.
 * @param string $meta_key Meta Key.
 * @param mixed  $default Default Value.
 *
 * @since 1.0.1
 *
 * @return mixed
 */
function ur_get_single_post_meta( $post_id, $meta_key, $default = null ) {

	$post_meta = get_post_meta( $post_id, $meta_key );

	if ( isset( $post_meta[0] ) ) {
		if (
			'user_registration_form_setting_enable_recaptcha_support' === $meta_key || 'user_registration_form_setting_enable_strong_password' === $meta_key
			|| 'user_registration_pdf_submission_to_admin' === $meta_key || 'user_registration_pdf_submission_to_user' === $meta_key || 'user_registration_form_setting_enable_assign_user_role_conditionally' === $meta_key
		) {
			$post_meta[0] = ur_string_to_bool( $post_meta[0] );
		}
		return $post_meta[0];
	}

	return $default;
}

/**
 * Get general form settings by meta key (settings id).
 *
 * @param int    $form_id Form ID.
 * @param string $meta_key Meta Key.
 * @param mixed  $default Default Value.
 *
 * @since 1.0.1
 *
 * @return mixed
 */
function ur_get_form_setting_by_key( $form_id, $meta_key, $default = '' ) {

	$fields = ur_admin_form_settings_fields( $form_id );
	$value  = '';

	foreach ( $fields as $field ) {

		if ( isset( $field['id'] ) && $meta_key == $field['id'] ) {
			$value = isset( $field['default'] ) ? sanitize_text_field( $field['default'] ) : $default;
			break;
		}
	}

	return $value;
}

/**
 * Get user status in case of admin approval login option
 *
 * @param int $user_id User ID.
 * @return int
 */
function ur_get_user_approval_status( $user_id ) {

	$user_status = 1;

	$login_option = ur_get_user_login_option( $user_id );

	if ( 'admin_approval' === $login_option ) {

		$user_status = get_user_meta( $user_id, 'ur_user_status', true );
	}

	return $user_status;
}

/**
 * Get form data by field key.
 *
 * @param array  $form_data Form Data.
 * @param string $key Field Key.
 *
 * @return array
 */
function ur_get_form_data_by_key( $form_data, $key = null ) {

	$form_data_array = array();

	foreach ( $form_data as $data ) {
		foreach ( $data as $single_data ) {
			foreach ( $single_data as $field_data ) {

				$field_key = isset( $field_data->field_key ) && null !== $field_data->field_key ? $field_data->field_key : '';

				if ( ! empty( $field_key ) ) {
					$field_name = isset( $field_data->general_setting->field_name ) && null !== $field_data->general_setting->field_name ? $field_data->general_setting->field_name : '';

					if ( null === $key ) {

						if ( ! empty( $field_name ) ) {
							$form_data_array[ $field_name ] = $field_data;
						} else {
							$form_data_array[] = $field_data;
						}
					} elseif ( $field_key === $key ) {

						if ( ! empty( $field_name ) ) {
							$form_data_array[ $field_name ] = $field_data;
						} else {
							$form_data_array[] = $field_data;
						}
					}
				}
			}
		}
	}

	return $form_data_array;
}

/**
 * Get a log file path.
 *
 * @since 1.0.5
 *
 * @param string $handle name.
 *
 * @return string the log file path.
 */
function ur_get_log_file_path( $handle ) {
	return UR_Log_Handler_File::get_log_file_path( $handle );
}

/**
 * Registers the default log handler.
 *
 * @since 1.0.5
 *
 * @param array $handlers Log handlers.
 *
 * @return array
 */
function ur_register_default_log_handler( $handlers ) {

	if ( defined( 'UR_LOG_HANDLER' ) && class_exists( UR_LOG_HANDLER ) ) {
		$handler_class   = UR_LOG_HANDLER;
		$default_handler = new $handler_class();
	} else {
		$default_handler = new UR_Log_Handler_File();
	}

	array_push( $handlers, $default_handler );

	return $handlers;
}

add_filter( 'user_registration_register_log_handlers', 'ur_register_default_log_handler' );


/**
 * Get a shared logger instance.
 *
 * Use the user_registration_logging_class filter to change the logging class. You may provide one of the following:
 *     - a class name which will be instantiated as `new $class` with no arguments
 *     - an instance which will be used directly as the logger
 * In either case, the class or instance *must* implement UR_Logger_Interface.
 *
 * @see UR_Logger_Interface
 * @since 1.1.0
 * @return UR_Logger
 */
function ur_get_logger() {
	static $logger = null;
	if ( null === $logger ) {
		/**
		 * Applies the 'user_registration_logging_class' filter to customize the logger class.
		 *
		 * @since 1.1.0
		 *
		 * @param string|object $class The class name or an instance of the logger.
		 */
		$class      = apply_filters( 'user_registration_logging_class', 'UR_Logger' );
		$implements = class_implements( $class );
		if ( is_array( $implements ) && in_array( 'UR_Logger_Interface', $implements ) ) {
			if ( is_object( $class ) ) {
				$logger = $class;
			} else {
				$logger = new $class();
			}
		} else {
			ur_doing_it_wrong(
				__FUNCTION__,
				sprintf(
					/* translators: %s: Class */
					__( 'The class <code>%s</code> provided by user_registration_logging_class filter must implement <code>UR_Logger_Interface</code>.', 'user-registration' ),
					esc_html( is_object( $class ) ? get_class( $class ) : $class )
				),
				'1.0.5'
			);
			$logger = new UR_Logger();
		}
	}

	return $logger;
}

/**
 * Handles addon plugin updater.
 *
 * @param string $file Plugin File.
 * @param int    $item_id Item ID.
 * @param string $addon_version Addon Version.
 * @param bool   $beta Is beta version.
 *
 * @since 1.1.0
 */
function ur_addon_updater( $file, $item_id, $addon_version, $beta = false ) {
	$api_endpoint = 'https://wpeverest.com/edd-sl-api/';
	$license_key  = trim( get_option( 'user-registration_license_key' ) );
	if ( class_exists( 'UR_AddOn_Updater' ) ) {
		new UR_AddOn_Updater(
			esc_url_raw( $api_endpoint ),
			$file,
			array(
				'version' => $addon_version,
				'license' => $license_key,
				'item_id' => $item_id,
				'author'  => 'WPEverest',
				'url'     => home_url(),
				'beta'    => $beta,
			)
		);
	}
}

/**
 * Check if username already exists in case of optional username
 * And while stripping through email address and incremet last number by 1.
 *
 * @param  string $username Username.
 * @return string
 */
function check_username( $username ) {

	if ( username_exists( $username ) ) {
		preg_match_all( '/\d+$/m', $username, $matches );

		if ( isset( $matches[0][0] ) ) {
			$last_char       = $matches[0][0];
			$strip_last_char = substr( $username, 0, - ( strlen( (string) $last_char ) ) );
			++$last_char;
			$username = $strip_last_char . $last_char;
			$username = check_username( $username );

			return $username;
		} else {
			$username = $username . '_1';
			$username = check_username( $username );

			return $username;
		}
	}

	return $username;
}

/**
 * Get all user registration forms title with respective id.
 *
 * @param int $post_count Post Count.
 * @return array
 */
function ur_get_all_user_registration_form( $post_count = -1 ) {
	$args        = array(
		'status'      => 'publish',
		'numberposts' => $post_count,
		'order'       => 'ASC',
	);
	$posts_array = UR()->form->get_form( '', $args );
	$all_forms   = array();

	foreach ( $posts_array as $post ) {
		$all_forms[ $post->ID ] = esc_html($post->post_title);
	}

	return $all_forms;
}

/**
 * Get the node to display google reCaptcha
 *
 * @param string $context Recaptcha context.
 * @param string $recaptcha_enabled Is Recaptcha enabled.
 * @return string
 */
function ur_get_recaptcha_node( $context, $recaptcha_enabled = false, $form_id = 0 ) {
	$recaptcha_type         = get_option( 'user_registration_captcha_setting_recaptcha_version', 'v2' );
	$invisible_recaptcha    = ur_option_checked( 'user_registration_captcha_setting_invisible_recaptcha_v2', false );
	$theme_mod              = '';
	$enqueue_script         = '';
	$recaptcha_site_key     = '';
	$recaptcha_site_secret  = '';
	$empty_credentials      = false;
	$global_captcha_enabled = false;

	if ( 'login' === $context ) {
		$recaptcha_type = get_option( 'user_registration_login_options_configured_captcha_type', $recaptcha_type );
	} elseif ( 'register' === $context && $form_id ) {
		$recaptcha_type = ur_get_single_post_meta( $form_id, 'user_registration_form_setting_configured_captcha_type', $recaptcha_type );
	} elseif ( 'test_captcha' === $context && false !== $recaptcha_enabled ) {
		$recaptcha_type = $recaptcha_enabled;
	} elseif ( 'lost_password' === $context ) {
		//Same recaptcha type as login.
		$recaptcha_type = get_option( 'user_registration_login_options_configured_captcha_type', $recaptcha_type );
		$recaptcha_type = apply_filters( 'user_registration_lost_password_captcha_type', $recaptcha_type );

	}

	if ( 'v2' === $recaptcha_type && ! $invisible_recaptcha ) {
		$recaptcha_site_key    = get_option( 'user_registration_captcha_setting_recaptcha_site_key' );
		$recaptcha_site_secret = get_option( 'user_registration_captcha_setting_recaptcha_site_secret' );
		$global_captcha_enabled = get_option( 'user_registration_captcha_setting_recaptcha_enable_v2', false );
		$enqueue_script        = 'ur-google-recaptcha';
	} elseif ( 'v2' === $recaptcha_type && $invisible_recaptcha ) {
		$recaptcha_site_key    = get_option( 'user_registration_captcha_setting_recaptcha_invisible_site_key' );
		$recaptcha_site_secret = get_option( 'user_registration_captcha_setting_recaptcha_invisible_site_secret' );
		$global_captcha_enabled = get_option( 'user_registration_captcha_setting_recaptcha_enable_v2', false );
		$enqueue_script        = 'ur-google-recaptcha';
	} elseif ( 'v3' === $recaptcha_type ) {
		$recaptcha_site_key    = get_option( 'user_registration_captcha_setting_recaptcha_site_key_v3' );
		$recaptcha_site_secret = get_option( 'user_registration_captcha_setting_recaptcha_site_secret_v3' );
		$global_captcha_enabled = get_option( 'user_registration_captcha_setting_recaptcha_enable_v3', false );
		$enqueue_script        = 'ur-google-recaptcha-v3';
	} elseif ( 'hCaptcha' === $recaptcha_type ) {
		$recaptcha_site_key    = get_option( 'user_registration_captcha_setting_recaptcha_site_key_hcaptcha' );
		$recaptcha_site_secret = get_option( 'user_registration_captcha_setting_recaptcha_site_secret_hcaptcha' );
		$global_captcha_enabled = get_option( 'user_registration_captcha_setting_recaptcha_enable_hcaptcha', false );
		$enqueue_script        = 'ur-recaptcha-hcaptcha';
	} elseif ( 'cloudflare' === $recaptcha_type ) {
		$recaptcha_site_key = get_option( 'user_registration_captcha_setting_recaptcha_site_key_cloudflare' );
		$recaptcha_site_secret = get_option( 'user_registration_captcha_setting_recaptcha_site_secret_cloudflare' );
		$theme_mod          = get_option( 'user_registration_captcha_setting_recaptcha_cloudflare_theme' );
		$global_captcha_enabled = get_option( 'user_registration_captcha_setting_recaptcha_enable_cloudflare', false );
		$enqueue_script     = 'ur-recaptcha-cloudflare';
	}
	static $rc_counter = 0;

	if (  empty( $recaptcha_site_key ) &&  empty( $recaptcha_site_secret ) ) {
		$empty_credentials = true;
	}

	//Exit early if recaptcha is not enabled in global settings or has messing credentials.
	if ( ! $global_captcha_enabled ||  $empty_credentials  ) {
		return '';
	}

	if ( $recaptcha_enabled  ) {

		if ( 0 === $rc_counter || 'test_captcha' === $context ) {
			wp_enqueue_script( 'ur-recaptcha' );
			wp_enqueue_script( $enqueue_script );

			$ur_google_recaptcha_code = array(
				'site_key'          => $recaptcha_site_key,
				'is_captcha_enable' => true,
				'version'           => $recaptcha_type,
				'is_invisible'      => $invisible_recaptcha,
				'theme_mode'        => $theme_mod,
			);

			if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
				?>
					<script id="<?php echo esc_attr( $enqueue_script ); ?>">
					const ur_recaptcha_code = <?php echo wp_json_encode( $ur_google_recaptcha_code ); ?>
					</script>
				<?php
			} else {
				wp_localize_script( $enqueue_script, 'ur_recaptcha_code', $ur_google_recaptcha_code );
			}
			++$rc_counter;
		}

		if ( 'v3' === $recaptcha_type ) {
			if ( 'login' === $context ) {
				$recaptcha_node = '<div id="node_recaptcha_login" class="g-recaptcha-v3" style="display:none"><textarea id="g-recaptcha-response" name="g-recaptcha-response" ></textarea></div>';
			} elseif ( 'test_captcha' === $context ) {
				$recaptcha_node = '<div id="node_recaptcha_login-v3" class="g-recaptcha-v3" style="display:none"><textarea id="g-recaptcha-response-v3" name="g-recaptcha-response" ></textarea></div>';
			} elseif ( 'register' === $context ) {
				$recaptcha_node = '<div id="node_recaptcha_register" class="g-recaptcha-v3" style="display:none"><textarea id="g-recaptcha-response" name="g-recaptcha-response" ></textarea></div>';
			} elseif ( 'lost_password' === $context ) {
				$recaptcha_node = '<div id="node_recaptcha_lost_password" class="g-recaptcha-v3" style="display:none"><textarea id="g-recaptcha-response" name="g-recaptcha-response" ></textarea></div>';
			} else {
				$recaptcha_node = '';
			}
		} elseif ( 'hCaptcha' === $recaptcha_type ) {
			if ( 'login' === $context ) {
				$recaptcha_node = '<div id="node_recaptcha_login" class="g-recaptcha-hcaptcha"></div>';
			} elseif ( 'test_captcha' === $context ) {
				$recaptcha_node = '<div id="node_recaptcha_login-hcaptcha" class="g-recaptcha-hcaptcha"></div>';
			} elseif ( 'register' === $context ) {
				$recaptcha_node = '<div id="node_recaptcha_register" class="g-recaptcha-hcaptcha"></div>';
			} elseif ( 'lost_password' === $context ) {
				$recaptcha_node = '<div id="node_recaptcha_lost_password" class="g-recaptcha-hcaptcha"></div>';
			} else {
				$recaptcha_node = '';
			}
		} elseif ( 'cloudflare' === $recaptcha_type ) {

			if ( 'login' === $context ) {
				$recaptcha_node = '<div id="node_recaptcha_login" class="cf-turnstile"></div>';
			} elseif ( 'test_captcha' === $context ) {
				$recaptcha_node = '<div id="node_recaptcha_login-cf-turnstile" class="cf-turnstile"></div>';
			} elseif ( 'register' === $context ) {
				$recaptcha_node = '<div id="node_recaptcha_register" class="cf-turnstile"></div>';
			} elseif ( 'lost_password' === $context ) {
				$recaptcha_node = '<div id="node_recaptcha_lost_password" class="cf-turnstile"></div>';
			} else {
				$recaptcha_node = '';
			}
		} elseif ( 'v2' === $recaptcha_type && $invisible_recaptcha ) {
			if ( 'login' === $context || 'test_captcha' === $context ) {
				$recaptcha_node = '<div id="node_recaptcha_login" class="g-recaptcha" data-size="invisible"></div>';
			} elseif ( 'register' === $context ) {
				$recaptcha_node = '<div id="node_recaptcha_register" class="g-recaptcha" data-size="invisible"></div>';
			} elseif ( 'lost_password' === $context ) {
				$recaptcha_node = '<div id="node_recaptcha_lost_password" class="g-recaptcha" data-size="invisible"></div>';
			} else {
				$recaptcha_node = '';
			}
		} elseif ( 'login' === $context || 'test_captcha' === $context ) {
			$recaptcha_node = '<div id="node_recaptcha_login" class="g-recaptcha"></div>';
		} elseif ( 'register' === $context ) {
			$recaptcha_node = '<div id="node_recaptcha_register" class="g-recaptcha"></div>';
		} elseif ( 'lost_password' === $context ) {
			$recaptcha_node = '<div id="node_recaptcha_lost_password" class="g-recaptcha"></div>';
		} else {
			$recaptcha_node = '';
		}
	} else {
		$recaptcha_node = '';
	}

	return $recaptcha_node;
}


/**
 * Get meta key label pair by form id
 *
 * @param  int $form_id Form ID.
 * @since  1.5.0
 * @return array
 */
function ur_get_meta_key_label( $form_id ) {

	$key_label = array();

	$post_content_array = ( $form_id ) ? UR()->form->get_form( $form_id, array( 'content_only' => true ) ) : array();

	foreach ( $post_content_array as $post_content_row ) {
		foreach ( $post_content_row as $post_content_grid ) {
			foreach ( $post_content_grid as $field ) {
				if ( isset( $field->field_key ) && isset( $field->general_setting->field_name ) ) {
					$key_label[ $field->general_setting->field_name ] = $field->general_setting->label;
				}
			}
		}
	}
	/**
	 * Filters the label for a meta key.
	 *
	 * @param string $key_label          The label for the meta key.
	 * @param int    $form_id            The ID of the user registration form.
	 * @param array  $post_content_array An array containing the post content for the form.
	 */
	return apply_filters( 'user_registration_meta_key_label', $key_label, $form_id, $post_content_array );
}

/**
 * Get all user registration fields of the user by querying to database.
 *
 * @param  int $user_id    User ID.
 * @since  1.5.0
 * @return array
 */
function ur_get_user_extra_fields( $user_id ) {
	$name_value = array();

	$admin_profile = new UR_Admin_Profile();
	$extra_data    = $admin_profile->get_user_meta_by_form_fields( $user_id );
	$form_fields   = isset( array_column( $extra_data, 'fields' )[0] ) ? array_column( $extra_data, 'fields' )[0] : array(); //phpcs:ignore.
	if ( ! empty( $form_fields ) ) {
		foreach ( $form_fields as $field_key => $field_data ) {
			$value     = get_user_meta( $user_id, $field_key, true );
			$field_key = str_replace( 'user_registration_', '', $field_key );

			if ( is_serialized( $value ) ) {
				$value = unserialize( $value, array( 'allowed_classes' => false ) ); //phpcs:ignore.
				$value = implode( ',', $value );
			}

			$name_value[ $field_key ] = $value;
		}
	}
	/**
	 * Filters extra fields associated with a user.
	 *
	 * @param array $name_value An array of name-value pairs representing extra fields.
	 * @param int   $user_id    The user ID associated with the registration process.
	 */
	return apply_filters( 'user_registration_user_extra_fields', $name_value, $user_id );
}

/**
 * Get User status like approved, pending.
 *
 * @param  string $user_status Admin approval status of user.
 * @param  string $user_email_status Email confirmation status of user.
 */
function ur_get_user_status( $user_status, $user_email_status ) {
	$status = array();
	if ( '0' === $user_status || '0' === $user_email_status ) {
		array_push( $status, 'Pending' );
	} elseif ( '-1' === $user_status || '-1' === $user_email_status ) {
		array_push( $status, 'Denied' );
	} elseif ( $user_email_status ) {
		array_push( $status, 'Verified' );
	} else {
		array_push( $status, 'Approved' );
	}
	return $status;
}

/**
 * Get link for back button used on email settings.
 *
 * @param  string $label Label.
 * @param  string $url URL.
 */
function ur_back_link( $label, $url ) {
	return '<a href="' . esc_url( $url ) . '" aria-label="' . esc_attr( $label ) . '">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#000" viewBox="0 0 24 24">
                  <path d="M15.653 2.418a1.339 1.339 0 0 1 1.944 0 1.468 1.468 0 0 1 0 2.02L10.32 12l7.278 7.562.094.108a1.47 1.47 0 0 1-.094 1.912c-.503.523-1.3.555-1.84.098l-.104-.098-8.25-8.572a1.468 1.468 0 0 1 0-2.02l8.25-8.572Z"/>
                </svg>
            </a>';
}

/**
 * The function wp_doing ajax() is introduced in core @since 4.7,
 */
if ( ! function_exists( 'wp_doing_ajax' ) ) {
	/**
	 * Filters whether the current request is a WordPress Ajax request.
	 */
	function wp_doing_ajax() {
		return apply_filters( 'wp_doing_ajax', defined( 'DOING_AJAX' ) && DOING_AJAX );
	}
}

/**
 * Checks if the string is json or not
 *
 * @param  string $str String to check.
 * @since  1.4.2
 * @return mixed
 */
function ur_is_json( $str ) {
	if ( ! is_string( $str ) ) {
		return false;
	}

	$json = json_decode( $str );
	return $json && $str != $json && json_last_error() == JSON_ERROR_NONE;
}

/**
 * Checks if the form contains a date field or not.
 *
 * @deprecated 3.1.3
 * @param  int $form_id     Form ID.
 * @since  1.5.3
 * @return void
 */
function ur_has_date_field( $form_id ) {
	ur_deprecated_function( 'ur_has_date_field', '3.1.3', 'ur_has_flatpickr_field' );
}

/**
 * Checks if the form contains a date and time field or not.
 *
 * @param  int $form_id     Form ID.
 * @since  1.5.3
 * @return boolean
 */
function ur_has_flatpickr_field( $form_id ) {

	$post_content_array = ( $form_id ) ? UR()->form->get_form( $form_id, array( 'content_only' => true ) ) : array();

	if ( ! empty( $post_content_array ) ) {
		foreach ( $post_content_array as $post_content_row ) {
			foreach ( $post_content_row as $post_content_grid ) {
				foreach ( $post_content_grid as $field ) {
					if ( isset( $field->field_key ) && ( 'date' === $field->field_key || 'timepicker' === $field->field_key ) ) {
						return true;
					}
				}
			}
		}
	}

	return false;
}

/**
 * Get attributes from the shortcode content.
 *
 * @param  string $content     Shortcode content.
 * @return array        Array of attributes within the shortcode.
 *
 * @since  1.6.0
 */
function ur_get_shortcode_attr( $content ) {
	$pattern = get_shortcode_regex();

	$keys   = array();
	$result = array();

	if ( preg_match_all( '/' . $pattern . '/s', $content, $matches ) ) {

		foreach ( $matches[0] as $key => $value ) {

			// $matches[ 3 ] return the shortcode attribute as string.
			// replace space with '&' for parse_str() function.
			$get = str_replace( ' ', '&', $matches[3][ $key ] );
			parse_str( $get, $output );

			// Get all shortcode attribute keys.
			$keys     = array_unique( array_merge( $keys, array_keys( $output ) ) );
			$result[] = $output;
		}

		if ( $keys && $result ) {

			// Loop the result array and add the missing shortcode attribute key.
			foreach ( $result as $key => $value ) {

				// Loop the shortcode attribute key.
				foreach ( $keys as $attr_key ) {
					$result[ $key ][ $attr_key ] = isset( $result[ $key ][ $attr_key ] ) ? $result[ $key ][ $attr_key ] : null;
				}

				// Sort the array key.
				ksort( $result[ $key ] );
			}
		}
	}

	return $result;
}

/**
 * Print js script by properly sanitizing and escaping.
 *
 * @since 1.1.2
 * Output any queued javascript code in the footer.
 */
function ur_print_js() {
	global $ur_queued_js;

	if ( ! empty( $ur_queued_js ) ) {
		// Sanitize.
		$ur_queued_js = wp_check_invalid_utf8( $ur_queued_js );
		$ur_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $ur_queued_js );
		$ur_queued_js = str_replace( "\r", '', $ur_queued_js );

		$js = "<!-- User Registration JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) { $ur_queued_js });\n</script>\n";

		/**
		 * User Registration js filter.
		 *
		 * @param string $js JavaScript code.
		 */
		echo wp_kses( apply_filters( 'user_registration_queued_js', $js ), array( 'script' => array( 'type' => true ) ) );

		unset( $ur_queued_js );
	}
}
/**
 * Enqueue UR js.
 *
 * @since 1.1.2
 * Queue some JavaScript code to be output in the footer.
 *
 * @param string $code Code to enqueue.
 */
function ur_enqueue_js( $code ) {
	global $ur_queued_js;

	if ( empty( $ur_queued_js ) ) {
		$ur_queued_js = '';
	}

	$ur_queued_js .= "\n" . $code . "\n";
}

/**
 * Delete expired transients.
 *
 * Deletes all expired transients. The multi-table delete syntax is used.
 * to delete the transient record from table a, and the corresponding.
 * transient_timeout record from table b.
 *
 * Based on code inside core's upgrade_network() function.
 *
 * @since  1.2.0
 * @return int Number of transients that were cleared.
 */
function ur_delete_expired_transients() {
	global $wpdb;

	$rows = $wpdb->query(
		$wpdb->prepare(
			"DELETE a, b FROM $wpdb->options a, $wpdb->options b
			WHERE a.option_name LIKE %s
			AND a.option_name NOT LIKE %s
			AND b.option_name = CONCAT( '_transient_timeout_', SUBSTRING( a.option_name, 12 ) )
			AND b.option_value < %d",
			$wpdb->esc_like( '_transient_' ) . '%',
			$wpdb->esc_like( '_transient_timeout_' ) . '%',
			time()
		)
	);

	$rows2 = $wpdb->query(
		$wpdb->prepare(
			"DELETE a, b FROM $wpdb->options a, $wpdb->options b
			WHERE a.option_name LIKE %s
			AND a.option_name NOT LIKE %s
			AND b.option_name = CONCAT( '_site_transient_timeout_', SUBSTRING( a.option_name, 17 ) )
			AND b.option_value < %d",
			$wpdb->esc_like( '_site_transient_' ) . '%',
			$wpdb->esc_like( '_site_transient_timeout_' ) . '%',
			time()
		)
	);

	return absint( $rows + $rows2 );
}
add_action( 'user_registration_installed', 'ur_delete_expired_transients' );

/**
 * String translation function.
 *
 * @since 1.7.3
 *
 * @param int    $form_id Form ID.
 * @param string $field_id Field ID.
 * @param mixed  $variable To be translated for WPML compatibility.
 */
function ur_string_translation( $form_id, $field_id, $variable ) {
	if ( function_exists( 'icl_register_string' ) ) {
		icl_register_string( isset( $form_id ) && 0 !== $form_id ? 'user_registration_' . absint( $form_id ) : 'user-registration', isset( $field_id ) ? $field_id : '', $variable );
	}
	if ( function_exists( 'icl_t' ) ) {
		$variable = icl_t( isset( $form_id ) && 0 !== $form_id ? 'user_registration_' . absint( $form_id ) : 'user-registration', isset( $field_id ) ? $field_id : '', $variable );
	}
	return $variable;
}

/**
 * Get Form ID from User ID.
 *
 * @param int $user_id User ID.
 *
 * @return int $form_id Form ID.
 */
function ur_get_form_id_by_userid( $user_id ) {
	$form_id_array = get_user_meta( $user_id, 'ur_form_id' );
	$form_id       = 0;

	if ( isset( $form_id_array[0] ) ) {
		$form_id = $form_id_array[0];
	}
	return $form_id;
}

/**
 * Get source ID through which the given user was supposedly registered.
 *
 * @since 1.9.0
 *
 * @param int $user_id User ID.
 *
 * @return mixed
 */
function ur_get_registration_source_id( $user_id ) {
	$user_metas = get_user_meta( $user_id );

	if ( isset( $user_metas['user_registration_social_connect_bypass_current_password'] ) ) {
		$networks = array( 'facebook', 'linkedin', 'google', 'twitter' );

		foreach ( $networks as $network ) {

			if ( isset( $user_metas[ 'user_registration_social_connect_' . $network . '_username' ] ) ) {
				return $network;
			}
		}
	} elseif ( isset( $user_metas['ur_form_id'] ) ) {
		return $user_metas['ur_form_id'][0];
	} else {
		return null;
	}
}

/**
 * Check if a datetime falls in a range of time.
 *
 * @since 1.9.0
 *
 * @param string      $target_date Target date.
 * @param string|null $start_date Start date.
 * @param string|null $end_date End date.
 *
 * @return bool
 */
function ur_falls_in_date_range( $target_date, $start_date = null, $end_date = null ) {
	$start_ts       = strtotime( $start_date );
	$end_ts         = strtotime( $end_date . ' +1 Day' );
	$target_date_ts = strtotime( $target_date );

	// If the starting and the ending date are set as same.
	if ( $start_ts === $end_ts ) {
		$datetime = new DateTime();
		$datetime->setTimestamp( $end_ts );

		date_add( $datetime, date_interval_create_from_date_string( '23 hours 59 mins 59 secs' ) );
		$end_ts = $datetime->getTimestamp();
	}

	if ( $start_date && $end_date ) {
		return ( $start_ts <= $target_date_ts ) && ( $target_date_ts <= $end_ts );
	} elseif ( $start_date ) {
		return ( $start_ts <= $target_date_ts );
	} elseif ( $end_date ) {
		return ( $target_date_ts <= $end_ts );
	} else {
		return false;
	}
}

/**
 * Get Post Content By Form ID.
 *
 * @param int $form_id Form Id.
 * @param string $form_status The form status.
 *
 * @return array|mixed|null|object
 */

function ur_get_post_content( $form_id, $form_status='publish' ) {
	$args      = array(
		'post_type'   => 'user_registration',
		'post_status' => $form_status,
		'post__in' => array( $form_id ),
	);
	$post_data = get_posts( $args );

	if ( isset( $post_data[0]->post_content ) ) {

		return json_decode( $post_data[0]->post_content );
	} else {

		return array();
	}
}

/**
 * A wp_parse_args() for multi-dimensional array.
 *
 * @see https://developer.wordpress.org/reference/functions/wp_parse_args/
 *
 * @since 1.9.0
 *
 * @param array $args       Value to merge with $defaults.
 * @param array $defaults   Array that serves as the defaults.
 *
 * @return array    Merged user defined values with defaults.
 */
function ur_parse_args( &$args, $defaults ) {
	$args     = (array) $args;
	$defaults = (array) $defaults;
	$result   = $defaults;
	foreach ( $args as $k => &$v ) {
		if ( is_array( $v ) && isset( $result[ $k ] ) ) {
			$result[ $k ] = ur_parse_args( $v, $result[ $k ] );
		} else {
			$result[ $k ] = $v;
		}
	}
	return $result;
}

/**
 * Override email content for specific form.
 *
 * @param int    $form_id Form Id.
 * @param object $settings Settings for specific email.
 * @param string $message Message to be sent in email body.
 * @param string $subject Subject of the email.
 *
 * @return array
 */
function user_registration_email_content_overrider( $form_id, $settings, $message, $subject ) {
	// Check if email templates addon is active.
	if ( class_exists( 'User_Registration_Email_Templates' ) ) {
		$email_content_override = ur_get_single_post_meta( $form_id, 'user_registration_email_content_override', '' );

		// Check if the post meta exists and have contents.
		if ( $email_content_override ) {

			$auto_password_template_overrider = isset( $email_content_override[ $settings->id ] ) ? $email_content_override[ $settings->id ] : '';

			// Check if the email override is enabled.
			if ( '' !== $auto_password_template_overrider && ur_string_to_bool( $auto_password_template_overrider['override'] ) ) {
				$message = $auto_password_template_overrider['content'];
				$subject = $auto_password_template_overrider['subject'];
			}
		}
	}
	return array( $message, $subject );
}

/** Get User Data in particular array format.
 *
 * @param string $new_string Field Key.
 * @param string $post_key Post Key.
 * @param array  $profile Form Data.
 * @param mixed  $value Value.
 */
function ur_get_valid_form_data_format( $new_string, $post_key, $profile, $value ) {
	$valid_form_data = array();
	if ( isset( $profile[ $post_key ] ) ) {
		$field_type = $profile[ $post_key ]['type'];

		if ( 'repeater' === $field_type ) {
			return $valid_form_data;
		}
		switch ( $field_type ) {
			case 'checkbox':
			case 'multi_select2':
				if ( ! is_array( $value ) && ! empty( $value ) ) {
					$value = ur_maybe_unserialize( $value );
				}
				break;
			case 'file':
				$files = is_array( $value ) ? $value : explode( ',', $value );

				if ( is_array( $files ) && isset( $files[0] ) ) {
					$attachment_ids = '';

					foreach ( $files as $key => $file ) {
						$seperator = 0 < $key ? ',' : '';

						if ( wp_http_validate_url( $file ) ) {

							$attachment_ids = $attachment_ids . '' . $seperator . '' . attachment_url_to_postid( $file );
						}
					}
					$value = ! empty( $attachment_ids ) ? $attachment_ids : $value;
				} elseif ( wp_http_validate_url( $value ) ) {

					$value = attachment_url_to_postid( $value );
				}
				break;
		}

		$valid_form_data[ $new_string ]               = new stdClass();
		$valid_form_data[ $new_string ]->field_name   = $new_string;
		$valid_form_data[ $new_string ]->value        = $value;
		$valid_form_data[ $new_string ]->field_type   = $profile[ $post_key ]['type'];
		$valid_form_data[ $new_string ]->label        = $profile[ $post_key ]['label'];
		$valid_form_data[ $new_string ]->extra_params = array(
			'field_key' => $profile[ $post_key ]['field_key'],
			'label'     => $profile[ $post_key ]['label'],
		);
	} else {
		$valid_form_data[ $new_string ]               = new stdClass();
		$valid_form_data[ $new_string ]->field_name   = $new_string;
		$valid_form_data[ $new_string ]->value        = $value;
		$valid_form_data[ $new_string ]->extra_params = array(
			'field_key' => $new_string,
		);
	}
	return $valid_form_data;
}

/**
 * Add our login and my account shortcodes to conflicting shortcodes filter of All In One Seo plugin to resolve the conflict
 *
 * @param array $conflict_shortcodes Array of shortcodes that All in one Seo is conflicting with.
 *
 * @since 1.9.4
 */
function ur_resolve_conflicting_shortcodes_with_aioseo( $conflict_shortcodes ) {
	$ur_shortcodes = array(
		'User Registration My Account' => '[user_registration_my_account]',
		'User Registration Login'      => '[user_registration_login]',
	);

	$conflict_shortcodes = array_merge( $conflict_shortcodes, $ur_shortcodes );
	return $conflict_shortcodes;
}

add_filter( 'aioseo_conflicting_shortcodes', 'ur_resolve_conflicting_shortcodes_with_aioseo' );

/**
 * Parse name values and smart tags
 *
 * @param  int   $user_id User ID.
 * @param  int   $form_id Form ID.
 * @param  array $valid_form_data Form filled data.
 *
 * @since 1.9.6
 *
 * @return array
 */
function ur_parse_name_values_for_smart_tags( $user_id, $form_id, $valid_form_data ) {

	$name_value = array();
	$data_html  = '<table class="user-registration-email__entries" cellpadding="0" cellspacing="0"><tbody>';

	// Generate $data_html string to replace for {{all_fields}} smart tag.
	foreach ( $valid_form_data as $field_meta => $form_data ) {

		if ( 'user_confirm_password' === $field_meta || 'user_pass' === $field_meta || preg_match( '/password_/', $field_meta ) ) {
			continue;
		}

		// Donot include privacy policy value.
		if ( isset( $form_data->extra_params['field_key'] ) && 'privacy_policy' === $form_data->extra_params['field_key'] ) {
			continue;
		}

		if ( isset( $form_data->extra_params['field_key'] ) && 'country' === $form_data->extra_params['field_key'] && '' !== $form_data->value ) {
			$country_class    = ur_load_form_field_class( $form_data->extra_params['field_key'] );
			$countries        = $country_class::get_instance()->get_country();
			$form_data->value = isset( $countries[ $form_data->value ] ) ? $countries[ $form_data->value ] : $form_data->value;
		}
		/**
		 * Filter hook allows developers to modify the parsed values for smart tags
		 * during the user registration process. It provides an opportunity to customize
		 * the values based on the form data.
		 *
		 * @param array $form_data An array of form data used for parsing smart tags.
		 */
		$form_data = apply_filters( 'user_registration_parse_values_for_smart_tags', $form_data );

		if( is_array ( $form_data ) ) {
			$label = isset( $form_data['label']) ? $form_data['label'] : '';
			$field_name = isset( $form_data['field_key'] ) ? $form_data['field_key'] : '';
			$value      = isset( $form_data['default'] ) ? $form_data['default'] : '';
			if( 'checkbox' === $field_name && !empty($value)) {
				$unserialized_value = unserialize($form_data['default'] );

				if (is_array($unserialized_value)) {
					$value = implode(", ", $unserialized_value);
				} else {
					$value = (string) $unserialized_value;
				}
			}

		} else{
			$label      = isset( $form_data->extra_params['label'] ) ? $form_data->extra_params['label'] : '';
			$field_name = isset( $form_data->field_name ) ? $form_data->field_name : '';
			$value      = isset( $form_data->value ) ? $form_data->value : '';
		}

		if ( 'user_pass' === $field_meta ) {
			$value = __( 'Chosen Password', 'user-registration' );
		}

		// Check if value contains array.
		if ( is_array( $value ) ) {
			$value = ! isset( $value['row_1'] ) ? implode( ',', $value ) : '';
		}

		$data_html .= '<tr>';

		if ( isset( $form_data->field_type ) && 'repeater' === $form_data->field_type ) {
			$data_html .= '<td>' . $value . '</td></tr>';
		} elseif ( isset( $form_data->extra_params['field_key'] ) && 'signature' === $form_data->extra_params['field_key'] ) {
				$data_html .= '<tr><td>' . $label . ' : </td><td><img class="profile-preview" alt="Signature" width="50px" height="50px" src="' . ( is_numeric( $value ) ? esc_url( wp_get_attachment_url( $value ) ) : esc_url( $value ) ) . '" /></td></tr>';
		} else {
			$data_html .= '<tr><td>' . $label . ' : </td><td>' . $value . '</td></tr>';
		}

		$name_value[ $field_name ] = $value;
	}

	$data_html .= '</tbody></table>';
	/**
	 * Filters the processed values for a smart tag.
	 *
	 * @param array $name_value       An array of name-value pairs representing the smart tag.
	 * @param array $valid_form_data  An array of valid form data used in the registration process.
	 * @param int   $form_id          The ID of the user registration form.
	 * @param int   $user_id          The user ID associated with the registration process.
	 */
	$name_value = apply_filters( 'user_registration_process_smart_tag', $name_value, $valid_form_data, $form_id, $user_id );

	return array( $name_value, $data_html );
}

/**
 * Get field data by field_name.
 *
 * @param int    $form_id Form Id.
 * @param string $field_name Field Name.
 *
 * @return array
 */
function ur_get_field_data_by_field_name( $form_id, $field_name ) {
	$field_data = array();

	$post_content_array = ( $form_id ) ? UR()->form->get_form( $form_id, array( 'content_only' => true ) ) : array();

	foreach ( $post_content_array as $post_content_row ) {
		foreach ( $post_content_row as $post_content_grid ) {
			if ( is_array( $post_content_grid ) || is_object( $post_content_grid ) ) {
				foreach ( $post_content_grid as $field ) {
					if ( isset( $field->field_key ) && isset( $field->general_setting->field_name ) && $field->general_setting->field_name === $field_name ) {
						$field_data = array(
							'field_key'       => $field->field_key,
							'general_setting' => $field->general_setting,
							'advance_setting' => $field->advance_setting,
						);
					}
				}
			}
		}
	}
	return $field_data;
}

if ( ! function_exists( 'user_registration_get_form_fields_for_dropdown' ) ) {
	/**
	 * Get form fields array for dropdown
	 *
	 * @param int $form_id Form ID.
	 */
	function user_registration_get_form_fields_for_dropdown( $form_id ) {
		$get_all_fields = user_registration_pro_get_conditional_fields_by_form_id( $form_id, '' );
		$field_array    = array();
		if ( isset( $get_all_fields ) ) {
			foreach ( $get_all_fields as $key => $field ) {
				if ( $field['field_key'] === 'phone' ) {
					$field_array[ $key ] = $field['label'];
				}
			}
		}
		return $field_array;
	}
}

if ( ! function_exists( 'user_registration_pro_get_conditional_fields_by_form_id' ) ) {
	/**
	 * Get form fields by form id
	 *
	 * @param int    $form_id Form ID.
	 * @param string $selected_field_key Field Key.
	 */
	function user_registration_pro_get_conditional_fields_by_form_id( $form_id, $selected_field_key ) {
		$args      = array(
			'post_type'   => 'user_registration',
			'post_status' => array('publish', 'draft'),
			'post__in'    => array( $form_id ),
		);
		$post_data = get_posts( $args );
		// wrap all fields in array.
		$fields = array();
		if ( isset( $post_data[0]->post_content ) ) {
			$post_content_array = json_decode( $post_data[0]->post_content );

			if ( ! is_null( $post_content_array ) ) {
				foreach ( $post_content_array as $data ) {
					foreach ( $data as $single_data ) {
						foreach ( $single_data as $field_data ) {
							if (
								isset( $field_data->general_setting->field_name )
								&& isset( $field_data->general_setting->label )
							) {

								$strip_fields = array(
									'section_title',
									'html',
									'wysiwyg',
									'billing_address_title',
									'shipping_address_title',
									'stripe_gateway',
									'authorize_net_gateway',
									'profile_picture',
									'file',
								);

								if ( isset( $field_data->field_key ) && in_array( $field_data->field_key, $strip_fields, true ) ) {
									continue;
								}

								$fields[ $field_data->general_setting->field_name ] = array(
									'label'     => $field_data->general_setting->label,
									'field_key' => isset( $field_data->field_key ) ? $field_data->field_key : '',
								);
							}
						}
					}
				}
			}
		}
		// Unset selected meta key.
		unset( $fields[ $selected_field_key ] );
		return $fields;
	}
}

if ( ! function_exists( 'user_registration_pro_render_conditional_logic' ) ) {
	/**
	 * Render Conditional Logic in form settings of form builder.
	 *
	 * @param array  $connection Connection Data.
	 * @param string $integration Integration.
	 * @param int    $form_id Form ID.
	 * @return string
	 */
	function user_registration_pro_render_conditional_logic( $connection, $integration, $form_id ) {
		$output  = '<div class="ur_conditional_logic_container">';
		$output .= '<h4>' . esc_html__( 'Conditional Logic', 'user-registration' ) . '</h4>';
		$output .= '<div class="ur_use_conditional_logic_wrapper ur-check">';
		$checked = '';

		if ( isset( $connection['enable_conditional_logic'] ) && ur_string_to_bool( $connection['enable_conditional_logic'] ) ) {

			$checked = 'checked=checked';
		}
		$output .= '<div class="ur-toggle-section ur-form-builder-toggle">';
		$output .= '<span class="user-registration-toggle-form">';
		$output .= '<input class="ur-use-conditional-logic" type="checkbox" name="ur_use_conditional_logic" id="ur_use_conditional_logic" ' . $checked . '>';
		$output .= '<span class="slider round">';
		$output .= '</span>';
		$output .= '</span>';
		$output .= '<label>' . esc_html__( 'Use Conditional Logics', 'user-registration' ) . '</label>';
		$output .= '</div>';
		$output .= '</div>';

		$output                .= '<div class="ur_conditional_logic_wrapper" data-source="' . esc_attr( $integration ) . '">';
		$output                .= '<h4>' . esc_html__( 'Conditional Rules', 'user-registration' ) . '</h4>';
		$output                .= '<div class="ur-logic"><p>' . esc_html__( 'Send data only if the following matches.', 'user-registration' ) . '</p></div>';
		$output                .= '<div class="ur-conditional-wrapper">';
		$output                .= '<select class="ur_conditional_field" name="ur_conditional_field">';
		$get_all_fields         = user_registration_pro_get_conditional_fields_by_form_id( $form_id, '' );
		$selected_ur_field_type = '';

		if ( isset( $get_all_fields ) ) {

			foreach ( $get_all_fields as $key => $field ) {
				$selected_attr = '';

				if ( isset( $connection['conditional_logic_data']['conditional_field'] ) && $connection['conditional_logic_data']['conditional_field'] === $key ) {
					$selected_attr          = 'selected=selected';
					$selected_ur_field_type = $field['field_key'];
				}
				$output .= '<option data-type="' . esc_attr( $field['field_key'] ) . '" data-label="' . esc_attr( $field['label'] ) . '" value="' . esc_attr( $key ) . '" ' . $selected_attr . '>' . esc_html( $field['label'] ) . '</option>';
			}
		}
		$output .= '</select>';
		$output .= '<select class="ur-conditional-condition" name="ur-conditional-condition">';
		$output .= '<option value="is" ' . ( isset( $connection['conditional_logic_data']['conditional_operator'] ) && 'is' === $connection['conditional_logic_data']['conditional_operator'] ? 'selected' : '' ) . '> is </option>';
		$output .= '<option value="is_not" ' . ( isset( $connection['conditional_logic_data']['conditional_operator'] ) && 'is_not' === $connection['conditional_logic_data']['conditional_operator'] ? 'selected' : '' ) . '> is not </option>';
		$output .= '</select>';

		if ( 'checkbox' == $selected_ur_field_type || 'radio' == $selected_ur_field_type || 'select' == $selected_ur_field_type || 'country' == $selected_ur_field_type || 'billing_country' == $selected_ur_field_type || 'shipping_country' == $selected_ur_field_type || 'select2' == $selected_ur_field_type || 'multi_select2' == $selected_ur_field_type ) {
			$choices = user_registration_pro_get_checkbox_choices( $form_id, $connection['conditional_logic_data']['conditional_field'] );
			$output .= '<select name="ur-conditional-input" class="ur-conditional-input">';

			if ( is_array( $choices ) && array_filter( $choices ) ) {
				$output .= '<option>--select--</option>';

				foreach ( $choices as $key => $choice ) {
					$key           = 'country' == $selected_ur_field_type ? $key : $choice;
					$selectedvalue = isset( $connection['conditional_logic_data']['conditional_value'] ) && $connection['conditional_logic_data']['conditional_value'] == $key ? 'selected="selected"' : '';
					$output       .= '<option ' . $selectedvalue . ' value="' . esc_attr( $key ) . '">' . esc_html( $choice ) . '</option>';
				}
			} else {
				$selected = isset( $connection['conditional_logic_data']['conditional_value'] ) ? $connection['conditional_logic_data']['conditional_value'] : 0;
				$output  .= '<option value="1" ' . ( ur_string_to_bool( $selected ) ? 'selected="selected"' : '' ) . ' >' . esc_html__( 'Checked', 'user-registration' ) . '</option>';
			}
			$output .= '</select>';
		} else {
			$value   = isset( $connection['conditional_logic_data']['conditional_value'] ) ? $connection['conditional_logic_data']['conditional_value'] : '';
			$output .= '<input class="ur-conditional-input" type="text" name="ur-conditional-input" value="' . esc_attr( $value ) . '">';
		}
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		return $output;
	}
}


if ( ! function_exists( 'user_registration_pro_get_checkbox_choices' ) ) {
	/**
	 * Get Select and Checkbox Fields Choices
	 *
	 * @param int    $form_id Form ID.
	 * @param string $field_name Field Name.
	 * @return array $choices
	 */
	function user_registration_pro_get_checkbox_choices( $form_id, $field_name ) {

		$form_data = (object) user_registration_pro_get_field_data( $form_id, $field_name );
		/* Backward Compatibility. Modified since 1.5.7. To be removed later. */
		$advance_setting_choices = isset( $form_data->advance_setting->choices ) ? $form_data->advance_setting->choices : '';
		$advance_setting_options = isset( $form_data->advance_setting->options ) ? $form_data->advance_setting->options : '';
		/* Bacward Compatibility end.*/

		$choices = isset( $form_data->general_setting->options ) ? $form_data->general_setting->options : '';

		/* Backward Compatibility. Modified since 1.5.7. To be removed later. */
		if ( ! empty( $advance_setting_choices ) ) {
			$choices = explode( ',', $advance_setting_choices );
		} elseif ( ! empty( $advance_setting_options ) ) {
			$choices = explode( ',', $advance_setting_options );
			/* Backward Compatibility end. */
		} elseif ( 'country' === $form_data->field_key ) {
			$country = new UR_Form_Field_Country();
			$country->get_country();
			$choices = $country->get_country();
		}

		return $choices;
	}
}

if ( ! function_exists( 'user_registration_pro_get_field_data' ) ) {
	/**
	 * Get all fields data
	 *
	 * @param  int    $form_id    Form ID.
	 * @param  string $field_name Field Name.
	 * @return array    $field_data.
	 */
	function user_registration_pro_get_field_data( $form_id, $field_name ) {
		$args      = array(
			'post_type'   => 'user_registration',
			'post_status' => 'publish',
			'post__in'    => array( $form_id ),
		);
		$post_data = get_posts( $args );

		if ( isset( $post_data[0]->post_content ) ) {
			$post_content_array = json_decode( $post_data[0]->post_content );

			foreach ( $post_content_array as $data ) {
				foreach ( $data as $single_data ) {
					foreach ( $single_data as $field_data ) {
						isset( $field_data->general_setting->field_name ) ? $field_data->general_setting->field_name : '';
						if ( $field_data->general_setting->field_name === $field_name ) {
							return $field_data;
						}
					}
				}
			}
		}
	}
}

if ( ! function_exists( 'ur_install_extensions' ) ) {
	/**
	 * This function return boolean according to string to avoid colision of 1, true, yes.
	 *
	 * @param [string] $name Name of the extension.
	 * @param [string] $slug Slug of the extension.
	 * @throws Exception Extension Download and activation unsuccessful message.
	 */
	function ur_install_extensions( $name, $slug ) {
		try {
			$plugin = 'user-registration-pro' === $slug ? plugin_basename( sanitize_text_field( wp_unslash( $slug . '/user-registration.php' ) ) ) : plugin_basename( sanitize_text_field( wp_unslash( $slug . '/' . $slug . '.php' ) ) );
			$status = array(
				'install' => 'plugin',
				'slug'    => sanitize_key( wp_unslash( $slug ) ),
			);

			if ( ! current_user_can( 'install_plugins' ) ) {
				$status['errorMessage'] = esc_html__( 'Sorry, you are not allowed to install plugins on this site.', 'user-registration' );

				/* translators: %1$s: Activation error message */
				throw new Exception( sprintf( __( '<strong>Activation error:</strong> %1$s', 'user-registration' ), $status['errorMessage'] ) );
			}

			include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

			if ( file_exists( WP_PLUGIN_DIR . '/' . $slug ) ) {
				$plugin_data          = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
				$status['plugin']     = $plugin;
				$status['pluginName'] = $plugin_data['Name'];

				if ( current_user_can( 'activate_plugin', $plugin ) && is_plugin_inactive( $plugin ) ) {
					$result = activate_plugin( $plugin );

					if ( is_wp_error( $result ) ) {
						$status['errorCode']    = $result->get_error_code();
						$status['errorMessage'] = $result->get_error_message();

						/* translators: %1$s: Activation error message */
						throw new Exception( sprintf( __( '<strong>Activation error:</strong> %1$s', 'user-registration' ), $status['errorMessage'] ) );
					}

					$status['success'] = true;
					$status['message'] = $name . ' has been installed and activated successfully';

					return $status;
				}
			}

			$api = json_decode(
				UR_Updater_Key_API::version(
					array(
						'license'   => get_option( 'user-registration_license_key' ),
						'item_name' => $name,
					)
				)
			);

			if ( is_wp_error( $api ) ) {
				$status['errorMessage'] = $api->get_error_message();

				/* translators: %1$s: Activation error message */
				throw new Exception( sprintf( __( '<strong>Activation error:</strong> %1$s', 'user-registration' ), $status['errorMessage'] ) );
			}

			$status['pluginName'] = $api->name;
			$api->version         = isset( $api->new_version ) ? $api->new_version : '1.0.0';

			$skin     = new WP_Ajax_Upgrader_Skin();
			$upgrader = new Plugin_Upgrader( $skin );
			$result   = $upgrader->install( $api->download_link );

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				$status['debug'] = $skin->get_upgrade_messages();
			}

			if ( is_wp_error( $result ) ) {
				$status['errorCode']    = $result->get_error_code();
				$status['errorMessage'] = $result->get_error_message();

				/* translators: %1$s: Activation error message */
				throw new Exception( sprintf( __( '<strong>Activation error:</strong> %1$s', 'user-registration' ), $status['errorMessage'] ) );
			} elseif ( is_wp_error( $skin->result ) ) {
				$status['errorCode']    = $skin->result->get_error_code();
				$status['errorMessage'] = $skin->result->get_error_message();

				/* translators: %1$s: Activation error message */
				throw new Exception( sprintf( __( '<strong>Activation error:</strong> %1$s', 'user-registration' ), $status['errorMessage'] ) );
			} elseif ( $skin->get_errors()->get_error_code() ) {
				$status['errorMessage'] = $skin->get_error_messages();

				/* translators: %1$s: Activation error message */
				throw new Exception( sprintf( __( '<strong>Activation error:</strong> %1$s', 'user-registration' ), $status['errorMessage'] ) );
			} elseif ( is_null( $result ) ) {
				global $wp_filesystem;

				$status['errorCode']    = 'unable_to_connect_to_filesystem';
				$status['errorMessage'] = esc_html__( 'Unable to connect to the filesystem. Please confirm your credentials.', 'user-registration' );

				// Pass through the error from WP_Filesystem if one was raised.
				if ( $wp_filesystem instanceof WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
					$status['errorMessage'] = esc_html( $wp_filesystem->errors->get_error_message() );
				}

				/* translators: %1$s: Activation error message */
				throw new Exception( sprintf( __( '<strong>Activation error:</strong> %1$s', 'user-registration' ), $status['errorMessage'] ) );
			}

			$install_status = install_plugin_install_status( $api );

			if ( current_user_can( 'activate_plugin', $install_status['file'] ) ) {
				if ( is_plugin_inactive( $install_status['file'] ) ) {
					$status['activateUrl'] =
						esc_url_raw(
							add_query_arg(
								array(
									'action'   => 'activate',
									'plugin'   => $install_status['file'],
									'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $install_status['file'] ),
								),
								admin_url( 'admin.php?page=user-registration-addons' )
							)
						);
				} else {
					$status['deActivateUrl'] =
						esc_url_raw(
							add_query_arg(
								array(
									'action'   => 'deactivate',
									'plugin'   => $install_status['file'],
									'_wpnonce' => wp_create_nonce( 'deactivate-plugin_' . $install_status['file'] ),
								),
								admin_url( 'admin.php?page=user-registration-addons' )
							)
						);
				}
			}

			$status['success'] = true;
			$status['message'] = $name . ' has been installed and activated successfully';

			return $status;
		} catch ( Exception $e ) {

			$message           = $e->getMessage();
			$status['success'] = false;
			$status['message'] = $message;

			return $status;
		}
	}
}

add_action( 'user_registration_init', 'ur_profile_picture_migration_script' );

if ( ! function_exists( 'ur_profile_picture_migration_script' ) ) {

	/**
	 * Update usermeta from profile_pic_url to attachemnt id and move files to new directory.
	 *
	 * @since 1.5.0.
	 */
	function ur_profile_picture_migration_script() {

		if ( ! get_option( 'ur_profile_picture_migrated', false ) ) {

			$users = get_users(
				array(
					'meta_key' => 'user_registration_profile_pic_url',
				)
			);

			foreach ( $users as $user ) {
				$user_registration_profile_pic_url = get_user_meta( $user->ID, 'user_registration_profile_pic_url', true );

				if ( ! is_numeric( $user_registration_profile_pic_url ) ) {
					$user_registration_profile_pic_attachment = attachment_url_to_postid( $user_registration_profile_pic_url );
					if ( 0 != $user_registration_profile_pic_attachment ) {
						update_user_meta( $user->ID, 'user_registration_profile_pic_url', absint( $user_registration_profile_pic_attachment ) );
					}
				}
			}

			update_option( 'ur_profile_picture_migrated', true );
		}
	}
}

add_action( 'user_registration_init', 'ur_size_to_limit_length_migration_script' );

if ( ! function_exists( 'ur_size_to_limit_length_migration_script' ) ) {

	/**
	 * Update text field advance settings from size to limit length.
	 *
	 * @since 3.1.2.
	 */
	function ur_size_to_limit_length_migration_script() {

		if ( ! get_option( 'ur_size_to_limit_length_migrated', false ) ) {

			$all_forms = ur_get_all_user_registration_form();

			foreach ( $all_forms as $key => $value ) {

				$form_id            = $key;
				$post               = ( $form_id ) ? get_post( $form_id ) : '';
				$post_content       = isset( $post->post_content ) ? $post->post_content : '';
				$post_content_array = json_decode( $post_content );

				foreach ( $post_content_array as $post_content_row ) {
					foreach ( $post_content_row as $post_content_grid ) {
						foreach ( $post_content_grid as $field ) {

							if ( isset( $field->field_key ) && 'text' === $field->field_key ) {
								if ( isset( $field->advance_setting ) ) {
									if ( isset( $field->advance_setting->size ) && ! empty( $field->advance_setting->size ) ) {
										$field->advance_setting->limit_length             = true;
										$field->advance_setting->limit_length_limit_count = $field->advance_setting->size;
										$field->advance_setting->limit_length_limit_mode  = 'characters';
									}
								}
							}
						}
					}
					$post_content       = json_encode( $post_content_array );
					$post->post_content = $post_content;
				}
				wp_update_post( $post );
			}

			update_option( 'ur_size_to_limit_length_migrated', true );
		}
	}
}

add_action( 'delete_user', 'ur_delete_user_files_on_user_delete', 10, 3 );

if ( ! function_exists( 'ur_delete_user_files_on_user_delete' ) ) {

	/**
	 * Delete user uploaded files when user is deleted.
	 *
	 * @param [type] $user_id User Id.
	 * @param [type] $reassign  Reassign to another user ( admin ).
	 * @param [type] $user User Data.
	 */
	function ur_delete_user_files_on_user_delete( $user_id, $reassign, $user ) {

		// Return if reassign is set.
		if ( null !== $reassign ) {
			return;
		}

		// Delete user uploaded file when user is deleted.
		if ( class_exists( 'URFU_Uploaded_Data' ) ) {
			$post = get_post( ur_get_form_id_by_userid( $user_id ) );

			$form_data_object = json_decode( $post->post_content );

			$file_fields = URFU_Uploaded_Data::get_file_field( $form_data_object );

			foreach ( $file_fields as $field ) {

				$meta_key = isset( $field['key'] ) ? $field['key'] : '';

				$attachment_ids = get_user_meta( $user->ID, 'user_registration_' . $meta_key, true );

				if ( is_string( $attachment_ids ) ) {
					$attachment_ids = explode( ',', $attachment_ids );
				}

				foreach ( $attachment_ids as $attachment_id ) {
					$file_path = get_attached_file( $attachment_id );

					if ( file_exists( $file_path ) ) {
						unlink( $file_path );
					}
				}
			}
		}

		// Delete user uploaded profile image when user is deleted.
		$profile_pic_attachment_id = get_user_meta( $user_id, 'user_registration_profile_pic_url', true );

		$pic_path = get_attached_file( $profile_pic_attachment_id );

		if ( file_exists( $pic_path ) ) {
			unlink( $pic_path );
		}
	}
}

if ( ! function_exists( 'ur_format_field_values' ) ) {

	/**
	 * Get field type by meta key
	 *
	 * @param int    $field_meta_key Field key or meta key.
	 * @param string $field_value Field's value .
	 */
	function ur_format_field_values( $field_meta_key, $field_value ) {
		if ( strpos( $field_meta_key, 'user_registration_' ) ) {
			$field_meta_key = substr( $field_meta_key, 0, strpos( $field_meta_key, 'user_registration_' ) );
		}

		$user_id = isset( $_GET['user'] ) ? sanitize_text_field( wp_unslash( $_GET['user'] ) ) : get_current_user_id(); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$user_id = isset( $_GET['user_id'] ) ? sanitize_text_field( wp_unslash( $_GET['user_id'] ) ) : $user_id; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$form_id = isset( $_POST['form_id'] ) ? sanitize_text_field( wp_unslash( $_POST['form_id'] ) ) : ur_get_form_id_by_userid( $user_id ); //phpcs:ignore.

		$field_name = ur_get_field_data_by_field_name( $form_id, $field_meta_key );

		$field_key   = isset( $field_name['field_key'] ) ? $field_name['field_key'] : '';
		$field_value = ur_format_field_values_using_field_key( $field_key, $field_value );

		return $field_value;
	}
}

if ( ! function_exists( 'ur_format_field_values_using_field_key' ) ) {
	function ur_format_field_values_using_field_key( $field_key, $field_value ) {

		switch ( $field_key ) {
			case 'checkbox':
			case 'multi_select2':
				if ( empty( $field_value ) ) {
					$field_value = '';
				} elseif ( is_array( $field_value ) && ! empty( $field_value ) ) {
					$field_value = implode( ', ', $field_value );
				} elseif ( ! empty( json_decode( $field_value ) ) ) { // phpcs:ignore;
					$field_value = is_array( json_decode( $field_value ) ) ? implode( ', ', json_decode( $field_value ) ) : $field_value;
				}
				break;
			case 'country':
				$countries = UR_Form_Field_Country::get_instance()->get_country();
				if ( ! isset( $countries[ $field_value ] ) ) {
					$key = array_search( $field_value, $countries, true );
					if ( $key ) {
						$field_value = $key;
					}
				}
				$field_value = isset( $countries[ $field_value ] ) ? $countries[ $field_value ] : '';
				break;
			case 'file':
				$attachment_ids = is_array( $field_value ) ? $field_value : explode( ',', $field_value );
				$links          = array();

				foreach ( $attachment_ids as $attachment_id ) {
					if ( is_numeric( $attachment_id ) ) {
						$attachment_url = '<a href="' . wp_get_attachment_url( $attachment_id ) . '">' . basename( get_attached_file( $attachment_id ) ) . '</a>';
						array_push( $links, $attachment_url );
					} elseif ( ur_is_valid_url( $attachment_id ) ) {
						$attachment_url = '<a href="' . $attachment_id . '">' . $attachment_id . '</a>';
						array_push( $links, $attachment_url );
					} else {
						array_push( $links, $attachment_id );
					}
				}
				$field_value = implode( ', ', $links );

				break;
			case 'privacy_policy':
				if ( ur_string_to_bool( $field_value ) ) {
					$field_value = 'Checked';
				} else {
					$field_value = 'Not Checked';
				}
				break;
			case 'wysiwyg':
				$field_value = html_entity_decode( $field_value );
				break;
			case 'profile_picture':
				$field_value = '<img class="profile-preview" alt="Profile Picture" width="50px" height="50px" src="' . ( is_numeric( $field_value ) ? esc_url( wp_get_attachment_url( $field_value ) ) : esc_url( $field_value ) ) . '" />';
				$field_value = wp_kses_post( $field_value );
				break;
			case 'signature':
				$field_value = '<img class="profile-preview" alt="Signature" width="50px" height="50px" src="' . ( is_numeric( $field_value ) ? esc_url( wp_get_attachment_url( $field_value ) ) : esc_url( $field_value ) ) . '" />';
				$field_value = wp_kses_post( $field_value );
				break;
			default:
				$field_value = $field_value;
				break;
		}

		return $field_value;
	}
}

if ( ! function_exists( 'ur_find_my_account_in_page' ) ) {

	/**
	 * Find My Account Shortcode.
	 *
	 * @param int $login_page_id Login Page ID.
	 * @return int If matched then 1 else 0.
	 * @since  2.2.7
	 */
	function ur_find_my_account_in_page( $login_page_id ) {
		global $wpdb;
		$post_table      = $wpdb->prefix . 'posts';
		$post_meta_table = $wpdb->prefix . 'postmeta';

		$matched = $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(*) FROM {$post_table} WHERE ID = '{$login_page_id}' AND ( post_content LIKE '%[user_registration_login%' OR post_content LIKE '%[user_registration_my_account%' OR post_content LIKE '%[woocommerce_my_account%' OR post_content LIKE '%<!-- wp:user-registration/myaccount%' OR post_content LIKE '%<!-- wp:user-registration/login%')" ) //phpcs:ignore.
		);

		if ( $matched <= 0 ) {
			$matched = $wpdb->get_var(
				$wpdb->prepare( "SELECT COUNT(*) FROM {$post_meta_table} WHERE post_id = '{$login_page_id}' AND ( meta_value LIKE '%[user_registration_login%' OR meta_value LIKE '%[user_registration_my_account%' OR meta_value LIKE '%[woocommerce_my_account%' OR meta_value LIKE '%<!-- wp:user-registration/myaccount%' OR meta_value LIKE '%<!-- wp:user-registration/login%' )" ) //phpcs:ignore.
			);
		}
		/**
		 * Filters the result of finding "My Account" in a page.
		 *
		 * @param bool  $matched         The result of finding "My Account" in a page.
		 * @param int   $login_page_id   The ID of the associated login page.
		 */
		$matched = apply_filters( 'user_registration_find_my_account_in_page', $matched, $login_page_id );

		return $matched;
	}
}

if ( ! function_exists( 'ur_find_lost_password_in_page' ) ) {

	/**
	 * Find Lost Password Shortcode.
	 *
	 * @param int $lost_password_page_id Lost Password Page ID.
	 * @return int If matched then 1 else 0.
	 * @since  4.0
	 */
	function ur_find_lost_password_in_page( $lost_password_page_id ) {
		global $wpdb;
		$post_table      = $wpdb->prefix . 'posts';
		$post_meta_table = $wpdb->prefix . 'postmeta';

		$matched = $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(*) FROM {$post_table} WHERE ID = '{$lost_password_page_id}' AND ( post_content LIKE '%[user_registration_lost_password%' OR post_content LIKE '%<!-- wp:user-registration/lost_password%' OR post_content LIKE '%<!-- wp:user-registration/lost_password%')" ) //phpcs:ignore.
		);

		if ( $matched <= 0 ) {
			$matched = $wpdb->get_var(
				$wpdb->prepare( "SELECT COUNT(*) FROM {$post_meta_table} WHERE post_id = '{$lost_password_page_id}' AND ( meta_value LIKE '%[user_registration_lost_password%' OR meta_value LIKE '%<!-- wp:user-registration/lost_password%' OR meta_value LIKE '%<!-- wp:user-registration/lost_password%' )" ) //phpcs:ignore.
			);
		}
		/**
		 * Filters the result of finding "Lost Password" in a page.
		 *
		 * @param bool  $matched         The result of finding "Lost Password" in a page.
		 * @param int   $lost_password_page_id   The ID of the associated lost password page.
		 */
		$matched = apply_filters( 'user_registration_find_lost_password_in_page', $matched, $lost_password_page_id );

		return $matched;
	}
}

if ( ! function_exists( 'ur_get_license_plan' ) ) {

	/**
	 * Get a license plan.
	 *
	 * @return bool|object Plan on success, false on failure.
	 * @since  2.2.4
	 */
	function ur_get_license_plan() {
		$license_key = get_option( 'user-registration_license_key' );

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( $license_key && is_plugin_active( 'user-registration-pro/user-registration.php' ) ) {
			$license_data = get_transient( 'ur_pro_license_plan' );

			if ( false === $license_data ) {
				$license_data = json_decode(
					UR_Updater_Key_API::check(
						array(
							'license' => $license_key,
						)
					)
				);

				if ( ! empty( $license_data->item_name ) ) {
					$license_data->item_plan = strtolower( str_replace( 'LifeTime', '', str_replace( 'User Registration', '', $license_data->item_name ) ) );
					set_transient( 'ur_pro_license_plan', $license_data, WEEK_IN_SECONDS );
				}
			}

			return isset( $license_data ) ? $license_data : false;
		}

		return false;
	}
}

if ( ! function_exists( 'ur_get_json_file_contents' ) ) {

	/**
	 * UR Get json file contents.
	 *
	 * @param mixed $file File path.
	 * @param mixed $to_array Returned data in array.
	 * @since  2.2.4
	 */
	function ur_get_json_file_contents( $file, $to_array = false ) {
		if ( $to_array ) {
			return json_decode( ur_file_get_contents( $file ), true );
		}
		return json_decode( ur_file_get_contents( $file ) );
	}
}

if ( ! function_exists( 'ur_is_valid_url' ) ) {

	/**
	 * UR file get contents.
	 *
	 * @param mixed $url URL.
	 */
	function ur_is_valid_url( $url ) {
		// Must start with http:// or https://.
		if ( 0 !== strpos( $url, 'http://' ) && 0 !== strpos( $url, 'https://' ) ) {
			return false;
		}

		// Must pass validation.
		if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'ur_file_get_contents' ) ) {

	/**
	 * UR file get contents.
	 *
	 * @param mixed $file File path.
	 * @since  2.2.4
	 */
	function ur_file_get_contents( $file ) {

		if ( $file ) {
			global $wp_filesystem;
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
			$local_file = preg_replace( '/\\\\|\/\//', '/', plugin_dir_path( UR_PLUGIN_FILE ) . $file );

			if ( $wp_filesystem->exists( $local_file ) ) {
				$response = $wp_filesystem->get_contents( $local_file );
				return $response;
			}
		}
		return;
	}
}

if ( ! function_exists( 'crypt_the_string' ) ) {
	/**
	 * Encrypt/Decrypt the provided string.
	 * Encrypt while setting token and updating to database, decrypt while comparing the stored token.
	 *
	 * @param  string $string String to encrypt/decrypt.
	 * @param  string $action Encrypt/decrypt action. 'e' for encrypt and 'd' for decrypt.
	 * @return string Encrypted/Decrypted string.
	 */
	function crypt_the_string( $string, $action = 'e' ) {
		$secret_key = get_option( 'ur_secret_key' );
		$secret_iv  = get_option( 'ur_secret_iv' );

		if ( empty( $secret_key ) || empty( $secret_iv ) ) {
			$secret_key = ur_generate_random_key();
			$secret_iv  = ur_generate_random_key();
			update_option( 'ur_secret_key', $secret_key );
			update_option( 'ur_secret_iv', $secret_iv );
		}

		$output         = false;
		$encrypt_method = 'AES-256-CBC';
		$key            = hash( 'sha256', $secret_key );
		$iv             = substr( hash( 'sha256', $secret_iv ), 0, 16 );

		if ( 'e' == $action ) {
			if ( function_exists( 'openssl_encrypt' ) ) {
				$output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
			} else {
				$output = base64_encode( $string );
			}
		} elseif ( 'd' == $action ) {
			if ( function_exists( 'openssl_decrypt' ) ) {
				$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
			} else {
				$output = base64_decode( $string );
			}
		}

		return $output;
	}
} //phpcs:ignore.
if ( ! function_exists( 'ur_generate_random_key' ) ) {
	/**
	 * Function to generate the random key.
	 *
	 * @since 3.0.2.1
	 */
	function ur_generate_random_key() {
		$length              = 32;
		$allow_special_chars = true;
		$key                 = wp_generate_password( $length, $allow_special_chars );
		return $key;
	}
}

if ( ! function_exists( 'ur_clean_tmp_files' ) ) {
	/**
	 * Clean up the tmp folder - remove all old files every day (filterable interval).
	 */
	function ur_clean_tmp_files() {
		$files = glob( trailingslashit( ur_get_tmp_dir() ) . '*' );

		if ( ! is_array( $files ) || empty( $files ) ) {
			return;
		}
		/**
		 * Filters the lifespan of temporary files cleanup.
		 *
		 * @param int $lifespan The default lifespan of temporary files cleanup in seconds.
		 * @return int Modified lifespan for temporary files cleanup in seconds.
		 */
		$lifespan = (int) apply_filters( 'user_registration_clean_tmp_files_lifespan', DAY_IN_SECONDS );

		foreach ( $files as $file ) {
			if ( ! is_file( $file ) ) {
				continue;
			}

			// In some cases filemtime() can return false, in that case - pretend this is a new file and do nothing.
			$modified = (int) filemtime( $file );
			if ( empty( $modified ) ) {
				$modified = time();
			}

			if ( ( time() - $modified ) >= $lifespan ) {
				@unlink( $file ); // phpcs:ignore.WordPress.PHP.NoSilencedErrors.Discouraged
			}
		}
	}
}

if ( ! function_exists( 'ur_get_tmp_dir' ) ) {
	/**
	 * Get tmp dir for files.
	 *
	 * @return string
	 */
	function ur_get_tmp_dir() {
		$tmp_root = UR_UPLOAD_PATH . 'temp-uploads';

		if ( ! file_exists( $tmp_root ) || ! wp_is_writable( $tmp_root ) ) {
			wp_mkdir_p( $tmp_root );
		}

		$index = trailingslashit( $tmp_root ) . 'index.html';

		if ( ! file_exists( $index ) ) {
			file_put_contents( $index, '' ); // phpcs:ignore.WordPress.WP.AlternativeFunctions
		}

		return $tmp_root;
	}
}

if ( ! function_exists( 'ur_get_user_roles' ) ) {
	/**
	 * Returns an array of all roles associated with the user.
	 *
	 * @param [int] $user_id User Id.
	 *
	 * @returns array
	 */
	function ur_get_user_roles( $user_id ) {
		$roles = array();

		if ( $user_id ) {
			$user_meta = get_userdata( $user_id );
			$roles     = isset( $user_meta->roles ) ? $user_meta->roles : array();
		}

		$user_roles = array_map( 'ucfirst', $roles );

		return $user_roles;
	}
}

if ( ! function_exists( 'ur_upload_profile_pic' ) ) {
	/**
	 * Upload Profile Picture
	 *
	 * @param [array] $valid_form_data Valid Form Data.
	 * @param [int]   $user_id User Id.
	 */
	function ur_upload_profile_pic( $valid_form_data, $user_id ) {
		$attachment_id = array();
		/**
		 * Filters the URL for uploading profile pictures during user registration.
		 *
		 * This filter hook allows developers to customize the URL for uploading profile pictures
		 * during the user registration process. By default, the profile pictures are uploaded
		 * to the 'profile-pictures' directory within the UR_UPLOAD_PATH constant. Developers can
		 * use this filter to modify the upload URL based on specific requirements or preferences.
		 *
		 * @param string $upload_url The default URL for uploading profile pictures.
		 */
		$upload_path = apply_filters( 'user_registration_profile_pic_upload_url', UR_UPLOAD_PATH . 'profile-pictures' ); /*Get path of upload dir of WordPress*/

		// Checks if the upload directory exists and create one if not.
		if ( ! file_exists( $upload_path ) ) {
			wp_mkdir_p( $upload_path );
		}
		$valid_extensions = array( 'image/jpeg', 'image/jpg', 'image/gif', 'image/png' );
		$upload_file      = $valid_form_data['profile_pic_url']->value;
		$valid_ext        = array();

		foreach ( $valid_extensions as $key => $value ) {
			$image_extension   = explode( '/', $value );
			$valid_ext[ $key ] = isset( $image_extension[1] ) ? $image_extension[1] : '';

			if ( 'jpeg' === $valid_ext[ $key ] ) {
				$index               = count( $valid_extensions );
				$valid_ext[ $index ] = 'jpg';
			}
		}

		if ( ! is_numeric( $upload_file ) ) {
			$upload = ur_maybe_unserialize( crypt_the_string( $upload_file, 'd' ) );
			if ( function_exists( 'mime_content_type' ) ) {
				$upload_file_type = isset( $upload['file_path'] ) ? mime_content_type( $upload['file_path'] ) : '';
			} else {
				$upload_file_info = isset( $upload['file_path'] ) ? wp_check_filetype( $upload['file_path'] ) : '';
				$upload_file_type = ! empty( $upload_file_info ) ? $upload_file_info['type'] : '';
			}

			if ( isset( $upload['file_name'] ) && isset( $upload['file_path'] ) && isset( $upload['file_extension'] ) && in_array( $upload_file_type, $valid_extensions ) && in_array( $upload['file_extension'], $valid_ext ) ) {
				$upload_path = $upload_path . '/';
				$file_name   = wp_unique_filename( $upload_path, $upload['file_name'] );
				$file_path   = $upload_path . sanitize_file_name( $file_name );
				// Check the type of file. We'll use this as the 'post_mime_type'.
				$filetype = wp_check_filetype( basename( $file_name ), null );
				$moved    = '';

				if ( basename( $upload['file_path'] ) === $upload['file_name'] ) {
					$moved = rename( $upload['file_path'], $file_path );
				}

				if ( $moved ) {
					$attachment_id = wp_insert_attachment(
						array(
							'guid'           => $file_path,
							'post_mime_type' => $filetype['type'],
							'post_title'     => preg_replace( '/\.[^.]+$/', '', sanitize_file_name( $file_name ) ),
							'post_content'   => '',
							'post_status'    => 'inherit',
						),
						$file_path
					);

					if ( ! is_wp_error( $attachment_id ) ) {
						include_once ABSPATH . 'wp-admin/includes/image.php';

						// Generate and save the attachment metas into the database.
						wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $file_path ) );
					}
				}
			}
		} else {
			$attachment_id = $upload_file;
		}
		$attachment_id = ! empty( $attachment_id ) ? $attachment_id : '';
		update_user_meta( $user_id, 'user_registration_profile_pic_url', $attachment_id );
	}
}

/**
 * Check given string is valid url or not.
 */
if ( ! function_exists( 'ur_is_valid_url' ) ) {
	/**
	 * Checks if url is valid.
	 *
	 * @param [string] $url URL.
	 * @return bool
	 */
	function ur_is_valid_url( $url ) {

		// Must start with http:// or https://.
		if ( 0 !== strpos( $url, 'http://' ) && 0 !== strpos( $url, 'https://' ) ) {
			return false;
		}

		// Must pass validation.
		if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'ur_option_checked' ) ) {
	/**
	 * Returns whether a setting checkbox or toggle is enabled.
	 *
	 * @param string $option_name Option Name.
	 * @param string $default Default Value.
	 * @return boolean
	 */
	function ur_option_checked( $option_name = '', $default = '' ) {

		if ( empty( $option_name ) ) {
			return false;
		}

		$option_value = get_option( $option_name, $default );

		// Handling Backward Compatibility.
		if ( 'yes' === $option_value ) {
			return true;
		} elseif ( 'no' === $option_value ) {
			return false;
		}

		return ur_string_to_bool( $option_value );
	}
}

if ( ! function_exists( 'ur_check_captch_keys' ) ) {
	/**
	 * Check the site key and secret key for the selected captcha type, are valid or not.
	 *
	 * @return bool
	 */
	function ur_check_captch_keys( $context = 'register', $form_id = 0, $form_save_action = false ) {
		$recaptcha_type      = get_option( 'user_registration_captcha_setting_recaptcha_version', 'v2' );
		$invisible_recaptcha = ur_option_checked( 'user_registration_captcha_setting_invisible_recaptcha_v2', false );

		if ( 'login' === $context ) {
			$recaptcha_type = get_option( 'user_registration_login_options_configured_captcha_type', $recaptcha_type );
		} elseif ( 'register' === $context && $form_id ) {
			if ( $form_save_action ) {
				if ( isset( $_POST['data']['form_setting_data'] ) ) {
					foreach ( $_POST['data']['form_setting_data'] as $value ) {
						if ( 'user_registration_form_setting_configured_captcha_type' === $value['name'] ) {
							$recaptcha_type = $value['value'];
						}
					}
				}
			} else {
				$recaptcha_type = ur_get_single_post_meta( $form_id, 'user_registration_form_setting_configured_captcha_type', $recaptcha_type );
			}
		}

		$site_key   = '';
		$secret_key = '';

		if ( 'v2' === $recaptcha_type ) {
			if ( $invisible_recaptcha ) {
				$site_key   = get_option( 'user_registration_captcha_setting_recaptcha_invisible_site_key' );
				$secret_key = get_option( 'user_registration_captcha_setting_recaptcha_invisible_site_secret' );
			} else {
				$site_key   = get_option( 'user_registration_captcha_setting_recaptcha_site_key' );
				$secret_key = get_option( 'user_registration_captcha_setting_recaptcha_site_secret' );
			}
		} elseif ( 'v3' === $recaptcha_type ) {
			$site_key   = get_option( 'user_registration_captcha_setting_recaptcha_site_key_v3' );
			$secret_key = get_option( 'user_registration_captcha_setting_recaptcha_site_secret_v3' );
		} elseif ( 'hCaptcha' === $recaptcha_type ) {
			$site_key   = get_option( 'user_registration_captcha_setting_recaptcha_site_key_hcaptcha' );
			$secret_key = get_option( 'user_registration_captcha_setting_recaptcha_site_secret_hcaptcha' );
		} elseif ( 'cloudflare' === $recaptcha_type ) {
			$site_key   = get_option( 'user_registration_captcha_setting_recaptcha_site_key_cloudflare' );
			$secret_key = get_option( 'user_registration_captcha_setting_recaptcha_site_secret_cloudflare' );
		}

		if ( ! empty( $site_key ) && ! empty( $secret_key ) ) {
			return true;
		}

		return false;
	}
}


if ( ! function_exists( 'ur_premium_settings_tab' ) ) {

	/**
	 * Settings tab list to display as premium tabs.
	 *
	 * @since 3.0
	 */
	function ur_premium_settings_tab() {

		$premium_tabs = array(
			'woocommerce'                            => array(
				'label'  => esc_html__( 'WooCommerce', 'user-registration' ),
				'plugin' => 'user-registration-woocommerce',
				'plan'   => array( 'personal', 'plus', 'professional', 'themegrill agency' ),
				'name'   => esc_html__( 'User Registration - WooCommerce', 'user-registration' ),
			),
			'file_upload'                            => array(
				'label'  => esc_html__( 'File Uploads', 'user-registration' ),
				'plugin' => 'user-registration-file-upload',
				'plan'   => array( 'personal', 'plus', 'professional', 'themegrill agency' ),
				'name'   => esc_html__( 'User Registration - File Upload', 'user-registration' ),
			),
			'user-registration-customize-my-account' => array(
				'label'  => esc_html__( 'Customize My Account', 'user-registration' ),
				'plugin' => 'user-registration-customize-my-account',
				'plan'   => array( 'plus', 'professional', 'themegrill agency' ),
				'name'   => esc_html__( 'User Registration customize my account', 'user-registration' ),
			),
		);
		/**
		 * Filters the premium settings tabs for customization or extension.
		 *
		 * The 'user_registration_premium_settings_tab' filter allows developers to modify
		 * or extend the premium settings tabs defined in the plugin. Developers can customize
		 * the array of premium tabs based on their specific requirements, adding, removing,
		 * or altering tabs as needed.
		 *
		 * @param array $premium_tabs An array of premium settings tabs for User Registration.
		 */
		return apply_filters( 'user_registration_premium_settings_tab', $premium_tabs );
	}
}

add_action( 'user_registration_settings_tabs', 'ur_display_premium_settings_tab' );

if ( ! function_exists( 'ur_display_premium_settings_tab' ) ) {

	/**
	 * Method to display premium settings tabs.
	 *
	 * @since 3.0
	 */
	function ur_display_premium_settings_tab() {
		$license_data    = ur_get_license_plan();
		$license_plan    = ! empty( $license_data->item_plan ) ? $license_data->item_plan : false;
		$premium_tabs    = ur_premium_settings_tab();
		$tabs_to_display = array();
		$tab_html        = '';

		foreach ( $premium_tabs as $tab => $detail ) {
			$tooltip_html = '';
			$button       = '';
			if ( 'woocommerce' === $tab && ! is_plugin_active( $detail['plugin'] . '/' . $detail['plugin'] . '.php' ) ) {
				continue;
			}

			if ( ! empty( $license_plan ) ) {
				$license_plan = trim( str_replace('lifetime', '', strtolower( $license_plan ) ) );
				if ( ! in_array( $license_plan, $detail['plan'], true ) ) {
					if ( is_plugin_active( $detail['plugin'] . '/' . $detail['plugin'] . '.php' ) ) {
						continue;
					}

					/* translators: %s: License Plan Name. */
					$tooltip_html = sprintf( __( 'You have been subscribed to %s plan. Please upgrade to higher plans to use this feature.', 'user-registration' ), ucfirst( $license_plan ) );
					$button       = '<a rel="noreferrer noopener" target="_blank" href="https://wpuserregistration.com/pricing/?utm_source=settings-sidebar-right&utm_medium=premium-addon-tooltip&utm_campaign=' . UR()->utm_campaign . '">' . esc_html__( 'Upgrade Plan', 'user-registration' ) . '</a>';
					array_push( $tabs_to_display, $tab );
				} else {
					$plugin_name = $detail['name'];
					$action      = '';

					if ( file_exists( WP_PLUGIN_DIR . '/' . $detail['plugin'] ) ) {
						if ( ! is_plugin_active( $detail['plugin'] . '/' . $detail['plugin'] . '.php' ) ) {
							$action = 'Activate';
						} else {
							continue;
						}
					} else {
						$action = 'Install';
					}

					/* translators: %s: Addon Name. */
					$tooltip_html = sprintf( __( 'Please %1$s %2$s addon to use this feature.', 'user-registration' ), $action, ucwords( str_replace( '-', ' ', $detail['plugin'] ) ) );

					/* translators: %s: Action Name. */
					$button = '<a href="#" class="user-registration-settings-addon-' . strtolower( $action ) . '" data-slug="' . $detail['plugin'] . '" data-name="' . $plugin_name . '">' . sprintf( esc_html__( '%s Addon', 'user-registration' ), $action ) . '</a>';
					array_push( $tabs_to_display, $tab );
				}
			} else {

				if ( is_plugin_active( $detail['plugin'] . '/' . $detail['plugin'] . '.php' ) ) {
					continue;
				}

				$tooltip_html = __( 'You are currently using the free version of our plugin. Please upgrade to premium version to use this feature.', 'user-registration' );
				$button       = '<a rel="noreferrer noopener" target="_blank" href="https://wpuserregistration.com/pricing/?utm_source=settings-sidebar-right&utm_medium=premium-addon-tooltip&utm_campaign=' . UR()->utm_campaign . '">' . esc_html__( 'Upgrade to Pro', 'user-registration' ) . '</a>';
				array_push( $tabs_to_display, $tab );
			}

			if ( in_array( $tab, $tabs_to_display, true ) ) {
				$tab_html .= '<button class="nav-tab ur-nav__link ur-nav-premium" disabled>';
				$tab_html .= '<span class="ur-tooltip">' . esc_html( $tooltip_html ) . wp_kses_post( $button ) . '</span>';
				$tab_html .= '<span class="ur-nav__link-icon">';
				$tab_html .= ur_file_get_contents( '/assets/images/settings-icons/' . $tab . '.svg' );
				$tab_html .= '</span>';
				$tab_html .= '<span class="ur-nav__link-label">';
				$tab_html .= '<p>' . esc_html( $detail['label'] ) . '</p>';
				$tab_html .= '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="0.5" y="0.5" width="19" height="19" rx="2.5" fill="#5462FF" stroke="#5462FF"/><path d="M10 5L13 13H7L10 5Z" fill="#EFEFEF"/><path fill-rule="evenodd" clip-rule="evenodd" d="M5 7L5.71429 13H14.2857L15 7L10 11.125L5 7ZM14.2857 13.5714H5.71427V15H14.2857V13.5714Z" fill="white"/></svg>';
				$tab_html .= '</span>';
				$tab_html .= '</button>';
			}
		}

		echo $tab_html; // phpcs:ignore.WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'ur_is_ajax_login_enabled' ) ) {
	/**
	 * Check whether the ajax login is enabled or not.
	 *
	 * @return bool
	 */
	function ur_is_ajax_login_enabled() {
		return ur_option_checked( 'ur_login_ajax_submission', false );
	}
}

if ( ! function_exists( 'ur_process_login' ) ) {
	/**
	 * Process the login form.
	 *
	 * @param string $nonce_value Nonce.
	 * @throws Exception Login errors.
	 *
	 * @since 3.0
	 */
	function ur_process_login( $nonce_value ) {
		try {
			// Custom error messages.
			$messages = array(
				'empty_username'   => get_option( 'user_registration_message_username_required', esc_html__( 'Username is required.', 'user-registration' ) ),
				'empty_password'   => get_option( 'user_registration_message_empty_password', null ),
				'invalid_username' => get_option( 'user_registration_message_invalid_username', null ),
				'unknown_email'    => get_option( 'user_registration_message_unknown_email', esc_html__( 'A user could not be found with this email address.', 'user-registration' ) ),
				'pending_approval' => get_option( 'user_registration_message_pending_approval', null ),
				'denied_access'    => get_option( 'user_registration_message_denied_account', null ),
				'user_disabled'    => esc_html__( 'Sorry! You are disabled.Please Contact Your Administrator.', 'user-registration' ),
			);

			$post = $_POST; // phpcs:ignore.

			$recaptcha_value     = isset( $post['g-recaptcha-response'] ) ? ur_clean( wp_unslash( $post['g-recaptcha-response'] ) ) : '';
			$captcha_response    = isset( $post['CaptchaResponse'] ) ? $post['CaptchaResponse'] : ''; //phpcs:ignore.
			$recaptcha_enabled   = ur_option_checked( 'user_registration_login_options_enable_recaptcha', false );
			$recaptcha_type      = get_option( 'user_registration_captcha_setting_recaptcha_version', 'v2' );
			$recaptcha_type      = get_option( 'user_registration_login_options_configured_captcha_type', $recaptcha_type );
			$invisible_recaptcha = ur_option_checked( 'user_registration_captcha_setting_invisible_recaptcha_v2', false );

			$login_data = array(
				'user_password' => isset( $post['password'] ) ? $post['password'] : '', //phpcs:ignore.
				'remember'      => isset( $post['rememberme'] ),
			);

			$username = isset( $post['username'] ) ? trim( sanitize_user( wp_unslash( $post['username'] ) ) ) : '';

			// Check if the entered value is a valid email address.
			$user_details = null;
			if ( is_email( $username ) ) {
				$user_details = get_user_by( 'email', $username );
			} else {
				$user_details = get_user_by( 'login', $username );
			}

			if ( 'v2' === $recaptcha_type && ! $invisible_recaptcha ) {
				$site_key   = get_option( 'user_registration_captcha_setting_recaptcha_site_key' );
				$secret_key = get_option( 'user_registration_captcha_setting_recaptcha_site_secret' );
			} elseif ( 'v2' === $recaptcha_type && $invisible_recaptcha ) {
				$site_key   = get_option( 'user_registration_captcha_setting_recaptcha_invisible_site_key' );
				$secret_key = get_option( 'user_registration_captcha_setting_recaptcha_invisible_site_secret' );
			} elseif ( 'v3' === $recaptcha_type ) {
				$site_key   = get_option( 'user_registration_captcha_setting_recaptcha_site_key_v3' );
				$secret_key = get_option( 'user_registration_captcha_setting_recaptcha_site_secret_v3' );
			} elseif ( 'hCaptcha' === $recaptcha_type ) {
				$recaptcha_value = isset( $post['h-captcha-response'] ) ? ur_clean( wp_unslash( $post['h-captcha-response'] ) ) : '';
				$site_key        = get_option( 'user_registration_captcha_setting_recaptcha_site_key_hcaptcha' );
				$secret_key      = get_option( 'user_registration_captcha_setting_recaptcha_site_secret_hcaptcha' );
			} elseif ( 'cloudflare' === $recaptcha_type ) {
				$recaptcha_value = isset( $post['cf-turnstile-response'] ) ? ur_clean( wp_unslash( $post['cf-turnstile-response'] ) ) : '';
				$site_key        = get_option( 'user_registration_captcha_setting_recaptcha_site_key_cloudflare' );
				$secret_key      = get_option( 'user_registration_captcha_setting_recaptcha_site_secret_cloudflare' );
			}

			if ( ur_is_ajax_login_enabled() ) {
				$recaptcha_value = $captcha_response;
			}

			if ( $recaptcha_enabled && ! empty( $site_key ) && ! empty( $secret_key ) ) {
				if ( ! empty( $recaptcha_value ) ) {
					if ( 'hCaptcha' === $recaptcha_type ) {
						$data = wp_remote_get( 'https://hcaptcha.com/siteverify?secret=' . $secret_key . '&response=' . $recaptcha_value );
						$data = json_decode( wp_remote_retrieve_body( $data ) );

						if ( empty( $data->success ) || ( isset( $data->score ) && $data->score < apply_filters( 'user_registration_hcaptcha_threshold', 0.5 ) ) ) {
							throw new Exception( '<strong>' . esc_html__( 'ERROR:', 'user-registration' ) . '</strong>' . esc_html__( 'Error on hCaptcha. Contact your site administrator.', 'user-registration' ) );
						}
					} elseif ( 'cloudflare' === $recaptcha_type ) {
						$url    = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
						$params = array(
							'method' => 'POST',
							'body'   => array(
								'secret'   => $secret_key,
								'response' => $recaptcha_value,
							),
						);
						$data   = wp_safe_remote_post( $url, $params );
						$data   = json_decode( wp_remote_retrieve_body( $data ) );

						if ( empty( $data->success ) ) {
							throw new Exception( '<strong>' . esc_html__( 'ERROR:', 'user-registration' ) . '</strong>' . esc_html__( 'Error on Cloudflare. Contact your site administrator.', 'user-registration' ) );
						}
					} else {
						$data = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $recaptcha_value );
						$data = json_decode( wp_remote_retrieve_body( $data ) );
						if ( empty( $data->success ) || ( isset( $data->score ) && $data->score <= get_option( 'user_registration_captcha_setting_recaptcha_threshold_score_v3', apply_filters( 'user_registration_recaptcha_v3_threshold', 0.5 ) ) ) ) {
							throw new Exception( '<strong>' . esc_html__( 'ERROR:', 'user-registration' ) . '</strong>' . esc_html__( 'Error on google reCaptcha. Contact your site administrator.', 'user-registration' ) );
						}
					}
				} else {
					throw new Exception( '<strong>' . esc_html__( 'ERROR:', 'user-registration' ) . '</strong>' . get_option( 'user_registration_form_submission_error_message_recaptcha', esc_html__( 'Captcha code error, please try again.', 'user-registration' ) ) );
				}
			}

			// Handles the role based redirection.
			if ( ur_string_to_bool( get_option( 'user_registration_pro_role_based_redirection', false ) ) ) {
				$registration_redirect = get_option( 'ur_pro_settings_redirection_after_login', array() );

				foreach ( $registration_redirect as $role => $page_id ) {

					$roles = isset( $user_details->roles ) ? (array) $user_details->roles : array();

					if ( 0 !== $page_id && in_array( $role, $roles ) ) {
						$redirect_url = get_permalink( $page_id );
						$post['redirect'] = $redirect_url;
					}
				}
			}

			/**
			 * Executes an action before validating the username during the login process.
			 *
			 * The 'user_registration_login_process_before_username_validation' action allows developers to perform
			 * actions before validating the username during the login process.
			 *
			 * @param WP_Post $post The WordPress post object.
			 * @param string $username The entered username.
			 * @param string $nonce_value The nonce value for security validation.
			 * @param array $messages Array of messages for communication.
			 */
			do_action( 'user_registration_login_process_before_username_validation', $post, $username, $nonce_value, $messages );

			$validation_error = new WP_Error();
			/**
			 * Filters the login errors during the user registration process.
			 *
			 * The 'user_registration_process_login_errors' filter allows developers to modify
			 * the validation error messages related to the login credentials during the user
			 * registration process. It provides an opportunity to customize the error messages
			 * based on the original validation error, username, and password submitted.
			 *
			 * @param WP_Error $validation_error The original validation error object.
			 * @param string   $username         The sanitized username submitted during registration.
			 * @param string   $password         The sanitized password submitted during registration.
			 */
			$validation_error = apply_filters( 'user_registration_process_login_errors', $validation_error, sanitize_user( wp_unslash( $post['username'] ) ), sanitize_user( $post['password'] ) );



			if ( $validation_error->get_error_code() ) {
				throw new Exception( '<strong>' . esc_html__( 'ERROR:', 'user-registration' ) . '</strong>' . $validation_error->get_error_message() );
			}

			if ( empty( $username ) ) {
				throw new Exception( '<strong>' . esc_html__( 'ERROR:', 'user-registration' ) . '</strong>' . $messages['empty_username'] );
			}

			if ( is_email( $username ) && apply_filters( 'user_registration_get_username_from_email', true ) ) {
				$user = get_user_by( 'email', $username );

				if ( isset( $user->user_login ) ) {
					$login_data['user_login'] = $user->user_login;
				} else {
					$user = get_user_by( 'login', $username );

					if ( isset( $user->user_login ) ) {
						$login_data['user_login'] = $user->user_login;
					} elseif ( empty( $messages['unknown_email'] ) ) {
						$messages['unknown_email'] = esc_html__( 'A user could not be found with this email address.', 'user-registration' );
						throw new Exception( '<strong>' . esc_html__( 'ERROR: ', 'user-registration' ) . '</strong>' . $messages['unknown_email'] );
					}
				}
			} else {
				$login_data['user_login'] = $username;
			}

			// On multisite, ensure user exists on current site, if not add them before allowing login.
			if ( is_multisite() ) {
				$user_data = get_user_by( 'login', $username );

				if ( $user_data && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
					add_user_to_blog( get_current_blog_id(), $user_data->ID, 'customer' );
				}
			}

			// To check the specific login.
			if ( 'email' === get_option( 'user_registration_general_setting_login_options_with', array() ) ) {
				$user_data                = get_user_by( 'email', $username );
				$login_data['user_login'] = isset( $user_data->user_email ) ? $user_data->user_email : '1#45$$&*@ur.com'; //provided invalid email to show invalid email error instead of empty username which will show empty_username error regardless of the login option
			} elseif ( 'username' === get_option( 'user_registration_general_setting_login_options_with', array() ) ) {
				$user_data                = get_user_by( 'login', $username );
				$login_data['user_login'] = isset( $user_data->user_login ) ? $user_data->user_login : ! is_email( $username );
			} else {
				$login_data['user_login'] = $username;
			}

			// Perform the login.
			$user = wp_signon( apply_filters( 'user_registration_login_credentials', $login_data ), is_ssl() );

			if ( is_wp_error( $user ) ) {
				// Set custom error messages.
				if ( ! empty( $user->errors['empty_username'] ) && ! empty( $messages['empty_username'] ) ) {
					$user->errors['empty_username'][0] = sprintf( '<strong>%s:</strong> %s', __( 'ERROR', 'user-registration' ), $messages['empty_username'] );
				}
				if ( ! empty( $user->errors['empty_password'] ) && ! empty( $messages['empty_password'] ) ) {
					$user->errors['empty_password'][0] = sprintf( '<strong>%s:</strong> %s', __( 'ERROR', 'user-registration' ), $messages['empty_password'] );
				}
				if ( ! empty( $user->errors['invalid_username'] ) && ! empty( $messages['invalid_username'] ) ) {
					$user->errors['invalid_username'][0] = sprintf( '<strong>%s:</strong> %s', __( 'ERROR', 'user-registration' ), $messages['invalid_username']);
				}
				if ( ! empty( $user->errors['invalid_email'] ) && ! empty( $messages['unknown_email'] ) ) {
					$user->errors['invalid_email'][0] = sprintf( '<strong>%s:</strong> %s', __( 'ERROR', 'user-registration' ), $messages['unknown_email'] );
				}
				if ( ! empty( $user->errors['pending_approval'] ) && ! empty( $messages['pending_approval'] ) ) {
					$user->errors['pending_approval'][0] = sprintf( '<strong>%s:</strong> %s', __( 'ERROR', 'user-registration' ), $messages['pending_approval'] );
				}
				if ( ! empty( $user->errors['denied_access'] ) && ! empty( $messages['denied_access'] ) ) {
					$user->errors['denied_access'][0] = sprintf( '<strong>%s:</strong> %s', __( 'ERROR', 'user-registration' ), $messages['denied_access'] );
				}

				$message = $user->get_error_message();
				$message = str_replace( '<strong>' . esc_html( $login_data['user_login'] ) . '</strong>', '<strong>' . esc_html( $username ) . '</strong>', $message );
				throw new Exception( $message );
			} elseif ( isset( $user->ID ) && $is_disabled = get_user_meta( $user->ID, 'ur_disable_users', true ) ) {
					wp_logout();
					throw new Exception( '<strong>' . esc_html__( 'ERROR: ', 'user-registration' ) . '</strong>' . $messages['user_disabled'] );

			} else {

				if ( in_array( 'administrator', $user->roles, true ) && ur_option_checked( 'user_registration_login_options_prevent_core_login', true ) ) {
					$redirect = admin_url();
				} elseif ( ! empty( $post['redirect'] ) ) {
					$redirect = esc_url_raw( wp_unslash( $post['redirect'] ) );
				} elseif ( wp_get_raw_referer() ) {
					if( get_permalink( get_option( 'user_registration_login_page_id' ) ) === wp_get_raw_referer() || '/login/' === wp_get_raw_referer() ) {
						$redirect = ur_get_my_account_url();
					} else {
						$redirect = wp_get_raw_referer();
					}
				} else {
					$redirect = get_home_url();
				}

				/**
				 * Filters the login redirection.
				 *
				 * @param string   $redirect The original redirect URL after successful login.
				 * @param WP_User  $user     The user object representing the newly registered user.
				 */
				$redirect = apply_filters( 'user_registration_login_redirect', $redirect, $user );

				if ( ur_is_ajax_login_enabled() ) {
					wp_send_json_success( array( 'message' => $redirect ) );
					wp_send_json( $user );
				} else {
					wp_redirect( wp_validate_redirect( $redirect, $redirect ) );
					exit;
				}

				if ( ur_is_ajax_login_enabled() ) {
					wp_send_json( $user );
				}
			}
		} catch ( Exception $e ) {
			$status_code = $e->getCode();
			$message     = $e->getMessage();

			if ( $status_code >= 200 && $status_code < 300 ) {
				if ( ur_is_ajax_login_enabled() ) {
					wp_send_json_success(
						array(
							'message' => $message,
							'status'  => true,
						)
					);
				}

				/**
				 * Filters the error messages displayed on the login screen.
				 *
				 * @param string $message The original error message displayed on the login screen.
				 */
				add_filter( "user_registration_passwordless_login_notice", function( $err_msg ) use ($message) {
					return $message;
				}, 10, 1 );
			} else {

				if ( ur_is_ajax_login_enabled() ) {
					wp_send_json_error(
						array(
							'message' => apply_filters( 'login_errors', $message ),
						)
					);
				}
				/**
				 * Filters the error messages displayed on the login screen.
				 *
				 * @param string $message The original error message displayed on the login screen.
				 */
				add_filter( "user_registration_post_login_errors", function( $err_msg ) use ($message) {
					return apply_filters( 'login_errors', $message );
				}, 10, 1 );
				/**
				 * Triggered when a user fails to log in during the user registration process.
				 */
				do_action( 'user_registration_login_failed' );
			}
		}
	}
}

if ( ! function_exists( 'paypal_generate_redirect_url' ) ) {
	/**
	 * Regenerate PayPal redirect URL to pay after
	 *
	 * @param  int $user_id User Id.
	 * @return string redirect url
	 */
	function paypal_generate_redirect_url( $user_id ) {

		// Check an user was created and passed.
		if ( empty( $user_id ) ) {
			return '#';
		}

		// Filter redirect url for other payment add-ons.
		if ( 'paypal_standard' !== get_user_meta( $user_id, 'ur_payment_method', true ) ) {
			return apply_filters( 'user_registration_payment_generate_redirect_url', '#', $user_id );
		}

		$form_id   = get_user_meta( $user_id, 'ur_form_id', true );
		$user_data = get_user_by( 'id', $user_id );

		// Get data from saved user and for.
		$currency       = get_user_meta( $user_id, 'ur_payment_currency', true );
		$payment_mode   = get_user_meta( $user_id, 'ur_payment_mode', true );
		$payment_type   = get_user_meta( $user_id, 'ur_payment_type', true );
		$receiver_email = get_user_meta( $user_id, 'ur_payment_recipient', true );
		$total_amount   = get_user_meta( $user_id, 'ur_payment_total_amount', true );
		$ur_cart_items  = json_decode( get_user_meta( $user_id, 'ur_cart_items', true ) );

		$cancel_url = ur_get_single_post_meta( $form_id, 'user_registration_paypal_cancel_url', home_url() );
		$return_url = ur_get_single_post_meta( $form_id, 'user_registration_paypal_return_url', wp_login_url() );

		if ( empty( $form_id ) || empty( $currency ) || empty( $payment_mode ) || empty( $payment_type ) || empty( $receiver_email ) || empty( $total_amount ) ) {
			return '#';
		}

		// Build the return URL with hash.
		$query_args = 'form_id=' . absint( $form_id ) . '&user_id=' . absint( $user_id ) . '&hash=' . wp_hash( $form_id . ',' . $user_id );

		$return_url = esc_url_raw(
			add_query_arg(
				array(
					'user_registration_return' => base64_encode( $query_args ),
				),
				$return_url
			)
		);

		$redirect   = ( 'production' === $payment_mode ) ? 'https://www.paypal.com/cgi-bin/webscr/?' : 'https://www.sandbox.paypal.com/cgi-bin/webscr/?';
		$cancel_url = ! empty( $cancel_url ) ? esc_url_raw( $cancel_url ) : home_url();

		// Subscription.
		$paypal_recurring_enabled = ur_string_to_bool( ur_get_single_post_meta( $form_id, 'user_registration_enable_paypal_standard_subscription', false ) );
		if ( $paypal_recurring_enabled ) {
			$transaction = '_xclick-subscriptions';
		} elseif ( 'donation' === $payment_type ) {
			$transaction = '_donations';
		} else {
			$transaction = '_cart';
		}
		// Setup PayPal arguments.
		$paypal_args = array(
			'bn'            => 'UserRegistration_SP',
			'business'      => sanitize_email( $receiver_email ),
			'cancel_return' => $cancel_url,
			'cbt'           => get_bloginfo( 'name' ),
			'charset'       => get_bloginfo( 'charset' ),
			'cmd'           => $transaction,
			'currency_code' => strtoupper( $currency ),
			'custom'        => absint( $form_id ),
			'invoice'       => absint( $user_id ),
			'notify_url'    => add_query_arg( 'user-registration-listener', 'IPN', home_url( 'index.php' ) ),
			'return'        => $return_url,
			'rm'            => '2',
			'tax'           => 0,
			'upload'        => '1',
			'amount'        => $total_amount,
			'sra'           => '1',
			'src'           => '1',
			'no_note'       => '1',
			'no_shipping'   => '1',
			'shipping'      => '0',
		);

		// Add cart items.
		if ( '_cart' === $transaction ) {

			// Product/service.
			$i = 1;

			if ( ! empty( $ur_cart_items ) ) {
				$i = 1;
				foreach ( $ur_cart_items as $key => $payment_items ) {
					$quantity = isset( $payment_items->quantity ) ? $payment_items->quantity : '';
					$amount   = isset( $payment_items->amount ) ? $payment_items->amount : '';

					if ( is_object( $payment_items->value ) ) {

						foreach ( $payment_items->value as $label => $value ) {

							if ( ! empty( $quantity ) ) {
								$paypal_args[ 'item_name_' . $i ] = $label;
								$paypal_args[ 'amount_' . $i ]    = $quantity * $value;

							} else {
								$paypal_args[ 'item_name_' . $i ] = $label;
								$paypal_args[ 'amount_' . $i ]    = $value;
							}
							++$i;
						}
					} elseif ( ! empty( $quantity ) ) {
						$paypal_args[ 'item_name_' . $i ] = $payment_items->extra_params->label;
						$paypal_args[ 'amount_' . $i ]    = $amount;

					} else {

						$paypal_args[ 'item_name_' . $i ] = $payment_items->extra_params->label;
						$paypal_args[ 'amount_' . $i ]    = $payment_items->value;
					}
					++$i;
				}
			}
		} elseif ( '_donations' === $transaction ) {

			// Combine a donation name from all payment fields names.
			$item_names = array();

			if ( ! empty( $ur_cart_items ) ) {
				foreach ( $ur_cart_items as $key => $payment_items ) {
					if ( is_object( $payment_items->value ) ) {
						foreach ( $payment_items->value as $label => $value ) {
							$item_names[] = $label;
						}
					} else {
						$item_names[] = $payment_items->extra_params->label;
					}
				}
			}

			$paypal_args['item_name'] = implode( '; ', $item_names );
			$paypal_args['amount']    = $total_amount;
		} else {
			$customer_email          = isset( $user_data->user_email ) ? $user_data->email : '';
			$post_data               = ur_get_post_content( $form_id );
			$subscription_plan_field = ur_get_form_data_by_key( $post_data, 'subscription_plan' );

			if ( ! empty( $subscription_plan_field ) ) {
				if ( isset( $subscription_plan_field['subscription_plan']->general_setting->options ) ) {
					$plan_lists = $subscription_plan_field['subscription_plan']->general_setting->options;
					if ( ! empty( $plan_lists ) ) {

						foreach ( $plan_lists as $plan ) {
							$interval_count   = $plan->interval_count;
							$plan_name        = $plan->label;
							$recurring_period = $plan->recurring_period;
						}
					}
				}
			} else {
				$plan_name        = ur_get_single_post_meta( $form_id, 'user_registration_paypal_plan_name', '' );
				$recurring_period = ur_get_single_post_meta( $form_id, 'user_registration_paypal_recurring_period' );
				$interval_count   = ur_get_single_post_meta( $form_id, 'user_registration_paypal_interval_count', '1' );
			}
			$paypal_args['email']     = $customer_email;
			$paypal_args['a3']        = $total_amount;
			$paypal_args['item_name'] = ! empty( $plan_name ) ? $plan_name : '';
			$paypal_args['t3']        = ! empty( $recurring_period ) ? strtoupper( substr( $recurring_period, 0, 1 ) ) : '';
			$paypal_args['p3']        = ! empty( $interval_count ) ? $interval_count : 1;
		}

		// Build query.
		$redirect .= http_build_query( $paypal_args );
		$redirect  = str_replace( '&amp;', '&', $redirect );

		return $redirect;
	}
}

if ( ! function_exists( 'ur_generate_onetime_token' ) ) {
	/**
	 * Generate a one-time token for the given user ID and action.
	 *
	 * @param int    $user_id The ID of the user for whom to generate the token.
	 * @param string $action The action for which to generate the token.
	 * @param int    $key_length The length of the random key to be generated. Defaults to 32.
	 * @param int    $expiration_time The duration of the token's validity in minutes. Defaults to 60.
	 * @return string The generated one-time token.
	 */
	function ur_generate_onetime_token( $user_id = 0, $action = '', $key_length = 32, $expiration_time = 60 ) {
		$time = time();
		$key  = wp_generate_password( $key_length, false );

		// Concatenate the key, action, and current time to form the token string.
		$string = $key . $action . $time;

		// Generate the token hash.
		$token = wp_hash( $string );

		// Set the token expiration time in seconds.
		$expiration = apply_filters( $action . '_onetime_token_expiration', $expiration_time * 60 );

		// Set the user meta values for the token and expiration time.
		update_user_meta( $user_id, $action . '_token' . $user_id, $token );
		update_user_meta( $user_id, $action . '_token_expiration' . $user_id, $time + $expiration );

		return $token;
	}
}

if ( ! function_exists( 'ur_get_current_page_url' ) ) {
	/**
	 * Get the current page URL.
	 *
	 * @return string The URL of the current page.
	 */
	function ur_get_current_page_url() {
		$page_url = '';

		if ( isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ) {
			$page_url .= 'https://';
		} else {
			$page_url .= 'http://';
		}

		if ( isset( $_SERVER['HTTP_HOST'] ) ) {
			$page_url .= sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) );
		}

		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$page_url .= sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		}

		return $page_url;
	}
}

if ( ! function_exists( 'ur_is_passwordless_login_enabled' ) ) {
	/**
	 * Check whether the passwordless login is enabled or not.
	 *
	 * @return bool
	 */
	function ur_is_passwordless_login_enabled() {
		return ur_option_checked( 'user_registration_pro_passwordless_login', false );
	}
}

if ( ! function_exists( 'ur_is_user_registration_pro_passwordless_login_default_login_area_enabled' ) ) {
	/**
	 * Check whether the passwordless login as default login is enabled or not.
	 *
	 * @return bool
	 *
	 * @since 4.0
	 */
	function ur_is_user_registration_pro_passwordless_login_default_login_area_enabled() {
		return ur_option_checked( 'user_registration_pro_passwordless_login_default_login_area', false );
	}
}

if ( ! function_exists( 'ur_get_ip_address' ) ) {

	/**
	 * Get current user IP Address.
	 *
	 * @return string
	 */
	function ur_get_ip_address() {
		if ( isset( $_SERVER['HTTP_X_REAL_IP'] ) ) { // WPCS: input var ok, CSRF ok.
			return sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_REAL_IP'] ) );  // WPCS: input var ok, CSRF ok.
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) { // WPCS: input var ok, CSRF ok.
			// Proxy servers can send through this header like this: X-Forwarded-For: client1, proxy1, proxy2
			// Make sure we always only send through the first IP in the list which should always be the client IP.
			return (string) rest_is_ip_address( trim( current( preg_split( '/[,:]/', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) ) ) ) ); // WPCS: input var ok, CSRF ok.
		} elseif (isset($_SERVER['REMOTE_ADDR'])) { // @codingStandardsIgnoreLine
			return sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])); // @codingStandardsIgnoreLine
		}
		return '';
	}
}

if ( ! function_exists( 'ur_get_all_pages' ) ) {
	/**
	 * Returns map of published pages as id->title format.
	 *
	 * @return array
	 */
	function ur_get_all_pages() {
		$pages = get_pages();

		$pages_array = array();

		foreach ( $pages as $page ) {
			$pages_array[ $page->ID ] = $page->post_title;
		}

		return $pages_array;
	}
}


if ( ! function_exists( 'user_registration_process_email_content' ) ) {
	/**
	 * Returns email content wrapped in email template.
	 *
	 * @param string $email_content Email Content.
	 * @param string $template Email Template id.
	 */
	function user_registration_process_email_content( $email_content, $template = '' ) {
		// Check if email template is selected.
		if ( '' !== $template && 'none' !== $template ) {
			/**
			 * Filters the email template message.
			 *
			 * The 'user_registration_email_template_message' filter allows developers to modify
			 * the content of the email template used during the user registration process. It provides
			 * an opportunity to customize the email content based on the original content and the template.
			 *
			 * @param string $email_content The original content of the email template.
			 * @param string $template      The template being used for the email.
			 */
			$email_content = apply_filters( 'user_registration_email_template_message', $email_content, $template );
		} else {
			$default_width = '50%';

			/**
			 * Filters to change the email body width.
			 *
			 * The 'user_registration_email_body_width' filter allows developers to modify
			 * the width of the email body used during the user registration process. It provides
			 * an opportunity to customize the width of the email body based as per user requirements.
			 *
			 * @param string $default_width The default width.
			 */
			$email_body_width = apply_filters( 'user_registration_email_body_width', $default_width );
			ob_start();
			?>
<div class="user-registration-email-body" style="padding: 100px 0; background-color: #ebebeb;">
	<table class="user-registration-email" border="0" cellpadding="0" cellspacing="0"
		style="width: <?php echo esc_attr( $email_body_width ); ?>; margin: 0 auto; background: #ffffff; padding: 30px 30px 26px; border: 0.4px solid #d3d3d3; border-radius: 11px; font-family: 'Segoe UI', sans-serif; ">
		<tbody>
			<tr>
				<td colspan="2" style="text-align: left;">
					<?php echo wp_kses_post( $email_content ); ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
			<?php
			$email_content = wp_kses_post( ob_get_clean() );
		}

		return $email_content;
	}
}

if ( ! function_exists( 'ur_email_preview_link' ) ) {

	/**
	 * Get link for preview email button used on email settings.
	 *
	 * @param  string $label Label.
	 * @param  string $email_id Email id.
	 */
	function ur_email_preview_link( $label, $email_id ) {
		$url = add_query_arg(
			array(
				'ur_email_preview' => $email_id,
			),
			home_url()
		);

		return '<a href="' . esc_url( $url ) . '" aria-label="' . esc_attr( $label ) . '" class="button user-registration-email-preview " style="min-width:70px;">' . esc_html( $label ) . '</a>';
	}
}

add_action( 'user_registration_after_user_meta_update', 'ur_parse_and_update_hidden_field', 10, 3 );

if ( ! function_exists( 'ur_parse_and_update_hidden_field' ) ) {
	/**
	 * Parse the hidden field value and update.
	 *
	 * @param array $form_data form data.
	 * @param int   $form_id form id.
	 * @param int   $user_id user id.
	 */
	function ur_parse_and_update_hidden_field( $form_data, $form_id, $user_id ) {
		$values = array(
			'form_id'      => $form_id,
			'process_type' => 'ur_parse_after_meta_update',
		);

		foreach ( $form_data as $key => $value ) {
			if ( 'user_email' === $value->field_name ) {
				$values['email'] = ur_format_field_values( $value->field_name, $value->value );
			}

			$values[ $value->field_name ] = ur_format_field_values( $value->field_name, $value->value );
		}

		foreach ( $form_data as $key => $value ) {
			if ( isset( $value->extra_params['field_key'] ) && 'hidden' === $value->extra_params['field_key'] ) {
				$content    = $value->value;
				$field_name = 'user_registration_' . $value->field_name;
				if ( '' !== $content ) {
					/**
					 * Filters the processed content of smart tags.
					 *
					 * The 'user_registration_process_smart_tags' filter allows developers to modify
					 * the content of smart tags processed during the user registration process. It provides
					 * an opportunity to customize the content based on the original content and the values
					 * of the smart tags.
					 *
					 * @param string $content The original content containing smart tags.
					 * @param array  $values  The values of smart tags processed during registration.
					 */
					$content = apply_filters( 'user_registration_process_smart_tags', $content, $values );
					update_user_meta( $user_id, $field_name, $content );
				}
			}
		}
	}
}

if ( ! function_exists( 'ur_maybe_unserialize' ) ) {
	/**
	 * UR Unserialize data.
	 *
	 * @param string $data Data that might be unserialized.
	 * @param array  $options Options.
	 *
	 * @return mixed Unserialized data can be any type.
	 *
	 * @since 3.0.2
	 */
	function ur_maybe_unserialize( $data, $options = array() ) {

		if ( is_serialized( $data ) ) {
			if ( version_compare( PHP_VERSION, '7.1.0', '>=' ) ) {
				$options = wp_parse_args( $options, array( 'allowed_classes' => false ) );
				return @unserialize( trim( $data ), $options ); //phpcs:ignore.
			}
			return @unserialize( trim( $data ) ); //phpcs:ignore.
		}

		return $data;
	}
}

if ( ! function_exists( 'user_registration_conditional_user_meta_filter' ) ) {
	/**
	 * Filter user meta field when conditinal logic applied.
	 *
	 * @param array $valid_form_data Form Data.
	 * @param int   $user_id User Id.
	 * @param int   $form_id Form Id.
	 * @return array array of form data.
	 *
	 * @since 3.0.4
	 */
	function user_registration_conditional_user_meta_filter( $valid_form_data, $user_id, $form_id ) {
		if ( $user_id <= 0 ) {
			return $valid_form_data;
		}

		$field_name   = '';
		$hidden_field = isset( $_POST['urcl_hide_fields'] ) ? ur_clean( $_POST['urcl_hide_fields'] ) : array(); //phpcs:ignore.

		if ( empty( $hidden_field ) ) {
			return $valid_form_data;
		}

		$hidden_array_field = json_decode( stripslashes( $hidden_field ) );

		if ( isset( $_POST['action'] ) && 'user_registration_user_form_submit' ===  $_POST['action'] ) { //phpcs:ignore.
			foreach ( $hidden_array_field as $field ) {
				$field_name = $field;
				if ( in_array( $field_name, array_keys( $valid_form_data ) ) ) {
					unset( $valid_form_data[ $field_name ] );
				}
			}
		} else {
			foreach ( $hidden_array_field as $field ) {
				$field_name = 'user_registration_' . $field;
				if ( in_array( $field_name, array_keys( $valid_form_data ) ) ) {
					unset( $valid_form_data[ $field_name ] );
				}
			}
		}

		return $valid_form_data;
	}
}

add_filter( 'user_registration_before_user_meta_update', 'user_registration_conditional_user_meta_filter', 10, 3 );
add_filter( 'user_registration_before_save_profile_details', 'user_registration_conditional_user_meta_filter', 10, 3 );

if ( ! function_exists( 'ur_get_ip_address' ) ) {
	/**
	 * Get current user IP Address.
	 *
	 * @return string
	 */
	function ur_get_ip_address() {
		if ( isset( $_SERVER['HTTP_X_REAL_IP'] ) ) { // WPCS: input var ok, CSRF ok.
			return sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_REAL_IP'] ) );  // WPCS: input var ok, CSRF ok.
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) { // WPCS: input var ok, CSRF ok.
			// Proxy servers can send through this header like this: X-Forwarded-For: client1, proxy1, proxy2.
			// Make sure we always only send through the first IP in the list which should always be the client IP.
			return (string) rest_is_ip_address( trim( current( preg_split( '/[,:]/', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) ) ) ) ); // WPCS: input var ok, CSRF ok.
		} elseif (isset($_SERVER['REMOTE_ADDR'])) { // @codingStandardsIgnoreLine
			return sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])); // @codingStandardsIgnoreLine
		}
		return '';
	}
}

if ( ! function_exists( 'ur_get_all_page_slugs' ) ) {
	/**
	 * Get all the page slugs.
	 */
	function ur_get_all_page_slugs() {
		$args = array(
			'post_type'      => 'page',
			'posts_per_page' => -1,
		);

		$pages = get_pages( $args );

		$slugs = array();

		foreach ( $pages as $page ) {
			$slugs[] = $page->post_name;
		}

		return $slugs;
	}
}

if ( ! function_exists( 'ur_add_links_to_top_nav' ) ) {
	/**
	 * Add plugin specific links to the admin bar menu.
	 *
	 * @param [WP_Admin_Bar] $wp_admin_bar Admin Bar.
	 * @return void
	 */
	function ur_add_links_to_top_nav( $wp_admin_bar ) {
		if ( ! is_admin_bar_showing() || ! current_user_can( 'manage_user_registration' ) ) {
			return;
		}

		if ( apply_filters( 'user_registration_show_link_to_admin_top_nav', false ) ) {
			return;
		}
		/**
		 * Add User Registration links in the admin top nav bar.
		 */

		$wp_admin_bar->add_menu(
			array(
				'id'     => 'user-registration-menu',
				'parent' => null,
				'group'  => null,
				'title'  => __( 'User Registration', 'user-registration' ), // you can use img tag with image link. it will show the image icon Instead of the title.
				'href'   => admin_url( 'admin.php?page=user-registration' ),
			)
		);

		/**
		 * Add Edit Form link in Form Preview Page.
		 */

		$form_id = 0;

		if ( isset( $_GET['ur_preview'] ) && isset( $_GET['form_id'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$form_id = sanitize_text_field( wp_unslash( $_GET['form_id'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		} elseif ( is_page() || is_single() ) {

			if ( isset( $_GET['vc_editable'] ) ) {
				return;
			}
			$post_content = get_the_content();

			if ( has_shortcode( $post_content, 'user_registration_form' ) ) {
				if ( preg_match( '/\[user_registration_form id="(\d+)"\]/', $post_content, $matches ) ) {
					$form_id = $matches[1];
				}
			}
		}

		if ( ! empty( $form_id ) ) {
			$wp_admin_bar->add_menu(
				array(
					'parent' => 'user-registration-menu',
					'id'     => 'ur-edit-form',
					'title'  => __( 'Edit Form', 'user-registration' ),
					'href'   => add_query_arg(
						'edit-registration',
						$form_id,
						admin_url( 'admin.php?page=add-new-registration' )
					),
					'meta'   => array(
						'target' => '_blank',
						'rel'    => 'noopener noreferrer',
					),
				)
			);
		}

		$wp_admin_bar->add_menu(
			array(
				'parent' => 'user-registration-menu',
				'id'     => 'user-registration-all-forms',
				'title'  => __( 'All Forms', 'user-registration' ),
				'href'   => admin_url( 'admin.php?page=user-registration' ),
			)
		);

		$wp_admin_bar->add_menu(
			array(
				'parent' => 'user-registration-menu',
				'id'     => 'user-registration-add-new',
				'title'  => __( 'Add New', 'user-registration' ),
				'href'   => admin_url( 'admin.php?page=add-new-registration' ),
			)
		);

		$wp_admin_bar->add_menu(
			array(
				'parent' => 'user-registration-menu',
				'id'     => 'user-registration-settings',
				'title'  => __( 'Settings', 'user-registration' ),
				'href'   => admin_url( 'admin.php?page=user-registration-settings' ),
			)
		);

		$href = add_query_arg(
			array(
				'utm_medium'  => 'admin-bar',
				'utm_source'  => 'WordPress',
				'utm_content' => 'Documentation',
			),
			esc_url_raw( 'https://docs.wpuserregistration.com/' )
		);

		$wp_admin_bar->add_menu(
			array(
				'parent' => 'user-registration-menu',
				'id'     => 'user-registration-docs',
				'title'  => __( 'Documentation', 'user-registration' ),
				'href'   => $href,
				'meta'   => array(
					'target' => '_blank',
					'rel'    => 'noopener noreferrer',
				),
			)
		);
		/**
		 * Triggered to customize the top admin bar menu.
		 *
		 * @param WP_Admin_Bar $wp_admin_bar The WordPress admin bar object.
		 */
		do_action( 'user_registration_top_admin_bar_menu', $wp_admin_bar );
	}

	add_action( 'admin_bar_menu', 'ur_add_links_to_top_nav', 999, 1 );
}

if ( ! function_exists( 'ur_array_clone' ) ) {
	/**
	 * Clone Array or Object
	 *
	 * @since 3.0.5
	 *
	 * @param  [mixed] $array Array to clone.
	 */
	function ur_array_clone( $array ) {
		if ( is_object( $array ) ) {
			return clone $array;
		}
		if ( ! is_array( $array ) ) {
			return $array;
		}
		return array_map(
			function ( $element ) {
				return ( ( is_array( $element ) )
					? array_clone( $element )
					: ( ( is_object( $element ) )
						? clone $element
						: $element
					)
				);
			},
			$array
		);
	}
	if ( ! function_exists( 'ur_unlink_user_profile_pictures' ) ) {
		/**
		 * Remove user uploaded profile pictures and related thumbnail.
		 *
		 * @param int $id User ID.
		 */
		function ur_unlink_user_profile_pictures( $id ) {
			$profile_pic_url = get_user_meta( $id, 'user_registration_profile_pic_url', true );
			if ( ! empty( $profile_pic_url ) ) {

				$profile_id = get_post_meta( $profile_pic_url, '_wp_attachment_metadata' );

				// Unlink profile picture before removing users.
				if ( is_array( $profile_id ) && ! empty( $profile_id ) ) {
					foreach ( $profile_id as $profile ) {
						if ( is_array( $profile ) && isset( $profile['file'] ) ) {
							$base_dir  = wp_upload_dir()['basedir'];
							$file      = $profile['file'];
							$full_path = trailingslashit( $base_dir ) . $file;

							if ( file_exists( $full_path ) ) {
								unlink( $full_path );
							}

							// unlink different size thumbnails of profile picture.
							if ( isset( $profile['sizes'] ) && is_array( $profile['sizes'] ) ) {
								foreach ( $profile['sizes'] as $size ) {
									if ( is_array( $size ) && isset( $size['file'] ) ) {
										$size_file      = $size['file'];
										$full_size_path = UR_UPLOAD_PATH . 'profile-pictures/' . $size_file;

										if ( file_exists( $full_size_path ) ) {
											unlink( $full_size_path );
										}
									}
								}
							}

							// Unlink original uploaded image.
							if ( isset( $profile['original_image'] ) ) {
								$original_file = UR_UPLOAD_PATH . 'profile-pictures/' . $profile['original_image'];
								if ( file_exists( $original_file ) ) {
									unlink( $original_file );
								}
							}
						}
					}
				}
				// Remove profile pictures related metadata from DB.
				delete_post_meta( $profile_pic_url, '_wp_attachment_metadata' );
				delete_post_meta( $profile_pic_url, '_wp_attached_file' );

				// Remove attachments form media library.
				wp_delete_attachment( $profile_pic_url, true );
			}
		}
	}
	add_action( 'ur_remove_profile_pictures_and_metadata', 'ur_unlink_user_profile_pictures' );
}

if ( ! function_exists( 'ur_automatic_user_login' ) ) {
	/**
	 * Automatically login users.
	 *
	 * @since 3.1.5
	 *
	 * @param object $user The user.
	 */
	function ur_automatic_user_login( $user ) {
		wp_clear_auth_cookie();
		$remember = apply_filters( 'user_registration_autologin_remember_user', false );
		wp_set_auth_cookie( $user->ID, $remember );

		/**
		 * Filters the login redirection.
		 *
		 * @param string   $redirect The original redirect URL after successful login.
		 * @param WP_User  $user     The user object representing the newly registered user.
		 */
		$redirect = apply_filters( 'user_registration_login_redirect', ur_get_my_account_url(), $user );
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			wp_redirect( esc_url_raw( $redirect ) );
			exit();
		} else {
			wp_send_json_success(
				array(
					'redirect' => esc_url_raw( $redirect ),
				)
			);
		}
	}
}

if ( ! function_exists( 'ur_resend_verification_email' ) ) {
	/**
	 * This function will send email verification email to the user.
	 *
	 * @since 3.1.5
	 *
	 * @param int $user_id User ID.
	 */
	function ur_resend_verification_email( $user_id ) {
		$user    = get_user_by( 'id', $user_id );
		$form_id = ur_get_form_id_by_userid( $user_id );

		$confirm_email = new UR_Email_Confirmation();
		$confirm_email->set_email_status( array(), $form_id, $user_id );
		/**
		 * Filter hook to modify the email attachment resending token.
		 * Default value is empty array.
		 */
		$attachments = apply_filters( 'user_registration_email_attachment_resending_token', array() );
		$name_value  = ur_get_user_extra_fields( $user_id );

		// Get selected email template id for specific form.
		$template_id = ur_get_single_post_meta( $form_id, 'user_registration_select_email_template' );

		UR_Emailer::send_mail_to_user( $user->user_email, $user->user_login, $user_id, '', $name_value, $attachments, $template_id );
	}
}


if ( ! function_exists( 'ur_merge_translations' ) ) {
	/**
	 * Merge Addons Translation in Pro text domain.
	 *
	 * @since 4.1.5
	 *
	 * @param string $source_dir Addon Language Source Directory.
	 * @param string $destination_dir Pro Language Directory.
	 * @param string $file_extension File Extentions.
	 * @param string $text_domain Existing Text Domain/ Addon slug.
	 */
	function ur_merge_translations( $source_dir, $destination_dir, $file_extension, $text_domain ) {
		$source_files = glob( $source_dir . '/*.' . $file_extension );

		foreach ( $source_files as $source_file ) {
			$language_code             = basename( $source_file, '.' . $file_extension );
			$destination_language_code = str_replace( '-' . $text_domain, '', $language_code );

			if ( 'user-registration' === $destination_language_code ) {
				$source_file_path      = $source_dir . '/' . $language_code . '.' . $file_extension; //phpcs:ignore
				$destination_file_path = $destination_dir . '/' . $destination_language_code . '.' . $file_extension; //phpcs:ignore
				if ( ! file_exists( $destination_file_path ) ) {
					touch( $destination_file_path );
				}

				$source_content = file_get_contents( $source_file_path );
				file_put_contents( $destination_file_path, $source_content, FILE_APPEND );
			}
		}
	}
}

if ( ! function_exists( 'user_registration_validate_form_field_data' ) ) {

	/**
	 * Function to validate individual form field data.
	 *
	 * @param object $data Form field data submitted by the user.
	 * @param array  $form_data Form Data.
	 * @param int    $form_id Form id.
	 * @param array  $response_array Response Array.
	 * @param array  $form_field_data Form Field Data..
	 * @param array  $valid_form_data Valid Form Data..
	 */
	function user_registration_validate_form_field_data( $data, $form_data, $form_id, $response_array, $form_field_data, $valid_form_data ) {
		$form_key_list  = wp_list_pluck( wp_list_pluck( $form_field_data, 'general_setting' ), 'field_name' );
		$form_validator = new UR_Form_Validation();

		if ( in_array( $data->field_name, $form_key_list, true ) ) {
			$form_data_index    = array_search( $data->field_name, $form_key_list, true );
			$single_form_field  = $form_field_data[ $form_data_index ];
			$general_setting    = isset( $single_form_field->general_setting ) ? $single_form_field->general_setting : new stdClass();
			$single_field_key   = $single_form_field->field_key;
			$single_field_label = isset( $general_setting->label ) ? ur_string_translation( $form_id, 'user_registration_single_field_label', $general_setting->label) : '';
			$single_field_value = isset( $data->value ) ? $data->value : '';
			$data->extra_params = array(
				'field_key' => $single_field_key,
				'label'     => $single_field_label,
			);

			/**
			 * Validate form fields according to the validations set in $validations array.
			 *
			 * @see this->get_field_validations()
			 */

			$validations = $form_validator->get_field_validations( $single_field_key );

			if ( $form_validator->is_field_required( $single_form_field, $form_data ) ) {
				array_unshift( $validations, 'required' );
			}

			if ( ! empty( $validations ) ) {
				if ( in_array( 'required', $validations, true ) || ! empty( $single_field_value ) ) {
					foreach ( $validations as $validation ) {
						$result = UR_Form_Validation::$validation( $single_field_value );

						if ( is_wp_error( $result ) ) {
							$response_array = $form_validator->add_error( $result, $single_field_label, $response_array );
							break;
						}
					}
				}
			}

			/**
			 * Hook to update form field data.
			 */
			$field_hook_name = 'user_registration_form_field_' . $single_form_field->field_key . '_params';
			/**
			 * Filter the single field params.
			 *
			 * The dynamic portion of the hook name, $field_hook_name.
			 *
			 * @param array $data The form data.
			 * @param array $single_form_field The single form field.
			 */
			$data                                 = apply_filters( $field_hook_name, $data, $single_form_field );
			$valid_form_data[ $data->field_name ] = UR_Form_Validation::get_sanitize_value( $data );

			/**
			 * Hook to custom validate form field.
			 */
			$hook        = "user_registration_validate_{$single_form_field->field_key}";
			$filter_hook = $hook . '_message';

			if ( isset( $data->field_type ) && 'email' === $data->field_type ) {
				/**
				 * Action validate email whitelist.
				 *
				 * @param array $data->value The data value.
				 * @param string $filter_hook The dynamic Filter hook.
				 * @param array $single_form_field The single form field.
				 * @param int $form_id The form ID.
				 */
				do_action( 'user_registration_validate_email_whitelist', $data->value, $filter_hook, $single_form_field, $form_id );
			}

			if ( 'honeypot' === $single_form_field->field_key ) {
				/**
				 * Action validate honeypot container.
				 *
				 * @param array $data The data.
				 * @param string $filter_hook The dynamic Filter hook.
				 * @param int $form_id The form ID.
				 * @param array $form_data The form data.
				 */
				do_action( 'user_registration_validate_honeypot_container', $data, $filter_hook, $form_id, $form_data );
			}

			/**
			 * Slot booking backend validation.
			 *
			 * @since 4.1.0
			 */
			if ( 'date' === $single_form_field->field_key || 'timepicker' === $single_form_field->field_key ) {
				/**
				 * Action validate slot booking.
				 *
				 * @param array $form_data The form data.
				 * @param string $filter_hook The dynamic Filter hook.
				 * @param array $single_form_field The form field.
				 * @param int $form_id The form ID.
				 */
				do_action( 'user_registration_validate_slot_booking', $form_data, $filter_hook, $single_form_field, $form_id );
			}

			if (
				isset( $single_form_field->advance_setting->enable_conditional_logic ) && ur_string_to_bool( $single_form_field->advance_setting->enable_conditional_logic )
			) {
				$single_form_field->advance_setting->enable_conditional_logic = ur_string_to_bool( $single_form_field->advance_setting->enable_conditional_logic );
			}
			/**
			 * Action validate single field.
			 *
			 * The dynamic portion of the hook name, $hook.
			 *
			 * @param array $single_form_field The form field.
			 * @param array $data The form data.
			 * @param string $filter_hook The dynamic filter hook.
			 * @param int $form_id The form ID.
			 */
			do_action( $hook, $single_form_field, $data, $filter_hook, $form_id );

			/**
			 * Filter the validate message.
			 *
			 * The dynamic portion of the hook name, $filter_hook.
			 * Default value is blank string.
			 */
			$response = apply_filters( $filter_hook, '' );

			if ( ! empty( $response ) ) {
				array_push( $response_array, $response );
			}
			remove_all_filters( $filter_hook );
		}
		return array( $response_array, $valid_form_data );
	}
}

if ( ! function_exists( 'user_registration_validate_edit_profile_form_field_data' ) ) {

	/**
	 * Function to validate edit profile individual form field data.
	 *
	 * @param object $data Form field data submitted by the user.
	 * @param array  $form_data Form Data.
	 * @param int    $form_id Form id.
	 * @param array  $form_field_data Form Field Data..
	 * @param array  $form_fields Form Fields.
	 * @param int    $user_id User ID.
	 */
	function user_registration_validate_edit_profile_form_field_data( $data, $form_data, $form_id, $form_field_data, $form_fields, $user_id ) {
		$form_validator   = new UR_Form_Validation();
		$skippable_fields = $form_validator->get_update_profile_validation_skippable_fields( $form_field_data );
		$form_key_list    = wp_list_pluck( wp_list_pluck( $form_field_data, 'general_setting' ), 'field_name' );

		$single_field_name = strpos( $data->field_name, 'user_registration_' ) !== -1 ? trim( str_replace( 'user_registration_', '', $data->field_name ) ) : $data->field_name;

		if ( ! in_array( $single_field_name, $skippable_fields, true ) && in_array( $single_field_name, $form_key_list, true ) ) {
			$form_data_index   = array_search( $single_field_name, $form_key_list, true );
			$single_form_field = $form_field_data[ $form_data_index ];

			$general_setting    = isset( $single_form_field->general_setting ) ? $single_form_field->general_setting : new stdClass();
			$single_field_key   = $single_form_field->field_key;
			$single_field_label = isset( $general_setting->label ) ? $general_setting->label : '';
			$single_field_value = isset( $data->value ) ? $data->value : '';
			$data->extra_params = array(
				'field_key' => $single_field_key,
				'label'     => $single_field_label,
			);

			/**
			 * Validate form field according to the validations set in $validations array.
			 *
			 * @see form_validator->get_field_validations()
			 */
			$validations = $form_validator->get_field_validations( $single_field_key );

			$required = isset( $single_form_field->general_setting->required ) ? $single_form_field->general_setting->required : false;

			$urcl_hide_fields = isset( $_POST['urcl_hide_fields'] ) ? (array) json_decode( stripslashes( $_POST['urcl_hide_fields'] ), true ) : array(); //phpcs:ignore;

			if ( ! in_array( $single_field_name, $urcl_hide_fields, true ) && ur_string_to_bool( $required ) ) {
				array_unshift( $validations, 'required' );
			}

			if ( ! empty( $validations ) ) {

				if ( in_array( 'required', $validations, true ) || ! empty( $single_field_value ) ) {
					foreach ( $validations as $validation ) {
						$result = UR_Form_Validation::$validation( $single_field_value );

						if ( is_wp_error( $result ) ) {
							$error_code = $result->get_error_code();
							$message    = $form_validator->get_error_message( $error_code, $single_field_label );
							ur_add_notice( $message, 'error' );
							break;
						}
					}
				}
			}

			/**
			 * Filter to allow modification of value.
			 *
			 * The dynamic portion of the hook name, $single_field_name.
			 *
			 * @param array $single_field_value The single field value.
			 */
			$single_field_value = apply_filters( 'user_registration_process_myaccount_field_' . $single_field_name, wp_unslash( $single_field_value ) );

			if ( isset( $data->field_type ) && 'email' === $data->field_type ) {
				/**
				 * Action validate email whitelist.
				 *
				 * @param array $single_field_value The data value.
				 * @param array $single_form_field The single form field.
				 * @param int $form_id The form ID.
				 */
				do_action( 'user_registration_validate_email_whitelist', sanitize_text_field( $single_field_value ), '', $single_form_field, $form_id );
			}

			/**
			 * Slot booking backend validation.
			 *
			 * @since 4.1.0
			 */
			if ( 'date' === $single_form_field->field_key || 'timepicker' === $single_form_field->field_key ) {
				/**
				 * Action validate slot booking.
				 *
				 * @param array $form_data The form data.
				 * @param array $single_form_field The field setting.
				 * @param int $form_id The form ID.
				 */
				do_action( 'user_registration_validate_slot_booking', $form_data, '', $single_form_field, $form_id );
			}

			if ( 'user_email' === $single_form_field->field_key ) {
				// Do not allow admin to update others email, case may change in future
				if ( ! email_exists( sanitize_text_field( wp_unslash( $single_field_value ) ) ) && $user_id !== get_current_user_id() ) {
					ur_add_notice( esc_html__( 'Email field is not editable.', 'user-registration' ), 'error' );
				}
				// Check if email already exists before updating user details.
				if ( email_exists( sanitize_text_field( wp_unslash( $single_field_value ) ) ) && email_exists( sanitize_text_field( wp_unslash( $single_field_value ) ) ) !== $user_id ) {
					ur_add_notice( esc_html__( 'Email already exists', 'user-registration' ), 'error' );
				}
			}

			$form_validator->run_field_validations_on_profile_update( $single_field_key, $single_form_field, $data, $form_id );

			/** Action to add extra validation to edit profile fields.
			 *
			 * The dynamic portion of the hook name, $single_field_key.
			 *
			 * @param array $single_field_value The single field value.
			 * @param array $single_form_field The form field.
			 */
			do_action( 'user_registration_validate_field_' . $single_field_key, wp_unslash( $single_field_value ), $single_form_field ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		}
	}
}

if ( ! function_exists( 'user_registration_edit_profile_row_template' ) ) {

	/**
	 * Generate edit profile individual row template
	 *
	 * @param array  $data Form row data.
	 * @param array  $profile User profile data.
	 * @param string $current_row Current row id.
	 * @param string $row_count Current row count.
	 */
	function user_registration_edit_profile_row_template( $data, $profile, $current_row = '', $row_count = '' ) {

		$user_id = ! empty( $_REQUEST['user_id'] ) ? absint( $_REQUEST['user_id'] ) : get_current_user_id();
		$form_id = ur_get_form_id_by_userid( $user_id );
		$width   = floor( 100 / count( $data ) ) - count( $data );
		$is_edit = isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'edit' && $user_id !== get_current_user_id();

		foreach ( $data as $grid_key => $grid_data ) {
			$found_field = false;

			foreach ( $grid_data as $grid_data_key => $single_item ) {
				if ( ! isset( $single_item->general_setting->field_name ) ) {
					continue;
				}

				$key = 'user_registration_' . $single_item->general_setting->field_name;
				if ( isset( $single_item->field_key ) ) {
					$found_field = isset( $profile[ $key ] );
				}
				if ( $found_field ) {
					break;
				}
			}

			if ( $found_field ) {
				?>
			<div class="ur-form-grid ur-grid-<?php echo esc_attr( ( $grid_key + 1 ) ); ?>" style="width:<?php echo esc_attr( $width ); ?>%;">
				<?php
			}

			foreach ( $grid_data as $grid_data_key => $single_item ) {

				if ( ! isset( $single_item->general_setting->field_name ) ) {
					continue;
				}

				$key = 'user_registration_' . $single_item->general_setting->field_name;

				if ( $found_field ) {
					$form_id = ur_get_form_id_by_userid( $user_id );
					$field   = isset( $profile[ $key ] ) ? $profile[ $key ] : array();

					$field['input_class']       = array( 'ur-edit-profile-field ' );
					$advance_data               = array(
						'general_setting' => (object) $single_item->general_setting,
						'advance_setting' => (object) $single_item->advance_setting,
					);
					$field['custom_attributes'] = isset( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ? $field['custom_attributes'] : array();
					$field_id                   = $single_item->general_setting->field_name;
					$cl_props                   = null;

					// If the conditional logic addon is installed.
					if ( class_exists( 'UserRegistrationConditionalLogic' ) ) {
						// Migrate the conditional logic to logic_map schema.
						$single_item = class_exists( 'URCL_Field_Settings' ) ? URCL_Field_Settings::migrate_to_logic_map_schema( $single_item ) : $single_item;

						$cl_enabled = isset( $single_item->advance_setting->enable_conditional_logic ) && ur_string_to_bool( $single_item->advance_setting->enable_conditional_logic );
						$cl_props   = sprintf( 'data-conditional-logic-enabled="%s"', esc_attr( $cl_enabled ) );

						if ( $cl_enabled && isset( $single_item->advance_setting->cl_map ) ) {
							$cl_map   = esc_attr( $single_item->advance_setting->cl_map );
							$cl_props = sprintf( 'data-conditional-logic-enabled="%s" data-conditional-logic-map="%s"', esc_attr( $cl_enabled ), esc_attr( $cl_map ) );
						}
					}

					if ( 'profile_picture' === $single_item->field_key ) {
						continue;
					}

					// unset invite code.
					if ( 'invite_code' === $single_item->field_key ) {
						continue;
					}
					// unset learndash code.
					if ( 'learndash_course' === $single_item->field_key ) {
						continue;
					}

					// Unset multiple choice and single item.
					if ( 'subscription_plan' === $single_item->field_key || 'multiple_choice' === $single_item->field_key || 'single_item' === $single_item->field_key || 'captcha' === $single_item->field_key || 'stripe_gateway' === $single_item->field_key ) {
						continue;
					}

					?>
					<div class="ur-field-item field-<?php echo esc_attr( $single_item->field_key );?> <?php echo esc_attr( ! empty( $single_item->advance_setting->custom_class ) ? $single_item->advance_setting->custom_class : '' ); ?>"  <?php echo $cl_props; //PHPCS:ignore?> data-field-id="<?php echo esc_attr( $field_id ); ?>" data-ref-id="<?php echo esc_attr( $key ); ?>">
					<?php
					$readonly_fields = ur_readonly_profile_details_fields();
					if ( $is_edit ) {
						unset( $readonly_fields['user_pass'] );
					}
					if ( isset( $field['field_key'] ) && array_key_exists( $field['field_key'], $readonly_fields ) ) {
						$field['custom_attributes']['readonly'] = 'readonly';
						if ( isset( $readonly_fields[ $field['field_key'] ] ['value'] ) ) {
							$field['value'] = $readonly_fields[ $field['field_key'] ] ['value'];
						}
						if ( isset( $readonly_fields[ $field['field_key'] ] ['message'] ) ) {
							$field['custom_attributes']['title'] = $readonly_fields[ $field['field_key'] ] ['message'];
							$field['input_class'][]              = 'user-registration-help-tip';
						}
					}

					if ( 'number' === $single_item->field_key ) {
						$field['min']  = isset( $advance_data['advance_setting']->min ) ? $advance_data['advance_setting']->min : '';
						$field['max']  = isset( $advance_data['advance_setting']->max ) ? $advance_data['advance_setting']->max : '';
						$field['step'] = isset( $advance_data['advance_setting']->step ) ? $advance_data['advance_setting']->step : '';
					}
					$length_validation_fields = array( 'text', 'textarea' , 'display_name', 'first_name','last_name','description','nickname');
					if ( in_array( $single_item->field_key, $length_validation_fields, true ) ) {
						if ( isset( $advance_data['advance_setting']->limit_length ) && $advance_data['advance_setting']->limit_length ) {
							if ( isset( $advance_data['advance_setting']->limit_length_limit_count ) && isset( $advance_data['advance_setting']->limit_length_limit_mode ) ) {
								if ( 'characters' === $advance_data['advance_setting']->limit_length_limit_mode ) {
									$field['max-characters'] = $advance_data['advance_setting']->limit_length_limit_count;
								} elseif ( 'words' === $advance_data['advance_setting']->limit_length_limit_mode ) {
									$field['max-words'] = $advance_data['advance_setting']->limit_length_limit_count;
								}
							}
						}

						if ( isset( $advance_data['advance_setting']->minimum_length ) && $advance_data['advance_setting']->minimum_length ) {
							if ( isset( $advance_data['advance_setting']->minimum_length_limit_count ) && isset( $advance_data['advance_setting']->minimum_length_limit_mode ) ) {
								if ( 'characters' === $advance_data['advance_setting']->minimum_length_limit_mode ) {
									$field['min-characters'] = $advance_data['advance_setting']->minimum_length_limit_count;
								} elseif ( 'words' === $advance_data['advance_setting']->minimum_length_limit_mode ) {
									$field['min-words'] = $advance_data['advance_setting']->minimum_length_limit_count;
								}
							}
						}
					}

					if ( 'range' === $single_item->field_key ) {
						$field['range_min']             = ( isset( $advance_data['advance_setting']->range_min ) && '' !== $advance_data['advance_setting']->range_min ) ? $advance_data['advance_setting']->range_min : '0';
						$field['range_max']             = ( isset( $advance_data['advance_setting']->range_max ) && '' !== $advance_data['advance_setting']->range_max ) ? $advance_data['advance_setting']->range_max : '10';
						$field['range_step']            = isset( $advance_data['advance_setting']->range_step ) ? $advance_data['advance_setting']->range_step : '1';
						$field['enable_payment_slider'] = isset( $advance_data['advance_setting']->enable_payment_slider ) ? $advance_data['advance_setting']->enable_payment_slider : 'false';

						if ( ur_string_to_bool( $advance_data['advance_setting']->enable_prefix_postfix ) ) {
							if ( ur_string_to_bool( $advance_data['advance_setting']->enable_text_prefix_postfix ) ) {
								$field['range_prefix']  = isset( $advance_data['advance_setting']->range_prefix ) ? $advance_data['advance_setting']->range_prefix : '';
								$field['range_postfix'] = isset( $advance_data['advance_setting']->range_postfix ) ? $advance_data['advance_setting']->range_postfix : '';
							} else {
								$field['range_prefix']  = $field['range_min'];
								$field['range_postfix'] = $field['range_max'];
							}
						}

						// to hide the range as payment slider in edit profile.
						if ( ur_string_to_bool( $field['enable_payment_slider'] ) ) {
							continue;
						}
					}

					if ( 'phone' === $single_item->field_key ) {
						$field['phone_format'] = $single_item->general_setting->phone_format;
						if ( 'smart' === $field['phone_format'] ) {
							unset( $field['input_mask'] );
						}
					}

					if ( 'password' === $single_item->field_key ) {
						$field['size'] = $advance_data['advance_setting']->size;
					}

					if ( isset( $single_item->general_setting->hide_label ) ) {
						if ( ur_string_to_bool( $single_item->general_setting->hide_label ) ) {
							unset( $field['label'] );
						}
					}

					if ( 'select' === $single_item->field_key ) {
						$option_data         = isset( $advance_data['advance_setting']->options ) ? explode( ',', $advance_data['advance_setting']->options ) : array();
						$option_advance_data = isset( $advance_data['general_setting']->options ) ? $advance_data['general_setting']->options : $option_data;
						$options             = array();

						if ( is_array( $option_advance_data ) ) {
							foreach ( $option_advance_data as $index_data => $option ) {
								$options[ $option ] = ur_string_translation( $form_id, 'user_registration_' . $advance_data['general_setting']->field_name . '_option_' . ( ++$index_data ), $option );
							}
							$field['options'] = $options;
						}

						$field['placeholder'] = $single_item->general_setting->placeholder;

					}

					if ( 'radio' === $single_item->field_key ) {
						if ( isset( $advance_data['general_setting']->image_choice ) && ur_string_to_bool( $advance_data['general_setting']->image_choice ) ) {
							$option_advance_data = isset( $advance_data['general_setting']->image_options ) ? $advance_data['general_setting']->image_options : array();
							$options             = array();
							if ( is_array( $option_advance_data ) ) {
								foreach ( $option_advance_data as $index_data => $option ) {
									$options[ $option->label ] = array(
										'label' => ur_string_translation( $form_id, 'user_registration_' . $advance_data['general_setting']->field_name . '_option_' . ( ++$index_data ), $option->label ),
										'image' => $option->image,
									);
								}
								$field['image_options'] = $options;
							}
						} else {
							$option_advance_data = isset( $advance_data['general_setting']->options ) ? $advance_data['general_setting']->options : array();
							$options             = array();

							if ( is_array( $option_advance_data ) ) {
								foreach ( $option_advance_data as $index_data => $option ) {
									$options[ $option ] = ur_string_translation( $form_id, 'user_registration_' . $advance_data['general_setting']->field_name . '_option_' . ( ++$index_data ), $option );
								}
								$field['options'] = $options;
							}
						}
					}

					if ( 'file' === $single_item->field_key ) {
						if ( isset( $single_item->general_setting->max_files ) ) {
							$field['max_files'] = $single_item->general_setting->max_files;
						} else {
							$field['max_files'] = 1;
						}

						if ( isset( $advance_data['advance_setting']->max_upload_size ) ) {
							$field['max_upload_size'] = $advance_data['advance_setting']->max_upload_size;
						}

						if ( isset( $advance_data['advance_setting']->valid_file_type ) ) {
							$field['valid_file_type'] = $advance_data['advance_setting']->valid_file_type;
						}

						// Remove files attachment id from user meta if file is deleted by admin.
						if ( isset( $field['value'] ) && '' !== $field['value'] ) {
							$attachment_ids = is_array( $field['value'] ) ? $field['value'] : explode( ',', $field['value'] );

							foreach ( $attachment_ids as $attachment_key => $attachment_id ) {
								$attachment_url = get_attached_file( $attachment_id );

								// Check to see if file actually exists or not.
								if ( '' !== $attachment_url && file_exists( $attachment_url ) ) {
									continue;
								}
								unset( $attachment_ids[ $attachment_key ] );
							}

							$field['value'] = ! empty( $attachment_ids ) ? implode( ',', $attachment_ids ) : '';
							update_user_meta( get_current_user_id(), 'user_registration_' . $single_item->general_setting->field_name, $field['value'] );
						}
					}

					if ( isset( $advance_data['general_setting']->required ) ) {
						if ( in_array( $single_item->field_key, ur_get_required_fields() )
						|| ur_string_to_bool( $advance_data['general_setting']->required ) ) {
							$field['required']                      = true;
							$field['custom_attributes']['required'] = 'required';
						}
					}

					// Add choice_limit setting valur in order to limit choice fields.
					if ( 'checkbox' === $single_item->field_key || 'multi_select2' === $single_item->field_key ) {
						if ( isset( $advance_data['general_setting']->image_choice ) && ur_string_to_bool( $advance_data['general_setting']->image_choice ) ) {
							$option_data = isset( $advance_data['general_setting']->image_options ) ? $advance_data['general_setting']->image_options : array();
							$options     = array();

							if ( is_array( $option_data ) ) {
								foreach ( $option_data as $index_data => $option ) {
									$options[ $option->label ] = array(
										'label' => ur_string_translation( $form_id, 'user_registration_' . $advance_data['general_setting']->field_name . '_option_' . ( ++$index_data ), $option->label ),
										'image' => $option->image,
									);
								}
								$field['image_options'] = $options;
							}
						} else {
							$option_data = isset( $advance_data['general_setting']->options ) ? $advance_data['general_setting']->options : array();
							$options     = array();

							if ( is_array( $option_data ) ) {
								foreach ( $option_data as $index_data => $option ) {
									$options[ $option ] = ur_string_translation( $form_id, 'user_registration_' . $advance_data['general_setting']->field_name . '_option_' . ( ++$index_data ), $option );
								}

								$field['options'] = $options;
							}
						}

						if ( isset( $advance_data['advance_setting']->choice_limit ) ) {
							$field['choice_limit'] = $advance_data['advance_setting']->choice_limit;
						}
						if ( isset( $advance_data['advance_setting']->select_all ) ) {
							$field['select_all'] = ur_string_to_bool( $advance_data['advance_setting']->select_all );
						}
					}

					if ( 'timepicker' === $single_item->field_key ) {
						$field['current_time']  = isset( $advance_data['advance_setting']->current_time ) ? $advance_data['advance_setting']->current_time : '';
						$field['time_interval'] = isset( $advance_data['advance_setting']->time_interval ) ? $advance_data['advance_setting']->time_interval : '';
						$field['time_format']   = isset( $advance_data['advance_setting']->time_format ) ? $advance_data['advance_setting']->time_format : '';
						$field['time_range']    = isset( $advance_data['advance_setting']->time_range ) ? $advance_data['advance_setting']->time_range : '';
						$field['time_min']      = ( isset( $advance_data['advance_setting']->time_min ) && '' !== $advance_data['advance_setting']->time_min ) ? $advance_data['advance_setting']->time_min : '';
						$field['time_max']      = ( isset( $advance_data['advance_setting']->time_max ) && '' !== $advance_data['advance_setting']->time_max ) ? $advance_data['advance_setting']->time_max : '';
						$timemin                = isset( $field['time_min'] ) ? strtolower( substr( $field['time_min'], -2 ) ) : '';
						$timemax                = isset( $field['time_max'] ) ? strtolower( substr( $field['time_max'], -2 ) ) : '';
						$minampm                = intval( $field['time_min'] ) <= 12 ? 'AM' : 'PM';
						$maxampm                = intval( $field['time_max'] ) <= 12 ? 'AM' : 'PM';
						// For slot booking.
						$field['enable_time_slot_booking'] = isset( $advance_data['advance_setting']->enable_time_slot_booking ) ? $advance_data['advance_setting']->enable_time_slot_booking : '';
						$field['target_date_field']        = isset( $advance_data['advance_setting']->target_date_field ) ? $advance_data['advance_setting']->target_date_field : '';
							// Handles the time format.
						if ( 'am' === $timemin || 'pm' === $timemin ) {
							$field['time_min'] = $field['time_min'];
						} else {
							$field['time_min'] = $field['time_min'] . '' . $minampm;
						}

						if ( 'am' === $timemax || 'pm' === $timemax ) {
							$field['time_max'] = $field['time_max'];
						} else {
							$field['time_max'] = $field['time_max'] . '' . $maxampm;
						}
					}

					if ( 'date' === $single_item->field_key ) {
						// For slot booking.
						$field['enable_date_slot_booking'] = isset( $advance_data['advance_setting']->enable_date_slot_booking ) ? $advance_data['advance_setting']->enable_date_slot_booking : false;
					}
					$field['form_id'] = $form_id;
					$filter_data = array(
						'form_data' => $field,
						'data'      => $advance_data,
					);

					$field_key       = isset( $field['field_key'] ) ? $field['field_key'] : '';
					$form_data_array = apply_filters( 'user_registration_' . $field_key . '_frontend_form_data', $filter_data, true );
					$field           = isset( $form_data_array['form_data'] ) ? $form_data_array['form_data'] : $field;
					$value           = ! empty( $_POST[ $key ] ) ? ur_clean( wp_unslash( $_POST[ $key ] ) ) : ( isset( $field['value'] ) ? $field['value'] : '' ); // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

					if ( isset( $field['field_key'] ) ) {
						$row_count_to_send = '' === $row_count ? $current_row : $row_count;
						$field             = user_registration_form_field( $key, $field, $value, $row_count_to_send, $is_edit );
					}

					/**
					 * Embed the current country value to allow to remove it if it's not allowed.
					 */
					if ( 'country' === $single_item->field_key && ! empty( $value ) ) {
						printf( '<span hidden class="ur-data-holder" data-option-value="%s" data-option-html="%s"></span>', esc_attr( $value ), esc_attr( UR_Form_Field_Country::get_instance()->get_country()[ $value ] ) );
					}
					?>
					</div>
					<?php } ?>
				<?php } ?>

					<?php if ( $found_field ) { ?>
				</div>
						<?php
					}
		}
		if ( ! $is_edit ) {
			echo apply_filters( 'user_registration_frontend_form_row_end', '', $form_id, $current_row ); // phpcs:ignore
		}
	}
}

if ( ! file_exists( 'user_registration_sanitize_profile_update' ) ) {

	/**
	 * Sanitize the data submitted by user.
	 *
	 * @param array  $submitted_data Submitted data.
	 * @param string $field_type Field Type.
	 * @param string $key Field Key.
	 */
	function user_registration_sanitize_profile_update( $submitted_data, $field_type, $key ) {

		$value = '';

		switch ( $field_type ) {
			case 'checkbox':
				if ( isset( $submitted_data[ $key ] ) && is_array( $submitted_data[ $key ] ) ) { // phpcs:ignore
					$value = wp_unslash( $submitted_data[ $key ] ); // phpcs:ignore
				} else {
					$value = (int) isset( $submitted_data[ $key ] ); // phpcs:ignore
				}
				break;

			case 'wysiwyg':
				if ( isset( $submitted_data[ $key ] ) ) { // phpcs:ignore
					$value = sanitize_text_field( htmlentities( wp_unslash( $submitted_data[ $key ] ) ) ); // phpcs:ignore
				} else {
					$value = '';
				}
				break;

			case 'email':
				if ( isset( $submitted_data[ $key ] ) ) { // phpcs:ignore
					$value = sanitize_email( wp_unslash( $submitted_data[ $key ] ) ); // phpcs:ignore
				} else {
					$user_id   = get_current_user_id();
					$user_data = get_userdata( $user_id );
					$value     = $user_data->data->user_email;
				}
				break;
			case 'profile_picture':
				if ( isset( $submitted_data['profile_pic_url'] ) ) { // phpcs:ignore
					$value = sanitize_text_field( wp_unslash( $submitted_data['profile_pic_url'] ) ); // phpcs:ignore
				} else {
					$value = '';
				}
				break;
			default:
				$value = isset( $submitted_data[ $key ] ) ? $submitted_data[ $key ] : ''; // phpcs:ignore
				break;
		}

		return $value;
	}
}

if ( ! function_exists( 'ur_get_coupon_details' ) ) {
	/**
	 * This function will send email verification email to the user.
	 *
	 * @since 3.1.5
	 *
	 * @param int $user_id User ID.
	 */
	function ur_get_coupon_details( $coupon ) {

		$posts = new WP_Query(
			array(
				'post_type'  => 'ur_coupons',
				'meta_key'   => 'ur_coupon_code',
				'meta_query' => array(
					array(
						'key'     => 'ur_coupon_code',
						'value'   => $coupon,
						'compare' => '=',
					),
				),
			)
		);

		if ( $posts->post_count > 0 ) {
			$posts_meta               = get_post_meta( $posts->post->ID, 'ur_coupon_meta', true );
			$coupon_data              = json_decode( $posts_meta, true );
			$coupon_data['coupon_id'] = $posts->post->ID;
			return $coupon_data;
		}

		return $posts->posts;
	}
}

if ( ! function_exists( 'ur_get_registration_field_value_by_field_name' ) ) {

	/**
	 * Get Field value by field name while registration.
	 *
	 * @since 0
	 *
	 * @param  string $field_name Field Name.
	 */
	function ur_get_registration_field_value_by_field_name( $field_name ) {
		$field_value = '';

		if ( isset( $_POST['form_data'] ) ) { // phpcs:ignore
			$form_data = json_decode( wp_unslash( $_POST['form_data'] ) ); // phpcs:ignore
		}
		if ( gettype( $form_data ) != 'array' && gettype( $form_data ) != 'object' ) {
			$form_data = array();
		}
		foreach ( $form_data as $index => $single_data ) {

			if ( $field_name == $single_data->field_name ) {
				$field_value = $single_data->value;
			}
		}
		return $field_value;
	}
}


if ( ! function_exists( 'ur_get_translated_string' ) ) {
	/**
	 * Function to get translated string using WPML
	 *
	 * @since 4.2.1
	 *
	 * @param  string $domain Domain.
	 * @param  string $string String Value.
	 * @param  string $language_code Language Code.
	 * @param  string $field_key Field Key.
	 * @param  string $form_id Form ID.
	 */
	function ur_get_translated_string( $domain, $string, $language_code, $field_key, $form_id = 0 ) {
		if ( function_exists( 'icl_translate' ) ) {
			$language_code     = is_array( $language_code ) ? $language_code[0] : $language_code;
			$translated_string = apply_filters( 'wpml_translate_single_string', $string, $domain, $field_key, $language_code );

			if ( false === $translated_string || $translated_string === $language_code ) {
				return $string;
			} else {
				return $translated_string;
			}
		} else {
			return $string;
		}
	}
}

add_action( 'init', 'ur_check_is_disabled' );
if ( ! function_exists( 'ur_check_is_disabled' ) ) {

	/**
	 * Check if user is disabled.
	 */
	function ur_check_is_disabled() {
		$is_auto_enable = get_user_meta( get_current_user_id(), 'ur_auto_enable_time', true );
		if ( $is_auto_enable ) {
			$current_time = current_time( 'timestamp' );
			if ( $current_time >= $is_auto_enable ) {
				delete_user_meta( get_current_user_id(), 'ur_auto_enable_time' );
				delete_user_meta( get_current_user_id(), 'ur_disable_users' );
			}
		}
		$is_disabled = get_user_meta( get_current_user_id(), 'ur_disable_users', true );
		if ( $is_disabled ) {
			wp_logout();
		}
	}
}

add_action( 'init', 'ur_check_is_denied' );

if ( ! function_exists( 'ur_check_is_denied' ) ) {
	/**
	 * Check if user is denied.
	 */
	function ur_check_is_denied() {
		$is_denied = get_user_meta( get_current_user_id(), 'ur_user_status', true );
		if ( '-1' === $is_denied ) {
			wp_logout();
		}
	}
}

add_action( 'init', 'ur_check_is_inactive' );

if ( ! function_exists( 'ur_check_is_inactive' ) ) {
	/**
	 * Check if user is denied.
	 */
	function ur_check_is_inactive() {
		if ( ! ur_check_module_activation( 'membership' ) ||
			 current_user_can( 'manage_options' ) ||
			 ( ! empty( $_POST['action'] ) && in_array( $_POST['action'], array(
					"user_registration_membership_confirm_payment",
					"user_registration_membership_create_stripe_subscription"
				) ) )
		) {
			return;
		}
		$members_repository = new \WPEverest\URMembership\Admin\Repositories\MembersRepository();

		$membership = $members_repository->get_member_membership_by_id( get_current_user_id() );

		if ( empty( $membership ) ) {
			return;
		}

		if ( in_array( $membership['status'], array( 'pending', 'canceled', 'inactive' ) ) ) {
			wp_logout();
		}

	}
}
if ( ! function_exists( 'ur_check_is_auto_enable_user' ) ) {

	/**
	 * Check if user is auto enabled.
	 *
	 * @param int $user_id User ID.
	 */
	function ur_check_is_auto_enable_user( $user_id = 0 ) {
		if ( 0 === $user_id || '' === $user_id ) {
			return;
		}

		$is_auto_enable = get_user_meta( $user_id, 'ur_auto_enable_time', true );
		if ( ! $is_auto_enable || '' === $is_auto_enable ) {
			return;
		}
		if ( strtotime( $is_auto_enable ) < strtotime( date( 'Y-m-d H:i:s' ) ) ) {
			delete_user_meta( $user_id, 'ur_auto_enable_time' );
			delete_user_meta( $user_id, 'ur_disable_users' );
			return;
		}
	}
}

// Hook the redirection to admin_init
add_action(
	'admin_init',
	'ur_redirect_to_addons_page'
);

if ( ! function_exists( 'ur_redirect_to_addons_page' ) ) {
	/**
	 * Redirect to addons page.
	 */
	function ur_redirect_to_addons_page() {
		if ( isset( $_GET['page'] ) && 'user-registration-addons' === $_GET['page'] ) {
			wp_safe_redirect( esc_url_raw( admin_url( 'admin.php?page=user-registration-dashboard#features' ) ) );
			exit;
		}
	}
}

if ( ! function_exists( 'ur_check_akismet_installation' ) ) {

	/**
	 * Check the configuration status of Akismet.
	 *
	 * @return string The status message indicating whether Akismet is installed, activated, or configured.
	 */
	function ur_check_akismet_installation() {
		$warning_color = '#ffcc00';

		if ( ! file_exists( WP_PLUGIN_DIR . '/akismet/akismet.php' ) ) {
			return sprintf(
				'<div class="ur-form-settings-warning"><span style="color: %s;">%s</span>%s %s %s %s</div>',
				$warning_color,
				esc_html__( 'Warning:-', 'user-registration' ),
				esc_html__( ' This feature is inactive because', 'user-registration' ),
				'<a href="' . esc_url_raw( 'https://wordpress.org/plugins/akismet/' ) . '" rel="noreferrer noopener" target="_blank">' . esc_html__( 'Akismet', 'user-registration' ) . '</a>',
				esc_html__( 'plugin has not been installed. For more', 'user-registration' ),
				'<a href="https://docs.wpuserregistration.com/docs/individual-form-settings/#10-toc-title" rel="noreferrer noopener" target="_blank">' . esc_html__( 'information.', 'user-registration' ) . '</a>'
			);
		} elseif ( ! is_plugin_active( 'akismet/akismet.php' ) ) {
			return sprintf(
				'<div class="ur-form-settings-warning"><span style="color: %s;">%s</span>%s %s %s %s</div>',
				$warning_color,
				esc_html__( 'Warning:- ', 'user-registration' ),
				esc_html__( 'This feature is inactive because', 'user-registration' ),
				'<a href="' . esc_url( admin_url( 'plugins.php' ) ) . '" rel="noreferrer noopener" target="_blank">' . esc_html__( 'Akismet', 'user-registration' ) . '</a>',
				esc_html__( 'plugin is not activated. For more', 'user-registration' ),
				'<a href="https://docs.wpuserregistration.com/docs/individual-form-settings/#10-toc-title" rel="noreferrer noopener" target="_blank">' . esc_html__( 'information.', 'user-registration' ) . '</a>'
			);
		} elseif ( ! ur_is_akismet_configured() ) {
			return sprintf(
				'<div class="ur-form-settings-warning"><span style="color: %s;">%s</span>%s %s %s %s</div>',
				$warning_color,
				esc_html__( 'Warning:-', 'user-registration' ),
				esc_html__( 'This feature is inactive because', 'user-registration' ),
				'<a href="' . esc_url( admin_url( 'options-general.php?page=akismet-key-config' ) ) . '" rel="noreferrer noopener" target="_blank">' . esc_html__( 'Akismet', 'user-registration' ) . '</a>',
				esc_html__( 'plugin has not been properly configured. For more', 'user-registration' ),
				'<a href="https://docs.wpuserregistration.com/docs/individual-form-settings/#10-toc-title" rel="noreferrer noopener" target="_blank">' . esc_html__( 'information.', 'user-registration' ) . '</a>'
			);
		}
	}

}

if ( ! function_exists( 'ur_is_akismet_configured' ) ) {

	/**
	 * Has the Akismet plugin been configured wih a valid API key?
	 *
	 * @since 4.2.1.2
	 *
	 * @return bool
	 */
	function ur_is_akismet_configured() {

		if ( ! is_plugin_active( 'akismet/akismet.php' ) ) {
			return false;
		}
		require_once WP_PLUGIN_DIR . '/akismet/akismet.php';

		$akismet_instance = new Akismet();
		// Akismet will only allow an API key to be saved if it is a valid key.
		// We can assume that if there is an API key saved, it is valid.
		$akismet_api_key = $akismet_instance->get_api_key();

		if ( ! empty( $akismet_api_key ) ) {
			return true;
		}

		return false;
	}


}

add_filter( 'user_registration_get_akismet_validate', 'ur_get_akismet_validate', 10, 2 );
if ( ! function_exists( 'ur_get_akismet_validate' ) ) {
	/**
	 * Check if registration should be validated by Akismet for potential spam.
	 *
	 * This function checks whether the Akismet plugin is installed and configured, and if the Akismet
	 * validation option is enabled for a specific form. If validation is enabled, it prepares the
	 * necessary data for the validation request and sends it to Akismet's 'registration-check' endpoint.
	 *
	 * @param int   $form_id The form_id to check if to validate.
	 * @param array $form_data  values to validate.
	 *
	 * @return bool
	 *   - true if the form_data is potentially spam according to Akismet.
	 *   - false if Akismet validation is not enabled, the plugin is not properly configured, or the form_data is not considered spam.
	 */
	function ur_get_akismet_validate( $form_id, $form_data ) {
		if ( ! file_exists( WP_PLUGIN_DIR . '/akismet/akismet.php' ) ) {
			return false;
		}

		if ( ! ur_is_akismet_configured() ) {
			return false;
		}

		$is_akismet_enabled = ur_get_single_post_meta( $form_id, 'user_registration_enable_akismet' );
		if ( $is_akismet_enabled ) {

			$form_content = ur_get_form_data_for_akismet( $form_data );

			$request = array(
				'blog'                 => home_url(),
				'user_ip'              => ur_get_ip_address(),
				'user_agent'           => isset( $_SERVER['HTTP_USER_AGENT'] ) ? wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) : null, // phpcs:ignore
				'referrer'             => wp_get_referer() ? wp_get_referer() : null,
				'permalink'            => ur_current_url(),
				'comment_type'         => 'registration',
				'comment_author'       => isset( $form_content['user_login'] ) ? $form_content['user_login'] : '',
				'comment_author_email' => isset( $form_content['user_email'] ) ? $form_content['user_email'] : '',
				'comment_author_url'   => isset( $form_content['user_url'] ) ? $form_content['user_url'] : '',
				'comment_content'      => isset( $form_content['other_content'] ) ? $form_content['other_content'] : '',
				'blog_lang'            => get_locale(),
				'blog_charset'         => get_bloginfo( 'charset' ),
			);

			$response = Akismet::http_post( build_query( $request ), 'comment-check' );
			return ! empty( $response ) && isset( $response[1] ) && 'true' === trim( $response[1] );
		}

		return false;
	}
}

if ( ! function_exists( 'ur_get_form_data_for_akismet' ) ) {
	/**
	 * Get user submitted form data for akismet spam monitoring.
	 *
	 * @param array $form_data User submitted form data.
	 */
	function ur_get_form_data_for_akismet( $form_data ) {
		$field_type_allowlist = ur_get_allowed_field_for_akisment();
		$entry_data           = array();
		$form_content         = array();
		$other_content        = array();

		foreach ( $form_data as $key => $field ) {
			if ( isset( $field->extra_params['field_key'] ) ) {
				$entry_data[ $field->extra_params['field_key'] ] = $field->value;
			}
		}

		foreach ( $entry_data as $field_key => $value ) {
			if ( in_array( $field_key, $field_type_allowlist ) ) {
				switch ( $field_key ) {
					case 'user_email':
						$form_content['user_email'] = $value;
						break;
					case 'user_url':
						$form_content['user_url'] = $value;
						break;
					case 'user_login':
						$form_content['user_login'] = $value;
						break;
					default:
						$other_content[] = $value;
						break;
				}
			}
		}

		if ( ! empty( $other_content ) ) {
			$form_content['other_content'] = implode( ' ', $other_content );
		}
		return $form_content;
	}
}

if ( ! function_exists( 'ur_get_allowed_field_for_akisment' ) ) {
	/**
	 * List of allowed fields for spam protection by akismet.
	 */
	function ur_get_allowed_field_for_akisment() {
		$field_type_allowlist = array(
			'user_login',
			'text',
			'textarea',
			'user_email',
			'phone',
			'address',
			'user_url',
			'wysiwyg',
			'description',
		);
		return $field_type_allowlist;
	}
}

if ( ! function_exists( 'ur_current_url' ) ) {
	/**
	 * Get the current URL.
	 *
	 * @since 3.2.0
	 *
	 * @return string
	 */
	function ur_current_url() {

		$parsed_home_url = wp_parse_url( home_url() );

		$url = $parsed_home_url['scheme'] . '://' . $parsed_home_url['host'];

		if ( ! empty( $parsed_home_url['port'] ) ) {
			$url .= ':' . $parsed_home_url['port'];
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$url .= wp_unslash( $_SERVER['REQUEST_URI'] );

		return esc_url_raw( $url );
	}
}

add_action(
	'admin_head',
	function () {
		$js = <<<JS
		let isSidebarEnabled = localStorage.getItem( 'isSidebarEnabled' );
		isSidebarEnabled = 'false' === isSidebarEnabled ? false : true;

		document.cookie =
		"isSidebarEnabled=" + isSidebarEnabled + "; path=/;";
		const interval = setInterval( () => {
			if ( document.body ) {
				clearInterval(interval);
				if (isSidebarEnabled) {
					document.body.classList.add( 'ur-settings-sidebar-show' );
				} else {
					document.body.classList.add( 'ur-settings-sidebar-hidden' );
				}
			}
		}, 1 );
		JS;
		wp_print_inline_script_tag( $js );
	}
);

if ( ! function_exists( 'ur_quick_settings_tab_content' ) ) {

	/**
	 * Quick settings tab content.
	 */
	function ur_quick_settings_tab_content() {
		$default_form_page_id      = get_option( 'user_registration_default_form_page_id', false );
		$registration_form_page_id = get_option( 'user_registration_registration_page_id', false );
		$my_account_page_id        = get_option( 'user_registration_myaccount_page_id', false );
		$prevent_core_login        = get_option( 'user_registration_login_options_prevent_core_login', false );
		$captcha_setup             = get_option( 'user_registration_captcha_setting_recaptcha_version', false );
		$anyone_can_register       = get_option( 'users_can_register', false );

		$lists = array(
			array(
				'text'          => esc_html__( 'Create a registration form.', 'user-registration' ),
				'completed'     => $default_form_page_id ? true : false,
				'documentation' => esc_url_raw( "https://docs.wpuserregistration.com/docs/how-to-create-a-user-registration-form/?utm_source=settings-sidebar-right&utm_medium=quick-setup-card&utm_campaign='" . UR()->utm_campaign . "'" ),
			),
			array(
				'text'          => esc_html__( 'Create registration and my account page.', 'user-registration' ),
				'completed'     => $registration_form_page_id || $my_account_page_id ? true : false,
				'documentation' => esc_url_raw( "https://docs.wpuserregistration.com/docs/how-to-show-account-profile/?utm_source=settings-sidebar-right&utm_medium=quick-setup-card&utm_campaign='" . UR()->utm_campaign . "'" ),
			),
			array(
				'text'      => esc_html__( 'Enable anyone can register.', 'user-registration' ),
				'completed' => ur_string_to_bool( $anyone_can_register ),
			),
			array(
				'text'          => esc_html__( 'Disable WordPress default registration and login page.', 'user-registration' ),
				'completed'     => $prevent_core_login ? ur_string_to_bool( $prevent_core_login ) : false,
				'documentation' => esc_url_raw( "https://docs.wpuserregistration.com/docs/how-to-hide-the-wordpress-default-login-page-and-use-user-registration-login-page/?utm_source=settings-sidebar-right&utm_medium=quick-setup-card&utm_campaign='" . UR()->utm_campaign . "'" ),
			),
			array(
				'text'          => esc_html__( 'Setup spam protection mechanisms.', 'user-registration' ),
				'completed'     => $captcha_setup ? true : false,
				'documentation' => esc_url_raw( "https://docs.wpuserregistration.com/docs/how-to-integrate-google-recaptcha/?utm_source=settings-sidebar-right&utm_medium=quick-setup-card&utm_campaign='" . UR()->utm_campaign . "'" ),
			),
		);

		$completed_count = 0;

		foreach ( $lists as $list ) {
			if ( isset( $list['completed'] ) && $list['completed'] ) {
				++$completed_count;
			}
		}

		if ( $completed_count === count( $lists ) ) {
			update_option( 'user_registration_quick_setup_completed', true );
		}

		$activation_date   = get_option( 'user_registration_activated' );
		$installation_date = get_option( 'user_registration_installation_date', $activation_date );

		$days_to_validate = strtotime( $installation_date );
		$days_to_validate = strtotime( '+15 day', $days_to_validate );
		$days_to_validate = date_i18n( 'Y-m-d', $days_to_validate );

		$current_date = date_i18n( 'Y-m-d' );

		if ( $current_date > $days_to_validate ) {
			update_option( 'user_registration_quick_setup_completed', true );
		}

		return $lists;
	}
}

add_filter( 'user_registration_settings_text_format', 'ur_settings_text_format', 10 );
if ( ! function_exists( 'ur_settings_text_format' ) ) {
	/**
	 * Settings text format.
	 *
	 * @since 3.3.1
	 *
	 * @param array $args
	 * @return array
	 */
	function ur_settings_text_format( $args ) {
		// Group similar text format fields.
		$fields_to_format = array( 'description', 'tip', 'tooltip', 'tooltip_message', 'desc' );

		foreach ( $args as &$arg ) {
			if ( in_array( $arg['id'], ur_get_exclude_text_format_settings() ) ) {
				continue;
			}

			if ( isset( $arg['label'] ) ) {
				$arg['label'] = ur_get_capitalized_words( $arg['label'] );
			}

			if ( isset( $arg['desc_tip'] ) && ( $arg['desc_tip'] != 1 || $arg['desc_tip'] !== true ) ) {
				$arg['desc_tip'] = ucWords( strtolower( $arg['desc_tip'] ) );
			}

			if ( isset( $arg['title'] ) ) {
				$arg['title'] = strtoupper( $arg['title'] );
			}

			foreach ( $fields_to_format as $field ) {

				if ( isset( $arg[ $field ] ) ) {
					if ( strpos( trim( $arg[ $field ] ), '<div' ) !== 0 ) {
						strpos( trim( $arg[ $field ] ), '<div' );
						$arg[ $field ] = ur_format_sentence_case( strtolower( $arg[ $field ] ) );

					} else {
						$arg[ $field ] = $arg[ $field ];
					}
				}
			}

			if ( isset( $arg['options'] ) && is_array( $arg['options'] ) ) {
				foreach ( $arg['options'] as $key => $option ) {
					$arg['options'][ $key ] = ucfirst( strtolower( $option ) );
				}
			}
		}

		return $args;
	}
}
if ( ! function_exists( 'ur_format_sentence_case' ) ) {
	/**
	 * Capitalizes the first letter of the initial word and each word after a period.
	 *
	 * @param string $string
	 * @return string
	 */
	function ur_format_sentence_case( $string ) {
		$sentences = preg_split( '/(\.\s+)/', $string, -1, PREG_SPLIT_DELIM_CAPTURE );
		foreach ( $sentences as &$sentence ) {
			$sentence = ucfirst( trim( $sentence ) );
		}
		return implode( '', $sentences );
	}
}

if ( ! function_exists( 'ur_get_capitalized_words' ) ) {
	/**
	 * Get form data.
	 *
	 *  @since 3.3.1
	 *
	 * @param string $label
	 * @return array
	 */
	function ur_get_capitalized_words( $label ) {
		$prepositions = array( 'at', 'by', 'for', 'in', 'on', 'to', 'or' );

		$words = explode( ' ', $label );

		$capitalized_words = array();

		foreach ( $words as $word ) {

			$word = trim( $word );
			// Convert the word to lowercase if it is a preposition.
			if ( in_array( strtolower( $word ), $prepositions ) ) {
				$capitalized_words[] = strtolower( $word );
				continue;
			}
			// Convert the word to uppercase if it is an abbreviation.
			if ( strpos( $word, '-' ) !== false || strpos( $word, '/' ) !== false ) {
				$separators = array( '-', '/' );
				foreach ( $separators as $separator ) {
					if ( strpos( $word, $separator ) !== false ) {
						$terms             = explode( $separator, $word );
						$capitalized_terms = array();
						foreach ( $terms as $term ) {
							$capitalized_terms[] = ucfirst( strtolower( $term ) );
						}
						$word = implode( $separator, $capitalized_terms );
						break;
					}
				}
			} else {
				$word = ucfirst( strtolower( $word ) );
			}

			$capitalized_words[] = $word;
		}

		return implode( ' ', $capitalized_words );
	}
}

add_action( 'wp_mail_failed', 'ur_email_send_failed_handler', 1 );

if ( ! function_exists( 'ur_email_send_failed_handler' ) ) {

	/**
	 * Handle errors fetch mechanism when mail send failed.
	 *
	 * @param object $error_instance WP_Error message instance.
	 */
	function ur_email_send_failed_handler( $error_instance ) {
		$error_message = '';

		if ( '' !== json_decode( $error_instance->get_error_message() ) ) {
			/* translators: %s: Status Log URL*/
			$error_message = wp_kses_post( sprintf( __( 'Please check the `ur_mail_logs` log under <a target="_blank" href= "%s"> Status Log </a> section.', 'user-registration' ), admin_url( 'admin.php?page=user-registration-status' ) ) );
			ur_get_logger()->error( $error_instance->get_error_message(), array( 'source' => 'ur_mail_logs' ) );
		} else {
			$error_message = $error_instance->get_error_message();
			ur_get_logger()->error( $error_instance->get_error_message(), array( 'source' => 'ur_mail_logs' ) );
		}

		if ( '' !== $error_message ) {
			add_filter(
				'user_registration_email_send_failed_message',
				function ( $msg ) use ( $error_message ) {
					return $error_message;
				}
			);
		}
	}
}

add_action( 'user_registration_custom_notices', 'ur_email_send_failed_notice' );

if ( ! function_exists( 'ur_email_send_failed_notice' ) ) {

	/**
	 * Add notice about email send failed to be displayed in dashboard.
	 *
	 * @param array $notices Custom notices.
	 */
	function ur_email_send_failed_notice( $notices ) {
		$failed_data = get_transient( 'user_registration_mail_send_failed_count' );

		if ( ! $failed_data ) {
			return $notices;
		}

		$failed_count  = isset( $failed_data['failed_count'] ) ? $failed_data['failed_count'] : 0;
		$error_message = isset( $failed_data['error_message'] ) ? $failed_data['error_message'] : '';

		$custom_notice = array(
			array(
				'id'                    => 'ur_email_send_failed',
				'type'                  => 'info',
				'status'                => 'active',
				'priority'              => '1',
				'title'                 => __( 'User Registration Email Send Error', 'user-registration' ),
				'message_content'       => wp_kses_post(
					sprintf(
						'<p>%s</p><p style="border-left: 2px solid #72aee6; background: #F0FFFF; padding: 10px;">%s</p><br/>',
						__( 'The last emails sent from User Registration Plugin was not delivered to the user. ', 'user-registration' ),
						$error_message
					)
				),
				'buttons'               => array(
					array(
						'title'  => __( 'I have a query', 'user-registration' ),
						'icon'   => 'dashicons-testimonial',
						'link'   => 'https://wpuserregistration.com/support',
						'class'  => 'button-secondary notice-have-query',
						'target' => '_blank',
					),
					array(
						'title'  => __( 'Visit Documentation', 'user-registration' ),
						'icon'   => 'dashicons-media-document',
						'link'   => 'https://docs.wpuserregistration.com/docs/emails-are-not-being-delivered/',
						'class'  => 'button-secondary notice-have-query',
						'target' => '_blank',
					),
				),
				'permanent_dismiss'     => true,
				'reopen_days'           => '1',
				'reopen_times'          => '1',
				'conditions_to_display' => array(
					array(
						'operator'    => 'AND',
						'show_notice' => $failed_count > 5 ? true : false,
					),
				),
			),
		);

		$notices = array_merge( $notices, $custom_notice );

		return $custom_notice;
	}
}

add_action( 'admin_init', 'user_registration_spam_users_detector' );

if ( ! function_exists( 'user_registration_spam_users_detector' ) ) {

	/**
	 * Count numbers of spams registered in previous hour.
	 */
	function user_registration_spam_users_detector() {
		global $wpdb;
		$activation_date   = get_option( 'user_registration_activated' );
		$current_timestamp = time();

		$spam_notice_dismissed = get_option( 'user_registration_info_ur_spam_users_detected_notice_dismissed_temporarily', false );
		$spam_notice_dismissed = ! $spam_notice_dismissed ? get_option( 'user_registration_ur_spam_users_detected_notice_dismissed', false ) : $spam_notice_dismissed;

		if ( $current_timestamp - strtotime( $activation_date ) > 86400 && ! $spam_notice_dismissed ) {
			$spam_count = get_transient( 'ur_spam_users_detected_count' );

			if ( ! $spam_count ) {

				$current_hour_time_gmt   = gmdate( 'Y-m-d H:i:s', $current_timestamp );
				$previous_hour_timestamp = $current_timestamp - 3600;
				$previous_hour_time_gmt  = gmdate( 'Y-m-d H:i:s', $previous_hour_timestamp );

				$results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT u.ID
						FROM {$wpdb->users} u
						LEFT JOIN {$wpdb->usermeta} um ON u.ID = um.user_id AND um.meta_key = 'ur_form_id'
						WHERE um.user_id IS NULL
						AND u.user_registered BETWEEN %s AND %s",
						$previous_hour_time_gmt,
						$current_hour_time_gmt
					)
				);

				$total_users = count( $results );
				set_transient( 'ur_spam_users_detected_count', $total_users, HOUR_IN_SECONDS );
			}
		}
	}
}

add_action( 'user_registration_custom_notices', 'ur_spam_users_detected' );

if ( ! function_exists( 'ur_spam_users_detected' ) ) {

	/**
	 * Display spam users registered notice if spam users count exceeds 20 in past hour.
	 *
	 * @param array $notices Custom Notices.
	 */
	function ur_spam_users_detected( $notices ) {
		$spam_users_count      = get_transient( 'ur_spam_users_detected_count' );
		$spam_notice_dismissed = get_option( 'user_registration_info_ur_spam_users_detected_notice_dismissed_temporarily', false );
		$spam_notice_dismissed = ! $spam_notice_dismissed ? get_option( 'user_registration_ur_spam_users_detected_notice_dismissed', false ) : $spam_notice_dismissed;

		if ( ! $spam_users_count || $spam_notice_dismissed ) {
			return $notices;
		}

		$custom_notice = array(
			array(
				'id'                    => 'ur_spam_users_detected',
				'type'                  => 'info',
				'status'                => 'active',
				'priority'              => '1',
				'title'                 => __( 'Unusual User Registrations Detected', 'user-registration' ),
				'message_content'       => wp_kses_post(
					sprintf(
						'<p>%s</p><p>%s</p><br/>',
						__( 'A significant number of users have registered on your site from sources other than the User Registration plugin\'s form.', 'user-registration' ),
						__( 'These registrations may be suspicious. Please review and disable any other methods that allow user registrations if they are not intended. Additionally, consider enabling spam protection measures in the User Registration plugin to safeguard your site.', 'user-registration' ),
					)
				),
				'buttons'               => array(
					array(
						'title'  => __( 'It was a false alarm', 'user-registration' ),
						'icon'   => 'dashicons-no-alt',
						'class'  => 'notice-dismiss notice-dismiss-permanently',
						'target' => '',
						'link'   => '',
					),
					array(
						'title'  => __( 'I have a query', 'user-registration' ),
						'icon'   => 'dashicons-testimonial',
						'link'   => 'https://wpuserregistration.com/support',
						'class'  => 'button-secondary notice-have-query',
						'target' => '_blank',
					),
					array(
						'title'  => __( 'Visit Documentation', 'user-registration' ),
						'icon'   => 'dashicons-media-document',
						'link'   => 'https://docs.wpuserregistration.com/docs/how-to-integrate-google-recaptcha/',
						'class'  => 'button-secondary',
						'target' => '_blank',
					),
				),
				'permanent_dismiss'     => true,
				'reopen_days'           => '1',
				'reopen_times'          => '1',
				'conditions_to_display' => array(
					array(
						'operator'    => 'AND',
						'show_notice' => $spam_users_count > 20 ? true : false,
					),
				),
			),
		);

		$notices = array_merge( $notices, $custom_notice );

		return $custom_notice;
	}
}

if ( ! function_exists( 'ur_non_deletable_fields' ) ) {
	/**
	 * user registration non deletable fields.
	 */
	function ur_non_deletable_fields() {
		return apply_filters(
			'user_registration_non_deletable_fields',
			array(
				'user_email',
				'user_pass',
			)
		);
	}
}

// TODO: Remove this code once Really Simple SSL plugin resolves the conflict from their side.
if ( ! function_exists( 'ur_rsssl_anyone_can_register_conflict_resolver' ) ) {

	/**
	 * Resolve anyone can register setting conflict with Really Simple SSL Plugin.
	 *
	 * @param bool $value Option value.
	 */
	function ur_rsssl_anyone_can_register_conflict_resolver( $value ) {
		global $wpdb;

		if ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'really-simple-ssl/rlrsssl-really-simple-ssl.php' ) ) {

			$rsssl_options = get_option( 'rsssl_options', '' );
			$rsssl_options = maybe_unserialize( $rsssl_options );

			if ( isset( $rsssl_options['disable_anyone_can_register'] ) && $rsssl_options['disable_anyone_can_register'] ) {
				$value = $wpdb->get_var( "SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = 'users_can_register';" ); // phpcs:ignore;

				if ( $value ) {
					return true;
				}
			}
		}

		return $value;
	}
}
add_filter( 'ur_register_setting_override', 'ur_rsssl_anyone_can_register_conflict_resolver', 10, 1 );

add_filter( 'user_registration_settings_prevent_default_login', 'ur_prevent_default_login' );
if ( ! function_exists( 'ur_prevent_default_login' ) ) {
	/**
	 * Handel error when default login screen is disabled but redirect login poage is not selected.
	 *
	 * @since 3.3.1
	 *
	 * @return @mixed
	 */
	function ur_prevent_default_login( $data ) {

		// Return if default wp_login is disabled and no redirect url is set.
		if ( isset( $data['user_registration_login_options_prevent_core_login'] ) && $data['user_registration_login_options_prevent_core_login'] ) {
			if ( isset( $data['user_registration_login_options_login_redirect_url'] ) ) {
				gettype( $data['user_registration_login_options_login_redirect_url'] );
				if ( ! $data['user_registration_login_options_login_redirect_url'] ) {
					return 'redirect_login_error';
				}
				if ( is_numeric( $data['user_registration_login_options_login_redirect_url'] ) ) {
					$is_page_my_account_page = ur_find_my_account_in_page( $data['user_registration_login_options_login_redirect_url'] );
					if ( ! $is_page_my_account_page ) {
						return 'redirect_login_not_myaccount';
					}
				}
			}
		} elseif ( isset( $data['user_registration_myaccount_page_id'] ) ) {
			if ( is_numeric( $data['user_registration_myaccount_page_id'] ) ) {
				$is_page_my_account_page = ur_find_my_account_in_page( $data['user_registration_myaccount_page_id'] );
				if ( ! $is_page_my_account_page ) {
					return 'redirect_login_not_myaccount';
				}
			}
		} elseif ( ur_check_module_activation( 'membership' ) && isset( $data['user_registration_member_registration_page_id'] ) && isset( $data['user_registration_thank_you_page_id'] ) ) {
			if ( is_numeric( $data['user_registration_thank_you_page_id'] ) && is_numeric( $data['user_registration_member_registration_page_id'] ) ) {
				$membership_service = new \WPEverest\URMembership\Admin\Services\MembershipService();
				$pages              = array(
					'user_registration_member_registration_page_id',
					'user_registration_thank_you_page_id'
				);
				$has_invalid_page   = false;
				foreach ( $pages as $k => $page ) {
					$response = $membership_service->verify_page_content( $page, $data[ $page ] );
					if ( ! $response['status'] ) {
						$has_invalid_page = true;
					}
				}

				if($has_invalid_page) {
                    return 'invalid_membership_pages';
                }
			}
		}
		elseif(isset($data['user_registration_membership_renewal_reminder_days_before'])) {
			if($data['user_registration_membership_renewal_reminder_days_before'] <= 0) {
				return 'invalid_renewal_period';
			}
		}
		return true;
	}
}

if ( ! function_exists( 'get_forms_for_wpbakery' ) ) {

	/**
	 * Get User Registration forms list for wpbakery.
	 */
	function get_forms_for_wpbakery() {
		$user_registration_forms = array();

		if ( empty( $user_registration_forms ) ) {
			$ur_forms = ur_get_all_user_registration_form();
			if ( ! empty( $ur_forms ) ) {

				foreach ( $ur_forms as $form_value => $form_name ) {
					$user_registration_forms[ $form_name ] = $form_value;
				}
			} else {
				$user_registration_forms[0] = esc_html__( 'You have not created a form, Please Create a form first', 'user-registration' );
			}

			return $user_registration_forms;
		}
	}
}

/**
 * Create WPBakery Widget for User Registration.
 */
add_action( 'vc_before_init', 'create_wpbakery_widget_category' );

/**
 * Create WPBakery Widgets for User Registration.
 *
 * @since 3.3.2
 */
function create_wpbakery_widget_category() {
	vc_map(
		array(
			'name'        => esc_html__( 'Registration Form', 'user-registration' ),
			'base'        => 'user_registration_form',
			'icon'        => 'icon-wpb-vc_user_registration',
			'category'    => esc_html__( 'User Registration', 'user-registration' ),
			'description' => esc_html__( 'Registration Form widget for WPBakery.', 'user-registration' ),
			'params'      => array(
				array(
					'type'        => 'dropdown',
					'heading'     => esc_html__( 'Form', 'user-registration' ),
					'param_name'  => 'id',
					'value'       => get_forms_for_wpbakery(),
					'description' => esc_html__( 'Select Form.', 'user-registration' ),
				),
			),
		)
	);
	vc_map(
		array(
			'name'        => esc_html__( 'My Account', 'user-registration' ),
			'base'        => 'user_registration_my_account',
			'icon'        => 'icon-wpb-vc_user_registration',
			'category'    => esc_html__( 'User Registration', 'user-registration' ),
			'description' => esc_html__( 'My Account widget for WPBakery.', 'user-registration' ),
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Redirect URL', 'user-registration' ),
					'param_name'  => 'redirect_url',
					'value'       => '',
					'description' => esc_html__( 'Enter redirect url after login.', 'user-registration' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Logout URL', 'user-registration' ),
					'param_name'  => 'logout_redirect',
					'value'       => '',
					'description' => esc_html__( 'Enter url which redirect after logout.', 'user-registration' ),
				),
			),
		),
	);
	vc_map(
		array(
			'name'        => esc_html__( 'Login Form', 'user-registration' ),
			'base'        => 'user_registration_login',
			'icon'        => 'icon-wpb-vc_user_registration',
			'category'    => esc_html__( 'User Registration', 'user-registration' ),
			'description' => esc_html__( 'Login Form widget for WPBakery.', 'user-registration' ),
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Redirect URL', 'user-registration' ),
					'param_name'  => 'redirect_url',
					'value'       => '',
					'description' => esc_html__( 'Enter redirect url after login.', 'user-registration' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Logout URL', 'user-registration' ),
					'param_name'  => 'logout_redirect',
					'value'       => '',
					'description' => esc_html__( 'Enter url which redirect after logout.', 'user-registration' ),
				),
			),
		),
	);
	vc_map(
		array(
			'name'        => esc_html__( 'Edit Profile', 'user-registration' ),
			'base'        => 'user_registration_edit_profile',
			'icon'        => 'icon-wpb-vc_user_registration',
			'category'    => esc_html__( 'User Registration', 'user-registration' ),
			'description' => esc_html__( 'Edit Profile widget for WPBakery.', 'user-registration' ),
		),
	);
	vc_map(
		array(
			'name'        => esc_html__( 'Edit Password', 'user-registration' ),
			'base'        => 'user_registration_edit_password',
			'icon'        => 'icon-wpb-vc_user_registration',
			'category'    => esc_html__( 'User Registration', 'user-registration' ),
			'description' => esc_html__( 'Edit Password widget for WPBakery.', 'user-registration' ),
		),
	);

	/**
	 * Hook to add more wpbakery widget for user registration.
	 */
	do_action( 'user_registration_add_wpbakery_widget' );
}
if ( ! function_exists( 'ur_integration_addons' ) ) {
	/**
	 * List of integrations.
	 *
	 * @since 3.3.1
	 *
	 * @return array
	 */
	function ur_integration_addons() {

		$integration_list = array(
			'UR_Settings_SMS_Integration' => array(
				'id'           => 'sms_integration',
				'type'         => 'accordian',
				'title'        => esc_html__( 'Twilio', 'user-registration' ),
				'video_id'     => '-iUMcr03FP8',
				'available_in' => 'Personal Plan',
				'activated'    => ur_check_module_activation( 'sms-integration' ),
				'display'      => array( 'settings' ),
				'connected'    => ! empty( get_option( 'ur_sms_integration_accounts', array() ) ) ? true : false,
				'plugin_name' => esc_html__( 'Twilio', 'user-registration' ),
			),
			$integration['UR_Settings_ActiveCampaign'] = array(
				'id'           => 'activecampaign',
				'type'         => 'accordian',
				'title'        => esc_html__( 'ActiveCampaign', 'user-registration' ),
				'desc'         => '',
				'video_id'     => 'AfapJxM9klk',
				'available_in' => 'Themegrill Agency Plan or Professional Plan or Plus Plan',
				'activated'    => is_plugin_active( 'user-registration-activecampaign/user-registration-activecampaign.php' ),
				'display'      => array( 'settings', 'form_settings' ),
				'connected'    => ! empty( get_option( 'ur_activecampaign_accounts', array() ) ) ? true : false,
				'plugin_name' => esc_html__( 'User Registration ActiveCampaign', 'user-registration' ),
			),
			$integration['UR_Settings_MailerLite'] = array(
				'id'           => 'mailerlite',
				'type'         => 'accordian',
				'title'        => esc_html__( 'MailerLite', 'user-registration' ),
				'desc'         => '',
				'video_id'     => '4f1lGgFuJx4',
				'available_in' => 'Themegrill Agency Plan or Professional Plan or Plus Plan',
				'activated'    => is_plugin_active( 'user-registration-mailerlite/user-registration-mailerlite.php' ),
				'display'      => array( 'settings', 'form_settings' ),
				'connected'    => ! empty( get_option( 'ur_mailerlite_accounts', array() ) ) ? true : false,
				'plugin_name' => esc_html__( 'User Registration MailerLite', 'user-registration' ),
			),
			$integration['UR_Settings_klaviyo'] = array(
				'id'           => 'klaviyo',
				'type'         => 'accordian',
				'title'        => esc_html__( 'Klaviyo', 'user-registration' ),
				'desc'         => '',
				'video_id'     => 'nKOMqrkNK3Y',
				'available_in' => 'Themegrill Agency Plan or Professional Plan or Plus Plan',
				'activated'    => is_plugin_active( 'user-registration-klaviyo/user-registration-klaviyo.php' ),
				'display'      => array( 'settings', 'form_settings' ),
				'connected'    => ! empty( get_option( 'ur_klaviyo_accounts', array() ) ) ? true : false,
				'plugin_name' => esc_html__( 'User Registration Klaviyo', 'user-registration' ),
			),
			$integration['UR_Settings_Mailchimp'] = array(
				'id'           => 'mailchimp',
				'type'         => 'accordian',
				'title'        => esc_html__( 'Mailchimp', 'user-registration' ),
				'desc'         => '',
				'video_id'     => 'iyCByez_7U8',
				'available_in' => 'Personal Plan',
				'activated'    => is_plugin_active( 'user-registration-mailchimp/user-registration-mailchimp.php' ),
				'display'      => array( 'settings', 'form_settings' ),
				'connected'    => ! empty( get_option( 'ur_mailchimp_accounts', array() ) ) ? true : false,
				'plugin_name' => esc_html__( 'User Registration - Mailchimp', 'user-registration' ),
			),
			'User_Registration_Zapier'    => array(
				'id'           => 'zapier',
				'type'         => 'accordian',
				'title'        => esc_html__( 'Zapier', 'user-registration' ),
				'desc'         => '',
				'video_id'     => 'zxl2nsXyOmw',
				'available_in' => 'Themegrill Agency Plan or Professional Plan or Plus Plan',
				'activated'    => is_plugin_active( 'user-registration-zapier/user-registration-zapier.php' ),
				'display'      => array( 'form_settings' ),
				'connected'    => ! empty( get_option( 'ur_zapier_accounts', array() ) ) ? true : false,
				'plugin_name' => esc_html__( 'User Registration Zapier', 'user-registration' ),
			),
			'WPEverest\URMailPoet'        => array(
				'id'           => 'mailpoet',
				'type'         => 'accordian',
				'title'        => esc_html__( 'MailPoet', 'user-registration' ),
				'desc'         => '',
				'video_id'     => '4uFlZoXlye4',
				'available_in' => 'Themegrill Agency Plan or Professional Plan or Plus Plan',
				'activated'    => is_plugin_active( 'user-registration-mailpoet/user-registration-mailpoet.php' ),
				'display'      => array( 'settings', 'form_settings' ),
				'connected'    => ur_string_to_bool( get_option( 'user_registration_integrations_mailpoet_connection', false ) ),
				'plugin_name' => esc_html__( 'User Registration MailPoet', 'user-registration' ),
			),
			'WPEverest\URConvertKit'      => array(
				'id'           => 'convertkit',
				'type'         => 'accordian',
				'title'        => esc_html__( 'ConvertKit', 'user-registration' ),
				'desc'         => '',
				'video_id'     => '',
				'available_in' => 'Themegrill Agency Plan or Professional Plan or Plus Plan',
				'activated'    => is_plugin_active( 'user-registration-convertkit/user-registration-convertkit.php' ),
				'display'      => array( 'settings', 'form_settings' ),
				'connected'    => is_plugin_active( 'user-registration-convertkit/user-registration-convertkit.php' ) && ! empty( get_option( 'ur_convertkit_accounts', array() ) ) ? true : false,
				'plugin_name' => esc_html__( 'User Registration ConvertKit', 'user-registration' ),
			),
			'User_Registration_Brevo'     => array(
				'id'           => 'brevo',
				'type'         => 'accordian',
				'title'        => esc_html__( 'Brevo', 'user-registration' ),
				'desc'         => '',
				'video_id'     => '',
				'available_in' => 'Themegrill Agency Plan or Professional Plan or Plus Plan',
				'activated'    => is_plugin_active( 'user-registration-brevo/user-registration-brevo.php' ),
				'display'      => array( 'settings', 'form_settings' ),
				'connected'    => is_plugin_active( 'user-registration-brevo/user-registration-brevo.php' ) && ur_string_to_bool( get_option( 'user_registration_integrations_brevo_connection', false ) ),
				'plugin_name' => esc_html__( 'User Registration Brevo', 'user-registration' ),
			),
			'User_Registration_Salesforce' => array(
				'id'           => 'salesforce',
				'type'         => 'accordian',
				'title'        => esc_html__( 'Salesforce', 'user-registration' ),
				'desc'         => '',
				'video_id'     => '',
				'available_in' => 'Themegrill Agency Plan or Professional Plan or Plus Plan',
				'activated'    => is_plugin_active( 'user-registration-salesforce/user-registration-salesforce.php' ),
				'display'      => array( 'settings', 'form_settings' ),
			    'connected'    => is_plugin_active( 'user-registration-salesforce/user-registration-salesforce.php' ) && ! empty( get_option( 'ur_salesforce_accounts', array() ) ) ? true : false,
				'plugin_name' => esc_html__( 'User Registration Salesforce', 'user-registration' ),
			),
		);

		usort(
			$integration_list,
			function ( $a, $b ) {
			return $b['activated'] <=> $a['activated']; //phpcs:ignore;
			}
		);

		usort(
			$integration_list,
			function ( $a, $b ) {
			return $b['connected'] <=> $a['connected']; //phpcs:ignore;
			}
		);

		return $integration_list;
	}

}
if ( ! function_exists( 'ur_list_top_integrations' ) ) {
	/**
	 * List top integrations.
	 *
	 * @since 3.3.1
	 *
	 * @param array $integrations Integrations.
	 * @return array
	 */
	function ur_list_top_integrations( $integrations ) {
		$is_free = is_plugin_active( 'user-registration/user-registration.php' );
		if ( $is_free ) {
			$integration_addons = ur_integration_addons();
			foreach ( $integration_addons as $key => $addon ) {
				if ( isset( $addon['display'] ) && ! in_array( 'settings', $addon['display'] ) ) {
					continue;
				}

				$integration[ $key ] = $addon;
			}
			return $integration;
		}
	}
}
add_filter( 'user_registration_integrations_classes', 'ur_list_top_integrations' );

if ( ! function_exists( 'ur_get_captcha_integrations' ) ) {
	/**
	 * List top captchas.
	 *
	 * @since 3.3.4
	 *
	 * @return array
	 */
	function ur_get_captcha_integrations() {
		return apply_filters(
			'user_registration_captcha_integrations',
			array(
				'v2'         => 'reCaptcha v2',
				'v3'         => 'reCaptcha v3',
				'hCaptcha'   => 'hCaptcha',
				'cloudflare' => 'Cloudflare Turnstile',
			)
		);
	}
}

add_action(
	'user_registration_form_shortcode_scripts',
	function ( $atts ) {

		$form_id        = isset( $atts['id'] ) ? $atts['id'] : 0;
		$recaptcha_type = ur_get_single_post_meta( $form_id, 'user_registration_form_setting_configured_captcha_type', 'v2' );

		add_filter(
			'user_registration_params',
			function ( $data ) use ( $recaptcha_type ) {
				$data['recaptcha_type'] = $recaptcha_type;
				return $data;
			}
		);
	},
	10,
	1
);


add_action( 'user_registration_init', 'ur_captcha_settings_migration_script' );

if ( ! function_exists( 'ur_captcha_settings_migration_script' ) ) {

	/**
	 * Update Captcha Settings for all forms and global settings.
	 *
	 * @since 3.3.4.
	 */
	function ur_captcha_settings_migration_script() {

		if ( ! get_option( 'ur_captcha_settings_migrated', false ) ) {

			$all_forms              = ur_get_all_user_registration_form();
			$enabled_recaptcha_type = get_option( 'user_registration_captcha_setting_recaptcha_version', 'v2' );

			foreach ( $all_forms as $key => $value ) {

				$form_id = $key;

				$form_captcha_enabled = ur_get_single_post_meta( $form_id, 'user_registration_form_setting_enable_recaptcha_support', false );
				if ( $form_captcha_enabled ) {
					update_post_meta( $form_id, 'user_registration_form_setting_configured_captcha_type', $enabled_recaptcha_type );
				}
			}

			if ( get_option( 'user_registration_login_options_enable_recaptcha', false ) ) {
				update_option( 'user_registration_login_options_configured_captcha_type', $enabled_recaptcha_type );
			}
			update_option( 'user_registration_captcha_setting_recaptcha_enable_' . $enabled_recaptcha_type, true );

			update_option( 'ur_captcha_settings_migrated', true );
		}
	}
}

// Hook the end setup wizard to admin_init
add_action(
	'admin_init',
	'ur_end_setup_wizard'
);

if ( ! function_exists( 'ur_end_setup_wizard' ) ) {
	/**
	 * End to setup wizard.
	 */
	function ur_end_setup_wizard() {
		// End setup wizard when skipped to list table.
		if ( ! empty( $_REQUEST['end-setup-wizard'] ) && sanitize_text_field( wp_unslash( $_REQUEST['end-setup-wizard'] ) ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			update_option( 'user_registration_first_time_activation_flag', false );
			update_option( 'user_registration_onboarding_skipped', true );

			if ( isset( $_REQUEST['activeStep'] ) ) {
				update_option( 'user_registration_onboarding_skipped_step', sanitize_text_field( wp_unslash( $_REQUEST['activeStep'] ) ) );
			} else {
				delete_option( 'user_registration_onboarding_skipped_step' );
				update_option( 'user_registration_onboarding_skipped', false );
			}
		}
	}
}

if ( ! function_exists( 'ur_get_exclude_text_format_settings' ) ) {
	function ur_get_exclude_text_format_settings() {
		$settings = array(
			'user_registration_form_setting_enable_recaptcha_support',
		);

		return $settings;
	}
}
if ( ! function_exists( 'ur_check_url_is_image' ) ) {

	/**
	 * ur_check_is_image
	 *
	 * @param string $url
	 *
	 * @return bool
	 */
	function ur_check_url_is_image( $url ) {
		$ch       = curl_init();
		$headers  = array(
			'Accept: application/json',
			'Content-Type: application/json',

		);
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false); //used for sites that have ssl disabled

		curl_setopt( $ch, CURLOPT_NOBODY, true );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HEADER, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );

		$response = curl_exec( $ch );

		if ( false === $response ) {
			curl_close( $ch );
			return false;
		}

		$contentType = curl_getinfo( $ch, CURLINFO_CONTENT_TYPE );
		return str_contains( $contentType, 'image/' );
	}


}

if ( ! function_exists( 'ur_get_user_registered_source' ) ) {
	/**
	 * Returns the user registered source/form name.
	 *
	 * @param [int] $user_id User Id.
	 *
	 * @since 4.1
	 *
	 * @return string
	 */
	function ur_get_user_registered_source( $user_id ) {
		$user_metas = get_user_meta( $user_id );
		if ( isset( $user_metas['user_registration_social_connect_bypass_current_password'] ) ) {
			$networks = array( 'facebook', 'linkedin', 'google', 'twitter' );

			foreach ( $networks as $network ) {

				if ( isset( $user_metas[ 'user_registration_social_connect_' . $network . '_username' ] ) ) {
					return ucfirst( $network );
				}
			}
		} elseif ( isset( $user_metas['ur_form_id'] ) ) {
			$form_post = get_post( $user_metas['ur_form_id'][0] );

			if ( ! empty( $form_post ) ) {
				return $form_post->post_title;
			} else {
				return '-';
			}
		}

		return '';
	}
}

if ( ! function_exists( 'ur_return_social_profile_pic' ) ) {

	/**
	 * ur_return_social_profile_pic
	 *
	 * @param $url
	 * @param $user_id
	 *
	 * @return mixed
	 */
	function ur_return_social_profile_pic( $url, $user_id ) {
		$source = ur_get_user_registered_source( $user_id );

		$user_meta = get_user_meta( $user_id, 'user_registration_social_connect_' . strtolower( $source ) . '_profile_pic', true );

		if ( ! empty( $user_meta ) && ur_check_url_is_image( $user_meta ) ) {
			return $user_meta;
		}

		return $url;
	}
}

add_filter( 'user_registration_profile_picture_url',  'ur_return_social_profile_pic' , 10, 2 );

if ( ! function_exists( 'get_login_options_settings' ) ) {
	/**
	 * Get settings for login form
	 *
	 * @return array
	 */
	function get_login_options_settings() {

		$ur_captchas = ur_get_captcha_integrations();
		$ur_enabled_captchas = array(
			'' => __( "Select Enabled Captcha", 'user-registration' )
		);

		foreach ( $ur_captchas as $key => $value ) {
			if ( get_option( 'user_registration_captcha_setting_recaptcha_enable_' . $key, false ) ) {
				$ur_enabled_captchas[ $key ] = $value;
			}
		}
		/**
		 * Filter to add the login options settings.
		 *
		 * @param array Options to be enlisted.
		 */
		$settings = apply_filters(
			'user_registration_login_options_settings',
			array(
				'title'    => '',
				'sections' => array(
					'login_options_settings'           => array(
						'title'    => __( 'General', 'user-registration' ),
						'type'     => 'card',
						'desc'     => '',
						'settings' => array(
							array(
								'title'    => __( 'Form Template', 'user-registration' ),
								'desc'     => __( 'Choose the login form template.', 'user-registration' ),
								'id'       => 'user_registration_login_options_form_template',
								'type'     => 'select',
								'desc_tip' => true,
								'css'      => 'min-width: 350px;',
								'class'    => 'ur-enhanced-select',
								'default'  => 'default',
								'options'  => array(
									'default'      => __( 'Default', 'user-registration' ),
									'bordered'     => __( 'Bordered', 'user-registration' ),
									'flat'         => __( 'Flat', 'user-registration' ),
									'rounded'      => __( 'Rounded', 'user-registration' ),
									'rounded_edge' => __( 'Rounded Edge', 'user-registration' ),
								),
							),
							array(
								'title'    => __( 'Allow Users to Login With', 'user-registration' ),
								'desc'     => __( 'Allow users to login with Username, Email or both.', 'user-registration' ),
								'id'       => 'user_registration_general_setting_login_options_with',
								'default'  => 'default',
								'type'     => 'select',
								'class'    => 'ur-enhanced-select',
								'css'      => 'min-width: 350px;',
								'desc_tip' => true,
								'options'  => ur_login_option_with(),
							),
							array(
								'title'    => __( 'Enable Login Title', 'user-registration' ),
								'desc'     => '',
								'id'       => 'user_registration_login_title',
								'type'     => 'toggle',
								'desc_tip' => __( 'Check to enable login title in login form.', 'user-registration' ),
								'css'      => 'min-width: 350px;',
								'default'  => 'no',
							),
							array(
								'title'    => __( 'Login Form Title', 'user-registration' ),
								'desc'     => __( 'This text will appear as the login form title', 'user-registration' ),
								'id'       => 'user_registration_general_setting_login_form_title',
								'type'     => 'text',
								'desc_tip' => true,
								'css'      => 'min-width: 350px;',
								'default' => __( 'Welcome', 'user-registration' ),
							),
							array(
								'title'    => __( 'Login Form Description', 'user-registration' ),
								'desc'     => __( 'This text will appear as the login form description', 'user-registration' ),
								'id'       => 'user_registration_general_setting_login_form_desc',
								'type'     => 'text',
								'desc_tip' => true,
								'css'      => 'min-width: 350px;',
								'default' => __( 'Please enter your details to access your account.', 'user-registration' ),
							),
							array(
								'title'    => __( 'Enable Ajax Login', 'user-registration' ),
								'desc'     => '',
								'id'       => 'ur_login_ajax_submission',
								'type'     => 'toggle',
								'desc_tip' => __( 'Check to enable Ajax login i.e login without page reload on submission.', 'user-registration' ),
								'css'      => 'min-width: 350px;',
								'default'  => 'no',
							),
							array(
								'title'    => __( 'Enable Remember Me', 'user-registration' ),
								'desc'     => '',
								'id'       => 'user_registration_login_options_remember_me',
								'type'     => 'toggle',
								'desc_tip' => __( 'Check to enable/disable Remember Me.', 'user-registration' ),
								'css'      => 'min-width: 350px;',
								'default'  => 'yes',
							),

							array(
								'title'    => __( 'Enable Lost Password', 'user-registration' ),
								'desc'     => '',
								'id'       => 'user_registration_login_options_lost_password',
								'type'     => 'toggle',
								'desc_tip' => __( 'Check to enable/disable lost password.', 'user-registration' ),
								'css'      => 'min-width: 350px;',
								'default'  => 'yes',
							),
							array(
								'title'    => __( 'Lost Password Page', 'user-registration' ),
								'desc'     => sprintf( __( 'Select the page which contains your login form: [%s]', 'user-registration' ), apply_filters( 'user_registration_lost_password_shortcode_tag', 'user_registration_lost_password' ) ), //phpcs:ignore
								'id'       => 'user_registration_lost_password_page_id',
								'type'     => 'single_select_page',
								'default'  => '',
								'class'    => 'ur-enhanced-select-nostd',
								'css'      => 'min-width:350px;',
								'desc_tip' => true,
							),
							array(
								'title'    => __( 'Hide Field Labels', 'user-registration' ),
								'desc'     => '',
								'id'       => 'user_registration_login_options_hide_labels',
								'type'     => 'toggle',
								'desc_tip' => __( 'Check to hide field labels.', 'user-registration' ),
								'css'      => 'min-width: 350px;',
								'default'  => 'no',
							),

							array(
								'title'    => __( 'Enable Captcha', 'user-registration' ),
								'desc'     => '',
								'id'       => 'user_registration_login_options_enable_recaptcha',
								'type'     => 'toggle',
								'desc_tip' => sprintf( __( 'Enable %1$s %2$s Captcha %3$s support', 'user-registration' ), '<a title="', 'Please make sure the site key and secret are not empty in setting page." href="' . admin_url() . 'admin.php?page=user-registration-settings&tab=captcha" rel="noreferrer noopener" target="_blank" style="color: #9ef01a;text-decoration:none;">', '</a>' ), //phpcs:ignore
								'css'      => 'min-width: 350px;',
								'default'  => 'no',
							),
							array(
								'title'    => __( 'Select Configured Captcha', 'user-registration' ),
								'desc'     => __( 'Choose the captcha type for Login Form.', 'user-registration' ),
								'id'       => 'user_registration_login_options_configured_captcha_type',
								'type'     => 'select',
								'desc_tip' => true,
								'css'      => 'min-width: 350px;',
								'default'  => 'default',
								'options'  => $ur_enabled_captchas,
							),
							array(
								'title'    => __( 'Registration URL', 'user-registration' ),
								'desc'     => __( 'This option lets you display the registration page URL in the login form.', 'user-registration' ),
								'id'       => 'user_registration_general_setting_registration_url_options',
								'type'     => 'text',
								'desc_tip' => true,
								'css'      => 'min-width: 350px;',
							),

							array(
								'title'    => __( 'Registration URL Label', 'user-registration' ),
								'desc'     => __( 'This option lets you enter the label to registration url in login form.', 'user-registration' ),
								'id'       => 'user_registration_general_setting_registration_label',
								'type'     => 'text',
								'desc_tip' => true,
								'css'      => 'min-width: 350px;',
								'default'  => __( 'Not a member yet? Register now.', 'user-registration' ),
							),

							array(
								'title'      => __( 'Disable Default WordPress Login Screen', 'user-registration' ),
								'desc'       => '',
								'id'         => 'user_registration_login_options_prevent_core_login',
								'type'       => 'toggle',
								'desc_tip'   => __( 'Default WordPress login page wp-login.php will  be disabled.', 'user-registration' ),
								'css'        => 'min-width: 350px;',
								'default'    => 'no',
								'desc_field' => __( 'Please make sure that you have created a login or my-account page which has a login form before enabling this option. Learn how to create a login form <a href="https://docs.wpuserregistration.com/docs/how-to-show-login-form/" rel="noreferrer noopener" target="_blank">here</a>.', 'user-registration' ),
							),

							array(
								'title'    => __( 'Redirect Default WordPress Login To', 'user-registration' ),
								'desc'     => __( 'Select the login page where you want to redirect the wp-admin or wp-login.php page.', 'user-registration' ),
								'id'       => 'user_registration_login_options_login_redirect_url',
								'type'     => 'single_select_page',
								'desc_tip' => true,
								'css'      => 'min-width: 350px;',
								'class'    => 'ur-redirect-to-login-page ur-enhanced-select-nostd',
								'default'  => get_option( 'user_registration_myaccount_page_id', '' ),
							),
						),
					),
					'login_form_labels_settings'       => array(
						'title'    => __( 'Labels', 'user-registration' ),
						'type'     => 'card',
						'desc'     => '',
						'settings' => array(
							array(
								'title'    => __( 'Username or Email', 'user-registration' ),
								'desc'     => __( 'This option lets you edit the "Username or Email" field label.', 'user-registration' ),
								'id'       => 'user_registration_label_username_or_email',
								'type'     => 'text',
								'desc_tip' => true,
								'css'      => 'min-width: 350px;',
								'default'  => __( 'Username or Email', 'user-registration' ),
							),

							array(
								'title'    => __( 'Password', 'user-registration' ),
								'desc'     => __( 'This option lets you edit the "Password" field label.', 'user-registration' ),
								'id'       => 'user_registration_label_password',
								'type'     => 'text',
								'desc_tip' => true,
								'css'      => 'min-width: 350px;',
								'default'  => __( 'Password', 'user-registration' ),
							),

							array(
								'title'    => __( 'Remember Me', 'user-registration' ),
								'desc'     => __( 'This option lets you edit the "Remember Me" option label.', 'user-registration' ),
								'id'       => 'user_registration_label_remember_me',
								'type'     => 'text',
								'desc_tip' => true,
								'css'      => 'min-width: 350px;',
								'default'  => __( 'Remember Me', 'user-registration' ),
							),

							array(
								'title'    => __( 'Login', 'user-registration' ),
								'desc'     => __( 'This option lets you edit the "Login" button label.', 'user-registration' ),
								'id'       => 'user_registration_label_login',
								'type'     => 'text',
								'desc_tip' => true,
								'css'      => 'min-width: 350px;',
								'default'  => __( 'Login', 'user-registration' ),
							),

							array(
								'title'    => __( 'Lost Your Password?', 'user-registration' ),
								'desc'     => __( 'This option lets you edit the "Lost your password?" option label.', 'user-registration' ),
								'id'       => 'user_registration_label_lost_your_password',
								'type'     => 'text',
								'desc_tip' => true,
								'css'      => 'min-width: 350px;',
								'default'  => __( 'Lost your password?', 'user-registration' ),
							),
						),
					),
					'login_form_placeholders_settings' => array(
						'title'    => __( 'Placeholders', 'user-registration' ),
						'type'     => 'card',
						'desc'     => '',
						'settings' => array(
							array(
								'title'    => __( 'Username or Email Field', 'user-registration' ),
								'desc'     => __( 'This option lets you set placeholder for the "Username or Email" field.', 'user-registration' ),
								'id'       => 'user_registration_placeholder_username_or_email',
								'type'     => 'text',
								'desc_tip' => true,
								'css'      => 'min-width: 350px;',
								'default'  => '',
							),

							array(
								'title'    => __( 'Password Field', 'user-registration' ),
								'desc'     => __( 'This option lets you set placeholder for the "Password" field.', 'user-registration' ),
								'id'       => 'user_registration_placeholder_password',
								'type'     => 'text',
								'desc_tip' => true,
								'css'      => 'min-width: 350px;',
								'default'  => '',
							),
						),
					),
					'login_form_messages_settings'     => array(
						'title'    => __( 'Messages', 'user-registration' ),
						'type'     => 'card',
						'desc'     => '',
						'settings' => array(
							array(
								'title'    => __( 'Username Required', 'user-registration' ),
								'desc'     => __( 'Show this message when username is empty.', 'user-registration' ),
								'id'       => 'user_registration_message_username_required',
								'type'     => 'text',
								'desc_tip' => true,
								'css'      => 'min-width: 350px;',
								'default'  => 'Username is required.',
							),

							array(
								'title'       => __( 'Empty Password', 'user-registration' ),
								'desc'        => __( 'Show this message when password is empty.', 'user-registration' ),
								'id'          => 'user_registration_message_empty_password',
								'type'        => 'text',
								'desc_tip'    => true,
								'css'         => 'min-width: 350px;',
								'default'     => '',
								'placeholder' => 'Default message from WordPress',
							),

							array(
								'title'       => __( 'Invalid/Unknown Username', 'user-registration' ),
								'desc'        => __( 'Show this message when username is unknown or invalid.', 'user-registration' ),
								'id'          => 'user_registration_message_invalid_username',
								'type'        => 'text',
								'desc_tip'    => true,
								'css'         => 'min-width: 350px;',
								'default'     => '',
								'placeholder' => 'Default message from WordPress',
							),

							array(
								'title'    => __( 'Unknown Email', 'user-registration' ),
								'desc'     => __( 'Show this message when email is unknown.', 'user-registration' ),
								'id'       => 'user_registration_message_unknown_email',
								'type'     => 'text',
								'desc_tip' => true,
								'css'      => 'min-width: 350px;',
								'default'  => 'A user could not be found with this email address.',
							),

							array(
								'title'       => __( 'Pending Approval', 'user-registration' ),
								'desc'        => __( 'Show this message when an account is pending approval.', 'user-registration' ),
								'id'          => 'user_registration_message_pending_approval',
								'type'        => 'text',
								'desc_tip'    => true,
								'css'         => 'min-width: 350px;',
								'default'     => '',
								'placeholder' => 'Default message from WordPress',
							),

							array(
								'title'       => __( 'Denied Account', 'user-registration' ),
								'desc'        => __( 'Show this message when an account is denied.', 'user-registration' ),
								'id'          => 'user_registration_message_denied_account',
								'type'        => 'text',
								'desc_tip'    => true,
								'css'         => 'min-width: 350px;',
								'default'     => '',
								'placeholder' => 'Default message from WordPress',
							),
						),
					),
				),
			)
		);

		return $settings;
	}
}

if ( ! function_exists( 'render_login_option_settings' ) ) {

	function render_login_option_settings( $section ) {
		$settings = '';
		foreach ( $section['settings'] as $key => $value ) {

			if ( ! isset( $value['type'] ) ) {
				continue;
			}

			if ( ! isset( $value['id'] ) ) {
				$value['id'] = '';
			}
			if ( ! isset( $value['row_class'] ) ) {
				$value['row_class'] = '';
			}
			if ( ! isset( $value['rows'] ) ) {
				$value['rows'] = '';
			}
			if ( ! isset( $value['cols'] ) ) {
				$value['cols'] = '';
			}
			if ( ! isset( $value['title'] ) ) {
				$value['title'] = isset( $value['name'] ) ? $value['name'] : '';
			}
			if ( ! isset( $value['class'] ) ) {
				$value['class'] = '';
			}
			if ( ! isset( $value['css'] ) ) {
				$value['css'] = '';
			}
			if ( ! isset( $value['default'] ) ) {
				$value['default'] = '';
			}
			if ( ! isset( $value['desc'] ) ) {
				$value['desc'] = '';
			}
			if ( ! isset( $value['desc_tip'] ) ) {
				$value['desc_tip'] = false;
			}
			if ( ! isset( $value['desc_field'] ) ) {
				$value['desc_field'] = false;
			}
			if ( ! isset( $value['placeholder'] ) ) {
				$value['placeholder'] = '';
			}

			// Capitalize Setting Label.
			$value['title'] = UR_Admin_Settings::capitalize_title( $value['title'] );

			// Custom attribute handling.
			$custom_attributes = array();

			if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) ) {
				foreach ( $value['custom_attributes'] as $attribute => $attribute_value ) {
					$custom_attributes[] = esc_attr( $attribute ) . '=' . esc_attr( $attribute_value ) . '';
				}
			}

			$field_description = UR_Admin_Settings::get_field_description( $value );
			extract( $field_description );

			// Switch based on type.
			switch ( $value['type'] ) {

				// Standard text inputs and subtypes like 'number'.
				case 'text':
				case 'email':
				case 'number':
				case 'password':
				case 'date':
					$option_value = UR_Admin_Settings::get_option( $value['id'], $value['default'] );

					$settings .= '<div class="user-registration-login-form-global-settings">';
					$settings .= '<label class="ur-label" for="' . esc_attr( $value['id'] ) . '">' . esc_html( $value['title'] ) . ' ' . wp_kses_post( $tooltip_html ) . '</label>';
					$settings .= '<div class="user-registration-login-form-global-settings--field">';
					$settings .= '<input
							name="' . esc_attr( $value['id'] ) . '"
							id="' . esc_attr( $value['id'] ) . '"
							type="' . esc_attr( $value['type'] ) . '"
							style="' . esc_attr( $value['css'] ) . '"
							value="' . esc_attr( $option_value ) . '"
							class="' . esc_attr( $value['class'] ) . '"
							placeholder="' . esc_attr( $value['placeholder'] ) . '"
							' . esc_attr( implode( ' ', $custom_attributes ) ) . ' ' . wp_kses_post( $description ) . '/>';
					$settings .= '</div>';
					$settings .= '</div>';
					break;
				case 'nonce':
					$settings .= '<div class="user-registration-login-form-global-settings">';
					$settings .= '<div class="user-registration-login-form-global-settings--field">';
					$settings .= '<input
							name="' . esc_attr( $value['id'] ) . '"
							id="' . esc_attr( $value['id'] ) . '"
							type="hidden"
							value="' . esc_attr( wp_create_nonce( $value['action'] ) ) . '"
							/>';
					$settings .= '</div>';
					$settings .= '</div>';
					break;

				// Color picker.
				case 'color':
					$option_value = UR_Admin_Settings::get_option( $value['id'], $value['default'] );
					$settings    .= '<div class="user-registration-login-form-global-settings">';
					$settings    .= '<label for="' . esc_attr( $value['id'] ) . '">' . esc_html( $value['title'] ) . ' ' . wp_kses_post( $tooltip_html ) . '</label>';
					$settings    .= '<div class="user-registration-login-form-global-settings--field">';
					$settings    .= '<input
							name="' . esc_attr( $value['id'] ) . '"
							id="' . esc_attr( $value['id'] ) . '"
							type="text"
							dir="ltr"
							style="' . esc_attr( $value['css'] ) . '"
							value="' . esc_attr( $option_value ) . '"
							class="' . esc_attr( $value['class'] ) . 'colorpick"
							placeholder="' . esc_attr( $value['placeholder'] ) . '"
							' . esc_attr( implode( ' ', $custom_attributes ) ) . '/>&lrm;' . wp_kses_post( $description );
					$settings    .= '<div id="colorPickerDiv_' . esc_attr( $value['id'] ) . '" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div></div>';
					$settings    .= '</div>';
					break;

				// Textarea.
				case 'textarea':
					$option_value = UR_Admin_Settings::get_option( $value['id'], $value['default'] );

					$settings .= '<div class="user-registration-login-form-global-settings">';
					$settings .= '<label for="' . esc_attr( $value['id'] ) . '">' . esc_html( $value['title'] ) . ' ' . wp_kses_post( $tooltip_html ) . '</label>';
					$settings .= '<div class="user-registration-login-form-global-settings--field">';
					$settings .= wp_kses_post( $description );
					$settings .= '<textarea
							name="' . esc_attr( $value['id'] ) . '"
							id="' . esc_attr( $value['id'] ) . '"
							style="' . esc_attr( $value['css'] ) . '"
							class="' . esc_attr( $value['class'] ) . '"
							rows="' . esc_attr( $value['rows'] ) . '"
							cols="' . esc_attr( $value['cols'] ) . '"
							placeholder="' . esc_attr( $value['placeholder'] ) . '"
							' . esc_html( implode( ' ', $custom_attributes ) ) . '>'
							. esc_textarea( $option_value ) . '</textarea>';
					$settings .= '</div>';
					$settings .= '</div>';
					break;

				// Select boxes.
				case 'select':
				case 'multiselect':
					$option_value = UR_Admin_Settings::get_option( $value['id'], $value['default'] );

					$settings .= '<div class="user-registration-login-form-global-settings">';
					$settings .= '<label for="' . esc_attr( $value['id'] ) . '">' . esc_html( $value['title'] ) . ' ' . wp_kses_post( $tooltip_html ) . '</label>';
					$settings .= '<div class="user-registration-login-form-global-settings--field">';
					$multiple  = '';
					$type      = '';
					if ( 'multiselect' == $value['type'] ) {
						$type     = '[]';
						$multiple = 'multiple="multiple"';
					}

					$settings .= '<select
							name="' . esc_attr( $value['id'] ) . '' . $type . '"
							id="' . esc_attr( $value['id'] ) . '"
							style="' . esc_attr( $value['css'] ) . '"
							class="' . esc_attr( $value['class'] ) . '"
							' . esc_attr( implode( ' ', $custom_attributes ) ) . '
							' . esc_attr( $multiple ) . '>';

					foreach ( $value['options'] as $key => $val ) {
						$selected = '';

						if ( is_array( $option_value ) ) {
							$selected = selected( in_array( $key, $option_value ), true, false );
						} else {
							$selected = selected( $option_value, $key, false );
						}

						$settings .= '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . '>';
						$settings .= esc_html( $val );
						$settings .= '</option>';
					}

					$settings .= '</select>' . wp_kses_post( $description );
					$settings .= '</div>';
					$settings .= '</div>';
					break;

				// Radio inputs.
				case 'radio':
					$option_value = UR_Admin_Settings::get_option( $value['id'], $value['default'] );
					$settings    .= '<div class="user-registration-login-form-global-settings">';
					$settings    .= '<label for="' . esc_attr( $value['id'] ) . '">' . esc_html( $value['title'] ) . ' ' . wp_kses_post( $tooltip_html ) . '</label>';
					$settings    .= '<div class="user-registration-login-form-global-settings--field">';
					$settings    .= '<fieldset>';
					$settings    .= wp_kses_post( $description );
					$settings    .= '<ul>';

					foreach ( $value['options'] as $key => $val ) {
						$settings .= '<li>';
						$settings .= '<label>';
						$settings .= '<input
									name="' . esc_attr( $value['id'] ) . '"
									value="' . esc_attr( $key ) . '"
									type="radio"
									style="' . esc_attr( $value['css'] ) . '"
									class="' . esc_attr( $value['class'] ) . '"
									' . esc_attr( implode( ' ', $custom_attributes ) ) . '
									' . esc_attr( checked( $key, $option_value, false ) ) . '
									/>' . wp_kses_post( $val ) . '</label>';
						$settings .= '</li>';
					}

					$settings .= '</ul>';
					$settings .= '</fieldset>';
					$settings .= '</div>';
					$settings .= '</div>';
					break;

				// Checkbox input.
				case 'checkbox':
					$option_value = UR_Admin_Settings::get_option( $value['id'], $value['default'] );

					$visbility_class = array();

					if ( ! isset( $value['hide_if_checked'] ) ) {
						$value['hide_if_checked'] = false;
					}
					if ( ! isset( $value['show_if_checked'] ) ) {
						$value['show_if_checked'] = false;
					}
					if ( 'yes' === $value['hide_if_checked'] || 'yes' === $value['show_if_checked'] ) {
						$visbility_class[] = 'hidden_option';
					}
					if ( 'option' === $value['hide_if_checked'] ) {
						$visbility_class[] = 'hide_options_if_checked';
					}
					if ( 'option' === $value['show_if_checked'] ) {
						$visbility_class[] = 'show_options_if_checked';
					}
					$settings .= '<div class="user-registration-login-form-global-settings ' . esc_attr( implode( ' ', $visbility_class ) ) . ' ' . esc_attr( $value['row_class'] ) . '">';

					if ( ! isset( $value['checkboxgroup'] ) || 'start' === $value['checkboxgroup'] ) {
						$settings .= '<label for="' . esc_attr( $value['id'] ) . '">' . esc_html( $value['title'] ) . ' ' . wp_kses_post( $tooltip_html ) . '</label>';
						$settings .= '<div class="user-registration-login-form-global-settings--field">';
						$settings .= '<fieldset>';
					} else {
						$settings .= '<div class="user-registration-login-form-global-settings--field">';
						$settings .= '<fieldset class="' . esc_attr( implode( ' ', $visbility_class ) ) . '">';
					}

					$settings .= '<input
							name="' . esc_attr( $value['id'] ) . '"
							id="' . esc_attr( $value['id'] ) . '"
							type="checkbox"
							class="' . esc_attr( isset( $value['class'] ) ? $value['class'] : '' ) . '"
							value="1"
							' . esc_attr( checked( $option_value, 'yes', false ) ) . '
							' . esc_attr( implode( ' ', $custom_attributes ) ) . '/>';

					$settings .= '</fieldset>';
					$settings .= wp_kses_post( $description );
					$settings .= wp_kses_post( $desc_field );
					$settings .= '</div>';
					break;

				// Single page selects.
				case 'single_select_page':
					$args = array(
						'name'             => $value['id'],
						'id'               => $value['id'],
						'sort_column'      => 'menu_order',
						'sort_order'       => 'ASC',
						'show_option_none' => ' ',
						'class'            => $value['class'],
						'echo'             => false,
						'selected'         => absint( UR_Admin_Settings::get_option( $value['id'], $value['default'] ) ),
					);

					if ( isset( $value['args'] ) ) {
						$args = wp_parse_args( $value['args'], $args );
					}

					$settings .= '<div class="user-registration-login-form-global-settings single_select_page" ' . ( ( isset( $value['display'] ) && 'none' === $value['display'] ) ? 'style="display:none"' : '' ) . '>';
					$settings .= '<label for="' . esc_attr( $value['id'] ) . '">' . esc_html( $value['title'] ) . ' ' . wp_kses_post( $tooltip_html ) . '</label>';
					$settings .= '<div class="user-registration-login-form-global-settings--field">';
					$settings .= str_replace( ' id=', " data-placeholder='" . esc_attr__( 'Select a page&hellip;', 'user-registration' ) . "' style='" . esc_attr( $value['css'] ) . "' class='" . esc_attr( $value['class'] ) . "' id=", wp_dropdown_pages( $args ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$settings .= wp_kses_post( $description );
					$settings .= '</div>';
					$settings .= '</div>';
					break;

				case 'tinymce':
					$editor_settings = array(
						'name'       => esc_attr( $value['id'] ),
						'id'         => esc_attr( $value['id'] ),
						'style'      => esc_attr( $value['css'] ),
						'default'    => esc_attr( $value['default'] ),
						'class'      => esc_attr( $value['class'] ),
						'quicktags'  => array( 'buttons' => 'em,strong,link' ),
						'tinymce'    => array(
							'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
							'theme_advanced_buttons2' => '',
						),
						'editor_css' => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>',
					);

					$option_value = UR_Admin_Settings::get_option( $value['id'], $value['default'] );

					$settings .= '<div class="user-registration-login-form-global-settings">';
					$settings .= '<label for="' . esc_attr( $value['id'] ) . '">' . esc_html( $value['title'] ) . ' ' . wp_kses_post( $tooltip_html ) . '</label>';
					$settings .= '<div class="user-registration-login-form-global-settings--field">';
					$settings .= wp_kses_post( $description );

					// Output buffer for tinymce editor.
					ob_start();
					wp_editor( $option_value, $value['id'], $editor_settings );
					$settings .= ob_get_clean();

					$settings .= '</div>';
					$settings .= '</div>';

					break;

				case 'link':
					$settings .= '<div class="user-registration-login-form-global-settings">';
					$settings .= '<label for="' . esc_attr( $value['id'] ) . '">' . esc_attr( $value['title'] ) . ' ' . wp_kses_post( $tooltip_html ) . '</label>';
					$settings .= '<div class="user-registration-login-form-global-settings--field">';

					if ( isset( $value['buttons'] ) && is_array( $value['buttons'] ) ) {
						foreach ( $value['buttons'] as $button ) {
							$settings .= '<a
										href="' . esc_url( $button['href'] ) . '"
										class="button ' . esc_attr( $button['class'] ) . '" style="' . esc_attr( $value['css'] ) . '">' . esc_html( $button['title'] ) . '</a>';
						}
					}

					$settings .= ( isset( $value['desc'] ) && isset( $value['desc_tip'] ) && true !== $value['desc_tip'] ) ? '<p class="description" >' . wp_kses_post( $value['desc'] ) . '</p>' : '';
					$settings .= '</div>';
					$settings .= '</div>';
					break;
				// Image upload.
				case 'image':
					$option_value = UR_Admin_Settings::get_option( $value['id'], $value['default'] );

					$settings .= '<div class="user-registration-login-form-global-settings image-upload">';

					$settings .= '<label for="' . esc_attr( $value['id'] ) . '">' . esc_attr( $value['title'] ) . ' ' . wp_kses_post( $tooltip_html ) . '</label>';
					$settings .= '<div class="user-registration-login-form-global-settings--field">';
					$settings .= '<img src="' . esc_attr( $option_value ) . '" alt="' . esc_attr__( 'Header Logo', 'user-registration' ) . '" class="ur-image-uploader" height="auto" width="20%">';
					$settings .= '<button type="button" class="ur-image-uploader ur-button button-secondary" ' . ( empty( $option_value ) ? '' : 'style = "display:none"' ) . '>' . esc_html__( 'Upload Image', 'user-registration' ) . '</button>';
					$settings .= '<button type="button" class="ur-image-remover ur-button button-secondary" ' . ( ! empty( $option_value ) ? '' : 'style = "display:none"' ) . '>' . esc_html__( 'Remove Image', 'user-registration' ) . '</button>';

					$settings .= '	<input
							name="' . esc_attr( $value['id'] ) . '"
							id="' . esc_attr( $value['id'] ) . '"
							value="' . esc_attr( $option_value ) . '"
							type="hidden"
						>';
					$settings .= '</div>';
					$settings .= '</div>';
					wp_enqueue_media();

					break;

				// Radio image inputs.
				case 'radio-image':
					$option_value = UR_Admin_Settings::get_option( $value['id'], $value['default'] );

					$settings .= '<div class="user-registration-login-form-global-settings radio-image">';
					$settings .= '<label for="' . esc_attr( $value['id'] ) . '">' . esc_attr( $value['title'] ) . ' ' . wp_kses_post( $tooltip_html ) . '</label>';
					$settings .= '<div class="user-registration-login-form-global-settings--field">';
					$settings .= '<ul>';

					foreach ( $value['options'] as $key => $val ) {
						$settings .= '<li>';
						$settings .= '<label class="' . ( esc_attr( checked( $key, $option_value, false ) ) ? 'selected' : '' ) . '">';
						$settings .= '<img src="' . esc_html( $val['image'] ) . '">';
						$settings .= '<input
									name="' . esc_attr( $value['id'] ) . '"
									value="' . esc_attr( $key ) . '"
									type="radio"
									style="' . esc_attr( $value['css'] ) . '"
									class="' . esc_attr( $value['class'] ) . '"
									' . esc_attr( implode( ' ', $custom_attributes ) ) . '
									' . esc_attr( checked( $key, $option_value, false ) ) . '>';

						$settings .= esc_html( $val['name'] );
						$settings .= '</label>';
						$settings .= '</li>';
					}

					$settings .= '</ul>';
					$settings .= '</div>';
					$settings .= '</div>';
					break;
				// Toggle input.
				case 'toggle':
					$option_value = UR_Admin_Settings::get_option( $value['id'], $value['default'] );

					$settings .= '<div class="user-registration-login-form-global-settings">';
					$settings .= '<div class="user-registration-login-form-toggle-option">';
					$settings .= '<label for="' . esc_attr( $value['id'] ) . '">' . esc_html( $value['title'] ) . ' ' . wp_kses_post( $tooltip_html ) . '</label>';
					$settings .= '<div class="user-registration-login-form-global-settings--field">';
					$settings .= '<div class="ur-toggle-section">';
					$settings .= '<span class="user-registration-toggle-form">';
					$settings .= '<input
								type="checkbox"
								name="' . esc_attr( $value['id'] ) . '"
								id="' . esc_attr( $value['id'] ) . '"
								style="' . esc_attr( $value['css'] ) . '"
								class="' . esc_attr( $value['class'] ) . '"
								value="1"
								' . esc_attr( implode( ' ', $custom_attributes ) ) . '
								' . esc_attr( checked( true, ur_string_to_bool( $option_value ), false ) ) . '>';
					$settings .= '<span class="slider round"></span>';
					$settings .= '</span>';
					$settings .= '</div>';
					$settings .= '</div>';
					$settings .= '</div>';
					$settings .= wp_kses_post( $description );
					$settings .= wp_kses_post( $desc_field );
					$settings .= '</div>';
					break;
				case 'radio-group':
					$option_value = UR_Admin_Settings::get_option( $value['id'], $value['default'] );
					$options      = isset( $value['options'] ) ? $value['options'] : array(); // $args['choices'] for backward compatibility. Modified since 1.5.7.

					if ( ! empty( $options ) ) {
						$settings .= '<div class="user-registration-login-form-global-settings">';
						$settings .= '<label for="' . esc_attr( $value['id'] ) . '">' . esc_html( $value['title'] ) . ' ' . wp_kses_post( $tooltip_html ) . '</label>';
						$settings .= '<div class="user-registration-login-form-global-settings--field">';

						$settings .= '<ul class="ur-radio-group-list">';
						foreach ( $options as $option_index => $option_text ) {
							$class     = str_replace( ' ', '-', strtolower( $option_text ) );
							$settings .= '<li class="ur-radio-group-list--item  ' . $class . ( trim( $option_index ) === $option_value ? ' active' : '' ) . '">';

							$checked = '';

							if ( '' !== $option_value ) {
								$checked = checked( $option_value, trim( $option_index ), false );
							}

							$settings .= '<label for="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_text ) . '" class="radio">';

							if ( isset( $value['radio-group-images'] ) ) {
								$settings .= '<img src="' . $value['radio-group-images'][ $option_index ] . '" />';
							}

							$settings .= wp_kses(
								trim( $option_text ),
								array(
									'a'    => array(
										'href' => array(),
										'title' => array(),
									),
									'span' => array(),
								)
							);

							$settings .= '<input type="radio" name="' . esc_attr( $value['id'] ) . '" id="' . esc_attr( $value['id'] ) . '"	style="' . esc_attr( $value['css'] ) . '" class="' . esc_attr( $value['class'] ) . '" value="' . esc_attr( trim( $option_index ) ) . '" ' . implode( ' ', $custom_attributes ) . ' / ' . $checked . ' /> ';
							$settings .= '</label>';

							$settings .= '</li>';
						}
						$settings .= '</ul>';
						$settings .= '</div>';
						$settings .= '</div>';

					}
					break;
				// Default: run an action.
				default:
					/**
					 * Filter to retrieve default admin field for output
					 *
					 * @param string $settings Settings.
					 * @param mixed $settings Field value.
					 */
					$settings = apply_filters( 'user_registration_admin_field_' . $value['type'], $settings, $value );
					break;
			}// End switch case.
		}
		echo $settings;
	}
}

add_filter('user_registration_find_my_account_in_page', 'ur_find_my_account_in_custom_template', 10, 2);

if ( ! function_exists( 'ur_find_my_account_in_custom_template' ) ) {
	/**
	 * Return true if found ur my account or login
	 *
	 * @param $value
	 * @param $page_id
	 *
	 * @return mixed
	 */
	function ur_find_my_account_in_custom_template( $value, $page_id ) {

		if ( $value ) {
			return $value;
		}

		$template_path = get_page_template();

		if ( empty( $template_path ) ) {
			return $value;
		}

		$content = ur_file_get_contents( $template_path );

		if ( empty( $content ) || !is_string( $content ) ) {
			return $value;
		}

		if ( strpos( $content, '[user_registration_my_account' ) !== false ) {
			return true;
		}
		if ( strpos( $content, '[user_registration_login' ) !== false ) {
			return true;
		}
		if ( strpos( $content, '[woocommerce_my_account' ) !== false ) {
			return true;
		}
		if ( strpos( $content, '<!-- wp:user-registration/myaccount' ) !== false ) {
			return true;
		}
		if ( strpos( $content, '<!-- wp:user-registration/login' ) !== false ) {
			return true;
		}

		return $value;
	}
}

add_filter( 'user_registration_get_endpoint_url', 'ur_filter_get_endpoint_url' , 10, 4 );

if( ! function_exists( 'ur_filter_get_endpoint_url' ) ) {
/**
 * Filter the endpoint URL for WPML compatibility.
 *
 * This function modifies the endpoint URL when WPML is active to ensure proper translation
 * and localization of URLs. It removes the filter temporarily to avoid infinite loops,
 * translates the endpoint, converts the URL using WPML's convert_url method, and then
 * re-adds the filter.
 *
 *
 * @param string $url       The endpoint URL.
 * @param string $endpoint  The endpoint slug.
 * @param mixed  $value     The value to add to the URL.
 * @param string $permalink The permalink URL.
 *
 * @return string Modified URL if WPML is active, original urk if WPML is not active.
 */

	 function ur_filter_get_endpoint_url( $url, $endpoint, $value, $permalink ) {
		//Return early WPML is not active
		if ( ! class_exists( 'SitePress' ) ) {
			return $url;
		}
		$site_press = new SitePress();
		remove_filter( 'user_registration_get_endpoint_url', 'ur_filter_get_endpoint_url', 10 );

		$translated_endpoint = ur_get_endpoint_translation( $endpoint );
		$url = ur_get_endpoint_url( $translated_endpoint, $value, $site_press->convert_url( $permalink ) );
		add_filter( 'user_registration_get_endpoint_url', 'ur_filter_get_endpoint_url', 10, 4 );
		return $url;
	}
}

if( ! function_exists( 'ur_get_endpoint_translation' ) ) {
	/**
	 * Get the translated endpoint
	 *
	 * @param $endpoint
	 *
	 * @return string
	 */
	 function ur_get_endpoint_translation( $endpoint ,$language = null ) {

		return apply_filters( 'wpml_get_endpoint_translation', $endpoint, $endpoint, $language );
	}
}

add_filter('user_registration_get_endpoint_url',  'ur_filter_get_endpoint_url', 10, 4);

if( ! function_exists( 'ur_register_endpoints_translations') ) {

	function ur_register_endpoints_translations(){
		/**
		 * Register the endpoint translations
		 */
    	 if(  is_admin() || ! defined('ICL_SITEPRESS_VERSION') || ICL_PLUGIN_INACTIVE){
			return false;
		 }

		 $ur_vars = UR()->query->query_vars;

		 if (! empty($ur_vars)) {
			$query_vars = array(

				// My account actions.
				'edit-profile'       => get_endpoint_translation('edit-profile', $ur_vars['edit-profile'], $language),
				'change-password'       => get_endpoint_translation('change-password', $ur_vars['change-password'], $language),
				'lost-password'      => get_endpoint_translation('lost-password', $ur_vars['lost-password'], $language),
				'user-logout'    => get_endpoint_translation('user-logout', $ur_vars['user-logout'], $language),
			);
			$query_vars = apply_filters('wcml_register_endpoints_query_vars', $query_vars, $ur_vars, $this);

			$query_vars             = array_merge($ur_vars, $query_vars);
			UR()->query->query_vars = $query_vars;
		}

		return UR()->query->query_vars;

 	}
}

if( ! function_exists( 'get_endpoint_translation' ) ) {
	/**
	 * Get the translated endpoint
	 *
	 * @param $endpoint
	 *
	 * @return string
	 */
	 function get_endpoint_translation( $endpoint, $value, $language = null ) {

		if (function_exists('icl_t')) {
			$trnsl = apply_filters('wpml_translate_single_string', $endpoint, 'UserRegistration Endpoints', $key, $language);

			if (! empty($trnsl)) {
				return $trnsl;
			} else {
				return $endpoint;
			}
		} else {
			return $endpoint;
		}
	}
}

add_filter( 'user_registration_check_user_order_status', 'get_user_order_status', 10, 1 );

if ( ! function_exists( 'get_user_order_status' ) ) {

	function get_user_order_status( $user_id ) {
		$member_order_repository = new MembersOrderRepository();
		$member_order            = $member_order_repository->get_member_orders( $user_id );
		$status                  = '';
		if ( ! empty( $member_order ) && isset( $member_order['status'] ) ) {
			$status = $member_order['status'];
		}

		return $status;
	}
}

if ( ! function_exists( 'ur_get_sms_verification_default_message_content' ) ) {
	/**
	 * Get sms verification message content .
	 *
	 * @since 4.2.1
	 * @return array
	 */
	function ur_get_sms_verification_default_message_content() {
		$message = sprintf(__("Hi {{username}}, <br> Your One  Time Password (OTP) is : {{sms_otp}} <br> Enter this code to login to your account. <br> Note: This code expires in {{sms_otp_validity}} minutes. <br> Thank You!", 'user-registration'));

		return $message;
	}
}

if ( ! function_exists( 'ur_setting_keys' ) ) {
	/**
     * Returns an array of default settings for User Registration and its addons.
     *
     * This function provides default settings for different plugins related to
     * user registration, including general settings, login options, file uploads,
     * PDF submissions, social login, and two-factor authentication.
     *
     * @return array Default settings for various User Registration addons.
     */
    function ur_setting_keys() {
        return array(
            'user-registration/user-registration.php' => array(
                array( 'user_registration_general_setting_disabled_user_roles', '["subscriber"]' ),
                array( 'user_registration_login_option_hide_show_password', false ),
                array( 'user_registration_myaccount_page_id', '' ),
                array( 'user_registration_my_account_layout', 'horizontal' ),
                array( 'user_registration_ajax_form_submission_on_edit_profile', false ),
                array( 'user_registration_disable_profile_picture', false ),
                array( 'user_registration_disable_logout_confirmation', false ),
                array( 'user_registration_login_options_form_template', 'default' ),
                array( 'user_registration_general_setting_login_options_with', 'default' ),
                array( 'user_registration_login_title', false ),
                array( 'ur_login_ajax_submission', false ),
                array( 'user_registration_login_options_remember_me', true ),
                array( 'user_registration_login_options_lost_password', true ),
                array( 'user_registration_login_options_hide_labels', false ),
                array( 'user_registration_login_options_enable_recaptcha', false ),
                array( 'user_registration_general_setting_registration_url_options', '' ),
                array( 'user_registration_login_options_prevent_core_login', false ),
                array( 'user_registration_login_options_login_redirect_url', '' ),
                array( 'user_registration_captcha_setting_recaptcha_version', 'v2' ),
                array( 'user_registration_login_options_configured_captcha_type', 'v2' ),
                array( 'user_registration_general_setting_uninstall_option', false ),
                array( 'user_registration_allow_usage_tracking', false )
            ),
            'user-registration-pro/user-registration.php' => array(
                array( 'user_registration_pro_general_setting_delete_account', 'disable' ),
                array( 'user_registration_pro_general_setting_login_form', false ),
                array( 'user_registration_pro_general_setting_prevent_active_login', false ),
                array( 'user_registration_pro_general_setting_limited_login', '5' ),
                array( 'user_registration_pro_general_setting_redirect_back_to_previous_page', false ),
                array( 'user_registration_pro_general_post_submission_settings', '' ),
                array( 'user_registration_pro_general_setting_post_submission', 'disable' ),
                array( 'user_registration_pro_role_based_redirection', false ),
                array( 'user_registration_payment_currency', 'USD' ),
                array( 'user_registration_content_restriction_enable', true ),
                array( 'user_registration_content_restriction_allow_to_roles', '["administrator"]' )
            ),
            'user-registration-file-upload/user-registration-file-upload.php' => array(
                array( 'user_registration_file_upload_setting_valid_file_type', '["pdf"]' ),
                array( 'user_registration_file_upload_setting_max_file_size', '1024' )
            ),
            'user-registration-pdf-submission/user-registration-pdf-submission.php' => array(
                array( 'user_registration_pdf_template', 'default' ),
                array( 'user_registration_pdf_logo_image', '' ),
                array( 'user_registration_pdf_setting_header', '' ),
                array( 'user_registration_pdf_custom_header_text', '' ),
                array( 'user_registration_pdf_paper_size', '' ),
                array( 'user_registration_pdf_orientation', 'portrait' ),
                array( 'user_registration_pdf_font', '' ),
                array( 'user_registration_pdf_font_size', '12' ),
                array( 'user_registration_pdf_font_color', '#000000' ),
                array( 'user_registration_pdf_background_color', '#ffffff' ),
                array( 'user_registration_pdf_header_font_color', '#000000' ),
                array( 'user_registration_pdf_header_background_color', '#ffffff' ),
                array( 'user_registration_pdf_multiple_column', false ),
                array( 'user_registration_pdf_rtl', false ),
                array( 'user_registration_pdf_print_user_default_fields', false ),
                array( 'user_registration_pdf_hide_empty_fields', false )
            ),
            'user-registration-social-connect/user-registration-social-connect.php' => array(
                array( 'user_registration_social_setting_enable_facebook_connect', '' ),
                array( 'user_registration_social_setting_enable_twitter_connect', '' ),
                array( 'user_registration_social_setting_enable_google_connect', '' ),
                array( 'user_registration_social_setting_enable_linkedin_connect', '' ),
                array( 'user_registration_social_setting_enable_social_registration', false ),
                array( 'user_registration_social_setting_display_social_buttons_in_registration', false ),
                array( 'user_registration_social_setting_default_user_role', 'subscriber' ),
                array( 'user_registration_social_login_position', 'bottom' ),
                array( 'user_registration_social_login_template', 'ursc_theme_4' )
            ),
            'user-registration-two-factor-authentication/user-registration-two-factor-authentication.php' => array(
                array( 'user_registration_tfa_enable_disable', false ),
                array( 'user_registration_tfa_roles', '["subscriber"]' ),
                array( 'user_registration_tfa_otp_length', '6' ),
                array( 'user_registration_tfa_otp_expiry_time', '10' ),
                array( 'user_registration_tfa_otp_resend_limit', '3' ),
                array( 'user_registration_tfa_incorrect_otp_limit', '5' ),
                array( 'user_registration_tfa_login_hold_period', '60' )
            ),
        );
    }
}
/**
 * Trigger logging cleanup using the logging class.
 *
 * @since x.x.x
 */
function ur_cleanup_logs() {
	$logger = ur_get_logger();

	if ( is_callable( array( $logger, 'clear_expired_logs' ) ) ) {
		$logger->clear_expired_logs();
	}
}
add_action( 'user_registration_cleanup_logs', 'ur_cleanup_logs' );

if ( ! function_exists( 'ur_sanitize_value_by_type' ) ) {
	/**
	 * Get sms verification message content .
	 *
	 * @since 4.2.1
	 * @return array
	 */
	function ur_sanitize_value_by_type($option, $raw_value) {

		// Format the value based on option type.
		switch ( $option['type'] ) {

			case 'checkbox':
			case 'toggle':
				$value = ur_string_to_bool( $raw_value );
				break;
			case 'textarea':
				$value = wp_kses_post( trim( $raw_value ) );
				break;
			case 'multiselect':
				$value = array_filter( array_map( 'ur_clean', (array) $raw_value ) );
				break;
			case 'select':
				$allowed_values = empty( $option['options'] ) ? array() : array_keys( $option['options'] );
				if ( empty( $option['default'] ) && empty( $allowed_values ) ) {
					$value = null;
					break;
				}
				$default = ( empty( $option['default'] ) ? $allowed_values[0] : $option['default'] );
				$value   = in_array( $raw_value, $allowed_values ) ? sanitize_text_field( $raw_value ) : sanitize_text_field( $default );
				break;
			case 'tinymce':
				$value = wpautop( $raw_value );
				break;

			default:
				$value = ur_clean( $raw_value );
				break;
		}
		return $value;
	}
};


if ( ! function_exists( 'ur_save_settings_options' ) ) {

	/**
	 * ur_save_settings_options
	 *
	 * @param $option
	 * @param $raw_value
	 *
	 * @return void
	 */
	function ur_save_settings_options($section, $form_data) {
		$update_options = array();

		foreach ( $section['settings'] as $option ) {
			if ( empty( $option['id'] ) ) {
				continue;
			}

			$option_id = $option['id'];
			$option_name  = '';
			$setting_name = '';

			// Parse array notation (e.g., option_name[setting_name])
			if ( str_contains( $option_id, '[' ) && str_contains( $option_id, ']' ) ) {
				if ( preg_match( '/^([^[\]]+)\[([^[\]]+)\]$/', $option_id, $matches ) ) {
					$option_name  = sanitize_text_field( $matches[1] );
					$setting_name = sanitize_text_field( $matches[2] );
				}
			} else {
				$option_name = sanitize_text_field( $option_id );
			}

			if ( isset( $form_data[ $option_id ] ) ) {
				$value = ur_sanitize_value_by_type( $option, $form_data[ $option_id ] );
				if ( $option_name && $setting_name ) {
					if ( ! isset( $update_options[ $option_name ] ) || ! is_array( $update_options[ $option_name ] ) ) {
						$existing = get_option( $option_name, array() );
						$update_options[ $option_name ] = is_array( $existing ) ? $existing : array();
					}
					$update_options[ $option_name ][ $setting_name ] = $value;
				} elseif ( $option_name ) {
					$update_options[ $option_name ] = $value;
				}
			}
		}

		foreach ( $update_options as $name => $value ) {
			update_option( $name, $value );
		}
	}
};

