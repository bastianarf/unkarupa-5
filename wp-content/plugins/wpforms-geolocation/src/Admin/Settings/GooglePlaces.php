<?php

namespace WPFormsGeolocation\Admin\Settings;

/**
 * Class GooglePlaces.
 *
 * @since 2.0.0
 */
class GooglePlaces {

	/**
	 * Provider slug.
	 *
	 * @since 2.0.0
	 */
	const SLUG = 'google-places';

	/**
	 * Init hooks.
	 *
	 * @since 2.0.0
	 */
	public function hooks() {

		add_filter( 'wpforms_geolocation_admin_settings_settings_get_providers', [ $this, 'register_provider' ] );
		add_filter( 'wpforms_geolocation_admin_settings_settings_get_provider_options_' . self::SLUG, [ $this, 'settings' ] );
	}

	/**
	 * Register providers.
	 *
	 * @since 2.0.0
	 *
	 * @param array $providers List of providers.
	 *
	 * @return array
	 */
	public function register_provider( $providers ) {

		$providers[ self::SLUG ] = esc_html__( 'Google Places API', 'wpforms-geolocation' );

		return $providers;
	}

	/**
	 * Register settings.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function settings() {

		return [
			'heading' => [
				'id'       => 'heading',
				'content'  => sprintf(
					'<h4>%s</h4><p>%s</p>',
					esc_html__( 'Google Places API', 'wpforms-geolocation' ),
					esc_html__( 'Fill in credentials for the Google API.', 'wpforms-geolocation' )
				),
				'type'     => 'content',
				'no_label' => true,
				'class'    => [ 'section-heading' ],
			],
			'api-key' => [
				'type' => 'text',
				'id'   => 'api-key',
				'name' => esc_html__( 'API Key', 'wpforms-geolocation' ),
			],
		];
	}
}
