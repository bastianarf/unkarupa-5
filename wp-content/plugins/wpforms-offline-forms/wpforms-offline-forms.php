<?php
/**
 * Plugin Name:       WPForms Offline Forms
 * Plugin URI:        https://wpforms.com
 * Description:       Offline Forms for WPForms.
 * Requires at least: 4.9
 * Requires PHP:      5.5
 * Author:            WPForms
 * Author URI:        https://wpforms.com
 * Version:           1.2.2
 * Text Domain:       wpforms-offline-forms
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
 * along with WPForms. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin version.
define( 'WPFORMS_OF_VERSION', '1.2.2' );

// Plugin URL.
define( 'WPFORMS_OF_URL', plugin_dir_url( __FILE__ ) );

// Plugin directory.
define( 'WPFORMS_OF_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Load the provider class.
 *
 * @since 1.0.0
 */
function wpforms_offline_forms() {

	// WPForms Pro is required.
	if ( ! wpforms()->pro ) {
		return;
	}

	// Load translated strings.
	load_plugin_textdomain( 'wpforms-offline-forms', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	// Load main addon class.
	require_once WPFORMS_OF_DIR . 'class-offline-forms.php';
}

add_action( 'wpforms_loaded', 'wpforms_offline_forms' );

/**
 * Load the plugin updater.
 *
 * @since 1.0.0
 *
 * @param string $key
 */
function wpforms_offline_forms_updater( $key ) {

	new WPForms_Updater(
		array(
			'plugin_name' => 'WPForms Offline Forms',
			'plugin_slug' => 'wpforms-offline-forms',
			'plugin_path' => plugin_basename( __FILE__ ),
			'plugin_url'  => trailingslashit( WPFORMS_OF_URL ),
			'remote_url'  => WPFORMS_UPDATER_API,
			'version'     => WPFORMS_OF_VERSION,
			'key'         => $key,
		)
	);
}

add_action( 'wpforms_updater', 'wpforms_offline_forms_updater' );
