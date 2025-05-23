<?php

use PrimeSlider\Utils;
use PrimeSlider\Admin\ModuleService;
use Elementor\Modules\Usage\Module;
use Elementor\Tracker;
/**
 * Prime Slider Admin Settings Class
 */

class PrimeSlider_Admin_Settings {

	public static $modules_list = null;
	public static $modules_names = null;

	public static $modules_list_only_widgets = null;
	public static $modules_names_only_widgets = null;

	public static $modules_list_only_3rdparty = null;
	public static $modules_names_only_3rdparty = null;

	const PAGE_ID = 'prime_slider_options';

	private $settings_api;

	public $responseObj;
	public $showMessage = false;
	private $is_activated = false;

	function __construct() {
		$this->settings_api = new PrimeSlider_Settings_API;

		if (!defined('BDTPS_CORE_HIDE')) {
			add_action('admin_init', [$this, 'admin_init']);
			add_action('admin_menu', [$this, 'admin_menu'], 201);
		}

		/**
		 * Mini-Cart issue fixed
		 * Check if MiniCart activate in EP and Elementor
		 * If both is activated then Show Notice
		 */

		$ps_3rdPartyOption = get_option('prime_slider_third_party_widget');

		$el_use_mini_cart = get_option('elementor_use_mini_cart_template');

		if ($el_use_mini_cart !== false && $ps_3rdPartyOption !== false) {
			if ($ps_3rdPartyOption) {
				if ('yes' == $el_use_mini_cart && isset($ps_3rdPartyOption['wc-mini-cart']) && 'off' !== trim($ps_3rdPartyOption['wc-mini-cart'])) {
					add_action('admin_notices', [$this, 'el_use_mini_cart'], 10, 3);
				}
			}
		}
	}

	/**
	 * Get used widgets.
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */
	public static function get_used_widgets() {

		$used_widgets = array();

		if (class_exists('Elementor\Modules\Usage\Module')) {

			$module     = Module::instance();
			
			$old_error_level = error_reporting();
 			error_reporting(E_ALL & ~E_WARNING); // Suppress warnings
 			$elements = $module->get_formatted_usage('raw');
 			error_reporting($old_error_level); // Restore
			
			$ps_widgets = self::get_ps_widgets_names();

			if (is_array($elements) || is_object($elements)) {

				foreach ($elements as $post_type => $data) {
					foreach ($data['elements'] as $element => $count) {
						if (in_array($element, $ps_widgets, true)) {
							if (isset($used_widgets[$element])) {
								$used_widgets[$element] += $count;
							} else {
								$used_widgets[$element] = $count;
							}
						}
					}
				}
			}
		}

		return $used_widgets;
	}

	/**
	 * Get used separate widgets.
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_used_only_widgets() {

		$used_widgets = array();

		if (class_exists('Elementor\Modules\Usage\Module')) {

			$module     = Module::instance();
			
			$old_error_level = error_reporting();
 			error_reporting(E_ALL & ~E_WARNING); // Suppress warnings
 			$elements = $module->get_formatted_usage('raw');
 			error_reporting($old_error_level); // Restore
			
			$ps_widgets = self::get_ps_only_widgets();

			if (is_array($elements) || is_object($elements)) {

				foreach ($elements as $post_type => $data) {
					foreach ($data['elements'] as $element => $count) {
						if (in_array($element, $ps_widgets, true)) {
							if (isset($used_widgets[$element])) {
								$used_widgets[$element] += $count;
							} else {
								$used_widgets[$element] = $count;
							}
						}
					}
				}
			}
		}

		return $used_widgets;
	}

	/**
	 * Get used only separate 3rdParty widgets.
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_used_only_3rdparty() {

		$used_widgets = array();

		if (class_exists('Elementor\Modules\Usage\Module')) {

			$module     = Module::instance();
			
			$old_error_level = error_reporting();
 			error_reporting(E_ALL & ~E_WARNING); // Suppress warnings
 			$elements = $module->get_formatted_usage('raw');
 			error_reporting($old_error_level); // Restore
			
			$ps_widgets = self::get_ps_only_3rdparty_names();

			if (is_array($elements) || is_object($elements)) {

				foreach ($elements as $post_type => $data) {
					foreach ($data['elements'] as $element => $count) {
						if (in_array($element, $ps_widgets, true)) {
							if (isset($used_widgets[$element])) {
								$used_widgets[$element] += $count;
							} else {
								$used_widgets[$element] = $count;
							}
						}
					}
				}
			}
		}

		return $used_widgets;
	}

	/**
	 * Get unused widgets.
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_unused_widgets() {

		if (!current_user_can('install_plugins')) {
			die();
		}

		$ps_widgets = self::get_ps_widgets_names();

		$used_widgets = self::get_used_widgets();

		$unused_widgets = array_diff($ps_widgets, array_keys($used_widgets));

		return $unused_widgets;
	}

	/**
	 * Get unused separate widgets.
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_unused_only_widgets() {

		if (!current_user_can('install_plugins')) {
			die();
		}

		$ps_widgets = self::get_ps_only_widgets();

		$used_widgets = self::get_used_only_widgets();

		$unused_widgets = array_diff($ps_widgets, array_keys($used_widgets));

		return $unused_widgets;
	}

	/**
	 * Get unused separate 3rdparty widgets.
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_unused_only_3rdparty() {

		if (!current_user_can('install_plugins')) {
			die();
		}

		$ps_widgets = self::get_ps_only_3rdparty_names();

		$used_widgets = self::get_used_only_3rdparty();

		$unused_widgets = array_diff($ps_widgets, array_keys($used_widgets));

		return $unused_widgets;
	}

	/**
	 * Get widgets name
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_ps_widgets_names() {
		$names = self::$modules_names;

		if (null === $names) {
			$names = array_map(
				function ($item) {
					return isset($item['name']) ? 'prime-slider-' . str_replace('_', '-', $item['name']) : 'none';
				},
				self::$modules_list
			);
		}

		return $names;
	}

	/**
	 * Get separate widgets name
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_ps_only_widgets() {
		$names = self::$modules_names_only_widgets;

		if (null === $names) {
			$names = array_map(
				function ($item) {
					return isset($item['name']) ? 'prime-slider-' . str_replace('_', '-', $item['name']) : 'none';
				},
				self::$modules_list_only_widgets
			);
		}

		return $names;
	}

	/**
	 * Get separate 3rdParty widgets name
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_ps_only_3rdparty_names() {
		$names = self::$modules_names_only_3rdparty;

		if (null === $names) {
			$names = array_map(
				function ($item) {
					return isset($item['name']) ? 'prime-slider-' . str_replace('_', '-', $item['name']) : 'none';
				},
				self::$modules_list_only_3rdparty
			);
		}

		return $names;
	}

	/**
	 * Get URL with page id
	 *
	 * @access public
	 *
	 */

	public static function get_url() {
		return admin_url('admin.php?page=' . self::PAGE_ID);
	}

