<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 3.0
 */
class FrmFieldRadio extends FrmFieldType {

	/**
	 * @var string
	 * @since 3.0
	 */
	protected $type = 'radio';

	/**
	 * @var bool
	 * @since 3.0
	 */
	protected $holds_email_values = true;

	/**
	 * Does the html for this field label need to include "for"?
	 *
	 * @var bool
	 * @since 3.06.01
	 */
	protected $has_for_label = false;

	/**
	 * @var bool
	 */
	protected $array_allowed = true;

	protected function input_html() {
		return $this->multiple_input_html();
	}

	protected function include_form_builder_file() {
		return $this->include_front_form_file();
	}

	/**
	 * @return bool[]
	 */
	protected function field_settings_for_type() {
		return array(
			'invalid' => true,
		);
	}

	/**
	 * Get the type of field being displayed.
	 *
	 * @since 4.02.01
	 * @return array
	 */
	public function displayed_field_type( $field ) {
		return array(
			$this->type => true,
		);
	}

	/**
	 * @return array
	 */
	protected function extra_field_opts() {
		$form_id = $this->get_field_column( 'form_id' );

		return array(
			'align' => FrmStylesController::get_style_val( 'radio_align', ( empty( $form_id ) ? 'default' : $form_id ) ),
		);
	}

	/**
	 * @return string[]
	 */
	protected function new_field_settings() {
		return array(
			'options' => serialize(
				array(
					__( 'Option 1', 'formidable' ),
					__( 'Option 2', 'formidable' ),
				)
			),
		);
	}

	/**
	 * @since 4.06
	 *
	 * @return void
	 */
	protected function show_priority_field_choices( $args = array() ) {
		include FrmAppHelper::plugin_path() . '/classes/views/frm-fields/back-end/radio-images.php';
	}

	/**
	 * @return string
	 */
	protected function include_front_form_file() {
		return FrmAppHelper::plugin_path() . '/classes/views/frm-fields/front-end/radio-field.php';
	}

	/**
	 * @return bool
	 */
	protected function show_readonly_hidden() {
		return true;
	}
}
