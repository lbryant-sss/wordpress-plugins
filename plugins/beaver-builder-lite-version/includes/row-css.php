<?php if ( ! empty( $settings->text_color ) ) : // Text Color ?>
.fl-node-<?php echo $id; ?> {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->text_color ); ?>;
}
.fl-builder-content .fl-node-<?php echo $id; ?> *:not(input):not(textarea):not(select):not(a):not(h1):not(h2):not(h3):not(h4):not(h5):not(h6):not(.fl-menu-mobile-toggle) {
	color: inherit;
}
<?php endif; ?>

<?php if ( ! empty( $settings->link_color ) ) : // Link Color ?>
.fl-builder-content .fl-node-<?php echo $id; ?> a {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->link_color ); ?>;
}
<?php elseif ( ! empty( $settings->text_color ) ) : ?>
.fl-builder-content .fl-node-<?php echo $id; ?> a {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->text_color ); ?>;
}
<?php endif; ?>

<?php if ( ! empty( $settings->hover_color ) ) : // Link Hover Color ?>
.fl-builder-content .fl-node-<?php echo $id; ?> a:hover {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->hover_color ); ?>;
}
<?php elseif ( ! empty( $settings->text_color ) ) : ?>
.fl-builder-content .fl-node-<?php echo $id; ?> a:hover {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->text_color ); ?>;
}
<?php endif; ?>

<?php if ( ! empty( $settings->heading_color ) ) : // Heading Color ?>
.fl-builder-content .fl-node-<?php echo $id; ?> h1,
.fl-builder-content .fl-node-<?php echo $id; ?> h2,
.fl-builder-content .fl-node-<?php echo $id; ?> h3,
.fl-builder-content .fl-node-<?php echo $id; ?> h4,
.fl-builder-content .fl-node-<?php echo $id; ?> h5,
.fl-builder-content .fl-node-<?php echo $id; ?> h6,
.fl-builder-content .fl-node-<?php echo $id; ?> h1 a,
.fl-builder-content .fl-node-<?php echo $id; ?> h2 a,
.fl-builder-content .fl-node-<?php echo $id; ?> h3 a,
.fl-builder-content .fl-node-<?php echo $id; ?> h4 a,
.fl-builder-content .fl-node-<?php echo $id; ?> h5 a,
.fl-builder-content .fl-node-<?php echo $id; ?> h6 a {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->heading_color ); ?>;
}
<?php elseif ( ! empty( $settings->text_color ) ) : ?>
.fl-builder-content .fl-node-<?php echo $id; ?> h1,
.fl-builder-content .fl-node-<?php echo $id; ?> h2,
.fl-builder-content .fl-node-<?php echo $id; ?> h3,
.fl-builder-content .fl-node-<?php echo $id; ?> h4,
.fl-builder-content .fl-node-<?php echo $id; ?> h5,
.fl-builder-content .fl-node-<?php echo $id; ?> h6,
.fl-builder-content .fl-node-<?php echo $id; ?> h1 a,
.fl-builder-content .fl-node-<?php echo $id; ?> h2 a,
.fl-builder-content .fl-node-<?php echo $id; ?> h3 a,
.fl-builder-content .fl-node-<?php echo $id; ?> h4 a,
.fl-builder-content .fl-node-<?php echo $id; ?> h5 a,
.fl-builder-content .fl-node-<?php echo $id; ?> h6 a {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->text_color ); ?>;
}
<?php endif; ?>

<?php if ( 'yes' === $row->settings->bg_video_audio ) : ?>
.fl-node-<?php echo $row->node; ?> .fl-bg-video-audio {
	display: none;
	cursor: pointer;
	position: absolute;
	bottom: 20px;
	right: 20px;
	z-index: 5;
	width: 20px;
}
.fl-node-<?php echo $row->node; ?> .fl-bg-video-audio .fl-audio-control {
	font-size: 20px;
}
.fl-node-<?php echo $row->node; ?> .fl-bg-video-audio .fa-times {
	font-size: 10px;
	vertical-align: middle;
	position: absolute;
	top: 5px;
	left: 11px;
	bottom: 0;
}
<?php endif; ?>

