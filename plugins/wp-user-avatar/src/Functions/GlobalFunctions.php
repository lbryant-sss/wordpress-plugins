<?php

use ProfilePress\Core\Admin\SettingsPages\MailOptin;
use ProfilePress\Core\Base;
use ProfilePress\Core\Classes\ExtensionManager as EM;
use ProfilePress\Core\Classes\FormRepository as FR;
use ProfilePress\Core\Classes\PROFILEPRESS_sql;
use ProfilePress\Core\Classes\SendEmail;
use ProfilePress\Core\Membership\CheckoutFields;
use ProfilePress\Core\ShortcodeParser\Builder\FrontendProfileBuilder;

/** Plugin DB settings data */
function ppress_db_data()
{
    return get_option(PPRESS_SETTINGS_DB_OPTION_NAME, []);
}

function ppress_update_settings($key, $value)
{
    $data       = ppress_db_data();
    $data[$key] = $value;
    update_option(PPRESS_SETTINGS_DB_OPTION_NAME, $data);
}

/**
 * Array of WooCommerce billing fields.
 *
 * @return array
 */
function ppress_woocommerce_billing_fields()
{
    return array(
        'billing_first_name',
        'billing_last_name',
        'billing_company',
        'billing_address_1',
        'billing_address_2',
        'billing_city',
        'billing_postcode',
        'billing_country',
        'billing_state',
        'billing_phone',
        'billing_email'
    );
}

/**
 * Array of WooCommerce billing fields.
 *
 * @return array
 */
function ppress_woocommerce_shipping_fields()
{
    return array(
        'shipping_first_name',
        'shipping_last_name',
        'shipping_company',
        'shipping_address_1',
        'shipping_address_2',
        'shipping_city',
        'shipping_postcode',
        'shipping_country',
        'shipping_state'
    );
}

/**
 * Array of WooCommerce billing and shipping fields.
 *
 * @return array
 */
function ppress_woocommerce_billing_shipping_fields()
{
    return array_merge(ppress_woocommerce_billing_fields(), ppress_woocommerce_shipping_fields());
}

/**
 * @param string $key
 * @param bool $default
 * @param bool $is_empty set to true to return the default if value is empty
 *
 * @return mixed
 */
function ppress_settings_by_key($key = '', $default = false, $is_empty = false)
{
    $data = ppress_db_data();

    if ($is_empty === true) {
        return isset($data[$key]) && ( ! empty($data[$key]) || ppress_is_boolean($data[$key])) ? $data[$key] : $default;
    }

    return $data[$key] ?? $default;
}

function ppress_get_setting($key = '', $default = false, $is_empty = false)
{
    return ppress_settings_by_key($key, $default, $is_empty);
}

/**
 * Send email.
 *
 * @param string|array $to
 * @param $subject
 * @param $message
 *
 * @return bool|WP_Error
 */
function ppress_send_email($to, $subject, $message)
{
    return (new SendEmail($to, $subject, $message))->send();
}

function ppress_welcome_msg_content_default()
{
    return <<<HTML
<h1>Welcome {{first_name}}!</h1>
<p>We are so happy to have you. Below is your login credential:</p>
<p>Username: {{username}}</p>
<p>Password: the password you registered with.</p>

<div style="padding: 10px 0 50px 0; text-align: center;">
    <a style="background: #555555; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 3px; letter-spacing: 0.3px;" href="{{login_link}}">Click here to login</a>
</div>
<p>If you have any problems, do not hesitate to contact us.</p>
HTML;
}

function ppress_new_user_admin_notification_message_default()
{
    return <<<HTML
<p>New user registration on your site {{site_title}}.</p>
<p>Username: {{username}}</p>
<p>Email address: {{user_email}}</p>
HTML;
}

function ppress_passwordless_login_message_default()
{
    return <<<MESSAGE
<p>Hi {{username}}, we have generated a one-time login link for you.</p>
<div style="padding: 10px 0 50px 0; text-align: center;">
    <a style="background: #555555; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 3px; letter-spacing: 0.3px;" href="{{passwordless_link}}">Click here to login</a>
</div>
MESSAGE;
}

function ppress_user_moderation_msg_default($type)
{
    $pending = <<<MESSAGE
<p>Hi {{first_name}} {{last_name}}, your account is pending approval.</p>
<p>You will receive an email once your account is approved.</p>
<p>Regards.</p>
MESSAGE;

    $approved = <<<MESSAGE
<p>Hi {{first_name}} {{last_name}}, your account has been approved.</p>

<p>Regards.</p>
MESSAGE;

    $rejected = <<<MESSAGE
<p>Hi {{first_name}} {{last_name}}, your account has been rejected.</p>

<p>Regards.</p>
MESSAGE;

    $blocked = <<<MESSAGE
<p>Hi {{first_name}} {{last_name}}, your account has been blocked.</p>

<p>Regards.</p>
MESSAGE;

    $unblocked = <<<MESSAGE
<p>Hi {{first_name}} {{last_name}}, your account with username "{{username}}" has been unblocked.</p>

<p>Regards.</p>
MESSAGE;

    $admin_notification = <<<MESSAGE
<p>A new user is awaiting your approval on your site.</p>
<p>Username: {{username}}</p>
<p>E-mail: {{email}}</p>
<p>Click to approve: {{approval_url}}</p>
<p>Click to reject: {{rejection_url}}</p>
MESSAGE;

    return ${$type};
}

function ppress_password_reset_content_default()
{
    return <<<HTML
<p>Someone requested to reset the password for the following account:</p>
<p>Username: {{username}}</p>
<p>If this was a mistake, just ignore this email and nothing will happen.</p>
<p>To reset your password, click the button below.</p>
<div style="padding: 10px 0 50px 0; text-align: center;">
    <a style="background: #555555; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 3px; letter-spacing: 0.3px;" href="{{password_reset_link}}"> Reset Password</a>
</div>
HTML;
}

/**
 * Return the url to redirect to after login authentication
 *
 * @return bool|string
 */
