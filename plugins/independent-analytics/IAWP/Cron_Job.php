<?php

namespace IAWP;

use IAWPSCOPED\Carbon\CarbonImmutable;
/** @internal */
abstract class Cron_Job
{
    protected $name = '';
    protected $interval = 'daily';
    public abstract function handle() : void;
    public function register_handler() : void
    {
        \add_action($this->name, function () {
            if ($this->should_execute_handler()) {
                $this->handle();
            }
        });
    }
    public function unschedule()
    {
        $scheduled_at_timestamp = \wp_next_scheduled($this->name);
        if (\is_int($scheduled_at_timestamp)) {
            \wp_unschedule_event($scheduled_at_timestamp, $this->name);
        }
    }
    public function schedule()
    {
        $scheduled_at_timestamp = \wp_next_scheduled($this->name);
        if ($scheduled_at_timestamp === \false) {
            \wp_schedule_event($this->timestamp_for_next_interval($this->interval), $this->interval, $this->name);
        }
    }
    public function timestamp_for_next_interval(string $interval_id) : ?int
    {
        // Run hourly intervals on the hour
        if ($this->interval === 'hourly') {
            $now = CarbonImmutable::now()->startOfSecond();
            $next_hour = $now->addHour()->startOfHour();
            $seconds_until_next_hour = $next_hour->diffInSeconds($now);
            return \time() + $seconds_until_next_hour;
        }
        return \time() + \wp_get_schedules()[$interval_id]['interval'];
    }
    public function should_execute_handler() : bool
    {
        return \true;
    }
    public static function register_custom_intervals() : void
    {
        \add_filter('cron_schedules', function ($schedules) {
            $schedules['monthly'] = ['interval' => \MONTH_IN_SECONDS, 'display' => 'Once a Month'];
            $schedules['five_minutes'] = ['interval' => 300, 'display' => 'Every 5 minutes'];
            $schedules['every_minute'] = ['interval' => 60, 'display' => 'Every minute'];
            return $schedules;
        });
    }
}