<?php

// Background Color
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id > .fl-row-content-wrap",
	'enabled'  => in_array( $settings->bg_type, array( 'color', 'photo', 'parallax', 'slideshow', 'video' ) ),
	'props'    => array(
		'background-color' => $settings->bg_color,
	),
) );

// Background Gradient
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id > .fl-row-content-wrap",
	'enabled'  => 'gradient' === $settings->bg_type,
	'media'    => 'default',
	'props'    => array(
		'background-image' => FLBuilderColor::gradient( $settings->bg_gradient ),
	),
) );

FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id > .fl-row-content-wrap",
	'enabled'  => 'gradient' === $settings->bg_type && ! empty( $settings->bg_gradient_medium ) && isset( $settings->bg_gradient_medium['colors'] ) && is_array( $settings->bg_gradient_medium['colors'] ) && ! empty( array_filter( $settings->bg_gradient_medium['colors'] ) ),
	'media'    => 'medium',
	'props'    => array(
		'background-image' => FLBuilderColor::gradient( $settings->bg_gradient_medium ),
	),
) );

FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id > .fl-row-content-wrap",
	'enabled'  => 'gradient' === $settings->bg_type && ! empty( $settings->bg_gradient_responsive ) && isset( $settings->bg_gradient_responsive['colors'] ) && is_array( $settings->bg_gradient_responsive['colors'] ) && ! empty( array_filter( $settings->bg_gradient_responsive['colors'] ) ),
	'media'    => 'responsive',
	'props'    => array(
		'background-image' => FLBuilderColor::gradient( $settings->bg_gradient_responsive ),
	),
) );

// Background Overlay
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id > .fl-row-content-wrap:after",
	'enabled'  => 'none' !== $settings->bg_overlay_type && in_array( $settings->bg_type, array( 'photo', 'parallax', 'slideshow', 'video' ) ),
	'props'    => array(
		'background-color' => 'color' === $settings->bg_overlay_type ? $settings->bg_overlay_color : '',
		'background-image' => 'gradient' === $settings->bg_overlay_type ? FLBuilderColor::gradient( $settings->bg_overlay_gradient ) : '',
	),
) );

// Background Photo - Desktop
if ( 'photo' == $row->settings->bg_type ) :
	$row_bg_image_xl = '';

	if ( 'library' == $row->settings->bg_image_source ) {
		$row_bg_image_xl = $row->settings->bg_image_src;
	} elseif ( 'url' == $row->settings->bg_image_source && ! empty( $row->settings->bg_image_url ) ) {
		if ( 'array' == gettype( $row->settings->bg_image_url ) ) {
			$row_bg_image_xl = do_shortcode( $row->settings->bg_image_url['url'] );
		} else {
			$row_bg_image_xl = (string) do_shortcode( $row->settings->bg_image_url );
		}
	}
	if ( 'custom_pos' == $row->settings->bg_position ) {
		$row_bg_position_lg  = empty( $row->settings->bg_x_position ) ? '0' : $row->settings->bg_x_position;
		$row_bg_position_lg .= $row->settings->bg_x_position_unit;
		$row_bg_position_lg .= ' ';
		$row_bg_position_lg .= empty( $row->settings->bg_y_position ) ? '0' : $row->settings->bg_y_position;
		$row_bg_position_lg .= $row->settings->bg_y_position_unit;

	} else {
		$row_bg_position_lg = $row->settings->bg_position;
	}

	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id > .fl-row-content-wrap",
		'enabled'  => 'photo' === $settings->bg_type,
		'props'    => array(
			'background-image'      => $row_bg_image_xl,
			'background-repeat'     => $settings->bg_repeat,
			'background-position'   => $row_bg_position_lg,
			'background-attachment' => $settings->bg_attachment,
			'background-size'       => $settings->bg_size,
		),
	) );
endif;

