<?php

namespace Uncanny_Automator_Pro;

use WC_Order_Item_Product;

/**
 * Class WC_PURCHPROD_CATEGORY
 * @package Uncanny_Automator_Pro
 */
class WC_PURCHPROD_CATEGORY {

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
		$this->trigger_code      = 'WCPURCHPRODUCTINCAT';
		$this->trigger_meta      = 'WOOPRODCAT';
		$this->trigger_condition = 'TRIGGERCOND';
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
			'sentence'            => sprintf( __( 'A user {{completes, pays for, lands on a thank you page for:%1$s}} an order with a product in {{a category:%2$s}}', 'uncanny-automator-pro' ), $this->trigger_condition, $this->trigger_meta ),
			/* translators: Logged-in trigger - WooCommerce */
			'select_option_name'  => __( 'A user {{completes, pays for, lands on a thank you page for}} an order with a product in {{a category}}', 'uncanny-automator-pro' ),
			'action'              => [
				'woocommerce_order_status_completed',
				'woocommerce_thankyou',
				'woocommerce_payment_complete',
			],
			'priority'            => 9,
			'accepted_args'       => 1,
			'validation_function' => array( $this, 'wc_payment_completed' ),
			'options'             => array(
				$trigger_condition,
				$uncanny_automator->helpers->recipe->woocommerce->options->pro->all_wc_product_categories(
					__( 'Product category', 'uncanny-automator-pro' ),
					$this->trigger_meta )
			),
		);
		$uncanny_automator->register->trigger( $trigger );
	}

	/**
	 * Validation function when the trigger action is hit
	 *
	 * @param $order_id
	 */
	public function wc_payment_completed( $order_id ) {
		global $uncanny_automator;

		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		$user_id                   = $order->get_customer_id();
		$recipes                   = $uncanny_automator->get->recipes_from_trigger_code( $this->trigger_code );
		$required_product_category = $uncanny_automator->get->meta_from_recipes( $recipes, $this->trigger_meta );
		$required_condition        = $uncanny_automator->get->meta_from_recipes( $recipes, $this->trigger_condition );
		$matched_recipe_ids        = array();
		$trigger_cond_ids          = array();
		$product_ids               = array();

		if ( ! $recipes ) {
			return;
		}

		if ( ! $required_product_category ) {
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

		$items = $order->get_items();
		/** @var WC_Order_Item_Product $item */
		foreach ( $items as $item ) {
			$product_ids[] = $item->get_product_id();
		}

		if ( empty( $product_ids ) ) {
			return;
		}

		//Add where option is set to Any product
		foreach ( $recipes as $recipe_id => $recipe ) {
			if ( ! in_array( $recipe_id, $trigger_cond_ids, false ) ) {
				continue;
			}
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];//return early for all products
				if ( intval( '-1' ) === intval( $required_product_category[ $recipe_id ][ $trigger_id ] ) ) {
					foreach ( $product_ids as $k => $product_id ) {
						$tags = get_the_terms( $product_id, 'product_cat' );
						if ( ! empty( $tags ) ) {
							$matched_recipe_ids[] = [
								'recipe_id'  => $recipe_id,
								'trigger_id' => $trigger_id,
							];
							break;
						}
					}
				}
			}
		}

		// Get Product Categories
		$category_ids = array();
		foreach ( $product_ids as $k => $product_id ) {
			$categories = get_the_terms( $product_id, 'product_cat' );
			if ( ! $categories ) {
				continue;
			}
			foreach ( $categories as $category ) {
				$category_ids[] = $category->term_id;
			}
		}

		if ( empty( $category_ids ) ) {
			return;
		}

		//Add where Product ID is set for trigger
		foreach ( $recipes as $recipe_id => $recipe ) {
			if ( ! in_array( $recipe_id, $trigger_cond_ids, false ) ) {
				continue;
			}
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];//return early for all products

				if ( ! isset( $required_product_category[ $recipe_id ] ) ) {
					continue;
				}
				if ( ! isset( $required_product_category[ $recipe_id ][ $trigger_id ] ) ) {
					continue;
				}
				if ( in_array( absint( $required_product_category[ $recipe_id ][ $trigger_id ] ), $category_ids, false ) ) {
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
			do_action( 'uap_wc_trigger_save_meta', $order_id, $matched_recipe_id['recipe_id'], $args, 'product' );

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