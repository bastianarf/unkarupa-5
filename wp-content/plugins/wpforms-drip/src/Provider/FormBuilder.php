<?php

namespace WPFormsDrip\Provider;

/**
 * Class FormBuilder handles functionality inside the form builder.
 *
 * @since 1.0.0
 */
class FormBuilder extends \WPForms\Providers\Provider\Settings\FormBuilder {

	/**
	 * @inheritdoc
	 *
	 * @since 1.0.0
	 */
	protected function init_hooks() {

		parent::init_hooks();

		\add_filter( 'wpforms_save_form_args', array( $this, 'save_form_args' ), 11, 3 );

		// Ajax requests.
		\add_action( 'wpforms_providers_settings_builder_ajax_connections_get_' . $this->core->slug, array( $this, 'ajax_connections_get' ) );
		\add_action( 'wpforms_providers_settings_builder_ajax_accounts_get_' . $this->core->slug, array( $this, 'ajax_accounts_get' ) );
		\add_action( 'wpforms_providers_settings_builder_ajax_events_get_' . $this->core->slug, array( $this, 'ajax_events_get' ) );
		\add_action( 'wpforms_providers_settings_builder_ajax_campaigns_get_' . $this->core->slug, array( $this, 'ajax_campaigns_get' ) );
		\add_action( 'wpforms_providers_settings_builder_ajax_account_template_get_' . $this->core->slug, array( $this, 'ajax_account_template_get' ) );
		\add_action( 'wpforms_providers_settings_builder_ajax_account_save_' . $this->core->slug, array( $this, 'ajax_account_save' ) );
	}

	/**
	 * Preprocess provider data before saving it in form_data when editing form.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form Form array, usable with wp_update_post.
	 * @param array $data Data retrieved from $_POST and processed.
	 * @param array $args Empty by default, may have custom data not intended to be saved, but used for processing.
	 *
	 * @return array
	 */
	public function save_form_args( $form, $data, $args ) {

		if ( empty( $data['providers'][ $this->core->slug ] ) ) {
			return $form;
		}

		$post_content = json_decode( stripslashes( $form['post_content'] ), true ); // phpcs:ignore

		// Modify content as we need, done by reference.
		foreach ( $post_content['providers'][ $this->core->slug ] as $connection_id => &$connection ) {

			// It will be easier in templates to reuse this connection_id.
			$connection['id'] = $connection_id;

			if ( empty( $connection['action'] ) ) {
				continue;
			}

			// Base lead score is always an integer, when it is set.
			if ( isset( $connection['base_lead_score'] ) ) {
				$connection['base_lead_score'] = (int) $connection['base_lead_score'];
			}

			/*
			 * Make fields meta field_id an integer, sanitize name.
			 */
			if ( ! empty( $connection['fields_meta'] ) ) {
				$temp_prop = [];

				foreach ( $connection['fields_meta'] as $id => $property ) {
					$name = wpforms_drip()->sanitize_field_id( $property['name'] );

					// Do not allow to save empty property name.
					if ( empty( $name ) ) {
						continue;
					}

					$temp_prop[] = [
						'name'     => $name,
						'field_id' => (int) $property['field_id'],
					];
				}

				$connection['fields_meta'] = $temp_prop;
			}

			if ( isset( $connection['tags'] ) && isset( $connection['tags']['add'] ) && ! is_array( $connection['tags']['add'] ) ) {
				$tags_add                  = isset( $connection['tags']['add'] ) ? \explode( ',', $connection['tags']['add'] ) : [];
				$connection['tags']['add'] = \array_map( 'sanitize_text_field', $tags_add );
			}

			if ( isset( $connection['tags'] ) && isset( $connection['tags']['delete'] ) && ! is_array( $connection['tags']['delete'] ) ) {
				$tags_del                     = isset( $connection['tags']['delete'] ) ? \explode( ',', $connection['tags']['delete'] ) : [];
				$connection['tags']['delete'] = \array_map( 'sanitize_text_field', $tags_del );
			}

			// Action specific connection data modifications.
			switch ( $connection['action'] ) {
				case 'event':
					// Sanitize current value.
					if ( ! empty( $connection['events']['name'] ) ) {
						$connection['events']['name'] = \sanitize_text_field( $connection['events']['name'] );
					}
					// If user provided a new one - re-save it.
					if ( ! empty( $connection['events']['new'] ) ) {
						$connection['events']['name'] = \ucfirst( \sanitize_text_field( $connection['events']['new'] ) );
						unset( $connection['events']['new'] );
					}
					break;

				case 'subscriber_subscribe':
					if ( ! empty( $connection['campaign_id'] ) ) {
						$connection['campaign_id'] = (int) $connection['campaign_id'];
					}

					break;
			}
		}
		unset( $connection );

		// Save the modified version back to form.
		$form['post_content'] = \wpforms_encode( $post_content );

		return $form;
	}

