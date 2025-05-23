<?php
namespace EssentialBlocks\Integrations;

use EssentialBlocks\Utils\Installer;

class PluginInstaller extends ThirdPartyIntegration {

	public function __construct() {
		$this->add_ajax(
			array(
				'plugin_installer' => array(
					'callback' => 'plugin_install',
					'public'   => false,
				),
				'plugin_deactivater' => array(
					'callback' => 'plugin_deactivate',
					'public'   => false,
				),
			)
		);
	}

	/**
	 * Openverse plugin_install
	 */
	public function plugin_install() {
		if ( ! isset( $_POST['admin_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['admin_nonce'] ), 'admin-nonce' ) ) {
			wp_send_json_error( __( 'Could not install the plugin.', 'essential-blocks' ) );
			die( esc_html__( 'Nonce did not match', 'essential-blocks' ) );
		}
		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( __( 'You are not authorized!', 'essential-blocks' ) );
		}

		if ( isset( $_POST['slug'] ) && isset( $_POST['plugin_file'] ) ) {
			$plugin                = array();
			$plugin['slug']        = sanitize_text_field( $_POST['slug'] );
			$plugin['plugin_file'] = sanitize_text_field( $_POST['plugin_file'] );
			$installer             = Installer::get_instance();
			$response              = $installer->install( $plugin );
			wp_send_json_success( $response );
		} else {
			wp_send_json_error( __( 'Could not install the plugin.', 'essential-blocks' ) );
		}
		wp_die();
	}
	public function plugin_deactivate() {
		if ( ! isset( $_POST['admin_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['admin_nonce'] ), 'admin-nonce' ) ) {
			wp_send_json_error( __( 'Could not deactivate the plugin.', 'essential-blocks' ) );
			die( esc_html__( 'Nonce did not match', 'essential-blocks' ) );
		}
		if ( ! current_user_can( 'deactivate_plugins' ) ) {
			wp_send_json_error( __( 'You are not authorized!', 'essential-blocks' ) );
		}

		if ( isset( $_POST['plugin_file'] ) ) {
            $plugin_file = sanitize_text_field( $_POST['plugin_file'] );

            if ( is_plugin_active( $plugin_file ) ) {
                deactivate_plugins( $plugin_file);

                if ( is_plugin_active( $plugin_file ) ) {
                    wp_send_json_error( __( 'Plugin deactivation failed.', 'essential-blocks' ) );
                } else {
                    wp_send_json_success( __( 'Plugin deactivated successfully.', 'essential-blocks' ) );
                }
            } else {
                wp_send_json_error( __( 'Plugin is already inactive.', 'essential-blocks' ) );
            }
        } else {
            wp_send_json_error( __( 'Plugin file not specified.', 'essential-blocks' ) );
        }

        wp_die();
	}

}