<?php
namespace sgpb;
use \SgpbDataConfig;
use \SgpbPopupConfig;
class RegisterPostType
{
	private $popupTypeObj;
	private $popupType;
	private $popupId;

	public function setPopupType($popupType)
	{
		$this->popupType = $popupType;
	}

	public function getPopupType()
	{
		return $this->popupType;
	}

	public function setPopupId($popupId)
	{
		$this->popupId = $popupId;
	}

	public function getPopupId()
	{
		return $this->popupId;
	}

	public function __construct()
	{
		if (!AdminHelper::showMenuForCurrentUser()) {
			return false;
		}
		// its need here to an apply filters for add popup types needed data
		SgpbPopupConfig::popupTypesInit();
		$this->init();
		// Call init data configs apply filters
		SgpbDataConfig::init();

		return true;

	}

	public function setPopupTypeObj($popupTypeObj)
	{
		$this->popupTypeObj = $popupTypeObj;
	}

	public function getPopupTypeObj()
	{
		return $this->popupTypeObj;
	}

	public function getPostTypeArgs()
	{
		$labels = $this->getPostTypeLabels();

		$args = array(
			'labels'              => $labels,
			'description'         => __('Description.', 'popup-builder'),
			// Exclude_from_search
			'exclude_from_search' => true,
			'public'              => true,
			'has_archive'         => false,
			// Where to show the post type in the admin menu
			'show_ui'             => true,
			'query_var'           => false,
			'rewrite'             => array('slug' => SG_POPUP_POST_TYPE),
			'map_meta_cap'        => true,
			'capability_type'     => array('sgpb_popup', 'sgpb_popups'),
			'menu_position'       => 10,
			'supports'            => apply_filters('sgpbPostTypeSupport', array('title', 'editor')),
			'menu_icon'           => 'dashicons-menu-icon-sgpb',
			'show_in_rest'        => true
		);

		if (is_admin()) {
			$args['capability_type'] = 'post';
		}

		return $args;
	}

	public function getPostTypeLabels()
	{
		$count = SGPBNotificationCenter::getAllActiveNotifications();
		$count = '';
		$labels = array(
			'name'               => _x('Popup Builder', 'post type general name', 'popup-builder'),
			'singular_name'      => _x('Popup', 'post type singular name', 'popup-builder'),
			'menu_name'          => _x('Popup Builder', 'admin menu', 'popup-builder').$count,
			'name_admin_bar'     => _x('Popup', 'add new on admin bar', 'popup-builder'),
			'add_new'            => _x('Add New', 'Popup', 'popup-builder'),
			'add_new_item'       => __('Add New Popup', 'popup-builder'),
			'new_item'           => __('New Popup', 'popup-builder'),
			'edit_item'          => __('Edit Popup', 'popup-builder'),
			'view_item'          => __('View Popup', 'popup-builder'),
			'all_items'          => __('All Popups', 'popup-builder'),
			'search_items'       => __('Search', 'popup-builder'),
			'parent_item_colon'  => __('Parent Popups:', 'popup-builder'),
			'not_found'          => __('No popups found.', 'popup-builder'),
			'not_found_in_trash' => __('No popups found in Trash.', 'popup-builder')
		);

		return $labels;
	}

	public function postTypeSupportForPopupTypes($supports)
	{
		$popupType = $this->getPopupTypeName();
		$typePath = $this->getPopupTypePathFormPopupType($popupType);
		$popupClassName = $this->getPopupClassNameFromPopupType($popupType);

		if (!file_exists($typePath.$popupClassName.'.php')) {
			return $supports;
		}

		require_once($typePath.$popupClassName.'.php');
		$popupClassName = __NAMESPACE__.'\\'.$popupClassName;

		// We need to remove wyswyg editor for some popup types and that’s why we are doing this check.
		if (method_exists($popupClassName, 'getPopupTypeSupports')) {
			$supports = $popupClassName::getPopupTypeSupports();
		}

		return $supports;
	}

