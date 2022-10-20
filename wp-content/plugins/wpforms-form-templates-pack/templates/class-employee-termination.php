<?php

/**
 * Employee Termination Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Employee_Termination extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Employee Termination Form', 'wpforms-form-templates-pack' );
		$this->slug = 'employee-termination';
		$this->data = array(
			'field_id' => 11,
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
					'label' => esc_html__( 'Employee Position', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				3  => array(
					'id'    => '3',
					'type'  => 'text',
					'label' => esc_html__( 'Employee Department', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				4  => array(
					'id'            => '4',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Start Date', 'wpforms-form-templates-pack' ),
					'format'        => 'date',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'dropdown',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				5  => array(
					'id'            => '5',
					'type'          => 'date-time',
					'label'         => esc_html__( 'End Date', 'wpforms-form-templates-pack' ),
					'format'        => 'date',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'dropdown',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				6  => array(
					'id'      => '6',
					'type'    => 'radio',
					'label'   => esc_html__( 'Termination status', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Voluntary', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Involuntary', 'wpforms-form-templates-pack' ),
						),
					),
				),
				10 => array(
					'id'    => '10',
					'type'  => 'text',
					'label' => esc_html__( 'Termination details', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				9  => array(
					'id'      => '9',
					'type'    => 'radio',
					'label'   => esc_html__( 'Is employee eligible for rehire?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Yes', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'No', 'wpforms-form-templates-pack' ),
						),
					),
				),
				8  => array(
					'id'       => '8',
					'type'     => 'name',
					'label'    => esc_html__( 'Supervisor', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Employee_Termination;
