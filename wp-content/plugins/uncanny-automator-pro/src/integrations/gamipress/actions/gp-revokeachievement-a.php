<?php

namespace Uncanny_Automator_Pro;

/**
 * Class GP_REVOKEACHIEVEMENT_A
 * @package Uncanny_Automator_Pro
 */
class GP_REVOKEACHIEVEMENT_A {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'GP';

	private $action_code;
	private $action_meta;

	/**
	 * Set up Automator action constructor.
	 */
	public function __construct() {
		$this->action_code = 'REVOKEACHIEVEMENT';
		$this->action_meta = 'GPACHIEVEMENT';
		$this->define_action();
	}

	/**
	 * Define and register the action by pushing it into the Automator object
	 */
	public function define_action() {

		global $uncanny_automator;

		$action = array(
			'author'             => $uncanny_automator->get_author_name(),
			'support_link'       => $uncanny_automator->get_author_support_link( $this->action_code, 'integration/gamipress/' ),
			'is_pro'             => true,
			'integration'        => self::$integration,
			'code'               => $this->action_code,
			/* translators: Actions - GamiPress */
			'sentence'           => sprintf( __( 'Revoke {{an achievement:%1$s}} from the user', 'uncanny-automator-pro' ), $this->action_meta ),
			/* translators: Actions - GamiPress */
			'select_option_name' => __( 'Revoke {{an achievement}} from the user', 'uncanny-automator-pro' ),
			'priority'           => 10,
			'accepted_args'      => 1,
			'execution_function' => array( $this, 'revoke_an_achievement' ),
			'options_group'      => [
				$this->action_meta => [
					$uncanny_automator->helpers->recipe->gamipress->options->list_gp_award_types(
						__( 'Achievement type', 'uncanny-automator' ),
						'GPAWARDTYPES',
						[
							'token'        => false,
							'is_ajax'      => true,
							'target_field' => $this->action_meta,
							'endpoint'     => 'select_achievements_from_types_REVOKEACHIEVEMENT',
						]
					),
					$uncanny_automator->helpers->recipe->field->select_field( $this->action_meta, __( 'Award', 'uncanny-automator' ) ),
				],
			],
		);

		$uncanny_automator->register->action( $action );
	}

	/**
	 * Validation function when the trigger action is hit
	 *
	 * @param $user_id
	 * @param $action_data
	 * @param $recipe_id
	 */
	public function revoke_an_achievement( $user_id, $action_data, $recipe_id, $args ) {

		global $uncanny_automator;

		$achievement_id = $action_data['meta'][ $this->action_meta ];
		// If the user has not already earned the achievement...
		if ( gamipress_get_user_achievements( [
			'user_id'        => absint( $user_id ),
			'achievement_id' => absint( $achievement_id )
		] ) ) {

			gamipress_revoke_achievement_to_user( absint( $achievement_id ), absint( $user_id ) );

			$uncanny_automator->complete_action( $user_id, $action_data, $recipe_id );
		} else {
			$uncanny_automator->complete_action( $user_id, $action_data, $recipe_id, __( "The user didn't have the specified achievement.", 'uncanny-automator-pro' ) );
		}
	}

}
