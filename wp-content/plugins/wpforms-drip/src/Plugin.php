<?php

namespace WPFormsDrip;

use WPForms\Providers\Loader as ProvidersLoader;

/**
 * Class Plugin that loads the plugin.
 *
 * @since 1.0.0
 */
class Plugin {

	/**
	 * Get a single instance of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return \WPFormsDrip\Plugin
	 */
	public static function get_instance() {

		static $instance;

		if ( ! $instance ) {
			$instance = new Plugin();
		}

		return $instance;
	}

	/**
	 * Plugin constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->load_translations();

		$providers_loader = ProvidersLoader::get_instance();

		$providers_loader->register(
			Provider\Core::get_instance()
		);
	}

	/**
	 * Load translated strings.
	 *
	 * @since 1.0.0
	 */
	protected function load_translations() {
		load_plugin_textdomain( 'wpforms-drip', false, dirname( plugin_basename( WPFORMS_DRIP_FILE ) ) . '/languages/' );
	}

	/**
	 * Sanitize custom field identifier according to Drip specification.
	 *
	 * @see https://help.drip.com/hc/en-us/articles/115003767652-Custom-Fields
	 *
	 * @since 1.4.0
	 *
	 * @param string $str Custom field id.
	 *
	 * @return string Sanitized custom field id.
	 */
	public function sanitize_field_id( $str ) {

		$str = preg_replace( '/[ +-<>]/', '_', $str );
		$str = preg_replace( '/[^0-9a-z_]/i', '', $str );

		return $str;
	}
}
