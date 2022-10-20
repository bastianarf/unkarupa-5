<?php
/**
 * Load the class only when it's requested.
 * Inspired by the official example implementations of PSR-4:
 * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md
 *
 * @since 1.0.0
 *
 * @param string $class Class name to load.
 */
spl_autoload_register( function( $class ) {

	// Out base namespace for all plugin classes.
	$prefix = 'WPFormsDrip\\';

	// Does the class use the namespace prefix?
	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		// No, move to the next registered autoloader.
		return;
	}

	// Base directory for the namespace prefix.
	$base_dir = __DIR__ . '/src/';

	// Get the relative class name.
	$relative_class = substr( $class, $len );

	// Replace the namespace prefix with the base directory.
	// Replace namespace separators with directory separators in the relative
	// class name. Append with .php.
	$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

	// Finally, require the file.
	if ( file_exists( $file ) ) {
		require_once $file;
	}
} );

/**
 * Get the instance of the Drip main class, which actually loads
 * all the plugin code.
 *
 * @since 1.0.0
 *
 * @return \WPFormsDrip\Plugin
 */
function wpforms_drip() {

	// WPForms Pro is required.
	if ( ! wpforms()->pro ) {
		return;
	}

	return \WPFormsDrip\Plugin::get_instance();
}
add_action( 'wpforms_loaded', 'wpforms_drip' );

/**
 * Load the plugin updater.
 *
 * @since 1.0.0
 *
 * @param string $key Unique addon key.
 */
function wpforms_drip_updater( $key ) {

	new WPForms_Updater(
		array(
			'plugin_name' => 'WPForms Drip',
			'plugin_slug' => 'wpforms-drip',
			'plugin_path' => plugin_basename( WPFORMS_DRIP_FILE ),
			'plugin_url'  => trailingslashit( WPFORMS_DRIP_URL ),
			'remote_url'  => WPFORMS_UPDATER_API,
			'version'     => WPFORMS_DRIP_VERSION,
			'key'         => $key,
		)
	);
}
add_action( 'wpforms_updater', 'wpforms_drip_updater' );
