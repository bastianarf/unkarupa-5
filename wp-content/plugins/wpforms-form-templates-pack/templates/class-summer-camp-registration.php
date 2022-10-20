<?php

/**
 * Summer Camp Registration Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Summer_Camp_Registration extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Summer Camp Registration Form', 'wpforms-form-templates-pack' );
		$this->slug = 'summer-camp-registration';
		$this->data = array(
			'field_id' => 15,
			'fields'   => array(
				1  => array(
					'id'            => '1',
					'type'          => 'divider',
					'label'         => esc_html__( 'Child Information', 'wpforms-form-templates-pack' ),
					'label_disable' => '1',
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
					'id'            => '3',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Date of Birth', 'wpforms-form-templates-pack' ),
					'format'        => 'date',
					'required'      => '1',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'dropdown',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				4  => array(
					'id'       => '4',
					'type'     => 'select',
					'label'    => esc_html__( 'Grade', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( '1st Grade', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( '2nd Grade', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( '3rd Grade', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( '4th Grade', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( '5th Grade', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( '6th Grade', 'wpforms-form-templates-pack' ),
						),
						7 => array(
							'label' => esc_html__( '7th Grade', 'wpforms-form-templates-pack' ),
						),
						8 => array(
							'label' => esc_html__( '8th Grade', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
					'size'     => 'medium',
				),
				5  => array(
					'id'    => '5',
					'type'  => 'text',
					'label' => esc_html__( 'Name of School', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				6  => array(
					'id'            => '6',
					'type'          => 'divider',
					'label'         => esc_html__( 'Parent/Guardian Information', 'wpforms-form-templates-pack' ),
					'label_disable' => '1',
				),
				7  => array(
					'id'       => '7',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				8  => array(
					'id'       => '8',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				9  => array(
					'id'       => '9',
					'type'     => 'phone',
					'label'    => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format'   => 'us',
					'required' => '1',
					'size'     => 'medium',
				),
				10 => array(
					'id'       => '10',
					'type'     => 'address',
					'label'    => esc_html__( 'Address', 'wpforms-form-templates-pack' ),
					'scheme'   => 'us',
					'required' => '1',
					'size'     => 'medium',
				),
				11 => array(
					'id'            => '11',
					'type'          => 'divider',
					'label'         => esc_html__( 'Section Divider', 'wpforms-form-templates-pack' ),
					'label_disable' => '1',
				),
				12 => array(
					'id'       => '12',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				13 => array(
					'id'       => '13',
					'type'     => 'phone',
					'label'    => esc_html__( 'Emergency Contact', 'wpforms-form-templates-pack' ),
					'format'   => 'us',
					'required' => '1',
					'size'     => 'medium',
				),
				14 => array(
					'id'    => '14',
					'type'  => 'textarea',
					'label' => esc_html__( 'Medical Concerns', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Summer_Camp_Registration;
