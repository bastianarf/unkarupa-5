<?php

/**
 * IT Service Request Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_IT_Service_Request extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'IT Service Request Form', 'wpforms-form-templates-pack' );
		$this->slug = 'it-service-request';
		$this->data = array(
			'field_id' => 8,
			'fields'   => array(
				1 => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				2 => array(
					'id'       => '2',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				3 => array(
					'id'     => '3',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				4 => array(
					'id'    => '4',
					'type'  => 'text',
					'label' => esc_html__( 'Department', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				5 => array(
					'id'      => '5',
					'type'    => 'radio',
					'label'   => esc_html__( 'What are you having issues with?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Computer', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Projector', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Mobile Device / Tablet', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Internet Connection / Network', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Other', 'wpforms-form-templates-pack' ),
						),
					),
				),
				6 => array(
					'id'    => '6',
					'type'  => 'text',
					'label' => esc_html__( 'If other', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				7 => array(
					'id'    => '7',
					'type'  => 'textarea',
					'label' => esc_html__( 'Additional Details or Information', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_IT_Service_Request;