	/**
	 * Get the list of all saved connections.
	 *
	 * @since 1.0.0
	 *
	 * @return array Return null on any kind of error. Array of data otherwise.
	 */
	public function ajax_connections_get() {

		$connections = array(
			'connections'  => isset( $this->form_data['providers'][ $this->core->slug ] ) ? array_reverse( $this->form_data['providers'][ $this->core->slug ], true ) : [],
			'conditionals' => [],
		);

		// Get conditional logic for each connection_id.
		foreach ( $connections['connections'] as $connection ) {
			if ( empty( $connection['id'] ) ) {
				continue;
			}

			// This will either return an empty placeholder or complete set of rules, as a DOM.
			$connections['conditionals'][ $connection['id'] ] = wpforms_conditional_logic()->builder_block(
				array(
					'form'       => $this->form_data,
					'type'       => 'panel',
					'parent'     => 'providers',
					'panel'      => $this->core->slug,
					'subsection' => $connection['id'],
					'reference'  => esc_html__( 'Marketing provider connection', 'wpforms' ),
				),
				false
			);
		}

		// Get accounts as well, so js won't need to make several requests.
		$accounts = $this->ajax_accounts_get();

		return array_merge( $connections, $accounts );
	}

	/**
	 * Get the list of all accounts for all API Tokens that might have been saved.
	 *
	 * @since 1.0.0
	 *
	 * @return array May return an empty sub-array.
	 */
	public function ajax_accounts_get() {

		$options = \wpforms_get_providers_options( $this->core->slug );
		$data    = [ 'accounts' => [] ];

		// We might have several different API tokens.
		foreach ( $options as $option_id => $option ) {
			if ( empty( $option['api_token'] ) ) {
				continue;
			}

			$drip = new Drip( $option['api_token'] );

			try {
				$drip_data = $drip->makeRawRequest( 'get', 'accounts' );

				if ( property_exists( $drip_data, 'errors' ) ) {
					continue;
				}

				$data['accounts'][ $option_id ] = $drip_data->accounts;
			} catch ( \Exception $e ) {
				continue;
			}
		}

		return $data;
	}

	/**
	 * Get the list of all saved custom events from Drip.
	 *
	 * @since 1.0.0
	 *
	 * @return array|null Return null on any kind of error. Array of data otherwise.
	 */
	public function ajax_events_get() {

		$data          = null;
		$account_id    = (int) $_POST['connection_account_id'];
		$option_id     = \sanitize_key( $_POST['connection_option_id'] );
		$connection_id = \sanitize_key( $_POST['connection_id'] );

		// Check the required fields.
		if (
			empty( $account_id ) ||
			empty( $option_id ) ||
			empty( $connection_id )
		) {
			return $data;
		}

		$options = \wpforms_get_providers_options( $this->core->slug );

		// If we have custom event name - add it.
		// This is useful when no events have been sent and Drip doesn't know about it yet.
		if ( ! empty( $this->form_data['providers'][ $this->core->slug ][ $connection_id ]['events']['name'] ) ) {
			$data['events'][ $connection_id ] = [ $this->form_data['providers'][ $this->core->slug ][ $connection_id ]['events']['name'] ];
		}

		if ( empty( $options[ $option_id ]['api_token'] ) ) {
			return $data;
		}

		$drip = new Drip( $options[ $option_id ]['api_token'], $account_id );

		try {
			$drip_data = $drip->get( 'event_actions' );

			if ( property_exists( $drip_data, 'errors' ) ) {
				return $data;
			}

			// In case we are displaying we need to avoid duplicated names.
			if ( \is_array( $data ) ) {
				$data['events'][ $connection_id ] = \array_unique( \array_merge( (array) $data['events'][ $connection_id ], (array) $drip_data->event_actions ) );
			} else {
				$data['events'][ $connection_id ] = $drip_data->event_actions;
			}
		} catch ( \Exception $e ) {
			return $data;
		}

		return $data;
	}

