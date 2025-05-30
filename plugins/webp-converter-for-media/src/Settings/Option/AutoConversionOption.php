<?php

namespace WebpConverter\Settings\Option;

/**
 * {@inheritdoc}
 */
class AutoConversionOption extends OptionAbstract {

	const OPTION_NAME = 'auto_conversion';

	/**
	 * {@inheritdoc}
	 */
	public function get_name(): string {
		return self::OPTION_NAME;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_form_name(): string {
		return OptionAbstract::FORM_TYPE_GENERAL;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_type(): string {
		return OptionAbstract::OPTION_TYPE_TOGGLE;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function get_label(): string {
		return __( 'Conversion of new images', 'webp-converter-for-media' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_info(): string {
		return __( 'Automatically convert new images when uploading to Media Library', 'webp-converter-for-media' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_available_values( array $settings ): ?array {
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_value(): string {
		return 'yes';
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate_value( $current_value, ?array $available_values = null, ?array $disabled_values = null ): string {
		return ( $current_value === 'yes' ) ? 'yes' : '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function sanitize_value( $current_value ): string {
		return $this->validate_value( $current_value );
	}
}
