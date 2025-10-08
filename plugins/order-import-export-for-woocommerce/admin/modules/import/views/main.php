<?php

/**
 * Main view file of import section
 *
 * @link            
 *
 * @package  Wt_Import_Export_For_Woo
 */
if (!defined('ABSPATH')) {
	exit;
}
?>
<?php
do_action('wt_iew_importer_before_head');
$wf_admin_view_path = plugin_dir_path(WT_O_IEW_PLUGIN_FILENAME) . 'admin/views/';
?>
<style type="text/css">
	.wt_iew_import_step {
		display: none;
	}

	.wt_iew_import_step_loader {
		width: 100%;
		height: 400px;
		text-align: center;
		line-height: 400px;
		font-size: 14px;
	}

	.wt_iew_import_step_main {
		float: left;
		box-sizing: border-box;
		padding: 15px;
		padding-bottom: 0px;
		width: 95%;
		margin: 30px 2.5%;
		background: #fff;
		box-shadow: 0px 2px 2px #ccc;
		border: solid 1px #efefef;
	}

	.wt_iew_import_main {
		padding: 20px 0px;
	}

	select[name=wt_iew_file_from] {
		visibility: hidden;
	}

	.wt-something-went-wrong-wrap {
		position: absolute;
		margin-top: 150px;
		left: 25%;
		color: #FFF;
		width: 450px;
		height: 275px;
		background: #FFF;
		padding: 25px;
		text-align: center;
		border: 1px solid #B32D2E;
		border-radius: 10px;
		box-shadow: 0px 0px 2px 2px #cdc8c8;
	}
</style>
<div class="wt_iew_view_log wt_iew_popup" style="text-align:left">
	<div class="wt_iew_popup_hd">
		<span style="line-height:40px;" class="dashicons dashicons-media-text"></span>
		<span class="wt_iew_popup_hd_label"><?php esc_html_e('History Details', 'order-import-export-for-woocommerce'); ?></span>
		<div class="wt_iew_popup_close">X</div>
	</div>
	<div class="wt_iew_log_container" style="padding:25px;">

	</div>
</div>

<div class="wt_iew_import_progress_wrap wt_iew_popup">
	<div class="wt_iew_popup_hd wt_iew_import_progress_header">
		<span style="line-height:40px;" class="dashicons dashicons-media-text"></span>
		<span class="wt_iew_popup_hd_label"><?php esc_html_e('Import progress', 'order-import-export-for-woocommerce'); ?></span>
		<div class="wt_iew_popup_close">X</div>
	</div>
	<div class="wt_iew_import_progress_content" style="max-height:620px;overflow: auto;">
		<table id="wt_iew_import_progress" class="widefat_importer widefat wt_iew_import_progress wp-list-table fixed striped history_list_tb log_list_tb">
			<thead>
				<tr>
					<th style="width:15%" class="row"><?php esc_html_e('Row', 'order-import-export-for-woocommerce'); ?></th>
					<th style="width:20%"><?php esc_html_e('Item', 'order-import-export-for-woocommerce'); ?></th>
					<th style="width:50%"><?php esc_html_e('Message', 'order-import-export-for-woocommerce'); ?></th>
					<th style="width:20%" class="reason"><?php esc_html_e('Status', 'order-import-export-for-woocommerce'); ?></th>
				</tr>
			</thead>
			<tbody id="wt_iew_import_progress_tbody"></tbody>
		</table>
	</div>
	<br />
	<div id="wt_iew_import_progress_end"></div>
	<div class="progressa">
		<div class="progressab" style="background-color: rgb(178, 222, 75);width:5px; "></div>
	</div>
	<div class="progresscta">
		<div class="wt_iew_cta_banner_border"></div>
		<p id="dynamic-cta-content" style="font-size: 13px; font-weight: 400; padding:0px 20px;">
		</p>
	</div>

	<div class="wt-iew-import-completed" style="display:none;border-top: 1px outset;">
		<h3><?php esc_html_e('Import Completed', 'order-import-export-for-woocommerce'); ?><span style="color:green" class="dashicons dashicons-yes-alt"></span></h3>
		<div class="wt-iew-import-results">
			<div class="wt-iew-import-result-row">
				<div class="wt-iew-import-results-total wt-iew-import-result-column"><?php esc_html_e('Total records identified', 'order-import-export-for-woocommerce'); ?>:<span id="wt-iew-import-results-total-count"></span></div>
				<div style="color:green" class="wt-iew-import-results-imported wt-iew-import-result-column"><?php esc_html_e('Imported successfully', 'order-import-export-for-woocommerce'); ?>:<span id="wt-iew-import-results-imported-count"></span></div>
				<div style="color:red" class="wt-iew-import-results-failed wt-iew-import-result-column"><?php esc_html_e('Failed/Skipped', 'order-import-export-for-woocommerce'); ?>:<span id="wt-iew-import-results-failed-count"></span></div>
			</div>
		</div>
	</div>


	<div class="wt-iew-plugin-toolbar bottom" style="padding:5px;margin-left:-10px;">
		<div style="float: left">
			<div class="wt-iew-import-time" style="display:none;padding-left: 40px;margin-top:10px;"><?php esc_html_e('Time taken to complete', 'order-import-export-for-woocommerce'); ?>:<span id="wt-iew-import-time-taken"></span></div>
		</div>
		<div style="float:right;">
			<div style="float:right;">
				<a target="_blank" href="#" class="button button-primary wt_iew_view_imported_items" data-log-file="" style="display:none" type="button" style="margin-right:10px;"><?php esc_html_e('View Item', 'order-import-export-for-woocommerce'); ?></a>
				<button class="button button-primary wt_iew_view_log_btn" data-log-file="" style="display:none" type="button" style="margin-right:10px;"><?php esc_html_e('View Log', 'order-import-export-for-woocommerce'); ?></button>
				<button class="button button-primary wt_iew_popup_cancel_btn" type="button" style="margin-right:10px;"><?php esc_html_e('Cancel', 'order-import-export-for-woocommerce'); ?></button>
				<button class="button button-primary wt_iew_popup_close_btn" style="display:none" type="button" style="margin-right:10px;"><?php esc_html_e('Close', 'order-import-export-for-woocommerce'); ?></button>
			</div>
		</div>
	</div>
