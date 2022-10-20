<?php

namespace WPFormsGetResponse\Provider\Settings;

use Exception;
use WPFormsGetResponse\Provider\Api;
use WPFormsGetResponse\Provider\Connection;
use WPForms\Providers\Provider\Settings\FormBuilder as FormBuilderAbstract;

/**
 * Class FormBuilder handles functionality inside the Form Builder.
 *
 * @since 1.3.0
 */
class FormBuilder extends FormBuilderAbstract {

	/**
	 * Register all hooks (actions and filters).
	 *
	 * @since 1.3.0
	 */
	protected function init_hooks() {

		parent::init_hooks();

		// AJAX-event names.
		static $ajax_events = [
			'ajax_account_save',
			'ajax_account_template_get',
			'ajax_connections_get',
			'ajax_accounts_get',
			'ajax_subscribe_data_get',
		];

		// Register callbacks for AJAX events.
		array_walk(
			$ajax_events,
			static function( $ajax_event, $key, $instance ) {

				add_filter(
					"wpforms_providers_settings_builder_{$ajax_event}_{$instance->core->slug}",
					[ $instance, $ajax_event ]
				);
			},
			$this
		);

		// Register callbacks for hooks.
		add_filter( 'wpforms_save_form_args', [ $this, 'save_form' ], 11, 3 );
	}

	/**
	 * Pre-process provider data before saving it in form_data when editing a form.
	 *
	 * @since 1.3.0
	 *
	 * @param array $form Form array which is usable with `wp_update_post()`.
	 * @param array $data Data retrieved from $_POST and processed.
	 * @param array $args Empty by default, may have custom data not intended to be saved, but used for processing.
	 *
	 * @return array
	 */
	public function save_form( $form, $data, $args ) {

		// Get a filtered (or modified by another addon) form content.
		$form_data = json_decode( stripslashes( $form['post_content'] ), true );

		// Provider exists.
		if ( ! empty( $form_data['providers'][ $this->core->slug ] ) ) {
			$modified_post_content = $this->modify_form_data( $form_data );

			if ( ! empty( $modified_post_content ) ) {
				$form['post_content'] = wpforms_encode( $modified_post_content );

				return $form;
			}
		}

		/*
		 * This part works when modification is locked or current filter was called on NOT Providers panel.
		 * Then we need to restore provider connections from the previous form content.
		 */

		// Get a "previous" form content (current content are still not saved).
		$prev_form = wpforms()->form->get( $data['id'], [ 'content_only' => true ] );

		if ( ! empty( $prev_form['providers'][ $this->core->slug ] ) ) {
			$provider = $prev_form['providers'][ $this->core->slug ];

			if ( ! isset( $form_data['providers'] ) ) {
				$form_data = array_merge( $form_data, [ 'providers' => [] ] );
			}

			$form_data['providers'] = array_merge( (array) $form_data['providers'], [ $this->core->slug => $provider ] );
			$form['post_content']   = wpforms_encode( $form_data );
		}

		return $form;
	}

	/**
	 * Prepare modifications for form content, if it's not locked.
	 *
	 * @since 1.3.0
	 *
	 * @param array $form_data Form content.
	 *
	 * @return array|null
	 */
	protected function modify_form_data( $form_data ) {

		/**
		 * Connection is locked.
		 * Why? User clicked the "Save" button when one of the AJAX requests
		 * for data retrieval from API was in progress or failed.
		 */
		if (
			isset( $form_data['providers'][ $this->core->slug ]['__lock__'] ) &&
			absint( $form_data['providers'][ $this->core->slug ]['__lock__'] ) === 1
		) {
			return null;
		}

		// Modify content as we need, done by reference.
		foreach ( $form_data['providers'][ $this->core->slug ] as $connection_id => &$connection ) {

			if ( $connection_id === '__lock__' ) {
				unset( $form_data['providers'][ $this->core->slug ]['__lock__'] );
				continue;
			}

			try {
				$connection = ( new Connection( $connection ) )->get_data();
			} catch ( Exception $e ) {
				continue;
			}
		}
		unset( $connection );

		return $form_data;
	}

