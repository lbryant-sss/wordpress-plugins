(function($) {
	/**
	 * This file handling some LIVE to the LoginPress Customizer live preview.
	 */
	/**
	 * loginpress_manage_customizer_controls
	 * @param [array/string] controller controller name.
	 * @param boolean action    Turn on/off the customizer control.
	 * @return string           CSS code.
	 */
	function loginpress_manage_customizer_controls( controller, action ) {

		if ( Array.isArray( controller ) ) {
		  controller.forEach( function ( item, index ) {
			if ( 'on' == action ) {
			  $( '#customize-control-loginpress_customization-' + item ).fadeIn().css( 'display', 'list-item' );
			} else {
			  $( '#customize-control-loginpress_customization-' + item ).fadeOut().css( 'display', 'none' );
			}
		  } );
		} else {
		  if ( 'on' == action ) {
			$( '#customize-control-loginpress_customization-' + controller ).fadeIn().css( 'display', 'list-item' );
		  } else {
			$( '#customize-control-loginpress_customization-' + controller ).fadeOut().css( 'display', 'none' );
		  }
		}
	}
	var formbg;
	var get_theme;
// Wait until the customizer is ready
wp.customize.bind('ready', function() {
    var highestValue = 0;
    var value = {
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        unit: 'px',
        lock: 0
    }

    // Helper function to update the hidden input
    function updateSpacingControl(settingName, value) {
        // Create a string that combines all the values with the unit
        if (
			!isNaN(value.top) &&
			!isNaN(value.right) &&
			!isNaN(value.bottom) &&
			!isNaN(value.left) &&
			value.unit !== undefined
		) {
			var combinedValue = `${value.top}${value.unit} ${value.right}${value.unit} ${value.bottom}${value.unit} ${value.left}${value.unit}`;
			wp.customize(settingName).set(combinedValue);
		} else {
			wp.customize(settingName).set('0px 0px 0px 0px');
		}
		
		    
        // Set the combined value in the Customizer
    }

    // Centralized function to handle value updates
    function updateValues($controlWrapper, settingName, type, inputvalue=false) {
        // Get the current values from the inputs
        var top = parseFloat($controlWrapper.find('input[id*="_top"]').val()) || 0;
        var left = parseFloat($controlWrapper.find('input[id*="_left"]').val()) || 0;
        var right = parseFloat($controlWrapper.find('input[id*="_right"]').val()) || 0;
        var bottom = parseFloat($controlWrapper.find('input[id*="_bottom"]').val()) || 0;
        var unit = $controlWrapper.find('select[id*="_unit"]').val() || 'px';
        var lock = $controlWrapper.find('input[type="checkbox"][id*="_lock"]').is(':checked') ? 1 : 0;

        // Update the value object
        value = {
            top: top,
            left: left,
            right: right,
            bottom: bottom,
            unit: unit,
            lock: lock
        };
		top = isNaN(top) ? 0 : top;
        left = isNaN(left) ? 0 : left;
        right = isNaN(right) ? 0 : right;
        bottom = isNaN(bottom) ? 0 : bottom;

        // If lock is enabled, sync all fields to the highest value
		if(type){
			highestValue = Math.max(parseFloat(top) || 0, parseFloat(left) || 0, parseFloat(right) || 0, parseFloat(bottom) || 0);
            if (highestValue) {
                value.top = value.left = value.right = value.bottom = highestValue;
				highestValue = isNaN(highestValue) ? 0 : highestValue;
                $controlWrapper.find('input[type="number"]').each(function() {
                    $(this).val(highestValue);  // Set the highest value to all related inputs
                });
            }
		}else{
			
			if (lock === 1) {
				value.top = value.left = value.right = value.bottom = inputvalue;
				inputvalue = isNaN(inputvalue) ? 0 : inputvalue;
				$controlWrapper.find('input[type="number"]').each(function() {
					$(this).val(inputvalue);
				});
				
			}
		}

        // Update the setting in the Customizer
        updateSpacingControl(settingName, value);
    }

    // Handle updates for a specific control
    function bindControlEvents($controlWrapper, settingName) {
        // Listen for input changes in individual padding/margin fields
        $controlWrapper.find('input[type="number"]').on('input change keyup', function() {
			// Get the current input value
			var inputVal = $(this).val();

			// Allow only numbers and one decimal point
			var sanitizedVal = inputVal.replace(/^(0|[1-9]\d*)(\.\d{0,1})?$/g, '$&'); // Match numbers with an optional decimal point
		
			// Set the sanitized value back to the input
			$(this).val(sanitizedVal);
            updateValues($controlWrapper, settingName, false, parseFloat($(this).val()));
        });

        // Handle changes in the unit dropdown
        $controlWrapper.find('select[id*="_unit"]').on('change', function() {
            updateValues($controlWrapper, settingName, true);
        });

        // Handle the lock toggle
        $controlWrapper.find('input[type="checkbox"][id*="_lock"]').on('change', function() {
            updateValues($controlWrapper, settingName, true);
        });
    }
	if($('.customize-control-loginpress-spacing [data-loginpresstarget="customize-control-loginpress_customization-customize_form_padding"]').length > 0){
		// Bind control events for padding
		bindControlEvents($('.customize-control-loginpress-spacing [data-loginpresstarget="customize-control-loginpress_customization-customize_form_padding"]'), 'loginpress_customization[customize_form_padding]');
	}
	if($('.customize-control-loginpress-spacing [data-loginpresstarget="customize-control-loginpress_customization-textfield_margin"]').length > 0){
    	// Bind control events for margin
    	bindControlEvents($('.customize-control-loginpress-spacing [data-loginpresstarget="customize-control-loginpress_customization-textfield_margin"]'), 'loginpress_customization[textfield_margin]');
	}
	// Function to update padding inputs based on margin input
    function updatePaddingFromMargin($marginControlWrapper, marginControlTarget) {
        // Get the value of the margin input
        var marginValue = $marginControlWrapper.find('input[type="text"]').val() || '';
		var unitValue = $marginControlWrapper.find('input[type="text"]').val().match(/[a-zA-Z%]+/) || ['px']; // Get the unit (defaults to 'px' if not found)
        var values = marginValue.split(' ');

        // Ensure we have 4 values and set default to '0' if not
        var top = values[0] ? parseFloat(values[0]) || 0 : 0;
        var right = values[1] ? parseFloat(values[1]) || 0 : 0;
        var bottom = values[2] ? parseFloat(values[2]) || 0 : 0;
        var left = values[3] ? parseFloat(values[3]) || 0 : 0;
		top = isNaN(top) ? 0 : top;
        left = isNaN(left) ? 0 : left;
        right = isNaN(right) ? 0 : right;
        bottom = isNaN(bottom) ? 0 : bottom;
        
        // Update the corresponding padding inputs
        $('.customize-control-loginpress-spacing [data-loginpresstarget="'+marginControlTarget+'"] input[type="number"][id*="_top"]').val(top);
        $('.customize-control-loginpress-spacing [data-loginpresstarget="'+marginControlTarget+'"] input[type="number"][id*="_right"]').val(right);
        $('.customize-control-loginpress-spacing [data-loginpresstarget="'+marginControlTarget+'"] input[type="number"][id*="_bottom"]').val(bottom);
        $('.customize-control-loginpress-spacing [data-loginpresstarget="'+marginControlTarget+'"] input[type="number"][id*="_left"]').val(left);
        $('.customize-control-loginpress-spacing [data-loginpresstarget="'+marginControlTarget+'"] select[id*="_unit"]').val(unitValue);
    }
	if($('#customize-control-loginpress_customization-customize_form_padding').length > 0){
		updatePaddingFromMargin($('#customize-control-loginpress_customization-customize_form_padding'), 'customize-control-loginpress_customization-customize_form_padding');
	}
	if($('#customize-control-loginpress_customization-textfield_margin').length > 0){
		updatePaddingFromMargin($('#customize-control-loginpress_customization-textfield_margin'), 'customize-control-loginpress_customization-textfield_margin');
	}
});


	jQuery(document).ready(function($) {

		// Update gallery default thumbnail on load. @since 3.0.4
		get_theme = jQuery('.customize-control-checkbox-multiple input[type="radio"]:checked').val();
    	var defaultThumbnails = jQuery('.customize-control-checkbox-multiple input[type="radio"]:checked').next('label').find('img').attr('src');
    	$('.loginpress_gallery_thumbnails:first-child').find('img').attr({'src': defaultThumbnails,'title': defaultThumbnails});

		/**
		 * Presets Settings
		 * @param  {[type]} ) {               checkbox_values [checkbox value]
		 * @return {[type]}   [description]
		 * @since 1.0.9
		 * @version 1.1.3
		 */
		 jQuery( '.customize-control-checkbox-multiple input[type="radio"]' ).on( 'change', function() {

			checkbox_values = jQuery(this)
			.parents( '.customize-control' )
			.find( 'input[type="radio"]:checked' )
			.val();

			style_values = jQuery(this)
			.parents( '.customize-control' )
			.find( 'input[type="radio"]:checked' )
			.data('style');

			var val = [];
			val.push(checkbox_values);
			val.push(style_values);
			// console.log(val);
			jQuery(this)
			.parents( '.customize-control' )
			.find( 'input[type="hidden"]' )
			.val(checkbox_values)
			.delay(500)
			.trigger( 'change' );

			// Update gallery default thumbnail on presets change. @since 1.1.3
			var defaultThumbnails = jQuery(this).next('label').find('img').attr('src');
			$('.loginpress_gallery_thumbnails:first-child').find('img').attr({'src': defaultThumbnails,'title': defaultThumbnails});
			// if theme is not Company remove label controls.
			if(checkbox_values == 'default2'){
				$('#customize-control-loginpress_customization-textfield_label_color,#customize-control-loginpress_customization-customize_form_label').hide();
			}else{
				$('#customize-control-loginpress_customization-textfield_label_color,#customize-control-loginpress_customization-customize_form_label').show();
			}
			if(checkbox_values == 'default18') {
				loginpress_manage_customizer_controls( ['setting_logo', 'customize_logo_width', 'customize_logo_height'], 'off' );
			} else {
				loginpress_manage_customizer_controls( ['setting_logo', 'customize_logo_width', 'customize_logo_height'], 'on' );
			}
			formbg = $('#customize-preview iframe').contents().find( '#login' ).css( 'background');
  		} );
	} ); // jQuery( document ).ready

	/**
	 * [loginpress_find find CSS classes in WordPress customizer]
	 * @param  {String} [finder='#loginpress-customize'] [find class in customizer]
	 * @return {[type]}                                  [iframe content finder]
	 * @since 1.1.0
	 * @version 1.1.3
	 */
   	function loginpress_find( finder = '#loginpress-customize' ) {

		var customizer_finder = $('#customize-preview iframe').contents().find( finder );
		return customizer_finder;
	}
  	var formbgimg = '';
	// function for change LoginPress background-image in real time...
	function loginpress_background_img( setting, target ) {
		wp.customize( setting, function( value ) {
			value.bind( function( loginPressVal ) {
				if (get_theme == 'minimalist' || change_theme == 'minimalist') {
					if (target == '.login-action-lostpassword #login') {
						target = "#lostpasswordform";
					}
				}
				if ( loginPressVal == '' ) {
					formbgimg = '';
					loginpress_find( target ).css( 'background-image', 'none' );
				} else {
					formbgimg = loginPressVal;
					loginpress_find( target ).css( 'background-image', 'url(' + loginPressVal + ')' );
				}
			} );
		} );
	} // ! loginpress_background_img();

	// function for change LoginPress CSS in real time...
	function loginpress_css_property( setting, target, property, em = false ) {
		// Update the login logo width in real time...
		wp.customize( setting, function( value ) {
			value.bind( function( loginPressVal ) {
				if (get_theme == 'minimalist' || change_theme == 'minimalist') {
					if (target == '#loginform, #login') {
						target = "#loginform";
					}
					
					if (target == '.login-action-lostpassword #login') {
						target = "#lostpasswordform";
					}
				}
				console.log(target)
				if ( loginPressVal == '' ) {
					loginpress_find( target ).css( property, em );
				} else {
					loginpress_find( target ).css( property, loginPressVal );
				}
			} );
		} );
	} // finish loginpress_css_property();
var wp_logo_height = '84';
	// function for change LoginPress CSS in real time...
	function loginpress_new_css_property( setting, target, property, suffix ) {
		// Update the login logo width in real time...
		wp.customize( setting, function( value ) {
			value.bind( function( loginPressVal ) {
				if ((get_theme == 'minimalist' || change_theme == 'minimalist') && property != 'max-width') {
					if (target == '#login') {
						target = "#loginform";
					}
				}
				if ( loginPressVal == '' ) {
					loginpress_find( target ).css( property, '' );
				} else {
					loginpress_find( target )[0].style.setProperty(property , loginPressVal + suffix , 'important' );
					if( property == 'height' ){
						wp_logo_height = loginPressVal;
					}
				}
			} );
		} );
	} // finish loginpress_new_css_property();

	// Declare Variable values for button shadow and button Opacity. Since 1.1.3

	var loginpress_button_shadow_opacity = 'rgba(0,0,0,1)',
	loginpress_button_shadow = 0,
	loginpress_button_inset_shadow = '';
	$(window).on('load', function(){
		loginpress_button_shadow_opacity = 'rgba(0,0,0,'+(parseInt($('#customize-control-loginpress_customization-textfield_shadow_opacity').find('.loginpress-range-slider_val').val())/100)+')';
		loginpress_button_shadow = $('#customize-control-loginpress_customization-textfield_shadow').find('.loginpress-range-slider_val').val();
		if($('#customize-control-loginpress_customization-textfield_inset_shadow').find('.loginpress-radio.loginpress-radio-ios').is(':checked')== true){
			loginpress_button_inset_shadow = ' inset';
		}
		loginpress_find( '#loginform input[type="text"], #loginform input[type="password"]' ).css( 'box-shadow', '0 0 ' + loginpress_button_shadow + 'px ' + loginpress_button_shadow_opacity + loginpress_button_inset_shadow );
		// if theme is not Company remove label controls.
		var checkbox_values = $('#customize-control-customize_presets_settings input[type=radio]:checked').val();
		if(checkbox_values == 'default2'){
			$('#customize-control-loginpress_customization-textfield_label_color,#customize-control-loginpress_customization-customize_form_label').hide();
		}else{
			$('#customize-control-loginpress_customization-textfield_label_color,#customize-control-loginpress_customization-customize_form_label').show();
		}
	});

	// function for change LoginPress Button Shadow in real time... since 1.1.3
	function loginpress_shadow_property( setting, target, property, suffix ) {
		// Update the login logo width in real time...
		wp.customize( setting, function( value ) {
			value.bind( function( loginPressVal ) {
				
				if (get_theme == 'minimalist' || change_theme == 'minimalist') {
					if (target == '#login') {
						target = "#loginform";
					}
				}
				if ( loginPressVal == '' ) {
					loginpress_find( target ).css( property, '' );
				} else {
				loginpress_button_shadow = loginPressVal;
					loginpress_find( target ).css( property, '0 0 ' + loginPressVal + 'px ' + loginpress_button_shadow_opacity + loginpress_button_inset_shadow );
				}
			} );
		} );
	} // finish loginpress_css_property();


	// function for change LoginPress CSS in real time...
	function loginpress_shadow_opacity_property( setting, target, property, suffix ) {
		// Update the login logo width in real time...
		wp.customize( setting, function( value ) {
			value.bind( function( loginPressVal ) {
				if (get_theme == 'minimalist' || change_theme == 'minimalist') {
					if (target == '#login') {
						target = "#loginform";
					}
				}
				if ( loginPressVal == '' ) {
					// loginpress_find( target ).css( property, '' );
				} else {
					loginpress_button_shadow_opacity = 'rgba(0,0,0,'+(loginPressVal/100)+')';
					loginpress_find( target ).css( property, '0 0 ' + loginpress_button_shadow + 'px ' + loginpress_button_shadow_opacity + loginpress_button_inset_shadow );
					// loginpress_shadow_property( 'loginpress_customization[login_button_shadow]', '.login input[type="submit"]', 'box-shadow', 'px' );
				}
			} );
		} );
	} // finish loginpress_css_property();

	wp.customize( 'loginpress_customization[textfield_inset_shadow]', function( value ) {
		value.bind( function( loginPressVal ) {
		  if ( loginPressVal == true ) {
			// loginpress_find( target ).css( property, '' );
			loginpress_button_inset_shadow = ' inset';
		  } else {
			loginpress_button_inset_shadow = '';
			// loginpress_button_shadow_opacity = 'rgba(0,0,0,'+(loginPressVal/100)+')';
			//
			// loginpress_shadow_property( 'loginpress_customization[login_button_shadow]', '.login input[type="submit"]', 'box-shadow', 'px' );
		  }
		  loginpress_find( '#loginform input[type="text"], #loginform input[type="password"]' ).css( 'box-shadow', '0 0 ' + loginpress_button_shadow + 'px ' + loginpress_button_shadow_opacity + loginpress_button_inset_shadow );
		} );
	} );

    // controls for add and remove background video in version 1.1.22
    wp.customize( 'loginpress_customization[background_video]', function( value ) {
        value.bind( function( loginPressVal ) {
			if ( loginPressVal ) {
				$.ajax( {
					url     : loginpress_script.ajaxurl,
					type    : 'post',
					data    : {
						'src'      : loginPressVal,
						'action'   : 'loginpress_video_url',
						'security' : loginpress_script.attachment_nonce
					},
					success : function( response ) {
						// console.log(response);
						var videoExtensions = ['mp4', 'webm', 'ogg']; // Add more if needed

						var urlParts = response.split('.');
						var fileExtension = urlParts[urlParts.length - 1].toLowerCase();

						if (!videoExtensions.includes(fileExtension)) {
							// Handle the case where the URL doesn't point to a video
							$("#customize-control-loginpress_customization-background_video .remove-button").trigger('click');
							$('#customize-control-loginpress_customization-background_video_object, #customize-control-loginpress_customization-video_obj_position,#customize-control-loginpress_customization-background_video_muted').hide();
							$('#customize-control-loginpress_customization-background_video_error').show();
							return;
						} else {
							$('#customize-control-loginpress_customization-background_video_error').hide();
							if (loginpress_find('#loginpress_video-background').length > 0) {
								var video = loginpress_find('#loginpress_video-background')[0];
								loginpress_find('#loginpress_video-background')[0].pause();
								loginpress_find('#loginpress_video-background source')[0].setAttribute('src', response);
								video.style.display = "block";
								video.load();
								video.play();
							} else {
								if (change_theme == 'default8' || change_theme == 'default3' || change_theme == 'default5') {
									$('<div id="loginpress_video-background-wrapper"><video autoplay loop muted id="loginpress_video-background" playsinline><source src="' + response + '"></video></div>').appendTo(loginpress_find('body'));
								} else {
									$('<div id="loginpress_video-background-wrapper"><video autoplay loop id="loginpress_video-background" playsinline muted><source src="' + response + '"></video></div>').appendTo(loginpress_find('#login'));
									var video = loginpress_find('#loginpress_video-background')[0];
									video.load();
									video.play();
								}
							}
						}
					}, // !success.
					error : function( xhr, textStatus, errorThrown ) {
						// console.log('Ajax Not Working');
					}
				} );  // ! $.ajax().

				$('#customize-control-loginpress_customization-background_video_object').fadeIn().css( 'display', 'list-item' );
				$('#customize-control-loginpress_customization-video_obj_position').fadeIn().css( 'display', 'list-item' );
				$('#customize-control-loginpress_customization-background_video_muted').fadeIn().css( 'display', 'list-item' );

			} else {
				if( loginpress_find( '#loginpress_video-background' ).length > 0 ){
					var video = loginpress_find( '#loginpress_video-background' )[0];
					video.remove();
					$('#loginpress_video-background-wrapper').remove();

					$('#customize-control-loginpress_customization-background_video_object').fadeOut().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-video_obj_position').fadeOut().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-background_video_muted').fadeOut().css( 'display', 'none' );
					// loginpress_find( '#loginpress_video-background' )[0].pause();
				}
			}
        } );
    } );

      // Control for mute background video
    wp.customize( 'loginpress_customization[background_video_muted]', function( value ) {
        value.bind( function( loginPressVal ) {
            if(loginpress_find( '#loginpress_video-background' ).length>0){
				var video = loginpress_find( '#loginpress_video-background' )[0];
				if(video.muted){
					video.muted = false;
				} else {
					video.muted = true;
				}
            }
        } );
    } );

	// function for change LoginPress attribute in real time...
	function loginpress_attr_property( setting, target, property ) {
		wp.customize( setting, function( value ) {
			value.bind( function( loginPressVal ) {

				if ( loginPressVal == '' ) {
					loginpress_find( target ).attr( property, '' );
				} else {
					loginpress_find( target ).attr( property, loginPressVal );
				}
			} );
		} );
	}

	/**
	 * loginpress_footer_date_change LoginPress (Date) Change Footer year on live preview.
	 * @param  id       [ID of Section]
	 * @param  target   [Value of section]
	 * @param  output   [output with added year]
	 * @since 1.5.5
	 * 
	 * @return string   [CSS property]
	 */
	function loginpress_footer_date_change( id, target, output ) {
		wp.customize( id, function(value) {
			value.bind( function(loginPressVal ) {

				if (loginPressVal == '') {
					loginpress_find(target).html('');
				} else {
					loginpress_find(target).html(output);
				}
			} );
		} );
	}
	
	// function for change LoginPress input fields in real time...
	function loginpress_input_property( setting, property ) {
		wp.customize( setting, function( value ) {
			value.bind( function( loginPressVal ) {

				if ( loginPressVal == '' ) {
					loginpress_find( '.login input[type="text"]' ).css( property, '' );
					loginpress_find( '.login input[type="password"]' ).css( property, '' );
				} else {
					loginpress_find( '.login input[type="text"]' ).css( property, loginPressVal );
					loginpress_find( '.login input[type="password"]' ).css( property, loginPressVal );
				}
			} );
		} );
	} // finish loginpress_input_property();

	// function for change LoginPress input fields in real time...
	function loginpress_new_input_property( setting, property, suffix ) {
		wp.customize( setting, function( value ) {
			value.bind( function( loginPressVal ) {

				if ( loginPressVal == '' ) {
					loginpress_find( '.login input[type="text"]' ).css( property, '' );
					loginpress_find( '.login input[type="password"]' ).css( property, '' );
				} else {
					loginpress_find( '.login input[type="text"]' ).css( property, loginPressVal + suffix);
					loginpress_find( '.login input[type="password"]' ).css( property, loginPressVal + suffix);
				}
			} );
		} );
	} // finish loginpress_input_property();

	// function for change LoginPress error and welcome messages in real time...
	/**
	 * [loginpress_text_message LoginPress (Error + Welcome) Message live Control.]
	 * @param  id       [Unique ID of the section. ]
	 * @param  target   [CSS Property]
	 * @return string   [CSS property]
	 */
	function loginpress_text_message( id, target ) {
		wp.customize( id, function( value ) {
			value.bind( function( loginPressVal ) {

				if ( loginPressVal == '' ) {
					loginpress_find( target ).html('');
					loginpress_find( target ).css( 'display', 'none' );
				} else {
					loginpress_find( target ).html( loginPressVal );
					loginpress_find( target ).css( 'display', 'block' );
				}
			} );
		} );
	}

	/**
	 * loginpress_change_form_label LoginPress (Label) Text live Control.
	 * @param  id       [Unique ID of the section. ]
	 * @param  target   [CSS Property]
	 * @since 1.1.3
	 * @return string   [CSS property]
	 */
	function loginpress_change_form_label( id, target ) {
		wp.customize( id, function( value ) {
			value.bind( function( loginPressVal ) {

				if ( loginPressVal == '' ) {
					loginpress_find( target ).html('');
				} else {
					loginpress_find( target ).html( loginPressVal );
				}
			} );
		} );
	}

  	var change_theme;

	/**
	 * Change the LoginPress Presets Theme.
	 * @param  {[type]} value [Customized value from user.]
	 * @return {[type]}       [Theme ID]
	 */
	wp.customize( 'customize_presets_settings', function(value) {
		value.bind( function(loginPressVal) {
			change_theme = loginPressVal;
			get_theme = change_theme;
		} );
	} );


	// function for change LoginPress CSS in real time...
	function loginpress_display_control(setting) {
		// Update the login logo width in real time...
		wp.customize(setting, function(value) {
			value.bind(function( loginPressVal ) {
				// Control on footer text.

				if ( 'loginpress_customization[footer_display_text]' == setting && false == loginPressVal ) {

					$('#customize-preview iframe' ).contents().find( '.login #nav' ).css( 'display', 'none' );
					$('#customize-control-loginpress_customization-login_footer_text').fadeOut().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-login_footer_text_decoration').fadeOut().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-login_footer_color').fadeOut().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-login_footer_color_hover').fadeOut().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-login_footer_font_size').fadeOut().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-login_footer_bg_color').fadeOut().css( 'display', 'none' );

				} else if ('loginpress_customization[footer_display_text]' == setting && true == loginPressVal ) {

					if (get_theme == 'minimalist' || change_theme == 'minimalist') {
						$('#customize-preview iframe').contents().find('.login #nav').css('display', 'flex');

					} else {
						$('#customize-preview iframe').contents().find('.login #nav').css('display', 'block');
					}
					$('#customize-control-loginpress_customization-login_footer_text').fadeIn().css( 'display', 'list-item' );
					$('#customize-control-loginpress_customization-login_footer_text_decoration').fadeIn().css( 'display', 'list-item' );
					$('#customize-control-loginpress_customization-login_footer_color').fadeIn().css( 'display', 'list-item' );
					$('#customize-control-loginpress_customization-login_footer_color_hover').fadeIn().css( 'display', 'list-item' );
					$('#customize-control-loginpress_customization-login_footer_font_size').fadeIn().css( 'display', 'list-item' );
					$('#customize-control-loginpress_customization-login_footer_bg_color').fadeIn().css( 'display', 'list-item' );

				}

				// Control on footer back link text.
				if ('loginpress_customization[back_display_text]' == setting && false == loginPressVal ) {

					$( '#customize-preview iframe' ).contents().find( '.login #backtoblog' ).css( 'display', 'none' );
					$('#customize-control-loginpress_customization-login_back_text_decoration').fadeOut().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-login_back_color').fadeOut().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-login_back_color_hover').fadeOut().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-login_back_font_size').fadeOut().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-login_back_bg_color').fadeOut().css( 'display', 'none' );
						
				} else if ('loginpress_customization[back_display_text]' == setting && true == loginPressVal ) {

					loginpress_find('.login').append('<p id="backtoblog"></p>');
					$('#customize-preview iframe' ).contents().find( '.login #backtoblog' ).css( 'display', 'block' );
					$('#customize-control-loginpress_customization-login_back_text_decoration').fadeIn().css( 'display', 'list-item' );
					$('#customize-control-loginpress_customization-login_back_color').fadeIn().css( 'display', 'list-item' );
					$('#customize-control-loginpress_customization-login_back_color_hover').fadeIn().css( 'display', 'list-item' );
					$('#customize-control-loginpress_customization-login_back_font_size').fadeIn().css( 'display', 'list-item' );
					$('#customize-control-loginpress_customization-login_back_bg_color').fadeIn().css( 'display', 'list-item' );

				}

				// Control on Video Background.
				if ('loginpress_customization[loginpress_display_bg_video]' == setting && false == loginPressVal ) {

					$( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background' ).css( 'display', 'none' );
					if( $( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background' ).length > 0 && 'media' === $('#customize-control-loginpress_customization-bg_video_medium input[type="radio"]:checked').val() ) {
						// $( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background' )[0].pause();
					}

					$('#customize-control-loginpress_customization-bg_video_medium').fadeOut().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-yt_video_id').fadeOut().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-background_video').fadeOut().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-background_video_object').fadeOut().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-video_obj_position').fadeOut().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-background_video_muted').fadeOut().css( 'display', 'none' );
					$( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background-wrapper' ).css( 'display', 'none' );

				} else if ('loginpress_customization[loginpress_display_bg_video]' == setting && true == loginPressVal ) {

					$('#customize-control-loginpress_customization-bg_video_medium').fadeIn().css( 'display', 'block' );
					$( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background' ).css( 'display', 'block' );
					if( $( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background' ).length > 0 && 'media' === $('#customize-control-loginpress_customization-bg_video_medium input[type="radio"]:checked').val() ){
						// $( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background' )[0].play();
					} else {
						// $( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background' )
						var video = $('#customize-control-loginpress_customization-background_video video').html();
						
						if ( $('#customize-control-loginpress_customization-background_video video').length > 0 ) {
							loginpress_html_video_structure( video)
							// $( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background' )[0].play();
						}
					}

					if( $( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background' ).length > 0 && 'media' === $('#customize-control-loginpress_customization-bg_video_medium input[type="radio"]:checked').val() ){

						$('#customize-control-loginpress_customization-background_video_object').fadeIn().css( 'display', 'list-item' );
						$('#customize-control-loginpress_customization-video_obj_position').fadeIn().css( 'display', 'list-item' );
						$('#customize-control-loginpress_customization-background_video_muted').fadeIn().css( 'display', 'list-item' );
						// $( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background' )[0].play();

						var video_id = $('#customize-control-loginpress_customization-yt_video_id input[type="text"]').val();
						// $( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background' )
						
						if ( video_id.length > 0 ) {
							loginpress_html_video_structure( video_id );
							// $( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background' )[0].play();
						}

					} else if( $( '#customize-preview iframe' ).contents().find( '.login iframe#loginpress_video-background-wrapper' ).length > 0 && 'youtube' === $('#customize-control-loginpress_customization-bg_video_medium input[type="radio"]:checked').val() ) {
						$('#customize-control-loginpress_customization-background_video_object').fadeIn().css( 'display', 'none' );
						$('#customize-control-loginpress_customization-video_obj_position').fadeIn().css( 'display', 'none' );
						$('#customize-control-loginpress_customization-background_video_muted').fadeIn().css( 'display', 'none' );
						var video_id = $('#customize-control-loginpress_customization-yt_video_id input[type="text"]').val();
						// $( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background' )
						if ( video_id.length > 0 ) {
							loginpress_youtube_video_structure( video_id );
							// $( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background' )[0].play();
						}
					}

					if( 'youtube' === $('#customize-control-loginpress_customization-bg_video_medium input[type="radio"]:checked').val() ) {
						$('#customize-control-loginpress_customization-yt_video_id').css( 'display', 'list-item' );
						$('#customize-control-loginpress_customization-background_video_object').fadeIn().css( 'display', 'none' );
						$('#customize-control-loginpress_customization-video_obj_position').fadeIn().css( 'display', 'none' );
						$('#customize-control-loginpress_customization-background_video_muted').fadeIn().css( 'display', 'none' );
					} else {
						$('#customize-control-loginpress_customization-background_video').css( 'display', 'list-item' );
						$('#customize-control-loginpress_customization-background_video_object').fadeIn().css( 'display', 'list-item' );
						$('#customize-control-loginpress_customization-video_obj_position').fadeIn().css( 'display', 'list-item' );
						$('#customize-control-loginpress_customization-background_video_muted').fadeIn().css( 'display', 'list-item' );
					}

					// $('#customize-control-loginpress_customization-background_video').fadeIn().css( 'display', 'list-item' );
					if($('#customize-control-loginpress_customization-background_video video').length>0 && $('#customize-control-loginpress_customization-bg_video_medium input:checked').val() == 'media'){
						$('#customize-control-loginpress_customization-background_video_object').fadeIn().css( 'display', 'list-item' );
						$('#customize-control-loginpress_customization-video_obj_position').fadeIn().css( 'display', 'list-item' );
						$('#customize-control-loginpress_customization-background_video_muted').fadeIn().css( 'display', 'list-item' );
					} else {
						$('#customize-control-loginpress_customization-background_video_object').fadeOut().css( 'display', 'none' );
						$('#customize-control-loginpress_customization-video_obj_position').fadeOut().css( 'display', 'none' );
						$('#customize-control-loginpress_customization-background_video_muted').fadeOut().css( 'display', 'none' );
					}
				}
				
				// Control on video Media.
				if ('loginpress_customization[bg_video_medium]' == setting && 'media' == loginPressVal ) {
					$( '#customize-preview iframe' ).contents().find( '.login iframe#loginpress_video-background-wrapper' ).css( 'display', 'none' );
					$( '#customize-preview iframe' ).contents().find( '.login div#loginpress_video-background-wrapper' ).css( 'display', 'none' );

					// Handle Media video vs YT video ID controls.
					$('#customize-control-loginpress_customization-yt_video_id').fadeIn().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-background_video').fadeOut().css( 'display', 'list-item' );

					// Handle Rest of the video controls.
					if($('#customize-control-loginpress_customization-yt_video_id input[type="text"]').val().length > 0){
						$('#customize-control-loginpress_customization-background_video_object').fadeIn().css( 'display', 'none' );
						$('#customize-control-loginpress_customization-video_obj_position').fadeIn().css( 'display', 'none' );
						$('#customize-control-loginpress_customization-background_video_muted').fadeIn().css( 'display', 'none' );
					} else {
						$('#customize-control-loginpress_customization-background_video_object').fadeOut().css( 'display', 'none' );
						$('#customize-control-loginpress_customization-video_obj_position').fadeOut().css( 'display', 'none' );
						$('#customize-control-loginpress_customization-background_video_muted').fadeOut().css( 'display', 'none' );
					}

					// Handle video Live preview for Media type.
					$( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background' ).css( 'display', 'block' );
					if( $( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background' ).length > 0 ) {
						// $( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background' )[0].play();

						var video = $('#customize-control-loginpress_customization-background_video video').html();
						var muted_video = '';
						if ( $('[data-customize-setting-link="loginpress_customization[background_video_muted]"]').is(':checked') ) {
							muted_video = ' muted ';
						}
						
						if ( $('#customize-control-loginpress_customization-background_video video').length > 0 ) {
							loginpress_html_video_structure( video );
							// $( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background' )[0].play();
						} else {
							loginpress_html_video_structure( video );
						}
					}

				} else if ('loginpress_customization[bg_video_medium]' == setting && 'youtube' == loginPressVal ) {
					$( '#customize-preview iframe' ).contents().find( '.login iframe#loginpress_video-background-wrapper' ).css( 'display', 'block' );
					$( '#customize-preview iframe' ).contents().find( '.login div#loginpress_video-background-wrapper' ).css( 'display', 'none' );

					// Handle Media video vs YT video ID controls.
					$('#customize-control-loginpress_customization-yt_video_id').fadeIn().css( 'display', 'list-item' );
					$('#customize-control-loginpress_customization-background_video').fadeOut().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-background_video_object').fadeOut().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-video_obj_position').fadeOut().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-background_video_muted').fadeOut().css( 'display', 'none' );

					var video_id = $('#customize-control-loginpress_customization-yt_video_id input[type="text"]').val();
					$( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background' ).css( 'display', 'block' );
					if( video_id.length > 0 ) {
						// $( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background' )[0].play();
						loginpress_youtube_video_structure( video_id );
						// onYouTubeIframeAPIReady( video_id );
					}

					// Handle Rest of the video controls.
				}
				if($('#customize-control-loginpress_customization-background_video video').length>0  && $('#customize-control-loginpress_customization-bg_video_medium input:checked').val() == 'media'){
					$('#customize-control-loginpress_customization-background_video_object').fadeIn().css( 'display', 'list-item' );
					$('#customize-control-loginpress_customization-video_obj_position').fadeIn().css( 'display', 'list-item' );
					$('#customize-control-loginpress_customization-background_video_muted').fadeIn().css( 'display', 'list-item' );
				} else {
					$('#customize-control-loginpress_customization-background_video_object').fadeOut().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-video_obj_position').fadeOut().css( 'display', 'none' );
					$('#customize-control-loginpress_customization-background_video_muted').fadeOut().css( 'display', 'none' );
				}
			});
		});
	}

	wp.customize( 'loginpress_customization[bg_video_medium]', function(value) {
		value.bind( function(loginPressVal) {
			if( loginPressVal == 'youtube' ){
				if( $('.yt_video_not_found').length == 0 ) {
					// code to run if it isn't there
					$('#customize-control-loginpress_customization-yt_video_id').append( $( "<span class='yt_video_not_found'>"+loginpress_script.translated_strings['wrong_yt_id']+"</span>" ) );
					$('#customize-control-loginpress_customization-yt_video_id .customize-control-title').append( $( '<span class="loginpress_customizer_loader"><img src="'+ loginpress_script.preset_loader +'" /></span>' ) );
					$('.yt_video_not_found').hide();
					$('.loginpress_customizer_loader').hide();
				}
				$('#customize-control-loginpress_customization-background_video_object').fadeOut().css( 'display', 'none' );
				$('#customize-control-loginpress_customization-video_obj_position').fadeOut().css( 'display', 'none' );
				$('#customize-control-loginpress_customization-background_video_muted').fadeOut().css( 'display', 'none' );
			}
			// console.log( $('#customize-control-loginpress_customization-yt_video_id').length );
		});

	});

	wp.customize( 'loginpress_customization[yt_video_id]', function(value) {
		value.bind( function(loginPressVal) {

			if ( loginPressVal ) {
				$.ajax( {
					url     : loginpress_script.ajaxurl,
					type    : 'post',
					data    : {
						'src'      : loginPressVal,
						'action'   : 'loginpress_youtube_video_url',
						'security' : loginpress_script.attachment_nonce
					},
					beforeSend: function(){
						$(".loginpress_customizer_loader").show();
						$('.yt_video_not_found').hide();
						$("#_customize-input-loginpress_customization[yt_video_id]").attr("disabled", "disabled");
					},
					success : function( response ) {
						$("#_customize-input-loginpress_customization[yt_video_id]").removeAttr("disabled", "disabled");
						$(".loginpress_customizer_loader").hide();
						if( response !== '0' ) {
							$( '#customize-control-loginpress_customization-yt_video_id input' ).css( 'margin-top', '10px' );
							$('#customize-control-loginpress_customization-yt_video_id .customize-control-description').fadeIn();
							$( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background-wrapper' ).css( 'display', 'block' );
							loginpress_youtube_video_structure( loginPressVal );
							$('.yt_video_not_found').hide();
							// Channel_ID = response;
						} else if( response == '0' ) {
							$('#customize-control-loginpress_customization-yt_video_id .customize-control-description').fadeOut();
							$( '#customize-preview iframe' ).contents().find( '.login #loginpress_video-background-wrapper' ).css( 'display', 'none' );
							$('.yt_video_not_found').show();
						}
						$('#customize-control-loginpress_customization-background_video_object').fadeIn().css('display', 'none');
						$('#customize-control-loginpress_customization-video_obj_position').fadeIn().css('display', 'none');
						$('#customize-control-loginpress_customization-background_video_muted').fadeIn().css('display', 'none');
					}, // !success.
					error : function( xhr, textStatus, errorThrown ) {
						// console.log('Ajax Not Working');
					}
				} );  // ! $.ajax().
	
				$('#customize-control-loginpress_customization-background_video_object').fadeIn().css( 'display', 'none' );
				$('#customize-control-loginpress_customization-video_obj_position').fadeIn().css( 'display', 'none' );
				$('#customize-control-loginpress_customization-background_video_muted').fadeIn().css( 'display', 'none' );
	
			}
		});
	});

	function loginpress_youtube_video_structure( video_id ) {

		var muted_video = '';
		if ( $('[data-customize-setting-link="loginpress_customization[background_video_muted]"]').is(':checked') ) {
			muted_video = ' muted ';
		}
		$( '#customize-preview iframe' ).contents().find('.login').find('div#loginpress_video-background-wrapper').css('display', 'none');
		if($( '#customize-preview iframe' ).contents().find('.login').find('iframe#loginpress_video-background-wrapper[data-ytvideo]').length ==0){
			$( '#customize-preview iframe' ).contents().find('.login').find('div#loginpress_video-background-wrapper:not([data-ytvideo])').hide();
			if( $('#customize_presets_settingsdefault6').is(':checked') || $('#customize_presets_settingsdefault10').is(':checked') || $('#customize_presets_settingsdefault17').is(':checked')) {
				$( '#customize-preview iframe' ).contents().find('#login').append("<div id=\"loginpress_video-background-wrapper\" style='background-image: url(https://img.youtube.com/vi/"+video_id+"/maxresdefault.jpg);' data-ytvideo=" + video_id + "><script>var tv,playerDefaults = {autoplay: 0, autohide: 1, modestbranding: 0, rel: 0, showinfo: 0, controls: 0, disablekb: 1, enablejsapi: 0, iv_load_policy: 3};var vid = [{'videoId': '"+video_id+"', 'startSeconds': 0, 'endSeconds': 105, 'suggestedQuality': 'hd720'},],randomvid = Math.floor(Math.random() * (vid.length - 1 + 1));function onYouTubePlayerAPIReady(){tv = new YT.Player('loginpress_video-background-wrapper', {events: {'onReady': onPlayerReady, 'onStateChange': onPlayerStateChange}, playerVars: playerDefaults});}function onPlayerReady(){tv.loadVideoById(vid[0]);tv.mute();}function onPlayerStateChange(e) {if (e.data === 1){} else if (e.data === 0){tv.seekTo(vid[0].startSeconds)}}var tag = document.createElement('script');tag.src = 'https://www.youtube.com/player_api';var firstScriptTag = document.getElementsByTagName('script')[0];firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);</script></div>\n");
			} else if( $('#customize_presets_settingsdefault17').is(':checked') || $('#customize_presets_settingsdefault18').is(':checked') ) {
				if($( '#customize-preview iframe' ).contents().find('.login').find('#loginpress_video-background-wrapper').length==0){
					$( '#customize-preview iframe' ).contents().find('.login').append("<div id=\"loginpress_video-background-wrapper\" style='background-image: url(https://img.youtube.com/vi/"+video_id+"/maxresdefault.jpg);' data-ytvideo=" + video_id + "></div><script>var tv,playerDefaults = {autoplay: 0, autohide: 1, modestbranding: 0, rel: 0, showinfo: 0, controls: 0, disablekb: 1, enablejsapi: 0, iv_load_policy: 3};var vid = [{'videoId': '"+video_id+"', 'startSeconds': 0, 'endSeconds': 105, 'suggestedQuality': 'hd720'},],randomvid = Math.floor(Math.random() * (vid.length - 1 + 1));function onYouTubePlayerAPIReady(){tv = new YT.Player('loginpress_video-background-wrapper', {events: {'onReady': onPlayerReady, 'onStateChange': onPlayerStateChange}, playerVars: playerDefaults});}function onPlayerReady(){tv.loadVideoById(vid[randomvid]);tv.mute();}function onPlayerStateChange(e) {if (e.data === 1){tv.playVideo()} else if (e.data === 0){tv.seekTo(vid[randomvid].startSeconds)}}var tag = document.createElement('script');tag.src = 'https://www.youtube.com/player_api';var firstScriptTag = document.getElementsByTagName('script')[0];firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);</script>\n");
				}
			} else {
				$( '#customize-preview iframe' ).contents().find('.login').append("<div id=\"loginpress_video-background-wrapper\" style='background-image: url(https://img.youtube.com/vi/"+video_id+"/maxresdefault.jpg);' data-ytvideo=" + video_id + "></div><script>var tv,playerDefaults = {autoplay: 0, autohide: 1, modestbranding: 0, rel: 0, showinfo: 0, controls: 0, disablekb: 1, enablejsapi: 0, iv_load_policy: 3};var vid = [{'videoId': '"+video_id+"', 'startSeconds': 0, 'endSeconds': 105, 'suggestedQuality': 'hd720'},],randomvid = Math.floor(Math.random() * (vid.length - 1 + 1));function onYouTubePlayerAPIReady(){tv = new YT.Player('loginpress_video-background-wrapper', {events: {'onReady': onPlayerReady, 'onStateChange': onPlayerStateChange}, playerVars: playerDefaults});}function onPlayerReady(){tv.loadVideoById(vid[0]);tv.mute();}function onPlayerStateChange(e) {if (e.data === 1){tv.playVideo()} else if (e.data === 0){tv.seekTo(vid[0].startSeconds)}}var tag = document.createElement('script');tag.src = 'https://www.youtube.com/player_api';var firstScriptTag = document.getElementsByTagName('script')[0];firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);</script>\n");
			}
		}else{
			$( '#customize-preview iframe' ).contents().find('.login').find('#loginpress_video-background-wrapper').show();
			$( '#customize-preview iframe' ).contents().find('.login').append('<script>tv.loadVideoById("'+video_id+'")</script>')
		}
	}

	function loginpress_html_video_structure( video ) {
		$( '#customize-preview iframe' ).contents().find('.login').find('iframe#loginpress_video-background-wrapper').hide();
		$( '#customize-preview iframe' ).contents().find('.login').find('div#loginpress_video-background-wrapper').show();

		var muted_video = '';
		if ( $('[data-customize-setting-link="loginpress_customization[background_video_muted]"]').is(':checked') ) {
			muted_video = ' muted ';
		}
		if( $('#customize_presets_settingsdefault6').is(':checked') || $('#customize_presets_settingsdefault10').is(':checked') || $('#customize_presets_settingsdefault17').is(':checked')) {
			$( '#customize-preview iframe' ).contents().find('#login').append("<div id=\"loginpress_video-background-wrapper\"><video autoplay loop id=\"loginpress_video-background\"" + muted_video + " playsinline>\n" + video + "</video></div>\n");
		} else if( $('#customize_presets_settingsdefault17').is(':checked') || $('#customize_presets_settingsdefault18').is(':checked') ) {
			$( '#customize-preview iframe' ).contents().find('.login').append("<div id=\"loginpress_video-background-wrapper\"><video autoplay loop id=\"loginpress_video-background\"" + muted_video + " playsinline>\n" + video + "</video></div>\n");
		} else {
			$( '#customize-preview iframe' ).contents().find('.login').append("<div id=\"loginpress_video-background-wrapper\"><video autoplay loop id=\"loginpress_video-background\"" + muted_video + " playsinline>\n" + video + "</video></div>\n");
		}
	}

	// function for change LoginPress error and welcome messages in real time...
	function loginpress_footer_text_message( errorlog, target ) {
		wp.customize( errorlog, function(value) {
			value.bind( function(loginPressVal) {

				if ( loginPressVal == '' ) {
					loginpress_find(target).html('');
					if ( errorlog == 'loginpress_customization[login_footer_copy_right]' ) {
						loginpress_find(target).css( 'display', 'none' );
					}
				} else {
					loginpress_find(target).html(loginPressVal);
					if ( errorlog == 'loginpress_customization[login_footer_copy_right]' ) {
						loginpress_find(target).css( 'display', 'block' );
					}
				}
			} );
		} );
	}

	$(window).on('load',function(){

		if($('[data-customize-setting-link="loginpress_customization[setting_form_display_bg]"]').is(':checked')){
		  	loginpress_find('#login').addClass('login_transparent');
		}
	
	});

	// Update the login logo width in real time... // v1.2.2
	wp.customize( 'loginpress_customization[setting_form_display_bg]', function( value ) {
		value.bind( function( loginPressVal ) {
			if($('#customize-control-loginpress_customization-form_background_color .wp-color-picker').val().length>0){
				formbg = $('#customize-control-loginpress_customization-form_background_color .wp-color-picker').val();
			}
			if ( loginPressVal == true ) {
				// loginpress_find( '#login, #loginform' ).css( 'background-color', 'transparent' );
				// loginpress_find( '#login, #loginform' ).css( 'background-image', 'none' );
				loginpress_find('#login').addClass('login_transparent');
				$('#customize-control-loginpress_customization-form_background_color').fadeOut().hide();
				$('#customize-control-loginpress_customization-setting_form_background').fadeOut().hide();
			} else{
				// loginpress_find('#loginform').css('background-image', 'url('+formbgimg+')');
				//   loginpress_find( '#login, #loginform' ).css( 'background-color', formbg );
				loginpress_find('#login').removeClass('login_transparent');

				$('#customize-control-loginpress_customization-form_background_color').fadeIn().show();
				$('#customize-control-loginpress_customization-setting_form_background').fadeIn().show();
			}
		} );
	} );

	/**
	 * [loginpress_customizer_bg LoginPress Customizer Background Image Control that Retrive the Image URL w.r.t theme]
	 * @param  {[string]} customizer_bg [Preset Option]
	 * @return {[URL]} loginpress_bg   [Image URL]
	 * @version 1.4.0
	 */
	function loginpress_customizer_bg(customizer_bg) {

		if ( 'default1' == customizer_bg && loginpress_script.filter_bg.length > 0 ) {
			loginpress_bg = 'url(' + loginpress_script.filter_bg + ')';
		} else if ( 'default1' == customizer_bg ) {
			loginpress_bg = 'url(' + loginpress_script.plugin_url + '/loginpress/img/bg-default.jpg)';
		} else if ('minimalist' == customizer_bg) {
			loginpress_bg = 'url(' + loginpress_script.plugin_url + '/loginpress/img/minimalist.jpg)';
		} else if ( 'default2' == customizer_bg ) {
			loginpress_bg = 'url(' + loginpress_script.plugin_url + '/loginpress/img/bg2.jpg)';
		} else if ( 'default3' == customizer_bg ) {
			loginpress_bg = 'url(' + loginpress_script.plugin_url + '/loginpress-pro/assets/img/bg3.jpg)';
		} else if ( 'default4' == customizer_bg ) {
			loginpress_bg = 'url(' + loginpress_script.plugin_url + '/loginpress-pro/assets/img/bg4.jpg)';
		} else if ( 'default5' == customizer_bg ) {
			loginpress_bg = 'url(' + loginpress_script.plugin_url + '/loginpress-pro/assets/img/bg5.jpg)';
		} else if ( 'default6' == customizer_bg ) {
			loginpress_bg = 'url(' + loginpress_script.plugin_url + '/loginpress-pro/assets/img/bg6.jpg)';
		} else if ( 'default7' == customizer_bg ) {
			loginpress_bg = 'url(' + loginpress_script.plugin_url + '/loginpress-pro/assets/img/bg7.jpg)';
		} else if ( 'default8' == customizer_bg ) {
			loginpress_bg = 'url(' + loginpress_script.plugin_url + '/loginpress-pro/assets/img/bg8.jpg)';
		} else if ( 'default9' == customizer_bg ) {
			loginpress_bg = 'url(' + loginpress_script.plugin_url + '/loginpress-pro/assets/img/bg9.jpg)';
		} else if ( 'default10' == customizer_bg ) {
			loginpress_bg = 'url(' + loginpress_script.plugin_url + '/loginpress-pro/assets/img/bg10.jpg)';
		} else if ( 'default11' == customizer_bg ) {
			loginpress_bg = 'url(' + loginpress_script.plugin_url + '/loginpress-pro/assets/img/bg11.png)';
		} else if ( 'default12' == customizer_bg ) {
			loginpress_bg = 'url(' + loginpress_script.plugin_url + '/loginpress-pro/assets/img/bg12.jpg)';
		} else if ( 'default13' == customizer_bg ) {
			loginpress_bg = 'url(' + loginpress_script.plugin_url + '/loginpress-pro/assets/img/bg13.jpg)';
		} else if ( 'default14' == customizer_bg ) {
			loginpress_bg = 'url(' + loginpress_script.plugin_url + '/loginpress-pro/assets/img/bg14.jpg)';
		} else if ( 'default15' == customizer_bg ) {
			loginpress_bg = 'url(' + loginpress_script.plugin_url + '/loginpress-pro/assets/img/bg15.jpg)';
		} else if ( 'default16' == customizer_bg ) {
			loginpress_bg = 'url(' + loginpress_script.plugin_url + '/loginpress-pro/assets/img/bg16.jpg)';
		} else if ( 'default17' == customizer_bg ) {
			loginpress_bg = 'url(' + loginpress_script.plugin_url + '/loginpress-pro/assets/img/bg17.jpg)';
		} else if ( 'default18' == customizer_bg ) {
			loginpress_bg = 'url(' + loginpress_script.plugin_url + '/loginpress-pro/assets/img/bg18.jpg)';
		}
	}

	// Enable / Disable LoginPress Background.
	wp.customize( 'loginpress_customization[loginpress_display_bg]', function(value) {
		value.bind( function(loginPressVal) {

			// Check the theme id.
			customizer_bg = change_theme ? change_theme : loginpress_script.login_theme;

			// Set custom style on customizer.
			if ( loginpress_find().length == 0 ) {
				$("<style type='text/css' id='loginpress-customize'></style>").appendTo(loginpress_find('head'));
			}

			if ( loginPressVal == false ) { // Set conditions on behalf on themes.

				if ( 'default6' == customizer_bg ) {
					loginpress_find().html( "#login::after{background-image: none}" );
				} else if ( 'default8' == customizer_bg ) {
					loginpress_find().html( "body.login::after{background: none}" );
				} else if ( 'default10' == customizer_bg ) {
					loginpress_find().html( "#login::after{background-image: none}" );
				} else if ( 'default17' == customizer_bg ) {
					loginpress_find().html( "#login{background: none}" );
				} else {
					loginpress_find('body.login').css('background-image', 'none');
				}

				// Turn Off the Dependencies controls.
				// $('#customize-control-loginpress_customization-loginpress_display_bg').nextAll().hide();
				loginpress_manage_customizer_controls( ['gallery_background', 'setting_background', 'background_repeat_radio', 'background_position', 'background_image_size', 'mobile_background'], 'off' );

			} else {
				if ( localStorage.loginpress_bg ) {

					loginpress_bg_ = 'url(' + localStorage.loginpress_bg + ')';

					if ( 'default6' == customizer_bg ) {
						loginpress_find().html( "#login::after{background-image: " + loginpress_bg_ + "}" );
					} else if ( 'default8' == customizer_bg ) {
						loginpress_find().html( "body.login::after{background: " + loginpress_bg_ + " no-repeat 0 0; background-size: cover}" );
					} else if ( 'default10' == customizer_bg ) {
						loginpress_find().html( "#login::after{background-image: " + loginpress_bg_ + "}" );
					} else if ( 'default17' == customizer_bg ) {
						loginpress_find().html( "#login{background: " + loginpress_bg_ + " no-repeat 0 0;}" );
					} else {
						loginpress_find('body.login').css( 'background-image', loginpress_bg_ );
					}

				} else if ( loginpress_script.loginpress_bg_url == true ) {

					if ( 'default6' == customizer_bg ) {
						loginpress_find().html( "#login::after{background-image: " + loginpress_script.loginpress_bg_url + "}" );
					} else if ( 'default8' == customizer_bg ) {
						loginpress_find().html( "body.login::after{background: " + loginpress_script.loginpress_bg_url + " no-repeat 0 0; background-size: cover}" );
					} else if ( 'default10' == customizer_bg ) {
						loginpress_find().html( "#login::after{background-image: " + loginpress_script.loginpress_bg_url + "}" );
					} else if ( 'default17' == customizer_bg ) {
						loginpress_find().html( "#login{background: " + loginpress_script.loginpress_bg_url + " no-repeat 0 0;}" );
					} else {
						loginpress_find('body.login').css( 'background-image', 'url(' + loginpress_script.loginpress_bg_url + ')' );
					}

				} else {

					/**
					 * [loginpress_customizer_bg Retrive the Image URL w.r.t theme]
					 * @param  {[string]} customizer_bg [Preset Option]
					 * @return {[URL]} loginpress_bg   [Image URL]
					 */
					loginpress_customizer_bg(customizer_bg);
					if( $('#loginpress-gallery .image-select:checked').length > 0 && $('#loginpress-gallery .image-select:checked').parent('.loginpress_gallery_thumbnails').index() != 0 ) {
						loginpress_bg = $('#loginpress-gallery .image-select:checked').val();
						loginpress_bg = 'url(' + loginpress_bg + ')';
					}

					if ( 'default6' == customizer_bg ) {
						loginpress_find().html( "#login::after{background-image: " + loginpress_bg + "}" );
					} else if ( 'default8' == customizer_bg ) {
						loginpress_find().html( "body.login::after{background: " + loginpress_bg + " no-repeat 0 0; background-size: cover}" );
					} else if ( 'default10' == customizer_bg ) {
						loginpress_find().html( "#login::after{background-image: " + loginpress_bg + "}" );
					} else if ( 'default17' == customizer_bg ) {
						loginpress_find().html( "#login{background: " + loginpress_bg + " no-repeat 0 0;}" );
					} else {
						loginpress_find('body.login').css( 'background-image', loginpress_bg );
					}

					// Display Gallery Control.
					$('#customize-control-loginpress_customization-gallery_background').fadeIn().css( 'display', 'list-item' );
					if ( $('#customize-control-loginpress_customization-setting_background .attachment-media-view-image').length > 0  ) {
						$('#customize-control-loginpress_customization-gallery_background').css( 'display', 'none' );
					}
				}

				// Turn On the Dependencies controls.
				// $('#customize-control-loginpress_customization-loginpress_display_bg').nextAll().show();
				loginpress_manage_customizer_controls( ['gallery_background', 'setting_background', 'background_repeat_radio', 'background_position', 'background_image_size', 'mobile_background'], 'on' );
				if( $('#customize-control-loginpress_customization-setting_background .attachment-thumb').length > 0 ) {
					$('#customize-control-loginpress_customization-gallery_background').hide();
				}
			} // endif; conditions on behalf on themes.
		});
	});

	// Change LoginPress Custom Background that choosen by user.
	wp.customize( 'loginpress_customization[setting_background]', function(value) {
		value.bind( function(loginPressVal) {

			customizer_bg = change_theme ? change_theme : loginpress_script.login_theme;

			if ( loginpress_find().length == 0 ) {
				$("<style type='text/css' id='loginpress-customize'></style>").appendTo( loginpress_find('head') );
			}

			if ( loginPressVal == '' ) {

				if ( localStorage.loginpress_bg ) {
					localStorage.removeItem("loginpress_bg");
				}

				/**
				 * [loginpress_customizer_bg Retrive the Image URL w.r.t theme]
				 * @param  {[string]} customizer_bg [Preset Option]
				 * @return {[URL]} loginpress_bg   [Image URL]
				 */
				loginpress_customizer_bg(customizer_bg);
				if( $('#loginpress-gallery .image-select:checked').length > 0 && $('#loginpress-gallery .image-select:checked').parent('.loginpress_gallery_thumbnails').index() != 0 ) { // when remove custom background, set selected gallery bg
					loginpress_bg = $('#loginpress-gallery .image-select:checked').val();
					loginpress_bg = 'url('+loginpress_bg+')';
				}
				if ( 'default6' == customizer_bg ) {
					loginpress_find().html( "#login::after{background-image: " + loginpress_bg + "}" );
				} else if ( 'default8' == customizer_bg ) {
					loginpress_find().html( "body.login::after{background: " + loginpress_bg + " no-repeat 0 0; background-size: cover}" );
				} else if ( 'default10' == customizer_bg ) {
					loginpress_find().html( "#login::after{background-image: " + loginpress_bg + "}" );
				} else if ( 'default17' == customizer_bg ) {
					loginpress_find().html( "#login{background: " + loginpress_bg + " no-repeat 0 0;}" );
				} else {
					loginpress_find('body.login').css( 'background-image', loginpress_bg );
				}

				// Display the Gallery Control.
				$('#customize-control-loginpress_customization-gallery_background').fadeIn().css( 'display', 'list-item' );

			} else {

				// if (!localStorage.loginpress_bg) {
				localStorage.setItem("loginpress_bg", loginPressVal);
				// }

				if ( 'default6' == customizer_bg ) {
					loginpress_find().html( "#login::after{background-image: url(" + loginPressVal + ")}" );
				} else if ( 'default8' == customizer_bg ) {
					loginpress_find().html( "body.login::after{background: url(" + loginPressVal + ") no-repeat 0 0; background-size: cover}" );
				} else if ( 'default10' == customizer_bg ) {
					loginpress_find().html( "#login::after{background-image: url(" + loginPressVal + ")}" );
				} else if ( 'default17' == customizer_bg ) {
					loginpress_find().html( "#login{background: url(" + loginPressVal + ") no-repeat 0 0;}" );
				} else {
					loginpress_find('body.login').css( 'background-image', 'url(' + loginPressVal + ')' );
				}

				// Disable the Gallery Control.
				$('#customize-control-loginpress_customization-gallery_background').fadeOut().css( 'display', 'none' );
			}
		});
	});

	// Change LoginPress Background Image that choosen from Gallery.
	wp.customize( 'loginpress_customization[gallery_background]', function(value) {
		value.bind( function(loginPressVal) {

			// Check the theme id.
			customizer_bg = change_theme ? change_theme : loginpress_script.login_theme;

			// Set custom style on customizer.
			if ( loginpress_find().length == 0 ) {
				$("<style type='text/css' id='loginpress-customize'></style>").appendTo(loginpress_find('head'));
			}

			if ( loginpress_script.plugin_url + '/loginpress/img/gallery/img-1.jpg' == loginPressVal ) {

				/**
				 * [loginpress_customizer_bg Retrive the Image URL w.r.t theme]
				 * @param  {[string]} customizer_bg [Preset Option]
				 * @return {[URL]} loginpress_bg   [Image URL]
				 */
				loginpress_customizer_bg(customizer_bg);
				// console.log(loginpress_bg);
				if ( 'default6' == customizer_bg ) {
					loginpress_find().html( "#login::after{background-image: " + loginpress_bg + "}" );
				} else if ( 'default8' == customizer_bg ) {
					loginpress_find().html( "body.login::after{background: " + loginpress_bg + " no-repeat 0 0; background-size: cover}" );
				} else if ( 'default10' == customizer_bg ) {
					loginpress_find().html( "#login::after{background-image: " + loginpress_bg + "}" );
				} else if ( 'default17' == customizer_bg ) {
					loginpress_find().html( "#login{background: " + loginpress_bg + " no-repeat 0 0;}" );
				} else {
					loginpress_find('body.login').css( 'background-image', loginpress_bg );
				}

			} else {

				if ( 'default6' == customizer_bg ) {
					loginpress_find().html( "#login::after{background-image: url(" + loginPressVal + ")}" );
				} else if ( 'default8' == customizer_bg ) {
					loginpress_find().html( "body.login::after{background: url(" + loginPressVal + ") no-repeat 0 0; background-size: cover}" );
				} else if ( 'default10' == customizer_bg ) {
					loginpress_find().html( "#login::after{background-image: url(" + loginPressVal + ")}" );
				} else if ( 'default17' == customizer_bg ) {
					loginpress_find().html( "#login{background: url(" + loginPressVal + ") no-repeat 0 0;}" );
				} else {
					loginpress_find('body.login').css( 'background-image', 'url(' + loginPressVal + ')' );
				}

			}
		});
	});
	

	// loginpress_background_img( 'loginpress_customization[]', 'body.login' );
	$('.customize-controls-close').on('click', function() {
		// localStorage.removeItem("loginpress_bg_check");
		// localStorage.removeItem("loginpress_bg");
	});

	// localStorage.removeItem("loginpress_bg");
	// localStorage.removeItem("loginpress_bg_check");
	loginpress_display_control( 'loginpress_customization[footer_display_text]' );
	loginpress_display_control( 'loginpress_customization[back_display_text]' );
	loginpress_display_control( 'loginpress_customization[loginpress_display_bg_video]' );
	loginpress_display_control( 'loginpress_customization[bg_video_medium]' );

	// Update the WordPress login logo in real time...
	wp.customize( 'loginpress_customization[setting_logo]', function(value) {	
		value.bind( function(loginPressVal) {	
			if ( loginPressVal == '' ) {
				loginpress_find('#login h1 a').css( 'background-image', 'url(' + loginpress_script.admin_url + '/images/wordpress-logo.svg)' );
			} else {
				loginpress_find('#login h1 a').css( 'background-image', 'url(' + loginPressVal + ')' );
				loginpress_find('.login h1 a.bb-login-title:has(.bs-cs-login-title)').css('text-indent', '-10000px');
				if(wp_logo_height == 84){
					loginpress_find('.login h1 a.bb-login-title:has(.bs-cs-login-title)').css('height', wp_logo_height + 'px');
				}
			}
		});
	});

	// Enable / Disabe WordPress login logo in real time... since 1.1.3
	wp.customize( 'loginpress_customization[setting_logo_display]', function(value) {
		value.bind( function(loginPressVal) {
			if ( loginPressVal == true ) {
				loginpress_find('#login h1').fadeOut();
				$('#customize-control-loginpress_customization-setting_logo_display').nextAll().hide();
				$('#customize-control-loginpress_customization-customize_login_page_title').show();
				$('#customize-control-loginpress_customization-login_favicon').show();
				$('#customize-control-loginpress_customization-login_page_meta_group').show();

			} else {
				loginpress_find('#login h1').fadeIn();
				$('#customize-control-loginpress_customization-setting_logo_display').nextAll().show();
			}
		});
	});

	/**
	 * [loginpress_new_css_property Apply Live JS on WordPress Login Page Logo]
	 * @param  {[type]} loginpress_customization [Section ID]
	 * @param  {[type]} login                    [Targeted CSS]
	 * @param  {[type]} width                    [Property]
	 * @param  {[type]} px                       [Unit]
	 */
	loginpress_new_css_property( 'loginpress_customization[customize_logo_width]', '#login h1 a', 'width', 'px' );
	loginpress_new_css_property( 'loginpress_customization[customize_logo_height]', '#login h1 a', 'height', 'px' );
	loginpress_new_css_property( 'loginpress_customization[customize_logo_padding]', '#login h1 a', 'margin-bottom', 'px' );
	loginpress_new_css_property( 'loginpress_customization[custom_button_color]', '.wp-core-ui #login input[type=checkbox]', 'border-color', '' );
	loginpress_new_css_property( 'loginpress_customization[custom_button_color]', '.wp-core-ui #login .dashicons-visibility', 'color', '' );
	
	loginpress_attr_property( 'loginpress_customization[customize_logo_hover]', '#login h1 a', 'href' );
	loginpress_attr_property( 'loginpress_customization[customize_logo_hover_title]', '#login h1 a', 'title' );

	// Live Background color change.
	wp.customize( 'loginpress_customization[setting_background_color]', function(value) {
		value.bind( function(loginPressVal) {

			customizer_bg = change_theme ? change_theme : loginpress_script.login_theme;

			if ( loginpress_find('#loginpress-iframe-bgColor').length == 0 ) {
				$("<style type='text/css' id='loginpress-iframe-bgColor'></style>").appendTo(loginpress_find('head'));
			}

			if ( loginPressVal == '' ) {

				if ( 'default6' == customizer_bg || 'default10' == customizer_bg ) {
					loginpress_find('#loginpress-iframe-bgColor' ).html( "#login::after{background-color: #f1f1f1}" );
				} else if ( 'default17' == customizer_bg ) {
					loginpress_find('#login').css( "background-color" , "'#f1f1f1'" );
				} else if ( 'default8' == customizer_bg ) {
					loginpress_find('#loginpress-iframe-bgColor').html( "body.login::after{background-color: #f1f1f1}" );
				} else {
					loginpress_find('body.login').css( "background-color", "#f1f1f1" );
				}
			} else {

				if ( 'default6' == customizer_bg || 'default10' == customizer_bg ) {
					loginpress_find('#loginpress-iframe-bgColor').html( "#login::after{background-color: " + loginPressVal + "}" );
				} else if ( 'default17' == customizer_bg ) {
					loginpress_find('#login').css( "background-color" , loginPressVal );
				} else if ( 'default8' == customizer_bg ) {
					loginpress_find('#loginpress-iframe-bgColor').html( "body.login::after{background-color: " + loginPressVal + "}" );
				} else {
					loginpress_find('body.login').css( "background-color", loginPressVal );
				}
			}
		});
	});


	// Live Background Repeat change.
	wp.customize( 'loginpress_customization[background_repeat_radio]', function(value) {
		value.bind(function(loginPressVal) {

			customizer_bg = change_theme ? change_theme : loginpress_script.login_theme;

				if ( loginpress_find('#loginpress-scbg-repeat').length == 0 ) {
					$("<style type='text/css' id='loginpress-scbg-repeat'></style>").appendTo(loginpress_find('head'));
				}

			if ( loginPressVal != '' ) {

				if ( 'default6' == customizer_bg || 'default10' == customizer_bg ) {
					loginpress_find('#loginpress-scbg-repeat').html( "#login::after{background-repeat: " + loginPressVal + "}" );
				} else if ( 'default17' == customizer_bg ) {
					loginpress_find('#login').css( "background-repeat" , loginPressVal );
				} else if ( 'default8' == customizer_bg ) {
					loginpress_find('#loginpress-scbg-repeat').html( "body.login::after{background-repeat: " + loginPressVal + "}" );
				} else {
					loginpress_find('body.login').css( "background-repeat", loginPressVal );
				}

			}
		});
	});

	// Live Background Image Size Change.
	wp.customize( 'loginpress_customization[background_image_size]', function(value) {
		value.bind( function(loginPressVal) {

			customizer_bg = change_theme ? change_theme : loginpress_script.login_theme;

				if ( loginpress_find('#loginpress-scbg-size').length == 0 ) {
					$("<style type='text/css' id='loginpress-scbg-size'></style>").appendTo(loginpress_find('head'));
				}

			if ( loginPressVal != '' ) {

				if ( 'default6' == customizer_bg || 'default10' == customizer_bg ) {
					loginpress_find('#loginpress-scbg-size').html( "#login::after{background-size: " + loginPressVal + "}" );
				} else if ( 'default17' == customizer_bg ) {
					loginpress_find('#login').css( "background-size" , loginPressVal );
				} else if ( 'default8' == customizer_bg ) {
					loginpress_find('#loginpress-scbg-size').html( "body.login::after{background-size: " + loginPressVal + "}" );
				} else {
					loginpress_find('body.login').css( "background-size", loginPressVal );
				}

			}
		});
	});

	// Live Background Position Change.
	// @version 1.1.21
	wp.customize( 'loginpress_customization[background_position]', function(value) {
		value.bind( function(loginPressVal) {

			customizer_bg = change_theme ? change_theme : loginpress_script.login_theme;
			loginPressVal = loginPressVal.replace( "-", " " );

				if ( loginpress_find('#loginpress-scbg-position').length == 0 ) {
					$("<style type='text/css' id='loginpress-scbg-position'></style>").appendTo(loginpress_find('head'));
				}

			if ( loginPressVal != '' ) {

				if ( 'default6' == customizer_bg || 'default10' == customizer_bg ) {
					loginpress_find('#loginpress-scbg-position').html( "#login::after{background-position: " + loginPressVal + "}" );
				} else if ( 'default17' == customizer_bg ) {
					loginpress_find('#login').css( "background-position" , loginPressVal );
				} else if ( 'default8' == customizer_bg ) {
					loginpress_find('#loginpress-scbg-position').html( "body.login::after{background-position: " + loginPressVal + "}" );
				} else {
					loginpress_find('body.login').css( "background-position", loginPressVal );
				}

			}
		});
	});

	// Live Templates Change. 1.1.16
	// wp.customize( 'customize_presets_settings', function(value) {
	//   value.bind( function(loginPressVal) {
	//
	//     customizer_bg = change_theme ? change_theme : loginpress_script.login_theme;
	//
	//       var preset_nonce = loginpress_script.preset_nonce;
	//
	//       $.ajax({
	//
	//         url : ajaxurl,
	//         type: 'POST',
	//         data: {
	//           action   : 'loginpress_presets',
	//           security : preset_nonce
	//         },
	//         beforeSend: function() {
	//           loginpress_find('.login').append('<div class="loginpres-previewer-loader" style="position: fixed;top: 0;left: 0; height: 100%; width: 100%; background: rgba(255,255, 255, .5) url(' + loginpress_script.preset_loader + ') no-repeat center center; z-index: 9999999;"></div>');
	//         },
	//         success: function(response) {
	//
	//           loginpress_find('#loginpress-style').remove();
	//           loginpress_find('head').append(response);
	//           loginpress_find('.loginpres-previewer-loader').remove();
	//
	//           // setTimeout(function() {
	//           //   $(".log-file-text").fadeOut()
	//           // }, 3000);
	//         }
	//       });
	//   });
	// });


	loginpress_background_img( 'loginpress_customization[setting_form_background]', '#loginform');

	loginpress_new_css_property( 'loginpress_customization[customize_form_width]', '#login', 'max-width', 'px' );
	loginpress_new_css_property( 'loginpress_customization[customize_form_height]', '#loginform', 'min-height', 'px' );
	loginpress_css_property( 'loginpress_customization[customize_form_padding]', '#loginform', 'padding' );
	loginpress_css_property( 'loginpress_customization[customize_form_border]', '#loginform', 'border' );
  
	loginpress_new_input_property( 'loginpress_customization[textfield_width]', 'width', '%' );
	loginpress_input_property( 'loginpress_customization[textfield_margin]', 'margin' );
	loginpress_input_property( 'loginpress_customization[textfield_background_color]', 'background' );
	loginpress_input_property( 'loginpress_customization[textfield_color]', 'color' );
  
	loginpress_css_property( 'loginpress_customization[form_background_color]', '#loginform, #login', 'background-color', '#FFF' );
	loginpress_css_property( 'loginpress_customization[textfield_label_color]', '.login label[for="user_login"], .login label[for="user_pass"]', 'color', '#777' );
	loginpress_css_property( 'loginpress_customization[remember_me_label_size]', '.login label[for="rememberme"]', 'color', '#777' );
  
	loginpress_css_property( 'loginpress_customization[background_video_object]', 'body.login #loginpress_video-background', 'object-fit', 'contain' );
	loginpress_css_property( 'loginpress_customization[video_obj_position]', 'body.login #loginpress_video-background', 'object-position', '50% 50%' );
  
	loginpress_new_css_property( 'loginpress_customization[textfield_radius]', '#loginform input[type="text"], #loginform input[type="password"], #registerform input[type="text"], #registerform input[type="password"], #registerform input[type="number"], #registerform input[type="email"], #lostpasswordform input[type="text"]', 'border-radius', 'px' );
  
	loginpress_shadow_property( 'loginpress_customization[textfield_shadow]', '#loginform input[type="text"], #loginform input[type="password"], #registerform input[type="text"], #registerform input[type="password"], #registerform input[type="number"], #registerform input[type="email"], #lostpasswordform input[type="text"]', 'box-shadow', 'px' );
	loginpress_shadow_opacity_property( 'loginpress_customization[textfield_shadow_opacity]', '#loginform input[type="text"], #loginform input[type="password"], #registerform input[type="text"], #registerform input[type="password"], #registerform input[type="number"], #registerform input[type="email"], #lostpasswordform input[type="text"]', 'box-shadow', 'px' );
  
	loginpress_background_img( 'loginpress_customization[forget_form_background]', '.login-action-lostpassword #login' );
	loginpress_css_property( 'loginpress_customization[forget_form_background_color]', '.login-action-lostpassword #login', 'background-color' );
  
	loginpress_new_css_property( 'loginpress_customization[customize_form_radius]', '#login', 'border-radius', 'px' );
	loginpress_shadow_property( 'loginpress_customization[customize_form_shadow]', '#login', 'box-shadow', 'px' );
	loginpress_shadow_opacity_property( 'loginpress_customization[customize_form_opacity]', '#login', 'box-shadow', 'px' );

	//Buttons starts.
	// Update the login form button background in real time...
	var loginPressBtnClr;
	var loginPressBtnHvr;
	wp.customize( 'loginpress_customization[custom_button_color]', function(value) {
		value.bind( function(loginPressVal) {
			if ( loginPressVal == '' ) {

				loginPressBtnClr = undefined;
				loginpress_find('.wp-core-ui #login  .button-primary').css( 'background', '' );
				loginpress_find('.wp-core-ui #login  .button-primary').on( 'mouseover', function() {
					if ( typeof loginPressBtnHvr !== "undefined" || loginPressBtnHvr === null ) {
						$(this).css( 'background', loginPressBtnHvr );
					} else {
						$(this).css( 'background', '' );
					}
				}).on( 'mouseleave', function() {
					$(this).css( 'background', '' );
				});

			} else {

				loginpress_find('.wp-core-ui #login .button-primary').css( 'background', loginPressVal );
				loginPressBtnClr = loginPressVal;

				loginpress_find('.wp-core-ui #login  .button-primary').on( 'mouseover', function() {
					if ( typeof loginPressBtnHvr !== "undefined" || loginPressBtnHvr === null ) {
						$(this).css( 'background', loginPressBtnHvr );
					} else {
						$(this).css( 'background', '' );
					}
				}).on( 'mouseleave', function() {
					$(this).css( 'background', loginPressVal );
				});
			}
		});
	});

	var loginPressBtnBrdrClr;
	// Update the login form button border-color in real time...
	wp.customize( 'loginpress_customization[button_border_color]', function(value) {
		value.bind( function(loginPressVal) {
			if ( loginPressVal == '' ) {
				loginpress_find('.wp-core-ui #login  .button-primary').css( 'border-color', '' );
			} else {
				loginpress_find('.wp-core-ui #login  .button-primary').css( 'border-color', loginPressVal );
				loginPressBtnBrdrClr = loginPressVal;
			}
		});
	});

	// Update the login form button border-color in real time...
	wp.customize( 'loginpress_customization[button_hover_color]', function(value) {
		value.bind( function(loginPressVal) {
			if ( loginPressVal == '' ) {
				loginPressBtnHvr = undefined;
				// loginpress_find('.wp-core-ui #login  .button-primary').css( 'background', '' );
				loginpress_find('.wp-core-ui #login  .button-primary').on( 'mouseover', function() {
					$(this).css( 'background', '' );
				}).on( 'mouseleave', function() {
					if ( typeof loginPressBtnClr !== "undefined" || loginPressBtnClr === null ) {
						$(this).css( 'background', loginPressBtnClr );
					} else {
						$(this).css( 'background', '' );
					}
				});
			} else {
				loginPressBtnHvr = loginPressVal;
				loginpress_find('.wp-core-ui #login  .button-primary').on( 'mouseover', function() {
					$(this).css( 'background', loginPressVal );
				}).on( 'mouseleave', function() {
					if ( typeof loginPressBtnClr !== "undefined" || loginPressBtnClr === null ) {
						$(this).css( 'background', loginPressBtnClr );
					} else {
						$(this).css( 'background', '' );
					}
				});
			}
		});
	});

	// Update the login form button border-color in real time...
	wp.customize( 'loginpress_customization[button_hover_border]', function(value) {
		value.bind( function(loginPressVal) {
			if ( loginPressVal == '' ) {
				loginpress_find('.wp-core-ui #login  .button-primary').css( 'border-color', '' );
			} else {
				loginpress_find('.wp-core-ui #login  .button-primary').on( 'mouseover', function() {
					$(this).css( 'border-color', loginPressVal );
				}).on( 'mouseleave', function() {
					if ( typeof loginPressBtnBrdrClr !== "undefined" || loginPressBtnBrdrClr === null ) {
						$(this).css( 'border-color', loginPressBtnBrdrClr );
					} else {
						$(this).css( 'border-color', '' );
					}
				});
			}
		});
	});

	var loginPressBtnTxtClr;
	var loginPressBtnTxtHvr;
	// Update the login form button text color on hover in real time...
	wp.customize( 'loginpress_customization[button_hover_text_color]', function(value) {
		value.bind( function(loginPressVal) {
			if ( loginPressVal == '' ) {
				loginPressBtnTxtHvr = undefined;
				loginpress_find('.wp-core-ui #login  .button-primary').on( 'mouseover', function() {
					$(this).css( 'color', loginPressVal );
				}).on( 'mouseleave', function() {
					if ( typeof loginPressBtnTxtClr !== "undefined" || loginPressBtnTxtClr === null ) {
						$(this).css( 'color', loginPressBtnTxtClr );
					} else {
						$(this).css( 'color', '' );
					}
				});
			} else {
				loginPressBtnTxtHvr = loginPressVal;
				loginpress_find('.wp-core-ui #login  .button-primary').on( 'mouseover', function() {
					$(this).css( 'color', loginPressVal );
				}).on( 'mouseleave', function() {
					if ( typeof loginPressBtnTxtClr !== "undefined" || loginPressBtnTxtClr === null ) {
						$(this).css( 'color', loginPressBtnTxtClr );
					} else {
						$(this).css( 'color', '' );
					}
				});
			}
		});
	});

	// Update the login form button border-color in real time...
	wp.customize( 'loginpress_customization[custom_button_shadow]', function(value) {
		value.bind( function(loginPressVal) {
			if ( loginPressVal == '' ) {
				loginpress_find('.wp-core-ui #login .button-primary').css( 'box-shadow', '' );
			} else {
				loginpress_find('.wp-core-ui #login .button-primary').css( 'box-shadow', loginPressVal );
			}
		});
	});

	// Update the login form button text color in real time...
	wp.customize( 'loginpress_customization[button_text_color]', function(value) {
		value.bind( function(loginPressVal) {
			if ( loginPressVal == '' ) {
				loginPressBtnTxtClr = undefined;
				loginpress_find('.wp-core-ui #login .button-primary').css( 'color', '' );
			} else {
				loginpress_find('.wp-core-ui #login .button-primary').css( 'color', loginPressVal );

				loginpress_find('.wp-core-ui #login  .button-primary').on( 'mouseover', function() {
					if ( typeof loginPressBtnTxtHvr !== "undefined" || loginPressBtnTxtHvr === null ) {
						$(this).css( 'color', loginPressBtnTxtHvr );
					} else {
						$(this).css( 'color', '' );
					}
				}).on( 'mouseleave', function() {
					$(this).css( 'color', loginPressVal );
				});
				loginPressBtnTxtClr = loginPressVal;
			}
		});
	});

	/**
	 * WordPress Login Form Label Change.
	 * @since 1.1.3
	 */
	loginpress_change_form_label( 'loginpress_customization[form_username_label]', '.login label[for="user_login"] span' );
	loginpress_change_form_label( 'loginpress_customization[form_password_label]', '.login label[for="user_pass"] span' );

	/**
	 * WordPress Login Page Footer Message.
	 */
	// loginpress_css_property( 'loginpress_customization[footer_display_text]', '.login #nav', 'display' );
	loginpress_css_property('loginpress_customization[login_footer_text_decoration]', '.login #nav a', 'text-decoration');

	/**
	 * Update the footer text of the lost password.
	 *
	 * @since 3.0.0
	 */
	wp.customize('loginpress_customization[login_footer_text]', function (value) {
		value.bind(function (loginPressVal) {
			if (loginpress_find('.wp-login-lost-password').length == 0) {
				loginpress_change_form_label('loginpress_customization[login_footer_text]', '.login #nav a:last-child');
			} else {
				loginpress_change_form_label('loginpress_customization[login_footer_text]', '.wp-login-lost-password');
			}
		});

	});

	var loginPressFtrClr;
	var loginPressFtrHvr;
	// Update the login form button border-color in real time...
	wp.customize( 'loginpress_customization[login_footer_color]', function(value) {
		value.bind( function(loginPressVal) {

			if ( loginPressVal == '' ) {
				loginPressFtrHvr = '';
				loginPressFtrClr = '';
				loginpress_find('.login #nav a, .login #nav').css( 'color', '' );
				loginpress_find('.login #nav a, .login #nav').on( 'mouseover', function() {
					if ( typeof loginPressFtrHvr !== "undefined" || loginPressFtrHvr === null ) {
						$(this).css( 'color', loginPressFtrHvr );
					} else {
						$(this).css( 'color', '' );
					}
				}).on( 'mouseleave', function() {
					$(this).css( 'color', '' );
				});
			} else {
				loginPressFtrClr = loginPressVal;
				loginpress_find('.login #nav a, .login #nav').css( 'color', loginPressVal );
				loginpress_find('.login #nav a, .login #nav').on( 'mouseover', function() {
					if ( typeof loginPressFtrHvr !== "undefined" || loginPressFtrHvr === null ) {
						$(this).css( 'color', loginPressFtrHvr );
					} else {
						$(this).css( 'color', '' );
					}
				}).on( 'mouseleave', function() {
					$(this).css( 'color', loginPressVal );
				});
			}
		});
	});

	// Update the login form button border-color in real time...
	wp.customize( 'loginpress_customization[login_footer_color_hover]', function(value) {
		value.bind( function(loginPressVal) {

			if ( loginPressVal == '' ) {
						loginPressFtrClr = '';
				loginpress_find('.login #nav a').css( 'color', '' );
				loginpress_find('.login #nav a').on( 'mouseover', function() {
					$(this).css( 'color', '' );
				}).on( 'mouseleave', function() {
					if ( typeof loginPressFtrClr !== "undefined" || loginPressFtrClr === null ) {
						$(this).css( 'color', loginPressFtrClr );
					} else {
						$(this).css( 'color', '' );
					}
				});
			} else {
				loginPressFtrHvr = loginPressVal;
				loginpress_find('.login #nav a').on( 'mouseover', function() {
					$(this).css('color', loginPressVal);
				}).on('mouseleave', function() {
					if ( typeof loginPressFtrClr !== "undefined" || loginPressFtrClr === null ) {
						$(this).css( 'color', loginPressFtrClr );
					} else {
						$(this).css( 'color', '' );
					}
				});
			}
		});
	});

	loginpress_new_css_property( 'loginpress_customization[login_footer_font_size]', '.login #nav a', 'font-size', 'px' );
	loginpress_new_css_property( 'loginpress_customization[customize_form_label]', '.login label[for="user_login"], .login label[for="user_pass"]', 'font-size', 'px' );
	loginpress_new_css_property( 'loginpress_customization[remember_me_font_size]', '.login form .forgetmenot label', 'font-size', 'px' );
	loginpress_css_property( 'loginpress_customization[login_footer_bg_color]', '.login #nav', 'background-color', 'transparent' );
	loginpress_css_property( 'loginpress_customization[back_display_text]', '.login #backtoblog', 'display' );
	loginpress_css_property( 'loginpress_customization[login_back_text_decoration]', '.login #backtoblog a', 'text-decoration' );
  
	var loginPressFtrBackClr;
	var loginPressFtrBackHvr;
	/**
	 * Change LoginPress 'Back to Blog(link)' color live.
	 */
	wp.customize( 'loginpress_customization[login_back_color]', function( value ) {
		value.bind(function( loginPressVal ) {

			if ( loginPressVal == '' ) {
						loginPressFtrBackClr = '';
				loginpress_find('.login #backtoblog a').css( 'color', '' );
				loginpress_find('.login #backtoblog a').on( 'mouseover', function() {
					if ( typeof loginPressFtrBackHvr !== "undefined" || loginPressFtrBackHvr === null ) {
						$(this).css( 'color', loginPressFtrBackHvr );
					} else {
						$(this).css( 'color', '' );
					}
				} ).on( 'mouseleave', function() {
					$(this).css( 'color', '' );
				} );
			} else {
				loginPressFtrBackClr = loginPressVal;
				loginpress_find('.login #backtoblog a').css( 'color', loginPressVal );
				loginpress_find('.login #backtoblog a').on( 'mouseover', function() {
					if ( typeof loginPressFtrBackHvr !== "undefined" || loginPressFtrBackHvr === null ) {
						$(this).css( 'color', loginPressFtrBackHvr );
					} else {
						$(this).css( 'color', '' );
					}
				} ).on( 'mouseleave', function() {
					$(this).css( 'color', loginPressVal );
				});
			}
		});
	});

	/**
	 * Change LoginPress 'Button' CSS. Since 1.1.3
	 */
	loginpress_new_css_property('loginpress_customization[login_button_size]', '.login form p input[type="submit"]', 'width', '%');
	loginpress_new_css_property('loginpress_customization[login_button_top]', '.wp-core-ui .button-group.button-large .button, .wp-core-ui .button.button-large', 'padding-top', 'px');
	loginpress_new_css_property('loginpress_customization[login_button_bottom]', '.wp-core-ui .button-group.button-large .button, .wp-core-ui .button.button-large', 'padding-bottom', 'px');
	loginpress_new_css_property('loginpress_customization[login_button_radius]', '.login form p input[type="submit"]', 'border-radius', 'px');
	loginpress_shadow_property('loginpress_customization[login_button_shadow]', '.login form p input[type="submit"]', 'box-shadow', 'px');
	loginpress_shadow_opacity_property('loginpress_customization[login_button_shadow_opacity]', '.login form p input[type="submit"]', 'box-shadow', 'px');
	loginpress_css_property_imp('loginpress_customization[login_button_text_size]', '.login form p input[type="submit"]', 'font-size', 'px');

	/**
	 * function for change LoginPress CSS in real time with !important...
	 * @param  string setting  [Name of the setting]
	 * @param  string target   [Targeted CSS class/ID]
	 * @param  string property [CSS property]
	 * @param  string suffix   [unit value]
	 *
	 * @return string          [CSS property in real time]
	 * @since 1.4.6
	 */
	function loginpress_css_property_imp( setting, target, property, suffix ) {
		// Update the login logo width in real time...
		wp.customize( setting, function( value ) {
			value.bind( function( loginPressVal ) {

				if ( loginPressVal == '' ) {
					loginpress_find( target ).css( property, '' );
				} else {
					loginpress_find( target )[0].style.setProperty(property , loginPressVal + suffix , 'important' );
				}
			} );
		} );
	}

	/**
	 * Change LoginPress 'Back to Blog(link)' hover color live.
	 */
	wp.customize( 'loginpress_customization[login_back_color_hover]', function( value ) {
		value.bind( function( loginPressVal ) {

			if ( loginPressVal == '' ) {

				loginPressFtrBackHvr = '';
				loginpress_find('.login #backtoblog a').css( 'color', '' );
				loginpress_find('.login #backtoblog a').on( 'mouseover', function() {
					$(this).css( 'color', '' );
				} ).on( 'mouseleave', function() {
					if ( typeof loginPressFtrBackClr !== "undefined" || loginPressFtrBackClr === null ) {
						$(this).css( 'color', loginPressFtrBackClr );
					} else {
						$(this).css( 'color', '' );
					}
				});

			} else {

				loginPressFtrBackHvr = loginPressVal;
				loginpress_find('.login #backtoblog a').on( 'mouseover', function() {
					$(this).css( 'color', loginPressVal );
				} ).on( 'mouseleave', function() {
					if ( typeof loginPressFtrBackClr !== "undefined" || loginPressFtrBackClr === null ) {
						$(this).css( 'color', loginPressFtrBackClr );
					} else {
						$(this).css( 'color', '' );
					}
				});
				
			}
		});
	});

	/**
	 * WordPress Login Page Footer Style.
	 */
	loginpress_new_css_property( 'loginpress_customization[login_back_font_size]', '.login #backtoblog a', 'font-size', 'px' );
	loginpress_css_property( 'loginpress_customization[login_back_font_size]', '.login #backtoblog a', 'font-size' );
	loginpress_css_property( 'loginpress_customization[login_back_bg_color]', '.login #backtoblog', 'background-color', 'transparent' );
	// loginpress_css_property( 'loginpress_customization[show_some_love_text_color]', '.loginpress-show-love, .loginpress-show-love a', 'color', 'transparent' );
	// loginpress_css_property( 'loginpress_customization[copyright_text_color]', '.footer-cont .copyRight', 'color', '' );
	// loginpress_css_property( 'loginpress_customization[copyright_background_color]', '.footer-cont .copyRight', 'background-color', 'transparent' );
	loginpress_footer_text_message( 'loginpress_customization[login_footer_copy_right]', '.copyRight' );
	
	/**
	* WordPress Login Page Error Messages.
	*/
	loginpress_text_message( 'loginpress_customization[incorrect_username]', '#login_error' );
	loginpress_text_message( 'loginpress_customization[incorrect_password]', '#login_error' );
	loginpress_text_message( 'loginpress_customization[empty_username]', '#login_error' );
	loginpress_text_message( 'loginpress_customization[empty_password]', '#login_error' );
	loginpress_text_message( 'loginpress_customization[invalid_email]', '#login_error' );
	loginpress_text_message( 'loginpress_customization[empty_email]', '#login_error' );
	loginpress_text_message( 'loginpress_customization[invalidcombo_message]', '#login_error' );

	/**
	 * WordPress Login Page Welcome Messages.
	 */
	loginpress_text_message( 'loginpress_customization[lostpwd_welcome_message]', '.login-action-lostpassword .custom-message' );
	loginpress_text_message( 'loginpress_customization[welcome_message]', '.login-action-login .custom-message' );
	loginpress_text_message( 'loginpress_customization[register_welcome_message]', '.login-action-register .custom-message' );
	loginpress_text_message( 'loginpress_customization[logout_message]', '.login .message, .login .success, .login .custom-message' );
	
	/**
	* WordPress Login Page Welcome Messages Style.
	*/
	loginpress_css_property( 'loginpress_customization[message_background_border]', '.login .message, .login .success, .login .custom-message', 'border' );
	loginpress_css_property( 'loginpress_customization[message_background_color]', '.login .message, .login .success, .login .custom-message', 'background-color' );

	/**
	 * Enable / Disable LoginPress Footer link.
	 */
	wp.customize( 'loginpress_customization[loginpress_show_love]', function( value ) {
		value.bind( function( loginPressVal ) {

			if ( loginPressVal == false ) {

				loginpress_find('.loginpress-show-love').fadeOut().hide();
				$('#customize-control-loginpress_customization-loginpress_show_love').nextAll().hide();
				
			} else {

				if(loginpress_find('.loginpress-show-love').length===0){
					$('<div class="loginpress-show-love">Powered by: <a href="https://wpbrigade.com" target="_blank">LoginPress</a></div>').insertBefore($('#customize-preview iframe').contents().find('.footer-wrapper'));
				}

				loginpress_find('.loginpress-show-love').fadeIn().show();
				$('#customize-control-loginpress_customization-loginpress_show_love').nextAll().show();
			}
		} );
	} );

	/**
	 * Set position of Footer link.
	 */
	wp.customize( 'loginpress_customization[show_love_position]', function( value ) {
		value.bind( function( loginPressVal ) {
			if ( loginPressVal == 'left' ) {
				loginpress_find('.loginpress-show-love').addClass('love-position');
			} else {
				loginpress_find('.loginpress-show-love').removeClass('love-position');
			}
		} );
	} );
	
	var footerBgClr;

	// Update the form footer background color...
		
	wp.customize( 'loginpress_customization[copyright_background_color]', function(value) {
		value.bind( function(loginPressVal) {
			if ( loginPressVal == '' ) {
				loginpress_find('.footer-cont .copyRight').css( 'background-color', 'transparent' );
				footerBgClr = 'transparent';
			} else {
				footerBgClr = loginPressVal;
				loginpress_find('.footer-cont .copyRight').css( 'background-color', footerBgClr );
			}
		});
	});

	var footerTextClr;
	wp.customize( 'loginpress_customization[copyright_text_color]', function(value) {
		value.bind( function(loginPressVal) {
			if ( loginPressVal == '' ) {
				loginpress_find('.footer-cont .copyRight').css( 'color', '' );
				footerTextClr = loginPressVal;
			} else {
				footerTextClr = loginPressVal;
				loginpress_find('.footer-cont .copyRight').css( 'color', footerTextClr );
			}
		});
	});

	wp.customize('loginpress_customization[login_footer_copy_right]', function(value) {
		value.bind(function(loginPressVal) {
			if (loginPressVal == '') {

			} else {
				var date = new Date().getFullYear();
				var s = $('[id="_customize-input-loginpress_customization[login_footer_copy_right]"]').val();
				const result = s.replace( '$YEAR$', date );
				loginpress_footer_date_change('loginpress_customization[login_footer_copy_right]', '.copyRight', result);
			}
		});
	});

	/**
	 * Set position of Footer link.
	 */
	wp.customize( 'loginpress_customization[login_copy_right_display]', function( value ) {
		value.bind( function( loginPressVal ) {
			if ( loginPressVal == true ) {

				if( loginpress_find('.copyRight').length == 0 ){
					var s = $('[id="_customize-input-loginpress_customization[login_footer_copy_right]"]').val();
					var date = new Date().getFullYear();
					const result = s.replace('$YEAR$', date);
					loginpress_find('.footer-cont').append('<div class="copyRight">'+$('[id="_customize-input-loginpress_customization[login_footer_copy_right]"]').val()+'</div>');
					loginpress_find('.footer-cont .copyRight').html(result);
				}

				$('#customize-control-loginpress_customization-login_footer_copy_right').show();
				$('#customize-control-loginpress_customization-copyright_background_color').show();
				$('#customize-control-loginpress_customization-copyright_text_color').show();
				loginpress_find('.copyRight').css( 'background-color', footerBgClr );
				loginpress_find('.copyRight').css( 'color', footerTextClr );
				loginpress_find('.footer-wrapper').show();
				// $('#customize-control-loginpress_customization-show_some_love_text_color').show();		

			} else {

				loginpress_find('.copyRight').remove();
				$('#customize-control-loginpress_customization-login_footer_copy_right').hide();
				$('#customize-control-loginpress_customization-copyright_background_color').hide();
				$('#customize-control-loginpress_customization-copyright_text_color').hide();
				// $('#customize-control-loginpress_customization-show_some_love_text_color').hide();
				loginpress_find('.footer-wrapper').hide();

			}
		});
	});
	
	/**
	 * Change LoginPress Google reCaptcha size in real time...
	 * @version 3.0.0
	 */
	wp.customize('loginpress_customization[recaptcha_size]', function (value) {
		value.bind(function (loginPressVal) {

			if (loginPressVal == '') {
				loginpress_find('.loginpress_recaptcha_wrapper .g-recaptcha').css('transform', '');
			} else {
				loginpress_find('.loginpress_recaptcha_wrapper .g-recaptcha').css('transform', 'scale(' + loginPressVal + ')');
				if (loginpress_find('.loginpress_recaptcha_wrapper')) {
					var reCaptchaWidth = 304;
					// Get the containing element's width
					var containerWidth = loginpress_find('.loginpress_recaptcha_wrapper')[0].clientWidth;
					// Only scale the reCAPTCHA if it won't fit inside the container
					if (reCaptchaWidth > containerWidth && loginpress_find('.g-recaptcha')[0].getBoundingClientRect().width > loginpress_find('.g-recaptcha')[0].parentElement.clientWidth) {
						// Calculate the scale
						var captchaScale = containerWidth / reCaptchaWidth;
						// Apply the transformation with !important flag
						loginpress_find('.g-recaptcha')[0].style.setProperty('transform', 'scale(' + captchaScale + ')', 'important');
					}
				}
			}
		});
	});


	/**
	 * @since 1.0.9
	 * @version 1.0.12
	 */
	$(window).on('load', function () {

		if ($('#customize-control-loginpress_customization-setting_logo_display input[type="checkbox"]').is(":checked")) {
			$('#customize-control-loginpress_customization-setting_logo_display').nextAll().hide();
		} else {
			$('#customize-control-loginpress_customization-setting_logo_display').nextAll().show();
		}

		if ( $('#customize-control-loginpress_customization-loginpress_show_love input[type="checkbox"]').is(":checked") ) {
			$('#customize-control-loginpress_customization-loginpress_show_love').nextAll().show();
			// $('#customize-control-loginpress_customization-show_some_love_text_color').css( 'display', 'none' );
		} else {
			$('#customize-control-loginpress_customization-loginpress_show_love').nextAll().hide();
			// $('#customize-control-loginpress_customization-show_some_love_text_color').css( 'display', 'block' );
		}

		if ( $('#customize-control-loginpress_customization-loginpress_display_bg input[type="checkbox"]').is(":checked") ) {
			$('#customize-control-loginpress_customization-loginpress_display_bg').nextUntil('#customize-control-loginpress_customization-bg_video_group').show();
			$('#customize-control-loginpress_customization-mobile_background').show();
			if( $('#customize-control-loginpress_customization-setting_background .attachment-thumb').length > 0 ){
				$('#customize-control-loginpress_customization-gallery_background').hide();
			}
		} else {
			$('#customize-control-loginpress_customization-loginpress_display_bg').nextUntil('#customize-control-loginpress_customization-bg_video_group').hide();
			$('#customize-control-loginpress_customization-mobile_background').hide();

		}

		if ( $('#customize-control-loginpress_customization-setting_background .attachment-media-view-image').length > 0  ) {
			$('#customize-control-loginpress_customization-gallery_background').css( 'display', 'none' );
		}

		if ( $('#customize-control-loginpress_customization-setting_form_display_bg input[type="checkbox"]').is(":checked") ) {
			$('#customize-control-loginpress_customization-form_background_color').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-setting_form_background').css( 'display', 'none' );
		} else {
			$('#customize-control-loginpress_customization-form_background_color').css( 'display', 'list-item' );
			$('#customize-control-loginpress_customization-setting_form_background').css( 'display', 'list-item' );
		}

		if ( $('#customize-control-loginpress_customization-footer_display_text input[type="checkbox"]').is(":checked") ) {

			$('#customize-control-loginpress_customization-login_footer_text').css( 'display', 'list-item' );
			$('#customize-control-loginpress_customization-login_footer_text_decoration').css( 'display', 'list-item' );
			$('#customize-control-loginpress_customization-login_footer_color').css( 'display', 'list-item' );
			$('#customize-control-loginpress_customization-login_footer_color_hover').css( 'display', 'list-item' );
			$('#customize-control-loginpress_customization-login_footer_font_size').css( 'display', 'list-item' );
			$('#customize-control-loginpress_customization-login_footer_bg_color').css( 'display', 'list-item' );

		} else {

			$('#customize-control-loginpress_customization-login_footer_text').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-login_footer_text_decoration').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-login_footer_color').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-login_footer_color_hover').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-login_footer_font_size').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-login_footer_bg_color').css( 'display', 'none' );

		}

		if ( $('#customize-control-loginpress_customization-back_display_text input[type="checkbox"]').is(":checked") ) {

			$('#customize-control-loginpress_customization-login_back_text_decoration').css( 'display', 'list-item' );
			$('#customize-control-loginpress_customization-login_back_color').css( 'display', 'list-item' );
			$('#customize-control-loginpress_customization-login_back_color_hover').css( 'display', 'list-item' );
			$('#customize-control-loginpress_customization-login_back_font_size').css( 'display', 'list-item' );
			$('#customize-control-loginpress_customization-login_back_bg_color').css( 'display', 'list-item' );
			$('#customize-control-loginpress_customization-copyright_background_color').css( 'display', 'list-item' );

		} else {

			$('#customize-control-loginpress_customization-login_back_text_decoration').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-login_back_color').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-login_back_color_hover').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-login_back_font_size').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-login_back_bg_color').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-copyright_background_color').css( 'display', 'none' );

		}

		if ( $('#customize-control-loginpress_customization-login_copy_right_display input[type="checkbox"]').is(":checked") ) {

			$('#customize-control-loginpress_customization-login_footer_copy_right').css( 'display', 'list-item' );
			$('#customize-control-loginpress_customization-copyright_background_color').css( 'display', 'list-item' );
			$('#customize-control-loginpress_customization-copyright_text_color').css( 'display', 'list-item' );
			// $('#customize-control-loginpress_customization-show_some_love_text_color').css( 'display', 'list-item' );
				
		} else {

			$('#customize-control-loginpress_customization-login_footer_copy_right').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-copyright_background_color').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-copyright_text_color').css( 'display', 'none' );

		}

		if ( $('#customize-control-loginpress_customization-loginpress_display_bg_video input[type="checkbox"]').is(":checked") ) {

			$('#customize-control-loginpress_customization-bg_video_medium').css( 'display', 'list-item' );
			
			if( 'youtube' === $('#customize-control-loginpress_customization-bg_video_medium input[type="radio"]:checked').val() ) {
				$('#customize-control-loginpress_customization-yt_video_id').css( 'display', 'list-item' );

				if ($('.yt_video_not_found').length == 0) {
					// code to run if it isn't there
					$('#customize-control-loginpress_customization-yt_video_id').append( $( "<span class='yt_video_not_found'>"+loginpress_script.translated_strings['wrong_yt_id']+"</span>" ) );
					$('#customize-control-loginpress_customization-yt_video_id').append( $( '<span class="loginpress_customizer_loader .customize-control-title"><img src="'+ loginpress_script.preset_loader +'" /></span>' ) );
				}
				$('.yt_video_not_found').hide();
				$('.loginpress_customizer_loader').hide();
				$('#customize-control-loginpress_customization-background_video').css( 'display', 'none' );

				$('#customize-control-loginpress_customization-background_video_object').css( 'display', 'none' );
				$('#customize-control-loginpress_customization-video_obj_position').css( 'display', 'none' );
				$('#customize-control-loginpress_customization-background_video_muted').css( 'display', 'none' );
			} else {
				$('#customize-control-loginpress_customization-yt_video_id').css( 'display', 'none' );
				$('#customize-control-loginpress_customization-background_video').css( 'display', 'list-item' );
			}

			
			$('#customize-control-loginpress_customization-background_video_object').css( 'display', 'list-item' );
			$('#customize-control-loginpress_customization-video_obj_position').css( 'display', 'list-item' );
			$('#customize-control-loginpress_customization-background_video_muted').css( 'display', 'list-item' );

		} else {

			$('#customize-control-loginpress_customization-bg_video_medium').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-background_video').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-yt_video_id').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-background_video_object').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-video_obj_position').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-background_video_muted').css( 'display', 'none' );

		}

		// Toggle controls if video exist or not in customizer for background.
		if ($('#customize-control-loginpress_customization-background_video video').length > 0 || ($('#customize-control-loginpress_customization-yt_video_id input[type="text"]').length > 0 && $('#customize-control-loginpress_customization-yt_video_id input[type="text"]').val().length > 0)) {

			$('#customize-control-loginpress_customization-background_video_object').css( 'display', 'list-item' );
			$('#customize-control-loginpress_customization-video_obj_position').css( 'display', 'list-item' );
			$('#customize-control-loginpress_customization-background_video_muted').css( 'display', 'list-item' );

		} else {

			$('#customize-control-loginpress_customization-background_video_object').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-video_obj_position').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-background_video_muted').css( 'display', 'none' );

		}

		if ( $('#customize-control-loginpress_customization-background_video video').length > 0 && !$('#customize-control-loginpress_customization-loginpress_display_bg_video input[type="checkbox"]').is(":checked") ) {

			$('#customize-control-loginpress_customization-background_video').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-background_video_object').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-video_obj_position').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-background_video_muted').css( 'display', 'none' );

		} else if( !$('#customize-control-loginpress_customization-loginpress_display_bg_video input[type="checkbox"]').is(":checked")) {

			$('#customize-control-loginpress_customization-background_video').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-background_video_object').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-video_obj_position').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-background_video_muted').css( 'display', 'none' );

		} else if($('#customize-control-loginpress_customization-background_video video').length == 0 && $('#customize-control-loginpress_customization-loginpress_display_bg_video input[type="checkbox"]').is(":checked")) {

			if( 'youtube' === $('#customize-control-loginpress_customization-bg_video_medium input[type="radio"]:checked').val() ) {
				$('#customize-control-loginpress_customization-yt_video_id').css( 'display', 'list-item' );

				$('#customize-control-loginpress_customization-background_video_object').css( 'display', 'none' );
				$('#customize-control-loginpress_customization-video_obj_position').css( 'display', 'none' );
				$('#customize-control-loginpress_customization-background_video_muted').css( 'display', 'none' );
			} else {
				$('#customize-control-loginpress_customization-background_video').css( 'display', 'list-item' );
				

			$('#customize-control-loginpress_customization-background_video_object').css( 'display', 'list-item' );
			$('#customize-control-loginpress_customization-video_obj_position').css( 'display', 'list-item' );
			$('#customize-control-loginpress_customization-background_video_muted').css( 'display', 'list-item' );
			}

			$('#customize-control-loginpress_customization-background_video_object').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-video_obj_position').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-background_video_muted').css( 'display', 'none' );
		} else {

			if( 'youtube' === $('#customize-control-loginpress_customization-bg_video_medium input[type="radio"]:checked').val() ) {
				$('#customize-control-loginpress_customization-yt_video_id').css( 'display', 'list-item' );

				$('#customize-control-loginpress_customization-background_video_object').css( 'display', 'none' );
				$('#customize-control-loginpress_customization-video_obj_position').css( 'display', 'none' );
				$('#customize-control-loginpress_customization-background_video_muted').css( 'display', 'none' );
			} else {
				$('#customize-control-loginpress_customization-background_video').css( 'display', 'list-item' );

				$('#customize-control-loginpress_customization-background_video_object').css( 'display', 'list-item' );
				$('#customize-control-loginpress_customization-video_obj_position').css( 'display', 'list-item' );
				$('#customize-control-loginpress_customization-background_video_muted').css( 'display', 'list-item' );
			}
			
			$('#customize-control-loginpress_customization-background_video_object').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-video_obj_position').css( 'display', 'none' );
			$('#customize-control-loginpress_customization-background_video_muted').css( 'display', 'none' );

		}
		$("<style type='text/css' id='loginpress-customize'></style>").appendTo(loginpress_find('head'));
		$("<style type='text/css' id='loginpress-iframe-bgColor'></style>").appendTo(loginpress_find('head'));
		$("<style type='text/css' id='loginpress-scbg-position'></style>").appendTo(loginpress_find('head'));
		$("<style type='text/css' id='loginpress-scbg-size'></style>").appendTo(loginpress_find('head'));
		$("<style type='text/css' id='loginpress-scbg-repeat'></style>").appendTo(loginpress_find('head'));
		// Fix Firefox focus issue.
		if (loginpress_script.autoFocusPanel && !(navigator.userAgent.toLowerCase().indexOf('firefox') > -1 )  ) { // Auto Focus on LoginPress Panel // 1.2.0
			wp.customize.panel("loginpress_panel").focus();
		}

		if ( 'v2-robot' != loginpress_script.recaptchaType ) { // Disabled reCaptcha Size Option in customizer. // 1.2.1 - 2.1.2 Pro
			$("#customize-control-loginpress_customization-recaptcha_size select").attr('disabled', 'disabled');
		}

		if( $('#customize_presets_settingsdefault18').is(':checked') == true ) {

			loginpress_manage_customizer_controls( ['setting_logo', 'customize_logo_width', 'customize_logo_height'], 'off' );
			loginpress_find('#loginform #user_login').on('focus',function(){
				loginpress_find('.login h1 a').attr('data-state', 'uifocus');
				loginpress_find('.login h1 a').addClass('watchdown');
			});
			loginpress_find('#loginform #user_login').on('blur',function(){
				loginpress_find('.login h1 a').attr('data-state', 'uiblur');
				loginpress_find('.login h1 a').removeClass('watchdown').addClass('watchup');
				setTimeout( function() {
					loginpress_find('.login h1 a').removeClass('watchup');
				}, 800);
			});
			loginpress_find('#loginform #user_pass').on('focus',function(){
				loginpress_find('.login h1 a').attr('data-state', 'pwfocus');
				setTimeout( function() {
					loginpress_find('.login h1 a').addClass('yeti-hide');
				}, 800);
			});
			loginpress_find('#loginform #user_pass').on('blur',function(){
				loginpress_find('.login h1 a').attr('data-state', 'pwblur');
				loginpress_find('.login h1 a').removeClass('yeti-hide').addClass('yeti-seak');
				setTimeout( function() {
					loginpress_find('.login h1 a').removeClass('yeti-seak');
				}, 500);
			});
		} else {
			//Disable toggle fix for Display Logo
			loginpress_manage_customizer_controls( ['customize_login_page_title'], 'on' );
		}
	});
})(jQuery);
