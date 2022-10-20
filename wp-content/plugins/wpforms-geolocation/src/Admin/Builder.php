<?php

namespace WPFormsGeolocation\Admin;

use WPFormsGeolocation\Map;
use WPFormsGeolocation\PlacesProviders\ProvidersFactory;

/**
 * Class Builder.
 *
 * @since 2.0.0
 */
class Builder {

	/**
	 * Provider factory.
	 *
	 * @since 2.0.0
	 *
	 * @var ProvidersFactory
	 */
	private $providers_factory;

	/**
	 * Map.
	 *
	 * @since 2.0.0
	 *
	 * @var Map
	 */
	private $map;

	/**
	 * Builder constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param ProvidersFactory $providers_factory Provider factory.
	 * @param Map              $map               Map.
	 */
	public function __construct( ProvidersFactory $providers_factory, Map $map ) {

		$this->providers_factory = $providers_factory;
		$this->map               = $map;
	}

	/**
	 * Init hooks.
	 *
	 * @since 2.0.0
	 */
	public function hooks() {

		if ( wp_doing_ajax() ) {
			add_action( 'wpforms_field_options_bottom_advanced-options', [ $this, 'register_fields' ], 10, 2 );
		}

		if ( ! wpforms_is_admin_page( 'builder' ) ) {
			return;
		}

		add_action( 'wpforms_builder_enqueues', [ $this, 'enqueue_styles' ] );
		add_action( 'wpforms_builder_enqueues', [ $this, 'enqueue_scripts' ] );
		add_action( 'wpforms_field_options_bottom_advanced-options', [ $this, 'register_fields' ], 10, 2 );
		add_filter( 'wpforms_builder_strings', [ $this, 'builder_strings' ] );
	}

	/**
	 * Enqueue styles.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_styles() {

		$min = wpforms_get_min_suffix();

		wp_enqueue_style(
			'wpforms-geolocation-builder',
			WPFORMS_GEOLOCATION_URL . "assets/css/admin/wpforms-geolocation-builder{$min}.css",
			[],
			WPFORMS_GEOLOCATION_VERSION
		);
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_scripts() {

		$min = wpforms_get_min_suffix();

		wp_enqueue_script(
			'wpforms-geolocation-builder',
			WPFORMS_GEOLOCATION_URL . "assets/js/admin/wpforms-geolocation-builder{$min}.js",
			[ 'jquery', 'jquery-confirm' ],
			WPFORMS_GEOLOCATION_VERSION,
			false
		);
	}

	/**
	 * Display geolocation options.
	 *
	 * @since 2.0.0
	 *
	 * @param array  $field    Field data.
	 * @param object $instance Builder instance.
	 */
	public function register_fields( $field, $instance ) {

		// Limit to our specific field types.
		if ( ! isset( $field['type'] ) || ! in_array( $field['type'], [ 'text', 'address' ], true ) ) {
			return;
		}

		$provider  = $this->providers_factory->get_current_provider();
		$is_active = $provider ? $provider->is_active() : false;

		$instance->field_element(
			'row',
			$field,
			[
				'slug'    => 'enable_address_autocomplete',
				'class'   => ! $is_active ? 'wpforms-geolocation-fill-settings' : '',
				'content' => $instance->field_element(
					'checkbox',
					$field,
					[
						'slug'  => 'enable_address_autocomplete',
						'value' => $is_active && isset( $field['enable_address_autocomplete'] ) ? $field['enable_address_autocomplete'] : '0',
						'desc'  => esc_html__( 'Enable Address Autocomplete', 'wpforms-geolocation' ),
					],
					false
				),
			]
		);
		$instance->field_element(
			'row',
			$field,
			[
				'slug'    => 'display_map',
				'class'   => ! $is_active || empty( $field['enable_address_autocomplete'] ) ? 'wpforms-field-option-row-hide' : '',
				'content' => $instance->field_element(
					'checkbox',
					$field,
					[
						'slug'  => 'display_map',
						'value' => $is_active && isset( $field['display_map'] ) ? '1' : '0',
						'desc'  => esc_html__( 'Display Map', 'wpforms-geolocation' ),
					],
					false
				),
			]
		);
		$instance->field_element(
			'row',
			$field,
			[
				'slug'    => 'map_position',
				'class'   => ! $is_active || empty( $field['display_map'] ) ? 'wpforms-field-option-row-hide' : '',
				'content' => $instance->field_element(
					'select',
					$field,
					[
						'slug'    => 'map_position',
						'value'   => isset( $field['map_position'] ) ? $field['map_position'] : 'above',
						'desc'    => esc_html__( 'Map location', 'wpforms-geolocation' ),
						'options' => [
							'above' => esc_html__( 'Above field', 'wpforms-geolocation' ),
							'below' => esc_html__( 'Below field', 'wpforms-geolocation' ),
						],
					],
					false
				),
			]
		);
	}

	/**
	 * Add a custom JS setting for builder.
	 *
	 * @since 2.0.0
	 *
	 * @param array $strings List of settings.
	 *
	 * @return array
	 */
	public function builder_strings( $strings ) {

		$strings['places_provider_required'] = sprintf(
			'<p>%s</p><p>%s</p>',
			esc_html__( 'Places API connection is required when using the autocomplete field.', 'wpforms-geolocation' ),
			esc_html__( 'To proceed please go to the WPForms Settings > Geolocation page, choose a Places Provider and set credentials for it.', 'wpforms-geolocation' )
		);
		$strings['map']                      = wp_kses_post( $this->map->get_map( 'medium' ) );
		$strings['disable_limit_length']     = esc_html__( 'Limit Length option is not compatible with the Address Autocomplete. It will be disabled.', 'wpforms-geolocation' );

		return $strings;
	}
}
