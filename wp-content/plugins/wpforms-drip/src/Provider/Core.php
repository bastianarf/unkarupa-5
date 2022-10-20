<?php

namespace WPFormsDrip\Provider;

/**
 * Class Core.
 *
 * @since 1.0.0
 */
class Core extends \WPForms\Providers\Provider\Core {

	/**
	 * Custom priority for a provider, that affects loading/placement order.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	const PRIORITY = 21;

	/**
	 * Core constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct(
			array(
				'slug' => 'drip',
				'name' => esc_html__( 'Drip', 'wpforms-drip' ),
				'icon' => WPFORMS_DRIP_URL . 'assets/images/addon-icon-drip.png',
			)
		);
	}

	/**
	 * @inheritdoc
	 *
	 * @since 1.0.0
	 */
	public function get_process() {

		static $process;

		if ( ! $process ) {
			$process = new Process( static::get_instance() );
		}

		return $process;
	}

	/**
	 * @inheritdoc
	 *
	 * @since 1.0.0
	 */
	public function get_page_integrations() {

		static $integration;

		if ( ! $integration ) {
			$integration = new PageIntegrations( static::get_instance() );
		}

		return $integration;
	}

	/**
	 * @inheritdoc
	 *
	 * @since 1.0.0
	 */
	public function get_form_builder() {

		static $builder;

		if ( ! $builder ) {
			$builder = new FormBuilder( static::get_instance() );
		}

		return $builder;
	}

	/**
	 * Get HTML fields needed to add a new provider account.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public static function get_add_new_account_fields_html() {

		$core_name = static::get_instance()->name;

		$html = '';

		$html .= \sprintf(
			'<input type="text" name="account_name" placeholder="%s">',
			\sprintf(
				/* translators: %s - current provider name. */
				\esc_html__( '%s Account Name', 'wpforms-drip' ),
				\esc_html( $core_name )
			)
		);

		$html .= \sprintf(
			'<input type="text" name="api_token" placeholder="%s" class="wpforms-required">',
			\sprintf(
				/* translators: %s - current provider name. */
				\esc_html__( '%s API Token', 'wpforms-drip' ),
				\esc_html( $core_name )
			)
		);

		$html .= '<p class="description">';
		$html .= sprintf(
			wp_kses(
				/* translators: %s - URL to a Drip User Info page. */
				__( 'The API Token can be found in your Drip account settings. <a href="%s" target="_blank" rel="noopener noreferrer">Learn More</a>.', 'wpforms-drip' ),
				array(
					'code' => [],
					'a'    => array(
						'href'   => [],
						'target' => [],
						'rel'    => [],
					),
				)
			),
			'https://wpforms.com/docs/how-to-install-and-use-the-drip-addon-in-wpforms/#initial-setup'
		);

		$html .= '</p>';
		$html .= sprintf( '<p class="error hidden">%s</p>', esc_html__( 'Something went wrong while performing an AJAX request.', 'wpforms-drip' ) );

		return $html;
	}
}
