<?php

namespace WPFormsGeolocation\PlacesProviders;

use WPFormsGeolocation\Admin\Settings\Settings;
use WPFormsGeolocation\Admin\Settings\GooglePlaces as SettingsGooglePlaces;

/**
 * Class GooglePlaces.
 *
 * @since 2.0.0
 */
class GooglePlaces implements IPlacesProvider {

	/**
	 * Init Google Places provider.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		if ( ! $this->is_active() ) {
			return;
		}

		add_action( 'wpforms_frontend_css', [ $this, 'enqueue_styles' ] );
		add_action( 'wpforms_frontend_js', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Enqueue styles.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_styles() {

		$min = wpforms_get_min_suffix();

		wp_enqueue_style(
			'wpforms-geolocation-google-places',
			WPFORMS_GEOLOCATION_URL . "assets/css/wpforms-geolocation-google{$min}.css",
			[],
			WPFORMS_GEOLOCATION_VERSION
		);
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_scripts() {

		$min = wpforms_get_min_suffix();

		wp_enqueue_script(
			'wpforms-geolocation-google-places',
			WPFORMS_GEOLOCATION_URL . "assets/js/wpforms-geolocation-google-api{$min}.js",
			[],
			WPFORMS_GEOLOCATION_VERSION
		);

		wp_enqueue_script(
			'google-geolocation-api',
			add_query_arg(
				[
					'key'       => wpforms_setting( Settings::SLUG . '-' . SettingsGooglePlaces::SLUG . '-api-key' ),
					'libraries' => 'places',
					'callback'  => 'WPFormsGeolocationInitGooglePlacesAPI',
				],
				'https://maps.googleapis.com/maps/api/js'
			),
			[ 'wpforms-geolocation-google-places' ],
			null,
			true
		);
	}

	/**
	 * Check it is an active provider.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_active() {

		return (bool) wpforms_setting( Settings::SLUG . '-' . SettingsGooglePlaces::SLUG . '-api-key' );
	}
}
