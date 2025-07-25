<?php

/**
 * Class SiteOrigin_Widget_Field_Number
 */
class SiteOrigin_Widget_Field_Number extends SiteOrigin_Widget_Field_Text_Input_Base {
	/**
	 * The minimum value of the allowed range.
	 *
	 * @var float
	 */
	protected $min;

	/**
	 * The maximum value of the allowed range.
	 *
	 * @var float
	 */
	protected $max;

	/**
	 * The step size when moving in the range.
	 *
	 * @var float
	 */
	protected $step;

	/**
	 * The measurement unit this number uses.
	 *
	 * @var string
	 */
	protected $unit;

	/**
	 * Whether to apply abs() when saving to ensure only positive numbers are possible.
	 *
	 * @var bool
	 */
	protected $abs;

	protected function get_default_options() {
		return array(
			'input_type' => 'number',
		);
	}

	protected function get_input_attributes() {
		$input_attributes = array(
			'step' => $this->step,
			'min'  => $this->min,
			'max'  => $this->max,
		);

		return array_filter( $input_attributes );
	}

	protected function get_input_classes() {
		return array(
			'siteorigin-widget-input',
			'siteorigin-widget-input-number',
		);
	}

	protected function render_after_field( $value, $instance ) {
		if ( ! empty( $this->unit ) ) {
			echo '<span class="siteorigin-widget-input-number-unit">' . esc_html( $this->unit ) . '</span>';
		}

		parent::render_after_field( $value, $instance );
	}

	protected function sanitize_field_input( $value, $instance ) {
		if ( ! is_numeric( $value ) ) {
			return false;
		}

		if ( ! empty( $this->min ) ) {
			$value = max( $value, $this->min );
		}

		if ( ! empty( $this->max ) && ! empty( $value ) ) {
			$value = min( $value, $this->max );
		}

		if ( ! empty( $this->abs ) && ! empty( $value ) ) {
			$value = abs( $value );
		}

		return (float) $value;
	}
}
