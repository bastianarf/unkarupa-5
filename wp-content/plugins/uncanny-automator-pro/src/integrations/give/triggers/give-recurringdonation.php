<?php

namespace Uncanny_Automator_Pro;

/**
 * Class GIVE_RECURRINGDONATION
 * @package Uncanny_Automator_Pro
 */
class GIVE_RECURRINGDONATION {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'GIVEWP';

	/**
	 * @var string
	 */
	private $trigger_code;
	/**
	 * @var string
	 */
	private $trigger_meta;

	/**
	 * SetAutomatorTriggers constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'GIVERECURRING';
		$this->trigger_meta = 'RECURRINGDONATION';
		$this->define_trigger();
	}

	/**
	 * Define trigger settings
	 */
	public function define_trigger() {
		global $uncanny_automator;

		$trigger = array(
			'author'              => $uncanny_automator->get_author_name( $this->trigger_code ),
			'support_link'        => $uncanny_automator->get_author_support_link( $this->trigger_code, 'integration/givewp/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			/* translators: Logged-in trigger - GiveWP */
			'sentence'            => sprintf( __( 'A user continues a recurring donation', 'uncanny-automator-pro' ), $this->trigger_meta ),
			/* translators: Logged-in trigger - GiveWP */
			'select_option_name'  => __( 'A user continues a recurring donation', 'uncanny-automator-pro' ),
			'action'              => 'give_subscription_updated',
			'priority'            => 10,
			'accepted_args'       => 4,
			'validation_function' => array( $this, 'give_recurring_donation_updated' ),
			'options'             => [],
		);

		$uncanny_automator->register->trigger( $trigger );
	}

	/**
	 * @param $status
	 * @param $row_id
	 * @param $data
	 * @param $where
	 */
	public function give_recurring_donation_updated( $status, $row_id, $data, $where ) {
		global $uncanny_automator;

		$subscription = new \Give_Subscription( $row_id );

		$recurring_amount = $subscription->recurring_amount;

		$total_payment = $subscription->get_total_payments();
		$donor         = $subscription->donor;
		$user_id       = $donor->user_id;

		if ( 0 === absint( $user_id ) ) {
			// Its a logged in recipe and
			// user ID is 0. Skip process
			return;
		}

		if ( $total_payment > 1 && 'active' === (string) $data['status'] ) {
			$pass_args = [
				'code'           => $this->trigger_code,
				'meta'           => $this->trigger_meta,
				'user_id'        => $user_id,
				'ignore_post_id' => true,
				'is_signed_in'   => true,
			];

			$args = $uncanny_automator->maybe_add_trigger_entry( $pass_args, false );

			if ( $args ) {
				foreach ( $args as $result ) {
					if ( true === $result['result'] ) {

						$trigger_meta = [
							'user_id'        => $user_id,
							'trigger_id'     => $result['args']['trigger_id'],
							'trigger_log_id' => $result['args']['get_trigger_id'],
							'run_number'     => $result['args']['run_number'],
						];

						$trigger_meta['meta_key']   = $result['args']['trigger_id'] . ':' . $this->trigger_code . ':RECURRINGDONATIONAMOUNT';
						$trigger_meta['meta_value'] = maybe_serialize( $recurring_amount );
						$uncanny_automator->insert_trigger_meta( $trigger_meta );

						$uncanny_automator->maybe_trigger_complete( $result['args'] );
					}
				}
			}

			return;
		}
	}

}