</div>

<?php
Wt_Iew_IE_Basic_Helper::debug_panel($this->module_base);
?>
<?php include WT_O_IEW_PLUGIN_PATH . "/admin/views/_save_template_popup.php"; ?>



<?php
if ($requested_rerun_id > 0 && $this->rerun_id == 0) {
?>
	<div class="wt_iew_warn wt_iew_rerun_warn">
		<?php esc_html_e('Unable to handle Re-Run request.', 'order-import-export-for-woocommerce'); ?>
	</div>
<?php
}
?>

<div class="wt_iew_loader_info_box"></div>
<div class="wt_iew_overlayed_loader"></div>

<div class="wt_iew_import_step_main_wrapper" style="width:68%; float: left">



	<div class="wt-something-went-wrong" style="position:relative;display:none;">
		<div class="wt-something-went-wrong-wrap">
			<p class="wt_iew_popup_close" style="float:right;margin-top: -15px !important;margin-right: -15px !important;line-height: 0;"><a href="javascript:void(0)"><img src="<?php echo esc_url(WT_O_IEW_PLUGIN_URL . '/assets/images/wt-close-button.png'); ?>" /></a></p>
			<img src="<?php echo esc_url(WT_O_IEW_PLUGIN_URL . '/assets/images/wt-error-icon.png'); ?>" />
			<h3><?php esc_html_e('Something went wrong', 'order-import-export-for-woocommerce'); ?></h3>
			<p style="color:#000;text-align: left;"><?php esc_html_e('We are unable to complete your request.Try reducing the import batch count to 5 or less and increasing the Maximum execution time in the ', 'order-import-export-for-woocommerce'); ?><a target="_blank" href="<?php echo esc_url(admin_url('admin.php?page=wt_import_export_for_woo_basic')); ?>"><?php esc_html_e('General settings', 'order-import-export-for-woocommerce'); ?></a>.</p>
			<p style="color:#000;text-align: left;"><?php esc_html_e(' If not resolved, contact the', 'order-import-export-for-woocommerce'); ?> <a target="_blank" href="https://www.webtoffee.com/contact/"><?php esc_html_e('support team', 'order-import-export-for-woocommerce'); ?></a> <?php esc_html_e('with the', 'order-import-export-for-woocommerce'); ?> <a target="_blank" href="<?php echo esc_url(admin_url('admin.php?page=wc-status&tab=logs')); ?>"><?php esc_html_e('WooCommerce fatal error log', 'order-import-export-for-woocommerce'); ?></a>, <?php esc_html_e('if any', 'order-import-export-for-woocommerce'); ?>.</p>
			<br />
			<a href="javascript:void(0)" onclick='wt_iew_basic_import.refresh_import_page();' class="button button-primary"><?php esc_html_e('Try again', 'order-import-export-for-woocommerce'); ?></a>
		</div>
	</div>


	<div class="wt_iew_import_step_main" style="width:100%; float: left">
		<?php
		foreach ($this->steps as $stepk => $stepv) {
		?>
			<div class="wt_iew_import_step wt_iew_import_step_<?php echo esc_attr($stepk); ?>" data-loaded="0"></div>
		<?php
		}
		?>
	</div>
