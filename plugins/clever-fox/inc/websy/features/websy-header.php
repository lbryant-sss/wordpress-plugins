<?php
function websy_lite_header_settings( $wp_customize ) {
$selective_refresh = isset( $wp_customize->selective_refresh ) ? 'postMessage' : 'refresh';	
    // Button
	$wp_customize->add_setting(
		'hdr_nav_btn2'
			,array(
			'capability'     	=> 'edit_theme_options',
			'sanitize_callback' => 'webique_sanitize_text',
			'priority' => 5,
		)
	);

	$wp_customize->add_control(
	'hdr_nav_btn2',
		array(
			'type' => 'hidden',
			'label' => __('Button','clever-fox'),
			'section' => 'above_header',
		)
	);
	

	$wp_customize->add_setting( 
		'hide_show_nav_btn2' , 
			array(
			'default' => '1',
			'capability'     => 'edit_theme_options',
			'sanitize_callback' => 'webique_sanitize_checkbox',
			'priority' => 6,
		) 
	);
	
	$wp_customize->add_control(
	'hide_show_nav_btn2', 
		array(
			'label'	      => esc_html__( 'Hide/Show', 'clever-fox' ),
			'section'     => 'above_header',
			'type'        => 'checkbox'
		) 
	);	

	// icon // 
	$wp_customize->add_setting(
    	'nav_btn2_icon',
    	array(
	        'default' => 'fa-user',
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'edit_theme_options',
			'priority' => 7,
		)
	);	

	$wp_customize->add_control(new Webique_Icon_Picker_Control($wp_customize, 
		'nav_btn2_icon',
		array(
		    'label'   		=> __('Icon','gradiant'),
		    'section' 		=> 'above_header',
			'iconset' => 'fa',
		))  
	);
		
	// Button Label // 
	$wp_customize->add_setting(
    	'nav_btn2_lbl',
    	array(
            'default' => 'Consult Now',
			'sanitize_callback' => 'webique_sanitize_text',
			'capability' => 'edit_theme_options',
			'priority' => 7,
		)
	);	

	$wp_customize->add_control( 
		'nav_btn2_lbl',
		array(
		    'label'   		=> __('Button Label','clever-fox'),
		    'section' 		=> 'above_header',
			'type'		 =>	'text'
		)  
	);
	
	// Button Link // 
	$wp_customize->add_setting(
    	'nav_btn2_link',
    	array(
			'sanitize_callback' => 'webique_sanitize_url',
			'capability' => 'edit_theme_options',
			'priority' => 8,
		)
	);	

	$wp_customize->add_control( 
		'nav_btn2_link',
		array(
		    'label'   		=> __('Button Link','clever-fox'),
		    'section' 		=> 'above_header',
			'type'		 =>	'text'
		)  
	);	
}
add_action( 'customize_register', 'websy_lite_header_settings' );