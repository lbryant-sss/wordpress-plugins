<?php
/**
 * The Accordion FAQs single item template.
 *
 * This template can be overridden by copying it to yourtheme/easy-accordion-free/templates/templates-parts/single-item.php
 *
 * @package easy_accordion_free
 */

?>
<!-- Start accordion card div. -->
<div class="ea-card <?php echo esc_attr( $accordion_mode['expand_class'] . ' ' . $accordion_item_class ); ?>">
	<!-- Start accordion header. -->
	<<?php echo esc_attr( $eap_title_tag ); ?> class="ea-header">
		<!-- Add anchor tag for header. -->
		<a class="collapsed" id="ea-header-<?php echo esc_attr( $post_id . $key ); ?>" role="button" data-sptoggle="spcollapse" data-sptarget="<?php echo esc_attr( $data_sptarget ); ?>" aria-controls="collapse<?php echo esc_attr( $post_id . $key ); ?>" href="#" <?php echo esc_attr( $nofollow_link_text ); ?> aria-expanded="<?php echo esc_attr( $accordion_mode['aria_expanded'] ); ?>" tabindex="0">
		<?php
		// Add icon and title.
		echo wp_kses_post( $eap_icon_markup . $content_title );
		?>
		</a><!-- Close anchor tag for header. -->
	</<?php echo esc_attr( $eap_title_tag ); ?>>	<!-- Close header tag. -->
	<!-- Start collapsible content div. -->
	<div class="sp-collapse spcollapse <?php echo esc_attr( $accordion_mode['open_first'] ); ?>" id="collapse<?php echo esc_attr( $post_id . $key ); ?>" <?php echo wp_kses_post( $eap_single_collapse ); ?> role="region" aria-labelledby="ea-header-<?php echo esc_attr( $post_id . $key ); ?>">  <!-- Content div. -->
		<div class="ea-body">
		<?php
		if ( ! empty( $content ) ) {
			// Add escaping filter to the accordion content before autoembed and do_shortcode unless the shortcode scripts or few tags generated by shortcodes are removed by the filter.
			$content_to_embed = wp_kses( $content, $eapro_allowed_description_tags );
			$embedded_content = $wp_embed->autoembed( $content_to_embed );

			if ( $eap_autop ) {
				$embedded_content = wpautop( $embedded_content );
				// Remove empty p tags before and after shortcodes and then do_shortcode.
				echo do_shortcode( shortcode_unautop( $embedded_content ) );
			} else {
				echo do_shortcode( $embedded_content );
			}
		} else {
			esc_html_e( 'No Content', 'easy-accordion-free' );
		}
		?>
		</div><!-- Close content div. -->
	</div><!-- Close collapse div. -->
</div><!-- Close card div. -->