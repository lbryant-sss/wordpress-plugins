<?php // master slider shortcodes

/*-----------------------------------------------------------------------------------*/
/*  MasterSlider
/*-----------------------------------------------------------------------------------*/

add_shortcode( 'masterslider'	, 'msp_masterslider_shortcode' );
add_shortcode( 'master_slider'	, 'msp_masterslider_shortcode' );


function msp_masterslider_shortcode( $atts, $content = null ) {
	extract( shortcode_atts(
					array( 'id' => ''),
					$atts,
					'masterslider'
				)
	);

	return get_masterslider( $id );
}

/*-----------------------------------------------------------------------------------*/
/*  MasterSlider Page Builder Element
/*-----------------------------------------------------------------------------------*/

add_shortcode( 'masterslider_pb', 'msp_masterslider_pb_shortcode' );

function msp_masterslider_pb_shortcode( $atts, $content = null ) {
	$mixed = shortcode_atts(
		array(
		      'id' 	  => '',
		      'title' => '',
		      'class' => ''
		),
		$atts,
		'masterslider_pb'
	);

	extract( $mixed );

	$wrapper_open_tag  = sprintf( '<div class="avt_masterslider_el %s" >', esc_attr( $class ) );
	$the_title_tag     = empty( $title ) ? '' : sprintf( '<h2>%s</h2>', wp_kses_post( $title ) );
	$wrapper_close_tag = '</div>';
	$slider_markup     = get_masterslider( $id );
	$output 		   = $wrapper_open_tag . $the_title_tag . $slider_markup . $wrapper_close_tag;

	return apply_filters( 'masterslider_pb_shortcode', $output, $slider_markup, $wrapper_open_tag, $the_title_tag, $wrapper_close_tag );
}


/*-----------------------------------------------------------------------------------*/
/*  MasterSlider Wrapper
/*-----------------------------------------------------------------------------------*/

add_shortcode( 'ms_slider', 'msp_masterslider_wrapper_shortcode' );

