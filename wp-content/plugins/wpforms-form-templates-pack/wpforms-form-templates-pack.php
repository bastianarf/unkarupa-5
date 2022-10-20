<?php
/**
 * Plugin Name:       WPForms Form Templates Pack
 * Plugin URI:        https://wpforms.com
 * Description:       Additional form templates for WPForms.
 * Author:            WPForms
 * Author URI:        https://wpforms.com
 * Version:           1.2.1
 * Requires at least: 4.9
 * Requires PHP:      5.5
 * Text Domain:       wpforms-form-templates-pack
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
define( 'WPFORMS_FORM_TEMPLATES_VERSION', '1.2.1' );

/**
 * Load the provider class.
 *
 * @since 1.0.0
 */
function wpforms_form_templates_pack() {

	// WPForms Pro is required.
	if ( ! wpforms()->pro ) {
		return;
	}

	load_plugin_textdomain( 'wpforms-form-templates-pack', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	$templates = apply_filters(
		'wpforms_form_templates_pack_load',
		array(
			'accident-report',
			'address-book',
			'alumni-donation',
			'auction-item-registration',
			'automobile-information',
			'avon-order',
			'baseball-league-registration',
			'basic-mortgage-application',
			'birthday-party-invitation-rsvp',
			'bug-tracker',
			'call-for-proposals',
			'camp-registration',
			'cancellation-survey',
			'change-request',
			'check-request',
			'church-donation',
			'church-membership',
			'college-application',
			'complaint',
			'conference-registration',
			'content-download',
			'contest-entry',
			'diet-log',
			'dinner-reservation',
			'emergency-contact',
			'employee-incident-report',
			'employee-information',
			'employee-referral',
			'employee-termination',
			'enrollment',
			'equipment-checkout',
			'event-feedback',
			'event-planner',
			'exercise-log',
			'family-reunion-registration',
			'file-upload',
			'fitness-interest',
			'football-league-registration',
			'fundraiser-registration',
			'gaming-tournament-registration',
			'general-donation',
			'gift-card-order',
			'girl-scout-order',
			'grade-book',
			'gym-registration',
			'health-insurance-quote',
			'height-and-weight',
			'high-school-reunion-registration',
			'home-buyer-feedback',
			'house-inventory',
			'it-asset',
			'it-service-request',
			'job-application-upload',
			'lecture-notes',
			'lesson-plan',
			'maintenance-request',
			'meeting-room-registration',
			'movie-collection',
			'music-collection',
			'neighborhood-events-questionnaire',
			'neighborhood-feedback',
			'new-years-party-rsvp',
			'newspaper-subscription',
			'online-event-registration',
			'online-proposal',
			'online-race-registration',
			'parental-consent',
			'partnership-agreement',
			'party-invitation-rsvp',
			'performance-improvement-plan',
			'personal-trainer-signup',
			'pledge-drive',
			'potluck-invitation-rsvp',
			'prayer-request',
			'race-registration',
			'recipe-catalog',
			'rent-receipt',
			'retreat-registration',
			'school-id-registration',
			'simple-rsvp',
			'ski-lodge-registration',
			'sponsorship-request',
			'summer-camp-registration',
			'super-bowl-party-rsvp',
			'support-ticket',
			'takeout-order',
			'testimonial',
			'time-sheet',
			'to-do-list',
			'travel-request',
			'tshirt-order',
			'vendor-contact-information',
			'vendor-registration',
			'veterinarian-patient-intake',
			'video-release',
			'volunteer-recruitment',
			'webinar-registration',
			'wedding-invitation-rsvp',
			'work-order-request',
			'workshop-registration',
		)
	);

	foreach ( $templates as $template ) {
		$path = plugin_dir_path( __FILE__ ) . 'templates/class-' . sanitize_file_name( $template ) . '.php';

		if ( file_exists( $path ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'templates/class-' . sanitize_file_name( $template ) . '.php';
		}
	}
}

add_action( 'wpforms_loaded', 'wpforms_form_templates_pack' );

/**
 * Load the plugin updater.
 *
 * @since 1.0.0
 *
 * @param string $key
 */
function wpforms_form_templates_pack_updater( $key ) {

	new WPForms_Updater(
		array(
			'plugin_name' => 'WPForms Form Templates Pack',
			'plugin_slug' => 'wpforms-form-templates-pack',
			'plugin_path' => plugin_basename( __FILE__ ),
			'plugin_url'  => trailingslashit( plugin_dir_url( __FILE__ ) ),
			'remote_url'  => WPFORMS_UPDATER_API,
			'version'     => WPFORMS_FORM_TEMPLATES_VERSION,
			'key'         => $key,
		)
	);
}

add_action( 'wpforms_updater', 'wpforms_form_templates_pack_updater' );
