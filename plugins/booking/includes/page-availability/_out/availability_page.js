"use strict";

/**
 * Request Object
 * Here we can  define Search parameters and Update it later,  when  some parameter was changed
 *
 */
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
var wpbc_ajx_availability = function (obj, $) {
  // Secure parameters for Ajax	------------------------------------------------------------------------------------
  var p_secure = obj.security_obj = obj.security_obj || {
    user_id: 0,
    nonce: '',
    locale: ''
  };
  obj.set_secure_param = function (param_key, param_val) {
    p_secure[param_key] = param_val;
  };
  obj.get_secure_param = function (param_key) {
    return p_secure[param_key];
  };

  // Listing Search parameters	------------------------------------------------------------------------------------
  var p_listing = obj.search_request_obj = obj.search_request_obj || {
    // sort            : "booking_id",
    // sort_type       : "DESC",
    // page_num        : 1,
    // page_items_count: 10,
    // create_date     : "",
    // keyword         : "",
    // source          : ""
  };
  obj.search_set_all_params = function (request_param_obj) {
    p_listing = request_param_obj;
  };
  obj.search_get_all_params = function () {
    return p_listing;
  };
  obj.search_get_param = function (param_key) {
    return p_listing[param_key];
  };
  obj.search_set_param = function (param_key, param_val) {
    // if ( Array.isArray( param_val ) ){
    // 	param_val = JSON.stringify( param_val );
    // }
    p_listing[param_key] = param_val;
  };
  obj.search_set_params_arr = function (params_arr) {
    _.each(params_arr, function (p_val, p_key, p_data) {
      // Define different Search  parameters for request
      this.search_set_param(p_key, p_val);
    });
  };

  // Other parameters 			------------------------------------------------------------------------------------
  var p_other = obj.other_obj = obj.other_obj || {};
  obj.set_other_param = function (param_key, param_val) {
    p_other[param_key] = param_val;
  };
  obj.get_other_param = function (param_key) {
    return p_other[param_key];
  };
  return obj;
}(wpbc_ajx_availability || {}, jQuery);
var wpbc_ajx_bookings = [];

/**
 *   Show Content  ---------------------------------------------------------------------------------------------- */

/**
 * Show Content - Calendar and UI elements
 *
 * @param ajx_data_arr
 * @param ajx_search_params
 * @param ajx_cleaned_params
 */
function wpbc_ajx_availability__page_content__show(ajx_data_arr, ajx_search_params, ajx_cleaned_params) {
  var template__availability_main_page_content = wp.template('wpbc_ajx_availability_main_page_content');

  // Content
  jQuery(wpbc_ajx_availability.get_other_param('listing_container')).html(template__availability_main_page_content({
    'ajx_data': ajx_data_arr,
    'ajx_search_params': ajx_search_params,
    // $_REQUEST[ 'search_params' ]
    'ajx_cleaned_params': ajx_cleaned_params
  }));
  jQuery('.wpbc_processing.wpbc_spin').parent().parent().parent().parent('[id^="wpbc_notice_"]').hide();
  // Load calendar
  wpbc_ajx_availability__calendar__show({
    'resource_id': ajx_cleaned_params.resource_id,
    'ajx_nonce_calendar': ajx_data_arr.ajx_nonce_calendar,
    'ajx_data_arr': ajx_data_arr,
    'ajx_cleaned_params': ajx_cleaned_params
  });

  /**
   * Trigger for dates selection in the booking form
   *
   * jQuery( wpbc_ajx_availability.get_other_param( 'listing_container' ) ).on('wpbc_page_content_loaded', function(event, ajx_data_arr, ajx_search_params , ajx_cleaned_params) { ... } );
   */
  jQuery(wpbc_ajx_availability.get_other_param('listing_container')).trigger('wpbc_page_content_loaded', [ajx_data_arr, ajx_search_params, ajx_cleaned_params]);
}

/**
 * Show inline month view calendar              with all predefined CSS (sizes and check in/out,  times containers)
 * @param {obj} calendar_params_arr
			{
				'resource_id'       	: ajx_cleaned_params.resource_id,
				'ajx_nonce_calendar'	: ajx_data_arr.ajx_nonce_calendar,
				'ajx_data_arr'          : ajx_data_arr = { ajx_booking_resources:[], booked_dates: {}, resource_unavailable_dates:[], season_availability:{},.... }
				'ajx_cleaned_params'    : {
											calendar__days_selection_mode: "dynamic"
											calendar__start_week_day: "0"
											calendar__timeslot_day_bg_as_available: ""
											calendar__view__cell_height: ""
											calendar__view__months_in_row: 4
											calendar__view__visible_months: 12
											calendar__view__width: "100%"

											dates_availability: "unavailable"
											dates_selection: "2023-03-14 ~ 2023-03-16"
											do_action: "set_availability"
											resource_id: 1
											ui_clicked_element_id: "wpbc_availability_apply_btn"
											ui_usr__availability_selected_toolbar: "info"
								  		 }
			}
*/
function wpbc_ajx_availability__calendar__show(calendar_params_arr) {
  // Update nonce
  jQuery('#ajx_nonce_calendar_section').html(calendar_params_arr.ajx_nonce_calendar);

  //------------------------------------------------------------------------------------------------------------------
  // Update bookings
  if ('undefined' == typeof wpbc_ajx_bookings[calendar_params_arr.resource_id]) {
    wpbc_ajx_bookings[calendar_params_arr.resource_id] = [];
  }
  wpbc_ajx_bookings[calendar_params_arr.resource_id] = calendar_params_arr['ajx_data_arr']['booked_dates'];

  //------------------------------------------------------------------------------------------------------------------
  /**
   * Define showing mouse over tooltip on unavailable dates
   * It's defined, when calendar REFRESHED (change months or days selection) loaded in jquery.datepick.wpbc.9.0.js :
   * 		$( 'body' ).trigger( 'wpbc_datepick_inline_calendar_refresh', ...		// FixIn: 9.4.4.13.
   */
  jQuery('body').on('wpbc_datepick_inline_calendar_refresh', function (event, resource_id, inst) {
    // inst.dpDiv  it's:  <div class="datepick-inline datepick-multi" style="width: 17712px;">....</div>
    inst.dpDiv.find('.season_unavailable,.before_after_unavailable,.weekdays_unavailable').on('mouseover', function (this_event) {
      // also available these vars: 	resource_id, jCalContainer, inst
      var jCell = jQuery(this_event.currentTarget);
      wpbc_avy__show_tooltip__for_element(jCell, calendar_params_arr['ajx_data_arr']['popover_hints']);
    });
  });

  //------------------------------------------------------------------------------------------------------------------
  /**
   * Define height of the calendar  cells, 	and  mouse over tooltips at  some unavailable dates
   * It's defined, when calendar loaded in jquery.datepick.wpbc.9.0.js :
   * 		$( 'body' ).trigger( 'wpbc_datepick_inline_calendar_loaded', ...		// FixIn: 9.4.4.12.
   */
  jQuery('body').on('wpbc_datepick_inline_calendar_loaded', function (event, resource_id, jCalContainer, inst) {
    // Remove highlight day for today  date
    jQuery('.datepick-days-cell.datepick-today.datepick-days-cell-over').removeClass('datepick-days-cell-over');

    // Set height of calendar  cells if defined this option  // FixIn: 10.12.4.2.
    if ('' !== calendar_params_arr.ajx_cleaned_params.calendar__view__cell_height) {
      jQuery('head').append('<style type="text/css">' + '.hasDatepick .datepick-inline .datepick-title-row th, ' + '.hasDatepick .datepick-inline .datepick-days-cell {' + 'max-height: ' + calendar_params_arr.ajx_cleaned_params.calendar__view__cell_height + ' !important;' + '}' + '</style>');
    }

    // Define showing mouse over tooltip on unavailable dates
    jCalContainer.find('.season_unavailable,.before_after_unavailable,.weekdays_unavailable').on('mouseover', function (this_event) {
      // also available these vars: 	resource_id, jCalContainer, inst
      var jCell = jQuery(this_event.currentTarget);
      wpbc_avy__show_tooltip__for_element(jCell, calendar_params_arr['ajx_data_arr']['popover_hints']);
    });
  });

  //------------------------------------------------------------------------------------------------------------------
  // Define width of entire calendar
  var width = 'width:' + calendar_params_arr.ajx_cleaned_params.calendar__view__width + ';'; // var width = 'width:100%;max-width:100%;';

  if (undefined != calendar_params_arr.ajx_cleaned_params.calendar__view__max_width && '' != calendar_params_arr.ajx_cleaned_params.calendar__view__max_width) {
    width += 'max-width:' + calendar_params_arr.ajx_cleaned_params.calendar__view__max_width + ';';
  } else {
    width += 'max-width:' + calendar_params_arr.ajx_cleaned_params.calendar__view__months_in_row * 341 + 'px;';
  }

  //------------------------------------------------------------------------------------------------------------------
  // Add calendar container: "Calendar is loading..."  and textarea
  jQuery('.wpbc_ajx_avy__calendar').html('<div class="' + ' bk_calendar_frame' + ' months_num_in_row_' + calendar_params_arr.ajx_cleaned_params.calendar__view__months_in_row + ' cal_month_num_' + calendar_params_arr.ajx_cleaned_params.calendar__view__visible_months + ' ' + calendar_params_arr.ajx_cleaned_params.calendar__timeslot_day_bg_as_available // 'wpbc_timeslot_day_bg_as_available' || ''
  + '" ' + 'style="' + width + '">' + '<div id="calendar_booking' + calendar_params_arr.resource_id + '">' + 'Calendar is loading...' + '</div>' + '</div>' + '<textarea      id="date_booking' + calendar_params_arr.resource_id + '"' + ' name="date_booking' + calendar_params_arr.resource_id + '"' + ' autocomplete="off"' + ' style="display:none;width:100%;height:10em;margin:2em 0 0;"></textarea>');

  //------------------------------------------------------------------------------------------------------------------
  var cal_param_arr = {
    'html_id': 'calendar_booking' + calendar_params_arr.ajx_cleaned_params.resource_id,
    'text_id': 'date_booking' + calendar_params_arr.ajx_cleaned_params.resource_id,
    'calendar__start_week_day': calendar_params_arr.ajx_cleaned_params.calendar__start_week_day,
    'calendar__view__visible_months': calendar_params_arr.ajx_cleaned_params.calendar__view__visible_months,
    'calendar__days_selection_mode': calendar_params_arr.ajx_cleaned_params.calendar__days_selection_mode,
    'resource_id': calendar_params_arr.ajx_cleaned_params.resource_id,
    'ajx_nonce_calendar': calendar_params_arr.ajx_data_arr.ajx_nonce_calendar,
    'booked_dates': calendar_params_arr.ajx_data_arr.booked_dates,
    'season_availability': calendar_params_arr.ajx_data_arr.season_availability,
    'resource_unavailable_dates': calendar_params_arr.ajx_data_arr.resource_unavailable_dates,
    'popover_hints': calendar_params_arr['ajx_data_arr']['popover_hints'] // {'season_unavailable':'...','weekdays_unavailable':'...','before_after_unavailable':'...',}
  };
  wpbc_show_inline_booking_calendar(cal_param_arr);

  //------------------------------------------------------------------------------------------------------------------
  /**
   * On click AVAILABLE |  UNAVAILABLE button  in widget	-	need to  change help dates text
   */
  jQuery('.wpbc_radio__set_days_availability').on('change', function (event, resource_id, inst) {
    wpbc__inline_booking_calendar__on_days_select(jQuery('#' + cal_param_arr.text_id).val(), cal_param_arr);
  });

  // Show 	'Select days  in calendar then select Available  /  Unavailable status and click Apply availability button.'
  jQuery('#wpbc_toolbar_dates_hint').html('<div class="ui_element"><span class="wpbc_ui_control wpbc_ui_addon wpbc_help_text" >' + cal_param_arr.popover_hints.toolbar_text + '</span></div>');
}

/**
 * 	Load Datepick Inline calendar
 *
 * @param calendar_params_arr		example:{
											'html_id'           : 'calendar_booking' + calendar_params_arr.ajx_cleaned_params.resource_id,
											'text_id'           : 'date_booking' + calendar_params_arr.ajx_cleaned_params.resource_id,

											'calendar__start_week_day': 	  calendar_params_arr.ajx_cleaned_params.calendar__start_week_day,
											'calendar__view__visible_months': calendar_params_arr.ajx_cleaned_params.calendar__view__visible_months,
											'calendar__days_selection_mode':  calendar_params_arr.ajx_cleaned_params.calendar__days_selection_mode,

											'resource_id'        : calendar_params_arr.ajx_cleaned_params.resource_id,
											'ajx_nonce_calendar' : calendar_params_arr.ajx_data_arr.ajx_nonce_calendar,
											'booked_dates'       : calendar_params_arr.ajx_data_arr.booked_dates,
											'season_availability': calendar_params_arr.ajx_data_arr.season_availability,

											'resource_unavailable_dates' : calendar_params_arr.ajx_data_arr.resource_unavailable_dates
										}
 * @returns {boolean}
 */
function wpbc_show_inline_booking_calendar(calendar_params_arr) {
  if (0 === jQuery('#' + calendar_params_arr.html_id).length // If calendar DOM element not exist then exist
  || true === jQuery('#' + calendar_params_arr.html_id).hasClass('hasDatepick') // If the calendar with the same Booking resource already  has been activated, then exist.
  ) {
    return false;
  }

  //------------------------------------------------------------------------------------------------------------------
  // Configure and show calendar
  jQuery('#' + calendar_params_arr.html_id).text('');
  jQuery('#' + calendar_params_arr.html_id).datepick({
    beforeShowDay: function beforeShowDay(date) {
      return wpbc__inline_booking_calendar__apply_css_to_days(date, calendar_params_arr, this);
    },
    onSelect: function onSelect(date) {
      jQuery('#' + calendar_params_arr.text_id).val(date);
      //wpbc_blink_element('.wpbc_widget_available_unavailable', 3, 220);
      return wpbc__inline_booking_calendar__on_days_select(date, calendar_params_arr, this);
    },
    onHover: function onHover(value, date) {
      //wpbc_avy__prepare_tooltip__in_calendar( value, date, calendar_params_arr, this );

      return wpbc__inline_booking_calendar__on_days_hover(value, date, calendar_params_arr, this);
    },
    onChangeMonthYear: null,
    showOn: 'both',
    numberOfMonths: calendar_params_arr.calendar__view__visible_months,
    stepMonths: 1,
    // prevText: 			'&laquo;',
    // nextText: 			'&raquo;',
    prevText: '&lsaquo;',
    nextText: '&rsaquo;',
    dateFormat: 'yy-mm-dd',
    // 'dd.mm.yy',
    changeMonth: false,
    changeYear: false,
    minDate: 0,
    //null,  //Scroll as long as you need
    maxDate: '10y',
    // minDate: new Date(2020, 2, 1), maxDate: new Date(2020, 9, 31), 	// Ability to set any  start and end date in calendar
    showStatus: false,
    closeAtTop: false,
    firstDay: calendar_params_arr.calendar__start_week_day,
    gotoCurrent: false,
    hideIfNoPrevNext: true,
    multiSeparator: ', ',
    multiSelect: 'dynamic' == calendar_params_arr.calendar__days_selection_mode ? 0 : 365,
    // Maximum number of selectable dates:	 Single day = 0,  multi days = 365
    rangeSelect: 'dynamic' == calendar_params_arr.calendar__days_selection_mode,
    rangeSeparator: ' ~ ',
    //' - ',
    // showWeeks: true,
    useThemeRoller: false
  });
  return true;
}

/**
 * Apply CSS to calendar date cells
 *
 * @param date					-  JavaScript Date Obj:  		Mon Dec 11 2023 00:00:00 GMT+0200 (Eastern European Standard Time)
 * @param calendar_params_arr	-  Calendar Settings Object:  	{
																  "html_id": "calendar_booking4",
																  "text_id": "date_booking4",
																  "calendar__start_week_day": 1,
																  "calendar__view__visible_months": 12,
																  "resource_id": 4,
																  "ajx_nonce_calendar": "<input type=\"hidden\" ... />",
																  "booked_dates": {
																	"12-28-2022": [
																	  {
																		"booking_date": "2022-12-28 00:00:00",
																		"approved": "1",
																		"booking_id": "26"
																	  }
																	], ...
																	}
																	'season_availability':{
																		"2023-01-09": true,
																		"2023-01-10": true,
																		"2023-01-11": true, ...
																	}
																  }
																}
 * @param datepick_this			- this of datepick Obj
 *
 * @returns [boolean,string]	- [ {true -available | false - unavailable}, 'CSS classes for calendar day cell' ]
 */
