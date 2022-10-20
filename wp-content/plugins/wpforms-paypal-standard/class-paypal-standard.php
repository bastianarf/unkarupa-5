<?php

/**
 * PayPal Standard integration.
 *
 * @since 1.0.0
 */
class WPForms_Paypal_Standard extends WPForms_Payment {

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->version  = WPFORMS_PAYPAL_STANDARD_VERSION;
		$this->name     = 'PayPal Standard';
		$this->slug     = 'paypal_standard';
		$this->priority = 10;
		$this->icon     = plugins_url( 'assets/images/addon-icon-paypal.png', __FILE__ );

		add_action( 'wpforms_process_complete', array( $this, 'process_entry' ), 20, 4 );
		add_action( 'init', array( $this, 'process_ipn' ) );
	}

	/**
	 * Display content inside the panel content area.
	 *
	 * @since 1.0.0
	 */
	public function builder_content() {

		/*
		if ( ! is_ssl() ) {
			// Don't need this quite yet.
			echo '<div class="wpforms-alert wpforms-alert-warning">' . esc_html__( 'For PayPal IPN verification to successfully complete, your site must have an SSL certificate.', 'wpforms-paypal-standard' ) . '</div>';
		}
		*/

		wpforms_panel_field(
			'checkbox',
			$this->slug,
			'enable',
			$this->form_data,
			esc_html__( 'Enable PayPal Standard payments', 'wpforms-paypal-standard' ),
			array(
				'parent'  => 'payments',
				'default' => '0',
			)
		);
		wpforms_panel_field(
			'text',
			$this->slug,
			'email',
			$this->form_data,
			esc_html__( 'PayPal Email Address', 'wpforms-paypal-standard' ),
			array(
				'parent'  => 'payments',
				'tooltip' => esc_html__( 'Enter your PayPal address for the payment to be sent', 'wpforms-paypal-standard' ),
			)
		);
		wpforms_panel_field(
			'select',
			$this->slug,
			'mode',
			$this->form_data,
			esc_html__( 'Mode', 'wpforms-paypal-standard' ),
			array(
				'parent'  => 'payments',
				'default' => 'production',
				'options' => array(
					'production' => esc_html__( 'Production', 'wpforms-paypal-standard' ),
					'test'       => esc_html__( 'Test / Sandbox', 'wpforms-paypal-standard' ),
				),
				'tooltip' => esc_html__( 'Select Production to receive real payments or select Test to use the PayPal developer sandbox', 'wpforms-paypal-standard' ),
			)
		);
		wpforms_panel_field(
			'select',
			$this->slug,
			'transaction',
			$this->form_data,
			esc_html__( 'Payment Type', 'wpforms-paypal-standard' ),
			array(
				'parent'  => 'payments',
				'default' => 'product',
				'options' => array(
					'product'  => esc_html__( 'Products and Services', 'wpforms-paypal-standard' ),
					'donation' => esc_html__( 'Donation', 'wpforms-paypal-standard' ),
				),
				'tooltip' => esc_html__( 'Select the type of payment you are receiving.', 'wpforms-paypal-standard' ),
			)
		);
		wpforms_panel_field(
			'text',
			$this->slug,
			'cancel_url',
			$this->form_data,
			esc_html__( 'Cancel URL', 'wpforms-paypal-standard' ),
			array(
				'parent'  => 'payments',
				'tooltip' => esc_html__( 'Enter the URL to send users to if they do not complete the PayPal checkout', 'wpforms-paypal-standard' ),
			)
		);
		wpforms_panel_field(
			'select',
			$this->slug,
			'shipping',
			$this->form_data,
			esc_html__( 'Shipping', 'wpforms-paypal-standard' ),
			array(
				'parent'  => 'payments',
				'default' => '0',
				'options' => array(
					'1' => esc_html__( 'Don\'t ask for an address', 'wpforms-paypal-standard' ),
					'0' => esc_html__( 'Ask for an address, but do not require', 'wpforms-paypal-standard' ),
					'2' => esc_html__( 'Ask for an address and require it', 'wpforms-paypal-standard' ),
				),
			)
		);
		wpforms_panel_field(
			'checkbox',
			$this->slug,
			'note',
			$this->form_data,
			esc_html__( 'Don\'t ask buyer to include a note with payment', 'wpforms-paypal-standard' ),
			array(
				'parent'  => 'payments',
				'default' => '1',
			)
		);

		if ( function_exists( 'wpforms_conditional_logic' ) ) {
			wpforms_conditional_logic()->conditionals_block(
				array(
					'form'        => $this->form_data,
					'type'        => 'panel',
					'panel'       => 'paypal_standard',
					'parent'      => 'payments',
					'actions'     => array(
						'go'   => esc_html__( 'Process', 'wpforms-paypal-standard' ),
						'stop' => esc_html__( 'Don\'t process', 'wpforms-paypal-standard' ),
					),
					'action_desc' => esc_html__( 'this charge if', 'wpforms-paypal-standard' ),
					'reference'   => esc_html__( 'PayPal Standard payment', 'wpforms-paypal-standard' ),
				)
			);
		} else {
			echo
				'<p class="note">' .
				sprintf(
					wp_kses(
						/* translators: %s - Addons page URL in admin area. */
						__( 'Install the <a href="%s">Conditional Logic addon</a> to enable conditional logic for PayPal Standard payments.', 'wpforms-paypal-standard' ),
						array(
							'a' => array(
								'href' => array(),
							),
						)
					),
					admin_url( 'admin.php?page=wpforms-addons' )
				) .
				'</p>';
		}
	}

	/**
	 * Process and submit entry to provider.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields
	 * @param array $entry
	 * @param array $form_data
	 * @param int   $entry_id
	 */
	public function process_entry( $fields, $entry, $form_data, $entry_id ) {

		// TODO: start actually using this variable or remove it.
		$error = false;

		// Check an entry was created and passed.
		if ( empty( $entry_id ) ) {
			return;
		}

		// Check if payment method exists.
		if ( empty( $form_data['payments'][ $this->slug ] ) ) {
			return;
		}

		// Check required payment settings.
		$payment_settings = $form_data['payments'][ $this->slug ];
		if (
			empty( $payment_settings['email'] ) ||
			empty( $payment_settings['enable'] ) ||
			$payment_settings['enable'] != '1'
		) {
			return;
		}

		// Check for conditional logic.
		if (
			! empty( $form_data['payments']['paypal_standard']['conditional_logic'] ) &&
			! empty( $form_data['payments']['paypal_standard']['conditional_type'] ) &&
			! empty( $form_data['payments']['paypal_standard']['conditionals'] ) &&
			function_exists( 'wpforms_conditional_logic' )
		) {

			// All conditional logic checks passed, continue with processing.
			$process = wpforms_conditional_logic()->conditionals_process( $fields, $form_data, $form_data['payments']['paypal_standard']['conditionals'] );

			if ( 'stop' === $form_data['payments']['paypal_standard']['conditional_type'] ) {
				$process = ! $process;
			}

			// If preventing the notification, log it, and then bail.
			if ( ! $process ) {
				wpforms_log(
					esc_html__( 'PayPal Standard Payment stopped by conditional logic', 'wpforms-paypal-standard' ),
					$fields,
					array(
						'parent'  => $entry_id,
						'type'    => array( 'payment', 'conditional_logic' ),
						'form_id' => $form_data['id'],
					)
				);

				return;
			}
		}

		// Check that, despite how the form is configured, the form and
		// entry actually contain payment fields, otherwise no need to proceed.
		$form_has_payments  = wpforms_has_payment( 'form', $form_data );
		$entry_has_paymemts = wpforms_has_payment( 'entry', $fields );
		if ( ! $form_has_payments || ! $entry_has_paymemts ) {
			$error = esc_html__( 'PayPal Standard Payment stopped, missing payment fields', 'wpforms-paypal-standard' );
		}

		// Check total charge amount.
		$amount = wpforms_get_total_payment( $fields );
		if ( empty( $amount ) || $amount == wpforms_sanitize_amount( 0 ) ) {
			$error = esc_html__( 'PayPal Standard Payment stopped, invalid/empty amount', 'wpforms-paypal-standard' );
		}

		if ( $error ) {
			return;
		}

		// Update entry to include payment details.
		$entry_data = array(
			'status' => 'pending',
			'type'   => 'payment',
			'meta'   => wp_json_encode(
				array(
					'payment_type'      => $this->slug,
					'payment_recipient' => trim( sanitize_email( $payment_settings['email'] ) ),
					'payment_total'     => $amount,
					'payment_currency'  => strtolower( wpforms_setting( 'currency', 'USD' ) ),
					'payment_mode'      => esc_html( $payment_settings['mode'] ),
				)
			),
		);
		wpforms()->entry->update( $entry_id, $entry_data, '', '', array( 'cap' => false ) );

		// Build the return URL with hash.
		$query_args  = 'form_id=' . $form_data['id'] . '&entry_id=' . $entry_id . '&hash=' . wp_hash( $form_data['id'] . ',' . $entry_id );
		$return_url  = is_ssl() ? 'https://' : 'http://';
		$return_url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		if ( ! empty( $form_data['settings']['ajax_submit'] ) && ! empty( $_SERVER['HTTP_REFERER'] ) ) {
			$return_url = $_SERVER['HTTP_REFERER'];
		}

		$return_url = esc_url_raw(
			add_query_arg(
				array(
					'wpforms_return' => base64_encode( $query_args ),
				),
				apply_filters( 'wpforms_paypal_return_url', $return_url, $form_data )
			)
		);

		// Setup various vars.
		$items       = wpforms_get_payment_items( $fields );
		$redirect    = 'production' === $payment_settings['mode'] ? 'https://www.paypal.com/cgi-bin/webscr/?' : 'https://www.sandbox.paypal.com/cgi-bin/webscr/?';
		$cancel_url  = ! empty( $payment_settings['cancel_url'] ) ? esc_url_raw( $payment_settings['cancel_url'] ) : home_url();
		$transaction = 'donation' === $payment_settings['transaction'] ? '_donations' : '_cart';

		// Setup PayPal arguments.
		$paypal_args = array(
			'bn'            => 'WPForms_SP',
			'business'      => trim( $payment_settings['email'] ),
			'cancel_return' => $cancel_url,
			'cbt'           => get_bloginfo( 'name' ),
			'charset'       => get_bloginfo( 'charset' ),
			'cmd'           => $transaction,
			'currency_code' => strtoupper( wpforms_setting( 'currency', 'USD' ) ),
			'custom'        => absint( $form_data['id'] ),
			'invoice'       => absint( $entry_id ),
			'no_note'       => (int) empty( $payment_settings['note'] ),
			'no_shipping'   => absint( $payment_settings['shipping'] ),
			'notify_url'    => add_query_arg( 'wpforms-listener', 'IPN', home_url( 'index.php' ) ),
			'return'        => $return_url,
			'rm'            => '2',
			'tax'           => 0,
			'upload'        => '1',
		);

		// Add cart items.
		if ( '_cart' === $transaction ) {

			// Product/service.
			$i = 1;
			foreach ( $items as $item ) {

				$item_amount = wpforms_sanitize_amount( $item['amount'] );

				if (
					! empty( $item['value_choice'] ) &&
					in_array( $item['type'], array( 'payment-multiple', 'payment-select', 'payment-checkbox' ), true )
				) {
					$item['value_choice'] = ( 'payment-checkbox' === $item['type'] ) ? str_replace( "\r\n", ', ', $item['value_choice'] ) : $item['value_choice'];
					$item_name            = $item['name'] . ' - ' . $item['value_choice'];
				} else {
					$item_name = $item['name'];
				}
				$paypal_args[ 'item_name_' . $i ] = stripslashes_deep( html_entity_decode( $item_name, ENT_COMPAT, 'UTF-8' ) );
				// Don't yet support quantities.
				//$paypal_args['quantity_' . $i ]  = $item['quantity'];
				$paypal_args[ 'amount_' . $i ] = $item_amount;
				$i ++;
			}
		} else {

			// Combine a donation name from all payment fields names.
			$item_names = array();

			foreach ( $items as $item ) {

				if (
					! empty( $item['value_choice'] ) &&
					in_array( $item['type'], array( 'payment-multiple', 'payment-select', 'payment-checkbox' ), true )
				) {
					$item['value_choice'] = ( 'payment-checkbox' === $item['type'] ) ? str_replace( "\r\n", ', ', $item['value_choice'] ) : $item['value_choice'];
					$item_name            = $item['name'] . ' - ' . $item['value_choice'];
				} else {
					$item_name = $item['name'];
				}

				$item_names[] = stripslashes_deep( html_entity_decode( $item_name, ENT_COMPAT, 'UTF-8' ) );
			}

			$paypal_args['item_name'] = implode( '; ', $item_names );
			$paypal_args['amount']    = $amount;
		}

		// Last change to filter args.
		$paypal_args = apply_filters( 'wpforms_paypal_redirect_args', $paypal_args, $fields, $form_data, $entry_id );

		// Build query.
		$redirect .= http_build_query( $paypal_args );
		$redirect  = str_replace( '&amp;', '&', $redirect );

		// Redirect to PayPal.
		wp_redirect( $redirect );
		exit;
	}

	/**
	 * Process PayPal IPN.
	 *
	 * Adapted from EDD and the PHP PayPal IPN Class.
	 *
	 * @link https://github.com/easydigitaldownloads/Easy-Digital-Downloads/blob/master/includes/gateways/paypal-standard.php
	 * @link https://github.com/WadeShuler/PHP-PayPal-IPN/blob/master/src/IpnListener.php
	 *
	 * @since 1.0.0
	 */
	public function process_ipn() {

		// Verify the call back query and its method.
		if (
			! isset( $_GET['wpforms-listener'] ) ||
			'IPN' !== $_GET['wpforms-listener'] ||
			(
				isset( $_SERVER['REQUEST_METHOD'] ) &&
				'POST' !== $_SERVER['REQUEST_METHOD']
			)
		) {
			return;
		}

		// Set initial post data to empty string.
		$post_data = '';

		// Fallback just in case post_max_size is lower than needed.
		if ( ini_get( 'allow_url_fopen' ) ) {
			$post_data = file_get_contents( 'php://input' );
		} else {
			// If allow_url_fopen is not enabled, then make sure that post_max_size is large enough.
			ini_set( 'post_max_size', '12M' );
		}

		// Start the encoded data collection with notification command.
		$encoded_data = 'cmd=_notify-validate';

		// Verify there is a post_data.
		if ( $post_data || strlen( $post_data ) > 0 ) {
			// Append the data.
			$encoded_data .= ini_get( 'arg_separator.output' ) . $post_data;
		} else {
			// Check if POST is empty.
			if ( empty( $_POST ) ) {
				// Nothing to do.
				return;
			}

			// Loop through each POST.
			foreach ( $_POST as $key => $value ) {
				// Encode the value and append the data.
				$encoded_data .= ini_get( 'arg_separator.output' ) . "$key=" . rawurlencode( $value );
			}
		}

		// Convert collected post data to an array.
		parse_str( $encoded_data, $data );

		foreach ( $data as $key => $value ) {
			if ( false !== strpos( $key, 'amp;' ) ) {
				$new_key = str_replace( array( '&amp;', 'amp;' ), '&', $key );
				unset( $data[ $key ] );
				$data[ $new_key ] = $value;
			}
		}

		// Check if $post_data_array has been populated.
		if ( ! is_array( $data ) || empty( $data ) || empty( $data['invoice'] ) ) {
			return;
		}

		// Get payment (entry).
		if ( empty( $data['invoice'] ) ) {
			return;
		}

		$error          = '';
		$payment_id     = absint( $data['invoice'] );
		$payment        = wpforms()->entry->get( absint( $payment_id ) );
		$payment_status = strtolower( $data['payment_status'] );
		$form_data      = wpforms()->form->get( $payment->form_id, array(
			'content_only' => true,
		) );

		// If payment or form doesn't exist, bail.
		if ( empty( $payment ) || empty( $form_data ) ) {
			return;
		}

		$payment_meta = json_decode( $payment->meta, true );

		// Verify IPN with PayPal unless specifically disabled.
		$remote_post_args = array(
			'method'      => 'POST',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.1',
			'blocking'    => true,
			'headers'     => array(
				'host'         => 'www.paypal.com',
				'connection'   => 'close',
				'content-type' => 'application/x-www-form-urlencoded',
				'post'         => '/cgi-bin/webscr HTTP/1.1',
				'user-agent'   => 'WPForms IPN Verification',
			),
			'body'        => $data,
		);
		$remote_post_url  = ! empty( $payment_meta['payment_mode'] ) && 'production' === $payment_meta['payment_mode'] ? 'https://ipnpb.paypal.com/cgi-bin/webscr' : 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
		$remote_post      = wp_remote_post( $remote_post_url, $remote_post_args );

		if ( is_wp_error( $remote_post ) || wp_remote_retrieve_body( $remote_post ) !== 'VERIFIED' ) {
			wpforms_log(
				esc_html__( 'PayPal Standard IPN Error', 'wpforms-paypal-standard' ),
				$remote_post,
				array(
					'parent'  => $payment_id,
					'type'    => array( 'error', 'payment' ),
					'form_id' => $payment->form_id,
				)
			);

			return;
		}

		// Verify transaction type.
		if ( 'web_accept' !== $data['txn_type'] && 'cart' !== $data['txn_type'] ) {
			return;
		}

		// Verify payment recipient emails match.
		if ( empty( $form_data['payments']['paypal_standard']['email'] ) || strtolower( $data['business'] ) !== strtolower( trim( $form_data['payments']['paypal_standard']['email'] ) ) ) {
			$error = esc_html__( 'Payment failed: recipient emails do not match', 'wpforms-paypal-standard' );

		// Verify payment currency.
		} elseif ( empty( $payment_meta['payment_currency'] ) || strtolower( $data['mc_currency'] ) !== strtolower( $payment_meta['payment_currency'] ) ) {
			$error = esc_html__( 'Payment failed: currency formats do not match', 'wpforms-paypal-standard' );

		// Verify payment amounts.
		} elseif ( empty( $payment_meta['payment_total'] ) || number_format( (float) $data['mc_gross'] ) !== number_format( (float) $payment_meta['payment_total'] ) ) {
			$error = esc_html__( 'Payment failed: payment amounts do not match', 'wpforms-paypal-standard' );
		}

		// If there was an error, log and update the payment status.
		if ( ! empty( $error ) ) {
			$payment_meta['payment_note'] = $error;
			wpforms_log(
				esc_html__( 'PayPal Standard IPN Error', 'wpforms-paypal-standard' ),
				sprintf( '%s - IPN data: %s', $error, '<pre>' . print_r( $data, true ) . '</pre>' ),
				array(
					'parent'  => $payment_id,
					'type'    => array( 'error', 'payment' ),
					'form_id' => $payment->form_id,
				)
			);
			wpforms()->entry->update(
				$payment_id,
				array(
					'status' => 'failed',
					'meta'   => wp_json_encode( $payment_meta ),
				),
				'',
				'',
				array( 'cap' => false )
			);

			return;
		}

		// Completed payment.
		if ( 'completed' === $payment_status || 'production' !== $payment_meta['payment_mode'] ) {

			$payment_meta['payment_transaction'] = $data['txn_id'];
			$payment_meta['payment_note']        = '';
			wpforms()->entry->update(
				$payment_id,
				array(
					'status' => 'completed',
					'meta'   => wp_json_encode( $payment_meta ),
				),
				'',
				'',
				array( 'cap' => false )
			);

		// Refunded payment.
		} elseif ( 'refunded' === $payment_status ) {
			/* translators: %s - Paypal payment transaction ID. */
			$payment_meta['payment_note'] = sprintf( esc_html__( 'Payment refunded: PayPal refund transaction ID: %s', 'wpforms-paypal-standard' ), $data['txn_id'] );
			wpforms()->entry->update(
				$payment_id,
				array(
					'status' => 'refunded',
					'meta'   => wp_json_encode( $payment_meta ),
				),
				'',
				'',
				array( 'cap' => false )
			);

		// Pending payment.
		} elseif ( 'pending' === $payment_status && isset( $data['pending_reason'] ) ) {

			switch ( strtolower( $data['pending_reason'] ) ) {
				case 'echeck':
					$note = esc_html__( 'Payment made via eCheck and will clear automatically in 5-8 days', 'wpforms-paypal-standard' );
					break;
				case 'address':
					$note = esc_html__( 'Payment requires a confirmed customer address and must be accepted manually through PayPal', 'wpforms-paypal-standard' );
					break;
				case 'intl':
					$note = esc_html__( 'Payment must be accepted manually through PayPal due to international account regulations', 'wpforms-paypal-standard' );
					break;
				case 'multi-currency':
					$note = esc_html__( 'Payment received in non-shop currency and must be accepted manually through PayPal', 'wpforms-paypal-standard' );
					break;
				case 'paymentreview':
				case 'regulatory_review':
					$note = esc_html__( 'Payment is being reviewed by PayPal staff as high-risk or in possible violation of government regulations', 'wpforms-paypal-standard' );
					break;
				case 'unilateral':
					$note = esc_html__( 'Payment was sent to non-confirmed or non-registered email address.', 'wpforms-paypal-standard' );
					break;
				case 'upgrade':
					$note = esc_html__( 'PayPal account must be upgraded before this payment can be accepted', 'wpforms-paypal-standard' );
					break;
				case 'verify':
					$note = esc_html__( 'PayPal account is not verified. Verify account in order to accept this payment', 'wpforms-paypal-standard' );
					break;
				case 'other':
					$note = esc_html__( 'Payment is pending for unknown reasons. Contact PayPal support for assistance', 'wpforms-paypal-standard' );
					break;
				default:
					$note = esc_html( $data['pending_reason'] );
					break;
			}

			$payment_meta['payment_note'] = $note;
			wpforms()->entry->update(
				$payment_id,
				array(
					'status' => 'pending',
					'meta'   => wp_json_encode( $payment_meta ),
				),
				'',
				'',
				array( 'cap' => false )
			);
		}

		// Completed PayPal IPN call.
		do_action( 'wpforms_paypal_standard_process_complete', wpforms_decode( $payment->fields ), $form_data, $payment_id, $data );

		exit;
	}
}

new WPForms_Paypal_Standard();
