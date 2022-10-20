<?php

/**
 * MailChimp integration.
 *
 * @since 1.1.0
 */
class WPForms_MailChimpv3 extends WPForms_Provider {

	/**
	 * Account ID for current account.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	public $account = false;

	/**
	 * Initialize.
	 *
	 * @since 1.1.0
	 */
	public function init() {

		$this->version  = WPFORMS_MAILCHIMP_VERSION;
		$this->name     = 'MailChimp';
		$this->slug     = 'mailchimpv3';
		$this->priority = 34;
		$this->icon     = WPFORMS_MAILCHIMP_URL . 'assets/images/addon-icon-mailchimp.png';

		add_action( 'admin_notices', array( $this, 'upgrade_notice' ) );
	}

	/**
	 * Display upgrade notice for sites using the v2 API integration.
	 *
	 * @since 1.1.0
	 */
	public function upgrade_notice() {

		// Only consider showing to admin users.
		if ( ! is_super_admin() ) {
			return;
		}

		// Only display if site has a v2 integration configured.
		$providers = get_option( 'wpforms_providers' );

		if ( empty( $providers['mailchimp'] ) ) {
			return;
		}

		?>
		<div class="notice notice-warning wpforms-mailchimp-update-notice">
			<p>
				<?php esc_html_e( 'Your forms are currently using an outdated MailChimp integration that is no longer supported. Please update your forms to use the new integration to avoid losing subscribers.', 'wpforms-mailchimp' ); ?>
				<strong>
					<a href="https://wpforms.com/new-announcing-an-important-mailchimp-addon-update/#update" target="_blank" rel="noopener noreferrer">
						<?php esc_html_e( 'Click here for more details.', 'wpforms-mailchimp' ); ?>
					</a>
				</strong>
			</p>
		</div>
		<?php
	}

