<?php

class wsNewMenuHighlighter {
	const ITEM_SLUG_INDEX = 2;
	const ITEM_TITLE_INDEX = 0;
	const ITEM_CLASS_INDEX = 4;

	const STORAGE_KEY = 'ws_nmh_seen_menus';
	const AJAX_FLAG_ACTION = 'nmh-flag-as-seen';
	const COOKIE_NAME = 'ws_nmh_pending_seen_urls';

	const MAX_URLS_PER_USER = 700;
	const MAX_URLS_PER_REQUEST = 100;
	const MAX_URL_LENGTH = 512;

	static $blacklist = array(
		//These items are invisible when the theme customizer is available.
		'themes.php?page=custom-header'     => true,
		'themes.php?page=custom-background' => true,
		//Files in /wp-admin.
		'customize.php'                     => true,
		'edit-comments.php'                 => true,
		'edit-tags.php'                     => true,
		'edit.php'                          => true,
		'export.php'                        => true,
		'import.php'                        => true,
		'index.php'                         => true,
		'link-add.php'                      => true,
		'link-manager.php'                  => true,
		'media-new.php'                     => true,
		'nav-menus.php'                     => true,
		'options-discussion.php'            => true,
		'options-general.php'               => true,
		'options-media.php'                 => true,
		'options-permalink.php'             => true,
		'options-reading.php'               => true,
		'options-writing.php'               => true,
		'plugin-editor.php'                 => true,
		'plugin-install.php'                => true,
		'plugins.php'                       => true,
		'post-new.php'                      => true,
		'profile.php'                       => true,
		'privacy.php'                       => true,
		'site-health.php'                   => true,
		'theme-editor.php'                  => true,
		'themes.php'                        => true,
		'tools.php'                         => true,
		'update-core.php'                   => true,
		'upload.php'                        => true,
		'user-new.php'                      => true,
		'users.php'                         => true,
		'widgets.php'                       => true,
		//Network admin items.
		'settings.php'                      => true,
		'site-new.php'                      => true,
		'sites.php'                         => true,
		'theme-install.php'                 => true,
		'upgrade.php'                       => true,

		//Hidden ACF menu. It's used to show the "Welcome to Advanced Custom Fields" page.
		'edit.php?post_type=acf-field-group&page=acf-settings-info' => true,

		//The Forminator Forms plugin adds some menus, then hides them in "admin_head" hooks.
		'admin.php?page=forminator-cform-wizard'     => true,
		'admin.php?page=forminator-cform-view'       => true,
		'admin.php?page=forminator-poll-wizard'      => true,
		'admin.php?page=forminator-poll-view'        => true,
		'admin.php?page=forminator-nowrong-wizard'   => true,
		'admin.php?page=forminator-knowledge-wizard' => true,
		'admin.php?page=forminator-quiz-view'        => true,
	);

	private $menusWithNewSubmenus = array();
	private $seenMenuUrls = array();
	private $isFirstRun = false;

	public function __construct() {
		//Run after AME replaces the menu so that we don't pollute the menu editor with our flags and classes.
		if ( class_exists('WPMenuEditor', false) ) {
			add_action('admin_menu_editor-menu_replaced', array($this, 'parseAdminMenu'));
			add_action('admin_menu_editor-menu_replacement_skipped', array($this, 'parseAdminMenu'));
		} else {
			add_action('admin_menu', array($this, 'parseAdminMenu'), 9000);
		}

		add_action('admin_enqueue_scripts', array($this, 'enqueueDependencies'));
		add_action('wp_ajax_' . self::AJAX_FLAG_ACTION, array($this, 'ajaxFlagAsSeen'));

		add_action('admin_init', array($this, 'flagUrlsFromCookie'));
	}

