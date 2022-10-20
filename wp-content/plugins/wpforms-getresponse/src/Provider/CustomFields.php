<?php

namespace WPFormsGetResponse\Provider;

/**
 * Class CustomFields handles functionality for custom fields (mapping, formatting, etc.).
 *
 * @since 1.3.0
 */
class CustomFields {

	/**
	 * Array of form fields.
	 *
	 * @since 1.3.0
	 *
	 * @var array
	 */
	protected $fields = [];

	/**
	 * Submitted form content.
	 *
	 * @since 1.3.0
	 *
	 * @var array
	 */
	protected $entry = [];

	/**
	 * Form data and settings.
	 *
	 * @since 1.3.0
	 *
	 * @var array
	 */
	protected $form_data = [];

	/**
	 * CustomFields constructor.
	 *
	 * @since 1.3.0
	 *
	 * @param array $fields    Array of form fields.
	 * @param array $entry     Submitted form content.
	 * @param array $form_data Form data and settings.
	 */
	public function __construct( $fields, $entry, $form_data ) {

		$this->fields    = $fields;
		$this->entry     = $entry;
		$this->form_data = $form_data;
	}

	/**
	 * Apply special formatting for GetResponse fields.
	 *
	 * @since 1.3.0
	 *
	 * @param int   $field_id Form field ID.
	 * @param array $gr_field GetResponse custom field.
	 *
	 * @return array
	 */
	public function run( $field_id, $gr_field ) {

		if ( ! isset( $gr_field['type'], $this->fields[ $field_id ], $this->fields[ $field_id ]['type'], $this->fields[ $field_id ]['value'] ) ) {
			return [];
		}

		if ( ! $this->is_normalized_types( $gr_field['type'], $this->fields[ $field_id ]['type'] ) ) {
			return [];
		}

		$value = $this->get_form_field_value( $field_id );
		if ( wpforms_is_empty_string( $value ) ) {
			return [];
		}

		$result = $this->convert( $value, $field_id, $gr_field );

		/**
		 * Filter GetResponse custom field value(s).
		 *
		 * @since 1.3.0
		 *
		 * @param array  $result        Formatted field value(s).
		 * @param string $gr_field_type GetResponse field type.
		 * @param int    $field_id      Form field ID.
		 * @param array  $data          All data about form submission.
		 */
		return apply_filters(
			'wpforms_getresponse_provider_custom_fields_value',
			$result,
			$gr_field['type'],
			$field_id,
			[
				'form_data' => $this->form_data,
				'fields'    => $this->fields,
				'entry'     => $this->entry,
			]
		);
	}

	/**
	 * Convert a submitted value to value for GetResponse custom field.
	 *
	 * @since 1.3.0
	 *
	 * @param string $value    Submitted field value.
	 * @param int    $field_id Form field ID.
	 * @param array  $gr_field GetResponse custom field.
	 */
	protected function convert( $value, $field_id, $gr_field ) {

		$gr_multiple = $this->is_multiple_values( $gr_field['format'] );
		$values      = $gr_multiple ? explode( "\n", $value ) : [ $value ];

		// Unique formatting for specific GetResponse field types.
		switch ( $gr_field['type'] ) {
			case 'date':
			case 'datetime':
				$date_format = ( 'datetime' === $gr_field['type'] ) ? 'Y-m-d H:i:s' : 'Y-m-d';
				$values      = $this->format_date( $this->form_data['fields'][ $field_id ], $this->fields[ $field_id ], $date_format );
				break;

			case 'number':
				// GetResponse allows to add integer and float numbers.
				$values = array_filter( $values, 'is_numeric' );
				$values = array_map( 'floatval', $values );
				break;

			case 'phone':
				// Skip US format, because phone prefix is required.
				$values = ( 'us' !== $this->form_data['fields'][ $field_id ]['format'] ) ? $values : [];
				break;

			case 'ip':
				$values = array_filter(
					$values,
					static function( $ip ) {
						return filter_var( $ip, FILTER_VALIDATE_IP );
					}
				);
				break;
		}

		/**
		 * Check if submitted values/choices are all present in GetResponse field `values` property.
		 * If has a difference - we need to skip mapping for this field, because request will fail.
		 */
		$gr_predefined = $this->is_predefined_values( $gr_field['format'] );
		$values        = $gr_predefined && ! empty( array_diff( $values, $gr_field['values'] ) ) ? [] : $values;

		return $values;
	}