	public function init()
	{
		add_filter('sgpbPostTypeSupport', array($this, 'postTypeSupportForPopupTypes'), 1, 1);
		$postType = SG_POPUP_POST_TYPE;
		$args = $this->getPostTypeArgs();

		register_post_type($postType, $args);

		$this->createPopupObjFromPopupType();
		

	}

	private function createPopupObjFromPopupType()
	{
		$popupId = 0;

		if (!empty($_GET['post'])) {
			$popupId = (int)sanitize_text_field( wp_unslash( $_GET['post'] ) );
		}

		$popupType = $this->getPopupTypeName();
		$this->setPopupType($popupType);
		$this->setPopupId($popupId);

		$this->createPopupObj();
	}

	private function getPopupTypeName()
	{
		$popupType = 'html';

		/*
		 * First, we try to find the popup type with the post id then,
		 * if the post id doesn't exist, we try to find it with $_GET['sgpb_type']
		 */
		if (!empty($_GET['post'])) {
			$popupId = (int)sanitize_text_field( wp_unslash( $_GET['post'] ) );
			$popupOptionsData = SGPopup::getPopupOptionsById($popupId);
			if (!empty($popupOptionsData['sgpb-type'])) {
				$popupType = $popupOptionsData['sgpb-type'];
			}
		}
		else if (!empty($_GET['sgpb_type'])) {
			$popupType = sanitize_text_field( wp_unslash( $_GET['sgpb_type'] ) );
		}

		return $popupType;
	}

	private function getPopupClassNameFromPopupType($popupType)
	{
		$popupName = ucfirst(strtolower($popupType));
		$popupClassName = $popupName.'Popup';

		return $popupClassName;
	}

	private function getPopupTypePathFormPopupType($popupType)
	{
		global $SGPB_POPUP_TYPES;
		$typePath = '';

		if (!empty($SGPB_POPUP_TYPES['typePath'][$popupType])) {
			$typePath = $SGPB_POPUP_TYPES['typePath'][$popupType];
		}

		return $typePath;
	}

	public function createPopupObj()
	{
		$popupId = $this->getPopupId();
		$popupType = $this->getPopupType();
		$typePath = $this->getPopupTypePathFormPopupType($popupType);
		$popupClassName = $this->getPopupClassNameFromPopupType($popupType);

		if (!file_exists($typePath.$popupClassName.'.php')) {
			die(esc_html__('Popup class does not exist', 'popup-builder'));
		}
		require_once($typePath.$popupClassName.'.php');
		$popupClassName = __NAMESPACE__.'\\'.$popupClassName;
		$popupTypeObj = new $popupClassName();
		$popupTypeObj->setId($popupId);
		$popupTypeObj->setType($popupType);
		$this->setPopupTypeObj($popupTypeObj);
		$popupTypeMainView = $popupTypeObj->getPopupTypeMainView();
		
		$popupTypeViewData = $popupTypeObj->getPopupTypeOptionsView();

		if (!empty($popupTypeMainView)) {
			add_filter('sgpbAdditionalMetaboxes', array($this, 'sgpbPopupTypeMainViewMetaboxes'), 1, 1);
			//add_action('add_meta_boxes', array($this, 'popupTypeMain'));
		}
		if (!empty($popupTypeViewData)) {
			//add_action('add_meta_boxes', array($this, 'popupTypeOptions'));
			add_filter('sgpbAdditionalMetaboxes', array($this, 'sgpbPopupTypeOptionsViewMetaboxes'), 1, 1);
		}
		// TODO remove all content about this section!
	/*	if ($popupType == 'subscription') {
			add_action('add_meta_boxes', array($this, 'rightBannerMetabox'));
		}*/
		
	}

