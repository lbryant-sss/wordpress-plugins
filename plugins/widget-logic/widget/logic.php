<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

include_once 'logic/tokenizer.php';
include_once 'logic/parser.php';

/**
 * Main function to check widget logic expressions
 */
function widget_logic_check_logic($logic)
{
    $allowed_functions = array(
        'is_home', 'is_front_page', 'is_single', 'is_page', 'is_category',
        'is_tag', 'is_archive', 'is_search', 'is_404', 'is_user_logged_in',
        'current_user_can', 'is_active_sidebar', 'is_admin',
    );

    $allowed_functions = apply_filters('widget_logic_allowed_functions', $allowed_functions);

    $logic = trim((string) $logic);
    if ('' === $logic) {
        return true;
    }

    // Set up error handling
    set_error_handler('widget_logic_error_handler', E_WARNING | E_USER_WARNING);  // @codingStandardsIgnoreLine - we need this for error handling

    try {
        // Tokenize the logic string
        $tokens = widget_logic_tokenize($logic);

        // Parse and evaluate the expression
        $pos = 0;
        $result = widget_logic_parse_expression($tokens, $pos, $allowed_functions);

        // Check if there are any unexpected tokens after the expression
        if ($pos < count($tokens)) {
            throw new Exception(esc_html__('Widget Logic: Unexpected tokens after expression.', 'widget-logic'));
        }

        return (bool)$result;
    } catch (Exception $e) {
        widget_logic_error_handler(E_USER_WARNING, $e->getMessage());
        return false;
    } finally {
        restore_error_handler();
    }
}

/**
 * Generic error handler for widget logic
 */
function widget_logic_error_handler($errno, $errstr)
{
    global $wl_options;

    // For testing, we want to see all errors
    $show_errors = true;

    // In normal operation, respect user settings
    if (!defined('WIDGET_LOGIC_TESTING')) {
        $show_errors = !empty($wl_options['widget_logic-options-show_errors']) && current_user_can('manage_options');
    }

    if ($show_errors) {
        echo 'Invalid Widget Logic: ' . esc_html($errstr);
    }

    return true;
}

function widget_logic_by_id($widget_id)
{
    global $wl_options;

    if (preg_match('/^(.+)-(\d+)$/', $widget_id, $m)) {
        $widget_class = $m[1];
        $widget_i     = $m[2];

        $info = get_option('widget_' . $widget_class);
        if (empty($info[$widget_i])) {
            return '';
        }

        $info = $info[$widget_i];
    } else {
        $info = (array) get_option('widget_' . $widget_id, array());
    }

    if (isset($info['widget_logic'])) {
        $logic = $info['widget_logic'];
    } elseif (isset($wl_options[$widget_id])) {
        $logic = stripslashes($wl_options[$widget_id]);
        widget_logic_save($widget_id, $logic);

        unset($wl_options[$widget_id]);
        update_option('widget_logic', $wl_options);
    } else {
        $logic = '';
    }

    return $logic;
}

function widget_logic_save($widget_id, $logic)
{
    global $wl_options;

    if (preg_match('/^(.+)-(\d+)$/', $widget_id, $m)) {
        $widget_class = $m[1];
        $widget_i     = $m[2];

        $info = get_option('widget_' . $widget_class);
        if (!is_array($info[$widget_i])) {
            $info[$widget_i] = array();
        }

        $info[$widget_i]['widget_logic'] = $logic;
        update_option('widget_' . $widget_class, $info);
    } elseif (
        isset($_POST['widget_logic_nonce'])
        && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['widget_logic_nonce'])), 'widget_logic_save')
    ) {
        $info                 = (array) get_option('widget_' . $widget_id, array());
        $info['widget_logic'] = $logic;
        update_option('widget_' . $widget_id, $info);
    }
}