	/**
	 * Save the data for a new account and validate it.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 * @throws \Getresponse\Sdk\Client\Exception\MalformedResponseDataException When JSON is invalid.
	 */
	public function ajax_account_save() {

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( empty( $_POST['apikey'] ) ) {
			return [ 'error' => esc_html__( 'Please provide a valid API Key.', 'wpforms-getresponse' ) ];
		}

		$data = wp_unslash( $_POST );
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		$api_key = trim( $data['apikey'] );

		// API call.
		try {
			$api = new Api( $api_key );
		} catch ( Exception $e ) {
			return [
				'error' => $e->getMessage(),
			];
		}

		// Request error.
		$response = $api->get_account();
		if ( ! $response->isSuccess() ) {
			return [ 'error' => esc_html( $response->getErrorMessage() ) ];
		}

		// Response error.
		$account = $response->getData();
		if (
			empty( $account ) ||
			200 !== $response->getResponse()->getStatusCode()
		) {
			return [ 'error' => esc_html__( 'GetResponse API error: response is empty or unexpected status code.', 'wpforms-getresponse' ) ];
		}

		$label        = ! empty( $data['label'] ) ? sanitize_text_field( $data['label'] ) : sanitize_email( $account['email'] );
		$option_key   = uniqid( '', true );
		$option_value = [
			'apikey' => $api_key,
			'label'  => $label,
			'date'   => time(),
		];

		// Save this account.
		wpforms_update_providers_options( $this->core->slug, $option_value, $option_key );

		// Update a cache.
		$cache = get_transient( 'wpforms_providers_' . $this->core->slug . '_ajax_accounts_get' );

		if ( empty( $cache ) ) {
			$cache = [ 'accounts' => [] ];
		}

		$cache['accounts'][ $option_key ] = $label;
		set_transient( 'wpforms_providers_' . $this->core->slug . '_ajax_accounts_get', $cache, 12 * HOUR_IN_SECONDS );

		return $option_value;
	}

	/**
	 * Content for the "Add New Account" modal.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	public function ajax_account_template_get() {

		$content = wpforms_render(
			WPFORMS_GETRESPONSE_PATH . 'templates/settings/new-account-connection',
			[ 'provider_name' => $this->core->name ]
		);

		return [
			'title'   => esc_html__( 'New GetResponse Account', 'wpforms-getresponse' ),
			'content' => $content,
			'type'    => 'blue',
		];
	}

	/**
	 * Get the list of all saved connections.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	public function ajax_connections_get() {

		$connections = [
			'connections'  => ! empty( $this->get_connections_data() ) ? array_reverse( $this->get_connections_data(), true ) : [],
			'conditionals' => [],
		];

		// Get conditional logic for each connection_id.
		foreach ( $connections['connections'] as $connection ) {

			if ( empty( $connection['id'] ) ) {
				continue;
			}

			// This will either return an empty placeholder or complete set of rules, as a DOM.
			$connections['conditionals'][ $connection['id'] ] = wpforms_conditional_logic()->builder_block(
				[
					'form'       => $this->form_data,
					'type'       => 'panel',
					'parent'     => 'providers',
					'panel'      => $this->core->slug,
					'subsection' => $connection['id'],
					'reference'  => esc_html__( 'Marketing provider connection', 'wpforms-getresponse' ),
				],
				false
			);
		}

		// Get accounts as well.
		$accounts = $this->ajax_accounts_get();

		return array_merge( $connections, $accounts );
	}

	/**
	 * Get the list of all accounts for all API keys that might have been saved.
	 *
	 * @since 1.3.0
	 *
	 * @return array May return an empty sub-array.
	 */
	public function ajax_accounts_get() {

		// Check a cache.
		$cache = get_transient( 'wpforms_providers_' . $this->core->slug . '_ajax_accounts_get' );

		// Retrieve accounts from cache.
		if ( is_array( $cache ) && isset( $cache['accounts'] ) ) {
			return $cache;
		}

		// If no cache - preparing to make real external requests.
		$data             = [];
		$data['accounts'] = $this->get_accounts_data();

		// Save accounts to cache.
		if ( ! empty( $data['accounts'] ) ) {
			set_transient( 'wpforms_providers_' . $this->core->slug . '_ajax_accounts_get', $data, 12 * HOUR_IN_SECONDS );
		}

		return $data;
	}

