<?php

namespace Uncanny_Automator_Pro;


/**
 * Class WC_SUBSCRIPTION_STATUS_CHANGE
 * @package Uncanny_Automator_Pro
 */
class WC_SUBSCRIPTION_STATUS_CHANGE {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'WC';

	private $trigger_code;
	private $trigger_meta;
	private $trigger_meta_status;


	/**
	 * SetAutomatorTriggers constructor.
	 */
	public function __construct() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( is_plugin_active( 'woocommerce-subscriptions' . DIRECTORY_SEPARATOR . 'woocommerce-subscriptions.php' ) ) {
			$this->trigger_code        = 'WCSUBSCRIPTIONSTATUSCHANGED';
			$this->trigger_meta        = 'WOOSUBSCRIPTIONS';
			$this->trigger_meta_status = 'WOOSUBSCRIPTIONSTATUS';
			$this->define_trigger();
		}
	}

	/**
	 *
	 */
	public function define_trigger() {

		global $uncanny_automator;

		$options = $uncanny_automator->helpers->recipe->woocommerce->options->pro->all_wc_subscriptions();

		$statuses = $uncanny_automator->helpers->recipe->woocommerce->options->pro->get_wcs_statuses();

		$trigger = array(
			'author'              => $uncanny_automator->get_author_name( $this->trigger_code ),
			'support_link'        => $uncanny_automator->get_author_support_link( $this->trigger_code, 'integration/woocommerce/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			/* translators: Logged-in trigger - WooCommerce */
			'sentence'            => sprintf( __( "A user's subscription to {{a product:%1\$s}} is set to {{a status:%2\$s}}", 'uncanny-automator-pro' ), $this->trigger_meta, $this->trigger_meta_status ),
			/* translators: Logged-in trigger - WooCommerce */
			'select_option_name'  => __( "A user's subscription to {{a product}} is set to {{a status}}", 'uncanny-automator-pro' ),
			'action'              => 'woocommerce_subscription_status_updated',
			'priority'            => 30,
			'accepted_args'       => 3,
			'validation_function' => array( $this, 'status_changed' ),
			'options'             => [
				$options,
				$statuses,
			]
		);

		$uncanny_automator->register->trigger( $trigger );
	}

	/**
	 * @param $subscription
	 * @param $new_status
	 * @param $old_status
	 *
	 * @since 2.12
	 */
	public function status_changed( $subscription, $new_status, $old_status ) {
		if ( ! $subscription instanceof \WC_Subscription ) {
			return;
		}
		global $uncanny_automator;

		$user_id            = $subscription->get_user_id();
		$recipes            = $uncanny_automator->get->recipes_from_trigger_code( $this->trigger_code );
		$required_product   = $uncanny_automator->get->meta_from_recipes( $recipes, $this->trigger_meta );
		$required_status    = $uncanny_automator->get->meta_from_recipes( $recipes, $this->trigger_meta_status );
		$matched_recipe_ids = [];

		if ( empty( $recipes ) ) {
			return;
		}

		$items       = $subscription->get_items();
		$product_ids = array();
		foreach ( $items as $item ) {
			$product = $item->get_product();
			if ( class_exists( '\WC_Subscriptions_Product' ) && \WC_Subscriptions_Product::is_subscription( $product ) ) {
				$product_ids[] = $product->get_id();
			}
		}

		//Add where option is set to Any product
		foreach ( $recipes as $recipe_id => $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];//return early for all products
				if (
					( intval( '-1' ) === intval( $required_product[ $recipe_id ][ $trigger_id ] ) || in_array( $required_product[ $recipe_id ][ $trigger_id ], $product_ids, false ) )
					&&
					( intval( '-1' ) === intval( $required_status[ $recipe_id ][ $trigger_id ] ) || (string) $required_status[ $recipe_id ][ $trigger_id ] === (string) "wc-{$new_status}" )
				) {
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

				if ( $args ) {
					foreach ( $args as $result ) {
						if ( true === $result['result'] ) {
							// Add token for options
							$uncanny_automator->insert_trigger_meta(
								[
									'user_id'        => $user_id,
									'trigger_id'     => $result['args']['trigger_id'],
									'meta_key'       => 'subscription_status',
									'meta_value'     => $new_status,
									'trigger_log_id' => $result['args']['get_trigger_id'],
									'run_number'     => $result['args']['run_number'],
								]
							);
							// Add token for options
							$uncanny_automator->insert_trigger_meta(
								[
									'user_id'        => $user_id,
									'trigger_id'     => $result['args']['trigger_id'],
									'meta_key'       => 'subscription_id',
									'meta_value'     => $subscription->get_id(),
									'trigger_log_id' => $result['args']['get_trigger_id'],
									'run_number'     => $result['args']['run_number'],
								]
							);

							$uncanny_automator->maybe_trigger_complete( $result['args'] );
						}
					}
				}
			}
		}
	}
}
