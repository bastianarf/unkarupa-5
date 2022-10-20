<?php


namespace Uncanny_Automator_Pro;


use Uncanny_Automator\Woocommerce_Helpers;

/**
 * Class Woocommerce_Pro_Helpers
 * @package Uncanny_Automator_Pro
 */
class Woocommerce_Pro_Helpers extends Woocommerce_Helpers {

	/**
	 * @var bool
	 */
	public $load_options;

	/**
	 * Woocommerce_Pro_Helpers constructor.
	 */
	public function __construct() {
		// Selectively load options
		if ( property_exists( '\Uncanny_Automator\Woocommerce_Helpers', 'load_options' ) ) {
			global $uncanny_automator;
			$this->load_options = $uncanny_automator->helpers->recipe->maybe_load_trigger_options( __CLASS__ );
		} else {
			$this->load_options = true;
		}

		add_action( 'wp_ajax_select_variations_from_WOOSELECTVARIATION', [
			$this,
			'select_all_product_variations',
		] );
	}

	/**
	 * @param Woocommerce_Pro_Helpers $pro
	 */
	public function setPro( Woocommerce_Pro_Helpers $pro ) {
		$this->pro = $pro;
	}

	/**
	 * @param string $label
	 * @param string $option_code
	 *
	 * @return mixed
	 */
	public function all_wc_subscriptions( $label = null, $option_code = 'WOOSUBSCRIPTIONS' ) {
		if ( ! $this->load_options ) {
			global $uncanny_automator;

			return $uncanny_automator->helpers->recipe->build_default_options_array( $label, $option_code );
		}


		if ( ! $label ) {
			$label = __( 'Select a subscription', 'uncanny-automator-pro' );
		}

		global $wpdb;
		$q = "
			SELECT posts.ID, posts.post_title FROM $wpdb->posts as posts
			LEFT JOIN $wpdb->term_relationships as rel ON (posts.ID = rel.object_id)
			WHERE rel.term_taxonomy_id IN (SELECT term_id FROM $wpdb->terms WHERE slug IN ('subscription','variable-subscription'))
			AND posts.post_type = 'product'
			AND posts.post_status = 'publish'
			UNION ALL
			SELECT ID, post_title FROM $wpdb->posts
			WHERE post_type = 'shop_subscription'
			AND post_status = 'publish'
			ORDER BY post_title
		";

		// Query all subscription products based on the assigned product_type category (new WC type) and post_type shop_"
		$subscriptions = $wpdb->get_results( $q );

		$options       = array();
		$options['-1'] = __( 'Any subscription', 'uncanny-automator-pro' );

		foreach ( $subscriptions as $post ) {
			$title = $post->post_title;

			if ( empty( $title ) ) {
				$title = sprintf( __( 'ID: %s (no title)', 'uncanny-automator-pro' ), $post->ID );
			}

			$options[ $post->ID ] = $title;
		}

		$option = [
			'option_code'     => $option_code,
			'label'           => $label,
			'input_type'      => 'select',
			'required'        => true,
			'options'         => $options,
			'relevant_tokens' => [
				$option_code                                     => __( 'Product title', 'uncanny-automator' ),
				$option_code . '_ID'                             => __( 'Product ID', 'uncanny-automator' ),
				$option_code . '_URL'                            => __( 'Product URL', 'uncanny-automator' ),
				$option_code . '_THUMB_URL'                      => __( 'Product featured image URL', 'uncanny-automator' ),
				$option_code . '_THUMB_ID'                       => __( 'Product featured image ID', 'uncanny-automator' ),
				$option_code . '_SUBSCRIPTION_ID'                => __( 'Subscription ID', 'uncanny-automator-pro' ),
				$option_code . '_SUBSCRIPTION_STATUS'            => __( 'Subscription status', 'uncanny-automator-pro' ),
				$option_code . '_SUBSCRIPTION_TRIAL_END_DATE'    => __( 'Subscription trial end date', 'uncanny-automator-pro' ),
				$option_code . '_SUBSCRIPTION_END_DATE'          => __( 'Subscription end date', 'uncanny-automator-pro' ),
				$option_code . '_SUBSCRIPTION_NEXT_PAYMENT_DATE' => __( 'Subscription next payment date', 'uncanny-automator-pro' ),
			],
		];

		return apply_filters( 'uap_option_all_wc_subscriptions', $option );
	}

