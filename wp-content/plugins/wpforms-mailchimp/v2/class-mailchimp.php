<?php

/**
 * MailChimp integration (Legacy v2 API).
 *
 * @since 1.0.0
 */
class WPForms_Mailchimp extends WPForms_Provider {

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->version  = WPFORMS_MAILCHIMP_VERSION;
		$this->name     = 'MailChimp (Legacy)';
		$this->slug     = 'mailchimp';
		$this->priority = 34;
		$this->icon     = WPFORMS_MAILCHIMP_URL . 'assets/images/addon-icon-mailchimp.png';
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
	 *
	 * @throws \Mailchimp_Error_WPF If error occurred.
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
			if ( empty( $connection['fields']['EMAIL'] ) ) {
				continue;
			}

			// Setup basic data.
			$account_id = $connection['account_id'];
			$list_id    = $connection['list_id'];
			$email_data = explode( '.', $connection['fields']['EMAIL'] );
			$email_id   = $email_data[0];
			$email      = $fields[ $email_id ]['value'];
			$double     = isset( $connection['options']['doubleoptin'] );
			$welcome    = isset( $connection['options']['welcome'] );
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
					esc_html__( 'MailChimp Subscription stopped by conditional logic', 'wpforms-mailchimp' ),
					$fields,
					array(
						'type'    => array( 'provider', 'conditional_logic' ),
						'parent'  => $entry_id,
						'form_id' => $form_data['id'],
					)
				);
				continue;
			}

			/*
			 * Setup the merge vars.
			 */

			foreach ( $connection['fields'] as $name => $merge_var ) {

				// Don't include EMAIL merge var.
				if ( 'EMAIL' === $name ) {
					continue;
				}

				// Check if merge var is mapped.
				if ( empty( $merge_var ) ) {
					continue;
				}

				$merge_var = explode( '.', $merge_var );
				$id        = $merge_var[0];
				$key       = ! empty( $merge_var[1] ) ? $merge_var[1] : 'value';
				$type      = ! empty( $merge_var[2] ) ? $merge_var[2] : 'text';

				// Check if mapped form field has a value.
				if ( empty( $fields[ $id ][ $key ] ) ) {
					continue;
				}

				$value = $fields[ $id ][ $key ];

				// Special formatting for US phone.
				if ( 'phone' === $type && ! empty( $form_data['fields'][ $id ]['format'] ) && 'us' === $form_data['fields'][ $id ]['format'] ) {
					$value = str_replace( ' ', '-', $value );
					$value = str_replace( '(', '', $value );
					$value = str_replace( ')', '', $value );
				}

				$data[ $name ] = $value;
			}

			/*
			 * Setup groups.
			 */

			if ( ! empty( $connection['groups'] ) ) {

				$provider_groups = array();

				foreach ( $connection['groups'] as $id => $groups ) {
					$provider_groups[] = array(
						'id'     => $id,
						'groups' => $groups,
					);
				}

				if ( ! empty( $provider_groups ) ) {
					$data['groupings'] = $provider_groups;
				}
			}

			// Submit to API: https://apidocs.mailchimp.com/api/2.0/lists/subscribe.php.
			try {
				$this->api[ $account_id ]->lists->subscribe(
					$list_id,
					array(
						'email' => $email,
					),
					$data,
					'html',
					$double,
					true,
					true,
					$welcome
				);

			} catch ( Mailchimp_List_AlreadySubscribed_WPF $e ) {
				// Good!
			} catch ( Mailchimp_Error_WPF $e ) {
				wpforms_log(
					esc_html__( 'MailChimp Subscription error', 'wpforms-mailchimp' ),
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
	 * @throws \Mailchimp_Error_WPF If error occurred.
	 */
	public function api_auth( $data = array(), $form_id = '' ) {

		if ( ! class_exists( 'Mailchimp_WPF' ) ) {
			require_once WPFORMS_MAILCHIMP_DIR . 'v2/vendor/Mailchimp.php';
		}

		$api = new Mailchimp_WPF( trim( $data['apikey'] ) );

		try {
			$api->helper->ping();
		} catch ( Mailchimp_Invalid_ApiKey_WPF $e ) {
			wpforms_log(
				'MailChimp API error',
				$e->getMessage(),
				array(
					'type'    => array( 'provider', 'error' ),
					'form_id' => absint( $form_id ),
				)
			);
			/* translators: %s - error details. */
			return $this->error( sprintf( esc_html__( 'API auth error: %s', 'wpforms-mailchimp' ), $e->getMessage() ) );
		}

		$id                              = uniqid();
		$providers                       = get_option( 'wpforms_providers', array() );
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
	 * @throws \Mailchimp_Error_WPF If error occurred.
	 */
	public function api_connect( $account_id ) {

		if ( ! class_exists( 'Mailchimp_WPF' ) ) {
			require_once WPFORMS_MAILCHIMP_DIR . 'v2/vendor/Mailchimp.php';
		}

		if ( ! empty( $this->api[ $account_id ] ) ) {
			return $this->api[ $account_id ];
		} else {
			$providers = get_option( 'wpforms_providers' );
			if ( ! empty( $providers[ $this->slug ][ $account_id ]['api'] ) ) {
				$this->api[ $account_id ] = new Mailchimp_WPF( $providers[ $this->slug ][ $account_id ]['api'] );

				return $this->api[ $account_id ];
			} else {
				return $this->error( esc_html__( 'API connect error', 'wpforms-mailchimp' ) );
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
	 * @throws \Mailchimp_Error_WPF If error occurred.
	 */
	public function api_lists( $connection_id = '', $account_id = '' ) {

		$this->api_connect( $account_id );

		try {
			$lists = $this->api[ $account_id ]->lists->getList( array(), 0, 100 );

			return $lists['data'];
		} catch ( Exception $e ) {
			wpforms_log(
				esc_html__( 'MailChimp API error', 'wpforms-mailchimp' ),
				$e->getMessage(),
				array(
					'type' => array( 'provider', 'error' ),
				)
			);

			/* translators: %s - error message. */
			return $this->error( sprintf( esc_html__( 'API list error: %s', 'wpforms-mailchimp' ), $e->getMessage() ) );
		}
	}

	/**
	 * Retrieve provider account list groups.
	 *
	 * @since 1.0.0
	 *
	 * @param string $connection_id
	 * @param string $account_id
	 * @param string $list_id
	 *
	 * @return mixed array or error object
	 * @throws \Mailchimp_Error_WPF If error occurred.
	 */
	public function api_groups( $connection_id = '', $account_id = '', $list_id = '' ) {

		$this->api_connect( $account_id );

		try {
			return $this->api[ $account_id ]->lists->interestGroupings( $list_id );
		} catch ( Exception $e ) {
			wpforms_log(
				'MailChimp API error',
				$e->getMessage(),
				array(
					'type' => array( 'provider', 'error' ),
				)
			);

			/* translators: %s - error message. */
			return $this->error( sprintf( esc_html__( 'API groups error: %s', 'wpforms-mailchimp' ), $e->getMessage() ) );
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
	 * @return mixed array or error object
	 * @throws \Mailchimp_Error_WPF If error occurred.
	 */
	public function api_fields( $connection_id = '', $account_id = '', $list_id = '' ) {

		$this->api_connect( $account_id );

		try {
			$provider_fields = $this->api[ $account_id ]->lists->mergeVars( array( $list_id ) );
			if ( ! empty( $provider_fields['data'][0]['merge_vars'] ) ) {
				return $provider_fields['data'][0]['merge_vars'];
			} else {
				return $this->error( esc_html__( 'API fields error: No fields', 'wpforms-mailchimp' ) );
			}
		} catch ( Exception $e ) {
			wpforms_log(
				esc_html__( 'MailChimp API error', 'wpforms-mailchimp' ),
				$e->getMessage(),
				array(
					'type' => array( 'provider', 'error' ),
				)
			);

			/* translators: %s - error message. */
			return $this->error( sprintf( esc_html__( 'API fields error: %s', 'wpforms-mailchimp' ), $e->getMessage() ) );
		}
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

		$output .= '<h4>' . esc_html__( 'Add New Account', 'wpforms-mailchimp' ) . '</h4>';

		/* translators: %s - provider name. */
		$output .= '<input type="text" data-name="apikey" placeholder="' . sprintf( esc_attr__( '%s API Key', 'wpforms-mailchimp' ), $this->name ) . '" class="wpforms-required">';
		/* translators: %s - provider name. */
		$output .= '<input type="text" data-name="label" placeholder="' . sprintf( esc_attr__( '%s Account Nickname', 'wpforms-mailchimp' ), $this->name ) . '" class="wpforms-required">';

		$output .= '<button data-provider="' . esc_attr( $this->slug ) . '">' . esc_html__( 'Connect', 'wpforms-mailchimp' ) . '</button>';

		$output .= '</div>';

		return $output;
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

		$output .= sprintf( '<h4>%s</h4>', esc_html__( 'Options', 'wpforms-mailchimp' ) );

		$output .= sprintf(
			'<p><input id="%s_options_welcome" type="checkbox" value="1" name="providers[%s][%s][options][welcome]" %s><label for="%s_options_welcome">%s</label></p>',
			$connection_id,
			$this->slug,
			$connection_id,
			checked( ! empty( $connection['options']['welcome'] ), true, false ),
			$connection_id,
			esc_html__( 'Send welcome email', 'wpforms-mailchimp' )
		);

		$output .= sprintf(
			'<p><input id="%s_options_doubleoptin" type="checkbox" value="1" name="providers[%s][%s][options][doubleoptin]" %s><label for="%s_options_doubleoptin">%s</label></p>',
			$connection_id,
			$this->slug,
			$connection_id,
			checked( ! empty( $connection['options']['doubleoptin'] ), true, false ),
			$connection_id,
			esc_html__( 'Use double opt-in', 'wpforms-mailchimp' )
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

		/* translators: %s - provider name. */
		echo '<input type="text" name="apikey" placeholder="' . sprintf( esc_attr__( '%s API Key', 'wpforms-mailchimp' ), $this->name ) . '">';
		/* translators: %s - provider name. */
		echo '<input type="text" name="label" placeholder="' . sprintf( esc_attr__( '%s Account Nickname', 'wpforms-mailchimp' ), $this->name ) . '">';
	}
}

new WPForms_Mailchimp;
