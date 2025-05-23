<?php
// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

wp_enqueue_script( 'jquery-ui-slider' );
pods_form_enqueue_style( 'pods-styles' );

if ( is_array( $value ) ) {
	$value = implode( ',', $value );
}

if ( strlen( $value ) < 1 ) {
	$value = 0;
}

$values = explode( ',', $value );
$values = array(
	(int) pods_v( 0, $values, pods_v( $form_field_type . '_min', $options, 0, true ) ),
	(int) pods_v( 1, $values, pods_v( $form_field_type . '_max', $options, 100, true ) ),
);

$values[0] = max( $values[0], (int) pods_v( $form_field_type . '_min', $options, 0 ) );
$values[1] = min( $values[1], (int) pods_v( $form_field_type . '_min', $options, 100 ) );

if ( 0 === pods_v( $form_field_type . '_range', $options, 0 ) ) {
	$value        = $values[0];
	$output_value = $value;
} else {
	$value        = implode( ',', $values );
	$output_value = implode( ' - ', $values );
}

$attributes          = array();
$attributes['type']  = 'hidden';
$attributes['value'] = $value;
$attributes          = PodsForm::merge_attributes( $attributes, $name, $form_field_type, $options );
?>
<input<?php PodsForm::attributes( $attributes, $name, $form_field_type, $options ); ?> />

<div class="pods-slider-field pods-compat-container">
	<div id="<?php echo esc_js( pods_js_name( $attributes['id'] ) ); ?>-range" class="pods-slider-range"></div>
	<div id="<?php echo esc_js( pods_js_name( $attributes['id'] ) ); ?>-amount-display" class="pods-slider-field-display">
		<?php echo $output_value; ?>
	</div>
</div>

<script>
	jQuery( function ( $ ) {
		$( "#<?php echo esc_js( pods_js_name( $attributes['id'] ) ); ?>-range" ).slider( {
			orientation : '<?php echo esc_js( pods_v( $form_field_type . '_orientation', $options, 'horizontal' ) ); ?>',
			min         : <?php echo esc_js( pods_v( $form_field_type . '_min', $options, 0 ) ); ?>,
			max         : <?php echo esc_js( pods_v( $form_field_type . '_max', $options, 100 ) ); ?>,
			step        : <?php echo esc_js( pods_v( $form_field_type . '_step', $options, 1 ) ); ?>,

			<?php
			if ( 1 === (int) pods_v( $form_field_type . '_range', $options, 0 ) ) {
				?>
				range  : true,
				values : [
				<?php echo esc_js( $values[0] ); ?>,
				<?php echo esc_js( $values[1] ); ?>
			],
			slide  : function ( event, ui ) {
				$( "#<?php echo esc_js( pods_js_name( $attributes['id'] ) ); ?>" ).val( ui.values[0] + ',' + ui.values[1] );
				$( "#<?php echo esc_js( pods_js_name( $attributes['id'] ) ); ?>-amount-display" ).html( ui.values[0] + ' - ' + ui.values[1] );
			}
			<?php
			} else {
				?>
				range  : false,
				value  : <?php echo esc_js( $value ); ?>,
			slide  : function ( event, ui ) {
				$( "#<?php echo esc_js( pods_js_name( $attributes['id'] ) ); ?>" ).val( ui.value );
				$( "#<?php echo esc_js( pods_js_name( $attributes['id'] ) ); ?>-amount-display" ).html( ui.value );
			}
			<?php
			}//end if
			?>
			} );
	} );
</script>
