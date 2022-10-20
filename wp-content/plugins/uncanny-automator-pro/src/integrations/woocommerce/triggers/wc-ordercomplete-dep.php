<?php

namespace Uncanny_Automator_Pro;

/**
 * Class WC_ORDERCOMPLETE
 * @package Uncanny_Automator_Pro
 */
class WC_ORDERCOMPLETE_DEP {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'WC';

	private $trigger_code;
	private $trigger_meta;


	/**
	 * SetAutomatorTriggers constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'WCORDERCOMPLETE';
		$this->trigger_meta = 'WOORDERTOTAL';
		$this->define_trigger();
	}

	/**
	 *
	 */
	public function define_trigger() {

		global $uncanny_automator;

		// Get the currency symbol
		$currency_symbol = get_woocommerce_currency_symbol();
		$currency_symbol = empty( $currency_symbol ) ? '$' : $currency_symbol;

		$trigger = array(
			'author'              => $uncanny_automator->get_author_name( $this->trigger_code ),
			'support_link'        => $uncanny_automator->get_author_support_link( $this->trigger_code, 'integration/woocommerce/' ),
			'is_pro'              => true,
			'is_deprecated'       => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			/* translators: Logged-in trigger - WooCommerce. 2. Currency symbol */
			'sentence'            => sprintf( __( 'A user completes an order with a total {{greater than, less than or equal to:%1$s}} %2$s{{a specific amount:%3$s}} {{a number of:%4$s}} times (deprecated)', 'uncanny-automator-pro' ), 'NUMBERCOND', $currency_symbol, $this->trigger_meta, 'NUMTIMES' ),
			/* translators: Logged-in trigger - WooCommerce */
			'select_option_name'  => __( 'A user completes {{an order}} (deprecated)', 'uncanny-automator-pro' ),
			'action'              => [
				'woocommerce_order_status_completed',
				'woocommerce_thankyou',
				'woocommerce_payment_complete',
			],
			'priority'            => 99,
			'accepted_args'       => 1,
			'validation_function' => array( $this, 'order_completed' ),
			'options'             => [
				$uncanny_automator->helpers->recipe->field->float_field( $this->trigger_meta, __( 'Order price', 'uncanny-automator' ) ),
				$uncanny_automator->helpers->recipe->less_or_greater_than(),
				$uncanny_automator->helpers->recipe->options->number_of_times(),
			]
		);

		$uncanny_automator->register->trigger( $trigger );
	}

	/**
	 * @param $order_id
	 * @param $from_status
	 * @param $to_status
	 * @param $this_order
	 */
	public function order_completed( $order_id ) {

		global $uncanny_automator;

		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		if ( 'completed' !== $order->get_status() ) {
			return;
		}

		$user_id = $order->get_user_id();
		if ( 0 === $user_id ) {
			// Its a logged in recipe and
			// user ID is 0. Skip process
			return;
		}

		global $uncanny_automator;

		//if ( 'completed' === (string) $to_status ) {

		$order               = wc_get_order( $order_id );
		$user_id             = $order->get_user_id();
		$order_total         = $order->get_total();
		$recipes             = $uncanny_automator->get->recipes_from_trigger_code( $this->trigger_code );
		$required_totals     = $uncanny_automator->get->meta_from_recipes( $recipes, $this->trigger_meta );
		$required_conditions = $uncanny_automator->get->meta_from_recipes( $recipes, 'NUMBERCOND' );
		$matched_recipe_ids  = [];
		foreach ( $recipes as $recipe_id => $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];
				if ( $uncanny_automator->utilities->match_condition_vs_number( $required_conditions[ $recipe_id ][ $trigger_id ], $required_totals[ $recipe_id ][ $trigger_id ], $order_total ) ) {
					$matched_recipe_ids[] = [
						'recipe_id'  => $recipe_id,
						'trigger_id' => $trigger_id,
					];
				}
			}
		}

		if ( ! empty( $matched_recipe_ids ) ) {
			foreach ( $matched_recipe_ids as $matched_recipe_id ) {
				$pass_args = [
					'code'             => $this->trigger_code,
					'meta'             => $this->trigger_meta,
					'user_id'          => $user_id,
					'recipe_to_match'  => $matched_recipe_id['recipe_id'],
					'trigger_to_match' => $matched_recipe_id['trigger_id'],
					'ignore_post_id'   => true,
				];

				$args = $uncanny_automator->maybe_add_trigger_entry( $pass_args, false );

				//Adding an action to save order id in trigger meta
				do_action( 'uap_wc_trigger_save_meta', $order_id, $matched_recipe_id['recipe_id'], $args, 'order' );

				if ( $args ) {
					foreach ( $args as $result ) {
						if ( true === $result['result'] ) {
							$uncanny_automator->maybe_trigger_complete( $result['args'] );
						}
					}
				}
			}
		}
		//}
	}

}