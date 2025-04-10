<?php

namespace NinjaTables\Framework\Support;

use DateTimeZone;
use DateInterval;
use DateTimeInterface;
use DateTime as PHPDateTime;
use InvalidArgumentException;

class DateTime extends PHPDateTime
{
    /**
     * $singularUnits for checking during dynamic calls
     * @var array
     */
    protected static $singularUnits = [
        'year', 'month', 'week', 'day', 'hour', 'minute', 'second',
    ];

    /**
     * $pluralUnits for checking during dynamic calls
     * @var array
     */
    protected static $pluralUnits = [
        'years','months', 'weeks', 'days', 'hours', 'minutes', 'seconds'
    ];

    /**
     * Construct the DateTime Object
     * 
     * @param string $datetime
     * @param \DateTimeZone $timezone|null
     */
    public function __construct($datetime = "now", $timezone = null)
    {
        $timezone = $timezone ?: $this->getDefaultTimezone();

        parent::__construct($datetime, $timezone);
    }

    /**
     * Create a new DateTime Object with current time
     * 
     * @return $this
     */
    public static function now($tz = null)
    {
        return static::create('now', $tz);
    }

    /**
     * Create a new DateTime Object with today's time
     * 
     * @return $this
     */
    public static function today($tz = null)
    {
        return static::create('today', $tz)->startOfDay();
    }

    /**
     * Create a new DateTime Object with yesterday's time
     * 
     * @return $this
     */
    public static function yesterday($tz = null)
    {
        return static::create('now', $tz)->modify('-1 day')->startOfDay();
    }

    /**
     * Create a new DateTime Object with tomorrow's time
     * 
     * @return $this
     */
    public static function tomorrow($tz = null)
    {
        return static::create('now', $tz)->modify('+1 day')->startOfDay();
    }

    /**
     * Get the default timezone
     *
     * @return \DateTimeZone
     */
    public function getDefaultTimezone()
    {
        return wp_timezone();
    }

    /**
     * Set the timezone
     *
     * @return $this
     */
    public function timezone($tz)
    {
        if (is_string($tz)) {
            $tz = new DateTimeZone($tz);
        }

        return $this->setTimezone($tz);
    }

    /**
     * Get the default date format
     * 
     * @return string
     */
    public function getDateFormat()
    {   
        return 'Y-m-d H:i:s';
    }

    /**
     * Check if the current instance is between two dates
     *
     * @param string|DateTimeInterface $date1
     * @param string|DateTimeInterface $date2,
     * @return bool
     */
    public function between($date1, $date2)
    {
        if (!$date1 instanceof DateTimeInterface) {
            $date1 = new DateTime($date1);
        }

        if (!$date2 instanceof DateTimeInterface) {
            $date2 = new DateTime($date2);
        }

        return ($this >= $date1 && $this <= $date2);
    }

    /**
     * Create a DateTime object from a string, UNIX timestamp,
     * or other DateTimeInterface object.
     * 
     * @param  string|int|\DateTimeInterface  $time
     * @return static
     * @throws \Exception
     */
    public static function create($time = null, $tz = null)
    {
        if (func_num_args() > 2) {
            return static::createFromDate(...func_get_args());
        }

        $time = $time ?: static::now();

        if (is_null($tz)) {
            $timezone = (new static)->getDefaultTimezone();
        } else {
            $timezone = is_string($tz) ? new DateTimeZone($tz) : $tz;
        }

        if (!$timezone instanceof DateTimeZone) {
            throw new InvalidArgumentException('Invalid timezone.');
        }

        if ($time instanceof DateTimeInterface) {

            $dateTime = new static(
                $time->format((new static)->getDateFormat()), $time->getTimezone()
            );

            // Override the timezone if the timezone is explictly provided
            // otherwise don't set the default timezone from $timezone.
            !is_null($tz) && $dateTime->setTimezone($timezone);

        } elseif (is_numeric($time)) {
            if ($time <= YEAR_IN_SECONDS) {
                $time += time();
            }

            $dateTime = new static('@' . $time);

            $dateTime->setTimezone($timezone);

        } else {
            $dateTime = new static((string) $time);

            // Set the timezone if timezone is explicitly provided
            // otherwise set the default timezone if there was no
            // timezne information available with the string.
            if ($tz || !$dateTime->hasTimezone($time)) {
                $dateTime->setTimezone($timezone);
            }
        }

        return $dateTime;
    }

