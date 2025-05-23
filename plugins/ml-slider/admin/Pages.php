<?php

if (!defined('ABSPATH')) {
    die('No direct access.');
}

/**
 * Entry point for building the wordpress admin pages.
 * Temporarily extends the MetaSlider Class until more refactoring can be done.
 */
class MetaSlider_Admin_Pages extends MetaSliderPlugin
{
    /**
     * The MetaSlider plugin class
     *
     * @var object $plugin
     */
    private $plugin;

    /**
     * The current admin page
     *
     * @var string $current_page
     */
    private $current_page;

    /**
     * @var \MetaSlider_Notices
     */
    protected $notices;

    /**
     * Sets up the notices, security and loads assets for the admin page
     *
     * @param array $plugin Plugin details
     */
    public function __construct($plugin)
    {
        $this->plugin = $plugin;
        add_action('init', function() {
            $this->notices = new MetaSlider_Notices($this->plugin);
        });
        add_action('admin_enqueue_scripts', array($this, 'load_upgrade_page_assets'));
    }

    /**
     * Loads in the overlay assets
     */
    public function load_overlay()
    {
        wp_enqueue_style('metaslider-colorbox-styles', METASLIDER_ADMIN_URL . 'assets/vendor/colorbox/colorbox.css', false, METASLIDER_ASSETS_VERSION);
        wp_enqueue_script('metaslider-colorbox', METASLIDER_ADMIN_URL . 'assets/vendor/colorbox/jquery.colorbox-min.js', array('jquery'), METASLIDER_ASSETS_VERSION, true);
    }

    /**
     * Loads in tooltips
     */
    public function load_tooltips()
    {
        wp_enqueue_style('metaslider-tipsy-styles', METASLIDER_ADMIN_URL . 'assets/vendor/tipsy/tipsy.css', false, METASLIDER_ASSETS_VERSION);
        wp_enqueue_script('metaslider-tipsy', METASLIDER_ADMIN_URL . 'assets/vendor/tipsy/jquery.tipsy.js', array('jquery'), METASLIDER_ASSETS_VERSION, true);
    }

    /**
     * Loads TinyMCE
     * 
     * @since 3.62
     * 
     * @return void
     */
    public function load_wysiwyg()
    {
        $global_settings = $this->get_global_settings();

        if (! isset($global_settings['tinyMce']) 
            || ( isset($global_settings['tinyMce'] ) && true == $global_settings['tinyMce'])
        ) {
            wp_enqueue_script(
                'metaslider-tinymce-script',
                METASLIDER_ADMIN_URL . 'assets/vendor/tinymce/js/tinymce/tinymce.min.js',
                array(),
                METASLIDER_ASSETS_VERSION
            );
        }
    }

