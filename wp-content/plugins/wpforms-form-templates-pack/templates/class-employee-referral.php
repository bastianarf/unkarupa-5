<?php

/**
 * Employee Referral Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Employee_Referral extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Employee Referral Form', 'wpforms-form-templates-pack' );
		$this->slug = 'employee-referral';
		$this->data = array(
			'field_id' => 11,
			'fields'   => array(
				1  => array(
					'id'            => '1',
					'type'          => 'divider',
					'label'         => esc_html__( 'Employee Information', 'wpforms-form-templates-pack' ),
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
					'label'         => esc_html__( 'Referral Information', 'wpforms-form-templates-pack' ),
					'label_disable' => '1',
				),
				6  => array(
					'id'       => '6',
					'type'     => 'name',
					'label'    => esc_html__( 'Candidate\'s Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				7  => array(
					'id'       => '7',
					'type'     => 'email',
					'label'    => esc_html__( 'Candidate\'s email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				8  => array(
					'id'     => '8',
					'type'   => 'phone',
					'label'  => esc_html__( 'Candidate\'s phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				9  => array(
					'id'    => '9',
					'type'  => 'text',
					'label' => esc_html__( 'Position applied for', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				10 => array(
					'id'    => '10',
					'type'  => 'textarea',
					'label' => esc_html__( 'Why is this candidate qualified for the position?', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Employee_Referral;
