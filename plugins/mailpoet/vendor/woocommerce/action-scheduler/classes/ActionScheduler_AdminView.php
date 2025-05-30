<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_AdminView extends ActionScheduler_AdminView_Deprecated {
 private static $admin_view = null;
 private static $screen_id = 'tools_page_action-scheduler';
 protected $list_table;
 public static function instance() {
 if ( empty( self::$admin_view ) ) {
 $class = apply_filters( 'action_scheduler_admin_view_class', 'ActionScheduler_AdminView' );
 self::$admin_view = new $class();
 }
 return self::$admin_view;
 }
 public function init() {
 if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
 if ( class_exists( 'WooCommerce' ) ) {
 add_action( 'woocommerce_admin_status_content_action-scheduler', array( $this, 'render_admin_ui' ) );
 add_action( 'woocommerce_system_status_report', array( $this, 'system_status_report' ) );
 add_filter( 'woocommerce_admin_status_tabs', array( $this, 'register_system_status_tab' ) );
 }
 add_action( 'admin_menu', array( $this, 'register_menu' ) );
 add_action( 'admin_notices', array( $this, 'maybe_check_pastdue_actions' ) );
 add_action( 'current_screen', array( $this, 'add_help_tabs' ) );
 }
 }
 public function system_status_report() {
 $table = new ActionScheduler_wcSystemStatus( ActionScheduler::store() );
 $table->render();
 }
 public function register_system_status_tab( array $tabs ) {
 $tabs['action-scheduler'] = __( 'Scheduled Actions', 'action-scheduler' );
 return $tabs;
 }
 public function register_menu() {
 $hook_suffix = add_submenu_page(
 'tools.php',
 __( 'Scheduled Actions', 'action-scheduler' ),
 __( 'Scheduled Actions', 'action-scheduler' ),
 'manage_options',
 'action-scheduler',
 array( $this, 'render_admin_ui' )
 );
 add_action( 'load-' . $hook_suffix, array( $this, 'process_admin_ui' ) );
 }
 public function process_admin_ui() {
 $this->get_list_table();
 }
 public function render_admin_ui() {
 $table = $this->get_list_table();
 $table->display_page();
 }
 protected function get_list_table() {
 if ( null === $this->list_table ) {
 $this->list_table = new ActionScheduler_ListTable( ActionScheduler::store(), ActionScheduler::logger(), ActionScheduler::runner() );
 $this->list_table->process_actions();
 }
 return $this->list_table;
 }
 public function maybe_check_pastdue_actions() {
 // Filter to prevent checking actions (ex: inappropriate user).
 if ( ! apply_filters( 'action_scheduler_check_pastdue_actions', current_user_can( 'manage_options' ) ) ) {
 return;
 }
 // Get last check transient.
 $last_check = get_transient( 'action_scheduler_last_pastdue_actions_check' );
 // If transient exists, we're within interval, so bail.
 if ( ! empty( $last_check ) ) {
 return;
 }
 // Perform the check.
 $this->check_pastdue_actions();
 }
 protected function check_pastdue_actions() {
 // Set thresholds.
 $threshold_seconds = (int) apply_filters( 'action_scheduler_pastdue_actions_seconds', DAY_IN_SECONDS );
 $threshold_min = (int) apply_filters( 'action_scheduler_pastdue_actions_min', 1 );
 // Set fallback value for past-due actions count.
 $num_pastdue_actions = 0;
 // Allow third-parties to preempt the default check logic.
 $check = apply_filters( 'action_scheduler_pastdue_actions_check_pre', null );
 // If no third-party preempted and there are no past-due actions, return early.
 if ( ! is_null( $check ) ) {
 return;
 }
 // Scheduled actions query arguments.
 $query_args = array(
 'date' => as_get_datetime_object( time() - $threshold_seconds ),
 'status' => ActionScheduler_Store::STATUS_PENDING,
 'per_page' => $threshold_min,
 );
 // If no third-party preempted, run default check.
 if ( is_null( $check ) ) {
 $store = ActionScheduler_Store::instance();
 $num_pastdue_actions = (int) $store->query_actions( $query_args, 'count' );
 // Check if past-due actions count is greater than or equal to threshold.
 $check = ( $num_pastdue_actions >= $threshold_min );
 $check = (bool) apply_filters( 'action_scheduler_pastdue_actions_check', $check, $num_pastdue_actions, $threshold_seconds, $threshold_min );
 }
 // If check failed, set transient and abort.
 if ( ! boolval( $check ) ) {
 $interval = apply_filters( 'action_scheduler_pastdue_actions_check_interval', round( $threshold_seconds / 4 ), $threshold_seconds );
 set_transient( 'action_scheduler_last_pastdue_actions_check', time(), $interval );
 return;
 }
 $actions_url = add_query_arg(
 array(
 'page' => 'action-scheduler',
 'status' => 'past-due',
 'order' => 'asc',
 ),
 admin_url( 'tools.php' )
 );
 // Print notice.
 echo '<div class="notice notice-warning"><p>';
 printf(
 wp_kses(
 // translators: 1) is the number of affected actions, 2) is a link to an admin screen.
 _n(
 '<strong>Action Scheduler:</strong> %1$d <a href="%2$s">past-due action</a> found; something may be wrong. <a href="https://actionscheduler.org/faq/#my-site-has-past-due-actions-what-can-i-do" target="_blank">Read documentation &raquo;</a>',
 '<strong>Action Scheduler:</strong> %1$d <a href="%2$s">past-due actions</a> found; something may be wrong. <a href="https://actionscheduler.org/faq/#my-site-has-past-due-actions-what-can-i-do" target="_blank">Read documentation &raquo;</a>',
 $num_pastdue_actions,
 'action-scheduler'
 ),
 array(
 'strong' => array(),
 'a' => array(
 'href' => true,
 'target' => true,
 ),
 )
 ),
 absint( $num_pastdue_actions ),
 esc_attr( esc_url( $actions_url ) )
 );
 echo '</p></div>';
 // Facilitate third-parties to evaluate and print notices.
 do_action( 'action_scheduler_pastdue_actions_extra_notices', $query_args );
 }
 public function add_help_tabs() {
 $screen = get_current_screen();
 if ( ! $screen || self::$screen_id !== $screen->id ) {
 return;
 }
 $as_version = ActionScheduler_Versions::instance()->latest_version();
 $as_source = ActionScheduler_SystemInformation::active_source();
 $as_source_path = ActionScheduler_SystemInformation::active_source_path();
 $as_source_markup = sprintf( '<code>%s</code>', esc_html( $as_source_path ) );
 if ( ! empty( $as_source ) ) {
 $as_source_markup = sprintf(
 '%s: <abbr title="%s">%s</abbr>',
 ucfirst( $as_source['type'] ),
 esc_attr( $as_source_path ),
 esc_html( $as_source['name'] )
 );
 }
 $screen->add_help_tab(
 array(
 'id' => 'action_scheduler_about',
 'title' => __( 'About', 'action-scheduler' ),
 'content' =>
 // translators: %s is the Action Scheduler version.
 '<h2>' . sprintf( __( 'About Action Scheduler %s', 'action-scheduler' ), $as_version ) . '</h2>' .
 '<p>' .
 __( 'Action Scheduler is a scalable, traceable job queue for background processing large sets of actions. Action Scheduler works by triggering an action hook to run at some time in the future. Scheduled actions can also be scheduled to run on a recurring schedule.', 'action-scheduler' ) .
 '</p>' .
 '<h3>' . esc_html__( 'Source', 'action-scheduler' ) . '</h3>' .
 '<p>' .
 esc_html__( 'Action Scheduler is currently being loaded from the following location. This can be useful when debugging, or if requested by the support team.', 'action-scheduler' ) .
 '</p>' .
 '<p>' . $as_source_markup . '</p>' .
 '<h3>' . esc_html__( 'WP CLI', 'action-scheduler' ) . '</h3>' .
 '<p>' .
 sprintf(
 esc_html__( 'WP CLI commands are available: execute %1$s for a list of available commands.', 'action-scheduler' ),
 '<code>wp help action-scheduler</code>'
 ) .
 '</p>',
 )
 );
 $screen->add_help_tab(
 array(
 'id' => 'action_scheduler_columns',
 'title' => __( 'Columns', 'action-scheduler' ),
 'content' =>
 '<h2>' . __( 'Scheduled Action Columns', 'action-scheduler' ) . '</h2>' .
 '<ul>' .
 sprintf( '<li><strong>%1$s</strong>: %2$s</li>', __( 'Hook', 'action-scheduler' ), __( 'Name of the action hook that will be triggered.', 'action-scheduler' ) ) .
 sprintf( '<li><strong>%1$s</strong>: %2$s</li>', __( 'Status', 'action-scheduler' ), __( 'Action statuses are Pending, Complete, Canceled, Failed', 'action-scheduler' ) ) .
 sprintf( '<li><strong>%1$s</strong>: %2$s</li>', __( 'Arguments', 'action-scheduler' ), __( 'Optional data array passed to the action hook.', 'action-scheduler' ) ) .
 sprintf( '<li><strong>%1$s</strong>: %2$s</li>', __( 'Group', 'action-scheduler' ), __( 'Optional action group.', 'action-scheduler' ) ) .
 sprintf( '<li><strong>%1$s</strong>: %2$s</li>', __( 'Recurrence', 'action-scheduler' ), __( 'The action\'s schedule frequency.', 'action-scheduler' ) ) .
 sprintf( '<li><strong>%1$s</strong>: %2$s</li>', __( 'Scheduled', 'action-scheduler' ), __( 'The date/time the action is/was scheduled to run.', 'action-scheduler' ) ) .
 sprintf( '<li><strong>%1$s</strong>: %2$s</li>', __( 'Log', 'action-scheduler' ), __( 'Activity log for the action.', 'action-scheduler' ) ) .
 '</ul>',
 )
 );
 }
}