	/**
	 * @param null $label
	 * @param string $option_code
	 *
	 * @return array|mixed|void
	 */
	public function all_wc_variation_subscriptions( $label = null, $option_code = 'WOOSUBSCRIPTIONS', $args = array() ) {

		if ( ! $this->load_options ) {
			global $uncanny_automator;

			return $uncanny_automator->helpers->recipe->build_default_options_array( $label, $option_code );
		}

		if ( ! $label ) {
			$label = __( 'Select a variable subscription', 'uncanny-automator-pro' );
		}

		$token         = key_exists( 'token', $args ) ? $args['token'] : false;
		$is_ajax       = key_exists( 'is_ajax', $args ) ? $args['is_ajax'] : false;
		$target_field  = key_exists( 'target_field', $args ) ? $args['target_field'] : '';
		$end_point     = key_exists( 'endpoint', $args ) ? $args['endpoint'] : '';
		$options       = array();
		$options['-1'] = __( 'Any variable subscription', 'uncanny-automator-pro' );

		$subscription_products = array();

		if ( function_exists( 'wc_get_products' ) ) {
			$subscription_products = wc_get_products( array(
				'type'    => array( 'variable-subscription' ),
				'limit'   => - 1,
				'orderby' => 'date',
				'order'   => 'DESC',
			) );
		}

		foreach ( $subscription_products as $product ) {

			$title = $product->get_title();

			if ( empty( $title ) ) {
				$title = sprintf( __( 'ID: %s (no title)', 'uncanny-automator-pro' ), $product->get_id() );
			}

			$options[ $product->get_id() ] = $title;

		}

		$option = [
			'option_code'     => $option_code,
			'label'           => $label,
			'input_type'      => 'select',
			'required'        => true,
			'supports_tokens' => $token,
			'is_ajax'         => $is_ajax,
			'fill_values_in'  => $target_field,
			'endpoint'        => $end_point,
			'options'         => $options,
			'relevant_tokens' => array(
				$option_code                                     => __( 'Variation title', 'uncanny-automator-pro' ),
				$option_code . '_ID'                             => __( 'Variation ID', 'uncanny-automator-pro' ),
				$option_code . '_URL'                            => __( 'Variation URL', 'uncanny-automator-pro' ),
				$option_code . '_THUMB_URL'                      => __( 'Variation featured image URL', 'uncanny-automator-pro' ),
				$option_code . '_THUMB_ID'                       => __( 'Variation featured image ID', 'uncanny-automator-pro' ),
				$option_code . '_PRODUCT'                        => __( 'Product title', 'uncanny-automator' ),
				$option_code . '_PRODUCT_ID'                     => __( 'Product ID', 'uncanny-automator' ),
				$option_code . '_PRODUCT_URL'                    => __( 'Product URL', 'uncanny-automator' ),
				$option_code . '_PRODUCT_THUMB_URL'              => __( 'Product featured image URL', 'uncanny-automator' ),
				$option_code . '_PRODUCT_THUMB_ID'               => __( 'Product featured image ID', 'uncanny-automator' ),
				$option_code . '_SUBSCRIPTION_ID'                => __( 'Subscription ID', 'uncanny-automator-pro' ),
				$option_code . '_SUBSCRIPTION_STATUS'            => __( 'Subscription status', 'uncanny-automator-pro' ),
				$option_code . '_SUBSCRIPTION_TRIAL_END_DATE'    => __( 'Subscription trial end date', 'uncanny-automator-pro' ),
				$option_code . '_SUBSCRIPTION_END_DATE'          => __( 'Subscription end date', 'uncanny-automator-pro' ),
				$option_code . '_SUBSCRIPTION_NEXT_PAYMENT_DATE' => __( 'Subscription next payment date', 'uncanny-automator-pro' ),
			),
		];

		return apply_filters( 'uap_option_all_wc_variation_subscriptions', $option );
	}

