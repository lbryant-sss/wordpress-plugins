<?php

defined('ABSPATH') || exit;

class NewsletterComposerAdmin extends NewsletterModuleAdmin {

    static $instance;

    /**
     * @return NewsletterComposerAdmin
     */
    static function instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function __construct() {
        parent::__construct('composer');
    }

    function wp_loaded() {
        if (defined('DOING_AJAX') && DOING_AJAX && $this->is_allowed()) {
            add_action('wp_ajax_tnpc_options', array($this, 'ajax_tnpc_options'));
            add_action('wp_ajax_tnpc_get_all_presets', array($this, 'ajax_get_all_presets'));
            add_action('wp_ajax_tnpc_get_preset', array($this, 'ajax_get_preset'));
            add_action('wp_ajax_tnpc_render', array($this, 'ajax_tnpc_render'));
            add_action('wp_ajax_tnpc_test', array($this, 'ajax_tnpc_test'));
            add_action('wp_ajax_tnpc_preview', array($this, 'ajax_tnpc_preview'));
            add_action('wp_ajax_tnpc_css', array($this, 'ajax_tnpc_css'));
            add_action('wp_ajax_tnpc_regenerate_email', array($this, 'ajax_tnpc_regenerate_email'));
            add_action('wp_ajax_tnpc_block_form', array($this, 'ajax_tnpc_block_form'));
        }
    }

    /**
     * Builds the HTML of a block configuration form.
     *
     * @param array $block
     * @return string
     */
    function get_block_form($block) {

        require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
        $encoded_options = wp_unslash($_REQUEST['options']);
        $options = NewsletterComposer::options_decode($encoded_options);

        $defaults_file = $block['dir'] . '/defaults.php';
        if (file_exists($defaults_file)) {
            include $defaults_file;
        }

        if (!isset($defaults) || !is_array($defaults)) {
            $defaults = [];
        }

        $options = array_merge($defaults, $options);

        $composer = wp_unslash($_POST['composer'] ?? []);

        if (empty($composer['width'])) {
            $composer['width'] = 600;
        }

        $context = ['type' => sanitize_key($_REQUEST['context_type'] ?? '')];

        // Used by the options.php script
        $controls = new NewsletterControls($options);
        $fields = new NewsletterFields($controls);

        ob_start();
        $controls->init(['nested' => true]); // To avoid conflict with master form...
        echo '<input type="hidden" name="action" value="tnpc_render">';
        echo '<input type="hidden" name="id" value="' . esc_attr($block['id']) . '">';
        echo '<input type="hidden" name="context_type" value="' . esc_attr($context['type']) . '">';
        wp_nonce_field('save');
        $inline_edits = '';
        if (isset($controls->data['inline_edits'])) {
            $inline_edits = $controls->data['inline_edits'];
        }
        echo '<input type="hidden" name="options[inline_edits]" value="', esc_attr(NewsletterComposer::options_encode($inline_edits)), '">';
        include $block['dir'] . '/options.php';
        $html = ob_get_clean();
        return $html;
    }

    /**
     * Used by Composer 3
     */
    function ajax_tnpc_block_form() {
        $block_id = sanitize_key($_REQUEST['id']);
        $block = NewsletterComposer::instance()->get_block($block_id);
        if (!$block) {
            die('Block not found with id ' . $block_id);
        }
        $data = ['form' => $this->get_block_form($block), 'title' => $block['name']];
        header('Content-Type: application/json;charset=UTF-8');
        echo wp_json_encode($data);
        die();
    }

    function ajax_tnpc_test() {
        check_admin_referer('save');
        if (!$this->is_allowed()) {
            wp_send_json_error('Not allowed', 403);
        }

        if (!class_exists('NewsletterControls')) {
            include NEWSLETTER_INCLUDES_DIR . '/controls.php';
        }

        $controls = new NewsletterControls();
        $email = new TNP_Email();
        NewsletterComposer::update_email($email, $controls);

        if (isset($_POST['to_email'])) {
            $message = NewsletterEmailsAdmin::instance()->send_test_newsletter_to_email_address($email, $controls->data['test_email']);
            echo $message;
        } else {
            NewsletterEmailsAdmin::instance()->send_test_email($email, $controls);
            if ($controls->messages) {
                echo $controls->messages;
            } else {
                echo $controls->errors;
            }
        }

        die();
    }

