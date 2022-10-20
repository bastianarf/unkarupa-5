<?php

namespace WPFormsLocker\Lockers;

/**
 * Require unique answer for selected field types.
 *
 * @since 1.0.0
 */
class UniqueAnswer {

	/**
	 * Current form information.
	 *
	 * @var array $form_data
	 *
	 * @since 1.0.0
	 */
	public $form_data;

	/**
	 * UniqueAnswer constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Locker hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		\add_action( 'wpforms_frontend_js', array( $this, 'enqueue_frontend_scripts' ) );

		\add_filter( 'wpforms_settings_defaults', array( $this, 'register_settings_messages' ) );
		\add_filter( 'wpforms_frontend_strings', array( $this, 'register_frontend_messages' ) );
		\add_filter( 'wpforms_field_properties', array( $this, 'field_properties' ), 10, 2 );

		\add_filter( 'wpforms_process_initial_errors', array( $this, 'submit_form' ), 10, 2 );

		\add_action( 'wp_ajax_wpforms_form_locker_unique_answer', array( $this, 'is_unique_ajax' ) );
		\add_action( 'wp_ajax_nopriv_wpforms_form_locker_unique_answer', array( $this, 'is_unique_ajax' ) );
	}

	/**
	 * Set current form information for internal use.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form information.
	 */
	protected function set_form_data( $form_data ) {
		$this->form_data = $form_data;
	}

	/**
	 * Enqueue frontend scripts.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_frontend_scripts() {

		$min = \wpforms_get_min_suffix();

		\wp_enqueue_script(
			'wpforms-form-locker',
			\wpforms_form_locker()->url . "assets/js/wpforms-form-locker{$min}.js",
			array( 'wpforms' ),
			\WPFORMS_FORM_LOCKER_VERSION,
			true
		);

		\wp_localize_script(
			'wpforms-form-locker',
			'wpforms_form_locker',
			array(
				'ajaxurl' => \admin_url( 'admin-ajax.php' ),
			)
		);
	}

	/**
	 * Register validation messages in settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Array of current form settings.
	 *
	 * @return array
	 */
	public function register_settings_messages( $settings ) {

		$settings['validation']['validation-unique'] = array(
			'id'      => 'validation-unique',
			'name'    => \esc_html__( 'Unique Answer', 'wpforms-form-locker' ),
			'type'    => 'text',
			'default' => \esc_html__( 'The value must be unique.', 'wpforms-form-locker' ),
		);
		return $settings;
	}

	/**
	 * Modify javascript `wpforms_settings` properties on site front end.
	 *
	 * @since 1.0.0
	 *
	 * @param array $strings Array wpforms_setting properties.
	 *
	 * @return array
	 */
	public function register_frontend_messages( $strings ) {

		$strings['val_unique'] = \wpforms_setting( 'validation-unique', \esc_html__( 'The value must be unique.', 'wpforms-form-locker' ) );
		return $strings;
	}

	/**
	 * Conditionally add properties to an element enabling jQuery Validate.
	 *
	 * @since 1.0.0
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Field settings.
	 *
	 * @return mixed Field properties.
	 */
	public function field_properties( $properties, $field ) {

		if ( empty( $field['unique_answer'] ) ) {
			return $properties;
		}

		// In case of the `Name` field.
		if ( ! empty( $field['type'] ) && $field['type'] === 'name' ) {
			return $this->name_field_properties( $properties, $field );
		}

		$properties['inputs']['primary']['class'][]             = 'wpforms-novalidate-onkeyup';
		$properties['inputs']['primary']['data']['rule-unique'] = 'true';

		return $properties;
	}

	/**
	 * Conditionally add properties to the Name field elements enabling jQuery Validate.
	 *
	 * @since {VERSION}
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Field settings.
	 *
	 * @return mixed Field properties.
	 */
	public function name_field_properties( $properties, $field ) {

		if ( empty( $field['unique_answer'] ) ) {
			return $properties;
		}

		if ( empty( $field['type'] ) || $field['type'] !== 'name' ) {
			return $properties;
		}

		$format = ! empty( $field['format'] ) ? $field['format'] : 'simple';

		switch ( $format ) {
			case 'first-last':
				$inputs = [ 'first', 'last' ];
				break;
			case 'first-middle-last':
				$inputs = [ 'first', 'middle', 'last' ];
				break;
			default:
				$inputs = [ 'primary' ];
				break;
		}

		foreach ( $inputs as $input ) {
			$properties['inputs'][ $input ]['class'][]             = 'wpforms-novalidate-onkeyup';
			$properties['inputs'][ $input ]['class'][]             = 'wpforms-validation-group-member';
			$properties['inputs'][ $input ]['data']['rule-unique'] = 'true';
		}

		$properties['container']['class'][] = 'wpforms-field-unique';

		return $properties;
	}

