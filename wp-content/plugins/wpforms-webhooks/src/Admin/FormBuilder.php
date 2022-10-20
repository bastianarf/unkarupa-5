<?php

namespace WPFormsWebhooks\Admin;

/**
 * Class FormBuilder handles functionality inside the form builder.
 *
 * @since 1.0.0
 */
class FormBuilder {

	/**
	 * White list of field types to allow for mapping select.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $allowed_field_types = [];

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @since 1.0.0
	 */
	protected function hooks() {

		add_action( 'wpforms_form_settings_panel_content', [ $this, 'panel_content' ],   40 );
		add_action( 'wpforms_builder_enqueues',            [ $this, 'enqueue_assets' ],  10 );

		add_filter( 'wpforms_builder_settings_sections',   [ $this, 'panel_sidebar' ],   40, 2 );
		add_filter( 'wpforms_builder_strings',             [ $this, 'builder_strings' ], 40, 2 );
		add_filter( 'wpforms_get_form_fields_allowed',     [ $this, 'form_fields_allowed' ] );
	}

	/**
	 * Add a content for `Webhooks` panel.
	 *
	 * @since 1.0.0
	 *
	 * @param \WPForms_Builder_Panel_Settings $builder_panel_settings WPForms_Builder_Panel_Settings object.
	 */
	public function panel_content( $builder_panel_settings ) {

		wpforms_webhooks()->settings->set_props( $builder_panel_settings );
		$webhooks = wpforms_webhooks()->settings->get_prop( 'webhooks' );

		echo wpforms_render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			WPFORMS_WEBHOOKS_PATH . 'views/settings/section',
			[
				'next_id'             => max( array_keys( $webhooks ) ) + 1,
				'enable_control_html' => $this->get_enable_control_html(),
				'webhooks_html'       => $this->get_webhooks_html(),
				'add_new_btn_classes' => $this->get_html_class(
					[
						'wpforms-builder-settings-block-add',
						'wpforms-webooks-add',
					]
				),
			]
		);
	}

	/**
	 * Retrieve a HTML for On/Off select control.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_enable_control_html() {

		return wpforms_panel_field(
			'select',
			'settings',
			'webhooks_enable',
			wpforms_webhooks()->settings->get_prop( 'form_data' ),
			esc_html__( 'Webhooks', 'wpforms-webhooks' ),
			[
				'default' => '0',
				'options' => [
					'1' => esc_html__( 'On', 'wpforms-webhooks' ),
					'0' => esc_html__( 'Off', 'wpforms-webhooks' ),
				],
			],
			false
		);
	}

	/**
	 * Retrieve a HTML for settings.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_webhooks_html() {

		$webhooks = wpforms_webhooks()->settings->get_prop( 'webhooks' );
		$result   = '';

		foreach ( $webhooks as $webhook_id => $webhook ) {
			$webhook['webhook_id'] = $webhook_id;

			$result .= $this->get_webhook_block( $webhook );
		}

		return $result;
	}

	/**
	 * Retrieve a HTML for setting block.
	 *
	 * @since 1.0.0
	 *
	 * @param array $webhook Webhook data.
	 *
	 * @return string
	 */
	protected function get_webhook_block( $webhook ) {

		$webhook_id   = $webhook['webhook_id'];
		$form_data    = wpforms_webhooks()->settings->get_prop( 'form_data' );
		$toggle_state = '<i class="fa fa-chevron-up"></i>';
		$closed_state = '';

		if (
			! empty( $form_data['id'] ) &&
			'closed' === wpforms_builder_settings_block_get_state( $form_data['id'], $webhook_id, 'webhook' )
		) {
			$toggle_state = '<i class="fa fa-chevron-down"></i>';
			$closed_state = 'style="display:none;"';
		}

		$result = wpforms_render(
			WPFORMS_WEBHOOKS_PATH . 'views/settings/block',
			[
				'id'            => $webhook_id,
				'name'          => $webhook['name'],
				'toggle_state'  => $toggle_state,
				'closed_state'  => $closed_state,
				'fields'        => $this->get_webhook_fields( $webhook ),
				'block_classes' => $this->get_html_class(
					[
						'wpforms-builder-settings-block',
						'wpforms-builder-settings-block-webhook',
					]
				),
			]
		);

		/**
		 * Filter a HTML for setting block.
		 *
		 * @since 1.0.0
		 *
		 * @param string $result     HTML for setting block.
		 * @param array  $form_data  Form data.
		 * @param int    $webhook_id Webhook ID.
		 */
		return apply_filters( 'wpforms_webhooks_form_builder_get_webhook_block', $result, $form_data, $webhook_id );
	}

	/**
	 * Retrieve HTML for fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array $webhook Webhook data.
	 *
	 * @return string
	 */
	protected function get_webhook_fields( $webhook ) {

		$webhook_id    = $webhook['webhook_id'];
		$webhooks      = wpforms_webhooks()->settings->get_prop( 'webhooks' );
		$form_data     = wpforms_webhooks()->settings->get_prop( 'form_data' );
		$form_fields   = wpforms_get_form_fields( $form_data );
		$form_fields   = empty( $form_fields ) && ! is_array( $form_fields ) ? [] : $form_fields;
		$allowed_types = implode( ' ', $this->allowed_field_types );

		$result = wpforms_panel_field(
			'text',
			'webhooks',
			'url',
			$form_data,
			esc_html__( 'Request URL', 'wpforms-webhooks' ),
			[
				'parent'      => 'settings',
				'subsection'  => $webhook_id,
				'input_id'    => 'wpforms-panel-field-webhooks-request-url-' . $webhook_id,
				'input_class' => 'wpforms-required wpforms-required-url',
				'default'     => '',
				'placeholder' => esc_html__( 'Enter a Request URL&hellip;', 'wpforms-webhooks' ),
				'tooltip'     => esc_html__( 'Enter the URL to be used in the webhook request.', 'wpforms-webhooks' ),
			],
			false
		);

		$result .= wpforms_panel_field(
			'select',
			'webhooks',
			'method',
			$form_data,
			esc_html__( 'Request Method', 'wpforms-webhooks' ),
			[
				'parent'     => 'settings',
				'subsection' => $webhook_id,
				'default'    => 'get',
				'options'    => wpforms_webhooks()->get_available_methods(),
				'tooltip'    => esc_html__( 'Select the HTTP method used for the webhook request.', 'wpforms-webhooks' ),
			],
			false
		);

		$result .= wpforms_panel_field(
			'select',
			'webhooks',
			'format',
			$form_data,
			esc_html__( 'Request Format', 'wpforms-webhooks' ),
			[
				'parent'     => 'settings',
				'subsection' => $webhook_id,
				'default'    => 'json',
				'options'    => wpforms_webhooks()->get_available_formats(),
				'tooltip'    => esc_html__( 'Select the format for the webhook request.', 'wpforms-webhooks' ),
			],
			false
		);

		$result .= wpforms_panel_field(
			'text',
			'webhooks',
			'secret',
			$form_data,
			esc_html__( 'Secret', 'wpforms-webhooks' ),
			[
				'parent'      => 'settings',
				'subsection'  => $webhook_id,
				'input_id'    => 'wpforms-panel-field-webhooks-secret-' . $webhook_id,
				'default'     => '',
				'placeholder' => esc_html__( 'Enter a Secret value&hellip;', 'wpforms-webhooks' ),
				'tooltip'     => esc_html__( 'The secret key is used to generate a hash of the delivered webhook and provided in the request headers.', 'wpforms-webhooks' ),
			],
			false
		);

		$result .= wpforms_render(
			WPFORMS_WEBHOOKS_PATH . 'views/settings/fields-mapping',
			[
				'title'         => esc_html__( 'Request Headers', 'wpforms-webhooks' ),
				'webhook_id'    => $webhook_id,
				'fields'        => $form_fields,
				'allowed_types' => $allowed_types,
				'meta'          => ! empty( $webhooks[ $webhook_id ]['headers'] ) ? $webhooks[ $webhook_id ]['headers'] : [ false ],
				'name'          => "settings[webhooks][{$webhook_id}][headers]",
			]
		);

		$result .= wpforms_render(
			WPFORMS_WEBHOOKS_PATH . 'views/settings/fields-mapping',
			[
				'title'         => esc_html__( 'Request Body', 'wpforms-webhooks' ),
				'webhook_id'    => $webhook_id,
				'fields'        => $form_fields,
				'allowed_types' => $allowed_types,
				'meta'          => ! empty( $webhooks[ $webhook_id ]['body'] ) ? $webhooks[ $webhook_id ]['body'] : [ false ],
				'name'          => "settings[webhooks][{$webhook_id}][body]",
			]
		);

		$result .= wpforms_conditional_logic()->builder_block(
			[
				'form'        => $form_data,
				'type'        => 'panel',
				'panel'       => 'webhooks',
				'parent'      => 'settings',
				'subsection'  => $webhook_id,
				'actions'     => [
					'go'   => esc_html__( 'Send', 'wpforms-webhooks' ),
					'stop' => esc_html__( "Don't send", 'wpforms-webhooks' ),
				],
				'action_desc' => esc_html__( ' this webhook if', 'wpforms-webhooks' ),
			],
			false
		);

		/**
		 * Filter HTML for fields.
		 *
		 * @since 1.0.0
		 *
		 * @param string $result     HTML for fields.
		 * @param array  $form_data  Form data.
		 * @param int    $webhook_id Webhook ID.
		 */
		return apply_filters( 'wpforms_webhooks_form_builder_get_webhook_fields', $result, $form_data, $webhook_id );
	}

	/**
	 * Retrieve string of the class names.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes Array of class names for element.
	 *
	 * @return string
	 */
	protected function get_html_class( $classes ) {

		if ( ! is_array( $classes ) ) {
			$classes = (array) $classes;
		}

		if ( ! wpforms_webhooks()->settings->get_prop( 'is_enabled' ) ) {
			$classes[] = 'hidden';
		}

		$classes = array_unique( array_map( 'esc_attr', $classes ) );

		return implode( ' ', $classes );
	}

	/**
	 * Add a new item `Webhooks` to panel sidebar.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sections  Registered sections.
	 * @param array $form_data Contains array of the form data (post_content).
	 *
	 * @return array
	 */
	public function panel_sidebar( $sections, $form_data ) {

		$sections['webhooks'] = esc_html__( 'Webhooks', 'wpforms-webhooks' );

		return $sections;
	}

	/**
	 * Save allowed field types to property.
	 *
	 * @since 1.0.0
	 *
	 * @param array $allowed_field_types White list of field types to allow.
	 *
	 * @return array
	 */
	public function form_fields_allowed( $allowed_field_types ) {

		$this->allowed_field_types = ! empty( $allowed_field_types ) ? $allowed_field_types : [ 'all-fields' ];

		return $allowed_field_types;
	}

	/**
	 * Add own localized strings to the Builder.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $strings Localized strings.
	 * @param object $form    Current form.
	 *
	 * @return array
	 */
	public function builder_strings( $strings, $form ) {

		$strings['webhook_prompt']        = esc_html__( 'Enter a webhook name', 'wpforms-webhooks' );
		$strings['webhook_ph']            = '';
		$strings['webhook_error']         = esc_html__( 'You must provide a webhook name', 'wpforms-webhooks' );
		$strings['webhook_error2']        = esc_html__( 'To disable all webhooks use the "Webhooks" dropdown setting.', 'wpforms-webhooks' );
		$strings['webhook_delete']        = esc_html__( 'Are you sure that you want to delete this webhook?', 'wpforms-webhooks' );
		$strings['webhook_def_name']      = esc_html__( 'Unnamed Webhook', 'wpforms-webhooks' );
		$strings['webhook_required_flds'] = esc_html__( 'Your form contains required Webhook settings that have not been configured. Please double-check and configure these settings to complete the connection setup.', 'wpforms-webhooks' );

		return $strings;
	}

	/**
	 * Enqueue a JavaScript file and inline CSS styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {

		$min = wpforms_get_min_suffix();

		wp_enqueue_script(
			'wpforms-webhooks-admin-builder',
			WPFORMS_WEBHOOKS_URL . "assets/js/webhooks-builder{$min}.js",
			[ 'wpforms-builder' ],
			WPFORMS_WEBHOOKS_VERSION,
			true
		);

		wp_enqueue_style(
			'wpforms-webhooks-admin-builder',
			WPFORMS_WEBHOOKS_URL . "assets/css/webhooks{$min}.css",
			[ 'wpforms-builder' ],
			WPFORMS_WEBHOOKS_VERSION
		);
	}
}