function ppress_login_redirect()
{
    if ( ! empty($_REQUEST['redirect_to'])) {
        $redirect = rawurldecode($_REQUEST['redirect_to']);
    } else {
        $login_redirect            = ppress_get_setting('set_login_redirect');
        $custom_url_login_redirect = ppress_get_setting('custom_url_login_redirect');
        $referrer_url              = ppress_var($_POST, 'login_referrer_page', ppress_var($_POST, 'signup_referrer_page'));

        if ( ! empty($custom_url_login_redirect)) {
            $redirect = $custom_url_login_redirect;
        } elseif ($login_redirect == 'dashboard') {
            $redirect = network_site_url('/wp-admin');
        } elseif ($login_redirect == 'previous_page' && ! empty($referrer_url)) {
            $reset_url = untrailingslashit(ppress_password_reset_url());
            $redirect  = strstr($referrer_url, $reset_url) ? ppress_my_account_url() : $referrer_url;
        } elseif ('current_page' == $login_redirect) {
            // in ajax mode, pp_current_url is set so we can do client-side redirection to current page after login.
            // no way to get current url in social login hence, look it up from $_GET['pp_current_url']
            if ( ! empty($_GET['pp_current_url'])) {
                $redirect = rawurldecode($_GET['pp_current_url']);
            } elseif ( ! empty($_POST['pp_current_url'])) {
                $redirect = rawurldecode($_POST['pp_current_url']);
            } else {
                $redirect = ppress_get_current_url_raw();
            }
        } elseif ( ! empty($login_redirect) && is_numeric($login_redirect)) {
            $redirect = get_permalink($login_redirect);
        } else {
            $redirect = admin_url();
        }
    }

    return wp_validate_redirect(apply_filters('ppress_core_login_redirect', $redirect));
}

/**
 * Return the url to redirect to after successful reset / change of password.
 *
 * @return bool|string
 */
function ppress_password_reset_redirect()
{
    $password_reset_redirect            = ppress_get_setting('set_password_reset_redirect');
    $custom_url_password_reset_redirect = ppress_get_setting('custom_url_password_reset_redirect');

    if ( ! empty($custom_url_password_reset_redirect)) {
        $redirect = $custom_url_password_reset_redirect;
    } elseif ( ! empty($password_reset_redirect)) {
        $redirect = get_permalink($password_reset_redirect);
        if ($password_reset_redirect == 'no_redirect') {
            $redirect = ppress_password_reset_url() . '?password=changed';
        }
    } else {
        $redirect = ppress_password_reset_url() . '?password=changed';
    }

    return apply_filters('ppress_do_password_reset_redirect', esc_url_raw($redirect));
}

/**
 * Return the url to frontend myprofile page.
 *
 * @return bool|string
 */
function ppress_profile_url()
{
    $url = admin_url('profile.php');

    $page_id = ppress_get_setting('set_user_profile_shortcode');

    if ( ! empty($page_id)) {
        $url = get_permalink($page_id);
    }

    return apply_filters('ppress_profile_url', $url);
}

/**
 * @param $username_or_id
 *
 * @return string
 */
function ppress_get_frontend_profile_url($username_or_id)
{
    if (is_numeric($username_or_id)) {
        $username_or_id = ppress_get_username_by_id($username_or_id);
    }

    return apply_filters(
        'ppress_frontend_profile_url',
        home_url(ppress_get_profile_slug() . '/' . rawurlencode($username_or_id)),
        $username_or_id
    );
}

/**
 * Return ProfilePress edit profile page URL or WP default profile URL as fallback
 *
 * @return bool|string
 */
function ppress_edit_profile_url()
{
    return apply_filters('ppress_edit_profile_url', ppress_my_account_url());
}

function ppress_my_account_url()
{
    $url = get_edit_profile_url();

    $page_id = ppress_settings_by_key('edit_user_profile_url');

    if ( ! empty($page_id) && get_post_status($page_id)) {
        $url = get_permalink($page_id);
    }

    return apply_filters('ppress_my_account_url', $url);
}

/**
 * Return ProfilePress password reset url.
 *
 * @return string
 */
function ppress_password_reset_url()
{
    $url = wp_lostpassword_url();

    $page_id = ppress_get_setting('set_lost_password_url');

    if ( ! empty($page_id) && get_post_status($page_id)) {
        $url = get_permalink($page_id);
    }

    return apply_filters('ppress_password_reset_url', $url);
}


/**
 * Get ProfilePress login page URL or WP default login url if it isn't set.
 *
 * @param $redirect
 *
 * @return string
 */
function ppress_login_url($redirect = '')
{
    $login_url = wp_login_url();

    $login_page_id = ppress_get_setting('set_login_url');

    if ( ! empty($login_page_id) && get_post_status($login_page_id)) {
        $login_url = get_permalink($login_page_id);
    }

    if ( ! empty($redirect)) {
        $login_url = add_query_arg('redirect_to', rawurlencode(wp_validate_redirect($redirect)), $login_url);
    }

    return apply_filters('ppress_login_url', $login_url);
}

/**
 * Get ProfilePress login page URL or WP default login url if it isn't set.
 */
function ppress_registration_url()
{
    $page_id = ppress_get_setting('set_registration_url');

    if ( ! empty($page_id) && get_post_status($page_id)) {
        $reg_url = get_permalink($page_id);
    } else {
        $reg_url = wp_registration_url();
    }

    return apply_filters('ppress_registration_url', $reg_url);
}

/**
 * Return the URL of the currently view page.
 *
 * @return string
 */
function ppress_get_current_url()
{
    global $wp;

    return home_url(add_query_arg(array(), $wp->request));
}


/**
 * Return currently viewed page url without query string.
 *
 * @return string
 */
function ppress_get_current_url_raw()
{
    $protocol = 'http://';

    if ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1))
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
    ) {
        $protocol = 'https://';
    }

    return esc_url_raw($protocol . $_SERVER['HTTP_HOST'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
}

/**
 * Return currently viewed page url with query string.
 *
 * @return string
 */
function ppress_get_current_url_query_string()
{
    $protocol = 'http://';

    if ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1))
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
    ) {
        $protocol = 'https://';
    }

    $url = $protocol . $_SERVER['HTTP_HOST'];

    $url .= $_SERVER['REQUEST_URI'];

    return esc_url_raw($url);
}

/**
 * @return string blog URL without scheme
 */
