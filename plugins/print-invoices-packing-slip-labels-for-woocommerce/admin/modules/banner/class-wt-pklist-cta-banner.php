<?php

namespace Wtpdf\Banners;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Wt_Invoice_Cta_Banner
 *
 * @since    4.7.8  This class is responsible for displaying the CTA banner on the product edit page.
 */
if (!class_exists('\\Wtpdf\\Banners\\Wt_Invoice_Cta_Banner')) {
    class Wt_Invoice_Cta_Banner {
        /**
         * Constructor.
         */
        public function __construct() {  
            // Check if premium plugin is active
            if (!in_array('wt-woocommerce-invoice-addon/wt-woocommerce-invoice-addon.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
                add_action('add_meta_boxes', array($this, 'add_meta_box'));
                add_action('wp_ajax_wt_dismiss_invoice_cta_banner', array($this, 'dismiss_banner'));
            }
        }
        /**
         * Enqueue required scripts and styles.
         */
        public function enqueue_scripts($hook) {

            $currentScreen = get_current_screen();
                        
            // Check if current screen is allowed
            if ($currentScreen->id !== 'woocommerce_page_wc-orders' && $currentScreen->id !== 'shop_order') {
                return;
            } 
            

            wp_enqueue_style( 
                'wt-pklist-cta-banner',
                plugin_dir_url(__FILE__) . 'assets/css/wt-pklist-cta-banner.css',
                array(),
                WF_PKLIST_VERSION
            );

            wp_enqueue_script(
                'wt-pklist-cta-banner',
                plugin_dir_url(__FILE__) . 'assets/js/wt-pklist-cta-banner.js',
                array('jquery'),
                WF_PKLIST_VERSION,
                true
            );

            // Localize script with AJAX data
            wp_localize_script('wt-pklist-cta-banner', 'wt_invoice_cta_banner_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wt_dismiss_invoice_cta_banner_nonce'),
                'action' => 'wt_dismiss_invoice_cta_banner'
            ));
        }

        /**
         * Add the meta box to the product edit screen
         */
        public function add_meta_box() {
            add_meta_box(
                'wt_pdf_invoice_pro',
                '—',
                array($this, 'render_banner'),
                array('woocommerce_page_wc-orders', 'shop_order'),
                'side',
                'low'
            );
        }

        /**
         * Render the banner HTML.
         */
        public function render_banner() {
            // Check if banner should be hidden based on option
            $hide_banner = get_option('wt_hide_invoice_cta_banner', false);
            
            $plugin_url = 'https://www.webtoffee.com/product/woocommerce-pdf-invoices-packing-slips/?utm_source=free_plugin_cross_promotion&utm_medium=add_new_order_sidebar&utm_campaign=PDF_invoice';
            $wt_admin_img_path = WF_PKLIST_PLUGIN_URL . 'assets/images';
            
            if ($hide_banner) {
                echo '<style>#wt_pdf_invoice_pro { display: none !important; }</style>';
            }
            ?>
            <div class="wt-cta-banner">
                <div class="wt-cta-content">
                    <div class="wt-cta-header">
                        <img src="<?php echo esc_url($wt_admin_img_path . '/pdf_invoice.svg'); ?>" alt="<?php _e('Product Import Export', 'print-invoices-packing-slip-labels-for-woocommerce'); ?>" class="wt-cta-icon">
                        <h3><?php _e('WooCommerce PDF Invoices, Packing Slips and Credit Notes', 'print-invoices-packing-slip-labels-for-woocommerce'); ?></h3>
                    </div>

                    <ul class="wt-cta-features">
                        <li><?php _e('Automatically generate PDF invoices, packing slips, and credit notes', 'print-invoices-packing-slip-labels-for-woocommerce'); ?></li>
                        <li><?php _e('Use ready-made, customizable templates to match your brand', 'print-invoices-packing-slip-labels-for-woocommerce'); ?></li>
                        <li><?php _e('Print or download invoices individually or in bulk', 'print-invoices-packing-slip-labels-for-woocommerce'); ?></li>
                        <li><?php _e('Set custom invoice numbering for better organization', 'print-invoices-packing-slip-labels-for-woocommerce'); ?></li>
                        <li class="hidden-feature"><?php _e('Customize documents fully with visual or code editors', 'print-invoices-packing-slip-labels-for-woocommerce'); ?></li>
                        <li class="hidden-feature"><?php _e('Include VAT, GST, ABN, and other tax details', 'print-invoices-packing-slip-labels-for-woocommerce'); ?></li>
                        <li class="hidden-feature"><?php _e('Add "Pay Now" link on invoices', 'print-invoices-packing-slip-labels-for-woocommerce'); ?></li>
                        <li class="hidden-feature"><?php _e('Add custom fields to any order document with ease', 'print-invoices-packing-slip-labels-for-woocommerce'); ?></li>
                    </ul>

                    <div class="wt-cta-footer">
                        <div class="wt-cta-footer-links">
                            <a href="#" class="wt-cta-toggle" data-show-text="<?php esc_attr_e('View all premium features', 'print-invoices-packing-slip-labels-for-woocommerce'); ?>" data-hide-text="<?php esc_attr_e('Show less', 'print-invoices-packing-slip-labels-for-woocommerce'); ?>"><?php _e('View all premium features', 'print-invoices-packing-slip-labels-for-woocommerce'); ?></a>
                            <a href="<?php echo esc_url($plugin_url); ?>" class="wt-cta-button" target="_blank"><img src="<?php echo esc_url($wt_admin_img_path . '/promote_crown.png');?>" style="width: 15.01px; height: 10.08px; margin-right: 8px;"><?php _e('Get the plugin', 'print-invoices-packing-slip-labels-for-woocommerce'); ?></a>
                        </div>
                        <a href="#" class="wt-cta-dismiss" style="display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none;"><?php _e('Dismiss', 'print-invoices-packing-slip-labels-for-woocommerce'); ?></a>
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
            if (!wp_verify_nonce($_POST['nonce'], 'wt_dismiss_invoice_cta_banner_nonce')) {
                wp_send_json_error('Invalid nonce');
            }

            // Check if user has permission
            if (!current_user_can('manage_options')) {
                wp_send_json_error('Insufficient permissions');
            }

            // Update the option to hide the banner
            update_option('wt_hide_invoice_cta_banner', true);

            wp_send_json_success('Banner dismissed successfully');
        }
    }

    new \Wtpdf\Banners\Wt_Invoice_Cta_Banner();
}
