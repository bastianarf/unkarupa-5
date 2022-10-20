<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

namespace Uncanny_Automator_Pro;

use Exception;
use WC_Order;
use WC_Order_Item_Product;

/**
 * Class ANON_WC_PURCHASEPROD
 * @package Uncanny_Automator_Pro
 */
class ANON_ORDER_ITEM_CREATED {

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
	 * Set up Automator trigger constructor.
	 * @throws Exception
	 */
	public function __construct() {
		$this->trigger_code = 'ANONORDERITEMCREATED';
		$this->trigger_meta = 'WOOPRODUCT';
		$this->define_trigger();
		add_action( 'automator_woo_order_item_added', array( $this, 'automator_woo_order_item_added_handle' ), 99, 5 );
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 * @throws Exception
	 */
	public function define_trigger() {

		global $uncanny_automator;

		$options            = $uncanny_automator->helpers->recipe->woocommerce->options->all_wc_products( __( 'Product', 'uncanny-automator' ) );
		$options['options'] = array( '-1' => __( 'Any product', 'uncanny-automator' ) ) + $options['options'];

		$trigger = array(
			'author'              => $uncanny_automator->get_author_name( $this->trigger_code ),
			'support_link'        => $uncanny_automator->get_author_support_link( $this->trigger_code, 'integration/woocommerce/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			/* translators: Anonymous trigger - WooCommerce */
			'sentence'            => sprintf( __( '{{A product:%1$s}} is purchased in an order', 'uncanny-automator-pro' ), $this->trigger_meta ),
			/* translators: Anonymous trigger - WooCommerce */
			'select_option_name'  => __( '{{A product}} is purchased in an order', 'uncanny-automator-pro' ),
			'action'              => 'woocommerce_new_order_item',
			'priority'            => 999,
			'accepted_args'       => 3,
			'type'                => 'anonymous',
			'validation_function' => array( $this, 'woo_order_item_created' ),
			'options'             => array(
				//$trigger_condition,
				$options,
			),
		);

		$uncanny_automator->register->trigger( $trigger );
	}


	/**
	 * @param $item_id
	 * @param WC_Order_Item_Product $item
	 * @param $order_id
	 */
	public function woo_order_item_created( $item_id, WC_Order_Item_Product $item, $order_id ) {
		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order instanceof WC_Order ) {
			return;
		}

		global $uncanny_automator;
		$recipes = $uncanny_automator->get->recipes_from_trigger_code( $this->trigger_code );

		if ( empty( $recipes ) ) {
			return;
		}

		$required_product = $uncanny_automator->get->meta_from_recipes( $recipes, $this->trigger_meta );

		if ( empty( $required_product ) ) {
			return;
		}

		$user_id            = $order->get_customer_id();
		$matched_recipe_ids = array();
		$product_id         = $item->get_product_id();

		$skip_product_ids = apply_filters( 'automator_woocommerce_item_added_skip_product_ids', array(), $product_id, $item, $order );
		if ( ! empty( $skip_product_ids ) && in_array( absint( $product_id ), $skip_product_ids, true ) ) {
			return;
		}

		$skip_product_types = apply_filters( 'automator_woocommerce_item_added_skip_product_type', array(), $product_id, $item, $order );
		if ( ! empty( $skip_product_types ) && in_array( $item->get_product()->get_type(), $skip_product_types, true ) ) {
			return;
		}

		//Add where option is set to Any product
		foreach ( $recipes as $recipe_id => $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];//return early for all products
				if ( ! isset( $required_product[ $recipe_id ] ) ) {
					continue;
				}
				if ( ! isset( $required_product[ $recipe_id ][ $trigger_id ] ) ) {
					continue;
				}
				if ( intval( '-1' ) === intval( $required_product[ $recipe_id ][ $trigger_id ] ) || (int) $required_product[ $recipe_id ][ $trigger_id ] === $product_id ) {
					$matched_recipe_ids[] = array(
						'recipe_id'  => $recipe_id,
						'trigger_id' => $trigger_id,
					);
				}
			}
		}

		if ( empty( $matched_recipe_ids ) ) {
			return;
		}

		$this->automator_woo_order_item_added_handle( $matched_recipe_ids, $user_id, $item_id, $order_id, 0 );
	}

	/**
	 * @param $time
	 * @param $hook
	 * @param $args
	 * @param string $group
	 */
	public function schedule_a_delayed_trigger( $hook, $args, $time = null, $group = 'automator' ) {
		if ( is_null( $time ) ) {
			$time = time() + 15;
		}
		$time = apply_filters( 'automator_woocommerce_item_added_delay_time', $time, $args );
		as_schedule_single_action( $time, $hook, $args, $group );
	}

	/**
	 * @param $matched_recipe_ids
	 * @param $user_id
	 * @param $item_id
	 * @param $order_id
	 * @param $attempt
	 */
	public function automator_woo_order_item_added_handle( $matched_recipe_ids, $user_id, $item_id, $order_id, $attempt ) {
		$order          = wc_get_order( $order_id );
		$order_statuses = apply_filters(
			'automator_woocommerce_item_added_order_status',
			array(
				'processing',
				'completed',
			),
			$item_id,
			$order_id
		);
		if ( ! in_array( $order->get_status(), $order_statuses, true ) ) {
			$hook = 'automator_woo_order_item_added';
			++ $attempt;
			if ( $attempt > apply_filters( 'automator_woocommerce_item_added_attempts', 3, $item_id, $order_id ) ) {
				return;
			}
			$args = array(
				$matched_recipe_ids,
				$user_id,
				$item_id,
				$order_id,
				$attempt,
			);

			$this->schedule_a_delayed_trigger( $hook, $args );

			return;
		}

		global $uncanny_automator;
		foreach ( $matched_recipe_ids as $matched_recipe_id ) {
			$pass_args = array(
				'code'             => $this->trigger_code,
				'meta'             => $this->trigger_meta,
				'user_id'          => $user_id,
				'recipe_to_match'  => $matched_recipe_id['recipe_id'],
				'trigger_to_match' => $matched_recipe_id['trigger_id'],
				'ignore_post_id'   => true,
			);

			if ( 0 !== $user_id ) {
				$pass_args['is_signed_in'] = true;
			}

			$args = $uncanny_automator->process->user->maybe_add_trigger_entry( $pass_args, false );
			//Adding an action to save order id in trigger meta
			do_action( 'uap_wc_trigger_save_meta', $order_id, $matched_recipe_id['recipe_id'], $args, 'product' );
			do_action( 'uap_wc_order_item_meta', $item_id, $order_id, $matched_recipe_id['recipe_id'], $args );

			if ( $args ) {
				foreach ( $args as $result ) {
					if ( true === $result['result'] ) {
						$uncanny_automator->process->user->maybe_trigger_complete( $result['args'] );
					}
				}
			}
		}
	}
}