function ppress_site_url_without_scheme()
{
    $parsed_url = parse_url(home_url());

    return $parsed_url['host'];
}

/**
 * Append an option to a select dropdown
 *
 * @param string $option option to add
 * @param string $select select dropdown
 *
 * @return string
 */
function ppress_append_option_to_select($option, $select)
{
    $regex = "/<select ([^<]*)>/";

    preg_match($regex, $select, $matches);
    $select_attr = ppress_var($matches, 1);

    $a = preg_split($regex, $select);

    $join = '<select ' . $select_attr . '>' . "\r\n";
    $join .= $option . ppress_var($a, 1, '');

    return $join;
}

/**
 * Blog name or domain name if name doesn't exist
 *
 * @return string
 */
function ppress_site_title()
{
    $blog_name = get_option('blogname');

    return ! empty($blog_name) ? wp_specialchars_decode($blog_name, ENT_QUOTES) : str_replace(
        array(
            'http://',
            'https://',
        ),
        '',
        site_url()
    );
}


/**
 * Check if an admin settings page is ProfilePress'
 *
 * @return bool
 */
function ppress_is_admin_page()
{
    $pp_builder_pages = [
        PPRESS_SETTINGS_SLUG,
        PPRESS_MEMBERSHIP_ORDERS_SETTINGS_SLUG,
        PPRESS_MEMBERSHIP_SUBSCRIPTIONS_SETTINGS_SLUG,
        PPRESS_MEMBERSHIP_PLANS_SETTINGS_SLUG,
        PPRESS_MEMBERSHIP_CUSTOMERS_SETTINGS_SLUG,
        PPRESS_FORMS_SETTINGS_SLUG,
        PPRESS_MEMBER_DIRECTORIES_SLUG,
        PPRESS_CONTENT_PROTECTION_SETTINGS_SLUG,
        PPRESS_EXTENSIONS_SETTINGS_SLUG,
        PPRESS_DASHBOARD_SETTINGS_SLUG,
        MailOptin::SLUG
    ];

    return (isset($_GET['page']) && in_array($_GET['page'], $pp_builder_pages)) ||
           isset($_GET['tab']) && $_GET['tab'] == 'ppress_extensions';
}


/**
 * Return admin email
 *
 * @return string
 */
function ppress_admin_email()
{
    return get_option('admin_email');
}

/**
 * Checks whether the given user ID exists.
 *
 * @param string $user_id ID of user
 *
 * @return null|int The user's ID on success, and null on failure.
 */
function ppress_user_id_exist($user_id)
{
    if ($user = get_user_by('id', $user_id)) {
        return $user->ID;
    }

    return null;
}

/**
 * Get a user's username by their ID
 *
 * @param int $user_id
 *
 * @return bool|string
 */
function ppress_get_username_by_id($user_id)
{
    return ppress_var_obj(get_user_by('id', $user_id), 'user_login');
}

/**
 * front-end profile slug.
 *
 * @return string
 */
function ppress_get_profile_slug()
{
    return apply_filters('ppress_profile_slug', ppress_get_setting('set_user_profile_slug', 'profile', true));
}

/**
 * Filter form field attributes for unofficial attributes.
 *
 * @param array $atts supplied shortcode attributes
 *
 * @return mixed
 *
 */
function ppress_other_field_atts($atts)
{
    if ( ! is_array($atts)) return $atts;

    $official_atts = array(
        'name',
        'class',
        'id',
        'value',
        'title',
        'required',
        'placeholder',
        'key',
        'field_key',
        'limit',
        'options',
        'checkbox_text',
        'processing_label'
    );

    $other_atts = array();

    foreach ($atts as $key => $value) {
        if ( ! in_array($key, $official_atts) && strpos($key, 'on') !== 0) {
            $other_atts[esc_attr($key)] = esc_attr($value);
        }
    }

    $other_atts_html = '';

    if (is_array($other_atts) && ! empty($other_atts)) {

        foreach ($other_atts as $key => $value) {

            if ( ! empty($value)) {
                $other_atts_html = sprintf('%s="%s" ', esc_attr($key), esc_attr($value));
            }
        }
    }

    return $other_atts_html;
}


/**
 * Create an index.php file to prevent directory browsing.
 *
 * @param string $location folder path to create the file in.
 */
function ppress_create_index_file($location)
{
    $content = "You are not allowed here!";
    $fp      = fopen($location . "/index.php", "wb");
    if ($fp) {
        fwrite($fp, $content);
        fclose($fp);
    }
}

/**
 * Get front-end do password reset form url.
 *
 * @param string $user_login
 * @param string $key
 *
 * @return string
 */
function ppress_get_do_password_reset_url($user_login, $key)
{
    $url = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login');

    $page_id = ppress_get_setting('set_lost_password_url');

    if (apply_filters('ppress_front_end_do_password_reset', true) && ! empty($page_id)) {

        $url = add_query_arg(
            array(
                'key'   => $key,
                'login' => rawurlencode($user_login)
            ),
            ppress_password_reset_url()
        );
    }

    return $url;
}

/**
 * Return true if a field key exist/is multi selectable dropdown.
 *
 * @param $field_key
 *
 * @return bool
 */
function ppress_is_select_field_multi_selectable($field_key)
{
    $data = get_option('ppress_cpf_select_multi_selectable', array());

    return array_key_exists($field_key, $data);
}


/**
 * Return username/username of a user using the user's nicename to do the DB search.
 *
 * @param string $slug
 *
 * @return bool|null|string
 */
function ppress_is_slug_nice_name($slug)
{
    global $wpdb;

    $response = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT user_login FROM {$wpdb->prefix}users WHERE user_nicename = '%s'",
            array($slug)
        )
    );

    // if response isn't null, the username/user_login is returned.
    return is_null($response) ? false : $response;
}

/**
 * Return array of editable roles.
 *
 * @param $remove_admin
 *
 * @return mixed
 */
function ppress_get_editable_roles($remove_admin = true)
{
    $all_roles = wp_roles()->roles;

    if (true == $remove_admin) {
        unset($all_roles['administrator']);
    }

    return $all_roles;
}

