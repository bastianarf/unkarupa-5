<?php

namespace WPFormsWebhooks;

use WPForms\Helpers\Crypto;
use WPFormsWebhooks\Helpers\Formatting;

/**
 * Class Webhook.
 *
 * @since 1.0.0
 */
class Webhook {

	/**
	 * Webhook data.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * Apply encryption for custom request values.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $use_encrypt = false;

	/**
	 * Constructor method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data        Webhook data.
	 * @param bool  $use_encrypt Pass `true` if we want to encrypt custom request values.
	 */
	public function __construct( $data = [], $use_encrypt = false ) {

		$this->use_encrypt = wp_validate_boolean( $use_encrypt );

		if ( ! empty( $data ) && is_array( $data ) ) {
			$this->set_data( $data );
		}
	}

	/**
	 * Set and sanitize data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $raw_data Webhook data.
	 */
	protected function set_data( $raw_data ) {

		$defaults = $this->get_defaults();
		$data     = wp_parse_args( $raw_data, $defaults );
		$methods  = wpforms_webhooks()->get_available_methods();

		$data['id']      = absint( $data['id'] );
		$data['name']    = sanitize_text_field( $data['name'] );
		$data['secret']  = Formatting::sanitize_header_value( $data['secret'] );
		$data['url']     = esc_url_raw( $data['url'] );
		$data['method']  = isset( $methods[ $data['method'] ] ) ? $data['method'] : $defaults['method'];
		$data['headers'] = $this->set_request_params( $data['headers'] );
		$data['body']    = $this->set_request_params( $data['body'] );

		$this->data = $data;
	}

	/**
	 * Returns all data for this object.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_data() {

		return $this->data;
	}

	/**
	 * Set and sanitize request parameters for header, body, etc.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $raw_params Request parameters.
	 * @param string $context    Context for request parameters - header or body.
	 *
	 * @return array
	 */
	protected function set_request_params( $raw_params, $context = 'header' ) {

		$params = [];

		foreach ( $raw_params as $name => $value ) {

			$name = Formatting::sanitize_header_name( $name );

			// Skip params if name it's an empty string.
			if ( wpforms_is_empty_string( $name ) ) {
				continue;
			}

			// Determine if it a custom value.
			if ( 0 === strpos( $name, 'custom_' ) && is_array( $value ) ) {
				$value = $this->set_request_custom_value( $value, $context );
			} else {
				$value = ! wpforms_is_empty_string( $value ) ? absint( $value ) : false;
			}

			if ( false === $value ) {
				continue;
			}

			$params[ $name ] = $value;
		}

		return $params;
	}

	/**
	 * Set and sanitize custom value for request.
	 *
	 * @since {VERSION}
	 *
	 * @param array  $custom  Custom value data.
	 * @param string $context Context for request parameters - header or body.
	 *
	 * @return array|false
	 */
	protected function set_request_custom_value( $custom, $context ) {

		if ( ! isset( $custom['value'] ) ) {
			return false;
		}

		if ( wpforms_is_empty_string( $custom['value'] ) ) {
			return [ 'value' => '' ];
		}

		if ( 'header' === $context ) {
			$custom['value'] = Formatting::sanitize_header_value( $custom['value'] );
		}

		if ( ! $this->use_encrypt || ! isset( $custom['secure'] ) ) {
			return $custom;
		}

		$custom['secure'] = wp_validate_boolean( $custom['secure'] );

		if ( $custom['secure'] ) {
			$custom['value'] = Crypto::encrypt( $custom['value'] );
		}

		return $custom;
	}

	/**
	 * Set the secret used for generating the HMAC-SHA256 signature.
	 *
	 * @since 1.0.0
	 */
	public function set_secret() {

		$this->data['secret'] = wp_generate_password( 64, true, true );
	}

	/**
	 * Get the secret used for generating the HMAC-SHA256 signature.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_secret() {

		return $this->data['secret'];
	}

	/**
	 * Get the Webhook default data.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_defaults() {

		return [
			'name'    => esc_html__( 'Unnamed Webhook', 'wpforms-webhooks' ),
			'method'  => 'get',
			'format'  => 'json',
			'url'     => '',
			'secret'  => '',
			'headers' => [],
			'body'    => [],
		];
	}
}
