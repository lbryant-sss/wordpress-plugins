<?php 

defined('ABSPATH') || exit;

class Pi_Live_Sales_Pro_Warning{

    static $instance = null;

    public static function get_instance(){
        if(self::$instance == null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    function __construct() {
        add_action( 'after_plugin_row_live-sales-notifications-for-woocommerce-pro/pisol-sales-notification.php', [$this,'disable_free_version_warning'], 10, 3 );
    }

    function disable_free_version_warning(){
        $deactivate_url = $this->deactivation_link();
        ?>
        <tr class="plugin-update-tr">
            <td colspan="6" class="plugin-update colspanchange" style="background-color: #ffebe8; border: 1px solid #c00; width:100%; padding:10px;">
                <p>
                    ⚠️ You have Free version of <strong>Live Sales Notifications for WooCommerce</strong> active. 
                    Please <a href="<?php echo esc_url( $deactivate_url ); ?>">Click to deactivate Free version</a> 
                    before activating Pro.
                </p>
            </td>
        </tr>
        <?php
    }

    function deactivation_link(){
        $free_plugin = 'live-sales-notifications-for-woocommerce/pisol-sales-notification.php';
        $deactivate_url = wp_nonce_url(
            add_query_arg(
                array(
                    'action'        => 'deactivate',
                    'plugin'        => $free_plugin,
                    'plugin_status' => 'all',
                    'paged'         => 1,
                    '_wpnonce'      => wp_create_nonce( 'deactivate-plugin_' . $free_plugin ),
                ),
                admin_url( 'plugins.php' )
            ),
            'deactivate-plugin_' . $free_plugin
        );
        return $deactivate_url;
    }

}

Pi_Live_Sales_Pro_Warning::get_instance();