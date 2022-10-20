<?php
/**
 * Plugin Name: WPForms MailChimp
 * Plugin URI:  https://wpforms.com
 * Description: MailChimp integration with WPForms.
 * Author:      WPForms
 * Author URI:  https://wpforms.com
 * Version:     1.4.1
 * Text Domain: wpforms-mailchimp
 * Domain Path: languages
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
};

// Plugin version.
define( 'WPFORMS_MAILCHIMP_VERSION', '1.4.1' );

// Plugin URL.
define( 'WPFORMS_MAILCHIMP_URL', plugin_dir_url( __FILE__ ) );

// Plugin directory.
define( 'WPFORMS_MAILCHIMP_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Load the provider class.
 *
 * @since 1.0.0
 */
function wpforms_mailchimp() {

	// WPForms Pro is required.
	if ( ! wpforms()->pro ) {
		return;
	}

	// Load translated strings.
	load_plugin_textdomain( 'wpforms-mailchimp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	// Get all active integrations.
	$providers = get_option( 'wpforms_providers' );

	// Load v3 API integration.
	require_once WPFORMS_MAILCHIMP_DIR . 'v3/class-mailchimp.php';

	// Load v2 API integration if the user currently has it setup.
	if ( ! empty( $providers['mailchimp'] ) ) {
		require_once WPFORMS_MAILCHIMP_DIR . 'v2/class-mailchimp.php';
	}
}

add_action( 'wpforms_loaded', 'wpforms_mailchimp' );

/**
 * Load the plugin updater.
 *
 * @since 1.0.0
 *
 * @param string $key
 */
function wpforms_mailchimp_updater( $key ) {

	new WPForms_Updater(
		array(
			'plugin_name' => 'WPForms MailChimp',
			'plugin_slug' => 'wpforms-mailchimp',
			'plugin_path' => plugin_basename( __FILE__ ),
			'plugin_url'  => trailingslashit( WPFORMS_MAILCHIMP_URL ),
			'remote_url'  => WPFORMS_UPDATER_API,
			'version'     => WPFORMS_MAILCHIMP_VERSION,
			'key'         => $key,
		)
	);
}
add_action( 'wpforms_updater', 'wpforms_mailchimp_updater' );