	/**
	 * Init settings API
	 *
	 * @access public
	 *
	 */

	public function admin_init() {

		//set the settings
		$this->settings_api->set_sections($this->get_settings_sections());
		$this->settings_api->set_fields($this->prime_slider_admin_settings());

		//initialize settings
		$this->settings_api->admin_init();
		$this->ps_redirect_to_get_pro();
		if (true === _is_ps_pro_activated()) {
			$this->bdt_redirect_to_renew_link();
		}
	}

	/**
	 * Add Plugin Menus
	 *
	 * @access public
	 *
	 */

	// Redirect to Prime Slider Pro pricing page
	public function ps_redirect_to_get_pro() {
        if (isset($_GET['page']) && $_GET['page'] === self::PAGE_ID . '_get_pro') {
            wp_redirect('https://primeslider.pro/pricing/?utm_source=PrimeSlider&utm_medium=PluginPage&utm_campaign=30%OffOnPrimeSlider&coupon=FREETOPRO');
            exit;
        }
    }

	// Redirect to renew link
	public function bdt_redirect_to_renew_link() {
		if (isset($_GET['page']) && $_GET['page'] === self::PAGE_ID . '_license_renew') {
			wp_redirect('https://account.bdthemes.com/');
			exit;
		}
	}

	public function admin_menu() {
		add_menu_page(
			BDTPS_CORE_TITLE . ' ' . esc_html__('Dashboard', 'bdthemes-prime-slider'),
			BDTPS_CORE_TITLE,
			'manage_options',
			self::PAGE_ID,
			[$this, 'plugin_page'],
			$this->prime_slider_icon(),
			58
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTPS_CORE_TITLE,
			esc_html__('Core Widgets', 'bdthemes-prime-slider'),
			'manage_options',
			self::PAGE_ID . '#prime_slider_active_modules',
			[$this, 'display_page']
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTPS_CORE_TITLE,
			esc_html__('3rd Party Widgets', 'bdthemes-prime-slider'),
			'manage_options',
			self::PAGE_ID . '#prime_slider_third_party_widget',
			[$this, 'display_page']
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTPS_CORE_TITLE,
			esc_html__('Extensions', 'bdthemes-prime-slider'),
			'manage_options',
			self::PAGE_ID . '#prime_slider_elementor_extend',
			[$this, 'display_page']
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTPS_CORE_TITLE,
			esc_html__('Other Settings', 'bdthemes-prime-slider'),
			'manage_options',
			self::PAGE_ID . '#prime_slider_other_settings',
			[$this, 'display_page']
		);

		if (true !== _is_ps_pro_activated()) {
			add_submenu_page(
				self::PAGE_ID,
				BDTPS_CORE_TITLE,
				esc_html__('Upgrade For 30% Off!', 'bdthemes-prime-slider'),
				'manage_options',
				self::PAGE_ID . '_get_pro',
				[$this, 'display_page']
			);
		}
	}

	/**
	 * Get SVG Icons of Prime Slider
	 *
	 * @access public
	 * @return string
	 */

	public function prime_slider_icon() {
		return 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyMy4wLjMsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9IjAgMCAyMzAuNyAyNTQuOCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMjMwLjcgMjU0Ljg7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+DQoJLnN0MHtmaWxsOnVybCgjU1ZHSURfMV8pO30NCgkuc3Qxe2ZpbGw6dXJsKCNTVkdJRF8yXyk7fQ0KCS5zdDJ7ZmlsbDp1cmwoI1NWR0lEXzNfKTt9DQoJLnN0M3tmaWxsOnVybCgjU1ZHSURfNF8pO30NCgkuc3Q0e2ZpbGw6dXJsKCNTVkdJRF81Xyk7fQ0KPC9zdHlsZT4NCjxnPg0KCTxsaW5lYXJHcmFkaWVudCBpZD0iU1ZHSURfMV8iIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIiB4MT0iMTY1Ljg4MTMiIHkxPSItOS4xNzQyIiB4Mj0iLTE0Ljk3ODMiIHkyPSIxOTIuNzE1NiI+DQoJCTxzdG9wICBvZmZzZXQ9IjAiIHN0eWxlPSJzdG9wLWNvbG9yOiNGQzZBMkMiLz4NCgkJPHN0b3AgIG9mZnNldD0iMSIgc3R5bGU9InN0b3AtY29sb3I6I0ZFNTE2QiIvPg0KCTwvbGluZWFyR3JhZGllbnQ+DQoJPHBhdGggY2xhc3M9InN0MCIgZD0iTTIwMi4yLDY5LjJoLTE3NGMtMywwLTUuNS0yLjUtNS41LTUuNVYzMS4xYzAtMywyLjUtNS41LDUuNS01LjVoMTc0YzMsMCw1LjUsMi41LDUuNSw1LjV2MzIuNg0KCQlDMjA3LjcsNjYuOCwyMDUuMiw2OS4yLDIwMi4yLDY5LjJ6Ii8+DQoJPGxpbmVhckdyYWRpZW50IGlkPSJTVkdJRF8yXyIgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiIHgxPSIyMDUuNjI4MSIgeTE9IjI2LjQzMjMiIHgyPSIyNC43Njg1IiB5Mj0iMjI4LjMyMjEiPg0KCQk8c3RvcCAgb2Zmc2V0PSIwIiBzdHlsZT0ic3RvcC1jb2xvcjojRkM2QTJDIi8+DQoJCTxzdG9wICBvZmZzZXQ9IjEiIHN0eWxlPSJzdG9wLWNvbG9yOiNGRTUxNkIiLz4NCgk8L2xpbmVhckdyYWRpZW50Pg0KCTxwYXRoIGNsYXNzPSJzdDEiIGQ9Ik0yMDIuMiwxNDkuMmgtMTc0Yy0zLDAtNS41LTIuNS01LjUtNS41di0zMi42YzAtMywyLjUtNS41LDUuNS01LjVoMTc0YzMsMCw1LjUsMi41LDUuNSw1LjV2MzIuNg0KCQlDMjA3LjcsMTQ2LjgsMjA1LjIsMTQ5LjIsMjAyLjIsMTQ5LjJ6Ii8+DQoJPGxpbmVhckdyYWRpZW50IGlkPSJTVkdJRF8zXyIgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiIHgxPSIyMjMuMDM5IiB5MT0iNDIuMDI5NSIgeDI9IjQyLjE3OTQiIHkyPSIyNDMuOTE5NCI+DQoJCTxzdG9wICBvZmZzZXQ9IjAiIHN0eWxlPSJzdG9wLWNvbG9yOiNGQzZBMkMiLz4NCgkJPHN0b3AgIG9mZnNldD0iMSIgc3R5bGU9InN0b3AtY29sb3I6I0ZFNTE2QiIvPg0KCTwvbGluZWFyR3JhZGllbnQ+DQoJPHBhdGggY2xhc3M9InN0MiIgZD0iTTEyMS42LDIyOS4ySDI4LjJjLTMsMC01LjUtMi41LTUuNS01LjV2LTMyLjZjMC0zLDIuNS01LjUsNS41LTUuNWg5My41YzMsMCw1LjUsMi41LDUuNSw1LjV2MzIuNg0KCQlDMTI3LjIsMjI2LjcsMTI0LjcsMjI5LjIsMTIxLjYsMjI5LjJ6Ii8+DQoJPGxpbmVhckdyYWRpZW50IGlkPSJTVkdJRF80XyIgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiIHgxPSIxNDYuMDMzMSIgeTE9Ii0yNi45NTUiIHgyPSItMzQuODI2NiIgeTI9IjE3NC45MzQ4Ij4NCgkJPHN0b3AgIG9mZnNldD0iMCIgc3R5bGU9InN0b3AtY29sb3I6I0ZDNkEyQyIvPg0KCQk8c3RvcCAgb2Zmc2V0PSIxIiBzdHlsZT0ic3RvcC1jb2xvcjojRkU1MTZCIi8+DQoJPC9saW5lYXJHcmFkaWVudD4NCgk8cGF0aCBjbGFzcz0ic3QzIiBkPSJNNjYuMyw0NS43VjEyN2MwLDMtMi41LDUuNS01LjUsNS41SDI4LjJjLTMsMC01LjUtMi41LTUuNS01LjVWNDUuN2MwLTMsMi41LTUuNSw1LjUtNS41aDMyLjYNCgkJQzYzLjgsNDAuMiw2Ni4zLDQyLjcsNjYuMyw0NS43eiIvPg0KCTxsaW5lYXJHcmFkaWVudCBpZD0iU1ZHSURfNV8iIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIiB4MT0iMjY0LjcxMzQiIHkxPSI3OS4zNjI4IiB4Mj0iODMuODUzNyIgeTI9IjI4MS4yNTI2Ij4NCgkJPHN0b3AgIG9mZnNldD0iMCIgc3R5bGU9InN0b3AtY29sb3I6I0ZDNkEyQyIvPg0KCQk8c3RvcCAgb2Zmc2V0PSIxIiBzdHlsZT0ic3RvcC1jb2xvcjojRkU1MTZCIi8+DQoJPC9saW5lYXJHcmFkaWVudD4NCgk8cGF0aCBjbGFzcz0ic3Q0IiBkPSJNMjA3LjcsMTExLjF2MTEyLjZjMCwzLTIuNSw1LjUtNS41LDUuNWgtMzIuNmMtMywwLTUuNS0yLjUtNS41LTUuNVYxMTEuMWMwLTMsMi41LTUuNSw1LjUtNS41aDMyLjYNCgkJQzIwNS4yLDEwNS42LDIwNy43LDEwOCwyMDcuNywxMTEuMXoiLz4NCjwvZz4NCjwvc3ZnPg0K';
	}

