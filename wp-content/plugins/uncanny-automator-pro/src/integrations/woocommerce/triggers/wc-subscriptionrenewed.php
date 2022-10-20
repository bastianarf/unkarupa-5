<?php

namespace Uncanny_Automator_Pro;

/**
 * Class WC_SUBSCRIPTION_RENEWED
 * @package Uncanny_Automator_Pro
 */
class WC_SUBSCRIPTIONRENEWED {

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
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( is_plugin_active( 'woocommerce-subscriptions' . DIRECTORY_SEPARATOR . 'woocommerce-subscriptions.php' ) ) {
			$this->trigger_code = 'WCSUBSCRIPTIONRENEWED';
			$this->trigger_meta = 'WOOSUBSCRIPTIONS';
			$this->define_trigger();
		}
	}

	/**
	 *
	 */
	public function define_trigger() {

		global $uncanny_automator;

		$options = $uncanny_automator->helpers->recipe->woocommerce->options->pro->all_wc_subscriptions();

		$trigger = array(
			'author'              => $uncanny_automator->get_author_name( $this->trigger_code ),
			'support_link'        => $uncanny_automator->get_author_support_link( $this->trigger_code, 'integration/woocommerce/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			/* translators: Logged-in trigger - WooCommerce */
			'sentence'            => sprintf( __( 'A user renews a subscription to {{a product:%1$s}} {{a number of:%2$s}} times', 'uncanny-automator-pro' ), $this->trigger_meta, 'NUMTIMES' ),
			/* translators: Logged-in trigger - WooCommerce */
			'select_option_name'  => __( 'A user renews a subscription to {{a product}}', 'uncanny-automator-pro' ),
			'action'              => 'woocommerce_subscription_renewal_payment_complete',
			'priority'            => 30,
			'accepted_args'       => 2,
			'validation_function' => array( $this, 'order_renewed' ),
			'options'             => [
				$uncanny_automator->helpers->recipe->options->number_of_times(),
				$options,
			]
		);

		$uncanny_automator->register->trigger( $trigger );

		return;
	}

	/**
	 * Do action after a WooCommerce subscription renewal payment has been completed
	 *
	 * @param $_this      WC_Subscription class instance extended from WC_Order
	 * @param $last_order The last order of $_this WC_Subscription
	 */
	public function order_renewed( $_this, $last_order ) {

		if ( ! $_this ) {
			return;
		}

		global $uncanny_automator;

		$user_id            = $_this->get_user_id();
		$recipes            = $uncanny_automator->get->recipes_from_trigger_code( $this->trigger_code );
		$required_product   = $uncanny_automator->get->meta_from_recipes( $recipes, $this->trigger_meta );
		$matched_recipe_ids = [];

		//Add where option is set to Any product
		foreach ( $recipes as $recipe_id => $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];//return early for all products
				if ( - 1 === intval( $required_product[ $recipe_id ][ $trigger_id ] ) ) {
					$matched_recipe_ids[] = [
						'recipe_id'  => $recipe_id,
						'trigger_id' => $trigger_id,
					];

					break;
				}
			}
		}

		$items       = $_this->get_items();
		$product_ids = array();
		foreach ( $items as $item ) {
			$product_ids[] = $item->get_product_id();
		}

		//Add where Product ID is set for trigger
		foreach ( $recipes as $recipe_id => $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];//return early for all products
				if ( in_array( $required_product[ $recipe_id ][ $trigger_id ], $product_ids, false ) ) {
					$matched_recipe_ids[] = [
						'recipe_id'  => $recipe_id,
						'trigger_id' => $trigger_id,
					];
				}
			}
		}

		if ( ! empty( $matched_recipe_ids ) ) {
			foreach ( $matched_recipe_ids as $matched_recipe_id ) {
				$args = [
					'code'             => $this->trigger_code,
					'meta'             => $this->trigger_meta,
					'user_id'          => $user_id,
					'recipe_to_match'  => $matched_recipe_id['recipe_id'],
					'trigger_to_match' => $matched_recipe_id['trigger_id'],
					'ignore_post_id'   => true,
				];
				$uncanny_automator->maybe_add_trigger_entry( $args );
			}
		}

		return;

	}

}