function ppress_wp_roles_key_value($remove_admin = true)
{
    $wp_roles = ppress_get_editable_roles($remove_admin);

    return array_reduce(array_keys($wp_roles), function ($carry, $item) use ($wp_roles) {
        $carry[$item] = $wp_roles[$item]['name'];

        return $carry;
    }, []);
}

function ppress_wp_new_user_notification($user_id, $deprecated = null, $notify = '')
{
    if (null !== $deprecated) {
        _deprecated_argument(__FUNCTION__, '4.3.1');
    }

    // Accepts only 'user', 'admin' , 'both' or default '' as $notify.
    if ( ! in_array($notify, array('user', 'admin', 'both', ''), true)) {
        return;
    }

    $new_user_notification = apply_filters('ppress_new_user_notification', 'enable');

    if ('enable' != $new_user_notification) return;

    $user = get_userdata($user_id);

    // The blogname option is escaped with esc_html() on the way into the database in sanitize_option().
    // We want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    /**
     * Filters whether the admin is notified of a new user registration.
     *
     * @param bool $send Whether to send the email. Default true.
     * @param WP_User $user User object for new user.
     *
     * @since 6.1.0
     *
     */
    $send_notification_to_admin = apply_filters('wp_send_new_user_notification_to_admin', true, $user);

    if ('user' !== $notify && true === $send_notification_to_admin) {

        if (ppress_get_setting('new_user_admin_email_email_enabled', 'on') == 'on') {

            $message = ppress_get_setting('new_user_admin_email_email_content', ppress_new_user_admin_notification_message_default(), true);

            $title = ppress_get_setting('new_user_admin_email_email_subject', sprintf(__('[%s] New User Registration'), $blogname), true);

            $search = array(
                '{{username}}',
                '{{user_email}}',
                '{{site_title}}',
                '{{first_name}}',
                '{{last_name}}'
            );

            $replace = array(
                $user->user_login,
                $user->user_email,
                $blogname,
                $user->first_name,
                $user->last_name
            );

            $message = htmlspecialchars_decode(
                apply_filters(
                    'ppress_signup_admin_email_message',
                    str_replace($search, $replace, $message),
                    $user
                )
            );

            $title = apply_filters(
                'ppress_signup_admin_email_subject',
                str_replace($search, $replace, $title),
                $user
            );

            $message = ppress_custom_profile_field_search_replace($message, $user);

            $admin_email = apply_filters('ppress_signup_notification_admin_email', ppress_get_admin_notification_emails());

            ppress_send_email($admin_email, $title, $message);
        }
    }

    /**
     * Filters whether the user is notified of their new user registration.
     *
     * @param bool $send Whether to send the email. Default true.
     * @param WP_User $user User object for new user.
     *
     * @since 6.1.0
     *
     */
    $send_notification_to_user = apply_filters('wp_send_new_user_notification_to_user', true, $user);

    // `$deprecated` was pre-4.3 `$plaintext_pass`. An empty `$plaintext_pass` didn't sent a user notification.
    if ('admin' === $notify || true !== $send_notification_to_user || (empty($deprecated) && empty($notify))) {
        return;
    }

    $key = get_password_reset_key($user);
    if (is_wp_error($key)) {
        return;
    }

    $switched_locale = switch_to_locale(get_user_locale($user));

    /* translators: %s: User login. */
    $message = sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
    $message .= __('To set your password, visit the following address:') . "\r\n\r\n";
    $message .= network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . "\r\n\r\n";

    $message .= wp_login_url() . "\r\n";

    $wp_new_user_notification_email = array(
        'to'      => $user->user_email,
        /* translators: Login details notification email subject. %s: Site title. */
        'subject' => __('[%s] Login Details'),
        'message' => $message,
        'headers' => '',
    );

    /**
     * Filters the contents of the new user notification email sent to the new user.
     *
     * @param array $wp_new_user_notification_email {
     *     Used to build wp_mail().
     *
     * @type string $to The intended recipient - New user email address.
     * @type string $subject The subject of the email.
     * @type string $message The body of the email.
     * @type string $headers The headers of the email.
     * }
     *
     * @param WP_User $user User object for new user.
     * @param string $blogname The site title.
     *
     * @since 4.9.0
     *
     */
    $wp_new_user_notification_email = apply_filters('wp_new_user_notification_email', $wp_new_user_notification_email, $user, $blogname);

    wp_mail(
        $wp_new_user_notification_email['to'],
        wp_specialchars_decode(sprintf($wp_new_user_notification_email['subject'], $blogname)),
        $wp_new_user_notification_email['message'],
        $wp_new_user_notification_email['headers']
    );

    if ($switched_locale) {
        restore_previous_locale();
    }
}

if ( ! function_exists('wp_new_user_notification')) :
    /**
     * Email login credentials to a newly-registered user.
     *
     * A new user registration notification is also sent to admin email.
     *
     * @param int $user_id User ID.
     * @param null $deprecated Not used (argument deprecated).
     * @param string $notify Optional. Type of notification that should happen. Accepts 'admin' or an empty
     *                           string (admin only), 'user', or 'both' (admin and user). Default empty.
     *
     * @since 4.6.0 The `$notify` parameter accepts 'user' for sending notification only to the user created.
     *
     * @global wpdb $wpdb WordPress database object for queries.
     * @global PasswordHash $wp_hasher Portable PHP password hashing framework instance.
     *
     * @since 2.0.0
     * @since 4.3.0 The `$plaintext_pass` parameter was changed to `$notify`.
     * @since 4.3.1 The `$plaintext_pass` parameter was deprecated. `$notify` added as a third parameter.
     */
    function wp_new_user_notification($user_id, $deprecated = null, $notify = '')
    {
        return ppress_wp_new_user_notification($user_id, $deprecated, $notify);
    }
endif;

/**
 * Does registration form has username requirement disabled?
 *
 * @param int $form_id
 * @param bool $is_melange
 *
 * @return bool
 */
function ppress_is_signup_form_username_disabled($form_id, $is_melange = false)
{
    $result = FR::get_form_meta($form_id, FR::REGISTRATION_TYPE, FR::DISABLE_USERNAME_REQUIREMENT);

    if ($is_melange === true) {
        $result = FR::get_form_meta($form_id, FR::MELANGE_TYPE, FR::DISABLE_USERNAME_REQUIREMENT);
    }

    if (is_string($result)) {
        $result = $result == 'true' ? true : false;
    }

    return $result;
}

