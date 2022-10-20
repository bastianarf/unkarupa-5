<?php

namespace Uncanny_Automator_Pro;

/**
 * Class WC_ORDERCOMPLETE
 * @package Uncanny_Automator_Pro
 */
class WC_ORDERCOMPLETE {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'WC';

	/**
	 * @var string
	 */
	private $trigger_code;
	/**
	 * @var string
	 */
	private $trigger_meta;
	/**
	 * @var string
	 */
	private $trigger_condition;

	/**
	 * SetAutomatorTriggers constructor.
	 */
	public function __construct() {
		$this->trigger_code      = 'WCORDERCONDITION';
		$this->trigger_meta      = 'WOORDERTOTAL';
		$this->trigger_condition = 'TRIGGERCOND';
		$this->define_trigger();
	}

	/**
	 *
	 */
	public function define_trigger() {

		global $uncanny_automator;

		// Get the currency symbol
		$currency_symbol   = get_woocommerce_currency_symbol();
		$currency_symbol   = empty( $currency_symbol ) ? '$' : $currency_symbol;
		$trigger_condition = $uncanny_automator->helpers->recipe->woocommerce->pro->get_woocommerce_trigger_conditions( $this->trigger_condition );

		$trigger = array(
			'author'              => $uncanny_automator->get_author_name( $this->trigger_code ),
			'support_link'        => $uncanny_automator->get_author_support_link( $this->trigger_code, 'integration/woocommerce/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			/* translators: Logged-in trigger - WooCommerce. 2. Currency symbol */
			'sentence'            => sprintf( __( 'A user {{completes, pays for, lands on a thank you page for:%5$s}} an order with a total {{greater than, less than or equal to:%1$s}} %2$s{{a specific amount:%3$s}} {{a number of:%4$s}} times', 'uncanny-automator-pro' ), 'NUMBERCOND', $currency_symbol, $this->trigger_meta, 'NUMTIMES', $this->trigger_condition ),
			/* translators: Logged-in trigger - WooCommerce */
			'select_option_name'  => __( 'A user {{completes, pays for, lands on a thank you page for}} an order with a total {{greater than, less than or equal to}} {{a specific amount}}', 'uncanny-automator-pro' ),
			'action'              => [
				'woocommerce_order_status_completed',
				'woocommerce_thankyou',
				'woocommerce_payment_complete',
			],
			'priority'            => 99,
			'accepted_args'       => 1,
			'validation_function' => array( $this, 'order_completed' ),
			'options'             => [
				$uncanny_automator->helpers->recipe->field->float(
					array(
						'option_code' => $this->trigger_meta,
						'label'       => __( 'Order price', 'uncanny-automator' ),
						'placeholder' => 0.00,
						'default'     => 0.00,
					)
				),
				$uncanny_automator->helpers->recipe->less_or_greater_than(),
				$uncanny_automator->helpers->recipe->options->number_of_times(),
				$trigger_condition,
			],
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

		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		$user_id = $order->get_user_id();
		if ( 0 === $user_id ) {
			// Its a logged in recipe and
			// user ID is 0. Skip process
			return;
		}

		global $uncanny_automator;

		$order               = wc_get_order( $order_id );
		$user_id             = $order->get_user_id();
		$order_total         = $order->get_total();
		$recipes             = $uncanny_automator->get->recipes_from_trigger_code( $this->trigger_code );
		$required_totals     = $uncanny_automator->get->meta_from_recipes( $recipes, $this->trigger_meta );
		$required_conditions = $uncanny_automator->get->meta_from_recipes( $recipes, 'NUMBERCOND' );
		$trigger_condition   = $uncanny_automator->get->meta_from_recipes( $recipes, $this->trigger_condition );
		$matched_recipe_ids  = array();
		$trigger_cond_ids    = array();

		if ( empty( $recipes ) ) {
			return;
		}

		if ( empty( $required_totals ) ) {
			return;
		}

		if ( empty( $required_conditions ) ) {
			return;
		}

		if ( empty( $trigger_condition ) ) {
			return;
		}

		//Add where Product ID is set for trigger
		foreach ( $recipes as $recipe_id => $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];//return early for all products
				if ( (string) current_action() === (string) $trigger_condition[ $recipe_id ][ $trigger_id ] ) {
					$trigger_cond_ids[] = $recipe_id;
				}
			}
		}

		if ( empty( $trigger_cond_ids ) ) {
			return;
		}

		if ( 'woocommerce_order_status_completed' === (string) current_action() ) {
			if ( 'completed' !== $order->get_status() ) {
				return;
			}
		}

		foreach ( $recipes as $recipe_id => $recipe ) {
			if ( ! in_array( $recipe_id, $trigger_cond_ids, false ) ) {
				continue;
			}
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
