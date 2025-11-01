<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
/* delete after redirect */
if(isset($_GET['wt_iew_delete_log'])) 
{
	// Verify nonce for security - deletion already processed in history.php, this is just for cleanup redirect
	$nonce = isset($_GET['_wpnonce']) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
	if ( wp_verify_nonce( $nonce, WT_IEW_PLUGIN_ID_BASIC ) )
	{
		?>
		<script type="text/javascript">
			window.location.href='<?php echo esc_url(admin_url('admin.php?page='.$this->module_id.'_log')); ?>';
		</script>
		<?php
	}
}
?>
<div class="wt_iew_history_page">
	<h2 class="wp-heading-inline"><?php esc_html_e('Import Logs', 'product-import-export-for-woo');?></h2>
	<p>
		<?php esc_html_e('Lists developer logs mostly required for debugging purposes. Options to view detailed logs are available along with delete and download(that can be shared with the support team in case of issues).', 'product-import-export-for-woo');?>
	</p>

	<?php
	$log_path=Wt_Import_Export_For_Woo_Basic_Log::$log_dir;
	$log_files = glob($log_path.'/*'.'.log');
	if(is_array($log_files) && count($log_files)>0)
	{
            foreach ($log_files as $key => $value) {                  
                $date_time = str_replace('.log','',substr($value, strrpos($value, '_') + 1));
                $d = DateTime::createFromFormat('Y-m-d H i s A', $date_time);
                if ($d == false) {
                    $index = $date_time;
                } else {
                   $index = $d->getTimestamp();
                }
                $indexed_log_files[$index] = $value;                                
            }           
		krsort($indexed_log_files);
                $log_files = $indexed_log_files;

		?>
	<div class="wt_iew_bulk_action_box">
		<select class="wt_iew_bulk_action wt_iew_select">
			<option value=""><?php esc_html_e( 'Bulk Actions', 'product-import-export-for-woo' ); ?></option>
			<option value="delete"><?php esc_html_e( 'Delete', 'product-import-export-for-woo' ); ?></option>
		</select>
		<button class="button button-primary wt_iew_bulk_action_logs_btn" type="button" style="float:left;"><?php esc_html_e( 'Apply', 'product-import-export-for-woo' ); ?></button>
	</div>
		<table class="wp-list-table widefat fixed striped history_list_tb log_list_tb">
		<thead>
			<tr>
				<th width="100">
					<input type="checkbox" name="" class="wt_iew_history_checkbox_main">
					<?php esc_html_e("No.", 'product-import-export-for-woo'); ?>
				</th>
				<th class="log_file_name_col"><?php esc_html_e("File", 'product-import-export-for-woo'); ?></th>
				<th><?php esc_html_e("Actions", 'product-import-export-for-woo'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		$i = 0;
		foreach($log_files as $log_file)
		{
			$i++;
			$file_name=basename($log_file);
			?>
			<tr>
				<th>
					<input type="checkbox" value="<?php echo esc_attr($file_name);?>" name="logfile_name[]" class="wt_iew_history_checkbox_sub">
					<?php echo absint($i);?>						
				</td>
				<td class="log_file_name_col"><a class="wt_iew_view_log_btn" data-log-file="<?php echo esc_attr($file_name);?>"><?php echo esc_html($file_name); ?></a></td>
				<td>
					<a class="wt_iew_delete_log" data-href="<?php echo esc_url(str_replace('_log_file_', $file_name, $delete_url));?>"><?php esc_html_e('Delete', 'product-import-export-for-woo'); ?></a>
					| <a class="wt_iew_view_log_btn" data-log-file="<?php echo esc_attr($file_name);?>"><?php esc_html_e("View", 'product-import-export-for-woo');?></a>
					| <a class="wt_iew_download_log_btn" href="<?php echo esc_url(str_replace('_log_file_', $file_name, $download_url));?>"><?php esc_html_e("Download", 'product-import-export-for-woo');?></a>
				</td>
			</tr>
			<?php
		}
		?>
		</tbody>
		</table>
		<?php
	}else
	{
		?>
		<h4 class="wt_iew_history_no_records"><?php esc_html_e( "No logs found.", 'product-import-export-for-woo' ); ?>
			<?php if ( Wt_Import_Export_For_Woo_Basic_Common_Helper::get_advanced_settings( 'enable_import_log' ) == 0 ): ?>		
				<span> <?php esc_html_e( 'Please enable import log under', 'product-import-export-for-woo' ); ?> <a target="_blank" href="<?php echo esc_url(admin_url( 'admin.php?page=wt_import_export_for_woo_basic' )); ?>"><?php esc_html_e( 'settings', 'product-import-export-for-woo' ); ?></a></span>		
			<?php endif; ?>
		</h4>
		<?php
	}
	?>
</div>