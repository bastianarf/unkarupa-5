<?php

namespace WPFormsDrip\Provider;

require_once dirname( WPFORMS_DRIP_FILE ) . '/vendor/autoload.php';

use \DrewM\Drip\Drip as LibDrip;
use \DrewM\Drip\Response as LibResponse;

/**
 * Class Drip which extends 3rd party Drip library to provide more WPForms related things.
 *
 * @since 1.0.0
 */
class Drip extends LibDrip {

	/**
	 * Custom Drip constructor to make accountID optional.
	 *
	 * @since 1.0.0
	 *
	 * @param string $token Required to make requests.
	 * @param string $accountID Optional to make requests.
	 */
	public function __construct( $token, $accountID = '' ) {
		parent::__construct( $token, $accountID );
	}

	/**
	 * Make a request to Drip API without anchoring to accountID.
	 * Example: /accounts. See http://developer.drip.com/#accounts for more details.
	 *
	 * @since 1.0.0
	 *
	 * @param string $http_verb Possible values: get, post, delete.
	 * @param string $api_method Example: accounts.
	 * @param array $args Array of arguments to pass further to API.
	 * @param int $timeout Allowed time frame for a request.
	 *
	 * @return LibResponse
	 * @throws \Exception When no cURL available or error while connecting.
	 */
	public function makeRawRequest( $http_verb = 'post', $api_method, $args = [], $timeout = 10 ) {

		$url = $this->api_endpoint . '/' . $api_method;

		if ( function_exists( 'curl_init' ) && function_exists( 'curl_setopt' ) ) {

			// phpcs:disable
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, [
				'Accept: application/vnd.api+json',
				'Content-Type: application/vnd.api+json',
			] );
			curl_setopt( $ch, CURLOPT_USERAGENT, 'DrewM/Drip (github.com/drewm/drip)' );
			curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
			curl_setopt( $ch, CURLOPT_USERPWD, $this->token . ': ' );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl );
			curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
			curl_setopt( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
			curl_setopt( $ch, CURLOPT_URL, $url );

			switch ( $http_verb ) {
				case 'post':
					curl_setopt( $ch, CURLOPT_POST, 1 );
					curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $args ) );
					break;

				case 'get':
					$query = http_build_query( $args );
					curl_setopt( $ch, CURLOPT_URL, $url . '?' . $query );
					break;

				case 'delete':
					curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'DELETE' );
					break;
			}

			$result = curl_exec( $ch );

			if ( ! curl_errno( $ch ) ) {
				$info = curl_getinfo( $ch );
				curl_close( $ch );

				return new LibResponse( $info, $result );
			}

			$errno = curl_errno( $ch );
			$error = curl_error( $ch );

			curl_close( $ch );
			// phpcs:enable

			throw new \Exception( $error, $errno );
		} else {
			throw new \Exception( "cURL support is required, but can't be found.", 1 );
		}
	}

}
