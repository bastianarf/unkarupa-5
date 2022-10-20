<?php

namespace WPFormsGetResponse\Provider;

/**
 * Class Core registers all the handlers for
 * Form Builder, Settings > Integrations page, Processing, etc.
 *
 * @since 1.3.0
 */
class Core extends \WPForms\Providers\Provider\Core {

	/**
	 * Priority for a provider, that will affect loading/placement order.
	 *
	 * @since 1.3.0
	 */
	const PRIORITY = 22;

	/**
	 * Core constructor.
	 *
	 * @since 1.3.0
	 */
	public function __construct() {

		parent::__construct(
			[
				'slug' => 'getresponse_v3',
				'name' => esc_html__( 'GetResponse', 'wpforms-getresponse' ),
				'icon' => WPFORMS_GETRESPONSE_URL . 'assets/images/addon-icon-getresponse.png',
			]
		);
	}

	/**
	 * Provide an instance of the object, that should process the submitted entry.
	 * It will use data from an already saved entry to pass it further to a Provider.
	 *
	 * @since 1.3.0
	 *
	 * @return null|\WPFormsGetResponse\Provider\Process
	 */
	public function get_process() {

		static $process = null;

		if (
			null === $process ||
			! $process instanceof Process
		) {
			$process = new Process( static::get_instance() );
		}

		return $process;
	}

	/**
	 * Provide an instance of the object, that should display provider settings
	 * on Settings > Integrations page in admin area.
	 *
	 * @since 1.3.0
	 *
	 * @return null|\WPFormsGetResponse\Provider\Settings\PageIntegrations
	 */
	public function get_page_integrations() {

		static $integration = null;

		if (
			null === $integration ||
			! $integration instanceof Settings\PageIntegrations
		) {
			$integration = new Settings\PageIntegrations( static::get_instance() );
		}

		return $integration;
	}

	/**
	 * Provide an instance of the object, that should display provider settings in the Form Builder.
	 *
	 * @since 1.3.0
	 *
	 * @return null|\WPFormsGetResponse\Provider\Settings\FormBuilder
	 */
	public function get_form_builder() {

		static $builder = null;

		if (
			null === $builder ||
			! $builder instanceof Settings\FormBuilder
		) {
			$builder = new Settings\FormBuilder( static::get_instance() );
		}

		return $builder;
	}

	/**
	 * Get provider options.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	public function get_provider_options() {

		$providers = wpforms_get_providers_options();

		return ! empty( $providers[ $this->slug ] ) ? $providers[ $this->slug ] : [];
	}
}
