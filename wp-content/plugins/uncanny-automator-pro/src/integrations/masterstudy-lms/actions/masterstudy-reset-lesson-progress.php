<?php

namespace Uncanny_Automator_Pro;

/**
 * Class MASTERSTUDY_RESET_LESSON_PROGRESS
 * @package Uncanny_Automator
 */
class MASTERSTUDY_RESET_LESSON_PROGRESS {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'MSLMS';

	private $action_code;
	private $action_meta;

	/**
	 * Set up Automator action constructor.
	 */
	public function __construct() {
		$this->action_code = 'MSLMSRESETLESSONPROGRESS';
		$this->action_meta = 'MSLMSLESSON';
		$this->define_action();
	}

	/**
	 * Define and register the action by pushing it into the Automator object
	 */
	public function define_action() {

		global $uncanny_automator;

		$args = [
			'post_type'      => 'stm-courses',
			'posts_per_page' => 999,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'post_status'    => 'publish',
		];

		$options = $uncanny_automator->helpers->recipe->options->wp_query( $args, false );

		$action = array(
			'author'             => $uncanny_automator->get_author_name(),
			'support_link'       => $uncanny_automator->get_author_support_link( $this->action_code, 'integration/masterstudy-lms/' ),
			'integration'        => self::$integration,
			'code'               => $this->action_code,
			'is_pro'             => true,
			/* translators: Action - MasterStudy LMS */
			'sentence'           => sprintf( esc_attr__( 'Mark {{a lesson:%1$s}} not complete for the user', 'uncanny-automator-pro' ), $this->action_meta ),
			/* translators: Action - MasterStudy LMS */
			'select_option_name' => esc_attr__( 'Mark {{a lesson}} not complete for the user', 'uncanny-automator-pro' ),
			'priority'           => 10,
			'accepted_args'      => 3,
			'execution_function' => array( $this, 'reset_lesson' ),
			'options'            => [],
			'options_group'      => [
				$this->action_meta => [
					$uncanny_automator->helpers->recipe->field->select_field_ajax(
						'MSLMSCOURSE',
						esc_attr_x( 'Course', 'MasterStudy LMS', 'uncanny-automator' ),
						$options,
						'',
						'',
						false,
						true,
						[
							'target_field' => $this->action_meta,
							'endpoint'     => 'select_mslms_lesson_from_course_x',
						]
					),
					$uncanny_automator->helpers->recipe->field->select_field( $this->action_meta, esc_attr_x( 'Lesson', 'MasterStudy LMS', 'uncanny-automator' ), [], false, false, false ),
				],
			]
		);

		$uncanny_automator->register->action( $action );
	}

	/**
	 * Validation function when the action is hit
	 *
	 * @param $user_id
	 * @param $action_data
	 * @param $recipe_id
	 */
	public function reset_lesson( $user_id, $action_data, $recipe_id, $args ) {
		global $uncanny_automator;

		$course_id = $action_data['meta']['MSLMSCOURSE'];
		$lesson_id = $action_data['meta'][ $this->action_meta ];

		/*Check if lesson in course*/
		$curriculum = get_post_meta( $course_id, 'curriculum', true );

		if ( ! empty( $curriculum ) ) {
			$curriculum = \STM_LMS_Helpers::only_array_numbers( explode( ',', $curriculum ) );

			$curriculum_posts = get_posts( [
				'post__in'       => $curriculum,
				'posts_per_page' => 999,
				'post_type'      => array( 'stm-lessons', 'stm-quizzes' ),
				'post_status'    => 'publish',
			] );

			if ( ! empty( $curriculum_posts ) ) {

				$curriculum = get_post_meta( $course_id, 'curriculum', true );

				if ( empty( $curriculum ) ) {
					$uncanny_automator->complete_action( $user_id, $action_data, $recipe_id );
				} else {
					$curriculum = explode( ',', $curriculum );

					foreach ( $curriculum as $item_id ) {

						$item_type = get_post_type( $item_id );

						if ( $item_type === 'stm-lessons' ) {
							if ( $item_id == $lesson_id ) {
								\STM_LMS_User_Manager_Course_User::reset_lesson( $user_id, $course_id, $item_id );
							}
						}
					}

					\STM_LMS_Course::update_course_progress( $user_id, $course_id );


				}
			}
		}
		$uncanny_automator->complete_action( $user_id, $action_data, $recipe_id );
	}
}
