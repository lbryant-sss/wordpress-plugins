<?php

defined('ABSPATH') || exit;

/**
 * Description of NewsletterEngine
 */
class NewsletterEngine {

    static $instance = null;
    var $logger = null;
    var $max_emails = null;
    var $time_limit = 0;
    var $options = [];

    /**
     *
     * @return NewsletterEngine
     */
    static function instance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function __construct() {
        $this->logger = new NewsletterLogger('engine');
        $this->options = Newsletter::instance()->get_main_options(); // Language indipendent main options
    }

    function run() {
        $this->logger->debug('START');

        if (!$this->set_lock(NEWSLETTER_CRON_INTERVAL * 1.2)) {
            $this->logger->fatal('Delivery engine lock already set: can be due to concurrent executions or fatal error during delivery');
            return;
        }

        $emails = $this->get_results("select * from " . NEWSLETTER_EMAILS_TABLE . " where status='sending' and send_on<=" . time() . " order by send_on asc");

        $this->logger->debug(count($emails) . ' newsletter to be processed');

        foreach ($emails as $email) {

            $this->update_email_total($email);

            $r = $this->send($email);

            if (!$r) {
                break;
            }
        }

        $this->reset_lock();

        $this->logger->debug('END');
    }

    /**
     * Sends an newsletter to targeted users or to given users.
     *
     * With given subscribers, all of them are processed. If the method returns "false"
     * it means there are no more room to send other messages. If it returns "true", there is
     * room for more messages. This behavior can be used for external batching.
     *
     * With given subscribers, the sending time window specified on the main settings is
     * ignored.
     *
     * Without given subscribers, they're loaded using the $email targeting data.
     * The max number of messages sent is based on the delivery speed and computed internally.
     * If the method returns true, it means it was not able to use all the capacity and can be
     * called again (for example when a newsletter is completed and the next one can be started).
     *
     * Without given subscribers, the sending time window specified on the main settings is
     * respected.
     *
     * @global wpdb $wpdb
     * @param TNP_Email $email The object type is only specified for the IDE autocompletion
     * @param TNP_User[] $users The object type is only specified for the IDE autocompletion
     * @param bool $test If true no stats are collected and the progress is not registered
     * @return boolean|WP_Error True if the process completed, false if limits was reached. On false the caller should no continue to call it with other emails.
     */
    function send($email, $users = null, $test = false) {
        global $wpdb;

        if (is_array($email)) {
            $email = (object) $email;
        }

        $this->logger->info('Starting newsletter ' . $email->id);

        $this->send_setup();

        if ($this->max_emails <= 0) {
            $this->logger->info('No more capacity');
            return false;
        }

        $this->fix_email($email);

        // This stops the update of last_id and sent fields since
        // it's not a scheduled delivery but a test or something else (like an autoresponder)
        $supplied_users = $users != null;

        if (!$supplied_users) {

            if ($this->skip_run($email)) {
                $this->logger->info('Out of the sending time window');
                return true;
            }

            $query = $email->query;
            $query .= " and id>" . ((int) $email->last_id) . " order by id limit " . $this->max_emails;

            $this->logger->debug('Query: ' . $query);

            $users = $this->get_results($query);

            if ($users === false) {
                $this->set_error_state_of_email($email, 'Database error (see logs)');
                return true; // Continue with the next newsletter
            }

            $this->logger->debug(count($users) . ' subscribers loaded');

            if (empty($users)) {
                $this->logger->info('No more subscribers set as "sent"');
                $wpdb->query("update " . NEWSLETTER_EMAILS_TABLE . " set status='sent', total=sent where id=" . ((int) $email->id) . " limit 1");
                do_action('newsletter_send_end', $email);
                return true;
            }
        } else {
            $this->logger->info(count($users) . ' subscribers supplied');
        }

        $start_time = microtime(true);
        $count = 0;
        $result = true;

        $mailer = Newsletter::instance()->get_mailer();

        $batch_size = $mailer->get_batch_size();
        if (NEWSLETTER_DEBUG) $batch_size = 2;

        $this->logger->debug('Batch size ' . $batch_size);

        $delay = $this->get_send_delay();

        $this->logger->debug('Delay set to ' . $delay . ' ms');

        // Content optimisations (not to be saved!)
        $email->message = preg_replace('/data-json=".*?"/is', '', $email->message);
        $email->message = preg_replace('/  +/s', ' ', $email->message);

        // When the batch size is 1, chunks is a list of arrays with only one element (a single subscriber)
        // so we can use the same code for both single subscriber a multi-subscriber batches.
        $chunks = array_chunk($users, $batch_size);

        $this->logger->debug(count($chunks) . ' chunks to process');


        foreach ($chunks as $index=>$chunk) {


            $this->logger->debug('Processing chunk #' . $index);

            $messages = [];

            // Peeparing a batch of messages
            foreach ($chunk as $user) {

                $this->logger->debug('Processing user # ' . $user->id);

                $user = apply_filters('newsletter_send_user', $user);
                if (!NewsletterModuleBase::is_email($user->email)) {
                    $this->logger->error('Subscriber ' . $user->id . ' with invalid email');
                    continue;
                }
                $message = $this->build_message($email, $user);
                $this->save_sent_message($message);
                $messages[] = $message;

                if (!$test) {
                    $wpdb->query("update " . NEWSLETTER_EMAILS_TABLE . " set sent=sent+1, last_id=" . $user->id . " where id=" . $email->id . " limit 1");
                }
                $this->max_emails--;
                $count++;
            }

            // This is an optimisation, the mailer should already manage correctly a batch with a single message
            if (count($messages) === 1) {
                $r = $mailer->send($messages[0]);
            } else {
                $r = $mailer->send_batch($messages);
            }

            // Updating the status of the sent messages
            foreach ($messages as $message) {
                if (!empty($message->error)) {
                    $this->save_sent_message($message);
                }
            }

            // The batch went in error
            if (is_wp_error($r)) {
                $this->logger->error($r);

                if (!$supplied_users && !$test && $r->get_error_code() == NewsletterMailer::ERROR_FATAL) {
                    $this->set_error_state_of_email($email, $r->get_error_message());
                    $this->notify_fatal_error($email, $r->get_error_message());
                    return $r;
                }
            }

            if (!$supplied_users && !$test && $this->time_exceeded()) {
                $result = false;
                break;
            }

            if ($delay) {
                usleep($delay * 1000);
            }

            unset($messages);
            gc_collect_cycles();
        }

        $end_time = microtime(true);

        // Stats only for newsletter with enough emails in a batch (we exclude the Autoresponder since it send one email per call)
        if (!$test && !$supplied_users && $count > 5) {
            $this->update_send_stats($start_time, $end_time, $count, $result);
        }

        // Cached general statistics are reset
        if (!$test) {
            NewsletterStatistics::instance()->reset_stats_time($email->id);
        }

        $this->logger->info('End run for email ' . $email->id);

        return $result;
    }

