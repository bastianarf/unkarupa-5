<?php

namespace WPFormsWebhooks\Admin;

use WPFormsWebhooks\Webhook;
use WPFormsWebhooks\Helpers\Formatting;

/**
 * Settings class.
 *
 * @since 1.0.0
 */
class Settings {

	/**
	 * Form data.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $form_data = [];

	/**
	 * Webhooks settings.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $webhooks = [];

	/**
	 * Is Webhooks enabled?
	 *
	 * @since 1.0.0
	 *
	 * @var boolean
	 */
	private $is_enabled = false;

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		if ( empty( $this->webhooks ) ) {
			$this->webhooks[1] = ( new Webhook() )->get_defaults();
		}

		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @since 1.0.0
	 */
	protected function hooks() {

		add_filter( 'wpforms_save_form_args', [ $this, 'save_form_args' ], 11, 3 );
	}

	/**
	 * Preprocess webhooks data before saving it in form_data when editing form.
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

		// Bail if form has no webhooks.
		if ( empty( $data['settings']['webhooks'] ) ) {
			return $form;
		}

		// Get a filtered form content.
		$form_data = json_decode( stripslashes( $form['post_content'] ), true );

		// Modify content as we need, done by reference.
		foreach ( $form_data['settings']['webhooks'] as $webhook_id => &$webhook_data ) {

			if (
				empty( $webhook_data['url'] ) ||
				! Formatting::is_url( $webhook_data['url'] )
			) {
				unset( $form_data['settings']['webhooks'][ $webhook_id ] );
				continue;
			}

			$webhook_data['id'] = $webhook_id;
			$webhook            = new Webhook( $webhook_data, true );

			if ( empty( $webhook->get_secret() ) ) {
				$webhook->set_secret();
			}

			$webhook_data = $webhook->get_data();
		}
		unset( $webhook_data );

		// Save the modified version back to form.
		$form['post_content'] = wpforms_encode( $form_data );

		return $form;
	}

	/**
	 * Set a collection of props.
	 *
	 * @since 1.0.0
	 *
	 * @param \WPForms_Builder_Panel_Settings $panel_settings WPForms_Builder_Panel_Settings object.
	 */
	public function set_props( $panel_settings ) {

		if ( ! ( $panel_settings instanceof \WPForms_Builder_Panel_Settings ) ) {
			return;
		}

		$this->form_data = $panel_settings->form_data;

		if (
			empty( $this->form_data['settings'] ) ||
			! is_array( $this->form_data['settings'] )
		) {
			return;
		}

		$form_settings = $this->form_data['settings'];

		if ( isset( $form_settings['webhooks_enable'] ) ) {
			$this->is_enabled = wp_validate_boolean( $form_settings['webhooks_enable'] );
		}

		if ( ! empty( $form_settings['webhooks'] ) ) {
			$this->webhooks = $form_settings['webhooks'];
		}
	}

	/**
	 * Gets a prop for a getter method.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Name of prop to get.
	 *
	 * @return mixed
	 */
	public function get_prop( $name ) {

		return isset( $this->{$name} ) ? $this->{$name} : null;
	}
}