    /**
     * Loads in custom javascript
     */
    public function load_javascript()
    {
        wp_enqueue_media();
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_style( 'wp-color-picker' );
        wp_register_script(
            'metaslider-admin-script', 
            METASLIDER_ADMIN_URL . 'assets/dist/js/admin.js', 
            array('jquery', 'plupload-all', 'metaslider-wp-color-picker'), 
            METASLIDER_ASSETS_VERSION, 
            true
        );
        wp_register_script(
            'metaslider-wp-color-picker',
            METASLIDER_ADMIN_URL . 'assets/vendor/wp-color-picker-alpha/wp-color-picker-alpha.min.js',
            array( 'wp-color-picker' ),
            METASLIDER_VERSION
        );
        
        $global_settings = get_option( 'metaslider_global_settings' );

        // @TODO - Move the logic below and MetaSlider->get_breakpoints() to a single place to avoid duplication
        $default_settings   = get_site_option( 'metaslider_default_settings' );
        $global_settings    = $this->get_global_settings();
        
        if ( $default_settings 
            && ! isset( $global_settings['mobileSettings'] ) 
            || ( isset( $global_settings['mobileSettings'] ) 
                && true == $global_settings['mobileSettings'] 
            )  
        ) {
            $breakpoints = array(
                'smartphone' => isset( $default_settings['smartphone'] ) ? (int) $default_settings['smartphone'] : 480,
                'tablet' => isset( $default_settings['tablet'] ) ? (int) $default_settings['tablet'] : 768,
                'laptop' => isset( $default_settings['laptop'] ) ? (int) $default_settings['laptop'] : 1024,
                'desktop' => isset( $default_settings['desktop'] ) ? (int) $default_settings['desktop'] : 1440,
            );
        } else {
            $breakpoints = array(
                'smartphone' => 480,
                'tablet' => 768,
                'laptop' => 1024,
                'desktop' => 1440
            );
        }

        wp_localize_script('metaslider-admin-script', 'metaslider', array(
            'url' => esc_html__("URL", "ml-slider"),
            'caption' => esc_html__("Caption", "ml-slider"),
            'new_window' => esc_html__("New Window", "ml-slider"),
            'confirm' => esc_html__("Please confirm that you would like to delete this slideshow.", "ml-slider"),
            'restore_language' => esc_html__("Undo", "ml-slider"),
            'restored_language' => esc_html__("Slide restored", "ml-slider"),
            'deleted_language' => esc_html__("Slide deleted", "ml-slider"),
            'success_language' => esc_html__("Success", "ml-slider"),
            'copied_language' => esc_html__("Item was copied to your clipboard", "ml-slider"),
            'creating_slideshow_language' => esc_html__("Creating slideshow...", "ml-slider"),
            'click_to_undo_language' => esc_html__("Press to undo", "ml-slider"),
            'ajaxurl' => admin_url('admin-ajax.php'),
            'update_image' => esc_html__("Select replacement image", "ml-slider"),
            'resize_nonce' => wp_create_nonce('metaslider_resize'),
            'create_slide_nonce' => wp_create_nonce('metaslider_create_slide'),
            'delete_slide_nonce' => wp_create_nonce('metaslider_delete_slide'),
            'undelete_slide_nonce' => wp_create_nonce('metaslider_undelete_slide'),
            'permanent_delete_slide_nonce' => wp_create_nonce('metaslider_permanent_delete_slide'),
            'update_slide_image_nonce' => wp_create_nonce('metaslider_update_slide_image'),
            'duplicate_slide_nonce' => wp_create_nonce('metaslider_duplicate_slide'),
            'quickstart_slideshow_nonce' => wp_create_nonce('metaslider_quickstart_slideshow'),
            'legacy_notification_nonce' => wp_create_nonce('metaslider_legacy_notification'),
            'useWithCaution' => esc_html__("Caution: This setting is for advanced developers only. If you're unsure, leave it checked.", "ml-slider"),
            'locale' => preg_replace('/[^a-z]/', '', get_locale()),
            'newSlideOrder' => isset( $global_settings['newSlideOrder'] ) 
                && $global_settings['newSlideOrder'] === 'first' 
                ? esc_html( $global_settings['newSlideOrder'] ) : 'last',
            'tinymce' => array(), // Just initialize to add values later through JS files
            'autoThemeConfig' => ! isset( $global_settings['autoThemeConfig'] ) 
                || (bool) $global_settings['autoThemeConfig'] ? 1 : 0,
            'quickstart_slugs' => $this->quickstart_slugs(),
            'breakpoints' => $breakpoints,
            'tone' => esc_html__( 'Tone', 'ml-slider' ),
            'opacity' => esc_html__( 'Opacity', 'ml-slider' ),
            'device_options_dropdown'  => esc_html__('Device Options', 'ml-slider'),
            'hide_on_mobile'  => esc_html__('Hide on Smartphone', 'ml-slider'),
            'hide_on_tablet'  => esc_html__('Hide on Tablet', 'ml-slider'),
            'hide_on_laptop'  => esc_html__('Hide on Laptop', 'ml-slider'),
            'hide_on_desktop' => esc_html__('Hide on Desktop', 'ml-slider'),
            'mobile_settings' => !isset($global_settings['mobileSettings']) || (isset($global_settings['mobileSettings']) && $global_settings['mobileSettings'] === true)

        ));
        wp_enqueue_script('metaslider-admin-script');
        do_action('metaslider_register_admin_scripts');

        // Javascript translations
        if (function_exists('wp_set_script_translations')) {
            wp_set_script_translations(
                'metaslider-admin-script',
                'ml-slider',
                METASLIDER_PATH . 'languages'
            );
        }

        // Register components and add support for the REST API / Admin AJAX
        do_action('metaslider_register_admin_components');
        $dev = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG;
        wp_register_script('metaslider-admin-components', METASLIDER_ADMIN_URL . 'assets/dist/js/app' . ($dev ? '' : '.min') . '.js', array('jquery','wp-i18n'), METASLIDER_ASSETS_VERSION, true);

        // Check if rest is available
        $is_rest_enabled = $this->is_rest_enabled();

        wp_localize_script('metaslider-admin-components', 'metaslider_api', array(
            'root' => $is_rest_enabled ? esc_url_raw(rest_url("metaslider/v1/")) : false,
            'nonce' => wp_create_nonce('wp_rest'),
            'ajaxurl' => admin_url('admin-ajax.php'),
            'proUser' => metaslider_pro_is_active(),
            'hoplink' => metaslider_get_upgrade_link(),
            'privacy_link' => metaslider_get_privacy_link(),
            'metaslider_admin_assets' => METASLIDER_ADMIN_ASSETS_URL,
            'metaslider_page' => admin_url('admin.php?page=metaslider'),
            'theme_editor_link' => admin_url('admin.php?page=metaslider-theme-editor'),
            'site_id' => get_current_blog_id(),
            'supports_rest' => $is_rest_enabled,
            'locale' => $this->gutenberg_get_jed_locale_data('ml-slider'),
            'default_locale' => $this->gutenberg_get_jed_locale_data('default'),
            'current_server_time' => current_time('mysql'),
        ));
        wp_enqueue_script('metaslider-admin-components');
    }