	/**
	 * Retrieve saved provider connections data.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	public function get_connections_data() {

		return isset( $this->form_data['providers'][ $this->core->slug ] ) ? $this->form_data['providers'][ $this->core->slug ] : [];
	}

	/**
	 * Retrieve saved provider accounts data.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 * @throws \Getresponse\Sdk\Client\Exception\MalformedResponseDataException When JSON in response is invalid.
	 */
	public function get_accounts_data() {

		$providers       = wpforms_get_providers_options();
		$update_required = false;
		$accounts        = [];

		// We might have several different API keys.
		foreach ( $this->core->get_provider_options() as $option_id => $option ) {

			$apikey = ! empty( $option['apikey'] ) ? $option['apikey'] : '';

			// API call.
			try {
				$api = new Api( $apikey );
			} catch ( Exception $e ) {
				continue;
			}

			$response = $api->get_account();

			// Code error 401 - Unauthorized.
			// If API key expired (free trial) this status (without any data) will return for all API calls.
			// That's why we remove it from saved accounts.
			if ( $response->getResponse()->getStatusCode() === 401 ) {
				unset( $providers[ $this->core->slug ][ $option_id ] );
				$update_required = true;
				continue;
			}

			$account = $response->getData();
			if (
				! $response->isSuccess() ||
				$response->getResponse()->getStatusCode() !== 200 ||
				empty( $account )
			) {
				continue;
			}

			$accounts[ $option_id ] = $option['label'];
		}

		// Re-save provider accounts.
		if ( $update_required ) {
			update_option( 'wpforms_providers', $providers );
		}

		return $accounts;
	}

	/**
	 * Retrieve a GetResponse data (lists, tags, custom fields), that is needed for subscribing process.
	 *
	 * @since 1.3.0
	 *
	 * @return array|null Return null on any kind of error. Array of data otherwise.
	 * @throws \Exception When something goes wrong.
	 */
	public function ajax_subscribe_data_get() {

		$options = $this->core->get_provider_options();

		// phpcs:disable
		if (
			empty( $options ) ||
			empty( $_POST['account_id'] ) ||
			empty( $_POST['connection_id'] ) ||
			empty( $_POST['sources'] )
		) {
			return null;
		}

		$connection_id = sanitize_text_field( wp_unslash( $_POST['connection_id'] ) );
		$account_id    = sanitize_text_field( wp_unslash( $_POST['account_id'] ) );
		$sources       = array_map( 'wp_validate_boolean', wp_unslash( $_POST['sources'] ) );
		// phpcs:enable

		if ( empty( $options[ $account_id ]['apikey'] ) ) {
			return null;
		}

		// API call.
		$api_client = new Api( $options[ $account_id ]['apikey'] );

		// Retrieve lists.
		if ( isset( $sources['lists'] ) ) {
			$sources['lists'] = array_column( $api_client->get_data_lists(), 'name', 'campaignId' );
		}

		// Retrieve tags.
		if ( isset( $sources['tags'] ) ) {
			$connections     = $this->get_connections_data();
			$connection_tags = ! empty( $connections[ $connection_id ]['tag_names'] ) ? $connections[ $connection_id ]['tag_names'] : [];
			$sources['tags'] = array_unique(
				array_merge(
					array_column( $api_client->get_data_tags(), 'name', 'tagId' ),
					(array) $connection_tags
				)
			);
		}

		// Retrieve custom fields.
		if ( isset( $sources['customFields'] ) ) {
			$sources['customFields'] = array_column( $api_client->get_data_custom_fields(), 'name', 'customFieldId' );
		}

		return $sources;
	}

