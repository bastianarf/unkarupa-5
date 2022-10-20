<?php

namespace Uncanny_Automator_Pro;

/**
 * Class PMP_REMOVEUSERTOMEMBERSHIP
 * @package Uncanny_Automator_Pro
 */
class PMP_REMOVEUSERTOMEMBERSHIP {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'PMP';

	private $action_code;
	private $action_meta;

	/**
	 * Set up Automator action constructor.
	 */
	public function __construct() {
		$this->action_code = 'PMPREMOVEMEMBERSHIPLEVEL';
		$this->action_meta = 'REMOVEUSERFROMMEMBERSHIPLEVEL';
		$this->define_action();
	}

	/**
	 * Define and register the action by pushing it into the Automator object
	 */
	public function define_action() {

		global $uncanny_automator;

		$options                  = $uncanny_automator->helpers->recipe->paid_memberships_pro->options->all_memberships( esc_attr__( 'Membership level', 'uncanny-automator-pro' ), $this->action_meta );
		$options['options']['-1'] = esc_attr__( 'All membership', 'uncanny-automator' );

		$action = [
			'author'             => $uncanny_automator->get_author_name(),
			'support_link'       => $uncanny_automator->get_author_support_link( $this->action_code, 'integration/paid-memberships-pro/' ),
			'is_pro'             => true,
			'integration'        => self::$integration,
			'code'               => $this->action_code,
			/* translators: Action - Paid Memberships Pro */
			'sentence'           => sprintf( esc_attr__( 'Remove the user from {{a membership level:%1$s}}', 'uncanny-automator-pro' ), $this->action_meta ),
			/* translators: Action - Paid Memberships Pro */
			'select_option_name' => esc_attr__( 'Remove the user from {{a membership level}}', 'uncanny-automator-pro' ),
			'priority'           => 10,
			'accepted_args'      => 1,
			'execution_function' => [ $this, 'remove_user_from_membership_level' ],
			'options'            => [
				$options
			],
		];

		$uncanny_automator->register->action( $action );
	}

	/**
	 * Validation function when the trigger action is hit
	 *
	 * @param $user_id
	 * @param $action_data
	 * @param $recipe_id
	 */
	public function remove_user_from_membership_level( $user_id, $action_data, $recipe_id, $args ) {

		global $uncanny_automator;

		$membership_level = $action_data['meta'][ $this->action_meta ];
		$current_level    = pmpro_getMembershipLevelForUser( $user_id );

		if ( $membership_level != '-1' && ( ( empty( $current_level ) ) || ( ! empty( $current_level ) && absint( $current_level->ID ) != absint( $membership_level ) ) ) ) {
			$error_msg                           = sprintf( __( 'User was not a member of the specified level.', 'uncanny-automator-pro' ) );
			$action_data['do-nothing']           = true;
			$action_data['complete_with_errors'] = true;
			$uncanny_automator->complete_action( $user_id, $action_data, $recipe_id, $error_msg );

			return;
		}

		if ( $membership_level == '-1' ) {
			$cancel_level = pmpro_cancelMembershipLevel( absint( $current_level->ID ), absint( $user_id ) );
		} else {
			$cancel_level = pmpro_cancelMembershipLevel( absint( $membership_level ), absint( $user_id ) );
		}

		if ( $cancel_level === true ) {
			$uncanny_automator->complete_action( $user_id, $action_data, $recipe_id );

			return;
		} else {
			$error_msg                           = sprintf( __( "We're unable to cancel the specified level from the user.", 'uncanny-automator-pro' ) );
			$action_data['do-nothing']           = true;
			$action_data['complete_with_errors'] = true;
			$uncanny_automator->complete_action( $user_id, $action_data, $recipe_id, $error_msg );

			return;
		}
	}

}
