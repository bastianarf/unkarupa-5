<?php

namespace Uncanny_Automator_Pro;

/**
 * Class WP_USERRECREPLYONCOMMENT
 * @package Uncanny_Automator_Pro
 */
class WP_USERRECREPLYONCOMMENT {

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
		$this->trigger_code = 'WPREPLYONCOMMENT';
		$this->trigger_meta = 'REPLYTOUSERSCOMMENT';
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
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			'is_pro'              => true,
			/* translators: Logged-in trigger - WordPress */
			'sentence'            => sprintf( esc_attr__( 'A user\'s comment on {{a specific type of post:%1$s}} receives a reply', 'uncanny-automator-pro' ), 'WPPOSTTYPES' ),
			/* translators: Logged-in trigger - WordPress */
			'select_option_name'  => esc_attr__( 'A user\'s comment on {{a specific type of post}} receives a reply', 'uncanny-automator-pro' ),
			'action'              => 'comment_post',
			'priority'            => 90,
			'accepted_args'       => 3,
			'validation_function' => array( $this, 'reply_on_comment' ),
			'options'             => [
				$uncanny_automator->helpers->recipe->wp->options->pro->all_wp_post_types(
					__( 'Post type', 'uncanny-automator-pro' ),
					'WPPOSTTYPES',
					array(
						'relevant_tokens' => array(
							'POSTTITLE'          => __( 'Post title', 'uncanny-automator-pro' ),
							'WPPOSTTYPES'        => __( 'Post type', 'uncanny-automator-pro' ),
							'COMMENTPARENT'      => __( 'Comment author', 'uncanny-automator-pro' ),
							'COMMENTID'          => __( 'Reply ID', 'uncanny-automator-pro' ),
							'COMMENTAUTHOR'      => __( 'Replier name', 'uncanny-automator-pro' ),
							'COMMENTAUTHOREMAIL' => __( 'Replier email', 'uncanny-automator-pro' ),
							'COMMENTAUTHORWEB'   => __( 'Replier website', 'uncanny-automator-pro' ),
							'COMMENTCONTENT'     => __( 'Reply', 'uncanny-automator-pro' ),
						)
					)
				)
			],
		);

		$uncanny_automator->register->trigger( $trigger );

		return;
	}

	/**
	 * Validation function when the trigger action is hit
	 *
	 * @param $comment_id
	 * @param $comment_approved
	 * @param $commentdata
	 */
	public function reply_on_comment( $comment_id, $comment_approved, $commentdata ) {

		global $uncanny_automator;

		if ( $commentdata['user_id'] && $commentdata['comment_parent'] != 0 ) {
			$recipes            = $uncanny_automator->get->recipes_from_trigger_code( $this->trigger_code );
			$required_post_type = $uncanny_automator->get->meta_from_recipes( $recipes, 'WPPOSTTYPES' );
			$post_type          = get_post_type( $commentdata['comment_post_ID'] );
			$parent_comment     = get_comment( $commentdata['comment_parent'] );
			$matched_recipe_ids = [];

			//Add where option is set to specific post type
			foreach ( $recipes as $recipe_id => $recipe ) {
				foreach ( $recipe['triggers'] as $trigger ) {
					$trigger_id = $trigger['ID'];
					if ( - 1 === intval( $required_post_type[ $recipe_id ][ $trigger_id ] ) ||
					     $required_post_type[ $recipe_id ][ $trigger_id ] == $post_type ) {
						$matched_recipe_ids[] = [
							'recipe_id'  => $recipe_id,
							'trigger_id' => $trigger_id,
						];
					}
				}
			}

			//	If recipe matches
			if ( ! empty( $matched_recipe_ids ) ) {
				foreach ( $matched_recipe_ids as $matched_recipe_id ) {
					$pass_args = [
						'code'             => $this->trigger_code,
						'meta'             => $this->trigger_meta,
						'user_id'          => $parent_comment->user_id,
						'recipe_to_match'  => $matched_recipe_id['recipe_id'],
						'trigger_to_match' => $matched_recipe_id['trigger_id'],
					];

					$args = $uncanny_automator->maybe_add_trigger_entry( $pass_args, false );
					if ( $args ) {
						foreach ( $args as $result ) {
							if ( true === $result['result'] ) {
								$trigger_meta = [
									'user_id'        => $commentdata['user_id'],
									'trigger_id'     => $result['args']['trigger_id'],
									'trigger_log_id' => $result['args']['get_trigger_id'],
									'run_number'     => $result['args']['run_number'],
								];

								// Comment ID
								$trigger_meta['meta_key']   = 'COMMENTID';
								$trigger_meta['meta_value'] = maybe_serialize( $comment_id );
								$uncanny_automator->insert_trigger_meta( $trigger_meta );

								// Comment Author
								$trigger_meta['meta_key']   = 'COMMENTAUTHOR';
								$trigger_meta['meta_value'] = maybe_serialize( $commentdata['comment_author'] );
								$uncanny_automator->insert_trigger_meta( $trigger_meta );

								// Comment Author Email
								$trigger_meta['meta_key']   = 'COMMENTAUTHOREMAIL';
								$trigger_meta['meta_value'] = maybe_serialize( $commentdata['comment_author_email'] );
								$uncanny_automator->insert_trigger_meta( $trigger_meta );

								// Comment Author Website
								$trigger_meta['meta_key']   = 'COMMENTAUTHORWEB';
								$trigger_meta['meta_value'] = maybe_serialize( $commentdata['comment_author_url'] );
								$uncanny_automator->insert_trigger_meta( $trigger_meta );

								// Comment Author Content
								$trigger_meta['meta_key']   = 'COMMENTCONTENT';
								$trigger_meta['meta_value'] = maybe_serialize( $commentdata['comment_content'] );
								$uncanny_automator->insert_trigger_meta( $trigger_meta );

								// Post Tokens
								$trigger_meta['meta_key']   = 'POSTTITLE';
								$trigger_meta['meta_value'] = maybe_serialize( get_post_field( 'post_title', (int) $commentdata['comment_post_ID'] ) );
								$uncanny_automator->insert_trigger_meta( $trigger_meta );

								$trigger_meta['meta_key']   = 'WPPOSTTYPES';
								$trigger_meta['meta_value'] = maybe_serialize( $post_type );
								$uncanny_automator->insert_trigger_meta( $trigger_meta );

								$trigger_meta['meta_key']   = 'COMMENTPARENT';
								$trigger_meta['meta_value'] = maybe_serialize( get_comment_author( $commentdata['comment_parent'] ) );
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


}
