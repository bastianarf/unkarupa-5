<?php

namespace WPFormsGeolocation\Front;

use WPFormsGeolocation\Map;
use WPFormsGeolocation\PlacesProviders\ProvidersFactory;

/**
 * Class Fields.
 *
 * @since 2.0.0
 */
class Fields {

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
	 * Enqueue autocomplete scripts.
	 *
	 * @since 2.0.0
	 *
	 * @var bool
	 */
	private static $are_scripts_included = false;

	/**
	 * Allow field types.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	public static $field_types = [ 'text', 'address' ];

	/**
	 * Fields constructor.
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

		add_action( 'wpforms_frontend_output_before', [ $this, 'init_autocomplete' ] );
		add_action( 'wpforms_wp_footer', [ $this, 'settings' ] );

		foreach ( self::$field_types as $field_type ) {
			add_filter( 'wpforms_field_properties_' . $field_type, [ $this, $field_type . '_field_attributes' ], 10, 2 );
			add_action( 'wpforms_display_field_' . $field_type, [ $this, 'map_before_field' ], 9 );
			add_action( 'wpforms_display_field_' . $field_type, [ $this, 'map_after_field' ], 11 );
		}

		add_filter( 'wpforms_field_data', [ $this, 'disable_limit' ], 1000 );
	}

	/**
	 * Init autocomplete.
	 *
	 * @since 2.0.0
	 *
	 * @param array $form_data Form data.
	 */
	public function init_autocomplete( $form_data ) {

		if ( self::$are_scripts_included ) {
			return;
		}

		if ( ! $this->has_autocomplete_field( [ $form_data ] ) ) {
			return;
		}

		$provider = $this->providers_factory->get_current_provider();

		if ( ! $provider ) {
			return;
		}

		$provider->init();

		self::$are_scripts_included = true;
	}

	/**
	 * Show a map before a field.
	 *
	 * @since 2.0.0
	 *
	 * @param array $field Field.
	 */
	public function map_before_field( $field ) {

		if ( empty( $field['enable_address_autocomplete'] ) || empty( $field['display_map'] ) ) {
			return;
		}

		if ( empty( $field['map_position'] ) || $field['map_position'] !== 'above' ) {
			return;
		}

		$provider = $this->providers_factory->get_current_provider();

		if ( ! $provider || ! $provider->is_active() ) {
			return;
		}

		$this->map->print_map( $this->get_map_size( $field ) );
	}

	/**
	 * Show a map after a field.
	 *
	 * @since 2.0.0
	 *
	 * @param array $field Field.
	 */
	public function map_after_field( $field ) {

		if ( empty( $field['enable_address_autocomplete'] ) || empty( $field['display_map'] ) ) {
			return;
		}

		if ( empty( $field['map_position'] ) || $field['map_position'] !== 'below' ) {
			return;
		}

		$provider = $this->providers_factory->get_current_provider();

		if ( ! $provider || ! $provider->is_active() ) {
			return;
		}

		$this->map->print_map( $this->get_map_size( $field ) );
	}

	/**
	 * Get map size.
	 *
	 * @since 2.0.0
	 *
	 * @param array $field Field settings.
	 *
	 * @return string
	 */
	private function get_map_size( $field ) {

		return ! empty( $field['size'] ) ? $field['size'] : '';
	}

	/**
	 * Add properties for text field.
	 *
	 * @since 2.0.0
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Field.
	 *
	 * @return array
	 */
	public function text_field_attributes( $properties, $field ) {

		if ( ! empty( $field['enable_address_autocomplete'] ) ) {
			$properties['inputs']['primary']['attr']['data-autocomplete'] = true;
		}

		if ( ! empty( $field['display_map'] ) ) {
			$properties['inputs']['primary']['attr']['data-display-map'] = true;
		}

		return $properties;
	}

	/**
	 * Disable Limit Length when Address Autocomplete option enabled.
	 *
	 * @since 2.0.0
	 *
	 * @param array $field Current field.
	 *
	 * @return array
	 */
	public function disable_limit( $field ) {

		if ( empty( $field['type'] ) || $field['type'] !== 'text' ) {
			return $field;
		}

		if ( empty( $field['enable_address_autocomplete'] ) ) {
			return $field;
		}

		unset( $field['limit_enabled'] );
		unset( $field['limit_count'] );
		unset( $field['limit_mode'] );

		return $field;
	}

	/**
	 * Add properties for address field.
	 *
	 * @since 2.0.0
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Field.
	 *
	 * @return array
	 */
	public function address_field_attributes( $properties, $field ) {

		if ( ! empty( $field['enable_address_autocomplete'] ) ) {
			$properties['inputs']['address1']['attr']['data-autocomplete'] = true;
		}

		if ( ! empty( $field['display_map'] ) ) {
			$properties['inputs']['address1']['attr']['data-display-map'] = true;
		}

		return $properties;
	}

	/**
	 * Print a settings for JS.
	 *
	 * @since 2.0.0
	 *
	 * @param array $forms Page forms.
	 */
	public function settings( $forms ) {

		if ( ! self::$are_scripts_included ) {
			return;
		}

		$settings = (array) apply_filters(
			'wpforms_geolocation_front_fields_settings',
			array_merge(
				$this->map->get_settings(),
				[
					'current_location' => wpforms_setting( 'geolocation-current-location', false ),
					'states'           => WPFORMS_GEOLOCATION_URL . 'assets/json/states.json',
				]
			),
			$forms
		);

		/*
		 * Below we do our own implementation of wp_localize_script in an effort
		 * to be better compatible with caching plugins which were causing
		 * conflicts.
		 */
		echo "<script type='text/javascript'>\n";
		echo "/* <![CDATA[ */\n";
		echo 'var wpforms_geolocation_settings = ' . wp_json_encode( $settings ) . "\n";
		echo "/* ]]> */\n";
		echo "</script>\n";
	}

	/**
	 * Forms has a autocomplete field.
	 *
	 * @since 2.0.0
	 *
	 * @param array $forms Forms.
	 *
	 * @return bool
	 */
	private function has_autocomplete_field( $forms ) {

		foreach ( $forms as $form ) {
			if ( empty( $form['fields'] ) ) {
				continue;
			}

			foreach ( $form['fields'] as $field ) {
				if ( ! in_array( $field['type'], self::$field_types, true ) || empty( $field['enable_address_autocomplete'] ) ) {
					continue;
				}

				return true;
			}
		}

		return false;
	}
}
