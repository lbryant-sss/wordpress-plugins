<?php
/**
 * Admin-bar modules
 *
 * @package   PopupMaker
 * @copyright Copyright (c) 2024, Code Atlantic LLC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PUM_Modules_Admin_Bar
 *
 * This class adds admin bar menu for Popup Management.
 */
class PUM_Modules_Admin_Bar {

	/**
	 * Initializes this module.
	 */
	public static function init() {
		add_action( 'admin_bar_menu', [ __CLASS__, 'toolbar_links' ], 999 );
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_files' ] );
		add_action( 'init', [ __CLASS__, 'show_debug_bar' ] );
	}

	/**
	 * Renders the admin debug bar when PUM Debug is enabled.
	 */
	public static function show_debug_bar() {
		if ( self::should_render() && Popup_Maker::debug_mode() ) {
			show_admin_bar( true );
		}
	}

	/**
	 * Returns true only if all of the following are true:
	 * - User is logged in.
	 * - Not in WP Admin.
	 * - The admin bar is showing.
	 * - PUM Admin bar is not disabled.
	 * - Current user can edit others posts or manage options.
	 *
	 * @return bool
	 */
	public static function should_render() {
		$tests = [
			is_user_logged_in(),
			! is_admin(),
			is_admin_bar_showing(),
			! pum_get_option( 'disabled_admin_bar' ),
			( current_user_can( 'edit_others_posts' ) || current_user_can( 'manage_options' ) ),
		];

		return ! in_array( false, $tests, true );
	}

