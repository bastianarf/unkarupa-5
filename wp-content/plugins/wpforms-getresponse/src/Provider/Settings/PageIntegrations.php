<?php

namespace WPFormsGetResponse\Provider\Settings;

use WPFormsGetResponse\Provider\Api;
use WPForms\Providers\Provider\Settings\PageIntegrations as PageIntegrationsAbstract;

/**
 * Class PageIntegrations handles functionality inside the Settings > Integrations page.
 *
 * @since 1.3.0
 */
class PageIntegrations extends PageIntegrationsAbstract {

	/**
	 * AJAX to add a provider from the Settings > Integrations tab.
	 *
	 * @since 1.3.0
	 */
	public function ajax_connect() {

		parent::ajax_connect();

		$creds = wp_parse_args( wp_unslash( $_POST['data'] ), [ 'apikey' => '' ] ); // phpcs:ignore WordPress.Security

		if ( empty( $creds['apikey'] ) ) {
			wp_send_json_error( [ 'error_msg' => esc_html__( 'Please provide a valid API Key.', 'wpforms-getresponse' ) ] );
		}

		$api_key = trim( $creds['apikey'] );

		if ( $this->is_already_exist( $api_key ) ) {
			wp_send_json_error( [ 'error_msg' => esc_html__( 'Account with these credentials has already been added.', 'wpforms-getresponse' ) ] );
		}

		// API call.
		try {
			$api = new Api( $api_key );
		} catch ( \Exception $e ) {
			wp_send_json_error( [ 'error_msg' => $e->getMessage() ] );
		}

		// Retrieve a response data.
		$response = $api->get_account();
		if ( ! $response->isSuccess() ) {
			wp_send_json_error( [ 'error_msg' => esc_html( $response->getErrorMessage() ) ] );
		}

		// Retrieve an account data.
		$account = $response->getData();
		if (
			empty( $account ) ||
			$response->getResponse()->getStatusCode() !== 200
		) {
			wp_send_json_error( [ 'error_msg' => esc_html__( 'GetResponse API error: response is empty or an unexpected status code.', 'wpforms-getresponse' ) ] );
		}

		// Success.
		wp_send_json_success( [ 'html' => $this->prepare_result_html_list( $creds, $account ) ] );
	}

	/**
	 * Check if account with those credentials already exists.
	 *
	 * @since 1.3.0
	 *
	 * @param string $api_key API key for check.
	 *
	 * @return bool True if account already exists, false otherwise.
	 */
	protected function is_already_exist( $api_key ) {

		$keys = array_column( $this->core->get_provider_options(), 'apikey' );

		return in_array( $api_key, $keys, true );
	}

	/**
	 * Prepare a HTML for a new account.
	 *
	 * @since 1.3.0
	 *
	 * @param array $creds   Array with user credentials.
	 * @param array $account Account data.
	 *
	 * @return string
	 */
	protected function prepare_result_html_list( $creds, $account ) {

		$option_key = uniqid( '', true );
		$label      = ! empty( $creds['label'] ) ? sanitize_text_field( $creds['label'] ) : sanitize_email( $account['email'] );
		$date       = time();

		// Save this account.
		wpforms_update_providers_options(
			$this->core->slug,
			[
				'apikey' => $creds['apikey'],
				'label'  => $label,
				'date'   => $date,
			],
			$option_key
		);

		return wpforms_render(
			WPFORMS_GETRESPONSE_PATH . 'templates/settings/connected-account',
			[
				'key'           => $option_key,
				'label'         => $label,
				'date'          => $date,
				'provider_slug' => $this->core->slug,
			]
		);
	}

	/**
	 * Display fields that will store GetResponse account details.
	 *
	 * @since 1.3.0
	 */
	protected function display_add_new_connection_fields() {

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wpforms_render(
			WPFORMS_GETRESPONSE_PATH . 'templates/settings/new-account-connection',
			[ 'provider_name' => $this->core->name ]
		);
	}
}
