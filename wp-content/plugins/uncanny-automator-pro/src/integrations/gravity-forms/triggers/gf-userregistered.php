<?php

namespace Uncanny_Automator_Pro;

/**
 *
 */
class GF_USERREGISTERED {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'GF';

	/**
	 * @var string
	 */
	private $trigger_code;
	/**
	 * @var string
	 */
	private $trigger_meta;

	/**
	 * SetAutomatorTriggers constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'GFUSERCREATED';
		$this->trigger_meta = 'USERCREATED';
		if ( defined( 'GF_USER_REGISTRATION_VERSION' ) ) {
			$this->define_trigger();
		}
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
			'meta'                => $this->trigger_meta,
			/* translators: Logged-in trigger - Gravity Forms */
			'sentence'            => __( 'A user is registered', 'uncanny-automator-pro' ),
			/* translators: Logged-in trigger - Gravity Forms */
			'select_option_name'  => __( 'A user is registered', 'uncanny-automator-pro' ),
			'action'              => 'gform_user_registered',
			'priority'            => 10,
			'accepted_args'       => 4,
			'validation_function' => array( $this, 'save_data' ),
			'options'             => array(),
		);

		$uncanny_automator->register->trigger( $trigger );
	}

	/**
	 * @param $user_id
	 * @param $feed
	 * @param $entry
	 * @param $password
	 */
	public function save_data( $user_id, $feed, $entry, $password ) {

		global $uncanny_automator;

		$args = array(
			'code'           => $this->trigger_code,
			'meta'           => $this->trigger_meta,
			'post_id'        => - 1,
			'ignore_post_id' => true,
			'user_id'        => $user_id,
			'is_signed_in'   => true,
		);

		$uncanny_automator->maybe_add_trigger_entry( $args );
	}
}