	/**
	 * Get SVG Icons of Prime Slider
	 *
	 * @access public
	 * @return array
	 */

	public function get_settings_sections() {
		$sections = [
			[
				'id'    => 'prime_slider_active_modules',
				'title' => esc_html__('Core Widgets', 'bdthemes-prime-slider')
			],
			[
				'id'    => 'prime_slider_third_party_widget',
				'title' => esc_html__('3rd Party Widgets', 'bdthemes-prime-slider')
			],
			[
				'id'    => 'prime_slider_elementor_extend',
				'title' => esc_html__('Extensions', 'bdthemes-prime-slider')
			],
			[
				'id'    => 'prime_slider_other_settings',
				'title' => esc_html__('Other Settings', 'bdthemes-prime-slider'),
			],
		];

		return $sections;
	}

	/**
	 * Merge Admin Settings
	 *
	 * @access protected
	 * @return array
	 */

	protected function prime_slider_admin_settings() {

		return ModuleService::get_widget_settings(function ($settings) {
			$settings_fields = $settings['settings_fields'];

			self::$modules_list               = array_merge($settings_fields['prime_slider_active_modules'], $settings_fields['prime_slider_third_party_widget']);
			self::$modules_list_only_widgets  = $settings_fields['prime_slider_active_modules'];
			self::$modules_list_only_3rdparty = $settings_fields['prime_slider_third_party_widget'];

			return $settings_fields;
		});
	}

	/**
	 * Get Welcome Panel
	 *
	 * @access public
	 * @return void
	 */

