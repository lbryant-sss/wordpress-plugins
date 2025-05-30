<?php
/**
 * Render preview iFrame
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="field-wrap field-wrap-<?php echo esc_attr( $field['name'] ); ?> <?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : '' ); ?>">
	<iframe
		data-name="preview"
		class="preview-iframe"
		src="<?php echo esc_url( add_query_arg( $field['value'], admin_url() ) ); ?>"
		scrolling="no"
		style=" width: 100%; height: 500px;  overflow: hidden;"
	></iframe>
</div>