function msp_masterslider_wrapper_shortcode( $atts, $content = null ) {

	 $mixed = shortcode_atts(

				array(
					'id'            => '1',     // slider id
					'uid'           => '',      // an unique and temporary id
					'class'         => '',      // a class that adds to slider wrapper
					'margin'        => 0,

					'inline_style'  => '',
					'bg_color'      => '',
					'bg_image'      => '',

					'slider_type'   => 'custom',   // values: custom, flickr, facebook, post

					'width'         => 300,     // base width of slides. It helps the slider to resize in correct ratio.
					'height'        => 150,     // base height of slides, It helps the slider to resize in correct ratio.
					'min_height' 	=> 0,

					'start'         => 1,
					'space'         => 0,

					'grab_cursor'   => 'true',  // Whether the slider uses grab mouse cursor
					'swipe'         => 'true',  // Whether the drag/swipe navigation is enabled

					'wheel'         => 'false', // Enables mouse scroll wheel navigation
					'mouse'         => 'true',  // Whether the user can use mouse drag navigation

					'crop' 			 => 'false', // Automatically crop slide images?

					'autoplay'      => 'false', // Enables the autoplay slideshow
					'loop'          => 'false', //
					'shuffle'       => 'false', // Enables the shuffle slide order
					'preload'       =>  0,

					'wrapper_width' => '',
	        		'wrapper_width_unit' => 'px',

					'fullwidth'     => 'false', // It enables the slider to adapt width to its parent element
					'fullheight'    => 'false', // It enables the slider to adapt height to its parent element
					'autofill'      => 'false', // It enables the slider to adapt width and height to its parent element

					'layout'        => 'fillwidth',

					'fullscreen_margin' => 0,

					'height_limit'  => 'false', // It force the slide to use max height value as its base specified height value.
					'auto_height'   => 'false',
					'smooth_height' => 'true',

					'end_pause'     => 'false',
					'over_pause'    => 'false',

					'fill_mode'     => 'fill',
					'center_controls'=> 'true',

					'layers_mode'   => 'center',// It accepts two values "center" and "full"
					'hide_layers'   => 'false',

					'instant_show_layers' => 'false',

					'speed'         => 17,

					'skin'          => 'ms-skin-default', // slider skin. should be seperated by space - should be started by ms-skin
					'template'      => '',
					'template_class'=> '',
					'direction'     => 'h',
					'view'          => 'basic',

					'gfonts' 		=> '',

	        		'parallax_mode' => 'swipe',

	        		'start_on_appear'=> 'false',

					'flickr_key'    => '',
					'flickr_id'     => '',
		      		'flickr_count'  => 10,
		      		'flickr_type'   => 'photos',
		      		'flickr_size'   => 'c',
		      		'flickr_thumb_size' => 'q',


					'facebook_username' => '',
					'facebook_albumid'  => '',
					'facebook_count'	=> 10,
					'facebook_type' 	=> 'album',
					'facebook_size' 	=> 'orginal',
					'facebook_thumb_size' => '320',

					'ps_post_type' 		=> '',
					'ps_tax_term_ids' 	=> '',
					'ps_post_count' 	=> 10,
					'ps_image_from' 	=> 'auto',
					'ps_order' 			=> 'DESC',
					'ps_orderby' 		=> 'menu_order date',
					'ps_posts_not_in'   => '',
					'ps_excerpt_len' 	=> 100,
					'ps_offset' 		=> 0,
					'ps_link_slide' 	=> false,
					'ps_link_target' 	=> '_self',
					'ps_slide_bg'  		=> '',


					'arrows'           => 'true',   // display arrows?
					'arrows_autohide'  => 'true',   // auto hide arrows?
					'arrows_overvideo' => 'true',   // visible over slide video while playing?
					'arrows_hideunder' => '',

					'bullets'          => 'false',  // display bullets?
					'bullets_autohide' => 'true',   // auto hide bullets?
					'bullets_overvideo'=> 'true',   // visible over slide video while playing?
					'bullets_direction'=> 'h',
					'bullets_align'    => 'bottom',
					'bullets_margin'   => '',
					'bullets_hideunder'=> '',

					'thumbs'           => 'false',  // display thumbnails?
					'thumbs_autohide'  => 'true',   // auto hide thumbs?
					'thumbs_overvideo' => 'true',   // visible over slide video while playing?
					'thumbs_direction' => 'h',      // direction of control
					'thumbs_type'      => 'thumbs',
					'thumbs_speed'     => 17,       // scrolling speed. It accepts float values between 0 and 100
					'thumbs_inset'     => 'true',   // insert thumbs inside slider
					'thumbs_align'     => 'bottom',
					'thumbs_margin'    => 0,
					'thumbs_width'     => 100,
					'thumbs_height'    => 80,
					'thumbs_space'     => 5,
					'thumbs_hideunder' => '',
					'thumbs_fillmode'  => 'fill',
					'thumbs_arrows'    => 'false',
					'thumbs_in_tab'    => 'false',
					'thumbs_hoverchange'=> 'false',

					'scroll'           => 'false',  // display scrollbar?
					'scroll_autohide'  => 'true',   // auto hide scroll?
					'scroll_overvideo' => 'true',   // visible over slide video while playing?
					'scroll_direction' => 'h',      // direction of control
					'scroll_align'     => 'top',
					'scroll_inset'     => 'true',
					'scroll_margin'    => '',
					'scroll_color'     => '#3D3D3D',
					'scroll_hideunder' => '',
					'scroll_width' 	 => '',

					'circletimer'          => 'false',  // display circletimer?
					'circletimer_autohide' => 'true',   // auto hide circletimer?
					'circletimer_overvideo'=> 'true',   // visible over slide video while playing?
					'circletimer_color'    => '#A2A2A2',// color of circle timer
					'circletimer_radius'   => 4,        // radius of circle timer in pixels
					'circletimer_stroke'   => 10,       // the stroke of circle timer in pixels
					'circletimer_margin'   => '',
					'circletimer_hideunder'=> '',

					'timebar'          => 'false',   // display timebar?
					'timebar_autohide' => 'true',   // auto hide timebar?
					'timebar_overvideo'=> 'true',   // visible over slide video while playing?
					'timebar_align'    => 'bottom',
					'timebar_color'    => '#FFFFFF',
					'timebar_hideunder'=> '',
					'timebar_width' 	 => '',


					'slideinfo'          => 'false',   // display timebar?
					'slideinfo_autohide' => 'true',   // auto hide timebar?
					'slideinfo_overvideo'=> 'true',   // visible over slide video while playing?
					'slideinfo_direction'=> 'h',
					'slideinfo_align'    => 'bottom',
					'slideinfo_inset'    => 'false',
					'slideinfo_margin'   => '',
					'slideinfo_hideunder'=> '',
					'slideinfo_width'	 => '',
					'slideinfo_height'   => '',

                    'on_init'            => '',
                    'on_change_start'    => '',
                    'on_change_end'      => '',
                    'on_waiting'         => '',
                    'on_resize'          => '',
                    'on_video_play'      => '',
                    'on_video_close'     => '',
                    'on_swipe_start'     => '',
                    'on_swipe_move'      => '',
                    'on_swipe_end'       => ''
				)
				, $atts , 'ms_slider'
	 );

	 extract( $mixed );

	 // load masterslider script
	 wp_enqueue_style ( 'masterslider-main');
	 wp_enqueue_script( 'masterslider-core');
	 wp_enqueue_script( 'prettyPhoto' );

	// create an unique id for slider
	$uid    = empty($uid ) ? uniqid("MS") : $uid;
	// unique id for parant wrapper
	$puid   = 'P_' . $uid;


	// class name for slider template
	$template_class = empty( $template_class ) ? '' : esc_attr( $template_class );

	$preload = is_numeric($preload) ? ( (int)$preload + 1 ) : "'$preload'";


	// add max-width to wrapper for boxed and partialview layout
	if( ! empty( $wrapper_width ) && ( 'boxed' == $layout || 'partialview' == $layout ) ) {
		// validate wrapper_width_unit
		$wrapper_width_unit = in_array( $wrapper_width_unit, array( 'px', '%', 'em' ) ) ? $wrapper_width_unit : 'px';
		$inline_style .= sprintf( 'max-width:%s%s;', $wrapper_width, $wrapper_width_unit );

	// if wrapper_width is not set use slider width as default
	} elseif ( 'boxed' == $layout ) {
		$inline_style .= sprintf( 'max-width:%spx;', $width );

	// if wrapper_width is not set the value to 100%
	} elseif ( 'partialview' == $layout ) {
		$inline_style .= 'max-width:100%;';
	}


	$arrows_hideunder   = empty( $arrows_hideunder  ) ? '' : sprintf( ', hideUnder:%s', $arrows_hideunder  );
	$bullets_hideunder  = empty( $bullets_hideunder ) ? '' : sprintf( ', hideUnder:%s', $bullets_hideunder );
	$thumbs_hideunder   = empty( $thumbs_hideunder  ) ? '' : sprintf( ', hideUnder:%s', $thumbs_hideunder  );
	$scroll_hideunder   = empty( $scroll_hideunder  ) ? '' : sprintf( ', hideUnder:%s', $scroll_hideunder  );
	$timebar_hideunder  = empty( $timebar_hideunder ) ? '' : sprintf( ', hideUnder:%s', $timebar_hideunder );
	$slideinfo_hideunder   = empty( $slideinfo_hideunder    ) ? '' : sprintf( ', hideUnder:%s', $slideinfo_hideunder   );
	$circletimer_hideunder = empty( $circletimer_hideunder  ) ? '' : sprintf( ', hideUnder:%s', $circletimer_hideunder );

	$bullets_margin     = empty( $bullets_margin )    ? '' : sprintf( ', margin:%s', $bullets_margin     );
	$circletimer_margin = empty( $circletimer_margin )? '' : sprintf( ', margin:%s', $circletimer_margin );
	$scroll_margin      = empty( $scroll_margin )     ? '' : sprintf( ', margin:%s', $scroll_margin      );
	$slideinfo_margin   = empty( $slideinfo_margin )  ? '' : sprintf( ', margin:%s', $slideinfo_margin   );

	$timebar_width      = empty( $timebar_width )     ? '' : sprintf( ', width:%s', $timebar_width );
	$scroll_width       = empty( $scroll_width  )     ? '' : sprintf( ', width:%s', $scroll_width );


	if ( in_array( $bullets_align, array('left', 'right') ) )
		$bullets_direction = 'v';
	if ( in_array( $bullets_align, array('top', 'bottom') ) )
		$bullets_direction = 'h';

	if ( in_array( $thumbs_align, array('left', 'right') ) )
		$thumbs_direction = 'v';
	if ( in_array( $thumbs_align, array('top', 'bottom') ) )
		$thumbs_direction = 'h';

	if ( in_array( $scroll_align, array('left', 'right') ) )
		$scroll_direction = 'v';
	if ( in_array( $scroll_align, array('top', 'bottom') ) )
		$scroll_direction = 'h';

	if ( in_array( $slideinfo_align, array('left', 'right') ) )
		$slideinfo_direction = 'v';
	if ( in_array( $slideinfo_align, array('top', 'bottom') ) )
		$slideinfo_direction = 'h';

	// set slideinfo size to spefified height is direction is horizontal, else set it to width
	if( empty( $slideinfo_width ) && empty( $slideinfo_height ) ) {
		$slideinfo_size = '';
	} elseif( 'h' == $slideinfo_direction ){
		$slideinfo_size = sprintf( ', size:%s', $slideinfo_height );
	} else {
		$slideinfo_size = sprintf( ', size:%s', $slideinfo_width );
	}

	$instance_suffix = substr($uid, -4);
	// slider javascript instance name
	$instance_name = "masterslider_".$instance_suffix;

	// stores inner markup for some spesific templates
	$inner_template_container_open_tags  = '';
	$inner_template_container_close_tags = '';


	if( 'laptop' == $template ){
		$inner_template_container_open_tags  = sprintf( '<div class="ms-laptop-cont"><img src="%s" class="ms-laptop-bg" /><div class="ms-lt-slider-cont">', MSWP_AVERTA_PUB_URL.'/assets/css/templates/laptop.png' );
		$inner_template_container_close_tags = '</div></div>';

	} elseif( 'display' == $template ){
		$inner_template_container_open_tags  = sprintf( '<div class="ms-display-cont"><img src="%s" class="ms-display-bg" /><div class="ms-dis-slider-cont">', MSWP_AVERTA_PUB_URL.'/assets/css/templates/display.png' );
		$inner_template_container_close_tags = '</div></div>';

	} elseif( 'flat-laptop' == $template ){
		$inner_template_container_open_tags  = sprintf( '<div class="ms-laptop-cont"><img src="%s" class="ms-laptop-bg" /><div class="ms-lt-slider-cont">', MSWP_AVERTA_PUB_URL.'/assets/css/templates/flat-laptop.png' );
		$inner_template_container_close_tags = '</div></div>';

	} elseif( 'flat-display' == $template ){
		$inner_template_container_open_tags  = sprintf( '<div class="ms-display-cont"><img src="%s" class="ms-display-bg" /><div class="ms-dis-slider-cont">', MSWP_AVERTA_PUB_URL.'/assets/css/templates/flat-display.png' );
		$inner_template_container_close_tags = '</div></div>';

	} elseif( 'tablet' == $template ){
		$inner_template_container_open_tags  = sprintf( '<div class="ms-tablet-cont"><img src="%s" class="ms-tablet-bg" /><div class="ms-lt-slider-cont">', MSWP_AVERTA_PUB_URL.'/assets/css/templates/tablet.png' );
		$inner_template_container_close_tags = '</div></div>';

	} elseif( 'flat-tablet' == $template ){
		$inner_template_container_open_tags  = sprintf( '<div class="ms-tablet-cont"><img src="%s" class="ms-tablet-bg" /><div class="ms-lt-slider-cont">', MSWP_AVERTA_PUB_URL.'/assets/css/templates/flat-tablet.png' );
		$inner_template_container_close_tags = '</div></div>';

	} elseif( 'tablet-land' == $template ){
		$inner_template_container_open_tags  = sprintf( '<div class="ms-tablet-cont"><img src="%s" class="ms-tablet-bg" /><div class="ms-lt-slider-cont">', MSWP_AVERTA_PUB_URL.'/assets/css/templates/tablet-land.png' );
		$inner_template_container_close_tags = '</div></div>';

	} elseif( 'flat-tablet-land' == $template ){
		$inner_template_container_open_tags  = sprintf( '<div class="ms-tablet-cont"><img src="%s" class="ms-tablet-bg" /><div class="ms-lt-slider-cont">', MSWP_AVERTA_PUB_URL.'/assets/css/templates/flat-tablet-land.png' );
		$inner_template_container_close_tags = '</div></div>';

	} elseif( 'phone' == $template ){
		$inner_template_container_open_tags  = sprintf( '<div class="ms-phone-cont"><img src="%s" class="ms-phone-bg" /><div class="ms-lt-slider-cont">', MSWP_AVERTA_PUB_URL.'/assets/css/templates/phone.png' );
		$inner_template_container_close_tags = '</div></div>';

	} elseif( 'flat-phone' == $template ){
		$inner_template_container_open_tags  = sprintf( '<div class="ms-phone-cont"><img src="%s" class="ms-phone-bg" /><div class="ms-lt-slider-cont">', MSWP_AVERTA_PUB_URL.'/assets/css/templates/flat-phone.png' );
		$inner_template_container_close_tags = '</div></div>';

	} elseif( 'phone-land' == $template ){
		$inner_template_container_open_tags  = sprintf( '<div class="ms-phone-cont"><img src="%s" class="ms-phone-bg" /><div class="ms-lt-slider-cont">', MSWP_AVERTA_PUB_URL.'/assets/css/templates/phone-land.png' );
		$inner_template_container_close_tags = '</div></div>';

	} elseif( 'flat-phone-land' == $template ){
		$inner_template_container_open_tags  = sprintf( '<div class="ms-phone-cont"><img src="%s" class="ms-phone-bg" /><div class="ms-lt-slider-cont">', MSWP_AVERTA_PUB_URL.'/assets/css/templates/flat-phone-land.png' );
		$inner_template_container_close_tags = '</div></div>';
	}


	$inner_template_container_open_tags  = apply_filters( 'masterslider_ms_slider_inner_template_container_open_tags' , $inner_template_container_open_tags , $template, $mixed );
	$inner_template_container_close_tags = apply_filters( 'masterslider_ms_slider_inner_template_container_close_tags', $inner_template_container_close_tags, $template, $mixed );

	// class names for master slider wrapper
	$wrapper_classes = $class.' '.$template_class.' '.'ms-parent-id-'.$id;

	ob_start();
 ?>

		<!-- MasterSlider -->
		<div id="<?php echo esc_attr( $puid ); ?>" class="master-slider-parent msl <?php echo esc_attr( trim( $wrapper_classes ) ); ?>" style="<?php echo esc_attr( $inline_style ); ?>">

			<?php echo wp_kses_post( $inner_template_container_open_tags ); ?>

			<!-- MasterSlider Main -->
			<div id="<?php echo esc_attr( $uid ); ?>" class="master-slider <?php echo esc_attr( $skin ); ?>" >
				 <?php // generate all ms slide shortcodes ?>
				 <?php echo do_shortcode($content); ?>

			</div>
			<!-- END MasterSlider Main -->

			 <?php echo wp_kses_post( $inner_template_container_close_tags ); ?>

		</div>
		<!-- END MasterSlider -->

		<script>
		(function ( $ ) {
			"use strict";

			$(function () {
				var <?php echo esc_js( $instance_name ); ?> = new MasterSlider();

				// slider controls
<?php if($arrows  == 'true' || 'image-gallery' == $template ){
						printf( "\t\t\t\t$instance_name.control('%s'     ,{ autohide:%s, overVideo:%s %s });",
									'arrows',
									msp_is_true($arrows_autohide ),
									msp_is_true($arrows_overvideo ),
									$arrows_hideunder
								);
} ?>
<?php if($bullets == 'true'){
						printf( "\t\t\t\t$instance_name.control('%s'    ,{ autohide:%s, overVideo:%s, dir:'%s', align:'%s' %s %s });\n",
									'bullets'  ,
									msp_is_true($bullets_autohide ),
									msp_is_true($bullets_overvideo ),
									$bullets_direction,
									$bullets_align,
									$bullets_margin,
									$bullets_hideunder
								);
} ?>

<?php if($thumbs  == 'true'){
						$thumbs_custom_class = 'true' == $thumbs_in_tab ? 'ms-has-thumb' : '';
						printf( "\t\t\t\t$instance_name.control('%s'  ,{ autohide:%s, overVideo:%s, dir:'%s', speed:%d, inset:%s, arrows:%s, hover:%s, customClass:'%s', align:'%s',type:'%s', margin:%d, width:%d, height:%d, space:%d, fillMode:'%s' %s });\n",
									'thumblist',
									msp_is_true( $thumbs_autohide  ),
									msp_is_true( $thumbs_overvideo ),
									$thumbs_direction,
									(int)$thumbs_speed,
									msp_is_true( $thumbs_inset ),
									msp_is_true( $thumbs_arrows ),
									msp_is_true( $thumbs_hoverchange ),
									$thumbs_custom_class,
									esc_attr( $thumbs_align ),
									esc_attr( $thumbs_type ),
									(int)$thumbs_margin,
									(int)$thumbs_width,
									(int)$thumbs_height,
									(int)$thumbs_space,
									esc_attr( $thumbs_fillmode ),
									$thumbs_hideunder
								);


} ?>
<?php if($scroll  == 'true'){
						printf( "\t\t\t\t$instance_name.control('%s'  ,{ autohide:%s, overVideo:%s, dir:'%s', inset:%s, align:'%s', color:'%s' %s %s %s });\n",
								  'scrollbar',
									msp_is_true($scroll_autohide  ),
									msp_is_true($scroll_overvideo ),
									$scroll_direction,
									msp_is_true($scroll_inset ),
									$scroll_align,
									$scroll_color,
									$scroll_margin,
									$scroll_hideunder,
									$scroll_width
								);
} ?>
<?php if($circletimer == 'true'){
						printf( "\t\t\t\t$instance_name.control('%s',{ autohide:%s, overVideo:%s, color:'%s', radius:%d, stroke:%d %s %s });\n",
								  "circletimer",
									msp_is_true($circletimer_autohide ),
									msp_is_true($circletimer_overvideo ),
									$circletimer_color,
									$circletimer_radius,
									$circletimer_stroke,
									$circletimer_margin,
									$circletimer_hideunder
								);
} ?>
<?php if($timebar == 'true'){
						printf( "\t\t\t\t$instance_name.control('%s'    ,{ autohide:%s, overVideo:%s, align:'%s', color:'%s' %s %s });\n",
								  "timebar",
									msp_is_true($timebar_autohide  ),
									msp_is_true($timebar_overvideo ),
									$timebar_align,
									$timebar_color,
									$timebar_hideunder,
									$timebar_width
								);
} ?>
<?php if($slideinfo == 'true'){
						printf( "\t\t\t\t$instance_name.control('%s'  ,{ autohide:%s, overVideo:%s, dir:'%s', align:'%s',inset:%s %s %s %s });\n",
								  "slideinfo",
									msp_is_true($slideinfo_autohide  ),
									msp_is_true($slideinfo_overvideo ),
									$slideinfo_direction,
									$slideinfo_align,
									msp_is_true( $slideinfo_inset ),
									$slideinfo_margin,
									$slideinfo_hideunder,
									$slideinfo_size
								);
} ?>
				// slider setup
				<?php echo esc_js( $instance_name ); ?>.setup("<?php echo esc_attr($uid); ?>", {
						width           : <?php echo (int)$width; ?>,
						height          : <?php echo (int) $height; ?>,
						minHeight       : <?php echo (int) $min_height; ?>,
						space           : <?php echo (int) $space;  ?>,
						start           : <?php echo (int) $start;  ?>,
						grabCursor      : <?php msp_is_true_e($grab_cursor); ?>,
						swipe           : <?php msp_is_true_e($swipe); ?>,
						mouse           : <?php msp_is_true_e($mouse); ?>,
						layout          : "<?php echo $layout; ?>",
						wheel           : <?php msp_is_true_e($wheel); ?>,
						autoplay        : <?php msp_is_true_e($autoplay); ?>,
						instantStartLayers:<?php msp_is_true_e( $instant_show_layers ); ?>,
						loop            : <?php msp_is_true_e($loop); ?>,
						shuffle         : <?php msp_is_true_e($shuffle); ?>,
						preload         : <?php echo $preload; ?>,
						heightLimit     : <?php msp_is_true_e($height_limit); ?>,
						autoHeight      : <?php msp_is_true_e($auto_height); ?>,
						smoothHeight    : <?php msp_is_true_e($smooth_height); ?>,
						endPause        : <?php msp_is_true_e($end_pause); ?>,
						overPause       : <?php msp_is_true_e($over_pause); ?>,
						fillMode        : "<?php echo $fill_mode; ?>",
						centerControls  : <?php msp_is_true_e($center_controls); ?>,
						startOnAppear   : <?php msp_is_true_e($start_on_appear); ?>,
						layersMode      : "<?php echo $layers_mode; ?>",
						hideLayers      : <?php msp_is_true_e($hide_layers); ?>,
						fullscreenMargin: <?php echo (int) $fullscreen_margin;  ?>,
						speed           : <?php echo (int)$speed; ?>,
						dir             : "<?php echo $direction; ?>",
<?php if( 'staff-3' == $template      ) { echo "viewOption      : { centerSpace:1.6 },\n"; } ?>
<?php if( 'off'     != $parallax_mode ) { echo "\t\t\t\t\t\tparallaxMode    : '$parallax_mode',\n"; } ?>
						view            : "<?php echo $view; ?>"
				});

				<?php
                if( ! empty( $on_init ) )
                    printf( "$instance_name.api.addEventListener(MSSliderEvent.INIT, %s );\n"        , msp_masterslider_prevent_closing_script_tag( msp_maybe_base64_decode( $on_init ) ) ) ;

				if( ! empty( $on_change_start ) )
					printf( "$instance_name.api.addEventListener(MSSliderEvent.CHANGE_START, %s );\n"		  , msp_masterslider_prevent_closing_script_tag( msp_maybe_base64_decode( $on_change_start ) ) ) ;

				if( ! empty( $on_change_end ) )
					printf( "\t\t\t\t$instance_name.api.addEventListener(MSSliderEvent.CHANGE_END, %s );\n"  , msp_masterslider_prevent_closing_script_tag( msp_maybe_base64_decode( $on_change_end ) ) ) ;

				if( ! empty( $on_waiting ) )
					printf( "\t\t\t\t$instance_name.api.addEventListener(MSSliderEvent.WAITING, %s );\n"     , msp_masterslider_prevent_closing_script_tag( msp_maybe_base64_decode( $on_waiting ) ) ) ;

				if( ! empty( $on_resize ) )
					printf( "\t\t\t\t$instance_name.api.addEventListener(MSSliderEvent.RESIZE, %s );\n"      , msp_masterslider_prevent_closing_script_tag( msp_maybe_base64_decode( $on_resize ) ) ) ;

				if( ! empty( $on_video_play ) )
					printf( "\t\t\t\t$instance_name.api.addEventListener(MSSliderEvent.VIDEO_PLAY, %s );\n"  , msp_masterslider_prevent_closing_script_tag( msp_maybe_base64_decode( $on_video_play ) ) ) ;
				if( ! empty( $on_video_close ) )
					printf( "\t\t\t\t$instance_name.api.addEventListener(MSSliderEvent.VIDEO_CLOSE, %s );\n" , msp_masterslider_prevent_closing_script_tag( msp_maybe_base64_decode( $on_video_close ) ) ) ;

				if( $on_swipe_start || $on_swipe_move || $on_swipe_end ){

                    echo "\t\t\t$instance_name.api.addEventListener(MSSliderEvent.INIT, function(){\n";

                    if( ! empty( $on_swipe_start ) ){
                        printf( "\t\t\t\t$instance_name.api.view.addEventListener(MSViewEvents.SWIPE_START, %s );\n" , msp_masterslider_prevent_closing_script_tag( msp_maybe_base64_decode( $on_swipe_start ) ) ) ;
                    }

                    if( ! empty( $on_swipe_move ) ){
                        printf( "\t\t\t\t$instance_name.api.view.addEventListener(MSViewEvents.SWIPE_MOVE, %s );\n"  , msp_masterslider_prevent_closing_script_tag( msp_maybe_base64_decode( $on_swipe_move ) ) ) ;
                    }

                    if( ! empty( $on_swipe_end ) ){
                        printf( "\t\t\t\t$instance_name.api.view.addEventListener(MSViewEvents.SWIPE_END, %s );\n"   , msp_masterslider_prevent_closing_script_tag( msp_maybe_base64_decode( $on_swipe_end ) ) ) ;
                    }

                    echo "\t\t\t});\n";
                }

				if ( 'image-gallery' == $template ) {
					printf( "new MSGallery( '%s' , %s).setup();", $puid, $instance_name );
				}

				if ( 'flickr' == $slider_type ) {
					printf( "new MSFlickrV2( %s, { key:'%s', id:'%s', count:%d, thumbSize:'%s',imgSize:'%s', type:'%s' });", $instance_name, $flickr_key, $flickr_id, $flickr_count, $flickr_thumb_size, $flickr_size, $flickr_type );
				}

				if ( 'facebook' == $slider_type ) {
					$facebook_username_prop   = empty( $facebook_username ) ? '' : sprintf( "username:'%s', " , $facebook_username  );
					$facebook_albumid_prop    = empty( $facebook_albumid  ) ? '' : sprintf( "albumId :'%s', " , $facebook_albumid   );

					printf( "new MSFacebookGallery( %s, { %s %s count:%d, thumbSize:'%s',imgSize:'%s', type:'%s' });",
					        $instance_name, $facebook_username_prop, $facebook_albumid_prop, $facebook_count, $facebook_thumb_size, $facebook_size, $facebook_type );
				}

				// add slider instance to global scope
				echo "\n\t\t\t\twindow.masterslider_instances = window.masterslider_instances || [];";
				echo "\n\t\t\t\twindow.masterslider_instances.push( $instance_name );\n";
				?>
			 });

		})(jQuery);
		</script>

<?php
	if ( ! empty( $gfonts ) ) {
		$response = wp_remote_get( 'http://fonts.googleapis.com/css?family=' . $gfonts );
		if ( !is_wp_error( $response ) ) {
			wp_add_inline_style( 'ms-fonts', $response['body'] );
		}
	}
	return apply_filters( "masterslider_ms_slider_shortcode", ob_get_clean(), $mixed );
}


