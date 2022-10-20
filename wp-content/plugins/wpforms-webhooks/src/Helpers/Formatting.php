<?php

namespace WPFormsWebhooks\Helpers;

/**
 * Class Formatting.
 *
 * @since 1.0.0
 */
class Formatting {

	/**
	 * Sanitize a HTTP header name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name HTTP header name.
	 *
	 * @return string The sanitized value.
	 */
	public static function sanitize_header_name( $name ) {

		// Allow characters (lowercase and uppercase), numbers, decimal point, underscore and minus.
		$sanitized = preg_replace( '/[^a-zA-Z0-9._-]/', '', $name );

		/**
		 * Filter a sanitized HTTP header name.
		 *
		 * @since 1.0.0
		 *
		 * @param string $sanitized The sanitized HTTP header name.
		 * @param string $name      HTTP header name before sanitization.
		 */
		return apply_filters( 'wpforms_webhooks_formatting_sanitize_header_name', $sanitized, $name );
	}

	/**
	 * Sanitize a HTTP header value.
	 *
	 * @since 1.0.0
	 *
	 * @see https://github.com/laminas/laminas-http/blob/master/src/Header/HeaderValue.php#L33
	 *
	 * @param string $value HTTP header value.
	 *
	 * @return string The sanitized value.
	 */
	public static function sanitize_header_value( $value ) {

		$value     = (string) $value;
		$length    = strlen( $value );
		$sanitized = '';

		for ( $i = 0; $i < $length; ++$i ) {
			$ascii = ord( $value[ $i ] );

			// Non-visible, non-whitespace characters
			// 9 === horizontal tab
			// 32-126, 128-254 === visible
			// 127 === DEL
			// 255 === null byte.
			if (
				( $ascii < 32 && $ascii !== 9 ) ||
				$ascii === 127 ||
				$ascii > 254
			) {
				continue;
			}

			$sanitized .= $value[ $i ];
		}

		/**
		 * Filter a sanitized HTTP header value.
		 *
		 * @since 1.0.0
		 *
		 * @param string $sanitized The sanitized HTTP header value.
		 * @param string $value     HTTP header value before sanitization.
		 */
		return apply_filters( 'wpforms_webhooks_formatting_sanitize_header_value', $sanitized, $value );
	}

	/**
	 * Check if a string is a valid URL.
	 * If debugging is on - localhost-environments is included to whitelist.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url Input URL.
	 *
	 * @return bool
	 */
	public static function is_url( $url ) {

		$valid = wpforms_is_url( $url );

		// Return result if it's not debugging.
		if ( ! wpforms_debug() ) {
			return $valid;
		}

		// Return result if URL already valid.
		if ( $valid ) {
			return $valid;
		}

		// Array for testing on local environment.
		$whitelist = [ 'localhost', '127.0.0.1', '192.168.' ];

		foreach ( $whitelist as $env ) {

			if ( false !== strpos( $url, $env ) ) {
				$valid = true;
				break;
			}
		}

		return $valid;
	}
}
