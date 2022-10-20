<?php
/**
 * Plugin Name:       WPForms Drip
 * Plugin URI:        https://wpforms.com
 * Description:       Drip integration with WPForms.
 * Requires at least: 4.9
 * Requires PHP:      5.5
 * Author:            WPForms
 * Author URI:        https://wpforms.com
 * Version:           1.4.2
 * Text Domain:       wpforms-drip
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

/**
 * Check addon requirements.
 * We do it on `plugins_loaded` hook. If earlier - core constants still not defined.
 *
 * @since 1.0.0
 */
function wpforms_drip_required() {

	if ( version_compare( PHP_VERSION, '5.5', '<' ) ) {
		add_action( 'admin_init', 'wpforms_drip_deactivate' );
		add_action( 'admin_notices', 'wpforms_drip_deactivate_msg' );

	} elseif ( version_compare( WPFORMS_VERSION, '1.5.9', '<' ) ) {
		add_action( 'admin_init', 'wpforms_drip_deactivate' );
		add_action( 'admin_notices', 'wpforms_drip_fail_wpforms_version' );

	} else {

		// Actually, load the Drip addon now, as we met all the requirements.
		require_once __DIR__ . '/autoloader.php';
	}
}

add_action( 'plugins_loaded', 'wpforms_drip_required' );

/**
 * Deactivate plugin.
 *
 * @since 1.0.0
 */
function wpforms_drip_deactivate() {

	deactivate_plugins( plugin_basename( __FILE__ ) );
}

/**
 * Admin notice for minimum PHP version.
 *
 * @since 1.0.0
 */
function wpforms_drip_deactivate_msg() {

	echo '<div class="notice notice-error"><p>';
	printf(
		wp_kses(
			/* translators: %s - WPForms.com documentation page URI. */
			__( 'The WPForms Drip plugin has been deactivated. Your site is running an outdated version of PHP that is no longer supported and is not compatible with the Drip addon. <a href="%s" target="_blank" rel="noopener noreferrer">Read more</a> for additional information.', 'wpforms-drip' ),
			array(
				'a' => array(
					'href'   => array(),
					'rel'    => array(),
					'target' => array(),
				),
			)
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
 * @since 1.0.0
 */
function wpforms_drip_fail_wpforms_version() {

	echo '<div class="notice notice-error"><p>';
	esc_html_e( 'The WPForms Drip plugin has been deactivated, because it requires WPForms v1.5.9 or later to work.', 'wpforms-drip' );
	echo '</p></div>';

	// phpcs:disable
	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}
	// phpcs:enable
}

/**
 * Plugin constants.
 */
define( 'WPFORMS_DRIP_VERSION', '1.4.2' );
define( 'WPFORMS_DRIP_FILE', __FILE__ );
define( 'WPFORMS_DRIP_URL', plugin_dir_url( __FILE__ ) );
