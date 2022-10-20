<?php

namespace Uncanny_Automator_Pro;

/**
 * Class ANON_WPF_SUBMITFIELD
 * @package Uncanny_Automator_Pro
 */
class ANON_WPF_SUBMITFIELD {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'WPF';

	private $trigger_code;
	private $trigger_meta;

	/**
	 * Set up Automator trigger constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'ANONWPFSUBMITFIELD';
		$this->trigger_meta = 'ANONWPFFORMS';
		$this->define_trigger();

	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {

		global $uncanny_automator;

		$trigger = array(
			'author'              => $uncanny_automator->get_author_name(),
			'support_link'        => $uncanny_automator->get_author_support_link( $this->trigger_code, 'integration/wp-forms/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			'meta'                => $this->trigger_meta,
			'sentence'            => sprintf(
			/* translators: Anonymous trigger - WP Forms */
				__( '{{A form:%1$s}} is submitted with {{a specific value:%2$s}} in {{a specific field:%3$s}}', 'uncanny-automator-pro' ),
				$this->trigger_meta,
				'SUBVALUE' . ':' . $this->trigger_meta,
				$this->trigger_code . ':' . $this->trigger_meta
			),
			/* translators: Anonymous trigger - WP Forms */
			'select_option_name'  => __( '{{A form}} is submitted with {{a specific value}} in {{a specific field}}', 'uncanny-automator-pro' ),
			'action'              => 'wpforms_process_complete',
			'type'                => 'anonymous',
			'priority'            => 10,
			'accepted_args'       => 4,
			'validation_function' => array( $this, 'wpform_submit' ),
			'options'             => [],
			'options_group'       => [
				$this->trigger_meta => [
					$uncanny_automator->helpers->recipe->wpforms->options->list_wp_forms( null, $this->trigger_meta, [
						'token'        => false,
						'is_ajax'      => true,
						'target_field' => $this->trigger_code,
						'endpoint'     => 'select_form_fields_ANONWPFFORMS',
					] ),
					$uncanny_automator->helpers->recipe->field->select_field( $this->trigger_code, __( 'Field', 'uncanny-automator-pro' ) ),
					$uncanny_automator->helpers->recipe->field->text_field( 'SUBVALUE', __( 'Value', 'uncanny-automator' ) ),
				],
			],
		);

		$uncanny_automator->register->trigger( $trigger );

		return;
	}

	/**
	 * Validation function when the trigger action is hit
	 *
	 * @param array $fields fields array.
	 * @param array $entry errors array.
	 * @param array $form_data form object.
	 * @param string $entry_id other settings.
	 */
	public function wpform_submit( $fields, $entry, $form_data, $entry_id ) {

		global $uncanny_automator;
		$recipes          = $uncanny_automator->get->recipes_from_trigger_code( $this->trigger_code );
		$entry['form_id'] = $form_data['id'];
		$entry['fields']  = $fields;

		if ( empty( $entry ) ) {
			return;
		}

		$conditions = $uncanny_automator->helpers->recipe->wpforms->pro->match_condition( $entry, $recipes, $this->trigger_meta, $this->trigger_code, 'SUBVALUE' );

		if ( ! $conditions ) {
			return;
		}

		$user_id  = get_current_user_id();
		$triggers = [];
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

					$triggers[] = $uncanny_automator->maybe_add_trigger_entry( $args, false );
				}
			}
		}

		if ( ! empty( $triggers ) ) {
			foreach ( $triggers as $args ) {
				if ( $args ) {
					do_action( 'automator_save_anon_wp_form', $fields, $form_data, $recipes, $args );
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
