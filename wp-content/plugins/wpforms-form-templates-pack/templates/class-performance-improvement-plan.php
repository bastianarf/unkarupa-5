<?php

/**
 * Performance Improvement Plan Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Performance_Improvement_Plan extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Performance Improvement Plan Form', 'wpforms-form-templates-pack' );
		$this->slug = 'performance-improvement-plan';
		$this->data = array(
			'field_id' => 8,
			'fields'   => array(
				1 => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Employee Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				2 => array(
					'id'      => '2',
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Areas of improvement (Check all that apply)', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Efficacy', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Attendance', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Quality', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( 'Teamwork', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Productivity', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Professional Conduct', 'wpforms-form-templates-pack' ),
						),
						7 => array(
							'label' => esc_html__( 'Other', 'wpforms-form-templates-pack' ),
						),
					),
				),
				3 => array(
					'id'    => '3',
					'type'  => 'textarea',
					'label' => esc_html__( 'Please describe the reason(s) for submitting this plan.', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				4 => array(
					'id'    => '4',
					'type'  => 'textarea',
					'label' => esc_html__( 'Improvement Goals', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				5 => array(
					'id'    => '5',
					'type'  => 'textarea',
					'label' => esc_html__( 'Performance Goals', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				6 => array(
					'id'            => '6',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Date for improvements to be completed', 'wpforms-form-templates-pack' ),
					'format'        => 'date',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'datepicker',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				7 => array(
					'id'       => '7',
					'type'     => 'name',
					'label'    => esc_html__( 'Supervisor Name', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Performance_Improvement_Plan;
