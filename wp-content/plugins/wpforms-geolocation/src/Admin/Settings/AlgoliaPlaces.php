<?php

namespace WPFormsGeolocation\Admin\Settings;

use WPForms_Admin_Notice;

/**
 * Class AlgoliaPlaces.
 *
 * @since 2.0.0
 */
class AlgoliaPlaces {

	/**
	 * Provider slug.
	 *
	 * @since 2.0.0
	 */
	const SLUG = 'algolia-places';

	/**
	 * Init hooks.
	 *
	 * @since 2.0.0
	 */
	public function hooks() {

		add_filter( 'wpforms_geolocation_admin_settings_settings_get_providers', [ $this, 'register_provider' ] );
		add_filter( 'wpforms_geolocation_admin_settings_settings_get_provider_options_' . self::SLUG, [ $this, 'settings' ] );
		add_filter( 'pre_update_option_wpforms_settings', [ $this, 'validate' ] );
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

		$providers[ self::SLUG ] = esc_html__( 'Algolia Places API', 'wpforms-geolocation' );

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
			'heading'             => [
				'id'       => 'heading',
				'content'  => sprintf(
					'<h4>%s</h4><p>%s</p>',
					esc_html__( 'Algolia Places API', 'wpforms-geolocation' ),
					esc_html__( 'Fill in credentials for the Algolia API.', 'wpforms-geolocation' )
				),
				'type'     => 'content',
				'no_label' => true,
				'class'    => [ 'section-heading' ],
			],
			'application-id'      => [
				'type' => 'text',
				'id'   => 'application-id',
				'name' => esc_html__( 'Application ID', 'wpforms-geolocation' ),
			],
			'search-only-api-key' => [
				'type' => 'text',
				'id'   => 'search-only-api-key',
				'name' => esc_html__( 'Search Only API Key', 'wpforms-geolocation' ),
			],
		];
	}

	/**
	 * Validate credentials.
	 *
	 * @since 2.0.0
	 *
	 * @param mixed $settings WPForms settings.
	 *
	 * @return mixed
	 */
	public function validate( $settings ) {

		if ( ! is_array( $settings ) ) {
			return $settings;
		}

		$application_id_name = sprintf(
			'%s-%s-%s',
			Settings::SLUG,
			self::SLUG,
			'application-id'
		);

		if ( empty( $settings[ $application_id_name ] ) || strpos( $settings[ $application_id_name ], 'pl' ) === 0 ) {
			return $settings;
		}

		$search_only_api_key_name = sprintf(
			'%s-%s-%s',
			Settings::SLUG,
			self::SLUG,
			'search-only-api-key'
		);

		unset( $settings[ $application_id_name ] );
		unset( $settings[ $search_only_api_key_name ] );

		$field_provider_name = sprintf( '%s-field-provider', Settings::SLUG );

		if ( empty( $settings[ $field_provider_name ] ) || $settings[ $field_provider_name ] !== self::SLUG ) {
			return $settings;
		}

		WPForms_Admin_Notice::error(
			wp_kses(
				sprintf( /* translators: %s - link to documentation. */
					__( 'Your Application ID for Algolia Places is invalid. An Application ID must begin with \'pl\'. Please read more in <a href="%s" target="_blank" rel="noopener noreferrer">our addon documentation</a>.', 'wpforms-geolocation' ),
					'https://wpforms.com/docs/how-to-install-and-use-the-geolocation-addon-with-wpforms/'
				),
				[
					'a' => [
						'rel'    => true,
						'href'   => true,
						'target' => true,
					],
				]
			)
		);

		return $settings;
	}
}
