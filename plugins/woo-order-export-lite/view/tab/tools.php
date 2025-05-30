<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$modes = array(
	WC_Order_Export_Manage::EXPORT_NOW,
	WC_Order_Export_Manage::EXPORT_PROFILE,
	WC_Order_Export_Manage::EXPORT_SCHEDULE,
	WC_Order_Export_Manage::EXPORT_ORDER_ACTION,
);

$settings_export = array();
$setting_names   = array();
foreach ( $modes as $mode ) {
	$setting_name             = WC_Order_Export_Manage::get_settings_name_for_mode( $mode );
	$setting_names[ $mode ]   = $setting_name;
	$settings_export[ $mode ] = WC_Order_Export_Manage::get( $mode );
}

$type_labels = apply_filters( 'woe_tools_page_get_type_labels', array() );

$user_can_add_custom_php = intval(WC_Order_Export_Admin::user_can_add_custom_php());
?>
<div class="weo_clearfix"></div>
<div id="woe-admin" class="container-fluid wpcontent">
    <form>
		<?php wp_nonce_field( 'woe_nonce', 'woe_nonce' ); ?>
        <div class="woe-tab" id="woe-tab-general">
            <div class="woe-box woe-box-main">
                <h3 class="woe-box-title"><?php esc_html_e( 'Export settings', 'woo-order-export-lite' ) ?></h3>
                <div class="row">
                    <div class="col-sm-12 form-group">
                        <p><?php esc_html_e( 'Copy these settings and use it to migrate plugin to another WordPress install.',
								'woo-order-export-lite' ) ?></p>
                    </div>
                    <div class="col-sm-8 form-group woe-input-simple">
                        <select id="tools-export-selector">
                            <option data-json='<?php echo json_encode( $settings_export,
								JSON_PRETTY_PRINT | JSON_HEX_APOS ) ?>'><?php esc_html_e( 'All',
									'woo-order-export-lite' ) ?></option>
                            <option data-json='<?php echo json_encode( $settings_export[ WC_Order_Export_Manage::EXPORT_NOW ],
								JSON_PRETTY_PRINT | JSON_HEX_APOS ) ?>'>
								<?php esc_html_e( 'Export now', 'woo-order-export-lite' ) ?></option>
							<?php foreach ( $type_labels as $group => $label ): ?>
                                <optgroup label="<?php echo esc_attr($label) ?>"></optgroup>
								<?php foreach ( $settings_export[ $group ] as $item_id => $item ): ?>
                                    <option data-json='<?php echo json_encode( $item,
										JSON_PRETTY_PRINT | JSON_HEX_APOS ) ?>'>
										<?php echo esc_html($item_id . " - " . ( isset( $item['title'] ) ? $item['title'] : '' )) ?></option>
								<?php endforeach; ?>
							<?php endforeach; ?>
                        </select>
                        <textarea rows="10" id="tools-export-text" class='tools-textarea'></textarea>
                    </div>
                        <p><?php esc_html_e( 'Just click inside the textarea and copy (Ctrl+C)',
								'woo-order-export-lite' ) ?></p>
                </div>
            </div>
        </div>
    </form>
    <form method="post">
        <div class="woe-tab" id="woe-tab-general">
            <div class="woe-box woe-box-main">
                <h3 class="woe-box-title"><?php esc_html_e( 'Import settings', 'woo-order-export-lite' ) ?></h3>
                <div class="row">
                    <div class="col-sm-12 form-group">
                        <p><?php esc_html_e( 'Paste text into this field to import settings into the current WordPress install.',
								'woo-order-export-lite' ) ?></p>
                    </div>
                    <div class="col-sm-8 form-group woe-input-simple">
                        <textarea rows="10" id="tools-import-text" name="tools-import"></textarea>
                    </div>
                        <p ><?php esc_html_e( 'This process will overwrite your settings for "Advanced Order Export For WooCommerce" !',
								'woo-order-export-lite' ) ?></p>
                </div>
                <div class="row">
                    <div class="col-sm-2 form-group col-md-offset-7">
                        <div id="import-error" style='display:none;color:red;font-size: 120%;padding-bottom: 10px;'>
                            <label class="error-message"></label>
                        </div>

                        <input type="submit" class="woe-btn-tools"
                               value="<?php esc_html_e( 'Import', 'woo-order-export-lite' ) ?>" name="woe-tools-import"
                               id="submit-import">

                        <div id="Settings_updated"
                             style='display:none;color:green;font-size: 120%;padding-bottom: 10px;'><?php esc_html_e( "Settings were successfully updated!",
								'woo-order-export-lite' ) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
	jQuery( function ( $ ) {
	    function showError($e) {
	        var message = '';
	        if ( $e instanceof Error) {
                message = $e.message;
            } else if ( $e instanceof String) {
	            message = $e;
            } else {
	            return;
            }

	        $('#import-error .error-message').text(message);
	        $('#import-error').show().delay( 5000 ).fadeOut();
        }

		jQuery( '#wpbody .tools-textarea' ).click( function () {
			jQuery( this ).select();
		} );

		jQuery( '#tools-import-text' ).on( 'input propertychange', function () {
			var $textarea = jQuery( this ).val();
			var disable = (
				$textarea.length == ''
			);
			$( "#submit-import" ).prop( "disabled", disable );
		} );

		jQuery( '#submit-import' ).on( 'click', function ( e ) {
            try {
                var importJson = JSON.parse($('#tools-import-text').val());
            } catch ($e) {
                showError($e);
                e.preventDefault();
                return;
            }

            let userCanAddCustomPhp = <?php echo esc_attr($user_can_add_custom_php); ?>;
            let defaultWarning = '<?php esc_attr_e( 'Are you sure to continue?', 'woo-order-export-lite' ) ?>';
            let customPHPWarning = '<?php esc_attr_e( 'Your settings uses custom PHP code. This code will be removed during import. Proceed?',
                'woo-order-export-lite' ) ?>';
            let importHasPhpCode = false;

            //check if import json has custom php code
            if (!userCanAddCustomPhp) {
                if (importJson.hasOwnProperty('custom_php') && importJson.custom_php !== '0' && importJson.custom_php_code ||
                importJson.hasOwnProperty('now') && importJson.now.custom_php !== '0' && importJson.now.custom_php_code) {
                    importHasPhpCode = true;
                }
                if (!importHasPhpCode && importJson.hasOwnProperty('profiles')) {
                    for (const index in importJson.profiles) {
                        if (importJson.profiles[index].custom_php !== '0' && importJson.profiles[index].custom_php_code) {
                            importHasPhpCode = true;
                            break;
                        }
                    }
                }
                if (!importHasPhpCode && importJson.hasOwnProperty('cron')) {
                    for (const index in importJson.cron) {
                        if (importJson.cron[index].custom_php !== '0' && importJson.cron[index].custom_php_code) {
                            importHasPhpCode = true;
                            break;
                        }
                    }
                }
                if (!importHasPhpCode && importJson.hasOwnProperty('order-action')) {
                    for (const index in importJson['order-action']) {
                        if (importJson['order-action'][index].custom_php !== '0' && importJson['order-action'][index].custom_php_code) {
                            importHasPhpCode = true;
                            break;
                        }
                    }
                }
            }

			if ( ! confirm( !userCanAddCustomPhp && importHasPhpCode ? customPHPWarning : defaultWarning ) ) {
				e.preventDefault();
				$( document.activeElement ).blur();
			} else {
				$( '#Settings_updated' ).hide();
				var data = $( '#woe-admin form' ).serialize();
				data = data + "&action=order_exporter&method=save_tools&tab=tools";
				$.post( ajaxurl, data, function ( response ) {
					$( '#tools-import-text' ).val( '' );
					$( '#Settings_updated' ).show().delay( 5000 ).fadeOut();
				});
				return false;
			}
		} );

		jQuery( '#tools-export-selector' ).change( function () {
			jQuery( '#tools-export-text' ).val( jQuery( this ).find( ':selected' ).attr( 'data-json' ) );
		} ).change();

	} );
</script>