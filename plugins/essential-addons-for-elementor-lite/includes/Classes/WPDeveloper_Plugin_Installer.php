<?php
namespace Essential_Addons_Elementor\Classes;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly.

use \WP_Error;

class WPDeveloper_Plugin_Installer
{
	public function __construct() {
		add_action( 'wp_ajax_wpdeveloper_auto_active_even_not_installed', [ $this, 'ajax_auto_active_even_not_installed' ] );
		add_action( 'wp_ajax_wpdeveloper_install_plugin', [ $this, 'ajax_install_plugin' ] );
		add_action( 'wp_ajax_wpdeveloper_upgrade_plugin', [ $this, 'ajax_upgrade_plugin' ] );
		add_action( 'wp_ajax_wpdeveloper_activate_plugin', [ $this, 'ajax_activate_plugin' ] );
		add_action( 'wp_ajax_wpdeveloper_deactivate_plugin', [ $this, 'ajax_deactivate_plugin' ] );
	}

    /**
     * get_local_plugin_data
     *
     * @param  mixed $basename
     * @return array|false
     */
    public function get_local_plugin_data($basename = '')
    {
        if (empty($basename)) {
            return false;
        }

        if (!function_exists('get_plugins')) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugins = get_plugins();

        if (!isset($plugins[$basename])) {
            return false;
        }

        return $plugins[$basename];
    }

    /**
     * get_remote_plugin_data
     *
     * @param  mixed $slug
     * @return mixed array|WP_Error
     */
    public function get_remote_plugin_data($slug = '')
    {
        if (empty($slug)) {
            return new WP_Error('empty_arg', __('Argument should not be empty.', 'essential-addons-for-elementor-lite'));
        }

        $response = wp_remote_post(
            'http://api.wordpress.org/plugins/info/1.0/',
            [
                'body' => [
                    'action' => 'plugin_information',
                    'request' => serialize((object) [
                        'slug' => $slug,
                        'fields' => [
                            'version' => false,
                        ],
                    ]),
                ],
            ]
        );

        if (is_wp_error($response)) {
            return $response;
        }

        return unserialize(wp_remote_retrieve_body($response));
    }

    /**
     * install_plugin
     *
     * @param  mixed $slug
     * @param  bool $active
     * @return mixed bool|WP_Error
     */
    public function install_plugin($slug = '', $active = true)
    {
        if (empty($slug)) {
            return new WP_Error('empty_arg', __('Argument should not be empty.', 'essential-addons-for-elementor-lite'));
        }

        include_once ABSPATH . 'wp-admin/includes/file.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/class-automatic-upgrader-skin.php';

        $plugin_data = $this->get_remote_plugin_data($slug);

        if (is_wp_error($plugin_data)) {
            return $plugin_data;
        }

        $upgrader = new \Plugin_Upgrader(new \Automatic_Upgrader_Skin());

        // install plugin
        $install = $upgrader->install($plugin_data->download_link);

        if (is_wp_error($install)) {
            return $install;
        }

        // activate plugin
        if ($install === true && $active) {
            $active = activate_plugin($upgrader->plugin_info(), '', false, true);

            if (is_wp_error($active)) {
                return $active;
            }

            return $active === null;
        }

        return $install;
    }

    /**
     * upgrade_plugin
     *
     * @param  mixed $basename
     * @return mixed bool|WP_Error
     */
    public function upgrade_plugin($basename = '')
    {
        if (empty($slug)) {
            return new WP_Error('empty_arg', __('Argument should not be empty.', 'essential-addons-for-elementor-lite'));
        }

        include_once ABSPATH . 'wp-admin/includes/file.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/class-automatic-upgrader-skin.php';

        $upgrader = new \Plugin_Upgrader(new \Automatic_Upgrader_Skin());

        // upgrade plugin
        return $upgrader->upgrade($basename);
    }

    public function ajax_install_plugin()
    {
        check_ajax_referer('essential-addons-elementor', 'security');

        if(!current_user_can( 'install_plugins' )) {
            wp_send_json_error(__('you are not allowed to do this action', 'essential-addons-for-elementor-lite'));
        }

	    $slug   = isset( $_POST['slug'] ) ? sanitize_text_field( $_POST['slug'] ) : '';
	    $result = $this->install_plugin( $slug );

        if ( isset( $_POST['promotype'], $_POST['slug'] ) ) {
            $promotype = sanitize_text_field( $_POST['promotype'] );
            $slug      = sanitize_text_field( $_POST['slug'] );
        
            $remote_urls = [
                'quick-setup' => [
                    'essential-blocks' => 'https://essential-addons.com/essential-blocks-install-quick-setup',
                    'templately'       => 'https://essential-addons.com/templately-install-quick-setup',
                ]
            ];
        
            if ( isset( $remote_urls[ $promotype ][ $slug ] ) ) {
                wp_remote_get( $remote_urls[ $promotype ][ $slug ] );
            }
        }

	    if ( is_wp_error( $result ) ) {
		    wp_send_json_error( $result->get_error_message() );
	    }

        wp_send_json_success(__('Plugin is installed successfully!', 'essential-addons-for-elementor-lite'));
    }

    public function ajax_upgrade_plugin()
    {
        check_ajax_referer('essential-addons-elementor', 'security');
        //check user capabilities
        if(!current_user_can( 'update_plugins' )) {
            wp_send_json_error(__('you are not allowed to do this action', 'essential-addons-for-elementor-lite'));
        }

	    $basename = isset( $_POST['basename'] ) ? sanitize_text_field( $_POST['basename'] ) : '';
	    $result   = $this->upgrade_plugin( $basename );

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        wp_send_json_success(__('Plugin is updated successfully!', 'essential-addons-for-elementor-lite'));
    }

    public function ajax_activate_plugin()
    {
        check_ajax_referer('essential-addons-elementor', 'security');

        //check user capabilities
        if(!current_user_can( 'activate_plugins' )) {
            wp_send_json_error(__('you are not allowed to do this action', 'essential-addons-for-elementor-lite'));
        }

	    $basename = isset( $_POST['basename'] ) ? sanitize_text_field( $_POST['basename'] ) : '';
	    $result   = activate_plugin( $basename, '', false, true );

	    if ( is_wp_error( $result ) ) {
		    wp_send_json_error( $result->get_error_message() );
	    }

        if ($result === false) {
            wp_send_json_error(__('Plugin couldn\'t be activated.', 'essential-addons-for-elementor-lite'));
        }
        wp_send_json_success(__('Plugin is activated successfully!', 'essential-addons-for-elementor-lite'));
    }

	public function ajax_deactivate_plugin() {
		check_ajax_referer( 'essential-addons-elementor', 'security' );

		//check user capabilities
		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( __( 'you are not allowed to do this action', 'essential-addons-for-elementor-lite' ) );
		}

		$basename = isset( $_POST['basename'] ) ? sanitize_text_field( $_POST['basename'] ) : '';
		deactivate_plugins( $basename, true );

		wp_send_json_success( __( 'Plugin is deactivated successfully!', 'essential-addons-for-elementor-lite' ) );
	}

	public function ajax_auto_active_even_not_installed() {
		check_ajax_referer( 'essential-addons-elementor', 'security' );

		if ( $this->get_local_plugin_data( $_POST['basename'] ) === false ) {
			$this->ajax_install_plugin();
		} else {
			$this->ajax_activate_plugin();
		}
	}
}
