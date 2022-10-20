<?php

namespace Uncanny_Automator_Pro;

/**
 * Class LD_EMAILGROUPSLEADERS_A
 * @package Uncanny_Automator_Pro
 */
class LD_EMAILGROUPSLEADERS_A {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'LD';

	private $action_code;
	private $action_meta;
	private $key_generated;
	private $key;

	/**
	 * Set up Automator action constructor.
	 */
	public function __construct() {
		$this->action_code = 'EMAILGROUPSLEADERS';
		$this->action_meta = 'EMAILTOLEADERS';
		$this->key_generated = false;
		$this->key           = null;
		$this->define_action();
	}

	/**
	 * Define and register the action by pushing it into the Automator object
	 */
	public function define_action() {

		global $uncanny_automator;

		$action = array(
			'author'             => $uncanny_automator->get_author_name( $this->action_code ),
			'support_link'       => $uncanny_automator->get_author_support_link( $this->action_code, 'integration/learndash/' ),
			'is_pro'             => true,
			'integration'        => self::$integration,
			'code'               => $this->action_code,
			/* translators: Action - LearnDash */
			'sentence'           => sprintf( __( "Send an {{email:%1\$s}} to the user's group leader(s)", 'uncanny-automator-pro' ), $this->action_meta ),
			/* translators: Action - LearnDash */
			'select_option_name' => __( "Send an {{email}} to the user's group leader(s)", 'uncanny-automator-pro' ),
			'priority'           => 10,
			'accepted_args'      => 1,
			'execution_function' => array( $this, 'send_email' ),
			// very last call in WP, we need to make sure they viewed the page and didn't skip before is was fully viewable
			'options_group'      => [
				$this->action_meta => [
					$uncanny_automator->helpers->recipe->field->text_field( 'EMAILFROM', __( 'From', 'uncanny-automator' ), true, 'email', '{{admin_email}}', true, __( 'This email will be sent to all leaders of all LearnDash groups the user is a member of.', 'uncanny-automator' ) ),
					//$uncanny_automator->helpers->field->text_field( 'EMAILTO', __( 'To:', 'uncanny-automator' ), false, '', '', false, __( 'Separate multiple email addresses with a comma', 'uncanny-automator' ) ),
					$uncanny_automator->helpers->recipe->field->text_field( 'EMAILCC', __( 'CC', 'uncanny-automator' ), true, 'email', '', false ),
					$uncanny_automator->helpers->recipe->field->text_field( 'EMAILBCC', __( 'BCC', 'uncanny-automator' ), true, 'email', '', false ),
					$uncanny_automator->helpers->recipe->field->text_field( 'EMAILSUBJECT', __( 'Subject', 'uncanny-automator' ), true ),
					$uncanny_automator->helpers->recipe->field->text_field( 'EMAILBODY', __( 'Body', 'uncanny-automator' ), true, 'textarea' ),
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
	public function send_email( $user_id, $action_data, $recipe_id, $args ) {

		global $uncanny_automator;

		$from    = $uncanny_automator->parse->text( $action_data['meta']['EMAILFROM'], $recipe_id, $user_id, $args );
		$cc      = $uncanny_automator->parse->text( $action_data['meta']['EMAILCC'], $recipe_id, $user_id, $args );
		$bcc     = $uncanny_automator->parse->text( $action_data['meta']['EMAILBCC'], $recipe_id, $user_id, $args );
		$subject = $uncanny_automator->parse->text( $action_data['meta']['EMAILSUBJECT'], $recipe_id, $user_id, $args );
		$body_text = $action_data['meta']['EMAILBODY'];

		if ( false !== strpos( $body_text, '{{reset_pass_link}}' ) ) {
			$reset_pass = ! is_null( $this->key ) ? $this->key : $uncanny_automator->parse->generate_reset_token( $user_id );
			$body       = str_replace( '{{reset_pass_link}}', $reset_pass, $body_text );
		} else {
			$body = $body_text;
		}

		$body = $uncanny_automator->parse->text( $body, $recipe_id, $user_id, $args );
		$body = do_shortcode( $body );
		$body = wpautop( $body );

		$to_leaders_emails = [];
		$user_groups_ids   = learndash_get_users_group_ids( $user_id, true );
		if ( ! empty( $user_groups_ids ) ) {
			foreach ( $user_groups_ids as $group_id ) {
				$group_leaders = learndash_get_groups_administrators( $group_id, true );
				if ( ! empty( $group_leaders ) ) {
					foreach ( $group_leaders as $leader ) {
						if ( ! in_array( $leader->data->user_email, $to_leaders_emails ) ) {
							$to_leaders_emails[] = $leader->data->user_email;
						}
					}
				}
			}
		}

		$headers[] = 'From: <' . $from . '>';

		if ( ! empty( $cc ) ) {
			$headers[] = 'Cc: ' . $cc;
		}

		if ( ! empty( $bcc ) ) {
			$headers[] = 'Bcc: ' . $bcc;
		}

		$headers[] = 'Content-Type: text/html; charset=UTF-8';

		$mailed = wp_mail( $to_leaders_emails, $subject, $body, $headers );

		if ( ! $mailed ) {
			$error_message = $uncanny_automator->error_message->get( 'email-failed' );
			$uncanny_automator->complete_action( $user_id, $action_data, $recipe_id, $error_message );

			return;
		}

		$uncanny_automator->complete_action( $user_id, $action_data, $recipe_id );
	}
}