/**
 * Generate url to reset user's password.
 *
 * @param string $user_login
 *
 * @return string
 */
function ppress_generate_password_reset_url($user_login)
{
    $user = get_user_by('login', $user_login);

    $key = get_password_reset_key($user);

    if (is_wp_error($key)) {
        ppress_log_error($key->get_error_message());

        return '';
    }

    return ppress_get_do_password_reset_url($user_login, $key);
}

function ppress_nonce_action_string()
{
    return 'pp_plugin_nonce';
}

/**
 * Return array of countries.
 *
 * @param string $country_code
 *
 * @return mixed|string
 */
function ppress_array_of_world_countries($country_code = '')
{
    $list = apply_filters('ppress_countries_list', include(PROFILEPRESS_SRC . 'Functions/data/countries.php'));

    if ( ! empty($country_code)) {
        return ppress_var($list, $country_code);
    }

    return $list;
}

/**
 * @param $country
 *
 * @return mixed
 */
function ppress_array_of_world_states($country = '')
{
    $states = apply_filters('ppress_countries_states_list', include(PROFILEPRESS_SRC . 'Functions/data/states.php'));

    if ( ! empty($country)) {
        return ppress_var($states, $country, []);
    }

    return $states;
}

function ppress_get_country_title($country)
{
    if ( ! empty($country)) {

        $val = ppress_array_of_world_countries($country);

        if ( ! empty($val)) return $val;
    }

    return $country;
}

function ppress_get_country_state_title($state, $country)
{
    return ppress_var(ppress_array_of_world_states($country), $state, $state, true);
}

function ppress_create_nonce()
{
    return wp_create_nonce(ppress_nonce_action_string());
}

function ppress_nonce_field()
{
    return wp_nonce_field(ppress_nonce_action_string(), '_wpnonce', true, false);
}

function ppress_verify_nonce()
{
    return check_admin_referer(ppress_nonce_action_string());
}

function ppress_verify_ajax_nonce()
{
    return check_ajax_referer(ppress_nonce_action_string());
}

/**
 * Returns a more compact md5 hashing.
 *
 * @param $string
 *
 * @return false|string
 */
function ppress_md5($string)
{
    return substr(base_convert(md5($string), 16, 32), 0, 12);
}

/**
 * Generate unique ID
 *
 * @param int $length
 *
 * @return string
 */
function ppress_generate_unique_id($length = 10)
{
    $characters       = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
    }

    return ppress_md5(time() . $randomString);
}

function ppress_minify_css($buffer)
{
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    $buffer = str_replace(': ', ':', $buffer);
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);

    return $buffer;
}

function ppress_minify_js($code)
{
    // make it into one long line
    $code = str_replace(array("\n", "\r"), '', $code);
    // replace all multiple spaces by one space
    $code = preg_replace('!\s+!', ' ', $code);
    // replace some unneeded spaces, modify as needed
    $code = str_replace(array(' {', ' }', '{ ', '; '), array('{', '}', '{', ';'), $code);

    return $code;
}

function ppress_minify_html($html)
{
    $lines = explode(PHP_EOL, $html);
    array_walk($lines, function (&$line) {
        $line = trim($line);
    });

    $lines = array_filter($lines, function ($line) {
        return $line !== '';
    });

    return implode(PHP_EOL, $lines);
}

function ppress_get_ip_address()
{
    $user_ip = '127.0.0.1';

    $keys = array(
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR',
    );

    foreach ($keys as $key) {
        // Bail if the key doesn't exists.
        if ( ! isset($_SERVER[$key])) {
            continue;
        }

        if ($key == 'HTTP_X_FORWARDED_FOR' && ! empty($_SERVER[$key])) {
            //to check ip is pass from proxy
            // can include more than 1 ip, first is the public one
            $_SERVER[$key] = explode(',', $_SERVER[$key]);
            $_SERVER[$key] = $_SERVER[$key][0];
        }

        // Bail if the IP is not valid.
        if ( ! filter_var(wp_unslash(trim($_SERVER[$key])), FILTER_VALIDATE_IP)) {
            continue;
        }

        $user_ip = str_replace('::1', '127.0.0.1', $_SERVER[$key]);
    }

    return apply_filters('ppress_get_ip', $user_ip);
}

/**
 * Admin email address to receive admin notification.
 *
 * @return mixed
 */
function ppress_get_admin_notification_emails()
{
    return ppress_get_setting('admin_email_addresses', ppress_admin_email(), true);
}

/**
 * @return WP_Filesystem_Base|false
 */
function ppress_file_system()
{
    global $wp_filesystem;

    require_once ABSPATH . 'wp-admin/includes/file.php';

    // If for some reason the include doesn't work as expected just return false.
    if ( ! function_exists('\WP_Filesystem')) {
        return false;
    }

    $writable = WP_Filesystem(false, '', true);

    // We consider the directory as writable if it uses the direct transport,
    // otherwise credentials would be needed.
    return ($writable && 'direct' === $wp_filesystem->method) ? $wp_filesystem : false;
}

function ppress_get_file($file)
{
    $content = '';

    $fs = ppress_file_system();

    if ($fs && $fs->exists($file)) {
        $content = $fs->get_contents($file);
    }

    return $content;
}

function ppress_get_error_log($type = 'debug')
{
    $file_token = get_option('ppress_debug_log_token');

    $log_file = PPRESS_ERROR_LOG_FOLDER . "{$type}-{$file_token}.log";

    $file_contents = '';

    if (file_exists($log_file)) {
        $file_contents = @file_get_contents($log_file);
    }

    return $file_contents;
}

