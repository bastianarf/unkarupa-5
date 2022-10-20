<?php

namespace Uncanny_Automator_Pro;

/**
 * Class Add_Twilio_Integration
 * @package Uncanny_Automator_Pro
 */
class Add_Twilio_Integration {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'TWILIO';

	/**
	 * Add_Integration constructor.
	 */
	public function __construct() {

		// Add directories to auto loader
		// add_filter( 'uncanny_automator_pro_integration_directory', [ $this, 'add_integration_directory_func' ], 11 );

		// Add code, name and icon set to automator
		// $this->add_integration_func();

		// Verify is the plugin is active based on integration code
//		add_filter( 'uncanny_automator_maybe_add_integration', [
//			$this,
//			'plugin_active',
//		], 30, 2 );
	}

	/**
	 * Only load this integration and its triggers and actions if the related plugin is active
	 *
	 * @param $status
	 * @param $code
	 *
	 * @return bool
	 */
	public function plugin_active( $status, $code ) {
		return true;
	}

	/**
	 * Set the directories that the auto loader will run in
	 *
	 * @param $directory
	 *
	 * @return array
	 */
	public function add_integration_directory_func( $directory ) {

		//require dirname( __FILE__ ) . '/vendor/autoload.php';
		// Use the REST API Client to make requests to the Twilio REST API
		$directory[] = dirname( __FILE__ ) . '/helpers';
		$gtw_options = get_option( '_uncannyowl_twilio_settings', [] );
		if ( isset( $gtw_options['auth_token'] ) && ! empty( $gtw_options['auth_token'] ) ) {
			// just for now...
			$directory[] = dirname( __FILE__ ) . '/actions';
			$directory[] = dirname( __FILE__ ) . '/triggers';
			$directory[] = dirname( __FILE__ ) . '/tokens';
		}

		return $directory;
	}

	/**
	 * Register the integration by pushing it into the global automator object
	 */
	public function add_integration_func() {

		global $uncanny_automator;

		$uncanny_automator->register->integration( self::$integration, [
			'name'     => 'Twilio',
			'icon_svg' => Utilities::get_integration_icon( 'twilio-icon.svg' ),
		] );

	}
}
