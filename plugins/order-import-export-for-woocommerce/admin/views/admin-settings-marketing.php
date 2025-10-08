<?php
if (!defined('WPINC')) {
    die;
}
?>
<div class="wt-iew-tab-content" data-id="<?php echo esc_attr($target_id); ?>">
    <div class="wt-ier-wrapper">
        <h2 class="wt-ier-page-title"><?php esc_html_e('One stop solution for all your import-export needs.', 'order-import-export-for-woocommerce'); ?></h2>
        <p class="wt-ier-subp"><?php esc_html_e('Upgrade to the premium version and get access to the advanced features with premium support.', 'order-import-export-for-woocommerce'); ?></p>
        <div class="wt-ier-row">
            <div class="wt-ier-col-12 wt-ier-col-lg-4 wt-ier-lg-4 wt-ier-mb-lg-0">
                <div class="wt-ier-p-5 wt-ier-box-wrapper wt-ier-box-highlight">
                    <img src="<?php echo esc_url(WT_O_IEW_PLUGIN_URL); ?>assets/images/upgrade/product-ie.svg" class="wt-ier-thumbnails">
                    <h3 class="wt-ier-plugin-name"><?php esc_html_e('PRODUCT IMPORT EXPORT PLUGIN FOR WOOCOMMERCE', 'order-import-export-for-woocommerce'); ?></h3>
                    <p class="wt-ier-p"><?php esc_html_e('Imports and exports all product types and reviews. Supports both CSV and XML file formats.', 'order-import-export-for-woocommerce'); ?></p>
                    <div class="wt-ier-d-sm-flex wt-ier-btn-group">
                        <a href="https://www.webtoffee.com/product/product-import-export-woocommerce/?utm_source=free_plugin_revamp&utm_medium=basic_revamp&utm_campaign=Product_Import_Export&utm_content=<?php echo esc_attr(WT_O_IEW_VERSION); ?>" target="_blank" class="wt-ier-primary-btn wt-ier-btn"><?php esc_html_e('Get Premium', 'order-import-export-for-woocommerce'); ?></a>
                        <a href="" class="wt-ier-secondary-btn wt-ier-btn" data-toggle="modal" data-target="#wt-ier-comparison-modal-product"><?php esc_html_e('Compare with basic', 'order-import-export-for-woocommerce'); ?></a>
                    </div>
                </div>
            </div>
            <div class="wt-ier-col-12 wt-ier-col-lg-4 wt-ier-lg-4 wt-ier-mb-lg-0">
                <div class="wt-ier-p-5 wt-ier-box-wrapper wt-ier-box-highlight">
                    <img src="<?php echo esc_url(WT_O_IEW_PLUGIN_URL); ?>assets/images/upgrade/customer-ie.svg" class="wt-ier-thumbnails">
                    <h3 class="wt-ier-plugin-name"><?php esc_html_e('WORDPRESS USERS & WOOCOMMERCE CUSTOMERS IMPORT EXPORT', 'order-import-export-for-woocommerce'); ?></h3>
                    <p class="wt-ier-p"><?php esc_html_e('Import and export all your WordPress User and WooCommerce Customer data in CSV/XML file formats.', 'order-import-export-for-woocommerce'); ?></p>
                    <div class="wt-ier-d-sm-flex wt-ier-btn-group">
                        <a href="https://www.webtoffee.com/product/wordpress-users-woocommerce-customers-import-export/?utm_source=free_plugin_revamp&utm_medium=basic_revamp&utm_campaign=User_Import_Export&utm_content=<?php echo esc_attr(WT_O_IEW_VERSION); ?>" class="wt-ier-primary-btn wt-ier-btn" target="_blank"><?php esc_html_e('Get Premium', 'order-import-export-for-woocommerce'); ?></a>
                        <a href="" class="wt-ier-secondary-btn wt-ier-btn" data-toggle="modal" data-target="#wt-ier-comparison-modal-customer"><?php esc_html_e('Compare with basic', 'order-import-export-for-woocommerce'); ?></a>
                    </div>
                </div>
            </div>
            <div class="wt-ier-col-12 wt-ier-col-lg-4 wt-ier-lg-4 wt-ier-mb-lg-0">
                <div class="wt-ier-p-5 wt-ier-box-wrapper wt-ier-box-highlight">
                    <img src="<?php echo esc_url(WT_O_IEW_PLUGIN_URL); ?>assets/images/upgrade/order-ie.svg" class="wt-ier-thumbnails">
                    <h3 class="wt-ier-plugin-name"><?php esc_html_e('ORDER, COUPON, SUBSCRIPTION EXPORT IMPORT FOR WOOCOMMERCE', 'order-import-export-for-woocommerce'); ?></h3>
                    <p class="wt-ier-p"><?php esc_html_e('Export or Import WooCommerce orders, Coupons and Subscriptions.', 'order-import-export-for-woocommerce'); ?></p>
                    <div class="wt-ier-d-sm-flex wt-ier-btn-group">
                        <a  href="https://www.webtoffee.com/product/order-import-export-plugin-for-woocommerce/?utm_source=free_plugin_revamp&utm_medium=basic_revamp&utm_campaign=Order_Import_Export&utm_content=<?php echo esc_attr(WT_O_IEW_VERSION); ?>" class="wt-ier-primary-btn wt-ier-btn" target="_blank"><?php esc_html_e('Get Premium', 'order-import-export-for-woocommerce'); ?></a>
                        <a href="" class="wt-ier-secondary-btn wt-ier-btn" data-toggle="modal" data-target="#wt-ier-comparison-modal-order"><?php esc_html_e('Compare with basic', 'order-import-export-for-woocommerce'); ?></a>
                    </div>
                </div>
            </div>
        </div>
        <!--------product imp-exp comparison table --------->
        <div id="wt-ier-comparison-modal-product" class="wt-ier-modal">
            <div class="wt-ier-modal-content">
                <div class="wt-ier-resposive-table">
                    <table class="wt-ier-table">
                        <thead>
                            <tr class="wt-ier-top-tr">
                                <td></td>
                                <td colspan="3"><span class="wt-ier-close">&times;</span></td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('Features', 'order-import-export-for-woocommerce'); ?></th>
                                <th><?php esc_html_e('Free', 'order-import-export-for-woocommerce'); ?></th>
                                <th><img src="<?php echo esc_url(WT_O_IEW_PLUGIN_URL); ?>assets/images/upgrade/pre-icon.svg" class="wt-ier-pre-icon"><?php esc_html_e('Premium', 'order-import-export-for-woocommerce'); ?></th>
                                <th><?php esc_html_e('Import Export Suite', 'order-import-export-for-woocommerce'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php esc_html_e('Import and export', 'order-import-export-for-woocommerce'); ?></td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Products', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Product Reviews', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Product Categories', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Product Tags', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Orders', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Coupons', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Subscriptions', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('WordPress Users', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('WooCommerce Customers', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Products', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Product Reviews', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Product Categories', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Product Tags', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Orders', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Coupons', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Subscriptions', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('WordPress Users', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('WooCommerce Customers', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Products', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Product Reviews', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Product Categories', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Product Tags', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Orders', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Coupons', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Subscriptions', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('WordPress Users', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('WooCommerce Customers', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Supported product types', 'order-import-export-for-woocommerce'); ?></td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Simple Products', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Grouped Products', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('External/Affiliate Products', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Variable product', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Simple subscription', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Variable subscription', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('WooCommerce Bookings', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Custom Products', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Simple Products', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Grouped Products', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('External/Affiliate Products', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Variable product', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Simple subscription', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Variable subscription', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('WooCommerce Bookings', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Custom Products', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Simple Products', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Grouped Products', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('External/Affiliate Products', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Variable product', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Simple subscription', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Variable subscription', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('WooCommerce Bookings', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Custom Products', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Supported file types', 'order-import-export-for-woocommerce'); ?></td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('CSV', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('XML', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('CSV', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('XML', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('CSV', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('XML', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Supported import methods', 'order-import-export-for-woocommerce'); ?></td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Local', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('FTP/SFTP', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('From URL', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('From existing files', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Local', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('FTP/SFTP', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('From URL', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('From existing files', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Local', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('FTP/SFTP', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('From URL', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('From existing files', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Automatic scheduled import & export', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Import & export custom fields ( metadata )', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Import & export hidden meta', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Bulk delete products', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Option to import products as new item during post id conflicts', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Export to FTP/SFTP', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Set CSV delimiter for export', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Export images as a separate zip', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Convert shortcodes to HTML on export', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Custom export filename', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--------customer imp-exp comparison table --------->
        <div id="wt-ier-comparison-modal-customer" class="wt-ier-modal">
            <div class="wt-ier-modal-content">
                <div class="wt-ier-resposive-table">
                    <table class="wt-ier-table">
                        <thead>
                            <tr class="wt-ier-top-tr">
                                <td></td>
                                <td colspan="3"><span class="wt-ier-close">&times;</span></td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('Features', 'order-import-export-for-woocommerce'); ?></th>
                                <th><?php esc_html_e('Free', 'order-import-export-for-woocommerce'); ?></th>
                                <th><img src="<?php echo esc_url(WT_O_IEW_PLUGIN_URL); ?>assets/images/upgrade/pre-icon.svg" class="wt-ier-pre-icon"><?php esc_html_e('Premium', 'order-import-export-for-woocommerce'); ?></th>
                                <th><?php esc_html_e('Import Export Suite', 'order-import-export-for-woocommerce'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php esc_html_e('Import and export', 'order-import-export-for-woocommerce'); ?></td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('WordPress Users', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('WooCommerce Customers', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Products', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Product Reviews', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Product Categories', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Product Tags', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Orders', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Coupons', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Subscriptions', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('WordPress Users', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('WooCommerce Customers', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Products', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Product Reviews', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Product Categories', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Product Tags', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Orders', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Coupons', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Subscriptions', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('WordPress Users', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('WooCommerce Customers', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Products', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Product Reviews', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Product Categories', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Product Tags', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Orders', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Coupons', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Subscriptions', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Supported file types', 'order-import-export-for-woocommerce'); ?></td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('CSV', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('XML', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('CSV', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('XML', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('CSV', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('XML', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Supported import methods', 'order-import-export-for-woocommerce'); ?></td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Local', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('FTP/SFTP', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('From URL', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('From existing files', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Local', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('FTP/SFTP', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('From URL', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('From existing files', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Local', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('FTP/SFTP', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('From URL', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('From existing files', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Automatic scheduled import & export', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Import & export custom fields ( metadata )', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Import & export hidden meta', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Option to email new users on import', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Export to FTP/SFTP', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Set CSV delimiter for export', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Custom export filename', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--------order imp-exp comparison table --------->
        <div id="wt-ier-comparison-modal-order" class="wt-ier-modal">
            <div class="wt-ier-modal-content">
                <div class="wt-ier-resposive-table">
                    <table class="wt-ier-table">
                        <thead>
                            <tr class="wt-ier-top-tr">
                                <td></td>
                                <td colspan="3"><span class="wt-ier-close">&times;</span></td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e('Features', 'order-import-export-for-woocommerce'); ?></th>
                                <th><?php esc_html_e('Free', 'order-import-export-for-woocommerce'); ?></th>
                                <th><img src="<?php echo esc_url(WT_O_IEW_PLUGIN_URL); ?>assets/images/upgrade/pre-icon.svg" class="wt-ier-pre-icon"><?php esc_html_e('Premium', 'order-import-export-for-woocommerce'); ?></th>
                                <th><?php esc_html_e('Import Export Suite', 'order-import-export-for-woocommerce'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php esc_html_e('Import and export', 'order-import-export-for-woocommerce'); ?></td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Orders', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Coupons', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Subscriptions', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Products', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Product Reviews', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Product Categories', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Product Tags', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('WordPress Users', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('WooCommerce Customers', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Orders', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Coupons', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Subscriptions', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Products', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Product Reviews', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Product Categories', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('Product Tags', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('WordPress Users', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('WooCommerce Customers', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Orders', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Coupons', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Subscriptions', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Products', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Product Reviews', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Product Categories', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Product Tags', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('WordPress Users', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('WooCommerce Customers', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Supported file types', 'order-import-export-for-woocommerce'); ?></td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('CSV', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('XML', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('CSV', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('XML', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('CSV', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('XML', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Supported import methods', 'order-import-export-for-woocommerce'); ?></td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Local', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('FTP/SFTP', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('From URL', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span> <?php esc_html_e('From existing files', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Local', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('FTP/SFTP', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('From URL', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('From existing files', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Local', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('FTP/SFTP', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('From URL', 'order-import-export-for-woocommerce'); ?></li>
                                        <li><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('From existing files', 'order-import-export-for-woocommerce'); ?></li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Automatic scheduled import & export', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Import & export custom fields ( metadata )', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Import & export hidden meta', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Email customers on order status update', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Create customers on order import', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Bulk delete orders/coupons/subcriptions', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Export to FTP/SFTP', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Set CSV delimiter for export', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Custom export filename', 'order-import-export-for-woocommerce'); ?></td>
                                <td><span style="color:red; line-height:inherit;" class="dashicons dashicons-dismiss"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                                <td><span style="color:#36D144; line-height:inherit;" class="dashicons dashicons-yes-alt"></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--------comparison table ends here--------->
        <div class="wt-ier-box-wrapper wt-ier-mt-5 wt-ier-suite">
            <div class="wt-ier-row wt-ier-p-5">
                <div class="wt-ier-col-12 wt-ier-col-lg-6">
                    <img src="<?php echo esc_url(WT_O_IEW_PLUGIN_URL); ?>assets/images/upgrade/suite.svg" class="wt-ier-thumbnails">
                    <h2 class="wt-ier-page-title"><?php esc_html_e('Import Export Suite for WooCommerce', 'order-import-export-for-woocommerce'); ?></h2>
                    <p class="wt-ier-p"><?php esc_html_e('WooCommerce Import Export Suite is an all-in-one bundle of plugins with which you can import and export WooCommerce products, product reviews, orders, customers, coupons and subscriptions.', 'order-import-export-for-woocommerce'); ?></p>
                    <a href="https://www.webtoffee.com/product/woocommerce-import-export-suite/?utm_source=free_plugin_revamp&utm_medium=basic_revamp&utm_campaign=Import_Export_Suite&utm_content=<?php echo esc_attr(WT_O_IEW_VERSION); ?>" class="wt-ier-primary-btn" target="_blank"><?php esc_html_e('Get Premium', 'order-import-export-for-woocommerce'); ?></a>
                </div>
            </div>
        </div>
    </div>
    <script>
        jQuery("a[data-toggle=modal]").click(function(e) {
            e.preventDefault();
            var target = jQuery(this).attr('data-target');
            jQuery(target).css('display', 'block');
        });
        jQuery(document).click(function(e) {
            if (jQuery(e.target).is('.wt-ier-modal')) {
                jQuery('.wt-ier-modal').css('display', 'none');
            }
        });
        jQuery(".wt-ier-close").click(function(e) {
            jQuery(this).closest('.wt-ier-modal').css('display', 'none');
        });
    </script>
</div>