	/**
	 * Retrieve a form field value.
	 *
	 * @since 1.3.0
	 *
	 * @param int $field_id The Field ID.
	 *
	 * @return string
	 */
	protected function get_form_field_value( $field_id ) {

		// Submitted field value.
		$field_value = $this->fields[ $field_id ]['value'];

		// Additional operations for some specific form field types.
		switch ( $this->fields[ $field_id ]['type'] ) {
			case 'payment-checkbox':
			case 'payment-multiple':
			case 'payment-select':
				// Make a delimiter like in `Checkbox` field and convert a currency symbol.
				$field_value = str_replace( "\r\n", "\n", $field_value );
				$field_value = wpforms_decode_string( $field_value );
				break;

			case 'payment-single':
			case 'payment-total':
				// Additional conversion for correct currency symbol display.
				$field_value = wpforms_decode_string( $field_value );
				break;
		}

		return $field_value;
	}

	/**
	 * Convert a date value into an expected format.
	 *
	 * @since 1.3.0
	 *
	 * @param array  $field_data      Field data.
	 * @param array  $field           Field attributes.
	 * @param string $expected_format Date format.
	 *
	 * @return array
	 */
	protected function format_date( $field_data, $field, $expected_format ) {

		$result = [];

		// Skip for `time` format.
		if ( ! empty( $field_data['format'] ) && $field_data['format'] === 'time' ) {
			return $result;
		}

		// Try to parse a value with date string.
		$date_time = false;
		if ( ! empty( $field_data['date_format'] ) ) {
			$date_time = date_create_from_format( $field_data['date_format'], $field['value'] );
		}

		// Fallback with using timestamp value.
		if ( ! $date_time && ! empty( $field['unix'] ) ) {
			$date_time = date_create( '@' . $field['unix'] );
		}

		// If has a DateTime object - return a date formatted according to expected format.
		if ( $date_time ) {
			$result[] = $date_time->format( $expected_format );
		}

		return $result;
	}

	/**
	 * Some GetResponse field types require value validation.
	 * Therefore, for those types we allow limited WPForms field types.
	 *
	 * @since 1.3.0
	 *
	 * @param string $gr_field_type   GetResponse field type name.
	 * @param string $form_field_type Form field type name.
	 *
	 * @return bool
	 */
	protected function is_normalized_types( $gr_field_type, $form_field_type ) {

		// Specific mapping for some types. Key is a GetResponse field type, value - WPForms field type.
		$types = [
			'url'      => 'url',
			'phone'    => 'phone',
			'date'     => 'date-time',
			'datetime' => 'date-time',
		];

		if ( ! isset( $types[ $gr_field_type ] ) ) {
			return true;
		}

		return $form_field_type === $types[ $gr_field_type ];
	}

	/**
	 * Determine if passed GetResponse field must have multiple values.
	 *
	 * @since 1.3.0
	 *
	 * @param string $name Format name.
	 *
	 * @return bool
	 */
	protected function is_multiple_values( $name ) {

		$formats = [
			'checkbox'     => true,
			'multi_select' => true,
		];

		return isset( $formats[ $name ] );
	}

	/**
	 * Determine if passed GetResponse field must have predefined values.
	 *
	 * @since 1.3.0
	 *
	 * @param string $name Format name.
	 *
	 * @return bool
	 */
	protected function is_predefined_values( $name ) {

		$formats = [
			'radio'         => true,
			'checkbox'      => true,
			'single_select' => true,
			'multi_select'  => true,
		];

		return isset( $formats[ $name ] );
	}
}
