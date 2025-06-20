<?php
/**
 * Plugin Name: Form Maker
 * Plugin URI: https://10web.io/plugins/wordpress-form-maker/?utm_source=form_maker&utm_medium=free_plugin
 * Description: This plugin is a modern and advanced tool for easy and fast creating of a WordPress Form. The backend interface is intuitive and user friendly which allows users far from scripting and programming to create WordPress Forms.
 * Version: 1.15.34
 * Author: 10Web Form Builder Team
 * Author URI: https://10web.io/plugins/?utm_source=form_maker&utm_medium=free_plugin 
 * License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('ABSPATH') || die('Access Denied');
#[\AllowDynamicProperties] 
final class WDFM {
  /**
   * PLUGIN = 2 points to Contact Form Maker
   */
  const PLUGIN = 1;

  /**
   * The single instance of the class.
   */
  protected static $_instance = null;
  public $abspath;
  public $plugin_dir = '';
  public $plugin_url = '';
  public $front_urls = array();
  public $main_file = '';
  public $plugin_version = '1.15.34';
  public $db_version = '2.15.34';
  public $menu_postfix = '_fm';
  public $plugin_postfix = '';
  public $handle_prefix = 'fm';
  public $slug = 'form-maker';
  public $nicename = 'Form Maker';
  public $menu_slug = '';
  public $prefix = '';
  public $nonce = 'nonce_fm';
  public $fm_form_nonce = 'fm_form_nonce';
  public $is_free = 1;
  public $is_demo = false;
  public $fm_settings = array();

  /**
   * Main WDFM Instance.
   *
   * Ensures only one instance is loaded or can be loaded.
   *
   * @static
   * @return WDFM - Main instance.
   */
  public static function instance() {
    if ( is_null( self::$_instance ) ) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function __construct() {
    $this->define_constants();
    require_once( $this->plugin_dir . '/framework/WDW_FM_Library.php' );
    if ( get_option( 'wd_form_maker_version', FALSE ) ) {
      require_once( $this->plugin_dir . '/framework/Cookie.php' );
    }
    if ( is_admin() ) {
      require_once( wp_normalize_path( $this->plugin_dir . '/admin/controllers/controller.php' ) );
      require_once( wp_normalize_path( $this->plugin_dir . '/admin/models/model.php' ) );
      require_once( wp_normalize_path( $this->plugin_dir . '/admin/views/view.php' ) );
    }
    $this->add_actions();
  }

  /**
   * Define Constants.
   */
  private function define_constants() {
    $this->abspath = $this->fm_get_abspath();
  	$this->plugin_dir = WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__));
    $this->plugin_url = plugins_url(plugin_basename(dirname(__FILE__)));
    $this->front_urls = $this->get_front_urls();
    $this->main_file = plugin_basename(__FILE__);
    if ( $this->is_free == 2 ) {
      $this->menu_postfix = '_fmc';
      $this->plugin_postfix = $this->menu_postfix;
      $this->handle_prefix = 'fmc';
      $this->slug = 'contact-form-maker';
      $this->nicename = 'Contact Form';
    }
    
    $this->menu_slug = 'manage' . $this->menu_postfix;
    $this->prefix = 'form_maker' . $this->plugin_postfix;
    $this->fm_settings = get_option( $this->handle_prefix . '_settings' );
    if ( empty($this->fm_settings['fm_advanced_layout']) ) {
      $this->fm_settings['fm_advanced_layout'] = 0;
    }
    if ( empty($this->fm_settings['fm_antispam_referer']) ) {
      $this->fm_settings['fm_antispam_referer'] = 0;
    }
    if ( empty($this->fm_settings['fm_antispam_bot_validation']) ) {
      $this->fm_settings['fm_antispam_bot_validation'] = 0;
    }
    if ( empty($this->fm_settings['fm_antispam_nonce']) ) {
      $this->fm_settings['fm_antispam_nonce'] = 0;
    }
    if ( empty($this->fm_settings['fm_block_ip_exceeded_limit']) ) {
      $this->fm_settings['fm_block_ip_exceeded_limit'] = 0;
    }
    if ( empty($this->fm_settings['fm_developer_mode']) ) {
      $this->fm_settings['fm_developer_mode'] = 0;
    }
    if ( empty($this->fm_settings['fm_file_read']) ) {
      $this->fm_settings['fm_file_read'] = 0;
    }
    if ( empty($this->fm_settings['fm_ajax_submit']) ) {
      $this->fm_settings['fm_ajax_submit'] = 0;
    }
  }

  /**
   * Get ABSPATH from WP_CONTENT_DIR.
   *
   * @return string
   */
  public static function fm_get_abspath() {
    $dirpath = defined( 'WP_CONTENT_DIR' ) ? WP_CONTENT_DIR : ABSPATH;
    $array = explode( "wp-content", $dirpath );
    if( isset( $array[0] ) && $array[0] != "" ) {
      return $array[0];
    }
    return ABSPATH;
  }

  /**
   * Add actions.
   */
  private function add_actions() {
    add_action('init', array($this, 'init'), 9);
    add_action('admin_menu', array( $this, 'form_maker_options_panel' ) );

    add_action('wp_ajax_manage' . $this->menu_postfix, array($this, 'form_maker_ajax')); //Post/page search on display options pages.
    add_action('wp_ajax_get_stats' . $this->plugin_postfix, array($this, 'form_maker')); //Show statistics
    add_action('wp_ajax_generete_csv' . $this->plugin_postfix, array($this, 'form_maker_ajax')); // Export csv.
    add_action('wp_ajax_generete_xml' . $this->plugin_postfix, array($this, 'form_maker_ajax')); // Export xml.
    add_action('wp_ajax_formmakerwdcaptcha' . $this->plugin_postfix, array($this, 'form_maker_ajax')); // Generete captcha image and save it code in session.
    add_action('wp_ajax_nopriv_formmakerwdcaptcha' . $this->plugin_postfix, array($this, 'form_maker_ajax')); // Generete captcha image and save it code in session for all users.
    add_action('wp_ajax_formmakerwdmathcaptcha' . $this->plugin_postfix, array($this, 'form_maker_ajax')); // Generete math captcha image and save it code in session.
    add_action('wp_ajax_nopriv_formmakerwdmathcaptcha' . $this->plugin_postfix, array($this, 'form_maker_ajax')); // Generete math captcha image and save it code in session for all users.
    add_action('wp_ajax_product_option' . $this->plugin_postfix, array($this, 'form_maker_ajax')); // Open product options on add paypal field.
    add_action('wp_ajax_FormMakerEditCountryinPopup' . $this->plugin_postfix, array($this, 'form_maker_ajax')); // Open country list.
    add_action('wp_ajax_FormMakerMapEditinPopup' . $this->plugin_postfix, array($this, 'form_maker_ajax')); // Open map in submissions.
    add_action('wp_ajax_FormMakerIpinfoinPopup' . $this->plugin_postfix, array($this, 'form_maker_ajax')); // Open ip in submissions.
    add_action('wp_ajax_show_matrix' . $this->plugin_postfix, array($this, 'form_maker_ajax')); // Edit matrix in submissions.
    add_action('wp_ajax_FormMakerSubmits' . $this->plugin_postfix, array($this, 'form_maker_ajax')); // Open submissions in submissions.

    if ( !$this->is_demo ) {
      add_action('wp_ajax_FormMakerSQLMapping' . $this->plugin_postfix, array($this, 'form_maker_ajax')); // Add/Edit SQLMaping from form options.
      add_action('wp_ajax_select_data_from_db' . $this->plugin_postfix, array( $this, 'form_maker_ajax' )); // select data from db.
    }

    add_action('wp_ajax_manage' . $this->plugin_postfix, array($this, 'form_maker_ajax')); //Show statistics

    if ( !$this->is_free ) {
      add_action('wp_ajax_paypal_info', array($this, 'form_maker_ajax')); // Paypal info in submissions page.
      add_action('wp_ajax_checkpaypal', array($this, 'form_maker_ajax')); // Notify url from Paypal Sandbox.
      add_action('wp_ajax_nopriv_checkpaypal', array($this, 'form_maker_ajax')); // Notify url from Paypal Sandbox for all users.
      add_action('wp_ajax_get_frontend_stats', array($this, 'form_maker_ajax_frontend')); //Show statistics frontend
      add_action('wp_ajax_nopriv_get_frontend_stats', array($this, 'form_maker_ajax_frontend')); //Show statistics frontend
      add_action('wp_ajax_frontend_show_map', array($this, 'form_maker_ajax_frontend')); //Show map frontend
      add_action('wp_ajax_nopriv_frontend_show_map', array($this, 'form_maker_ajax_frontend')); //Show map frontend
      add_action('wp_ajax_frontend_show_matrix', array($this, 'form_maker_ajax_frontend')); //Show matrix frontend
      add_action('wp_ajax_nopriv_frontend_show_matrix', array($this, 'form_maker_ajax_frontend')); //Show matrix frontend
      add_action('wp_ajax_frontend_paypal_info', array($this, 'form_maker_ajax_frontend')); //Show paypal info frontend
      add_action('wp_ajax_nopriv_frontend_paypal_info', array($this, 'form_maker_ajax_frontend')); //Show paypal info frontend
      add_action('wp_ajax_frontend_generate_csv', array($this, 'form_maker_ajax_frontend')); //generate csv frontend
      add_action('wp_ajax_nopriv_frontend_generate_csv', array($this, 'form_maker_ajax_frontend')); //generate csv frontend
      add_action('wp_ajax_frontend_generate_xml', array($this, 'form_maker_ajax_frontend')); //generate xml frontend
      add_action('wp_ajax_nopriv_frontend_generate_xml', array($this, 'form_maker_ajax_frontend')); //generate xml frontend
    }
    add_action('wp_ajax_fm_reload_input', array($this, 'form_maker_ajax_frontend'));
    add_action('wp_ajax_nopriv_fm_reload_input', array($this, 'form_maker_ajax_frontend'));
    add_action('wp_ajax_fm_submit_form', array($this, 'FM_front_end_main')); //Show statistics
    add_action( 'wp_ajax_nopriv_fm_submit_form', array($this, 'FM_front_end_main') );
    // Add media button to WP editor.
    add_action('wp_ajax_FMShortocde' . $this->plugin_postfix, array($this, 'form_maker_ajax'));
    add_action('media_buttons', array($this, 'media_button'));

    add_action('wp_ajax_fm_init_cookies', array($this, 'FM_front_end_init_cookies'));
    add_action( 'wp_ajax_nopriv_fm_init_cookies', array($this, 'FM_front_end_init_cookies') );

    add_action('admin_head', array($this, 'form_maker_admin_ajax'));//js variables for admin.

    // Form maker shortcodes.
    if ( !is_admin() ) {
      add_shortcode('FormPreview' . $this->plugin_postfix, array($this, 'fm_form_preview_shortcode'));
      if ($this->is_free != 2) {
        add_shortcode('Form', array($this, 'fm_shortcode'));
      }
      if (!($this->is_free == 1)) {
        add_shortcode('contact_form', array($this, 'fm_shortcode'));
        add_shortcode('wd_contact_form', array($this, 'fm_shortcode'));
      }
      add_shortcode('email_verification' . $this->plugin_postfix, array($this, 'fm_email_verification_shortcode'));
    }
    // Action to display not emedded type forms.
    global $pagenow;
    if (!is_admin() || !in_array($pagenow, array('wp-login.php', 'wp-register.php'))) {
      add_action('wp_footer', array($this, 'FM_front_end_main'));
    }

    // Form Maker Widget.
    if (class_exists('WP_Widget')) {
      add_action('widgets_init',  array($this, 'register_widgets'));
    }

    // Plugin activation.
    register_activation_hook(__FILE__, array($this, 'global_activate'));
	  // Plugin deactivate.
    register_deactivation_hook( __FILE__, array($this, 'global_deactivate'));
    add_action('wpmu_new_blog', array($this, 'new_blog_added'), 10, 6);

    if ( (!isset($_GET['action']) || $_GET['action'] != 'deactivate')
      && (!isset($_GET['page']) || $_GET['page'] != 'uninstall' . $this->menu_postfix) ) {
      add_action('admin_init', array($this, 'form_maker_activate'));
    }

    // Register scripts/styles.
    add_action('wp_enqueue_scripts', array($this, 'register_frontend_scripts'));
    add_action('admin_enqueue_scripts', array($this, 'register_admin_scripts'));

    // Set per_page option for submissions.
    add_filter('set-screen-option', array($this, 'set_option_submissions'), 10, 3);

    // Check extensions versions.
    if ( $this->is_free != 2 && isset( $_GET[ 'page' ] ) && strpos( esc_html( $_GET[ 'page' ] ), '_' . $this->handle_prefix ) !== FALSE ) {
      add_action('admin_notices', array($this, 'fm_check_addons_compatibility'));
    }

    add_action('plugins_loaded', array($this, 'plugins_loaded'), 9);

    add_filter('wpseo_whitelist_permalink_vars', array($this, 'add_query_vars_seo'));

    // Enqueue block editor assets for Gutenberg.
    add_filter('tw_get_block_editor_assets', array($this, 'register_block_editor_assets'));
    add_filter('tw_get_plugin_blocks', array($this, 'register_plugin_block'));
    add_action( 'enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets') );

    // Privacy policy.
    add_action( 'admin_init', array($this, 'add_privacy_policy_content') );

    // Personal data export.
    add_filter( 'wp_privacy_personal_data_exporters', array($this, 'register_privacy_personal_data_exporter') );
    // Personal data erase.
    add_filter( 'wp_privacy_personal_data_erasers', array($this, 'register_privacy_personal_data_eraser') );

    // Register widget for Elementor builder.
    add_action('elementor/widgets/widgets_registered', array($this, 'register_elementor_widget'));
    // Register 10Web category for Elementor widget if 10Web builder doesn't installed.
    add_action('elementor/elements/categories_registered', array($this, 'register_widget_category'), 1, 1);
    //fires after elementor editor styles and scripts are enqueued.
    add_action('elementor/editor/after_enqueue_styles', array($this, 'enqueue_editor_styles'), 11);
    add_action('elementor/editor/after_enqueue_scripts', array($this, 'enqueue_elementor_widget_scripts'));

    // Divi frontend builder assets.
    add_action('et_fb_enqueue_assets', array($this, 'enqueue_divi_bulder_assets'));
    add_action('et_fb_enqueue_assets', array($this, 'form_maker_admin_ajax'));
    add_action('fm_admin_container_ready', array($this, 'check_db_full_privileged'), 99);
    add_action( 'wp_ajax_dismiss_db_full_privileged_notice', array($this, 'dismiss_db_full_privileged_notice_callback') );
    if ( $this->is_free == 1 ) {
      /* Add wordpress.org support custom link in plugin page */
      add_filter('plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'add_ask_question_links' ));
    }
    if (!$this->is_free) {
      add_action('rest_api_init', array($this,'register_endpoint_for_stripe_events'));
    }
  }

  /* Init cookies and get response new key for fm_empty_field_validation which js added to form */
  public function FM_front_end_init_cookies() {
    if ( get_option('wd_form_maker_version', FALSE) ) {
      if ( !class_exists('Cookie_fm') ) {
        require_once(WDFMInstance(self::PLUGIN)->plugin_dir . '/framework/Cookie.php');
      }
      $form_ids = WDW_FM_Library::get('form_ids', '');
      if( empty($form_ids) || gettype($form_ids) != 'array' ) {
        die();
      }
      new Cookie_fm();
      $new_values = array();
      foreach ( $form_ids as $id ) {
        $value = md5('uniqid(rand(), TRUE)');
        $new_values[] = array( 'form_id' => $id, 'field_validation_value' => $value );
        Cookie_fm::saveCookieValueByKey( $id, 'fm_empty_field_validation', $value );
      }
      echo json_encode($new_values);
      die();
    }
  }

  public function check_db_full_privileged() {
    $version = substr_replace(get_option("wd_form_maker_version"), '1.', 0, 2);
    if ( get_option('fm_db_full_privileged', FALSE ) === '0' ) {
      require_once $this->plugin_dir . "/form_maker_update.php";
      if ( $this->is_free == 2 ) {
        WDCFMUpdate::form_maker_update($version);
      }
      else {
        WDFMUpdate::form_maker_update($version);
      }
    }
    if ( get_option('fm_db_full_privileged', FALSE ) === '0' ) {
      if ( get_option('fm_db_full_privileged_notice', FALSE ) === '1' ) {
        echo WDW_FM_Library(self::PLUGIN)->message_id(17, '', 'error', array(WDW_FM_Library(self::PLUGIN),'notice_dismiss_button'));
      } else if ( get_option('fm_db_full_privileged_notice', FALSE ) == '2' ) {
        echo WDW_FM_Library(self::PLUGIN)->message_id(18, '', 'error', array(WDW_FM_Library(self::PLUGIN),'notice_dismiss_button'));
      }
    }
  }

  function dismiss_db_full_privileged_notice_callback() {
    $db_full_privileged_notice = intval( WDW_FM_Library(self::PLUGIN)->get('db_full_privileged_notice') );
    update_option('fm_db_full_privileged_notice', $db_full_privileged_notice);
    wp_die();
  }


  public function enqueue_divi_bulder_assets() {
	  wp_enqueue_style('thickbox');
	  wp_enqueue_script('thickbox');
  }

  /**
   * Add plugin action links.
   *
   * Add a link to the settings page on the plugins.php page.
   *
   * @since 1.0.0
   *
   * @param  array  $links List of existing plugin action links.
   * @return array         List of modified plugin action links.
   */
  function add_ask_question_links ( $links ) {
    $url = 'https://wordpress.org/support/plugin/' . (WDFMInstance(self::PLUGIN)->is_free == 2 ? 'contact-form-maker' : 'form-maker') . '/#new-post';
    $fm_ask_question_link = array('<a href="' . $url . '" target="_blank">' . __('Help', $this->prefix) . '</a>');
    return array_merge( $links, $fm_ask_question_link );
  }

  public function enqueue_editor_styles() {
    wp_enqueue_style($this->handle_prefix . '-icons', $this->plugin_url . '/css/fonts.css', array(), '1.0.1');
  }

  public function enqueue_elementor_widget_scripts(){
    wp_enqueue_script($this->handle_prefix  . 'elementor_widget_js', $this->plugin_url.'/js/fm_elementor_widget.js', array('jquery'));
  }

  /**
   * Register widget for Elementor builder.
   */
  public function register_elementor_widget() {
    if ( defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base') ) {
      require_once ($this->plugin_dir . '/admin/controllers/elementorWidget.php');
    }
  }

  /**
   * Register 10Web category for Elementor widget if 10Web builder doesn't installed.
   *
   * @param $elements_manager
   */
  public function register_widget_category( $elements_manager ) {
    $elements_manager->add_category('tenweb-plugins-widgets', array(
      'title' => __('10WEB Plugins', 'tenweb-builder'),
      'icon' => 'fa fa-plug',
    ));
  }

  function add_privacy_policy_content() {
    if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
      return;
    }

    $content = __( 'When you leave a comment on this site, we send your name, email
        address, IP address and comment text to example.com. Example.com does
        not retain your personal data.', $this->prefix );

    wp_add_privacy_policy_content(
      $this->nicename,
      wp_kses_post( wpautop( $content, false ) )
    );
  }

  public function register_privacy_personal_data_exporter( $exporters ) {
    $exporters[ $this->slug ] = array(
      'exporter_friendly_name' => $this->nicename,
      'callback' => array( WDW_FM_Library(self::PLUGIN), 'privacy_personal_data_export' ),
    );
    return $exporters;
  }

  public function register_privacy_personal_data_eraser( $erasers  ) {
    $erasers[ $this->slug ] = array(
      'eraser_friendly_name' => $this->nicename,
      'callback' => array( WDW_FM_Library(self::PLUGIN), 'privacy_personal_data_erase' ),
    );
    return $erasers;
  }

  public function register_block_editor_assets($assets) {
    $version = '2.0.3';
    $js_path = $this->plugin_url . '/js/tw-gb/block.js';
    $css_path = $this->plugin_url . '/css/tw-gb/block.css';
    if (!isset($assets['version']) || version_compare($assets['version'], $version) === -1) {
      $assets['version'] = $version;
      $assets['js_path'] = $js_path;
      $assets['css_path'] = $css_path;
    }
    return $assets;
  }

  public function register_plugin_block($blocks) {
    if ($this->is_free == 2) {
      $key = 'tw/contact-form-maker';
      $key_submissions = 'tw/cfm-submissions';
    }
    else {
      $key = 'tw/form-maker';
      $key_submissions = 'tw/fm-submissions';
    }
    $fm_nonce = wp_create_nonce('fm_ajax_nonce');
    $plugin_name = $this->nicename;
    $plugin_name_submissions = __('Submissions', $this->prefix);
    $icon_url = $this->plugin_url . '/images/tw-gb/icon_colored.svg';
    $icon_svg = $this->plugin_url . '/images/tw-gb/icon.svg';
    $url = add_query_arg(array('action' => 'FMShortocde' . $this->plugin_postfix, 'task' => 'submissions', 'nonce' => $fm_nonce), admin_url('admin-ajax.php'));
    $data = WDW_FM_Library(self::PLUGIN)->get_shortcode_data();
    $blocks[$key] = array(
      'title' => $plugin_name,
      'titleSelect' => sprintf(__('Select %s', $this->prefix), $plugin_name),
      'iconUrl' => $icon_url,
      'iconSvg' => array('width' => 20, 'height' => 20, 'src' => $icon_svg),
      'isPopup' => false,
      'data' => $data,
    );
    $blocks[$key_submissions] = array(
      'title' => $plugin_name_submissions,
      'titleSelect' => sprintf(__('Select %s', $this->prefix), $plugin_name),
      'iconUrl' => $icon_url,
      'iconSvg' => array('width' => 20, 'height' => 20, 'src' => $icon_svg),
      'isPopup' => true,
      'containerClass' => 'tw-container-wrap-520-400',
      'data' => array('shortcodeUrl' => $url),
    );
    return $blocks;
  }

  public function enqueue_block_editor_assets() {
    // Remove previously registered or enqueued versions
    $wp_scripts = wp_scripts();
    foreach ($wp_scripts->registered as $key => $value) {
      // Check for an older versions with prefix.
      if (strpos($key, 'tw-gb-block') > 0) {
        wp_deregister_script( $key );
        wp_deregister_style( $key );
      }
    }
    // Get plugin blocks from all 10Web plugins.
    $blocks = apply_filters('tw_get_plugin_blocks', array());
    // Get the last version from all 10Web plugins.
    $assets = apply_filters('tw_get_block_editor_assets', array());
    // Not performing unregister or unenqueue as in old versions all are with prefixes.
    wp_enqueue_script('tw-gb-block', $assets['js_path'], array( 'wp-blocks', 'wp-element' ), $assets['version']);
    wp_localize_script('tw-gb-block', 'tw_obj_translate', array(
      'nothing_selected' => __('Nothing selected.', $this->prefix),
      'empty_item' => __('- Select -', $this->prefix),
      'blocks' => json_encode($blocks)
    ));
    wp_enqueue_style('tw-gb-block', $assets['css_path'], array( 'wp-edit-blocks' ), $assets['version']);
  }

  /**
   * Wordpress init actions.
   */
  public function init() {
    ob_start();
    $this->fm_overview();

    // Register fmemailverification post type
    $this->register_fmemailverification_cpt();

    // Register fmformpreview post type
    $this->register_form_preview_cpt();
  }

  /**
   * Plugins loaded actions.
   */
  public function plugins_loaded() {
    // Languages localization.
    load_plugin_textdomain($this->prefix, FALSE, basename(dirname(__FILE__)) . '/languages');

    if ($this->is_free != 2 && !function_exists('WDFM')) {
      require_once($this->plugin_dir . '/WDFM.php');
    }

    // Initialize extensions.
    if ($this->is_free != 2) {
      do_action('fm_init_addons');
    }
    // Prevent adding shortcode conflict with some builders.
    $this->before_shortcode_add_builder_editor();
  }

  /**
   * Plugin menu.
   */
  public function form_maker_options_panel() {
    $parent_slug = $this->menu_slug;
    add_menu_page($this->nicename, $this->nicename, 'manage_options', $this->menu_slug, array( $this, 'form_maker' ), $this->plugin_url . '/images/FormMakerLogo-16.png');
    add_submenu_page($parent_slug, __('Forms', $this->prefix), __('Forms', $this->prefix), 'manage_options', $this->menu_slug, array($this, 'form_maker'));
    $submissions_page = add_submenu_page($parent_slug, __('Submissions', $this->prefix), __('Submissions', $this->prefix), 'manage_options', 'submissions' . $this->menu_postfix, array($this, 'form_maker'));
    add_action('load-' . $submissions_page, array($this, 'submissions_per_page'));

    add_submenu_page('', __('Blocked IPs', $this->prefix), __('Blocked IPs', $this->prefix), 'manage_options', 'blocked_ips' . $this->menu_postfix, array($this, 'form_maker'));
    add_submenu_page($parent_slug, __('Themes', $this->prefix), __('Themes', $this->prefix), 'manage_options', 'themes' . $this->menu_postfix, array($this, 'form_maker'));
    add_submenu_page($parent_slug, __('Options', $this->prefix), __('Options', $this->prefix), 'manage_options', 'options' . $this->menu_postfix, array($this, 'form_maker'));
    add_submenu_page('', __('Uninstall', $this->prefix), __('Uninstall', $this->prefix), 'manage_options', 'uninstall' . $this->menu_postfix, array($this, 'form_maker'));

    if ( current_user_can('manage_options') && $this->is_free ) {
      /* Custom link to wordpress.org*/
      global $submenu;
      $url = 'https://wordpress.org/support/plugin/' . (WDFMInstance(self::PLUGIN)->is_free == 2 ? 'contact-form-maker' : 'form-maker') . '/#new-post';
      $submenu[$parent_slug][] = array(
        '<div id="fm_ask_question">' . __('Ask a question', $this->prefix) . '</div>',
        'manage_options',
        $url
      );
    }
  }

  /**
   * Set front plugin url.
   *
   * return string  $plugin_url
   */
  private function set_front_plugin_url() {
    $plugin_url = plugins_url(plugin_basename(dirname(__FILE__)));

    return $plugin_url;
  }

  /**
   * Set front upload url.
   *
   * return string  $upload_url
   */
  private function set_front_upload_url() {
    $wp_upload_dir = wp_upload_dir();
    $upload_url = $wp_upload_dir['baseurl'];
    $http  = 'http://';
    $https = 'https://';
    if ( (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) || strpos(get_option('home'), $https) > -1 ) {
      $upload_url = str_replace($http, $https, $wp_upload_dir['baseurl']);
    }

    return $upload_url;
  }

  /**
   * Get front urls.
   *
   * return array  $urls
   */
  public function get_front_urls() {
    $urls = array();
    $urls['plugin_url'] = $this->set_front_plugin_url();
    $urls['upload_url'] = $this->set_front_upload_url();

    return $urls;
  }

  /**
   * Add per_page screen option for submissions page.
   */
  function submissions_per_page() {
    $option = 'per_page';
    $args_rates = array(
      'label' => __('Number of items per page:', $this->prefix),
      'default' => 20,
      'option' => 'fm_submissions_per_page'
    );
    add_screen_option( $option, $args_rates );
  }

  /**
   * Set per_page option for submissions page.
   *
   * @param $status
   * @param $option
   * @param $value
   * @return mixed
   */
  function set_option_submissions($status, $option, $value) {
    if ( 'fm_submissions_per_page' == $option ) return $value;
    return $status;
  }

  /**
   * Output for admin pages.
   */
  public function form_maker() {
    if (function_exists('current_user_can')) {
      if (!current_user_can('manage_options')) {
        die('Access Denied');
      }
    }
    else {
      die('Access Denied');
    }
    $page = WDW_FM_Library(self::PLUGIN)->get('page');
    if (($page != '') && (($page == 'manage' . $this->menu_postfix) || ($page == 'options' . $this->menu_postfix) || ($page == 'submissions' . $this->menu_postfix) || ($page == 'blocked_ips' . $this->menu_postfix) || ($page == 'themes' . $this->menu_postfix) || ($page == 'uninstall' . $this->menu_postfix))) {

      $page = ucfirst(substr($page, 0, strlen($page) - strlen($this->menu_postfix)));
      echo '<div id="fm_loading"></div>';
      echo '<div id="fm_admin_container" class="fm-form-container" style="display: none;">';
      do_action( 'fm_admin_container_ready' );
      try {
        require_once ($this->plugin_dir . '/admin/controllers/' . $page . '_fm.php');
        $controller_class = 'FMController' . $page . $this->menu_postfix;
        $controller = new $controller_class();
        $controller->execute();
      } catch (Exception $e) {
        ob_start();
        debug_print_backtrace();
        error_log(ob_get_clean());
      }
      echo '</div>';
    }
  }

  /**
   * Register widgets.
   */
  public function register_widgets() {
    require_once($this->plugin_dir . '/admin/controllers/Widget.php');
    register_widget('FMControllerWidget' . $this->plugin_postfix);
  }

  /**
   * Register Admin styles/scripts.
   */
  public function register_admin_scripts() {
    $current_screen = get_current_screen();
    if ( $this->is_free && !empty($current_screen->id) && $current_screen->id == "toplevel_page_fm_subscribe" ) {
      wp_enqueue_style($this->handle_prefix . '_subscribe', $this->plugin_url . '/css/fm_subscribe.css', array(), $this->plugin_version);
    }
    $fm_settings = $this->fm_settings;
    // Admin styles.
    wp_register_style($this->handle_prefix . '-tables', $this->plugin_url . '/css/form_maker_tables.css', array(), $this->plugin_version);
    wp_register_style($this->handle_prefix . '-phone_field_css', $this->plugin_url . '/css/intlTelInput.min.css', array(), '17.0.13');
    wp_register_style($this->handle_prefix . '-jquery-ui', $this->plugin_url . '/css/jquery-ui.custom.css', array(), $this->plugin_version);
    wp_register_style($this->handle_prefix . '-codemirror', $this->plugin_url . '/css/codemirror.min.css', array(), '5.63.0');
    wp_register_style($this->handle_prefix . '-layout', $this->plugin_url . '/css/form_maker_layout.css', array(), $this->plugin_version);
    wp_register_style($this->handle_prefix . '-bootstrap', $this->plugin_url . '/css/fm-bootstrap.css', array(), $this->plugin_version);
    wp_register_style($this->handle_prefix . '-colorpicker', $this->plugin_url . '/css/spectrum.min.css', array(), '1.8.1');
    // Roboto font for top bar.
    wp_register_style($this->handle_prefix . '-roboto', 'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap');

    // Admin scripts.
    $localize_key_all = $this->handle_prefix . '-admin';
    $localize_key_manage = $this->handle_prefix . '-manage';
    $localize_key_add_fields = $this->handle_prefix . '-add-fields';
    $localize_key_formmaker_div = $this->handle_prefix . '-formmaker_div';

    if (!$fm_settings['fm_developer_mode']) {
      $localize_key_all = $this->handle_prefix . '-scripts';
      if (WDW_FM_Library(self::PLUGIN)->get('page') == 'submissions_' . $this->handle_prefix) {
        $localize_key_all = $this->handle_prefix . '-submission';
      }
      if (WDW_FM_Library(self::PLUGIN)->get('page') == 'manage_' . $this->handle_prefix) {
        $localize_key_all = $this->handle_prefix . '-manage';
      }
      $localize_key_manage .= '-edit';
      $localize_key_add_fields = $localize_key_manage;
      $localize_key_formmaker_div = $localize_key_manage;
      wp_register_style($this->handle_prefix . '-styles', $this->plugin_url . '/css/fm-styles.min.css', array(), $this->plugin_version);
      wp_register_script($this->handle_prefix . '-scripts', $this->plugin_url . '/js/fm-scripts.min.js', array(), $this->plugin_version);

      wp_register_style($this->handle_prefix . '-manage', $this->plugin_url . '/css/manage-styles.min.css', array(), $this->plugin_version);
      wp_register_script($this->handle_prefix . '-manage', $this->plugin_url . '/js/manage-scripts.min.js', array(), $this->plugin_version);

      wp_register_style($this->handle_prefix . '-manage-edit', $this->plugin_url . '/css/manage-edit-styles.min.css', array(), $this->plugin_version);
      wp_register_script($this->handle_prefix . '-manage-edit', $this->plugin_url . '/js/manage-edit-scripts.min.js', array(), $this->plugin_version);

      wp_register_style($this->handle_prefix . '-submission', $this->plugin_url . '/css/submission-styles.min.css', array(), $this->plugin_version);
      wp_register_script($this->handle_prefix . '-submission', $this->plugin_url . '/js/submission-scripts.min.js', array(), $this->plugin_version);

      wp_register_style($this->handle_prefix . '-theme-edit', $this->plugin_url . '/css/theme-edit-styles.min.css', array(), $this->plugin_version);
      wp_register_script($this->handle_prefix . '-theme-edit', $this->plugin_url . '/js/theme-edit-scripts.min.js', array(), $this->plugin_version);
    }

    $google_map_key = !empty($fm_settings['map_key']) ? '&key=' . $fm_settings['map_key'] : '';
    wp_register_script('google-maps', 'https://maps.google.com/maps/api/js?v=3.exp' . $google_map_key);
    wp_register_script($this->handle_prefix . '-gmap_form', $this->plugin_url . '/js/if_gmap_back_end.js', array(), $this->plugin_version);

    wp_register_script($this->handle_prefix . '-signaturepad', $this->plugin_url . '/js/jquery.signaturepad.min.js', array(), '2.5.2');
    wp_register_script($this->handle_prefix . '-phone_field', $this->plugin_url . '/js/intlTelInput.min.js', array(), '17.0.13');

    // For drag and drop on mobiles.
    wp_register_script($this->handle_prefix . '_jquery.ui.touch-punch.min', $this->plugin_url . '/js/jquery.ui.touch-punch.min.js', array('jquery'), '0.2.3');

    wp_register_script($this->handle_prefix . '-admin', $this->plugin_url . '/js/form_maker_admin.js', array(), $this->plugin_version);
    wp_register_script($localize_key_manage, $this->plugin_url . '/js/form_maker_manage.js', array(), $this->plugin_version);
    wp_register_script($this->handle_prefix . '-manage-edit', $this->plugin_url . '/js/form_maker_manage_edit.js', array(), $this->plugin_version);
    wp_register_script($localize_key_formmaker_div, $this->plugin_url . '/js/formmaker_div.js', array(), $this->plugin_version);
    wp_register_script($this->handle_prefix . '-form-options', $this->plugin_url . '/js/form_maker_form_options.js', array(), $this->plugin_version);
    wp_register_script($this->handle_prefix . '-form-advanced-layout', $this->plugin_url . '/js/form_maker_form_advanced_layout.js', array(), $this->plugin_version);
    wp_register_script($localize_key_add_fields, $this->plugin_url . '/js/add_field.js', array($this->handle_prefix . '-formmaker_div'), $this->plugin_version);

    wp_localize_script($localize_key_manage, 'form_maker_manage', array(
      'add_new_field' => __('Add Field', $this->prefix),
      'add_column' => __('Add Column', $this->prefix),
      'required_field' => __('Field is required.', $this->prefix),
      'not_valid_value' => __('Enter a valid value.', $this->prefix),
      'not_valid_email' => __('Enter a valid email address.', $this->prefix),
      'succeeded' => __('Succeeded', $this->prefix),
      'failed' => __('Failed', $this->prefix),
    ));

    wp_localize_script($localize_key_all, 'form_maker', array(
      'countries' => WDW_FM_Library(self::PLUGIN)->get_countries(),
      'delete_confirmation' => __('Do you want to delete selected items?', $this->prefix),
      'select_at_least_one_item' => __('You must select at least one item.', $this->prefix),
      'add_placeholder' => __('Add placeholder', $this->prefix),
    ));

	  wp_localize_script($localize_key_all, 'form_maker_stripe_statuses', array(
	    'succeeded'         => __( 'Succeeded', $this->prefix ),
	    'already_succeeded' => __( 'Already succeeded', $this->prefix ),
	    'failed'            => __( 'Failed', $this->prefix ),
	    'add_new_field'     => __( 'Add Field', $this->prefix ),
    ) );

    wp_localize_script($localize_key_add_fields, 'form_maker', array(
      'countries' => WDW_FM_Library(self::PLUGIN)->get_countries(),
      'states' => WDW_FM_Library(self::PLUGIN)->get_states(),
      'provinces' => WDW_FM_Library(self::PLUGIN)->get_provinces_canada(),
      'plugin_url' => $this->plugin_url,
      'nothing_found' => __('Nothing found.', $this->prefix),
      'captcha_created' => __('The captcha already has been created.', $this->prefix),
      'update' => __('Update', $this->prefix),
      'add' => __('Add', $this->prefix),
      'add_field' => __('Add Field', $this->prefix),
      'edit_field' => __('Edit Field', $this->prefix),
      'stripe3' => __('To use this feature, please go to Settings > Payment Options and select "Stripe" as the Payment Method.', $this->prefix),
      'sunday' => __('Sunday', $this->prefix),
      'monday' => __('Monday', $this->prefix),
      'tuesday' => __('Tuesday', $this->prefix),
      'wednesday' => __('Wednesday', $this->prefix),
      'thursday' => __('Thursday', $this->prefix),
      'friday' => __('Friday', $this->prefix),
      'saturday' => __('Saturday', $this->prefix),
      'leave_empty' => __('Leave empty to set the width to 100%.', $this->prefix),
      'is_demo' => $this->is_demo,
      'important_message' => __('The free version is limited up to 7 fields to add. If you need this functionality, you need to buy the commercial version.', $this->prefix),
      'no_preview' => __('No preview available for reCAPTCHA.', $this->prefix),
      'invisible_recaptcha_error' => sprintf(__('%s Old reCAPTCHA keys will not work for %s. Please make sure to enable the API keys for Invisible reCAPTCHA.', $this->prefix), '<b>' . __('Note:', $this->prefix) . '</b>', '<b>' . __('Invisible reCAPTCHA', $this->prefix) . '</b>'),
      'type_text_description' => __('This field is a single line text input.', $this->prefix) . '<br><br>' . __('To set a default value, just fill the field above.', $this->prefix) . '<br><br>' . __('You can set the text input as Required, making sure the submitter provides a value for it.', $this->prefix) . '<br><br>' . __('Validation (RegExp.) option in Advanced options lets you configure Regular Expression for your Single Line Text field. Use Common Regular Expressions select box to use built-in validation patterns. For instance, in case you can add a validation for decimal number, IP address or zip code by selecting corresponding options of Common Regular Expressions drop-down.', $this->prefix) . '<br><br>' . __('Additionally, you can add HTML attributes to your form fields with Additional Attributes.', $this->prefix),
      'type_textarea_description' => __('This field adds a textarea box to your form. Users can write alphanumeric text, special characters and line breaks.', $this->prefix) . '<br><br>' . __('You can set the text input as Required, making sure the submitter provides a value for it.', $this->prefix) . '<br><br>' . __('Set the width and height of the textarea box using Size(px) option.', $this->prefix),
      'type_number_description' => __('This is an input text that accepts only numbers. Users can type a number directly, or use the spinner arrows to specify it.', $this->prefix) . '<br><br>' . __('Step option defines the number to increment/decrement the spinner value, when the users press up or down arrows.', $this->prefix) . '<br><br>' . __('Use Min Value and Max Value options to set lower and upper limitation for the value of this Number field.', $this->prefix) . '<br><br>' . __('To set a default value, just fill the field above.', $this->prefix),
      'type_select_description' => __('This field allows the submitter to choose values from select box. Just click (+) Option button and fill in all options you will need or click  (+) From Database to fill the options from a database table.', $this->prefix) . '<br><br>' . __('In case you need to have option values to be different from option names, mark Enable option\'s value from Advanced options as checked.', $this->prefix),
      'type_radio_description' => __('Using this field you can add a list of Radio buttons to your form. Just click (+) Option button and fill in all options you will need or click  (+) From Database to fill the options from a database table.', $this->prefix) . '<br><br>' . __('Relative Position lets you choose the position of options in relation to each other. Whereas Option Label Position lets you select the position of radio button label.', $this->prefix) . '<br><br>' . __('In case you need to have option values to be different from option names, mark Enable option\'s value from Advanced options as checked.', $this->prefix) . '<br><br>' . __('And by enabling Allow other, you can let the user to write their own specific value.', $this->prefix),
      'type_checkbox_description' => __('Multiple Choice field lets you have a list of Checkboxes. This field allows the submitter to choose more than one values.', $this->prefix) . '<br><br>' . __('Just click (+) Option button and fill in all options you will need or click  (+) From Database to fill the options from a database table.', $this->prefix) . '<br><br>' . __('Relative Position lets you choose the position of options in relation to each other. Whereas Option Label Position lets you select the position of radio button label.', $this->prefix) . '<br><br>' . __('In case you need to have option values to be different from option names, mark Enable option\'s value from Advanced options as checked.', $this->prefix) . '<br><br>' . __('And by enabling Allow other, you can let the user to write their own specific value.', $this->prefix),
      'type_recaptcha_description' => sprintf(__('Form Maker is integrated with Google ReCaptcha, which protects your forms from spam bots. Before adding ReCaptcha to your form, you need to configure Site and Secret Keys by registering your website on %s', $this->prefix), '<a href="https://www.google.com/recaptcha/intro/" target="_blank">' . __('Google ReCaptcha website', $this->prefix) . '</a>') . '<br><br>' . __('After registering and creating the keys, copy them to Form Maker > Options page.', $this->prefix),
      'type_submit_description' => __('The Submit button validates all form field values, saves them on MySQL database of your website, sends emails and performs other actions configured in Form Options. You can have more than one submit button in your form.', $this->prefix),
      'type_captcha_description' => __('You can use this field as an alternative to ReCaptcha to protect your forms against spambots. It’s a random combination of numbers and letters, and users need to type them in correctly to submit the form.', $this->prefix) . '<br><br>' . __('You can specify the number of symbols in Simple Captcha using Symbols (3 - 9) option.', $this->prefix),
      'type_name_description' => __('This field lets the user write their name.', $this->prefix) . '<br><br>' . __('To set a default value, just fill the field above.', $this->prefix) . '<br><br>' . __('Enabling Autofill with user name setting will automatically fill in Name field with the name of the logged in user.', $this->prefix) . '<br><br>' . __('In case you do not wish to receive the same data for the same Name field twice, activate Allow only unique values option.', $this->prefix),
      'type_email_description' => __('This field is an input field that accepts an email address.', $this->prefix) . '<br><br>' . __('To set a default value, just fill the field above.', $this->prefix) . '<br><br>' . __('Using Confirmation Email setting in Advanced Options you can require the submitter to re-type their email address.', $this->prefix) . '<br><br>' . __('Autofill with user email will autofill Email field with the email address of the logged in user.', $this->prefix) . '<br><br>' . __('Upon successful submission of the Form, you have the option to send the submitted data (or just a confirmation message) to the email address entered here. To do this you need to set the corresponding options on Form Options > Email Options page.', $this->prefix),
      'type_phone_description' => __('This field is an input for a phone number. It provides a list of country flags, which users can select and have their country code automatically added to the phone number.', $this->prefix) . '<br><br>' . __('In case you do not wish to receive the same data for the same Phone field more than once, activate Allow only unique values setting from Advanced options.', $this->prefix),
      'type_address_description' => __('This field lets you skip a few steps and quickly add one set for requesting the address of the submitter. Use Overall size(px) option to set the width of Address field.', $this->prefix) . '<br><br>' . __('You can enable or disable elements of Address field using Disable Field(s) setting in Advanced Options.', $this->prefix) . '<br><br>' . __('You can turn State/Province/Region field into a list of US states by activating Use list for US states setting from Advanced Options. Note: This only works in case United States is selected for Country select box.', $this->prefix),
      'type_mark_on_map_description' => __('Mark on Map field lets users to drag the map pin and drop it on their selected location. You can specify a default address for the location pin with Address option.', $this->prefix) . '<br><br>' . __('In addition, Marker Info setting allows you to provide additional details about the location. It will appear after users click on the location pin.', $this->prefix),
      'type_country_list_description' => __('Country List is a select box which provides a list of all countries in alphabetical order.', $this->prefix) . '<br><br>' . __('You can include/exclude specific countries from the list using the Edit country list setting in Advanced Options.', $this->prefix),
      'type_date_of_birth_description' => __('Users can specify their birthday or any date with this field.', $this->prefix) . '<br><br>' . __('Use Fields separator setting in Advanced options to change the divider between day, month and year boxes.', $this->prefix) . '<br><br>' . __('You can set the fields to be text inputs or select boxes using Day field type, Month field type and Year field type options.', $this->prefix) . '<br><br>' . __('In addition, you can specify the width of day, month and year fields using Day field size(px), Month field size(px) and Year field size(px) settings.', $this->prefix) . '<br><br>' . __('Min Value of Date: Use the date format dd/m/yy, e.g. 05/07/2020". The range of Year field from 1901 to the current year.', $this->prefix),
      'type_file_upload_description' => __('You can allow users to upload single or multiple documents, images and various files through your form.', $this->prefix) . '<br><br>' . __('Use Allowed file extensions option to specify all acceptable file formats. Make sure to separate them with commas.', $this->prefix) . '<br><br>' . __('Mark Allow Uploading Multiple Files option in Advanced Options to allow users to select and upload multiple files.', $this->prefix),
      'type_map_description' => __('Map field can be used for pinning one or more locations on Google Map and displaying them on your form.', $this->prefix) . '<br><br>' . __('Press the small Plus icon to add a location pin.', $this->prefix),
      'type_time_description' => __('Time field of Form Maker plugin will allow users to specify time value. Set the time format of the field to 24-hour or 12-hour using Time Format option.', $this->prefix),
      'type_send_copy_description' => __('When users fill in an email address using Email Field, this checkbox will allow them to choose if they wish to receive a copy of the submission email.', $this->prefix) . '<br><br>' . __('Note: Make sure to configure Form Options > Email Options of your form.', $this->prefix),
      'type_stars_description' => __('Add Star rating field to your form with this field. You can display as many stars, as you will need, set the number using Number of Stars option.', $this->prefix),
      'type_rating_description' => __('Place Rating field on your form to have radio buttons, which indicate rating from worst to best. You can set many radio buttons to display using Scale Range option.', $this->prefix),
      'type_slider_description' => __('Slider field lets users specify the field value by dragging its handle from Min Value to Max Value.', $this->prefix),
      'type_range_description' => __('You can use this field to let users choose a numeric range by providing values for 2 number inputs. Its Step option allows to set the increment/decrement of spinners’ values, when users click on up or down arrows.', $this->prefix),
      'type_grades_description' => __('Users will be able to grade specified items with this field. The sum of all values will appear below the field with Total parameter.', $this->prefix) . '<br><br>' . __('Items option allows you to add multiple options to your Grades field.', $this->prefix),
      'type_matrix_description' => __('Table of Fields lets you place a matrix on your form, which will let the submitter to answer a few questions with one field.', $this->prefix) . '<br><br>' . __('It allows you to configure the matrix with radio buttons, checkboxes, text boxes or drop-downs. Use Input Type option to set this.', $this->prefix),
      'type_hidden_description' => __('Hidden Input field is similar to Single Line Text field, but it is not visible to users. Hidden Fields are handy, in case you need to run a custom Javascript and submit the result with the info on your form.', $this->prefix) . '<br><br>' . __('Name option of this field is mandatory. Note: we highly recommend you to avoid using spaces or special characters in Hidden Input name. You can write the custom Javascript code using the editor on Form Options > Javascript page.', $this->prefix),
      'type_button_description' => __('In case you wish to run custom Javascript on your form, you can place Custom Button on your form. Its lets you call the script with its OnClick function.', $this->prefix) . '<br><br>' . __('You can write the custom Javascript code using the editor on Form Options > Javascript page.', $this->prefix),
      'type_password_description' => __('Password input can be used to allow users provide secret text, such as passwords. All symbols written in this field are replaced with dots.', $this->prefix) . '<br><br>' . __('You can activate Password Confirmation option to ask users to repeat the password.', $this->prefix),
      'type_phone_area_code_description' => __('Phone-Area Code is a Phone type field, which allows users to write Area Code and Phone Number into separate inputs.', $this->prefix),
      'type_arithmetic_captcha_description' => __('Arithmetic Captcha is quite similar to Simple Captcha. However, instead of showing random symbols, it displays arithmetic operations.', $this->prefix) . '<br><br>' . __('You can set the operations using Operations option. The field can use addition (+), subtraction (-), multiplication (*) and division (/).', $this->prefix) . '<br><br>' . __('Make sure to separate the operations with commas.', $this->prefix),
      'type_price_description' => __('Users can set a payment amount of their choice with Price field. Assigns minimum and maximum limits on its value using Range option.', $this->prefix) . '<br><br>' . __('To set a default value, just fill the field above.', $this->prefix) . '<br><br>' . __('Additionally, you can activate Readonly attribute. This way, users will not be able to edit the value of Price.', $this->prefix) . '<br><br>' . __('Note: Make sure to configure Form Options > Payment Options of your form.', $this->prefix),
      'type_payment_select_description' => __('Payment Select field lets you create lists of products, one of which the submitter can choose to buy through your form. Add or edit list items using Options setting of the fields.', $this->prefix) . '<br><br>' . __('Enable Quantity property from Advanced Options, in case you would like the users to mention the quantity of items they purchase.', $this->prefix) . '<br><br>' . __('Also, you can configure custom or built-in Product Properties for your products, such as Color, T-Shirt Size or Print Size.', $this->prefix) . '<br><br>' . __('Note: Make sure to configure Form Options > Payment Options of your form.', $this->prefix),
      'type_payment_radio_description' => __('Payment Single Choice field lets you create lists of products, one of which the submitter can choose to buy through your form. Add or edit list items using Options setting of the fields.', $this->prefix) . '<br><br>' . __('Enable Quantity property from Advanced Options, in case you would like the users to mention the quantity of items they purchase.', $this->prefix) . '<br><br>' . __('Also, you can configure custom or built-in Product Properties for your products, such as Color, T-Shirt Size or Print Size.', $this->prefix) . '<br><br>' . __('Note: Make sure to configure Form Options > Payment Options of your form.', $this->prefix),
      'type_payment_checkbox_description' => __('Payment Multiple Choice field lets you create lists of products, which the submitter can choose to buy through your form. Add or edit list items using Options setting of the fields.', $this->prefix) . '<br><br>' . __('Enable Quantity property from Advanced Options, in case you would like the users to mention the quantity of items they purchase.', $this->prefix) . '<br><br>' . __('Also, you can configure custom or built-in Product Properties for your products, such as Color, T-Shirt Size or Print Size.', $this->prefix) . '<br><br>' . __('Note: Make sure to configure Form Options > Payment Options of your form.', $this->prefix),
      'type_shipping_description' => __('Shipping allows you to configure shipping types, set price for each of them and display them on your form as radio buttons.', $this->prefix),
      'type_total_description' => __('Please Total field to your payment form to sum up the values of Payment fields. ', $this->prefix),
      'type_stripe_description' => __('This field adds the credit card details inputs (card number, expiration date, etc.) and allows you to accept direct payments made by credit cards.', $this->prefix),
      'upload_max_size' => __('Your upload_max_filesize directive in php.ini is ' . intval(ini_get('upload_max_filesize')) * 1024 . 'KB', $this->prefix),
    ));

    wp_register_script($this->handle_prefix . '-codemirror', $this->plugin_url . '/js/layout/codemirror.min.js', array(), '5.63.3');
    wp_register_script($this->handle_prefix . '-clike', $this->plugin_url . '/js/layout/clike.min.js', array(), '5.63.3');
    wp_register_script($this->handle_prefix . '-formatting', $this->plugin_url . '/js/layout/formatting.js', array(), '1.0.0');
    wp_register_script($this->handle_prefix . '-css', $this->plugin_url . '/js/layout/css.min.js', array(), '5.63.3');
    wp_register_script($this->handle_prefix . '-javascript', $this->plugin_url . '/js/layout/javascript.min.js', array(), '5.63.3');
    wp_register_script($this->handle_prefix . '-xml', $this->plugin_url . '/js/layout/xml.min.js', array(), '5.63.3');
    wp_register_script($this->handle_prefix . '-php', $this->plugin_url . '/js/layout/php.min.js', array(), '5.63.3');
    wp_register_script($this->handle_prefix . '-htmlmixed', $this->plugin_url . '/js/layout/htmlmixed.min.js', array(), '5.63.3');

    wp_register_script($this->handle_prefix . '-colorpicker', $this->plugin_url . '/js/spectrum.min.js', array(), '1.8.1');
    wp_register_script($this->handle_prefix . '-themes', $this->plugin_url . '/js/themes.js', array(), $this->plugin_version);
    wp_register_script($this->handle_prefix . '-submissions', $this->plugin_url . '/js/form_maker_submissions.js', array(), $this->plugin_version);
    wp_register_script($this->handle_prefix . '-ng-js', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular.min.js', array(), '1.5.0');
    wp_register_script($this->handle_prefix . '-theme-edit-ng', $this->plugin_url . '/js/fm-theme-edit-ng.js', array(), $this->plugin_version);


    wp_register_style($this->handle_prefix . '-deactivate-css', $this->plugin_url . '/wd/assets/css/deactivate_popup.css', array(), $this->plugin_version);
    wp_register_script($this->handle_prefix . '-deactivate-popup', $this->plugin_url . '/wd/assets/js/deactivate_popup.js', array(), $this->plugin_version, true);
    $admin_data = wp_get_current_user();
    wp_localize_script($this->handle_prefix . '-deactivate-popup', ($this->is_free == 2 ? 'cfmWDDeactivateVars' : 'fmWDDeactivateVars'), array(
    "prefix" => "fm",
    "deactivate_class" => 'fm_deactivate_link',
    "email" => $admin_data->data->user_email,
    "plugin_wd_url" => "https://10web.io/plugins/wordpress-form-maker/?utm_source=form_maker&utm_medium=free_plugin",
    ));

    wp_register_style($this->handle_prefix . '-topbar', $this->plugin_url . '/css/topbar.css', array(), $this->plugin_version);
    wp_register_style($this->handle_prefix . '-icons', $this->plugin_url . '/css/fonts.css', array(), '1.0.1');

    wp_localize_script($localize_key_all, 'fm_ajax', array(
      'ajaxnonce' => wp_create_nonce('fm_ajax_nonce'),
    ));
    wp_localize_script($localize_key_add_fields, 'fm_ajax', array(
      'ajaxnonce' => wp_create_nonce('fm_ajax_nonce'),
    ));
    wp_localize_script($localize_key_formmaker_div, 'fm_ajax', array(
      'ajaxnonce' => wp_create_nonce('fm_ajax_nonce'),
    ));
  }

  /**
   * Admin ajax scripts.
   */
  public function register_admin_ajax_scripts() {
    $fm_settings = $this->fm_settings;
    wp_register_style($this->handle_prefix . '-tables', $this->plugin_url . '/css/form_maker_tables.css', array(), $this->plugin_version);
    wp_register_style($this->handle_prefix . '-jquery-ui', $this->plugin_url . '/css/jquery-ui.custom.css', array(), $this->plugin_version);

    wp_register_script($this->handle_prefix . '-shortcode' . $this->menu_postfix, $this->plugin_url . '/js/shortcode.js', array('jquery'), $this->plugin_version);
    $google_map_key = !empty($fm_settings['map_key']) ? '&key=' . $fm_settings['map_key'] : '';

    wp_register_script('google-maps', 'https://maps.google.com/maps/api/js?v=3.exp' . $google_map_key);
    wp_register_script($this->handle_prefix . '-gmap_form', $this->plugin_url . '/js/if_gmap_back_end.js', array(), $this->plugin_version);

    wp_localize_script($this->handle_prefix . '-shortcode' . $this->menu_postfix, 'form_maker', array(
      'insert_form' => __('You must select a form', $this->prefix),
      'update' => __('Update', $this->prefix),
    ));
    wp_register_style($this->handle_prefix . '-topbar', $this->plugin_url . '/css/topbar.css', array(), $this->plugin_version);
    // Roboto font for submissions shortcode.
    wp_register_style($this->handle_prefix . '-roboto', 'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap');
  }

  /**
   * admin-ajax actions for admin.
   */
  public function form_maker_ajax() {
    $page = WDW_FM_Library(self::PLUGIN)->get('action');
    $ajax_nonce = WDW_FM_Library(self::PLUGIN)->get('nonce');

    $allowed_pages = array(
      'manage' . $this->menu_postfix,
      'manage' . $this->plugin_postfix,
      'generete_csv' . $this->plugin_postfix,
      'generete_xml' . $this->plugin_postfix,
      'formmakerwdcaptcha' . $this->plugin_postfix,
      'formmakerwdmathcaptcha' . $this->plugin_postfix,
      'product_option' . $this->plugin_postfix,
      'FormMakerEditCountryinPopup' . $this->plugin_postfix,
      'FormMakerMapEditinPopup' . $this->plugin_postfix,
      'FormMakerIpinfoinPopup' . $this->plugin_postfix,
      'show_matrix' . $this->plugin_postfix,
      'FormMakerSubmits' . $this->plugin_postfix,
      'FMShortocde' . $this->plugin_postfix,
    );
    if ( !$this->is_demo ) {
      $allowed_pages[] = 'FormMakerSQLMapping' . $this->plugin_postfix;
      $allowed_pages[] = 'select_data_from_db' . $this->plugin_postfix;
    }
    if ( !$this->is_free ) {
      $allowed_pages[] = 'paypal_info';
      $allowed_pages[] = 'checkpaypal';
    }
    $allowed_nonce_pages = array('checkpaypal', 'formmakerwdcaptcha' . $this->plugin_postfix, 'formmakerwdmathcaptcha' . $this->plugin_postfix);

    if ( !in_array($page, $allowed_nonce_pages) && wp_verify_nonce($ajax_nonce , 'fm_ajax_nonce') == FALSE ) {
      die(-1);
    }

    if ( !empty($page) && in_array($page, $allowed_pages) ) {
      if ( $page != 'formmakerwdcaptcha' . $this->plugin_postfix
        && $page != 'formmakerwdmathcaptcha' . $this->plugin_postfix
        && $page != 'checkpaypal' ) {
        if ( function_exists('current_user_can') ) {
          if ( !current_user_can('manage_options') ) {
            die('Access Denied');
          }
        }
        else {
          die('Access Denied');
        }
      }
      $page = ucfirst(substr($page, 0, strlen($page) - strlen($this->plugin_postfix)));
      $this->register_admin_ajax_scripts();
      require_once($this->plugin_dir . '/admin/controllers/' . $page . '.php');
      $controller_class = 'FMController' . $page . $this->plugin_postfix;
      $controller = new $controller_class();
      $controller->execute();
    }
  }

  /**
   * admin-ajax actions for site.
   */
  public function form_maker_ajax_frontend() {
    $page = WDW_FM_Library(self::PLUGIN)->get('page');
    $action = WDW_FM_Library(self::PLUGIN)->get('action');
	  $ajax_nonce = WDW_FM_Library(self::PLUGIN)->get('nonce');

    $allowed_pages = array(
      'form_submissions',
	    'form_maker',
    );
    $allowed_actions = array(
      'frontend_generate_xml',
      'frontend_generate_csv',
      'frontend_paypal_info',
      'frontend_show_matrix',
      'frontend_show_map',
      'get_frontend_stats',
	    'fm_reload_input',
    );

    if ( wp_verify_nonce($ajax_nonce , 'fm_ajax_nonce') == FALSE ) {
      die(-1);
    }
    if ( !empty($page) && in_array($page, $allowed_pages)
      && !empty($action) &&  in_array($action, $allowed_actions) ) {
      $this->register_frontend_ajax_scripts();
      require_once ($this->plugin_dir . '/frontend/controllers/' . $page . '.php');
      $controller_class = 'FMController' . ucfirst($page) . $this->plugin_postfix;
      $controller = new $controller_class();
      $controller->execute();
    }
  }

  /**
   * Javascript variables for admin.
   * todo: change to array.
   */
  public function form_maker_admin_ajax() {
    $upload_dir = wp_upload_dir();
    ?>
    <script>
      var fm_site_url = '<?php echo site_url() .'/'; ?>';
      var admin_url = '<?php echo admin_url('admin.php'); ?>';
      var plugin_url = '<?php echo $this->plugin_url; ?>';
      var upload_url = '<?php echo $upload_dir['baseurl']; ?>';
      var nonce_fm = '<?php echo wp_create_nonce($this->nonce); ?>';
      // Set shortcode popup dimensions.
      function fm_set_shortcode_popup_dimensions(tbWidth, tbHeight) {
        var tbWindow = jQuery('#TB_window'), H = jQuery(window).height(), W = jQuery(window).width(), w, h;
        w = (tbWidth && tbWidth < W - 90) ? tbWidth : W - 40;
        h = (tbHeight && tbHeight < H - 60) ? tbHeight : H - 40;
        if (tbWindow.length) {
          tbWindow.width(w).height(h);
          jQuery('#TB_iframeContent').width(w).height(h - 27);
          tbWindow.css({'margin-left': '-' + parseInt((w / 2), 10) + 'px'});
          if (typeof document.body.style.maxWidth != 'undefined') {
            tbWindow.css({'top': (H - h) / 2, 'margin-top': '0'});
          }
        }
      }
    </script>
    <?php
  }

  /**
   * Form maker preview shortcode output.
   *
   * @return mixed|string
   */
  public function fm_form_preview_shortcode() {
    // check is adminstrator
    if ( !current_user_can('manage_options') ) {
      echo __('Sorry, you are not allowed to access this page.', $this->prefix);
    }
    else {
      $id = WDW_FM_Library(self::PLUGIN)->get('wdform_id', 0);
      $display_options_row = WDW_FM_Library(self::PLUGIN)->display_options($id);
      $display_options_row = WDW_FM_Library::convert_json_options_to_old($display_options_row, 'display_options');
      $type = $display_options_row->type;
      $attrs = array( 'id' => $id );
      if ( $type == "embedded" ) {
        ob_start();
        $this->FM_front_end_main($attrs, $type); // embedded popover topbar scrollbox

        return str_replace(array( "\r\n", "\n", "\r" ), '', ob_get_clean());
      }
    }
  }

  /**
   * Form maker shortcode output.
   *
   * @param $attrs
   * @return mixed|string
   */
  public function fm_shortcode($attrs) {
    ob_start();
    $this->FM_front_end_main($attrs, 'embedded');

    return str_replace(array("\r\n", "\n", "\r"), '', ob_get_clean());
  }

  /**
   * Form maker output.
   *
   * @param array $params
   * @param string $type
   */
  public function FM_front_end_main($params = array(), $type = '') {
    $form_id = isset($params['id']) ? (int) $params['id'] : 0;
    if ( !isset($params['type']) ) {
      if ($this->is_free == 2) {
        wd_contact_form_maker($form_id, $type);
      }
      else {
        wd_form_maker( $form_id, $type );
      }
    }
    else if (!$this->is_free) {
      $shortcode_deafults = array(
        'id' => 0,
        'startdate' => '',
        'enddate' => '',
        'submit_date' => '',
        'submitter_ip' => '',
        'username' => '',
        'useremail' => '',
        'form_fields' => '1',
        'show' => '1,1,1,1,1,1,1,1,1,1',
      );
      shortcode_atts($shortcode_deafults, $params);

      require_once($this->plugin_dir . '/frontend/controllers/form_submissions.php');
      $controller = new FMControllerForm_submissions();

      $submissions = $controller->execute($params);

      echo $submissions;
    }
    return;
  }

  /**
   * Email verification output.
   */
  public function fm_email_verification_shortcode() {
    require_once($this->plugin_dir . '/frontend/controllers/verify_email.php');
    $controller_class = 'FMControllerVerify_email' . $this->plugin_postfix;
    $controller = new $controller_class();
    $controller->execute();
  }

  /**
   * Register email verification custom post type.
   */
  public function register_fmemailverification_cpt() {
    $args = array(
      'label' => 'FM Mail Verification',
      'public' => true,
      'exclude_from_search' => true,
      'show_in_menu' => false,
      'show_in_nav_menus' => false,
      'create_posts' => 'do_not_allow',
      'capabilities' => array(
        'create_posts' => FALSE,
        'edit_post' => 'edit_posts',
        'read_post' => 'edit_posts',
        'delete_posts' => FALSE,
      )
    );
    register_post_type(($this->is_free == 2 ? 'cfmemailverification' : 'fmemailverification'), $args);
  }

    /**
   * Register form preview custom post type.
   */
  public function register_form_preview_cpt() {
    $args = array(
      'label' => 'FM Preview',
      'public' => true,
      'exclude_from_search' => true,
      'show_in_menu' => false,
      'show_in_nav_menus' => false,
      'create_posts' => 'do_not_allow',
      'capabilities' => array(
      'create_posts' => FALSE,
      'edit_post' => 'edit_posts',
      'read_post' => 'edit_posts',
      'delete_posts' => FALSE,
      )
    );

    register_post_type('form-maker' . $this->plugin_postfix, $args);
  }

  /**
   * Frontend scripts/styles.
   */
  public function register_frontend_scripts() {
    $fm_settings = $this->fm_settings;
	  $front_plugin_url = $this->front_urls['plugin_url'];

    $required_scripts = array(
      'jquery',
      'jquery-ui-widget',
      'jquery-effects-shake',
    );
    $required_styles = array(
      $this->handle_prefix . '-googlefonts'
    );
    if ($fm_settings['fm_developer_mode']) {
      array_push($required_styles, $this->handle_prefix . '-jquery-ui', $this->handle_prefix . '-animate');
    }
    // For drag and drop on mobiles.
    wp_register_script($this->handle_prefix . '-jquery-ui-touch-punch', $this->plugin_url . '/js/jquery.ui.touch-punch.min.js', array('jquery'), '0.2.3');

    wp_register_style($this->handle_prefix . '-jquery-ui', $front_plugin_url . '/css/jquery-ui.custom.css', array(), $this->plugin_version);
    wp_register_style($this->handle_prefix . '-animate', $front_plugin_url . '/css/fm-animate.css', array(), $this->plugin_version);

    $google_map_key = !empty($fm_settings['map_key']) ? '&key=' . $fm_settings['map_key'] : '';
    wp_register_script('google-maps', 'https://maps.google.com/maps/api/js?v=3.exp' . $google_map_key);

    wp_register_script($this->handle_prefix . '-phone_field', $front_plugin_url . '/js/intlTelInput.min.js', array(), '17.0.13');
    wp_register_style($this->handle_prefix . '-phone_field_css', $front_plugin_url . '/css/intlTelInput.min.css', array(), '17.0.13');

    wp_register_script($this->handle_prefix . '-gmap_form', $front_plugin_url . '/js/if_gmap_front_end.js', array('google-maps'), $this->plugin_version);
    wp_register_style($this->handle_prefix . '-googlefonts', WDW_FM_Library(self::PLUGIN)->get_all_used_google_fonts(), null, null);
    wp_register_script($this->handle_prefix . '-signaturepad', $this->plugin_url . '/js/jquery.signaturepad.min.js', array(), '2.5.2');

    /* Getting admin language to show recaptcha language */
    $lng = get_locale();
    wp_register_script($this->handle_prefix . '-g-recaptcha', 'https://www.google.com/recaptcha/api.js?hl='.$lng.'&onload=fmRecaptchaInit&render=explicit');
    if ( isset($fm_settings['public_key']) ) {
      wp_register_script($this->handle_prefix . '-g-recaptcha-v3', 'https://www.google.com/recaptcha/api.js?hl='.$lng.'&onload=fmRecaptchaInit&render=' . $fm_settings['public_key']);
    }
    // Register admin styles to use in frontend submissions.
    wp_register_script('gmap_form_back', $front_plugin_url . '/js/if_gmap_back_end.js', array(), $this->plugin_version);

    if (!$this->is_free) {
      wp_register_script($this->handle_prefix . '-file-upload', $front_plugin_url . '/js/file-upload.js', array(), $this->plugin_version);
      wp_register_style($this->handle_prefix . '-submissions_css', $front_plugin_url . '/css/style_submissions.css', array(), $this->plugin_version);

      if (WDW_FM_Library(self::PLUGIN)->elementor_is_active() && $fm_settings['fm_developer_mode']) {
        array_push($required_styles, $this->handle_prefix . '-submissions_css');
        array_push($required_scripts, $this->handle_prefix . '-file-upload', 'gmap_form_back');
      }
    }

    if (WDW_FM_Library(self::PLUGIN)->elementor_is_active()) {
      array_push($required_scripts,
        'jquery-ui-spinner',
        'jquery-ui-datepicker',
        'jquery-ui-slider'
      );

      if ($fm_settings['fm_developer_mode']) {
        array_push($required_scripts, $this->handle_prefix . '-phone_field', $this->handle_prefix . '-gmap_form', $this->handle_prefix . '-signaturepad');
        array_push($required_styles, $this->handle_prefix . '-phone_field_css');
      }
    }

    $style_file = '/css/styles.min.css';
    $script_file = '/js/scripts.min.js';
    if ($fm_settings['fm_developer_mode']) {
      $style_file = '/css/form_maker_frontend.css';
      $script_file = '/js/main_div_front_end.js';
    }

    wp_register_style($this->handle_prefix . '-frontend', $front_plugin_url . $style_file, $required_styles, $this->plugin_version);
    wp_register_script($this->handle_prefix . '-frontend', $front_plugin_url . $script_file, $required_scripts, $this->plugin_version);

    wp_register_script($this->handle_prefix . '-frontend-momentjs', $front_plugin_url . '/js/moment.min.js', array(), '2.29.2');

    if (WDW_FM_Library(self::PLUGIN)->elementor_is_active()) {
      wp_enqueue_style($this->handle_prefix . '-frontend');
      wp_enqueue_script($this->handle_prefix . '-frontend');
    }

    wp_localize_script($this->handle_prefix . '-frontend', 'fm_objectL10n', array(
      'states' => WDW_FM_Library(self::PLUGIN)->get_states(),
      'provinces' => WDW_FM_Library(self::PLUGIN)->get_provinces_canada(),
      'plugin_url' => $front_plugin_url,
      'form_maker_admin_ajax' => admin_url('admin-ajax.php'),
      'fm_file_type_error' => addslashes(__('Can not upload this type of file', $this->prefix)),
      'fm_file_type_allowed_size_error' => addslashes(__('The file exceeds the allowed size of %s KB.', $this->prefix)),
      'fm_field_is_required' => addslashes(__('Field is required', $this->prefix)),
      'fm_min_max_check_1' => addslashes((__('The ', $this->prefix))),
      'fm_min_max_check_2' => addslashes((__(' value must be between ', $this->prefix))),
      'fm_spinner_check' => addslashes((__('Value must be between ', $this->prefix))),
      'fm_clear_data' => addslashes((__('Are you sure you want to clear saved data?', $this->prefix))),
      'fm_grading_text' => addslashes(__('Your score should be less than', $this->prefix)),
      'time_validation' => addslashes(__('This is not a valid time value.', $this->prefix)),
      'number_validation' => addslashes(__('This is not a valid number value.', $this->prefix)),
      'date_validation' => addslashes(__('This is not a valid date value.', $this->prefix)),
      'year_validation' => addslashes(sprintf(__('The year must be between %s and %s', $this->prefix), '%%start%%', '%%end%%')),
      'fm_frontend_ajax_url' => admin_url( 'admin-ajax.php' ),
    ));

    wp_localize_script($this->handle_prefix . '-frontend', 'fm_ajax', array(
      'ajaxnonce' => wp_create_nonce('fm_ajax_nonce'),
    ));
  }

  /**
   * Frontend ajax scripts.
   */
  public function register_frontend_ajax_scripts() {
	  $fm_settings = $this->fm_settings;
    $front_plugin_url = $this->front_urls['plugin_url'];
    $google_map_key = !empty($fm_settings['map_key']) ? '&key=' . $fm_settings['map_key'] : '';
    wp_register_script('google-maps', 'https://maps.google.com/maps/api/js?v=3.exp' . $google_map_key);
    wp_register_script($this->handle_prefix . '-gmap_form_back', $front_plugin_url . '/js/if_gmap_back_end.js', array(), $this->plugin_version);
  }

  /*
  * Global activate.
  *
  * @param $networkwide
  */
  public function global_activate($networkwide) {
    if ( function_exists('is_multisite') && is_multisite() ) {
      // Check if it is a network activation - if so, run the activation function for each blog id.
      if ( $networkwide ) {
        global $wpdb;
        // Get all blog ids.
        $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
        foreach ( $blogids as $blog_id ) {
          switch_to_blog($blog_id);
          $this->form_maker_on_activate();
          restore_current_blog();
        }

        return;
      }
    }
    $this->form_maker_on_activate();
  }

  public function new_blog_added( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
    if ( is_plugin_active_for_network( $this->main_file ) ) {
      switch_to_blog($blog_id);
      $this->form_maker_on_activate();
      restore_current_blog();
    }
  }

  /**
   * Activate plugin.
   */
  public function form_maker_on_activate() {
    $this->form_maker_activate();
    if ($this->is_free == 2) {
      WDCFMInsert::install_demo_forms();
    }
    else {
      WDFMInsert::install_demo_forms();
    }
    $this->init();
	// Using this insted of flush_rewrite_rule() for better performance with multisite.
    global $wp_rewrite;
    $wp_rewrite->init();
    $wp_rewrite->flush_rules();
  }

    /**
   * Global deactivate.
   *
   * @param $networkwide
   */
  public function global_deactivate($networkwide) {
    if ( function_exists('is_multisite') && is_multisite() ) {
      if ( $networkwide ) {
        global $wpdb;
        // Check if it is a network activation - if so, run the activation function for each blog id.
        // Get all blog ids.
        $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
        foreach ( $blogids as $blog_id ) {
          switch_to_blog($blog_id);
          $this->deactivate();
          restore_current_blog();
        }

        return;
      }
    }
    $this->deactivate();
  }

  /**
   * Deactivate.
   */
  public function deactivate() {
    // Using this insted of flush_rewrite_rule() for better performance with multisite.
    global $wp_rewrite;
    $wp_rewrite->init();
    $wp_rewrite->flush_rules();
  }

  /**
   * Activate plugin.
   */
  public function form_maker_activate() {
    global $wpdb;
    if (!$this->is_free) {
      deactivate_plugins("contact-form-maker/contact-form-maker.php");
      delete_transient('fm_update_check');
    }
    $version = get_option("wd_form_maker_version");
    $new_version = $this->db_version;
	  $option_key = ($this->is_free == 2 ? 'fmc_settings' : 'fm_settings');
    require_once $this->plugin_dir . "/form_maker_insert.php";

    if (!$version) {
      if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "formmaker'") == $wpdb->prefix . "formmaker") {
        deactivate_plugins($this->main_file);
        wp_die(__("Oops! Seems like you installed the update over a quite old version of Form Maker. Unfortunately, this version is deprecated.<br />Please contact 10Web support team at support@10web.io. We will take care of this issue as soon as possible.", $this->prefix));
      }
      else {
        add_option("wd_form_maker_version", $new_version, '', 'no');
        if ($this->is_free == 2) {
          WDCFMInsert::form_maker_insert();
        }
        else {
          WDFMInsert::form_maker_insert();
        }
        add_option($option_key, array('public_key' => '', 'private_key' => '', 'csv_delimiter' => ',', 'map_key' => '', 'fm_file_read' => 0, 'ajax_export_per_page' => 1000));
      }
    }
    elseif (version_compare($version, $new_version, '<')) {
      $version = substr_replace($version, '1.', 0, 2);
      require_once $this->plugin_dir . "/form_maker_update.php";
      $mail_verification_post_ids = $wpdb->get_results($wpdb->prepare('SELECT mail_verification_post_id FROM ' . $wpdb->prefix . 'formmaker WHERE mail_verification_post_id != %d', 0));
      if ( !empty($mail_verification_post_ids) ) {
        foreach ($mail_verification_post_ids as $mail_verification_post_id) {
          $update_email_ver_post_type = array(
            'ID' => (int) $mail_verification_post_id->mail_verification_post_id,
            'post_type' => ($this->is_free == 2 ? 'cfmemailverification' : 'fmemailverification'),
          );
          wp_update_post($update_email_ver_post_type);
        }
      }
      if ($this->is_free == 2) {
        WDCFMUpdate::form_maker_update($version);
      }
      else {
        WDFMUpdate::form_maker_update($version);
      }
      update_option("wd_form_maker_version", $new_version);
      $fm_settings = get_option($option_key);
      if ( $fm_settings === FALSE ) {
          $recaptcha_keys = $wpdb->get_row('SELECT `public_key`, `private_key` FROM ' . $wpdb->prefix . 'formmaker WHERE public_key!="" and private_key!=""', ARRAY_A);
          $public_key = isset($recaptcha_keys['public_key']) ? $recaptcha_keys['public_key'] : '';
          $private_key = isset($recaptcha_keys['private_key']) ? $recaptcha_keys['private_key'] : '';
          $option_value = array(
              'public_key' => $public_key,
              'private_key' => $private_key,
              'csv_delimiter' => ',',
              'map_key' => '',
              'fm_advanced_layout' => 0,
              'fm_enable_wp_editor' => 1,
              'fm_antispam_referer' => 0,
              'fm_antispam_bot_validation' => 0,
              'fm_antispam_nonce' => 0,
              'fm_block_ip_exceeded_limit' => 0,
              'fm_developer_mode' => 0,
              'fm_file_read' => 0,
              'ajax_export_per_page' => 1000);
          add_option($option_key, $option_value);
	    }
      if ( !isset($fm_settings['fm_enable_wp_editor']) ) {
        $fm_settings['fm_enable_wp_editor'] = 1;
        update_option( $option_key, $fm_settings );
      }
      if ( !isset($fm_settings['fm_antispam_referer']) ) {
        $fm_settings['fm_antispam_referer'] = 0;
        update_option( $option_key, $fm_settings );
      }
      if ( !isset($fm_settings['fm_antispam_bot_validation']) ) {
        $fm_settings['fm_antispam_bot_validation'] = 0;
        update_option( $option_key, $fm_settings );
      }
      if ( !isset($fm_settings['fm_antispam_nonce']) ) {
        $fm_settings['fm_antispam_nonce'] = 0;
        update_option( $option_key, $fm_settings );
      }
      if ( !isset($fm_settings['fm_block_ip_exceeded_limit']) ) {
        $fm_settings['fm_block_ip_exceeded_limit'] = 0;
        update_option( $option_key, $fm_settings );
      }
      if ( !isset($fm_settings['fm_developer_mode']) ) {
        $fm_settings['fm_developer_mode'] = 0;
        update_option( $option_key, $fm_settings );
      }
      if ( !isset($fm_settings['fm_file_read']) ) {
        $fm_settings['fm_file_read'] = 0;
        update_option( $option_key, $fm_settings );
      }
    }
  }

  /**
   * Form maker overview.
   */
  public function fm_overview() {
    if (is_admin() && !isset($_REQUEST['ajax'])) {
      if (!class_exists("TenWebLibNew")) {
        $plugin_dir = apply_filters('tenweb_free_users_lib_path', array('version' => '1.1.1', 'path' => $this->plugin_dir));
        require_once($plugin_dir['path'] . '/wd/start.php');
      }
      global $fm_options;
      $fm_options = array(
        "prefix" => ($this->is_free == 2 ? 'cfm' : 'fm'),
        "wd_plugin_id" => ($this->is_free == 2 ? 183 : 31),
	      "plugin_id" => ($this->is_free == 2 ? 95 : 95),
        "plugin_title" => ($this->is_free == 2 ? 'Contact Form Maker' : 'Form Maker'),
        "plugin_wordpress_slug" => ($this->is_free == 2 ? 'contact-form-maker' : 'form-maker'),
        "plugin_dir" => $this->plugin_dir,
        "plugin_main_file" => __FILE__,
        "description" => ($this->is_free == 2 ? __('WordPress Contact Form Maker is a simple contact form builder, which allows the user with almost no knowledge of programming to create and edit different type of contact forms.', $this->prefix) : __('Form Maker plugin is a modern and advanced tool for easy and fast creating of a WordPress Form. The backend interface is intuitive and user friendly which allows users far from scripting and programming to create WordPress Forms.', $this->prefix)),
        "plugin_features" => array(
          0 => array(
            "title" => __("Easy to Use", $this->prefix),
            "description" => __("This responsive form maker plugin is one of the most easy-to-use form builder solutions available on the market. Simple, yet powerful plugin allows you to quickly and easily build any complex forms.", $this->prefix),
          ),
          1 => array(
            "title" => __("Customizable Fields", $this->prefix),
            "description" => __("All the fields of Form Maker plugin are highly customizable, which allows you to change almost every detail in the form and make it look exactly like you want it to be.", $this->prefix),
          ),
          2 => array(
            "title" => __("Submissions", $this->prefix),
            "description" => __("You can view the submissions for each form you have. The plugin allows to view submissions statistics, filter submission data and export in csv or xml formats.", $this->prefix),
          ),
          3 => array(
            "title" => __("Multi-Page Forms", $this->prefix),
            "description" => __("With the form builder plugin you can create muilti-page forms. Simply use the page break field to separate the pages in your forms.", $this->prefix),
          ),
          4 => array(
            "title" => __("Themes", $this->prefix),
            "description" => __("The WordPress Form Maker plugin comes with a wide range of customizable themes. You can choose from a list of existing themes or simply create the one that better fits your brand and website.", $this->prefix),
          )
        ),
        "user_guide" => array(
          0 => array(
            "main_title" => __("Installing", $this->prefix),
            "url" => "https://help.10web.io/hc/en-us/articles/360015435831-Introducing-Form-Maker-Plugin?utm_source=form_maker&utm_medium=free_plugin",
            "titles" => array()
          ),
          1 => array(
            "main_title" => __("Creating a new Form", $this->prefix),
            "url" => "https://help.10web.io/hc/en-us/articles/360015244232-Creating-a-Form-on-WordPress?utm_source=form_maker&utm_medium=free_plugin",
            "titles" => array()
          ),
          2 => array(
            "main_title" => __("Configuring Form Options", $this->prefix),
            "url" => "https://help.10web.io/hc/en-us/articles/360015862812-Settings-General-Options?utm_source=form_maker&utm_medium=free_plugin",
            "titles" => array()
          ),
          3 => array(
            "main_title" => __("Description of The Form Fields", $this->prefix),
            "url" => "https://help.10web.io/hc/en-us/articles/360016081951-Form-Fields-Basic?utm_source=form_maker&utm_medium=free_plugin",
            "titles" => array(
              array(
                "title" => __("Selecting Options from Database", $this->prefix),
                "url" => "https://help.10web.io/hc/en-us/articles/360015862632-Selecting-Options-from-Database?utm_source=form_maker&utm_medium=free_plugin",
              ),
            )
          ),
          4 => array(
            "main_title" => __("Publishing the Created Form", $this->prefix),
            "url" => "https://help.10web.io/hc/en-us/articles/360016083211-Additional-Publishing-Options?utm_source=form_maker&utm_medium=free_plugin",
            "titles" => array()
          ),
          5 => array(
            "main_title" => __("Blocking IPs", $this->prefix),
            "url" => "https://help.10web.io/hc/en-us/articles/360015863292-Managing-Form-Submissions?utm_source=form_maker&utm_medium=free_plugin",
            "titles" => array()
          ),
          6 => array(
            "main_title" => __("Managing Submissions", $this->prefix),
            "url" => "https://help.10web.io/hc/en-us/articles/360015863292-Managing-Form-Submissions?utm_source=form_maker&utm_medium=free_plugin",
            "titles" => array()
          ),
          7 => array(
            "main_title" => __("Publishing Submissions", $this->prefix),
            "url" => "https://help.10web.io/hc/en-us/articles/360016083211-Additional-Publishing-Options?utm_source=form_maker&utm_medium=free_plugin",
            "titles" => array()
          ),
        ),
        "video_youtube_id" => "tN3_c6MhqFk",
        "plugin_wd_url" => "https://10web.io/plugins/wordpress-form-maker/?utm_source=form_maker&utm_medium=free_plugin",
        "plugin_wd_demo_link" => "https://demo.10web.io/form-maker?utm_source=form_maker&utm_medium=free_plugin",
        "plugin_wd_addons_link" => "https://10web.io/plugins/wordpress-form-maker/?utm_source=form_maker&utm_medium=free_plugin#plugin_extensions",
        "plugin_wd_docs_link" => "https://help.10web.io/hc/en-us/sections/360002133951-Form-Maker-Documentation/?utm_source=form_maker&utm_medium=free_plugin",
        "after_subscribe" => admin_url('admin.php?page=manage_' . ($this->is_free == 2 ? 'cfm' : 'fm')), // this can be plagin overview page or set up page
        "plugin_wizard_link" => '',
        "plugin_menu_title" => $this->nicename,
        "plugin_menu_icon" => $this->plugin_url . '/images/FormMakerLogo-16.png',
        "deactivate" => ($this->is_free ? true : false),
        "subscribe" => false,
        "custom_post" => 'manage' . $this->menu_postfix,
        "menu_position" => null,
        "display_overview" => false,
      );

      ten_web_new_lib_init($fm_options);
    }
  }

  /**
   * Add media button to Wp editor.
   *
   * @param $context
   *
   * @return string
   */
  function media_button() {
    $fm_nonce = wp_create_nonce('fm_ajax_nonce');
    ob_start();
    $url = add_query_arg(array('action' => 'FMShortocde' . $this->plugin_postfix, 'task' => 'forms', 'nonce' => $fm_nonce, 'TB_iframe' => '1'), admin_url('admin-ajax.php'));
    ?>
    <a onclick="tb_click.call(this); fm_set_shortcode_popup_dimensions(400, 140); return false;" href="<?php echo $url; ?>" class="button" title="<?php _e('Insert Form', $this->prefix); ?>">
      <span class="wp-media-buttons-icon" style="background: url('<?php echo $this->plugin_url; ?>/images/fm-media-form-button.png') no-repeat scroll left top rgba(0, 0, 0, 0);"></span>
      <?php _e('Add Form', $this->prefix); ?>
    </a>
    <?php
    $url = add_query_arg(array('action' => 'FMShortocde' . $this->plugin_postfix, 'task' => 'submissions', 'nonce' => $fm_nonce, 'TB_iframe' => '1'), admin_url('admin-ajax.php'));
    ?>
    <a onclick="tb_click.call(this); fm_set_shortcode_popup_dimensions(520, 570); return false;" href="<?php echo $url; ?>" class="button" title="<?php _e('Insert submissions', $this->prefix); ?>">
      <span class="wp-media-buttons-icon" style="background: url(<?php echo $this->plugin_url; ?>/images/fm-media-submissions-button.png) no-repeat scroll left top rgba(0, 0, 0, 0);"></span>
      <?php _e('Add Submissions', $this->prefix); ?>
    </a>
    <?php
    echo ob_get_clean();
  }


  /**
   * Check extensions version compatibility with FM.
   *
   */
  function fm_check_addons_compatibility() {
    // version - addon maximal version which is compatible with current version of form maker.
    // fm_version - form maker minimal version which is compatible with current version of addon.
    $add_ons = array(
      'form-maker-calculator' => array(
        'version' => '1.1.8',
        'fm_version' => '2.13.0',
        'file' => 'fm_calculator.php',
      ),
      'form-maker-conditional-emails' => array(
        'version' => '1.1.6',
        'fm_version' => '2.13.0',
        'file' => 'fm_conditional_emails.php',
      ),
      'form-maker-dropbox-integration' => array(
        'version' => '1.2.8',
        'fm_version' => '2.14.0',
        'file' => 'fm_dropbox_integration.php',
      ),
      'form-maker-export-import' => array(
        'version' => '2.1.11',
        'fm_version' => '2.14.0',
        'file' => 'fm_exp_imp.php',
      ),
      'form-maker-gdrive-integration' => array(
        'version' => '1.1.7',
        'fm_version' => '2.14.0',
        'file' => 'fm_gdrive_integration.php',
      ),
      'form-maker-mailchimp' => array(
        'version' => '1.1.11',
        'fm_version' => '2.14.0',
        'file' => 'fm_mailchimp.php',
      ),
      'form-maker-pdf-integration' => array(
        'version' => '1.1.7',
        'fm_version' => '2.13.0',
        'file' => 'fm_pdf_integration.php',
      ),
      'form-maker-post-generation' => array(
        'version' => '1.1.10',
        'fm_version' => '2.14.0',
        'file' => 'fm_post_generation.php',
      ),
      'form-maker-pushover' => array(
        'version' => '1.1.7',
        'fm_version' => '2.14.0',
        'file' => 'fm_pushover.php',
      ),
      'form-maker-reg' => array(
        'version' => '1.2.11',
        'fm_version' => '2.14.0',
        'file' => 'fm_reg.php',
      ),
      'form-maker-save-progress' => array(
        'version' => '1.1.14',
        'fm_version' => '2.14.10',
        'file' => 'fm_save.php',
      ),
      'form-maker-stripe' => array(
        'version' => '1.2.11',
        'fm_version' => '2.15.5',
        'file' => 'fm_stripe.php',
      ),
      'form-maker-webhooks' => array(
        'version' => '1.0.5',
        'fm_version' => '2.14.0',
        'file' => 'fm_webhooks.php',
      ),
    );

    $add_ons_notice = array();
    $add_ons_need_higher_version = array();
    $max_required_version = $this->db_version;
    include_once($this->abspath . 'wp-admin/includes/plugin.php');

    foreach ( $add_ons as $add_on_key => $add_on_value ) {
      $addon_path = plugin_dir_path(dirname(__FILE__)) . $add_on_key . '/' . $add_on_value['file'];
      if ( is_plugin_active($add_on_key . '/' . $add_on_value['file']) ) {
        $addon = get_plugin_data($addon_path); // array
        if ( version_compare($addon['Version'], $add_on_value['version'], '<') ) {
          // deactivate_plugins($addon_path);
          array_push($add_ons_notice, $addon['Name']);
        }
      }
      if ( version_compare($this->db_version, $add_on_value['fm_version']) == -1 ) {
        array_push($add_ons_need_higher_version, $add_on_key);
      }
      if ( version_compare($max_required_version, $add_on_value['fm_version']) == -1 ) {
        $max_required_version = $add_on_value['fm_version'];
      }
    }

    if ( !empty($add_ons_notice) ) {
      $this->fm_addons_compatibility_notice($add_ons_notice);
    }
    if ( !empty($add_ons_need_higher_version) ) {
      $this->fm_compatibility_notice($max_required_version);
    }
  }

  /**
   * Incompatibility message.
   *
   * @param $add_ons_notice
   */
  function fm_addons_compatibility_notice( $add_ons_notice ) {
    $addon_names = implode(', ', $add_ons_notice);
    $count = count($add_ons_notice);
    $single = __('The current version of %s extension is not compatible with Form Maker. Some functions may not work correctly. Please update the extension to fully use its features.', $this->prefix);
    $plural = __('The current version of %s extensions are not compatible with Form Maker. Some functions may not work correctly. Please update the extensions to fully use its features.', $this->prefix);
    echo '<div class="error"><p>' . sprintf(_n($single, $plural, $count, $this->prefix), $addon_names) . '</p></div>';
  }

  function fm_compatibility_notice( $max_required_version ) {
    $message = __('Please install %s plugin version %s and higher to start using add-on.', 'form_maker');
    echo '<div class="error"><p>' . sprintf($message, 'Form Maker', $max_required_version) . '</p></div>';
  }

  public function add_query_vars_seo( $vars ) {
    $vars[] = 'form_id';

    return $vars;
  }

  /**
   * Prevent adding shortcode conflict with some builders.
   */
  private function before_shortcode_add_builder_editor() {
    if ( defined('ELEMENTOR_VERSION') ) {
      add_action('elementor/editor/before_enqueue_scripts', array( $this, 'form_maker_admin_ajax' ));
    }
    if ( class_exists('FLBuilder') ) {
      add_action('wp_enqueue_scripts', array( $this, 'form_maker_admin_ajax' ));
    }
  }

  public function webinar_banner() {
    // Webinar banner
    if ( !class_exists('TWFMWebinar') ) {
      require_once($this->plugin_dir . '/framework/TWWebinar.php');
    }
    new TWFMWebinar(array(
                      'menu_postfix' => $this->menu_postfix,
                      'title' => 'Join the Webinar',
                      'description' => 'How to Create a Fully Functional WP Website with Various Forms in Just an Hour + SPECIAL GIFT FOR WEBINAR ATTENDEES',
                      'preview_type' => 'youtube',
                      'preview_url' => 'Ry2hDk3LtPk',
                      'button_text' => 'SIGN UP',
                      'button_link' => 'https://my.demio.com/ref/qWIW655LXhVTdRoY',
                    ));
  }

  /**
   * Stripe events processing logic.
   */
  public function stripe_events_processing_logic() {
    if ( !class_exists('Stripe\Stripe') ) {
      require_once WD_FM_STRIPE_DIR . '/stripe/init.php';
    }
    $payload = @file_get_contents('php://input');
    $event = NULL;
    try {
      $event = \Stripe\Event::constructFrom(json_decode($payload, TRUE));
    }
    catch ( \UnexpectedValueException $e ) {
      // Invalid payload
      http_response_code(400);
      exit();
    }
    switch ( $event->type ) {
      case 'charge.captured':
        $endpoint_object = $event->data->object;
        if ( $endpoint_object->captured ) {
          global $wpdb;
          $group_id = $wpdb->get_var($wpdb->prepare("SELECT group_id FROM " . $wpdb->prefix . "formmaker_submits WHERE element_value = '%s'", $event->data->object->payment_intent));
          if ( !class_exists('WD_FM_STRIPE_model') ) {
            require_once WD_FM_STRIPE_DIR . '/model.php';
          }
          $model = new WD_FM_STRIPE_model();
          if ( $endpoint_object->amount !== $endpoint_object->amount_captured ) {
            $capture_less_data = array(
              'amount' => strtoupper($endpoint_object->currency) . " " . $endpoint_object->amount / 100,
              'amount_captured' => strtoupper($endpoint_object->currency) . " " . $endpoint_object->amount_captured / 100,
            );
          }
          else {
            $capture_less_data = array();
          }
          $model->update_stripe_status($group_id, $capture_less_data);
        }
        break;
      default:
        error_log('Some other event');
    }
  }

  /**
   * Register endpoint for stripe events
   */
  public function register_endpoint_for_stripe_events() {
    register_rest_route('form_maker/v1', 'stripe_events', array(
      'methods' => "POST",
      'callback' => array( $this, 'stripe_events_processing_logic' ),
      'permission_callback' => '__return_true',
    ));
  }
}

