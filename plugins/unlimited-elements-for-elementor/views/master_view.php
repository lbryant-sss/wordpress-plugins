<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$bottomLineClass = "";

if($view == "layout")
	$bottomLineClass = " unite-position-right";

UniteFunctionsUC::obStart();

self::requireView($view);

$htmlView = ob_get_contents();

ob_end_clean();


$htmlClassAdd = "";

if(!empty($view)){
	$htmlClassAdd = " unite-view-{$view}";
	$bottomLineClass .= " unite-view-{$view}";
}

$showMenu = true;

switch($view){
	case "testaddonnew":
	case GlobalsUC::VIEW_TEST_ADDON:
	case GlobalsUC::VIEW_ASSETS:
	case GlobalsUC::VIEW_EDIT_ADDON:
	case "addondefaults":
		$showMenu = false;
	break;
}


?>

<?php 
HelperHtmlUC::putGlobalsHtmlOutput(); 

$script = 'var g_view = "' . esc_attr(self::$view) . '";';
UniteProviderFunctionsUC::printCustomScript($script, true); 

?>

<?php HelperHtmlUC::putInternalAdminNotices() ?>

<div id="viewWrapper" class="unite-view-wrapper unite-admin unite-inputs <?php echo esc_attr($htmlClassAdd); ?>">

	<?php require_once(GlobalsUC::$pathTemplates . "head.php"); ?>

	<div class="ue-content-wrapper">

		<?php
			if($showMenu == true)
				require_once(GlobalsUC::$pathTemplates . "menu.php");
		?>

		<?php 
		uelm_echo( $htmlView ); 
		?>
		<?php

		$filenameProviderView = GlobalsUC::$pathProviderViews . $view . ".php";

		if(file_exists($filenameProviderView))
			require_once($filenameProviderView);

		?>
	</div>

</div>

<?php

$filepathProviderMasterView = GlobalsUC::$pathProviderViews . "master_view.php";

if(file_exists($filepathProviderMasterView))
	require_once $filepathProviderMasterView;

?>

<?php if(GlobalsUC::$blankWindowMode == false): ?>

	<?php HelperHtmlUC::putFooterAdminNotices() ?>

	<div id="uc_dialog_version" title="<?php 
	echo esc_html(__("Version Release Log. Current Version: ", "unlimited-elements-for-elementor") . ' ' . UNLIMITED_ELEMENTS_VERSION);
	?>" style="display:none;">
		<div class="unite-dialog-inside">
			<div id="uc_dialog_version_content" class="unite-dialog-version-content">
				<div id="uc_dialog_loader" class="loader_text"><?php esc_html_e("Loading...", "unlimited-elements-for-elementor")?></div>
			</div>
		</div>
	</div>

	<div class="unite-clear"></div> 

	<div class="unite-plugin-version-line unite-admin <?php echo esc_attr($bottomLineClass)?>">
		<?php UniteProviderFunctionsUC::putFooterTextLine() ?>
		<?php esc_html_e("Plugin version", "unlimited-elements-for-elementor"); ?> <?php echo esc_html(UNLIMITED_ELEMENTS_VERSION); ?>
		<?php if(defined("UNLIMITED_ELEMENTS_UPRESS_VERSION")) esc_html_e("upress", "unlimited-elements-for-elementor"); ?>
		(<a id="uc_version_link" href="#"><?php esc_html_e("view changelog", "unlimited-elements-for-elementor"); ?></a>)
		<?php UniteProviderFunctionsUC::doAction(UniteCreatorFilters::ACTION_BOTTOM_PLUGIN_VERSION)?>
	</div>

<?php endif; ?>