function wpbc__inline_booking_calendar__apply_css_to_days(date, calendar_params_arr, datepick_this) {
  var today_date = new Date(_wpbc.get_other_param('today_arr')[0], parseInt(_wpbc.get_other_param('today_arr')[1]) - 1, _wpbc.get_other_param('today_arr')[2], 0, 0, 0);
  var real_today_date = new Date(_wpbc.get_other_param('time_local_arr')[0], parseInt(_wpbc.get_other_param('time_local_arr')[1]) - 1, _wpbc.get_other_param('time_local_arr')[2], 0, 0, 0);
  var class_day = date.getMonth() + 1 + '-' + date.getDate() + '-' + date.getFullYear(); // '1-9-2023'
  var sql_class_day = wpbc__get__sql_class_date(date); // '2023-01-09'

  var css_date__standard = 'cal4date-' + class_day;
  var css_date__additional = ' wpbc_weekday_' + date.getDay() + ' ';

  //--------------------------------------------------------------------------------------------------------------

  // WEEKDAYS :: Set unavailable week days from - Settings General page in "Availability" section
  for (var i = 0; i < _wpbc.get_other_param('availability__week_days_unavailable').length; i++) {
    if (date.getDay() == _wpbc.get_other_param('availability__week_days_unavailable')[i]) {
      return [!!false, css_date__standard + ' date_user_unavailable' + ' weekdays_unavailable'];
    }
  }
  // 10.9.6.3.
  var date_midnight = new Date(parseInt(date.getFullYear()), parseInt(date.getMonth()) - 0, parseInt(date.getDate()), 0, 0, 0);
  // BEFORE_AFTER :: Set unavailable days Before / After the Today date.
  //if ( ((wpbc_dates__days_between( date, real_today_date )) < parseInt( _wpbc.get_other_param( 'availability__unavailable_from_today' ) ))
  if (today_date.getTime() - date_midnight.getTime() > 0 || parseInt('0' + parseInt(_wpbc.get_other_param('availability__available_from_today'))) > 0 && wpbc_dates__days_between(date_midnight, real_today_date) >= parseInt('0' + parseInt(_wpbc.get_other_param('availability__available_from_today')))) {
    return [false, css_date__standard + ' date_user_unavailable' + ' before_after_unavailable'];
  }

  // SEASONS ::  					Booking > Resources > Availability page
  var is_date_available = calendar_params_arr.season_availability[sql_class_day];
  if (false === is_date_available) {
    // FixIn: 9.5.4.4.
    return [!!false, css_date__standard + ' date_user_unavailable' + ' season_unavailable'];
  }

  // RESOURCE_UNAVAILABLE ::   	Booking > Availability page
  if (wpbc_in_array(calendar_params_arr.resource_unavailable_dates, sql_class_day)) {
    is_date_available = false;
  }
  if (false === is_date_available) {
    // FixIn: 9.5.4.4.
    return [!false, css_date__standard + ' date_user_unavailable' + ' resource_unavailable'];
  }

  //--------------------------------------------------------------------------------------------------------------

  //--------------------------------------------------------------------------------------------------------------

  // Is any bookings in this date ?
  if ('undefined' !== typeof calendar_params_arr.booked_dates[class_day]) {
    var bookings_in_date = calendar_params_arr.booked_dates[class_day];
    if ('undefined' !== typeof bookings_in_date['sec_0']) {
      // "Full day" booking  -> (seconds == 0)

      css_date__additional += '0' === bookings_in_date['sec_0'].approved ? ' date2approve ' : ' date_approved '; // Pending = '0' |  Approved = '1'
      css_date__additional += ' full_day_booking';
      return [!false, css_date__standard + css_date__additional];
    } else if (Object.keys(bookings_in_date).length > 0) {
      // "Time slots" Bookings

      var is_approved = true;
      _.each(bookings_in_date, function (p_val, p_key, p_data) {
        if (!parseInt(p_val.approved)) {
          is_approved = false;
        }
        var ts = p_val.booking_date.substring(p_val.booking_date.length - 1);
        if (true === _wpbc.get_other_param('is_enabled_change_over')) {
          if (ts == '1') {
            css_date__additional += ' check_in_time' + (parseInt(p_val.approved) ? ' check_in_time_date_approved' : ' check_in_time_date2approve');
          }
          if (ts == '2') {
            css_date__additional += ' check_out_time' + (parseInt(p_val.approved) ? ' check_out_time_date_approved' : ' check_out_time_date2approve');
          }
        }
      });
      if (!is_approved) {
        css_date__additional += ' date2approve timespartly';
      } else {
        css_date__additional += ' date_approved timespartly';
      }
      if (!_wpbc.get_other_param('is_enabled_change_over')) {
        css_date__additional += ' times_clock';
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------

  return [true, css_date__standard + css_date__additional + ' date_available'];
}

/**
 * Apply some CSS classes, when we mouse over specific dates in calendar
 * @param value
 * @param date					-  JavaScript Date Obj:  		Mon Dec 11 2023 00:00:00 GMT+0200 (Eastern European Standard Time)
 * @param calendar_params_arr	-  Calendar Settings Object:  	{
																  "html_id": "calendar_booking4",
																  "text_id": "date_booking4",
																  "calendar__start_week_day": 1,
																  "calendar__view__visible_months": 12,
																  "resource_id": 4,
																  "ajx_nonce_calendar": "<input type=\"hidden\" ... />",
																  "booked_dates": {
																	"12-28-2022": [
																	  {
																		"booking_date": "2022-12-28 00:00:00",
																		"approved": "1",
																		"booking_id": "26"
																	  }
																	], ...
																	}
																	'season_availability':{
																		"2023-01-09": true,
																		"2023-01-10": true,
																		"2023-01-11": true, ...
																	}
																  }
																}
 * @param datepick_this			- this of datepick Obj
 *
 * @returns {boolean}
 */
function wpbc__inline_booking_calendar__on_days_hover(value, date, calendar_params_arr, datepick_this) {
  if (null === date) {
    jQuery('.datepick-days-cell-over').removeClass('datepick-days-cell-over'); // clear all highlight days selections
    return false;
  }
  var inst = jQuery.datepick._getInst(document.getElementById('calendar_booking' + calendar_params_arr.resource_id));
  if (1 == inst.dates.length // If we have one selected date
  && 'dynamic' === calendar_params_arr.calendar__days_selection_mode // while have range days selection mode
  ) {
    var td_class;
    var td_overs = [];
    var is_check = true;
    var selceted_first_day = new Date();
    selceted_first_day.setFullYear(inst.dates[0].getFullYear(), inst.dates[0].getMonth(), inst.dates[0].getDate()); //Get first Date

    while (is_check) {
      td_class = selceted_first_day.getMonth() + 1 + '-' + selceted_first_day.getDate() + '-' + selceted_first_day.getFullYear();
      td_overs[td_overs.length] = '#calendar_booking' + calendar_params_arr.resource_id + ' .cal4date-' + td_class; // add to array for later make selection by class

      if (date.getMonth() == selceted_first_day.getMonth() && date.getDate() == selceted_first_day.getDate() && date.getFullYear() == selceted_first_day.getFullYear() || selceted_first_day > date) {
        is_check = false;
      }
      selceted_first_day.setFullYear(selceted_first_day.getFullYear(), selceted_first_day.getMonth(), selceted_first_day.getDate() + 1);
    }

    // Highlight Days
    for (var i = 0; i < td_overs.length; i++) {
      // add class to all elements
      jQuery(td_overs[i]).addClass('datepick-days-cell-over');
    }
    return true;
  }
  return true;
}

/**
 * On DAYs selection in calendar
 *
 * @param dates_selection		-  string:			 '2023-03-07 ~ 2023-03-07' or '2023-04-10, 2023-04-12, 2023-04-02, 2023-04-04'
 * @param calendar_params_arr	-  Calendar Settings Object:  	{
																  "html_id": "calendar_booking4",
																  "text_id": "date_booking4",
																  "calendar__start_week_day": 1,
																  "calendar__view__visible_months": 12,
																  "resource_id": 4,
																  "ajx_nonce_calendar": "<input type=\"hidden\" ... />",
																  "booked_dates": {
																	"12-28-2022": [
																	  {
																		"booking_date": "2022-12-28 00:00:00",
																		"approved": "1",
																		"booking_id": "26"
																	  }
																	], ...
																	}
																	'season_availability':{
																		"2023-01-09": true,
																		"2023-01-10": true,
																		"2023-01-11": true, ...
																	}
																  }
																}
 * @param datepick_this			- this of datepick Obj
 *
 * @returns boolean
 */
function wpbc__inline_booking_calendar__on_days_select(dates_selection, calendar_params_arr) {
  var datepick_this = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
  var inst = jQuery.datepick._getInst(document.getElementById('calendar_booking' + calendar_params_arr.resource_id));
  var dates_arr = []; //  [ "2023-04-09", "2023-04-10", "2023-04-11" ]

  if (-1 !== dates_selection.indexOf('~')) {
    // Range Days

    dates_arr = wpbc_get_dates_arr__from_dates_range_js({
      'dates_separator': ' ~ ',
      //  ' ~ '
      'dates': dates_selection // '2023-04-04 ~ 2023-04-07'
    });
  } else {
    // Multiple Days
    dates_arr = wpbc_get_dates_arr__from_dates_comma_separated_js({
      'dates_separator': ', ',
      //  ', '
      'dates': dates_selection // '2023-04-10, 2023-04-12, 2023-04-02, 2023-04-04'
    });
  }
  wpbc_avy_after_days_selection__show_help_info({
    'calendar__days_selection_mode': calendar_params_arr.calendar__days_selection_mode,
    'dates_arr': dates_arr,
    'dates_click_num': inst.dates.length,
    'popover_hints': calendar_params_arr.popover_hints
  });
  return true;
}

/**
 * Show help info at the top  toolbar about selected dates and future actions
 *
 * @param params
 * 					Example 1:  {
									calendar__days_selection_mode: "dynamic",
									dates_arr:  [ "2023-04-03" ],
									dates_click_num: 1
									'popover_hints'					: calendar_params_arr.popover_hints
								}
 * 					Example 2:  {
									calendar__days_selection_mode: "dynamic"
									dates_arr: Array(10) [ "2023-04-03", "2023-04-04", "2023-04-05", … ]
									dates_click_num: 2
									'popover_hints'					: calendar_params_arr.popover_hints
								}
 */
function wpbc_avy_after_days_selection__show_help_info(params) {
  // console.log( params );	//		[ "2023-04-09", "2023-04-10", "2023-04-11" ]

  var message, color;
  if (jQuery('#ui_btn_avy__set_days_availability__available').is(':checked')) {
    message = params.popover_hints.toolbar_text_available; //'Set dates _DATES_ as _HTML_ available.';
    color = '#11be4c';
  } else {
    message = params.popover_hints.toolbar_text_unavailable; //'Set dates _DATES_ as _HTML_ unavailable.';
    color = '#e43939';
  }
  message = '<span>' + message + '</span>';
  var first_date = params['dates_arr'][0];
  var last_date = 'dynamic' == params.calendar__days_selection_mode ? params['dates_arr'][params['dates_arr'].length - 1] : params['dates_arr'].length > 1 ? params['dates_arr'][1] : '';
  first_date = jQuery.datepick.formatDate('dd M, yy', new Date(first_date + 'T00:00:00'));
  last_date = jQuery.datepick.formatDate('dd M, yy', new Date(last_date + 'T00:00:00'));
  if ('dynamic' == params.calendar__days_selection_mode) {
    if (1 == params.dates_click_num) {
      last_date = '___________';
    } else {
      if ('first_time' == jQuery('.wpbc_ajx_availability_container').attr('wpbc_loaded')) {
        jQuery('.wpbc_ajx_availability_container').attr('wpbc_loaded', 'done');
        wpbc_blink_element('.wpbc_widget_available_unavailable', 3, 220);
      }
    }
    message = message.replace('_DATES_', '</span>'
    //+ '<div>' + 'from' + '</div>'
    + '<span class="wpbc_big_date">' + first_date + '</span>' + '<span>' + '-' + '</span>' + '<span class="wpbc_big_date">' + last_date + '</span>' + '<span>');
  } else {
    // if ( params[ 'dates_arr' ].length > 1 ){
    // 	last_date = ', ' + last_date;
    // 	last_date += ( params[ 'dates_arr' ].length > 2 ) ? ', ...' : '';
    // } else {
    // 	last_date='';
    // }
    var dates_arr = [];
    for (var i = 0; i < params['dates_arr'].length; i++) {
      dates_arr.push(jQuery.datepick.formatDate('dd M yy', new Date(params['dates_arr'][i] + 'T00:00:00')));
    }
    first_date = dates_arr.join(', ');
    message = message.replace('_DATES_', '</span>' + '<span class="wpbc_big_date">' + first_date + '</span>' + '<span>');
  }
  message = message.replace('_HTML_', '</span><span class="wpbc_big_text" style="color:' + color + ';">') + '<span>';

  //message += ' <div style="margin-left: 1em;">' + ' Click on Apply button to apply availability.' + '</div>';

  message = '<div class="wpbc_toolbar_dates_hints">' + message + '</div>';
  jQuery('.wpbc_help_text').html(message);
}

/**
 *   Parse dates  ------------------------------------------------------------------------------------------- */

/**
 * Get dates array,  from comma separated dates
 *
 * @param params       = {
									* 'dates_separator' => ', ',                                        // Dates separator
									* 'dates'           => '2023-04-04, 2023-04-07, 2023-04-05'         // Dates in 'Y-m-d' format: '2023-01-31'
						 }
 *
 * @return array      = [
									* [0] => 2023-04-04
									* [1] => 2023-04-05
									* [2] => 2023-04-06
									* [3] => 2023-04-07
						]
 *
 * Example #1:  wpbc_get_dates_arr__from_dates_comma_separated_js(  {  'dates_separator' : ', ', 'dates' : '2023-04-04, 2023-04-07, 2023-04-05'  }  );
 */
function wpbc_get_dates_arr__from_dates_comma_separated_js(params) {
  var dates_arr = [];
  if ('' !== params['dates']) {
    dates_arr = params['dates'].split(params['dates_separator']);
    dates_arr.sort();
  }
  return dates_arr;
}

/**
 * Get dates array,  from range days selection
 *
 * @param params       =  {
									* 'dates_separator' => ' ~ ',                         // Dates separator
									* 'dates'           => '2023-04-04 ~ 2023-04-07'      // Dates in 'Y-m-d' format: '2023-01-31'
						  }
 *
 * @return array        = [
									* [0] => 2023-04-04
									* [1] => 2023-04-05
									* [2] => 2023-04-06
									* [3] => 2023-04-07
						  ]
 *
 * Example #1:  wpbc_get_dates_arr__from_dates_range_js(  {  'dates_separator' : ' ~ ', 'dates' : '2023-04-04 ~ 2023-04-07'  }  );
 * Example #2:  wpbc_get_dates_arr__from_dates_range_js(  {  'dates_separator' : ' - ', 'dates' : '2023-04-04 - 2023-04-07'  }  );
 */
function wpbc_get_dates_arr__from_dates_range_js(params) {
  var dates_arr = [];
  if ('' !== params['dates']) {
    dates_arr = params['dates'].split(params['dates_separator']);
    var check_in_date_ymd = dates_arr[0];
    var check_out_date_ymd = dates_arr[1];
    if ('' !== check_in_date_ymd && '' !== check_out_date_ymd) {
      dates_arr = wpbc_get_dates_array_from_start_end_days_js(check_in_date_ymd, check_out_date_ymd);
    }
  }
  return dates_arr;
}

/**
 * Get dates array based on start and end dates.
 *
 * @param string sStartDate - start date: 2023-04-09
 * @param string sEndDate   - end date:   2023-04-11
 * @return array             - [ "2023-04-09", "2023-04-10", "2023-04-11" ]
 */
function wpbc_get_dates_array_from_start_end_days_js(sStartDate, sEndDate) {
  sStartDate = new Date(sStartDate + 'T00:00:00');
  sEndDate = new Date(sEndDate + 'T00:00:00');
  var aDays = [];

  // Start the variable off with the start date
  aDays.push(sStartDate.getTime());

  // Set a 'temp' variable, sCurrentDate, with the start date - before beginning the loop
  var sCurrentDate = new Date(sStartDate.getTime());
  var one_day_duration = 24 * 60 * 60 * 1000;

  // While the current date is less than the end date
  while (sCurrentDate < sEndDate) {
    // Add a day to the current date "+1 day"
    sCurrentDate.setTime(sCurrentDate.getTime() + one_day_duration);

    // Add this new day to the aDays array
    aDays.push(sCurrentDate.getTime());
  }
  for (var i = 0; i < aDays.length; i++) {
    aDays[i] = new Date(aDays[i]);
    aDays[i] = aDays[i].getFullYear() + '-' + (aDays[i].getMonth() + 1 < 10 ? '0' : '') + (aDays[i].getMonth() + 1) + '-' + (aDays[i].getDate() < 10 ? '0' : '') + aDays[i].getDate();
  }
  // Once the loop has finished, return the array of days.
  return aDays;
}

/**
 *   Tooltips  ---------------------------------------------------------------------------------------------- */

/**
 * Define showing tooltip,  when  mouse over on  SELECTABLE (available, pending, approved, resource unavailable),  days
 * Can be called directly  from  datepick init function.
 *
 * @param value
 * @param date
 * @param calendar_params_arr
 * @param datepick_this
 * @returns {boolean}
 */
function wpbc_avy__prepare_tooltip__in_calendar(value, date, calendar_params_arr, datepick_this) {
  if (null == date) {
    return false;
  }
  var td_class = date.getMonth() + 1 + '-' + date.getDate() + '-' + date.getFullYear();
  var jCell = jQuery('#calendar_booking' + calendar_params_arr.resource_id + ' td.cal4date-' + td_class);
  wpbc_avy__show_tooltip__for_element(jCell, calendar_params_arr['popover_hints']);
  return true;
}

/**
 * Define tooltip  for showing on UNAVAILABLE days (season, weekday, today_depends unavailable)
 *
 * @param jCell					jQuery of specific day cell
 * @param popover_hints		    Array with tooltip hint texts	 : {'season_unavailable':'...','weekdays_unavailable':'...','before_after_unavailable':'...',}
 */
function wpbc_avy__show_tooltip__for_element(jCell, popover_hints) {
  var tooltip_time = '';
  if (jCell.hasClass('season_unavailable')) {
    tooltip_time = popover_hints['season_unavailable'];
  } else if (jCell.hasClass('weekdays_unavailable')) {
    tooltip_time = popover_hints['weekdays_unavailable'];
  } else if (jCell.hasClass('before_after_unavailable')) {
    tooltip_time = popover_hints['before_after_unavailable'];
  } else if (jCell.hasClass('date2approve')) {} else if (jCell.hasClass('date_approved')) {} else {}
  jCell.attr('data-content', tooltip_time);
  var td_el = jCell.get(0); //jQuery( '#calendar_booking' + calendar_params_arr.resource_id + ' td.cal4date-' + td_class ).get(0);

  if (undefined == td_el._tippy && '' != tooltip_time) {
    wpbc_tippy(td_el, {
      content: function content(reference) {
        var popover_content = reference.getAttribute('data-content');
        return '<div class="popover popover_tippy">' + '<div class="popover-content">' + popover_content + '</div>' + '</div>';
      },
      allowHTML: true,
      trigger: 'mouseenter focus',
      interactive: !true,
      hideOnClick: true,
      interactiveBorder: 10,
      maxWidth: 550,
      theme: 'wpbc-tippy-times',
      placement: 'top',
      delay: [400, 0],
      // FixIn: 9.4.2.2.
      ignoreAttributes: true,
      touch: true,
      //['hold', 500], // 500ms delay			// FixIn: 9.2.1.5.
      appendTo: function appendTo() {
        return document.body;
      }
    });
  }
}

/**
 *   Ajax  ------------------------------------------------------------------------------------------------------ */

/**
 * Send Ajax show request
 */
function wpbc_ajx_availability__ajax_request() {
  console.groupCollapsed('WPBC_AJX_AVAILABILITY');
  console.log(' == Before Ajax Send - search_get_all_params() == ', wpbc_ajx_availability.search_get_all_params());
  wpbc_availability_reload_button__spin_start();

  // Start Ajax
  jQuery.post(wpbc_url_ajax, {
    action: 'WPBC_AJX_AVAILABILITY',
    wpbc_ajx_user_id: wpbc_ajx_availability.get_secure_param('user_id'),
    nonce: wpbc_ajx_availability.get_secure_param('nonce'),
    wpbc_ajx_locale: wpbc_ajx_availability.get_secure_param('locale'),
    search_params: wpbc_ajx_availability.search_get_all_params()
  },
  /**
   * S u c c e s s
   *
   * @param response_data		-	its object returned from  Ajax - class-live-searcg.php
   * @param textStatus		-	'success'
   * @param jqXHR				-	Object
   */
  function (response_data, textStatus, jqXHR) {
    console.log(' == Response WPBC_AJX_AVAILABILITY == ', response_data);
    console.groupEnd();

    // Probably Error
    if (_typeof(response_data) !== 'object' || response_data === null) {
      wpbc_ajx_availability__show_message(response_data);
      return;
    }

    // Reload page, after filter toolbar has been reset
    if (undefined != response_data['ajx_cleaned_params'] && 'reset_done' === response_data['ajx_cleaned_params']['do_action']) {
      location.reload();
      return;
    }

    // Show listing
    wpbc_ajx_availability__page_content__show(response_data['ajx_data'], response_data['ajx_search_params'], response_data['ajx_cleaned_params']);

    //wpbc_ajx_availability__define_ui_hooks();						// Redefine Hooks, because we show new DOM elements
    if ('' != response_data['ajx_data']['ajx_after_action_message'].replace(/\n/g, "<br />")) {
      wpbc_admin_show_message(response_data['ajx_data']['ajx_after_action_message'].replace(/\n/g, "<br />"), '1' == response_data['ajx_data']['ajx_after_action_result'] ? 'success' : 'error', 10000);
    }
    wpbc_availability_reload_button__spin_pause();
    // Remove spin icon from  button and Enable this button.
    wpbc_button__remove_spin(response_data['ajx_cleaned_params']['ui_clicked_element_id']);
    jQuery('#ajax_respond').html(response_data); // For ability to show response, add such DIV element to page
  }).fail(function (jqXHR, textStatus, errorThrown) {
    if (window.console && window.console.log) {
      console.log('Ajax_Error', jqXHR, textStatus, errorThrown);
    }
    var error_message = '<strong>' + 'Error!' + '</strong> ' + errorThrown;
    if (jqXHR.status) {
      error_message += ' (<b>' + jqXHR.status + '</b>)';
      if (403 == jqXHR.status) {
        error_message += ' Probably nonce for this page has been expired. Please <a href="javascript:void(0)" onclick="javascript:location.reload();">reload the page</a>.';
      }
    }
    if (jqXHR.responseText) {
      error_message += ' ' + jqXHR.responseText;
    }
    error_message = error_message.replace(/\n/g, "<br />");
    wpbc_ajx_availability__show_message(error_message);
  })
  // .done(   function ( data, textStatus, jqXHR ) {   if ( window.console && window.console.log ){ console.log( 'second success', data, textStatus, jqXHR ); }    })
  // .always( function ( data_jqXHR, textStatus, jqXHR_errorThrown ) {   if ( window.console && window.console.log ){ console.log( 'always finished', data_jqXHR, textStatus, jqXHR_errorThrown ); }     })
  ; // End Ajax
}

/**
 *   H o o k s  -  its Action/Times when need to re-Render Views  ----------------------------------------------- */

/**
 * Send Ajax Search Request after Updating search request parameters
 *
 * @param params_arr
 */
function wpbc_ajx_availability__send_request_with_params(params_arr) {
  // Define different Search  parameters for request
  _.each(params_arr, function (p_val, p_key, p_data) {
    //console.log( 'Request for: ', p_key, p_val );
    wpbc_ajx_availability.search_set_param(p_key, p_val);
  });

  // Send Ajax Request
  wpbc_ajx_availability__ajax_request();
}

/**
 * Search request for "Page Number"
 * @param page_number	int
 */
function wpbc_ajx_availability__pagination_click(page_number) {
  wpbc_ajx_availability__send_request_with_params({
    'page_num': page_number
  });
}

/**
 *   Show / Hide Content  --------------------------------------------------------------------------------------- */

/**
 *  Show Listing Content 	- 	Sending Ajax Request	-	with parameters that  we early  defined
 */
function wpbc_ajx_availability__actual_content__show() {
  wpbc_ajx_availability__ajax_request(); // Send Ajax Request	-	with parameters that  we early  defined in "wpbc_ajx_booking_listing" Obj.
}

/**
 * Hide Listing Content
 */
function wpbc_ajx_availability__actual_content__hide() {
  jQuery(wpbc_ajx_availability.get_other_param('listing_container')).html('');
}

/**
 *   M e s s a g e  --------------------------------------------------------------------------------------------- */

/**
 * Show just message instead of content
 */
function wpbc_ajx_availability__show_message(message) {
  wpbc_ajx_availability__actual_content__hide();
  jQuery(wpbc_ajx_availability.get_other_param('listing_container')).html('<div class="wpbc-settings-notice notice-warning" style="text-align:left">' + message + '</div>');
}

/**
 *   Support Functions - Spin Icon in Buttons  ------------------------------------------------------------------ */

/**
 * Spin button in Filter toolbar  -  Start
 */
function wpbc_availability_reload_button__spin_start() {
  jQuery('#wpbc_availability_reload_button .menu_icon.wpbc_spin').removeClass('wpbc_animation_pause');
}

/**
 * Spin button in Filter toolbar  -  Pause
 */
function wpbc_availability_reload_button__spin_pause() {
  jQuery('#wpbc_availability_reload_button .menu_icon.wpbc_spin').addClass('wpbc_animation_pause');
}

/**
 * Spin button in Filter toolbar  -  is Spinning ?
 *
 * @returns {boolean}
 */
function wpbc_availability_reload_button__is_spin() {
  if (jQuery('#wpbc_availability_reload_button .menu_icon.wpbc_spin').hasClass('wpbc_animation_pause')) {
    return true;
  } else {
    return false;
  }
}
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5jbHVkZXMvcGFnZS1hdmFpbGFiaWxpdHkvX291dC9hdmFpbGFiaWxpdHlfcGFnZS5qcyIsIm5hbWVzIjpbIl90eXBlb2YiLCJvIiwiU3ltYm9sIiwiaXRlcmF0b3IiLCJjb25zdHJ1Y3RvciIsInByb3RvdHlwZSIsIndwYmNfYWp4X2F2YWlsYWJpbGl0eSIsIm9iaiIsIiQiLCJwX3NlY3VyZSIsInNlY3VyaXR5X29iaiIsInVzZXJfaWQiLCJub25jZSIsImxvY2FsZSIsInNldF9zZWN1cmVfcGFyYW0iLCJwYXJhbV9rZXkiLCJwYXJhbV92YWwiLCJnZXRfc2VjdXJlX3BhcmFtIiwicF9saXN0aW5nIiwic2VhcmNoX3JlcXVlc3Rfb2JqIiwic2VhcmNoX3NldF9hbGxfcGFyYW1zIiwicmVxdWVzdF9wYXJhbV9vYmoiLCJzZWFyY2hfZ2V0X2FsbF9wYXJhbXMiLCJzZWFyY2hfZ2V0X3BhcmFtIiwic2VhcmNoX3NldF9wYXJhbSIsInNlYXJjaF9zZXRfcGFyYW1zX2FyciIsInBhcmFtc19hcnIiLCJfIiwiZWFjaCIsInBfdmFsIiwicF9rZXkiLCJwX2RhdGEiLCJwX290aGVyIiwib3RoZXJfb2JqIiwic2V0X290aGVyX3BhcmFtIiwiZ2V0X290aGVyX3BhcmFtIiwialF1ZXJ5Iiwid3BiY19hanhfYm9va2luZ3MiLCJ3cGJjX2FqeF9hdmFpbGFiaWxpdHlfX3BhZ2VfY29udGVudF9fc2hvdyIsImFqeF9kYXRhX2FyciIsImFqeF9zZWFyY2hfcGFyYW1zIiwiYWp4X2NsZWFuZWRfcGFyYW1zIiwidGVtcGxhdGVfX2F2YWlsYWJpbGl0eV9tYWluX3BhZ2VfY29udGVudCIsIndwIiwidGVtcGxhdGUiLCJodG1sIiwicGFyZW50IiwiaGlkZSIsIndwYmNfYWp4X2F2YWlsYWJpbGl0eV9fY2FsZW5kYXJfX3Nob3ciLCJyZXNvdXJjZV9pZCIsImFqeF9ub25jZV9jYWxlbmRhciIsInRyaWdnZXIiLCJjYWxlbmRhcl9wYXJhbXNfYXJyIiwib24iLCJldmVudCIsImluc3QiLCJkcERpdiIsImZpbmQiLCJ0aGlzX2V2ZW50IiwiakNlbGwiLCJjdXJyZW50VGFyZ2V0Iiwid3BiY19hdnlfX3Nob3dfdG9vbHRpcF9fZm9yX2VsZW1lbnQiLCJqQ2FsQ29udGFpbmVyIiwicmVtb3ZlQ2xhc3MiLCJjYWxlbmRhcl9fdmlld19fY2VsbF9oZWlnaHQiLCJhcHBlbmQiLCJ3aWR0aCIsImNhbGVuZGFyX192aWV3X193aWR0aCIsInVuZGVmaW5lZCIsImNhbGVuZGFyX192aWV3X19tYXhfd2lkdGgiLCJjYWxlbmRhcl9fdmlld19fbW9udGhzX2luX3JvdyIsImNhbGVuZGFyX192aWV3X192aXNpYmxlX21vbnRocyIsImNhbGVuZGFyX190aW1lc2xvdF9kYXlfYmdfYXNfYXZhaWxhYmxlIiwiY2FsX3BhcmFtX2FyciIsImNhbGVuZGFyX19zdGFydF93ZWVrX2RheSIsImNhbGVuZGFyX19kYXlzX3NlbGVjdGlvbl9tb2RlIiwiYm9va2VkX2RhdGVzIiwic2Vhc29uX2F2YWlsYWJpbGl0eSIsInJlc291cmNlX3VuYXZhaWxhYmxlX2RhdGVzIiwid3BiY19zaG93X2lubGluZV9ib29raW5nX2NhbGVuZGFyIiwid3BiY19faW5saW5lX2Jvb2tpbmdfY2FsZW5kYXJfX29uX2RheXNfc2VsZWN0IiwidGV4dF9pZCIsInZhbCIsInBvcG92ZXJfaGludHMiLCJ0b29sYmFyX3RleHQiLCJodG1sX2lkIiwibGVuZ3RoIiwiaGFzQ2xhc3MiLCJ0ZXh0IiwiZGF0ZXBpY2siLCJiZWZvcmVTaG93RGF5IiwiZGF0ZSIsIndwYmNfX2lubGluZV9ib29raW5nX2NhbGVuZGFyX19hcHBseV9jc3NfdG9fZGF5cyIsIm9uU2VsZWN0Iiwib25Ib3ZlciIsInZhbHVlIiwid3BiY19faW5saW5lX2Jvb2tpbmdfY2FsZW5kYXJfX29uX2RheXNfaG92ZXIiLCJvbkNoYW5nZU1vbnRoWWVhciIsInNob3dPbiIsIm51bWJlck9mTW9udGhzIiwic3RlcE1vbnRocyIsInByZXZUZXh0IiwibmV4dFRleHQiLCJkYXRlRm9ybWF0IiwiY2hhbmdlTW9udGgiLCJjaGFuZ2VZZWFyIiwibWluRGF0ZSIsIm1heERhdGUiLCJzaG93U3RhdHVzIiwiY2xvc2VBdFRvcCIsImZpcnN0RGF5IiwiZ290b0N1cnJlbnQiLCJoaWRlSWZOb1ByZXZOZXh0IiwibXVsdGlTZXBhcmF0b3IiLCJtdWx0aVNlbGVjdCIsInJhbmdlU2VsZWN0IiwicmFuZ2VTZXBhcmF0b3IiLCJ1c2VUaGVtZVJvbGxlciIsImRhdGVwaWNrX3RoaXMiLCJ0b2RheV9kYXRlIiwiRGF0ZSIsIl93cGJjIiwicGFyc2VJbnQiLCJyZWFsX3RvZGF5X2RhdGUiLCJjbGFzc19kYXkiLCJnZXRNb250aCIsImdldERhdGUiLCJnZXRGdWxsWWVhciIsInNxbF9jbGFzc19kYXkiLCJ3cGJjX19nZXRfX3NxbF9jbGFzc19kYXRlIiwiY3NzX2RhdGVfX3N0YW5kYXJkIiwiY3NzX2RhdGVfX2FkZGl0aW9uYWwiLCJnZXREYXkiLCJpIiwiZGF0ZV9taWRuaWdodCIsImdldFRpbWUiLCJ3cGJjX2RhdGVzX19kYXlzX2JldHdlZW4iLCJpc19kYXRlX2F2YWlsYWJsZSIsIndwYmNfaW5fYXJyYXkiLCJib29raW5nc19pbl9kYXRlIiwiYXBwcm92ZWQiLCJPYmplY3QiLCJrZXlzIiwiaXNfYXBwcm92ZWQiLCJ0cyIsImJvb2tpbmdfZGF0ZSIsInN1YnN0cmluZyIsIl9nZXRJbnN0IiwiZG9jdW1lbnQiLCJnZXRFbGVtZW50QnlJZCIsImRhdGVzIiwidGRfY2xhc3MiLCJ0ZF9vdmVycyIsImlzX2NoZWNrIiwic2VsY2V0ZWRfZmlyc3RfZGF5Iiwic2V0RnVsbFllYXIiLCJhZGRDbGFzcyIsImRhdGVzX3NlbGVjdGlvbiIsImFyZ3VtZW50cyIsImRhdGVzX2FyciIsImluZGV4T2YiLCJ3cGJjX2dldF9kYXRlc19hcnJfX2Zyb21fZGF0ZXNfcmFuZ2VfanMiLCJ3cGJjX2dldF9kYXRlc19hcnJfX2Zyb21fZGF0ZXNfY29tbWFfc2VwYXJhdGVkX2pzIiwid3BiY19hdnlfYWZ0ZXJfZGF5c19zZWxlY3Rpb25fX3Nob3dfaGVscF9pbmZvIiwicGFyYW1zIiwibWVzc2FnZSIsImNvbG9yIiwiaXMiLCJ0b29sYmFyX3RleHRfYXZhaWxhYmxlIiwidG9vbGJhcl90ZXh0X3VuYXZhaWxhYmxlIiwiZmlyc3RfZGF0ZSIsImxhc3RfZGF0ZSIsImZvcm1hdERhdGUiLCJkYXRlc19jbGlja19udW0iLCJhdHRyIiwid3BiY19ibGlua19lbGVtZW50IiwicmVwbGFjZSIsInB1c2giLCJqb2luIiwic3BsaXQiLCJzb3J0IiwiY2hlY2tfaW5fZGF0ZV95bWQiLCJjaGVja19vdXRfZGF0ZV95bWQiLCJ3cGJjX2dldF9kYXRlc19hcnJheV9mcm9tX3N0YXJ0X2VuZF9kYXlzX2pzIiwic1N0YXJ0RGF0ZSIsInNFbmREYXRlIiwiYURheXMiLCJzQ3VycmVudERhdGUiLCJvbmVfZGF5X2R1cmF0aW9uIiwic2V0VGltZSIsIndwYmNfYXZ5X19wcmVwYXJlX3Rvb2x0aXBfX2luX2NhbGVuZGFyIiwidG9vbHRpcF90aW1lIiwidGRfZWwiLCJnZXQiLCJfdGlwcHkiLCJ3cGJjX3RpcHB5IiwiY29udGVudCIsInJlZmVyZW5jZSIsInBvcG92ZXJfY29udGVudCIsImdldEF0dHJpYnV0ZSIsImFsbG93SFRNTCIsImludGVyYWN0aXZlIiwiaGlkZU9uQ2xpY2siLCJpbnRlcmFjdGl2ZUJvcmRlciIsIm1heFdpZHRoIiwidGhlbWUiLCJwbGFjZW1lbnQiLCJkZWxheSIsImlnbm9yZUF0dHJpYnV0ZXMiLCJ0b3VjaCIsImFwcGVuZFRvIiwiYm9keSIsIndwYmNfYWp4X2F2YWlsYWJpbGl0eV9fYWpheF9yZXF1ZXN0IiwiY29uc29sZSIsImdyb3VwQ29sbGFwc2VkIiwibG9nIiwid3BiY19hdmFpbGFiaWxpdHlfcmVsb2FkX2J1dHRvbl9fc3Bpbl9zdGFydCIsInBvc3QiLCJ3cGJjX3VybF9hamF4IiwiYWN0aW9uIiwid3BiY19hanhfdXNlcl9pZCIsIndwYmNfYWp4X2xvY2FsZSIsInNlYXJjaF9wYXJhbXMiLCJyZXNwb25zZV9kYXRhIiwidGV4dFN0YXR1cyIsImpxWEhSIiwiZ3JvdXBFbmQiLCJ3cGJjX2FqeF9hdmFpbGFiaWxpdHlfX3Nob3dfbWVzc2FnZSIsImxvY2F0aW9uIiwicmVsb2FkIiwid3BiY19hZG1pbl9zaG93X21lc3NhZ2UiLCJ3cGJjX2F2YWlsYWJpbGl0eV9yZWxvYWRfYnV0dG9uX19zcGluX3BhdXNlIiwid3BiY19idXR0b25fX3JlbW92ZV9zcGluIiwiZmFpbCIsImVycm9yVGhyb3duIiwid2luZG93IiwiZXJyb3JfbWVzc2FnZSIsInN0YXR1cyIsInJlc3BvbnNlVGV4dCIsIndwYmNfYWp4X2F2YWlsYWJpbGl0eV9fc2VuZF9yZXF1ZXN0X3dpdGhfcGFyYW1zIiwid3BiY19hanhfYXZhaWxhYmlsaXR5X19wYWdpbmF0aW9uX2NsaWNrIiwicGFnZV9udW1iZXIiLCJ3cGJjX2FqeF9hdmFpbGFiaWxpdHlfX2FjdHVhbF9jb250ZW50X19zaG93Iiwid3BiY19hanhfYXZhaWxhYmlsaXR5X19hY3R1YWxfY29udGVudF9faGlkZSIsIndwYmNfYXZhaWxhYmlsaXR5X3JlbG9hZF9idXR0b25fX2lzX3NwaW4iXSwic291cmNlcyI6WyJpbmNsdWRlcy9wYWdlLWF2YWlsYWJpbGl0eS9fc3JjL2F2YWlsYWJpbGl0eV9wYWdlLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIlwidXNlIHN0cmljdFwiO1xyXG5cclxuLyoqXHJcbiAqIFJlcXVlc3QgT2JqZWN0XHJcbiAqIEhlcmUgd2UgY2FuICBkZWZpbmUgU2VhcmNoIHBhcmFtZXRlcnMgYW5kIFVwZGF0ZSBpdCBsYXRlciwgIHdoZW4gIHNvbWUgcGFyYW1ldGVyIHdhcyBjaGFuZ2VkXHJcbiAqXHJcbiAqL1xyXG5cclxudmFyIHdwYmNfYWp4X2F2YWlsYWJpbGl0eSA9IChmdW5jdGlvbiAoIG9iaiwgJCkge1xyXG5cclxuXHQvLyBTZWN1cmUgcGFyYW1ldGVycyBmb3IgQWpheFx0LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0dmFyIHBfc2VjdXJlID0gb2JqLnNlY3VyaXR5X29iaiA9IG9iai5zZWN1cml0eV9vYmogfHwge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR1c2VyX2lkOiAwLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRub25jZSAgOiAnJyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0bG9jYWxlIDogJydcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgfTtcclxuXHJcblx0b2JqLnNldF9zZWN1cmVfcGFyYW0gPSBmdW5jdGlvbiAoIHBhcmFtX2tleSwgcGFyYW1fdmFsICkge1xyXG5cdFx0cF9zZWN1cmVbIHBhcmFtX2tleSBdID0gcGFyYW1fdmFsO1xyXG5cdH07XHJcblxyXG5cdG9iai5nZXRfc2VjdXJlX3BhcmFtID0gZnVuY3Rpb24gKCBwYXJhbV9rZXkgKSB7XHJcblx0XHRyZXR1cm4gcF9zZWN1cmVbIHBhcmFtX2tleSBdO1xyXG5cdH07XHJcblxyXG5cclxuXHQvLyBMaXN0aW5nIFNlYXJjaCBwYXJhbWV0ZXJzXHQtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHR2YXIgcF9saXN0aW5nID0gb2JqLnNlYXJjaF9yZXF1ZXN0X29iaiA9IG9iai5zZWFyY2hfcmVxdWVzdF9vYmogfHwge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBzb3J0ICAgICAgICAgICAgOiBcImJvb2tpbmdfaWRcIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8gc29ydF90eXBlICAgICAgIDogXCJERVNDXCIsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vIHBhZ2VfbnVtICAgICAgICA6IDEsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vIHBhZ2VfaXRlbXNfY291bnQ6IDEwLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBjcmVhdGVfZGF0ZSAgICAgOiBcIlwiLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBrZXl3b3JkICAgICAgICAgOiBcIlwiLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBzb3VyY2UgICAgICAgICAgOiBcIlwiXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9O1xyXG5cclxuXHRvYmouc2VhcmNoX3NldF9hbGxfcGFyYW1zID0gZnVuY3Rpb24gKCByZXF1ZXN0X3BhcmFtX29iaiApIHtcclxuXHRcdHBfbGlzdGluZyA9IHJlcXVlc3RfcGFyYW1fb2JqO1xyXG5cdH07XHJcblxyXG5cdG9iai5zZWFyY2hfZ2V0X2FsbF9wYXJhbXMgPSBmdW5jdGlvbiAoKSB7XHJcblx0XHRyZXR1cm4gcF9saXN0aW5nO1xyXG5cdH07XHJcblxyXG5cdG9iai5zZWFyY2hfZ2V0X3BhcmFtID0gZnVuY3Rpb24gKCBwYXJhbV9rZXkgKSB7XHJcblx0XHRyZXR1cm4gcF9saXN0aW5nWyBwYXJhbV9rZXkgXTtcclxuXHR9O1xyXG5cclxuXHRvYmouc2VhcmNoX3NldF9wYXJhbSA9IGZ1bmN0aW9uICggcGFyYW1fa2V5LCBwYXJhbV92YWwgKSB7XHJcblx0XHQvLyBpZiAoIEFycmF5LmlzQXJyYXkoIHBhcmFtX3ZhbCApICl7XHJcblx0XHQvLyBcdHBhcmFtX3ZhbCA9IEpTT04uc3RyaW5naWZ5KCBwYXJhbV92YWwgKTtcclxuXHRcdC8vIH1cclxuXHRcdHBfbGlzdGluZ1sgcGFyYW1fa2V5IF0gPSBwYXJhbV92YWw7XHJcblx0fTtcclxuXHJcblx0b2JqLnNlYXJjaF9zZXRfcGFyYW1zX2FyciA9IGZ1bmN0aW9uKCBwYXJhbXNfYXJyICl7XHJcblx0XHRfLmVhY2goIHBhcmFtc19hcnIsIGZ1bmN0aW9uICggcF92YWwsIHBfa2V5LCBwX2RhdGEgKXtcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBEZWZpbmUgZGlmZmVyZW50IFNlYXJjaCAgcGFyYW1ldGVycyBmb3IgcmVxdWVzdFxyXG5cdFx0XHR0aGlzLnNlYXJjaF9zZXRfcGFyYW0oIHBfa2V5LCBwX3ZhbCApO1xyXG5cdFx0fSApO1xyXG5cdH1cclxuXHJcblxyXG5cdC8vIE90aGVyIHBhcmFtZXRlcnMgXHRcdFx0LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0dmFyIHBfb3RoZXIgPSBvYmoub3RoZXJfb2JqID0gb2JqLm90aGVyX29iaiB8fCB7IH07XHJcblxyXG5cdG9iai5zZXRfb3RoZXJfcGFyYW0gPSBmdW5jdGlvbiAoIHBhcmFtX2tleSwgcGFyYW1fdmFsICkge1xyXG5cdFx0cF9vdGhlclsgcGFyYW1fa2V5IF0gPSBwYXJhbV92YWw7XHJcblx0fTtcclxuXHJcblx0b2JqLmdldF9vdGhlcl9wYXJhbSA9IGZ1bmN0aW9uICggcGFyYW1fa2V5ICkge1xyXG5cdFx0cmV0dXJuIHBfb3RoZXJbIHBhcmFtX2tleSBdO1xyXG5cdH07XHJcblxyXG5cclxuXHRyZXR1cm4gb2JqO1xyXG59KCB3cGJjX2FqeF9hdmFpbGFiaWxpdHkgfHwge30sIGpRdWVyeSApKTtcclxuXHJcbnZhciB3cGJjX2FqeF9ib29raW5ncyA9IFtdO1xyXG5cclxuLyoqXHJcbiAqICAgU2hvdyBDb250ZW50ICAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tICovXHJcblxyXG4vKipcclxuICogU2hvdyBDb250ZW50IC0gQ2FsZW5kYXIgYW5kIFVJIGVsZW1lbnRzXHJcbiAqXHJcbiAqIEBwYXJhbSBhanhfZGF0YV9hcnJcclxuICogQHBhcmFtIGFqeF9zZWFyY2hfcGFyYW1zXHJcbiAqIEBwYXJhbSBhanhfY2xlYW5lZF9wYXJhbXNcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfYWp4X2F2YWlsYWJpbGl0eV9fcGFnZV9jb250ZW50X19zaG93KCBhanhfZGF0YV9hcnIsIGFqeF9zZWFyY2hfcGFyYW1zICwgYWp4X2NsZWFuZWRfcGFyYW1zICl7XHJcblxyXG5cdHZhciB0ZW1wbGF0ZV9fYXZhaWxhYmlsaXR5X21haW5fcGFnZV9jb250ZW50ID0gd3AudGVtcGxhdGUoICd3cGJjX2FqeF9hdmFpbGFiaWxpdHlfbWFpbl9wYWdlX2NvbnRlbnQnICk7XHJcblxyXG5cdC8vIENvbnRlbnRcclxuXHRqUXVlcnkoIHdwYmNfYWp4X2F2YWlsYWJpbGl0eS5nZXRfb3RoZXJfcGFyYW0oICdsaXN0aW5nX2NvbnRhaW5lcicgKSApLmh0bWwoIHRlbXBsYXRlX19hdmFpbGFiaWxpdHlfbWFpbl9wYWdlX2NvbnRlbnQoIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnYWp4X2RhdGEnICAgICAgICAgICAgICA6IGFqeF9kYXRhX2FycixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnYWp4X3NlYXJjaF9wYXJhbXMnICAgICA6IGFqeF9zZWFyY2hfcGFyYW1zLFx0XHRcdFx0XHRcdFx0XHQvLyAkX1JFUVVFU1RbICdzZWFyY2hfcGFyYW1zJyBdXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2FqeF9jbGVhbmVkX3BhcmFtcycgICAgOiBhanhfY2xlYW5lZF9wYXJhbXNcclxuXHRcdFx0XHRcdFx0XHRcdFx0fSApICk7XHJcblxyXG5cdGpRdWVyeSggJy53cGJjX3Byb2Nlc3Npbmcud3BiY19zcGluJykucGFyZW50KCkucGFyZW50KCkucGFyZW50KCkucGFyZW50KCAnW2lkXj1cIndwYmNfbm90aWNlX1wiXScgKS5oaWRlKCk7XHJcblx0Ly8gTG9hZCBjYWxlbmRhclxyXG5cdHdwYmNfYWp4X2F2YWlsYWJpbGl0eV9fY2FsZW5kYXJfX3Nob3coIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdyZXNvdXJjZV9pZCcgICAgICAgOiBhanhfY2xlYW5lZF9wYXJhbXMucmVzb3VyY2VfaWQsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnYWp4X25vbmNlX2NhbGVuZGFyJzogYWp4X2RhdGFfYXJyLmFqeF9ub25jZV9jYWxlbmRhcixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdhanhfZGF0YV9hcnInICAgICAgICAgIDogYWp4X2RhdGFfYXJyLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2FqeF9jbGVhbmVkX3BhcmFtcycgICAgOiBhanhfY2xlYW5lZF9wYXJhbXNcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHR9ICk7XHJcblxyXG5cclxuXHQvKipcclxuXHQgKiBUcmlnZ2VyIGZvciBkYXRlcyBzZWxlY3Rpb24gaW4gdGhlIGJvb2tpbmcgZm9ybVxyXG5cdCAqXHJcblx0ICogalF1ZXJ5KCB3cGJjX2FqeF9hdmFpbGFiaWxpdHkuZ2V0X290aGVyX3BhcmFtKCAnbGlzdGluZ19jb250YWluZXInICkgKS5vbignd3BiY19wYWdlX2NvbnRlbnRfbG9hZGVkJywgZnVuY3Rpb24oZXZlbnQsIGFqeF9kYXRhX2FyciwgYWp4X3NlYXJjaF9wYXJhbXMgLCBhanhfY2xlYW5lZF9wYXJhbXMpIHsgLi4uIH0gKTtcclxuXHQgKi9cclxuXHRqUXVlcnkoIHdwYmNfYWp4X2F2YWlsYWJpbGl0eS5nZXRfb3RoZXJfcGFyYW0oICdsaXN0aW5nX2NvbnRhaW5lcicgKSApLnRyaWdnZXIoICd3cGJjX3BhZ2VfY29udGVudF9sb2FkZWQnLCBbIGFqeF9kYXRhX2FyciwgYWp4X3NlYXJjaF9wYXJhbXMgLCBhanhfY2xlYW5lZF9wYXJhbXMgXSApO1xyXG59XHJcblxyXG5cclxuLyoqXHJcbiAqIFNob3cgaW5saW5lIG1vbnRoIHZpZXcgY2FsZW5kYXIgICAgICAgICAgICAgIHdpdGggYWxsIHByZWRlZmluZWQgQ1NTIChzaXplcyBhbmQgY2hlY2sgaW4vb3V0LCAgdGltZXMgY29udGFpbmVycylcclxuICogQHBhcmFtIHtvYmp9IGNhbGVuZGFyX3BhcmFtc19hcnJcclxuXHRcdFx0e1xyXG5cdFx0XHRcdCdyZXNvdXJjZV9pZCcgICAgICAgXHQ6IGFqeF9jbGVhbmVkX3BhcmFtcy5yZXNvdXJjZV9pZCxcclxuXHRcdFx0XHQnYWp4X25vbmNlX2NhbGVuZGFyJ1x0OiBhanhfZGF0YV9hcnIuYWp4X25vbmNlX2NhbGVuZGFyLFxyXG5cdFx0XHRcdCdhanhfZGF0YV9hcnInICAgICAgICAgIDogYWp4X2RhdGFfYXJyID0geyBhanhfYm9va2luZ19yZXNvdXJjZXM6W10sIGJvb2tlZF9kYXRlczoge30sIHJlc291cmNlX3VuYXZhaWxhYmxlX2RhdGVzOltdLCBzZWFzb25fYXZhaWxhYmlsaXR5Ont9LC4uLi4gfVxyXG5cdFx0XHRcdCdhanhfY2xlYW5lZF9wYXJhbXMnICAgIDoge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0Y2FsZW5kYXJfX2RheXNfc2VsZWN0aW9uX21vZGU6IFwiZHluYW1pY1wiXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRjYWxlbmRhcl9fc3RhcnRfd2Vla19kYXk6IFwiMFwiXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRjYWxlbmRhcl9fdGltZXNsb3RfZGF5X2JnX2FzX2F2YWlsYWJsZTogXCJcIlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0Y2FsZW5kYXJfX3ZpZXdfX2NlbGxfaGVpZ2h0OiBcIlwiXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRjYWxlbmRhcl9fdmlld19fbW9udGhzX2luX3JvdzogNFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0Y2FsZW5kYXJfX3ZpZXdfX3Zpc2libGVfbW9udGhzOiAxMlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0Y2FsZW5kYXJfX3ZpZXdfX3dpZHRoOiBcIjEwMCVcIlxyXG5cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdGRhdGVzX2F2YWlsYWJpbGl0eTogXCJ1bmF2YWlsYWJsZVwiXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRkYXRlc19zZWxlY3Rpb246IFwiMjAyMy0wMy0xNCB+IDIwMjMtMDMtMTZcIlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0ZG9fYWN0aW9uOiBcInNldF9hdmFpbGFiaWxpdHlcIlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0cmVzb3VyY2VfaWQ6IDFcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdHVpX2NsaWNrZWRfZWxlbWVudF9pZDogXCJ3cGJjX2F2YWlsYWJpbGl0eV9hcHBseV9idG5cIlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0dWlfdXNyX19hdmFpbGFiaWxpdHlfc2VsZWN0ZWRfdG9vbGJhcjogXCJpbmZvXCJcclxuXHRcdFx0XHRcdFx0XHRcdCAgXHRcdCB9XHJcblx0XHRcdH1cclxuKi9cclxuZnVuY3Rpb24gd3BiY19hanhfYXZhaWxhYmlsaXR5X19jYWxlbmRhcl9fc2hvdyggY2FsZW5kYXJfcGFyYW1zX2FyciApe1xyXG5cclxuXHQvLyBVcGRhdGUgbm9uY2VcclxuXHRqUXVlcnkoICcjYWp4X25vbmNlX2NhbGVuZGFyX3NlY3Rpb24nICkuaHRtbCggY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfbm9uY2VfY2FsZW5kYXIgKTtcclxuXHJcblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBVcGRhdGUgYm9va2luZ3NcclxuXHRpZiAoICd1bmRlZmluZWQnID09IHR5cGVvZiAod3BiY19hanhfYm9va2luZ3NbIGNhbGVuZGFyX3BhcmFtc19hcnIucmVzb3VyY2VfaWQgXSkgKXsgd3BiY19hanhfYm9va2luZ3NbIGNhbGVuZGFyX3BhcmFtc19hcnIucmVzb3VyY2VfaWQgXSA9IFtdOyB9XHJcblx0d3BiY19hanhfYm9va2luZ3NbIGNhbGVuZGFyX3BhcmFtc19hcnIucmVzb3VyY2VfaWQgXSA9IGNhbGVuZGFyX3BhcmFtc19hcnJbICdhanhfZGF0YV9hcnInIF1bICdib29rZWRfZGF0ZXMnIF07XHJcblxyXG5cclxuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8qKlxyXG5cdCAqIERlZmluZSBzaG93aW5nIG1vdXNlIG92ZXIgdG9vbHRpcCBvbiB1bmF2YWlsYWJsZSBkYXRlc1xyXG5cdCAqIEl0J3MgZGVmaW5lZCwgd2hlbiBjYWxlbmRhciBSRUZSRVNIRUQgKGNoYW5nZSBtb250aHMgb3IgZGF5cyBzZWxlY3Rpb24pIGxvYWRlZCBpbiBqcXVlcnkuZGF0ZXBpY2sud3BiYy45LjAuanMgOlxyXG5cdCAqIFx0XHQkKCAnYm9keScgKS50cmlnZ2VyKCAnd3BiY19kYXRlcGlja19pbmxpbmVfY2FsZW5kYXJfcmVmcmVzaCcsIC4uLlx0XHQvLyBGaXhJbjogOS40LjQuMTMuXHJcblx0ICovXHJcblx0alF1ZXJ5KCAnYm9keScgKS5vbiggJ3dwYmNfZGF0ZXBpY2tfaW5saW5lX2NhbGVuZGFyX3JlZnJlc2gnLCBmdW5jdGlvbiAoIGV2ZW50LCByZXNvdXJjZV9pZCwgaW5zdCApe1xyXG5cdFx0Ly8gaW5zdC5kcERpdiAgaXQnczogIDxkaXYgY2xhc3M9XCJkYXRlcGljay1pbmxpbmUgZGF0ZXBpY2stbXVsdGlcIiBzdHlsZT1cIndpZHRoOiAxNzcxMnB4O1wiPi4uLi48L2Rpdj5cclxuXHRcdGluc3QuZHBEaXYuZmluZCggJy5zZWFzb25fdW5hdmFpbGFibGUsLmJlZm9yZV9hZnRlcl91bmF2YWlsYWJsZSwud2Vla2RheXNfdW5hdmFpbGFibGUnICkub24oICdtb3VzZW92ZXInLCBmdW5jdGlvbiAoIHRoaXNfZXZlbnQgKXtcclxuXHRcdFx0Ly8gYWxzbyBhdmFpbGFibGUgdGhlc2UgdmFyczogXHRyZXNvdXJjZV9pZCwgakNhbENvbnRhaW5lciwgaW5zdFxyXG5cdFx0XHR2YXIgakNlbGwgPSBqUXVlcnkoIHRoaXNfZXZlbnQuY3VycmVudFRhcmdldCApO1xyXG5cdFx0XHR3cGJjX2F2eV9fc2hvd190b29sdGlwX19mb3JfZWxlbWVudCggakNlbGwsIGNhbGVuZGFyX3BhcmFtc19hcnJbICdhanhfZGF0YV9hcnInIF1bJ3BvcG92ZXJfaGludHMnXSApO1xyXG5cdFx0fSk7XHJcblxyXG5cdH1cdCk7XHJcblxyXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0LyoqXHJcblx0ICogRGVmaW5lIGhlaWdodCBvZiB0aGUgY2FsZW5kYXIgIGNlbGxzLCBcdGFuZCAgbW91c2Ugb3ZlciB0b29sdGlwcyBhdCAgc29tZSB1bmF2YWlsYWJsZSBkYXRlc1xyXG5cdCAqIEl0J3MgZGVmaW5lZCwgd2hlbiBjYWxlbmRhciBsb2FkZWQgaW4ganF1ZXJ5LmRhdGVwaWNrLndwYmMuOS4wLmpzIDpcclxuXHQgKiBcdFx0JCggJ2JvZHknICkudHJpZ2dlciggJ3dwYmNfZGF0ZXBpY2tfaW5saW5lX2NhbGVuZGFyX2xvYWRlZCcsIC4uLlx0XHQvLyBGaXhJbjogOS40LjQuMTIuXHJcblx0ICovXHJcblx0alF1ZXJ5KCAnYm9keScgKS5vbiggJ3dwYmNfZGF0ZXBpY2tfaW5saW5lX2NhbGVuZGFyX2xvYWRlZCcsIGZ1bmN0aW9uICggZXZlbnQsIHJlc291cmNlX2lkLCBqQ2FsQ29udGFpbmVyLCBpbnN0ICl7XHJcblxyXG5cdFx0Ly8gUmVtb3ZlIGhpZ2hsaWdodCBkYXkgZm9yIHRvZGF5ICBkYXRlXHJcblx0XHRqUXVlcnkoICcuZGF0ZXBpY2stZGF5cy1jZWxsLmRhdGVwaWNrLXRvZGF5LmRhdGVwaWNrLWRheXMtY2VsbC1vdmVyJyApLnJlbW92ZUNsYXNzKCAnZGF0ZXBpY2stZGF5cy1jZWxsLW92ZXInICk7XHJcblxyXG5cdFx0Ly8gU2V0IGhlaWdodCBvZiBjYWxlbmRhciAgY2VsbHMgaWYgZGVmaW5lZCB0aGlzIG9wdGlvbiAgLy8gRml4SW46IDEwLjEyLjQuMi5cclxuXHRcdGlmICggJycgIT09IGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2NsZWFuZWRfcGFyYW1zLmNhbGVuZGFyX192aWV3X19jZWxsX2hlaWdodCApe1xyXG5cdFx0XHRqUXVlcnkoICdoZWFkJyApLmFwcGVuZCggJzxzdHlsZSB0eXBlPVwidGV4dC9jc3NcIj4nXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0KyAnLmhhc0RhdGVwaWNrIC5kYXRlcGljay1pbmxpbmUgLmRhdGVwaWNrLXRpdGxlLXJvdyB0aCwgJ1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdCsgJy5oYXNEYXRlcGljayAuZGF0ZXBpY2staW5saW5lIC5kYXRlcGljay1kYXlzLWNlbGwgeydcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCsgJ21heC1oZWlnaHQ6ICcgKyBjYWxlbmRhcl9wYXJhbXNfYXJyLmFqeF9jbGVhbmVkX3BhcmFtcy5jYWxlbmRhcl9fdmlld19fY2VsbF9oZWlnaHQgKyAnICFpbXBvcnRhbnQ7J1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdCsgJ30nXHJcblx0XHRcdFx0XHRcdFx0XHRcdCsnPC9zdHlsZT4nICk7XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gRGVmaW5lIHNob3dpbmcgbW91c2Ugb3ZlciB0b29sdGlwIG9uIHVuYXZhaWxhYmxlIGRhdGVzXHJcblx0XHRqQ2FsQ29udGFpbmVyLmZpbmQoICcuc2Vhc29uX3VuYXZhaWxhYmxlLC5iZWZvcmVfYWZ0ZXJfdW5hdmFpbGFibGUsLndlZWtkYXlzX3VuYXZhaWxhYmxlJyApLm9uKCAnbW91c2VvdmVyJywgZnVuY3Rpb24gKCB0aGlzX2V2ZW50ICl7XHJcblx0XHRcdC8vIGFsc28gYXZhaWxhYmxlIHRoZXNlIHZhcnM6IFx0cmVzb3VyY2VfaWQsIGpDYWxDb250YWluZXIsIGluc3RcclxuXHRcdFx0dmFyIGpDZWxsID0galF1ZXJ5KCB0aGlzX2V2ZW50LmN1cnJlbnRUYXJnZXQgKTtcclxuXHRcdFx0d3BiY19hdnlfX3Nob3dfdG9vbHRpcF9fZm9yX2VsZW1lbnQoIGpDZWxsLCBjYWxlbmRhcl9wYXJhbXNfYXJyWyAnYWp4X2RhdGFfYXJyJyBdWydwb3BvdmVyX2hpbnRzJ10gKTtcclxuXHRcdH0pO1xyXG5cdH0gKTtcclxuXHJcblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBEZWZpbmUgd2lkdGggb2YgZW50aXJlIGNhbGVuZGFyXHJcblx0dmFyIHdpZHRoID0gICAnd2lkdGg6J1x0XHQrICAgY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfY2xlYW5lZF9wYXJhbXMuY2FsZW5kYXJfX3ZpZXdfX3dpZHRoICsgJzsnO1x0XHRcdFx0XHQvLyB2YXIgd2lkdGggPSAnd2lkdGg6MTAwJTttYXgtd2lkdGg6MTAwJTsnO1xyXG5cclxuXHRpZiAoICAgKCB1bmRlZmluZWQgIT0gY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfY2xlYW5lZF9wYXJhbXMuY2FsZW5kYXJfX3ZpZXdfX21heF93aWR0aCApXHJcblx0XHQmJiAoICcnICE9IGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2NsZWFuZWRfcGFyYW1zLmNhbGVuZGFyX192aWV3X19tYXhfd2lkdGggKVxyXG5cdCl7XHJcblx0XHR3aWR0aCArPSAnbWF4LXdpZHRoOicgXHQrIGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2NsZWFuZWRfcGFyYW1zLmNhbGVuZGFyX192aWV3X19tYXhfd2lkdGggKyAnOyc7XHJcblx0fSBlbHNlIHtcclxuXHRcdHdpZHRoICs9ICdtYXgtd2lkdGg6JyBcdCsgKCBjYWxlbmRhcl9wYXJhbXNfYXJyLmFqeF9jbGVhbmVkX3BhcmFtcy5jYWxlbmRhcl9fdmlld19fbW9udGhzX2luX3JvdyAqIDM0MSApICsgJ3B4Oyc7XHJcblx0fVxyXG5cclxuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIEFkZCBjYWxlbmRhciBjb250YWluZXI6IFwiQ2FsZW5kYXIgaXMgbG9hZGluZy4uLlwiICBhbmQgdGV4dGFyZWFcclxuXHRqUXVlcnkoICcud3BiY19hanhfYXZ5X19jYWxlbmRhcicgKS5odG1sKFxyXG5cclxuXHRcdCc8ZGl2IGNsYXNzPVwiJ1x0KyAnIGJrX2NhbGVuZGFyX2ZyYW1lJ1xyXG5cdFx0XHRcdFx0XHQrICcgbW9udGhzX251bV9pbl9yb3dfJyArIGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2NsZWFuZWRfcGFyYW1zLmNhbGVuZGFyX192aWV3X19tb250aHNfaW5fcm93XHJcblx0XHRcdFx0XHRcdCsgJyBjYWxfbW9udGhfbnVtXycgXHQrIGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2NsZWFuZWRfcGFyYW1zLmNhbGVuZGFyX192aWV3X192aXNpYmxlX21vbnRoc1xyXG5cdFx0XHRcdFx0XHQrICcgJyBcdFx0XHRcdFx0KyBjYWxlbmRhcl9wYXJhbXNfYXJyLmFqeF9jbGVhbmVkX3BhcmFtcy5jYWxlbmRhcl9fdGltZXNsb3RfZGF5X2JnX2FzX2F2YWlsYWJsZSBcdFx0XHRcdC8vICd3cGJjX3RpbWVzbG90X2RheV9iZ19hc19hdmFpbGFibGUnIHx8ICcnXHJcblx0XHRcdFx0KyAnXCIgJ1xyXG5cdFx0XHQrICdzdHlsZT1cIicgKyB3aWR0aCArICdcIj4nXHJcblxyXG5cdFx0XHRcdCsgJzxkaXYgaWQ9XCJjYWxlbmRhcl9ib29raW5nJyArIGNhbGVuZGFyX3BhcmFtc19hcnIucmVzb3VyY2VfaWQgKyAnXCI+JyArICdDYWxlbmRhciBpcyBsb2FkaW5nLi4uJyArICc8L2Rpdj4nXHJcblxyXG5cdFx0KyAnPC9kaXY+J1xyXG5cclxuXHRcdCsgJzx0ZXh0YXJlYSAgICAgIGlkPVwiZGF0ZV9ib29raW5nJyArIGNhbGVuZGFyX3BhcmFtc19hcnIucmVzb3VyY2VfaWQgKyAnXCInXHJcblx0XHRcdFx0XHQrICcgbmFtZT1cImRhdGVfYm9va2luZycgKyBjYWxlbmRhcl9wYXJhbXNfYXJyLnJlc291cmNlX2lkICsgJ1wiJ1xyXG5cdFx0XHRcdFx0KyAnIGF1dG9jb21wbGV0ZT1cIm9mZlwiJ1xyXG5cdFx0XHRcdFx0KyAnIHN0eWxlPVwiZGlzcGxheTpub25lO3dpZHRoOjEwMCU7aGVpZ2h0OjEwZW07bWFyZ2luOjJlbSAwIDA7XCI+PC90ZXh0YXJlYT4nXHJcblx0KTtcclxuXHJcblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHR2YXIgY2FsX3BhcmFtX2FyciA9IHtcclxuXHRcdFx0XHRcdFx0XHQnaHRtbF9pZCcgICAgICAgICAgIDogJ2NhbGVuZGFyX2Jvb2tpbmcnICsgY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfY2xlYW5lZF9wYXJhbXMucmVzb3VyY2VfaWQsXHJcblx0XHRcdFx0XHRcdFx0J3RleHRfaWQnICAgICAgICAgICA6ICdkYXRlX2Jvb2tpbmcnICsgY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfY2xlYW5lZF9wYXJhbXMucmVzb3VyY2VfaWQsXHJcblxyXG5cdFx0XHRcdFx0XHRcdCdjYWxlbmRhcl9fc3RhcnRfd2Vla19kYXknOiBcdCAgY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfY2xlYW5lZF9wYXJhbXMuY2FsZW5kYXJfX3N0YXJ0X3dlZWtfZGF5LFxyXG5cdFx0XHRcdFx0XHRcdCdjYWxlbmRhcl9fdmlld19fdmlzaWJsZV9tb250aHMnOiBjYWxlbmRhcl9wYXJhbXNfYXJyLmFqeF9jbGVhbmVkX3BhcmFtcy5jYWxlbmRhcl9fdmlld19fdmlzaWJsZV9tb250aHMsXHJcblx0XHRcdFx0XHRcdFx0J2NhbGVuZGFyX19kYXlzX3NlbGVjdGlvbl9tb2RlJzogIGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2NsZWFuZWRfcGFyYW1zLmNhbGVuZGFyX19kYXlzX3NlbGVjdGlvbl9tb2RlLFxyXG5cclxuXHRcdFx0XHRcdFx0XHQncmVzb3VyY2VfaWQnICAgICAgICA6IGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2NsZWFuZWRfcGFyYW1zLnJlc291cmNlX2lkLFxyXG5cdFx0XHRcdFx0XHRcdCdhanhfbm9uY2VfY2FsZW5kYXInIDogY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfZGF0YV9hcnIuYWp4X25vbmNlX2NhbGVuZGFyLFxyXG5cdFx0XHRcdFx0XHRcdCdib29rZWRfZGF0ZXMnICAgICAgIDogY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfZGF0YV9hcnIuYm9va2VkX2RhdGVzLFxyXG5cdFx0XHRcdFx0XHRcdCdzZWFzb25fYXZhaWxhYmlsaXR5JzogY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfZGF0YV9hcnIuc2Vhc29uX2F2YWlsYWJpbGl0eSxcclxuXHJcblx0XHRcdFx0XHRcdFx0J3Jlc291cmNlX3VuYXZhaWxhYmxlX2RhdGVzJyA6IGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2RhdGFfYXJyLnJlc291cmNlX3VuYXZhaWxhYmxlX2RhdGVzLFxyXG5cclxuXHRcdFx0XHRcdFx0XHQncG9wb3Zlcl9oaW50cyc6IGNhbGVuZGFyX3BhcmFtc19hcnJbICdhanhfZGF0YV9hcnInIF1bJ3BvcG92ZXJfaGludHMnXVx0XHQvLyB7J3NlYXNvbl91bmF2YWlsYWJsZSc6Jy4uLicsJ3dlZWtkYXlzX3VuYXZhaWxhYmxlJzonLi4uJywnYmVmb3JlX2FmdGVyX3VuYXZhaWxhYmxlJzonLi4uJyx9XHJcblx0XHRcdFx0XHRcdH07XHJcblx0d3BiY19zaG93X2lubGluZV9ib29raW5nX2NhbGVuZGFyKCBjYWxfcGFyYW1fYXJyICk7XHJcblxyXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0LyoqXHJcblx0ICogT24gY2xpY2sgQVZBSUxBQkxFIHwgIFVOQVZBSUxBQkxFIGJ1dHRvbiAgaW4gd2lkZ2V0XHQtXHRuZWVkIHRvICBjaGFuZ2UgaGVscCBkYXRlcyB0ZXh0XHJcblx0ICovXHJcblx0alF1ZXJ5KCAnLndwYmNfcmFkaW9fX3NldF9kYXlzX2F2YWlsYWJpbGl0eScgKS5vbignY2hhbmdlJywgZnVuY3Rpb24gKCBldmVudCwgcmVzb3VyY2VfaWQsIGluc3QgKXtcclxuXHRcdHdwYmNfX2lubGluZV9ib29raW5nX2NhbGVuZGFyX19vbl9kYXlzX3NlbGVjdCggalF1ZXJ5KCAnIycgKyBjYWxfcGFyYW1fYXJyLnRleHRfaWQgKS52YWwoKSAsIGNhbF9wYXJhbV9hcnIgKTtcclxuXHR9KTtcclxuXHJcblx0Ly8gU2hvdyBcdCdTZWxlY3QgZGF5cyAgaW4gY2FsZW5kYXIgdGhlbiBzZWxlY3QgQXZhaWxhYmxlICAvICBVbmF2YWlsYWJsZSBzdGF0dXMgYW5kIGNsaWNrIEFwcGx5IGF2YWlsYWJpbGl0eSBidXR0b24uJ1xyXG5cdGpRdWVyeSggJyN3cGJjX3Rvb2xiYXJfZGF0ZXNfaGludCcpLmh0bWwoICAgICAnPGRpdiBjbGFzcz1cInVpX2VsZW1lbnRcIj48c3BhbiBjbGFzcz1cIndwYmNfdWlfY29udHJvbCB3cGJjX3VpX2FkZG9uIHdwYmNfaGVscF90ZXh0XCIgPidcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQrIGNhbF9wYXJhbV9hcnIucG9wb3Zlcl9oaW50cy50b29sYmFyX3RleHRcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0KyAnPC9zcGFuPjwvZGl2PidcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCk7XHJcbn1cclxuXHJcblxyXG4vKipcclxuICogXHRMb2FkIERhdGVwaWNrIElubGluZSBjYWxlbmRhclxyXG4gKlxyXG4gKiBAcGFyYW0gY2FsZW5kYXJfcGFyYW1zX2Fyclx0XHRleGFtcGxlOntcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdodG1sX2lkJyAgICAgICAgICAgOiAnY2FsZW5kYXJfYm9va2luZycgKyBjYWxlbmRhcl9wYXJhbXNfYXJyLmFqeF9jbGVhbmVkX3BhcmFtcy5yZXNvdXJjZV9pZCxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCd0ZXh0X2lkJyAgICAgICAgICAgOiAnZGF0ZV9ib29raW5nJyArIGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2NsZWFuZWRfcGFyYW1zLnJlc291cmNlX2lkLFxyXG5cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdjYWxlbmRhcl9fc3RhcnRfd2Vla19kYXknOiBcdCAgY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfY2xlYW5lZF9wYXJhbXMuY2FsZW5kYXJfX3N0YXJ0X3dlZWtfZGF5LFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2NhbGVuZGFyX192aWV3X192aXNpYmxlX21vbnRocyc6IGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2NsZWFuZWRfcGFyYW1zLmNhbGVuZGFyX192aWV3X192aXNpYmxlX21vbnRocyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdjYWxlbmRhcl9fZGF5c19zZWxlY3Rpb25fbW9kZSc6ICBjYWxlbmRhcl9wYXJhbXNfYXJyLmFqeF9jbGVhbmVkX3BhcmFtcy5jYWxlbmRhcl9fZGF5c19zZWxlY3Rpb25fbW9kZSxcclxuXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQncmVzb3VyY2VfaWQnICAgICAgICA6IGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2NsZWFuZWRfcGFyYW1zLnJlc291cmNlX2lkLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J2FqeF9ub25jZV9jYWxlbmRhcicgOiBjYWxlbmRhcl9wYXJhbXNfYXJyLmFqeF9kYXRhX2Fyci5hanhfbm9uY2VfY2FsZW5kYXIsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnYm9va2VkX2RhdGVzJyAgICAgICA6IGNhbGVuZGFyX3BhcmFtc19hcnIuYWp4X2RhdGFfYXJyLmJvb2tlZF9kYXRlcyxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdzZWFzb25fYXZhaWxhYmlsaXR5JzogY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfZGF0YV9hcnIuc2Vhc29uX2F2YWlsYWJpbGl0eSxcclxuXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQncmVzb3VyY2VfdW5hdmFpbGFibGVfZGF0ZXMnIDogY2FsZW5kYXJfcGFyYW1zX2Fyci5hanhfZGF0YV9hcnIucmVzb3VyY2VfdW5hdmFpbGFibGVfZGF0ZXNcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcbiAqIEByZXR1cm5zIHtib29sZWFufVxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19zaG93X2lubGluZV9ib29raW5nX2NhbGVuZGFyKCBjYWxlbmRhcl9wYXJhbXNfYXJyICl7XHJcblxyXG5cdGlmIChcclxuXHRcdCAgICggMCA9PT0galF1ZXJ5KCAnIycgKyBjYWxlbmRhcl9wYXJhbXNfYXJyLmh0bWxfaWQgKS5sZW5ndGggKVx0XHRcdFx0XHRcdFx0Ly8gSWYgY2FsZW5kYXIgRE9NIGVsZW1lbnQgbm90IGV4aXN0IHRoZW4gZXhpc3RcclxuXHRcdHx8ICggdHJ1ZSA9PT0galF1ZXJ5KCAnIycgKyBjYWxlbmRhcl9wYXJhbXNfYXJyLmh0bWxfaWQgKS5oYXNDbGFzcyggJ2hhc0RhdGVwaWNrJyApIClcdC8vIElmIHRoZSBjYWxlbmRhciB3aXRoIHRoZSBzYW1lIEJvb2tpbmcgcmVzb3VyY2UgYWxyZWFkeSAgaGFzIGJlZW4gYWN0aXZhdGVkLCB0aGVuIGV4aXN0LlxyXG5cdCl7XHJcblx0ICAgcmV0dXJuIGZhbHNlO1xyXG5cdH1cclxuXHJcblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBDb25maWd1cmUgYW5kIHNob3cgY2FsZW5kYXJcclxuXHRqUXVlcnkoICcjJyArIGNhbGVuZGFyX3BhcmFtc19hcnIuaHRtbF9pZCApLnRleHQoICcnICk7XHJcblx0alF1ZXJ5KCAnIycgKyBjYWxlbmRhcl9wYXJhbXNfYXJyLmh0bWxfaWQgKS5kYXRlcGljayh7XHJcblx0XHRcdFx0XHRiZWZvcmVTaG93RGF5OiBcdGZ1bmN0aW9uICggZGF0ZSApe1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdHJldHVybiB3cGJjX19pbmxpbmVfYm9va2luZ19jYWxlbmRhcl9fYXBwbHlfY3NzX3RvX2RheXMoIGRhdGUsIGNhbGVuZGFyX3BhcmFtc19hcnIsIHRoaXMgKTtcclxuXHRcdFx0XHRcdFx0XHRcdFx0fSxcclxuICAgICAgICAgICAgICAgICAgICBvblNlbGVjdDogXHQgIFx0ZnVuY3Rpb24gKCBkYXRlICl7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0alF1ZXJ5KCAnIycgKyBjYWxlbmRhcl9wYXJhbXNfYXJyLnRleHRfaWQgKS52YWwoIGRhdGUgKTtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHQvL3dwYmNfYmxpbmtfZWxlbWVudCgnLndwYmNfd2lkZ2V0X2F2YWlsYWJsZV91bmF2YWlsYWJsZScsIDMsIDIyMCk7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0cmV0dXJuIHdwYmNfX2lubGluZV9ib29raW5nX2NhbGVuZGFyX19vbl9kYXlzX3NlbGVjdCggZGF0ZSwgY2FsZW5kYXJfcGFyYW1zX2FyciwgdGhpcyApO1xyXG5cdFx0XHRcdFx0XHRcdFx0XHR9LFxyXG4gICAgICAgICAgICAgICAgICAgIG9uSG92ZXI6IFx0XHRmdW5jdGlvbiAoIHZhbHVlLCBkYXRlICl7XHJcblxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdC8vd3BiY19hdnlfX3ByZXBhcmVfdG9vbHRpcF9faW5fY2FsZW5kYXIoIHZhbHVlLCBkYXRlLCBjYWxlbmRhcl9wYXJhbXNfYXJyLCB0aGlzICk7XHJcblxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdHJldHVybiB3cGJjX19pbmxpbmVfYm9va2luZ19jYWxlbmRhcl9fb25fZGF5c19ob3ZlciggdmFsdWUsIGRhdGUsIGNhbGVuZGFyX3BhcmFtc19hcnIsIHRoaXMgKTtcclxuXHRcdFx0XHRcdFx0XHRcdFx0fSxcclxuICAgICAgICAgICAgICAgICAgICBvbkNoYW5nZU1vbnRoWWVhcjpcdG51bGwsXHJcbiAgICAgICAgICAgICAgICAgICAgc2hvd09uOiBcdFx0XHQnYm90aCcsXHJcbiAgICAgICAgICAgICAgICAgICAgbnVtYmVyT2ZNb250aHM6IFx0Y2FsZW5kYXJfcGFyYW1zX2Fyci5jYWxlbmRhcl9fdmlld19fdmlzaWJsZV9tb250aHMsXHJcbiAgICAgICAgICAgICAgICAgICAgc3RlcE1vbnRoczpcdFx0XHQxLFxyXG4gICAgICAgICAgICAgICAgICAgIC8vIHByZXZUZXh0OiBcdFx0XHQnJmxhcXVvOycsXHJcbiAgICAgICAgICAgICAgICAgICAgLy8gbmV4dFRleHQ6IFx0XHRcdCcmcmFxdW87JyxcclxuXHRcdFx0XHRcdHByZXZUZXh0ICAgICAgOiAnJmxzYXF1bzsnLFxyXG5cdFx0XHRcdFx0bmV4dFRleHQgICAgICA6ICcmcnNhcXVvOycsXHJcbiAgICAgICAgICAgICAgICAgICAgZGF0ZUZvcm1hdDogXHRcdCd5eS1tbS1kZCcsLy8gJ2RkLm1tLnl5JyxcclxuICAgICAgICAgICAgICAgICAgICBjaGFuZ2VNb250aDogXHRcdGZhbHNlLFxyXG4gICAgICAgICAgICAgICAgICAgIGNoYW5nZVllYXI6IFx0XHRmYWxzZSxcclxuICAgICAgICAgICAgICAgICAgICBtaW5EYXRlOiBcdFx0XHRcdFx0IDAsXHRcdC8vbnVsbCwgIC8vU2Nyb2xsIGFzIGxvbmcgYXMgeW91IG5lZWRcclxuXHRcdFx0XHRcdG1heERhdGU6IFx0XHRcdFx0XHQnMTB5JyxcdC8vIG1pbkRhdGU6IG5ldyBEYXRlKDIwMjAsIDIsIDEpLCBtYXhEYXRlOiBuZXcgRGF0ZSgyMDIwLCA5LCAzMSksIFx0Ly8gQWJpbGl0eSB0byBzZXQgYW55ICBzdGFydCBhbmQgZW5kIGRhdGUgaW4gY2FsZW5kYXJcclxuICAgICAgICAgICAgICAgICAgICBzaG93U3RhdHVzOiBcdFx0ZmFsc2UsXHJcbiAgICAgICAgICAgICAgICAgICAgY2xvc2VBdFRvcDogXHRcdGZhbHNlLFxyXG4gICAgICAgICAgICAgICAgICAgIGZpcnN0RGF5Olx0XHRcdGNhbGVuZGFyX3BhcmFtc19hcnIuY2FsZW5kYXJfX3N0YXJ0X3dlZWtfZGF5LFxyXG4gICAgICAgICAgICAgICAgICAgIGdvdG9DdXJyZW50OiBcdFx0ZmFsc2UsXHJcbiAgICAgICAgICAgICAgICAgICAgaGlkZUlmTm9QcmV2TmV4dDpcdHRydWUsXHJcbiAgICAgICAgICAgICAgICAgICAgbXVsdGlTZXBhcmF0b3I6IFx0JywgJyxcclxuXHRcdFx0XHRcdG11bHRpU2VsZWN0OiAoKCdkeW5hbWljJyA9PSBjYWxlbmRhcl9wYXJhbXNfYXJyLmNhbGVuZGFyX19kYXlzX3NlbGVjdGlvbl9tb2RlKSA/IDAgOiAzNjUpLFx0XHRcdC8vIE1heGltdW0gbnVtYmVyIG9mIHNlbGVjdGFibGUgZGF0ZXM6XHQgU2luZ2xlIGRheSA9IDAsICBtdWx0aSBkYXlzID0gMzY1XHJcblx0XHRcdFx0XHRyYW5nZVNlbGVjdDogICgnZHluYW1pYycgPT0gY2FsZW5kYXJfcGFyYW1zX2Fyci5jYWxlbmRhcl9fZGF5c19zZWxlY3Rpb25fbW9kZSksXHJcblx0XHRcdFx0XHRyYW5nZVNlcGFyYXRvcjogXHQnIH4gJyxcdFx0XHRcdFx0Ly8nIC0gJyxcclxuICAgICAgICAgICAgICAgICAgICAvLyBzaG93V2Vla3M6IHRydWUsXHJcbiAgICAgICAgICAgICAgICAgICAgdXNlVGhlbWVSb2xsZXI6XHRcdGZhbHNlXHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgKTtcclxuXHJcblx0cmV0dXJuICB0cnVlO1xyXG59XHJcblxyXG5cclxuXHQvKipcclxuXHQgKiBBcHBseSBDU1MgdG8gY2FsZW5kYXIgZGF0ZSBjZWxsc1xyXG5cdCAqXHJcblx0ICogQHBhcmFtIGRhdGVcdFx0XHRcdFx0LSAgSmF2YVNjcmlwdCBEYXRlIE9iajogIFx0XHRNb24gRGVjIDExIDIwMjMgMDA6MDA6MDAgR01UKzAyMDAgKEVhc3Rlcm4gRXVyb3BlYW4gU3RhbmRhcmQgVGltZSlcclxuXHQgKiBAcGFyYW0gY2FsZW5kYXJfcGFyYW1zX2Fyclx0LSAgQ2FsZW5kYXIgU2V0dGluZ3MgT2JqZWN0OiAgXHR7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIFwiaHRtbF9pZFwiOiBcImNhbGVuZGFyX2Jvb2tpbmc0XCIsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIFwidGV4dF9pZFwiOiBcImRhdGVfYm9va2luZzRcIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJjYWxlbmRhcl9fc3RhcnRfd2Vla19kYXlcIjogMSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJjYWxlbmRhcl9fdmlld19fdmlzaWJsZV9tb250aHNcIjogMTIsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIFwicmVzb3VyY2VfaWRcIjogNCxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJhanhfbm9uY2VfY2FsZW5kYXJcIjogXCI8aW5wdXQgdHlwZT1cXFwiaGlkZGVuXFxcIiAuLi4gLz5cIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJib29rZWRfZGF0ZXNcIjoge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcIjEyLTI4LTIwMjJcIjogW1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcImJvb2tpbmdfZGF0ZVwiOiBcIjIwMjItMTItMjggMDA6MDA6MDBcIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcImFwcHJvdmVkXCI6IFwiMVwiLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFwiYm9va2luZ19pZFwiOiBcIjI2XCJcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICB9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdF0sIC4uLlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdzZWFzb25fYXZhaWxhYmlsaXR5Jzp7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XCIyMDIzLTAxLTA5XCI6IHRydWUsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XCIyMDIzLTAxLTEwXCI6IHRydWUsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XCIyMDIzLTAxLTExXCI6IHRydWUsIC4uLlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIH1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH1cclxuXHQgKiBAcGFyYW0gZGF0ZXBpY2tfdGhpc1x0XHRcdC0gdGhpcyBvZiBkYXRlcGljayBPYmpcclxuXHQgKlxyXG5cdCAqIEByZXR1cm5zIFtib29sZWFuLHN0cmluZ11cdC0gWyB7dHJ1ZSAtYXZhaWxhYmxlIHwgZmFsc2UgLSB1bmF2YWlsYWJsZX0sICdDU1MgY2xhc3NlcyBmb3IgY2FsZW5kYXIgZGF5IGNlbGwnIF1cclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX19pbmxpbmVfYm9va2luZ19jYWxlbmRhcl9fYXBwbHlfY3NzX3RvX2RheXMoIGRhdGUsIGNhbGVuZGFyX3BhcmFtc19hcnIsIGRhdGVwaWNrX3RoaXMgKXtcclxuXHJcblx0XHR2YXIgdG9kYXlfZGF0ZSA9IG5ldyBEYXRlKCBfd3BiYy5nZXRfb3RoZXJfcGFyYW0oICd0b2RheV9hcnInIClbIDAgXSwgKHBhcnNlSW50KCBfd3BiYy5nZXRfb3RoZXJfcGFyYW0oICd0b2RheV9hcnInIClbIDEgXSApIC0gMSksIF93cGJjLmdldF9vdGhlcl9wYXJhbSggJ3RvZGF5X2FycicgKVsgMiBdLCAwLCAwLCAwICk7XHJcblx0XHR2YXIgcmVhbF90b2RheV9kYXRlID0gbmV3IERhdGUoIF93cGJjLmdldF9vdGhlcl9wYXJhbSggJ3RpbWVfbG9jYWxfYXJyJyApWyAwIF0sIChwYXJzZUludCggX3dwYmMuZ2V0X290aGVyX3BhcmFtKCAndGltZV9sb2NhbF9hcnInIClbIDEgXSApIC0gMSksIF93cGJjLmdldF9vdGhlcl9wYXJhbSggJ3RpbWVfbG9jYWxfYXJyJyApWyAyIF0sIDAsIDAsIDAgKTtcclxuXHJcblx0XHR2YXIgY2xhc3NfZGF5ICA9ICggZGF0ZS5nZXRNb250aCgpICsgMSApICsgJy0nICsgZGF0ZS5nZXREYXRlKCkgKyAnLScgKyBkYXRlLmdldEZ1bGxZZWFyKCk7XHRcdFx0XHRcdFx0Ly8gJzEtOS0yMDIzJ1xyXG5cdFx0dmFyIHNxbF9jbGFzc19kYXkgPSB3cGJjX19nZXRfX3NxbF9jbGFzc19kYXRlKCBkYXRlICk7XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyAnMjAyMy0wMS0wOSdcclxuXHJcblx0XHR2YXIgY3NzX2RhdGVfX3N0YW5kYXJkICAgPSAgJ2NhbDRkYXRlLScgKyBjbGFzc19kYXk7XHJcblx0XHR2YXIgY3NzX2RhdGVfX2FkZGl0aW9uYWwgPSAnIHdwYmNfd2Vla2RheV8nICsgZGF0ZS5nZXREYXkoKSArICcgJztcclxuXHJcblx0XHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblxyXG5cdFx0Ly8gV0VFS0RBWVMgOjogU2V0IHVuYXZhaWxhYmxlIHdlZWsgZGF5cyBmcm9tIC0gU2V0dGluZ3MgR2VuZXJhbCBwYWdlIGluIFwiQXZhaWxhYmlsaXR5XCIgc2VjdGlvblxyXG5cdFx0Zm9yICggdmFyIGkgPSAwOyBpIDwgX3dwYmMuZ2V0X290aGVyX3BhcmFtKCAnYXZhaWxhYmlsaXR5X193ZWVrX2RheXNfdW5hdmFpbGFibGUnICkubGVuZ3RoOyBpKysgKXtcclxuXHRcdFx0aWYgKCBkYXRlLmdldERheSgpID09IF93cGJjLmdldF9vdGhlcl9wYXJhbSggJ2F2YWlsYWJpbGl0eV9fd2Vla19kYXlzX3VuYXZhaWxhYmxlJyApWyBpIF0gKSB7XHJcblx0XHRcdFx0cmV0dXJuIFsgISFmYWxzZSwgY3NzX2RhdGVfX3N0YW5kYXJkICsgJyBkYXRlX3VzZXJfdW5hdmFpbGFibGUnIFx0KyAnIHdlZWtkYXlzX3VuYXZhaWxhYmxlJyBdO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblx0XHQvLyAxMC45LjYuMy5cclxuXHRcdHZhciBkYXRlX21pZG5pZ2h0ID0gbmV3IERhdGUoIHBhcnNlSW50KCBkYXRlLmdldEZ1bGxZZWFyKCkgKSwgKHBhcnNlSW50KCBkYXRlLmdldE1vbnRoKCkgKSAtIDApLCBwYXJzZUludCggZGF0ZS5nZXREYXRlKCkgKSwgMCwgMCwgMCApO1xyXG5cdFx0Ly8gQkVGT1JFX0FGVEVSIDo6IFNldCB1bmF2YWlsYWJsZSBkYXlzIEJlZm9yZSAvIEFmdGVyIHRoZSBUb2RheSBkYXRlLlxyXG5cdFx0Ly9pZiAoICgod3BiY19kYXRlc19fZGF5c19iZXR3ZWVuKCBkYXRlLCByZWFsX3RvZGF5X2RhdGUgKSkgPCBwYXJzZUludCggX3dwYmMuZ2V0X290aGVyX3BhcmFtKCAnYXZhaWxhYmlsaXR5X191bmF2YWlsYWJsZV9mcm9tX3RvZGF5JyApICkpXHJcblx0XHRpZiAoXHJcblx0XHRcdCggKHRvZGF5X2RhdGUuZ2V0VGltZSgpIC0gZGF0ZV9taWRuaWdodC5nZXRUaW1lKCkgKSA+IDAgKVxyXG5cdFx0XHR8fCAoXHJcblx0XHRcdFx0KHBhcnNlSW50KCAnMCcgKyBwYXJzZUludCggX3dwYmMuZ2V0X290aGVyX3BhcmFtKCAnYXZhaWxhYmlsaXR5X19hdmFpbGFibGVfZnJvbV90b2RheScgKSApICkgPiAwKVxyXG5cdFx0XHRcdCYmICh3cGJjX2RhdGVzX19kYXlzX2JldHdlZW4oIGRhdGVfbWlkbmlnaHQsIHJlYWxfdG9kYXlfZGF0ZSApID49IHBhcnNlSW50KCAnMCcgKyBwYXJzZUludCggX3dwYmMuZ2V0X290aGVyX3BhcmFtKCAnYXZhaWxhYmlsaXR5X19hdmFpbGFibGVfZnJvbV90b2RheScgKSApICkpXHJcblx0XHRcdClcclxuXHRcdCkge1xyXG5cdFx0XHRyZXR1cm4gWyBmYWxzZSwgY3NzX2RhdGVfX3N0YW5kYXJkICsgJyBkYXRlX3VzZXJfdW5hdmFpbGFibGUnICsgJyBiZWZvcmVfYWZ0ZXJfdW5hdmFpbGFibGUnIF07XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gU0VBU09OUyA6OiAgXHRcdFx0XHRcdEJvb2tpbmcgPiBSZXNvdXJjZXMgPiBBdmFpbGFiaWxpdHkgcGFnZVxyXG5cdFx0dmFyICAgIGlzX2RhdGVfYXZhaWxhYmxlID0gY2FsZW5kYXJfcGFyYW1zX2Fyci5zZWFzb25fYXZhaWxhYmlsaXR5WyBzcWxfY2xhc3NfZGF5IF07XHJcblx0XHRpZiAoIGZhbHNlID09PSBpc19kYXRlX2F2YWlsYWJsZSApe1x0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBGaXhJbjogOS41LjQuNC5cclxuXHRcdFx0cmV0dXJuIFsgISFmYWxzZSwgY3NzX2RhdGVfX3N0YW5kYXJkICsgJyBkYXRlX3VzZXJfdW5hdmFpbGFibGUnXHRcdCsgJyBzZWFzb25fdW5hdmFpbGFibGUnIF07XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gUkVTT1VSQ0VfVU5BVkFJTEFCTEUgOjogICBcdEJvb2tpbmcgPiBBdmFpbGFiaWxpdHkgcGFnZVxyXG5cdFx0aWYgKCB3cGJjX2luX2FycmF5KGNhbGVuZGFyX3BhcmFtc19hcnIucmVzb3VyY2VfdW5hdmFpbGFibGVfZGF0ZXMsIHNxbF9jbGFzc19kYXkgKSApe1xyXG5cdFx0XHRpc19kYXRlX2F2YWlsYWJsZSA9IGZhbHNlO1xyXG5cdFx0fVxyXG5cdFx0aWYgKCAgZmFsc2UgPT09IGlzX2RhdGVfYXZhaWxhYmxlICl7XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLyBGaXhJbjogOS41LjQuNC5cclxuXHRcdFx0cmV0dXJuIFsgIWZhbHNlLCBjc3NfZGF0ZV9fc3RhbmRhcmQgKyAnIGRhdGVfdXNlcl91bmF2YWlsYWJsZSdcdFx0KyAnIHJlc291cmNlX3VuYXZhaWxhYmxlJyBdO1xyXG5cdFx0fVxyXG5cclxuXHRcdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHJcblx0XHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblxyXG5cclxuXHRcdC8vIElzIGFueSBib29raW5ncyBpbiB0aGlzIGRhdGUgP1xyXG5cdFx0aWYgKCAndW5kZWZpbmVkJyAhPT0gdHlwZW9mKCBjYWxlbmRhcl9wYXJhbXNfYXJyLmJvb2tlZF9kYXRlc1sgY2xhc3NfZGF5IF0gKSApIHtcclxuXHJcblx0XHRcdHZhciBib29raW5nc19pbl9kYXRlID0gY2FsZW5kYXJfcGFyYW1zX2Fyci5ib29rZWRfZGF0ZXNbIGNsYXNzX2RheSBdO1xyXG5cclxuXHJcblx0XHRcdGlmICggJ3VuZGVmaW5lZCcgIT09IHR5cGVvZiggYm9va2luZ3NfaW5fZGF0ZVsgJ3NlY18wJyBdICkgKSB7XHRcdFx0Ly8gXCJGdWxsIGRheVwiIGJvb2tpbmcgIC0+IChzZWNvbmRzID09IDApXHJcblxyXG5cdFx0XHRcdGNzc19kYXRlX19hZGRpdGlvbmFsICs9ICggJzAnID09PSBib29raW5nc19pbl9kYXRlWyAnc2VjXzAnIF0uYXBwcm92ZWQgKSA/ICcgZGF0ZTJhcHByb3ZlICcgOiAnIGRhdGVfYXBwcm92ZWQgJztcdFx0XHRcdC8vIFBlbmRpbmcgPSAnMCcgfCAgQXBwcm92ZWQgPSAnMSdcclxuXHRcdFx0XHRjc3NfZGF0ZV9fYWRkaXRpb25hbCArPSAnIGZ1bGxfZGF5X2Jvb2tpbmcnO1xyXG5cclxuXHRcdFx0XHRyZXR1cm4gWyAhZmFsc2UsIGNzc19kYXRlX19zdGFuZGFyZCArIGNzc19kYXRlX19hZGRpdGlvbmFsIF07XHJcblxyXG5cdFx0XHR9IGVsc2UgaWYgKCBPYmplY3Qua2V5cyggYm9va2luZ3NfaW5fZGF0ZSApLmxlbmd0aCA+IDAgKXtcdFx0XHRcdC8vIFwiVGltZSBzbG90c1wiIEJvb2tpbmdzXHJcblxyXG5cdFx0XHRcdHZhciBpc19hcHByb3ZlZCA9IHRydWU7XHJcblxyXG5cdFx0XHRcdF8uZWFjaCggYm9va2luZ3NfaW5fZGF0ZSwgZnVuY3Rpb24gKCBwX3ZhbCwgcF9rZXksIHBfZGF0YSApIHtcclxuXHRcdFx0XHRcdGlmICggIXBhcnNlSW50KCBwX3ZhbC5hcHByb3ZlZCApICl7XHJcblx0XHRcdFx0XHRcdGlzX2FwcHJvdmVkID0gZmFsc2U7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHR2YXIgdHMgPSBwX3ZhbC5ib29raW5nX2RhdGUuc3Vic3RyaW5nKCBwX3ZhbC5ib29raW5nX2RhdGUubGVuZ3RoIC0gMSApO1xyXG5cdFx0XHRcdFx0aWYgKCB0cnVlID09PSBfd3BiYy5nZXRfb3RoZXJfcGFyYW0oICdpc19lbmFibGVkX2NoYW5nZV9vdmVyJyApICl7XHJcblx0XHRcdFx0XHRcdGlmICggdHMgPT0gJzEnICkgeyBjc3NfZGF0ZV9fYWRkaXRpb25hbCArPSAnIGNoZWNrX2luX3RpbWUnICsgKChwYXJzZUludChwX3ZhbC5hcHByb3ZlZCkpID8gJyBjaGVja19pbl90aW1lX2RhdGVfYXBwcm92ZWQnIDogJyBjaGVja19pbl90aW1lX2RhdGUyYXBwcm92ZScpOyB9XHJcblx0XHRcdFx0XHRcdGlmICggdHMgPT0gJzInICkgeyBjc3NfZGF0ZV9fYWRkaXRpb25hbCArPSAnIGNoZWNrX291dF90aW1lJyArICgocGFyc2VJbnQocF92YWwuYXBwcm92ZWQpKSA/ICcgY2hlY2tfb3V0X3RpbWVfZGF0ZV9hcHByb3ZlZCcgOiAnIGNoZWNrX291dF90aW1lX2RhdGUyYXBwcm92ZScpOyB9XHJcblx0XHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdH0pO1xyXG5cclxuXHRcdFx0XHRpZiAoICEgaXNfYXBwcm92ZWQgKXtcclxuXHRcdFx0XHRcdGNzc19kYXRlX19hZGRpdGlvbmFsICs9ICcgZGF0ZTJhcHByb3ZlIHRpbWVzcGFydGx5J1xyXG5cdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRjc3NfZGF0ZV9fYWRkaXRpb25hbCArPSAnIGRhdGVfYXBwcm92ZWQgdGltZXNwYXJ0bHknXHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRpZiAoICEgX3dwYmMuZ2V0X290aGVyX3BhcmFtKCAnaXNfZW5hYmxlZF9jaGFuZ2Vfb3ZlcicgKSApe1xyXG5cdFx0XHRcdFx0Y3NzX2RhdGVfX2FkZGl0aW9uYWwgKz0gJyB0aW1lc19jbG9jaydcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHR9XHJcblxyXG5cdFx0fVxyXG5cclxuXHRcdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHJcblx0XHRyZXR1cm4gWyB0cnVlLCBjc3NfZGF0ZV9fc3RhbmRhcmQgKyBjc3NfZGF0ZV9fYWRkaXRpb25hbCArICcgZGF0ZV9hdmFpbGFibGUnIF07XHJcblx0fVxyXG5cclxuXHJcblx0LyoqXHJcblx0ICogQXBwbHkgc29tZSBDU1MgY2xhc3Nlcywgd2hlbiB3ZSBtb3VzZSBvdmVyIHNwZWNpZmljIGRhdGVzIGluIGNhbGVuZGFyXHJcblx0ICogQHBhcmFtIHZhbHVlXHJcblx0ICogQHBhcmFtIGRhdGVcdFx0XHRcdFx0LSAgSmF2YVNjcmlwdCBEYXRlIE9iajogIFx0XHRNb24gRGVjIDExIDIwMjMgMDA6MDA6MDAgR01UKzAyMDAgKEVhc3Rlcm4gRXVyb3BlYW4gU3RhbmRhcmQgVGltZSlcclxuXHQgKiBAcGFyYW0gY2FsZW5kYXJfcGFyYW1zX2Fyclx0LSAgQ2FsZW5kYXIgU2V0dGluZ3MgT2JqZWN0OiAgXHR7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIFwiaHRtbF9pZFwiOiBcImNhbGVuZGFyX2Jvb2tpbmc0XCIsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIFwidGV4dF9pZFwiOiBcImRhdGVfYm9va2luZzRcIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJjYWxlbmRhcl9fc3RhcnRfd2Vla19kYXlcIjogMSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJjYWxlbmRhcl9fdmlld19fdmlzaWJsZV9tb250aHNcIjogMTIsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIFwicmVzb3VyY2VfaWRcIjogNCxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJhanhfbm9uY2VfY2FsZW5kYXJcIjogXCI8aW5wdXQgdHlwZT1cXFwiaGlkZGVuXFxcIiAuLi4gLz5cIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJib29rZWRfZGF0ZXNcIjoge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcIjEyLTI4LTIwMjJcIjogW1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcImJvb2tpbmdfZGF0ZVwiOiBcIjIwMjItMTItMjggMDA6MDA6MDBcIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcImFwcHJvdmVkXCI6IFwiMVwiLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFwiYm9va2luZ19pZFwiOiBcIjI2XCJcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICB9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdF0sIC4uLlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdzZWFzb25fYXZhaWxhYmlsaXR5Jzp7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XCIyMDIzLTAxLTA5XCI6IHRydWUsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XCIyMDIzLTAxLTEwXCI6IHRydWUsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XCIyMDIzLTAxLTExXCI6IHRydWUsIC4uLlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIH1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH1cclxuXHQgKiBAcGFyYW0gZGF0ZXBpY2tfdGhpc1x0XHRcdC0gdGhpcyBvZiBkYXRlcGljayBPYmpcclxuXHQgKlxyXG5cdCAqIEByZXR1cm5zIHtib29sZWFufVxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIHdwYmNfX2lubGluZV9ib29raW5nX2NhbGVuZGFyX19vbl9kYXlzX2hvdmVyKCB2YWx1ZSwgZGF0ZSwgY2FsZW5kYXJfcGFyYW1zX2FyciwgZGF0ZXBpY2tfdGhpcyApe1xyXG5cclxuXHRcdGlmICggbnVsbCA9PT0gZGF0ZSApe1xyXG5cdFx0XHRqUXVlcnkoICcuZGF0ZXBpY2stZGF5cy1jZWxsLW92ZXInICkucmVtb3ZlQ2xhc3MoICdkYXRlcGljay1kYXlzLWNlbGwtb3ZlcicgKTsgICBcdCAgICAgICAgICAgICAgICAgICAgICAgIC8vIGNsZWFyIGFsbCBoaWdobGlnaHQgZGF5cyBzZWxlY3Rpb25zXHJcblx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdH1cclxuXHJcblx0XHR2YXIgaW5zdCA9IGpRdWVyeS5kYXRlcGljay5fZ2V0SW5zdCggZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoICdjYWxlbmRhcl9ib29raW5nJyArIGNhbGVuZGFyX3BhcmFtc19hcnIucmVzb3VyY2VfaWQgKSApO1xyXG5cclxuXHRcdGlmIChcclxuXHRcdFx0ICAgKCAxID09IGluc3QuZGF0ZXMubGVuZ3RoKVx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdC8vIElmIHdlIGhhdmUgb25lIHNlbGVjdGVkIGRhdGVcclxuXHRcdFx0JiYgKCdkeW5hbWljJyA9PT0gY2FsZW5kYXJfcGFyYW1zX2Fyci5jYWxlbmRhcl9fZGF5c19zZWxlY3Rpb25fbW9kZSkgXHRcdFx0XHRcdC8vIHdoaWxlIGhhdmUgcmFuZ2UgZGF5cyBzZWxlY3Rpb24gbW9kZVxyXG5cdFx0KXtcclxuXHJcblx0XHRcdHZhciB0ZF9jbGFzcztcclxuXHRcdFx0dmFyIHRkX292ZXJzID0gW107XHJcblx0XHRcdHZhciBpc19jaGVjayA9IHRydWU7XHJcbiAgICAgICAgICAgIHZhciBzZWxjZXRlZF9maXJzdF9kYXkgPSBuZXcgRGF0ZSgpO1xyXG4gICAgICAgICAgICBzZWxjZXRlZF9maXJzdF9kYXkuc2V0RnVsbFllYXIoaW5zdC5kYXRlc1swXS5nZXRGdWxsWWVhcigpLChpbnN0LmRhdGVzWzBdLmdldE1vbnRoKCkpLCAoaW5zdC5kYXRlc1swXS5nZXREYXRlKCkgKSApOyAvL0dldCBmaXJzdCBEYXRlXHJcblxyXG4gICAgICAgICAgICB3aGlsZSggIGlzX2NoZWNrICl7XHJcblxyXG5cdFx0XHRcdHRkX2NsYXNzID0gKHNlbGNldGVkX2ZpcnN0X2RheS5nZXRNb250aCgpICsgMSkgKyAnLScgKyBzZWxjZXRlZF9maXJzdF9kYXkuZ2V0RGF0ZSgpICsgJy0nICsgc2VsY2V0ZWRfZmlyc3RfZGF5LmdldEZ1bGxZZWFyKCk7XHJcblxyXG5cdFx0XHRcdHRkX292ZXJzWyB0ZF9vdmVycy5sZW5ndGggXSA9ICcjY2FsZW5kYXJfYm9va2luZycgKyBjYWxlbmRhcl9wYXJhbXNfYXJyLnJlc291cmNlX2lkICsgJyAuY2FsNGRhdGUtJyArIHRkX2NsYXNzOyAgICAgICAgICAgICAgLy8gYWRkIHRvIGFycmF5IGZvciBsYXRlciBtYWtlIHNlbGVjdGlvbiBieSBjbGFzc1xyXG5cclxuICAgICAgICAgICAgICAgIGlmIChcclxuXHRcdFx0XHRcdCggICggZGF0ZS5nZXRNb250aCgpID09IHNlbGNldGVkX2ZpcnN0X2RheS5nZXRNb250aCgpICkgICYmXHJcbiAgICAgICAgICAgICAgICAgICAgICAgKCBkYXRlLmdldERhdGUoKSA9PSBzZWxjZXRlZF9maXJzdF9kYXkuZ2V0RGF0ZSgpICkgICYmXHJcbiAgICAgICAgICAgICAgICAgICAgICAgKCBkYXRlLmdldEZ1bGxZZWFyKCkgPT0gc2VsY2V0ZWRfZmlyc3RfZGF5LmdldEZ1bGxZZWFyKCkgKVxyXG5cdFx0XHRcdFx0KSB8fCAoIHNlbGNldGVkX2ZpcnN0X2RheSA+IGRhdGUgKVxyXG5cdFx0XHRcdCl7XHJcblx0XHRcdFx0XHRpc19jaGVjayA9ICBmYWxzZTtcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHRcdHNlbGNldGVkX2ZpcnN0X2RheS5zZXRGdWxsWWVhciggc2VsY2V0ZWRfZmlyc3RfZGF5LmdldEZ1bGxZZWFyKCksIChzZWxjZXRlZF9maXJzdF9kYXkuZ2V0TW9udGgoKSksIChzZWxjZXRlZF9maXJzdF9kYXkuZ2V0RGF0ZSgpICsgMSkgKTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Ly8gSGlnaGxpZ2h0IERheXNcclxuXHRcdFx0Zm9yICggdmFyIGk9MDsgaSA8IHRkX292ZXJzLmxlbmd0aCA7IGkrKykgeyAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBhZGQgY2xhc3MgdG8gYWxsIGVsZW1lbnRzXHJcblx0XHRcdFx0alF1ZXJ5KCB0ZF9vdmVyc1tpXSApLmFkZENsYXNzKCdkYXRlcGljay1kYXlzLWNlbGwtb3ZlcicpO1xyXG5cdFx0XHR9XHJcblx0XHRcdHJldHVybiB0cnVlO1xyXG5cclxuXHRcdH1cclxuXHJcblx0ICAgIHJldHVybiB0cnVlO1xyXG5cdH1cclxuXHJcblxyXG5cdC8qKlxyXG5cdCAqIE9uIERBWXMgc2VsZWN0aW9uIGluIGNhbGVuZGFyXHJcblx0ICpcclxuXHQgKiBAcGFyYW0gZGF0ZXNfc2VsZWN0aW9uXHRcdC0gIHN0cmluZzpcdFx0XHQgJzIwMjMtMDMtMDcgfiAyMDIzLTAzLTA3JyBvciAnMjAyMy0wNC0xMCwgMjAyMy0wNC0xMiwgMjAyMy0wNC0wMiwgMjAyMy0wNC0wNCdcclxuXHQgKiBAcGFyYW0gY2FsZW5kYXJfcGFyYW1zX2Fyclx0LSAgQ2FsZW5kYXIgU2V0dGluZ3MgT2JqZWN0OiAgXHR7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIFwiaHRtbF9pZFwiOiBcImNhbGVuZGFyX2Jvb2tpbmc0XCIsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIFwidGV4dF9pZFwiOiBcImRhdGVfYm9va2luZzRcIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJjYWxlbmRhcl9fc3RhcnRfd2Vla19kYXlcIjogMSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJjYWxlbmRhcl9fdmlld19fdmlzaWJsZV9tb250aHNcIjogMTIsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIFwicmVzb3VyY2VfaWRcIjogNCxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJhanhfbm9uY2VfY2FsZW5kYXJcIjogXCI8aW5wdXQgdHlwZT1cXFwiaGlkZGVuXFxcIiAuLi4gLz5cIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgXCJib29rZWRfZGF0ZXNcIjoge1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcIjEyLTI4LTIwMjJcIjogW1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcImJvb2tpbmdfZGF0ZVwiOiBcIjIwMjItMTItMjggMDA6MDA6MDBcIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcImFwcHJvdmVkXCI6IFwiMVwiLFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFwiYm9va2luZ19pZFwiOiBcIjI2XCJcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ICB9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdF0sIC4uLlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdzZWFzb25fYXZhaWxhYmlsaXR5Jzp7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XCIyMDIzLTAxLTA5XCI6IHRydWUsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XCIyMDIzLTAxLTEwXCI6IHRydWUsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XCIyMDIzLTAxLTExXCI6IHRydWUsIC4uLlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQgIH1cclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH1cclxuXHQgKiBAcGFyYW0gZGF0ZXBpY2tfdGhpc1x0XHRcdC0gdGhpcyBvZiBkYXRlcGljayBPYmpcclxuXHQgKlxyXG5cdCAqIEByZXR1cm5zIGJvb2xlYW5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX19pbmxpbmVfYm9va2luZ19jYWxlbmRhcl9fb25fZGF5c19zZWxlY3QoIGRhdGVzX3NlbGVjdGlvbiwgY2FsZW5kYXJfcGFyYW1zX2FyciwgZGF0ZXBpY2tfdGhpcyA9IG51bGwgKXtcclxuXHJcblx0XHR2YXIgaW5zdCA9IGpRdWVyeS5kYXRlcGljay5fZ2V0SW5zdCggZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoICdjYWxlbmRhcl9ib29raW5nJyArIGNhbGVuZGFyX3BhcmFtc19hcnIucmVzb3VyY2VfaWQgKSApO1xyXG5cclxuXHRcdHZhciBkYXRlc19hcnIgPSBbXTtcdC8vICBbIFwiMjAyMy0wNC0wOVwiLCBcIjIwMjMtMDQtMTBcIiwgXCIyMDIzLTA0LTExXCIgXVxyXG5cclxuXHRcdGlmICggLTEgIT09IGRhdGVzX3NlbGVjdGlvbi5pbmRleE9mKCAnficgKSApIHsgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gUmFuZ2UgRGF5c1xyXG5cclxuXHRcdFx0ZGF0ZXNfYXJyID0gd3BiY19nZXRfZGF0ZXNfYXJyX19mcm9tX2RhdGVzX3JhbmdlX2pzKCB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnZGF0ZXNfc2VwYXJhdG9yJyA6ICcgfiAnLCAgICAgICAgICAgICAgICAgICAgICAgICAvLyAgJyB+ICdcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdkYXRlcycgICAgICAgICAgIDogZGF0ZXNfc2VsZWN0aW9uLCAgICBcdFx0ICAgLy8gJzIwMjMtMDQtMDQgfiAyMDIzLTA0LTA3J1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH0gKTtcclxuXHJcblx0XHR9IGVsc2UgeyAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gTXVsdGlwbGUgRGF5c1xyXG5cdFx0XHRkYXRlc19hcnIgPSB3cGJjX2dldF9kYXRlc19hcnJfX2Zyb21fZGF0ZXNfY29tbWFfc2VwYXJhdGVkX2pzKCB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnZGF0ZXNfc2VwYXJhdG9yJyA6ICcsICcsICAgICAgICAgICAgICAgICAgICAgICAgIFx0Ly8gICcsICdcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdkYXRlcycgICAgICAgICAgIDogZGF0ZXNfc2VsZWN0aW9uLCAgICBcdFx0XHQvLyAnMjAyMy0wNC0xMCwgMjAyMy0wNC0xMiwgMjAyMy0wNC0wMiwgMjAyMy0wNC0wNCdcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9ICk7XHJcblx0XHR9XHJcblxyXG5cdFx0d3BiY19hdnlfYWZ0ZXJfZGF5c19zZWxlY3Rpb25fX3Nob3dfaGVscF9pbmZvKHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdjYWxlbmRhcl9fZGF5c19zZWxlY3Rpb25fbW9kZSc6IGNhbGVuZGFyX3BhcmFtc19hcnIuY2FsZW5kYXJfX2RheXNfc2VsZWN0aW9uX21vZGUsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnZGF0ZXNfYXJyJyAgICAgICAgICAgICAgICAgICAgOiBkYXRlc19hcnIsXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnZGF0ZXNfY2xpY2tfbnVtJyAgICAgICAgICAgICAgOiBpbnN0LmRhdGVzLmxlbmd0aCxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdwb3BvdmVyX2hpbnRzJ1x0XHRcdFx0XHQ6IGNhbGVuZGFyX3BhcmFtc19hcnIucG9wb3Zlcl9oaW50c1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH0gKTtcclxuXHRcdHJldHVybiB0cnVlO1xyXG5cdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFNob3cgaGVscCBpbmZvIGF0IHRoZSB0b3AgIHRvb2xiYXIgYWJvdXQgc2VsZWN0ZWQgZGF0ZXMgYW5kIGZ1dHVyZSBhY3Rpb25zXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHBhcmFtc1xyXG5cdFx0ICogXHRcdFx0XHRcdEV4YW1wbGUgMTogIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdGNhbGVuZGFyX19kYXlzX3NlbGVjdGlvbl9tb2RlOiBcImR5bmFtaWNcIixcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdGRhdGVzX2FycjogIFsgXCIyMDIzLTA0LTAzXCIgXSxcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdGRhdGVzX2NsaWNrX251bTogMVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3BvcG92ZXJfaGludHMnXHRcdFx0XHRcdDogY2FsZW5kYXJfcGFyYW1zX2Fyci5wb3BvdmVyX2hpbnRzXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0fVxyXG5cdFx0ICogXHRcdFx0XHRcdEV4YW1wbGUgMjogIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdGNhbGVuZGFyX19kYXlzX3NlbGVjdGlvbl9tb2RlOiBcImR5bmFtaWNcIlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0ZGF0ZXNfYXJyOiBBcnJheSgxMCkgWyBcIjIwMjMtMDQtMDNcIiwgXCIyMDIzLTA0LTA0XCIsIFwiMjAyMy0wNC0wNVwiLCDigKYgXVxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0ZGF0ZXNfY2xpY2tfbnVtOiAyXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQncG9wb3Zlcl9oaW50cydcdFx0XHRcdFx0OiBjYWxlbmRhcl9wYXJhbXNfYXJyLnBvcG92ZXJfaGludHNcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHR9XHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIHdwYmNfYXZ5X2FmdGVyX2RheXNfc2VsZWN0aW9uX19zaG93X2hlbHBfaW5mbyggcGFyYW1zICl7XHJcbi8vIGNvbnNvbGUubG9nKCBwYXJhbXMgKTtcdC8vXHRcdFsgXCIyMDIzLTA0LTA5XCIsIFwiMjAyMy0wNC0xMFwiLCBcIjIwMjMtMDQtMTFcIiBdXHJcblxyXG5cdFx0XHR2YXIgbWVzc2FnZSwgY29sb3I7XHJcblx0XHRcdGlmIChqUXVlcnkoICcjdWlfYnRuX2F2eV9fc2V0X2RheXNfYXZhaWxhYmlsaXR5X19hdmFpbGFibGUnKS5pcygnOmNoZWNrZWQnKSl7XHJcblx0XHRcdFx0IG1lc3NhZ2UgPSBwYXJhbXMucG9wb3Zlcl9oaW50cy50b29sYmFyX3RleHRfYXZhaWxhYmxlOy8vJ1NldCBkYXRlcyBfREFURVNfIGFzIF9IVE1MXyBhdmFpbGFibGUuJztcclxuXHRcdFx0XHQgY29sb3IgPSAnIzExYmU0Yyc7XHJcblx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0bWVzc2FnZSA9IHBhcmFtcy5wb3BvdmVyX2hpbnRzLnRvb2xiYXJfdGV4dF91bmF2YWlsYWJsZTsvLydTZXQgZGF0ZXMgX0RBVEVTXyBhcyBfSFRNTF8gdW5hdmFpbGFibGUuJztcclxuXHRcdFx0XHRjb2xvciA9ICcjZTQzOTM5JztcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0bWVzc2FnZSA9ICc8c3Bhbj4nICsgbWVzc2FnZSArICc8L3NwYW4+JztcclxuXHJcblx0XHRcdHZhciBmaXJzdF9kYXRlID0gcGFyYW1zWyAnZGF0ZXNfYXJyJyBdWyAwIF07XHJcblx0XHRcdHZhciBsYXN0X2RhdGUgID0gKCAnZHluYW1pYycgPT0gcGFyYW1zLmNhbGVuZGFyX19kYXlzX3NlbGVjdGlvbl9tb2RlIClcclxuXHRcdFx0XHRcdFx0XHQ/IHBhcmFtc1sgJ2RhdGVzX2FycicgXVsgKHBhcmFtc1sgJ2RhdGVzX2FycicgXS5sZW5ndGggLSAxKSBdXHJcblx0XHRcdFx0XHRcdFx0OiAoIHBhcmFtc1sgJ2RhdGVzX2FycicgXS5sZW5ndGggPiAxICkgPyBwYXJhbXNbICdkYXRlc19hcnInIF1bIDEgXSA6ICcnO1xyXG5cclxuXHRcdFx0Zmlyc3RfZGF0ZSA9IGpRdWVyeS5kYXRlcGljay5mb3JtYXREYXRlKCAnZGQgTSwgeXknLCBuZXcgRGF0ZSggZmlyc3RfZGF0ZSArICdUMDA6MDA6MDAnICkgKTtcclxuXHRcdFx0bGFzdF9kYXRlID0galF1ZXJ5LmRhdGVwaWNrLmZvcm1hdERhdGUoICdkZCBNLCB5eScsICBuZXcgRGF0ZSggbGFzdF9kYXRlICsgJ1QwMDowMDowMCcgKSApO1xyXG5cclxuXHJcblx0XHRcdGlmICggJ2R5bmFtaWMnID09IHBhcmFtcy5jYWxlbmRhcl9fZGF5c19zZWxlY3Rpb25fbW9kZSApe1xyXG5cdFx0XHRcdGlmICggMSA9PSBwYXJhbXMuZGF0ZXNfY2xpY2tfbnVtICl7XHJcblx0XHRcdFx0XHRsYXN0X2RhdGUgPSAnX19fX19fX19fX18nXHJcblx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdGlmICggJ2ZpcnN0X3RpbWUnID09IGpRdWVyeSggJy53cGJjX2FqeF9hdmFpbGFiaWxpdHlfY29udGFpbmVyJyApLmF0dHIoICd3cGJjX2xvYWRlZCcgKSApe1xyXG5cdFx0XHRcdFx0XHRqUXVlcnkoICcud3BiY19hanhfYXZhaWxhYmlsaXR5X2NvbnRhaW5lcicgKS5hdHRyKCAnd3BiY19sb2FkZWQnLCAnZG9uZScgKVxyXG5cdFx0XHRcdFx0XHR3cGJjX2JsaW5rX2VsZW1lbnQoICcud3BiY193aWRnZXRfYXZhaWxhYmxlX3VuYXZhaWxhYmxlJywgMywgMjIwICk7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdG1lc3NhZ2UgPSBtZXNzYWdlLnJlcGxhY2UoICdfREFURVNfJywgICAgJzwvc3Bhbj4nXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLysgJzxkaXY+JyArICdmcm9tJyArICc8L2Rpdj4nXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQrICc8c3BhbiBjbGFzcz1cIndwYmNfYmlnX2RhdGVcIj4nICsgZmlyc3RfZGF0ZSArICc8L3NwYW4+J1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0KyAnPHNwYW4+JyArICctJyArICc8L3NwYW4+J1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0KyAnPHNwYW4gY2xhc3M9XCJ3cGJjX2JpZ19kYXRlXCI+JyArIGxhc3RfZGF0ZSArICc8L3NwYW4+J1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0KyAnPHNwYW4+JyApO1xyXG5cdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdC8vIGlmICggcGFyYW1zWyAnZGF0ZXNfYXJyJyBdLmxlbmd0aCA+IDEgKXtcclxuXHRcdFx0XHQvLyBcdGxhc3RfZGF0ZSA9ICcsICcgKyBsYXN0X2RhdGU7XHJcblx0XHRcdFx0Ly8gXHRsYXN0X2RhdGUgKz0gKCBwYXJhbXNbICdkYXRlc19hcnInIF0ubGVuZ3RoID4gMiApID8gJywgLi4uJyA6ICcnO1xyXG5cdFx0XHRcdC8vIH0gZWxzZSB7XHJcblx0XHRcdFx0Ly8gXHRsYXN0X2RhdGU9Jyc7XHJcblx0XHRcdFx0Ly8gfVxyXG5cdFx0XHRcdHZhciBkYXRlc19hcnIgPSBbXTtcclxuXHRcdFx0XHRmb3IoIHZhciBpID0gMDsgaSA8IHBhcmFtc1sgJ2RhdGVzX2FycicgXS5sZW5ndGg7IGkrKyApe1xyXG5cdFx0XHRcdFx0ZGF0ZXNfYXJyLnB1c2goICBqUXVlcnkuZGF0ZXBpY2suZm9ybWF0RGF0ZSggJ2RkIE0geXknLCAgbmV3IERhdGUoIHBhcmFtc1sgJ2RhdGVzX2FycicgXVsgaSBdICsgJ1QwMDowMDowMCcgKSApICApO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRmaXJzdF9kYXRlID0gZGF0ZXNfYXJyLmpvaW4oICcsICcgKTtcclxuXHRcdFx0XHRtZXNzYWdlID0gbWVzc2FnZS5yZXBsYWNlKCAnX0RBVEVTXycsICAgICc8L3NwYW4+J1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0KyAnPHNwYW4gY2xhc3M9XCJ3cGJjX2JpZ19kYXRlXCI+JyArIGZpcnN0X2RhdGUgKyAnPC9zcGFuPidcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCsgJzxzcGFuPicgKTtcclxuXHRcdFx0fVxyXG5cdFx0XHRtZXNzYWdlID0gbWVzc2FnZS5yZXBsYWNlKCAnX0hUTUxfJyAsICc8L3NwYW4+PHNwYW4gY2xhc3M9XCJ3cGJjX2JpZ190ZXh0XCIgc3R5bGU9XCJjb2xvcjonK2NvbG9yKyc7XCI+JykgKyAnPHNwYW4+JztcclxuXHJcblx0XHRcdC8vbWVzc2FnZSArPSAnIDxkaXYgc3R5bGU9XCJtYXJnaW4tbGVmdDogMWVtO1wiPicgKyAnIENsaWNrIG9uIEFwcGx5IGJ1dHRvbiB0byBhcHBseSBhdmFpbGFiaWxpdHkuJyArICc8L2Rpdj4nO1xyXG5cclxuXHRcdFx0bWVzc2FnZSA9ICc8ZGl2IGNsYXNzPVwid3BiY190b29sYmFyX2RhdGVzX2hpbnRzXCI+JyArIG1lc3NhZ2UgKyAnPC9kaXY+JztcclxuXHJcblx0XHRcdGpRdWVyeSggJy53cGJjX2hlbHBfdGV4dCcgKS5odG1sKFx0bWVzc2FnZSApO1xyXG5cdFx0fVxyXG5cclxuXHQvKipcclxuXHQgKiAgIFBhcnNlIGRhdGVzICAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tICovXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBHZXQgZGF0ZXMgYXJyYXksICBmcm9tIGNvbW1hIHNlcGFyYXRlZCBkYXRlc1xyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSBwYXJhbXMgICAgICAgPSB7XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQqICdkYXRlc19zZXBhcmF0b3InID0+ICcsICcsICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIERhdGVzIHNlcGFyYXRvclxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0KiAnZGF0ZXMnICAgICAgICAgICA9PiAnMjAyMy0wNC0wNCwgMjAyMy0wNC0wNywgMjAyMy0wNC0wNScgICAgICAgICAvLyBEYXRlcyBpbiAnWS1tLWQnIGZvcm1hdDogJzIwMjMtMDEtMzEnXHJcblx0XHRcdFx0XHRcdFx0XHQgfVxyXG5cdFx0ICpcclxuXHRcdCAqIEByZXR1cm4gYXJyYXkgICAgICA9IFtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCogWzBdID0+IDIwMjMtMDQtMDRcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCogWzFdID0+IDIwMjMtMDQtMDVcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCogWzJdID0+IDIwMjMtMDQtMDZcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCogWzNdID0+IDIwMjMtMDQtMDdcclxuXHRcdFx0XHRcdFx0XHRcdF1cclxuXHRcdCAqXHJcblx0XHQgKiBFeGFtcGxlICMxOiAgd3BiY19nZXRfZGF0ZXNfYXJyX19mcm9tX2RhdGVzX2NvbW1hX3NlcGFyYXRlZF9qcyggIHsgICdkYXRlc19zZXBhcmF0b3InIDogJywgJywgJ2RhdGVzJyA6ICcyMDIzLTA0LTA0LCAyMDIzLTA0LTA3LCAyMDIzLTA0LTA1JyAgfSAgKTtcclxuXHRcdCAqL1xyXG5cdFx0ZnVuY3Rpb24gd3BiY19nZXRfZGF0ZXNfYXJyX19mcm9tX2RhdGVzX2NvbW1hX3NlcGFyYXRlZF9qcyggcGFyYW1zICl7XHJcblxyXG5cdFx0XHR2YXIgZGF0ZXNfYXJyID0gW107XHJcblxyXG5cdFx0XHRpZiAoICcnICE9PSBwYXJhbXNbICdkYXRlcycgXSApe1xyXG5cclxuXHRcdFx0XHRkYXRlc19hcnIgPSBwYXJhbXNbICdkYXRlcycgXS5zcGxpdCggcGFyYW1zWyAnZGF0ZXNfc2VwYXJhdG9yJyBdICk7XHJcblxyXG5cdFx0XHRcdGRhdGVzX2Fyci5zb3J0KCk7XHJcblx0XHRcdH1cclxuXHRcdFx0cmV0dXJuIGRhdGVzX2FycjtcclxuXHRcdH1cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEdldCBkYXRlcyBhcnJheSwgIGZyb20gcmFuZ2UgZGF5cyBzZWxlY3Rpb25cclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0gcGFyYW1zICAgICAgID0gIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCogJ2RhdGVzX3NlcGFyYXRvcicgPT4gJyB+ICcsICAgICAgICAgICAgICAgICAgICAgICAgIC8vIERhdGVzIHNlcGFyYXRvclxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0KiAnZGF0ZXMnICAgICAgICAgICA9PiAnMjAyMy0wNC0wNCB+IDIwMjMtMDQtMDcnICAgICAgLy8gRGF0ZXMgaW4gJ1ktbS1kJyBmb3JtYXQ6ICcyMDIzLTAxLTMxJ1xyXG5cdFx0XHRcdFx0XHRcdFx0ICB9XHJcblx0XHQgKlxyXG5cdFx0ICogQHJldHVybiBhcnJheSAgICAgICAgPSBbXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQqIFswXSA9PiAyMDIzLTA0LTA0XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQqIFsxXSA9PiAyMDIzLTA0LTA1XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQqIFsyXSA9PiAyMDIzLTA0LTA2XHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQqIFszXSA9PiAyMDIzLTA0LTA3XHJcblx0XHRcdFx0XHRcdFx0XHQgIF1cclxuXHRcdCAqXHJcblx0XHQgKiBFeGFtcGxlICMxOiAgd3BiY19nZXRfZGF0ZXNfYXJyX19mcm9tX2RhdGVzX3JhbmdlX2pzKCAgeyAgJ2RhdGVzX3NlcGFyYXRvcicgOiAnIH4gJywgJ2RhdGVzJyA6ICcyMDIzLTA0LTA0IH4gMjAyMy0wNC0wNycgIH0gICk7XHJcblx0XHQgKiBFeGFtcGxlICMyOiAgd3BiY19nZXRfZGF0ZXNfYXJyX19mcm9tX2RhdGVzX3JhbmdlX2pzKCAgeyAgJ2RhdGVzX3NlcGFyYXRvcicgOiAnIC0gJywgJ2RhdGVzJyA6ICcyMDIzLTA0LTA0IC0gMjAyMy0wNC0wNycgIH0gICk7XHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIHdwYmNfZ2V0X2RhdGVzX2Fycl9fZnJvbV9kYXRlc19yYW5nZV9qcyggcGFyYW1zICl7XHJcblxyXG5cdFx0XHR2YXIgZGF0ZXNfYXJyID0gW107XHJcblxyXG5cdFx0XHRpZiAoICcnICE9PSBwYXJhbXNbJ2RhdGVzJ10gKSB7XHJcblxyXG5cdFx0XHRcdGRhdGVzX2FyciA9IHBhcmFtc1sgJ2RhdGVzJyBdLnNwbGl0KCBwYXJhbXNbICdkYXRlc19zZXBhcmF0b3InIF0gKTtcclxuXHRcdFx0XHR2YXIgY2hlY2tfaW5fZGF0ZV95bWQgID0gZGF0ZXNfYXJyWzBdO1xyXG5cdFx0XHRcdHZhciBjaGVja19vdXRfZGF0ZV95bWQgPSBkYXRlc19hcnJbMV07XHJcblxyXG5cdFx0XHRcdGlmICggKCcnICE9PSBjaGVja19pbl9kYXRlX3ltZCkgJiYgKCcnICE9PSBjaGVja19vdXRfZGF0ZV95bWQpICl7XHJcblxyXG5cdFx0XHRcdFx0ZGF0ZXNfYXJyID0gd3BiY19nZXRfZGF0ZXNfYXJyYXlfZnJvbV9zdGFydF9lbmRfZGF5c19qcyggY2hlY2tfaW5fZGF0ZV95bWQsIGNoZWNrX291dF9kYXRlX3ltZCApO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fVxyXG5cdFx0XHRyZXR1cm4gZGF0ZXNfYXJyO1xyXG5cdFx0fVxyXG5cclxuXHRcdFx0LyoqXHJcblx0XHRcdCAqIEdldCBkYXRlcyBhcnJheSBiYXNlZCBvbiBzdGFydCBhbmQgZW5kIGRhdGVzLlxyXG5cdFx0XHQgKlxyXG5cdFx0XHQgKiBAcGFyYW0gc3RyaW5nIHNTdGFydERhdGUgLSBzdGFydCBkYXRlOiAyMDIzLTA0LTA5XHJcblx0XHRcdCAqIEBwYXJhbSBzdHJpbmcgc0VuZERhdGUgICAtIGVuZCBkYXRlOiAgIDIwMjMtMDQtMTFcclxuXHRcdFx0ICogQHJldHVybiBhcnJheSAgICAgICAgICAgICAtIFsgXCIyMDIzLTA0LTA5XCIsIFwiMjAyMy0wNC0xMFwiLCBcIjIwMjMtMDQtMTFcIiBdXHJcblx0XHRcdCAqL1xyXG5cdFx0XHRmdW5jdGlvbiB3cGJjX2dldF9kYXRlc19hcnJheV9mcm9tX3N0YXJ0X2VuZF9kYXlzX2pzKCBzU3RhcnREYXRlLCBzRW5kRGF0ZSApe1xyXG5cclxuXHRcdFx0XHRzU3RhcnREYXRlID0gbmV3IERhdGUoIHNTdGFydERhdGUgKyAnVDAwOjAwOjAwJyApO1xyXG5cdFx0XHRcdHNFbmREYXRlID0gbmV3IERhdGUoIHNFbmREYXRlICsgJ1QwMDowMDowMCcgKTtcclxuXHJcblx0XHRcdFx0dmFyIGFEYXlzPVtdO1xyXG5cclxuXHRcdFx0XHQvLyBTdGFydCB0aGUgdmFyaWFibGUgb2ZmIHdpdGggdGhlIHN0YXJ0IGRhdGVcclxuXHRcdFx0XHRhRGF5cy5wdXNoKCBzU3RhcnREYXRlLmdldFRpbWUoKSApO1xyXG5cclxuXHRcdFx0XHQvLyBTZXQgYSAndGVtcCcgdmFyaWFibGUsIHNDdXJyZW50RGF0ZSwgd2l0aCB0aGUgc3RhcnQgZGF0ZSAtIGJlZm9yZSBiZWdpbm5pbmcgdGhlIGxvb3BcclxuXHRcdFx0XHR2YXIgc0N1cnJlbnREYXRlID0gbmV3IERhdGUoIHNTdGFydERhdGUuZ2V0VGltZSgpICk7XHJcblx0XHRcdFx0dmFyIG9uZV9kYXlfZHVyYXRpb24gPSAyNCo2MCo2MCoxMDAwO1xyXG5cclxuXHRcdFx0XHQvLyBXaGlsZSB0aGUgY3VycmVudCBkYXRlIGlzIGxlc3MgdGhhbiB0aGUgZW5kIGRhdGVcclxuXHRcdFx0XHR3aGlsZShzQ3VycmVudERhdGUgPCBzRW5kRGF0ZSl7XHJcblx0XHRcdFx0XHQvLyBBZGQgYSBkYXkgdG8gdGhlIGN1cnJlbnQgZGF0ZSBcIisxIGRheVwiXHJcblx0XHRcdFx0XHRzQ3VycmVudERhdGUuc2V0VGltZSggc0N1cnJlbnREYXRlLmdldFRpbWUoKSArIG9uZV9kYXlfZHVyYXRpb24gKTtcclxuXHJcblx0XHRcdFx0XHQvLyBBZGQgdGhpcyBuZXcgZGF5IHRvIHRoZSBhRGF5cyBhcnJheVxyXG5cdFx0XHRcdFx0YURheXMucHVzaCggc0N1cnJlbnREYXRlLmdldFRpbWUoKSApO1xyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0Zm9yIChsZXQgaSA9IDA7IGkgPCBhRGF5cy5sZW5ndGg7IGkrKykge1xyXG5cdFx0XHRcdFx0YURheXNbIGkgXSA9IG5ldyBEYXRlKCBhRGF5c1tpXSApO1xyXG5cdFx0XHRcdFx0YURheXNbIGkgXSA9IGFEYXlzWyBpIF0uZ2V0RnVsbFllYXIoKVxyXG5cdFx0XHRcdFx0XHRcdFx0KyAnLScgKyAoKCAoYURheXNbIGkgXS5nZXRNb250aCgpICsgMSkgPCAxMCkgPyAnMCcgOiAnJykgKyAoYURheXNbIGkgXS5nZXRNb250aCgpICsgMSlcclxuXHRcdFx0XHRcdFx0XHRcdCsgJy0nICsgKCggICAgICAgIGFEYXlzWyBpIF0uZ2V0RGF0ZSgpIDwgMTApID8gJzAnIDogJycpICsgIGFEYXlzWyBpIF0uZ2V0RGF0ZSgpO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHQvLyBPbmNlIHRoZSBsb29wIGhhcyBmaW5pc2hlZCwgcmV0dXJuIHRoZSBhcnJheSBvZiBkYXlzLlxyXG5cdFx0XHRcdHJldHVybiBhRGF5cztcclxuXHRcdFx0fVxyXG5cclxuXHJcblxyXG5cdC8qKlxyXG5cdCAqICAgVG9vbHRpcHMgIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0gKi9cclxuXHJcblx0LyoqXHJcblx0ICogRGVmaW5lIHNob3dpbmcgdG9vbHRpcCwgIHdoZW4gIG1vdXNlIG92ZXIgb24gIFNFTEVDVEFCTEUgKGF2YWlsYWJsZSwgcGVuZGluZywgYXBwcm92ZWQsIHJlc291cmNlIHVuYXZhaWxhYmxlKSwgIGRheXNcclxuXHQgKiBDYW4gYmUgY2FsbGVkIGRpcmVjdGx5ICBmcm9tICBkYXRlcGljayBpbml0IGZ1bmN0aW9uLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHZhbHVlXHJcblx0ICogQHBhcmFtIGRhdGVcclxuXHQgKiBAcGFyYW0gY2FsZW5kYXJfcGFyYW1zX2FyclxyXG5cdCAqIEBwYXJhbSBkYXRlcGlja190aGlzXHJcblx0ICogQHJldHVybnMge2Jvb2xlYW59XHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19hdnlfX3ByZXBhcmVfdG9vbHRpcF9faW5fY2FsZW5kYXIoIHZhbHVlLCBkYXRlLCBjYWxlbmRhcl9wYXJhbXNfYXJyLCBkYXRlcGlja190aGlzICl7XHJcblxyXG5cdFx0aWYgKCBudWxsID09IGRhdGUgKXsgIHJldHVybiBmYWxzZTsgIH1cclxuXHJcblx0XHR2YXIgdGRfY2xhc3MgPSAoIGRhdGUuZ2V0TW9udGgoKSArIDEgKSArICctJyArIGRhdGUuZ2V0RGF0ZSgpICsgJy0nICsgZGF0ZS5nZXRGdWxsWWVhcigpO1xyXG5cclxuXHRcdHZhciBqQ2VsbCA9IGpRdWVyeSggJyNjYWxlbmRhcl9ib29raW5nJyArIGNhbGVuZGFyX3BhcmFtc19hcnIucmVzb3VyY2VfaWQgKyAnIHRkLmNhbDRkYXRlLScgKyB0ZF9jbGFzcyApO1xyXG5cclxuXHRcdHdwYmNfYXZ5X19zaG93X3Rvb2x0aXBfX2Zvcl9lbGVtZW50KCBqQ2VsbCwgY2FsZW5kYXJfcGFyYW1zX2FyclsgJ3BvcG92ZXJfaGludHMnIF0gKTtcclxuXHRcdHJldHVybiB0cnVlO1xyXG5cdH1cclxuXHJcblxyXG5cdC8qKlxyXG5cdCAqIERlZmluZSB0b29sdGlwICBmb3Igc2hvd2luZyBvbiBVTkFWQUlMQUJMRSBkYXlzIChzZWFzb24sIHdlZWtkYXksIHRvZGF5X2RlcGVuZHMgdW5hdmFpbGFibGUpXHJcblx0ICpcclxuXHQgKiBAcGFyYW0gakNlbGxcdFx0XHRcdFx0alF1ZXJ5IG9mIHNwZWNpZmljIGRheSBjZWxsXHJcblx0ICogQHBhcmFtIHBvcG92ZXJfaGludHNcdFx0ICAgIEFycmF5IHdpdGggdG9vbHRpcCBoaW50IHRleHRzXHQgOiB7J3NlYXNvbl91bmF2YWlsYWJsZSc6Jy4uLicsJ3dlZWtkYXlzX3VuYXZhaWxhYmxlJzonLi4uJywnYmVmb3JlX2FmdGVyX3VuYXZhaWxhYmxlJzonLi4uJyx9XHJcblx0ICovXHJcblx0ZnVuY3Rpb24gd3BiY19hdnlfX3Nob3dfdG9vbHRpcF9fZm9yX2VsZW1lbnQoIGpDZWxsLCBwb3BvdmVyX2hpbnRzICl7XHJcblxyXG5cdFx0dmFyIHRvb2x0aXBfdGltZSA9ICcnO1xyXG5cclxuXHRcdGlmICggakNlbGwuaGFzQ2xhc3MoICdzZWFzb25fdW5hdmFpbGFibGUnICkgKXtcclxuXHRcdFx0dG9vbHRpcF90aW1lID0gcG9wb3Zlcl9oaW50c1sgJ3NlYXNvbl91bmF2YWlsYWJsZScgXTtcclxuXHRcdH0gZWxzZSBpZiAoIGpDZWxsLmhhc0NsYXNzKCAnd2Vla2RheXNfdW5hdmFpbGFibGUnICkgKXtcclxuXHRcdFx0dG9vbHRpcF90aW1lID0gcG9wb3Zlcl9oaW50c1sgJ3dlZWtkYXlzX3VuYXZhaWxhYmxlJyBdO1xyXG5cdFx0fSBlbHNlIGlmICggakNlbGwuaGFzQ2xhc3MoICdiZWZvcmVfYWZ0ZXJfdW5hdmFpbGFibGUnICkgKXtcclxuXHRcdFx0dG9vbHRpcF90aW1lID0gcG9wb3Zlcl9oaW50c1sgJ2JlZm9yZV9hZnRlcl91bmF2YWlsYWJsZScgXTtcclxuXHRcdH0gZWxzZSBpZiAoIGpDZWxsLmhhc0NsYXNzKCAnZGF0ZTJhcHByb3ZlJyApICl7XHJcblxyXG5cdFx0fSBlbHNlIGlmICggakNlbGwuaGFzQ2xhc3MoICdkYXRlX2FwcHJvdmVkJyApICl7XHJcblxyXG5cdFx0fSBlbHNlIHtcclxuXHJcblx0XHR9XHJcblxyXG5cdFx0akNlbGwuYXR0ciggJ2RhdGEtY29udGVudCcsIHRvb2x0aXBfdGltZSApO1xyXG5cclxuXHRcdHZhciB0ZF9lbCA9IGpDZWxsLmdldCgwKTtcdC8valF1ZXJ5KCAnI2NhbGVuZGFyX2Jvb2tpbmcnICsgY2FsZW5kYXJfcGFyYW1zX2Fyci5yZXNvdXJjZV9pZCArICcgdGQuY2FsNGRhdGUtJyArIHRkX2NsYXNzICkuZ2V0KDApO1xyXG5cclxuXHRcdGlmICggKCB1bmRlZmluZWQgPT0gdGRfZWwuX3RpcHB5ICkgJiYgKCAnJyAhPSB0b29sdGlwX3RpbWUgKSApe1xyXG5cclxuXHRcdFx0XHR3cGJjX3RpcHB5KCB0ZF9lbCAsIHtcclxuXHRcdFx0XHRcdGNvbnRlbnQoIHJlZmVyZW5jZSApe1xyXG5cclxuXHRcdFx0XHRcdFx0dmFyIHBvcG92ZXJfY29udGVudCA9IHJlZmVyZW5jZS5nZXRBdHRyaWJ1dGUoICdkYXRhLWNvbnRlbnQnICk7XHJcblxyXG5cdFx0XHRcdFx0XHRyZXR1cm4gJzxkaXYgY2xhc3M9XCJwb3BvdmVyIHBvcG92ZXJfdGlwcHlcIj4nXHJcblx0XHRcdFx0XHRcdFx0XHRcdCsgJzxkaXYgY2xhc3M9XCJwb3BvdmVyLWNvbnRlbnRcIj4nXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0KyBwb3BvdmVyX2NvbnRlbnRcclxuXHRcdFx0XHRcdFx0XHRcdFx0KyAnPC9kaXY+J1xyXG5cdFx0XHRcdFx0XHRcdCArICc8L2Rpdj4nO1xyXG5cdFx0XHRcdFx0fSxcclxuXHRcdFx0XHRcdGFsbG93SFRNTCAgICAgICAgOiB0cnVlLFxyXG5cdFx0XHRcdFx0dHJpZ2dlclx0XHRcdCA6ICdtb3VzZWVudGVyIGZvY3VzJyxcclxuXHRcdFx0XHRcdGludGVyYWN0aXZlICAgICAgOiAhIHRydWUsXHJcblx0XHRcdFx0XHRoaWRlT25DbGljayAgICAgIDogdHJ1ZSxcclxuXHRcdFx0XHRcdGludGVyYWN0aXZlQm9yZGVyOiAxMCxcclxuXHRcdFx0XHRcdG1heFdpZHRoICAgICAgICAgOiA1NTAsXHJcblx0XHRcdFx0XHR0aGVtZSAgICAgICAgICAgIDogJ3dwYmMtdGlwcHktdGltZXMnLFxyXG5cdFx0XHRcdFx0cGxhY2VtZW50ICAgICAgICA6ICd0b3AnLFxyXG5cdFx0XHRcdFx0ZGVsYXlcdFx0XHQgOiBbNDAwLCAwXSxcdFx0XHQvLyBGaXhJbjogOS40LjIuMi5cclxuXHRcdFx0XHRcdGlnbm9yZUF0dHJpYnV0ZXMgOiB0cnVlLFxyXG5cdFx0XHRcdFx0dG91Y2hcdFx0XHQgOiB0cnVlLFx0XHRcdFx0Ly9bJ2hvbGQnLCA1MDBdLCAvLyA1MDBtcyBkZWxheVx0XHRcdC8vIEZpeEluOiA5LjIuMS41LlxyXG5cdFx0XHRcdFx0YXBwZW5kVG86ICgpID0+IGRvY3VtZW50LmJvZHksXHJcblx0XHRcdFx0fSk7XHJcblx0XHR9XHJcblx0fVxyXG5cclxuXHJcblxyXG5cclxuXHJcbi8qKlxyXG4gKiAgIEFqYXggIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLSAqL1xyXG5cclxuLyoqXHJcbiAqIFNlbmQgQWpheCBzaG93IHJlcXVlc3RcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfYWp4X2F2YWlsYWJpbGl0eV9fYWpheF9yZXF1ZXN0KCl7XHJcblxyXG5jb25zb2xlLmdyb3VwQ29sbGFwc2VkKCAnV1BCQ19BSlhfQVZBSUxBQklMSVRZJyApOyBjb25zb2xlLmxvZyggJyA9PSBCZWZvcmUgQWpheCBTZW5kIC0gc2VhcmNoX2dldF9hbGxfcGFyYW1zKCkgPT0gJyAsIHdwYmNfYWp4X2F2YWlsYWJpbGl0eS5zZWFyY2hfZ2V0X2FsbF9wYXJhbXMoKSApO1xyXG5cclxuXHR3cGJjX2F2YWlsYWJpbGl0eV9yZWxvYWRfYnV0dG9uX19zcGluX3N0YXJ0KCk7XHJcblxyXG5cdC8vIFN0YXJ0IEFqYXhcclxuXHRqUXVlcnkucG9zdCggd3BiY191cmxfYWpheCxcclxuXHRcdFx0XHR7XHJcblx0XHRcdFx0XHRhY3Rpb24gICAgICAgICAgOiAnV1BCQ19BSlhfQVZBSUxBQklMSVRZJyxcclxuXHRcdFx0XHRcdHdwYmNfYWp4X3VzZXJfaWQ6IHdwYmNfYWp4X2F2YWlsYWJpbGl0eS5nZXRfc2VjdXJlX3BhcmFtKCAndXNlcl9pZCcgKSxcclxuXHRcdFx0XHRcdG5vbmNlICAgICAgICAgICA6IHdwYmNfYWp4X2F2YWlsYWJpbGl0eS5nZXRfc2VjdXJlX3BhcmFtKCAnbm9uY2UnICksXHJcblx0XHRcdFx0XHR3cGJjX2FqeF9sb2NhbGUgOiB3cGJjX2FqeF9hdmFpbGFiaWxpdHkuZ2V0X3NlY3VyZV9wYXJhbSggJ2xvY2FsZScgKSxcclxuXHJcblx0XHRcdFx0XHRzZWFyY2hfcGFyYW1zXHQ6IHdwYmNfYWp4X2F2YWlsYWJpbGl0eS5zZWFyY2hfZ2V0X2FsbF9wYXJhbXMoKVxyXG5cdFx0XHRcdH0sXHJcblx0XHRcdFx0LyoqXHJcblx0XHRcdFx0ICogUyB1IGMgYyBlIHMgc1xyXG5cdFx0XHRcdCAqXHJcblx0XHRcdFx0ICogQHBhcmFtIHJlc3BvbnNlX2RhdGFcdFx0LVx0aXRzIG9iamVjdCByZXR1cm5lZCBmcm9tICBBamF4IC0gY2xhc3MtbGl2ZS1zZWFyY2cucGhwXHJcblx0XHRcdFx0ICogQHBhcmFtIHRleHRTdGF0dXNcdFx0LVx0J3N1Y2Nlc3MnXHJcblx0XHRcdFx0ICogQHBhcmFtIGpxWEhSXHRcdFx0XHQtXHRPYmplY3RcclxuXHRcdFx0XHQgKi9cclxuXHRcdFx0XHRmdW5jdGlvbiAoIHJlc3BvbnNlX2RhdGEsIHRleHRTdGF0dXMsIGpxWEhSICkge1xyXG5cclxuY29uc29sZS5sb2coICcgPT0gUmVzcG9uc2UgV1BCQ19BSlhfQVZBSUxBQklMSVRZID09ICcsIHJlc3BvbnNlX2RhdGEgKTsgY29uc29sZS5ncm91cEVuZCgpO1xyXG5cclxuXHRcdFx0XHRcdC8vIFByb2JhYmx5IEVycm9yXHJcblx0XHRcdFx0XHRpZiAoICh0eXBlb2YgcmVzcG9uc2VfZGF0YSAhPT0gJ29iamVjdCcpIHx8IChyZXNwb25zZV9kYXRhID09PSBudWxsKSApe1xyXG5cclxuXHRcdFx0XHRcdFx0d3BiY19hanhfYXZhaWxhYmlsaXR5X19zaG93X21lc3NhZ2UoIHJlc3BvbnNlX2RhdGEgKTtcclxuXHJcblx0XHRcdFx0XHRcdHJldHVybjtcclxuXHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHQvLyBSZWxvYWQgcGFnZSwgYWZ0ZXIgZmlsdGVyIHRvb2xiYXIgaGFzIGJlZW4gcmVzZXRcclxuXHRcdFx0XHRcdGlmICggICAgICAgKCAgICAgdW5kZWZpbmVkICE9IHJlc3BvbnNlX2RhdGFbICdhanhfY2xlYW5lZF9wYXJhbXMnIF0pXHJcblx0XHRcdFx0XHRcdFx0JiYgKCAncmVzZXRfZG9uZScgPT09IHJlc3BvbnNlX2RhdGFbICdhanhfY2xlYW5lZF9wYXJhbXMnIF1bICdkb19hY3Rpb24nIF0pXHJcblx0XHRcdFx0XHQpe1xyXG5cdFx0XHRcdFx0XHRsb2NhdGlvbi5yZWxvYWQoKTtcclxuXHRcdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHRcdC8vIFNob3cgbGlzdGluZ1xyXG5cdFx0XHRcdFx0d3BiY19hanhfYXZhaWxhYmlsaXR5X19wYWdlX2NvbnRlbnRfX3Nob3coIHJlc3BvbnNlX2RhdGFbICdhanhfZGF0YScgXSwgcmVzcG9uc2VfZGF0YVsgJ2FqeF9zZWFyY2hfcGFyYW1zJyBdICwgcmVzcG9uc2VfZGF0YVsgJ2FqeF9jbGVhbmVkX3BhcmFtcycgXSApO1xyXG5cclxuXHRcdFx0XHRcdC8vd3BiY19hanhfYXZhaWxhYmlsaXR5X19kZWZpbmVfdWlfaG9va3MoKTtcdFx0XHRcdFx0XHQvLyBSZWRlZmluZSBIb29rcywgYmVjYXVzZSB3ZSBzaG93IG5ldyBET00gZWxlbWVudHNcclxuXHRcdFx0XHRcdGlmICggJycgIT0gcmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdWyAnYWp4X2FmdGVyX2FjdGlvbl9tZXNzYWdlJyBdLnJlcGxhY2UoIC9cXG4vZywgXCI8YnIgLz5cIiApICl7XHJcblx0XHRcdFx0XHRcdHdwYmNfYWRtaW5fc2hvd19tZXNzYWdlKFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgcmVzcG9uc2VfZGF0YVsgJ2FqeF9kYXRhJyBdWyAnYWp4X2FmdGVyX2FjdGlvbl9tZXNzYWdlJyBdLnJlcGxhY2UoIC9cXG4vZywgXCI8YnIgLz5cIiApXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0LCAoICcxJyA9PSByZXNwb25zZV9kYXRhWyAnYWp4X2RhdGEnIF1bICdhanhfYWZ0ZXJfYWN0aW9uX3Jlc3VsdCcgXSApID8gJ3N1Y2Nlc3MnIDogJ2Vycm9yJ1xyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCwgMTAwMDBcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0KTtcclxuXHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHR3cGJjX2F2YWlsYWJpbGl0eV9yZWxvYWRfYnV0dG9uX19zcGluX3BhdXNlKCk7XHJcblx0XHRcdFx0XHQvLyBSZW1vdmUgc3BpbiBpY29uIGZyb20gIGJ1dHRvbiBhbmQgRW5hYmxlIHRoaXMgYnV0dG9uLlxyXG5cdFx0XHRcdFx0d3BiY19idXR0b25fX3JlbW92ZV9zcGluKCByZXNwb25zZV9kYXRhWyAnYWp4X2NsZWFuZWRfcGFyYW1zJyBdWyAndWlfY2xpY2tlZF9lbGVtZW50X2lkJyBdIClcclxuXHJcblx0XHRcdFx0XHRqUXVlcnkoICcjYWpheF9yZXNwb25kJyApLmh0bWwoIHJlc3BvbnNlX2RhdGEgKTtcdFx0Ly8gRm9yIGFiaWxpdHkgdG8gc2hvdyByZXNwb25zZSwgYWRkIHN1Y2ggRElWIGVsZW1lbnQgdG8gcGFnZVxyXG5cdFx0XHRcdH1cclxuXHRcdFx0ICApLmZhaWwoIGZ1bmN0aW9uICgganFYSFIsIHRleHRTdGF0dXMsIGVycm9yVGhyb3duICkgeyAgICBpZiAoIHdpbmRvdy5jb25zb2xlICYmIHdpbmRvdy5jb25zb2xlLmxvZyApeyBjb25zb2xlLmxvZyggJ0FqYXhfRXJyb3InLCBqcVhIUiwgdGV4dFN0YXR1cywgZXJyb3JUaHJvd24gKTsgfVxyXG5cclxuXHRcdFx0XHRcdHZhciBlcnJvcl9tZXNzYWdlID0gJzxzdHJvbmc+JyArICdFcnJvciEnICsgJzwvc3Ryb25nPiAnICsgZXJyb3JUaHJvd24gO1xyXG5cdFx0XHRcdFx0aWYgKCBqcVhIUi5zdGF0dXMgKXtcclxuXHRcdFx0XHRcdFx0ZXJyb3JfbWVzc2FnZSArPSAnICg8Yj4nICsganFYSFIuc3RhdHVzICsgJzwvYj4pJztcclxuXHRcdFx0XHRcdFx0aWYgKDQwMyA9PSBqcVhIUi5zdGF0dXMgKXtcclxuXHRcdFx0XHRcdFx0XHRlcnJvcl9tZXNzYWdlICs9ICcgUHJvYmFibHkgbm9uY2UgZm9yIHRoaXMgcGFnZSBoYXMgYmVlbiBleHBpcmVkLiBQbGVhc2UgPGEgaHJlZj1cImphdmFzY3JpcHQ6dm9pZCgwKVwiIG9uY2xpY2s9XCJqYXZhc2NyaXB0OmxvY2F0aW9uLnJlbG9hZCgpO1wiPnJlbG9hZCB0aGUgcGFnZTwvYT4uJztcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0aWYgKCBqcVhIUi5yZXNwb25zZVRleHQgKXtcclxuXHRcdFx0XHRcdFx0ZXJyb3JfbWVzc2FnZSArPSAnICcgKyBqcVhIUi5yZXNwb25zZVRleHQ7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRlcnJvcl9tZXNzYWdlID0gZXJyb3JfbWVzc2FnZS5yZXBsYWNlKCAvXFxuL2csIFwiPGJyIC8+XCIgKTtcclxuXHJcblx0XHRcdFx0XHR3cGJjX2FqeF9hdmFpbGFiaWxpdHlfX3Nob3dfbWVzc2FnZSggZXJyb3JfbWVzc2FnZSApO1xyXG5cdFx0XHQgIH0pXHJcblx0ICAgICAgICAgIC8vIC5kb25lKCAgIGZ1bmN0aW9uICggZGF0YSwgdGV4dFN0YXR1cywganFYSFIgKSB7ICAgaWYgKCB3aW5kb3cuY29uc29sZSAmJiB3aW5kb3cuY29uc29sZS5sb2cgKXsgY29uc29sZS5sb2coICdzZWNvbmQgc3VjY2VzcycsIGRhdGEsIHRleHRTdGF0dXMsIGpxWEhSICk7IH0gICAgfSlcclxuXHRcdFx0ICAvLyAuYWx3YXlzKCBmdW5jdGlvbiAoIGRhdGFfanFYSFIsIHRleHRTdGF0dXMsIGpxWEhSX2Vycm9yVGhyb3duICkgeyAgIGlmICggd2luZG93LmNvbnNvbGUgJiYgd2luZG93LmNvbnNvbGUubG9nICl7IGNvbnNvbGUubG9nKCAnYWx3YXlzIGZpbmlzaGVkJywgZGF0YV9qcVhIUiwgdGV4dFN0YXR1cywganFYSFJfZXJyb3JUaHJvd24gKTsgfSAgICAgfSlcclxuXHRcdFx0ICA7ICAvLyBFbmQgQWpheFxyXG5cclxufVxyXG5cclxuXHJcblxyXG4vKipcclxuICogICBIIG8gbyBrIHMgIC0gIGl0cyBBY3Rpb24vVGltZXMgd2hlbiBuZWVkIHRvIHJlLVJlbmRlciBWaWV3cyAgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0gKi9cclxuXHJcbi8qKlxyXG4gKiBTZW5kIEFqYXggU2VhcmNoIFJlcXVlc3QgYWZ0ZXIgVXBkYXRpbmcgc2VhcmNoIHJlcXVlc3QgcGFyYW1ldGVyc1xyXG4gKlxyXG4gKiBAcGFyYW0gcGFyYW1zX2FyclxyXG4gKi9cclxuZnVuY3Rpb24gd3BiY19hanhfYXZhaWxhYmlsaXR5X19zZW5kX3JlcXVlc3Rfd2l0aF9wYXJhbXMgKCBwYXJhbXNfYXJyICl7XHJcblxyXG5cdC8vIERlZmluZSBkaWZmZXJlbnQgU2VhcmNoICBwYXJhbWV0ZXJzIGZvciByZXF1ZXN0XHJcblx0Xy5lYWNoKCBwYXJhbXNfYXJyLCBmdW5jdGlvbiAoIHBfdmFsLCBwX2tleSwgcF9kYXRhICkge1xyXG5cdFx0Ly9jb25zb2xlLmxvZyggJ1JlcXVlc3QgZm9yOiAnLCBwX2tleSwgcF92YWwgKTtcclxuXHRcdHdwYmNfYWp4X2F2YWlsYWJpbGl0eS5zZWFyY2hfc2V0X3BhcmFtKCBwX2tleSwgcF92YWwgKTtcclxuXHR9KTtcclxuXHJcblx0Ly8gU2VuZCBBamF4IFJlcXVlc3RcclxuXHR3cGJjX2FqeF9hdmFpbGFiaWxpdHlfX2FqYXhfcmVxdWVzdCgpO1xyXG59XHJcblxyXG5cclxuXHQvKipcclxuXHQgKiBTZWFyY2ggcmVxdWVzdCBmb3IgXCJQYWdlIE51bWJlclwiXHJcblx0ICogQHBhcmFtIHBhZ2VfbnVtYmVyXHRpbnRcclxuXHQgKi9cclxuXHRmdW5jdGlvbiB3cGJjX2FqeF9hdmFpbGFiaWxpdHlfX3BhZ2luYXRpb25fY2xpY2soIHBhZ2VfbnVtYmVyICl7XHJcblxyXG5cdFx0d3BiY19hanhfYXZhaWxhYmlsaXR5X19zZW5kX3JlcXVlc3Rfd2l0aF9wYXJhbXMoIHtcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdwYWdlX251bSc6IHBhZ2VfbnVtYmVyXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0fSApO1xyXG5cdH1cclxuXHJcblxyXG5cclxuLyoqXHJcbiAqICAgU2hvdyAvIEhpZGUgQ29udGVudCAgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tICovXHJcblxyXG4vKipcclxuICogIFNob3cgTGlzdGluZyBDb250ZW50IFx0LSBcdFNlbmRpbmcgQWpheCBSZXF1ZXN0XHQtXHR3aXRoIHBhcmFtZXRlcnMgdGhhdCAgd2UgZWFybHkgIGRlZmluZWRcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfYWp4X2F2YWlsYWJpbGl0eV9fYWN0dWFsX2NvbnRlbnRfX3Nob3coKXtcclxuXHJcblx0d3BiY19hanhfYXZhaWxhYmlsaXR5X19hamF4X3JlcXVlc3QoKTtcdFx0XHQvLyBTZW5kIEFqYXggUmVxdWVzdFx0LVx0d2l0aCBwYXJhbWV0ZXJzIHRoYXQgIHdlIGVhcmx5ICBkZWZpbmVkIGluIFwid3BiY19hanhfYm9va2luZ19saXN0aW5nXCIgT2JqLlxyXG59XHJcblxyXG4vKipcclxuICogSGlkZSBMaXN0aW5nIENvbnRlbnRcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfYWp4X2F2YWlsYWJpbGl0eV9fYWN0dWFsX2NvbnRlbnRfX2hpZGUoKXtcclxuXHJcblx0alF1ZXJ5KCAgd3BiY19hanhfYXZhaWxhYmlsaXR5LmdldF9vdGhlcl9wYXJhbSggJ2xpc3RpbmdfY29udGFpbmVyJyApICApLmh0bWwoICcnICk7XHJcbn1cclxuXHJcblxyXG5cclxuLyoqXHJcbiAqICAgTSBlIHMgcyBhIGcgZSAgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tICovXHJcblxyXG4vKipcclxuICogU2hvdyBqdXN0IG1lc3NhZ2UgaW5zdGVhZCBvZiBjb250ZW50XHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2FqeF9hdmFpbGFiaWxpdHlfX3Nob3dfbWVzc2FnZSggbWVzc2FnZSApe1xyXG5cclxuXHR3cGJjX2FqeF9hdmFpbGFiaWxpdHlfX2FjdHVhbF9jb250ZW50X19oaWRlKCk7XHJcblxyXG5cdGpRdWVyeSggd3BiY19hanhfYXZhaWxhYmlsaXR5LmdldF9vdGhlcl9wYXJhbSggJ2xpc3RpbmdfY29udGFpbmVyJyApICkuaHRtbChcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0JzxkaXYgY2xhc3M9XCJ3cGJjLXNldHRpbmdzLW5vdGljZSBub3RpY2Utd2FybmluZ1wiIHN0eWxlPVwidGV4dC1hbGlnbjpsZWZ0XCI+JyArXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0bWVzc2FnZSArXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCc8L2Rpdj4nXHJcblx0XHRcdFx0XHRcdFx0XHRcdFx0KTtcclxufVxyXG5cclxuXHJcblxyXG4vKipcclxuICogICBTdXBwb3J0IEZ1bmN0aW9ucyAtIFNwaW4gSWNvbiBpbiBCdXR0b25zICAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0gKi9cclxuXHJcbi8qKlxyXG4gKiBTcGluIGJ1dHRvbiBpbiBGaWx0ZXIgdG9vbGJhciAgLSAgU3RhcnRcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfYXZhaWxhYmlsaXR5X3JlbG9hZF9idXR0b25fX3NwaW5fc3RhcnQoKXtcclxuXHRqUXVlcnkoICcjd3BiY19hdmFpbGFiaWxpdHlfcmVsb2FkX2J1dHRvbiAubWVudV9pY29uLndwYmNfc3BpbicpLnJlbW92ZUNsYXNzKCAnd3BiY19hbmltYXRpb25fcGF1c2UnICk7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBTcGluIGJ1dHRvbiBpbiBGaWx0ZXIgdG9vbGJhciAgLSAgUGF1c2VcclxuICovXHJcbmZ1bmN0aW9uIHdwYmNfYXZhaWxhYmlsaXR5X3JlbG9hZF9idXR0b25fX3NwaW5fcGF1c2UoKXtcclxuXHRqUXVlcnkoICcjd3BiY19hdmFpbGFiaWxpdHlfcmVsb2FkX2J1dHRvbiAubWVudV9pY29uLndwYmNfc3BpbicgKS5hZGRDbGFzcyggJ3dwYmNfYW5pbWF0aW9uX3BhdXNlJyApO1xyXG59XHJcblxyXG4vKipcclxuICogU3BpbiBidXR0b24gaW4gRmlsdGVyIHRvb2xiYXIgIC0gIGlzIFNwaW5uaW5nID9cclxuICpcclxuICogQHJldHVybnMge2Jvb2xlYW59XHJcbiAqL1xyXG5mdW5jdGlvbiB3cGJjX2F2YWlsYWJpbGl0eV9yZWxvYWRfYnV0dG9uX19pc19zcGluKCl7XHJcbiAgICBpZiAoIGpRdWVyeSggJyN3cGJjX2F2YWlsYWJpbGl0eV9yZWxvYWRfYnV0dG9uIC5tZW51X2ljb24ud3BiY19zcGluJyApLmhhc0NsYXNzKCAnd3BiY19hbmltYXRpb25fcGF1c2UnICkgKXtcclxuXHRcdHJldHVybiB0cnVlO1xyXG5cdH0gZWxzZSB7XHJcblx0XHRyZXR1cm4gZmFsc2U7XHJcblx0fVxyXG59XHJcbiJdLCJtYXBwaW5ncyI6IkFBQUEsWUFBWTs7QUFFWjtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBSkEsU0FBQUEsUUFBQUMsQ0FBQSxzQ0FBQUQsT0FBQSx3QkFBQUUsTUFBQSx1QkFBQUEsTUFBQSxDQUFBQyxRQUFBLGFBQUFGLENBQUEsa0JBQUFBLENBQUEsZ0JBQUFBLENBQUEsV0FBQUEsQ0FBQSx5QkFBQUMsTUFBQSxJQUFBRCxDQUFBLENBQUFHLFdBQUEsS0FBQUYsTUFBQSxJQUFBRCxDQUFBLEtBQUFDLE1BQUEsQ0FBQUcsU0FBQSxxQkFBQUosQ0FBQSxLQUFBRCxPQUFBLENBQUFDLENBQUE7QUFNQSxJQUFJSyxxQkFBcUIsR0FBSSxVQUFXQyxHQUFHLEVBQUVDLENBQUMsRUFBRTtFQUUvQztFQUNBLElBQUlDLFFBQVEsR0FBR0YsR0FBRyxDQUFDRyxZQUFZLEdBQUdILEdBQUcsQ0FBQ0csWUFBWSxJQUFJO0lBQ3hDQyxPQUFPLEVBQUUsQ0FBQztJQUNWQyxLQUFLLEVBQUksRUFBRTtJQUNYQyxNQUFNLEVBQUc7RUFDUixDQUFDO0VBRWhCTixHQUFHLENBQUNPLGdCQUFnQixHQUFHLFVBQVdDLFNBQVMsRUFBRUMsU0FBUyxFQUFHO0lBQ3hEUCxRQUFRLENBQUVNLFNBQVMsQ0FBRSxHQUFHQyxTQUFTO0VBQ2xDLENBQUM7RUFFRFQsR0FBRyxDQUFDVSxnQkFBZ0IsR0FBRyxVQUFXRixTQUFTLEVBQUc7SUFDN0MsT0FBT04sUUFBUSxDQUFFTSxTQUFTLENBQUU7RUFDN0IsQ0FBQzs7RUFHRDtFQUNBLElBQUlHLFNBQVMsR0FBR1gsR0FBRyxDQUFDWSxrQkFBa0IsR0FBR1osR0FBRyxDQUFDWSxrQkFBa0IsSUFBSTtJQUNsRDtJQUNBO0lBQ0E7SUFDQTtJQUNBO0lBQ0E7SUFDQTtFQUFBLENBQ0E7RUFFakJaLEdBQUcsQ0FBQ2EscUJBQXFCLEdBQUcsVUFBV0MsaUJBQWlCLEVBQUc7SUFDMURI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
