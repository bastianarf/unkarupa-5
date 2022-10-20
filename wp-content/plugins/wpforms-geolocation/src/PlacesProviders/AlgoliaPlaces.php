<?php

namespace WPFormsGeolocation\PlacesProviders;

use WPFormsGeolocation\Admin\Settings\Settings;
use WPFormsGeolocation\Admin\Settings\AlgoliaPlaces as SettingsAlgoliaPlaces;

/**
 * Class AlgoliaPlaces.
 *
 * @since 2.0.0
 */
class AlgoliaPlaces implements IPlacesProvider {

	/**
	 * Forms has map.
	 *
	 * @since 2.0.0
	 *
	 * @var bool|null
	 */
	protected $has_map;

	/**
	 * Init Algolia Places provider.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		if ( ! $this->is_active() ) {
			return;
		}

		add_action( 'wpforms_frontend_css', [ $this, 'enqueue_styles' ] );
		add_action( 'wpforms_frontend_js', [ $this, 'enqueue_scripts' ] );
		add_filter( 'wpforms_geolocation_front_fields_settings', [ $this, 'settings' ] );
	}

	/**
	 * Enqueue styles.
	 *
	 * @since 2.0.0
	 *
	 * @param array $forms List of forms.
	 */
	public function enqueue_styles( $forms ) {

		$min = wpforms_get_min_suffix();

		$deps = [];

		if ( $this->has_map( $forms ) ) {
			wp_enqueue_style(
				'leaflet',
				'https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.css',
				[],
				'1.7.1'
			);

			$deps[] = 'leaflet';
		}

		wp_enqueue_style(
			'wpforms-geolocation-algolia',
			WPFORMS_GEOLOCATION_URL . "assets/css/wpforms-geolocation-algolia{$min}.css",
			$deps,
			WPFORMS_GEOLOCATION_VERSION
		);
	}

	/**
	 * Forms has autocomplete field with enabled map.
	 *
	 * @since 2.0.0
	 *
	 * @param array $forms List of forms.
	 *
	 * @return bool
	 */
	private function has_map( $forms ) {

		if ( ! is_null( $this->has_map ) ) {
			return $this->has_map;
		}

		foreach ( $forms as $form ) {
			foreach ( $form['fields'] as $field ) {
				if ( ! empty( $field['enable_address_autocomplete'] ) && ! empty( $field['display_map'] ) ) {
					$this->has_map = true;

					return true;
				}
			}
		}

		$this->has_map = false;

		return false;
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 2.0.0
	 *
	 * @param array $forms List of forms.
	 */
	public function enqueue_scripts( $forms ) {

		$min = wpforms_get_min_suffix();

		$deps = [ 'algolia-places', 'algolia-autocomplete' ];

		if ( $this->has_map( $forms ) ) {
			wp_enqueue_script(
				'leaflet',
				'https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.js',
				[],
				'1.7.1',
				true
			);

			$deps[] = 'leaflet';
		}

		wp_enqueue_script(
			'algolia-places',
			'https://cdn.jsdelivr.net/npm/places.js',
			[],
			'1.19.0',
			true
		);

		wp_enqueue_script(
			'algolia-autocomplete',
			'https://cdn.jsdelivr.net/npm/algoliasearch@3.35.1/dist/algoliasearchLite.min.js',
			[],
			'3.35.1',
			true
		);

		wp_enqueue_script(
			'wpforms-geolocation-algolia-api',
			WPFORMS_GEOLOCATION_URL . "assets/js/wpforms-geolocation-algolia-api{$min}.js",
			$deps,
			WPFORMS_GEOLOCATION_VERSION,
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

		return wpforms_setting( Settings::SLUG . '-' . SettingsAlgoliaPlaces::SLUG . '-application-id' ) && wpforms_setting( Settings::SLUG . '-' . SettingsAlgoliaPlaces::SLUG . '-search-only-api-key' );
	}

	/**
	 * Add JS settings.
	 *
	 * @since 2.0.0
	 *
	 * @param array $settings List of JS settings.
	 *
	 * @return array
	 */
	public function settings( $settings ) {

		$settings += [
			'app_id'   => wpforms_setting( Settings::SLUG . '-' . SettingsAlgoliaPlaces::SLUG . '-application-id' ),
			'api_key'  => wpforms_setting( Settings::SLUG . '-' . SettingsAlgoliaPlaces::SLUG . '-search-only-api-key' ),
			'language' => $this->get_language(),
		];

		return $settings;
	}

	/**
	 * Get current language.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	private function get_language() {

		return (string) apply_filters( 'wpforms_geolocation_place_providers_algolia_places_get_language', substr( get_locale(), 0, 2 ) );
	}
}
