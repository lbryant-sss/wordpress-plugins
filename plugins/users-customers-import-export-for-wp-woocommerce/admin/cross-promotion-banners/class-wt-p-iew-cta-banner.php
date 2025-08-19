<?php
/**
 * Class Wt_P_IEW_Cta_Banner
 *
 * This class is responsible for displaying the CTA banner on the product edit page.
 */

if ( ! defined('ABSPATH') ) {
    exit;
}


if ( ! class_exists('Wt_P_IEW_Cta_Banner') ) {
    class Wt_P_IEW_Cta_Banner {
        /**
         * Constructor.
         */
        public function __construct() {  
            // Check if premium plugin is active
            if (!in_array('wt-import-export-for-woo-product/wt-import-export-for-woo-product.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
                add_action('add_meta_boxes', array($this, 'add_meta_box'));
                add_action('wp_ajax_wt_dismiss_product_ie_cta_banner', array($this, 'dismiss_banner'));
            }
        }
        /**
         * Enqueue required scripts and styles.
         */
        public function enqueue_scripts($hook) {
            if (!in_array($hook, array('post.php', 'post-new.php')) || get_post_type() !== 'product') {
                return;
            }

            wp_enqueue_style( 
                'wt-wbte-cta-banner',
                plugin_dir_url(__FILE__) . 'assets/css/wbte-cross-promotion-banners.css',
                array(),
                Wbte_Cross_Promotion_Banners::get_banner_version(),
            );

            wp_enqueue_script(
                'wt-wbte-cta-banner',
                plugin_dir_url(__FILE__) . 'assets/js/wbte-cross-promotion-banners.js',
                array('jquery'),
                Wbte_Cross_Promotion_Banners::get_banner_version(),
                true
            );

            // Localize script with AJAX data
            wp_localize_script('wt-wbte-cta-banner', 'wt_product_ie_cta_banner_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wt_dismiss_product_ie_cta_banner_nonce'),
                'action' => 'wt_dismiss_product_ie_cta_banner'
            ));
        }

        /**
         * Add the meta box to the product edit screen
         */
        public function add_meta_box() {
            global $wpdb;

            $total_products = $wpdb->get_var("
                SELECT COUNT(ID)
                FROM {$wpdb->posts}
                WHERE post_type = 'product'
                AND post_status NOT IN ('trash')
            ");  
            
            // Show banner if there are 50 or more products
            if ( !defined( 'WT_PRODUCT_IMPORT_EXPORT_DISPLAY_BANNER' ) && $total_products >= 50) {
                add_meta_box(
                    'wt_product_import_export_pro',
                    'Product Import Export Pro',
                    array($this, 'render_banner'),
                    'product',
                    'side',
                    'low'
                );
                define( 'WT_PRODUCT_IMPORT_EXPORT_DISPLAY_BANNER', true );
            }
        }

        /**
         * Render the banner HTML.
         */
        public function render_banner() {
            // Check if banner should be hidden based on option
            $hide_banner = get_option('wt_hide_product_ie_cta_banner', false);
            
            $plugin_url = 'https://www.webtoffee.com/product/product-import-export-woocommerce/?utm_source=free_plugin_cross_promotion&utm_medium=add_new_product_tab&utm_campaign=Product_import_export';
            $wt_admin_img_path = plugin_dir_url( __FILE__ ) . 'assets/images';
            
            if ( $hide_banner ) {
                echo '<style>#wt_product_import_export_pro { display: none !important; }</style>';
                return;
            }
            ?>
            <div class="wt-cta-banner">
                <div class="wt-cta-content">
                    <div class="wt-cta-header">
                        <img src="<?php echo esc_url($wt_admin_img_path . '/product-ie.svg'); ?>" alt="<?php _e('Product Import Export'); ?>" class="wt-cta-icon">
                        <h3><?php _e('Product Import Export for WooCommerce'); ?></h3>
                    </div>

                    <ul class="wt-cta-features">
                        <li><?php _e('Import, export, or update WooCommerce products'); ?></li>
                        <li><?php _e('Supports all types of products (Simple, variable, subscription grouped, and external)'); ?></li>
                        <li><?php _e('Multiple file formats - CSV, XML, Excel, and TSV'); ?></li>
                        <li><?php _e('Advanced filters and customizations for better control'); ?></li>
                        <li class="hidden-feature"><?php _e('Bulk update WooCommerce product data'); ?></li>
                        <li class="hidden-feature"><?php _e('Import via FTP/SFTP and URL'); ?></li>
                        <li class="hidden-feature"><?php _e('Schedule automated import & export'); ?></li>
                        <li class="hidden-feature"><?php _e('Export and Import custom fields and third-party plugin fields'); ?></li>
                    </ul>

                    <div class="wt-cta-footer">
                        <div class="wt-cta-footer-links">
                            <a href="#" class="wt-cta-toggle" data-show-text="<?php esc_attr_e('View all premium features'); ?>" data-hide-text="<?php esc_attr_e('Show less'); ?>"><?php _e('View all premium features'); ?></a>
                            <a href="<?php echo esc_url($plugin_url); ?>" class="wt-cta-button" target="_blank"><img src="<?php echo esc_url($wt_admin_img_path . '/promote_crown.png');?>" style="width: 15.01px; height: 10.08px; margin-right: 8px;"><?php _e('Get the plugin'); ?></a>
                        </div>
                        <a href="#" class="wt-cta-dismiss" style="display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none;"><?php _e('Dismiss'); ?></a>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         * Handle the dismiss action via AJAX
         */
        public function dismiss_banner() {
            // Verify nonce for security
            if (!wp_verify_nonce($_POST['nonce'], 'wt_dismiss_product_ie_cta_banner_nonce')) {
                wp_send_json_error('Invalid nonce');
            }

            // Check if user has permission
            if (!current_user_can('manage_options')) {
                wp_send_json_error('Insufficient permissions');
            }

            // Update the option to hide the banner
            update_option('wt_hide_product_ie_cta_banner', true);

            wp_send_json_success('Banner dismissed successfully');
        }
    }

    new Wt_P_IEW_Cta_Banner();
}