	/**
	 * On form submit actions.
	 *
	 * @since 1.0.0
	 *
	 * @param array $errors    Form submit errors.
	 * @param array $form_data Form information.
	 *
	 * @return array
	 */
	public function submit_form( $errors, $form_data ) {

		$this->set_form_data( $form_data );

		$unique_enabled = $this->get_unique_enabled_field_ids();

		if ( empty( $unique_enabled ) ) {
			return $errors;
		}

		$field_ids = $this->get_non_unique_field_ids( $unique_enabled );

		if ( empty( $field_ids ) ) {
			return $errors;
		}

		foreach ( $field_ids as $field_id ) {
			$errors[ $this->form_data['id'] ][ $field_id ] = \wpforms_setting( 'validation-unique', \esc_html__( 'The value must be unique.', 'wpforms-form-locker' ) );
		}

		return $errors;
	}

	/**
	 * Validate a value via AJAX call.
	 *
	 * @since 1.0.0
	 */
	public function is_unique_ajax() {

		$form_id  = ! empty( $_POST['form_id'] ) ? \absint( $_POST['form_id'] ) : 0;
		$field_id = ! empty( $_POST['field_id'] ) ? \absint( $_POST['field_id'] ) : 0;

		if ( empty( $form_id ) ) {
			echo \wp_json_encode( true );
			exit();
		}

		$form_data = \wpforms()->form->get(
			$form_id,
			array( 'content_only' => true )
		);

		if ( empty( $form_data ) ) {
			echo \wp_json_encode( true );
			exit();
		}

		$this->set_form_data( $form_data );

		if ( $this->get_non_unique_field_ids( $field_id ) ) {
			echo \wp_json_encode( false );
			exit();
		}

		// jQuery Validation requires an answer to be JSON-encoded 'true' or 'false'.
		// Can't use 'wp_send_json_success()' here and above.
		echo \wp_json_encode( true );
		exit();
	}

	/**
	 * Get an field ids with enabled "Require Unique Answer" setting.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_unique_enabled_field_ids() {

		$field_ids = array();
		foreach ( $this->form_data['fields'] as $field ) {
			if ( empty( $field['unique_answer'] ) ) {
				continue;
			}
			if ( empty( $_POST['wpforms']['fields'][ $field['id'] ] ) ) {
				continue;
			}
			$field_ids[] = $field['id'];
		}

		if ( empty( $field_ids ) ) {
			return array();
		}

		return $field_ids;
	}

	/**
	 * Check a field id(s) for uniqueness.
	 * Return an array on non-unique field ids.
	 *
	 * @since 1.0.0
	 *
	 * @param array|int $field_ids Field id(s) to check for uniqueness.
	 *
	 * @return array
	 */
	public function get_non_unique_field_ids( $field_ids ) {

		if ( empty( $field_ids ) || empty( $this->form_data['id'] ) ) {
			return array();
		}

		$fields = \wpforms()->entry_fields->get_fields(
			[
				'number'   => -1,
				'form_id'  => $this->form_data['id'],
				'field_id' => $field_ids,
				'orderby'  => 'field_id',
			]
		);

		$non_unique = array();

		foreach ( $fields as $field ) {
			if ( in_array( $field->field_id, $non_unique, true ) ) {
				continue;
			}

			$raw_value = ! empty( $_POST['wpforms']['fields'][ $field->field_id ] ) ? wp_unslash( $_POST['wpforms']['fields'][ $field->field_id ] ) : ''; // phpcs:disable WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			// If submitted data is array we should check the combined value.
			$value = is_array( $raw_value ) ? sanitize_text_field( implode( ' ', array_filter( $raw_value ) ) ) : sanitize_textarea_field( $raw_value );

			if ( strtolower( $value ) === strtolower( $field->value ) ) {
				$non_unique[] = $field->field_id;
			}
		}

		return $non_unique;
	}

	/**
	 * Get an array of field types that support Unique Answer Locker.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_unique_answer_field_types() {

		return \apply_filters(
			'wpforms_form_locker_fields_get_unique_answer_field_types',
			array( 'text', 'name', 'email', 'url', 'password', 'phone' )
		);
	}

	/**
	 * Check if AJAX is a 'field_new' call for Unique Answer enabled field.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_unique_answer_enabled_new_field_ajax() {

		if ( ! \defined( 'DOING_AJAX' ) || ! \DOING_AJAX ) {
			return false;
		}

		if ( ! isset( $_POST['nonce'] ) || ! \wp_verify_nonce( \sanitize_key( $_POST['nonce'] ), 'wpforms-builder' ) ) {
			return false;
		}

		if ( empty( $_POST['action'] ) ) {
			return false;
		}

		$prefix = 'wpforms_new_field_';
		$action = \sanitize_text_field( \wp_unslash( $_POST['action'] ) );

		if ( 0 !== \strpos( $action, $prefix ) ) {
			return false;
		}

		$allowed_actions = \preg_filter( '/^/', $prefix, self::get_unique_answer_field_types() );

		if ( ! \in_array( $action, $allowed_actions, true ) ) {
			return false;
		}

		return true;
	}
}
