<?php

namespace Uncanny_Automator_Pro;

/**
 *
 */
class Automator_Pro_Load {
	/**
	 * The instance of the class
	 *
	 * @since    3.1.0
	 * @access   public
	 * @var      Object
	 */
	public static $instance = null;

	/**
	 * Creates singleton instance of class
	 *
	 * @return Automator_Pro_Load $instance The Automator_Pro_Load Class
	 * @since 3.1.0
	 *
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * class constructor
	 */
	private function __construct() {

		// Load Utilities
		$this->initialize_utilities();

		// Load Configuration
		$this->initialize_config();

		// Load the plugin files
		$this->boot_plugin();

	}

	/**
	 * Initialize Static singleton class that has shared function and variables that can be used anywhere in WP
	 *
	 * @since 1.0.0
	 */
	private function initialize_utilities() {
		include_once __DIR__ . DIRECTORY_SEPARATOR . 'utilities.php';

		Utilities::get_instance();
	}

	/**
	 * Initialize Static singleton class that configures all constants, utilities variables and handles activation/deactivation
	 *
	 * @since 1.0.0
	 */
	private function initialize_config() {

		do_action( 'uapro_define_constants_after' );

		register_activation_hook( AUTOMATOR_PRO_FILE, array( $this, 'activation' ) );

		register_deactivation_hook( AUTOMATOR_PRO_FILE, array( $this, 'deactivation' ) );

		do_action( 'uapro_config_setup_after' );

		//add_action( 'upgrader_process_complete', array( $this, 'plugin_updated' ), 10, 2 );
	}

	/**
	 * Initialize Static singleton class autoload all the files needed for the plugin to work
	 *
	 * @since 1.0.0
	 */
	private function boot_plugin() {

		include_once __DIR__ . DIRECTORY_SEPARATOR . 'boot.php';

		Boot::get_instance();
		do_action( 'uapro_plugin_loaded' );
	}

	/**
	 * The code that runs during plugin activation.
	 * @since    1.0.0
	 */
	public function activation() {

		do_action( 'uapro_activation_before' );

		do_action( 'uapro_activation_after' );
	}

	/**
	 * The code that runs after plugin update.
	 *
	 * @param \WP_Upgrader $upgrader_object
	 * @param $hook_extra
	 *
	 * @since    2.0.0
	 */
//	public function plugin_updated( \WP_Upgrader $upgrader_object, $hook_extra ) {
//		if ( ( key_exists( 'action', $hook_extra ) && 'update' === $hook_extra['action'] ) && ( key_exists( 'type', $hook_extra ) && 'plugin' === $hook_extra['type'] ) && key_exists( 'plugins', $hook_extra ) && is_array( $hook_extra['plugins'] ) && ! empty( $hook_extra['plugins'] ) ) {
//			// if this action is update plugin.
//			$this_plugin = AUTOMATOR_PRO_FILE;
//			foreach ( $hook_extra['plugins'] as $each_plugin ) {
//				if ( $each_plugin === $this_plugin ) {
//					if ( wp_get_scheduled_event( 'uapro_auto_purge_logs' ) ) {
//						//Remove already scheduled cron
//						wp_clear_scheduled_hook( 'uapro_auto_purge_logs' );
//						$this->schedule_purging_event( true );
//					}
//				}
//			}
//		}
//	}


	/**
	 * The code that runs during plugin deactivation.
	 * @since    1.0.0
	 */
	public function deactivation() {

		do_action( 'uapro_deactivation_before' );

		wp_clear_scheduled_hook( 'uapro_auto_purge_logs' );

		do_action( 'uapro_deactivation_after' );

	}
}