    /**
     * Loads the options.php file of a block and outputs the generated form.
     *
     * @global wpdb $wpdb
     */
    function ajax_tnpc_options() {
        global $wpdb;
        $block_id = sanitize_key($_REQUEST['id']);
        $block = NewsletterComposer::instance()->get_block($block_id);
        if (!$block) {
            die('Block not found with id ' . $block_id);
        }

        require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
        $encoded_options = wp_unslash($_REQUEST['options']);
        $options = NewsletterComposer::options_decode($encoded_options);

        $defaults_file = $block['dir'] . '/defaults.php';
        if (file_exists($defaults_file)) {
            include $defaults_file;
        }

        if (!isset($defaults) || !is_array($defaults)) {
            $defaults = [];
        }

        $options = array_merge($defaults, $options);

        $composer = wp_unslash($_POST['composer'] ?? []);

        if (empty($composer['width'])) {
            $composer['width'] = 600;
        }

        $context = ['type' => sanitize_key($_REQUEST['context_type'] ?? '')];

        // Used by the options.php script
        $controls = new NewsletterControls($options);
        $fields = new NewsletterFields($controls);

        $controls->init(['nested' => true]); // To avoid conflict with master form...
        echo '<input type="hidden" name="action" value="tnpc_render">';
        echo '<input type="hidden" name="id" value="' . esc_attr($block_id) . '">';
        echo '<input type="hidden" name="context_type" value="' . esc_attr($context['type']) . '">';
        wp_nonce_field('save');
        $inline_edits = '';
        if (isset($controls->data['inline_edits'])) {
            $inline_edits = $controls->data['inline_edits'];
        }
        echo '<input type="hidden" name="options[inline_edits]" value="', esc_attr(NewsletterComposer::options_encode($inline_edits)), '">';
        echo "<h3>", esc_html($block["name"]), "</h3>";
        include $block['dir'] . '/options.php';
        wp_die();
    }

    /**
     * Retrieves the presets list (no id in GET) or a specific preset id in GET)
     */
    function ajax_get_all_presets() {
        wp_send_json_success($this->get_all_preset());
    }

    function ajax_get_preset() {
        $id = sanitize_key($_REQUEST['id']);
        $email = null;

        // If it is an email id, get it from the database and fall back to the
        // static templates if not found (maybe a template folder has been created
        // just using a number...). I don't like this.
        if (is_numeric($id)) {
            $email = $this->get_email($id);
            if ($email) {
                NewsletterComposer::instance()->regenerate($email);
            }
        }

        if (!$email) {
            $email = NewsletterComposer::instance()->build_email_from_template($id);
        }

        if (is_wp_error($email)) {
            wp_send_json_error($email);
        }

        // Send back and keep only the blocks' HTML
        wp_send_json_success([
            'content' => NewsletterComposer::extract_body($email),
            'globalOptions' => NewsletterComposer::extract_composer_options($email),
            'subject' => $email->subject
        ]);
    }

    function ajax_tnpc_preview() {
        $email = $this->get_email((int) $_REQUEST['id']);

        echo $email->message;

        die();
    }

    function ajax_tnpc_css() {
        include NEWSLETTER_DIR . '/emails/tnp-composer/css/newsletter.css';
        wp_die();
    }

    /**
     * Ajax call to render a block with a new set of options after the settings popup
     * has been saved.
     *
     * @param type $block_id
     * @param type $wrapper
     */
    function ajax_tnpc_render() {
        if (!check_ajax_referer('save')) {
            wp_die('Invalid nonce', 403);
        }

        $block_id = sanitize_key($_POST['id']);
        $wrapper = isset($_POST['full']);
        $options = $this->restore_options_from_request();
        $composer = wp_unslash($_POST['composer'] ?? []);
        $context = ['type' => sanitize_key($_REQUEST['context_type'] ?? '')];
        NewsletterComposer::instance()->render_block($block_id, $wrapper, $options, $context, $composer);
        die();
    }

