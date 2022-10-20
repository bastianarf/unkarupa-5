<?php

namespace Uncanny_Automator_Pro;

/**
 * Class GTT_REGISTERUSER
 * @package Uncanny_Automator_Pro
 */
class GTT_REGISTERUSER {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'GTT';

	private $action_code;
	private $action_meta;

	/**
	 * Set up Automator action constructor.
	 */
	public function __construct() {
		$this->action_code = 'GTTREGISTERUSER';
		$this->action_meta = 'GTTTRAINING';
		$this->define_action();
	}

	/**
	 * Define and register the action by pushing it into the Automator object
	 */
	public function define_action() {

		global $uncanny_automator;

		$action = array(
			'author'             => $uncanny_automator->get_author_name( $this->action_code ),
			'support_link'       => $uncanny_automator->get_author_support_link( $this->action_code, 'knowledge-base/gototraining/' ),
			'is_pro'             => true,
			'integration'        => self::$integration,
			'code'               => $this->action_code,
			'sentence'           => sprintf( __( 'Add the user to {{a training session:%1$s}}', 'uncanny-automator-pro' ), $this->action_meta ),
			'select_option_name' => __( 'Add the user to {{a training session}}', 'uncanny-automator-pro' ),
			'priority'           => 10,
			'accepted_args'      => 1,
			'execution_function' => array( $this, 'gtt_register_user' ),
			'options'            => [
				$uncanny_automator->helpers->recipe->gototraining->pro->get_trainings( null, $this->action_meta ),
			],
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
	public function gtt_register_user( $user_id, $action_data, $recipe_id, $args ) {

		global $uncanny_automator;
		$training_key = $uncanny_automator->parse->text( $action_data['meta'][ $this->action_meta ], $recipe_id, $user_id, $args );
		if ( empty( $user_id ) ) {
			$error_msg                           = __( 'User not found.', 'uncanny-automator-pro' );
			$action_data['do-nothing']           = true;
			$action_data['complete_with_errors'] = true;
			$uncanny_automator->complete_action( $user_id, $action_data, $recipe_id, $error_msg );

			return;
		}

		if ( empty( $training_key ) ) {
			$error_msg                           = __( 'Training not found.', 'uncanny-automator-pro' );
			$action_data['do-nothing']           = true;
			$action_data['complete_with_errors'] = true;
			$uncanny_automator->complete_action( $user_id, $action_data, $recipe_id, $error_msg );

			return;
		}

		if ( ! empty( $training_key ) ) {
			$training_key = str_replace( '-objectkey', '', $training_key );
		}

		$result = $uncanny_automator->helpers->recipe->gototraining->pro->gtt_register_user( $user_id, $training_key );
		if ( ! $result['result'] ) {
			$error_msg                           = $result['message'];
			$action_data['do-nothing']           = true;
			$action_data['complete_with_errors'] = true;
			$uncanny_automator->complete_action( $user_id, $action_data, $recipe_id, $error_msg );

			return;

		}

		$uncanny_automator->complete_action( $user_id, $action_data, $recipe_id );

	}

}
