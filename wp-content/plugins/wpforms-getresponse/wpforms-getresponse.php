<?php
/**
 * Plugin Name:       WPForms GetResponse
 * Plugin URI:        https://wpforms.com
 * Description:       GetResponse integration with WPForms.
 * Author:            WPForms
 * Author URI:        https://wpforms.com
 * Version:           1.3.0
 * Requires at least: 4.9
 * Requires PHP:      5.5
 * Text Domain:       wpforms-getresponse
 * Domain Path:       languages
 *
 * WPForms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WPForms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WPForms. If not, see <https://www.gnu.org/licenses/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin constants.
define( 'WPFORMS_GETRESPONSE_VERSION', '1.3.0' );
define( 'WPFORMS_GETRESPONSE_FILE', __FILE__ );
define( 'WPFORMS_GETRESPONSE_PATH', plugin_dir_path( WPFORMS_GETRESPONSE_FILE ) );
define( 'WPFORMS_GETRESPONSE_URL', plugin_dir_url( WPFORMS_GETRESPONSE_FILE ) );

/**
 * Load the provider class.
 *
 * @since 1.0.0
 * @since 1.3.0 Update API version.
 */
function wpforms_getresponse() {

	// Load translated strings.
	load_plugin_textdomain( 'wpforms-getresponse', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	// Check requirements.
	if ( ! wpforms_getresponse_required() ) {
		return;
	}

	// Load plugin.
	wpforms_getresponse_plugin();

	// Get all active integrations.
	$providers = wpforms_get_providers_options();

	// Load v2 API integration if the user currently has it setup.
	if ( ! empty( $providers['getresponse'] ) ) {
		require_once WPFORMS_GETRESPONSE_PATH . 'deprecated/class-getresponse.php';
	}
}

add_action( 'plugins_loaded', 'wpforms_getresponse' );

/**
 * Check addon requirements.
 *
 * @since 1.3.0
 */
function wpforms_getresponse_required() {

	if ( version_compare( PHP_VERSION, '5.5', '<' ) ) {
		add_action( 'admin_init', 'wpforms_getresponse_deactivation' );
		add_action( 'admin_notices', 'wpforms_getresponse_fail_php_version' );

		return false;

	} elseif (
		! function_exists( 'wpforms' ) ||
		! wpforms()->pro ||
		version_compare( wpforms()->version, '1.6.3', '<' )
	) {
		add_action( 'admin_init', 'wpforms_getresponse_deactivation' );
		add_action( 'admin_notices', 'wpforms_getresponse_fail_wpforms_version' );

		return false;
	}

	return true;
}

/**
 * Deactivate the plugin.
 *
 * @since 1.3.0
 */
function wpforms_getresponse_deactivation() {

	deactivate_plugins( plugin_basename( __FILE__ ) );
}

/**
 * Admin notice for minimum PHP version.
 *
 * @since 1.3.0
 */
function wpforms_getresponse_fail_php_version() {

	echo '<div class="notice notice-error"><p>';
	printf(
		wp_kses(
			/* translators: %s - WPForms.com documentation page URI. */
			__( 'The WPForms GetResponse plugin has been deactivated. Your site is running an outdated version of PHP that is no longer supported and is not compatible with the GetResponse plugin. <a href="%s" target="_blank" rel="noopener noreferrer">Read more</a> for additional information.', 'wpforms-getresponse' ),
			[
				'a' => [
					'href'   => [],
					'rel'    => [],
					'target' => [],
				],
			]
		),
		'https://wpforms.com/docs/supported-php-version/'
	);
	echo '</p></div>';

	// phpcs:disable
	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}
	// phpcs:enable
}

/**
 * Admin notice for minimum WPForms version.
 *
 * @since 1.3.0
 */
function wpforms_getresponse_fail_wpforms_version() {

	echo '<div class="notice notice-error"><p>';
	esc_html_e( 'The WPForms GetResponse plugin has been deactivated, because it requires WPForms v1.6.3 or later to work.', 'wpforms-getresponse' );
	echo '</p></div>';

	// phpcs:disable
	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}
	// phpcs:enable
}

/**
 * Get the instance of the `\WPFormsGetResponse\Plugin` class.
 * This function is useful for quickly grabbing data used throughout the plugin.
 *
 * @since 1.3.0
 *
 * @return \WPFormsGetResponse\Plugin
 */
function wpforms_getresponse_plugin() {

	// Actually, load the GetResponse addon now, as we met all the requirements.
	require_once __DIR__ . '/vendor/autoload.php';

	return \WPFormsGetResponse\Plugin::get_instance();
}

/**
 * Load the plugin updater.
 *
 * @since 1.0.0
 *
 * @param string $key License key.
 */
function wpforms_getresponse_updater( $key ) {

	new WPForms_Updater(
		[
			'plugin_name' => 'WPForms GetResponse',
			'plugin_slug' => 'wpforms-getresponse',
			'plugin_path' => plugin_basename( __FILE__ ),
			'plugin_url'  => trailingslashit( plugin_dir_url( __FILE__ ) ),
			'remote_url'  => WPFORMS_UPDATER_API,
			'version'     => WPFORMS_GETRESPONSE_VERSION,
			'key'         => $key,
		]
	);
}
add_action( 'wpforms_updater', 'wpforms_getresponse_updater' );
