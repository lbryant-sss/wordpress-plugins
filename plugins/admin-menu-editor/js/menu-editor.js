//(c) W-Shadow

/*global wsEditorData, defaultMenu, customMenu, _:false */

/**
 * @property wsEditorData
 * @property {boolean} wsEditorData.wsMenuEditorPro
 *
 * @property {object} wsEditorData.blankMenuItem
 * @property {object} wsEditorData.itemTemplates
 * @property {object} wsEditorData.customItemTemplate
 *
 * @property {string} wsEditorData.adminAjaxUrl
 * @property {string} wsEditorData.imagesUrl
 *
 * @property {string} wsEditorData.menuFormatName
 * @property {string} wsEditorData.menuFormatVersion
 *
 * @property {boolean} wsEditorData.hideAdvancedSettings
 * @property {boolean} wsEditorData.showExtraIcons
 * @property {boolean} wsEditorData.dashiconsAvailable
 * @property {string}  wsEditorData.submenuIconsEnabled
 *
 * @property {Object} wsEditorData.showHints
 * @property {string} wsEditorData.hideHintNonce
 *
 * @property {string} wsEditorData.hideAdvancedSettingsNonce
 * @property {string} wsEditorData.getPagesNonce
 * @property {string} wsEditorData.getPageDetailsNonce
 * @property {string} wsEditorData.disableDashboardConfirmationNonce
 *
 * @property {string} wsEditorData.captionShowAdvanced
 * @property {string} wsEditorData.captionHideAdvanced
 *
 * @property {string} wsEditorData.unclickableTemplateId
 * @property {string} wsEditorData.unclickableTemplateClass
 * @property {string} wsEditorData.embeddedPageTemplateId
 *
 * @property {string} wsEditorData.currentUserLogin
 * @property {string|null} wsEditorData.selectedActor
 *
 * @property {object} wsEditorData.actors
 * @property {string[]} wsEditorData.visibleUsers
 *
 * @property {object} wsEditorData.postTypes
 * @property {object} wsEditorData.taxonomies
 *
 * @property {string|null} wsEditorData.selectedMenu
 * @property {string|null} wsEditorData.selectedSubmenu
 *
 * @property {string} wsEditorData.setTestConfigurationNonce
 * @property {string} wsEditorData.testAccessNonce
 *
 * @property {string|null} wsEditorData.deepNestingEnabled
 *
 * @property {object} wsEditorData.auxDataConfig
 *
 * @property {boolean} wsEditorData.isDemoMode
 * @property {boolean} wsEditorData.isMasterMode
 */

wsEditorData.wsMenuEditorPro = !!wsEditorData.wsMenuEditorPro; //Cast to boolean.
var wsIdCounter = 0;

//A bit of black magic/hack to convince my IDE that wsAmeLodash is an alias for lodash.
window.wsAmeLodash = (function() {
	'use strict';
	if (typeof wsAmeLodash !== 'undefined') {
		return wsAmeLodash;
	}
	return _.noConflict();
})();

//These two properties must be objects, not arrays.
jQuery.each(['grant_access', 'hidden_from_actor'], function(unused, key) {
	'use strict';
	if (wsEditorData.blankMenuItem.hasOwnProperty(key) && !jQuery.isPlainObject(wsEditorData.blankMenuItem[key])) {
		wsEditorData.blankMenuItem[key] = {};
	}
});

AmeCapabilityManager = AmeActors;

/**
 * A utility for retrieving post and page titles.
 */
var AmePageTitles = (function($) {
	'use strict';

	var me = {}, cache = {};

	function getCacheKey(pageId, blogId) {
		return blogId + '_' + pageId;
	}

	/**
	 * Add a page title to the cache.
	 *
	 * @param {Number} pageId Post or page ID.
	 * @param {Number} blogId Blog ID.
	 * @param {String} title The title of the post or page.
	 */
	me.add = function(pageId, blogId, title) {
		cache[getCacheKey(pageId, blogId)] = title;
	};

	/**
	 * Get page title.
	 *
	 * Note: This method does not return the title. Instead, it calls the provided callback with the title
	 * as the first argument. The callback will be executed asynchronously if the title hasn't been cached yet.
	 *
	 * @param {Number} pageId
	 * @param {Number} blogId
	 * @param {Function} callback
	 */
	me.get = function(pageId, blogId, callback) {
		var key = getCacheKey(pageId, blogId);
		if (typeof cache[key] !== 'undefined') {
			callback(cache[key], pageId, blogId);
			return;
		}

		$.getJSON(
			wsEditorData.adminAjaxUrl,
			{
				'action' : 'ws_ame_get_page_details',
				'_ajax_nonce' : wsEditorData.getPageDetailsNonce,
				'post_id' : pageId,
				'blog_id' : blogId
			},
			function(details) {
				var title;
				if (typeof details.error !== 'undefined'){
					title = details.error;
				} else if ((typeof details !== 'object') || (typeof details.post_title === 'undefined')) {
					title = '< Server error >';
				} else {
					title = details.post_title;
				}
				cache[key] = title;

				callback(cache[key], pageId, blogId);
			}
		);
	};

	return me;
})(jQuery);

var AmeEditorApi = {};
window.AmeEditorApi = AmeEditorApi;