function ppress_log_error($message, $type = 'debug')
{
    if (is_array($message)) wp_json_encode($message);

    $log_folder = PPRESS_ERROR_LOG_FOLDER;

    // does bugs folder exist? if NO, create it.
    if ( ! file_exists($log_folder)) {
        mkdir($log_folder, 0755, true);
    }

    if ( ! file_exists(PPRESS_ERROR_LOG_FOLDER . '/index.php')) {
        ppress_create_index_file(PPRESS_ERROR_LOG_FOLDER);
    }

    //
    $fs = ppress_file_system();

    $file_token = get_option('ppress_debug_log_token');
    if (false === $file_token) {
        $file_token = uniqid(wp_rand(), true);
        update_option('ppress_debug_log_token', $file_token);
    }

    $filename = "{$type}-{$file_token}.log"; // ex. debug-5c2f6a9b9b5a3.log.
    $file     = $log_folder . $filename;
    $old_file = $log_folder . "{$type}.log";

    // If old file exists, move it.
    if ($fs && $fs->exists($old_file)) {
        $old_content = ppress_get_file($old_file);
        $fs->put_contents($file, $old_content, FS_CHMOD_FILE);

        // Move old file to new location.
        $fs->move($old_file, $file);

        if ($fs->exists($old_file)) {
            $fs->delete($old_file);
        }
    }
    //

    $message = current_time('mysql') . ' - ' . $message . "\r\n\r\n";

    error_log($message, 3, "{$log_folder}{$filename}");
}

function ppress_clear_error_log($type = 'debug')
{
    $file_token = get_option('ppress_debug_log_token');
    @unlink(PPRESS_ERROR_LOG_FOLDER . "{$type}-{$file_token}.log");
    @unlink(PPRESS_ERROR_LOG_FOLDER . $type . '.log');
}

function ppressPOST_var($key, $default = false, $empty = false, $bucket = false)
{
    $bucket = ! $bucket ? $_POST : $bucket;

    if ($empty) {
        return ! empty($bucket[$key]) ? $bucket[$key] : $default;
    }

    return isset($bucket[$key]) ? $bucket[$key] : $default;
}

function ppressGET_var($key, $default = false, $empty = false)
{
    $bucket = $_GET;

    if ($empty) {
        return ! empty($bucket[$key]) ? $bucket[$key] : $default;
    }

    return $bucket[$key] ?? $default;
}

function ppress_var($bucket, $key, $default = false, $empty = false)
{
    if ($empty) {
        return isset($bucket[$key]) && ( ! empty($bucket[$key]) || ppress_is_boolean($bucket[$key]) || is_numeric($bucket[$key])) ? $bucket[$key] : $default;
    }

    return $bucket[$key] ?? $default;
}

function ppress_var_obj($bucket, $key, $default = false, $empty = false)
{
    if ($empty) {
        return isset($bucket->$key) && ( ! empty($bucket->$key) || ppress_is_boolean($bucket->$key)) ? $bucket->$key : $default;
    }

    return $bucket->$key ?? $default;
}

/**
 * Normalize unamed shortcode
 *
 * @param array $atts
 *
 * @return mixed
 */
function ppress_normalize_attributes($atts)
{
    if (is_array($atts)) {
        foreach ($atts as $key => $value) {
            if (is_int($key)) {
                $atts[$value] = true;
                unset($atts[$key]);
            }
        }
    }

    return $atts;
}

function ppress_dnd_field_key_description()
{
    return esc_html__('It must be unique for each field, not a reserve text, in lowercase letters only with an underscore ( _ ) separating words e.g job_title', 'wp-user-avatar');
}

function ppress_reserved_field_keys()
{
    return [
        'ID',
        'id',
        'user_pass',
        'user_login',
        'user_nicename',
        'user_url',
        'user_email',
        'display_name',
        'nickname',
        'first_name',
        'last_name',
        'description',
        'rich_editing',
        'syntax_highlighting',
        'comment_shortcuts',
        'admin_color',
        'use_ssl',
        'user_registered',
        'user_activation_key',
        'spam',
        'show_admin_bar_front',
        'role',
        'locale',
        'deleted',
        'user_level',
        'user_status',
        'user_description'
    ];
}

function ppress_is_boolean($maybe_bool)
{
    if (is_bool($maybe_bool)) {
        return true;
    }

    if (is_string($maybe_bool)) {
        $maybe_bool = strtolower($maybe_bool);

        $valid_boolean_values = array(
            'false',
            'true',
            '0',
            '1',
        );

        return in_array($maybe_bool, $valid_boolean_values, true);
    }

    if (is_int($maybe_bool)) {
        return in_array($maybe_bool, array(0, 1), true);
    }

    return false;
}

function ppress_filter_empty_array($values)
{
    if ( ! is_array($values)) return $values;

    return array_filter($values, function ($value) {
        return ppress_is_boolean($value) || is_int($value) || ! empty($value);
    });
}

/**
 * Check if HTTP status code is successful.
 *
 * @param int $code
 *
 * @return bool
 */
function ppress_is_http_code_success($code)
{
    $code = absint($code);

    return $code >= 200 && $code <= 299;
}

/**
 * Converts date/time which should be in UTC to timestamp.
 *
 * strtotime uses the default timezone set in PHP which may or may not be UTC.
 *
 * @param $time
 *
 * @return false|int
 */
function ppress_strtotime_utc($time)
{
    return strtotime($time . ' UTC');
}

function ppress_array_flatten($array)
{
    if ( ! is_array($array)) {
        return false;
    }
    $result = array();
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            // we are not doing array_merge here because we wanna keep array keys.
            // PS: The + operator is not an addition, it's a union. If the keys don't overlap then all is good.
            $result = $result + ppress_array_flatten($value);
        } else {
            $result[$key] = $value;
        }
    }

    return $result;
}

/**
 * Sanitizes a string key.
 *
 * Keys are used as internal identifiers. Lowercase alphanumeric characters and underscores are allowed.
 *
 * @param string $key String key
 *
 * @return string Sanitized key
 */
function ppress_sanitize_key($key)
{
    return str_replace('-', '_', sanitize_key($key));
}

function ppress_woocommerce_field_transform($cf_key, $cf_value)
{
    if (class_exists('WooCommerce')) {

        if (in_array($cf_key, ppress_woocommerce_billing_fields())) {
            $cf_value = sprintf(esc_html__('%s (WooCommerce Billing Address)', 'wp-user-avatar'), $cf_value);
        }

        if (in_array($cf_key, ppress_woocommerce_shipping_fields())) {
            $cf_value = sprintf(esc_html__('%s (WooCommerce Shipping Address)', 'wp-user-avatar'), $cf_value);
        }
    }

    return $cf_value;
}