/*-----------------------------------------------------------------------------------*/
/*  Master Slider Slide
/*-----------------------------------------------------------------------------------*/

add_shortcode( 'ms_slide', 'msp_masterslider_slide_shortcode' );

function msp_masterslider_slide_shortcode( $atts, $content = null ) {
	extract( shortcode_atts(
				array(
					'src'       => '',
					'src_full'  => '',

					'css_class' => '',
					'css_id'    => '',
					'style' 	=> '',

					'src_blank'	=> MSWP_BLANK_IMG, // url to black image for preloading job

					'title'     => '', // image title
					'alt'       => '', // image alternative text

					'link'       => '',
		            'link_title' => '',
		            'link_class' => '',
		            'link_id'    => '',
		            'link_rel'   => '',

					'target'    => '_blank',
					'video'     => '', // youtube or vimeo video link
					'auto_play_video' => '', // autoplay for youtube or vimeo videos

					'mp4'		=> '', // self host video bg
					'webm'		=> '', // self host video bg
					'ogg'		=> '', // self host video bg

					'info'      => '',

					'autopause' => 'false',
					'mute'		=> 'true',
					'loop' 		=> 'true',
					'vbgalign' 	=> 'fill',

					'crop_width'  => '', // empty means auto
					'crop_height' => '', // empty means auto

					'thumb' 	=> '',
					'tab' 		=> '',
					'tab_thumb' => '',
					'delay'     => '', // data-delay
					'bgalign'	=> '',  // data-fill-mode
					'bgcolor' 	=> '',
					'pattern'   => '',
					'tintcolor' => ''
				)
				, $atts, 'masterslider_slide' )
			 );

	$css_class = empty( $css_class ) ? '' : ' '.$css_class;

	$css_id    = empty( $css_id ) ? '' : 'id="' . esc_attr($css_id) . '"';

	$style    .= empty( $bgcolor ) ? '' : 'background-color:' . $bgcolor . ';';
	$style_attr= empty( $style ) ? '' : 'style="'. esc_attr( $style ) .'"';

		// create delay attr if is set
	$data_delay = empty( $delay ) ? '' : 'data-delay="'.( (float) $delay ).'"';

	// create bg align attr if is set
	$data_align = empty( $bgalign )?'':'data-fill-mode="'. esc_attr( $bgalign ) .'"';

	// add slide starter tag
	$slide_start_tag = sprintf( '<div %s class="ms-slide%s" %s %s %s >', $css_id, esc_attr( $css_class ), $data_delay, $data_align, $style_attr )."\n";

	// making start tag filterable for extend purposes
	$slide_start_tag = apply_filters( 'msp_masterslider_slide_start_tag', "\t\t\t\t".$slide_start_tag, $atts );

	// parse slide content ///////////////////////////////////////////

	$slide_content = "";

	// if blank image is not set use original img instead
	$src_blank 	= empty( $src_blank ) ? $src : $src_blank;

    if( ! empty( $pattern ) || ! empty( $tintcolor ) ){
        $inline_style   = ! empty( $tintcolor ) ? esc_attr( 'background-color:' . $tintcolor . ';') : '';
        $slide_content .= "\t\t\t\t\t" . sprintf('<div class="ms-pattern %s" style="%s"></div>', $pattern, $inline_style )."\n";
    }

	// decode escaped square brackets
	$title 		= str_replace( array( "%5B", "%5D" ), array('[', ']'), $title 		);
	$alt   		= str_replace( array( "%5B", "%5D" ), array('[', ']'), $alt   		);
	$link_title = str_replace( array( "%5B", "%5D" ), array('[', ']'), $link_title  );
	$link_rel   = str_replace( array( "%5B", "%5D" ), array('[', ']'), $link_rel    );

	// main image markup
	if( ! empty( $src ) ) {
		$crop_width  = empty( $crop_width  ) || ! is_numeric( $crop_width  ) ? NULL : (int)$crop_width;
		$crop_height = empty( $crop_height ) || ! is_numeric( $crop_height ) ? NULL : (int)$crop_height;

		if( strpos( $src, '{{image}}' ) === false )
			$src = msp_get_the_absolute_media_url( $src );

		if( $crop_width ||  $crop_height )
			$src = msp_get_the_resized_image_src( $src, $crop_width, $crop_height, true );

		$slide_content .= "\t\t\t\t\t" . sprintf('<img src="%s" alt="%s" title="%s" data-src="%s" />', esc_url( $src_blank ), esc_attr( $alt ), esc_attr( $title ), esc_url( $src ) )."\n";
	}

	$self_video_markup = '';
	// self host video background
	if( ! empty( $mp4 ) )
		 $self_video_markup .= "\t\t".sprintf('<source src="%s" type="video/mp4"/>', esc_url( $mp4 ) )."\n";

	if( ! empty( $webm ) )
		 $self_video_markup .= "\t\t".sprintf('<source src="%s" type="video/webm"/>', esc_url( $webm ) )."\n";

	if( ! empty( $ogg ) )
		 $self_video_markup .= "\t\t".sprintf('<source src="%s" type="video/ogg"/>', esc_url( $ogg ) )."\n";


	if( ! empty( $self_video_markup ) ) {
		$slide_content .= "\t".sprintf(	'<video data-autopause="%s" data-mute="%s" data-loop="%s" data-fill-mode="%s" >%s%s%s</video>',
									msp_is_true( $autopause ), esc_attr( $mute ), esc_attr( $loop ), esc_attr( $vbgalign ), "\n", $self_video_markup, "\t" )."\n";
	}


	// link markup
	if( ! empty( $link ) ){
		$link = '{{slide-image-url}}' == $link ? msp_get_the_absolute_media_url( $src_full ) : esc_url($link);

		$att_link_target = $target     ? 'target="'. esc_attr( $target ) .'"' : '';
		$att_link_rel    = $link_rel   ? 'rel="'.    esc_attr( $link_rel ) .'"' : '';
		$att_link_title  = $link_title ? 'title="'.  esc_attr( $link_title ) .'"' : '';
		$att_link_class  = $link_class ? 'class="'.  esc_attr( $link_class ) .'"' : '';
		$att_link_id     = $link_id    ? 'id="'.     esc_attr( $link_id ) .'"' : '';

		$slide_content .= "\t".sprintf('<a href="%s" %s %s %s %s %s>%s</a>', $link, $att_link_target,
		                               			$att_link_rel, $att_link_title, $att_link_class,
		                               			$att_link_id, wp_kses_post( $title ) )."\n";
	}

	// add layers that passed as content
	if( ! empty( $content ) )
		 $slide_content .= $content."\n";

	// thumb markup
	if( ! empty( $thumb ) ) {

		if( strpos( $thumb, '{{thumb}}' ) === false )
			$thumb = msp_get_the_absolute_media_url( $thumb );

		$slide_content .= "\t".sprintf('<img class="ms-thumb" src="%s" alt="%s" />', esc_url( $thumb ), esc_attr( $alt ) )."\n";
	}

	// markup for thumb in tab
	$tab_image   = empty( $tab_thumb ) ? '' : sprintf('<img class="ms-tab-thumb" src="%s" alt="%s" />', msp_get_the_absolute_media_url( $tab_thumb ), esc_attr( $alt ) )."\n";
	$tab_context = empty( $tab )       ? '' : sprintf('<div class="ms-tab-context">%s</div>', wp_kses_post( str_replace( '&quote;', '"', wp_specialchars_decode( $tab ) ), $alt ))."\n";

	// tab markup
	if( ! empty( $tab_image ) || ! empty( $tab_context ) ) {
		$slide_content .= "\t".sprintf( '<div class="ms-thumb" >%s%s</div>', $tab_image, $tab_context)."\n";
	}

	// video markup
	if( ! empty( $video ) )
		 $slide_content .= "\t".sprintf('<a href="%s" data-type="video"></a>', esc_url( $video ) )."\n";

	// end slide content ////////////////////////////////////////////

	$slide_end_tag  = "\t\t\t\t</div>";

	// making end tag filterable for extend purposes
	$slide_end_tag = apply_filters("msp_masterslider_slide_end_tag", $slide_end_tag, $atts );

	$slide_content = do_shortcode( $slide_content );

	$output = empty( $slide_content ) ? '' : $slide_start_tag.$slide_content.$slide_end_tag;

	return apply_filters( 'masterslider_slide_content', $output, $slide_start_tag, $slide_content, $slide_end_tag );
}


