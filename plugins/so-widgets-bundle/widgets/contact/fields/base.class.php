<?php

abstract class SiteOrigin_Widget_ContactForm_Field_Base {
	/**
	 * The options for this field. Used when enqueueing styles and scripts and rendering the field.
	 *
	 * @var array
	 */
	protected $options;
	public $type;

	public function __construct( $options ) {
		$this->options = $options;
		$this->init();
	}

	private function init() {
		$this->initialize( $this->options );
	}

	protected function initialize( $options ) {
	}

	abstract protected function render_field( $options );

	public function render() {
		$this->render_field( $this->options );
	}

	public static function add_custom_attrs( $type, $options = array() ) {
		$attr = array();

		// If the field has a description, add the aria-describedby attribute.
		if (
			! empty( $options ) &&
			is_array( $options ) &&
			! empty( $options['has_description'] )
		) {
			$attr['aria-describedby'] = esc_attr( $options['field_id'] . '-description' );
		}

		$attr = apply_filters(
			'siteorigin_widgets_contact_field_attr',
			$attr,
			$type,
			$options
		);

		foreach ( $attr as $k => $v ) {
			echo siteorigin_sanitize_attribute_key( $k ) . '="' . esc_attr( $v ) . '" ';
		}
	}
}