// Background Photo - Large
if ( 'photo' == $row->settings->bg_type ) :
	$row_bg_image_lg = '';

	if ( 'library' == $row->settings->bg_image_source ) {
		$row_bg_image_lg = $row->settings->bg_image_large_src;
	} elseif ( 'url' == $row->settings->bg_image_source && ! empty( $row->settings->bg_image_url ) ) {
		$row_bg_image_lg = $row_bg_image_xl;
	}
	if ( 'custom_pos' == $row->settings->bg_position_large ) {
		$row_bg_position_lg  = empty( $row->settings->bg_x_position_large ) ? '0' : $row->settings->bg_x_position_large;
		$row_bg_position_lg .= $row->settings->bg_x_position_large_unit;
		$row_bg_position_lg .= ' ';
		$row_bg_position_lg .= empty( $row->settings->bg_y_position_large ) ? '0' : $row->settings->bg_y_position_large;
		$row_bg_position_lg .= $row->settings->bg_y_position_large_unit;
	} else {
		$row_bg_position_lg = $row->settings->bg_position_large;
	}

	FLBuilderCSS::rule( array(
		'media'    => 'large',
		'selector' => ".fl-node-$id > .fl-row-content-wrap",
		'enabled'  => 'photo' === $settings->bg_type,
		'props'    => array(
			'background-image'      => $row_bg_image_lg,
			'background-repeat'     => $settings->bg_repeat_large,
			'background-position'   => $row_bg_position_lg,
			'background-attachment' => $settings->bg_attachment_large,
			'background-size'       => $settings->bg_size_large,
		),
	) );
endif;

// Background Photo - Medium
if ( 'photo' == $row->settings->bg_type ) :
	$row_bg_image_md = '';

	if ( 'library' == $row->settings->bg_image_source ) {
		$row_bg_image_md = $row->settings->bg_image_medium_src;
	} elseif ( 'url' == $row->settings->bg_image_source && ! empty( $row->settings->bg_image_url ) ) {
		$row_bg_image_md = $row_bg_image_xl;
	}
	if ( 'custom_pos' == $row->settings->bg_position_medium ) {
		$row_bg_position_md  = empty( $row->settings->bg_x_position_medium ) ? '0' : $row->settings->bg_x_position_medium;
		$row_bg_position_md .= $row->settings->bg_x_position_medium_unit;
		$row_bg_position_md .= ' ';
		$row_bg_position_md .= empty( $row->settings->bg_y_position_medium ) ? '0' : $row->settings->bg_y_position_medium;
		$row_bg_position_md .= $row->settings->bg_y_position_medium_unit;

	} else {
		$row_bg_position_md = $row->settings->bg_position_medium;
	}

	FLBuilderCSS::rule( array(
		'media'    => 'medium',
		'selector' => ".fl-node-$id > .fl-row-content-wrap",
		'enabled'  => 'photo' === $settings->bg_type,
		'props'    => array(
			'background-image'      => $row_bg_image_md,
			'background-repeat'     => $settings->bg_repeat_medium,
			'background-position'   => $row_bg_position_md,
			'background-attachment' => $settings->bg_attachment_medium,
			'background-size'       => $settings->bg_size_medium,
		),
	) );
endif;

// Background Photo - Responsive
if ( 'photo' == $row->settings->bg_type ) :
	$row_bg_image_sm = '';

	if ( 'library' == $row->settings->bg_image_source ) {
		$row_bg_image_sm = $row->settings->bg_image_responsive_src;
	} elseif ( 'url' == $row->settings->bg_image_source && ! empty( $row->settings->bg_image_url ) ) {
		$row_bg_image_sm = $row_bg_image_xl;
	}

	if ( 'custom_pos' == $row->settings->bg_position_responsive ) {
		$row_bg_position_sm  = empty( $row->settings->bg_x_position_responsive ) ? '0' : $row->settings->bg_x_position_responsive;
		$row_bg_position_sm .= $row->settings->bg_x_position_responsive_unit;
		$row_bg_position_sm .= ' ';
		$row_bg_position_sm .= empty( $row->settings->bg_y_position_responsive ) ? '0' : $row->settings->bg_y_position_responsive;
		$row_bg_position_sm .= $row->settings->bg_y_position_responsive_unit;

	} else {
		$row_bg_position_sm = $row->settings->bg_position_responsive;
	}

	FLBuilderCSS::rule( array(
		'media'    => 'responsive',
		'selector' => ".fl-node-$id > .fl-row-content-wrap",
		'enabled'  => 'photo' === $settings->bg_type,
		'props'    => array(
			'background-image'      => $row_bg_image_sm,
			'background-repeat'     => $settings->bg_repeat_responsive,
			'background-position'   => $row_bg_position_sm,
			'background-attachment' => $settings->bg_attachment_responsive,
			'background-size'       => $settings->bg_size_responsive,
		),
	) );
