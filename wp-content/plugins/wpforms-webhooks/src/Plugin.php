<?php

namespace WPFormsWebhooks;

/**
 * Webhooks plugin class.
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * FormBuilder instance.
	 *
	 * @since 1.0.0
	 *
	 * @var \WPFormsWebhooks\Admin\FormBuilder
	 */
	public $form_builder;

	/**
	 * Settings instance.
	 *
	 * @since 1.0.0
	 *
	 * @var \WPFormsWebhooks\Admin\Settings
	 */
	public $settings;

	/**
	 * Process instance.
	 *
	 * @since 1.0.0
	 *
	 * @var \WPFormsWebhooks\Process
	 */
	public $process;

	/**
	 * Constructor method.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 *
	 * @return \WPFormsWebhooks\Plugin
	 */
	public static function get_instance() {

		static $instance = null;

		if (
			null === $instance ||
			! $instance instanceof self
		) {
			$instance = ( new self() )->init();
		}

		return $instance;
	}

	/**
	 * All the actual plugin loading is done here.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->hooks();

		return $this;
	}

	/**
	 * Hooks.
	 *
	 * @since 1.0.0
	 */
	protected function hooks() {

		add_action( 'wpforms_loaded', [ $this, 'init_components' ] );
		add_action( 'wpforms_updater', [ $this, 'updater' ] );
		add_filter( 'wpforms_helpers_templates_include_html_located', [ $this, 'templates' ], 10, 4 );
	}

	/**
	 * Init components.
	 *
	 * @since 1.0.0
	 */
	public function init_components() {

		// Available to top-tier level license only.
		if ( ! in_array( wpforms_get_license_type(), [ 'elite', 'agency', 'ultimate' ], true ) ) {
			return;
		}

		if (
			wpforms_is_admin_page( 'builder' ) ||
			wp_doing_ajax()
		) {
			$this->form_builder = new Admin\FormBuilder();
			$this->settings     = new Admin\Settings();

			$this->form_builder->init();
			$this->settings->init();
		}

		$this->process = new Process();
		$this->process->init();
	}

	/**
	 * Load the addon updater.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key License key.
	 */
	public function updater( $key ) {

		new \WPForms_Updater(
			[
				'plugin_name' => 'WPForms Webhooks',
				'plugin_slug' => 'wpforms-webhooks',
				'plugin_path' => plugin_basename( WPFORMS_WEBHOOKS_FILE ),
				'plugin_url'  => trailingslashit( WPFORMS_WEBHOOKS_URL ),
				'remote_url'  => WPFORMS_UPDATER_API,
				'version'     => WPFORMS_WEBHOOKS_VERSION,
				'key'         => $key,
			]
		);
	}

	/**
	 * Change a template location.
	 *
	 * @since 1.0.0
	 *
	 * @param string $located  Template location.
	 * @param string $template Template.
	 * @param array  $args     Arguments.
	 * @param bool   $extract  Extract arguments.
	 *
	 * @return string
	 */
	public function templates( $located, $template, $args, $extract ) {

		// Checking if `$template` is an absolute path and passed from this plugin.
		if (
			( 0 === strpos( $template, WPFORMS_WEBHOOKS_PATH ) ) &&
			is_readable( $template )
		) {
			return $template;
		}

		return $located;
	}

	/**
	 * Retrieve available request methods.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_available_methods() {

		return [
			'get'    => 'GET',
			'post'   => 'POST',
			'put'    => 'PUT',
			'patch'  => 'PATCH',
			'delete' => 'DELETE',
		];
	}

	/**
	 * Retrieve available request formats.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_available_formats() {

		return [
			'json' => esc_html__( 'JSON', 'wpforms-webhooks' ),
			'form' => esc_html__( 'FORM', 'wpforms-webhooks' ),
		];
	}
}
