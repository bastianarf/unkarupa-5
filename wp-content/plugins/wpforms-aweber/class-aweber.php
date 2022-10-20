<?php
/**
 * AWeber integration.
 *
 * @since 1.0.0
 * @package WPFormsAWeber
 */
class WPForms_Aweber extends WPForms_Provider {

	/**
	 * Provider access token.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $access_token;

	/**
	 * Provider access token secret.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $access_token_secret;

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->version  = WPFORMS_AWEBER_VERSION;
		$this->name     = 'AWeber';
		$this->slug     = 'aweber';
		$this->priority = 10;
		$this->icon     = plugins_url( 'assets/images/addon-icon-aweber.png', __FILE__ );
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

		// Fire for each connection. --------------------------------------//

		foreach ( $form_data['providers'][ $this->slug ] as $connection ) :

			// Before proceeding make sure required fields are configured.
			if ( empty( $connection['fields']['Email'] ) ) {
				continue;
			}

			// Setup basic data.
			$full_name  = '';
			$account_id = $connection['account_id'];
			$list_id    = $connection['list_id'];
			$email_data = explode( '.', $connection['fields']['Email'] );
			$email_id   = $email_data[0];
			$email      = $fields[ $email_id ]['value'];
			$data       = array();
			$api        = $this->api_connect( $account_id );

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
					'AWeber Subscription stopped by conditional logic',
					$fields,
					array(
						'type'    => array( 'provider', 'conditional_logic' ),
						'parent'  => $entry_id,
						'form_id' => $form_data['id'],
					)
				);
				continue;
			}

			// Setup the merge vars. --------------------------------------//

			foreach ( $connection['fields'] as $name => $merge_var ) {

				// Don't include Email or Full name fields.
				if ( 'Email' === $name ) {
					continue;
				}

				// Check if merge var is mapped.
				if ( empty( $merge_var ) ) {
					continue;
				}

				$merge_var = explode( '.', $merge_var );
				$id        = $merge_var[0];
				$key       = ! empty( $merge_var[1] ) ? $merge_var[1] : 'value';

				// Check if mapped form field has a value.
				if ( empty( $fields[ $id ][ $key ] ) ) {
					continue;
				}

				$value = $fields[ $id ][ $key ];

				// Omit the Full Name from custom fields.
				if ( 'Full Name' === $name ) {
					$full_name = $value;
					continue;
				}

				$data[ $name ] = $value;
			}

			$params = array(
				'email' => $email,
				'name'  => $full_name,
			);

			if ( ! empty( $data ) ) {
				$params['custom_fields'] = $data;
			}

			// Tags.
			if ( ! empty( $connection['options']['tags'] ) ) {
				$params['tags'] = wp_json_encode( array_map( 'trim', explode( ',', $connection['options']['tags'] ) ) );
			}

			// Submit to API. ---------------------------------------------//
			// https://labs.aweber.com/snippets/subscribers
			try {
				$account     = $this->api[ $account_id ]->getAccount( $this->access_token, $this->access_token_secret );
				$list        = $account->loadFromUrl( "/accounts/{$account->id}/lists/{$list_id}" );
				$subscribers = $list->subscribers;
				$subscribers->create( $params );
			} catch ( AWeberAPIException $e ) {
				wpforms_log(
					'AWeber Subscription error',
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

		if ( ! class_exists( 'AWeberAPI' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'vendor/aweber_api/aweber_api.php';
		}

		list( $consumer_key, $consumer_secret, $access_key, $access_secret, $oauth ) = explode( '|', $data['authcode'] );

		// Connect.
		$api                     = new AWeberAPI( $consumer_key, $consumer_secret );
		$api->user->requestToken = $access_key;
		$api->user->tokenSecret  = $access_secret;
		$api->user->verifier     = $oauth;

		// Retrieve an access token.
		try {
			list( $access_token, $access_token_secret ) = $api->getAccessToken();
		} catch ( AWeberException $e ) {
			wpforms_log(
				'AWeber API error',
				$e->getMessage(),
				array(
					'type'    => array( 'provider', 'error' ),
					'form_id' => $form_id['id'],
				)
			);

			return $this->error( 'API authorization error: ' . $e->getMessage() );
		}

		// Verify we can connect to AWeber.
		try {
			$account = $api->getAccount();
		} catch ( AWeberException $e ) {
			wpforms_log(
				'AWeber API error',
				$e->getMessage(),
				array(
					'type'    => array( 'provider', 'error' ),
					'form_id' => $form_id['id'],
				)
			);

			return $this->error( 'API authorization error: ' . $e->getMessage() );
		}

		$id                              = uniqid();
		$providers                       = get_option( 'wpforms_providers', array() );
		$providers[ $this->slug ][ $id ] = array(
			'consumer_key'        => $consumer_key,
			'consumer_secret'     => $consumer_secret,
			'access_token'        => $access_token,
			'access_token_secret' => $access_token_secret,
			'label'               => sanitize_text_field( $data['label'] ),
			'date'                => time(),
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
	 * @return mixed Array or WP_Error object.
	 */
	public function api_connect( $account_id ) {

		if ( ! class_exists( 'AWeberAPI' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'vendor/aweber_api/aweber_api.php';
		}

		if ( ! empty( $this->api[ $account_id ] ) ) {
			return $this->api[ $account_id ];
		} else {
			$providers = get_option( 'wpforms_providers' );
			if ( ! empty( $providers[ $this->slug ][ $account_id ] ) ) {
				$this->api[ $account_id ]  = new AWeberAPI( $providers[ $this->slug ][ $account_id ]['consumer_key'], $providers[ $this->slug ][ $account_id ]['consumer_secret'] );
				$this->access_token        = $providers[ $this->slug ][ $account_id ]['access_token'];
				$this->access_token_secret = $providers[ $this->slug ][ $account_id ]['access_token_secret'];
			} else {
				return $this->error( 'API error' );
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
	 * @return mixed Array or WP_Error object.
	 */
	public function api_lists( $connection_id = '', $account_id = '' ) {

		$this->api_connect( $account_id );

		try {
			$account = $this->api[ $account_id ]->getAccount( $this->access_token, $this->access_token_secret );
			if ( ! empty( $account->lists->data['entries'] ) ) {
				return $account->lists->data['entries'];
			} else {
				return $this->error( 'API list error: No lists found' );
			}
		} catch ( AWeberAPIException $e ) {
			wpforms_log(
				'AWeber API error',
				$e->getMessage(),
				array(
					'type' => array( 'provider', 'error' ),
				)
			);

			return $this->error( 'API list error: ' . $e->getMessage() );
		}
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
	 * @return mixed Array or WP_Error object.
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
				'name'       => 'Full Name',
				'field_type' => 'text',
				'tag'        => 'Full Name',
			),
		);

		$account = $this->api[ $account_id ]->getAccount( $this->access_token, $this->access_token_secret );
		$fields  = $account->loadFromUrl( "/accounts/{$account->id}/lists/{$list_id}/custom_fields" );

		if ( ! empty( $fields->data['entries'] ) ) {
			foreach ( $fields->data['entries'] as $field ) {
				$provider_fields[ $field['name'] ] = array(
					'name'       => $field['name'],
					'field_type' => 'text',
					'tag'        => $field['name'],
				);
			}
		}

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

		$output .= '<h4>' . esc_html__( 'Add New Account', 'wpforms-aweber' ) . '</h4>';

		$output .= '<p>';
		$output .= esc_html__( 'AWeber requires external authentication.', 'wpforms-aweber' );
		$output .= sprintf(
			' <a onclick="window.open(this.href,\'\',\'resizable=yes,location=no,width=730,height=450,status\'); return false" href="https://auth.aweber.com/1.0/oauth/authorize_app/e4ddf1e4">%s</a>',
			esc_html__( 'Click here to authorize.', 'wpforms-aweber' )
		);
		$output .= '</p>';

		$output .= sprintf(
			'<input type="text" data-name="authcode" placeholder="%s" class="wpforms-required">',
			sprintf(
				/* translators: %s - current provider name. */
				esc_html__( '%s Authorization Code', 'wpforms-aweber' ),
				$this->name
			)
		);

		$output .= sprintf(
			'<input type="text" data-name="label" placeholder="%s" class="wpforms-required">',
			sprintf(
				/* translators: %s - current provider name. */
				esc_html__( '%s Account Nickname', 'wpforms-aweber' ),
				$this->name
			)
		);

		$output .= sprintf( '<button data-provider="%s">%s</button>', esc_attr( $this->slug ), esc_html__( 'Connect', 'wpforms-aweber' ) );

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

		if ( empty( $connection_id ) || empty( $connection['account_id'] ) || empty( $connection['list_id'] ) ) {
			return '';
		}

		$output = '<div class="wpforms-provider-options wpforms-connection-block">';

		$output .= '<h4>' . esc_html__( 'Options', 'wpforms-aweber' ) . '</h4>';

		$output .= sprintf(
			'<p>
				<label for="%s_options_tags" class="block">%s <i class="fa fa-question-circle wpforms-help-tooltip" title="%s"></i></label>
				<input id="%s_options_tags" type="text" name="providers[%s][%s][options][tags]" value="%s">
			</p>',
			esc_attr( $connection_id ),
			esc_html__( 'Lead Tags', 'wpforms-aweber' ),
			esc_html__( 'Comma-separated list of tags to assign to a new lead in AWeber.', 'wpforms-aweber' ),
			esc_attr( $connection_id ),
			esc_attr( $this->slug ),
			esc_attr( $connection_id ),
			! empty( $connection['options']['tags'] ) ? esc_attr( $connection['options']['tags'] ) : ''
		);

		$output .= '</div>';

		return $output;
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

		echo '<p>';
		esc_html_e( 'AWeber requires external authentication.', 'wpforms-aweber' );
		printf(
			' <a onclick="window.open(this.href,\'\',\'resizable=yes,location=no,width=730,height=450,status\'); return false" href="https://auth.aweber.com/1.0/oauth/authorize_app/e4ddf1e4">%s</a>',
			esc_html__( 'Click here to authorize.', 'wpforms' )
		);
		echo '</p>';

		printf(
			'<input type="text" name="authcode" placeholder="%s">',
			sprintf(
				/* translators: %s - current provider name. */
				esc_html__( '%s Authorization Code', 'wpforms-aweber' ),
				$this->name
			)
		);

		printf(
			'<input type="text" name="label" placeholder="%s">',
			sprintf(
				/* translators: %s - current provider name. */
				esc_html__( '%s Account Nickname', 'wpforms-aweber' ),
				$this->name
			)
		);
	}
}

new WPForms_Aweber;
