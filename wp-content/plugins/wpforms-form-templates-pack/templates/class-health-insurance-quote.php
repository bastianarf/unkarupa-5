<?php

/**
 * Health Insurance Quote Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Health_Insurance_Quote extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Health Insurance Quote Form', 'wpforms-form-templates-pack' );
		$this->slug = 'health-insurance-quote';
		$this->data = array(
			'field_id' => 10,
			'fields'   => array(
				1 => array(
					'id'    => '1',
					'type'  => 'text',
					'label' => esc_html__( 'Company', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				2 => array(
					'id'       => '2',
					'type'     => 'name',
					'label'    => esc_html__( 'Primary Contact Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				3 => array(
					'id'       => '3',
					'type'     => 'email',
					'label'    => esc_html__( 'Primary Contact Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				4 => array(
					'id'     => '4',
					'type'   => 'phone',
					'label'  => esc_html__( 'Primary Contact Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				5 => array(
					'id'    => '5',
					'type'  => 'text',
					'label' => esc_html__( 'Primary Contact Title', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				6 => array(
					'id'     => '6',
					'type'   => 'address',
					'label'  => esc_html__( 'Primary Contact Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
				),
				7 => array(
					'id'    => '7',
					'type'  => 'textarea',
					'label' => esc_html__( 'Please tell us about your industry', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				8 => array(
					'id'    => '8',
					'type'  => 'textarea',
					'label' => esc_html__( 'How did you hear about us?', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				9 => array(
					'id'    => '9',
					'type'  => 'textarea',
					'label' => esc_html__( 'Questions or Comments', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Health_Insurance_Quote;
