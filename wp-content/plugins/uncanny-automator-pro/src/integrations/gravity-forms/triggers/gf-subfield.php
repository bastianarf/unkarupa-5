<?php

namespace Uncanny_Automator_Pro;

use GFAPI;

/**
 * Class GF_MATCH_FIELD_VALUE
 * @package Uncanny_Automator_Pro
 */
class GF_SUBFIELD {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'GF';

	private $trigger_code;
	private $trigger_meta;

	/**
	 * SetAutomatorTriggers constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'SUBFIELD';
		$this->trigger_meta = 'GFFORMS';
		$this->define_trigger();

	}

	/**
	 *
	 */
	public function define_trigger() {

		global $uncanny_automator;

		$trigger = array(
			'author'              => $uncanny_automator->get_author_name( $this->trigger_code ),
			'support_link'        => $uncanny_automator->get_author_support_link( $this->trigger_code, 'integration/gravity-forms/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			'sentence'            => sprintf(
			/* translators: Logged-in trigger - Gravity Forms */
				__( 'A user submits {{a form:%1$s}} with {{a specific value:%2$s}} in {{a specific field:%3$s}}', 'uncanny-automator-pro' ),
				$this->trigger_meta,
				'SUBVALUE' . ':' . $this->trigger_meta,
				$this->trigger_code . ':' . $this->trigger_meta
			),
			/* translators: Logged-in trigger - Gravity Forms */
			'select_option_name'  => __( 'A user submits {{a form}} with {{a specific value}} in {{a specific field}}', 'uncanny-automator-pro' ),
			'action'              => 'gform_after_submission',
			'priority'            => 20,
			'accepted_args'       => 2,
			'validation_function' => array( $this, 'gform_submit' ),
			'options'             => [],
			'options_group'       => [
				$this->trigger_meta => [
					$uncanny_automator->helpers->recipe->gravity_forms->options->list_gravity_forms( null, $this->trigger_meta, [
						'token'        => false,
						'is_ajax'      => true,
						'target_field' => $this->trigger_code,
						'endpoint'     => 'select_form_fields_GFFORMS',
					] ),
					$uncanny_automator->helpers->recipe->field->select_field( $this->trigger_code, __( 'Field', 'uncanny-automator-pro' ) ),
					$uncanny_automator->helpers->recipe->field->text_field( 'SUBVALUE' ),
				],
			],
		);

		$uncanny_automator->register->trigger( $trigger );

		return;
	}

	/**
	 * @param $entry
	 * @param $form
	 */
	public function gform_submit( $entry, $form ) {
		global $uncanny_automator;
		$recipes = $uncanny_automator->get->recipes_from_trigger_code( $this->trigger_code );

		if ( empty( $entry ) ) {
			return;
		}

		$conditions = $this->match_condition( $entry, $recipes, $this->trigger_meta, $this->trigger_code, 'SUBVALUE', $form );

		if ( ! $conditions ) {
			return;
		}

		$user_id = get_current_user_id();
		if ( ! empty( $conditions ) ) {
			foreach ( $conditions['recipe_ids'] as $trigger_id => $recipe_id ) {
				if ( ! $uncanny_automator->is_recipe_completed( $recipe_id, $user_id ) ) {
					$args = [
						'code'             => $this->trigger_code,
						'meta'             => $this->trigger_meta,
						'recipe_to_match'  => $recipe_id,
						'trigger_to_match' => $trigger_id,
						'ignore_post_id'   => true,
						'user_id'          => $user_id,
					];

					$uncanny_automator->maybe_add_trigger_entry( $args );
				}
			}
		}
	}


	/**
	 *
	 *
	 *
	 * @param $entry
	 * @param null $recipes
	 * @param null $trigger_meta
	 * @param null $trigger_code
	 * @param null $trigger_second_code
	 * @param null $form
	 *
	 * @return array|bool
	 */
	public function match_condition( $entry, $recipes = null, $trigger_meta = null, $trigger_code = null, $trigger_second_code = null, $form = null ) {
		if ( null === $recipes ) {
			return false;
		}

		$matches        = [];
		$recipe_ids     = [];
		$entry_to_match = $entry['form_id'];
		foreach ( $recipes as $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				if ( key_exists( $trigger_meta, $trigger['meta'] ) && $trigger['meta'][ $trigger_meta ] === $entry_to_match ) {
					$matches[ $trigger['ID'] ]    = [
						'field' => $trigger['meta'][ $trigger_code ],
						'value' => $trigger['meta'][ $trigger_second_code ],
					];
					$recipe_ids[ $trigger['ID'] ] = $recipe['ID'];
				}
			}
		}

		if ( ! empty( $matches ) ) {
			foreach ( $matches as $trigger_id => $match ) {
				// check form field type
				// Passing the form object with the field id.
				$gf_field = GFAPI::get_field( $form, $match['field'] );
				if ( 'multiselect' === $gf_field->type ) {
					// convert string to array.
					$user_submission = json_decode( $entry[ $match['field'] ], true );
					$trigger_match   = explode( ',', $match['value'] );
					if ( count( $trigger_match ) !== count( $user_submission ) ) {
						unset( $recipe_ids[ $trigger_id ] );
					} elseif ( ! empty( array_diff( $trigger_match, $user_submission ) ) ) {
						unset( $recipe_ids[ $trigger_id ] );
					}
				} else {
					if ( $entry[ $match['field'] ] !== $match['value'] ) {
						unset( $recipe_ids[ $trigger_id ] );
					}
				}
			}
		}

		if ( ! empty( $recipe_ids ) ) {
			return [ 'recipe_ids' => $recipe_ids, 'result' => true ];
		}

		return false;
	}
}