    function send_setup() {
        $this->logger->debug('Setting up (once)');
        if (is_null($this->max_emails)) {
            // It's ok to get it from Newsletter, it's the only thing we need
            $this->max_emails = Newsletter::instance()->get_emails_per_run();
            $this->logger->debug('Max emails: ' . $this->max_emails);
            ignore_user_abort(true);

            @set_time_limit(NEWSLETTER_CRON_INTERVAL + 30);

            $max_time = (int) (@ini_get('max_execution_time') * 0.95);
            if ($max_time == 0 || $max_time > NEWSLETTER_CRON_INTERVAL) {
                $max_time = (int) (NEWSLETTER_CRON_INTERVAL * 0.95);
            }

            // time_start is when the plugin has been loaded, but other task could have been executed and
            // we may have less seconds to use
            // TODO: Create a loaded time function or the like
            $this->time_limit = Newsletter::instance()->time_start + $max_time;

            $this->logger->debug('Max time set to ' . $max_time);
        } else {
            $this->logger->debug('Already setup');
        }
    }

    /**
     *
     * @param TNP_Email $email
     * @param TNP_User $user
     * @return \TNP_Mailer_Message
     */
    function build_message($email, $user) {

        $message = new TNP_Mailer_Message();

        $message->to = $user->email;

        $message->headers = [
            'Precedence' => 'bulk',
            'X-Newsletter-Email-Id' => (string) $email->id,
            'X-Auto-Response-Suppress' => 'OOF, AutoReply'
        ];

        $message->headers = apply_filters('newsletter_message_headers', $message->headers, $email, $user);

        $message->body = Newsletter::instance()->replace_for_email($email->message, $user, $email);
        $message->body = do_shortcode($message->body);

        $message->body_text = Newsletter::instance()->replace($email->message_text, $user, $email);
        $message->body_text = apply_filters('newsletter_message_text', $message->body_text, $email, $user);

        if ($email->track == 1) {
            $message->body = NewsletterStatistics::instance()->relink($message->body, $email->id, $user->id, $email->token);
        }

        if (empty($email->subject)) {
            $message->subject = '[no subject]';
        } else {
            $message->subject = Newsletter::instance()->replace($email->subject, $user, $email);
        }

        if (!empty($email->options['sender_email'])) {
            $message->from = $email->options['sender_email'];
        } else {
            $message->from = Newsletter::instance()->get_sender_email();
        }

        if (!empty($email->options['sender_name'])) {
            $message->from_name = $email->options['sender_name'];
        } else {
            $message->from_name = Newsletter::instance()->get_sender_name();
        }

        $message->email_id = $email->id;
        $message->user_id = $user->id;

        return apply_filters('newsletter_message', $message, $email, $user);
    }