	public function rightBannerMetabox()
	{
		$banner = AdminHelper::getRightMetaboxBannerText();
		$isSubscriptionPlusActive = is_plugin_active(SGPB_POPUP_SUBSCRIPTION_PLUS_EXTENSION_KEY);
		if ($banner == '' || $isSubscriptionPlusActive) {
			return;
		}
		add_meta_box(
			'popupTypeRightBannerView',
			__('News', 'popup-builder'),
			array($this, 'popupTypeRightBannerView'),
			SG_POPUP_POST_TYPE,
			'side',
			'low'
		);
	}

	public function sgpbPopupTypeMainViewMetaboxes($metaboxes)
	{
		
		$popupTypeObj = $this->getPopupTypeObj();
		
		
		
		$optionsView = $popupTypeObj->getPopupTypeMainView();
		$typeView = array();

		$metaboxes['popupTypeMainView'] = array(
			'key' => 'popupTypeMainView',
			/* translators: metabox Title. */
			'displayName' => sprintf( '%s', $optionsView['metaboxTitle']),
			'filePath' => $optionsView['filePath'],
			'short_description' => $optionsView['short_description'],
			SG_POPUP_POST_TYPE,
			'normal',
			'high'
		);

		return $metaboxes;
	}

	public function popupTypeMain()
	{
		$popupTypeObj = $this->getPopupTypeObj();
		$optionsView = $popupTypeObj->getPopupTypeMainView();
		add_meta_box(
			'popupTypeMainView',
			sprintf( '%s', $optionsView['metaboxTitle']),
			array($this, 'popupTypeMainView'),
			SG_POPUP_POST_TYPE,
			'normal',
			'high'
		);
	}

	public function sgpbPopupTypeOptionsViewMetaboxes($metaboxes)
	{
		$popupTypeObj = $this->getPopupTypeObj();
		$optionsView = $popupTypeObj->getPopupTypeOptionsView();

		$metaboxes['popupTypeOptionsView'] = array(
			'key' => 'popupTypeOptionsView',
			/* translators: metabox Title. */
			'displayName' => sprintf( '%s', $optionsView['metaboxTitle']),
			'short_description' => $optionsView['short_description'],
			'filePath' => $optionsView['filePath'],
			SG_POPUP_POST_TYPE,
			'normal',
			'high'
		);

		return $metaboxes;
	}

	public function popupTypeOptions()
	{
		$popupTypeObj = $this->getPopupTypeObj();
		$optionsView = $popupTypeObj->getPopupTypeOptionsView();
		add_meta_box(
			'popupTypeOptionsView',
			/* translators: metabox Title. */
			sprintf( '%s', $optionsView['metaboxTitle']),
			array($this, 'popupTypeOptionsView'),
			SG_POPUP_POST_TYPE,
			'normal',
			'high'
		);
	}

	public function supportLinks()
	{
		add_submenu_page(
			'edit.php?post_type='.SG_POPUP_POST_TYPE,
			__('Settings', 'popup-builder'),
			__('Settings', 'popup-builder'),
			'manage_options',
			SG_POPUP_SETTINGS_PAGE,
			array($this, 'settings')
		);

		add_submenu_page(
			'edit.php?post_type='.SG_POPUP_POST_TYPE,
			__('Extend', 'popup-builder'),
			__('Extend', 'popup-builder'),
			'sgpb_manage_options',
			SG_POPUP_EXTEND_PAGE,
			array($this, 'extendLink')
		);

		add_submenu_page(
			'edit.php?post_type='.SG_POPUP_POST_TYPE,
			__('Support', 'popup-builder'),
			__('Support', 'popup-builder'),
			'sgpb_manage_options',
			SG_POPUP_SUPPORT_PAGE,
			array($this, 'supportLink')
		);
	}