	/**
	 * @param null $label
	 * @param string $option_code
	 * @param array $args
	 *
	 * @return mixed|void
	 */
	public function all_wc_variable_products( $label = null, $option_code = 'WOOVARIABLEPRODUCTS', $args = [] ) {
		if ( ! $this->load_options ) {
			global $uncanny_automator;

			return $uncanny_automator->helpers->recipe->build_default_options_array( $label, $option_code );
		}


		if ( ! $label ) {
			$label = __( 'Product', 'uncanny-automator' );
		}

		$token        = key_exists( 'token', $args ) ? $args['token'] : false;
		$is_ajax      = key_exists( 'is_ajax', $args ) ? $args['is_ajax'] : false;
		$target_field = key_exists( 'target_field', $args ) ? $args['target_field'] : '';
		$end_point    = key_exists( 'endpoint', $args ) ? $args['endpoint'] : '';

		global $wpdb;
		$query = "
			SELECT posts.ID, posts.post_title FROM $wpdb->posts as posts
			LEFT JOIN $wpdb->term_relationships as rel ON (posts.ID = rel.object_id)
			WHERE rel.term_taxonomy_id IN (SELECT term_id FROM $wpdb->terms WHERE slug IN ('variable'))
			AND posts.post_type = 'product'
			AND posts.post_status = 'publish'
			ORDER BY post_title
		";

		$all_products  = $wpdb->get_results( $query );
		$options       = array();
		$options['-1'] = __( 'Any product', 'uncanny-automator' );

		foreach ( $all_products as $product ) {
			$title = $product->post_title;
			if ( empty( $title ) ) {
				$title = sprintf( __( 'ID: %s (no title)', 'uncanny-automator-pro' ), $product->ID );
			}
			$options[ $product->ID ] = $title;
		}

		$option = [
			'option_code'     => $option_code,
			'label'           => $label,
			'input_type'      => 'select',
			'required'        => true,
			'supports_tokens' => $token,
			'is_ajax'         => $is_ajax,
			'fill_values_in'  => $target_field,
			'endpoint'        => $end_point,
			'options'         => $options,
			'relevant_tokens' => [
				$option_code                => __( 'Product title', 'uncanny-automator' ),
				$option_code . '_ID'        => __( 'Product ID', 'uncanny-automator' ),
				$option_code . '_URL'       => __( 'Product URL', 'uncanny-automator' ),
				$option_code . '_THUMB_ID'  => __( 'Product featured image ID', 'uncanny-automator' ),
				$option_code . '_THUMB_URL' => __( 'Product featured image URL', 'uncanny-automator' ),
			],
		];

		return apply_filters( 'uap_option_all_wc_variable_products', $option );
	}

	/**
	 * @param null $label
	 * @param string $option_code
	 *
	 * @return mixed|void
	 */
	public function all_wc_product_categories( $label = null, $option_code = 'WOOPRODCAT', $args = [] ) {
		if ( ! $this->load_options ) {
			global $uncanny_automator;

			return $uncanny_automator->helpers->recipe->build_default_options_array( $label, $option_code );
		}

		$supports_multiple_values = key_exists( 'supports_multiple_values', $args ) ? $args['supports_multiple_values'] : false;
		$description              = key_exists( 'description', $args ) ? $args['description'] : false;
		$required                 = key_exists( 'required', $args ) ? $args['required'] : true;
		if ( ! $label ) {
			$label = __( 'Categories', 'uncanny-automator-pro' );
		}

		global $wpdb;
		$query = "
			SELECT terms.term_id,terms.name FROM $wpdb->terms as terms
			LEFT JOIN $wpdb->term_taxonomy as rel ON (terms.term_id = rel.term_id)
			WHERE rel.taxonomy = 'product_cat'
			ORDER BY terms.name";

		$categories = $wpdb->get_results( $query );

		$options = array();

		foreach ( $categories as $category ) {
			$title = $category->name;
			if ( empty( $title ) ) {
				$title = sprintf( __( 'ID: %s (no title)', 'uncanny-automator-pro' ), $category->term_id );
			}
			$options[ $category->term_id ] = $title;
		}

		$option = [
			'option_code'              => $option_code,
			'label'                    => $label,
			'description'              => $description,
			'input_type'               => 'select',
			'required'                 => $required,
			'options'                  => $options,
			'supports_multiple_values' => $supports_multiple_values,
			'relevant_tokens'          => [
				$option_code          => __( 'Category title', 'uncanny-automator' ),
				$option_code . '_ID'  => __( 'Category ID', 'uncanny-automator' ),
				$option_code . '_URL' => __( 'Category URL', 'uncanny-automator' ),
			],
		];

		return apply_filters( 'uap_option_all_wc_product_categories', $option );
	}

