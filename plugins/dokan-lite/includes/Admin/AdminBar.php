<?php

namespace WeDevs\Dokan\Admin;

use WeDevs\Dokan\Dashboard\Templates\Dashboard;
use WeDevs\Dokan\Utilities\ReportUtil;
use WP_Admin_Bar;

/**
 * WordPress settings API For Dokan Admin Settings class
 *
 * @author Tareq Hasan
 */
class AdminBar {

    /**
     * Class constructor
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @return void
     */
    public function __construct() {
        add_action( 'wp_before_admin_bar_render', [ $this, 'dokan_admin_toolbar' ] );

        if ( apply_filters( 'dokan_show_admin_bar_visit_dashboard', true ) ) {
            add_action( 'admin_bar_menu', [ $this, 'visit_dashboard_menu' ], 35 );
        }
    }

    /**
     * Add Menu in Dashboard Top bar
     *
     * @return void
     */
    public function dokan_admin_toolbar() {
        global $wp_admin_bar;

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }

        $args = [
            'id'     => 'dokan',
            'title'  => __( 'Dokan', 'dokan-lite' ),
            'href'   => admin_url( 'admin.php?page=dokan' ),
        ];

        $wp_admin_bar->add_menu( $args );

        $wp_admin_bar->add_menu(
            [
                'id'     => 'dokan-dashboard',
                'parent' => 'dokan',
                'title'  => __( 'Dashboard', 'dokan-lite' ),
                'href'   => admin_url( 'admin.php?page=dokan' ),
            ]
        );

        $wp_admin_bar->add_menu(
            [
                'id'     => 'dokan-withdraw',
                'parent' => 'dokan',
                'title'  => __( 'Withdraw', 'dokan-lite' ),
                'href'   => admin_url( 'admin.php?page=dokan#/withdraw' ),
            ]
        );

        $wp_admin_bar->add_menu(
            [
                'id'     => 'dokan-pro-features',
                'parent' => 'dokan',
                'title'  => __( 'PRO Features', 'dokan-lite' ),
                'href'   => admin_url( 'admin.php?page=dokan#/premium' ),
            ]
        );

        $wp_admin_bar->add_menu(
            [
                'id'     => 'dokan-settings',
                'parent' => 'dokan',
                'title'  => __( 'Settings', 'dokan-lite' ),
                'href'   => admin_url( 'admin.php?page=dokan#/settings' ),
            ]
        );

        /*
         * Add new or remove toolbar
         *
         * @since 2.5.3
         */
        do_action( 'dokan_render_admin_toolbar', $wp_admin_bar );
    }

    /**
     * Show visit vendor dashboard
     *
     * @param WP_Admin_Bar $wp_admin_bar
     *
     * @return void
     */
    public function visit_dashboard_menu( $wp_admin_bar ) {
        if ( ! is_admin() || ! is_admin_bar_showing() ) {
            return;
        }

        // Show only when the user is a member of this site, or they're a super admin.
        if ( ! is_user_member_of_blog() && ! is_super_admin() ) {
            return;
        }

        $menus = $this->get_dokan_admin_bar_menus();

        // Added admin menus for dokan in wp admin bar.
        foreach ( $menus as $menu ) {
            $wp_admin_bar->add_node( $menu );
        }
    }

    /**
     * Get admin menus data for dokan.
     *
     * @since 3.2.15
     *
     * @return array
     */
    public function get_dokan_admin_bar_menus() {
        $menus            = [];
        $shop             = wc_get_page_permalink( 'shop' );
        $stores           = (int) dokan_get_option( 'store_listing', 'dokan_pages', 0 );
        $vendor_dashboard = (int) dokan_get_option( 'dashboard', 'dokan_pages', 0 );

        if ( $shop ) {
            $menus[] = [
                'parent' => 'site-name',
                'id'     => 'view-store',
                'title'  => __( 'Visit Shop', 'dokan-lite' ),
                'href'   => wc_get_page_permalink( 'shop' ),
            ];
        }

        if ( $stores ) {
            $menus[] = [
                'parent' => 'site-name',
                'id'     => 'view-stores',
                'title'  => __( 'Visit Stores', 'dokan-lite' ),
                'href'   => get_permalink( $stores ),
            ];
        }

        if ( $vendor_dashboard ) {
            $menus[] = [
                'parent' => 'site-name',
                'id'     => 'view-dashboard',
                'title'  => __( 'Visit Vendor Dashboard', 'dokan-lite' ),
                'href'   => get_permalink( $vendor_dashboard ) . ( ReportUtil::is_analytics_enabled() ? '?path=%2Fanalytics%2FOverview' : '' ),
            ];
        }

        return $menus;
    }
}
