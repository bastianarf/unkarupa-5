<?php

namespace Uncanny_Automator_Pro;

/**
 * Class WP_POSTINSTATUSUPDATED
 * @package Uncanny_Automator_Pro
 */
class WP_POSTINSTATUSUPDATED {

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
		$this->trigger_code = 'WPPOSTINSTATUS';
		$this->trigger_meta = 'POSTSTATUSUPDATED';
		if ( is_admin() && empty( $_POST ) ) {
			add_action( 'wp_loaded', array( $this, 'plugins_loaded' ), 99 );
		} else {
			$this->define_trigger();
		}
	}

	/**
	 * Delay loading trigger setting
	 */
	public function plugins_loaded() {
		$this->define_trigger();
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {

		global $uncanny_automator;

		$relevant_tokens = [
			'POSTSTATUSUPDATED' => __( 'Status', 'uncanny-automator-pro' ),
			'POSTTITLE'         => __( 'Post Title', 'uncanny-automator-pro' ),
			'POSTID'            => __( 'Post ID', 'uncanny-automator-pro' ),
			'POSTCONTENT'       => __( 'Post Content', 'uncanny-automator-pro' ),
			'POSTURL'           => __( 'Post URL', 'uncanny-automator-pro' ),
			'POSTAUTHORFN'      => __( 'Author first name', 'uncanny-automator-pro' ),
			'POSTAUTHORLN'      => __( 'Author last name', 'uncanny-automator-pro' ),
			'POSTAUTHORDN'      => __( 'Author display name', 'uncanny-automator-pro' ),
			'POSTAUTHOREMAIL'   => __( 'Author email', 'uncanny-automator-pro' ),
		];

		$trigger = array(
			'author'              => $uncanny_automator->get_author_name( $this->trigger_code ),
			'support_link'        => $uncanny_automator->get_author_support_link( $this->trigger_code ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			'meta'                => $this->trigger_meta,
			/* translators: Logged-in trigger - WordPress Core */
			'sentence'            => sprintf( __( 'A user updates a post in {{a specific:%1$s}} status', 'uncanny-automator-pro' ), $this->trigger_meta ),
			/* translators: Logged-in trigger - WordPress Core */
			'select_option_name'  => __( 'A user updates a post in {{a specific}} status', 'uncanny-automator-pro' ),
			'action'              => 'post_updated',
			'priority'            => 10,
			'accepted_args'       => 3,
			'validation_function' => array( $this, 'wp_post_updated' ),
			'options_group'       => [
				$this->trigger_meta => [
					$uncanny_automator->helpers->recipe->wp->options->pro->all_wp_post_types(
						__( 'Post Type', 'uncanny-automator-pro' ),
						'WPPOSTTYPES',
						[
							'token'           => true,
							'is_ajax'         => false,
							'relevant_tokens' => []
						]
					),
					$uncanny_automator->helpers->recipe->wp->options->pro->wp_post_statuses(
						__( 'Status', 'uncanny-automator-pro' ), $this->trigger_meta, [
						'is_any'          => true,
						'relevant_tokens' => $relevant_tokens
					] ),
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
	 */

	public function wp_post_updated( $postID, $post_after, $post_before ) {
		global $uncanny_automator;
		$recipes              = $uncanny_automator->get->recipes_from_trigger_code( $this->trigger_code );
		$required_post_type   = $uncanny_automator->get->meta_from_recipes( $recipes, 'WPPOSTTYPES' );
		$required_post_status = $uncanny_automator->get->meta_from_recipes( $recipes, $this->trigger_meta );
		$post                 = get_post( $postID );

		// $term_ids           = [];
		$matched_recipe_ids = [];

		$user_obj = get_user_by( 'ID', (int) $post_before->post_author );

		foreach ( $recipes as $recipe_id => $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];

				// is post type
				if (
					'-1' === $required_post_type[ $recipe_id ][ $trigger_id ] // any post type
					|| $post->post_type === $required_post_type[ $recipe_id ][ $trigger_id ] // specific post type
					|| empty( $required_post_type[ $recipe_id ][ $trigger_id ] ) // Backwards compatibility -- the trigger didnt have a post type selection pre 2.10
				) {
					// is post status
					if ( - 1 === intval( $required_post_status[ $recipe_id ][ $trigger_id ] ) || $required_post_status[ $recipe_id ][ $trigger_id ] == $post_before->post_status ) {
						$matched_recipe_ids[] = [
							'recipe_id'  => $recipe_id,
							'trigger_id' => $trigger_id,
						];
						break;
					}
				}
			}
		}

		if ( ! empty( $matched_recipe_ids ) ) {
			foreach ( $matched_recipe_ids as $matched_recipe_id ) {
				$pass_args = [
					'code'             => $this->trigger_code,
					'meta'             => $this->trigger_meta,
					'user_id'          => $user_obj->ID,
					'recipe_to_match'  => $matched_recipe_id['recipe_id'],
					'trigger_to_match' => $matched_recipe_id['trigger_id'],
					'ignore_post_id'   => true,
				];

				$args = $uncanny_automator->maybe_add_trigger_entry( $pass_args, false );

				if ( $args ) {
					foreach ( $args as $result ) {
						if ( true === $result['result'] ) {

							$trigger_meta = [
								'user_id'        => $user_obj->ID,
								'trigger_id'     => $result['args']['trigger_id'],
								'trigger_log_id' => $result['args']['get_trigger_id'],
								'run_number'     => $result['args']['run_number'],
							];

							// Post Title Token
							$trigger_meta['meta_key']   = $result['args']['trigger_id'] . ':' . $this->trigger_code . ':POSTTITLE';
							$trigger_meta['meta_value'] = maybe_serialize( $post_after->post_title );
							$uncanny_automator->insert_trigger_meta( $trigger_meta );

							// Post ID Token
							$trigger_meta['meta_key']   = $result['args']['trigger_id'] . ':' . $this->trigger_code . ':POSTID';
							$trigger_meta['meta_value'] = maybe_serialize( $post_after->ID );
							$uncanny_automator->insert_trigger_meta( $trigger_meta );

							// Post URL Token
							$trigger_meta['meta_key']   = $result['args']['trigger_id'] . ':' . $this->trigger_code . ':POSTURL';
							$trigger_meta['meta_value'] = maybe_serialize( get_permalink( $post_after->ID ) );
							$uncanny_automator->insert_trigger_meta( $trigger_meta );

							// Post Content Token
							$trigger_meta['meta_key']   = $result['args']['trigger_id'] . ':' . $this->trigger_code . ':POSTCONTENT';
							$trigger_meta['meta_value'] = maybe_serialize( $post_after->post_content );
							$uncanny_automator->insert_trigger_meta( $trigger_meta );

							// Post Status Token
							$trigger_meta['meta_key']   = $result['args']['trigger_id'] . ':' . $this->trigger_code . ':POSTSTATUS';
							$trigger_meta['meta_value'] = maybe_serialize( $post_after->post_status );
							$uncanny_automator->insert_trigger_meta( $trigger_meta );

							// Post Author First Name Token
							$trigger_meta['meta_key']   = $result['args']['trigger_id'] . ':' . $this->trigger_code . ':POSTAUTHORFN';
							$trigger_meta['meta_value'] = maybe_serialize( $user_obj->first_name );
							$uncanny_automator->insert_trigger_meta( $trigger_meta );

							// Post Author Last Name Token
							$trigger_meta['meta_key']   = $result['args']['trigger_id'] . ':' . $this->trigger_code . ':POSTAUTHORLN';
							$trigger_meta['meta_value'] = maybe_serialize( $user_obj->last_name );
							$uncanny_automator->insert_trigger_meta( $trigger_meta );

							// Post Author Display Name Token
							$trigger_meta['meta_key']   = $result['args']['trigger_id'] . ':' . $this->trigger_code . ':POSTAUTHORDN';
							$trigger_meta['meta_value'] = maybe_serialize( $user_obj->display_name );
							$uncanny_automator->insert_trigger_meta( $trigger_meta );

							// Post Author Email Token
							$trigger_meta['meta_key']   = $result['args']['trigger_id'] . ':' . $this->trigger_code . ':POSTAUTHOREMAIL';
							$trigger_meta['meta_value'] = maybe_serialize( $user_obj->user_email );
							$uncanny_automator->insert_trigger_meta( $trigger_meta );

							// $trigger_meta['meta_key']   = $result['args']['trigger_id'] . ':' . $this->trigger_code . ':' . $this->trigger_meta;
							// $trigger_meta['meta_value'] = maybe_serialize( $required_term[ $recipe_id ][ $trigger_id ] );
							// $uncanny_automator->insert_trigger_meta( $trigger_meta );

							$uncanny_automator->maybe_trigger_complete( $result['args'] );
						}
					}
				}
			}

		}

		return;
	}

}