    /**
     * Check if the given datetime string has the timezone
     * information attached: Z or +/-00:00 or Asia\Dhaka.
     * 
     * @param  string $datetimeString
     * @return boolean
     */
    public function hasTimezone($datetimeString)
    {
        // Regular expression to match timezone
        // identifier, UTC, or timezone offset
        $pattern = '/(?:[A-Z][a-zA-Z_]+\/[a-zA-Z_]+|Z|[-+]\d{2}:\d{2})/';

        return preg_match($pattern, $datetimeString) === 1;
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public static function createFromFormat($format, $datetimeString, $timezone = null)
    {
        if (is_null($timezone)) {
            $timezone = (new static)->getDefaultTimezone();
        } else {
            $timezone = is_string($timezone) ? new DateTimeZone($timezone) : $timezone;
        }

        if (!$timezone instanceof DateTimeZone) {
            throw new InvalidArgumentException('Invalid timezone.');
        }

        $dateTime = PHPDateTime::createFromFormat($format, $datetimeString);
        
        if ($dateTime !== false) {
            
            if (!$dateTime instanceof static) {
                return new static(
                    $dateTime->format(ltrim($format, '!')), $timezone
                );
            }

            $dateTime->setTimezone($timezone);

            return $dateTime;
        }

        throw new InvalidArgumentException(
            "Unable to create datetime from: {$datetimeString}."
        );
    }

    /**
     * Create DateTime object.
     * 
     * @return static
     * @throws InvalidArgumentException
     */
    public static function createFromDate(
        $year, $month, $day, $hour = 0, $minute = 0, $second = 0.0, $tz = null
    ) {
        
        $s = sprintf(
            '%04d-%02d-%02d %02d:%02d:%02.5F', $year, $month, $day, $hour, $minute, $second
        );

        if (
            !checkdate($month, $day, $year)
            || $hour < 0
            || $hour > 23
            || $minute < 0
            || $minute > 59
            || $second < 0
            || $second >= 60
        ) {
            throw new InvalidArgumentException("Invalid date '$s'");
        }

        return new static($s, (is_string($tz) ? new DateTimeZone($tz) : $tz));
    }

    /**
     * Given a date in UTC or GMT timezone, returns
     * that date in the timezone of the site.
     *
     * Requires a date in the Y-m-d H:i:s format.
     * 
     * Default return format of 'Y-m-d H:i:s' can be
     * overridden using the `$format` parameter.
     *
     * @param string $date_string The date to be converted, in UTC or GMT timezone.
     * @param string $format      The format string for the returned date. Default 'Y-m-d H:i:s'.
     * @see https://developer.wordpress.org/reference/functions/get_date_from_gmt/
     * 
     * @return string Formatted version of the date, in the site's timezone.
     */
    public static function createFromUTC($dateString, $format = 'Y-m-d H:i:s')
    {
        $date = new static(get_date_from_gmt($dateString, $format));

        return $date->timezone($date->getDefaultTimezone())->format($format);
    }

    /**
     * Parse a datetime string
     * @param  string $datetimeString
     * @param  string $timezone
     * @return $this
     * @throws InvalidArgumentException
     */
    public static function parse($datetimeString, $timezone = null)
    {
        $parsedDate = date_parse($datetimeString);
        
        $datetimeString = date('Y-m-d H:i:s', mktime(
            $parsedDate['hour'],
            $parsedDate['minute'],
            $parsedDate['second'],
            $parsedDate['month'],
            $parsedDate['day'],
            $parsedDate['year']
        ));

        if ($timezone && is_scalar($timezone)) {
            $timezone = new DateTimeZone($timezone);
        } elseif (isset($parsedDate['tz_id'])) {
            $timezone = new DateTimeZone($parsedDate['tz_id']);
        }

        $dateTime = new PHPDateTime($datetimeString, $timezone);

        if ($dateTime instanceof DateTimeInterface) {
            return new static($datetimeString, $timezone);
        }

        throw new InvalidArgumentException('Unable to handle datetime.');
    }

    /**
     * Add inetrvals, for example:
     * 
     * add(1, day)
     * add('2 day 8 hours 22 minutes')
     * 
     * @param \DateInterval|string
     * @return $this
     */
    #[\ReturnTypeWillChange]
    public function add($interval)
    {
        if ($interval instanceof DateInterval) {
            return parent::add($interval);
        } elseif (func_num_args() === 1 && is_string($interval)) {
            return $this->modify('+'.$interval);
        }

        return $this->addOrSub('add', func_get_args());
    }

    /**
     * Substruct inetrvals, for example:
     * 
     * sub(1, day)
     * sub('2 day 8 hours 22 minutes')
     * 
     * @param \DateInterval $interval (optional)
     * @return $this
     */
    #[\ReturnTypeWillChange]
    public function sub($interval)
    {
        if ($interval instanceof DateInterval) {
            return parent::add($interval);
        } elseif (func_num_args() === 1 && is_string($interval)) {
            return $this->modify('-'.$interval);
        }

        return $this->addOrSub('sub', func_get_args());
    }

    /**
     * Add or sub intervals
     * @param string $action add/sub
     * @param array $args
     */
    protected function addOrSub($action, $args)
    {
        $value = reset($args);
        
        $action = $action.end($args);

        return $this->{$action}($value);
    }

    /**
     * Adds the given number of seconds to the current date and time.
     *
     * @param int $seconds The number of seconds to add.
     * @return $this The current instance for method chaining.
     */
    public function addSeconds(int $seconds)
    {
        return $this->add("{$seconds} seconds");
    }

    /**
     * Adds the given number of minutes to the current date and time.
     *
     * @param int $minutes The number of minutes to add.
     * @return $this The current instance for method chaining.
     */
    public function addMinutes(int $minutes)
    {
        return $this->add("{$minutes} minutes");
    }

    /**
     * Adds the given number of hours to the current date and time.
     *
     * @param int $hours The number of hours to add.
     * @return $this The current instance for method chaining.
     */
    public function addHours(int $hours)
    {
        return $this->add("{$hours} hours");
    }

    /**
     * Adds the given number of days to the current date and time.
     *
     * @param int $days The number of days to add.
     * @return $this The current instance for method chaining.
     */
    public function addDays(int $days)
    {
        return $this->add("{$days} days");
    }

    /**
     * Adds the given number of weeks to the current date and time.
     *
     * @param int $weeks The number of weeks to add.
     * @return $this The current instance for method chaining.
     */
    public function addWeeks(int $weeks)
    {
        return $this->add("{$weeks} weeks");
    }

    /**
     * Adds the given number of months to the current date and time.
     *
     * @param int $months The number of months to add.
     * @return $this The current instance for method chaining.
     */
    public function addMonths(int $months)
    {
        return $this->add("{$months} months");
    }

    /**
     * Adds the given number of years to the current date and time.
     *
     * @param int $years The number of years to add.
     * @return $this The current instance for method chaining.
     */
    public function addYears(int $years)
    {
        return $this->add("{$years} years");
    }

     /**
     * Subtracts the given number of seconds from the current date and time.
     *
     * @param int $seconds The number of seconds to subtract.
     * @return $this The current instance for method chaining.
     */
    public function subSeconds(int $seconds)
    {
        return $this->sub("{$seconds} seconds");
    }

    /**
     * Subtracts the given number of minutes from the current date and time.
     *
     * @param int $minutes The number of minutes to subtract.
     * @return $this The current instance for method chaining.
     */
    public function subMinutes(int $minutes)
    {
        return $this->sub("{$minutes} minutes");
    }

    /**
     * Subtracts the given number of hours from the current date and time.
     *
     * @param int $hours The number of hours to subtract.
     * @return $this The current instance for method chaining.
     */
    public function subHours(int $hours)
    {
        return $this->sub("{$hours} hours");
    }

    /**
     * Subtracts the given number of days from the current date and time.
     *
     * @param int $days The number of days to subtract.
     * @return $this The current instance for method chaining.
     */
    public function subDays(int $days)
    {
        return $this->sub("{$days} days");
    }

    /**
     * Subtracts the given number of weeks from the current date and time.
     *
     * @param int $weeks The number of weeks to subtract.
     * @return $this The current instance for method chaining.
     */
    public function subWeeks(int $weeks)
    {
        return $this->sub("{$weeks} weeks");
    }

    /**
     * Subtracts the given number of months from the current date and time.
     *
     * @param int $months The number of months to subtract.
     * @return $this The current instance for method chaining.
     */
    public function subMonths(int $months)
    {
        return $this->sub("{$months} months");
    }

    /**
     * Subtracts the given number of years from the current date and time.
     *
     * @param int $years The number of years to subtract.
     * @return $this The current instance for method chaining.
     */
    public function subYears(int $years)
    {
        return $this->sub("{$years} years");
    }

    /**
     * Set the date to start of the decade.
     * @return $this
     */
    public function startOfDecade()
    {
        $year = (int) $this->format('Y');
        // Find the start of the decade by subtracting the remainder
        // of the division by 10 from the current year.
        $startOfDecadeYear = $year - ($year % 10);
        
        // Set the date to the start of the decade (January 1st)
        return $this->setDate($startOfDecadeYear, 1, 1)->setTime(0, 0);
    }

    /**
     * Set the date to end of the decade.
     * @return $this
     */
    public function endOfDecade()
    {
        $year = (int) $this->format('Y');
        // Find the last year of the decade by adding 9 to the current
        // year and subtracting the remainder of the division by 10.
        $endOfDecadeYear = $year + (9 - ($year % 10));

        // Set the date to December 31st of that year at 23:59:59
        return $this->setDate($endOfDecadeYear, 12, 31)->setTime(23, 59, 59);
    }

    /**
     * Sets start of the year in the current dateTime
     * 
     * @return $this
     */
    public function startOfYear()
    {
        return $this->modify('first day of January')->startOfDay();
    }

    /**
     * Sets end of the year in the current dateTime
     * 
     * @return $this
     */
    public function endOfYear()
    {
        return $this->modify('last day of December')->endOfDay();
    }

    /**
     * Sets the date to the first day of the current quarter at 00:00:00.
     *
     * @return $this
     */
    public function startOfQuarter()
    {
        $month = (int) $this->format('m');
        // Determine the start month of the current quarter
        if ($month <= 3) {
            $startMonth = 1; // Q1 starts in January
        } elseif ($month <= 6) {
            $startMonth = 4; // Q2 starts in April
        } elseif ($month <= 9) {
            $startMonth = 7; // Q3 starts in July
        } else {
            $startMonth = 10; // Q4 starts in October
        }

        // Set the date to the first day of the quarter at 00:00:00
        return $this->setDate((int) $this->format('Y'), $startMonth, 1)->setTime(0, 0, 0);
    }

    /**
     * Sets the date to the last day of the current quarter at 23:59:59.
     *
     * @return $this
     */
    public function endOfQuarter()
    {
        $month = (int) $this->format('m');
        // Determine the end month of the current quarter
        if ($month <= 3) {
            $endMonth = 3; // Q1 ends in March
        } elseif ($month <= 6) {
            $endMonth = 6; // Q2 ends in June
        } elseif ($month <= 9) {
            $endMonth = 9; // Q3 ends in September
        } else {
            $endMonth = 12; // Q4 ends in December
        }

        // Set the date to the last day of the quarter at 23:59:59
        return $this->setDate((int) $this->format('Y'), $endMonth, cal_days_in_month(CAL_GREGORIAN, $endMonth, (int) $this->format('Y')))
                    ->setTime(23, 59, 59);
    }

    /**
     * Sets start of the month in the current dateTime
     * 
     * @return $this
     */
    public function startOfMonth()
    {
        return $this->modify('first day of this month')->startOfDay();
    }

    /**
     * Sets end of the month in the current dateTime
     * 
     * @return $this
     */
    public function endOfMonth()
    {
        return $this->modify('last day of this month')->endOfDay();
    }

    /**
     * Sets start of the week in the current dateTime
     * 
     * @return $this
     */
    public function startOfWeek()
    {
        $startOfWeek = intval(get_option('start_of_week'));

        $this->modify('this week');

        // If the start of the week is Sunday (0)
        if ($startOfWeek === 0) {
            return $this->modify('this Sunday')->startOfDay();
        } else {
            // If it's Monday (1), we need to subtract 1 day.
            return $this->modify(
                'this Sunday - ' . (7 - $startOfWeek) . ' days'
            )->startOfDay();
        }
    }

    /**
     * Sets end of the week in the current dateTime
     * 
     * @return $this
     */
    public function endOfWeek()
    {
        // 0 = Sunday, 1 = Monday, etc.
        $startOfWeek = intval(get_option('start_of_week'));

        // If the start of the week is Monday (1), the
        // end of the week is the upcoming Sunday
        if ($startOfWeek === 1) {
            return $this->modify('next Sunday')->endOfDay();
        }

        // If the start of the week is Sunday (0), the
        // end of the week is the upcoming Saturday
        return $this->modify('next Saturday')->endOfDay();
    }

    /**
     * Sets start of the day in the current dateTime
     * 
     * @return $this
     */
    public function startOfDay()
    {
        return $this->setTime(0, 0, 0, 0);
    }

    /**
     * Sets end of the day in the current dateTime
     * 
     * @return $this
     */
    public function endOfDay()
    {
        return $this->setTime(23, 59, 59);
    }

    /**
     * Sets start of the hour in the current DateTime object
     * 
     * @return $this
     */
    public function startOfHour()
    {
        return $this->setTime($this->format('H'), 0, 0, 0);
    }

    /**
     * Sets end of the hour in the current DateTime object
     * 
     * @return $this
     */
    public function endOfHour()
    {
        return $this->setTime($this->format('H'), 59, 59, 999999);
    }

    /**
     * Sets start of the minute in the current DateTime object
     * 
     * @return $this
     */
    public function startOfMinute()
    {
        $hour = $this->format('H');
        $minute = $this->format('i');
        return $this->setTime($hour, $minute, 0, 0);
    }

    /**
     * Sets end of the minute in the current DateTime object
     * 
     * @return $this
     */
    public function endOfMinute()
    {
        $hour = $this->format('H');
        $minute = $this->format('i');
        return $this->setTime($hour, $minute, 59, 999999);
    }

    /**
     * Check if the current instance is a weekend.
     *
     * @return bool
     */
    public function isWeekend($startOfWeek = null): bool
    {
        if ($startOfWeek === null) {
            $startOfWeek = $startOfWeek = intval(get_option('start_of_week'));
        }

        // Get the numeric representation of the current day of the week (0 - 6)
        $dayOfWeek = (int) $this->format('w');
        
        // Adjust the day of the week based on the start of the week
        switch ($startOfWeek) {
            case 'monday':
                // If the week starts on Monday, adjust Sunday to 6
                return ($dayOfWeek === 0 || $dayOfWeek === 6);
            case 'saturday':
                // If the week starts on Saturday, adjust Friday to 6
                return ($dayOfWeek === 5 || $dayOfWeek === 6);
            case 'sunday':
            default:
                // Default behavior, week starts on Sunday
                return ($dayOfWeek === 0 || $dayOfWeek === 6);
        }
    }

    /**
     * Check if the current instance is a weekday.
     *
     * @return bool
     */
    public function isWeekday()
    {
        return !$this->isWeekend();
    }

    /**
     * Check if the current instance is in the past.
     *
     * @return bool
     */
    public function isPast()
    {
        // Compare with current date and time
        return $this < new static();
    }

    /**
     * Check if the current instance is in the future.
     *
     * @return bool
     */
    public function isFuture()
    {
        return $this > new static();
    }

    /**
     * Check if the year is a leap year.
     * @return boolean
     */
    public function isLeapYear(): bool
    {
        $year = (int) $this->format('Y');
        return ($year % 4 === 0 && $year % 100 !== 0) || ($year % 400 === 0);
    }

    /**
     * Checks if the current time is midnight (00:00:00).
     *
     * @return bool
     */
    public function isMidnight()
    {
        return $this->format('H:i:s') === '00:00:00';
    }

    /**
     * Check if the current instance is the same day as another DateTime instance.
     *
     * @param DateTime $other
     * @return bool
     */
    public function isSameDay(DateTime $other)
    {
        return $this->format('Y-m-d') === $other->format('Y-m-d');
    }

    /**
     * Clone the current Object
     * 
     * @return \NinjaTables\Framework\Support\DateTime
     */
    public function copy()
    {
        return clone $this;
    }

    /**
     * Get the difference in years
     * 
     * @param  \NinjaTables\Framework\Support\DateTime $date
     * @return int
     */
    public function diffInYears($date)
    {
        return $this->diff($date)->y;
    }

    /**
     * Get the difference in months
     * 
     * @param  \NinjaTables\Framework\Support\DateTime $date
     * @return int
     */
    public function diffInMonths($date)
    {
        $diff = $this->diff($date);

        return $diff->y  * 12 + $diff->m;
    }

    /**
     * Get the difference in days
     * 
     * @param  \NinjaTables\Framework\Support\DateTime $date
     * @return int
     */
    public function diffInDays($date)
    {
        $diff = $this->diff($date);

        return $diff->days;
    }

    /**
     * Get the difference in hours
     * 
     * @param  \NinjaTables\Framework\Support\DateTime $date
     * @return int
     */
    public function diffInHours($date)
    {
        $diff = $this->diff($date);

        $diffInHours = $diff->h;

        return $diffInHours + $diff->days * 24;
    }

    /**
     * Get the difference in minutes
     * 
     * @param  \NinjaTables\Framework\Support\DateTime $date
     * @return int
     */
    public function diffInMinutes($date)
    {
        $diff = $this->diff($date);

        $diffInMinutes = $diff->i;

        $diffInMinutes += $diff->h * 60;

        return $diffInMinutes + $diff->days * 24 * 60;
    }

    /**
     * Get the difference in seconds
     * 
     * @param  \NinjaTables\Framework\Support\DateTime $date
     * @return int
     */
    public function diffInSeconds($date)
    {
        $diff = $this->diff($date);

        $diffInSeconds = $diff->days * 24 * 60 * 60;

        $diffInSeconds += $diff->h * 60 * 60;

        $diffInSeconds += $diff->i * 60;

        return $diffInSeconds + $diff->s;
    }

    /**
     * Get human friendly time difference (2 hours ago/ 2 hours from now)
     * 
     * @param  \DateTime|string|timestamp $from The datetime to compare from
     * @param  \DateTime|string|timestamp $to The datetime to compare to

     * @return string Human readable string, ie. 5 days ago/from now
     */
    public function diffForHumans($from = null, $to = null)
    {
        // Use the current object's timestamp if $from (and $to) is null
        // This is because ORM's datetime field can call it without params.
        if (is_null($from)) {
            $from = $this->getTimestamp();
        } elseif ($from instanceof \DateTime) {
            $from = $from->getTimestamp();
        } elseif (!is_numeric($from)) {
            $from = (new \DateTime($from))->getTimestamp();
        }

        // Use the current time as $to if not provided
        if (is_null($to)) {
            $to = time();
        } elseif ($to instanceof \DateTime) {
            $to = $to->getTimestamp();
        } elseif (!is_numeric($to)) {
            $to = (new \DateTime($to))->getTimestamp();
        }

        // Calculate the difference in seconds
        $diffInSeconds = abs($to - $from);
        $dateTimeDiff = human_time_diff($from, $to);

        // Determine if the difference is in the past or future
        if ($from > $to) {
            // The "from" time is earlier than "to" (future)
            return sprintf(__('%s from now'), $dateTimeDiff);
        } else {
            // The "from" time is later than "to" (older)
            if ($diffInSeconds > 60) {
                return sprintf(__('%s ago'), $dateTimeDiff);
            }

            // If difference is less than 1 minute, return just now
            return __('just now');
        }
    }

    /**
     * Given a date in the timezone of the site, returns that date in UTC.
     *
     * Requires and returns a date in the Y-m-d H:i:s format.
     * 
     * Return format can be overridden using the $format parameter.
     *
     * @param string $dateString The date to be converted, in the timezone of the site.
     * @param string $format The format string for the returned date. Default 'Y-m-d H:i:s'.
     * @see https://developer.wordpress.org/reference/functions/get_gmt_from_date/
     * 
     * @return string Formatted version of the date, in UTC.
     */
    public function toUTC($dateString, $format = 'Y-m-d H:i:s')
    {
        return get_gmt_from_date($dateString, $format);
    }

    /**
     * Return the ISO-8601 string
     *
     * @see https://stackoverflow.com/a/11173072/741747
     *
     * @return mixed
     */
    public function toJSON()
    {
        return date('c', $this->getTimestamp());
    }

    /**
     * Returns the formatted string
     * 
     * @return string
     */
    public function toString()
    {
        return (string) $this;
    }

    /**
     * Return only the date part as string
     * 
     * @return string
     */
    public function toDateString()
    {
        return (string) $this->format('Y-m-d');
    }

    /**
     * Return only the time part as string
     * 
     * @return string
     */
    public function toTimeString()
    {
        return (string) $this->format('H:i:s');
    }

    /**
     * Returns the formatted string
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->format($this->getDateFormat());
    }

    /**
     * Getter to get an unit of DateTime
     * @param  string $key
     * @return string|null
     */
    public function __get($key)
    {
        if ($key == 'year') {
            return $this->format('Y');
        } elseif ($key == 'month') {
            return $this->format('m');
        } elseif ($key == 'day') {
            return $this->format('d');
        } elseif ($key == 'hour') {
            return $this->format('H');
        } elseif ($key == 'minute') {
            return $this->format('i');
        } elseif ($key == 'second') {
            return $this->format('s');
        }
    }

    /**
     * Setter to set an unit of DateTime
     * @param  string $key
     * @param  string|int $value
     * @return $this
     */
    public function __set($key, $value)
    {
        if ($key == 'year') {
            return $this->setDate($value, $this->format('m'), $this->format('d'));
        } elseif ($key == 'month') {
            return $this->setDate($this->format('Y'), $value, $this->format('d'));
        } elseif ($key == 'day') {
            return $this->setDate($this->format('Y'), $this->format('m'), $value);
        } elseif ($key == 'hour') {
            return $this->setTime($value, $this->format('i'), $this->format('s'));
        } elseif ($key == 'minute') {
            return $this->setTime($this->format('H'), $value, $this->format('s'));
        } elseif ($key == 'second') {
           return $this->setTime($this->format('H'), $this->format('i'), $value);
        }
    }

    /**
     * Handle Dynamic calls (add/sub)
     * 
     * @param  string $method
     * @param  array $params
     * @return $this
     */
    public function __call($method, $params)
    {
        // Dynamic Setter/Getter
        if (strpos($method, 'set') === 0) {
            $unit = strtolower(substr($method, 3));
            if ($params && in_array($unit, static::$singularUnits)) {
                $this->{$unit} = reset($params);
                return $this;
            }
        } elseif (strpos($method, 'get') === 0) {
            $unit = strtolower(substr($method, 3));
            if (in_array($unit, static::$singularUnits)) {
                return $this->{$unit};
            }
        }

        // Dynamic adder/subtractor
        if (strpos($method, 'add') === 0) {
            $action = '+';
        } elseif (strpos($method, 'sub') === 0) {
            $action = '-';
        }

        if (isset($action) && in_array($action, ['+', '-'])) {

            if (!$params) {
                $duration = 1;
            } else {
                $duration = reset($params);
            }


            $unit = strtolower(substr($method, 3));

            $units = array_merge(static::$singularUnits, static::$pluralUnits);

            if (in_array($unit, $units)) {
                return $this->modify("{$action}{$duration}{$unit}");
            }
        }

        throw new InvalidArgumentException("Call to undefined method {$method}.");
    }
}
