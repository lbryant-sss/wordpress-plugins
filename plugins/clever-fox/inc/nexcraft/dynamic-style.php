<?php
if( ! function_exists( 'cleverfox_nexcraft_dynamic_styles' ) ):
    function cleverfox_nexcraft_dynamic_styles() {
		$output_css = '';
		
		$theme = wp_get_theme(); // gets the current theme
		
		/**
		 * Logo Width 
		 */
		 $logo_width			= get_theme_mod('logo_width','220');
		if($logo_width !== '') { 
				$output_css .=".main-navigation a img, .mobile-logo img {
					max-width: " .esc_attr($logo_width). "px;
				}\n";
			}
		
		/**
		 *  Typography Body
		 */
		 $nexcraft_body_text_transform	 	 = get_theme_mod('nexcraft_body_text_transform','inherit');
		 $nexcraft_body_font_style	 		 = get_theme_mod('nexcraft_body_font_style','inherit');
		 $nexcraft_body_font_size	 		 = get_theme_mod('nexcraft_body_font_size','15');
		 $nexcraft_body_line_height		 = get_theme_mod('nexcraft_body_line_height','1.5');
		
		 $output_css .=" body{ 
			font-size: " .esc_attr($nexcraft_body_font_size). "px;
			line-height: " .esc_attr($nexcraft_body_line_height). ";
			text-transform: " .esc_attr($nexcraft_body_text_transform). ";
			font-style: " .esc_attr($nexcraft_body_font_style). ";
		}\n";		 
		
		/**
		 *  Typography Heading
		 */
		 for ( $i = 1; $i <= 6; $i++ ) {	
			 $nexcraft_heading_text_transform 	= get_theme_mod('nexcraft_h' . $i . '_text_transform','inherit');
			 $nexcraft_heading_font_style	 	= get_theme_mod('nexcraft_h' . $i . '_font_style','inherit');
			 $nexcraft_heading_font_size	 		 = get_theme_mod('nexcraft_h' . $i . '_font_size');
			 $nexcraft_heading_line_height		 	 = get_theme_mod('nexcraft_h' . $i . '_line_height');
			 
			 $output_css .=" h" . $i . "{ 
				font-size: " .esc_attr($nexcraft_heading_font_size). "px;
				line-height: " .esc_attr($nexcraft_heading_line_height). ";
				text-transform: " .esc_attr($nexcraft_heading_text_transform). ";
				font-style: " .esc_attr($nexcraft_heading_font_style). ";
			}\n";
		 }
		 	
			
	 wp_add_inline_style( 'nexcraft-style', $output_css );
    }
endif;
add_action( 'wp_enqueue_scripts', 'cleverfox_nexcraft_dynamic_styles' );
?>