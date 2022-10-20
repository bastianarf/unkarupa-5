<?php
/**
 * Plugin Name:       WPForms Form Abandonment
 * Plugin URI:        https://wpforms.com
 * Description:       Form abandonment lead capture with WPForms.
 * Requires at least: 4.9
 * Requires PHP:      5.5
 * Author:            WPForms
 * Author URI:        https://wpforms.com
 * Version:           1.4.4
 * Text Domain:       wpforms-form-abandonment
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
define( 'WPFORMS_FORM_ABANDONMENT_VERSION', '1.4.4' );

/**
 * Load the main class.
 *
 * @since 1.0.0
 */
function wpforms_form_abandonment() {

	// WPForms Pro is required.
	if ( ! wpforms()->pro ) {
		return;
	}

	load_plugin_textdomain( 'wpforms-form-abandonment', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	require_once plugin_dir_path( __FILE__ ) . 'class-form-abandonment.php';
}

add_action( 'wpforms_loaded', 'wpforms_form_abandonment' );

/**
 * Load the plugin updater.
 *
 * @since 1.0.0
 *
 * @param string $key WPForms license key.
 */
function wpforms_form_abandonment_updater( $key ) {

	new WPForms_Updater(
		array(
			'plugin_name' => 'WPForms Form Abandonment',
			'plugin_slug' => 'wpforms-form-abandonment',
			'plugin_path' => plugin_basename( __FILE__ ),
			'plugin_url'  => trailingslashit( plugin_dir_url( __FILE__ ) ),
			'remote_url'  => WPFORMS_UPDATER_API,
			'version'     => WPFORMS_FORM_ABANDONMENT_VERSION,
			'key'         => $key,
		)
	);
}
add_action( 'wpforms_updater', 'wpforms_form_abandonment_updater' );
