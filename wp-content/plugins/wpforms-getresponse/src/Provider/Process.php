<?php

namespace WPFormsGetResponse\Provider;

use Exception;
use WPForms\Tasks\Meta;
use WPFormsGetResponse\Helpers\Formatting;

/**
 * Class Process handles entries processing using the provider settings and configuration.
 *
 * @since 1.3.0
 */
class Process extends \WPForms\Providers\Provider\Process {

	/**
	 * Async task action: subscribe.
	 *
	 * @since 1.3.0
	 */
	const ACTION_SUBSCRIBE = 'wpforms_getresponse_process_action_subscribe';

	/**
	 * Async task action: unsubscribe.
	 *
	 * @since 1.3.0
	 */
	const ACTION_UNSUBSCRIBE = 'wpforms_getresponse_process_action_unsubscribe';

	/**
	 * Connection data.
	 *
	 * @since 1.3.0
	 *
	 * @var array
	 */
	private $connection;

	/**
	 * Main class that communicates with the GetResponse API.
	 *
	 * @since 1.3.0
	 *
	 * @var Api
	 */
	private $api_client;

	/**
	 * Process constructor.
	 *
	 * @since 1.3.0
	 *
	 * @param Core $core Core instance of the provider class.
	 */
	public function __construct( Core $core ) {

		parent::__construct( $core );

		$this->hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.3.0
	 */
	public function hooks() {

		// Register async tasks handlers.
		add_action( self::ACTION_SUBSCRIBE,   [ $this, 'task_async_action_trigger' ] );
		add_action( self::ACTION_UNSUBSCRIBE, [ $this, 'task_async_action_trigger' ] );
	}

	/**
	 * Receive all wpforms_process_complete params and do the actual processing.
	 *
	 * @since 1.3.0
	 *
	 * @param array $fields    Array of form fields.
	 * @param array $entry     Submitted form content.
	 * @param array $form_data Form data and settings.
	 * @param int   $entry_id  ID of a saved entry.
	 */
	public function process( $fields, $entry, $form_data, $entry_id ) {

		// Only run if we have required data.
		if (
			empty( $form_data['providers'][ $this->core->slug ] ) ||
			empty( $entry_id )
		) {
			return;
		}

		$this->fields    = $fields;
		$this->entry     = $entry;
		$this->form_data = $form_data;
		$this->entry_id  = $entry_id;

		$this->process_each_connection();
	}

	/**
	 * Iteration loop for connections - call action for each connection.
	 *
	 * @since 1.3.0
	 */
	protected function process_each_connection() {

		foreach ( $this->form_data['providers'][ $this->core->slug ] as $connection_id => $connection_data ) :

			try {
				$connection = new Connection( $connection_data );
			} catch ( Exception $e ) {
				continue;
			}

			if ( ! $connection->is_valid() ) {
				continue;
			}

			$connection_data = $connection->get_data();

			// Make sure that we have an email value.
			if (
				! isset( $this->fields[ $connection_data['fields']['email'] ] ) ||
				empty( $this->fields[ $connection_data['fields']['email'] ]['value'] )
			) {
				continue;
			}

			// Check for conditional logic.
			if ( ! $this->is_conditionals_passed( $connection_data ) ) {
				continue;
			}

			$this->connection = $connection_data;
			$this->register_action_task();

		endforeach;
	}

	/**
	 * Register AS task for a connection.
	 *
	 * @since 1.3.0
	 */
	protected function register_action_task() {

		// Fire a connection action.
		switch ( $this->connection['action'] ) {

			case 'subscriber_subscribe':
				wpforms()->get( 'tasks' )
						 ->create( self::ACTION_SUBSCRIBE )->async()
						 ->params( $this->connection, $this->fields, $this->form_data, $this->entry_id )
						 ->register();
				break;

			case 'subscriber_unsubscribe':
				wpforms()->get( 'tasks' )
						 ->create( self::ACTION_UNSUBSCRIBE )->async()
						 ->params( $this->connection, $this->fields, $this->form_data, $this->entry_id )
						 ->register();
				break;
		}
	}

	/**
	 * Process Conditional Logic for the provided connection.
	 *
	 * @since 1.3.0
	 *
	 * @param array $connection Connection data.
	 *
	 * @return bool False if CL rules stopped the connection execution, true otherwise.
	 */
	protected function is_conditionals_passed( $connection ) {

		$pass = $this->process_conditionals( $this->fields, $this->form_data, $connection );

		// Check the conditional logic.
		if ( ! $pass ) {
			wpforms_log(
				'Form to GetResponse processing stopped by conditional logic.',
				$this->fields,
				[
					'type'    => [ 'provider', 'conditional_logic' ],
					'parent'  => $this->entry_id,
					'form_id' => $this->form_data['id'],
				]
			);
		}

		return $pass;
	}

	/**
	 * Process the addon async tasks.
	 *
	 * @since 1.3.0
	 *
	 * @param int $meta_id Task meta ID.
	 */
	public function task_async_action_trigger( $meta_id ) {

		$meta = $this->get_task_meta( $meta_id );

		// We expect a certain type and number of params.
		if ( ! is_array( $meta ) || count( $meta ) !== 4 ) {
			return;
		}

		// We expect a certain meta data structure for this task.
		list( $this->connection, $this->fields, $this->form_data, $this->entry_id ) = $meta;

		$this->api_client = $this->get_api_client();

		if ( null === $this->api_client ) {
			return;
		}

		// Finally, fire the actual action processing.
		switch ( $this->connection['action'] ) {

			case 'subscriber_subscribe':
				$this->task_async_action_subscribe();
				break;

			case 'subscriber_unsubscribe':
				$this->task_async_action_unsubscribe();
				break;
		}
	}

	/**
	 * Subscriber: Create or Update action.
	 *
	 * @since 1.3.0
	 */
	protected function task_async_action_subscribe() {

		$email = sanitize_email( $this->fields[ $this->connection['fields']['email'] ]['value'] );

		// Firstly, we need to check if contact already exists.
		$response = $this->api_client->search_contact(
			$email,
			[
				$this->connection['list_id'],
			]
		);

		// Request or response error.
		if (
			! $response->isSuccess() ||
			$response->getResponse()->getStatusCode() !== 200
		) {
			$this->log_errors( $response, $this->connection );
			return;
		}

		$contact    = $response->getData();
		$is_updated = false;

		if ( is_array( $contact ) && isset( $contact[0] ) ) {
			$contact    = $contact[0];
			$is_updated = true;
		}

		$name          = $this->fill_contact_name();
		$tags          = $this->fill_contact_tags();
		$custom_fields = $this->fill_contact_custom_fields();
		$cycle_day     = $this->fill_contact_cycle_day();

		if ( $is_updated ) {
			$request_args = [
				'name'              => $name,
				'dayOfCycle'        => $cycle_day,
				'tags'              => $tags,
				'customFieldValues' => $custom_fields,
			];

			// API call: update a contact data.
			$response = $this->api_client->update_contact( $request_args, $contact['contactId'] );

		} else {
			$request_args = [
				'email'             => $email,
				'campaignId'        => $this->connection['list_id'],
				'name'              => $name,
				'dayOfCycle'        => $cycle_day,
				'tags'              => $tags,
				'customFieldValues' => $custom_fields,
			];

			// API call: create a new contact.
			$response = $this->api_client->create_contact( $request_args );
		}

		// Request or response error.
		if (
			! $response->isSuccess() ||
			! in_array( $response->getResponse()->getStatusCode(), [ 200, 202 ], true )
		) {
			$this->log_errors( $response, $this->connection );
		}

		/**
		 * Fire when request was sent successfully or not.
		 *
		 * @since 1.3.0
		 *
		 * @param object $response     Response data.
		 * @param array  $request_args Request arguments.
		 * @param array  $contact      GetResponse contact data.
		 * @param array  $connection   Connection data.
		 * @param array  $args         Additional arguments.
		 */
		do_action(
			'wpforms_getresponse_provider_process_task_async_action_subscribe_after',
			$response,
			$request_args,
			$contact,
			$this->connection,
			[
				'form_data' => $this->form_data,
				'fields'    => $this->fields,
				'entry'     => $this->entry,
			]
		);
	}

	/**
	 * Subscriber: Unsubscribe action.
	 *
	 * @since 1.3.0
	 */
	protected function task_async_action_unsubscribe() {

		$email = sanitize_email( $this->fields[ $this->connection['fields']['email'] ]['value'] );

		// API call: we need to check if contact exists.
		$response = $this->api_client->search_contact(
			$email,
			[
				$this->connection['list_id'],
			]
		);

		// Request or response error.
		if (
			! $response->isSuccess() ||
			200 !== $response->getResponse()->getStatusCode() ||
			empty( $response->getData() )
		) {
			$this->log_errors( $response, $this->connection );
			return;
		}

		// Get a contact data.
		$contact = $response->getData();
		if ( is_array( $contact ) && ! empty( $contact ) ) {
			$contact = $contact[0];
		}

		// API call: delete a contact.
		$response = $this->api_client->delete_contact( $contact['contactId'] );

		// Request or response error.
		if (
			! $response->isSuccess() ||
			204 !== $response->getResponse()->getStatusCode()
		) {
			$this->log_errors( $response, $this->connection );
		}

		/**
		 * Fire when request was sent successfully or not.
		 *
		 * @since 1.3.0
		 *
		 * @param object $response   Response data.
		 * @param array  $contact    GetResponse contact data.
		 * @param array  $connection Connection data.
		 * @param array  $args       Additional arguments.
		 */
		do_action(
			'wpforms_getresponse_provider_process_task_async_action_unsubscribe_after',
			$response,
			$contact,
			$this->connection,
			[
				'form_data' => $this->form_data,
				'fields'    => $this->fields,
				'entry'     => $this->entry,
			]
		);
	}

	/**
	 * Fill a contact `name` and prepare it for attaching.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	protected function fill_contact_name() {

		// Check required data in the connection.
		if (
			! isset( $this->connection['fields']['name'], $this->fields[ $this->connection['fields']['name'] ] ) ||
			empty( $this->fields[ $this->connection['fields']['name'] ]['value'] )
		) {
			return '';
		}

		return Formatting::sanitize_contact_name( $this->fields[ $this->connection['fields']['name'] ]['value'] );
	}

	/**
	 * Fill connection tags and prepare their for attaching to a contact.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 * @throws \Getresponse\Sdk\Client\Exception\MalformedResponseDataException When JSON is invalid.
	 */
	protected function fill_contact_tags() {

		// Check a connection.
		if ( empty( $this->connection['tag_names'] ) ) {
			return [];
		}

		// API call: retrieve all tags.
		$exist_tags = array_column( $this->api_client->get_data_tags(), 'name', 'tagId' );

		$process = $this;

		// Run chain over connection tags array.
		return wpforms_chain( $this->connection['tag_names'] )
			->map(
				static function( $tag_name ) use ( $exist_tags, $process ) {

					// Check if a tag exists in GetResponse dashboard.
					$tag_id = array_search( $tag_name, $exist_tags, true );

					// Tag is exists - return it.
					if ( $tag_id !== false ) {
						return $tag_id;
					}

					// API call: create a new tag.
					$response = $process->api_client->create_tag( $tag_name );
					$new_tag  = $response->getData();

					// Request or response error.
					if (
						! $response->isSuccess() ||
						$response->getResponse()->getStatusCode() !== 201 ||
						empty( $new_tag['tagId'] )
					) {
						$process->log_errors( $response, $process->connection );
						return false;
					}

					return $new_tag['tagId'];
				}
			)
			->array_filter()
			->value();
	}

	/**
	 * Fill connection custom fields and prepare their for attaching to a contact.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 * @throws \Getresponse\Sdk\Client\Exception\MalformedResponseDataException When JSON is invalid.
	 */
	protected function fill_contact_custom_fields() {

		// Check a connection.
		if ( empty( $this->connection['fields_meta'] ) ) {
			return [];
		}

		// API call: retrieve all custom fields.
		$gr_custom_fields = $this->api_client->get_data_custom_fields();
		if ( empty( $gr_custom_fields ) ) {
			return [];
		}

		// Prepare a formatter instance.
		$formatter = new CustomFields( $this->fields, $this->entry, $this->form_data );

		// Run chain over connection fields meta array.
		return wpforms_chain( $this->connection['fields_meta'] )
			->map(
				static function( $field_meta ) use ( $gr_custom_fields, $formatter ) {

					// Check if a custom field exists in GetResponse dashboard.
					$key = array_search( $field_meta['name'], array_column( $gr_custom_fields, 'customFieldId' ), true );

					if ( false === $key ) {
						return '';
					}

					$gr_field_value = $formatter->run( $field_meta['field_id'], $gr_custom_fields[ $key ] );

					return ! empty( $gr_field_value ) ? [
						'id'    => $field_meta['name'],
						'value' => $gr_field_value,
					] : '';
				}
			)
			->array_filter()
			->value();
	}

	/**
	 * Fill connection day of cycle and prepare it for attaching to a contact.
	 *
	 * @since 1.3.0
	 *
	 * @return mixed
	 */
	protected function fill_contact_cycle_day() {

		if (
			! isset( $this->connection['cycle_day'] ) ||
			wpforms_is_empty_string( $this->connection['cycle_day'] )
		) {
			// `null` - it's a default value.
			return 'null';
		}

		return absint( $this->connection['cycle_day'] );
	}

	/**
	 * Get task meta data.
	 *
	 * @since 1.3.0
	 *
	 * @param int $meta_id Task meta ID.
	 *
	 * @return array|null Null when no data available.
	 */
	protected function get_task_meta( $meta_id ) {

		$task_meta = new Meta();
		$meta      = $task_meta->get( (int) $meta_id );

		// We should actually receive something.
		if ( empty( $meta ) || empty( $meta->data ) ) {
			return null;
		}

		return $meta->data;
	}

	/**
	 * Below are API related methods and their helpers.
	 */

	/**
	 * Get the API client based on connection and provider options.
	 *
	 * @since 1.3.0
	 *
	 * @return Api|null Null on error.
	 */
	protected function get_api_client() {

		if ( empty( $this->connection['account_id'] ) ) {
			return null;
		}

		$options = $this->core->get_provider_options();

		// Validate existence of required data.
		if ( empty( $options[ $this->connection['account_id'] ]['apikey'] ) ) {
			return null;
		}

		$api_key = trim( $options[ $this->connection['account_id'] ]['apikey'] );

		// Prepare an API client.
		try {
			return new Api( $api_key );
		} catch ( Exception $e ) {
			$this->log_errors( $e->getMessage(), $this->connection );
			return null;
		}
	}

	/**
	 * Log an API-related error with all the data.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed $response   Response data.
	 * @param array $connection Specific connection data that errored.
	 */
	protected function log_errors( $response, $connection ) {

		wpforms_log(
			'Submission to GetResponse failed.' . "(#{$this->entry_id})",
			[
				'response'   => is_object( $response ) && method_exists( $response, 'getData' ) ? $response->getData() : $response,
				'connection' => $connection,
			],
			[
				'type'    => [ 'provider', 'error' ],
				'parent'  => $this->entry_id,
				'form_id' => $this->form_data['id'],
			]
		);
	}
}
