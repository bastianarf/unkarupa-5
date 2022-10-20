<?php

namespace WPFormsGeolocation;

/**
 * Class RetrieveGeoData.
 *
 * @since 2.0.0
 */
class RetrieveGeoData {

	/**
	 * Get geolocation information from an IP address.
	 *
	 * @since 2.0.0
	 *
	 * @param string $ip User IP.
	 *
	 * @return array
	 */
	public function get_location( $ip = '' ) {

		if ( $this->is_local( $ip ) ) {
			return [];
		}

		$sources = [
			'wpforms' => 'https://geo.wpforms.com/v3/geolocate/json/%s',
			'ipapi'   => 'https://ipapi.co/%s/json',
			'keycdn'  => 'https://tools.keycdn.com/geo.json?host=%s',
		];

		foreach ( $sources as $source => $endpoint ) {
			$data = $this->request( $source, $endpoint, $ip );

			if ( ! empty( $data ) ) {
				return $data;
			}
		}

		return [];
	}

	/**
	 * Is local IP Address.
	 *
	 * @since 2.0.0
	 *
	 * @param string $ip IP Address.
	 *
	 * @return bool
	 */
	private function is_local( $ip = '' ) {

		return empty( $ip ) || in_array( $ip, [ '127.0.0.1', '::1' ], true );
	}

	/**
	 * Request for get user geolocation data.
	 *
	 * @since 2.0.0
	 *
	 * @param string $source   Source name.
	 * @param string $endpoint Endpoint.
	 * @param string $ip       IP address.
	 *
	 * @return array
	 */
	private function request( $source, $endpoint, $ip ) {

		$endpoint = sprintf(
			$endpoint,
			$ip
		);
		$data     = [];
		$request  = wp_remote_get( $endpoint );

		if ( ! is_wp_error( $request ) ) {
			$request = json_decode( wp_remote_retrieve_body( $request ), true );
			$method  = $source . '_response';
			$data    = $this->{$method}( $request );
		}

		return $data;
	}

	/**
	 * Processing request from WPForms.
	 *
	 * @since 2.0.0
	 *
	 * @param array $request_body Request body.
	 *
	 * @return array
	 */
	private function wpforms_response( $request_body ) {

		if ( empty( $request_body['latitude'] ) || empty( $request_body['longitude'] ) ) {
			return [];
		}

		return [
			'latitude'  => sanitize_text_field( $request_body['latitude'] ),
			'longitude' => sanitize_text_field( $request_body['longitude'] ),
			'city'      => ! empty( $request_body['city'] ) ? sanitize_text_field( $request_body['city'] ) : '',
			'region'    => ! empty( $request_body['region_name'] ) ? sanitize_text_field( $request_body['region_name'] ) : '',
			'country'   => ! empty( $request_body['country_iso'] ) ? sanitize_text_field( $request_body['country_iso'] ) : '',
			'postal'    => ! empty( $request_body['zip_code'] ) ? sanitize_text_field( $request_body['zip_code'] ) : '',
		];
	}

	/**
	 * Processing request from IpAPI.
	 *
	 * @since 2.0.0
	 *
	 * @param array $request_body Request body.
	 *
	 * @return array
	 */
	private function ipapi_response( $request_body ) {

		if ( empty( $request_body['latitude'] ) || empty( $request_body['longitude'] ) ) {
			return [];
		}

		return [
			'latitude'  => sanitize_text_field( $request_body['latitude'] ),
			'longitude' => sanitize_text_field( $request_body['longitude'] ),
			'city'      => ! empty( $request_body['city'] ) ? sanitize_text_field( $request_body['city'] ) : '',
			'region'    => ! empty( $request_body['region'] ) ? sanitize_text_field( $request_body['region'] ) : '',
			'country'   => ! empty( $request_body['country'] ) ? sanitize_text_field( $request_body['country'] ) : '',
			'postal'    => ! empty( $request_body['postal'] ) ? sanitize_text_field( $request_body['postal'] ) : '',
		];
	}

	/**
	 * Processing request from KeyCDN.
	 *
	 * @since 2.0.0
	 *
	 * @param array $request_body Request body.
	 *
	 * @return array
	 */
	private function keycdn_response( $request_body ) {

		if ( empty( $request_body['data']['geo']['latitude'] ) || empty( $request_body['data']['geo']['longitude'] ) ) {
			return [];
		}

		return [
			'latitude'  => sanitize_text_field( $request_body['data']['geo']['latitude'] ),
			'longitude' => sanitize_text_field( $request_body['data']['geo']['longitude'] ),
			'city'      => ! empty( $request_body['data']['geo']['city'] ) ? sanitize_text_field( $request_body['data']['geo']['city'] ) : '',
			'region'    => ! empty( $request_body['data']['geo']['region_name'] ) ? sanitize_text_field( $request_body['data']['geo']['region_name'] ) : '',
			'country'   => ! empty( $request_body['data']['geo']['country_code'] ) ? sanitize_text_field( $request_body['data']['geo']['country_code'] ) : '',
			'postal'    => ! empty( $request_body['data']['geo']['postal_code'] ) ? sanitize_text_field( $request_body['data']['geo']['postal_code'] ) : '',
		];
	}
}