	/**
	 * Add additional toolbar menu items to the front end.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar
	 */
	public static function toolbar_links( $wp_admin_bar ) {
		if ( ! self::should_render() ) {
			return;
		}

		$popups = self::loaded_popups();

		$count = count( $popups );

		$wp_admin_bar->add_node(
			[
				'id'     => 'popup-maker',
				'title'  => sprintf( '%s <span class="counter">%d</span>', __( 'Popup Maker', 'popup-maker' ), $count ),
				'href'   => '#popup-maker',
				'meta'   => [ 'class' => 'popup-maker-toolbar' ],
				'parent' => false,
			]
		);

		$wp_admin_bar->add_node(
			[
				'id'     => 'popups',
				'title'  => sprintf( '%s <span class="counter">%d</span>', __( 'Popups', 'popup-maker' ), $count ),
				'href'   => '#',
				'parent' => 'popup-maker',
			]
		);

		if ( $count ) {
			foreach ( $popups as $popup ) {
				/** @var WP_Post $popup */

				$node_id = 'popup-' . $popup->ID;

				$can_edit = current_user_can( 'edit_post', $popup->ID );

				$edit_url = $can_edit ? admin_url( 'post.php?post=' . $popup->ID . '&action=edit' ) : '#';

				// Single Popup Menu Node
				$wp_admin_bar->add_node(
					[
						'id'     => $node_id,
						'title'  => esc_html( $popup->post_title ),
						'href'   => $edit_url,
						'parent' => 'popups',
					]
				);

				// Trigger Link
				$wp_admin_bar->add_node(
					[
						'id'     => $node_id . '-open',
						'title'  => __( 'Open Popup', 'popup-maker' ),
						'meta'   => [
							'onclick' => 'PUM.open(' . $popup->ID . '); return false;',
						],
						'href'   => '#popup-maker-open-popup-' . $popup->ID,
						'parent' => $node_id,
					]
				);

				$wp_admin_bar->add_node(
					[
						'id'     => $node_id . '-close',
						'title'  => __( 'Close Popup', 'popup-maker' ),
						'meta'   => [
							'onclick' => 'PUM.close(' . $popup->ID . '); return false;',
						],
						'href'   => '#popup-maker-close-popup-' . $popup->ID,
						'parent' => $node_id,
					]
				);

				if ( pum_get_popup( $popup->ID )->has_conditions( [ 'js_only' => true ] ) ) {
					$wp_admin_bar->add_node(
						[
							'id'     => $node_id . '-conditions',
							'title'  => __( 'Check Conditions', 'popup-maker' ),
							'meta'   => [
								'onclick' => 'alert(PUM.checkConditions(' . $popup->ID . ') ? "Pass" : "Fail"); return false;',
							],
							'href'   => '#popup-maker-check-conditions-popup-' . $popup->ID,
							'parent' => $node_id,
						]
					);
				}

				$wp_admin_bar->add_node(
					[
						'id'     => $node_id . '-reset-cookies',
						'title'  => __( 'Reset Cookies', 'popup-maker' ),
						'meta'   => [
							'onclick' => 'PUM.clearCookies(' . $popup->ID . '); alert("' . __( 'Success', 'popup-maker' ) . '"); return false;',
						],
						'href'   => '#popup-maker-reset-cookies-popup-' . $popup->ID,
						'parent' => $node_id,
					]
				);

				if ( $can_edit ) {
					// Edit Popup Link
					$wp_admin_bar->add_node(
						[
							'id'     => $node_id . '-edit',
							'title'  => __( 'Edit Popup', 'popup-maker' ),
							'href'   => $edit_url,
							'parent' => $node_id,
						]
					);
				}
			}
		} else {
			$wp_admin_bar->add_node(
				[
					'id'     => 'no-popups-loaded',
					'title'  => __( 'No Popups Loaded', 'popup-maker' ) . '<strong style="color:#fff; margin-left: 5px;">?</strong>',
					'href'   => 'https://wppopupmaker.com/docs/problem-solving/troubleshooting-your-first-popup/?utm_campaign=contextual-help&utm_medium=inline-doclink&utm_source=plugin-admin-bar&utm_content=no-popups-loaded',
					'parent' => 'popups',
					'meta'   => [
						'target' => '_blank',
					],

				]
			);
		}

		if ( current_user_can( 'edit_posts' ) ) {
			$wp_admin_bar->add_node(
				[
					'id'     => 'all-popups',
					'title'  => __( 'All Popups', 'popup-maker' ),
					'href'   => admin_url( 'edit.php?post_type=popup' ),
					'parent' => 'popup-maker',
				]
			);
			$wp_admin_bar->add_node(
				[
					'id'     => 'new-popups', // Just `new-popup` moves this to the top of the menu for some reason. Leave the `s` to keep it in the right place.
					'title'  => __( 'Create New Popup', 'popup-maker' ),
					'href'   => admin_url( 'post-new.php?post_type=popup' ),
					'parent' => 'popup-maker',
				]
			);
		}

		/**
		 * Tools
		 */
		$wp_admin_bar->add_node(
			[
				'id'     => 'pum-tools',
				'title'  => __( 'Tools', 'popup-maker' ),
				'href'   => '#popup-maker-tools',
				'parent' => 'popup-maker',
			]
		);

		$wp_admin_bar->add_node(
			[
				'id'     => 'flush-popup-cache',
				'title'  => __( 'Flush Popup Cache', 'popup-maker' ),
				'href'   => wp_nonce_url( add_query_arg( 'flush_popup_cache', 'yes' ), 'flush_popup_cache' ),
				'parent' => 'pum-tools',
			]
		);

		/**
		 * Get Selector
		 */
		$wp_admin_bar->add_node(
			[
				'id'     => 'pum-get-selector',
				'title'  => __( 'Get Selector', 'popup-maker' ),
				'href'   => '#popup-maker-get-selector-tool',
				'parent' => 'pum-tools',
			]
		);
	}

	/**
	 * @return array
	 */
	public static function loaded_popups() {
		static $popups;

		if ( ! isset( $popups ) ) {
			$loaded = PUM_Site_Popups::get_loaded_popups();
			$popups = $loaded->posts;
		}

		return $popups;
	}

	/**
	 * Enqueues and prepares our styles and scripts for the admin bar
	 *
	 * @since 1.11.0
	 */
	public static function enqueue_files() {
		if ( ! self::should_render() ) {
			return;
		}
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'pum-admin-bar', Popup_Maker::$URL . 'assets/js/admin-bar' . $suffix . '.js', [ 'jquery' ], Popup_Maker::$VER, true );
		wp_enqueue_style( 'pum-admin-bar-style', Popup_Maker::$URL . 'assets/css/pum-admin-bar' . $suffix . '.css', [], Popup_Maker::$VER );

		$admin_bar_text = [
			'instructions' => __( 'After clicking ok, click the element you want a selector for.', 'popup-maker' ),
			'results'      => _x( 'Selector', 'JS alert for CSS get selector tool', 'popup-maker' ),
		];
		wp_localize_script( 'pum-admin-bar', 'pumAdminBarText', $admin_bar_text );
	}
}

PUM_Modules_Admin_Bar::init();
