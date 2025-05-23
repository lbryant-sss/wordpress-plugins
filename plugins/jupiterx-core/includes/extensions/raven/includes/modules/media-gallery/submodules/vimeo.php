<?php

namespace JupiterX_Core\Raven\Modules\Media_Gallery\Submodules;

use Elementor\Embed;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;

defined( 'ABSPATH' ) || die();

class Vimeo extends Base {
	public static function render_item( $data, $settings ) {
		$video_properties = Embed::get_video_properties( $data['vimeo_url']['url'] );
		$meta_data        = self::get_meta_data( $data, 'vimeo_poster' );
		$lazy             = self::is_lazy_load( $settings ) ? 'loading=lazy' : '';

		/**
		 * `#t=` is required for Vimeo urls.
		 * elementor\assets\dev\js\frontend\utils\video-api\vimeo-loader.js
		 */
		$url = add_query_arg(
			[
				'title' => 1,
				'color' => 'auto',
				'autoplay' => 0,
				'autopause' => 0,
				'loop' => 0,
				'muted' => 0,
			],
			'https://player.vimeo.com/video/' . $video_properties['video_id'] . '#t=0'
		);

		ob_start();
		?>
		<a class="gallery-item"
			href="<?php echo esc_attr( $data['vimeo_poster']['url'] ); ?>"
			data-elementor-open-lightbox="yes"
			data-elementor-lightbox-slideshow="<?php echo esc_attr( $data['lightbox_id'] ); ?>"
			data-elementor-lightbox-video="<?php echo esc_attr( esc_url( $url ) ); ?>"
		>
			<div class="type-video vimeo">
				<?php
				if ( 'player' !== $settings['video_preview'] ) {
					Utils::print_unescaped_internal_string( self::poster_image( $data, $settings ) );
				} else {
					?>
					<iframe
						<?php echo esc_attr( $lazy ); ?>
						class="elementor-video-iframe"
						allowfullscreen=""
						title="<?php esc_html_e( 'vimeo Video Player', 'jupiterx-core' ); ?>"
						src="<?php echo esc_url( $url ); ?>">
					</iframe>
				<?php } ?>
			</div>
			<?php Utils::print_unescaped_internal_string( self::render_overlay( $meta_data, $settings ) ); ?>
		</a>
		<?php
		return ob_get_clean();
	}

	private static function poster_image( $data, $settings ) {
		if ( 'player' === $settings['video_preview'] ) {
			return '';
		}

		// WPML compatibility.
		$data['vimeo_poster']['id']  = apply_filters( 'wpml_object_id', $data['vimeo_poster']['id'], 'attachment', true );
		$data['vimeo_poster']['alt'] = get_post_meta( $data['vimeo_poster']['id'], '_wp_attachment_image_alt', true );

		$lazy       = self::is_lazy_load( $settings ) ? 'loading="lazy"' : '';
		$poster_url = Group_Control_Image_Size::get_attachment_image_src( $data['vimeo_poster']['id'], 'thumbnail_image', $settings );
		$play_icon  = self::render_play_icon( $settings );
		$zoom_img   = '';

		if ( 'zoom' === $settings['image_hover_animation'] && ! empty( $data['vimeo_poster']['id'] ) ) {
			$full_poster = wp_get_attachment_image_url( $data['vimeo_poster']['id'], 'full' );
			$zoom_img    = sprintf( '<img alt="zoomImg" class="zoom-animation-image" src="%s">', $full_poster );
		}

		if ( empty( $poster_url ) ) {
			$poster_url = Utils::get_placeholder_image_src();
		}

		return sprintf(
			'<div class="poster">%1$s%2$s<img src="%3$s" alt="%4$s" %5$s></div>',
			$play_icon,
			$zoom_img,
			esc_url( $poster_url ),
			! empty( $data['vimeo_poster']['alt'] ) ? esc_html( $data['vimeo_poster']['alt'] ) : '',
			$lazy
		);
	}
}