	/**
	 * @param null $label
	 * @param string $option_code
	 * @param array $args
	 *
	 * @return array|mixed|void
	 */
	public function all_wc_payment_gateways( $label = null, $option_code = 'WOOPAYMENTGATEWAY', $args = [] ) {
		if ( ! $this->load_options ) {
			global $uncanny_automator;

			return $uncanny_automator->helpers->recipe->build_default_options_array( $label, $option_code );
		}

		$description = key_exists( 'description', $args ) ? $args['description'] : false;
		$required    = key_exists( 'required', $args ) ? $args['required'] : true;
		if ( ! $label ) {
			$label = __( 'Payment method', 'uncanny-automator-pro' );
		}

		$methods = WC()->payment_gateways->payment_gateways();

		$options = array();

		$options['-1'] = __( 'Any payment method', 'uncanny-automator-pro' );

		foreach ( $methods as $method ) {
			if ( $method->enabled == 'yes' ) {
				$title = $method->title;
				if ( empty( $title ) ) {
					$title = sprintf( __( 'ID: %s (no title)', 'uncanny-automator-pro' ), $method->id );
				}
				$options[ $method->id ] = $title;
			}
		}

		$option = [
			'option_code'              => $option_code,
			'label'                    => $label,
			'description'              => $description,
			'input_type'               => 'select',
			'required'                 => $required,
			'options'                  => $options,
			'supports_multiple_values' => false,
		];

		return apply_filters( 'uap_option_all_wc_payment_gateways', $option );
	}

	/**
	 * @param null $label
	 * @param string $option_code
	 *
	 * @return mixed|void
	 */
	public function all_wc_product_tags( $label = null, $option_code = 'WOOPRODTAG' ) {
		if ( ! $this->load_options ) {
			global $uncanny_automator;

			return $uncanny_automator->helpers->recipe->build_default_options_array( $label, $option_code );
		}

		if ( ! $label ) {
			$label = __( 'Categories', 'uncanny-automator-pro' );
		}

		global $wpdb;
		$query = "
			SELECT terms.term_id,terms.name FROM $wpdb->terms as terms
			LEFT JOIN $wpdb->term_taxonomy as rel ON (terms.term_id = rel.term_id)
			WHERE rel.taxonomy = 'product_tag'
			ORDER BY terms.name";

		$tags = $wpdb->get_results( $query );


		$options       = array();
		$options['-1'] = __( 'Any tag', 'uncanny-automator-pro' );

		foreach ( $tags as $tag ) {
			$title = $tag->name;
			if ( empty( $title ) ) {
				$title = sprintf( __( 'ID: %s (no title)', 'uncanny-automator-pro' ), $tag->term_id );
			}
			$options[ $tag->term_id ] = $title;
		}

		$option = [
			'option_code'     => $option_code,
			'label'           => $label,
			'input_type'      => 'select',
			'required'        => true,
			'options'         => $options,
			'relevant_tokens' => [
				$option_code          => __( 'Tag title', 'uncanny-automator' ),
				$option_code . '_ID'  => __( 'Tag ID', 'uncanny-automator' ),
				$option_code . '_URL' => __( 'Tag URL', 'uncanny-automator' ),
			],
		];

		return apply_filters( 'uap_option_all_wc_product_tags', $option );
	}

	/**
	 * get all variations of selected variable product
	 */
	public function select_all_product_variations() {
		global $uncanny_automator;

		// Nonce and post object validation
		$uncanny_automator->utilities->ajax_auth_check( $_POST );

		$fields = array();
		if ( isset( $_POST['value'] ) && ! empty( $_POST['value'] ) ) {

			$args = array(
				'post_type'      => 'product_variation',
				'post_parent'    => absint( $_POST['value'] ),
				'posts_per_page' => 999,
				'orderby'        => 'ID',
				'order'          => 'ASC',
				'post_status'    => 'publish',
			);

			$options = get_posts( $args );
			if ( isset( $options ) && ! empty( $options ) ) {
				foreach ( $options as $option ) {
					$fields[] = array(
						'value' => $option->ID,
						'text'  => ! empty( $option->post_excerpt ) ? $option->post_excerpt : $option->post_title,
					);
				}
			} else {
				$fields[] = array(
					'value' => - 1,
					'text'  => __( 'Any variation', 'uncanny-automator-pro' ),
				);
			}
		}
		echo wp_json_encode( $fields );
		die();
	}

	/**
	 * Fetch labels for trigger conditions.
	 *
	 * @return array
	 * @since 2.10
	 *
	 */
	public function get_trigger_condition_labels() {
		/**
		 * Filters WooCommerce Integrations' trigger conditions.
		 *
		 * @param array $trigger_conditions An array of key-value pairs of action hook handle and human readable label.
		 */
		return apply_filters(
			'uap_wc_trigger_conditions',
			array(
				'woocommerce_payment_complete'       => _x( 'pays for', 'WooCommerce', 'uncanny-automator-pro' ),
				'woocommerce_order_status_completed' => _x( 'completes', 'WooCommerce', 'uncanny-automator-pro' ),
				'woocommerce_thankyou'               => _x( 'lands on a thank you page for', 'WooCommerce', 'uncanny-automator-pro' ),
			)
		);
	}

