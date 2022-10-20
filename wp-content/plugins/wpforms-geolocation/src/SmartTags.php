<?php

namespace WPFormsGeolocation;

/**
 * Class SmartTags.
 *
 * @since 2.0.0
 */
class SmartTags {

	/**
	 * Hooks.
	 *
	 * @since 2.0.0
	 */
	public function hooks() {

		add_filter( 'wpforms_email_message', [ $this, 'entry_location' ], 10, 2 );
		add_filter( 'wpforms_smart_tags', [ $this, 'register_tag' ] );
	}

	/**
	 * Register the new {entry_geolocation} smart tag.
	 *
	 * @since 2.0.0
	 *
	 * @param array $tags List of tags.
	 *
	 * @return array $tags List of tags.
	 */
	public function register_tag( $tags ) {

		$tags['entry_geolocation'] = esc_html__( 'Entry Location', 'wpforms-geolocation' );

		return $tags;
	}

	/**
	 * Check for {entry_geolocation} Smart Tag inside email messages.
	 *
	 * @since 2.0.0
	 *
	 * @param string $message Theme email message.
	 * @param object $email   WPForms_WP_Emails.
	 *
	 * @return string
	 */
	public function entry_location( $message, $email ) {

		// Check to see if SmartTag is in email notification message.
		if ( strpos( $message, '{entry_geolocation}' ) === false ) {
			return $message;
		}

		// Attempt to fetch location data, which should be in the database.
		$location = wpforms()->entry_meta->get_meta(
			[
				'entry_id' => $email->entry_id,
				'type'     => 'location',
				'number'   => 1,
			]
		);

		if ( empty( $location ) ) {
			return str_replace( '{entry_geolocation}', '', $message );
		}

		$location = json_decode( $location[0]->data, true );

		$geo = $email->get_content_type() === 'text/plain'
			? $this->plain_entry_location( $location )
			: $this->html_entry_location( $location, $email );

		return str_replace( '{entry_geolocation}', $geo, $message );
	}

	/**
	 * Entry location for plain/text content type mail.
	 *
	 * @since 2.0.0
	 *
	 * @param array $location Location information.
	 *
	 * @return string
	 */
	private function plain_entry_location( $location ) {

		$geo = '--- ' . esc_html__( 'Entry Geolocation', 'wpforms-geolocation' ) . " ---\r\n";

		$geo .= $location['city'] . ', ' . $location['region'] . ', ' . $location['country'] . "\r\n";

		return $geo . $location['latitude'] . ', ' . $location['longitude'] . "\r\n\r\n";
	}


	/**
	 * Entry location for html content type mail.
	 *
	 * @since 2.0.0
	 *
	 * @param array  $location Location information.
	 * @param object $email    WPForms_WP_Emails.
	 *
	 * @return string
	 */
	private function html_entry_location( $location, $email ) {

		ob_start();
		$email->get_template_part( 'field', $email->get_template(), true );

		$geo   = ob_get_clean();
		$geo   = str_replace( '{field_name}', esc_html__( 'Entry Geolocation', 'wpforms-geolocation' ), $geo );
		$value = implode(
			', ',
			array_filter(
				[
					! empty( $location['city'] ) ? $location['city'] : '',
					! empty( $location['region'] ) ? $location['region'] : '',
					! empty( $location['country'] ) ? $location['country'] : '',
				]
			)
		);

		if ( ! empty( $location['latitude'] ) && ! empty( $location['longitude'] ) ) {
			$value .= '<br>' . $location['latitude'] . ', ' . $location['longitude'];
		}

		return (string) str_replace( '{field_value}', $value, $geo );
	}
}