	/**
	 * Get all campaigns from Drip, of any status (active and/or draft).
	 *
	 * @since 1.0.0
	 *
	 * @return array|null Return null on any kind of error. Array of data otherwise.
	 */
	public function ajax_campaigns_get() {

		$data          = null;
		$account_id    = (int) $_POST['connection_account_id'];
		$option_id     = \sanitize_key( $_POST['connection_option_id'] );
		$connection_id = \sanitize_key( $_POST['connection_id'] );

		// Check the required fields.
		if (
			empty( $account_id ) ||
			empty( $option_id ) ||
			empty( $connection_id )
		) {
			return $data;
		}

		$options = \wpforms_get_providers_options( $this->core->slug );

		if ( empty( $options[ $option_id ]['api_token'] ) ) {
			return $data;
		}

		$drip = new Drip( $options[ $option_id ]['api_token'], $account_id );

		try {
			$drip_data = $drip->get( 'campaigns', [
				'status'    => 'all',
				'sort'      => 'name',
				'direction' => 'asc',
			] );

			if ( property_exists( $drip_data, 'errors' ) ) {
				return null;
			}

			$data['campaigns'][ $connection_id ] = $drip_data->campaigns;

		} catch ( \Exception $e ) {
			return null;
		}

		return $data;
	}

	/**
	 * Save the date for a new account, and validate it.
	 *
	 * @since 1.1.0
	 *
	 * @return array|null Return null on any kind of error. Array of data otherwise.
	 */
	public function ajax_account_template_get() {

		$data = array(
			'title'   => esc_html__( 'New Drip Account', 'wpforms-drip' ),
			'content' => Core::get_add_new_account_fields_html(),
			'type'    => 'blue',
		);

		return $data;
	}

	/**
	 * Save the date for a new account, and validate it.
	 *
	 * @since 1.1.0
	 *
	 * @return array|null Return null on any kind of error. Array of data otherwise.
	 */
	public function ajax_account_save() {

		$acc_name  = sanitize_text_field( $_POST['acc_name'] );
		$acc_token = sanitize_key( $_POST['acc_token'] );

		$drip = new Drip( $acc_token );
		$data = [];

		try {
			$drip_data = $drip->makeRawRequest( 'get', 'user' );

			if ( empty( $drip_data->users[0] ) ) {
				$data['error'] = esc_html__( 'Error requesting a Drip user info. Make sure API token is correct.', 'wpforms-drip' );

				return $data;
			}

			$label = $acc_name;
			if ( empty( $label ) ) {
				$label = ! empty( $drip_data->users[0]['name'] ) ? sanitize_text_field( $drip_data->users[0]['name'] ) : sanitize_text_field( $drip_data->users[0]['email'] );
			}

			// Save this connection for the provider.
			wpforms_update_providers_options( $this->core->slug, array(
				'api_token' => $acc_token,
				'label'     => $label,
				'date'      => time(),
			) );

		} catch ( \Exception $e ) {
			return null;
		}

		return [];
	}

