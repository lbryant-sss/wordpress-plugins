<?php
/**
 * Framework code_editor field file.
 *
 * @link https://shapedplugin.com
 * @since 2.0.0
 *
 * @package team-free
 * @subpackage team-free/framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.

if ( ! class_exists( 'TEAMFW_Field_code_editor' ) ) {
	/**
	 *
	 * Field: code_editor
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class TEAMFW_Field_code_editor extends TEAMFW_Fields {
		/**
		 * Field constructor.
		 *
		 * @param array  $field The field type.
		 * @param string $value The values of the field.
		 * @param string $unique The unique ID for the field.
		 * @param string $where To where show the output CSS.
		 * @param string $parent The parent args.
		 */
		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		/**
		 * Render field
		 *
		 * @return void
		 */
		public function render() {

			$default_settings = array(
				'tabSize'     => 2,
				'lineNumbers' => true,
				'theme'       => 'default',
			);

			$settings = ( ! empty( $this->field['settings'] ) ) ? $this->field['settings'] : array();
			$settings = wp_parse_args( $settings, $default_settings );

			echo wp_kses_post( $this->field_before() );
			echo '<textarea name="' . esc_attr( $this->field_name() ) . '"' . $this->field_attributes() . ' data-editor="' . esc_attr( json_encode( $settings ) ) . '">' . $this->value . '</textarea>'; // phpcs:ignore
			echo wp_kses_post( $this->field_after() );
		}

		/**
		 * Enqueue function
		 *
		 * @return void
		 */
		public function enqueue() {
			// Enqueue code-mirror.
			wp_enqueue_script( 'code-editor' );
			wp_enqueue_style( 'code-editor' );
		}
	}
}
