<?php
/**
 * @var $instance
 * @var $args
 * @var $player_id
 * @var $autoplay
 * @var $skin_class
 * @var $is_skinnable_video_host
 * @var $sources
 * @var $src
 * @var $video_type
 * @var $fitvids
 * @var $hide_controls
 */
if ( ! empty( $instance['title'] ) ) {
	echo $args['before_title'] . wp_kses_post( $instance['title'] ) . $args['after_title'];
}

$video_args = array(
	'id'      => $player_id,
	'class'   => 'sow-video-widget',
	'preload' => 'auto',
	'style'   => 'width:100%;height:100%;',
);

if ( $autoplay ) {
	$video_args['autoplay'] = '';
	$video_args['playsinline'] = '';
	// In most browsers, Videos need to be muted to autoplay.
	if ( apply_filters( 'sow_video_autoplay_mute_self_hosted', true ) ) {
		$video_args['muted'] = true;
	}
}

if ( $loop ) {
	$video_args['loop'] = 'true';
}

if ( ! empty( $poster ) ) {
	$video_args['poster'] = esc_url( $poster );
}

if ( $skin_class != 'default' ) {
	$video_args['class'] = 'mejs-' . $skin_class;
}

if ( ! $hide_controls ) {
	$video_args['controls'] = '';
}

$so_video = new SiteOrigin_Video();

do_action( 'siteorigin_widgets_sow-video_before_video', $instance );
?>

<div class="sow-video-wrapper
<?php
if ( $fitvids ) {
	echo ' use-fitvids';
}
?>
">
	<?php if ( $is_skinnable_video_host ) { ?>
		<video
			<?php
			foreach ( $video_args as $k => $v ) {
				echo siteorigin_sanitize_attribute_key( $k );

				if ( empty( $v ) ) {
					echo ' ';
					continue;
				}

				echo '="' . esc_attr( $v ) . '" ';
			}
			?>
		>
			<?php foreach ( $sources as $source ) { ?>
				<source type="<?php echo esc_attr( $source['video_type'] ); ?>" src="<?php echo esc_url( $source['src'] ); ?>"/>
			<?php } ?>
		</video>
	<?php } else { ?>
		<?php echo $so_video->get_video_oembed( $src, $autoplay, false, $loop, true ); ?>
	<?php } ?>
</div>
<?php do_action( 'siteorigin_widgets_sow-video_after_video', $instance ); ?>
