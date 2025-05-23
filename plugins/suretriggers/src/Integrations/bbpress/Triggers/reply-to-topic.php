<?php
/**
 * ReplyToTopic.
 * php version 5.6
 *
 * @category ReplyToTopic
 * @package  SureTriggers
 * @author   BSF <username@example.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @link     https://www.brainstormforce.com/
 * @since    1.0.0
 */

namespace SureTriggers\Integrations\BbPress\Triggers;

use SureTriggers\Controllers\AutomationController;
use SureTriggers\Integrations\WordPress\WordPress;
use SureTriggers\Traits\SingletonLoader;

if ( ! class_exists( 'ReplyToTopic' ) ) :
	/**
	 * ReplyToTopic
	 *
	 * @category ReplyToTopic
	 * @package  SureTriggers
	 * @author   BSF <username@example.com>
	 * @license  https://www.gnu.org/licenses/gpl-3.0.html GPLv3
	 * @link     https://www.brainstormforce.com/
	 * @since    1.0.0
	 *
	 * @psalm-suppress UndefinedTrait
	 */
	class ReplyToTopic {

		use SingletonLoader;

		/**
		 * Integration type.
		 *
		 * @var string
		 */
		public $integration = 'bbPress';

		/**
		 * Trigger name.
		 *
		 * @var string
		 */
		public $trigger = 'bbpress_reply_to_topic';

		use SingletonLoader;

		/**
		 * Constructor
		 *
		 * @since  1.0.0
		 */
		public function __construct() {
			add_filter( 'sure_trigger_register_trigger', [ $this, 'register' ] );
		}

		/**
		 * Register action.
		 *
		 * @param array $triggers triggers.
		 *
		 * @return array
		 */
		public function register( $triggers ) {
			$triggers[ $this->integration ][ $this->trigger ] = [
				'label'         => __( 'Reply To Topic.', 'suretriggers' ),
				'action'        => $this->trigger,
				'common_action' => 'bbp_new_reply',
				'function'      => [ $this, 'trigger_listener' ],
				'priority'      => 10,
				'accepted_args' => 5,
			];

			return $triggers;
		}

		/**
		 *  Trigger listener
		 *
		 * @param int   $reply_id reply id.
		 * @param int   $topic_id topic id.
		 * @param int   $forum_id forum id.
		 * @param array $anonymous_data $anonymous_data.
		 * @param int   $user_id $user_id.
		 *
		 * @return void
		 */
		public function trigger_listener( $reply_id, $topic_id, $forum_id, $anonymous_data, $user_id ) {
			$topic             = get_the_title( $topic_id );
			$topic_link        = get_the_permalink( $topic_id );
			$topic_description = get_the_content( null, false, $topic_id );
			$topic_status      = get_post_status( $topic_id );

			$forum             = get_the_title( $forum_id );
			$forum_link        = get_the_permalink( $forum_id );
			$forum_description = get_the_content( null, false, $forum_id );
			$forum_status      = get_post_status( $forum_id );

			$reply             = get_the_title( $reply_id );
			$reply_link        = get_the_permalink( $reply_id );
			$reply_description = get_the_content( null, false, $reply_id );
			$reply_status      = get_post_status( $reply_id );

			$forum = [
				'forum'             => $forum_id,
				'forum_title'       => $forum,
				'forum_link'        => $forum_link,
				'forum_description' => $forum_description,
				'forum_status'      => $forum_status,
			];

			$topic = [
				'topic'             => $topic_id,
				'topic_title'       => $topic,
				'topic_link'        => $topic_link,
				'topic_description' => $topic_description,
				'topic_status'      => $topic_status,
			];
			$reply = [
				'reply_title'       => $reply,
				'reply_link'        => $reply_link,
				'reply_description' => $reply_description,
				'reply_status'      => $reply_status,
			];

			
			if ( is_int( $user_id ) ) { 
				$user_context = WordPress::get_user_context( $user_id );
				$context      = [];
			
				if ( is_array( $user_context ) ) {
					$context = array_merge( $context, $user_context );
				}
				if ( is_array( $forum ) ) {
					$context = array_merge( $context, $forum );
				}
				if ( is_array( $topic ) ) {
					$context = array_merge( $context, $topic );
				}
				if ( is_array( $reply ) ) {
					$context = array_merge( $context, $reply );
				}
				if ( is_array( $anonymous_data ) ) {
					$context = array_merge( $context, $anonymous_data );
				}
			} else {
				$anonymous_data = [
					'bbp_anonymous_name'    => get_post_meta( $reply_id, '_bbp_anonymous_name', true ),
					'bbp_anonymous_email'   => get_post_meta( $reply_id, '_bbp_anonymous_email', true ),
					'bbp_anonymous_website' => get_post_meta( $reply_id, '_bbp_anonymous_website', true ),
				];
				$context        = [];
				if ( is_array( $forum ) ) {
					$context = array_merge( $context, $forum );
				}
				if ( is_array( $topic ) ) {
					$context = array_merge( $context, $topic );
				}
				if ( is_array( $reply ) ) {
					$context = array_merge( $context, $reply );
				}
				if ( is_array( $anonymous_data ) ) {
					$context = array_merge( $context, $anonymous_data );
				}
			}
		
			AutomationController::sure_trigger_handle_trigger(
				[
					'trigger' => $this->trigger,
					'context' => $context,
				]
			);
		}
	}

	ReplyToTopic::get_instance();
endif;
