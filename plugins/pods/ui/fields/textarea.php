<?php
// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$type                   = 'textarea';
$attributes             = array();
$attributes['tabindex'] = 2;
$attributes             = PodsForm::merge_attributes( $attributes, $name, $form_field_type, $options );

if ( pods_v( 'readonly', $options, false ) ) {
	$attributes['readonly'] = 'READONLY';

	$attributes['class'] .= ' pods-form-ui-read-only';
}
?>
	<textarea<?php PodsForm::attributes( $attributes, $name, $form_field_type, $options ); ?>><?php echo esc_textarea( $value ); ?></textarea>
<?php
PodsForm::regex( $form_field_type, $options );
