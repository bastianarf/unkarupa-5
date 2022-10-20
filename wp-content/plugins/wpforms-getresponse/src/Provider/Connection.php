<?php

namespace WPFormsGetResponse\Provider;

use RuntimeException;
use WPFormsGetResponse\Helpers\Formatting;

/**
 * Class Connection.
 *
 * @since 1.3.0
 */
class Connection {

	/**
	 * Subscribe action name.
	 *
	 * @since 1.3.0
	 */
	const SUBSCRIBE = 'subscriber_subscribe';

	/**
	 * Unsubscribe action name.
	 *
	 * @since 1.3.0
	 */
	const UNSUBSCRIBE = 'subscriber_unsubscribe';

	/**
	 * Connection data.
	 *
	 * @since 1.3.0
	 *
	 * @var array
	 */
	private $data = [];

	/**
	 * Constructor method.
	 *
	 * @since 1.3.0
	 *
	 * @param array $raw_data Connection data.
	 *
	 * @throws RuntimeException Emitted when something went wrong.
	 */
	public function __construct( $raw_data ) {

		if ( ! is_array( $raw_data ) || empty( $raw_data ) ) {
			throw new RuntimeException( esc_html__( 'Unexpected connection data.', 'wpforms-getresponse' ) );
		}

		$this->set_data( $raw_data );
	}

	/**
	 * Sanitize and set connection data.
	 *
	 * @since 1.3.0
	 *
	 * @param array $raw_data Connection data.
	 */
	protected function set_data( $raw_data ) {

		$this->data = array_replace_recursive( $this->get_required_data(), $raw_data );

		$this->data['id']              = sanitize_key( $this->data['id'] );
		$this->data['name']            = sanitize_text_field( $this->data['name'] );
		$this->data['account_id']      = sanitize_text_field( $this->data['account_id'] );
		$this->data['list_id']         = Formatting::sanitize_resource_id( $this->data['list_id'] );
		$this->data['fields']['email'] = absint( $this->data['fields']['email'] );

		$this->set_action();

		if ( $this->data['action'] === self::SUBSCRIBE ) {
			$this->set_subscribe_data();
		}
	}

	/**
	 * Sanitize and set connection action.
	 *
	 * @since 1.3.0
	 */
	protected function set_action() {

		$action = sanitize_text_field( $this->data['action'] );

		if ( ! in_array( $action, [ self::SUBSCRIBE, self::UNSUBSCRIBE ], true ) ) {
			$action = '';
		}

		$this->data['action'] = $action;
	}

	/**
	 * Sanitize and set connection data for `Subscribe` action.
	 *
	 * @since 1.3.0
	 */
	protected function set_subscribe_data() {

		if ( isset( $this->data['fields']['name'] ) && ! wpforms_is_empty_string( $this->data['fields']['name'] ) ) {
			$this->data['fields']['name'] = absint( $this->data['fields']['name'] );
		}

		// Day of Cycle.
		if ( isset( $this->data['cycle_day'] ) && ! wpforms_is_empty_string( $this->data['cycle_day'] ) ) {
			$this->data['cycle_day'] = absint( $this->data['cycle_day'] );
		}

		// Sanitize tags.
		$this->sanitize_tags();

		// Sanitize custom fields.
		$this->sanitize_fields_meta();
	}

	/**
	 * Sanitize and retrieve tags.
	 *
	 * @since 1.3.0
	 */
	protected function sanitize_tags() {

		// Tags already exist in connection data. We just need to sanitize tag names.
		if ( ! empty( $this->data['tag_names'] ) && is_array( $this->data['tag_names'] ) ) {
			$this->data['tag_names'] = array_map( '\WPFormsGetResponse\Helpers\Formatting::sanitize_tag_name', $this->data['tag_names'] );
			return;
		}

		// No tags in connection data - it's a saving process. We need to grab tags from $_POST variable.
		$provider_slug = wpforms_getresponse_plugin()->provider->slug;
		$connection_id = $this->data['id'];
		$form_post     = ! empty( $_POST['data'] ) ? json_decode( wp_unslash( $_POST['data'] ), true ) : []; // phpcs:ignore WordPress.Security

		// Native WPForms saving doesn't support multiple selects.
		// We need to get tags data from $_POST variable.
		$tags = wpforms_chain( $form_post )
			->map(
				static function( $post_pair ) use ( $provider_slug, $connection_id ) {
					if (
						empty( $post_pair['name'] ) ||
						"providers[{$provider_slug}][{$connection_id}][tags][add][]" !== $post_pair['name']
					) {
						return '';
					}

					return Formatting::sanitize_tag_name( $post_pair['value'] );
				}
			)
			->array_filter()
			->array_values()
			->value();

		// If user provided new tags.
		if ( ! empty( $this->data['tags']['new'] ) ) {
			$tags = wpforms_chain( $this->data['tags']['new'] )
				->explode( ',' )
				->map(
					static function( $name ) {

						return Formatting::sanitize_tag_name( $name );
					}
				)
				->array_filter()
				->array_merge( (array) $tags )
				->array_unique()
				->value();
		}

		if ( ! empty( $tags ) ) {
			$this->data['tag_names'] = $tags;
		}
	}

	/**
	 * Sanitize and retrieve fields meta.
	 *
	 * @since 1.3.0
	 */
	protected function sanitize_fields_meta() {

		if ( empty( $this->data['fields_meta'] ) || ! is_array( $this->data['fields_meta'] ) ) {
			return;
		}

		$fields_meta = [];
		foreach ( $this->data['fields_meta'] as $property ) {

			if ( ! isset( $property['name'], $property['field_id'] ) ) {
				continue;
			}

			$custom_field_id = Formatting::sanitize_resource_id( $property['name'] );

			if ( empty( $custom_field_id ) ) {
				continue;
			}

			$fields_meta[] = [
				'name'     => $custom_field_id,
				'field_id' => (int) $property['field_id'],
			];
		}

		$this->data['fields_meta'] = $fields_meta;
	}

	/**
	 * Retrieve connection data.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	public function get_data() {

		return $this->data;
	}

	/**
	 * Retrieve defaults for connection data.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	protected function get_required_data() {

		return [
			'id'         => '',
			'name'       => '',
			'action'     => '',
			'account_id' => '',
			'list_id'    => '',
			'fields'     => [
				'email' => '',
			],
		];
	}

	/**
	 * Determine if connection data is valid.
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public function is_valid() {

		if (
			empty( $this->data['action'] ) ||
			empty( $this->data['account_id'] ) ||
			empty( $this->data['list_id'] )
		) {
			return false;
		}

		return true;
	}
}
