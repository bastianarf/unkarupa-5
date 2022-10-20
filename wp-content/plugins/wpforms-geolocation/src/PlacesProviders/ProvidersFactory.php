<?php

namespace WPFormsGeolocation\PlacesProviders;

use WPFormsGeolocation\Admin\Settings\Settings;

/**
 * Class ProvidersFactory.
 *
 * @since 2.0.0
 */
class ProvidersFactory {

	/**
	 * Settings.
	 *
	 * @since 2.0.0
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * List of available providers.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	private $providers = [
		'google-places'  => GooglePlaces::class,
		'algolia-places' => AlgoliaPlaces::class,
	];

	/**
	 * ProvidersFactory constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param Settings $settings Settings.
	 */
	public function __construct( Settings $settings ) {

		$this->settings = $settings;
	}

	/**
	 * Get current provider.
	 *
	 * @since 2.0.0
	 *
	 * @return IPlacesProvider|null
	 */
	public function get_current_provider() {

		$current_provider = $this->settings->get_current_provider();

		if ( ! isset( $this->providers[ $current_provider ] ) ) {
			return null;
		}

		return new $this->providers[ $current_provider ]();
	}
}
