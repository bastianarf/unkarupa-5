<?php

/**
 * Employee Information Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Employee_Information extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Employee Information Form', 'wpforms-form-templates-pack' );
		$this->slug = 'employee-information';
		$this->data = array(
			'field_id' => 18,
			'fields'   => array(
				1  => array(
					'id'            => '1',
					'type'          => 'divider',
					'label'         => esc_html__( 'Personal Information', 'wpforms-form-templates-pack' ),
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
					'id'     => '5',
					'type'   => 'address',
					'label'  => esc_html__( 'Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
				),
				6  => array(
					'id'            => '6',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Date of Birth', 'wpforms-form-templates-pack' ),
					'format'        => 'date',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'dropdown',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				7  => array(
					'id'            => '7',
					'type'          => 'divider',
					'label'         => esc_html__( 'Office Information', 'wpforms-form-templates-pack' ),
					'label_disable' => '1',
				),
				8  => array(
					'id'    => '8',
					'type'  => 'text',
					'label' => esc_html__( 'Job Title', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				9  => array(
					'id'    => '9',
					'type'  => 'text',
					'label' => esc_html__( 'Supervisor Name', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				10 => array(
					'id'    => '10',
					'type'  => 'text',
					'label' => esc_html__( 'Department', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				11 => array(
					'id'     => '11',
					'type'   => 'phone',
					'label'  => esc_html__( 'Office Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				12 => array(
					'id'            => '12',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Start Date', 'wpforms-form-templates-pack' ),
					'format'        => 'date',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'dropdown',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				13 => array(
					'id'     => '13',
					'type'   => 'payment-single',
					'label'  => esc_html__( 'Salary', 'wpforms-form-templates-pack' ),
					'format' => 'user',
					'size'   => 'medium',
				),
				14 => array(
					'id'            => '14',
					'type'          => 'divider',
					'label'         => esc_html__( 'Emergency Contact', 'wpforms-form-templates-pack' ),
					'label_disable' => '1',
				),
				15 => array(
					'id'       => '15',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				16 => array(
					'id'     => '16',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				17 => array(
					'id'    => '17',
					'type'  => 'text',
					'label' => esc_html__( 'Relationship', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Employee_Information;
