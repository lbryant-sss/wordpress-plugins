<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Reset_Password_Form extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'fields';
	}

	public function get_fields() {
		return [ 'label', 'placeholder' ];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'label':
				return esc_html__( 'Raven Reset Password: Item Label', 'jupiterx-core' );

			case 'placeholder':
				return esc_html__( 'Raven Reset Password: Item Placeholder', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'label':
				return 'LINE';

			case 'placeholder':
				return 'LINE';

			default:
				return '';
		}
	}
}
