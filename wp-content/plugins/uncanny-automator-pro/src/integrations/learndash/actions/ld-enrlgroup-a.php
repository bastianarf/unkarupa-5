<?php

namespace Uncanny_Automator_Pro;

/**
 * Class LD_MARKLESSONDONE
 * @package Uncanny_Automator_Pro
 */
class LD_ENRLGROUP_A {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'LD';

	private $action_code;
	private $action_meta;

	/**
	 * Set up Automator action constructor.
	 */
	public function __construct() {
		$this->action_code = 'ENRLGROUP_A';
		$this->action_meta = 'LDGROUP';
		$this->define_action();
	}

	/**
	 * Define and register the action by pushing it into the Automator object
	 */
	public function define_action() {
		global $uncanny_automator;
		$action = array(
			'author'             => $uncanny_automator->get_author_name(),
			'support_link'       => $uncanny_automator->get_author_support_link( $this->action_code, 'integration/learndash/' ),
			'is_pro'             => true,
			'integration'        => self::$integration,
			'code'               => $this->action_code,
			/* translators: Action - LearnDash */
			'sentence'           => sprintf( __( 'Add the user to {{a group:%1$s}}', 'uncanny-automator-pro' ), $this->action_meta ),
			/* translators: Action - LearnDash */
			'select_option_name' => __( 'Add the user to {{a group}}', 'uncanny-automator-pro' ),
			'priority'           => 10,
			'accepted_args'      => 1,
			'execution_function' => array( $this, 'enrol_in_to_group' ),
			'options'            => [
				$uncanny_automator->helpers->recipe->learndash->options->all_ld_groups( null, 'LDGROUP', false, false ),
			],
		);

		$uncanny_automator->register->action( $action );
	}

	/**
	 * @param $user_id
	 * @param $action_data
	 * @param $recipe_id
	 */
	public function enrol_in_to_group( $user_id, $action_data, $recipe_id, $args ) {

		global $uncanny_automator;
		$group_id = $action_data['meta'][ $this->action_meta ];
		ld_update_group_access( $user_id, $group_id );
		$uncanny_automator->complete_action( $user_id, $action_data, $recipe_id );
	}
}
