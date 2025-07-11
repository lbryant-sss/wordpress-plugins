<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

/**
 * WordPress cron substitute
 *
 * @author realmag777
 * @site https://pluginus.net
 */

final class PN_WP_CRON_WOOCS {

    public $actions = array();
    public $cron_key = null;

    public function __construct($key)
    {
        $this->cron_key = $key;
        $this->actions = get_option($this->cron_key, array());
    }

    public function process()
    {
        if (!empty($this->actions))
        {
            $now = time();
            foreach ($this->actions as $action_hook => $event)
            {
                if ($event['next'] <= $now)
                {
                    $_REQUEST['woocs_cron_running'] = true;//just marker for another applications
                    do_action($action_hook);

                    if ((int) $event['recurrence'] > 0)
                    {
                        $event['next'] = $now + $event['recurrence'];
                        $this->actions[$action_hook] = $event;
                        $this->update();
                    } else
                    {
                        $this->remove($action_hook);
                    }
                }
            }
        }
    }

    public function attach($hook, $start_time, $recurrence)
    {
        //recurrence - is in seconds
        if ($recurrence > 0)
        {
            $next = $start_time + $recurrence;
            $this->actions[$hook] = array(
                'start_time' => $start_time,
                'next' => $next,
                'recurrence' => $recurrence
            );
            $this->update();
        }
    }

    public function reset($hook, $recurrence)
    {
        $this->attach($hook, time(), $recurrence);
    }

    public function is_attached($hook, $recurrence = 0)
    {

        if (isset($this->actions[$hook]) AND $recurrence !== 0)
        {
            if ((int) $this->actions[$hook]['recurrence'] !== $recurrence)
            {
                //if recurrence change - change it immediately in $this->actions array
                return false;
            }
        }


        return isset($this->actions[$hook]);
    }

    public function remove($hook)
    {
        unset($this->actions[$hook]);
        $this->update();
    }

    public function update()
    {
        update_option($this->cron_key, $this->actions);
    }

}
