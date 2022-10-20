<?php
/**
 * Contains Lesson Completion action.
 *
 * @since 2.3.0
 * @version 2.3.0
 *
 * @package Uncanny_Automator_Pro
 */

namespace Uncanny_Automator_Pro;

use function tutils;

/**
 * Lesson Completion Action.
 *
 * @since 2.3.0
 */
class TUTORLMS_LESSONCOMPLETE {

	/**
	 * Integration code
	 *
	 * @var string
	 * @since 2.3.0
	 */
	public static $integration = 'TUTORLMS';

	/**
	 * Action Code
	 *
	 * @var string
	 * @since 2.3.0
	 */
	private $action_code;

	/**
	 * Action Meta
	 *
	 * @var string
	 * @since 2.3.0
	 */
	private $action_meta;

	/**
	 * Set up Automator action constructor.
	 *
	 * @since 2.3.0
	 */
	public function __construct() {
		$this->action_code = 'TUTORLMSLESSONCOMPLETE';
		$this->action_meta = 'TUTORLMSLESSON';
		$this->define_action();
	}

	/**
	 * Register the action.
	 *
	 * @since 2.3.0
	 */
	public function define_action() {

		global $uncanny_automator;

		$action = array(
			'author'             => $uncanny_automator->get_author_name(),
			'support_link'       => $uncanny_automator->get_author_support_link( $this->action_code, 'integration/tutor-lms/' ),
			'is_pro'             => true,
			'integration'        => self::$integration,
			'code'               => $this->action_code,
			/* translators: Action - TutorLMS */
			'sentence'           => sprintf( __( 'Mark {{a lesson:%1$s}} complete for the user', 'uncanny-automator-pro' ), $this->action_meta ),
			/* translators: Action - TutorLMS */
			'select_option_name' => __( 'Mark {{a lesson}} complete for the user', 'uncanny-automator-pro' ),
			'priority'           => 10,
			'accepted_args'      => 1,
			'execution_function' => array( $this, 'complete' ),
			'options'            => [
				$uncanny_automator->helpers->recipe->tutorlms->options->all_tutorlms_lessons( null, $this->action_meta, false ),
			],
		);

		$uncanny_automator->register->action( $action );
	}

	/**
	 * Completes the Lesson Completion Action.
	 *
	 * @param int $user_id User ID
	 * @param array $action_data Action information
	 * @param int $recipe_id ID of the recipe
	 *
	 * @since 2.3.0
	 */
	public function complete( $user_id, $action_data, $recipe_id, $args ) {

		global $uncanny_automator;

		$lesson_id = $action_data['meta'][ $this->action_meta ];

		// otherwise, simply complete it.
		tutils()->mark_lesson_complete( $lesson_id, $user_id );

		// finally, wrap up the proceedings!
		$uncanny_automator->complete_action( $user_id, $action_data, $recipe_id );
	}

}