/*-----------------------------------------------------------------------------------*/
/*  Master Slider Layer
/*-----------------------------------------------------------------------------------*/

add_shortcode( 'ms_layer', 'msp_masterslider_layer_shortcode' );

function msp_masterslider_layer_shortcode( $atts, $content = null ) {

	// merge input and default attrs
	$merged = shortcode_atts(
				  array(
					'src'       => '', // image layer src or video cover image
					'src_blank'	=> MSWP_BLANK_IMG, // url to black image for preloading job

					'widthlimit'=> '-1',

					'type'		=> 'text', // layer type : text, image, video, hotspot
					'resize'	=> 'true',

					'css_class' => '',
            		'btn_class' => 'ms-default-btn',
					'css_id'    => '',
					'style_id'  => '',

					'action'    => 'next',
            		'use_action'=> 'false',
            		'to_slide'  => 1,

					'offsetx'   => '',
					'offsety'   => '',
					'origin'    => 'tl',
					'fixed'     => 'false',

					'show_effect'	=> 'fade',
					'show_duration'	=> '1000',
					'show_delay'    => '0', // when the show transition effect starts, in milliseconds
					'show_ease'		=> 'linear',

					'style'			=> '',

					'use_hide'      => 'false',
					'hide_effect'   => '',
					'hide_duration' => '1000',
					'hide_delay'    => '1000',
					'hide_ease'     => '',
					'title'     	=> '',

            		'tooltip_align' => 'top',
            		'tooltip_stay_hover' => 'true',
            		'tooltip_width' 	 => '',

            		'parallax' 	=> '',

					'rel'       => '', // image alternative text
					'alt'       => '', // image alternative text
					'link'      => '', // image external url
					'target'    => '_blank',

					'video'     => '', // youtube or vimeo video link
					'width'	    => '',
					'height'    => ''
				  )
				  , $atts, 'masterslider_layer' );

		extract( $merged );


	$wrapper_class = trim( 'ms-layer '. $css_class.' '. $style_id );
	$id_attr = empty( $css_id ) ? '' : 'id="'. esc_attr( $css_id ) .'"';


	// position attrs
	$data_offset_x  = empty( $offsetx ) ? 'data-offset-x="0"' : 'data-offset-x="'.rtrim( $offsetx, 'px' ).'"' ;
	$data_offset_y  = empty( $offsety ) ? 'data-offset-y="0"' : 'data-offset-y="'.rtrim( $offsety, 'px' ).'"' ;
	$data_origin    = empty( $origin  ) ? 'data-origin="tl"'  : 'data-origin="'.$origin.'"' ;

	// custom style + size styles
	$style_size  = $style;

	// dont't add width and height style for hotspot
	if( 'hotspot' !== $type ) {
	 	$style_size .= empty( $width  ) ? '' : 'width:' .rtrim( $width , 'px' ).'px;' ;
		$style_size .= empty( $height ) ? '' : 'height:'.rtrim( $height, 'px' ).'px;' ;
	}


	$show_duration = ( ! is_numeric( $show_duration ) || empty( $show_duration ) ) ? 1000 : (int)$show_duration;
	$show_delay    = ( ! is_numeric( $show_delay    ) || empty( $show_delay    ) ) ?    0 : (int)$show_delay;

	$hide_duration = ( ! is_numeric( $hide_duration ) || empty( $hide_duration ) ) ? 1000 : (int)$hide_duration;
	$hide_delay    = ( ! is_numeric( $hide_delay    ) || empty( $hide_delay    ) ) ? 1000 : (int)$hide_delay;

	$hide_start_time = $show_duration + $show_delay + $hide_delay;



	// create widthlimit attr if it's not default value
	$data_widthlimit = ( (int)$widthlimit < 1 ) ? '': 'data-widthlimit="'.((int)$widthlimit).'"';

	// create type attr if it's not default value
	$data_type = ( 'text' === $type ) ? '': 'data-type="'.$type.'"';

	// create resize attr if it's not default value
	$data_resize = ( 'true' == $resize || 'yes' == $resize )? '': 'data-resize="'.$resize.'"';

	// create show_effect attr if it's not default value
	$data_show_effect = ( 'fade' === $show_effect ) ? '': 'data-effect="'.$show_effect.'"';

	// create show_duration attr if it's not default value
	$data_show_duration = ( 1000 === (int)$show_duration ) ?'':'data-duration="'.((int)$show_duration).'"';

	// create show_delay attr if it's not default value
	$data_show_delay = ( 0 === (int)$show_delay ) ? '': 'data-delay="'.((int)$show_delay).'"';

	// create show_ease attr if it's not default value
	$data_show_ease = 'data-ease="'.$show_ease.'"';

	// create parallax attr if it's not default value
	$data_parallax = empty( $parallax ) ? '': 'data-parallax="'.$parallax.'"';

	// create fixed attr if it's not default value
	$data_fixed = ( 'true' === $fixed ) ? 'data-fixed="true"' : '';



	if( 'true' != $use_hide ){

		 $data_hide_effect = $data_hide_duration = $data_hide_time = $data_hide_ease = $data_hide_delay = '';

	} else {

		 // create hide_effect attr if it's not default value
		 $data_hide_effect = empty( $hide_effect )?'':'data-hide-effect="'.$hide_effect.'"';

		 // create hide_duration attr if it's not default value
		 $data_hide_duration = empty( $hide_duration )?'':'data-hide-duration="'.$hide_duration.'"';

		 // create hide_time attr if it's not default value
		 $data_hide_time = empty( $hide_time )?'':'data-hide-time="'.$hide_time.'"';

		 // create hide_ease attr if it's not default value
		 $data_hide_ease = empty( $hide_ease )?'':'data-hide-ease="'.$hide_ease.'"';

		 // create data-hidedelay ease attr if it's not default value
		 $data_hide_delay = 'data-hide-time="'.$hide_start_time.'"';
	}


	$rel_attr = empty( $rel ) ? '' : 'rel="'.$rel.'"';

	$rel_attr = apply_filters( 'masterslider_layer_shortcode_attr_rel', $rel_attr, $rel );

	$link     = apply_filters( 'masterslider_layer_shortcode_attr_link', $link );

	// create data-link attr if it's not default value
	$data_link = empty( $link ) ? '' : 'data-link="'.$link.'"';

	// create data-action attr if it's enabled and defined
	if( 'true' == $use_action ) {
		$data_action  = 'gotoSlide' == $action ? 'data-action="'.$action.'('.(int)$to_slide.')"' : 'data-action="'.$action.'"';
		$data_link    = '';
		$link 		  = '#';

	} else {
		$data_action  = '';
	}


	// convert relative image link to absolute
	$src = ! empty( $src ) ? msp_get_the_absolute_media_url( $src ) : $src;

	// add data align if layer type is hotspot
	$data_align      = 'hotspot' == $type ? 'data-align="'.$tooltip_align.'"' : '';
	$data_stay_hover = 'hotspot' == $type ? 'data-stay-hover="'.$tooltip_stay_hover.'"' : '';
	$data_tp_width   = 'hotspot' == $type ? 'data-width="'.$tooltip_width.'"' : '';
	$data_target     = 'hotspot' == $type ? 'data-target="'.$target.'"' : '';

	$effect_attrs 	 = sprintf( '%s %s %s %s %s %s %s %s %s %s',
									  $data_show_effect, $data_show_duration, $data_show_delay, $data_show_ease, $data_hide_effect,
									  $data_hide_duration, $data_hide_time, $data_hide_ease, $data_hide_delay, $data_fixed );

	$common_attrs 	= sprintf( '%s %s %s %s %s %s %s %s', $data_parallax, $data_type, $data_resize, $data_align, $data_stay_hover, $data_tp_width, $data_target, $data_widthlimit );

	$position_attrs = sprintf( '%s %s %s', $data_offset_x, $data_offset_y, $data_origin );

	 // store layer markup
	 $layer = '';

	 // start layer markup generation /////////////////////////////////////////

	 // if layer type was image ..
	 if( 'image' == $type ) {

	 	// if was linked image
		if( ! empty( $link ) && 'true' != $use_action ) {

				$layer_image = "\n\t".sprintf( '<img src="%s" data-src="%s" alt="%s" style="%s" %s %s %s %s />', esc_url( $src_blank ), esc_url( $src ), esc_attr( $alt ), esc_attr( $style_size ), $effect_attrs, $data_type, $data_parallax, $position_attrs )."\n";
				$layer .= sprintf( '<a %s class="%s" href="%s" target="%s" %s >%s</a>', $id_attr, esc_attr( $wrapper_class ), esc_url( $link ), esc_attr( $target ), $rel_attr, $layer_image ). "\n";

		// or single image
		} else {
			$layer .= sprintf( '<img %s class="%s" src="%s" data-src="%s" alt="%s" style="%s" %s %s %s %s %s />',
									 $id_attr, esc_attr( $wrapper_class ), esc_url( $src_blank ), esc_url( $src ), esc_attr( $alt ), esc_attr( $style_size ), $effect_attrs, $common_attrs, $rel_attr, $data_action, $position_attrs )."\n";
		}

	} elseif( 'button' == $type ) {

		$layer_content = ! empty( $content ) ? do_shortcode( $content ) : '';
	 	$layer = sprintf( '<a %s href="%s" target="%s" class="%s %s" %s %s %s %s %s >%s</a>',
								 $id_attr, esc_url( $link ), esc_attr( $target ), esc_attr( $wrapper_class ), esc_attr( $btn_class ), $effect_attrs, $common_attrs, $position_attrs, $rel_attr, $data_action, $layer_content )."\n";

	// if layer type was text, video or hotspot
	} else {

		$layer_content = '';
		// add video iframe as layer content if type was video
		if( 'video' == $type ) {
			// add cover image if src attr is set
			if( ! empty( $src ) )
				$layer_content .= sprintf( '<img src="%s" data-src="%s" alt="%s" />', esc_url( $src_blank ), $src, $alt );
			// add video iframe markup if video is set
			if( ! empty( $video ) ){
					$vid_width  = empty( $width  ) ? '460' : rtrim( $width , 'px' ) ;
					$vid_height = empty( $height ) ? '270' : rtrim( $height, 'px' ) ;
					$layer_content .= sprintf( '<iframe src="%s" width="%s" height="%s" > </iframe>', $video, $vid_width, $vid_height );
			}

		// add shortcode content if layer type was text or hotspot
		} else {
			$layer_content .= ! empty( $content ) ? do_shortcode( wp_unslash( $content ) ) : '';
		}

		$layer = sprintf( '<div %s class="%s" style="%s" %s %s %s %s >%s</div>',
								 $id_attr, esc_attr( $wrapper_class ), esc_attr( $style_size ), $data_link, $effect_attrs, $common_attrs, $position_attrs, $layer_content )."\n";
	}

	// end layer markup generation //////////////////////////////////////////


	 return apply_filters( "masterslider_layer_shortcode", "\t\t\t\t\t".$layer, $merged, $atts, $content );
}


