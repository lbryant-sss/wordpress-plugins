/* global wsNmhData */

jQuery(function($) {
	'use strict';

	var $adminMenu = $('#adminmenu'),
		seenOnThisPage = {},
		foundNewMenus;

	/**
	 * Flag a menu URL as seen (i.e. no longer new).
	 *
	 * This function is both debounced and throttled. As long as you continue to call it,
	 * the AJAX request won't be triggered. It will be sent only once the function stops
	 * being called for N milliseconds. Additionally, it will wait at least N milliseconds
	 * between requests.
	 */
	var flagAsSeen = (function() {
		var queue = [],
			timeout = null,
			waitMs = 500,
			isRequestInProgress = false;

		function sendQueuedUrls() {

			if (queue.length > 0) {
				if (isRequestInProgress) {
					//console.warn('A request is already in progress');
				}
				isRequestInProgress = true;

				$.ajax(
					ajaxurl,
					{
						'method': 'POST',
						'data': {
							'action': wsNmhData['flagAction'],
							'_ajax_nonce': wsNmhData['flagNonce'],
							'urls' : JSON.stringify(queue)
						},
						'complete': function() {
							isRequestInProgress = false;
							if (queue.length > 0) {
								timeout = setTimeout(sendQueuedUrls, waitMs);
							}
						}
					}
				);
			}

			queue = [];
			timeout = null;
		}

		/**
		 * @param string menuUrl
		 */
		return function(menuUrl) {
			if ((menuUrl === '') || seenOnThisPage.hasOwnProperty(menuUrl)) {
				return;
			}
			seenOnThisPage[menuUrl] = true;

			//In case the AJAX request doesn't succeed for whatever reason, lets *also*
			//set a session cookie that contains the seen URLs.
			if ($.cookie) {
				$.cookie('ws_nmh_pending_seen_urls', JSON.stringify(seenOnThisPage));
			}

			queue.push(menuUrl);

			if (isRequestInProgress) {
				return;
			}

			if (timeout) {
				//Move the timeout forward.
				clearTimeout(timeout);
			}
			timeout = setTimeout(sendQueuedUrls, waitMs);
		}
	})();

	function maybeFlagItem($item) {
		var shouldFlagThis = true,
			shouldFlagParent = false,
			$link, $flag, $parent;

		if ($item.hasClass('menu-top')) {
			//Only flag top level menus when they no longer have any new submenus.
			shouldFlagThis = $item
					.find('> ul li.ws-nmh-is-new-menu')
					.not('.wp-submenu-head')
					.length === 0;
		} else {
			//If all of the submenus on this level are seen/not new, flag the parent menu as well.
			shouldFlagParent = $item.siblings('li.ws-nmh-is-new-menu').not('.wp-submenu-head').length === 0;
		}

		if (!shouldFlagThis) {
			return;
		}

		$link = $item.children('a').first();
		$flag = $link.find('.ws-nmh-new-menu-flag').add($item.children('.ws-nmh-new-menu-flag')).first();

		$item.removeClass('ws-nmh-is-new-menu');
		$link.removeClass('ws-nmh-is-new-menu');

		if ($flag.length > 0) {
			flagAsSeen($flag.data('nmh-menu-url'));
			$flag.remove();
		}

		if (shouldFlagParent) {
			$parent = $item.parentsUntil($adminMenu, 'li');
			if (($parent.length > 0) && !$parent.is($item)) {
				maybeFlagItem($parent);
			}
		}
	}

	var $newItems = $adminMenu.find('.ws-nmh-new-menu-flag').closest('li').addClass('ws-nmh-is-new-menu');
	foundNewMenus = $newItems.length > 0;

	//Flag the current item as seen.
	$newItems.filter('.current, .wp-has-current-submenu').each(function() {
		maybeFlagItem($(this));
	});

	//Flag any hidden items as seen. The user can't actually click hidden items,
	//so the parent menu would permanently stay highlighted if we didn't do this.
	if (foundNewMenus) {
		//We want to run after all other $(document).ready callbacks because some
		//of them might hide admin menu items by calling $('selector').hide().
		setTimeout(function() {
			$newItems.filter(':hidden').not('.wp-submenu-head').each(function() {
				maybeFlagItem($(this));
			});
		}, 60);
	}

	if (foundNewMenus) {
		$adminMenu.on('mouseenter click focusin', 'li.ws-nmh-is-new-menu', function() {
			maybeFlagItem($(this).closest('li'));
		});
	}
});