function ppress_custom_fields_key_value_pair($remove_default = false)
{
    $defined_custom_fields = [];

    if ($remove_default === false) {
        $defined_custom_fields[''] = esc_html__('Select...', 'wp-user-avatar');
    }

    foreach (CheckoutFields::standard_billing_fields() as $key => $field) {
        $defined_custom_fields[$key] = $field['label'];
    }

    if (EM::is_premium()) {
        $db_custom_fields = PROFILEPRESS_sql::get_profile_custom_fields();
        $db_contact_infos = PROFILEPRESS_sql::get_contact_info_fields();

        if ( ! empty($db_contact_infos)) {
            foreach ($db_contact_infos as $key => $value) {
                $defined_custom_fields[$key] = $value;
            }
        }

        if ( ! empty($db_custom_fields)) {
            foreach ($db_custom_fields as $db_custom_field) {
                $field_key                         = $db_custom_field['field_key'];
                $defined_custom_fields[$field_key] = ppress_woocommerce_field_transform($field_key, $db_custom_field['label_name']);
            }
        }
    }

    return $defined_custom_fields;
}

function ppress_standard_fields_key_value_pair($remove_default = false)
{
    $fields = [];
    if ($remove_default === false) {
        $fields[''] = esc_html__('Select...', 'wp-user-avatar');
    }

    return array_merge($fields, [
        'first_last_names'  => esc_html__('First and Last Names', 'wp-user-avatar'),
        'last_first_names'  => esc_html__('Last and First Names', 'wp-user-avatar'),
        'username'          => esc_html__('Username', 'wp-user-avatar'),
        'first-name'        => esc_html__('First Name', 'wp-user-avatar'),
        'last-name'         => esc_html__('Last Name', 'wp-user-avatar'),
        'nickname'          => esc_html__('Nickname', 'wp-user-avatar'),
        'display-name'      => esc_html__('Display Name', 'wp-user-avatar'),
        'email'             => esc_html__('Email Address', 'wp-user-avatar'),
        'bio'               => esc_html__('Biography', 'wp-user-avatar'),
        'registration_date' => esc_html__('Registration Date', 'wp-user-avatar'),
    ]);
}

function ppress_standard_custom_fields_key_value_pair($remove_default = false)
{
    $fields = [];

    if ($remove_default === false) {
        $fields[''] = esc_html__('Select...', 'wp-user-avatar');
    }

    $fields[esc_html__('Standard Fields', 'wp-user-avatar')] = ppress_standard_fields_key_value_pair(true);

    if (EM::is_enabled(EM::CUSTOM_FIELDS)) {
        $fields[esc_html__('Custom Fields', 'wp-user-avatar')] = ppress_custom_fields_key_value_pair(true);
    }

    return $fields;
}

/**
 * @param int|bool $user_id
 *
 * @return bool
 */
function ppress_user_has_cover_image($user_id = false)
{
    $user_id = ! $user_id ? get_current_user_id() : $user_id;

    $cover = get_user_meta($user_id, 'pp_profile_cover_image', true);

    return ! empty($cover);
}


/**
 * @param int|bool $user_id
 *
 * @return string|bool
 */
function ppress_get_cover_image_url($user_id = false)
{
    $user_id = ! $user_id ? get_current_user_id() : $user_id;

    $slug = get_user_meta($user_id, 'pp_profile_cover_image', true);

    $url = ! empty($slug) ? PPRESS_COVER_IMAGE_UPLOAD_URL . "$slug" : get_option('wp_user_cover_default_image_url');

    return esc_url_raw($url);
}

function ppress_is_my_own_profile()
{
    global $ppress_frontend_profile_user_obj;

    return ppress_var_obj($ppress_frontend_profile_user_obj, 'ID') == get_current_user_id();
}

function ppress_is_my_account_page()
{
    return ppress_post_content_has_shortcode('profilepress-my-account');
}

function ppress_social_network_fields()
{
    return apply_filters('ppress_core_contact_info_fields', [
        Base::cif_facebook  => 'Facebook',
        Base::cif_twitter   => 'Twitter',
        Base::cif_linkedin  => 'LinkedIn',
        Base::cif_vk        => 'VK',
        Base::cif_youtube   => 'YouTube',
        Base::cif_instagram => 'Instagram',
        Base::cif_github    => 'GitHub',
        Base::cif_pinterest => 'Pinterest',
        Base::cif_bluesky   => 'Bluesky',
        Base::cif_threads   => 'Threads',
    ]);
}

function ppress_social_login_networks()
{
    return apply_filters('ppress_social_login_networks', [
        'facebook'     => 'Facebook',
        'twitter'      => 'X/Twitter',
        'google'       => 'Google',
        'linkedin'     => 'LinkedIn',
        'microsoft'    => 'Microsoft',
        'yahoo'        => 'Yahoo',
        'amazon'       => 'Amazon',
        'github'       => 'GitHub',
        'wordpresscom' => 'WordPress.com',
        'vk'           => 'VK.com'
    ]);
}

function ppress_mb_function($function_names, $args)
{
    $mb_function_name = $function_names[0];
    $function_name    = $function_names[1];
    if (function_exists($mb_function_name)) {
        $function_name = $mb_function_name;
    }

    return call_user_func_array($function_name, $args);
}

function ppress_recursive_trim($item)
{
    if (is_array($item)) {

        $sanitized_data = [];
        foreach ($item as $key => $value) {
            $sanitized_data[$key] = ppress_recursive_trim($value);
        }

        return $sanitized_data;
    }

    return trim($item);
}