</div>
<?php
include $wf_admin_view_path . "market.php";
include $wf_admin_view_path."admin-header-and-help.php";

?>
<script type="text/javascript">
	/* external modules can hook */
	function wt_iew_importer_validate_basic(action, action_type, is_previous_step) {
		var is_continue = true;
		<?php
		do_action('wt_iew_importer_validate_basic');
		?>
		return is_continue;
	}

	function wt_iew_importer_reset_form_data_basic() {
		<?php
		do_action('wt_iew_importer_reset_form_data_basic');
		?>
	}

	document.addEventListener('DOMContentLoaded', function() {
		var ctaHeaders = [
			'<?php esc_html_e("Get scheduled imports and exports", 'order-import-export-for-woocommerce'); ?>',
			'<?php esc_html_e("Did You Know?", 'order-import-export-for-woocommerce'); ?>',
			'<?php esc_html_e("Get Premium Support", 'order-import-export-for-woocommerce'); ?>',
			'<?php esc_html_e("99% Happy Customers 😊", 'order-import-export-for-woocommerce'); ?>'
		];

		var ctaContents = [
			'<?php esc_html_e("Upgrade to premium and enjoy scheduled imports and exports using WordPress cron and Server cron.", 'order-import-export-for-woocommerce'); ?> <a href="<?php echo esc_url("https://www.webtoffee.com/product/order-import-export-plugin-for-woocommerce/?utm_source=free_plugin_progressive_bar&utm_medium=basic_revamp&utm_campaign=Order_Import_Export" . WT_O_IEW_VERSION); ?>" style="color: blue;" target="_blank"><?php esc_html_e("Upgrade to pro now.", 'order-import-export-for-woocommerce'); ?></a>',	
			'<?php esc_html_e("With the premium version, you can import and export custom fields and meta data.", 'order-import-export-for-woocommerce'); ?> <a href="<?php echo esc_url("https://www.webtoffee.com/product/order-import-export-plugin-for-woocommerce/?utm_source=free_plugin_progressive_bar&utm_medium=basic_revamp&utm_campaign=Order_Import_Export" . WT_O_IEW_VERSION); ?>" style="color: blue;" target="_blank"><?php esc_html_e("Upgrade to pro now.", 'order-import-export-for-woocommerce'); ?></a>',
			'<?php esc_html_e("Experience our premium and priority support for hassle-free import/export of WooCommerce products.", 'order-import-export-for-woocommerce'); ?> <a href="<?php echo esc_url("https://www.webtoffee.com/product/order-import-export-plugin-for-woocommerce/?utm_source=free_plugin_progressive_bar&utm_medium=basic_revamp&utm_campaign=Order_Import_Export" . WT_O_IEW_VERSION); ?>" style="color: blue;" target="_blank"><?php esc_html_e("Get Premium Support.", 'order-import-export-for-woocommerce'); ?></a>',
			'<?php esc_html_e("We take pride in our 99% customer satisfaction rating and provide top-tier priority support to enhance your experience.", 'order-import-export-for-woocommerce'); ?> <a href="<?php echo esc_url("https://www.webtoffee.com/product/order-import-export-plugin-for-woocommerce/?utm_source=free_plugin_progressive_bar&utm_medium=basic_revamp&utm_campaign=Order_Import_Export" . WT_O_IEW_VERSION); ?>" style="color: blue;" target="_blank"><?php esc_html_e("Join 99% Happy Customers.", 'order-import-export-for-woocommerce'); ?></a>'
		];

		var currentIndex = 0;
		var ctaElement = document.getElementById('dynamic-cta-content');

		setInterval(function() {
			currentIndex = (currentIndex + 1) % ctaHeaders.length;

			ctaElement.innerHTML = '<img src="<?php echo esc_url(WT_O_IEW_PLUGIN_URL . '/assets/images/greenbulb.png'); ?>" style="margin-right: 5px;float:inline-start;">' +
				'<strong style="float:inline-start;">' + ctaHeaders[currentIndex] + '</strong><br>' +
				ctaContents[currentIndex];
		}, 10000); // Change content and header every 10 seconds
	});
</script>