<?php

namespace Uncanny_Automator_Pro;

/**
 * Class GTW_UNREGISTERUSER
 * @package Uncanny_Automator_Pro
 */
class GTW_UNREGISTERUSER {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'GTW';

	private $action_code;
	private $action_meta;

	/**
	 * Set up Automator action constructor.
	 */
	public function __construct() {
		$this->action_code = 'GTWUNREGISTERUSER';
		$this->action_meta = 'GTWWEBINAR';
		$this->define_action();
	}

	/**
	 * Define and register the action by pushing it into the Automator object
	 */
	public function define_action() {

		global $uncanny_automator;

		$action = array(
			'author'             => $uncanny_automator->get_author_name( $this->action_code ),
			'support_link'       => $uncanny_automator->get_author_support_link( $this->action_code, 'knowledge-base/gotowebinar/' ),
			'is_pro'             => true,
			'integration'        => self::$integration,
			'code'               => $this->action_code,
			'sentence'           => sprintf( __( 'Remove the user from {{a webinar:%1$s}}', 'uncanny-automator-pro' ), $this->action_meta ),
			'select_option_name' => __( 'Remove the user from {{a webinar}}', 'uncanny-automator-pro' ),
			'priority'           => 10,
			'accepted_args'      => 1,
			'execution_function' => array( $this, 'gtw_unregister_user' ),
			'options'            => [
				$uncanny_automator->helpers->recipe->gotowebinar->pro->get_webinars( null, $this->action_meta, [ 'token' => true ] ),
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
	public function gtw_unregister_user( $user_id, $action_data, $recipe_id, $args ) {

		global $uncanny_automator;
		$webinar_key = $uncanny_automator->parse->text( $action_data['meta'][ $this->action_meta ], $recipe_id, $user_id, $args );
		if ( empty( $user_id ) ) {
			$error_msg                           = __( 'User not found.', 'uncanny-automator-pro' );
			$action_data['do-nothing']           = true;
			$action_data['complete_with_errors'] = true;
			$uncanny_automator->complete_action( $user_id, $action_data, $recipe_id, $error_msg );

			return;
		}

		if ( empty( $webinar_key ) ) {
			$error_msg                           = __( 'Webinar not found.', 'uncanny-automator-pro' );
			$action_data['do-nothing']           = true;
			$action_data['complete_with_errors'] = true;
			$uncanny_automator->complete_action( $user_id, $action_data, $recipe_id, $error_msg );

			return;
		}

		if ( ! empty( $webinar_key ) ) {
			$webinar_key = str_replace( '-objectkey', '', $webinar_key );
		}

		$user_registrant_key = get_user_meta( $user_id, '_uncannyowl_gtw_webinar_' . $webinar_key . '_registrantKey', true );

		if ( empty( $user_registrant_key ) ) {
			$error_msg                           = __( 'User was not registered for webinar.', 'uncanny-automator-pro' );
			$action_data['do-nothing']           = true;
			$action_data['complete_with_errors'] = true;
			$uncanny_automator->complete_action( $user_id, $action_data, $recipe_id, $error_msg );

			return;

		}
		$result = $uncanny_automator->helpers->recipe->gotowebinar->pro->gtw_unregister_user( $user_id, $webinar_key );
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