	/**
	 * Process and submit entry to provider.
	 *
	 * @since 1.1.0
	 *
	 * @param array $fields WPForms form array of fields.
	 * @param array $entry
	 * @param array $form_data
	 * @param int $entry_id
	 *
	 * @throws \Exception If errors occurred.
	 */
	public function process_entry( $fields, $entry, $form_data, $entry_id = 0 ) {

		// Only run if this form has connections for this provider.
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
			$double     = isset( $connection['options']['doubleoptin'] );
			$api        = $this->api_connect( $account_id );
			$ip         = wpforms_get_ip();
			$data       = array(
				'email_type'    => 'html',
				'status'        => 'subscribed',
				'status_if_new' => $double ? 'pending' : 'subscribed',
				'ip_signup'     => $ip,
				'ip_opt'        => $ip,
			);

			// Bail if there is any sort of issues with the API connection.
			if ( is_wp_error( $api ) ) {
				continue;
			}

			// Email is required.
			if ( empty( $fields[ $email_id ]['value'] ) ) {
				continue;
			} else {
				$data['email_address'] = strtolower( $fields[ $email_id ]['value'] );
			}

			// Check for conditional logic.
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
			 * Setup merge fields.
			 */

			foreach ( $connection['fields'] as $name => $merge_field ) {

				// Don't include EMAIL merge fields.
				if ( 'EMAIL' === $name ) {
					continue;
				}

				// Check if merge vars are used.
				if ( empty( $merge_field ) ) {
					continue;
				}

				$merge_field = explode( '.', $merge_field );
				$id          = $merge_field[0]; // WPForms Field ID.
				$key         = ! empty( $merge_field[1] ) ? $merge_field[1] : 'value';
				$type        = ! empty( $merge_field[2] ) ? $merge_field[2] : 'text'; // MC merge field type.

				// Check if mapped form field has a value.
				if ( empty( $fields[ $id ][ $key ] ) ) {
					continue;
				}

				$value = $fields[ $id ][ $key ];

				// Special formatting for different types of data.
				switch ( $type ) {
					case 'phone':
						if ( ! empty( $form_data['fields'][ $id ]['format'] ) && 'us' === $form_data['fields'][ $id ]['format'] ) {
							$value = str_replace( ' ', '-', $value );
							$value = str_replace( '(', '', $value );
							$value = str_replace( ')', '', $value );
						}
						break;

					case 'birthday':
						if (
							! empty( $form_data['fields'][ $id ]['format'] ) &&
							'date' === $form_data['fields'][ $id ]['format']
						) {
							// To make use of all possible formats.
							$date  = DateTime::createFromFormat( $form_data['fields'][ $id ]['date_format'], $value );
							$value = $date->format( 'm/d' );

							if ( 'd/m/Y' === $form_data['fields'][ $id ]['date_format'] ) {
								$value = $date->format( 'd/m' );
							}
						}
						break;
				}

				$data['merge_fields'][ $name ] = $value;
			} // End foreach( $connection['fields'] ).

			/*
			 * Setup segments (groups).
			 */

			if ( ! empty( $connection['groups'] ) ) {

				$s = array();

				foreach ( $connection['groups'] as $id => $segments ) {
					foreach ( $segments as $id => $segment ) {
						$s[ $id ] = true;
					}
				}

				if ( ! empty( $s ) ) {
					$data['interests'] = $s;
				}
			}

			/*
			 * Send to API
			 */

			$hash = md5( $data['email_address'] ); // In order to both insert or update, we have to PUT to the specific resource.
			$res  = $this->api->put( 'lists/' . $list_id . '/members/' . $hash, $data );

			if ( false === $res || isset( $res['status'] ) && $res['status'] >= 300 ) {
				$error_msg = ! empty( $res['detail'] ) ? $res['detail'] : esc_html__( 'Error creating subscription.', 'wpforms-mailchimp' );
				wpforms_log(
					esc_html__( 'MailChimp Subscription error', 'wpforms-mailchimp' ),
					$error_msg,
					array(
						'type'    => array( 'provider', 'error' ),
						'parent'  => $entry_id,
						'form_id' => $form_data['id'],
					)
				);
			}

		endforeach; // End foreach( $form_data['providers'][ $this->slug ] ).
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

		if ( ! class_exists( 'MailChimp_WPForms' ) ) {
			require_once WPFORMS_MAILCHIMP_DIR . 'v3/vendor/MailChimp.php';
		}

		try {
			$api = new MailChimp_WPForms( trim( $data['apikey'] ) );
		} catch ( Exception $e ) {
			return $this->error( $e->getMessage() );
		}

		$res = $api->get( '' );

		if ( empty( $res['account_id'] ) ) {

			$details = ! empty( $res['detail'] ) ? $res['detail'] : esc_html__( 'Could not verify API key', 'wpforms-mailchimp' );

			wpforms_log(
				esc_html__( 'MailChimp API error', 'wpforms-mailchimp' ),
				$res,
				array(
					'type' => array( 'provider', 'error' ),
				)
			);
			/* translators: %s - error details. */
			return $this->error( sprintf( esc_html__( 'API auth error: %s', 'wpforms-mailchimp' ), $details ) );
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
	 * @since 1.1.0
	 *
	 * @param string $account_id
	 *
	 * @return mixed array or error object.
	 * @throws \Exception In case error occurred.
	 */
	public function api_connect( $account_id ) {

		if ( ! class_exists( 'MailChimp_WPForms' ) ) {
			require_once WPFORMS_MAILCHIMP_DIR . 'v3/vendor/MailChimp.php';
		}

		if ( ! empty( $this->api ) && $account_id === $this->account ) {
			return $this->api;
		} else {
			$providers = get_option( 'wpforms_providers' );
			if ( ! empty( $providers[ $this->slug ][ $account_id ]['api'] ) ) {
				$this->account = $account_id;
				$this->api     = new MailChimp_WPForms( $providers[ $this->slug ][ $account_id ]['api'] );

				return $this->api;
			} else {
				return $this->error( esc_html__( 'API connect error', 'wpforms-mailchimp' ) );
			}
		}
	}

	/**
	 * Retrieve provider account lists.
	 *
	 * @since 1.1.0
	 *
	 * @param string $connection_id
	 * @param string $account_id
	 *
	 * @return mixed array or error object.
	 * @throws \Exception In case error occurred.
	 */
	public function api_lists( $connection_id = '', $account_id = '' ) {

		$this->api_connect( $account_id );

		try {

			$lists = $this->api->get( 'lists', array(
				'count'  => 500,
				'fields' => 'lists.id,lists.name',
			) );

			if ( ! empty( $lists['lists'] ) ) {
				$l = array();
				foreach ( $lists['lists'] as $list ) {
					if ( empty( $list['id'] ) ) {
						continue;
					}
					$l[ $list['id'] ] = array(
						'id'   => $list['id'],
						'name' => isset( $list['name'] ) ? trim( $list['name'] ) : esc_html__( 'Unknown List', 'wpforms-mailchimp' ),
					);
				}

				return $l;
			} else {
				$error_msg = esc_html__( 'API list error: No lists', 'wpforms-mailchimp' );

				return $this->error( $error_msg );
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
			return $this->error( sprintf( esc_html__( 'API list error: %s', 'wpforms-mailchimp' ), $e->getMessage() ) );
		}
	}

	/**
	 * Retrieve provider account list segments (groups).
	 *
	 * @since 1.1.0
	 *
	 * @param string $connection_id
	 * @param string $account_id
	 * @param string $list_id
	 *
	 * @return mixed array or error object
	 * @throws \Exception In case error occurred.
	 */
	public function api_groups( $connection_id = '', $account_id = '', $list_id = '' ) {

		$this->api_connect( $account_id );

		try {

			$segments = $this->api->get( 'lists/' . $list_id . '/interest-categories', array(
				'count'  => 500,
				'fields' => 'categories.id,categories.title',
			) );

			if ( ! empty( $segments['categories'] ) ) {
				$s = array();
				foreach ( $segments['categories'] as $segment ) {
					if ( empty( $segment['id'] ) ) {
						continue;
					}
					$s[ $segment['id'] ] = array(
						'id'   => $segment['id'],
						'name' => isset( $segment['title'] ) ? trim( $segment['title'] ) : esc_html__( 'Unknown Segment', 'wpforms-mailchimp' ),
					);
					// Grab groups within the segment headings (now called Interest Categories).
					$groups = $this->api->get( 'lists/' . $list_id . '/interest-categories/' . $segment['id'] . '/interests', array(
						'count'  => 500,
						'fields' => 'interests.id,interests.name',
					) );
					if ( ! empty( $groups['interests'] ) ) {
						$s[ $segment['id'] ]['groups'] = array();
						foreach ( $groups['interests'] as $i => $group ) {
							if ( empty( $group['id'] ) ) {
								continue;
							}
							$s[ $segment['id'] ]['groups'][] = array(
								'id'   => $group['id'],
								'name' => isset( $group['name'] ) ? trim( $group['name'] ) : esc_html__( 'Unknown Group', 'wpforms-mailchimp' ),
							);
						}
					}
				}

				return $s;
			} else {
				$error_msg = esc_html__( 'API groups error: No groups', 'wpforms-mailchimp' );

				return $this->error( $error_msg );
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
			return $this->error( sprintf( esc_html__( 'API groups error: %s', 'wpforms-mailchimp' ), $e->getMessage() ) );
		} // End try().
	}

	/**
	 * Retrieve provider account list fields.
	 *
	 * @since 1.1.0
	 *
	 * @param string $connection_id
	 * @param string $account_id
	 * @param string $list_id
	 *
	 * @return mixed array or error object
	 * @throws \Exception In case error occurred.
	 */
	public function api_fields( $connection_id = '', $account_id = '', $list_id = '' ) {

		$this->api_connect( $account_id );

		try {
			$fields = $this->api->get( 'lists/' . $list_id . '/merge-fields', array(
				'count' => 500,
			) );

			// Add email.
			$f[0] = array(
				'id'         => 0,
				'name'       => esc_html__( 'Email Address', 'wpforms-mailchimp' ),
				'req'        => true,
				'field_type' => 'email',
				'tag'        => 'EMAIL',
			);

			if ( ! empty( $fields['merge_fields'] ) ) {
				foreach ( $fields['merge_fields'] as $field ) {
					$f[ $field['merge_id'] ] = array(
						'id'         => $field['merge_id'],
						'name'       => isset( $field['name'] ) ? trim( $field['name'] ) : esc_html__( 'Unknown Merge Var', 'wpforms-mailchimp' ),
						'req'        => $field['required'] ? '1' : '',
						'field_type' => $field['type'],
						'tag'        => $field['tag'],
					);
				}
			}

			return $f;
		} catch ( Exception $e ) {

			wpforms_log(
				esc_html__( 'MailChimp API error', 'wpforms' ),
				$e->getMessage(),
				array(
					'type' => array( 'provider', 'error' ),
				)
			);
			/* translators: %s - error message. */
			return $this->error( sprintf( esc_html__( 'API fields error: %s', 'wpforms-mailchimp' ), $e->getMessage() ) );
		} // End try().
	}

	/*************************************************************************
	 * Output methods - these methods generally return HTML for the builder. *
	 *************************************************************************/

	/**
	 * Provider account authorize fields HTML.
	 *
	 * @since 1.1.0
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
	 * @since 1.1.0
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

		$output .= '<h4>' . esc_html__( 'Options', 'wpforms-mailchimp' ) . '</h4>';

		$output .= sprintf(
			'<p><input id="%s_options_doubleoptin" type="checkbox" value="1" name="providers[%s][%s][options][doubleoptin]" %s><label for="%s_options_doubleoptin">%s</label></p>',
			esc_attr( $connection_id ),
			esc_attr( $this->slug ),
			esc_attr( $connection_id ),
			checked( ! empty( $connection['options']['doubleoptin'] ), true, false ),
			esc_attr( $connection_id ),
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
	 * @since 1.1.0
	 */
	public function integrations_tab_new_form() {

		/* translators: %s - provider name. */
		echo '<input type="text" name="apikey" placeholder="' . sprintf( esc_attr__( '%s API Key', 'wpforms-mailchimp' ), $this->name ) . '">';
		/* translators: %s - provider name. */
		echo '<input type="text" name="label" placeholder="' . sprintf( esc_attr__( '%s Account Nickname', 'wpforms-mailchimp' ), $this->name ) . '">';

	}
}

new WPForms_MailChimpv3;
