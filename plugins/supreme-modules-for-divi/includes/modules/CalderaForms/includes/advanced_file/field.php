<?php
	$is_multiple = null;
if ( ! empty( $field['config']['multi_upload'] ) ) {
	$is_multiple .= ' multiple="multiple"';
}


	$uniqu_code = Caldera_Forms_Field_Util::generate_file_field_unique_id( $field, $form );


	$required_check = '';
if ( $field_required !== null ) {
	$required_check = 'required="required"';
	$field_required = null;
	$is_multiple   .= ' data-required="true"';

}
if ( empty( $field['config']['multi_upload_text'] ) ) {
	$field['config']['multi_upload_text'] = __( 'Add File', 'supreme-modules-for-divi' );
	if ( ! empty( $field['config']['multi_upload'] ) ) {
		$field['config']['multi_upload_text'] = __( 'Add Files', 'supreme-modules-for-divi' );
	}
}

	// Set allowed types.
	$accept_tag = array();
if ( ! empty( $field['config']['allowed'] ) ) {
	$allowed                    = array_map( 'trim', explode( ',', trim( $field['config']['allowed'] ) ) );
	$field['config']['allowed'] = array();
	foreach ( $allowed as $ext ) {
		$ext                          = trim( $ext, '.' );
		$file_type                    = wp_check_filetype( 'tmp.' . $ext );
		$field['config']['allowed'][] = $file_type['type'];
		$field['config']['allowed'][] = $file_type['ext'];
		$accept_tag[]                 = '.' . $ext;
	}
} else {
	$allowed                    = get_allowed_mime_types();
	$field['config']['allowed'] = array();
	foreach ( $allowed as $ext => $mime ) {
		$field['config']['allowed'][] = $mime;
		$accept_tag[]                 = '.' . str_replace( '|', ',.', $ext );
	}
}

	// Fix allowed types
	// @see https://github.com/CalderaWP/Caldera-Forms/issues/2471
if ( ! empty( $field['config']['allowed'] ) ) {
	if ( in_array( 'audio/mpeg', $field['config']['allowed'] ) ) {
		$field['config']['allowed'][] = 'audio/mp3';
	}
	$field['config']['allowed'] = array_unique( $field['config']['allowed'] );
}

if ( ! empty( $accept_tag ) ) {
	$accept_tag = array_unique( $accept_tag );
}

	$accept_tag = 'accept="' . esc_attr( implode( ',', $accept_tag ) ) . '"';
	$field['config']['max_size'] = wp_max_upload_size();

	$field['config']['notices'] = array(
		'file_exceeds_size_limit' => esc_html__( 'File exceeds the maximum upload size for this site.', 'supreme-modules-for-divi' ),
		'zero_byte_file'          => esc_html__( 'This file is empty. Please try another.', 'supreme-modules-for-divi' ),
		'invalid_filetype'        => esc_html__( 'This file type is not allowed. Please try another.', 'supreme-modules-for-divi' ),
	);

	?><?php echo et_core_intentionally_unescaped( $wrapper_before, 'html' ); ?>
	<?php echo et_core_intentionally_unescaped( $field_label, 'html' ); ?>
		<?php echo et_core_intentionally_unescaped( $field_before, 'html' ); ?>
			<div
					id="<?php echo esc_attr( $field_id ); ?>_file_list"
					data-id="<?php echo esc_attr( $field_id ); ?>"
					data-field="<?php echo esc_attr( $field_base_id ); ?>"
					class="cf-multi-uploader-list"
			></div>

			<button
					id="<?php echo esc_attr( $field_id ); ?>_trigger"
					type="button"
					class="btn btn-block cf-uploader-trigger dsm-cf-advanced-button et_pb_button"
					data-parent="<?php echo esc_attr( $field_id ); ?>"
			><?php echo esc_html( $field['config']['multi_upload_text'] ); ?></button>

			<input
					style="display:none;" <?php echo esc_attr( $accept_tag ); ?>
					class="cf-multi-uploader"
					data-config="<?php echo esc_attr( wp_json_encode( $field['config'] ) ); ?>"
					data-controlid="<?php echo esc_attr( $uniqu_code ); ?>" <?php echo esc_attr( $field_placeholder ); ?> <?php echo esc_attr( $is_multiple ); ?>
					type="file"
					data-field="<?php echo esc_attr( $field_base_id ); ?>"
					id="<?php echo esc_attr( $field_id ); ?>"
					name="<?php echo esc_attr( $field_name ); ?>" <?php echo esc_attr( $field_required ); ?>>
			<input
					style="display:none;"
					type="text"
					id="<?php echo esc_attr( $field_id ); ?>_validator"
					data-field="<?php echo esc_attr( $field_base_id ); ?>"
					data-parsley-file-type="true" 
					<?php
					echo esc_attr( $required_check );
					if ( $required_check ) :
						echo 'data-required="true"';
endif;
					?>
			>
			<input
					type="hidden"
					name="<?php echo esc_attr( $field_name ); ?>"
					value="<?php echo esc_attr( $uniqu_code ); ?>"
			>
			<?php //phpcs:ignore
			echo et_core_intentionally_unescaped( $field_caption, 'html' ); ?>
	<?php //phpcs:ignore
echo et_core_intentionally_unescaped( $field_after, 'html' ); ?>
<?php //phpcs:ignore
echo et_core_intentionally_unescaped( $wrapper_after, 'html' ); ?>
