<?php

namespace WPFormsDrip\Provider;

/**
 * Class PageIntegrations handles functionality inside the Settings > Integrations page.
 *
 * @since 1.0.0
 */
class PageIntegrations extends \WPForms\Providers\Provider\Settings\PageIntegrations {

	/**
	 * @inheritdoc
	 *
	 * @since 1.0.0
	 */
	public function ajax_connect() {

		if ( parent::ajax_connect() === false ) {
			return;
		}

		$data = wp_parse_args( $_POST['data'], [] ); //phpcs:ignore

		$data['account_name'] = sanitize_text_field( $data['account_name'] );
		$data['api_token']    = sanitize_key( $data['api_token'] );

		$drip = new Drip( $data['api_token'] );

		try {
			$drip_data = $drip->makeRawRequest( 'get', 'user' );

			if ( empty( $drip_data->users[0] ) ) {
				throw new \Exception(
					esc_html__( 'Error requesting a Drip user info. Make sure API token is correct.', 'wpforms-drip' )
				);
			}

			$label = $data['account_name'];
			if ( empty( $label ) ) {
				$label = ! empty( $drip_data->users[0]['name'] ) ? sanitize_text_field( $drip_data->users[0]['name'] ) : sanitize_text_field( $drip_data->users[0]['email'] );
			}
			$key = uniqid();

			// Save this connection for the provider.
			wpforms_update_providers_options( $this->core->slug, array(
				'api_token' => $data['api_token'],
				'label'     => $label,
				'date'      => time(),
			), $key );

			$list  = '<li class="wpforms-clear">';
			$list .= '<span class="label">' . \esc_html( $label ) . '</span>';
			/* translators: %s - Connection date. */
			$list .= '<span class="date">' . \sprintf( \esc_html__( 'Connected on: %s', 'wpforms' ), \date_i18n( \get_option( 'date_format', \time() ) ) ) . '</span>';
			$list .= '<span class="remove"><a href="#" data-provider="' . $this->core->slug . '" data-key="' . $key . '">' . \esc_html__( 'Disconnect', 'wpforms' ) . '</a></span>';
			$list .= '</li>';

			\wp_send_json_success(
				array(
					'html' => $list,
				)
			);
		} catch ( \Exception $e ) {
			wp_send_json_error(
				array(
					'error' => $e->getMessage(),
				)
			);
		}
	}

	/**
	 * Display fields that will store Drip account details.
	 *
	 * @since 1.0.0
	 */
	protected function display_add_new_connection_fields() {
		echo Core::get_add_new_account_fields_html(); // phpcs:ignore
	}
}
