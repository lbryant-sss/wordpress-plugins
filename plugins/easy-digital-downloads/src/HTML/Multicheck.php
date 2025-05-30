<?php
/**
 * Multicheck HTML Element
 *
 * @package EDD
 * @subpackage HTML
 * @since 3.3.5
 */

namespace EDD\HTML;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit; // @codeCoverageIgnore

/**
 * Multicheck class.
 */
class Multicheck extends Base {

	/**
	 * Gets the HTML for the multicheck.
	 *
	 * @since 3.3.5
	 * @return string
	 */
	public function get() {
		$fieldset_classes = array( $this->args['name'] );
		if ( $this->is_sortable() ) {
			$fieldset_classes[] = 'edd-sortable-list';
		}
		ob_start();
		if ( $this->is_sortable() ) {
			$order = $this->args['sortable']['order'];
			if ( is_string( $order ) ) {
				$order = explode( ',', $order );
			}
			?>
			<input type="hidden" name="<?php echo esc_attr( $this->args['sortable']['name'] ); ?>" class="edd-order" value="<?php echo esc_attr( implode( ',', $order ) ); ?>" />
			<?php
		}
		?>
		<fieldset class="<?php echo esc_attr( $this->get_css_class_string( $fieldset_classes ) ); ?>">
			<?php if ( ! empty( $this->args['legend'] ) ) : ?>
				<legend><?php echo wp_kses_post( $this->args['legend'] ); ?></legend>
			<?php endif; ?>
			<?php
			foreach ( $this->args['options'] as $key => $data ) {
				$label   = ! empty( $data['label'] ) ? $data['label'] : $data;
				$checked = (int) (bool) ! empty( $data['checked'] );
				$classes = array(
					'edd-form-group__control--wrap',
				);
				if ( ! empty( $data['classes'] ) ) {
					$classes = array_merge( $classes, $data['classes'] );
				}
				if ( ! empty( $this->args['sortable']['enabled'] ) ) {
					$classes[] = 'edd-sortable-list__item';
				}
				$inner_classes = array(
					'edd-form-group__control',
				);
				if ( ! empty( $this->args['toggle'] ) ) {
					$inner_classes[] = 'edd-toggle';
				}
				?>
				<div class="<?php echo esc_attr( $this->array_to_css_string( $classes ) ); ?>" data-key="<?php echo esc_attr( $key ); ?>">
					<div class="<?php echo esc_attr( $this->array_to_css_string( $inner_classes ) ); ?>">
						<?php $this->do_icon( $data ); ?>
						<input type="hidden" name="<?php echo esc_attr( $this->args['name'] ); ?>[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $checked && ! empty( $data['disabled'] ) ? 1 : 0 ); ?>">
						<input
							type="checkbox"
							name="<?php echo esc_attr( $this->args['name'] ); ?>[<?php echo esc_attr( $key ); ?>]"
							value="1"
							id="<?php echo esc_attr( $this->args['name'] . '[' . $key . ']' ); ?>"
							<?php checked( $checked, 1 ); ?>
							<?php disabled( ! empty( $data['disabled'] ), true ); ?>
							<?php wp_readonly( ! empty( $data['readonly'] ), true ); ?>
						>
						<div class="edd-form-group__control__label">
							<label for="<?php echo esc_attr( $this->args['name'] . '[' . $key . ']' ); ?>">
								<?php echo wp_kses_post( $label ); ?>
							</label>
							<?php $this->do_tooltip( $data ); ?>
						</div>
					</div>
					<?php if ( ! empty( $data['desc'] ) ) : ?>
						<p class="edd-form-group__help description"><?php echo wp_kses_post( $data['desc'] ); ?></p>
					<?php endif; ?>
				</div>
				<?php
			}
			?>
		</fieldset>
		<?php

		return ob_get_clean();
	}

	/**
	 * Default arguments for the checkbox.
	 *
	 * @since 3.3.5
	 * @return array
	 */
	protected function defaults() {
		return array(
			'name'     => null,
			'class'    => 'multicheck',
			'options'  => array(), // key => array( label => label, disabled => bool, checked => bool ).
			'legend'   => '',
			'toggle'   => false,
			'sortable' => array(
				'enabled' => false,
				'order'   => array(),
				'name'    => '',
			),
		);
	}

	/**
	 * Gets the base CSS classes for the select element.
	 *
	 * @since 3.3.5
	 * @return array
	 */
	protected function get_base_classes(): array {
		$classes = array(
			'edd-form-group',
			'edd-multicheck',
		);

		if ( ! empty( $this->args['toggle'] ) ) {
			$classes[] = 'edd-form-group--has-toggle';
		}

		return $classes;
	}

	/**
	 * Outputs the icon for the multicheck.
	 *
	 * @since 3.3.5
	 * @param array $data The data for the icon.
	 */
	private function do_icon( $data ) {
		if ( empty( $data['icon'] ) ) {
			return;
		}
		echo $data['icon'];
	}

	/**
	 * Outputs the tooltip for the multicheck.
	 *
	 * @since 3.3.5
	 * @param array $data The data for the tooltip.
	 */
	private function do_tooltip( $data ) {
		if ( empty( $data['tooltip'] ) ) {
			return;
		}
		$tooltip = new Tooltip( $data['tooltip'] );
		$tooltip->output();
	}

	/**
	 * Determines if the multicheck is sortable.
	 *
	 * @since 3.3.8
	 * @return bool
	 */
	private function is_sortable() {
		return ! empty( $this->args['sortable']['enabled'] ) && ! empty( $this->args['sortable']['name'] );
	}
}