	public function parseAdminMenu() {
		if ( !current_user_can('activate_plugins') ) {
			return;
		}

		global $menu, $submenu;
		$this->seenMenuUrls = $this->loadSeenMenus();

		if ( empty($this->seenMenuUrls) ) {
			$this->isFirstRun = true;
		}

		foreach ($submenu as $parent => &$items) {
			foreach ($items as &$submenuItem) {
				$submenuItem = $this->processItem($submenuItem, $parent);
			}
		}
		unset($items, $submenuItem);

		foreach ($menu as &$item) {
			$item = $this->processItem($item);
		}

		if ( $this->isFirstRun ) {
			$urls = array_keys($this->seenMenuUrls);
			$this->seenMenuUrls = array();
			$this->flagAsSeen($urls, true);
		}
	}

	private function loadSeenMenus() {
		$seenMenuUrls = get_user_meta(get_current_user_id(), self::STORAGE_KEY, true);
		if ( !is_array($seenMenuUrls) ) {
			$seenMenuUrls = array();
		}
		return $seenMenuUrls;
	}

	private function processItem($item, $parentSlug = null) {
		if ( $this->isIgnoredItem($item) ) {
			return $item;
		}

		$itemSlug = $item[self::ITEM_SLUG_INDEX];
		$url = $this->getMenuUrl($itemSlug, $parentSlug);
		$isBlacklisted = empty($url) || isset(self::$blacklist[$url]);

		//On first run, just collect all items and flag them as seen.
		if ( $this->isFirstRun ) {
			if ( !$isBlacklisted ) {
				$this->seenMenuUrls[$url] = true;
			}
			return $item;
		}

		if ( ($this->isNewMenu($url) && !$isBlacklisted) || $this->hasNewSubmenus($itemSlug) ) {
			$item[self::ITEM_TITLE_INDEX] .= sprintf(
				'<span class="ws-nmh-new-menu-flag" data-nmh-menu-url="%s"></span>',
				esc_attr($url)
			);

			if ( ($parentSlug === null) && isset($item[self::ITEM_CLASS_INDEX]) ) {
				$item[self::ITEM_CLASS_INDEX] .= ' ws-nmh-is-new-menu';
			}

			if ( $parentSlug !== null ) {
				$this->menusWithNewSubmenus[$parentSlug] = true;
			}
		}

		return $item;
	}

	private function getMenuUrl($itemSlug, $parentSlug) {
		if ( class_exists('ameMenuItem') ) {
			return ameMenuItem::generate_url($itemSlug, $parentSlug);
		} else {
			return $itemSlug;
		}
	}

	private function isIgnoredItem($item) {
		if ( !isset($item[self::ITEM_SLUG_INDEX]) ) {
			return true; //That's either an invalid item or an improvised separator.
		}

		//Skip separators and unnamed menus.
		$isSeparator = isset($item[self::ITEM_CLASS_INDEX])
			&& (strpos($item[self::ITEM_CLASS_INDEX], 'wp-menu-separator') !== false);

		if ( $isSeparator || empty($item[self::ITEM_SLUG_INDEX]) || ($item[self::ITEM_TITLE_INDEX] === '') ) {
			return true;
		}

		//Skip customizer links. They have a different URL on every admin page, so they'd always show up as new.
		if ( strpos($item[self::ITEM_SLUG_INDEX], 'customize.php') === 0 ) {
			return true;
		}

		return false;
	}

	private function isNewMenu($url) {
		return empty($this->seenMenuUrls[$url]);
	}

	private function hasNewSubmenus($slug) {
		return !empty($this->menusWithNewSubmenus[$slug]);
	}

	public function enqueueDependencies() {
		$dependencies = array('jquery');

		if ( isset($GLOBALS['wp_menu_editor']) && is_callable(array(
				$GLOBALS['wp_menu_editor'],
				'register_jquery_plugins',
			))
		) {
			$GLOBALS['wp_menu_editor']->register_jquery_plugins();
			$dependencies[] = 'ame-jquery-cookie';
		}

		wp_enqueue_script(
			'ws-nmh-admin-script',
			plugins_url('assets/highlight-menus.js', __FILE__),
			$dependencies,
			'20250629-2'
		);

		wp_localize_script(
			'ws-nmh-admin-script',
			'wsNmhData',
			array(
				'flagAction' => self::AJAX_FLAG_ACTION,
				'flagNonce'  => wp_create_nonce(self::AJAX_FLAG_ACTION),
			)
		);

		wp_enqueue_style(
			'ws-nmh-admin-style',
			plugins_url('assets/menu-highlights.css', __FILE__),
			array(),
			'20170503'
		);
	}