function ppress_check_type_and_ext($file, $accepted_mime_types = [], $accepted_file_ext = [])
{

    if (empty($file_name)) {
        $file_name = $file['name'];
    }

    $tmp_name = $file['tmp_name'];

    $wp_filetype = wp_check_filetype_and_ext($tmp_name, $file_name);

    $ext             = $wp_filetype['ext'];
    $type            = $wp_filetype['type'];
    $proper_filename = $wp_filetype['proper_filename'];

    // When a proper_filename value exists, it could be a security issue if it's different than the original file name.
    if ($proper_filename && strtolower($proper_filename) !== strtolower($file_name)) {
        return new WP_Error('invalid_file', esc_html__('There was an problem while verifying your file.', 'wp-user-avatar'));
    }

    // If either $ext or $type are empty, WordPress doesn't like this file and we should bail.
    if ( ! $ext) {
        return new WP_Error('illegal_extension', esc_html__('Sorry, this file extension is not permitted for security reasons.', 'wp-user-avatar'));
    }

    if ( ! $type) {
        return new WP_Error('illegal_type', esc_html__('Sorry, this file type is not permitted for security reasons.', 'wp-user-avatar'));
    }

    if ( ! empty($accepted_mime_types) && ! in_array($type, $accepted_mime_types)) {
        return new WP_Error('illegal_type', esc_html__('Error: The file you uploaded is not accepted on our website.', 'wp-user-avatar'));
    }

    if ( ! empty($accepted_file_ext) && ! in_array($ext, $accepted_file_ext)) {
        return new WP_Error('illegal_type', esc_html__('Error: The file you uploaded is not accepted on our website.', 'wp-user-avatar'));
    }

    return true;
}

function ppress_decode_html_strip_tags($val)
{
    return strip_tags(html_entity_decode($val));
}

function ppress_content_http_redirect($myURL)
{
    ?>
    <script type="text/javascript">
        window.location.href = "<?php echo $myURL;?>"
    </script>
    <meta http-equiv="refresh" content="0; url=<?php echo $myURL; ?>">
    Please wait while you are redirected...or
    <a href="<?php echo $myURL; ?>">Click Here</a> if you do not want to wait.
    <?php
}

function ppress_do_admin_redirect($url)
{
    if ( ! headers_sent()) {
        wp_safe_redirect($url);
        exit;
    }

    ppress_content_http_redirect($url);
}

function ppress_is_json($str)
{
    $json = json_decode($str);

    return $json && $str != $json;
}

function ppress_clean($var, $callback = 'sanitize_textarea_field')
{
    if (is_array($var)) {
        return array_map('ppress_clean', $var);
    } else {
        return is_scalar($var) ? call_user_func($callback, $var) : $var;
    }
}

/**
 * @param $s
 *
 * @return bool
 * @see https://stackoverflow.com/a/23810738/2648410
 */
function ppress_is_base64($s)
{
    // Check if there are valid base64 characters
    if ( ! preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s)) return false;

    // Decode the string in strict mode and check the results
    $decoded = base64_decode($s, true);
    if (false === $decoded) return false;

    // Encode the string again
    if (base64_encode($decoded) != $s) return false;

    return true;
}

/**
 * @param int $plan_id Plan ID or Subscription ID if change plan URL
 * @param bool $is_change_plan set to true to return checkout url to change plan
 *
 * @return false|string
 */
function ppress_plan_checkout_url($plan_id, $is_change_plan = false)
{
    $page_id = ppress_settings_by_key('checkout_page_id');

    if ( ! empty($page_id)) {

        $cid = $is_change_plan ? 'change_plan' : 'plan';

        return add_query_arg($cid, absint($plan_id), get_permalink($page_id));
    }

    return false;
}

/**
 * Generate unique ID for each optin form.
 *
 * @param int $length
 *
 * @return string
 */
function ppress_generateUniqueId($length = 10)
{
    $characters       = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
    }

    return ppress_md5(time() . $randomString);
}

function ppress_render_view($template, $vars = [], $parentDir = '')
{
    if (empty($parentDir)) $parentDir = dirname(__FILE__, 2) . '/templates/';

    $path = $parentDir . $template . '.php';

    extract($vars);
    ob_start();
    require apply_filters('ppress_render_view', $path, $vars, $template, $parentDir);
    echo apply_filters('ppress_render_view_output', ob_get_clean(), $template, $vars, $parentDir);
}

function ppress_post_content_has_shortcode($tag = '', $post = null)
{
    if (is_null($post)) {
        global $post;
    }

    return is_singular() && is_a($post, 'WP_Post') && has_shortcode($post->post_content, $tag);
}

function ppress_maybe_define_constant($name, $value)
{
    if ( ! defined($name)) {
        define($name, $value);
    }
}

function ppress_upgrade_urls_affilify($url)
{
    return apply_filters('ppress_pro_upgrade_url', $url);
}

function ppress_cache_transform($cache_key, $callback)
{
    static $cache = [];

    // If the cache key does not exist, compute the value and cache it.
    if ( ! isset($cache[$cache_key])) {
        $cache[$cache_key] = $callback();
    }

    // Return the cached result.
    return $cache[$cache_key];
}

function ppress_form_has_field($form_id, $form_type, $field_shortcode_tag)
{
    if (FR::is_drag_drop($form_id, $form_type)) {

        $settings = FR::form_builder_fields_settings($form_id, $form_type);

        $found_field = wp_list_filter($settings, ['fieldType' => $field_shortcode_tag]);

        if ( ! empty($found_field)) return true;

    } else {

        $registration_structure = FR::get_form_meta($form_id, $form_type, FR::FORM_STRUCTURE);

        // find the first occurrence of reg-select-role shortcode.
        preg_match('/\[' . $field_shortcode_tag . '.*\]/', $registration_structure, $matches);

        if ( ! empty($matches[0])) return true;
    }

    return false;
}

/**
 * @param $message
 * @param WP_User $user
 *
 * @return array|mixed|string|string[]
 */
function ppress_custom_profile_field_search_replace($message, $user)
{
    // handle support for custom fields placeholder.
    preg_match_all('#({{[a-z_-]+}})#', $message, $matches);

    if ( ! empty($matches[1])) {

        foreach ($matches[1] as $match) {

            $key = str_replace(['{', '}'], '', $match);

            $value = '';

            $field_type = PROFILEPRESS_sql::get_field_type($key);

            if ($field_type == 'file') {
                $value = FrontendProfileBuilder::get_user_uploaded_file($user->ID, $key);
            } elseif (isset($user->{$key})) {

                $value = $user->{$key};

                if (is_array($value)) $value = implode(', ', $value);
            }

            $message = str_replace($match, $value, $message);
        }
    }

    return $message;
}