    /**
     * @param TNP_Mailer_Message $message
     */
    function save_sent_message($message) {
        global $wpdb;

        // Usually tests
        if (!$message->user_id || !$message->email_id) {
            return;
        }

        $status = empty($message->error) ? 0 : 1;

        $error = mb_substr($message->error, 0, 250);

        $this->query($wpdb->prepare("insert into " . $wpdb->prefix . 'newsletter_sent (user_id, email_id, time, status, error) values (%d, %d, %d, %d, %s) on duplicate key update time=%d, status=%d, error=%s',
                        $message->user_id, $message->email_id, time(), $status, $error, time(), $status, $error));
    }

    /**
     * @param TNP_Email $email
     */
    private function set_error_state_of_email($email, $message = '') {
        // Handle only message type at the moment
        //if ($email->type !== 'message') {
        //    return;
        //}

        do_action('newsletter_error_on_sending', $email, $message);

        $edited_email = new stdClass();
        $edited_email->id = $email->id;
        $edited_email->status = TNP_Email::STATUS_ERROR;
        $edited_email->options = $email->options;
        $edited_email->options['error_message'] = $message;

        Newsletter::instance()->save_email($edited_email);

        Newsletter\Logs::add('newsletter-' . $email->id, 'Error: ' . $message);
    }

    /**
     * Attempts to notify a sending fatal error usding wp_mail().
     *
     * The function does not usethe current mailer, since the connected service is probably
     * not working.
     *
     * @param TNP_Email $email
     * @param string $error
     */
    function notify_fatal_error($email, $error) {
        $to = get_option('admin_email');
        $title = get_option('blogname');
        wp_mail($to, '[' . $title . '] Fatal error while sending a newsletter',
                'Please check the Newsletter plugin Help/Sending for more details. The error description is: ' . $error);
    }

    function fix_email($email) {
        if (empty($email->query)) {
            $email->query = "select * from " . NEWSLETTER_USERS_TABLE . " where status='C'";
        }
        if (empty($email->id)) {
            $email->id = '0'; // As string, it's ok, compatible with WP query results
        }
        $email->options = maybe_unserialize($email->options);
    }

