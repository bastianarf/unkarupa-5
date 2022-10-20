<?php

namespace WPFormsGetResponse;

use WPForms\Providers\Loader as ProvidersLoader;

/**
 * Class Plugin that loads the whole plugin.
 *
 * @since 1.3.0
 */
final class Plugin {

	/**
	 * Provider Core instance.
	 *
	 * @since 1.3.0
	 *
	 * @var \WPFormsGetResponse\Provider\Core
	 */
	public $provider;

	/**
	 * Plugin constructor.
	 *
	 * @since 1.3.0
	 */
	private function __construct() {}

	/**
	 * Get a single instance of the addon.
	 *
	 * @since 1.3.0
	 *
	 * @return \WPFormsGetResponse\Plugin
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
	 * @since 1.3.0
	 */
	public function init() {

		$this->hooks();

		return $this;
	}

	/**
	 * Hooks.
	 *
	 * @since 1.3.0
	 */
	protected function hooks() {

		add_action( 'wpforms_loaded', [ $this, 'init_components' ] );
		add_action( 'admin_notices', [ $this, 'upgrade_notice' ] );
		add_action( 'update_option_wpforms_providers', [ $this, 'flush_cache' ] );
		add_filter( 'wpforms_helpers_templates_include_html_located', [ $this, 'templates' ], 10, 4 );
	}

	/**
	 * Init components.
	 *
	 * @since 1.3.0
	 */
	public function init_components() {

		$this->provider = Provider\Core::get_instance();

		ProvidersLoader::get_instance()->register(
			$this->provider
		);
	}

	/**
	 * Display upgrade notice for sites using the v2 API integration.
	 *
	 * @since 1.3.0
	 */
	public function upgrade_notice() {

		// Only consider showing to admin users.
		if ( ! is_super_admin() ) {
			return;
		}

		$providers = wpforms_get_providers_options();

		// Only display if site has a v2 integration configured.
		if ( empty( $providers['getresponse'] ) ) {
			return;
		}

		?>
		<div class="notice notice-warning wpforms-getresponse-update-notice">
			<p>
				<?php esc_html_e( 'Your forms are currently using an outdated GetResponse integration that is no longer supported. Please update your forms to use the new integration to avoid losing subscribers.', 'wpforms-getresponse' ); ?>
				<strong>
					<a href="https://wpforms.com/docs/how-to-install-and-use-getresponse-addon-with-wpforms/" target="_blank" rel="noopener noreferrer">
						<?php esc_html_e( 'Click here for more details.', 'wpforms-getresponse' ); ?>
					</a>
				</strong>
			</p>
		</div>
		<?php
	}

	/**
	 * Flush a transient cache on providers data change.
	 *
	 * @since 1.3.0
	 */
	public function flush_cache() {

		// Call it when account was added or disconnected.
		if (
			did_action( "wp_ajax_wpforms_settings_provider_add_{$this->provider->slug}" ) ||
			did_action( "wp_ajax_wpforms_settings_provider_disconnect_{$this->provider->slug}" )
		) {
			delete_transient( "wpforms_providers_{$this->provider->slug}_ajax_accounts_get" );
		}
	}

	/**
	 * Change a template location.
	 *
	 * @since 1.3.0
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
			( 0 === strpos( $template, WPFORMS_GETRESPONSE_PATH ) ) &&
			is_readable( $template )
		) {
			return $template;
		}

		return $located;
	}
}
