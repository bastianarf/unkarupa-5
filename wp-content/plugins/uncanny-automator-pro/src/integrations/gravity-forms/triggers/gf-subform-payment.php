<?php

namespace Uncanny_Automator_Pro;

/**
 * Class GF_SUBFORM_PAYMENT
 * @package Uncanny_Automator_Pro
 */
class GF_SUBFORM_PAYMENT {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'GF';

	private $trigger_code;
	private $trigger_meta;

	/**
	 * Set up Automator trigger constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'GFSUBFORMPAYMENT';
		$this->trigger_meta = 'GFFORMS';
		$this->define_trigger();
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {

		global $uncanny_automator;

		$trigger = array(
			'author'              => $uncanny_automator->get_author_name( $this->trigger_code ),
			'support_link'        => $uncanny_automator->get_author_support_link( $this->trigger_code, 'integration/gravity-forms/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			/* translators: Logged-in trigger - Gravity Forms */
			'sentence'            => sprintf( __( 'A user submits {{a form:%1$s}} with payment', 'uncanny-automator-pro' ), $this->trigger_meta ),
			/* translators: Logged-in trigger - Gravity Forms */
			'select_option_name'  => __( 'A user submits {{a form}} with payment', 'uncanny-automator-pro' ),
			'action'              => 'gform_post_payment_completed',
			'priority'            => 20,
			'accepted_args'       => 2,
			'validation_function' => array( $this, 'gform_submit' ),
			'options'             => [
				$uncanny_automator->helpers->recipe->gravity_forms->options->list_gravity_forms(),
			],
		);

		$uncanny_automator->register->trigger( $trigger );

		return;
	}

	/**
	 * Validation function when the trigger action is hit
	 *
	 * @param $entry
	 * @param $form
	 */
	public function gform_submit( $entry, $action ) {

		global $uncanny_automator;

		if ( empty( $entry ) ) {
			return;
		}

		$user_id = get_current_user_id();
		if ( ! empty( $action ) && isset( $action['type'] ) && $action['type'] === 'complete_payment' ) {
			$form_id = isset( $entry->form_id ) ? $entry->form_id : $entry['form_id'];
			if ( empty( $user_id ) ) {
				$user_id = $entry['created_by'];
			}
			$args = [
				'code'         => $this->trigger_code,
				'meta'         => $this->trigger_meta,
				'post_id'      => $form_id,
				'user_id'      => $user_id,
				'is_signed_in' => true,
			];

			$uncanny_automator->maybe_add_trigger_entry( $args );
		}
	}
}
