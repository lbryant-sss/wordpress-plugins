<?php
// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$attributes = array();

$type = 'text';

if ( 1 === (int) pods_v( 'email_html5', $options ) ) {
	$type = 'email';
}

$attributes['type']     = $type;
$attributes['value']    = $value;
$attributes['tabindex'] = 2;
$attributes             = PodsForm::merge_attributes( $attributes, $name, $form_field_type, $options );
?>
	<input<?php PodsForm::attributes( $attributes, $name, $form_field_type, $options ); ?> />
<?php
PodsForm::regex( $form_field_type, $options );
