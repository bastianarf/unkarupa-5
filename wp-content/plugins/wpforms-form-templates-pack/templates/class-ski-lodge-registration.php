<?php

/**
 * Ski Lodge Registration Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Ski_Lodge_Registration extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Ski Lodge Registration Form', 'wpforms-form-templates-pack' );
		$this->slug = 'ski-lodge-registration';
		$this->data = array(
			'field_id' => 12,
			'fields'   => array(
				1  => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				2  => array(
					'id'     => '2',
					'type'   => 'address',
					'label'  => esc_html__( 'Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
				),
				3  => array(
					'id'       => '3',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				4  => array(
					'id'     => '4',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				5  => array(
					'id'            => '5',
					'type'          => 'divider',
					'label'         => esc_html__( 'Booking Information', 'wpforms-form-templates-pack' ),
					'label_disable' => '1',
				),
				6  => array(
					'id'    => '6',
					'type'  => 'text',
					'label' => esc_html__( 'Ski Resort', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				7  => array(
					'id'      => '7',
					'type'    => 'select',
					'label'   => esc_html__( 'Preferred Lodge', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Lodge A', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Lodge B', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Lodge C', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				8  => array(
					'id'            => '8',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Check In Date', 'wpforms-form-templates-pack' ),
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
					'label'         => esc_html__( 'Check Out Date', 'wpforms-form-templates-pack' ),
					'format'        => 'date-time',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'datepicker',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				10 => array(
					'id'    => '10',
					'type'  => 'number',
					'label' => esc_html__( 'Total number of guests', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				11 => array(
					'id'    => '11',
					'type'  => 'textarea',
					'label' => esc_html__( 'Special Requests', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Ski_Lodge_Registration;