    function skip_run($email = null) {
        $skip = false;

        $schedule = (int) $this->options['schedule'];

        $this->logger->debug('Global schedule: ' . ($schedule ? 'yes' : 'no'));

        if ($schedule) {

            $hour = gmdate('G') + get_option('gmt_offset');
            $start = (int) $this->options['schedule_start'];
            $end = (int) $this->options['schedule_end'];
            $end--; // Stop at the starting of the hour

            $this->logger->debug('Start: ' . $start);
            $this->logger->debug('End: ' . $end);

            $skip = $hour < $start || $hour > $end;
            $this->logger->debug('Skip: ' . ($skip ? 'true' : 'false'));
        }

        // Used by the speed control obsolete addon
        return (bool) apply_filters('newsletter_send_skip', $skip, $email);
    }

    /**
     * Returns the delay in milliseconds between emails to respect a per second max speed.
     *
     * @return int Milliseconds
     */
    function get_send_delay() {
        if (defined('NEWSLETTER_SEND_DELAY')) {
            return (int) NEWSLETTER_SEND_DELAY;
        }
        $max = (float) $this->options['max_per_second'];
        if ($max > 0) {
            return (int) (1000 / $max);
        }
        return 0;
    }

    function time_exceeded() {
        if ($this->time_limit && time() > $this->time_limit) {
            $this->logger->info('Max execution time limit reached');
            return true;
        }
        return false;
    }

    function update_send_stats($start_time, $end_time, $count, $result) {
        $send_calls = NewsletterModuleBase::get_option_array('newsletter_diagnostic_send_calls', []);
        $send_calls[] = [$start_time, $end_time, $count, $result];

        if (count($send_calls) > 100) {
            array_shift($send_calls);
        }

        update_option('newsletter_diagnostic_send_calls', $send_calls, false);
    }

    /**
     *
     * @global wpdb $wpdb
     * @param TNP_Email $email
     */
    function update_email_total($email) {
        global $wpdb;
        $total = (int) $wpdb->get_var(str_replace('*', 'count(*)', $email->query));
        if ($total > $email->total) {
            $wpdb->update(NEWSLETTER_EMAILS_TABLE, ['total' => $total], ['id' => $email->id], ['%d', '%d'], ['%d']);
            $email->total = $total;
        }
    }

    function query($query) {
        global $wpdb;

        //$this->logger->debug($query);
        $r = $wpdb->query($query);
        if ($r === false) {
            $this->logger->fatal($query);
            $this->logger->fatal($wpdb->last_error);
        }
        return $r;
    }

    function get_results($query) {
        global $wpdb;
        $r = $wpdb->get_results($query);
        if ($r === false) {
            $this->logger->fatal($query);
            $this->logger->fatal($wpdb->last_error);
        }
        return $r;
    }

    function set_lock($duration) {
        global $wpdb;

        $duration = (int) $duration;

        $wpdb->flush();
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->options WHERE option_name = %s LIMIT 1", 'newsletter_lock_engine'));
        if ($row) {
            $value = (int) $row->option_value;
            if ($value < time()) {
                $wpdb->query($wpdb->prepare("update $wpdb->options set option_value=%s where option_id=%d limit 1", '' . (time() + $duration), $row->option_id));
                $wpdb->flush();
                return true;
            }
            return false;
        }
        $wpdb->insert($wpdb->options, ['option_name' => 'newsletter_lock_engine', 'option_value' => '' . (time() + $duration)]);
        $wpdb->flush();
        return true;
    }

    function reset_lock() {
        global $wpdb;
        $wpdb->query($wpdb->prepare("update $wpdb->options set option_value=%s where option_name=%s limit 1", '0', 'newsletter_lock_engine'));
        $wpdb->flush();
    }
}