	/**
	 * @inheritdoc
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {

		parent::enqueue_assets();

		$min = \wpforms_get_min_suffix();

		\wp_enqueue_script(
			'wpforms-drip-admin-builder',
			WPFORMS_DRIP_URL . "assets/js/drip-builder{$min}.js",
			array( 'wpforms-admin-builder-providers' ),
			WPFORMS_DRIP_VERSION,
			true
		);

		\wp_enqueue_style(
			'wpforms-drip-admin-builder',
			WPFORMS_DRIP_URL . "assets/css/drip-builder{$min}.css",
			array( 'wpforms-builder' ),
			WPFORMS_DRIP_VERSION
		);
	}

	/**
	 * @inheritdoc
	 *
	 * @since 1.0.0
	 */
	public function builder_custom_templates() {
		?>
		<!-- Holder for all provider builder content. -->
		<script type="text/html" id="tmpl-wpforms-<?php echo \esc_attr( $this->core->slug ); ?>-builder-content">
			<div class="wpforms-provider-connections-wrap wpforms-clear">

				<div class="wpforms-builder-provider-connections"></div>
			</div>
		</script>

		<!-- Single connection. -->
		<script type="text/html" id="tmpl-wpforms-<?php echo \esc_attr( $this->core->slug ); ?>-builder-content-connection">
			<div class="wpforms-builder-provider-connection" data-connection_id="{{ data.connection.id }}">
				<input type="hidden" class="wpforms-builder-provider-connection-option_id"
				       name="providers[<?php echo \esc_attr( $this->core->slug ); ?>][{{ data.connection.id }}][option_id]"
				       value="{{ data.connection.option_id }}">

				<div class="wpforms-builder-provider-connection-title">
					{{ data.connection.name }}

					<button class="wpforms-builder-provider-connection-delete js-wpforms-builder-provider-connection-delete">
						<i class="fa fa-times-circle"></i>
					</button>

					<input type="hidden"
					       id="wpforms-builder-drip-provider-%connection_id%-name"
					       name="providers[<?php echo \esc_attr( $this->core->slug ); ?>][{{ data.connection.id }}][name]"
					       value="{{ data.connection.name }}">
				</div>

				<div class="wpforms-builder-provider-connection-block wpforms-builder-drip-provider-accounts">
					<h4><?php \esc_html_e( 'Select Account', 'wpforms-drip' ); ?><span class="required">*</span></h4>

					<select class="js-wpforms-builder-drip-provider-connection-account wpforms-required"
					        id="wpforms-builder-drip-provider-%connection_id%-account"
					        <# if ( _.isEmpty( data.accounts ) ) { #>disabled<# } #>
					        name="providers[<?php echo \esc_attr( $this->core->slug ); ?>][{{ data.connection.id }}][account_id]">

						<option value=""><?php esc_html_e( '--- Select Account ---', 'wpforms-drip' ); ?></option>

						<# _.each( data.accounts, function( accounts, option_id ) { #>
							<# _.each( accounts, function( account, key ) { #>
								<option value="{{ account.id }}" data-option_id="{{ option_id }}" <# if ( _.isMatch( data.connection, {account_id: account.id} ) ) { #>selected="selected"<# } #>>
									{{ account.name }}
								</option>
							<# } ); #>
						<# } ); #>

					</select>
				</div>

				<# if ( ! _.isEmpty( data.accounts ) ) { #>
					<div class="wpforms-builder-provider-connection-block wpforms-builder-drip-provider-actions">
						<h4><?php \esc_html_e( 'Action To Perform', 'wpforms-drip' ); ?><span class="required">*</span></h4>

						<select class="wpforms-required js-wpforms-builder-drip-provider-connection-action"
						        id="wpforms-builder-drip-provider-%connection_id%-action"
						        <# if ( _.isEmpty( data.connection.account_id ) ) { #>disabled<# } #>
						        name="providers[<?php echo \esc_attr( $this->core->slug ); ?>][{{ data.connection.id }}][action]">

							<option value=""><?php \esc_html_e( '--- Select Action ---', 'wpforms-drip' ); ?></option>

							<option value="subscriber_subscribe" <# if ( 'subscriber_subscribe' === data.connection.action ) { #>selected="selected"<# } #>><?php \esc_html_e( 'Subscriber: Create or Update', 'wpforms-drip' ); ?></option>
							<option value="subscriber_delete" <# if ( 'subscriber_delete' === data.connection.action ) { #>selected="selected"<# } #>><?php \esc_html_e( 'Subscriber: Delete', 'wpforms-drip' ); ?></option>
							<option value="campaign_sub" <# if ( 'campaign_sub' === data.connection.action ) { #>selected="selected"<# } #>><?php \esc_html_e( 'Campaign: Subscribe', 'wpforms-drip' ); ?></option>
							<option value="campaign_unsub" <# if ( 'campaign_unsub' === data.connection.action ) { #>selected="selected"<# } #>><?php \esc_html_e( 'Campaign: Unsubscribe', 'wpforms-drip' ); ?></option>
							<option value="event" <# if ( 'event' === data.connection.action ) { #>selected="selected"<# } #>><?php \esc_html_e( 'Record Event', 'wpforms-drip' ); ?></option>

						</select>
					</div>
				<# } #>

				<!-- Here is where sub-templates will put its compiled HTML. -->
				<div class="wpforms-builder-drip-provider-actions-data"></div>

				<# if ( ! _.isEmpty( data.accounts ) ) { #>
					{{{ data.conditional }}}
				<# } #>

			</div>
		</script>

		<!-- Single connection block: EVENTS -->
		<script type="text/html" id="tmpl-wpforms-<?php echo \esc_attr( $this->core->slug ); ?>-builder-content-connection-events">
			<div class="wpforms-builder-provider-connection-block wpforms-builder-drip-provider-events">
				<div class="wpforms-builder-provider-connection-setting">
					<?php $this->display_email_field(); ?>
				</div>

				<div class="wpforms-builder-provider-connection-settings-group">
					<h4><?php \esc_html_e( 'Custom Events', 'wpforms-drip' ); ?></h4>

					<p><?php esc_html_e( 'Select one of the existing events:', 'wpforms-drip' ); ?></p>

					<div class="wpforms-builder-provider-connection-setting">
						<select class="js-wpforms-builder-drip-provider-connection-event"
						        id="wpforms-builder-drip-provider-%connection_id%-event"
						        <# if ( _.isEmpty(data.events) ) { #>disabled<# } #>
						        name="providers[<?php echo \esc_attr( $this->core->slug ); ?>][{{ data.connection.id }}][events][name]">

							<option value=""><?php \esc_html_e( '--- Select Saved Event ---', 'wpforms-drip' ); ?></option>

							<# _.each( data.events, function( event, key ) { #>
								<option value="{{ event }}" <# if (  _.isMatch( data.connection.events, {name: event} ) ) { #>selected="selected"<# } #>>
									{{ event }}
								</option>
							<# } ); #>

						</select>

						<p class="text-btn-inlined">
							<span class="text-btn-inlined-text">
								<?php esc_html_e( 'or', 'wpforms-drip' ); ?>
							</span>
							<button class="button-secondary text-btn-inlined-btn js-wpforms-builder-drip-provider-connection-event-new">
								<?php \esc_html_e( 'Add New', 'wpforms-drip' ); ?>
							</button>
						</p>
					</div>

					<div class="wpforms-builder-provider-connection-setting wpforms-builder-drip-provider-events-new">
						<input type="text" value=""
						       id="wpforms-builder-drip-provider-%connection_id%-events-new"
						       name="providers[<?php echo \esc_attr( $this->core->slug ); ?>][{{ data.connection.id }}][events][new]"
						       placeholder="<?php \esc_attr_e( 'Your new custom event name', 'wpforms-drip' ); ?>" />

						<p class="description">
							<?php \esc_html_e( 'Example: Submitted Drip form', 'wpforms-drip' ); ?>
						</p>
					</div>
				</div>

				<div class="wpforms-builder-provider-connection-settings-group wpforms-builder-drip-provider-prospect">
					<div class="wpforms-builder-provider-connection-setting wpforms-builder-drip-provider-prospect-check">
						<input type="checkbox" value="on"
						       id="wpforms-builder-drip-provider-%connection_id%-prospect-check"
						       name="providers[<?php echo \esc_attr( $this->core->slug ); ?>][{{ data.connection.id }}][prospect]"
						       <# if ( _.isMatch( data.connection, {prospect: 'on'} ) ) { #>checked="checked"<# } #>
						>

						<label for="wpforms-builder-drip-provider-%connection_id%-prospect-check">
							<?php \esc_html_e( 'Mark user as a prospect', 'wpforms-drip' ); ?>
						</label>

						<p class="description">
							<?php \esc_html_e( 'Attach a lead score to the subscriber (when lead scoring is enabled).', 'wpforms-drip' ); ?>
						</p>
					</div>
				</div>
			</div>
		</script>

		<!-- Single connection block: SUBSCRIBER - CREATE / UPDATE -->
		<script type="text/html" id="tmpl-wpforms-<?php echo \esc_attr( $this->core->slug ); ?>-builder-content-connection-subscriber-subscribe">
			<div class="wpforms-builder-provider-connection-block wpforms-builder-drip-provider-subscriber-subscribe">
				<h4><?php \esc_html_e( 'Create or Update Subscriber', 'wpforms-drip' ); ?></h4>

				<div class="wpforms-builder-provider-connection-setting">
					<?php $this->display_email_field(); ?>
				</div>

				<# if ( _.indexOf( data.ignore, 'new_email' ) === -1  ) { #>
					<div class="wpforms-builder-provider-connection-setting">
						<?php $this->display_email_field( 'new' ); ?>
					</div>
				<# } #>

				<# if ( _.indexOf( data.ignore, 'user_id' ) === -1  ) { #>
					<div class="wpforms-builder-provider-connection-setting wpforms-builder-drip-provider-subscriber-user_id">
						<input type="checkbox" value="on"
						       id="wpforms-builder-drip-provider-%connection_id%-subscriber-user_id"
						       name="providers[<?php echo \esc_attr( $this->core->slug ); ?>][{{ data.connection.id }}][user_id]"
						       <# if ( _.isMatch( data.connection, {user_id: 'on'} ) ) { #>checked="checked"<# } #>
						>

						<label for="wpforms-builder-drip-provider-%connection_id%-subscriber-user_id">
							<?php \esc_html_e( 'Send user ID to Drip (if the user was logged in)', 'wpforms-drip' ); ?>
						</label>

						<p class="description">
							<?php \esc_html_e( 'Make sure that users can submit this form only once. All repeated submissions will be ignored by Drip.', 'wpforms-drip' ); ?>
						</p>
					</div>
				<# } #>

				<# if ( _.indexOf( data.ignore, 'ip_address' ) === -1  ) { #>
					<div class="wpforms-builder-provider-connection-setting wpforms-builder-drip-provider-subscriber-ip_address">
						<input type="checkbox" value="on"
						       id="wpforms-builder-drip-provider-%connection_id%-subscriber-ip_address"
						       name="providers[<?php echo \esc_attr( $this->core->slug ); ?>][{{ data.connection.id }}][ip_address]"
						       <# if ( _.isMatch( data.connection, {ip_address: 'on'} ) ) { #>checked="checked"<# } #>
						>

						<label for="wpforms-builder-drip-provider-%connection_id%-subscriber-ip_address">
							<?php \esc_html_e( 'Send user IP address to Drip', 'wpforms-drip' ); ?>
						</label>
					</div>
				<# } #>

				<# if ( _.indexOf( data.ignore, 'prospect' ) === -1  ) { #>
					<div class="wpforms-builder-provider-settings-group wpforms-builder-drip-provider-prospect">
						<div class="wpforms-builder-provider-connection-setting wpforms-builder-drip-provider-prospect-check">
							<input type="checkbox" value="on"
							       id="wpforms-builder-drip-provider-%connection_id%-prospect-check"
							       name="providers[<?php echo \esc_attr( $this->core->slug ); ?>][{{ data.connection.id }}][prospect]"
							       <# if ( _.isMatch( data.connection, {prospect: 'on'} ) ) { #>checked="checked"<# } #>
							>

							<label for="wpforms-builder-drip-provider-%connection_id%-prospect-check"><?php \esc_html_e( 'Mark user as a prospect', 'wpforms-drip' ); ?></label>
						</div>

						<div class="wpforms-builder-provider-connection-setting wpforms-builder-drip-provider-prospect-score <# if ( ! _.isMatch( data.connection, {prospect: 'on'} ) ) { #>hidden<# } #>">
							<label for="wpforms-builder-drip-provider-%connection_id%-prospect-score">
								<?php \esc_html_e( 'Starting Lead Score Value', 'wpforms-drip' ); ?>
							</label>

							<input type="number" value="{{ data.connection.base_lead_score }}"
							       id="wpforms-builder-drip-provider-%connection_id%-prospect-score"
								   name="providers[<?php echo \esc_attr( $this->core->slug ); ?>][{{ data.connection.id }}][base_lead_score]">

						   <p class="description">
								<?php \esc_html_e( 'Attach a lead score to the subscriber (when lead scoring is enabled).', 'wpforms-drip' ); ?>
							</p>
						</div>
					</div>
				<# } #>

				<# if ( _.indexOf( data.ignore, 'tags' ) === -1  ) { #>
					<div class="wpforms-builder-provider-connection-settings-group wpforms-builder-drip-provider-subscriber-subscribe-tags">
						<div class="wpforms-builder-provider-connection-setting wpforms-builder-drip-provider-subscriber-subscribe-tags-add">
							<label for="wpforms-builder-drip-provider-%connection_id%-subscriber-subscribe-tags-add">
								<?php \esc_html_e( 'Tags To Add', 'wpforms-drip' ); ?>
								<i class="fa fa-question-circle wpforms-help-tooltip" title="<?php \esc_attr_e( 'Comma-separated list of tags is accepted.', 'wpforms-drip' ); ?>"></i>
							</label>

							<input type="text" class="regular-text"
							       id="wpforms-builder-drip-provider-%connection_id%-subscriber-subscribe-tags-add"
							       value="<# if ( ! _.isEmpty( data.connection.tags ) && ! _.isEmpty( data.connection.tags.add ) ) { #>{{ data.connection.tags.add.join() }}<# } else { #>WPForms<# } #>"
								   name="providers[<?php echo \esc_attr( $this->core->slug ); ?>][{{ data.connection.id }}][tags][add]">
						</div>

						<# if ( _.indexOf( data.ignore, 'tags_delete' ) === -1  ) { #>
							<div class="wpforms-builder-provider-connection-setting wpforms-builder-drip-provider-subscriber-subscribe-tags-delete">
								<label for="wpforms-builder-drip-provider-%connection_id%-subscriber-subscribe-tags-delete">
									<?php \esc_html_e( 'Tags To Delete', 'wpforms-drip' ); ?>
									<i class="fa fa-question-circle wpforms-help-tooltip" title="<?php \esc_attr_e( 'Comma-separated list of tags is accepted.', 'wpforms-drip' ); ?>"></i>
								</label>

								<input type="text" class="regular-text"
								       id="wpforms-builder-drip-provider-%connection_id%-subscriber-subscribe-tags-delete"
								       value="<# if ( ! _.isEmpty( data.connection.tags ) && ! _.isEmpty( data.connection.tags.delete ) ) { #>{{ data.connection.tags.delete.join() }}<# } #>"
									   name="providers[<?php echo \esc_attr( $this->core->slug ); ?>][{{ data.connection.id }}][tags][delete]">
							</div>
						<# } #>


					</div>
				<# } #>

			</div>
		</script>

		<!-- Single connection block: SUBSCRIBER - DELETE -->
		<script type="text/html" id="tmpl-wpforms-<?php echo \esc_attr( $this->core->slug ); ?>-builder-content-connection-subscriber-delete">
			<div class="wpforms-builder-provider-connection-block wpforms-builder-drip-provider-subscriber-delete">
				<h4><?php \esc_html_e( 'Delete a Subscriber', 'wpforms-drip' ); ?></h4>

				<div class="wpforms-builder-provider-connection-setting">

					<p class="description before">
						<?php \esc_html_e( 'Sometimes you may want to give your users the option to remove themselves from your subscribers using your own form.', 'wpforms-drip' ); ?><br>
						<?php \esc_html_e( 'This action is irreversible, you will lose all the data in Drip about the subscriber.', 'wpforms-drip' ); ?>
					</p>

					<?php $this->display_email_field(); ?>
				</div>
			</div>
		</script>

		<!-- Single connection block: CAMPAIGNS - ADD -->
		<script type="text/html" id="tmpl-wpforms-<?php echo \esc_attr( $this->core->slug ); ?>-builder-content-connection-campaigns-add">
			<div class="wpforms-builder-provider-connection-block wpforms-builder-drip-provider-campaigns">
				<h4><?php \esc_html_e( 'Select Campaign', 'wpforms-drip' ); ?><span class="required">*</span></h4>

				<div class="wpforms-builder-provider-connection-setting wpforms-builder-drip-provider-campaigns-campaign">

					<select class="wpforms-required js-wpforms-builder-drip-provider-connection-campaign"
					        id="wpforms-builder-drip-provider-%connection_id%-campaigns-campaign"
							<# if ( _.isEmpty(data.campaigns) ) { #>disabled<# } #>
					        name="providers[<?php echo \esc_attr( $this->core->slug ); ?>][{{ data.connection.id }}][campaign_id]">

						<option value=""><?php \esc_html_e( '--- Select Campaign ---', 'wpforms-drip' ); ?></option>

						<# _.each( data.campaigns, function( campaign, key ) { #>
							<option value="{{ campaign.id }}" <# if ( _.isMatch( data.connection, {campaign_id: campaign.id} ) ) { #>selected="selected"<# } #>>
								{{ campaign.name }} ({{ campaign.status }})
							</option>
						<# } ); #>

					</select>

					<# if ( _.isEmpty( data.campaigns ) ) { #>
						<p class="description error-message">
							<?php
							printf(
								wp_kses(
									/* translators: %s - URL to Drip Knowledge base article. */
									\__( 'You have no campaigns yet. Consider creating at least one. Here is <a href="%s" rel="noopener noreferrer" target="_blank">how to do it</a>.', 'wpforms-drip' ),
									array(
										'a' => array(
											'href'   => array(),
											'rel'    => array(),
											'target' => array(),
										),
									)
								),
								'https://help.drip.com/hc/en-us/articles/115003737832-Create-a-Campaign'
							);
							?>
						</p>
					<# } #>
				</div>
			</div>
		</script>

		<!-- Single connection block: CAMPAIGNS - DELETE -->
		<script type="text/html" id="tmpl-wpforms-<?php echo \esc_attr( $this->core->slug ); ?>-builder-content-connection-campaigns-delete">
			<div class="wpforms-builder-provider-connection-block wpforms-builder-drip-provider-campaigns">
				<h4><?php \esc_html_e( 'Unsubscribe From Campaign(s)', 'wpforms-drip' ); ?></h4>

				<div class="wpforms-builder-provider-connection-setting wpforms-builder-drip-provider-campaigns-campaign">

					<p class="description before">
						<?php \esc_html_e( 'Sometimes you may want to give your users the option to unsubscribe themselves from your campaign(s) using your own form.', 'wpforms-drip' ); ?>
					</p>

					<select class="wpforms-required js-wpforms-builder-drip-provider-connection-campaign"
					        id="wpforms-builder-drip-provider-%connection_id%-campaigns-campaign"
							<# if ( _.isEmpty(data.campaigns) ) { #>disabled<# } #>
					        name="providers[<?php echo \esc_attr( $this->core->slug ); ?>][{{ data.connection.id }}][campaign_id]">

						<option value=""><?php \esc_html_e( '--- Select Campaign ---', 'wpforms-drip' ); ?></option>

						<# _.each( data.campaigns, function( campaign, key ) { #>
							<option value="{{ campaign.id }}" <# if ( _.isMatch( data.connection, {campaign_id: campaign.id} ) ) { #>selected="selected"<# } #>>
								{{ campaign.name }} ({{ campaign.status }})
							</option>
						<# } ); #>

						<option value="all" <# if ( _.isMatch( data.connection, {campaign_id: 'all'} ) ) { #>selected="selected"<# } #>><?php \esc_html_e( 'All Campaigns', 'wpforms-drip' ); ?></option>

					</select>

					<# if ( _.isEmpty( data.campaigns ) ) { #>
						<p class="description error-message">
							<?php \esc_html_e( 'You have no campaigns yet. Consider creating at least one.', 'wpforms-drip' ); ?>
						</p>
					<# } #>
				</div>

				<div class="wpforms-builder-provider-connection-setting">
					<!-- Subscriber Email -->
					<?php $this->display_email_field(); ?>
				</div>
			</div>
		</script>

		<?php
	}

