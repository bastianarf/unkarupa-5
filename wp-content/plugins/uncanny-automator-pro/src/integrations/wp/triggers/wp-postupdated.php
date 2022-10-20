<?php

namespace Uncanny_Automator_Pro;

/**
 * Class WP_POSTUPDATED
 * @package Uncanny_Automator_Pro
 */
class WP_POSTUPDATED {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'WP';

	private $trigger_code;
	private $trigger_meta;

	/**
	 * Set up Automator trigger constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'WPPOSTUPDATED';
		$this->trigger_meta = 'POSTUPDATED';
		if ( is_admin() ) {
			add_action( 'wp_loaded', array( $this, 'plugins_loaded' ), 99 );
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

		$trigger = array(
			'author'              => $uncanny_automator->get_author_name( $this->trigger_code ),
			'support_link'        => $uncanny_automator->get_author_support_link( $this->trigger_code, 'integration/wordpress-core/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			'meta'                => $this->trigger_meta,
			/* translators: Logged-in trigger - WordPress Core */
			'sentence'            => sprintf( __( 'A user updates {{a post:%2$s}}', 'uncanny-automator-pro' ),
				'WPPOSTTYPES' . ':' . $this->trigger_meta, $this->trigger_meta ),
			/* translators: Logged-in trigger - WordPress Core */
			'select_option_name'  => __( 'A user updates {{a post}}', 'uncanny-automator-pro' ),
			'action'              => 'post_updated',
			'priority'            => 10,
			'accepted_args'       => 3,
			'validation_function' => array( $this, 'wp_post_updated' ),
			'options_group'       => [
				$this->trigger_meta => [
					$uncanny_automator->helpers->recipe->wp->options->pro->all_wp_post_types(
						__( 'Post type', 'uncanny-automator-pro' ),
						'WPPOSTTYPES',
						[
							'token'        => false,
							'is_ajax'      => true,
							'target_field' => $this->trigger_meta,
							'endpoint'     => 'select_all_post_from_SELECTEDPOSTTYPE',
						]
					),
					$uncanny_automator->helpers->recipe->field->select_field(
						$this->trigger_meta,
						__( 'Post', 'uncanny-automator-pro' )
					),
				],
			],
		);

		$uncanny_automator->register->trigger( $trigger );

		return;
	}

	/**
	 * Validation function when the trigger action is hit
	 *
	 * @param $postID
	 * @param $post_after
	 * @param $post_before
	 *
	 * @return bool|void
	 */
	public function wp_post_updated( $postID, $post_after, $post_before ) {

		// Avoid double call. T#25676
		if ( isset( $_GET['meta-box-loader'] ) === true ) { // phpcs:ignore WordPress.Security.NonceVerification
			return false;
		}

		global $uncanny_automator;

		$user_id            = get_current_user_id();
		$recipes            = $uncanny_automator->get->recipes_from_trigger_code( $this->trigger_code );
		$required_post_type = $uncanny_automator->get->meta_from_recipes( $recipes, 'WPPOSTTYPES' );
		$required_post      = $uncanny_automator->get->meta_from_recipes( $recipes, $this->trigger_meta );

		$matched_recipe_ids = [];

		foreach ( $recipes as $recipe_id => $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];

				if ( ! isset( $required_post_type[ $recipe_id ] ) ) {
					continue;
				}

				if ( ! isset( $required_post_type[ $recipe_id ][ $trigger_id ] ) ) {
					continue;
				}

				if ( ! isset( $required_post[ $recipe_id ] ) ) {
					continue;
				}

				if ( ! isset( $required_post[ $recipe_id ][ $trigger_id ] ) ) {
					continue;
				}
				//Add where option is set to Any post type
				if ( - 1 === intval( $required_post_type[ $recipe_id ][ $trigger_id ] ) ) {
					$matched_recipe_ids[] = [
						'recipe_id'  => $recipe_id,
						'trigger_id' => $trigger_id,
					];
					break;
				} //Add where option is set to Any post of specific post type
				elseif ( $required_post_type[ $recipe_id ][ $trigger_id ] === $post_before->post_type && - 1 === intval( $required_post[ $recipe_id ][ $trigger_id ] ) ) {
					$matched_recipe_ids[] = [
						'recipe_id'  => $recipe_id,
						'trigger_id' => $trigger_id,
					];
					break;
				} elseif ( $required_post_type[ $recipe_id ][ $trigger_id ] == $post_before->post_type && $postID == $required_post[ $recipe_id ][ $trigger_id ] ) {
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
							$trigger_meta = [
								'user_id'        => $user_id,
								'trigger_id'     => $result['args']['trigger_id'],
								'trigger_log_id' => $result['args']['get_trigger_id'],
								'run_number'     => $result['args']['run_number'],
							];
							// Post Title Token
							$trigger_meta['meta_key']   = $result['args']['trigger_id'] . ':' . $this->trigger_code . ':WPPOSTTYPES';
							$trigger_meta['meta_value'] = maybe_serialize( $post_before->post_title );
							$uncanny_automator->insert_trigger_meta( $trigger_meta );

							// Post ID Token
							$trigger_meta['meta_key']   = $result['args']['trigger_id'] . ':' . $this->trigger_code . ':WPPOSTTYPES_ID';
							$trigger_meta['meta_value'] = maybe_serialize( $post_before->ID );
							$uncanny_automator->insert_trigger_meta( $trigger_meta );

							// Post URL Token
							$trigger_meta['meta_key']   = $result['args']['trigger_id'] . ':' . $this->trigger_code . ':WPPOSTTYPES_URL';
							$trigger_meta['meta_value'] = maybe_serialize( get_permalink( $post_before->ID ) );
							$uncanny_automator->insert_trigger_meta( $trigger_meta );

							// Post Image URL Token
							$trigger_meta['meta_key']   = $result['args']['trigger_id'] . ':' . $this->trigger_code . ':WPPOSTTYPES_THUMB_URL';
							$trigger_meta['meta_value'] = maybe_serialize( get_the_post_thumbnail_url( $post_before->ID, 'full' ) );
							$uncanny_automator->insert_trigger_meta( $trigger_meta );

							// Post Image ID Token
							$trigger_meta['meta_key']   = $result['args']['trigger_id'] . ':' . $this->trigger_code . ':WPPOSTTYPES_THUMB_ID';
							$trigger_meta['meta_value'] = maybe_serialize( get_post_thumbnail_id( $post_before->ID ) );
							$uncanny_automator->insert_trigger_meta( $trigger_meta );

							$uncanny_automator->maybe_trigger_complete( $result['args'] );
						}
					}
				}
			}

		}

		return;
	}
}