/*-----------------------------------------------------------------------------------*/
/*  Master Slider Slide info
/*-----------------------------------------------------------------------------------*/

add_shortcode( 'ms_slide_info', 'msp_masterslider_slide_info_shortcode' );

function msp_masterslider_slide_info_shortcode( $atts, $content = null ) {

	 $args = shortcode_atts(
		  array(
				'css_class' => '',
				'tag_name'  => 'div'
		  )
		  , $atts, 'masterslider_slide_info' );

	 extract( $args );

     if( is_array( $css_class ) ){
        $css_class = join( ' ' , $css_class );
     } else {
        $css_class = empty( $css_class ) ? '' : ' '.$css_class;
     }

	 // create slide info markup
	 $output = sprintf( '<%1$s class="ms-info%2$s">%3$s</%1$s>', tag_escape( $tag_name ), esc_attr( $css_class ), do_shortcode( wp_unslash( $content ) ) )."\n";

	 return apply_filters( 'masterslider_slide_info_shortcode', "\t\t\t\t\t".$output, $args );
}

/*-----------------------------------------------------------------------------------*/

add_shortcode( 'ms_slide_flickr', 'msp_masterslider_slide_flickr_shortcode' );

function msp_masterslider_slide_flickr_shortcode( $atts, $content = null ) {

	 $args = shortcode_atts(
		  array(
		      'src_blank'	=> MSWP_BLANK_IMG, // url to black image for preloading job
				'thumb'  => 'yes'
		  )
		  , $atts, 'masterslider_slide_flickr' );

	extract( $args );

	$output = sprintf( '<img src="%s" data-src="{{image}}" alt="{{title}}"/>', esc_attr( $src_blank ) ) . "\n";

	if( 'yes' == $thumb )
		$output .= "\t\t\t\t" . '<img class="ms-thumb" src="{{thumb}}" alt="{{title}}"/>';


	return apply_filters( 'masterslider_slide_flickr_shortcode', "\t\t\t\t".$output, $args );
}

/**
 * prevent closing script tag
 *
 * @param string $data
 * @return string
 */
function msp_masterslider_prevent_closing_script_tag( $data ) {
	return str_replace('</', '<\\/', $data);
}