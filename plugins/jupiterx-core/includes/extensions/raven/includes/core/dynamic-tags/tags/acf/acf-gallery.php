<?php
namespace JupiterX_Core\Raven\Core\Dynamic_Tags\Tags\ACF;

use Elementor\Core\DynamicTags\Data_Tag;
use JupiterX_Core\Raven\Core\Dynamic_Tags\Tags\ACF\Util as ACF_Utility;

defined( 'ABSPATH' ) || die();

class ACF_Gallery extends Data_Tag {

	public function get_name() {
		return 'acf-gallery';
	}

	public function get_title() {
		return esc_html__( 'ACF Gallery Field', 'jupiterx-core' );
	}

	public function get_group() {
		return 'acf';
	}

	public function get_categories() {
		return [ \Elementor\Modules\DynamicTags\Module::GALLERY_CATEGORY ];
	}

	public function get_panel_template_setting_key() {
		return 'key';
	}

	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function get_value( array $options = [] ) {
		$images = [];

		list( $field, $meta_key ) = ACF_Utility::get_tag_value_field( $this );

		if ( $field ) {
			$value = $field['value'];
		} else {
			// Field settings has been deleted or not available.
			$value = get_field( $meta_key );
		}

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $image ) {
				$images[] = [
					'id' => $image['ID'],
				];
			}
		}

		return $images;
	}

	protected function register_controls() {
		ACF_Utility::add_key_control( $this );
	}

	public function get_supported_fields() {
		return [
			'gallery',
		];
	}
}
