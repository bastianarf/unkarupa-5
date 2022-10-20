<?php
/**
 * Plugin Name:       WPForms PayPal Standard
 * Plugin URI:        https://wpforms.com
 * Description:       PayPal Standard integration with WPForms.
 * Requires at least: 4.9
 * Requires PHP:      5.5
 * Author:            WPForms
 * Author URI:        https://wpforms.com
 * Version:           1.3.4
 * Text Domain:       wpforms-paypal-standard
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
define( 'WPFORMS_PAYPAL_STANDARD_VERSION', '1.3.4' );

/**
 * Load the payment class.
 *
 * @since 1.0.0
 */
function wpforms_paypal_standard() {

	// WPForms Pro is required.
	if ( ! wpforms()->pro ) {
		return;
	}

	load_plugin_textdomain( 'wpforms-paypal-standard', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	require_once plugin_dir_path( __FILE__ ) . 'class-paypal-standard.php';
}

add_action( 'wpforms_loaded', 'wpforms_paypal_standard' );

/**
 * Load the plugin updater.
 *
 * @since 1.0.0
 *
 * @param string $key
 */
function wpforms_paypal_standard_updater( $key ) {

	new WPForms_Updater(
		array(
			'plugin_name' => 'WPForms PayPal Standard',
			'plugin_slug' => 'wpforms-paypal-standard',
			'plugin_path' => plugin_basename( __FILE__ ),
			'plugin_url'  => trailingslashit( plugin_dir_url( __FILE__ ) ),
			'remote_url'  => WPFORMS_UPDATER_API,
			'version'     => WPFORMS_PAYPAL_STANDARD_VERSION,
			'key'         => $key,
		)
	);
}

add_action( 'wpforms_updater', 'wpforms_paypal_standard_updater' );