    function restore_options_from_request() {

        require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
        $controls = new NewsletterControls();
        $options = $controls->data;

        if (isset($_POST['options']) && is_array($_POST['options'])) {
            // Get all block options
            //$options = stripslashes_deep($_POST['options']);
            // Deserialize inline edits when
            // render is preformed on saving block options
            if (isset($options['inline_edits']) && !is_array($options['inline_edits'])) {
                $options['inline_edits'] = NewsletterComposer::options_decode($options['inline_edits']);
            }

            // Restore inline edits from data-json
            // coming from inline editing
            // and merge with current inline edit
            if (isset($_POST['encoded_options'])) {
                $decoded_options = NewsletterComposer::options_decode($_POST['encoded_options']);

                $to_merge_inline_edits = [];

                if (isset($decoded_options['inline_edits'])) {
                    foreach ($decoded_options['inline_edits'] as $decoded_inline_edit) {
                        $to_merge_inline_edits[$decoded_inline_edit['post_id'] . $decoded_inline_edit['type']] = $decoded_inline_edit;
                    }
                }

                //Overwrite with new edited content
                if (isset($options['inline_edits'])) {
                    foreach ($options['inline_edits'] as $inline_edit) {
                        $to_merge_inline_edits[$inline_edit['post_id'] . $inline_edit['type']] = $inline_edit;
                    }
                }

                $options['inline_edits'] = array_values($to_merge_inline_edits);
                $options = array_merge($decoded_options, $options);
            }

            return $options;
        }

        return [];
    }

    function ajax_tnpc_regenerate_email() {

        if (!check_ajax_referer('save')) {
            wp_die('Invalid nonce', 403);
        }

        $content = stripslashes($_POST['content']);
        $content = urldecode(base64_decode($content));
        $composer = stripslashes_deep($_POST['composer']);

        $result = NewsletterComposer::instance()->regenerate_blocks($content, [], $composer);

        wp_send_json_success([
            'content' => $result['content'],
            'message' => __('Successfully updated', 'newsletter')
        ]);
    }

    private function is_normal_context_request() {
        return empty($_REQUEST['context_type']);
    }

