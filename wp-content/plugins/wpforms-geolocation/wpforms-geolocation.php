<?php
/**
 * Plugin Name:       WPForms Geolocation
 * Plugin URI:        https://wpforms.com
 * Description:       Display geolocation details with WPForms.
 * Author:            WPForms
 * Author URI:        https://wpforms.com
 * Version:           2.0.0
 * Requires at least: 4.9
 * Requires PHP:      5.6
 * Text Domain:       wpforms-geolocation
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

// Plugin version.
define( 'WPFORMS_GEOLOCATION_VERSION', '2.0.0' );
define( 'WPFORMS_GEOLOCATION_FILE', __FILE__ );
define( 'WPFORMS_GEOLOCATION_PATH', plugin_dir_path( WPFORMS_GEOLOCATION_FILE ) );
define( 'WPFORMS_GEOLOCATION_URL', plugin_dir_url( WPFORMS_GEOLOCATION_FILE ) );

/**
 * Load the main class.
 *
 * @since 2.0.0
 */
function wpforms_geolocation_load() {

	// Load translated strings.
	load_plugin_textdomain( 'wpforms-geolocation', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	// Check requirements.
	if ( ! wpforms_geolocation_required() ) {
		return;
	}

	// Load plugin.
	wpforms_geolocation();
}

add_action( 'wpforms_loaded', 'wpforms_geolocation_load' );

/**
 * Check addon requirements.
 *
 * @since 2.0.0
 */
function wpforms_geolocation_required() {

	if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
		add_action( 'admin_init', 'wpforms_geolocation_deactivation' );
		add_action( 'admin_notices', 'wpforms_geolocation_fail_php_version' );

		return false;
	}

	if ( ! function_exists( 'wpforms' ) || ! wpforms()->pro ) {
		return false;
	}

	if ( version_compare( wpforms()->version, '1.6.3', '<' ) ) {
		add_action( 'admin_init', 'wpforms_geolocation_deactivation' );
		add_action( 'admin_notices', 'wpforms_geolocation_fail_wpforms_version' );

		return false;
	}

	if ( ! function_exists( 'wpforms_get_license_type' ) || ! in_array( wpforms_get_license_type(), [ 'pro', 'elite', 'agency', 'ultimate' ], true ) ) {
		return false;
	}

	return true;
}

/**
 * Deactivate the plugin.
 *
 * @since 2.0.0
 */
function wpforms_geolocation_deactivation() {

	deactivate_plugins( plugin_basename( __FILE__ ) );
}

/**
 * Admin notice for minimum PHP version.
 *
 * @since 2.0.0
 */
function wpforms_geolocation_fail_php_version() {

	echo '<div class="notice notice-error"><p>';
	printf(
		wp_kses( /* translators: %s - WPForms.com documentation page URI. */
			__( 'The WPForms Geolocation plugin has been deactivated. Your site is running an outdated version of PHP that is no longer supported and is not compatible with the Geolocation plugin. <a href="%s" target="_blank" rel="noopener noreferrer">Read more</a> for additional information.', 'wpforms-geolocation' ),
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
 * @since 2.0.0
 */
function wpforms_geolocation_fail_wpforms_version() {

	echo '<div class="notice notice-error"><p>';
	esc_html_e( 'The WPForms Geolocation plugin has been deactivated, because it requires WPForms v1.6.3 or later to work.', 'wpforms-geolocation' );
	echo '</p></div>';

	// phpcs:disable
	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}
	// phpcs:enable
}

/**
 * Get the instance of the `\WPFormsGeolocation\Plugin` class.
 * This function is useful for quickly grabbing data used throughout the plugin.
 *
 * @since 1.0.0
 *
 * @return \WPFormsGeolocation\Plugin
 */
function wpforms_geolocation() {

	// Actually, load the Geolocation addon now.
	$loader = require_once WPFORMS_GEOLOCATION_PATH . 'vendor/autoload.php';

	// Load deprecated classes.
	$loader->addClassMap(
		[
			'WPForms_Geolocation' => WPFORMS_GEOLOCATION_PATH . 'class-geolocation.php',
		]
	);

	return \WPFormsGeolocation\Plugin::get_instance();
}

/**
 * Load the plugin updater.
 *
 * @since 1.0.0
 *
 * @param string $key License key.
 */
function wpforms_geolocation_updater( $key ) {

	new WPForms_Updater(
		[
			'plugin_name' => 'WPForms Geolocation',
			'plugin_slug' => 'wpforms-geolocation',
			'plugin_path' => plugin_basename( WPFORMS_GEOLOCATION_FILE ),
			'plugin_url'  => trailingslashit( WPFORMS_GEOLOCATION_URL ),
			'remote_url'  => WPFORMS_UPDATER_API,
			'version'     => WPFORMS_GEOLOCATION_VERSION,
			'key'         => $key,
		]
	);
}

add_action( 'wpforms_updater', 'wpforms_geolocation_updater' );
