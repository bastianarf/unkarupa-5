<?php

/**
 * Enrollment Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Enrollment extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Enrollment Form', 'wpforms-form-templates-pack' );
		$this->slug = 'enrollment';
		$this->data = array(
			'field_id' => 11,
			'fields'   => array(
				1  => array(
					'id'            => '1',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Anticipated Start Date', 'wpforms-form-templates-pack' ),
					'format'        => 'date',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'dropdown',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				2  => array(
					'id'       => '2',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				3  => array(
					'id'     => '3',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				4  => array(
					'id'       => '4',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				5  => array(
					'id'     => '5',
					'type'   => 'address',
					'label'  => esc_html__( 'Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
				),
				6  => array(
					'id'      => '6',
					'type'    => 'radio',
					'label'   => esc_html__( 'Gender', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Male', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Female', 'wpforms-form-templates-pack' ),
						),
					),
				),
				7  => array(
					'id'            => '7',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Birthday', 'wpforms-form-templates-pack' ),
					'format'        => 'date',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'dropdown',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				8  => array(
					'id'    => '8',
					'type'  => 'text',
					'label' => esc_html__( 'High School Name', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				9  => array(
					'id'     => '9',
					'type'   => 'address',
					'label'  => esc_html__( 'High School Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
				),
				10 => array(
					'id'    => '10',
					'type'  => 'text',
					'label' => esc_html__( 'GPA', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Enrollment;