	public function ajaxFlagAsSeen() {
		check_ajax_referer(self::AJAX_FLAG_ACTION);
		if ( empty($_POST['urls']) ) {
			if ( function_exists('status_header') ) {
				status_header(400);
			}
			exit('Error: The required "urls" parameter is missing.');
		}

		//JSON decoding will reject severely malformed data, and flagAsSeen() will
		//further validate the input.
		//phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$json = strval($_POST['urls']);
		//Unfortunately, WP applies magic quotes to POST data.
		if ( function_exists('wp_magic_quotes') && did_action('plugins_loaded') ) {
			$json = stripslashes($json);
		}

		if ( $this->flagAsSeen(json_decode($json)) ) {
			exit('Success');
		} else {
			exit('Failure');
		}
	}

	public function flagUrlsFromCookie() {
		if ( !is_user_logged_in() || empty($_COOKIE[self::COOKIE_NAME]) || defined('DOING_AJAX') ) {
			return;
		}

		//As above, flagAsSeen() does some validation.
		//phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$urls = json_decode(stripslashes($_COOKIE[self::COOKIE_NAME]), true);
		if ( is_array($urls) ) {
			$this->flagAsSeen(array_keys($urls));
		}

		if ( version_compare(phpversion(), '7.3', '>=') ) {
			setcookie(self::COOKIE_NAME, '', array('expires' => time() - (24 * 3600), 'samesite' => 'Lax'));
		} else {
			setcookie(self::COOKIE_NAME, '', time() - (24 * 3600), '', '', is_ssl());
		}
	}

	private function flagAsSeen($menuUrls, $isInternalCall = false) {
		if ( empty($menuUrls) || !is_array($menuUrls) ) {
			return false;
		}
		//Reduce the risk of DoS attacks by limiting the number of URLs per request.
		if ( !$isInternalCall && (count($menuUrls) > self::MAX_URLS_PER_REQUEST) ) {
			return false;
		}

		$menuUrls = array_filter($menuUrls, array($this, 'couldBeMenuUrl'));
		$this->seenMenuUrls = $this->loadSeenMenus();

		//Optimization: Save only if there are changes / new URLs.
		$urlIndex = array_fill_keys($menuUrls, true);
		$newUrls = array_diff_key($urlIndex, $this->seenMenuUrls);
		if ( !empty($newUrls) ) {
			$this->seenMenuUrls = array_merge($this->seenMenuUrls, $urlIndex);

			//To avoid creating huge user meta rows, let's save only the most recent URLs.
			if ( count($this->seenMenuUrls) > self::MAX_URLS_PER_USER ) {
				$this->seenMenuUrls = array_slice(
					$this->seenMenuUrls,
					-self::MAX_URLS_PER_USER,
					self::MAX_URLS_PER_USER,
					true
				);
			}

			return update_user_meta(get_current_user_id(), self::STORAGE_KEY, $this->seenMenuUrls);
		} else {
			return false;
		}
	}

	private function couldBeMenuUrl($input) {
		if ( !is_string($input) || ($input === '') ) {
			return false;
		}

		$input = wp_check_invalid_utf8($input);
		if ( !$input ) {
			return false;
		}

		//Admin menu URLs are not necessarily fully qualified, or even valid
		//URLs. Some plugins use weird hacks. Let's just verify that the "URL"
		//is not too huge.
		return (strlen($input) < self::MAX_URL_LENGTH);
	}
}