endif;

// Background Parallax
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id > .fl-row-content-wrap",
	'enabled'  => 'parallax' === $settings->bg_type,
	'props'    => array(
		'background-repeat'     => 'no-repeat',
		'background-position'   => 'center center',
		'background-attachment' => 'fixed',
		'background-size'       => 'cover',
	),
) );

FLBuilderCSS::rule( array(
	'selector' => ".fl-builder-mobile .fl-node-$id > .fl-row-content-wrap",
	'enabled'  => 'parallax' === $settings->bg_type && ! empty( $settings->bg_parallax_image_src ),
	'props'    => array(
		'background-image'      => $settings->bg_parallax_image_src,
		'background-position'   => 'center center',
		'background-attachment' => 'scroll',
	),
) );

// Parallax BG Medium
FLBuilderCSS::rule( array(
	'media'    => 'medium',
	'selector' => ".fl-builder-mobile .fl-node-$id > .fl-row-content-wrap",
	'enabled'  => 'parallax' === $settings->bg_type && ! empty( $settings->bg_parallax_image_medium_src ),
	'props'    => array(
		'background-image'      => $settings->bg_parallax_image_medium_src,
		'background-position'   => 'center center',
		'background-attachment' => 'scroll',
	),
) );

// Parallax BG Small
FLBuilderCSS::rule( array(
	'media'    => 'responsive',
	'selector' => ".fl-builder-mobile .fl-node-$id > .fl-row-content-wrap",
	'enabled'  => 'parallax' === $settings->bg_type && ! empty( $settings->bg_parallax_image_responsive_src ),
	'props'    => array(
		'background-image'      => $settings->bg_parallax_image_responsive_src,
		'background-position'   => 'center center',
		'background-attachment' => 'scroll',
	),
) );

// Background Video Fallback
$video_data = FLBuilderUtils::get_video_data( do_shortcode( $settings->bg_video_service_url ) );

FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-bg-video",
	'enabled'  => 'video_service' === $settings->bg_video_source && isset( $video_data['type'] ) && 'vimeo' == $video_data['type'] && ! empty( $settings->bg_video_fallback_src ),
	'props'    => array(
		'background-image'      => $settings->bg_video_fallback_src,
		'background-repeat'     => 'no-repeat',
		'background-position'   => 'center center',
		'background-attachment' => 'fixed',
		'background-size'       => 'cover',
	),
) );

// Border
FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'border',
	'selector'     => ".fl-node-$id > .fl-row-content-wrap",
) );

// Min Height
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'min_height',
	'selector'     => ".fl-node-$id.fl-row-custom-height > .fl-row-content-wrap",
	'prop'         => 'min-height',
	'enabled'      => 'custom' === $settings->full_height,
) );

// Aspect Ratio
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'aspect_ratio',
	'selector'     => ".fl-node-$id > .fl-row-content-wrap",
	'prop'         => 'aspect-ratio',
) );