	/**
	 * Display a generated field with all markup for email selection.
	 * Used internally in templates.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	protected function get_fields_html() {

		return [
			'email' => wpforms_panel_field(
				'select',
				$this->core->slug,
				'email',
				$this->form_data,
				esc_html__( 'Subscriber Email', 'wpforms-getresponse' ),
				[
					'parent'        => 'providers',
					'field_name'    => 'providers[' . $this->core->slug . '][%connection_id%][fields][email]',
					'field_map'     => [ 'email' ],
					'placeholder'   => esc_html__( '--- Select Email Field ---', 'wpforms-getresponse' ),
					'after_tooltip' => '<span class="required">*</span>',
					'input_class'   => 'wpforms-required',
					'input_id'      => 'wpforms-panel-field-' . $this->core->slug . '-%connection_id%-email',
				],
				false
			),
			'name'  => wpforms_panel_field(
				'select',
				$this->core->slug,
				'name',
				$this->form_data,
				esc_html__( 'Subscriber Name', 'wpforms-getresponse' ),
				[
					'parent'      => 'providers',
					'field_name'  => 'providers[' . $this->core->slug . '][%connection_id%][fields][name]',
					'field_map'   => [ 'name', 'text' ],
					'placeholder' => esc_html__( '--- Select Name Field ---', 'wpforms-getresponse' ),
					'input_id'    => 'wpforms-panel-field-' . $this->core->slug . '-%connection_id%-name',
				],
				false
			),
		];
	}

	/**
	 * Use this method to register own templates for form builder.
	 * Make sure, that you have `tmpl-` in template name in `<script id="tmpl-*">`.
	 *
	 * @since 1.3.0
	 */
	public function builder_custom_templates() { ?>

		<!-- Single GR connection. -->
		<script type="text/html" id="tmpl-wpforms-<?php echo esc_attr( $this->core->slug ); ?>-builder-content-connection">
			<?php $this->print_underscore_template( 'connection' ); ?>
		</script>

		<!-- Single GR connection block: SUBSCRIBER - CREATE / UPDATE. -->
		<script type="text/html" id="tmpl-wpforms-<?php echo esc_attr( $this->core->slug ); ?>-builder-content-connection-subscriber-subscribe">
			<?php $this->print_underscore_template( 'subscriber-subscribe', [ 'fields' => $this->get_fields_html() ] ); ?>
		</script>

		<!-- Single GR connection block: SUBSCRIBER - UNSUBSCRIBE. -->
		<script type="text/html" id="tmpl-wpforms-<?php echo esc_attr( $this->core->slug ); ?>-builder-content-connection-subscriber-unsubscribe">
			<?php $this->print_underscore_template( 'subscriber-unsubscribe', [ 'fields' => $this->get_fields_html() ] ); ?>
		</script>

		<!-- Single GR connection block: ERROR. -->
		<script type="text/html" id="tmpl-wpforms-<?php echo esc_attr( $this->core->slug ); ?>-builder-content-connection-error">
			<?php $this->print_underscore_template( 'error' ); ?>
		</script>

		<!-- Single GR connection block: LOCK. -->
		<script type="text/html" id="tmpl-wpforms-<?php echo esc_attr( $this->core->slug ); ?>-builder-content-connection-lock">
			<?php $this->print_underscore_template( 'lock' ); ?>
		</script>

		<?php
	}

	/**
	 * Enqueue JavaScript and CSS files.
	 *
	 * @since 1.3.0
	 */
	public function enqueue_assets() {

		parent::enqueue_assets();

		$min = wpforms_get_min_suffix();

		wp_enqueue_script(
			'wpforms-getresponse-admin-builder',
			WPFORMS_GETRESPONSE_URL . "assets/js/getresponse-builder{$min}.js",
			[ 'wpforms-admin-builder-providers', 'choicesjs' ],
			WPFORMS_GETRESPONSE_VERSION,
			true
		);

		wp_localize_script(
			'wpforms-getresponse-admin-builder',
			'wpformsGetResponseBuilderVars',
			[ 'i18n' => [ 'providerPlaceholder' => esc_html__( '--- Select GetResponse Field ---', 'wpforms-getresponse' ) ] ]
		);
	}

	/**
	 * Print an Underscore JS template.
	 *
	 * @since 1.3.0
	 *
	 * @param string $name Template file name.
	 * @param array  $args Arguments.
	 */
	protected function print_underscore_template( $name, $args = [] ) {

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wpforms_render( WPFORMS_GETRESPONSE_PATH . "templates/tmpl/{$name}", $args );
	}
}
