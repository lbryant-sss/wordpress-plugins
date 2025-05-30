<?php
/**
 * @var $title
 * @var $title_position
 * @var $url
 * @var $link_attributes
 * @var $link_title
 * @var $new_window
 * @var $attributes
 * @var $classes
 */

// Don't output an empty image.
if ( empty( $attributes['src'] ) ) {
	return;
}

if ( $title_position == 'above' ) {
	echo $args['before_title'];

	if ( $link_title && ! empty( $url ) ) {
		echo $this->generate_anchor_open( $url, $link_attributes ) . wp_kses_post( $title ) . '</a>';
	} else {
		echo wp_kses_post( $title );
	}
	echo $args['after_title'];
}
?>

<div class="sow-image-container">
	<?php
	if ( ! empty( $url ) ) {
		$this->generate_anchor_open( $url, $link_attributes );
	}
	?>
	<img 
	<?php
	foreach ( $attributes as $n => $v ) {
		if ( $n === 'alt' || ! empty( $v ) ) {
			echo siteorigin_sanitize_attribute_key( $n ) . '="' . esc_attr( $v ) . '" ';
		}
	}
	?>
		class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"/>
	<?php
	if ( ! empty( $url ) ) {
		?>
		</a><?php } ?>
</div>

<?php
if ( $title_position == 'below' ) {
	echo $args['before_title'];

	if ( $link_title && ! empty( $url ) ) {
		echo $this->generate_anchor_open( $url, $link_attributes ) . wp_kses_post( $title ) . '</a>';
	} else {
		echo wp_kses_post( $title );
	}
	echo $args['after_title'];
}
?>
