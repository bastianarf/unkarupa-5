<?php

namespace WPFormsGeolocation;

/**
 * Class Map.
 *
 * @since 2.0.0
 */
class Map {

	/**
	 * Print map container.
	 *
	 * @since 2.0.0
	 *
	 * @param string $size Map size.
	 */
	public function print_map( $size ) {

		echo $this->get_map( $size ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}


	/**
	 * Get map container.
	 *
	 * @since 2.0.0
	 *
	 * @param string $size Map size. 'medium' by default.
	 *
	 * @return string
	 */
	public function get_map( $size ) {

		$size = (string) $size;

		if ( empty( $size ) ) {
			$size = 'medium';
		}

		return sprintf(
			'<div class="wpforms-field-row %s wpforms-geolocation-map"></div>',
			esc_attr( 'wpforms-field-' . $size )
		);
	}

	/**
	 * Get default latitude and longitude.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	private function get_default_location() {

		return (array) apply_filters(
			'wpforms_geolocation_map_default_location',
			[
				'lat' => 40.7831,
				'lng' => -73.9712,
			]
		);
	}

	/**
	 * Get default zoom.
	 *
	 * @since 2.0.0
	 *
	 * @return int
	 */
	private function get_zoom() {

		return absint( apply_filters( 'wpforms_geolocation_map_zoom', 9, 'field' ) );
	}

	/**
	 * Get map settings.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_settings() {

		return [
			'zoom'             => $this->get_zoom(),
			'default_location' => $this->get_default_location(),
		];
	}
}
