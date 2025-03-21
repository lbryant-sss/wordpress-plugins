<?php
if (false === class_exists('WF_Licensing_CSMM')) {
  class WF_Licensing_CSMM
  {
    public $prefix = '';
    private $licensing_servers = array();
    private $version = '';
    private $slug = '';
    private $basename = '';
    private $plugin_page = '';
    private $plugin_file = '';
    private $js_folder = '';
    protected $api_ver = 'v1/';
    protected $valid_forever = '2035-01-01';
    protected $unlimited_installs = 99999;
    public $disable_remote = true;
    public $debug = false;
    public $filesystem_initialized = false;


    /**
     * Init licensing by setting up various params and hooking into actions.
     *
     * @param array $params Prefix, licensing_servers, version, plugin_file, skip_hooks
     *
     * @return void
     */
    function __construct($params)
    {
      $this->prefix = trim($params['prefix']);
      $this->licensing_servers = $params['licensing_servers'];
      $this->version = trim($params['version']);
      $this->slug = dirname(plugin_basename(trim($params['plugin_file'])));
      $this->basename = plugin_basename(trim($params['plugin_file']));
      $this->plugin_file = $params['plugin_file'];
      $this->plugin_page = trim($params['plugin_page']);
      $this->disable_remote = !empty($params['disable_remote']);
      $this->debug = !empty($params['debug']);

      if ($params['js_folder']) {
        $this->js_folder = trim($params['js_folder']);
      } else {
        $this->js_folder = plugin_dir_url($this->plugin_file) . 'js/';
      }

      if (empty($params['skip_hooks'])) {
        register_activation_hook($this->plugin_file, array($this, 'activate_plugin'));
        register_deactivation_hook($this->plugin_file, array($this, 'deactivate_plugin'));

        add_action('init', array($this, 'init'));

        add_action('wp_ajax_wf_licensing_' . $this->prefix . '_validate', array($this, 'validate_ajax'));
        add_action('wp_ajax_wf_licensing_' . $this->prefix . '_save', array($this, 'save_ajax'));

        add_action('wp_ajax_wf_licensing_' . $this->prefix . '_deactivate', array($this, 'deactivate_ajax'));
      }

      $this->log('__construct', $params, get_object_vars($this));
    } // __construct


    /**
     * Actions performed on WP init action.
     *
     * @return void
     */
    function init()
    {
      if (is_admin()) {
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
      }
    } // init


    function admin_enqueue_scripts() {
      $current_screen = get_current_screen();

      if (empty($current_screen->id) || $current_screen->id != $this->plugin_page) {
        return false;
      }

      $vars = array(
        'prefix' => $this->prefix,
        'debug' => $this->debug,
        'nonce' => wp_create_nonce('wf_licensing_' . $this->prefix),
        'licensing_endpoint' => $this->licensing_servers[0] . $this->api_ver,
        'request_data' => array(
          'action' => 'validate_license',
          'license_key' => '',
          'rand' => wp_rand(1000, 9999),
          'version' => $this->version,
          'wp_version' => get_bloginfo('version'),
          'site_url' => get_home_url(),
          'site_title' => get_bloginfo('name'),
          'meta' => array()
        )
      );

      wp_enqueue_script('wf_licensing', $this->js_folder . 'wf-licensing.js', array(), 1.0, true);
      wp_localize_script('wf_licensing', 'wf_licensing_' . $this->prefix, $vars);
    } // admin_enqueue_scripts

    /**
     * Log message if debugging is enabled.
     * Log file: /wp-content/wf-licensing.log
     *
     * @param string $message Message to write to log.
     * @param mixed $data Optional, extra data to write to debug log.
     *
     * @return void
     */
    function log($message, ...$data)
    {
      global $wp_filesystem;
        
      if (!$this->debug) {
        return;
      }

      global $wp_filesystem;
      $this->wp_init_filesystem();

      $log_file = trailingslashit(WP_CONTENT_DIR) . 'wf-licensing.log';
      
      if ( ! $wp_filesystem->exists( $log_file ) ) {
        $wp_filesystem->put_contents( $log_file, '' ); // Create an empty file if it doesn't exist.
      }

      $log_content = $wp_filesystem->get_contents( $log_file ); // wp_filesystem can't append so we have to read existing content

      $log_content .= '[' . gmdate('r') . '] ' . $this->prefix . ': ';
      $log_content .= (string) $message . PHP_EOL;
      foreach ($data as $tmp) {
        // phpcs:ignore as this is only used for debug reasons
        $log_content .= var_export($tmp, true) . PHP_EOL; //phpcs:ignore
      }
      $log_content .= PHP_EOL;
      $wp_filesystem->put_contents( $log_file, $log_content );
    } // log

    /**
     * Fetches license details from the database.
     *
     * @param string $key If set returns only requested options key.
     *
     * @return string
     */
    function get_license($key = '')
    {
      $default = array(
        'license_key' => '',
        'error' => '',
        'valid_until' => '',
        'last_check' => 0,
        'name' => '',
        'access_key' => '',
        'meta' => array()
      );

      $options = get_option('wf_licensing_' . $this->prefix, array());
      $options = array_merge($default, $options);

      if (empty($options['access_key'])) {
        $options['access_key'] = $this->generate_access_key();
        $this->update_license($options);
      }

      if (!empty($key)) {
        return $options[$key];
      } else {
        return $options;
      }
    } // get_license


    function generate_access_key()
    {
      $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $pieces = array();
      $max = strlen($keyspace) - 1;

      for ($i = 0; $i < 32; ++$i) {
        $pieces[] = $keyspace[random_int(0, $max)];
      }
      return implode('', $pieces);
    } // generate_access_key


    function get_license_formatted($key = '')
    {
      $license = $this->get_license();
      $out = array(
        'name' => '',
        'name_long' => '',
        'valid_until' => '',
        'expires' => '',
        'license_key' => '',
        'license_key_hidden' => '',
        'recurring' => false,
        'keyless' => false,
      );

      if (!$this->is_active()) {
        return $out;
      }
      $license['valid_until'] = $license['valid_until'];

      $out['name'] = $license['name'];
      $out['name_long'] = $license['name'];
      if ($license['meta']) {
        $tmp = '';
        foreach ($license['meta'] as $meta => $meta_value) {

          if ($meta[0] == '_' || filter_var($meta_value, FILTER_VALIDATE_BOOLEAN) != true) {
            continue;
          }
          $meta = str_replace('_', ' ', $meta);
          $meta = ucwords($meta);
          $tmp .= $meta . ', ';
        }
        $tmp = trim($tmp, ', ');
        if ($tmp) {
          $out['name_long'] .= ' with ' . $tmp;
        }
      }

      if ($license['valid_until'] == $this->valid_forever) {
        $out['valid_until'] = 'forever';
        $out['recurring'] = false;
      } else {
        $out['valid_until'] = 'until ' . gmdate(get_option('date_format'), strtotime($license['valid_until']));
        $out['recurring'] = true;
      }

      if (gmdate('Y-m-d') == $license['valid_until']) {
        $out['expires'] = 'today';
      } elseif (gmdate('Y-m-d', time() + 30 * DAY_IN_SECONDS) > $license['valid_until']) {
        $tmp = (strtotime($license['valid_until'] . gmdate(' G:i:s')) - time()) / DAY_IN_SECONDS;
        $out['expires'] = 'in ' . round($tmp) . ' days';
      } else {
        $out['expires'] = 'in more than 30 days';
      }

      if (empty($license['license_key']) || $license['license_key'] == 'keyless') {
        $out['keyless'] = true;
      } else {
        $out['keyless'] = false;
        $out['license_key'] = $license['license_key'];
        $tmp = strlen($license['license_key']);
        $dash = false;
        $new = '';
        for ($i = $tmp - 1; $i >= 0; $i--) {
          if ($dash == false || $out['license_key'][$i] == '-') {
            $new = $out['license_key'][$i] . $new;
          } else {
            $new = '*' . $new;
          }
          if ($out['license_key'][$i] == '-') {
            $dash = true;
          }
        }
        $out['license_key_hidden'] = $new;
      }

      $out = apply_filters('wf_licensing_license_formatted_' . $this->prefix, $out);

      if (!empty($key)) {
        return $out[$key];
      } else {
        return $out;
      }
    } // get_license_formatted


    /**
     * Updates license details in the database.
     *
     * @param string $data License data to save; or empty to delete license
     *
     * @return bool
     */
    function update_license($data = false)
    {
      if (false === $data) {
        $tmp = delete_option('wf_licensing_' . $this->prefix);
      } else {
        if (!isset($data['access_key'])) {
          $data['access_key'] = $this->get_license('access_key');
        }
        $tmp = update_option('wf_licensing_' . $this->prefix, $data);
      }

      return $tmp;
    } // update_license


    /**
     * Check if license is valid
     *
     * @param string $feature If set it checks for a specific feature.
     * @param bool $force_check Forces license recheck on server instead of just cached values.
     *
     * @return boolean
     */
    function is_active($feature = '', $force_check = false, $local_only = false)
    {
      $last_check = $this->get_license('last_check');
      if ($local_only == false) {
        if ($force_check || ($last_check && ($last_check + HOUR_IN_SECONDS * 8) < time())) {
          $this->log('auto recheck license');
          $this->validate();
        }
      }

      $license = $this->get_license();

      if (
        !empty($license['license_key']) && !empty($license['name']) &&
        !empty($license['valid_until']) && $license['valid_until'] >= gmdate('Y-m-d')
      ) {
        if (!empty($feature)) {
          if (!empty($license['meta'][$feature]) && filter_var($license['meta'][$feature], FILTER_VALIDATE_BOOLEAN) == true) {
            return true;
          } else {
            return false;
          }
        } else {
          return true;
        }
      } else {
        return false;
      }
    } // is_active


    /**
     * Hook to plugin activation action.
     * If there's a license key, try to activate & write response.
     *
     * @return void
     */
    function activate_plugin()
    {
      $license = $this->get_license();
      if ($this->is_active() || !$license['license_key']) {
        return false;
      }

      $tmp = $this->validate();
      if ($tmp) {
        $this->log('activating plugin, license activated');
        return true;
      } else {
        $this->log('activating plugin, unable to activate license');
        return false;
      }
    } // activate_plugin


    /**
     * Hook to plugin deactivation action.
     * If there's a license key, try to deactivate & write response.
     *
     * @return void
     */
    function deactivate_plugin()
    {
      if (!$this->is_active()) {
        return false;
      }

      $license = $this->get_license();
      $result = $this->query_licensing_server('deactivate_license');

      if (is_wp_error($result) || !is_array($result) || !isset($result['success']) || $result['success'] == false) {
        $this->log('unable to deactivate license');

        return false;
      } else {
        $license['error'] = '';
        $license['name'] = '';
        $license['valid_until'] = '';
        $license['meta'] = '';
        $license['last_check'] = 0;
        $this->update_license($license);
        $this->log('license deactivated');

        return true;
      }
    } // deactivate_plugin


    /**
     * Use when uninstalling (deleting) the plugin to clean up.
     *
     * @param string $prefix Same prefix as used when initialising the class.
     * @return bool
     */
    static function uninstall_plugin($prefix)
    {
      $tmp = delete_option('wf_licensing_' . $prefix);

      return $tmp;
    } // uninstall_plugin


    /**
     * Delete license locally and send deactivate ping to licensing server
     *
     * @return void
     */
    function deactivate() {
      $license = $this->get_license();
      $result = $this->query_licensing_server('deactivate_license', array());
      $this->update_license(false);

      return $result;
    } // deactivate

    /**
     * Validate license key on server and save response.
     *
     * @param string $license_key License key, or leave empty to pull from saved.
     *
     * @return void
     */
    function validate($license_key = '')
    {
      $license = $this->get_license();
      if (empty($license_key)) {
        $license_key = $license['license_key'];
      }

      $out = array(
        'license_key' => $license_key,
        'error' => '',
        'name' => '',
        'last_check' => 0,
        'valid_until' => '',
        'meta' => array()
      );

      $result = $this->query_licensing_server('validate_license', array('license_key' => $license_key));

      if (is_wp_error($result)) {
        $out['error'] = 'Error querying licensing server. ' .  $result->get_error_message() . ' Please try again in a few moments.';
        $this->update_license($out);

        return false;
      } elseif (!is_array($result) || !isset($result['success'])) {
        $out['error'] = 'Invalid response from licensing server. Please try again in a few moments.';
        $this->update_license($out);

        return false;
      } elseif ($result['success'] == false) {
        $out['error'] = $result['data'];
        $this->update_license($out);

        return true;
      } else {
        $out['error'] = $result['data']['error'];
        $out['name'] = $result['data']['name'];
        $out['valid_until'] = $result['data']['valid_until'];
        $out['meta'] = $result['data']['meta'];
        $out['last_check'] = time();
        $this->update_license($out);

        return true;
      }
    } // validate


    function validate_ajax()
    {
      check_ajax_referer('wf_licensing_' . $this->prefix);
      
      if (false === current_user_can('manage_options')) {
        wp_die('Sorry, you have to be an admin to run this action.');
      }

      if(!isset($_REQUEST['license_key'])){
          wp_send_json_error('Missing license key');
      }

      $license_key = sanitize_text_field(wp_unslash($_REQUEST['license_key']));
      $license_key = trim(substr($license_key, 0, 64));
      if (empty($license_key)) {
        $this->update_license(false);
        do_action('wf_licensing_' . $this->prefix . '_validate_ajax', $license_key, false);

        wp_send_json_success();
      } else {
        $result = $this->validate($license_key);
        $license = $this->get_license();
        do_action('wf_licensing_' . $this->prefix . '_validate_ajax', $license_key, $result);

        if ($result == true) {
          wp_send_json_success($result);
        } else {
          wp_send_json_error($license);
        }
      }
    } // validate_ajax


    function deactivate_ajax()
    {
      check_ajax_referer('wf_licensing_' . $this->prefix);

      if (false === current_user_can('manage_options')) {
        wp_die('Sorry, you have to be an admin to run this action.');
      }

      $old_license = $this->get_license();
      $result = $this->deactivate();
      do_action('wf_licensing_' . $this->prefix . 'deactivate_ajax', $old_license, $result);
      wp_send_json_success($result);
    } // deactivate_ajax


    function save_ajax()
    {
      check_ajax_referer('wf_licensing_' . $this->prefix);

      if (false === current_user_can('manage_options')) {
        wp_die('Sorry, you have to be an admin to run this action.');
      }

      if(!isset($_POST['license_key'])){
        wp_send_json_error('Missing license key');
      }

      $license_key = sanitize_text_field(wp_unslash($_POST['license_key']));
      $license_key = trim(substr($license_key, 0, 64));
      $out['license_key'] = $license_key;

      if(isset($_POST['data'])){
        $data = array_map('sanitize_text_field', wp_unslash($_POST['data']));
      } else {
        $data = array();
      }

      if (isset($_POST['success']) && sanitize_text_field(wp_unslash($_POST['success'])) == 'true') {
        $out['error'] = sanitize_text_field($data['error']);
        $out['name'] = sanitize_text_field($data['name']);
        $out['valid_until'] = sanitize_text_field($data['valid_until']);
        $out['meta'] = sanitize_text_field($data['meta']);
      } else {
        $out['error'] = sanitize_text_field($data);
        $out['name'] = '';
        $out['valid_until'] = '';
        $out['meta'] = array();
      }
      $out['last_check'] = time();

      $this->update_license($out);
      do_action('wf_licensing_' . $this->prefix . '_save_ajax', $out);

      wp_send_json_success();
    } // save_ajax


    function prepare_server_query_data($action)
    {
      $license = $this->get_license();

      $query_data = array(
        'action' => $action,
        'license_key' => $license['license_key'],
        'rand' => wp_rand(1000, 9999),
        'version' => $this->version,
        'wp_version' => get_bloginfo('version'),
        'site_url' => get_home_url(),
        'site_title' => get_bloginfo('name'),
        'access_key' => $license['access_key'],
        'meta' => apply_filters('wf_licensing_' . trim($this->prefix, '_') . '_query_server_meta', array(), $action)
      );

      if (substr($action, 0, 7) == 'remote_') {
        unset($query_data['action'], $query_data['license_key']);
      }

      return $query_data;
    } // prepare_server_query_data


    /**
     * Run license server query.
     *
     * @param string $action
     * @param array $data
     *
     * @return string response|bool
     */
    function query_licensing_server($action, $data = array())
    {
      $license = $this->get_license();

      $request_params = array('sslverify' => false, 'timeout' => 25, 'redirection' => 2);
      $default_data = $this->prepare_server_query_data($action);

      $request_data = array_merge($default_data, $data, array('action' => $action));
      $request_data = apply_filters('wf_licensing_' . trim($this->prefix, '_') . '_query_server_data', $request_data, $action);
      array_walk_recursive($request_data, function (&$val, $ind) {
        $val = rawurlencode($val);
      });

      $this->log('query licensing server', $request_data);

      $url = rtrim(add_query_arg($request_data, trailingslashit($this->licensing_servers[0] . $this->api_ver)), '&');

      $response = wp_remote_get($url, $request_params);

      $body = wp_remote_retrieve_body($response);
      $result = @json_decode($body, true);

      $this->log('licensing server response', $response);

      if (is_wp_error($response) || empty($body) || !is_array($result) || !isset($result['success'])) {
        if (is_wp_error($response)) {
          return $response;
        } else {
          return new WP_Error(1, 'Invalid server response format.');
        }
      } else {
        return $result;
      }
    } // query_licensing_server

    /**
     * Initializes the WordPress filesystem.
     *
     * @return bool
     */
    function wp_init_filesystem()
    {
        if (! $this->filesystem_initialized) {
            if (! class_exists('WP_Filesystem')) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
            }

            WP_Filesystem();
            $this->filesystem_initialized = true;
        }

        return true;
    }
  } // WF_Licensing_CSMM
} // if WF_Licensing_CSMM
