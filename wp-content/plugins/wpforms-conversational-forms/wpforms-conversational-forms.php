<?php
/**
 * Plugin Name:       WPForms Conversational Forms
 * Plugin URI:        https://wpforms.com
 * Description:       Create Conversational Forms with WPForms.
 * Requires at least: 4.9
 * Requires PHP:      5.5
 * Author:            WPForms
 * Author URI:        https://wpforms.com
 * Version:           1.5.0
 * Text Domain:       wpforms-conversational-forms
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

/**
 * Require PHP 5.5+.
 */
if ( version_compare( PHP_VERSION, '5.5', '<' ) ) {

	/**
	 * Deactivate plugin.
	 *
	 * @since 1.0.0
	 */
	function wpforms_conversational_forms_deactivate() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}
	add_action( 'admin_init', 'wpforms_conversational_forms_deactivate' );

	/**
	 * Display notice after deactivation.
	 *
	 * @since 1.0.0
	 */
	function wpforms_conversational_forms_deactivate_msg() {

		echo '<div class="notice notice-error"><p>';
		printf(
			wp_kses(
				/* translators: %s - WPForms.com documentation page URL. */
				__( 'The WPForms Conversational Forms plugin has been deactivated. Your site is running an outdated version of PHP that is no longer supported and is not compatible with the Conversational Forms addon. <a href="%s" target="_blank" rel="noopener noreferrer">Read more</a> for additional information.', 'wpforms-conversational-forms' ),
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

		if ( isset( $_GET['activate'] ) ) { // phpcs:ignore
			unset( $_GET['activate'] ); // phpcs:ignore
		}
	}
	add_action( 'admin_notices', 'wpforms_conversational_forms_deactivate_msg' );

	return;
}

// Plugin constants.
define( 'WPFORMS_CONVERSATIONAL_FORMS_VERSION', '1.5.0' );
define( 'WPFORMS_CONVERSATIONAL_FORMS_FILE', __FILE__ );

require_once __DIR__ . '/autoloader.php';
