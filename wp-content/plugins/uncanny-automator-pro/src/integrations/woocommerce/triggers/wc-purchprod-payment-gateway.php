<?php

namespace Uncanny_Automator_Pro;

/**
 * Class WC_PURCHPROD_PAYMENT_GATEWAY
 * @package Uncanny_Automator_Pro
 */
class WC_PURCHPROD_PAYMENT_GATEWAY {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'WC';

	private $trigger_code;
	private $trigger_meta;

	/**
	 * Set up Automator trigger constructor.
	 */
	public function __construct() {
		$this->trigger_code      = 'WCPURCHPRODUCTVIAGATEWAY';
		$this->trigger_meta      = 'WOOPAYMENTGATEWAY';
		$this->trigger_condition = 'TRIGGERCOND';
		global $uncanny_automator;
		if ( $uncanny_automator->helpers->recipe->is_edit_page() ) {
			add_action( 'wp_loaded', array( $this, 'plugins_loaded' ), 9999 );
		} else {
			$this->define_trigger();
		}
	}

	/**
	 *
	 */
	public function plugins_loaded() {
		$this->define_trigger();
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {

		global $uncanny_automator;
		$trigger_condition = $uncanny_automator->helpers->recipe->woocommerce->pro->get_woocommerce_trigger_conditions( $this->trigger_condition );

		$trigger = array(
			'author'              => $uncanny_automator->get_author_name( $this->trigger_code ),
			'support_link'        => $uncanny_automator->get_author_support_link( $this->trigger_code, 'integration/woocommerce/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			/* translators: Logged-in trigger - WooCommerce */
			'sentence'            => sprintf( __( 'A user {{completes, pays for, lands on a thank you page for:%1$s}} an order paid for with {{a specific method:%2$s}}', 'uncanny-automator-pro' ), $this->trigger_condition, $this->trigger_meta ),
			/* translators: Logged-in trigger - WooCommerce */
			'select_option_name'  => __( 'A user {{completes, pays for, lands on a thank you page for}} an order paid for with {{a specific method}}', 'uncanny-automator-pro' ),
			'action'              => [
				'woocommerce_order_status_completed',
				'woocommerce_thankyou',
				'woocommerce_payment_complete',
			],
			'priority'            => 9,
			'accepted_args'       => 1,
			'validation_function' => array( $this, 'wc_payment_gateway' ),
			'options'             => array(
				$trigger_condition,
				$uncanny_automator->helpers->recipe->woocommerce->options->pro->all_wc_payment_gateways(
					__( 'Payment method', 'uncanny-automator-pro' ),
					$this->trigger_meta ),
			),
		);
		$uncanny_automator->register->trigger( $trigger );
	}

	/**
	 * Validation function when the trigger action is hit
	 *
	 * @param $order_id
	 */
	public function wc_payment_gateway( $order_id ) {
		global $uncanny_automator;

		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		$user_id                 = $order->get_customer_id();
		$recipes                 = $uncanny_automator->get->recipes_from_trigger_code( $this->trigger_code );
		$required_payment_method = $uncanny_automator->get->meta_from_recipes( $recipes, $this->trigger_meta );
		$required_condition      = $uncanny_automator->get->meta_from_recipes( $recipes, $this->trigger_condition );
		$matched_recipe_ids      = array();
		$trigger_cond_ids        = array();

		if ( ! $recipes ) {
			return;
		}

		if ( ! $required_payment_method ) {
			return;
		}

		if ( empty( $required_condition ) ) {
			return;
		}

		//Add where Product ID is set for trigger
		foreach ( $recipes as $recipe_id => $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];//return early for all products
				if ( (string) current_action() === (string) $required_condition[ $recipe_id ][ $trigger_id ] ) {
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

		//Add where option is set to Any payment method
		foreach ( $recipes as $recipe_id => $recipe ) {
			if ( ! in_array( $recipe_id, $trigger_cond_ids, false ) ) {
				continue;
			}
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];//return early for all products
				if ( intval( '-1' ) === intval( $required_payment_method[ $recipe_id ][ $trigger_id ] ) ) {
					$matched_recipe_ids[] = [
						'recipe_id'  => $recipe_id,
						'trigger_id' => $trigger_id,
					];
					break;
				}
			}
		}

		$payment_method = $order->get_payment_method();

		if ( empty( $payment_method ) ) {
			return;
		}

		//Add where Product ID is set for trigger
		foreach ( $recipes as $recipe_id => $recipe ) {
			if ( ! in_array( $recipe_id, $trigger_cond_ids, false ) ) {
				continue;
			}
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];//return early for all products

				if ( ! isset( $required_payment_method[ $recipe_id ] ) ) {
					continue;
				}
				if ( ! isset( $required_payment_method[ $recipe_id ][ $trigger_id ] ) ) {
					continue;
				}
				if ( $required_payment_method[ $recipe_id ][ $trigger_id ] == $payment_method ) {
					$matched_recipe_ids[] = [
						'recipe_id'  => $recipe_id,
						'trigger_id' => $trigger_id,
					];
				}
			}
		}

		if ( empty( $matched_recipe_ids ) ) {
			return;
		}
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
}
