<?php
/**
 * This template will display the calendar for a reurring event, allowing to pick recurrence dates to load the booking form selection
 */
/* @var EM_Event $EM_Event */
/* @var EM_Booking $EM_Booking booking intent */
/* @var bool $id */
?>
<?php
$calendar_args = [
	'id' => $id,
	'recurrence' => $EM_Event->event_id,
	'calendar_size' => 'small',
	//'calendar_event_style' => 'dot',
	'long_events' => false,
	'calendar_preview_mode' => 'booking',
	'class' => 'em-booking-calendar',
	'empty_months' => false,
	'scope' => 'future',
	'calendar_header' => 'centered',
	'calendar_timezone' => $EM_Event->event_timezone,
];
echo EM_Calendar::output( $calendar_args );