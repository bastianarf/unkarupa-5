<?php

/**
 * Change Request Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Change_Request extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Change Request Form', 'wpforms-form-templates-pack' );
		$this->slug = 'change-request';
		$this->data = array(
			'field_id' => 11,
			'fields'   => array(
				1  => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Requester Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				2  => array(
					'id'       => '2',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
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
					'id'    => '4',
					'type'  => 'text',
					'label' => esc_html__( 'Company Name', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				5  => array(
					'id'    => '5',
					'type'  => 'text',
					'label' => esc_html__( 'Client ID Number', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				6  => array(
					'id'    => '6',
					'type'  => 'text',
					'label' => esc_html__( 'Project Name', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				7  => array(
					'id'      => '7',
					'type'    => 'radio',
					'label'   => esc_html__( 'What type of change are you requesting?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Update to Account Information', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Project or Product Enhancement', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Update to Project Requirements/Deliverables', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Update to Project Schedule', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Bug/Defect Fix', 'wpforms-form-templates-pack' ),
						),
					),
				),
				8  => array(
					'id'    => '8',
					'type'  => 'text',
					'label' => esc_html__( 'Reason for change', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				9  => array(
					'id'      => '9',
					'type'    => 'radio',
					'label'   => esc_html__( 'Priority', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Low', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Medium', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'High', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Critical', 'wpforms-form-templates-pack' ),
						),
					),
				),
				10 => array(
					'id'    => '10',
					'type'  => 'textarea',
					'label' => esc_html__( 'Please describe in detail the change being requested.', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Change_Request;