/**
 * Main instance of WDFM.
 *
 * @return WDFM The main instance to prevent the need to use globals.
 */
if ( !function_exists('WDFMInstance') ) {
  function WDFMInstance( $version ) {
    if ( $version == 2 ) {
      return WDCFM::instance();
    }

    return WDFM::instance();
  }
}
WDFMInstance(1);
if ( !function_exists('WDW_FM_Library') ) {
  function WDW_FM_Library( $version = 1 ) {
    if ( $version == 2 ) {
      return WDW_FMC_Library::instance();
    }

    return WDW_FM_Library::instance();
  }
}
/**
 * Form maker output.
 *
 * @param        $id
 * @param string $type
 */
function wd_form_maker( $id, $type = 'embedded' ) {
  require_once(WDFMInstance(1)->plugin_dir . '/frontend/controllers/form_maker.php');
  $controller = new FMControllerForm_maker();
  $form = $controller->execute($id, $type);
  echo $form;
}

function fm_add_plugin_meta_links( $meta_fields, $file ) {
  if ( plugin_basename(__FILE__) == $file ) {
    $plugin_url = "https://wordpress.org/support/plugin/form-maker";
    $prefix = WDFMInstance(1)->prefix;
    $meta_fields[] = "<a href='" . $plugin_url . "/#new-post' target='_blank'>" . __('Ask a question', $prefix) . "</a>";
    $meta_fields[] = "<a href='" . $plugin_url . "/reviews#new-post' target='_blank' title='" . __('Rate', $prefix) . "'>
            <i class='wdi-rate-stars'>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "</i></a>";

    $stars_color = "#ffb900";

    echo "<style>"
      . ".wdi-rate-stars{display:inline-block;color:" . $stars_color . ";position:relative;top:3px;}"
      . ".wdi-rate-stars svg{fill:" . $stars_color . ";}"
      . ".wdi-rate-stars svg:hover{fill:" . $stars_color . "}"
      . ".wdi-rate-stars svg:hover ~ svg{fill:none;}"
      . "</style>";
  }

  return $meta_fields;
}

if ( WDFMInstance(1)->is_free ) {
  add_filter("plugin_row_meta", 'fm_add_plugin_meta_links', 10, 2);
}

require_once(WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__)) . '/booster/init.php');
add_action('init', function() {
  TWB(array(
        'submenu' => array(
          'parent_slug' => 'manage_fm',
          'title' => 'Speed Optimization',
        ),
        'page' => array(
          'slug' => 'form-maker',
          'section_booster_title' => 'Optimize forms and increase your conversions',
          'section_booster_desc' => 'Use the free 10Web Booster plugin to automatically optimize pages with forms and boost performance.',
          'section_booster_success_title' => 'Optimize pages with forms',
          'section_booster_success_desc' => 'Improve website performance',
          'section_optimize_images' => FALSE,
          'section_analyze_desc' => 'Speed up your website and increase conversions by optimizing all pages that include forms.',
        ),
      ));
}, 11);