	/**
	 * @param string $code
	 *
	 * @return mixed|void
	 */
	public function get_woocommerce_trigger_conditions( $code = 'TRIGGERCOND' ) {
		$options = array(
			'option_code' => $code,
			/* translators: Noun */
			'label'       => esc_attr__( 'Trigger condition', 'uncanny-automator-pro' ),
			'input_type'  => 'select',
			'required'    => true,
			'options'     => $this->get_trigger_condition_labels(),
		);

		return apply_filters( 'uap_option_woocommerce_trigger_conditions', $options );
	}

	/**
	 * @param string $label
	 * @param string $option_code
	 *
	 * @return mixed
	 */
	public function all_wc_products_multiselect( $label = null, $option_code = 'WOOPRODUCT', $settings = [] ) {
		if ( ! $this->load_options ) {
			global $uncanny_automator;

			return $uncanny_automator->helpers->recipe->build_default_options_array( $label, $option_code );
		}

		if ( ! $label ) {
			$label = esc_attr__( 'Product', 'uncanny-automator' );
		}
		$description = '';
		if ( isset( $settings['description'] ) ) {
			$description = $settings['description'];
		}

		$required = key_exists( 'required', $settings ) ? $settings['required'] : true;

		$args = [
			'post_type'      => 'product',
			'posts_per_page' => 999,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'post_status'    => 'publish',
		];

		global $uncanny_automator;
		$options = $uncanny_automator->helpers->recipe->options->wp_query( $args, false, esc_attr__( 'Any product', 'uncanny-automator' ) );

		$option = [
			'option_code'              => $option_code,
			'label'                    => $label,
			'description'              => $description,
			'input_type'               => 'select',
			'required'                 => $required,
			'options'                  => $options,
			'supports_multiple_values' => true,
			'relevant_tokens'          => [
				$option_code                => esc_attr__( 'Product title', 'uncanny-automator' ),
				$option_code . '_ID'        => esc_attr__( 'Product ID', 'uncanny-automator' ),
				$option_code . '_URL'       => esc_attr__( 'Product URL', 'uncanny-automator' ),
				$option_code . '_THUMB_ID'  => esc_attr__( 'Product featured image ID', 'uncanny-automator' ),
				$option_code . '_THUMB_URL' => esc_attr__( 'Product featured image URL', 'uncanny-automator' ),
			],
		];

		return apply_filters( 'uap_option_all_wc_products', $option );
	}

	/**
	 * @param null $label
	 * @param string $option_code
	 *
	 * @return mixed|void
	 */
	public function get_wcs_statuses( $label = null, $option_code = 'WOOSUBSCRIPTIONSTATUS' ) {
		if ( ! $label ) {
			$label = esc_attr__( 'Status', 'uncanny-automator' );
		}
		$statuses      = wcs_get_subscription_statuses();
		$options       = array();
		$options['-1'] = __( 'Any status', 'uncanny-automator-pro' );
		$options       = $options + $statuses;

		$option = [
			'option_code' => $option_code,
			'label'       => $label,
			'input_type'  => 'select',
			'required'    => true,
			'options'     => $options,
//			'relevant_tokens' => [
//				$option_code                     => __( 'Subscription status', 'uncanny-automator' ),
//				$option_code . '_ID'             => __( 'Subscription ID', 'uncanny-automator' ),
//				$option_code . '_END_DATE'       => __( 'Subscription end date', 'uncanny-automator' ),
//				$option_code . '_TRIAL_END_DATE' => __( 'Subscription trial end date', 'uncanny-automator' ),
//			],
		];

		return apply_filters( 'uap_option_all_wc_statuses', $option );
	}


	/**
	 * @param $item_id
	 * @param $order_id
	 *
	 * @return array|\WC_Order_Item
	 */
	public static function get_order_item_by_id( $item_id, $order_id ) {
		$order = wc_get_order( $order_id );
		foreach ( $order->get_items() as $line_item_id => $line_item ) {
			if ( $item_id === $line_item_id ) {
				return $line_item;
			}
		}

		return array();
	}
}