	public function prime_slider_welcome() {
		$track_nw_msg = '';
		if (!Tracker::is_allow_track()) {
			$track_nw     = esc_html__('This feature is not working because the Elementor Usage Data Sharing feature is Not Enabled.', 'bdthemes-prime-slider');
			$track_nw_msg = 'bdt-tooltip="' . $track_nw . '"';
		}
?>

		<div class="ps-dashboard-panel" bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">

			<div class="bdt-grid bdt-grid-medium" bdt-grid bdt-height-match="target: > div > .bdt-card">
				<div class="bdt-width-1-2@m bdt-width-1-4@l">
					<div class="ps-widget-status bdt-card bdt-card-body" <?php echo wp_kses_post($track_nw_msg); ?>>

						<?php
						$used_widgets    = count(self::get_used_widgets());
						$un_used_widgets = count(self::get_unused_widgets());
						?>


						<div class="ps-count-canvas-wrap">
							<h1 class="ps-feature-title"><?php echo esc_html__('All Widgets', 'bdthemes-prime-slider'); ?></h1>
							<div class="bdt-flex bdt-flex-between bdt-flex-middle">
								<div class="ps-count-wrap">
									<div class="ps-widget-count"><?php echo esc_html__('Used:', 'bdthemes-prime-slider'); ?> <b>
											<?php echo esc_html($used_widgets); ?>
										</b></div>
									<div class="ps-widget-count"><?php echo esc_html__('Unused:', 'bdthemes-prime-slider'); ?> <b>
											<?php echo esc_html($un_used_widgets); ?>
										</b></div>
									<div class="ps-widget-count"><?php echo esc_html__('Total:', 'bdthemes-prime-slider'); ?>
										<b>
											<?php echo esc_html($used_widgets + $un_used_widgets); ?>
										</b>
									</div>
								</div>

								<div class="ps-canvas-wrap">
									<canvas id="bdt-db-total-status" style="height: 100px; width: 100px;" data-label="<?php echo esc_html__('Total Widgets Status', 'bdthemes-prime-slider'); ?> - (<?php echo esc_html($used_widgets + $un_used_widgets); ?>)" data-labels="<?php echo esc_attr('Used, Unused'); ?>" data-value="<?php echo esc_attr($used_widgets) . ',' . esc_attr($un_used_widgets); ?>" data-bg="#FFD166, #fff4d9" data-bg-hover="#0673e1, #e71522"></canvas>
								</div>
							</div>
						</div>

					</div>
				</div>
				<div class="bdt-width-1-2@m bdt-width-1-4@l">
					<div class="ps-widget-status bdt-card bdt-card-body" <?php echo wp_kses_post($track_nw_msg); ?>>

						<?php
						$used_only_widgets   = count(self::get_used_only_widgets());
						$unused_only_widgets = count(self::get_unused_only_widgets());
						?>


						<div class="ps-count-canvas-wrap">
							<h1 class="ps-feature-title"><?php echo esc_html__('Core', 'bdthemes-prime-slider'); ?></h1>
							<div class="bdt-flex bdt-flex-between bdt-flex-middle">
								<div class="ps-count-wrap">
									<div class="ps-widget-count"><?php echo esc_html__('Used:', 'bdthemes-prime-slider'); ?> <b>
											<?php echo esc_html($used_only_widgets); ?>
										</b></div>
									<div class="ps-widget-count"><?php echo esc_html__('Unused:', 'bdthemes-prime-slider'); ?> <b>
											<?php echo esc_html($unused_only_widgets); ?>
										</b></div>
									<div class="ps-widget-count"><?php echo esc_html__('Total:', 'bdthemes-prime-slider'); ?>
										<b>
											<?php echo esc_html($used_only_widgets + $unused_only_widgets); ?>
										</b>
									</div>
								</div>

								<div class="ps-canvas-wrap">
									<canvas id="bdt-db-only-widget-status" style="height: 100px; width: 100px;" data-label="<?php echo esc_html__('Core Widgets Status', 'bdthemes-prime-slider'); ?> - (<?php echo esc_attr($used_only_widgets + $unused_only_widgets); ?>)" data-labels="<?php echo esc_attr('Used, Unused'); ?>" data-value="<?php echo esc_attr($used_only_widgets) . ',' . esc_attr($unused_only_widgets); ?>" data-bg="#EF476F, #ffcdd9" data-bg-hover="#0673e1, #e71522"></canvas>
								</div>
							</div>
						</div>

					</div>
				</div>
				<div class="bdt-width-1-2@m bdt-width-1-4@l">
					<div class="ps-widget-status bdt-card bdt-card-body" <?php echo wp_kses_post($track_nw_msg); ?>>

						<?php
						$used_only_3rdparty   = count(self::get_used_only_3rdparty());
						$unused_only_3rdparty = count(self::get_unused_only_3rdparty());
						?>


						<div class="ps-count-canvas-wrap">
							<h1 class="ps-feature-title"><?php echo esc_html__('3rd Party', 'bdthemes-prime-slider'); ?></h1>
							<div class="bdt-flex bdt-flex-between bdt-flex-middle">
								<div class="ps-count-wrap">
									<div class="ps-widget-count"><?php echo esc_html__('Used:', 'bdthemes-prime-slider'); ?> <b>
											<?php echo esc_html($used_only_3rdparty); ?>
										</b></div>
									<div class="ps-widget-count"><?php echo esc_html__('Unused:', 'bdthemes-prime-slider'); ?> <b>
											<?php echo esc_html($unused_only_3rdparty); ?>
										</b></div>
									<div class="ps-widget-count"><?php echo esc_html__('Total:', 'bdthemes-prime-slider'); ?>
										<b>
											<?php echo esc_html($used_only_3rdparty + $unused_only_3rdparty); ?>
										</b>
									</div>
								</div>

								<div class="ps-canvas-wrap">
									<canvas id="bdt-db-only-3rdparty-status" style="height: 100px; width: 100px;" data-label="<?php echo esc_html__('3rd Party Widgets Status', 'bdthemes-prime-slider'); ?> - (<?php echo esc_attr($used_only_3rdparty + $unused_only_3rdparty); ?>)" data-labels="<?php echo esc_attr('Used, Unused'); ?>" data-value="<?php echo esc_attr($used_only_3rdparty) . ',' . esc_attr($unused_only_3rdparty); ?>" data-bg="#06D6A0, #B6FFEC" data-bg-hover="#0673e1, #e71522"></canvas>
								</div>
							</div>
						</div>

					</div>
				</div>

				<div class="bdt-width-1-2@m bdt-width-1-4@l">
					<div class="ps-widget-status bdt-card bdt-card-body" <?php echo wp_kses_post($track_nw_msg); ?>>

						<div class="ps-count-canvas-wrap">
							<h1 class="ps-feature-title"><?php echo esc_html__('Active', 'bdthemes-prime-slider'); ?></h1>
							<div class="bdt-flex bdt-flex-between bdt-flex-middle">
								<div class="ps-count-wrap">
									<div class="ps-widget-count"><?php echo esc_html__('Core:', 'bdthemes-prime-slider'); ?> <b id="bdt-total-widgets-status-core"></b></div>
									<div class="ps-widget-count"><?php echo esc_html__('3rd Party:', 'bdthemes-prime-slider'); ?> <b id="bdt-total-widgets-status-3rd"></b></div>
									<div class="ps-widget-count"><?php echo esc_html__('Total:', 'bdthemes-prime-slider'); ?> <b id="bdt-total-widgets-status-heading"></b></div>
								</div>

								<div class="ps-canvas-wrap">
									<canvas id="bdt-total-widgets-status" style="height: 100px; width: 100px;" data-label="<?php echo esc_html__('Total Active Widgets Status', 'bdthemes-prime-slider'); ?>" data-labels="<?php echo esc_attr('Core, 3rd Party'); ?>" data-bg="#0680d6, #B0EBFF" data-bg-hover="#0673e1, #B0EBFF">
									</canvas>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>

			<?php if ( !Tracker::is_allow_track() ) : ?>
				<div class="bdt-border-rounded bdt-box-shadow-small bdt-alert-warning" bdt-alert>
					<a href class="bdt-alert-close" bdt-close></a>
					<div class="bdt-text-default">
					<?php
					printf(
						esc_html__('To view widgets analytics, Elementor Usage Data Sharing feature needs to be activated. Please activate the feature to get widget analytics instantly %s', 'bdthemes-prime-slider'),
						'<a href="' . esc_url(admin_url('admin.php?page=elementor')) . '">' . esc_html__('from here', 'bdthemes-prime-slider') . '</a>'
					);
					?>

					</div>
				</div>
			<?php endif; ?>

			<div class="bdt-grid bdt-grid-medium" bdt-grid bdt-height-match="target: > div > .bdt-card">
				<div class="bdt-width-2-5@m ps-support-section">
					<div class="ps-support-content bdt-card bdt-card-body">
						<h1 class="ps-feature-title"><?php echo esc_html__('Support And Feedback', 'bdthemes-prime-slider'); ?></h1>
						<p><?php echo esc_html__('Feeling like to consult with an expert? Take live Chat support immediately from', 'bdthemes-prime-slider'); ?> <a href="https://PrimeSlider.pro" target="_blank" rel="">PrimeSlider</a>. <?php echo esc_html__('We are always ready to help you 24/7.', 'bdthemes-prime-slider'); ?></p>
						<p><strong><?php echo esc_html__('Or if you are facing technical issues with our plugin, then please create a support ticket', 'bdthemes-prime-slider'); ?></strong></p>
						<a class="bdt-button bdt-btn-blue bdt-margin-small-top bdt-margin-small-right" target="_blank" rel="" href="https://bdthemes.com/all-knowledge-base-of-prime-slider/"><?php echo esc_html__('Knowledge Base', 'bdthemes-prime-slider'); ?></a>
						<a class="bdt-button bdt-btn-grey bdt-margin-small-top" target="_blank" href="https://bdthemes.com/support/"><?php echo esc_html__('Get Support', 'bdthemes-prime-slider'); ?></a>
					</div>
				</div>

				<div class="bdt-width-3-5@m">
					<div class="bdt-card bdt-card-body ps-system-requirement">
						<h1 class="ps-feature-title bdt-margin-small-bottom"><?php echo esc_html__('System Requirement', 'bdthemes-prime-slider'); ?></h1>
						<?php $this->prime_slider_system_requirement(); ?>
					</div>
				</div>
			</div>

			<div class="bdt-grid bdt-grid-medium" bdt-grid bdt-height-match="target: > div > .bdt-card">
				<div class="bdt-width-1-2@m ps-support-section">
					<div class="bdt-card bdt-card-body ps-feedback-bg">
						<h1 class="ps-feature-title"><?php echo esc_html__('Missing Any Feature?', 'bdthemes-prime-slider'); ?></h1>
						<p style="max-width: 520px;"><?php echo esc_html__('Are you in need of a feature that is not available in our plugin?
							Feel free to do a feature request from here,', 'bdthemes-prime-slider'); ?></p>
						<a class="bdt-button bdt-btn-grey bdt-margin-small-top" target="_blank" rel="" href="https://feedback.bdthemes.com/b/6vr2250l/feature-requests/"><?php echo esc_html__('Request Feature', 'bdthemes-prime-slider'); ?></a>
					</div>
				</div>

				<div class="bdt-width-1-2@m">
					<div class="bdt-card bdt-card-body ps-tryaddon-bg">
						<h1 class="ps-feature-title"><?php echo esc_html__('Try Our Others Plugins', 'bdthemes-prime-slider'); ?></h1>
						<p style="max-width: 520px;">
							<?php 
								echo esc_html__('Element Pack, Ultimate Post Kit, Ultimate Store Kit, Pixel Gallery & Live Copy Paste addons for Elementor is the best slider, blogs and eCommerce plugin for WordPress.', 'bdthemes-prime-slider');
								echo '<br>';
								echo esc_html__('Also, try our new plugin ZoloBlocks for Gutenberg.', 'bdthemes-prime-slider');
							?>
						</p>
						<div class="bdt-others-plugins-link">
							<a class="bdt-button bdt-btn-ep bdt-margin-small-right" target="_blank" href="https://wordpress.org/plugins/bdthemes-element-pack-lite/" bdt-tooltip="<?php echo esc_html__('Element Pack Lite provides more than 50+ essential elements for everyday applications to simplify the whole web building process. It\'s Free! Download it.', 'bdthemes-prime-slider'); ?>">Element pack</a>
							<a class="bdt-button bdt-btn-zb bdt-margin-small-right" target="_blank" rel="" href="https://wordpress.org/plugins/zoloblocks/" bdt-tooltip="<?php echo esc_html__('ZoloBlocks is a collection of creative Gutenberg blocks for WordPress. It\'s Free! Download it.', 'bdthemes-prime-slider'); ?>">ZoloBlocks</a>
							<a class="bdt-button bdt-btn-upk bdt-margin-small-right" target="_blank" rel="" href="https://wordpress.org/plugins/ultimate-post-kit/" bdt-tooltip="<?php echo esc_html__('Best blogging addon for building quality blogging website with fine-tuned features and widgets. It\'s Free! Download it.', 'bdthemes-prime-slider'); ?>">Ultimate Post Kit</a>
							<a class="bdt-button bdt-btn-usk bdt-margin-small-right" target="_blank" rel="" href="https://wordpress.org/plugins/ultimate-store-kit/" bdt-tooltip="<?php echo esc_html__('The only eCommmerce addon for answering all your online store design problems in one package. It\'s Free! Download it.', 'bdthemes-prime-slider'); ?>">Ultimate Store Kit</a>
							<a class="bdt-button bdt-btn-pg bdt-margin-small-right" target="_blank" href="https://wordpress.org/plugins/pixel-gallery/" bdt-tooltip="<?php echo esc_html__('Pixel Gallery provides more than 30+ essential elements for everyday applications to simplify the whole web building process. It\'s Free! Download it.', 'bdthemes-prime-slider'); ?>">Pixel Gallery</a>
							<a class="bdt-button bdt-btn-live-copy bdt-margin-small-right" target="_blank" rel="" href="https://wordpress.org/plugins/live-copy-paste/" bdt-tooltip="<?php echo esc_html__('Superfast cross-domain copy-paste mechanism for WordPress websites with true UI copy experience. It\'s Free! Download it.', 'bdthemes-prime-slider'); ?>">Live Copy Paste</a>
						</div>
					</div>
				</div>
			</div>

		</div>


	<?php
	}

	/**
	 * Get Pro
	 *
	 * @access public
	 * @return void
	 */

	function prime_slider_get_pro() {
	?>
		<div class="ps-dashboard-panel" bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">

			<div class="bdt-grid" bdt-grid bdt-height-match="target: > div > .bdt-card" style="max-width: 800px; margin-left: auto; margin-right: auto;">
				<div class="bdt-width-1-1@m ps-comparision bdt-text-center">
					<div class="bdt-flex bdt-flex-between bdt-flex-middle">
						<div class="bdt-text-left">
							<h1 class="bdt-text-bold"><?php echo esc_html__('WHY GO WITH PRO?', 'bdthemes-prime-slider'); ?></h1>
							<h2><?php echo esc_html__('Just Compare With ', 'bdthemes-prime-slider'); ?>Prime Slider<?php echo esc_html__(' Free Vs Pro', 'bdthemes-prime-slider'); ?></h2>
						</div>
						<?php if (true !== _is_ps_pro_activated()) : ?>
							<div class="ps-purchase-button">
								<a href="https://primeslider.pro/pricing/" target="_blank"><?php echo esc_html__('Purchase Now', 'bdthemes-prime-slider'); ?></a>
							</div>
						<?php endif; ?>
					</div>


					<div>

						<ul class="bdt-list bdt-list-divider bdt-text-left bdt-text-normal" style="font-size: 15px;">


							<li class="bdt-text-bold">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m"><?php echo esc_html__('Features', 'bdthemes-prime-slider'); ?></div>
									<div class="bdt-width-auto@m"><?php echo esc_html__('Free', 'bdthemes-prime-slider'); ?></div>
									<div class="bdt-width-auto@m"><?php echo esc_html__('Pro', 'bdthemes-prime-slider'); ?></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m"><span bdt-tooltip="pos: top-left; title: <?php echo esc_html__('Free have 27+ Widgets but Pro have 21+ core widgets', 'bdthemes-prime-slider'); ?>"><?php echo esc_html__('Core Widgets', 'bdthemes-prime-slider'); ?></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m"><span bdt-tooltip="pos: top-left; title: <?php echo esc_html__('Free have 3+ Widgets but Pro have 3+ 3rd party widgets', 'bdthemes-prime-slider'); ?>"><?php echo esc_html__('3rd Party Widgets', 'bdthemes-prime-slider'); ?></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m"><?php echo esc_html__('Theme Compatibility', 'bdthemes-prime-slider'); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m"><?php echo esc_html__('Dynamic Content & Custom Fields Capabilities', 'bdthemes-prime-slider'); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m"><?php echo esc_html__('Proper Documentation', 'bdthemes-prime-slider'); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m"><?php echo esc_html__('Updates & Support', 'bdthemes-prime-slider'); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m"><?php echo esc_html__('Ready Made Pages', 'bdthemes-prime-slider'); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m"><?php echo esc_html__('Ready Made Blocks', 'bdthemes-prime-slider'); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m"><?php echo esc_html__('Elementor Extended Widgets', 'bdthemes-prime-slider'); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m"><?php echo esc_html__('Live Copy or Paste', 'bdthemes-prime-slider'); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m"><?php echo esc_html__('Duplicator', 'bdthemes-prime-slider'); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">Rooten<?php echo esc_html__(' Theme Pro Features', 'bdthemes-prime-slider'); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m"><?php echo esc_html__('Priority Support', 'bdthemes-prime-slider'); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m"><?php echo esc_html__('Reveal Effects', 'bdthemes-prime-slider'); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
						</ul>


						<!-- <div class="ps-dashboard-divider"></div> -->


						<div class="ps-more-features bdt-card bdt-card-body bdt-margin-medium-top bdt-padding-large">
							<ul class="bdt-list bdt-list-divider bdt-text-left" style="font-size: 15px;">
								<li>
									<div class="bdt-grid bdt-grid-small">
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span><?php echo esc_html__(' Incredibly Advanced', 'bdthemes-prime-slider'); ?>
										</div>
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span><?php echo esc_html__(' Refund or Cancel Anytime', 'bdthemes-prime-slider'); ?>
										</div>
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span><?php echo esc_html__(' Dynamic Content', 'bdthemes-prime-slider'); ?>
										</div>
									</div>
								</li>

								<li>
									<div class="bdt-grid bdt-grid-small">
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span><?php echo esc_html__(' Super-Flexible Widgets', 'bdthemes-prime-slider'); ?>
										</div>
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span><?php echo esc_html__(' 24/7 Premium Support', 'bdthemes-prime-slider'); ?>
										</div>
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span><?php echo esc_html__(' Third Party Plugins', 'bdthemes-prime-slider'); ?>
										</div>
									</div>
								</li>

								<li>
									<div class="bdt-grid bdt-grid-small">
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span><?php echo esc_html__(' Special Discount!', 'bdthemes-prime-slider'); ?>
										</div>
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span><?php echo esc_html__(' Custom Field Integration', 'bdthemes-prime-slider'); ?>
										</div>
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span><?php echo esc_html__(' With Live Chat Support', 'bdthemes-prime-slider'); ?>
										</div>
									</div>
								</li>

								<li>
									<div class="bdt-grid bdt-grid-small">
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span><?php echo esc_html__(' Trusted Payment Methods', 'bdthemes-prime-slider'); ?>
										</div>
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span><?php echo esc_html__(' Interactive Effects', 'bdthemes-prime-slider'); ?>
										</div>
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span><?php echo esc_html__(' Video Tutorial', 'bdthemes-prime-slider'); ?>
										</div>
									</div>
								</li>
							</ul>

							<?php if (true !== _is_ps_pro_activated()) : ?>
								<div class="ps-purchase-button bdt-margin-medium-top">
									<a href="https://primeslider.pro/pricing/" target="_blank"><?php echo esc_html__('Purchase Now', 'bdthemes-prime-slider'); ?></a>
								</div>
							<?php endif; ?>

						</div>

					</div>
				</div>
			</div>

		</div>
	<?php
	}


	/**
	 * Display System Requirement
	 *
	 * @access public
	 * @return void
	 */

	function prime_slider_system_requirement() {
		$php_version        = phpversion();
		$max_execution_time = ini_get('max_execution_time');
		$memory_limit       = ini_get('memory_limit');
		$post_limit         = ini_get('post_max_size');
		$uploads            = wp_upload_dir();
		$upload_path        = $uploads['basedir'];

		$environment = Utils::get_environment_info();


	?>
		<ul class="check-system-status bdt-grid bdt-child-width-1-2@m bdt-grid-small ">
			<li>
				<div>

					<span class="label1"><?php echo esc_html__('PHP Version:', 'bdthemes-prime-slider'); ?> </span>

					<?php
					if (version_compare($php_version, '7.0.0', '<')) {
						echo '<span class="invalid"><i class="dashicons-before dashicons-no-alt"></i></span>';
						echo '<span class="label2" title=" '. esc_html__('Min: 7.0 Recommended', 'bdthemes-prime-slider') .'" bdt-tooltip>'. esc_html__('Currently: ', 'bdthemes-prime-slider') .' ' . esc_html($php_version) . '</span>';
					} else {
						echo '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
						echo '<span class="label2">'. esc_html__('Currently: ', 'bdthemes-prime-slider') .'' . esc_html($php_version) . '</span>';
					}
					?>
				</div>
			</li>

			<li>
				<div>
					<span class="label1"><?php echo esc_html__('Max execution time: ', 'bdthemes-prime-slider'); ?></span>

					<?php
					if ($max_execution_time < '90') {
						echo '<span class="invalid"><i class="dashicons-before dashicons-no-alt"></i></span>';
						echo '<span class="label2" title="'. esc_html__('Min: 90 Recommended', 'bdthemes-prime-slider') .'" bdt-tooltip>'. esc_html__('Currently: ', 'bdthemes-prime-slider') .'' . esc_html($max_execution_time) . '</span>';
					} else {
						echo '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
						echo '<span class="label2">'. esc_html__('Currently: ', 'bdthemes-prime-slider') .'' . esc_html($max_execution_time) . '</span>';
					}
					?>
				</div>
			</li>
			<li>
				<div>
					<span class="label1"><?php echo esc_html__('Memory Limit: ', 'bdthemes-prime-slider'); ?></span>

					<?php
					if (intval($memory_limit) < '812') {
						echo '<span class="invalid"><i class="dashicons-before dashicons-no-alt"></i></span>';
						echo '<span class="label2" title="'. esc_html__('Min: 812M Recommended', 'bdthemes-prime-slider') .'" bdt-tooltip>'. esc_html__('Currently: ', 'bdthemes-prime-slider') .'' . esc_html($memory_limit) . '</span>';
					} else {
						echo '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
						echo '<span class="label2">'. esc_html__('Currently: ', 'bdthemes-prime-slider') .'' . esc_html($memory_limit) . '</span>';
					}
					?>
				</div>
			</li>
			<li>
				<div>
					<span class="label1"><?php echo esc_html__('Max Post Limit: ', 'bdthemes-prime-slider'); ?></span>

					<?php
					if (intval($post_limit) < '32') {
						echo '<span class="invalid"><i class="dashicons-before dashicons-no-alt"></i></span>';
						echo '<span class="label2" title="'. esc_html__('Min: 32M Recommended', 'bdthemes-prime-slider') .'" bdt-tooltip>'. esc_html__('Currently: ', 'bdthemes-prime-slider') .'' . esc_html($post_limit) . '</span>';
					} else {
						echo '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
						echo '<span class="label2">'. esc_html__('Currently: ', 'bdthemes-prime-slider') .'' . esc_html($post_limit) . '</span>';
					}
					?>
				</div>
			</li>

			<li>
				<div>
					<span class="label1"><?php echo esc_html__('Uploads folder writable: ', 'bdthemes-prime-slider'); ?></span>

					<?php
					if (!is_writable($upload_path)) {
						echo '<span class="invalid"><i class="dashicons-before dashicons-no-alt"></i></span>';
					} else {
						echo '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
					}
					?>
				</div>
			</li>

			<li>
				<div>
					<span class="label1"><?php echo esc_html__('MultiSite: ', 'bdthemes-prime-slider'); ?></span>

					<?php
					if ($environment['wp_multisite']) {
						echo '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
						echo '<span class="label2">'. esc_html__('MultiSite', 'bdthemes-prime-slider') .'</span>';
					} else {
						echo '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
						echo '<span class="label2">'. esc_html__('No MultiSite', 'bdthemes-prime-slider') .'</span>';
					}
					?>
				</div>
			</li>

			<li>
				<div>
					<span class="label1"><?php echo esc_html__('GZip Enabled: ', 'bdthemes-prime-slider'); ?></span>

					<?php
					if ($environment['gzip_enabled']) {
						echo '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
					} else {
						echo '<span class="invalid"><i class="dashicons-before dashicons-no-alt"></i></span>';
					}
					?>
				</div>
			</li>

			<li>
				<div>
					<span class="label1"><?php echo esc_html__('Debug Mode: ', 'bdthemes-prime-slider'); ?></span>
					<?php
					if ($environment['wp_debug_mode']) {
						echo '<span class="invalid"><i class="dashicons-before dashicons-no-alt"></i></span>';
						echo '<span class="label2">'. esc_html__('Currently Turned On', 'bdthemes-prime-slider') .'</span>';
					} else {
						echo '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
						echo '<span class="label2">'. esc_html__('Currently Turned Off', 'bdthemes-prime-slider') .'</span>';
					}
					?>
				</div>
			</li>

		</ul>

		<div class="bdt-admin-alert">
			<?php 
			echo '<strong>' . esc_html__('Note: ', 'bdthemes-prime-slider') . '</strong>';
			echo esc_html__('If you have multiple addons like Prime Slider so you need some more requirement some cases so make sure you added more memory for others addon too.', 'bdthemes-prime-slider');
			?>
		</div>
	<?php
	}

	/**
	 * Display Plugin Page
	 *
	 * @access public
	 * @return void
	 */

	function plugin_page() {

		echo '<div class="wrap prime-slider-dashboard">';
		echo '<h1>' . wp_kses_post(BDTPS_CORE_TITLE) . esc_html__(' Settings', 'bdthemes-prime-slider') . '</h1>';

		$this->settings_api->show_navigation();

	?>


		<div class="bdt-switcher bdt-tab-container bdt-container-xlarge">
			<div id="prime_slider_welcome_page" class="ps-option-page group">
				<?php $this->prime_slider_welcome(); ?>

				<?php if (!defined('BDTPS_CORE_WL')) {
					$this->footer_info();
				} ?>
			</div>

			<?php
			$this->settings_api->show_forms();
			?>

			<?php if (_is_ps_pro_activated() !== true) : ?>
				<div id="prime_slider_get_pro" class="ps-option-page group">
					<?php $this->prime_slider_get_pro(); ?>
				</div>
			<?php endif; ?>

			<div id="prime_slider_pro_license_settings_page" class="ps-option-page group">

				<?php
				if (_is_ps_pro_activated() == true) {
					apply_filters('ps_license_page', '');
				}
				?>
			</div>

		</div>

		</div>

		<?php

		$this->script();

		?>

	<?php
	}


	/**
	 * Tabbable JavaScript codes & Initiate Color Picker
	 *
	 * This code uses localstorage for displaying active tabs
	 */
	function script() {
	?>
		<script>
			jQuery(document).ready(function() {
				jQuery('.ps-no-result').removeClass('bdt-animation-shake');
			});

			function filterSearch(e) {
				var parentID = '#' + jQuery(e).data('id');

				var search = jQuery(parentID).find('.bdt-search-input').val().toLowerCase();

				jQuery(".ps-options .ps-option-item").filter(function() {
					jQuery(this).toggle(jQuery(this).attr('data-widget-name').toLowerCase().indexOf(search) > -1)
				});

				if (!search) {
					jQuery(parentID).find('.bdt-search-input').attr('bdt-filter-control', "");
					jQuery(parentID).find('.ps-widget-all').trigger('click');
				} else {
					jQuery(parentID).find('.bdt-search-input').attr('bdt-filter-control', "filter: [data-widget-name*='" + search + "']");
					jQuery(parentID).find('.bdt-search-input').removeClass('bdt-active'); // Thanks to Bar-Rabbas
					jQuery(parentID).find('.bdt-search-input').trigger('click');
				}
			}

			jQuery('.ps-options-parent').each(function(e, item) {
				var eachItem = '#' + jQuery(item).attr('id');
				jQuery(eachItem).on("beforeFilter", function() {
					jQuery(eachItem).find('.ps-no-result').removeClass('bdt-animation-shake');
				});

				jQuery(eachItem).on("afterFilter", function() {

					var isElementVisible = false;
					var i = 0;

					while (!isElementVisible && i < jQuery(eachItem).find(".ps-option-item").length) {
						if (jQuery(eachItem).find(".ps-option-item").eq(i).is(":visible")) {
							isElementVisible = true;
						}
						i++;
					}

					if (isElementVisible === false) {
						jQuery(eachItem).find('.ps-no-result').addClass('bdt-animation-shake');
					}
				});
			});

			function clearSearchInputs(context) {
				context.find('.bdt-search-input').val('').attr('bdt-filter-control', '');
			}

			jQuery('.ps-widget-filter-nav li a').on('click', function () {
				const wrapper = jQuery(this).closest('.bdt-widget-filter-wrapper');
				clearSearchInputs(wrapper);
			});

			jQuery('.bdt-dashboard-navigation li a').on('click', function () {
				const tabContainer = jQuery(this).closest('.bdt-dashboard-navigation').siblings('.bdt-tab-container');
				clearSearchInputs(tabContainer);
					tabContainer.find('.bdt-search-input').trigger('keyup');
			});

			jQuery(document).ready(function($) {
				'use strict';

				function hashHandler() {
					var $tab = jQuery('.prime-slider-dashboard .bdt-tab');
					if (window.location.hash) {
						var hash = window.location.hash.substring(1);
						bdtUIkit.tab($tab).show(jQuery('#bdt-' + hash).data('tab-index'));
					}
				}

				function onWindowLoad() {
 					hashHandler();
 				}

 				if (document.readyState === 'complete') {
 					onWindowLoad();
 				} else {
 					jQuery(window).on('load', onWindowLoad);
 				}

				window.addEventListener("hashchange", hashHandler, true);

				jQuery('.toplevel_page_prime_slider_options > ul > li > a ').on('click', function(event) {
					jQuery(this).parent().siblings().removeClass('current');
					jQuery(this).parent().addClass('current');
				});

				jQuery('#prime_slider_active_modules_page a.ps-active-all-widget').on('click', function() {

					jQuery('#prime_slider_active_modules_page .checkbox:visible').not("[disabled]").each(function() {
						jQuery(this).attr('checked', 'checked').prop("checked", true);
					});

					jQuery(this).addClass('bdt-active');
					jQuery('a.ps-deactive-all-widget').removeClass('bdt-active');
				});

				jQuery('#prime_slider_active_modules_page a.ps-deactive-all-widget').on('click', function() {

					jQuery('#prime_slider_active_modules_page .checkbox:visible').not("[disabled]").each(function() {
						jQuery(this).removeAttr('checked');
					});

					jQuery(this).addClass('bdt-active');
					jQuery('a.ps-active-all-widget').removeClass('bdt-active');
				});

				jQuery('#prime_slider_third_party_widget_page a.ps-active-all-widget').on('click', function() {

					jQuery('#prime_slider_third_party_widget_page .checkbox:visible').not("[disabled]").each(function() {
						jQuery(this).attr('checked', 'checked').prop("checked", true);
					});

					jQuery(this).addClass('bdt-active');
					jQuery('a.ps-deactive-all-widget').removeClass('bdt-active');
				});

				jQuery('#prime_slider_third_party_widget_page a.ps-deactive-all-widget').on('click', function() {

					jQuery('#prime_slider_third_party_widget_page .checkbox:visible').not("[disabled]").each(function() {
						jQuery(this).removeAttr('checked');
					});

					jQuery(this).addClass('bdt-active');
					jQuery('a.ps-active-all-widget').removeClass('bdt-active');
				});

				jQuery('#prime_slider_elementor_extend_page a.ps-active-all-widget').on('click', function() {

					jQuery('#prime_slider_elementor_extend_page .checkbox:visible').not("[disabled]").each(function() {
						jQuery(this).attr('checked', 'checked').prop("checked", true);
					});

					jQuery(this).addClass('bdt-active');
					jQuery('a.ps-deactive-all-widget').removeClass('bdt-active');
				});

				jQuery('#prime_slider_elementor_extend_page a.ps-deactive-all-widget').on('click', function() {

					jQuery('#prime_slider_elementor_extend_page .checkbox:visible').not("[disabled]").each(function() {
						jQuery(this).removeAttr('checked');
					});

					jQuery(this).addClass('bdt-active');
					jQuery('a.ps-active-all-widget').removeClass('bdt-active');
				});


				jQuery('form.settings-save').on('submit', function(event) {
					event.preventDefault();

					bdtUIkit.notification({
						message: '<div bdt-spinner></div> <?php esc_html_e('Please wait, Saving settings...', 'bdthemes-prime-slider') ?>',
						timeout: false
					});

					jQuery(this).ajaxSubmit({
						success: function() {
							bdtUIkit.notification.closeAll();
							bdtUIkit.notification({
								message: '<span class="dashicons dashicons-yes"></span> <?php esc_html_e('Settings Saved Successfully.', 'bdthemes-prime-slider') ?>',
								status: 'primary'
							});
						},
						error: function(data) {
							bdtUIkit.notification.closeAll();
							bdtUIkit.notification({
								message: '<span bdt-icon=\'icon: warning\'></span> <?php esc_html_e('Unknown error, make sure access is correct!', 'bdthemes-prime-slider') ?>',
								status: 'warning'
							});
						}
					});

					return false;
				});

				jQuery('#prime_slider_active_modules_page .ps-pro-inactive .checkbox').each(function() {
					jQuery(this).removeAttr('checked');
					jQuery(this).attr("disabled", true);
				});
				jQuery('#prime_slider_third_party_widget_page .ps-pro-inactive .checkbox').each(function() {
					jQuery(this).removeAttr('checked');
					jQuery(this).attr("disabled", true);
				});
				jQuery('#prime_slider_elementor_extend_page .ps-pro-inactive .checkbox').each(function() {
					jQuery(this).removeAttr('checked');
					jQuery(this).attr("disabled", true);
				});
				jQuery('#prime_slider_other_settings_page .ps-pro-inactive .checkbox').each(function() {
					jQuery(this).removeAttr('checked');
					jQuery(this).attr("disabled", true);
				});

			});

			jQuery(document).ready(function ($) {
                const getProLink = $('a[href="admin.php?page=prime_slider_options_get_pro"]');
                if (getProLink.length) {
                    getProLink.attr('target', '_blank');
                }
            });

			// License Renew Redirect
			jQuery(document).ready(function ($) {
                const renewalLink = $('a[href="admin.php?page=prime_slider_options_license_renew"]');
                if (renewalLink.length) {
                    renewalLink.attr('target', '_blank');
                }
            });
		</script>
	<?php
	}

	/**
	 * Display Footer
	 *
	 * @access public
	 * @return void
	 */

	function footer_info() {
	?>

		<div class="prime-slider-footer-info bdt-margin-medium-top">

			<div class="bdt-grid ">

				<div class="bdt-width-auto@s ps-setting-save-btn">



				</div>

				<div class="bdt-width-expand@s bdt-text-right">
					<p class="">
						<?php 
						echo esc_html__('Prime Slider plugin made with love by', 'bdthemes-prime-slider') . ' <a target="_blank" href="https://bdthemes.com">BdThemes</a> ' . esc_html__('Team.', 'bdthemes-prime-slider');
						echo '<br>';
						echo esc_html__('All rights reserved by', 'bdthemes-prime-slider') . ' <a target="_blank" href="https://bdthemes.com">BdThemes.com</a>.';
						?>
					</p>
				</div>
			</div>

		</div>

<?php
	}

	/**
	 * Get all the pages
	 *
	 * @return array page names with key value pairs
	 */
	function get_pages() {
		$pages         = get_pages();
		$pages_options = [];
		if ($pages) {
			foreach ($pages as $page) {
				$pages_options[$page->ID] = $page->post_title;
			}
		}

		return $pages_options;
	}
}

new PrimeSlider_Admin_Settings();