(function ($, _){
'use strict';

var actorSelectorWidget = new AmeActorSelector(AmeActors, wsEditorData.wsMenuEditorPro);

AmeEditorApi.actorSelectorWidget = actorSelectorWidget;

var itemTemplates = {
	templates: wsEditorData.itemTemplates,

	getTemplateById: function(templateId) {
		if (wsEditorData.itemTemplates.hasOwnProperty(templateId)) {
			return wsEditorData.itemTemplates[templateId];
		} else if ((templateId === '') || (templateId === 'custom')) {
			return wsEditorData.customItemTemplate;
		}
		return null;
	},

	getDefaults: function (templateId) {
		var template = this.getTemplateById(templateId);
		if (template) {
			return template.defaults;
		} else {
			return null;
		}
	},

	getDefaultValue: function (templateId, fieldName) {
		if (fieldName === 'template_id') {
			return null;
		}

		var defaults = this.getDefaults(templateId);
		if (defaults && (typeof defaults[fieldName] !== 'undefined')) {
			return defaults[fieldName];
		}
		return null;
	},

	hasDefaultValue: function(templateId, fieldName) {
		return (this.getDefaultValue(templateId, fieldName) !== null);
	}
};

	/**
	 * @type {AmeMenuPresenter}
	 */
	let menuPresenter;

/**
 * Set an input field to a value. The only difference from jQuery.val() is that
 * setting a checkbox to true/false will check/clear it.
 *
 * @param input
 * @param value
 */
function setInputValue(input, value) {
	if (input.attr('type') === 'checkbox'){
		input.prop('checked', value);
    } else {
        input.val(value);
    }
}

/**
 * Get the value of an input field. The only difference from jQuery.val() is that
 * checked/unchecked checkboxes will return true/false.
 *
 * @param input
 * @return {*}
 */
function getInputValue(input) {
	if (input.attr('type') === 'checkbox'){
		return input.is(':checked');
	}
	return input.val();
}


/*
 * Utility function for generating pseudo-random alphanumeric menu IDs.
 * Rationale: Simpler than atomically auto-incrementing or globally unique IDs.
 */
function randomMenuId(prefix, size){
	prefix = (typeof prefix === 'undefined') ? 'custom_item_' : prefix;
	size = (typeof size === 'undefined') ? 5 : size;

    var suffix = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < size; i++ ) {
        suffix += possible.charAt(Math.floor(Math.random() * possible.length));
    }

    return prefix + suffix;
}
AmeEditorApi.randomMenuId = randomMenuId;

function outputWpMenu(menu){
	const menuCopy = $.extend(true, {}, menu);

	//Remove the current menu data
	menuPresenter.clear();

	//Display the new menu
	const firstColumn = menuPresenter.getColumnImmediate(1);
	const itemList = firstColumn.getVisibleItemList();
	for (let filename in menuCopy){
		if (!menuCopy.hasOwnProperty(filename)){
			continue;
		}
		firstColumn.outputItem(menuCopy[filename], null, itemList);
	}

	//Automatically select the first top-level menu
	if (itemList) {
		itemList.find('.ws_menu:first').trigger('click');
	}
}

/**
 * Load a menu configuration in the editor.
 * Note: All previous settings will be discarded without warning. Unsaved changes will be lost.
 *
 * @param {Object} adminMenu The menu structure to load.
 */
function loadMenuConfiguration(adminMenu) {
	//There are some menu properties that need to be objects, but PHP JSON-encodes empty associative
	//arrays as numeric arrays. We want them to be empty objects instead.
	if (adminMenu.hasOwnProperty('color_presets') && !$.isPlainObject(adminMenu.color_presets)) {
		adminMenu.color_presets = {};
	}

	var objectProperties = ['grant_access', 'hidden_from_actor'];
	//noinspection JSUnusedLocalSymbols
	function fixEmptyObjects(unused, menuItem) {
		for (var i = 0; i < objectProperties.length; i++) {
			var key = objectProperties[i];
			if (menuItem.hasOwnProperty(key) && !$.isPlainObject(menuItem[key])) {
				menuItem[key] = {};
			}
		}
		if (menuItem.hasOwnProperty('items')) {
			$.each(menuItem.items, fixEmptyObjects);
		}
	}
	$.each(adminMenu.tree, fixEmptyObjects);

	//Load color presets from the new configuration.
	if (typeof adminMenu.color_presets === 'object') {
		colorPresets = $.extend(true, {}, adminMenu.color_presets);
	} else {
		colorPresets = {};
	}
	wasPresetDropdownPopulated = false;

	//Load capabilities.
	AmeCapabilityManager.setGrantedCapabilities(_.get(adminMenu, 'granted_capabilities', {}));

	//Load general menu visibility.
	generalComponentVisibility = _.get(adminMenu, 'component_visibility', {});
	AmeEditorApi.refreshComponentVisibility();

	//Display the new admin menu.
	outputWpMenu(adminMenu.tree);

	$(document).trigger('menuConfigurationLoaded.adminMenuEditor', adminMenu);
}

	/**
	 * Check if it's possible to delete a menu item.
	 *
	 * @param {JQuery} containerNode
	 * @returns {boolean}
	 */
	function canDeleteItem(containerNode) {
		if (!containerNode || (containerNode.length < 1)) {
			return false;
		}

		var menuItem = containerNode.data('menu_item');
		var isDefaultItem =
			( menuItem.template_id !== '')
			&& ( menuItem.template_id !== wsEditorData.unclickableTemplateId)
			&& ( menuItem.template_id !== wsEditorData.embeddedPageTemplateId)
			&& (!menuItem.separator);

		var otherCopiesExist = false;
		if (isDefaultItem) {
			//Check if there are any other menus with the same template ID.
			$('#ws_menu_editor').find('.ws_container').each(function() {
				var otherItem = $(this).data('menu_item');
				if ((menuItem !== otherItem) && (menuItem.template_id === otherItem.template_id)) {
					otherCopiesExist = true;
					return false;
				}
				return true;
			});
		}

		return (!isDefaultItem || otherCopiesExist);
	}

	/**
	 * Get or create the submenu container of a menu item.
	 *
	 * @param {JQuery|null} container
	 * @param {AmeEditorColumn} [nextColumn]
	 * @return {JQuery|null}
	 */
	function getSubmenuOf(container, nextColumn) {
		if (!container || (container.length < 1)) {
			return null;
		}

		const submenuId = container.data('submenu_id');
		if (submenuId) {
			let $submenu = $('#' + submenuId).first();
			if ($submenu.length > 0) {
				return $submenu;
			}
		}

		//If a submenu doesn't exist yet, create it in the next column.
		if (nextColumn) {
			return createSubmenuFor(container, nextColumn);
		} else {
			return null;
		}
	}

	/**
	 * Create a submenu container for a menu item.
	 * @param {JQuery} container
	 * @param {AmeEditorColumn} nextColumn
	 * @return {JQuery}
	 */
	function createSubmenuFor(container, nextColumn) {
		const $submenu = nextColumn.buildSubmenuContainer(container.attr('id'));
		nextColumn.appendSubmenuContainer($submenu);
		container.data('submenu_id', $submenu.attr('id'))
		return $submenu;
	}

	/**
	 * @param {Number} level
	 * @param {JQuery|null} predecessor
	 * @param {JQuery|null} [container]
	 * @param {Function} [getNextColumn]
	 * @constructor
	 */
	function AmeEditorColumn(level, predecessor, container, getNextColumn) {
		const self = this;

		this.level = level;
		this.usesSubmenuContainers = (this.level > 1);

		if ((typeof container === 'undefined') || (container === null)) {
			container = $('#ame-submenu-column-template').first().clone();
			container.attr('id', '');
			container.find('.ws_box').first().attr('id', '');
			container.show().insertAfter(predecessor);
		}
		container.data('ame-menu-level', level);
		container.addClass('ame-editor-column-' + level);

		this.container = container;
		this.menuBox = container.find('.ws_box').first();
		this.dropZone = container.children('.ws_dropzone').first();
		this.visibleItemList = null;

		if (!this.usesSubmenuContainers) {
			this.menuBox.addClass('ame-visible-item-list');
		}

		if (typeof getNextColumn !== 'undefined') {
			this.getNextColumn = getNextColumn;
		} else {
			this.getNextColumn = function(callback) {
				callback(null);
			};
		}

		this.container.children('.ws_toolbar').on('click', '.ws_button', function() {
			const $button = $(this);
			let buttonAction = $button.data('ame-button-action') || 'unknown';
			let selectedItem = self.getSelectedItem();
			self.container.trigger(
				'adminMenuEditor:action-' + buttonAction,
				[(selectedItem.length > 0) ? selectedItem : null, self, $button]
			);
			return false;
		});
	}

	/**
	 * Create editor widgets for a menu item and its submenus.
	 *
	 * @param {Object} itemData An object containing menu data.
	 * @param {JQuery|null|number} [insertPosition] Insert the widget after this node. If it's NULL, the widget
	 *  will be added to the end fo the list. If it's -1, the widget will be added to the beginning.
	 * @param {JQuery} [itemList] The container where to insert the widget. Defaults to the currently
	 *  visible item list. For columns that don't use submenu containers, it's always the menuBox.
	 * @return {Object} Object with two fields - 'menu' and 'submenu' - containing the jQuery objects
	 *  of the created widgets.
	 */
	AmeEditorColumn.prototype.outputItem = function(itemData, insertPosition, itemList) {
		if (!itemList) {
			itemList = this.getVisibleItemList();
		}
		const self = this;

		//Create the menu widget
		const isTopLevel = this.level <= 1;
		const $item = buildMenuItem(itemData, isTopLevel);

		if (typeof insertPosition === 'undefined') {
			insertPosition = null;
		}
		if (insertPosition === null) {
			$item.appendTo(itemList);
		} else if (insertPosition === -1) {
			$item.prependTo(itemList);
		} else {
			//phpcs:ignore WordPressVIPMinimum.JS.HTMLExecutingFunctions -- buildMenuItem() should be safe.
			$(insertPosition).after($item);
		}

		const children = (typeof itemData.items !== 'undefined') ? itemData.items : [];
		const hasChildren = !_.isEmpty(children);
		let $submenu = null;

		this.getNextColumn(
			/**
			 * @param {AmeEditorColumn|null} nextColumn
			 */
			function (nextColumn) {
				if (nextColumn) {
					//Create a submenu container even if this item doesn't have children.
					//The user could add submenu items later.
					$submenu = createSubmenuFor($item, nextColumn);

					//Output children.
					if (hasChildren) {
						$.each(children, function (index, item) {
							nextColumn.outputItem(item, null, $submenu);
						});
					}
				} else {
					//TODO: This branch could be optimized by letting the recursive outputItem call know that there is no next column.
					//There is no next column, so any submenu items that belong to this item will be
					//displayed in the same column, below the item.
					if (hasChildren) {
						let $previousItem = $item;
						$.each(children, function (index, child) {
							const result = self.outputItem(child, $previousItem, itemList);
							if (result && result.menu) {
								$previousItem = result.menu;
							}
						});
					}
				}

				//Note: Update the menu only after its children are ready. It needs the submenu items to decide
				//whether to display the access checkbox as checked or indeterminate.
				updateItemEditor($item);
			},
			hasChildren
		);

		//Note that $submenu could still be NULL at this point if the "get next column" callback
		//is called asynchronously.
		return {
			'menu': $item,
			'submenu': $submenu
		};
	};

	/**
	 * Paste a menu item in this column.
	 *
	 * @param {Object} item
	 * @param {JQuery|null|number} [insertPosition] Defaults to inserting the item below the current selection.
	 *  Set to NULL to paste at the end of the list, or -1 to paste at the beginning.
	 * @param {JQuery} [itemList]
	 */
	AmeEditorColumn.prototype.pasteItem = function (item, insertPosition, itemList) {
		if (typeof insertPosition === 'undefined') {
			insertPosition = this.getSelectedItem();
			if (insertPosition.length < 1) {
				insertPosition = null;
			}
		}

		if (!itemList) {
			itemList = this.getVisibleItemList();
		}

		//The user shouldn't need to worry about giving separators a unique filename.
		if (item.separator) {
			item.defaults.file = randomMenuId('separator_');
		}

		//If we're pasting from a sub-menu into the top level, we may need to fix some properties
		//that are blank for sub-menu items but required for top level menus.
		const isTopLevel = this.level <= 1;
		if (isTopLevel) {
			function isNonEmptyString(value) {
				return (typeof value === 'string') && (value !== '');
			}

			if (!isNonEmptyString(getFieldValue(item, 'css_class', ''))) {
				item.css_class = 'menu-top';
			}
			if (!isNonEmptyString(getFieldValue(item, 'icon_url', ''))) {
				item.icon_url = 'dashicons-admin-generic';
			}
			if (!isNonEmptyString(getFieldValue(item, 'hookname', ''))) {
				item.hookname = randomMenuId();
			}
		}

		const result = this.outputItem(item, insertPosition, itemList);

		if (this.level > 1) {
			updateParentAccessUi(itemList);
		}

		return result;
	};

	/**
	 * @return {JQuery|null}
	 */
	AmeEditorColumn.prototype.getVisibleItemList = function() {
		if (this.usesSubmenuContainers) {
			if (this.visibleItemList) {
				return this.visibleItemList;
			}

			const $list = this.menuBox.children('.ws_submenu:visible').first().addClass('ame-visible-item-list');
			if ($list && ($list.length > 0)) {
				this.visibleItemList = $list;
			}
			return $list;
		} else {
			return this.menuBox;
		}
	};

	/**
	 * @param {JQuery|null} $submenu
	 */
	AmeEditorColumn.prototype.setVisibleItemList = function($submenu) {
		//Do nothing if the new list is the same as the old one.
		if (($submenu === this.visibleItemList) || ($submenu && ($submenu.is(this.visibleItemList)))) {
			return;
		}

		if (this.visibleItemList) {
			this.visibleItemList.hide().removeClass('ame-visible-item-list');
		}
		this.visibleItemList = $submenu;

		if (this.visibleItemList) {
			this.visibleItemList.show().addClass('ame-visible-item-list');
		}

		//Each item list/submenu has its own own selected item, so switching to a different item list
		//also effectively changes the selected item.
		this.selectionHasChanged();
	};

	/**
	 * @return {JQuery}
	 */
	AmeEditorColumn.prototype.getAllItemLists = function() {
		if (this.usesSubmenuContainers) {
			return this.menuBox.children('.ws_submenu');
		}
		return this.menuBox;
	};

	/**
	 * @return {JQuery}
	 */
	AmeEditorColumn.prototype.getSelectedItem = function() {
		const list = this.getVisibleItemList();
		if (list && (list.length > 0)) {
			return list.children('.ws_active').first();
		}
		return $([]);
	};

	/**
	 * @param {JQuery} container
	 */
	AmeEditorColumn.prototype.selectItem = function(container) {
		if (container.hasClass('ws_active')) {
			//The menu item is already selected.
			return;
		}

		//Highlight the active item and un-highlight the previous one
		container.addClass('ws_active');
		container.siblings('.ws_active').removeClass('ws_active');

		this.selectionHasChanged(container);
	};

	/**
	 * @param {JQuery|null} [$item]
	 */
	AmeEditorColumn.prototype.selectionHasChanged = function($item) {
		if (typeof $item === 'undefined') {
			$item = this.getSelectedItem();
		}
		if (!$item || ($item.length < 1)) {
			$item = null;
		}

		//Make the "delete" button appear disabled if you can't delete this item.
		this.container.find('.ws_toolbar .ws_delete_menu_button')
			.toggleClass('ws_button_disabled', !canDeleteItem($item))

		const self = this;
		this.getNextColumn(function(nextColumn) {
			if (nextColumn) {
				nextColumn.setVisibleItemList(getSubmenuOf($item, nextColumn));
				if ($item) {
					self.updateSubmenuBoxHeight($item, nextColumn);
				}
			}
		}, false);
	};

	/**
	 * @param {JQuery} selectedMenu
	 * @param {AmeEditorColumn} nextColumn
	 */
	AmeEditorColumn.prototype.updateSubmenuBoxHeight = function updateSubmenuBoxHeight(selectedMenu, nextColumn) {
		if (!nextColumn || (nextColumn === this)) {
			return;
		}
		let mainMenuBox = this.menuBox,
			submenuBox = nextColumn.menuBox,
			submenuDropZone = nextColumn.dropZone;

		//Make the submenu box tall enough to reach the selected item.
		//This prevents the menu tip (if any) from floating in empty space.
		if (selectedMenu.hasClass('ws_menu_separator')) {
			submenuBox.css('min-height', '');
		} else {
			var menuTipHeight = 30,
				empiricalExtraHeight = 4,
				verticalBoxOffset = (submenuBox.offset().top - mainMenuBox.offset().top),
				minSubmenuHeight = (selectedMenu.offset().top - mainMenuBox.offset().top)
					- verticalBoxOffset
					+ menuTipHeight - (submenuDropZone.outerHeight() || 0) + empiricalExtraHeight;
			minSubmenuHeight = Math.max(minSubmenuHeight, 0);
			submenuBox.css('min-height', minSubmenuHeight);
		}
	}

	AmeEditorColumn.prototype.buildSubmenuContainer = function(parentMenuId) {
		//Create a container for menu items.
		const submenu = $('<div class="ws_submenu" style="display:none;"></div>');
		submenu.attr('id', 'ws-submenu-'+(wsIdCounter++));

		if (parentMenuId) {
			submenu.data('parent_menu_id', parentMenuId);
		}

		//Make the submenu sortable
		makeBoxSortable(submenu);

		return submenu;
	};

	AmeEditorColumn.prototype.appendSubmenuContainer = function($submenu) {
		this.usesSubmenuContainers = true;
		$submenu.appendTo(this.menuBox);
	};

	/**
	 * Delete a menu item and all of its children.
	 *
	 * @param {JQuery} container
	 */
	AmeEditorColumn.prototype.destroyItem = function(container) {
		const wasSelected = container.is('.ws_active');

		//Recursively destroy any submenu items.
		const submenuId = container.data('submenu_id');
		if (submenuId) {
			const self = this;
			const $submenu = $('#' + submenuId);
			$submenu.children('.ws_container').each(function() {
				self.destroyItem($(this));
			});
			$submenu.remove();
		}

		//Destroy the item itself.
		container.remove();

		if (wasSelected) {
			this.selectionHasChanged();
		}
	};

	/**
	 * Check if this column can accept a menu item that's being dragged/moved to it.
	 *
	 * @param {JQuery} $itemNode
	 * @returns {boolean}
	 */
	AmeEditorColumn.prototype.canAcceptItem = function($itemNode) {
		const visibleSubmenu = this.getVisibleItemList();
		if (!visibleSubmenu || (visibleSubmenu.length < 1)) {
			return false; //Can't move anything to a non-existent submenu.
		}

		return (
			//It must actually be a menu item.
			$itemNode.hasClass('ws_container')

			//Prevent users from dropping a parent menu on one of its own sub-menus.
			&& !isParentMenuNodeOf($itemNode, visibleSubmenu)
		);
	}

	/**
	 * Remove all items and item lists from this column.
	 *
	 * Note: Does not remove item submenus that are in other columns.
	 */
	AmeEditorColumn.prototype.reset = function() {
		this.menuBox.empty();
		this.visibleItemList = null;
		this.selectionHasChanged(null);
	};

	/**
	 *
	 * @param {JQuery} editorNode
	 * @param {Boolean|null|string} [deepNestingEnabled]
	 * @param {Number} [maxLevels]
	 * @param {Number} [initialLevels]
	 * @constructor
	 */
	function AmeMenuPresenter(editorNode, deepNestingEnabled, maxLevels, initialLevels ) {
		const self = this;
		this.editorNode = editorNode;

		if (typeof deepNestingEnabled === 'string') {
			deepNestingEnabled = (deepNestingEnabled === '1');
		}
		this.isDeepNestingEnabled = (typeof deepNestingEnabled !== 'undefined') ? deepNestingEnabled : null;
		this.nestingQueryPromise = null;

		if (typeof maxLevels === 'undefined') {
			maxLevels = 3;
		}
		if (typeof initialLevels === 'undefined') {
			if (this.isDeepNestingEnabled) {
				//If additional levels are enabled, show the maximum number of levels.
				initialLevels = maxLevels;
			} else {
				//WordPress only supports up to two levels by default.
				initialLevels = Math.min(maxLevels, 2);
			}
		}
		if (initialLevels > this.maxLevels) {
			initialLevels = this.maxLevels;
		}

		this.maxLevels = maxLevels;

		const $topLevelContainer = this.editorNode.find('#ws_menu_box').first().closest('.ws_main_container');
		this.columns = [
			//Empty zeroth column.
			new AmeEditorColumn(0, null, $()),
			//The first column contains top level menus.
			new AmeEditorColumn(1, null, $topLevelContainer, makeNextColumnGetter(1))
		];
		this.currentLevels = this.columns.length - 1;

		function makeNextColumnGetter(ownLevel) {
			if (ownLevel >= self.maxLevels) {
				//This column will never have a next column, so we can just use NULL.
				return function(callback) {
					callback(null);
				};
			}
			return function(callback, createIfNotExists) {
				self.getColumn(ownLevel + 1, callback, createIfNotExists);
			};
		}

		/**
		 * @param {Number} level
		 * @return {AmeEditorColumn}
		 */
		function createColumn(level) {
			if (level > self.maxLevels) {
				throw new Error('Cannot exceed maximum nesting level: ' + self.maxLevels);
			}
			if (typeof self.columns[level] !== 'undefined') {
				throw new Error('Cannot overwrite an existing column ' + level);
			}

			let predecessor;
			if (typeof self.columns[level - 1] !== 'undefined') {
				predecessor = self.columns[level - 1].container;
			} else {
				predecessor = self.columns[self.currentLevels].container;
			}

			let newColumn = new AmeEditorColumn(level, predecessor, null, makeNextColumnGetter(level));
			self.columns.push(newColumn);

			if (level > self.currentLevels) {
				self.currentLevels = level;
			}

			return newColumn;
		}

		/**
		 * Can we create another column?
		 *
		 * @param {Number} level
		 * @param {Function} callback
		 */
		function queryCanCreateColumn(level, callback) {
			if (
				(level > self.maxLevels)                            //Do not exceed the maximum depth.
				|| (typeof self.columns[level] !== 'undefined')     //Do not overwrite existing columns.
			) {
				callback(false);
				return;
			}

			//WordPress core only supports two admin menu levels. We call anything beyond that "deep".
			const isDeep = (level > 2);
			if (!isDeep) {
				callback(true);
				return;
			}
			//Do we already know if we can create deeply nested menus?
			if (self.isDeepNestingEnabled !== null) {
				callback(self.isDeepNestingEnabled);
				return;
			}

			//If we're already waiting for a decision, just add another callback to the queue.
			if (self.nestingQueryPromise !== null) {
				self.nestingQueryPromise.always(function() {
					callback(self.isDeepNestingEnabled);
				});
				return;
			}

			//Let's allow other code/plugins to decide this. Scripts can add deferred objects or promises
			//to an array. All deferred objects must resolve successfully to enable deep nesting.
			let deferreds = [];
			self.editorNode.trigger('adminMenuEditor:queryDeepNesting', [deferreds]);

			if (deferreds.length > 0) {
				self.nestingQueryPromise = $.when.apply($, deferreds)
					.done(function() {
						self.isDeepNestingEnabled = true;
					})
					.fail(function() {
						self.isDeepNestingEnabled = false;
					})
					.always(function() {
						callback(self.isDeepNestingEnabled);
					});
			} else {
				//Deep nesting is disabled by default.
				self.isDeepNestingEnabled = false;
				callback(self.isDeepNestingEnabled);
			}
		}

		/**
		 * Get or create a column. The callback will be called with one argument: either the column object,
		 * or NULL if the column does not exist and could not be created.
		 *
		 * @param {Number} level
		 * @param {Function} callback
		 * @param {Boolean} [createIfNotExists] Defaults to true.
		 */
		this.getColumn = function(level, callback, createIfNotExists) {
			if (typeof this.columns[level] !== 'undefined') {
				callback(this.columns[level]);
				return;
			}

			if (typeof createIfNotExists === 'undefined') {
				createIfNotExists = true;
			}

			if (createIfNotExists) {
				queryCanCreateColumn(level, function (isAllowed) {
					//It could be that another callback has already created the next column,
					//so we need to check again if it exists.
					if (typeof self.columns[level] !== 'undefined') {
						callback(self.columns[level]);
					} else if (isAllowed) {
						callback(createColumn(level));
					} else {
						callback(null);
					}
				});
			} else {
				callback(null);
			}
		};

		/**
		 * Get or create a column. Like getColumn(), but it will default to not creating deeply nested
		 * menu levels unless that feature is already enabled.
		 *
		 * @param {Number} level
		 * @return {AmeEditorColumn|null}
		 */
		this.getColumnImmediate = function(level) {
			if (typeof this.columns[level] !== 'undefined') {
				return this.columns[level];
			}
			if (level > this.maxLevels) {
				return null;
			}

			if ((level <= 2) || (this.isDeepNestingEnabled === true)) {
				return createColumn(level);
			}
			return null;
		};

		/**
		 * Get the column that contains a specific menu item or element.
		 *
		 * @param  {JQuery} container Menu item container, or another element that's inside a column.
		 * @return {AmeEditorColumn|null}
		 */
		this.getItemColumn = function(container) {
			if (!container) {
				return null;
			}
			const level = container.closest('.ws_main_container').data('ame-menu-level');
			if (typeof level === 'undefined') {
				return null;
			}
			return this.getColumnImmediate(level);
		};

		/**
		 * Create editor widgets for a menu item and its submenus and append them all to the DOM.
		 *
		 * @param {Number} level
		 * @param {Object} itemData
		 * @param {JQuery} [afterNode] Insert the widget after this node.
		 */
		this.outputMenuItem = function(level, itemData, afterNode) {
			const column = this.getColumnImmediate(level);
			return column.outputItem(itemData, afterNode);
		}

		/**
		 * Select a menu item and show its submenu.
		 *
		 * @param {JQuery} container
		 */
		this.selectItem = function(container) {
			const thisColumn = this.getColumnImmediate(container.closest('.ws_main_container').data('ame-menu-level'));
			if (thisColumn) {
				thisColumn.selectItem(container);
			}
		};

		/**
		 * Delete a menu item and all of its children.
		 *
		 * @param {JQuery} container
		 */
		this.destroyItem = function(container) {
			const column = this.getItemColumn(container);
			if (column) {
				column.destroyItem(container);
			}
		};

		/**
		 * Delete all items and reset all columns.
		 */
		this.clear = function() {
			for (let level = 0; level < this.columns.length; level++) {
				if (typeof this.columns[level] !== 'undefined') {
					this.columns[level].reset();
				}
			}
		};

		//Initialisation.
		for (let level = this.currentLevels + 1; level <= initialLevels; level++) {
			createColumn(level);
		}
	}

/*
 * Create edit widgets for a top-level menu and its submenus and append them all to the DOM.
 *
 * Inputs :
 *	menu - an object containing menu data
 *	afterNode - if specified, the new menu widget will be inserted after this node. Otherwise,
 *	            it will be added to the end of the list.
 * Outputs :
 *	Object with two fields - 'menu' and 'submenu' - containing the DOM nodes of the created widgets.
 */
function outputTopMenu(menu, afterNode){
	if (!menuPresenter) {
		throw new Error('outputTopMenu cannot be called before the menu presenter has been initialised.');
	}
	return menuPresenter.outputMenuItem(1, menu, afterNode);
}

/**
 * Create an edit widget for a menu item.
 *
 * @param {Object} itemData
 * @param {Boolean} [isTopLevel] Specify if this is a top-level menu or a sub-menu item. Defaults to false (= sub-item).
 * @return {*} The created widget as a jQuery object.
 */
function buildMenuItem(itemData, isTopLevel) {
	isTopLevel = (typeof isTopLevel === 'undefined') ? false : isTopLevel;
	const canHaveSubmenuItems = isTopLevel && !itemData.separator;

	//Create the menu HTML
	var item = $('<div></div>')
		.attr('class', "ws_container")
		.attr('id', 'ws-menu-item-' + (wsIdCounter++))
		.data('menu_item', itemData)
		.data('field_editors_created', false);

	item.addClass(isTopLevel ? 'ws_menu' : 'ws_item');
	if ( itemData.separator ) {
		item.addClass('ws_menu_separator');
	}

	//Add a header and a container for property editors (to improve performance
	//the editors themselves are created later, when the user tries to access them
	//for the first time).
	var contents = [];
	var menuTitle = getFieldValue(itemData, 'menu_title', '');
	if (menuTitle === '') {
		menuTitle = '&nbsp;';
	}

	contents.push(
		'<div class="ws_item_head">',
			itemData.separator ? '' : '<a class="ws_edit_link"> </a><div class="ws_flag_container"> </div>',
			'<input type="checkbox" class="ws_actor_access_checkbox">',
			'<span class="ws_item_title">',
				formatMenuTitle(menuTitle),
			'&nbsp;</span>',

		'</div>',
		'<div class="ws_editbox" style="display: none;"></div>'
	);
	item.append(contents.join(''));

	//Apply flags based on the item's state
	var flags = ['hidden', 'unused', 'custom'];
	for (var i = 0; i < flags.length; i++) {
		setMenuFlag(item, flags[i], getFieldValue(itemData, flags[i], false));
	}

	if ( canHaveSubmenuItems ){
		//Allow the user to drag menu items to top-level menus
		item.droppable({
			'hoverClass' : 'ws_menu_drop_hover',

			'accept' : (function(thing){
				return thing.hasClass('ws_item');
			}),

			'drop' : (function(event, ui){
				const column = menuPresenter.getItemColumn(item);
				if (!column) {
					return;
				}
				const nextColumn = menuPresenter.getColumnImmediate(column.level + 1);
				const submenu = getSubmenuOf(item, nextColumn);
				if (!submenu || !nextColumn) {
					return;
				}

				const droppedItemData = readItemState(ui.draggable);
				const sourceSubmenu = ui.draggable.parent();

				let result = nextColumn.outputItem(droppedItemData, null, submenu);

				if ( !event.ctrlKey ) {
					menuPresenter.destroyItem(ui.draggable);
				}

				updateItemEditor(result.menu);

				//Moving an item can change aggregate menu permissions. Update the UI accordingly.
				updateParentAccessUi(submenu);
				if (sourceSubmenu) {
					updateParentAccessUi(sourceSubmenu);
				}
			})
		});
	}

	return item;
}

function jsTrim(str){
	return str.replace(/^\s+|\s+$/g, "");
}

//Expose this handy tool to our other scripts.
AmeEditorApi.jsTrim = jsTrim;

function stripAllTags(input) {
	//Based on: http://phpjs.org/functions/strip_tags/
	var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
		commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
	return input.replace(commentsAndPhpTags, '').replace(tags, '');
}

function truncateString(input, maxLength, padding) {
	if (typeof padding === 'undefined') {
		padding = '';
	}

	if (input.length > maxLength) {
		input = input.substring(0, maxLength - 1) + padding;
	}

	return input;
}

/**
 * Format menu title for display in HTML.
 * Strips tags and truncates long titles.
 *
 * @param {String} title
 * @returns {String}
 */
function formatMenuTitle(title) {
	title = stripAllTags(title);

	//Compact whitespace.
	title = title.replace(/[\s\t\r\n]+/g, ' ');
	title = jsTrim(title);

	//The max. length was chosen empirically.
	title = truncateString(title, 34, '\u2026');
	return title;
}

AmeEditorApi.formatMenuTitle = formatMenuTitle;

//Editor field spec template.
var baseField = {
	caption : '[No caption]',
    standardCaption : true,
	advanced : false,
	type : 'text',
	defaultValue: '',
	onlyForTopMenus: false,
	addDropdown : false,
	visible: true,

	write: null,
	display: null,

	tooltip: null
};

/*
 * List of all menu fields that have an associated editor
 */
var knownMenuFields = {
	'menu_title' : $.extend({}, baseField, {
		caption : 'Menu title',
		display: function(menuItem, displayValue, input, containerNode) {
			//Update the header as well.
			containerNode.find('.ws_item_title').text(formatMenuTitle(displayValue) + '\xa0');
			return displayValue;
		},
		write: function(menuItem, value, input, containerNode) {
			menuItem.menu_title = value;
			containerNode.find('.ws_item_title').text(stripAllTags(input.val()) + '\xa0');
		}
	}),

	'template_id' : $.extend({}, baseField, {
		caption : 'Target page',
		type : 'select',
		options : (function(){
			//Generate name => id mappings for all item templates + the special "Custom" template.
			var itemTemplateIds = [];
			itemTemplateIds.push([wsEditorData.customItemTemplate.name, '']);

			for (var template_id in wsEditorData.itemTemplates) {
				if (wsEditorData.itemTemplates.hasOwnProperty(template_id)) {
					itemTemplateIds.push([wsEditorData.itemTemplates[template_id].name, template_id]);
				}
			}

			itemTemplateIds.sort(function(a, b) {
				if (a[1] === b[1]) {
					return 0;
				}

				//The "Custom" item is always first.
				if (a[1] === '') {
					return -1;
				} else if (b[1] === '') {
					return 1;
				}

				//Top-level items go before submenus.
				var aIsTop = (a[1].charAt(0) === '>') ? 1 : 0;
				var bIsTop = (b[1].charAt(0) === '>') ? 1 : 0;
				if (aIsTop !== bIsTop) {
					return bIsTop - aIsTop;
				}

				//Everything else is sorted by name, in alphabetical order.
				if (a[0] > b[0]) {
					return 1;
				} else if (a[0] < b[0]) {
					return -1;
				}
				return 0;
			});

			return itemTemplateIds;
		})(),

		write: function(menuItem, value, input, containerNode) {
			var oldTemplateId = menuItem.template_id;

			menuItem.template_id = value;
			menuItem.defaults = itemTemplates.getDefaults(menuItem.template_id);
		    menuItem.custom = (menuItem.template_id === '');

		    // The file/URL of non-custom items is read-only and equal to the default
		    // value. Rationale: simplifies menu generation, prevents some user mistakes.
		    if (menuItem.template_id !== '') {
			    menuItem.file = null;
		    }

		    // The new template might not have default values for some of the fields
		    // currently set to null (= "default"). In those cases, we need to make
		    // the current values explicit.
		    containerNode.find('.ws_edit_field').each(function(index, field){
			    field = $(field);
			    var fieldName = field.data('field_name');
			    var isSetToDefault = (menuItem[fieldName] === null);
			    var hasDefaultValue = itemTemplates.hasDefaultValue(menuItem.template_id, fieldName);

			    if (isSetToDefault && !hasDefaultValue) {
					var oldDefaultValue = itemTemplates.getDefaultValue(oldTemplateId, fieldName);
					if (oldDefaultValue !== null) {
						menuItem[fieldName] = oldDefaultValue;
					}
			    }
		    });
		}
	}),

	'embedded_page_id' : $.extend({}, baseField, {
		caption: 'Embedded page ID',
		defaultValue: 'Select page to display',
		type: 'text',
		addDropdown: 'ws_embedded_page_selector',

		display: function(menuItem, displayValue, input) {
			input.prop('readonly', true);
			var pageId = parseInt(getFieldValue(menuItem, 'embedded_page_id', 0), 10),
				blogId = parseInt(getFieldValue(menuItem, 'embedded_page_blog_id', 1), 10),
				formattedId = 'ID: ' + pageId;

			if (pageId <= 0) {
				return 'Select page =>';
			}

			if (blogId !== 1) {
				formattedId = formattedId + ', blog ID: ' + blogId;
			}
			displayValue = formattedId;

			AmePageTitles.get(pageId, blogId, function(title) {
				//If we retrieved the title via AJAX, the user might have selected a different page in the meantime.
				//Make sure it's still the same page before displaying the title.
				var currentPageId = parseInt(getFieldValue(menuItem, 'embedded_page_id', 0), 10),
					currentBlogId = parseInt(getFieldValue(menuItem, 'embedded_page_blog_id', 1), 10);
				if ((currentPageId !== pageId) || (currentBlogId !== blogId)) {
					return;
				}

				displayValue = title + ' (' + formattedId + ')';
				input.val(displayValue);
			});

			return displayValue;
		},

		write: function() {
			//The user cannot directly edit this field. We deliberately ignore writes.
		},

		visible: function(menuItem) {
			//Only show this field if the "Embed WP page" template is selected.
			return (menuItem.template_id === wsEditorData.embeddedPageTemplateId);
		}
	}),

	'file' : $.extend({}, baseField, {
		caption: 'URL',
		display: function(menuItem, displayValue, input) {
			// The URL/file field is read-only for default menus. Also, since the "file"
			// field is usually set to a page slug or plugin filename for plugin/hook pages,
			// we display the dynamically generated "url" field here (i.e. the actual URL) instead.
			if (menuItem.template_id !== '') {
				input.prop('readonly', true);
				displayValue = itemTemplates.getDefaultValue(menuItem.template_id, 'url');
			} else {
				input.prop('readonly', false);
			}
			return displayValue;
		},

		write: function(menuItem, value) {
			// A menu must always have a non-empty URL. If the user deletes the current value,
			// reset it to the old value.
			if (value === '') {
				value = menuItem.file;
			}
			// Default menus always point to the default file/URL.
			if (menuItem.template_id !== '') {
				value = null;
			}
			menuItem.file = value;
		}
	}),

	'access_level' : $.extend({}, baseField, {
		caption: 'Permissions',
		defaultValue: 'read',
		type: 'access_editor',
		visible: false, //Will be set to visible only in Pro version.

		display: function(menuItem) {
			//Permissions display is a little complicated and could use improvement.
			var requiredCap = getFieldValue(menuItem, 'access_level', '');
			var extraCap = getFieldValue(menuItem, 'extra_capability', '');

			var displayValue = (menuItem.template_id === '') ? '< Custom >' : requiredCap;
			if (extraCap !== '') {
				if (menuItem.template_id === '') {
					displayValue = extraCap;
				} else {
					displayValue = displayValue + '+' + extraCap;
				}
			}

			return displayValue;
		},

		write: function(menuItem) {
			//The required capability can't be directly edited and always equals the default.
			menuItem.access_level = null;
		}
	}),

	//TODO: Never save this field. It just wastes database space.
	'required_capability_read_only' : $.extend({}, baseField, {
		caption: 'Required capability',
		defaultValue: 'none',
		type: 'text',
		tooltip: "Only users who have this capability can see the menu. "+
			"The capability can't be changed because it's usually hard-coded in WordPress or the plugin that created the menu."+
			"<br><br>Use the \"Extra capability\" field to restrict access to this menu.",

		visible: function(menuItem) {
			//Show only in the free version, on non-custom menus.
			return !wsEditorData.wsMenuEditorPro && (menuItem.template_id !== '');
		},

		display: function(menuItem, displayValue, input) {
			input.prop('readonly', true);
			return getFieldValue(menuItem, 'access_level', '');
		},

		write: function(menuItem, value) {
			//The required capability is read-only. Ignore writes.
		}
	}),

	'extra_capability' : $.extend({}, baseField, {
		caption: 'Extra capability',
		defaultValue: 'read',
		type: 'text',
		addDropdown: 'ws_cap_selector',
		tooltip: function(menuItem) {
			if (menuItem.template_id === '') {
				return 'Only users who have this capability can see the menu.';
			}
			return 'An additional capability check that is applied on top of the required capability.';
		},

		display: function(menuItem) {
			var requiredCap = getFieldValue(menuItem, 'access_level', '');
			var extraCap = getFieldValue(menuItem, 'extra_capability', '');

			//On custom menus, show the default required cap when no extra cap is selected.
			//Otherwise there would be no visible capability requirements at all.
			var displayValue = extraCap;
			if ((menuItem.template_id === '') && (extraCap === '')) {
				displayValue = requiredCap;
			}

			return displayValue;
		},

		write: function(menuItem, value) {
			value = jsTrim(value);

			//Reset to default if the user clears the input.
			if (value === '') {
				menuItem.extra_capability = null;
				return;
			}

			menuItem.extra_capability = value;
		}
	}),

	'appearance_heading' : $.extend({}, baseField, {
		caption: 'Appearance',
		advanced : true,
		onlyForTopMenus: false,
		type: 'heading',
		standardCaption: false,
		visible: false //Only visible in the Pro version.
	}),

	'icon_url' : $.extend({}, baseField, {
		caption: 'Icon URL',
		type : 'icon_selector',
		advanced : true,
		defaultValue: 'div',
		onlyForTopMenus: true,

		display: function(menuItem, displayValue, input, containerNode) {
			//Display the current icon in the selector.
			var cssClass = getFieldValue(menuItem, 'css_class', '');
			var iconUrl = getFieldValue(menuItem, 'icon_url', '', containerNode);
			displayValue = iconUrl;

			//When submenu icon visibility is set to "only if manually selected",
			//don't show the default submenu icons.
			var isDefault = (typeof menuItem.icon_url === 'undefined') || (menuItem.icon_url === null);
			if (isDefault && (wsEditorData.submenuIconsEnabled === 'if_custom') && containerNode.hasClass('ws_item')) {
				iconUrl = 'none';
				cssClass = '';
			}

			var selectButton = input.closest('.ws_edit_field').find('.ws_select_icon');
			var cssIcon = selectButton.find('.ws_icon_image');
			var imageIcon = selectButton.find('img');

			var matches = cssClass.match(/\b(ame-)?menu-icon-([^\s]+)\b/);
			var iconFontMatches = iconUrl && iconUrl.match(/^\s*((dashicons|ame-fa)-[a-z0-9\-]+)/);

			//Icon URL takes precedence over icon class.
			if ( iconUrl && iconUrl !== 'none' && iconUrl !== 'div' && !iconFontMatches ) {
				//Regular image icon.
				cssIcon.hide();
				imageIcon.prop('src', iconUrl).show();
			} else if ( iconFontMatches ) {
				cssIcon.removeClass().addClass('ws_icon_image');
				if ( iconFontMatches[2] === 'dashicons' ) {
					//Dashicon.
					cssIcon.addClass('dashicons ' + iconFontMatches[1]);
				} else if ( iconFontMatches[2] === 'ame-fa' ) {
					//FontAwesome icon.
					cssIcon.addClass('ame-fa ' + iconFontMatches[1]);
				}
				imageIcon.hide();
				cssIcon.show();
			} else if ( matches ) {
				//Other CSS-based icon.
				imageIcon.hide();
				var iconClass = (matches[1] ? matches[1] : '') + 'icon-' + matches[2];
				cssIcon.removeClass().addClass('ws_icon_image ' + iconClass).show();
			} else {
				//This menu has no icon at all. This is actually a valid state
				//and WordPress will display a menu like that correctly.
				imageIcon.hide();
				cssIcon.removeClass().addClass('ws_icon_image').show();
			}

			return displayValue;
		}
	}),

	'colors' : $.extend({}, baseField, {
		caption: 'Color scheme',
		defaultValue: 'Default',
		type: 'color_scheme_editor',
		onlyForTopMenus: true,
		visible: false,
		advanced : true,

		display: function(menuItem, displayValue, input, containerNode) {
			var colors = getFieldValue(menuItem, 'colors', {}) || {};
			var colorList = containerNode.find('.ws_color_scheme_display');

			colorList.empty();
			var count = 0, maxColorsToShow = 7;

			$.each(colors, function(name, value) {
				if ( !value || (count >= maxColorsToShow) ) {
					return;
				}

				colorList.append(
					$('<span></span>').addClass('ws_color_display_item').css('background-color', value)
				);
				count++;
			});

			if (count === 0) {
				colorList.append('Default');
			}

			return 'Placeholder. You should never see this.';
		},

		write: function(menuItem) {
			//Menu colors can't be directly edited.
		}
	}),

	'html_heading' : $.extend({}, baseField, {
		caption: 'HTML',
		advanced : true,
		onlyForTopMenus: true,
		type: 'heading',
		standardCaption: false
	}),

	'open_in' : $.extend({}, baseField, {
		caption: 'Open in',
		advanced : true,
		type : 'select',
		options : [
			['Same window or tab', 'same_window'],
			['New window', 'new_window'],
			['Frame', 'iframe']
		],
		defaultValue: 'same_window',
		visible: false
	}),

	'iframe_height' : $.extend({}, baseField, {
		caption: 'Frame height (pixels)',
		advanced : true,
		visible: function(menuItem) {
			return wsEditorData.wsMenuEditorPro && (getFieldValue(menuItem, 'open_in') === 'iframe');
		},

		display: function(menuItem, displayValue, input) {
			input.prop('placeholder', 'Auto');
			if (displayValue === 0 || displayValue === '0') {
				displayValue = '';
			}
			return displayValue;
		},

		write: function(menuItem, value) {
			value = parseInt(value, 10);
			if (isNaN(value) || (value < 0)) {
				value = 0;
			}
			value = Math.round(value);

			if (value > 10000) {
				value = 10000;
			}

			if (value === 0) {
				menuItem.iframe_height = null;
			} else {
				menuItem.iframe_height = value;
			}

		}
	}),

	'css_class' : $.extend({}, baseField, {
		caption: 'CSS classes',
		advanced : true
	}),

	'hookname' : $.extend({}, baseField, {
		caption: 'ID attribute',
		advanced : true,
		onlyForTopMenus: true
	}),

	'page_properties_heading' : $.extend({}, baseField, {
		caption: 'Page',
		advanced : true,
		onlyForTopMenus: true,
		type: 'heading',
		standardCaption: false
	}),

	'page_heading' : $.extend({}, baseField, {
		caption: 'Page heading',
		advanced : true,
		onlyForTopMenus: false,
		visible: false
	}),

	'page_title' : $.extend({}, baseField, {
		caption: "Window title",
		standardCaption : true,
		advanced : true
	}),

	'is_always_open' : $.extend({}, baseField, {
		caption: 'Keep this menu expanded',
		advanced : true,
		onlyForTopMenus: true,
		type: 'checkbox',
		standardCaption: false
	})
};

var visibleMenuFieldsByType = {};

AmeEditorApi.getItemDisplayUrl = function(menuItem) {
	var url = getFieldValue(menuItem, 'file', '');
	if (menuItem.template_id !== '') {
		//Use the template URL. It's a preset that can't be overridden.
		var defaultUrl = itemTemplates.getDefaultValue(menuItem.template_id, 'url');
		if (defaultUrl) {
			url = defaultUrl;
		}
	}
	return url;
};

/*
 * Create editors for the visible fields of a menu entry and append them to the specified node.
 */
function buildEditboxFields(fieldContainer, entry, isTopLevel){
	isTopLevel = (typeof isTopLevel === 'undefined') ? false : isTopLevel;

	var basicFields = $('<div class="ws_edit_panel ws_basic"></div>').appendTo(fieldContainer);
    var advancedFields = $('<div class="ws_edit_panel ws_advanced"></div>').appendTo(fieldContainer);

    if ( wsEditorData.hideAdvancedSettings ){
    	advancedFields.css('display', 'none');
    }

	for (var field_name in knownMenuFields){
		if (!knownMenuFields.hasOwnProperty(field_name)) {
			continue;
		}

		var fieldSpec = knownMenuFields[field_name];
		if (fieldSpec.onlyForTopMenus && !isTopLevel) {
			continue;
		}

		var field = buildEditboxField(entry, field_name, fieldSpec);
		if (field){
            if (fieldSpec.advanced){
                advancedFields.append(field);
            } else {
                basicFields.append(field);
            }
		}
	}

	//Add a link that shows/hides advanced fields
	fieldContainer.append(
		$('<div>').addClass('ws_toggle_container').append(
			$('<a></a>', {href: '#'})
				.addClass('ws_toggle_advanced_fields')
				.text(
					wsEditorData.hideAdvancedSettings
						? wsEditorData.captionShowAdvanced
						: wsEditorData.captionHideAdvanced
				)
				.toggle(!!wsEditorData.hideAdvancedSettings) //Conver to boolean because it could be a string ("1" or "0").
		)
	);
}

/*
 * Create an editor for a specified field.
 */
//noinspection JSUnusedLocalSymbols
function buildEditboxField(entry, field_name, field_settings){
	//Build a form field of the appropriate type
	var inputBox;
	var basicTextField = '<input type="text" class="ws_field_value">';
	//noinspection FallthroughInSwitchStatementJS
	switch(field_settings.type){
		case 'select':
			inputBox = $('<select class="ws_field_value">');
			var option = null;
			for( var index = 0; index < field_settings.options.length; index++ ){
				var optionTitle = field_settings.options[index][0];
				var optionValue = field_settings.options[index][1];

				option = $('<option>')
					.val(optionValue)
					.text(optionTitle);
				option.appendTo(inputBox);
			}
			break;

        case 'checkbox':
	        inputBox = $('<label></label>')
		        .append($('<input>', {type: 'checkbox', "class": 'ws_field_value'}))
		        .append(' ')
		        .append($('<span></span>', {"class": 'ws_field_label_text'}).text(field_settings.caption))
            break;

		case 'access_editor':
			inputBox = $('<input type="text" class="ws_field_value" readonly="readonly">')
                .add('<input type="button" class="button ws_launch_access_editor" value="Edit...">');
			break;

		case 'icon_selector':
			//noinspection HtmlUnknownTag
			inputBox = $(basicTextField)
                .add('<button class="button ws_select_icon" title="Select icon"><div class="ws_icon_image dashicons dashicons-admin-generic"></div><img src="" style="display:none;" alt="Icon"></button>');
			break;

		case 'color_scheme_editor':
			inputBox = $('<span class="ws_color_scheme_display">Placeholder</span>')
				.add('<input type="button" class="button ws_open_color_editor" value="Edit...">');
			break;

		case 'heading':
			inputBox = $('<span></span>').text(field_settings.caption);
			break;

		case 'text':
			/* falls through */
		default:
			inputBox = $(basicTextField);
	}


	var className = "ws_edit_field ws_edit_field-"+field_name;
	if (field_settings.addDropdown){
		className += ' ws_has_dropdown';
	}
	if (!field_settings.standardCaption) {
		className += ' ws_no_field_caption';
	}
	if (field_settings.type === 'heading') {
		className += ' ws_field_group_heading';
	}

	var caption = $(); //Empty set by default.
	if (field_settings.standardCaption) {
		var $labelText = $('<span></span>')
			.addClass('ws_field_label_text')
			.text(field_settings.caption + ' ');

		if (field_settings.tooltip !== null) {
			$labelText.append(
				'<a class="ws_field_tooltip_trigger"><div class="dashicons dashicons-info"></div></a>'
			);
		}

		caption = caption.add($labelText).add('<br>'); //Note: add(), not append().
	}
	var editField = $('<div></div>')
		.attr('class', className)
		.append(caption)
		.append(inputBox);

	if (field_settings.addDropdown) {
		//Add a dropdown button
		var dropdownId = field_settings.addDropdown;
		editField.append(
			$('<input type="button" value="&#xf347;">')
				.addClass('button ws_dropdown_button ' + dropdownId + '_trigger')
				.attr('tabindex', '-1')
				.data('dropdownId', dropdownId)
		);
	}

	editField
		.append(
			$('<img class="ws_reset_button" title="Reset to default value" src="" alt="Reset">')
				.attr('src', wsEditorData.imagesUrl + '/transparent16.png')
		).data('field_name', field_name);

	var visible;
	if (typeof field_settings.visible === 'function') {
		visible = field_settings.visible(entry, field_name);
	} else {
		visible = field_settings.visible;
	}
	if (!visible) {
		editField.css('display', 'none');
	}

	return editField;
}

/**
 * Get the parent menu of a menu item.
 *
 * @param containerNode A DOM element as a jQuery object.
 * @return {JQuery} Parent container node, or an empty jQuery set.
 */
function getParentMenuNode(containerNode) {
	var submenu = containerNode.closest('.ws_submenu', '#ws_menu_editor'),
		parentId = submenu.data('parent_menu_id');
	if (parentId) {
		return $('#' + parentId);
	} else {
		return $([]);
	}
}

/**
 * Check if a menu item is the parent of another item or a submenu list.
 *
 * @param {JQuery} menuItem
 * @param {JQuery} something
 * @returns {boolean}
 */
function isParentMenuNodeOf(menuItem, something) {
	const parent = getParentMenuNode(something)
	if (menuItem.is(parent)) {
		return true;
	} else if (parent.length > 0) {
		return isParentMenuNodeOf(menuItem, parent);
	}
	return false;
}

/**
 * Get all submenu items of a menu item.
 *
 * @param {JQuery} containerNode
 * @return {JQuery} A list of submenu item container nodes, or an empty set.
 */
function getSubmenuItemNodes(containerNode) {
	var subMenuId = containerNode.data('submenu_id');
	if (subMenuId) {
		return $('#' + subMenuId).find('.ws_container');
	} else {
		return $([]);
	}
}

/**
 * Apply a callback recursively to a menu item and all of its children, in depth-first order.
 * The callback will be invoked with two arguments: (containerNode, menuItem).
 *
 * @param containerNode
 * @param {Function} callback
 */
function walkMenuTree(containerNode, callback) {
	getSubmenuItemNodes(containerNode).each(function() {
		walkMenuTree($(this), callback);
	});
	callback(containerNode, containerNode.data('menu_item'));
}

/**
 * Update the UI elements that that indicate whether the currently selected
 * actor can access a menu item.
 *
 * @param containerNode
 */
function updateActorAccessUi(containerNode) {
	//Update the permissions checkbox & UI
	const menuItem = containerNode.data('menu_item');
	if (actorSelectorWidget.selectedActor !== null) {
		let hasAccess = actorCanAccessMenu(menuItem, actorSelectorWidget.selectedActor);
		const hasCustomPermissions = actorHasCustomPermissions(menuItem, actorSelectorWidget.selectedActor);

		let isOverrideActive = !hasAccess && getFieldValue(menuItem, 'restrict_access_to_items', false);

		//Check if the parent menu has the "hide all submenus if this is hidden" override in effect.
		let currentChild = containerNode, parentNode, parentItem;
		do {
			parentNode = getParentMenuNode(currentChild);
			parentItem = parentNode.data('menu_item');
			if (
				parentItem
				&& getFieldValue(parentItem, 'restrict_access_to_items', false)
				&& !actorCanAccessMenu(parentItem, actorSelectorWidget.selectedActor)
			) {
				hasAccess = false;
				isOverrideActive = true;
				break;
			}
			currentChild = parentNode;
		} while (parentNode.length > 0);

		//For better UX, try to predict the visible/hidden state even when we can't determine
		//it reliably for items that use meta capabilities.
		let predictedHasAccess = !!hasAccess;
		let isUncertainMetaCap = false;

		//Check meta capabilities.
		if (hasAccess === null) {
			const requiredCap = getFieldValue(menuItem, 'access_level', '< Error: access_level is missing! [2] >');
			const result = AmeCapabilityManager.maybeHasMetaCap(actorSelectorWidget.selectedActor, requiredCap);
			if (result !== null) {
				predictedHasAccess = !!result.prediction;
				isUncertainMetaCap = true;
			}
		}

		const checkbox = containerNode.find('.ws_actor_access_checkbox');
		checkbox.prop('checked', predictedHasAccess);

		//Display the checkbox in an indeterminate state if the actual menu permissions are unknown
		//because it uses meta capabilities.
		let isIndeterminate = (hasAccess === null);
		//Also show it as indeterminate if some items of this menu are hidden and some are visible,
		//or if their permissions don't match this menu's permissions.
		const submenuItems = getSubmenuItemNodes(containerNode);
		if ((submenuItems.length > 0) && !isOverrideActive)  {
			let differentPermissions = false;
			submenuItems.each(function() {
				const item = $(this).data('menu_item');
				if ( !item ) { //Skip placeholder items created by drag & drop operations.
					return true;
				}
				const hasSubmenuAccess = actorCanAccessMenu(item, actorSelectorWidget.selectedActor);
				if (hasSubmenuAccess !== hasAccess) {
					differentPermissions = true;
					return false;
				}
				return true;
			});

			if (differentPermissions) {
				isIndeterminate = true;
			}
		}
		checkbox.prop('indeterminate', isIndeterminate);

		if (isUncertainMetaCap) {
			setMenuFlag(
				containerNode,
				'uncertain_meta_cap',
				true,
				"This item might " + (predictedHasAccess ? 'not ' : '') + "be visible.\n"
				+ "The plugin cannot reliably detect if \"" + actorSelectorWidget.selectedDisplayName
				+ "\" has the \"" + getFieldValue(menuItem, 'access_level', '[No capability]')
				+ "\" capability. If you need to hide the item, try checking and then unchecking it."
			);
		} else {
			setMenuFlag(containerNode, 'uncertain_meta_cap', false);
		}

		containerNode.toggleClass('ws_is_hidden_for_actor', !predictedHasAccess);
		containerNode.toggleClass('ws_has_custom_permissions_for_actor', hasCustomPermissions);
		setMenuFlag(containerNode, 'custom_actor_permissions', hasCustomPermissions);
		setMenuFlag(containerNode, 'hidden_from_others', false);
	} else {
		containerNode.removeClass('ws_is_hidden_for_actor ws_has_custom_permissions_for_actor');
		setMenuFlag(containerNode, 'custom_actor_permissions', false);
		setMenuFlag(containerNode, 'uncertain_meta_cap', false);

		const currentUserActor = 'user:' + wsEditorData.currentUserLogin;
		const otherActors = _(wsEditorData.actors).keys().without(currentUserActor, 'special:super_admin').value(),
			hiddenFromCurrentUser = !actorCanAccessMenu(menuItem, currentUserActor),
			hasAccessToThisItem = _.curry(actorCanAccessMenu, 2)(menuItem),
			hiddenFromOthers = _.every(otherActors, function (actorId) {
				return (hasAccessToThisItem(actorId) === false);
			}),
			visibleForSuperAdmin = AmeActors.isMultisite && actorCanAccessMenu(menuItem, 'special:super_admin');

		setMenuFlag(
			containerNode,
			'hidden_from_others',
			hiddenFromOthers,
			hiddenFromCurrentUser
				? 'Hidden from everyone'
				: ('Hidden from everyone except you' + (visibleForSuperAdmin ? ' and Super Admins' : ''))
		);
	}

	//Update the "hidden" flag.
	setMenuFlag(containerNode, 'hidden', itemHasHiddenFlag(menuItem, actorSelectorWidget.selectedActor));
}

/**
 * Like updateActorAccessUi() except it updates the specified menu's parent, not the menu itself.
 * If the menu has no parent (i.e. it's a top-level menu), this function does nothing.
 *
 * @param containerNode Either a menu item or a submenu container.
 */
function updateParentAccessUi(containerNode) {
	var submenu;
	if ( containerNode.is('.ws_submenu') ) {
		submenu = containerNode;
	} else {
		submenu = containerNode.parent();
	}

	var parentId = submenu.data('parent_menu_id');
	if (parentId) {
		updateActorAccessUi($('#' + parentId));
	}
}

/**
 * Update an edit widget with the current menu item settings.
 *
 * @param {JQuery} containerNode
 */
function updateItemEditor(containerNode) {
	var menuItem = containerNode.data('menu_item');
	var itemSubType = (menuItem.hasOwnProperty('sub_type') ? menuItem['sub_type'] : '');

	//Apply flags based on the item's state.
	var flags = ['hidden', 'unused', 'custom'];
	for (var i = 0; i < flags.length; i++) {
		setMenuFlag(containerNode, flags[i], getFieldValue(menuItem, flags[i], false));
	}

	if (itemSubType) {
		var typeTitle = itemSubType.charAt(0).toUpperCase() + itemSubType.slice(1);
		setMenuFlag(containerNode, 'subtype_' + itemSubType, true, typeTitle);
	}

	//Update the permissions checkbox & other actor-specific UI
	updateActorAccessUi(containerNode);

	//Update all input fields with the current values.
	containerNode.find('.ws_edit_field').each(function(index, field) {
		field = $(field);
		var fieldName = field.data('field_name');
		var input = field.find('.ws_field_value').first();

		var hasADefaultValue = itemTemplates.hasDefaultValue(menuItem.template_id, fieldName);
		var defaultValue = getDefaultValue(menuItem, fieldName, null, containerNode);
		var isDefault = hasADefaultValue && ((typeof menuItem[fieldName] === 'undefined') || (menuItem[fieldName] === null));

        if (fieldName === 'access_level') {
            isDefault = (getFieldValue(menuItem, 'extra_capability', '') === '')
				&& isEmptyObject(menuItem.grant_access)
				&& (!getFieldValue(menuItem, 'restrict_access_to_items', false));
        } else if (fieldName === 'required_capability_read_only') {
        	isDefault = true;
	        hasADefaultValue = true;
        }

		field.toggleClass('ws_has_no_default', !hasADefaultValue);
		field.toggleClass('ws_input_default', isDefault);

		var displayValue = isDefault ? defaultValue : menuItem[fieldName];
		if (knownMenuFields[fieldName].display !== null) {
			displayValue = knownMenuFields[fieldName].display(menuItem, displayValue, input, containerNode);
		}

        setInputValue(input, displayValue);

		//Store the value to help with change detection.
		if (input.length > 0) {
			$.data(input.get(0), 'ame_last_display_value', displayValue);
		}

		var isFieldVisible = _.get(visibleMenuFieldsByType, [itemSubType, fieldName], true);
		if (typeof (knownMenuFields[fieldName].visible) === 'function') {
			isFieldVisible = isFieldVisible && knownMenuFields[fieldName].visible(menuItem, fieldName);
		} else {
			isFieldVisible = isFieldVisible && knownMenuFields[fieldName].visible;
		}
		if (isFieldVisible) {
			field.css('display', '');
		} else {
			field.css('display', 'none');
		}
    });
}

AmeEditorApi.updateParentAccessUi = updateParentAccessUi;
AmeEditorApi.updateItemEditor = updateItemEditor;

function isEmptyObject(obj) {
    for (var prop in obj) {
        if (obj.hasOwnProperty(prop)) {
            return false;
        }
    }
    return true;
}

/**
 * Get the current value of a single menu field.
 *
 * If the specified field is not set, this function will attempt to retrieve it
 * from the "defaults" property of the menu object. If *that* fails, it will return
 * the value of the optional third argument defaultValue.
 *
 * @param {Object} entry
 * @param {string} fieldName
 * @param {*} [defaultValue]
 * @param {JQuery} [containerNode]
 * @return {*}
 */
function getFieldValue(entry, fieldName, defaultValue, containerNode){
	if ( (typeof entry[fieldName] === 'undefined') || (entry[fieldName] === null) ) {
		return getDefaultValue(entry, fieldName, defaultValue, containerNode);
	} else {
		return entry[fieldName];
	}
}

AmeEditorApi.getFieldValue = getFieldValue;

/**
 * Get the default value of a menu field.
 *
 * @param {Object} entry
 * @param {String} fieldName
 * @param {*} [defaultValue]
 * @param {JQuery} [containerNode]
 * @returns {*}
 */
function getDefaultValue(entry, fieldName, defaultValue, containerNode) {
	//By default, a submenu item has the same icon as its parent.
	if ((fieldName === 'icon_url') && containerNode && (wsEditorData.submenuIconsEnabled !== 'never')) {
		var parentContainerNode = getParentMenuNode(containerNode),
			parentMenuItem = parentContainerNode.data('menu_item');
		if (parentMenuItem) {
			return getFieldValue(parentMenuItem, fieldName, defaultValue, parentContainerNode);
		}
	}

	//Use the custom menu title as the page title if the default page title matches the default menu title.
	//Note that if the page title is an empty string (''), WP automatically uses the menu title. So we do the same.
	if ((fieldName === 'page_title') && (entry.template_id !== '')) {
		var defaultPageTitle = itemTemplates.getDefaultValue(entry.template_id, 'page_title'),
			defaultMenuTitle = itemTemplates.getDefaultValue(entry.template_id, 'menu_title'),
			customMenuTitle = entry['menu_title'];

		if (
			(customMenuTitle !== null)
			&& (customMenuTitle !== '')
			&& ((defaultPageTitle === '') || (defaultMenuTitle === defaultPageTitle))
		) {
			return customMenuTitle;
		}
	}

	if (typeof defaultValue === 'undefined') {
		defaultValue = null;
	}

	//Known templates take precedence.
	if ((entry.template_id === '') || (typeof itemTemplates.templates[entry.template_id] !== 'undefined')) {
		var templateDefault = itemTemplates.getDefaultValue(entry.template_id, fieldName);
		return (templateDefault !== null) ? templateDefault : defaultValue;
	}

	if (fieldName === 'template_id') {
		return null;
	}

	//Separators can have their own defaults, independent of templates.
	var hasDefault = (typeof entry.defaults !== 'undefined') && (typeof entry.defaults[fieldName] !== 'undefined');
	if (hasDefault){
		return entry.defaults[fieldName];
	}

	return defaultValue;
}

/*
 * Make a menu container sortable
 */
function makeBoxSortable(menuBox){
	//Make the submenu sortable
	menuBox.sortable({
		items: '> .ws_container',
		cursor: 'move',
		dropOnEmpty: true,
		cancel : '.ws_editbox, .ws_edit_link',

		placeholder: 'ws_container ws_sortable_placeholder',
		forcePlaceholderSize: true,

		connectWith: '.ws_submenu',

		stop: function(even, ui) {
			//Fix incorrect item overlap caused by jQuery.sortable applying the initial z-index as an inline style.
			ui.item.css('z-index', '');

			//Fix submenu container height. It should be tall enough to reach the selected parent menu.
			if (ui.item.hasClass('ws_menu') && ui.item.hasClass('ws_active')) {
				AmeEditorApi.updateSubmenuBoxHeight(ui.item);
			}
		},

		over: function(event, ui) {
			//Provide visual feedback if the user drags an unacceptable item over the list.
			const $list = $(this);
			const targetColumn = menuPresenter.getItemColumn($list);
			if (!targetColumn) {
				return;
			}

			$list.closest('.ws_main_container').toggleClass(
				'ws_invalid_item_drop_target',
				!targetColumn.canAcceptItem(ui.item)
			);
		},

		out: function() {
			$(this).closest('.ws_main_container').removeClass('ws_invalid_item_drop_target');
		},

		receive: function(event, ui) {
			//Receive a menu item from another column.
			const $sender = $(ui.sender);

			const $itemNode = ui.item;
			const targetColumn = menuPresenter.getItemColumn($itemNode);
			const sourceColumn = menuPresenter.getItemColumn($sender);

			if (!targetColumn || !sourceColumn)  {
				$sender.sortable('cancel');
				return;
			}
			if (!targetColumn.canAcceptItem($itemNode)) {
				$sender.sortable('cancel');
				return;
			}

			//The way that inter-column drag & drop actually works is that we copy the item
			//to the target column and then delete the original item. This way all the internal
			//data structures are updated correctly.

			//Remember where the item was dropped in the target column.
			const $previousItem = $itemNode.prev('.ws_container');

			//Move the original item back.
			$sender.sortable('cancel');

			//Copy & paste the item to the target column.
			const droppedItemData = readItemState($itemNode);
			targetColumn.pasteItem(droppedItemData, ($previousItem.length > 0) ? $previousItem : -1);

			//Delete the original. Optionally, the user can hold Ctrl to avoid this
			//(i.e. to copy the item instead of moving it).
			if ( !event.ctrlKey ) {
				sourceColumn.destroyItem($itemNode);
			}
		}
	});
}

/**
 * Iterates over all menu items invoking a callback for each item.
 *
 * The callback will be passed two arguments: the menu item and its UI container node (a jQuery object).
 * You can stop iteration by returning false from the callback.
 *
 * @param {Function} callback
 * @param {boolean} [skipSeparators] Defaults to true. Set to false to include separators in the iteration.
 */
AmeEditorApi.forEachMenuItem = function(callback, skipSeparators) {
	if (typeof skipSeparators === 'undefined') {
		skipSeparators = true;
	}

	$('#ws_menu_editor').find('.ws_container').each(function() {
		var containerNode = $(this);
		if ( !(skipSeparators && containerNode.hasClass('ws_menu_separator')) ) {
			return callback(containerNode.data('menu_item'), containerNode);
		}
	});
};

/**
 * Select the first menu item that has the specified URL.
 *
 * @param {number|string} selectorOrLevel
 * @param {string} url
 * @param {null|Boolean} [expandProperties]
 * @returns {JQuery}
 */
AmeEditorApi.selectMenuItemByUrl = function(selectorOrLevel, url, expandProperties) {
	if (typeof expandProperties === 'undefined') {
		expandProperties = null;
	}

	let level;
	if (selectorOrLevel === '#ws_menu_box') {
		level = 1;
	} else if (selectorOrLevel === '#ws_submenu_box') {
		level = 2;
	} else {
		level = selectorOrLevel;
	}

	const column = menuPresenter.getColumnImmediate(level);
	if (!column) {
		return $([]);
	}

	const box = column.getVisibleItemList();

	const containerNode =
		box.find('.ws_container')
		.filter(function() {
			const itemUrl = AmeEditorApi.getItemDisplayUrl($(this).data('menu_item'));
			return (itemUrl === url);
		})
		.first();

	if (containerNode.length > 0) {
		AmeEditorApi.selectItem(containerNode);

		if (expandProperties !== null) {
			const expandLink = containerNode.find('.ws_edit_link').first();
			if (expandLink.hasClass('ws_edit_link_expanded') !== expandProperties) {
				expandLink.trigger('click');
			}
		}
	}
	return containerNode;
};

/***************************************************************************
                       Parsing & encoding menu inputs
 ***************************************************************************/

/**
 * Encode the current menu structure as JSON
 *
 * @return {String} A JSON-encoded string representing the current menu tree loaded in the editor.
 */
function encodeMenuAsJSON(tree){
	if (typeof tree === 'undefined' || !tree) {
		tree = readMenuTreeState();
	}
	tree.format = {
		name: wsEditorData.menuFormatName,
		version: wsEditorData.menuFormatVersion
	};

	//Compress the admin menu.
	tree = compressMenu(tree);

	return JSON.stringify(tree);
}

function readMenuTreeState(){
	var tree = {};
	var menuPosition = 0;
	var itemsByFilename = {};

	//Gather all menus and their items
	$('#ws_menu_box').find('.ws_menu').each(function() {
		var containerNode = this;
		var menu = readItemState(containerNode, menuPosition++);

		//Attach the current menu to the main structure.
		var filename = getFieldValue(menu, 'file');

		//Give unclickable items unique keys.
		if (menu.template_id === wsEditorData.unclickableTemplateId) {
			ws_paste_count++;
			filename = '#' + wsEditorData.unclickableTemplateClass + '-' + ws_paste_count;
		} else if (menu.template_id === wsEditorData.embeddedPageTemplateId) {
			ws_paste_count++;
			filename = '#embedded-page-' + ws_paste_count;
		}

		//Prevent the user from saving top level items with duplicate URLs.
		//WordPress indexes the submenu array by parent URL and AME uses a {url : menu_data} hashtable internally.
		//Duplicate URLs would cause problems for both.
		if (itemsByFilename.hasOwnProperty(filename)) {
			throw {
				code: 'duplicate_top_level_url',
				message: 'Error: Found a duplicate URL! All top level menus must have unique URLs.',
				duplicates: [itemsByFilename[filename], containerNode]
			};
		}

		tree[filename] = menu;
		itemsByFilename[filename] = containerNode;
	});

	// Ensure items that need auto-generated slugs have unique IDs. The IDs only
	// need to be unique within the same menu configuration, not globally.
	let localIdCounter = 0;
	const usedLocalIds = {};
	function ensureUniqueIdIfNeeded(menuItem) {
		// Recurse into children.
		if (menuItem.items) {
			_.forEach(menuItem.items, ensureUniqueIdIfNeeded);
		}

		const needsUniqueId = (menuItem.template_id === wsEditorData.embeddedPageTemplateId)
			|| (menuItem.open_in === 'iframe');
		const currentLocalId = (typeof menuItem.local_id === 'string') ? menuItem.local_id : '';

		// Assign a new ID if the item needs one and doesn't have it, or if the current ID
		// is a duplicate. IDs can get duplicated if the user copies and pastes items.
		if ((needsUniqueId && (currentLocalId === '')) || usedLocalIds.hasOwnProperty(currentLocalId)) {
			menuItem.local_id = randomMenuId(localIdCounter + 'C', 8);
		}

		if (typeof menuItem.local_id === 'string') {
			usedLocalIds[menuItem.local_id] = true;
			localIdCounter++;
		}
	}
	_.forEach(tree, ensureUniqueIdIfNeeded);

	AmeCapabilityManager.pruneGrantedUserCapabilities();

	var result = {
		tree: tree,
		granted_capabilities: AmeCapabilityManager.getGrantedCapabilities(),
		suspected_meta_caps: AmeCapabilityManager.getSuspectedMetaCaps(),
		component_visibility: $.extend(true, {}, generalComponentVisibility)
	};

	$(document).trigger('getMenuConfiguration.adminMenuEditor', result);
	return result;
}

/**
 * Losslessly compress the admin menu configuration.
 *
 * This is a JS port of the ameMenu::compress() function defined in /includes/menu.php.
 *
 * @param {Object} adminMenu
 * @returns {Object}
 */
function compressMenu(adminMenu) {
	var common = {
		properties: _.omit(wsEditorData.blankMenuItem, ['defaults']),
		basic_defaults: _.clone(_.get(wsEditorData.blankMenuItem, 'defaults', {})),
		custom_item_defaults: _.clone(itemTemplates.getTemplateById('').defaults)
	};

	adminMenu.format.compressed = true;
	adminMenu.format.common = common;

	function compressItem(item) {
		//These empty arrays can be dropped.
		if ( _.isEmpty(item['grant_access']) ) {
			delete item['grant_access'];
		}
		if ( _.isEmpty(item['items']) ) {
			delete item['items'];
		}

		//Normal and custom menu items have different defaults.
		//Remove defaults that are the same for all items of that type.
		var defaults = _.get(item, 'custom', false) ? common['custom_item_defaults'] : common['basic_defaults'];
		if ( _.has(item, 'defaults') ) {
			_.forEach(defaults, function(value, key) {
				if (_.has(item['defaults'], key) && (item['defaults'][key] === value)) {
					delete item['defaults'][key];
				}
			});
		}

		//Remove properties that match the common values.
		_.forEach(common['properties'], function(value, key) {
			if (_.has(item, key) && (item[key] === value)) {
				delete item[key];
			}
		});

		return item;
	}

	adminMenu.tree = _.mapValues(adminMenu.tree, function(topMenu) {
		topMenu = compressItem(topMenu);
		if (typeof topMenu.items !== 'undefined') {
			topMenu.items = _.map(topMenu.items, compressItem);
		}
		return topMenu;
	});

	return adminMenu;
}

AmeEditorApi.readMenuTreeState = readMenuTreeState;
AmeEditorApi.encodeMenuAsJson = encodeMenuAsJSON;

/**
 * Extract the current menu item settings from its editor widget.
 *
 * @param itemDiv DOM node containing the editor widget, usually with the .ws_item or .ws_menu class.
 * @param {Number} [position] Menu item position among its sibling menu items. Defaults to zero.
 * @return {Object} A menu object in the tree format.
 */
function readItemState(itemDiv, position){
	position = (typeof position === 'undefined') ? 0 : position;

	itemDiv = $(itemDiv);
	var item = $.extend(true, {}, wsEditorData.blankMenuItem, itemDiv.data('menu_item'), readAllFields(itemDiv));

	item.defaults = itemDiv.data('menu_item').defaults;

	//Save the position data
	item.position = position;
	item.defaults.position = position; //The real default value will later overwrite this

	item.separator = itemDiv.hasClass('ws_menu_separator');
	item.custom = menuHasFlag(itemDiv, 'custom');

	//Gather the menu's sub-items, if any
	item.items = [];
	var subMenuId = itemDiv.data('submenu_id');
	if (subMenuId) {
		var itemPosition = 0;
		$('#' + subMenuId).find('.ws_item').each(function () {
			var sub_item = readItemState(this, itemPosition++);
			item.items.push(sub_item);
		});
	}

	return item;
}

/*
 * Extract the values of all menu/item fields present in a container node
 *
 * Inputs:
 *	container - a jQuery collection representing the node to read.
 */
function readAllFields(container){
	if ( !container.hasClass('ws_container') ){
		container = container.closest('.ws_container');
	}

	if ( !container.data('field_editors_created') ){
		return container.data('menu_item');
	}

	var state = {};

	//Iterate over all fields of the item
	container.find('.ws_edit_field').each(function() {
		var field = $(this);

		//Get the name of this field
		var field_name = field.data('field_name');
		//Skip if unnamed
		if (!field_name) {
			return true;
		}

		//Hackety-hack. The "Page" input is for display purposes and contains more than just the ID. Skip it.
		//Eventually we'll need a better way to handle this.
		if (field_name === 'embedded_page_id') {
			return true;
		}
		//Headings contain no useful data.
		if (field.hasClass('ws_field_group_heading')) {
			return true;
		}

		//Find the field (usually an input or select element).
		var input_box = field.find('.ws_field_value');

		//Save null if default used, custom value otherwise
		if (field.hasClass('ws_input_default')){
			state[field_name] = null;
		} else {
			state[field_name] = getInputValue(input_box);
		}
		return true;
	});

    //Permission settings are not stored in the visible access_level field (that's just for show),
    //so do not attempt to read them from there.
    state.access_level = null;

	return state;
}


/***************************************************************************
 Flag manipulation
 ***************************************************************************/

const item_flags = {
	'custom': 'This is a custom menu item',
	'unused': 'This item was added since the last time you saved menu settings.',
	'hidden': 'Cosmetically hidden',
	'custom_actor_permissions': "The selected role has custom permissions for this item.",
	'hidden_from_others': 'Hidden from everyone except you.',
	'uncertain_meta_cap': 'The plugin cannot detect if this item is visible by default.'
};

function setMenuFlag(item, flag, state, title) {
	title = title || item_flags[flag];
	item = $(item);

	const item_class = 'ws_' + flag;
	const img_class = 'ws_' + flag + '_flag';

	item.toggleClass(item_class, state);
	if (state) {
		//Add the flag image.
		const flag_container = item.find('.ws_flag_container');
		let image = flag_container.find('.' + img_class);
		if (image.length === 0) {
			image = $('<div></div>').addClass('ws_flag').addClass(img_class);
			flag_container.append(image);
		}
		image.attr('title', title);
	} else {
		//Remove the flag image.
		item.find('.' + img_class).remove();
	}
}

function menuHasFlag(item, flag){
	return $(item).hasClass('ws_'+flag);
}

//The "hidden" flag is special. There's both a global version and one that's actor-specific.

/**
 * Check if a menu item is hidden from an actor.
 * This function only checks the "hidden" and "hidden_from_actor" flags, not permissions.
 *
 * @param {Object} menuItem
 * @param {string|null} actor
 * @returns {boolean}
 */
function itemHasHiddenFlag(menuItem, actor) {
	let isHidden = false,
		userActors,
		userPrefix = 'user:',
		userLogin;

	//(Only) A globally hidden item is hidden from everyone.
	if ((actor === null) || menuItem.hidden) {
		return menuItem.hidden;
	}

	if (actor.substr(0, userPrefix.length) === userPrefix) {
		//You can set an exception for a specific user. It takes precedence.
		if (menuItem.hidden_from_actor.hasOwnProperty(actor)) {
			isHidden = menuItem.hidden_from_actor[actor];
		} else {
			//Otherwise the item is hidden only if it is hidden from all of the user's roles.
			userLogin = actorSelectorWidget.selectedActor.substr(userPrefix.length);
			userActors = AmeCapabilityManager.getGroupActorsFor(userLogin);
			for (let i = 0; i < userActors.length; i++) {
				if (menuItem.hidden_from_actor.hasOwnProperty(userActors[i]) && menuItem.hidden_from_actor[userActors[i]]) {
					isHidden = true;
				} else {
					isHidden = false;
					break;
				}
			}
		}
	} else {
		//Roles and the super admin are straightforward.
		isHidden = menuItem.hidden_from_actor.hasOwnProperty(actor) && menuItem.hidden_from_actor[actor];
	}

	return isHidden;
}

/**
 * Toggle menu visibility without changing its permissions.
 *
 * Applies to the selected actor, or all actors if no actor is selected.
 *
 * @param {JQuery} selection A menu container node.
 * @param {boolean} [isHidden] Optional. True = hide the menu, false = show the menu.
 */
function toggleItemHiddenFlag(selection, isHidden) {
	var menuItem = selection.data('menu_item');

	//By default, invert the current state.
	if (typeof isHidden === 'undefined') {
		isHidden = !itemHasHiddenFlag(menuItem, actorSelectorWidget.selectedActor);
	}

	//Mark the menu as hidden/visible
	if (actorSelectorWidget.selectedActor === null) {
		//For ALL roles and users.
		menuItem.hidden = isHidden;
		menuItem.hidden_from_actor = {};
	} else {
		//Just for the current role.
		if (isHidden) {
			menuItem.hidden_from_actor[actorSelectorWidget.selectedActor] = true;
		} else {
			if (actorSelectorWidget.selectedActor.indexOf('user:') === 0) {
				//User-specific exception. Lets you can hide a menu from all admins but leave it visible to yourself.
				menuItem.hidden_from_actor[actorSelectorWidget.selectedActor] = false;
			} else {
				delete menuItem.hidden_from_actor[actorSelectorWidget.selectedActor];
			}
		}

		//When the user un-hides a menu that was globally hidden via the "hidden" flag, we must remove
		//that flag but also make sure the menu stays hidden from other roles.
		if (!isHidden && menuItem.hidden) {
			menuItem.hidden = false;
			$.each(wsEditorData.actors, function(otherActor) {
				if (otherActor !== actorSelectorWidget.selectedActor) {
					menuItem.hidden_from_actor[otherActor] = true;
				}
			});
		}
	}
	setMenuFlag(selection, 'hidden', isHidden);

	//Also mark all of it's submenus as hidden/visible
	var submenuId = selection.data('submenu_id');
	if (submenuId) {
		$('#' + submenuId + ' .ws_item').each(function(){
			toggleItemHiddenFlag($(this), isHidden);
		});
	}
}

/***********************************************************
                  Capability manipulation
 ************************************************************/

function actorCanAccessMenu(menuItem, actor) {
	if (!$.isPlainObject(menuItem.grant_access)) {
		menuItem.grant_access = {};
	}

	//By default, any actor that has the required cap has access to the menu.
	//Users can override this on a per-menu basis.
	const requiredCap = getFieldValue(menuItem, 'access_level', '< Error: access_level is missing! >');
	let actorHasAccess;
	if (menuItem.grant_access.hasOwnProperty(actor)) {
		actorHasAccess = menuItem.grant_access[actor];
	} else {
		actorHasAccess = AmeCapabilityManager.hasCap(actor, requiredCap, menuItem.grant_access);
	}
	return actorHasAccess;
}

AmeEditorApi.actorCanAccessMenu = actorCanAccessMenu;

function actorHasCustomPermissions(menuItem, actor) {
	if (menuItem.grant_access && menuItem.grant_access.hasOwnProperty && menuItem.grant_access.hasOwnProperty(actor)) {
		return (menuItem.grant_access[actor] !== null);
	}
	return false;
}

/**
 * @param containerNode
 * @param {string|Object.<string, boolean>} actor
 * @param {boolean} [allowAccess]
 */
function setActorAccess(containerNode, actor, allowAccess) {
	var menuItem = containerNode.data('menu_item');

	//grant_access comes from PHP, which JSON-encodes empty assoc. arrays as arrays.
	//However, we want it to be a dictionary.
	if (!$.isPlainObject(menuItem.grant_access)) {
		menuItem.grant_access = {};
	}

	if (typeof actor === 'string') {
		menuItem.grant_access[actor] = Boolean(allowAccess);
	} else {
		_.assign(menuItem.grant_access, actor);
	}
}

/**
 * Make a menu item inaccessible to everyone except a particular actor.
 *
 * Will not change access settings for actors that are more specific than the input actor.
 * For example, if the input actor is a "role:", this function will only disable other roles,
 * but will leave "user:" actors untouched.
 *
 * @param {Object} menuItem
 * @param {String} actor
 * @return {Object}
 */
function denyAccessForAllExcept(menuItem, actor) {
	//grant_access comes from PHP, which JSON-encodes empty assoc. arrays as arrays.
	//However, we want it to be a dictionary.
	if (!$.isPlainObject(menuItem.grant_access)) {
		menuItem.grant_access = {};
	}

	$.each(wsEditorData.actors, function(otherActor) {
		//If the input actor is more or equally specific...
		if ((actor === null) || (AmeActorManager.compareActorSpecificity(actor, otherActor) >= 0)) {
			menuItem.grant_access[otherActor] = false;
		}
	});

	if (actor !== null) {
		menuItem.grant_access[actor] = true;
	}
	return menuItem;
}

/***************************************************************************
 Event handlers
 ***************************************************************************/

//Cut & paste stuff
var menu_in_clipboard = null;
var ws_paste_count = 0;

//Color preset stuff.
var colorPresets = {},
	wasPresetDropdownPopulated = false;

//General admin menu visibility.
var generalComponentVisibility = {};

//Combined DOM-ready event handler.
var isDomReadyDone = false;

function ameOnDomReady() {
	if (isDomReadyDone) {
		return;
	}
	isDomReadyDone = true;

	//Some editor elements are only available in the Pro version.
	if (wsEditorData.wsMenuEditorPro) {
		knownMenuFields.open_in.visible = true;
		knownMenuFields.access_level.visible = true;
		knownMenuFields.page_heading.visible = true;
		knownMenuFields.colors.visible = true;
		knownMenuFields.appearance_heading.visible = true;
		knownMenuFields.appearance_heading.onlyForTopMenus = false;
		knownMenuFields.extra_capability.visible = false; //Superseded by the "access_level" field.

		//The Pro version supports submenu icons, but they can be disabled by the user.
		knownMenuFields.icon_url.onlyForTopMenus = (wsEditorData.submenuIconsEnabled === 'never');

		//The Pro version has more submenu fields, so let's enable the separator below "CSS classes".
		//In the free version, the separator is hidden because there would only be a single field below it.
		knownMenuFields.page_properties_heading.onlyForTopMenus = false;

		$('.ws_hide_if_pro').hide();
	}

	//Let other plugins filter knownMenuFields and menu fields by type.
	$(document).trigger('filterMenuFields.adminMenuEditor', [knownMenuFields, baseField]);
	$(document).trigger('filterVisibleMenuFields.adminMenuEditor', [visibleMenuFieldsByType]);

	//Make the top menu box sortable (we only need to do this once)
    var mainMenuBox = $('#ws_menu_box');
    makeBoxSortable(mainMenuBox);

	/***************************************************************************
	                  Event handlers for editor widgets
	 ***************************************************************************/
	const menuEditorNode = $('#ws_menu_editor');

	menuPresenter = new AmeMenuPresenter(menuEditorNode, wsEditorData.deepNestingEnabled);

	/**
	 * Select a menu item and show its submenu.
	 *
	 * @param {JQuery|HTMLElement} container Menu container node.
	 */
	function selectItem(container) {
		menuPresenter.selectItem(container);
	}
	AmeEditorApi.selectItem = selectItem;

	//Select the clicked menu item and show its submenu
	menuEditorNode.on('click', '.ws_container', (function () {
		selectItem($(this));
    }));

	function updateSubmenuBoxHeight(selectedMenu) {
		//TODO: Eliminate this duplication. Maybe we could just call the corresponding column method.
		const myColumn = menuPresenter.getColumnImmediate(selectedMenu.closest('.ws_main_container').data('ame-menu-level') || 1);
		const nextColumn = menuPresenter.getColumnImmediate(myColumn.level + 1);
		if (!nextColumn || (nextColumn === myColumn)) {
			return;
		}
		let mainMenuBox = myColumn.menuBox,
			submenuBox = nextColumn.menuBox,
			submenuDropZone = nextColumn.container.find('.ws_dropzone').first();

		//Make the submenu box tall enough to reach the selected item.
		//This prevents the menu tip (if any) from floating in empty space.
		if (selectedMenu.hasClass('ws_menu_separator')) {
			submenuBox.css('min-height', '');
		} else {
			var menuTipHeight = 30,
				empiricalExtraHeight = 4,
				verticalBoxOffset = (submenuBox.offset().top - mainMenuBox.offset().top),
				minSubmenuHeight = (selectedMenu.offset().top - mainMenuBox.offset().top)
					- verticalBoxOffset
					+ menuTipHeight - (submenuDropZone.outerHeight() || 0) + empiricalExtraHeight;
			minSubmenuHeight = Math.max(minSubmenuHeight, 0);
			submenuBox.css('min-height', minSubmenuHeight);
		}
	}

	AmeEditorApi.updateSubmenuBoxHeight = updateSubmenuBoxHeight;

	//Show a notification icon next to the "Permissions" field when the menu item supports extended permissions.
	function updateExtPermissionsIndicator(container, menuItem) {
		var extPermissions = AmeItemAccessEditor.detectExtPermissions(AmeEditorApi.getItemDisplayUrl(menuItem)),
			fieldTitle = container.find('.ws_edit_field-access_level .ws_field_label_text'),
			indicator = fieldTitle.find('.ws_ext_permissions_indicator');

		if (wsEditorData.wsMenuEditorPro && (extPermissions !== null)) {
			if (indicator.length < 1) {
				indicator = $('<div class="dashicons dashicons-info ws_ext_permissions_indicator"></div>');
				fieldTitle.append(" ").append(indicator);
			}
			//Idea: Change the icon based on the kind of permissions available (post type, tags, etc).
			indicator.show().data('ext_permissions', extPermissions);
		} else {
			indicator.hide();
		}
	}

	menuEditorNode.on('adminMenuEditor:fieldChange', function(event, menuItem, fieldName) {
		if ((fieldName === 'template_id') || (fieldName === 'file')) {
			updateExtPermissionsIndicator($(event.target), menuItem);
		}
	});

	//Show/hide a menu's properties
	menuEditorNode.on('click', '.ws_edit_link', (function (event) {
		event.preventDefault();

		var container = $(this).parents('.ws_container').first();
		var box = container.find('.ws_editbox');

		//For performance, the property editors for each menu are only created
		//when the user tries to access access them for the first time.
		if ( !container.data('field_editors_created') ){
			var menuItem = container.data('menu_item');
			buildEditboxFields(box, menuItem, container.hasClass('ws_menu'));
			container.data('field_editors_created', true);
			updateItemEditor(container);
			updateExtPermissionsIndicator(container, menuItem);
		}

		$(this).toggleClass('ws_edit_link_expanded');
		//show/hide the editbox
		if ($(this).hasClass('ws_edit_link_expanded')){
			box.show();
		} else {
			//Make sure changes are applied before the menu is collapsed
			box.find('input').change();
			box.hide();
		}
    }));

    //The "Default" button : Reset to default value when clicked
    menuEditorNode.on('click', '.ws_reset_button', (function () {
        //Find the field div (it holds the field name)
        var field = $(this).parents('.ws_edit_field');
	    var fieldName = field.data('field_name');

		if ( (field.length > 0) && fieldName ) {
			//Extract the default value from the menu item.
            var containerNode = field.closest('.ws_container');
			var menuItem = containerNode.data('menu_item');

			if (fieldName === 'access_level') {
	            //This is a pretty nasty hack.
	            menuItem.grant_access = {};
	            menuItem.extra_capability = null;
				menuItem.restrict_access_to_items = false;
				delete menuItem.had_access_before_hiding;
            }

			if (itemTemplates.hasDefaultValue(menuItem.template_id, fieldName)) {
				menuItem[fieldName] = null;
				updateItemEditor(containerNode);
				updateParentAccessUi(containerNode);
			}
		}
	}));

	//When a field is edited, change it's appearance if it's contents don't match the default value.
    function fieldValueChange(){
	    /* jshint validthis:true */
        var input = $(this);
		var field = input.parents('.ws_edit_field').first();
	    var fieldName = field.data('field_name');

        if ((fieldName === 'access_level') || (fieldName === 'embedded_page_id')) {
            //These fields are read-only and can never be directly edited by the user.
            //Ignore spurious change events.
            return;
        }

	    var containerNode = field.parents('.ws_container').first();
	    var menuItem = containerNode.data('menu_item');

	    var oldValue = menuItem[fieldName];
	    var oldDisplayValue = $.data(this, 'ame_last_display_value');
	    var value = getInputValue(input);
	    var defaultValue = getDefaultValue(menuItem, fieldName, null, containerNode);
        var hasADefaultValue = (defaultValue !== null);

	    //Some fields/templates have no default values.
        field.toggleClass('ws_has_no_default', !hasADefaultValue);
        if (!hasADefaultValue) {
            field.removeClass('ws_input_default');
        }

        // noinspection EqualityComparisonWithCoercionJS It's been like this so long that I'm afraid to change it.
	    if (field.hasClass('ws_input_default') && (value == defaultValue)) {
            value = null; //null = use default.
        }

	    //Ignore changes where the new value is the same as the old one.
	    if ((value === oldValue) || (value === oldDisplayValue)) {
		    return;
	    }

	    //Update the item.
	    if (knownMenuFields[fieldName].write !== null) {
			// phpcs:ignore WordPressVIPMinimum.JS.HTMLExecutingFunctions.write -- Misdetected. Not document.write().
		    knownMenuFields[fieldName].write(menuItem, value, input, containerNode);
	    } else {
		    menuItem[fieldName] = value;
	    }

	    updateItemEditor(containerNode);
	    updateParentAccessUi(containerNode);

	    containerNode.trigger('adminMenuEditor:fieldChange', [menuItem, fieldName]);
    }
	menuEditorNode.on('click change', '.ws_field_value', fieldValueChange);

	//Show/hide advanced fields
	menuEditorNode.on('click', '.ws_toggle_advanced_fields', function(){
		var self = $(this);
		var advancedFields = self.parents('.ws_container').first().find('.ws_advanced');

		if ( advancedFields.is(':visible') ){
			advancedFields.hide();
			self.text(wsEditorData.captionShowAdvanced);
		} else {
			advancedFields.show();
			self.text(wsEditorData.captionHideAdvanced);
		}

		return false;
	});

	//Allow/forbid items in actor-specific views
	menuEditorNode.on('click', 'input.ws_actor_access_checkbox', function() {
		if (actorSelectorWidget.selectedActor === null) {
			return;
		}

		var checked = $(this).is(':checked');
		var containerNode = $(this).closest('.ws_container');

		var menu = containerNode.data('menu_item');
		//Ask for confirmation if the user tries to hide Dashboard -> Home.
		if ( !checked && ((menu.template_id === 'index.php>index.php') || (menu.template_id === '>index.php')) ) {
			updateItemEditor(containerNode); //Resets the checkbox back to the old value.
			confirmDashboardHiding(function(ok) {
				if (ok) {
					setActorAccessForTreeAndUpdateUi(containerNode, actorSelectorWidget.selectedActor, checked);
				}
			});
		} else {
			setActorAccessForTreeAndUpdateUi(containerNode, actorSelectorWidget.selectedActor, checked);
		}
	});

	/**
	 * This confusingly named function sets actor access for the specified menu item
	 * and all of its children (if any). It also updates the UI with the new settings.
	 *
	 * (And it violates SRP in a particularly egregious manner.)
	 *
	 * @param containerNode
	 * @param {String|Object.<String, Boolean>} actor
	 * @param {Boolean} [allowAccess]
	 * @param {Boolean} [skipParentUiRefresh] Whether to skip updating the parent access UI. Defaults to false.
	 */
	function setActorAccessForTreeAndUpdateUi(containerNode, actor, allowAccess, skipParentUiRefresh) {
		setActorAccess(containerNode, actor, allowAccess);

		//Apply the same permissions to sub-menus.
		const subMenuId = containerNode.data('submenu_id');
		if (subMenuId) {
			$('.ws_item', '#' + subMenuId).each(function() {
				const node = $(this);
				setActorAccessForTreeAndUpdateUi(node, actor, allowAccess, true);
			});
		}

		updateItemEditor(containerNode);
		updateActorAccessUi(containerNode);

		if ( !skipParentUiRefresh ) {
			updateParentAccessUi(containerNode);
		}
	}

	/**
	 * Insert a new top level menu after the selected menu or at the end of the list.
	 *
	 * @param {Object} menu
	 */
	function insertMenu(menu) {
		const selection = (typeof getSelectedMenu !== 'undefined') ? getSelectedMenu() : null;
		if (selection && (selection.length > 0) ) {
			outputTopMenu(menu, selection);
		} else {
			outputTopMenu(menu);
		}
	}
	AmeEditorApi.insertMenu = insertMenu;

	/**
	 * Confirm with the user that they want to hide "Dashboard -> Home".
	 *
	 * This particular menu is important because hiding it can cause an "insufficient permissions" error
	 * to be displayed right when someone logs in, making it look like login failed.
	 */
	var permissionConfirmationDialog = $('#ws-ame-dashboard-hide-confirmation').dialog({
		autoOpen: false,
		modal: true,
		closeText: ' ',
		width: 380,
		title: 'Warning'
	});
	var currentConfirmationCallback = function(ok) {};

	/**
	 * Confirm hiding "Dashboard -> Home".
	 *
	 * @param callback Called when the user selects an option. True = confirmed.
	 */
	function confirmDashboardHiding(callback) {
		//The user can disable the confirmation dialog.
		if (!wsEditorData.dashboardHidingConfirmationEnabled) {
			callback(true);
			return;
		}

		currentConfirmationCallback = callback;
		permissionConfirmationDialog.dialog('open');
	}

	$('#ws_confirm_menu_hiding, #ws_cancel_menu_hiding').on('click', function() {
		var confirmed = $(this).is('#ws_confirm_menu_hiding');
		var dontShowAgain = permissionConfirmationDialog.find('.ws_dont_show_again input[type="checkbox"]').is(':checked');

		currentConfirmationCallback(confirmed);
		permissionConfirmationDialog.dialog('close');

		if (dontShowAgain) {
			wsEditorData.dashboardHidingConfirmationEnabled = false;
			//Run an AJAX request to disable the dialog for this user.
			$.post(
				wsEditorData.adminAjaxUrl,
				{
					'action' : 'ws_ame_disable_dashboard_hiding_confirmation',
					'_ajax_nonce' : wsEditorData.disableDashboardConfirmationNonce
				}
			);
		}
	});


	/*************************************************************************
	                  Access editor dialog
	 *************************************************************************/

	AmeItemAccessEditor.setup({
		api: AmeEditorApi,
		actorSelector: actorSelectorWidget,
		postTypes: wsEditorData.postTypes,
		taxonomies: wsEditorData.taxonomies,
		lodash: _,
		isPro: wsEditorData.wsMenuEditorPro,

		save: function(menuItem, containerNode, settings) {
			//Save the new settings.
			menuItem.extra_capability         = settings.extraCapability;
			menuItem.grant_access             = settings.grantAccess;
			menuItem.restrict_access_to_items = settings.restrictAccessToItems;

			//Save granted capabilities.
			var newlyDisabledCaps = {};
			_.forEach(settings.grantedCapabilities, function(capabilities, actor) {
				_.forEach(capabilities, function(grant, capability) {
					if (!_.isArray(grant)) {
						grant = [grant, null, null];
					}

					AmeCapabilityManager.setCap(actor, capability, grant[0], grant[1], grant[2]);

					if (!grant[0]) {
						if (!newlyDisabledCaps.hasOwnProperty(capability)) {
							newlyDisabledCaps[capability] = [];
						}
						newlyDisabledCaps[capability].push(actor);
					}
				});
			});

			AmeEditorApi.forEachMenuItem(function(menuItem, containerNode) {
				//When the user unchecks a capability, uncheck ALL menu items associated with that capability.
				//Anything less won't actually get rid of the capability as enabled menus auto-grant req. caps.
				var requiredCap = getFieldValue(menuItem, 'access_level');
				if (newlyDisabledCaps.hasOwnProperty(requiredCap)) {
					//It's enough to remove custom "allow" settings. The rest happens automatically - items that
					//have no custom per-role settings use capability checks.
					_.forEach(newlyDisabledCaps[requiredCap], function(actor) {
						if (_.get(menuItem.grant_access, actor) === true) {
							delete menuItem.grant_access[actor];
						}
					});
				}

				//Due to changed caps and cascading submenu overrides, changes to one item's permissions
				//can affect other items. Lets just update all items.
				updateActorAccessUi(containerNode);
			});

			//Refresh the UI.
			updateItemEditor(containerNode);
		}
	});

	menuEditorNode.on('click', '.ws_launch_access_editor', function() {
		var containerNode = $(this).parents('.ws_container').first();
		var menuItem = containerNode.data('menu_item');

		AmeItemAccessEditor.open({
			menuItem: menuItem,
			containerNode: containerNode,
			selectedActor: actorSelectorWidget.selectedActor,
			itemHasSubmenus: (!!(containerNode.data('submenu_id')) &&
				$('#' + containerNode.data('submenu_id')).find('.ws_item').length > 0)
		});
	});

	/***************************************************************************
		              General dialog handlers
	 ***************************************************************************/

	$(document).on('click', '.ws_close_dialog', function() {
		$(this).parents('.ui-dialog-content').dialog('close');
	});


	/***************************************************************************
	              Drop-down list for combo-box fields
	 ***************************************************************************/

	var capSelectorDropdown = $('#ws_cap_selector');
	var currentDropdownOwner = null; //The input element that the dropdown is currently associated with.
	var currentDropdownOwnerMenu = null; //The menu item that the above input belongs to.

	var isDropdownBeingHidden = false, isSuggestionClick = false;

	const $extraCapInAccessEditor = $('#ws_extra_capability');

	//Show/hide the capability drop-down list when the trigger button is clicked
	$('#ws_trigger_capability_dropdown').on('mousedown click', onDropdownTriggerClicked);
	menuEditorNode.on('mousedown click', '.ws_cap_selector_trigger', onDropdownTriggerClicked);

	function onDropdownTriggerClicked(event){
		/* jshint validthis:true */
		var inputBox;
		var button = $(this);

		var isInAccessEditor = false;
		isSuggestionClick = false;

		//Find the input associated with the button that was clicked.
		if ( button.attr('id') === 'ws_trigger_capability_dropdown' ) {
			inputBox = $extraCapInAccessEditor;
			isInAccessEditor = true;
		} else {
			inputBox = button.closest('.ws_edit_field').find('.ws_field_value').first();
		}

		//If the user clicks the same button again while the dropdown is already visible,
		//ignore the click. The dropdown will be hidden by its "blur" handler.
		if (event.type === 'mousedown') {
			if ( capSelectorDropdown.is(':visible') && inputBox.is(currentDropdownOwner) ) {
				isDropdownBeingHidden = true;
			}
			return;
		} else if (isDropdownBeingHidden) {
			isDropdownBeingHidden = false; //Ignore the click event.
			return;
		}

		//A jQuery UI dialog widget will prevent focus from leaving the dialog. So if we want
		//the dropdown to be properly focused when displaying it in a dialog, we must make it
		//a child of the dialog's DOM node (and vice versa when it's not in a dialog).
		var parentContainer = $(this).closest('.ui-dialog, #ws_menu_editor');
		if ((parentContainer.length > 0) && (capSelectorDropdown.closest(parentContainer).length === 0)) {
			var oldHeight = capSelectorDropdown.height(); //Height seems to reset when moving to a new parent.
			capSelectorDropdown.detach().appendTo(parentContainer).height(oldHeight);
		}

		//Pre-select the current capability (will clear selection if there's no match).
		capSelectorDropdown.val(inputBox.val()).show();

		//Move the drop-down near the input box.
		var inputPos = inputBox.offset();
		capSelectorDropdown
			.css({
				position: 'absolute',
				zIndex: 1010 //Must be higher than the permissions dialog overlay.
			})
			.offset({
				left: inputPos.left,
				top : inputPos.top + inputBox.outerHeight()
			}).
			width(inputBox.outerWidth());

		currentDropdownOwner = inputBox;

		currentDropdownOwnerMenu = null;
		if (isInAccessEditor) {
			currentDropdownOwnerMenu = AmeItemAccessEditor.getCurrentMenuItem();
		} else {
			currentDropdownOwnerMenu = currentDropdownOwner.closest('.ws_container').data('menu_item');
		}

		capSelectorDropdown.focus();

		capSuggestionFeature.show();
	}

	//Also show it when the user presses the down arrow in the input field (doesn't work in Opera).
	$extraCapInAccessEditor.bind('keyup', function(event){
		if ( event.which === 40 ){
			$('#ws_trigger_capability_dropdown').trigger('click');
		}
	});

	function hideCapSelector() {
		capSelectorDropdown.hide();
		capSuggestionFeature.hide();
		isSuggestionClick = false;
	}

	//Event handlers for the drop-down lists themselves
	var dropdownNodes = $('.ws_dropdown');

	// Hide capability drop-down when it loses focus.
	dropdownNodes.on('blur', function(){
		if (!isSuggestionClick) {
			hideCapSelector();
		}
	});

	dropdownNodes.on('keydown', function(event){

		//Hide it when the user presses Esc
		if ( event.which === 27 ){
			hideCapSelector();
			if (currentDropdownOwner) {
				currentDropdownOwner.focus();
			}

		//Select an item & hide the list when the user presses Enter or Tab
		} else if ( (event.which === 13) || (event.which === 9) ){
			hideCapSelector();

			if (currentDropdownOwner) {
				if ( capSelectorDropdown.val() ){
					currentDropdownOwner.val(capSelectorDropdown.val()).change();
				}
				currentDropdownOwner.focus();
			}

			event.preventDefault();
		}
	});

	//Eat Tab keys to prevent focus theft. Required to make the "select item on Tab" thing work.
	dropdownNodes.on('keyup', function(event){
		if ( event.which === 9 ){
			event.preventDefault();
		}
	});


	//Update the input & hide the list when an option is clicked
	dropdownNodes.on('click', function(){
		if (capSelectorDropdown.val()){
			hideCapSelector();
			if (currentDropdownOwner) {
				currentDropdownOwner.val(capSelectorDropdown.val()).change().focus();
			}
		}
	});

	//Highlight an option when the user mouses over it (doesn't work in IE)
	dropdownNodes.on('mousemove', function(event){
		if ( !event.target ){
			return;
		}

		var option = event.target;
		if ( (typeof option.selected !== 'undefined') && !option.selected && option.value ){
			option.selected = true;

			//Preview which roles have this capability and the required cap.
			capSuggestionFeature.previewAccessForItem(currentDropdownOwnerMenu, option.value);
		}
	});

	/************************************************************************
	 *                     Capability suggestions
	 *************************************************************************/

	var capSuggestionFeature = (function() {
		//This feature is not used in the Pro version because it has a different permission UI.
		if (wsEditorData.wsMenuEditorPro) {
			return {
				previewAccessForItem: function () {},
				show: function () {},
				hide: function () {}
			}
		}

		var capabilitySuggestions = $('#ws_capability_suggestions'),
			suggestionBody = capabilitySuggestions.find('table tbody').first().empty(),
			suggestedCapabilities = AmeActors.getSuggestedCapabilities();

		for (var i = 0; i < suggestedCapabilities.length; i++) {
			var role = suggestedCapabilities[i].role, capability = suggestedCapabilities[i].capability;
			$('<tr>')
				.data('role', role)
				.data('capability', capability)
				.append(
					$('<th>', {text: role.displayName, scope: 'row'}).addClass('ws_ame_role_name')
				)
				.append(
					$('<td>', {text: capability}).addClass('ws_ame_suggested_capability')
				)
				.appendTo(suggestionBody);
		}

		var currentPreviewedCaps = null;

		/**
		 * Update the access preview.
		 * @param {string|string[]|null} capabilities
		 */
		function previewAccess(capabilities) {
			if (typeof capabilities === 'string') {
				capabilities = [capabilities];
			}

			if (_.isEqual(capabilities, currentPreviewedCaps)) {
				return;
			}
			currentPreviewedCaps = capabilities;
			capabilitySuggestions.find('#ws_previewed_caps').text(currentPreviewedCaps.join(' + '));

			//Short-circuit the no-caps case.
			if (capabilities === null || capabilities.length === 0) {
				suggestionBody.find('tr').removeClass('ws_preview_has_access');
				return;
			}

			suggestionBody.find('tr').each(function() {
				var $row = $(this),
					role = $row.data('role');

				var hasCaps = true;
				for (var i = 0; i < capabilities.length; i++) {
					hasCaps = hasCaps && AmeActors.hasCap(role.id, capabilities[i]);
				}
				$row.toggleClass('ws_preview_has_access', hasCaps);
			});
		}

		function previewAccessForItem(menuItem, selectedExtraCap) {
			var requiredCap = '', extraCap = '';

			if (menuItem) {
				requiredCap = getFieldValue(menuItem, 'access_level', '');
				extraCap = getFieldValue(menuItem, 'extra_capability', '');
			}
			if (typeof selectedExtraCap !== 'undefined') {
				extraCap = selectedExtraCap;
			}

			var caps = [];
			if (menuItem && (menuItem.template_id !== '') || (extraCap === '')) {
				caps.push(requiredCap);
			}
			if (extraCap !== '') {
				caps.push(extraCap);
			}

			previewAccess(caps);
		}

		suggestionBody.on('mouseenter', 'td.ws_ame_suggested_capability', function() {
			var row = $(this).closest('tr');
			previewAccessForItem(currentDropdownOwnerMenu, row.data('capability'));
		});

		capSelectorDropdown.on('keydown keyup', function() {
			previewAccessForItem(currentDropdownOwnerMenu, capSelectorDropdown.val());
		});

		suggestionBody.on('mousedown', 'td.ws_ame_suggested_capability', function() {
			//Don't immediately hide the list when the user tries to click a suggestion.
			//It would prevent the click from registering.
			isSuggestionClick = true;
		});

		suggestionBody.on('click', 'td.ws_ame_suggested_capability', function() {
			var capability = $(this).closest('tr').data('capability');

			//Change the input to the selected capability.
			if (currentDropdownOwner) {
				currentDropdownOwner.val(capability).change();
			}

			hideCapSelector();
		});

		//Workaround for pressing LMB on a suggestion, then moving the mouse outside the suggestion box and releasing the button.
		$(document).on('click', function(event) {
			if (
				isSuggestionClick
				&& capabilitySuggestions.is(':visible')
				&& ( $(event.target).closest(capabilitySuggestions).length < 1 )
			) {
				hideCapSelector();
			}
		});

		return {
			previewAccessForItem: previewAccessForItem,
			show: function() {
				//Position the capability suggestion table next to the selector and match heights.
				capabilitySuggestions
					.css({
						position: 'absolute',
						zIndex: 1009
					})
					.show()
					.position({
						my: 'left top',
						at: 'right top',
						of: capSelectorDropdown,
						collision: 'none'
					});

				var selectorHeight = capSelectorDropdown.height(),
					suggestionsHeight = capabilitySuggestions.height(),
					desiredHeight = Math.max(selectorHeight, suggestionsHeight);
				if (selectorHeight < desiredHeight) {
					capSelectorDropdown.height(desiredHeight);
				}
				if (suggestionsHeight < desiredHeight) {
					capabilitySuggestions.height(desiredHeight);
				}

				if (currentDropdownOwnerMenu) {
					previewAccessForItem(currentDropdownOwnerMenu);
				}
			},
			hide: function() {
				capabilitySuggestions.hide();
			}
		};
	})();


	/*************************************************************************
	                           Icon selector
	 *************************************************************************/
	var iconSelector = $('#ws_icon_selector');
	var currentIconButton = null; //Keep track of the last clicked icon button.

	var iconSelectorTabs = iconSelector.find('#ws_icon_source_tabs');
	iconSelectorTabs.tabs();

	//When the user clicks one of the available icons, update the menu item.
	iconSelector.on('click', '.ws_icon_option', function() {
		var selectedIcon = $(this).addClass('ws_selected_icon');
		iconSelector.hide();

		//Assign the selected icon to the menu.
		if (currentIconButton) {
			var container = currentIconButton.closest('.ws_container');
			var item = container.data('menu_item');

			//Remove the existing icon class, if any.
			var cssClass = getFieldValue(item, 'css_class', '');
			cssClass = jsTrim( cssClass.replace(/\b(ame-)?menu-icon-[^\s]+\b/, '') );

			if (selectedIcon.data('icon-class')) {
				//Add the new class.
				cssClass = selectedIcon.data('icon-class') + ' ' + cssClass;
				//Can't have both a class and an image or we'll get two overlapping icons.
				item.icon_url = '';
			} else if (selectedIcon.data('icon-url')) {
				item.icon_url = selectedIcon.data('icon-url');
			}
			item.css_class = cssClass;

			updateItemEditor(container);
		}

		currentIconButton = null;
	});

	//Show/hide the icon selector when the user clicks the icon button.
	menuEditorNode.on('click', '.ws_select_icon', function() {
		var button = $(this);
		//Clicking the same button a second time hides the icon list.
		if ( currentIconButton && button.is(currentIconButton) ) {
			iconSelector.hide();
			//noinspection JSUnusedAssignment
			currentIconButton = null;
			return;
		}

		currentIconButton = button;

		var containerNode = currentIconButton.closest('.ws_container');
		var menuItem = containerNode.data('menu_item');
		var cssClass = getFieldValue(menuItem, 'css_class', '');
		var iconUrl = getFieldValue(menuItem, 'icon_url', '', containerNode);

		//Clear the search box and restore icons that were hidden by a previous search.
		const $searchBoxes = iconSelector.find('.ws_icon_search_box');
		$searchBoxes.each(function() {
			const $this = $(this);
			if ($this.val() !== '') {
				$this.val('');
				//Let's call the search handler directly instead of using $.trigger('keyup').
				//The event handler is throttled and might not run until later.
				searchMenuIcons('', $this.closest('.ws_tool_tab'));
			}
		});

		var customImageOption = iconSelector.find('.ws_custom_image_icon').hide();
		iconSelector.data('ame-item-has-custom-image', false);

		//Highlight the currently selected icon.
		iconSelector.find('.ws_selected_icon').removeClass('ws_selected_icon');

		var selectedIcon = null;
		var classMatches = cssClass.match(/\b(ame-)?menu-icon-([^\s]+)\b/);
		//Dashicons and FontAwesome icons are set via the icon URL field, but they are actually CSS-based.
		var iconFontMatches = iconUrl && iconUrl.match('^\s*((?:dashicons|ame-fa)-[a-z0-9\-]+)\s*$');

		if ( iconUrl && iconUrl !== 'none' && iconUrl !== 'div' && !iconFontMatches ) {
			var currentIcon = iconSelector.find('.ws_icon_option img[src="' + iconUrl + '"]').first().closest('.ws_icon_option');
			if ( currentIcon.length > 0 ) {
				selectedIcon = currentIcon.addClass('ws_selected_icon').show();
			} else {
				//Display and highlight the custom image.
				customImageOption.find('img').prop('src', iconUrl);
				customImageOption.addClass('ws_selected_icon').show().data('icon-url', iconUrl);
				iconSelector.data('ame-item-has-custom-image', true);
				selectedIcon = customImageOption;
			}
		} else if ( classMatches || iconFontMatches ) {
			//Highlight the icon that corresponds to the current CSS class or Dashicon/FontAwesome icon.
			var iconClass = iconFontMatches ? iconFontMatches[1] : ((classMatches[1] ? classMatches[1] : '') + 'icon-' + classMatches[2]);
			selectedIcon = iconSelector.find('.' + iconClass).closest('.ws_icon_option').addClass('ws_selected_icon');
		}

		//Activate the tab that contains the icon.
		var activeTabId = ((selectedIcon !== null)
				? selectedIcon.closest('.ws_tool_tab').prop('id')
				: 'ws_core_icons_tab'),
			activeTabItem = iconSelectorTabs.find('a[href="#' + activeTabId + '"]').closest('li');
		if (activeTabItem.length > 0) {
			iconSelectorTabs.tabs('option', 'active', activeTabItem.index());
		}

		//Before showing the selector, clear the fixed height that was set when it was last visible.
		iconSelector.css('height', '');

		iconSelector.show();

		//Set a fixed height while the selector is visible. This prevents the selector's
		//height from changing when the user filters the icon list.
		const initialHeight = iconSelector.height();
		iconSelector.css('height', initialHeight);

		iconSelector.position({ //Requires jQuery UI.
			my: 'left top',
			at: 'left bottom',
			of: button
		});
	});

	//Alternatively, use the WordPress media uploader to select a custom icon.
	//This code is based on the header selection script in /wp-admin/js/custom-header.js.
	var mediaFrame = null;
	$('#ws_choose_icon_from_media').on('click', function(event) {
		event.preventDefault();

		//This option is not usable on the demo site since the filesystem is usually read-only.
		if (wsEditorData.isDemoMode) {
			alert('Sorry, image upload is disabled in demo mode!');
			return;
		}

        //If the media frame already exists, reopen it.
        if ( mediaFrame !== null ) {
            mediaFrame.open();
            return;
        }

        //Create a custom media frame.
        mediaFrame = wp.media.frames.customAdminMenuIcon = wp.media({
            //Set the title of the modal.
            title: 'Choose a Custom Icon (20x20)',

            //Tell it to show only images.
            library: {
                type: 'image'
            },

            //Customize the submit button.
            button: {
                text: 'Set as icon', //Button text.
                close: true //Clicking the button closes the frame.
            }
        });

        //When an image is selected, set it as the menu icon.
        mediaFrame.on( 'select', function() {
            //Grab the selected attachment.
            var attachment = mediaFrame.state().get('selection').first();
            //TODO: Warn the user if the image exceeds 20x20 pixels.

	        //Set the menu icon to the attachment URL.
            if (currentIconButton) {
                var container = currentIconButton.closest('.ws_container');
                var item = container.data('menu_item');

                //Remove the existing icon class, if any.
                var cssClass = getFieldValue(item, 'css_class', '');
	            item.css_class = jsTrim( cssClass.replace(/\b(ame-)?menu-icon-[^\s]+\b/, '') );

	            //Set the new icon URL.
	            item.icon_url = attachment.attributes.url;

                updateItemEditor(container);
            }

            currentIconButton = null;
        });

		//If the user closes the frame by via Esc or the "X" button, clear up state.
		mediaFrame.on('escape', function(){
			currentIconButton = null;
		});

        mediaFrame.open();
		iconSelector.hide();
	});

	//Hide the icon selector if the user clicks outside of it.
	//Exception: Clicks on "Select icon" buttons are handled above.
	$(document).on('mouseup', function(event) {
		if ( !iconSelector.is(':visible') ) {
			return;
		}

		if (
			!iconSelector.is(event.target)
			&& iconSelector.has(event.target).length === 0
			&& $(event.target).closest('.ws_select_icon').length === 0
		) {
			iconSelector.hide();
			currentIconButton = null;
		}
	});

	//Provide search-as-you-type functionality for the icon selector.
	function searchMenuIcons(query, $currentTab) {
		let $searchableItems = $currentTab.find('.ws_icon_option');
		//If the current menu item doesn't have a custom image, exclude the custom image
		//option from the search results.
		if (!iconSelector.data('ame-item-has-custom-image')) {
			$searchableItems = $searchableItems.not('.ws_custom_image_icon');
		}

		let foundAnything = false;

		$searchableItems.each(function() {
			const $icon = $(this);
			const name = $icon.prop('title').toLowerCase();

			if (name.includes(query)) {
				$icon.show();
				foundAnything = true;
			} else {
				$icon.hide();
			}
		});

		$currentTab.find('.ws_no_matching_icons').toggle(!foundAnything);
	}

	iconSelectorTabs.find('.ws_icon_search_box').on('keyup', _.throttle(
		function() {
			const $inputField = $(this);
			const $tab = $inputField.closest('.ws_tool_tab');

			searchMenuIcons($inputField.val().toLowerCase().trim(), $tab);
		},
		250
	));

	/*************************************************************************
	                        Embedded page selector
	 *************************************************************************/

	var pageSelector = $('#ws_embedded_page_selector'),
		pageListBox = pageSelector.find('#ws_current_site_pages'),
		currentPageSelectorButton = null, //The last page dropdown button that was clicked.
		isPageListPopulated = false,
		isPageRequestInProgress = false;

	pageSelector.tabs({
		heightStyle: 'auto',
		hide: false,
		show: false
	});
	//Hack. The selector needs to be hidden by default, but it can't start out as "display: none" because that makes
	//jQuery miscalculate tab heights. So we put it in a hidden container, then hide it on load and move it elsewhere.
	pageSelector.hide().appendTo(menuEditorNode);

	/**
	 * Update the page selector with the current menu item's settings.
	 */
	function updatePageSelector() {
		var menuItem, selectedPageId = 0, selectedBlogId = 1;
		if ( currentPageSelectorButton ) {
			menuItem = currentPageSelectorButton.closest('.ws_container').data('menu_item');
			selectedPageId = parseInt(getFieldValue(menuItem, 'embedded_page_id', 0), 10);
			selectedBlogId = parseInt(getFieldValue(menuItem, 'embedded_page_blog_id', 1), 10);
		}

		if (selectedPageId === 0) {
			pageListBox.val(null);
		} else {
			var optionValue = selectedBlogId + '_' + selectedPageId;
			pageListBox.val(optionValue);
			if ( pageListBox.val() !== optionValue ) {
				pageListBox.val('custom');
			}
		}

		pageSelector.find('#ws_embedded_page_id').val(selectedPageId);
		pageSelector.find('#ws_embedded_page_blog_id').val(selectedBlogId);
	}

	menuEditorNode.on('click', '.ws_embedded_page_selector_trigger', function(event) {
		var thisButton = $(this),
			thisInput = thisButton.closest('.ws_edit_field').find('input.ws_field_value:first');

		//Clicking the same button a second time hides the page selector.
		if (thisButton.is(currentPageSelectorButton) && pageSelector.is(':visible')) {
			pageSelector.hide();
			//noinspection JSUnusedAssignment
			currentPageSelectorButton = null;
			return;
		}

		currentPageSelectorButton = thisButton;
		pageSelector.show();
		pageSelector.position({
			my: 'left top',
			at: 'left bottom',
			of: thisInput
		});

		event.stopPropagation();

		if (!isPageListPopulated && !isPageRequestInProgress) {
			isPageRequestInProgress = true;

			var pageList = pageSelector.find('#ws_current_site_pages');
			pageList.prop('readonly', true);

			$.getJSON(
				wsEditorData.adminAjaxUrl,
				{
					'action' : 'ws_ame_get_pages',
					'_ajax_nonce' : wsEditorData.getPagesNonce
				},
				function(data){
					isPageRequestInProgress = false;
					pageList.prop('readonly', false);

					if (typeof data.error !== 'undefined'){
						alert(data.error);
						return;
					} else if ((typeof data !== 'object') || (typeof data.length === 'undefined')) {
						alert('Error: Could not retrieve a list of pages. Unexpected response from the server.');
						return;
					}

					//An alphabetised list is easier to scan visually.
					var pages = data.sort(function(a, b) {
						return a.post_title.localeCompare(b.post_title);
					});

					//Populate the select box.
					pageList.empty();
					$.each(pages, function(index, page) {
						pageList.append($('<option>', {
							val: page.blog_id + '_' + page.post_id,
							text: page.post_title
						}));
					});

					//Add a "custom" option. Select it when the current setting doesn't match any of the listed pages.
					pageList.prepend($('<option>', {
						val: 'custom',
						text: '< Custom >'
					}));

					updatePageSelector();
					isPageListPopulated = true;
				},
				'json'
			);

		}

		updatePageSelector();

		//Open the "Pages" tab by default, or the "Custom" tab if that's what's selected in the list box.
		//The updatePageSelector call above sets the pageListBox value.
		pageSelector.tabs('option', 'active', (pageListBox.val() === 'custom') ? 1 : 0);
	});

	//Hide the page selector if the user clicks outside of it and outside the current button.
	$(document).on('mouseup', function(event) {
		if ( !pageSelector.is(':visible') ) {
			return;
		}

		var target = $(event.target);
		var isOutsideSelector = target.closest(pageSelector).length === 0;
		var isOutsideButton = currentPageSelectorButton && (target.closest(currentPageSelectorButton).length === 0);

		if (isOutsideSelector && isOutsideButton) {
			pageSelector.hide();
			currentPageSelectorButton = null;
		}
	});

	function setEmbeddedPageForCurrentItem(newPageId, newBlogId, title) {
		if ( currentPageSelectorButton ) {
			var containerNode = currentPageSelectorButton.closest('.ws_container'),
				menuItem = containerNode.data('menu_item');

			menuItem.embedded_page_id = newPageId;
			menuItem.embedded_page_blog_id = newBlogId;

			if (typeof title === 'string') {
				//Store the page title for later. It will be displayed in the text box.
				AmePageTitles.add(newPageId, newBlogId, title);
			}

			updateItemEditor(containerNode);
		}
	}

	//When the user chooses a page from the list, update the menu item and hide the dropdown.
	pageListBox.on('change', function() {
		var selection = pageListBox.val();
		if (selection === 'custom') { // jshint ignore:line
			//Do nothing. Presumably, the user will now switch to the "Custom" tab and enter new settings.
			//If they don't do that and just close the dropdown, we keep the previous settings.
		} else if ( currentPageSelectorButton ) {
			//Set the new page and blog IDs. The expected value format is "blogid_postid".
			var parts = selection.split('_'),
				newBlogId = parseInt(parts[0], 10),
				newPageId = parseInt(parts[1], 10);

			pageSelector.hide();
			setEmbeddedPageForCurrentItem(newPageId, newBlogId, pageListBox.children(':selected').text());
		}
	});

	pageSelector.find('#ws_custom_embedded_page_tab form').on('submit', function(event) {
		event.preventDefault();

		var newPageId = parseInt(pageSelector.find('#ws_embedded_page_id').val(), 10),
			newBlogId = parseInt(pageSelector.find('#ws_embedded_page_blog_id').val(), 10);

		if (isNaN(newPageId) || (newPageId < 0)) {
			alert('Error: Invalid post ID');
		} else if (isNaN(newBlogId) || (newBlogId < 0)) {
			alert('Error: Invalid blog ID');
		} else if ( currentPageSelectorButton ) {
			pageSelector.hide();
			setEmbeddedPageForCurrentItem(newPageId, newBlogId);
		}
	});

	/*************************************************************************
	                  Unsaved changes indicator
	 *************************************************************************/

	/**
	 * @param {JQuery} $rootNode
	 * @constructor
	 */
	function AmeUnsavedChangesIndicator($rootNode) {
		this.rootNode = $rootNode;
		this.reportedUnsavedChanges = 0;

		$(document)
			.on('adminMenuEditor:menuConfigChanged', () => {
				this.reportedUnsavedChanges++;
				this.update();
			})
			.on('menuConfigurationLoaded.adminMenuEditor', () => {
				this.reportedUnsavedChanges = 0;
				this.update();
			});
	}

	AmeUnsavedChangesIndicator.prototype.update = function() {
		const hasUnsavedChanges = this.reportedUnsavedChanges > 0;
		this.rootNode.toggleClass('ws_ame_has_unsaved_changes', hasUnsavedChanges);

		const $saveButton = this.rootNode.find('#ws_save_menu');
		if (hasUnsavedChanges) {
			$saveButton.attr('title', 'Click to save pending changes');
		} else {
			$saveButton.attr('title', '');
		}
	};

	new AmeUnsavedChangesIndicator(menuEditorNode);

//region Toolbar buttons

    /*************************************************************************
	                           Menu toolbar buttons
	 *************************************************************************/
    function getSelectedMenu() {
	    return menuPresenter.getColumnImmediate(1).getSelectedItem();
    }
    AmeEditorApi.getSelectedMenu = getSelectedMenu;

	//Show/Hide menu
	menuEditorNode.on(
		'adminMenuEditor:action-hide',
		/**
		 * @param event
		 * @param {JQuery|null} selectedItem
		 * @param {AmeEditorColumn} column
		 */
		function(event, selectedItem, column) {
			const selection = column.getSelectedItem();
			if (selection.length < 1) {
				return;
			}

			toggleItemHiddenFlag(selection);
		}
	);

	//Hide a menu and deny access.
	menuEditorNode.on(
		'adminMenuEditor:action-deny',
		/**
		 * @param event
		 * @param {JQuery|null} selectedItem
		 * @param {AmeEditorColumn} column
		 */
		function(event, selectedItem, column) {
			const selection = column.getSelectedItem();
			if (selection.length < 1) {
				return;
			}

			function objectFillKeys(keys, value) {
				let result = {};
				_.forEach(keys, function(key) {
					result[key] = value;
				});
				return result;
			}

			if (actorSelectorWidget.selectedActor === null) {
				//Hide from everyone except Super Admin and the current user.
				let menuItem = selection.data('menu_item'),
					validActors = _.keys(wsEditorData.actors),
					alwaysAllowedActors = _.intersection(
						['special:super_admin', 'user:' + wsEditorData.currentUserLogin],
						validActors
					),
					victims = _.difference(validActors, alwaysAllowedActors),
					shouldHide;

				//First, let's check who has access. Maybe this item is already hidden from the victims.
				shouldHide = _.some(victims, _.curry(actorCanAccessMenu, 2)(menuItem));

				let keepEnabled = objectFillKeys(alwaysAllowedActors, true),
					hideAllExceptAllowed = _.assign(objectFillKeys(victims, false), keepEnabled);

				walkMenuTree(selection, function(container, item) {
					let newAccess;
					if (shouldHide) {
						//Yay, hide it now!
						newAccess = hideAllExceptAllowed;
						//Only update had_access_before_hiding if this item isn't hidden yet or the field is missing.
						//We don't want to double-hide an item.
						let actorsWithAccess = _.filter(victims, function(actor) {
							return actorCanAccessMenu(item, actor);
						});
						if ((actorsWithAccess.length) > 0 || _.isEmpty(_.get(item, 'had_access_before_hiding', null))) {
							item.had_access_before_hiding = actorsWithAccess;
						}
					} else {
						//Give back access to the roles and users who previously had access.
						//Careful, don't give access to roles that no longer exist.
						let actorsWhoHadAccess = _.get(item, 'had_access_before_hiding', []) || [];
						actorsWhoHadAccess = _.intersection(actorsWhoHadAccess, validActors);

						newAccess = _.assign(objectFillKeys(actorsWhoHadAccess, true), keepEnabled);
						delete item.had_access_before_hiding;
					}

					setActorAccess(container, newAccess);
					updateItemEditor(container);
				});

			} else {
				//Just toggle the checkbox.
				selection.find('input.ws_actor_access_checkbox').trigger('click');
			}
		}
	);

	//Delete error dialog. It shows up when the user tries to delete one of the default menus.
	var menuDeletionDialog = $('#ws-ame-menu-deletion-error').dialog({
		autoOpen: false,
		modal: true,
		closeText: ' ',
		title: 'Error',
		draggable: false
	});
	var menuDeletionCallback = function(hide) {
		menuDeletionDialog.dialog('close');
		var selection = menuDeletionDialog.data('selected_menu');

		function applyCallbackRecursively(containerNode, callback) {
			callback(containerNode.data('menu_item'));

			var subMenuId = containerNode.data('submenu_id');
			if (subMenuId && containerNode.hasClass('ws_menu')) {
				$('.ws_item', '#' + subMenuId).each(function() {
					var node = $(this);
					callback(node.data('menu_item'));
					updateItemEditor(node);
				});
			}

			updateItemEditor(containerNode);
		}

		function hideRecursively(containerNode, exceptActor) {
			var otherActors = _(actorSelectorWidget.getVisibleActors())
				.map('id')
				.without(exceptActor)
				.value();

			applyCallbackRecursively(containerNode, function(menuItem) {
				//Remember which actors had access to this item so that it
				//can be un-hidden by the toolbar button.
				var actorsWithAccess = _.filter(otherActors, function(actor) {
					return actorCanAccessMenu(menuItem, actor);
				});
				if ((actorsWithAccess.length) > 0) {
					menuItem.had_access_before_hiding = actorsWithAccess;
				}

				denyAccessForAllExcept(menuItem, exceptActor);
			});
			updateParentAccessUi(containerNode);
		}

		//TODO: Write had_access_before_hiding so that it can be un-hidden using the toolbar button.
		if (hide === 'all') {
			if (wsEditorData.wsMenuEditorPro) {
				hideRecursively(selection, null);
			} else {
				//The free version doesn't have role permissions, so use the global "hidden" flag.
				applyCallbackRecursively(selection, function(menuItem) {
					menuItem.hidden = true;
				});
			}
		} else if (hide === 'except_current_user') {
			hideRecursively(selection, 'user:' + wsEditorData.currentUserLogin);
		} else if (hide === 'except_administrator' && !wsEditorData.wsMenuEditorPro) {
			//Set "required capability" to something only the Administrator role would have.
			var adminOnlyCap = 'manage_options';
			applyCallbackRecursively(selection, function(menuItem) {
				menuItem.extra_capability = adminOnlyCap;
			});
			alert('The "required capability" field was set to "' + adminOnlyCap + '".');
		}
	};

	//Callbacks for each of the dialog buttons.
	$('#ws_cancel_menu_deletion').on('click', function() {
		menuDeletionCallback(false);
	});
	$('#ws_hide_menu_from_everyone').on('click', function() {
		menuDeletionCallback('all');
	});
	const $hideExceptCurrentUser = $('#ws_hide_menu_except_current_user').on('click', function() {
		menuDeletionCallback('except_current_user');
	});
	const $hideExceptAdmin = $('#ws_hide_menu_except_administrator').on('click', function() {
		menuDeletionCallback('except_administrator');
	});

	/**
	 * Attempt to delete a menu item. Will check if the item can actually be deleted and ask the user for confirmation.
	 * UI callback.
	 *
	 * @param {JQuery} selection The selected menu item (DOM node).
	 */
	function tryDeleteItem(selection) {
		var menuItem = selection.data('menu_item');
		var shouldDelete = false;

		if (canDeleteItem(selection)) {
			//Custom and duplicate items can be deleted normally.
			shouldDelete = confirm('Delete this menu?');
		} else {
			//Non-custom items can not be deleted, but they can be hidden. Ask the user if they want to do that.
			menuDeletionDialog.find('#ws-ame-menu-type-desc').text(
				getDefaultValue(menuItem, 'is_plugin_page') ? 'an item added by another plugin' : 'a built-in menu item'
			);
			menuDeletionDialog.data('selected_menu', selection);

			//Different versions get slightly different options because only the Pro version has
			//role-specific permissions.
			$hideExceptCurrentUser.toggleClass('hidden', !wsEditorData.wsMenuEditorPro);
			$hideExceptAdmin.toggleClass('hidden', wsEditorData.wsMenuEditorPro);

			menuDeletionDialog.dialog('open');

			//Select "Cancel" as the default button.
			menuDeletionDialog.find('#ws_cancel_menu_deletion').focus();
		}

		if (shouldDelete) {
			const parentSubmenu = selection.closest('.ws_submenu');

			//Delete the menu.
			menuPresenter.destroyItem(selection);

			if (parentSubmenu && (parentSubmenu.length > 0)) {
				//Refresh permissions UI for this menu's parent (if any).
				updateParentAccessUi(parentSubmenu);
			}
		}
	}

	//Delete menu
	menuEditorNode.on(
		'adminMenuEditor:action-delete',
		/**
		 * @param event
		 * @param {JQuery|null} selectedItem
		 * @param {AmeEditorColumn} column
		 */
		function(event, selectedItem, column) {
			const selection = column.getSelectedItem();
			if (selection.length < 1) {
				return;
			}

			tryDeleteItem(selection);
		}
	);

	//Copy menu
	menuEditorNode.on(
		'adminMenuEditor:action-copy',

		/**
		 * @param event
		 * @param {JQuery|null} selectedItem
		 */
		function (event, selectedItem) {
			//Get the selected menu
			if (!selectedItem || (selectedItem.lengt < 1)) {
				return;
			}

			//Store a copy of the current menu state in clipboard
			menu_in_clipboard = readItemState(selectedItem);
		}
	);

	//Cut menu
	menuEditorNode.on(
		'adminMenuEditor:action-cut',

		/**
		 * @param event
		 * @param {JQuery|null} selectedItem
		 * @param {AmeEditorColumn} column
		 */
		function (event, selectedItem, column) {
			if (selectedItem === null) {
				alert('Please select a menu item first.');
				return;
			}
			const submenu = selectedItem.closest('.ws_submenu');

			//Store a copy of the current menu state in clipboard
			menu_in_clipboard = readItemState(selectedItem);

			//Remove the original menu and submenu
			column.destroyItem(selectedItem);

			//If this submenu had mixed permissions, that might have changed now that the item is gone.
			updateParentAccessUi(submenu);
		}
	);

	menuEditorNode.on(
		'adminMenuEditor:action-paste',
		/**
		 * @param event
		 * @param {JQuery|null} selectedItem
		 * @param {AmeEditorColumn} column
		 */
		function(event, selectedItem, column) {
			//Check if anything has been copied/cut
			if (!menu_in_clipboard) {
				return;
			}

			//You can only add separators to submenus in the Pro version.
			if ( menu_in_clipboard.separator && !wsEditorData.wsMenuEditorPro ) {
				return;
			}

			const copyOfItem = $.extend(true, {}, menu_in_clipboard);

			//Paste the menu after the selection.
			column.pasteItem(copyOfItem, selectedItem);
		}
	);

	//New menu
	menuEditorNode.on(
		'adminMenuEditor:action-new-menu',
		/**
		 * @param event
		 * @param {JQuery|null} selectedItem
		 * @param {AmeEditorColumn} column
		 */
		function(event, selectedItem, column) {
			const visibleList = column.getVisibleItemList();
			if (!visibleList || (visibleList.length < 1)) {
				//Abort if there's no item list in this column. This can happen if nothing is selected
				//in the previous column.
				return;
			}

			ws_paste_count++;

			//The new menu starts out rather bare.
			let item = $.extend(true, {}, wsEditorData.blankMenuItem, {
				custom: true, //Important : flag the new menu as custom, or it won't show up after saving.
				template_id: '',
				menu_title: 'Custom Menu ' + ws_paste_count,
				file: randomMenuId(),
				items: []
			});
			item.defaults = $.extend(true, {}, itemTemplates.getDefaults(''));

			//Top-level menus automatically get the "menu-top" class.
			if (column.level <= 1) {
				item['css_class'] = 'menu-top';
			}

			//Make it accessible only to the current actor if one is selected.
			if (actorSelectorWidget.selectedActor !== null) {
				denyAccessForAllExcept(item, actorSelectorWidget.selectedActor);
			}

			//Insert the new menu item.
			let selection = column.getSelectedItem();
			if (!selection || (selection.length < 1)) {
				selection = null;
			}
			let result = column.outputItem(item, selection);

			if (result && result.menu) {
				//The menu's editbox is always open
				result.menu.find('.ws_edit_link').trigger('click');

				updateParentAccessUi(result.menu);
			}
		}
	);

	//New separator
	menuEditorNode.on(
		'adminMenuEditor:action-new-separator',
		/**
		 * @param event
		 * @param {JQuery|null} selectedItem
		 * @param {AmeEditorColumn} column
		 */
		function(event, selectedItem, column) {
			const visibleList = column.getVisibleItemList();
			if (!visibleList || (visibleList.length < 1)) {
				//Abort if there's no item list in this column. This can happen if nothing is selected
				//in the previous column.
				return;
			}

			ws_paste_count++;

			const randomId = randomMenuId('separator_');
			let item = $.extend(true, {}, wsEditorData.blankMenuItem, {
				separator: true, //Flag as a separator
				custom: false,   //Separators don't need to flagged as custom to be retained.
				items: [],
				defaults: {
					separator: true,
					css_class : 'wp-menu-separator',
					access_level : 'read',
					file : randomId,
					hookname : randomId
				}
			});

			const selection = column.getSelectedItem();
			column.outputItem(item, (selection.length > 0) ? selection : null);
		}
	);

	//Toggle all menus for the currently selected actor
	menuEditorNode.on(
		'adminMenuEditor:action-toggle-all',
		function() {
			if ( actorSelectorWidget.selectedActor === null ) {
				alert("This button enables/disables all menus for the selected role. To use it, click a role and then click this button again.");
				return;
			}

			//Look at the first menu's permissions and set everything to the opposite.
			const firstColumn = menuPresenter.getColumnImmediate(1);
			const topMenuNodes = $('.ws_menu', firstColumn.getVisibleItemList());

			const allow = ! actorCanAccessMenu(topMenuNodes.eq(0).data('menu_item'), actorSelectorWidget.selectedActor);

			topMenuNodes.each(function() {
				let containerNode = $(this);
				setActorAccessForTreeAndUpdateUi(containerNode, actorSelectorWidget.selectedActor, allow);
			});
		}
	);

	//Copy all menu permissions from one role to another.
	var copyPermissionsDialog = $('#ws-ame-copy-permissions-dialog').dialog({
		autoOpen: false,
		modal: true,
		closeText: ' ',
		draggable: false
	});

	var sourceActorList = $('#ame-copy-source-actor'), destinationActorList = $('#ame-copy-destination-actor');

	//The "Copy permissions" toolbar button.
	menuEditorNode.on(
		'adminMenuEditor:action-copy-permissions',
		function() {
			const previousSource = sourceActorList.val();

			//Populate source/destination lists.
			sourceActorList.find('option').not('[disabled]').remove();
			destinationActorList.find('option').not('[disabled]').remove();
			$.each(actorSelectorWidget.getVisibleActors(), function(index, actor) {
				let option = $('<option>', {
					val: actor.id,
					text: actorSelectorWidget.getNiceName(actor)
				});
				sourceActorList.append(option);
				destinationActorList.append(option.clone());
			});

			//Pre-select the current actor as the destination.
			if (actorSelectorWidget.selectedActor !== null) {
				destinationActorList.val(actorSelectorWidget.selectedActor);
			}

			//Restore the previous source selection.
			if (previousSource) {
				sourceActorList.val(previousSource);
			}
			if (!sourceActorList.val()) {
				sourceActorList.find('option').first().prop('selected', true); //Fallback.
			}

			copyPermissionsDialog.dialog('open');
		}
	);

	//Actually copy the permissions when the user click the confirmation button.
	var copyConfirmationButton = $('#ws-ame-confirm-copy-permissions');
	copyConfirmationButton.on('click', function() {
		var sourceActor = sourceActorList.val();
		var destinationActor = destinationActorList.val();

		if (sourceActor === null || destinationActor === null) {
			alert('Select a source and a destination first.');
			return;
		}

		//Iterate over all menu items and copy the permissions from one actor to the other.
		AmeEditorApi.forEachMenuItem(function (menuItem, node) {
			//Only change permissions when they don't match. This ensures we won't unnecessarily overwrite default
			//permissions and bloat the configuration with extra grant_access entries.
			const sourceAccess      = actorCanAccessMenu(menuItem, sourceActor);
			const destinationAccess = actorCanAccessMenu(menuItem, destinationActor);
			if (sourceAccess !== destinationAccess) {
				setActorAccess(node, destinationActor, sourceAccess);
				//Note: In theory, we could also look at the default permissions for destinationActor and
				//revert to default instead of overwriting if that would make the two actors' permissions match.
			}
		});

		//todo: copy granted permissions like CPTs.

		//If the user is currently looking at the destination actor, force the UI to refresh
		//so that they can see the new permissions.
		if (actorSelectorWidget.selectedActor === destinationActor) {
			//This is a bit of a hack, but right now there's no better way to refresh all items at once.
			actorSelectorWidget.setSelectedActor(null);
			actorSelectorWidget.setSelectedActor(destinationActor);
		}

		//All done.
		copyPermissionsDialog.dialog('close');
	});

	//Only enable the copy button when the user selects a valid source and destination.
	copyConfirmationButton.prop('disabled', true);
	sourceActorList.add(destinationActorList).on('click', function() {
		var sourceActor = sourceActorList.val();
		var destinationActor = destinationActorList.val();

		var validInputs = (sourceActor !== null) && (destinationActor !== null) && (sourceActor !== destinationActor);
		copyConfirmationButton.prop('disabled', !validInputs);
	});

	//Sort menus in ascending or descending order.
	menuEditorNode.on(
		'adminMenuEditor:action-sort',
		/**
		 * @param event
		 * @param {JQuery|null} selectedItem
		 * @param {AmeEditorColumn} column
		 * @param {JQuery} button
		 */
		function(event, selectedItem, column, button) {
			let direction = button.data('sort-direction') || 'asc',
				menuBox = column.getVisibleItemList();

			if (!menuBox || (menuBox.length < 1)) {
				return;
			}

			function sortRecursively($box, currentColumn) {
				//When indirectly sorting the second menu level (regular submenus), leave the first item unmoved.
				//Moving the first item would change the parent menu URL (WP always links it to the first item),
				//which can be unexpected and confusing. The user can always move the first item manually.
				let leaveFirstItem = ((currentColumn !== column) && (currentColumn.level === 2));
				sortMenuItems($box, direction, leaveFirstItem);

				//Also sort child items in the next columns.
				const nextColumn = menuPresenter.getColumnImmediate(currentColumn.level + 1);
				if (nextColumn) {
					$box.find('.ws_container').each(function () {
						const $submenu = getSubmenuOf($(this), null);
						if ($submenu) {
							sortRecursively($submenu, nextColumn);
						}
					});
				}
			}

			sortRecursively(menuBox, column);
		}
	);

	/**
	 * Sort menu items by title.
	 *
	 * @param $menuBox A DOM node that contains multiple menu items.
	 * @param {string} direction 'asc' or 'desc'
	 * @param {boolean} [leaveFirstItem] Leave the first item in its original position. Defaults to false.
	 */
	function sortMenuItems($menuBox, direction, leaveFirstItem) {
		var multiplier = (direction === 'desc') ? -1 : 1,
			items = $menuBox.find('.ws_container'),
			firstItem = items.first();

		//Separators don't have a title, but we don't want them to end up at the top of the list.
		//Instead, lets keep their position the same relative to the previous item.
		var prevItemTitle = '';
		items.each((function(){
			var item = $(this), sortValue;
			if (item.is('.ws_menu_separator')) {
				sortValue = prevItemTitle;
			} else {
				sortValue = jsTrim(item.find('.ws_item_title').text());
				prevItemTitle = sortValue;
			}
			item.data('ame-sort-value', sortValue);
		}));

		function compareMenus(a, b){
			var aTitle = $(a).data('ame-sort-value'),
				bTitle = $(b).data('ame-sort-value');

			aTitle = aTitle.toLowerCase();
			bTitle = bTitle.toLowerCase();

			if (aTitle > bTitle) {
				return multiplier;
			} else if (aTitle < bTitle) {
				return -multiplier;
			}
			return 0;
		}

		items.sort(compareMenus);

		if (leaveFirstItem) {
			//Move the first item back to the top.
			firstItem.prependTo($menuBox);
		}
	}

	//Toggle the second row of toolbar buttons.
	menuEditorNode.on(
		'adminMenuEditor:action-toggle-toolbar',
		function() {
			let visible = menuEditorNode.find('.ws_second_toolbar_row').toggle().is(':visible');
			if (typeof $['cookie'] !== 'undefined') {
				$.cookie('ame-show-second-toolbar', visible ? '1' : '0', {expires: 90});
			}
		}
	);


	/*************************************************************************
	                          Item toolbar buttons
	 *************************************************************************/
	function getSelectedSubmenuItem() {
		return menuPresenter.getColumnImmediate(2).getSelectedItem();
	}

	//endregion

	//==============================================
	//				Main buttons
	//==============================================

	//Save Changes - encode the current menu as JSON and save
	$('#ws_save_menu').on('click', function () {
		try {
			var tree = readMenuTreeState();
		} catch (error) {
			//Right now the only known error condition is duplicate top level URLs.
			if (error.hasOwnProperty('code') && (error.code === 'duplicate_top_level_url')) {
				var message = 'Error: Duplicate menu URLs. The following top level menus have the same URL:\n\n' ;
				for (var i = 0; i < error.duplicates.length; i++) {
					var containerNode = $(error.duplicates[i]);
					message += (i + 1) + '. ' + containerNode.find('.ws_item_title').first().text() + '\n';
				}
				message += '\nPlease change the URLs to be unique or delete the duplicates.';
				alert(message);
			} else {
				alert(error.message);
			}
			return;
		}

		function findItemByTemplateId(items, templateId) {
			var foundItem = null;

			$.each(items, function(index, item) {
				if (item.template_id === templateId) {
					foundItem = item;
					return false;
				}
				if (item.hasOwnProperty('items') && (item.items.length > 0)) {
					foundItem = findItemByTemplateId(item.items, templateId);
					if (foundItem !== null) {
						return false;
					}
				}
				return true;
			});

			return foundItem;
		}

		//Abort the save if it would make the editor inaccessible.
        if (wsEditorData.wsMenuEditorPro) {
            var myMenuItem = findItemByTemplateId(tree.tree, 'options-general.php>menu_editor');
            if (myMenuItem === null) { // jshint ignore:line
                //This is OK - the missing menu item will be re-inserted automatically.
            } else if (!actorCanAccessMenu(myMenuItem, 'user:' + wsEditorData.currentUserLogin)) {
                alert(
	                "Error: This configuration would make you unable to access the menu editor!\n\n" +
	                "Please click either your role name or \"Current user (" + wsEditorData.currentUserLogin + ")\" "+
	                "and enable the \"Menu Editor Pro\" menu item."
                );
                return;
            }
        }

		var data = encodeMenuAsJSON(tree);
		$('#ws_data').val(data);
		$('#ws_data_length').val(data.length);
		$('#ws_selected_actor').val(actorSelectorWidget.selectedActor === null ? '' : actorSelectorWidget.selectedActor);

		$('#ws_is_deep_nesting_enabled').val(JSON.stringify(menuPresenter.isDeepNestingEnabled));

		var selectedMenu = getSelectedMenu();
		if (selectedMenu.length > 0) {
			$('#ws_selected_menu_url').val(AmeEditorApi.getItemDisplayUrl(selectedMenu.data('menu_item')));
			$('#ws_expand_selected_menu').val(selectedMenu.find('.ws_editbox').is(':visible') ? '1' : '');

			var selectedSubmenu = getSelectedSubmenuItem();
			if (selectedSubmenu.length > 0) {
				$('#ws_selected_submenu_url').val(AmeEditorApi.getItemDisplayUrl(selectedSubmenu.data('menu_item')));
				$('#ws_expand_selected_submenu').val(selectedSubmenu.find('.ws_editbox').is(':visible') ? '1' : '');
			}
		}

		$('#ws_main_form').trigger('submit');
	});

	//Load default menu - load the default WordPress menu
	$('#ws_load_menu').on('click', function () {
		if (confirm('Are you sure you want to load the default WordPress menu?')){
			loadMenuConfiguration(defaultMenu);
		}
	});

	//Reset menu - re-load the custom menu. Discards any changes made by user.
	$('#ws_reset_menu').on('click', function () {
		if (confirm('Undo all changes made in the current editing session?')){
			loadMenuConfiguration(customMenu);
		}
	});

	//Enable the "load default menu" and "undo changes" buttons only when "All" is selected.
	//Otherwise some users incorrectly assume these buttons only affect the currently selected role or user.
	actorSelectorWidget.onChange(function (newSelectedActor) {
		$('#ws_load_menu, #ws_reset_menu').prop('disabled', newSelectedActor !== null);
	});
	$('#ws_load_menu, #ws_reset_menu').prop('disabled', actorSelectorWidget.selectedActor !== null);

	$('#ws_toggle_editor_layout').on('click', function () {
		var isCompactLayoutEnabled = menuEditorNode.toggleClass('ws_compact_layout').hasClass('ws_compact_layout');
		if (typeof $['cookie'] !== 'undefined') {
			$.cookie('ame-compact-layout', isCompactLayoutEnabled ? '1' : '0', {expires: 90});
		}

		var button = $(this);
		if (button.is('input')) {
			var checkMark = '\u2713';
			button.val(button.val().replace(checkMark, ''));
			if (isCompactLayoutEnabled) {
				button.val(checkMark + ' ' + button.val());
			}
		}
	});

	//Export menu - download the current menu as a file
	$('#export_dialog').dialog({
		autoOpen: false,
		closeText: ' ',
		modal: true,
		minHeight: 100
	});

	$('#ws_export_menu').on('click', function(){
		var button = $(this);
		button.prop('disabled', true);
		button.val('Exporting...');

		$('#export_complete_notice, #download_menu_button').hide();
		$('#export_progress_notice').show();
		var exportDialog = $('#export_dialog');
		exportDialog.dialog('open');

		//Encode the menu.
		try {
			var exportData = encodeMenuAsJSON();
		} catch (error) {
			exportDialog.dialog('close');
			alert(error.message);

			button.val('Export');
			button.prop('disabled', false);
			return;
		}

		//Store the menu for download.
		$.post(
			wsEditorData.adminAjaxUrl,
			{
				'data' : exportData,
				'action' : 'export_custom_menu',
				'_ajax_nonce' : wsEditorData.exportMenuNonce
			},
			/**
			 * @param {Object} data
			 */
			function(data){
				button.val('Export');
				button.prop('disabled', false);

				if ( typeof data.error !== 'undefined' ){
					exportDialog.dialog('close');
					alert(data.error);
				}

				if ( _.has(data, 'download_url') ){
					//window.location = data.download_url;
					$('#download_menu_button').attr('href', _.get(data, 'download_url')).data('filesize', _.get(data, 'filesize'));
					$('#export_progress_notice').hide();
					$('#export_complete_notice, #download_menu_button').show();
				}
			},
			'json'
		);
	});

	$('#ws_cancel_export').on('click', function(){
		$('#export_dialog').dialog('close');
	});

	$('#download_menu_button').on('click', function(){
		$('#export_dialog').dialog('close');
	});

	//Import menu - upload an exported menu and show it in the editor
	$('#import_dialog').dialog({
		autoOpen: false,
		closeText: ' ',
		modal: true
	});
	const $importMenuForm = $('#import_menu_form');

	$('#ws_cancel_import').on('click', function(){
		$('#import_dialog').dialog('close');
	});

	$('#ws_import_menu').on('click', function(){
		$('#import_progress_notice, #import_progress_notice2, #import_complete_notice, #ws_import_error').hide();
		$('#ws_import_panel').show();
		$importMenuForm.resetForm();
		//The "Upload" button is disabled until the user selects a file
		$('#ws_start_import').attr('disabled', 'disabled');

		var importDialog = $('#import_dialog');
		importDialog.find('.hide-when-uploading').show();
		importDialog.dialog('open');
	});

	$('#import_file_selector').on('change', function(){
		$('#ws_start_import').prop('disabled', ! $(this).val() );
	});

	//This function displays unhandled server side errors. In theory, our upload handler always returns a well-formed
	//response even if there's an error. In practice, stuff can go wrong in unexpected ways (e.g. plugin conflicts).
	function handleUnexpectedImportError(xhr, errorMessage) {
		//The server-side code didn't catch this error, so it's probably something serious
		//and retrying won't work.
		$importMenuForm.resetForm();
		$('#ws_import_panel').hide();

		//Display error information.
		$('#ws_import_error_message').text(errorMessage);
		$('#ws_import_error_http_code').text(xhr.status);
		$('#ws_import_error_response').text((xhr.responseText !== '') ? xhr.responseText : '[Empty response]');
		$('#ws_import_error').show();
	}

	//AJAXify the upload form
	$importMenuForm.ajaxForm({
		dataType : 'json',
		beforeSubmit: function(formData) {

			//Check if the user has selected a file
			for(var i = 0; i < formData.length; i++){
				if ( formData[i].name === 'menu' ){
					if ( (typeof formData[i].value === 'undefined') || !formData[i].value){
						alert('Select a file first!');
						return false;
					}
				}
			}

			$('#import_dialog').find('.hide-when-uploading').hide();
			$('#import_progress_notice').show();

			$('#ws_start_import').attr('disabled', 'disabled');
			return true;
		},
		success: function(data, status, xhr) {
			$('#import_progress_notice').hide();

			var importDialog = $('#import_dialog');
			if ( !importDialog.dialog('isOpen') ){
				//Whoops, the user closed the dialog while the upload was in progress.
				//Discard the response silently.
				return;
			}

			if ( data === null ) {
				handleUnexpectedImportError(xhr, 'Invalid response from server. Please check your PHP error log.');
				return;
			}

			if ( typeof data.error !== 'undefined' ){
				alert(data.error);
				//Let the user try again
				$importMenuForm.resetForm();
				importDialog.find('.hide-when-uploading').show();
			}

			if ( (typeof data.tree !== 'undefined') && data.tree ){
				//Whee, we got back a (seemingly) valid menu. A veritable miracle!
				//Lets load it into the editor.
				var progressNotice = $('#import_progress_notice2').show();
				loadMenuConfiguration(data);
				progressNotice.hide();
				//Display a success notice, then automatically close the window after a few moments
				$('#import_complete_notice').show();
				setTimeout((function(){
					//Close the import dialog
					$('#import_dialog').dialog('close');
				}), 500);
			}

		},
		error: function(xhr, status, errorMessage) {
			handleUnexpectedImportError(xhr, errorMessage);
		}
	});

	/*************************************************************************
	                 Drag & drop items between menu levels
	 *************************************************************************/

	//Allow the user to drag sub-menu items to the top level.
	$('#ws_top_menu_dropzone').droppable({
		'hoverClass' : 'ws_dropzone_hover',
		'activeClass' : 'ws_dropzone_active',

		'accept' : (function(thing){
			return thing.hasClass('ws_item');
		}),

		'drop' : (function(event, ui){
			const firstColumn = menuPresenter.getColumnImmediate(1);
			if (!firstColumn) {
				return;
			}
			const nextColumn = menuPresenter.getColumnImmediate(firstColumn.level + 1);

			const droppedItemData = readItemState(ui.draggable);
			const newItemNodes = firstColumn.pasteItem(droppedItemData, null);

			//If the item was originally a top level menu, also move its original submenu items.
			if ((getFieldValue(droppedItemData, 'parent') === null) && (newItemNodes.submenu)) {
				const droppedItemFile = getFieldValue(droppedItemData, 'file');
				const nearbyItems = $(ui.draggable).siblings('.ws_item');
				nearbyItems.each(function() {
					const containerNode = $(this),
						submenuItem = containerNode.data('menu_item');

					//Was this item originally a child of the dragged menu?
					if (getFieldValue(submenuItem, 'parent') === droppedItemFile) {
						nextColumn.pasteItem(submenuItem, null, newItemNodes.submenu);
						if ( !event.ctrlKey ) {
							menuPresenter.destroyItem(containerNode);
						}
					}
				});
			}

			if ( !event.ctrlKey ) {
				menuPresenter.destroyItem(ui.draggable);
			}
		})
	});

	/******************************************************************
	                 Component visibility settings
	 ******************************************************************/

	var $generalVisBox = $('#ws_ame_general_vis_box'),
		$showAdminMenu = $('#ws_ame_show_admin_menu'),
		$showWpToolbar = $('#ws_ame_show_toolbar');

	AmeEditorApi.actorCanSeeComponent = function(component, actorId) {
		if (actorId === null) {
			return _.some(actorSelectorWidget.getVisibleActors(), function(actor) {
				return AmeEditorApi.actorCanSeeComponent(component, actor.id);
			});
		}

		var actorSpecificSetting = _.get(generalComponentVisibility, [component, actorId], null);
		if (actorSpecificSetting !== null) {
			return actorSpecificSetting;
		}

		//Super Admin can see everything by default.
		if (actorId === AmeSuperAdmin.permanentActorId) {
			return _.get(generalComponentVisibility, [component, AmeSuperAdmin.permanentActorId], true);
		}

		var actor = AmeActors.getActor(actorId);
		if (actor instanceof AmeUser) {
			var grants = _.get(generalComponentVisibility, component, {});

			//Super Admin has priority.
			if (actor.isSuperAdmin) {
				return AmeEditorApi.actorCanSeeComponent(component, AmeSuperAdmin.permanentActorId);
			}

			//The user can see the admin menu/Toolbar if at least one of their roles can see it.
			var result = null;
			_.forEach(actor.roles, function(roleName) {
				var allow = _.get(grants, 'role:' + roleName, true);
				if (result === null) {
					result = allow;
				} else {
					result = result || allow;
				}
			});

			if (result !== null) {
				return result;
			}
		}

		//Everyone can see the admin menu and the Toolbar by default.
		return true;
	};

	AmeEditorApi.refreshComponentVisibility = function() {
		if ($generalVisBox.length < 1) {
			return;
		}

		var actorId = actorSelectorWidget.selectedActor;
		$showAdminMenu.prop('checked', AmeEditorApi.actorCanSeeComponent('adminMenu', actorId));
		$showWpToolbar.prop('checked', AmeEditorApi.actorCanSeeComponent('toolbar', actorId));
	};

	AmeEditorApi.setComponentVisibility = function(section, actorId, enabled) {
		if (actorId === null) {
			_.forEach(actorSelectorWidget.getVisibleActors(), function(actor) {
				_.set(generalComponentVisibility, [section, actor.id], enabled);
			});
		} else {
			_.set(generalComponentVisibility, [section, actorId], enabled);
		}
	};

	if ($generalVisBox.length > 0) {
		$showAdminMenu.on('click', function() {
			AmeEditorApi.setComponentVisibility(
				'adminMenu',
				actorSelectorWidget.selectedActor,
				$(this).is(':checked')
			);
		});
		$showWpToolbar.on('click', function () {
			AmeEditorApi.setComponentVisibility(
				'toolbar',
				actorSelectorWidget.selectedActor,
				$(this).is(':checked')
			);
		});

		$generalVisBox.find('.handlediv').on('click', function() {
			$generalVisBox.toggleClass('closed');
			if (typeof $['cookie'] !== 'undefined') {
				$.cookie(
					'ame_vis_box_open',
					($generalVisBox.hasClass('closed') ? '0' : '1'),
					{ expires: 90 }
				);
			}
		});

		actorSelectorWidget.onChange(function() {
			AmeEditorApi.refreshComponentVisibility();
		});
	}

	//region Aux menu data adapter
	/******************************************************************
	                     Auxiliary menu data adapter
	 ******************************************************************/

	/**
	 * Provides read/write access to additional arbitrary data that can be stored
	 * in the admin menu configuration (i.e. everything that's not the menu tree).
	 *
	 * @constructor
	 */
	class AuxiliaryConfigDataAdapter {
		currentConfig = {};
		registeredKeys = {};
		settingIdMap = {};
		prefixMap = {};

		constructor(adapterConfig = {}) {
			adapterConfig = adapterConfig || {};
			const initialPrefixes = _.get(adapterConfig, 'prefixMap', {});
			//Convert dot-separated paths like "a.b.c" to arrays.
			this.prefixMap = _.mapValues(initialPrefixes, (value) => {
				return _.isString(value) ? value.split('.') : value;
			});

			const initialKeys = _.get(adapterConfig, 'keys', {});
			for (const key in initialKeys) {
				this.registerKey(key, initialKeys[key]);
			}
			const initialSettingIdMap = _.get(adapterConfig, 'settingIdMap', {});
			for (const key in initialSettingIdMap) {
				this.registerSettingId(key, initialSettingIdMap[key]);
			}

			$(document)
				.on('menuConfigurationLoaded.adminMenuEditor', (event, menuConfiguration) => {
					//To avoid accidentally modifying the original config, make
					//a copy of each key except "tree" and "format".
					let configCopy = {};
					for (const key in menuConfiguration) {
						if (!menuConfiguration.hasOwnProperty(key) || (key === 'tree') || (key === 'format')) {
							continue;
						}

						if ((typeof menuConfiguration[key] === 'object') && (menuConfiguration[key] !== null)) {
							configCopy[key] = $.extend(true, {}, menuConfiguration[key]);
						} else {
							configCopy[key] = menuConfiguration[key];
						}

					}
					this.currentConfig = configCopy;
				})
				.on('getMenuConfiguration.adminMenuEditor', (event, menuConfiguration) => {
					//Copy registered settings to the menu configuration.
					for (let key in this.registeredKeys) {
						if (
							!this.registeredKeys.hasOwnProperty(key)
							|| (key === 'tree')
							|| (key === 'format')
						) {
							continue;
						}

						//Don't overwrite keys added by other scripts/event callbacks.
						if (typeof menuConfiguration[key] !== 'undefined') {
							continue;
						}

						if ((typeof this.currentConfig[key] !== 'undefined') && (this.currentConfig[key] !== null)) {
							const newValue = this.currentConfig[key];
							if (typeof newValue === 'object') {
								menuConfiguration[key] = $.extend(true, {}, newValue);
							} else {
								menuConfiguration[key] = newValue;
							}
						} else {
							delete menuConfiguration[key];
						}
					}
				});
		}

		/**
		 * Register a key on the menu configuration object. It will be preserved
		 * when the menu configuration is saved.
		 *
		 * @param {string} key
		 * @param {string|null} settingIdPrefix
		 */
		registerKey(key, settingIdPrefix = null) {
			this.registeredKeys[key] = true;
			this.prefixMap[settingIdPrefix] = [key];
		}

		/**
		 * Register a setting ID that corresponds to a specific path in the menu configuration object.
		 * You will be able to use the setting ID to read and write the corresponding value.
		 *
		 * @param {string} settingId
		 * @param {string|string[]} path
		 */
		registerSettingId(settingId, path) {
			this.settingIdMap[settingId] = path;
		}

		/**
		 * Get a list of all setting prefixes that this adapter may be able to handle.
		 *
		 * @returns {string[]}
		 */
		getKnownPrefixes() {
			return Object.keys(this.prefixMap);
		}

		getSettingValue(settingId, defaultValue = null) {
			const path = this.mapSettingIdToPath(settingId);
			if (path === null) {
				return defaultValue;
			}
			return _.get(this.currentConfig, path, defaultValue);
		}

		/**
		 * @param {string} settingId
		 * @returns {null|string|string[]}
		 */
		mapSettingIdToPath(settingId) {
			const knownPath = this.settingIdMap[settingId];
			if (typeof knownPath !== 'undefined') {
				return knownPath;
			}

			//Does this ID start with a known prefix?
			for (const prefix in this.prefixMap) {
				if (!this.prefixMap.hasOwnProperty(prefix)) {
					continue;
				}

				if (settingId.indexOf(prefix) === 0) {
					const suffix = settingId.substring(prefix.length);
					//Strip leading dots and convert to an array.
					const suffixPath = suffix.replace(/^\.+/, '').split('.');
					//Combine the prefix path with the suffix path.
					return this.prefixMap[prefix].concat(suffixPath);
				}
			}

			return null;
		}

		/**
		 * Set multiple settings at once.
		 *
		 * @param {object} settingsById Object where keys are setting IDs and values are the new setting values.
		 */
		updateSettingsById(settingsById) {
			for (const settingId in settingsById) {
				if (settingsById.hasOwnProperty(settingId)) {
					const path = this.mapSettingIdToPath(settingId);
					if (path !== null) {
						_.set(this.currentConfig, path, settingsById[settingId]);
					}
				}
			}
		}

		/**
		 * Get a value from the menu configuration object. Uses a simple path, not a setting ID.
		 *
		 * @param {string|string[]} path
		 * @param {*} defaultValue
		 * @returns {*}
		 */
		getPath(path, defaultValue = null) {
			return _.get(this.currentConfig, path, defaultValue);
		}

		/**
		 * Directly set a value in the menu configuration object. Does not translate setting IDs.
		 *
		 * @param {string|string[]} path Plain path, not a setting ID.
		 * @param {*} value
		 */
		setPath(path, value) {
			_.set(this.currentConfig, path, value);
		}
	}

	AmeEditorApi.configDataAdapter = new AuxiliaryConfigDataAdapter(wsEditorData.auxDataConfig);
	//endregion

	/******************************************************************
	                      Tooltips and hints
	 ******************************************************************/


	//Increase tooltip z-index to avoid a conflict with the Essential Grid plugin.
	//That plugin sets the jQuery UI dialog z-index to 100102, making tooltips appear
	//underneath the dialog.
	$.fn.qtip.zindex = 100200;
	//Set up tooltips
	$('.ws_tooltip_trigger').qtip({
		style: {
			classes: 'qtip qtip-rounded ws_tooltip_node'
		},
		hide: {
			fixed: true,
			delay: 300
		}
	});

	//Set up menu field tooltips.
	menuEditorNode.on('mouseenter click', '.ws_edit_field .ws_field_tooltip_trigger', function(event) {
		var $trigger = $(this),
			fieldName = $trigger.closest('.ws_edit_field').data('field_name');

		if (knownMenuFields[fieldName].tooltip === null) {
			return;
		}

		var tooltipText = 'Invalid tooltip';
		if (typeof knownMenuFields[fieldName].tooltip === 'string') {
			tooltipText = knownMenuFields[fieldName].tooltip;
		} else if (typeof knownMenuFields[fieldName].tooltip === 'function') {
			tooltipText = function() {
				var $theTrigger = $(this),
					menuItem = $theTrigger.closest('.ws_container').data('menu_item');
				return knownMenuFields[fieldName].tooltip(menuItem);
			}
		}

		$trigger.qtip({
			overwrite: false,
			content: {
				text: tooltipText
			},
			show: {
				event: event.type,
				ready: true //Show immediately.
			},
			style: {
				classes: 'qtip qtip-rounded ws_tooltip_node'
			},
			hide: {
				fixed: true,
				delay: 300
			},
			position: {
				my: 'bottom center',
				at: 'top center'
			}
		}, event);
	});

	//Set up the "additional permissions are available" tooltips.
	menuEditorNode.on('mouseenter click', '.ws_ext_permissions_indicator', function() {
		var $indicator = $(this);
		$indicator.qtip({
			overwrite: false,
			content: {
				text: function() {
					var indicator = $(this),
						extPermissions = indicator.data('ext_permissions'),
						text = 'Additional permission settings are available. Click "Edit..." to change them.',
						heading = '',
						$content = $('<span></span>');

					if (extPermissions && extPermissions.hasOwnProperty('title')) {
						heading = extPermissions.title;
						if (extPermissions.hasOwnProperty('type')) {
							heading = _.capitalize(_.startCase(extPermissions.type).toLowerCase()) + ': ' + heading;
						}
						$content.append($('<strong></strong>').text(heading)).append('<br>');
					}

					$content.append($(document.createTextNode(text)));
					return $content;
				}
			},
			show: {
				ready: true //Show immediately.
			},
			style: {
				classes: 'qtip qtip-rounded ws_tooltip_node'
			},
			hide: {
				fixed: true,
				delay: 300
			},
			position: {
				my: 'bottom center',
				at: 'top center'
			}
		});
	});

	//Flag closed hints as hidden by sending the appropriate AJAX request to the backend.
	$('.ws_hint_close').on('click', function() {
		var hint = $(this).parents('.ws_hint').first();
		hint.hide();
		wsEditorData.showHints[hint.attr('id')] = false;
		$.post(
			wsEditorData.adminAjaxUrl,
			{
				'action': 'ws_ame_hide_hint',
				'_ajax_nonce': wsEditorData.hideHintNonce,
				'hint': hint.attr('id')
			}
		);
	});

	//Expand/collapse the "How To" box.
	var $howToBox = $("#ws_ame_how_to_box");
	$howToBox.find(".handlediv").on('click', function() {
		$howToBox.toggleClass('closed');
		if (typeof $['cookie'] !== 'undefined') {
			$.cookie(
				'ame_how_to_box_open',
				($howToBox.hasClass('closed') ? '0' : '1'),
				{ expires: 180 }
			);
		}
	});


	/******************************************************************
	                           Actor views
	 ******************************************************************/

	if (wsEditorData.wsMenuEditorPro) {
		actorSelectorWidget.onChange(function() {
			//There are some UI elements that can be visible or hidden depending on whether an actor is selected.
			var editorNode = $('#ws_menu_editor');
			editorNode.toggleClass('ws_is_actor_view', (actorSelectorWidget.selectedActor !== null));

			//Update the menu item states to indicate whether they're accessible.
			editorNode.find('.ws_container').each(function() {
				updateActorAccessUi($(this));
			});
		});

		if (wsEditorData.hasOwnProperty('selectedActor') && wsEditorData.selectedActor) {
			actorSelectorWidget.setSelectedActor(wsEditorData.selectedActor);
		} else {
			actorSelectorWidget.setSelectedActor(null);
		}
	}

	/******************************************************************
	                        "Test Access" feature
	 ******************************************************************/
	var testAccessDialog = $('#ws_ame_test_access_screen').dialog({
			autoOpen: false,
			modal: true,
			closeText: ' ',
			title: 'Test access',
			width: 900
			//draggable: false
		}),
		testMenuItemList = $('#ws_ame_test_menu_item'),
		testActorList = $('#ws_ame_test_relevant_actor'),
		testAccessButton = $('#ws_ame_start_access_test'),
		testAccessFrame = $('#ws_ame_test_access_frame'),
		testConfig = null,

		testProgress = $('#ws_ame_test_progress'),
		testProgressText = $('#ws_ame_test_progress_text');

	$('#ws_test_access').on('click', function () {
		testConfig = readMenuTreeState();

		var selectedMenuContainer = getSelectedMenu(),
			selectedItemContainer = getSelectedSubmenuItem(),
			selectedMenu = null,
			selectedItem = null,
			selectedUrl = null;
		if (selectedMenuContainer.length > 0) {
			selectedMenu = selectedMenuContainer.data('menu_item');
			selectedUrl = getFieldValue(selectedMenu, 'url');
		}
		if (selectedItemContainer.length > 0) {
			selectedItem = selectedItemContainer.data('menu_item');
			selectedUrl = getFieldValue(selectedItem, 'url');
		}

		function addMenuItems(collection, parentTitle, parentFile) {
			_.each(collection, function (menuItem) {
				if (menuItem.separator) {
					return;
				}

				var title = formatMenuTitle(getFieldValue(menuItem, 'menu_title', '[Untitled menu]'));
				if (parentTitle) {
					title = parentTitle + ' -> ' + title;
				}
				var url = getFieldValue(menuItem, 'url', '[no-url]');

				var option = $(
					'<option>', {
						val: url,
						text: title
					}
				);
				option.data('menu_item', menuItem);
				option.data('parent_file', parentFile || '');
				option.prop('selected', (url === selectedUrl));

				testMenuItemList.append(option);

				if (menuItem.items) {
					addMenuItems(menuItem.items, title, getFieldValue(menuItem, 'file', ''));
				}
			});
		}

		//Populate the list of menu items.
		testMenuItemList.empty();
		addMenuItems(testConfig.tree);

		//Populate the actor list.
		testActorList.empty();
		testActorList.append($('<option>', {text: 'Not selected', val: ''}));
		_.each(actorSelectorWidget.getVisibleActors(), function (actor) {
			//TODO: Skip anything that isn't a role
			var option = $('<option>', {
				val: actor.id,
				text: actorSelectorWidget.getNiceName(actor)
			});
			testActorList.append(option);
		});

		//Pre-select the current actor.
		if (actorSelectorWidget.selectedActor !== null) {
			testActorList.val(actorSelectorWidget.selectedActor);
		}

		testAccessDialog.dialog('open');
	});

	testAccessButton.on('click', function () {
		testAccessButton.prop('disabled', true);
		testProgress.show();
		testProgressText.text('Sending menu settings...');

		var selectedOption = testMenuItemList.find('option:selected').first(),
			selectedMenu = selectedOption.data('menu_item');

		$.ajax(
			wsEditorData.adminAjaxUrl,
			{
				data: {
					'action': 'ws_ame_set_test_configuration',
					'data': encodeMenuAsJSON(testConfig),
					'_ajax_nonce': wsEditorData.setTestConfigurationNonce
				},
				method: 'post',
				dataType: 'json',
				success: function(response) {
					if (!response) {
						alert('Error: Could not parse the server response.');
						testAccessButton.prop('disabled', false);
						return;
					}
					if (response.error) {
						alert(response.error);
						testAccessButton.prop('disabled', false);
						return;
					}
					if (!response.success) {
						alert('Error: The request failed, but there is no error information available.');
						testAccessButton.prop('disabled', false);
						return;
					}

					throw new Error('Not fully implemented yet!');
					//Caution: Won't work in IE. Needs compat checks.
					//var testPageUrl = new URL(menuUrl, window.location.href);
					var testPageUrl = 'fixme';
					testPageUrl.searchParams.append('ame-test-menu-access-as', $('#ws_ame_test_access_username').val());
					testPageUrl.searchParams.append('_wpnonce', wsEditorData.testAccessNonce);
					testPageUrl.searchParams.append('ame-test-relevant-role', testActorList.val());

					testPageUrl.searchParams.append('ame-test-target-item', getFieldValue(selectedMenu, 'file', ''));
					testPageUrl.searchParams.append('ame-test-target-parent', selectedOption.data('parent_file'));

					testProgressText.text('Loading the test page....');
					$('#ws_ame_test_frame_placeholder').hide();

					$(window).on('message', receiveTestAccessResults);
					testAccessFrame
						.show()
						.on('load', onAccessTestLoaded)
						.prop('src', testPageUrl.href);
				},
				error: function(jqXHR, textStatus) {
					alert('HTTP Error: ' + textStatus);
					testAccessButton.prop('disabled', false);
				}
			}
		);
	});

	function onAccessTestLoaded() {
		testAccessFrame.off('load', onAccessTestLoaded);
		testProgress.hide();

		testAccessButton.prop('disabled', false);
	}

	function receiveTestAccessResults(event) {
		if (event.originalEvent.source !== testAccessFrame.get(0).contentWindow) {
			if (console && console.warn) {
				console.warn('AME: Received a message from an unexpected source. Message ignored.');
			}
			return;
		}
		var message = event.originalEvent.data || event.originalEvent.message;
		console.log('message received', message);

		$(window).off('message', receiveTestAccessResults);
	}


	//Finally, show the menu
	loadMenuConfiguration(customMenu);

	//Select the previous selected menu, if any.
	if (wsEditorData.selectedMenu) {
		AmeEditorApi.selectMenuItemByUrl(
			'#ws_menu_box',
			wsEditorData.selectedMenu,
			_.get(wsEditorData, 'expandSelectedMenu') === '1'
		);

		if (wsEditorData.selectedSubmenu) {
			AmeEditorApi.selectMenuItemByUrl(
				'#ws_submenu_box',
				wsEditorData.selectedSubmenu,
				_.get(wsEditorData, 'expandSelectedSubmenu') === '1'
			);
		}
	}

	//... and make the UI visible now that it's fully rendered.
	menuEditorNode.css('visibility', 'visible');

	//Add an extra class to the editor toolbars when their "position: sticky" triggers.
	//This is useful for adding a bottom border and other styles.
	if (IntersectionObserver) {
		/*
		This assumes that the toolbars stick below the admin bar. If that changes,
		this code will need to be updated.

		How do we detect that?
		- We could use IntersectionObserver to detect when the toolbar leaves the viewport,
		  but since it's sticky, it usually won't.
		- We can get around that by using negative root margins. Negative margins effectively shrink
		  the bounding box of the viewport. If we set the top margin to "-1px", the effective top of
		  the viewport will be 1px lower, so the observer will fire just *before* the toolbar would
		  leave the viewport.
		- The admin bar is always at the top of the viewport.
		- So we can detect when the toolbar is right below the admin bar by using a negative top
		  margin that is equal to the height of the admin bar + 1px.
		*/
		let observerRootMargin = '-33px'; //Default admin bar height is 32px.
		const adminBarHeight = $('#wpadminbar').outerHeight();
		if (adminBarHeight > 0) {
			observerRootMargin = (-1 * adminBarHeight - 1) + 'px';
		}

		const observer = new IntersectionObserver(
			(entries) => {
				for (const e of entries) {
					e.target.classList.toggle('ws_is_sticky_toolbar', e.intersectionRatio < 1);
				}
			},
			{
				threshold: [1],
				rootMargin: observerRootMargin + ' 0px 0px 0px'
			}
		);

		const editorToolbars = document.querySelectorAll('.ws_main_container .ws_toolbar');
		for (const toolbar of editorToolbars) {
			//Skip the toolbar that's inside the template column.
			if (toolbar.closest && (toolbar.closest('#ame-submenu-column-template') !== null)) {
				continue;
			}
			observer.observe(toolbar);
		}
	}
}

$(document).ready(ameOnDomReady);

//Compatibility workaround: If another plugin or theme throws an exception in its jQuery.ready() handler,
//our callback might never get run. As a backup, set a timer and manually check if the DOM is ready.
var domCheckAttempts = 0,
	maxDomCheckAttempts = 30;
var domCheckIntervalId = window.setInterval(function () {
	if (isDomReadyDone || (domCheckAttempts >= maxDomCheckAttempts)) {
		window.clearInterval(domCheckIntervalId);
		return;
	}
	domCheckAttempts++;

	if ($ && $.isReady) {
		window.clearInterval(domCheckIntervalId);
		ameOnDomReady();
	}
}, 1000);

})(jQuery, wsAmeLodash);
