<?php

namespace Uncanny_Automator_Pro;

/**
 * Class WP_SETPOSTSTATUS
 * @package Uncanny_Automator_Pro
 */
class WP_SETPOSTSTATUS {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'WP';

	private $action_code;
	private $action_meta;

	/**
	 * Set up Automator action constructor.
	 */
	public function __construct() {
		$this->action_code = 'SETPOSTSTATUS';
		$this->action_meta = 'WPSETPOSTSTATUS';
		global $uncanny_automator;
		if ( $uncanny_automator->helpers->recipe->is_edit_page() ) {
			add_action( 'wp_loaded', array( $this, 'plugins_loaded' ), 99 );
		} else {
			$this->define_action();
		}
	}

	public function plugins_loaded() {
		$this->define_action();
	}

	/**
	 * Define and register the action by pushing it into the Automator object
	 */
	public function define_action() {

		global $uncanny_automator;

		$action = array(
			'author'             => $uncanny_automator->get_author_name( $this->action_code ),
			'support_link'       => $uncanny_automator->get_author_support_link( $this->action_code, 'integration/wordpress-core/' ),
			'is_pro'             => true,
			'integration'        => self::$integration,
			'code'               => $this->action_code,
			'requires_user'      => false,
			/* translators: Action - WordPress Core */
			'sentence'           => sprintf( __( 'Set {{a post:%1$s}} to {{a status:%2$s}}', 'uncanny-automator-pro' ), $this->action_meta, 'SETSPECIFICSTATUS' ),
			/* translators: Action - WordPress Core */
			'select_option_name' => __( 'Set {{a post}} to {{a status}}', 'uncanny-automator-pro' ),
			'priority'           => 11,
			'accepted_args'      => 3,
			'execution_function' => array( $this, 'set_post_status' ),
			'options_group'      => [
				$this->action_meta  => [
					$uncanny_automator->helpers->recipe->wp->options->pro->all_wp_post_types( __( 'Post type', 'uncanny-automator-pro' ), 'WPSPOSTTYPES', [
						'token'        => false,
						'is_ajax'      => true,
						'target_field' => $this->action_meta,
						'is_any'       => false,
						'endpoint'     => 'select_all_post_of_selected_post_type',
					] ),
					$uncanny_automator->helpers->recipe->field->select_field( $this->action_meta, __( 'Post', 'uncanny-automator-pro' ) ),
				],
				'SETSPECIFICSTATUS' => [
					$uncanny_automator->helpers->recipe->wp->options->pro->wp_post_statuses( null, 'SETSPECIFICSTATUS' ),
				],
			],
		);

		$uncanny_automator->register->action( $action );
	}

	/**
	 * @param $user_id
	 * @param $action_data
	 * @param $recipe_id
	 * @param $args
	 */
	public function set_post_status( $user_id, $action_data, $recipe_id, $args ) {
		global $wpdb;
		global $uncanny_automator;

		$post_type   = $action_data['meta']['WPSPOSTTYPES'];
		$post_id     = $action_data['meta'][ $this->action_meta ];
		$post_status = $action_data['meta']['SETSPECIFICSTATUS'];

		if ( $post_id == - 1 ) {
			$wpdb->update( $wpdb->posts, array( 'post_status' => $post_status ), array( 'post_type' => $post_type ) );
		} else {
			$wpdb->update( $wpdb->posts, array( 'post_status' => $post_status ), array( 'ID' => $post_id ) );
		}

		$uncanny_automator->complete_action( $user_id, $action_data, $recipe_id );

		return;
	}

}
