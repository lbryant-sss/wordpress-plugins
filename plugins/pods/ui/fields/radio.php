<?php
// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$options['data'] = (array) pods_v( 'data', $options, [] );

if ( 1 === (int) pods_v( 'grouped', $options, 0 ) ) {
	?>
	<div class="pods-pick-values pods-pick-radio">
	<ul>
	<?php
}

$counter        = 1;
$primary_name   = $name;
$primary_id     = 'pods-form-ui-' . PodsForm::clean( $name );
$selection_made = false;

foreach ( $options['data'] as $val => $label ) {
	if ( is_array( $label ) ) {
		if ( isset( $label['label'] ) ) {
			$label = $label['label'];
		} else {
			$label = $val;
		}
	}

	$attributes = array();

	$attributes['type'] = 'radio';

	$attributes['checked']  = null;
	$attributes['tabindex'] = 2;

	if ( ! $selection_made && ( $val == $value || ( is_array( $value ) && in_array( $val, $value ) ) ) ) {
		$attributes['checked'] = 'CHECKED';
		$selection_made        = true;
	}

	$attributes['value'] = $val;

	$attributes = PodsForm::merge_attributes( $attributes, $name, $form_field_type, $options );

	$indent = '';

	$indent_count = substr_count( $label, '&nbsp;&nbsp;&nbsp;' );

	if ( 0 < $indent_count ) {
		$label = str_replace( '&nbsp;&nbsp;&nbsp;', '', $label );

		$indent = ' style="margin-left:' . ( 18 * $indent_count ) . 'px;"';
	}

	if ( (bool) pods_v( 'readonly', $options, false ) ) {
		$attributes['readonly'] = 'READONLY';

		$attributes['class'] .= ' pods-form-ui-read-only';
	}

	if ( 1 < count( $options['data'] ) ) {
		$attributes['id'] = $primary_id . $counter;
	}

	if ( 1 === (int) pods_v( 'grouped', $options, 0 ) ) {
		?>
		<li>
		<?php
	}
	?>
	<div class="pods-field pods-boolean"<?php echo $indent; ?>>
		<input<?php PodsForm::attributes( $attributes, $name, $form_field_type, $options ); ?> />
		<?php
		if ( 0 < strlen( $label ) ) {
			$help = pods_v( 'help', $options );

			if ( 1 === (int) pods_v( 'grouped', $options, 0 ) || empty( $help ) ) {
				$help = '';
			}

			echo PodsForm::label( $attributes['id'], $label, $help );
		}
		?>
	</div>
	<?php

	if ( 1 === (int) pods_v( 'grouped', $options, 0 ) ) {
		?>
		</li>
		<?php
	}

	$counter ++;
}//end foreach

if ( 1 === (int) pods_v( 'grouped', $options, 0 ) ) {
	?>
	</ul>
	</div>
	<?php
}