	/**
	 * Display a generated field with all markup for email selection.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Is it for a main email field of a secondary, known in Drip as a new email.
	 */
	protected function display_email_field( $type = 'main' ) {

		switch ( $type ) {
			case 'main':
				\wpforms_panel_field(
					'select',
					'drip',
					'email',
					$this->form_data,
					\esc_html__( 'Subscriber Email', 'wpforms-drip' ),
					array(
						'parent'        => 'providers',
						'subsection'    => '%connection_id%][fields', // This is a hack to add a required nesting level.
						'field_map'     => array( 'email' ),
						'placeholder'   => \esc_html__( '--- Select Email Field ---', 'wpforms-drip' ),
						'after_tooltip' => '<span class="required">*</span>',
						'input_class'   => 'wpforms-required',
						'input_id'      => 'wpforms-panel-field-' . $this->core->slug . '-%connection_id%-email',
					)
				);
				break;

			case 'new':
				\wpforms_panel_field(
					'select',
					'drip',
					'new_email',
					$this->form_data,
					\esc_html__( 'New Subscriber Email', 'wpforms-drip' ),
					array(
						'parent'      => 'providers',
						'subsection'  => '%connection_id%][fields', // This is a hack to add a required nesting level.
						'field_map'   => array( 'email' ),
						'placeholder' => \esc_attr__( '--- Select New Email Field ---', 'wpforms-drip' ),
						'input_id'    => 'wpforms-panel-field-' . $this->core->slug . '-%connection_id%-email-new',
					)
				);
				break;
		}
	}
}
