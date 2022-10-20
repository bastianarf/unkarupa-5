<?php

namespace Uncanny_Automator_Pro;

/**
 * Class WC_ORDERSTATUSCHANGE
 * @package Uncanny_Automator_Pro
 */
class WC_ORDERSTATUSCHANGE {

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
		$this->trigger_code = 'WCORDERSTATUSCHANGE';
		$this->trigger_meta = 'WCORDERSTATUS';
		$this->define_trigger();
	}

	/**
	 *
	 */
	public function define_trigger() {

		global $uncanny_automator;

		$trigger = [
			'author'              => $uncanny_automator->get_author_name( $this->trigger_code ),
			'support_link'        => $uncanny_automator->get_author_support_link( $this->trigger_code, 'integration/woocommerce/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			/* translators: Logged-in trigger - WooCommerce */
			'sentence'            => sprintf( __( "A user's order status changes to {{a specific status:%1\$s}}", 'uncanny-automator-pro' ), $this->trigger_meta ),
			/* translators: Logged-in trigger - WooCommerce */
			'select_option_name'  => __( "A user's order status changes to {{a specific status}}", 'uncanny-automator-pro' ),
			'action'              => 'woocommerce_order_status_changed',
			'priority'            => 30,
			'accepted_args'       => 4,
			'validation_function' => [ $this, 'order_status_changed' ],
			'options'             => [
				$uncanny_automator->helpers->recipe->woocommerce->options->wc_order_statuses( null, $this->trigger_meta ),
			],
		];

		$uncanny_automator->register->trigger( $trigger );

		return;
	}

	/**
	 * @param $order_id
	 * @param $from_status
	 * @param $to_status
	 * @param $this_order
	 */
	public function order_status_changed( $order_id, $from_status, $to_status, $this_order ) {

		if ( ! $order_id ) {
			return;
		}
		$order   = wc_get_order( $order_id );
		$user_id = $order->get_user_id();

		if ( ! $user_id ) {
			return;
		}

		global $uncanny_automator;

		if ( '' !== (string) $to_status ) {
			$recipes            = $uncanny_automator->get->recipes_from_trigger_code( $this->trigger_code );
			$required_statuses  = $uncanny_automator->get->meta_from_recipes( $recipes, $this->trigger_meta );
			$matched_recipe_ids = [];
			foreach ( $recipes as $recipe_id => $recipe ) {
				foreach ( $recipe['triggers'] as $trigger ) {
					$trigger_id = $trigger['ID'];
					$status     = $required_statuses[ $recipe_id ][ $trigger_id ];
					$status     = 'wc-' === substr( $status, 0, 3 ) ? substr( $status, 3 ) : $status;
					if ( $status === (string) $to_status ) {
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
		}
	}
}
