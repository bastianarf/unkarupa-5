<?php

/**
 * Travel Request Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Travel_Request extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Travel Request Form', 'wpforms-form-templates-pack' );
		$this->slug = 'travel-request';
		$this->data = array(
			'field_id' => 12,
			'fields'   => array(
				1  => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Employee Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				2  => array(
					'id'    => '2',
					'type'  => 'text',
					'label' => esc_html__( 'Employee ID', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				10 => array(
					'id'    => '10',
					'type'  => 'text',
					'label' => esc_html__( 'Manager / Supervisor', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				4  => array(
					'id'    => '4',
					'type'  => 'text',
					'label' => esc_html__( 'Reason for travel', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				5  => array(
					'id'      => '5',
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Travel arrangements needed', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Flight', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Hotel', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Rental Car', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Other', 'wpforms-form-templates-pack' ),
						),
					),
				),
				6  => array(
					'id'    => '6',
					'type'  => 'text',
					'label' => esc_html__( 'Departure City', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				8  => array(
					'id'    => '8',
					'type'  => 'text',
					'label' => esc_html__( 'Arrival City', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				7  => array(
					'id'            => '7',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Departure Date', 'wpforms-form-templates-pack' ),
					'format'        => 'date-time',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'datepicker',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				9  => array(
					'id'            => '9',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Return Date', 'wpforms-form-templates-pack' ),
					'format'        => 'date-time',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'datepicker',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				11 => array(
					'id'    => '11',
					'type'  => 'textarea',
					'label' => esc_html__( 'Additional Information', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
			),
			'settings' => array(
				'antispam'                    => '1',
				'confirmation_message_scroll' => '1',
				'submit_text_processing'      => esc_html__( 'Sending...', 'wpforms-form-templates-pack' ),
			),
			'meta'     => array(
				'template' => $this->slug,
			),
		);
	}
}

new WPForms_Template_Travel_Request;