    /**
     * Generated the HTML with the preset lists for the modal used in the composer to create a new
     * email.
     *
     * TODO: move out as modal to be included, it's simpler
     *
     * @return string The presets selection HTML
     */
    function get_all_preset() {

        $content = "<div class='tnpc-preset-container'>";

        if ($this->is_normal_context_request()) {
            $content .= "<div class='tnpc-preset-legacy-themes'><a href='?page=newsletter_emails_theme'>" . __('Looking for legacy themes?', 'newsletter') . "</a></div>";
        }

        // LOAD USER PRESETS
        $user_preset_list = $this->get_emails(NewsletterEmails::PRESET_EMAIL_TYPE);

        if ($user_preset_list) {

            $content .= '<h3>Custom templates</h3>';

            $content .= '<div class="tnpc-preset-block">';

            foreach ($user_preset_list as $user_preset) {

                $default_icon_url = plugins_url('newsletter') . "/emails/presets/default-icon.png?ver=2";
                $preset_name = $user_preset->subject;

                // esc_js() assumes the string will be in single quote (arghhh!!!)
                $onclick_load = 'tnpc_load_preset(' . ((int) $user_preset->id) . ', \'' . esc_js($preset_name) . '\', event)';

                $content .= "<div class='tnpc-preset' onclick='" . esc_attr($onclick_load) . "'>\n";
                $content .= "<img src='$default_icon_url' title='" . esc_attr($preset_name) . "' alt='" . esc_attr($preset_name) . "'>\n";
                $content .= "<span class='tnpc-preset-label'>" . esc_html($user_preset->subject) . "</span>\n";
                $content .= "</div>";
            }
            $content .= '</div>';
        }

        $content .= '<h3>Standard templates</h3>';

        $content .= '<div class="tnpc-preset-block">';

        $templates = NewsletterComposer::instance()->get_templates();

        foreach ($templates as $template) {
            $type = $template->type ?? '';
            if ($type === 'activation' || $type === 'welcome') {
                continue;
            }
            $content .= '<div class="tnpc-preset tnpc-preset2" onclick="tnpc_load_preset(\'' . esc_attr($template->id) . '\')">';
            $content .= '<img src="' . esc_attr($template->icon) . '" title="' . esc_attr($template->name) . '" alt="' . esc_attr($template->name) . '">';
            $content .= '<span class="tnpc-preset-label">' . esc_html($template->name) . '</span>';
            $content .= '</div>';
        }

        if ($this->is_normal_context_request()) {
            $content .= $this->get_automated_spot_element();
            $content .= $this->get_autoresponder_spot_element();
            $content .= $this->get_raw_html_preset_element();
        }
        $content .= '</div>';

        $content .= '<h3>Welcome and Confirmation templates</h3>';

        $content .= '<div class="tnpc-preset-block">';
        foreach ($templates as $template) {
            $type = $template->type ?? '';
            if ($type !== 'confirmation' && $type !== 'welcome') {
                continue;
            }
            $content .= '<div class="tnpc-preset tnpc-preset2" onclick="tnpc_load_preset(\'' . esc_attr($template->id) . '\')">';
            $content .= '<img src="' . esc_attr($template->icon) . '" title="' . esc_attr($template->name) . '" alt="' . esc_attr($template->name) . '">';
            $content .= '<span class="tnpc-preset-label">' . esc_html($template->name) . '</span>';
            $content .= '</div>';
        }

        $content .= '</div>';
        $content .= '</div>';

        return $content;
    }

    private function get_automated_spot_element() {
        $result = "<div class='tnpc-preset'>";
        if (class_exists('NewsletterAutomated')) {
            $result .= "<a href='?page=newsletter_automated_index'>";
        } else {
            $result .= "<a href='https://www.thenewsletterplugin.com/automated?utm_source=composer&utm_campaign=plugin&utm_medium=automated'>";
        }
        $result .= "<img src='" . plugins_url('newsletter') . "/emails/images/automated.png' title='Automated addon' alt='Automated'/>";
        $result .= "<span class='tnpc-preset-label'>Daily, weekly and monthly newsletters</span></a>";
        $result .= "</div>";

        return $result;
    }

    private function get_autoresponder_spot_element() {
        $result = "<div class='tnpc-preset'>";
        if (class_exists('NewsletterAutoresponder')) {
            $result .= "<a href='?page=newsletter_autoresponder_index'>";
        } else {
            $result .= "<a href='https://www.thenewsletterplugin.com/autoresponder?utm_source=composer&utm_campaign=plugin&utm_medium=autoresponder' target='_blank'>";
        }
        $result .= "<img src='" . plugins_url('newsletter') . "/emails/images/autoresponder.png' title='Autoresponder addon' alt='Autoresponder'/>";
        $result .= "<span class='tnpc-preset-label'>Autoresponders</span></a>";
        $result .= "</div>";

        return $result;
    }

    private function get_raw_html_preset_element() {

        $result = "<div class='tnpc-preset tnpc-preset-html' onclick='location.href=\"" . wp_nonce_url('admin.php?page=newsletter_emails_new&id=rawhtml', 'newsletter-new') . "\"'>";
        $result .= "<img src='" . plugins_url('newsletter') . "/emails/images/rawhtml.png' title='RAW HTML' alt='RAW'/>";
        $result .= "<span class='tnpc-preset-label'>Raw HTML</span>";
        $result .= "</div>";

        $result .= "<div class='clear'></div>";
        $result .= "</div>";

        return $result;
    }
}
