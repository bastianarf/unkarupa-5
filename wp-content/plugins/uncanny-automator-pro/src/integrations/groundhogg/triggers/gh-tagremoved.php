<?php

namespace Uncanny_Automator_Pro;

use Groundhogg\DB\Tags;

/**
 * Class GH_TAGREMOVED
 * @package Uncanny_Automator_Pro
 */
class GH_TAGREMOVED {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'GH';

	private $trigger_code;
	private $trigger_meta;

	/**
	 * Set up Automator trigger constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'GHTAGREMOVED';
		$this->trigger_meta = 'GHTAG';
		$this->define_trigger();
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {

		global $uncanny_automator;

		$tags = new Tags;

		$tag_options = [];
		foreach ( $tags->get_tags() as $tag ) {
			$tag_options[ $tag->tag_id ] = $tag->tag_name;
		}

		$option = [
			'option_code' => $this->trigger_meta,
			'label'       => __( 'Tags', 'uncanny-automator' ),
			'input_type'  => 'select',
			'required'    => true,
			'options'     => $tag_options,
		];

		$trigger = array(
			'author'              => $uncanny_automator->get_author_name( $this->trigger_code ),
			'support_link'        => $uncanny_automator->get_author_support_link( $this->trigger_code, 'integration/groundhogg/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			'meta'                => $this->trigger_meta,
			/* translators: Logged-in trigger - Groundhogg */
			'sentence'            => sprintf( __( '{{A tag:%1$s}} is removed from a user', 'uncanny-automator-pro' ), $this->trigger_meta ),
			/* translators: Logged-in trigger - Groundhogg */
			'select_option_name'  => __( '{{A tag}} is removed from a user', 'uncanny-automator-pro' ),
			'action'              => 'groundhogg/contact/tag_removed',
			'priority'            => 10,
			'accepted_args'       => 2,
			'validation_function' => array( $this, 'tag_removed' ),
			'options'             => [ $option ],
		);

		$uncanny_automator->register->trigger( $trigger );

		return;
	}

	/**
	 * Validation function when the trigger action is hit
	 *
	 * @param $user_id
	 */
	public function tag_removed( $class, $tag_id ) {

		global $uncanny_automator;

		$user_id = $class->get_user_id();

		if ( ! $user_id ) {
			return;
		}

		// Get all recipes that have the "$this->trigger_code = 'GHTAGADDED'" trigger
		$recipes = $uncanny_automator->get->recipes_from_trigger_code( $this->trigger_code );
		// Get the specific GHTAG meta data from the recipes
		$recipe_trigger_meta_data = $uncanny_automator->get->meta_from_recipes( $recipes, 'GHTAG' );
		$matched_recipe_ids       = [];

		// Loop through recipe
		foreach ( $recipe_trigger_meta_data as $recipe_id => $trigger_meta ) {
			// Loop through recipe GHTAG trigger meta data
			foreach ( $trigger_meta as $trigger_id => $required_tag_id ) {
				if (
					0 === absint( $required_tag_id ) || // Any tag is set as the option
					$tag_id === absint( $required_tag_id ) // Match specific tag
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
					'is_signed_in'     => true,
				];

				$args = $uncanny_automator->maybe_add_trigger_entry( $pass_args, false );

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
