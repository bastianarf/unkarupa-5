<?php

namespace WPFormsGeolocation\PlacesProviders;

/**
 * Interface IPlacesProvider.
 *
 * @since 2.0.0
 */
interface IPlacesProvider {

	/**
	 * Init provider.
	 *
	 * @since 2.0.0
	 */
	public function init();

	/**
	 * Check filling settings for this provider.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_active();
}