	public function addSubMenu()
	{
		add_submenu_page(
			'edit.php?post_type='.SG_POPUP_POST_TYPE,
			__('Add New', 'popup-builder'),
			__('Add New', 'popup-builder'),
			'sgpb_manage_options',
			SG_POPUP_POST_TYPE,
			array($this, 'popupTypesPage')
		);

		add_submenu_page(
			'edit.php?post_type='.SG_POPUP_POST_TYPE,
			__('All Subscribers', 'popup-builder'),
			__('All Subscribers', 'popup-builder'),
			'sgpb_manage_options',
			SG_POPUP_SUBSCRIBERS_PAGE,
			array($this, 'subscribersPage')
		);

		add_submenu_page(
			'edit.php?post_type='.SG_POPUP_POST_TYPE,
			__('Newsletter', 'popup-builder'),
			__('Newsletter', 'popup-builder'),
			'sgpb_manage_options',
			SG_POPUP_NEWSLETTER_PAGE,
			array($this, 'newsletter')
		);
	}

	public function supportLink()
	{
		wp_redirect(SG_POPUP_SUPPORT_URL);
	}

	public function extendLink()
	{
		wp_redirect(SG_POPUP_EXTENSIONS_URL);
	}

	public function addOptionsMetaBox()
	{
		add_meta_box(
			'optionsMetaboxView',
			__('Advanced', 'popup-builder'),
			array($this, 'optionsMetaboxView'),
			SG_POPUP_POST_TYPE,
			'normal',
			'low'
		);
	}

	public function addPopupMetaboxes()
	{
		$additionalMetaboxes = apply_filters('sgpbAdditionalMetaboxes', array());

		if (empty($additionalMetaboxes)) {
			return false;
		}


		$context = 'normal';
		$priority = 'high';
		$key = 'allMetaboxesView';
		$metabox = $additionalMetaboxes['allMetaboxesView'];
		$popupTypeObj = $this->getPopupTypeObj();
		$filepath = $metabox['filePath'];
		
		add_meta_box(
			$key,
			/* translators: %s: display Name*/
			sprintf( '%s', $metabox['displayName']),			
			function() use ($filepath, $popupTypeObj) {				
				require_once $filepath;
			},
			SG_POPUP_POST_TYPE,
			$context,
			$priority
		);

		remove_meta_box('popup-categoriesdiv', SG_POPUP_POST_TYPE, 'side');

		return true;
	}

	public function popupTypesPage()
	{
		if (file_exists(SG_POPUP_VIEWS_PATH.'popupTypes.php')) {
			require_once(SG_POPUP_VIEWS_PATH.'popupTypes.php');
		}
	}

	public function subscribersPage()
	{
		if (file_exists(SG_POPUP_VIEWS_PATH.'subscribers.php')) {
			require_once(SG_POPUP_VIEWS_PATH . 'subscribers.php');
		}
	}

	public function settings()
	{
		if (file_exists(SG_POPUP_VIEWS_PATH.'settings.php')) {
			require_once(SG_POPUP_VIEWS_PATH.'settings.php');
		}
	}

	public function newsletter()
	{
		if (file_exists(SG_POPUP_VIEWS_PATH.'newsletter.php')) {
			require_once(SG_POPUP_VIEWS_PATH.'newsletter.php');
		}
	}

	public function popupTypeOptionsView()
	{
		$popupTypeObj = $this->getPopupTypeObj();
		$optionsView = $popupTypeObj->getPopupTypeOptionsView();		
		if (file_exists($optionsView['filePath'])) {
			require_once($optionsView['filePath']);
		}
	}

	public function popupTypeMainView()
	{
		$popupTypeObj = $this->getPopupTypeObj();
		$optionsView = $popupTypeObj->getPopupTypeMainView();		
		if (file_exists($optionsView['filePath'])) {
			require_once($optionsView['filePath']);
		}
	}

	public function popupTypeRightBannerView()
	{
		$banner = AdminHelper::getRightMetaboxBannerText();
		echo wp_kses($banner, AdminHelper::allowed_html_tags());
	}
}
