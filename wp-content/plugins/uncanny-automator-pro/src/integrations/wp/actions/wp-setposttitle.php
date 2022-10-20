<?php

/**
 * Class WP_SETPOSTTITLE
 *
 * @package Uncanny_Automator_Pro
 */

namespace Uncanny_Automator_Pro;

class WP_SETPOSTTITLE {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'WP';

	/**
	 * Action code
	 *
	 * @var string
	 */
	private $action_code;

	/**
	 * Action meta
	 *
	 * @var string
	 */
	private $action_meta;

	/**
	 * Set up Automator action constructor.
	 */
	public function __construct() {
		$this->action_code = 'SETPOSTTITLE';
		$this->action_meta = 'WPPOST';
		global $uncanny_automator;
		if ( $uncanny_automator->helpers->recipe->is_edit_page() ) {
			add_action( 'wp_loaded', array( $this, 'plugins_loaded' ), 99 );
		} else {
			$this->define_action();
		}
	}

	/**
	 * Plugins loaded
	 *
	 * @return void
	 */
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
			'sentence'           => sprintf( __( 'Update the title of {{a post:%1$s}}', 'uncanny-automator-pro' ), $this->action_meta ),
			/* translators: Action - WordPress Core */
			'select_option_name' => __( 'Update the title of {{a post}}', 'uncanny-automator-pro' ),
			'priority'           => 11,
			'accepted_args'      => 4,
			'execution_function' => array( $this, 'set_post_title' ),
			'options_group'      => array(
				$this->action_meta => array(
					$uncanny_automator->helpers->recipe->wp->options->pro->all_wp_post_types(
						__( 'Post type', 'uncanny-automator-pro' ),
						'WPPOSTTYPE',
						array(
							'token'        => false,
							'is_ajax'      => true,
							'target_field' => $this->action_meta,
							'is_any'       => false,
							'endpoint'     => 'select_all_post_of_selected_post_type',
						)
					),
					$uncanny_automator->helpers->recipe->field->select_field( $this->action_meta, __( 'Post', 'uncanny-automator-pro' ) ),
					$uncanny_automator->helpers->recipe->field->text_field( 'WPPOSTTITLE', __( 'Title', 'uncanny-automator' ), true, 'text', '', true ),
				),

			),
		);

		$uncanny_automator->register->action( $action );
	}

	/**
	 * Set Post Title
	 *
	 * @param int $user_id
	 * @param array $action_data
	 * @param int $recipe_id
	 * @param array $args
	 */
	public function set_post_title( $user_id, $action_data, $recipe_id, $args ) {

		global $uncanny_automator;

		$data = array(
			'ID'         => $action_data['meta'][ $this->action_meta ],
			'post_type'  => sanitize_text_field( $action_data['meta']['WPPOSTTYPE'] ),
			'post_title' => sanitize_text_field( $uncanny_automator->parse->text( $action_data['meta']['WPPOSTTITLE'], $recipe_id, $user_id, $args ) ),
		);

		$post_id = wp_update_post( $data, true );

		if ( is_wp_error( $post_id ) ) {

			$errors = $post_id->get_error_messages();

			$message = '';

			foreach ( $errors as $error ) {
				$message .= $error . "\n";
			}

			$action_data['do-nothing']           = true;
			$action_data['complete_with_errors'] = true;

			$uncanny_automator->complete_action( $user_id, $action_data, $recipe_id, $message );

			return;
		}

		$uncanny_automator->complete_action( $user_id, $action_data, $recipe_id );

		return;
	}

}