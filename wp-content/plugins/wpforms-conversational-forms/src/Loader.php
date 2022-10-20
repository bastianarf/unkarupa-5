<?php

namespace WPFormsConversationalForms;

/**
 * Conversational Forms loader class.
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
	 * Admin\Builder Instance if instantiated.
	 *
	 * @var Admin\Builder
	 *
	 * @since {VERSION}
	 */
	public $admin_builder = null;

	/**
	 * Admin\Overview Instance if instantiated.
	 *
	 * @var Admin\Overview
	 *
	 * @since {VERSION}
	 */
	public $admin_overview = null;

	/**
	 * Admin\Ajax Instance if instantiated.
	 *
	 * @var Admin\Ajax
	 *
	 * @since {VERSION}
	 */
	public $admin_ajax = null;

	/**
	 * Frontend Instance if instantiated.
	 *
	 * @var Frontend
	 *
	 * @since {VERSION}
	 */
	public $frontend = null;

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
		\load_plugin_textdomain( 'wpforms-conversational-forms', false, \dirname( \plugin_basename( WPFORMS_CONVERSATIONAL_FORMS_FILE ) ) . '/languages/' );

		if ( \wpforms_is_admin_page( 'builder' ) ) {
			$this->admin_builder = new Admin\Builder();
		}

		if ( \wpforms_is_admin_page( 'overview' ) ) {
			$this->admin_overview = new Admin\Overview();
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$this->admin_ajax = new Admin\Ajax();
		}

		if ( ! \is_admin() ) {
			$this->frontend = new Frontend();
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

		\add_action(
			'wpforms_updater',
			function ( $key ) {
				new \WPForms_Updater(
					array(
						'plugin_name' => 'WPForms Conversational Forms',
						'plugin_slug' => 'wpforms-conversational-forms',
						'plugin_path' => \plugin_basename( \WPFORMS_CONVERSATIONAL_FORMS_FILE ),
						'plugin_url'  => \trailingslashit( $this->url ),
						'remote_url'  => \WPFORMS_UPDATER_API,
						'version'     => \WPFORMS_CONVERSATIONAL_FORMS_VERSION,
						'key'         => $key,
					)
				);
			}
		);
	}
}
