<?php

/**
 * Employee Incident Report Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Employee_Incident_Report extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Employee Incident Report Form', 'wpforms-form-templates-pack' );
		$this->slug = 'employee-incident-report';
		$this->data = array(
			'field_id' => 9,
			'fields'   => array(
				1 => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Violating employee\'s name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				2 => array(
					'id'       => '2',
					'type'     => 'name',
					'label'    => esc_html__( 'Reporting employee\'s name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				3 => array(
					'id'       => '3',
					'type'     => 'email',
					'label'    => esc_html__( 'Reporting employee\'s email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				4 => array(
					'id'            => '4',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Date / Time of incident', 'wpforms-form-templates-pack' ),
					'format'        => 'date-time',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'datepicker',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				5 => array(
					'id'      => '5',
					'type'    => 'radio',
					'label'   => esc_html__( 'What violation has the employee committed?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Neglect of Duty', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Failure to Follow Instructions', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Inappropriate Behavior', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( 'Attendance Related', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Damage to Company Property', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Theft', 'wpforms-form-templates-pack' ),
						),
						8 => array(
							'label' => esc_html__( 'Harassment', 'wpforms-form-templates-pack' ),
						),
						7 => array(
							'label' => esc_html__( 'Poor Work Performance', 'wpforms-form-templates-pack' ),
						),
						9 => array(
							'label' => esc_html__( 'Violation of Company Policy', 'wpforms-form-templates-pack' ),
						),
					),
				),
				6 => array(
					'id'    => '6',
					'type'  => 'textarea',
					'label' => esc_html__( 'Please describe the incident.', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				8 => array(
					'id'       => '8',
					'type'     => 'name',
					'label'    => esc_html__( 'Report prepared by', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
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

new WPForms_Template_Employee_Incident_Report;
