<?php

/**
 * Class SiteOrigin_Widget_Field_Link
 */
class SiteOrigin_Widget_Field_Link extends SiteOrigin_Widget_Field_Text_Input_Base {
	/**
	 * An array of post types to use in the query for posts when the 'Select Content' button is clicked.
	 *
	 * @var array
	 */
	protected $post_types;

	/**
	 * Prevents protocol from automatically being added to better support shortcodes.
	 *
	 * @var bool
	 */
	protected $allow_shortcode;

	protected function render_before_field( $value, $instance ) {
		parent::render_before_field( $value, $instance );
		$post_types = ! empty( $this->post_types ) && is_array( $this->post_types ) ? implode( ',', $this->post_types ) : '';
		?>
		<div class="url-input-wrapper">
			<?php
	}

	protected function render_after_field( $value, $instance ) {
			$post_types = ! empty( $this->post_types ) && is_array( $this->post_types ) ? implode( ',', $this->post_types ) : '';
			?>
			<a href="#" class="select-content-button button button-small"><?php esc_html_e( 'Select Content', 'so-widgets-bundle' ); ?></a>
			<div class="existing-content-selector">

				<input
					type="text"
					class="content-text-search"
					data-post-types="<?php echo esc_attr( $post_types ); ?>"
					placeholder="<?php esc_attr_e( 'Search Content', 'so-widgets-bundle' ); ?>"
					tabindex="0"
				/>

				<ul class="posts"></ul>

				<p class="content-no-results hidden"><?php esc_html_e( 'No results found.', 'so-widgets-bundle' ); ?></p>

				<div class="buttons">
					<a href="#" class="button-close button"><?php esc_html_e( 'Close', 'so-widgets-bundle' ); ?></a>
				</div>
			</div>
		</div>
		<?php
		parent::render_after_field( $value, $instance );
	}

	protected function sanitize_field_input( $value, $instance ) {
		if ( empty( $value ) ) {
			return '';
		}

		$sanitized_value = trim( $value );

		if ( preg_match( '/^post\: *([0-9]+)/', $sanitized_value, $matches ) ) {
			$sanitized_value = 'post: ' . $matches[1];
		} elseif ( $this->allow_shortcode && strpos( $sanitized_value, '[' ) !== false ) {
			$sanitized_value = esc_attr( $sanitized_value );
		} else {
			$sanitized_value = sow_esc_url_raw( $sanitized_value );
		}

		return $sanitized_value;
	}
}
