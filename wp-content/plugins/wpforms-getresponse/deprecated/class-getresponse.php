<?php

/**
 * GetResponse integration.
 *
 * @since 1.0.0
 */
class WPForms_GetResponse extends WPForms_Provider {

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->version  = WPFORMS_GETRESPONSE_VERSION;
		$this->name     = 'GetResponse (Legacy)';
		$this->slug     = 'getresponse';
		$this->priority = 22;
		$this->icon     = WPFORMS_GETRESPONSE_URL . 'assets/images/addon-icon-getresponse.png';
	}

	/**
	 * Process and submit entry to provider.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields
	 * @param array $entry
	 * @param array $form_data
	 * @param int $entry_id
	 */
	public function process_entry( $fields, $entry, $form_data, $entry_id = 0 ) {

		// Only run if this form has a connections for this provider.
		if ( empty( $form_data['providers'][ $this->slug ] ) ) {
			return;
		}

		/*
		 * Fire for each connection.
		 */

		foreach ( $form_data['providers'][ $this->slug ] as $connection ) :

			// Before proceeding make sure required fields are configured.
			if ( empty( $connection['fields']['Email'] ) ) {
				continue;
			}

			// Setup basic data.
			$account_id = $connection['account_id'];
			$list_id    = $connection['list_id'];
			$email_data = explode( '.', $connection['fields']['Email'] );
			$email_id   = $email_data[0];
			$email      = $fields[ $email_id ]['value'];
			$data       = array();
			$api        = $this->api_connect( $account_id );
			$name       = '';

			// Bail if there is any sort of issues with the API connection.
			if ( is_wp_error( $api ) ) {
				continue;
			}

			// Email is required.
			if ( empty( $email ) ) {
				continue;
			}

			// Check for conditionals.
			$pass = $this->process_conditionals( $fields, $entry, $form_data, $connection );
			if ( ! $pass ) {
				wpforms_log(
					'GetResponse Subscription stopped by conditional logic',
					$fields,
					array(
						'type'    => array( 'provider', 'conditional_logic' ),
						'parent'  => $entry_id,
						'form_id' => $form_data['id'],
					)
				);
				continue;
			}

			// Setup Name if configured and provided.
			if ( ! empty( $connection['fields']['Name'] ) ) {
				$name_data = explode( '.', $connection['fields']['Name'] );
				$name_id   = $name_data[0];
				$name      = $fields[ $name_id ]['value'];
				if ( ! empty( $name ) ) {
					$data['name'] = $name;
				}
			}
			$name = ! empty( $name ) ? $name : '';

			// Submit to API: http://apidocs.getresponse.com/en/api/1.5.0.
			try {
				$res = $this->api[ $account_id ]->addContact(
					$list_id,
					$name,
					$email
				);
			} catch ( Exception $e ) {
				wpforms_log(
					'GetResponse Subscription error',
					$e->getMessage(),
					array(
						'type'    => array( 'provider', 'error' ),
						'parent'  => $entry_id,
						'form_id' => $form_data['id'],
					)
				);
			}

		endforeach;
	}

	/************************************************************************
	 * API methods - these methods interact directly with the provider API. *
	 ************************************************************************/

	/**
	 * Authenticate with the API.
	 *
	 * @param array $data
	 * @param string $form_id
	 *
	 * @return mixed id or error object
	 */
	public function api_auth( $data = array(), $form_id = '' ) {

		if ( ! class_exists( 'GetResponse' ) ) {
			require_once __DIR__ . '/vendor/GetResponseAPI.class.php';
		}

		$api  = new GetResponse( trim( $data['apikey'] ) );
		$ping = false;

		try {
			$ping = $api->ping();
		} catch ( Exception $e ) {
			wpforms_log(
				'GetResponse API error',
				$e->getMessage(),
				array(
					'type'    => array( 'provider', 'error' ),
					'form_id' => $form_id,
				)
			);
		}

		if ( ! $ping ) {
			return $this->error( esc_html__( 'API authorization error: Error connecting to GetResponse API', 'wpforms-getresponse' ) );
		}

		$id        = uniqid();
		$providers = get_option( 'wpforms_providers', array() );

		$providers[ $this->slug ][ $id ] = array(
			'api'   => trim( $data['apikey'] ),
			'label' => sanitize_text_field( $data['label'] ),
			'date'  => time(),
		);
		update_option( 'wpforms_providers', $providers );

		return $id;
	}

	/**
	 * Establish connection object to API.
	 *
	 * @since 1.0.0
	 *
	 * @param string $account_id
	 *
	 * @return mixed array or error object
	 */
	public function api_connect( $account_id ) {

		if ( ! class_exists( 'GetResponse' ) ) {
			require_once __DIR__ . '/vendor/GetResponseAPI.class.php';
		}

		if ( ! empty( $this->api[ $account_id ] ) ) {
			return $this->api[ $account_id ];
		} else {
			$providers = get_option( 'wpforms_providers' );
			if ( ! empty( $providers[ $this->slug ][ $account_id ]['api'] ) ) {
				$this->api[ $account_id ] = new GetResponse( $providers[ $this->slug ][ $account_id ]['api'] );

				return $this->api[ $account_id ];
			} else {
				return $this->error( esc_html__( 'API error: GetResponse API error', 'wpforms-getresponse' ) );
			}
		}
	}

	/**
	 * Retrieve provider account lists.
	 *
	 * @since 1.0.0
	 *
	 * @param string $connection_id
	 * @param string $account_id
	 *
	 * @return mixed array or error object
	 */
	public function api_lists( $connection_id = '', $account_id = '' ) {

		$this->api_connect( $account_id );

		$lists = $this->api[ $account_id ]->getCampaigns();

		if ( ! $lists ) {
			wpforms_log(
				'GetResponse API error',
				'',
				array(
					'type' => array( 'provider', 'error' ),
				)
			);

			return $this->error( esc_html__( 'API list error: GetResponse API error', 'wpforms-getresponse' ) );
		}

		$lists = wpforms_object_to_array( $lists );

		foreach ( $lists as $key => $list ) {
			$lists[ $key ]['id'] = $key;
		}

		return $lists;
	}

	/**
	 * Retrieve provider account list fields.
	 *
	 * @since 1.0.0
	 *
	 * @param string $connection_id
	 * @param string $account_id
	 * @param string $list_id
	 *
	 * @return mixed array or error object
	 */
	public function api_fields( $connection_id = '', $account_id = '', $list_id = '' ) {

		$this->api_connect( $account_id );

		$provider_fields = array(
			array(
				'name'       => 'Email',
				'field_type' => 'email',
				'req'        => '1',
				'tag'        => 'Email',
			),
			array(
				'name'       => 'Name',
				'field_type' => 'text',
				'tag'        => 'Name',
			),
		);

		return $provider_fields;
	}


	/*************************************************************************
	 * Output methods - these methods generally return HTML for the builder. *
	 *************************************************************************/

	/**
	 * Provider account authorize fields HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function output_auth() {

		$providers = get_option( 'wpforms_providers' );
		$class     = ! empty( $providers[ $this->slug ] ) ? 'hidden' : '';

		$output = '<div class="wpforms-provider-account-add ' . $class . ' wpforms-connection-block">';

		$output .= '<h4>' . esc_html__( 'Add New Account', 'wpforms-getresponse' ) . '</h4>';

		/* translators: %s - provider name. */
		$output .= '<input type="text" data-name="apikey" placeholder="' . sprintf( esc_attr__( '%s API Key', 'wpforms-getresponse' ), $this->name ) . '" class="wpforms-required">';
		/* translators: %s - provider name. */
		$output .= '<input type="text" data-name="label" placeholder="' . sprintf( esc_attr__( '%s Account Nickname', 'wpforms-getresponse' ), $this->name ) . '" class="wpforms-required">';

		$output .= '<button data-provider="' . esc_attr( $this->slug ) . '">' . esc_html__( 'Connect', 'wpforms-getresponse' ) . '</button>';

		$output .= '</div>';

		return $output;
	}

	/**
	 * Provider account list groups HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param string $connection_id
	 * @param array $connection
	 *
	 * @return string
	 */
	public function output_groups( $connection_id = '', $connection = array() ) {
		// No groups or segments for this provider.
		return '';
	}

	/**
	 * Provider account list options HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param string $connection_id
	 * @param array $connection
	 *
	 * @return string
	 */
	public function output_options( $connection_id = '', $connection = array() ) {
		// No options for this provider.
		return '';
	}

	/*************************************************************************
	 * Integrations tab methods - these methods relate to the settings page. *
	 *************************************************************************/

	/**
	 * Form fields to add a new provider account.
	 *
	 * @since 1.0.0
	 */
	public function integrations_tab_new_form() {

		/* translators: %s - provider name. */
		echo '<input type="text" name="apikey" placeholder="' . sprintf( esc_attr__( '%s API Key', 'wpforms-getresponse' ), $this->name ) . '">';
		/* translators: %s - provider name. */
		echo '<input type="text" name="label" placeholder="' . sprintf( esc_attr__( '%s Account Nickname', 'wpforms-getresponse' ), $this->name ) . '">';
	}
}

new WPForms_GetResponse;
