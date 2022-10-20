<?php

namespace WPFormsDrip\Provider;

/**
 * Class Process handles entries processing using the provider settings and configuration.
 *
 * @since 1.0.0
 */
class Process extends \WPForms\Providers\Provider\Process {

	/**
	 * Receive all wpforms_process_complete params and do the actual processing.
	 *
	 * @since 1.0.0
	 * @since 1.4.0 Add `eu_consent` and `eu_consent_message` fields to subscriber data.
	 *
	 * @param array   $fields    Fields data.
	 * @param array   $entry     Entry data.
	 * @param array   $form_data Form data.
	 * @param integer $entry_id  Entry ID.
	 */
	public function process( $fields, $entry, $form_data, $entry_id ) {

		$this->fields    = $fields;
		$this->entry     = $entry;
		$this->form_data = $form_data;
		$this->entry_id  = $entry_id;

		// Only run if this form has connections for this provider.
		if ( empty( $form_data['providers'][ $this->core->slug ] ) ) {
			return;
		}

		$field_ids = array_flip( wp_list_pluck( $fields, 'type', 'id' ) );

		// Determine GDPR-related status.
		$gdpr_enabled = wpforms_setting( 'gdpr', false ) && ! empty( $field_ids['gdpr-checkbox'] );
		$gdpr_granted = $gdpr_enabled && ! empty( $fields[ $field_ids['gdpr-checkbox'] ]['value'] );

		/*
		 * Fire for each form connection.
		 */
		$options = $this->get_options();

		foreach ( $form_data['providers'][ $this->core->slug ] as $connection_id => $connection ) :

			// Email, API Token and account_id are required. We can't make requests to Drip without them.
			if (
				empty( $connection['fields']['email'] ) ||
				empty( $options[ $connection['option_id'] ]['api_token'] ) ||
				empty( $connection['account_id'] )
			) {
				continue;
			}

			// Make sure that we have an email.
			$email = '';
			if (
				isset( $connection['fields']['email'] ) &&
				isset( $fields[ $connection['fields']['email'] ] ) &&
				isset( $fields[ $connection['fields']['email'] ]['value'] )
			) {
				$email = \strtolower( $fields[ $connection['fields']['email'] ]['value'] );
			}
			if ( empty( $email ) ) {
				continue;
			}

			// Check for conditional logic.
			$pass = $this->process_conditionals( $this->fields, $this->form_data, $connection );
			if ( ! $pass ) {
				wpforms_log(
					esc_html__( 'Form to Drip processing stopped by conditional logic.', 'wpforms-drip' ),
					$fields,
					array(
						'type'    => array( 'provider', 'conditional_logic' ),
						'parent'  => $this->entry_id,
						'form_id' => $this->form_data['id'],
					)
				);
				continue;
			}

			$drip = new Drip( $options[ $connection['option_id'] ]['api_token'], $connection['account_id'] );

			/*
			 * Properties or custom fields.
			 * User can overwrite those defaults.
			 */
			$properties = [
				'form_id'         => (int) $form_data['id'],
				'entry_id'        => (int) $entry_id,
				'connection_name' => $connection['name'],
			];

			if ( ! empty( $connection['fields_meta'] ) ) {
				foreach ( $connection['fields_meta'] as $property ) {
					if (
						empty( $property['name'] ) ||
						( empty( $property['field_id'] ) && 0 !== $property['field_id'] ) ||
						empty( $fields[ $property['field_id'] ] )
					) {
						continue;
					}

					$property_name = wpforms_drip()->sanitize_field_id( $property['name'] );

					$properties[ $property_name ] = $fields[ $property['field_id'] ]['value'];
				}
			}

			switch ( $connection['action'] ) {
				// EVENT.
				case 'event':
					// Event name is required.
					if ( empty( $connection['events']['name'] ) ) {
						continue 2;
					}

					/*
					 * Finally, send an event to Drip.
					 */
					$r = $drip->post( 'events', [
						'events' => [
							[
								'email'      => $email,
								'action'     => $connection['events']['name'],
								'prospect'   => isset( $connection['prospect'] ) ? (bool) $connection['prospect'] : false,
								'properties' => $properties,
							],
						],
					] );

					$this->log_errors( $r, $connection );

					break;

				// SUBSCRIBER: DELETE.
				case 'subscriber_delete':
					$r = $drip->delete( 'subscribers/' . $email );

					$this->log_errors( $r, $connection );

					break;

				// SUBSCRIBER: CREATE / UPDATE.
				case 'subscriber_subscribe':
					$saved_entry = \wpforms()->entry->get( (int) $entry_id );

					$new_email = '';
					if (
						isset( $connection['fields']['new_email'] ) &&
						isset( $fields[ $connection['fields']['new_email'] ] ) &&
						isset( $fields[ $connection['fields']['new_email'] ]['value'] )
					) {
						$new_email = \strtolower( $fields[ $connection['fields']['new_email'] ]['value'] );
					}

					$subscribers = [
						[
							'email'           => $email,
							'new_email'       => $new_email,
							'user_id'         => isset( $connection['user_id'] ) && ! empty( $saved_entry->user_id ) ? (int) $saved_entry->user_id : '',
							'ip_address'      => isset( $connection['ip_address'] ) && ! empty( $saved_entry->ip_address ) ? $saved_entry->ip_address : '',
							'tags'            => $connection['tags']['add'],
							'remove_tags'     => $connection['tags']['delete'],
							'prospect'        => isset( $connection['prospect'] ),
							'base_lead_score' => isset( $connection['base_lead_score'] ) ? (int) $connection['base_lead_score'] : 30,
							'custom_fields'   => $properties,
						],
					];

					// Add GDPR-related data.
					if ( $gdpr_enabled ) {
						$subscribers[0]['eu_consent']         = $gdpr_granted ? 'granted' : 'denied';
						$subscribers[0]['eu_consent_message'] = $fields[ $field_ids['gdpr-checkbox'] ]['value'];
					}

					$r = $drip->post( 'subscribers', [ 'subscribers' => $subscribers ] );

					$this->log_errors( $r, $connection );

					break;

				// CAMPAIGN: SUBSCRIBE.
				case 'campaign_sub':
					// campaign_id is required.
					if ( empty( $connection['campaign_id'] ) ) {
						continue 2;
					}

					$saved_entry = \wpforms()->entry->get( (int) $entry_id );

					$r = $drip->post( "campaigns/{$connection['campaign_id']}/subscribers", [
						'subscribers' => [
							[
								'email'           => $email,
								'user_id'         => isset( $connection['user_id'] ) && ! empty( $saved_entry->user_id ) ? (int) $saved_entry->user_id : '',
								'custom_fields'   => $properties,
								'tags'            => $connection['tags']['add'],
								'prospect'        => isset( $connection['prospect'] ),
								'base_lead_score' => isset( $connection['base_lead_score'] ) ? (int) $connection['base_lead_score'] : 30,
							],
						],
					] );

					$this->log_errors( $r, $connection );

					break;

				// CAMPAIGN: UNSUBSCRIBE.
				case 'campaign_unsub':
					$args = [];

					if ( ! empty( $connection['campaign_id'] ) ) {
						if ( 'all' !== $connection['campaign_id'] ) {
							$args['campaign_id'] = $connection['campaign_id'];
						}
					} else {
						continue 2;
					}

					$r = $drip->post( "subscribers/{$email}/remove", $args );

					$this->log_errors( $r, $connection );

					break;
			}

		endforeach;
	}

	/**
	 * Log error if any.
	 *
	 * @since 1.0.0
	 *
	 * @param \DrewM\Drip\Response $response   An object with various errors by Drip.
	 * @param array                $connection Specific connection data that errored.
	 */
	protected function log_errors( $response, $connection ) {

		if ( ! isset( $response->errors ) ) {
			return;
		}

		wpforms_log(
			esc_html__( 'Submission to Drip failed.', 'wpforms-drip' ) . "(#{$this->entry_id})",
			[
				'response'   => $response,
				'connection' => $connection,
				'drip'       => $response->errors,
			],
			[
				'type'    => array( 'provider', 'error' ),
				'parent'  => $this->entry_id,
				'form_id' => $this->form_data['id'],
			]
		);
	}
}
