<?php
/**
 * Text HTML Element
 *
 * @package EDD
 * @subpackage HTML
 * @since 3.2.8
 */

namespace EDD\HTML;

defined( 'ABSPATH' ) || exit;

/**
 * Class Text
 *
 * @since 3.2.8
 * @package EDD\HTML
 */
class Text extends Base {

	/**
	 * Gets the HTML for the text field.
	 *
	 * @since 1.5.2
	 * @return string Text field
	 */
	public function get() {
		ob_start();
		if ( $this->args['include_span'] ) :
			?>
			<span id="edd-<?php echo edd_sanitize_key( $this->args['name'] ); ?>-wrap">
			<?php
		endif;
		if ( ! empty( $this->args['label'] ) ) {
			?>
			<label class="edd-label" for="<?php echo edd_sanitize_key( $this->args['id'] ); ?>">
				<?php echo esc_html( $this->args['label'] ); ?>
			</label>
			<?php
		}

		if ( ! empty( $this->args['desc'] ) ) {
			?>
			<span class="description edd-description"><?php echo esc_html( $this->args['desc'] ); ?></span>
			<?php
		}

		?>
		<input
			type="<?php echo esc_attr( $this->get_type() ); ?>"
			name="<?php echo esc_attr( $this->args['name'] ); ?>"
			id="<?php echo esc_attr( $this->args['id'] ); ?>"
			value="<?php echo esc_attr( $this->args['value'] ); ?>"
			placeholder="<?php echo esc_attr( $this->args['placeholder'] ); ?>"
			class="<?php echo esc_attr( $this->get_css_class_string() ); ?>"
			<?php
			echo $this->get_data_elements();
			if ( $this->args['autocomplete'] ) :
				?>
				autocomplete="<?php echo esc_attr( $this->args['autocomplete'] ); ?>"
				<?php
			endif;
			if ( $this->args['disabled'] ) :
				?>
				disabled
				<?php
			endif;
			if ( $this->args['required'] ) :
				?>
				required
				<?php
			endif;
			?>
		/>
		<?php if ( $this->args['include_span'] ) : ?>
			</span>
			<?php
		endif;

		return ob_get_clean();
	}

	/**
	 * Get the default arguments for the text field
	 *
	 * @since 3.2.8
	 * @return array
	 */
	protected function defaults() {
		return array(
			'id'           => '',
			'name'         => 'text',
			'value'        => '',
			'label'        => '',
			'desc'         => '',
			'placeholder'  => '',
			'class'        => 'regular-text',
			'disabled'     => false,
			'autocomplete' => '',
			'data'         => false,
			'required'     => false,
			'include_span' => true,
			'type'         => 'text',
		);
	}

	/**
	 * Get the type of the text field.
	 *
	 * @since 3.3.9
	 * @return string
	 */
	private function get_type(): string {
		if ( ! in_array( $this->args['type'], array( 'text', 'password', 'email', 'url' ), true ) ) {
			return 'text';
		}

		return $this->args['type'];
	}
}
