<?php

namespace WPFormsLocker;

/**
 * WPForms Form Locker loader class.
 *
 * @since 1.0.0
 */
final class Loader {

	/**
	 * Have the only available instance of the class.
	 *
	 * @var Loader
	 *
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * URL to a plugin directory. Used for assets.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	public $url = '';

	/**
	 * Initiate main plugin instance.
	 *
	 * @since 1.0.0
	 *
	 * @return Loader
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) || ! ( self::$instance instanceof self ) ) {
			self::$instance = new Loader();
		}

		return self::$instance;
	}

	/**
	 * Loader constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->url = \plugin_dir_url( __DIR__ );

		\add_action( 'wpforms_loaded', array( $this, 'init' ) );
	}

	/**
	 * All the actual plugin loading is done here.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// WPForms Pro is required.
		if ( ! \wpforms()->pro ) {
			return;
		}

		// Load translated strings.
		\load_plugin_textdomain( 'wpforms-form-locker', false, \dirname( \plugin_basename( WPFORMS_FORM_LOCKER_FILE ) ) . '/languages/' );

		new Lockers\Password();
		new Lockers\Schedule();
		new Lockers\EntryLimit();
		new Lockers\User();
		new Lockers\UniqueAnswer();

		if ( \wpforms_is_admin_page( 'builder' ) || Lockers\UniqueAnswer::is_unique_answer_enabled_new_field_ajax() ) {
			new Admin();
		}

		// Register the updater of this plugin.
		$this->updater();
	}

	/**
	 * Load the plugin updater.
	 *
	 * @since 1.0.0
	 */
	private function updater() {

		$url = $this->url;

		\add_action( 'wpforms_updater', function( $key ) use ( $url ) {

			new \WPForms_Updater(
				array(
					'plugin_name' => 'WPForms Form Locker',
					'plugin_slug' => 'wpforms-form-locker',
					'plugin_path' => \plugin_basename( \WPFORMS_FORM_LOCKER_FILE ),
					'plugin_url'  => \trailingslashit( $url ),
					'remote_url'  => \WPFORMS_UPDATER_API,
					'version'     => \WPFORMS_FORM_LOCKER_VERSION,
					'key'         => $key,
				)
			);
		} );
	}
}
