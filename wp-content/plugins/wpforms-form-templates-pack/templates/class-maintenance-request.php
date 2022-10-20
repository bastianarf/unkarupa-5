<?php

/**
 * Maintenance Request Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Maintenance_Request_Form extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Maintenance Request Form', 'wpforms-form-templates-pack' );
		$this->slug = 'maintenance-request-form';
		$this->data = array(
			'field_id' => 11,
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
					'id'       => '2',
					'type'     => 'phone',
					'label'    => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format'   => 'us',
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
					'id'       => '4',
					'type'     => 'address',
					'label'    => esc_html__( 'Property Address', 'wpforms-form-templates-pack' ),
					'scheme'   => 'us',
					'required' => '1',
					'size'     => 'medium',
				),
				5  => array(
					'id'      => '5',
					'type'    => 'select',
					'label'   => esc_html__( 'Priority Level', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Normal', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Low', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'High', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				6  => array(
					'id'      => '6',
					'type'    => 'select',
					'label'   => esc_html__( 'Type of problem', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'AC', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Heating', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Plumbing', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Electrical', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Other / N/A', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				7  => array(
					'id'      => '7',
					'type'    => 'select',
					'label'   => esc_html__( 'Location of problem', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Living room', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Kitchen', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Bathroom', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Bedroom', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Patio', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( 'Other / N/A', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				8  => array(
					'id'    => '8',
					'type'  => 'textarea',
					'label' => esc_html__( 'Please describe the issue', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				9  => array(
					'id'       => '9',
					'type'     => 'radio',
					'label'    => esc_html__( 'Does our maintenance team have permission to enter your residence without you present?', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( 'Yes', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'No', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
				),
				10 => array(
					'id'       => '10',
					'type'     => 'radio',
					'label'    => esc_html__( 'Do you have any pets?', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( 'Yes', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'No', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
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

new WPForms_Template_Maintenance_Request_Form;