// Row Resize - Max Width
if ( isset( $settings->max_content_width ) || isset( $settings->max_content_width_large ) || isset( $settings->max_content_width_medium ) || isset( $settings->max_content_width_responsive ) ) {
	$has_max_width            = ! FLBuilderCSS::is_empty( $settings->max_content_width );
	$has_large_max_width      = isset( $settings->max_content_width_large ) && ! FLBuilderCSS::is_empty( $settings->max_content_width_large );
	$has_medium_max_width     = isset( $settings->max_content_width_medium ) && ! FLBuilderCSS::is_empty( $settings->max_content_width_medium );
	$has_responsive_max_width = isset( $settings->max_content_width_responsive ) && ! FLBuilderCSS::is_empty( $settings->max_content_width_responsive );
	$is_row_fixed             = ( 'fixed' === $settings->width );
	$is_row_content_fixed     = ( 'fixed' === $settings->content_width );
	$are_both_full_width      = ( ! $is_row_fixed && ! $is_row_content_fixed );
	$max_width_selector       = '';

	if ( $is_row_fixed ) {
		$max_width_selector = ".fl-node-$id.fl-row-fixed-width, .fl-node-$id .fl-row-fixed-width";
	} else {
		$max_width_selector = ".fl-node-$id .fl-row-content";
	}

	FLBuilderCSS::rule( array(
		'selector' => $max_width_selector,
		'enabled'  => $has_max_width && ! $are_both_full_width,
		'props'    => array(
			'max-width' => array(
				'value' => $settings->max_content_width,
				'unit'  => FLBuilderCSS::get_unit( 'max_content_width', $settings ),
			),
		),
	) );

	if ( isset( $settings->max_content_width_large ) ) {
		FLBuilderCSS::rule( array(
			'selector' => $max_width_selector,
			'media'    => 'large',
			'enabled'  => $has_large_max_width && ! $are_both_full_width,
			'props'    => array(
				'max-width' => array(
					'value' => $settings->max_content_width_large,
					'unit'  => FLBuilderCSS::get_unit( 'max_content_width_large', $settings ),
				),
			),
		) );
	}

	if ( isset( $settings->max_content_width_medium ) ) {
		FLBuilderCSS::rule( array(
			'selector' => $max_width_selector,
			'media'    => 'medium',
			'enabled'  => $has_medium_max_width && ! $are_both_full_width,
			'props'    => array(
				'max-width' => array(
					'value' => $settings->max_content_width_medium,
					'unit'  => FLBuilderCSS::get_unit( 'max_content_width_medium', $settings ),
				),
			),
		) );
	}

	if ( isset( $settings->max_content_width_responsive ) ) {
		FLBuilderCSS::rule( array(
			'selector' => $max_width_selector,
			'media'    => 'responsive',
			'enabled'  => $has_responsive_max_width && ! $are_both_full_width,
			'props'    => array(
				'max-width' => array(
					'value' => $settings->max_content_width_responsive,
					'unit'  => FLBuilderCSS::get_unit( 'max_content_width_responsive', $settings ),
				),
			),
		) );
	}
}

FLBuilderArt::render_shape_layers_css( $row );

if ( ! empty( $settings->full_height ) && ( 'full' == $settings->full_height || 'custom' == $row->settings->full_height ) ) :
	?>
	/* Full Height Rows */
	.fl-node-<?php echo $id; ?>.fl-row-full-height > .fl-row-content-wrap,
	.fl-node-<?php echo $id; ?>.fl-row-custom-height > .fl-row-content-wrap {
		display: -webkit-box;
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;
	}
	.fl-node-<?php echo $id; ?>.fl-row-full-height > .fl-row-content-wrap {
		min-height: 100vh;
	}
	.fl-node-<?php echo $id; ?>.fl-row-custom-height > .fl-row-content-wrap {
		min-height: 0;
	}

	.fl-builder-edit .fl-node-<?php echo $id; ?>.fl-row-full-height > .fl-row-content-wrap {
		min-height: calc( 100vh - 48px );
	}

	/* Full height iPad with portrait orientation. */
	@media all and (width: 768px) and (height: 1024px) and (orientation:portrait){
		.fl-node-<?php echo $id; ?>.fl-row-full-height > .fl-row-content-wrap {
			min-height: 1024px;
		}
	}
	/* Full height iPad with landscape orientation. */
	@media all and (width: 1024px) and (height: 768px) and (orientation:landscape){
		.fl-node-<?php echo $id; ?>.fl-row-full-height > .fl-row-content-wrap {
			min-height: 768px;
		}
	}
	/* Full height iPhone 5. You can also target devices with aspect ratio. */
	@media screen and (aspect-ratio: 40/71) {
		.fl-node-<?php echo $id; ?>.fl-row-full-height > .fl-row-content-wrap {
			min-height: 500px;
		}
	}
<?php endif; ?>
