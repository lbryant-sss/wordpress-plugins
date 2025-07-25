// FixIn: 8.7.11.10.
(function ( $ ){

	$.fn.extend( {

		wpbc_timeselector: function (){

			var times_options = [];

			this.each( function (){

				var el = $( this );

				// On new days click we are searching for old time items,  and remove them from this booking form
				if ( el.parent().find( '.wpbc_times_selector' ).length ) {
					el.parent().find( '.wpbc_times_selector' ).remove();
				}

				el.find( 'option' ).each( function ( ind ){

					times_options.push( {
										  title   : jQuery( this ).text()
										, value   : jQuery( this ).val()
										, disabled: jQuery( this ).is( ':disabled' )
										, selected: jQuery( this ).is( ':selected' )
										} );

				} );

				var times_options_html = $.fn.wpbc_timeselector.format( times_options );

				el.after( times_options_html );

				el.next('.wpbc_times_selector').find('div').not('.wpbc_time_picker_disabled').on( "click", function() {

					// Get data value of clicked DIV time-slot
					var selected_value = jQuery( this ).attr( 'data-value' );

					// Remove previos selected class
					jQuery( this ).parent( '.wpbc_times_selector' ).find( '.wpbc_time_selected' ).removeClass( 'wpbc_time_selected' );
					// Set  time item with  selected Class
					jQuery( this ).addClass('wpbc_time_selected');

					el.find( 'option' ).prop( 'selected', false );
					// Find option in selectbox with this value
					el.find( 'option[value="' + selected_value + '"]' ).prop( 'selected', true );

					el.trigger( 'change' );
				});

				// Execute a function when the user presses a key (13 - Enter) on the keyboard. (FixIn: EAA)
				el.next( '.wpbc_times_selector' ).find( 'div' ).not( '.wpbc_time_picker_disabled' ).on( "keypress", function (event) {
					if ( 13 === event.which ) {
						event.preventDefault();
						console.log( jQuery( this ) );
						jQuery( this ).trigger( 'click' );
					}
				} );

				el.hide();

				times_options = [];
			} );

			return this;				// Chain
		}
	} );


	// Get HTML structure of times selection
	$.fn.wpbc_timeselector.format = function ( el_arr ) {

		var select_div = '';
		var css_class='';

		$.each( el_arr, function (index, el_item){

			if ( !el_item.disabled ){

				if (el_item.selected){
					css_class = 'wpbc_time_selected';
				} else {
					css_class = '';
				}

				select_div += '<div '
									+ ' data-value="' + el_item.value + '" '
									+ ' class="' + css_class + '" '
									+ ' tabindex="0" '
					         + '>'
									+ el_item.title
							 + '</div>'
			} else {
				// Uncomment row bellow to Show booked time slots as unavailable RED slots		// FixIn: 9.9.0.2.
				// select_div += '<div class="wpbc_time_picker_disabled">' + el_item.title + '</div>';
			}

		} );

		if ( '' == select_div ){
			select_div = '<span class="wpbc_no_time_pickers">'
							+ 'No available times'
					   + '</span>'
		}
		return '<div class="wpbc_times_selector">' + select_div + '</div>';
	}


})( jQuery );


/**
 *  Init  time selector
 */
function wpbc_hook__init_timeselector(){

	if ( true !== _wpbc.get_other_param( 'is_enabled_booking_timeslot_picker' ) ) {
		return false;
	}

	// Load after page loaded
	jQuery( 'select[name^="rangetime"]' ).wpbc_timeselector();
	jQuery( 'select[name^="starttime"]' ).wpbc_timeselector();
	jQuery( 'select[name^="endtime"]' ).wpbc_timeselector();
	jQuery( 'select[name^="durationtime"]' ).wpbc_timeselector();

	// This hook loading after each day selection																// FixIn: 8.7.11.9.
	jQuery( ".booking_form_div" ).on( 'wpbc_hook_timeslots_disabled', function ( event, bk_type, all_dates ){
		jQuery( '#booking_form_div' + bk_type + ' select[name^="rangetime"]' ).wpbc_timeselector();
		jQuery( '#booking_form_div' + bk_type + ' select[name^="starttime"]' ).wpbc_timeselector();
		jQuery( '#booking_form_div' + bk_type + ' select[name^="endtime"]' ).wpbc_timeselector();
		jQuery( '#booking_form_div' + bk_type + ' select[name^="durationtime"]' ).wpbc_timeselector();
	} );
}

jQuery(document).ready(function(){
//	 setTimeout( function ( ) {					// Need to  have some delay  for loading of all  times in Garbage
	wpbc_hook__init_timeselector();
//	}, 1000 );
});