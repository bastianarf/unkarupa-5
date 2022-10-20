<?php

namespace WPFormsGetResponse\Helpers;

/**
 * Class Formatting.
 *
 * @since 1.3.0
 */
class Formatting {

	/**
	 * Sanitize a contact name.
	 *
	 * @since 1.3.0
	 *
	 * @param string $name Contact name.
	 *
	 * @return string The sanitized value.
	 */
	public static function sanitize_contact_name( $name ) {

		// GetResponse allows 128 characters max. Otherwise it returns a request error.
		$sanitized = wp_html_excerpt( $name, 128 );

		/**
		 * Filter a sanitized contact name.
		 *
		 * @since 1.3.0
		 *
		 * @param string $sanitized The sanitized contact name.
		 * @param string $name      Contact name before sanitization.
		 */
		return apply_filters( 'wpforms_getresponse_helpers_formatting_sanitize_contact_name', $sanitized, $name );
	}

	/**
	 * Sanitize a tag name.
	 *
	 * @since 1.3.0
	 *
	 * @param string $name Tag name.
	 *
	 * @return string The sanitized value.
	 */
	public static function sanitize_tag_name( $name ) {

		// GetResponse allows an English alphabet, numbers, underscores ("_") and 255 characters max. Otherwise it returns a request error.
		$name      = preg_replace( '/[^_a-zA-Z0-9]/', '_', $name );
		$sanitized = wp_html_excerpt( $name, 255 );

		// Skip names where less than 2 characters.
		if ( strlen( $sanitized ) < 2 ) {
			$sanitized = '';
		}

		/**
		 * Filter a sanitized tag name.
		 *
		 * @since 1.3.0
		 *
		 * @param string $sanitized The sanitized tag name.
		 * @param string $name      Tag name before sanitization.
		 */
		return apply_filters( 'wpforms_getresponse_helpers_formatting_sanitize_tag_name', $sanitized, $name );
	}

	/**
	 * Sanitize a resource unique ID.
	 *
	 * @since 1.3.0
	 *
	 * @param string $id Resource unique ID.
	 *
	 * @return string The sanitized value.
	 */
	public static function sanitize_resource_id( $id ) {

		// The ID is a combination of lower and upper case letters and digits (no special characters).
		$sanitized = preg_replace( '/[^a-zA-Z0-9]/', '', $id );

		/**
		 * Filter a sanitized resource unique ID.
		 *
		 * @since 1.3.0
		 *
		 * @param string $sanitized The sanitized resource unique ID.
		 * @param string $id        Resource unique ID before sanitization.
		 */
		return apply_filters( 'wpforms_getresponse_helpers_formatting_sanitize_resource_id', $sanitized, $id );
	}

	/**
	 * Sanitize a custom field value.
	 *
	 * @since 1.3.0
	 *
	 * @param string|array $value Custom field value(s).
	 *
	 * @return string The sanitized value.
	 */
	public static function sanitize_custom_field_value( $value ) {

		// GetResponse allows maximum 255 characters. Otherwise it returns a request error.
		$sanitized = wp_html_excerpt( $value, 255 );

		/**
		 * Filter a sanitized contact name.
		 *
		 * @since 1.3.0
		 *
		 * @param string $sanitized The sanitized contact name.
		 * @param string $value     Custom field value before sanitization.
		 */
		return apply_filters( 'wpforms_getresponse_helpers_formatting_sanitize_custom_field_value', $sanitized, $value );
	}
}