    /**
     * Loads in custom styling for upgrade page
     */
    public function load_upgrade_page_assets()
    {
        if ('upgrade-metaslider' == $this->current_page || !is_plugin_active('ml-slider-pro/ml-slider-pro.php')){
            wp_enqueue_style('metaslider-upgrade-styles', METASLIDER_ADMIN_URL . 'assets/css/upgrade.css', false, METASLIDER_ASSETS_VERSION);
        }
    }

    /**
     * Loads in custom styling
     */
    public function load_styles()
    {
        wp_enqueue_style('metaslider-shepherd-css', METASLIDER_ADMIN_URL . 'assets/tether-shepherd/dist/css/shepherd-theme-arrows.css', false, METASLIDER_ASSETS_VERSION);
        wp_enqueue_style('metaslider-admin-styles', METASLIDER_ADMIN_URL . 'assets/dist/css/admin.css', false, METASLIDER_ASSETS_VERSION);

        // Hook to load more styles and scripts (from pro)
        do_action('metaslider_register_admin_styles');
    }

    /**
     * Area used to handle any third-party plugin conflicts
     */
    public function fix_conflicts()
    {
        // WP Posts Filter Fix (Advanced Settings not toggling)
        wp_dequeue_script('link');

        // All In One Events Calendar Fix (Advanced Settings not toggling)
        wp_dequeue_script('ai1ec_requirejs');
    }

    /**
     * Method to add pages
     *
     * @param string $title  - The title of the page
     * @param string $slug   - The slug used for the page
     * @param string $parent - Setting a parent will make this page a submenu item
     */
    public function add_page($title, $slug = '', $parent = '')
    {
        $slug = ('' == $slug) ? sanitize_title($title) : $slug;
        $method = 'render_' . str_replace("-", "_", $slug) . '_page';
        if (!method_exists($this, $method)) {
            return false;
        }
        $this->current_page = $slug;
        $capability = apply_filters('metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES);

        $dashboard_icon = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(dirname(__FILE__) . '/assets/metaslider.svg'));

        $page = ('' == $parent) ? add_menu_page($title, $title, $capability, $slug, array($this, $method), $dashboard_icon) : add_submenu_page($parent, $title, $title, $capability, $slug, array($this, $method));

        // Load assets on all pages
        add_action('load-' . $page, array($this, 'fix_conflicts'));
        add_action('load-' . $page, array($this, 'load_overlay'));
        add_action('load-' . $page, array($this, 'load_tooltips'));
        add_action('load-' . $page, array($this, 'load_javascript'));
        add_action('load-' . $page, array($this, 'load_styles'));

        // TinyMCE is only required in edit sldieshow page
        add_action('load-toplevel_page_metaslider', array($this, 'load_wysiwyg'));
    }

    /**
     * Sets up any logic needed for the main page
     * TODO continue refactoring from here
     */
    public function render_metaslider_page()
    {
        // @since 3.90.1
        do_action('metaslider_admin_notices');

        $listtable = new MetaSlider_Admin_Table();
        $listtable->prepare_items();
        if (isset($_REQUEST['post_status'])) {
            include METASLIDER_PATH . "admin/views/pages/parts/toolbar.php";
            include METASLIDER_PATH . "admin/views/pages/dashboard.php";
        } else {
            if (empty($listtable->check_num_rows())) {
                include METASLIDER_PATH . "admin/views/pages/parts/toolbar.php";
                include METASLIDER_PATH . "admin/views/pages/dashboard.php";
            } else {
                if (isset($_REQUEST['id'])) {
                    parent::render_admin_page();
                } else {
                    include METASLIDER_PATH . "admin/views/pages/parts/toolbar.php";
                    include METASLIDER_PATH . "admin/views/pages/dashboard.php";
                }
            }
        }
    }

    /**
     * Sets up any logic needed for the main page
     * TODO continue refactoring from here
     */
    public function render_metaslider_start_page()
    {
        // @since 3.90.1
        do_action('metaslider_admin_notices');

        include METASLIDER_PATH . "admin/views/pages/parts/toolbar.php";
        include METASLIDER_PATH . "admin/views/pages/start.php";
    }

    /**
     * Sets up any logic needed for the main page
     * TODO continue refactoring from here
     */
    public function render_metaslider_settings_page()
    {
        // @since 3.90.1
        do_action('metaslider_admin_notices');

        include METASLIDER_PATH . "admin/views/pages/settings.php";
    }

    /**
     * Sets up any logic needed for the upgrade page
     */
    public function render_upgrade_metaslider_page()
    {
        // @since 3.90.1
        do_action('metaslider_admin_notices');

        include METASLIDER_PATH . "admin/views/pages/parts/toolbar.php";
        include METASLIDER_PATH . "admin/views/pages/upgrade.php";
    }

    /**
     * Backup function for Gutenberg's gutenberg_get_jed_locale_data
     *
     * @param string $domain - The text domain for the strings
     */
    private function gutenberg_get_jed_locale_data($domain)
    {
        if (function_exists('gutenberg_get_jed_locale_data')) {
            return gutenberg_get_jed_locale_data($domain);
        }

        $translations = get_translations_for_domain($domain);
        $locale = array(
            '' => array(
                'domain' => $domain,
                'lang' => is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale(),
            ),
        );

        if (!empty($translations->headers['Plural-Forms'])) {
            $locale['']['plural_forms'] = $translations->headers['Plural-Forms'];
        }

        if (isset($translations->entries) && ! empty($translations->entries)) {
            foreach ($translations->entries as $msgid => $entry) {
                $locale[$msgid] = $entry->translations;
            }
        }

        return $locale;
    }

    /**
     * The main purpose of this is to try to detect that REST is *disabled*. That can be done in many ways, so the results will not be 100% successful in catching all possibilities.
     *
     * @return Boolean
     */
    private function is_rest_enabled()
    {

        // Let users override
        if (defined('METASLIDER_FORCE_ADMIN_AJAX') && METASLIDER_FORCE_ADMIN_AJAX) {
            return false;
        }

        // < WP 4.4
        if (!class_exists('WP_REST_Controller')) {
            return false;
        }

        // A pre-WP 4.7 filter (deprecated in 4.7)
        if (!(bool) apply_filters('rest_enabled', true)) {
            return false;
        }

        // Some plugins remove this
        if (!has_action('init', 'rest_api_init')) {
            return false;
        }

        // Disable via blanking the URL
        if (function_exists('get_rest_url') && class_exists('WP_Rewrite')) {
            global $wp_rewrite;
            if (empty($wp_rewrite)) {
                $wp_rewrite = new WP_Rewrite(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
            }
            if ('' == get_rest_url()) {
                return false;
            }
        }

        // Disable via removing from the <head> output, and non-default permalinks which mean that a default guess will not work
        if (false === has_action('wp_head', 'rest_output_link_wp_head')) {
            $permalink_structure = get_option('permalink_structure');
            if (!$permalink_structure || false !== strpos($permalink_structure, 'index.php')) {
                return false;
            }
        }

        // Plugins which, when active, disable REST. Do not add a plugin which merely has an *option* to disable REST. (For that, we will need further logic, to detect the option setting).
        $plugins = array(
            'disable-permanently-rest-api' => 'Disable Permanently REST API',
        );

        $slugs = array_keys($plugins);

        $active_plugins = get_option('active_plugins');
        if (!is_array($active_plugins)) {
            $active_plugins = array();
        }
        if (is_multisite()) {
            $active_plugins = array_merge($active_plugins, array_keys(get_site_option('active_sitewide_plugins', array())));
        }

        // Loops around each plugin available.
        foreach ($active_plugins as $value) {
            if (!preg_match('#^([^/]+)/#', $value, $matches)) {
                continue;
            }
            if (in_array($matches[1], $slugs)) {
                return false;
            }
        }

        return true;
    }
}
